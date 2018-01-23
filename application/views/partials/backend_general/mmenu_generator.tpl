<script type="text/javascript">
    var navigation_links = {
        'iconbar': {
            'home': '{internal_url url='admin'}',
            'tasks': '{internal_url url='admin_tasks'}',
            'task_sets': '{internal_url url='admin_task_sets'}',
            'course_content': '{internal_url url='admin_course_content'}',
            'solutions': '{internal_url url='admin_solutions'}',
            'valuation_tables': '{internal_url url='admin_solutions/valuation_tables'}',
            'my_account': '{internal_url url='admin_teachers/my_account'}',
            'logout': '{internal_url url='admin_teachers/logout'}'
        },
        'tabs': {
            'menu': '{translate line='adminmenu_tabs_menu'}',
            'account': '{translate line='adminmenu_tabs_account'}'
        }
    };

    jQuery(document).ready(function($) {
        $('nav#list-navigation').show().mmenu({
            extensions: [ 'theme-dark', "pagedim-black", "fx-listitems-slide", "multiline" ],
            dropdown: true,
            autoHeight: true,
            counters: true,
            setSelected: {
                hover: true,
                parent: true,
                current: true
            },
            navbars: [
                {
                    type: 'tabs',
                    content: [
                        '<a href="#panel-menu">' + navigation_links.tabs.menu + '</a>',
                        '<a href="#panel-account">' + navigation_links.tabs.account + '</a>'
                    ]
                },
                {
                    content: [ 'prev', 'breadcrumbs', 'close' ]
                }
            ],
            iconbar: {
                add: true,
                size: 40,
                top: [
                    '<a href="' + navigation_links.iconbar.home + '"><span class="fa fa-home"></span></a>',
                    '<a href="' + navigation_links.iconbar.tasks + '"><span class="fa fa-tasks"></span></a>',
                    '<a href="' + navigation_links.iconbar.task_sets + '"><span class="fa fa-list"></span></a>',
                    '<a href="' + navigation_links.iconbar.course_content + '"><span class="fa fa-archive"></span></a>',
                    '<a href="' + navigation_links.iconbar.solutions + '"><span class="fa fa-check-square-o"></span></a>',
                    '<a href="' + navigation_links.iconbar.valuation_tables + '"><span class="fa fa-table"></span></a>'
                ],
                'bottom': [
                    '<a href="' + navigation_links.iconbar.my_account + '"><span class="fa fa-id-card-o"></span></a>',
                    '<a href="' + navigation_links.iconbar.logout + '" class="adminmenu_logout"><span class="fa fa-sign-out"></span></a>'
                ]
            }
        });
    });
</script>
{function name='admin_menu_get_is_child_selected' menu=[] current=''}

{/function}
{function name='generate_admin_menu' menu=[] current='' level=0}
{if $menu}
    <ul>
    {foreach $menu as $item}
        <li {if $item.pagetag eq $current}class="selected"{/if}>
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