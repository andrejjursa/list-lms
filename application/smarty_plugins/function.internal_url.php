<?php

function smarty_function_internal_url($params, $template) {
    if (isset($params['url'])) {
        $simple = FALSE;
        if (isset($params['simple']) && (is_bool($params['simple']) || is_numeric($params['simple']) || strtolower($params['simple']) === 'true')) { $simple = (bool)$params['simple']; }
        return create_internal_url($params['url'], $simple);        
    }
    return '';
}