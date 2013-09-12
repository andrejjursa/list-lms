{function name='category_tree' level=1 structure=[]}
{if $structure}<ul class="category_tree_structure tree_level_{$level}">
    {foreach $structure as $node}
        <li><div class="tree_line">{translate_text|escape:'html' text=$node.category->name} (<span title="{translate|sprintf:$node.category->task_count line='admin_categories_category_used_info'}"><strong>{$node.category->task_count}</strong></span>) <span class="controlls"><a href="{internal_url url="admin_categories/edit/category_id/{$node.category->id}"}" class="button">{translate line='admin_categories_tree_controlls_edit'}</a> <a href="{internal_url url="admin_categories/delete/category_id/{$node.category->id}"}" class="button delete">{translate line='admin_categories_tree_controlls_delete'}</a></span></div>
    {category_tree level=$level+1 structure=$node.subcategories}</li>    
    {/foreach}
</ul>{/if}
{/function}
{category_tree structure=$structure}