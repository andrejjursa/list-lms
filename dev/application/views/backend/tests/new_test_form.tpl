{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_tests_new_test_form_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tests_new_test_form_title'}</h2>
    {if $task->exists()}<h3>{overlay table='tasks' table_id=$task->id column='name' default=$task->name}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $task->exists()}
    <fieldset>
        <form action="{internal_url url="admin_tests/prepare_new_test/{$task->id}"}" method="post" id="new_test_form_id">
            <div class="field">
                <label for="test_name_id" class="required">{translate line='admin_tests_test_form_label_name'}:</label>
                <p class="input"><input type="text" name="test[name]" value="{$smarty.post.test.name|escape:'html'}" id="test_name_id" /></p>
                {form_error field='test[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="test_type_id" class="required">{translate line='admin_tests_test_form_label_type'}:</label>
                <div class="input">
                    <select name="test[type]" size="1" id="test_type_id">
                        <option value=""></option>
                        {list_html_options options=$test_types selected=$smarty.post.test.type}
                    </select>
                </div>
                {form_error field='test[type]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field test_subtype_field">
                <label for="test_subtype_id" class="required">{translate line='admin_tests_test_form_label_subtype'}:</label>
                <div class="input">
                    <select name="test[subtype]" size="1" id="test_subtype_id">
                        <option value=""></option>
                    </select>
                </div>
                {form_error field='test[subtype]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field test_subtype_field_else">
                <label class="required">{translate line='admin_tests_test_form_label_subtype'}:</label>
                <p class="input"><em>{translate line='admin_tests_test_form_label_subtype_hint'}</em></p>
                {form_error field='test[subtype]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_tests_test_form_button_submit'}" class="button" />
                <input type="hidden" name="test_subtype_selected" value="{$smarty.post.test.subtype|escape:'html'}" id="test_subtype_selected_id" />
            </div>
        </form>
    </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_tasks_error_cant_find_task' inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var subtypes = {$test_subtypes|default:[]|json_encode};
</script>{/block}