im.controls = new Kinetic.Layer();
im.stage.add(im.controls);
im.controls.hide()
im.controls.moveToBottom();
im.controls.draw();
im.bind('changecontrolset',function(e){
  if (e.eventData != im.namespace) {
    im.controls.hide()
    im.controls.moveToBottom();
    im.controls.draw();
  } else {
    im.controls.show()
    im.controls.moveToTop();
    im.controls.draw();
  }
});

var Control = function(name,cursor,align,move) {
  var c = this;
  c.name = name;
  c.cursor = cursor;
  c.control = new Kinetic.Rect({
    x:-10,
    y:-10,
    width:10,
    height:10,
    stroke:'#333',
    fill:'white',
    draggable:'true'
  });
  c.dragStart={x:0,y:0};
  c.startSize={x:im.image.getWidth(),y:im.image.getHeight()};
  im.bind('imagemove',function(){align(c);im.controls.draw()});
  im.bind('imagechange',function(){align(c);im.controls.draw()});
  align(c);
  c.control.on('dragstart',function(){
    c.dragStart.x = c.control.getX();
    c.dragStart.y = c.control.getY();
    c.startSize.x = im.image.getWidth();
    c.startSize.y = im.image.getHeight();
    c.control.parent.getCanvas().element.style.cursor = c.cursor;
  });
  c.control.on('mouseover',function(){
    c.control.parent.getCanvas().element.style.cursor = c.cursor;
  });
  c.control.on('mouseout',function(){
    c.control.parent.getCanvas().element.style.cursor = 'auto';
  });
  c.control.on('dragend',function(){
    c.control.parent.getCanvas().element.style.cursor = 'auto';
  });
  c.control.on('dragmove',function(){move(c);im.controls.draw()});
  im.controls.add(c.control);
};

var controls = [
  new Control('topleft','nw-resize',function(c){
    c.control.setX(Math.max(im.image.getX()-5,0));
    c.control.setY(Math.max(im.image.getY()-5,0));
  },function(c){
    var newwidth = c.startSize.x + c.dragStart.x - c.control.getX();
    var newheight = c.startSize.y + c.dragStart.y - c.control.getY();
    im.image.setWidth(Math.max(newwidth,0));
    im.image.setHeight(Math.max(newheight,0));
    if (im.image.getWidth() != 0) im.image.setX(Math.max(c.control.getX()+5,0));
    if (im.image.getHeight() != 0) im.image.setY(Math.max(c.control.getY()+5,0));
    im.trigger('imagechange');
  }),
  new Control('bottomleft','sw-resize',function(c){
    c.control.setX(Math.max(im.image.getX()-5,0));
    c.control.setY(Math.max(im.image.getY()+im.image.getHeight()-5,0));
  },function(c){
    var newwidth = c.startSize.x + c.dragStart.x - c.control.getX();
    var newheight = c.startSize.y - (c.dragStart.y - c.control.getY());
    im.image.setWidth(Math.max(newwidth,0));
    im.image.setHeight(Math.max(newheight,0));
    if (im.image.getWidth() != 0) im.image.setX(Math.max(c.control.getX()+5,0));
    im.trigger('imagechange');
  }),
  new Control('topright','ne-resize',function(c){
    c.control.setX(Math.max(im.image.getX()+im.image.getWidth()-5,0));
    c.control.setY(Math.max(im.image.getY()-5,0));
  },function(c){
    var newwidth = c.startSize.x - (c.dragStart.x - c.control.getX());
    var newheight = c.startSize.y + c.dragStart.y - c.control.getY();
    im.image.setWidth(Math.max(newwidth,0));
    im.image.setHeight(Math.max(newheight,0));
    if (im.image.getHeight() != 0) im.image.setY(Math.max(c.control.getY()+5,0));
    im.trigger('imagechange');
  }),
  new Control('bottomright','se-resize',function(c){
    c.control.setX(Math.max(Math.abs(im.image.getX()+im.image.getWidth()-5),0));
    c.control.setY(Math.max(im.image.getY()+im.image.getHeight()-5,0));
  },function(c){
    var newwidth = c.startSize.x - (c.dragStart.x - c.control.getX());
    var newheight = c.startSize.y - (c.dragStart.y - c.control.getY());
    im.image.setWidth(Math.max(newwidth,0));
    im.image.setHeight(Math.max(newheight,0));
    im.trigger('imagechange');
  }),
  new Control('middleleft','w-resize',function(c){
    c.control.setX(Math.max(im.image.getX()-5,0));
    c.control.setY(Math.max(im.image.getY()+im.image.getHeight()/2-5,0));
  },function(c){
    var newwidth = c.startSize.x + c.dragStart.x - c.control.getX();
    im.image.setWidth(Math.max(newwidth,0));
    if (im.image.getWidth() != 0) im.image.setX(Math.max(c.control.getX()+5,0));
    im.trigger('imagechange');
  }),
  new Control('middleright','e-resize',function(c){
    c.control.setX(Math.max(im.image.getX()+im.image.getWidth()-5,0));
    c.control.setY(Math.max(im.image.getY()+im.image.getHeight()/2-5,0));
  },function(c){
    var newwidth = c.startSize.x - (c.dragStart.x - c.control.getX());
    im.image.setWidth(Math.max(newwidth,0));
    im.trigger('imagechange');
  }),
  new Control('middletop','n-resize',function(c){
    c.control.setX(Math.max(im.image.getX()+im.image.getWidth()/2-5,0));
    c.control.setY(Math.max(im.image.getY()-5,0));
  },function(c){
    var newheight = c.startSize.y + c.dragStart.y - c.control.getY();
    im.image.setHeight(Math.max(newheight,0));
    if (im.image.getHeight() != 0) im.image.setY(Math.max(c.control.getY()+5,0));
    im.trigger('imagechange');
  }),
  new Control('middlebottom','s-resize',function(c){
    c.control.setX(Math.max(im.image.getX()+im.image.getWidth()/2-5,0));
    c.control.setY(Math.max(im.image.getY()+im.image.getHeight()-5,0));
  },function(c){
    var newheight = c.startSize.y - (c.dragStart.y - c.control.getY());
    im.image.setHeight(Math.max(newheight,0));
    im.trigger('imagechange');
  })
];