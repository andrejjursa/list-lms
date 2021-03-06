<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'solutions',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'task_set_id',
    4 => 'student_id',
    5 => 'teacher_id',
    6 => 'comment',
    7 => 'tests_points',
    8 => 'revalidate',
    9 => 'not_considered',
    10 => 'ip_address',
    11 => 'best_version',
    12 => 'disable_evaluation_by_tests',
    13 => 'points',
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
    'teacher_id' => 
    array (
      'field' => 'teacher_id',
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
    'tests_points' => 
    array (
      'field' => 'tests_points',
      'rules' => 
      array (
      ),
    ),
    'revalidate' => 
    array (
      'field' => 'revalidate',
      'rules' => 
      array (
      ),
    ),
    'not_considered' => 
    array (
      'field' => 'not_considered',
      'rules' => 
      array (
      ),
    ),
    'ip_address' => 
    array (
      'field' => 'ip_address',
      'rules' => 
      array (
      ),
    ),
    'best_version' => 
    array (
      'field' => 'best_version',
      'rules' => 
      array (
      ),
    ),
    'disable_evaluation_by_tests' => 
    array (
      'field' => 'disable_evaluation_by_tests',
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
    'teacher' => 
    array (
      'field' => 'teacher',
      'rules' => 
      array (
      ),
    ),
    'solution_version' => 
    array (
      'field' => 'solution_version',
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
      'other_field' => 'solution',
      'join_self_as' => 'solution',
      'join_other_as' => 'task_set',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'student' => 
    array (
      'class' => 'student',
      'other_field' => 'solution',
      'join_self_as' => 'solution',
      'join_other_as' => 'student',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'teacher' => 
    array (
      'class' => 'teacher',
      'other_field' => 'solution',
      'join_self_as' => 'solution',
      'join_other_as' => 'teacher',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'solution_version' => 
    array (
      'class' => 'solution_version',
      'other_field' => 'solution',
      'join_self_as' => 'solution',
      'join_other_as' => 'solution_version',
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