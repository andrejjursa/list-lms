{if $course->exists()}
    {foreach $all_task_sets as $task_set_type_id => $task_set_type_data}
        <fieldset class="basefieldset sorting_list">
            <legend>{translate_text text={overlay table='task_set_types' table_id=$task_set_type_id column='name' default=$task_set_type_data.name}}</legend>
            <div data-task-set-type-id="{$task_set_type_id}">
                {include file='./partial/single_type_task_sets_sorting.tpl'}
            </div>
        </fieldset>
    {/foreach}
{else}
    {include file='partials/backend_general/error_box.tpl' message={translate line='admin_task_sets_sorting_error_course_not_selected'} inline}
{/if}