<fieldset class="basefieldset">
    <table class="tests_queue_table">
        <thead>
            <tr>
                <th>{translate line='admin_tests_student_test_queue_table_header_test_type'}</th>
                <th>{translate line='admin_tests_student_test_queue_table_header_version'}</th>
                <th>{translate line='admin_tests_student_test_queue_table_header_start'}</th>
                <th>{translate line='admin_tests_student_test_queue_table_header_finish'}</th>
                <th>{translate line='admin_tests_student_test_queue_table_header_worker'}</th>
                <th>{translate line='admin_tests_student_test_queue_table_header_priority'}</th>
                <th>{translate line='admin_tests_student_test_queue_table_header_status'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $test_queue as $test_item}
            <tr class="test_status_{$test_item->status|intval}">
                <td>{$test_types[$test_item->test_type]}:{$test_item->tests_count}</td>
                <td>{$test_item->version|intval}</td>
                <td>{$test_item->start|date_format:{translate line='common_datetime_format'}}</td>
                <td>{$test_item->finish|date_format:{translate line='common_datetime_format'}|default:{translate line='admin_tests_student_test_queue_not_finished'}}</td>
                <td>{$test_item->worker|default:{translate line='admin_tests_student_test_queue_worker_not_assigned'}}</td>
                <td>{$test_item->priority|intval}</td>
                <td>{translate|default:{translate line='admin_tests_student_test_queue_unknown_status'} line="admin_tests_student_test_queue_status_{$test_item->status}"}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</fieldset>