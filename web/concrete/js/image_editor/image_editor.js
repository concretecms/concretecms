Kinetic.Stage.prototype.createCopy = function() {
    var e = [], t = this.getChildren(), n;
    for (n = 0; n < t.length; n++) {
        e.push(t[n].clone());
    }
    return e;
};

Kinetic.Stage.prototype.getScaledWidth = function() {
    return Math.ceil(this.getWidth() / this.getScale().x);
};

Kinetic.Stage.prototype.getScaledHeight = function() {
    return Math.ceil(this.getHeight() / this.getScale().y);
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

Kinetic.Text.prototype.rasterize = function(e) {
    var t = this.parent;
    var n = this;
    this.toImage({
        callback: function(r) {
            var i = new Kinetic.Image({
                image: r,
                x: n.getPosition().x,
                y: n.getPosition().y
            });
            n.remove();
            t.add(i).draw();
            e.callback(i);
        }
    });
};

var ImageEditor = function(settings) {
    "use strict";
    if (settings === undefined) return this;
    var im = this, x, round = function(e) {
        return Math.round(e);
    };
    im.width = settings.width;
    im.height = settings.height;
    im.saveWidth = settings.saveWidth || round(im.width / 2);
    im.saveHeight = settings.saveHeight || round(im.height / 2);
    im.strictSize = settings.saveWidth !== undefined ? true : false;
    im.stage = new Kinetic.Stage(settings);
    im.editor = new Kinetic.Layer;
    im.namespaces = {};
    im.controlSets = {};
    im.components = {};
    im.filters = {};
    im.scale = 1;
    im.crosshair = new Image;
    im.crosshair.src = "/concrete/images/image_editor/crosshair.png";
    im.center = {
        x: im.width / 2,
        y: im.height / 2
    };
    var log = function() {
        if (settings.debug === true && console !== undefined) {
            var e = arguments;
            if (e.length == 1) e = e[0];
            console.log(e);
        }
    }, error = function() {
        if (console !== undefined) {
            var e = arguments;
            if (e.length == 1) e = e[0];
            console.error(e);
        }
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
    im.bindEvent = im.bind = im.on = function(e, t, n) {
        var r = n || im.stage.getContainer();
        ccm_event.sub(e, t, r);
    };
    im.fireEvent = im.fire = im.trigger = function(e, t, n) {
        var r = im.stage.getContainer() || n;
        ccm_event.pub(e, t, r);
    };
    var controlBar = $(im.stage.getContainer()).parent().children(".bottomBar");
    var zoom = {};
    zoom.in = $("<span><i class='icon-plus'></i></span>");
    zoom.out = $("<span><i class='icon-minus'></i></span>");
    zoom.in.appendTo(controlBar);
    zoom.out.appendTo(controlBar);
    zoom.in.click(function(e) {
        im.fire("zoomInClick", e);
    });
    zoom.out.click(function(e) {
        im.fire("zoomOutClick", e);
    });
    var minScale = 1 / 3, maxScale = 3, stepScale = 1 / 3;
    im.stage.setDraggable();
    im.on("zoomInClick", function(e) {
        im.scale += stepScale;
        if (im.scale > maxScale) im.scale = maxScale;
        if (Math.abs(im.scale - Math.round(im.scale)) < stepScale / 2) im.scale = Math.round(im.scale);
        im.stage.setScale(im.scale);
        im.fire("stageChanged");
        im.stage.draw();
    });
    im.on("zoomOutClick", function(e) {
        im.scale -= stepScale;
        if (im.scale < minScale) im.scale = minScale;
        if (Math.abs(im.scale - Math.round(im.scale)) < stepScale / 2) im.scale = Math.round(im.scale);
        im.stage.setScale(im.scale);
        im.fire("stageChanged");
        im.stage.draw();
    });
    var saveSize = {};
    saveSize.width = $("<input/>");
    saveSize.height = $("<input/>");
    saveSize.both = saveSize.height.add(saveSize.width).width(32);
    saveSize.area = $("<span/>").css({
        "float": "right",
        margin: "-5px 14px 0 0"
    });
    saveSize.width.appendTo(saveSize.area);
    saveSize.area.append($("<span> x </span>"));
    saveSize.height.appendTo(saveSize.area);
    saveSize.area.appendTo(controlBar);
    if (im.strictSize) {
        saveSize.both.attr("disabled", "true");
    } else {
        saveSize.both.keydown(function(e) {
            log(e.keyCode);
            if (e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39) return true;
            if (e.keyCode == 38) {
                var t = parseInt($(this).val()) + 1;
                $(this).val(Math.min(5e3, t)).change();
            }
            if (e.keyCode == 40) {
                var t = parseInt($(this).val()) - 1;
                $(this).val(Math.max(0, t)).change();
            }
            var n = String.fromCharCode(e.keyCode);
            if (!n.match(/\d/)) {
                return false;
            }
            var r = "" + $(this).val() + n;
            if (r > 5e3) {
                r = 5e3;
            }
            $(this).val(r).change();
            return false;
        }).keyup(function(e) {
            if (e.keyCode == 8) im.fire("editedSize");
        }).change(function() {
            im.fire("editedSize");
        });
    }
    im.bind("editedSize", function() {
        im.saveWidth = parseInt(saveSize.width.val());
        im.saveHeight = parseInt(saveSize.height.val());
        if (isNaN(im.saveWidth)) im.saveWidth = 0;
        if (isNaN(im.saveHeight)) im.saveHeight = 0;
        im.trigger("saveSizeChange");
        im.adjustSavers();
    });
    im.bind("saveSizeChange", function() {
        saveSize.width.val(im.saveWidth);
        saveSize.height.val(im.saveHeight);
    });
    im.save = function() {
        var e = im.stage.getScale();
        im.stage.setScale(1);
        var t = im.image.getX() - im.dragger.getX(), n = im.image.getY() - im.dragger.getY();
        im.image.setX(t);
        im.image.setY(n);
        im.image.disableStroke();
        im.image.toImage({
            width: im.width,
            height: im.height,
            callback: function(t) {
                im.image.enableStroke();
                im.image.setImage(t);
                im.image.setX(im.dragger.getX());
                im.image.setY(im.dragger.getY());
                im.stage.setScale(e);
                im.stage.draw();
            }
        });
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
            try {
                eval(js);
            } catch (e) {
                error(e);
                var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                var jsstack = js.split("\n");
                var error = "Parse error at line #" + pos[0] + " char #" + pos[1] + " within " + ns;
                error += "\n" + jsstack[parseInt(pos[0]) - 1];
                error += "\n" + (new Array(parseInt(pos[1]))).join(" ") + "^";
                error(error);
            }
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
    im.addComponent = function(ns, js, elem) {
        if (jQuery && elem instanceof jQuery) elem = elem[0];
        elem.component = function(im, js) {
            this.im = im;
            try {
                eval(js);
            } catch (e) {
                error(e);
                var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                var jsstack = js.split("\n");
                var error = "Parse error at line #" + pos[0] + " char #" + pos[1] + " within " + ns;
                error += "\n" + jsstack[parseInt(pos[0]) - 1];
                error += "\n" + (new Array(parseInt(pos[1]))).join(" ") + "^";
                error(error);
            }
            return this;
        };
        var newim = im.clone(ns);
        var nso = elem.component(newim, js);
        im.components[ns] = nso;
        return nso;
    };
    im.background = new Kinetic.Layer;
    im.stage.add(im.background);
    im.buildBackground = function() {
        var e, t = im.background.getChildren();
        for (var e in t) {
            t[e].remove();
        }
        im.background.add(new Kinetic.Rect({
            x: 0,
            y: 0,
            width: im.stage.getScaledWidth(),
            height: im.stage.getScaledHeight(),
            fill: "#eee"
        }));
        var n = function(e, t) {
            return {
                x: 2 * e,
                y: -e + t
            };
        };
        var r = Math.max(im.stage.getScaledWidth(), im.stage.getScaledHeight()) * 2;
        for (x = -r; x <= r; x += 20) {
            im.background.add(new Kinetic.Line({
                points: [ n(x, 0), n(im.background.getWidth(), x) ],
                stroke: "#e3e3e3"
            }));
        }
        im.background.draw();
    };
    im.buildBackground();
    im.on("stageChanged", im.buildBackground);
    var me = $(this);
    im.savers = new Kinetic.Layer;
    var savercolor = "rgba(0,0,0,.7)", saverTopLeft = new Kinetic.Rect({
        x: 0,
        y: 0,
        fill: savercolor,
        width: Math.floor(im.stage.getScaledWidth() / 2),
        height: Math.floor(im.stage.getScaledHeight() / 2)
    }), saverBottomLeft = new Kinetic.Rect({
        x: 0,
        y: Math.floor(im.stage.getScaledHeight() / 2),
        fill: savercolor,
        width: Math.floor(im.stage.getScaledWidth() / 2),
        height: Math.ceil(im.stage.getScaledHeight() / 2)
    }), saverTopRight = new Kinetic.Rect({
        x: Math.floor(im.stage.getScaledWidth() / 2),
        y: 0,
        fill: savercolor,
        width: Math.ceil(im.stage.getScaledWidth() / 2),
        height: Math.floor(im.stage.getScaledHeight() / 2)
    }), saverBottomRight = new Kinetic.Rect({
        x: Math.floor(im.stage.getScaledWidth() / 2),
        y: Math.floor(im.stage.getScaledHeight() / 2),
        fill: savercolor,
        width: Math.ceil(im.stage.getScaledWidth() / 2),
        height: Math.ceil(im.stage.getScaledHeight() / 2)
    });
    im.adjustSavers = function() {
        log("Adjusting");
        var e = Math.round(im.center.x - im.saveWidth / 2), t = e + im.saveWidth, n = Math.round(im.center.y - im.saveHeight / 2), r = n + im.saveHeight;
        if (r < n) {
            var i = r;
            r = n;
            n = i;
        }
        if (t < e) {
            var i = t;
            t = e;
            e = i;
        }
        saverTopLeft.setWidth(e);
        saverTopLeft.setHeight(r);
        saverTopRight.setX(e);
        saverTopRight.setWidth(im.stage.getScaledWidth() - e);
        saverTopRight.setHeight(n);
        saverBottomLeft.setWidth(t);
        saverBottomLeft.setY(r);
        saverBottomLeft.setHeight(im.stage.getScaledHeight() - r);
        saverBottomRight.setY(n);
        saverBottomRight.setX(t);
        saverBottomRight.setWidth(im.stage.getScaledWidth() - t);
        saverBottomRight.setHeight(im.stage.getScaledHeight() - n);
        im.fire("saveSizeChange");
        im.savers.draw();
    };
    im.savers.add(saverTopLeft);
    im.savers.add(saverTopRight);
    im.savers.add(saverBottomLeft);
    im.savers.add(saverBottomRight);
    im.stage.add(im.savers);
    im.adjustSavers();
    var img = new Image;
    img.src = settings.src;
    img.onload = function() {
        if (!im.strictSize) {
            im.saveWidth = img.width;
            im.saveHeight = img.height;
            im.adjustSavers();
        }
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
        im.fire("imageload");
    };
    im.bind("imageload", function() {
        var e = settings.controlsets || {}, t = settings.filters || {}, n = settings.components || {}, r, i;
        var s = 0;
        im.fire("LoadingControlSets");
        for (r in e) {
            var o = "ControlSet_" + r;
            $.ajax(e[r]["src"], {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: o,
                beforeSend: function() {
                    s++;
                },
                success: function(t) {
                    s--;
                    var n = im.addControlSet(this.myns, t, e[this.namespace]["element"]);
                    log(n);
                    im.fire("controlSetLoad", n);
                    if (0 == s) {
                        im.trigger("ControlSetsLoaded");
                    }
                },
                error: function(e, t, n) {
                    s--;
                    if (0 == s) {
                        im.trigger("ControlSetsLoaded");
                    }
                }
            });
        }
        im.fire("LoadingComponents");
        for (r in n) {
            var o = "Component_" + r;
            $.ajax(n[r]["src"], {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: o,
                beforeSend: function() {
                    s++;
                },
                success: function(t) {
                    s--;
                    var n = im.addControlSet(this.myns, t, e[this.namespace]["element"]);
                    log(n);
                    im.fire("controlSetLoad", n);
                    if (0 == s) {
                        im.trigger("ComponentsLoaded");
                    }
                },
                error: function(e, t, n) {
                    s--;
                    if (0 == s) {
                        im.trigger("ComponentsLoaded");
                    }
                }
            });
        }
    });
    im.bind("ControlSetsLoaded", function() {
        log("Loaded");
        var e = settings.filters || {}, t = settings.components || {}, n, r, i;
        im.fire("LoadingFilters");
        for (n in e) {
            var s = "Filter_" + n;
            var o = e[n].name;
            if (!r) r = s;
            $.ajax(e[n].src, {
                dataType: "text",
                cache: false,
                namespace: n,
                myns: s,
                name: o,
                success: function(e) {
                    var t = im.addFilter(this.myns, e);
                    t.name = this.name;
                    im.fire("filterLoad", t);
                }
            });
        }
    });
    im.bind("ChangeActiveAction", function(e) {
        var t = e.eventData;
        if (t === im.activeControlSet) return;
        for (var n in im.controlSets) {
            if (n !== t) $(im.controlSets[n]).slideUp();
        }
        im.activeControlSet = t;
        if (!t) return;
        var r = $(im.controlSets[t]), i = r.show().height();
        r.hide().height(i).slideDown(function() {
            $(this).height("");
        });
    });
    im.bind("ChangeNavTab", function(e) {
        im.trigger("ChangeActiveAction");
        switch (e.eventData) {
          case "add":
        }
    });
    window.c5_image_editor = im;
    return im;
};

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
    $("div.controls").children("ul.nav").children().click(function() {
        if ($(this).hasClass("active")) return false;
        n.trigger("ChangeNavTab", $(this).text().toLowerCase());
        return false;
    });
    $("div.controlset").find("div.control").children("div.contents").slideUp(0);
    $("div.controlset").find("h4").click(function() {
        $("div.controlset").find("h4").not($(this)).removeClass("active");
        var e = $(this).parent().attr("data-namespace");
        n.trigger("ChangeActiveAction", "ControlSet_" + e);
    });
    n.bind("imageload", $.fn.dialog.hideLoader);
    return n;
};

ImageEditor.prototype.filter = {};

ImageEditor.prototype.filter.grayscale = Kinetic.Filters.Grayscale;

ImageEditor.prototype.filter.sepia = function(e) {
    var t;
    var n = e.data;
    for (t = 0; t < n.length; t += 4) {
        n[t] = n[t] * .393 + n[t + 1] * .769 + n[t + 2] * .189;
        n[t + 1] = n[t] * .349 + n[t + 1] * .686 + n[t + 2] * .168;
        n[t + 2] = n[t] * .272 + n[t + 1] * .534 + n[t + 2] * .131;
    }
};

ImageEditor.prototype.filter.brightness = function(e, t) {
    var n = t.level;
    var r = e.data;
    for (var i = 0; i < r.length; i += 4) {
        r[i] += n;
        r[i + 1] += n;
        r[i + 2] += n;
    }
};

ImageEditor.prototype.filter.restore = function(e, t) {
    var n = t.level;
    var r = e.data;
    var i = t.imageData.data;
    for (var s = 0; s < r.length; s += 4) {
        r[s] = i[s];
        r[s + 1] = i[s + 1];
        r[s + 2] = i[s + 2];
    }
};