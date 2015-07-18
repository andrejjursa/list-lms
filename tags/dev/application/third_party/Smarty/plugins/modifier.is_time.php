<?php

function smarty_modifier_is_time($string) {
    $CI =& get_instance();
    $CI->load->helper('application');
    return is_time($string);
}
