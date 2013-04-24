{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_courses_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_courses_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $course->exists() or $smarty.post.course}
        <fieldset>
            <form action="{internal_url url='admin_courses/update'}" method="post">
                <div class="field">
                    <label for="course_name_id">{translate line='admin_courses_form_label_course_name'}:</label>
                    <p class="input"><input type="text" name="course[name]" value="{$smarty.post.course.name|default:$course->name|escape:'html'}" id="course_name_id" /></p>
                    {form_error field='course[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="course_period_id_id">{translate line='admin_courses_form_label_course_period'}:</label>
                    <p class="input"><select name="course[period_id]" size="1">
                        {list_html_options options=$periods selected=$smarty.post.course.period_id|default:$course->period_id}
                    </select></p>
                    {form_error field='course[period_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="course_description_id">{translate line='admin_courses_form_label_course_description'}:</label>
                    <p class="input"><textarea name="course[description]" id="course_description_id" class="tinymce">{$smarty.post.course.description|default:$course->description|escape:'html'}</textarea></p>
                    {form_error field='course[description]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    {include file='partials/backend_general/overlay_editor.tpl' table='courses' table_id=$course->id column='description' editor_type='textarea' class='tinymce' inline}
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" class="button" value="{translate line='admin_courses_form_button_submit'}" />
                </div>
                <input type="hidden" name="course_id" value="{$smarty.post.course_id|default:$course->id|intval}" />
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_error_course_not_found' inline}
    {/if}
{/block}