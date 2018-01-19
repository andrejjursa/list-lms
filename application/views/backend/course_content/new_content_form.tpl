{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="course_content_course_id_id" class="required">{translate line='admin_course_content_form_label_course_id'}:</label>
    <p class="input"><select name="course_content[course_id]" size="1" id="course_content_course_id_id">{list_html_options options=$courses selected=$smarty.post.course_content.course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
    {form_error field='course_content[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_course_content_group_id_id">{translate line='admin_course_content_form_label_course_content_group_id'}:</label>
    <p class="input"><select name="course_content[course_content_group_id]" size="1" id="course_content_course_content_group_id_id"></select></p>
    {form_error field='course_content[course_content_group_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_title_id" class="required">{translate line='admin_course_content_form_label_title'}:</label>
    <p class="input"><input type="text" name="course_content[title]" id="course_content_title_id" value="{$smarty.post.course_content.title|htmlspecialchars}" /></p>
    {form_error field='course_content[title]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_content_id">{translate line='admin_course_content_form_label_content'}:</label>
    <p class="input"><textarea name="course_content[content]" id="course_content_content_id">{$smarty.post.course_content.content|htmlspecialchars}</textarea></p>
    {form_error field='course_content[content]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_published_from_id">{translate line='admin_course_content_form_label_published_from'}:</label>
    <p class="input"><input type="text" name="course_content[published_from]" id="course_content_published_from_id" value="{$smarty.post.course_content.published_from|htmlspecialchars}" /></p>
    {form_error field='course_content[published_from]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_published_to_id">{translate line='admin_course_content_form_label_published_to'}:</label>
    <p class="input"><input type="text" name="course_content[published_to]" id="course_content_published_to_id" value="{$smarty.post.course_content.published_to|htmlspecialchars}" /></p>
    {form_error field='course_content[published_to]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="course_content_public_id">{translate line='admin_course_content_form_label_public'}:</label>
    <p class="input"><input type="checkbox" name="course_content[public]" value="1" id="course_content_public_id"{if $smarty.post.course_content.public eq 1} checked="checked"{/if} /></p>
    {form_error field='course_content[public]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_course_content_form_button_submit'}" class="button" />
</div>