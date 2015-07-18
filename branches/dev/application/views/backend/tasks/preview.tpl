{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    {if $task->exists()}
        <h3>{overlay|escape:'html' table='tasks' table_id=$task->id column='name' default=$task->name}</h3>
        <div class="text_content">
        {overlay|add_base_url table='tasks' table_id=$task->id column='text' default=$task->text}
        </div>
        <div id="preview_tasks_id">
            <ul>
            {foreach $files as $file}
                <li><a href="{internal_url url="tasks/download_file/{$task->id}/{$file.file|encode_for_url}"}" target="_blank">{$file.file|escape:'html'}</a></li>
            {/foreach}
            </ul>
        </div>
    {/if}
{/block}