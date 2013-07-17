<div class="field">
    <label for="">{translate line='admin_task_sets_comments_form_label_text'}:</label>
    <div class="input"><textarea name="comment[text]">{$smarty.post.comment.text|escape:'html'}</textarea></div>
    <p class="input"><em>{translate line='admin_task_sets_comments_form_label_text_hint'}</em></p>
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_task_sets_comments_form_button_submit'}" class="button" />
</div>