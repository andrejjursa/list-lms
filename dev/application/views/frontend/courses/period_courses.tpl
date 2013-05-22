{foreach $period->course->order_by_with_constant('name','asc')->get_iterated() as $course}
    <div class="period_course">
        {include file='frontend/courses/single_course.tpl' inline}
    </div>
{foreachelse}
    <p class="no_course_in_period">{translate line='courses_no_course_in_period'}</p>
{/foreach}