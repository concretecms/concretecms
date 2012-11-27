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