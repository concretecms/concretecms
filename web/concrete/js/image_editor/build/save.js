im.save = function() {
  im.background.hide();
  if (im.activeElement !== undefined && typeof im.activeElement.releaseStroke == 'function') {
    im.activeElement.releaseStroke();
  }
  im.stage.setScale(1);
  im.setActiveElement(im.stage);

  im.fire('ChangeActiveAction');
  im.fire('changeActiveComponent');
  im.background.hide();
  im.foreground.hide();

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
      im.foreground.show();
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

im.adjustSavers = function() {
  im.foreground.autoCrop = false;
  im.background.autoCrop = false;
  var i, e, c = im.stage.getChildren(), l = c.length, count = {min:{x:false,y:false},max:{x:false,y:false}};
  for (i=0;i<l;i++) {
    if (c[i].autoCrop === false) continue;
    for (e in c[i].children) {
      var pos = c[i].children[e].getPosition(), size = c[i].children[e].getSize(), center = {x:pos.x + size.width / 2, y:pos.y + size.height / 2};
      if (count.min.x === false) {
        count.min.x = pos.x;
        count.min.y = pos.y;
        count.max.x = pos.x - size.width;
        count.max.y = pos.y + size.height;
      }
      if (count.min.x > pos.x) count.min.x = pos.x;
      if (count.min.y > pos.y) count.min.y = pos.y;
      if (count.max.x < pos.x + size.width) count.max.x = pos.x + size.width;
      if (count.max.y < pos.y + size.height) count.max.y = pos.y + size.height;
    }
  }

  var avg = {x:(count.min.x + count.max.x)/2, y:(count.min.y + count.max.y)/2},
      diff = {x:Math.round(avg.x-im.center.x), y:Math.round(avg.y-im.center.y)};

  if (diff.x !== 0 || diff.y !== 0) {
    for (i=0;i<l;i++) {
      if (c[i].autoCrop === false) continue;
      for (e in c[i].children) {
        c[i].children[e].attrs.x -= diff.x;
        c[i].children[e].attrs.y -= diff.y;
      }
    }
    return im.adjustSavers();
  }

  var size = {width: count.max.x - count.min.x, height: count.max.y - count.min.y};
  console.log(size);
  im.alterCore('saveWidth',Math.round(size.width));
  im.alterCore('saveHeight',Math.round(size.height));
  im.buildBackground();
  im.fire('adjustedsavers');
  im.stage.draw();
};