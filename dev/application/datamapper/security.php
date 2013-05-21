<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'security',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'account_type',
    4 => 'account_email',
    5 => 'login_ip_address',
    6 => 'login_browser',
    7 => 'login_failed_time',
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
    'account_type' => 
    array (
      'field' => 'account_type',
      'rules' => 
      array (
      ),
    ),
    'account_email' => 
    array (
      'field' => 'account_email',
      'rules' => 
      array (
      ),
    ),
    'login_ip_address' => 
    array (
      'field' => 'login_ip_address',
      'rules' => 
      array (
      ),
    ),
    'login_browser' => 
    array (
      'field' => 'login_browser',
      'rules' => 
      array (
      ),
    ),
    'login_failed_time' => 
    array (
      'field' => 'login_failed_time',
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