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
        <legend>{translate|sprintf:{translate_text|escape:'html' text=$group->name} line='admin_rooms_fieldset_legen_all_rooms'}</legend>
        <table class="rooms_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{translate line='admin_rooms_table_header_field_name'}</th>
                    <th>{translate line='admin_rooms_table_header_field_time_day'}</th>
                    <th>{translate line='admin_rooms_table_header_field_time_begin'}</th>
                    <th>{translate line='admin_rooms_table_header_field_time_end'}</th>
                    <th>{translate line='admin_rooms_table_header_field_capacity'}</th>
                    <th class="controlls" colspan="2">{translate line='admin_rooms_table_header_controlls'}</th>
                </tr>
            </thead>
            <tbody id="rooms_table_body_id">
            </tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var current_group_id = {$group->id|default:'0'};
    var messages = {
        delete_question: '{translate line="admin_rooms_delete_room_question"}',
        after_delete: '{translate line="admin_rooms_room_deleted_message"}',
    }; 
</script>{/block}