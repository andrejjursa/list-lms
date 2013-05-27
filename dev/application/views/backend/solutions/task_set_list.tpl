{foreach $task_sets as $task_set}
    <tr>
        <td>{$task_set->id|intval}</td>
        <td>{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</td>
        <td>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}</td>
        <td>{translate_text text=$task_set->group_name}</td>
        <td>{translate_text text=$task_set->task_set_type_name}</td>
        <td>{$task_set->solution_count}</td>
        <td>{$task_set->task_count}</td>
        <td>
            {if $task_set->join_upload_solution eq 1}
                {$task_set->upload_end_time|date_format:{translate line='admin_solutions_datetime_format'}|default:{translate line='admin_solutions_no_time_information'}}
            {else}
                {translate line='admin_solutions_no_solution_uploading'}
            {/if}
        </td>
        <td class="controlls"><a href="{internal_url url="admin_solutions/solutions_list/{$task_set->id}"}" class="button special">{translate line='admin_solutions_table_button_select_task_set'}</a></td>
    </tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="9">{include file='partials/backend_general/pagination.tpl' paged=$task_sets->paged inline}</td>
</tr>