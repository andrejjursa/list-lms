{if $course->exists()}
    {foreach $all_task_sets as $task_set_type_id => $task_set_type_data}
        <fieldset class="basefieldset">
            <legend>{overlay table='task_set_types' table_id=$task_set_type_id column='name' default=$task_set_type_data.name}</legend>
            <ul data-task-set-type-id="{$task_set_type_id}" data-course-id="{$course->id}">
                {foreach $task_set_type_data.items as $task_set}
                    <li data-id="{$task_set.id}">{overlay table='task_sets' table_id=$task_set.id column='name' default=$task_set.name}</li>
                {/foreach}
            </ul>
        </fieldset>
    {/foreach}
{else}
    {include file='partials/backend_general/error_box.tpl' message={translate line='admin_task_sets_sorting_error_course_not_selected'} inline}
{/if}