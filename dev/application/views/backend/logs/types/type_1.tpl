<table class="logs_table top_margin symmetric">
    <thead>
        <tr>
            <th>{translate line='admin_logs_table_header_for_student'}</th>
            <th>{translate line='admin_logs_table_header_task_set'}</th>
            <th>{translate line='admin_logs_table_header_course'}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{$solution->student_fullname}</td>
            <td>{overlay table='task_sets' table_id=$solution->task_set_id column='name' default=$solution->task_set_name}</td>
            <td>{translate_text text=$solution->task_set_course_name} / {translate_text text=$solution->task_set_course_period_name}</td>
        </tr>
    </tbody>
</table>