<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Task_set_permission model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set_permission extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_one = array(
        'task_set',
        'group',
        'room',
    );
    
}