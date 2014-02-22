<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$cache = array (
  'table' => 'task_set_permissions',
  'fields' => 
  array (
    0 => 'id',
    1 => 'updated',
    2 => 'created',
    3 => 'publish_start_time',
    4 => 'upload_end_time',
    5 => 'group_id',
    6 => 'room_id',
    7 => 'task_set_id',
    8 => 'enabled',
    9 => 'deadline_notification_emails',
    10 => 'deadline_notified',
    11 => 'deadline_notification_emails_handler',
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
    'publish_start_time' => 
    array (
      'field' => 'publish_start_time',
      'rules' => 
      array (
      ),
    ),
    'upload_end_time' => 
    array (
      'field' => 'upload_end_time',
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
    'room_id' => 
    array (
      'field' => 'room_id',
      'rules' => 
      array (
      ),
    ),
    'task_set_id' => 
    array (
      'field' => 'task_set_id',
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
    'deadline_notification_emails' => 
    array (
      'field' => 'deadline_notification_emails',
      'rules' => 
      array (
      ),
    ),
    'deadline_notified' => 
    array (
      'field' => 'deadline_notified',
      'rules' => 
      array (
      ),
    ),
    'deadline_notification_emails_handler' => 
    array (
      'field' => 'deadline_notification_emails_handler',
      'rules' => 
      array (
      ),
    ),
    'task_set' => 
    array (
      'field' => 'task_set',
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
    'room' => 
    array (
      'field' => 'room',
      'rules' => 
      array (
      ),
    ),
  ),
  'has_one' => 
  array (
    'task_set' => 
    array (
      'class' => 'task_set',
      'other_field' => 'task_set_permission',
      'join_self_as' => 'task_set_permission',
      'join_other_as' => 'task_set',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'group' => 
    array (
      'class' => 'group',
      'other_field' => 'task_set_permission',
      'join_self_as' => 'task_set_permission',
      'join_other_as' => 'group',
      'join_table' => '',
      'reciprocal' => false,
      'auto_populate' => NULL,
      'cascade_delete' => true,
    ),
    'room' => 
    array (
      'class' => 'room',
      'other_field' => 'task_set_permission',
      'join_self_as' => 'task_set_permission',
      'join_other_as' => 'room',
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