<div class="ip_address">
    <strong>{translate line='admin_solutions_valuation_version_metadata_ip_address'}:</strong>
    <span>{$solution_version->ip_address|default:{translate line='admin_solutions_valuation_version_metadata_ip_address_unknown'}}</span>
</div>
<div class="download_lock">
    <strong>{translate line='admin_solutions_valuation_version_metadata_download_lock'}:</strong>{if $solution_version->download_lock}{$set_download_lock = 0}{else}{$set_download_lock = 1}{/if}
    <span><input type="checkbox" class="download_lock_switch" name="solution_version[{$solution_version->id}][download_lock]" value="1"{if $solution_version->download_lock} checked="checked"{/if} data-change-url="{internal_url url="admin_solution/solution_version_set_download_lock/{$solution_version->id}/{$set_download_lock}"}" data-change-to="{$set_download_lock}" /></span>
</div>