<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// English language

$lang['admin_settings_page_title'] = 'Application settings';
$lang['admin_settings_form_label_language'] = 'Language';
$lang['admin_settings_form_label_rewrite_engine_enabled'] = 'Enable rewrite engine';
$lang['admin_settings_form_label_url_suffix'] = 'URL suffix extension';
$lang['admin_settings_form_label_teacher_login_security_timeout'] = 'Security timeout for teacher login (in minutes)';
$lang['admin_settings_form_label_student_login_security_timeout'] = 'Security timeout for student login (in minutes)';
$lang['admin_settings_form_label_teacher_login_security_allowed_attempts'] = 'Maximum number of failed login attempts for teacher';
$lang['admin_settings_form_label_student_login_security_allowed_attempts'] = 'Maximum number of failed login attempts for student';
$lang['admin_settings_form_label_maximum_solition_filesize'] = 'Maximum file size for solutions uploading';
$lang['admin_settings_form_label_maximum_solition_filesize_hint'] = 'Value is in KiB, 1 KiB = 1024 bytes, 1024 KiB = 1 MiB.';
$lang['admin_settings_form_label_readable_file_extensions'] = 'Readable files';
$lang['admin_settings_form_label_readable_file_extensions_hint'] = 'Comma separated list of file extensions, which are readable (i.e. in student solutions valuation).';
$lang['admin_settings_form_label_student_registration_enabled'] = 'Enable student registration feature';
$lang['admin_settings_form_save_button_text'] = 'Save settings';
$lang['admin_settings_form_rewrite_option_true'] = 'Yes';
$lang['admin_settings_form_rewrite_option_false'] = 'No';
$lang['admin_settings_form_field_language'] = 'language';
$lang['admin_settings_form_field_rewrite_engine_enabled'] = 'enable rewrite engine';
$lang['admin_settings_form_field_url_suffix'] = 'url suffix extension';
$lang['admin_settings_form_field_teacher_login_security_timeout'] = 'security timeout for teacher login (in minutes)';
$lang['admin_settings_form_field_student_login_security_timeout'] = 'security timeout for student login (in minutes)';
$lang['admin_settings_form_field_teacher_login_security_allowed_attempts'] = 'maximum number of failed login attempts for teacher';
$lang['admin_settings_form_field_student_login_security_allowed_attempts'] = 'maximum number of failed login attempts for student';
$lang['admin_settings_form_field_maximum_solition_filesize'] = 'maximum file size for solutions uploading';
$lang['admin_settings_form_field_readable_file_extensions'] = 'readable files';
$lang['admin_settings_form_error_message_url_suffix'] = 'The <strong>%s</strong> field must be file extension with dot, i.e.: .html';
$lang['admin_settings_mod_rewrite_not_found'] = 'Can\'t found mod rewrite on this server. Please, check your server configuration and make sure there is system variable MOD_REWRITE_ENABLED set to "yes", when your server has working mod_rewrite installation.';