<?php

/**
 * Task model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task extends DataMapper {
    
    public $has_many = array(
        'category' => array(
            'join_table' => 'task_category_rel',
        ),
        'task_set' => array(
            'join_table' => 'task_task_set_rel',
        ),
    );
}