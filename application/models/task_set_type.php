<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Task_set_type model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set_type extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_many = array(
        'task_set',
    	'course' => array(
    		'join_table' => 'course_task_set_type_rel',
    	),
    );
}