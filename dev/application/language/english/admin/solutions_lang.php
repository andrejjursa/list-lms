<?php

// English language

$lang['admin_solutions_page_title'] = 'Task sets solutions';
$lang['admin_solutions_fieldset_legend_task_sets'] = 'Task sets';
$lang['admin_solutions_filter_label_course'] = 'Filter by course';
$lang['admin_solutions_filter_label_group'] = 'Filter by group';
$lang['admin_solutions_filter_label_task_set_type'] = 'Filter by type of task set';
$lang['admin_solutions_filter_submit'] = 'Apply';
$lang['admin_solutions_table_header_task_set_name'] = 'Name of task set';
$lang['admin_solutions_table_header_task_set_course'] = 'Course of task set';
$lang['admin_solutions_table_header_task_set_course_group'] = 'Group from course';
$lang['admin_solutions_table_header_task_set_task_set_type'] = 'Type of task set';
$lang['admin_solutions_table_header_task_set_solution_count'] = 'Student solutions count';
$lang['admin_solutions_table_header_task_set_task_count'] = 'Number of tasks in task set';
$lang['admin_solutions_table_header_task_set_task_upload_end_time'] = 'End of solution uploading';
$lang['admin_solutions_table_header_controlls'] = 'Controlls';
$lang['admin_solutions_table_button_select_task_set'] = 'Select&nbsp;task&nbsp;set';
$lang['admin_solutions_table_button_batch_valuation'] = 'Batch&nbsp;valuation';
$lang['admin_solutions_table_button_remove_points'] = 'Remove&nbsp;points';
$lang['admin_solutions_datetime_format'] = 'm/d/Y H:i:s';
$lang['admin_solutions_no_time_information'] = 'No time information.';
$lang['admin_solutions_no_solution_uploading'] = 'Solution uploading is disabled.';
$lang['admin_solutions_group_no_group'] = 'No group';

$lang['admin_solutions_remove_points_error_task_set_upload_limit_not_reached'] = 'Time limit for solution uploading have not been reached yet.';
$lang['admin_solutions_remove_points_error_task_set_upload_not_limited'] = 'This task set does not have set limit for solution uploading.';
$lang['admin_solutions_remove_points_error_unknown'] = 'Points were not removed due to unknown error.';
$lang['admin_solutions_remove_points_error_task_set_solution_uploading_disabled'] = 'Solution uploading is disabled for this task set.';
$lang['admin_solutions_remove_points_form_field_points'] = 'points to remove';
$lang['admin_solutions_remove_points_success'] = 'Successfully removed points for this task set for %s students.';
$lang['admin_solutions_remove_points_dialog_title'] = 'Remove points';
$lang['admin_solutions_remove_points_dialog_message'] = 'If you want to remove points for all student which does not submit solution for this task set, enter points here and press Ok.';
$lang['admin_solutions_remove_points_form_label_points'] = 'Points to remove';
$lang['admin_solutions_remove_points_dialog_ok_button'] = 'Ok';
$lang['admin_solutions_remove_points_dialog_cancel_button'] = 'Cancel';
$lang['admin_solutions_remove_points_notification_subject'] = 'You have lost some points!';
$lang['admin_solutions_remove_points_notification_text'] = 'You have just lost %s points from task %s, where you do not have submited any solution until %s.';

$lang['admin_solutions_list_page_title'] = 'Solutions for task set %s';
$lang['admin_solutions_list_h3_all_groups'] = 'All groups';
$lang['admin_solutions_list_task_set_not_found'] = 'Task set not found.';
$lang['admin_solutions_list_fieldset_legend_add_solution_record'] = 'Add solution record';
$lang['admin_solutions_list_fieldset_legend_all_solutions'] = 'All solutions';
$lang['admin_solutions_list_solution_not_valuated'] = 'Not valuated.';
$lang['admin_solutions_list_table_header_student'] = 'Student';
$lang['admin_solutions_list_table_header_files_count'] = 'Nr. of solutions';
$lang['admin_solutions_list_table_header_valuation'] = 'Valuation';
$lang['admin_solutions_list_table_header_points'] = 'Points';
$lang['admin_solutions_list_table_header_comment'] = 'Comment';
$lang['admin_solutions_list_table_header_teacher'] = 'Teacher';
$lang['admin_solutions_list_table_button_valuate'] = 'Valuate';
$lang['admin_solutions_list_form_label_student'] = 'Student';
$lang['admin_solutions_list_form_label_points'] = 'Points';
$lang['admin_solutions_list_form_label_points_hint'] = 'Sum of points for tasks in this task set is <strong>%s</strong>.';
$lang['admin_solutions_list_form_label_comment'] = 'Comment';
$lang['admin_solutions_list_form_submit_button'] = 'Save';
$lang['admin_solutions_list_form_field_student'] = 'student';
$lang['admin_solutions_list_form_field_points'] = 'points';
$lang['admin_solutions_list_new_solution_created'] = 'Solution record created successfully.';
$lang['admin_solutions_list_new_solution_error_solution_exists'] = 'Solution records already existed for this student and task set.';
$lang['admin_solutions_list_new_solution_error_student_not_in_course_or_group'] = 'Selected student does not belongs to this course or group attached to this task set.';

$lang['admin_solutions_valuation_tabs_label_form'] = 'Form';
$lang['admin_solutions_valuation_tabs_label_files'] = 'Files';
$lang['admin_solutions_valuation_tabs_label_tasks'] = 'Tasks';
$lang['admin_solutions_valuation_form_label_points'] = 'Points';
$lang['admin_solutions_valuation_form_label_points_hint'] = 'Sum of points for tasks in this task set is <strong>%s</strong>.';
$lang['admin_solutions_valuation_form_label_comment'] = 'Comment';
$lang['admin_solutions_valuation_form_label_not_considered'] = 'Do not consider this solution in valuation';
$lang['admin_solutions_valuation_form_button_submit'] = 'Save';
$lang['admin_solutions_valuation_form_field_points'] = 'points';
$lang['admin_solutions_validation_filter_label_version'] = 'Solution version';
$lang['admin_solutions_validation_filter_button_download_file'] = 'Download this solution version';
$lang['admin_solutions_validation_filter_label_file'] = 'Select file in solution';
$lang['admin_solutions_validation_filter_button_read_file'] = 'Show content of file';
$lang['admin_solutions_valuation_file_content_error_task_set_not_found'] = 'Can\'t read file content because task set was not found.';
$lang['admin_solutions_valuation_file_content_error_cant_read_file'] = 'Can\'t read this type of file.';
$lang['admin_solutions_valuation_solution_not_found'] = 'Solution not found.';
$lang['admin_solutions_valuation_solution_saved'] = 'Solution saved successfully.';
$lang['admin_solutions_valuation_solution_not_saved'] = 'Failed to save solution.';

$lang['admin_solutions_tasks_list_instructions_header'] = 'Instructions for student';
$lang['admin_solutions_tasks_list_task_unknown_author'] = 'Unknown author';
$lang['admin_solutions_task_list_is_bonus_task'] = 'Bonus task';

$lang['admin_solutions_batch_valuation_page_title'] = 'Batch valuation';
$lang['admin_solutions_batch_valuation_fieldset_legend_table'] = 'Valuation table';
$lang['admin_solutions_batch_valuation_fieldset_legend_task_set_content'] = 'Task set content';
$lang['admin_solutions_batch_valuation_table_header_student_fullname'] = 'Student name';
$lang['admin_solutions_batch_valuation_table_header_student_email'] = 'Student e-mail';
$lang['admin_solutions_batch_valuation_table_header_solution_points'] = 'Points';
$lang['admin_solutions_batch_valuation_table_header_solution_not_considered'] = 'Not considered in valuation';
$lang['admin_solutions_batch_valuation_table_label_do_not_consider_this'] = 'Do not consider this solution in valuation';
$lang['admin_solutions_batch_valuation_form_submit_batch_save'] = 'Batch save';
$lang['admin_solutions_batch_valuation_success_message_save_ok'] = 'Batch saved successfully.';
$lang['admin_solutions_batch_valuation_error_message_save_failed'] = 'Batch save failed, nothing were saved.';

$lang['admin_solutions_valuation_tables_page_title'] = 'Valuation tables';
$lang['admin_solutions_valuation_tables_filter_label_course'] = 'Course';
$lang['admin_solutions_valuation_tables_filter_label_group'] = 'Group';
$lang['admin_solutions_valuation_tables_filter_label_simple'] = 'Simplified table';
$lang['admin_solutions_valuation_tables_error_no_course_selected'] = 'You do not have selected course. Please, select one from filter.';
$lang['admin_solutions_valuation_tables_table_header_student'] = 'Student';
$lang['admin_solutions_valuation_tables_table_header_total'] = 'Sum';
$lang['admin_solutions_valuation_tables_table_header_for_all_groups'] = 'For&nbsp;all&nbsp;groups';
$lang['admin_solutions_valuation_tables_solution_not_valuated'] = 'Not valuated';
$lang['admin_solutions_valuation_tables_solution_not_submited'] = 'Not submited';
$lang['admin_solutions_valuation_tables_solution_not_this_group'] = 'Not member';
$lang['admin_solutions_valuation_tables_solution_not_considered'] = 'Not consideret<br />in valuation';