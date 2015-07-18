{if $moss_enabled}
    {if $course->exists() and $task_set->exists()}
        {if $solutions->exists() and $solutions->result_count() gt 0}
            <form action="{internal_url url='admin_moss/run_comparation'}" method="post" class="run_configuration_form">
                <table class="solutions_list_table">
                    <thead>
                        <tr>
                            <th class="selection"></th>
                            <th class="fullname">{translate line='admin_moss_list_solutions_table_header_student_name'}</th>
                            <th class="version">{translate line='admin_moss_list_solutions_table_header_solution_version'}</th>
                        </tr>
                    </thead>
                    {form_error field='solutions' left_delimiter='<tfoot><tr><td colspan="3"><div class="flash_message message_error">' right_delimiter='</div></td></tr></tfoot>'}
                    <tbody>
                        {foreach $solutions as $solution}
                        <tr>{$student_files = $task_set->get_student_files($solution->student_id)}
                            <td class="selection">{if $student_files|count gt 0}<input type="checkbox" name="solutions[{$solution->id|intval}][selected]" value="1" {if !isset($smarty.post.solutions) or $smarty.post.solutions[$solution->id|intval].selected eq 1}checked="checked" {/if}/>{/if}</td>
                            <td class="fullname">{$solution->student_fullname}</td>
                            <td class="version">{if $student_files|count gt 0}
                                <select name="solutions[{$solution->id|intval}][version]" size="1">
                                    {foreach $student_files as $file}
                                        {$selected_file = ''}
                                        {if $smarty.post.solutions[$solution->id|intval].version eq $file.version or !$smarty.post.solutions and (($solution->best_version eq $file.version) or ($solution->best_version eq 0 and $file@last))}
                                            {$selected_file = ' selected="selected"'}
                                        {/if}
                                    <option value="{$file.version}"{$selected_file}>{$file.version}{if $solution->best_version eq $file.version} (*){/if}</option>
                                    {/foreach}
                                </select>
                                {else}
                                <strong class="no_files">{translate line='admin_moss_list_solutions_table_body_no_files'}</strong>
                                {/if}
                                <input type="hidden" value="{$solution->student_id|intval}" name="solutions[{$solution->id|intval}][student]" />
                            </td>
                        </tr>    
                        {/foreach}
                    </tbody>
                </table>
                <table class="base_files_table">
                    <thead>
                        <tr>
                            <th class="selection"></th>
                            <th class="filename">{translate line='admin_moss_list_base_files_table_header_base_file_name'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $base_files_list as $base_file_for_task}
                        <tr class="task_header">
                            <td colspan="2">{$base_file_for_task.task_name}</td>
                        </tr>
                            {foreach $base_file_for_task.files as $file_path => $file_name}
                            <tr class="file">
                                <td class="selection"><input type="checkbox" name="moss_base_files[{$base_file_for_task.task_id}][{$file_path|sha1}]" value="{$file_path}"{if $smarty.post.moss_base_files[$base_file_for_task.task_id][$file_path|sha1] eq $file_path} checked="checked"{/if} /></td>
                                <td class="filename">{$file_name}</td>
                            </tr>
                            {foreachelse}
                            <tr class="no_files">
                                <td colspan="2">
                                    {translate line='admin_moss_base_files_table_body_no_files_for_task'}
                                </td>
                            </tr>
                            {/foreach}
                        {/foreach}
                    </tbody>
                </table>
                <div class="field">
                    <label class="required">{translate line='admin_moss_list_solutions_form_label_language'}:</label>
                    <div class="input">
                        <select name="moss_setup[l]" size="1">
                            <option></option>
                            {html_options options=$languages selected=$smarty.post.moss_setup.l}
                        </select>
                    </div>
                    {form_error field='moss_setup[l]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label class="required">{translate line='admin_moss_list_solutions_form_label_sensitivity'}:</label>
                    <div class="input">
                        <input type="text" name="moss_setup[m]" value="{$smarty.post.moss_setup.m|default:10}">
                    </div>
                    <p class="input"><em>{translate line='admin_moss_list_solutions_form_label_sensitivity_hint'}</em></p>
                    {form_error field='moss_setup[m]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label class="required">{translate line='admin_moss_list_solutions_form_label_matching_files'}:</label>
                    <div class="input">
                        <input type="text" name="moss_setup[n]" value="{$smarty.post.moss_setup.n|default:250}">
                    </div>
                    <p class="input"><em>{translate line='admin_moss_list_solutions_form_label_matching_files_hint'}</em></p>
                    {form_error field='moss_setup[n]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" value="{translate line='admin_moss_list_solutions_form_button_submit'}" class="button" />
                    <input type="hidden" name="task_sets_setup[course]" value="{$course->id}" />
                    <input type="hidden" name="task_sets_setup[task_set]" value="{$task_set->id}" />
                </div>
            </form>
        {else}
            {include file='partials/backend_general/error_box.tpl' message='lang:admin_moss_list_solutions_error_no_solutions' inline}
        {/if}
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_moss_list_solutions_error_course_task_set' inline}
    {/if}
{else}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_moss_general_error_user_id_not_set' inline}
{/if}