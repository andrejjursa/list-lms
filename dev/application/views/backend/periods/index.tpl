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
        <table class="periods_table">
            <thead>
                <tr>
                    <th>{translate line='admin_periods_table_header_name'}</th>
                    <th>{translate line='admin_periods_table_header_relations_courses'}</th>
                    <th colspan="4" class="controlls">{translate line='admin_periods_table_header_controlls'}</th>
                </tr>
            </thead>
            <tbody id="periods_container_id">
            </tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_periods_delete_period_question"}'
    };
</script>{/block}