{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tasks_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {include file='backend/tasks/category_checkboxes.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_tasks_fieldset_legend_new_task'}</legend>
        <form action="{internal_url url='admin_tasks/create'}" method="post">
            <div class="field">
                <label for="task_name_id" class="required">{translate line='admin_tasks_form_label_name'}:</label>
                <p class="input"><input type="text" name="task[name]" value="{$smarty.post.task.name|escape:'html'}" id="task_name_id" /></p>
                {form_error field='task[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="task_text_id" class="required">{translate line='admin_tasks_form_label_text'}:</label>
                <p class="input"><textarea name="task[text]" id="task_text_id" class="tinymce">{$smarty.post.task.text}</textarea></p>
                {form_error field='task[text]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label class="required">{translate line='admin_tasks_form_label_categories'}:</label>
                <div class="input categories_structure">
                    <div class="categories_structure_switch">
                    {category_checkboxes chbname='task[categories][]' structure=$structure selected=$smarty.post.task.categories|default:[]}
                    </div>
                </div>
                {form_error field='task[categories][]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_tasks_form_button_save'}" class="button" />
                <input type="submit" name="submit_and_go_to_list" value="{translate line='admin_tasks_form_button_save_and_go_to_list'}" class="button" />
            </div>
        </form>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var highlighters = {$highlighters|json_encode};
    var categories_switch_text = '{translate line='admin_tasks_javascript_text_switch_categories'}';
</script>{/block}