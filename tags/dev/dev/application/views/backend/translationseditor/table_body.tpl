{foreach $translations as $constant => $translation}
    <tr class="constant_{$constant} row_of_constant">
    {include file='backend/translationseditor/table_row.tpl' inline}
    </tr>
{/foreach}