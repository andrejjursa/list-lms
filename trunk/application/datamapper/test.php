<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'tests',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'type',
    5 => 'subtype',
    6 => 'task_id',
    7 => 'configuration',
    8 => 'enabled',
    9 => 'instructions',
    10 => 'enable_scoring',
    11 => 'timeout',
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
    'type' => 
    array (
      'field' => 'type',
      'rules' => 
      array (
      ),
    ),
    'subtype' => 
    array (
      'field' => 'subtype',
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
    'configuration' => 
    array (
      'field' => 'configuration',
      'rules' => 
      array (
      ),
    ),
    'enabled' => 
    array (
      'field' => 'enabled',
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
    'enable_scoring' => 
    array (
      'field' => 'enable_scoring',
      'rules' => 
      array (
      ),
    ),
    'timeout' => 
    array (
      'field' => 'timeout',
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
    'test_queue' => 
    array (
      'field' => 'test_queue',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'task' => 
    array (
      'class' => 'task',
      'other_field' => 'test',
      'join_self_as' => 'test',
      'join_other_as' => 'task',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'test_queue' => 
    array (
      'join_table' => 'test_test_queue_rel',
      'class' => 'test_queue',
      'other_field' => 'test',
      'join_self_as' => 'test',
      'join_other_as' => 'test_queue',
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