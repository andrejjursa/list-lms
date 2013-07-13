<?php

/**
 * Teacher model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Teacher extends DataMapper {
    
    public $has_many = array(
        'solution',
        'comment' => array(
            'cascade_delete' => FALSE,
        ),
        'task' => array(
            'other_field' => 'author',
            'join_self_as' => 'author',
            'join_other_as' => 'task'
        ),
    );
    
}