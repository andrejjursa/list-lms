{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tasks_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_tasks_fieldset_legend_all_tasks'}</legend>
        <table>
            <thead>
                <tr>
                    <th>{translate line='admin_tasks_table_header_name'}</th>
                    <th>{translate line='admin_tasks_table_header_categories'}</th>
                    <th>{translate line='admin_tasks_table_header_task_sets'}</th>
                </tr>
            </thead>
            <tfoot id="table_pagination_footer_id">
            </tfoot>
            <tbody id="table_content_id">
            </tbody>
        </table>
    </fieldset>
{/block}