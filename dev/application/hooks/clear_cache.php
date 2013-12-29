<?php

class Clear_Cache {
    
    public function execute($params = array()) {
        $CI =& get_instance();
        if ($CI->output->get_internal_value(LIST_Output::IV_ACTION_RESULT)) {
            $files = isset($params[$CI->router->directory][$CI->router->class][$CI->router->method]) ? (is_array($params[$CI->router->directory][$CI->router->class][$CI->router->method]) ? $params[$CI->router->directory][$CI->router->class][$CI->router->method] : array($params[$CI->router->directory][$CI->router->class][$CI->router->method])) : array();
            if (count($files)) {
                //$view_path = APPPATH . 'views/';
                foreach($files as $file => $group_function) {
                    if ($file == '*') {
                        $CI->parser->clearAllCache();
                    /*} elseif (substr($file, -2) == '/*') {
                        $path = substr($file, 0, -1);
                        if (file_exists($view_path . $path)) {
                            $this->recursive_clear_cache($path, $view_path, $group_function);
                        }
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'tpl' && file_exists($view_path . $file)) {*/
                    } else {
                        $CI->parser->clearCache($file, is_string($group_function) ? $group_function : $group_function($CI));
                    }
                }
                //die();
            }
        }
    }
    
    /*private function recursive_clear_cache($path, $view_path, $group_function) {
        $CI =& get_instance();
        $files_dirs = scandir($view_path . $path);
        if (count($files_dirs)) {
            foreach ($files_dirs as $file_dir) {
                if ($file_dir != '.' && $file_dir != '..') {
                    if (is_dir($view_path . $path . $file_dir)) {
                        $this->recursive_clear_cache($path . $file_dir . '/', $view_path, $group_function);
                    } elseif (pathinfo($view_path . $path . $file_dir, PATHINFO_EXTENSION) == 'tpl') {
                        $CI->parser->clearCache($path . $file_dir, is_string($group_function) ? $group_function : $group_function($CI));
                    }
                }
            }
        }
    }*/
    
}