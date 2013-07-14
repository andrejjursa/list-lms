<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'students',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'fullname',
    4 => 'email',
    5 => 'password',
    6 => 'language',
    7 => 'active_course_id',
    8 => 'password_token',
  ),
  'validation' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'rules' => 
      array (
        0 => 'integer',
      ),
    ),
    'updated' => 
    array (
      'field' => 'updated',
      'rules' => 
      array (
      ),
    ),
    'created' => 
    array (
      'field' => 'created',
      'rules' => 
      array (
      ),
    ),
    'fullname' => 
    array (
      'field' => 'fullname',
      'rules' => 
      array (
      ),
    ),
    'email' => 
    array (
      'field' => 'email',
      'rules' => 
      array (
      ),
    ),
    'password' => 
    array (
      'field' => 'password',
      'rules' => 
      array (
      ),
    ),
    'language' => 
    array (
      'field' => 'language',
      'rules' => 
      array (
      ),
    ),
    'active_course_id' => 
    array (
      'field' => 'active_course_id',
      'rules' => 
      array (
      ),
    ),
    'password_token' => 
    array (
      'field' => 'password_token',
      'rules' => 
      array (
      ),
    ),
    'active_course' => 
    array (
      'field' => 'active_course',
      'rules' => 
      array (
      ),
    ),
    'participant' => 
    array (
      'field' => 'participant',
      'rules' => 
      array (
      ),
    ),
    'solution' => 
    array (
      'field' => 'solution',
      'rules' => 
      array (
      ),
    ),
    'comment' => 
    array (
      'field' => 'comment',
      'rules' => 
      array (
      ),
    ),
    'comment_subscription' => 
    array (
      'field' => 'comment_subscription',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'active_course' => 
    array (
      'class' => 'course',
      'other_field' => 'active_for_student',
      'join_self_as' => 'active_for_student',
      'join_other_as' => 'active_course',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'comment' => 
    array (
      'cascade_delete' => false,
      'class' => 'comment',
      'other_field' => 'student',
      'join_self_as' => 'student',
      'join_other_as' => 'comment',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
    ),
    'comment_subscription' => 
    array (
      'class' => 'task_set',
      'other_field' => 'comment_subscriber_student',
      'join_self_as' => 'comment_subscriber_student',
      'join_other_as' => 'comment_subscription',
      'join_table' => 'task_set_comment_subscription_rel',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'participant' => 
    array (
      'class' => 'participant',
      'other_field' => 'student',
      'join_self_as' => 'student',
      'join_other_as' => 'participant',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'solution' => 
    array (
      'class' => 'solution',
      'other_field' => 'student',
      'join_self_as' => 'student',
      'join_other_as' => 'solution',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  '_field_tracking' => 
  array (
    'get_rules' => 
    array (
    ),
    'matches' => 
    array (
    ),
    'intval' => 
    array (
      0 => 'id',
    ),
  ),
);