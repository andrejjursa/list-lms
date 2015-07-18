{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_solutions_upload_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_solutions_upload_page_title'}</h2>
    {if $solution->exists()}
        <h3>{$solution->student_fullname} / {overlay table='task_sets' column='name' table_id=$solution->task_set_id default=$solution->task_set_name} / {translate_text text=$solution->task_set_course_name} / {translate_text text=$solution->task_set_course_period_name}{if !is_null($solution->group_id)} / {translate_text text=$solution->group_name}{/if}</h3>
    {/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $solution->exists()}
        <fieldset>
            <legend>{translate line='admin_solutions_upload_fieldset_legend_form'}</legend>
            <form action="{internal_url url="admin_solutions/do_upload_student_solution/{$solution->id|intval}"}" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label for="upload_id" class="required">{translate line='admin_solutions_upload_form_label_upload'}:</label>
                    <p class="input"><input type="file" name="upload" id="upload_id" /></p>
                    {if $file_error_message}
                    <p class="error"><span class="message">{translate_text text=$file_error_message}</span></p>
                    {/if}
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_solutions_upload_form_button_submit'}" class="button" />
                </div>
            </form>
        </fieldset>
        
        <fieldset>
            <legend>{translate line='admin_solutions_upload_fieldset_legend_files'}</legend>
            {$task_set = $solution->task_set->get()}{$files = $task_set->get_student_files($solution->student_id|intval)}
            <ol id="solution_files_list_id">
                {foreach $files as $file}
                <li><a href="{internal_url url="tasks/download_solution/{$solution->task_set_id|intval}/{$file.file|encode_for_url}"}" target="_blank">{$file.file_name}_{$file.version}</a></li>
                {/foreach}
            </ol>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_upload_solution_not_found' inline}
    {/if}
{/block}