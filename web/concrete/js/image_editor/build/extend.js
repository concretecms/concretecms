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
  namespaces['namespace'] = newim;
  return newim;
};
