<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'tasks',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'text',
    5 => 'author_id',
    6 => 'internal_comment',
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
    'text' => 
    array (
      'field' => 'text',
      'rules' => 
      array (
      ),
    ),
    'author_id' => 
    array (
      'field' => 'author_id',
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
    'author' => 
    array (
      'field' => 'author',
      'rules' => 
      array (
      ),
    ),
    'category' => 
    array (
      'field' => 'category',
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
    'test' => 
    array (
      'field' => 'test',
      'rules' => 
      array (
      ),
    ),
    'project_selection' => 
    array (
      'field' => 'project_selection',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'author' => 
    array (
      'class' => 'teacher',
      'other_field' => 'task',
      'join_self_as' => 'task',
      'join_other_as' => 'author',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'category' => 
    array (
      'join_table' => 'task_category_rel',
      'class' => 'category',
      'other_field' => 'task',
      'join_self_as' => 'task',
      'join_other_as' => 'category',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task_set' => 
    array (
      'join_table' => 'task_task_set_rel',
      'class' => 'task_set',
      'other_field' => 'task',
      'join_self_as' => 'task',
      'join_other_as' => 'task_set',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'test' => 
    array (
      'class' => 'test',
      'other_field' => 'task',
      'join_self_as' => 'task',
      'join_other_as' => 'test',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'project_selection' => 
    array (
      'class' => 'project_selection',
      'other_field' => 'task',
      'join_self_as' => 'task',
      'join_other_as' => 'project_selection',
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