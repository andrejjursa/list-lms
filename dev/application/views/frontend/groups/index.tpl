{extends file='layouts/frontend.tpl'}
{block title}{translate line='groups_page_title'}{/block}
{block main_content}
    <h1>{translate line='groups_page_title'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend></legend>
    </fieldset>
{/block}