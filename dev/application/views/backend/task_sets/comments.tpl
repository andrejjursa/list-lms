{extends file='layouts/backend.tpl'}
{block title}{translate|sprintf:{overlay table='task_sets' column='name' table_id=$task_set->id default=$task_set->name} line='admin_task_sets_comments_page_title'}{/block}
{block main_content}
    <h1>{translate|sprintf:{overlay table='task_sets' column='name' table_id=$task_set->id default=$task_set->name} line='admin_task_sets_comments_page_title'}</h1>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_task_sets_comments_new_comment'}</legend>
        <form action="{internal_url url="admin_task_sets/post_comment/{$task_set->id}"}" method="post" id="new_comment_form_id">
            {include file='backend/task_sets/new_comment_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_task_sets_comments_all_comments'}</legend>
        <div id="comments_content_id"></div>
    </fieldset>
{/block}