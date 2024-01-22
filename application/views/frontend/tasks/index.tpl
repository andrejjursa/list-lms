{extends file='layouts/frontend.tpl'}
{block title}{translate line='tasks_page_title'}{/block}
{block main_content}
    <h1>{translate line='tasks_page_title'}</h1>
    {if $course->exists()}
        <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {$task_set_in_types = []}
        {foreach $task_sets as $task_set}
            {$task_set_in_types[$task_set->task_set_type_id][] = $task_set}
        {/foreach}
        <fieldset>
            <div class="task_sets_show_all{if $showAllTaskSets} show_all{/if}">
                <form method="post" action="{internal_url url='tasks'}">
                    <label><input type="checkbox" name="show_all_task_sets" value="1" onchange="javascript:submit();"{if $showAllTaskSets} checked="checked"{/if}> {translate line='tasks_show_all_task_sets'}</label>
                </form>
            </div>
            <table class="task_sets_table">
                <thead>
                    <tr>
                        <th class="th_name">{translate line='tasks_table_header_name'}</th>
                        <th class="th_time_limit">{translate line='tasks_table_time_limit'}</th>
                        <th class="th_points">{translate line='tasks_table_header_points'}</th>
                        <th class="th_comment">{translate line='tasks_table_header_comment'}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="4">{translate_text|sprintf:{$points.total|floatval}:{$points.max|floatval} text='lang:tasks_table_task_set_type_sum_points_total'}</td>
                    </tr>
                </tfoot>
                <tbody>
                    {$current_time = date('Y-m-d H:i:s')}
                    {foreach $task_set_types as $task_set_type}{if $task_set_in_types[$task_set_type->id]}
                    <tr>
                        <td colspan="4" class="td_task_set_type">{translate_text|str_to_first_upper text=$task_set_type->name}</td>
                    </tr>
                    {foreach $task_set_in_types[$task_set_type->id] as $task_set}
                    {if $task_set->join_upload_solution eq 1 and !is_null($task_set->pb_upload_end_time) and $task_set->pb_upload_end_time lt $current_time}
                        {$after_deadline = 1}
                    {else}
                        {$after_deadline = 0}
                    {/if}
                    {if !$showAllTaskSets && $after_deadline}{continue}{/if}
                    {$files_count = $task_set->get_student_files_count($list_student_account_model->id)}
                    <tr class="{cycle values="tr_background_odd,tr_background_even" name=$task_set_type->name} {get_task_set_timed_class($task_set->pb_upload_end_time, $task_set->join_upload_solution, $files_count)}">
                        <td class="td_name">{capture name='task_set_name' assign='task_set_name'}{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}{/capture}
                            <a href="{internal_url url="tasks/task/{$task_set->id}{$task_set_name|text_convert_for_url}"}">{$task_set_name}</a>
                            {if $files_count gt 0}
                            <br /><sub>{translate|sprintf:$files_count line='tasks_table_student_solutions_count'}</sub>
                            {/if}
                        </td>
                        <td class="td_time_limit">
                            {if $task_set->join_upload_solution eq 1}
                                {if is_null($task_set->pb_upload_end_time)}{translate line='tasks_table_no_upload_limit'}{else}{$task_set->pb_upload_end_time|date_format:{translate line='common_datetime_format'}}{/if}
                            {else}
                                {translate line='tasks_table_no_uploading'}
                            {/if}
                        </td>
                        <td class="td_points{if $task_set->solution_not_considered} not_considered{elseif $task_set->solution_revalidate} revalidate{/if}"{if !$task_set->solution_not_considered and $task_set->solution_revalidate} title="{translate line='tasks_table_points_in_revalidation_process'}"{/if}>{if is_null($task_set->solution_points) and is_null($task_set->solution_tests_points)}-{else}{$task_set->solution_points|default:0|floatval + $task_set->solution_tests_points|default:0|floatval}{/if} / {if !is_null($task_set->points_override)}{$task_set->points_override|default:0|floatval}{else}{$task_set->total_points|default:0|floatval}{/if}</td>
                        <td class="td_comment">
                            {if is_null($task_set->solution_id) OR (is_null($task_set->solution_points) AND is_null($task_set->solution_tests_points))}
                                <div class="not_valuated_message">{translate line='tasks_table_not_valuated'}</div>
                                {if !is_null($task_set->solution_id) AND (!is_null($task_set->solution_teacher_fullname) OR !is_null($task_set->solution_comment))}
                                    <hr />
                                {/if}
                            {/if}
                            {if !is_null($task_set->solution_id) AND (!is_null($task_set->solution_teacher_fullname) OR !is_null($task_set->solution_comment))}
                                {if trim($task_set->solution_comment)}
                                    {if $task_set->solution_comment|mb_strlen > 40}
                                        <a href="javascript:void(0);" title="{translate line='tasks_table_click_to_display_comment'}" class="click_enlarge_comment" for="task_set_comment_{$task_set->id}">{$task_set->solution_comment|escape:'html'|truncate:40}</a>
                                        <div class="whole_comment" id="task_set_comment_{$task_set->id}" title="{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} | {if is_null($task_set->solution_points) AND is_null($task_set->solution_tests_points)}-{else}{$task_set->solution_points|default:0|floatval + $task_set->solution_tests_points|default:0|floatval}{/if} / {if !is_null($task_set->points_override)}{$task_set->points_override|default:0|floatval}{else}{$task_set->total_points|default:0|floatval}{/if} | {$task_set->solution_teacher_fullname}"><div class="comment_body"><pre>{$task_set->solution_comment|escape:'html'}</pre></div></div>
                                    {else}
                                        {$task_set->solution_comment|nl2br}
                                    {/if}
                                    <hr />
                                {/if}
                                <em>{$task_set->solution_teacher_fullname}</em>
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                    <tr>
                        <td colspan="4" class="td_task_set_type_points">{translate_text|sprintf:{$points[$task_set_type->id].total|floatval}:{$points[$task_set_type->id].max|floatval} text='lang:tasks_table_task_set_type_points_total'}</td>
                    </tr>
                    {/if}{/foreach}
                </tbody>
            </table>

            {get_task_sets_color_legend()}
        </fieldset>
    {else}
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_no_active_course' inline}
    {/if}
{/block}
{block left_content}
    <div class="title"><h4>{translate line='tasks_left_bar_points_title'}</h4></div>
    <div class="tasks_points">
        <table class="points_table">
            <thead>
                <tr>
                    <th class="th_task_set_type">{translate line='tasks_left_bar_points_table_header_task_set_type'}</th>
                    <th class="th_points">{translate line='tasks_left_bar_points_table_header_points'}</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td class="td_task_set_type">{translate line='tasks_left_bar_points_table_footer_sum_points'}</td>
                    <td class="td_points">{$points.total|floatval}&nbsp;/&nbsp;{$points.max|floatval}</td>
                </tr>
            </tfoot>
            <tbody>
                {assign 'total_points' 0}{assign 'max_points' 0}
                {foreach $task_set_types as $task_set_type}
                
                {if $points[$task_set_type->id].total === 'err'}
                    {$total_points = 'err'}{$max_points = 'err'}
                {else}
                    {$total_points = $points[$task_set_type->id].total|floatval}{$max_points = $points[$task_set_type->id].max|floatval}
                {/if} 

                <tr class="{(isset($points[$task_set_type->id].include_in_total) && !$points[$task_set_type->id].include_in_total) ? 'tr_task_set_type_not_included' : ''}">
                    <td class="td_task_set_type">{translate_text|str_to_first_upper|truncate:20 text=$task_set_type->name}</td>
                    {if is_null($points[$task_set_type->id].min)}
                        <td class="td_points">{$total_points}&nbsp;/&nbsp;{$max_points}</td>
                    {else}
                        {if !$points[$task_set_type->id].min_in_percentage}
                            <td class="td_points">{$total_points}&nbsp;/&nbsp;{$max_points} ({translate line='tasks_left_bar_points_min'}&nbsp;{$points[$task_set_type->id].min|floatval})</td>
                        {else}
                            <td class="td_points">{$total_points}&nbsp;/&nbsp;{$max_points} ({translate line='tasks_left_bar_points_min'}&nbsp;{($points[$task_set_type->id].min * $points[$task_set_type->id].max / 100)|floatval})</td>
                        {/if}
                    {/if}
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="title"><h4>{translate line='tasks_left_bar_projects_points_title'}</h4></div>
    <div class="tasks_points">
        <table class="points_table">
            {$project_points = 0}{$project_total = 0}
            {capture name='projects_points_body' assign='projects_points_body'}
                {foreach $projects as $project}
                <tr>
                    <td class="td_task_set_type">{overlay|truncate:15 table='task_set' column='name' table_id=$project->id default=$project->name}</td>
                    <td class="td_points">{$project->solution_points|floatval}&nbsp;/&nbsp;{$project->points_override|floatval}</td>{$project_points = $project_points + $project->solution_points|floatval}{$project_total = $project_total + $project->points_override|floatval}
                </tr>
                {/foreach}
            {/capture}
            <thead>
                <tr>
                    <th class="th_task_set_type">{translate line='tasks_left_bar_projects_points_header_project'}</th>
                    <th class="th_points">{translate line='tasks_left_bar_points_table_header_points'}</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td class="td_task_set_type">{translate line='tasks_left_bar_points_table_footer_sum_points'}</td>
                    <td class="td_points">{$project_points|floatval}&nbsp;/&nbsp;{$project_total|floatval}</td>
                </tr>
            </tfoot>
            <tbody>
                {$projects_points_body}
            </tbody>
        </table>
    </div>
    <div class="color_legend">
        <div class="color_legend_title"><h4>{translate line='tasks_left_bar_color_legend_title'}</h4></div>
        <div class="color_legend_data">{translate line='tasks_left_bar_color_legend_data_black'}</div>
        <div class="color_legend_data blue_text">{translate line='tasks_left_bar_color_legend_data_blue'}</div>
    </div>

{/block}
