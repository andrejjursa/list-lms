<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'courses',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'period_id',
    5 => 'description',
    6 => 'capacity',
    7 => 'groups_change_deadline',
    8 => 'default_points_to_remove',
    9 => 'allow_subscription_to',
    10 => 'test_scoring_deadline',
    11 => 'hide_in_lists',
    12 => 'auto_accept_students',
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
    'period_id' => 
    array (
      'field' => 'period_id',
      'rules' => 
      array (
      ),
    ),
    'description' => 
    array (
      'field' => 'description',
      'rules' => 
      array (
      ),
    ),
    'capacity' => 
    array (
      'field' => 'capacity',
      'rules' => 
      array (
      ),
    ),
    'groups_change_deadline' => 
    array (
      'field' => 'groups_change_deadline',
      'rules' => 
      array (
      ),
    ),
    'default_points_to_remove' => 
    array (
      'field' => 'default_points_to_remove',
      'rules' => 
      array (
      ),
    ),
    'allow_subscription_to' => 
    array (
      'field' => 'allow_subscription_to',
      'rules' => 
      array (
      ),
    ),
    'test_scoring_deadline' => 
    array (
      'field' => 'test_scoring_deadline',
      'rules' => 
      array (
      ),
    ),
    'hide_in_lists' => 
    array (
      'field' => 'hide_in_lists',
      'rules' => 
      array (
      ),
    ),
    'auto_accept_students' => 
    array (
      'field' => 'auto_accept_students',
      'rules' => 
      array (
      ),
    ),
    'period' => 
    array (
      'field' => 'period',
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
    'task_set_type' => 
    array (
      'field' => 'task_set_type',
      'rules' => 
      array (
      ),
    ),
    'task_set' => 
    array (
      'field' => 'task_set',
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
    'active_for_student' => 
    array (
      'field' => 'active_for_student',
      'rules' => 
      array (
      ),
    ),
    'prefered_for_teacher' => 
    array (
      'field' => 'prefered_for_teacher',
      'rules' => 
      array (
      ),
    ),
    'course_content' => 
    array (
      'field' => 'course_content',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'period' => 
    array (
      'class' => 'period',
      'other_field' => 'course',
      'join_self_as' => 'course',
      'join_other_as' => 'period',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'task_set_type' => 
    array (
      'join_table' => 'course_task_set_type_rel',
      'class' => 'task_set_type',
      'other_field' => 'course',
      'join_self_as' => 'course',
      'join_other_as' => 'task_set_type',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'active_for_student' => 
    array (
      'class' => 'student',
      'other_field' => 'active_course',
      'join_self_as' => 'active_course',
      'join_other_as' => 'active_for_student',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'prefered_for_teacher' => 
    array (
      'class' => 'teacher',
      'other_field' => 'prefered_course',
      'join_self_as' => 'prefered_course',
      'joint_other_as' => 'prefered_for_teacher',
      'join_other_as' => 'prefered_for_teacher',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'group' => 
    array (
      'class' => 'group',
      'other_field' => 'course',
      'join_self_as' => 'course',
      'join_other_as' => 'group',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task_set' => 
    array (
      'class' => 'task_set',
      'other_field' => 'course',
      'join_self_as' => 'course',
      'join_other_as' => 'task_set',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'participant' => 
    array (
      'class' => 'participant',
      'other_field' => 'course',
      'join_self_as' => 'course',
      'join_other_as' => 'participant',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'course_content' => 
    array (
      'class' => 'course_content',
      'other_field' => 'course',
      'join_self_as' => 'course',
      'join_other_as' => 'course_content',
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