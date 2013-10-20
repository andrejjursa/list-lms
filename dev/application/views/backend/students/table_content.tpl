{foreach $students as $student}
<tr>
    <td>{$student->id|intval}</td>
    <td>{$student->fullname|escape:'html'}</td>
    <td>{$student->email|escape:'html'}</td>
    <td class="controlls"><a href="{internal_url url="admin_students/edit/student_id/{$student->id}"}" class="button">{translate line='admin_students_table_button_update'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_students/delete/student_id/{$student->id}"}" class="button delete">{translate line='admin_students_table_button_delete'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_students/log_in_as_student/student_id/{$student->id}"}" class="button special login_button" target="_blank">{translate line='admin_students_table_button_login_as'}</a></td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="6">{include file='partials/backend_general/pagination.tpl' paged=$students->paged inline}</td>
</tr>