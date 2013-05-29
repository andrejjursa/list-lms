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
    
}