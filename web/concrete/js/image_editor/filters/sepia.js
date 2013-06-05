var me = this;
im.bind('filterFullyLoaded',function(e){
	if (e.eventData.im.namespace === me.im.namespace){
		//This is me, start initialization.
	}
});
im.bind('filterChange',function(e){
	if (e.eventData.im.namespace === me.im.namespace) {
		im.showLoader('Applying Sepia Filter');
		setTimeout(function(){
			// Just apply, there is no variation.

			im.activeElement.applyFilter(im.filter.sepia,{},function(){
				im.hideLoader();
				im.fire('filterApplied', me);
				im.activeElement.parent.draw();
			});
			// Apply Filter
		},10); // Allow loader to show
	}
});
im.bind('filterApplyExample',function(e){
	if (e.eventData.namespace === me.namespace) {
		e.eventData.image.applyFilter(im.filter.sepia,{},function(){
			im.fire('filterBuiltExample', me, e.eventData.elem);
		});
	}
});