{if $message}
<div class="flash_message message_error">
    {translate_text text=$message}{if $back_url}<br /><br /><a href="{$back_url}" class="button delete">{translate line='common_button_back'}</a>{/if}
</div>
{/if}