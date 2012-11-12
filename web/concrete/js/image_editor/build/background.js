// Set up background
im.background = new Kinetic.Layer();
im.background.add(new Kinetic.Rect({
  x: 0,
  y: 0,
  width: im.stage.getWidth(),
  height: im.stage.getHeight(),
  fill: '#eee'
}));
var getCoords = function (x, offset) {
  return {x: 2 * x, y: -x + offset};
};

var to = Math.max(im.stage.getWidth(), im.stage.getHeight()) * 2;
for (x = -10; x <= to; x += 20) {
  im.background.add(new Kinetic.Line({
    points: [getCoords(x, 0), getCoords(im.background.getWidth(), x)],
    stroke: '#e3e3e3'
  }));
}
im.stage.add(im.background);