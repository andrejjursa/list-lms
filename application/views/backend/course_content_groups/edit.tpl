{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_course_content_groups_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_groups_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $content_group->exists() or $smarty.post.content_group}
        <fieldset>
            <form action="{internal_url url='admin_course_content_groups/update'}" method="post">
                <div class="field">
                    <label for="content_group_course_id_id" class="required">{translate line='admin_course_content_groups_form_label_course_id'}:</label>
                    <p class="input"><select name="content_group[course_id]" size="1" id="content_group_course_id_id">{list_html_options options=$courses selected=$smarty.post.content_group.course_id|default:$content_group->course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
                    {form_error field='content_group[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="content_group_title_id" class="required">{translate line='admin_course_content_groups_form_label_title'}:</label>
                    <p class="input"><input type="text" name="content_group[title]" id="content_group_title_id" value="{$smarty.post.content_group.title|default:$content_group->title|htmlspecialchars}" /></p>
                    {form_error field='content_group[title]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    {include file='partials/backend_general/overlay_editor.tpl' table='course_content_groups' table_id=$content_group->id column='title' editor_type='input' inline}
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_course_content_groups_form_button_submit'}" class="button" />
                    <a href="{internal_url url='admin_course_content_groups'}" class="button special">{translate line='common_button_back'}</a>
                </div>
                <input type="hidden" name="content_group_id" value="{$smarty.post.content_group_id|default:$content_group->id|intval}" />
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_course_content_groups_error_not_found_for_edit' inline}
    {/if}
{/block}