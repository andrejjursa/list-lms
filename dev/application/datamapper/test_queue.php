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
    5 => 'test_type',
    6 => 'task_set_id',
    7 => 'student_id',
    8 => 'version',
    9 => 'task_id',
    10 => 'teacher_id',
    11 => 'priority',
    12 => 'original_priority',
    13 => 'worker',
    14 => 'points',
    15 => 'bonus',
    16 => 'status',
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
    'test_type' => 
    array (
      'field' => 'test_type',
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
    'version' => 
    array (
      'field' => 'version',
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
    'original_priority' => 
    array (
      'field' => 'original_priority',
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
    'points' => 
    array (
      'field' => 'points',
      'rules' => 
      array (
      ),
    ),
    'bonus' => 
    array (
      'field' => 'bonus',
      'rules' => 
      array (
      ),
    ),
    'status' => 
    array (
      'field' => 'status',
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