{extends file='layouts/frontend_popup.tpl'}
{block title}{/block}
{block main_content}
    {if $course->exists()}
        <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
        {if $project->exists()}
            <h3>{overlay table='task_sets' column='name' table_id=$project->id default=$project->name}</h3>
            {foreach $tasks as $task}
                <fieldset>
                    <legend>{overlay table='tasks' column='name' table_id=$task->id default=$task->name}</legend>
                    <div class="text_content">
                        {overlay|add_base_url table='tasks' column='text' table_id=$task->id default=$task->text}
                    </div>
                    <div class="author">
                        <span>{translate|sprintf:{$task->author->get()->fullname|default:{translate line='projects_task_author_unknown'}} line='projects_task_author'}</span>
                    </div>
                    <div class="students">
                        <strong>{translate line='projects_project_tasks_table_header_students_working_on'}</strong>
                        {$students = $task->project_selection->include_related('student', 'fullname')->where('task_set_id', $project->id)->get_iterated()}
                        {foreach $students as $student}
                            <span{if $student@last} class="last_student"{/if}>{$student->student_fullname|trim|replace:' ':'&nbsp;'}</span>
                        {foreachelse}
                            {translate line='projects_project_tasks_table_body_no_students'}
                        {/foreach}
                    </div>
                </fieldset>
            {/foreach}
        {else}
            {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_project_found' inline}
        {/if}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:projects_no_course' inline}
    {/if}
{/block}