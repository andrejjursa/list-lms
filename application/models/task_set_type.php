<?php

/**
 * Task_set_type model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set_type extends DataMapper {
    
    public $has_many = array(
        'task_set',
    	'course' => array(
    		'join_table' => 'course_task_set_type_rel',
    	),
    );
}