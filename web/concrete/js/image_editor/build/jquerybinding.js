// End the ImageEditor object.
  window.c5_image_editor = im; // Safe keeping
  return im;
};
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

  $('div.controls').children('ul.nav').children().click(function(){
    if ($(this).hasClass('active')) return false;
    im.trigger('ChangeNavTab',$(this).text().toLowerCase());
    return false;
  });
  $('div.controlset').find('div.control').children('div.contents').slideUp(0);
  $('div.controlset').find('h4').click(function(){
    $('div.controlset').find('h4').not($(this)).removeClass('active');
    var ns = $(this).parent().attr('data-namespace');
    im.trigger('ChangeActiveAction',"ControlSet_"+ns);
  });
  im.bind('imageload', $.fn.dialog.hideLoader);
  return im;
};