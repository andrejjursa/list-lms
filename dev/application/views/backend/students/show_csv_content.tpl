{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_students_csv_import_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_students_csv_import_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $error_message}<div class="flash_message message_error">{translate_text text=$error_message}</div>{/if}
    <fieldset>
        <form action="{internal_url url="admin_students/csv_import_screen/{$url_config}"}" method="post" id="csv_form_id">
            <div class="controlls"><button type="button" class="button select_all special">{translate line='admin_students_csv_import_button_select_all'}</button> <button type="button" class="button select_none special">{translate line='admin_students_csv_import_button_select_none'}</button> <input type="submit" name="submit_button" value="{translate line='admin_students_csv_import_button_submit_do_import'}" class="button" /></div>
            <div id="csv_table_content_id">
                <table class="csv_table">
                    <thead>
                        <tr>
                            <th></th>
                            {for $col = 1 to $csv_cols}
                            <th class="col_{$col}">
                                <select name="col[{$col}]" size="1">{list_html_options options=['no_import'=>'lang:admin_students_csv_import_col_option_no_import', 'is_firstname'=>'lang:admin_students_csv_import_col_option_is_firstname', 'is_lastname'=>'lang:admin_students_csv_import_col_option_is_lastname', 'is_fullname'=>'lang:admin_students_csv_import_col_option_is_fullname', 'is_email'=>'lang:admin_students_csv_import_col_option_is_email'] selected=$smarty.post.col[$col]}</select>
                            </th>
                            {/for}
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $csv_array as $line}
                        <tr class="row_{$line@key}{if $smarty.post.row[$line@key] || !isset($smarty.post.row)} selected{/if}">
                            <td><input type="checkbox" name="row[{$line@key}]" value="1"{if $smarty.post.row[$line@key] || !isset($smarty.post.row)} checked="checked"{/if} /></td>
                            {for $row = 0 to $csv_cols - 1}
                            <td>{$line[$row]}</td>
                            {/for}
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </form>   
    </fieldset>
{/block}