{extends file='layouts/backend.tpl'}
{block main_content}
    <h2>{translate line='admin_students_csv_import_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <div id="progress_bar_wrap_id">
            <div class="progress_bar"></div>
        </div>
        <div id="import_log_id"></div>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var csv_data = {$csv_content|json_encode};
    var csv_rows = {$csv_content|count};
    var url_config = '{$url_config}';
    var password_type = '{$password_type}';
    var send_mail = {$send_mail|intval};
    var assign_to_course = {$assign_to_course|default:'0'};
</script>{/block}