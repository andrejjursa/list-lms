{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_tasks_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_tasks_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_tasks_fieldset_legend_all_tasks'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_tasks/get_all_tasks'}" method="post" id="filter_form_id">
                <div class="buttons" style="display: none;">
                    <input type="submit" name="filter_submit" value="{translate line='admin_groups_filter_submit_button'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                </div>
            </form>
        </div>
        <a href="{internal_url url='admin_tasks/new_task'}" class="button">{translate line='admin_tasks_new_task_button_label'}</a>
        <table>
            <thead>
                <tr>
                    <th>{translate line='admin_tasks_table_header_name'}</th>
                    <th>{translate line='admin_tasks_table_header_categories'}</th>
                    <th>{translate line='admin_tasks_table_header_task_sets'}</th>
                    <th colspan="2" class="controlls">{translate line='admin_tasks_table_header_controlls'}</th>
                </tr>
            </thead>
            <tfoot id="table_pagination_footer_id">
            </tfoot>
            <tbody id="table_content_id">
            </tbody>
        </table>
        <pre>{$filter|var_dump}</pre>
    </fieldset>
{/block}