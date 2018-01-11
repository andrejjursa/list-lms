{extends file="layouts/backend.tpl"}
{block title}{translate line='admin_course_content_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl'}
    <fieldset>
        <legend>{translate line='admin_course_content_fieldset_legend_new_content'}</legend>
        <form action="{internal_url url='admin_course_content/create'}" method="post" id="new_content_form_id">
            {include file='backend/course_content/new_content_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_course_content_fieldset_legend_all_content'}</legend>
        <div class="filter_wrap">
        </div>
        <div id="table_content"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
</script>{/block}