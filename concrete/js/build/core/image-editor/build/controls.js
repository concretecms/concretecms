// Zoom
var controlBar = getElem(im.stage.getContainer()).parent().children('.bottomBar');

controlBar.attr('unselectable', 'on');

var zoom = {};

zoom.zoomIn = getElem("<div class='bottombarbutton plus'><i class='fa fa-plus'></i></div>");
zoom.zoomOut = getElem("<div class='bottombarbutton'><i class='fa fa-minus'></i></div>");

zoom.zoomIn.appendTo(controlBar);
zoom.zoomOut.appendTo(controlBar);

zoom.zoomIn.click(function(e){im.fire('zoomInClick',e)});
zoom.zoomOut.click(function(e){im.fire('zoomOutClick',e)});

var scale = getElem('<div></div>').addClass('scale').text('100%');
im.on('scaleChange',function(e){
  scale.text(Math.round(im.scale * 10000)/100 + "%");
});
scale.click(function(){
  im.scale = 1;
  im.stage.setScale(im.scale);
  var pos = (im.stage.getDragBoundFunc())({x:im.stage.getX(),y:im.stage.getY()});
  im.stage.setX(pos.x);
  im.stage.setY(pos.y);
  im.fire('scaleChange');
  im.buildBackground();
  im.stage.draw();
});
scale.appendTo(controlBar);

var minScale = 0, maxScale = 3000, stepScale = 5/6;

im.on('zoomInClick',function(e){
  var centerx = (-im.stage.getX() + (im.stage.getWidth() / 2)) / im.scale,
      centery = (-im.stage.getY() + (im.stage.getHeight() / 2)) / im.scale;

  im.scale /= stepScale;
  im.scale = Math.round(im.scale * 1000) / 1000;
  im.alterCore('scale',im.scale);

  var ncenterx = (-im.stage.getX() + (im.stage.getWidth() / 2)) / im.scale,
      ncentery = (-im.stage.getY() + (im.stage.getHeight() / 2)) / im.scale;

  im.stage.setX(im.stage.getX() - (centerx - ncenterx) * im.scale);
  im.stage.setY(im.stage.getY() - (centery - ncentery) * im.scale);

  im.stage.setScale(im.scale);

  var pos = (im.stage.getDragBoundFunc())({x:im.stage.getX(),y:im.stage.getY()});
  im.stage.setX(pos.x);
  im.stage.setY(pos.y);

  im.fire('scaleChange');
  im.buildBackground();
  im.stage.draw();
});
im.on('zoomOutClick',function(e){
  var centerx = (-im.stage.getX() + (im.stage.getWidth() / 2)) / im.scale,
      centery = (-im.stage.getY() + (im.stage.getHeight() / 2)) / im.scale;

  im.scale *= stepScale;
  im.scale = Math.round(im.scale * 1000) / 1000;
  im.alterCore('scale',im.scale);

  var ncenterx = (-im.stage.getX() + (im.stage.getWidth() / 2)) / im.scale,
      ncentery = (-im.stage.getY() + (im.stage.getHeight() / 2)) / im.scale;

  im.stage.setX(im.stage.getX() - (centerx - ncenterx) * im.scale);
  im.stage.setY(im.stage.getY() - (centery - ncentery) * im.scale);

  im.stage.setScale(im.scale);

  var pos = (im.stage.getDragBoundFunc())({x:im.stage.getX(),y:im.stage.getY()});
  im.stage.setX(pos.x);
  im.stage.setY(pos.y);

  im.fire('scaleChange');
  im.buildBackground();
  im.stage.draw();
});

// Save
var saveSize = {};

saveSize.width = getElem('<span/>').addClass('saveWidth');
saveSize.height = getElem('<span/>').addClass('saveHeight');
saveSize.crop = getElem('<div><i class="icon-resize-full"/></div>').addClass('bottombarbutton').addClass('crop');
saveSize.both = saveSize.height.add(saveSize.width).width(32).attr('contenteditable',!!1);

saveSize.area = getElem('<span/>').css({float:'right'});
/*saveSize.crop.appendTo(saveSize.area);
saveSize.width.appendTo($('<div>w </div>').addClass('saveWidth').appendTo(saveSize.area));
saveSize.height.appendTo($('<div>h </div>').addClass('saveHeight').appendTo(saveSize.area));
saveSize.area.appendTo(controlBar);
*/

im.on('adjustedsavers',function(){
  saveSize.width.text(im.saveWidth);
  saveSize.height.text(im.saveHeight);
});

saveSize.crop.click(function(){
  im.adjustSavers();
});

if (im.strictSize) {
  saveSize.both.attr('disabled','true');
} else {
  saveSize.both.keyup(function(e){
    im.fire('editedSize',e);
  });
}

im.bind('editedSize',function(e){
  im.saveWidth = parseInt(saveSize.width.text());
  im.saveHeight = parseInt(saveSize.height.text());

  if (isNaN(im.saveWidth)) im.saveWidth = 0;
  if (isNaN(im.saveHeight)) im.saveHeight = 0;

  //im.trigger('saveSizeChange');
  im.buildBackground();
});

im.bind('saveSizeChange',function(){
  saveSize.width.text(im.saveWidth);
  saveSize.height.text(im.saveHeight);
});

im.setCursor = function(cursor) {
  $(im.stage.getContainer()).css('cursor',cursor);
};
