<table class="all_tests_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>{translate line='admin_tests_test_table_header_name'}</th>
            <th>{translate line='admin_tests_test_table_header_type'}</th>
            <th>{translate line='admin_tests_test_table_header_subtype'}</th>
            <th>{translate line='admin_tests_test_table_header_enabled'}</th>
            <th colspan="3" class="controlls">{translate line='admin_tests_test_table_header_controlls'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $tests as $test}
            <tr>
                <td>{$test->id}</td>
                <td>{overlay table='tests' table_id=$test->id column='name' default=$test->name}</td>
                <td>{$test_types[$test->type]}</td>
                <td>{$test_subtypes[$test->type][$test->subtype]}</td>
                <td>{if $test->enabled}{translate line='admin_tests_test_table_col_enabled_yes'}{else}{translate line='admin_tests_test_table_col_enabled_no'}{/if}</td>
                <td class="controlls"><a href="{internal_url url="admin_tests/prepare_execution/{$test->id}"}" class="button special execute_test">{translate line='admin_tests_test_table_button_execute'}</a></td>
                <td class="controlls"><a href="{internal_url url="admin_tests/configure_test/{$test->id}"}" class="button configure_test">{translate line='admin_tests_test_table_button_configure'}</a></td>
                <td class="controlls"><a href="{internal_url url="admin_tests/delete_test/{$test->id}"}" class="button delete delete_test">{translate line='admin_tests_test_table_button_delete'}</a></td>
            </tr>
        {foreachelse}
            <tr>
                <td colspan="8">
                    {include file='partials/backend_general/error_box.tpl' message='lang:admin_tests_error_there_are_no_tests' inline}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>