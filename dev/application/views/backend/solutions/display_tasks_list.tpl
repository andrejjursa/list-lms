{$instructions_text = {overlay table='task_sets' table_id=$task_set->id|intval column='instructions' default=$task_set->instructions}}
{if $instructions_text}
    <div class="instructions_wrap">
        <h5>{translate line='admin_solutions_tasks_list_instructions_header'}</h5>
        <div class="instructions_text">
            {$instructions_text|add_base_url}
        </div>
    </div>
{/if}
{foreach $tasks as $task}
    <div class="task_wrap">
        <h5>{$task@iteration}. {overlay table='tasks' column='name' table_id=$task->id default=$task->name} | <span class="task_points">{$task->join_points_total|floatval}</span> | <span class="task_author">{$task->author->get()->fullname|default:{translate line='admin_solutions_tasks_list_task_unknown_author'}}</span>{if $task->join_bonus_task} | <span class="bonus_task">{translate line='admin_solutions_task_list_is_bonus_task'}</span>{/if}</h5>
        <div class="task_text">{overlay|task|add_base_url table='tasks' column='text' table_id=$task->id default=$task->text}</div>
    </div>
{/foreach}
<script type="text/javascript">
jQuery(document).ready(function($) {
    if (typeof prettyPrint !== 'undefined') {
        prettyPrint();
    }
});
</script>