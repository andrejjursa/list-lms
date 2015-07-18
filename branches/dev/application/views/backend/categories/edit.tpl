{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_categories_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_categories_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {include file='backend/categories/categories_parent_selector.tpl' inline}
    {if $category->exists() or $smarty.post.category}
        <fieldset>
            <form action="{internal_url url='admin_categories/update'}" method="post" id="new_category_form_id">
                <div class="field">
                    <label for="category_name_id" class="required">{translate line='admin_categories_form_label_category_name'}:</label>
                    <p class="input"><input type="text" name="category[name]" value="{$smarty.post.category.name|default:$category->name|escape:'html'}" id="category_name_id" /></p>
                    {form_error field='category[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="category_parent_id_id" class="required">{translate line='admin_categories_form_label_parent_category'}:</label>
                    <p class="input"><select name="category[parent_id]" size="1" id="category_parent_id_id">
                        <option value=""></option><option value="root"{if $smarty.post.category.parent_id eq 'root' or ($category->exists() and $category->parent_id|is_null)} selected="selected"{/if}>[{translate line='admin_categories_parent_category_root'}]</option>{categories_tree_options structure=$structure selected=$smarty.post.category.parent_id|default:$category->parent_id}
                    </select></p>
                    {form_error field='category[parent_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" value="{translate line='admin_categories_form_button_save'}" name="save_button" class="button" /> <a href="{internal_url url='admin_categories'}" class="button special">{translate line='common_button_back'}</a>
                    <input type="hidden" value="{$smarty.post.category_id|default:$category->id}" name="category_id" />
                </div>
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_categories_error_category_not_found' inline}
    {/if}
{/block}