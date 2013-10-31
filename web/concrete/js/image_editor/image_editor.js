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
        Kinetic.Util.warn("Unable to get imageData.");
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

var ImageEditor = function(e) {
    "use strict";
    if (e === undefined) return this;
    e.pixelRatio = 1;
    var t = this, n, r = function(e) {
        return Math.round(e);
    };
    t.width = e.width;
    t.height = e.height;
    t.saveWidth = e.saveWidth || r(t.width / 2);
    t.saveHeight = e.saveHeight || r(t.height / 2);
    t.strictSize = e.saveWidth !== undefined ? true : false;
    t.stage = new Kinetic.Stage(e);
    t.namespaces = {};
    t.controlSets = {};
    t.components = {};
    t.settings = e;
    t.filters = {};
    t.fileId = t.settings.fID;
    t.scale = 1;
    t.crosshair = new Image;
    t.uniqid = t.stage.getContainer().id;
    t.editorContext = $(t.stage.getContainer()).parent();
    t.domContext = t.editorContext.parent();
    t.controlContext = t.domContext.children("div.controls");
    t.controlSetNamespaces = [];
    t.showLoader = $.fn.dialog.showLoader;
    t.hideLoader = $.fn.dialog.hideLoader;
    t.stage.im = t;
    t.stage.setX(.5);
    t.stage.setY(.5);
    t.stage.elementType = "stage";
    t.crosshair.src = "/concrete/images/image_editor/crosshair.png";
    t.center = {
        x: Math.round(t.width / 2),
        y: Math.round(t.height / 2)
    };
    t.centerOffset = {
        x: t.center.x,
        y: t.center.y
    };
    var i = function(e) {
        return $(e, t.domContext);
    }, s = function() {
        if (e.debug === true && typeof console !== "undefined") {
            var t = arguments;
            if (t.length == 1) t = t[0];
            console.log(t);
        }
    }, o = function() {
        if (e.debug === true && typeof console !== "undefined") {
            var t = arguments;
            if (t.length == 1) t = t[0];
            console.warn(t);
        }
    }, u = function() {
        if (typeof console !== "undefined") {
            var e = arguments;
            if (e.length == 1) e = e[0];
            console.error("Image Editor Error: " + e);
        }
    };
    t.stage._setDraggable = t.stage.setDraggable;
    t.stage.setDraggable = function(e) {
        o("setting draggable to " + e);
        return t.stage._setDraggable(e);
    };
    var a = function() {
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
    t.history = new a;
    t.bindEvent = t.bind = t.on = function(e, n, r) {
        var i = r || t.stage.getContainer();
        if (i instanceof jQuery) i = i[0];
        ccm_event.sub(e, n, i);
    };
    t.fireEvent = t.fire = t.trigger = function(e, n, r) {
        var i = r || t.stage.getContainer();
        if (i instanceof jQuery) i = i[0];
        ccm_event.pub(e, n, i);
    };
    t.addElement = function(e, n) {
        var r = new Kinetic.Layer;
        e.elementType = n;
        r.elementType = n;
        r.add(e);
        t.stage.add(r);
        r.moveDown();
        t.stage.draw();
    };
    t.on("backgroundBuilt", function() {
        if (t.activeElement !== undefined && t.activeElement.doppelganger !== undefined) {
            t.foreground.add(t.activeElement.doppelganger);
            t.activeElement.doppelganger.setPosition(t.activeElement.getPosition());
        }
    });
    t.setActiveElement = function(e) {
        if (e.defer) {
            return t.setActiveElement(e.defer);
        }
        if (t.activeElement == e) return;
        if (t.activeElement !== undefined && t.activeElement.doppelganger !== undefined) {
            t.activeElement.doppelganger.remove();
        }
        if (e === t.stage || e.nodeType == "Stage") {
            t.trigger("ChangeActiveAction", t.controlSetNamespaces.length ? t.controlSetNamespaces[0] : undefined);
            $("div.control-sets", t.controlContext).find("h4.active").removeClass("active");
        } else if (e.doppelganger !== undefined) {
            t.foreground.add(e.doppelganger);
            t.foreground.draw();
        }
        t.trigger("beforeChangeActiveElement", t.activeElement);
        t.alterCore("activeElement", e);
        t.trigger("changeActiveElement", e);
        t.stage.draw();
    };
    t.bind("ClickedElement", function(e) {
        t.setActiveElement(e.eventData);
    });
    t.bind("stageChanged", function(e) {
        if (t.activeElement.getWidth() > t.stage.getScaledWidth() || t.activeElement.getHeight() > t.stage.getScaledHeight()) {
            t.setActiveElement(t.stage);
        }
    });
    var f = i(t.stage.getContainer()).parent().children(".bottomBar");
    f.attr("unselectable", "on");
    var l = {};
    l.zoomIn = i("<div class='bottombarbutton plus'><i class='icon-plus'></i></div>");
    l.zoomOut = i("<div class='bottombarbutton'><i class='icon-minus'></i></div>");
    l.zoomIn.appendTo(f);
    l.zoomOut.appendTo(f);
    l.zoomIn.click(function(e) {
        t.fire("zoomInClick", e);
    });
    l.zoomOut.click(function(e) {
        t.fire("zoomOutClick", e);
    });
    var c = i("<div></div>").addClass("scale").text("100%");
    t.on("scaleChange", function(e) {
        c.text(Math.round(t.scale * 1e4) / 100 + "%");
    });
    c.click(function() {
        t.scale = 1;
        t.stage.setScale(t.scale);
        var e = t.stage.getDragBoundFunc()({
            x: t.stage.getX(),
            y: t.stage.getY()
        });
        t.stage.setX(e.x);
        t.stage.setY(e.y);
        t.fire("scaleChange");
        t.buildBackground();
        t.stage.draw();
    });
    c.appendTo(f);
    var h = 0, p = 3e3, d = 5 / 6;
    t.on("zoomInClick", function(e) {
        var n = (-t.stage.getX() + t.stage.getWidth() / 2) / t.scale, r = (-t.stage.getY() + t.stage.getHeight() / 2) / t.scale;
        t.scale /= d;
        t.scale = Math.round(t.scale * 1e3) / 1e3;
        t.alterCore("scale", t.scale);
        var i = (-t.stage.getX() + t.stage.getWidth() / 2) / t.scale, s = (-t.stage.getY() + t.stage.getHeight() / 2) / t.scale;
        t.stage.setX(t.stage.getX() - (n - i) * t.scale);
        t.stage.setY(t.stage.getY() - (r - s) * t.scale);
        t.stage.setScale(t.scale);
        var o = t.stage.getDragBoundFunc()({
            x: t.stage.getX(),
            y: t.stage.getY()
        });
        t.stage.setX(o.x);
        t.stage.setY(o.y);
        t.fire("scaleChange");
        t.buildBackground();
        t.stage.draw();
    });
    t.on("zoomOutClick", function(e) {
        var n = (-t.stage.getX() + t.stage.getWidth() / 2) / t.scale, r = (-t.stage.getY() + t.stage.getHeight() / 2) / t.scale;
        t.scale *= d;
        t.scale = Math.round(t.scale * 1e3) / 1e3;
        t.alterCore("scale", t.scale);
        var i = (-t.stage.getX() + t.stage.getWidth() / 2) / t.scale, s = (-t.stage.getY() + t.stage.getHeight() / 2) / t.scale;
        t.stage.setX(t.stage.getX() - (n - i) * t.scale);
        t.stage.setY(t.stage.getY() - (r - s) * t.scale);
        t.stage.setScale(t.scale);
        var o = t.stage.getDragBoundFunc()({
            x: t.stage.getX(),
            y: t.stage.getY()
        });
        t.stage.setX(o.x);
        t.stage.setY(o.y);
        t.fire("scaleChange");
        t.buildBackground();
        t.stage.draw();
    });
    var v = {};
    v.width = i("<span/>").addClass("saveWidth");
    v.height = i("<span/>").addClass("saveHeight");
    v.crop = i('<div><i class="icon-resize-full"/></div>').addClass("bottombarbutton").addClass("crop");
    v.both = v.height.add(v.width).width(32).attr("contenteditable", !!1);
    v.area = i("<span/>").css({
        "float": "right"
    });
    t.on("adjustedsavers", function() {
        v.width.text(t.saveWidth);
        v.height.text(t.saveHeight);
    });
    v.crop.click(function() {
        t.adjustSavers();
    });
    if (t.strictSize) {
        v.both.attr("disabled", "true");
    } else {
        v.both.keyup(function(e) {
            t.fire("editedSize", e);
        });
    }
    t.bind("editedSize", function(e) {
        t.saveWidth = parseInt(v.width.text());
        t.saveHeight = parseInt(v.height.text());
        if (isNaN(t.saveWidth)) t.saveWidth = 0;
        if (isNaN(t.saveHeight)) t.saveHeight = 0;
        t.buildBackground();
    });
    t.bind("saveSizeChange", function() {
        v.width.text(t.saveWidth);
        v.height.text(t.saveHeight);
    });
    t.setCursor = function(e) {
        $(t.stage.getContainer()).css("cursor", e);
    };
    t.save = function() {
        t.background.hide();
        t.stage.setScale(1);
        t.fire("ChangeActiveAction");
        t.fire("changeActiveComponent");
        t.background.hide();
        t.foreground.hide();
        $(t.stage.getContainer()).hide();
        var e = Math.round(t.center.x - t.saveWidth / 2), n = Math.round(t.center.y - t.saveHeight / 2), r = t.stage.getX(), i = t.stage.getY(), s = t.stage.getWidth(), o = t.stage.getHeight();
        t.stage.setX(-e);
        t.stage.setY(-n);
        t.stage.setWidth(Math.max(t.stage.getWidth(), t.saveWidth));
        t.stage.setHeight(Math.max(t.stage.getHeight(), t.saveHeight));
        t.stage.draw();
        t.showLoader("Saving..");
        t.stage.toDataURL({
            width: t.saveWidth,
            height: t.saveHeight,
            callback: function(e) {
                var n = $("<img/>").attr("src", e);
                $.fn.dialog.open({
                    element: $(n).width(250)
                });
                t.hideLoader();
                t.background.show();
                t.foreground.show();
                t.stage.setX(r);
                t.stage.setY(i);
                t.stage.setWidth(s);
                t.stage.setHeight(o);
                t.stage.setScale(t.scale);
                t.stage.draw();
                $(t.stage.getContainer()).show();
            }
        });
    };
    t.save = function() {
        t.fire("ChangeActiveAction");
        var n = t.stage.getPosition(), r = t.scale;
        t.stage.setPosition(-t.saveArea.getX(), -t.saveArea.getY());
        t.stage.setScale(1);
        t.background.hide();
        t.foreground.hide();
        t.stage.draw();
        t.stage.toDataURL({
            width: t.saveWidth,
            height: t.saveHeight,
            callback: function(i) {
                t.stage.setPosition(n);
                t.background.show();
                t.foreground.show();
                t.stage.setScale(r);
                t.stage.draw();
                $.post("/index.php/tools/files/importers/imageeditor", {
                    fID: t.fileId,
                    imgData: i
                }, function(e) {
                    var t = JSON.parse(e);
                    if (t.error === 1) {
                        alert(t.message);
                    } else {
                        window.location = window.location;
                    }
                });
            }
        });
    };
    t.actualPosition = function(n, r, i, s, o) {
        var u = r - s, a = n - i, f = t.activeElement.getRotation() + Math.atan2(u, a), l = Math.sqrt(Math.pow(a, 2) + Math.pow(u, 2));
        return [ i + l * Math.cos(f), s + l * Math.sin(f) ];
    };
    t.getActualRect = function(n, r, i) {
        var s = [], o = i.getRotation();
        s.push(t.actualPosition(i.getX(), i.getY(), n, r, o));
        s.push(t.actualPosition(i.getX() + i.getWidth() * i.getScaleX(), i.getY(), n, r, o));
        s.push(t.actualPosition(i.getX() + i.getWidth() * i.getScaleX(), i.getY() + i.getHeight() * i.getScaleY(), n, r, o));
        s.push(t.actualPosition(i.getX(), i.getY() + i.getHeight() * i.getScaleY(), n, r, o));
        return s;
    };
    t.adjustSavers = function(n) {
        if (t.activeElement.nodeType === "Stage") return;
        t.foreground.autoCrop = false;
        t.background.autoCrop = false;
        var r, i, s, o = {
            min: {
                x: false,
                y: false
            },
            max: {
                x: false,
                y: false
            }
        };
        var u = t.activeElement, a = u.parent, f = t.getActualRect(0, 0, u);
        for (var s = f.length - 1; s >= 0; s--) {
            var l = f[s], c = l[0] + a.getX(), h = l[1] + a.getY();
            if (c > o.max.x || o.max.x === false) o.max.x = c;
            if (c < o.min.x || o.min.x === false) o.min.x = c;
            if (h > o.max.y || o.max.y === false) o.max.y = h;
            if (h < o.min.y || o.min.y === false) o.min.y = h;
        }
        var p = {
            width: o.max.x - o.min.x,
            height: o.max.y - o.min.y
        };
        t.alterCore("saveWidth", Math.round(p.width));
        t.alterCore("saveHeight", Math.round(p.height));
        t.buildBackground();
        var d = [ t.center.x - t.activeElement.getWidth() * t.activeElement.getScaleX() / 2, t.center.y - t.activeElement.getHeight() * t.activeElement.getScaleY() / 2 ], v = t.actualPosition(d[0], d[1], t.center.x, t.center.y, t.activeElement.getRotation());
        t.activeElement.parent.setPosition(v.map(Math.round));
        if (n !== false) t.fire("adjustedsavers");
        t.stage.draw();
    };
    t.bind("imageLoad", function() {
        setTimeout(t.adjustSavers, 0);
    });
    t.extend = function(e, t) {
        this[e] = t;
    };
    t.alterCore = function(e, n) {
        var r = t, i = "core", s;
        if (t.namespace) {
            var i = r.namespace;
            r = t.realIm;
        }
        t[e] = n;
        for (s in t.controlSets) {
            t.controlSets[s].im.extend(e, n);
        }
        for (s in t.filters) {
            t.filters[s].im.extend(e, n);
        }
        for (s in t.components) {
            t.components[s].im.extend(e, n);
        }
    };
    t.clone = function(e) {
        var n = new ImageEditor, r;
        n.realIm = t;
        for (r in t) {
            n[r] = t[r];
        }
        n.namespace = e;
        return n;
    };
    t.addControlSet = function(e, n, r) {
        if (jQuery && r instanceof jQuery) r = r[0];
        r.controlSet = function(t, n) {
            t.disable = function() {
                t.enabled = false;
                $(r).parent().parent().addClass("disabled");
            };
            t.enable = function() {
                t.enabled = true;
                $(r).parent().parent().removeClass("disabled");
            };
            this.im = t;
            this.$ = $;
            o("Loading ControlSet", t);
            try {
                (new Function("im", "$", n)).call(this, t, $);
            } catch (i) {
                console.log(i.stack);
                var s = i.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                if (s[1] && !isNaN(parseInt(s[1]))) {
                    var a = n.split("\n");
                    var f = "Parse error at line #" + s[0] + " char #" + s[1] + " within " + e;
                    f += "\n" + a[parseInt(s[0]) - 1];
                    f += "\n" + (new Array(parseInt(s[1]))).join(" ") + "^";
                    u(f);
                } else {
                    u('"' + i.message + '" in "' + t.namespace + '"');
                }
            }
            return this;
        };
        var i = t.clone(e);
        var s = r.controlSet.call(r, i, n);
        t.controlSets[e] = s;
        return s;
    };
    t.addFilter = function(e, n) {
        var r = function(t, n) {
            this.namespace = t.namespace;
            this.im = t;
            try {
                (new Function("im", "$", n)).call(this, t, $);
            } catch (r) {
                u(r);
                window.lastError = r;
                var i = r.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                if (i.length != 2) {
                    u(r.message);
                    u(r.stack);
                } else {
                    var s = n.split("\n");
                    var o = "Parse error at line #" + i[0] + " char #" + i[1] + " within " + e;
                    o += "\n" + s[parseInt(i[0]) - 1];
                    o += "\n" + (new Array(parseInt(i[1]) || 0)).join(" ") + "^";
                    u(o);
                }
            }
            return this;
        };
        var i = t.clone(e);
        var s = new r(i, n);
        t.filters[e] = s;
        return s;
    };
    t.addComponent = function(e, n, r) {
        if (jQuery && r instanceof jQuery) r = r[0];
        r.component = function(t, n) {
            t.disable = function() {
                $(this).parent().parent().addClass("disabled");
            };
            t.enable = function() {
                $(this).parent().parent().removeClass("disabled");
            };
            this.im = t;
            o("Loading component", t);
            try {
                (new Function("im", "$", n)).call(this, t, $);
            } catch (r) {
                var i = r.stack.replace(/[\S\s]+at HTMLDivElement.eval.+?<anonymous>:(\d+:\d+)[\S\s]+/, "$1").split(":");
                if (i[1] && !isNaN(parseInt(i[1]))) {
                    var s = n.split("\n");
                    var a = "Parse error at line #" + i[0] + " char #" + i[1] + " within " + e;
                    a += "\n" + s[parseInt(i[0]) - 1];
                    a += "\n" + (new Array(parseInt(i[1]))).join(" ") + "^";
                    u(a);
                } else {
                    u('"' + r.message + '" in "' + t.namespace + '"');
                }
            }
            return this;
        };
        var i = t.clone(e);
        var s = r.component.call(r, i, n);
        t.components[e] = s;
        return s;
    };
    t.background = new Kinetic.Layer;
    t.foreground = new Kinetic.Layer;
    t.stage.add(t.background);
    t.stage.add(t.foreground);
    t.bgimage = new Image;
    t.saveArea = new Kinetic.Rect;
    t.background.add(t.saveArea);
    t.bind("load", function() {
        t.saveArea.setFillPatternImage(t.bgimage);
        t.saveArea.setFillPatternOffset([ -(t.saveWidth / 2), -(t.saveHeight / 2) ]);
        t.saveArea.setFillPatternScale(1 / t.scale);
        t.saveArea.setFillPatternX(0);
        t.saveArea.setFillPatternY(0);
        t.saveArea.setFillPatternRepeat("repeat");
        t.background.on("click", function() {
            t.setActiveElement(t.stage);
        });
    }, t.bgimage);
    t.bgimage.src = "/concrete/images/testbg.png";
    t.buildBackground = function() {
        var e = (new Date).getTime();
        var n = t.stage.getTotalDimensions();
        var r = (n.max.x + n.visibleHeight + n.visibleWidth) * 2;
        t.saveArea.setFillPatternOffset([ -(t.saveWidth / 2) * t.scale, -(t.saveHeight / 2) * t.scale ]);
        t.saveArea.setX(Math.round(t.center.x - t.saveWidth / 2));
        t.saveArea.setY(Math.round(t.center.y - t.saveHeight / 2));
        t.saveArea.setFillPatternScale(1 / t.scale);
        t.saveArea.setWidth(t.saveWidth);
        t.saveArea.setHeight(t.saveHeight);
        if (!t.coverLayer) {
            t.coverLayer = new Kinetic.Rect;
            t.coverLayer.setStroke("rgba(150,150,150,.5)");
            t.coverLayer.setFill("transparent");
            t.coverLayer.setListening(false);
            t.coverLayer.setStrokeWidth(Math.max(n.width, n.height, 500));
            t.foreground.add(t.coverLayer);
        }
        var i = Math.max(n.width, n.height) * 2;
        t.coverLayer.setPosition(t.saveArea.getX() - i / 2, t.saveArea.getY() - i / 2);
        t.coverLayer.setSize(t.saveArea.getWidth() + i, t.saveArea.getHeight() + i);
        t.coverLayer.setStrokeWidth(i);
        t.fire("backgroundBuilt");
        t.saveArea.draw();
        t.coverLayer.draw();
    };
    t.buildBackground();
    t.on("stageChanged", t.buildBackground);
    t.stage.setDragBoundFunc(function(e) {
        var n = t.stage.getTotalDimensions();
        var r = Math.max(n.max.x, n.min.x) - 1, i = Math.min(n.max.x, n.min.x) + 1, s = Math.max(n.max.y, n.min.y) - 1, o = Math.min(n.max.y, n.min.y) + 1;
        e.x = Math.floor(e.x);
        e.y = Math.floor(e.y);
        if (e.x > r) e.x = r;
        if (e.x < i) e.x = i;
        if (e.y > s) e.y = s;
        if (e.y < o) e.y = o;
        e.x = Math.floor(e.x);
        e.y = Math.floor(e.y);
        return e;
    });
    t.setActiveElement(t.stage);
    t.stage.setDraggable(true);
    t.autoCrop = true;
    t.on("imageLoad", function() {
        var e = 100;
        var n = t.stage.getWidth() - e * 2, r = t.stage.getHeight() - e * 2;
        if (t.saveWidth < n && t.saveHeight < r) return;
        var i = Math.max(t.saveWidth / n, t.saveHeight / r);
        t.stage.setX((t.stage.getWidth() - t.stage.getWidth() * t.stage.getScale().x) / 2);
        t.stage.setY((t.stage.getHeight() - t.stage.getHeight() * t.stage.getScale().y) / 2);
        var s = t.stage.getDragBoundFunc()({
            x: t.stage.getX(),
            y: t.stage.getY()
        });
        t.stage.setX(s.x);
        t.stage.setY(s.y);
        t.fire("scaleChange");
        t.fire("stageChanged");
        t.buildBackground();
    });
    t.fit = function(e, n) {
        if (n === false) {
            return {
                width: t.saveWidth,
                height: t.saveHeight
            };
        }
        var r = e.height, i = e.width;
        if (i > t.saveWidth) {
            r /= i / t.saveWidth;
            i = t.saveWidth;
        }
        if (r > t.saveHeight) {
            i /= r / t.saveHeight;
            r = t.saveHeight;
        }
        return {
            width: i,
            height: r
        };
    };
    if (e.src) {
        t.showLoader("Loading Image..");
        var m = new Image, g = false;
        t.bind("ControlSetsLoaded", function() {
            g = true;
        });
        t.bind("load", function() {
            if (!t.strictSize) {
                t.saveWidth = m.width;
                t.saveHeight = m.height;
                t.fire("saveSizeChange");
                t.buildBackground();
            }
            var n = {
                x: Math.floor(t.center.x - m.width / 2),
                y: Math.floor(t.center.y - m.height / 2)
            };
            var r = new Kinetic.Image({
                image: m,
                x: 0,
                y: 0
            });
            t.addElement(r, "image");
            r.setPosition(n);
            t.fire("imageload");
            var i = function() {
                setTimeout(function() {
                    t.setActiveElement(r);
                    t.fire("changeActiveAction", t.controlSetNamespaces[0]);
                }, 0);
            };
            if (g) {
                i();
            } else {
                t.bind("ControlSetsLoaded", i);
            }
        }, m);
        m.src = e.src;
    } else {
        t.fire("imageload");
    }
    t.bind("imageload", function() {
        var n = e.controlsets || {}, r = e.filters || {}, i, o;
        var u = 0;
        s("Loading ControlSets");
        t.showLoader("Loading Control Sets..");
        t.fire("LoadingControlSets");
        for (i in n) {
            var a = "ControlSet_" + i;
            t.controlSetNamespaces.push(a);
            $.ajax(n[i]["src"], {
                dataType: "text",
                cache: false,
                namespace: i,
                myns: a,
                beforeSend: function() {
                    u++;
                },
                success: function(e) {
                    u--;
                    var r = t.addControlSet(this.myns, e, n[this.namespace]["element"]);
                    s(r);
                    t.fire("controlSetLoad", r);
                    if (0 == u) {
                        t.trigger("ControlSetsLoaded");
                    }
                },
                error: function(e, n, r) {
                    u--;
                    if (0 == u) {
                        t.trigger("ControlSetsLoaded");
                    }
                }
            });
        }
    });
    t.bind("ControlSetsLoaded", function() {
        t.fire("LoadingComponents");
        t.showLoader("Loading Components..");
        var n = e.components || {}, r, i = 0;
        s("Loading Components");
        for (r in n) {
            var o = "Component_" + r;
            $.ajax(n[r]["src"], {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: o,
                beforeSend: function() {
                    i++;
                },
                success: function(e) {
                    i--;
                    var r = t.addComponent(this.myns, e, n[this.namespace]["element"]);
                    s(r);
                    t.fire("ComponentLoad", r);
                    if (0 == i) {
                        t.trigger("ComponentsLoaded");
                    }
                },
                error: function(e, n, r) {
                    i--;
                    if (0 == i) {
                        t.trigger("ComponentsLoaded");
                    }
                }
            });
        }
        if (0 == i) {
            t.trigger("ComponentsLoaded");
        }
    });
    t.bind("ComponentsLoaded", function() {
        s("Loading Filters");
        t.showLoader("Loading Filters..");
        var n = e.filters || {}, r, i, o, u = 0;
        t.fire("LoadingFilters");
        for (r in n) {
            var a = "Filter_" + r;
            var f = n[r].name;
            if (!i) i = a;
            u++;
            $.ajax(n[r].src, {
                dataType: "text",
                cache: false,
                namespace: r,
                myns: a,
                name: f,
                success: function(e) {
                    var n = t.addFilter(this.myns, e);
                    n.name = this.name;
                    t.fire("filterLoad", n);
                    u--;
                    if (0 == u) {
                        t.trigger("FiltersLoaded");
                    }
                },
                error: function(e, n, r) {
                    u--;
                    if (0 == u) {
                        t.trigger("FiltersLoaded");
                    }
                }
            });
        }
    });
    t.bind("ChangeActiveAction", function(e) {
        var n = e.eventData;
        if (n === t.activeControlSet) return;
        for (var r in t.controlSets) {
            i(t.controlSets[r]);
            if (r !== n) i(t.controlSets[r]).slideUp();
        }
        t.activeControlSet = n;
        t.alterCore("activeControlSet", n);
        if (!n) {
            $("div.control-sets", t.controlContext).find("h4.active").removeClass("active");
            return;
        }
        var s = $(t.controlSets[n]), o = s.show().height();
        if (s.length == 0) return;
        s.hide().height(o).slideDown(function() {
            $(this).height("");
        });
    });
    t.bind("ChangeActiveComponent", function(e) {
        var n = e.eventData;
        if (n === t.activeComponent) return;
        for (var r in t.components) {
            if (r !== n) i(t.components[r]).slideUp();
        }
        t.activeComponent = n;
        t.alterCore("activeComponent", n);
        if (!n) return;
        var s = $(t.components[n]), o = s.show().height();
        if (s.length == 0) return;
        s.hide().height(o).slideDown(function() {
            $(this).height("");
        });
    });
    t.bind("ChangeNavTab", function(e) {
        s("changenavtab", e);
        t.trigger("ChangeActiveAction", e.eventData);
        t.trigger("ChangeActiveComponent", e.eventData);
        var n = i("div.editorcontrols");
        switch (e.eventData) {
          case "add":
            n.children("div.control-sets").hide();
            n.children("div.components").show();
            break;
          case "edit":
            n.children("div.components").hide();
            n.children("div.control-sets").show();
            break;
        }
    });
    t.bind("FiltersLoaded", function() {
        t.hideLoader();
    });
    t.slideOut = $("<div/>").addClass("slideOut").css({
        width: 0,
        "float": "right",
        height: "100%",
        "overflow-x": "hidden",
        right: t.controlContext.width() - 1,
        position: "absolute",
        background: "white",
        "box-shadow": "black -20px 0 20px -25px"
    });
    t.slideOutContents = $("<div/>").appendTo(t.slideOut).width(300);
    t.showSlideOut = function(e, n) {
        t.hideSlideOut(function() {
            t.slideOut.empty();
            t.slideOutContents = e.width(300);
            t.slideOut.append(t.slideOutContents);
            t.slideOut.addClass("active").addClass("sliding");
            t.slideOut.stop(1).slideOut(300, function() {
                t.slideOut.removeClass("sliding");
                typeof n === "function" && n();
            });
        });
    };
    t.hideSlideOut = function(e) {
        t.slideOut.addClass("sliding");
        t.slideOut.slideIn(300, function() {
            t.slideOut.css("border-right", "0");
            t.slideOut.removeClass("active").removeClass("sliding");
            typeof e === "function" && e();
        });
    };
    t.controlContext.after(t.slideOut);
    t.setActiveElement(t.stage);
    window.c5_image_editor = t;
    window.im = t;
    return t;
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
    $("div.editorcontrols").height(t.height() - 90);
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
    $("div.controls > div.controlscontainer", r).children("div.save").children("button.save").click(function() {
        n.save();
    }).end().children("button.cancel").click(function() {
        if (confirm("Are you certain?")) $.fn.dialog.closeTop();
    });
    $("div.controls > div.controlscontainer", r).children("ul.nav").children().click(function() {
        if ($(this).hasClass("active")) return false;
        $("div.controls > div.controlscontainer", r).children("ul.nav").children().removeClass("active");
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