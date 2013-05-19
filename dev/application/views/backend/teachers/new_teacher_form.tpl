{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="teacher_fullname_id" class="required">{translate line='admin_teachers_list_form_label_fullname'}:</label>
    <p class="input"><input type="text" name="teacher[fullname]" value="{$smarty.post.teacher.fullname|escape:'html'}" id="teacher_fullname_id" /></p>
    {form_error field='teacher[fullname]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="teacher_email_id" class="required">{translate line='admin_teachers_list_form_label_email'}:</label>
    <p class="input"><input type="text" name="teacher[email]" value="{$smarty.post.teacher.email|escape:'html'}" id="teacher_email_id" /></p>
    {form_error field='teacher[email]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="teacher_password_id" class="required">{translate line='admin_teachers_list_form_label_password'}:</label>
    <p class="input"><input type="text" name="teacher[password]" value="{$smarty.post.teacher.password|escape:'html'}" id="teacher_password_id" /></p>
    {form_error field='teacher[password]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" class="button" name="submit_button" value="{translate line='admin_teachers_list_form_button_save'}" />
</div>