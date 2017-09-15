<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link type="text/css" rel="stylesheet" media="screen" href="{'/public/css/help_index_general.css'|base_url|add_file_version}" />
        <link href="{'/public/css/font-awesome/css/font-awesome.min.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{'favicon.ico'|base_url}" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var login_form_url = '{internal_url|addslashes url='students/login/current_url/###URL###'}';
            var list_version = "{''|add_file_version}";
        </script>
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url|add_file_version}"></script>
        {foreach $list_internal_css_files as $file nocache}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file nocache}{$file.html}{/foreach}
        <script type="text/javascript">
            var reselectIndexItem = function(url) {
                var url_copy = url.toString();
                var url_without_base = url_copy.replace(global_base_url, '');
                var url_without_index = url_without_base.replace('index.php/', '');
                var url_without_help_show = url_without_index.replace('help/show/', '');
                {if $this->config->item('url_suffix')|trim neq ''}
                    var url_without_suffix = url_without_help_show.replace('{$this->config->item('url_suffix')}', '');
                {else}
                    var url_without_suffix = url_without_help_show;
                {/if}
                var index = '';
                var first_slash = false;
                for (var i = 0; i < url_without_suffix.length; i++) {
                    var char = url_without_suffix.charAt(i);
                    if (!first_slash) {
                        if (char !== '/') {
                            index = index + char;
                        } else {
                            index = index + '-SPLIT-';
                            first_slash = true;
                        }
                    } else {
                        if (char !== '/') {
                            index = index + char;
                        } else {
                            break;
                        }
                    }
                }
                jQuery('#index_slider a').removeClass('selected');
                jQuery('#index_slider a.INDEX-' + index).addClass('selected');
            };
        </script>
    </head>
    <body>
        <div id="mainwrap">
            <div id="index">
                <div id="index_slider">
                    {block name='index'}{/block}
                </div>
            </div>
            <div id="content">
                <iframe id="content_iframe_id" name="content_frame" src="{internal_url url='help/show/manual/welcome'}" onload="javascript:reselectIndexItem(this.contentWindow.location);" />
            </div>
        </div>
    </body>
</html>
