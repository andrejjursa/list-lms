<?php

/**
 * Group model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Group extends DataMapper {
    
    public $has_one = array(
        'course'
    );
    
    public $has_many = array(
        'room'
    );
}