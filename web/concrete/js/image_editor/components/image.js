ccm_event.bind('FileManagerFileSelected',function(e){
	if (e.eventData.af == "addImageToImageEditor_"+im.uniqid) {
		$.fn.dialog.showLoader();
		ccm_clearFile({stopPropagation:function(){}},e.eventData.af);
		ccm_alGetFileData(e.eventData.fID,function(data){
			var img = new Image;
			img.onload = function(){
				var image = new Kinetic.Image({
					width:img.width,
					height:img.height,
					x:0,
					y:0,
					image:img
				});
				im.addElement(image,'image');
				$.fn.dialog.hideLoader();
			};
			img.src = data[0].filePathDirect;
		});
	}
});