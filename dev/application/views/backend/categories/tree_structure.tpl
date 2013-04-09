{function name='category_tree' level=1 structure=[]}
{if $structure}<ul class="category_tree_structure tree_level_{$level}">
    {foreach $structure as $node}
    <li>{translate_text|escape:'html' text=$node.category->name}{category_tree level=$level+1 structure=$node.subcategories}</li>    
    {/foreach}
</ul>{/if}
{/function}
{category_tree structure=$structure}