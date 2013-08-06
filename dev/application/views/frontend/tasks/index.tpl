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
                    {foreach $task_set_types as $task_set_type}{if $task_set_in_types[$task_set_type->id]}
                    <tr>
                        <td colspan="4" class="td_task_set_type">{translate_text text=$task_set_type->name}</td>
                    </tr>
                    {foreach $task_set_in_types[$task_set_type->id] as $task_set}
                    <tr class="{cycle values="tr_background_odd,tr_background_even" name=$task_set_type->name}">{$solution = $task_set->solution->where('student_id', $list_student_account.id)->include_related('teacher', 'fullname')->get()}
                        <td class="td_name"><a href="{internal_url url="tasks/task/{$task_set->id}"}">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}</a></td>
                        <td class="td_time_limit">{if is_null($task_set->upload_end_time)}{translate line='tasks_table_no_upload_limit'}{else}{$task_set->upload_end_time}{/if}</td>
                        <td class="td_points">{$solution->points|default:0|floatval} / {if !is_null($task_set->points_override)}{$task_set->points_override|default:0|floatval}{else}{$task_set->total_points|default:0|floatval}{/if}</td>
                        <td class="td_comment">
                            {if $solution->exists() AND !is_null($solution->points)}
                                {if trim($solution->comment)}
                                    {$solution->comment|nl2br}
                                    <hr />
                                {/if}
                                <em>{$solution->teacher_fullname}</em>
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
                    <td class="td_task_set_type">{translate_text text=$task_set_type->name}</td>
                    <td class="td_points">{$points[$task_set_type->id].total|floatval}&nbsp;/&nbsp;{$points[$task_set_type->id].max|floatval}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{/block}