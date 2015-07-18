{function name='adminmenu_is_child_selected' menu=[] current=''}
{foreach $menu as $subitem}
    {if $subitem.pagetag eq $current}
        yes{break}
    {else}
        {adminmenu_is_child_selected menu=$subitem.sub current=$current}
    {/if}
{/foreach}
{/function}
{function name='make_adminmenu' menu=[] current='' level=0}
{if $menu}
<ul{if $level eq 0} id="jMenu"{/if}>
    {if $menu and $level eq 1}<li class="arrow"></li>{/if}
    {foreach $menu as $item}
    {capture name='subitemselected' assign='subitemselected'}{adminmenu_is_child_selected menu=$item.sub current=$current}{/capture}
    <li class="{if $current eq $item.pagetag || $subitemselected|trim eq 'yes'}selected{/if}"><a href="{if $item.link|substr:0:9|upper eq 'EXTERNAL:'}{$item.link|substr:9}{else}{internal_url url=$item.link}{/if}" class="{$item.class}{if $level eq 0} fNiv{/if}">{translate_text text=$item.title}</a>{make_adminmenu menu=$item.sub current=$current level=$level+1}</li>
    {/foreach}
</ul>
{/if}
{/function}