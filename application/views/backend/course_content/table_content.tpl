<table class="course_content_table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="sort:title">{translate line='admin_course_content_table_header_title'}</th>
            <th class="sort:course">{translate line='admin_course_content_table_header_course'}</th>
            <th class="sort:published">{translate line='admin_course_content_table_header_published'}</th>
            <th class="sort:public">{translate line='admin_course_content_table_header_public'}</th>
            <th class="sort:course_content_group">{translate line='admin_course_content_table_header_content_group'}</th>
            <th colspan="3" class="controlls">{translate line='admin_course_content_table_header_controlls'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $course_content as $content}
            <tr class="grid{if !$content->published} not_published{/if}{if $content->public} public{/if}">
                <td>{$content->id}</td>
                <td>{overlay table='course_content' table_id=$content->id column='title' default=$content->title}</td>
                <td><span title="{translate_text text=$content->course_name}">{translate_text|abbreviation text=$content->course_name}</span> / <span title="{translate_text text=$content->course_period_name}">{translate_text|abbreviation text=$content->course_period_name}</span></td>
                <td>{if $content->published}<span class="published_yes"><i class="fa fa-check" aria-hidden="true"></i> {translate line='admin_course_content_table_content_published_yes'}</span class="published_no">{else}<span><i class="fa fa-times" aria-hidden="true"></i> {translate line='admin_course_content_table_content_published_no'}</span>{/if}</td>
                <td>{if $content->public}<span class="published_yes"><i class="fa fa-check" aria-hidden="true"></i> {translate line='admin_course_content_table_content_published_yes'}</span class="published_no">{else}<span><i class="fa fa-times" aria-hidden="true"></i> {translate line='admin_course_content_table_content_published_no'}</span>{/if}</td>
                <td>{if $content->course_content_group_id > 0}{overlay table='course_content_groups' table_id=$content->course_content_group_id column='title' default=$content->course_content_group_title}{else}{translate line='admin_course_content_table_content_none_content_group'}{/if}</td>
                <td class="controlls"><a href="javascript:void(0);" class="button toggle_content" data-content-id="{$content->id}"><i class="fa fa-chevron-down" aria-hidden="true"></i></a></td>
                <td class="controlls"><a href="{internal_url url="admin_course_content/edit/{$content->id}"}" class="button" title="{translate line='admin_course_content_table_button_edit'}"><span class="list-icon list-icon-edit"></span></a></td>
                <td class="controlls"><a href="{internal_url url="admin_course_content/delete/{$content->id}"}" class="button delete" title="{translate line='admin_course_content_table_button_delete'}"><span class="list-icon list-icon-delete"></span></a></td>
            </tr>
            <tr class="content_overview" data-content-id="{$content->id}">
                <td></td>
                <td colspan="5">
                    <div class="content_preview">{overlay table='course_content' table_id=$content->id column='content' default=$content->content}</div>
                    <div class="files">{$files = $content->get_files()}
                        {if $files}
                            <div class="default">
                                <span class="language_name">{translate line='admin_course_content_table_content_all_languages'}:</span>
                                {foreach $files as $file}
                                    <span class="file{if not $content->is_file_visible('default', $file)} hidden{/if}">{$file}</span>
                                {/foreach}
                            </div>
                        {/if}
                        {foreach $languages as $language => $language_name}{$files = $content->get_files($language)}
                            {if $files}
                                <div class="{$language}">
                                    <span class="language_name">{$language_name}:</span>
                                    {foreach $files as $file}
                                        <span class="file{if not $content->is_file_visible($language, $file)} hidden{/if}">{$file}</span>
                                    {/foreach}
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                </td>
                <td colspan="3" class="controlls"></td>
            </tr>
        {/foreach}
    </tbody>
    <tfoot id="table_pagination_footer_id">
    <tr>
        <td colspan="9">{include file='partials/backend_general/pagination.tpl' paged=$course_content->paged inline}</td>
    </tr>
    </tfoot>

</table>