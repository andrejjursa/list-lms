<div class="field">
    <label for="configuration_files_{$field_name}_id" class="{$label_class}">{translate line=$label_lang}:</label>
    <p class="input"><input type="file" name="configuration_test_files_{$field_name}" id="configuration_files_{$field_name}_id" /></p>
    {if $hint_lang}
    <p class="input"><em>{translate line=$hint_lang}</em></p>
    {/if}
    <div class="input">
        {if file_exists("private/uploads/unit_tests/test_{$test->id}/{$configuration[$field_name]}")}
            <a href="{internal_url url="admin_tests/download_test_file/{$test->id}/{$configuration[$field_name]|encode_for_url}"}" target="_blank">{$configuration[$field_name]|basename}</a>
        {/if}
    </div>
    {$error_message_variable = "configuration_test_files_{$field_name}_error"}
    {if ${$error_message_variable}}
    <div class="input">
        {include file='partials/backend_general/error_box.tpl' message=${$error_message_variable} inline}
    </div>
    {/if}
</div>