{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_task_sets_page_header'}{/block}
{block main_content}
	<h2>{translate line='admin_task_sets_page_header'}</h2>
	{include file='partials/backend_general/flash_messages.tpl' inline}
        {if $task_set->exists() or $smarty.post.task_set}
        <form action="{internal_url url="admin_task_sets/update"}" method="post">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-about_task_set">{translate line='admin_task_sets_tabs_label_about_task_set'}</a></li>
                    <li><a href="#tabs-additional_permissions">{translate line='admin_task_sets_tabs_label_additional_permissions'}</a></li>
                    <li><a href="#tabs-tasks">{translate line='admin_task_sets_tabs_label_tasks'}</a></li>
                    <li><a href="#tabs-instructions">{translate line='admin_task_sets_tabs_label_instructions'}</a></li>
                </ul>
                <div id="tabs-about_task_set">
                    <div class="field">
                        <label for="taks_set_name_id" class="required">{translate line='admin_task_sets_form_label_name'}:</label>
                        <p class="input"><input name="task_set[name]" value="{$smarty.post.task_set.name|default:$task_set->name|escape:'html'}" type="text" id="taks_set_name_id" /></p>
                        {form_error field='task_set[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                        {include file='partials/backend_general/overlay_editor.tpl' table='task_sets' table_id=$task_set->id column='name' editor_type='input' inline}
                    </div>
                    <div class="field">
                        <label for="taks_set_course_id_id" class="required">{translate line='admin_task_sets_form_label_course_id'}:</label>
                        <p class="input"><select name="task_set[course_id]" size="1" id="taks_set_course_id_id">{list_html_options options=$courses selected=$smarty.post.task_set.course_id|default:$task_set->course_id|intval}</select></p>
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
                        <p class="input"><input type="checkbox" name="task_set[published]" value="1" id="task_set_published_id"{if $smarty.post.task_set.published|default:$task_set->published eq 1} checked="checked"{/if} /></p>
                        {form_error field='task_set[published]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="task_set_publish_start_time_id">{translate line='admin_task_sets_form_label_publish_start_time'}:</label>
                        <p class="input"><input type="text" name="task_set[publish_start_time]" value="{$smarty.post.task_set.publish_start_time|default:$task_set->publish_start_time|escape:'html'}" id="task_set_publish_start_time_id" /></p>
                        {form_error field='task_set[publish_start_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="task_set_upload_end_time_id">{translate line='admin_task_sets_form_label_upload_end_time'}:</label>
                        <p class="input"><input type="text" name="task_set[upload_end_time]" value="{$smarty.post.task_set.upload_end_time|default:$task_set->upload_end_time|escape:'html'}" id="task_set_upload_end_time_id" /></p>
                        <p class="input"><em>{translate line='admin_task_sets_form_label_upload_end_time_hint'}</em></p>
                        {form_error field='task_set[upload_end_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field task_set_room_field" style="display: none;">
                        <label for="taks_set_room_id_id">{translate line='admin_task_sets_form_label_room_id'}:</label>
                        <p class="input"><select name="task_set[room_id]" size="1" id="taks_set_room_id_id"></select></p>
                        {form_error field='task_set[group_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field task_set_room_field_else">
                        <input type="hidden" name="task_set[room_id]" value="" />
                    </div>
                    <div class="field">
                        <label for="task_set_points_override_enabled_id">{translate line='admin_task_sets_form_label_points_override_enabled'}:</label>
                        <p class="input"><input type="checkbox" name="task_set[points_override_enabled]" value="1"{if $smarty.post.task_set.points_override_enabled or (!$smarty.post and !is_null($task_set->points_override))} checked="checked"{/if} id="task_set_points_override_enabled_id" /></p>
                    </div>
                    <div class="field task_set_points_override" style="display: none;">
                        <label for="task_set_points_override_id">{translate line='admin_task_sets_form_label_points_override'}:</label>
                        <p class="input"><input type="text" name="task_set[points_override]" value="{$smarty.post.task_set.points_override|default:$task_set->points_override|escape:'html'}" id="task_set_points_override_id" /></p>
                        {form_error field='task_set[points_override]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="task_set_comments_enabled_id">{translate line='admin_task_sets_form_label_comments_enabled'}:</label>
                        <p class="input"><input type="checkbox" name="task_set[comments_enabled]" value="1" id="task_set_comments_enabled_id"{if $smarty.post.task_set.comments_enabled|default:$task_set->comments_enabled} checked="checked"{/if} /></p>
                        {form_error field='task_set[comments_enabled]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field task_set_comments_moderated" style="display: none;">
                        <label for="task_set_comments_moderated_id">{translate line='admin_task_sets_form_label_comments_moderated'}:</label>
                        <p class="input"><input type="checkbox" name="task_set[comments_moderated]" value="1" id="task_set_comments_moderated_id"{if $smarty.post.task_set.comments_moderated|default:$task_set->comments_moderated} checked="checked"{/if} /></p>
                        {form_error field='task_set[comments_moderated]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field task_set_comments_moderated_else">
                        <input type="hidden" name="task_set[comments_moderated]" value="0" /> 
                    </div>
                    <div class="field">
                        <label for="task_set_allowed_file_types_id">{translate line='admin_task_sets_form_label_allowed_file_types'}:</label>
                        <p class="input"><input type="text" name="task_set[allowed_file_types]" value="{$smarty.post.task_set.allowed_file_types|default:$task_set->allowed_file_types|escape:'html'}" id="task_set_allowed_file_types_id" /></p>
                        <p class="input"><em>{translate line='admin_task_sets_form_label_allowed_file_types_hint'}</em></p>
                    </div>
                    <div class="field">
                        {$selected_allowed_test_types = ','|explode:$task_set->allowed_test_types}
                        <label for="">{translate line='admin_task_sets_form_label_allowed_test_types'}:</label>
                        <div class="input">
                            {html_checkboxes name='task_set[allowed_test_types]' options=$test_types separator='<br />' selected=$smarty.post.task_set.allowed_test_types|default:$selected_allowed_test_types}
                        </div>
                    </div>
                    <div class="field">
                        <label for="task_set_enable_tests_scoring_id">{translate line='admin_task_sets_form_label_enable_tests_scoring'}:</label>
                        <p class="input"><input type="checkbox" name="task_set[enable_tests_scoring]" value="1" id="task_set_enable_tests_scoring_id"{if $smarty.post.task_set.enable_tests_scoring|default:$task_set->enable_tests_scoring} checked="checked"{/if} /></p>
                    </div>
                    <div class="field task_set_test_min_needed" style="display: none;">
                        <label for="task_set_test_min_needed_id">{translate line='admin_task_sets_form_label_test_min_needed'}:</label>
                        <p class="input"><input name="task_set[test_min_needed]" value="{$smarty.post.task_set.test_min_needed|default:$task_set->test_min_needed|escape:'html'}" type="text" id="task_set_test_min_needed_id" /></p>
                        <p class="input"><em>{translate line='admin_task_sets_form_label_test_min_needed_hint'}</em></p>
                        {form_error field='task_set[test_min_needed]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field task_set_test_max_allowed" style="display: none;">
                        <label for="task_set_test_max_allowed_id">{translate line='admin_task_sets_form_label_test_max_allowed'}:</label>
                        <p class="input"><input name="task_set[test_max_allowed]" value="{$smarty.post.task_set.test_max_allowed|default:$task_set->test_max_allowed|escape:'html'}" type="text" id="task_set_test_max_allowed_id" /></p>
                        <p class="input"><em>{translate line='admin_task_sets_form_label_test_max_allowed_hint'}</em></p>
                        {form_error field='task_set[test_max_allowed]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="task_set_internal_comment_id">{translate line='admin_task_sets_form_label_internal_comment'}:</label>
                        <div class="input">
                            <textarea name="task_set[internal_comment]" id="task_set_internal_comment_id">{$smarty.post.task_set.internal_comment|default:$task_set->internal_comment|escape:'html'}</textarea>
                        </div>
                    </div>
                </div>
                <div id="tabs-additional_permissions">
                    <fieldset class="basefieldset">
                        <a href="{internal_url url="admin_task_set_permissions/new_permission/{$task_set->id|intval}"}" class="button new_permission">{translate line='admin_task_sets_permission_button_new_permission'}</a>
                    </fieldset>
                    <fieldset class="basefieldset">
                        <p>{translate line='admin_task_sets_permissions_info'}</p>
                    </fieldset>
                    <fieldset class="basefieldset">
                        <div id="additional_permissions_id"></div>
                    </fieldset>
                </div>
                <div id="tabs-tasks">
                    <ul id="tasks_sortable">
                        {$tasks_sorting = []}
                        {foreach $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get_iterated() as $task}
                            {$tasks_sorting[] = $task->id|intval}
                            <li id="task_{$task->id|intval}" class="ui-state-default task_sorting_item">
                                <h4><span class="ui-icon ui-icon-arrowthick-2-n-s" style="float: left;"></span> {overlay table='tasks' table_id=$task->id column='name' default=$task->name}</h4>
                                <div class="field">
                                    <label for="task_join_field_{$task->id|intval}_points_total_id" class="required">{translate line='admin_task_sets_form_label_task_points_total'}:</label>
                                    <p class="input"><input type="text" name="task_join_field[{$task->id|intval}][points_total]" value="{$smarty.post.task_join_field[$task->id|intval].points_total|default:$task->join_points_total|floatval}" id="task_join_field_{$task->id|intval}_points_total_id" /></p>
                                    {form_error field="task_join_field[{$task->id|intval}][points_total]" left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                                </div>
                                <div class="field">
                                    <label for="task_join_field_{$task->id|intval}_test_min_points_id" class="required">{translate line='admin_task_sets_form_label_task_test_min_points'}:</label>
                                    <p class="input"><input type="text" name="task_join_field[{$task->id|intval}][test_min_points]" value="{$smarty.post.task_join_field[$task->id|intval].test_min_points|default:$task->join_test_min_points|floatval}" id="task_join_field_{$task->id|intval}_test_min_points_id" /></p>
                                    {form_error field="task_join_field[{$task->id|intval}][test_min_points]" left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                                </div>
                                <div class="field">
                                    <label for="task_join_field_{$task->id|intval}_test_max_points_id" class="required">{translate line='admin_task_sets_form_label_task_test_max_points'}:</label>
                                    <p class="input"><input type="text" name="task_join_field[{$task->id|intval}][test_max_points]" value="{$smarty.post.task_join_field[$task->id|intval].test_max_points|default:$task->join_test_max_points|floatval}" id="task_join_field_{$task->id|intval}_test_max_points_id" /></p>
                                    {form_error field="task_join_field[{$task->id|intval}][test_max_points]" left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                                </div>
                                <div class="field">
                                    <label for="task_join_field_{$task->id|intval}_bonus_task_id">{translate line='admin_task_sets_form_label_task_bonus_task'}:</label>
                                    <p class="input"><input type="checkbox" name="task_join_field[{$task->id|intval}][bonus_task]" value="1" id="task_join_field_{$task->id|intval}_bonus_task_id"{if $smarty.post.task_join_field[$task->id|intval].bonus_task|default:$task->join_bonus_task} checked="checked"{/if} /></p>
                                </div>
                                <div class="field">
                                    <label for="task_join_field_{$task->id|intval}_delete_id">{translate line='admin_task_sets_form_label_delete_task'}:</label>
                                    <p class="input"><input type="checkbox" name="task_join_field[{$task->id|intval}][delete]" value="1"{if $smarty.post.task_join_field[$task->id|intval].delete eq 1} checked="checked"{/if} class="delete_checkbox" id="task_join_field_{$task->id|intval}_delete_id" /></p>
                                </div>
                                <div class="field">
                                    <label for="task_join_field_{$task->id|intval}_internal_comment_id">{translate line='admin_task_sets_form_label_internal_comment'}:</label>
                                    <div class="input">
                                        <div class="internal_comment">
                                            <textarea name="task_join_field[{$task->id|intval}][internal_comment]" id="task_join_field_{$task->id|intval}_internal_comment_id">{$smarty.post.task_join_field[$task->id|intval].internal_comment|default:$task->join_internal_comment|escape:'html'}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="input">
                                        <div class="task">
                                            <div class="text_content">{overlay|add_base_url table='tasks' column='text' table_id=$task->id default=$task->text}</div>
                                            <hr />
                                            <a href="{internal_url url="admin_tasks/edit/task_id/{$task->id}"}" target="_blank" class="button special">{translate line='admin_task_sets_edit_task_button'}</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
                <div id="tabs-instructions">
                    <div class="field">
                        <label for="task_set_instructions_id">{translate line='admin_task_sets_form_label_instructions'}:</label>
                        <p class="input"><textarea name="task_set[instructions]" class="tinymce">{$smarty.post.task_set.instructions|default:$task_set->instructions|add_base_url}</textarea></p>
                        {include file='partials/backend_general/overlay_editor.tpl' table='task_sets' column='instructions' table_id=$task_set->id|intval editor_type='textarea' class='tinymce' inline}
                    </div>
                </div>
            </div>
                    <fieldset class="basefieldset">
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_task_sets_form_button_submit'}" class="button" /> <a href="{internal_url url='admin_task_sets'}" class="button special">{translate line='common_button_back'}</a>
                    <input type="hidden" name="post_selected_task_set_type_id" value="{$smarty.post.task_set.task_set_type_id|default:$task_set->task_set_type_id|intval}" />
                    <input type="hidden" name="post_selected_group_id_id" value="{$smarty.post.task_set.group_id|default:$task_set->group_id|intval}" />
                    <input type="hidden" name="post_selected_room_id_id" value="{$smarty.post.task_set.room_id|default:$task_set->room_id|intval}" />
                    <input type="hidden" name="task_set_id" value="{$smarty.post.task_set_id|default:$task_set->id|intval}" />
                    <input type="hidden" name="tasks_sorting" value="{$smarty.post.tasks_sorting|default:{','|implode:$tasks_sorting}}" />
                </div>
            </fieldset>
        </form>
        {else}
            {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_error_task_set_not_found' inline}
        {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_set_id = {$task_set->id|intval};
    var delete_question = '{translate line='admin_task_sets_javascript_remove_task_question'}';
    var delete_permission_question = '{translate line='admin_task_sets_javascript_remove_task_set_permission_question'}';
    var task_text_title = '{translate line='admin_task_sets_javascript_task_text_title'}';
    var all_groups = {$all_groups|json_encode};
    var all_rooms = {$all_rooms|json_encode};
    var all_task_set_types = {$all_task_set_types|json_encode};
    var internal_comment_title = '{translate line='admin_task_sets_form_label_internal_comment'}';
</script>
{/block}