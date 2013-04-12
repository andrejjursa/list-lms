<?php

/**
 * Course model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course extends DataMapper {
    
    public $has_one = array(
        'period'
    );
    
    public $has_many = array(
        'group'
    );
    
}