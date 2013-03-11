{foreach $translations as $constant => $translation}
    <tr class="constant_{$constant}">
    {include file='backend/translationseditor/table_row.tpl' inline}
    </tr>
{/foreach}