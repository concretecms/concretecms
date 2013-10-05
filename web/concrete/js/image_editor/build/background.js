// Set up background
im.background = new Kinetic.Layer();
im.foreground = new Kinetic.Layer();
im.stage.add(im.background);
im.stage.add(im.foreground);
im.bgimage = new Image();
im.saveArea = new Kinetic.Rect();
im.bind('load', function(){
  im.saveArea.setFillPatternImage(im.bgimage);

  im.saveArea.setFillPatternOffset([-(im.saveWidth/2),-(im.saveHeight/2)]);
  im.saveArea.setFillPatternScale(1/im.scale);
  im.saveArea.setFillPatternX(0);
  im.saveArea.setFillPatternY(0);
  im.saveArea.setFillPatternRepeat('repeat');

  im.background.add(im.saveArea);
  im.background.on('click',function(){
    im.setActiveElement(im.stage);
  });
}, im.bgimage);
im.bgimage.src = '/concrete/images/testbg.png';
im.buildBackground = function() {
  var startbb = (new Date).getTime();

  var dimensions = im.stage.getTotalDimensions();
  var to = (dimensions.max.x + dimensions.visibleHeight + dimensions.visibleWidth) * 2;


  im.saveArea.setFillPatternOffset([-(im.saveWidth/2) * im.scale,-(im.saveHeight/2) * im.scale]);
  im.saveArea.setX(Math.floor(im.center.x - (im.saveWidth / 2)));
  im.saveArea.setY(Math.floor(im.center.y - (im.saveHeight / 2)));
  im.saveArea.setFillPatternScale(1/im.scale);
  im.saveArea.setWidth(im.saveWidth);
  im.saveArea.setHeight(im.saveHeight);

  if (im.foreground) {
    im.foreground.destroy();
  }
  im.foreground = new Kinetic.Layer();
  im.stage.add(im.foreground);
  if (!im.coverLayer) {
    im.coverLayer = new Kinetic.Rect;
    im.coverLayer.setStroke('rgba(150,150,150,.5)');
    im.coverLayer.setFill('transparent');
    im.coverLayer.setListening(false);
    im.coverLayer.setStrokeWidth(Math.max(dimensions.width,dimensions.height,500));
  }
  var width = Math.max(dimensions.width,dimensions.height)*2;
  im.coverLayer.setPosition(im.saveArea.getX() - width / 2, im.saveArea.getY() - width / 2);
  im.coverLayer.setSize(im.saveArea.getWidth() + width, im.saveArea.getHeight() + width);
  im.coverLayer.setStrokeWidth(width);
  im.foreground.add(im.coverLayer);

  im.fire('backgroundBuilt');
  im.background.draw();
  im.foreground.draw();
  if (im.activeElement && im.activeElement.elementType != 'stage' && im.activeElement.parent) {
    im.activeElement.parent.draw();
  }
};

im.buildBackground();
im.on('stageChanged',im.buildBackground);
