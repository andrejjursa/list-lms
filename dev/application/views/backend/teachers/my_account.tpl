{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_teachers_my_account_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_teachers_my_account_welcome_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_teachers_my_account_legend_basic_information'}</legend>
        <form action="{internal_url url='admin_teachers/save_basic_information'}" method="post">
            <div class="field">
                <label for="teacher_fullname_id">{translate line='admin_teachers_my_account_label_fullname'}:</label>
                <p><input type="text" name="teacher[fullname]" value="{$smarty.post.teacher.fullname|default:$teacher->fullname|escape:'html'}" id="teacher_fullname_id" /></p>
                {form_error field='teacher[fullname]' left_delimiter='<p class="error">' right_delimiter='</p>'}
            </div>
            <div class="field">
                <label for="teacher_language_id">{translate line='admin_teachers_my_account_label_language'}:</label>
                <p><select name="teacher[language]" size="1">
                    {html_options options=$languages selected=$smarty.post.teacher.language|default:$teacher->language }
                </select></p>
                {form_error field='teacher[language]' left_delimiter='<p class="error">' right_delimiter='</p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="button_submit" value="{translate line='admin_teachers_my_account_button_submit'}" class="button" />
            </div>
            <input type="hidden" name="teacher_id" value="{$smarty.post.teacher_id|default:$teacher->id|intval}" />
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_teachers_my_account_legent_change_password'}</legend>
        <form action="{internal_url url='admin_teachers/save_password'}" method="post">
            <div class="field">
                <label for="teacher_password_old_id">{translate line='admin_teachers_my_account_label_password_old'}:</label>
                <p><input type="password" name="teacher[password_old]" value="{$smarty.post.teacher.password_old|escape:'html'}" id="teacher_password_old_id" /></p>
                {form_error field='teacher[password_old]' left_delimiter='<p class="error">' right_delimiter='</p>'}
            </div>
            <div class="field">
                <label for="teacher_password_id">{translate line='admin_teachers_my_account_label_password_new'}:</label>
                <p><input type="password" name="teacher[password]" value="{$smarty.post.teacher.password|escape:'html'}" id="teacher_password_id" /></p>
                {form_error field='teacher[password]' left_delimiter='<p class="error">' right_delimiter='</p>'}
            </div>
            <div class="field">
                <label for="teacher_password_validation_id">{translate line='admin_teachers_my_account_label_password_validation'}:</label>
                <p><input type="password" name="teacher[password_validation]" value="{$smarty.post.teacher.password_validation|escape:'html'}" id="teacher_password_validation_id" /></p>
                {form_error field='teacher[password_validation]' left_delimiter='<p class="error">' right_delimiter='</p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="button_submit" value="{translate line='admin_teachers_my_account_button_submit'}" class="button" />
            </div>
            <input type="hidden" name="teacher_id" value="{$smarty.post.teacher_id|default:$teacher->id|intval}" />
        </form>
    </fieldset>
{/block}