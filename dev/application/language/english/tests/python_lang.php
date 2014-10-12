<?php

$lang['tests_python_type_name'] = 'Python';
$lang['python_tests_subtype_unit_test_name'] = 'Unit test';
$lang['python_tests_config_form_unit_test_zip_file'] = 'ZIP file with source code';
$lang['python_tests_config_form_unit_test_zip_file_hint'] = 'You can upload ZIP file, where test file will be in root directory. Optionaly you can submit PY file with tests, which will be ZIPed after upload. Max file size: 2MiB.';
$lang['python_tests_config_form_unit_test_class_to_run'] = 'Test file name';
$lang['python_tests_config_form_unit_test_class_to_run_hint'] = 'For security reason test file which will be run is named as Test<strong>&lt;your test file name&gt;</strong>.py.<br /><strong>Example:</strong> Test<strong>Square</strong>.py';
$lang['python_tests_config_form_unit_test_output_maximum_lines'] = 'Maximum lines of output';
$lang['python_tests_config_form_unit_test_output_maximum_lines_hint'] = 'Set to maximum linex of output from unit test or set to <strong>0</strong> to disable this feature.';
$lang['python_tests_config_validation_unit_test_class_to_run'] = 'test file name';
$lang['python_tests_config_validation_unit_test_output_maximum_lines'] = 'maximum lines of output';
$lang['python_tests_run_error_unit_test_class_not_set'] = 'Test file name is not set.';
$lang['python_tests_subtype_io_test_name'] = 'Input/output test';
$lang['python_tests_config_form_io_test_input_file'] = 'Input text file';
$lang['python_tests_config_form_io_test_input_file_hint'] = 'This is input text file for test. Make sure it have rigth character set and formating.';
$lang['python_tests_config_form_io_test_judge_type'] = 'Judge type';
$lang['python_tests_config_form_io_test_judge_type_diff'] = 'Standard DIFF program with text file containing target solution.';
$lang['python_tests_config_form_io_test_judge_type_script'] = 'Custom python script, which will obtain user generated output on his standard input.';
$lang['python_tests_config_form_io_test_target_file'] = 'Target text file';
$lang['python_tests_config_form_io_test_target_file_hint'] = 'If judge type is set to diff, please provide this text file which will be used as a target solution.';
$lang['python_tests_config_form_io_test_judge_source'] = 'Judge source file';
$lang['python_tests_config_form_io_test_judge_source_hint'] = 'If judge type is set to custom script, please provide this judge source code. It will be renamed do <strong>test_judge.py</strong>, so keep this in mind or give this name to your source code.';