<?php

function smarty_function_translate_text($params, $template) {
    $CI =& get_instance();
    $text = isset($params['text']) ? $params['text'] : '';
    $default = isset($params['default']) ? $params['default'] : '';
    return $CI->lang->text($text, $default);
}