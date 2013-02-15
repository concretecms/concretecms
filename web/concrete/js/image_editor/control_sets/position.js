// Handle selection
im.bind('ChangeActiveAction',function(e){
	if (e.eventData != im.namespace) {
		im.activeElement.setDraggable(false);
	} else {
		im.activeElement.setDraggable(true);
	}
});
var me = $(this);

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
		im.trigger('imagechange');
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
		im.trigger('imagechange');
	}
});

$('input.y',me).keyup(function(e){
	var m = $(this);
	v = parseInt(Number(m.val().replace(/[^0-9\.\-]/g,'')));
	if (e.keyCode == 38) v++;
	if (e.keyCode == 40) v--;
	im.activeElement.setY(v);
	im.trigger('imagechange');
	e.preventDefault();
});
$('input.x',me).keyup(function(e){
	var m = $(this);
	v = parseInt(Number(m.val().replace(/[^0-9\.\-]/g,'')));
	if (e.keyCode == 38) v++;
	if (e.keyCode == 40) v--;
	im.activeElement.setX(v);
	im.trigger('imagechange');
	e.preventDefault();
});
$('button.center',me).click(function(e){
	im.activeElement.transitionTo({
		x:Math.round(im.width / 2 - im.activeElement.getWidth()/2),
		y:Math.round(im.height / 2 - im.activeElement.getHeight()/2),
		duration:.2,
		callback: function(){
			im.trigger('imagemove');
			im.trigger('imagechange');
		}
	})
})
im.activeElement.on('dragend',function(e){
	var x = im.activeElement.getX(), y = im.activeElement.getY();
	im.trigger('imagemove');
	im.trigger('imagechange');
})
im.activeElement.on('dragstart',function(e){
	im.trigger('imagemove');
})
// Use our API, not kinetics.
im.activeElement.on('dragmove',function(e){im.trigger('imagemove',{change:false});});
im.bind('imagechange',function(e){
	var x = im.activeElement.getX(), y = im.activeElement.getY(),
		height=im.activeElement.getHeight(),width=im.activeElement.getWidth();

	// Update Sliders
	sliderx.slider('value',x);
	slidery.slider('value',y);
	$('input.x',me).val(x);
	$('input.y',me).val(y);
	im.activeElement.parent.draw();
});