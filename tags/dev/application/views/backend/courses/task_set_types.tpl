{extends file='layouts/backend_popup.tpl'}
{block title}{translate|sprintf:{translate_text|default:{translate line='admin_courses_error_course_not_found'} text=$course->name} line='admin_course_task_set_types_editor_page_header'}{/block}
{block main_content}
    <h3>{translate|sprintf:{translate_text|default:{translate line='admin_courses_error_course_not_found'} text=$course->name} line='admin_course_task_set_types_editor_page_header'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $course->exists()}
    <fieldset>
        <legend>{translate line='admin_courses_fieldset_legend_add_task_set_type'}</legend>
        <form action="{internal_url url="admin_courses/add_task_set_type/course_id/{$course->id}"}" method="post" id="add_task_set_type_form_id">
            {include file='backend/courses/add_task_set_type_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_courses_fieldset_legend_all_task_set_types'}</legend>
        <table class="task_set_types_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{translate line='admin_courses_table_header_task_set_type'}</th>
                    <th>{translate line='admin_courses_table_header_upload_solution'}</th>
                    <th colspan="2" class="controlls">{translate line='admin_courses_table_header_controlls'}</th>
                </tr>
            </thead>
            <tbody id="table_content_id">
            </tbody>
        </table>
    </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_error_course_not_found' inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate|addslashes line="admin_courses_message_delete_task_set_type_question"}',
        save_success: '{translate|addslashes line="admin_courses_message_task_set_type_updated"}',
        save_failed: '{translate|addslashes line="admin_courses_message_task_set_type_update_failed"}',
        delete_success: '{translate|addslashes line="admin_courses_message_task_set_type_deleted"}',
        delete_failed: '{translate|addslashes line="admin_courses_message_task_set_type_delete_failed"}',
    };
    var current_course = {$course->id|default:0|intval};
</script>{/block}