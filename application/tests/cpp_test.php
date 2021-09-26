<?php

	include_once APPPATH . 'core/abstract_test.php';

	/**
	 * Java test instance.
	 * @package LIST_Tests
	 * @author Andrej Jursa
	 */
	class cpp_test extends abstract_test {

		const UNIT_TEST_CLASS_TO_RUN_REGEXP = '/^[a-zA-Z]{1,}[\_a-zA-Z0-9]*$/';

		protected $test_subtypes = array(
			'simple_test' => array(
				'name' => 'lang:cpp_tests_subtype_simple_test_name',
				'method' => 'run_simple_test',
				'configure_view' => 'tests/cpp/configure.tpl',
				'configure_before_save' => 'save_simple_test_config',
				'configure_validator' => 'validator_simple_test',
				'configure_uploader' => 'uploader_simple_test',
			),
		);

		public function get_test_type_name() {
			return $this->CI->lang->line('tests_cpp_type_name');
		}

		protected function run_simple_test($save_score = FALSE, $score_token = '', $score_student = NULL) {
			$working_directory = $this->make_test_directory();
			$this->extract_zip_to($this->get_input_zip_file());
			$this->extract_zip_to($this->get_current_test_source_directory() . $this->get_current_test_configuration_value('zip_file'));

			$class_to_run = $this->get_current_test_configuration_value('file_to_run');
			if (!preg_match(self::UNIT_TEST_CLASS_TO_RUN_REGEXP, $class_to_run)) {
				set_time_limit(120);
				return $this->CI->lang->line('cpp_tests_run_error_simple_test_file_not_set');
			}

			$scripts_directory = $this->get_test_scripts_directory();
			$exec_command = $scripts_directory . 'start_cpp_simple_test test_' . $class_to_run . '.cpp ' . $class_to_run . '.cpp ' . $this->get_test_timeout() . ' ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR . $working_directory;
			$output_data = array();
			$exit_code = 0;
			@exec($exec_command, $output_data, $exit_code);
			$output = $this->read_output_file(self::TEST_OUTPUT_FILE);
			$scoring = $this->read_output_file(self::TEST_SCORING_FILE);
			$this->set_last_exit_code($exit_code);

			if (!empty($scoring)) {
				$this->construct_io_test_result((double)$scoring, (double)$this->get_current_test_configuration_value('scoring_percents'), 'lang:tasks_test_result_score_name_general_test');
			} else {
				$this->construct_io_test_result(0, (double)$this->get_current_test_configuration_value('scoring_percents'), 'lang:tasks_test_result_score_name_general_test');
			}

			$this->delete_test_directory();

			$lines = (int)$this->get_current_test_configuration_value('max_output_lines');

			return $this->encode_output($this->truncate_lines($output, $lines));
		}

		protected function save_simple_test_config($new_config) {
			$old_config = $this->get_current_test_configuration();
			return array_merge($old_config, $new_config);
		}

		protected function validator_simple_test() {
			$this->CI->form_validation->set_rules('configuration[file_to_run]', 'lang:cpp_tests_config_validation_simple_test_file_to_run', 'required|regex_match[' . self::UNIT_TEST_CLASS_TO_RUN_REGEXP . ']');
			$this->CI->form_validation->set_rules('configuration[max_output_lines]', 'lang:cpp_tests_config_validation_simple_test_output_maximum_lines', 'required|integer|greater_than_equal[0]');
			$this->CI->form_validation->set_rules('configuration[scoring_percents]', 'lang:cpp_tests_config_validation_simple_test_scoring_percents', 'required|number|greater_than_equal[0]|less_than_equal[100]');
			return TRUE;
		}

		protected function uploader_simple_test(&$new_config) {
			$valid = TRUE;
			if ($this->was_file_sent('zip_file')) {
				$data = $this->upload_file('unit_test', 'zip_file', 'zip', array(
                    'overwrite' => TRUE,
                    'file_name' => 'unit_test.zip',
                ), TRUE, 'java', 'unit_test.zip');
				if ($data === FALSE) {
					$valid = FALSE;
				} else {
					$new_config['zip_file'] = 'unit_test/' . $data['file_name'];
				}
			}
			return $valid;
		}

		/*protected function run_unit_test($save_score = FALSE, $score_token = '', $score_student = NULL) {
			$working_directory = $this->make_test_directory();
			$this->extract_zip_to($this->get_input_zip_file());
			$this->extract_zip_to($this->get_current_test_source_directory() . $this->get_current_test_configuration_value('zip_file'));
			$this->copy_file_to('test_sources/private/java/LISTTestScoring/LISTTestScoring.java', 'LISTTestScoring');
			$sandbox = $this->get_sandbox_type();
			$this->create_encryption_phrase($working_directory);

			$class_to_run = $this->get_current_test_configuration_value('class_to_run');
			if (!preg_match(self::UNIT_TEST_CLASS_TO_RUN_REGEXP, $class_to_run)) {
				set_time_limit(120);
				return $this->CI->lang->line('java_tests_run_error_unit_test_class_not_set');
			}

			$scripts_directory = $this->get_test_scripts_directory();
			$exec_command = $scripts_directory . 'execute_test jUnit ' . $sandbox . ' ' . $class_to_run . ' ' . $this->get_test_timeout() . ' ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR . $working_directory;
			$output_data = array();
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

			$this->delete_test_directory();

			$lines = (int)$this->get_current_test_configuration_value('max_output_lines');

			return $this->encode_output($this->truncate_lines($output, $lines));
		}

		protected function save_unit_test_config($new_config) {
			$old_config = $this->get_current_test_configuration();
			return array_merge($old_config, $new_config);
		}

		protected function validator_unit_test() {
			$this->CI->form_validation->set_rules('configuration[class_to_run]', 'lang:java_tests_config_validation_unit_test_class_to_run', 'required|regex_match[' . self::UNIT_TEST_CLASS_TO_RUN_REGEXP . ']');
			$this->CI->form_validation->set_rules('configuration[max_output_lines]', 'lang:java_tests_config_validation_unit_test_output_maximum_lines', 'required|integer|greater_than_equal[0]');
			return TRUE;
		}

		protected function uploader_unit_test(&$new_config) {
			$valid = TRUE;
			if ($this->was_file_sent('zip_file')) {
				$data = $this->upload_file('unit_test', 'zip_file', 'zip', array(
					'overwrite' => TRUE,
					'file_name' => 'unit_test.zip',
				), TRUE, 'java', 'unit_test.zip');
				if ($data === FALSE) {
					$valid = FALSE;
				} else {
					$new_config['zip_file'] = 'unit_test/' . $data['file_name'];
				}
			}
			return $valid;
		}*/

	}
