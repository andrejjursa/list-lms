<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Courses controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Courses extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->usermanager->student_login_protected_redirect();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->_initialize_student_menu();
    }
    
    public function index() {
        $this->_select_student_menu_pagetag('courses');
        $periods = new Period();
        $periods->order_by('sorting', 'asc')->get_iterated();
        $this->parser->parse('frontend/courses/index.tpl', array('periods' => $periods));
    }
}