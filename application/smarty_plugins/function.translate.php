<?php

function smarty_function_translate($params, $template) {
    $CI =& get_instance();
    $lang_line = isset($params['line']) ? $params['line'] : '';
    return $CI->lang->line($lang_line);
}