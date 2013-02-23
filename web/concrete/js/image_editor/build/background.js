// Set up background
im.background = new Kinetic.Layer();
im.stage.add(im.background);

im.buildBackground = function() {
  var z = im.background.getZIndex();
  im.background.destroy();
  im.background = new Kinetic.Layer();
  im.stage.add(im.background);
  im.background.setZIndex(z);

  var getCoords = function (x, offset) {
    // slope = 1
    return {x: x, y: -x + offset};
  };

  var dimensions = im.stage.getTotalDimensions();
  im.totalBackground = new Kinetic.Rect({
    x:dimensions.min.x - dimensions.width,
    y:dimensions.min.y - dimensions.height,
    width:to,
    height:to,
    fill:'#ccc'
  });

  im.saveArea = new Kinetic.Rect({
    width:im.saveWidth,
    height:im.saveHeight,
    x:Math.floor(im.center.x - (im.saveWidth / 2)),
    y:Math.floor(im.center.y - (im.saveHeight / 2)),
    fill:'white'
  })

  im.background.add(im.totalBackground);
  im.background.add(im.saveArea);
  // Todo, make this more efficient, as this is not a sound algorithm.
  // This should only draw in the visible space.
  if (im.scale < .25) return;
  var to = dimensions.max.x + dimensions.visibleHeight + dimensions.visibleWidth;
  if (im.scale > 1) to *= im.scale;
  for (x = -(dimensions.max.x + dimensions.visibleHeight); x <= to; x += 20) {
    im.background.add(new Kinetic.Line({
      points: [getCoords(-to, x), getCoords(to, x)],
      stroke: '#e3e3e3'
    }));
  }
  im.background.draw();
};
im.buildBackground();

im.on('stageChanged',im.buildBackground);
