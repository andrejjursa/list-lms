{include file='partials/tests_general/file_input.tpl' label_lang='python_tests_config_form_io_test_input_file' field_name='input_file' hint_lang='python_tests_config_form_io_test_input_file_hint' inline}
{include file='partials/tests_general/select_input.tpl'
    label_lang='python_tests_config_form_io_test_judge_type'
    field_name='judge_type'
    select_options=['diff' => 'lang:python_tests_config_form_io_test_judge_type_diff', 'script' => 'lang:python_tests_config_form_io_test_judge_type_script']
    default_option='diff'
    select_size=1 
}
{include file='partials/tests_general/file_input.tpl' label_lang='python_tests_config_form_io_test_target_file' field_name='target_file' hint_lang='python_tests_config_form_io_test_target_file_hint' inline}
{include file='partials/tests_general/file_input.tpl' label_lang='python_tests_config_form_io_test_judge_source' field_name='judge_source' hint_lang='python_tests_config_form_io_test_judge_source_hint' inline}