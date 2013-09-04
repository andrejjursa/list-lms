{if $list_teacher_account}
<div class="teacher_metainfo">
    {$list_teacher_account.fullname|escape:'html'} | <a href="{internal_url url='admin_teachers/my_account'}">{translate line='adminmenu_title_teacher_account'}</a> | 
    <div class="teacher_quick_langmenu"><a href="javascript:void(0);" id="teacher_quick_langmenu_button">{foreach $list_quicklang_menu as $language}{if $language@key eq $list_teacher_account.language}{$language}{/if}{/foreach}</a><ul id="teacher_quick_langmenu">{foreach $list_quicklang_menu as $language}<li{if $language@key eq $list_teacher_account.language} class="ui-state-disabled"{/if}><a href="{internal_url url="admin_teachers/switch_language/{$language@key}/{current_url}"}">{$language}</a></li>{/foreach}</ul></div> |
    <div class="teacher_quick_prefered_course_menu">
        <a href="javascript:void(0);" id="teacher_quick_prefered_course_menu_button">{$list_teacher_prefered_course_name}</a>
        <ul id="teacher_quick_prefered_course_menu">
            <li{if !$list_teacher_prefered_course_id} class="ui-state-disabled"{/if}><a href="{internal_url url="admin_teachers/switch_prefered_course/no/{current_url}"}">{translate line='admin_teachers_no_prefered_course'}</a></li>
            {foreach $list_teacher_prefered_course_menu as $period => $courses}
            <li>
                <a href="javascript:void(0);">{translate_text text=$period}</a>
                <ul>
                    {foreach $courses as $course}
                    <li{if $course@key eq $list_teacher_prefered_course_id} class="ui-state-disabled"{/if}><a href="{internal_url url="admin_teachers/switch_prefered_course/{$course@key}/{current_url}"}">{translate_text text=$course}</a></li>
                    {/foreach}
                </ul>
            </li>
            {/foreach}
        </ul>
    </div> | <a href="{internal_url url='admin_teachers/logout'}" class="adminmenu_logout">{translate line='adminmenu_title_logout'}</a>
</div>
{/if}