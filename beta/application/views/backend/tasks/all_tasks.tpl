<table class="tasks_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th class="sort:created">{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th class="sort:updated">{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th class="sort:name">{translate line='admin_tasks_table_header_name'}</th>{/if}
            {if $filter.fields.categories}<th>{translate line='admin_tasks_table_header_categories'}</th>{/if}
            {if $filter.fields.task_sets}<th class="sort:task_sets:desc">{translate line='admin_tasks_table_header_task_sets'}</th>{/if}
            {if $filter.fields.test_count}<th class="sort:test_count:desc">{translate line='admin_tasks_table_header_test_count'}</th>{/if}
            {if $filter.fields.author}<th class="sort:author">{translate line='admin_tasks_table_header_author'}</th>{/if}
            <th colspan="5" class="controlls"><div id="open_fields_config_id">{translate line='admin_tasks_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tfoot id="table_pagination_footer_id">
        <tr>
            <td colspan="{6 + $filter.fields|sum_array}">{include file='partials/backend_general/pagination.tpl' paged=$tasks->paged inline}</td>
        </tr>
    </tfoot>
    <tbody>
        {foreach $tasks as $task}
        <tr{if $task->created gte date('Y-m-d H:i:s', strtotime('now -2 hours'))} class="new_task"{/if}>
            <td>{$task->id|intval}</td>
            {if $filter.fields.created}<td>{$task->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.updated}<td>{$task->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.name}<td><strong>{overlay|escape:'html' table='tasks' table_id=$task->id column='name' default=$task->name}</strong></td>{/if}
            {if $filter.fields.categories}<td>{foreach $task->category->order_by('name', 'asc')->get_iterated() as $category}{if !$category@first}, {/if}{translate_text|escape:'html' text=$category->name}{/foreach}</td>{/if}
            {if $filter.fields.task_sets}<td>{$task->task_set_count}</td>{/if}
            {if $filter.fields.test_count}<td>{$task->test_count}</td>{/if}
            {if $filter.fields.author}<td>{$task->author_fullname|default:{translate line='admin_tasks_table_content_unknown_author'}}</td>{/if}
            <td class="controlls"><a href="{internal_url url="admin_tasks/clone_task/{$task->id}"}" class="button special clone_task" title="{translate line='admin_tasks_form_button_clone_task'}"><span class="list-icon list-icon-copy"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_tasks/add_to_task_set/task_id/{$task->id}"}" class="button special add_to_task_set" title="{translate line='admin_tasks_form_button_add_to_task_set_button'} - {translate line='admin_tasks_form_button_add_to_task_set'}"><span class="list-icon list-icon-build"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_tasks/preview/task_id/{$task->id}"}" class="button special preview" title="{translate line='admin_tasks_form_button_preview'}"><span class="list-icon list-icon-page-preview"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_tasks/edit/task_id/{$task->id}"}" class="button" title="{translate line='admin_tasks_form_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_tasks/delete/task_id/{$task->id}"}" class="button delete" title="{translate line='admin_tasks_form_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
        </tr>
        {/foreach}
    </tbody>
</table>