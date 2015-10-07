{include file='partials/tests_general/file_input.tpl' label_lang='cpp_tests_config_form_simple_test_zip_file' field_name='zip_file' hint_lang='cpp_tests_config_form_simple_test_zip_file_hint' inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='cpp_tests_config_form_simple_test_file_to_run'
label_class='required'
hint_lang='cpp_tests_config_form_simple_test_file_to_run_hint'
field_name='file_to_run'
inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='cpp_tests_config_form_simple_test_output_maximum_lines'
label_class='required'
hint_lang='cpp_tests_config_form_simple_test_output_maximum_lines_hint'
field_name='max_output_lines'
default_text='0'
inline}
{include file='partials/tests_general/textline_input.tpl'
label_lang='cpp_tests_config_form_simple_test_scoring_percents'
label_class='required'
hint_lang='cpp_tests_config_form_simple_test_scoring_percents_hint'
field_name='scoring_percents'
default_text='100'
inline}
{*<div class="field">
    <label>{translate line="java_tests_config_form_scoring_class_label"}:</label>
    <p class="input"><a href="{'/test_sources/java/LISTTestScoring.zip'|base_url}">{translate line="java_tests_config_form_scoring_class_zip"}</a></p>
    <p class="input"><em>{translate line="java_tests_config_form_scoring_class_zip_hint"}</em></p>
</div>
*}