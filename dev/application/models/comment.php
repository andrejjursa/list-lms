<?php

/**
 * Comment model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Comment extends DataMapper {
    
    public $default_order_by = array('created');

    public $has_one = array(
        'student' => array(
            'cascade_delete' => FALSE,
        ),
        'teacher' => array(
            'cascade_delete' => FALSE,
        ),
        'task_set',
        'reply_at' => array(
            'class' => 'comment',
            'other_field' => 'comment',
            'join_self_as' => 'comment',
            'join_other_as' => 'reply_at',
        ),
    );
    
    public $has_many = array(
        'comment' => array(
            'other_field' => 'reply_at',
            'join_self_as' => 'reply_at',
            'join_other_as' => 'comment',
        ),
    );
    
}
