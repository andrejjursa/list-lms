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
                {include file='frontend/courses/period_courses.tpl' inline}
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