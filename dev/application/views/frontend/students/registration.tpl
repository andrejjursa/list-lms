{extends file='layouts/frontend.tpl'}
{block title}{translate line='students_registration_welcome_text'}{/block}
{block main_content}
    <h1>{translate line='students_registration_welcome_text'}</h1>
    {if $save_error}
    <div class="error">{translate line='students_registration_error_cant_save_student'}</div>
    {/if}
    <form action="{internal_url url='students/do_registration'}" method="post">
        <div class="field_wrap">
            <label>{translate line='students_registration_label_fullname'}:</label><br />
            <input type="text" name="student[fullname]" value="{$smarty.post.student.fullname|escape:'html'}" maxlength="255" />
            {form_error field='student[fullname]' left_delimiter='<div class="error">' right_delimiter='</div>'}
        </div>
        <div class="field_wrap">
            <label>{translate line='students_registration_label_email'}:</label><br />
            <input type="text" name="student[email]" value="{$smarty.post.student.email|escape:'html'}" maxlength="255" />
            {form_error field='student[email]' left_delimiter='<div class="error">' right_delimiter='</div>'}
        </div>
        <div class="field_wrap">
            <label>{translate line='students_registration_label_password'}:</label><br />
            <input type="text" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" maxlength="255" />
            {form_error field='student[password]' left_delimiter='<div class="error">' right_delimiter='</div>'}
        </div>
        <div class="field_wrap">
            <label>{translate line='students_registration_label_password_verification'}:</label><br />
            <input type="text" name="student[password_verification]" value="{$smarty.post.student.password_verification|escape:'html'}" maxlength="255" />
            {form_error field='student[password_verification]' left_delimiter='<div class="error">' right_delimiter='</div>'}
        </div>
        <div class="buttons_wrap">
            <input type="submit" name="submit_button" value="{translate line='students_registration_submit_button_text'}" />
        </div>
    </form>
{/block}