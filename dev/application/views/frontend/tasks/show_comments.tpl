{function name='comments' level=1}
    {if $comments->exists()}
    <ul class="comments_list level_{$level}">
        {foreach $comments->all as $comment}{if $comment->teacher_id}{$teacher = $comment->teacher->get()}{else}{$student = $comment->student->get()}{/if}
            {if $comment->teacher_id or ($comment->approved eq 1) or ($comment->approved eq 0 and $comment->student_id eq $list_student_account_model->id)}
            <li>
                <div class="comment_body{if $comment->teacher_id} teacher_comment{else} student_comment{/if}{if $comment->approved eq 0 and $comment->student_id eq $list_student_account_model->id} preview_comment{/if}">
                    <div class="comment_header">
                        <strong class="author">{if $comment->teacher_id}{$teacher->fullname}{else}{$student->fullname}{/if}</strong> | <span class="created">{$comment->created|date_format:{translate line='common_datetime_format'}}</span>
                    </div>
                    <div class="comment_text">
                        {$comment->text|strip_tags:'<p><a><strong><em><br><span>'}
                    </div>
                    <div class="comment_buttons">
                        {if $comment->teacher_id or $comment->approved eq 1}<a href="{internal_url url="tasks/reply_at_comment/{$comment->id}"}" class="button reply_at">{translate line='tasks_comments_button_reply_at'}</a>{/if}
                    </div>
                </div>
                {comments comments=$comment->comment->get() level=$level+1}
            </li>
            {/if}
        {/foreach}
    </ul>
    {/if}
{/function}
<div class="comments_wrap">
    {comments comments=$comments}
</div>