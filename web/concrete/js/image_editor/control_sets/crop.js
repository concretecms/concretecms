im.active = false;
me = this;
console.log(im, me);

function CropSetup(im, me, $) {
  var my = this;

  my.activeElement = null;
  my.stage = im.stage;
  my.namespace = im.namespace;



  my.cropLayer = new Kinetic.Layer({
    draggable: true,
    x: im.saveArea.getX(),
    y: im.saveArea.getY()
  });
  my.cropGroup = new Kinetic.Group();

  my.cropper = new Kinetic.Rect({
    width:       im.saveWidth + 100000,
    height:      im.saveHeight + 100000,
    x:           0,
    y:           0,
    stroke:      'rgba(0,0,0,.5)',
    strokeWidth: 100000,
    offset:      [50000, 50000]
  });

  my.cropperWidth = function getCropperWidth() {
    var v = my.cropper.getWidth() - my.cropper.getStrokeWidth();
    my.widthInput.val(v);
    return v;
  };
  my.cropperHeight = function getCropperHeight() {
    var v = my.cropper.getHeight() - my.cropper.getStrokeWidth();
    my.heightInput.val(v);
    return v;
  };
  my.setCropperWidth = function setCropperWidth(v) {
    my.cropper.setWidth(Math.max(1, v) + my.cropper.getStrokeWidth());
    my.cropperWidth();
  };
  my.setCropperHeight = function setCropperHeight(v) {
    my.cropper.setHeight(Math.max(1, v) + my.cropper.getStrokeWidth());
    my.cropperHeight();
  };
  my.cropperX =         function getCropperX() { return my.cropLayer.getX(); };
  my.cropperY =         function getCropperY() { return my.cropLayer.getY(); };
  my.setCropperX =      function setCropperX(v) { return my.cropLayer.setX(v); };
  my.setCropperX =      function setCropperY(v) { return my.cropLayer.setX(v); };


  // Elements
  $('.btn.crop', me).click(function cropButtonClicked(){
    my.crop();
  });
  my.widthInput = $('div.widthinput input', me).keyup(function(){
    var width = parseInt(my.widthInput.val());
    if (isNaN(width)) width = 0;
    my.setCropperWidth(width);
    my.cropLayer.draw();
  });
  my.heightInput = $('div.heightinput input', me).keyup(function(){
    var height = parseInt(my.heightInput.val());
    if (isNaN(height)) height = 0;
    my.setCropperHeight(height);
    my.cropLayer.draw();
  });

  // Dragger
  // This is required because our width and height are artificial.
  my.dragger = new Kinetic.Rect({ draggable: true });
  my.dragger.getWidth =  my.cropperWidth;
  my.dragger.getHeight = my.cropperHeight;
  my.cropGroup.add(my.dragger);

  // Cropper Lines
  // Vertical Left
  my.cropperLineVLeft = new Kinetic.Line({
    stroke:'rgba(0,0,0,.3)',
    strokeWidth:1
  });
  my.cropperLineVLeft.getPoints = function getCropperLinePoints() {
    return [ {x: my.cropperWidth() / 3, y: 0}, {x: my.cropperWidth() / 3, y: my.cropperHeight()} ];
  };
  my.cropGroup.add(my.cropperLineVLeft);

  // Vertical Right
  my.cropperLineVRight = my.cropperLineVLeft.clone();
  my.cropperLineVRight.getPoints = function getCropperLinePoints() {
    return [ {x: my.cropperWidth() / 3 * 2, y: 0},  {x: my.cropperWidth() / 3 * 2, y: my.cropperHeight()} ];
  };
  my.cropGroup.add(my.cropperLineVRight);

  // Horizontal Top
  my.cropperLineHTop = my.cropperLineVLeft.clone();
  my.cropperLineHTop.getPoints = function getCropperLinePoints() {
    return [ {x: 0, y: my.cropperHeight() / 3}, {x: my.cropperWidth(), y: my.cropperHeight() / 3} ];
  };
  my.cropGroup.add(my.cropperLineHTop);

  // Horizontal Bottom
  my.cropperLineHBottom = my.cropperLineVLeft.clone();
  my.cropperLineHBottom.getPoints = function getCropperLinePoints() {
    return [ {x: 0, y: my.cropperHeight() / 3 * 2}, {x: my.cropperWidth(), y: my.cropperHeight() / 3 * 2} ];
  };
  my.cropGroup.add(my.cropperLineHBottom);

  my.cropGroup.add(my.cropper);
  my.cropLayer.add(my.cropGroup);
  my.cropGroup.add(my.dragger);

  // Kinetic Events
  my.startPosition = false;
  my.cropLayer.setDragBoundFunc(function dragBoundFunc(position){
    var mouseposition = my.stage.getMousePosition();
    if (!my.startPosition) {
      my.setCropperWidth(0);
      my.setCropperHeight(0);

      return mouseposition;
    }
    var width = Math.abs(mouseposition.x - my.startPosition.x),
        height = Math.abs(mouseposition.y - my.startPosition.y);

    my.setCropperWidth(width);
    my.setCropperHeight(height);

    var retposition = {
      x: Math.min(mouseposition.x, my.startPosition.x),
      y: Math.min(mouseposition.y, my.startPosition.y)
    };
    return retposition;
  });
  my.cropLayer.on('dragstart', function dragStartEvent() {
    var mouseposition = my.stage.getMousePosition();

    my.startPosition = mouseposition;
    this.setPosition(mouseposition);
  });
  my.cropLayer.on('dragend', function dragEndEvent(){
    my.startPosition = false;
  });
  my.cropLayer.on('mouseup', function dragEndEvent(){
    my.startPosition = false;
  });
  my.dragger.setDragBoundFunc(function draggerBoundFunc(position) {
    my.cropLayer.setPosition(position);
    return position;
  });

  my.crop = function Crop() {
    var layer = im.activeElement.parent,
        oldPosition = { x: layer.getX(), y: layer.getY() },
        oldScale = im.scale,
        oldStagePosition = { x: my.stage.getX(), y: my.stage.getY() };

    layer.setPosition(oldPosition.x - my.cropLayer.getX(), oldPosition.y - my.cropLayer.getY());
    my.stage.setScale(1);
    my.stage.setPosition(0, 0);

    my.stage.draw();
    layer.toImage({
      width: my.cropperWidth(),
      height: my.cropperHeight(),
      callback: function cropCallback(img) {
        im.activeElement.setImage(img);
        im.activeElement.setRotation(0);
        im.stage.setPosition(oldStagePosition);
        im.stage.setScale(oldScale);
        im.activeElement.setWidth(my.cropperWidth());
        im.activeElement.setHeight(my.cropperHeight());
        im.adjustSavers();
        my.cropLayer.setPosition(layer.getPosition());
        im.stage.draw();
      }
    })
  };

  // Event Bindings
  im.bind('changeActiveAction', function(e, data) {
    if (data === my.namespace) {
      my.stage.add(my.cropLayer);
    } else if (my.cropLayer.parent) {
      my.cropLayer.remove();
    }
  });
}

_.defer(function CropSetupTimer(){
  im.instance = new CropSetup(im, me, $);
});
