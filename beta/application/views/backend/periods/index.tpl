{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_periods_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_periods_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_periods_fieldset_legend_new_period'}</legend>
        <form action="{internal_url url='admin_periods/create'}" method="post" id="new_period_form_id">
            {include file='backend/periods/new_period_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_periods_fieldset_legend_periods'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_groups/get_table_content'}" method="post" id="filter_form_id">
                <input type="hidden" name="filter[fields][created]" value="{$filter.fields.created|default:0}" />
                <input type="hidden" name="filter[fields][updated]" value="{$filter.fields.updated|default:0}" />
                <input type="hidden" name="filter[fields][name]" value="{$filter.fields.name|default:1}" />
                <input type="hidden" name="filter[fields][related_courses]" value="{$filter.fields.related_courses|default:1}" />
            </form>
        </div>
        <div id="periods_container_id"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_periods_delete_period_question"}',
        after_delete: '{translate line="admin_periods_after_delete_message"}',
    };
</script>{/block}