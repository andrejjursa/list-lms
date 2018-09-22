<?php

function smarty_modifier_sum_array($array) {
    if (is_array($array)) {
        return @array_sum($array);
    } else {
        return 0;
    }
}