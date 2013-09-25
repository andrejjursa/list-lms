{extends file='layouts/backend_popup.tpl'}
{block title}{/block}
{block main_content}
    {if $solution->exists()}
        <h3>{translate_text text=$solution->task_set->name} / {translate_text text=$solution->task_set_course_name} / {translate_text text=$solution->task_set_course_period_name}{if $solution->task_set_group_name} / {translate_text text=$solution->task_set_group_name}{/if}</h3>
        <h4>{$solution->student_fullname} ({$solution->student_email})</h4>
        {include file='partials/backend_general/flash_messages.tpl' inline}
        <div id="tabs">
            <ul>
                <li><a href="#tabs-form">{translate line='admin_solutions_valuation_tabs_label_form'}</a></li>
                <li><a href="#tabs-files">{translate line='admin_solutions_valuation_tabs_label_files'}</a></li>
                <li><a href="#tabs-tests">{translate line='admin_solutions_valuation_tabs_label_tests'}</a></li>
                <li><a href="{internal_url url="admin_solutions/display_tasks_list/{$solution->task_set->id}"}">{translate line='admin_solutions_valuation_tabs_label_tasks'}</a></li>
            </ul>
            <div id="tabs-form">
                <form action="{internal_url url="admin_solutions/update_valuation/{$solution->task_set->id|intval}/{$solution->id|intval}"}" method="post">
                    <div class="field">
                        <label for="solution_points_id" class="required">{translate line='admin_solutions_valuation_form_label_points'}:</label>
                        <p class="input"><input type="text" name="solution[points]" value="{$smarty.post.solution.points|default:$solution->points|escape:'html'}" id="solution_points_id" /></p>{capture name='total_points' assign='total_points'}{if !is_null($solution->task_set->points_override)}{$solution->task_set->points_override}{else}{$solution->task_set_total_points}{/if}{/capture}
                        <p class="input"><em>{translate|sprintf:{$total_points|floatval} line='admin_solutions_valuation_form_label_points_hint'}</em></p>
                        {form_error field='solution[points]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="solution_comment_id">{translate line='admin_solutions_valuation_form_label_comment'}:</label>
                        <p class="input"><textarea name="solution[comment]" id="solution_comment_id">{$smarty.post.solution.comment|default:$solution->comment|escape:'html'}</textarea></p>
                        {form_error field='solution[comment]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="solution_not_considered_id">{translate line='admin_solutions_valuation_form_label_not_considered'}:</label>
                        <p class="input"><input type="checkbox" value="1" name="solution[not_considered]" id="solution_not_considered_id"{if $smarty.post.solution.not_considered|default:$solution->not_considered} checked="checked"{/if} /></p>
                    </div>
                    <div class="buttons">
                        <input type="submit" name="submit_button" value="{translate line='admin_solutions_valuation_form_button_submit'}" class="button" />
                    </div>
                </form>
            </div>
            <div id="tabs-files">
                <div class="filter_wrap">
                    <form action="" method="post" id="filter_form_id">
                        <div class="field">
                            <label>{translate line='admin_solutions_validation_filter_label_version'}:</label>
                            <p class="input">
                                <select name="zip[file]" size="1">
                                    <option value=""></option>
                                    {foreach $solution->task_set->get_student_files($solution->student_id) as $file}
                                        <option value="{$file.file|encode_for_url}"{if $file@last} selected="selected"{/if}>{$file@key}</option>
                                    {/foreach}
                                </select>
                            </p>
                        </div>
                        <div class="buttons download_file_buttons">
                            <input type="button" class="button" name="download_file_button" value="{translate line='admin_solutions_validation_filter_button_download_file'}" />
                        </div>
                        <div class="field select_file">
                            <label>{translate line='admin_solutions_validation_filter_label_file'}:</label>
                            <p class="input"><select name="zip[index]" size="1" id="zip_index_id"></select></p>
                        </div>
                        <div class="buttons read_file_buttons">
                            <input type="button" class="button" name="read_file_button" value="{translate line='admin_solutions_validation_filter_button_read_file'}" />
                        </div>
                    </form>
                </div>
                <div id="file_content_id"></div>
            </div>
            <div id="tabs-tests">
                <form action="" method="post" id="tests_form_id">
                    <div class="field">
                        <label>{translate line='admin_solutions_validation_filter_label_version'}:</label>
                        <p class="input">
                            <select name="test[version]" size="1">
                                <option value=""></option>
                                {foreach $solution->task_set->get_student_files($solution->student_id) as $file}
                                    <option value="{$file@key}"{if $file@last} selected="selected"{/if}>{$file@key}</option>
                                {/foreach}
                            </select>
                        </p>
                    </div>
                    {$tasks = $solution->task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get()}
                    {$this->lang->init_overlays('tasks', $tasks, ['name'])}
                    {$allowed_test_types = ','|explode:$solution->task_set->allowed_test_types}
                    {$any_test_found = FALSE}
                    {$tests_object = []}
                    {foreach $allowed_test_types as $test_type}
                        {if isset($test_types[$test_type])}
                        {$tests_found = FALSE}
                        {capture name="test_type_block" assign="test_type_block"}
                        {foreach $tasks->all as $task}
                            {$tests = $task->test->where('enabled', 1)->where('type', $test_type)->order_by('type', 'asc')->order_by('subtype', 'asc')->get()}
                            {if $tests->exists()}
                            {$tests_object[$task->id].name = "{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}"}
                            {$this->lang->init_overlays('tests', $tests, ['name'])}
                            <div class="field">
                                <label>{$task@iteration}. {overlay table='tasks' table_id=$task->id column='name' default=$task->name}:</label>
                                {foreach $tests->all as $test}{$tests_found = TRUE}{$any_test_found = TRUE}
                                {$tests_object[$task->id][$test->id].name = "{overlay table='tests' table_id=$task->id column='name' default=$test->name} ({$test_types[$test->type]} / {$test_subtypes[$test->type][$test->subtype]})"}
                                <div class="input"><label><input type="checkbox" name="test[id][]" value="{$test->id}" checked="checked" class="test_type-{$test->type} test_id" /> {overlay table='tests' table_id=$task->id column='name' default=$test->name} ({$test_subtypes[$test->type][$test->subtype]})</label></div>
                                {/foreach}
                            </div>
                            {/if}
                        {/foreach}
                        {/capture}
                        {if $tests_found}
                        <div class="field">
                            <h3><label>{$test_types[$test_type]} <input type="checkbox" name="switch_checkboxes[test_type-{$test_type}]" value="1" checked="checked" class="switch_checkboxes" /></label></h3>
                        </div>
                        {$test_type_block}
                        {/if}
                        {/if}
                    {/foreach}
                    {if $any_test_found}
                    <div class="buttons">
                        <input type="submit" name="execute_test" value="{translate line='admin_solutions_valuation_test_form_execute_tests'}" class="button" />
                    </div>
                    {else}
                        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_valuation_test_form_no_tests_found' inline}
                    {/if}
                </form>
                <div id="tests_execution_area_id"></div>
                <script type="text/javascript">
                    var tests_object = {$tests_object|json_encode};
                </script>
            </div>
        </div>
    {else}
        {include file='partials/backend_general/flash_messages.tpl' inline}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_valuation_solution_not_found' inline}
    {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_set_id = {$solution->task_set->id|intval};
    var solution_id = {$solution->id|intval};
    var student_id = {$solution->student_id|intval};
    var messages = {
        test_no_selection: '{translate line='admin_solutions_validation_test_messages_test_not_selected'}',
        test_being_executed: '{translate line='admin_solutions_validation_test_messages_test_being_executed'}'
    };
</script>
{/block}