{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_parallel_moss_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_parallel_moss_page_title'}</h2>
    {if $moss_enabled}
        <fieldset>

        </fieldset>

        <fieldset>
            <legend>{translate line='admin_parallel_moss_all_planed_and_finished_comparisons'}</legend>
            <table>

            </table>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_parallel_moss_general_error_user_id_not_set' inline}
    {/if}
{/block}