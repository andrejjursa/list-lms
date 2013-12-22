{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_courses_mail_to_course_page_title'}{/block}
{block main_content}
    <h3>{translate line='admin_courses_mail_to_course_page_title'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $course->exists()}
        <form action="{internal_url url="admin_courses/send_mail_to_course/{$course->id}"}" method="post">
            <fieldset class="basefieldset">
                <legend>{translate_text text=$course->name} / {translate_text text=$course->period_name}</legend>
                <div class="field">
                    <label class="required">{translate line='admin_courses_mail_to_course_form_label_subject'}:</label>
                    <p class="input"><input type="text" name="course_mail[subject]" value="{$smarty.post.course_mail.subject|escape:'html'}" /></p>
                    {form_error field='course_mail[subject]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="course_mail_body_id" class="required">{translate line='admin_courses_mail_to_course_form_label_body'}:</label>
                    <div class="input"><textarea name="course_mail[body]" id="course_mail_body_id" class="tinymce">{$smarty.post.course_mail.body}</textarea></div>
                    {form_error field='course_mail[body]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="course_mail_from_id" class="required">{translate line='admin_courses_mail_to_course_form_label_from'}:</label>
                    <div class="input">
                        <select name="course_mail[from]" size="1" id="course_mail_from_id">
                            {list_html_options
                                options=[''=>'','system'=>'lang:admin_courses_mail_to_course_form_label_from_system','me'=>'lang:admin_courses_mail_to_course_form_label_from_me']
                                selected=$smarty.post.course_mail.from}
                        </select>
                    </div>
                    {form_error field='course_mail[from]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_courses_mail_to_course_form_submit_button'}" class="button" />
                </div>
                <div class="field">
                    {form_error field='course_mail[student][]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
            </fieldset>
            {foreach $groups_students as $group_id => $group}
            {if $group.students}
            <fieldset class="basefieldset">
                <legend>{translate_text text=$group.name}</legend>
                <p>
                    <a href="javascript:void(0);" class="switch_students_on target:mail_students_group_{$group_id} button special">{translate line='admin_courses_mail_to_course_button_switch_students_on'}</a>
                    <a href="javascript:void(0);" class="switch_students_off target:mail_students_group_{$group_id} button special">{translate line='admin_courses_mail_to_course_button_switch_students_off'}</a>
                </p>
                <ul class="mail_students_list">
                    {foreach $group.students as $student_id => $student}
                    <li><label><input type="checkbox" name="course_mail[student][{$student_id}]" value="{$student_id}"{if !$smarty.post or $smarty.post.course_mail.student[$student_id]} checked="checked"{/if} class="mail_students mail_students_group_{$group_id}" /> {$student.fullname} ({$student.email})</label></li>
                    {/foreach}
                </ul>
                <p>
                    <a href="javascript:void(0);" class="switch_students_on target:mail_students_group_{$group_id} button special">{translate line='admin_courses_mail_to_course_button_switch_students_on'}</a>
                    <a href="javascript:void(0);" class="switch_students_off target:mail_students_group_{$group_id} button special">{translate line='admin_courses_mail_to_course_button_switch_students_off'}</a>
                </p>
            </fieldset>
            {/if}
            {/foreach}
        </form>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_mail_to_course_error_course_not_found' inline}
    {/if}
{/block}