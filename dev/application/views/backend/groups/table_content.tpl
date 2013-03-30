{foreach $groups as $group}
<tr>
    <td>{translate_text|escape:'html' text=$group->name}</td>
    <td>{translate_text|escape:'html' text=$group->course->get()->name}</td>
    <td>
        {if $group->room->order_by('name', 'asc')->get()->exists()}
        <ul class="room">
        {foreach $group->room as $room}
            <li>
                <strong>{$room->name|escape:'html'}:</strong>
                <span>{$list_days[$room->time_day]|escape:'html'} ({$room->time_begin|is_time|escape:'html'} - {$room->time_end|is_time|escape:'html'})</span>
            </li>
        {/foreach}
        </ul>
        {else}
        {translate line='admin_groups_table_content_no_rooms_message'} 
        {/if}
    </td>
</tr>
{/foreach}
<pre>{$groups|print_r:TRUE}</pre>