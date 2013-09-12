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
        'prefered_for_teacher' => array(
            'class' => 'teacher',
            'other_field' => 'prefered_course',
            'join_self_as' => 'prefered_course',
            'joint_other_as' => 'prefered_for_teacher',
        ),
    );
    
    /**
     * Tests if this course have allowed subscription.
     * @return boolean TRUE when subscription is allowed for this (existing) course, FALSE otherwise.
     */
    public function is_subscription_allowed() {
        if (!is_null($this->id)) {
            if (is_null($this->allow_subscription_to)) { return TRUE; }
            if (time() <= strtotime($this->allow_subscription_to)) { return TRUE; }
        }
        return FALSE;
    }
}