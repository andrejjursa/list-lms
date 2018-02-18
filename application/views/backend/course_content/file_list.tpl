{foreach $files as $file}
    <tr data-file="{$file|escape:'html'}" data-language="{$language}">
        <td><a href="{internal_url url="content/download_file/{$upload_folder}/{$language}/{$file|encode_for_url}"}" target="_blank">{$file}</a></td>
        <td class="controlls"><a href="" data-link="index.php/content/download_file/{$upload_folder}/{$language}/{$file|encode_for_url}" class="button copy_link"><i class="fa fa-link" aria-hidden="true"></i></a></td>
        <td class="controlls"><a href="javascript:void(0);" data-file="{$file|escape:'html'}" data-language="{$language}" class="button switch_visibility" title="{translate line='admin_course_content_table_button_switch_file_visibility'}"><i class="fa fa-minus-circle" aria-hidden="true"></i></a></td>
        <td class="controlls"><a href="{internal_url url="admin_course_content/delete_file/{$file}/{$upload_folder}/{$language}"}" data-language="{$language}" data-file="{$file|escape:'html'}" title="{translate line='admin_course_content_table_button_delete_file'}" class="button delete delete_file"><span class="list-icon list-icon-delete"></span></a></td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="4">{translate line='admin_course_content_table_content_no_files_here'}</td>
    </tr>
{/foreach}