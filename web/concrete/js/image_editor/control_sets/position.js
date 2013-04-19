var me = $(this), elem = this;
im.selected = false;
im.bind('changeActiveElement',function(e){
	console.log(e.eventData);
	if (e.eventData.elementType == 'stage') return im.disable();
	im.enable();
	if (im.selected) {
		im.currentElement.setDraggable(false);
		im.currentElement = e.eventData;
		im.currentElement.setDraggable(true);

		updateSliders();
	}
});
im.bind('ChangeActiveAction',function(e){
	if (e.eventData == im.namespace) {
		im.selected = true;
		im.currentElement = im.activeElement;
		im.currentElement.setDraggable(true);
	} else {
		im.selected = false;
		im.currentElement.setDraggable(false);
	}
});

var sliderx = $('div.horizontal > div',me).slider({
	step: 1,
	range: "min",
	min:-im.saveWidth,
	max:im.saveWidth,
	value:0,
	animate: true,
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
var slidery = $('div.vertical > div',me).slider({
	step: 1,
	range: "min",
	min:-im.saveHeight,
	max:im.saveHeight,
	value:0,
	animate: true,
	slide: function(ev,e){
		im.activeElement.setY(e.value + Math.round(im.center.y - im.saveHeight/2));
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
	console.log('triggered');
	im.currentElement.parent.draw();
	im.buildBackground();
},elem);

var updateSliders = function() {
	sliderx.slider('option', 'value', Math.round(Math.round(im.center.x - im.saveWidth/2) - im.currentElement.attrs.x));
	slidery.slider('option', 'value', Math.round(Math.round(im.center.y - im.saveheight/2) - im.currentElement.attrs.y));
};