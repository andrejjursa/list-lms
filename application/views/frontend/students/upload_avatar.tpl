{extends file='frontend_popup.tpl'}
{block title}{translate line='students_upload_avatar_page_title'}{/block}
{block main_content}
    <h1>{translate line='students_upload_avatar_page_title'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    <fieldset>
        <form action="{internal_url url='students/crop_avatar'}" method="post" enctype="multipart/form-data">
            <div class="field">
                <label for="file_id" class="required">{translate line='students_upload_avatar_label_file'}:</label>
                <p class="input"><input type="file" name="file" id="file_id" /></p>
                <p class="input"><em>{translate line='students_upload_avatar_file_hint'}</em></p>
                {if $upload_error}<div class="input">{include file='partials/frontend_general/error_box.tpl' message=$upload_error inline}</div>{/if}
            </div>
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='students_upload_avatar_upload_button'}" class="button" />
            </div>
        </form>
    </fieldset>
{/block}