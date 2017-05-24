{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_solutions_batch_valuation_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_solutions_batch_valuation_page_title'}</h2>
    {if $task_set->exists()}<h3>{overlay table='task_sets' column='name' table_id=$task_set->id default=$task_set->name} / {translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}{if $task_set->group_name} / {translate_text text=$task_set->group_name}{/if}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl'}
    <fieldset>
        <a href="{internal_url url='admin_solutions'}" class="button special">{translate line='common_button_back'}</a>
    </fieldset>
    {if $task_set->exists()}
        <fieldset>
            <legend>{translate line="admin_solutions_batch_valuation_fieldset_legend_table"}</legend>
            <div class="filter_wrap">
                <form action="{internal_url url="admin_solutions/batch_valuation_list/{$task_set->id|intval}"}" method="post" id="filter_form_id">
                    <div class="field">
                        <label>{translate line='admin_solutions_filter_label_group'}:</label>
                        <p class="input"><select name="filter[group]" size="1" id="filter_group_id">{list_html_options options=$possible_groups selected=$filter.group}</select></p>
                    </div>
                    <div class="buttons">
                        <input type="submit" name="filter_submit" value="{translate line='admin_solutions_filter_submit'}" class="button" />
                    </div>
                </form>
            </div>
            <form action="{internal_url url="admin_solutions/batch_save_solutions/{$task_set->id}"}" method="post" id="batch_valuation_form_id"></form>
        </fieldset>
        <fieldset>
            <legend>{translate line="admin_solutions_list_fieldset_legend_valuation_charts"}</legend>
            <div id="valuationCharts" style="height: 500px;"></div>
            <form id="histogramForm">
                <div class="field">
                    <label>{translate line='admin_solutions_histogram_bin_size'}:</label>
                    <p class="input">
                        <select name="histogram[size]" size="1" id="histogram_size_id">
                            <option value="0.25">0.25</option>
                            <option value="0.5" selected="selected">0.5</option>
                            <option value="0.75">0.75</option>
                            <option value="1.0">1.0</option>
                            <option value="1.25">1.25</option>
                            <option value="1.5">1.5</option>
                            <option value="1.75">1.75</option>
                            <option value="2.0">2.0</option>
                        </select>
                    </p>
                </div>
                <div class="field">
                    <label>{translate line='admin_solutions_histogram_hints'}:</label>
                    <p class="input">
                        <em>{translate line='admin_solutions_histogram_hint_text'}</em>
                    </p>
                </div>
            </form>
        </fieldset>
        <fieldset>
            <legend>{translate line="admin_solutions_batch_valuation_fieldset_legend_task_set_content"}</legend>
            <div id="task_set_content_id"></div>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_list_task_set_not_found' back_url={internal_url url='admin_solutions'} inline}
    {/if}
{/block}
{block custom_head}<script type="text/javascript">
    var task_set_id = {$task_set->id|intval};
    var chartmessages = {
        'chartTitle': '{translate line="admin_solutions_list_chart_title"}',
        'xAxis': '{translate line="admin_solutions_list_chart_xaxis"}',
        'yAxis': '{translate line="admin_solutions_list_chart_yaxis"}',
        'to': '{translate line="admin_solutions_list_chart_to"}',
        'range': '{translate line="admin_solutions_list_chart_tootlip_range"}',
        'sum': '{translate line="admin_solutions_list_chart_tootlip_sum"}',
        'subtitle': '{overlay|default:'' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}',
        'mean': '{translate line="admin_solutions_list_chart_mean"}',
        'sd': '{translate line="admin_solutions_list_chart_sd"}',
        'pointseries': {
            'name': '{translate line="admin_solutions_list_chart_pointseries_name"}',
            'x': '{translate line="admin_solutions_list_chart_pointseries_tooltip_x"}',
            'y': '{translate line="admin_solutions_list_chart_pointseries_tooltip_y"}'
        }
    };
</script>{/block}