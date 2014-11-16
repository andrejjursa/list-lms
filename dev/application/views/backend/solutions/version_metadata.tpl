<div class="version_metadata_panel">
    <div class="ip_address">
        <strong>{translate line='admin_solutions_valuation_version_metadata_ip_address'}:</strong>
        <span>{$solution_version->ip_address|default:{translate line='admin_solutions_valuation_version_metadata_ip_address_unknown'}}</span>
    </div>
    <div class="download_lock">
        <strong>{translate line='admin_solutions_valuation_version_metadata_download_lock'}:</strong>
        <span><input type="checkbox" class="download_lock_switch" name="solution_version[{$solution_version->id}][download_lock]" value="1"{if $solution_version->download_lock} checked="checked"{/if} data-change-url="{internal_url url="admin_solutions/solution_version_switch_download_lock/{$solution_version->id}"}" /></span>
    </div>
</div>