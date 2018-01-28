{extends file="layouts/backend.tpl"}
{block title}{translate line='admin_course_content_groups_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_groups_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl'}
    <fieldset>
        <legend>{translate line='admin_course_content_groups_fieldset_legend_new_group'}</legend>
        <form action="{internal_url url='admin_course_content_groups/create'}" method="post" id="new_content_group_form_id">
            {include file='backend/course_content_groups/new_content_group_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_course_content_groups_fieldset_legend_all_groups'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_course_content_groups/get_all_content_groups'}" method="post" id="filter_form_id">
                <div class="field">
                    <label for="filter_course_id_id">{translate line='admin_course_content_groups_filter_by_course'}:</label>
                    <p class="input">
                        <select name="filter[course_id]" size="1" id="filter_course_id_id">{list_html_options options=$courses selected=$filter.course_id}</select>
                    </p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_course_content_groups_filter_submit_button'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter[order_by_field]" value="{$filter.order_by_field|default:'title'}" />
                    <input type="hidden" name="filter[order_by_direction]" value="{$filter.order_by_direction|default:'asc'}" />
                </div>
            </form>
        </div>
        <div id="table_content"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
</script>{/block}