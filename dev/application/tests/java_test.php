<?php

include_once APPPATH . 'core/abstract_test.php';

class java_test extends abstract_test {
        
    protected $test_subtypes = array(
        'unit_test' => array(
            'name' => 'lang:java_tests_subtype_unit_test_name',
            'method' => 'run_unit_test',
            'configure_view' => 'tests/java/configure.tpl',
            'configure_before_save' => 'save_unit_test_config',
            'configure_validator' => 'validator_unit_test',
            'configure_uploader' => 'uploader_unit_test',
        ),
    );

    public function get_test_type_name() {
        return $this->CI->lang->line('tests_java_type_name');
    }
    
    protected function run_unit_test() {
        $this->make_test_directory();
        $this->extract_zip_to($this->get_input_zip_file(), 'files');
        $this->extract_zip_to($this->get_current_test_source_directory() . $this->get_current_test_configuration_value('zip_file'), 'files');
        
        // TODO: execute test and read output
        
        $this->delete_test_directory();
        
        // TODO: return test output
        return '';
    }
    
    protected function save_unit_test_config($new_config) {
        $old_config = $this->get_current_test_configuration();
        return array_merge($old_config, $new_config);
    }
    
    protected function validator_unit_test() {
        $this->CI->form_validation->set_rules('configuration[class_to_run]', 'lang:java_tests_config_validation_unit_test_class_to_run', 'required');
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
