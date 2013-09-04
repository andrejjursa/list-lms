{if $subscribed}
    <a href="{internal_url url="admin_task_sets/comments_unsubscribe/{$task_set->id}"}" class="button delete unsubscribe">{translate line='admin_task_sets_comments_my_settings_unsubscribe'}</a>
{else}
    <a href="{internal_url url="admin_task_sets/comments_subscribe/{$task_set->id}"}" class="button subscribe">{translate line='admin_task_sets_comments_my_settings_subscribe'}</a>
{/if}