{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_solutions_valuation_tables_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_solutions_valuation_tables_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="" method="post" id="filter_form_id">
            <div class="field">
                <label for="filter_course_id">{translate line='admin_solutions_valuation_tables_filter_label_course'}:</label>
                <div class="input"><select name="filter[course]" id="filter_course_id">{list_html_options options=$courses selected=$filter.course}</select></div>
            </div>
            <div class="group_field_else">
                <input type="hidden" name="filter[group]" value="" />
            </div>
            <div class="field group_field" style="display: none;">
                <label for="filter_group_id">{translate line='admin_solutions_valuation_tables_filter_label_group'}:</label>
                <p class="input"><select name="filter_group" size="1" id="filter_group_id"></select><input type="hidden" name="filter[group]" value="{$filter.group|intval}" /></p>
            </div>
            <div class="field">
                <label for="filter_simple_id">{translate line='admin_solutions_valuation_tables_filter_label_simple'}:</label>
                <p class="input"><input type="checkbox" name="filter[simple]" value="1" id="filter_simple_id"{if $filter.simple} checked="checked"{/if} /></p>
            </div>
            <div class="field">
                <label for="filter_header_repeat_id">{translate line='admin_solutions_valuation_tables_filter_label_header_repeat'}:</label>
                <p class="input">
                    <select name="filter[header_repeat]" size="1" id="filter_header_repeat_id">
                        {list_html_options options=[0 => 'lang:admin_solutions_valuation_tables_header_repeat_off', 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30] selected=$filter.header_repeat|default:20}
                    </select>
                </p>
            </div>
            <div class="buttons">
                <input type="submit" name="filter_submit" value="{translate line='admin_solutions_filter_submit'}" class="button" />
                <input type="hidden" name="filter_selected_group_id" value="{$filter.group|intval}" />
                <input type="hidden" name="filter[order_by_field]" value="{$filter.order_by_field|default:'students'}" />
                <input type="hidden" name="filter[order_by_direction]" value="{$filter.order_by_direction|default:'asc'}" />
            </div>
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_solutions_valuation_tables_table_legend'}</legend>
        <div class="legend">
            <div class="not_submited">{translate|space_to_nbsp line='admin_solutions_valuation_tables_solution_not_submited'}</div>
            <div class="not_this_group">{translate|space_to_nbsp line='admin_solutions_valuation_tables_solution_not_this_group'}</div>
            <div class="not_considered">{translate|space_to_nbsp line='admin_solutions_valuation_tables_solution_not_considered'}</div>
            <div class="not_valuated">{translate|space_to_nbsp line='admin_solutions_valuation_tables_solution_not_valuated'}</div>
        </div>
    </fieldset>
    <fieldset><div id="table_content_id"></div></fieldset>
{/block}