{foreach $students as $student}
<tr>
    <td>{$student->id|intval}</td>
    <td>{$student->fullname|escape:'html'}</td>
    <td>{$student->email|escape:'html'}</td>
    <td class="controlls"><a href="{internal_url url="admin_students/edit/student_id/{$student->id}"}" class="button" title="{translate line='admin_students_table_button_update'}"><span class="list-icon list-icon-edit"></span></a></td>
    <td class="controlls"><a href="{internal_url url="admin_students/delete/student_id/{$student->id}"}" class="button delete" title="{translate line='admin_students_table_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
    <td class="controlls"><a href="{internal_url url="admin_students/log_in_as_student/student_id/{$student->id}"}" class="button special login_button" target="_blank" title="{translate line='admin_students_table_button_login_as'}"><span class="list-icon list-icon-login-as"></span></a></td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="6">{include file='partials/backend_general/pagination.tpl' paged=$students->paged inline}</td>
</tr>