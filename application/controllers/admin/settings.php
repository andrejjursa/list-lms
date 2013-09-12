<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Settings controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Settings extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->load->library('configurator');
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('settings');
        $config = $this->configurator->get_config_array('config');
        $languages = $this->lang->get_list_of_languages();
        $this->parser->add_css_file('admin_settings.css');
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
        $this->form_validation->set_rules('config[teacher_login_security_timeout]', 'lang:admin_settings_form_field_teacher_login_security_timeout', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('config[student_login_security_timeout]', 'lang:admin_settings_form_field_student_login_security_timeout', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('config[teacher_login_security_allowed_attempts]', 'lang:admin_settings_form_field_teacher_login_security_allowed_attempts', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('config[student_login_security_allowed_attempts]', 'lang:admin_settings_form_field_student_login_security_allowed_attempts', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('config[maximum_solition_filesize]', 'lang:admin_settings_form_field_maximum_solition_filesize', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('config[readable_file_extensions]', 'lang:admin_settings_form_field_readable_file_extensions', 'required|min_length[0]|regex_match[/^[a-z]+[0-9]*(\,[a-z]+[0-9]*)*$/]');
        $this->form_validation->set_rules('config[email][protocol]', 'lang:admin_settings_form_field_email_protocol', 'required');
        $this->form_validation->set_rules('config[email][priority]', 'lang:admin_settings_form_field_email_priority', 'required|integer|greater_than[0]|lower_than[6]');
        $this->form_validation->set_rules('config[email][smtp_port]', 'lang:admin_settings_form_field_email_smtp_port', 'integer|greater_than[0]');
        $this->form_validation->set_rules('config[email][smtp_timeout]', 'lang:admin_settings_form_field_email_smtp_timeout', 'integer|greater_than[0]');
        
        if ($this->form_validation->run()) {
            $config = $this->protect_config_array($this->input->post('config'));
            if (is_mod_rewrite_enabled()) {
                $config['rewrite_engine_enabled'] = $this->bool_val($config['rewrite_engine_enabled']);
            } else {
                $config['rewrite_engine_enabled'] = FALSE;
            }
            $config['teacher_login_security_timeout'] = intval($config['teacher_login_security_timeout']);
            $config['student_login_security_timeout'] = intval($config['student_login_security_timeout']);
            $config['teacher_login_security_allowed_attempts'] = intval($config['teacher_login_security_allowed_attempts']);
            $config['student_login_security_allowed_attempts'] = intval($config['student_login_security_allowed_attempts']);
            $config['maximum_solition_filesize'] = intval($config['maximum_solition_filesize']);
            $config['student_registration']['enabled'] = $this->bool_val($config['student_registration']['enabled']);
            $config['email']['smtp_port'] = intval($config['email']['smtp_port']);
            $config['email']['smtp_timeout'] = intval($config['email']['smtp_timeout']);
            $config['email']['priority'] = intval($config['email']['priority']);
            $config['email_multirecipient_batch_mode'] = $this->bool_val($config['email_multirecipient_batch_mode']);
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
        return FALSE;
    }
    
    private function protect_config_array($config) {
        $allowed_keys = array(
            'language',
            'rewrite_engine_enabled',
            'url_suffix',
            'teacher_login_security_timeout',
            'student_login_security_timeout',
            'teacher_login_security_allowed_attempts',
            'student_login_security_allowed_attempts',
            'maximum_solition_filesize',
            'readable_file_extensions',
            'student_registration',
            'email',
            'email_multirecipient_batch_mode',
        );
        
        $output = array();
        if (count($config) > 0) { foreach ($config as $key => $value) {
            if (in_array($key, $allowed_keys)) {
                $output[$key] = $value;
            }
        }}
        
        return $output;
    }
}