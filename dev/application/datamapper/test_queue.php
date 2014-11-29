<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'tests_queue',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'start',
    4 => 'finish',
    5 => 'task_set_id',
    6 => 'student_id',
    7 => 'task_id',
    8 => 'teacher_id',
    9 => 'priority',
    10 => 'worker',
    11 => 'percent_points',
    12 => 'percent_bonus',
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
    'start' => 
    array (
      'field' => 'start',
      'rules' => 
      array (
      ),
    ),
    'finish' => 
    array (
      'field' => 'finish',
      'rules' => 
      array (
      ),
    ),
    'task_set_id' => 
    array (
      'field' => 'task_set_id',
      'rules' => 
      array (
      ),
    ),
    'student_id' => 
    array (
      'field' => 'student_id',
      'rules' => 
      array (
      ),
    ),
    'task_id' => 
    array (
      'field' => 'task_id',
      'rules' => 
      array (
      ),
    ),
    'teacher_id' => 
    array (
      'field' => 'teacher_id',
      'rules' => 
      array (
      ),
    ),
    'priority' => 
    array (
      'field' => 'priority',
      'rules' => 
      array (
      ),
    ),
    'worker' => 
    array (
      'field' => 'worker',
      'rules' => 
      array (
      ),
    ),
    'percent_points' => 
    array (
      'field' => 'percent_points',
      'rules' => 
      array (
      ),
    ),
    'percent_bonus' => 
    array (
      'field' => 'percent_bonus',
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
    'student' => 
    array (
      'field' => 'student',
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
    'teacher' => 
    array (
      'field' => 'teacher',
      'rules' => 
      array (
      ),
    ),
    'test' => 
    array (
      'field' => 'test',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'task_set' => 
    array (
      'class' => 'task_set',
      'other_field' => 'test_queue',
      'join_self_as' => 'test_queue',
      'join_other_as' => 'task_set',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'student' => 
    array (
      'class' => 'student',
      'other_field' => 'test_queue',
      'join_self_as' => 'test_queue',
      'join_other_as' => 'student',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task' => 
    array (
      'class' => 'task',
      'other_field' => 'test_queue',
      'join_self_as' => 'test_queue',
      'join_other_as' => 'task',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'teacher' => 
    array (
      'class' => 'teacher',
      'other_field' => 'test_queue',
      'join_self_as' => 'test_queue',
      'join_other_as' => 'teacher',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'test' => 
    array (
      'join_table' => 'test_test_queue_rel',
      'class' => 'test',
      'other_field' => 'test_queue',
      'join_self_as' => 'test_queue',
      'join_other_as' => 'test',
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