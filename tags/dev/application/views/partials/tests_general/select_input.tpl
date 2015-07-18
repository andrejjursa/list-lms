<div class="field">
    <label for="" class="{$label_class}">{translate line=$label_lang}:</label>
    <div class="input">
        <select name="configuration[{$field_name}]" size="{$select_size|default:1}">
            {list_html_options options=$select_options selected=$smarty.post.configuration[$field_name]|default:$configuration[$field_name]|default:$default_option}
        </select>
    </div>
    {if $hint_lang}
    <p class="input"><em>{translate line=$hint_lang}</em></p>
    {/if}
    {form_error field="configuration[{$field_name}]" left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>