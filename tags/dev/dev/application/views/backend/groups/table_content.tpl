<table class="groups_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th class="sort:created">{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th class="sort:updated">{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th class="sort:name">{translate line='admin_groups_table_header_group_name'}</th>{/if}
            {if $filter.fields.course}<th class="sort:course">{translate line='admin_groups_table_header_group_course'}</th>{/if}
            {if $filter.fields.rooms}<th>{translate line='admin_groups_table_header_group_rooms'}</th>{/if}
            {if $filter.fields.capacity}<th class="sort:capacity:desc">{translate line='admin_groups_table_header_group_capacity'}</th>{/if}
            <th colspan="4" class="controlls"><div id="open_fields_config_id">{translate line='admin_groups_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tfoot id="table_pagination_footer_id">
        <tr>
            <td colspan="{5 + $filter.fields|sum_array}">{include file='partials/backend_general/pagination.tpl' paged=$groups->paged inline}</td>
        </tr>
    </tfoot>
    <tbody>
        {foreach $groups as $group}
        <tr>
            <td>{$group->id|intval}</td>
            {if $filter.fields.created}<td>{$group->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.updated}<td>{$group->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.name}<td>{translate_text|escape:'html' text=$group->name}</td>{/if}
            {if $filter.fields.course}<td><span title="{translate_text|escape:'html' text=$group->course_name}">{translate_text|abbreviation|escape:'html' text=$group->course_name}</span> / <span title="{translate_text|escape:'html' text=$group->course_period_name}">{translate_text|abbreviation|escape:'html' text=$group->course_period_name}</span></td>{/if}
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
            <td class="controlls"><a href="{internal_url url="admin_groups/group_mail/{$group->id}"}" class="button special group_mail" title="{translate line='admin_groups_table_controlls_group_mail'}"><span class="list-icon list-icon-mail"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_rooms/index/{$group->id}"}" class="button special rooms_editor" title="{translate line='admin_groups_table_controlls_rooms'}"><span class="list-icon list-icon-home"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_groups/edit/group_id/{$group->id}"}" class="button" title="{translate line='admin_groups_table_controlls_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_groups/delete/group_id/{$group->id}"}" class="button delete" title="{translate line='admin_groups_table_controlls_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
        </tr>
        {/foreach}
    </tbody>
</table>