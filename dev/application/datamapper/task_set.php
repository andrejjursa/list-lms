<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'task_sets',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
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