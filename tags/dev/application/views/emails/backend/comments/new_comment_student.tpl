<html>
    <head>
        <meta charset="utf-8" />
        <title>LIST - {translate line='admin_task_sets_comments_email_subject_new_post'}</title>
    </head>
    <body>
        <h1>{translate line='admin_task_sets_comments_email_subject_new_post'}</h1>{capture name='task_set_name' assign='task_set_name'}{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}{/capture}
        <p>{translate|sprintf:$student->fullname:$task_set_name line='admin_task_sets_comments_email_new_post_body_from'}</p>
        <p><strong>{translate line='admin_task_sets_comments_email_new_post_body_text'}:</strong></p>
        <p>{$comment->text|php_strip_tags:'<a><strong><span><em>'|nl2br}</p>
        <p><a href="{internal_url url="tasks/task/{$task_set->id}"}#comments-{$comment->id}">{internal_url url="tasks/task/{$task_set->id}"}</a></p>
        <p><em>LIST</em></p>
    </body>
</html>