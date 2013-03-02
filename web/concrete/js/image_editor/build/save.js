im.save = function() {
  im.background.hide();
  if (im.activeElement !== undefined && typeof im.activeElement.releaseStroke == 'function') {
    im.activeElement.releaseStroke();
  }
  im.stage.setScale(1);
  im.setActiveElement(im.stage);

  im.fire('ChangeActiveAction');
  im.fire('changeActiveComponent');

  $(im.stage.getContainer()).hide();

  var startx = Math.round(im.center.x - (im.saveWidth / 2)),
      starty = Math.round(im.center.y - (im.saveHeight / 2)),
      oldx = im.stage.getX(),
      oldy = im.stage.getY(),
      oldwidth = im.stage.getWidth(),
      oldheight = im.stage.getHeight();

  im.stage.setX(-startx);
  im.stage.setY(-starty);
  im.stage.setWidth(Math.max(im.stage.getWidth(),im.saveWidth));
  im.stage.setHeight(Math.max(im.stage.getHeight(),im.saveHeight));
  im.stage.draw();


  im.showLoader('Saving..');
  im.stage.toDataURL({
    width:im.saveWidth,
    height:im.saveHeight,
    callback:function(data){
      var img = $('<img/>').attr('src',data);
      $.fn.dialog.open({element:$(img).width(250)});
      im.hideLoader();
      im.background.show();
      im.stage.setX(oldx);
      im.stage.setY(oldy);
      im.stage.setWidth(oldwidth);
      im.stage.setHeight(oldheight);
      im.stage.setScale(im.scale);
      im.stage.draw();
      $(im.stage.getContainer()).show();
    }
  })
};