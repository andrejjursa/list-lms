{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_students_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_students_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_students_fieldset_legend_new_student_account'}</legend>
        <form action="{internal_url url='admin_students/create'}" method="post" id="new_student_form_id">
            {include file='backend/students/new_student_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_students_fieldset_legend_all_students_accounts'}</legend>
        <form action="{internal_url url='admin_students/table_constent'}" method="post" id="filter_form_id">
            <div class="buttons">
                <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
            </div>
        </form>
        <table class="students_table">
            <thead>
                <tr>
                    <th>{translate line='admin_students_table_header_fullname'}</th>
                    <th>{translate line='admin_students_table_header_email'}</th>
                    <th class="controlls" colspan="2">{translate line='admin_students_table_header_controlls'}</th>
                </tr>
            </thead>
            <tfoot id="table_pagination_footer_id"></tfoot>
            <tbody id="table_content_id"></tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_students_message_delete_question"}',
        after_delete: '{translate line="admin_students_message_after_delete"}'
    };
</script>{/block}