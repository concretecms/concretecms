/////////////////////////////
//      Kinetic.Node       //
/////////////////////////////
Kinetic.Node.prototype.closest = function(type) {
  var active = this.parent;
  while (active !== undefined) {
    if (active.nodeType === type) return active;
    active = active.parent;
  }
  return false;
};


/////////////////////////////
//      Kinetic.Stage      //
/////////////////////////////
Kinetic.Stage.prototype.createCopy = function () {
  var copy = [], children = this.getChildren(), i;
  for (i = 0; i < children.length; i++) {
    copy.push(children[i].clone());
  }
  return copy;
};
Kinetic.Stage.prototype.getScaledWidth = function() {
  return Math.ceil(this.getWidth() / this.getScale().x);
};
Kinetic.Stage.prototype.getScaledHeight = function() {
  return Math.ceil(this.getHeight() / this.getScale().y);
};
Kinetic.Stage.prototype.getSaveWidth = function() {
  return this.im.saveWidth;
};
Kinetic.Stage.prototype.getSaveHeight = function() {
  return this.im.saveHeight;
};
Kinetic.Stage.prototype.getTotalDimensions = function() {
  var minY = (this.getSaveHeight() / 2 - this.im.center.y) * this.getScale().y;
  var maxY = minY + this.getHeight() - (this.getSaveHeight() * this.getScale().y);

  var minX = (this.getSaveWidth() / 2 - this.im.center.x) * this.getScale().x;
  var maxX = minX + this.getWidth() - (this.getSaveWidth() * this.getScale().x);

  return {
    min: {
      x: minX,
      y: minY
    },
    max: {
      x: maxX,
      y: maxY
    },
    width:this.getScaledWidth(),
    height:this.getScaledHeight(),
    visibleWidth:Math.max(this.getSaveWidth(),this.getScaledWidth() * 2 - this.getSaveWidth()),
    visibleHeight:Math.max(this.getSaveHeight(),this.getScaledHeight() * 2 - this.getSaveHeight())
  };
};
Kinetic.Stage.prototype.loadCopy = function (copy) {
  var i;
  this.removeChildren();
  for (i = 0; i < copy.length; i++) {
    this.add(copy[i]);
  }
  this.draw();
};
Kinetic.Stage.prototype.elementType = 'stage';

/////////////////////////////
//      Kinetic.Image      //
/////////////////////////////
Kinetic.Image.prototype.getImageData = function() {
  var canvas = new Kinetic.Canvas(this.attrs.image.width, this.attrs.image.height);
  var context = canvas.getContext();
  context.drawImage(this.attrs.image, 0, 0);
  try {
      var imageData = context.getImageData(0, 0, canvas.getWidth(), canvas.getHeight());
      return imageData;
  } catch(e) {
      Kinetic.Util.warn('Unable to get imageData.');
  }
};

/////////////////////////////
//      Kinetic.Layer      //
/////////////////////////////
Kinetic.Layer.prototype._cacheddraw = (new Kinetic.Layer).draw;
Kinetic.Layer.prototype.draw = function() {
  if (typeof im === 'undefined' || typeof im.trigger === 'undefined') {
    return this._cacheddraw();
  }
  //im.trigger('beforeredraw',this);
  var draw = this._cacheddraw();
  //im.trigger('afterredraw',this);
  return draw;
};
Kinetic.Layer.prototype.elementType = 'layer';


/////////////////////////////
//      Kinetic.Group      //
/////////////////////////////
Kinetic.Group.prototype.elementType = 'group';

/////////////////////////////
//       Kinetic.Text      //
/////////////////////////////
Kinetic.Text.prototype.rasterize = function(e) {
  var layer = this.parent;
  var me = this;
  this.toImage({
    callback:function(img){
      var rasterizedImage = new Kinetic.Image({image:img,x:me.getPosition().x,y:me.getPosition().y});
      me.remove();
      layer.add(rasterizedImage).draw();
      e.callback(rasterizedImage);
    }
  });
};
