<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['hr'] = 'hr/index';
$route['hr/table'] = 'hr/table';
$route['hr/employees'] = 'hr/employees';
$route['hr/employee/(:any)'] = 'hr/employee/$1';
$route['hr/employee'] = 'hr/employee';
$route['hr/change_employee_status/(:any)/(:any)'] = 'hr/change_employee_status/$1/$2';
$route['hr/mark_as_active/(:any)'] = 'hr/mark_as_active/$1';
$route['hr/remove_staff_profile_image/(:any)'] = 'hr/remove_staff_profile_image/$1';
$route['hr/delete/(:any)'] = 'hr/delete/$1';
$route['hr/bulk_action'] = 'hr/bulk_action';
$route['hr/index'] = 'hr/index';

// Bank Details
$route['hr/add_bank_details/(:num)'] = 'hr/add_bank_details/$1';
$route['hr/delete_bank_details/(:num)'] = 'hr/delete_bank_details/$1';

// Emergency Contacts
$route['hr/add_emergency_contact/(:num)'] = 'hr/add_emergency_contact/$1';
$route['hr/delete_emergency_contact/(:num)'] = 'hr/delete_emergency_contact/$1';

// Documents
$route['hr/add_document/(:num)'] = 'hr/add_document/$1';
$route['hr/delete_document/(:num)'] = 'hr/delete_document/$1';

// Salary & Leave
$route['hr/update_salary/(:num)'] = 'hr/update_salary/$1';
$route['hr/auto_allocate_leave/(:num)'] = 'hr/auto_allocate_leave/$1';

// Attendance
$route['hr/clock_in'] = 'hr/clock_in';
$route['hr/clock_out'] = 'hr/clock_out';
$route['hr/break_in'] = 'hr/break_in';
$route['hr/break_out'] = 'hr/break_out';
$route['hr/my_attendance'] = 'hr/my_attendance';
$route['hr/attendance'] = 'hr/attendance';
$route['hr/attendance_report'] = 'hr/attendance_report';

// Leave
$route['hr/leave'] = 'hr/leave';
$route['hr/my_leave'] = 'hr/my_leave';
$route['hr/approve_leave/(:num)'] = 'hr/approve_leave/$1';
$route['hr/reject_leave/(:num)'] = 'hr/reject_leave/$1';
$route['hr/leave_types'] = 'hr/leave_types';
$route['hr/delete_leave_type/(:num)'] = 'hr/delete_leave_type/$1';

// Setup
$route['hr/setup/(:any)'] = 'hr/setup/$1';
$route['hr/setup/(:any)/(:num)'] = 'hr/setup/$1/$2';
$route['hr/delete_setup/(:any)/(:num)'] = 'hr/delete_setup/$1/$2';

// Payroll
$route['hr/payroll'] = 'hr/payroll';
$route['hr/salary/(:num)'] = 'hr/salary/$1';
$route['hr/salary'] = 'hr/salary';
$route['hr/payslips/(:num)'] = 'hr/payslips/$1';
$route['hr/payslips'] = 'hr/payslips';
$route['hr/payslip/(:num)'] = 'hr/payslip/$1';
$route['hr/generate_payslip'] = 'hr/generate_payslip';
$route['hr/update_payslip_status/(:num)/(:any)'] = 'hr/update_payslip_status/$1/$2';
$route['hr/allowances/(:num)'] = 'hr/allowances/$1';
$route['hr/allowances'] = 'hr/allowances';
$route['hr/delete_allowance/(:num)'] = 'hr/delete_allowance/$1';
$route['hr/my_salary'] = 'hr/my_salary';
$route['hr/pro_rata_calculator'] = 'hr/pro_rata_calculator';

// Roles
$route['hr/roles/(:num)'] = 'hr/roles/$1';
$route['hr/roles'] = 'hr/roles';

// Performance
$route['hr/performance'] = 'hr/performance';
$route['hr/add_review'] = 'hr/add_review';
$route['hr/acknowledge_review/(:num)'] = 'hr/acknowledge_review/$1';

// Reports
$route['hr/reports'] = 'hr/reports';
