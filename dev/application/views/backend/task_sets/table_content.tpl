<table class="task_sets_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th>{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th>{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th>{translate line='admin_task_sets_table_header_name'}</th>{/if}
            {if $filter.fields.course}<th>{translate line='admin_task_sets_table_header_course'}</th>{/if}
            {if $filter.fields.group}<th>{translate line='admin_task_sets_table_header_group'}</th>{/if}
            {if $filter.fields.task_set_type}<th>{translate line='admin_task_sets_table_header_task_set_type'}</th>{/if}
            {if $filter.fields.tasks}<th>{translate line='admin_task_sets_table_header_tasks'}</th>{/if}
            {if $filter.fields.published}<th>{translate line='admin_task_sets_table_header_published'}</th>{/if}
            <th colspan="3" class="controlls"><div id="open_fields_config_id">{translate line='admin_task_sets_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tfoot id="table_pagination_footer_id">
        <tr>
            <td colspan="{4 + $filter.fields|sum_array}">{include file='partials/backend_general/pagination.tpl' paged=$task_sets->paged inline}</td>
        </tr>
    </tfoot>
    <tbody>
        {foreach $task_sets as $task_set}
        <tr{if $opened_task_set->exists() and $opened_task_set->id eq $task_set->id} class="opened_task_set"{/if}>
            <td>{$task_set->id|intval}</td>
            {if $filter.fields.created}<td>{$task_set->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.updated}<td>{$task_set->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.name}<td>{overlay|escape:'html' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</td>{/if}
            {if $filter.fields.course}<td>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}</td>{/if}
            {if $filter.fields.group}<td>{translate_text text=$task_set->group_name}</td>{/if}
            {if $filter.fields.task_set_type}<td>{translate_text text=$task_set->task_set_type_name}</td>{/if}
            {if $filter.fields.tasks}<td>{$task_set->task_count}</td>{/if}
            {if $filter.fields.published}<td>{if $task_set->published eq 1}{translate line='admin_task_sets_table_field_published_yes'}{else}{translate line='admin_task_sets_table_field_published_no'}{/if}</td>{/if}
            <td class="controlls"><a href="{internal_url url="admin_task_sets/open/task_set_id/{$task_set->id}"}" class="button special open_task_set_button">{translate line='admin_task_sets_table_button_open'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_task_sets/edit/task_set_id/{$task_set->id}"}" class="button">{translate line='admin_task_sets_table_button_edit'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_task_sets/delete/task_set_id/{$task_set->id}"}" class="button delete">{translate line='admin_task_sets_table_button_delete'}</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>