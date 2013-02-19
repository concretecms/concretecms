var me = $(this);


im.stage.getTotalDimensions = function() {
  var minY = Math.round((im.saveHeight / 2 - im.center.y) * im.scale);
  var maxY = minY + im.stage.getHeight() - (im.saveHeight * im.scale);

  var minX = Math.round((im.saveWidth / 2 - im.center.x) * im.scale);
  var maxX = minX + im.stage.getWidth() - (im.saveWidth * im.scale);

  return {
    min: {
      x: minX,
      y: minY
    },
    max: {
      x: maxX,
      y: maxY
    },
    width:maxX-minX,
    height:maxY-minY,
    visibleWidth:maxX-minX + im.stage.getScaledWidth(),
    visibleHeight:maxY-minY + im.stage.getScaledHeight()
  };
};


im.savers = new Kinetic.Layer();

var savercolor = "rgba(0,0,0,.7)",
saverTopLeft = new Kinetic.Rect({
  x:0,
  y:0,
  fill:savercolor,
  width:Math.floor(im.stage.getScaledWidth()/2),
  height:Math.floor(im.stage.getScaledHeight()/2)
}),
saverBottomLeft = new Kinetic.Rect({
  x:0,
  y:Math.floor(im.stage.getScaledHeight()/2),
  fill:savercolor,
  width:Math.floor(im.stage.getScaledWidth()/2),
  height:Math.ceil(im.stage.getScaledHeight()/2)
}),
saverTopRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getScaledWidth()/2),
  y:0,
  fill:savercolor,
  width:Math.ceil(im.stage.getScaledWidth()/2),
  height:Math.floor(im.stage.getScaledHeight()/2)
}),
saverBottomRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getScaledWidth()/2),
  y:Math.floor(im.stage.getScaledHeight()/2),
  fill:savercolor,
  width:Math.ceil(im.stage.getScaledWidth()/2),
  height:Math.ceil(im.stage.getScaledHeight()/2)
});

saverTopLeft.position = 'topleft';
saverTopRight.position = 'topright';
saverBottomLeft.position = 'bottomleft';
saverBottomRight.position = 'bottomright';

im.adjustSavers = function() {
  log("Adjusting");

  var dimensions = im.stage.getTotalDimensions();
  var startx = Math.round(im.center.x - (im.saveWidth / 2)),
      posx = Math.round(startx + im.saveWidth),
      starty = Math.round(im.center.y - (im.saveHeight / 2)),
      posy = Math.round(starty + im.saveHeight),
      width = dimensions.visibleWidth,
      height = dimensions.visibleHeight,
      stagex = -im.stage.getTotalDimensions().max.x - im.stage.getScaledWidth(),
      stagey = -im.stage.getTotalDimensions().max.y - im.stage.getScaledHeight();


  if (stagex > startx) stagex = startx;
  if (stagey > starty) stagey = starty;

  if (posy < starty) {
    var inter = posy;
    posy = starty;
    starty = inter;
  }
  if (posx < startx) {
    var inter = posx;
    posx = startx;
    startx = inter;
  }

  saverTopLeft.setX(stagex);
  saverTopLeft.setY(stagey);
  saverTopLeft.setWidth(startx - stagex);
  saverTopLeft.setHeight(posy - stagey);

  saverTopRight.setX(startx);
  saverTopRight.setY(stagey);
  saverTopRight.setWidth(width - startx);
  saverTopRight.setHeight(starty - stagey);

  saverBottomLeft.setX(stagex);
  saverBottomLeft.setY(posy);
  saverBottomLeft.setWidth(posx - stagex);
  saverBottomLeft.setHeight(height - posy);

  saverBottomRight.setX(posx);
  saverBottomRight.setY(starty);
  saverBottomRight.setWidth(width - posx);
  saverBottomRight.setHeight(height - starty);

  im.fire('saveSizeChange');

  im.savers.draw();
};

im.savers.add(saverTopLeft);
im.savers.add(saverTopRight);
im.savers.add(saverBottomLeft);
im.savers.add(saverBottomRight);

im.stage.add(im.savers);

im.adjustSavers();


im.bind('stageChanged',im.adjustSavers);

im.stage.setDragBoundFunc(function(ret) {

  var dim = im.stage.getTotalDimensions();

  var maxx = Math.max(dim.max.x,dim.min.x),
      minx = Math.min(dim.max.x,dim.min.x),
      maxy = Math.max(dim.max.y,dim.min.y),
      miny = Math.min(dim.max.y,dim.min.y);

  if (ret.x > maxx) ret.x = maxx;
  if (ret.x < minx) ret.x = minx;
  if (ret.y > maxy) ret.y = maxy;
  if (ret.y < miny) ret.y = miny;

  return ret;
});
im.stage.setDraggable(true);