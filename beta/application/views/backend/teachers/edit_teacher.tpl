{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_teachers_list_page_header'}{/block}
{block main_content}
    <h2>{translate line='admin_teachers_list_page_header'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $teacher->exists() or $smarty.post.teacher}
    <fieldset>
        <form action="{internal_url url='admin_teachers/update_teacher'}" method="post">
            <div class="field">
                <label for="teacher_fullname_id" class="required">{translate line='admin_teachers_list_form_label_fullname'}:</label>
                <p class="input"><input type="text" name="teacher[fullname]" value="{$smarty.post.teacher.fullname|default:$teacher->fullname|escape:'html'}" id="teacher_fullname_id" /></p>
                {form_error field='teacher[fullname]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="teacher_email_id" class="required">{translate line='admin_teachers_list_form_label_email'}:</label>
                <p class="input"><input type="text" name="teacher[email]" value="{$smarty.post.teacher.email|default:$teacher->email|escape:'html'}" id="teacher_email_id" /></p>
                {form_error field='teacher[email]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="teacher_password_id">{translate line='admin_teachers_list_form_label_password'}:</label>
                <p class="input"><input type="text" name="teacher[password]" value="{$smarty.post.teacher.password|escape:'html'}" id="teacher_password_id" /></p>
                {form_error field='teacher[password]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                <p class="input"><em>{translate line='admin_teachers_list_form_label_password_hint'}</em></p>
            </div>
            <div class="buttons">
                <input type="submit" class="button" name="submit_button" value="{translate line='admin_teachers_list_form_button_save'}" />
                <input type="hidden" name="teacher_id" value="{$smarty.post.teacher_id|default:$teacher->id|intval}" />
            </div>
        </form>
    </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_teachers_list_teacher_not_found' inline}
    {/if}
{/block}