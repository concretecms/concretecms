var me = $(this);
im.bind('filterLoad',function(e) {
	var newFilter = e.eventData;
	me.append($('<span/>').text(newFilter.namespace));
});