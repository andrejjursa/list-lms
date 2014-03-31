<?php

/**
 * Project selection model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Project_selection extends DataMapper {
    
    public $has_one = array(
        'student',
        'task_set',
        'task',
    );
    
}