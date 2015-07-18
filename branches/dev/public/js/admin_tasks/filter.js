jQuery(document).ready(function($) {
    
    var new_category = ' <span class="category">' + category_select_box + ' <a href="javascript:void(0);" class="button special remove_category" rel="">-</a></span>';
    var new_clause = '<div class="clause"> ( <span class="categories">' + new_category + '</span> ) [ <a href="javascript:void(0);" class="button special new_category" rel="">+</a> | <a href="javascript:void(0);" class="button special remove_clause" rel="">-</a> ]</div>';
    
    var redesign_clauses = function() {
        var clause_id = 0;
        $('#dynamic_categories_id div.clause').each(function() {
            var category_id = 0;
            $(this).find('span.category').each(function() {
                if (category_id == 0) {
                    $(this).addClass('first_category');
                } else {
                    $(this).removeClass('first_category');
                }
                $(this).attr('id', 'clause_' + clause_id + '_category_' + category_id + '_id');
                $(this).find('a.remove_category').attr('rel', clause_id + '|' + category_id);
                $(this).find('select').attr('name', 'filter[categories][clauses][' + clause_id + '][' + category_id + ']');
                category_id++;
            });
            if (clause_id == 0) {
                $(this).addClass('first_clause');
            } else {
                $(this).removeClass('first_clause');
            }
            $(this).attr('id', 'clause_' + clause_id + '_id');
            $(this).find('a.new_category').attr('rel', clause_id);
            $(this).find('a.remove_clause').attr('rel', clause_id);
            clause_id++;
        });
        if (reload_all_tasks != undefined && typeof reload_all_tasks == 'function') {
            reload_all_tasks();
        }
    };
    
    $(document).on('change', '#dynamic_categories_id select', function() {
        var select_value = $(this).val();
        $(this).find('option').each(function() {
            if ($(this).attr('value') == select_value) {
                $(this).attr('selected', 'selected');
            } else {
                $(this).removeAttr('selected');
            }
        });
        if (reload_all_tasks != undefined && typeof reload_all_tasks == 'function') {
            reload_all_tasks();
        }
    });
    
    $(document).on('click', '#dynamic_categories_id a.remove_clause', function(event) {
        event.preventDefault();
        var clause_id = $(this).attr('rel');
        $('#clause_' + clause_id + '_id').remove();
        redesign_clauses();
    });
    
    $(document).on('click', '#dynamic_categories_id a.remove_category', function(event) {
        event.preventDefault();
        var clause_category = $(this).attr('rel').split('|');
        var selector = '#clause_' + clause_category[0] + '_category_' + clause_category[1] + '_id';
        var selector_clause = '#clause_' + clause_category[0] + '_id';
        $(selector).remove();
        if ($(selector_clause).find('span.category').length == 0) {
            $(selector_clause).remove();
        }
        redesign_clauses();
    });
    
    $(document).on('click', '#dynamic_categories_id a.new_category', function(event) {
        var clause_id = $(this).attr('rel');
        var selector = '#clause_' + clause_id + '_id span.categories';
        $(selector).html($(selector).html() + new_category);
        redesign_clauses();
    });
    
    $(document).on('click', '#dynamic_categories_id a.new_clause', function(event) {
        event.preventDefault();
        var selector = '#dynamic_categories_id div.clauses';
        $(selector).html($(selector).html() + new_clause);
        redesign_clauses();
    });
    
});