<?php

abstract class abstract_test {
    
    protected $CI = NULL;
    private $test_type = NULL;
    protected $test_subtypes = NULL;
    private $current_test = NULL;
    private $zip_file_path = NULL;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->determine_test_type();
        $this->CI->lang->load('tests/' . $this->test_type);
        $this->CI->lang->load('tests_general');
    }
    
    abstract public function get_test_type_name();

    public function get_test_type() {
        return $this->test_type;
    }
    
    public function get_test_subtypes() {
        $output = array();
        if (is_array($this->test_subtypes) && count($this->test_subtypes)) {
            foreach ($this->test_subtypes as $subtype => $config) {
                $output[$subtype] = isset($config['name']) ? $config['name'] : $subtype;
            }
        }
        return $output;
    }
    
    public function get_current_test_subtype() {
        if (is_array($this->current_test)) {
            return $this->current_test['subtype'];
        }
        return NULL;
    }
    
    public function get_current_test_configuration() {
        if (is_array($this->current_test)) {
            return $this->current_test['config'];
        }
        return NULL;
    }
    
    public function get_current_test_configuration_value($constant) {
        if (is_array($this->current_test) && is_string($constant) && isset($this->current_test['config'][$constant])) {
            return $this->current_test['config'][$constant];
        }
        return NULL;
    }

    public function initialize($test_model) {
        if (is_object($test_model) && !($test_model instanceof Test)) {
            throw new TestException($this->CI->lang->line('tests_general_error_cant_initialize_with_non_test_model'), 1000001);
        } elseif (is_integer($test_model)) {
            $test_model_id = $test_model;
            $test_model = new Test();
            $test_model->get_by_id($test_model_id);
        }
        
        if ($test_model->exists()) {
            if ($test_model->type !== $this->test_type) {
                throw new TestException(sprintf($this->CI->lang->line('tests_general_error_test_type_is_not_supported'), get_class($this)), 1000002);
            } elseif (!array_key_exists($test_model->subtype, $this->test_subtypes)) {
                throw new TestException(sprintf($this->CI->lang->line('tests_general_error_test_subtype_is_not_supported'), get_class($this)), 1000003);
            }
            $current_test = array(
                'subtype' => $test_model->subtype,
                'config' => unserialize($test_model->configuration),
            );
            $this->current_test = $current_test;
        } else {
            throw new TestException($this->CI->lang->line('tests_general_error_test_record_not_exists'), 1000004);
        }
    }
    
    public function run($input_zip_file) {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1100001);
        }
        if (!file_exists($input_zip_file)) {
            throw new TestException($this->CI->lang->line('tests_general_error_input_zip_file_not_found'), 1100002);
        }
        $method_name = $this->test_subtypes[$this->get_current_test_subtype()]['method'];
        if (!method_exists($this, $method_name)) {
            throw new TestException($this->CI->lang->line('tests_general_error_subtype_method_not_found'), 1100003);
        }
        $this->zip_file_path = $input_zip_file;
        return $this->$method_name();
    }

    private function determine_test_type() {
        if ($this->test_type === NULL) {
            $class_name = get_class($this);
            $this->test_type = strtolower(substr($class_name, 0, -5));
        }
    }
    
}

class TestException extends Exception {
    
}