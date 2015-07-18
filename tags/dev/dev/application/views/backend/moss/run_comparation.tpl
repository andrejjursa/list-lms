{if $moss_enabled}
<fieldset class="basefieldset">
    <legend>{translate line='admin_moss_run_comparation_fieldset_legend_run'}</legend>
    {if $all_extracted}
        <div id="protocol_id">
            <p>{translate line='admin_moss_run_comparation_please_stand_by_message'}</p>
        </div>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_moss_run_comparation_error_files_not_exracted' inline}
    {/if}
</fieldset>
{/if}
{include file='backend/moss/list_solutions.tpl'}
{if $all_extracted and $moss_enabled}
<script type="text/javascript">
    exec_comparator('{$path|addslashes}', {$moss_config|json_encode});
</script>    
{/if}