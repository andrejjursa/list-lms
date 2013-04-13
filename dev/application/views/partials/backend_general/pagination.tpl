{translate|sprintf:$paged->current_page:$paged->total_pages line='common_pagination_page_of_pages'} | {translate|sprintf:{$paged->current_row+1}:{$paged->current_row+$paged->items_on_page}:$paged->total_rows line='common_pagination_records'} | {translate line='common_pagination_page'}: <select name="paging_page" size="1">
    {for $page=1 to $paged->total_pages}
    <option value="{$page}"{if $page eq $paged->current_page} selected="selected"{/if}>{$page}</option>
    {/for}
</select> | {translate line='common_pagination_rows_per_page'}: <select name="paging_rows_per_page" size="1">{html_options options=[25=>25, 50=>50, 100=>100] selected=$paged->page_size}</select>