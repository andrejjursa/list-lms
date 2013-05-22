<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Groups controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Groups extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->usermanager->student_login_protected_redirect();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->_initialize_student_menu();
    }
    
    public function index() {
        $this->_select_student_menu_pagetag('groups');
        $this->parser->parse('frontend/groups/index.tpl');
    }
    
}