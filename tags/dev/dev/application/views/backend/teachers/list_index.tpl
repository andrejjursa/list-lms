{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_teachers_list_page_header'}{/block}
{block main_content}
    <h2>{translate line='admin_teachers_list_page_header'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_teachers_list_fieldset_legend_create_new_account'}</legend>
        <form action="{internal_url url='admin_teachers/create_teacher'}" method="post" id="new_teacher_form_id">
            {include file='backend/teachers/new_teacher_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_teachers_list_fieldset_legend_all_accounts'}</legend>
        <table class="teachers_list_table">
            <thead>
                <th>ID</th>
                <th>{translate line='admin_teachers_list_table_header_fullname'}</th>
                <th>{translate line='admin_teachers_list_table_header_email'}</th>
                <th colspan="2" class="controlls">{translate line='admin_teachers_list_table_header_controlls'}</th>
            </thead>
            <tbody id="table_content_id"></tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate|addslashes line="admin_teachers_list_message_delete_question"}',
        after_delete: '{translate|addslashes line="admin_teachers_list_message_after_delete"}'
    };
</script>{/block}