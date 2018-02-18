<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <link type="text/css" rel="stylesheet" href="{'/public/css/page.css'|base_url|add_file_version}" />
        <link href="{'/public/js/jquery.mmenu/jquery.mmenu.all.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/font-awesome/css/font-awesome.min.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.mmenu/jquery.mmenu.all.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/page/page.js'|base_url|add_file_version}"></script>
        <script>
            jQuery(document).ready(function($) {
                $('nav#page_navigation').show().mmenu({
                    extensions: [ 'theme-dark', "pagedim-black", "multiline", "shadow-page", "shadow-panels" ],
                    dropdown: false,
                    autoHeight: false,
                    counters: true,
                    setSelected: {
                        hover: true,
                        parent: true,
                        current: false
                    },
                    iconbar: {
                        add: true,
                        size: 40,
                        top: [,
                            '<a href="javascript:void(0);"><span class="fa fa-bars"></span></a>'
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

                jQuery(document).on('mouseover', 'a.mm-menu__blocker', function() {
                    jQuery('#page').removeClass('no-initial-slideout');
                    jQuery('nav#page_navigation').data('mmenu').open();
                });

                jQuery(document).on('mouseover', 'div.mm-page__blocker', function() {
                    jQuery('#page').removeClass('no-initial-slideout');
                    jQuery('nav#page_navigation').data('mmenu').close();
                });

                jQuery(document).mouseleave(function() {
                    jQuery('#page').removeClass('no-initial-slideout');
                    jQuery('nav#page_navigation').data('mmenu').close();
                });
            });
        </script>
        {block name='custom_head'}{/block}
    </head>
    <body>
        <div id="page" class="no-initial-slideout">
            <iframe id="main_content"></iframe>
            <nav id="page_navigation" style="display: none;">
                <div id="menu-panel">
                    <ul>
                        <li><a data-link-id="description" href="{internal_url url="courses/show_description/{$course->id}/{$lang}"}">{translate line='content_page_link_course_description'}</a></li>
                        <li><a data-link-id="content" href="{internal_url url="content/show_content/{$course->id}/{$lang}"}">{translate line='content_page_link_course_content'}</a></li>
                        {if not $course->disable_public_groups_page}
                            <li><a data-link-id="groups" href="{internal_url url="courses/show_details/{$course->id}/{$lang}"}">{translate line='content_page_link_course_groups'}</a></li>
                        {/if}
                    </ul>
                    {if Course_content_model::isJson($course->additional_menu_links)}
                        {$links = $course->additional_menu_links|json_decode}
                        {capture name='all_links' assign=all_links}{strip}
                            {foreach $links as $id => $link}
                                {if not isset($link->lang) or $link->lang eq $lang or $link->lang eq ''}
                                    {if not isset($link->id) or $link->id eq ''}
                                        <li><a target="_blank" href="{$link->href|escape:'html'}">{$link->text|htmlspecialchars}</a></li>
                                    {else}
                                        <li><a data-link-id="pake_{$link->id}" href="{$link->href|escape:'html'}">{$link->text|htmlspecialchars}</a></li>
                                    {/if}
                                {/if}
                            {/foreach}
                        {/strip}{/capture}
                        {if $all_links}
                            <hr />
                            <ul>
                                {$all_links}
                            </ul>
                        {/if}
                    {/if}
                    <hr />
                    <ul>
                        <li><span>{translate line='content_page_link_language_switch'}</span>
                            <ul class="Vertical">
                                {foreach $languages as $language => $langName}
                                    <li>
                                        <a class="language_switch" href="{internal_url url="page/{$course->id}/{$language}"}">{$langName}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                    </ul>
                    <hr />
                    {if $student->exists()}
                        <p id="loged-in-user"><strong>{translate line='content_page_current_user'}:</strong> {$student->fullname}</p>
                        <ul>
                            <li><a href="{internal_url url='tasks'}" target="_blank">{translate line='content_page_link_to_list'}</a></li>
                            <li><a href="{internal_url url="students/logout_to_page/{$course->id}/{$lang}"}">{translate line='content_page_link_logout'}</a></li>
                        </ul>
                    {else}
                        <ul>
                            <li><a href="{internal_url url="students/login/current_url/{internal_url|encode_for_url url="page/{$course->id}/{$lang}"}"}">{translate line='content_page_link_login'}</a></li>
                        </ul>
                    {/if}
                </div>
            </nav>
        </div>
    </body>
</html>