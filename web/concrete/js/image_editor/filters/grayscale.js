var me = this;
im.bind('filterFullyLoaded',function(e){
	if (e.eventData.im.namespace === me.im.namespace){
		//This is me, start initialization.
	}
});
im.bind('filterChange',function(e){
	if (e.eventData.im.namespace === me.im.namespace) {
		// Just apply, there is no variation.
		im.showLoader('Applying Grayscale');
		setTimeout(function(){
			im.activeElement.applyFilter(im.filter.grayscale,{},function(){
				$.fn.dialog.hideLoader();
				im.fire('filterApplied', me);
				im.activeElement.parent.draw();
			});
			// Apply Filter
		}, 10);
	}
});
im.bind('filterApplyExample',function(e){
	if (e.eventData.namespace === me.im.namespace) {
		e.eventData.image.applyFilter(im.filter.grayscale,{},function(){
			im.fire('filterBuiltExample', me,  e.eventData.elem);
		});
	}
});