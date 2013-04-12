{foreach $tasks as $task}
<tr>
    <td>{$task->name|escape:'html'}</td>
    <td>{foreach $task->category->order_by('name', 'asc')->get_iterated() as $category}{if !$category@first}, {/if}{translate_text|escape:'html' text=$category->name}{/foreach}</td>
    <td>{$task->task_set->count()}</td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td>{$tasks->paged->current_page} / {$tasks->paged->total_pages} <pre>{$tasks->paged|print_r:true}</pre></td>
</tr>