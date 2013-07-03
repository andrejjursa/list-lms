<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>LIST - {translate line='students_password_recovery_email_body_subject'}</title>
    </head>
    <body>
        <h1>{translate line='students_password_recovery_email_body_subject'}</h1>
        <p>{translate line='students_password_recovery_email_body_instructions'}</p>
        <p><a href="{internal_url url="students/change_password/{$student->password_token}/{$student->email|encode_for_url}"}">{translate line='students_password_recovery_email_body_recovery_link'}</a></p>
        <p><em>LIST</em></p>
    </body>
</html>