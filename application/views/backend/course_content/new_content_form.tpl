{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="course_content_course_id_id" class="required">{translate line='admin_course_content_form_label_course_id'}:</label>
    <p class="input"><select name="course_content[course_id]" size="1" id="course_content_course_id_id">{list_html_options options=$courses selected=$smarty.post.course_content.course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
    {form_error field='course_content[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_title_id" class="required">{translate line='admin_course_content_form_label_title'}:</label>
    <p class="input"><input type="text" name="course_content[title]" id="course_content_title_id" value="{$smarty.post.course_content.title|htmlspecialchars}" /></p>
    {form_error field='course_content[title]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_content_id" class="required">{translate line='admin_course_content_form_label_content'}:</label>
    <p class="input"><textarea name="course_content[content]" id="course_content_content_id">{$smarty.post.course_content.content|htmlspecialchars}</textarea></p>
    {form_error field='course_content[content]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_published_id">{translate line='admin_course_content_form_label_published'}:</label>
    <p class="input"><input type="checkbox" name="course_content[published]" value="1" id="course_content_published_id"{if $smarty.post.course_content.published eq 1} checked="checked"{/if} /></p>
    {form_error field='course_content[published]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>