{include file='partials/tests_general/file_input.tpl' label_lang='haskell_tests_config_form_unit_test_zip_file' field_name='zip_file' hint_lang='haskell_tests_config_form_unit_test_zip_file_hint' inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='haskell_tests_config_form_unit_test_file_to_run'
label_class='required'
hint_lang='haskell_tests_config_form_unit_test_file_to_run_hint'
field_name='file_to_run'
inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='haskell_tests_config_form_unit_test_output_maximum_lines'
label_class='required'
hint_lang='haskell_tests_config_form_unit_test_output_maximum_lines_hint'
field_name='max_output_lines'
default_text='0'
inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='haskell_tests_config_form_unit_test_scoring_percents'
label_class='required'
hint_lang='haskell_tests_config_form_unit_test_scoring_percents_hint'
field_name='scoring_percents'
default_text='100'
inline}