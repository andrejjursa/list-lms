<div class="log_item {if $error_message}error_log{/if}{if $success_message}success_log{/if}">
    {if $student_id}<p><strong>{translate line='admin_students_csv_import_log_student_id'}:</strong> {$student_id}</p>{/if}
    {if trim($firstname)}<p><strong>{translate line='admin_students_csv_import_log_firstname'}:</strong> {$firstname|trim}</p>{/if}
    {if trim($lastname)}<p><strong>{translate line='admin_students_csv_import_log_lastname'}:</strong> {$lastname|trim}</p>{/if}
    {if trim($fullname)}<p><strong>{translate line='admin_students_csv_import_log_fullname'}:</strong> {$fullname|trim}</p>{/if}
    {if trim($email)}<p><strong>{translate line='admin_students_csv_import_log_email'}:</strong> {$email|trim}</p>{/if}
    {if trim($password)}<p><strong>{translate line='admin_students_csv_import_log_password'}:</strong> {$password|trim}</p>{/if}
    {if $error_message}<p class="error">{translate_text text=$error_message}</p>{/if}
    {if $success_message}<p class="success">{translate_text text=$success_message}</p>{/if}
    {if $course_assignment_success_message}<p class="success">{translate_text text=$course_assignment_success_message}</p>{/if}
    {if $course_assignment_error_message}<p class="error">{translate_text text=$course_assignment_error_message}</p>{/if}
    {if $course_assignment_approwal_success_message}<p class="success">{translate_text text=$course_assignment_approwal_success_message}</p>{/if}
    {if $course_assignment_approwal_error_message}<p class="error">{translate_text text=$course_assignment_approwal_error_message}</p>{/if}
    {if $email_success_message}<p class="success">{translate_text text=$email_success_message}</p>{/if}
    {if $email_error_message}<p class="error">{translate_text text=$email_error_message}</p>{/if}
</div>