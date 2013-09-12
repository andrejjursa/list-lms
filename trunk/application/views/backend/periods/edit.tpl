{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_periods_page_title_edit'}{/block}
{block main_content}
    <h2>{translate line='admin_periods_page_title_edit'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $period->exists() or $smarty.post.period}
        <fieldset>
            <form action="{internal_url url="admin_periods/update"}" method="post">
                <div class="field">
                    <label for="period_name_id" class="required">{translate line='admin_periods_form_label_name'}:</label>
                    <p class="input"><input type="text" name="period[name]" value="{$smarty.post.period.name|default:$period->name|escape:'html'}" id="period_name_id" /></p>
                    {form_error field='period[name]' left_delimiter='<p class="error"><span class="message">' right_delimiter='</span></p>'}
                </div>
                <div class="buttons">
                    <input type="submit" name="save_button" value="{translate line='admin_periods_form_save_button'}" class="button" /> <a href="{internal_url url='admin_periods'}" class="button special">{translate line='common_button_back'}</a>
                </div>
                <input type="hidden" name="period_id" value="{$smarty.post.period_id|default:$period->id|intval}" />
            </form>
        </fieldset>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_periods_error_period_not_found' inline}
    {/if}
{/block}