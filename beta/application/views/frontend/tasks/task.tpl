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
                    <div class="instructions_text text_content">
                        {$instructions_text|add_base_url}
                    </div>
                    {/if}
                    {$tasks = $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get()}
                    {$this->lang->init_overlays('tasks', $tasks->all, ['name', 'text'])}
                    {foreach $tasks->all as $task}
                    <h3>{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}{if $task->join_bonus_task} <span class="bonus_task">({translate line='tasks_task_is_bonus_task'})</span>{/if}</h3>
                    <div class="task_text text_content">
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
                    <div class="upload_solution" id="upload_solution_id">
                        <fieldset class="basefieldset">
                            <legend>{translate line='tasks_task_fieldset_legend_upload_solution'}</legend>
                            <form action="{internal_url url="tasks/upload_solution/{$task_set->id|intval}"}" method="post" enctype="multipart/form-data">
                                <div class="field">
                                    <label for="file_id">{translate line='tasks_task_form_label_file'}:</label>
                                    <p class="input"><input type="file" name="file" id="file_id" /></p>
                                    <p class="input"><em>{translate|sprintf:$max_filesize line='tasks_task_form_label_file_hint' nocache}</em></p>
                                    {if trim($task_set->allowed_file_types) ne ''}
                                    {$exploded_allow_file_types = ','|explode:$task_set->allowed_file_types}
                                    {$exploded_allow_file_types = 'trim'|array_map:$exploded_allow_file_types}   
                                    {capture name='allowed_file_types' assign='allowed_file_types'}{', '|implode:$exploded_allow_file_types}{/capture}    
                                    <p class="input"><em>{translate|sprintf:$allowed_file_types line='tasks_task_form_label_file_hint2'}</em></p>
                                    {/if}
                                    {nocache}
                                    {if $file_error_message}
                                    <p class="error"><span class="message">{translate_text text=$file_error_message}</span></p>
                                    {/if}
                                    {/nocache}
                                </div>
                                {if !is_null($task_set->upload_end_time)}
                                <div class="field">
                                    <label>{translate line='tasks_task_form_label_remaining'}:</label>
                                    <p class="input" id="remaining_time"></p>
                                    <span id="remaining_counter" style="display: none;"></span>
                                </div>
                                {/if}
                                <div class="buttons">
                                    <input type="submit" name="submit_button" value="{translate line='tasks_task_form_submit'}" class="button" />
                                </div>
                            </form>
                        </fieldset>
                    </div>
                    {/if}
                </div>
                <div id="tabs-solution">
                    {$test_allowed_types = ','|explode:$task_set->allowed_test_types}
                    {$show_tests = false}
                    {$tests_for_json = [] scope='global'}
                    {capture name="tests_table" assign="tests_table"}
                    <fieldset class="basefieldset">
                        <legend>{translate line='tasks_test_fieldset_legend'}</legend>
                        {if $test_allowed_types|count gt 1}
                            <div class="field">
                                <label class="required" for="select_test_type_id">{translate line='tasks_test_label_select_test_type'}:</label>
                                <div class="input">
                                    <select name="select_test_type" size="1" id="select_test_type_id">
                                        <option value=""></option>
                                        {foreach $test_allowed_types as $test_type}
                                        <option value="{$test_type}">{$test_types[$test_type]}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        {else}
                            <input type="hidden" name="select_test_type" value="{$task_set->allowed_test_types}" id="select_test_type_id" />
                        {/if}
                        {$test_scoring = $task_set->enable_tests_scoring > 0 and $course->test_scoring_deadline gt date('Y-m-d H:i:s')}
                        <table class="solution_tests_table">
                            <thead>
                                <tr>
                                    <th class="test_select">&nbsp;</th>
                                    <th class="test_name">{translate line='tasks_test_table_header_test_name'}</th>
                                    <th class="test_type">{translate line='tasks_test_table_header_test_type'}</th>
                                    <th class="test_subtype">{translate line='tasks_test_table_header_test_subtype'}</th>
                                    {if $test_scoring}<th class="test_evaluation">{translate line='tasks_test_table_header_test_evaluation'}</th>{/if}
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="{if $test_scoring}5{else}4{/if}"><input type="submit" name="execute_tests" value="{translate line='tasks_test_table_button_execute_tests'}" class="button" /></td>
                                </tr>
                            </tfoot>
                            <tbody>
                            {foreach $tasks->all as $task}
                                {$tests = $task->test->where_in('type', $test_allowed_types)->where('enabled', 1)->order_by('type', 'asc')->order_by('subtype', 'asc')->get()}
                                {$this->lang->init_overlays('tests', $tests->all, ['name', 'instructions'])}
                                {if $tests->exists()}{$show_tests = true}
                                {$tests_for_json[$task->id].name = "{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}" scope='global'}
                                <tr class="task_header test_task_{$task->id}">
                                    <td colspan="{if $test_scoring}5{else}4{/if}" class="task_name">{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}{if $task->join_bonus_task} <span class="bonus_task">({translate line='tasks_task_is_bonus_task'})</span>{/if}</td>
                                </tr>
                                {/if}
                                {foreach $tests->all as $test}
                                <tr class="test_header test_type_{$test->type} test_task_{$task->id}">
                                    <td class="test_select"><input type="checkbox" name="test[id][]" value="{$test->id|intval}" checked="checked" /></td>
                                    <td class="test_name">{overlay table='tests' table_id=$test->id column='name' default=$test->name}</td>
                                    <td class="test_type">{$test_types[$test->type]}</td>
                                    <td class="test_subtype">{$test_subtypes[$test->type][$test->subtype]}</td>
                                    {if $test_scoring}<td class="test_evaluation">{if $test->enable_scoring gt 0}{translate line='tasks_test_table_test_evaluation_yes'}{else}{translate line='tasks_test_table_test_evaluation_no'}{/if}</td>{/if}
                                </tr>
                                {$tests_for_json[$task->id][$test->id].name = "{overlay table='tests' table_id=$test->id column='name' default=$test->name} ({$test_types[$test->type]} / {$test_subtypes[$test->type][$test->subtype]})" scope='global'}
                                {$test_instructions = {overlay table='tests' table_id=$test->id column='instructions' default=$test->instructions}}
                                {if $test_instructions}
                                <tr class="test_instructions test_type_{$test->type} test_task_{$task->id}">
                                    <td class="test_select"></td>
                                    <td class="test_instructions" colspan="{if $test_scoring}4{else}3{/if}">{$test_instructions|add_base_url}</td>
                                </tr>
                                {/if}
                                {/foreach}
                            {/foreach}
                            </tbody>
                        </table>
                        <div id="tests_execution_area_id"></div>
                    </fieldset>
                    {/capture}
                    {if $show_tests}<form action="" method="post" id="tests_form_id">{/if}
                    <table class="solutions_table">
                        <thead>
                            <tr>
                                {if $show_tests}<th class="select"></th>{/if}
                                <th class="version">{translate line='tasks_task_solution_table_header_version'}</th>
                                <th class="file">{translate line='tasks_task_solution_table_header_file'}</th>
                                <th class="size">{translate line='tasks_task_solution_table_header_size'}</th>
                                <th class="modified">{translate line='tasks_task_solution_table_header_modified'}</th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $solution_files as $file}
                            <tr>
                                {if $show_tests}<td class="select"><input type="radio" name="test[version]" value="{$file@key}" /></td>{/if}
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
                    {if $show_tests}{$tests_table}{/if}
                    {if $show_tests}</form>{/if}
                    <script type="text/javascript">
                        var tests_object = {$tests_for_json|json_encode};
                    </script>
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
    var student_id = {$list_student_account_model->id|intval};
    var messages = {
        test_being_executed: '{translate|addslashes line='tasks_test_message_test_being_executed'}',
        test_no_selection: '{translate|addslashes line='tasks_test_message_test_no_selection'}',
        test_type_not_selected: '{translate|addslashes line='tasks_test_message_test_type_not_selected'}',
        test_result_area: '{translate|addslashes line='tasks_test_message_test_result_area_legend'}',
        test_result_tests_in_progress: '{translate|addslashes line='tasks_test_message_tests_in_progress'}',
        test_result_evaluation: '{translate|addslashes line='tasks_test_message_tests_evaluation'}',
        test_result_not_obtained: '{translate|addslashes line='tasks_test_message_result_not_obtained'}',
        test_result_token_failed: '{translate|addslashes line='tasks_test_message_token_request_failed'}',
        countdown_time: '{translate|addslashes line='tasks_countdown_message_time_info'}',
        countdown_expired: '{translate|addslashes line='tasks_countdown_message_expired'}'
    };
    var test_evaluation_enabled = {if $task_set->enable_tests_scoring > 0 and $course->test_scoring_deadline gt date('Y-m-d H:i:s')}true{else}false{/if};
    {if $task_set_can_upload and !is_null($task_set->upload_end_time)}
    var enable_countdown = true;
    var countdown_to = new Date({$task_set->upload_end_time|date_format:Y}, {$task_set->upload_end_time|date_format:m} - 1, {$task_set->upload_end_time|date_format:d}, {$task_set->upload_end_time|date_format:H}, {$task_set->upload_end_time|date_format:i}, {$task_set->upload_end_time|date_format:s});
    {else}
    var enable_countdown = false;
    {/if}
</script>
{/block}