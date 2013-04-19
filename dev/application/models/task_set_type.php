<?php

/**
 * Task_set_type model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set_type extends DataMapper {
    
    public $has_many = array(
        'task_set' => array(
            'join_table' => 'task_set_task_set_type_rel',
        ),
    );
}