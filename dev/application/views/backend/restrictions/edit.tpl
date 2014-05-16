{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_restrictions_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_restrictions_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $restriction->exists()}
        <fieldset>
            <form action="{internal_url url="admin_restrictions/update/{$restriction->id|intval}"}" method="post">
                <div class="field">
                    <label for="restriction_ip_addresses_id" class="required">{translate line='admin_restrictions_form_label_ip_addresses'}:</label>
                    <div class="input">
                        <input type="text" name="restriction[ip_addresses]" value="{$smarty.post.restriction.ip_addresses|default:$restriction->ip_addresses|escape:'html'}" id="restriction_ip_addresses_id" />
                    </div>
                    <p class="input"><em>{translate line='admin_restrictions_form_label_ip_addresses_hint'}</em></p>
                    {form_error field='restriction[ip_addresses]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="restriction_start_time_id" class="required">{translate line='admin_restrictions_form_label_start_time'}:</label>
                    <div class="input">
                        <input type="text" name="restriction[start_time]" value="{$smarty.post.restriction.start_time|default:$restriction->start_time|escape:'html'}" id="restriction_start_time_id" />
                    </div>
                    {form_error field='restriction[start_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="field">
                    <label for="restriction_end_time_id" class="required">{translate line='admin_restrictions_form_label_end_time'}:</label>
                    <div class="input">
                        <input type="text" name="restriction[end_time]" value="{$smarty.post.restriction.end_time|default:$restriction->end_time|escape:'html'}" id="restriction_end_time_id" />
                    </div>
                    {form_error field='restriction[end_time]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" value="{translate line='admin_restrictions_form_button_update'}" class="button" />
                </div>
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_restrictions_error_message_restriction_not_found' inline}
    {/if}
{/block}