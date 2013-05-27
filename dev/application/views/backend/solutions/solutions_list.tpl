{extends file='layouts/backend.tpl'}
{block title}{translate|sprintf:{overlay|default:'' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} line='admin_solutions_list_page_title'}{/block}
{block main_content}
    <h2>{translate|sprintf:{overlay|default:'' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} line='admin_solutions_list_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $task_set->exists()}
        <h3>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name}</h3>
        <fieldset>
            <legend>{translate line='admin_solutions_list_fieldset_legend_add_solution_record'}</legend>
        </fieldset>
        <fieldset>
            <legend>{translate line='admin_solutions_list_fieldset_legend_all_solutions'}</legend>
            <table class="solutions_table">
                <thead>
                    <tr>
                        <th rowspan="2" class="left_corner">ID</th>
                        <th rowspan="2">{translate line='admin_solutions_list_table_header_student'}</th>
                        <th rowspan="2">{translate line='admin_solutions_list_table_header_files_count'}</th>
                        <th colspan="3" class="valuation">{translate line='admin_solutions_list_table_header_valuation'}</th>
                        <th rowspan="2" class="controlls right_corner">{translate line='admin_solutions_table_header_controlls'}</th>
                    </tr>
                    <tr>
                        <th>{translate line='admin_solutions_list_table_header_points'}</th>
                        <th>{translate line='admin_solutions_list_table_header_comment'}</th>
                        <th>{translate line='admin_solutions_list_table_header_teacher'}</th>
                    </tr>
                </thead>
                <tbody id="table_content_id"></tbody>
            </table>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_list_task_set_not_found' inline}
    {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_set_id = {$task_set->id|intval};
</script>
{/block}