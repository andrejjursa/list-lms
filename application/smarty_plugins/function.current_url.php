<?php

function smarty_function_current_url($params, $template) {
    $CI =& get_instance();
    $CI->load->library('usermanager');
    return encode_for_url($CI->usermanager->clear_current_url());
}