{extends file='layouts/backend_popup.tpl'}
{block title}{translate line='admin_rooms_page_title'}{/block}
{block main_content}
    <h3>{translate line='admin_rooms_page_title'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_rooms_fieldset_legen_new_room'}</legend>
        <form action="{internal_url url='admin_rooms/create'}" method="post" id="new_room_form_id">{include file='backend/rooms/new_room_form.tpl' inline}</form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_rooms_fieldset_legen_all_rooms'}</legend>
        <table class="rooms_table">
            <thead>
                <tr>
                    <th></th>
                </tr>
            </thead>
            <tbody id="rooms_table_body_id">
            </tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var current_group_id = {$group->id|default:'0'};
</script>{/block}