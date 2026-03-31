<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: HR
Description: Human Resources Management Module - Employee, Attendance, Leave, Payroll, Performance
Version: 1.0.0
Requires at least: 2.3.*
*/

define('HR_MODULE_NAME', 'hr');

hooks()->add_action('admin_init', 'hr_permissions');
hooks()->add_action('admin_init', 'hr_module_init_menu_items');
hooks()->add_action('staff_member_deleted', 'hr_staff_member_deleted');
hooks()->add_action('after_staff_login', 'hr_add_dashboard_widgets');
hooks()->add_filter('get_dashboard_widgets', 'hr_get_dashboard_widgets');
hooks()->add_action('app_admin_head', 'hr_load_css');

function hr_load_css()
{
    echo '<link href="' . module_dir_url('hr', 'assets/css/hr_widgets.css') . '" rel="stylesheet" type="text/css">';
}

function hr_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view_own'           => _l('permission_view_own'),
        'view'               => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create'             => _l('permission_create'),
        'edit'               => _l('permission_edit'),
        'delete'             => _l('permission_delete'),
        'approve_leave'      => _l('hr_permission_approve_leave'),
        'adjust_attendance'  => _l('hr_permission_adjust_attendance'),
        'view_reports'       => _l('hr_permission_view_reports'),
    ];

    register_staff_capabilities('hr', $capabilities, _l('hr_module'));
}

function hr_add_dashboard_widgets()
{
    // Clock in/out widget will be added via filter
}

function hr_get_dashboard_widgets($widgets)
{
    $widgets[] = [
        'path'      => 'admin/hr/widget_clock',
        'container' => 'hr-top-left',
    ];

    $widgets[] = [
        'path'      => 'admin/hr/widget_leave_requests',
        'container' => 'hr-top-right',
    ];

    return $widgets;
}

function hr_staff_member_deleted($data)
{
    $CI = &get_instance();
    
    $CI->db->where('staff_id', $data['id']);
    $CI->db->update(db_prefix() . 'hr_attendance', [
        'staff_id' => $data['transfer_data_to'],
    ]);

    $CI->db->where('staff_id', $data['id']);
    $CI->db->update(db_prefix() . 'hr_leave_requests', [
        'staff_id' => $data['transfer_data_to'],
    ]);
}

function hr_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_activation_hook(HR_MODULE_NAME, 'hr_module_activation_hook');

register_language_files(HR_MODULE_NAME, [HR_MODULE_NAME]);

function hr_module_init_menu_items()
{
    $CI = &get_instance();

    if (is_staff_member()) {
        // Add HR menu for ALL staff (collapsed by default if no children shown)
        $CI->app_menu->add_sidebar_menu_item('hr', [
            'collapse' => true,
            'name'     => _l('hr_module'),
            'href'     => '#',
            'icon'     => 'fa fa-users',
            'position' => 6,
            'badge'    => [],
        ]);

        // HR Management - Only for users with HR view permission or admins
        if (staff_can('view', 'hr') || is_admin()) {
            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_employees',
                'name'     => _l('hr_employees'),
                'href'     => admin_url('hr/employees'),
                'position' => 5,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_attendance',
                'name'     => _l('hr_attendance'),
                'href'     => admin_url('hr/attendance'),
                'position' => 10,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_leave',
                'name'     => _l('hr_leave'),
                'href'     => admin_url('hr/leave'),
                'position' => 15,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_performance',
                'name'     => _l('hr_performance'),
                'href'     => admin_url('hr/performance'),
                'position' => 20,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_reports',
                'name'     => _l('hr_reports'),
                'href'     => admin_url('hr/reports'),
                'position' => 25,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_payroll',
                'name'     => _l('hr_payroll'),
                'href'     => admin_url('hr/payroll'),
                'position' => 26,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('hr', [
                'slug'     => 'hr_payslips',
                'name'     => _l('hr_payslips'),
                'href'     => admin_url('hr/payslips'),
                'position' => 27,
                'badge'    => [],
            ]);
        }

        // Personal HR items - Available to ALL staff members (clock in/out, leave request, payslip)
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_my_attendance',
            'name'     => _l('hr_my_attendance'),
            'href'     => admin_url('hr/my_attendance'),
            'position' => 30,
            'badge'    => [],
        ]);

        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_my_leave',
            'name'     => _l('hr_my_leave'),
            'href'     => admin_url('hr/my_leave'),
            'position' => 35,
            'badge'    => [],
        ]);

        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_my_salary',
            'name'     => _l('hr_my_salary'),
            'href'     => admin_url('hr/my_salary'),
            'position' => 36,
            'badge'    => [],
        ]);
    }

    // Setup menu - Only for admins
    if (is_admin()) {
        $CI->app_menu->add_setup_menu_item('hr_setup', [
            'collapse' => true,
            'name'     => _l('hr_setup'),
            'position' => 5,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_employees',
            'name'     => _l('hr_employees'),
            'href'     => admin_url('hr/employees'),
            'position' => 1,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_roles',
            'name'     => _l('hr_roles_management'),
            'href'     => admin_url('hr/roles'),
            'position' => 3,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_departments',
            'name'     => _l('hr_departments'),
            'href'     => admin_url('hr/setup/departments'),
            'position' => 5,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_designations',
            'name'     => _l('hr_designations'),
            'href'     => admin_url('hr/setup/designations'),
            'position' => 10,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_leave_types',
            'name'     => _l('hr_leave_types'),
            'href'     => admin_url('hr/leave_types'),
            'position' => 15,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_shifts',
            'name'     => _l('hr_shifts'),
            'href'     => admin_url('hr/setup/shifts'),
            'position' => 20,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_holidays',
            'name'     => _l('hr_holidays'),
            'href'     => admin_url('hr/setup/holidays'),
            'position' => 25,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_allowances',
            'name'     => _l('hr_allowances'),
            'href'     => admin_url('hr/allowances'),
            'position' => 30,
            'badge'    => [],
        ]);

        $CI->app_menu->add_setup_children_item('hr_setup', [
            'slug'     => 'hr_pro_rata_calculator',
            'name'     => _l('hr_pro_rata_calculator'),
            'href'     => admin_url('hr/pro_rata_calculator'),
            'position' => 35,
            'badge'    => [],
        ]);
    }

    $CI->app->add_quick_actions_link([
        'name'       => _l('hr_check_in'),
        'url'        => 'hr/attendance/clock_in',
        'permission' => 'hr',
        'position'   => 1,
        'icon'       => 'fa fa-clock',
    ]);
}

function get_hr_leave_balance($staff_id, $leave_type_id, $year = null)
{
    $CI = &get_instance();
    if ($year === null) {
        $year = date('Y');
    }

    $CI->db->select_sum('days');
    $CI->db->where('staff_id', $staff_id);
    $CI->db->where('leave_type_id', $leave_type_id);
    $CI->db->where('YEAR(start_date)', $year);
    $CI->db->where('status', 'approved');
    $CI->db->where('deleted', 0);
    $approved = $CI->db->get(db_prefix() . 'hr_leave_requests')->row()->days;

    $CI->db->select_sum('allocated_days');
    $CI->db->where('staff_id', $staff_id);
    $CI->db->where('leave_type_id', $leave_type_id);
    $CI->db->where('year', $year);
    $allocated = $CI->db->get(db_prefix() . 'hr_leave_allocations')->row()->allocated_days;

    return $allocated - $approved;
}

function get_hr_staff_department($staff_id)
{
    $CI = &get_instance();
    $CI->db->select('department_id');
    $CI->db->where('staffid', $staff_id);
    $staff = $CI->db->get(db_prefix() . 'staff')->row();
    return $staff ? $staff->department_id : 0;
}

function get_hr_department($id)
{
    $CI = &get_instance();
    return $CI->db->where('id', $id)->get(db_prefix() . 'hr_departments')->row_array();
}

function get_hr_designation($id)
{
    $CI = &get_instance();
    return $CI->db->where('id', $id)->get(db_prefix() . 'hr_designations')->row_array();
}

function get_hr_staff_manager($staff_id)
{
    $CI = &get_instance();
    $CI->db->select('manager_id');
    $CI->db->where('staffid', $staff_id);
    $staff = $CI->db->get(db_prefix() . 'staff')->row();
    return $staff ? $staff->manager_id : 0;
}

function hr_notify_manager($staff_id, $type, $data = [])
{
    $CI = &get_instance();
    
    $manager_id = get_hr_staff_manager($staff_id);
    if (!$manager_id) {
        $department_id = get_hr_staff_department($staff_id);
        $CI->db->select('manager_id');
        $CI->db->where('id', $department_id);
        $dept = $CI->db->get(db_prefix() . 'hr_departments')->row();
        if ($dept) {
            $manager_id = $dept->manager_id;
        }
    }

    if ($manager_id) {
        $CI->load->model('hr/hr_model');
        $staff = $CI->staff_model->get($staff_id);
        $manager = $CI->staff_model->get($manager_id);

        if ($type == 'leave_request') {
            $message = sprintf(
                _l('hr_notification_leave_request'),
                $staff->firstname . ' ' . $staff->lastname,
                $data['start_date'] . ' to ' . $data['end_date']
            );
            $CI->hr_model->add_notification($manager_id, $message, 'hr/leave', 'leave_request');
        }
    }
}
