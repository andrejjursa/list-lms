<table class="groups_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th>{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th>{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th>{translate line='admin_groups_table_header_group_name'}</th>{/if}
            {if $filter.fields.course}<th>{translate line='admin_groups_table_header_group_course'}</th>{/if}
            {if $filter.fields.rooms}<th>{translate line='admin_groups_table_header_group_rooms'}</th>{/if}
            {if $filter.fields.capacity}<th>{translate line='admin_groups_table_header_group_capacity'}</th>{/if}
            <th colspan="4" class="controlls"><div id="open_fields_config_id">{translate line='admin_groups_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $groups as $group}
        <tr>
            <td>{$group->id|intval}</td>
            {if $filter.fields.created}<td>{$group->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.updated}<td>{$group->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.name}<td>{translate_text|escape:'html' text=$group->name}</td>{/if}
            {if $filter.fields.course}<td>{translate_text|escape:'html' text=$group->course_name} / {translate_text|escape:'html' text=$group->course_period_name}</td>{/if}
            {if $filter.fields.rooms}<td>
                {if $group->room->order_by('time_day', 'asc')->order_by('time_begin', 'asc')->get()->exists()}
                <ul class="room">
                {foreach $group->room as $room}
                    <li>
                        <strong>{translate_text|escape:'html' text=$room->name}:</strong>
                        <span>{$list_days[$room->time_day]|escape:'html'} ({$room->time_begin|is_time|escape:'html'} - {$room->time_end|is_time|escape:'html'})</span>
                    </li>
                {/foreach}
                </ul>
                {else}
                {translate line='admin_groups_table_content_no_rooms_message'} 
                {/if}
            </td>{/if}
            {if $filter.fields.capacity}<td>{$group->group_capacity|intval}</td>{/if}
            <td class="controlls"><a href="{internal_url url="admin_groups/group_mail/{$group->id}"}" class="button special group_mail">{translate line='admin_groups_table_controlls_group_mail'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_rooms/index/{$group->id}"}" class="button special rooms_editor">{translate line='admin_groups_table_controlls_rooms'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_groups/edit/group_id/{$group->id}"}" class="button">{translate line='admin_groups_table_controlls_edit'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_groups/delete/group_id/{$group->id}"}" class="button delete">{translate line='admin_groups_table_controlls_delete'}</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>