{foreach $tasks as $task}
<tr>
    <td>{overlay|escape:'html' table='tasks' table_id=$task->id column='name' default=$task->name}</td>
    <td>{foreach $task->category->order_by('name', 'asc')->get_iterated() as $category}{if !$category@first}, {/if}{translate_text|escape:'html' text=$category->name}{/foreach}</td>
    <td>{$task->task_set->count()}</td>
    <td class="controlls"><a href="{internal_url url="admin_tasks/edit/task_id/{$task->id}"}" class="button">{translate line='admin_tasks_form_button_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_tasks/delete/task_id/{$task->id}"}" class="button delete">{translate line='admin_tasks_form_button_delete'}</a></td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="5">{include file='partials/backend_general/pagination.tpl' paged=$tasks->paged inline}</td>
</tr>