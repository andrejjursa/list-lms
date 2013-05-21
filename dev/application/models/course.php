<?php

/**
 * Course model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course extends DataMapper {
    
    //const ACTIVE_COURSE_SESSION_NAME = 'ACTIVE_COURSE_SESSION';
    
    public $has_one = array(
        'period'
    );
    
    public $has_many = array(
        'group',
    	'task_set_type' => array(
    		'join_table' => 'course_task_set_type_rel',
    	),
    	'task_set',
        'participant',
        'active_for_student' => array(
            'class' => 'student',
            'other_field' => 'active_course',
        ),
    );
    
    /*public function set_as_active() {
        if (!is_null($this->id)) {
            $CI =& get_instance();
            $CI->load->database();
            $CI->load->library('session');
            
            $CI->session->set_userdata(self::ACTIVE_COURSE_SESSION_NAME, $this->id);
            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_as_active() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
            
        $id = $CI->session->userdata(self::ACTIVE_COURSE_SESSION_NAME);
        
        $this->get_by_id(intval($id));
        
        return $this;
    }
    
    public function get_active_course_id() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
            
        return $CI->session->userdata(self::ACTIVE_COURSE_SESSION_NAME);
    }*/
    
}