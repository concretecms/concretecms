$('.ccm-image-block-hover')
  .mouseover(function () {
    $(this).attr('src', $(this).data('hover-src'));
  })
  .mouseout(function () {
    $(this).attr("src", $(this).data('default-src'));
  });