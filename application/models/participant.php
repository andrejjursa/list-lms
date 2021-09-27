<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Participant model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Participant extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_one = array(
        'student',
        'course',
        'group',
    );
    
}