<?php

class Clear_Cache {
    
    public function execute($params = array()) {
        $CI =& get_instance();
        if ($CI->output->get_internal_value(LIST_Output::IV_ACTION_RESULT)) {
            $files = isset($params[$CI->router->directory][$CI->router->class][$CI->router->method]) ? (is_array($params[$CI->router->directory][$CI->router->class][$CI->router->method]) ? $params[$CI->router->directory][$CI->router->class][$CI->router->method] : array($params[$CI->router->directory][$CI->router->class][$CI->router->method])) : array();
            if (count($files)) {
                foreach($files as $file => $group_function) {
                    if ($file == '*') {
                        $CI->parser->clearAllCache();
                    } else {
                        $CI->parser->clearCache($file, is_string($group_function) ? $group_function : $group_function($CI));
                    }
                }
            }
        }
    }
    
}