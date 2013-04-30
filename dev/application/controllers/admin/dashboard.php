<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dashboard controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Dashboard extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('dashboard');
        
        $this->parser->parse('backend/dashboard/index.tpl');
    }
    
}