{if $course->exists()}{strip}
    <div class="top_level_sorting">
    {foreach $top_level as $content_or_group}
        {if $content_or_group->type == 'content'}
            <div class="sorted_content" data-id="{$content_or_group->id}" data-type="content">
                {overlay table='course_content' table_id=$content_or_group->id column='title' default=$content_or_group->title}
            </div>
        {else}
            <div class="sorted_group" data-id="{$content_or_group->id}" data-type="group">
                {overlay table='course_content_groups' table_id=$content_or_group->id column='title' default=$content_or_group->title}
                <div class="inner_content" data-parent="{$content_or_group->id}">
                    {if $content_or_group->content_count > 0}
                        {foreach $grouped_content as $sub_content}
                            {if $sub_content->course_content_group_id eq $content_or_group->id}
                            <div class="sorted_content" data-id="{$sub_content->id}" data-type="content">
                                {overlay table='course_content' table_id=$sub_content->id column='title' default=$sub_content->title}
                            </div>
                            {/if}
                        {/foreach}
                    {/if}
                </div>
            </div>
        {/if}
    {/foreach}
    </div><div id="current_course" data-id="{$course->id}"></div>
{/strip}{else}

{/if}