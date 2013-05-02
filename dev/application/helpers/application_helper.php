<?php

/**
 * Aplication helper functions.
 * @package LIST_Helpers
 * @author Andrej Jursa
 */

/**
 * This function will check if there is server variable MOD_REWRITE_ENABLED, which is created in .htaccess.
 * @return boolean TRUE, if this variable exists and is set to yes, FALSE otherwise.
 */
function is_mod_rewrite_enabled() {
    if (isset($_SERVER['MOD_REWRITE_ENABLED']) && $_SERVER['MOD_REWRITE_ENABLED'] == 'yes') {
        return TRUE;
    }
    return FALSE;
}

/**
 * Creates internal url from relative url.
 * Function respects setting of rewrite engine from application/config/config.php.
 * @param string $relative_url relative url as controller/action/[parameters].
 * @return string internal url.
 */
function create_internal_url($relative_url) {
    $CI =& get_instance();
    if ($CI->config->item('rewrite_engine_enabled') && is_mod_rewrite_enabled()) {
        return base_url('/' . trim($relative_url, '/')) . $CI->config->item('url_suffix');
    } else {
        return base_url('index.php/' . trim($relative_url, '/')) . $CI->config->item('url_suffix');
    }
}

/**
 * Encodes string using base64 algorithm and replace some url invalid characters like / and = to - and _ .
 * @param string $string string to encode.
 * @return string encoded string.
 */ 
function encode_for_url($string) {
    $encoded64 = base64_encode($string);
    return str_replace(array('/', '='), array('-', '_'), $encoded64);
}

/**
 * Decodes string encoded by function {@link encode_for_url()}.
 * @param string $string string to decode.
 * @return string decoded string.
 */
function decode_from_url($string) {
    $decode64 = str_replace(array('-', '_'), array('/', '='), $string);
    return base64_decode($decode64);
}

/**
 * Translates url parameters to url segments string.
 * @param array<mixed> $params array of parameters (only non-array and non-object values will be added to output).
 * @return string segmented url of name => value pairs.
 */
function implode_uri_params($params) {
    if (is_array($params) && count($params) > 0) {
        $output = '';
        foreach($params as $name => $value) {
            if (!is_array($value) && !is_object($value)) {
                $pair = rawurlencode($name) . '/' . rawurlencode($value);
                $output .= strlen($output) > 0 ? '/' . $pair : $pair;
            }
        }
        return $output;
    }
    return '';
}

/**
 * Checks if database driver uses mysql, mysqli or pdo mysql.
 * @return boolean TRUE if uses one of this drivers.
 */ 
function db_is_mysql() {
    $CI =& get_instance();
    $provider = strtolower($CI->db->dbdriver);
    return $provider == 'mysql' || $provider == 'mysqli' || ($provider == 'pdo' && strpos($CI->db->hostname, 'mysql') !== FALSE);
}

/**
 * If database uses mysql, it changes table to InnoDB engine (to support transactions).
 * @param string $table table name.
 */
function change_mysql_table_to_InnoDB($table) {
    if (db_is_mysql()) {
        $CI =& get_instance();
        $CI->db->query('ALTER TABLE `' . $table . '` ENGINE = INNODB');
    }
}

/**
 * This function will construct days array and assign them to template parser.
 */
function smarty_inject_days() {
    $CI =& get_instance();
    $days = array(
        1 => $CI->lang->line('common_day_monday'),
        2 => $CI->lang->line('common_day_tuesday'),
        3 => $CI->lang->line('common_day_wednesday'),
        4 => $CI->lang->line('common_day_thursday'),
        5 => $CI->lang->line('common_day_friday'),
        6 => $CI->lang->line('common_day_saturday'),
        7 => $CI->lang->line('common_day_sunday'),
    );
    $CI->parser->assign('list_days', $days);
}

/** 
 * Recursively delete a directory.
 * @param string $dir directory name.
 * @param boolean $delete_root_too delete specified top-level directory as well. 
 */ 
function unlink_recursive($dir, $delete_root_too) { 
    if(!$dh = @opendir($dir)) { return; } 
    
    while (FALSE !== ($obj = readdir($dh))) { 
        if($obj == '.' || $obj == '..') { continue; } 

        if (!@unlink($dir . '/' . $obj)) { 
            unlink_recursive($dir.'/'.$obj, TRUE); 
        } 
    } 

    closedir($dh); 
    
    if ($delete_root_too) { @rmdir($dir); } 
    
    return; 
} 