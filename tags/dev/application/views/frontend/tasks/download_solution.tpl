{extends file='layouts/error.tpl'}
{block main_content}
    {if $version_download_disabled}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_download_solution_version_disabled' inline}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_download_solution_now_disabled' inline}
    {/if}
{/block}