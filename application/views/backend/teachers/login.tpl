{extends file='layouts/backend_loginbox.tpl'}
{block title}{translate line='admin_teachers_login_page_title'}{/block}
{block main_content}
    <h1>{translate line='admin_teachers_login_welcome_title'}</h1>
    {include file='partials/backend_general/error_box.tpl' message=$general_error inline}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <form action="{internal_url url="admin_teachers/do_login/{$uri_params|implode_uri_params}"}" method="post" id="login_form_id">
        <div class="field_login">
            <label for="id_teacher_email">{translate line='admin_teachers_login_label_email'}:</label>
            <p class="input"><input type="text" name="teacher[email]" value="{$smarty.post.teacher.email|escape:'html'}" id="id_teacher_email" /></p>
            {form_error field='teacher[email]' left_delimiter = '<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field_login">
            <label for="id_teacher_password">{translate line='admin_teachers_login_label_password'}:</label>
            <p class="input"><input type="password" name="teacher[password]" value="" id="id_teacher_password" /></p>
            {form_error field='teacher[password]' left_delimiter = '<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="buttons_login">
            <input type="submit" name="submit_button" value="{translate line='admin_teachers_login_submit_button_text'}" class="button" />
        </div>
    </form>
{/block}