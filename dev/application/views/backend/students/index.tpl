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
        <a href="{internal_url url='admin_students/csv_import'}" class="button special">{translate line='admin_students_button_csv_import'}</a>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_students_fieldset_legend_all_students_accounts'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_students/table_constent'}" method="post" id="filter_form_id">
                <div class="field">
                    <label for="filter_fullname_id">{translate line='admin_students_filter_form_label_fullname'}:</label>
                    <p class="input"><input type="text" name="filter[fullname]" value="{$filter.fullname|escape:'html'}" id="filter_fullname_id" /></p>
                </div>
                <div class="field">
                    <label for="filter_email_id">{translate line='admin_students_filter_form_label_email'}:</label>
                    <p class="input"><input type="text" name="filter[email]" value="{$filter.email|escape:'html'}" id="filter_email_id" /></p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_students_filter_form_submit_button'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter[order_by_field]" value="{$filter.order_by_field|default:'fullname'}" />
                    <input type="hidden" name="filter[order_by_direction]" value="{$filter.order_by_direction|default:'asc'}" />
                </div>
            </form>
        </div>
        <table class="students_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="sort:fullname">{translate line='admin_students_table_header_fullname'}</th>
                    <th class="sort:email">{translate line='admin_students_table_header_email'}</th>
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