<table class="restrictions_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>{translate line='admin_restrictions_table_header_start_time'}</th>
            <th>{translate line='admin_restrictions_table_header_end_time'}</th>
            <th>{translate line='admin_restrictions_table_header_ip_addresses'}</th>
            <th colspan="2" class="controlls">{translate line='admin_restrictions_table_header_controlls'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $restrictions as $restriction}
        <tr>
            <td>{$restriction->id}</td>
            <td>{$restriction->start_time|date_format:{translate line='common_datetime_format'}}</td>
            <td>{$restriction->end_time|date_format:{translate line='common_datetime_format'}}</td>
            <td>{$restriction->ip_addresses|truncate:80}</td>
            <td class="controlls"><a href="{internal_url url="admin_restrictions/edit/{$restriction->id|intval}"}" class="button" title="{translate line='admin_restrictions_table_content_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_restrictions/delete/{$restriction->id|intval}"}" class="button delete" title="{translate line='admin_restrictions_table_content_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
        </tr>
        {/foreach}
    </tbody>
</table>