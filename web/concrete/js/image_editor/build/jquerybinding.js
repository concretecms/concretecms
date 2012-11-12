// End the ImageEditor object.
  window.c5_image_editor = im; // Safe keeping
  return im;
};

$.fn.ImageEditor = function (settings) {
  (settings === undefined && (settings = {}));
  settings.imageload = $.fn.dialog.hideLoader;
  var self = $(this);
  settings.container = self[0];
  (settings.width === undefined && (settings.width = self.width()));
  (settings.height === undefined && (settings.height = self.height()));
  $.fn.dialog.showLoader();
  var im = new ImageEditor(settings);
  im.bind('imageload', $.fn.dialog.hideLoader);
  return im;
};