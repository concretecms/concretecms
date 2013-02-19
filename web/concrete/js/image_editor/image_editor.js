///////////////////////////////////////////////////////////////////////////////
//                            kinetic.prototype.js                           //
///////////////////////////////////////////////////////////////////////////////
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
Kinetic.Stage.prototype.loadCopy = function (copy) {
  var i;
  this.removeChildren();
  for (i = 0; i < copy.length; i++) {
    this.add(copy[i]);
  }
  this.draw();
};
Kinetic.Image.prototype.getImageData = function() {
  var canvas = new Kinetic.Canvas(this.attrs.image.width, this.attrs.image.height);
  var context = canvas.getContext();
  context.drawImage(this.attrs.image, 0, 0);
  try {
      var imageData = context.getImageData(0, 0, canvas.getWidth(), canvas.getHeight());
      return imageData;
  } catch(e) {
      Kinetic.Global.warn('Unable to get imageData.');
  }
};

Kinetic.Layer.prototype._cacheddraw = (new Kinetic.Layer).draw;
Kinetic.Layer.prototype.draw = function() {
  if (typeof im === 'undefined' || typeof im.trigger === 'undefined') {
    return this._cacheddraw();
  }
  im.trigger('beforeredraw',this);
  var draw = this._cacheddraw();
  im.trigger('afterredraw',this);
  return draw;
};

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


///////////////////////////////////////////////////////////////////////////////
//                               imageeditor.js                              //
///////////////////////////////////////////////////////////////////////////////

var ImageEditor = function (settings) {
  "use strict";
  if (settings === undefined) return this;
  var im           = this, x, round = function(float){return Math.round(float)};
  im.width         = settings.width;
  im.height        = settings.height;
  im.saveWidth     = settings.saveWidth || round(im.width / 2);
  im.saveHeight    = settings.saveHeight || round(im.height / 2);
  im.strictSize    = (settings.saveWidth !== undefined ? true : false);
  im.stage         = new Kinetic.Stage(settings);
  im.namespaces    = {};
  im.controlSets   = {};
  im.components    = {};
  im.filters       = {};
  im.scale         = 1;
  im.crosshair     = new Image();
  im.uniqid        = im.stage.getContainer().id;
  im.editorContext = $(im.stage.getContainer()).parent();
  im.domContext    = im.editorContext.parent();

  im.crosshair.src = '/concrete/images/image_editor/crosshair.png';

  im.center = {
    x: Math.round(im.width / 2),
    y: Math.round(im.height / 2)
  };

  im.centerOffset = {
    x: im.center.x,
    y: im.center.y
  };

  var getElem = function(selector) {
    return $(selector, im.domContext);
  },
  log = function() {
    if (settings.debug === true && console !== undefined) {
      var args = arguments;
      if (args.length == 1) args = args[0];
      console.log(args);
    }
  },
  error = function() {
    if (console !== undefined) {
      var args = arguments;
      if (args.length == 1) args = args[0];
      console.error(args);
    }
  };


///////////////////////////////////////////////////////////////////////////////
//                                 history.js                                //
///////////////////////////////////////////////////////////////////////////////
var History = function () {
  var h = this;
  h.history = [];
  h.pointer = -1;
  h.save = function () {
    im.fire('beforehistorysave');
    h.history = h.history.slice(0, h.pointer + 1);
    h.history.push(im.stage.createCopy());
    h.movePointer(1);
    im.fire('historysave');
  };
  h.movePointer = function (diff) {
    h.pointer += diff;
    (h.pointer < 0 && (h.pointer = 0));
    (h.pointer >= h.history.length && (h.pointer = h.history.length - 1));
    return h.pointer;
  };
  h.render = function () {
    im.fire('beforehistoryrender');
    im.stage.loadCopy(h.history[h.pointer]);
    im.fire('historyrender');
  };
  h.undo = function () {
    im.fire('beforehistoryundo');
    h.movePointer(-1);
    h.render();
    im.fire('historyundo');
  };
  h.redo = function () {
    im.fire('beforehistoryredo');
    h.movePointer(1);
    h.render();
    im.fire('historyredo');
  };
};
im.history = new History();


///////////////////////////////////////////////////////////////////////////////
//                                 events.js                                 //
///////////////////////////////////////////////////////////////////////////////
// Handle event binding.
im.bindEvent = im.bind = im.on = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  ccm_event.sub(type,handler,element);
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data, elem) {
  var element = im.stage.getContainer() || elem;
  ccm_event.pub(type,data,element);
};


///////////////////////////////////////////////////////////////////////////////
//                                elements.js                                //
///////////////////////////////////////////////////////////////////////////////
im.addElement = function(object,type) {
	var layer = new Kinetic.Layer();
	layer.add(object);
	object.setX(im.center.x - Math.round(object.getWidth() / 2));
	object.setY(im.center.y - Math.round(object.getHeight() / 2));

	object.elementType = type;

	object.on('click',function(){
		log('clicked Element',this);
		im.fire('ClickedElement',this);
	});
	im.stage.add(layer);
	im.fire('newObject',{object:object,type:type});
	im.stage.draw();
};
im.setActiveElement = function(element) {
	im.trigger('beforeChangeActiveElement',im.activeElement);
	im.alterCore('activeElement',element);
	im.trigger('changeActiveElement',element);
	im.stage.draw();
};
im.bind('ClickedElement',function(e) {
  im.setActiveElement(e.eventData);
});


///////////////////////////////////////////////////////////////////////////////
//                                controls.js                                //
///////////////////////////////////////////////////////////////////////////////
// Zoom
var controlBar = getElem(im.stage.getContainer()).parent().children('.bottomBar');

var zoom = {};

zoom.in = getElem("<span><i class='icon-plus'></i></span>");
zoom.out = getElem("<span><i class='icon-minus'></i></span>");

zoom.in.appendTo(controlBar);
zoom.out.appendTo(controlBar);

zoom.in.click(function(e){im.fire('zoomInClick',e)});
zoom.out.click(function(e){im.fire('zoomOutClick',e)});

var scale = getElem('<span></span>').addClass('scale').text('100%');
im.on('stageChanged',function(e){
	scale.text(Math.round(im.scale * 10000)/100 + "%");
});
scale.appendTo(controlBar);

var minScale = 0, maxScale = 3000, stepScale = 1/4;

im.on('zoomInClick',function(e){
	im.scale += (im.scale * stepScale);
	if (im.scale > maxScale) im.scale = maxScale;
	if (im.scale > stepScale && (Math.abs(im.scale - Math.round(im.scale)) % 1) < stepScale / 2) im.scale = Math.round(im.scale);
	im.stage.setScale(im.scale);
	im.stage.setX(Math.round((im.stage.getWidth() - (im.stage.getWidth() * im.scale))/2));
	im.stage.setY(Math.round((im.stage.getHeight() - (im.stage.getHeight() * im.scale))/2));
	im.fire('stageChanged');
	im.stage.draw();
});
im.on('zoomOutClick',function(e){
	im.scale -= (im.scale * stepScale);
	if (im.scale < minScale) im.scale = minScale;
	if (im.scale > stepScale && (Math.abs(im.scale - Math.round(im.scale)) % 1) < stepScale / 2) im.scale = Math.round(im.scale);
	im.stage.setScale(im.scale);
	im.centerOffset.x = Math.round((im.stage.getWidth() - (im.stage.getWidth() * im.scale))/2);
	im.centerOffset.y = Math.round((im.stage.getHeight() - (im.stage.getHeight() * im.scale))/2);
	im.stage.setX(Math.round((im.stage.getWidth() - (im.stage.getWidth() * im.scale))/2));
	im.stage.setY(Math.round((im.stage.getHeight() - (im.stage.getHeight() * im.scale))/2));
	im.fire('stageChanged');
	im.stage.draw();
});

// Save
var saveSize = {};

saveSize.width = getElem('<input/>');
saveSize.height = getElem('<input/>');
saveSize.both = saveSize.height.add(saveSize.width).width(32);

saveSize.area = getElem('<span/>').css({float:'right',margin:'-5px 14px 0 0'});
saveSize.width.appendTo(saveSize.area);
saveSize.area.append(getElem('<span> x </span>'));
saveSize.height.appendTo(saveSize.area);
saveSize.area.appendTo(controlBar);

var saveButton = $('<button/>').addClass('btn').addClass('btn-primary').text('Save');
saveButton.appendTo(saveSize.area);
saveButton.click(function(){im.save()});


if (im.strictSize) {
	saveSize.both.attr('disabled','true');
} else {
	saveSize.both.keydown(function(e){
		log(e.keyCode);
		if (e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39) return true;

		if (e.keyCode == 38) {
			var newval = parseInt($(this).val()) + 1;
			$(this).val(Math.min(5000,newval)).change();
		}
		if (e.keyCode == 40) {
			var newval = parseInt($(this).val()) - 1;
			$(this).val(Math.max(0,newval)).change();
		}
		var key = String.fromCharCode(e.keyCode);
		if (!key.match(/\d/)) {
			return false;
		}
		var amnt = "" + $(this).val() + key;
		if (amnt > 5000) {
			amnt = 5000;
		}
		$(this).val(amnt).change();

		return false;
	}).keyup(function(e){
		if (e.keyCode == 8) im.fire('editedSize');
	}).change(function(){
		im.fire('editedSize');
	});
}


im.bind('editedSize',function(){
	im.saveWidth = parseInt(saveSize.width.val());
	im.saveHeight = parseInt(saveSize.height.val());

	if (isNaN(im.saveWidth)) im.saveWidth = 0;
	if (isNaN(im.saveHeight)) im.saveHeight = 0;

	im.trigger('saveSizeChange');
	im.adjustSavers();
});

im.bind('saveSizeChange',function(){
	saveSize.width.val(im.saveWidth);
	saveSize.height.val(im.saveHeight);
});


///////////////////////////////////////////////////////////////////////////////
//                                  save.js                                  //
///////////////////////////////////////////////////////////////////////////////
im.save = function() {
  im.savers.hide();
  im.background.hide();
  im.stage.setScale(1);

  im.fire('ChangeActiveAction');
  im.fire('changeActiveComponent');

  $(im.stage.getContainer()).hide();

  var startx = Math.round(im.center.x - (im.saveWidth / 2)),
      starty = Math.round(im.center.y - (im.saveHeight / 2)),
      oldx = im.stage.getX(),
      oldy = im.stage.getY(),
      oldwidth = im.stage.getWidth(),
      oldheight = im.stage.getHeight();

  im.stage.setX(-startx);
  im.stage.setY(-starty);
  im.stage.setWidth(im.saveWidth);
  im.stage.setHeight(im.saveHeight);
  im.stage.draw();


  $.fn.dialog.showLoader();
  im.stage.toDataURL({
    width:im.saveWidth,
    height:im.saveHeight,
    callback:function(data){
      var img = $('<img/>').attr('src',data);
      $.fn.dialog.open({element:img});
      $.fn.dialog.hideLoader();
      im.savers.show();
      im.background.show();
      im.stage.setX(oldx);
      im.stage.setY(oldy);
      im.stage.setWidth(oldwidth);
      im.stage.setHeight(oldheight);
      im.stage.setScale(im.scale);
      im.stage.draw();
      $(im.stage.getContainer()).show();

    }
  })
};


///////////////////////////////////////////////////////////////////////////////
//                                 extend.js                                 //
///////////////////////////////////////////////////////////////////////////////
im.extend = function(property,value) {
  this[property] = value;
};

im.alterCore = function(property,value) {
  var nim = im, ns = 'core', i;
  if (im.namespace) {
    var ns = nim.namespace;
    nim = im.realIm;
  }
  im[property] = value;
  for (i in im.controlSets){
    log('updating '+property+' on '+i);
    im.controlSets[i].im.extend(property,value);
  }
  for (i in im.filters){
    im.filters[i].im.extend(property,value);
  }
  for (i in im.components){
    im.components[i].im.extend(property,value);
  }
};

im.clone = function(namespace) {
  var newim = new ImageEditor(),i;
  newim.realIm = im;
  for (i in im) {
    newim[i] = im[i];
  }
  newim.namespace = namespace;
  return newim;
};


im.addControlSet = function(ns,js,elem) {
  if (jQuery && elem instanceof jQuery) elem = elem[0];
  elem.controlSet = function(im,js) {
    this.im = im;
    try {
      eval(js);
    } catch(e) {
      error(e);
      var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/,'$1').split(':');
      var jsstack = js.split("\n");
      var error = "Parse error at line #"+pos[0]+" char #"+pos[1]+" within "+ns;
      error += "\n"+jsstack[parseInt(pos[0])-1];
      error += "\n"+(new Array(parseInt(pos[1])).join(" "))+"^";
      error(error);
    }
    return this;
  };
  var newim = im.clone(ns);
  var nso = elem.controlSet(newim,js);
  im.controlSets[ns] = nso;
  return nso;
};

im.addFilter = function(ns,js) {
  var filter = function(im,js) {
    this.im = im;
    eval(js);
    return this;
  };
  var newim = im.clone(ns);
  var nso = new filter(newim,js);
  im.filters[ns] = nso;
  return nso;
};

im.addComponent = function(ns,js,elem) {
  if (jQuery && elem instanceof jQuery) elem = elem[0];
  elem.component = function(im,js) {
    this.im = im;
    try {
      eval(js);
    } catch(e) {
      error(e);
      var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/,'$1').split(':');
      var jsstack = js.split("\n");
      var error = "Parse error at line #"+pos[0]+" char #"+pos[1]+" within "+ns;
      error += "\n"+jsstack[parseInt(pos[0])-1];
      error += "\n"+(new Array(parseInt(pos[1])).join(" "))+"^";
      error(error);
    }
    return this;
  };
  var newim = im.clone(ns);
  var nso = elem.component(newim,js);
  im.components[ns] = nso;
  return nso;
};


///////////////////////////////////////////////////////////////////////////////
//                               background.js                               //
///////////////////////////////////////////////////////////////////////////////
// Set up background
im.background = new Kinetic.Layer();
im.stage.add(im.background);

im.buildBackground = function() {
  var z = im.background.getZIndex();
  im.background.destroy();
  if (im.scale < .25) return;
  im.background = new Kinetic.Layer();
  im.stage.add(im.background);
  im.background.setZIndex(z);

  var getCoords = function (x, offset) {
    // slope = 1
    return {x: x, y: -x + offset};
  };

  var to = Math.max(im.stage.getWidth() * 2, im.stage.getHeight() * 2, im.stage.getScaledWidth() * 2, im.stage.getScaledHeight() * 2);
  for (x = -to; x <= to; x += 20) {
    im.background.add(new Kinetic.Line({
      points: [getCoords(-to, x), getCoords(to, x)],
      stroke: '#e3e3e3'
    }));
  }
  im.background.draw();
};
im.buildBackground();

im.on('stageChanged',im.buildBackground);


///////////////////////////////////////////////////////////////////////////////
//                               imagestage.js                               //
///////////////////////////////////////////////////////////////////////////////
var me = $(this);


im.stage.getTotalDimensions = function() {
  var minY = Math.round((im.saveHeight / 2 - im.center.y) * im.scale);
  var maxY = minY + im.stage.getHeight() - (im.saveHeight * im.scale);

  var minX = Math.round((im.saveWidth / 2 - im.center.x) * im.scale);
  var maxX = minX + im.stage.getWidth() - (im.saveWidth * im.scale);

  return {
    min: {
      x: minX,
      y: minY
    },
    max: {
      x: maxX,
      y: maxY
    },
    width:maxX-minX,
    height:maxY-minY,
    visibleWidth:maxX-minX + im.stage.getScaledWidth(),
    visibleHeight:maxY-minY + im.stage.getScaledHeight()
  };
};


im.savers = new Kinetic.Layer();

var savercolor = "rgba(0,0,0,.7)",
saverTopLeft = new Kinetic.Rect({
  x:0,
  y:0,
  fill:savercolor,
  width:Math.floor(im.stage.getScaledWidth()/2),
  height:Math.floor(im.stage.getScaledHeight()/2)
}),
saverBottomLeft = new Kinetic.Rect({
  x:0,
  y:Math.floor(im.stage.getScaledHeight()/2),
  fill:savercolor,
  width:Math.floor(im.stage.getScaledWidth()/2),
  height:Math.ceil(im.stage.getScaledHeight()/2)
}),
saverTopRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getScaledWidth()/2),
  y:0,
  fill:savercolor,
  width:Math.ceil(im.stage.getScaledWidth()/2),
  height:Math.floor(im.stage.getScaledHeight()/2)
}),
saverBottomRight = new Kinetic.Rect({
  x:Math.floor(im.stage.getScaledWidth()/2),
  y:Math.floor(im.stage.getScaledHeight()/2),
  fill:savercolor,
  width:Math.ceil(im.stage.getScaledWidth()/2),
  height:Math.ceil(im.stage.getScaledHeight()/2)
});

saverTopLeft.position = 'topleft';
saverTopRight.position = 'topright';
saverBottomLeft.position = 'bottomleft';
saverBottomRight.position = 'bottomright';

im.adjustSavers = function() {
  log("Adjusting");

  var dimensions = im.stage.getTotalDimensions();
  var startx = Math.round(im.center.x - (im.saveWidth / 2)),
      posx = Math.round(startx + im.saveWidth),
      starty = Math.round(im.center.y - (im.saveHeight / 2)),
      posy = Math.round(starty + im.saveHeight),
      width = dimensions.visibleWidth,
      height = dimensions.visibleHeight,
      stagex = -im.stage.getTotalDimensions().max.x - im.stage.getScaledWidth(),
      stagey = -im.stage.getTotalDimensions().max.y - im.stage.getScaledHeight();


  if (stagex > startx) stagex = startx;
  if (stagey > starty) stagey = starty;

  if (posy < starty) {
    var inter = posy;
    posy = starty;
    starty = inter;
  }
  if (posx < startx) {
    var inter = posx;
    posx = startx;
    startx = inter;
  }

  saverTopLeft.setX(stagex);
  saverTopLeft.setY(stagey);
  saverTopLeft.setWidth(startx - stagex);
  saverTopLeft.setHeight(posy - stagey);

  saverTopRight.setX(startx);
  saverTopRight.setY(stagey);
  saverTopRight.setWidth(width - startx);
  saverTopRight.setHeight(starty - stagey);

  saverBottomLeft.setX(stagex);
  saverBottomLeft.setY(posy);
  saverBottomLeft.setWidth(posx - stagex);
  saverBottomLeft.setHeight(height - posy);

  saverBottomRight.setX(posx);
  saverBottomRight.setY(starty);
  saverBottomRight.setWidth(width - posx);
  saverBottomRight.setHeight(height - starty);

  im.fire('saveSizeChange');

  im.savers.draw();
};

im.savers.add(saverTopLeft);
im.savers.add(saverTopRight);
im.savers.add(saverBottomLeft);
im.savers.add(saverBottomRight);

im.stage.add(im.savers);

im.adjustSavers();


im.bind('stageChanged',im.adjustSavers);

im.stage.setDragBoundFunc(function(ret) {

  var dim = im.stage.getTotalDimensions();

  var maxx = Math.max(dim.max.x,dim.min.x),
      minx = Math.min(dim.max.x,dim.min.x),
      maxy = Math.max(dim.max.y,dim.min.y),
      miny = Math.min(dim.max.y,dim.min.y);

  if (ret.x > maxx) ret.x = maxx;
  if (ret.x < minx) ret.x = minx;
  if (ret.y > maxy) ret.y = maxy;
  if (ret.y < miny) ret.y = miny;

  return ret;
});
im.stage.setDraggable(true);


///////////////////////////////////////////////////////////////////////////////
//                                  image.js                                 //
///////////////////////////////////////////////////////////////////////////////
var img = new Image();
img.src = settings.src;
img.onload = function () {
  if (!im.strictSize) {
    im.saveWidth = img.width;
    im.saveHeight = img.height;
    im.adjustSavers();
  }
  var center = {
    x: im.center.x - (img.width / 2),
    y: im.center.y - (img.height / 2)
  };
  im.image = new Kinetic.Image({
    image: img,
    x: Math.round(center.x),
    y: Math.round(center.y)
  });
  im.imageData = im.image.getImageData();
  im.fire('imageload');
  im.addElement(im.image,'image');
  im.setActiveElement(im.image);
};


///////////////////////////////////////////////////////////////////////////////
//                                 actions.js                                //
///////////////////////////////////////////////////////////////////////////////
im.bind('imageload',function(){
  var cs = settings.controlsets || {}, filters = settings.filters || {}, components = settings.components || {}, namespace, firstcs;
  var running = 0;
  im.fire('LoadingControlSets');
  for (namespace in cs) {
    var myns = "ControlSet_" + namespace;
    $.ajax(cs[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        running--;
        var nso = im.addControlSet(this.myns,js,cs[this.namespace]['element']);
        log(nso);
        im.fire('controlSetLoad',nso);
        if (0 == running) {
          im.trigger('ControlSetsLoaded');
        }
      },
      error: function(xhr, errDesc, exception) {
        running--;
        if (0 == running) {
          im.trigger('ControlSetsLoaded');
        }
      }
    });
  }
  im.fire('LoadingComponents');
  for (namespace in components) {
    var myns = "Component_" + namespace;
    $.ajax(components[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        running--;
        var nso = im.addComponent(this.myns,js,components[this.namespace]['element']);
        log(nso);
        im.fire('ComponentLoad',nso);
        if (0 == running) {
          im.trigger('ComponentsLoaded');
        }
      },
      error: function(xhr, errDesc, exception) {
        running--;
        if (0 == running) {
          im.trigger('ComponentsLoaded');
        }
      }
    });
  }
});
im.bind('ControlSetsLoaded',function(){ // do this when the control sets finish loading.
  log('Loaded');
  var filters = settings.filters || {}, components = settings.components || {}, namespace, firstf, firstc;
  im.fire('LoadingFilters');
  for (namespace in filters) {
    var myns = "Filter_" + namespace;
    var name = filters[namespace].name;
    if (!firstf) firstf = myns;
    $.ajax(filters[namespace].src,{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      name:name,
      success:function(js){
        var nso = im.addFilter(this.myns,js);
        nso.name = this.name;
        im.fire('filterLoad',nso);
      }
    });
  }
});
im.bind('ChangeActiveAction',function(e){
  var ns = e.eventData;
  if (ns === im.activeControlSet) return;
  for (var ons in im.controlSets) {
    if (ons !== ns) getElem(im.controlSets[ons]).slideUp();
  }
  im.activeControlSet = ns;
  im.alterCore('activeControlSet',ns);
  if (!ns) return;
  var cs = $(im.controlSets[ns]),
      height = cs.show().height();
  if (cs.length == 0) return;
  cs.hide().height(height).slideDown(function(){$(this).height('')});
});

im.bind('ChangeActiveComponent',function(e){
  var ns = e.eventData;
  if (ns === im.activeComponent) return;
  for (var ons in im.components) {
    if (ons !== ns) getElem(im.components[ons]).slideUp();
  }
  im.activeComponent = ns;
  im.alterCore('activeComponent',ns);
  if (!ns) return;
  var cs = $(im.components[ns]),
      height = cs.show().height();
  if (cs.length == 0) return;
  cs.hide().height(height).slideDown(function(){$(this).height('')});
});

im.bind('ChangeNavTab',function(e) {
  console.log('changenavtab',e);
  im.trigger('ChangeActiveAction',e.eventData);
  im.trigger('ChangeActiveComponent',e.eventData);
  var parent = getElem('div.editorcontrols');
  switch(e.eventData) {
    case 'add':
      parent.children('div.control-sets').hide();
      parent.children('div.components').show();
      break;
    case 'edit':
      parent.children('div.components').hide();
      parent.children('div.control-sets').show();
      break;
  }
});


///////////////////////////////////////////////////////////////////////////////
//                              jquerybinding.js                             //
///////////////////////////////////////////////////////////////////////////////
// End the ImageEditor object.
  window.c5_image_editor = im; // Safe keeping
  window.im = im;
  return im;
};
$.fn.ImageEditor = function (settings) {
  (settings === undefined && (settings = {}));
  settings.imageload = $.fn.dialog.hideLoader;
  var self = $(this);
  settings.container = self[0];
  if (self.height() == 0) {
    setTimeout(function(){
      self.ImageEditor(settings);
    },50);
    return;
  }
  self.height(self.height()-31);
  (settings.width === undefined && (settings.width = self.width()));
  (settings.height === undefined && (settings.height = self.height()));
  $.fn.dialog.showLoader();
  var im = new ImageEditor(settings);

  var context = im.domContext;
  $('div.controls',context).children('ul.nav').children().click(function(){
    if ($(this).hasClass('active')) return false;
    $('div.controls',context).children('ul.nav').children().removeClass('active');
    $(this).addClass('active');
    im.trigger('ChangeNavTab',$(this).text().toLowerCase());
    return false;
  });
  $('div.controlset',context).find('div.control').children('div.contents').slideUp(0)
  .end().end().find('h4').click(function(){
    $('div.controlset',context).find('h4').not($(this)).removeClass('active');
    var ns = $(this).parent().attr('data-namespace');
    im.trigger('ChangeActiveAction',"ControlSet_"+ns);
  });

  $('div.component',context).find('div.control').children('div.contents').slideUp(0).hide()
  .end().end().find('h4').click(function(){
    $('div.component',context).children('h4').not($(this)).removeClass('active');
    var ns = $(this).parent().attr('data-namespace');
    im.trigger('ChangeActiveComponent',"Component_"+ns);
  });
  $('div.components').hide();

  im.bind('imageload', $.fn.dialog.hideLoader);
  return im;
};


///////////////////////////////////////////////////////////////////////////////
//                                 filters.js                                //
///////////////////////////////////////////////////////////////////////////////
ImageEditor.prototype.filter = {};
ImageEditor.prototype.filter.grayscale = Kinetic.Filters.Grayscale;
ImageEditor.prototype.filter.sepia = function (imageData) {
  var i;
  var data = imageData.data;
  for (i = 0; i < data.length; i += 4) {
    data[i]     = (data[i] * 0.393 + data[i + 1] * 0.769 + data[i + 2] * 0.189);
    data[i + 1] = (data[i] * 0.349 + data[i + 1] * 0.686 + data[i + 2] * 0.168);
    data[i + 2] = (data[i] * 0.272 + data[i + 1] * 0.534 + data[i + 2] * 0.131);
  }
};
ImageEditor.prototype.filter.brightness = function (imageData,ob) {
	var adjustment = ob.level;
	var d = imageData.data;
	for (var i=0; i<d.length; i+=4) {
		d[i] += adjustment;
		d[i+1] += adjustment;
		d[i+2] += adjustment;
	}
};
ImageEditor.prototype.filter.restore = function (imageData,ob) {
	var adjustment = ob.level;
  	var d = imageData.data;
  	var g = ob.imageData.data;
	for (var i=0; i<d.length; i+=4) {
		d[i] = g[i];
		d[i+1] = g[i+1];
		d[i+2] = g[i+2];
	}
};
