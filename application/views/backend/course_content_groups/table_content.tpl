<table class="course_content_group_table">
    <thead>
    <tr>
        <th>ID</th>
        <th class="sort:title">{translate line='admin_course_content_group_table_header_title'}</th>
        <th class="sort:course">{translate line='admin_course_content_group_table_header_course'}</th>
        <th class="sort:content_count">{translate line='admin_course_content_group_table_header_content_count'}</th>
        <th colspan="2" class="controlls">{translate line='admin_course_content_group_table_header_controlls'}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $content_groups as $content_group}
        <tr>
            <td>{$content_group->id}</td>
            <td>{overlay table='course_content_group' table_id=$content_group->id column='title' default=$content_group->title}</td>
            <td><span title="{translate_text text=$content_group->course_name}">{translate_text|abbreviation text=$content_group->course_name}</span> / <span title="{translate_text text=$content_group->course_period_name}">{translate_text|abbreviation text=$content_group->course_period_name}</span></td>
            <td>{$content_group->course_content_count}</td>
            <td class="controlls"><a href="{internal_url url="admin_course_content_groups/edit/{$content_group->id}"}" class="button" title="{translate line='admin_course_content_groups_table_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_course_content_groups/delete/{$content_group->id}"}" class="button delete" title="{translate line='admin_course_content_groups_table_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
        </tr>
    {/foreach}
    </tbody>
    <tfoot id="table_pagination_footer_id">
    <tr>
        <td colspan="{6 + $filter.fields|sum_array}">{include file='partials/backend_general/pagination.tpl' paged=$content_groups->paged inline}</td>
    </tr>
    </tfoot>

</table>
