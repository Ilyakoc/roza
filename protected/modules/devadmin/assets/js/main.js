jQuery.toggleText = function(id, text) {
    if ($(id).val() == '') { $(id).val(text); }

    $(id).bind({
        focus: function() {
            if ($(this).val() == text) {
                $(this).val('');
                $(this).toggleClass('focus');
            }
        },
        blur: function () {
            if ($(this).val() == '') {
                $(this).val(text) ;
                $(this).toggleClass('focus');
            }
        }
    });
}
