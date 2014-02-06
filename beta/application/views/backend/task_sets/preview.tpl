{extends file="layouts/backend_popup.tpl"}
{block title}{translate line='admin_task_sets_preview_title'}{/block}
{block main_content}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $task_set->exists()}
        <h3 class="task_name">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</h3>
        {$instructions_text = {overlay table='task_sets' table_id=$task_set->id|intval column='instructions' default=$task_set->instructions}}
        {if $instructions_text}
        <h3>{translate line='admin_task_sets_preview_instructions_header'}</h3>
        <div class="instructions_text text_content">
            {$instructions_text|add_base_url}
        </div>
        {/if}
        {$tasks = $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get()}
        {$this->lang->init_overlays('tasks', $tasks->all, ['name', 'text'])}
        {foreach $tasks->all as $task}
        <h3>{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}{if $task->join_bonus_task} <span class="bonus_task">({translate line='tasks_task_is_bonus_task'})</span>{/if}</h3>
        <div class="task_text text_content">
        {overlay|add_base_url table='tasks' table_id=$task->id column='text' default=$task->text}
        </div>{$files = $task->get_task_files()}
        {if count($files) > 0}
        <div class="task_files">
            <div class="task_files_title">{translate line='admin_task_sets_preview_task_files_title'}:</div>
            {foreach $files as $file}
            <div class="task_file">
                <a href="{internal_url url="tasks/download_file/{$task->id}/{$file.file|encode_for_url}"}">{$file.file}</a> ({$file.size})
            </div>
            {/foreach}
        </div>
        {/if}
        <hr />
        <div class="task_points">{translate|sprintf:{$task->join_points_total|floatval} line='admin_task_sets_preview_points_for_task'}</div>
        <div class="task_author">{translate|sprintf:{$task->author->get()->fullname|default:{translate line='admin_task_sets_preview_author_unknown'}} line='admin_task_sets_preview_task_author'}</div>
        <hr />
        {/foreach}
    {else}
        {include file="partials/backend_general/error_box.tpl" message="lang:admin_task_sets_error_task_set_not_found" inline}
    {/if}
{/block}