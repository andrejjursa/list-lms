{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tests_configure_test_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tests_configure_test_page_title'}</h2>
    {if !$error_message}<h3>{overlay table='tasks' table_id=$test->task_id column='name' default=$test->task_name} / {overlay table='tests' table_id=$test->id column='name' default=$test->name}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if !$error_message}
    <fieldset>
        <form action="{internal_url url="admin_tests/save_test_configuration/{$test->id}"}" method="post">
            {include file=$test_config_view}
        </form>
    </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message=$error_message inline}
    {/if}
{/block}