// Set up background
im.background = new Kinetic.Layer();
im.foreground = new Kinetic.Layer();
im.stage.add(im.background);
im.stage.add(im.foreground);
im.bgimage = new Image();
im.bgimage.src = '/concrete/images/testbg.png';
im.buildBackground = function() {
  var startbb = (new Date).getTime();
  im.foreground.moveToTop();

  var dimensions = im.stage.getTotalDimensions();
  var to = (dimensions.max.x + dimensions.visibleHeight + dimensions.visibleWidth) * 2;
  if (!im.totalBackground) {
    im.totalBackground = new Kinetic.Rect({
      x:dimensions.max.x - dimensions.width,
      y:dimensions.max.y - dimensions.height,
      width:to,
      height:to,
      fill:'#aaa'
    });
    im.background.add(im.totalBackground);
  }
  im.totalBackground.setX(dimensions.max.x - dimensions.width);
  im.totalBackground.setY(dimensions.max.y - dimensions.height);
  im.totalBackground.setWidth(to);
  im.totalBackground.setHeight(to);

  if (!im.saveArea) {
    im.saveArea = new Kinetic.Rect({
      width:im.saveWidth,
      height:im.saveHeight,
      fillPatternImage: im.bgimage,
      fillPatternOffset: [-(im.saveWidth/2),-(im.saveHeight/2)],
      fillPatternScale: 1/im.scale,
      fillPatternX:0,
      fillPatternY:0,
      fillPatternRepeat:'repeat',
      x:Math.floor(im.center.x - (im.saveWidth / 2)),
      y:Math.floor(im.center.y - (im.saveHeight / 2))
    });
    im.background.add(im.saveArea);
    im.background.on('click',function(){
      im.setActiveElement(im.stage);
    });
  }

  im.saveArea.setFillPatternOffset([-(im.saveWidth/2) * im.scale,-(im.saveHeight/2) * im.scale]);
  im.saveArea.setX(Math.floor(im.center.x - (im.saveWidth / 2)));
  im.saveArea.setY(Math.floor(im.center.y - (im.saveHeight / 2)));
  im.saveArea.setFillPatternScale(1/im.scale);
  im.saveArea.setWidth(im.saveWidth);
  im.saveArea.setHeight(im.saveHeight);

  var dimensions = im.stage.getTotalDimensions();

  if (im.foreground) {
    im.foreground.destroy();
  }
  im.foreground = new Kinetic.Layer();
  im.stage.add(im.foreground);
  if (!im.coverLayer) {
    im.coverLayer = new Kinetic.Rect;
    im.coverLayer.setStroke('rgba(255,0,0,.5)');
    im.coverLayer.setStrokeWidth(Math.max(dimensions.width,dimensions.height,500));
  }
  im.foreground.add(im.coverLayer);


  im.fire('backgroundBuilt');
  im.background.draw();
  im.foreground.draw();
};

im.buildBackground();
im.on('stageChanged',im.buildBackground);
