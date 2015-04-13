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
var control_sets = [], components = [], filters = [];
var ImageEditor = function (settings) {
    "use strict";
    if (settings === undefined) return this;
    settings.pixelRatio = 1;
    var im = this, x, round = function (float) {
        return Math.round(float)
    };
    im.saveData = settings.saveData || {};
    im.saveUrl = settings.saveUrl;
    im.width = settings.width;
    im.height = settings.height;
    im.strictSize = typeof settings.strictSize !== 'undefined' ? !!settings.strictSize : settings.saveWidth > 0;
    im.saveWidth = settings.saveWidth || (im.strictSize ? 0 : round(im.width / 2));
    im.saveHeight = settings.saveHeight || (im.strictSize ? 0 : round(im.height / 2));
    im.stage = new Kinetic.Stage(settings);
    im.namespaces = {};
    im.controlSets = {};
    im.components = {};
    im.settings = settings;
    im.filters = {};
    im.fileId = im.settings.fID;
    im.scale = 1;
    im.crosshair = new Image();
    im.uniqid = im.stage.getContainer().id;
    im.editorContext = $(im.stage.getContainer()).parent();
    im.domContext = im.editorContext.parent();
    im.controlContext = im.domContext.children('div.controls');
    im.controlSetNamespaces = [];
    debugger;

    im.showLoader = $.fn.dialog.showLoader;
    im.hideLoader = $.fn.dialog.hideLoader;
    im.stage.im = im;
    im.stage.elementType = 'stage';
    im.crosshair.src = CCM_IMAGE_PATH + '/image_editor/crosshair.png';

    im.center = {
        x: Math.round(im.width / 2),
        y: Math.round(im.height / 2)
    };

    im.centerOffset = {
        x: im.center.x,
        y: im.center.y
    };

    var getElem = function (selector) {
            return $(selector, im.domContext);
        },
        log = function () {
            if (settings.debug === true && typeof console !== 'undefined') {
                var args = arguments;
                if (args.length == 1) args = args[0];
                console.log(args);
            }
        },
        warn = function () {
            if (settings.debug === true && typeof console !== 'undefined') {
                var args = arguments;
                if (args.length == 1) args = args[0];
                console.warn(args);
            }
        },
        error = function () {
            if (typeof console !== 'undefined') {
                var args = arguments;
                if (args.length == 1) args = args[0];
                console.error("Image Editor Error: " + args);
            }
        };

    im.stage._setDraggable = im.stage.setDraggable;
    im.stage.setDraggable = function (v) {
        warn('setting draggable to ' + v);
        return im.stage._setDraggable(v);
    };
// Handle event binding.
im.bindEvent = im.bind = im.on = function (type, handler, elem) {
  var element = elem || im.stage.getContainer();
  if (element instanceof jQuery) element = element[0];
  ConcreteEvent.sub(type,handler,element);
};

// Handle event firing
im.fireEvent = im.fire = im.trigger = function (type, data, elem) {
  var element = elem || im.stage.getContainer();
  if (element instanceof jQuery) element = element[0];
  ConcreteEvent.pub(type,data,element);
};
im.addElement = function(object, type) {
  var layer = new Kinetic.Layer();
  object.elementType = type;
  layer.elementType = type;

  layer.add(object);
  im.stage.add(layer);
  layer.moveDown();
  im.stage.draw();
};

im.on('backgroundBuilt',function(){
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.foreground.add(im.activeElement.doppelganger);
    im.activeElement.doppelganger.setPosition(im.activeElement.getPosition());
  }
});

im.setActiveElement = function(element) {
  if (element.defer) {
    return im.setActiveElement(element.defer);
  }
  if (im.activeElement == element) return;
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.activeElement.doppelganger.remove();
  }
  if (element === im.stage || element.nodeType == 'Stage') {
    im.trigger('ChangeActiveAction', im.controlSetNamespaces.length ? im.controlSetNamespaces[0] : undefined);
    $('div.control-sets',im.controlContext).find('h4.active').removeClass('active');
  } else if (element.doppelganger !== undefined) {
    im.foreground.add(element.doppelganger);
    im.foreground.draw();
  }
  im.trigger('beforeChangeActiveElement',im.activeElement);
  im.alterCore('activeElement',element);
  im.trigger('changeActiveElement',element);
  im.stage.draw();
};
im.bind('ClickedElement',function(e, data) {
  im.setActiveElement(data);
});

im.bind('stageChanged',function(e){
  if (im.activeElement.getWidth() > im.stage.getScaledWidth() || im.activeElement .getHeight() > im.stage.getScaledHeight()) {
    im.setActiveElement(im.stage);
  }
});
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
im.save = function saveImage() {
    im.fire('ChangeActiveAction');
    im.fire('ImageEditorWillSave');

    $.fn.dialog.showLoader();

    im.stage.toDataURL({
        callback: function (data) {
            var fake_canvas = $('<img />').addClass('fake_canvas').appendTo(im.editorContext.children('.Editor'));
            fake_canvas.attr('src', data);

            fake_canvas.css({
                position: 'absolute',
                top: 0,
                left: 0,
                backgroundColor: 'white'
            });

            var oldStagePosition = im.stage.getPosition(),
                oldScale = im.scale,
                oldWidth = im.stage.getWidth(),
                oldHeight = im.stage.getHeight();

            im.stage.setPosition(-im.saveArea.getX(), -im.saveArea.getY());
            im.stage.setScale(1);
            im.background.hide();
            im.foreground.hide();
            im.stage.setHeight(im.saveHeight + 100);
            im.stage.setWidth(im.saveWidth + 100);
            im.stage.draw();

            var mime = settings.mime;
            if (mime !== 'image/jpeg' && mime !== 'image/png') {
                // default to png
                mime = 'image/png';
            }

            im.stage.toDataURL({
                mimeType: mime,
                quality: settings.jpegCompression,
                width: im.saveWidth,
                height: im.saveHeight,
                callback: function saveImageDataUrlCallback(url) {
                    im.stage.setPosition(oldStagePosition);
                    im.background.show();
                    im.foreground.show();
                    im.stage.setScale(oldScale);
                    im.stage.setHeight(oldHeight);
                    im.stage.setWidth(oldWidth);
                    im.stage.draw();

                    fake_canvas.remove();

                    $.post(im.saveUrl, _.extend(im.saveData, {
                        fID: im.fileId,
                        imgData: url
                    }), function (res) {
                        $.fn.dialog.hideLoader();
                        var result = JSON.parse(res);
                        if (result.error === 1) {
                            alert(result.message);
                            $('button.save[disabled]').attr('disabled', false);
                        } else if (result.error === 0) {
                            im.fire('ImageEditorDidSave', _.extend(im.saveData, {
                                fID: im.fileId,
                                imgData: url
                            }));
                            Concrete.event.fire('ImageEditorDidSave', _.extend(im.saveData, {
                                fID: im.fileId,
                                imgData: url
                            }));
                        }
                    });
                }
            });
        }
    });
};

im.actualPosition = function actualPosition(x, y, cx, cy, rad) {
    var ay = y - cy,
        ax = x - cx,
        degChange = im.activeElement.getRotation() + Math.atan2(ay, ax),
        r = Math.sqrt(Math.pow(ax, 2) + Math.pow(ay, 2));
    return [cx + (r * Math.cos(degChange)), cy + (r * Math.sin(degChange))];
};

im.getActualRect = function actualRect(cx, cy, elem) {
    var rect = [], rad = elem.getRotation();
    rect.push(im.actualPosition(elem.getX(), elem.getY(), cx, cy, rad));
    rect.push(im.actualPosition(elem.getX() + elem.getWidth() * elem.getScaleX(), elem.getY(), cx, cy, rad));
    rect.push(im.actualPosition(elem.getX() + elem.getWidth() * elem.getScaleX(), elem.getY() + elem.getHeight() * elem.getScaleY(), cx, cy, rad));
    rect.push(im.actualPosition(elem.getX(), elem.getY() + elem.getHeight() * elem.getScaleY(), cx, cy, rad));
    return rect;
};

im.adjustSavers = function AdjustingSavers(fire) {
    if (im.activeElement.nodeType === 'Stage') return;
    im.foreground.autoCrop = false;
    im.background.autoCrop = false;
    var i, e, u, score = {min: {x: false, y: false}, max: {x: false, y: false}};
    /*
     for (var i = im.stage.children.length - 1; i >= 0; i--) {
     var layer = im.stage.children[i];
     if (layer.autoCrop === false) continue;
     for (var e = layer.children.length - 1; e >= 0; e--) {
     var child = layer.children[e],
     rect = im.getActualRect(0, 0, child);
     console.log(child);

     for (var u = rect.length - 1; u >= 0; u--) {
     var point = rect[u], x = point[0] + layer.getX(), y = point[1] + layer.getY();
     if (x > score.max.x || score.max.x === false) score.max.x = x;
     if (x < score.min.x || score.min.x === false) score.min.x = x;
     if (y > score.max.y || score.max.y === false) score.max.y = y;
     if (y < score.min.y || score.min.y === false) score.min.y = y;
     }
     }
     }
     */
    var child = im.activeElement,
        layer = child.parent,
        rect = im.getActualRect(0, 0, child),
        u, size;

    for (u = rect.length - 1; u >= 0; u--) {
        var point = rect[u], x = point[0] + layer.getX(), y = point[1] + layer.getY();
        if (x > score.max.x || score.max.x === false) score.max.x = x;
        if (x < score.min.x || score.min.x === false) score.min.x = x;
        if (y > score.max.y || score.max.y === false) score.max.y = y;
        if (y < score.min.y || score.min.y === false) score.min.y = y;
    }

    size = {width: score.max.x - score.min.x, height: score.max.y - score.min.y};
    if (!im.strictSize) {
        im.alterCore('saveWidth', Math.round(size.width));
        im.alterCore('saveHeight', Math.round(size.height));
        im.buildBackground();
    }

    var ap = [im.center.x - (im.activeElement.getWidth() * im.activeElement.getScaleX()) / 2,
            im.center.y - (im.activeElement.getHeight() * im.activeElement.getScaleY()) / 2],
        adj = im.actualPosition(ap[0], ap[1], im.center.x, im.center.y, im.activeElement.getRotation());

    im.activeElement.parent.setPosition(adj.map(Math.round));

    if (fire !== false) im.fire('adjustedsavers');
    im.stage.draw();
};

im.bind('imageLoad', function () {
    setTimeout(im.adjustSavers, 0);
});
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
    im.disable = function() {
      im.enabled = false;
      $(elem).parent().parent().addClass('disabled');
    };
    im.enable = function() {
      im.enabled = true;
      $(elem).parent().parent().removeClass('disabled');
    };
    this.im = im;
    this.$ = $;
    warn('Loading ControlSet',im);
    try {
      (new Function('im','$',js)).call(this, im, $);
    } catch(e) {
      console.log(e.stack);
      var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/,'$1').split(':');
      if (pos[1] && !isNaN(parseInt(pos[1]))) {
        var jsstack = js.split("\n");
        var msg = "Parse error at line #"+pos[0]+" char #"+pos[1]+" within "+ns;
        msg += "\n"+jsstack[parseInt(pos[0])-1];
        msg += "\n"+(new Array(parseInt(pos[1])).join(" "))+"^";
        error(msg);
      } else {
        error("\"" + e.message + "\" in \"" + im.namespace + "\"");
      }
    }
    return this;
  };
  var newim = im.clone(ns);
  var nso = elem.controlSet.call(elem,newim,js);
  im.controlSets[ns] = nso;
  return nso;
};

im.addFilter = function(ns,js) {
  var filter = function(im,js) {
    this.namespace = im.namespace;
    this.im = im;
    try {
      (new Function('im','$',js)).call(this, im, $);
    } catch(e) {
      error(e);
      window.lastError = e;
      var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/,'$1').split(':');
      if (pos.length != 2) {
        error(e.message);
        error(e.stack);
      } else {
        var jsstack = js.split("\n");
        var msg = "Parse error at line #"+pos[0]+" char #"+pos[1]+" within "+ns;
        msg += "\n"+jsstack[parseInt(pos[0])-1];
        msg += "\n"+(new Array(parseInt(pos[1]) || 0).join(" "))+"^";
        error(msg);
      }
    }
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
    im.disable = function() {
      $(this).parent().parent().addClass('disabled');
    };
    im.enable = function() {
      $(this).parent().parent().removeClass('disabled');
    };
    this.im = im;
    warn('Loading component',im);
    try {
      (new Function('im','$',js)).call(this, im, $);
    } catch(e) {
      var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/,'$1').split(':');
      if (pos[1] && !isNaN(parseInt(pos[1]))) {
        var jsstack = js.split("\n");
        var msg = "Parse error at line #"+pos[0]+" char #"+pos[1]+" within "+ns;
        msg += "\n"+jsstack[parseInt(pos[0])-1];
        msg += "\n"+(new Array(parseInt(pos[1])).join(" "))+"^";
        error(msg);
      } else {
        error("\"" + e.message + "\" in \"" + im.namespace + "\"");
      }
    }
    return this;
  };
  var newim = im.clone(ns);
  var nso = elem.component.call(elem,newim,js);
  im.components[ns] = nso;
  return nso;
};
// Set up background
im.background = new Kinetic.Layer();
im.foreground = new Kinetic.Layer();
im.stage.add(im.background);
im.stage.add(im.foreground);
im.bgimage = new Image();
im.saveArea = new Kinetic.Rect();
im.background.add(im.saveArea);
im.bind('load', function(){
  im.saveArea.setFillPatternImage(im.bgimage);

  im.saveArea.setFillPatternOffset([-(im.saveWidth/2),-(im.saveHeight/2)]);
  im.saveArea.setFillPatternScale(1/im.scale);
  im.saveArea.setFillPatternX(0);
  im.saveArea.setFillPatternY(0);
  im.saveArea.setFillPatternRepeat('repeat');

  im.background.on('click',function(){
    im.setActiveElement(im.stage);
  });
}, im.bgimage);
im.bgimage.src = '/concrete/images/testbg.png';
im.buildBackground = function() {
  var dimensions = im.stage.getTotalDimensions();

  im.saveArea.setFillPatternOffset([-(im.saveWidth/2) * im.scale,-(im.saveHeight/2) * im.scale]);
  im.saveArea.setX(Math.round(im.center.x - (im.saveWidth / 2)));
  im.saveArea.setY(Math.round(im.center.y - (im.saveHeight / 2)));
  im.saveArea.setFillPatternScale(1/im.scale);
  im.saveArea.setWidth(im.saveWidth);
  im.saveArea.setHeight(im.saveHeight);

  if (!im.coverLayer) {
    im.coverLayer = new Kinetic.Rect;
    im.coverLayer.setStroke('rgba(150,150,150,.5)');
    im.coverLayer.setFill('transparent');
    im.coverLayer.setListening(false);
    im.coverLayer.setStrokeWidth(Math.max(dimensions.width,dimensions.height,500));
    im.foreground.add(im.coverLayer);
  }
  var width = Math.max(dimensions.width,dimensions.height)*2;
  im.coverLayer.setPosition(im.saveArea.getX() - width / 2, im.saveArea.getY() - width / 2);
  im.coverLayer.setSize(im.saveArea.getWidth() + width, im.saveArea.getHeight() + width);
  im.coverLayer.setStrokeWidth(width);
  im.fire('backgroundBuilt');
  //im.foreground.moveToTop();
  im.saveArea.draw();
  im.coverLayer.draw();
    debugger;
};

im.buildBackground();
im.on('stageChanged',im.buildBackground);
im.stage.setDragBoundFunc(function (ret) {
    var dim = im.stage.getTotalDimensions();

    var maxx = Math.max(dim.max.x, dim.min.x) + 100,
        minx = Math.min(dim.max.x, dim.min.x) - 100,
        maxy = Math.max(dim.max.y, dim.min.y) + 100,
        miny = Math.min(dim.max.y, dim.min.y) - 100;

    ret.x = Math.floor(ret.x);
    ret.y = Math.floor(ret.y);

    if (ret.x > maxx) ret.x = maxx;
    if (ret.x < minx) ret.x = minx;
    if (ret.y > maxy) ret.y = maxy;
    if (ret.y < miny) ret.y = miny;

    ret.x = Math.floor(ret.x);
    ret.y = Math.floor(ret.y);

    return ret;
});
im.setActiveElement(im.stage);
im.stage.setDraggable(true);
im.autoCrop = true;
im.on('imageLoad', function () {
    var padding = 50;

    var w = im.stage.getWidth() - (padding * 2), h = im.stage.getHeight() - (padding * 2);
    if (im.saveWidth < w && im.saveHeight < h) return;
    var perc = Math.max(im.saveWidth / w, im.saveHeight / h);

    im.scale = 1 / perc;
    im.scale = Math.round(im.scale * 100) / 100;
    im.alterCore('scale', im.scale);

    im.stage.setScale(im.scale);
    im.stage.setX((im.stage.getWidth() - (im.stage.getWidth() * im.stage.getScale().x)) / 2);
    im.stage.setY((im.stage.getHeight() - (im.stage.getHeight() * im.stage.getScale().y)) / 2);

    var pos = (im.stage.getDragBoundFunc())({x: im.stage.getX(), y: im.stage.getY()});
    im.stage.setX(pos.x);
    im.stage.setY(pos.y);

    im.fire('scaleChange');
    im.fire('stageChanged');
    im.buildBackground();
});

im.fit = function (wh, scale) {
    if (scale === false) {
        return {
            width: im.saveWidth,
            height: im.saveHeight
        };
    }
    var height = wh.height,
        width = wh.width;

    if (width > im.saveWidth) {
        height /= width / im.saveWidth;
        width = im.saveWidth;
    }
    if (height > im.saveHeight) {
        width /= height / im.saveHeight;
        height = im.saveHeight;
    }
    return {width: width, height: height};
};
if (settings.src) {
    im.showLoader(ccmi18n_imageeditor.loadingImage);
    var img = new Image(), controlSetsLoaded = false;
    im.bind('ControlSetsLoaded', function () {
        controlSetsLoaded = true;
    });

    im.bind('load', function imageLoaded() {
        if (!im.strictSize) {
            im.saveWidth = img.width;
            im.saveHeight = img.height;
            im.fire('saveSizeChange');
            im.buildBackground();
        } else if (im.saveWidth == 0 || im.saveHeight == 0) {
            if (im.saveWidth == 0) {
                if (im.saveHeight == 0) {
                    im.saveWidth = img.width;
                    im.saveHeight = img.height;

                    im.fire('saveSizeChange');
                    im.buildBackground();
                } else {
                    im.saveWidth = Math.floor(img.width / img.height * im.saveHeight);

                    im.fire('saveSizeChange');
                    im.buildBackground();
                }
            } else if (im.saveHeight == 0) {
                im.saveHeight = Math.floor(img.height / img.width * im.saveWidth);

                im.fire('saveSizeChange');
                im.buildBackground();
            }
        }
        debugger;
        var center = {
            x: Math.floor(im.center.x - (img.width / 2)),
            y: Math.floor(im.center.y - (img.height / 2))
        };
        var image = new Kinetic.Image({
            image: img,
            x: 0,
            y: 0
        });
        image.setPosition(center);
        im.addElement(image, 'image');
        _.defer(function () {
            im.fire('imageload');
        });
        function activate() {
            _.defer(function activateImageElement() {
                im.stage.draw();
                im.setActiveElement(image);
                im.fire('changeActiveAction', im.controlSetNamespaces[0]);
            });
        }

        if (controlSetsLoaded) {
            activate();
        } else {
            im.bind('ControlSetsLoaded', activate);
        }
    }, img);

    img.src = settings.src;
} else {
    im.fire('imageload');
}
im.bind('imageload', function () {
    var cs = settings.controlsets || {}, filters = settings.filters || {}, namespace, firstcs;
    var running = 0;
    log('Loading ControlSets');
    im.showLoader(ccmi18n_imageeditor.loadingControlSets);
    im.fire('LoadingControlSets');
    for (namespace in cs) {
        var myns = "ControlSet_" + namespace;
        im.controlSetNamespaces.push(myns);
        $.ajax(cs[namespace]['src'], {
            dataType: 'text',
            cache: false,
            namespace: namespace,
            myns: myns,
            beforeSend: function () {
                running++;
            },
            success: function (js) {
                running--;
                var nso = im.addControlSet(this.myns, js, cs[this.namespace]['element']);
                log(nso);
                im.fire('controlSetLoad', nso);
                if (0 == running) {
                    im.trigger('ControlSetsLoaded');
                }
            },
            error: function (xhr, errDesc, exception) {
                running--;
                if (0 == running) {
                    im.trigger('ControlSetsLoaded');
                }
            }
        });
    }
});
im.bind('ControlSetsLoaded', function () {
    im.fire('LoadingComponents');
    im.showLoader(ccmi18n_imageeditor.loadingComponents);
    var components = settings.components || {}, namespace, running = 0;
    log('Loading Components');

    for (namespace in components) {
        var myns = "Component_" + namespace;
        $.ajax(components[namespace]['src'], {
            dataType: 'text',
            cache: false,
            namespace: namespace,
            myns: myns,
            beforeSend: function () {
                running++;
            },
            success: function (js) {
                running--;
                var nso = im.addComponent(this.myns, js, components[this.namespace]['element']);
                log(nso);
                im.fire('ComponentLoad', nso);
                if (0 == running) {
                    im.trigger('ComponentsLoaded');
                }
            },
            error: function (xhr, errDesc, exception) {
                running--;
                if (0 == running) {
                    im.trigger('ComponentsLoaded');
                }
            }
        });
    }
    if (0 == running) {
        im.trigger('ComponentsLoaded');
    }
});

im.bind('ComponentsLoaded', function () { // do this when the control sets finish loading.
    log('Loading Filters');
    im.showLoader(ccmi18n_imageeditor.loadingFilters);
    var filters = settings.filters || {}, namespace, firstf, active = 0;
    im.fire('LoadingFilters');
    for (namespace in filters) {
        if (filters.hasOwnProperty(namespace)) {
            (function(namespace) {
                var settings = _.clone(filters[namespace]),
                    myns = "Filter_" + namespace,
                    name = settings.name;

                if (!firstf) {
                    firstf = myns;
                }

                active++;
                $.ajax(filters[namespace].src, {
                    dataType: 'text',
                    cache: false,
                    namespace: namespace,
                    myns: myns,
                    name: name,
                    success: function (js) {
                        var nso = im.addFilter(this.myns, js);
                        nso.name = this.name;
                        nso.settings = settings;
                        im.fire('filterLoad', nso);
                        active--;
                        if (0 === active) {
                            im.trigger('FiltersLoaded');
                        }
                    },
                    error: function (xhr, errDesc, exception) {
                        active--;
                        if (0 === active) {
                            im.trigger('FiltersLoaded');
                        }
                    }
                });
            }(namespace));
        }
    }
});
im.bind('ChangeActiveAction', function (e, ns) {
    if (ns === im.activeControlSet) return;
    for (var ons in im.controlSets) {
        getElem(im.controlSets[ons]);
        if (ons !== ns) getElem(im.controlSets[ons]).slideUp();
    }
    im.activeControlSet = ns;
    im.alterCore('activeControlSet', ns);
    if (!ns) {
        $('div.control-sets', im.controlContext).find('h4.active').removeClass('active');
        return;
    }
    var cs = $(im.controlSets[ns]),
        height = cs.show().height();
    if (cs.length == 0) return;
    cs.hide().height(height).slideDown(function () {
        $(this).height('')
    });
});

im.bind('ChangeActiveComponent', function (e, ns) {
    if (ns === im.activeComponent) return;
    for (var ons in im.components) {
        if (ons !== ns) getElem(im.components[ons]).slideUp();
    }
    im.activeComponent = ns;
    im.alterCore('activeComponent', ns);
    if (!ns) return;
    var cs = $(im.components[ns]),
        height = cs.show().height();
    if (cs.length == 0) return;
    cs.hide().height(height).slideDown(function () {
        $(this).height('')
    });
});

im.bind('ChangeNavTab', function (e, data) {
    im.trigger('ChangeActiveAction', data);
    im.trigger('ChangeActiveComponent', data);
    var parent = getElem('div.editorcontrols');
    switch (data) {
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


im.bind('FiltersLoaded', function () {
    im.hideLoader();
});
im.slideOut = $("<div/>").addClass('slideOut').css({
  width:0,
  float:'right',
  height:'100%',
  'overflow-x':'hidden',
  right:im.controlContext.width()-1,
  position:'absolute',
  background:'white',
  'box-shadow':'black -20px 0 20px -25px'
});

im.slideOutContents = $('<div/>').appendTo(im.slideOut).width(300);
im.showSlideOut = function(contents,callback) {
  im.hideSlideOut(function(){
    im.slideOut.empty();
    im.slideOutContents = contents.width(300);
    im.slideOut.append(im.slideOutContents)
    im.slideOut.addClass('active').addClass('sliding');
    im.slideOut.stop(1).slideOut(300, function(){
      im.slideOut.removeClass('sliding');
      ((typeof callback === 'function') && callback());
    });
  });
};
im.hideSlideOut = function(callback) {
  im.slideOut.addClass('sliding');
  im.slideOut.slideIn(300,function(){
    im.slideOut.css('border-right','0');
    im.slideOut.removeClass('active').removeClass('sliding');
    ((typeof callback === 'function') && callback());
  });
};
im.controlContext.after(im.slideOut);
    // End the ImageEditor object.

    im.setActiveElement(im.stage);

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
        setTimeout(function () {
            self.ImageEditor(settings);
        }, 50);
        return;
    }
    self.closest('.ui-dialog').find('.ui-resizable-handle').hide();
    self.height("-=30");
    $('div.editorcontrols').height(self.height() - 90);
    self.width("-=330").parent().width("-=330").children('div.bottomBar').width("-=330");
    (settings.width === undefined && (settings.width = self.width()));
    (settings.height === undefined && (settings.height = self.height()));
    $.fn.dialog.showLoader();
    var im = new ImageEditor(settings);

    var context = im.domContext;
    $('div.control-sets > div.controlset', context).each(function () {
        var container = $(this),
            type = container.data('namespace');

        container.find('h4').click(function () {
            if (!container.hasClass('active')) {
                im.fire('ChangeActiveAction', 'ControlSet_' + type);
            }
        });

        im.bind('ChangeActiveAction', function(e, data) {
            if (data === 'ControlSet_' + type) {
                context.find('div.controlset.active').removeClass('active').children('.control').slideUp(250);
                container.addClass('active');

                var control = container.children('.control').height('auto');
                control.slideDown(250);
            }
        });
    });

    $('div.controls > div.controlscontainer', context).children('div.save').children('button.save').click(function () {
        $(this).attr('disabled', true);
        im.save();
    }).end().children('button.cancel').click(function () {
        if (confirm(ccmi18n_imageeditor.areYouSure))
            $.fn.dialog.closeTop();
    });

    im.on('ChangeActiveAction', function (e, data) {
        if (!data) {
            $('h4.active', context).removeClass('active');
        }
    });

    im.on('ChangeActiveComponent', function (e, data) {
        if (!data) {
            $('div.controlset.active', context).removeClass('active');
        }
    });

    im.bind('imageload', $.fn.dialog.hideLoader);
    return im;
};
$.fn.slideOut = function (time, callback) {
    var me = $(this),
        startWidth = me.width(),
        totalWidth = 255;
    me.css('overflow-y', 'auto');
    if (startWidth == totalWidth) {
        me.animate({width: totalWidth}, 0, callback);
        return this;
    }
    me.width(startWidth).animate({width: totalWidth}, time || 300, callback || function () {
    });
    return this;
};
$.fn.slideIn = function (time, callback) {
    var me = $(this);
    me.css('overflow-y', 'hidden');
    if (me.width() === 0) {
        me.animate({width: 0}, 0, callback);
        return this;
    }

    me.animate({width: 0}, time || 300, callback || function () {
    });
    return this;
};
ImageEditor.prototype = ImageEditor.fn = {
  filter: {
    grayscale: Kinetic.Filters.Grayscale,
    sepia: function (imageData) {
      var i;
      var data = imageData.data;
      for (i = 0; i < data.length; i += 4) {
        data[i]     = (data[i] * 0.393 + data[i + 1] * 0.769 + data[i + 2] * 0.189);
        data[i + 1] = (data[i] * 0.349 + data[i + 1] * 0.686 + data[i + 2] * 0.168);
        data[i + 2] = (data[i] * 0.272 + data[i + 1] * 0.534 + data[i + 2] * 0.131);
      }
    },
    brightness: function (imageData,ob) {
      var adjustment = ob.level;
      var d = imageData.data;
      for (var i=0; i<d.length; i+=4) {
        d[i] += adjustment;
        d[i+1] += adjustment;
        d[i+2] += adjustment;
      }
    },
    invert: function (imageData,ob) {
      var d = imageData.data;
      for (var i=0; i<d.length; i+=4) {
        d[i] = 255 - d[i];
        d[i+1] = 255 - d[i+1];
        d[i+2] = 255 - d[i+2];
      }
    },
    restore: function (imageData,ob) {
      var adjustment = ob.level;
        var d = imageData.data;
        var g = ob.imageData.data;
      for (var i=0; i<d.length; i+=4) {
        d[i] = g[i];
        d[i+1] = g[i+1];
        d[i+2] = g[i+2];
      }
    }
  }
};
