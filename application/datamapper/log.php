<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'logs',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'message',
    4 => 'ip_address',
    5 => 'language',
    6 => 'log_type',
    7 => 'student_id',
    8 => 'teacher_id',
    9 => 'affected_table',
    10 => 'affected_row_primary_id',
    11 => 'additional_data',
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
    'message' => 
    array (
      'field' => 'message',
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
    'language' => 
    array (
      'field' => 'language',
      'rules' => 
      array (
      ),
    ),
    'log_type' => 
    array (
      'field' => 'log_type',
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
    'affected_table' => 
    array (
      'field' => 'affected_table',
      'rules' => 
      array (
      ),
    ),
    'affected_row_primary_id' => 
    array (
      'field' => 'affected_row_primary_id',
      'rules' => 
      array (
      ),
    ),
    'additional_data' => 
    array (
      'field' => 'additional_data',
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
  ),
  'has_one' => 
  array (
    'student' => 
    array (
      'class' => 'student',
      'other_field' => 'log',
      'join_self_as' => 'log',
      'join_other_as' => 'student',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'teacher' => 
    array (
      'class' => 'teacher',
      'other_field' => 'log',
      'join_self_as' => 'log',
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