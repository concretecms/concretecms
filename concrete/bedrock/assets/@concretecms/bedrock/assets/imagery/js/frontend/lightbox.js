$('a[data-concrete-link-lightbox=image]').each(function() {
    var me = $(this)
    me.magnificPopup({
        type: 'image',
        removalDelay: 500, // delay removal by X to allow out-animation
        callbacks: {
            beforeOpen: function () {
                // just a hack that adds mfp-anim class to markup
                this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim')
                this.st.mainClass = 'mfp-zoom-in'
            }
        },
        closeOnContentClick: true,
        midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
    })
})
$('a[data-concrete-link-lightbox=iframe]').each(function() {
    var me = $(this)
    var width = 500
    var height = 400

    if ($(this).attr('data-concrete-link-lightbox-width') && $(this).attr('data-concrete-link-lightbox-height')) {
        width = $(this).attr('data-concrete-link-lightbox-width')
        height = $(this).attr('data-concrete-link-lightbox-height')
    }

    me.magnificPopup({
        type: 'iframe',
        callbacks: {
            beforeOpen: function () {
                this.st.iframe.markup = this.st.iframe.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim')
                this.st.mainClass = 'mfp-zoom-in'
                var magnificPopup = $.magnificPopup.instance
                $(magnificPopup.contentContainer).css('maxWidth', width + 'px').css('maxHeight', height + 'px')
            }
        },
        iframe: {
            patterns: {
                website: {
                    index: '',
                    src: '%id%'
                }
            }
        },
        closeOnContentClick: true,
        midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
    })
})
