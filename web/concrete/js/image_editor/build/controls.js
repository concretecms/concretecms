// Zoom
var controlBar = $(im.stage.getContainer()).parent().children('.bottomBar');

var zoom = {};

zoom.in = $("<span><i class='icon-plus'></i></span>");
zoom.out = $("<span><i class='icon-minus'></i></span>");

zoom.in.appendTo(controlBar);
zoom.out.appendTo(controlBar);

zoom.in.click(function(e){im.fire('zoomInClick',e)});
zoom.out.click(function(e){im.fire('zoomOutClick',e)});

var minScale = 1/3, maxScale = 3, stepScale = 1/3;

im.stage.setDraggable();
im.on('zoomInClick',function(e){
	im.scale += stepScale;
	if (im.scale > maxScale) im.scale = maxScale;
	if (Math.abs(im.scale - Math.round(im.scale)) < stepScale / 2) im.scale = Math.round(im.scale);
	im.stage.setScale(im.scale);
	im.fire('stageChanged');
	im.stage.draw();
});
im.on('zoomOutClick',function(e){
	im.scale -= stepScale;
	if (im.scale < minScale) im.scale = minScale;
	if (Math.abs(im.scale - Math.round(im.scale)) < stepScale / 2) im.scale = Math.round(im.scale);
	im.stage.setScale(im.scale);
	im.fire('stageChanged');
	im.stage.draw();
});




// Save
var saveSize = {};

saveSize.width = $('<input/>');
saveSize.height = $('<input/>');
saveSize.both = saveSize.height.add(saveSize.width).width(32);

saveSize.area = $('<span/>').css({float:'right',margin:'-5px 14px 0 0'});
saveSize.width.appendTo(saveSize.area);
saveSize.area.append($('<span> x </span>'));
saveSize.height.appendTo(saveSize.area);
saveSize.area.appendTo(controlBar);

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
