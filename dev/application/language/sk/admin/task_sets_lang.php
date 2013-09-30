<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Slovak language
$lang['admin_task_sets_page_header'] = 'Zostavy úloh';
$lang['admin_task_sets_fieldset_legend_new_task_set'] = 'Nová zostava úloh';
$lang['admin_task_sets_fieldset_legend_all_task_sets'] = 'Všetky zostavy úloh';
$lang['admin_task_sets_form_label_name'] = 'Názov';
$lang['admin_task_sets_form_label_course_id'] = 'Kurz';
$lang['admin_task_sets_form_label_task_set_type_id'] = 'Typ zostavy úloh';
$lang['admin_task_sets_form_label_task_set_type_id_hint'] = 'Najprv vyberte kurz pre túto zostavu úloh!';
$lang['admin_task_sets_form_label_published'] = 'Je publikovaná?';
$lang['admin_task_sets_form_label_publish_start_time'] = 'Čas začiatku publikovania';
$lang['admin_task_sets_form_label_upload_end_time'] = 'Čas konca odosielania riešení';
$lang['admin_task_sets_form_label_upload_end_time_hint'] = 'Nechajte prázdne, aby sa dali riešenia odosielať bez časového obmedzenia.';
$lang['admin_task_sets_form_label_group_id'] = 'Zostava úloh iba pre skupinu';
$lang['admin_task_sets_form_label_room_id'] = 'Publikovať, keď sa začne výučba v miestnosti';
$lang['admin_task_sets_form_label_points_override_enabled'] = 'Zadať počet bodov manuálne?';
$lang['admin_task_sets_form_label_points_override'] = 'Počet bodov';
$lang['admin_task_sets_form_label_comments_enabled'] = 'Povoliť komentáre';
$lang['admin_task_sets_form_label_comments_moderated'] = 'Komentáre sú moderované';
$lang['admin_task_sets_form_label_instructions'] = 'Inštrukcie';
$lang['admin_task_sets_form_label_task_bonus_task'] = 'Je to bonusová úloha?';
$lang['admin_task_sets_form_label_allowed_file_types'] = 'Povolené typy súborov';
$lang['admin_task_sets_form_label_allowed_file_types_hint'] = 'Povolené typy súborov pre odosielanie študentských riešení. Ak študent pošle súbor tohoto typu, bude zabalený do ZIP archívu. Toto je čiarkou oddelovaný zoznam typov (prípon súborov).<br /><strong>Pozor:</strong> Tieto prípony súborov musia byť definované v application/config/mimes.php, inak nebudú fungovať.';
$lang['admin_task_sets_form_label_allowed_test_types'] = 'Povolené typy testov';
$lang['admin_task_sets_form_button_submit'] = 'Uložiť';
$lang['admin_task_sets_form_field_name'] = 'názov';
$lang['admin_task_sets_form_field_course_id'] = 'kurz';
$lang['admin_task_sets_form_field_task_set_type_id'] = 'typ zostavy úloh';
$lang['admin_task_sets_form_field_points_override'] = 'počet bodov';
$lang['admin_task_sets_flash_message_save_successful'] = 'Zostava úloh úspešne uložená.';
$lang['admin_task_sets_flash_message_save_fail'] = 'Zostavu úloh sa nepodarilo uložiť.';
$lang['admin_task_sets_table_header_name'] = 'Názov';
$lang['admin_task_sets_table_header_course'] = 'Kurz';
$lang['admin_task_sets_table_header_group'] = 'Skupina';
$lang['admin_task_sets_table_header_task_set_type'] = 'Typ zostavy úloh';
$lang['admin_task_sets_table_header_tasks'] = 'Počet úloh';
$lang['admin_task_sets_table_header_published'] = 'Môže byť publikovaná?';
$lang['admin_task_sets_table_header_publish_start_time'] = 'Publikované od';
$lang['admin_task_sets_table_header_upload_end_time'] = 'Deadline odosielania';
$lang['admin_task_sets_table_header_controlls'] = 'Ovládanie';
$lang['admin_task_sets_table_button_edit'] = 'Upraviť';
$lang['admin_task_sets_table_button_delete'] = 'Vymazať';
$lang['admin_task_sets_table_button_open'] = 'Otvoriť';
$lang['admin_task_sets_table_button_discussion'] = 'Diskusia';
$lang['admin_task_sets_table_button_clone_task_set'] = 'Klonovať';
$lang['admin_task_sets_table_field_published_yes'] = 'Áno';
$lang['admin_task_sets_table_field_published_no'] = 'Nie';
$lang['admin_task_sets_error_task_set_not_found'] = 'Požadovaná zostava úloh sa nenašla!';
$lang['admin_task_sets_error_task_set_cant_be_cloned'] = 'Chyba v procese klonovania, zostava úloh sa nedá naklonovať.';
$lang['admin_task_sets_success_task_set_cloned'] = 'Zostava úloh bola úspešne naklonovaná.';
$lang['admin_task_set_javascript_message_delete_question'] = 'Naozaj chcete vymazať túto zostavu úloh?';
$lang['admin_task_set_javascript_message_after_delete'] = 'Zostava úloh bola úspešne vymazaná.';
$lang['admin_task_set_javascript_message_after_open'] = 'Zostava úloh bola úspešne otvorená.';
$lang['admin_task_set_javascript_message_clone_question'] = 'Chcete naklonovať túto zostavu úloh? Správy z diskusie nebudú klonované. Klonovaná zostava úloh nebude publikovaná.';
$lang['admin_task_sets_javascript_task_text_title'] = 'Text úlohy';
$lang['admin_task_sets_filter_form_submit_button'] = 'Použiť';
$lang['admin_task_sets_filter_form_field_course'] = 'Kurz';
$lang['admin_task_sets_filter_form_field_group'] = 'Skupina';
$lang['admin_task_sets_filter_form_field_task_set_type'] = 'Typ zostavy úloh';
$lang['admin_task_sets_filter_form_field_tasks'] = 'Úlohy';
$lang['admin_task_sets_filter_form_field_tasks_option_all'] = 'Všetky zostavy úloh';
$lang['admin_task_sets_filter_form_field_tasks_option_without_tasks'] = 'Zostavy úloh neobsahujúce úlohy';
$lang['admin_task_sets_filter_form_field_tasks_option_with_tasks'] = 'Zostavy úloh obsahujúce úlohy';
$lang['admin_task_sets_filter_form_field_name'] = 'Názov';
$lang['admin_task_sets_filter_option_without_group'] = 'Zostavy úloh bez skupiny';
$lang['admin_task_sets_tabs_label_about_task_set'] = 'O zostave úloh';
$lang['admin_task_sets_tabs_label_additional_permissions'] = 'Ďalšie oprávnenia';
$lang['admin_task_sets_tabs_label_tasks'] = 'Úlohy';
$lang['admin_task_sets_tabs_label_instructions'] = 'Inštrukcie';
$lang['admin_task_sets_form_label_task_points_total'] = 'Body za túto úlohu';
$lang['admin_task_sets_form_label_delete_task'] = 'Vymazať zo zostavy úloh';
$lang['admin_task_sets_form_field_task_points_total'] = 'body za túto úlohu';
$lang['admin_task_sets_javascript_remove_task_question'] = 'Ste si istý odobratím tejto úlohy zo zostavy úloh?';
$lang['admin_task_sets_comments_page_title'] = 'Komentáre pre zostavu úloh "%s"';
$lang['admin_task_sets_comments_error_comments_disabled'] = 'Komentáre pre požadovanú zostavu úloh sú zakázané.';
$lang['admin_task_sets_comments_error_no_comments_yet'] = 'Ešte tu nie sú žiadne komentáre!';
$lang['admin_task_sets_comments_error_reply_at_comment_from_different_task_set'] = 'Snažíte sa odpovedať na komentár z inej zostavy úloh!';
$lang['admin_task_sets_comments_error_reply_at_comment_not_exists'] = 'Komentár, na ktorý sa snažíte odpovedať, nebol nájdený.';
$lang['admin_task_sets_comments_error_save_failed'] = 'Uloženie komentára zlyhalo.';
$lang['admin_task_sets_comments_error_delete_comment'] = 'Komentár nebol vymazaný.';
$lang['admin_task_sets_comments_success_delete_comment'] = 'Komentár bol vymazaný.';
$lang['admin_task_sets_comments_error_approve_comment'] = 'Komentár nebol schválený.';
$lang['admin_task_sets_comments_success_approve_comment'] = 'Komentár bol schválený.';
$lang['admin_task_sets_comments_save_successfully'] = 'Komentár bol úspešne uložený.';
$lang['admin_task_sets_comments_new_comment'] = 'Nový komentár';
$lang['admin_task_sets_comments_all_comments'] = 'Všetky komentáre';
$lang['admin_task_sets_comments_my_settings'] = 'Moje nastavenia';
$lang['admin_task_sets_comments_form_label_text'] = 'Text komentára';
$lang['admin_task_sets_comments_form_label_text_hint'] = 'Môžete použiť html značky &lt;strong&gt;, &lt;a&gt;, &lt;em&gt; a &lt;span&gt;.';
$lang['admin_task_sets_comments_form_button_submit'] = 'Odoslať komentár';
$lang['admin_task_sets_comments_form_field_text'] = 'text komentára';
$lang['admin_task_sets_comments_my_settings_unsubscribe'] = 'Neodoberať komentáre z tejto zostavy úloh';
$lang['admin_task_sets_comments_my_settings_subscribe'] = 'Odoberať komentáre z tejto zostavy úloh';
$lang['admin_task_sets_comments_my_settings_unsubscribe_success'] = 'Ste úspešne odhlásený z odoberania komentárov tejto zostavy úloh.';
$lang['admin_task_sets_comments_my_settings_unsubscribe_error'] = 'Nepodarilo sa vás odhlásiť z odoberania komentárov tejto zostavy úloh.';
$lang['admin_task_sets_comments_my_settings_subscribe_success'] = 'Ste úspešne prihlásený na odber komentárov tejto zostavy úloh.';
$lang['admin_task_sets_comments_my_settings_subscribe_error'] = 'Nepodarilo sa vás prihlásiť na odber komentárov tejto zostavy úloh.';
$lang['admin_task_sets_comments_button_reply_at'] = 'Odpovedať na tento komentár';
$lang['admin_task_sets_comments_button_approve_comment'] = 'Schváliť tento komentár';
$lang['admin_task_sets_comments_button_delete_comment'] = 'Vymazať tento komentár';
$lang['admin_task_sets_comments_js_message_question_delete'] = 'Naozaj chcete vymazať tento komentár? Všetky odpovede naň, ak existujú, budú tiež zmazané.';
$lang['admin_task_sets_comments_js_message_question_approve'] = 'Naozaj chcete schváliť tento komentár a zobraziť ho tým pre všetkých?';
$lang['admin_task_sets_comments_reply_at_page_title'] = 'Odpoveď na predchodzí komentár';
$lang['admin_task_sets_comments_email_subject_new_post'] = 'Nový komentár!';
$lang['admin_task_sets_comments_email_new_post_body_from'] = 'Učiteľ(ka) <strong>%s</strong> práve poslal(a) nový komentár do diskusie k úlohe <strong>%s</strong>.';
$lang['admin_task_sets_comments_email_new_post_body_text'] = 'Text nového komentára';
$lang['admin_task_sets_edit_task_button'] = 'Upraviť túto úlohu';
$lang['admin_task_sets_permission_button_new_permission'] = 'Nové oprávnenie';