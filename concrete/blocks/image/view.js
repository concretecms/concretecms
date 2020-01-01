$(function() {
    var hoverImg = $('.ccm-image-block-hover');

    hoverImg.each(function(index) {
        var hoverImgPictureParent = $(this).parent('picture');
        if (hoverImgPictureParent) {
            hoverImgPictureParent
                .mouseover(function() {
                    $(this).find('source').attr('srcset', $(this).data('hover-src'));
                    $(this).find('img').attr('src', $(this).data('hover-src'));
                })
                .mouseout(function() {
                    $(this).find('source').attr('srcset', $(this).data('default-src'));
                    $(this).find('img').attr('src', $(this).data('default-src'));
                });
        } else {
            $(this)
                .mouseover(function() {
                    $(this).attr('src', $(this).data('hover-src'));
                })
                .mouseout(function() {
                    $(this).attr('src', $(this).data('default-src'));
                });
        }
    });
});