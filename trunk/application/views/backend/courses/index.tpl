{extends file="layouts/backend.tpl"}
{block title}{translate line='admin_courses_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_courses_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl'}
    <fieldset>
        <legend>{translate line='admin_courses_fieldset_legend_new_course'}</legend>
        <form action="{internal_url url='admin_courses/create'}" method="post" id="new_course_form_id">
            {include file='backend/courses/new_course_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_courses_fieldset_legend_all_courses'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_groups/get_table_content'}" method="post" id="filter_form_id">
                <input type="hidden" name="filter[fields][created]" value="{$filter.fields.created|default:0}" />
                <input type="hidden" name="filter[fields][updated]" value="{$filter.fields.updated|default:0}" />
                <input type="hidden" name="filter[fields][name]" value="{$filter.fields.name|default:1}" />
                <input type="hidden" name="filter[fields][description]" value="{$filter.fields.description|default:1}" />
                <input type="hidden" name="filter[fields][period]" value="{$filter.fields.period|default:1}" />
                <input type="hidden" name="filter[fields][groups]" value="{$filter.fields.groups|default:1}" />
                <input type="hidden" name="filter[fields][task_set_types]" value="{$filter.fields.task_set_types|default:1}" />
                <input type="hidden" name="filter[fields][capacity]" value="{$filter.fields.capacity|default:1}" />
                <input type="hidden" name="filter[order_by_field]" value="{$filter.order_by_field|default:'period'}" />
                <input type="hidden" name="filter[order_by_direction]" value="{$filter.order_by_direction|default:'asc'}" />
            </form>
        </div>
        <div id="table_content"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_courses_message_delete_question"}',
        after_delete: '{translate line="admin_courses_message_after_delete"}',
    };
</script>{/block}