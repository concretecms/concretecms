Kinetic.Stage.prototype.createCopy = function() {
    var e = [], t = this.getChildren(), n;
    for (n = 0; n < t.length; n++) {
        e.push(t[n].clone());
    }
    return e;
};

Kinetic.Stage.prototype.loadCopy = function(e) {
    var t;
    this.removeChildren();
    for (t = 0; t < e.length; t++) {
        this.add(e[t]);
    }
    this.draw();
};

Kinetic.Image.prototype.getImageData = function() {
    var e = new Kinetic.Canvas(this.attrs.image.width, this.attrs.image.height);
    var t = e.getContext();
    t.drawImage(this.attrs.image, 0, 0);
    try {
        var n = t.getImageData(0, 0, e.getWidth(), e.getHeight());
        return n;
    } catch (r) {
        Kinetic.Global.warn("Unable to get imageData.");
    }
};

Kinetic.Layer.prototype._cacheddraw = (new Kinetic.Layer).draw;

Kinetic.Layer.prototype.draw = function() {
    if (typeof im === "undefined" || typeof im.trigger === "undefined") {
        return this._cacheddraw();
    }
    im.trigger("beforeredraw", this);
    var e = this._cacheddraw();
    im.trigger("afterredraw", this);
    return e;
};

var ImageEditor = function(settings) {
    "use strict";
    if (settings === undefined) return this;
    var im = this, x;
    im.width = settings.width;
    im.height = settings.height;
    im.stage = new Kinetic.Stage(settings);
    im.editor = new Kinetic.Layer;
    im.namespaces = {};
    im.controlSets = {};
    im.components = {};
    im.filters = {};
    im.center = {
        x: im.width / 2,
        y: im.height / 2
    };
    var History = function() {
        var e = this;
        e.history = [];
        e.pointer = -1;
        e.save = function() {
            im.fire("beforehistorysave");
            e.history = e.history.slice(0, e.pointer + 1);
            e.history.push(im.stage.createCopy());
            e.movePointer(1);
            im.fire("historysave");
        };
        e.movePointer = function(t) {
            e.pointer += t;
            e.pointer < 0 && (e.pointer = 0);
            e.pointer >= e.history.length && (e.pointer = e.history.length - 1);
            return e.pointer;
        };
        e.render = function() {
            im.fire("beforehistoryrender");
            im.stage.loadCopy(e.history[e.pointer]);
            im.fire("historyrender");
        };
        e.undo = function() {
            im.fire("beforehistoryundo");
            e.movePointer(-1);
            e.render();
            im.fire("historyundo");
        };
        e.redo = function() {
            im.fire("beforehistoryredo");
            e.movePointer(1);
            e.render();
            im.fire("historyredo");
        };
    };
    im.history = new History;
    im.bindEvent = im.bind = function(e, t, n) {
        var r = n || im.stage.getContainer();
        ccm_event.sub(e, t, r);
    };
    im.fireEvent = im.fire = im.trigger = function(e, t, n) {
        var r = im.stage.getContainer() || n;
        ccm_event.pub(e, t, r);
    };
    im.extend = function(e, t) {
        im[e] = t;
    };
    im.alterCore = function(e, t) {
        var n = n, r = "core", i;
        if (n.namespace) {
            var r = n.namespace;
            n = window.c5_image_editor;
        }
        n[e] = t;
        for (i in n.namespaces) {
            n.namespaces[i][e] = t;
        }
    };
    im.clone = function(e) {
        var t = new ImageEditor, n;
        for (n in im) {
            t[n] = im[n];
        }
        t.namespace = e;
        im.namespaces["namespace"] = t;
        return t;
    };
    im.addControlSet = function(ns, js, elem) {
        if (jQuery && elem instanceof jQuery) elem = elem[0];
        elem.controlSet = function(im, js) {
            this.im = im;
            eval(js);
            return this;
        };
        var newim = im.clone(ns);
        var nso = elem.controlSet(newim, js);
        im.controlSets[ns] = nso;
        return nso;
    };
    im.addFilter = function(ns, js) {
        var filter = function(im, js) {
            this.im = im;
            eval(js);
            return this;
        };
        var newim = im.clone(ns);
        var nso = new filter(newim, js);
        im.filters[ns] = nso;
        return nso;
    };
    im.addComponent = function(ns, js) {
        var component = function(im, js) {
            this.im = im;
            eval(js);
            return this;
        };
        var newim = im.clone(ns);
        var nso = new component(newim, js);
        im.components[ns] = nso;
        return nso;
    };
    im.background = new Kinetic.Layer;
    im.background.add(new Kinetic.Rect({
        x: 0,
        y: 0,
        width: im.stage.getWidth(),
        height: im.stage.getHeight(),
        fill: "#eee"
    }));
    var getCoords = function(e, t) {
        return {
            x: 2 * e,
            y: -e + t
        };
    };
    var to = Math.max(im.stage.getWidth(), im.stage.getHeight()) * 2;
    for (x = -10; x <= to; x += 20) {
        im.background.add(new Kinetic.Line({
            points: [ getCoords(x, 0), getCoords(im.background.getWidth(), x) ],
            stroke: "#e3e3e3"
        }));
    }
    im.stage.add(im.background);
    var imageStage = new Kinetic.Rect({
        x: im.center.x,
        y: im.center.y,
        width: 0,
        height: 0,
        fill: "#ccc",
        stroke: "#777",
        strokeWidth: 1
    });
    im.imageStage = imageStage;
    im.imageStage._setheight = im.imageStage.setHeight;
    im.imageStage._setwidth = im.imageStage.setWidth;
    im.imageStage._setx = im.imageStage.setX;
    im.imageStage._sety = im.imageStage.setY;
    im.imageStage.setWidth = function(e) {
        this._setwidth(e);
        this._setx(im.center.x - Math.floor(e / 2));
        im.trigger("imageStageUpdate", this);
        this.parent.draw();
    };
    im.imageStage.setHeight = function(e) {
        this._setheight(e);
        this._sety(im.center.y - Math.floor(e / 2));
        im.trigger("imageStageUpdate", this);
        this.parent.draw();
    };
    im.imageStage.setX = function(e) {
        this._setx(e);
        this._setwidth(Math.abs(e - im.center.x) * 2);
        im.trigger("imageStageUpdate", this);
        this.parent.draw();
    };
    im.imageStage.setY = function(e) {
        this._sety(e);
        this._setheight(Math.abs(e - im.center.y) * 2);
        im.trigger("imageStageUpdate", this);
        this.parent.draw();
    };
    im.imageStageLayer = new Kinetic.Layer;
    im.imageStageLayer.add(imageStage);
    im.imageStage.setWidth(settings.imageStageWidth || Math.floor(im.stage.getWidth() / 2));
    im.imageStage.setHeight(settings.imageStageHeight || Math.floor(im.stage.getHeight() / 2));
    im.stage.add(im.imageStageLayer);
    var img = new Image;
    img.src = settings.src;
    img.onload = function() {
        var e = {
            x: im.center.x - img.width / 2,
            y: im.center.y - img.height / 2
        };
        im.prettifier = new Kinetic.Layer;
        im.image = new Kinetic.Image({
            image: img,
            x: Math.round(e.x),
            y: Math.round(e.y),
            stroke: "#000"
        });
        im.image.on("draw", function() {
            im.fire("imagedraw");
        });
        im.editor.add(im.image);
        im.stage.add(im.editor);
        im.imageData = im.image.getImageData();
        im.fireEvent("imageload");
    };
    im.bind("imageload", function() {
        var e = settings.controlsets || {}, t = settings.filters || {}, n = settings.components || {}, r, i, s, o;
        for (r in e) {
            var u = "ControlSet_" + r;
            console.log(u);
            if (!i) i = u;
            var a = 0;
            $.ajax(e[r]["src"], {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: u,
                beforeSend: function() {
                    a++;
                },
                success: function(t) {
                    a--;
                    var n = im.addControlSet(this.myns, t, e[this.namespace]["element"]);
                    console.log(n);
                    im.fire("controlSetLoad", n);
                    if (0 == a) {
                        im.activeControlSet = i;
                        im.trigger("ChangeActiveAction", i);
                    }
                },
                error: function(e, t, n) {
                    a--;
                    if (0 == a) {
                        im.activeControlSet = i;
                        im.trigger("ChangeActiveAction", i);
                    }
                }
            });
        }
        for (r in t) {
            var u = "Filter_" + r;
            if (!s) s = u;
            $.ajax(t[r]["src"], {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: u,
                success: function(t) {
                    var n = im.addFilter(this.myns, t, e[this.namespace]["element"]);
                    im.fire("filterLoad", n);
                }
            });
        }
        for (r in n) {
            var u = "Component_" + r;
            if (!o) o = u;
            var a = 0;
            $.ajax(n[r]["src"], {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: u,
                success: function(t) {
                    var n = im.addComponent(this.myns, t, e[this.namespace]["element"]);
                    im.fire("componentLoad", n);
                }
            });
        }
    });
    im.bind("ChangeActiveAction", function(e) {
        var t = e.eventData.substr(11);
        var n = $("div.controlset[data-namespace=" + t + "]", "div.controls").children("div.control").slideDown().end().children("h4").addClass("active").end();
        $("div.controlset", "div.controls").not(n).children("div.control").slideUp().end().children("h4").removeClass("active");
    });
    window.c5_image_editor = im;
    return im;
};

$("div.controlset").find("div.control").slideUp(0);

$("div.controlset").find("h4").click(function() {
    $("div.controlset").find("h4").not($(this)).removeClass("active");
    var e = $(this).parent().attr("data-namespace");
    im.trigger("ChangeActiveAction", "ControlSet_" + e);
});

$.fn.ImageEditor = function(e) {
    e === undefined && (e = {});
    e.imageload = $.fn.dialog.hideLoader;
    var t = $(this);
    e.container = t[0];
    if (t.height() == 0) {
        setTimeout(function() {
            t.ImageEditor(e);
        }, 50);
        return;
    }
    e.width === undefined && (e.width = t.width());
    e.height === undefined && (e.height = t.height());
    $.fn.dialog.showLoader();
    var n = new ImageEditor(e);
    n.bind("imageload", $.fn.dialog.hideLoader);
    return n;
};

ImageEditor.fn = ImageEditor.prototype;

ImageEditor.fn.filters = {};

ImageEditor.fn.filters.grayscale = Kinetic.Filters.Grayscale;

ImageEditor.fn.filters.sepia = function(e) {
    var t;
    var n = e.data;
    for (t = 0; t < n.length; t += 4) {
        n[t] = n[t] * .393 + n[t + 1] * .769 + n[t + 2] * .189;
        n[t + 1] = n[t] * .349 + n[t + 1] * .686 + n[t + 2] * .168;
        n[t + 2] = n[t] * .272 + n[t + 1] * .534 + n[t + 2] * .131;
    }
};

ImageEditor.fn.filters.brightness = function(e, t) {
    var n = t.level;
    var r = e.data;
    for (var i = 0; i < r.length; i += 4) {
        r[i] += n;
        r[i + 1] += n;
        r[i + 2] += n;
    }
};

ImageEditor.fn.filters.restore = function(e, t) {
    var n = t.level;
    var r = e.data;
    var i = t.imageData.data;
    for (var s = 0; s < r.length; s += 4) {
        r[s] = i[s];
        r[s + 1] = i[s + 1];
        r[s + 2] = i[s + 2];
    }
};