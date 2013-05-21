<h4>{translate_text text=$course->name}</h4>
<p>
{if $list_student_account_model->participant->where_related_course('id', $course->id)->count() eq 1}
    {if $list_student_account_model->participant->where_related_course('id', $course->id)->get()->allowed eq 0}
        <span class="signup_message">{translate line='courses_message_signed_up_and_waiting'}</span>
    {else}
        {if $list_student_account_model->active_course_id ne $course->id}
        <a href="{internal_url url="courses/activate_course/{$course->id}"}" class="button activate_course">{translate line='courses_button_activate_course'}</a>
        {/if}
    {/if}
{else}
    <a href="{internal_url url="courses/signup_to_course/{$course->id}"}" class="button signup_to_course">{translate line='courses_button_signup_to_course'}</a>
{/if}
<a href="{internal_url url="courses/show_details/{$course->id}"}" class="button special show_details">{translate line='courses_button_show_details'}</a>
</p>