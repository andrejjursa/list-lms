jQuery(document).ready(function($) {
    
    var participant_last_course_id = '';
    
    $('#add_participant_form_id').activeForm({
        speed: 0,
        hiddenClass: 'hidden'
    }).setDisplayCondition('div.group_field', function() {
        var participant_course_id = this.findElement('select[name="participant[course]"]').val();
        if (participant_course_id > 0) {
            if (participant_course_id != participant_last_course_id) {
                var selected_id = this.findElement('input[name=participant_selected_group_id]').val() != undefined ? this.findElement('input[name=participant_selected_group_id]').val() : '0';
                var url = global_base_url + 'index.php/admin_participants/get_groups_from_course/' + participant_course_id + '/' + selected_id;
                var target = '#participant_group_id';
                api_ajax_load(url, target);
                participant_last_course_id = participant_course_id;
            }
            return true;
        }
        return false;
    }).setDisplayCondition('div.group_field_else', function() {
        return !this.isDisplayed('div.group_field');
    });
    $('#add_participant_form_id').activeForm().applyConditions();
        
    $('#participant_student_searchbox_id').autocomplete({
        source: function( request, response ) {
            var term = request.term;
            if (term in students_cache) {
                response(students_cache[term]);
                return;
            }
            var source_url = global_base_url + 'index.php/admin_participants/get_all_students';
            $.getJSON( source_url, request, function( data ) {
                students_cache[term] = data;
                response( data );
            });
        },
        minLength: 3,
        select: function( event, ui ) {
            if (ui.item != undefined) {
                $('#hidden_participant_student_name_id').val(ui.item.value);
                $('#hidden_participant_student_id_id').val(ui.item.id);
            } else {
                $('#hidden_participant_student_name_id').val('');
                $('#hidden_participant_student_id_id').val('');
            }
        }
    });
    
    $('#add_student_to_list_button_id').click(function() {
        if ($('#participant_student_searchbox_id').val().trim() != ''
            && $('#hidden_participant_student_name_id').val() != ''
            && $('#hidden_participant_student_id_id').val() != '') {
            var name = $('#hidden_participant_student_name_id').val();
            var id = $('#hidden_participant_student_id_id').val();
            if ($('#real_participants_list_id input[name="participant[students][' + id + ']"]').length == 0) {
                var id_record = $('<input type="hidden" name="participant[students][' + id + ']"]" value="' + id + '" />');
                var name_record = $('<input type="hidden" name="participant_students[' + id + ']"]" value="' + name + '" />');
                var option_record = $('<option value="' + id + '">' + name + '</option>');
                id_record.prependTo('#real_participants_list_id');
                name_record.prependTo('#names_participants_list_id');
                option_record.prependTo('#participants_names_id');
                $('#hidden_participant_student_name_id').val('');
                $('#hidden_participant_student_id_id').val('');
                $('#participant_student_searchbox_id').val('');
            }
        }
    });
    
    $('#remove_students_from_list_button_id').click(function() {
        var ids = $('#participants_names_id').val();
        if (ids != null && ids.length > 0) {
            for (var i = 0; i < ids.length; i++) {
                var id = ids[i];
                $('#names_participants_list_id input[name="participant_students[' + id + ']"]').remove();
                $('#real_participants_list_id input[name="participant[students][' + id + ']"]').remove();
                $('#participants_names_id option[value=' + id + ']').remove();
            }
        }
    });
    
});
