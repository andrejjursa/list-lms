{extends file='layouts/frontend.tpl'}
{block title}{translate line='courses_page_title'}{/block}
{block main_content}
    <h1>{translate line='courses_page_title'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="{internal_url url='courses'}" method="post">
            <select name="period_id" size="1">
                {list_html_options options=$period_options selected=$periods->id}
            </select>
            <input type="submit" name="submit_button" value="{translate line='courses_button_select_period'}" class="button" />
        </form>
    </fieldset>
    {foreach $periods->all as $period}
        <fieldset>
        <legend>{translate_text text=$period->name}</legend>
        <div class="period_courses">
        {foreach $period->course->order_by_with_constant('name','asc')->get_iterated() as $course}
            <div class="period_course">
                {include file='frontend/courses/single_course.tpl' inline}
            </div>
        {foreachelse}
            <p class="no_course_in_period">{translate line='courses_no_course_in_period'}</p>
        {/foreach}
        </div>
        </fieldset>
    {/foreach}
{/block}
{block custom_head}
<script type="text/javascript">
    var messages = {
        unknown_error: '{translate line='courses_messages_unknown_error'}'
    };
</script>
{/block}