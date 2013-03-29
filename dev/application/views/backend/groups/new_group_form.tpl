{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="group_name_id">{translate line='admin_groups_form_label_group_name'}:</label>
    <p class="input"><input type="text" name="group[name]" value="{$smarty.post.group.name|escape:'html'}" id="group_name_id" /></p>
    {form_error field='group[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="group_course_id_id">{translate line='admin_groups_form_label_group_course'}:</label>
    <p class="input"><select name="group[course_id]" size="1" id="group_course_id_id">
        {list_html_options options=$courses selected=$smarty.post.group.course_id}
    </select></p>
    {form_error field='group[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_groups_form_button_save'}" class="button" />
</div>