{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="">{translate line='admin_task_sets_comments_form_label_text'}:</label>
    <div class="input"><textarea name="comment[text]">{$smarty.post.comment.text|escape:'html'}</textarea></div>
    <p class="input"><em>{translate line='admin_task_sets_comments_form_label_text_hint'}</em></p>
    {form_error field='comment[text]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_task_sets_comments_form_button_submit'}" class="button" />
    <input type="hidden" name="comment[task_set_id]" value="{$task_set->id|intval}" />
</div>