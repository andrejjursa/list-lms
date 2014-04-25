<div class="field">
    <label for="configure_course_id_id" class="required">{translate line='widget_admin_course_overview_configure_form_label_course'}:</label>
    <div class="input">
        <select name="configure[course_id]" size="1" id="configure_course_id_id">
            {list_html_options options=$courses selected=$smarty.post.configure.course_id|default:$widget_config.course_id}
        </select>
    </div>
    {form_error field='configure[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>