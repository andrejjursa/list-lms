{if $list_student_account_model->active_course_id}
<span>{translate_text text=$list_student_account_model->active_course->get()->name} / {translate_text text=$list_student_account_model->active_course->period->get()->name}</span>
{/if}