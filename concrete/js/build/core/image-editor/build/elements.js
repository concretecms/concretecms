im.addElement = function(object, type) {
  var layer = new Kinetic.Layer();
  object.elementType = type;
  layer.elementType = type;

  layer.add(object);
  im.stage.add(layer);
  layer.moveDown();
  im.stage.draw();
};

im.on('backgroundBuilt',function(){
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.foreground.add(im.activeElement.doppelganger);
    im.activeElement.doppelganger.setPosition(im.activeElement.getPosition());
  }
});

im.setActiveElement = function(element) {
  if (element.defer) {
    return im.setActiveElement(element.defer);
  }
  if (im.activeElement == element) return;
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.activeElement.doppelganger.remove();
  }
  if (element === im.stage || element.nodeType == 'Stage') {
    im.trigger('ChangeActiveAction', im.controlSetNamespaces.length ? im.controlSetNamespaces[0] : undefined);
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
im.bind('ClickedElement',function(e, data) {
  im.setActiveElement(data);
});

im.bind('stageChanged',function(e){
  if (im.activeElement.getWidth() > im.stage.getScaledWidth() || im.activeElement .getHeight() > im.stage.getScaledHeight()) {
    im.setActiveElement(im.stage);
  }
});
