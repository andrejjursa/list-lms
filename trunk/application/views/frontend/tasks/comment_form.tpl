<form action="{if $reply_at}{internal_url url="tasks/post_comment_reply/{$task_set->id|intval}/{$reply_at|intval}"}{else}{internal_url url="tasks/post_comment/{$task_set->id|intval}"}{/if}" method="post" id="comment_form_id">
    <label for="comment_text_id">{translate line='tasks_comments_form_label_comment_text'}:</label><br />
    <textarea name="comment[text]" class="comment_text" id="comment_text_id">{$smarty.post.comment.text}</textarea>
    <p><em>{translate line='tasks_comments_form_label_comment_text_hint'}</em></p>
    <p>
        <input type="submit" value="{translate line='tasks_comments_form_button_submit'}" class="button" />
        <input type="hidden" name="comment[task_set_id]" value="{$task_set->id|intval}" />
        <input type="hidden" name="comment[reply_at_id]" value="{$reply_at}" />
    </p>
</form>