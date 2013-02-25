/////////////////////////////
//      Kinetic.Stage      //
/////////////////////////////
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

Kinetic.Stage.prototype.getSaveWidth = function() {
    return this.im.saveWidth;
};

Kinetic.Stage.prototype.getSaveHeight = function() {
    return this.im.saveHeight;
};

Kinetic.Stage.prototype.getTotalDimensions = function() {
    var e = (this.getSaveHeight() / 2 - this.im.center.y) * this.getScale().y;
    var t = e + this.getHeight() - this.getSaveHeight() * this.getScale().y;
    var n = (this.getSaveWidth() / 2 - this.im.center.x) * this.getScale().x;
    var r = n + this.getWidth() - this.getSaveWidth() * this.getScale().x;
    return {
        min: {
            x: n,
            y: e
        },
        max: {
            x: r,
            y: t
        },
        width: this.getScaledWidth(),
        height: this.getScaledHeight(),
        visibleWidth: Math.max(this.getSaveWidth(), this.getScaledWidth() * 2 - this.getSaveWidth()),
        visibleHeight: Math.max(this.getSaveHeight(), this.getScaledHeight() * 2 - this.getSaveHeight())
    };
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
    im.namespaces = {};
    im.controlSets = {};
    im.components = {};
    im.filters = {};
    im.scale = 1;
    im.crosshair = new Image;
    im.uniqid = im.stage.getContainer().id;
    im.editorContext = $(im.stage.getContainer()).parent();
    im.domContext = im.editorContext.parent();
    im.showLoader = $.fn.dialog.showLoader;
    im.hideLoader = $.fn.dialog.hideLoader;
    im.stage.im = im;
    im.stage.elementType = "stage";
    im.crosshair.src = "/concrete/images/image_editor/crosshair.png";
    im.center = {
        x: Math.round(im.width / 2),
        y: Math.round(im.height / 2)
    };
    im.centerOffset = {
        x: im.center.x,
        y: im.center.y
    };
    var getElem = function(e) {
        return $(e, im.domContext);
    }, log = function() {
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
    im.addElement = function(e, t) {
        var n = new Kinetic.Layer;
        n.add(e);
        e.setX(im.center.x - Math.round(e.getWidth() / 2));
        e.setY(im.center.y - Math.round(e.getHeight() / 2));
        e.elementType = t;
        e.on("click", function() {
            im.fire("ClickedElement", this);
        });
        im.stage.add(n);
        im.fire("newObject", {
            object: e,
            type: t
        });
        im.stage.draw();
    };
    im.setActiveElement = function(e) {
        if (im.activeElement == e) return;
        if (e === im.stage) {
            im.trigger("ChangeActiveAction", "ControlSet_Position");
        }
        im.trigger("beforeChangeActiveElement", im.activeElement);
        im.alterCore("activeElement", e);
        im.trigger("changeActiveElement", e);
        im.stage.draw();
    };
    im.bind("ClickedElement", function(e) {
        if (e.eventData.getWidth() > im.stage.getScaledWidth() || e.eventData.getHeight() > im.stage.getScaledHeight()) {
            im.setActiveElement(im.stage);
            return;
        }
        im.setActiveElement(e.eventData);
    });
    im.bind("stageChanged", function(e) {
        if (im.activeElement.getWidth() > im.stage.getScaledWidth() || im.activeElement.getHeight() > im.stage.getScaledHeight()) {
            im.setActiveElement(im.stage);
        }
    });
    var controlBar = getElem(im.stage.getContainer()).parent().children(".bottomBar");
    var zoom = {};
    zoom.in = getElem("<span><i class='icon-plus'></i></span>");
    zoom.out = getElem("<span><i class='icon-minus'></i></span>");
    zoom.in.appendTo(controlBar);
    zoom.out.appendTo(controlBar);
    zoom.in.click(function(e) {
        im.fire("zoomInClick", e);
    });
    zoom.out.click(function(e) {
        im.fire("zoomOutClick", e);
    });
    var scale = getElem("<span></span>").addClass("scale").text("100%");
    im.on("stageChanged", function(e) {
        scale.text(Math.round(im.scale * 1e4) / 100 + "%");
    });
    scale.appendTo(controlBar);
    var minScale = 0, maxScale = 3e3, stepScale = 1 / 4;
    im.on("zoomInClick", function(e) {
        var t = im.scale * stepScale;
        im.scale += t;
        if (im.scale > stepScale && Math.abs(im.scale - Math.round(im.scale)) % 1 < stepScale / 2) im.scale = Math.round(im.scale);
        im.scale = Math.round(im.scale * 1e3) / 1e3;
        im.alterCore("scale", im.scale);
        im.stage.setScale(im.scale);
        im.stage.setX(im.stage.getX() + -.5 * t * im.stage.getWidth());
        im.stage.setY(im.stage.getY() + -.5 * t * im.stage.getHeight());
        var n = im.stage.getDragBoundFunc()({
            x: im.stage.getX(),
            y: im.stage.getY()
        });
        im.stage.setX(n.x);
        im.stage.setY(n.y);
        im.fire("stageChanged");
        im.stage.draw();
    });
    im.on("zoomOutClick", function(e) {
        var t = im.scale * stepScale;
        im.scale -= t;
        if (im.scale > stepScale && Math.abs(im.scale - Math.round(im.scale)) % 1 < stepScale / 2) im.scale = Math.round(im.scale);
        im.scale = Math.round(im.scale * 1e3) / 1e3;
        im.alterCore("scale", im.scale);
        im.stage.setScale(im.scale);
        im.stage.setX(im.stage.getX() - -.5 * t * im.stage.getWidth());
        im.stage.setY(im.stage.getY() - -.5 * t * im.stage.getHeight());
        var n = im.stage.getDragBoundFunc()({
            x: im.stage.getX(),
            y: im.stage.getY()
        });
        im.stage.setX(n.x);
        im.stage.setY(n.y);
        im.fire("stageChanged");
        im.stage.draw();
    });
    var saveSize = {};
    saveSize.width = getElem("<input/>");
    saveSize.height = getElem("<input/>");
    saveSize.both = saveSize.height.add(saveSize.width).width(32);
    saveSize.area = getElem("<span/>").css({
        "float": "right",
        margin: "-5px 14px 0 0"
    });
    saveSize.width.appendTo(saveSize.area);
    saveSize.area.append(getElem("<span> x </span>"));
    saveSize.height.appendTo(saveSize.area);
    saveSize.area.appendTo(controlBar);
    var saveButton = $("<button/>").addClass("btn").addClass("btn-primary").text("Save");
    saveButton.appendTo(saveSize.area);
    saveButton.click(function() {
        im.save();
    });
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
        im.buildBackground();
    });
    im.bind("saveSizeChange", function() {
        saveSize.width.val(im.saveWidth);
        saveSize.height.val(im.saveHeight);
    });
    im.save = function() {
        im.background.hide();
        im.stage.setScale(1);
        im.fire("ChangeActiveAction");
        im.fire("changeActiveComponent");
        $(im.stage.getContainer()).hide();
        var e = Math.round(im.center.x - im.saveWidth / 2), t = Math.round(im.center.y - im.saveHeight / 2), n = im.stage.getX(), r = im.stage.getY(), i = im.stage.getWidth(), s = im.stage.getHeight();
        im.stage.setX(-e);
        im.stage.setY(-t);
        im.stage.setWidth(Math.max(im.stage.getWidth(), im.saveWidth));
        im.stage.setHeight(Math.max(im.stage.getHeight(), im.saveHeight));
        im.stage.draw();
        im.showLoader("Saving..");
        im.stage.toDataURL({
            width: im.saveWidth,
            height: im.saveHeight,
            callback: function(e) {
                var t = $("<img/>").attr("src", e);
                $.fn.dialog.open({
                    element: t
                });
                im.hideLoader();
                im.background.show();
                im.stage.setX(n);
                im.stage.setY(r);
                im.stage.setWidth(i);
                im.stage.setHeight(s);
                im.stage.setScale(im.scale);
                im.stage.draw();
                $(im.stage.getContainer()).show();
            }
        });
    };
    im.extend = function(e, t) {
        this[e] = t;
    };
    im.alterCore = function(e, t) {
        var n = im, r = "core", i;
        if (im.namespace) {
            var r = n.namespace;
            n = im.realIm;
        }
        im[e] = t;
        for (i in im.controlSets) {
            im.controlSets[i].im.extend(e, t);
        }
        for (i in im.filters) {
            im.filters[i].im.extend(e, t);
        }
        for (i in im.components) {
            im.components[i].im.extend(e, t);
        }
    };
    im.clone = function(e) {
        var t = new ImageEditor, n;
        t.realIm = im;
        for (n in im) {
            t[n] = im[n];
        }
        t.namespace = e;
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
        var e = im.background.getZIndex();
        im.background.destroy();
        im.background = new Kinetic.Layer;
        im.stage.add(im.background);
        im.background.setZIndex(e);
        var t = function(e, t) {
            return {
                x: e,
                y: -e + t
            };
        };
        var n = im.stage.getTotalDimensions();
        var r = n.max.x + n.visibleHeight + n.visibleWidth;
        im.totalBackground = new Kinetic.Rect({
            x: n.max.x - n.width,
            y: n.max.y - n.height,
            width: r,
            height: r,
            fill: "#aaa"
        });
        im.saveArea = new Kinetic.Rect({
            width: im.saveWidth,
            height: im.saveHeight,
            x: Math.floor(im.center.x - im.saveWidth / 2),
            y: Math.floor(im.center.y - im.saveHeight / 2),
            fill: "white"
        });
        im.background.add(im.totalBackground);
        im.background.add(im.saveArea);
        if (im.scale < .25) return;
        var r = n.max.x + n.visibleHeight + n.visibleWidth;
        if (im.scale > 1) r *= im.scale;
        for (x = -(n.max.x + n.visibleHeight); x <= r; x += 20) {
            im.background.add(new Kinetic.Line({
                points: [ t(-r, x), t(r, x) ],
                stroke: "#e3e3e3"
            }));
        }
        im.background.draw();
    };
    im.buildBackground();
    im.on("stageChanged", im.buildBackground);
    im.stage.setDragBoundFunc(function(e) {
        var t = im.stage.getTotalDimensions();
        var n = Math.max(t.max.x, t.min.x) - 1, r = Math.min(t.max.x, t.min.x) + 1, i = Math.max(t.max.y, t.min.y) - 1, s = Math.min(t.max.y, t.min.y) + 1;
        e.x = Math.floor(e.x);
        e.y = Math.floor(e.y);
        if (e.x > n) e.x = n;
        if (e.x < r) e.x = r;
        if (e.y > i) e.y = i;
        if (e.y < s) e.y = s;
        e.x = Math.floor(e.x);
        e.y = Math.floor(e.y);
        return e;
    });
    im.setActiveElement(im.stage);
    im.stage.setDraggable(true);
    var img = new Image;
    img.src = settings.src;
    img.onload = function() {
        if (!im.strictSize) {
            im.saveWidth = img.width;
            im.saveHeight = img.height;
            im.fire("saveSizeChange");
            im.buildBackground();
        }
        var e = {
            x: im.center.x - img.width / 2,
            y: im.center.y - img.height / 2
        };
        im.image = new Kinetic.Image({
            image: img,
            x: Math.round(e.x),
            y: Math.round(e.y)
        });
        im.imageData = im.image.getImageData();
        im.fire("imageload");
        im.addElement(im.image, "image");
    };
    im.bind("imageload", function() {
        var e = settings.controlsets || {}, t = settings.filters || {}, n, r;
        var i = 0;
        log("Loading ControlSets");
        im.fire("LoadingControlSets");
        for (n in e) {
            var s = "ControlSet_" + n;
            $.ajax(e[n]["src"], {
                dataType: "text",
                cache: false,
                namespace: n,
                myns: s,
                beforeSend: function() {
                    i++;
                },
                success: function(t) {
                    i--;
                    var n = im.addControlSet(this.myns, t, e[this.namespace]["element"]);
                    log(n);
                    im.fire("controlSetLoad", n);
                    if (0 == i) {
                        im.trigger("ControlSetsLoaded");
                    }
                },
                error: function(e, t, n) {
                    i--;
                    if (0 == i) {
                        im.trigger("ControlSetsLoaded");
                    }
                }
            });
        }
    });
    im.bind("ControlSetsLoaded", function() {
        im.fire("LoadingComponents");
        var e = settings.components || {}, t, n = 0;
        log("Loading Components");
        for (t in e) {
            var r = "Component_" + t;
            $.ajax(e[t]["src"], {
                dataType: "text",
                cache: false,
                namespace: t,
                myns: r,
                beforeSend: function() {
                    n++;
                },
                success: function(t) {
                    n--;
                    var r = im.addComponent(this.myns, t, e[this.namespace]["element"]);
                    log(r);
                    im.fire("ComponentLoad", r);
                    if (0 == n) {
                        im.trigger("ComponentsLoaded");
                    }
                },
                error: function(e, t, r) {
                    n--;
                    if (0 == n) {
                        im.trigger("ComponentsLoaded");
                    }
                }
            });
        }
    });
    im.bind("ComponentsLoaded", function() {
        log("Loading Filters");
        var e = settings.filters || {}, t, n, r;
        im.fire("LoadingFilters");
        for (t in e) {
            var i = "Filter_" + t;
            var s = e[t].name;
            if (!n) n = i;
            $.ajax(e[t].src, {
                dataType: "text",
                cache: false,
                namespace: t,
                myns: i,
                name: s,
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
            if (n !== t) getElem(im.controlSets[n]).slideUp();
        }
        im.activeControlSet = t;
        im.alterCore("activeControlSet", t);
        if (!t) return;
        var r = $(im.controlSets[t]), i = r.show().height();
        if (r.length == 0) return;
        r.hide().height(i).slideDown(function() {
            $(this).height("");
        });
    });
    im.bind("ChangeActiveComponent", function(e) {
        var t = e.eventData;
        if (t === im.activeComponent) return;
        for (var n in im.components) {
            if (n !== t) getElem(im.components[n]).slideUp();
        }
        im.activeComponent = t;
        im.alterCore("activeComponent", t);
        if (!t) return;
        var r = $(im.components[t]), i = r.show().height();
        if (r.length == 0) return;
        r.hide().height(i).slideDown(function() {
            $(this).height("");
        });
    });
    im.bind("ChangeNavTab", function(e) {
        console.log("changenavtab", e);
        im.trigger("ChangeActiveAction", e.eventData);
        im.trigger("ChangeActiveComponent", e.eventData);
        var t = getElem("div.editorcontrols");
        switch (e.eventData) {
          case "add":
            t.children("div.control-sets").hide();
            t.children("div.components").show();
            break;
          case "edit":
            t.children("div.components").hide();
            t.children("div.control-sets").show();
            break;
        }
    });
    im.setActiveElement(im.stage);
    window.c5_image_editor = im;
    window.im = im;
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
    t.height(t.height() - 31);
    e.width === undefined && (e.width = t.width());
    e.height === undefined && (e.height = t.height());
    $.fn.dialog.showLoader();
    var n = new ImageEditor(e);
    var r = n.domContext;
    $("div.controls", r).children("ul.nav").children().click(function() {
        if ($(this).hasClass("active")) return false;
        $("div.controls", r).children("ul.nav").children().removeClass("active");
        $(this).addClass("active");
        n.trigger("ChangeNavTab", $(this).text().toLowerCase());
        return false;
    });
    $("div.controlset", r).find("div.control").children("div.contents").slideUp(0).end().end().find("h4").click(function() {
        $("div.controlset", r).find("h4").not($(this)).removeClass("active");
        var e = $(this).parent().attr("data-namespace");
        n.trigger("ChangeActiveAction", "ControlSet_" + e);
    });
    $("div.component", r).find("div.control").children("div.contents").slideUp(0).hide().end().end().find("h4").click(function() {
        $("div.component", r).children("h4").not($(this)).removeClass("active");
        var e = $(this).parent().attr("data-namespace");
        n.trigger("ChangeActiveComponent", "Component_" + e);
    });
    $("div.components").hide();
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