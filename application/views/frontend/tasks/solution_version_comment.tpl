{extends file='layouts/frontend_popup.tpl'}
{block main_content}
  {if is_object($solution_version) && $solution_version->exists()}
    <h3>{overlay table='task_sets' table_id=$solution_version->solution_task_set_id column='name' default=$solution_version->solution_task_set_name} | {$solution_version->solution_student_fullname} | {$solution_version->version}. {translate line="tasks_solution_version_version"}</h3>

    {include file='partials/frontend_general/flash_messages.tpl' inline}

    <fieldset class="basefieldset">
      <form action="{internal_url url="tasks/save_solution_version_comment/{$solution_version->id|intval}"}" method="post">
        <div class="field">
            <label for="comment_id">{translate line='tasks_task_form_label_comment'}:</label>
            <p class="input"><textarea name="comment" id="comment_id">{$solution_version->comment|escape:'html'}</textarea></p>
            <p class="input"><em>{translate line='tasks_task_form_label_comment_hint'}</em></p>
        </div>
        <div class="buttons">
            <input type="submit" name="submit_button" value="{translate line='tasks_task_form_submit_comment'}" class="button" />
        </div>
      </form>
    </fieldset>
  {else}
    {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_solution_version_not_found' inline}
  {/if}
{/block}
