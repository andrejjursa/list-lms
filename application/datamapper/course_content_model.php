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
    10 => 'sorting',
    11 => 'course_content_group_id',
    12 => 'files_visibility',
    13 => 'creator_id',
    14 => 'updator_id',
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
    'sorting' => 
    array (
      'field' => 'sorting',
      'rules' => 
      array (
      ),
    ),
    'course_content_group_id' => 
    array (
      'field' => 'course_content_group_id',
      'rules' => 
      array (
      ),
    ),
    'files_visibility' => 
    array (
      'field' => 'files_visibility',
      'rules' => 
      array (
      ),
    ),
    'creator_id' => 
    array (
      'field' => 'creator_id',
      'rules' => 
      array (
      ),
    ),
    'updator_id' => 
    array (
      'field' => 'updator_id',
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
    'course_content_group' => 
    array (
      'field' => 'course_content_group',
      'rules' => 
      array (
      ),
    ),
    'creator' => 
    array (
      'field' => 'creator',
      'rules' => 
      array (
      ),
    ),
    'updator' => 
    array (
      'field' => 'updator',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'creator' => 
    array (
      'class' => 'Teacher',
      'other_field' => 'created_content',
      'join_self_as' => 'created_content',
      'join_other_as' => 'creator',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'updator' => 
    array (
      'class' => 'Teacher',
      'other_field' => 'updated_content',
      'join_self_as' => 'updated_content',
      'join_other_as' => 'updator',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
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
    'course_content_group' => 
    array (
      'class' => 'course_content_group',
      'other_field' => 'course_content_model',
      'join_self_as' => 'course_content_model',
      'join_other_as' => 'course_content_group',
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