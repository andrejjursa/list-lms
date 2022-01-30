{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_parallel_moss_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_parallel_moss_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $moss_enabled}
        <fieldset>

        </fieldset>

        <fieldset>
            <legend>{translate line='admin_parallel_moss_all_planed_and_finished_comparisons'}</legend>
            <div class="filter_wrap">
                <form action="{internal_url url='admin_parallel_moss/get_comparisons'}" method="post" id="filter_form_id">

                </form>
            </div>
            <table class="comparisons_table" id="comparisons_table_id">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Started at</th>
                        <th>Finished at</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr></tr>
                </tfoot>
                <tbody></tbody>
            </table>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_parallel_moss_general_error_user_id_not_set' inline}
    {/if}
{/block}