<?php

function smarty_function_overlay($params, $template) {
    $CI =& get_instance();
    $output = '';
    if (array_key_exists('table', $params) && array_key_exists('table_id', $params) && array_key_exists('column', $params)) {
        $table = $params['table'];
        $table_id = $params['table_id'];
        $column = $params['column'];
        $idiom = isset($params['idiom']) ? $params['idiom'] : NULL;
        $output =  $CI->lang->get_overlay($table, $table_id, $column, $idiom);
    }
    if (empty($output)) {
        $output = isset($params['default']) ? $params['default'] : '';
    }
    return $output;
}