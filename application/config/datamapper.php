<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
$config['local_time'] = true;
$config['unix_timestamp'] = false;
$config['timestamp_format'] = 'Y-m-d H:i:s';
$config['lang_file_format'] = 'model_${model}';
$config['field_label_lang_format'] = '${model}_${field}';
$config['auto_transaction'] = false;
$config['auto_populate_has_many'] = false;
$config['auto_populate_has_one'] = false;
$config['all_array_uses_ids'] = false;

// set to FALSE to use the same DB instance across the board (breaks subqueries)
// Set to any acceptable parameters to $CI->database() to override the default.
$config['db_params'] = '';

// Uncomment to enable the production cache
$config['production_cache'] = APPPATH . 'datamapper';

$config['extensions_path'] = 'extensions';
$config['extensions'] = ['array', 'translations', 'ordering', 'joins', 'unions', 'helpers', 'fixes'];

/* End of file datamapper.php */
/* Location: ./sparks/Datamapper-ORM/config/datamapper.php */
