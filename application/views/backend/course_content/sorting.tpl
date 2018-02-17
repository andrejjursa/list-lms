{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_course_content_sorting_page_header'}{/block}
{block main_content}
    <h2>{translate line='admin_course_content_sorting_page_header'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_course_content_fieldset_sort_all_content'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_course_content/sorting'}" method="post" id="filter_form_id">
                <div class="field">
                    <label>{translate line='admin_course_content_filter_by_course'}:</label>
                    <p class="input"><select name="filter[course]" size="1">{list_html_options options=$courses selected=$filter.course|intval}</select></p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_course_content_filter_submit_button'}" class="button" />
                </div>
            </form>
        </div>
        <div id="table_content_id"></div>
    </fieldset>
{/block}