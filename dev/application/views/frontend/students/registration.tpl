{extends file='layouts/frontend_loginbox.tpl'}
{block title}{translate line='students_registration_welcome_text'}{/block}
{block main_content}
    <div id="registrationboxwrap">
        <h1>{translate line='students_registration_welcome_text'}</h1>
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {if $save_error}
        <div class="error">{translate line='students_registration_error_cant_save_student'}</div>
        {/if}
        <form action="{internal_url url='students/do_registration'}" method="post">
            <div class="field">
                <label>{translate line='students_registration_label_fullname'}:</label>
                <p class="input"><input type="text" name="student[fullname]" value="{$smarty.post.student.fullname|escape:'html'}" maxlength="255" /></p>
                {form_error field='student[fullname]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='students_registration_label_email'}:</label>
                <p class="input"><input type="text" name="student[email]" value="{$smarty.post.student.email|escape:'html'}" maxlength="255" /></p>
                {form_error field='student[email]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='students_registration_label_password'}:</label>
                <p class="input"><input type="text" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" maxlength="255" /></p>
                {form_error field='student[password]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='students_registration_label_password_verification'}:</label>
                <p class="input"><input type="text" name="student[password_verification]" value="{$smarty.post.student.password_verification|escape:'html'}" maxlength="255" /></p>
                {form_error field='student[password_verification]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='students_registration_submit_button_text'}" class="button" />
            </div>
        </form>
    </div>
{/block}