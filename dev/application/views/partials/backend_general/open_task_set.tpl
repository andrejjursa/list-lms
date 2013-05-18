<span class="header_open_task_set">
    {if $list_open_task_set->exists()}
    {capture name='open_task_set_capture_block' assign='list_open_task_set_edit_link'}<a href="{internal_url url="admin_task_sets/edit/task_set_id/{$list_open_task_set->id}"}" title="{translate_text text=$list_open_task_set->course->get()->name}/{translate_text text=$list_open_task_set->course->period->get()->name}">{overlay|escape:'html' table='task_sets' table_id=$list_open_task_set->id column='name' default=$list_open_task_set->name}</a>{/capture}
        {translate|sprintf:$list_open_task_set_edit_link:$list_open_task_set->task->count() line='adminmenu_open_task_set_message'}
    {else}
        {translate|sprintf:{translate line='adminmenu_open_task_set_nothing_opened'}:0 line='adminmenu_open_task_set_message'}
    {/if}
</span>