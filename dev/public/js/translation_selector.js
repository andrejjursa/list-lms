(function($){
    $.fn = $.extend($.fn, {
        translationSelector: function(options) {
            if (this.length == 0) {
                console.log('Can\'t start translation selector on empty set of elements.');
                return this;
            }
        } 
    });
    
    $.translationSelector = function(options) {
        
    };
    
    $.extend($.translationSelector, {
        
    });
}) (jQuery)