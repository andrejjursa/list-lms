{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="student_fullname_id">{translate line='admin_students_form_label_fullname'}:</label>
    <p class="input"><input type="text" name="student[fullname]" value="{$smarty.post.student.fullname|escape:'html'}" id="student_fullname_id" /></p>
    {form_error field='student[fullname]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="student_email_id">{translate line='admin_students_form_label_email'}:</label>
    <p class="input"><input type="text" name="student[email]" value="{$smarty.post.student.email|escape:'html'}" id="student_email_id" /></p>
    {form_error field='student[email]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="student_password_id">{translate line='admin_students_form_label_password'}:</label>
    <p class="input"><input type="text" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" id="student_password_id" /></p>
    {form_error field='student[password]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" class="button" name="submit_button" value="{translate line='admin_students_form_button_save'}" />
</div>