{foreach $task_set_types as $task_set_type}
<tr>
    <td>{translate_text|escape:'html' text=$task_set_type->name}</td>
    <td class="controlls"><a href="{internal_url url="admin_task_set_types/edit/task_set_type_id/{$task_set_type->id}"}" class="button">{translate line='admin_task_set_types_table_buttons_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_task_set_types/delete/task_set_type_id/{$task_set_type->id}"}" class="button delete">{translate line='admin_task_set_types_table_buttons_delete'}</a></td>
</tr>
{/foreach}