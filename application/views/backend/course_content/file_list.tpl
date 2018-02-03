{foreach $files as $file}
    <tr>
        <td>{$file}</td>
        <td class="controlls"><a href="{internal_url url="admin_course_content/delete_file/{$file}/{$upload_folder}/{$language}"}" data-language="{$language}" data-file="{$file|escape:'html'}" title="{translate line='admin_course_content_table_button_delete_file'}" class="button delete delete_file"><span class="list-icon list-icon-delete"></span></a></td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="2">{translate line='admin_course_content_table_content_no_files_here'}</td>
    </tr>
{/foreach}