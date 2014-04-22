<?php

$lang['admin_comparator_page_title'] = 'Java comparator';

$lang['admin_comparator_fieldset_legend_task_set'] = 'Task set selection';
$lang['admin_comparator_fieldset_legend_protocol'] = 'Comparation protocol / configuration';

$lang['admin_comparator_task_set_form_label_course'] = 'Course';
$lang['admin_comparator_task_set_form_label_task_set'] = 'Task set';
$lang['admin_comparator_task_set_form_label_task_set_else'] = 'Please, select course first.';
$lang['admin_comparator_task_set_form_button_submit'] = 'Display solutions';

$lang['admin_comparator_list_solutions_form_label_threshold'] = 'Threshold';
$lang['admin_comparator_list_solutions_form_label_threshold_hint'] = 'Threshold of similarity of two cloned source codes, between 0 and 1.';
$lang['admin_comparator_list_solutions_form_label_min_tree_size'] = 'Minimum tree size';
$lang['admin_comparator_list_solutions_form_label_min_tree_size_hint'] = 'The size of smallest acceptable tree (sequence), number of statements.';
$lang['admin_comparator_list_solutions_form_label_max_cutted_tree_size'] = 'Maximum cutted tree size';
$lang['admin_comparator_list_solutions_form_label_max_cutted_tree_size_hint'] = 'The size of biggest accepted tree.';
$lang['admin_comparator_list_solutions_form_label_branching_factor'] = 'Branching factor';
$lang['admin_comparator_list_solutions_form_label_branching_factor_hint'] = 'How mutch branches can the tree have.';
$lang['admin_comparator_list_solutions_form_label_minimum_similarity'] = 'Minimum similarity';
$lang['admin_comparator_list_solutions_form_label_minimum_similarity_hint'] = 'Minimum similarity of two source codes to be added into list of similar source codes.';
$lang['admin_comparator_list_solutions_form_button_submit'] = 'Compare';

$lang['admin_comparator_list_solutions_table_header_student_name'] = 'Student name';
$lang['admin_comparator_list_solutions_table_header_solution_version'] = 'Solution version';
$lang['admin_comparator_list_solutions_table_body_no_files'] = 'No files.';

$lang['admin_comparator_list_solutions_form_field_solution_selection'] = 'solution';
$lang['admin_comparator_list_solutions_form_field_threshold'] = 'threshold';
$lang['admin_comparator_list_solutions_form_field_min_tree_size'] = 'minimum tree size';
$lang['admin_comparator_list_solutions_form_field_max_cutted_tree_size'] = 'maximum cutted tree size';
$lang['admin_comparator_list_solutions_form_field_branching_factor'] = 'branching factor';
$lang['admin_comparator_list_solutions_form_field_minimum_similarity'] = 'minimum similarity';
$lang['admin_comparator_list_solutions_validation_callback_selected_solutions'] = 'At least one <strong>%s</strong> must be selected.';

$lang['admin_comparator_list_solutions_error_course_task_set'] = 'Course or task set is not found in database. Check your settings.';
$lang['admin_comparator_list_solutions_error_no_solutions'] = 'There are no solutions submitted to this task set yet.';

$lang['admin_comparator_run_comparation_fieldset_legend_run'] = 'Comparation protocol';
$lang['admin_comparator_run_comparation_please_stand_by_message'] = 'Comparation process is being run, please stand by.';
$lang['admin_comparator_run_comparation_error_files_not_exracted'] = 'Source code extraction error. Not all student solutions were found and extracted.';

$lang['admin_comparator_execute_button_open_report'] = 'Open comparation report';