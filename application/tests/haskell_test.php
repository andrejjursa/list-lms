<?php

	include_once APPPATH . 'core/abstract_test.php';

	/**
	 * Java test instance.
	 * @package LIST_Tests
	 * @author  Andrej Jursa
	 */
	class haskell_test extends abstract_test {

		const UNIT_TEST_CLASS_TO_RUN_REGEXP = '/^[a-zA-Z]{1,}[\_a-zA-Z0-9]*$/';

		protected $test_subtypes = array(
			'unit_test' => array(
				'name'                  => 'lang:haskell_tests_subtype_unit_test_name',
				'method'                => 'run_unit_test',
				'configure_view'        => 'tests/haskell/configure.tpl',
				'configure_before_save' => 'save_unit_test_config',
				'configure_validator'   => 'validator_unit_test',
				'configure_uploader'    => 'uploader_unit_test',
			),
		);

		public function get_test_type_name() {
			return $this->CI->lang->line('tests_haskell_type_name');
		}

		protected function run_unit_test($save_score = FALSE, $score_token = '', $score_student = NULL) {
			$working_directory = $this->make_test_directory();
			$this->extract_zip_to($this->get_input_zip_file());
			$this->extract_zip_to($this->get_current_test_source_directory() . $this->get_current_test_configuration_value('zip_file'));

			$class_to_run = $this->get_current_test_configuration_value('file_to_run');
			if (!preg_match(self::UNIT_TEST_CLASS_TO_RUN_REGEXP, $class_to_run)) {
				set_time_limit(120);

				return $this->CI->lang->line('haskell_tests_run_error_unit_test_file_not_set');
			}

			$scripts_directory = $this->get_test_scripts_directory();
			$sandbox = $this->get_sandbox_type();
			$exec_command      = $scripts_directory . 'execute_test hUnit ' . $sandbox . ' Test' . $class_to_run . '.hs ' . $this->get_test_timeout() . ' ' . rtrim(getcwd(), '\\/') . DIRECTORY_SEPARATOR . $working_directory;
			$output_data       = array();
			$exit_code         = 0;
			@exec($exec_command, $output_data, $exit_code);
			$output  = $this->read_output_file(self::TEST_OUTPUT_FILE);
			$score_file = $this->read_output_file(self::TEST_SCORING_FILE);
			$scoring = (double)$this->get_current_test_configuration_value('scoring_percents');
			$this->set_last_exit_code($exit_code);

			if ((int)$exit_code == 0 && $this->unit_test_analyze_score_file($score_file)) {
				$this->construct_io_test_result($scoring, $scoring, 'lang:tasks_test_result_score_name_general_test');
			} else {
				$this->construct_io_test_result(0, $scoring, 'lang:tasks_test_result_score_name_general_test');
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
			$this->CI->form_validation->set_rules('configuration[file_to_run]', 'lang:haskell_tests_config_validation_unit_test_file_to_run', 'required|regex_match[' . self::UNIT_TEST_CLASS_TO_RUN_REGEXP . ']');
			$this->CI->form_validation->set_rules('configuration[max_output_lines]', 'lang:haskell_tests_config_validation_unit_test_output_maximum_lines', 'required|integer|greater_than_equal[0]');
			$this->CI->form_validation->set_rules('configuration[scoring_percents]', 'lang:haskell_tests_config_validation_unit_test_scoring_percents', 'required|number|greater_than_equal[0]|less_than_equal[100]');

			return TRUE;
		}

		protected function uploader_unit_test(&$new_config) {
			// TODO: otestovat, ci je upload spravne kontrolovany
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

		protected function unit_test_analyze_score_file($file_content) {
			$file_content = str_replace("\r", "\n", $file_content);
			$lines = explode("\n", $file_content);

			$found = FALSE;

			if (count($lines)) {
				foreach ($lines as $line) {
					$matches = array();
					if (preg_match('/^Cases: (?P<cases>[0-9]+) +Tried: (?P<tried>[0-9]+) +Errors: (?P<errors>[0-9]+) +Failures: (?P<failures>[0-9]+)/i', trim($line), $matches)) {
						$found = TRUE;
						if ((int)$matches['errors'] > 0 || (int)$matches['failures'] > 0) {
							return FALSE;
						}
					}
				}

			}

			return $found;
		}

	}
