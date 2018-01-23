<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link href="{'/public/css/admin_general.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" media="screen" />
        <link href="{'/public/css/admin_icons.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/list-theme/jquery-ui-1.10.2.custom.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/jqueryui-timepicker-addon.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/notification.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/font-awesome/css/font-awesome.min.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/js/jquery.mmenu/jquery.mmenu.all.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{'favicon.ico'|base_url}" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var logout_question_text = "{translate|addslashes line='adminmenu_logout_question_text'}";
            var jqueryui_datepicker_region = "{translate|addslashes line='common_jqueryui_datepicker_region'|default:'en'}";
            var list_version = "{''|add_file_version}";
        </script>
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.mmenu/jquery.mmenu.all.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{$list_lang_js_messages|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.mousewheel-3.0.6.pack.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.fancybox.pack.js?v=2.1.4'|base_url|add_file_version}"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="{'/public/css/jquery.fancybox.css?v=2.1.4'|base_url|add_file_version}" />
        <link rel="stylesheet" type="text/css" media="screen" href="{'/public/css/jquery.fancybox-buttons.css?v=2.1.4'|base_url|add_file_version}" />
        <script type="text/javascript" src="{'/public/js/jquery.fancybox-buttons.js?v=2.1.4'|base_url|add_file_version}"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="{'/public/css/jquery.fancybox-thumbs.css?v=2.1.4'|base_url|add_file_version}" />
        <script type="text/javascript" src="{'/public/js/jquery.fancybox-thumbs.js?v=2.1.4'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.fancybox-media.js?v=1.0.0'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery-ui-1.10.2.custom.min.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery-ui-timepicker-addon.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jMenu.jquery.min.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.blockUI.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/admin_menu.js'|base_url|add_file_version}"></script>
        <script type="text/javascript" src="{'/public/js/api.js'|base_url|add_file_version}"></script>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
        {include file='partials/backend_general/mmenu_generator.tpl' inline}
        {block name='custom_head'}{/block}
    </head>
    {include file='partials/backend_general/adminmenu.tpl' inline}
    <body>
        <div id="page">
            <div id="list-top-header">
                <a href="#list-navigation" id="menu-open-close-button"><i class="fa fa-bars" aria-hidden="true"></i></a>
                <span id="list-title">L.I.S.T.</span> <span>Long-term Internet Storage of Tasks</span> <span></span>
            </div>
            <div id="list-content">
                <div class="backend_body header_margin">{block name='main_content'}{/block}</div>
            </div>
            <div id="list-footer">
                {translate|sprintf:'Andrej Jursa':2013:{translate line='common_copyright_faculty'}:"<a href=\"{internal_url url='admin_settings/changelog'}\">{$this->config->item('list_version')}</a>" line='commont_copyright_text'}
            </div>
            <nav id="list-navigation" style="display: none;">
                <div id="panel-menu">
                    <span id="header_open_task_set_id">{include file='partials/backend_general/open_task_set.tpl' inline}</span>
                    {generate_admin_menu menu=$list_adminmenu current=$list_adminmenu_current}
                </div>
                <div id="panel-account">
                    <p>{translate line='adminmenu_user'}: {$list_teacher_account.fullname|escape:'html'}</p>
                    <ul>
                        <li><a href="{internal_url url='admin_teachers/my_account'}"><i class="fa fa-id-card-o" aria-hidden="true"></i>{translate line='adminmenu_title_teacher_account'}</a></li>
                        <li><span><i class="fa fa-language" aria-hidden="true"></i>{foreach $list_quicklang_menu as $language}{if $language@key eq $list_teacher_account.language}{$language}{/if}{/foreach}</span>
                            <ul>
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
        </div>
        {*<div id="pagewrap_id">
            <div id="headerwrap_id">
                <div id="top_meta_informations">
                    <div class="left"><span id="header_open_task_set_id">{include file='partials/backend_general/open_task_set.tpl' inline}</span></div>
                    <div class="right">{include file='partials/backend_general/metainfo_teacher.tpl' inline}</div>
                    <div class="clear"></div>
                </div>
                <h1>L.I.S.T. - {translate line='adminmenu_administration_title'}</h1>
                <p>Long-term Internet Storage of Tasks</p>
                <nav>{make_adminmenu menu=$list_adminmenu current=$list_adminmenu_current}</nav>
            </div>
            <div id="body_id">
                <div class="backend_body header_margin">{block name='main_content'}{/block}</div>
                <div class="backend_footer">{translate|sprintf:'Andrej Jursa':2013:{translate line='common_copyright_faculty'}:"<a href=\"{internal_url url='admin_settings/changelog'}\">{$this->config->item('list_version')}</a>" line='commont_copyright_text'}</div>
            </div>

        </div>*}
    </body>
</html>
