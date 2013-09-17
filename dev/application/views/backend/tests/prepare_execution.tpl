{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tests_testing_execution_page_header'}{/block}
{block main_content}
    <h2>{translate line='admin_tests_testing_execution_page_header'}</h2>
    {if $test->exists()}<h3>{overlay table='tasks' table_id=$test->task_id column='name' default=$test->task_name} / {overlay table='tests' table_id=$test->id column='name' default=$test->name}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $test->exists()}
        {$instructions = {overlay|add_base_url table='tests' table_id=$test->id column='instructions' default=$test->instructions}}
        {if $instructions|trim}
        <fieldset>
            <div>{$instructions}</div>
        </fieldset>
        {/if}
        <fieldset>
            <form action="{internal_url url="admin_tests/run_testing_execution/{$test->id}"}" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label for="source_codes_id" class="required">{translate line='admin_tests_prepare_execution_form_label_file'}:</label>
                    <p class="input"><input type="file" name="source_codes" id="source_codes_id" /></p>
                    {if $source_codes_error}
                    <div class="input">
                        {include file='partials/backend_general/error_box.tpl' message=$source_codes_error inline}
                    </div>
                    {/if}
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_tests_prepare_execution_form_button_submit'}" class="button" />
                </div>
            </form>
        </fieldset>
        <fieldset>
            <div>{overlay|add_base_url table='tasks' table_id=$test->task_id column='text' default=$test->task_text}</div>
        </fieldset>            
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_tests_error_cant_find_test' inline}
    {/if}
{/block}