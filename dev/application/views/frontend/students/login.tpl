{extends file='layouts/frontend.tpl'}
{block title}{translate line='students_login_welcome_text'}{/block}
{block main_content}
    <div id="loginbox">
        <div class="boxborder">
            <h1>{translate line='students_login_welcome_text'}</h1>
            <form action="{internal_url url='students/do_login'}" method="post">
                <p><label>{translate line='students_login_label_email'}:</label></p>
                <p><input type="text" name="student[email]" value="{$smarty.post.student.email|escape:'html'}" /></p>
                {form_error field='student[email]' left_delimiter = '<p class="error">' right_delimiter='</p>'}
                <p><label>{translate line='students_login_label_password'}:</label></p>
                <p><input type="password" name="student[password]" value="{$smarty.post.student.password|escape:'html'}" /></p>
                {form_error field='student[password]' left_delimiter = '<p class="error">' right_delimiter='</p>'}
                <p><input type="submit" name="button_submit" value="{translate line='students_login_submit_button_label'}" /></p>
            </form>
        </div>
    </div>
{/block}