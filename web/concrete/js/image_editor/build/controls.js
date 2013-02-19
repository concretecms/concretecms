// Zoom
var controlBar = getElem(im.stage.getContainer()).parent().children('.bottomBar');

var zoom = {};

zoom.in = getElem("<span><i class='icon-plus'></i></span>");
zoom.out = getElem("<span><i class='icon-minus'></i></span>");

zoom.in.appendTo(controlBar);
zoom.out.appendTo(controlBar);

zoom.in.click(function(e){im.fire('zoomInClick',e)});
zoom.out.click(function(e){im.fire('zoomOutClick',e)});

var scale = getElem('<span></span>').addClass('scale').text('100%');
im.on('stageChanged',function(e){
	scale.text(Math.round(im.scale * 10000)/100 + "%");
});
scale.appendTo(controlBar);

var minScale = 0, maxScale = 3000, stepScale = 1/4;

im.on('zoomInClick',function(e){
	im.scale += (im.scale * stepScale);
	if (im.scale > maxScale) im.scale = maxScale;
	if (im.scale > stepScale && (Math.abs(im.scale - Math.round(im.scale)) % 1) < stepScale / 2) im.scale = Math.round(im.scale);
	im.stage.setScale(im.scale);
	im.stage.setX(Math.round((im.stage.getWidth() - (im.stage.getWidth() * im.scale))/2));
	im.stage.setY(Math.round((im.stage.getHeight() - (im.stage.getHeight() * im.scale))/2));
	im.fire('stageChanged');
	im.stage.draw();
});
im.on('zoomOutClick',function(e){
	im.scale -= (im.scale * stepScale);
	if (im.scale < minScale) im.scale = minScale;
	if (im.scale > stepScale && (Math.abs(im.scale - Math.round(im.scale)) % 1) < stepScale / 2) im.scale = Math.round(im.scale);
	im.stage.setScale(im.scale);
	im.centerOffset.x = Math.round((im.stage.getWidth() - (im.stage.getWidth() * im.scale))/2);
	im.centerOffset.y = Math.round((im.stage.getHeight() - (im.stage.getHeight() * im.scale))/2);
	im.stage.setX(Math.round((im.stage.getWidth() - (im.stage.getWidth() * im.scale))/2));
	im.stage.setY(Math.round((im.stage.getHeight() - (im.stage.getHeight() * im.scale))/2));
	im.fire('stageChanged');
	im.stage.draw();
});

// Save
var saveSize = {};

saveSize.width = getElem('<input/>');
saveSize.height = getElem('<input/>');
saveSize.both = saveSize.height.add(saveSize.width).width(32);

saveSize.area = getElem('<span/>').css({float:'right',margin:'-5px 14px 0 0'});
saveSize.width.appendTo(saveSize.area);
saveSize.area.append(getElem('<span> x </span>'));
saveSize.height.appendTo(saveSize.area);
saveSize.area.appendTo(controlBar);

var saveButton = $('<button/>').addClass('btn').addClass('btn-primary').text('Save');
saveButton.appendTo(saveSize.area);
saveButton.click(function(){im.save()});


if (im.strictSize) {
	saveSize.both.attr('disabled','true');
} else {
	saveSize.both.keydown(function(e){
		log(e.keyCode);
		if (e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39) return true;

		if (e.keyCode == 38) {
			var newval = parseInt($(this).val()) + 1;
			$(this).val(Math.min(5000,newval)).change();
		}
		if (e.keyCode == 40) {
			var newval = parseInt($(this).val()) - 1;
			$(this).val(Math.max(0,newval)).change();
		}
		var key = String.fromCharCode(e.keyCode);
		if (!key.match(/\d/)) {
			return false;
		}
		var amnt = "" + $(this).val() + key;
		if (amnt > 5000) {
			amnt = 5000;
		}
		$(this).val(amnt).change();

		return false;
	}).keyup(function(e){
		if (e.keyCode == 8) im.fire('editedSize');
	}).change(function(){
		im.fire('editedSize');
	});
}


im.bind('editedSize',function(){
	im.saveWidth = parseInt(saveSize.width.val());
	im.saveHeight = parseInt(saveSize.height.val());

	if (isNaN(im.saveWidth)) im.saveWidth = 0;
	if (isNaN(im.saveHeight)) im.saveHeight = 0;

	im.trigger('saveSizeChange');
	im.adjustSavers();
});

im.bind('saveSizeChange',function(){
	saveSize.width.val(im.saveWidth);
	saveSize.height.val(im.saveHeight);
});
