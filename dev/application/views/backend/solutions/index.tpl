{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_solutions_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_solutions_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_solutions_fieldset_legend_task_sets'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_solutions/get_task_set_list'}" method="post" id="filter_form_id">
                <div class="field">
                    <label>{translate line='admin_solutions_filter_label_course'}:</label>
                    <p class="input"><select name="filter[course]">{list_html_options options=$courses selected=$filter.course}</select></p>
                </div>
                <div class="group_field_else">
                    <input type="hidden" name="filter[group]" value="" />
                </div>
                <div class="field group_field" style="display: none;">
                    <label>{translate line='admin_solutions_filter_label_group'}:</label>
                    <p class="input"><select name="filter_group" size="1" id="filter_group_id"></select><input type="hidden" name="filter[group]" value="{$filter.group|intval}" /></p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_solutions_filter_submit'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter_selected_group_id" value="{$filter.group|intval}" />
                </div>
            </form>
        </div>
        <table class="task_sets_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{translate line='admin_solutions_table_header_task_set_name'}</th>
                    <th>{translate line='admin_solutions_table_header_task_set_course'}</th>
                    <th>{translate line='admin_solutions_table_header_task_set_course_group'}</th>
                    <th>{translate line='admin_solutions_table_header_task_set_task_set_type'}</th>
                    <th>{translate line='admin_solutions_table_header_task_set_solution_count'}</th>
                    <th>{translate line='admin_solutions_table_header_task_set_task_count'}</th>
                    <th>{translate line='admin_solutions_table_header_task_set_task_upload_end_time'}</th>
                    <th class="controlls">{translate line='admin_solutions_table_header_controlls'}</th>
                </tr>
            </thead>
            <tfoot id="table_pagination_footer_id">
            </tfoot>
            <tbody id="table_content_id"></tbody>
        </table>
    </fieldset>
{/block}