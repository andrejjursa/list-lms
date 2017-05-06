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
                            {if !is_null($task_set->solution_id) AND (!is_null($task_set->solution_points) OR !is_null($task_set->solution_tests_points))}
                                {if trim($task_set->solution_comment)}
                                    {if $task_set->solution_comment|mb_strlen > 40}
                                        <a href="javascript:void(0);" title="{translate line='tasks_table_click_to_display_comment'}" class="click_enlarge_comment" for="task_set_comment_{$task_set->id}">{$task_set->solution_comment|strip_tags|truncate:40}</a>
                                        <div class="whole_comment" id="task_set_comment_{$task_set->id}" title="{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} | {$task_set->solution_points|default:0|floatval + $task_set->solution_tests_points|default:0|floatval} / {if !is_null($task_set->points_override)}{$task_set->points_override|default:0|floatval}{else}{$task_set->total_points|default:0|floatval}{/if} | {$task_set->solution_teacher_fullname}"><div class="comment_body">{$task_set->solution_comment|nl2br}</div></div>
                                    {else}
                                        {$task_set->solution_comment|nl2br}
                                    {/if}
                                    <hr />
                                {/if}
                                <em>{$task_set->solution_teacher_fullname}</em>
                            {else}
                                {translate line='tasks_table_not_valuated'}
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
                {foreach $task_set_types as $task_set_type}
                <tr>
                    <td class="td_task_set_type">{translate_text|str_to_first_upper|truncate:20 text=$task_set_type->name}</td>
                    <td class="td_points">{$points[$task_set_type->id].total|floatval}&nbsp;/&nbsp;{$points[$task_set_type->id].max|floatval}</td>
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
{/block}
