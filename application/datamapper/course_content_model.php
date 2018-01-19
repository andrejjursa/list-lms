<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'course_content',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'title',
    4 => 'content',
    5 => 'course_id',
    6 => 'published',
    7 => 'published_from',
    8 => 'published_to',
    9 => 'public',
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
    'content' => 
    array (
      'field' => 'content',
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
    'published' => 
    array (
      'field' => 'published',
      'rules' => 
      array (
      ),
    ),
    'published_from' => 
    array (
      'field' => 'published_from',
      'rules' => 
      array (
      ),
    ),
    'published_to' => 
    array (
      'field' => 'published_to',
      'rules' => 
      array (
      ),
    ),
    'public' => 
    array (
      'field' => 'public',
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
  ),
  'has_one' => 
  array (
    'course' => 
    array (
      'class' => 'course',
      'other_field' => 'course_content_model',
      'join_self_as' => 'course_content_model',
      'join_other_as' => 'course',
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