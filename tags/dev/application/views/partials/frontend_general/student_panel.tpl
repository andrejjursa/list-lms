<div class="student_panel">
{if $list_student_account}
    {$list_student_account.fullname|escape:'html'} | <a href="{internal_url url='students/my_account'}">{translate line='pagemenu_title_student_account'}</a> | 
    <a href="{internal_url url='students/logout'}" class="pagemenu_logout">{translate line='pagemenu_title_logout'}</a>
{else}
    <span>{translate line='pagemenu_student_not_loged_in'}</span>
{/if}
</div>