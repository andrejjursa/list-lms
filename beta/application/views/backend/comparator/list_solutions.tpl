{if $course->exists() and $task_set->exists()}
    {if $solutions->exists() and $solutions->result_count() gt 0}
        <form action="{internal_url url='admin_comparator/run_comparation'}" method="post" class="run_configuration_form">
            <table class="solutions_list_table">
                <thead>
                    <tr>
                        <th class="selection"></th>
                        <th class="fullname">{translate line='admin_comparator_list_solutions_table_header_student_name'}</th>
                        <th class="version">{translate line='admin_comparator_list_solutions_table_header_solution_version'}</th>
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
                            <strong class="no_files">{translate line='admin_comparator_list_solutions_table_body_no_files'}</strong>
                            {/if}
                            <input type="hidden" value="{$solution->student_id|intval}" name="solutions[{$solution->id|intval}][student]" />
                        </td>
                    </tr>    
                    {/foreach}
                </tbody>
            </table>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_threshold'}:</label>
                <div class="input"><input type="text" name="comparator_setup[threshold]" value="{$smarty.post.comparator_setup.threshold|default:'0.76'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_threshold_hint'}</em></p>
                {form_error field='comparator_setup[threshold]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_min_tree_size'}:</label>
                <div class="input"><input type="text" name="comparator_setup[min_tree_size]" value="{$smarty.post.comparator_setup.min_tree_size|default:'6'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_min_tree_size_hint'}</em></p>
                {form_error field='comparator_setup[min_tree_size]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_max_cutted_tree_size'}:</label>
                <div class="input"><input type="text" name="comparator_setup[max_cutted_tree_size]" value="{$smarty.post.comparator_setup.max_cutted_tree_size|default:'10'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_max_cutted_tree_size_hint'}</em></p>
                {form_error field='comparator_setup[max_cutted_tree_size]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_branching_factor'}:</label>
                <div class="input"><input type="text" name="comparator_setup[branching_factor]" value="{$smarty.post.comparator_setup.branching_factor|default:'6000000'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_branching_factor_hint'}</em></p>
                {form_error field='comparator_setup[branching_factor]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_minimum_similarity'}:</label>
                <div class="input"><input type="text" name="comparator_setup[min_similarity]" value="{$smarty.post.comparator_setup.min_similarity|default:'0.3'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_minimum_similarity_hint'}</em></p>
                {form_error field='comparator_setup[min_similarity]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_timeout'}:</label>
                <div class="input"><input type="text" name="comparator_setup[timeout]" value="{$smarty.post.comparator_setup.timeout|default:'5'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_timeout_hint'}</em></p>
                {form_error field='comparator_setup[timeout]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" value="{translate line='admin_comparator_list_solutions_form_button_submit'}" class="button" />
                <input type="hidden" name="task_sets_setup[course]" value="{$course->id}" />
                <input type="hidden" name="task_sets_setup[task_set]" value="{$task_set->id}" />
            </div>
        </form>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_comparator_list_solutions_error_no_solutions' inline}
    {/if}
{else}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_comparator_list_solutions_error_course_task_set' inline}
{/if}