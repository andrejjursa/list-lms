{if $list_student_account_model->active_course_id and $list_student_account_model->participant->where('course_id', $list_student_account_model->active_course_id)->where('allowed', 1)->get()->exists()}
<span>{translate_text text=$list_student_account_model->active_course->get()->name} / {translate_text text=$list_student_account_model->active_course->period->get()->name}</span>
{/if}