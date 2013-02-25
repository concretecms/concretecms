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
		im.showLoader('Applying Sepia Filter');
		me.controls.stop(1,1).hide();
		me.label.click();

		setTimeout(function(){
			// Just apply, there is no variation.

			console.log('applyFilter');
			im.image.applyFilter(im.filter.sepia,{},function(){
				im.hideLoader();
				im.fire('filterApplied', me);
				im.fire('SepiaFilterDidFinish');
				im.image.parent.draw();
			});
			// Apply Filter
		},10); // Allow loader to show
	}
});