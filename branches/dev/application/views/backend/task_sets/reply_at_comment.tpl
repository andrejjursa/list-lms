{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_task_sets_comments_reply_at_page_title'}{/block}
{block main_content}
    <h3>{translate line='admin_task_sets_comments_reply_at_page_title'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if !$task_set->exists()}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_error_task_set_not_found' inline}
    {elseif !$task_set->comments_enabled}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_comments_error_comments_disabled' inline}
    {elseif !$comment->exists()}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_comments_error_reply_at_comment_not_exists' inline}
    {elseif $comment->task_set_id ne $task_set->id}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_task_sets_comments_error_reply_at_comment_from_different_task_set' inline}
    {else}
    <fieldset>
        <ul class="comments_list level_1">{if $comment->teacher_id}{$teacher = $comment->teacher}{else}{$student = $comment->student}{/if}
            <li>
                <div class="comment_body{if $comment->teacher_id} teacher_comment{else} student_comment{/if}{if $comment->approved eq 0} preview_comment{/if}">
                    <div class="comment_header">
                        <strong class="author">{if $comment->teacher_id}{$teacher->fullname}{else}{$student->fullname}{/if}</strong> | {if $comment->teacher_id}{$teacher->email}{else}{$student->email}{/if} | <span class="created">{$comment->created|date_format:{translate line='common_datetime_format'}}</span>
                    </div>
                    <div class="comment_text">
                        {$comment->text|php_strip_tags:'<a><strong><em><span>'|nl2br}
                    </div>
                </div>
            </li>
        </ul>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_task_sets_comments_new_comment'}</legend>
        <form action="{internal_url url="admin_task_sets/post_comment_reply/{$task_set->id}/{$comment->id}"}" method="post">
            <div class="field">
                <label for="">{translate line='admin_task_sets_comments_form_label_text'}:</label>
                <div class="input"><textarea name="comment[text]">{$smarty.post.comment.text|escape:'html'}</textarea></div>
                <p class="input"><em>{translate line='admin_task_sets_comments_form_label_text_hint'}</em></p>
                {form_error field='comment[text]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_task_sets_comments_form_button_submit'}" class="button" />
                <input type="hidden" name="comment[task_set_id]" value="{$task_set->id|intval}" />
                <input type="hidden" name="comment[reply_at_id]" value="{$comment->id|intval}" />
            </div>
        </form>
    </fieldset>
    {/if}
{/block}