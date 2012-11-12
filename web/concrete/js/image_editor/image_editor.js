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

var ImageEditor = function(e) {
    if (e === undefined) return this;
    var t = this, n;
    t.width = e.width;
    t.height = e.height;
    t.stage = new Kinetic.Stage(e);
    t.editor = new Kinetic.Layer;
    t.namespaces = {};
    t.center = {
        x: t.width / 2,
        y: t.height / 2
    };
    var r = function() {
        var e = this;
        e.history = [];
        e.pointer = -1;
        e.save = function() {
            t.fire("beforehistorysave");
            e.history = e.history.slice(0, e.pointer + 1);
            e.history.push(t.stage.createCopy());
            e.movePointer(1);
            t.fire("historysave");
        };
        e.movePointer = function(t) {
            e.pointer += t;
            e.pointer < 0 && (e.pointer = 0);
            e.pointer >= e.history.length && (e.pointer = e.history.length - 1);
            return e.pointer;
        };
        e.render = function() {
            t.fire("beforehistoryrender");
            t.stage.loadCopy(e.history[e.pointer]);
            t.fire("historyrender");
        };
        e.undo = function() {
            t.fire("beforehistoryundo");
            e.movePointer(-1);
            e.render();
            t.fire("historyundo");
        };
        e.redo = function() {
            t.fire("beforehistoryredo");
            e.movePointer(1);
            e.render();
            t.fire("historyredo");
        };
    };
    t.history = new r;
    t.bindEvent = t.bind = function(e, n, r) {
        var i = r || t.stage.getContainer();
        if (i.addEventListener) {
            i.addEventListener(e.toLowerCase(), n, false);
        } else {
            i.attachEvent("on" + e.toLowerCase(), n);
        }
    };
    t.fireEvent = t.fire = t.trigger = function(e, n) {
        var r, i = "ImageEditorEvent", s = t.stage.getContainer(), n = n || t;
        if (document.createEvent) {
            r = document.createEvent("HTMLEvents");
            r.initEvent(e.toLowerCase(), true, true);
        } else {
            r = document.createEventObject();
            r.eventType = e.toLowerCase();
        }
        r.eventName = i;
        r.memo = n || {};
        if (document.createEvent) {
            s.dispatchEvent(r);
        } else {
            s.fireEvent("on" + r.eventType, r);
        }
        if (typeof s["on" + e.toLowerCase()] === "function") {
            s["on" + e.toLowerCase()](r);
        }
    };
    t.extend = function(e, n) {
        t[e] = n;
    };
    t.alterCore = function(e, t) {
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
    t.clone = function(e) {
        var n = new ImageEditor, r;
        for (r in t) {
            n[r] = t[r];
        }
        n.namespace = e;
        namespaces["namespace"] = n;
        return n;
    };
    t.background = new Kinetic.Layer;
    t.background.add(new Kinetic.Rect({
        x: 0,
        y: 0,
        width: t.stage.getWidth(),
        height: t.stage.getHeight(),
        fill: "#eee"
    }));
    var i = function(e, t) {
        return {
            x: 2 * e,
            y: -e + t
        };
    };
    var s = Math.max(t.stage.getWidth(), t.stage.getHeight()) * 2;
    for (n = -10; n <= s; n += 20) {
        t.background.add(new Kinetic.Line({
            points: [ i(n, 0), i(t.background.getWidth(), n) ],
            stroke: "#e3e3e3"
        }));
    }
    t.stage.add(t.background);
    var o = new Image;
    o.src = e.src;
    o.onload = function() {
        var e = {
            x: t.center.x - o.width / 2,
            y: t.center.y - o.width / 2
        };
        t.prettifier = new Kinetic.Layer;
        t.Image = new Kinetic.Image({
            image: o,
            x: e.x,
            y: e.y
        });
        t.Image.on("draw", function() {
            t.fire("imagedraw");
        });
        t.editor.add(t.Image);
        t.stage.add(t.editor);
        t.fireEvent("imageload");
    };
    window.c5_image_editor = t;
    return t;
};

$.fn.ImageEditor = function(e) {
    e === undefined && (e = {});
    e.imageload = $.fn.dialog.hideLoader;
    var t = $(this);
    e.container = t[0];
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