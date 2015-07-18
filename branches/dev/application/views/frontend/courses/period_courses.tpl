{foreach $period->course->where('hide_in_lists', 0)->or_group_start()->where_related('participant/student', 'id', $list_student_account_model->id)->where_related('participant', 'allowed', 1)->group_end()->order_by_with_constant('name','asc')->group_by('id')->get_iterated() as $course}
    <div class="period_course">
        {include file='frontend/courses/single_course.tpl' inline}
    </div>
{foreachelse}
    <p class="no_course_in_period">{translate line='courses_no_course_in_period'}</p>
{/foreach}