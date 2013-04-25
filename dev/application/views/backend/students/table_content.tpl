{foreach $students as $student}
<tr>
    <td>{$student->fullname|escape:'html'}</td>
    <td>{$student->email|escape:'html'}</td>
    <td class="controlls"><a href="{internal_url url="admin_students/edit/student_id/{$student->id}"}" class="button">{translate line='admin_students_table_button_update'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_students/delete/student_id/{$student->id}"}" class="button delete">{translate line='admin_students_table_button_delete'}</a></td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="4">{include file='partials/backend_general/pagination.tpl' paged=$students->paged inline}</td>
</tr>