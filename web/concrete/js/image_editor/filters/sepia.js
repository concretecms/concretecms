var me = this;
im.bind('filterFullyLoaded',function(e){
	if (e.eventData.namespace === me.namespace){
		//This is me, start initialization.
		me.label.text(me.name);
	}
});
im.bind('filterChange',function(e){
	if (e.eventData.namespace === me.namespace) {
		if (!me.controls.hasClass('active')) return;
		// Just apply, there is no variation.
		me.label.click();

		console.log('applyFilter');
		$.fn.dialog.showLoader();
		im.image.applyFilter(im.filter.grayscale,{},function(){
			$.fn.dialog.hideLoader();
			im.fire('filterApplied', me);
			im.fire('SepiaFilterDidFinish');
			im.image.parent.draw();
		});
		// Apply Filter
	}
});