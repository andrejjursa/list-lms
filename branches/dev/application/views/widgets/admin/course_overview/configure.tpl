<div class="field">
    <label for="configure_course_id_id" class="required">{translate line='widget_admin_course_overview_configure_form_label_course'}:</label>
    <div class="input">
        <select name="configure[course_id]" size="1" id="configure_course_id_id">
            {list_html_options options=$courses selected=$smarty.post.configure.course_id|default:$widget_config.course_id}
        </select>
    </div>
    {form_error field='configure[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="configure_number_of_task_sets_id" class="required">{translate line='widget_admin_course_overview_configure_form_label_number_of_task_sets'}:</label>
    <div class="input">
        <select name="configure[number_of_task_sets]" size="1" id="configure_number_of_task_sets_id">
            {$options = ['' => '', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10]}
            {list_html_options options=$options selected=$smarty.post.configure.number_of_task_sets|default:$widget_config.number_of_task_sets}
        </select>
    </div>
    {form_error field='configure[number_of_task_sets]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>