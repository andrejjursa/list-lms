<table class="logs_table">
    <thead>
        <tr>
            <th>{translate line='admin_logs_table_header_type'}</th>
            <th>{translate line='admin_logs_table_header_ip_address'}</th>
            <th>{translate line='admin_logs_table_header_language'}</th>
            <th>{translate line='admin_logs_table_header_message'}</th>
            <th>{translate line='admin_logs_table_header_student'}</th>
            <th>{translate line='admin_logs_table_header_teacher'}</th>
            <th>{translate line='admin_logs_table_header_created'}</th>
            <th class="controlls">{translate line='admin_logs_table_header_controlls'}</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="8">{include file='partials/backend_general/pagination.tpl' paged=$logs->paged inline}</td>
        </tr>
    </tfoot>
    <tbody>
        {$languages = $this->lang->get_list_of_languages()}
        {foreach $logs as $log}
        <tr>
            <td><span title="{translate|escape:'html' line="admin_logs_log_type_{$log->log_type}"}">{translate|abbreviation line="admin_logs_log_type_{$log->log_type}"}</span></td>
            <td>{$log->ip_address}</td>
            <td><span title="{$languages[$log->language]|escape:'html'}">{$languages[$log->language]|abbreviation}</span></td>
            <td><span title="{$log->message|escape:'html'}">{$log->message|truncate:50|escape:'html'}</span></td>
            <td>{$log->student_fullname}</td>
            <td>{$log->teacher_fullname}</td>
            <td>{$log->created|date_format:{translate line='common_datetime_format'}}</td>
            <td class="controlls"><a href="{internal_url url="admin_logs/details/{$log->id|intval}"}" class="button special details" title="{translate|escape:'html' line='admin_logs_table_button_details'}"><span class="list-icon list-icon-page-preview"></span></a></td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="8">{include file='partials/backend_general/error_box.tpl' message='lang:admin_logs_table_content_no_logs' inline}</td>
        </tr>
        {/foreach}
    </tbody>
</table>