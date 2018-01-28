<table class="course_content_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>{translate line='admin_course_content_table_header_title'}</th>
            <th>{translate line='admin_course_content_table_header_course'}</th>
            <th>{translate line='admin_course_content_table_header_published'}</th>
            <th colspan="2" class="controlls">{translate line='admin_course_content_table_header_controlls'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $course_content as $content}
        <tr class="{if !$content->published}not_published{/if}">
            <td>{$content->id}</td>
            <td>{overlay table='course_content' table_id=$content->id column='title' default=$content->title}</td>
            <td><span title="{translate_text text=$content->course_name}">{translate_text|abbreviation text=$content->course_name}</span> / <span title="{translate_text text=$content->course_period_name}">{translate_text|abbreviation text=$content->course_period_name}</span></td>
            <td>{if $content->published}<span class="published_yes"><i class="fa fa-check" aria-hidden="true"></i> {translate line='admin_course_content_table_content_published_yes'}</span class="published_no">{else}<span><i class="fa fa-times" aria-hidden="true"></i> {translate line='admin_course_content_table_content_published_no'}</span>{/if}</td>
            <td class="controlls"><a href="{internal_url url="admin_course_content/edit/{$content->id}"}" class="button" title="{translate line='admin_course_content_table_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
            <td class="controlls"><a href="{internal_url url="admin_course_content/delete/{$content->id}"}" class="button delete" title="{translate line='admin_course_content_table_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
        </tr>
        {/foreach}
    </tbody>
    <tfoot id="table_pagination_footer_id">
    <tr>
        <td colspan="{6 + $filter.fields|sum_array}">{include file='partials/backend_general/pagination.tpl' paged=$course_content->paged inline}</td>
    </tr>
    </tfoot>

</table>