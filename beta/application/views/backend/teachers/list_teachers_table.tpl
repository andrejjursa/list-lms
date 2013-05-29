{foreach $teachers as $teacher}
<tr>
    <td>{$teacher->id|intval}</td>
    <td>{$teacher->fullname|escape:'html'}</td>
    <td>{$teacher->email|escape:'html'}</td>
    <td class="controlls"><a href="{internal_url url="admin_teachers/edit_teacher/teacher_id/{$teacher->id}"}" class="button">{translate line='admin_teachers_list_table_button_update'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_teachers/delete_teacher/teacher_id/{$teacher->id}"}" class="button delete">{translate line='admin_teachers_list_table_button_delete'}</a></td>
</tr>
{/foreach}