{function name='comments' level=1 parent=0}
    {if isset($comments[$parent])}
        <ul class="comments_list level_{$level}">
        {foreach $comments[$parent] as $comment}{if $comment->teacher_id}{$teacher = $comment->teacher}{else}{$student = $comment->student}{/if}
            {if $comment->teacher_id or ($comment->approved eq 1) or ($comment->approved eq 0 and $comment->student_id eq $list_student_account_model->id)}
            <li class="comment_id_{$comment->id}">
                <div class="comment_body{if $comment->teacher_id} teacher_comment{else} student_comment{/if}{if $comment->approved eq 0 and $comment->student_id eq $list_student_account_model->id} preview_comment{/if}">
                    <div class="comment_header">
                        <strong class="author">{if $comment->teacher_id}{$teacher->fullname}{else}{$student->fullname}{/if}</strong> | <span class="created">{$comment->created|date_format:{translate line='common_datetime_format'}}</span>
                    </div>
                    <div class="comment_text">
                        {$comment->text|php_strip_tags:'<a><strong><em><span>'|nl2br}
                    </div>
                    <div class="comment_buttons">
                        {if $comment->teacher_id or $comment->approved eq 1}<a href="{internal_url url="tasks/reply_at_comment/{$task_set->id}/{$comment->id}"}" class="button reply_at">{translate line='tasks_comments_button_reply_at'}</a>{else}<span class="unapproved">{translate line='tasks_comments_message_waiting_for_approval'}</span>{/if}
                    </div>
                </div>
                {comments comments=$comments level=$level+1 parent=$comment->id}
            </li>
            {/if}
        {/foreach}
        </ul>
    {elseif $level eq 1}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_comments_message_no_comments_here' inline}
    {/if}
{/function}
<div class="comments_wrap">
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $task_set->exists() and $task_set->comments_enabled eq 1}
        <fieldset class="basefieldset">{$subscriber = $task_set->comment_subscriber_student->get_by_id($list_student_account_model->id)}
            {if $subscriber->exists() and !is_null($subscriber->id)}
                <a href="{internal_url url="tasks/unsubscribe_to_task_comments/{$task_set->id}"}" class="button unsubscribe">{translate line='tasks_comments_button_unsubscribe'}</a>
            {else}
                <a href="{internal_url url="tasks/subscribe_to_task_comments/{$task_set->id}"}" class="button subscribe">{translate line='tasks_comments_button_subscribe'}</a>
            {/if}
        </fieldset>
        {comments comments=$comments}
        <fieldset class="basefieldset"><div id="comment_form_div_id">
            {include file='frontend/tasks/comment_form.tpl' inline}
        </div></fieldset>
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_comments_message_not_found_or_disabled' inline}
    {/if}
</div>