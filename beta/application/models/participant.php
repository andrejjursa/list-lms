<?php

/**
 * Participant model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Participant extends DataMapper {
    
    public $has_one = array(
        'student',
        'course',
        'group',
    );
    
}