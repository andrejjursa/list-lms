{extends file='layouts/frontend.tpl'}
{block title}{translate line='students_my_account_welcome_title'}{/block}
{block main_content}
    <h1>{translate line='students_my_account_welcome_title'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='students_my_account_legend_basic_information'}</legend>
        <form action="{internal_url url='students/save_basic_information'}" method="post">
            <div class="field">
                <label for="student_fullname_id">{translate line='students_my_account_label_fullname'}:</label>
                <p class="input"><input type="text" name="student[fullname]" value="{$smarty.post.student.fullname|default:$student->fullname|escape:'html'}" id="student_fullname_id" /></p>
                {form_error field='student[fullname]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="student_language_id">{translate line='students_my_account_label_language'}:</label>
                <p class="input"><select name="student[language]" size="1">
                    {html_options options=$languages selected=$smarty.post.student.language|default:$student->language}
                </select></p>
                {form_error field='student[language]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="button_submit" value="{translate line='students_my_account_button_submit'}" class="button" />
            </div>
            <input type="hidden" name="student_id" value="{$smarty.post.student_id|default:$student->id|intval}" />
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='students_my_account_legent_change_password'}</legend>
        <form action="{internal_url url='students/save_password'}" method="post">
            <div class="field">
                <label for="student_password_old_id">{translate line='students_my_account_label_password_old'}:</label>
                <p class="input"><input type="password" name="student[password_old]" value="{$smarty.post.student.password_old|escape:'html'}" id="student_password_old_id" /></p>
                {form_error field='student[password_old]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="student_password_id">{translate line='students_my_account_label_password_new'}:</label>
                <p class="input"><input type="password" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" id="student_password_id" /></p>
                {form_error field='student[password]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="student_password_validation_id">{translate line='students_my_account_label_password_validation'}:</label>
                <p class="input"><input type="password" name="student[password_validation]" value="{$smarty.post.student.password_validation|escape:'html'}" id="student_password_validation_id" /></p>
                {form_error field='student[password_validation]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="button_submit" value="{translate line='students_my_account_button_submit'}" class="button" />
            </div>
            <input type="hidden" name="student_id" value="{$smarty.post.student_id|default:$student->id|intval}" />
        </form>
    </fieldset>
    {if $this->config->item('student_mail_change') eq TRUE}
    <fieldset>
        <legend>{translate line='students_my_account_legend_change_email'}</legend>
        <form action="{internal_url url='students/save_email'}" method="post">
            <div class="field">
                <label>{translate line='students_my_account_label_email_current'}:</label>
                <p class="input">{$student->email|escape:'html'}</p>
            </div>
            <div class="field">
                <label for="student_email_id">{translate line='students_my_account_label_email'}:</label>
                <p class="input"><input type="text" name="student[email]" value="{$smarty.post.student.email|escape:'html'}" id="student_email_id" /></p>
                {form_error field='student[email]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="student_email_validation_id">{translate line='students_my_account_label_email_validation'}:</label>
                <p class="input"><input type="text" name="student[email_validation]" value="{$smarty.post.student.email_validation|escape:'html'}" id="student_email_validation_id" /></p>
                {form_error field='student[email_validation]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="button_submit" value="{translate line='students_my_account_button_submit'}" class="button" />
            </div>
            <input type="hidden" name="student_id" value="{$smarty.post.student_id|default:$student->id|intval}" />
        </form>
    </fieldset>
    {/if}
    <fieldset>
        <legend>{translate line='students_my_account_legend_avatar'}</legend>
        <form action="" method="post">
            <div class="field">
                <label>{translate line='students_my_account_label_avatar'}:</label>
                <p class="input"><img src="{$student->get_avatar()}" alt="" /></p>
                <p class="input">
                    <a href="{internal_url url='students/upload_avatar'}" class="button upload_avatar">{translate line='students_my_account_button_upload_avatar'}</a>
                    {if $student->has_avatar()}<a href="{internal_url url='students/delete_avatar'}" class="button delete delete_avatar">{translate line='students_my_account_button_delete_avatar'}</a>{/if}
                </p>
            </div>
        </form>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_avatar: '{translate line='students_my_account_delete_avatar_question'}'
    };
</script>{/block}