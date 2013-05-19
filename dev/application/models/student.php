<?php

/**
 * Student model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Student extends DataMapper {
    
    public $has_many = array(
        'participant',
    );
    
}