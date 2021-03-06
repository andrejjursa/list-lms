{if $content}
    {***************** Prepare phase *****************}
    {$contents = []}
    {$groups_tmp = []}
    {$groups = []}
    {foreach $content as $record}
        {capture name='content_body' assign=content_body}{strip}
            {overlay table='course_content' table_id=$record->id column='content' default=$record->content}
        {/strip}{/capture}
        {capture name='content_files' assign=content_files}{strip}
            {foreach $record->get_files() as $file}
                {if $record->is_file_visible('default', $file)}
                    <span class="file"><a href="{internal_url url="content/download_file/{$record->id}/default/{$file|encode_for_url}"}">{$file}</a></span>
                {/if}
            {/foreach}
            {foreach $record->get_files($this->lang->get_current_idiom()) as $file}
                {if $record->is_file_visible($this->lang->get_current_idiom(), $file)}
                    <span class="file"><a href="{internal_url url="content/download_file/{$record->id}/{$this->lang->get_current_idiom()}/{$file|encode_for_url}"}">{$file}</a></span>
                {/if}
            {/foreach}
        {/strip}{/capture}
        {if $content_body or $content_files}
            {capture name="content_markup" assign=content_item}{strip}
                <section class="content_wrap">
                    <header class="content_header">
                        <h1>{overlay table='course_content' table_id=$record->id column='title' default=$record->title}</h1>
                        {$reference_time = '1971-01-01 00:00:00'|strtotime}
                        {$created_at = $record->created|strtotime}
                        {$updated_at = $record->updated|strtotime}
                        <h2>
                            {if $updated_at > $reference_time}
                                <span>{$updated_at|date_format:{translate line='common_datetime_format'}}{if $record->updator_id} ({$record->updator_fullname}){/if}</span>
                            {/if}
                            {if $created_at > $reference_time}
                                <span>{$created_at|date_format:{translate line='common_datetime_format'}}{if $record->creator_id} ({$record->creator_fullname}){/if}</span>
                            {/if}
                        </h2>
                    </header>
                    {if $content_body}
                        <section class="content_body content_styles">
                            {$content_body}
                        </section>
                    {/if}
                    {if $content_files}
                        <section class="content_files">
                            {$content_files}
                        </section>
                    {/if}
                </section>
            {/strip}{/capture}
        {else}
            {$content_item = ''}
        {/if}
        {if $record->course_content_group_id}
            {$groups_tmp[$record->course_content_group_id][] = $content_item}
        {else}
            {$contents[$record->id] = $content_item}
        {/if}
    {/foreach}
    {foreach $content_groups as $group}
        {if isset($groups_tmp[$group->id])}
            {capture name='group_body' assign=group_body}{strip}
                {foreach $groups_tmp[$group->id] as $grouped_content}
                    {$grouped_content}
                {/foreach}
            {/strip}{/capture}
            {if $group_body}
                {capture name="group_markup" assign=group_item}{strip}
                    <fieldset class="group_wrap basefieldset">
                        <legend>{overlay table='course_content_groups' table_id=$group->id column='title' default=$group->title}</legend>
                        {$group_body}
                    </fieldset>
                {/strip}{/capture}
            {else}
                {$group_item = ''}
            {/if}
            {$groups[$group->id] = $group_item}
        {/if}
    {/foreach}
    {***************** Print phase *****************}
    <div class="course_content">{strip}
            {foreach $top_level_order as $order}
                {if $order.0 eq 'content'}
                    {$contents[$order.1]}
                {else}
                    {$groups[$order.1]}
                {/if}
            {/foreach}
        {/strip}</div>
{else}

{/if}