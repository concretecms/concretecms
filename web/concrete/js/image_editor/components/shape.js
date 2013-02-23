im.selected = false;

im.bind('changeActiveComponent',function(e){
	if (e.eventData == im.namespace) {
		im.selected = true;
	} else {
		im.selected = false;
	}
});


var me = $(this);
me.find('button').click(function(){
	var type = me.find('select').val(),
		fill = me.find('input.fill').val(),
		stroke = me.find('input.stroke').val();
	if (type == 'rect') {
		var rect =  new Kinetic.Rect({
			width:Math.round(im.saveWidth / 2),
			height:Math.round(im.saveHeight / 2),
			x:0,
			y:0,
			fill:fill,
			stroke:stroke
		});
		im.addElement(rect,'shape');
	} else {
		var star =  new Kinetic.Star({
			width:Math.round(im.saveWidth / 2),
			height:Math.round(im.saveHeight / 2),
			x:0,
			y:0,
			fill:fill,
			stroke:stroke
		});
		im.addElement(star,'shape');
	}
});