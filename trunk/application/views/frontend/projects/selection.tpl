{extends file='layouts/frontend.tpl'}
{block title}{translate line='projects_selection_page_header'}{/block}
{block main_content}
    <h1>{translate line='projects_selection_page_header'}</h1>
    {if $course->exists()}
        {if $project->exists()}
            <h2>{overlay table='task_sets' table_id=$project->id column='name' default=$project->name}</h2>
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            <fieldset>
                {$instructions = {overlay table='task_sets' table_id=$project->id column='instructions' default=$project->instructions}}
                {if $instructions}
                <div class="instructions text_content">
                    {$instructions|add_base_url}
                </div>
                {/if}
                <div class="instructions text_content">
                    <p>{translate|sprintf:{$project->project_selection_deadline|date_format:{translate line='common_datetime_format'}} line='projects_selection_time_limit_information'}</p>
                </div>
                <table class="project_tasks_table">
                    <thead>
                        <tr>
                            <th>{translate line='projects_project_tasks_table_header_task_name'}</th>
                            <th>{translate line='projects_project_tasks_table_header_free_selections'}</th>
                            <th>{translate line='projects_project_tasks_table_header_students_working_on'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $tasks as $task}
                        <tr class="{cycle values='project_tasks_line_odd,project_tasks_line_even'}">
                            {$students = $task->project_selection->include_related('student', 'fullname')->where('task_set_id', $project->id)->get_iterated()}
                            <td><a href="{internal_url url="projects/task/{$project->id}{overlay|text_convert_for_url table='task_sets' table_id=$project->id column='name' default=$project->name}/{$task->id}{overlay|text_convert_for_url table='tasks' table_id=$task->id column='name' default=$task->name}"}" class="preview_link">{overlay table='tasks' table_id=$task->id column='name' default=$task->name}</a></td>
                            <td>{translate|sprintf:{$task->join_max_projects_selections - $students->result_count()}:$task->join_max_projects_selections line='projects_project_tasks_table_body_free_out_of'}</td>
                            <td class="students">
                                {foreach $students as $student}
                                    <span{if $student@last} class="last_student"{/if}>{$student->student_fullname|trim|replace:' ':'&nbsp;'}</span>
                                {foreachelse}
                                    {translate line='projects_project_tasks_table_body_no_students'}
                                {/foreach}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </fieldset>
        {else}
            {include file='partials/frontend_general/flash_messages.tpl' inline}
            {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_project_found' inline}
        {/if}
    {else}
        {include file='partials/frontend_general/flash_messages.tpl' inline}
        {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_active_course' inline}
    {/if}
{/block}