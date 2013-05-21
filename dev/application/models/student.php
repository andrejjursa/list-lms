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
    
    public $has_one = array(
        'active_course' => array(
            'class' => 'course',
            'other_field' => 'active_for_student',
        ),
    );
    
}