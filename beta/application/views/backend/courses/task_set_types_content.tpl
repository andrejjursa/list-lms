{foreach $task_set_types as $task_set_type}
<tr class="task_set_types_table_row">
    <td>{translate_text|escape:'html' text=$task_set_type->name}</td>
    <td><select name="upload_solution" size="1">
        {list_html_options options=[1 => {translate line='admin_courses_form_select_option_upload_solution_yes'}, 0 => {translate line='admin_courses_form_select_option_upload_solution_no'}]
         selected=$task_set_type->join_upload_solution|intval}
    </select><input type="hidden" value="{$task_set_type->id|intval}" name="task_set_type_id"><input type="hidden" value="{$course->id|intval}" name="course_id"></td>
    <td class="controlls"><a href="{internal_url url="admin_courses/save_task_set_type"}" class="button save_button">{translate line='admin_courses_form_button_submit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_courses/delete_task_set_type"}" class="button delete">{translate line='admin_courses_table_controlls_delete'}</a></td>
</tr>
{/foreach}