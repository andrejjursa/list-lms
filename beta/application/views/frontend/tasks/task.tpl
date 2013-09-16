{extends file='layouts/frontend.tpl'}
{block title}{translate text='tasks_task_page_header'}{/block}
{block main_content}
    <h1>{translate line='tasks_task_page_header'}</h1>
    {if $course->exists()}
        {if $task_set->exists()}
            <h2 class="task_name">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</h2>
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            <div id="tabs">
                <ul style="height: 37px;">
                    <li><a href="#tabs-task">{translate line='tasks_task_tabs_task'}</a></li>
                    <li><a href="#tabs-solution">{translate line='tasks_task_tabs_solutions'}</a></li>
                    {if $task_set->comments_enabled}<li class="comments_tab"><a href="{internal_url url="tasks/show_comments/{$task_set->id}"}">{translate line='tasks_task_tabs_comments'}</a></li>{/if}
                </ul>
                <div id="tabs-task">
                    {$instructions_text = {overlay table='task_sets' table_id=$task_set->id|intval column='instructions' default=$task_set->instructions}}
                    {if $instructions_text}
                    <h3>{translate line='tasks_instructions_header'}</h3>
                    <div class="instructions_text">
                        {$instructions_text|add_base_url}
                    </div>
                    {/if}
                    {$tasks = $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get()}
                    {$this->lang->init_overlays('tasks', $tasks->all, ['name', 'text'])}
                    {foreach $tasks->all as $task}
                    <h3>{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}{if $task->join_bonus_task} <span class="bonus_task">({translate line='tasks_task_is_bonus_task'})</span>{/if}</h3>
                    <div class="task_text">
                    {overlay|add_base_url table='tasks' table_id=$task->id column='text' default=$task->text}
                    </div>{$files = $task->get_task_files()}
                    {if count($files) > 0}
                    <div class="task_files">
                        <div class="task_files_title">{translate line='tasks_task_task_files_title'}:</div>
                        {foreach $files as $file}
                        <div class="task_file">
                            <a href="{internal_url url="tasks/download_file/{$task->id}/{$file.file|encode_for_url}"}">{$file.file}</a> ({$file.size})
                        </div>
                        {/foreach}
                    </div>
                    {/if}
                    <div class="task_points">{translate|sprintf:{$task->join_points_total|floatval} line='tasks_task_points_for_task'}</div>
                    <div class="task_author">{translate|sprintf:{$task->author->get()->fullname|default:{translate line='tasks_task_author_unknown'}} line='tasks_task_author'}</div>
                    {/foreach}
                    {if $task_set_can_upload}
                    <div class="upload_solution">
                        <fieldset class="basefieldset">
                            <legend>{translate line='tasks_task_fieldset_legend_upload_solution'}</legend>
                            <form action="{internal_url url="tasks/upload_solution/{$task_set->id|intval}"}" method="post" enctype="multipart/form-data">
                                <div class="field">
                                    <label for="file_id">{translate line='tasks_task_form_label_file'}:</label>
                                    <p class="input"><input type="file" name="file" id="file_id" /></p>
                                    <p class="input"><em>{translate|sprintf:$max_filesize line='tasks_task_form_label_file_hint'}</em></p>
                                    {if trim($task_set->allowed_file_types) ne ''}
                                    {$exploded_allow_file_types = ','|explode:$task_set->allowed_file_types}
                                    {$exploded_allow_file_types = 'trim'|array_map:$exploded_allow_file_types}   
                                    {capture name='allowed_file_types' assign='allowed_file_types'}{', '|implode:$exploded_allow_file_types}{/capture}    
                                    <p class="input"><em>{translate|sprintf:$allowed_file_types line='tasks_task_form_label_file_hint2'}</em></p>
                                    {/if}
                                    {if $file_error_message}
                                    <p class="error"><span class="message">{translate_text text=$file_error_message}</span></p>
                                    {/if}
                                </div>
                                <div class="buttons">
                                    <input type="submit" name="submit_button" value="{translate line='tasks_task_form_submit'}" class="button" />
                                </div>
                            </form>
                        </fieldset>
                    </div>
                    {/if}
                </div>
                <div id="tabs-solution">
                    <table class="solutions_table">
                        <thead>
                            <tr>
                                <th class="version">{translate line='tasks_task_solution_table_header_version'}</th>
                                <th class="file">{translate line='tasks_task_solution_table_header_file'}</th>
                                <th class="size">{translate line='tasks_task_solution_table_header_size'}</th>
                                <th class="modified">{translate line='tasks_task_solution_table_header_modified'}</th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $solution_files as $file}
                            <tr>
                                <td class="version">{$file@key}</td>
                                <td class="file"><a href="{internal_url url="tasks/download_solution/{$task_set->id|intval}/{$file.file|encode_for_url}"}" target="_blank">{$file.file_name}_{$file@key}.zip</a></td>
                                <td class="size">{$file.size}</td>
                                <td class="modified">{$file.last_modified|date_format:{translate line='tasks_date_format'}}</td>
                            </tr>
                        {foreachelse}
                            <tr>
                                <td colspan="4">{include file='partials/frontend_general/error_box.tpl' message='lang:tasks_task_no_solutions_yet' inline}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {else}
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_task_task_set_not_found' inline}
        {/if}
    {else}
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_no_active_course' inline}
    {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_id = {$task_set->id|intval};
</script>
{/block}