{extends file='layouts/frontend.tpl'}
{block title}{translate line='projects_task_page_header'}{/block}
{block main_content}
    <h1>{translate line='projects_task_page_header'}</h1>
    {if $course->exists()}
        {if $project->exists()}
            {if $task->exists()}
                <h2>{overlay table='task_sets' table_id=$project->id column='name' default=$project->name} / {overlay table='tasks' table_id=$task->id column='name' default=$task->name}</h2>
                {include file='partials/frontend_general/flash_messages.tpl' inline}
                <div id="tabs">
                    <ul style="height: 37px;">
                        <li><a href="#tabs-task">{translate line='projects_task_tabs_task'}</a></li>
                        {if $project_selection->exists()}<li><a href="#tabs-solution">{translate line='projects_task_tabs_solutions'}</a></li>{/if}
                    </ul>
                    <div id="tabs-task">
                        <div class="task_text text_content">
                            {overlay|add_base_url table='tasks' table_id=$task->id column='text' default=$task->text}
                        </div>
                        {$files = $task->get_task_files()}
                        {if count($files) > 0}
                        <div class="task_files">
                            <div class="task_files_title">{translate line='projects_task_task_files_title'}:</div>
                            {foreach $files as $file}
                            <div class="task_file">
                                <a href="{internal_url url="tasks/download_file/{$task->id}/{$file.file|encode_for_url}"}">{$file.file}</a> ({$file.size})
                            </div>
                            {/foreach}
                        </div>
                        {/if}
                        <div class="task_author">{translate|sprintf:{$task->author->get()->fullname|default:{translate line='projects_task_author_unknown'}} line='projects_task_author'}</div>
                        {if $project_selection->exists()}{if $project->upload_end_time gt 'Y-m-d H:i:s'|date}
                        <div class="upload_solution" id="upload_solution_id">
                            <fieldset class="basefieldset">
                                <legend>{translate line='projects_task_fieldset_legend_upload_solution'}</legend>

                                <form action="{internal_url url="projects/upload_solution/{$project->id|intval}{overlay|text_convert_for_url table='task_sets' column='name' table_id=$project->id default=$project->name}/{$task->id|intval}{overlay|text_convert_for_url table='tasks' column='name' table_id=$task->id default=$task->name}"}" method="post" enctype="multipart/form-data">
                                    <div class="field">
                                        <label for="file_id">{translate line='projects_task_form_label_file'}:</label>
                                        <p class="input"><input type="file" name="file" id="file_id" /></p>
                                        <p class="input"><em>{translate|sprintf:$max_filesize line='projects_task_form_label_file_hint' nocache}</em></p>
                                        {nocache}
                                        {if $file_error_message}
                                        <p class="error"><span class="message">{translate_text text=$file_error_message}</span></p>
                                        {/if}
                                        {/nocache}
                                    </div>
                                    {if !is_null($project->upload_end_time)}
                                    <div class="field">
                                        <label>{translate line='projects_task_form_label_remaining'}:</label>
                                        <p class="input" id="remaining_time"></p>
                                        <span id="remaining_counter" style="display: none;"></span>
                                    </div>
                                    {/if}
                                    <div class="buttons">
                                        <input type="submit" name="submit_button" value="{translate line='projects_task_form_submit'}" class="button" />
                                    </div>
                                </form>
                            </fieldset>
                        </div>{/if}
                        {else}
                        <div class="select_project">
                            <fieldset class="basefieldset">
                                <legend>{translate line='projects_task_fieldset_legend_select_project'}</legend>
                                <form action="" method="post">
                                    <div class="field">
                                        <label>{translate line='projects_task_students_working_on_task'}:</label>
                                        <p class="input students_working_on_task">
                                            {foreach $students as $student}
                                                <span{if $student@last} class="last_student"{/if}>{$student->fullname}</span>
                                            {foreachelse}
                                                {translate line='projects_task_no_student_working_on_this_task'}
                                            {/foreach}
                                        </p>
                                    </div>
                                    {if 'Y-m-d H:i:s'|date lte $project->project_selection_deadline and $solution_files|count eq 0}
                                    <div class="buttons">
                                        <a href="{internal_url url="projects/select_project/{$project->id}{overlay|text_convert_for_url table='task_sets' table_id=$project->id column='name' default=$project->name}/{$task->id}{overlay|text_convert_for_url table='tasks' table_id=$task->id column='name' default=$task->name}"}" class="button">{translate line='projects_task_select_project'}</a>
                                    </div>
                                    {/if}
                                </form>
                            </fieldset>
                        </div>
                        {/if}
                    </div>
                    {if $project_selection->exists()}
                    <div id="tabs-solution">
                        <table class="solutions_table">
                            <thead>
                                <tr>
                                    <th class="version">{translate line='projects_task_solution_table_header_version'}</th>
                                    <th class="file">{translate line='projects_task_solution_table_header_file'}</th>
                                    <th class="size">{translate line='projects_task_solution_table_header_size'}</th>
                                    <th class="modified">{translate line='projects_task_solution_table_header_modified'}</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach $solution_files as $file}
                                <tr>
                                    <td class="version">{$file@key}</td>
                                    {if isset($versions_metadata[$file@key]) && $versions_metadata[$file@key]->download_lock}
                                    <td class="file"><span class="download_lock">{$file.file_name}_{$file@key}.zip</span></td>
                                    {else}
                                    <td class="file"><a href="{internal_url url="tasks/download_solution/{$project->id|intval}/{$file.file|encode_for_url}"}" target="_blank">{$file.file_name}_{$file@key}.zip</a></td>
                                    {/if}
                                    <td class="size">{$file.size}</td>
                                    <td class="modified">{$file.last_modified|date_format:{translate line='common_datetime_format'}}</td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="4">{include file='partials/frontend_general/error_box.tpl' message='lang:projects_task_no_solutions_yet' inline}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    {/if}
                </div>
            {else}
                {include file='partials/frontend_general/flash_messages.tpl' inline}
                {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_task_found' inline}
            {/if}
        {else}
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_project_found' inline}
        {/if}
    {else}
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_active_course' inline}
    {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_id = {$task->id|intval};
    var project_id = {$project->id|intval};
    var student_id = {$list_student_account_model->id|intval};
    var messages = {
        countdown_time: '{translate|addslashes line='projects_countdown_message_time_info'}',
        countdown_expired: '{translate|addslashes line='projects_countdown_message_expired'}'
    };
    {if $project_selection->exists() and !is_null($project->upload_end_time)}
    var enable_countdown = true;
    var countdown_to = new Date({$project->upload_end_time|date_format:Y}, {$project->upload_end_time|date_format:m} - 1, {$project->upload_end_time|date_format:d}, {$project->upload_end_time|date_format:H}, {$project->upload_end_time|date_format:i}, {$project->upload_end_time|date_format:s});
    {else}
    var enable_countdown = false;
    {/if}
</script>
{/block}