var me = $(this);

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

im.adjustSavers = function() {
  log("Adjusting");
  var startx = Math.round(im.center.x - (im.saveWidth / 2)),
      posx = startx + im.saveWidth,
      starty = Math.round(im.center.y - (im.saveHeight / 2)),
      posy = starty + im.saveHeight;

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

  saverTopLeft.setWidth(startx);
  saverTopLeft.setHeight(posy);

  saverTopRight.setX(startx);
  saverTopRight.setWidth(im.stage.getScaledWidth()-startx);
  saverTopRight.setHeight(starty);

  saverBottomLeft.setWidth(posx);
  saverBottomLeft.setY(posy);
  saverBottomLeft.setHeight(im.stage.getScaledHeight()-posy);

  saverBottomRight.setY(starty);
  saverBottomRight.setX(posx);
  saverBottomRight.setWidth(im.stage.getScaledWidth()-posx);
  saverBottomRight.setHeight(im.stage.getScaledHeight()-starty);

  im.fire('saveSizeChange');

  im.savers.draw();
};

im.savers.add(saverTopLeft);
im.savers.add(saverTopRight);
im.savers.add(saverBottomLeft);
im.savers.add(saverBottomRight);

im.stage.add(im.savers);

im.adjustSavers();
