{extends file='layouts/help_index.tpl'}
{block title}{translate line='help_index_manual_page_title'}{/block}
{block index}
{function index menu=[] level=1}
    {if $menu|is_array and $menu|count gt 0}
        <ul class="index_block index_level_{$level}">
        {foreach $menu as $item}
            <li>
                {if $item.type eq 'link'}<a href="{internal_url url="help/show/{$item.index}"}" target="content_frame" class="INDEX-{$item.index|replace:'/':'-SPLIT-'}">{translate_text text=$item.title}</a>{/if}
                {if $item.type eq 'text'}<span>{translate_text text=$item.title}</span>{/if}
                {index menu=$item.sub level=$level+1}
            </li>
        {/foreach}
        </ul>
    {/if}
{/function}
<h1>{translate line='help_index_manual_page_title'}</h1>
{index menu=$index}
{/block}