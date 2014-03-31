<table class="task_sets_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th class="sort:created">{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th class="sort:updated">{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th class="sort:name">{translate line='admin_task_sets_table_header_name'}</th>{/if}
            {if $filter.fields.content_type}<th class="sort:content_type">{translate line='admin_task_sets_table_header_content_type'}</th>{/if}
            {if $filter.fields.course}<th class="sort:course">{translate line='admin_task_sets_table_header_course'}</th>{/if}
            {if $filter.fields.group}<th class="sort:group">{translate line='admin_task_sets_table_header_group'}</th>{/if}
            {if $filter.fields.task_set_type}<th class="sort:task_set_type">{translate line='admin_task_sets_table_header_task_set_type'}</th>{/if}
            {if $filter.fields.tasks}<th class="sort:tasks:desc">{translate line='admin_task_sets_table_header_tasks'}</th>{/if}
            {if $filter.fields.published}<th class="sort:published:desc">{translate line='admin_task_sets_table_header_published'}</th>{/if}
            {if $filter.fields.publish_start_time}<th class="sort:publish_start_time:desc">{translate line='admin_task_sets_table_header_publish_start_time'}</th>{/if}
            {if $filter.fields.upload_end_time}<th class="sort:upload_end_time:desc">{translate line='admin_task_sets_table_header_upload_end_time'}</th>{/if}
            {if $filter.fields.project_selection_deadline}<th class="sort:project_selection_deadline:desc">{translate line='admin_task_sets_table_header_project_selection_deadline'}</th>{/if}
            <th colspan="6" class="controlls"><div id="open_fields_config_id">{translate line='admin_task_sets_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tfoot id="table_pagination_footer_id">
        <tr>
            <td colspan="{7 + $filter.fields|sum_array}">{include file='partials/backend_general/pagination.tpl' paged=$task_sets->paged inline}</td>
        </tr>
    </tfoot>
    <tbody>
        {foreach $task_sets as $task_set}
        {if $task_set->task_set_permission_count ne 0}
            {$task_set_permissions = $task_set->task_set_permission->where('enabled', 1)->include_related('group')->order_by_related_with_constant('group', 'name', 'asc')->get()}
        {/if}
        <tr{if $opened_task_set->exists() and $opened_task_set->id eq $task_set->id} class="opened_task_set"{/if}>
            <td>{$task_set->id|intval}</td>
            {if $filter.fields.created}<td>{$task_set->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.updated}<td>{$task_set->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.name}<td><strong>{overlay|escape:'html' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</strong></td>{/if}
            {if $filter.fields.content_type}<td>{translate line="admin_task_sets_form_label_content_type_{$task_set->content_type}"}</td>{/if}
            {if $filter.fields.course}<td><span title="{translate_text text=$task_set->course_name}">{translate_text|abbreviation text=$task_set->course_name}</span> / <span title="{translate_text text=$task_set->course_period_name}">{translate_text|abbreviation text=$task_set->course_period_name}</span></td>{/if}
            {if $filter.fields.group}<td>
                {if $task_set->task_set_permission_count eq 0}
                    <span title="{translate_text text=$task_set->group_name}">{translate_text|abbreviation text=$task_set->group_name}</span>
                {else}
                    <ol>
                    {foreach $task_set_permissions->all as $task_set_permission}
                        <li><span title="{translate_text text=$task_set_permission->group_name}">{translate_text|abbreviation text=$task_set_permission->group_name}</span></li>
                    {/foreach}
                    </ol>
                {/if}
            </td>{/if}
            {if $filter.fields.task_set_type}<td>{translate_text text=$task_set->task_set_type_name}</td>{/if}
            {if $filter.fields.tasks}<td>{$task_set->task_count}</td>{/if}
            {if $filter.fields.published}<td>{if $task_set->published eq 1}{translate line='admin_task_sets_table_field_published_yes'}{else}{translate line='admin_task_sets_table_field_published_no'}{/if}</td>{/if}
            {if $filter.fields.publish_start_time}<td>
                {if $task_set->task_set_permission_count eq 0}
                    {$task_set->publish_start_time|date_format:{translate line='common_datetime_format'}}
                {else}
                    <ol>
                    {foreach $task_set_permissions->all as $task_set_permission}
                        <li>{$task_set_permission->publish_start_time|date_format:{translate line='common_datetime_format'}}</li>
                    {/foreach}
                    </ol>
                {/if}
            </td>{/if}
            {if $filter.fields.upload_end_time}<td>
                {if $task_set->task_set_permission_count eq 0}
                    {$task_set->upload_end_time|date_format:{translate line='common_datetime_format'}}
                {else}
                    <ol>
                    {foreach $task_set_permissions->all as $task_set_permission}
                        <li>{$task_set_permission->upload_end_time|date_format:{translate line='common_datetime_format'}}</li>
                    {/foreach}
                    </ol>
                {/if}
            </td>{/if}
            {if $filter.fields.project_selection_deadline}<td>{$task_set->project_selection_deadline|date_format:{translate line='common_datetime_format'}}</td>{/if}
            <td class="controlls"><a href="{internal_url url="admin_task_sets/clone_task_set/task_set_id/{$task_set->id}"}" class="button special clone_task_set" title="{translate line='admin_task_sets_table_button_clone_task_set'}"><span class="list-icon list-icon-copy"></span></a></td>
            <td class="controlls">{if $task_set->comments_enabled}<a href="{internal_url url="admin_task_sets/comments/{$task_set->id}"}" class="button special" title="{translate line='admin_task_sets_table_button_discussion'}"><span class="list-icon list-icon-comment"></span> [{$task_set->comment_count}]</a>{/if}</td>
            <td class="controlls"><a href="{internal_url url="admin_task_sets/preview/{$task_set->id}"}" class="button special preview_task_set" title="{translate line='admin_task_sets_table_button_preview'}"><span class="list-icon list-icon-page-preview"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_task_sets/open/task_set_id/{$task_set->id}"}" class="button special open_task_set_button" title="{translate line='admin_task_sets_table_button_open'}"><span class="list-icon list-icon-task-set-open"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_task_sets/edit/task_set_id/{$task_set->id}"}" class="button" title="{translate line='admin_task_sets_table_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_task_sets/delete/task_set_id/{$task_set->id}"}" class="button delete" title="{translate line='admin_task_sets_table_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
        </tr>
        {/foreach}
    </tbody>
</table>