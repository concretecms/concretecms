// End the ImageEditor object.
  window.c5_image_editor = im; // Safe keeping
  return im;
};

$('div.controlset').find('div.control').slideUp(0);
$('div.controlset').find('h4').click(function(){
  $('div.controlset').find('h4').not($(this)).removeClass('active');
  var ns = $(this).parent().attr('data-namespace');
  im.trigger('changecontrolset',ns);
});
$.fn.ImageEditor = function (settings) {
  (settings === undefined && (settings = {}));
  settings.imageload = $.fn.dialog.hideLoader;
  var self = $(this);
  settings.container = self[0];
  if (self.height() == 0) {
    setTimeout(function(){
      self.ImageEditor(settings);
    },50);
    return;
  }
  (settings.width === undefined && (settings.width = self.width()));
  (settings.height === undefined && (settings.height = self.height()));
  $.fn.dialog.showLoader();
  var im = new ImageEditor(settings);
  im.bind('imageload', $.fn.dialog.hideLoader);
  return im;
};