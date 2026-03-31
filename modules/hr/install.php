<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// HR Departments
if (!$CI->db->table_exists(db_prefix() . 'hr_departments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_departments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `manager_id` int(11) DEFAULT NULL,
        `description` text,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Designations
if (!$CI->db->table_exists(db_prefix() . 'hr_designations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_designations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `department_id` int(11) DEFAULT NULL,
        `description` text,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Shifts
if (!$CI->db->table_exists(db_prefix() . 'hr_shifts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_shifts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `start_time` time NOT NULL,
        `end_time` time NOT NULL,
        `grace_period_minutes` int(11) DEFAULT 15,
        `working_hours` decimal(4,2) DEFAULT 8.00,
        `is_night_shift` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    
    // Insert default shift
    $CI->db->query('INSERT INTO `' . db_prefix() . 'hr_shifts` (`name`, `start_time`, `end_time`, `grace_period_minutes`, `working_hours`) VALUES ("General", "09:00:00", "18:00:00", 15, 8.00)');
}

// HR Leave Types
if (!$CI->db->table_exists(db_prefix() . 'hr_leave_types')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_leave_types` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `color` varchar(7) DEFAULT '#3788d8',
        `days_allowed` int(11) DEFAULT 0,
        `is_paid` tinyint(1) DEFAULT 1,
        `requires_approval` tinyint(1) DEFAULT 1,
        `allow_half_day` tinyint(1) DEFAULT 1,
        `max_consecutive_days` int(11) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    
    // Insert default leave types
    $CI->db->query('INSERT INTO `' . db_prefix() . 'hr_leave_types` (`name`, `color`, `days_allowed`, `is_paid`, `requires_approval`) VALUES 
        ("Annual Leave", "#3788d8", 20, 1, 1),
        ("Sick Leave", "#e74c3c", 10, 1, 1),
        ("Personal Leave", "#f39c12", 5, 0, 1),
        ("Maternity Leave", "#9b59b6", 90, 1, 0),
        ("Paternity Leave", "#1abc9c", 10, 1, 0)
    ');
}

// HR Leave Allocations
if (!$CI->db->table_exists(db_prefix() . 'hr_leave_allocations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_leave_allocations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `leave_type_id` int(11) NOT NULL,
        `year` year NOT NULL,
        `allocated_days` int(11) DEFAULT 0,
        `carry_forward_days` int(11) DEFAULT 0,
        `used_days` int(11) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `leave_type_id` (`leave_type_id`),
        KEY `year` (`year`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Payroll - Salary
if (!$CI->db->table_exists(db_prefix() . 'hr_payroll_salary')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_payroll_salary` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `base_salary` decimal(15,2) DEFAULT 0,
        `currency` varchar(3) DEFAULT 'USD',
        `pay_frequency` enum('monthly','bi-weekly','weekly') DEFAULT 'monthly',
        `bank_name` varchar(191) DEFAULT NULL,
        `bank_account` varchar(100) DEFAULT NULL,
        `tax_id` varchar(100) DEFAULT NULL,
        `social_security` decimal(15,2) DEFAULT 0,
        `health_insurance` decimal(15,2) DEFAULT 0,
        `other_deductions` decimal(15,2) DEFAULT 0,
        `effective_from` date DEFAULT NULL,
        `effective_to` date DEFAULT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Payroll - Payslips
if (!$CI->db->table_exists(db_prefix() . 'hr_payslips')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_payslips` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `salary_id` int(11) DEFAULT NULL,
        `pay_period_start` date NOT NULL,
        `pay_period_end` date NOT NULL,
        `basic_salary` decimal(15,2) DEFAULT 0,
        `allowances` decimal(15,2) DEFAULT 0,
        `deductions` decimal(15,2) DEFAULT 0,
        `tax` decimal(15,2) DEFAULT 0,
        `net_salary` decimal(15,2) DEFAULT 0,
        `days_worked` decimal(5,2) DEFAULT 0,
        `days_paid` decimal(5,2) DEFAULT 0,
        `pro_rata_amount` decimal(15,2) DEFAULT 0,
        `overtime_hours` decimal(8,2) DEFAULT 0,
        `overtime_amount` decimal(15,2) DEFAULT 0,
        `bonus` decimal(15,2) DEFAULT 0,
        `status` enum('draft','calculated','approved','paid') DEFAULT 'draft',
        `payment_date` date DEFAULT NULL,
        `notes` text,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `pay_period` (`pay_period_start`,`pay_period_end`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Payroll - Allowances
if (!$CI->db->table_exists(db_prefix() . 'hr_payroll_allowances')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_payroll_allowances` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `type` enum('fixed','percentage') DEFAULT 'fixed',
        `amount` decimal(15,2) DEFAULT 0,
        `is_taxable` tinyint(1) DEFAULT 1,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Staff Allowances (link allowances to staff)
if (!$CI->db->table_exists(db_prefix() . 'hr_staff_allowances')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_staff_allowances` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `allowance_id` int(11) NOT NULL,
        `amount` decimal(15,2) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        `effective_from` date DEFAULT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `allowance_id` (`allowance_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Holidays
if (!$CI->db->table_exists(db_prefix() . 'hr_holidays')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_holidays` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `date` date NOT NULL,
        `year` year DEFAULT NULL,
        `is_recurring` tinyint(1) DEFAULT 0,
        `description` text,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Attendance
if (!$CI->db->table_exists(db_prefix() . 'hr_attendance')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_attendance` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `date` date NOT NULL,
        `clock_in` datetime DEFAULT NULL,
        `clock_out` datetime DEFAULT NULL,
        `clock_in_note` text,
        `clock_out_note` text,
        `total_hours` decimal(5,2) DEFAULT 0.00,
        `break_minutes` int(11) DEFAULT 0,
        `overtime_hours` decimal(5,2) DEFAULT 0.00,
        `status` enum('present','absent','late','half_day','holiday','weekoff') DEFAULT 'present',
        `late_minutes` int(11) DEFAULT 0,
        `shift_id` int(11) DEFAULT NULL,
        `is_manual` tinyint(1) DEFAULT 0,
        `approved_by` int(11) DEFAULT NULL,
        `approved_at` datetime DEFAULT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_daily_attendance` (`staff_id`, `date`),
        KEY `staff_id` (`staff_id`),
        KEY `date` (`date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
} else {
    // Add missing columns if they don't exist
    if (!$CI->db->field_exists('clock_in', db_prefix() . 'hr_attendance')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'hr_attendance` ADD COLUMN `clock_in` datetime DEFAULT NULL AFTER `date`');
    }
    if (!$CI->db->field_exists('clock_out', db_prefix() . 'hr_attendance')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'hr_attendance` ADD COLUMN `clock_out` datetime DEFAULT NULL AFTER `clock_in`');
    }
}

// HR Attendance Breaks
if (!$CI->db->table_exists(db_prefix() . 'hr_attendance_breaks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_attendance_breaks` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `attendance_id` int(11) NOT NULL,
        `break_start` datetime NOT NULL,
        `break_end` datetime DEFAULT NULL,
        `break_minutes` int(11) DEFAULT 0,
        `note` text,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `attendance_id` (`attendance_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Leave Requests
if (!$CI->db->table_exists(db_prefix() . 'hr_leave_requests')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_leave_requests` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `leave_type_id` int(11) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `days` decimal(5,2) NOT NULL,
        `half_day` tinyint(1) DEFAULT 0,
        `half_day_type` enum('first','second') DEFAULT NULL,
        `reason` text,
        `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
        `approved_by` int(11) DEFAULT NULL,
        `approved_at` datetime DEFAULT NULL,
        `rejected_by` int(11) DEFAULT NULL,
        `rejected_at` datetime DEFAULT NULL,
        `rejection_reason` text,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `leave_type_id` (`leave_type_id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Leave Allocations
if (!$CI->db->table_exists(db_prefix() . 'hr_leave_allocations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_leave_allocations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `leave_type_id` int(11) NOT NULL,
        `year` year NOT NULL,
        `allocated_days` decimal(5,2) NOT NULL,
        `carry_forward_days` decimal(5,2) DEFAULT 0.00,
        `used_days` decimal(5,2) DEFAULT 0.00,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_allocation` (`staff_id`, `leave_type_id`, `year`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Performance Reviews
if (!$CI->db->table_exists(db_prefix() . 'hr_performance_reviews')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_performance_reviews` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `reviewer_id` int(11) NOT NULL,
        `review_period` varchar(100) DEFAULT NULL,
        `review_date` date NOT NULL,
        `rating` decimal(3,2) DEFAULT NULL,
        `objectives` text,
        `achievements` text,
        `areas_for_improvement` text,
        `comments` text,
        `employee_feedback` text,
        `acknowledged` tinyint(1) DEFAULT 0,
        `acknowledged_at` datetime DEFAULT NULL,
        `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `reviewer_id` (`reviewer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Documents
if (!$CI->db->table_exists(db_prefix() . 'hr_documents')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_documents` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `document_type` varchar(100) DEFAULT NULL,
        `title` varchar(191) NOT NULL,
        `file_name` varchar(191) NOT NULL,
        `file_path` varchar(500) NOT NULL,
        `file_size` int(11) DEFAULT NULL,
        `mime_type` varchar(100) DEFAULT NULL,
        `description` text,
        `created_by` int(11) DEFAULT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Notifications
if (!$CI->db->table_exists(db_prefix() . 'hr_notifications')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_notifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `type` varchar(50) DEFAULT NULL,
        `message` text NOT NULL,
        `link` varchar(255) DEFAULT NULL,
        `is_read` tinyint(1) DEFAULT 0,
        `read_at` datetime DEFAULT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `is_read` (`is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// HR Roles
if (!$CI->db->table_exists(db_prefix() . 'hr_roles')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_roles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(191) NOT NULL,
        `description` text,
        `permissions` text,
        `active` tinyint(1) DEFAULT 1,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    
    // Insert default HR role
    $CI->db->query('INSERT INTO `' . db_prefix() . 'hr_roles` (`name`, `description`, `permissions`, `active`) VALUES 
        ("HR Manager", "Full HR management access", \'{"view":1,"create":1,"edit":1,"delete":1,"approve_leave":1,"view_reports":1}\', 1),
        ("HR Staff", "Standard HR staff access", \'{"view":1,"create":1,"edit":1,"view_reports":1}\', 1),
        ("Department Manager", "Manager level HR access", \'{"view_own":1,"view":1,"approve_leave":1}\', 1)
    ');
}

// Add HR related columns to staff table
$columns_to_add = [
    'department_id' => "ADD COLUMN `department_id` int(11) DEFAULT NULL AFTER `admin`",
    'designation_id' => "ADD COLUMN `designation_id` int(11) DEFAULT NULL",
    'manager_id' => "ADD COLUMN `manager_id` int(11) DEFAULT NULL",
    'date_of_joining' => "ADD COLUMN `date_of_joining` date DEFAULT NULL",
    'employment_type' => "ADD COLUMN `employment_type` enum('full_time','part_time','contract','intern') DEFAULT 'full_time'",
    'salary' => "ADD COLUMN `salary` decimal(15,2) DEFAULT NULL",
    'hr_shift_id' => "ADD COLUMN `hr_shift_id` int(11) DEFAULT NULL",
    'hr_role_id' => "ADD COLUMN `hr_role_id` int(11) DEFAULT NULL",
    'is_active_hr' => "ADD COLUMN `is_active_hr` tinyint(1) DEFAULT 1",
];

foreach ($columns_to_add as $column => $sql) {
    if (!$CI->db->field_exists(str_replace('ADD COLUMN ', '', $sql), db_prefix() . 'staff')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ' . $sql);
    }
}

// Add indexes
$CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD INDEX `department_id` (`department_id`)');
$CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD INDEX `manager_id` (`manager_id`)');

// Create HR folders for document storage
$hr_upload_path = FCPATH . 'uploads/hr/';
if (!is_dir($hr_upload_path)) {
    mkdir($hr_upload_path, 0777, true);
    mkdir($hr_upload_path . 'documents/', 0777, true);
}
