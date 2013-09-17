jQuery(document).ready(function($) {
    
    $('#new_test_form_id').activeForm({
        speed: 0
    });
    
    var old_test_type = '';
    
    $('#new_test_form_id div.field.test_subtype_field').setActiveFormDisplayCondition(function() {
        var test_type = $('#test_type_id').val();
        if (test_type !== '') {
            if (old_test_type !== test_type) {
                old_test_type = test_type;
                var select_element = $('#test_subtype_id');
                select_element.html('');
                var empty_option = $('<option value=""></option>');
                empty_option.appendTo(select_element);
                var option_subtypes = subtypes !== undefined && subtypes[test_type] !== undefined ? subtypes[test_type] : {};
                var selected_option = $('#test_subtype_selected_id').val();
                for (var subtype in option_subtypes) {
                    var html_option = $('<option></option>');
                    html_option.attr('value', subtype);
                    html_option.text(option_subtypes[subtype]);
                    if (subtype === selected_option) { html_option.attr('selected', 'selected'); }
                    html_option.appendTo(select_element);
                }
            }
            return true;
        } else {
            return false;
        }
    });
    
    $('#new_test_form_id div.field.test_subtype_field_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.test_subtype_field');
    });
    
    $('#new_test_form_id').activeForm().applyConditions();
    
});