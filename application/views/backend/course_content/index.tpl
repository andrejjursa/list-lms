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
            <form action="{internal_url url='admin_course_content/get_all_content'}" method="post" id="filter_form_id">
                <div class="field">
                    <label for="filter_course_id_id">{translate line='admin_course_content_filter_by_course'}:</label>
                    <p class="input">
                        <select name="filter[course_id]" size="1" id="filter_course_id_id">{list_html_options options=$courses selected=$filter.course_id}</select>
                    </p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_course_content_filter_submit_button'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter[order_by_field]" value="{$filter.order_by_field|default:'title'}" />
                    <input type="hidden" name="filter[order_by_direction]" value="{$filter.order_by_direction|default:'asc'}" />
                </div>
            </form>
        </div>
        <div id="table_content"></div>
        <table class="course_content_legend_table">
            <caption>{translate line='admin_course_content_legend_table_caption'}</caption>
            <tr>
                <td></td>
                <td class="header">{translate line='admin_course_content_header_public_yes'}</td>
                <td class="header">{translate line='admin_course_content_header_public_no'}</td>
            </tr>
            <tr>
                <td class="header">{translate line='admin_course_content_header_published_yes'}</td>
                <td class="course_content_legend_table_published_public"></td>
                <td class="course_content_legend_table_published_nonpublic"></td>
            </tr>
            <tr>
                <td class="header">{translate line='admin_course_content_header_published_no'}</td>
                <td class="course_content_legend_table_nonpublished_public"></td>
                <td class="course_content_legend_table_nonpublished_nonpublic"></td>
            </tr>
        </table>
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
    var coppied_to_clipboard = '{translate line='admin_course_content_text_coppied_to_clipboard'}';
    var delete_content_question = '{translate line='admin_course_content_delete_content_question'}';
</script>{/block}