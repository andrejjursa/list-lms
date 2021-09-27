<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Group model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Group extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_one = array(
        'course'
    );
    
    public $has_many = array(
        'room',
        'participant',
        'task_set',
        'task_set_permission',
    );
}