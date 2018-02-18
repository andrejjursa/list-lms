{extends file='layouts/frontend_popup.tpl'}
{block title}{translate line='courses_page_description_title'}{/block}
{block main_content}
    <h1>{translate line='courses_page_description_title'}</h1>
    {if $course->exists()}
        <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
    {/if}
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $course->exists()}
        {include file='frontend/courses/partials/description_markup.tpl' inline}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:courses_course_not_found' inline}
    {/if}
{/block}
{block custom_head}
    <script type="text/javascript">

    </script>
{/block}