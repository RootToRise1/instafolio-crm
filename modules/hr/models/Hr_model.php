<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hr_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Attendance Methods
    public function clock_in($staff_id, $note = '')
    {
        $today = date('Y-m-d');
        
        // Check if already checked in today (check both date column and clock_in datetime)
        $exists = $this->db->where('staff_id', $staff_id)
                          ->where('(date = "' . $today . '" OR clock_in LIKE "' . $today . '%")')
                          ->where('clock_out IS NULL')
                          ->get(db_prefix() . 'hr_attendance')->row();
        
        if ($exists) {
            return ['success' => false, 'message' => 'Already checked in today'];
        }

        $shift = $this->get_staff_shift($staff_id);
        $late_minutes = 0;
        
        if ($shift) {
            $current_time = date('H:i:s');
            $shift_start = $shift->start_time;
            if ($current_time > $shift_start) {
                $late_minutes = $this->calculate_minutes_late($shift_start, $current_time);
            }
        }

        $data = [
            'staff_id'    => $staff_id,
            'date'        => $today,
            'clock_in'    => date('Y-m-d H:i:s'),
            'clock_in_note' => $note,
            'late_minutes' => $late_minutes,
            'status'      => $late_minutes > 0 ? 'late' : 'present',
        ];

        $this->db->insert(db_prefix() . 'hr_attendance', $data);
        return ['success' => true, 'message' => 'Checked in successfully'];
    }

    public function clock_out($staff_id, $note = '')
    {
        $today = date('Y-m-d');
        
        // Find the open attendance record for today
        $attendance = $this->db->where('staff_id', $staff_id)
                               ->where('clock_out IS NULL')
                               ->where('(date = "' . $today . '" OR clock_in LIKE "' . $today . '%")')
                               ->order_by('id', 'desc')
                               ->get(db_prefix() . 'hr_attendance')->row();

        if (!$attendance) {
            return ['success' => false, 'message' => 'Not checked in'];
        }

        if ($attendance->clock_out) {
            return ['success' => false, 'message' => 'Already checked out'];
        }

        $break_minutes = $attendance->break_minutes ?: 0;
        $clock_out = date('Y-m-d H:i:s');
        $clock_in = $attendance->clock_in;
        
        $total_hours = $this->calculate_hours($clock_in, $clock_out, $break_minutes);
        $shift = $this->get_staff_shift($staff_id);
        $overtime = 0;
        
        if ($shift && $total_hours > $shift->working_hours) {
            $overtime = $total_hours - $shift->working_hours;
        }

        $this->db->where('id', $attendance->id)
                 ->update(db_prefix() . 'hr_attendance', [
                     'clock_out'      => $clock_out,
                     'clock_out_note' => $note,
                     'total_hours'    => $total_hours,
                     'overtime_hours' => $overtime,
                 ]);

        return ['success' => true, 'message' => 'Checked out successfully', 'total_hours' => $total_hours];
    }

    public function break_in($staff_id)
    {
        $today = date('Y-m-d');
        
        // Find the open attendance record for today
        $attendance = $this->db->where('staff_id', $staff_id)
                               ->where('clock_out IS NULL')
                               ->where('(date = "' . $today . '" OR clock_in LIKE "' . $today . '%")')
                               ->order_by('id', 'desc')
                               ->get(db_prefix() . 'hr_attendance')->row();

        if (!$attendance) {
            return ['success' => false, 'message' => 'Not checked in'];
        }

        // Store break start time in a breaks table
        $this->db->insert(db_prefix() . 'hr_attendance_breaks', [
            'attendance_id' => $attendance->id,
            'break_start'  => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Break started'];
    }

    public function break_out($staff_id)
    {
        $today = date('Y-m-d');
        
        // Find the open attendance record for today
        $attendance = $this->db->where('staff_id', $staff_id)
                               ->where('clock_out IS NULL')
                               ->where('(date = "' . $today . '" OR clock_in LIKE "' . $today . '%")')
                               ->order_by('id', 'desc')
                               ->get(db_prefix() . 'hr_attendance')->row();

        if (!$attendance) {
            return ['success' => false, 'message' => 'Not checked in'];
        }

        // Find the most recent open break
        $break = $this->db->where('attendance_id', $attendance->id)
                          ->where('break_end IS NULL', null)
                          ->order_by('break_start', 'desc')
                          ->get(db_prefix() . 'hr_attendance_breaks')->row();

        if (!$break) {
            return ['success' => false, 'message' => 'Not on break'];
        }

        $break_end = date('Y-m-d H:i:s');
        $break_minutes = $this->calculate_minutes($break->break_start, $break_end);

        $this->db->where('id', $break->id)
                 ->update(db_prefix() . 'hr_attendance_breaks', [
                     'break_end'    => $break_end,
                     'break_minutes' => $break_minutes,
                 ]);

        // Update total break minutes
        $total_break = ($attendance->break_minutes ?: 0) + $break_minutes;
        $this->db->where('id', $attendance->id)
                 ->update(db_prefix() . 'hr_attendance', [
                     'break_minutes' => $total_break,
                 ]);

        return ['success' => true, 'message' => 'Break ended', 'break_minutes' => $break_minutes];
    }

    public function get_today_attendance($staff_id)
    {
        $today = date('Y-m-d');
        return $this->db->where('staff_id', $staff_id)
                       ->where('(date = "' . $today . '" OR clock_in LIKE "' . $today . '%")')
                       ->order_by('id', 'desc')
                       ->get(db_prefix() . 'hr_attendance')->row();
    }

    public function get_staff_attendance($staff_id, $start_date = '', $end_date = '')
    {
        $this->db->where('staff_id', $staff_id);
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $this->db->order_by('date', 'desc');
        return $this->db->get(db_prefix() . 'hr_attendance')->result_array();
    }

    public function get_department_attendance($department_id, $date = '')
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $this->db->select('att.*, staff.firstname, staff.lastname, staff.email');
        $this->db->from(db_prefix() . 'hr_attendance att');
        $this->db->join(db_prefix() . 'staff staff', 'staff.staffid = att.staff_id');
        $this->db->where('staff.department_id', $department_id);
        $this->db->where('att.date', $date);
        return $this->db->get()->result_array();
    }

    private function get_staff_shift($staff_id)
    {
        $this->db->select('s.*');
        $this->db->from(db_prefix() . 'staff st');
        $this->db->join(db_prefix() . 'hr_shifts s', 's.id = st.hr_shift_id', 'left');
        $this->db->where('st.staffid', $staff_id);
        return $this->db->get()->row();
    }

    private function calculate_hours($clock_in, $clock_out, $break_minutes)
    {
        // Handle both datetime and time formats
        $in = (strpos($clock_in, '-') !== false) ? new DateTime($clock_in) : new DateTime(date('Y-m-d') . ' ' . $clock_in);
        $out = (strpos($clock_out, '-') !== false) ? new DateTime($clock_out) : new DateTime(date('Y-m-d') . ' ' . $clock_out);
        
        if ($out < $in) {
            $out->modify('+1 day');
        }
        
        $diff = $in->diff($out);
        $hours = ($diff->h) + ($diff->i / 60) + ($diff->s / 3600);
        return max(0, $hours - ($break_minutes / 60));
    }

    private function calculate_minutes($start, $end)
    {
        // Handle both datetime and time formats
        $in = (strpos($start, '-') !== false) ? new DateTime($start) : new DateTime(date('Y-m-d') . ' ' . $start);
        $out = (strpos($end, '-') !== false) ? new DateTime($end) : new DateTime(date('Y-m-d') . ' ' . $end);
        $diff = $in->diff($out);
        return ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    }

    private function calculate_minutes_late($expected, $actual)
    {
        $expected_time = new DateTime($expected);
        $actual_time = new DateTime($actual);
        $diff = $expected_time->diff($actual_time);
        if ($diff->invert) {
            return 0;
        }
        return ($diff->h * 60) + $diff->i;
    }

    // Leave Methods
    public function submit_leave_request($data)
    {
        $data['status'] = 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert(db_prefix() . 'hr_leave_requests', $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id && function_exists('hr_notify_manager')) {
            hr_notify_manager($data['staff_id'], 'leave_request', $data);
        }
        
        return $insert_id;
    }

    public function approve_leave($request_id, $approved_by)
    {
        $this->db->where('id', $request_id);
        $this->db->update(db_prefix() . 'hr_leave_requests', [
            'status'      => 'approved',
            'approved_by' => $approved_by,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
        
        $request = $this->get_leave_request($request_id);
        $this->update_leave_balance($request['staff_id'], $request['leave_type_id'], $request['days']);
        
        return true;
    }

    public function reject_leave($request_id, $rejected_by, $reason = '')
    {
        $this->db->where('id', $request_id);
        $this->db->update(db_prefix() . 'hr_leave_requests', [
            'status'           => 'rejected',
            'rejected_by'      => $rejected_by,
            'rejected_at'      => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason,
        ]);
        return true;
    }

    public function get_leave_request($id)
    {
        return $this->db->where('id', $id)
                        ->get(db_prefix() . 'hr_leave_requests')->row_array();
    }

    public function get_leave_requests($staff_id = '', $status = '', $year = '')
    {
        if ($staff_id) {
            $this->db->where('staff_id', $staff_id);
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        if ($year) {
            $this->db->where('YEAR(start_date)', $year);
        }
        $this->db->order_by('created_at', 'desc');
        return $this->db->get(db_prefix() . 'hr_leave_requests')->result_array();
    }

    public function get_pending_leave_requests($manager_id = '')
    {
        $this->db->select('lr.*, s.firstname, s.lastname, lt.name as leave_type_name');
        $this->db->from(db_prefix() . 'hr_leave_requests lr');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = lr.staff_id');
        $this->db->join(db_prefix() . 'hr_leave_types lt', 'lt.id = lr.leave_type_id');
        $this->db->where('lr.status', 'pending');
        
        if ($manager_id) {
            $this->db->where('s.manager_id', $manager_id);
        }
        
        return $this->db->get()->result_array();
    }

    public function get_leave_balance($staff_id, $year = '')
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $leave_types = $this->get_leave_types();
        $balances = [];
        
        foreach ($leave_types as $lt) {
            $used = $this->get_used_leave_days($staff_id, $lt['id'], $year);
            $allocated = $lt['days_allowed'] ?? 0;
            $pending = $this->get_pending_leave_days($staff_id, $lt['id'], $year);
            $available = max(0, $allocated - $used);
            
            $balances[] = [
                'id' => $lt['id'],
                'name' => $lt['name'],
                'color' => $lt['color'],
                'allocated' => $allocated,
                'used' => $used,
                'pending' => $pending,
                'available' => $available,
            ];
        }
        
        return $balances;
    }

    private function get_used_leave_days($staff_id, $leave_type_id, $year)
    {
        $this->db->select('SUM(DATEDIFF(end_date, start_date) + 1) as total_days');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $leave_type_id);
        $this->db->where('YEAR(start_date)', $year);
        $this->db->where('status', 'approved');
        $result = $this->db->get(db_prefix() . 'hr_leave_requests')->row();
        return $result->total_days ?? 0;
    }
    
    private function get_pending_leave_days($staff_id, $leave_type_id, $year)
    {
        $this->db->select('SUM(DATEDIFF(end_date, start_date) + 1) as total_days');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $leave_type_id);
        $this->db->where('YEAR(start_date)', $year);
        $this->db->where('status', 'pending');
        $result = $this->db->get(db_prefix() . 'hr_leave_requests')->row();
        return $result->total_days ?? 0;
    }
    
    public function get_used_leave($staff_id, $leave_type_id, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $this->db->select_sum('days');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $leave_type_id);
        $this->db->where('YEAR(start_date)', $year);
        $this->db->where('status', 'approved');
        $result = $this->db->get(db_prefix() . 'hr_leave_requests')->row();
        return $result->days ?? 0;
    }
    
    public function get_pending_leave($staff_id, $leave_type_id, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $this->db->select_sum('days');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $leave_type_id);
        $this->db->where('YEAR(start_date)', $year);
        $this->db->where('status', 'pending');
        $result = $this->db->get(db_prefix() . 'hr_leave_requests')->row();
        return $result->days ?? 0;
    }

    private function update_leave_balance($staff_id, $leave_type_id, $days)
    {
        $year = date('Y');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $leave_type_id);
        $this->db->where('year', $year);
        $allocation = $this->db->get(db_prefix() . 'hr_leave_allocations')->row();
        
        if ($allocation) {
            $this->db->where('id', $allocation->id);
            $this->db->update(db_prefix() . 'hr_leave_allocations', [
                'used_days' => $allocation->used_days + $days,
            ]);
        }
    }

    // Performance Methods
    public function get_performance_reviews($staff_id = '', $reviewer_id = '')
    {
        if ($staff_id) {
            $this->db->where('staff_id', $staff_id);
        }
        if ($reviewer_id) {
            $this->db->where('reviewer_id', $reviewer_id);
        }
        $this->db->order_by('review_date', 'desc');
        return $this->db->get(db_prefix() . 'hr_performance_reviews')->result_array();
    }

    public function add_performance_review($data)
    {
        $this->db->insert(db_prefix() . 'hr_performance_reviews', $data);
        return $this->db->insert_id();
    }

    public function acknowledge_review($review_id)
    {
        $this->db->where('id', $review_id);
        $this->db->update(db_prefix() . 'hr_performance_reviews', [
            'acknowledged'   => 1,
            'acknowledged_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    // Leave Types
    public function get_leave_types()
    {
        return $this->db->get(db_prefix() . 'hr_leave_types')->result_array();
    }

    public function add_leave_type($data)
    {
        $this->db->insert(db_prefix() . 'hr_leave_types', $data);
        return $this->db->insert_id();
    }

    public function update_leave_type($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_leave_types', $data);
        return true;
    }

    public function delete_leave_type($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hr_leave_types');
        return true;
    }

    // Departments
    public function get_departments()
    {
        return $this->db->get(db_prefix() . 'hr_departments')->result_array();
    }

    public function get_department($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'hr_departments')->row_array();
    }

    public function add_department($data)
    {
        $this->db->insert(db_prefix() . 'hr_departments', $data);
        return $this->db->insert_id();
    }

    public function update_department($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_departments', $data);
        return true;
    }

    public function delete_department($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hr_departments');
        return true;
    }

    // Designations
    public function get_designations($department_id = '')
    {
        if ($department_id) {
            $this->db->where('department_id', $department_id);
        }
        return $this->db->get(db_prefix() . 'hr_designations')->result_array();
    }

    public function get_designation($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'hr_designations')->row_array();
    }

    public function get_roles()
    {
        return $this->db->where('active', 1)->get(db_prefix() . 'hr_roles')->result_array();
    }

    public function add_designation($data)
    {
        $this->db->insert(db_prefix() . 'hr_designations', $data);
        return $this->db->insert_id();
    }

    public function update_designation($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_designations', $data);
        return true;
    }

    public function delete_designation($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hr_designations');
        return true;
    }

    // Shifts
    public function get_shifts()
    {
        return $this->db->get(db_prefix() . 'hr_shifts')->result_array();
    }

    public function get_shift($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'hr_shifts')->row_array();
    }

    public function add_shift($data)
    {
        $this->db->insert(db_prefix() . 'hr_shifts', $data);
        return $this->db->insert_id();
    }

    public function update_shift($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_shifts', $data);
        return true;
    }

    public function delete_shift($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hr_shifts');
        return true;
    }

    // Holidays
    public function get_holidays($year = '')
    {
        if ($year) {
            $this->db->where('year', $year);
        }
        $this->db->order_by('date', 'asc');
        return $this->db->get(db_prefix() . 'hr_holidays')->result_array();
    }

    public function get_holiday($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'hr_holidays')->row_array();
    }

    public function add_holiday($data)
    {
        if (!isset($data['year'])) {
            $data['year'] = date('Y', strtotime($data['date']));
        }
        $this->db->insert(db_prefix() . 'hr_holidays', $data);
        return $this->db->insert_id();
    }

    public function update_holiday($id, $data)
    {
        if (isset($data['date'])) {
            $data['year'] = date('Y', strtotime($data['date']));
        }
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_holidays', $data);
        return true;
    }

    public function delete_holiday($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hr_holidays');
        return true;
    }

    // Notifications
    public function add_notification($staff_id, $message, $link = '', $type = '')
    {
        $this->db->insert(db_prefix() . 'hr_notifications', [
            'staff_id'  => $staff_id,
            'message'   => $message,
            'link'      => $link,
            'type'      => $type,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->db->insert_id();
    }

    public function get_notifications($staff_id, $limit = 10)
    {
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('created_at', 'desc');
        $this->db->limit($limit);
        return $this->db->get(db_prefix() . 'hr_notifications')->result_array();
    }

    public function mark_notification_read($id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    public function get_unread_notification_count($staff_id)
    {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('is_read', 0);
        return $this->db->count_all_results(db_prefix() . 'hr_notifications');
    }

    // Reports
    public function get_attendance_report($start_date, $end_date, $department_id = '', $staff_id = '')
    {
        $this->db->select('att.*, s.firstname, s.lastname, s.email, d.name as department_name');
        $this->db->from(db_prefix() . 'hr_attendance att');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = att.staff_id');
        $this->db->join(db_prefix() . 'hr_departments d', 'd.id = s.department_id', 'left');
        $this->db->where('att.date >=', $start_date);
        $this->db->where('att.date <=', $end_date);
        
        if ($department_id) {
            $this->db->where('s.department_id', $department_id);
        }
        if ($staff_id) {
            $this->db->where('att.staff_id', $staff_id);
        }
        
        $this->db->order_by('att.date', 'desc');
        return $this->db->get()->result_array();
    }

    public function get_leave_utilization_report($year = '')
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $this->db->select('lt.name, lt.color, la.allocated_days, la.carry_forward_days, la.used_days, 
                          (la.allocated_days + la.carry_forward_days - la.used_days) as remaining,
                          (la.used_days / (la.allocated_days + la.carry_forward_days) * 100) as utilization_percent');
        $this->db->from(db_prefix() . 'hr_leave_types lt');
        $this->db->join(db_prefix() . 'hr_leave_allocations la', 'la.leave_type_id = lt.id AND la.year = ' . $year, 'left');
        return $this->db->get()->result_array();
    }

    // ==================== PAYROLL METHODS ====================

    public function get_staff_salary($staff_id)
    {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('effective_from', 'desc');
        return $this->db->get(db_prefix() . 'hr_payroll_salary')->row_array();
    }

    public function get_all_salaries()
    {
        $this->db->select('ps.*, s.firstname, s.lastname, s.email');
        $this->db->from(db_prefix() . 'hr_payroll_salary ps');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = ps.staff_id');
        $this->db->where('ps.is_active', 1);
        return $this->db->get()->result_array();
    }

    public function add_salary($data)
    {
        $this->db->insert(db_prefix() . 'hr_payroll_salary', $data);
        return $this->db->insert_id();
    }

    public function update_salary($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_payroll_salary', $data);
        return true;
    }

    public function delete_salary($id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_payroll_salary', ['is_active' => 0]);
        return true;
    }

    public function get_payslips($staff_id = '', $status = '')
    {
        $this->db->select('p.*, s.firstname, s.lastname');
        $this->db->from(db_prefix() . 'hr_payslips p');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.staff_id');
        
        if ($staff_id) {
            $this->db->where('p.staff_id', $staff_id);
        }
        if ($status) {
            $this->db->where('p.status', $status);
        }
        
        $this->db->order_by('p.pay_period_end', 'desc');
        return $this->db->get()->result_array();
    }

    public function get_payslip($id)
    {
        $this->db->select('p.*, s.firstname, s.lastname, s.email');
        $this->db->from(db_prefix() . 'hr_payslips p');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.staff_id');
        $this->db->where('p.id', $id);
        return $this->db->get()->row_array();
    }

    public function create_payslip($staff_id, $pay_period_start, $pay_period_end, $data = [])
    {
        $salary = $this->get_staff_salary($staff_id);
        if (!$salary) {
            return false;
        }

        $staff = $this->db->where('staffid', $staff_id)->get(db_prefix() . 'staff')->row();
        $working_days = $this->get_working_days_in_period($pay_period_start, $pay_period_end);
        $days_worked = $this->get_staff_attendance_count($staff_id, $pay_period_start, $pay_period_end);
        
        $pro_rata = 0;
        if ($days_worked < $working_days && $days_worked > 0) {
            $pro_rata = ($salary['base_salary'] / $working_days) * $days_worked;
        } elseif ($days_worked >= $working_days) {
            $pro_rata = $salary['base_salary'];
        }

        $allowances = $this->calculate_staff_allowances($staff_id);
        $deductions = $salary['social_security'] + $salary['health_insurance'] + $salary['other_deductions'];
        $tax = $this->calculate_tax($pro_rata + $allowances);
        $net_salary = $pro_rata + $allowances - $deductions - $tax;

        $payslip_data = [
            'staff_id' => $staff_id,
            'salary_id' => $salary['id'],
            'pay_period_start' => $pay_period_start,
            'pay_period_end' => $pay_period_end,
            'basic_salary' => $pro_rata > 0 ? $pro_rata : $salary['base_salary'],
            'allowances' => $allowances,
            'deductions' => $deductions,
            'tax' => $tax,
            'net_salary' => $net_salary,
            'days_worked' => $days_worked,
            'days_paid' => $working_days,
            'pro_rata_amount' => $pro_rata,
            'status' => 'draft',
        ];

        $this->db->insert(db_prefix() . 'hr_payslips', $payslip_data);
        return $this->db->insert_id();
    }

    public function calculate_pro_rata($staff_id, $month = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }
        
        $start_date = $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $staff = $this->db->where('staffid', $staff_id)->get(db_prefix() . 'staff')->row();
        $salary = $this->get_staff_salary($staff_id);
        
        if (!$staff || !$salary) {
            return 0;
        }

        $join_date = $staff->date_of_joining ? strtotime($staff->date_of_joining) : 0;
        $month_start = strtotime($start_date);
        $month_end = strtotime($end_date);
        
        $total_days = date('t', strtotime($start_date));
        $working_days = $this->get_working_days_in_period($start_date, $end_date);
        
        if ($join_date > $month_end) {
            return 0;
        }
        
        $days_worked = $working_days;
        if ($join_date > $month_start) {
            $join_day = date('j', $join_date);
            $days_in_month = date('t', $month_start);
            $days_worked = $days_in_month - $join_day + 1;
            $days_worked = min($days_worked, $working_days);
        }

        $daily_rate = $salary['base_salary'] / $working_days;
        return round($daily_rate * $days_worked, 2);
    }

    private function get_working_days_in_period($start_date, $end_date)
    {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $working_days = 0;
        
        while ($start <= $end) {
            $day_of_week = $start->format('N');
            if ($day_of_week < 6) {
                $working_days++;
            }
            $start->modify('+1 day');
        }
        
        return $working_days;
    }

    private function get_staff_attendance_count($staff_id, $start_date, $end_date)
    {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->where('clock_in IS NOT NULL');
        return $this->db->count_all_results(db_prefix() . 'hr_attendance');
    }

    private function calculate_staff_allowances($staff_id)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('is_active', 1);
        $result = $this->db->get(db_prefix() . 'hr_staff_allowances')->row();
        return $result->total ?? 0;
    }

    private function calculate_tax($taxable_income)
    {
        $tax = 0;
        if ($taxable_income > 5000) {
            $tax += ($taxable_income - 5000) * 0.10;
        }
        if ($taxable_income > 10000) {
            $tax += (10000 - 5000) * 0.10;
            $tax += ($taxable_income - 10000) * 0.20;
        }
        if ($taxable_income > 20000) {
            $tax += 1500;
            $tax += 10000 * 0.10;
            $tax += ($taxable_income - 20000) * 0.30;
        }
        return $tax;
    }

    public function get_allowances()
    {
        return $this->db->where('is_active', 1)->get(db_prefix() . 'hr_payroll_allowances')->result_array();
    }

    public function add_allowance($data)
    {
        $this->db->insert(db_prefix() . 'hr_payroll_allowances', $data);
        return $this->db->insert_id();
    }

    public function assign_allowance_to_staff($staff_id, $allowance_id, $amount)
    {
        $data = [
            'staff_id' => $staff_id,
            'allowance_id' => $allowance_id,
            'amount' => $amount,
            'is_active' => 1,
        ];
        $this->db->insert(db_prefix() . 'hr_staff_allowances', $data);
        return $this->db->insert_id();
    }

    public function update_payslip_status($id, $status)
    {
        $this->db->where('id', $id);
        $data = ['status' => $status];
        if ($status == 'paid') {
            $data['payment_date'] = date('Y-m-d');
        }
        $this->db->update(db_prefix() . 'hr_payslips', $data);
        return true;
    }

    // ==================== PRO-RATA CALCULATIONS ====================
    
    public function calculate_monthly_salary($staff_id, $month = null, $year = null)
    {
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        
        $staff = $this->db->where('staffid', $staff_id)->get(db_prefix() . 'staff')->row();
        if (!$staff) return ['basic_salary' => 0, 'prorata_salary' => 0, 'prorata_factor' => 0, 'working_days' => 0, 'salary_days' => 0, 'actual_days' => 0, 'joining_date' => null, 'leaving_date' => null];
        
        $joining_date = $staff->date_of_joining ? date('Y-m-d', strtotime($staff->date_of_joining)) : null;
        $leaving_date = isset($staff->leaving_date) && $staff->leaving_date ? date('Y-m-d', strtotime($staff->leaving_date)) : null;
        
        $month_start = date('Y-m-01', strtotime("$year-$month-01"));
        $month_end = date('Y-m-t', strtotime("$year-$month-01"));
        
        $salary = $this->get_staff_salary($staff_id);
        if (!$salary) return ['basic_salary' => 0, 'prorata_salary' => 0, 'prorata_factor' => 0, 'working_days' => 0, 'salary_days' => 0, 'actual_days' => 0, 'joining_date' => $joining_date, 'leaving_date' => $leaving_date];
        
        $total_monthly_salary = $salary['base_salary'] ?? 0;
        
        $working_days = $this->get_working_days_in_month($month, $year);
        $actual_days = $working_days;
        $salary_days = $working_days;
        $prorata_factor = 1.0;
        
        if ($joining_date && $joining_date > $month_start && $joining_date <= $month_end) {
            $actual_days = $this->get_working_days_between($month_start, $joining_date, true);
            $salary_days = $actual_days;
            $prorata_factor = $actual_days / $working_days;
        }
        
        if ($leaving_date && $leaving_date >= $month_start && $leaving_date < $month_end) {
            $actual_days = $this->get_working_days_between($month_start, $leaving_date, true);
            $salary_days = $actual_days;
            $prorata_factor = $actual_days / $working_days;
        }
        
        if ($leaving_date && $joining_date && $leaving_date < $month_end) {
            $prorata_factor = 0;
        }
        
        return [
            'basic_salary' => $total_monthly_salary,
            'prorata_salary' => round($total_monthly_salary * $prorata_factor, 2),
            'prorata_factor' => round($prorata_factor, 4),
            'working_days' => $working_days,
            'salary_days' => $salary_days,
            'actual_days' => $actual_days,
            'joining_date' => $joining_date,
            'leaving_date' => $leaving_date,
        ];
    }
    
    public function get_working_days_in_month($month, $year)
    {
        $days_in_month = date('t', strtotime("$year-$month-01"));
        $working_days = 0;
        
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = date('Y-m-d', strtotime("$year-$month-$day"));
            $day_of_week = date('N', strtotime($date));
            if ($day_of_week < 6) {
                if (!$this->is_holiday($date)) {
                    $working_days++;
                }
            }
        }
        
        return $working_days;
    }
    
    public function get_working_days_between($start_date, $end_date, $inclusive = false)
    {
        if ($inclusive) {
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }
        
        $working_days = 0;
        $current = strtotime($start_date);
        $end = strtotime($end_date);
        
        while ($current < $end) {
            $day_of_week = date('N', $current);
            if ($day_of_week < 6) {
                $date = date('Y-m-d', $current);
                if (!$this->is_holiday($date)) {
                    $working_days++;
                }
            }
            $current = strtotime('+1 day', $current);
        }
        
        return $working_days;
    }
    
    public function is_holiday($date)
    {
        return $this->db->where('date', $date)->get(db_prefix() . 'hr_holidays')->num_rows() > 0;
    }
    
    public function calculate_leave_deduction($staff_id, $leave_type_id, $days)
    {
        $salary = $this->get_staff_salary($staff_id);
        if (!$salary) return 0;
        
        $daily_rate = $salary['basic_salary'] / 30;
        return round($daily_rate * $days, 2);
    }
    
    public function calculate_overtime_rate($staff_id)
    {
        $salary = $this->get_staff_salary($staff_id);
        if (!$salary) return 0;
        
        $monthly_hours = 22 * 8;
        $hourly_rate = $salary['basic_salary'] / $monthly_hours;
        return round($hourly_rate * 1.5, 2);
    }
    
    public function calculate_late_deduction($staff_id, $minutes)
    {
        $salary = $this->get_staff_salary($staff_id);
        if (!$salary) return 0;
        
        $monthly_minutes = 22 * 8 * 60;
        $per_minute_rate = $salary['basic_salary'] / $monthly_minutes;
        return round($per_minute_rate * $minutes, 2);
    }
    
    public function calculate_leave_pro_rata($staff_id, $leave_type_id, $year = null)
    {
        $year = $year ?: date('Y');
        
        $leave_type = $this->get_leave_type($leave_type_id);
        if (!$leave_type) return 0;
        
        $staff = $this->db->where('staffid', $staff_id)->get(db_prefix() . 'staff')->row();
        if (!$staff || !$staff->date_of_joining) return $leave_type['default_days'];
        
        $joining_date = date('Y-m-d', strtotime($staff->date_of_joining));
        $year_start = "$year-01-01";
        $year_end = "$year-12-31";
        
        if ($joining_date > $year_end) return 0;
        
        $eligible_months = 12;
        $year_start_for_calc = $year_start;
        
        if ($joining_date > $year_start) {
            $join_month = date('n', strtotime($joining_date));
            $eligible_months = 12 - $join_month + 1;
        }
        
        $prorata_days = ($leave_type['default_days'] / 12) * $eligible_months;
        return round($prorata_days, 2);
    }
    
    // ==================== BANK DETAILS ====================
    
    public function get_staff_bank_details($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)->get(db_prefix() . 'hr_bank_details')->result_array();
    }
    
    public function get_primary_bank_details($staff_id)
    {
        $result = $this->db->where('staff_id', $staff_id)
                          ->where('is_primary', 1)
                          ->get(db_prefix() . 'hr_bank_details')->row();
        if (!$result) {
            $result = $this->db->where('staff_id', $staff_id)
                              ->order_by('id', 'ASC')
                              ->get(db_prefix() . 'hr_bank_details')->row();
        }
        return $result;
    }
    
    public function add_bank_details($data)
    {
        if (!isset($data['is_primary'])) {
            $data['is_primary'] = 1;
        }
        if ($data['is_primary']) {
            $this->db->where('staff_id', $data['staff_id']);
            $this->db->update(db_prefix() . 'hr_bank_details', ['is_primary' => 0]);
        }
        
        $this->db->insert(db_prefix() . 'hr_bank_details', $data);
        return $this->db->insert_id();
    }
    
    public function update_bank_details($id, $data)
    {
        if (isset($data['is_primary']) && $data['is_primary']) {
            $staff_id = $this->db->select('staff_id')->where('id', $id)->get(db_prefix() . 'hr_bank_details')->row()->staff_id;
            $this->db->where('staff_id', $staff_id);
            $this->db->update(db_prefix() . 'hr_bank_details', ['is_primary' => 0]);
        }
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_bank_details', $data);
        return true;
    }
    
    public function delete_bank_details($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'hr_bank_details');
        return true;
    }
    
    // ==================== EMERGENCY CONTACTS ====================
    
    public function get_emergency_contacts($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)->get(db_prefix() . 'hr_emergency_contacts')->result_array();
    }
    
    public function add_emergency_contact($data)
    {
        if (!isset($data['is_primary'])) {
            $data['is_primary'] = 1;
        }
        if ($data['is_primary']) {
            $this->db->where('staff_id', $data['staff_id']);
            $this->db->update(db_prefix() . 'hr_emergency_contacts', ['is_primary' => 0]);
        }
        
        $this->db->insert(db_prefix() . 'hr_emergency_contacts', $data);
        return $this->db->insert_id();
    }
    
    public function update_emergency_contact($id, $data)
    {
        if (isset($data['is_primary']) && $data['is_primary']) {
            $staff_id = $this->db->select('staff_id')->where('id', $id)->get(db_prefix() . 'hr_emergency_contacts')->row()->staff_id;
            $this->db->where('staff_id', $staff_id);
            $this->db->update(db_prefix() . 'hr_emergency_contacts', ['is_primary' => 0]);
        }
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_emergency_contacts', $data);
        return true;
    }
    
    public function delete_emergency_contact($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'hr_emergency_contacts');
        return true;
    }
    
    // ==================== DEPENDENTS ====================
    
    public function get_dependents($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)->get(db_prefix() . 'hr_dependents')->result_array();
    }
    
    public function add_dependent($data)
    {
        $this->db->insert(db_prefix() . 'hr_dependents', $data);
        return $this->db->insert_id();
    }
    
    public function update_dependent($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_dependents', $data);
        return true;
    }
    
    public function delete_dependent($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'hr_dependents');
        return true;
    }
    
    // ==================== DOCUMENTS ====================
    
    public function get_staff_documents($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)
                        ->order_by('created_at', 'DESC')
                        ->get(db_prefix() . 'hr_documents')->result_array();
    }
    
    public function add_document($data)
    {
        $this->db->insert(db_prefix() . 'hr_documents', $data);
        return $this->db->insert_id();
    }
    
    public function update_document($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hr_documents', $data);
        return true;
    }
    
    public function delete_document($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'hr_documents');
        return true;
    }
    
    // ==================== LEAVE AUTO-ACCRUAL ====================
    
    public function auto_allocate_leave($staff_id, $year = null)
    {
        $year = $year ?: date('Y');
        
        $leave_types = $this->get_leave_types();
        foreach ($leave_types as $type) {
            $existing = $this->db->where('staff_id', $staff_id)
                                ->where('leave_type_id', $type['id'])
                                ->where('year', $year)
                                ->get(db_prefix() . 'hr_leave_allocations')->row();
            
            if (!$existing) {
                $prorata_days = $this->calculate_leave_pro_rata($staff_id, $type['id'], $year);
                
                $this->db->insert(db_prefix() . 'hr_leave_allocations', [
                    'staff_id' => $staff_id,
                    'leave_type_id' => $type['id'],
                    'days' => $prorata_days,
                    'year' => $year,
                ]);
            }
        }
        
        return true;
    }
    
    public function get_leave_balance_by_type($staff_id, $leave_type_id, $year = null)
    {
        $year = $year ?: date('Y');
        
        $allocation = $this->db->where('staff_id', $staff_id)
                              ->where('leave_type_id', $leave_type_id)
                              ->where('year', $year)
                              ->get(db_prefix() . 'hr_leave_allocations')->row();
        
        if (!$allocation) return 0;
        
        $used = $this->db->select_sum('days')
                         ->where('staff_id', $staff_id)
                         ->where('leave_type_id', $leave_type_id)
                         ->where('status', 'approved')
                         ->where('YEAR(start_date)', $year)
                         ->get(db_prefix() . 'hr_leave_requests')->row()->days;
        
        return $allocation->days - ($used ?: 0);
    }
    
    public function get_leave_type($id)
    {
        return $this->db->where('id', $id)->get(db_prefix() . 'hr_leave_types')->row_array();
    }
}
