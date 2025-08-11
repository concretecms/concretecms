$(function () {
    // We move the background image and class from data-stripe-wrapper up to the closest
    // div containing the special custom template class. Because it's this DIV that should
    // have the parallax image close to it.

    // Note, this relies on parallax-image.js loading from the block's custom template js/ directory. This is done this
    // way for backward compatibility, but a better way to do this would be to add this functionality directly to the
    // theme

    var $parallax = $('div[data-stripe-wrapper=parallax]');

    $parallax.each(function () {
        var $self = $(this);
            $wrapper = $self.closest('div.ccm-block-custom-template-parallax'),
            $children = $wrapper.children(),
            $inner = $children.first();

        $wrapper.attr('data-stripe', 'parallax').attr('data-background-image', $self.attr('data-background-image'));
        $inner.addClass('parallax-stripe-inner');

        $wrapper.parallaxize({
            speed: 0.2
        });

    });

});
