<div class="content_preview content_styles">{overlay table='course_content' table_id=$content->id column='content' default=$content->content}</div>
<div class="files">{$files = $content->get_files()}
    {if $files}
        <div class="default">
            <span class="language_name">{translate line='admin_course_content_table_content_all_languages'}:</span>
            {foreach $files as $file}
                <span class="file{if not $content->is_file_visible('default', $file)} hidden{/if}"><a href="{internal_url url="content/download_file/{$content->id}/default/{$file|encode_for_url}"}" target="_blank">{$file}</a></span>
            {/foreach}
        </div>
    {/if}
    {foreach $languages as $language => $language_name}{$files = $content->get_files($language)}
        {if $files}
            <div class="{$language}">
                <span class="language_name">{$language_name}:</span>
                {foreach $files as $file}
                    <span class="file{if not $content->is_file_visible($language, $file)} hidden{/if}"><a href="{internal_url url="content/download_file/{$content->id}/{$language}/{$file|encode_for_url}"}" target="_blank">{$file}</a></span>
                {/foreach}
            </div>
        {/if}
    {/foreach}
</div>