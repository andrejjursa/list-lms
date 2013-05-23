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
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related_active_for_student($student);
        $course->where_related('participant/student', $student);
        $course->where_related_participant('allowed', 1);
        $course->get();
        
        $can_change_group = FALSE;
        
        if ($course->exists()) {
            if (is_null($course->groups_change_deadline) || date('U', strtotime($course->groups_change_deadline)) >= time()) { $can_change_group = TRUE; }
        }
        
        smarty_inject_days();
        $this->parser->add_css_file('frontend_groups.css');
        
        $this->parser->parse('frontend/groups/index.tpl', array('course' => $course, 'can_change_group' => $can_change_group));
    }
    
}