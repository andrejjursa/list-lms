{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_settings_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_settings_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="{internal_url url='admin_settings/save'}" method="post">
            <div class="field">
                <label for="config_language_id">{translate line='admin_settings_form_label_language'}:</label>
                <p class="input"><select name="config[language]" size="1" id="config_language_id">{html_options options=$languages selected=$smarty.post.config.language|default:$config.language}</select></p>
                {form_error field='config[language]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            {if is_mod_rewrite_enabled()}
            <div class="field">
                <label for="config_rewrite_engine_enabled_id">{translate line='admin_settings_form_label_rewrite_engine_enabled'}:</label>
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
            <div class="buttons">
                <input type="submit" class="button" name="save_settings" value="{translate line='admin_settings_form_save_button_text'}" />
            </div>
        </form>
    </fieldset>
{/block}