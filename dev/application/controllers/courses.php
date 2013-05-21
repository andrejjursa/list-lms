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
        $this->parser->add_css_file('frontend_courses.css');
        $this->parser->add_js_file('courses/selection.js');
        $period_id = $this->input->post('period_id');
        $periods = new Period();
        if (intval($period_id) == 0) {
            $periods->limit(1);
        } else {
            $periods->where('id', $period_id);
        }
        $periods->order_by('sorting', 'asc')->get();
        $this->inject_period_options();
        $this->parser->parse('frontend/courses/index.tpl', array('periods' => $periods));
    }
    
    public function signup_to_course($course_id) {
        $this->output->set_content_type('application/json');
        
        $output = new stdClass();
        $output->status = FALSE;
        $output->message = '';
        $output->content = '';
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->get_by_id($course_id);
        
        if ($course->exists()) {
            if ($student->participant->where_related($course)->count() == 0) {
                $participant = new Participant();
                $participant->allowed = 0;
                $participant->save(array($student, $course));
                $this->db->trans_commit();
                $output->message = sprintf($this->lang->line('courses_message_signed_up_for_course'), $this->lang->text($course->name));
                $this->parser->assign('course', $course);
                $output->content = $this->parser->parse('frontend/courses/single_course.tpl', array(), TRUE);
                $output->status = TRUE;
            } else {
                $output->message = $this->lang->line('courses_message_already_in_course_or_waiting_for_approwal');
                $this->db->trans_rollback();
            }
        } else {
            $output->message = $this->lang->line('courses_message_course_not_found');
            $this->db->trans_rollback();
        }
        
        $this->output->set_output(json_encode($output));
    }


    private function inject_period_options() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc')->get_iterated();
        
        $data = array();
        
        foreach ($periods as $period) {
            $data[$period->id] = $period->name;
        }
        
        $this->parser->assign('period_options', $data);
    }
}