<table class="task_set_permissions_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>{translate line='admin_task_set_permissions_table_header_group'}</th>
            <th>{translate line='admin_task_set_permissions_table_header_room'}</th>
            <th>{translate line='admin_task_set_permissions_table_header_publish_start_time'}</th>
            <th>{translate line='admin_task_set_permissions_table_header_upload_end_time'}</th>
            <th>{translate line='admin_task_set_permissions_table_header_enabled'}</th>
            <th class="controlls" colspan="2">{translate line='admin_task_set_permissions_table_header_controlls'}</th>
        </tr>
    </thead>
    <tbody>{$enabled_status = [0 => {translate line='admin_task_set_permissions_table_value_no'}, 1 => {translate line='admin_task_set_permissions_table_value_yes'}]}
        {foreach $task_set_permissions as $task_set_permission}
        <tr>
            <td>{$task_set_permission->id|intval}</td>
            <td>{translate_text|default:{translate line='admin_task_set_permissions_table_value_not_selected'} text=$task_set_permission->group_name}</td>
            <td>{translate_text|default:{translate line='admin_task_set_permissions_table_value_not_selected'} text=$task_set_permission->room_name}</td>
            <td>{$task_set_permission->publish_start_time|date_format:{translate line='common_datetime_format'}}</td>
            <td>{$task_set_permission->upload_end_time|date_format:{translate line='common_datetime_format'}}</td>
            <td>{$enabled_status[$task_set_permission->enabled|intval]}</td>
            <td class="controlls"><a href="{internal_url url="admin_task_set_permissions/edit_permission/{$task_set->id|intval}/{$task_set_permission->id|intval}"}" class="button edit_task_set_permission">{translate line='admin_task_set_permissions_table_button_edit'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_task_set_permissions/delete_permission/{$task_set->id|intval}/{$task_set_permission->id|intval}"}" class="button delete delete_task_set_permission">{translate line='admin_task_set_permissions_table_button_delete'}</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>