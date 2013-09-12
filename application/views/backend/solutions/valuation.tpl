{extends file='layouts/backend_popup.tpl'}
{block title}{/block}
{block main_content}
    {if $solution->exists()}
        <h3>{translate_text text=$solution->task_set->name} / {translate_text text=$solution->task_set_course_name} / {translate_text text=$solution->task_set_course_period_name}{if $solution->task_set_group_name} / {translate_text text=$solution->task_set_group_name}{/if}</h3>
        <h4>{$solution->student_fullname} ({$solution->student_email})</h4>
        {include file='partials/backend_general/flash_messages.tpl' inline}
        <div id="tabs">
            <ul>
                <li><a href="#tabs-form">{translate line='admin_solutions_valuation_tabs_label_form'}</a></li>
                <li><a href="#tabs-files">{translate line='admin_solutions_valuation_tabs_label_files'}</a></li>
                <li><a href="{internal_url url="admin_solutions/display_tasks_list/{$solution->task_set->id}"}">{translate line='admin_solutions_valuation_tabs_label_tasks'}</a></li>
            </ul>
            <div id="tabs-form">
                <form action="{internal_url url="admin_solutions/update_valuation/{$solution->task_set->id|intval}/{$solution->id|intval}"}" method="post">
                    <div class="field">
                        <label for="solution_points_id" class="required">{translate line='admin_solutions_valuation_form_label_points'}:</label>
                        <p class="input"><input type="text" name="solution[points]" value="{$smarty.post.solution.points|default:$solution->points|escape:'html'}" id="solution_points_id" /></p>{capture name='total_points' assign='total_points'}{if !is_null($solution->task_set->points_override)}{$solution->task_set->points_override}{else}{$solution->task_set_total_points}{/if}{/capture}
                        <p class="input"><em>{translate|sprintf:{$total_points|floatval} line='admin_solutions_valuation_form_label_points_hint'}</em></p>
                        {form_error field='solution[points]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="solution_comment_id">{translate line='admin_solutions_valuation_form_label_comment'}:</label>
                        <p class="input"><textarea name="solution[comment]" id="solution_comment_id">{$smarty.post.solution.comment|default:$solution->comment|escape:'html'}</textarea></p>
                        {form_error field='solution[comment]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                    </div>
                    <div class="field">
                        <label for="solution_not_considered_id">{translate line='admin_solutions_valuation_form_label_not_considered'}:</label>
                        <p class="input"><input type="checkbox" value="1" name="solution[not_considered]" id="solution_not_considered_id"{if $smarty.post.solution.not_considered|default:$solution->not_considered} checked="checked"{/if} /></p>
                    </div>
                    <div class="buttons">
                        <input type="submit" name="submit_button" value="{translate line='admin_solutions_valuation_form_button_submit'}" class="button" />
                    </div>
                </form>
            </div>
            <div id="tabs-files">
                <div class="filter_wrap">
                    <form action="" method="post" id="filter_form_id">
                        <div class="field">
                            <label>{translate line='admin_solutions_validation_filter_label_version'}:</label>
                            <p class="input">
                                <select name="zip[file]" size="1">
                                    <option value=""></option>
                                    {foreach $solution->task_set->get_student_files($solution->student_id) as $file}
                                        <option value="{$file.file|encode_for_url}">{$file@key}</option>
                                    {/foreach}
                                </select>
                            </p>
                        </div>
                        <div class="buttons download_file_buttons">
                            <input type="button" class="button" name="download_file_button" value="{translate line='admin_solutions_validation_filter_button_download_file'}" />
                        </div>
                        <div class="field select_file">
                            <label>{translate line='admin_solutions_validation_filter_label_file'}:</label>
                            <p class="input"><select name="zip[index]" size="1" id="zip_index_id"></select></p>
                        </div>
                        <div class="buttons read_file_buttons">
                            <input type="button" class="button" name="read_file_button" value="{translate line='admin_solutions_validation_filter_button_read_file'}" />
                        </div>
                    </form>
                </div>
                <div id="file_content_id"></div>
            </div>
        </div>
    {else}
        {include file='partials/backend_general/flash_messages.tpl' inline}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_valuation_solution_not_found' inline}
    {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_set_id = {$solution->task_set->id|intval};
    var solution_id = {$solution->id|intval};
</script>
{/block}