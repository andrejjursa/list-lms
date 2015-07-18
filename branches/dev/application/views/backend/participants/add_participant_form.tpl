{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="participant_course_id" class="required">{translate line='admin_participants_form_label_course'}:</label>
    <p class="input"><select name="participant[course]" size="1" id="participant_course_id">{list_html_options options=$courses selected=$smarty.post.participant.course|default:$list_teacher_account.prefered_course_id|intval}</select></p>
    {form_error field='participant[course]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field group_field" style="display: none;">
    <label for="participant_group_id">{translate line='admin_participants_form_label_group'}:</label>
    <p class="input"><select name="participant[group]" size="1" id="participant_group_id"></select></p>
</div>
<div class="group_field_else">
    <input type="hidden" name="participant[group_id]" value="" />
</div>
<div class="field">
    <label for="participant_allowed_id">{translate line='admin_participants_form_label_allowed'}:</label>
    <p class="input"><input type="checkbox" name="participant[allowed]" value="1"{if $smarty.post.participant.allowed eq 1 or !$smarty.post} checked="checked"{/if} /></p>
</div>
<div class="field">
    <label for="participant_student_searchbox_id" class="required">{translate line='admin_participants_form_label_students'}:</label>
    <p class="input"><input type="text" name="participant_student_searchbox" value="" id="participant_student_searchbox_id" style="width: 200px;" /> <input type="button" class="button special" name="add_student_to_list_button" value="{translate line='admin_participants_form_button_add_student_to_list'}" id="add_student_to_list_button_id" /></p>
    <div class="input">
        <input type="hidden" name="hidden_participant_student_name" value="" id="hidden_participant_student_name_id" />
        <input type="hidden" name="hidden_participant_student_id" value="" id="hidden_participant_student_id_id" />
        <select name="participants_names" size="5" multiple="multiple" style="min-width: 210px;" id="participants_names_id">
            {foreach $smarty.post.participant_students as $participant_student}<option value="{$participant_student@key|intval}">{$participant_student|escape:'html'}</option>{/foreach}
        </select>
        <input type="button" name="remove_students_from_list_button" value="{translate line='admin_participants_form_button_remove_students_from_list'}" id="remove_students_from_list_button_id" class="button delete" />
    </div>
    {form_error field='participant[students][]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    <div id="real_participants_list_id">
        {foreach $smarty.post.participant.students as $participant_student}<input type="hidden" name="participant[students][{$participant_student|intval}]" value="{$participant_student|intval}" />{/foreach}
    </div>
    <div id="names_participants_list_id">
        {foreach $smarty.post.participant_students as $participant_student}<input type="hidden" name="participant_students[{$participant_student@key|intval}]" value="{$participant_student|escape:'html'}" />{/foreach}
    </div>
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_participants_form_button_submit'}" class="button" />
    <input type="hidden" name="participant_selected_group_id" value="{$smarty.post.participant.group|intval}" />
</div>