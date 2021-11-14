<?php

include_once APPPATH . 'core/abstract_test.php';

/**
 * Python test instance.
 *
 * @package LIST_Tests
 * @author  Andrej Jursa
 */
class python_test extends abstract_test
{
    
    public const UNIT_TEST_CLASS_TO_RUN_REGEXP = '/^[a-zA-Z]{1,}[\_a-zA-Z0-9]*$/';
    
    protected $test_subtypes = [
        'unit_test' => [
            'name'                  => 'lang:python_tests_subtype_unit_test_name',
            'method'                => 'run_unit_test',
            'configure_view'        => 'tests/python/configure.tpl',
            'configure_before_save' => 'save_unit_test_config',
            'configure_validator'   => 'validator_unit_test',
            'configure_uploader'    => 'uploader_unit_test',
        ],
        'io_test'   => [
            'name'                  => 'lang:python_tests_subtype_io_test_name',
            'method'                => 'run_io_test',
            'configure_view'        => 'tests/python/configure_io.tpl',
            'configure_js'          => 'tests/python/configure_io.js',
            'configure_before_save' => 'save_io_test_config',
            'configure_validator'   => 'validator_io_test',
            'configure_uploader'    => 'uploader_io_test',
        ],
    ];
    
    public function get_test_type_name(): string
    {
        return $this->CI->lang->line('tests_python_type_name');
    }
    
    /**
     * @throws TestException
     */
    protected function run_unit_test($save_score = false, $score_token = '', $score_student = null): string
    {
        $working_directory = $this->make_test_directory();
        $this->extract_zip_to($this->get_input_zip_file());
        $this->extract_zip_to(
            $this->get_current_test_source_directory()
            . $this->get_current_test_configuration_value('zip_file')
        );
        $this->copy_file_to('test_sources/private/python/LISTTestScoring.py');
        $sandbox = $this->get_sandbox_type();
        $this->create_encryption_phrase($working_directory);
        
        $class_to_run = $this->get_current_test_configuration_value('class_to_run');
        if (!preg_match(self::UNIT_TEST_CLASS_TO_RUN_REGEXP, $class_to_run)) {
            set_time_limit(120);
            return $this->CI->lang->line('python_tests_run_error_unit_test_class_not_set');
        }
        
        $scripts_directory = $this->get_test_scripts_directory();
        //$exec_command = $scripts_directory . 'test ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR
        // . $working_directory . ' ' . $class_to_run . ' PYTHON ' . $this->get_test_timeout();
        $exec_command = $scripts_directory . 'execute_test pyUnit ' . $sandbox . ' ' . $class_to_run . ' '
            . $this->get_test_timeout() . ' ' . rtrim(getcwd(), '\\/')
            . DIRECTORY_SEPARATOR . $working_directory;
        $output_data = [];
        $exit_code = 0;
        @exec($exec_command, $output_data, $exit_code);
        $output = $this->read_output_file(self::TEST_OUTPUT_FILE);
        $scoring = $this->read_output_file(self::TEST_SCORING_FILE);
        $this->set_last_exit_code($exit_code);
        
        if (!empty($scoring)) {
            try {
                $this->decode_scoring($scoring);
            } catch (Exception $e) {
                $output .= '<br /><br /><span style="color: red;">' . $e->getMessage() . '</span>';
            }
        }
        
        /*if ($save_score) {
            $this->save_test_result($exit_code, $score_student, $score_token);
        }*/
        
        $this->delete_test_directory();
        
        $lines = (int)$this->get_current_test_configuration_value('max_output_lines');
        
        return $this->encode_output($this->truncate_lines($output, $lines));
    }
    
    protected function save_unit_test_config($new_config): ?array
    {
        $old_config = $this->get_current_test_configuration();
        return array_merge($old_config, $new_config);
    }
    
    protected function validator_unit_test(): bool
    {
        $this->CI->form_validation->set_rules(
            'configuration[class_to_run]',
            'lang:python_tests_config_validation_unit_test_class_to_run',
            'required|regex_match[' . self::UNIT_TEST_CLASS_TO_RUN_REGEXP . ']'
        );
        $this->CI->form_validation->set_rules(
            'configuration[max_output_lines]',
            'lang:python_tests_config_validation_unit_test_output_maximum_lines',
            'required|integer|greater_than_equal[0]'
        );
        return true;
    }
    
    protected function uploader_unit_test(&$new_config): bool
    {
        $valid = true;
        if ($this->was_file_sent('zip_file')) {
            $data = $this->upload_file('unit_test', 'zip_file', 'zip', [
                'overwrite' => true,
                'file_name' => 'unit_test.zip',
            ], true, 'py', 'unit_test.zip');
            if ($data === false) {
                $valid = false;
            } else {
                $new_config['zip_file'] = 'unit_test/' . $data['file_name'];
            }
        }
        return $valid;
    }
    
    /**
     * @throws TestException
     */
    protected function run_io_test($save_score = false, $score_token = '', $score_student = null): string
    {
        $working_directory = $this->make_test_directory();
        $this->extract_zip_to($this->get_input_zip_file());
        $this->copy_file_to(
            $this->get_current_test_source_directory()
            . $this->get_current_test_configuration_value('input_file'),
            'test_data'
        );
        if ($this->get_current_test_configuration_value('judge_type') == 'diff') {
            $this->copy_file_to(
                $this->get_current_test_source_directory()
                . $this->get_current_test_configuration_value('target_file'),
                'test_data'
            );
        } else {
            $this->copy_file_to(
                $this->get_current_test_source_directory()
                . $this->get_current_test_configuration_value('judge_source'),
                'test_data'
            );
        }
        $sandbox = $this->get_sandbox_type();
        
        $file_to_run = $this->get_current_test_configuration_value('file_to_run');
        if (trim($file_to_run) == '') {
            set_time_limit(120);
            return $this->CI->lang->line('python_tests_run_error_io_test_file_not_set');
        }
        
        $scripts_directory = $this->get_test_scripts_directory();
        //$exec_command = $scripts_directory . 'test ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR
        // . $working_directory . ' ' . $file_to_run . ' PYTHONIO ' . $this->get_test_timeout()
        // . ' judge-type-' . $this->get_current_test_configuration_value('judge_type');
        $exec_command = $scripts_directory . 'execute_test pythonIO ' . $sandbox . ' ' . $file_to_run . ' '
            . $this->get_test_timeout() . ' ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR
            . $working_directory . ' judge-type-' . $this->get_current_test_configuration_value('judge_type');
        
        $output_data = [];
        $exit_code = 0;
        @exec($exec_command, $output_data, $exit_code);
        $output = $this->read_output_file(self::TEST_OUTPUT_FILE);
        $this->set_last_exit_code($exit_code);
        
        /*if ($save_score && $this->get_current_test_configuration_value('scoring_percents') && $exit_code == 0) {
            $this->save_test_result(
                (int)$this->get_current_test_configuration_value('scoring_percents'),
                $score_student,
                $score_token
            );
        }*/
        
        if ((double)$this->get_current_test_configuration_value('scoring_percents') > 0) {
            if ((int)$exit_code === 0) {
                $this->construct_io_test_result(
                    (double)$this->get_current_test_configuration_value('scoring_percents'),
                    (double)$this->get_current_test_configuration_value('scoring_percents')
                );
            } else {
                $this->construct_io_test_result(
                    0,
                    (double)$this->get_current_test_configuration_value('scoring_percents')
                );
            }
        }
        
        $this->delete_test_directory();
        
        return $output;
    }
    
    protected function save_io_test_config($new_config): ?array
    {
        $old_config = $this->get_current_test_configuration();
        return array_merge($old_config, $new_config);
    }
    
    protected function validator_io_test(): bool
    {
        $this->CI->form_validation->set_rules(
            'configuration[file_to_run]',
            'lang:python_test_config_validation_io_test_file_to_run',
            'required|regex_match[' . self::UNIT_TEST_CLASS_TO_RUN_REGEXP . ']'
        );
        $this->CI->form_validation->set_rules(
            'configuration[scoring_percents]',
            'lang:python_test_config_validation_io_test_scoring_percents',
            'required|integer|greater_than_equal[0]'
        );
        return true;
    }
    
    protected function uploader_io_test(&$new_config): bool
    {
        $valid = true;
        if ($this->was_file_sent('input_file')) {
            $data = $this->upload_file('io_test', 'input_file', 'txt', [
                'overwrite' => true,
                'file_name' => 'test_input.txt',
            ]);
            if ($data === false) {
                $valid = false;
            } else {
                $new_config['input_file'] = 'io_test/' . $data['file_name'];
            }
        }
        if ($this->was_file_sent('target_file')) {
            $data = $this->upload_file('io_test', 'target_file', 'txt', [
                'overwrite' => true,
                'file_name' => 'test_target.txt',
            ]);
            if ($data === false) {
                $valid = false;
            } else {
                $new_config['target_file'] = 'io_test/' . $data['file_name'];
            }
        }
        if ($this->was_file_sent('judge_source')) {
            $data = $this->upload_file('io_test', 'judge_source', 'py', [
                'overwrite' => true,
                'file_name' => 'test_judge.py',
            ]);
            if ($data === false) {
                $valid = false;
            } else {
                $new_config['judge_source'] = 'io_test/' . $data['file_name'];
            }
        }
        return $valid;
    }
    
}
