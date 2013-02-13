
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