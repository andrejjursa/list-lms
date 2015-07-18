{include file='partials/backend_general/flash_messages.tpl' inline}
{include file='backend/categories/categories_parent_selector.tpl' inline}
<div class="field">
    <label for="category_name_id" class="required">{translate line='admin_categories_form_label_category_name'}:</label>
    <p class="input"><input type="text" name="category[name]" value="{$smarty.post.category.name|escape:'html'}" id="category_name_id" /></p>
    {form_error field='category[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="category_parent_id_id" class="required">{translate line='admin_categories_form_label_parent_category'}:</label>
    <p class="input"><select name="category[parent_id]" size="1" id="category_parent_id_id">
        <option value=""></option><option value="root"{if $smarty.post.category.parent_id eq 'root'} selected="selected"{/if}>[{translate line='admin_categories_parent_category_root'}]</option>{categories_tree_options structure=$structure selected=$smarty.post.category.parent_id}
    </select></p>
    {form_error field='category[parent_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" value="{translate line='admin_categories_form_button_save'}" name="save_button" class="button" />
</div>