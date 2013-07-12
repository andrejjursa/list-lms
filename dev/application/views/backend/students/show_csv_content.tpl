{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_students_csv_import_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_students_csv_import_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $error_message}<div class="flash_message message_error">{translate_text text=$error_message}</div>{/if}
    <fieldset>
        <form action="{internal_url url="admin_students/csv_import_screen/{$url_config}"}" method="post" id="csv_form_id">
            <div class="controlls">
                <button type="button" class="button select_all special">{translate line='admin_students_csv_import_button_select_all'}</button>
                <button type="button" class="button select_none special">{translate line='admin_students_csv_import_button_select_none'}</button>
                <input type="submit" name="submit_button" value="{translate line='admin_students_csv_import_button_submit_do_import'}" class="button" />
                <select name="password_type" size="1">
                    {list_html_options
                        options=['default'=>'lang:admin_students_csv_import_password_type_default_password', 'random'=>'lang:admin_students_csv_import_password_type_random_password', 'blank'=>'lang:admin_students_csv_import_password_type_blank_password']
                        selected=$smarty.post.password_type}
                </select>
                <input type="checkbox" name="send_mail" value="1" id="send_mail_checkbox_id"{if $smarty.post.send_mail} checked="checked"{/if} /> <label for="send_mail_checkbox_id">{translate line='admin_students_csv_import_send_mail_checkbox'}</label>
                <select name="assign_to_course" size="1">
                    <option value="">{translate line='admin_students_csv_import_assign_to_course_do_not_assign'}</option>
                    {list_html_options
                        options=$courses
                        selected=$smarty.post.assign_to_course}
                </select>
            </div>
            <div id="csv_table_content_id">
                <div class="overflow">
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
            </div>
        </form>   
    </fieldset>
{/block}