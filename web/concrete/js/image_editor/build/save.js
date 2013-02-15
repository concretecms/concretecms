im.save = function() {
  var oldScale = im.stage.getScale();
  im.stage.setScale(1);
  var newx = im.image.getX() - im.dragger.getX(),
      newy = im.image.getY() - im.dragger.getY()
  im.image.setX(newx);
  im.image.setY(newy);
  im.image.disableStroke();

  im.image.toImage({
    width:im.width,
    height:im.height,
    callback:function(img) {
      im.image.enableStroke();
      im.image.setImage(img);
      im.image.setX(im.dragger.getX());
      im.image.setY(im.dragger.getY());
      im.stage.setScale(oldScale);
      im.stage.draw();
    }
  });
};
