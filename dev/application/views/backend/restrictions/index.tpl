{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_restrictions_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_restrictions_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_restrictions_fieldset_legend_new_restriction'}</legend>
        <form action="{internal_url url='admin_restrictions/create'}" method="post" id="new_restriction_form_id">
            {include file='backend/restrictions/new_restriction_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_restrictions_fieldset_legend_all_restriction'}</legend>
        <div id="table_container_id"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        'delete_question': '{translate|addslashes line='admin_restrictions_js_messages_delete_question'}'
    };
</script>{/block}