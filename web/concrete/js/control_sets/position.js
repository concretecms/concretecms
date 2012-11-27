var me = $(this);
var snap = $('input.snap',me)[0];

// Pretty Layer Setup
im.layer = new Kinetic.Layer();
var xaxis = new Kinetic.Line({
	points: [Math.round(im.width/2),0,Math.round(im.width/2),im.height],
	stroke: 'white',
	opacity:0.7,
});
var xaxisshadow = new Kinetic.Line({
	points: [Math.round(im.width/2)+1,0,Math.round(im.width/2)+1,im.height],
	stroke: '#333',
	opacity:0.5,
});
im.layer.add(xaxis);
im.layer.add(xaxisshadow);
var yaxis = new Kinetic.Line({
	points: [0,Math.round(im.height/2),im.width,Math.round(im.height/2)],
	stroke: 'white',
	opacity:0.7
});
var yaxisshadow = new Kinetic.Line({
	points: [0,Math.round(im.height/2)-1,im.width,Math.round(im.height/2)-1],
	stroke: '#333',
	opacity:0.5
});
im.layer.add(yaxis);
im.layer.add(yaxisshadow);
var center = [
	im.image.getX() + im.image.getWidth() / 2,
	im.image.getY() + im.image.getHeight() / 2,
]
im.origin = [
	new Kinetic.Line({
		points: [center[0]-10,center[1],center[0]+10,center[1]],
		stroke: 'black',
	}),
	new Kinetic.Line({
		points: [center[0],center[1]-10,center[0],center[1]+10],
		stroke: 'black',
	}),
	new Kinetic.Rect({
		x:im.image.getX(),
		y:im.image.getY(),
		width:im.image.getWidth(),
		height:im.image.getHeight(),
		stroke:'black'
	})
];
im.layer.add(im.origin[0]);
im.layer.add(im.origin[1]);
im.layer.add(im.origin[2]);
im.stage.add(im.layer);

// Do stuff when event is fired
im.bind('changecontrolset',function(e){
	if (e.memo != 'position') {
		im.image.setDraggable(false);
		im.layer.hide();
	} else {
		im.image.setDraggable(true);
	}
});

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
	im.layer.hide();
})
im.image.on('dragstart',function(e){
	im.layer.show();
	im.trigger('imagemove',{change:false});
})
// Use our API, not kinetics.
im.image.on('dragmove',function(e){im.trigger('imagemove',{change:false});});
im.bind('imagemove',function(e){
	// Handle Snapping
	var x = im.image.getX(), y = im.image.getY(),
		height=im.image.getHeight(),width=im.image.getWidth();
	var change = (function() {
		xaxis.show();
		yaxis.show();
		xaxisshadow.show();
		yaxisshadow.show();
		if (!snap.checked) return false;
		var changed = false;
		xaxis.hide();
		xaxisshadow.hide();
		// If x is within 10 pixels of 0 x
		if (Math.abs(x) < 10) {
			im.image.setX(0);
			changed=true;
		}
		// If the right image x is within 10 pixels of right stage x
		else if (Math.abs(x + width - im.width) < 10) {
			im.image.setX(im.width - width);
			changed=true;
		}
		// If the median image x is within 10 pixels of the median stage x
		else if (Math.abs(x + width / 2 - im.width / 2) < 10) {
			im.image.setX(Math.round(im.width / 2 - width / 2));
			changed=true;
			xaxis.show();
			xaxisshadow.show();
		}
		// If x is within 10 pixels of the median stage x
		else if (Math.abs(x - im.width / 2) < 10) {
			im.image.setX(Math.round(im.width / 2));
			changed=true;
			xaxis.show();
			xaxisshadow.show();
		}
		// If right x is within 10 pixels of the median stage x
		else if (Math.abs(x + width - im.width / 2) < 10) {
			im.image.setX(Math.round(im.width / 2) - width);
			changed=true;
			xaxis.show();
			xaxisshadow.show();
		}
		yaxis.hide();
		yaxisshadow.hide();
		// If y is within 10 pixels of 0 y
		if (Math.abs(y) < 10) {
			im.image.setY(0);
			changed=true;
		}
		// If the bottom image y is within 10 pixels of bottom stage y
		else if (Math.abs(y + height - im.height) < 10) {
			im.image.setY(im.height - height);
			changed=true;
			yaxis.show();
			yaxisshadow.show();
		}
		// If the median image y is within 10 pixels of the median stage y
		else if (Math.abs(y + height / 2 - im.height / 2) < 10) {
			im.image.setY(Math.round(im.height / 2 - height / 2));
			changed=true;
			yaxis.show();
			yaxisshadow.show();
		}
		// If y is within 10 pixels of the median stage y
		else if (Math.abs(y - im.height / 2) < 10) {
			im.image.setY(Math.round(im.height / 2));
			changed=true;
			yaxis.show();
			yaxisshadow.show();
		}
		// If bottom y is within 10 pixels of the median stage y
		else if (Math.abs(y + height - im.height / 2) < 10) {
			im.image.setY(Math.round(im.height / 2) - height);
			changed=true;
			yaxis.show();
			yaxisshadow.show();
		}
		return changed;
	})()
	// Reset because snapping fudges with stuff.
	var x = im.image.getX(), y = im.image.getY(),
		height=im.image.getHeight(),width=im.image.getWidth();

	// Update origin markers
	var center = [
		x + width / 2,
		y + height / 2,
	];
	im.origin[0].setPoints([center[0]-10,center[1],center[0]+10,center[1]]);
	im.origin[1].setPoints([center[0],center[1]-10,center[0],center[1]+10]);
	im.origin[2].setX(x);
	im.origin[2].setY(y);
	im.origin[2].setWidth(im.image.getWidth());
	im.origin[2].setHeight(im.image.getHeight());
	im.layer.draw();
	if (e.memo.change !== false) im.trigger('imagechange');
});
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