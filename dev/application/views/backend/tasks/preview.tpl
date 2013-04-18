{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    {if $task->exists()}
        <h3>{overlay|escape:'html' table='tasks' table_id=$task->id column='name' default=$task->name}</h3>
        {overlay|task table='tasks' table_id=$task->id column='text' default=$task->text}
    {else}
    {/if}
{/block}