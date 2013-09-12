<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>LIST - {translate line='admin_solutions_remove_points_notification_subject'}</title>
    </head>
    <body>
        <h1>{translate line='admin_solutions_remove_points_notification_subject'}</h1>
        {capture name='task_set_name' assign='task_set_name'}{overlay table='task_sets' table_id=$task_set->id column='name' default=$task_set->name}{/capture}
        {capture name='course_name' assign='course_name'}{translate_text text=$task_set->course->name}{/capture}
        {capture name='period_name' assign='period_name'}{translate_text text=$task_set->course_period_name}{/capture}
        <p>{translate|sprintf:{$points_to_remove|floatval}:$task_set_name:$course_name:$period_name:{$task_set->upload_end_time|date_format:{translate line='common_datetime_format'}}
        line='admin_solutions_remove_points_notification_text'}</p>
        
        <p><em>LIST</em></p>
    </body>
</html>