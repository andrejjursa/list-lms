{include file='partials/backend_general/flash_messages.tpl' inline}
<div class="field">
    <label for="solution_student_id_id" class="required">{translate line='admin_solutions_list_form_label_student'}:</label>
    <p class="input"><select name="solution[student_id]" size="1" id="solution_student_id_id">{html_options options=$students selected=$smarty.post.solution.student_id|intval}</select></p>
    {form_error field='solution[student_id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="solution_points_id" class="required">{translate line='admin_solutions_list_form_label_points'}:</label>
    <p class="input"><input type="text" name="solution[points]" value="{$smarty.post.solution.points|escape:'html'}" id="solution_points_id" /></p>
    <p class="input"><em>{translate|sprintf:{$task_set->task_set_total_points|floatval} line='admin_solutions_list_form_label_points_hint'}</em></p>
    {form_error field='solution[points]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="field">
    <label for="solution_comment_id">{translate line='admin_solutions_list_form_label_comment'}:</label>
    <p class="input"><textarea name="solution[comment]" id="solution_comment_id">{$smarty.post.solution.comment|escape:'html'}</textarea></p>
    {form_error field='solution[comment]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
</div>
<div class="buttons">
    <input type="submit" name="submit_button" value="{translate line='admin_solutions_list_form_submit_button'}" class="button" />
</div>