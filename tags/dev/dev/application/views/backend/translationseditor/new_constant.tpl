{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_translationseditor_new_constant_title'}{/block}
{block main_content}
    <h3>{translate line='admin_translationseditor_new_constant_title'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="{internal_url url='admin_translationseditor/save_new_constant/'}" method="post">
            <div class="field">
                <label for="translation_constant_id" class="required">{translate line='admin_translationseditor_new_constant_label_constant'}:</label>
                <p class="input"><input type="text" maxlength="255" name="translation[constant]" value="{$smarty.post.translation.constant|escape:'html'}" id="translation_constant_id" /></p>
                {form_error field='translation[constant]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            {foreach $languages as $idiom => $language}
            <div class="field">
                <label for="translation_text_{$idiom}_id">{translate|sprintf:$language line='admin_translationseditor_new_constant_label_translation_for'}:</label>
                <p class="input"><textarea name="translation[text][{$idiom}]" id="translation_text_{$idiom}_id">{$smarty.post.translation.text[$idiom]|escape:'html'}</textarea></p>
            </div>
            {/foreach}
            <div class="buttons">
                <input type="submit" name="save_button" value="{translate line='admin_translationseditor_new_constant_button_save'}" class="button" />
            </div>
        </form>
    </fieldset>
{/block}