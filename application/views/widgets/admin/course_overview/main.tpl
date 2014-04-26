{extends file='widgets/layouts/admin/simple.tpl'}
{block widget_name}{translate line='widget_admin_course_overview_title'}{if $course and $course->exists()} - {translate_text text=$course->name} / {translate_text text=$course->period_name}{/if}{/block}
{block widget_content}
    {if $course and $course->exists()}
        <div class="widget_course_overview_content">
            <div class="widget_course_overview_content_left">
                <p><strong>{translate line='widget_admin_course_overview_content_task_sets_count'}:</strong> {$task_sets_count}</p>
                <p><strong>{translate line='widget_admin_course_overview_content_projects_count'}:</strong> {$projects_count}</p>
                <p><strong>{translate line='widget_admin_course_overview_content_groups_count'}:</strong> {$groups_count}</p>
                <p><strong>{translate line='widget_admin_course_overview_content_students_count'}:</strong> {$students_count}</p>
            </div>
            <div class="widget_course_overview_content_right">
                <p><strong>{translate line='widget_admin_course_overview_content_nearest_deadlines'}:</strong></p>
                {foreach $task_sets as $task_set}
                    <p><a href="{internal_url url="admin_solutions/solutions_list/{$task_set->id}"}">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</a> ({$task_set->common_upload_end_time|date_format:{translate line='common_datetime_format'}})</p>
                {foreachelse}
                    <p>{translate line='widget_admin_course_overview_content_nearest_deadlines_none'}</p>
                {/foreach}
            </div>
            <div class="widget_course_overwiew_clear"></div>
        </div>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:widget_admin_course_overview_error_course_not_found' inline}
    {/if}
{/block}