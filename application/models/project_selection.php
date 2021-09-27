<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Project selection model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Project_selection extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_one = array(
        'student',
        'task_set',
        'task',
    );
    
}