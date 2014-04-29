{extends file='widgets/layouts/admin/simple.tpl'}
{block widget_name}{translate line='widget_admin_unevaluated_solutions_title'}{if $course and $course->exists()} - {translate_text text=$course->name} / {translate_text text=$course->period_name}{/if}{/block}
{block widget_content}
    {if $course and $course->exists()}
        <div class="widget_unevaluated_solutions_content">
            <p><strong>{translate line='widget_admin_unevaluated_solutions_list_of_task_sets'}:</strong></p>
            {foreach $task_sets as $task_set}
                <div class="widget_unevaluated_solutions_task_set"><a href="{internal_url url="admin_solutions/solutions_list/{$task_set->id}"}">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</a> ({$task_set->solutions_count})</div>
            {foreachelse}
                <p><em>{translate line='widget_admin_unevaluated_solutions_list_of_task_sets_empty'}</em></p>
            {/foreach}
        </div>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:widget_admin_unevaluated_solutions_error_course_not_found' inline}
    {/if}
{/block}