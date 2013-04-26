{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="taks_set_name_id">{translate line='admin_task_sets_form_label_name'}:</label>
    <p class="input"><input name="task_set[name]" value="{$smarty.post.task_set.name|escape:'html'}" type="text" id="taks_set_name_id" /></p>
    {form_error field='task_set[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="taks_set_course_id_id">{translate line='admin_task_sets_form_label_course_id'}:</label>
    <p class="input"><select name="task_set[course_id]" size="1" id="taks_set_course_id_id">{list_html_options options=$courses selected=$smarty.post.task_set.course_id|intval}</select></p>
    {form_error field='task_set[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_type_field" style="display: none;">
    <label for="taks_set_task_set_type_id_id">{translate line='admin_task_sets_form_label_task_set_type_id'}:</label>
    <p class="input"><select name="task_set[task_set_type_id]" size="1" id="taks_set_task_set_type_id_id"></select></p>
    {form_error field='task_set[task_set_type_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_type_field_msg">
    <label>{translate line='admin_task_sets_form_label_task_set_type_id'}:</label>
    <p class="input"><em>{translate line='admin_task_sets_form_label_task_set_type_id_hint'}</em></p>
    {form_error field='task_set[task_set_type_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_task_sets_form_button_submit'}" class="button" />
    <input type="hidden" name="post_selected_task_set_type_id" value="{$smarty.post.task_set.task_set_type_id|intval}" />
</div>