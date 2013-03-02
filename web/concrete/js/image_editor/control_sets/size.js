var me  = $(this), selected = false, locked = true, ratio = 1;
im.disable();
im.myControls = new Kinetic.Layer();
var locker = me.find('input');
locker.click(function(){
  locked=locker[0].checked;
});

im.on('changeActiveAction',function(e){
  if (e.eventData === im.namespace) {
    selected = true;
    locked = true;
    locker[0].checked = locked;
    im.stage.add(im.myControls);
    im.adjust();
  } else {
    if (selected) im.myControls.remove();
    selected = false;
  }
});

im.on('changeActiveElement',function(e){
  locked = true;
  if (im.activeElement.elementType === 'stage' || im.activeElement.elementType === 'text') {
    im.disable();
  } else {
    im.enable();
  }
  if (!selected) return;
  ratio = im.activeElement.getHeight() / im.activeElement.getWidth();
  im.adjust();
});

var control = new Kinetic.Image({
  x:0,
  y:0,
  width:11,
  height:11,
  image:im.crosshair,
  draggable:true
});
im.myControls.add(control);
im.adjust = function() {
  control.setScale(1/im.scale);
  control.setX(im.activeElement.getX() + im.activeElement.getWidth() - (5 * control.getScale().x));
  control.setY(im.activeElement.getY() + im.activeElement.getHeight() - (5 * control.getScale().y));
  im.myControls.draw();
};

var startpos = {x:0,y:0,width:0,height:0};
control.on('dragstart',function(){
  ratio = im.activeElement.getHeight() / im.activeElement.getWidth();
  startpos.x = control.getX();
  startpos.y = control.getY();
  startpos.width = im.activeElement.getWidth();
  startpos.height = im.activeElement.getHeight();
});

control.on('dragmove',function(){
  var offsetX = (startpos.x - control.getX()),
      offsetY = (startpos.y - control.getY()),
      newWidth = startpos.width - offsetX,
      newHeight = startpos.height - offsetY
  if (locked) {
    if (offsetX < offsetY) {
      newHeight = Math.abs(newWidth * ratio);
    } else {
      newWidth = Math.abs(newHeight / ratio);
    }
  }
  im.activeElement.setWidth(newWidth);
  im.activeElement.setHeight(newHeight);
  im.stage.draw();
});

control.on('mouseover',function(){
  im.setCursor('pointer');
});

control.on('dragend',im.adjust);
control.on('click',im.adjust);