{extends file='layouts/backend_popup.tpl'}
{block title}{translate|sprintf:{overlay table='tasks' table_id=$task->id column='name' default=$task->name} line='admin_tasks_add_to_task_set_page_title'}{/block}
{block main_content}
    <h3>{translate|sprintf:{overlay table='tasks' table_id=$task->id column='name' default=$task->name} line='admin_tasks_add_to_task_set_page_title'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if !$task_set->exists()}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_tasks_add_to_task_set_nothing_opened' inline}
    {elseif !$task->exists()}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_tasks_error_message_task_not_found' inline}
    {else}
    <fieldset>
        <form action="{internal_url url='admin_tasks/insert_to_task_set'}" method="post">
            <div class="field">
                <label>{translate line='admin_tasks_add_to_task_set_form_label_selected_task_set'}:</label>
                <p class="input">{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} / {translate_text text=$task_set->course->get()->name} / {translate_text text=$task_set->course->period->get()->name}</p>
            </div>
            {if $task_set->content_type eq 'task_set'}
            <div class="field">
                <label for="points_total_id" class="required">{translate line='admin_tasks_add_to_task_set_form_label_points_total'}:</label>
                <p class="input"><input type="text" name="points_total" value="{$smarty.post.points_total|floatval}" id="points_total_id" /></p>
                {form_error field='points_total' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="test_max_points_id" class="required">{translate line='admin_tasks_add_to_task_set_form_label_test_max_points'}:</label>
                <p class="input"><input type="text" name="test_max_points" value="{$smarty.post.test_max_points|floatval}" id="test_max_points_id" /></p>
                {form_error field='test_max_points' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="test_min_points_id" class="required">{translate line='admin_tasks_add_to_task_set_form_label_test_min_points'}:</label>
                <p class="input"><input type="text" name="test_min_points" value="{$smarty.post.test_min_points|floatval}" id="test_min_points_id" /></p>
                {form_error field='test_min_points' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="bonus_task_id">{translate line='admin_tasks_add_to_task_set_form_label_bonus_task'}:</label>
                <p class="input"><input type="checkbox" name="bonus_task" value="1" id="bonus_task_id"{if $smarty.post.bonus_task} checked="checked"{/if} /></p>
                <p class="input"><em>{translate line='admin_tasks_add_to_task_set_form_label_bonus_task_hint'}</em></p>
            </div>
            {/if}
            {if $task_set->content_type eq 'project'}
            <div class="field">
                <label for="max_projects_selections_id" class="required">{translate line='admin_tasks_add_to_task_set_form_label_max_projects_selections'}:</label>
                <p class="input"><input type="text" name="max_projects_selections" value="{$smarty.post.max_projects_selections|floatval}" id="max_projects_selections_id" /></p>
                {form_error field='max_projects_selections' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>    
            {/if}
            <div class="field">
                <label for="internal_comment_id">{translate line='admin_tasks_add_to_task_set_form_label_internal_comment'}:</label>
                <div class="input">
                    <textarea name="internal_comment" id="internal_comment_id">{$smarty.post.internal_comment|escape:'html'}</textarea>
                </div>
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_tasks_add_to_task_set_form_button_submit'}" class="button" />
                <input type="hidden" name="task_id" value="{$task->id|intval}" />
                <input type="hidden" name="task_set_id" value="{$task_set->id|intval}" />
            </div>
        </form>
    </fieldset>
    <fieldset>
        <legend>{overlay table='tasks' table_id=$task->id column='name' default=$task->name}</legend>
        <div class="text_content">
        {overlay|add_base_url table='tasks' table_id=$task->id column='text' default=$task->text}
        </div>
    </fieldset>
    {/if}
{/block}