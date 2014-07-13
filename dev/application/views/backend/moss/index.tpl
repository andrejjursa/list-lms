{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_moss_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_moss_page_title'}</h2>
    <fieldset>
        <legend>{translate line='admin_moss_fieldset_legend_task_set'}</legend>
        <form action="{internal_url url='admin_moss/list_solutions'}" method="post" class="task_set_form">
            <div class="field">
                <label class="" for="task_sets_setup_course_id">{translate line='admin_moss_task_set_form_label_course'}:</label>
                <div class="input">
                    <select name="task_sets_setup[course]" size="1" id="task_sets_setup_course_id">
                        <option></option>
                        {list_html_options options=$courses selected=$smarty.post.task_sets_setup.course|default:$filter.course}
                    </select>
                </div>
            </div>
            <div class="field field_task_set_selection_else">
                <label class="required">{translate line='admin_moss_task_set_form_label_task_set'}:</label>
                <p class="input"><em>{translate line='admin_moss_task_set_form_label_task_set_else'}</em></p>
            </div>
            <div class="field field_task_set_selection" style="display: none;">
                <label class="" for="task_sets_setup_task_set_id">{translate line='admin_moss_task_set_form_label_task_set'}:</label>
                <div class="input">
                    <select name="task_sets_setup_task_set_select" size="1" id="task_sets_setup_task_set_id">
                        <option></option>
                    </select>
                    <input type="hidden" name="task_sets_setup[task_set]" value="{$smarty.post.task_sets_setup.task_set|default:$filter.task_set|default:''}" />
                </div>
            </div>
            <div class="buttons">
                <input type="submit" class="button" value="{translate line='admin_moss_task_set_form_button_submit'}" />
                <input type="hidden" name="post_selected_task_set_setup_task_set" value="{$smarty.post.task_sets_setup.task_set|default:$filter.task_set|default:''}" />
            </div>
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_moss_fieldset_legend_protocol'}</legend>
        <div id="solutions_table_content"></div>
    </fieldset>
{/block}
{block custom_head}<script tyle="text/javascript">
    var all_task_sets = {$task_sets|json_encode};
</script>{/block}