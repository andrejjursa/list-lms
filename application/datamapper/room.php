<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'rooms',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'name',
    4 => 'group_id',
    5 => 'time_begin',
    6 => 'time_end',
    7 => 'time_day',
    8 => 'capacity',
    9 => 'teachers_plain',
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
    'group_id' => 
    array (
      'field' => 'group_id',
      'rules' => 
      array (
      ),
    ),
    'time_begin' => 
    array (
      'field' => 'time_begin',
      'rules' => 
      array (
      ),
    ),
    'time_end' => 
    array (
      'field' => 'time_end',
      'rules' => 
      array (
      ),
    ),
    'time_day' => 
    array (
      'field' => 'time_day',
      'rules' => 
      array (
      ),
    ),
    'capacity' => 
    array (
      'field' => 'capacity',
      'rules' => 
      array (
      ),
    ),
    'teachers_plain' => 
    array (
      'field' => 'teachers_plain',
      'rules' => 
      array (
      ),
    ),
    'group' => 
    array (
      'field' => 'group',
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
    'group' => 
    array (
      'class' => 'group',
      'other_field' => 'room',
      'join_self_as' => 'room',
      'join_other_as' => 'group',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
  ),
  'has_many' => 
  array (
    'teacher' => 
    array (
      'join_table' => 'rooms_teachers_rel',
      'class' => 'teacher',
      'other_field' => 'room',
      'join_self_as' => 'room',
      'join_other_as' => 'teacher',
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