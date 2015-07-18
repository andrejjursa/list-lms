<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'solution_versions',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'solution_id',
    4 => 'version',
    5 => 'download_lock',
    6 => 'ip_address',
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
    'solution_id' => 
    array (
      'field' => 'solution_id',
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
    'download_lock' => 
    array (
      'field' => 'download_lock',
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
    'solution' => 
    array (
      'field' => 'solution',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'solution' => 
    array (
      'class' => 'solution',
      'other_field' => 'solution_version',
      'join_self_as' => 'solution_version',
      'join_other_as' => 'solution',
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