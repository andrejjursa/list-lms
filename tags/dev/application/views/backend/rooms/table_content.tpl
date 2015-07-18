{foreach $rooms as $room}
<tr>
    <td>{$room->id|intval}</td>
    <td>{translate_text|escape:'html' text=$room->name}</td>
    <td>{$list_days[$room->time_day|intval]|escape:'html'}</td>
    <td>{$room->time_begin|is_time|escape:'html'}</td>
    <td>{$room->time_end|is_time|escape:'html'}</td>
    <td>{$room->capacity|intval}</td>
    <td>
        <ul>
        {foreach $room->teachers->get_iterated() as $teacher}
            <li>{$teacher->fullname}</li>
        {/foreach}
        {if $room->teachers_plain}
            {$teachers_plain_names = ','|explode:$room->teachers_plain}
            {foreach $teachers_plain_names as $teacher_plain_name}
            <li>{$teacher_plain_name|trim}</li>
            {/foreach}
        {/if}
        </ul>
    </td>
    <td class="controlls"><a href="{internal_url url="admin_rooms/edit/{$group_id}/room_id/{$room->id}"}" class="button" title="{translate line='admin_rooms_table_controlls_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
    <td class="controlls"><a href="{internal_url url="admin_rooms/delete/{$group_id}/room_id/{$room->id}"}" class="button delete" title="{translate line='admin_rooms_table_controlls_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
</tr>
{/foreach}