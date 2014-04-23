{extends file='layouts/frontend_popup.tpl'}
{block title}{translate line='students_upload_avatar_page_title'}{/block}
{block main_content}
    <h1>{translate line='students_upload_avatar_page_title'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    <fieldset>
        <div><em>{translate line='students_upload_avater_crop_hint'}</em></div>
        <div style="text-align: center">
            <div style="display: inline-block; border: 5px solid black; border-radius: 3px;"><img src="{"public/images_users/students/{$list_student_account_model->id}/avatar/{$file_data.file_name}"|base_url}" alt="" id="cropbox_id" /></div>
        </div>
        <form action="{internal_url url='students/save_avatar'}" method="post">
            <div class="buttons">
                <input type="submit" name="submit_button" value="{translate line='students_upload_avatar_save_button'}" class="button" />
                <input type="hidden" name="file_name" value="{$file_data.file_name|escape:'html'}" />
                <input type="hidden" name="crop[x]" value="" />
                <input type="hidden" name="crop[y]" value="" />
                <input type="hidden" name="crop[width]" value="" />
                <input type="hidden" name="crop[height]" value="" />
            </div>
        </form>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var nothing_selected_notification = '{translate|addslashes line='students_upload_avatar_no_coordinates_selected'}';
</script>{/block}