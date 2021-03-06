<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <title>{block name='title'}{/block}</title>
        <link href="{'/public/css/admin_general.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" media="screen" />
        <link href="{'/public/css/admin_icons.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link href="{'/public/css/font-awesome/css/font-awesome.min.css'|base_url|add_file_version}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{'favicon.ico'|base_url}" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var login_form_url = "{internal_url|addslashes url='admin_teachers/login/current_url/###URL###'}";
            var jqueryui_datepicker_region = "{translate|addslashes line='common_jqueryui_datepicker_region'|default:'en'}";
            var list_version = "{''|add_file_version}";
        </script>
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url|add_file_version}"></script>
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
    </head>
    <body>
        <div class="loginbox">{block name='main_content'}{/block}</div>
	{if $list_version_info and is_array($list_version_info) and count($list_version_info)}
		<div class="version_info">
			<div class="changelog_version">
				<p class="version">{translate line='admin_settings_changelog_version'} {$version} ({$list_version_info.date|date_format:{translate line='common_date_format'}})</p>
				{if $list_version_info.reports}
				<div class="version_reports">
					{foreach $list_version_info.reports as $report}
					<div class="report report_type_{$report->getType()|strtolower}">
						<p class="type">{translate line="admin_settings_changelog_type_{$report->getType()|strtolower}"}:</p>
						<p class="text">{$report->getText($this->lang->get_current_idiom())|changelog_to_html}</p>
					</div>
					{/foreach}
				</div>
				{/if}
			</div>
		</div>
	{/if}
    </body>
</html>
