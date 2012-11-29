var imageStage = new Kinetic.Rect({
  x:im.center.x,
  y:im.center.y,
  width:0,
  height:0,
  fill:'#ccc',
  stroke:'#777',
  strokeWidth:1
});
im.imageStage = imageStage;

im.imageStage._setheight = im.imageStage.setHeight;
im.imageStage._setwidth = im.imageStage.setWidth;
im.imageStage._setx = im.imageStage.setX;
im.imageStage._sety = im.imageStage.setY;
im.imageStage.setWidth = function(width) {
  this._setwidth(width);
  this._setx(im.center.x - Math.floor(width/2));

  im.trigger('imageStageUpdate',this);
  this.parent.draw();
}
im.imageStage.setHeight = function(height) {
  this._setheight(height);
  this._sety(im.center.y - Math.floor(height/2));

  im.trigger('imageStageUpdate',this);
  this.parent.draw();
}
im.imageStage.setX = function(x) {
  this._setx(x);
  this._setwidth(Math.abs(x - im.center.x) * 2);
  im.trigger('imageStageUpdate',this);
  this.parent.draw();
}
im.imageStage.setY = function(y) {
  this._sety(y);
  this._setheight(Math.abs(y - im.center.y) * 2);
  im.trigger('imageStageUpdate',this);
  this.parent.draw();
}

im.imageStageLayer = new Kinetic.Layer();
im.imageStageLayer.add(imageStage);
im.imageStage.setWidth(settings.imageStageWidth || Math.floor(im.stage.getWidth() / 2));
im.imageStage.setHeight(settings.imageStageHeight || Math.floor(im.stage.getHeight() / 2));

im.stage.add(im.imageStageLayer);