{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_students_csv_import_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_students_csv_import_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="{internal_url url='admin_students/upload_csv_file'}" method="post" enctype="multipart/form-data">
            <div class="field">
                <label for="csv_file_id" class="required">{translate line='admin_students_csv_import_form_label_file'}:</label>
                <p class="input"><input type="file" name="csv_file" id="csv_file_id" /></p>
                {if $error_message}<p class="error"><span class="message">{translate_text text=$error_message}</span></p>{/if}
            </div>
            <div class="field">
                <label for="csv_data_delimiter_id" class="required">{translate line='admin_students_csv_import_form_label_delimiter'}:</label>
                <p class="input"><input type="text" name="csv_data[delimiter]" value="{$smarty.post.csv_data.delimiter|default:','|escape:'html'}" size="1" maxlength="1" id="csv_data_delimiter_id" /></p>
                {form_error field='csv_data[delimiter]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="csv_data_enclosure_id" class="required">{translate line='admin_students_csv_import_form_label_enclosure'}:</label>
                <p class="input"><input type="text" name="csv_data[enclosure]" value="{$smarty.post.csv_data.enclosure|default:'"'|escape:'html'}" size="1" maxlength="1" id="csv_data_enclosure_id" /></p>
                {form_error field='csv_data[enclosure]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="field">
                <label for="csv_data_escape_id" class="required">{translate line='admin_students_csv_import_form_label_escape'}:</label>
                <p class="input"><input type="text" name="csv_data[escape]" value="{$smarty.post.csv_data.escape|default:'\\'|escape:'html'}" size="1" maxlength="1" id="csv_data_escape_id" /></p>
                {form_error field='csv_data[escape]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='admin_students_csv_import_form_submit_button_upload'}" class="button" />
            </div>
        </form>
    </fieldset>
{/block}