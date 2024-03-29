{extends file='layouts/frontend_loginbox.tpl'}
{block title}{translate line='students_login_welcome_text'}{/block}
{block main_content}
    <div id="loginbox">
        <div class="boxborder">
            <div class="loginbox">
                <h1>{translate line='students_login_welcome_text'}</h1>
                {if $general_error}
                    {include file='partials/frontend_general/error_box.tpl' message=$general_error inline}
                {/if}
                {include file='partials/frontend_general/flash_messages.tpl' inline}
                <form action="{internal_url url="students/do_login/{$uri_params|implode_uri_params}"}" method="post">
                    <div class="field_login">
                        <label for="id_students_email">{translate line='students_login_label_email'}:</label>
                        <p class="input"><input type="text" name="student[email]" value="{$smarty.post.student.email|escape:'html'}" id="id_students_email" /></p>
                        {form_error field='student[email]' left_delimiter = '<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field_login">
                        <label for="id_students_password">{translate line='students_login_label_password'}:</label>
                        <p class="input"><input type="password" name="student[password]" value="" id="id_students_password" /></p>
                        {form_error field='student[password]' left_delimiter = '<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="buttons">
                        <input type="submit" name="button_submit" value="{translate line='students_login_submit_button_label'}" class="button" />
                        <a href="{internal_url url="students/password_recovery/{$uri_params|implode_uri_params}"}" class="button special">{translate line='students_login_forgoten_password'}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}