{include file='partials/tests_general/file_input.tpl' label_lang='python_tests_config_form_unit_test_zip_file' field_name='zip_file' hint_lang='python_tests_config_form_unit_test_zip_file_hint' inline}
{include file='partials/tests_general/textline_input.tpl' 
label_lang='python_tests_config_form_unit_test_class_to_run'
label_class='required'
hint_lang='python_tests_config_form_unit_test_class_to_run_hint'
field_name='class_to_run'
inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='python_tests_config_form_unit_test_output_maximum_lines'
label_class='required'
hint_lang='python_tests_config_form_unit_test_output_maximum_lines_hint'
field_name='max_output_lines'
default_text='0'
inline}