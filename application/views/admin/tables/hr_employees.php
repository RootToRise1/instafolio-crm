<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = staff_can('delete', 'hr') || is_admin();
$has_permission_edit = staff_can('edit', 'hr') || is_admin();

$CI = &get_instance();

// Get total count
$CI->db->select('COUNT(*) as total');
$CI->db->from(db_prefix() . 'staff');
$total = $CI->db->get()->row()->total;

// Get data with joins
$CI->db->select([
    db_prefix() . 'staff.staffid',
    db_prefix() . 'staff.firstname',
    db_prefix() . 'staff.lastname',
    db_prefix() . 'staff.email',
    db_prefix() . 'staff.active',
    'dept.name as department_name',
    'desig.name as designation_name',
    'role.name as role_name',
], false);
$CI->db->from(db_prefix() . 'staff');
$CI->db->join(db_prefix() . 'hr_departments as dept', 'dept.id = ' . db_prefix() . 'staff.department_id', 'left');
$CI->db->join(db_prefix() . 'hr_designations as desig', 'desig.id = ' . db_prefix() . 'staff.designation_id', 'left');
$CI->db->join(db_prefix() . 'hr_roles as role', 'role.id = ' . db_prefix() . 'staff.hr_role_id', 'left');

// Pagination
$start = $CI->input->post('start') ? intval($CI->input->post('start')) : 0;
$length = $CI->input->post('length') ? intval($CI->input->post('length')) : 25;
if ($length != -1) {
    $CI->db->limit($length, $start);
}

// Order
if ($CI->input->post('order')) {
    $columns = ['firstname', 'email', 'department_name', 'designation_name', 'role_name', 'active'];
    $order = $CI->input->post('order');
    $colIndex = intval($order[0]['column']);
    $dir = strtoupper($order[0]['dir']);
    if (isset($columns[$colIndex])) {
        $CI->db->order_by($columns[$colIndex], $dir);
    }
} else {
    $CI->db->order_by('firstname', 'ASC');
}

$rResult = $CI->db->get()->result_array();

// Build output
$output = [
    'sEcho' => intval($CI->input->post('draw')),
    'iTotalRecords' => $total,
    'iTotalDisplayRecords' => $total,
    'aaData' => [],
];

foreach ($rResult as $aRow) {
    $row = [];
    
    // Name column
    $name = '<a href="' . admin_url('hr/employee/' . $aRow['staffid']) . '">' . staff_profile_image($aRow['staffid'], ['staff-profile-image-small']) . '</a>';
    $name .= ' <a href="' . admin_url('hr/employee/' . $aRow['staffid']) . '">' . e($aRow['firstname'] . ' ' . $aRow['lastname']) . '</a>';
    $name .= '<div class="row-options">';
    $name .= '<a href="' . admin_url('hr/employee/' . $aRow['staffid']) . '">' . _l('view') . '</a>';
    if ($has_permission_edit) {
        $name .= ' | <a href="' . admin_url('hr/employee/' . $aRow['staffid']) . '">' . _l('edit') . '</a>';
    }
    if ($has_permission_delete && $aRow['staffid'] != get_staff_user_id() && $total > 1) {
        $name .= ' | <a href="#" onclick="delete_employee(' . $aRow['staffid'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
    }
    $name .= '</div>';
    $row[] = $name;
    
    // Email
    $row[] = '<a href="mailto:' . e($aRow['email']) . '">' . e($aRow['email']) . '</a>';
    
    // Department
    $row[] = $aRow['department_name'] ? e($aRow['department_name']) : '<span class="text-muted">-</span>';
    
    // Designation
    $row[] = $aRow['designation_name'] ? e($aRow['designation_name']) : '<span class="text-muted">-</span>';
    
    // Role
    $row[] = $aRow['role_name'] ? e($aRow['role_name']) : '<span class="text-muted">-</span>';
    
    // Active status
    $checked = $aRow['active'] == 1 ? 'checked' : '';
    $disabled = $aRow['staffid'] == get_staff_user_id() ? 'disabled' : '';
    $row[] = '<div class="onoffswitch">
        <input type="checkbox" ' . $disabled . ' data-switch-url="' . admin_url() . 'hr/change_employee_status" name="onoffswitch" class="onoffswitch-checkbox" id="emp_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
        <label class="onoffswitch-label" for="emp_' . $aRow['staffid'] . '"></label>
    </div>
    <span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
    
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}

echo json_encode($output);
die;
