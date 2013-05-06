{foreach $groups as $group}
<tr>
    <td>{translate_text|escape:'html' text=$group->name}</td>
    <td>{translate_text|escape:'html' text=$group->course_name} / {translate_text|escape:'html' text=$group->course_period_name}</td>
    <td>
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
    </td>
    <td>{$group->group_capacity|intval}</td>
    <td class="controlls"><a href="{internal_url url="admin_rooms/index/{$group->id}"}" class="button special rooms_editor">{translate line='admin_groups_table_controlls_rooms'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_groups/edit/group_id/{$group->id}"}" class="button">{translate line='admin_groups_table_controlls_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_groups/delete/group_id/{$group->id}"}" class="button delete">{translate line='admin_groups_table_controlls_delete'}</a></td>
</tr>
{/foreach}