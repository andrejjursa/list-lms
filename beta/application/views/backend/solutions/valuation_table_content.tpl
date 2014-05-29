{if $course->exists()}
    <h3>{translate_text text=$course->name} / {translate_text text=$course->period_name}{if $group->exists()} / {translate_text text=$group->name}{/if}</h3>
    
    <table id="valutation_table" width="100%">
        <thead>
            {if count($table_data.header.content_type_task_set.items) gt 0 or count($table_data.header.content_type_project.items) gt 0}
            <tr>
                <th rowspan="2" class="static">{translate line='admin_solutions_valuation_tables_table_header_student_firstname'}</th>
                <th rowspan="2" class="static">{translate line='admin_solutions_valuation_tables_table_header_student_lastname'}</th>
                <th class="content_type_task_sets centering" colspan="{$table_data.header.content_type_task_set.items|count + 1}">{$table_data.header.content_type_task_set.content_type_name}</th>
                <th class="content_type_projects centering" colspan="{$table_data.header.content_type_project.items|count + 1}">{$table_data.header.content_type_project.content_type_name}</th>
                <th rowspan="2" class="total_sum">{translate line='admin_solutions_valuation_tables_table_header_total'}</th>
            </tr>
            <tr>
                {foreach $table_data.header.content_type_task_set.items as $header_item}
                    {if $header_item.type eq 'task_set_type'}
                        <th class="type_{$header_item.type} ctype_task_set" title="{$header_item.title|escape:'html'}">{$header_item.name|str_to_first_upper}</th>    
                    {else}
                        <th class="type_{$header_item.type} ctype_task_set" title="{$header_item.name|escape:'html'}{if $header_item.title} | {$header_item.title|escape:'html'}{/if}">{$header_item.name|abbreviation}</th>
                    {/if}
                {/foreach}
                <th class="summary ctype_task_set">{translate line='admin_solutions_valuation_tables_table_header_task_sets_subtotal'}</th>
                {foreach $table_data.header.content_type_project.items as $header_item}
                    <th class="type_{$header_item.type} ctype_project" title="{$header_item.name|escape:'html'}">{$header_item.name|abbreviation}</th>
                {/foreach}
                <th class="summary ctype_project">{translate line='admin_solutions_valuation_tables_table_header_projects_subtotal'}</th>
            </tr>
            {else}
            <tr>
                <th class="static">{translate line='admin_solutions_valuation_tables_table_header_student_firstname'}</th>
                <th class="static">{translate line='admin_solutions_valuation_tables_table_header_student_lastname'}</th>
                <th class="no_content">{translate line='admin_solutions_valuation_tables_table_header_no_content'}</th>
                <th class="total_sum">{translate line='admin_solutions_valuation_tables_table_header_total'}</th>
            </tr>
            {/if}
        </thead>
        <tbody>
            {foreach $table_data.content as $row}
            <tr class="gradeA">
                <td><a href="mailto:{$row.email}">{$row.fullname|get_firstname_from_fullname}</a></td>
                <td><a href="mailto:{$row.email}">{$row.fullname|get_lastname_from_fullname}</a></td>
                {if count($row.task_sets_points) gt 0 or count($row.projects_points) gt 0}
                {if count($row.task_sets_points) gt 0}
                    {foreach $row.task_sets_points as $item}
                        <td class="type_{$item.type} flag_{$item.flag} ctype_task_set" title="{translate line="admin_solutions_valuation_tables_table_body_flag_{$item.flag}"}" data-order="{$item.points|valuation_table_col_points_to_data_order}" data-sort="{$item.points|valuation_table_col_points_to_data_order}">{$item.points}</td>
                    {/foreach}
                {/if}
                <td class="type_summary ctype_task_set">{$row.task_sets_points_total}</td>
                {if count($row.projects_points) gt 0}
                    {foreach $row.projects_points as $item}
                        <td class="type_{$item.type} flag_{$item.flag} ctype_project" title="{translate line="admin_solutions_valuation_tables_table_body_flag_{$item.flag}"}" data-order="{$item.points|valuation_table_col_points_to_data_order}" data-sort="{$item.points|valuation_table_col_points_to_data_order}">{$item.points}</td>
                    {/foreach}
                {/if}
                <td class="type_summary ctype_project">{$row.projects_points_total}</td>
                {else}
                <td>{translate line='admin_solutions_valuation_tables_table_body_no_content'}</td>
                {/if}
                <td class="type_summary">{$row.total_points}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_valuation_tables_error_no_course_selected' inline}
{/if}