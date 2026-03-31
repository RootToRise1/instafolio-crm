<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        hooks()->do_action('clients_authentication_constructor', $this);
    }

    public function index()
    {
        $this->unified();
    }

    public function login()
    {
        // If already logged in, redirect to appropriate dashboard
        if (is_staff_logged_in()) {
            redirect(admin_url());
        }
        if (is_client_logged_in()) {
            redirect(site_url());
        }
        
        // If not a POST request, show login page
        if ($this->input->method() !== 'post') {
            $data['title'] = 'Login - Instafolio CRM';
            $data['bodyclass'] = 'login-page';
            $this->load->view('authentication/login_unified', $data);
            return;
        }
        
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() !== false) {
            $email = $this->input->post('email');
            $password = $this->input->post('password', false);
            $remember = $this->input->post('remember');
            
            // First try staff login
            $this->load->model('Authentication_model');
            $staff_data = $this->Authentication_model->login($email, $password, $remember, true);
            
            if (is_array($staff_data) && isset($staff_data['memberinactive'])) {
                $this->session->set_flashdata('message', 'Account is inactive');
                $this->session->set_flashdata('alert_type', 'danger');
                $this->session->set_flashdata('email', $email);
                redirect('authentication/login');
            } elseif (is_array($staff_data) && isset($staff_data['two_factor_auth'])) {
                $this->session->set_userdata('_two_factor_auth_established', true);
                $this->session->set_userdata('_two_factor_auth_staff_email', $email);
                redirect(admin_url('authentication/two_factor'));
            } elseif ($staff_data === true) {
                // Staff login success
                $this->load->model('announcements_model');
                $this->announcements_model->set_announcements_as_read_except_last_one(get_staff_user_id(), true);
                hooks()->do_action('after_staff_login');
                redirect(admin_url());
                exit;
            }
            
            // If staff login fails, try client login
            $client_data = $this->Authentication_model->login($email, $password, $remember, false);
            
            if (is_array($client_data) && isset($client_data['memberinactive'])) {
                $this->session->set_flashdata('message', 'Account is inactive');
                $this->session->set_flashdata('alert_type', 'danger');
                $this->session->set_flashdata('email', $email);
                redirect('authentication/login');
            } elseif ($client_data === true) {
                // Client login success
                if ($this->input->post('language') && $this->input->post('language') != '') {
                    set_contact_language($this->input->post('language'));
                }
                
                $this->load->model('announcements_model');
                $this->announcements_model->set_announcements_as_read_except_last_one(get_contact_user_id());
                hooks()->do_action('after_contact_login');
                
                maybe_redirect_to_previous_url();
                redirect(site_url('clients'));
                exit;
            }
            
            // Both failed
            $this->session->set_flashdata('message', 'Invalid email or password');
            $this->session->set_flashdata('alert_type', 'danger');
            $this->session->set_flashdata('email', $email);
            redirect('authentication/login');
        }
        
        $this->session->set_flashdata('message', validation_errors());
        $this->session->set_flashdata('alert_type', 'danger');
        redirect('authentication/login');
    }

    // Backward compatibility - redirect old unified method
    public function unified()
    {
        redirect('authentication/login');
    }

    // Backward compatibility - redirect old unified_login method
    public function unified_login()
    {
        redirect('authentication/login');
    }

    public function register()
    {
        if (get_option('allow_registration') != 1 || is_client_logged_in()) {
            redirect(site_url());
        }

        $requiredFields = get_required_fields_for_registration();
       
        $honeypot = get_option('enable_honeypot_spam_validation') == 1;

        $fields = [
            'firstname' => $honeypot ? 'firstnamemjxw' : 'firstname',
            'lastname'  => $honeypot ? 'lastnamemjxw' : 'lastname',
            'email'     => $honeypot ? 'emailmjxw' : 'email',
            'company'   => $honeypot ? 'companymjxw' : 'company',
        ];

        if (get_option('company_is_required') == 1) {
            $this->form_validation->set_rules($fields['company'], _l('client_company'), 'required');
        }

        $emailRules = 'trim|is_unique[' . db_prefix() . 'contacts.email]|valid_email';

        foreach(['contact', 'company'] as $fieldsKey) {
            foreach($requiredFields[$fieldsKey] as $key => $field) {
                $formKey = strafter($key, '_');

                if(isset($fields[$formKey])) {
                    $formKey = $fields[$formKey];
                }
                
                if($key !== 'contact_email'){
                    if($field['is_required']) {
                        $this->form_validation->set_rules($formKey, $field['label'], 'required');
                    }
                } else {
                    if($field['is_required']) {
                        $emailRules .= '|required';
                    }

                    $this->form_validation->set_rules($formKey, $field['label'], $emailRules);
                }
            }
        }

        if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions') == 1) {
            $this->form_validation->set_rules(
                'accept_terms_and_conditions',
                _l('terms_and_conditions'),
                'required',
                ['required' => _l('terms_and_conditions_validation')]
            );
        }
       
        $this->form_validation->set_rules('password', _l('clients_register_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('clients_register_password_repeat'), 'required|matches[password]');

        if (show_recaptcha_in_customers_area()) {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }

        $custom_fields = get_custom_fields('customers', [
            'show_on_client_portal' => 1,
            'required'              => 1,
        ]);

        $custom_fields_contacts = get_custom_fields('contacts', [
            'show_on_client_portal' => 1,
            'required'              => 1,
        ]);

        foreach ($custom_fields as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }

        foreach ($custom_fields_contacts as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }

        if ($this->input->post()) {
            if ($honeypot &&
                count(array_filter($this->input->post(['email', 'firstname', 'lastname', 'company']))) > 0) {
                show_404();
            }

            if ($this->form_validation->run() !== false) {
                $data      = $this->input->post();
                $countryId = is_numeric($data['country']) ? $data['country'] : 0;

                if (is_automatic_calling_codes_enabled()) {
                    $customerCountry = get_country($countryId);

                    if ($customerCountry) {
                        $callingCode = '+' . ltrim($customerCountry->calling_code, '+');

                        if (startsWith($data['contact_phonenumber'], $customerCountry->calling_code)) { // with calling code but without the + prefix
                            $data['contact_phonenumber'] = '+' . $data['contact_phonenumber'];
                        } elseif (!startsWith($data['contact_phonenumber'], $callingCode)) {
                            $data['contact_phonenumber'] = $callingCode . $data['contact_phonenumber'];
                        }
                    }
                }

                define('CONTACT_REGISTERING', true);

                $clientid = $this->clients_model->add([
                      'billing_street'      => $data['address'],
                      'billing_city'        => $data['city'],
                      'billing_state'       => $data['state'],
                      'billing_zip'         => $data['zip'],
                      'billing_country'     => $countryId,
                      'firstname'           => $data[$fields['firstname']],
                      'lastname'            => $data[$fields['lastname']],
                      'email'               => $data[$fields['email']],
                      'contact_phonenumber' => $data['contact_phonenumber'] ,
                      'website'             => $data['website'],
                      'title'               => $data['title'],
                      'password'            => $data['passwordr'],
                      'company'             => $data[$fields['company']],
                      'vat'                 => isset($data['vat']) ? $data['vat'] : '',
                      'phonenumber'         => $data['phonenumber'],
                      'country'             => $data['country'],
                      'city'                => $data['city'],
                      'address'             => $data['address'],
                      'zip'                 => $data['zip'],
                      'state'               => $data['state'],
                      'custom_fields'       => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
                      'default_language'    => (get_contact_language() != '') ? get_contact_language() : get_option('active_language'),
                ], true);

                if ($clientid) {
                    hooks()->do_action('after_client_register', $clientid);

                    if (get_option('customers_register_require_confirmation') == '1') {
                        send_customer_registered_email_to_administrators($clientid);

                        $this->clients_model->require_confirmation($clientid);
                        set_alert('success', _l('customer_register_account_confirmation_approval_notice'));
                        redirect(site_url('authentication/login'));
                    }

                    $this->load->model('authentication_model');

                    $logged_in = $this->authentication_model->login(
                        $data[$fields['email']],
                        $this->input->post('password', false),
                        false,
                        false
                    );

                    $redUrl = site_url();

                    if ($logged_in) {
                        hooks()->do_action('after_client_register_logged_in', $clientid);
                        set_alert('success', _l('clients_successfully_registered'));
                    } else {
                        set_alert('warning', _l('clients_account_created_but_not_logged_in'));
                        $redUrl = site_url('authentication/login');
                    }

                    send_customer_registered_email_to_administrators($clientid);
                    redirect($redUrl);
                }
            }
        }

        $data['requiredFields'] = $requiredFields;
        $data['title']     = _l('clients_register_heading');
        $data['bodyclass'] = 'register';
        $data['honeypot']  = $honeypot;
        $data['fields']    = $fields;
        
        // Use unified registration page
        $this->load->view('authentication/register_unified', $data);
    }

    public function forgot_password()
    {
        if (is_client_logged_in()) {
            redirect(site_url());
        }

        $this->form_validation->set_rules(
            'email',
            _l('customer_forgot_password_email'),
            'trim|required|valid_email|callback_contact_email_exists'
        );

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $this->load->model('Authentication_model');
                $success = $this->Authentication_model->forgot_password($this->input->post('email'));
                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', _l('inactive_account'));
                } elseif ($success == true) {
                    set_alert('success', _l('check_email_for_resetting_password'));
                } else {
                    set_alert('danger', _l('error_setting_new_password_key'));
                }
                redirect(site_url('authentication/forgot_password'));
            }
        }
        
        // Use unified forgot password page
        $data['title'] = _l('customer_forgot_password');
        $this->load->view('authentication/forgot_password_unified', $data);
    }

    public function reset_password($staff, $userid, $new_pass_key)
    {
        $this->load->model('Authentication_model');
        if (!$this->Authentication_model->can_reset_password($staff, $userid, $new_pass_key)) {
            set_alert('danger', _l('password_reset_key_expired'));
            redirect(site_url('authentication/login'));
        }

        $this->form_validation->set_rules('password', _l('customer_reset_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('customer_reset_password_repeat'), 'required|matches[password]');
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                hooks()->do_action('before_user_reset_password', [
                    'staff'  => $staff,
                    'userid' => $userid,
                ]);
                $success = $this->Authentication_model->reset_password(
                    0,
                    $userid,
                    $new_pass_key,
                    $this->input->post('passwordr', false)
                );
                if (is_array($success) && $success['expired'] == true) {
                    set_alert('danger', _l('password_reset_key_expired'));
                } elseif ($success == true) {
                    hooks()->do_action('after_user_reset_password', [
                        'staff'  => $staff,
                        'userid' => $userid,
                    ]);
                    set_alert('success', _l('password_reset_message'));
                } else {
                    set_alert('danger', _l('password_reset_message_fail'));
                }
                redirect(site_url('authentication/login'));
            }
        }
        $data['title'] = _l('admin_auth_reset_password_heading');
        $this->data($data);
        $this->view('reset_password');
        $this->layout();
    }

    public function logout()
    {
        $this->load->model('authentication_model');
        $this->authentication_model->logout(false);
        hooks()->do_action('after_client_logout');
        redirect(site_url('authentication/login'));
    }

    public function contact_email_exists($email = '')
    {
        $this->db->where('email', $email);
        $total_rows = $this->db->count_all_results(db_prefix() . 'contacts');

        if ($total_rows == 0) {
            $this->form_validation->set_message('contact_email_exists', _l('auth_reset_pass_email_not_found'));

            return false;
        }

        return true;
    }

    public function recaptcha($str = '')
    {
        return do_recaptcha_validation($str);
    }

    public function change_language($lang = '')
    {
        if (is_language_disabled()) {
            redirect(site_url());
        }

        set_contact_language($lang);

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }
}
