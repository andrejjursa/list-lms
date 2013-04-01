{foreach $rooms as $room}
<tr>
    <td>{translate_text|escape:'html' text=$room->name}</td>
    <td>{$list_days[$room->time_day|intval]|escape:'html'}</td>
    <td>{$room->time_begin|is_time|escape:'html'}</td>
    <td>{$room->time_end|is_time|escape:'html'}</td>
    <td class="controlls"><a href="{internal_url url="admin_rooms/edit/room_id/{$room->id}"}" class="button">{translate line='admin_rooms_table_controlls_button_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_rooms/delete/room_id/{$room->id}"}" class="button delete">{translate line='admin_rooms_table_controlls_button_delete'}</a></td>
</tr>
{/foreach}