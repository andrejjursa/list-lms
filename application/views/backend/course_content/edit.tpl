{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_course_content_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $content->exists() or $smarty.post.course_content}
        <form action="{internal_url url="admin_course_content/update/{$content->id}"}" method="post">
            <div class="field">
                <label for="course_content_course_id_id" class="required">{translate line='admin_course_content_form_label_course_id'}:</label>
                <p class="input"><select name="course_content[course_id]" size="1" id="course_content_course_id_id">{list_html_options options=$courses selected=$smarty.post.course_content.course_id|default:$content->course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
                {form_error field='course_content[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="course_content_title_id" class="required">{translate line='admin_course_content_form_label_title'}:</label>
                <p class="input"><input type="text" name="course_content[title]" id="course_content_title_id" value="{$smarty.post.course_content.title|default:$content->title|htmlspecialchars}" /></p>
                {form_error field='course_content[title]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="course_content_content_id">{translate line='admin_course_content_form_label_content'}:</label>
                <p class="input"><textarea name="course_content[content]" id="course_content_content_id">{$smarty.post.course_content.content|default:$content->content|htmlspecialchars}</textarea></p>
                {form_error field='course_content[content]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_course_content_form_button_submit'}" class="button" />
            </div>
        </form>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_course_content_error_course_content_not_found' inline}
    {/if}
{/block}
{block custom_head}
    <script type="text/javascript">
    </script>
{/block}