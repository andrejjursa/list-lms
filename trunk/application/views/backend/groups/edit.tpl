{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_groups_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_groups_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $group->exists() or $smarty.post.group}
    <fieldset>
        <form action="{internal_url url='admin_groups/update'}" method="post">
            <div class="field">
                <label for="group_name_id" class="required">{translate line='admin_groups_form_label_group_name'}:</label>
                <p class="input"><input type="text" name="group[name]" value="{$smarty.post.group.name|default:$group->name|escape:'html'}" id="group_name_id" /></p>
                {form_error field='group[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="group_course_id_id" class="required">{translate line='admin_groups_form_label_group_course'}:</label>
                <p class="input"><select name="group[course_id]" size="1" id="group_course_id_id">
                    {list_html_options options=$courses selected=$smarty.post.group.course_id|default:$group->course_id}
                </select></p>
                {form_error field='group[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_groups_form_button_save'}" class="button" /> <a href="{internal_url url='admin_groups'}" class="button special">{translate line='common_button_back'}</a>
            </div>
            <input type="hidden" name="group_id" value="{$smarty.post.group_id|default:$group->id|intval}" />
        </form>
    </fieldset>
    {else}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_groups_error_no_such_group_message' inline}
    {/if}
{/block}