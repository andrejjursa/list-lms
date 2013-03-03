<?php

function create_internal_url($relative_url) {
    $CI =& get_instance();
    if ($CI->config->item('rewrite_engine_enabled')) {
        return base_url('/' . trim($relative_url, '/'));
    } else {
        return base_url('index.php/' . trim($relative_url, '/'));
    }
}