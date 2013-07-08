{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tasks_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {include file='backend/categories/categories_parent_selector.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_tasks_fieldset_legend_all_tasks'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_tasks/get_all_tasks'}" method="post" id="filter_form_id">
                <div class="field">
                    <label>{translate line='admin_tasks_filter_label_filter_by_categories'}:</label>
                    <div class="input" id="dynamic_categories_id">
                        <div class="clauses">
                            {foreach $filter.categories.clauses as $clause}
                            <div class="clause{if $clause@first} first_clause{/if}" id="clause_{$clause@key}_id">
                                ( <span class="categories">
                                {foreach $clause as $category}
                                    <span class="category{if $category@first} first_category{/if}" id="clause_{$clause@key}_category_{$category@key}_id">
                                        <select name="filter[categories][clauses][{$clause@key}][{$category@key}]" size="1">
                                            {categories_tree_options structure=$structure selected=$category}
                                        </select>
                                        <a href="javascript:void(0);" class="button special remove_category" rel="{$clause@key}|{$category@key}">-</a>
                                    </span>
                                {/foreach}
                                </span>
                                ) [ <a href="javascript:void(0);" class="button special new_category" rel="{$clause@key}">+</a> | <a href="javascript:void(0);" class="button special remove_clause" rel="{$clause@key}">-</a> ]
                            </div>
                            {/foreach}
                        </div>
                        <a href="javascript:void(0);" class="button special new_clause">{translate line='admin_tasks_filter_button_new_clause'}</a>
                    </div>
                </div>
                <div class="field">
                    <label>{translate line='admin_tasks_filter_label_filter_by_name'}:</label>
                    <p class="input"><input type="text" name="filter[name]" value="{$filter.name|escape:'html'}" /></p>
                </div>
                <div class="field">
                    <label>{translate line='admin_tasks_filter_label_filter_by_text'}:</label>
                    <p class="input"><input type="text" name="filter[text]" value="{$filter.text|escape:'html'}" /></p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_tasks_filter_submit_button'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter[fields][created]" value="{$filter.fields.created|default:0}" />
                    <input type="hidden" name="filter[fields][updated]" value="{$filter.fields.updated|default:0}" />
                    <input type="hidden" name="filter[fields][name]" value="{$filter.fields.name|default:1}" />
                    <input type="hidden" name="filter[fields][categories]" value="{$filter.fields.categories|default:1}" />
                    <input type="hidden" name="filter[fields][task_sets]" value="{$filter.fields.task_sets|default:1}" />
                </div>
            </form>
        </div>
        <a href="{internal_url url='admin_tasks/new_task'}" class="button">{translate line='admin_tasks_new_task_button_label'}</a>
        <div id="table_content_id"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    {include file='backend/categories/categories_parent_selector.tpl' inline}
    var messages = {
        delete_question: '{translate line="admin_tasks_javascript_message_delete_question"}',
        after_delete: '{translate line="admin_tasks_javascript_message_after_delete"}',
    }; 
    var category_select_box = '<select name="" size="1">{categories_tree_options structure=$structure}</select>';
</script>{/block}