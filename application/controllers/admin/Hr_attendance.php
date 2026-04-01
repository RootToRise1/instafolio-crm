<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hr_attendance extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('hr/hr_model');
    }

    public function index()
    {
        redirect(admin_url('hr/my_attendance'));
    }

    public function clock_in()
    {
        $staff_id = get_staff_user_id();
        
        if (!$staff_id) {
            set_alert('danger', 'Unable to identify user. Please login again.');
            redirect(admin_url('authentication'));
            return;
        }
        
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
        
        if (!$staff_id) {
            set_alert('danger', 'Unable to identify user. Please login again.');
            redirect(admin_url('authentication'));
            return;
        }
        
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
        
        if (!$staff_id) {
            set_alert('danger', 'Unable to identify user. Please login again.');
            redirect(admin_url('authentication'));
            return;
        }
        
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
        
        if (!$staff_id) {
            set_alert('danger', 'Unable to identify user. Please login again.');
            redirect(admin_url('authentication'));
            return;
        }
        
        $result = $this->hr_model->break_out($staff_id);
        
        if ($result['success']) {
            set_alert('success', _l('hr_break_out_success'));
        } else {
            set_alert('danger', $result['message']);
        }
        
        redirect(admin_url('hr/my_attendance'));
    }
}