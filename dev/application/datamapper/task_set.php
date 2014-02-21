<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'task_sets',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'course_id',
    5 => 'task_set_type_id',
    6 => 'published',
    7 => 'publish_start_time',
    8 => 'upload_end_time',
    9 => 'group_id',
    10 => 'room_id',
    11 => 'instructions',
    12 => 'points_override',
    13 => 'comments_enabled',
    14 => 'comments_moderated',
    15 => 'allowed_file_types',
    16 => 'allowed_test_types',
    17 => 'internal_comment',
    18 => 'enable_tests_scoring',
    19 => 'test_min_needed',
    20 => 'test_max_allowed',
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
    'name' => 
    array (
      'field' => 'name',
      'rules' => 
      array (
      ),
    ),
    'course_id' => 
    array (
      'field' => 'course_id',
      'rules' => 
      array (
      ),
    ),
    'task_set_type_id' => 
    array (
      'field' => 'task_set_type_id',
      'rules' => 
      array (
      ),
    ),
    'published' => 
    array (
      'field' => 'published',
      'rules' => 
      array (
      ),
    ),
    'publish_start_time' => 
    array (
      'field' => 'publish_start_time',
      'rules' => 
      array (
      ),
    ),
    'upload_end_time' => 
    array (
      'field' => 'upload_end_time',
      'rules' => 
      array (
      ),
    ),
    'group_id' => 
    array (
      'field' => 'group_id',
      'rules' => 
      array (
      ),
    ),
    'room_id' => 
    array (
      'field' => 'room_id',
      'rules' => 
      array (
      ),
    ),
    'instructions' => 
    array (
      'field' => 'instructions',
      'rules' => 
      array (
      ),
    ),
    'points_override' => 
    array (
      'field' => 'points_override',
      'rules' => 
      array (
      ),
    ),
    'comments_enabled' => 
    array (
      'field' => 'comments_enabled',
      'rules' => 
      array (
      ),
    ),
    'comments_moderated' => 
    array (
      'field' => 'comments_moderated',
      'rules' => 
      array (
      ),
    ),
    'allowed_file_types' => 
    array (
      'field' => 'allowed_file_types',
      'rules' => 
      array (
      ),
    ),
    'allowed_test_types' => 
    array (
      'field' => 'allowed_test_types',
      'rules' => 
      array (
      ),
    ),
    'internal_comment' => 
    array (
      'field' => 'internal_comment',
      'rules' => 
      array (
      ),
    ),
    'enable_tests_scoring' => 
    array (
      'field' => 'enable_tests_scoring',
      'rules' => 
      array (
      ),
    ),
    'test_min_needed' => 
    array (
      'field' => 'test_min_needed',
      'rules' => 
      array (
      ),
    ),
    'test_max_allowed' => 
    array (
      'field' => 'test_max_allowed',
      'rules' => 
      array (
      ),
    ),
    'task_set_type' => 
    array (
      'field' => 'task_set_type',
      'rules' => 
      array (
      ),
    ),
    'course' => 
    array (
      'field' => 'course',
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
    'group' => 
    array (
      'field' => 'group',
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
    'task_set_permission' => 
    array (
      'field' => 'task_set_permission',
      'rules' => 
      array (
      ),
    ),
    'comment_subscriber_student' => 
    array (
      'field' => 'comment_subscriber_student',
      'rules' => 
      array (
      ),
    ),
    'comment_subscriber_teacher' => 
    array (
      'field' => 'comment_subscriber_teacher',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'task_set_type' => 
    array (
      'class' => 'task_set_type',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'task_set_type',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'course' => 
    array (
      'class' => 'course',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'course',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'room' => 
    array (
      'class' => 'room',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'room',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'group' => 
    array (
      'class' => 'group',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'group',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'task' => 
    array (
      'join_table' => 'task_task_set_rel',
      'class' => 'task',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'task',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'comment_subscriber_student' => 
    array (
      'class' => 'student',
      'other_field' => 'comment_subscription',
      'join_self_as' => 'comment_subscription',
      'join_other_as' => 'comment_subscriber_student',
      'join_table' => 'task_set_comment_subscription_rel',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'comment_subscriber_teacher' => 
    array (
      'class' => 'teacher',
      'other_field' => 'comment_subscription',
      'join_self_as' => 'comment_subscription',
      'join_other_as' => 'comment_subscriber_teacher',
      'join_table' => 'task_set_comment_subscription_rel',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'solution' => 
    array (
      'class' => 'solution',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'solution',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'comment' => 
    array (
      'class' => 'comment',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'comment',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task_set_permission' => 
    array (
      'class' => 'task_set_permission',
      'other_field' => 'task_set',
      'join_self_as' => 'task_set',
      'join_other_as' => 'task_set_permission',
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