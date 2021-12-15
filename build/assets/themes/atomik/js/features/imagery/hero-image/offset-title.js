computeOffsetTitleHeroImages = function() {
    var $offsetTitleHeroImages = $('.ccm-block-hero-image-offset-title')
    if ($offsetTitleHeroImages.length) {
        $offsetTitleHeroImages.each(function() {
            var height = $(this).find('img').css('height')
            var heightRatio = $(this).find('img').attr('data-height-ratio')
            if (height && heightRatio) {
                var containerHeight = parseInt(height) * heightRatio;
                var containerMarginY = parseInt(height) - containerHeight;
                $(this).find('.ccm-block-hero-image-offset-image-container').css('height', containerHeight + 'px')
                $(this).find('.ccm-block-hero-image-offset-image-container img').css('margin-top', '-' + containerMarginY + 'px')
                $(this).css('height', 'auto')
            }
        })
    }
}
$(function() {
    computeOffsetTitleHeroImages()
    $(window).on('resize', function() {
        computeOffsetTitleHeroImages()
    })
})
