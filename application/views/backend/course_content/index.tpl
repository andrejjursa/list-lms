{extends file="layouts/backend.tpl"}
{block title}{translate line='admin_course_content_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl'}
    {if not $is_writable}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_course_content_error_write_disabled'}
    {/if}
    <fieldset>
        <legend>{translate line='admin_course_content_fieldset_legend_new_content'}</legend>
        <form action="{internal_url url='admin_course_content/create'}" method="post" id="new_content_form_id"{if not $is_writable} data-disabled="disabled"{/if}>
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
    var data = {
        'all_course_content_groups': {$all_course_content_groups|json_encode}
    };
    var highlighters = {$highlighters|json_encode};
    var message_write_disabled = '{translate line='admin_course_content_error_cant_save_form'}';
    var languages = {$languages|json_encode};
    var delete_file_question = '{translate line='admin_course_content_delete_file_question'}';
    var show_uploader_text = '{translate line='admin_course_content_text_show_uploader'}';
</script>{/block}