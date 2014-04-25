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
    
    /**
     * Loads admin widget, instantiate it and returns.
     * @param string $name widget name.
     * @param integer $id widget id in database.
     * @param array<mixed> $config configuration array.
     * @return \abstract_admin_widget widget.
     * @throws Exception on error.
     */
    public function admin_widget($name, $id, $config = array()) {
        $path = APPPATH . 'widgets/admin/' . strtolower($name) . '.php';
        $class_name = strtoupper(substr($name, 0, 1)) . strtolower(substr($name, 1));
        
        if (file_exists($path)) {
            include_once $path;
            if (class_exists($class_name)) {
                $widget_class = new $class_name($id, $config);
                if ($widget_class instanceof abstract_admin_widget) {
                    return $widget_class;
                } else {
                    unset($widget_class);
                    throw new Exception('Loaded class is not admin_widget.');
                }
            } else {
                throw new Exception('Can\'t find admin widget class <strong>' . $class_name . '</strong>.');
            }
        } else {
            throw new Exception('Can\'t find admin widget <strong>' . $name . '</strong>.');
        }
    }

    protected function &is_test_loaded($name) {
        if (isset($this->_list_tests[$name])) {
            return $this->_list_tests[$name];
        }
        throw new Exception('Class not initialised');
    }
    
}