{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="taks_set_name_id" class="required">{translate line='admin_task_sets_form_label_name'}:</label>
    <p class="input"><input name="task_set[name]" value="{$smarty.post.task_set.name|escape:'html'}" type="text" id="taks_set_name_id" /></p>
    {form_error field='task_set[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="taks_set_course_id_id" class="required">{translate line='admin_task_sets_form_label_course_id'}:</label>
    <p class="input"><select name="task_set[course_id]" size="1" id="taks_set_course_id_id">{list_html_options options=$courses selected=$smarty.post.task_set.course_id|intval}</select></p>
    {form_error field='task_set[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_type_field" style="display: none;">
    <label for="taks_set_task_set_type_id_id" class="required">{translate line='admin_task_sets_form_label_task_set_type_id'}:</label>
    <p class="input"><select name="task_set[task_set_type_id]" size="1" id="taks_set_task_set_type_id_id"></select></p>
    {form_error field='task_set[task_set_type_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_type_field_msg">
    <label class="required">{translate line='admin_task_sets_form_label_task_set_type_id'}:</label>
    <p class="input"><em>{translate line='admin_task_sets_form_label_task_set_type_id_hint'}</em></p>
    {form_error field='task_set[task_set_type_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_group_field" style="display: none;">
    <label for="taks_set_group_id_id">{translate line='admin_task_sets_form_label_group_id'}:</label>
    <p class="input"><select name="task_set[group_id]" size="1" id="taks_set_group_id_id"></select></p>
    {form_error field='task_set[group_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_group_field_else">
    <input type="hidden" name="task_set[group_id]" value="" />
</div>
<div class="field">
    <label for="task_set_published_id">{translate line='admin_task_sets_form_label_published'}:</label>
    <p class="input"><input type="checkbox" name="task_set[published]" value="1" id="task_set_published_id"{if $smarty.post.task_set.published eq 1} checked="checked"{/if} /></p>
    {form_error field='task_set[published]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="task_set_publish_start_time_id">{translate line='admin_task_sets_form_label_publish_start_time'}:</label>
    <p class="input"><input type="text" name="task_set[publish_start_time]" value="{$smarty.post.task_set.publish_start_time|escape:'html'}" id="task_set_publish_start_time_id" /></p>
    {form_error field='task_set[publish_start_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="task_set_upload_end_time_id">{translate line='admin_task_sets_form_label_upload_end_time'}:</label>
    <p class="input"><input type="text" name="task_set[upload_end_time]" value="{$smarty.post.task_set.upload_end_time|escape:'html'}" id="task_set_upload_end_time_id" /></p>
    <p class="input"><em>{translate line='admin_task_sets_form_label_upload_end_time_hint'}</em></p>
    {form_error field='task_set[upload_end_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_room_field" style="display: none;">
    <label for="taks_set_room_id_id">{translate line='admin_task_sets_form_label_room_id'}:</label>
    <p class="input"><select name="task_set[room_id]" size="1" id="taks_set_room_id_id"></select></p>
    {form_error field='task_set[room_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_room_field_else">
    <input type="hidden" name="task_set[room_id]" value="" />
</div>
<div class="field">
    <label for="task_set_points_override_enabled_id">{translate line='admin_task_sets_form_label_points_override_enabled'}:</label>
    <p class="input"><input type="checkbox" name="task_set[points_override_enabled]" value="1"{if $smarty.post.task_set.points_override_enabled} checked="checked"{/if} id="task_set_points_override_enabled_id" /></p>
</div>
<div class="field task_set_points_override" style="display: none;">
    <label for="task_set_points_override_id">{translate line='admin_task_sets_form_label_points_override'}:</label>
    <p class="input"><input type="text" name="task_set[points_override]" value="{$smarty.post.task_set.points_override|escape:'html'}" id="task_set_points_override_id" /></p>
    {form_error field='task_set[points_override]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="task_set_comments_enabled_id">{translate line='admin_task_sets_form_label_comments_enabled'}:</label>
    <p class="input"><input type="checkbox" name="task_set[comments_enabled]" value="1" id="task_set_comments_enabled_id"{if !$smarty.post.task_set or $smarty.post.task_set.comments_enabled} checked="checked"{/if} /></p>
    {form_error field='task_set[comments_enabled]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_comments_moderated" style="display: none;">
    <label for="task_set_comments_moderated_id">{translate line='admin_task_sets_form_label_comments_moderated'}:</label>
    <p class="input"><input type="checkbox" name="task_set[comments_moderated]" value="1" id="task_set_comments_moderated_id"{if $smarty.post.task_set.comments_moderated} checked="checked"{/if} /></p>
    {form_error field='task_set[comments_moderated]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field task_set_comments_moderated_else">
    <input type="hidden" name="task_set[comments_moderated]" value="0" /> 
</div>
<div class="field">
    <label for="task_set_allowed_file_types_id">{translate line='admin_task_sets_form_label_allowed_file_types'}:</label>
    <p class="input"><input type="text" name="task_set[allowed_file_types]" value="{$smarty.post.task_set.allowed_file_types|escape:'html'}" id="task_set_allowed_file_types_id" /></p>
    <p class="input"><em>{translate line='admin_task_sets_form_label_allowed_file_types_hint'}</em></p>
</div>
<div class="field">
    <label for="">{translate line='admin_task_sets_form_label_allowed_test_types'}:</label>
    <div class="input">
        {html_checkboxes name='task_set[allowed_test_types]' options=$test_types separator='<br />' selected=$smarty.post.task_set.allowed_test_types}
    </div>
</div>
<div class="field">
    <label for="task_set_enable_tests_scoring_id">{translate line='admin_task_sets_form_label_enable_tests_scoring'}:</label>
    <p class="input"><input type="checkbox" name="task_set[enable_tests_scoring]" value="1" id="task_set_enable_tests_scoring_id"{if $smarty.post.task_set.enable_tests_scoring} checked="checked"{/if} /></p>
</div>
<div class="field">
    <label for="task_set_internal_comment_id">{translate line='admin_task_sets_form_label_internal_comment'}:</label>
    <div class="input">
        <textarea name="task_set[internal_comment]" id="task_set_internal_comment_id">{$smarty.post.task_set.internal_comment|escape:'html'}</textarea>
    </div>
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_task_sets_form_button_submit'}" class="button" />
    <input type="hidden" name="post_selected_task_set_type_id" value="{$smarty.post.task_set.task_set_type_id|intval}" />
    <input type="hidden" name="post_selected_group_id_id" value="{$smarty.post.task_set.group_id|intval}" />
    <input type="hidden" name="post_selected_room_id_id" value="{$smarty.post.task_set.room_id|intval}" />
</div>
