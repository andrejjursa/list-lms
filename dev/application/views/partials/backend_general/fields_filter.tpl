<table id="fields_filter_table_id" style="display: none;">
    <thead>
        <tr>
            <td class="caption">{translate line='common_fields_filter_caption'}</td>
            <td><a href="javascript:void(0);" class="button close_button special">x</a></td>
        </tr>
    </thead>
    <tbody>
        {foreach $fields_config as $field}
            <tr><td colspan="2"><input type="checkbox" name="fields_config[{$field.name}]" value="1" id="fields_config_{$field.name}_checkbox_id"{if $fields[$field.name] eq 1} checked="checked"{/if} /> <label for="fields_config_{$field.name}_checkbox_id">{translate_text|escape:html text=$field.caption}</label></td></tr>
        {/foreach}
    </tbody>
</table>