im.addElement = function(object,type) {
  var layer = new Kinetic.Layer();
  layer.add(object);
  object.setX(im.center.x - Math.round(object.getWidth() / 2));
  object.setY(im.center.y - Math.round(object.getHeight() / 2));

  object.elementType = type;

  object.on('click',function(){
    im.fire('ClickedElement',this);
  });
  im.stage.add(layer);
  im.fire('newObject',{object:object,type:type});
  im.stage.draw();
};
im.setActiveElement = function(element) {
  if (im.activeElement == element) return;

  if (element === im.stage) {
    im.trigger('ChangeActiveAction','ControlSet_Position');
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