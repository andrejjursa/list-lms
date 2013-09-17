<div class="field">
    <label for="" class="{$label_class}">{translate line=$label_lang}:</label>
    <p class="input"><input type="text" name="configuration[{$field_name}]" value="{$smarty.post.configuration[$field_name]|default:$configuration[$field_name]|escape:'html'}" /></p>
    {if $hint_lang}
    <p class="input"><em>{translate line=$hint_lang}</em></p>
    {/if}
    {form_error field="configuration[{$field_name}]" left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>