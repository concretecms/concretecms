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


///////////////////////////////////////////////////////////////////////////////
//                               imageeditor.js                              //
///////////////////////////////////////////////////////////////////////////////
var ControlSet = function(im,js,controlSet) {
  var Window = this;
  Window.controlSet = controlSet;
  Window.im = im;
  Window.js = js;
  eval(js);
};
var ImageEditor = function (settings) {
  if (settings === undefined) return this;
  var im         = this, x;
  im.width       = settings.width;
  im.height      = settings.height;
  im.stage       = new Kinetic.Stage(settings);
  im.editor      = new Kinetic.Layer();
  im.namespaces  = {};
  im.controlSets = {};

  im.center = {
    x: im.width / 2,
    y: im.height / 2
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
im.bindEvent = im.bind = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  if (element.addEventListener) {
    element.addEventListener(type.toLowerCase(), handler, false);
  } else {
    element.attachEvent('on' + type.toLowerCase(), handler);
  }
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data) {
  var event, eventName = 'ImageEditorEvent', element = im.stage.getContainer(), data = data || im;
  if (document.createEvent) {
    event = document.createEvent("HTMLEvents");
    event.initEvent(type.toLowerCase(), true, true);
  } else {
    event = document.createEventObject();
    event.eventType = type.toLowerCase();
  }
  event.eventName = eventName;
  event.eventData = data || { };

  if (document.createEvent) {
    element.dispatchEvent(event);
  } else {
    element.fireEvent("on" + event.eventType, event);
  }
  if (typeof element['on' + type.toLowerCase()] === 'function') { element['on' + type.toLowerCase()](event); }
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


im.addExtension = function(ns,js,elem) {
  if (jQuery && elem instanceof jQuery) elem = elem[0];
  elem.controlSet = function(im,js) {
    this.im = im;
    eval(js);
    return this;
  };
  var newim = im.clone(ns);
  var nso = elem.controlSet(newim,js);
  im.controlSets[ns] = nso;
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
//                              control_sets.js                              //
///////////////////////////////////////////////////////////////////////////////
im.bind('imageload',function(){
  var cs = settings.controlsets || {}, namespace,first;
  for (namespace in cs) {
    var myns = namespace;
    if (!first) first = myns;
    var running = 0;
    $.ajax(cs[myns]['src'],{
      dataType:'text',
      cache:false,
      myns:myns,
      beforeSend:function(){running++;},
      success:function(js){
        running--;
        var nso = im.addExtension(this.myns,js,cs[this.myns]['element']);
        im.fire('controlsetload',nso);
        if (0 == running) {
          im.activeControlSet = first;
          im.trigger('changecontrolset',first);
        }
      },
      error: function(xhr, errDesc, exception) {
        running--;
        if (0 == running) {
          im.activeControlSet = first;
          im.trigger('changecontrolset',first);
        }
      }
    });
  }
});
im.bind('changecontrolset',function(e){
  var active = $('div.controlset[data-namespace='+e.eventData+']','div.controls')
    .children('div.control').slideDown().end().children('h4').addClass('active').end();
  $('div.controlset','div.controls').not(active)
    .children('div.control').slideUp().end().children('h4').removeClass('active');
});


///////////////////////////////////////////////////////////////////////////////
//                              jquerybinding.js                             //
///////////////////////////////////////////////////////////////////////////////
// End the ImageEditor object.
  window.c5_image_editor = im; // Safe keeping
  return im;
};

$('div.controlset').find('div.control').slideUp(0);
$('div.controlset').find('h4').click(function(){
  $('div.controlset').find('h4').not($(this)).removeClass('active');
  var ns = $(this).parent().attr('data-namespace');
  im.trigger('changecontrolset',ns);
});
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
  im.bind('imageload', $.fn.dialog.hideLoader);
  return im;
};


///////////////////////////////////////////////////////////////////////////////
//                                 filters.js                                //
///////////////////////////////////////////////////////////////////////////////
ImageEditor.fn = ImageEditor.prototype;
ImageEditor.fn.filters = {};
ImageEditor.fn.filters.grayscale = Kinetic.Filters.Grayscale;
ImageEditor.fn.filters.sepia = function (imageData) {
  var i;
  var data = imageData.data;
  for (i = 0; i < data.length; i += 4) {
    data[i]     = (data[i] * 0.393 + data[i + 1] * 0.769 + data[i + 2] * 0.189);
    data[i + 1] = (data[i] * 0.349 + data[i + 1] * 0.686 + data[i + 2] * 0.168);
    data[i + 2] = (data[i] * 0.272 + data[i + 1] * 0.534 + data[i + 2] * 0.131);
  }
};
ImageEditor.fn.filters.brightness = function (imageData,ob) {
	var adjustment = ob.level;
	var d = imageData.data;
	for (var i=0; i<d.length; i+=4) {
		d[i] += adjustment;
		d[i+1] += adjustment;
		d[i+2] += adjustment;
	}
};
ImageEditor.fn.filters.restore = function (imageData,ob) {
	var adjustment = ob.level;
  	var d = imageData.data;
  	var g = ob.imageData.data;
	for (var i=0; i<d.length; i+=4) {
		d[i] = g[i];
		d[i+1] = g[i+1];
		d[i+2] = g[i+2];
	}
};
