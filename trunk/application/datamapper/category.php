<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'categories',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'parent_id',
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
    'parent_id' => 
    array (
      'field' => 'parent_id',
      'rules' => 
      array (
      ),
    ),
    'parent' => 
    array (
      'field' => 'parent',
      'rules' => 
      array (
      ),
    ),
    'subcategory' => 
    array (
      'field' => 'subcategory',
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
    'parent' => 
    array (
      'class' => 'category',
      'other_field' => 'subcategory',
      'join_self_as' => 'subcategory',
      'join_other_as' => 'parent',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'subcategory' => 
    array (
      'class' => 'category',
      'other_field' => 'parent',
      'join_self_as' => 'parent',
      'join_other_as' => 'subcategory',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'task' => 
    array (
      'join_table' => 'task_category_rel',
      'class' => 'task',
      'other_field' => 'category',
      'join_self_as' => 'category',
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