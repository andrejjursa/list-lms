<?php

$lang['tests_java_type_name'] = 'Java';
$lang['java_tests_subtype_unit_test_name'] = 'Unit test';
$lang['java_tests_config_form_unit_test_zip_file'] = 'ZIP file with source code';
$lang['java_tests_config_form_unit_test_zip_file_hint'] = 'You can upload ZIP file, where test file will be in root directory. Optionaly you can submit JAVA file with tests, which will be ZIPed after upload. Max file size: 2MiB.';
$lang['java_tests_config_form_unit_test_class_to_run'] = 'Test file name';
$lang['java_tests_config_form_unit_test_class_to_run_hint'] = 'For security reason test file which will be run is named as Test<strong>&lt;your test file name&gt;</strong>.java.<br /><strong>Example:</strong> Test<strong>Square</strong>.java';
$lang['java_tests_config_form_unit_test_output_maximum_lines'] = 'Maximum lines of output';
$lang['java_tests_config_form_unit_test_output_maximum_lines_hint'] = 'Set to maximum linex of output from unit test or set to <strong>0</strong> to disable this feature.';
$lang['java_tests_config_validation_unit_test_class_to_run'] = 'test file name';
$lang['java_tests_config_validation_unit_test_output_maximum_lines'] = 'maximum lines of output';
$lang['java_tests_run_error_unit_test_class_not_set'] = 'Test file name is not set.';
$lang['java_tests_config_form_scoring_class_label'] = 'Download evaluation package';
$lang['java_tests_config_form_scoring_class_zip'] = 'Click here to download evaluation package.';
$lang['java_tests_config_form_scoring_class_zip_hint'] = 'Extract this package to the directory where your jUnit test class is located. Use <strong>import LISTTestScoring.LISTTestScoring;</strong> to import this package\'s class into your code. Then make <strong>private static LISTTestScoring</strong> variable in your jUnit class. To instantiate it make <strong>public static void</strong> method without arguments, anotated with <strong>@BeforeClass</strong> and instantiate this package\'s class here. Do not instantiate this class elsewhere!';