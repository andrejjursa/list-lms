<td class="constant" style="text-align: center">
    user_custom_<strong>{$constant}</strong>
</td>
{foreach $languages as $language}
<td style="text-align: center"><textarea name="translation[{$constant}][{$language@key}]">{$smarty.post[$constant][$language@key]|default:$translation[$language@key]}</textarea></td>
{/foreach}
<td style="text-align: center">
    <input type="button" name="button_save" value="{translate line='admin_translationseditor_table_row_button_save'}" />
    <input type="button" name="button_delete" value="{translate line='admin_translationseditor_table_row_button_delete'}" />
</td>