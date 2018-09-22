{extends file='layouts/backend_popup.tpl'}
{block main_content}
    <h3>{translate line='admin_groups_group_email_page_title'}</h3>
    {if $group->exists()}<h4>{translate_text text=$group->name} ({translate_text text=$group->course_name} / {translate_text text=$group->course_period_name})</h4>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $group->exists()}
        {if $students->exists()}
        <form action="{internal_url url="admin_groups/send_group_mail/{$group->id}"}" method="post">
            <div class="field">
                <label for="group_mail_subject_id" class="required">{translate line='admin_groups_group_email_form_label_subject'}:</label>
                <p class="input"><input type="text" name="group_mail[subject]" value="{$smarty.post.group_mail.subject|escape:'html'}" id="group_mail_subject_id" /></p>
                {form_error field='group_mail[subject]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="group_mail_body_id" class="required">{translate line='admin_groups_group_email_form_label_body'}:</label>
                <div class="input"><textarea name="group_mail[body]" id="group_mail_body_id" class="tinymce">{$smarty.post.group_mail.body|htmlspecialchars}</textarea></div>
                {form_error field='group_mail[body]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="group_mail_from_id" class="required">{translate line='admin_groups_group_email_form_label_from'}:</label>
                <div class="input">
                    <select name="group_mail[from]" size="1" id="group_mail_from_id">
                        {list_html_options
                            options=[''=>'','system'=>'lang:admin_groups_group_email_from_system','me'=>'lang:admin_groups_group_email_from_me']
                            selected=$smarty.post.group_mail.from}
                    </select><br><br><label><input type="checkbox" name="group_mail[sender_copy]" value="1" {if !$smarty.post or $smarty.post.group_mail.sender_copy eq 1}checked="checked"{/if} /> {translate line="admin_groups_group_email_form_label_sender_copy"}</label>
                </div>
                {form_error field='group_mail[from]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label class="required">{translate line='admin_groups_group_email_form_label_students'}:</label>
                <div class="input">
                    {foreach $students as $student}
                        <input type="checkbox" name="group_mail[student][{$student->id}]" class="student_checkbox" value="{$student->id}"{if !$smarty.post or $smarty.post.group_mail.student[$student->id]} checked="checked"{/if} id="group_mail_student_{$student->id}_id" /> <label for="group_mail_student_{$student->id}_id" style="cursor: pointer;">{$student->fullname} ({$student->email})</label><br />
                    {/foreach}
                    <a href="javascript:void(0);" class="button special select_all_students">{translate line='admin_groups_group_email_button_select_all_students'}</a>
                </div>
                {form_error field='group_mail[student][]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_groups_group_email_form_submit_button'}" class="button" />
            </div>
        </form>
        {else}
            {include file='partials/backend_general/error_box.tpl' message='lang:admin_groups_group_email_error_group_is_empty' inline}
        {/if}
    {/if}
{/block}
