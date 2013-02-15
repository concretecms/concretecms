// Set up background
im.background = new Kinetic.Layer();
im.stage.add(im.background);

im.buildBackground = function() {
  var i,children = im.background.getChildren();
  for (var i in children) {
    children[i].remove();
  }

  im.background.add(new Kinetic.Rect({
    x: 0,
    y: 0,
    width: im.stage.getScaledWidth(),
    height: im.stage.getScaledHeight(),
    fill: '#eee'
  }));

  var getCoords = function (x, offset) {
    return {x: 2 * x, y: -x + offset};
  };

  var to = Math.max(im.stage.getScaledWidth(), im.stage.getScaledHeight()) * 2;
  for (x = -to; x <= to; x += 20) {
    im.background.add(new Kinetic.Line({
      points: [getCoords(x, 0), getCoords(im.background.getWidth(), x)],
      stroke: '#e3e3e3'
    }));
  }
  im.background.draw();
};
im.buildBackground();

im.on('stageChanged',im.buildBackground);
