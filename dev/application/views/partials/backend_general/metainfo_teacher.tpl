{if $list_teacher_account}
<div class="teacher_metainfo">
    {$list_teacher_account.fullname|escape:'html'} | <a href="{internal_url url='admin_teachers/my_account'}">{translate line='adminmenu_title_teacher_account'}</a> | <a href="{internal_url url='admin_teachers/logout'}" class="adminmenu_logout">{translate line='adminmenu_title_logout'}</a>
</div>
{/if}