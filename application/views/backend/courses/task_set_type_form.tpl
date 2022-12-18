{include file='partials/backend_general/flash_messages.tpl' inline}

{if $smarty.post}
    {assign 'task_set_type_id' $smarty.post.task_set_type.id}
    {assign 'join_upload_solution' $smarty.post.task_set_type.join_upload_solution}
    {assign 'join_min_points' $smarty.post.task_set_type.join_min_points}
    {assign 'join_min_points_in_percentage' $smarty.post.task_set_type.join_min_points_in_percentage}
    {assign 'join_include_in_total' $smarty.post.task_set_type.join_include_in_total}
    {assign 'join_virtual' $smarty.post.task_set_type.join_virtual}
    {assign 'join_formula' $smarty.post.task_set_type.join_formula}
{else}
    {assign 'task_set_type_id' $task_set_type->id}
    {assign 'join_upload_solution' $task_set_type->join_upload_solution}
    {assign 'join_min_points' $task_set_type->join_min_points}
    {assign 'join_min_points_in_percentage' $task_set_type->join_min_points_in_percentage}
    {assign 'join_include_in_total' $task_set_type->join_include_in_total}
    {assign 'join_virtual' $task_set_type->join_virtual}
    {assign 'join_formula' $task_set_type->join_formula}
{/if}

{if $edit} 
    <input name="task_set_type[id]" id="task_set_type_id_id" value={$task_set_type_id|intval} hidden />
{else}
    <div class="field">
        <label for="task_set_type_id_id">{translate line='admin_courses_form_label_task_set_type_name'}:</label>
        <p class="input">
            <select name="task_set_type[id]" size="1" id="task_set_type_id_id">
                {list_html_options options=$task_set_types selected=$task_set_type_id|intval}
            </select>
            {form_error field='task_set_type[id]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        </p>
    </div>
{/if}
<div class="field" id="task_set_type_join_upload_solution_field_id" {if $smarty.post.task_set_type.join_virtual|intval == 1}style="display:none"{/if}>
    <label for="task_set_type_join_upload_solution_id">{translate line='admin_courses_form_label_upload_solution'}:</label>
    <p class="input">
        <select name="task_set_type[join_upload_solution]" size="1" id="task_set_type_join_upload_solution_id">
            {list_html_options options=[1 => {translate line='admin_courses_form_select_option_yes'}, 0 => {translate line='admin_courses_form_select_option_no'}]
             selected=$join_upload_solution|default:1|intval}
        </select>
        {form_error field='task_set_type[join_upload_solution]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="field">
    <label for="task_set_type_join_min_points_id">{translate line='admin_courses_form_label_min_points'}:</label>
    <p class="input">
        <input type="text" name="task_set_type[join_min_points]" value="{$join_min_points}"
            id="task_set_type_join_min_points_id" />
        {form_error field='task_set_type[join_min_points]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="field">
    <label class="required"
        for="task_set_type_join_min_points_in_percentage_id">{translate line='admin_courses_form_label_min_points_in_percentage'}:</label>
    <p class="input">
        <select name="task_set_type[join_min_points_in_percentage]" size="1"
            id="task_set_type_join_min_points_in_percentage_id">
            {list_html_options options=[1 => {translate line='admin_courses_form_select_option_yes'},
            0 => {translate line='admin_courses_form_select_option_no'}]
            selected=$join_min_points_in_percentage|default:1|intval}
        </select>
        {form_error field='task_set_type[join_min_points_in_percentage]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="field">
    <label class="required"
        for="task_set_type_join_include_in_total_id">{translate line='admin_courses_form_label_include_in_total'}:</label>
    <p class="input">
        <select name="task_set_type[join_include_in_total]" size="1" id="task_set_type_join_include_in_total_id">
            {list_html_options options=[1 => {translate line='admin_courses_form_select_option_yes'}, 0
            => {translate line='admin_courses_form_select_option_no'}]
            selected=$join_include_in_total|default:1|intval}
        </select>
        {form_error field='task_set_type[join_include_in_total]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="field">
    <label class="required"
        for="task_set_type_join_virtual_id">{translate line='admin_courses_form_label_virtual'}:</label>
    <p class="input">
        <select name="task_set_type[join_virtual]" size="1" id="task_set_type_join_virtual_id">
            {list_html_options options=[1 => {translate line='admin_courses_form_select_option_yes'}, 0
            => {translate line='admin_courses_form_select_option_no'}]
            selected=$join_virtual|default:0|intval}
        </select>
        {form_error field='task_set_type[join_virtual]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="field" id="task_set_type_join_formula_field_id" {if $smarty.post.task_set_type.join_virtual|intval != 1}style="display:none"{/if}>
    <label for="task_set_type_join_formula_id">{translate line='admin_courses_form_label_formula'}:</label>
    <p class="input">
        <textarea name="task_set_type[join_formula]" id="task_set_type_join_formula_id"
            class="tinymce">{$join_formula}</textarea>
        {form_error field='task_set_type[join_formula]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
        {form_error field='task_set_type[join_formula_object]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
    </p>
</div>
<div class="buttons">
{if $edit}
    <input type="submit" value="{translate line='admin_courses_form_task_set_type_button_edit'}" name="submit_button" class="button" />
    <a href="{internal_url url="admin_courses/task_set_types/course_id/{$course->id}"}" class="button special">{translate line='common_button_back'}</a>
{else}
    <input type="submit" value="{translate line='admin_courses_form_task_set_type_button_add'}" name="submit_button" class="button" />
    <a href="{internal_url url="admin_courses"}" class="button special">{translate line='common_button_back'}</a>
{/if}
</div>