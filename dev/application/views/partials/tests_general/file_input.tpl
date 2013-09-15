<div class="field">
    <label for="configuration_files_{$field_name}_id" class="{$label_class}">{translate line=$label_lang}:</label>
    <p class="input"><input type="file" name="configuration[test_files][{$field_name}]" id="configuration_files_{$field_name}_id" /></p>
    <div class="input">
        {if file_exists("private/uploads/unit_tests/test_{$test->id}/{$configuration.test_files[$field_name]}")}
            <a href="{internal_url url="admin_tests/download_test_file/{$test->id}/{$configuration.test_files[$field_name]|encode_for_url}"}" target="_blank">{$configuration.test_files[$field_name]|basename}</a>
        {/if}
    </div>
    {if $file_error_message}
    <div class="input">
        {include file='partials/backend_general/error_box.tpl' message=$file_error_message inline}
    </div>
    {/if}
</div>