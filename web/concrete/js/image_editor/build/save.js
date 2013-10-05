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




im.actualPosition = function actualPosition(x, y, cx, cy, rad) {
  var ay = y - cy,
      ax = x - cx,
      degChange = im.activeElement.getRotation() + Math.atan2(ay, ax),
      r = Math.sqrt(Math.pow(ax, 2) + Math.pow(ay, 2));
  return [cx + (r * Math.cos(degChange)), cy + (r * Math.sin(degChange))];
}

im.getActualRect = function actualRect(cx, cy, elem) {
  var rect = [], rad = elem.getRotation();
  rect.push(im.actualPosition(elem.getX(), elem.getY(), cx, cy, rad));
  rect.push(im.actualPosition(elem.getX() + elem.getWidth(), elem.getY(), cx, cy, rad));
  rect.push(im.actualPosition(elem.getX() + elem.getWidth(), elem.getY() + elem.getHeight(), cx, cy, rad));
  rect.push(im.actualPosition(elem.getX(), elem.getY() + elem.getHeight(), cx, cy, rad));
  return rect;
}

im.getRect = function(elem) {
  var rect = [];
  rect.push([elem.getX(), elem.getY()]);
  rect.push([elem.getX() + elem.getWidth(), elem.getY()]);
  rect.push([elem.getX(), elem.getY() + elem.getHeight()]);
  rect.push([elem.getX() + elem.getWidth(), elem.getY() + elem.getHeight()]);
  return rect;
}

im.adjustSavers = function AdjustingSavers() {
  im.foreground.autoCrop = false;
  im.background.autoCrop = false;
  var i, e, u, score = {min:{x:false, y:false}, max:{x:false, y:false}};
  for (var i = im.stage.children.length - 1; i >= 0; i--) {
    var layer = im.stage.children[i];
    if (layer.autoCrop === false) continue;
    for (var e = layer.children.length - 1; e >= 0; e--) {
      var child = layer.children[e],
          rect = im.getActualRect(0, 0, child);
          console.log(child);

      for (var u = rect.length - 1; u >= 0; u--) {
        var point = rect[u], x = point[0] + layer.getX(), y = point[1] + layer.getY();
        if (x > score.max.x || score.max.x === false) score.max.x = x;
        if (x < score.min.x || score.min.x === false) score.min.x = x;
        if (y > score.max.y || score.max.y === false) score.max.y = y;
        if (y < score.min.y || score.min.y === false) score.min.y = y;
      }
    }
  }
  console.log(score);
  var size = {width: score.max.x - score.min.x, height: score.max.y - score.min.y};
  im.alterCore('saveWidth',Math.round(size.width));
  im.alterCore('saveHeight',Math.round(size.height));
  im.buildBackground();


  var ap = [im.center.x - im.activeElement.getWidth() / 2, im.center.y - im.activeElement.getHeight() / 2],
      adj = im.actualPosition(ap[0], ap[1], im.center.x, im.center.y, im.activeElement.getRotation());
  im.activeElement.parent.setPosition(adj);


  im.fire('adjustedsavers');
  im.stage.draw();
}

im.bind('imageLoad', function() {
  setTimeout(im.adjustSavers, 0);
});
