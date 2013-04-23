{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_task_sets_page_header'}{/block}
{block main_content}
	<h2>{translate line='admin_task_sets_page_header'}</h2>
	{include file='partials/backend_general/flash_messages.tpl' inline}
	<fieldset>
		<legend>{translate line='admin_task_sets_fieldset_legend_new_task_set'}</legend>
	</fieldset>
	<fieldset>
		<legend>{translate line='admin_task_sets_fieldset_legend_all_task_sets'}</legend>
	</fieldset>
{/block}