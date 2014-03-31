{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_settings_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_settings_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="{internal_url url='admin_settings/save'}" method="post">
            <div class="field">
                <label for="config_language_id" class="required">{translate line='admin_settings_form_label_language'}:</label>
                <p class="input"><select name="config[language]" size="1" id="config_language_id">{html_options options=$languages selected=$smarty.post.config.language|default:$config.language}</select></p>
                {form_error field='config[language]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            {if is_mod_rewrite_enabled()}
            <div class="field">
                <label for="config_rewrite_engine_enabled_id" class="required">{translate line='admin_settings_form_label_rewrite_engine_enabled'}:</label>
                <p class="input"><select name="config[rewrite_engine_enabled]" size="1" id="config_rewrite_engine_enabled_id">{html_options options=[1 => {translate line='admin_settings_form_rewrite_option_true'}, 0 => {translate line='admin_settings_form_rewrite_option_false'}] selected=$smarty.post.config.rewrite_engine_enabled|default:$config.rewrite_engine_enabled|intval}</select></p>
                {form_error field='config[rewrite_engine_enabled]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            {else}
            <div class="field">
                {include file='partials/backend_general/error_box.tpl' message='lang:admin_settings_mod_rewrite_not_found' inline}
            </div>
            {/if}
            <div class="field">
                <label for="config_url_suffix_id">{translate line='admin_settings_form_label_url_suffix'}:</label>
                <p class="input"><input type="text" name="config[url_suffix]" value="{$smarty.post.config.url_suffix|default:$config.url_suffix|escape:'html'}" id="config_url_suffix_id" /></p>
                {form_error field='config[url_suffix]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <hr />
            <div class="field">
                <label for="config_teacher_login_security_timeout_id" class="required">{translate line='admin_settings_form_label_teacher_login_security_timeout'}:</label>
                <p class="input"><input type="text" name="config[teacher_login_security_timeout]" value="{$smarty.post.config.teacher_login_security_timeout|default:$config.teacher_login_security_timeout|escape:'html'}" id="config_teacher_login_security_timeout_id" /></p>
                {form_error field='config[teacher_login_security_timeout]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_student_login_security_timeout_id" class="required">{translate line='admin_settings_form_label_student_login_security_timeout'}:</label>
                <p class="input"><input type="text" name="config[student_login_security_timeout]" value="{$smarty.post.config.student_login_security_timeout|default:$config.student_login_security_timeout|escape:'html'}" id="config_student_login_security_timeout_id" /></p>
                {form_error field='config[student_login_security_timeout]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_teacher_login_security_allowed_attempts_id" class="required">{translate line='admin_settings_form_label_teacher_login_security_allowed_attempts'}:</label>
                <p class="input"><input type="text" name="config[teacher_login_security_allowed_attempts]" value="{$smarty.post.config.teacher_login_security_allowed_attempts|default:$config.teacher_login_security_allowed_attempts|escape:'html'}" id="config_teacher_login_security_allowed_attempts_id" /></p>
                {form_error field='config[teacher_login_security_allowed_attempts]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_student_login_security_allowed_attempts_id" class="required">{translate line='admin_settings_form_label_student_login_security_allowed_attempts'}:</label>
                <p class="input"><input type="text" name="config[student_login_security_allowed_attempts]" value="{$smarty.post.config.student_login_security_allowed_attempts|default:$config.student_login_security_allowed_attempts|escape:'html'}" id="config_student_login_security_allowed_attempts_id" /></p>
                {form_error field='config[student_login_security_allowed_attempts]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <hr />
            <div class="field">
                <label for="config_maximum_solition_filesize_id" class="required">{translate line='admin_settings_form_label_maximum_solition_filesize'}:</label>
                <p class="input"><input type="text" name="config[maximum_solition_filesize]" value="{$smarty.post.config.maximum_solition_filesize|default:$config.maximum_solition_filesize|escape:'html'}" id="config_maximum_solition_filesize_id"/></p>
                <p class="input"><em>{translate line='admin_settings_form_label_maximum_solition_filesize_hint'}</em></p>
                {form_error field='config[maximum_solition_filesize]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_readable_file_extensions_id" class="required">{translate line='admin_settings_form_label_readable_file_extensions'}:</label>
                <p class="input"><input type="text" name="config[readable_file_extensions]" value="{$smarty.post.config.readable_file_extensions|default:$config.readable_file_extensions|escape:'html'}" id="config_readable_file_extensions_id"/></p>
                <p class="input"><em>{translate line='admin_settings_form_label_readable_file_extensions_hint'}</em></p>
                {form_error field='config[readable_file_extensions]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <hr />
            <div class="field">
                <label for="config_student_registration_enabled_id">{translate line='admin_settings_form_label_student_registration_enabled'}:</label>
                <p class="input"><input type="checkbox" name="config[student_registration][enabled]" value="1"{if $smarty.post.config.student_registration.enabled|default:$config.student_registration.enabled} checked="checked"{/if} id="config_student_registration_enabled_id" /></p>
            </div>
            <div class="field">
                <label for="config_student_mail_change_id">{translate line='admin_settings_form_label_student_mail_change'}:</label>
                <p class="input"><input type="checkbox" name="config[student_mail_change]" value="1"{if $smarty.post.config.student_mail_change|default:$config.student_mail_change} checked="checked"{/if} id="config_student_mail_change_id" /></p>
            </div>
            <hr />
            <div class="field">
                <label for="config_email_protocol_id" class="required">{translate line='admin_settings_form_label_email_protocol'}:</label>
                <p class="input">{$email_protocol_options=['mail'=>'lang:admin_settings_form_email_protocol_mail', 'sendmail'=>'lang:admin_settings_form_email_protocol_sendmail', 'smtp'=>'lang:admin_settings_form_email_protocol_smtp']}
                    <select name="config[email][protocol]" size="1" id="config_email_protocol_id">
                        {list_html_options options=$email_protocol_options selected=$smarty.post.config.email.protocol|default:$config.email.protocol}
                    </select>
                </p>
                {form_error field='config[email][protocol]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_email_mailpath_id">{translate line='admin_settings_form_label_email_mailpath'}:</label>
                <p class="input"><input type="text" name="config[email][mailpath]" value="{$smarty.post.config.email.mailpath|default:$config.email.mailpath|escape:'html'}" id="config_email_mailpath_id" /></p>
            </div>
            <div class="field">
                <label for="config_email_smtp_host_id">{translate line='admin_settings_form_label_email_smtp_host'}:</label>
                <p class="input"><input type="text" name="config[email][smtp_host]" value="{$smarty.post.config.email.smtp_host|default:$config.email.smtp_host|escape:'html'}" id="config_email_smtp_host_id" /></p>
            </div>
            <div class="field">
                <label for="config_email_smtp_user_id">{translate line='admin_settings_form_label_email_smtp_user'}:</label>
                <p class="input"><input type="text" name="config[email][smtp_user]" value="{$smarty.post.config.email.smtp_user|default:$config.email.smtp_user|escape:'html'}" id="config_email_smtp_user_id" /></p>
            </div>
            <div class="field">
                <label for="config_email_smtp_pass_id">{translate line='admin_settings_form_label_email_smtp_pass'}:</label>
                <p class="input"><input type="password" name="config[email][smtp_pass]" value="{$smarty.post.config.email.smtp_pass|default:$config.email.smtp_pass|escape:'html'}" id="config_email_smtp_pass_id" /></p>
            </div>
            <div class="field">
                <label for="config_email_smtp_port_id">{translate line='admin_settings_form_label_email_smtp_port'}:</label>
                <p class="input"><input type="text" name="config[email][smtp_port]" value="{$smarty.post.config.email.smtp_port|default:$config.email.smtp_port|escape:'html'}" id="config_email_smtp_port_id" /></p>
                {form_error field='config[email][smtp_port]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_email_smtp_timeout_id">{translate line='admin_settings_form_label_email_smtp_timeout'}:</label>
                <p class="input"><input type="text" name="config[email][smtp_timeout]" value="{$smarty.post.config.email.smtp_timeout|default:$config.email.smtp_timeout|escape:'html'}" id="config_email_smtp_timeout_id" /></p>
                {form_error field='config[email][smtp_timeout]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="config_email_priority_id" class="required">{translate line='admin_settings_form_label_email_priority'}:</label>
                <p class="input">{$email_priority_options=['1'=>'lang:admin_settings_form_email_priority_1', '2'=>'lang:admin_settings_form_email_priority_2', '3'=>'lang:admin_settings_form_email_priority_3', '4'=>'lang:admin_settings_form_email_priority_4', '5'=>'lang:admin_settings_form_email_priority_5']}
                    <select name="config[email][priority]" size="1" id="config_email_priority_id">
                        {list_html_options options=$email_priority_options selected=$smarty.post.config.email.priority|default:$config.email.priority}
                    </select>
                </p>
                {form_error field='config[email][priority]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="email_multirecipient_batch_mode_id" class="required">{translate line='admin_settings_form_label_email_multirecipient_batch_mode'}:</label>
                <p class="input">{$email_multirecipient_batch_mode_options=['0'=>'lang:admin_settings_form_email_multirecipient_batch_mode_false', '1'=>'lang:admin_settings_form_email_multirecipient_batch_mode_true']}
                    <select name="config[email_multirecipient_batch_mode]" size="1" id="email_multirecipient_batch_mode_id">
                        {list_html_options options=$email_multirecipient_batch_mode_options selected=$smarty.post.config.email_multirecipient_batch_mode|default:$config.email_multirecipient_batch_mode}
                    </select>
                </p>
            </div>
            <hr />
            <div class="buttons">
                <input type="submit" class="button" name="save_settings" value="{translate line='admin_settings_form_save_button_text'}" />
            </div>
            <hr />
            <div class="field">
                <label>{translate line='admin_settings_form_label_smarty'}:</label>
                <div class="input">
                    <a href="{internal_url url='admin_settings/clear_all_cache'}" class="button special">{translate|sprintf:$count_of_cached_records line='admin_settings_form_clear_all_cache_button'}</a>
                    <a href="{internal_url url='admin_settings/clear_all_compiled'}" class="button special">{translate|sprintf:$count_of_compiled_templates line='admin_settings_form_clear_all_compiled_button'}</a>
                </div>
            </div>
        </form>
    </fieldset>
{/block}