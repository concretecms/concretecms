var me  = $(this), selected = false, locked = true, ratio = 1;
im.disable();
im.selected = false;
im.cache = {};
im.sizeBoxes = {
  width:$('div.widthinput > div.input > input'),
  height:$('div.heightinput > div.input > input'),
};

im.sizeBoxes.width.keyup(function(){
  var wid = parseInt($(this).val()) || 0,
      hei = getHeight(wid);
  if (!im.locked) {
    hei = im.activeElement.getHeight();
  }
  im.sizeBoxes.height.val(hei);

  changeSize({width:wid,height:hei});
  im.activeElement.parent.draw();
});
im.sizeBoxes.height.keyup(function(){
  var hei = parseInt($(this).val()) || 0,
      wid = getWidth(hei);
  if (!im.locked) {
    wid = im.activeElement.getWidth();
  }
  im.sizeBoxes.width.val(wid);

  changeSize({width:wid,height:hei});
  im.activeElement.parent.draw();
});


im.myControls = new Kinetic.Layer();
var autocrop = $('input.autocrop',me);
var locked = $('div.locked',me).addClass('active');
$('div.cancelbutton',me).click(function(){
  im.activeElement.attrs.x = im.cache.startPos.x;
  im.activeElement.attrs.y = im.cache.startPos.y;
  im.activeElement.setWidth(im.cache.startSize.width);
  im.activeElement.setHeight(im.cache.startSize.height);
  im.saveWidth = im.cache.saveSize.width;
  im.saveHeight = im.cache.saveSize.height;
  im.buildBackground();
  im.activeElement.parent.draw();
  setupControls();
});
im.locked = true;
im.ratio = [1, 1];

locked.click(function(){
  if ($(this).hasClass('active')) {
    im.locked = false;
    $(this).removeClass('active');
  } else {
    im.locked = true;
    im.ratio = [im.activeElement.getWidth(),im.activeElement.getHeight()];
    $(this).addClass('active');
  }
});

autocrop.change(function(){
  console.log('change');
  im.alterCore('autoCrop',this.checked);
}).get(0).checked = im.autoCrop;

im.on('changeActiveAction',function(e){
  if (e.eventData == im.namespace) {
    im.selected = true;
    setupControls();
    im.stage.add(im.myControls);
  } else if (im.selected) {
    im.selected = false;
    im.myControls.remove();
  }
});

im.on('changeActiveElement',function(e){
  if (im.activeElement.elementType == 'stage' || im.activeElement.elementType == 'text' || im.activeElement.getRotation() !== 0) {
    return im.disable();
  }
  im.enable();
  if (im.selected) {
    setupControls();
  }
});

var getWidth = function(hei) {
  return round(hei * im.ratio[0] / im.ratio[1]) || 0;
}, getHeight = function(wid) {
  return round(wid * im.ratio[1] / im.ratio[0]) || 0;
};

var changeSize = function(size) {
  size.width = Math.max(size.width, 0);
  size.height = Math.max(size.height, 0);

  im.activeElement.setWidth(size.width);
  im.activeElement.setHeight(size.height);

  im.activeElement.setOffset([im.activeElement.getWidth()/2,im.activeElement.getHeight()/2]);
  im.activeElement.parent.setOffset([-im.activeElement.getWidth()/2,-im.activeElement.getHeight()/2]);

  im.activeElement.parent.draw();
};

var setupControls = function() {
  var elem = im.activeElement,
      x = elem.attrs.x,
      y = elem.attrs.y,
      width = elem.getWidth(),
      height = elem.getHeight();

  im.sizeBoxes.width.val(width);
  im.sizeBoxes.height.val(height);

  im.ratio = [width,height];

  positionControls();

  im.cache.saveSize   = {width:im.saveWidth, height:im.saveHeight};
  im.cache.startPos   = {x:x, y:y};
  im.cache.startSize  = {width:width, height:height};
}, positionControls = function() {
  var elem = im.activeElement,
      x = elem.attrs.x,
      y = elem.attrs.y,
      width = elem.getWidth(),
      height = elem.getHeight();

  //im.myControls.setRotation(elem.getRotation());
  im.myControls.setPosition({x: x,     y: y});
  im.br_control.setPosition({x: width, y: height});
  im.br_control.setSize({width:7 / im.scale, height: 7 / im.scale});
  im.br_control.setOffset([(4 / im.scale) + .5, (4 / im.scale) + .5]);
  im.br_control.setStrokeWidth(1 / im.scale);
};
im.br_control = new Kinetic.Image({
  x:0,
  y:0,
  width:7,
  height:7,
  fill:'transparent',
  stroke:'black',
  strokeWidth:1,
  image:im.crosshair,
  draggable:true,
  offset:[4,4]
});
im.tr_control = im.br_control.clone();
im.tl_control = im.br_control.clone();
im.bl_control = im.br_control.clone();

im.br_control.on('dragmove',function(){
  var width = Math.max(1,this.attrs.x);
  var height = getHeight(width);
  if (!im.locked) {
    height = Math.max(1, this.attrs.y);
  }
  if (height < this.attrs.y) {
    height = Math.max(1, this.attrs.y);
    if (im.locked) {
      width = getWidth(height);
    }
  }
  changeSize({width:width, height:height});
  im.sizeBoxes.width.val(width);
  im.sizeBoxes.height.val(height);
});
im.myControls.add(im.br_control);
im.myControls.autoCrop = false;


im.bind('adjustedsavers',positionControls);