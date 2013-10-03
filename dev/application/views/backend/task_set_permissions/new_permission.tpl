{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_task_set_permisions_page_title_new_permission'}{/block}
{block main_content}
    <h3>{translate line='admin_task_set_permisions_page_title_new_permission'}</h3>
    {if $task_set->exists()}
        <h4>{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} ({translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name})</h4>
    {/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $task_set->exists() && !is_null($task_set->course_id)}
        <fieldset>
            <form action="{internal_url url="admin_task_set_permissions/create_permission/{$task_set->id|intval}"}" method="post">
                <div class="field task_set_group_field">
                    <label for="taks_set_permission_group_id_id" class="required">{translate line='admin_task_sets_form_label_group_id'}:</label>
                    <p class="input"><select name="task_set_permission[group_id]" size="1" id="taks_set_permission_group_id_id">
                        {list_html_options options=$groups selected=$smarty.post.task_set_permission.group_id}
                    </select></p>
                    {form_error field='task_set_permission[group_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="task_set_permission_publish_start_time_id">{translate line='admin_task_sets_form_label_publish_start_time'}:</label>
                    <p class="input"><input type="text" name="task_set_permission[publish_start_time]" value="{$smarty.post.task_set_permission.publish_start_time|escape:'html'}" id="task_permission_set_publish_start_time_id" /></p>
                    {form_error field='task_set_permission[publish_start_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="task_set_permission_upload_end_time_id">{translate line='admin_task_sets_form_label_upload_end_time'}:</label>
                    <p class="input"><input type="text" name="task_set_permission[upload_end_time]" value="{$smarty.post.task_set_permission.upload_end_time|escape:'html'}" id="task_set_permission_upload_end_time_id" /></p>
                    <p class="input"><em>{translate line='admin_task_sets_form_label_upload_end_time_hint'}</em></p>
                    {form_error field='task_set_permission[upload_end_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field task_set_permission_room_field" style="display: none;">
                    <label for="taks_set_permission_room_id_id">{translate line='admin_task_sets_form_label_room_id'}:</label>
                    <p class="input"><select name="task_set_permission[room_id]" size="1" id="taks_set_permission_room_id_id"></select></p>
                    {form_error field='task_set_permission[room_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field task_set_permission_room_field_else">
                    <input type="hidden" name="task_set_permission[room_id]" value="" />
                </div>
                <div class="field">
                    <label for="task_set_permission_enabled_id">{translate line='admin_task_set_permissions_form_label_enabled'}:</label>
                    <p class="input"><input type="checkbox" name="task_set_permission[enabled]" value="1" id="task_set_permission_enabled_id"{if $smarty.post.task_set_permission.enabled eq 1} checked="checked"{/if} /></p>
                    {form_error field='task_set_permission[enabled]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_task_set_permissions_form_button_submit'}" class="button" />
                </div>
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_set_permissions_error_message_cant_find_task_set_or_course' inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var all_rooms = {$all_rooms|json_encode};
    var course_id = {$task_set->course_id|intval};
    var task_set_id = {$task_set->id|intval};
</script>{/block}