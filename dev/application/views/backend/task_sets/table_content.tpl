{foreach $task_sets as $task_set}
<tr>
    <td>{$task_set->id|intval}</td>
    <td>{overlay|escape:'html' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</td>
    <td>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}</td>
    <td>{translate_text text=$task_set->group_name}</td>
    <td>{translate_text text=$task_set->task_set_type_name}</td>
    <td>{$task_set->task_count}</td>
    <td>{if $task_set->published eq 1}{translate line='admin_task_sets_table_field_published_yes'}{else}{translate line='admin_task_sets_table_field_published_no'}{/if}</td>
    <td class="controlls"><a href="{internal_url url="admin_task_sets/open/task_set_id/{$task_set->id}"}" class="button special open_task_set_button">{translate line='admin_task_sets_table_button_open'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_task_sets/edit/task_set_id/{$task_set->id}"}" class="button">{translate line='admin_task_sets_table_button_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_task_sets/delete/task_set_id/{$task_set->id}"}" class="button delete">{translate line='admin_task_sets_table_button_delete'}</a></td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="10">{include file='partials/backend_general/pagination.tpl' paged=$task_sets->paged inline}</td>
</tr>