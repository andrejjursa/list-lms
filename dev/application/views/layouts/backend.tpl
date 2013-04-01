<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html" charset="utf-8" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link href="{'/public/css/admin_general.css'|base_url}" rel="stylesheet" type="text/css" media="screen" />
        <link href="{'/public/css/list-theme/jquery-ui-1.10.2.custom.css'|base_url}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/jqueryui-timepicker-addon.css'|base_url}" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var logout_question_text = "{translate line='adminmenu_logout_question_text'}";
            var jqueryui_datepicker_region = "{translate line='common_jqueryui_datepicker_region'|default:'en'}";
        </script>
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url}"></script>
        <script type="text/javascript" src="{$list_lang_js_messages|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.mousewheel-3.0.6.pack.js'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.fancybox.pack.js?v=2.1.4'|base_url}"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="{'/public/css/jquery.fancybox.css?v=2.1.4'|base_url}" />
        <link rel="stylesheet" type="text/css" media="screen" href="{'/public/css/jquery.fancybox-buttons.css?v=2.1.4'|base_url}" />
        <script type="text/javascript" src="{'/public/js/jquery.fancybox-buttons.js?v=2.1.4'|base_url}"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="{'/public/css/jquery.fancybox-thumbs.css?v=2.1.4'|base_url}" />
        <script type="text/javascript" src="{'/public/js/jquery.fancybox-thumbs.js?v=2.1.4'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/jquery.fancybox-media.js?v=1.0.0'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/jquery-ui-1.10.2.custom.min.js'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/jquery-ui-timepicker-addon.js'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/jMenu.jquery.min.js'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/admin_menu.js'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/api.js'|base_url}"></script>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
        {block name='custom_head'}{/block}
    </head>
    {include file='partials/backend_general/adminmenu.tpl' inline}
    <body>
        <div id="top_meta_informations">
            <div class="left"></div>
            <div class="right">{include file='partials/backend_general/metainfo_teacher.tpl' inline}</div>
            <div class="clear"></div>
        </div>
        <h1>L.I.S.T. - {translate line='adminmenu_administration_title'}</h1>
        <p>Long-term Internet Storage of Tasks</p>
        <nav>{make_adminmenu menu=$list_adminmenu current=$list_adminmenu_current}</nav>
        <div class="backend_body">{block name='main_content'}{/block}</div>
    </body>
</html>