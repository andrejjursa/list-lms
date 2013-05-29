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
            <div class="buttons">
                <input type="submit" class="button" name="save_settings" value="{translate line='admin_settings_form_save_button_text'}" />
            </div>
        </form>
    </fieldset>
{/block}