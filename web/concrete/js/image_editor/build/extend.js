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
