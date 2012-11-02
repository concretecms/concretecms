Kinetic.Stage.prototype.createCopy = function() {
    var a = [], b = this.getChildren(), c;
    for (c = 0; c < b.length; c++) {
        a.push(b[c].clone());
    }
    return a;
};

Kinetic.Stage.prototype.loadCopy = function(a) {
    var b;
    this.removeChildren();
    for (b = 0; b < a.length; b++) {
        this.add(a[b]);
    }
    this.draw();
};

var ImageEditor = function(a) {
    if (a === undefined) return this;
    var b = this, c;
    b.width = a.width;
    b.height = a.height;
    b.stage = new Kinetic.Stage(a);
    b.editor = new Kinetic.Layer;
    b.namespaces = {};
    b.center = {
        x: b.width / 2,
        y: b.height / 2
    };
    var d = function() {
        var a = this;
        a.history = [];
        a.pointer = -1;
        a.save = function() {
            b.fire("beforehistorysave");
            a.history = a.history.slice(0, a.pointer + 1);
            a.history.push(b.stage.createCopy());
            a.movePointer(1);
            b.fire("historysave");
        };
        a.movePointer = function(b) {
            a.pointer += b;
            a.pointer < 0 && (a.pointer = 0);
            a.pointer >= a.history.length && (a.pointer = a.history.length - 1);
            return a.pointer;
        };
        a.render = function() {
            b.fire("beforehistoryrender");
            b.stage.loadCopy(a.history[a.pointer]);
            b.fire("historyrender");
        };
        a.undo = function() {
            b.fire("beforehistoryundo");
            a.movePointer(-1);
            a.render();
            b.fire("historyundo");
        };
        a.redo = function() {
            b.fire("beforehistoryredo");
            a.movePointer(1);
            a.render();
            b.fire("historyredo");
        };
    };
    b.history = new d;
    b.bindEvent = b.bind = function(a, c, d) {
        var e = d || b.stage.getContainer();
        if (e.addEventListener) {
            e.addEventListener(a.toLowerCase(), c, false);
        } else {
            e.attachEvent("on" + a.toLowerCase(), c);
        }
    };
    b.fireEvent = b.fire = b.trigger = function(a, c) {
        var d, e = "ImageEditorEvent", f = b.stage.getContainer(), c = c || b;
        if (document.createEvent) {
            d = document.createEvent("HTMLEvents");
            d.initEvent(a.toLowerCase(), true, true);
        } else {
            d = document.createEventObject();
            d.eventType = a.toLowerCase();
        }
        d.eventName = e;
        d.memo = c || {};
        if (document.createEvent) {
            f.dispatchEvent(d);
        } else {
            f.fireEvent("on" + d.eventType, d);
        }
        if (typeof f["on" + a.toLowerCase()] === "function") {
            f["on" + a.toLowerCase()](d);
        }
    };
    b.extend = function(a, c) {
        b[a] = c;
    };
    b.alterCore = function(a, b) {
        var c = c, d = "core", e;
        if (c.namespace) {
            var d = c.namespace;
            c = window.c5_image_editor;
        }
        c[a] = b;
        for (e in c.namespaces) {
            c.namespaces[e][a] = b;
        }
    };
    b.clone = function(a) {
        var c = new ImageEditor, d;
        for (d in b) {
            c[d] = b[d];
        }
        c.namespace = a;
        namespaces["namespace"] = c;
        return c;
    };
    b.background = new Kinetic.Layer;
    b.background.add(new Kinetic.Rect({
        x: 0,
        y: 0,
        width: b.stage.getWidth(),
        height: b.stage.getHeight(),
        fill: "#eee"
    }));
    var e = function(a, b) {
        return {
            x: 2 * a,
            y: -a + b
        };
    };
    var f = Math.max(b.stage.getWidth(), b.stage.getHeight()) * 2;
    for (c = -10; c <= f; c += 20) {
        b.background.add(new Kinetic.Line({
            points: [ e(c, 0), e(b.background.getWidth(), c) ],
            stroke: "#e3e3e3"
        }));
    }
    b.stage.add(b.background);
    var g = new Image;
    g.src = a.src;
    g.onload = function() {
        var a = {
            x: b.center.x - g.width / 2,
            y: b.center.y - g.width / 2
        };
        b.prettifier = new Kinetic.Layer;
        b.Image = new Kinetic.Image({
            image: g,
            x: a.x,
            y: a.y
        });
        b.Image.on("draw", function() {
            b.fire("imagedraw");
        });
        b.editor.add(b.Image);
        b.stage.add(b.editor);
        b.fireEvent("imageload");
    };
    window.c5_image_editor = b;
    return b;
};

$.fn.ImageEditor = function(a) {
    a === undefined && (a = {});
    a.imageload = $.fn.dialog.hideLoader;
    var b = $(this);
    a.container = b[0];
    a.width === undefined && (a.width = b.width());
    a.height === undefined && (a.height = b.height());
    $.fn.dialog.showLoader();
    var c = new ImageEditor(a);
    c.bind("imageload", $.fn.dialog.hideLoader);
    return c;
};

ImageEditor.fn = ImageEditor.prototype;

ImageEditor.fn.filters = {};

ImageEditor.fn.filters.grayscale = Kinetic.Filters.Grayscale;

ImageEditor.fn.filters.sepia = function(a) {
    var b;
    var c = a.data;
    for (b = 0; b < c.length; b += 4) {
        c[b] = c[b] * .393 + c[b + 1] * .769 + c[b + 2] * .189;
        c[b + 1] = c[b] * .349 + c[b + 1] * .686 + c[b + 2] * .168;
        c[b + 2] = c[b] * .272 + c[b + 1] * .534 + c[b + 2] * .131;
    }
};