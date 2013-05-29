<?php

function smarty_function_internal_url($params, $template) {
    if (isset($params['url'])) {
        return create_internal_url($params['url']);        
    }
    return '';
}