<?php

/**
 * Teacher model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Teacher extends DataMapper {
    
    public $has_many = array(
        'solution',
    );
    
}