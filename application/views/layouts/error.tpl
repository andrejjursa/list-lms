<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link type="text/css" rel="stylesheet" media="screen" href="{'/public/css/error_general.css'|base_url|add_file_version}" />
        <link rel="shortcut icon" href="{'favicon.ico'|base_url}" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var login_form_url = '{internal_url|addslashes url='students/login/current_url/###URL###'}';
            var list_version = "{''|add_file_version}";
        </script>
    </head>
    <body>
        <div id="mainwrap">
            <div id="error_box">
            <div class="internal_padding">{block name='main_content'}{/block}</div></div>
        </div>
    </body>
</html>