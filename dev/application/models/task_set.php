<?php

/**
 * Task_set model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set extends DataMapper {
    
    public $has_many = array(
        'task' => array(
            'join_table' => 'task_task_set_rel',
        ),
    );
}