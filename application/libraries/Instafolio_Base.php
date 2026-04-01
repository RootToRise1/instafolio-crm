<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Instafolio_Base
{
    private $options = array();
    private $quick_actions = array();
    private $_instance;
    private $show_setup_menu = true;
    private $available_reminders = array('customer', 'lead', 'estimate', 'invoice', 'proposal', 'expense', 'credit_note');
    private $tables_with_currency = array();
    private $media_folder;
    private $available_languages = array();

    public function __construct()
    {
        $this->_instance =& get_instance();

        if ($this->_instance->db->field_exists('autoload', 'tbloptions')) {
            $options = $this->_instance->db->select('name, value')
            ->where('autoload', 1)
            ->get('tbloptions')->result_array();
        } else {
            $options = $this->_instance->db->select('name, value')
            ->get('tbloptions')->result_array();
        }

        foreach ($options as $option) {
            $this->options[$option['name']] = $option['value'];
        }

        $this->tables_with_currency = do_action('tables_with_currency', array(
            array(
                'table' => 'tblinvoices',
                'field' => 'currency',
            ),
            array(
                'table' => 'tblexpenses',
                'field' => 'currency',
            ),
            array(
                'table' => 'tblproposals',
                'field' => 'currency',
            ),
            array(
                'table' => 'tblestimates',
                'field' => 'currency',
            ),
            array(
                'table' => 'tblclients',
                'field' => 'default_currency',
            ),
            array(
                'table' => 'tblcreditnotes',
                'field' => 'currency',
            ),
        ));

        $this->media_folder = do_action('before_set_media_folder', 'media');

        foreach (list_folders(APPPATH . 'language') as $language) {
            if (is_dir(APPPATH.'language/'.$language)) {
                array_push($this->available_languages, $language);
            }
        }

        do_action('app_base_after_construct_action');
    }

    public function get_available_languages()
    {
        $languages = $this->available_languages;
        return do_action('before_get_languages', $languages);
    }

    public function get_table_data($table, $params = array())
    {
        $hook_data = do_action('before_render_table_data', array(
            'table' => $table,
            'params' => $params,
        ));

        foreach ($hook_data['params'] as $key => $val) {
            $$key = $val;
        }

        $table = $hook_data['table'];
        $customFieldsColumns = array();

        if (file_exists(VIEWPATH . 'admin/tables/my_' . $table . '.php')) {
            include_once(VIEWPATH . 'admin/tables/my_' . $table . '.php');
        } else {
            include_once(VIEWPATH . 'admin/tables/' . $table . '.php');
        }

        echo json_encode($output);
        die;
    }

    public function get_available_reminders_keys()
    {
        return $this->available_reminders;
    }

    public function get_options()
    {
        return $this->options;
    }

    public function get_option($name)
    {
        if ($name == 'number_padding_invoice_and_estimate') {
            $name = 'number_padding_prefixes';
        }

        $val = '';
        $name = trim($name);

        if (!isset($this->options[$name])) {
            $this->_instance->db->select('value');
            $this->_instance->db->where('name', $name);
            $row = $this->_instance->db->get('tbloptions')->row();
            if ($row) {
                $val = $row->value;
            }
        } else {
            $val = $this->options[$name];
        }

        $hook_data = do_action('get_option',array('name'=>$name,'value'=>$val));
        return $hook_data['value'];
    }

    public function add_quick_actions_link($item = array())
    {
        $this->quick_actions[] = $item;
    }

    public function get_quick_actions_links()
    {
        $this->quick_actions = do_action('before_build_quick_actions_links', $this->quick_actions);
        return $this->quick_actions;
    }

    public function get_contact_permissions()
    {
        $permissions = array(
            array(
                'id' => 1,
                'name' => _l('customer_permission_invoice'),
                'short_name' => 'invoices',
            ),
            array(
                'id' => 2,
                'name' => _l('customer_permission_estimate'),
                'short_name' => 'estimates',
            ),
            array(
                'id' => 3,
                'name' => _l('customer_permission_contract'),
                'short_name' => 'contracts',
            ),
            array(
                'id' => 4,
                'name' => _l('customer_permission_proposal'),
                'short_name' => 'proposals',
            ),
            array(
                'id' => 5,
                'name' => _l('customer_permission_support'),
                'short_name' => 'support',
            ),
            array(
                'id' => 6,
                'name' => _l('customer_permission_projects'),
                'short_name' => 'projects',
            ),
        );

        return do_action('get_contact_permissions', $permissions);
    }

    public function set_setup_menu_visibility($total_setup_menu_items)
    {
        if ($total_setup_menu_items == 0) {
            $this->show_setup_menu = false;
        } else {
            $this->show_setup_menu = true;
        }
    }

    public function show_setup_menu()
    {
        return do_action('show_setup_menu', $this->show_setup_menu);
    }

    public function get_tables_with_currency()
    {
        return do_action('tables_with_currencies', $this->tables_with_currency);
    }

    public function get_media_folder()
    {
        return do_action('get_media_folder', $this->media_folder);
    }
}