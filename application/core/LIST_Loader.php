<?php

class LIST_Loader extends CI_Loader {
    
    protected $_list_tests = array();
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     * @param type $name
     * @return abstract_test
     * @throws Exception
     */
    public function test($name) {
        try {
            $test_class = $this->is_test_loaded(strtolower($name));
        } catch (Exception $e) {
            $path = APPPATH . 'tests/' . strtolower($name) . '_test.php';
            include_once $path;
            $class_name = strtolower($name) . '_test';
            if (class_exists($class_name)) {
                $test_class = new $class_name();
                $this->_list_tests[strtolower($name)] =& $test_class;
            } else {
                throw new Exception('Class <strong>' . $class_name . '</strong> does not exists!');
            }
        }
        return $test_class;
    }

    protected function &is_test_loaded($name) {
        if (isset($this->_list_tests[$name])) {
            return $this->_list_tests[$name];
        }
        throw new Exception('Class not initialised');
    }
    
}