<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link href="{'/public/css/admin_general.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" media="screen" />
        <link href="{'/public/css/admin_icons.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/general_text_content_styles.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
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
        <div id="page" class="no-initial-slideout">
            <div id="list-top-header">
                <span id="list-title">L.I.S.T.</span> <span id="list-subtitle">(Long-term Internet Storage of Tasks)</span> <span id="list-quick-info"><span class="list-current-user">{$list_teacher_account.fullname|escape:'html'}</span> <span class="list-current-task-set-open">{include file='partials/backend_general/open_task_set.tpl' inline}</span></span>
            </div>
            <div id="list-content">
                <div class="backend_body header_margin">{block name='main_content'}{/block}</div>
            </div>
            <div id="list-footer">
                {translate|sprintf:'Andrej Jursa':2013:{translate line='common_copyright_faculty'}:"<a href=\"{internal_url url='admin_settings/changelog'}\">{$this->config->item('list_version')}</a>" line='common_copyright_text'}
            </div>
            {add_admin_menu menu=$list_adminmenu current=$list_adminmenu_current}
        </div>
    </body>
</html>
