$(function () {
    // We move the background image and class from data-stripe-wrapper up to the closest
    // div containing the special custom template class. Because it's this DIV that should
    // have the parallax image close to it.

    var $parallax = $('div[data-stripe-wrapper=parallax]');

    $parallax.each(function () {
        var $wrapper = $parallax.closest('div.ccm-block-custom-template-parallax'),
            $children = $wrapper.children(),
            $inner = $children.first();//$('<div />').appendTo($wrapper).append($children);

        $wrapper.attr('data-stripe', 'parallax').attr('data-background-image', $parallax.attr('data-background-image'));
        $children.first().addClass('parallax-stripe-inner');

        $wrapper.parallaxize({
            variation: $wrapper.height()
        });

    });

});
