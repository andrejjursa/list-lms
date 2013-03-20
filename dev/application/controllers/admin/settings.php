<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->load->library('configurator');
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('settings');
        $config = $this->configurator->get_config_array('config');
        $languages = $this->lang->get_list_of_languages();
        $this->parser->parse('backend/settings/index.tpl', array('config' => $config, 'languages' => $languages));
    }
    
    public function save() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('config[language]', 'lang:admin_settings_form_field_language', 'required');
        if (is_mod_rewrite_enabled()) {
            $this->form_validation->set_rules('config[rewrite_engine_enabled]', 'lang:admin_settings_form_field_rewrite_engine_enabled', 'required');
        }
        $this->form_validation->set_rules('config[url_suffix]', 'lang:admin_settings_form_field_url_suffix', 'callback__url_suffix');
        $this->form_validation->set_message('_url_suffix', $this->lang->line('admin_settings_form_error_message_url_suffix'));
        
        if ($this->form_validation->run()) {
            $config = $this->input->post('config');
            if (is_mod_rewrite_enabled()) {
                $config['rewrite_engine_enabled'] = $this->bool_val($config['rewrite_engine_enabled']);
            } else {
                $config['rewrite_engine_enabled'] = FALSE;
            }
            $this->configurator->set_config_array('config', $config);
            redirect(create_internal_url('admin_settings/index'));
        } else {
            $this->index();
        }
    }
    
    public function _url_suffix($str) {
        return (bool)preg_match('/^(\.[a-z]+[a-z0-9]*){0,1}$/i', $str);
    }
    
    private function bool_val($value) {
        if (is_numeric($value)) { return (bool)$value; }
        if (is_string($value)) { return strtolower($value) == 'true'; }
        return false;
    }
}