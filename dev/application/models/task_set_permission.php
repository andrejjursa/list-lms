<?php

/**
 * Task_set_permission model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set_permission extends DataMapper {
    
    public $has_one = array(
        'task_set',
        'group',
        'room',
    );
    
}