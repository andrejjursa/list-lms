{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_settings_changelog_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_settings_changelog_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        {if $error}
            {include file='partials/backend_general/error_box.tpl' message=$error inline}
        {else}
            {foreach $log as $version => $content}
                <div class="changelog_version">
                    <p class="version"><strong>{translate line='admin_settings_changelog_version'} {$version}</strong> ({$content.date|date_format:{translate line='common_date_format'}})</p>
                    {if $content.reports}
                    <div class="version_reports">
                        {foreach $content.reports as $report}
                        <div class="report report_type_{$report->getType()|strtolower}">
                            <p class="type">{translate line="admin_settings_changelog_type_{$report->getType()|strtolower}"}:</p>
                            <p class="text">{$report->getText($this->lang->get_current_idiom())|changelog_to_html}</p>
                        </div>
                        {/foreach}
                    </div>
                    {/if}
                </div>
            {foreachelse}
                {include file='partials/backend_general/error_box.tpl' message='lang:admin_settings_changelog_empty' inline}
            {/foreach}
        {/if}
    </fieldset>
{/block}