{function name='is_menu_item_active' menu_item=''}{if $list_adminmenu_current eq $menu_item}menu_item_active{/if}{/function}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('nav#list-navigation').show().mmenu({
            extensions: [ 'theme-dark', "pagedim-black", "multiline", "shadow-page", "shadow-panels" ],
            dropdown: false,
            autoHeight: false,
            counters: true,
            setSelected: {
                hover: true,
                parent: true,
                current: false
            },
            navbars: [
                {
                    content: [ 'prev', 'breadcrumbs' ]
                }
            ],
            iconbar: {
                add: true,
                size: 40,
                top: [
                    '<a href="javascript:void(0);" id="navigation_open_close"><span class="fa fa-bars"></span></a>',
                    '<a href="{internal_url url='admin'}" title="{translate line='adminmenu_title_dashboard'}"><span class="fa fa-home {is_menu_item_active menu_item='dashboard'}"></span></a>',
                    '<a href="{internal_url url='admin_tasks'}" title="{translate line='adminmenu_title_tasks'}"><span class="fa fa-tasks {is_menu_item_active menu_item='tasks'}"></span></a>',
                    '<a href="{internal_url url='admin_task_sets'}" title="{translate line='adminmenu_title_task_sets'}"><span class="fa fa-list {is_menu_item_active menu_item='task_sets'}"></span></a>',
                    '<a href="{internal_url url='admin_moss'}" title="{translate line='adminmenu_title_moss_comparator'}"><span class="fa fa-balance-scale {is_menu_item_active menu_item='moss'}"></span></a>',
                    '<a href="{internal_url url='admin_parallel_moss'}" title="{translate line='adminmenu_title_parallel_moss_comparator'}"><span class="fa fa-balance-scale {is_menu_item_active menu_item='parallel_moss'}"></span></a>',
                    '<a href="{internal_url url='admin_course_content'}" title="{translate line='adminmenu_title_course_content'}"><span class="fa fa-archive {is_menu_item_active menu_item='course_content'}"></span></a>',
                    '<a href="{internal_url url='admin_solutions'}" title="{translate line='adminmenu_title_solutions'}"><span class="fa fa-check-square-o {is_menu_item_active menu_item='solutions'}"></span></a>',
                    '<a href="{internal_url url='admin_solutions/valuation_tables'}" title="{translate line='adminmenu_title_valuation_tables'}"><span class="fa fa-table {is_menu_item_active menu_item='valuation_tables'}"></span></a>'
                ],
                'bottom': [
                    '<a href="{internal_url url='admin_teachers/my_account'}" title="{translate line='adminmenu_title_teacher_account'}"><span class="fa fa-id-card-o {is_menu_item_active menu_item='teacher_account'}"></span></a>',
                    '<a href="{internal_url url='admin_teachers/logout'}" title="{translate line='adminmenu_title_logout'}" class="adminmenu_logout"><span class="fa fa-sign-out"></span></a>'
                ]
            },
            sidebar: {
                collapsed: "(min-width: 40px)"
            }
        }, {
            offCanvas: {
                pageSelector: '#page'
            },
            navbars: {
                breadcrumbs: {
                    removeFirst: true,
                    separator: '&lt;'
                }
            }
        });

        jQuery('a.mm-menu__blocker').hide();

        jQuery('#navigation_open_close').click(function() {
            if (jQuery('nav#list-navigation').hasClass('mm-menu_opened')) {
                jQuery('nav#list-navigation').data('mmenu').close();
            } else {
                jQuery('nav#list-navigation').data('mmenu').open();
            }
        });
    });
</script>
{function name='admin_menu_is_selected' menu=[] current=''}
    {foreach $menu as $subitem}
        {if $subitem.pagetag eq $current}
            yes{break}
        {else}
            {admin_menu_is_selected menu=$subitem.sub current=$current}
        {/if}
    {/foreach}
{/function}
{function name='generate_admin_menu' menu=[] current='' level=0}
    {if $menu}
        <ul>
            {foreach $menu as $item}
                {capture name='subitemselected' assign='subitemselected'}{admin_menu_is_selected menu=$item.sub current=$current}{/capture}
                <li class="{if $item.pagetag eq $current}Selected{/if} {if $item.pagetag eq $current || $subitemselected|trim eq 'yes'}list-selected-menu-item{/if}">
                    {if $item.class eq 'inactive'}
                        <span>{if $item.icon}{$item.icon}{/if}{translate_text text=$item.title}</span>
                    {else}
                        <a href="{if $item.link|substr:0:9|upper eq 'EXTERNAL:'}{$item.link|substr:9}{else}{internal_url url=$item.link}{/if}" class="{$item.class}">{if $item.icon}{$item.icon}{/if}{translate_text text=$item.title}</a>
                    {/if}
                    {generate_admin_menu menu=$item.sub current=$current level=$level+1}
                </li>
            {/foreach}
        </ul>
    {/if}
{/function}
{function name='add_admin_menu' menu=[] current=''}
    <nav id="list-navigation" style="display: none;">
        <div id="panel-menu">
            <p>{translate line='adminmenu_user'}: <strong>{$list_teacher_account.fullname|escape:'html'}</strong></p>
            <span id="header_open_task_set_id">{include file='partials/backend_general/open_task_set.tpl' inline}</span>
            {generate_admin_menu menu=$menu current=$current}
            <ul>
                <li><a href="{internal_url url='admin_teachers/my_account'}"><i class="fa fa-id-card-o" aria-hidden="true"></i>{translate line='adminmenu_title_teacher_account'}</a></li>
                <li><span><i class="fa fa-language" aria-hidden="true"></i>{foreach $list_quicklang_menu as $language}{if $language@key eq $list_teacher_account.language}{$language}{/if}{/foreach}</span>
                    <ul class="Vertical">
                        {foreach $list_quicklang_menu as $language}
                            <li>
                                {if $language@key eq $list_teacher_account.language}
                                    <span>{$language}</span>
                                {else}
                                    <a href="{internal_url url="admin_teachers/switch_language/{$language@key}/{current_url}"}">{$language}</a>
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                </li>
                <li class="mm-multiline"><span><i class="fa fa-book" aria-hidden="true"></i>{$list_teacher_prefered_course_name}</span>
                    <ul>
                        <li><a href="{internal_url url="admin_teachers/switch_prefered_course/no/{current_url}"}">{translate line='admin_teachers_no_prefered_course'}</a></li>
                        {foreach $list_teacher_prefered_course_menu as $period => $courses}
                            <li><span>{translate_text text=$period}</span>
                                <ul>
                                    {foreach $courses as $course}
                                        <li>
                                            {if $course@key eq $list_teacher_prefered_course_id}
                                                <span>{translate_text text=$course}</span>
                                            {else}
                                                <a href="{internal_url url="admin_teachers/switch_prefered_course/{$course@key}/{current_url}"}">{translate_text text=$course}</a>
                                            {/if}
                                        </li>
                                    {/foreach}
                                </ul>
                            </li>
                        {/foreach}
                    </ul>
                </li>
                <li><a href="{internal_url url='admin_teachers/logout'}" class="adminmenu_logout"><i class="fa fa-sign-out" aria-hidden="true"></i>{translate line='adminmenu_title_logout'}</a></li>
            </ul>
        </div>
    </nav>
{/function}