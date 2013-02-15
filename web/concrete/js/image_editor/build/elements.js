im.addElement = function(object,type) {
	var layer = new Kinetic.Layer();
	layer.add(object);
	object.setX(im.center.x - Math.round(object.getWidth() / 2));
	object.setY(im.center.y - Math.round(object.getHeight() / 2));

	object.on('click',function(){
		im.setActiveElement(this);
	});
	im.stage.add(layer);
	im.fire('newObject',{object:object,type:type});
	im.stage.draw();
};
im.setActiveElement = function(element) {
	im.trigger('beforeActiveElementChange',im.activeElement);
	im.activeElement = element;
	im.trigger('activeElementChange',element);
	im.stage.draw();
};