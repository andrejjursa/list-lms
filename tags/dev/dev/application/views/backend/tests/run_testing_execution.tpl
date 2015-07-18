{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tests_testing_execution_page_header'}{/block}
{block main_content}
    <h2>{translate line='admin_tests_testing_execution_page_header'}</h2>
    {if $test->exists()}<h3>{overlay table='tasks' table_id=$test->task_id column='name' default=$test->task_name} / {overlay table='tests' table_id=$test->id column='name' default=$test->name}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $test->exists()}
        <fieldset>
            <div id="run_test_output_id">{translate line='admin_tests_testing_execution_test_is_being_executed_message'}</div>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_tests_error_cant_find_test' inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var test_id = {$test->id|intval};
    var file_name = '{$file_name|encode_for_url}';
    var file_full_path = '{"private/test_to_execute/testing_execution/{$file_name}"|encode_for_url}';
    var can_execute = {if $test->exists()}true{else}false{/if};
</script>{/block}