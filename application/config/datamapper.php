<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Data Mapper Configuration
 *
 * Global configuration settings that apply to all DataMapped models.
 */

$config['prefix'] = '';
$config['join_prefix'] = '';
$config['error_prefix'] = '<p>';
$config['error_suffix'] = '</p>';
$config['created_field'] = 'created';
$config['updated_field'] = 'updated';
$config['local_time'] = TRUE;
$config['unix_timestamp'] = FALSE;
$config['timestamp_format'] = 'Y-m-d H:i:s';
$config['lang_file_format'] = 'model_${model}';
$config['field_label_lang_format'] = '${model}_${field}';
$config['auto_transaction'] = FALSE;
$config['auto_populate_has_many'] = FALSE;
$config['auto_populate_has_one'] = FALSE;
$config['all_array_uses_ids'] = FALSE;

// set to FALSE to use the same DB instance across the board (breaks subqueries)
// Set to any acceptable parameters to $CI->database() to override the default.
$config['db_params'] = '';

// Uncomment to enable the production cache
$config['production_cache'] = APPPATH.'datamapper';

$config['extensions_path'] = 'extensions';
$config['extensions'] = array('array', 'translations', 'ordering', 'joins', 'unions', 'helpers', 'fixes');

/* End of file datamapper.php */
/* Location: ./sparks/Datamapper-ORM/config/datamapper.php */
