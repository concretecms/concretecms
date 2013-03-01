if (settings.src) {
  im.showLoader('Loading Image..');
  var img = new Image();
  img.src = settings.src;
  img.onload = function () {
    if (!im.strictSize) {
      im.saveWidth = img.width;
      im.saveHeight = img.height;
      im.fire('saveSizeChange');
      im.buildBackground();
    }
    var center = {
      x: im.center.x - (img.width / 2),
      y: im.center.y - (img.height / 2)
    };
    var image = new Kinetic.Image({
      image: img,
      x: Math.round(center.x),
      y: Math.round(center.y)
    });
    im.fire('imageload');
    im.addElement(image,'image');
  };
} else {
  im.fire('imageload');
}