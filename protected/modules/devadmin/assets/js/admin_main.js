jQuery(document).ready(function() {
    hideNonRequired();
});

editorCallBack = function() {
    //hideSpollers();
};

hideNonRequired = function() {
    var spollers = $('.non-required', $('#content .form'));
    
    $(spollers).each(function(i, item) {
        var label = $(item).parent().find('label');
        $(label).click(function() {
            $(item).toggleClass('hidden');
        }).addClass('open-link');
    });
};
