{$allowed_status = [0 => {translate line='admin_participants_column_allowed_0'}, 1 => {translate line='admin_participants_column_allowed_1'}]}
{foreach $participants as $participant}
<tr>
    <td>{$participant->id|intval}</td>
    <td>{$participant->student_fullname} ({$participant->student_email})</td>
    <td><span title="{translate_text text=$participant->course_name default={translate line='admin_participants_column_empty_message'}}">{translate_text|abbreviation text=$participant->course_name default={translate line='admin_participants_column_empty_message'}}</span> / <span title="{translate_text text=$participant->course_period_name default={translate line='admin_participants_column_empty_message'}}">{translate_text|abbreviation text=$participant->course_period_name default={translate line='admin_participants_column_empty_message'}}</span></td>
    <td class="group_column">
        {include file='backend/participants/group_column.tpl' inline}
    </td>
    <td>{$allowed_status[$participant->allowed|intval]}</td>
    {if $participant->allowed eq 0}
        <td class="controlls"><a href="{internal_url url="admin_participants/approve_participation/participant_id/{$participant->id}"}" class="button special participation_approve" title="{translate line='admin_participants_table_button_approve'}"><span class="list-icon list-icon-approve"></span></a></td>
        <td class="controlls"><a href="{internal_url url="admin_participants/disapprove_participation/participant_id/{$participant->id}"}" class="button delete participation_disapprove" title="{translate line='admin_participants_table_button_disapprove'}"><span class="list-icon list-icon-disapprove"></span></a></td>
    {else}
        <td class="controlls"><a href="{internal_url url="admin_participants/delete_participation/participant_id/{$participant->id}"}" class="button delete participation_delete" title="{translate line='admin_participants_table_button_blow'}"><span class="list-icon list-icon-delete"></span></a></td>
	<td class="controlls"><a href="{internal_url url="admin_students/log_in_as_student/student_id/{$participant->student_id}"}" class="button special login_button" target="_blank" title="{translate line='admin_students_table_button_login_as'}"><span class="list-icon list-icon-login-as"></span></a></td>
    {/if}
</tr>
{foreachelse}
<tr>
    <td colspan="6">{include file='partials/backend_general/error_box.tpl' message='lang:admin_participants_message_no_participants_found'}</td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="7">{include file='partials/backend_general/pagination.tpl' paged=$participants->paged inline}</td>
</tr>
