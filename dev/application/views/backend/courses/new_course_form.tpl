{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="course_name_id">{translate line='admin_courses_form_label_course_name'}:</label>
    <p class="input"><input type="text" name="course[name]" value="{$smarty.post.course.name|escape:'html'}" id="course_name_id" /></p>
    {form_error field='course[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_period_id_id">{translate line='admin_courses_form_label_course_period'}:</label>
    <p class="input"><select name="course[period_id]" size="1">
        {list_html_options options=$periods selected=$smarty.post.course.period_id}
    </select></p>
    {form_error field='course[period_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_description_id">{translate line='admin_courses_form_label_course_description'}:</label>
    <p class="input"><textarea name="course[description]" id="course_description_id" class="tinymce">{$smarty.post.course.description|escape:'html'}</textarea></p>
    {form_error field='course[description]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_capacity_id">{translate line='admin_courses_form_label_course_capacity'}:</label>
    <p class="input"><input type="text" name="course[capacity]" id="course_capacity_id" value="{$smarty.post.course.capacity|escape:'html'}" /></p>
    {form_error field='course[capacity]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" class="button" value="{translate line='admin_courses_form_button_submit'}" />
</div>