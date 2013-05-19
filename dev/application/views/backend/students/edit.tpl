{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_students_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_students_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $student->exists() or $smarty.post.student}
        <fieldset>
            <form action="{internal_url url='admin_students/update'}" method="post">
                <div class="field">
                    <label for="student_fullname_id" class="required">{translate line='admin_students_form_label_fullname'}:</label>
                    <p class="input"><input type="text" name="student[fullname]" value="{$smarty.post.student.fullname|default:$student->fullname|escape:'html'}" id="student_fullname_id" /></p>
                    {form_error field='student[fullname]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="student_email_id" class="required">{translate line='admin_students_form_label_email'}:</label>
                    <p class="input"><input type="text" name="student[email]" value="{$smarty.post.student.email|default:$student->email|escape:'html'}" id="student_email_id" /></p>
                    {form_error field='student[email]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="student_password_id">{translate line='admin_students_form_label_password'}:</label>
                    <p class="input"><input type="text" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" id="student_password_id" /></p>
                    {form_error field='student[password]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" class="button" name="submit_button" value="{translate line='admin_students_form_button_save'}" />
                    <input type="hidden" name="student_id" value="{$smarty.post.student_id|default:$student->id|intval}" />
                </div>
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_students_student_not_found' inline}
    {/if}
{/block}