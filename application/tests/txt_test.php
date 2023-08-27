<?php

include_once APPPATH . 'core/abstract_test.php';

/**
 * CPP test instance.
 *
 * @package LIST_Tests
 * @author  Andrej Jursa
 */
class txt_test extends abstract_test
{
    
    public const UNIT_TEST_CLASS_TO_RUN_REGEXP = '/^[a-zA-Z]{1,}[\_a-zA-Z0-9]*$/';
    
    protected $test_subtypes = [
        'simple_test' => [
            'name'                  => 'lang:txt_tests_subtype_simple_test_name',
            'method'                => 'run_simple_test',
            'configure_view'        => 'tests/txt/configure.tpl',
            'configure_before_save' => 'save_simple_test_config',
            'configure_validator'   => 'validator_simple_test',
            'configure_uploader'    => 'uploader_simple_test',
        ],
    ];
    
    public function get_test_type_name(): string
    {
        return $this->CI->lang->line('tests_txt_type_name');
    }
    
    /**
     * @throws TestException
     */
    protected function run_simple_test($save_score = false, $score_token = '', $score_student = null): string
    {
        $working_directory = $this->make_test_directory();
        $this->extract_zip_to($this->get_input_zip_file());
        $this->extract_zip_to(
            $this->get_current_test_source_directory()
            . $this->get_current_test_configuration_value('zip_file')
        );
        
        $scripts_directory = $this->get_test_scripts_directory();
        $exec_command = $scripts_directory . 'start_txt_simple_test ' 
            . $this->get_test_timeout() . ' ' . rtrim(getcwd(), '\\/')
            . DIRECTORY_SEPARATOR . $working_directory;
        $output_data = [];
        $exit_code = 0;
        @exec($exec_command, $output_data, $exit_code);
        $output = $this->read_output_file(self::TEST_OUTPUT_FILE);
        $scoring = $this->read_output_file(self::TEST_SCORING_FILE);
        $this->set_last_exit_code($exit_code);
        
        if (!empty($scoring)) {
            $this->construct_io_test_result(
                (double)$scoring,
                (double)$this->get_current_test_configuration_value('scoring_percents'),
                'lang:tasks_test_result_score_name_general_test'
            );
        } else {
            $this->construct_io_test_result(
                0,
                (double)$this->get_current_test_configuration_value('scoring_percents'),
                'lang:tasks_test_result_score_name_general_test'
            );
        }
        
        $this->delete_test_directory();
        
        return $this->encode_output($output);
    }
    
    protected function save_simple_test_config($new_config)
    {
        $old_config = $this->get_current_test_configuration();
        return array_merge($old_config, $new_config);
    }
    
    protected function validator_simple_test(): bool
    {
        $this->CI->form_validation->set_rules(
            'configuration[scoring_percents]',
            'lang:txt_tests_config_validation_simple_test_scoring_percents',
            'required|number|greater_than_equal[0]|less_than_equal[100]'
        );
        return true;
    }
    
    protected function uploader_simple_test(&$new_config): bool
    {
        $valid = true;
        if ($this->was_file_sent('zip_file')) {
            $data = $this->upload_file('unit_test', 'zip_file', 'zip', [
                'overwrite' => true,
                'file_name' => 'unit_test.zip',
            ], true, 'java', 'unit_test.zip');
            if ($data === false) {
                $valid = false;
            } else {
                $new_config['zip_file'] = 'unit_test/' . $data['file_name'];
            }
        }
        return $valid;
    }
    
}
