{function name='adminmenu_is_child_selected' menu=[] current=''}
{foreach $menu as $subitem}
    {if $subitem.pagetag eq $current}
        yes{break}
    {else}
        {adminmenu_is_child_selected menu=$subitem.sub current=$current}
    {/if}
{/foreach}
{/function}
{function name='make_adminmenu' menu=[] current=''}
{if $menu}
<ul>
    {foreach $menu as $item}
    {capture name='subitemselected' assign='subitemselected'}{adminmenu_is_child_selected menu=$item.sub current=$current}{/capture}
    <li class="{if $current eq $item.pagetag || $subitemselected|trim eq 'yes'}selected{/if}"><a href="{if $item.link|substr:0:9|upper eq 'EXTERNAL:'}{$item.link|substr:9}{else}{internal_url url=$item.link}{/if}">{if $item.title|substr:0:5|upper eq 'LANG:'}{translate line=$item.title|substr:5}{else}{$item.title}{/if}</a>{make_adminmenu menu=$item.sub current=$current}</li>
    {/foreach}
</ul>
{/if}
{/function}