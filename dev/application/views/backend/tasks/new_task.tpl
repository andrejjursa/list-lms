{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tasks_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {include file='backend/categories/categories_parent_selector.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_tasks_fieldset_legend_new_task'}</legend>
        <form action="{internal_url url='admin_tasks/create'}" method="post">
            <div class="field">
                <label for="task_name_id">{translate line='admin_tasks_form_label_name'}:</label>
                <p class="input"><input type="text" name="task[name]" value="{$smarty.post.task.name|escape:'html'}" id="task_name_id" /></p>
                {form_error field='task[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="task_text_id">{translate line='admin_tasks_form_label_text'}:</label>
                <p class="input"><textarea name="task[text]" id="task_text_id"></textarea></p>
                {form_error field='task[text]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="task_categories_id">{translate line='admin_tasks_form_label_categories'}:</label>
                <p class="input"><select size="10" name="task[categories]" multiple="multiple" id="task_categories_id">
                    {categories_tree_options_multiple structure=$structure selected=$smarty.post.task.categories|default:[]}
                </select></p>
                {form_error field='task[categories]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
        </form>
    </fieldset>
{/block}