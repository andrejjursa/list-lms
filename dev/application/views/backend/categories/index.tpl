{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_categories_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_categories_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_categories_fieldset_legend_new_category'}</legend>
        <form action="{internal_url url='admin_categories/create'}" method="post" id="new_category_form_id">
            {include file='backend/categories/new_category_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_categories_fieldset_legend_categories_tree'}</legend>
        <div id="category_tree_id">
            
        </div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_categories_message_delete_question"}'
    };
</script>{/block}