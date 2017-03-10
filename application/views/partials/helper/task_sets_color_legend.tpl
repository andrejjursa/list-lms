<table class="task_sets_colors_legend">
    <caption>{translate line='common_task_sets_colors_table'}</caption>
    <tr>
        <td class="task_set_time_long_after_deadline">{translate line='common_task_sets_colors_long_after_deadline'}</td>
        <td class="task_set_time_after_deadline">{translate line='common_task_sets_colors_after_deadline'}</td>
        <td class="task_set_time_day_before_deadline">{translate line='common_task_sets_colors_day_before_deadline'}</td>
        <td class="task_set_time_two_days_before_deadline">{translate line='common_task_sets_colors_two_days_before_deadline'}</td>
        <td class="task_set_time_week_before_deadline">{translate line='common_task_sets_colors_week_before_deadline'}</td>
        <td class="task_set_time_long_before_deadline">{translate line='common_task_sets_colors_long_before_deadline'}</td>
        {if !$admin}
            <td class="task_set_time_after_deadline_with_submits">{translate line='common_task_sets_colors_after_deadline_with_submits'}</td>
        {else}
            <td class="task_set_not_published">{translate line='common_task_sets_colors_not_published'}</td>
        {/if}
    </tr>
</table>