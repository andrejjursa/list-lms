<?php

include_once(APPPATH . 'third_party/simplehtmldom/simple_html_dom.php');

function smarty_modifier_add_base_url($string) {
    if (empty($string)) { return $string; }
    $absolute_url_pattern = '/^[a-z]+\:\/\//i';
    
    $html = str_get_html($string, true, true, DEFAULT_TARGET_CHARSET, false);
    
    foreach ($html->find('img, a') as $tag_with_link) {
        if ($tag_with_link->tag == 'img' && !preg_match($absolute_url_pattern, $tag_with_link->getAttribute('src'))) {
            $tag_with_link->setAttribute('src', add_base_url_base_url($tag_with_link->getAttribute('src')));
        } elseif ($tag_with_link->tag == 'a' && !preg_match($absolute_url_pattern, $tag_with_link->getAttribute('href'))) {
            $tag_with_link->setAttribute('href', add_base_url_base_url($tag_with_link->getAttribute('href')));
        } elseif ($tag_with_link->tag == 'a' && preg_match($absolute_url_pattern, $tag_with_link->getAttribute('href')) && trim((string)$tag_with_link->target) == '') {
            $tag_with_link->target = '_blank';
        }
    }
    
    ob_start();
    echo $html;
    return ob_get_clean();
}

function add_base_url_base_url($string) {
    if (empty($string)) { return $string; }
    $original_url = ltrim($string, '/\\');
    if (substr($original_url, 0, 2) == '..') {
        return $string;
    }
    $CI =& get_instance();
    $base_url = rtrim($CI->config->item('base_url'), '/') . '/';
    return $base_url . ltrim($string, '/\\');
}