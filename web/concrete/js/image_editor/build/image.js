var img = new Image();
img.src = settings.src;
img.onload = function () {
  var center = {
    x: im.center.x - (img.width / 2),
    y: im.center.y - (img.height / 2)
  };
  im.prettifier = new Kinetic.Layer();
  im.image = new Kinetic.Image({
    image: img,
    x: Math.round(center.x),
    y: Math.round(center.y),
    stroke: '#000'
  });
  im.image.on('draw',function(){im.fire('imagedraw');});
  im.editor.add(im.image);
  im.stage.add(im.editor);
  im.imageData = im.image.getImageData();
  im.fireEvent('imageload');
};