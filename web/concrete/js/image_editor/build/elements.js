im.addElement = function(object,type) {
  var layer = new Kinetic.Layer();
  layer.elementType = layer;
  layer.add(object);
  object.setX(im.center.x - Math.round(object.getWidth() / 2));
  object.setY(im.center.y - Math.round(object.getHeight() / 2));

  object.doppelganger = object.clone();
  if (type == 'image') object.doppelganger.setImage('');
  object.doppelganger.doppelganger = object;
  object.doppelganger.setDrawHitFunc(function(){return false});
  object.doppelganger.setFill('transparent');
  object.doppelganger.elementType = 'StokeClone';
  object.doppelganger.setStroke('blue');
  object.doppelganger.setStrokeWidth(5);
  object.doppelganger._drawFunc = object.getDrawFunc();
  object.doppelganger.setDrawFunc(function(canvas){
    this.setStrokeWidth(3/im.scale);
    this.setPosition(this.doppelganger.getPosition());
    this.setSize(this.doppelganger.getSize());
    this.setRotation(this.doppelganger.getRotation());
    this.setRotationDeg(this.doppelganger.getRotationDeg());
    this._drawFunc(canvas);
  });

  object.elementType = type;

  object.on('click',function(){
    im.fire('ClickedElement',this);
  });
  object._drawFunc = object.getDrawFunc();
  object.setDrawFunc(function(canvas) {
    for(var i in this.attrs) {
      if (i == 'drawFunc') continue;
      this.doppelganger.attrs[i] = this.attrs[i];
    }
    this.doppelganger.setSize(this.getSize());
    this.doppelganger.setPosition(this.getPosition());
    this.doppelganger.setDrawHitFunc(function(){return false});
    this.doppelganger.setFill('transparent');
    this.doppelganger.elementType = 'StokeClone';
    this.doppelganger.setStroke('blue');
    this.doppelganger.setStrokeWidth(5);
    if (this.elementType == 'image') this.doppelganger.setImage('');
    im.foreground.draw();
    object._drawFunc(canvas);
  });

  object.on('mouseover',function(){
    this.hovered = true;
    //im.stage.setDraggable(false);
    im.setCursor('pointer');
  });
  object.on('mouseout',function(){
    //im.stage.setDraggable(true);
    if (this.hovered == true) {
      im.setCursor('');
      this.hovered = false;
    }
  });

  im.stage.add(layer);
  im.fire('newObject',{object:object,type:type});
  im.foreground.moveToTop();
  im.stage.draw();
};

im.on('backgroundBuilt',function(){
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.foreground.add(im.activeElement.doppelganger);
  }
});

im.setActiveElement = function(element) {
  if (im.activeElement == element) return;
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.activeElement.doppelganger.remove();
  }
  if (element === im.stage || element.nodeType == 'Stage') {
    im.trigger('ChangeActiveAction','ControlSet_Position');
    $('div.control-sets',im.controlContext).find('h4.active').removeClass('active');
  } else if (element.doppelganger !== undefined) {
    im.foreground.add(element.doppelganger);
    im.foreground.draw();
  }
  im.trigger('beforeChangeActiveElement',im.activeElement);
  im.alterCore('activeElement',element);
  im.trigger('changeActiveElement',element);
  im.stage.draw();
};
im.bind('ClickedElement',function(e) {
  if (e.eventData.getWidth() > im.stage.getScaledWidth() || e.eventData.getHeight() > im.stage.getScaledHeight()) {
    im.setActiveElement(im.stage);
    return;
  }

  im.setActiveElement(e.eventData);
});

im.bind('stageChanged',function(e){
  if (im.activeElement.getWidth() > im.stage.getScaledWidth() || im.activeElement .getHeight() > im.stage.getScaledHeight()) {
    im.setActiveElement(im.stage);
  }
});