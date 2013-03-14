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
    
}