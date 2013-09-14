<?php

include_once APPPATH . 'core/abstract_test.php';

class java_test extends abstract_test {
    
    protected $test_subtypes = array(
        'unit_test' => array(
            'name' => 'lang:java_tests_subtype_unit_test_name',
            'method' => 'run_unit_test',
        ),
    );

    public function get_test_type_name() {
        return $this->CI->lang->line('tests_java_type_name');
    }
    
    protected function run_unit_test() {
        
    }
    
}
