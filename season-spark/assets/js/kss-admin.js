/**
 * kss-admin.js
 * Small admin helpers for the Season Spark settings page
 */
jQuery(document).ready(function ($) {
    // initialize WP color picker for any element with class kss-color
    if ( typeof $.fn.wpColorPicker === 'function' ) {
        $('.kss-color').wpColorPicker({
            defaultColor: '',
            change: function(event, ui){
                // update input value automatically handled by wpColorPicker
            },
            clear: function() { /* noop */ }
        });
    }

    // date inputs: try to use type=date where supported, else fallback to placeholder
    $('.kss-date').each(function(){
        var $el = $(this);
        try {
            $el.attr('type', 'date');
        } catch(e){
            $el.attr('placeholder', 'YYYY-MM-DD');
        }
    });

    // small UX: when toggling an effect off, gray out its inputs
    $('.kss-effect-card input[type="checkbox"]').on('change', function(){
        var $card = $(this).closest('.kss-effect-card');
        if ( $(this).is(':checked') ) {
            $card.removeClass('kss-disabled');
            $card.find('input,select,textarea').prop('disabled', false);
        } else {
            $card.addClass('kss-disabled');
            $card.find('input:not([type=checkbox]),select,textarea').prop('disabled', true);
        }
    }).trigger('change');
});
