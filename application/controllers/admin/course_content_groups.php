<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Course content groups controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Course_content_groups extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('course_content_groups');
        
        $this->inject_courses();
        
        $this->parser->parse('backend/course_content_groups/index.tpl');
    }
    
    private function inject_courses()
    {
        $this->parser->assign('courses', Course::get_all_courses_for_form_select());
    }
    
}