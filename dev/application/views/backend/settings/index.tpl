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
            <div class="buttons">
                <input type="submit" class="button" name="save_settings" value="{translate line='admin_settings_form_save_button_text'}" />
            </div>
        </form>
    </fieldset>
    <pre>{$config|print_r:true}</pre>
    <pre>{$languages|print_r:true}</pre>
{/block}