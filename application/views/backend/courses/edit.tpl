{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_courses_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_courses_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $course->exists() or $smarty.post.course}
        <fieldset>
            <form action="{internal_url url='admin_courses/update'}" method="post">
                <div class="field">
                    <label for="course_name_id" class="required">{translate line='admin_courses_form_label_course_name'}:</label>
                    <p class="input"><input type="text" name="course[name]" value="{$smarty.post.course.name|default:$course->name|escape:'html'}" id="course_name_id" /></p>
                    {form_error field='course[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="course_period_id_id" class="required">{translate line='admin_courses_form_label_course_period'}:</label>
                    <p class="input"><select name="course[period_id]" size="1">
                        {list_html_options options=$periods selected=$smarty.post.course.period_id|default:$course->period_id}
                    </select></p>
                    {form_error field='course[period_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="course_description_id">{translate line='admin_courses_form_label_course_description'}:</label>
                    <p class="input"><textarea name="course[description]" id="course_description_id" class="tinymce">{$smarty.post.course.description|default:$course->description|add_base_url|htmlspecialchars}</textarea></p>
                    {form_error field='course[description]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$smarty.post.course_id|default:$course->id column='description' editor_type='textarea' class='tinymce' inline}
                </div>
                <div class="columns">
                    <div class="col_50p">
                        <div class="field">
                            <label for="course_syllabus_id">{translate line='admin_courses_form_label_course_syllabus'}:</label>
                            <p class="input"><textarea name="course[syllabus]" id="course_syllabus_id" class="tinymce">{$smarty.post.course.syllabus|default:$course->syllabus|add_base_url|htmlspecialchars}</textarea></p>
                            {form_error field='course[syllabus]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                            {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$smarty.post.course_id|default:$course->id column='syllabus' editor_type='textarea' class='tinymce' inline}
                        </div>
                        <div class="field">
                            <label for="course_grading_id">{translate line='admin_courses_form_label_course_grading'}:</label>
                            <p class="input"><textarea name="course[grading]" id="course_grading_id" class="tinymce">{$smarty.post.course.grading|default:$course->grading|add_base_url|htmlspecialchars}</textarea></p>
                            {form_error field='course[grading]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                            {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$smarty.post.course_id|default:$course->id column='grading' editor_type='textarea' class='tinymce' inline}
                        </div>
                    </div>
                    <div class="col_50p">
                        <div class="field">
                            <label for="course_instructions_id">{translate line='admin_courses_form_label_course_instructions'}:</label>
                            <p class="input"><textarea name="course[instructions]" id="course_instructions_id" class="tinymce">{$smarty.post.course.instructions|default:$course->instructions|add_base_url|htmlspecialchars}</textarea></p>
                            {form_error field='course[instructions]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                            {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$smarty.post.course_id|default:$course->id column='instructions' editor_type='textarea' class='tinymce' inline}
                        </div>
                        <div class="field">
                            <label for="course_other_texts_id">{translate line='admin_courses_form_label_course_other_texts'}:</label>
                            <p class="input"><textarea name="course[other_texts]" id="course_other_texts_id" class="tinymce">{$smarty.post.course.other_texts|default:$course->other_texts|add_base_url|htmlspecialchars}</textarea></p>
                            {form_error field='course[other_texts]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                            {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$smarty.post.course_id|default:$course->id column='other_texts' editor_type='textarea' class='tinymce' inline}
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label for="course_capacity_id" class="required">{translate line='admin_courses_form_label_course_capacity'}:</label>
                    <p class="input"><input type="text" name="course[capacity]" id="course_capacity_id" value="{$smarty.post.course.capacity|default:$course->capacity|escape:'html'}" /></p>
                    {form_error field='course[capacity]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="columns">
                    <div class="col_50p">
                        <div class="field">
                            <label for="course_groups_change_deadline_id">{translate line='admin_courses_form_label_course_groups_change_deadline'}:</label>
                            <p class="input"><input type="text" name="course[groups_change_deadline]" value="{$smarty.post.course.groups_change_deadline|default:$course->groups_change_deadline|escape:'html'}" id="course_groups_change_deadline_id" /></p>
                            <p class="input"><em>{translate line='admin_courses_form_label_course_groups_change_deadline_hint'}</em></p>
                        </div>
                        <div class="field">
                            <label for="course_default_points_to_remove_id" class="required">{translate line='admin_courses_form_label_course_default_points_to_remove'}:</label>
                            <p class="input"><input type="text" name="course[default_points_to_remove]" value="{$smarty.post.course.default_points_to_remove|default:$course->default_points_to_remove}" id="course_default_points_to_remove_id" /></p>
                            <p class="input"><em>{translate line='admin_courses_form_label_course_default_points_to_remove_hint'}</em></p>
                            {form_error field='course[default_points_to_remove]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                        </div>
                    </div>
                    <div class="col_50p">
                        <div class="field">
                            <label for="course_allow_subscription_to_id">{translate line='admin_courses_form_label_allow_subscription_to'}:</label>
                            <p class="input"><input type="text" name="course[allow_subscription_to]" value="{$smarty.post.course.allow_subscription_to|default:$course->allow_subscription_to|escape:'html'}" id="course_allow_subscription_to_id" /></p>
                            <p class="input"><em>{translate line='admin_courses_form_label_allow_subscription_to_hint'}</em></p>
                        </div>
                        <div class="field">
                            <label for="course_test_scoring_deadline_id">{translate line='admin_courses_form_label_test_scoring_deadline'}:</label>
                            <p class="input"><input type="text" name="course[test_scoring_deadline]" value="{$smarty.post.course.test_scoring_deadline|default:$course->test_scoring_deadline|escape:'html'}" id="course_test_scoring_deadline_id" /></p>
                            <p class="input"><em>{translate line='admin_courses_form_label_test_scoring_deadline_hint'}</em></p>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="col_50p">
                        <div class="field">
                            <label for="course_hide_in_lists_id">{translate line='admin_courses_form_label_hide_in_lists'}:</label>
                            <p class="input"><input type="checkbox" name="course[hide_in_lists]" id="course_hide_in_lists_id" value="1"{if $smarty.post.course.hide_in_lists|default:$course->hide_in_lists eq 1} checked="checked"{/if}</p>
                            <p class="input"><em>{translate line='admin_courses_form_label_hide_in_lists_hint'}</em></p>
                        </div>
                    </div>
                    <div class="col_50p">
                        <div class="field">
                            <label for="course_auto_accept_students_id">{translate line='admin_courses_form_label_auto_accept_students'}:</label>
                            <p class="input"><input type="checkbox" name="course[auto_accept_students]" id="course_auto_accept_students_id" value="1"{if $smarty.post.course.auto_accelpt_students|default:$course->auto_accept_students eq 1} checked="checked"{/if} /></p>
                            <p class="input"><em>{translate line='admin_courses_form_label_auto_accept_students_hint'}</em></p>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label for="course_disable_public_groups_page_id">{translate line='admin_courses_form_label_disable_public_groups_page'}:</label>
                    <p class="input"><input type="checkbox" name="course[disable_public_groups_page]" id="course_disable_public_groups_page_id" value="1"{if $smarty.post.course.disable_public_groups_page|default:$course->disable_public_groups_page eq 1} checked="checked"{/if} /></p>
                </div>
                <div class="field">
                    <label for="new_additional_menu_link_href_id">{translate line='admin_courses_form_label_additional_menu_links'}:</label>
                    <div class="input">
                        <ul id="additional_links"></ul>
                    </div>
                    <div class="input">
                        <input type="text" value="" name="additional_link_href" placeholder="{translate line='admin_courses_form_label_additional_menu_links_placeholder_href'}" id="new_additional_menu_link_href_id" style="width: 200px;" class="additional_links" />
                        <input type="text" value="" name="additional_link_text" placeholder="{translate line='admin_courses_form_label_additional_menu_links_placeholder_text'}" id="new_additional_menu_link_name_id" style="width: 200px;" class="additional_links" />
                        <input type="text" value="" name="additional_link_id" placeholder="{translate line='admin_courses_form_label_additional_menu_links_placeholder_id'}" id="new_additional_menu_link_id_id" style="width: 200px;" class="additional_links">
                        <select name="additional_link_lang" size="1" id="new_additional_menu_link_lang_id" style="width: 200px;">
                            <option></option>
                            {foreach $languages as $lang => $langName}
                                <option value="{$lang}">{$langName}</option>
                            {/foreach}
                        </select>
                        <a href="javascript:void(0);" class="button special" id="add_additional_link">{translate line='admin_courses_form_button_add_additional_link'}</a>
                        <input type="hidden" name="course[additional_menu_links]" value="{$smarty.post.course.additional_menu_links|default:$course->additional_menu_links|default:'[]'|escape:'html'}" />
                    </div>
                    <div class="input"><em>{translate line='admin_courses_form_hint_additional_menu_links'}</em></div>
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" class="button" value="{translate line='admin_courses_form_button_submit'}" /> <a href="{internal_url url='admin_courses'}" class="button special">{translate line='common_button_back'}</a>
                </div>
                <input type="hidden" name="course_id" value="{$smarty.post.course_id|default:$course->id|intval}" />
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_error_course_not_found' inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var buttons = {
        delete_link: '{translate|addslashes line="admin_courses_form_button_delete_link"}',
        edit_link: '{translate|addslashes line="admin_courses_form_button_edit_link"}',
        update_link: '{translate|addslashes line="admin_courses_form_button_update_link"}',
        cancel_update_link: '{translate|addslashes line="admin_courses_form_button_cancel_update_link"}'
    };
    var inputs = {
        text_placeholder: '{translate|addslashes line="admin_courses_form_label_additional_menu_links_placeholder_text"}',
        href_placeholder: '{translate|addslashes line="admin_courses_form_label_additional_menu_links_placeholder_href"}',
        id_placeholder: '{translate|addslashes line="admin_courses_form_label_additional_menu_links_placeholder_id"}'
    };

    var languages = {$languages|json_encode};
</script>{/block}