{extends file='layouts/frontend.tpl'}
{block main_content}
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {foreach $periods as $period}
        <h3>{translate_text text=$period->name}</h3>
        <div class="period_courses">
        {foreach $period->course->order_by('name','asc')->get_iterated() as $course}
            <h4>{translate_text text=$course->name}</h4>
        {foreachelse}
            <p class="no_course_in_period">{translate line='courses_no_course_in_period'}</p>
        {/foreach}
        </div>
    {/foreach}
{/block}