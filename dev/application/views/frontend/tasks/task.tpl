{extends file='layouts/frontend.tpl'}
{block title}{translate text='tasks_task_page_header'}{/block}
{block main_content}
    <h1>{translate line='tasks_task_page_header'}</h1>
    {if $course->exists()}
        {if $task_set->exists()}
            <h2 class="task_name">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</h2>
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            <div id="tabs">
                <ul style="height: 37px;">
                    <li><a href="#tabs-task">{translate line='tasks_task_tabs_task'}</a></li>
                    <li><a href="#tabs-solution">{translate line='tasks_task_tabs_solutions'}</a></li>
                </ul>
                <div id="tabs-task">
                    {foreach $task_set->task->include_join_fields()->order_by_join_field('sorting', 'asc')->get_iterated() as $task}
                    <h3>{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}</h3>
                    <div class="task_text">
                    {overlay|task table='tasks' table_id=$task->id column='text' default=$task->text}
                    </div>{$files = $task->get_task_files()}
                    {if count($files) > 0}
                    <div class="task_files">
                        <div class="task_files_title">{translate line='tasks_task_task_files_title'}:</div>
                        {foreach $files as $file}
                        <div class="task_file">
                            <a href="{internal_url url="tasks/download_file/{$task->id}/{$file.file|encode_for_url}"}">{$file.file}</a> ({$file.size})
                        </div>
                        {/foreach}
                    </div>
                    {/if}
                    <div class="task_points">{translate|sprintf:{$task->join_points_total|floatval} line='tasks_task_points_for_task'}</div>
                    {/foreach}
                </div>
                <div id="tabs-solution"></div>
            </div>
        {else}
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_task_task_set_not_found' inline}
        {/if}
    {else}
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_no_active_course' inline}
    {/if}
{/block}