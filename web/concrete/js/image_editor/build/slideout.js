im.slideOut = $("<div/>").addClass('slideOut').css({
	width:0,
	float:'right',
	height:'100%',
	'overflow-x':'hidden',
	right:im.controlContext.width()-1,
	position:'absolute',
	background:'white',
	'box-shadow':'black -20px 0 20px -25px'
});

im.slideOutContents = $('<div/>').appendTo(im.slideOut).width(300);
im.showSlideOut = function(contents,callback){
	im.slideOut.css('border-right','solid 1px #eee');
	if (im.slideOut.hasClass('active')) {
		im.slideOut.addClass('sliding');
		im.slideOut.slideIn(300,function(){
			im.slideOutContents.replaceWith(contents.width(300));
			im.slideOutContents = contents;
			im.slideOut.slideOut(300,function(){
				im.slideOut.removeClass('sliding');
				((typeof callback === 'function') && callback());
			});
		});
	} else {
		im.slideOutContents.replaceWith(contents.width(300));
		im.slideOutContents = contents;
		im.slideOut.addClass('active').addClass('sliding');
		im.slideOut.slideOut(300,function(){
			im.slideOut.removeClass('sliding');
			((typeof callback === 'function') && callback());
		});
	}
};
im.hideSlideOut = function(callback) {
	im.slideOut.addClass('sliding');
	im.slideOut.slideIn(300,function(){
		im.slideOut.css('border-right','0');
		im.slideOut.removeClass('active').removeClass('sliding');
		((typeof callback === 'function') && callback());
	});
};
im.controlContext.after(im.slideOut);