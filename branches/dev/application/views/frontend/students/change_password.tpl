{extends file='layouts/frontend_loginbox.tpl'}
{block title}{translate line='students_change_password_page_title'}{/block}
{block main_content}
    <div class="boxborder">
        <div class="loginbox">
            <h1>{translate line='students_change_password_page_title'}</h1>
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            <form action="{internal_url url="students/do_change_password/{$token}/{$encoded_email}"}" method="post">
                <div class="field_login">
                    <label>{translate line='students_change_password_form_label_current_account'}:</label>
                    <p class="input"><em>{$student->fullname} [{$student->email}]</em></p>
                </div>
                <div class="field_login">
                    <label for="student_password_id">{translate line='students_change_password_form_label_password'}:</label>
                    <p class="input"><input type="password" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" id="student_password_id" /></p>
                    {form_error field='student[password]' left_delimiter = '<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field_login">
                    <label for="student_verify_id">{translate line='students_change_password_form_label_verify'}:</label>
                    <p class="input"><input type="password" name="student[verify]" value="{$smarty.post.student.verify|escape:'html'}" id="student_verify_id" /></p>
                    {form_error field='student[verify]' left_delimiter = '<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" name="button_submit" value="{translate line='students_change_password_submit_button_label'}" class="button" />
                </div>
            </form>
        </div>
    </div>
{/block}