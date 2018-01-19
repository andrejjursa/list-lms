{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="course_content_group_course_id_id" class="required">{translate line='admin_course_content_groups_form_label_course_id'}:</label>
    <p class="input"><select name="course_content_group[course_id]" size="1" id="course_content_group_course_id_id">{list_html_options options=$courses selected=$smarty.post.course_content_group.course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
    {form_error field='course_content_group[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_group_title_id" class="required">{translate line='admin_course_content_groups_form_label_title'}:</label>
    <p class="input"><input type="text" name="course_content_group[title]" id="course_content_group_title_id" value="{$smarty.post.course_content.title|htmlspecialchars}" /></p>
    {form_error field='course_content_group[title]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_course_content_groups_form_button_submit'}" class="button" />
</div>