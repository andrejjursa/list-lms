{if $course->exists()}
    <h3>{translate_text text=$course->name} / {translate_text text=$course->period_name}{if $group->exists()} / {translate_text text=$group->name}{/if}</h3>
    
    <div class="valuation_table_outer_wrap">
        <div class="valuation_table_wrap">
            <div class="overflow"><div class="extend">
            <table class="valuation_table">
                <thead>
                    <tr>
                        <th class="student_col sort:students">{translate line='admin_solutions_valuation_tables_table_header_student'}</th>
                        {foreach $header as $header_item}
                        <th class="task_set_type_col sort:task_set_type_{$header_item@key}:desc">{translate_text text=$header_item.name}:</th>
                            {if $filter.simple ne 1}{foreach $header_item.task_sets as $task_set}
                            <th class="task_set_col sort:task_set_{$header_item@key}_{$task_set@key}:desc">
                                {overlay table='task_sets' column='name' table_id=$task_set@key default=$task_set.name}
                                {if !$group->exists()}<br />
                                <sub>
                                    {if is_array($task_set.group_name)}
                                        {foreach $task_set.group_name as $group_name}
                                            {if !$group_name@first}, {/if}
                                            {translate_text text=$group_name}
                                        {/foreach}
                                    {else}
                                        {translate_text text=$task_set.group_name|default:'lang:admin_solutions_valuation_tables_table_header_for_all_groups'}
                                    {/if}
                                </sub>
                                {/if}
                            </th>
                            {/foreach}{/if}
                        {/foreach}
                        <th class="total_col sort:total:desc">{translate line='admin_solutions_valuation_tables_table_header_total'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $points_table as $points_row}
                    <tr>
                        <td class="student_col"><big>{$points_row.student.fullname}</big><br /><sub>[{$points_row.student.email}]</sub></td>
                        {foreach $header as $header_item}
                        <td class="task_set_type_col">{$points_row.points[$header_item@key].total|floatval} / {$task_set_types_points_max[$header_item@key]|floatval}</td>
                            {if $filter.simple ne 1}{foreach $header_item.task_sets as $task_set}
                                {if isset($points_row.points[$header_item@key]) && array_key_exists($task_set@key, $points_row.points[$header_item@key])}
                                    {if is_null($points_row.points[$header_item@key][$task_set@key].points)}
                                    <td class="task_set_col not_valuated">{translate line='admin_solutions_valuation_tables_solution_not_valuated'}</td>
                                    {else}
                                        {if $points_row.points[$header_item@key][$task_set@key].not_considered}
                                        <td class="task_set_col not_considered">{translate line='admin_solutions_valuation_tables_solution_not_considered'}</td>
                                        {else}
                                        <td class="task_set_col{if $points_row.points[$header_item@key][$task_set@key].revalidate} not_valuated{/if}">{$points_row.points[$header_item@key][$task_set@key].points|floatval} / {$task_set.points|floatval}</td>
                                        {/if}
                                    {/if}
                                {else}
                                    {if !is_null($task_set.group_id) and ((!is_array($task_set.group_id) and $points_row.student.group ne $task_set.group_id) or (is_array($task_set.group_id) and !in_array($points_row.student.group, $task_set.group_id)))}
                                    <td class="task_set_col not_this_group">{translate line='admin_solutions_valuation_tables_solution_not_this_group'}</td>
                                    {else}
                                    <td class="task_set_col not_submited">{translate line='admin_solutions_valuation_tables_solution_not_submited'}</td>
                                    {/if}
                                {/if}
                            {/foreach}{/if}
                        {/foreach}
                        <td class="total_col">{$points_row.points.total|floatval} / {$total_points|floatval}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            </div></div>
        </div>
    </div>
{else}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_valuation_tables_error_no_course_selected' inline}
{/if}