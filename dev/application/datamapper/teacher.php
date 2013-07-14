<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'teachers',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'fullname',
    4 => 'email',
    5 => 'password',
    6 => 'language',
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
    'task' => 
    array (
      'field' => 'task',
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
  ),
  'has_many' => 
  array (
    'comment' => 
    array (
      'cascade_delete' => false,
      'class' => 'comment',
      'other_field' => 'teacher',
      'join_self_as' => 'teacher',
      'join_other_as' => 'comment',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
    ),
    'task' => 
    array (
      'other_field' => 'author',
      'join_self_as' => 'author',
      'join_other_as' => 'task',
      'class' => 'task',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'comment_subscription' => 
    array (
      'class' => 'task_set',
      'other_field' => 'comment_subscriber_teacher',
      'join_self_as' => 'comment_subscriber_teacher',
      'join_other_as' => 'comment_subscription',
      'join_table' => 'task_set_comment_subscription_rel',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'solution' => 
    array (
      'class' => 'solution',
      'other_field' => 'teacher',
      'join_self_as' => 'teacher',
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