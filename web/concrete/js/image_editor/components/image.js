
var me = $(this);


ConcreteEvent.bind('FileManagerFileSelected',function(e){
	if (e.eventData.af == "addImageToImageEditor_"+im.uniqid) {
		im.showLoader();
		ccm_clearFile({stopPropagation:function(){}},e.eventData.af);
		ccm_alGetFileData(e.eventData.fID,function(data){
			var img = new Image;
			img.onload = function(){
        var size = im.fit({width:img.width,height:img.height});
        var height = size.height,
            width  = size.width;

				var image = new Kinetic.Image({
					width:width,
					height:height,
					x:0,
					y:0,
					image:img
				});
				im.addElement(image,'image');
				im.hideLoader();
			};
			img.src = data[0].filePathDirect;
		});
	}
});
me.find('a.webcam').click(function(e) {
  e.preventDefault();
  var photoDialog  = $($.parseHTML($('script.template',me).text()));
  console.log(photoDialog);
  $.fn.dialog.open({width:window.innerWidth - 300, height:window.innerHeight - 300, element:photoDialog});
  navigator.getMedia = (navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia ||
                         navigator.msGetUserMedia);

  var dialog = $('div.capture_image').css('text-align','center'),
      video  = $('video',dialog).get(0),
      canvas = $('canvas',dialog).hide().get(0),
      button = $('button',dialog).attr('disabled','true'),
      width, height, streaming = false, videoStream;
  im.video = video;
  navigator.getMedia(
    {
      video: true,
      audio: false
    },
    function(stream) {
      console.log("Stream Started");
      videoStream = stream;
      if (navigator.mozGetUserMedia) {
        video.mozSrcObject = stream;
      } else {
        var vendorURL = window.URL || window.webkitURL;
        video.src = vendorURL.createObjectURL(stream);
      }
      video.play();
    },
    function(err) {
      console.log("An error occured! " + err);
    }
  );
 
  video.addEventListener('canplay', function(ev){
    if (!streaming) {
      width = dialog.width();
      height = video.videoHeight / (video.videoWidth/width);
      if (height > dialog.parent().height() - 50) {
        height = dialog.parent().height() - 50;
        width = video.videoWidth * (height / video.videoHeight);
      }
      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      streaming = true;
      button.removeAttr('disabled');
    }
  }, false);

  button.click(function(){
    im.showLoader('Capturing..');
    video.pause();
    canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(video, 0, 0, width, height);
    var data = canvas.toDataURL('image/png');

    im.showLoader('Saving..');
    var img = new Image;
    img.onload = function(){
      var size = im.fit({width:img.width,height:img.height});
      var height = size.height,
          width  = size.width;
      var image = new Kinetic.Image({
        width:width,
        height:height,
        x:0,
        y:0,
        image:img
      });
      im.addElement(image,'image');
      im.hideLoader();
      videoStream.stop()
      $.fn.dialog.closeTop();
    };
    img.src = data;
  });
});