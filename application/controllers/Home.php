<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (is_staff_logged_in()) {
            redirect(admin_url());
            return;
        }
        
        if (is_client_logged_in()) {
            redirect(site_url('clients'));
            return;
        }
        
        redirect(site_url('authentication/login'));
    }
}