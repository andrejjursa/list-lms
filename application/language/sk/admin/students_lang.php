<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Slovak language

$lang['admin_students_page_title'] = 'Správa študentov';
$lang['admin_students_fieldset_legend_new_student_account'] = 'Nový študentský účet';
$lang['admin_students_fieldset_legend_all_students_accounts'] = 'Všetky študentské účty';
$lang['admin_students_form_label_fullname'] = 'Celé meno';
$lang['admin_students_form_label_email'] = 'E-mail';
$lang['admin_students_form_label_password'] = 'Heslo';
$lang['admin_students_form_button_save'] = 'Uložiť';
$lang['admin_students_table_header_fullname'] = 'Celé meno';
$lang['admin_students_table_header_email'] = 'E-mail';
$lang['admin_students_table_header_controlls'] = 'Ovládanie';
$lang['admin_students_table_button_update'] = 'Upraviť';
$lang['admin_students_table_button_delete'] = 'Vymazať';
$lang['admin_students_table_button_login_as'] = 'Prihlásiť&nbsp;sa&nbsp;ako&nbsp;študent';
$lang['admin_students_form_field_fullname'] = 'celé meno';
$lang['admin_students_form_field_email'] = 'e-mail';
$lang['admin_students_form_field_password'] = 'heslo';
$lang['admin_students_account_save_successful'] = 'Študentský účet bol úspešne uložený.';
$lang['admin_students_account_save_fail'] = 'Študentský účet sa nepodarilo uložiť.';
$lang['admin_students_message_delete_question'] = 'Naozaj chcete vymazať tento študentský účet?';
$lang['admin_students_message_after_delete'] = 'Študentský účet bol úspešne vymazaný.';
$lang['admin_students_student_not_found'] = 'Požadovaný študentský účet nebol nájdený.';
$lang['admin_students_form_error_email_not_available'] = 'Pole <strong>%s</strong> musí obsahovať unikátnu hodnotu.';
$lang['admin_students_button_csv_import'] = 'Importovať študentov zo súboru CSV';
$lang['admin_students_filter_form_label_fullname'] = 'Filtrovať podľa celého mena';
$lang['admin_students_filter_form_label_email'] = 'Filtrovať podľa e-mailu';
$lang['admin_students_filter_form_label_course'] = 'Filtrovať podľa kurzu';
$lang['admin_students_filter_form_submit_button'] = 'Použiť';
$lang['admin_students_failed_to_force_login'] = 'Zlyhalo vynútenie študentského prihlásenia.';
$lang['admin_students_csv_import_page_title'] = 'Správa študentov - import z CSV';
$lang['admin_students_csv_import_form_label_file'] = 'CSV súbor';
$lang['admin_students_csv_import_form_label_delimiter'] = 'Oddeľovač polí';
$lang['admin_students_csv_import_form_label_enclosure'] = 'Obal polí';
$lang['admin_students_csv_import_form_label_escape'] = 'Eskejpovací znak';
$lang['admin_students_csv_import_form_submit_button_upload'] = 'Odoslať súbor';
$lang['admin_students_csv_import_form_field_delimiter'] = 'oddeľovač polí';
$lang['admin_students_csv_import_form_field_enclosure'] = 'obal polí';
$lang['admin_students_csv_import_form_field_escape'] = 'eskejpovací znak';
$lang['admin_students_csv_import_error_file_not_exist_or_is_unreadable'] = 'CSV súbor sa nenašieľ alebo je nečitateľný.';
$lang['admin_students_csv_import_error_invalid_cols_config'] = 'Chyba v konfigurácii stĺpcov, prosím vyberte stĺpec pre krstné meno, priezvisko a e-mail alebo celé meno a e-mail.';
$lang['admin_students_csv_import_error_message_nothing_to_import'] = 'Nedá sa nič importovať!';
$lang['admin_students_csv_import_error_message_student_exists'] = 'Študentský účet už existuje!';
$lang['admin_students_csv_import_error_message_student_save_error'] = 'Chyba v db.: študentský účet nebol uložený!';
$lang['admin_students_csv_import_error_message_student_email_invalid'] = 'Študentov e-mail nemá správny formát.';
$lang['admin_students_csv_import_error_message_participation_save_failed'] = 'Nepodarilo sa nastaviť účasť v kurze pre tohoto študenta!';
$lang['admin_students_csv_import_error_message_already_in_course'] = 'Študent už je účastníkom zvoleného kurzu.';
$lang['admin_students_csv_import_error_message_course_not_found'] = 'Zvolený kurz neexistuje.';
$lang['admin_students_csv_import_error_message_added_course_participation_approwal'] = 'Účasť študenta v kurze nemôže byť potvrdená. Kurz je naplnený.';
$lang['admin_students_csv_import_successfully_imported'] = 'Študentský účet úspešne uložený.';
$lang['admin_students_csv_import_successfully_added_course_participation'] = 'Študent bol nastavený ako účastník zvoleného kurzu. Povoľte jeho účasť!';
$lang['admin_students_csv_import_successfully_added_course_participation_approwal'] = 'Účasť študenta v kurze bola úspešne potvrdená.';
$lang['admin_students_csv_import_col_option_no_import'] = 'Neimportovať';
$lang['admin_students_csv_import_col_option_is_firstname'] = 'Toto je krstné meno';
$lang['admin_students_csv_import_col_option_is_lastname'] = 'Toto je priezvisko';
$lang['admin_students_csv_import_col_option_is_fullname'] = 'Toto je celé meno';
$lang['admin_students_csv_import_col_option_is_email'] = 'Toto je e-mail';
$lang['admin_students_csv_import_button_select_all'] = 'Označiť všetko';
$lang['admin_students_csv_import_button_select_none'] = 'Odznačiť všetko';
$lang['admin_students_csv_import_button_submit_do_import'] = 'Spustiť import';
$lang['admin_students_csv_import_log_student_id'] = 'Študentovo ID';
$lang['admin_students_csv_import_log_firstname'] = 'Krstné meno';
$lang['admin_students_csv_import_log_lastname'] = 'Priezvisko';
$lang['admin_students_csv_import_log_fullname'] = 'Celé meno';
$lang['admin_students_csv_import_log_email'] = 'E-mail';
$lang['admin_students_csv_import_log_password'] = 'Heslo';
$lang['admin_students_csv_import_assign_to_course_do_not_assign'] = 'Nepriraďovať študentov do kurzu.';
$lang['admin_students_csv_import_send_mail_checkbox'] = 'Poslať e-mail';
$lang['admin_students_csv_import_password_type_default_password'] = 'Použiť východzie heslo';
$lang['admin_students_csv_import_password_type_random_password'] = 'Použiť náhodné heslo';
$lang['admin_students_csv_import_password_type_blank_password'] = 'Použiť prázdne heslo';
$lang['admin_students_csv_import_email_subject'] = 'Tvoj nový účet';
$lang['admin_students_csv_import_email_body_text1'] = 'Vitajte v LIST-e (Long-term Internet Storage of Tasks)!';
$lang['admin_students_csv_import_email_body_text2'] = 'Administrátor pre teba práve vytvoril nový účet s týmito detailami:';
$lang['admin_students_csv_import_email_body_fullname'] = 'Celé meno';
$lang['admin_students_csv_import_email_body_email'] = 'E-mail (prihlasovacie meno)';
$lang['admin_students_csv_import_email_body_password'] = 'Heslo';
$lang['admin_students_csv_import_email_body_password_empty'] = 'Teraz si musíš vytvoriť svoje nové heslo kliknutím na tento odkaz: %s';
$lang['admin_students_csv_import_email_body_password_link'] = 'Nové heslo';
$lang['admin_students_csv_import_email_body_text3'] = 'Do systému sa môžeš prihlásiť kliknutím na tento odkaz: %s';
$lang['admin_students_csv_import_email_sent_successfully'] = 'E-mail bol odoslaný!';
$lang['admin_students_csv_import_email_sent_failed'] = 'E-mail nebol odoslaný kvôli chybe!';