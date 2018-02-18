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
    7 => 'prefered_course_id',
    8 => 'widget_columns',
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
    'prefered_course_id' => 
    array (
      'field' => 'prefered_course_id',
      'rules' => 
      array (
      ),
    ),
    'widget_columns' => 
    array (
      'field' => 'widget_columns',
      'rules' => 
      array (
      ),
    ),
    'prefered_course' => 
    array (
      'field' => 'prefered_course',
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
    'room' => 
    array (
      'field' => 'room',
      'rules' => 
      array (
      ),
    ),
    'log' => 
    array (
      'field' => 'log',
      'rules' => 
      array (
      ),
    ),
    'admin_widget' => 
    array (
      'field' => 'admin_widget',
      'rules' => 
      array (
      ),
    ),
    'test_queue' => 
    array (
      'field' => 'test_queue',
      'rules' => 
      array (
      ),
    ),
    'created_content' => 
    array (
      'field' => 'created_content',
      'rules' => 
      array (
      ),
    ),
    'updated_content' => 
    array (
      'field' => 'updated_content',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'prefered_course' => 
    array (
      'class' => 'course',
      'other_field' => 'prefered_for_teacher',
      'join_self_as' => 'prefered_for_teacher',
      'joint_other_as' => 'prefered_course',
      'join_other_as' => 'prefered_course',
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
    'room' => 
    array (
      'join_table' => 'rooms_teachers_rel',
      'class' => 'room',
      'other_field' => 'teacher',
      'join_self_as' => 'teacher',
      'join_other_as' => 'room',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'created_content' => 
    array (
      'class' => 'Course_content_model',
      'other_field' => 'creator',
      'join_self_as' => 'creator',
      'join_other_as' => 'created_content',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'updated_content' => 
    array (
      'class' => 'Course_content_model',
      'other_field' => 'updator',
      'join_self_as' => 'updator',
      'join_other_as' => 'updated_content',
      'join_table' => '',
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
    'log' => 
    array (
      'class' => 'log',
      'other_field' => 'teacher',
      'join_self_as' => 'teacher',
      'join_other_as' => 'log',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'admin_widget' => 
    array (
      'class' => 'admin_widget',
      'other_field' => 'teacher',
      'join_self_as' => 'teacher',
      'join_other_as' => 'admin_widget',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'test_queue' => 
    array (
      'class' => 'test_queue',
      'other_field' => 'teacher',
      'join_self_as' => 'teacher',
      'join_other_as' => 'test_queue',
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