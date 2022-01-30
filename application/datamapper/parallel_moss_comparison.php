<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'parallel_moss_comparisons',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'status',
    4 => 'teacher_id',
    5 => 'configuration',
    6 => 'processing_start',
    7 => 'processing_finish',
    8 => 'result_link',
  ),
  'validation' => 
  array (
    'configuration' => 
    array (
      'label' => 'configuration',
      'rules' => 
      array (
        0 => 'jsonEncode',
      ),
      'get_rules' => 
      array (
        0 => 'jsonDecode',
      ),
      'field' => 'configuration',
    ),
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
    'status' => 
    array (
      'field' => 'status',
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
    'processing_start' => 
    array (
      'field' => 'processing_start',
      'rules' => 
      array (
      ),
    ),
    'processing_finish' => 
    array (
      'field' => 'processing_finish',
      'rules' => 
      array (
      ),
    ),
    'result_link' => 
    array (
      'field' => 'result_link',
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
  ),
  'has_one' => 
  array (
    'teacher' => 
    array (
      'class' => 'teacher',
      'other_field' => 'parallel_moss_comparison',
      'join_self_as' => 'parallel_moss_comparison',
      'join_other_as' => 'teacher',
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
      0 => 'configuration',
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