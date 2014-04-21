{if $course->exists() and $task_set->exists()}
    {if $solutions->exists() and $solutions->result_count() gt 0}
        <form action="{internal_url url="admin_comparator/run_comparation/{$task_set->id|intval}"}" method="post">
            <table class="solutions_list_table">
                <thead>
                    <tr>
                        <th></th>
                        <th>{translate line='admin_comparator_list_solutions_table_header_student_name'}</th>
                        <th>{translate line='admin_comparator_list_solutions_table_header_solution_version'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $solutions as $solution}
                    <tr>
                        <td><input type="checkbox" name="solutions[{$solution->id|intval}][selected]" value="1" checked="{if !$smarty.post.solutions or $smarty.post.solutions[$solution->id|intval].selected eq 1}checked{/if}" /></td>
                        <td>{$solution->student_fullname}</td>
                        <td>
                            <select name="solutions[{$solution->id|intval}][version]" size="1">
                                {foreach $task_set->get_student_files($solution->student_id) as $file}
                                    {$selected_file = ''}
                                    {if $smarty.post.solutions[$solution->id|intval].version eq $file.version or !$smarty.post.solutions and (($solution->best_version eq $file.version) or ($solution->best_version eq 0 and $file@last))}
                                        {$selected_file = ' selected="selected"'}
                                    {/if}
                                <option value="{$file.version}"{$selected_file}>{$file.version}{if $solution->best_version eq $file.version} (*){/if}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>    
                    {/foreach}
                </tbody>
            </table>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_threshold'}:</label>
                <div class="input"><input type="text" name="comparator_setup[threshold]" value="{$smarty.post.comparator_setup.threshold|default:'0.76'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_threshold_hint'}</em></p>
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_min_tree_size'}:</label>
                <div class="input"><input type="text" name="comparator_setup[min_tree_size]" value="{$smarty.post.comparator_setup.min_tree_size|default:'6'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_min_tree_size_hint'}</em></p>
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_max_cutted_tree_size'}:</label>
                <div class="input"><input type="text" name="comparator_setup[max_cutted_tree_size]" value="{$smarty.post.comparator_setup.max_cutted_tree_size|default:'10'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_max_cutted_tree_size_hint'}</em></p>
            </div>
            <div class="field">
                <label>{translate line='admin_comparator_list_solutions_form_label_branching_factor'}:</label>
                <div class="input"><input type="text" name="comparator_setup[branching_factor]" value="{$smarty.post.comparator_setup.branching_factor|default:'6000000'|escape:'html'}" /></div>
                <p class="input"><em>{translate line='admin_comparator_list_solutions_form_label_branching_factor_hint'}</em></p>
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