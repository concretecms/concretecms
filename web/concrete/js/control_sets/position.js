// Handle selection
im.bind('changecontrolset',function(e){
	console.log(e);
	if (e.eventData != 'position') {
		im.image.setDraggable(false);
	} else {
		im.image.setDraggable(true);
	}
});
me = $(this);

var sliderx = $('div.xslider',me).slider({
	step: 1,
	range: "min",
	min:-im.image.getWidth(),
	max:im.width,
	value:Math.round(im.image.getX()),
	animate: true,
	create: function(ev,e){
		$('input.x',me).val(im.image.getX());
	},
	slide: function(ev,e){
		im.image.setX(e.value);
		im.trigger('imagechange');
	}
});
var slidery = $('div.yslider',me).slider({
	step: 1,
	range: "min",
	min:-im.image.getHeight(),
	max:im.height,
	value:Math.round(im.image.getY()),
	animate: true,
	create: function(ev,e){
		$('input.y',me).val(im.image.getY());
	},
	slide: function(ev,e){
		im.image.setY(e.value);
		im.trigger('imagechange');
	}
});

$('input.y',me).keyup(function(e){
	var m = $(this);
	v = parseInt(Number(m.val().replace(/[^0-9\.\-]/g,'')));
	if (e.keyCode == 38) v++;
	if (e.keyCode == 40) v--;
	im.image.setY(v);
	im.trigger('imagechange');
	e.preventDefault();
});
$('input.x',me).keyup(function(e){
	var m = $(this);
	v = parseInt(Number(m.val().replace(/[^0-9\.\-]/g,'')));
	if (e.keyCode == 38) v++;
	if (e.keyCode == 40) v--;
	im.image.setX(v);
	im.trigger('imagechange');
	e.preventDefault();
});
$('button.center',me).click(function(e){
	im.image.transitionTo({
		x:Math.round(im.width / 2 - im.image.getWidth()/2),
		y:Math.round(im.height / 2 - im.image.getHeight()/2),
		duration:.2,
		callback: function(){
			im.trigger('imagemove');
		}
	})
})
im.image.on('dragend',function(e){
	var x = im.image.getX(), y = im.image.getY();
	im.trigger('imagemove');
	im.trigger('imagechange');
})
im.image.on('dragstart',function(e){
	im.trigger('imagemove');
})
// Use our API, not kinetics.
im.image.on('dragmove',function(e){im.trigger('imagemove',{change:false});});
im.bind('imagechange',function(e){
	var x = im.image.getX(), y = im.image.getY(),
		height=im.image.getHeight(),width=im.image.getWidth();

	// Update Sliders
	sliderx.slider('value',x);
	slidery.slider('value',y);
	$('input.x',me).val(x);
	$('input.y',me).val(y);
	im.image.parent.draw();
});