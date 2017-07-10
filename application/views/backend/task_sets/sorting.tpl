{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_task_sets_sorting_page_header'}{/block}
{block main_content}
    <h2>{translate line='admin_task_sets_sorting_page_header'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_task_sets_fieldset_legend_all_task_sets'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_students/table_constent'}" method="post" id="filter_form_id">
                <div class="field">
                    <label>{translate line='admin_task_sets_filter_form_field_course'}:</label>
                    <p class="input"><select name="filter[course]" size="1">{list_html_options options=$courses selected=$filter.course|intval}</select></p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_task_sets_filter_form_submit_button'}" class="button" />
                </div>
            </form>
        </div>
        <div id="table_content_id"></div>
    </fieldset>
{/block}