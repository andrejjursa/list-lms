{foreach $solutions as $solution}
<tr class="{if $solution->revalidate eq 1}revalidate_this{/if} {if $solution->not_considered}not_considered{/if}">
    <td>{$solution->id|intval}</td>
    <td>{$solution->created|date_format:{translate line='common_datetime_format'}}</td>
    <td>{$solution->updated|date_format:{translate line='common_datetime_format'}}</td>
    <td>{$solution->student_fullname} ({$solution->student_email})</td>
    <td>
        {if $solution->student_participant_group_name}
        <span title="{translate_text text=$solution->student_participant_group_name}">{translate_text|abbreviation text=$solution->student_participant_group_name}</span>
        {else}
        <span title="{translate line='admin_solutions_valuation_student_no_group'}">{translate|abbreviation line='admin_solutions_valuation_student_no_group'}</span>
        {/if}
    </td>
    <td>{$task_set->get_student_files_count($solution->student_id)|intval}</td>
    <td>{$solution->ip_address}</td>
    {if is_null($solution->points) and is_null($solution->tests_points)}
    <td colspan="3">{translate line='admin_solutions_list_solution_not_valuated'}</td>
    {else}
    <td>{$solution->points|floatval + $solution->tests_points|floatval}</td>
    <td><span title="{$solution->comment|strip_tags|escape:'html'}">{$solution->comment|strip_tags|truncate:20}</span></td>
    <td>{$solution->teacher_fullname} ({$solution->teacher_email})</td>
    {/if}
    <td class="controlls"><a href="{internal_url|add_to_url:{"group_id/{$solution->student_participant_group_id}"}:{!is_null($solution->student_participant_group_id)} url="admin_solutions/valuation/{$task_set->id|intval}/{$solution->id|intval}"}" class="button special open_valuation_dialog" title="{translate line='admin_solutions_list_table_button_valuate'}"><span class="list-icon list-icon-approve"></span></a></td>
    <td class="controlls"><a href="{internal_url url="admin_solutions/student_solution_upload/{$solution->id|intval}"}" class="button special open_upload_dialog" title="{translate line='admin_solutions_list_table_button_file_upload'}"><span class="list-icon list-icon-upload"></span></a></td>
</tr>
{/foreach}