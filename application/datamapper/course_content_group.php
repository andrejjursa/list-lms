<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'course_content_groups',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'title',
    4 => 'course_id',
    5 => 'sorting',
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
    'title' => 
    array (
      'field' => 'title',
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
    'sorting' => 
    array (
      'field' => 'sorting',
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
    'course_content_model' => 
    array (
      'field' => 'course_content_model',
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
      'other_field' => 'course_content_group',
      'join_self_as' => 'course_content_group',
      'join_other_as' => 'course',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'course_content_model' => 
    array (
      'class' => 'course_content_model',
      'other_field' => 'course_content_group',
      'join_self_as' => 'course_content_group',
      'join_other_as' => 'course_content_model',
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