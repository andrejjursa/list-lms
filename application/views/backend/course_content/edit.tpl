{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_course_content_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
    {if $content->exists() or $smarty.post.course_content}
        <form action="{internal_url url="admin_course_content/update"}" method="post" id="edit_form">
            {include file='partials/backend_general/flash_messages.tpl' inline}
            <div class="columns">
                <div class="col_50p">
                    <div class="field">
                        <label>{translate line='admin_course_content_form_label_created_by'}:</label>
                        <div class="input">{if $content->creator_id}{$content->creator_fullname}{else}-{/if} ({$content->created|date_format:{translate line='common_datetime_format'}})</div>
                    </div>
                </div>
                <div class="col_50p">
                    <div class="field">
                        <label>{translate line='admin_course_content_form_label_updated_by'}:</label>
                        <div class="input">{if $content->updator_id}{$content->updator_fullname}{else}-{/if} ({$content->updated|date_format:{translate line='common_datetime_format'}})</div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="col_50p">
                    <div class="field">
                        <label for="course_content_course_id_id" class="required">{translate line='admin_course_content_form_label_course_id'}:</label>
                        <p class="input"><select name="course_content[course_id]" size="1" id="course_content_course_id_id">{list_html_options options=$courses selected=$smarty.post.course_content.course_id|default:$content->course_id|default:$list_teacher_account.prefered_course_id|intval}</select></p>
                        {form_error field='course_content[course_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field course_content_group_field" style="display: none;">
                        <label for="course_content_course_content_group_id_id">{translate line='admin_course_content_form_label_course_content_group_id'}:</label>
                        <p class="input"><select name="course_content[course_content_group_id]" size="1" id="course_content_course_content_group_id_id">{list_html_options options=$course_content_groups selected=$smarty.post.course_content.course_content_group_id|default:$content->course_content_group_id|intval}</select></p>
                        {form_error field='course_content[course_content_group_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="course_content_title_id" class="required">{translate line='admin_course_content_form_label_title'}:</label>
                        <p class="input"><input type="text" name="course_content[title]" id="course_content_title_id" value="{$smarty.post.course_content.title|default:$content->title|htmlspecialchars}" /></p>
                        {form_error field='course_content[title]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                        {include file='partials/backend_general/overlay_editor.tpl' table='course_content' table_id=$content->id column='title' editor_type='input' inline}
                    </div>
                    <div class="field">
                        <label for="course_content_content_id">{translate line='admin_course_content_form_label_content'}:</label>
                        <p class="input"><textarea name="course_content[content]" id="course_content_content_id" class="tinymce">{$smarty.post.course_content.content|default:$content->content|htmlspecialchars}</textarea></p>
                        {form_error field='course_content[content]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                        {include file='partials/backend_general/overlay_editor.tpl' table='course_content' table_id=$content->id column='content' editor_type='textarea' class='tinymce' inline}
                    </div>
                    <div class="field">
                        <label for="course_content_published_from_id">{translate line='admin_course_content_form_label_published_from'}:</label>
                        <p class="input"><input type="text" name="course_content[published_from]" id="course_content_published_from_id" value="{$smarty.post.course_content.published_from|default:$content->published_from|htmlspecialchars}" /></p>
                        {form_error field='course_content[published_from]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="course_content_published_to_id">{translate line='admin_course_content_form_label_published_to'}:</label>
                        <p class="input"><input type="text" name="course_content[published_to]" id="course_content_published_to_id" value="{$smarty.post.course_content.published_to|default:$content->published_to|htmlspecialchars}" /></p>
                        {form_error field='course_content[published_to]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="course_content_published_id">{translate line='admin_course_content_form_label_published'}:</label>
                        <p class="input"><input type="hidden" name="course_content[published]" value="0" /><input type="checkbox" name="course_content[published]" value="1" id="course_content_published_id"{if $smarty.post.course_content.published|default:$content->published eq 1} checked="checked"{/if} /></p>
                        {form_error field='course_content[published]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="course_content_public_id">{translate line='admin_course_content_form_label_public'}:</label>
                        <p class="input"><input type="hidden" name="course_content[public]" value="0" /><input type="checkbox" name="course_content[public]" value="1" id="course_content_public_id"{if $smarty.post.course_content.public|default:$content->public eq 1} checked="checked"{/if} /></p>
                        {form_error field='course_content[public]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                </div>
                <div class="col_50p">
                    <div class="field">
                        <label>{translate line='admin_course_content_form_label_files_default'}:</label>
                        <div class="input"><div id="plupload_content_files_default_id" class="uploader_switch"></div></div>
                        <div class="input">
                            <table class="course_content_files_table">
                                <thead>
                                <tr>
                                    <th>{translate line='admin_course_content_table_header_file'}</th>
                                    <th class="controlls" colspan="3">{translate line='admin_course_content_table_header_controlls'}</th>
                                </tr>
                                </thead>
                                <tbody class="file_list_default"></tbody>
                            </table>
                        </div>
                        <div class="input"><em>{translate line='admin_course_content_form_hint_edit_files'}</em></div>
                    </div>
                    {foreach $languages as $language => $language_title}
                        <div class="field">
                            <label>{translate|sprintf:$language_title line='admin_course_content_form_label_files_language'}:</label>
                            <div class="input"><div id="plupload_content_files_{$language}_id" class="uploader_switch"></div></div>
                            <div class="input">
                                <table class="course_content_files_table">
                                    <thead>
                                    <tr>
                                        <th>{translate line='admin_course_content_table_header_file'}</th>
                                        <th class="controlls" colspan="3">{translate line='admin_course_content_table_header_controlls'}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="file_list_{$language}"></tbody>
                                </table>
                            </div>
                            <div class="input"><em>{translate line='admin_course_content_form_hint_edit_files'}</em></div>
                        </div>
                    {/foreach}
                </div>
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_course_content_form_button_submit'}" class="button" />
                <a href="{internal_url url='admin_course_content'}" class="button special">{translate line='common_button_back'}</a>
                <input type="hidden" name="post_selected_course_content_group_id" value="{$smarty.post.course_content.course_content_group_id|default:$content->course_content_group_id|intval}" />
                <input type="hidden" name="course_content[folder_name]" value="{$content->id}" />
                <input type="hidden" name="course_content[files_visibility]" id="files_visibility" value="{$smarty.post.course_content.files_visibility|default:$content->files_visibility|default:'{}'|escape:'html'}" />
                <input type="hidden" name="course_content_id" value="{$content->id}" />
            </div>
        </form>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_course_content_error_course_content_not_found' inline}
    {/if}
    </fieldset>
{/block}
{block custom_head}
    <script type="text/javascript">
        var data = {
            'all_course_content_groups': {$all_course_content_groups|json_encode}
        };
        var highlighters = {$highlighters|json_encode};
        var message_write_disabled = '{translate line='admin_course_content_error_cant_save_form'}';
        var languages = {$languages|json_encode};
        var delete_file_question = '{translate line='admin_course_content_delete_file_question'}';
        var show_uploader_text = '{translate line='admin_course_content_text_show_uploader'}';
        var coppied_to_clipboard = '{translate line='admin_course_content_text_coppied_to_clipboard'}';
    </script>
{/block}