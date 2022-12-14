{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="task_set_type_id_id">{translate line='admin_courses_form_label_task_set_type_name'}:</label>
    <p class="input">
        <select name="task_set_type[id]" size="1" id="task_set_type_id_id">
            {list_html_options options=$task_set_types selected=$smarty.post.task_set_type.id|intval}
        </select>
        {form_error field='task_set_type[id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="field">
    <label for="task_set_type_join_upload_solution_id">{translate line='admin_courses_form_label_upload_solution'}:</label>
    <p class="input">
        <select name="task_set_type[join_upload_solution]" size="1" id="task_set_type_join_upload_solution_id">
            {list_html_options options=[1 => {translate line='admin_courses_form_select_option_upload_solution_yes'}, 0 => {translate line='admin_courses_form_select_option_upload_solution_no'}]
             selected=$smarty.post.task_set_type.join_upload_solution|default:1|intval}
        </select>
        {form_error field='task_set_type[join_upload_solution]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="buttons">
    <input type="submit" value="{translate line='admin_courses_form_task_set_type_button_submit'}" name="submit_button" class="button" />
    <a href="{internal_url url="admin_courses"}" class="button special">{translate line='common_button_back'}</a>
</div>