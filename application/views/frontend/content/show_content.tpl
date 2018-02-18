{extends file='layouts/frontend_popup.tpl'}
{block title}{translate line='content_page_title'}{/block}
{block main_content}
    <h1>{translate line='content_page_title'}</h1>
    {if $course->exists()}
        <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
    {/if}
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $course->exists()}
        {include file='frontend/content/partials/content.tpl' inline}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:content_no_active_course' inline}
    {/if}
{/block}
{block custom_head}
    <script type="text/javascript">

    </script>
{/block}