{include file='partials/backend_general/flash_messages.tpl' inline}
{if $task_set->exists()}
    <table class="batch_valuation_table">
        <thead>
            <tr>
                <th>{translate line='admin_solutions_batch_valuation_table_header_student_fullname'}</th>
                <th>{translate line='admin_solutions_batch_valuation_table_header_student_email'}</th>
                <th>{translate line='admin_solutions_batch_valuation_table_header_solution_points'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $batch_valuation_students as $student}
            <tr class="student_{$student->id}">
                <td>{$student->fullname}</td>
                <td>{$student->email}</td>
                <td><input type="text" name="batch_valuation[{$student->id}][points]" value="{$smarty.post.batch_valuation[$student->id].points|default:$student->solution_points}" class="full_width" /></td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {if $batch_valuation_students}
    <div class="buttons">
        <input type="submit" name="submit_button" value="{translate line='admin_solutions_batch_valuation_form_submit_batch_save'}" class="button" />
    </div>
    {/if}
{/if}