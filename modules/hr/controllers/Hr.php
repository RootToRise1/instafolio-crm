<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hr extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('hr/hr_model');
        $this->load->model('staff_model');
    }

    public function index()
    {
        redirect(admin_url('hr/employees'));
    }

    // Employees
    public function employees()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('hr_employees');
            return;
        }
        
        if (!is_admin() && !staff_can('view', 'hr')) {
            access_denied('hr');
        }
        
        $data['departments'] = $this->hr_model->get_departments();
        $data['title'] = _l('hr_employees');
        $data['submenu'] = true;
        
        $this->load->view('admin/hr/employees', $data);
    }

    public function table()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('hr_employees');
        }
    }

    public function bulk_action()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            if (isset($data['mass_delete']) && $data['mass_delete']) {
                if (staff_can('delete', 'hr') || is_admin()) {
                    $ids = $data['ids'];
                    foreach ($ids as $id) {
                        if ($id != get_staff_user_id()) {
                            $this->staff_model->delete($id);
                        }
                    }
                }
            } else {
                if (isset($data['department']) && is_array($data['department']) && count($data['department']) > 0) {
                    foreach ($data['ids'] as $id) {
                        $this->db->where('staffid', $id);
                        $this->db->update(db_prefix() . 'staff', ['department_id' => end($data['department'])]);
                    }
                }
                
                if (isset($data['role']) && is_array($data['role']) && count($data['role']) > 0) {
                    foreach ($data['ids'] as $id) {
                        $this->db->where('staffid', $id);
                        $this->db->update(db_prefix() . 'staff', ['hr_role_id' => end($data['role'])]);
                    }
                }
            }
            
            set_alert('success', _l('mass_action_performed'));
        }
        redirect(admin_url('hr/employees'));
    }
    
    public function add_bank_details($staff_id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['staff_id'] = $staff_id;
            unset($data['staff_id']); // Will use the parameter
            
            $this->hr_model->add_bank_details([
                'staff_id' => $staff_id,
                'bank_name' => $this->input->post('bank_name'),
                'account_name' => $this->input->post('account_name'),
                'account_number' => $this->input->post('account_number'),
                'routing_number' => $this->input->post('routing_number'),
                'iban' => $this->input->post('iban'),
                'swift_code' => $this->input->post('swift_code'),
                'is_primary' => $this->input->post('is_primary') ? 1 : 0,
            ]);
            
            set_alert('success', _l('added_successfully', _l('hr_bank_details')));
        }
        redirect(admin_url('hr/employee/' . $staff_id . '?group=bank'));
    }
    
    public function delete_bank_details($id)
    {
        $staff_id = $this->hr_model->db->where('id', $id)->get(db_prefix() . 'hr_bank_details')->row()->staff_id ?? 0;
        $this->hr_model->delete_bank_details($id);
        set_alert('success', _l('deleted', _l('hr_bank_details')));
        redirect(admin_url('hr/employee/' . $staff_id . '?group=bank'));
    }
    
    public function add_emergency_contact($staff_id)
    {
        if ($this->input->post()) {
            $this->hr_model->add_emergency_contact([
                'staff_id' => $staff_id,
                'name' => $this->input->post('name'),
                'relationship' => $this->input->post('relationship'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'is_primary' => $this->input->post('is_primary') ? 1 : 0,
            ]);
            
            set_alert('success', _l('added_successfully', _l('hr_emergency_contact')));
        }
        redirect(admin_url('hr/employee/' . $staff_id . '?group=emergency'));
    }
    
    public function delete_emergency_contact($id)
    {
        $staff_id = $this->hr_model->db->where('id', $id)->get(db_prefix() . 'hr_emergency_contacts')->row()->staff_id ?? 0;
        $this->hr_model->delete_emergency_contact($id);
        set_alert('success', _l('deleted', _l('hr_emergency_contact')));
        redirect(admin_url('hr/employee/' . $staff_id . '?group=emergency'));
    }
    
    public function add_document($staff_id)
    {
        if ($this->input->post()) {
            $data = [
                'staff_id' => $staff_id,
                'document_type' => $this->input->post('document_type'),
                'document_name' => $this->input->post('document_name'),
                'expiry_date' => $this->input->post('expiry_date') ? date('Y-m-d', strtotime($this->input->post('expiry_date'))) : null,
            ];
            
            if (isset($_FILES['document_file']) && $_FILES['document_file']['size'] > 0) {
                upload_hr_document($staff_id);
                $data['file_path'] = 'uploads/hr/documents/' . $staff_id . '/' . $_FILES['document_file']['name'];
            }
            
            $this->hr_model->add_document($data);
            set_alert('success', _l('added_successfully', _l('hr_document')));
        }
        redirect(admin_url('hr/employee/' . $staff_id . '?group=documents'));
    }
    
    public function delete_document($id)
    {
        $staff_id = $this->hr_model->db->where('id', $id)->get(db_prefix() . 'hr_documents')->row()->staff_id ?? 0;
        $this->hr_model->delete_document($id);
        set_alert('success', _l('deleted', _l('hr_document')));
        redirect(admin_url('hr/employee/' . $staff_id . '?group=documents'));
    }
    
    public function update_salary($staff_id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            $this->db->where('staff_id', $staff_id);
            $this->db->where('is_active', 1);
            $existing = $this->db->get(db_prefix() . 'hr_payroll_salary')->row();
            
            if ($existing) {
                $this->db->where('staff_id', $staff_id);
                $this->db->where('is_active', 1);
                $this->db->update(db_prefix() . 'hr_payroll_salary', [
                    'base_salary' => $data['base_salary'],
                    'hourly_rate' => isset($data['hourly_rate']) ? $data['hourly_rate'] : null,
                ]);
            } else {
                $this->db->insert(db_prefix() . 'hr_payroll_salary', [
                    'staff_id' => $staff_id,
                    'base_salary' => $data['base_salary'],
                    'hourly_rate' => isset($data['hourly_rate']) ? $data['hourly_rate'] : null,
                    'effective_from' => date('Y-m-d'),
                    'is_active' => 1,
                ]);
            }
            
            set_alert('success', _l('updated_successfully', _l('hr_salary')));
        }
        redirect(admin_url('hr/employee/' . $staff_id . '?group=salary'));
    }
    
    public function auto_allocate_leave($staff_id)
    {
        $this->hr_model->auto_allocate_leave($staff_id);
        set_alert('success', _l('hr_leave_allocated_successfully'));
        redirect(admin_url('hr/employee/' . $staff_id . '?group=salary'));
    }

    public function change_employee_status($id, $status)
    {
        if (is_admin() || staff_can('edit', 'hr')) {
            $this->staff_model->change_staff_status($id, $status);
            set_alert('success', _l('updated_successfully'));
        }
        redirect($_SERVER['HTTP_REFERER'] ?? admin_url('hr/employees'));
    }

    public function mark_as_active($id)
    {
        if (is_admin() || staff_can('edit', 'hr')) {
            $this->db->where('staffid', $id);
            $this->db->update(db_prefix() . 'staff', ['active' => 1]);
            set_alert('success', _l('updated_successfully'));
        }
        redirect($_SERVER['HTTP_REFERER'] ?? admin_url('hr/employees'));
    }

    public function remove_staff_profile_image($staff_id)
    {
        if (is_admin() || staff_can('edit', 'hr')) {
            $this->staff_model->remove_staff_profile_image($staff_id);
            set_alert('success', _l('staff_profile_image_removed'));
        }
        redirect(admin_url('hr/employee/' . $staff_id));
    }

    public function employee($id = '', $group = 'profile')
    {
        if ($id != '' && $id != $this->session->userdata('staff_user_id')) {
            if (!is_admin() && !staff_can('view', 'hr')) {
                access_denied('hr');
            }
        }

        hooks()->do_action('staff_member_edit_view_profile', $id);

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['email_signature'] = $this->input->post('email_signature', false);
            $password = $this->input->post('password', false);
            
            if ($id == '') {
                if (!is_admin() && !staff_can('create', 'hr')) {
                    access_denied('hr');
                }
                $id = $this->staff_model->add($data, $password);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('hr_employee')));
                    redirect(admin_url('hr/employee/' . $id));
                } else {
                    set_alert('danger', 'Error adding employee');
                    redirect(admin_url('hr/employees'));
                }
            } else {
                if (!is_admin() && !staff_can('edit', 'hr') && $id != get_staff_user_id()) {
                    access_denied('hr');
                }
                handle_staff_profile_image_upload($id);
                $response = $this->staff_model->update($data, $id, $password);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('hr_employee')));
                }
                redirect(admin_url('hr/employee/' . $id . '?group=' . $group));
            }
        }

        if ($id != '') {
            $data['member'] = $this->staff_model->get($id);
            if (!$data['member']) {
                show_error('Employee not found', 404);
            }
            
            $data['employee_attendance'] = $this->hr_model->get_staff_attendance($id, date('Y-01-01'), date('Y-12-31'));
            $data['employee_leave'] = $this->hr_model->get_leave_requests($id);
            $data['leave_types'] = $this->hr_model->get_leave_types();
            
            // Bank Details
            $data['hr_bank_details'] = $this->hr_model->get_staff_bank_details($id);
            
            // Emergency Contacts
            $data['hr_emergency_contacts'] = $this->hr_model->get_emergency_contacts($id);
            
            // Documents
            $data['hr_documents'] = $this->hr_model->get_staff_documents($id);
            
            // Salary & Pro-rata
            $data['hr_salary_info'] = $this->hr_model->get_staff_salary($id);
            $data['hr_prorata_info'] = $this->hr_model->calculate_monthly_salary($id);
            
            // Leave Balances
            $leave_balances_raw = $this->hr_model->get_leave_balance($id);
            $leave_balances = [];
            foreach ($leave_balances_raw as $lb) {
                $leave_balances[] = [
                    'leave_type_name' => $lb['name'],
                    'allocated' => $lb['allocated'],
                    'used' => $lb['used'],
                    'pending' => $lb['pending'],
                    'balance' => $lb['available'],
                ];
            }
            $data['leave_balances'] = $leave_balances;
        } else {
            $data['member'] = null;
        }

        $data['group'] = $group;
        
        $data['departments'] = $this->hr_model->get_departments();
        $data['designations'] = $this->hr_model->get_designations();
        $data['shifts'] = $this->hr_model->get_shifts();
        $data['roles'] = $this->hr_model->get_roles();
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['title'] = $id == '' ? _l('hr_add_employee') : _l('hr_employee_profile');
        $data['submenu'] = true;
        
        $this->load->view('admin/hr/employee', $data);
    }

    public function delete($id = '')
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
        }
        
        if (is_admin() || staff_can('delete', 'hr')) {
            if ($id != get_staff_user_id()) {
                $this->staff_model->delete($id);
                set_alert('success', _l('deleted', _l('hr_employee')));
            }
        }
        redirect(admin_url('hr/employees'));
    }

    public function update_staff_role()
    {
        if ($this->input->is_ajax_request()) {
            $staff_id = $this->input->post('staff_id');
            $role_id = $this->input->post('role_id');
            
            if (!$staff_id) {
                echo json_encode(['success' => false, 'message' => 'Staff ID required']);
                return;
            }
            
            $this->db->where('staffid', $staff_id);
            $this->db->update(db_prefix() . 'staff', ['hr_role_id' => $role_id ?: null]);
            
            echo json_encode(['success' => true, 'message' => 'Role updated']);
        }
    }

    // Attendance
    public function attendance()
    {
        if (!staff_can('view', 'hr')) {
            access_denied('hr');
        }
        
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('hr_attendance');
        }
        
        $data['title'] = _l('hr_attendance');
        $data['submenu'] = true;
        $this->load->view('admin/hr/attendance', $data);
    }

    public function attendance_report()
    {
        if (!staff_can('view_reports', 'hr')) {
            access_denied('hr');
        }

        $start_date = $this->input->get('start_date') ?? date('Y-01-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $department_id = $this->input->get('department_id');

        $data['report'] = $this->hr_model->get_attendance_report($start_date, $end_date, $department_id);
        $data['departments'] = $this->hr_model->get_departments();
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['department_id'] = $department_id;
        $data['title'] = _l('hr_attendance_report');
        $data['submenu'] = true;

        $this->load->view('admin/hr/attendance_report', $data);
    }

    public function my_attendance()
    {
        $staff_id = get_staff_user_id();
        $start_date = $this->input->get('start_date') ?? date('Y-01-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');

        $data['attendance'] = $this->hr_model->get_staff_attendance($staff_id, $start_date, $end_date);
        $data['today_attendance'] = $this->hr_model->get_today_attendance($staff_id);
        $data['title'] = _l('hr_my_attendance');
        $data['submenu'] = true;
        $this->load->view('admin/hr/my_attendance', $data);
    }

    // Check In/Out
    public function clock_in()
    {
        $staff_id = get_staff_user_id();
        $result = $this->hr_model->clock_in($staff_id);
        
        if ($result['success']) {
            set_alert('success', _l('hr_check_in_success'));
        } else {
            set_alert('danger', $result['message']);
        }
        
        redirect(admin_url('hr/my_attendance'));
    }

    public function clock_out()
    {
        $staff_id = get_staff_user_id();
        $result = $this->hr_model->clock_out($staff_id);
        
        if ($result['success']) {
            set_alert('success', _l('hr_check_out_success') . ' (' . $result['total_hours'] . ' hours)');
        } else {
            set_alert('danger', $result['message']);
        }
        
        redirect(admin_url('hr/my_attendance'));
    }

    public function break_in()
    {
        $staff_id = get_staff_user_id();
        $result = $this->hr_model->break_in($staff_id);
        
        if ($result['success']) {
            set_alert('success', _l('hr_break_in_success'));
        } else {
            set_alert('danger', $result['message']);
        }
        
        redirect(admin_url('hr/my_attendance'));
    }

    public function break_out()
    {
        $staff_id = get_staff_user_id();
        $result = $this->hr_model->break_out($staff_id);
        
        if ($result['success']) {
            set_alert('success', _l('hr_break_out_success'));
        } else {
            set_alert('danger', $result['message']);
        }
        
        redirect(admin_url('hr/my_attendance'));
    }

    // Leave
    public function leave()
    {
        if (!staff_can('view', 'hr')) {
            access_denied('hr');
        }
        
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('hr_leave');
        }
        
        $data['title'] = _l('hr_leave');
        $data['submenu'] = true;
        $this->load->view('admin/hr/leave', $data);
    }

    public function my_leave()
    {
        $staff_id = get_staff_user_id();
        
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['staff_id'] = $staff_id;
            $data['days'] = (strtotime($data['end_date']) - strtotime($data['start_date'])) / (60 * 60 * 24) + 1;
            $id = $this->hr_model->submit_leave_request($data);
            if ($id) {
                set_alert('success', _l('hr_record_saved'));
            }
            redirect(admin_url('hr/my_leave'));
        }

        $data['leave_types'] = $this->hr_model->get_leave_types();
        $data['leave_balances'] = $this->hr_model->get_leave_balance($staff_id);
        $data['title'] = _l('hr_my_leave');
        $data['submenu'] = true;
        $this->load->view('admin/hr/my_leave', $data);
    }

    public function approve_leave($id)
    {
        if (!staff_can('approve_leave', 'hr')) {
            access_denied('hr');
        }

        $this->hr_model->approve_leave($id, get_staff_user_id());
        set_alert('success', _l('hr_leave_approved'));
        redirect(admin_url('hr/leave'));
    }

    public function reject_leave($id)
    {
        if (!staff_can('approve_leave', 'hr')) {
            access_denied('hr');
        }

        $reason = $this->input->post('reason');
        $this->hr_model->reject_leave($id, get_staff_user_id(), $reason);
        set_alert('success', _l('hr_leave_rejected'));
        redirect(admin_url('hr/leave'));
    }

    // Performance
    public function performance()
    {
        if (!staff_can('view', 'hr')) {
            access_denied('hr');
        }
        
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('hr_performance');
        }
        
        $data['title'] = _l('hr_performance');
        $data['submenu'] = true;
        $this->load->view('admin/hr/performance', $data);
    }

    public function add_review()
    {
        if (!staff_can('create', 'hr')) {
            access_denied('hr');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['reviewer_id'] = get_staff_user_id();
            $data['status'] = 'scheduled';
            $id = $this->hr_model->add_performance_review($data);
            if ($id) {
                set_alert('success', _l('hr_record_saved'));
            }
            redirect(admin_url('hr/performance'));
        }
        
        $data['title'] = _l('hr_add_review');
        $data['submenu'] = true;
        $this->load->view('admin/hr/review_form', $data);
    }

    public function acknowledge_review($id)
    {
        $review = $this->db->where('id', $id)
                          ->where('staff_id', get_staff_user_id())
                          ->get(db_prefix() . 'hr_performance_reviews')->row();
        
        if (!$review) {
            access_denied('hr');
        }

        $this->hr_model->acknowledge_review($id);
        set_alert('success', _l('hr_acknowledged'));
        redirect(admin_url('hr/performance'));
    }

    // Reports
    public function reports()
    {
        if (!staff_can('view_reports', 'hr')) {
            access_denied('hr');
        }

        $type = $this->input->get('type') ?? 'attendance';

        if ($type == 'attendance') {
            $this->attendance_report();
            return;
        }

        if ($type == 'leave') {
            $data['title'] = _l('hr_leave_utilization');
            $data['report'] = $this->hr_model->get_leave_utilization_report();
            $data['submenu'] = true;
            $this->load->view('admin/hr/leave_report', $data);
            return;
        }

        $data['title'] = _l('hr_reports');
        $data['departments'] = $this->hr_model->get_departments();
        $data['submenu'] = true;
        $this->load->view('admin/hr/reports', $data);
    }

    // Setup
    public function setup($type = 'departments', $id = '')
    {
        if (!is_admin()) {
            access_denied('hr');
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();
            $id = isset($post_data['id']) ? $post_data['id'] : '';
            unset($post_data['id']);

            $function = 'add_' . rtrim($type, 's');
            if ($id) {
                $function = 'update_' . rtrim($type, 's');
            }

            if (method_exists($this->hr_model, $function)) {
                if ($id) {
                    $this->hr_model->$function($id, $post_data);
                } else {
                    $this->hr_model->$function($post_data);
                }
                set_alert('success', _l('hr_record_saved'));
            }
            redirect(admin_url('hr/setup/' . $type));
        }

        $data['title'] = _l('hr_' . $type);
        $data['items'] = $this->hr_model->{'get_' . $type}();
        
        // Load single item for editing
        if ($id != '') {
            $single_function = 'get_' . rtrim($type, 's');
            if (method_exists($this->hr_model, $single_function)) {
                $data['id'] = $id;
                if ($type == 'departments') {
                    $data['department'] = $this->hr_model->$single_function($id);
                } elseif ($type == 'designations') {
                    $data['designation'] = $this->hr_model->$single_function($id);
                } elseif ($type == 'shifts') {
                    $data['shift'] = $this->hr_model->$single_function($id);
                } elseif ($type == 'holidays') {
                    $data['holiday'] = $this->hr_model->$single_function($id);
                }
            }
        }
        
        if ($type == 'departments') {
            $data['staff_members'] = $this->staff_model->get();
        }
        if ($type == 'designations') {
            $data['departments'] = $this->hr_model->get_departments();
        }
        
        $data['submenu'] = true;
        $this->load->view('admin/hr/setup_' . rtrim($type, 's'), $data);
    }

    public function delete_setup($type, $id)
    {
        if (!is_admin()) {
            access_denied('hr');
        }

        $function = 'delete_' . rtrim($type, 's');
        if (method_exists($this->hr_model, $function)) {
            $this->hr_model->$function($id);
            set_alert('success', _l('hr_record_deleted'));
        }
        redirect(admin_url('hr/setup/' . $type));
    }

    // Leave Types Management
    public function leave_types()
    {
        if (!is_admin()) {
            access_denied('hr');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $id = isset($data['id']) ? $data['id'] : '';
            unset($data['id']);

            if ($id) {
                $this->hr_model->update_leave_type($id, $data);
            } else {
                $this->hr_model->add_leave_type($data);
            }
            set_alert('success', _l('hr_record_saved'));
            redirect(admin_url('hr/leave_types'));
        }

        $data['title'] = _l('hr_leave_types');
        $data['types'] = $this->hr_model->get_leave_types();
        $data['submenu'] = true;
        $this->load->view('admin/hr/leave_types', $data);
    }

    public function delete_leave_type($id)
    {
        if (!is_admin()) {
            access_denied('hr');
        }

        $this->hr_model->delete_leave_type($id);
        set_alert('success', _l('hr_record_deleted'));
        redirect(admin_url('hr/leave_types'));
    }

    // Get attendance by date for AJAX
    public function get_attendance()
    {
        if ($this->input->is_ajax_request()) {
            $date = $this->input->get('date');
            $department_id = $this->input->get('department_id');
            
            if ($department_id) {
                $data = $this->hr_model->get_department_attendance($department_id, $date);
            } else {
                $this->db->select('att.*, s.firstname, s.lastname, s.email');
                $this->db->from(db_prefix() . 'hr_attendance att');
                $this->db->join(db_prefix() . 'staff s', 's.staffid = att.staff_id');
                $this->db->where('att.date', $date);
                $data = $this->db->get()->result_array();
            }
            
            echo json_encode($data);
        }
    }

    // Get today's attendance status for current user
    public function get_my_status()
    {
        if ($this->input->is_ajax_request()) {
            $staff_id = get_staff_user_id();
            $attendance = $this->hr_model->get_today_attendance($staff_id);
            
            $on_break = false;
            if ($attendance) {
                $on_break = $attendance->break_in && !$attendance->break_out;
            }
            
            echo json_encode([
                'clocked_in' => $attendance && !$attendance->checkout,
                'clocked_out' => $attendance && $attendance->checkout,
                'on_break' => $on_break,
                'attendance' => $attendance,
            ]);
        }
    }

    // ==================== PAYROLL METHODS ====================

    public function payroll()
    {
        if (!staff_can('view', 'hr')) {
            access_denied('hr');
        }
        
        $data['title'] = _l('hr_payroll');
        $data['salaries'] = $this->hr_model->get_all_salaries();
        $data['submenu'] = true;
        $this->load->view('admin/hr/payroll', $data);
    }

    public function salary($id = '')
    {
        if (!is_admin() && !staff_can('edit', 'hr')) {
            access_denied('hr');
        }
        
        if ($this->input->post()) {
            $data = $this->input->post();
            $existing_id = isset($data['id']) ? $data['id'] : '';
            
            if ($existing_id) {
                $this->hr_model->update_salary($existing_id, $data);
                set_alert('success', _l('hr_record_updated'));
            } else {
                $this->hr_model->add_salary($data);
                set_alert('success', _l('hr_record_added'));
            }
            redirect(admin_url('hr/payroll'));
        }
        
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        if ($id) {
            $data['salary'] = $this->hr_model->get_staff_salary($id);
        }
        $data['title'] = $id ? _l('hr_edit_salary') : _l('hr_add_salary');
        $data['submenu'] = true;
        $this->load->view('admin/hr/salary', $data);
    }

    public function payslips($staff_id = '')
    {
        if (!staff_can('view', 'hr') && $staff_id != get_staff_user_id()) {
            access_denied('hr');
        }
        
        if ($staff_id == '' && !staff_can('view', 'hr')) {
            $staff_id = get_staff_user_id();
        }
        
        $data['title'] = _l('hr_payslips');
        $data['payslips'] = $this->hr_model->get_payslips($staff_id);
        $data['staff_id'] = $staff_id;
        $data['submenu'] = true;
        $this->load->view('admin/hr/payslips', $data);
    }

    public function generate_payslip()
    {
        if (!is_admin() && !staff_can('create', 'hr')) {
            access_denied('hr');
        }
        
        if ($this->input->post()) {
            $staff_id = $this->input->post('staff_id');
            $period_start = $this->input->post('pay_period_start');
            $period_end = $this->input->post('pay_period_end');
            
            $id = $this->hr_model->create_payslip($staff_id, $period_start, $period_end);
            if ($id) {
                set_alert('success', _l('hr_payslip_generated'));
            } else {
                set_alert('danger', _l('hr_payslip_error'));
            }
            redirect(admin_url('hr/payslips'));
        }
    }

    public function payslip($id)
    {
        $payslip = $this->hr_model->get_payslip($id);
        
        if (!$payslip) {
            show_404();
        }
        
        if ($payslip['staff_id'] != get_staff_user_id() && !staff_can('view', 'hr')) {
            access_denied('hr');
        }
        
        $data['payslip'] = $payslip;
        $data['title'] = _l('hr_payslip') . ' - ' . _d($payslip['pay_period_start']) . ' to ' . _d($payslip['pay_period_end']);
        $this->load->view('admin/hr/payslip_detail', $data);
    }

    public function update_payslip_status($id, $status)
    {
        if (!is_admin() && !staff_can('edit', 'hr')) {
            access_denied('hr');
        }
        
        $this->hr_model->update_payslip_status($id, $status);
        set_alert('success', _l('hr_status_updated'));
        redirect(admin_url('hr/payslips'));
    }

    public function allowances($id = '')
    {
        if (!is_admin()) {
            access_denied('hr');
        }
        
        if ($this->input->post()) {
            $data = $this->input->post();
            $post_id = isset($data['id']) ? $data['id'] : '';
            unset($data['id']);
            
            if ($post_id) {
                $this->db->where('id', $post_id)->update(db_prefix() . 'hr_payroll_allowances', $data);
            } else {
                $this->hr_model->add_allowance($data);
            }
            set_alert('success', _l('hr_record_saved'));
            redirect(admin_url('hr/allowances'));
        }
        
        $data['title'] = _l('hr_allowances');
        $data['allowances'] = $this->hr_model->get_allowances();
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        
        if ($id != '') {
            $data['allowance'] = $this->db->where('id', $id)->get(db_prefix() . 'hr_payroll_allowances')->row_array();
        }
        
        $data['submenu'] = true;
        $this->load->view('admin/hr/allowances', $data);
    }
    
    public function delete_allowance($id)
    {
        if (!is_admin()) {
            access_denied('hr');
        }
        
        $this->db->where('id', $id)->delete(db_prefix() . 'hr_payroll_allowances');
        set_alert('success', _l('hr_record_deleted'));
        redirect(admin_url('hr/allowances'));
    }

    public function my_salary()
    {
        $staff_id = get_staff_user_id();
        $data['salary'] = $this->hr_model->get_staff_salary($staff_id);
        $data['payslips'] = $this->hr_model->get_payslips($staff_id);
        $data['title'] = _l('hr_my_salary');
        $data['submenu'] = true;
        $this->load->view('admin/hr/my_salary', $data);
    }

    public function pro_rata_calculator()
    {
        if ($this->input->is_ajax_request()) {
            $staff_id = $this->input->get('staff_id');
            $month = $this->input->get('month') ?: date('Y-m');
            
            $result = $this->hr_model->calculate_pro_rata($staff_id, $month);
            echo json_encode(['pro_rata' => $result]);
            return;
        }
        
        $data['title'] = _l('hr_pro_rata_calculator');
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['submenu'] = true;
        $this->load->view('admin/hr/pro_rata_calculator', $data);
    }

    public function roles($id = '')
    {
        if (!is_admin()) {
            access_denied('hr');
        }
        
        if ($this->input->post()) {
            $data = $this->input->post();
            $permissions = isset($data['permissions']) ? $data['permissions'] : [];
            unset($data['permissions']);
            
            $data['permissions'] = json_encode($permissions);
            
            if (isset($data['id']) && $data['id']) {
                $this->db->where('id', $data['id'])->update(db_prefix() . 'hr_roles', $data);
            } else {
                unset($data['id']);
                $this->db->insert(db_prefix() . 'hr_roles', $data);
            }
            
            set_alert('success', _l('hr_record_saved'));
            redirect(admin_url('hr/roles'));
        }
        
        $data['roles'] = $this->hr_model->get_roles();
        
        if ($id != '') {
            $role = $this->db->where('id', $id)->get(db_prefix() . 'hr_roles')->row_array();
            if ($role) {
                $data['role'] = $role;
                $data['permissions'] = json_decode($role['permissions'], true) ?? [];
            }
        }
        
        $data['title'] = _l('hr_roles_management');
        $data['submenu'] = true;
        $this->load->view('admin/hr/roles', $data);
    }
}
