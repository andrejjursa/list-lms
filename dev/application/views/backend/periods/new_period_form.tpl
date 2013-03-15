{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="period_name_id">{translate line='admin_periods_form_label_name'}:</label>
    <p class="input"><input type="text" name="period[name]" value="{$smarty.post.period.name|escape:'html'}" id="period_name_id" /></p>
    {form_error field='period[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="save_button" value="{translate line='admin_periods_form_save_button'}" class="button" />
</div>