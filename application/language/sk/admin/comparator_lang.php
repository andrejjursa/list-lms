<?php

$lang['admin_comparator_page_title'] = 'Porovnávač Java';

$lang['admin_comparator_fieldset_legend_task_set'] = 'Výber zostavy';
$lang['admin_comparator_fieldset_legend_protocol'] = 'Protokol porovnávania / nastavenie';

$lang['admin_comparator_task_set_form_label_course'] = 'Kurz';
$lang['admin_comparator_task_set_form_label_task_set'] = 'Zostava úloh';
$lang['admin_comparator_task_set_form_label_task_set_else'] = 'Najprv vyberte kurz prosím.';
$lang['admin_comparator_task_set_form_button_submit'] = 'Zobraziť riešenia';

$lang['admin_comparator_list_solutions_form_label_threshold'] = 'Medza';
$lang['admin_comparator_list_solutions_form_label_threshold_hint'] = 'Medza podobnosti dvoch klonov zdrojových kódov, medzi 0 a 1.';
$lang['admin_comparator_list_solutions_form_label_min_tree_size'] = 'Minimálna veľkosť stromu';
$lang['admin_comparator_list_solutions_form_label_min_tree_size_hint'] = 'Najmenší akceptovateľný strom (sekvencia), počet statement-ov.';
$lang['admin_comparator_list_solutions_form_label_max_cutted_tree_size'] = 'Maximálna orezaná veľkosť stromu';
$lang['admin_comparator_list_solutions_form_label_max_cutted_tree_size_hint'] = 'Najväčšia veľkosť akceptovaného stromu.';
$lang['admin_comparator_list_solutions_form_label_branching_factor'] = 'Faktor vetvenia';
$lang['admin_comparator_list_solutions_form_label_branching_factor_hint'] = 'Ako veľmi sa môže strom vetviť.';
$lang['admin_comparator_list_solutions_form_label_minimum_similarity'] = 'Minimálna podobnosť';
$lang['admin_comparator_list_solutions_form_label_minimum_similarity_hint'] = 'Minimálna podobnosť zdrojových kódov na vloženie do zoznamu podobných zdrojových kódov.';
$lang['admin_comparator_list_solutions_form_label_timeout'] = 'Timeout v minútach';
$lang['admin_comparator_list_solutions_form_label_timeout_hint'] = 'Timeout v <strong>minútach</strong>, nastavte tento parameter opatrne, pretože v prípade chyby a nekonečného cyklu v komparátore je toto najlepší spôsob ako prerušiť vykonávanie kódu bez zásahu administrátora serveru.';
$lang['admin_comparator_list_solutions_form_button_submit'] = 'Porovnať';

$lang['admin_comparator_list_solutions_table_header_student_name'] = 'Meno študenta';
$lang['admin_comparator_list_solutions_table_header_solution_version'] = 'Verzia riešenia';
$lang['admin_comparator_list_solutions_table_body_no_files'] = 'Žiadne súbory.';

$lang['admin_comparator_list_solutions_form_field_solution_selection'] = 'riešenie';
$lang['admin_comparator_list_solutions_form_field_threshold'] = 'medza';
$lang['admin_comparator_list_solutions_form_field_min_tree_size'] = 'minimálna veľkosť stromu';
$lang['admin_comparator_list_solutions_form_field_max_cutted_tree_size'] = 'maximálna orezaná veľkosť stromu';
$lang['admin_comparator_list_solutions_form_field_branching_factor'] = 'faktor vetvenia';
$lang['admin_comparator_list_solutions_form_field_minimum_similarity'] = 'minimálna podobnosť';
$lang['admin_comparator_list_solutions_form_field_timeout'] = 'timeout v minútach';
$lang['admin_comparator_list_solutions_validation_callback_selected_solutions'] = 'Aspoň jedno <strong>%s</strong> musí byť vybrané.';

$lang['admin_comparator_list_solutions_error_course_task_set'] = 'Kurz alebo zostava úloh sa v databáze nenašla. Skontrolujte nastavenie.';
$lang['admin_comparator_list_solutions_error_no_solutions'] = 'Zatiaľ tu nie sú žiadne odoslané riešenia pre túto zostavu úloh.';

$lang['admin_comparator_run_comparation_fieldset_legend_run'] = 'Protokol porovnávania';
$lang['admin_comparator_run_comparation_please_stand_by_message'] = 'Proces porovnávania beží, prosím vydržte.';
$lang['admin_comparator_run_comparation_error_files_not_exracted'] = 'Chyba extrakcie zdrojových kódov. Nie všetky študentské riešenia sa našli a rozbalili.';

$lang['admin_comparator_execute_button_open_report'] = 'Otvoriť správu o porovnaní';