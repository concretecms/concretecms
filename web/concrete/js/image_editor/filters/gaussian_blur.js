var me = this;
im.bind('filterFullyLoaded',function(e){
	if (e.eventData.namespace === me.namespace){
		//This is me, start initialization.
		me.label.text(me.name);
	}
});

var blur = function(imageData) {
	// example filter, not actually blur.
	for (var i = 0, l = imageData.data.length; i<l; i+=4) {
		var r = imageData.data[i],
			g = imageData.data[i+1],
			b = imageData.data[i+2],
			a = imageData.data[i+1]; // Alpha

		imageData.data[i] = 255; // Example change.
	}
};

im.bind('filterChange',function(e){
	if (e.eventData.namespace === me.namespace) {
		if (!me.controls.hasClass('active')) return;
		// Just apply, there is no variation.
		me.label.click();

		console.log('applyFilter');
		$.fn.dialog.showLoader();
		im.image.applyFilter(blur,{},function(){
			$.fn.dialog.hideLoader();
			im.fire('filterApplied', me);
			im.fire('SepiaFilterDidFinish');
			im.image.parent.draw();
		});
		// Apply Filter
	}
});