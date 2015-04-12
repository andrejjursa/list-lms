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
        $moss = $this->configurator->get_config_array('moss');
        $languages = $this->lang->get_list_of_languages();
        $this->inject_number_of_cache_records();
        $this->inject_number_of_compiled_templates();
        $this->parser->add_css_file('admin_settings.css');
        $this->parser->add_js_file('admin_settings/form.js');
        $this->parser->parse('backend/settings/index.tpl', array('config' => $config, 'languages' => $languages, 'moss' => $moss));
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
        $this->form_validation->set_rules('moss[moss_user_id]', 'lang:admin_settings_form_field_moss_user_id', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('config[test_aging_ticks_to_priority_increase]', 'lang:admin_settings_form_field_test_aging_ticks_to_priority_increase', 'required|integer|greater_than_equal[5]');
        $this->form_validation->set_rules('config[test_aging_max_tests_to_raise_priority]', 'lang:admin_settings_form_field_test_aging_max_tests_to_raise_priority', 'required|integer|greater_than_equal[5]');
        $this->form_validation->set_rules('config[test_maximum_enqueued_pe_student]', 'lang:admin_settings_form_field_test_maximum_enqueued_pe_student', 'required|integer|greater_than_equal[3]');
        
        if ($this->form_validation->run()) {
            $config = $this->protect_config_array($this->input->post('config'));
            $moss = $this->protect_moss_array($this->input->post('moss'));
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
            $config['student_mail_change'] = $this->bool_val($config['student_mail_change']);
            $config['email']['smtp_port'] = intval($config['email']['smtp_port']);
            $config['email']['smtp_timeout'] = intval($config['email']['smtp_timeout']);
            $config['email']['priority'] = intval($config['email']['priority']);
            $config['email_multirecipient_batch_mode'] = $this->bool_val($config['email_multirecipient_batch_mode']);
            $config['test_aging_ticks_to_priority_increase'] = intval($config['test_aging_ticks_to_priority_increase']);
            $config['test_aging_max_tests_to_raise_priority'] = intval($config['test_aging_max_tests_to_raise_priority']);
            $config['test_maximum_enqueued_pe_student'] = intval($config['test_maximum_enqueued_pe_student']);
            if (!in_array($config['test_sandbox'], array('implicit', 'docker'))) {
                $config['test_sandbox'] = 'implicit';
            }
            $this->configurator->set_config_array('config', $config);
            $this->configurator->set_config_array('moss', $moss);
            redirect(create_internal_url('admin_settings/index'));
        } else {
            $this->index();
        }
    }
    
    public function clear_all_cache() {
        $this->smarty->clearAllCache();
        $this->messages->add_message($this->lang->line('admin_settings_message_cache_cleared'));
        redirect(create_internal_url('admin_settings'));
    }
    
    public function clear_all_compiled() {
        $this->smarty->clearCompiledTemplate();
        $this->messages->add_message($this->lang->line('admin_settings_message_compiled_cleared'));
        redirect(create_internal_url('admin_settings'));
    }
    
    public function _url_suffix($str) {
        return (bool)preg_match('/^(\.[a-z]+[a-z0-9]*){0,1}$/i', $str);
    }
    
    public function changelog() {
        $this->load->helper('changelog');
        $this->_select_teacher_menu_pagetag('settings_changelog');
        $this->load->library('changelog');
        try {
            $this->changelog->read(FCPATH . 'changelog.txt');
            $this->changelog->parse();
        } catch (Exception $error) {
            $this->parser->assign('error', $error->getMessage());
        }
        $this->parser->add_css_file('admin_settings.css');
        $this->parser->parse('backend/settings/changelog.tpl', array('log' => $this->changelog->get()));
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
            'student_mail_change',
            'test_aging_ticks_to_priority_increase',
            'test_aging_max_tests_to_raise_priority',
            'test_maximum_enqueued_pe_student',
            'test_sandbox'
        );
        
        $output = array();
        if (count($config) > 0) { foreach ($config as $key => $value) {
            if (in_array($key, $allowed_keys)) {
                $output[$key] = $value;
            }
        }}
        
        return $output;
    }
    
    private function protect_moss_array($config) {
        $allowed_keys = array(
            'moss_user_id',
        );
        
        $output = array();
        if (count($config) > 0) { foreach ($config as $key => $value) {
            if (in_array($key, $allowed_keys)) {
                $output[$key] = $value;
            }
        }}
        
        return $output;
    }
    
    private function inject_number_of_cache_records() {
        $count = $this->db->select('*')->from('output_cache')->where('list_version', $this->config->item('list_version'))->count_all_results();
        
        $this->parser->assign('count_of_cached_records', $count);
    }
    
    private function inject_number_of_compiled_templates() {
        $count = 0;
        if (file_exists($this->config->item('compile_directory'))) {
            $dir = scandir($this->config->item('compile_directory'));
            foreach ($dir as $file) {
                if (mb_substr($file, -4) == '.php') { $count++; }
            }
        }
        
        $this->parser->assign('count_of_compiled_templates', $count);
    }
}