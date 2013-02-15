
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
  im.editor        = new Kinetic.Layer();
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
