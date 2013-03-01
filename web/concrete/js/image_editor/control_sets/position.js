// Handle selection
im.selected = false;
im.bind('changeActiveElement',function(e){
	if (im.selected) im.activeElement.setDraggable(true);
	if (im.activeElement.elementType == 'stage') {
		im.disable();
		return;
	}
	im.enable();
	if (im.activeElement.isBound !== true) {
		im.activeElement.on('dragmove',function(e){im.trigger('activeElementDragMove',e)});
		im.activeElement.isBound = true;
	}
	updateSliders();
});
im.bind('beforeChangeActiveElement',function(e){
	if (im.activeElement.elementType == 'stage') return;
	im.activeElement.setDraggable(false);
});
im.bind('ChangeActiveAction',function(e){
	if (e.eventData != im.namespace) {
		im.selected = false;
		if (im.activeElement.elementType != 'stage') {
			im.activeElement.setDraggable(false);
		}
	} else {
		im.selected = true;
		im.activeElement.setDraggable(true);
	}
});
var me = $(this);
im.disable();

var sliderx = $('div.xslider',me).slider({
	step: 1,
	range: "min",
	min:-im.activeElement.getWidth(),
	max:im.width,
	value:Math.round(im.activeElement.getX()),
	animate: true,
	create: function(ev,e){
		$('input.x',me).val(im.activeElement.getX());
	},
	slide: function(ev,e){
		im.activeElement.setX(e.value);
		im.trigger('activeElementMove');
	}
});
var slidery = $('div.yslider',me).slider({
	step: 1,
	range: "min",
	min:-im.activeElement.getHeight(),
	max:im.height,
	value:Math.round(im.activeElement.getY()),
	animate: true,
	create: function(ev,e){
		$('input.y',me).val(im.activeElement.getY());
	},
	slide: function(ev,e){
		im.activeElement.setY(e.value);
		im.trigger('activeElementMove');
	}
});

var updateSliders = function() {
	var max = {x:im.stage.getWidth(),y:im.stage.getHeight()},
		min = {x:-im.activeElement.getWidth(),y:-im.activeElement.getHeight()},
		cur = {x:im.activeElement.getX(),y:im.activeElement.getY()};
	sliderx.slider("option", "min", min.x);
	sliderx.slider("option", "max", max.x);
	sliderx.slider('value',cur.x);
	$('input.x',me).val(cur.x);

	slidery.slider("option", "min", min.y);
	slidery.slider("option", "max", max.y);
	slidery.slider('value',cur.y);
	$('input.y',me).val(cur.y);
};

updateSliders();

$('input.y',me).keyup(function(e){
	var m = $(this);
	v = parseInt(Number(m.val().replace(/[^0-9\.\-]/g,'')));
	if (e.keyCode == 38) v++;
	if (e.keyCode == 40) v--;
	im.activeElement.setY(v);
	im.trigger('activeElementMove');
	e.preventDefault();
});
$('input.x',me).keyup(function(e){
	var m = $(this);
	v = parseInt(Number(m.val().replace(/[^0-9\.\-]/g,'')));
	if (e.keyCode == 38) v++;
	if (e.keyCode == 40) v--;
	im.activeElement.setX(v);
	im.trigger('activeElementMove');
	e.preventDefault();
});
$('button.up',me).click(function(e) {
	im.activeElement.parent.moveUp();
  	im.foreground.moveToTop();
});
$('button.down',me).click(function(e) {
	// Don't go below the savers
	if (im.activeElement.parent.getZIndex() - im.background.getZIndex() == 1) return;
	im.activeElement.parent.moveDown();
});
$('button.center',me).click(function(e){
	im.activeElement.transitionTo({
		x:Math.round(im.width / 2 - im.activeElement.getWidth()/2),
		y:Math.round(im.height / 2 - im.activeElement.getHeight()/2),
		duration:.2,
		callback: function(){
			im.trigger('activeElementMove');
		}
	})
})
im.activeElement.on('dragend',function(e){
	var x = im.activeElement.getX(), y = im.activeElement.getY();
	im.trigger('activeElementDragMove');
	im.trigger('activeElementMove');
})
im.activeElement.on('dragstart',function(e){
	im.trigger('activeElementDragMove');
})
// Use our API, not kinetics.
im.activeElement.on('dragmove',function(e){im.trigger('activeElementDragMove',e)});

im.bind('activeElementMove',function(e){
	var x = im.activeElement.getX(), y = im.activeElement.getY(),
		height=im.activeElement.getHeight(),width=im.activeElement.getWidth();

	// Update Sliders
	sliderx.slider('value',x);
	slidery.slider('value',y);
	$('input.x',me).val(x);
	$('input.y',me).val(y);
	if (im.activeElement.parent) {
		im.activeElement.parent.draw();
	} else if (typeof im.activeElement.draw == 'function') {
		im.activeElement.draw();
	}
});