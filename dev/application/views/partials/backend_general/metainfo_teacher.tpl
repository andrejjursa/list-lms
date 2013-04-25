{if $list_teacher_account}
<div class="teacher_metainfo">
    {$list_teacher_account.fullname|escape:'html'} | <a href="{internal_url url='admin_teachers/my_account'}">{translate line='adminmenu_title_teacher_account'}</a> | 
    <div class="teacher_quick_langmenu"><a href="javascript:void(0);" id="teacher_quick_langmenu_button">{foreach $list_quicklang_menu as $language}{if $language@key eq $list_teacher_account.language}{$language}{/if}{/foreach}</a><ul id="teacher_quick_langmenu">{foreach $list_quicklang_menu as $language}<li{if $language@key eq $list_teacher_account.language} class="ui-state-disabled"{/if}><a href="{internal_url url="admin_teachers/switch_language/{$language@key}/{current_url}"}">{$language}</a></li>{/foreach}</ul></div> |
    <a href="{internal_url url='admin_teachers/logout'}" class="adminmenu_logout">{translate line='adminmenu_title_logout'}</a>
</div>
{/if}