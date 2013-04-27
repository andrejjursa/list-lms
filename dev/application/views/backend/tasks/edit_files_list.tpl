{foreach $files as $file}
<tr>
    <td><a href="{internal_url url="admin_tasks/download_file/{$task_id}/{$file.file|encode_for_url}"}" target="_blank">{$file.file|escape:'html'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_tasks/delete_file/{$task_id}/{$file.file|encode_for_url}"}" class="button delete">{translate line='admin_tasks_edit_files_delete_file'}<a></td>
</tr>
{/foreach}