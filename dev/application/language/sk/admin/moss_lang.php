<?php

$lang['admin_moss_page_title'] = 'Porovnávač MOSS';

$lang['admin_moss_fieldset_legend_task_set'] = 'Výber zostavy';
$lang['admin_moss_fieldset_legend_protocol'] = 'Protokol porovnávania / nastavenie';

$lang['admin_moss_task_set_form_label_course'] = 'Kurz';
$lang['admin_moss_task_set_form_label_task_set'] = 'Zostava úloh';
$lang['admin_moss_task_set_form_label_task_set_else'] = 'Najprv vyberte kurz prosím.';
$lang['admin_moss_task_set_form_button_submit'] = 'Zobraziť riešenia';

$lang['admin_moss_list_solutions_table_header_student_name'] = 'Meno študenta';
$lang['admin_moss_list_solutions_table_header_solution_version'] = 'Verzia riešenia';
$lang['admin_moss_list_solutions_table_body_no_files'] = 'Žiadne súbory.';

$lang['admin_moss_list_solutions_error_course_task_set'] = 'Kurz alebo zostava úloh sa v databáze nenašla. Skontrolujte nastavenie.';
$lang['admin_moss_list_solutions_error_no_solutions'] = 'Zatiaľ tu nie sú žiadne odoslané riešenia pre túto zostavu úloh.';

$lang['admin_moss_list_base_files_table_header_base_file_name'] = 'Základný súbor';
$lang['admin_moss_base_files_table_body_no_files_for_task'] = 'Neexistujú žiadne základné súbory pre túto úlohu.';

$lang['admin_moss_list_solutions_form_label_language'] = 'Jazyk';
$lang['admin_moss_list_solutions_form_label_sensitivity'] = 'Citlivosť MOSS-u';
$lang['admin_moss_list_solutions_form_label_sensitivity_hint'] = 'Ak nejaký prechod kódom bude rovnaký vo viacerých programoch a jeho počet výskytov bude vyšší ako táto hodnota, bude tento prechod kódom považovaný za legitímny (ako keby sa nachádzal aj v niektorom zo základných súborov).';
$lang['admin_moss_list_solutions_form_label_matching_files'] = 'Počet súborov vo výsledkoch';
$lang['admin_moss_list_solutions_form_label_matching_files_hint'] = 'Táto hodnota určuje počet porovnávaných súborov vo výsledku.';
$lang['admin_moss_list_solutions_form_button_submit'] = 'Porovnať';

$lang['admin_moss_list_solutions_form_field_solution_selection'] = 'riešenie';
$lang['admin_moss_list_solutions_form_field_language'] = 'jazyk';
$lang['admin_moss_list_solutions_form_field_sensitivity'] = 'citlivosť MOSS-u';
$lang['admin_moss_list_solutions_form_field_matching_files'] = 'počet súborov vo výsledkoch';
$lang['admin_moss_list_solutions_validation_callback_selected_solutions'] = 'Aspoň jedno <strong>%s</strong> musí byť vybrané.';

$lang['admin_moss_run_comparation_fieldset_legend_run'] = 'Výsledky porovnania';
$lang['admin_moss_run_comparation_please_stand_by_message'] = 'Posielam požiadavku na porovnanie na MOSS, počkajte na výsledky.';
$lang['admin_moss_run_comparation_error_files_not_exracted'] = 'Nie všetky súbory boli rozbalené / skopírované úspešne. Porovnávanie nemôže začať.';

$lang['admin_moss_execute_results_button_text'] = 'Zobraziť výsledky';

$lang['admin_moss_general_error_user_id_not_set'] = 'Id používateľa MOSS-u nie je zadané. Je nutné zadať id používateľa v nastaveniach L.I.S.T.-u aby sa povolil tento modul.';