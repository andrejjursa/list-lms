{extends file='layouts/backend_popup.tpl'}
{block main_content}
    {if isset($log) and $log->exists()}
        <h3>{translate line="admin_logs_log_type_{$log->log_type}"}</h3>
        {include file='partials/backend_general/flash_messages.tpl' inline}
        <fieldset>
            <p>{$log->message|escape:'html'}</p>
            
            <table class="logs_table top_margin symmetric">
                <thead>
                    <tr>
                        <th>{translate line='admin_logs_table_header_student'}</th>
                        <th>{translate line='admin_logs_table_header_teacher'}</th>
                        <th>{translate line='admin_logs_table_header_created'}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$log->student_fullname}</td>
                        <td>{$log->teacher_fullname}</td>
                        <td>{$log->created|date_format:{translate line='common_datetime_format'}}</td>
                    </tr>
                </tbody>
            </table>
            {$languages = $this->lang->get_list_of_languages()}        
            <table class="logs_table top_margin symmetric">
                <thead>
                    <tr>
                        <th>{translate line='admin_logs_table_header_type'}</th>
                        <th>{translate line='admin_logs_table_header_ip_address'}</th>
                        <th>{translate line='admin_logs_table_header_language'}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{translate|escape:'html' line="admin_logs_log_type_{$log->log_type}"}</td>
                        <td>{$log->ip_address}</td>
                        <td>{$languages[$log->language]|escape:'html'}</td>
                    </tr>
                </tbody>
            </table>
            
            {include file="backend/logs/types/type_{$log->log_type}.tpl"}
        </fieldset>
    {else}
        {include file='partials/backend_general/flash_messages.tpl' inline}
        {include file='partials/backend_generalt/error_box.tpl' message='admin_logs_log_not_found' inline}
    {/if}
{/block}