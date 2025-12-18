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
    $('.kss-effect-card input[name$="[enabled]"]').on('change', function(){
        var $card = $(this).closest('.kss-effect-card');
        var $enabledCheckbox = $card.find('input[name$="[enabled]"]');
        if ( $enabledCheckbox.is(':checked') ) {
            $card.removeClass('kss-disabled');
            // enable all inputs
            $card.find('input,select,textarea').prop('disabled', false);
        } else {
            $card.addClass('kss-disabled');
            // disable all inputs except the main enabled checkbox so user can re-enable
            $card.find('input').not('input[name$="[enabled]"]').prop('disabled', true);
            $card.find('select,textarea').prop('disabled', true);
        }
        // Ensure schedule block visibility respects the enabled state
        $card.find('.kss-schedule-toggle').trigger('change');
    }).each(function(){
        // ensure initial state applies for each effect card
        $(this).trigger('change');
    });

    // Schedule toggle: show/hide schedule fields and label
    $('.kss-schedule-toggle').on('change', function(){
        var $card = $(this).closest('.kss-effect-card');
        var $block = $card.find('.kss-schedule-block');
        var enabled = $card.find('input[name$="[enabled]"]').is(':checked');

        if ( $(this).is(':checked') && enabled ) {
            $block.slideDown(150);
            $block.find('input,select,textarea').prop('disabled', false);
        } else {
            $block.slideUp(150);
            $block.find('input,select,textarea').prop('disabled', true);
        }
    }).each(function(){
        // Ensure initial state matches markup
        $(this).trigger('change');
    });

    // Hide color picker when custom cursor is enabled for an effect (color irrelevant)
    $('.kss-cursor-toggle').on('change', function(){
        var $card = $(this).closest('.kss-effect-card');
        if ( $(this).is(':checked') ) {
            $card.find('.kss-color').hide();
        } else {
            $card.find('.kss-color').show();
        }
    }).each(function(){ $(this).trigger('change'); });

    // For generic/custom graphics: show/hide media buttons when card enabled/disabled
    $('.kss-effect-card input[name$="[enabled]"]').each(function(){
        var $card = $(this).closest('.kss-effect-card');
        var isGeneric = $card.find('.kss-generic-bg-input, .kss-generic-cursor-input, .kss-media-btn').length > 0;
        if ( isGeneric ) {
            $(this).on('change', function(){
                var enabled = $(this).is(':checked');
                if ( enabled ) {
                    $card.find('.kss-media-btn, .kss-media-remove, .kss-media-label').prop('disabled', false).show();
                    $card.find('.kss-generic-bg-input, .kss-generic-cursor-input').prop('disabled', false);
                } else {
                    $card.find('.kss-media-btn, .kss-media-remove, .kss-media-label').prop('disabled', true).hide();
                    $card.find('.kss-generic-bg-input, .kss-generic-cursor-input').prop('disabled', true);
                }
            }).trigger('change');
        }
    });

    // Show/hide cursor media button when custom cursor toggle changes
    $('.kss-cursor-toggle').on('change', function(){
        var $card = $(this).closest('.kss-effect-card');
        var checked = $(this).is(':checked');
        if ( checked ) {
            $card.find('.kss-cursor-btn, .kss-cursor-label').show();
            $card.find('.kss-generic-cursor-input').prop('disabled', false);
        } else {
            $card.find('.kss-cursor-btn, .kss-cursor-label').hide();
            $card.find('.kss-generic-cursor-input').prop('disabled', true).val('');
            $card.find('.kss-cursor-remove, .kss-cursor-label').hide();
        }
    }).each(function(){ $(this).trigger('change'); });

    // Media button click: open WP media modal and write URL to hidden input
    $(document).on('click', '.kss-media-btn', function(e){
        e.preventDefault();
        var $btn = $(this);
        var $card = $btn.closest('.kss-effect-card');
        var isCursor = $btn.hasClass('kss-cursor-btn');
        var $input = isCursor ? $card.find('.kss-generic-cursor-input') : $card.find('.kss-generic-bg-input');
        var $label = isCursor ? $card.find('.kss-cursor-label') : $card.find('.kss-bg-label');

        if ( typeof wp === 'undefined' || typeof wp.media !== 'function' ) {
            alert('Media library not available.');
            return;
        }

        var frame = wp.media({
            title: isCursor ? 'Select cursor image' : 'Select background image',
            library: { type: 'image' },
            button: { text: 'Use Image' },
            multiple: false
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            if ( attachment && attachment.url ) {
                $input.val(attachment.url).trigger('change');
                $label.text(attachment.url.split('/').pop()).show();
                // show remove button
                if ( isCursor ) { $card.find('.kss-cursor-remove').show(); } else { $card.find('.kss-bg-remove').show(); }
            }
        });

        frame.open();
    });

    // Remove selected media
    $(document).on('click', '.kss-media-remove', function(e){
        e.preventDefault();
        var $btn = $(this);
        var $card = $btn.closest('.kss-effect-card');
        if ( $btn.hasClass('kss-bg-remove') ) {
            $card.find('.kss-generic-bg-input').val('').trigger('change');
            $card.find('.kss-bg-label').text('').hide();
            $btn.hide();
        } else {
            $card.find('.kss-generic-cursor-input').val('').trigger('change');
            $card.find('.kss-cursor-label').text('').hide();
            $btn.hide();
        }
    });

    // Dev tabs behavior
    $('.kss-tab').on('click', function(){
        var tab = $(this).data('tab');
        $('.kss-tab').removeClass('kss-tab-active');
        $(this).addClass('kss-tab-active');
        $('.kss-tab-content').removeClass('kss-tab-active');
        $('.kss-tab-content[data-content="'+tab+'"]').addClass('kss-tab-active');
    });

    // per-card save removed; top/bottom Save buttons submit the form normally
});
