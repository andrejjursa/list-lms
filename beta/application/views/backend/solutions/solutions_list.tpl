{extends file='layouts/backend.tpl'}
{block title}{translate|sprintf:{overlay|default:'' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} line='admin_solutions_list_page_title'}{/block}
{block main_content}
    <h2>{translate|sprintf:{overlay|default:'' table='task_sets' table_id=$task_set->id column='name' default=$task_set->name} line='admin_solutions_list_page_title'}</h2>
    {if $task_set->exists()}<h3>{translate_text text=$task_set->course_name} / {translate_text text=$task_set->course_period_name} / {if $task_set->group_name}{translate_text text=$task_set->group_name}{else}{translate line='admin_solutions_list_h3_all_groups'}{/if}</h3>{/if}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <a href="{internal_url url='admin_solutions'}" class="button special">{translate line='common_button_back'}</a>
    </fieldset>
    {if $task_set->exists()}
        <fieldset>
            <legend>{translate line='admin_solutions_list_fieldset_legend_add_solution_record'}</legend>
            <form action="{internal_url url="admin_solutions/create_solution/{$task_set->id|intval}"}" method="post" id="new_solution_form_id">
                {include file='backend/solutions/new_solution_form.tpl' inline}
            </form>
        </fieldset>
        <fieldset>
            <legend>{translate line='admin_solutions_list_fieldset_legend_all_solutions'}</legend>
            <table class="solutions_table">
                <thead>
                    <tr>
                        <th rowspan="2" class="left_corner">ID</th>
                        <th rowspan="2">{translate line='common_table_header_created'}</th>
                        <th rowspan="2">{translate line='common_table_header_updated'}</th>
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
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_solutions_list_task_set_not_found' back_url={internal_url url='admin_solutions'} inline}     
    {/if}
{/block}
{block custom_head}
<script type="text/javascript">
    var task_set_id = {$task_set->id|intval};
</script>
{/block}