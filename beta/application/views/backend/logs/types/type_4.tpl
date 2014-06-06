<table class="logs_table top_margin symmetric">
    <thead>
        <tr>
            <th>{translate line='admin_logs_table_header_task_set'}</th>
            <th>{translate line='admin_logs_table_header_course'}</th>
            <th>{translate line='admin_logs_table_header_filename'}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</td>
            <td>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}</td>
            <td><a href="{internal_url url="tasks/download_solution/{$task_set->id}/{$filename|encode_for_url}"}" target="_blank">{$filename}</a></td>
        </tr>
    </tbody>
</table>