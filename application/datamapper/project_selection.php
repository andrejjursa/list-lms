<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'project_selections',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'student_id',
    4 => 'task_set_id',
    5 => 'task_id',
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
    'student_id' => 
    array (
      'field' => 'student_id',
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
    'task_id' => 
    array (
      'field' => 'task_id',
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
    'task_set' => 
    array (
      'field' => 'task_set',
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
  ),
  'has_one' => 
  array (
    'student' => 
    array (
      'class' => 'student',
      'other_field' => 'project_selection',
      'join_self_as' => 'project_selection',
      'join_other_as' => 'student',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task_set' => 
    array (
      'class' => 'task_set',
      'other_field' => 'project_selection',
      'join_self_as' => 'project_selection',
      'join_other_as' => 'task_set',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task' => 
    array (
      'class' => 'task',
      'other_field' => 'project_selection',
      'join_self_as' => 'project_selection',
      'join_other_as' => 'task',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
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