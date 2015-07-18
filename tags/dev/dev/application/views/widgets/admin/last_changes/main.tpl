{extends file='widgets/layouts/admin/simple.tpl'}
{block widget_name}{translate line='widget_admin_last_changes_widget_title'}{/block}
{block widget_content}
    {if $error}
        {include file='partials/backend_general/error_box.tpl' message=$error inline}
    {else}
        {if $content}
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
        {else}
            {include file='partials/backend_general/error_box.tpl' message='lang:admin_settings_changelog_empty' inline}
        {/if}
    {/if}
{/block}