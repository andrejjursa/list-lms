{extends file='layouts/frontend.tpl'}
{block title}{translate text='projects_index_page_header'}{/block}
{block main_content}
    <h1>{translate line='projects_index_page_header'}</h1>
    {if $course->exists()}
        <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        <fieldset>
            <table class="projects_list_table">
                <thead>
                    <tr>
                        <th>{translate line='projects_list_table_header_name'}</th>
                        <th>{translate line='projects_list_table_header_selected'}</th>
                        <th>{translate line='projects_list_table_header_solutions'}</th>
                        <th colspan="2">{translate line='projects_list_table_header_deadline_evaluation'}</th>
                    </tr>
                </thead>{$points = 0}{$max_points = 0}
                {capture name='table_body' assign='table_body'}
                {foreach $projects as $project}{cycle values='projects_list_line_odd,projects_list_line_even' assign='line_class'}
                    <tr class="{$line_class}">{capture name='project_name' assign='project_name'}{overlay table='task_sets' table_id=$project->id column='name' default=$project->name}{/capture}
                        <td rowspan="2"><a href="{internal_url url="projects/selection/{$project->id}{$project_name|text_convert_for_url}"}" title="{translate line='projects_list_table_body_click_to_select_preview_tasks'}">{$project_name}</a></td>
                        <td rowspan="2">
                            {if $project->project_selection_task_id}
                                {capture name='task_name' assign='task_name'}{overlay table='tasks' table_id=$project->project_selection_task_id column='name' default=$project->project_selection_task_name}{/capture}
                                <a href="{internal_url url="projects/task/{$project->id}{$project_name|text_convert_for_url}/{$project->project_selection_task_id}{$task_name|text_convert_for_url}"}">{$task_name}</a>
                            {else}
                                {translate line='projects_list_table_body_no_task_selected'}
                            {/if}
                        </td>
                        <td rowspan="2">{$project->get_student_files_count($list_student_account_model->id)}</td>
                        <td colspan="2">{$project->upload_end_time|date_format:{translate line='common_datetime_format'}}</td>
                    </tr>
                    <tr class="{$line_class}">{if $project->solution_not_considered eq 0}{$points = $points + $project->solution_points|default:0|floatval}{/if}{$max_points = $max_points + $project->points_override|default:0|floatval}
                        <td class="pl_points{if $project->solution_not_considered} not_considered{/if}">{$project->solution_points|default:0|floatval} / {$project->points_override|default:0|floatval}</td>
                        {if !is_null($project->solution_id) AND !is_null($project->solution_points)}
                        <td>
                            {if trim($project->solution_comment)}
                                {if $project->solution_comment|mb_strlen > 40}
                                    <a href="javascript:void(0);" title="{translate line='projects_list_table_body_click_to_display_comment'}" class="click_enlarge_comment" for="project_comment_{$project->id}">{$project->solution_comment|strip_tags|truncate:40}</a>
                                    <div class="whole_comment" id="project_comment_{$project->id}" title="{overlay table='task_sets' table_id=$project->id column='name' default=$project->name} | {$project->solution_points|default:0|floatval} / {$project->points_override|default:0|floatval} | {$project->solution_teacher_fullname}"><div class="comment_body">{$project->solution_comment|nl2br}</div></div>
                                {else}
                                    {$task_set->solution_comment|nl2br}
                                {/if}
                                <hr />
                            {/if}
                            <em>{$project->solution_teacher_fullname}</em>
                        </td>
                        {else}
                        <td>{translate line='projects_list_table_body_no_evaluation'}</td>
                        {/if}
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="5">
                            {include file='partials/frontend_general/error_box.tpl' message='lang:projects_list_table_body_no_projects_yet' inline}
                        </td>
                    </tr>
                {/foreach}
                {/capture}
                <tfoot>
                    <tr>
                        <td colspan="5">{translate line='projects_list_table_footer_points'}: {$points} / {$max_points}</td>
                    </tr>
                </tfoot>
                <tbody>{$table_body}</tbody>
            </table>
        </fieldset>
    {else}    
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_active_course' inline}
    {/if}
{/block}