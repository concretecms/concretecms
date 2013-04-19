var me = $(this);

im.disable();
im.bind('changeActiveElement',function(e){
  if (im.activeElement.elementType != 'image') {
    im.disable();
  } else {
    im.enable();
  }
});

im.controls = new Kinetic.Layer();
im.croppers = new Kinetic.Group();


var scaledWidth = Math.max(im.stage.getWidth(),im.stage.getScaledWidth()),
    scaledHeight = Math.max(im.stage.getHeight(),im.stage.getScaledHeight());

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
  width:Math.floor(scaledWidth/2),
  height:Math.floor(scaledHeight/2)
});
im.cropperBottomLeft = new Kinetic.Rect({
  x:0,
  y:Math.floor(scaledHeight/2),
  fill:croppercolor,
  width:Math.floor(scaledWidth/2),
  height:Math.ceil(scaledHeight/2)
});
im.cropperTopRight = new Kinetic.Rect({
  x:Math.floor(scaledWidth/2),
  y:0,
  fill:croppercolor,
  width:Math.ceil(scaledWidth/2),
  height:Math.floor(scaledHeight/2)
});
im.cropperBottomRight = new Kinetic.Rect({
  x:Math.floor(scaledWidth/2),
  y:Math.floor(scaledHeight/2),
  fill:croppercolor,
  width:Math.ceil(scaledWidth/2),
  height:Math.ceil(scaledHeight/2)
});

im.adjustCroppers = function() {

  var dimensions = im.stage.getTotalDimensions();
  scaledWidth = Math.max(im.stage.getWidth(),im.stage.getScaledWidth());
  scaledHeight = Math.max(im.stage.getHeight(),im.stage.getScaledHeight());
  var startx = im.start.x,
      posx = im.adjuster.getPosition().x + 5,
      starty = im.start.y,
      posy = im.adjuster.getPosition().y + 5,
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

  im.dragger.setWidth(im.width);
  im.dragger.setHeight(im.height);
  im.dragger.setX(startx);
  im.dragger.setY(starty);

  im.adjuster.setScale(im.stage.getScaledWidth()/im.stage.getWidth());
  im.adjuster.setX(im.start.x + im.width - (5 * im.adjuster.getScale().x));
  im.adjuster.setY(im.start.y + im.height - (5 * im.adjuster.getScale().y));
  im.adjuster.parent.draw();

  im.cropperTopLeft.setX(stagex);
  im.cropperTopLeft.setY(stagey);
  im.cropperTopLeft.setWidth(startx - stagex);
  im.cropperTopLeft.setHeight(posy - stagey);

  im.cropperTopRight.setX(startx);
  im.cropperTopRight.setY(stagey);
  im.cropperTopRight.setWidth(scaledWidth - startx);
  im.cropperTopRight.setHeight(starty - stagey);

  im.cropperBottomLeft.setX(stagex);
  im.cropperBottomLeft.setY(posy);
  im.cropperBottomLeft.setWidth(posx - stagex);
  im.cropperBottomLeft.setHeight(scaledHeight - posy);

  im.cropperBottomRight.setX(posx);
  im.cropperBottomRight.setY(starty);
  im.cropperBottomRight.setWidth(scaledWidth - posx);
  im.cropperBottomRight.setHeight(scaledHeight - starty);

  im.stage.draw();
};
var adjustCroppers = im.adjustCroppers;
im.croppers.add(im.cropperTopLeft);
im.croppers.add(im.cropperTopRight);
im.croppers.add(im.cropperBottomLeft);
im.croppers.add(im.cropperBottomRight);

im.controls.add(im.croppers);

im.width = im.activeElement.getWidth();
im.height = im.activeElement.getHeight();
im.start = {x:im.activeElement.getX(),y:im.activeElement.getY()};



var startpos, startstart, adjStart;
im.dragger.on('dragstart',function(e){
  startpos = im.dragger.getPosition();
  adjStart = im.adjuster.getPosition();
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

  im.adjuster.setX(im.start.x + im.width - (5 * im.adjuster.getScale().x));
  im.adjuster.setY(im.start.y + im.height - (5 * im.adjuster.getScale().x));

  adjustCroppers();
});

im.bind('ChangeActiveAction',function(e){
  if (e.eventData != im.namespace) {
    im.controls.remove();
    im.stage.draw();
  } else {

    im.width = im.activeElement.getWidth();
    im.height = im.activeElement.getHeight();
    im.start = {x:im.activeElement.getX(),y:im.activeElement.getY()};

    im.adjuster.setX(im.start.x + im.width - (5 * im.adjuster.getScale().x));
    im.adjuster.setY(im.start.y + im.height - (5 * im.adjuster.getScale().x));
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
  im.adjuster.setX(im.start.x + im.width - (5 * im.adjuster.getScale().x));
  im.adjuster.setY(im.start.y + im.height - (5 * im.adjuster.getScale().x));
});
im.adjuster.on('dragmove',function(e){
  var newpos = im.adjuster.getPosition(),
      offset = {
    x:Math.round(adjuststart.x - newpos.x + (5 * im.adjuster.getScale().x)),
    y:Math.round(adjuststart.y - newpos.y + (5 * im.adjuster.getScale().x))
  };

  im.width = adjuststartstart.width - offset.x;
  im.height = adjuststartstart.height - offset.y;

  adjustCroppers();
});

im.on('stageChanged',function(){
  adjustCroppers();
  im.adjuster.setScale(im.scale);
  im.adjuster.parent.draw();
});

var cropToCroppers = function() {
  im.width  = im.dragger.getWidth();
  im.height = im.dragger.getHeight();
  var oldScale = im.stage.getScale(),
      oldx = im.stage.getX(),
      oldy = im.stage.getY();

  im.stage.setScale(1);
  im.stage.setX(0);
  im.stage.setY(0);

  var newx = Math.round(im.activeElement.getX() - im.dragger.getX()),
      newy = Math.round(im.activeElement.getY() - im.dragger.getY());

  im.showLoader('cropping...');

  im.activeElement.toImage({
    x:im.dragger.getX(),
    y:im.dragger.getY(),
    width:im.width,
    height:im.height,
    callback:function(img) {
      im.activeElement.setImage(img);
      im.activeElement.setCrop();
      im.activeElement.setWidth(im.dragger.getWidth());
      im.activeElement.setHeight(im.dragger.getHeight());
      im.activeElement.setX(im.dragger.getX());
      im.activeElement.setY(im.dragger.getY());
      im.stage.setScale(oldScale);
      im.stage.setX(oldx);
      im.stage.setY(oldy);

      im.fire('imageChange');
      im.hideLoader();
      im.adjustSavers();
      im.stage.draw();
    }
  });
};

me.find('button.crop').click(cropToCroppers);
//adjustCroppers();