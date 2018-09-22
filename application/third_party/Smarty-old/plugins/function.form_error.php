<?php

function smarty_function_form_error($params, $template) {
    if (isset($params['field'])) {
        $CI =& get_instance();
        $CI->load->library('form_validation');
        $delimiter_left = isset($params['left_delimiter']) ? $params['left_delimiter'] : '';
        $delimiter_right = isset($params['right_delimiter']) ? $params['right_delimiter'] : '';
        return form_error($params['field'], $delimiter_left, $delimiter_right);
    }
    return '';
}