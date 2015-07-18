<table class="tests_evaluation_table">
    <thead>
        <tr>
            <th class="task_name">{translate line='admin_tests_evaluation_table_header_task_name'}</th>
            <th class="percentage">{translate line='admin_tests_evaluation_table_header_percentage'}</th>
            <th class="points">{translate line='admin_tests_evaluation_table_header_points'}</th>
            <th class="type">{translate line='admin_tests_evaluation_table_header_type'}</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="4">
                <em>{translate|sprintf:$max_results line='admin_tests_evaluation_table_footer'}</em>
            </td>
        </tr>
    </tfoot>
    <tbody>
        {foreach $tasks as $task}
            <tr>
                <td class="task_name">{overlay table='tasks' column='name' table_id=$task->id default=$task->name}</td>
                <td class="percentage">{if array_key_exists($task->id, $real_percentage)}{$real_percentage[$task->id]*100}%{elseif array_key_exists($task->id, $bonus_percentage)}{$bonus_percentage[$task->id]*100}%{else}-{/if}</td>
                <td class="points">{if array_key_exists($task->id, $real_points)}{$real_points[$task->id]}{elseif array_key_exists($task->id, $bonus_points)}{$bonus_points[$task->id]}{else}-{/if}</td>
                <td class="type">{if array_key_exists($task->id, $real_points)}{translate line='admin_tests_evaluation_type_bonus_no'}{elseif array_key_exists($task->id, $bonus_points)}{translate line='admin_tests_evaluation_type_bonus_yes'}{else}-{/if}</td>
            </tr>
        {/foreach}
    </tbody>
</table>