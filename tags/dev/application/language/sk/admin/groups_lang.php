<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Slovak language

$lang['admin_groups_page_title'] = 'Skupiny';
$lang['admin_groups_form_label_group_name'] = 'Názov skupiny';
$lang['admin_groups_form_label_group_course'] = 'Kurz';
$lang['admin_groups_form_button_save'] = 'Uložiť';
$lang['admin_groups_form_field_group_name'] = 'názov skupiny';
$lang['admin_groups_form_field_group_course'] = 'kurz';
$lang['admin_groups_flash_message_save_successful'] = 'Skupina úspešne uložená.';
$lang['admin_groups_flash_message_save_failed'] = 'Skupina nebola uložená. Skúste to znova.';
$lang['admin_groups_table_content_no_rooms_message'] = 'Táto skupina nemá žiadne miestnosti.';
$lang['admin_groups_table_header_group_name'] = 'Názov skupiny';
$lang['admin_groups_table_header_group_course'] = 'Kurz';
$lang['admin_groups_table_header_group_rooms'] = 'Miestnosti';
$lang['admin_groups_table_header_group_capacity'] = 'Kapacita skupiny';
$lang['admin_groups_table_header_controlls'] = 'Ovládanie';
$lang['admin_groups_table_controlls_group_mail'] = 'Poslať&nbsp;správu';
$lang['admin_groups_table_controlls_rooms'] = 'Miestnosti';
$lang['admin_groups_table_controlls_edit'] = 'Upraviť';
$lang['admin_groups_table_controlls_delete'] = 'Vymazať';
$lang['admin_groups_fieldset_legend_new_group'] = 'Nová skupina';
$lang['admin_groups_fieldset_legend_all_groups'] = 'Všetky skupiny';
$lang['admin_groups_filter_by_course'] = 'Filtrovať podla kurzu';
$lang['admin_groups_filter_submit_button'] = 'Použiť';
$lang['admin_groups_error_no_such_group_message'] = 'Nie je databázový záznam s daným ID.';
$lang['admin_groups_delete_period_question'] = 'Ste si istý vymazaním tejto skupiny? Všetky relácie študentou k tejto skupine budú stratené, rovnako ako busú vymazané všetky miestnosti tejto skupiny. Pokračovať?';
$lang['admin_groups_message_after_delete'] = 'Skupina bola úspešne vymazaná.';

$lang['admin_groups_group_email_page_title'] = 'Poslať správu členom skupiny';
$lang['admin_groups_group_email_form_label_subject'] = 'Subjekt';
$lang['admin_groups_group_email_form_label_body'] = 'Telo';
$lang['admin_groups_group_email_form_label_from'] = 'Od';
$lang['admin_groups_group_email_form_label_students'] = 'Pre študentov';
$lang['admin_groups_group_email_form_submit_button'] = 'Poslať e-mail';
$lang['admin_groups_group_email_form_field_subject'] = 'subjekt';
$lang['admin_groups_group_email_form_field_body'] = 'telo';
$lang['admin_groups_group_email_form_field_from'] = 'od';
$lang['admin_groups_group_email_form_field_students'] = 'pre študentov';
$lang['admin_groups_group_email_form_label_sender_copy'] = 'Poslať kópiu správy ja sebe.';
$lang['admin_groups_group_email_button_select_all_students'] = 'Vybrať všetkých študentov';
$lang['admin_groups_group_email_error_group_is_empty'] = 'Táto skupina je prázdna, nemožno poslať e-mail.';
$lang['admin_groups_group_email_error_group_not_found'] = 'Skupina sa nenašla.';
$lang['admin_groups_group_email_error_no_students_selected'] = 'Neboli vybraní študenti.';
$lang['admin_groups_group_email_error_send_failed'] = 'Zlyhanie pri posielaní e-mail(ov).';
$lang['admin_groups_group_email_success_sent'] = 'E-mail úspešne odoslaný.';
$lang['admin_groups_group_email_from_system'] = 'Systému';
$lang['admin_groups_group_email_from_me'] = 'Mňa';