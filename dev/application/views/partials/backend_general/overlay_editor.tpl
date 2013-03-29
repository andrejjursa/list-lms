{foreach $languages as $idiom => $language}
    <p class="caption"><label for="overlay_{$idiom}_{$table}_{$table_id}_{$column}_id">{$language|escape:'html'}:</label></p>
    {capture name='lang_overlays_editor_text' assign='default_lang_overlay_text'}{overlay table=$table table_id=$table_id column=$column idiom=$idiom}{/capture}
    {if $editor_type eq 'textarea'}
    <p class="input"><textarea name="overlay[{$idiom}][{$table}][{$table_id}][{$column}]" id="overlay_{$idiom}_{$table}_{$table_id}_{$column}_id"{if $class} class="{$class}"{/if}>{$smarty.post.overlay[$idiom][$table][$table_id][$column]|default:$default_lang_overlay_text|escape:'html'}</textarea></p>
    {else}
    <p class="input"><input type="text" name="overlay[{$idiom}][{$table}][{$table_id}][{$column}]" value="{$smarty.post.overlay[$idiom][$table][$table_id][$column]|default:$default_lang_overlay_text|escape:'html'}" id="overlay_{$idiom}_{$table}_{$table_id}_{$column}_id"{if $class} class="{$class}"{/if} /></p>
    {/if}
{/foreach}