im.selected = false;
var me = $(this), elem = this;

im.bind('changeActiveComponent',function(e){
	if (e.eventData == im.namespace) {
		im.fire("selected",e.eventData,elem);
		im.selected = true;
	} else {
		im.hideSlideOut();
		im.selected = false;
	}
});

im.bind("selected",function(e){
	im.showSlideOut(me.clone());
},elem);