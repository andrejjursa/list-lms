<?php

function smarty_modifier_add_to_url($string, $url_part, $can_add = TRUE) {
    return add_to_internal_url($string, $url_part, $can_add);
}