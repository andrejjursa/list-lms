{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_groups_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_groups_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend></legend>
        <form action="{internal_url url='admin_groups/create'}" method="post" id="groups_form_id">
            {include file='backend/groups/new_group_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend></legend>
        <div id="table_of_groups_container_id">
        </div>
    </fieldset>
{/block}