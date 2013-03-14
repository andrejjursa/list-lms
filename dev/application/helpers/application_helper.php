<?php

/**
 * Creates internal url from relative url.
 * Function respects setting of rewrite engine from application/config/config.php.
 * @param string $relative_url relative url as controller/action/[parameters].
 * @return string internal url.
 */
function create_internal_url($relative_url) {
    $CI =& get_instance();
    if ($CI->config->item('rewrite_engine_enabled')) {
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