{if $list_student_account_model->exists()}
    {if $list_student_account_model->active_course_id and $list_student_account_model->participant->where('course_id', $list_student_account_model->active_course_id)->where('allowed', 1)->get()->exists()}
    <span>{translate_text text=$list_student_account_model->active_course->get()->name} / {translate_text text=$list_student_account_model->active_course->period->get()->name}</span>
    {/if}
{else}
    {$student_registration = $this->config->item('student_registration')}
    {if $student_registration.enabled}
        <span><a href="{internal_url url='students/registration'}">{translate line='students_registration_link'}</a></span>
    {/if}
{/if}