/**
 * Event triggers for the lightbox plugin.
 */
!function(global, $) {
    'use strict';
    $('a[data-concrete5-link-launch="lightbox-image"],a[data-concrete5-link-launch="lightbox"]').each(function() {
        var me = $(this),
            type = me.data('concrete5-link-type') || 'ajax';
        me.magnificPopup({
            type: type,
            removalDelay: 500, //delay removal by X to allow out-animation
            callbacks: {
                beforeOpen: function () {
                    // just a hack that adds mfp-anim class to markup
                    this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
                    this.st.mainClass = 'mfp-zoom-in';
                }
            },
            closeOnContentClick: true,
            midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
        });
    });

}(this, $);
