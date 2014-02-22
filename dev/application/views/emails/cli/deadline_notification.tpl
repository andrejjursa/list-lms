<html>
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    {$query_fragment = ''}{if $group->exists()}{$query_fragment = "#group_{$group->id}"}{/if}
    {$task_set_name = {overlay table='task_sets' column='name' table_id=$task_set->id default=$task_set->name}}
    {$group_name = {translate_text text=$group->name}}
    <body>
        <p>{if $group->exists()}{translate|sprintf:$task_set_name:$group_name line='cli_deadline_notification_text_task_set_group'}{else}{translate|sprintf:$task_set_name line='cli_deadline_notification_text_task_set'}{/if}</p>
        <p><a href="{internal_url url="admin_solutions/solutions_list/{$task_set->id}{$query_fragment}" simple=true}">{translate line='cli_deadline_notification_link'}</a></p>
        <p>LIST</p>
    </body>
</html>