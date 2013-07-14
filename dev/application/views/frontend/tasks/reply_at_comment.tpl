{extends file='layouts/frontend_popup.tpl'}
{block title}{if $task_set->exists()}{overlay table='task_sets' column='name' table_id=$task_set->id default=$task_set->name}{/if}{/block}
{block main_content}
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $task_set->exists() and $task_set->comments_enabled and $comment->exists()}
        <div class="comments_wrap">
            <ul class="comments_list level_1">
                <li>
                    <div class="comment_body{if $comment->teacher_id} teacher_comment{else} student_comment{/if}{if $comment->approved eq 0 and $comment->student_id eq $list_student_account_model->id} preview_comment{/if}">
                        <div class="comment_header">
                            <strong class="author">{if $comment->teacher_id}{$comment->teacher->fullname}{else}{$comment->student->fullname}{/if}</strong> | <span class="created">{$comment->created|date_format:{translate line='common_datetime_format'}}</span>
                        </div>
                        <div class="comment_text">
                            {$comment->text|strip_tags:'<a><strong><em><span>'|nl2br}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        {include file='frontend/tasks/comment_form.tpl' reply_at=$comment->id inline}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_comments_reply_unable_to_reply' inline}
    {/if}
{/block}