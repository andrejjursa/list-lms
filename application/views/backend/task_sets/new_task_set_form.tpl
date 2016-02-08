{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="task_set_content_type_id" class="required">{translate line='admin_task_sets_form_label_content_type'}:</label>
    <div class="input">
        <select name="task_set[content_type]" size="1" id="task_set_content_type_id">
            {list_html_options options=['task_set' => 'lang:admin_task_sets_form_label_content_type_task_set', 'project' => 'lang:admin_task_sets_form_label_content_type_project']
             selected=$smarty.post.task_set.content_type}
        </select>
    </div>
    {form_error field='task_set[content_type]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="taks_set_name_id" class="required">{translate line='admin_task_sets_form_label_name'}:</label>
    <p class="input"><input name="task_set[name]" value="{$smarty.post.task_set.name|escape:'html'}" type="text" id="taks_set_name_id" /></p>
    {form_error field='task_set[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="columns">
    <div class="col_50p">
        <div class="field">
            <label for="taks_set_course_id_id" class="required">{translate line='admin_task_sets_form_label_course_id'}:</label>
            <p class="input"><select name="task_set[course_id]" size="1" id="taks_set_course_id_id">{list_html_options options=$courses selected=$smarty.post.task_set.course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
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
        <div class="field task_set_room_field" style="display: none;">
            <label for="taks_set_room_id_id">{translate line='admin_task_sets_form_label_room_id'}:</label>
            <p class="input"><select name="task_set[room_id]" size="1" id="taks_set_room_id_id"></select></p>
            {form_error field='task_set[room_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_room_field_else">
            <input type="hidden" name="task_set[room_id]" value="" />
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
        <div class="field task_set_upload_end_time">
            <label for="task_set_upload_end_time_id">{translate line='admin_task_sets_form_label_upload_end_time'}:</label>
            <p class="input"><input type="text" name="task_set[upload_end_time]" value="{$smarty.post.task_set.upload_end_time|escape:'html'}" id="task_set_upload_end_time_id" /></p>
            <p class="input"><em id="task_set_upload_end_time_hint_id">{translate line='admin_task_sets_form_label_upload_end_time_hint'}</em></p>
            {form_error field='task_set[upload_end_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_project_selection_deadline">
            <label for="task_set_project_selection_deadline_id" class="required">{translate line='admin_task_sets_form_label_project_selection_deadline'}:</label>
            <p class="input"><input type="text" name="task_set[project_selection_deadline]" value="{$smarty.post.task_set.project_selection_deadline|escape:'html'}" id="task_set_project_selection_deadline_id" /></p>
            {form_error field='task_set[project_selection_deadline]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_points_override_enabled">
            <label for="task_set_points_override_enabled_id">{translate line='admin_task_sets_form_label_points_override_enabled'}:</label>
            <p class="input"><input type="checkbox" name="task_set[points_override_enabled]" value="1"{if $smarty.post.task_set.points_override_enabled} checked="checked"{/if} id="task_set_points_override_enabled_id" /></p>
            {form_error field='task_set[points_override_enabled]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_points_override_enabled_project">
            <input type="hidden" name="task_set[points_override_enabled]" value="1" />
        </div>
        <div class="field task_set_points_override" style="display: none;">
            <label for="task_set_points_override_id">{translate line='admin_task_sets_form_label_points_override'}:</label>
            <p class="input"><input type="text" name="task_set[points_override]" value="{$smarty.post.task_set.points_override|escape:'html'}" id="task_set_points_override_id" /></p>
            {form_error field='task_set[points_override]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_comments_enabled">
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
    </div>
    <div class="col_50p">
        <div class="field task_sets_form_label_allowed_file_types">
            <label for="task_set_allowed_file_types_id">{translate line='admin_task_sets_form_label_allowed_file_types'}:</label>
            <p class="input"><input type="text" name="task_set[allowed_file_types]" value="{$smarty.post.task_set.allowed_file_types|escape:'html'}" id="task_set_allowed_file_types_id" /></p>
            <p class="input"><em>{translate line='admin_task_sets_form_label_allowed_file_types_hint'}</em></p>
        </div>
        <div class="field task_sets_form_label_allowed_test_types">
            <label for="">{translate line='admin_task_sets_form_label_allowed_test_types'}:</label>
            <div class="input">
                {html_checkboxes name='task_set[allowed_test_types]' options=$test_types separator='<br />' selected=$smarty.post.task_set.allowed_test_types}
            </div>
        </div>
        <div class="field task_sets_form_label_enable_tests_scoring">
            <label for="task_set_enable_tests_scoring_id">{translate line='admin_task_sets_form_label_enable_tests_scoring'}:</label>
            <p class="input"><input type="checkbox" name="task_set[enable_tests_scoring]" value="1" id="task_set_enable_tests_scoring_id"{if $smarty.post.task_set.enable_tests_scoring} checked="checked"{/if} /></p>
        </div>
        <div class="field task_set_test_min_needed" style="display: none;">
            <label for="task_set_test_min_needed_id">{translate line='admin_task_sets_form_label_test_min_needed'}:</label>
            <p class="input"><input name="task_set[test_min_needed]" value="{$smarty.post.task_set.test_min_needed|escape:'html'}" type="text" id="task_set_test_min_needed_id" /></p>
            <p class="input"><em>{translate line='admin_task_sets_form_label_test_min_needed_hint'}</em></p>
            {form_error field='task_set[test_min_needed]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_test_max_allowed" style="display: none;">
            <label for="task_set_test_max_allowed_id">{translate line='admin_task_sets_form_label_test_max_allowed'}:</label>
            <p class="input"><input name="task_set[test_max_allowed]" value="{$smarty.post.task_set.test_max_allowed|escape:'html'}" type="text" id="task_set_test_max_allowed_id" /></p>
            <p class="input"><em>{translate line='admin_task_sets_form_label_test_max_allowed_hint'}</em></p>
            {form_error field='task_set[test_max_allowed]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field task_set_test_priority">
            <label for="task_set_test_priority_id">{translate line='admin_task_sets_form_label_test_priority'}:</label>
            <div class="input">
                <select name="task_set[test_priority]" size="1" id="task_set_test_priority_id">
                    {list_html_options options=[1=>'lang:admin_task_sets_test_priority_level_1', 2=>'lang:admin_task_sets_test_priority_level_2',3=>'lang:admin_task_sets_test_priority_level_3'] selected=$smarty.post.task_set.task_priority|default:2}
                </select>
            </div>
        </div>
        <div class="field">
            <label for="task_set_deadline_notification_emails_id">{translate line='admin_task_sets_form_label_deadline_notification_emails'}:</label>
            <p class="input"><input type="text" name="task_set[deadline_notification_emails]" id="task_set_deadline_notification_emails_id" value="{$smarty.post.task_set.deadline_notification_emails|escape:'html'}" /></p>
            {form_error field='task_set[deadline_notification_emails]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
        <div class="field">
            <label class="required">{translate line='admin_task_sets_form_label_deadline_notification_emails_handler'}:</label>
            <div class="input">
                <label><input type="radio" name="task_set[deadline_notification_emails_handler]" value="0"{if isset($smarty.post.task_set.deadline_notification_emails_handler) and $smarty.post.task_set.deadline_notification_emails_handler eq 0} checked="checked"{/if} /> {translate line='admin_task_sets_form_label_deadline_notification_emails_handler_0'}</label><br />
                <label><input type="radio" name="task_set[deadline_notification_emails_handler]" value="1"{if isset($smarty.post.task_set.deadline_notification_emails_handler) and $smarty.post.task_set.deadline_notification_emails_handler eq 1} checked="checked"{/if} /> {translate line='admin_task_sets_form_label_deadline_notification_emails_handler_1'}</label><br />
                <label><input type="radio" name="task_set[deadline_notification_emails_handler]" value="2"{if isset($smarty.post.task_set.deadline_notification_emails_handler) and $smarty.post.task_set.deadline_notification_emails_handler eq 2} checked="checked"{/if} /> {translate line='admin_task_sets_form_label_deadline_notification_emails_handler_2'}</label>
            </div>
            {form_error field='task_set[deadline_notification_emails_handler]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="field">
    <label for="task_set_internal_comment_id">{translate line='admin_task_sets_form_label_internal_comment'}:</label>
    <div class="input">
        <textarea name="task_set[internal_comment]" id="task_set_internal_comment_id">{$smarty.post.task_set.internal_comment|escape:'html'}</textarea>
    </div>
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_task_sets_form_button_submit'}" class="button" />
    <input type="submit" name="submit_and_open_button" value="{translate line='admin_task_sets_form_button_submit_and_open'}" class="button" />
    <a href="{internal_url url='help/show/admin_task_sets/new_task_set'}" class="button help">{translate line='admin_task_sets_help_button_new_task_set'}</a>
    <input type="hidden" name="post_selected_task_set_type_id" value="{$smarty.post.task_set.task_set_type_id|intval}" />
    <input type="hidden" name="post_selected_group_id_id" value="{$smarty.post.task_set.group_id|intval}" />
    <input type="hidden" name="post_selected_room_id_id" value="{$smarty.post.task_set.room_id|intval}" />
</div>
<script type="text/javascript">
    var open_task_set_id = {if $url_params.force_open_task_set_id}{$url_params.force_open_task_set_id|intval}{else}null{/if};
</script>