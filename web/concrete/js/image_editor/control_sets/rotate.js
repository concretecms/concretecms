var me = $(this), self = this, handleLayer = false, handle = new Kinetic.Rect({
	x:0,
	y:0,
	width:7,
	height:7,
	offset:[3.5,-3.5],
	stroke:'black',
	fill:'transparent',
	draggable:true
}), cache = {rotation: 0,scale:{x:1,y:1}};

im.disable();

var anglediv = $('div.angle',me);
var slider = anglediv.children('div.slider').slider({
	step: 1,
	range: "min",
	min:-180,
	max:180,
	value:0,
	animate: 300,
	slide: function(ev,e){
		handle.setRotationDeg(e.value);
		im.activeElement.setRotationDeg(e.value);
		im.activeElement.parent.draw();
		anglediv.children('input').val(-e.value);
		setupLayers();
		if (!shown) {
			cancelbutton.slideDown(250);
			shown = true;
		}
	}
});
anglediv.children('input').keyup(function(){
	var a = parseFloat($(this).val()) || 0;
	handle.setRotationDeg(-a);
	im.activeElement.setRotationDeg(-a);
	slider.slider('option','value',-a);
	im.activeElement.parent.draw();
	setupLayers();
	if (!shown) {
		cancelbutton.slideDown(250);
		shown = true;
	}
}).val(0);

$('button.rot',me).click(function(){
	var me = $(this).attr('disabled',true);
	handle.hide();
	handle.parent.draw();
	im.activeElement.transitionTo({
		rotationDeg:im.activeElement.getRotationDeg() + 45,
        duration: .25,
        callback:function(){
			handle.setRotationDeg(im.activeElement.getRotationDeg());
			im.activeElement.parent.draw();
			setupLayers();
			me.removeAttr('disabled');
			handle.show();
			handle.parent.draw();
        }
	});
	cancelbutton.slideDown(250);
	shown = true;
});
$('button.hflip',me).click(function(){
	var scale = im.activeElement.getScale();
	var btn = $(this).attr('disabled',true);
	im.activeElement.transitionTo({scale:{x:-scale.x,y:scale.y},duration:.5,callback:function(){
		btn.removeAttr('disabled');
	}});
	im.activeElement.parent.draw();

	if (!shown) {
		cancelbutton.slideDown(250);
		shown = true;
	}
});
$('button.vflip',me).click(function(){
	var scale = im.activeElement.getScale();
	var btn = $(this).attr('disabled',true);
	im.activeElement.transitionTo({scale:{x:scale.x,y:-scale.y},duration:.5,callback:function(){
		btn.removeAttr('disabled');
	}});
	im.activeElement.parent.draw();

	if (!shown) {
		cancelbutton.slideDown(250);
		shown = true;
	}
});
var shown = false;
var cancelbutton = $('div.cancelbutton',me).click(function(){
	im.activeElement.setRotationDeg(cache.rotation);
	handle.setRotationDeg(cache.rotation);
	im.activeElement.setScale(cache.scale);
	im.activeElement.parent.draw();
	tearDownLayers();
	setupLayers();

	slider.slider('option','value',cache.rotation);
	cancelbutton.slideUp(250);
	shown = false;
}).slideUp();

handle.setDragBoundFunc(function(pos){
	if (!shown) {
		cancelbutton.slideDown(250);
		shown = true;
	}
	var p = handle.getPosition();
	var a = (Math.round(Math.atan2(p.x,-p.y) / Math.PI * 180 + 180) + 45) % 360;

	var aa = -((360 - a) % 360);
	if (aa < -179) aa += 360;
	slider.slider('option','value',aa);
	anglediv.children('input').val(-aa);
	im.activeElement.setRotationDeg(a);
	handle.setRotationDeg(Math.round(a));
	im.activeElement.parent.draw();

	return pos;
});

im.bind('changeactiveaction',function(e){
	if (e.eventData != im.namespace) {
		tearDownLayers();
		return im.selected = false;
	}
	im.selected = true;
	cache.rotation = im.activeElement.getRotationDeg();

	var s = im.activeElement.getScale();
	cache.scale.x = s.x;
	cache.scale.y = s.y;

	setupLayers();
});
im.bind('changeActiveElement',function(e){ 
	if (im.activeElement.elementType == 'stage') {
		im.disable();
	} else {
		im.enable();
	}
	if (im.selected) {
		if (!im.enabled) {
			im.fire('changeActiveAction');
		}
		cache.rotation = im.activeElement.getRotationDeg();

		var s = im.activeElement.getScale();
		cache.scale.x = s.x;
		cache.scale.y = s.y;

		slider.slider('option','value',cache.rotation);
		anglediv.children('input').val(cache.rotation);
		cancelbutton.slideUp(250);
		shown = false;
		setupLayers();
	}
});

var setupLayers = function() {
	if (!handleLayer) {
		handleLayer = new Kinetic.Layer();
		handleLayer.autoCrop = false;
		handleLayer.add(handle);

		im.stage.add(handleLayer);
	}
	handle.setDraggable(true);

	var elem = im.activeElement, center = {
		x:elem.getX() + elem.getWidth() / 2,
		y:elem.getY() + elem.getHeight() / 2
	};
	handleLayer.setPosition(center);

	var a = elem.getRotationDeg() || 0;
	a = (a + 45) % 360;
	a *= Math.PI/180;

	var r = Math.sqrt(Math.pow(elem.getWidth()/2,2) + Math.pow(elem.getHeight()/2,2));

	var x = r * Math.cos(a);
	var y = r * Math.sin(a);

	handle.setPosition({x:x,y:y});
	handleLayer.draw();
},
tearDownLayers = function() {
	handle.remove();
	if (handleLayer.destroy) {
		handleLayer.destroy();
	}
	handleLayer = false;
};
handle.on('dragend',setupLayers);