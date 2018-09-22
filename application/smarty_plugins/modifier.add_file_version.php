<?php

function smarty_modifier_add_file_version($string) {
    $CI =& get_instance();
    $file_version = $CI->config->item('list_version');
    $add = 'list_version=' . rawurlencode($file_version);
    if (strpos($string, '?', 0) === FALSE) {
        $add = '?' . $add;
    } else {
        $add = '&amp;' . $add;
    }
    return $string . $add;
}