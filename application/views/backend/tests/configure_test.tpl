{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tests_configure_test_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tests_configure_test_page_title'}</h2>
    {if !$error_message}<h3>{overlay table='tasks' table_id=$test->task_id column='name' default=$test->task_name} / {overlay table='tests' table_id=$test->id column='name' default=$test->name}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if !$error_message}
    <fieldset>
        <form action="{internal_url url="admin_tests/save_test_configuration/{$test->id}"}" method="post" enctype="multipart/form-data">
            <div class="field">
                <label for="test_name_id" class="required">{translate line='admin_tests_test_form_label_name'}:</label>
                <p class="input"><input type="text" name="test[name]" value="{$smarty.post.test.name|default:$test->name|escape:'html'}" id="test_name_id" /></p>
                {form_error field='test[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                {include file='partials/backend_general/overlay_editor.tpl' table='tests' table_id=$test->id column='name' editor_type='input' inline}
            </div>
            <div class="field">
                <label for="test_enabled_id">{translate line='admin_tests_test_form_label_enabled'}:</label>
                <p class="input"><input type="checkbox" name="test[enabled]" value="1"{if $smarty.post.test.enabled|default:$test->enabled} checked="checked"{/if} id="test_enabled_id" /></p>
            </div>
            <div class="field">
                <label for="test_instructions_id">{translate line='admin_tests_test_form_label_instructions'}:</label>
                <p class="input"><textarea name="test[instructions]" class="tinymce" id="test_instructions_id">{$smarty.post.test.instructions|default:$test->instructions|add_base_url|htmlspecialchars}</textarea></p>
                {include file='partials/backend_general/overlay_editor.tpl' table='tests' table_id=$test->id column='instructions' editor_type='textarea' class='tinymce' inline}
            </div>
            <div class="field">
                <label for="test_timeout_id" class="required">{translate line='admin_tests_test_form_label_timeout'}:</label>
                <p class="input"><input type="text" name="test[timeout]" value="{$smarty.post.test.timeout|default:$test->timeout|default:15000|escape:'html'}" id="test_timeout_id" /></p>
                <p class="input"><em>{translate line='admin_tests_test_form_label_timeout_hint'}</em></p>
                {form_error field='test[timeout]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="test_enable_scoring_id">{translate line='admin_tests_test_form_label_enable_scoring'}:</label>
                <p class="input"><input type="checkbox" name="test[enable_scoring]" value="1"{if $smarty.post.test.enable_scoring|default:$test->enable_scoring} checked="checked"{/if} id="test_enable_scoring_id" /></p>
                <p class="input"><em>{translate line='admin_tests_test_form_label_enable_scoring_hint'}</em></p>
            </div>
            <hr />
            {include file=$test_config_view}
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_tests_test_form_button_submit'}" class="button" />
            </div>
        </form>
    </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message=$error_message inline}
    {/if}
{/block}
