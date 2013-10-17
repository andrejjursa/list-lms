<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'groups',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'course_id',
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
    'participant' => 
    array (
      'field' => 'participant',
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
    'task_set_permission' => 
    array (
      'field' => 'task_set_permission',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'course' => 
    array (
      'class' => 'course',
      'other_field' => 'group',
      'join_self_as' => 'group',
      'join_other_as' => 'course',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'room' => 
    array (
      'class' => 'room',
      'other_field' => 'group',
      'join_self_as' => 'group',
      'join_other_as' => 'room',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'participant' => 
    array (
      'class' => 'participant',
      'other_field' => 'group',
      'join_self_as' => 'group',
      'join_other_as' => 'participant',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task_set' => 
    array (
      'class' => 'task_set',
      'other_field' => 'group',
      'join_self_as' => 'group',
      'join_other_as' => 'task_set',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task_set_permission' => 
    array (
      'class' => 'task_set_permission',
      'other_field' => 'group',
      'join_self_as' => 'group',
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