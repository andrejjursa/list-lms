{extends file='layouts/frontend_popup.tpl'}
{block main_content}
    {if is_object($test_queue) and $test_queue->exists()}
        <h3>{overlay table='task_sets' table_id=$test_queue->task_set_id column='name' default=$test_queue->task_set_name} / {translate_text text=$test_queue->task_set_course_name} / {translate_text text=$test_queue->task_set_course_period_name}</h3>
        
        <fieldset>
            <legend>{translate line='tasks_test_result_fieldset_legend_sum_result'}</legend>
            <table class="tests_result_sum_table">
                <tbody>
                    <tr>
                        <th>{translate line='tasks_test_result_sum_table_version'}:</th>
                        <td>{$test_queue->version}</td>
                    </tr>
                    <tr>
                        <th>{translate line='tasks_test_result_sum_table_type'}:</th>
                        <td>{$test_types[$test_queue->test_type]}</td>
                    </tr>
                    <tr>
                        <th>{translate line='tasks_test_result_sum_table_status'}:</th>
                        <td>{translate line="tasks_test_result_sum_table_status_{$test_queue->status}"}</td>
                    </tr>
                    <tr>
                        <th>{translate line='tasks_test_result_sum_table_points'}:</th>
                        <td>{$test_queue->points}</td>
                    </tr>
                    <tr>
                        <th>{translate line='tasks_test_result_sum_table_bonus'}:</th>
                        <td>{$test_queue->bonus}</td>
                    </tr>
                    <tr>
                        <th>{translate line='tasks_test_result_sum_table_message'}:</th>
                        <td>{$test_queue->result_message}</td>
                    </tr>
                </tbody>
            </table>
            {$test_queue->result_html}
        </fieldset>
        
        {foreach $tasks as $task}{if $tests_per_task[$task->id]}
        <fieldset>
            <legend>{overlay table='tasks' table_id=$task->id column='name' default=$task->name}</legend>
            <table class="tests_result_table">
                <thead>
                    <tr>
                        <th>{translate line='tasks_test_result_table_test_name'}</th>
                        <th>{translate line='tasks_test_result_table_points_percent'}</th>
                        <th>{translate line='tasks_test_result_table_points'}</th>
                        <th>{translate line='tasks_test_result_table_bonus_percent'}</th>
                        <th>{translate line='tasks_test_result_table_bonus'}</th>
                        <th>{translate line='tasks_test_result_table_result'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $tests_per_task[$task->id] as $test}
                        <tr>
                            <td>{overlay table='tests' table_id=$test.id column='name' default=$test.name}</td>
                            <td class="number">{$test.percent_points}%</td>
                            <td class="number">{$test.points}</td>
                            <td class="number">{$test.percent_bonus}%</td>
                            <td class="number">{$test.bonus}</td>
                            <td class="number"{if $test.result > 0} style="color: red;"{/if}>{$test.result}</td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <div class="test_result_text{if is_array($test.evaluation_table) and count($test.evaluation_table)} with_evaluation_table{/if}" data-test-id="{$test.id}">
                                    {$test.result_text}
                                </div>
                                {if is_array($test.evaluation_table) and count($test.evaluation_table)}
                                <div class="test_evaluation_table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>{translate line='tasks_test_result_evaluation_table_score_name'}</th>
                                                <th>{translate line='tasks_test_result_evaluation_table_score_current'}</th>
                                                <th>{translate line='tasks_test_result_evaluation_table_score_maximum'}</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$test_eval_current_total = 0}{$test_eval_maximum_total = 0}
                                            {foreach $test.evaluation_table as $evaluation_table_object}
                                            <tr>
                                                <td>{translate_text text=$evaluation_table_object->name}</td>
                                                <td>{$evaluation_table_object->current|doubleval} %{$test_eval_current_total = $test_eval_current_total + $evaluation_table_object->current|doubleval}</td>
                                                <td>{$evaluation_table_object->maximum|doubleval} %{$test_eval_maximum_total = $test_eval_maximum_total + $evaluation_table_object->maximum|doubleval}</td>
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                        <tfoot>{if $test_eval_current_total > 100}{$test_eval_current_total = 100}{elseif $test_eval_current_total < 0}{$test_eval_current_total = 0}{/if}
                                            <tr>{if $test_eval_maximum_total > 100}{$test_eval_maximum_total = 100}{elseif $test_eval_maximum_total < 0}{$test_eval_maximum_total = 0}{/if}
                                                <td>{translate line='tasks_test_result_evaluation_table_score_summary'}:</td>
                                                <td>{$test_eval_current_total} %</td>
                                                <td>{$test_eval_maximum_total} %</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="clear_evaluation_table"></div>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </fieldset>
        {/if}{/foreach}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_test_result_error_test_queue_not_found' inline}
    {/if}
{/block}