{$table_waiting = ''}{$table_executing = ''}{$table_done = ''}{$table_error = ''}
{foreach $test_queue as $test_item}
{capture name='tests_line' assign='tests_line'}
<tr class="test_status_{$test_item->status|intval}{if date('Y-m-d H:i:s', time() - 300) lte $test_item->finish|date_format:'Y-m-d H:i:s'} recently_finished{/if}">
  {$finish_time = {translate line='admin_tests_student_test_queue_not_finished'}}
  {if $test_item->finish >= $test_item->start}
    {$finish_time = $test_item->finish|date_format:{translate line='common_datetime_format'}}
  {/if}
    <td>{$test_types[$test_item->test_type]}:{$test_item->tests_count}</td>
    <td>{$test_item->version|intval}</td>
    <td>{$test_item->start|date_format:{translate line='common_datetime_format'}}</td>
    <td>{$finish_time}</td>
    <td>{$test_item->worker|default:{translate line='admin_tests_student_test_queue_worker_not_assigned'}}</td>
    <td>{$test_item->priority|intval}</td>
    <td>{translate|default:{translate line='admin_tests_student_test_queue_unknown_status'} line="admin_tests_student_test_queue_status_{$test_item->status}"}</td>
    <td>
        <a href="{internal_url url="tasks/test_result/{$test_item->id|intval}"}" class="button special open_test_queue_results">{translate line='admin_tests_student_test_queue_table_body_button_detail'}</a>
    </td>
</tr>
{/capture}
{if $test_item->status eq 0}
    {$table_waiting = "{$table_waiting}{$tests_line}"}
{/if}
{if $test_item->status eq 1}
    {$table_executing = "{$table_executing}{$tests_line}"}
{/if}
{if $test_item->status eq 2}
    {$table_done = "{$table_done}{$tests_line}"}
{/if}
{if $test_item->status eq 3}
    {$table_error = "{$table_error}{$tests_line}"}
{/if}
{/foreach}

{if $table_done}
<h4>{translate line='admin_tests_student_test_queue_status_header_done'}</h4>
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
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$table_done}
    </tbody>
</table>
{/if}

{if $table_executing}
<h4>{translate line='admin_tests_student_test_queue_status_header_executing'}</h4>
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
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$table_executing}
    </tbody>
</table>
{/if}

{if $table_waiting}
<h4>{translate line='admin_tests_student_test_queue_status_header_waiting'}</h4>
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
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$table_waiting}
    </tbody>
</table>
{/if}

{if $table_error}
<h4>{translate line='admin_tests_student_test_queue_status_header_error'}</h4>
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
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$table_error}
    </tbody>
</table>
{/if}

{if $test_queue->result_count() eq 0}
    <p>{translate line='admin_tests_student_test_queue_message_no_tests_yet'}</p>
{/if}
