var me = $(this), elem = this;
im.selected = false;
im.disable();
im.origin = {};
im.bind('changeActiveElement',function(e){
	if (e.eventData.elementType == 'stage') return im.disable();
	im.enable();
	if (im.selected) {
		if (im.currentElement) {
			im.currentElement.setDraggable(false);
		}
		im.currentElement = e.eventData;
		im.currentElement.setDraggable(true);
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
im.bind('ChangeActiveAction',function(e){
	if (e.eventData == im.namespace) {
		im.selected = true;
		im.currentElement = im.activeElement;
		im.currentElement.setDraggable(true);
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
		im.activeElement.setY(-e.value + Math.round(im.center.y - im.saveHeight/2));
		im.trigger('activeElementMove', e);
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
		im.activeElement.setX(e.value + Math.round(im.center.x - im.saveWidth/2));
		im.trigger('activeElementMove', e);
		im.trigger('sliderMove', e, elem);
	},
	stop: function(ev,e){
		im.trigger('activeElementDragMove', e);
		im.trigger('activeElementDragMove', e, elem);
		updateSliders();
	}
});

im.bind('sliderMove',function(){
	im.currentElement.parent.draw();
	im.buildBackground();
	$('div.horizontal > input').val(sliderx.slider('option', 'value') || "0");
	$('div.vertical > input').val(slidery.slider('option', 'value') || "0");
	updateInputs();
},elem);

var updateSliders = function() {
	sliderx.slider('option', 'value', -Math.round(Math.round(im.center.x - im.saveWidth/2) - im.currentElement.attrs.x));
	slidery.slider('option', 'value', Math.round(Math.round(im.center.y - im.saveHeight/2) - im.currentElement.attrs.y));
	updateInputs();
};
var inputx = $('div.horizontal > input',me), inputy = $('div.vertical > input',me);
var updateInputs = function() {
	inputx.val(-Math.round(Math.round(im.center.x - im.saveWidth/2) - im.currentElement.attrs.x) || "0");
	inputy.val(Math.round(Math.round(im.center.y - im.saveHeight/2) - im.currentElement.attrs.y) || "0");
};

$('button.up',me).click(function(e) {
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
	sliderx.slider('value',-Math.round(Math.round(im.center.x - im.saveWidth/2) - (im.center.x - im.currentElement.attrs.width/2)));
	slidery.slider('value',Math.round(Math.round(im.center.y - im.saveHeight/2) - (im.center.y - im.currentElement.attrs.height/2)));
	im.currentElement.transitionTo({
		x:Math.round(im.center.x - im.currentElement.attrs.width/2),
		y:Math.round(im.center.y - im.currentElement.attrs.height/2),
		duration:.3,
		callback:updateSliders
	});
});
$('div.cancelbutton',me).click(function(e) {
	sliderx.slider('value', -Math.round(Math.round(im.center.x - im.saveWidth/2) - im.origin.x));
	slidery.slider('value',Math.round(Math.round(im.center.y - im.saveHeight/2) - im.origin.y));
	im.currentElement.transitionTo({
		x:im.origin.x,
		y:im.origin.y,
		duration:.3,
		callback:updateSliders
	});
});