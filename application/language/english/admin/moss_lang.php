<?php 

$lang['admin_moss_page_title'] = 'MOSS comparator (deprecated)';

$lang['admin_moss_fieldset_legend_task_set'] = 'Task set selection';
$lang['admin_moss_fieldset_legend_protocol'] = 'Comparation protocol / configuration';

$lang['admin_moss_task_set_form_label_course'] = 'Course';
$lang['admin_moss_task_set_form_label_task_set'] = 'Task set';
$lang['admin_moss_task_set_form_label_task_set_else'] = 'Please, select course first.';
$lang['admin_moss_task_set_form_button_submit'] = 'Display solutions';

$lang['admin_moss_list_solutions_table_header_student_name'] = 'Student name';
$lang['admin_moss_list_solutions_table_header_solution_version'] = 'Solution version';
$lang['admin_moss_list_solutions_table_body_no_files'] = 'No files.';

$lang['admin_moss_list_solutions_error_course_task_set'] = 'Course or task set is not found in database. Check your settings.';
$lang['admin_moss_list_solutions_error_no_solutions'] = 'There are no solutions submitted to this task set yet.';

$lang['admin_moss_list_base_files_table_header_base_file_name'] = 'Base file';
$lang['admin_moss_base_files_table_body_no_files_for_task'] = 'There are no possible base files for this task.';

$lang['admin_moss_list_solutions_form_label_language'] = 'Language';
$lang['admin_moss_list_solutions_form_label_sensitivity'] = 'MOSS Sensitivity';
$lang['admin_moss_list_solutions_form_label_sensitivity_hint'] = 'When some passage of code appears in many programs and the number of appearence is greater than this value, the code passage will be consideret as legitimate (just like it appear in one of base files).';
$lang['admin_moss_list_solutions_form_label_matching_files'] = 'Number of files in results';
$lang['admin_moss_list_solutions_form_label_matching_files_hint'] = 'This value determines the number of matching files to show in the results.';
$lang['admin_moss_list_solutions_form_button_submit'] = 'Compare';

$lang['admin_moss_list_solutions_form_field_solution_selection'] = 'solution';
$lang['admin_moss_list_solutions_form_field_language'] = 'language';
$lang['admin_moss_list_solutions_form_field_sensitivity'] = 'MOSS sensitivity';
$lang['admin_moss_list_solutions_form_field_matching_files'] = 'number of files in results';
$lang['admin_moss_list_solutions_validation_callback_selected_solutions'] = 'At least one <strong>%s</strong> must be selected.';

$lang['admin_moss_run_comparation_fieldset_legend_run'] = 'Comparation results';
$lang['admin_moss_run_comparation_please_stand_by_message'] = 'Sending request for comparation to MOSS, stand by for results.';
$lang['admin_moss_run_comparation_error_files_not_exracted'] = 'Not all files were extracted / copied successfully. Comparation can\'t be executed.';

$lang['admin_moss_execute_results_button_text'] = 'Display results';

$lang['admin_moss_general_error_user_id_not_set'] = 'MOSS user id is not set. You need to set user id in L.I.S.T. setting to enable this module.';