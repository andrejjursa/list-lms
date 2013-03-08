{foreach $list_flash_messages as $flash_message}
    {if $flash_message.type eq 'default'}
    <div class="flash_message message_default">
        {if $flash_message.message|substr:0:5|upper eq 'LANG:'}{translate line=$flash_message.message|substr:5}{else}{$flash_message.message}{/if}
    </div>
    {elseif $flash_message.type eq 'success'}
    <div class="flash_message message_success">
        {if $flash_message.message|substr:0:5|upper eq 'LANG:'}{translate line=$flash_message.message|substr:5}{else}{$flash_message.message}{/if}
    </div>
    {elseif $flash_message.type eq 'error'}
    <div class="flash_message message_error">
        {if $flash_message.message|substr:0:5|upper eq 'LANG:'}{translate line=$flash_message.message|substr:5}{else}{$flash_message.message}{/if}
    </div>
    {/if}
{/foreach}