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
  width:Math.floor(im.stage.getWidth()/2),
  height:Math.floor(im.stage.getHeight()/2)
});
im.cropperBottomLeft = new Kinetic.Rect({
  x:0,
  y:Math.floor(im.stage.getHeight()/2),
  fill:croppercolor,
  width:Math.floor(im.stage.getWidth()/2),
  height:Math.ceil(im.stage.getHeight()/2)
});
im.cropperTopRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getWidth()/2),
  y:0,
  fill:croppercolor,
  width:Math.ceil(im.stage.getWidth()/2),
  height:Math.floor(im.stage.getHeight()/2)
});
im.cropperBottomRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getWidth()/2),
  y:Math.floor(im.stage.getHeight()/2),
  fill:croppercolor,
  width:Math.ceil(im.stage.getWidth()/2),
  height:Math.ceil(im.stage.getHeight()/2)
});

var adjustCroppers = function() {
  var startx = im.start.x,
      posx = startx + im.width,
      starty = im.start.y,
      posy = starty + im.height;

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
  im.cropperTopRight.setWidth(im.stage.getWidth()-startx);
  im.cropperTopRight.setHeight(starty);

  im.cropperBottomLeft.setWidth(posx);
  im.cropperBottomLeft.setY(posy);
  im.cropperBottomLeft.setHeight(im.stage.getHeight()-posy);

  im.cropperBottomRight.setY(starty);
  im.cropperBottomRight.setX(posx);
  im.cropperBottomRight.setWidth(im.stage.getWidth()-posx);
  im.cropperBottomRight.setHeight(im.stage.getHeight()-starty);

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

adjustCroppers();


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

  if (Math.abs(im.start.y - im.image.getY()) < 5) {
    im.start.y = im.image.getY();
  }
  
  if (Math.abs(im.start.x - im.image.getX()) < 5) {
    im.start.x = im.image.getX();
  }

  im.adjuster.setX(im.start.x + im.width);
  im.adjuster.setY(im.start.y + im.height);

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

    im.adjuster.setX(im.start.x + im.width);
    im.adjuster.setY(im.start.y + im.height);
    adjustCroppers();


    im.stage.add(im.controls);
    im.stage.draw();
  }
});

im.adjuster = new Kinetic.Rect({
  x:im.start.x + im.dragger.width,
  y:im.start.y + im.dragger.height,
  width:10,
  height:10,
  stroke:"black",
  fill:"white",
  draggable:true
});
im.croppers.add(im.adjuster);


var adjuststart, adjuststartstart;
im.adjuster.on('dragstart',function(){
  adjuststart = im.adjuster.getPosition();
  adjuststartstart = {width:im.width,height:im.height};
});
im.adjuster.on('dragmove',function(e){
  var newpos = im.adjuster.getPosition(),
      offset = {
    x:adjuststart.x - newpos.x,
    y:adjuststart.y - newpos.y
  };

  im.width = adjuststartstart.width - offset.x;
  im.height = adjuststartstart.height - offset.y;

  adjustCroppers();
});



var cropToCroppers = function() {
  var newx = im.image.getX() - im.start.x,
      newy = im.image.getY() - im.start.y
  im.image.setX(newx);
  im.image.setY(newy);
  im.image.disableStroke();

  im.image.toImage({
    width:im.width,
    height:im.height,
    callback:function(img) {
      im.image.enableStroke();
      im.image.setImage(img);
      im.image.setWidth(img.width);
      im.image.setHeight(img.height);
      im.image.setX(im.start.x);
      im.image.setY(im.start.y);
      im.image.parent.draw();
    }
  });
};

me.find('button.crop').click(cropToCroppers);