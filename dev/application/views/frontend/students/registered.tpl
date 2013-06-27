{extends file='layouts/frontend.tpl'}
{block title}{translate line='students_registration_welcome_text'}{/block}
{block main_content}
    <h1>{translate line='students_registration_welcome_text'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    <p>{translate line='students_registration_success'}</p>
    <p><a href="{internal_url url='students/login'}" class="button">{translate line='students_registration_login_button'}</a></p>
{/block}