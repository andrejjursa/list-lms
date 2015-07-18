{foreach $teachers as $teacher}
<tr>
    <td>{$teacher->id|intval}</td>
    <td>{$teacher->fullname|escape:'html'}</td>
    <td>{$teacher->email|escape:'html'}</td>
    <td class="controlls"><a href="{internal_url url="admin_teachers/edit_teacher/teacher_id/{$teacher->id}"}" class="button" title="{translate line='admin_teachers_list_table_button_update'}"><span class="list-icon list-icon-edit"></span></a></td>
    <td class="controlls"><a href="{internal_url url="admin_teachers/delete_teacher/teacher_id/{$teacher->id}"}" class="button delete" title="{translate line='admin_teachers_list_table_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
</tr>
{/foreach}