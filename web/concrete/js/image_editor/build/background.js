// Set up background
im.background = new Kinetic.Layer();
im.foreground = new Kinetic.Layer();
im.stage.add(im.background);
im.stage.add(im.foreground);
im.bgimage = new Image();
im.bgimage.src = '/concrete/images/testbg.png';
im.buildBackground = function() {
  var startbb = (new Date).getTime();
  var z = im.background.getZIndex();
  im.background.destroy();
  im.background = new Kinetic.Layer();
  im.foreground.destroy();
  im.foreground = new Kinetic.Layer();
  im.stage.add(im.background);
  im.stage.add(im.foreground);
  im.background.setZIndex(z);
  im.foreground.moveToTop();

  var dimensions = im.stage.getTotalDimensions();
  var to = (dimensions.max.x + dimensions.visibleHeight + dimensions.visibleWidth) * 2;
  im.totalBackground = new Kinetic.Rect({
    x:dimensions.max.x - dimensions.width,
    y:dimensions.max.y - dimensions.height,
    width:to,
    height:to,
    fill:'#aaa'
  });

  im.saveArea = new Kinetic.Rect({
    width:im.saveWidth,
    height:im.saveHeight,
    fillPatternImage: im.bgimage,
    fillPatternOffset: [0,0],
    fillPatternScale: 1/im.scale,
    fillPatternX:0,
    fillPatternY:0,
    fillPatternRepeat:'repeat',
    x:Math.floor(im.center.x - (im.saveWidth / 2)),
    y:Math.floor(im.center.y - (im.saveHeight / 2))
  });

  im.background.add(im.totalBackground);
  im.background.add(im.saveArea);

  var getPoints = function(offset) {
    var height = im.saveHeight + 2;
    var width = im.saveWidth + 2;
    var points = [{
      x:offset + im.center.x - (width/2),
      y:im.center.y + (height/2)
    }];
    points[1] = {
      x:points[0].x + height,
      y:points[0].y - height
    };

    return points;
  }, getPoints2 = function(offset) {
    var height = im.saveHeight + 2;
    var width = im.saveWidth + 2;
    var points = [{
      x:offset + im.center.x - (width/2),
      y:im.center.y + (height/2)
    }];
    points[1] = {
      x:points[0].x - height,
      y:points[0].y - height
    };

    return points;
  }

  /*
  var total = Math.max(im.saveWidth,im.saveHeight) + im.saveHeight;
  total *= im.scale;
  total /= 10;
  var total2 = Math.max(im.saveWidth,im.saveHeight) + im.saveWidth;
  total2 *= im.scale;
  total2 /= 10;
  total += total2;

  warn("Total Lines: "+Math.ceil(total));
  var start = (new Date).getTime();
  var lines = 0;
  for (var offset = -im.saveHeight; offset <= Math.max(im.saveWidth,im.saveHeight); offset += (10 / im.scale)) {
    var line = new Kinetic.Line({
      stroke:'#eee',
      strokeWidth:1 / im.scale,
      points:getPoints(offset)
    });
    im.background.add(line);
    lines++;
  }
  for (var offset = 0; offset <= Math.max(im.saveWidth,im.saveHeight) + im.saveWidth; offset += (10 / im.scale)) {
    var line = new Kinetic.Line({
      stroke:'#eee',
      strokeWidth:1 / im.scale,
      points:getPoints2(offset)
    });
    im.background.add(line);
    lines++;
  }
  var total = (new Date).getTime() - start;
  warn("Actual lines: "+lines + " in "+total/1000+" seconds. or " +lines/total+' milliseconds per');
  */

  im.background.on('click',function(){
    im.setActiveElement(im.stage);
  });

  var dimensions = im.stage.getTotalDimensions();
  /*
  var foregroundtop = new Kinetic.Rect({
    x:im.center.x - dimensions.visibleWidth/2,
    y:im.center.y - dimensions.visibleHeight/2,
    width:dimensions.visibleWidth,
    height:dimensions.visibleHeight / 2 - im.saveHeight / 2,
    fill:'#ccc'
  });
  var foregroundleft = new Kinetic.Rect({
    x:im.center.x - dimensions.visibleWidth/2,
    y:im.center.y - dimensions.visibleHeight/2,
    width:dimensions.visibleWidth / 2 - im.saveWidth / 2,
    height:dimensions.visibleHeight,
    fill:'#ccc'
  });
  var foregroundbottom = new Kinetic.Rect({
    x:im.center.x - dimensions.visibleWidth/2,
    y:im.center.y + dimensions.visibleHeight/2,
    width:dimensions.visibleWidth,
    height:dimensions.visibleHeight / 2 - im.saveHeight / 2,
    fill:'#ccc'
  });
  var foregroundright = new Kinetic.Rect({
    x:im.center.x + dimensions.visibleWidth/2,
    y:im.center.y - dimensions.visibleHeight/2,
    width:dimensions.visibleWidth / 2 - im.saveWidth / 2,
    height:dimensions.visibleHeight,
    fill:'#ccc'
  });
  foregroundbottom.setY(foregroundbottom.getY() - foregroundbottom.getHeight());
  foregroundright.setX(foregroundright.getX() - foregroundright.getWidth());

  im.foreground.add(foregroundtop);
  im.foreground.add(foregroundleft);
  im.foreground.add(foregroundbottom);
  im.foreground.add(foregroundright);
  */

  var foreground = im.saveArea.clone();
  foreground.setFillPatternImage('');
  foreground.setDrawHitFunc(function(){return false}); // disable hit all together
  foreground.setStroke('rgba(0,0,0,.5)');
  foreground.setStrokeWidth(Math.max(dimensions.visibleWidth,dimensions.visibleHeight)/2);
  foreground.setX(foreground.getX() - foreground.getStrokeWidth()/2);
  foreground.setWidth(foreground.getWidth() + foreground.getStrokeWidth());
  foreground.setY(foreground.getY() - foreground.getStrokeWidth()/2);
  foreground.setHeight(foreground.getHeight() + foreground.getStrokeWidth());
  im.foreground.add(foreground);

  warn('Building background took '+((new Date).getTime()-startbb)/1000 +" seconds.");
  im.fire('backgroundBuilt');
  im.stage.draw();
};

im.buildBackground();
im.on('stageChanged',im.buildBackground);
