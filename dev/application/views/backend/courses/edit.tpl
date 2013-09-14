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
                    <p class="input"><textarea name="course[description]" id="course_description_id" class="tinymce">{$smarty.post.course.description|default:$course->description|add_base_url}</textarea></p>
                    {form_error field='course[description]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$smarty.post.course_id|default:$course->id column='description' editor_type='textarea' class='tinymce' inline}
                </div>
                <div class="field">
                    <label for="course_capacity_id" class="required">{translate line='admin_courses_form_label_course_capacity'}:</label>
                    <p class="input"><input type="text" name="course[capacity]" id="course_capacity_id" value="{$smarty.post.course.capacity|default:$course->capacity|escape:'html'}" /></p>
                    {form_error field='course[capacity]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
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
                <div class="field">
                    <label for="course_allow_subscription_to_id">{translate line='admin_courses_form_label_allow_subscription_to'}:</label>
                    <p class="input"><input type="text" name="course[allow_subscription_to]" value="{$smarty.post.course.allow_subscription_to|default:$course->allow_subscription_to|escape:'html'}" id="course_allow_subscription_to_id" /></p>
                    <p class="input"><em>{translate line='admin_courses_form_label_allow_subscription_to_hint'}</em></p>
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