$('.ccm-image-block-hover')
  .mouseover(function () {
    $(this).attr('src', $(this).data('hover-src'));
    $(this).parent('picture').find('source').attr('srcset', $(this).data('hover-src'));
  })
  .mouseout(function () {
    $(this).attr("src", $(this).data('default-src'));
    $(this).parent('picture').find('source').attr('srcset', $(this).data('default-src'));
  });