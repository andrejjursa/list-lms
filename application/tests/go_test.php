<?php

include_once APPPATH . 'core/abstract_test.php';

/**
 * Go test instance.
 * @package LIST_Tests
 * @author Andrej Jursa
 */ 
class go_test extends abstract_test {
        
    const UNIT_TEST_CLASS_TO_RUN_REGEXP = '/^[a-zA-Z]{1,}[\_a-zA-Z0-9]*$/';
    
    protected $test_subtypes = array(
        'unit_test' => array(
            'name' => 'lang:go_tests_subtype_unit_test_name',
            'method' => 'run_unit_test',
            'configure_view' => 'tests/go/configure.tpl',
            'configure_before_save' => 'save_unit_test_config',
            'configure_validator' => 'validator_unit_test',
            'configure_uploader' => 'uploader_unit_test',
        ),
    );

    public function get_test_type_name() {
        return $this->CI->lang->line('tests_go_type_name');
    }
    
    protected function run_unit_test() {
        $working_directory = $this->make_test_directory();
        $this->extract_zip_to($this->get_input_zip_file());
        $this->extract_zip_to($this->get_current_test_source_directory() . $this->get_current_test_configuration_value('zip_file'));
        
        $class_to_run = $this->get_current_test_configuration_value('class_to_run');
        if (!preg_match(self::UNIT_TEST_CLASS_TO_RUN_REGEXP, $class_to_run)) {
            set_time_limit(120);
            return $this->CI->lang->line('go_tests_run_error_unit_test_class_not_set');
        }
        
        $scripts_directory = $this->get_test_scripts_directory();
        $exec_command = $scripts_directory . 'test ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR . $working_directory . ' ' . $class_to_run . ' GO';
        @exec($exec_command);
        $output = $this->read_output_file('test.out');
        
        $this->delete_test_directory();
        
        return $output;
    }
    
    protected function save_unit_test_config($new_config) {
        $old_config = $this->get_current_test_configuration();
        return array_merge($old_config, $new_config);
    }
    
    protected function validator_unit_test() {
        $this->CI->form_validation->set_rules('configuration[class_to_run]', 'lang:go_tests_config_validation_unit_test_class_to_run', 'required|regex_match[' . self::UNIT_TEST_CLASS_TO_RUN_REGEXP . ']');
        return TRUE;
    }
    
    protected function uploader_unit_test(&$new_config) {
        $valid = TRUE;
        if ($this->was_file_sent('zip_file')) {
            $data = $this->upload_file('unit_test', 'zip_file', 'zip', array(
                'overwrite' => TRUE,
                'file_name' => 'unit_test.zip',
            ));
            if ($data === FALSE) {
                $valid = FALSE;
            } else {
                $new_config['zip_file'] = 'unit_test/' . $data['file_name'];
            }
        }
        return $valid;
    }
    
}
