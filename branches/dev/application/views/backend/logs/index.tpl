{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_logs_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_logs_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_logs/all_logs'}" method="post" id="filter_form_id">
                <div class="field">
                    <label>{translate line='admin_logs_filter_field_label_type'}:</label>
                    <div class="input">
                        <select name="filter[type]" size="1">
                            <option></option>
                            {list_html_options options=[1 => 'lang:admin_logs_log_type_1', 2 => 'lang:admin_logs_log_type_2', 3 => 'lang:admin_logs_log_type_3', 4 => 'lang:admin_logs_log_type_4'] selected=$filter.type}
                        </select>
                    </div>
                </div>
                <div class="columns">
                    <div class="col_50p">
                        <div class="field">
                            <label>{translate line='admin_logs_filter_field_label_interval_start'}:</label>
                            <div class="input">
                                <input type="text" name="filter[interval_start]" value="{$filter.interval_start|escape:'html'}" />
                            </div>
                        </div>
                        <div class="field">
                            <label>{translate line='admin_logs_filter_field_label_interval_end'}:</label>
                            <div class="input">
                                <input type="text" name="filter[interval_end]" value="{$filter.interval_end|escape:'html'}" />
                            </div>
                        </div>
                        <div class="field">
                            <label>{translate line='admin_logs_filter_field_label_ip_address'}:</label>
                            <div class="input">
                                <input type="text" name="filter[ip_address]" value="{$filter.ip_address|escape:'html'}" />
                            </div>
                            <p class="input"><em>{translate line='admin_logs_filter_field_label_ip_address_hint'}</em></p>
                        </div>
                    </div>
                    <div class="col_50p">
                        <div class="field">
                            <label>{translate line='admin_logs_filter_field_label_course'}:</label>
                            <div class="input">
                                <select name="filter[course]" size="1">
                                    <option></option>
                                    {list_html_options options=$courses selected=$filter.course}
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>{translate line='admin_logs_filter_field_label_student'}:</label>
                            <div class="input">
                                <select name="filter[student]" size="1">
                                    <option></option>
                                    {list_html_options options=$students selected=$filter.student}
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>{translate line='admin_logs_filter_field_label_teacher'}:</label>
                            <div class="input">
                                <select name="filter[teacher]" size="1">
                                    <option></option>
                                    {list_html_options options=$teachers selected=$filter.teacher}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="buttons">
                    <input type="submit" value="{translate line='admin_logs_filter_submit_button'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter[order_by_field]" value="{$filter.order_by_field|default:'created'}" />
                    <input type="hidden" name="filter[order_by_direction]" value="{$filter.order_by_direction|default:'desc'}" />
                </div>
            </form>
        </div>
        <div id="table_content_id"></div>
    </fieldset>
{/block}