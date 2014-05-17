{extends file='layouts/error.tpl'}
{block main_content}
    {include file='partials/frontend_general/error_box.tpl' message='lang:help_error_show_error' inline}
    {if $options}
        <p>{translate line='help_error_possible_options'}:</p>
        <ul>
            {foreach $options as $lang => $url}
                <li><a href="{$url}">{$lang}</a></li>
            {/foreach}
        </ul>
    {/if}
{/block}