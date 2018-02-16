im.extend = function(property,value) {
  this[property] = value;
};

im.alterCore = function(property,value) {
  var nim = im, ns = 'core', i;
  if (im.namespace) {
    ns = nim.namespace;
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
  if ($ && elem instanceof $) elem = elem[0];
  elem.controlSet = function(im,js) {
    // jshint -W054
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
      window.console.log(e.stack);
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
     // jshint -W054
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
  if ($ && elem instanceof $) elem = elem[0];
  elem.component = function(im,js) {
    // jshint -W054
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
