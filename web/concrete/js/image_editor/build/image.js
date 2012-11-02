var img = new Image();
img.src = settings.src;
img.onload = function () {
  var center = {
    x: im.center.x - (img.width / 2),
    y: im.center.y - (img.width / 2)
  };
  im.prettifier = new Kinetic.Layer();
  im.Image = new Kinetic.Image({image: img, x: center.x, y: center.y});
  im.Image.on('draw',function(){im.fire('imagedraw');});
  im.editor.add(im.Image);
  im.stage.add(im.editor);
  im.fireEvent('imageload');
};