<?php

function smarty_modifier_is_time($string) {
    if (is_numeric($string) && intval($string) >= 0 && intval($string) < 86400) {
        $time = intval($string);
        $seconds = $time % 60;
        $minutes = (($time - $seconds) / 60) % 60; 
        $hours = ((($time - $seconds) / 60) - $minutes) / 60;
        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }
    return $string;
}
