{function name='comments' level=1 parent=0}
    {if isset($comments[$parent])}
        <ul class="comments_list level_{$level}">
        {foreach $comments[$parent] as $comment}{if $comment->teacher_id}{$teacher = $comment->teacher}{else}{$student = $comment->student}{/if}
            <li>
                <div class="comment_body{if $comment->teacher_id} teacher_comment{else} student_comment{/if}{if $comment->approved eq 0} preview_comment{/if}">
                    <div class="comment_header">
                        <strong class="author">{if $comment->teacher_id}{$teacher->fullname}{else}{$student->fullname}{/if}</strong> | {if $comment->teacher_id}{$teacher->email}{else}{$student->email}{/if} | <span class="created">{$comment->created|date_format:{translate line='common_datetime_format'}}</span>
                    </div>
                    <div class="comment_text">
                        {$comment->text|php_strip_tags:'<a><strong><em><span>'|nl2br}
                    </div>
                    <div class="comment_buttons">
                        {if $comment->teacher_id or $comment->approved eq 1}
                            <a href="{internal_url url="admin_task_sets/reply_at_comment/{$task_set->id}/{$comment->id}"}" class="button reply_at">{translate line='admin_task_sets_comments_button_reply_at'}</a>
                        {else}
                            <a href="{internal_url url="admin_task_sets/approve_comment/{$task_set->id}/{$comment->id}"}" class="button special approve_comment">{translate line='admin_task_sets_comments_button_approve_comment'}</a>
                        {/if}
                        <a href="{internal_url url="admin_task_sets/delete_comment/{$task_set->id}/{$comment->id}"}" class="button delete delete_comment">{translate line='admin_task_sets_comments_button_delete_comment'}</a>
                    </div>
                </div>
                {comments comments=$comments level=$level+1 parent=$comment->id}
            </li>
        {/foreach}
        </ul>
    {elseif $level eq 1}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_comments_error_no_comments_yet' inline}
    {/if}
{/function}
{if !$task_set->exists()}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_error_task_set_not_found' inline}
{elseif !$task_set->comments_enabled}
    {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_comments_error_comments_disabled' inline}
{else}
    {comments comments=$comments}
{/if}
    