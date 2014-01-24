var me = $(this), elem = this;
im.selected = false;
im.disable();

im.ratio = {x:1, y:1}

function Positioning(im, me, elem) {
  var my = this;

  if (!im.strictSize) {
    $('div.positioning', me).hide();
  }

  my.setActiveX = function(x) { im.activeElement.setX(Math.round(x)); };
  my.setActiveY = function(y) { im.activeElement.setY(Math.round(y)); };

  my.setActiveWidth = function(w) {
    im.sizeBoxes.width.val(Math.round(w));
    im.activeElement.setWidth(Math.round(w));
  };
  my.setActiveHeight = function(h) {
    im.sizeBoxes.height.val(Math.round(h));
    im.activeElement.setHeight(Math.round(h));
  };

  im.origin = {};
  im.bind('changeActiveElement',function(e, data){
    if (data && data.elementType == 'stage') return im.disable();
    im.enable();
    if (im.selected) {
      if (im.currentElement) {
        im.currentElement.setDraggable(false);
      }
      im.currentElement = im.activeElement;
      if (im.strictSize) {
        im.currentElement.setDraggable(true);
      }
      im.origin = im.currentElement.getPosition();
      if (!im.currentElement.isBound) {
        im.currentElement.on('dragmove',function() {
          updateInputs();
        });
        im.currentElement.on('dragend',function(){
          updateSliders();
        });
        im.currentElement.isBound = true;
      }
      updateSliders();
    }
  });
  im.bind('ChangeActiveAction',function(e, data){
    if (data == im.namespace) {
      im.selected = true;
      im.currentElement = im.activeElement;
      if (im.strictSize) {
        im.currentElement.setDraggable(true);
      }
      im.origin = im.currentElement.getPosition();
      if (!im.currentElement.isBound) {
        im.currentElement.on('dragmove',function() {
          im.trigger('activeElementDragMove', e);
          updateInputs();
        });
        im.currentElement.on('dragend',function(){
          im.trigger('activeElementDragMove', e);
          im.trigger('activeElementDragEnd', e);
          updateSliders();
        });
        im.currentElement.isBound = true;
      }
      updateSliders();
    } else {
      im.selected = false;
      if (im.currentElement) {
        im.currentElement.setDraggable(false);
      }
    }
  });

  var slidery = $('div.vertical > div',me);
  var sliderx = $('div.horizontal > div',me);
  slidery.slider({
    step: 1,
    range: "min",
    min:-im.saveHeight,
    max:im.saveHeight,
    value:0,
    animate: 300,
    slide: function(ev,e){
      my.setActiveY(-e.value + Math.round(im.center.y - im.saveHeight/2));
      //im.trigger('activeElementMove', e);
      im.trigger('sliderMove', e, elem);
    },
    stop: function(ev,e){
      im.trigger('activeElementDragMove', e);
      im.trigger('activeElementDragMove', e, elem);
      updateSliders();
    }
  });
  sliderx.slider({
    step: 1,
    range: "min",
    min:-im.saveWidth,
    max:im.saveWidth,
    value:0,
    animate: 300,
    slide: function(ev,e){
      my.setActiveX(im.saveArea.getX() - e.value);
      //im.trigger('activeElementMove', e);
      im.trigger('sliderMove', e, elem);
    },
    stop: function(ev,e){
      im.trigger('activeElementDragMove', e);
      im.trigger('activeElementDragMove', e, elem);
      updateSliders();
    }
  });

  im.bind(['adjustedsavers', 'activeElementSizeChange'], function() {
    sliderx.slider('option','min', -im.activeElement.getWidth());
    sliderx.slider('option','max', im.saveWidth);
    slidery.slider('option','min', -im.saveWidth);
    slidery.slider('option','max', im.activeElement.getHeight());
    updateSliders();
  });

  im.bind('sliderMove',function(){
    im.currentElement.parent.draw();
    //im.buildBackground();
    $('div.horizontal > input').val(sliderx.slider('option', 'value') || "0");
    $('div.vertical > input').val(slidery.slider('option', 'value') || "0");
    updateInputs();
  },elem);

  var updateSliders = function() {
    sliderx.slider('option', 'value', im.saveArea.getX() - im.activeElement.parent.getX());
    slidery.slider('option', 'value', im.saveArea.getY() - im.activeElement.parent.getY());
    updateInputs();
  };
  var inputx = $('div.horizontal > input',me), inputy = $('div.vertical > input',me);
  var updateInputs = function() {
    inputx.val(im.saveArea.getX() - im.activeElement.parent.getX());
    inputy.val(im.saveArea.getY() - im.activeElement.parent.getY());
  };

  $('button.up',me).click(function(e) {
    im.buildBackground();
    if (im.foreground.getZIndex() - im.activeElement.parent.getZIndex() == 1) return;
    im.currentElement.parent.moveUp();
    im.buildBackground();
  });
  $('button.down',me).click(function(e) {
    if (im.activeElement.parent.getZIndex() - im.background.getZIndex() == 1) return;
    im.currentElement.parent.moveDown();
    im.buildBackground();
  });
  $('button.center',me).click(function(e) {
    var ap = [im.center.x - im.activeElement.getWidth() / 2, im.center.y - im.activeElement.getHeight() / 2],
        center = im.actualPosition(ap[0], ap[1], im.center.x, im.center.y, im.activeElement.getRotation());

    sliderx.slider('value',-center[0]);
    slidery.slider('value',center[1]);

    var tween = new Kinetic.Tween({
      node:im.activeElement.parent,
      x:Math.round(center[0]),
      y:Math.round(center[1]),
      duration:.2,
      onFinish:function() {
        updateSliders();
        im.fire('activeElementDragMove');
      }
    });
    tween.play();
  });
  $('div.cancelbutton',me).click(function(e) {
    sliderx.slider('value', -Math.round(Math.round(im.center.x - im.saveWidth/2) - im.origin.x));
    slidery.slider('value',Math.round(Math.round(im.center.y - im.saveHeight/2) - im.origin.y));
    im.currentElement.transitionTo({
      x:im.origin.x,
      y:im.origin.y,
      duration:.3,
      callback:function() {
        updateSliders();
        im.fire('activeElementDragMove');
      }
    });
  });
  $('button.delete',me).click(function(e) {
    if (confirm('Really delete this?')) {
      im.activeElement.parent.remove();
      im.setActiveElement(im.stage);
    }
  });
  im.bind('adjustedsavers', updateSliders);
}

function Sizing(im, me, elem) {
  im.ratioLocked = true;
  var our = this;
  var locked = $('div.locked',me).addClass('active').click(function(){
    im.ratioLocked = !im.ratioLocked;
    im.ratio.x = im.activeElement.getWidth();
    im.ratio.y = im.activeElement.getHeight();
    ((im.ratioLocked && $(this).addClass('active')) || $(this).removeClass('active'));
  });

  this.scalingSize = {width:im.activeElement.getWidth(), height:im.activeElement.getHeight()};

  this.scaleBox = $('div.scale > span.perc > input', me).val(100);
  this.slider = $('div.scale > div', me).slider({
    step: 10,
    range: "min",
    min:10,
    max:500,
    value:100,
    animate: 300,
    slide: function(ev,e){
      our.scaleBox.val(e.value);
      var oldWidth = im.activeElement.getWidth(), oldHeight = im.activeElement.getHeight();
      im.activeElement.setWidth(Math.round(e.value / 100 * im.activeElement.getWidth()));
      im.activeElement.setHeight(Math.round(e.value / 100 * im.activeElement.getHeight()));
      im.sizeBoxes.width.val(im.activeElement.getWidth());
      im.sizeBoxes.height.val(im.activeElement.getHeight());
      im.adjustSavers();
      im.activeElement.parent.draw();
    },
    stop: function() {
      im.activeElement.setX(Math.round(im.activeElement.getX()));
      im.activeElement.setY(Math.round(im.activeElement.getY()));
      im.adjustSavers();
    }
  });
  im.bind('activeElementSizeChange',function(e, data) {
    if (data === 'scale') return;
    our.scalingSize.width  = im.activeElement.getWidth();
    our.scalingSize.height = im.activeElement.getHeight();
    our.slider.slider('option','value',100);
    our.scaleBox.val(100);
    im.adjustSavers();
  });

  im.sizeBoxes = {
    width:$('div.widthinput > div.input > input', me),
    height:$('div.heightinput > div.input > input', me),
  };
  im.sizeBoxes.width.keyup(function(){
    var ob = $(this),
        width = parseInt(ob.val(), 10),
        height = im.activeElement.getHeight();

    if (im.ratioLocked) {
      height = Math.round(im.ratio.y * (width / im.ratio.x));
    }
    if (height === 0) {
      height = 1;
    }

    im.activeElement.setSize(Math.round(width), Math.round(height));
    im.activeElement.parent.draw();
    im.sizeBoxes.height.val(im.activeElement.getHeight());
    im.fire('activeElementSizeChange');
  });
  im.sizeBoxes.height.keyup(function(){
    var ob = $(this),
        height = parseInt(ob.val(), 10),
        width = im.activeElement.getWidth();

    if (im.ratioLocked) {
      width = Math.round(im.ratio.x * (height / im.ratio.y));
    }
    if (width === 0) {
      width = 1;
    }

    im.activeElement.setSize(Math.round(width), Math.round(height));
    im.activeElement.parent.draw();
    im.sizeBoxes.width.val(im.activeElement.getWidth());
    im.fire('activeElementSizeChange');
  });

  im.controlLayer = new Kinetic.Layer();
  im.stage.add(im.controlLayer);
  im.controlLayer.hide();

  im.controlLayer.autoCrop = false;
  im.bind('rotationChanged',function(){
    im.controlLayer.setRotationDeg(im.activeElement.getRotationDeg());
    im.controlLayer.draw();
  });

  // Handle enums
  var p = function (n) { return Math.pow(2, n); },
      SizingHandleTop    = p(1),
      SizingHandleRight  = p(2),
      SizingHandleBottom = p(3),
      SizingHandleLeft   = p(4);

  function SizingHandle(position) {
    var my = this;

    this.handle = new Kinetic.Image({
      x:0,
      y:0,
      width:7,
      height:7,
      fill:'transparent',
      stroke:'black',
      strokeWidth:1,
      image:im.crosshair,
      draggable:true,
      offset:[4,4]
    });
    my.startPos = this.handle.getPosition();

    this.position = function(){ error('failed to set position'); };
    this.update   = function(){ error('failed to update'); };

    my.setX = function(x) { my.handle.setX(Math.round(x)); };
    my.setY = function(y) { my.handle.setY(Math.round(y)); };

    my.setActiveX = function(x) {
      var degChange = im.activeElement.getRotation(),
          r = im.activeElement.getScaleX() * x;

      im.activeElement.parent.setX(my.layerStartPos.x + (r * Math.cos(degChange)));
      im.activeElement.parent.setY(my.layerStartPos.y + (r * Math.sin(degChange)));
    };

    my.setActiveY = function(y) {
      im.activeElement.rotateDeg(90);
      var degChange = im.activeElement.getRotation(),
          r = im.activeElement.getScaleY() * y;
      im.activeElement.rotateDeg(-90);

      im.activeElement.parent.setX(my.layerStartPos.x + (r * Math.cos(degChange)));
      im.activeElement.parent.setY(my.layerStartPos.y + (r * Math.sin(degChange)));
    };

    my.setActivePos = function(x, y) {
      x *= im.activeElement.getScaleX();
      y *= im.activeElement.getScaleY();
      var degChange = im.activeElement.getRotation() + Math.atan2(y, x),
          r = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));

      im.activeElement.parent.setX(my.layerStartPos.x + (r * Math.cos(degChange)));
      im.activeElement.parent.setY(my.layerStartPos.y + (r * Math.sin(degChange)));
    };

    my.setActiveWidth = function(w) {
      w = Math.max(w, 1);
      im.sizeBoxes.width.val(Math.round(w));
      im.activeElement.setWidth(Math.round(w));
    };
    my.setActiveHeight = function(h) {
      h = Math.max(h, 1);
      im.sizeBoxes.height.val(Math.round(h));
      im.activeElement.setHeight(Math.round(h));
    };

    switch(position) {
      case SizingHandleTop:
        this.position = function PositionTop(){
          my.setX(im.activeElement.getWidth() / 2);
          my.setY(0);
        };
        this.update = function UpdateTop() {
          my.setActiveHeight(my.startSize.height - my.handle.getY());
          my.setActiveY(my.elemStartPos.y + my.handle.getY());
        }
        break;
      case SizingHandleRight:
        this.position = function PositionRight(){
          my.setX(im.activeElement.getWidth());
          my.setY(im.activeElement.getHeight() / 2);
        };
        this.update = function UpdateRight(){
          my.setActiveWidth(this.handle.getX());
        };
        break;
      case SizingHandleBottom:
        this.position = function PositionBottom(){
          my.setX(im.activeElement.getWidth() / 2);
          my.setY(im.activeElement.getHeight());
        };
        this.update = function UpdateBottom(){
          my.setActiveHeight(this.handle.getY());
        };
        break;
      case SizingHandleLeft:
        this.position = function PositionLeft(){
          my.setX(0);
          my.setY(im.activeElement.getHeight() / 2);
        };
        this.update = function UpdateLeft(){
          my.setActiveWidth(my.startSize.width - my.handle.getX());
          my.setActiveX(my.elemStartPos.x + my.handle.getX());
        };
        break;


      case SizingHandleTop | SizingHandleLeft:
        this.position = function PositionTopLeft(){
          my.setX(0);
          my.setY(0);
        };
        this.update = function UpdateTopLeft(){
          my.setActiveWidth(my.startSize.width - my.handle.getX());
          my.setActiveHeight(my.startSize.height * (im.activeElement.getWidth() / my.startSize.width));
          my.setActivePos(my.elemStartPos.x + my.handle.getX(), my.elemStartPos.y + my.startSize.height - im.activeElement.getHeight());
        };
        break;
      case SizingHandleTop | SizingHandleRight:
        this.position = function PositionTopRight(){
          my.setX(im.activeElement.getWidth());
          my.setY(0);
        };
        this.update = function UpdateTopRight(){
          my.setActiveWidth(my.handle.getX());
          my.setActiveHeight(my.startSize.height * (im.activeElement.getWidth() / my.startSize.width));
          my.setActiveY(my.elemStartPos.y + my.startSize.height - im.activeElement.getHeight());
        };
        break;
        case SizingHandleBottom | SizingHandleLeft:
        this.position = function PositionBottomLeft(){
          my.setX(0);
          my.setY(im.activeElement.getHeight());
        };
        this.update = function UpdateBottomLeft(){
          my.setActiveWidth(my.startSize.width - my.handle.getX());
          my.setActiveHeight(my.startSize.height * (im.activeElement.getWidth() / my.startSize.width));
          my.setActiveX(my.elemStartPos.x + my.handle.getX());
        };
        break;
      case SizingHandleBottom | SizingHandleRight:
        this.position = function PositionBottomRight(){
          my.setX(im.activeElement.getWidth());
          my.setY(im.activeElement.getHeight());
        };
        this.update = function UpdateBottomRight(){
          my.setActiveWidth(my.handle.getX());
          my.setActiveHeight(my.startSize.height * (im.activeElement.getWidth() / my.startSize.width));
        };
        break;

    }

    this.handle.on('dragstart', function(e) {
      my.startPos = this.getPosition();
      my.elemStartPos = im.activeElement.getPosition();
      my.layerStartPos = im.activeElement.parent.getPosition();
      my.startSize = im.activeElement.getSize();
    });
    this.handle.on('dragmove', function(e){
      my.update();
      my.startPos = this.getPosition();
      my.elemStartPos = im.activeElement.getPosition();
      my.layerStartPos = im.activeElement.parent.getPosition();
      my.startSize = im.activeElement.getSize();
      im.fire('activeElementChangingSize');
      im.fire('activeElementShouldAdjustLayer');
      im.activeElement.parent.draw();
    });
    this.handle.on('dragend', function(){
      my.position();
      im.activeElement.parent.draw();
      if (my.elemStartPos.x != im.activeElement.getX() || my.elemStartPos.y != im.activeElement.getY()) {
        im.fire('activeElementShouldAdjustLayer');
      }
      im.fire('activeElementSizeChange');
    });

    im.bind(['activeElementDragMove', 'activeElementSizeChange', 'activeElementChangingSize', 'adjustedsavers', 'scaleChange'], function(e, data){
      if (data === my) return;
      my.handle.setSize({width:7 / im.scale, height: 7 / im.scale});
      my.handle.setOffset([(4 / im.scale) + .5, (4 / im.scale) + .5]);
      my.handle.setStrokeWidth(1 / im.scale);

      my.position();
      im.controlLayer.draw();
    });

    im.controlLayer.add(this.handle);
  };

  function positionLayer(posChanged) {
    im.activeElement.parent.move(im.activeElement.getPosition());
    im.activeElement.setPosition(0,0);

    im.controlLayer.setPosition(im.activeElement.parent.getPosition());
    im.controlLayer.setOffset(im.activeElement.parent.getOffset());
    im.controlLayer.setScale(im.activeElement.getScale());
  }

  im.bind('activeElementShouldAdjustLayer', function(){
    positionLayer(true);
  });

  var handles = [
    new SizingHandle(SizingHandleTop    | SizingHandleLeft),
    new SizingHandle(SizingHandleTop    | SizingHandleRight),
    new SizingHandle(SizingHandleBottom | SizingHandleLeft),
    new SizingHandle(SizingHandleBottom | SizingHandleRight),
    new SizingHandle(SizingHandleTop),
    new SizingHandle(SizingHandleRight),
    new SizingHandle(SizingHandleBottom),
    new SizingHandle(SizingHandleLeft)
    ];

  im.controlLayer.draw();

  im.bind(['activeElementSizeChange', 'activeElementDragEnd'], function(){
    positionLayer();
    im.activeElement.parent.draw();
  });
  im.bind('ChangeActiveAction', function(e, data) {
    if (data == im.namespace) {
      handles.forEach(function(handle) {
        handle.position();
      });
      im.controlLayer.show();
      im.sizeBoxes.width.val(im.activeElement.getWidth());
      im.sizeBoxes.height.val(im.activeElement.getHeight());
      our.scalingSize.width = im.activeElement.getWidth();
      our.scalingSize.height = im.activeElement.getHeight();
    } else {
      im.controlLayer.hide();
    }
  });

  im.bind('changeActiveElement', function(e) {
    im.ratio.x = im.activeElement.getWidth();
    im.ratio.y = im.activeElement.getHeight();

    if (typeof im.activeControlSet !== "string") {
      im.fire('ChangeActiveAction', im.namespace);
    }
    if (im.activeElement.nodeType === 'Stage') {
      im.controlLayer.hide();

      im.disable();
      im.fire('ChangeActiveAction');
      return;
    } else {
      im.enable();
      if (!im.controlLayer.parent && im.activeControlSet === im.namespace) {
        im.controlLayer.show();
      }
    }
    handles.forEach(function(handle) {
      handle.position();
    });
    im.sizeBoxes.width.val(im.activeElement.getWidth());
    im.sizeBoxes.height.val(im.activeElement.getHeight());
    our.scalingSize.width  = im.activeElement.getWidth();
    our.scalingSize.height = im.activeElement.getHeight();
  });
  im.bind(['activeElementDragMove', 'activeElementSizeChange', 'adjustedsavers', 'changeActiveAction', 'changeActiveElement'], function(){
    setTimeout(function(){ // This always happens after the handles are positioned.
      positionLayer();
      im.controlLayer.draw();
    },0);
  });
}

function Rotation(im, me, elem) {
  var my = this,
      RotationFlipModeVertical = 0,
      RotationFlipModeHorizontal = 1;

  function flip(rotationFlipMode) {
    var scale = im.activeElement.getScale(),
        scaleCopy = {x:scale.x, y:scale.y};
    switch (rotationFlipMode) {
      case RotationFlipModeHorizontal:
        scaleCopy.x *= -1;

        var degChange = im.activeElement.getRotation(),
            r = -scaleCopy.x * im.activeElement.getWidth();
        im.activeElement.parent.move(r * Math.cos(degChange), r * Math.sin(degChange));
        break;
      case RotationFlipModeVertical:
        scaleCopy.y *= -1;

        im.activeElement.rotateDeg(90);
        var degChange = im.activeElement.getRotation(),
            r = -scaleCopy.y * im.activeElement.getHeight();
        im.activeElement.rotateDeg(-90);
        im.activeElement.parent.move(r * Math.cos(degChange), r * Math.sin(degChange));

        break;
    }
    im.activeElement.setScale(scaleCopy);
    im.controlLayer.setScale(scaleCopy);
    im.fire('activeElementShouldAdjustLayer');
    im.activeElement.parent.draw();
    im.fire('rotationChanged');
    setTimeout(function(){
      im.activeElement.parent.draw();
    },0);
  }

  $('button.hflip', me).click(function() {
    flip(RotationFlipModeHorizontal);
  });
  $('button.vflip', me).click(function() {
    flip(RotationFlipModeVertical);
  });
  $('button.rot', me).click(function() {
    var deg = im.activeElement.getRotationDeg();
    deg = (Math.round((deg + 90) / 90) * 90) % 360;
    im.activeElement.setRotationDeg(deg);
    im.adjustSavers();


    im.fire('activeElementShouldAdjustLayer');

    im.fire('rotationChanged');
    setTimeout(function(){
      im.activeElement.parent.draw();
    },0);
  });
  this.box = $('div.angle > input', me).val(0);
  this.slider = $('div.angle > div', me).slider({
    step: 1,
    range: "min",
    min:-180,
    max:180,
    value:0,
    animate: 300,
    slide: function(ev,e){
      my.box.val(e.value);
      im.activeElement.setRotationDeg(e.value);
      im.adjustSavers(false);

      im.fire('activeElementShouldAdjustLayer');
      im.fire('rotationChanged', me);
      im.activeElement.parent.draw();
    }
  });
  im.bind('rotationChanged', function(e, data) {
    if (data === me) return;
    var deg = im.activeElement.getRotationDeg();
    if (deg > 180) deg = (deg % 360) - 360;
    my.box.val(im.activeElement.getRotationDeg());
    my.slider.slider('option', 'value', deg);
  });
}
setTimeout(function(){
  var position = new Positioning(im, me, elem);
  var size = new Sizing(im, me, elem);
  var rotate = new Rotation(im, me, elem);
}, 0);

