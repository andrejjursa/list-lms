<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Slovak language

$lang['admin_settings_page_title'] = 'Nastavenia aplikácie';
$lang['admin_settings_form_label_language'] = 'Jazyk';
$lang['admin_settings_form_label_rewrite_engine_enabled'] = 'Povoliť rewrite engine';
$lang['admin_settings_form_label_url_suffix'] = 'URL suffixová prípona';
$lang['admin_settings_form_label_teacher_login_security_timeout'] = 'Bezpečnostný timeout pre učiteľský login (v minútach)';
$lang['admin_settings_form_label_student_login_security_timeout'] = 'Bezpečnostný timeout pre študentský login (v minútach)';
$lang['admin_settings_form_label_teacher_login_security_allowed_attempts'] = 'Maximálny počet zlyhaných pokusov o prihlásenie pre učiteľa';
$lang['admin_settings_form_label_student_login_security_allowed_attempts'] = 'Maximálny počet zlyhaných pokusov o prihlásenie pre študenta';
$lang['admin_settings_form_label_maximum_solition_filesize'] = 'Maximálna velkosť súboru pre upload riešení';
$lang['admin_settings_form_label_maximum_solition_filesize_hint'] = 'Hodnota je v KiB, 1 KiB = 1024 bajtov, 1024 KiB = 1 MiB.';
$lang['admin_settings_form_label_readable_file_extensions'] = 'Čitateľné súbory';
$lang['admin_settings_form_label_readable_file_extensions_hint'] = 'Čiarkou oddelovaný zoznam prípon súborov, ktoré sú čitateľné (napr. v hodnotení študentských riešení).';
$lang['admin_settings_form_label_student_registration_enabled'] = 'Povoliť funkciu registrácie študentov';
$lang['admin_settings_form_label_student_mail_change'] = 'Povoliť študentom funkciu zmeny e-mailu';
$lang['admin_settings_form_label_email_protocol'] = 'E-mailový protokol';
$lang['admin_settings_form_label_email_mailpath'] = '[Sendmail] Cesta k aplikácii sendmail';
$lang['admin_settings_form_label_email_smtp_host'] = '[SMTP] SMTP hostiteľ';
$lang['admin_settings_form_label_email_smtp_user'] = '[SMTP] SMTP používateľ';
$lang['admin_settings_form_label_email_smtp_pass'] = '[SMTP] SMTP heslo';
$lang['admin_settings_form_label_email_smtp_port'] = '[SMTP] SMTP port';
$lang['admin_settings_form_label_email_smtp_timeout'] = '[SMTP] SMTP timeout (v sekundách)';
$lang['admin_settings_form_label_email_priority'] = 'Východzia priorita';
$lang['admin_settings_form_label_email_multirecipient_batch_mode'] = 'Obsluha odosielania viacerým adresátom';
$lang['admin_settings_form_label_smarty'] = 'Smarty template engine';
$lang['admin_settings_form_label_moss_user_id'] = 'Id používateľa MOSS-u';
$lang['admin_settings_form_label_moss_user_id_hint'] = 'Vložte id používateľa MOSS-u na povolenie modulu porovnávača MOSS-u. Id používateľa MOSS-u môžete získať na http://theory.stanford.edu/~aiken/moss/';
$lang['admin_settings_form_label_test_aging_ticks_to_priority_increase'] = 'Počet tikov na zvýšenie priority';
$lang['admin_settings_form_label_test_aging_max_tests_to_raise_priority'] = 'Maximálny počet testov na zvýšenie priority';
$lang['admin_settings_form_label_test_maximum_enqueued_pe_student'] = 'Maximum testov povolených zaradiť jednému študentovi';
$lang['admin_settings_form_label_test_sandbox'] = 'Mód sandbox-u pre testy';
$lang['admin_settings_form_test_sanbox_implicit'] = 'Implicitný sandbox (neodporúča sa!)';
$lang['admin_settings_form_test_sandbox_docker'] = 'Docker sandbox (odporúča sa!)';
$lang['admin_settings_form_email_protocol_mail'] = 'PHP funkcia mail() [mail]';
$lang['admin_settings_form_email_protocol_sendmail'] = 'Sendmail [sendmail]';
$lang['admin_settings_form_email_protocol_smtp'] = 'Jednoduchy protokol na prenos pošty [SMTP]';
$lang['admin_settings_form_email_priority_1'] = 'Najniššia priorita';
$lang['admin_settings_form_email_priority_2'] = 'Nízka priorita';
$lang['admin_settings_form_email_priority_3'] = 'Stredná priorita';
$lang['admin_settings_form_email_priority_4'] = 'Vysoká priorita';
$lang['admin_settings_form_email_priority_5'] = 'Najvyššia priorita';
$lang['admin_settings_form_save_button_text'] = 'Uložiť nastavenia';
$lang['admin_settings_form_clear_all_cache_button'] = 'Vymazať vyrovnávaciu pamäť [%s záznamov]';
$lang['admin_settings_form_clear_all_compiled_button'] = 'Vymazať skompilované šablóny [%s súborov]';
$lang['admin_settings_form_email_multirecipient_batch_mode_false'] = 'Poslať jeden e-mail samostatne pre každého adresáta';
$lang['admin_settings_form_email_multirecipient_batch_mode_true'] = 'Poslať jeden e-mail celému zoznamu adresátov (každý adresát bude vidieť e-mailovú adresu ostatných adresátov v zozname)';
$lang['admin_settings_form_rewrite_option_true'] = 'Áno';
$lang['admin_settings_form_rewrite_option_false'] = 'Nie';
$lang['admin_settings_form_field_email_protocol'] = 'e-mailový protokol';
$lang['admin_settings_form_field_email_priority'] = 'východzia priorita';
$lang['admin_settings_form_field_email_smtp_port'] = '[SMTP] SMTP port';
$lang['admin_settings_form_field_email_smtp_timeout'] = '[SMTP] SMTP timeout (v sekundách)';
$lang['admin_settings_form_field_language'] = 'jazyk';
$lang['admin_settings_form_field_rewrite_engine_enabled'] = 'povoliť rewrite engine';
$lang['admin_settings_form_field_url_suffix'] = 'url suffixová prípona';
$lang['admin_settings_form_field_teacher_login_security_timeout'] = 'bezpečnostný timeout pre učiteľský login (v minútach)';
$lang['admin_settings_form_field_student_login_security_timeout'] = 'bezpečnostný timeout pre študentský login (v minútach)';
$lang['admin_settings_form_field_teacher_login_security_allowed_attempts'] = 'maximálny počet zlyhaných pokusov o prihlásenie pre učiteľa';
$lang['admin_settings_form_field_student_login_security_allowed_attempts'] = 'maximálny počet zlyhaných pokusov o prihlásenie pre študenta';
$lang['admin_settings_form_field_maximum_solition_filesize'] = 'maximálna velkosť súboru pre upload riešení';
$lang['admin_settings_form_field_readable_file_extensions'] = 'čitateľné súbory';
$lang['admin_settings_form_field_moss_user_id'] = 'id používateľa MOSS-u';
$lang['admin_settings_form_field_test_aging_ticks_to_priority_increase'] = 'počet tikov na zvýšenie priority';
$lang['admin_settings_form_field_test_aging_max_tests_to_raise_priority'] = 'maximálny počet testov na zvýšenie priority';
$lang['admin_settings_form_field_test_maximum_enqueued_pe_student'] = 'maximum testov povolených zaradiť jednému študentovi';
$lang['admin_settings_form_error_message_url_suffix'] = 'Pole <strong>%s</strong> musí byť súborová prípona s bodkou, napr.: .html';
$lang['admin_settings_mod_rewrite_not_found'] = 'Nie je možné nájsť mod rewrite na tomto servri. Prosím, skontrolujte konfiguráciu servera a uistite sa, že systémová premenná MOD_REWRITE_ENABLED je nastavená na "yes", ak Váš server má nainštalovaný mod_rewrite.';
$lang['admin_settings_message_cache_cleared'] = 'Všetky položky vyrovnávacej pamäte boli vymazané.';
$lang['admin_settings_message_compiled_cleared'] = 'Všetky skompilované verzie šablón boli vymazané.';
$lang['admin_settings_changelog_page_title'] = 'Záznam zmien';
$lang['admin_settings_changelog_empty'] = 'Záznam zmien je prázdny.';
$lang['admin_settings_changelog_version'] = 'Verzia';
$lang['admin_settings_changelog_type_new'] = 'Nové';
$lang['admin_settings_changelog_type_change'] = 'Zmenené';
$lang['admin_settings_changelog_type_fix'] = 'Opravené';
$lang['admin_settings_changelog_type_remove'] = 'Odstránené';