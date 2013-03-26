<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courses extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('courses');
        
        $courses = new Course();
        $courses->get_iterated();
        
        $this->parser->parse('backend/courses/index.tpl', array('courses' => $courses));
    }
    
}