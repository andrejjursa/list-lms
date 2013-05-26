{foreach $task_sets as $task_set}
    <tr>
        <td>{$task_set->id|intval}</td>
        <td>{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</td>
        <td>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}</td>
        <td>{translate_text text=$task_set->group_name}</td>
        <td>{$task_set->solution_count}</td>
        <td>{$task_set->task_count}</td>
        <td class="controlls"><a href="{internal_url url="admin_solutions/solutions_list/{$task_set->id}"}" class="button special">{translate line='admin_solutions_table_button_select_task_set'}</a></td>
    </tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="7">{include file='partials/backend_general/pagination.tpl' paged=$task_sets->paged inline}</td>
</tr>