{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tasks_page_title'}</h2>
    {if $task->task_set_count gt 0}{include file='partials/backend_general/error_box.tpl' message={translate|sprintf:$task->task_set_count line='admin_tasks_edit_warning_task_assigned_to_n_task_sets'}}{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {include file='backend/tasks/category_checkboxes.tpl' inline}
    {if $smarty.post.task or $task->exists()}
    {$task_sets = $task->task_set->order_by_with_overlay('name', 'asc')->include_related('course', 'name')->include_related('course/period', 'name')->include_related('group', 'name')->get()}
    <form action="{internal_url url='admin_tasks/update'}" method="post">
        <div id="tabs">
            <ul>
                <li><a href="#tabs-basic">{translate line='admin_tasks_edit_tabs_basic'}</a></li>
                <li><a href="#tabs-categories">{translate line='admin_tasks_edit_tabs_categories'}</a></li>
                <li><a href="#tabs-files">{translate line='admin_tasks_edit_tabs_files'}</a></li>
                <li><a href="#tabs-tests">{translate line='admin_tasks_edit_tabs_tests'}</a></li>
                <li><a href="#tabs-internal_comment">{translate line='admin_tasks_edit_tabs_internal_comment'}</a></li>
                {if $task_sets->exists()}<li><a href="#tabs-usages">{translate line='admin_tasks_edit_tabs_usages'}</a></li>{/if}
            </ul>
            <div id="tabs-basic">
                <div class="field">
                    <label for="task_name_id" class="required">{translate line='admin_tasks_form_label_name'}:</label>
                    <p class="input"><input type="text" name="task[name]" value="{$smarty.post.task.name|default:$task->name|escape:'html'}" id="task_name_id" /></p>
                    {form_error field='task[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    {include file='partials/backend_general/overlay_editor.tpl' table='tasks' table_id=$smarty.post.task_id|default:$task->id column='name' editor_type='input' inline}
                </div>
                <div class="field">
                    <label for="task_text_id" class="required">{translate line='admin_tasks_form_label_text'}:</label>
                    <p class="input"><textarea name="task[text]" id="task_text_id" class="tinymce">{$smarty.post.task.text|default:$task->text|add_base_url|htmlspecialchars}</textarea></p>
                    <p class="input"><em>{translate line='common_mathjax_hint'}</em></p>
                    {form_error field='task[text]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    {include file='partials/backend_general/overlay_editor.tpl' table='tasks' table_id=$smarty.post.task_id|default:$task->id column='text' editor_type='textarea' class='tinymce' inline}
                </div>
                <div class="field">
                    <label for="task_author_id_id">{translate line='admin_tasks_form_label_author'}:</label>
                    <div class="input">
                        <select name="task[author_id]" size="1" id="task_author_id_id">
                            {list_html_options options=$teachers selected=$smarty.post.task.author_id|default:$task->author_id}
                        </select>
                    </div>
                </div>
            </div>
            <div id="tabs-categories">
                <div class="field">
                    <label class="required">{translate line='admin_tasks_form_label_categories'}:</label>
                    <div class="input categories_structure">{category_checkboxes chbname='task[categories][]' structure=$structure selected=$smarty.post.task.categories|default:$task->category->get()->all_to_single_array('id')|default:[]}</div>
                    {form_error field='task[categories][]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
            </div>
            <div id="tabs-files">
                <div class="field">
                    <label for="">{translate line='admin_tasks_form_label_file_upload'}:</label>
                    <div class="input"><div id="plupload_queue_id">{translate line='admin_tasks_error_message_no_plupload_support'}</div></div>
                    <div class="input">
                        <table class="task_files_table">
                            <thead>
                                <tr>
                                    <th>{translate line='admin_tasks_edit_files_table_header_file'}</th>
                                    <th>{translate line='admin_tasks_edit_files_table_header_size'}</th>
                                    <th class="controlls">{translate line='admin_tasks_edit_files_table_header_controlls'}</th>
                                </tr>
                            </thead>
                            <tbody id="files_table_content_id"></tbody>
                        </table>
                    </div>
                </div>
                <div class="field">
                    <label for="">{translate line='admin_tasks_form_label_hidden_file_upload'}:</label>
                    <div class="input"><div id="plupload_queue_hidden_id">{translate line='admin_tasks_error_message_no_plupload_support'}</div></div>
                    <div class="input">
                        <table class="task_files_table">
                            <thead>
                                <tr>
                                    <th>{translate line='admin_tasks_edit_files_table_header_file'}</th>
                                    <th>{translate line='admin_tasks_edit_files_table_header_size'}</th>
                                    <th class="controlls">{translate line='admin_tasks_edit_files_table_header_controlls'}</th>
                                </tr>
                            </thead>
                            <tbody id="hidden_files_table_content_id"></tbody>
                        </table>
                    </div>
                </div>                
            </div>
            <div id="tabs-tests">
                <fieldset class="basefieldset">
                    <legend>{translate line='admin_tasks_edit_tests_fieldset_legend_new_test'}</legend>
                    <a href="{internal_url url="admin_tests/new_test_form/{$task->id}"}" class="button new_test_button">{translate line='admin_tasks_edit_button_create_new_test'}</a>
                </fieldset>
                <fieldset class="basefieldset">
                    <legend>{translate line='admin_tasks_edit_tests_fieldset_legend_all_tests'}</legend>
                    <div id="tests_content_id"></div>
                </fieldset>
            </div>
            <div id="tabs-internal_comment">
                <div class="field">
                    <label for="task_internal_comment_id">{translate line='admin_tasks_form_label_internal_comment'}:</label>
                    <div class="input"><textarea name="task[internal_comment]" id="task_internal_comment_id">{$smarty.post.task.internal_comment|default:$task->internal_comment|escape:'html'}</textarea></div>
                </div>
            </div>
            {if $task_sets->exists()}<div id="tabs-usages">{$this->lang->init_overlays('task_sets', $task_sets->all, ['name'])}
                <ul>
                    {foreach $task_sets->all as $task_set}
                    <li>
                        <strong><a href="{internal_url url="admin_task_sets/edit/task_set_id/{$task_set->id}"}">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</a> ({$task_set->created|date_format:{translate line='common_datetime_format'}})</strong><br />
                        {translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}{if $task_set->group_name} ({translate_text text=$task_set->group_name}){/if}
                    </li>
                    {/foreach}
                </ul>
            </div>{/if}
        </div>
        <fieldset class="basefieldset">
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_tasks_form_button_save'}" class="button" /> <a href="{internal_url url='admin_tasks'}" class="button special">{translate line='common_button_back'}</a>
                <a href="{internal_url url="admin_tasks/add_to_task_set/task_id/{$task->id}"}" class="button special add_to_task_set" title="{translate line='admin_tasks_form_button_add_to_task_set'}">{translate line='admin_tasks_form_button_add_to_task_set_button'}</a>
                <input type="hidden" name="task_id" value="{$smarty.post.task_id|default:$task->id|intval}" />
            </div>
        </fieldset>
    </form>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_tasks_error_message_task_not_found' inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var select_files_text = '{translate|addslashes line="admin_tasks_javascript_text_select_files"}';
    var messages = {
        delete_question: '{translate|addslashes line="admin_tasks_javascript_message_file_delete_question"}',
        after_delete: '{translate|addslashes line="admin_tasks_javascript_message_file_after_delete"}'
    };
    var highlighters = {$highlighters|json_encode};    
    var all_tests_list_url = '{internal_url url="admin_tests/all_tests/{$task->id}"}';
    var test_delete_question = '{translate|addslashes line="admin_tests_js_message_delete_question"}';
</script>{/block}