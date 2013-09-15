<?php

/**
 * Tests helper functions.
 * @package LIST_Helpers
 * @author Andrej Jursa
 */

function get_all_supported_test_types() {
    $output = array();
    
    $dir_path = APPPATH . 'tests/';
    
    $dir = scandir($dir_path);
    if (count($dir)) { foreach ($dir as $file) {
        if (is_file($dir_path . $file) && substr($file, -4) == '.php') {
            include_once $dir_path . $file;
            $class_name = substr($file, 0, -4);
            if (class_exists($class_name)) {
                $test_object = new $class_name();
                $output[$test_object->get_test_type()] = $test_object->get_test_type_name();
                unset($test_object);
            }
        }
    }}
    
    return $output;
}

function get_all_supported_test_types_and_subtypes() {
    $output = array();
    
    $dir_path = APPPATH . 'tests/';
    
    $dir = scandir($dir_path);
    if (count($dir)) { foreach ($dir as $file) {
        if (is_file($dir_path . $file) && substr($file, -4) == '.php') {
            include_once $dir_path . $file;
            $class_name = substr($file, 0, -4);
            if (class_exists($class_name)) {
                $test_object = new $class_name();
                $output['types'][$test_object->get_test_type()] = $test_object->get_test_type_name();
                $output['subtypes'][$test_object->get_test_type()] = $test_object->get_test_subtypes();
                unset($test_object);
            }
        }
    }}
    
    return $output;
}