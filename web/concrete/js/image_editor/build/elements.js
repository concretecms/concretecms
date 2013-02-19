im.addElement = function(object,type) {
	var layer = new Kinetic.Layer();
	layer.add(object);
	object.setX(im.center.x - Math.round(object.getWidth() / 2));
	object.setY(im.center.y - Math.round(object.getHeight() / 2));

	object.elementType = type;

	object.on('click',function(){
		log('clicked Element',this);
		im.fire('ClickedElement',this);
	});
	im.stage.add(layer);
	im.fire('newObject',{object:object,type:type});
	im.stage.draw();
};
im.setActiveElement = function(element) {
	im.trigger('beforeChangeActiveElement',im.activeElement);
	im.alterCore('activeElement',element);
	im.trigger('changeActiveElement',element);
	im.stage.draw();
};
im.bind('ClickedElement',function(e) {
  im.setActiveElement(e.eventData);
});
