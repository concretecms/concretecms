var me = $(this);
me.parent().parent().hide();

im.bind('ChangeActiveAction',function(e){
  if (e.eventData != im.namespace) {
  	// Not Me
  } else {
  	// Me
  }
});
im.bind('ChangeActiveElement',function(e){
	if (im.activeElement.elementType != 'image') {
		me.parent().parent().slideUp();
		return;
	}
	me.parent().parent().slideDown();
});


im.bind('filterLoad',function(e) {
	var newFilter = e.eventData;
	console.log(e,newFilter);
	newFilter.label = $('<h5/>').text(newFilter.name);
	newFilter.controls = $('<div/>').hide();
	newFilter.parent = me;

	// Bindings.
	newFilter.label.click(function(){
		if (newFilter.controls.hasClass('active')) {
			newFilter.controls.slideUp().removeClass('active');
		} else {
			newFilter.controls.slideDown().addClass('active');
		}
		im.fire('filterChange',newFilter);
	});

	me.append(newFilter.label);
	me.append(newFilter.controls);
	im.fire('filterFullyLoaded',newFilter);
});