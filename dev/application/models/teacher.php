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
        'comment_subscription' => array(
            'class' => 'task_set',
            'other_field' => 'comment_subscriber_teacher',
            'join_self_as' => 'comment_subscriber_teacher',
            'join_other_as' => 'comment_subscription',
            'join_table' => 'task_set_comment_subscription_rel',
        ),
    );
    public $has_one = array(
        'prefered_course' => array(
            'class' => 'course',
            'other_field' => 'prefered_for_teacher',
            'join_self_as' => 'prefered_for_teacher',
            'joint_other_as' => 'prefered_course',
        ),
    );
    
}