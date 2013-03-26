{extends file="layouts/backend.tpl"}
{block title}{translate line='admin_courses_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_courses_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl'}
    <fieldset>
        <table>
            <thead>
                <tr>
                    <th>{translate line='admin_courses_table_header_course_name'}</th>
                    <th>{translate line='admin_courses_table_header_course_period'}</th>
                    <th colspan="2">{translate line='admin_courses_table_header_controlls'}</th>
                </tr>
            </thead>
            <tbody id="table_content">
            </tbody>
        </table>
    </fieldset>
{/block}