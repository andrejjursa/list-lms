<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>LIST - {translate line='admin_students_csv_import_email_subject'}</title>
    </head>
    <body>
        <h1>{translate line='admin_students_csv_import_email_subject'}</h1>
        
        <p>{translate line='admin_students_csv_import_email_body_text1'}</p>
        <p>{translate line='admin_students_csv_import_email_body_text2'}</p>
        <p><strong>{translate line='admin_students_csv_import_email_body_fullname'}:</strong> {$student->fullname}</p>
        <p><strong>{translate line='admin_students_csv_import_email_body_email'}:</strong> {$student->email}</p>
        {if $password}
            <p><strong>{translate line='admin_students_csv_import_email_body_password'}:</strong> {$password}</p>
        {else}{capture name='password_link' assign='password_link'}<a href="{internal_url url="students/change_password/{$student->password_token}/{$student->email|encode_for_url}"}">LIST - {translate line='admin_students_csv_import_email_body_password_link'}</a>{/capture}
            <p>{translate|sprintf:$password_link line='admin_students_csv_import_email_body_password_empty'}</p>
        {/if}
        {capture name='login_link' assign='login_link'}<a href="{internal_url url='students/login'}">LIST</a>{/capture}
        <p>{translate|sprintf:$login_link line='admin_students_csv_import_email_body_text3'}</p>
        
        <p><em>LIST</em></p>
    </body>
</html>