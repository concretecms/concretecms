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
  var im         = this, x;
  im.width       = settings.width;
  im.height      = settings.height;
  im.stage       = new Kinetic.Stage(settings);
  im.editor      = new Kinetic.Layer();
  im.namespaces  = {};
  im.controlSets = {};
  im.components  = {};
  im.filters     = {};

  im.center = {
    x: im.width / 2,
    y: im.height / 2
  };
  var log = function() {
    if (settings.debug === true && console !== undefined) {
      var args = arguments;
      if (args.length == 1) args = args[0];
      console.log(args);
    }
  }
  var error = function() {
    if (console !== undefined) {
      var args = arguments;
      if (args.length == 1) args = args[0];
      console.error(args);
    }
  }


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
im.bindEvent = im.bind = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  ccm_event.sub(type,handler,element);
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data, elem) {
  var element = im.stage.getContainer() || elem;
  ccm_event.pub(type,data,element);
};


///////////////////////////////////////////////////////////////////////////////
//                                 extend.js                                 //
///////////////////////////////////////////////////////////////////////////////
im.extend = function(property,value) {
  im[property] = value;
};

im.alterCore = function(property,value) {
  var im = im, ns = 'core', i;
  if (im.namespace) {
    var ns = im.namespace;
    im = window.c5_image_editor;
  }
  im[property] = value;
  for (i in im.namespaces){
    im.namespaces[i][property] = value;
  }
};

im.clone = function(namespace) {
  var newim = new ImageEditor(),i;
  for (i in im) {
    newim[i] = im[i];
  }
  newim.namespace = namespace;
  im.namespaces['namespace'] = newim;
  return newim;
};


im.addControlSet = function(ns,js,elem) {
  if (jQuery && elem instanceof jQuery) elem = elem[0];
  elem.controlSet = function(im,js) {
    this.im = im;
    try {
      eval(js);
    } catch(e) {
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

im.addComponent = function(ns,js) {
  var component = function(im,js) {
    this.im = im;
    eval(js);
    return this;
  };
  var newim = im.clone(ns);
  var nso = new component(newim,js);
  im.components[ns] = nso;
  return nso;
};


///////////////////////////////////////////////////////////////////////////////
//                               background.js                               //
///////////////////////////////////////////////////////////////////////////////
// Set up background
im.background = new Kinetic.Layer();
im.background.add(new Kinetic.Rect({
  x: 0,
  y: 0,
  width: im.stage.getWidth(),
  height: im.stage.getHeight(),
  fill: '#eee'
}));
var getCoords = function (x, offset) {
  return {x: 2 * x, y: -x + offset};
};

var to = Math.max(im.stage.getWidth(), im.stage.getHeight()) * 2;
for (x = -10; x <= to; x += 20) {
  im.background.add(new Kinetic.Line({
    points: [getCoords(x, 0), getCoords(im.background.getWidth(), x)],
    stroke: '#e3e3e3'
  }));
}
im.stage.add(im.background);


///////////////////////////////////////////////////////////////////////////////
//                               imagestage.js                               //
///////////////////////////////////////////////////////////////////////////////
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


///////////////////////////////////////////////////////////////////////////////
//                                  image.js                                 //
///////////////////////////////////////////////////////////////////////////////
var img = new Image();
img.src = settings.src;
img.onload = function () {
  var center = {
    x: im.center.x - (img.width / 2),
    y: im.center.y - (img.height / 2)
  };
  im.prettifier = new Kinetic.Layer();
  im.image = new Kinetic.Image({
    image: img,
    x: Math.round(center.x),
    y: Math.round(center.y),
    stroke: '#000'
  });
  im.image.on('draw',function(){im.fire('imagedraw');});
  im.editor.add(im.image);
  im.stage.add(im.editor);
  im.imageData = im.image.getImageData();
  im.fireEvent('imageload');
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
    log(myns);
    if (!firstcs) firstcs = myns;
    $.ajax(cs[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        if (im === undefined) {
          im
        }
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
});
im.bind('ControlSetsLoaded',function(){ // do this when the control sets finish loading.
  log('Loaded');
  var filters = settings.filters || {}, components = settings.components || {}, namespace, firstf, firstc ;
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
  im.fire('LoadingComponents');
  for (namespace in components) {
    var myns = "Component_" + namespace;
    if (!firstc) firstc = myns;
    var running = 0;
    $.ajax(components[namespace]['src'],{
      dataType:'text',
      cache:false,
      namespace:namespace,
      myns:myns,
      success:function(js){
        var nso = im.addComponent(this.myns,js,cs[this.namespace]['element']);
        im.fire('componentLoad',nso);
      }
    });
  }
});
im.bind('ChangeActiveAction',function(e){
  var ns = e.eventData;
  if (ns === im.activeControlSet) return;
  for (var ons in im.controlSets) {
    if (ons !== ns) $(im.controlSets[ons]).slideUp();
  }
  im.activeControlSet = ns;
  if (!ns) return;
  var cs = $(im.controlSets[ns]),
      height = cs.show().height();
  cs.hide().height(height).slideDown(function(){$(this).height('');});
});

im.bind('ChangeNavTab',function(e) {
  im.trigger('ChangeActiveAction');
  switch(e.eventData) {
    case 'add':

  }
});


///////////////////////////////////////////////////////////////////////////////
//                              jquerybinding.js                             //
///////////////////////////////////////////////////////////////////////////////
// End the ImageEditor object.
  window.c5_image_editor = im; // Safe keeping
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
  (settings.width === undefined && (settings.width = self.width()));
  (settings.height === undefined && (settings.height = self.height()));
  $.fn.dialog.showLoader();
  var im = new ImageEditor(settings);

  $('div.controls').children('ul.nav').children().click(function(){
    if ($(this).hasClass('active')) return false;
    im.trigger('ChangeNavTab',$(this).text().toLowerCase());
    return false;
  });
  $('div.controlset').find('div.control').children('div.contents').slideUp(0);
  $('div.controlset').find('h4').click(function(){
    $('div.controlset').find('h4').not($(this)).removeClass('active');
    var ns = $(this).parent().attr('data-namespace');
    im.trigger('ChangeActiveAction',"ControlSet_"+ns);
  });
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
