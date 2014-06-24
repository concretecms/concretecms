// End the ImageEditor object.


  im.setActiveElement(im.stage);

  window.c5_image_editor = im; // Safe keeping
  window.im = im;
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
  self.closest('.ui-dialog').find('.ui-resizable-handle').hide();
  self.height("-=30");
  $('div.editorcontrols').height(self.height() - 90);
  self.width("-=330").parent().width("-=330").children('div.bottomBar').width("-=330");
  (settings.width === undefined && (settings.width = self.width()));
  (settings.height === undefined && (settings.height = self.height()));
  $.fn.dialog.showLoader();
  var im = new ImageEditor(settings);

  var context = im.domContext;
  im.on('ChangeActiveAction',function(e, data){
    if (!data)
      $('h4.active',context).removeClass('active');
  });
  im.on('ChangeActiveComponent',function(e, data){
    if (!data)
      $('h4.active',context).removeClass('active');
  });
  $('div.controls > div.controlscontainer',context).children('div.save').children('button.save').click(function(){
    im.save();
  }).end().children('button.cancel').click(function(){
    if (confirm("Are you certain?"))
      $.fn.dialog.closeTop();
  });
  $('div.controls > div.controlscontainer',context).children('ul.nav').children().click(function(){
    if ($(this).hasClass('active')) return false;
    $('div.controls > div.controlscontainer',context).children('ul.nav').children().removeClass('active');
    $(this).addClass('active');
    im.trigger('ChangeNavTab',$(this).text().toLowerCase());
    return false;
  });
  $('div.controlset',context).find('div.control').children('div.contents').slideUp(0)
  .end().end().find('h4').click(function(){
    if ($(this).parent().hasClass('disabled')) return;
    $(this).addClass('active');
    $('div.controlset',context).find('h4').not($(this)).removeClass('active');
    var ns = $(this).parent().attr('data-namespace');
    im.trigger('ChangeActiveAction',"ControlSet_"+ns);
  });

  $('div.component',context).find('div.control').children('div.childrenontents').slideUp(0).hide()
  .end().end().find('h4').click(function(){
    if ($(this).hasClass('active')) return false;
    $(this).addClass('active');
    $('div.component',context).children('h4').not($(this)).removeClass('active');
    var ns = $(this).parent().attr('data-namespace');
    im.trigger('ChangeActiveComponent',"Component_"+ns);
  });
  $('div.components').hide();

  im.bind('imageload', $.fn.dialog.hideLoader);
  return im;
};
$.fn.slideOut = function(time,callback) {
  var me = $(this),
      startWidth = me.width(),
      totalWidth = 300;
  me.css('overflow-y','auto');
  if (startWidth == totalWidth) {
    me.animate({width:totalWidth},0,callback);
    return this;
  };
  me.width(startWidth).animate({width:totalWidth},time || 300,callback || function(){});
  return this;
};
$.fn.slideIn = function(time,callback) {
  var me = $(this);
  me.css('overflow-y','hidden');
  if (me.width() === 0) {
    me.animate({width:0},0,callback);
    return this;
  };
  me.animate({width:0},time || 300,callback || function(){});
  return this;
};
