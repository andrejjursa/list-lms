{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_parallel_moss_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_parallel_moss_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $moss_enabled}
        <fieldset>
            <a href="{internal_url url='admin_parallel_moss/new_comparison'}" class="button">
                {translate line='admin_parallel_moss_new_comparison_button_label'}
            </a>
        </fieldset>

        <fieldset>
            <legend>{translate line='admin_parallel_moss_all_planed_and_finished_comparisons'}</legend>
            <div class="filter_wrap">
                <form action="{internal_url url='admin_parallel_moss/get_comparisons'}" method="post" id="filter_form_id">

                </form>
            </div>
            <table class="comparisons_table" id="comparisons_table_id"
               data-lang_comparison_status_queued="{translate|escape:'html' line='admin_parallel_moss_comparison_status_queued'}"
               data-lang_comparison_status_processing="{translate|escape:'html' line='admin_parallel_moss_comparison_status_processing'}"
               data-lang_comparison_status_finished="{translate|escape:'html' line='admin_parallel_moss_comparison_status_finished'}"
               data-lang_comparison_status_failed="{translate|escape:'html' line='admin_parallel_moss_comparison_status_failed'}"
               data-lang_comparison_action_pending="{translate|escape:'html' line='admin_parallel_moss_comparison_action_pending'}"
               data-lang_comparison_action_waiting="{translate|escape:'html' line='admin_parallel_moss_comparison_action_waiting'}"
               data-lang_comparison_requeue_queued="{translate|escape:'html' line='admin_parallel_moss_comparison_requeue_queued'}"
               data-lang_comparison_requeue_notFound="{translate|escape:'html' line='admin_parallel_moss_comparison_requeue_not_found'}"
               data-lang_comparison_requeue_invalidStatus="{translate|escape:'html' line='admin_parallel_moss_comparison_requeue_invalid_status'}"
               data-link_requeue="{internal_url url='admin_parallel_moss/requeue_comparison'}"
            >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{translate line='admin_parallel_moss_comparisons_table_header_comparison_name'}</th>
                        <th>{translate line='admin_parallel_moss_comparisons_table_header_teacher'}</th>
                        <th>{translate line='admin_parallel_moss_comparisons_table_header_status'}</th>
                        <th>{translate line='admin_parallel_moss_comparisons_table_header_started_at'}</th>
                        <th>{translate line='admin_parallel_moss_comparisons_table_header_finished_at'}</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot id="table_pagination_footer_id">
                    <tr>
                        <td colspan="8">{include file='partials/backend_general/pagination.tpl'}</td>
                    </tr>
                </tfoot>
                <tbody></tbody>
            </table>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_parallel_moss_general_error_user_id_not_set' inline}
    {/if}
{/block}