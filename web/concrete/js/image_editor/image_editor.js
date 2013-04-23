/////////////////////////////
//      Kinetic.Node       //
/////////////////////////////
Kinetic.Node.prototype.closest = function(e) {
    var t = this.parent;
    while (t !== undefined) {
        if (t.nodeType === e) return t;
        t = t.parent;
    }
    return false;
};

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

Kinetic.Stage.prototype.elementType = "stage";

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
    var e = this._cacheddraw();
    return e;
};

Kinetic.Layer.prototype.elementType = "layer";

Kinetic.Group.prototype.elementType = "group";

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

Kinetic.Global.extend(Kinetic.Container, Kinetic.Node);

Kinetic.Global.extend(Kinetic.Shape, Kinetic.Node);

Kinetic.Global.extend(Kinetic.Group, Kinetic.Container);

Kinetic.Global.extend(Kinetic.Layer, Kinetic.Container);

Kinetic.Global.extend(Kinetic.Stage, Kinetic.Container);

Kinetic.Global.extend(Kinetic.Circle, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Ellipse, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Image, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Line, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Path, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Polygon, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Rect, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.RegularPolygon, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Sprite, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Star, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Text, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.TextPath, Kinetic.Shape);

Kinetic.Global.extend(Kinetic.Wedge, Kinetic.Shape);

var ImageEditor = function(settings) {
    "use strict";
    if (settings === undefined) return this;
    settings.pixelRatio = 1;
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
    im.settings = settings;
    im.filters = {};
    im.scale = 1;
    im.crosshair = new Image;
    im.uniqid = im.stage.getContainer().id;
    im.editorContext = $(im.stage.getContainer()).parent();
    im.domContext = im.editorContext.parent();
    im.controlContext = im.domContext.children("div.controls");
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
    }, warn = function() {
        if (settings.debug === true && console !== undefined) {
            var e = arguments;
            if (e.length == 1) e = e[0];
            console.warn(e);
        }
    }, error = function() {
        if (console !== undefined) {
            var e = arguments;
            if (e.length == 1) e = e[0];
            console.error(e);
        }
    };
    im.stage._setDraggable = im.stage.setDraggable;
    im.stage.setDraggable = function(e) {
        warn("setting draggable to " + e);
        return im.stage._setDraggable(e);
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
        if (r instanceof jQuery) r = r[0];
        ccm_event.sub(e, t, r);
    };
    im.fireEvent = im.fire = im.trigger = function(e, t, n) {
        var r = n || im.stage.getContainer();
        if (r instanceof jQuery) r = r[0];
        ccm_event.pub(e, t, r);
    };
    im.addElement = function(e, t) {
        var n = new Kinetic.Layer;
        n.elementType = n;
        n.add(e);
        e.setX(im.center.x - Math.round(e.getWidth() / 2));
        e.setY(im.center.y - Math.round(e.getHeight() / 2));
        e.doppelganger = e.clone();
        if (t == "image") e.doppelganger.setImage("");
        e.doppelganger.doppelganger = e;
        e.doppelganger.drawHitFunc = e.doppelganger.attrs.drawHitFunc = function() {
            return false;
        };
        e.doppelganger.setFill("transparent");
        e.doppelganger.elementType = "StokeClone";
        e.doppelganger.setStroke("blue");
        e.doppelganger._drawFunc = e.getDrawFunc();
        e.doppelganger.setDrawFunc(function(e) {
            if (typeof this._drawFunc == "function") {
                this.attrs.strokeWidth = 1 / im.scale;
                this.setFill("transparent");
                if (t == "image") {
                    this.attrs.image = "";
                }
                this._drawFunc(e);
            }
        });
        e.elementType = t;
        e.on("click", function() {
            im.fire("ClickedElement", this);
        });
        e._drawFunc = e.getDrawFunc();
        e.setDrawFunc(function(e) {
            for (var t in this.attrs) {
                if (t == "drawFunc" || t == "drawHitFunc" || t == "strokeWidth" || t == "fill") continue;
                this.doppelganger.attrs[t] = this.attrs[t];
            }
            im.foreground.draw();
            this._drawFunc(e);
        });
        e.on("mouseover", function() {
            this.hovered = true;
            im.setCursor("pointer");
        });
        e.on("mouseout", function() {
            if (this.hovered == true) {
                im.setCursor("");
                this.hovered = false;
            }
        });
        im.stage.add(n);
        im.fire("newObject", {
            object: e,
            type: t
        });
        im.foreground.moveToTop();
        im.stage.draw();
    };
    im.on("backgroundBuilt", function() {
        if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
            im.foreground.add(im.activeElement.doppelganger);
            im.activeElement.doppelganger.setPosition(im.activeElement.getPosition());
        }
    });
    im.setActiveElement = function(e) {
        if (im.activeElement == e) return;
        if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
            im.activeElement.doppelganger.remove();
        }
        if (e === im.stage || e.nodeType == "Stage") {
            im.trigger("ChangeActiveAction", "ControlSet_Position");
            $("div.control-sets", im.controlContext).find("h4.active").removeClass("active");
        } else if (e.doppelganger !== undefined) {
            im.foreground.add(e.doppelganger);
            im.foreground.draw();
        }
        im.trigger("beforeChangeActiveElement", im.activeElement);
        im.alterCore("activeElement", e);
        im.trigger("changeActiveElement", e);
        im.stage.draw();
    };
    im.bind("ClickedElement", function(e) {
        im.setActiveElement(e.eventData);
    });
    im.bind("stageChanged", function(e) {
        if (im.activeElement.getWidth() > im.stage.getScaledWidth() || im.activeElement.getHeight() > im.stage.getScaledHeight()) {
            im.setActiveElement(im.stage);
        }
    });
    var controlBar = getElem(im.stage.getContainer()).parent().children(".bottomBar");
    controlBar.attr("unselectable", "on").css("user-select", "none").on("selectstart", false);
    var zoom = {};
    zoom.in = getElem("<div class='bottombarbutton plus'><i class='icon-plus'></i></div>");
    zoom.out = getElem("<div class='bottombarbutton'><i class='icon-minus'></i></div>");
    zoom.in.appendTo(controlBar);
    zoom.out.appendTo(controlBar);
    zoom.in.click(function(e) {
        im.fire("zoomInClick", e);
    });
    zoom.out.click(function(e) {
        im.fire("zoomOutClick", e);
    });
    var scale = getElem("<div></div>").addClass("scale").text("100%");
    im.on("scaleChange", function(e) {
        scale.text(Math.round(im.scale * 1e4) / 100 + "%");
    });
    scale.click(function() {
        im.scale = 1;
        im.stage.setScale(im.scale);
        var e = im.stage.getDragBoundFunc()({
            x: im.stage.getX(),
            y: im.stage.getY()
        });
        im.stage.setX(e.x);
        im.stage.setY(e.y);
        im.fire("scaleChange");
        im.buildBackground();
        im.stage.draw();
    });
    scale.appendTo(controlBar);
    var minScale = 0, maxScale = 3e3, stepScale = 5 / 6;
    im.on("zoomInClick", function(e) {
        var t = (-im.stage.getX() + im.stage.getWidth() / 2) / im.scale, n = (-im.stage.getY() + im.stage.getHeight() / 2) / im.scale;
        im.scale /= stepScale;
        im.scale = Math.round(im.scale * 1e3) / 1e3;
        im.alterCore("scale", im.scale);
        var r = (-im.stage.getX() + im.stage.getWidth() / 2) / im.scale, i = (-im.stage.getY() + im.stage.getHeight() / 2) / im.scale;
        im.stage.setX(im.stage.getX() - (t - r) * im.scale);
        im.stage.setY(im.stage.getY() - (n - i) * im.scale);
        im.stage.setScale(im.scale);
        var s = im.stage.getDragBoundFunc()({
            x: im.stage.getX(),
            y: im.stage.getY()
        });
        im.stage.setX(s.x);
        im.stage.setY(s.y);
        im.fire("scaleChange");
        im.buildBackground();
        im.stage.draw();
    });
    im.on("zoomOutClick", function(e) {
        var t = (-im.stage.getX() + im.stage.getWidth() / 2) / im.scale, n = (-im.stage.getY() + im.stage.getHeight() / 2) / im.scale;
        im.scale *= stepScale;
        im.scale = Math.round(im.scale * 1e3) / 1e3;
        im.alterCore("scale", im.scale);
        var r = (-im.stage.getX() + im.stage.getWidth() / 2) / im.scale, i = (-im.stage.getY() + im.stage.getHeight() / 2) / im.scale;
        im.stage.setX(im.stage.getX() - (t - r) * im.scale);
        im.stage.setY(im.stage.getY() - (n - i) * im.scale);
        im.stage.setScale(im.scale);
        var s = im.stage.getDragBoundFunc()({
            x: im.stage.getX(),
            y: im.stage.getY()
        });
        im.stage.setX(s.x);
        im.stage.setY(s.y);
        im.fire("scaleChange");
        im.buildBackground();
        im.stage.draw();
    });
    var saveSize = {};
    saveSize.width = getElem("<span/>").addClass("saveWidth");
    saveSize.height = getElem("<span/>").addClass("saveHeight");
    saveSize.crop = getElem('<div><i class="icon-resize-full"/></div>').addClass("bottombarbutton").addClass("crop");
    saveSize.both = saveSize.height.add(saveSize.width).width(32).attr("contenteditable", !!1);
    saveSize.area = getElem("<span/>").css({
        "float": "right"
    });
    saveSize.crop.appendTo(saveSize.area);
    saveSize.width.appendTo($("<div>w </div>").addClass("saveWidth").appendTo(saveSize.area));
    saveSize.height.appendTo($("<div>h </div>").addClass("saveHeight").appendTo(saveSize.area));
    saveSize.area.appendTo(controlBar);
    if (im.strictSize) {
        saveSize.both.attr("disabled", "true");
    } else {
        saveSize.both.keyup(function(e) {
            im.fire("editedSize", e);
        });
    }
    im.bind("editedSize", function(e) {
        im.saveWidth = parseInt(saveSize.width.text());
        im.saveHeight = parseInt(saveSize.height.text());
        if (isNaN(im.saveWidth)) im.saveWidth = 0;
        if (isNaN(im.saveHeight)) im.saveHeight = 0;
        im.buildBackground();
    });
    im.bind("saveSizeChange", function() {
        saveSize.width.text(im.saveWidth);
        saveSize.height.text(im.saveHeight);
    });
    im.setCursor = function(e) {
        $(im.stage.getContainer()).css("cursor", e);
    };
    im.save = function() {
        im.background.hide();
        if (im.activeElement !== undefined && typeof im.activeElement.releaseStroke == "function") {
            im.activeElement.releaseStroke();
        }
        im.stage.setScale(1);
        im.setActiveElement(im.stage);
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
                    element: $(t).width(250)
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
            im.disable = function() {
                $(elem).parent().parent().addClass("disabled");
            };
            im.enable = function() {
                $(elem).parent().parent().removeClass("disabled");
            };
            this.im = im;
            warn("Loading ControlSet", im);
            try {
                eval(js);
            } catch (e) {
                console.error(e);
                var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                var jsstack = js.split("\n");
                var error = "Parse error at line #" + pos[0] + " char #" + pos[1] + " within " + ns;
                error += "\n" + jsstack[parseInt(pos[0]) - 1];
                error += "\n" + (new Array(parseInt(pos[1]))).join(" ") + "^";
                console.error(error);
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
            try {
                eval(js);
            } catch (e) {
                console.error(e);
                window.lastError = e;
                var pos = e.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                if (e.count != 2) {
                    console.error(e.message);
                    console.error(e.stack);
                } else {
                    var jsstack = js.split("\n");
                    var error = "Parse error at line #" + pos[0] + " char #" + pos[1] + " within " + ns;
                    console.log(pos);
                    error += "\n" + jsstack[parseInt(pos[0]) - 1];
                    error += "\n" + (new Array(parseInt(pos[1]))).join(" ") + "^";
                    console.error(error);
                }
            }
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
            im.disable = function() {
                $(this).parent().parent().addClass("disabled");
            };
            im.enable = function() {
                $(this).parent().parent().removeClass("disabled");
            };
            this.im = im;
            warn("Loading component", im);
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
    im.foreground = new Kinetic.Layer;
    im.stage.add(im.background);
    im.stage.add(im.foreground);
    im.bgimage = new Image;
    im.bgimage.src = "/concrete/images/testbg.png";
    im.buildBackground = function() {
        var e = (new Date).getTime();
        var t = im.stage.getTotalDimensions();
        var n = (t.max.x + t.visibleHeight + t.visibleWidth) * 2;
        if (!im.saveArea) {
            im.saveArea = new Kinetic.Rect({
                width: im.saveWidth,
                height: im.saveHeight,
                fillPatternImage: im.bgimage,
                fillPatternOffset: [ -(im.saveWidth / 2), -(im.saveHeight / 2) ],
                fillPatternScale: 1 / im.scale,
                fillPatternX: 0,
                fillPatternY: 0,
                fillPatternRepeat: "repeat",
                x: Math.floor(im.center.x - im.saveWidth / 2),
                y: Math.floor(im.center.y - im.saveHeight / 2)
            });
            im.background.add(im.saveArea);
            im.background.on("click", function() {
                im.setActiveElement(im.stage);
            });
        }
        im.saveArea.setFillPatternOffset([ -(im.saveWidth / 2) * im.scale, -(im.saveHeight / 2) * im.scale ]);
        im.saveArea.setX(Math.floor(im.center.x - im.saveWidth / 2));
        im.saveArea.setY(Math.floor(im.center.y - im.saveHeight / 2));
        im.saveArea.setFillPatternScale(1 / im.scale);
        im.saveArea.setWidth(im.saveWidth);
        im.saveArea.setHeight(im.saveHeight);
        if (im.foreground) {
            im.foreground.destroy();
        }
        im.foreground = new Kinetic.Layer;
        im.stage.add(im.foreground);
        if (!im.coverLayer) {
            im.coverLayer = new Kinetic.Rect;
            im.coverLayer.setStroke("rgba(150,150,150,.5)");
            im.coverLayer.setFill("transparent");
            im.coverLayer.setDrawHitFunc(function() {});
            im.coverLayer.setStrokeWidth(Math.max(t.width, t.height, 500));
        }
        var r = Math.max(t.width, t.height) * 2;
        im.coverLayer.attrs.width = im.saveArea.attrs.width + r;
        im.coverLayer.attrs.height = im.saveArea.attrs.height + r;
        im.coverLayer.attrs.x = im.saveArea.attrs.x - r / 2;
        im.coverLayer.attrs.y = im.saveArea.attrs.y - r / 2;
        im.coverLayer.setStrokeWidth(r);
        im.foreground.add(im.coverLayer);
        im.fire("backgroundBuilt");
        im.background.draw();
        im.foreground.draw();
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
    im.autoCrop = true;
    im.on("imageLoad", function() {
        var e = 100;
        var t = im.stage.getWidth() - e * 2, n = im.stage.getHeight() - e * 2;
        if (im.saveWidth < t && im.saveHeight < n) return;
        var r = Math.max(im.saveWidth / t, im.saveHeight / n);
        im.stage.setX((im.stage.getWidth() - im.stage.getWidth() * im.stage.getScale().x) / 2);
        im.stage.setY((im.stage.getHeight() - im.stage.getHeight() * im.stage.getScale().y) / 2);
        var i = im.stage.getDragBoundFunc()({
            x: im.stage.getX(),
            y: im.stage.getY()
        });
        im.stage.setX(i.x);
        im.stage.setY(i.y);
        im.fire("scaleChange");
        im.fire("stageChanged");
        im.buildBackground();
    });
    im.fit = function(e, t) {
        if (t === false) {
            return {
                width: im.saveWidth,
                height: im.saveHeight
            };
        }
        var n = e.height, r = e.width;
        if (r > im.saveWidth) {
            n /= r / im.saveWidth;
            r = im.saveWidth;
        }
        if (n > im.saveHeight) {
            r /= n / im.saveHeight;
            n = im.saveHeight;
        }
        return {
            width: r,
            height: n
        };
    };
    if (settings.src) {
        im.showLoader("Loading Image..");
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
                x: Math.floor(im.center.x - img.width / 2),
                y: Math.floor(im.center.y - img.height / 2)
            };
            var t = new Kinetic.Image({
                image: img,
                x: Math.floor(e.x),
                y: Math.floor(e.y)
            });
            im.fire("imageload");
            im.addElement(t, "image");
        };
    } else {
        im.fire("imageload");
    }
    im.bind("imageload", function() {
        var e = settings.controlsets || {}, t = settings.filters || {}, n, r;
        var i = 0;
        log("Loading ControlSets");
        im.showLoader("Loading Control Sets..");
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
    im.adjustSavers = function() {
        if (im.activeElement.elementType != "stage" && im.autoCrop) {
            im.alterCore("saveWidth", Math.ceil(-(im.activeElement.getX() - im.center.x) * 2));
            im.alterCore("saveHeight", Math.ceil(-(im.activeElement.getY() - im.center.y) * 2));
            if ((im.activeElement.getWidth() - im.saveWidth / 2) * 2 > im.saveWidth) {
                im.alterCore("saveWidth", Math.ceil((im.activeElement.getWidth() - im.saveWidth / 2) * 2));
            }
            if ((im.activeElement.getHeight() - im.saveHeight / 2) * 2 > im.saveHeight) {
                im.alterCore("saveHeight", Math.ceil((im.activeElement.getHeight() - im.saveHeight / 2) * 2));
            }
            im.buildBackground();
            im.fire("saveSizeChange");
        }
    };
    im.bind("ControlSetsLoaded", function() {
        im.fire("LoadingComponents");
        im.showLoader("Loading Components..");
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
        im.showLoader("Loading Filters..");
        var e = settings.filters || {}, t, n, r, i = 0;
        im.fire("LoadingFilters");
        for (t in e) {
            var s = "Filter_" + t;
            var o = e[t].name;
            if (!n) n = s;
            i++;
            $.ajax(e[t].src, {
                dataType: "text",
                cache: false,
                namespace: t,
                myns: s,
                name: o,
                success: function(e) {
                    var t = im.addFilter(this.myns, e);
                    t.name = this.name;
                    im.fire("filterLoad", t);
                    i--;
                    if (0 == i) {
                        im.trigger("FiltersLoaded");
                    }
                },
                error: function(e, t, n) {
                    i--;
                    if (0 == i) {
                        im.trigger("FiltersLoaded");
                    }
                }
            });
        }
    });
    im.bind("ChangeActiveAction", function(e) {
        var t = e.eventData;
        if (t === im.activeControlSet) return;
        for (var n in im.controlSets) {
            getElem(im.controlSets[n]);
            if (n !== t) getElem(im.controlSets[n]).slideUp();
        }
        im.activeControlSet = t;
        im.alterCore("activeControlSet", t);
        if (!t) {
            $("div.control-sets", im.controlContext).find("h4.active").removeClass("active");
            return;
        }
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
        log("changenavtab", e);
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
    im.bind("FiltersLoaded", function() {
        im.hideLoader();
    });
    im.slideOut = $("<div/>").addClass("slideOut").css({
        width: 0,
        "float": "right",
        height: "100%",
        "overflow-x": "hidden",
        right: im.controlContext.width() - 1,
        position: "absolute",
        background: "white",
        "box-shadow": "black -20px 0 20px -25px"
    });
    im.slideOutContents = $("<div/>").appendTo(im.slideOut).width(300);
    im.showSlideOut = function(e, t) {
        im.hideSlideOut(function() {
            im.slideOut.empty();
            im.slideOutContents = e.width(300);
            im.slideOut.append(im.slideOutContents);
            im.slideOut.addClass("active").addClass("sliding");
            im.slideOut.stop(1).slideOut(300, function() {
                im.slideOut.removeClass("sliding");
                typeof t === "function" && t();
            });
        });
    };
    im.hideSlideOut = function(e) {
        im.slideOut.addClass("sliding");
        im.slideOut.slideIn(300, function() {
            im.slideOut.css("border-right", "0");
            im.slideOut.removeClass("active").removeClass("sliding");
            typeof e === "function" && e();
        });
    };
    im.controlContext.after(im.slideOut);
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
    t.closest(".ui-dialog").find(".ui-resizable-handle").hide();
    t.height("-=30");
    t.width("-=330").parent().width("-=330").children("div.bottomBar").width("-=330");
    e.width === undefined && (e.width = t.width());
    e.height === undefined && (e.height = t.height());
    $.fn.dialog.showLoader();
    var n = new ImageEditor(e);
    var r = n.domContext;
    n.on("ChangeActiveAction", function(e) {
        if (!e.eventData) $("h4.active", r).removeClass("active");
    });
    n.on("ChangeActiveComponent", function(e) {
        if (!e.eventData) $("h4.active", r).removeClass("active");
    });
    $("div.controls", r).children("div.save").children("button.save").click(function() {
        n.save();
    }).end().children("button.cancel").click(function() {
        if (confirm("Are you certain?")) $.fn.dialog.closeTop();
    });
    $("div.controls", r).children("ul.nav").children().click(function() {
        if ($(this).hasClass("active")) return false;
        $("div.controls", r).children("ul.nav").children().removeClass("active");
        $(this).addClass("active");
        n.trigger("ChangeNavTab", $(this).text().toLowerCase());
        return false;
    });
    $("div.controlset", r).find("div.control").children("div.contents").slideUp(0).end().end().find("h4").click(function() {
        if ($(this).parent().hasClass("disabled")) return;
        $(this).addClass("active");
        $("div.controlset", r).find("h4").not($(this)).removeClass("active");
        var e = $(this).parent().attr("data-namespace");
        n.trigger("ChangeActiveAction", "ControlSet_" + e);
    });
    $("div.component", r).find("div.control").children("div.contents").slideUp(0).hide().end().end().find("h4").click(function() {
        if ($(this).hasClass("active")) return false;
        $(this).addClass("active");
        $("div.component", r).children("h4").not($(this)).removeClass("active");
        var e = $(this).parent().attr("data-namespace");
        n.trigger("ChangeActiveComponent", "Component_" + e);
    });
    $("div.components").hide();
    n.bind("imageload", $.fn.dialog.hideLoader);
    return n;
};

$.fn.slideOut = function(e, t) {
    var n = $(this), r = n.width(), i = 300;
    n.css("overflow-y", "auto");
    if (r == i) {
        n.animate({
            width: i
        }, 0, t);
        return this;
    }
    n.width(r).animate({
        width: i
    }, e || 300, t || function() {});
    return this;
};

$.fn.slideIn = function(e, t) {
    var n = $(this);
    n.css("overflow-y", "hidden");
    if (n.width() === 0) {
        n.animate({
            width: 0
        }, 0, t);
        return this;
    }
    n.animate({
        width: 0
    }, e || 300, t || function() {});
    return this;
};

ImageEditor.prototype = ImageEditor.fn = {
    filter: {
        grayscale: Kinetic.Filters.Grayscale,
        sepia: function(e) {
            var t;
            var n = e.data;
            for (t = 0; t < n.length; t += 4) {
                n[t] = n[t] * .393 + n[t + 1] * .769 + n[t + 2] * .189;
                n[t + 1] = n[t] * .349 + n[t + 1] * .686 + n[t + 2] * .168;
                n[t + 2] = n[t] * .272 + n[t + 1] * .534 + n[t + 2] * .131;
            }
        },
        brightness: function(e, t) {
            var n = t.level;
            var r = e.data;
            for (var i = 0; i < r.length; i += 4) {
                r[i] += n;
                r[i + 1] += n;
                r[i + 2] += n;
            }
        },
        invert: function(e, t) {
            var n = e.data;
            for (var r = 0; r < n.length; r += 4) {
                n[r] = 255 - n[r];
                n[r + 1] = 255 - n[r + 1];
                n[r + 2] = 255 - n[r + 2];
            }
        },
        restore: function(e, t) {
            var n = t.level;
            var r = e.data;
            var i = t.imageData.data;
            for (var s = 0; s < r.length; s += 4) {
                r[s] = i[s];
                r[s + 1] = i[s + 1];
                r[s + 2] = i[s + 2];
            }
        }
    }
};