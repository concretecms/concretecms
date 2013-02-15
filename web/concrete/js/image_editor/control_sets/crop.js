var me = $(this);

im.controls = new Kinetic.Layer();
im.croppers = new Kinetic.Group();

im.dragger = new Kinetic.Rect({
  x:0,
  y:0,
  width:0,
  height:0,
  draggable:true
});
im.croppers.add(im.dragger);

var croppercolor = "rgba(0,0,0,.7)";

// Cropper aperture
im.cropperTopLeft = new Kinetic.Rect({
  x:0,
  y:0,
  fill:croppercolor,
  width:Math.floor(im.stage.getScaledWidth()/2),
  height:Math.floor(im.stage.getScaledHeight()/2)
});
im.cropperBottomLeft = new Kinetic.Rect({
  x:0,
  y:Math.floor(im.stage.getScaledHeight()/2),
  fill:croppercolor,
  width:Math.floor(im.stage.getScaledWidth()/2),
  height:Math.ceil(im.stage.getScaledHeight()/2)
});
im.cropperTopRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getScaledWidth()/2),
  y:0,
  fill:croppercolor,
  width:Math.ceil(im.stage.getScaledWidth()/2),
  height:Math.floor(im.stage.getScaledHeight()/2)
});
im.cropperBottomRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getScaledWidth()/2),
  y:Math.floor(im.stage.getScaledHeight()/2),
  fill:croppercolor,
  width:Math.ceil(im.stage.getScaledWidth()/2),
  height:Math.ceil(im.stage.getScaledHeight()/2)
});

var adjustCroppers = function() {
  var startx = im.start.x,
      posx = im.adjuster.getPosition().x + 5,
      starty = im.start.y,
      posy = im.adjuster.getPosition().y + 5;

  im.width = posx - startx;
  im.height = posy - starty;

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

  im.dragger.setWidth(im.width);
  im.dragger.setHeight(im.height);
  im.dragger.setX(startx);
  im.dragger.setY(starty);

  im.cropperTopLeft.setWidth(startx);
  im.cropperTopLeft.setHeight(posy);

  im.cropperTopRight.setX(startx);
  im.cropperTopRight.setWidth(im.stage.getScaledWidth()-startx);
  im.cropperTopRight.setHeight(starty);

  im.cropperBottomLeft.setWidth(posx);
  im.cropperBottomLeft.setY(posy);
  im.cropperBottomLeft.setHeight(im.stage.getScaledHeight()-posy);

  im.cropperBottomRight.setY(starty);
  im.cropperBottomRight.setX(posx);
  im.cropperBottomRight.setWidth(im.stage.getScaledWidth()-posx);
  im.cropperBottomRight.setHeight(im.stage.getScaledHeight()-starty);

  im.croppers.parent.draw();
};

im.croppers.add(im.cropperTopLeft);
im.croppers.add(im.cropperTopRight);
im.croppers.add(im.cropperBottomLeft);
im.croppers.add(im.cropperBottomRight);

im.controls.add(im.croppers);

im.width = im.image.getWidth();
im.height = im.image.getHeight();
im.start = {x:im.image.getX(),y:im.image.getY()};



var startpos, startstart;
im.dragger.on('dragstart',function(e){
  startpos = im.dragger.getPosition();
  startstart = {x:startpos.x,y:startpos.y};
});

im.dragger.on('dragmove',function(e){
  var newpos = im.dragger.getPosition(),
      offset = {
    x:startpos.x - newpos.x,
    y:startpos.y - newpos.y
  };

  im.start.x = startstart.x - offset.x;
  im.start.y = startstart.y - offset.y;

  im.adjuster.setX(im.start.x + im.width - 5);
  im.adjuster.setY(im.start.y + im.height - 5);

  adjustCroppers();
});

im.bind('ChangeActiveAction',function(e){
  if (e.eventData != im.namespace) {
    im.controls.remove();
    im.stage.draw();
  } else {

    im.width = im.image.getWidth();
    im.height = im.image.getHeight();
    im.start = {x:im.image.getX(),y:im.image.getY()};

    im.adjuster.setX(im.start.x + im.width - 5);
    im.adjuster.setY(im.start.y + im.height - 5);
    adjustCroppers();


    im.stage.add(im.controls);
    im.stage.draw();
  }
});

im.crosshair.onload = function() {
  im.adjuster.setImage(im.crosshair);
};

im.adjuster = new Kinetic.Image({
  x:im.start.x + im.dragger.width,
  y:im.start.y + im.dragger.height,
  width:11,
  height:11,
  image:im.crosshair,
  draggable:true
});
im.croppers.add(im.adjuster);


var adjuststart, adjuststartstart;
im.adjuster.on('dragstart',function(){
  adjuststart = im.adjuster.getPosition();
  adjuststart.x -= 5;
  adjuststart.y -= 5;
  adjuststartstart = {width:im.width,height:im.height};
});
im.adjuster.on('dragend',function(e){
  im.adjuster.setX(im.start.x + im.width - 5);
  im.adjuster.setY(im.start.y + im.height - 5);
});
im.adjuster.on('dragmove',function(e){
  var newpos = im.adjuster.getPosition(),
      offset = {
    x:adjuststart.x - newpos.x + 5,
    y:adjuststart.y - newpos.y + 5
  };

  im.width = adjuststartstart.width - offset.x;
  im.height = adjuststartstart.height - offset.y;

  adjustCroppers();
});

im.on('stageChanged',function(){
  adjustCroppers();
});

var cropToCroppers = function() {
  var oldScale = im.stage.getScale();
  im.stage.setScale(1);
  var newx = im.image.getX() - im.dragger.getX(),
      newy = im.image.getY() - im.dragger.getY()
  im.image.setX(newx);
  im.image.setY(newy);
  im.image.disableStroke();

  im.image.toImage({
    width:im.width,
    height:im.height,
    callback:function(img) {
      im.image.enableStroke();
      im.image.setImage(img);
      im.image.setWidth(im.dragger.getWidth());
      im.image.setHeight(im.dragger.getHeight());
      im.image.setX(im.dragger.getX());
      im.image.setY(im.dragger.getY());
      im.stage.setScale(oldScale);

      im.fire('imageChange');
      im.stage.draw();
    }
  });
};

me.find('button.crop').click(cropToCroppers);

adjustCroppers();