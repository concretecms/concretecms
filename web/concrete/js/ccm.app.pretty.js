/**
 *
 * Color picker
 * Author: Stefan Petre www.eyecon.ro
 * 
 */function ccmLayout(cvalID, layout_id, area, locked) {
    this.layout_id = layout_id;
    this.cvalID = cvalID;
    this.locked = locked;
    this.area = area;
    this.init = function() {
        var a = this;
        this.layoutWrapper = $("#ccm-layout-wrapper-" + this.cvalID);
        this.ccmControls = this.layoutWrapper.find("#ccm-layout-controls-" + this.cvalID);
        this.ccmControls.get(0).layoutObj = this;
        this.ccmControls.mouseover(function() {
            a.dontUpdateTwins = 0;
            a.highlightAreas(1);
        });
        this.ccmControls.mouseout(function() {
            if (!a.moving) a.highlightAreas(0);
        });
        this.ccmControls.find(".ccm-layout-menu-button").click(function(b) {
            a.optionsMenu(b);
        });
        this.gridSizing();
    };
    this.highlightAreas = function(a) {
        var b = this.layoutWrapper.find(".ccm-add-block");
        if (a) b.addClass("ccm-layout-area-highlight"); else b.removeClass("ccm-layout-area-highlight");
    };
    this.optionsMenu = function(a) {
        ccm_hideMenus();
        a.stopPropagation();
        ccm_menuActivated = true;
        var b = document.getElementById("ccm-layout-options-menu-" + this.cvalID);
        if (!b) {
            el = document.createElement("DIV");
            el.id = "ccm-layout-options-menu-" + this.cvalID;
            el.className = "ccm-menu ccm-ui";
            el.style.display = "none";
            document.body.appendChild(el);
            b = $(el);
            b.css("position", "absolute");
            var c = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
            c += "<ul>";
            c += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-edit-menu" dialog-title="' + ccmi18n.editAreaLayout + '" dialog-modal="false" dialog-width="550" dialog-height="280" dialog-append-buttons="true" id="menuEditLayout' + this.cvalID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(this.area) + "&layoutID=" + this.layout_id + "&cvalID=" + this.cvalID + '&atask=layout">' + ccmi18n.editAreaLayout + "</a></li>";
            c += '<li><a onclick="ccm_hideMenus()" href="javascript:void(0)" class="ccm-menu-icon ccm-icon-move-up" id="menuAreaLayoutMoveUp' + this.cvalID + '">' + ccmi18n.moveLayoutUp + "</a></li>";
            c += '<li><a onclick="ccm_hideMenus()" href="javascript:void(0)" class="ccm-menu-icon ccm-icon-move-down" id="menuAreaLayoutMoveDown' + this.cvalID + '">' + ccmi18n.moveLayoutDown + "</a></li>";
            var d = this.locked ? ccmi18n.unlockAreaLayout : ccmi18n.lockAreaLayout;
            c += '<li><a onclick="ccm_hideMenus()" href="javascript:void(0)" class="ccm-menu-icon ccm-icon-lock-menu" id="menuAreaLayoutLock' + this.cvalID + '">' + d + "</a></li>";
            c += '<li><a onclick="ccm_hideMenus()" href="javascript:void(0)" class="ccm-menu-icon ccm-icon-delete-menu" dialog-append-buttons="true" id="menuAreaLayoutDelete' + this.cvalID + '">' + ccmi18n.deleteLayout + "</a></li>";
            c += "</ul>";
            c += "</div></div></div>";
            b.append(c);
            var e = $(b);
            var f = this;
            e.find("#menuEditLayout" + this.cvalID).dialog();
            e.find("#menuAreaLayoutMoveUp" + this.cvalID).click(function() {
                f.moveLayout("up");
            });
            e.find("#menuAreaLayoutMoveDown" + this.cvalID).click(function() {
                f.moveLayout("down");
            });
            e.find("#menuAreaLayoutLock" + this.cvalID).click(function() {
                f.lock();
            });
            e.find("#menuAreaLayoutDelete" + this.cvalID).click(function() {
                f.deleteLayoutOptions();
            });
        } else {
            b = $("#ccm-layout-options-menu-" + this.cvalID);
        }
        ccm_fadeInMenu(b, a);
    };
    this.moveLayout = function(direction) {
        this.moving = 1;
        ccm_hideHighlighter();
        jQuery.fn.dialog.showLoader();
        this.highlightAreas(1);
        this.servicesAjax = $.ajax({
            url: CCM_TOOLS_PATH + "/layout_services/?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(this.area) + "&layoutID=" + this.layout_id + "&cvalID=" + this.cvalID + "&task=move&direction=" + direction,
            success: function(response) {
                eval("var jObj=" + response);
                if (parseInt(jObj.success) != 1) {
                    alert(jObj.msg);
                } else {
                    ccm_mainNavDisableDirectExit();
                    location.reload();
                }
            }
        });
    };
    this.lock = function(lock, twinLock) {
        var a = $("#menuAreaLayoutLock" + this.cvalID);
        this.locked = !this.locked;
        if (this.locked) {
            a.html(ccmi18n.unlockAreaLayout);
            if (this.s) this.s.slider("disable");
        } else {
            a.find("span").html(ccmi18n.lockAreaLayout);
            if (this.s) this.s.slider("enable");
        }
        var lock = this.locked ? 1 : 0;
        if (!twinLock) {
            this.servicesAjax = $.ajax({
                url: CCM_TOOLS_PATH + "/layout_services/?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(this.area) + "&layoutID=" + this.layout_id + "&task=lock&lock=" + lock,
                success: function(response) {
                    eval("var jObj=" + response);
                    if (parseInt(jObj.success) != 1) {
                        alert(jObj.msg);
                    } else {}
                }
            });
            this.getTwins();
            for (var i = 0; i < this.layoutTwinObjs.length; i++) this.layoutTwinObjs[i].lock(lock, 1);
        }
    };
    this.hasBeenQuickSaved = 0;
    this.quickSaveLayoutId = 0;
    this.quickSave = function() {
        var breakPoints = this.ccmControls.find("#layout_col_break_points_" + this.cvalID).val().replace(/%/g, "");
        clearTimeout(this.secondSavePauseTmr);
        if (!this.hasBeenQuickSaved && this.quickSaveInProgress) {
            quickSaveLayoutObj = this;
            this.secondSavePauseTmr = setTimeout("quickSaveLayoutObj.quickSave()", 100);
            return;
        }
        this.quickSaveInProgress = 1;
        var layoutObj = this;
        var modifyLayoutId = this.quickSaveLayoutId ? this.quickSaveLayoutId : this.layout_id;
        this.quickSaveAjax = $.ajax({
            url: CCM_TOOLS_PATH + "/layout_services/?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(this.area) + "&layoutID=" + modifyLayoutId + "&task=quicksave&breakpoints=" + encodeURIComponent(breakPoints),
            success: function(response) {
                eval("var jObj=" + response);
                if (parseInt(jObj.success) != 1) {
                    alert(jObj.msg);
                } else {
                    layoutObj.hasBeenQuickSaved = 1;
                    layoutObj.quickSaveInProgress = 0;
                    if (jObj.layoutID) {
                        layoutObj.quickSaveLayoutId = jObj.layoutID;
                    }
                    ccm_mainNavDisableDirectExit();
                }
            }
        });
    };
    this.deleteLayoutOptions = function() {
        var a = 0;
        deleteLayoutObj = this;
        this.layoutWrapper.find(".ccm-block").each(function(b, c) {
            if (c.style.display != "none") a = 1;
        });
        var b = a ? "135px" : "70px";
        $.fn.dialog.open({
            title: ccmi18n.deleteLayoutOptsTitle,
            href: CCM_TOOLS_PATH + "/layout_services/?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(this.area) + "&layoutID=" + this.layout_id + "&task=deleteOpts&hasBlocks=" + a,
            width: "340px",
            modal: false,
            appendButtons: true,
            height: b
        });
    };
    this.deleteLayout = function(deleteBlocks) {
        ccm_hideMenus();
        jQuery.fn.dialog.closeTop();
        this.layoutWrapper.slideUp(300);
        jQuery.fn.dialog.showLoader();
        var cvalID = this.cvalID;
        this.servicesAjax = $.ajax({
            url: CCM_TOOLS_PATH + "/layout_services/?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(this.area) + "&layoutID=" + this.layout_id + "&task=delete&deleteBlocks=" + parseInt(deleteBlocks),
            success: function(response) {
                eval("var jObj=" + response);
                if (parseInt(jObj.success) != 1) {
                    alert(jObj.msg);
                    jQuery.fn.dialog.hideLoader();
                } else {
                    $("#ccm-layout-wrapper-" + cvalID).remove();
                    ccm_hideHighlighter();
                    ccm_mainNavDisableDirectExit();
                    if (jObj.refreshPage) window.location = window.location; else jQuery.fn.dialog.hideLoader();
                }
            }
        });
    };
    this.gridSizing = function() {
        this.ccmGrid = $("#ccm-layout-" + this.layout_id);
        var a = parseInt(this.ccmControls.find(".layout_column_count").val());
        if (a > 1) {
            var b = this.ccmControls.find("#layout_col_break_points_" + this.cvalID).val().replace(/%/g, "").split("|");
            this.s = this.ccmControls.find(".ccm-layout-controls-slider");
            this.s.get(0).layoutObj = this;
            this.s.get(0).ccmGrid = this.ccmGrid;
            this.s.slider({
                step: 1,
                values: b,
                change: function() {
                    if (this.layoutObj.dontUpdateTwins) return;
                    this.layoutObj.resizeGrid(this.childNodes);
                    var a = [];
                    for (var b = 0; b < this.childNodes.length; b++) a.push(parseFloat(this.childNodes[b].style.left.replace("%", "")));
                    a.sort(function(a, b) {
                        return a - b;
                    });
                    this.layoutObj.ccmControls.find(".layout_col_break_points").val(a.join("%|") + "%");
                    this.layoutObj.quickSave();
                    ccm_arrangeMode = 0;
                    this.layoutObj.moving = 0;
                    this.layoutObj.highlightAreas(0);
                },
                slide: function() {
                    ccm_arrangeMode = 1;
                    this.layoutObj.moving = 1;
                    if (this.layoutObj.dontUpdateTwins) return;
                    this.layoutObj.resizeGrid(this.childNodes);
                }
            });
            if (parseInt(this.ccmControls.find(".layout_locked").val())) this.s.slider("disable");
        }
    };
    this.getTwins = function() {
        if (!this.layoutTwins) {
            this.layoutTwins = $(".ccm-layout-controls-layoutID-" + this.layout_id).not(this.ccmControls);
            this.layoutTwinObjs = [];
            for (var a = 0; a < this.layoutTwins.length; a++) {
                this.layoutTwinObjs.push(this.layoutTwins[a].layoutObj);
                this.layoutTwins[a].handles = $(this.layoutTwins[a]).find(".ui-slider-handle");
            }
        }
        return this.layoutTwins;
    };
    this.resizeGrid = function(a) {
        var b = [];
        this.getTwins();
        for (var c = 0; c < a.length; c++) {
            var d = parseFloat(a[c].style.left.replace("%", ""));
            b.push(d);
            if (!this.dontUpdateTwins) for (var e = 0; e < this.layoutTwinObjs.length; e++) {
                this.layoutTwinObjs[e].dontUpdateTwins = 1;
                this.layoutTwinObjs[e].s.slider("values", c, d);
            }
        }
        b.sort(function(a, b) {
            return a - b;
        });
        var f = 0;
        var g;
        for (g = 0; g < b.length; g++) {
            var d = b[g];
            var e = d - f;
            f += e;
            $(".ccm-layout-" + this.layout_id + "-col-" + (g + 1)).css("width", e + "%");
            if (!this.dontUpdateTwins) for (j = 0; j < this.layoutTwins.length; j++) this.layoutTwins[j].handles[g].style.left = d + "%";
        }
        $(".ccm-layout-" + this.layout_id + "-col-" + (g + 1)).css("width", 100 - f + "%");
    };
}

function fixResortingDroppables() {
    if (tr_reorderMode == false) {
        return false;
    }
    var a = $(".dropzone");
    for (var b = 0; b < a.length; b++) {
        var c = $(a[b]).attr("id").substr(7);
        if (c.indexOf("-sub") > 0) {
            c = c.substr(0, c.length - 4);
        }
        addResortDroppable(c);
    }
}

function addResortDroppable(a) {
    if ($(".tree-branch" + a).length <= 1) return;
    $("div.tree-dz" + a).droppable({
        accept: ".tree-branch" + a,
        activeClass: "dropzone-ready",
        hoverClass: "dropzone-active",
        drop: function(a, b) {
            var c = b.draggable;
            $(c).insertAfter(this);
            var d = $(c).attr("id").substring(9);
            $("#tree-dz" + d).insertAfter($(c));
            rescanDisplayOrder($(this).attr("tree-parent"));
        }
    });
}

function ccm_previewInternalTheme(a, b, c) {
    var d = $("input[name=ctID]").val();
    $.fn.dialog.open({
        title: c,
        href: CCM_TOOLS_PATH + "/themes/preview?themeID=" + b + "&previewCID=" + a + "&ctID=" + d,
        width: "85%",
        modal: false,
        height: "75%"
    });
}

function ccm_previewMarketplaceTheme(a, b, c, d) {
    var e = $("input[name=ctID]").val();
    $.fn.dialog.open({
        title: c,
        href: CCM_TOOLS_PATH + "/themes/preview?themeCID=" + b + "&previewCID=" + a + "&themeHandle=" + encodeURIComponent(d) + "&ctID=" + e,
        width: "85%",
        modal: false,
        height: "75%"
    });
}

(function(a) {
    var b = function() {
        var b = {}, c, d = 65, e, f = '<div class="colorpicker"><div class="colorpicker_color"><div><div></div></div></div><div class="colorpicker_hue"><div></div></div><div class="colorpicker_new_color"></div><div class="colorpicker_current_color"></div><div class="colorpicker_hex"><input type="text" class="text" maxlength="6" size="6" /></div><div class="colorpicker_rgb_r colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_g colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_b colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_h colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_s colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_b colorpicker_field"><input type="text" class="text" maxlength="3" size="3" /><span></span></div><input type="button" class="colorpicker_none" name="none" value="x" /><input type="button" class="colorpicker_submit" name="save" value="Ok" /></div>', g = {
            eventName: "click",
            onShow: function() {},
            onBeforeShow: function() {},
            onHide: function() {},
            onNone: function() {},
            onChange: function() {},
            onSubmit: function() {},
            color: "ff0000",
            livePreview: true,
            flat: false
        }, h = function(b, c) {
            var d = P(b);
            a(c).data("colorpicker").fields.eq(1).val(d.r).end().eq(2).val(d.g).end().eq(3).val(d.b).end();
        }, i = function(b, c) {
            a(c).data("colorpicker").fields.eq(4).val(b.h).end().eq(5).val(b.s).end().eq(6).val(b.b).end();
        }, j = function(b, c) {
            a(c).data("colorpicker").fields.eq(0).val(R(b)).end();
        }, k = function(b, c) {
            a(c).data("colorpicker").selector.css("backgroundColor", "#" + R({
                h: b.h,
                s: 100,
                b: 100
            }));
            a(c).data("colorpicker").selectorIndic.css({
                left: parseInt(150 * b.s / 100, 10),
                top: parseInt(150 * (100 - b.b) / 100, 10)
            });
        }, l = function(b, c) {
            a(c).data("colorpicker").hue.css("top", parseInt(150 - 150 * b.h / 360, 10));
        }, m = function(b, c) {
            a(c).data("colorpicker").currentColor.css("backgroundColor", "#" + R(b));
        }, n = function(b, c) {
            a(c).data("colorpicker").newColor.css("backgroundColor", "#" + R(b));
        }, o = function(b) {
            var c = b.charCode || b.keyCode || -1;
            if (c > d && c <= 90 || c == 32) {
                return false;
            }
            var e = a(this).parent().parent();
            if (e.data("colorpicker").livePreview === true) {
                p.apply(this);
            }
        }, p = function(b) {
            var c = a(this).parent().parent(), d;
            if (!c.data("colorpicker") || !c.data("colorpicker").fields) return;
            if (this.parentNode.className.indexOf("_hex") > 0) {
                c.data("colorpicker").color = d = N(L(this.value));
            } else if (this.parentNode.className.indexOf("_hsb") > 0) {
                c.data("colorpicker").color = d = J({
                    h: parseInt(c.data("colorpicker").fields.eq(4).val(), 10),
                    s: parseInt(c.data("colorpicker").fields.eq(5).val(), 10),
                    b: parseInt(c.data("colorpicker").fields.eq(6).val(), 10)
                });
            } else {
                c.data("colorpicker").color = d = O(K({
                    r: parseInt(c.data("colorpicker").fields.eq(1).val(), 10),
                    g: parseInt(c.data("colorpicker").fields.eq(2).val(), 10),
                    b: parseInt(c.data("colorpicker").fields.eq(3).val(), 10)
                }));
            }
            if (b) {
                h(d, c.get(0));
                j(d, c.get(0));
                i(d, c.get(0));
            }
            k(d, c.get(0));
            l(d, c.get(0));
            n(d, c.get(0));
            c.data("colorpicker").onChange.apply(c, [ d, R(d), P(d) ]);
        }, q = function(b) {
            var c = a(this).parent().parent();
            var d = c.data("colorpicker");
            if (d && d.fields) d.fields.parent().removeClass("colorpicker_focus");
        }, r = function() {
            d = this.parentNode.className.indexOf("_hex") > 0 ? 70 : 65;
            var b = a(this).parent().parent().data("colorpicker");
            if (b && b.fields) b.fields.parent().removeClass("colorpicker_focus");
            a(this).parent().addClass("colorpicker_focus");
        }, s = function(b) {
            var c = a(this).parent().find("input").focus();
            var d = {
                el: a(this).parent().addClass("colorpicker_slider"),
                max: this.parentNode.className.indexOf("_hsb_h") > 0 ? 360 : this.parentNode.className.indexOf("_hsb") > 0 ? 100 : 255,
                y: b.pageY,
                field: c,
                val: parseInt(c.val(), 10),
                preview: a(this).parent().parent().data("colorpicker").livePreview
            };
            a(document).bind("mouseup", d, u);
            a(document).bind("mousemove", d, t);
        }, t = function(a) {
            a.data.field.val(Math.max(0, Math.min(a.data.max, parseInt(a.data.val + a.pageY - a.data.y, 10))));
            if (a.data.preview) {
                p.apply(a.data.field.get(0), [ true ]);
            }
            return false;
        }, u = function(b) {
            p.apply(b.data.field.get(0), [ true ]);
            b.data.el.removeClass("colorpicker_slider").find("input").focus();
            a(document).unbind("mouseup", u);
            a(document).unbind("mousemove", t);
            return false;
        }, v = function(b) {
            var c = {
                cal: a(this).parent(),
                y: a(this).offset().top
            };
            c.preview = c.cal.data("colorpicker").livePreview;
            a(document).bind("mouseup", c, x);
            a(document).bind("mousemove", c, w);
        }, w = function(a) {
            p.apply(a.data.cal.data("colorpicker").fields.eq(4).val(parseInt(360 * (150 - Math.max(0, Math.min(150, a.pageY - a.data.y))) / 150, 10)).get(0), [ a.data.preview ]);
            return false;
        }, x = function(b) {
            h(b.data.cal.data("colorpicker").color, b.data.cal.get(0));
            j(b.data.cal.data("colorpicker").color, b.data.cal.get(0));
            a(document).unbind("mouseup", x);
            a(document).unbind("mousemove", w);
            return false;
        }, y = function(b) {
            var c = {
                cal: a(this).parent(),
                pos: a(this).offset()
            };
            c.preview = c.cal.data("colorpicker").livePreview;
            a(document).bind("mouseup", c, A);
            a(document).bind("mousemove", c, z);
        }, z = function(a) {
            p.apply(a.data.cal.data("colorpicker").fields.eq(6).val(parseInt(100 * (150 - Math.max(0, Math.min(150, a.pageY - a.data.pos.top))) / 150, 10)).end().eq(5).val(parseInt(100 * Math.max(0, Math.min(150, a.pageX - a.data.pos.left)) / 150, 10)).get(0), [ a.data.preview ]);
            return false;
        }, A = function(b) {
            h(b.data.cal.data("colorpicker").color, b.data.cal.get(0));
            j(b.data.cal.data("colorpicker").color, b.data.cal.get(0));
            a(document).unbind("mouseup", A);
            a(document).unbind("mousemove", z);
            return false;
        }, B = function(b) {
            a(this).addClass("colorpicker_focus");
        }, C = function(b) {
            a(this).removeClass("colorpicker_focus");
        }, D = function(b) {
            var c = a(this).parent();
            var d = c.data("colorpicker").color;
            c.data("colorpicker").origColor = d;
            m(d, c.get(0));
            var e = a("#" + a(this).data("colorpickerId"));
            c.data("colorpicker").onSubmit(d, R(d), P(d), c);
        }, E = function(b) {
            var c = a(this).parent();
            c.data("colorpicker").onNone(c);
            c.hide();
        }, F = function(b) {
            var c = a("#" + a(this).data("colorpickerId"));
            c.data("colorpicker").onBeforeShow.apply(this, [ c.get(0) ]);
            var d = a(this).offset();
            var e = I();
            var f = d.top + this.offsetHeight;
            var g = d.left;
            if (f + 176 > e.t + e.h) {
                f -= this.offsetHeight + 176;
            }
            if (g + 356 > e.l + e.w) {
                g -= 356;
            }
            c.css({
                left: g + "px",
                top: f + "px"
            });
            if (c.data("colorpicker").onShow.apply(this, [ c.get(0) ]) != false) {
                c.show();
            }
            a(document).bind("mousedown", {
                cal: c
            }, G);
            return false;
        }, G = function(b) {
            if (!H(b.data.cal.get(0), b.target, b.data.cal.get(0))) {
                if (b.data.cal.data("colorpicker").onHide.apply(this, [ b.data.cal.get(0) ]) != false) {
                    b.data.cal.hide();
                }
                a(document).unbind("mousedown", G);
            }
        }, H = function(a, b, c) {
            if (a == b) {
                return true;
            }
            if (a.contains) {
                return a.contains(b);
            }
            if (a.compareDocumentPosition) {
                return !!(a.compareDocumentPosition(b) & 16);
            }
            var d = b.parentNode;
            while (d && d != c) {
                if (d == a) return true;
                d = d.parentNode;
            }
            return false;
        }, I = function() {
            var a = document.compatMode == "CSS1Compat";
            return {
                l: window.pageXOffset || (a ? document.documentElement.scrollLeft : document.body.scrollLeft),
                t: window.pageYOffset || (a ? document.documentElement.scrollTop : document.body.scrollTop),
                w: window.innerWidth || (a ? document.documentElement.clientWidth : document.body.clientWidth),
                h: window.innerHeight || (a ? document.documentElement.clientHeight : document.body.clientHeight)
            };
        }, J = function(a) {
            return {
                h: Math.min(360, Math.max(0, a.h)),
                s: Math.min(100, Math.max(0, a.s)),
                b: Math.min(100, Math.max(0, a.b))
            };
        }, K = function(a) {
            return {
                r: Math.min(255, Math.max(0, a.r)),
                g: Math.min(255, Math.max(0, a.g)),
                b: Math.min(255, Math.max(0, a.b))
            };
        }, L = function(a) {
            var b = 6 - a.length;
            if (b > 0) {
                var c = [];
                for (var d = 0; d < b; d++) {
                    c.push("0");
                }
                c.push(a);
                a = c.join("");
            }
            return a;
        }, M = function(a) {
            var a = parseInt(a.indexOf("#") > -1 ? a.substring(1) : a, 16);
            return {
                r: a >> 16,
                g: (a & 65280) >> 8,
                b: a & 255
            };
        }, N = function(a) {
            return O(M(a));
        }, O = function(a) {
            var b = {};
            b.b = Math.max(Math.max(a.r, a.g), a.b);
            b.s = b.b <= 0 ? 0 : Math.round(100 * (b.b - Math.min(Math.min(a.r, a.g), a.b)) / b.b);
            b.b = Math.round(b.b / 255 * 100);
            if (a.r == a.g && a.g == a.b) b.h = 0; else if (a.r >= a.g && a.g >= a.b) b.h = 60 * (a.g - a.b) / (a.r - a.b); else if (a.g >= a.r && a.r >= a.b) b.h = 60 + 60 * (a.g - a.r) / (a.g - a.b); else if (a.g >= a.b && a.b >= a.r) b.h = 120 + 60 * (a.b - a.r) / (a.g - a.r); else if (a.b >= a.g && a.g >= a.r) b.h = 180 + 60 * (a.b - a.g) / (a.b - a.r); else if (a.b >= a.r && a.r >= a.g) b.h = 240 + 60 * (a.r - a.g) / (a.b - a.g); else if (a.r >= a.b && a.b >= a.g) b.h = 300 + 60 * (a.r - a.b) / (a.r - a.g); else b.h = 0;
            b.h = Math.round(b.h);
            return b;
        }, P = function(a) {
            var b = {};
            var c = Math.round(a.h);
            var d = Math.round(a.s * 255 / 100);
            var e = Math.round(a.b * 255 / 100);
            if (d == 0) {
                b.r = b.g = b.b = e;
            } else {
                var f = e;
                var g = (255 - d) * e / 255;
                var h = (f - g) * (c % 60) / 60;
                if (c == 360) c = 0;
                if (c < 60) {
                    b.r = f;
                    b.b = g;
                    b.g = g + h;
                } else if (c < 120) {
                    b.g = f;
                    b.b = g;
                    b.r = f - h;
                } else if (c < 180) {
                    b.g = f;
                    b.r = g;
                    b.b = g + h;
                } else if (c < 240) {
                    b.b = f;
                    b.r = g;
                    b.g = f - h;
                } else if (c < 300) {
                    b.b = f;
                    b.g = g;
                    b.r = g + h;
                } else if (c < 360) {
                    b.r = f;
                    b.g = g;
                    b.b = f - h;
                } else {
                    b.r = 0;
                    b.g = 0;
                    b.b = 0;
                }
            }
            return {
                r: Math.round(b.r),
                g: Math.round(b.g),
                b: Math.round(b.b)
            };
        }, Q = function(b) {
            var c = [ b.r.toString(16), b.g.toString(16), b.b.toString(16) ];
            a.each(c, function(a, b) {
                if (b.length == 1) {
                    c[a] = "0" + b;
                }
            });
            return c.join("");
        }, R = function(a) {
            return Q(P(a));
        };
        return {
            init: function(b) {
                b = a.extend({}, g, b || {});
                if (typeof b.color == "string") {
                    b.color = N(b.color);
                } else if (b.color.r != undefined && b.color.g != undefined && b.color.b != undefined) {
                    b.color = O(b.color);
                } else if (b.color.h != undefined && b.color.s != undefined && b.color.b != undefined) {
                    b.color = J(b.color);
                } else {
                    return this;
                }
                b.origColor = b.color;
                return this.each(function() {
                    if (!a(this).data("colorpickerId")) {
                        var c = "collorpicker_" + parseInt(Math.random() * 1e3);
                        a(this).data("colorpickerId", c);
                        var d = a(f).attr("id", c);
                        if (b.flat) {
                            d.appendTo(this).show();
                        } else {
                            d.appendTo(document.body);
                        }
                        b.fields = d.find("input").bind("keydown", o).bind("change", p).bind("blur", q).bind("focus", r);
                        d.find("span").bind("mousedown", s);
                        b.selector = d.find("div.colorpicker_color").bind("mousedown", y);
                        b.selectorIndic = b.selector.find("div div");
                        b.hue = d.find("div.colorpicker_hue div");
                        d.find("div.colorpicker_hue").bind("mousedown", v);
                        b.newColor = d.find("div.colorpicker_new_color");
                        b.currentColor = d.find("div.colorpicker_current_color");
                        d.data("colorpicker", b);
                        d.find("input.colorpicker_none").bind("click", E);
                        d.find("input.colorpicker_submit").bind("click", D);
                        h(b.color, d.get(0));
                        i(b.color, d.get(0));
                        j(b.color, d.get(0));
                        l(b.color, d.get(0));
                        k(b.color, d.get(0));
                        m(b.color, d.get(0));
                        n(b.color, d.get(0));
                        if (b.flat) {
                            d.css({
                                position: "relative",
                                display: "block"
                            });
                        } else {
                            a(this).bind(b.eventName, F);
                        }
                    }
                });
            },
            showPicker: function() {
                return this.each(function() {
                    if (a(this).data("colorpickerId")) {
                        F.apply(this);
                    }
                });
            },
            hidePicker: function() {
                return this.each(function() {
                    if (a(this).data("colorpickerId")) {
                        a("#" + a(this).data("colorpickerId")).hide();
                    }
                });
            },
            setColor: function(b) {
                if (typeof b == "string") {
                    b = N(b);
                } else if (b.r != undefined && b.g != undefined && b.b != undefined) {
                    b = O(b);
                } else if (b.h != undefined && b.s != undefined && b.b != undefined) {
                    b = J(b);
                } else {
                    return this;
                }
                return this.each(function() {
                    if (a(this).data("colorpickerId")) {
                        var c = a("#" + a(this).data("colorpickerId"));
                        c.data("colorpicker").color = b;
                        c.data("colorpicker").origColor = b;
                        h(b, c.get(0));
                        i(b, c.get(0));
                        j(b, c.get(0));
                        l(b, c.get(0));
                        k(b, c.get(0));
                        m(b, c.get(0));
                        n(b, c.get(0));
                    }
                });
            }
        };
    }();
    a.fn.extend({
        ColorPicker: b.init,
        ColorPickerHide: b.hide,
        ColorPickerShow: b.show,
        ColorPickerSetColor: b.setColor
    });
})(jQuery);

(function(a) {
    a.fn.hoverIntent = function(b, c) {
        var d = {
            sensitivity: 7,
            interval: 100,
            timeout: 0
        };
        d = a.extend(d, c ? {
            over: b,
            out: c
        } : b);
        var e, f, g, h;
        var i = function(a) {
            e = a.pageX;
            f = a.pageY;
        };
        var j = function(b, c) {
            c.hoverIntent_t = clearTimeout(c.hoverIntent_t);
            if (Math.abs(g - e) + Math.abs(h - f) < d.sensitivity) {
                a(c).unbind("mousemove", i);
                c.hoverIntent_s = 1;
                return d.over.apply(c, [ b ]);
            } else {
                g = e;
                h = f;
                c.hoverIntent_t = setTimeout(function() {
                    j(b, c);
                }, d.interval);
            }
        };
        var k = function(a, b) {
            b.hoverIntent_t = clearTimeout(b.hoverIntent_t);
            b.hoverIntent_s = 0;
            return d.out.apply(b, [ a ]);
        };
        var l = function(b) {
            var c = jQuery.extend({}, b);
            var e = this;
            if (e.hoverIntent_t) {
                e.hoverIntent_t = clearTimeout(e.hoverIntent_t);
            }
            if (b.type == "mouseenter") {
                g = c.pageX;
                h = c.pageY;
                a(e).bind("mousemove", i);
                if (e.hoverIntent_s != 1) {
                    e.hoverIntent_t = setTimeout(function() {
                        j(c, e);
                    }, d.interval);
                }
            } else {
                a(e).unbind("mousemove", i);
                if (e.hoverIntent_s == 1) {
                    e.hoverIntent_t = setTimeout(function() {
                        k(c, e);
                    }, d.timeout);
                }
            }
        };
        return this.bind("mouseenter", l).bind("mouseleave", l);
    };
})(jQuery);

(function(a) {
    var b = null;
    var c = "blocktypes";
    var d = null;
    a.fn.liveUpdate = function(b, c) {
        return this.each(function() {
            new a.liveUpdate(this, b, c);
        });
    };
    a.liveUpdate = function(b, c, d) {
        this.field = a(b);
        this.list = a("#" + c);
        this.lutype = "blocktypes";
        if (typeof d != "undefined") {
            this.lutype = d;
        }
        if (this.list.length > 0) {
            this.init();
        }
    };
    a.liveUpdate.prototype = {
        init: function() {
            var a = this;
            this.setupCache();
            this.field.parents("form").submit(function() {
                return false;
            });
            this.field.keyup(function() {
                a.filter();
            });
            a.filter();
        },
        filter: function() {
            if (this.field.val() != d) {
                if (a.trim(this.field.val()) == "") {
                    if (this.lutype == "blocktypes") {
                        this.list.children("li").addClass("ccm-block-type-available");
                        this.list.children("li").removeClass("ccm-block-type-selected");
                    } else if (this.lutype == "attributes") {
                        this.list.children("li").addClass("ccm-attribute-available");
                        this.list.children("li").removeClass("ccm-attribute-selected");
                    } else if (this.lutype == "stacks") {
                        this.list.children("li").addClass("ccm-stack-available");
                        this.list.children("li").removeClass("ccm-stack-selected");
                    } else if (this.lutype == "intelligent-search") {
                        if (this.list.is(":visible")) {
                            this.list.hide();
                        }
                    } else {
                        this.list.children("li").show();
                    }
                    return;
                }
                if (this.lutype != "intelligent-search" || this.field.val().length > 2) {
                    this.displayResults(this.getScores(this.field.val().toLowerCase()));
                } else if (this.lutype == "intelligent-search") {
                    if (this.list.is(":visible")) {
                        this.list.hide();
                    }
                }
            }
            d = this.field.val();
            if (d == "" && this.lutype == "intelligent-search") {
                if (this.list.is(":visible")) {
                    this.list.hide();
                }
            }
        },
        setupCache: function() {
            var b = this;
            this.cache = [];
            this.rows = [];
            var c = this.lutype;
            this.list.find("li").each(function() {
                if (c == "blocktypes") {
                    b.cache.push(a(this).find("a.ccm-block-type-inner").html().toLowerCase());
                } else if (c == "attributes") {
                    var d = a(this).find("a,span").html().toLowerCase();
                    b.cache.push(d);
                } else if (c == "stacks") {
                    var d = a(this).find("a,span").html().toLowerCase();
                    b.cache.push(d);
                } else if (c == "fileset") {
                    b.cache.push(a(this).find("span").html().toLowerCase());
                } else if (c == "intelligent-search") {
                    var e = a(this).find("span").html();
                    if (e) {
                        b.cache.push(e.toLowerCase());
                    }
                }
                b.rows.push(a(this));
            });
            this.cache_length = this.cache.length;
        },
        displayResults: function(b) {
            var c = this;
            if (this.lutype == "blocktypes") {
                this.list.children("li").removeClass("ccm-block-type-available");
                this.list.children("li").removeClass("ccm-block-type-selected");
                a.each(b, function(a, b) {
                    c.rows[b[1]].addClass("ccm-block-type-available");
                });
                a(this.list.find("li.ccm-block-type-available")[0]).addClass("ccm-block-type-selected");
            } else if (this.lutype == "attributes") {
                this.list.children("li").removeClass("ccm-attribute-available");
                this.list.children("li").removeClass("ccm-attribute-selected");
                this.list.children("li").removeClass("ccm-item-selected");
                a.each(b, function(a, b) {
                    c.rows[b[1]].addClass("ccm-attribute-available");
                });
                this.list.children("li.item-select-list-header").removeClass("ccm-attribute-available");
                a(this.list.find("li.ccm-attribute-available")[0]).addClass("ccm-item-selected");
            } else if (this.lutype == "stacks") {
                this.list.children("li").removeClass("ccm-stack-available");
                this.list.children("li").removeClass("ccm-stack-selected");
                this.list.children("li").removeClass("ccm-item-selected");
                a.each(b, function(a, b) {
                    c.rows[b[1]].addClass("ccm-stack-available");
                });
                this.list.children("li.item-select-list-header").removeClass("ccm-stack-available");
                a(this.list.find("li.ccm-stack-available")[0]).addClass("ccm-item-selected");
            } else if (this.lutype == "intelligent-search") {
                if (!this.list.is(":visible")) {
                    this.list.fadeIn(160, "easeOutExpo");
                }
                this.list.find(".ccm-intelligent-search-results-module-onsite").hide();
                this.list.find("li").hide();
                var d = 0;
                a.each(b, function(a, b) {
                    $li = c.rows[b[1]];
                    if (b[0] > .7) {
                        d++;
                        if (!$li.parent().parent().is(":visible")) {
                            $li.parent().parent().show();
                        }
                        $li.show();
                    }
                });
                this.list.find("li a").removeClass("ccm-intelligent-search-result-selected");
                this.list.find("li:visible a:first").addClass("ccm-intelligent-search-result-selected");
            } else {
                this.list.children("li").hide();
                a.each(b, function(a, b) {
                    c.rows[b[1]].show();
                });
            }
        },
        getScores: function(a) {
            var b = [];
            for (var c = 0; c < this.cache_length; c++) {
                var d = this.cache[c].score(a);
                if (d > 0) {
                    b.push([ d, c ]);
                }
            }
            return b.sort(function(a, b) {
                return b[0] - a[0];
            });
        }
    };
})(jQuery);

(function($) {
    $.extend({
        metadata: {
            defaults: {
                type: "class",
                name: "metadata",
                cre: /({.*})/,
                single: "metadata"
            },
            setType: function(a, b) {
                this.defaults.type = a;
                this.defaults.name = b;
            },
            get: function(elem, opts) {
                var settings = $.extend({}, this.defaults, opts);
                if (!settings.single.length) settings.single = "metadata";
                var data = $.data(elem, settings.single);
                if (data) return data;
                data = "{}";
                if (settings.type == "class") {
                    var m = settings.cre.exec(elem.className);
                    if (m) data = m[1];
                } else if (settings.type == "elem") {
                    if (!elem.getElementsByTagName) return;
                    var e = elem.getElementsByTagName(settings.name);
                    if (e.length) data = $.trim(e[0].innerHTML);
                } else if (elem.getAttribute != undefined) {
                    var attr = elem.getAttribute(settings.name);
                    if (attr) data = attr;
                }
                if (data.indexOf("{") < 0) data = "{" + data + "}";
                data = eval("(" + data + ")");
                $.data(elem, settings.single, data);
                return data;
            }
        }
    });
    $.fn.metadata = function(a) {
        return $.metadata.get(this[0], a);
    };
})(jQuery);

((function() {
    var a, b, c, d;
    var e = function(a, b) {
        return function() {
            return a.apply(b, arguments);
        };
    };
    d = this;
    a = jQuery;
    a.fn.extend({
        chosen: function(c) {
            if (a.browser === "msie" && (a.browser.version === "6.0" || a.browser.version === "7.0")) {
                return this;
            }
            return a(this).each(function(d) {
                if (!a(this).hasClass("chzn-done")) {
                    return new b(this, c);
                }
            });
        }
    });
    b = function() {
        function b(b, c) {
            this.form_field = b;
            this.options = c != null ? c : {};
            this.set_default_values();
            this.form_field_jq = a(this.form_field);
            this.is_multiple = this.form_field.multiple;
            this.is_rtl = this.form_field_jq.hasClass("chzn-rtl");
            this.default_text_default = this.form_field.multiple ? "Select Some Options" : "Select an Option";
            this.set_up_html();
            this.register_observers();
            this.form_field_jq.addClass("chzn-done");
        }
        b.prototype.set_default_values = function() {
            this.click_test_action = e(function(a) {
                return this.test_active_click(a);
            }, this);
            this.activate_action = e(function(a) {
                return this.activate_field(a);
            }, this);
            this.active_field = false;
            this.mouse_on_container = false;
            this.results_showing = false;
            this.result_highlighted = null;
            this.result_single_selected = null;
            this.allow_single_deselect = this.options.allow_single_deselect != null && this.form_field.options[0].text === "" ? this.options.allow_single_deselect : false;
            this.disable_search_threshold = this.options.disable_search_threshold || 0;
            this.choices = 0;
            return this.results_none_found = this.options.no_results_text || "No results match";
        };
        b.prototype.set_up_html = function() {
            var b, d, e, f;
            this.container_id = this.form_field.id.length ? this.form_field.id.replace(/(:|\.)/g, "_") : this.generate_field_id();
            this.container_id += "_chzn";
            this.f_width = this.form_field_jq.outerWidth();
            this.default_text = this.form_field_jq.data("placeholder") ? this.form_field_jq.data("placeholder") : this.default_text_default;
            b = a("<div />", {
                id: this.container_id,
                "class": "chzn-container" + (this.is_rtl ? " chzn-rtl" : ""),
                style: "width: " + this.f_width + "px;"
            });
            if (this.is_multiple) {
                b.html('<ul class="chzn-choices"><li class="search-field"><input type="text" value="' + this.default_text + '" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chzn-drop" style="left:-9000px;"><ul class="chzn-results"></ul></div>');
            } else {
                b.html('<a href="javascript:void(0)" class="chzn-single"><span>' + this.default_text + '</span><div><b></b></div></a><div class="chzn-drop" style="left:-9000px;"><div class="chzn-search"><input type="text" autocomplete="off" /></div><ul class="chzn-results"></ul></div>');
            }
            this.form_field_jq.hide().after(b);
            this.container = a("#" + this.container_id);
            this.container.addClass("chzn-container-" + (this.is_multiple ? "multi" : "single"));
            if (!this.is_multiple && this.form_field.options.length <= this.disable_search_threshold) {
                this.container.addClass("chzn-container-single-nosearch");
            }
            this.dropdown = this.container.find("div.chzn-drop").first();
            d = this.container.height();
            e = this.f_width - c(this.dropdown);
            this.dropdown.css({
                width: e + "px",
                top: d + "px"
            });
            this.search_field = this.container.find("input").first();
            this.search_results = this.container.find("ul.chzn-results").first();
            this.search_field_scale();
            this.search_no_results = this.container.find("li.no-results").first();
            if (this.is_multiple) {
                this.search_choices = this.container.find("ul.chzn-choices").first();
                this.search_container = this.container.find("li.search-field").first();
            } else {
                this.search_container = this.container.find("div.chzn-search").first();
                this.selected_item = this.container.find(".chzn-single").first();
                f = e - c(this.search_container) - c(this.search_field);
                this.search_field.css({
                    width: f + "px"
                });
            }
            this.results_build();
            return this.set_tab_index();
        };
        b.prototype.register_observers = function() {
            this.container.mousedown(e(function(a) {
                return this.container_mousedown(a);
            }, this));
            this.container.mouseup(e(function(a) {
                return this.container_mouseup(a);
            }, this));
            this.container.mouseenter(e(function(a) {
                return this.mouse_enter(a);
            }, this));
            this.container.mouseleave(e(function(a) {
                return this.mouse_leave(a);
            }, this));
            this.search_results.mouseup(e(function(a) {
                return this.search_results_mouseup(a);
            }, this));
            this.search_results.mouseover(e(function(a) {
                return this.search_results_mouseover(a);
            }, this));
            this.search_results.mouseout(e(function(a) {
                return this.search_results_mouseout(a);
            }, this));
            this.form_field_jq.bind("liszt:updated", e(function(a) {
                return this.results_update_field(a);
            }, this));
            this.search_field.blur(e(function(a) {
                return this.input_blur(a);
            }, this));
            this.search_field.keyup(e(function(a) {
                return this.keyup_checker(a);
            }, this));
            this.search_field.keydown(e(function(a) {
                return this.keydown_checker(a);
            }, this));
            if (this.is_multiple) {
                this.search_choices.click(e(function(a) {
                    return this.choices_click(a);
                }, this));
                return this.search_field.focus(e(function(a) {
                    return this.input_focus(a);
                }, this));
            }
        };
        b.prototype.search_field_disabled = function() {
            this.is_disabled = this.form_field_jq.attr("disabled");
            if (this.is_disabled) {
                this.container.addClass("chzn-disabled");
                this.search_field.attr("disabled", true);
                if (!this.is_multiple) {
                    this.selected_item.unbind("focus", this.activate_action);
                }
                return this.close_field();
            } else {
                this.container.removeClass("chzn-disabled");
                this.search_field.attr("disabled", false);
                if (!this.is_multiple) {
                    return this.selected_item.bind("focus", this.activate_action);
                }
            }
        };
        b.prototype.container_mousedown = function(b) {
            var c;
            if (!this.is_disabled) {
                c = b != null ? a(b.target).hasClass("search-choice-close") : false;
                if (b && b.type === "mousedown") {
                    b.stopPropagation();
                }
                if (!this.pending_destroy_click && !c) {
                    if (!this.active_field) {
                        if (this.is_multiple) {
                            this.search_field.val("");
                        }
                        a(document).click(this.click_test_action);
                        this.results_show();
                    } else if (!this.is_multiple && b && (a(b.target) === this.selected_item || a(b.target).parents("a.chzn-single").length)) {
                        b.preventDefault();
                        this.results_toggle();
                    }
                    return this.activate_field();
                } else {
                    return this.pending_destroy_click = false;
                }
            }
        };
        b.prototype.container_mouseup = function(a) {
            if (a.target.nodeName === "ABBR") {
                return this.results_reset(a);
            }
        };
        b.prototype.mouse_enter = function() {
            return this.mouse_on_container = true;
        };
        b.prototype.mouse_leave = function() {
            return this.mouse_on_container = false;
        };
        b.prototype.input_focus = function(a) {
            if (!this.active_field) {
                return setTimeout(e(function() {
                    return this.container_mousedown();
                }, this), 50);
            }
        };
        b.prototype.input_blur = function(a) {
            if (!this.mouse_on_container) {
                this.active_field = false;
                return setTimeout(e(function() {
                    return this.blur_test();
                }, this), 100);
            }
        };
        b.prototype.blur_test = function(a) {
            if (!this.active_field && this.container.hasClass("chzn-container-active")) {
                return this.close_field();
            }
        };
        b.prototype.close_field = function() {
            a(document).unbind("click", this.click_test_action);
            if (!this.is_multiple) {
                this.selected_item.attr("tabindex", this.search_field.attr("tabindex"));
                this.search_field.attr("tabindex", -1);
            }
            this.active_field = false;
            this.results_hide();
            this.container.removeClass("chzn-container-active");
            this.winnow_results_clear();
            this.clear_backstroke();
            this.show_search_field_default();
            return this.search_field_scale();
        };
        b.prototype.activate_field = function() {
            if (!this.is_multiple && !this.active_field) {
                this.search_field.attr("tabindex", this.selected_item.attr("tabindex"));
                this.selected_item.attr("tabindex", -1);
            }
            this.container.addClass("chzn-container-active");
            this.active_field = true;
            this.search_field.val(this.search_field.val());
            return this.search_field.focus();
        };
        b.prototype.test_active_click = function(b) {
            if (a(b.target).parents("#" + this.container_id).length) {
                return this.active_field = true;
            } else {
                return this.close_field();
            }
        };
        b.prototype.results_build = function() {
            var a, b, c, e, f, g;
            c = new Date;
            this.parsing = true;
            this.results_data = d.SelectParser.select_to_array(this.form_field);
            if (this.is_multiple && this.choices > 0) {
                this.search_choices.find("li.search-choice").remove();
                this.choices = 0;
            } else if (!this.is_multiple) {
                this.selected_item.find("span").text(this.default_text);
            }
            a = "";
            g = this.results_data;
            for (e = 0, f = g.length; e < f; e++) {
                b = g[e];
                if (b.group) {
                    a += this.result_add_group(b);
                } else if (!b.empty) {
                    a += this.result_add_option(b);
                    if (b.selected && this.is_multiple) {
                        this.choice_build(b);
                    } else if (b.selected && !this.is_multiple) {
                        this.selected_item.find("span").text(b.text);
                        if (this.allow_single_deselect) {
                            this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>');
                        }
                    }
                }
            }
            this.search_field_disabled();
            this.show_search_field_default();
            this.search_field_scale();
            this.search_results.html(a);
            return this.parsing = false;
        };
        b.prototype.result_add_group = function(b) {
            if (!b.disabled) {
                b.dom_id = this.container_id + "_g_" + b.array_index;
                return '<li id="' + b.dom_id + '" class="group-result">' + a("<div />").text(b.label).html() + "</li>";
            } else {
                return "";
            }
        };
        b.prototype.result_add_option = function(a) {
            var b, c;
            if (!a.disabled) {
                a.dom_id = this.container_id + "_o_" + a.array_index;
                b = a.selected && this.is_multiple ? [] : [ "active-result" ];
                if (a.selected) {
                    b.push("result-selected");
                }
                if (a.group_array_index != null) {
                    b.push("group-option");
                }
                if (a.classes !== "") {
                    b.push(a.classes);
                }
                c = a.style.cssText !== "" ? ' style="' + a.style + '"' : "";
                return '<li id="' + a.dom_id + '" class="' + b.join(" ") + '"' + c + ">" + a.html + "</li>";
            } else {
                return "";
            }
        };
        b.prototype.results_update_field = function() {
            this.result_clear_highlight();
            this.result_single_selected = null;
            return this.results_build();
        };
        b.prototype.result_do_highlight = function(a) {
            var b, c, d, e, f;
            if (a.length) {
                this.result_clear_highlight();
                this.result_highlight = a;
                this.result_highlight.addClass("highlighted");
                d = parseInt(this.search_results.css("maxHeight"), 10);
                f = this.search_results.scrollTop();
                e = d + f;
                c = this.result_highlight.position().top + this.search_results.scrollTop();
                b = c + this.result_highlight.outerHeight();
                if (b >= e) {
                    return this.search_results.scrollTop(b - d > 0 ? b - d : 0);
                } else if (c < f) {
                    return this.search_results.scrollTop(c);
                }
            }
        };
        b.prototype.result_clear_highlight = function() {
            if (this.result_highlight) {
                this.result_highlight.removeClass("highlighted");
            }
            return this.result_highlight = null;
        };
        b.prototype.results_toggle = function() {
            if (this.results_showing) {
                return this.results_hide();
            } else {
                return this.results_show();
            }
        };
        b.prototype.results_show = function() {
            var a;
            if (!this.is_multiple) {
                this.selected_item.addClass("chzn-single-with-drop");
                if (this.result_single_selected) {
                    this.result_do_highlight(this.result_single_selected);
                }
            }
            a = this.is_multiple ? this.container.height() : this.container.height() - 1;
            this.dropdown.css({
                top: a + "px",
                left: 0
            });
            this.results_showing = true;
            this.search_field.focus();
            this.search_field.val(this.search_field.val());
            return this.winnow_results();
        };
        b.prototype.results_hide = function() {
            if (!this.is_multiple) {
                this.selected_item.removeClass("chzn-single-with-drop");
            }
            this.result_clear_highlight();
            this.dropdown.css({
                left: "-9000px"
            });
            return this.results_showing = false;
        };
        b.prototype.set_tab_index = function(a) {
            var b;
            if (this.form_field_jq.attr("tabindex")) {
                b = this.form_field_jq.attr("tabindex");
                this.form_field_jq.attr("tabindex", -1);
                if (this.is_multiple) {
                    return this.search_field.attr("tabindex", b);
                } else {
                    this.selected_item.attr("tabindex", b);
                    return this.search_field.attr("tabindex", -1);
                }
            }
        };
        b.prototype.show_search_field_default = function() {
            if (this.is_multiple && this.choices < 1 && !this.active_field) {
                this.search_field.val(this.default_text);
                return this.search_field.addClass("default");
            } else {
                this.search_field.val("");
                return this.search_field.removeClass("default");
            }
        };
        b.prototype.search_results_mouseup = function(b) {
            var c;
            c = a(b.target).hasClass("active-result") ? a(b.target) : a(b.target).parents(".active-result").first();
            if (c.length) {
                this.result_highlight = c;
                return this.result_select(b);
            }
        };
        b.prototype.search_results_mouseover = function(b) {
            var c;
            c = a(b.target).hasClass("active-result") ? a(b.target) : a(b.target).parents(".active-result").first();
            if (c) {
                return this.result_do_highlight(c);
            }
        };
        b.prototype.search_results_mouseout = function(b) {
            if (a(b.target).hasClass("active-result" || a(b.target).parents(".active-result").first())) {
                return this.result_clear_highlight();
            }
        };
        b.prototype.choices_click = function(b) {
            b.preventDefault();
            if (this.active_field && !a(b.target).hasClass("search-choice" || a(b.target).parents(".search-choice").first) && !this.results_showing) {
                return this.results_show();
            }
        };
        b.prototype.choice_build = function(b) {
            var c, d;
            c = this.container_id + "_c_" + b.array_index;
            this.choices += 1;
            this.search_container.before('<li class="search-choice" id="' + c + '"><span>' + b.html + '</span><a href="javascript:void(0)" class="search-choice-close" rel="' + b.array_index + '"></a></li>');
            d = a("#" + c).find("a").first();
            return d.click(e(function(a) {
                return this.choice_destroy_link_click(a);
            }, this));
        };
        b.prototype.choice_destroy_link_click = function(b) {
            b.preventDefault();
            if (!this.is_disabled) {
                this.pending_destroy_click = true;
                return this.choice_destroy(a(b.target));
            } else {
                return b.stopPropagation;
            }
        };
        b.prototype.choice_destroy = function(a) {
            this.choices -= 1;
            this.show_search_field_default();
            if (this.is_multiple && this.choices > 0 && this.search_field.val().length < 1) {
                this.results_hide();
            }
            this.result_deselect(a.attr("rel"));
            return a.parents("li").first().remove();
        };
        b.prototype.results_reset = function(b) {
            this.form_field.options[0].selected = true;
            this.selected_item.find("span").text(this.default_text);
            this.show_search_field_default();
            a(b.target).remove();
            this.form_field_jq.trigger("change");
            if (this.active_field) {
                return this.results_hide();
            }
        };
        b.prototype.result_select = function(a) {
            var b, c, d, e;
            if (this.result_highlight) {
                b = this.result_highlight;
                c = b.attr("id");
                this.result_clear_highlight();
                if (this.is_multiple) {
                    this.result_deactivate(b);
                } else {
                    this.search_results.find(".result-selected").removeClass("result-selected");
                    this.result_single_selected = b;
                }
                b.addClass("result-selected");
                e = c.substr(c.lastIndexOf("_") + 1);
                d = this.results_data[e];
                d.selected = true;
                this.form_field.options[d.options_index].selected = true;
                if (this.is_multiple) {
                    this.choice_build(d);
                } else {
                    this.selected_item.find("span").first().text(d.text);
                    if (this.allow_single_deselect) {
                        this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>');
                    }
                }
                if (!(a.metaKey && this.is_multiple)) {
                    this.results_hide();
                }
                this.search_field.val("");
                this.form_field_jq.trigger("change");
                return this.search_field_scale();
            }
        };
        b.prototype.result_activate = function(a) {
            return a.addClass("active-result");
        };
        b.prototype.result_deactivate = function(a) {
            return a.removeClass("active-result");
        };
        b.prototype.result_deselect = function(b) {
            var c, d;
            d = this.results_data[b];
            d.selected = false;
            this.form_field.options[d.options_index].selected = false;
            c = a("#" + this.container_id + "_o_" + b);
            c.removeClass("result-selected").addClass("active-result").show();
            this.result_clear_highlight();
            this.winnow_results();
            this.form_field_jq.trigger("change");
            return this.search_field_scale();
        };
        b.prototype.results_search = function(a) {
            if (this.results_showing) {
                return this.winnow_results();
            } else {
                return this.results_show();
            }
        };
        b.prototype.winnow_results = function() {
            var b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r;
            j = new Date;
            this.no_results_clear();
            h = 0;
            i = this.search_field.val() === this.default_text ? "" : a("<div/>").text(a.trim(this.search_field.val())).html();
            f = new RegExp("^" + i.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), "i");
            m = new RegExp(i.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), "i");
            r = this.results_data;
            for (n = 0, p = r.length; n < p; n++) {
                c = r[n];
                if (!c.disabled && !c.empty) {
                    if (c.group) {
                        a("#" + c.dom_id).hide();
                    } else if (!(this.is_multiple && c.selected)) {
                        b = false;
                        g = c.dom_id;
                        if (f.test(c.html)) {
                            b = true;
                            h += 1;
                        } else if (c.html.indexOf(" ") >= 0 || c.html.indexOf("[") === 0) {
                            e = c.html.replace(/\[|\]/g, "").split(" ");
                            if (e.length) {
                                for (o = 0, q = e.length; o < q; o++) {
                                    d = e[o];
                                    if (f.test(d)) {
                                        b = true;
                                        h += 1;
                                    }
                                }
                            }
                        }
                        if (b) {
                            if (i.length) {
                                k = c.html.search(m);
                                l = c.html.substr(0, k + i.length) + "</em>" + c.html.substr(k + i.length);
                                l = l.substr(0, k) + "<em>" + l.substr(k);
                            } else {
                                l = c.html;
                            }
                            if (a("#" + g).html !== l) {
                                a("#" + g).html(l);
                            }
                            this.result_activate(a("#" + g));
                            if (c.group_array_index != null) {
                                a("#" + this.results_data[c.group_array_index].dom_id).show();
                            }
                        } else {
                            if (this.result_highlight && g === this.result_highlight.attr("id")) {
                                this.result_clear_highlight();
                            }
                            this.result_deactivate(a("#" + g));
                        }
                    }
                }
            }
            if (h < 1 && i.length) {
                return this.no_results(i);
            } else {
                return this.winnow_results_set_highlight();
            }
        };
        b.prototype.winnow_results_clear = function() {
            var b, c, d, e, f;
            this.search_field.val("");
            c = this.search_results.find("li");
            f = [];
            for (d = 0, e = c.length; d < e; d++) {
                b = c[d];
                b = a(b);
                f.push(b.hasClass("group-result") ? b.show() : !this.is_multiple || !b.hasClass("result-selected") ? this.result_activate(b) : void 0);
            }
            return f;
        };
        b.prototype.winnow_results_set_highlight = function() {
            var a, b;
            if (!this.result_highlight) {
                b = !this.is_multiple ? this.search_results.find(".result-selected.active-result") : [];
                a = b.length ? b.first() : this.search_results.find(".active-result").first();
                if (a != null) {
                    return this.result_do_highlight(a);
                }
            }
        };
        b.prototype.no_results = function(b) {
            var c;
            c = a('<li class="no-results">' + this.results_none_found + ' "<span></span>"</li>');
            c.find("span").first().html(b);
            return this.search_results.append(c);
        };
        b.prototype.no_results_clear = function() {
            return this.search_results.find(".no-results").remove();
        };
        b.prototype.keydown_arrow = function() {
            var b, c;
            if (!this.result_highlight) {
                b = this.search_results.find("li.active-result").first();
                if (b) {
                    this.result_do_highlight(a(b));
                }
            } else if (this.results_showing) {
                c = this.result_highlight.nextAll("li.active-result").first();
                if (c) {
                    this.result_do_highlight(c);
                }
            }
            if (!this.results_showing) {
                return this.results_show();
            }
        };
        b.prototype.keyup_arrow = function() {
            var a;
            if (!this.results_showing && !this.is_multiple) {
                return this.results_show();
            } else if (this.result_highlight) {
                a = this.result_highlight.prevAll("li.active-result");
                if (a.length) {
                    return this.result_do_highlight(a.first());
                } else {
                    if (this.choices > 0) {
                        this.results_hide();
                    }
                    return this.result_clear_highlight();
                }
            }
        };
        b.prototype.keydown_backstroke = function() {
            if (this.pending_backstroke) {
                this.choice_destroy(this.pending_backstroke.find("a").first());
                return this.clear_backstroke();
            } else {
                this.pending_backstroke = this.search_container.siblings("li.search-choice").last();
                return this.pending_backstroke.addClass("search-choice-focus");
            }
        };
        b.prototype.clear_backstroke = function() {
            if (this.pending_backstroke) {
                this.pending_backstroke.removeClass("search-choice-focus");
            }
            return this.pending_backstroke = null;
        };
        b.prototype.keyup_checker = function(a) {
            var b, c;
            b = (c = a.which) != null ? c : a.keyCode;
            this.search_field_scale();
            switch (b) {
              case 8:
                if (this.is_multiple && this.backstroke_length < 1 && this.choices > 0) {
                    return this.keydown_backstroke();
                } else if (!this.pending_backstroke) {
                    this.result_clear_highlight();
                    return this.results_search();
                }
                break;
              case 13:
                a.preventDefault();
                if (this.results_showing) {
                    return this.result_select(a);
                }
                break;
              case 27:
                if (this.results_showing) {
                    return this.results_hide();
                }
                break;
              case 9:
              case 38:
              case 40:
              case 16:
              case 91:
              case 17:
                break;
              default:
                return this.results_search();
            }
        };
        b.prototype.keydown_checker = function(a) {
            var b, c;
            b = (c = a.which) != null ? c : a.keyCode;
            this.search_field_scale();
            if (b !== 8 && this.pending_backstroke) {
                this.clear_backstroke();
            }
            switch (b) {
              case 8:
                this.backstroke_length = this.search_field.val().length;
                break;
              case 9:
                this.mouse_on_container = false;
                break;
              case 13:
                a.preventDefault();
                break;
              case 38:
                a.preventDefault();
                this.keyup_arrow();
                break;
              case 40:
                this.keydown_arrow();
                break;
            }
        };
        b.prototype.search_field_scale = function() {
            var b, c, d, e, f, g, h, i, j;
            if (this.is_multiple) {
                d = 0;
                h = 0;
                f = "position:absolute; left: -1000px; top: -1000px; display:none;";
                g = [ "font-size", "font-style", "font-weight", "font-family", "line-height", "text-transform", "letter-spacing" ];
                for (i = 0, j = g.length; i < j; i++) {
                    e = g[i];
                    f += e + ":" + this.search_field.css(e) + ";";
                }
                c = a("<div />", {
                    style: f
                });
                c.text(this.search_field.val());
                a("body").append(c);
                h = c.width() + 25;
                c.remove();
                if (h > this.f_width - 10) {
                    h = this.f_width - 10;
                }
                this.search_field.css({
                    width: h + "px"
                });
                b = this.container.height();
                return this.dropdown.css({
                    top: b + "px"
                });
            }
        };
        b.prototype.generate_field_id = function() {
            var a;
            a = this.generate_random_id();
            this.form_field.id = a;
            return a;
        };
        b.prototype.generate_random_id = function() {
            var b;
            b = "sel" + this.generate_random_char() + this.generate_random_char() + this.generate_random_char();
            while (a("#" + b).length > 0) {
                b += this.generate_random_char();
            }
            return b;
        };
        b.prototype.generate_random_char = function() {
            var a, b, c;
            a = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ";
            c = Math.floor(Math.random() * a.length);
            return b = a.substring(c, c + 1);
        };
        return b;
    }();
    c = function(a) {
        var b;
        return b = a.outerWidth() - a.width();
    };
    d.get_side_border_padding = c;
})).call(this);

((function() {
    var a;
    a = function() {
        function a() {
            this.options_index = 0;
            this.parsed = [];
        }
        a.prototype.add_node = function(a) {
            if (a.nodeName === "OPTGROUP") {
                return this.add_group(a);
            } else {
                return this.add_option(a);
            }
        };
        a.prototype.add_group = function(a) {
            var b, c, d, e, f, g;
            b = this.parsed.length;
            this.parsed.push({
                array_index: b,
                group: true,
                label: a.label,
                children: 0,
                disabled: a.disabled
            });
            f = a.childNodes;
            g = [];
            for (d = 0, e = f.length; d < e; d++) {
                c = f[d];
                g.push(this.add_option(c, b, a.disabled));
            }
            return g;
        };
        a.prototype.add_option = function(a, b, c) {
            if (a.nodeName === "OPTION") {
                if (a.text !== "") {
                    if (b != null) {
                        this.parsed[b].children += 1;
                    }
                    this.parsed.push({
                        array_index: this.parsed.length,
                        options_index: this.options_index,
                        value: a.value,
                        text: a.text,
                        html: a.innerHTML,
                        selected: a.selected,
                        disabled: c === true ? c : a.disabled,
                        group_array_index: b,
                        classes: a.className,
                        style: a.style.cssText
                    });
                } else {
                    this.parsed.push({
                        array_index: this.parsed.length,
                        options_index: this.options_index,
                        empty: true
                    });
                }
                return this.options_index += 1;
            }
        };
        return a;
    }();
    a.select_to_array = function(b) {
        var c, d, e, f, g;
        d = new a;
        g = b.childNodes;
        for (e = 0, f = g.length; e < f; e++) {
            c = g[e];
            d.add_node(c);
        }
        return d.parsed;
    };
    this.SelectParser = a;
})).call(this);

ccm_closeDashboardPane = function(a) {
    $(a).closest("div.ccm-pane").fadeOut(120, "easeOutExpo");
};

ccm_getDashboardBackgroundImageData = function(a, b) {
    $.getJSON(CCM_TOOLS_PATH + "/dashboard/get_image_data", {
        image: a
    }, function(a) {
        if (a && b) {
            var c = "<div>";
            c += "<strong>" + a.title + "</strong> " + ccmi18n.authoredBy + " ";
            if (a.link) {
                c += '<a target="_blank" href="' + a.link + '">' + a.author + "</a>";
            } else {
                c += a.author;
            }
            $('<div id="ccm-dashboard-image-caption" class="ccm-ui"/>').html(c).appendTo(document.body).show();
            setTimeout(function() {
                $("#ccm-dashboard-image-caption").fadeOut(1e3, "easeOutExpo");
            }, 5e3);
        }
    });
};

ccm_dashboardEqualizeMenus = function() {
    if ($(window).width() < 560) {
        $("div.dashboard-icon-list div.well").css("visibility", "visible");
        return false;
    }
    var a = -1;
    var b;
    var c = 0;
    var d = new Array;
    $("ul.nav-list").each(function() {
        if ($(this).position().top != c) {
            a++;
            d[a] = new Array;
        }
        d[a].push($(this));
        c = $(this).position().top;
    });
    for (b = 0; b < d.length; b++) {
        var e = 0;
        for (a = 0; a < d[b].length; a++) {
            var f = d[b][a];
            if (f.height() > e) {
                e = f.height();
            }
        }
        for (a = 0; a < d[b].length; a++) {
            var f = d[b][a];
            f.css("height", e);
        }
    }
    $("div.dashboard-icon-list div.well").css("visibility", "visible");
};

$(function() {
    ccm_activateToolbar();
    $("#ccm-page-help").popover({
        trigger: "click",
        content: function() {
            var a = $(this).attr("id") + "-content";
            return $("#" + a).html();
        },
        placement: "bottom",
        html: true
    });
    $(".launch-tooltip").tooltip({
        placement: "bottom"
    });
    if ($("#ccm-dashboard-result-message").length > 0) {
        if ($(".ccm-pane").length > 0) {
            var a = $(".ccm-pane").parent().attr("class");
            var b = $(".ccm-pane").parent().parent().attr("class");
            var c = $("#ccm-dashboard-result-message").html();
            $("#ccm-dashboard-result-message").html('<div class="' + b + '"><div class="' + a + '">' + c + "</div></div>").fadeIn(400);
        }
    } else {
        $("#ccm-dashboard-result-message").fadeIn(200);
    }
});

var ccm_totalAdvancedSearchFields = 0;

var ccm_alLaunchType = new Array;

var ccm_alActiveAssetField = "";

var ccm_alProcessorTarget = "";

var ccm_alDebug = false;

ccm_triggerSelectFile = function(a, b) {
    if (b == null) {
        var b = ccm_alActiveAssetField;
    }
    var c = $("#" + b + "-fm-selected");
    var d = $("#" + b + "-fm-display");
    d.hide();
    c.show();
    c.load(CCM_TOOLS_PATH + "/files/selector_data?fID=" + a + "&ccm_file_selected_field=" + b, function() {
        c.attr("fID", a);
        c.attr("ccm-file-manager-can-view", c.children("div").attr("ccm-file-manager-can-view"));
        c.attr("ccm-file-manager-can-edit", c.children("div").attr("ccm-file-manager-can-edit"));
        c.attr("ccm-file-manager-can-admin", c.children("div").attr("ccm-file-manager-can-admin"));
        c.attr("ccm-file-manager-can-replace", c.children("div").attr("ccm-file-manager-can-replace"));
        c.attr("ccm-file-manager-instance", b);
        c.click(function(a) {
            a.stopPropagation();
            ccm_alActivateMenu($(this), a);
        });
        if (typeof ccm_triggerSelectFileComplete == "function") {
            ccm_triggerSelectFileComplete(a, b);
        }
    });
    var e = $("#" + b + "-fm-value");
    e.attr("value", a);
    ccm_alSetupFileProcessor();
};

ccm_alGetFileData = function(a, b) {
    $.getJSON(CCM_TOOLS_PATH + "/files/get_data.php?fID=" + a, function(a) {
        b(a);
    });
};

ccm_clearFile = function(a, b) {
    a.stopPropagation();
    var c = $("#" + b + "-fm-selected");
    var d = $("#" + b + "-fm-display");
    var e = $("#" + b + "-fm-value");
    e.attr("value", 0);
    c.hide();
    d.show();
};

ccm_activateFileManager = function(a, b) {
    ccm_alLaunchType[b] = a;
    ccm_alSetupSelectFiles(b);
    $(document).click(function(a) {
        a.stopPropagation();
        ccm_alSelectNone();
    });
    ccm_setupAdvancedSearch(b);
    if (a == "DASHBOARD") {
        $(".dialog-launch").dialog();
    }
    ccm_alSetupCheckboxes(b);
    ccm_alSetupFileProcessor();
    ccm_alSetupSingleUploadForm();
    $("form#ccm-" + b + "-advanced-search select[name=fssID]").change(function() {
        if (a == "DASHBOARD") {
            window.location.href = CCM_DISPATCHER_FILENAME + "/dashboard/files/search?fssID=" + $(this).val();
        } else {
            jQuery.fn.dialog.showLoader();
            var c = $("div#ccm-" + b + "-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1&fssID=" + $(this).val();
            $.get(c, function(a) {
                jQuery.fn.dialog.hideLoader();
                $("div#ccm-" + b + "-overlay-wrapper").html(a);
                $("div#ccm-" + b + "-overlay-wrapper a.dialog-launch").dialog();
            });
        }
    });
    ccm_searchActivatePostFunction[b] = function() {
        ccm_alSetupCheckboxes(b);
        ccm_alSetupSelectFiles(b);
        ccm_alSetupSingleUploadForm();
    };
};

ccm_alSetupSingleUploadForm = function() {
    $(".ccm-file-manager-submit-single").submit(function() {
        $(this).attr("target", ccm_alProcessorTarget);
        ccm_alSubmitSingle($(this).get(0));
    });
};

ccm_activateFileSelectors = function() {
    $(".ccm-file-manager-launch").unbind();
    $(".ccm-file-manager-launch").click(function() {
        ccm_alLaunchSelectorFileManager($(this).parent().attr("ccm-file-manager-field"));
    });
};

ccm_alLaunchSelectorFileManager = function(a) {
    ccm_alActiveAssetField = a;
    var b = "";
    var c = $("#" + a + "-fm-display input.ccm-file-manager-filter");
    if (c.length) {
        for (i = 0; i < c.length; i++) {
            b += "&" + $(c[i]).attr("name") + "=" + $(c[i]).attr("value");
        }
    }
    ccm_launchFileManager(b);
};

ccm_launchFileManager = function(a) {
    $.fn.dialog.open({
        width: "90%",
        height: "70%",
        appendButtons: true,
        modal: false,
        href: CCM_TOOLS_PATH + "/files/search_dialog?ocID=" + CCM_CID + "&search=1" + a,
        title: ccmi18n_filemanager.title
    });
};

ccm_launchFileSetPicker = function(a) {
    $.fn.dialog.open({
        width: 500,
        height: 160,
        modal: false,
        href: CCM_TOOLS_PATH + "/files/pick_set?oldFSID=" + a,
        title: ccmi18n_filemanager.sets
    });
};

ccm_alSubmitSetsForm = function(a) {
    ccm_deactivateSearchResults(a);
    jQuery.fn.dialog.showLoader();
    $("#ccm-" + a + "-add-to-set-form").ajaxSubmit(function(b) {
        jQuery.fn.dialog.closeTop();
        jQuery.fn.dialog.hideLoader();
        $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
            $("#ccm-" + a + "-sets-search-wrapper").load(CCM_TOOLS_PATH + "/files/search_sets_reload", {
                searchInstance: a
            }, function() {
                $(".chosen-select").chosen();
                ccm_parseAdvancedSearchResponse(b, a);
            });
        });
    });
};

ccm_alSubmitPasswordForm = function(a) {
    ccm_deactivateSearchResults(a);
    $("#ccm-" + a + "-password-form").ajaxSubmit(function(b) {
        jQuery.fn.dialog.closeTop();
        $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
            ccm_parseAdvancedSearchResponse(b, a);
        });
    });
};

ccm_alSubmitStorageForm = function(a) {
    ccm_deactivateSearchResults(a);
    $("#ccm-" + a + "-storage-form").ajaxSubmit(function(b) {
        jQuery.fn.dialog.closeTop();
        $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
            ccm_parseAdvancedSearchResponse(b, a);
        });
    });
};

ccm_alSubmitPermissionsForm = function(a) {
    ccm_deactivateSearchResults(a);
    $("#ccm-" + a + "-permissions-form").ajaxSubmit(function(b) {
        jQuery.fn.dialog.closeTop();
        $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
            ccm_parseAdvancedSearchResponse(b, a);
        });
    });
};

ccm_alSetupSetsForm = function(a) {
    $("#fsAddToSearchName").liveUpdate("ccm-file-search-add-to-sets-list", "fileset");
    $(".ccm-file-set-add-cb a").each(function() {
        var a = $(this);
        var b = a.attr("ccm-tri-state-startup");
        $(this).click(function() {
            var a = $(this).attr("ccm-tri-state-selected");
            var c = 0;
            switch (a) {
              case "0":
                if (b == "1") {
                    c = "1";
                } else {
                    c = "2";
                }
                break;
              case "1":
                c = "2";
                break;
              case "2":
                c = "0";
                break;
            }
            $(this).attr("ccm-tri-state-selected", c);
            $(this).find("input").val(c);
            $(this).find("img").attr("src", CCM_IMAGE_PATH + "/checkbox_state_" + c + ".png");
        });
    });
    $("#ccm-" + a + "-add-to-set-form input[name=fsNew]").click(function() {
        if (!$(this).prop("checked")) {
            $("#ccm-" + a + "-add-to-set-form input[name=fsNewText]").val("");
        }
    });
    $("#ccm-" + a + "-add-to-set-form").submit(function() {
        ccm_alSubmitSetsForm(a);
        return false;
    });
};

ccm_alSetupPasswordForm = function() {
    $("#ccm-file-password-form").submit(function() {
        ccm_alSubmitPasswordForm();
        return false;
    });
};

ccm_alRescanFiles = function() {
    var a = CCM_TOOLS_PATH + "/files/rescan?";
    var b = arguments;
    for (i = 0; i < b.length; i++) {
        a += "fID[]=" + b[i] + "&";
    }
    $.fn.dialog.open({
        title: ccmi18n_filemanager.rescan,
        href: a,
        width: 350,
        modal: false,
        height: 200,
        onClose: function() {
            if (b.length == 1) {
                $("#ccm-file-properties-wrapper").html("");
                jQuery.fn.dialog.showLoader();
                $("#ccm-file-properties-wrapper").load(CCM_TOOLS_PATH + "/files/properties?fID=" + b[0] + "&reload=1", false, function() {
                    jQuery.fn.dialog.hideLoader();
                    $(this).find(".dialog-launch").dialog();
                });
            }
        }
    });
};

ccm_alSelectPermissionsEntity = function(a, b, c) {
    var d = $("#ccm-file-permissions-entity-base").html();
    $("#ccm-file-permissions-entities-wrapper").append('<div class="ccm-file-permissions-entity">' + d + "</div>");
    var e = $(".ccm-file-permissions-entity");
    var f = e[e.length - 1];
    $(f).find("h3 span").html(c);
    $(f).find("input[type=hidden]").val(a + "_" + b);
    $(f).find("select").each(function() {
        $(this).attr("name", $(this).attr("name") + "_" + a + "_" + b);
    });
    $(f).find("div.ccm-file-access-extensions input[type=checkbox]").each(function() {
        $(this).attr("name", $(this).attr("name") + "_" + a + "_" + b + "[]");
    });
    ccm_alActivateFilePermissionsSelector();
};

ccm_alActivateFilePermissionsSelector = function() {
    $(".ccm-file-access-add select").unbind();
    $(".ccm-file-access-add select").change(function() {
        var a = $(this).parents("div.ccm-file-permissions-entity")[0];
        if ($(this).val() == ccmi18n_filemanager.PTYPE_CUSTOM) {
            $(a).find("div.ccm-file-access-add-extensions").show();
        } else {
            $(a).find("div.ccm-file-access-add-extensions").hide();
        }
    });
    $(".ccm-file-access-file-manager select").change(function() {
        var a = $(this).parents("div.ccm-file-permissions-entity")[0];
        if ($(this).val() != ccmi18n_filemanager.PTYPE_NONE) {
            $(a).find(".ccm-file-access-add").show();
            $(a).find(".ccm-file-access-edit").show();
            $(a).find(".ccm-file-access-admin").show();
        } else {
            $(a).find(".ccm-file-access-add").hide();
            $(a).find(".ccm-file-access-edit").hide();
            $(a).find(".ccm-file-access-admin").hide();
            $(a).find("div.ccm-file-access-add-extensions").hide();
        }
    });
    $("a.ccm-file-permissions-remove").click(function() {
        $(this).parent().parent().fadeOut(100, function() {
            $(this).remove();
        });
    });
    $("input[name=toggleCanAddExtension]").unbind();
    $("input[name=toggleCanAddExtension]").click(function() {
        var a = $(this).parent().parent().find("div.ccm-file-access-extensions");
        if ($(this).prop("checked") == 1) {
            a.find("input").attr("checked", true);
        } else {
            a.find("input").attr("checked", false);
        }
    });
};

ccm_alSetupVersionSelector = function() {
    $("#ccm-file-versions-grid input[type=radio]").click(function() {
        $("#ccm-file-versions-grid tr").removeClass("ccm-file-versions-grid-active");
        var a = $(this).parent().parent();
        var b = a.attr("fID");
        var c = a.attr("fvID");
        var d = "task=approve_version&fID=" + b + "&fvID=" + c;
        $.post(CCM_TOOLS_PATH + "/files/properties", d, function(b) {
            a.addClass("ccm-file-versions-grid-active");
            a.find("td").show("highlight", {
                color: "#FFF9BB"
            });
        });
    });
    $(".ccm-file-versions-remove").click(function() {
        var a = $(this).parent().parent();
        var b = a.attr("fID");
        var c = a.attr("fvID");
        var d = "task=delete_version&fID=" + b + "&fvID=" + c;
        $.post(CCM_TOOLS_PATH + "/files/properties", d, function(b) {
            a.fadeOut(200, function() {
                a.remove();
            });
        });
        return false;
    });
};

ccm_alDeleteFiles = function(a) {
    $("#ccm-" + a + "-delete-form").ajaxSubmit(function(b) {
        ccm_parseJSON(b, function() {
            jQuery.fn.dialog.closeTop();
            ccm_deactivateSearchResults(a);
            $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
                ccm_parseAdvancedSearchResponse(b, a);
            });
        });
    });
};

ccm_alDuplicateFiles = function(searchInstance) {
    $("#ccm-" + searchInstance + "-duplicate-form").ajaxSubmit(function(resp) {
        ccm_parseJSON(resp, function() {
            jQuery.fn.dialog.closeTop();
            ccm_deactivateSearchResults(searchInstance);
            var r = eval("(" + resp + ")");
            $("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(a) {
                ccm_parseAdvancedSearchResponse(a, searchInstance);
                var b = new Array;
                for (i = 0; i < r.fID.length; i++) {
                    fID = r.fID[i];
                    ccm_uploadedFiles.push(fID);
                    b.push(fID);
                }
                ccm_alRefresh(b, searchInstance);
                ccm_filesUploadedDialog(searchInstance);
            });
        });
    });
};

ccm_alSetupSelectFiles = function(a) {
    $(".ccm-file-list").unbind();
    $(".ccm-file-list tr.ccm-list-record").click(function(a) {
        a.stopPropagation();
        ccm_alActivateMenu($(this), a);
    });
    $(".ccm-file-list img.ccm-star").click(function(a) {
        a.stopPropagation();
        var b = $(a.target).parents("tr.ccm-list-record")[0].id;
        b = b.substring(3);
        ccm_starFile(a.target, b);
    });
    if (ccm_alLaunchType[a] == "DASHBOARD") {
        $(".ccm-file-list-thumbnail").hover(function(a) {
            var b = $(this).attr("fID");
            var c = $("#fID" + b + "hoverThumbnail");
            if (c.length > 0) {
                var d = c.find("div");
                var e = c.position();
                d.css("top", e.top);
                d.css("left", e.left);
                d.show();
            }
        }, function() {
            var a = $(this).attr("fID");
            var b = $("#fID" + a + "hoverThumbnail");
            var c = b.find("div");
            c.hide();
        });
    }
};

ccm_alSetupCheckboxes = function(a) {
    $("#ccm-" + a + "-list-cb-all").unbind();
    $("#ccm-" + a + "-list-cb-all").click(function() {
        ccm_hideMenus();
        if ($(this).prop("checked") == true) {
            $("#ccm-" + a + "-search-results td.ccm-file-list-cb input[type=checkbox]").attr("checked", true);
            $("#ccm-" + a + "-list-multiple-operations").attr("disabled", false);
        } else {
            $("#ccm-" + a + "-search-results td.ccm-file-list-cb input[type=checkbox]").attr("checked", false);
            $("#ccm-" + a + "-list-multiple-operations").attr("disabled", true);
        }
    });
    $("#ccm-" + a + "-search-results td.ccm-file-list-cb input[type=checkbox]").click(function(b) {
        b.stopPropagation();
        ccm_hideMenus();
        ccm_alRescanMultiFileMenu(a);
    });
    $("#ccm-" + a + "-search-results td.ccm-file-list-cb").click(function(b) {
        b.stopPropagation();
        ccm_hideMenus();
        $(this).find("input[type=checkbox]").click();
        ccm_alRescanMultiFileMenu(a);
    });
    if (ccm_alLaunchType[a] != "DASHBOARD" && ccm_alLaunchType[a] != "BROWSE") {
        var b = ccmi18n_filemanager.select;
        $("#ccm-" + a + "-list-multiple-operations option:eq(0)").after('<option value="choose">' + b + "</option>");
    }
    $("#ccm-" + a + "-list-multiple-operations").change(function() {
        var b = $(this).val();
        var c = ccm_alGetSelectedFileIDs(a);
        switch (b) {
          case "choose":
            var d = new Array;
            $("#ccm-" + a + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
                d.push($(this).val());
            });
            ccm_alSelectFile(d, true);
            break;
          case "delete":
            jQuery.fn.dialog.open({
                width: 500,
                height: 400,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/files/delete?" + c + "&searchInstance=" + a,
                title: ccmi18n_filemanager.deleteFile
            });
            break;
          case "duplicate":
            jQuery.fn.dialog.open({
                width: 500,
                height: 400,
                modal: false,
                href: CCM_TOOLS_PATH + "/files/duplicate?" + c + "&searchInstance=" + a,
                title: ccmi18n_filemanager.duplicateFile
            });
            break;
          case "sets":
            jQuery.fn.dialog.open({
                width: 500,
                height: 400,
                modal: false,
                href: CCM_TOOLS_PATH + "/files/add_to?" + c + "&searchInstance=" + a,
                title: ccmi18n_filemanager.sets
            });
            break;
          case "properties":
            jQuery.fn.dialog.open({
                width: 690,
                height: 440,
                modal: false,
                href: CCM_TOOLS_PATH + "/files/bulk_properties?" + c + "&searchInstance=" + a,
                title: ccmi18n.properties
            });
            break;
          case "rescan":
            jQuery.fn.dialog.open({
                width: 350,
                height: 200,
                modal: false,
                href: CCM_TOOLS_PATH + "/files/rescan?" + c + "&searchInstance=" + a,
                title: ccmi18n_filemanager.rescan,
                onClose: function() {
                    $("#ccm-" + a + "-advanced-search").submit();
                }
            });
            break;
          case "download":
            window.frames[ccm_alProcessorTarget].location = CCM_TOOLS_PATH + "/files/download?" + c;
            break;
        }
        $(this).get(0).selectedIndex = 0;
    });
    ccm_alSetupFileSetSearch(a);
};

ccm_alSetupFileSetSearch = function(a) {
    $("#ccm-" + a + "-sets-search-wrapper select").chosen().unbind();
    $("#ccm-" + a + "-sets-search-wrapper select").chosen().change(function() {
        var b = $("#ccm-" + a + "-sets-search-wrapper option:selected");
        $("#ccm-" + a + "-advanced-search").submit();
    });
};

ccm_alGetSelectedFileIDs = function(a) {
    var b = "";
    $("#ccm-" + a + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
        b += "fID[]=" + $(this).val() + "&";
    });
    return b;
};

ccm_alRescanMultiFileMenu = function(a) {
    if ($("#ccm-" + a + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").length > 0) {
        $("#ccm-" + a + "-list-multiple-operations").attr("disabled", false);
    } else {
        $("#ccm-" + a + "-list-multiple-operations").attr("disabled", true);
    }
};

ccm_alSetupFileProcessor = function() {
    if (ccm_alProcessorTarget != "") {
        return false;
    }
    var a = parseInt((new Date).getTime().toString().substring(0, 10));
    var b;
    try {
        b = document.createElement('<iframe name="ccm-al-upload-processor' + a + '">');
    } catch (c) {
        b = document.createElement("iframe");
    }
    b.id = "ccm-al-upload-processor" + a;
    b.name = "ccm-al-upload-processor" + a;
    b.style.border = "0px";
    b.style.width = "0px";
    b.style.height = "0px";
    b.style.display = "none";
    document.body.appendChild(b);
    if (ccm_alDebug) {
        ccm_alProcessorTarget = "_blank";
    } else {
        ccm_alProcessorTarget = "ccm-al-upload-processor" + a;
    }
};

ccm_alSubmitSingle = function(a) {
    if ($(a).find(".ccm-al-upload-single-file").val() == "") {
        return false;
    } else {
        $(a).find(".ccm-al-upload-single-submit").hide();
        $(a).find(".ccm-al-upload-single-loader").show();
    }
};

ccm_alResetSingle = function() {
    $(".ccm-al-upload-single-file").val("");
    $(".ccm-al-upload-single-loader").hide();
    $(".ccm-al-upload-single-submit").show();
};

var ccm_uploadedFiles = [];

ccm_filesUploadedDialog = function(a) {
    if (document.getElementById("ccm-file-upload-multiple-tab")) jQuery.fn.dialog.closeTop();
    var b = "";
    for (var c = 0; c < ccm_uploadedFiles.length; c++) b = b + "&fID[]=" + ccm_uploadedFiles[c];
    jQuery.fn.dialog.open({
        width: 690,
        height: 440,
        modal: false,
        href: CCM_TOOLS_PATH + "/files/bulk_properties/?" + b + "&uploaded=true&searchInstance=" + a,
        onClose: function() {
            ccm_deactivateSearchResults(a);
            $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
                ccm_parseAdvancedSearchResponse(b, a);
            });
        },
        title: ccmi18n_filemanager.uploadComplete
    });
    ccm_uploadedFiles = [];
};

ccm_alSetupUploadDetailsForm = function(a) {
    $("#ccm-" + a + "-update-uploaded-details-form").submit(function() {
        ccm_alSubmitUploadDetailsForm(a);
        return false;
    });
};

ccm_alSubmitUploadDetailsForm = function(searchInstance) {
    jQuery.fn.dialog.showLoader();
    $("#ccm-" + searchInstance + "-update-uploaded-details-form").ajaxSubmit(function(r1) {
        var r1a = eval("(" + r1 + ")");
        var form = $("#ccm-" + searchInstance + "-advanced-search");
        if (form.length > 0) {
            form.ajaxSubmit(function(a) {
                $("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + "/files/search_sets_reload", {
                    searchInstance: searchInstance
                }, function() {
                    jQuery.fn.dialog.hideLoader();
                    jQuery.fn.dialog.closeTop();
                    ccm_parseAdvancedSearchResponse(a, searchInstance);
                    ccm_alHighlightFileIDArray(r1a);
                });
            });
        } else {
            jQuery.fn.dialog.hideLoader();
            jQuery.fn.dialog.closeTop();
        }
    });
};

ccm_alRefresh = function(a, b, c) {
    var d = a;
    ccm_deactivateSearchResults(b);
    $("#ccm-" + b + "-search-results").load(CCM_TOOLS_PATH + "/files/search_results", {
        ccm_order_by: "fvDateAdded",
        ccm_order_dir: "desc",
        fileSelector: c,
        searchType: ccm_alLaunchType[b],
        searchInstance: b
    }, function() {
        ccm_activateSearchResults(b);
        if (d != false) {
            ccm_alHighlightFileIDArray(d);
        }
        ccm_alSetupSelectFiles(b);
    });
};

ccm_alHighlightFileIDArray = function(a) {
    for (i = 0; i < a.length; i++) {
        var b = $("tr[fID=" + a[i] + "] td");
        var c = b.css("backgroundColor");
        b.animate({
            backgroundColor: "#FFF9BB"
        }, {
            queue: true,
            duration: 1e3
        }).animate({
            backgroundColor: c
        }, 500);
    }
};

ccm_alSelectFile = function(a) {
    if (typeof ccm_chooseAsset == "function") {
        var b = "";
        if (typeof a == "object") {
            for (i = 0; i < a.length; i++) {
                b += "fID[]=" + a[i] + "&";
            }
        } else {
            b += "fID=" + a;
        }
        $.getJSON(CCM_TOOLS_PATH + "/files/get_data.php?" + b, function(a) {
            ccm_parseJSON(a, function() {
                for (i = 0; i < a.length; i++) {
                    ccm_chooseAsset(a[i]);
                }
                jQuery.fn.dialog.closeTop();
            });
        });
    } else {
        if (typeof a == "object") {
            for (i = 0; i < a.length; i++) {
                ccm_triggerSelectFile(a[i]);
            }
        } else {
            ccm_triggerSelectFile(a);
        }
        jQuery.fn.dialog.closeTop();
    }
};

ccm_alActivateMenu = function(a, b) {
    var c = $(a).find("div[ccm-file-manager-field]");
    var d = "";
    if (c.length > 0) {
        d = c.attr("ccm-file-manager-field");
    }
    if (!d) {
        d = ccm_alActiveAssetField;
    }
    ccm_hideMenus();
    var e = $(a).attr("fID");
    var f = $(a).attr("ccm-file-manager-instance");
    var g = document.getElementById("ccm-al-menu" + e + f + d);
    if (!g) {
        el = document.createElement("DIV");
        el.id = "ccm-al-menu" + e + f + d;
        el.className = "ccm-menu ccm-ui";
        el.style.display = "block";
        el.style.visibility = "hidden";
        document.body.appendChild(el);
        var h = $("div[ccm-file-manager-field=" + d + "] input.ccm-file-manager-filter");
        var i = "";
        if (h.length > 0) {
            h.each(function() {
                i += "&" + $(this).attr("name") + "=" + $(this).attr("value");
            });
        }
        var j = $(a).attr("al-filepath");
        g = $("#ccm-al-menu" + e + f + d);
        g.css("position", "absolute");
        var k = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
        k += "<ul>";
        if (ccm_alLaunchType[f] != "DASHBOARD" && ccm_alLaunchType[f] != "BROWSE") {
            var l = c.length > 0 ? "ccm_alLaunchSelectorFileManager('" + d + "')" : "ccm_alSelectFile(" + e + ")";
            var m = c.length > 0 ? ccmi18n_filemanager.chooseNew : ccmi18n_filemanager.select;
            k += '<li><a class="ccm-menu-icon ccm-icon-choose-file-menu" dialog-modal="false" dialog-width="90%" dialog-height="70%" dialog-title="' + ccmi18n_filemanager.select + '" id="menuSelectFile' + e + '" href="javascript:void(0)" onclick="' + l + '">' + m + "</a></li>";
        }
        if (c.length > 0) {
            k += '<li><a class="ccm-menu-icon ccm-icon-clear-file-menu" href="javascript:void(0)" id="menuClearFile' + e + f + d + '">' + ccmi18n_filemanager.clear + "</a></li>";
        }
        if (ccm_alLaunchType[f] != "DASHBOARD" && ccm_alLaunchType[f] != "BROWSE" && c.length > 0) {
            k += '<li class="ccm-menu-separator"></li>';
        }
        if ($(a).attr("ccm-file-manager-can-view") == "1") {
            k += '<li><a class="ccm-menu-icon ccm-icon-view dialog-launch" dialog-modal="false" dialog-append-buttons="true" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" id="menuView' + e + '" href="' + CCM_TOOLS_PATH + "/files/view?fID=" + e + '">' + ccmi18n_filemanager.view + "</a></li>";
        } else {
            k += '<li><a class="ccm-menu-icon ccm-icon-download-menu" href="javascript:void(0)" id="menuDownload' + e + '" onclick="window.frames[\'' + ccm_alProcessorTarget + "'].location='" + CCM_TOOLS_PATH + "/files/download?fID=" + e + "'\">" + ccmi18n_filemanager.download + "</a></li>";
        }
        if ($(a).attr("ccm-file-manager-can-edit") == "1") {
            k += '<li><a class="ccm-menu-icon ccm-icon-edit-menu dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.edit + '" id="menuEdit' + e + '" href="' + CCM_TOOLS_PATH + "/files/edit?searchInstance=" + f + "&fID=" + e + i + '">' + ccmi18n_filemanager.edit + "</a></li>";
        }
        k += '<li><a class="ccm-menu-icon ccm-icon-properties-menu dialog-launch" dialog-modal="false" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" id="menuProperties' + e + '" href="' + CCM_TOOLS_PATH + "/files/properties?searchInstance=" + f + "&fID=" + e + '">' + ccmi18n_filemanager.properties + "</a></li>";
        if ($(a).attr("ccm-file-manager-can-replace") == "1") {
            k += '<li><a class="ccm-menu-icon ccm-icon-replace dialog-launch" dialog-modal="false" dialog-width="300" dialog-height="260" dialog-title="' + ccmi18n_filemanager.replace + '" id="menuFileReplace' + e + '" href="' + CCM_TOOLS_PATH + "/files/replace?searchInstance=" + f + "&fID=" + e + '">' + ccmi18n_filemanager.replace + "</a></li>";
        }
        if ($(a).attr("ccm-file-manager-can-duplicate") == "1") {
            k += '<li><a class="ccm-menu-icon ccm-icon-copy-menu" id="menuFileDuplicate' + e + '" href="javascript:void(0)" onclick="ccm_alDuplicateFile(' + e + ",'" + f + "')\">" + ccmi18n_filemanager.duplicate + "</a></li>";
        }
        k += '<li><a class="ccm-menu-icon ccm-icon-sets dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" id="menuFileSets' + e + '" href="' + CCM_TOOLS_PATH + "/files/add_to?searchInstance=" + f + "&fID=" + e + '">' + ccmi18n_filemanager.sets + "</a></li>";
        if ($(a).attr("ccm-file-manager-can-admin") == "1" || $(a).attr("ccm-file-manager-can-delete") == "1") {
            k += '<li class="ccm-menu-separator"></li>';
        }
        if ($(a).attr("ccm-file-manager-can-admin") == "1") {
            k += '<li><a class="ccm-menu-icon ccm-icon-access-permissions dialog-launch" dialog-modal="false" dialog-width="400" dialog-height="450" dialog-title="' + ccmi18n_filemanager.permissions + '" id="menuFilePermissions' + e + '" href="' + CCM_TOOLS_PATH + "/files/permissions?searchInstance=" + f + "&fID=" + e + '">' + ccmi18n_filemanager.permissions + "</a></li>";
        }
        if ($(a).attr("ccm-file-manager-can-delete") == "1") {
            k += '<li><a class="ccm-icon-delete-menu ccm-menu-icon dialog-launch" dialog-append-buttons="true" dialog-modal="false" dialog-width="500" dialog-height="200" dialog-title="' + ccmi18n_filemanager.deleteFile + '" id="menuDeleteFile' + e + '" href="' + CCM_TOOLS_PATH + "/files/delete?searchInstance=" + f + "&fID=" + e + '">' + ccmi18n_filemanager.deleteFile + "</a></li>";
        }
        k += "</ul>";
        k += "</div></div></div>";
        g.append(k);
        $(g).find("a").bind("click.hide-menu", function(a) {
            ccm_hideMenus();
            return false;
        });
        $("#ccm-al-menu" + e + f + d + " a.dialog-launch").dialog();
        $("a#menuClearFile" + e + f + d).click(function(a) {
            ccm_clearFile(a, d);
            ccm_hideMenus();
        });
    } else {
        g = $("#ccm-al-menu" + e + f + d);
    }
    ccm_fadeInMenu(g, b);
};

ccm_alSelectNone = function() {
    ccm_hideMenus();
};

var checkbox_status = false;

toggleCheckboxStatus = function(a) {
    if (checkbox_status) {
        for (i = 0; i < a.elements.length; i++) {
            if (a.elements[i].type == "checkbox") {
                a.elements[i].checked = false;
            }
        }
        checkbox_status = false;
    } else {
        for (i = 0; i < a.elements.length; i++) {
            if (a.elements[i].type == "checkbox") {
                a.elements[i].checked = true;
            }
        }
        checkbox_status = true;
    }
};

ccm_alDuplicateFile = function(fID, searchInstance) {
    var postStr = "fID=" + fID + "&searchInstance=" + searchInstance;
    $.post(CCM_TOOLS_PATH + "/files/duplicate", postStr, function(resp) {
        var r = eval("(" + resp + ")");
        if (r.error == 1) {
            ccmAlert.notice(ccmi18n.error, r.message);
            return false;
        }
        var highlight = new Array;
        if (r.fID) {
            highlight.push(r.fID);
            ccm_alRefresh(highlight, searchInstance);
            ccm_uploadedFiles.push(r.fID);
            ccm_filesUploadedDialog(searchInstance);
        }
    });
};

ccm_alSelectMultipleIncomingFiles = function(a) {
    if ($(a).prop("checked")) {
        $("input.ccm-file-select-incoming").attr("checked", true);
    } else {
        $("input.ccm-file-select-incoming").attr("checked", false);
    }
};

ccm_starFile = function(a, b) {
    var c = "";
    if ($(a).attr("src").indexOf(CCM_STAR_STATES.unstarred) != -1) {
        $(a).attr("src", $(a).attr("src").replace(CCM_STAR_STATES.unstarred, CCM_STAR_STATES.starred));
        c = "star";
    } else {
        $(a).attr("src", $(a).attr("src").replace(CCM_STAR_STATES.starred, CCM_STAR_STATES.unstarred));
        c = "unstar";
    }
    $.post(CCM_TOOLS_PATH + "/" + CCM_STAR_ACTION, {
        action: c,
        "file-id": b
    }, function(a, b) {});
};

jQuery.cookie = function(a, b, c) {
    if (typeof b != "undefined") {
        c = c || {};
        if (b === null) {
            b = "";
            c = $.extend({}, c);
            c.expires = -1;
        }
        var d = "";
        if (c.expires && (typeof c.expires == "number" || c.expires.toUTCString)) {
            var e;
            if (typeof c.expires == "number") {
                e = new Date;
                e.setTime(e.getTime() + c.expires * 24 * 60 * 60 * 1e3);
            } else {
                e = c.expires;
            }
            d = "; expires=" + e.toUTCString();
        }
        var f = c.path ? "; path=" + c.path : "";
        var g = c.domain ? "; domain=" + c.domain : "";
        var h = c.secure ? "; secure" : "";
        document.cookie = [ a, "=", encodeURIComponent(b), d, f, g, h ].join("");
    } else {
        var i = null;
        if (document.cookie && document.cookie != "") {
            var j = document.cookie.split(";");
            for (var k = 0; k < j.length; k++) {
                var l = jQuery.trim(j[k]);
                if (l.substring(0, a.length + 1) == a + "=") {
                    i = decodeURIComponent(l.substring(a.length + 1));
                    break;
                }
            }
        }
        return i;
    }
};

var quickSaveLayoutObj;

var deleteLayoutObj;

var ccmLayoutEdit = {
    init: function() {
        this.showPresetDeleteIcon();
        $("#ccmLayoutPresentIdSelector").change(function() {
            var a = parseInt($(this).val());
            var b = $("#ccmAreaLayoutForm_layoutID").val();
            jQuery.fn.dialog.showLoader();
            if (a > 0) {
                var c = $("#ccm-layout-refresh-action").val() + "&lpID=" + a;
            } else {
                var c = $("#ccm-layout-refresh-action").val() + "&layoutID=" + b;
            }
            $.get(c, function(a) {
                $("#ccm-layout-edit-wrapper").html(a);
                jQuery.fn.dialog.hideLoader();
                ccmLayoutEdit.showPresetDeleteIcon();
            });
        });
        $("#layoutPresetActionNew input[name=layoutPresetAction]").click(function() {
            if ($(this).val() == "create_new_preset" && $(this).prop("checked")) {
                $("input[name=layoutPresetName]").attr("disabled", false).focus();
            } else {
                $("input[name=layoutPresetName]").val("").attr("disabled", true);
            }
        });
        $("#layoutPresetActions input[name=layoutPresetAction]").click(function() {
            if ($(this).val() == "create_new_preset" && $(this).prop("checked")) {
                $("input[name=layoutPresetNameAlt]").attr("disabled", false).focus();
            } else {
                $("input[name=layoutPresetNameAlt]").val("").attr("disabled", true);
            }
        });
        if ($("#layoutPresetActions").length > 0) {
            $("#ccmLayoutConfigOptions input, #ccmLayoutConfigOptions select").bind("change click", function() {
                $("#layoutPresetActions").show();
                $("#layoutPresetActionNew").hide();
                $("#ccmLayoutConfigOptions input, #ccmLayoutConfigOptions select").unbind("change click");
            });
        }
    },
    showPresetDeleteIcon: function() {
        if ($("#ccmLayoutPresentIdSelector").val() > 0) {
            $("#ccm-layout-delete-preset").show();
        } else {
            $("#ccm-layout-delete-preset").hide();
        }
    },
    deletePreset: function() {
        var lpID = parseInt($("#ccmLayoutPresentIdSelector").val());
        if (lpID > 0) {
            if (!confirm(ccmi18n.confirmLayoutPresetDelete)) return false;
            jQuery.fn.dialog.showLoader();
            var area = $("#ccmAreaLayoutForm_arHandle").val();
            var url = CCM_TOOLS_PATH + "/layout_services/?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(area) + "&task=deletePreset&lpID=" + lpID;
            $.get(url, function(r) {
                eval("var jObj=" + r);
                if (parseInt(jObj.success) != 1) {
                    alert(jObj.msg);
                } else {
                    $("#ccmLayoutPresentIdSelector option[value='" + lpID + "']").remove();
                }
                jQuery.fn.dialog.hideLoader();
            });
        }
    }
};

$.widget.bridge("jqdialog", $.ui.dialog);

jQuery.fn.dialog = function() {
    if (arguments.length > 0) {
        $(this).jqdialog(arguments[0], arguments[1], arguments[2]);
        return;
    } else if ($(this).is("div")) {
        $(this).jqdialog();
        return;
    }
    return $(this).each(function() {
        $(this).unbind("click.make-dialog").bind("click.make-dialog", function(a) {
            var b = $(this).attr("href");
            var c = $(this).attr("dialog-width");
            var d = $(this).attr("dialog-height");
            var e = $(this).attr("dialog-title");
            var f = $(this).attr("dialog-on-open");
            var g = $(this).attr("dialog-on-destroy");
            var h = $(this).attr("dialog-on-close");
            obj = {
                modal: true,
                href: b,
                width: c,
                height: d,
                title: e,
                onOpen: f,
                onDestroy: g,
                onClose: h
            };
            jQuery.fn.dialog.open(obj);
            return false;
        });
    });
};

jQuery.fn.dialog.close = function(a) {
    a++;
    $("#ccm-dialog-content" + a).jqdialog("close");
};

jQuery.fn.dialog.open = function(obj) {
    jQuery.fn.dialog.showLoader();
    if (ccm_uiLoaded) {
        ccm_hideMenus();
    }
    var nd = $(".ui-dialog").length;
    nd++;
    $("body").append('<div id="ccm-dialog-content' + nd + '" style="display: none"></div>');
    if (typeof obj.width == "string") {
        if (obj.width.indexOf("%", 0) > 0) {
            w = obj.width.replace("%", "");
            w = $(window).width() * (w / 100);
            w = w + 50;
        } else {
            w = parseInt(obj.width) + 50;
        }
        if (obj.height.indexOf("%", 0) > 0) {
            h = obj.height.replace("%", "");
            h = $(window).height() * (h / 100);
            h = h + 100;
        } else {
            h = parseInt(obj.height) + 100;
        }
    } else if (obj.width) {
        w = parseInt(obj.width) + 50;
        h = parseInt(obj.height) + 100;
    } else {
        w = 550;
        h = 400;
    }
    if (h > $(window).height()) {
        h = $(window).height();
    }
    $("#ccm-dialog-content" + nd).jqdialog({
        modal: true,
        height: h,
        width: w,
        show: {
            effect: "fade",
            duration: 150,
            easing: "easeInExpo"
        },
        escapeClose: true,
        title: obj.title,
        open: function() {
            $("body").css("overflow", "hidden");
        },
        beforeClose: function() {
            var a = $(".ui-dialog").length;
            if (a == 1) {
                $("body").css("overflow", "auto");
            }
        },
        close: function(ev, u) {
            $(this).jqdialog("destroy").remove();
            $("#ccm-dialog-content" + nd).remove();
            if (typeof obj.onClose != "undefined") {
                if (typeof obj.onClose == "function") {
                    obj.onClose();
                } else {
                    eval(obj.onClose);
                }
            }
            if (typeof obj.onDestroy != "undefined") {
                if (typeof obj.onDestroy == "function") {
                    obj.onDestroy();
                } else {
                    eval(obj.onDestroy);
                }
            }
            nd--;
        }
    });
    if (!obj.element) {
        $.ajax({
            type: "GET",
            url: obj.href,
            success: function(r) {
                jQuery.fn.dialog.hideLoader();
                jQuery.fn.dialog.replaceTop(r);
                if (typeof obj.onOpen != "undefined") {
                    if (typeof obj.onOpen == "function") {
                        obj.onOpen();
                    } else {
                        eval(obj.onOpen);
                    }
                }
            }
        });
    } else {
        jQuery.fn.dialog.hideLoader();
        jQuery.fn.dialog.replaceTop($(obj.element));
        if (typeof obj.onOpen != "undefined") {
            if (typeof obj.onOpen == "function") {
                obj.onOpen();
            } else {
                eval(obj.onOpen);
            }
        }
    }
};

jQuery.fn.dialog.replaceTop = function(a) {
    var b = $(".ui-dialog").length;
    if (typeof a == "string") {
        $("#ccm-dialog-content" + b).html(a);
    } else {
        var c = a.clone(true, true).appendTo("#ccm-dialog-content" + b);
        if (c.css("display") == "none") {
            c.show();
        }
    }
    $("#ccm-dialog-content" + b + " .dialog-launch").dialog();
    $("#ccm-dialog-content" + b + " .ccm-dialog-close").click(function() {
        jQuery.fn.dialog.closeTop();
    });
    if ($("#ccm-dialog-content" + b + " .dialog-buttons").length > 0) {
        $("#ccm-dialog-content" + b).jqdialog("option", "buttons", [ {} ]);
        $("#ccm-dialog-content" + b + " .dialog-buttons").appendTo($("#ccm-dialog-content" + b).parent().find(".ui-dialog-buttonpane").addClass("ccm-ui"));
    }
    if ($("#ccm-dialog-content" + b + " .dialog-help").length > 0) {
        $("#ccm-dialog-content" + b + " .dialog-help").hide();
        var d = $("#ccm-dialog-content" + b + " .dialog-help").html();
        if (ccmi18n.helpPopup) {
            var e = ccmi18n.helpPopup;
        } else {
            var e = "Help";
        }
        $("#ccm-dialog-content" + b).parent().find(".ui-dialog-titlebar").append('<span class="ccm-dialog-help"><a href="javascript:void(0)" title="' + e + '" class="ccm-menu-help-trigger">Help</a></span>');
        $("#ccm-dialog-content" + b).parent().find(".ui-dialog-titlebar .ccm-menu-help-trigger").popover({
            content: function() {
                return d;
            },
            placement: "bottom",
            html: true,
            trigger: "click"
        });
    }
};

jQuery.fn.dialog.showLoader = function(a) {
    if (typeof imgLoader == "undefined" || !imgLoader || !imgLoader.src) return false;
    if ($("#ccm-dialog-loader").length < 1) {
        $("body").append("<div id='ccm-dialog-loader-wrapper' class='ccm-ui'><img id='ccm-dialog-loader' src='" + imgLoader.src + "' /></div>");
    }
    if (a != null) {
        $("<div />").attr("id", "ccm-dialog-loader-text").html(a).prependTo($("#ccm-dialog-loader-wrapper"));
    }
    var b = $("#ccm-dialog-loader-wrapper").width();
    var c = $("#ccm-dialog-loader-wrapper").height();
    var d = $(window).width();
    var e = $(window).height();
    var f = (d - b) / 2;
    var g = (e - c) / 2;
    $("#ccm-dialog-loader-wrapper").css("left", f + "px").css("top", g + "px");
    $("#ccm-dialog-loader-wrapper").show();
};

jQuery.fn.dialog.hideLoader = function() {
    $("#ccm-dialog-loader-wrapper").hide();
    $("#ccm-dialog-loader-text").remove();
};

jQuery.fn.dialog.closeTop = function() {
    var a = $(".ui-dialog").length;
    $("#ccm-dialog-content" + a).jqdialog("close");
};

jQuery.fn.dialog.closeAll = function() {
    $($(".ui-dialog-content").get().reverse()).jqdialog("close");
};

var imgLoader;

var ccm_dialogOpen = 0;

jQuery.fn.dialog.loaderImage = CCM_IMAGE_PATH + "/throbber_white_32.gif";

var ccmAlert = {
    notice: function(a, b, c) {
        $.fn.dialog.open({
            href: CCM_TOOLS_PATH + "/alert",
            title: a,
            width: 320,
            height: 160,
            modal: false,
            onOpen: function() {
                $("#ccm-popup-alert-message").html(b);
            },
            onDestroy: c
        });
    },
    hud: function(a, b, c, d) {
        if ($("#ccm-notification-inner").length == 0) {
            $(document.body).append('<div id="ccm-notification" class="ccm-ui"><div id="ccm-notification-inner"></div></div>');
        }
        if (c == null) {
            c = "edit_small";
        }
        if (d == null) {
            var e = a;
        } else {
            var e = "<h3>" + d + "</h3>" + a;
        }
        $("#ccm-notification-inner").html('<img id="ccm-notification-icon" src="' + CCM_IMAGE_PATH + "/icons/" + c + '.png" width="16" height="16" /><div id="ccm-notification-message">' + e + "</div>");
        $("#ccm-notification").show();
        if (b > 0) {
            setTimeout(function() {
                $("#ccm-notification").fadeOut({
                    easing: "easeOutExpo",
                    duration: 300
                });
            }, b);
        }
    }
};

$(document).ready(function() {
    imgLoader = new Image;
    imgLoader.src = jQuery.fn.dialog.loaderImage;
});

ccm_closeNewsflow = function(a) {
    $ovl = ccm_getNewsflowOverlayWindow();
    $ovl.fadeOut(300, "easeOutExpo");
    $(".ui-widget-overlay").fadeOut(300, "easeOutExpo", function() {
        $(this).remove();
    });
};

ccm_setNewsflowPagingArrowHeight = function() {
    if ($("#ccm-marketplace-detail").length > 0) {
        var a = $("#ccm-marketplace-detail");
    } else {
        var a = $("#newsflow-main");
    }
    var b = a.height();
    $(".newsflow-paging-previous a, .newsflow-paging-next a").css("height", b + "px");
    $(".newsflow-paging-previous, .newsflow-paging-next").css("height", b + "px");
    $(".newsflow-paging-next").show();
    $(".newsflow-paging-previous").show();
};

ccm_setNewsflowOverlayDimensions = function() {
    if ($("#newsflow-overlay").length > 0) {
        var a = $("#newsflow-overlay").width();
        var b = $(window).width();
        var c = $(window).height();
        var d = 650;
        var e = c - 80;
        if (e > d) {
            h = d;
        } else {
            h = e;
        }
        $("#newsflow-overlay").css("height", d);
        var f = (b - a) / 2;
        var g = (c - h) / 2;
        g = g + 29;
        f = f + "px";
        g = g + "px";
        $("#newsflow-overlay").css("left", f).css("top", g);
    }
};

ccm_getNewsflowOverlayWindow = function() {
    if ($("#ccm-dashboard-content").length > 0 && $("#newsflow-main").length > 0 && $("#newsflow-overlay").length == 0) {
        var a = $("#newsflow-main").parent();
    } else {
        if ($("#newsflow-overlay").length > 0) {
            var a = $("#newsflow-overlay");
        } else {
            var a = $("<div />").attr("id", "newsflow-overlay").attr("class", "ccm-ui").css("display", "none").appendTo(document.body);
        }
    }
    return a;
};

ccm_showNewsflowOverlayWindow = function(a, b) {
    if ($("#ccm-dashboard-content").length > 0 && $("#newsflow-main").length > 0) {} else {
        if ($(".ui-widget-overlay").length < 1) {
            var c = $('<div class="ui-widget-overlay"></div>').hide().appendTo("body");
        }
        $(".ui-widget-overlay").show();
    }
    $(window).resize(function() {
        ccm_setNewsflowOverlayDimensions();
    });
    $ovl = ccm_getNewsflowOverlayWindow();
    $ovl.load(a, function() {
        $ovl.hide();
        $(".newsflow-paging-next").hide();
        $(".newsflow-paging-previous").hide();
        $ovl.html($(this).html());
        if (b) {
            b();
        }
        ccm_setNewsflowOverlayDimensions();
        ccm_setupTrickleUpNewsflowStyles();
        $ovl.fadeIn("300", "easeOutExpo", function() {
            ccm_setNewsflowPagingArrowHeight();
        });
    });
};

ccm_setupTrickleUpNewsflowStyles = function() {
    ovl = ccm_getNewsflowOverlayWindow();
    ovl.find(".newsflow-em1").each(function() {
        $(this).parent().addClass("newsflow-em1");
    });
};

ccm_showDashboardNewsflowWelcome = function() {
    jQuery.fn.dialog.showLoader(ccmi18n.newsflowLoading);
    ccm_showNewsflowOverlayWindow(CCM_DISPATCHER_FILENAME + "/dashboard/home?_ccm_dashboard_external=1", function() {
        jQuery.fn.dialog.hideLoader();
    });
};

ccm_showNewsflowOffsite = function(a) {
    jQuery.fn.dialog.showLoader();
    ccm_showNewsflowOverlayWindow(CCM_TOOLS_PATH + "/newsflow?cID=" + a, function() {
        jQuery.fn.dialog.hideLoader();
    });
};

ccm_showAppIntroduction = function() {
    ccm_showNewsflowOverlayWindow(CCM_DISPATCHER_FILENAME + "/dashboard/welcome?_ccm_dashboard_external=1");
};

ccm_getNewsflowByPath = function(a) {
    jQuery.fn.dialog.showLoader();
    ccm_showNewsflowOverlayWindow(CCM_TOOLS_PATH + "/newsflow?cPath=" + a, function() {
        jQuery.fn.dialog.hideLoader();
    });
};

ccm_doPageReindexing = function() {
    $.get(CCM_TOOLS_PATH + "/reindex_pending_pages?ccm_token=" + CCM_SECURITY_TOKEN);
};

String.prototype.score = function(a, b) {
    b = b || 0;
    if (a.length == 0) return .9;
    if (a.length > this.length) return 0;
    for (var c = a.length; c > 0; c--) {
        var d = a.substring(0, c);
        var e = this.indexOf(d);
        if (e < 0) continue;
        if (e + a.length > this.length + b) continue;
        var f = this.substring(e + d.length);
        var g = null;
        if (c >= a.length) g = ""; else g = a.substring(c);
        var h = f.score(g, b + e);
        if (h > 0) {
            var i = this.length - f.length;
            if (e != 0) {
                var j = 0;
                var k = this.charCodeAt(e - 1);
                if (k == 32 || k == 9) {
                    for (var j = e - 2; j >= 0; j--) {
                        k = this.charCodeAt(j);
                        i -= k == 32 || k == 9 ? 1 : .15;
                    }
                } else {
                    i -= e;
                }
            }
            i += h * f.length;
            i /= this.length;
            return i;
        }
    }
    return 0;
};

ccm_openThemeLauncher = function() {
    jQuery.fn.dialog.closeTop();
    jQuery.fn.dialog.showLoader();
    ccm_testMarketplaceConnection(function() {
        $.fn.dialog.open({
            title: ccmi18n.community,
            href: CCM_TOOLS_PATH + "/marketplace/themes",
            width: "905",
            modal: false,
            height: "410"
        });
    }, "open_theme_launcher");
};

ccm_testMarketplaceConnection = function(a, b, c) {
    if (c) {
        mpIDStr = "&mpID=" + c;
    } else {
        mpIDStr = "";
    }
    if (!b) {
        b = "";
    }
    params = {
        mpID: c
    };
    $.getJSON(CCM_TOOLS_PATH + "/marketplace/connect", params, function(c) {
        if (c.isConnected) {
            a();
        } else {
            $.fn.dialog.open({
                title: ccmi18n.community,
                href: CCM_TOOLS_PATH + "/marketplace/frame?task=" + b + mpIDStr,
                width: "90%",
                modal: false,
                height: "70%"
            });
            return false;
        }
    });
};

ccm_openAddonLauncher = function() {
    jQuery.fn.dialog.closeTop();
    jQuery.fn.dialog.showLoader();
    ccm_testMarketplaceConnection(function() {
        $.fn.dialog.open({
            title: ccmi18n.community,
            href: CCM_TOOLS_PATH + "/marketplace/add-ons",
            width: "905",
            modal: false,
            height: "410"
        });
    }, "open_addon_launcher");
};

ccm_setupMarketplaceDialogForm = function() {
    $(".ccm-pane-dialog-pagination").each(function() {
        $(this).closest(".ui-dialog-content").dialog("option", "buttons", [ {} ]);
        $(this).closest(".ui-dialog").find(".ui-dialog-buttonpane .ccm-pane-dialog-pagination").remove();
        $(this).appendTo($(this).closest(".ui-dialog").find(".ui-dialog-buttonpane").addClass("ccm-ui"));
    });
    $(".ccm-pane-dialog-pagination a").click(function() {
        jQuery.fn.dialog.showLoader();
        $("#ccm-marketplace-browser-form").closest(".ui-dialog-content").load($(this).attr("href"), function() {
            jQuery.fn.dialog.hideLoader();
        });
        return false;
    });
    ccm_marketplaceBrowserInit();
    $("#ccm-marketplace-browser-form").ajaxForm({
        beforeSubmit: function() {
            jQuery.fn.dialog.showLoader();
        },
        success: function(a) {
            jQuery.fn.dialog.hideLoader();
            $("#ccm-marketplace-browser-form").closest(".ui-dialog-content").html(a);
        }
    });
};

ccm_marketplaceBrowserInit = function() {
    $(".ccm-marketplace-item").click(function() {
        ccm_getMarketplaceItemDetails($(this).attr("mpID"));
    });
    $(".ccm-marketplace-item-thumbnail").mouseover(function() {
        var a = $(this).parent().find("div.ccm-marketplace-results-image-hover").clone().addClass("ccm-marketplace-results-image-hover-displayed").appendTo(document.body);
        var b = $(this).offset().top;
        var c = $(this).offset().left;
        c = c + 60;
        a.css("top", b).css("left", c);
        a.show();
    });
    $(".ccm-marketplace-item-thumbnail").mouseout(function() {
        $(".ccm-marketplace-results-image-hover-displayed").hide().remove();
    });
};

ccm_getMarketplaceItemDetails = function(a) {
    jQuery.fn.dialog.showLoader();
    $("#ccm-intelligent-search-results").hide();
    ccm_testMarketplaceConnection(function() {
        $.fn.dialog.open({
            title: ccmi18n.community,
            href: CCM_TOOLS_PATH + "/marketplace/details?mpID=" + a,
            width: 820,
            appendButtons: true,
            modal: false,
            height: 640
        });
    }, "get_item_details", a);
};

ccm_getMarketplaceItem = function(a) {
    var b = a.mpID;
    var c = a.closeTop;
    this.onComplete = function() {};
    if (a.onComplete) {
        ccm_getMarketplaceItem.onComplete = a.onComplete;
    }
    if (c) {
        jQuery.fn.dialog.closeTop();
    }
    jQuery.fn.dialog.showLoader();
    params = {
        mpID: b
    };
    $.getJSON(CCM_TOOLS_PATH + "/marketplace/connect", params, function(a) {
        jQuery.fn.dialog.hideLoader();
        if (a.isConnected) {
            if (!a.purchaseRequired) {
                $.fn.dialog.open({
                    title: ccmi18n.community,
                    href: CCM_TOOLS_PATH + "/marketplace/download?install=1&mpID=" + b,
                    width: 500,
                    appendButtons: true,
                    modal: false,
                    height: 400
                });
            } else {
                $.fn.dialog.open({
                    title: ccmi18n.communityCheckout,
                    iframe: true,
                    href: CCM_TOOLS_PATH + "/marketplace/checkout?mpID=" + b,
                    width: "560px",
                    modal: false,
                    height: "400px"
                });
            }
        } else {
            $.fn.dialog.open({
                title: ccmi18n.community,
                href: CCM_TOOLS_PATH + "/marketplace/frame?task=get&mpID=" + b,
                width: "90%",
                modal: false,
                height: "70%"
            });
        }
    });
};

var ccm_searchActivatePostFunction = new Array;

ccm_setupAdvancedSearchFields = function(a) {
    ccm_totalAdvancedSearchFields = $(".ccm-search-request-field-set").length;
    $("#ccm-" + a + "-search-add-option").unbind();
    $("#ccm-" + a + "-search-add-option").click(function() {
        ccm_totalAdvancedSearchFields++;
        if ($("#ccm-search-fields-wrapper").length > 0) {
            $("#ccm-search-fields-wrapper").append('<div class="ccm-search-field" id="ccm-' + a + "-search-field-set" + ccm_totalAdvancedSearchFields + '">' + $("#ccm-search-field-base").html() + "</div>");
        } else {
            $("#ccm-" + a + "-search-advanced-fields").append('<tr class="ccm-search-field" id="ccm-' + a + "-search-field-set" + ccm_totalAdvancedSearchFields + '">' + $("#ccm-search-field-base").html() + "</tr>");
        }
        ccm_activateAdvancedSearchFields(a, ccm_totalAdvancedSearchFields);
    });
    var b = 1;
    $(".ccm-search-request-field-set").each(function() {
        ccm_activateAdvancedSearchFields(a, b);
        b++;
    });
};

ccm_setupAdvancedSearch = function(a) {
    ccm_setupAdvancedSearchFields(a);
    $("#ccm-" + a + "-advanced-search").ajaxForm({
        beforeSubmit: function() {
            ccm_deactivateSearchResults(a);
        },
        success: function(b) {
            ccm_parseAdvancedSearchResponse(b, a);
        }
    });
    ccm_setupInPagePaginationAndSorting(a);
    ccm_setupSortableColumnSelection(a);
};

ccm_parseAdvancedSearchResponse = function(a, b) {
    var c = $("#ccm-" + b + "-search-results");
    if (c.length == 0 || b == null) {
        c = $("#ccm-search-results");
    }
    c.html(a);
    ccm_activateSearchResults(b);
};

ccm_deactivateSearchResults = function(a) {
    var b = $("#ccm-" + a + "-search-fields-submit");
    if (b.length == 0 || a == null) {
        b = $("#ccm-search-fields-submit");
    }
    b.attr("disabled", true);
    var b = $("#ccm-" + a + "-search-results table.ccm-results-list");
    if (b.length == 0 || a == null) {
        b = $("#ccm-search-results");
    }
    b.css("opacity", .4);
    jQuery.fn.dialog.showLoader();
};

ccm_activateSearchResults = function(a) {
    if ($(".ui-dialog-content").length == 0) {
        window.scrollTo(0, 0);
    } else {
        $(".ui-dialog-content").each(function(a) {
            $(this).get(0).scrollTop = 0;
        });
    }
    $(".dialog-launch").dialog();
    var b = $("#ccm-" + a + "-search-results table.ccm-results-list");
    if (b.length == 0 || a == null) {
        b = $("#ccm-search-results");
    }
    b.css("opacity", 1);
    jQuery.fn.dialog.hideLoader();
    var b = $("#ccm-" + a + "-search-fields-submit");
    if (b.length == 0 || a == null) {
        b = $("#ccm-search-fields-submit");
    }
    b.attr("disabled", false);
    ccm_setupInPagePaginationAndSorting(a);
    ccm_setupSortableColumnSelection(a);
    if (typeof ccm_searchActivatePostFunction[a] == "function") {
        ccm_searchActivatePostFunction[a]();
    }
};

ccm_setupInPagePaginationAndSorting = function(a) {
    $(".ccm-results-list th a").click(function() {
        ccm_deactivateSearchResults(a);
        var b = $("#ccm-" + a + "-search-results");
        if (b.length == 0) {
            b = $("#ccm-search-results");
        }
        b.load($(this).attr("href"), false, function() {
            ccm_activateSearchResults(a);
        });
        return false;
    });
    $("div.ccm-pagination a").click(function() {
        if (!$(this).parent().hasClass("disabled")) {
            ccm_deactivateSearchResults(a);
            var b = $("#ccm-" + a + "-search-results");
            if (b.length == 0) {
                b = $("#ccm-search-results");
            }
            b.load($(this).attr("href"), false, function() {
                ccm_activateSearchResults(a);
                $("div.ccm-dialog-content").attr("scrollTop", 0);
            });
        }
        return false;
    });
    $(".ccm-pane-dialog-pagination").each(function() {
        $(this).closest(".ui-dialog-content").dialog("option", "buttons", [ {} ]);
        $(this).closest(".ui-dialog").find(".ui-dialog-buttonpane .ccm-pane-dialog-pagination").remove();
        $(this).appendTo($(this).closest(".ui-dialog").find(".ui-dialog-buttonpane").addClass("ccm-ui"));
    });
};

ccm_setupSortableColumnSelection = function(a) {
    $("#ccm-list-view-customize").unbind();
    $("#ccm-list-view-customize").click(function() {
        jQuery.fn.dialog.open({
            width: 550,
            height: 350,
            appendButtons: true,
            modal: false,
            href: $(this).attr("href"),
            title: ccmi18n.customizeSearch
        });
        return false;
    });
};

ccm_checkSelectedAdvancedSearchField = function(a, b) {
    $("#ccm-" + a + "-search-field-set" + b + " .ccm-search-option-type-date_time input").each(function() {
        $(this).attr("id", $(this).attr("id") + b);
    });
    $("#ccm-" + a + "-search-field-set" + b + " .ccm-search-option-type-date_time input").datepicker({
        showAnim: "fadeIn"
    });
    $("#ccm-" + a + "-search-field-set" + b + " .ccm-search-option-type-rating input").rating();
};

ccm_activateAdvancedSearchFields = function(a, b) {
    var c = $("#ccm-" + a + "-search-field-set" + b + " select:first");
    c.unbind();
    c.change(function() {
        var c = $(this).find(":selected").val();
        $(this).parent().parent().find("input.ccm-" + a + "-selected-field").val(c);
        var d = $("#ccm-" + a + "-search-field-base-elements span[search-field=" + c + "]");
        $("#ccm-" + a + "-search-field-set" + b + " .ccm-selected-field-content").html("");
        d.clone().appendTo("#ccm-" + a + "-search-field-set" + b + " .ccm-selected-field-content");
        $("#ccm-" + a + "-search-field-set" + b + " .ccm-selected-field-content .ccm-search-option").show();
        ccm_checkSelectedAdvancedSearchField(a, b);
    });
    $("#ccm-" + a + "-search-field-set" + b + " .ccm-search-remove-option").unbind();
    $("#ccm-" + a + "-search-field-set" + b + " .ccm-search-remove-option").click(function() {
        $(this).parents("div.ccm-search-field").remove();
        $(this).parents("tr.ccm-search-field").remove();
    });
    ccm_checkSelectedAdvancedSearchField(a, b);
};

ccm_activateEditablePropertiesGrid = function() {
    $("tr.ccm-attribute-editable-field").each(function() {
        var a = $(this);
        $(this).find("a").click(function() {
            a.find(".ccm-attribute-editable-field-text").hide();
            a.find(".ccm-attribute-editable-field-clear-button").hide();
            a.find(".ccm-attribute-editable-field-form").show();
            a.find(".ccm-attribute-editable-field-save-button").show();
        });
        a.find("form").submit(function() {
            ccm_submitEditablePropertiesGrid(a);
            return false;
        });
        a.find(".ccm-attribute-editable-field-save-button").parent().click(function() {
            ccm_submitEditablePropertiesGrid(a);
        });
        a.find(".ccm-attribute-editable-field-clear-button").parent().unbind();
        a.find(".ccm-attribute-editable-field-clear-button").parent().click(function() {
            a.find("form input[name=task]").val("clear_extended_attribute");
            ccm_submitEditablePropertiesGrid(a);
            return false;
        });
    });
};

ccm_submitEditablePropertiesGrid = function(a) {
    a.find(".ccm-attribute-editable-field-save-button").hide();
    a.find(".ccm-attribute-editable-field-clear-button").hide();
    a.find(".ccm-attribute-editable-field-loading").show();
    try {
        tinyMCE.triggerSave(true, true);
    } catch (b) {}
    a.find("form").ajaxSubmit(function(b) {
        a.find(".ccm-attribute-editable-field-loading").hide();
        a.find(".ccm-attribute-editable-field-save-button").show();
        a.find(".ccm-attribute-editable-field-text").html(b);
        a.find(".ccm-attribute-editable-field-form").hide();
        a.find(".ccm-attribute-editable-field-save-button").hide();
        a.find(".ccm-attribute-editable-field-text").show();
        a.find(".ccm-attribute-editable-field-clear-button").show();
        a.find("td").show("highlight", {
            color: "#FFF9BB"
        });
    });
};

var tr_activeNode = false;

if (typeof tr_doAnim == "undefined") {
    var tr_doAnim = false;
}

var tr_parseSubnodes = true;

var tr_reorderMode = false;

var tr_moveCopyMode = false;

showPageMenu = function(a, b) {
    ccm_hideMenus();
    b.stopPropagation();
    var c = $("#ccm-page-menu" + a.cID);
    if (!c.get(0)) {
        el = document.createElement("DIV");
        el.id = "ccm-page-menu" + a.cID;
        el.className = "ccm-menu ccm-ui";
        el.style.display = "block";
        el.style.visibility = "hidden";
        document.body.appendChild(el);
        c = $("#ccm-page-menu" + a.cID);
        c.css("position", "absolute");
        var d = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
        d += "<ul>";
        if (a.isTrash) {
            d += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" onclick="ccm_sitemapDeleteForever(' + a.instance_id + "," + a.cID + ', true)" href="javascript:void(0)">' + ccmi18n_sitemap.emptyTrash + "</a></li>";
        } else if (a.inTrash) {
            d += '<li><a class="ccm-menu-icon ccm-icon-search-pages" onclick="ccm_previewInternalTheme(' + a.cID + ", false, '" + ccmi18n_sitemap.previewPage + '\')" href="javascript:void(0)">' + ccmi18n_sitemap.previewPage + "</a></li>";
            d += '<li class="ccm-menu-separator"></li>';
            d += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" onclick="ccm_sitemapDeleteForever(' + a.instance_id + "," + a.cID + ', false)" href="javascript:void(0)">' + ccmi18n_sitemap.deletePageForever + "</a></li>";
        } else if (a.cAlias == "LINK" || a.cAlias == "POINTER") {
            d += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + a.cID + '" href="javascript:void(0)" onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + "?cID=" + a.cID + "'\">" + ccmi18n_sitemap.visitExternalLink + "</a></li>";
            if (a.cAlias == "LINK" && a.canEditProperties) {
                d += '<li><a class="ccm-menu-icon ccm-icon-edit-external-link" dialog-width="350" dialog-height="170" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" dialog-append-buttons="true" id="menuLink' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + '&ctask=edit_external">' + ccmi18n_sitemap.editExternalLink + "</a></li>";
            }
            if (a.canDelete) {
                d += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" dialog-append-buttons="true" id="menuDelete' + a.cID + '" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-append-buttons="true" dialog-title="' + ccmi18n_sitemap.deleteExternalLink + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + "&display_mode=" + a.display_mode + "&instance_id=" + a.instance_id + "&select_mode=" + a.select_mode + '&ctask=delete_external">' + ccmi18n_sitemap.deleteExternalLink + "</a></li>";
            }
        } else {
            d += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + a.cID + '" href="' + CCM_DISPATCHER_FILENAME + "?cID=" + a.cID + '">' + ccmi18n_sitemap.visitPage + "</a></li>";
            if (a.canCompose) {
                d += '<li><a class="ccm-menu-icon ccm-icon-edit-in-composer-menu" id="menuComposer' + a.cID + '" href="' + CCM_DISPATCHER_FILENAME + "/dashboard/composer/write/-/edit/" + a.cID + '">' + ccmi18n_sitemap.editInComposer + "</a></li>";
            }
            if (a.canEditProperties || a.canEditSpeedSettings || a.canEditPermissions || a.canEditDesign || a.canViewVersions || a.canDelete) {
                d += '<li class="ccm-menu-separator"></li>';
            }
            if (a.canEditProperties) {
                d += '<li><a class="ccm-menu-icon ccm-icon-properties-menu" dialog-on-close="ccm_sitemapExitEditMode(' + a.cID + ')" dialog-width="670" dialog-height="360" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pagePropertiesTitle + '" id="menuProperties' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + '&ctask=edit_metadata">' + ccmi18n_sitemap.pageProperties + "</a></li>";
            }
            if (a.canEditSpeedSettings) {
                d += '<li><a class="ccm-menu-icon ccm-icon-speed-settings-menu" dialog-on-close="ccm_sitemapExitEditMode(' + a.cID + ')" dialog-width="550" dialog-height="280" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.speedSettingsTitle + '" id="menuSpeedSettings' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + '&ctask=edit_speed_settings">' + ccmi18n_sitemap.speedSettings + "</a></li>";
            }
            if (a.canEditPermissions) {
                d += '<li><a class="ccm-menu-icon ccm-icon-permissions-menu" dialog-on-close="ccm_sitemapExitEditMode(' + a.cID + ')" dialog-width="420" dialog-height="630" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" id="menuPermissions' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + '&ctask=edit_permissions">' + ccmi18n_sitemap.setPagePermissions + "</a></li>";
            }
            if (a.canEditDesign) {
                d += '<li><a class="ccm-menu-icon ccm-icon-design-menu" dialog-on-close="ccm_sitemapExitEditMode(' + a.cID + ')" dialog-width="610" dialog-append-buttons="true" dialog-height="405" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageDesign + '" id="menuDesign' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + '&ctask=set_theme">' + ccmi18n_sitemap.pageDesign + "</a></li>";
            }
            if (a.canViewVersions) {
                d += '<li><a class="ccm-menu-icon ccm-icon-versions-menu" dialog-on-close="ccm_sitemapExitEditMode(' + a.cID + ')" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageVersions + '" id="menuVersions' + a.cID + '" href="' + CCM_TOOLS_PATH + "/versions.php?rel=SITEMAP&cID=" + a.cID + '">' + ccmi18n_sitemap.pageVersions + "</a></li>";
            }
            if (a.canDelete) {
                d += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" dialog-on-close="ccm_sitemapExitEditMode(' + a.cID + ')" dialog-append-buttons="true" id="menuDelete' + a.cID + '" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deletePage + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + "&display_mode=" + a.display_mode + "&instance_id=" + a.instance_id + "&select_mode=" + a.select_mode + '&ctask=delete">' + ccmi18n_sitemap.deletePage + "</a></li>";
            }
            if (a.display_mode == "explore" || a.display_mode == "search") {
                d += '<li class="ccm-menu-separator"></li>';
                d += '<li><a class="ccm-menu-icon ccm-icon-move-copy-menu" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.moveCopyPage + '" id="menuMoveCopy' + a.cID + '" href="' + CCM_TOOLS_PATH + "/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=" + a.cID + '" id="menuMoveCopy' + a.cID + '">' + ccmi18n_sitemap.moveCopyPage + "</a></li>";
                if (a.display_mode == "explore") {
                    d += '<li><a class="ccm-menu-icon ccm-icon-move-up" id="menuSendToStop' + a.cID + '" href="' + CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/explore?cNodeID=" + a.cID + '&task=send_to_top">' + ccmi18n_sitemap.sendToTop + "</a></li>";
                    d += '<li><a class="ccm-menu-icon ccm-icon-move-down" id="menuSendToBottom' + a.cID + '" href="' + CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/explore?cNodeID=" + a.cID + '&task=send_to_bottom">' + ccmi18n_sitemap.sendToBottom + "</a></li>";
                }
            }
            if (a.cNumChildren > 0) {
                d += '<li class="ccm-menu-separator"></li>';
                var e = CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/search/?selectedSearchField[]=parent&cParentAll=1&cParentIDSearchField=" + a.cID;
                if (a.display_mode == "full" || a.display_mode == "" || a.display_mode == "explore") {
                    d += '<li><a class="ccm-menu-icon ccm-icon-search-pages" id="menuSearch' + a.cID + '" href="' + e + '">' + ccmi18n_sitemap.searchPages + "</a></li>";
                }
                if (a.display_mode != "explore") {
                    d += '<li><a class="ccm-menu-icon ccm-icon-flat-view" id="menuExplore' + a.cID + '" href="' + CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/explore/-/" + a.cID + '">' + ccmi18n_sitemap.explorePages + "</a></li>";
                }
            }
            if (a.canAddSubpages || a.canAddExternalLinks) {
                d += '<li class="ccm-menu-separator"></li>';
            }
            if (a.canAddSubpages) {
                d += '<li><a class="ccm-menu-icon ccm-icon-add-page-menu" dialog-append-buttons="true" dialog-width="645" dialog-modal="false" dialog-height="345" dialog-title="' + ccmi18n_sitemap.addPage + '" id="menuSubPage' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&mode=" + a.display_mode + "&cID=" + a.cID + '&ctask=add">' + ccmi18n_sitemap.addPage + "</a></li>";
            }
            if (a.display_mode != "search" && a.canAddExternalLinks) {
                d += '<li><a class="ccm-menu-icon ccm-icon-add-external-link-menu" dialog-width="350" dialog-modal="false" dialog-height="170" dialog-title="' + ccmi18n_sitemap.addExternalLink + '" dialog-modal="false" dialog-append-buttons="true" id="menuLink' + a.cID + '" href="' + CCM_TOOLS_PATH + "/edit_collection_popup.php?rel=SITEMAP&cID=" + a.cID + '&ctask=add_external">' + ccmi18n_sitemap.addExternalLink + "</a></li>";
            }
        }
        d += "</ul>";
        d += "</div></div></div>";
        c.append(d);
        $(c).find("a").bind("click.hide-menu", function(a) {
            ccm_hideMenus();
        });
        $("#menuProperties" + a.cID).dialog();
        $("#menuSpeedSettings" + a.cID).dialog();
        $("#menuSubPage" + a.cID).dialog();
        $("#menuDesign" + a.cID).dialog();
        $("#menuLink" + a.cID).dialog();
        $("#menuVersions" + a.cID).dialog();
        $("#menuPermissions" + a.cID).dialog();
        $("#menuMoveCopy" + a.cID).dialog();
        $("#menuDelete" + a.cID).dialog();
    } else {
        c = $("#ccm-page-menu" + a.cID);
    }
    ccm_fadeInMenu(c, b);
};

hideBranch = function(a) {
    $("#tree-node" + a).hide();
    $("#tree-dz" + a).hide();
};

cancelReorder = function() {
    if (tr_reorderMode) {
        tr_reorderMode = false;
        $("li.tree-node").draggable("destroy");
        if (!tr_moveCopyMode) {
            hideSitemapMessage();
        }
    }
};

ccm_sitemapExitEditMode = function(a) {
    $.get(CCM_TOOLS_PATH + "/dashboard/sitemap_check_in?cID=" + a + "&ccm_token=" + CCM_SECURITY_TOKEN);
};

searchSubPages = function(a) {
    $("#ccm-tree-search-trigger" + a).hide();
    if (ccm_animEffects) {
        $("#ccm-tree-search" + a).fadeIn(200, function() {
            $("#ccm-tree-search" + a + " input").get(0).focus();
        });
    } else {
        $("#ccm-tree-search" + a).show();
        $("#ccm-tree-search" + a + " input").get(0).focus();
    }
};

activateReorder = function() {
    tr_reorderMode = true;
    $("li.tree-node[draggable=true]").draggable({
        handle: "img.handle",
        opacity: .5,
        revert: false,
        helper: "clone",
        start: function() {
            $(document.body).css("overflowX", "hidden");
        },
        stop: function() {
            $(document.body).css("overflowX", "auto");
        }
    });
    fixResortingDroppables();
};

deleteBranchFade = function(a) {
    if (ccm_animEffects) {
        $("#tree-node" + a).fadeOut(300, function() {
            $("#tree-node" + a).remove();
        });
        $("#tree-dz" + a).fadeOut(300, function() {
            $("#tree-dz" + a).remove();
        });
    } else {
        deleteBranchDirect(a);
    }
};

deleteBranchDirect = function(a) {
    $("#tree-node" + a).remove();
    $("#tree-dz" + a).remove();
};

showBranch = function(a) {
    var b = $("#tree-node" + a);
    $("#tree-node" + a).show();
    $("#tree-dz" + a).show();
};

rescanDisplayOrder = function(a) {
    setLoading(a);
    var b = "?foo=1";
    var c = $("#tree-root" + a).children("li.tree-node");
    for (i = 0; i < c.length; i++) {
        if ($(c[i]).hasClass("ui-draggable-dragging")) continue;
        b += "&cID[]=" + $(c[i]).attr("id").substring(9);
    }
    $.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_update.php", b, function(b) {
        ccm_parseJSON(b, function() {});
        removeLoading(a);
    });
};

var SITEMAP_LAST_DIALOGUE_URL = "";

var ccm_sitemap_html = "";

parseSitemapResponse = function(a, b, c, d, e) {
    var f = $("ul[tree-root-node-id=" + d + "][sitemap-instance-id=" + a + "]");
    f.html(e);
    f.slideDown(150, "easeOutExpo");
};

selectMoveCopyTarget = function(a, b, c, d, e) {
    if (!e) {
        var e = CCM_CID;
    }
    var f = ccmi18n_sitemap.moveCopyPage;
    var g = CCM_TOOLS_PATH + "/dashboard/sitemap_drag_request.php?instance_id=" + a + "&display_mode=" + b + "&select_mode=" + c + "&origCID=" + e + "&destCID=" + d;
    var h = 350;
    var i = 350;
    try {
        if (CCM_NODE_ACTION == "<none>") {
            if (CCM_TARGET_ID != "") {
                $("#" + CCM_TARGET_ID).val(d);
            }
            $.fn.dialog.closeTop();
            return;
        }
        if (CCM_NODE_ACTION != "") g = CCM_NODE_ACTION + "?destCID=" + d;
        if (CCM_DIALOG_TITLE != "") f = CCM_DIALOG_TITLE;
        if (CCM_DIALOG_HEIGHT != "") h = CCM_DIALOG_HEIGHT;
        if (CCM_DIALOG_WIDTH != "") i = CCM_DIALOG_WIDTH;
    } catch (j) {}
    $.fn.dialog.open({
        title: f,
        href: g,
        width: i,
        appendButtons: true,
        modal: false,
        height: h,
        onClose: function() {
            if (typeof CCM_TARGET_ID != "undefined" && CCM_TARGET_ID != "") {
                $("#" + CCM_TARGET_ID).val(d);
            }
            if (tr_moveCopyMode == true) {
                deactivateMoveCopy();
            }
        }
    });
};

selectLabel = function(e, node) {
    var cNumChildren = node.attr("tree-node-children");
    if (node.attr("sitemap-select-mode") == "move_copy_delete" || tr_moveCopyMode == true) {
        var destCID = node.attr("id").substring(10);
        var origCID = node.attr("selected-page-id");
        selectMoveCopyTarget(node.attr("sitemap-instance-id"), node.attr("sitemap-display-mode"), node.attr("sitemap-select-mode"), destCID, origCID);
    } else if (node.attr("sitemap-select-mode") == "select_page") {
        var callback = node.parents("[sitemap-wrapper=1]").attr("sitemap-select-callback");
        if (callback == null || callback == "" || typeof callback == "undefined") {
            callback = "ccm_selectSitemapNode";
        }
        eval(callback + "(node.attr('id').substring(10), unescape(node.attr('tree-node-title')));");
        jQuery.fn.dialog.closeTop();
    } else {
        node.addClass("tree-label-selected");
        if (tr_activeNode != false) {
            if (tr_activeNode.attr("id") != node.attr("id")) {
                tr_activeNode.removeClass("tree-label-selected");
            }
        }
        params = {
            cID: node.attr("id").substring(10),
            display_mode: node.attr("sitemap-display-mode"),
            isTrash: node.attr("tree-node-istrash"),
            inTrash: node.attr("tree-node-intrash"),
            select_mode: node.attr("sitemap-select-mode"),
            instance_id: node.attr("sitemap-instance-id"),
            canCompose: node.attr("tree-node-cancompose"),
            canEditProperties: node.attr("tree-node-can-edit-properties"),
            canEditSpeedSettings: node.attr("tree-node-can-edit-speed-settings"),
            canEditPermissions: node.attr("tree-node-can-edit-permissions"),
            canEditDesign: node.attr("tree-node-can-edit-design"),
            canViewVersions: node.attr("tree-node-can-view-versions"),
            canDelete: node.attr("tree-node-can-delete"),
            canAddSubpages: node.attr("tree-node-can-add-subpages"),
            canAddExternalLinks: node.attr("tree-node-can-add-external-links"),
            cNumChildren: node.attr("tree-node-children"),
            cAlias: node.attr("tree-node-alias")
        };
        showPageMenu(params, e);
        tr_activeNode = node;
    }
};

ccmSitemapHighlightPageLabel = function(a, b) {
    var c = $("#tree-label" + a + " > span");
    if (c.length == 0) {
        var c = $("tr.ccm-list-record[cID=" + a + "]");
        if (c.length > 0) {
            $("#ccm-page-advanced-search").submit();
        }
    } else {
        if (b != null) {
            c.html(b);
        }
    }
    c.show("highlight");
};

activateLabels = function(a, b, c) {
    var d = $("ul[sitemap-instance-id=" + a + "]");
    d.find("div.tree-label span").unbind();
    d.find("div.tree-label span").click(function(a) {
        selectLabel(a, $(this).parent());
    });
    d.find("ul[tree-root-state=closed]").each(function() {
        var a = $(this);
        var b = $(this).attr("tree-root-node-id");
        if ($(this).find("li").length > 0) {
            a.attr("tree-root-state", "open");
            $("#tree-collapse" + b).attr("src", CCM_IMAGE_PATH + "/dashboard/minus.jpg");
        }
    });
    if (c == "select_page" || c == "move_copy_delete") {
        d.find("li.ccm-sitemap-explore-paging a").each(function() {
            $(this).click(function() {
                var d = $(this).parentsUntil("ul").parent().parentsUntil("ul").parent().attr("tree-root-node-id");
                jQuery.fn.dialog.showLoader();
                $.get($(this).attr("href"), function(e) {
                    parseSitemapResponse(a, b, c, d, e);
                    activateLabels(a, b, c);
                    jQuery.fn.dialog.hideLoader();
                });
                return false;
            });
        });
    }
    if ((b == "explore" || b == "full") && !c) {
        d.find("img.handle").addClass("moveable");
    }
    if (b == "full" && !c) {
        d.find("div.tree-label").droppable({
            accept: ".tree-node",
            hoverClass: "on-drop",
            drop: function(b, c) {
                var d = c.draggable;
                var e = $(this).attr("id").substring(10);
                var f = $(d).attr("id").substring(9);
                if (e == f) return false;
                var g = CCM_TOOLS_PATH + "/dashboard/sitemap_drag_request.php?instance_id=" + a + "&origCID=" + f + "&destCID=" + e;
                if (SITEMAP_LAST_DIALOGUE_URL == g) return false; else SITEMAP_LAST_DIALOGUE_URL = g;
                $.fn.dialog.open({
                    title: ccmi18n_sitemap.moveCopyPage,
                    href: g,
                    width: 350,
                    modal: false,
                    height: 350,
                    appendButtons: true,
                    onClose: function() {
                        showBranch(f);
                    }
                });
            }
        });
        d.find("li.tree-node[draggable=true]").draggable({
            handle: "img.handle",
            opacity: .5,
            revert: false,
            helper: "clone",
            start: function() {
                $(document.body).css("overflowX", "hidden");
            },
            stop: function() {
                $(document.body).css("overflowX", "auto");
            }
        });
    }
};

ccm_triggerProgressiveOperation = function(a, b, c, d, e) {
    jQuery.fn.dialog.showLoader();
    $("#ccm-dialog-progress-bar").remove();
    $.ajax({
        url: a,
        type: "POST",
        data: b,
        success: function(f) {
            jQuery.fn.dialog.hideLoader();
            $('<div id="ccm-dialog-progress-bar" />').appendTo(document.body).html(f).jqdialog({
                autoOpen: false,
                height: 200,
                width: 400,
                modal: true,
                title: c,
                closeOnEscape: false,
                open: function(c, f) {
                    $(".ui-dialog-titlebar-close", this.parentNode).hide();
                    var g = $("#ccm-progressive-operation-progress-bar").attr("data-total-items");
                    ccm_doProgressiveOperation(a, b, g, d, e);
                }
            });
            $("#ccm-dialog-progress-bar").jqdialog("open");
        }
    });
};

ccm_doProgressiveOperation = function(a, b, c, d, e) {
    b.push({
        name: "process",
        value: "1"
    });
    b["process"] = true;
    $.ajax({
        url: a,
        dataType: "json",
        type: "POST",
        data: b,
        error: function(a, b, c) {
            switch (b) {
              case "timeout":
                var d = ccmi18n.requestTimeout;
                break;
              default:
                var d = a.responseText;
                break;
            }
            $("#ccm-dialog-progress-bar").dialog("option", "height", 200);
            $("#ccm-dialog-progress-bar").dialog("option", "closeOnEscape", true);
            $("#ccm-progressive-operation-progress-bar").html('<div class="alert alert-error">' + d + "</div>");
            $(".ui-dialog-titlebar-close").show();
        },
        success: function(f) {
            if (f.error) {
                var g = f.message;
                $("#ccm-dialog-progress-bar").dialog("option", "height", 200);
                $("#ccm-dialog-progress-bar").dialog("option", "closeOnEscape", true);
                $("#ccm-progressive-operation-progress-bar").html('<div class="alert alert-error">' + g + "</div>");
                $(".ui-dialog-titlebar-close").show();
                if (typeof e == "function") {
                    e(f);
                }
            } else {
                var h = f.totalItems;
                var i = Math.round((c - h) / c * 100);
                $("#ccm-progressive-operation-status").html(1);
                if (c - h > 0) {
                    $("#ccm-progressive-operation-status").html(c - h);
                }
                $("#ccm-progressive-operation-progress-bar div.bar").width(i + "%");
                if (h > 0) {
                    setTimeout(function() {
                        ccm_doProgressiveOperation(a, b, c, d, e);
                    }, 250);
                } else {
                    setTimeout(function() {
                        $("#ccm-progressive-operation-progress-bar div.bar").width("0%");
                        $("#ccm-dialog-progress-bar").dialog("close");
                        if (typeof d == "function") {
                            d(f);
                        }
                    }, 1e3);
                }
            }
        }
    });
};

ccm_refreshCopyOperations = function() {
    var a = ccmi18n_sitemap.copyProgressTitle;
    ccm_triggerProgressiveOperation(CCM_TOOLS_PATH + "/dashboard/sitemap_copy_all", [], a, function() {
        $(".ui-dialog-content").dialog("close");
        window.location.reload();
    });
};

moveCopyAliasNode = function(a) {
    var b = $("#origCID").val();
    var c = $("#destParentID").val();
    var d = $("#destCID").val();
    var e = $("input[name=ctask]:checked").val();
    var f = $("input[name=instance_id]").val();
    var g = $("input[name=display_mode]").val();
    var h = $("input[name=select_mode]").val();
    var i = $("input[name=copyAll]:checked").val();
    var j = $("input[name=saveOldPagePath]:checked").val();
    params = {
        origCID: b,
        destCID: d,
        ctask: e,
        ccm_token: CCM_SECURITY_TOKEN,
        copyAll: i,
        saveOldPagePath: j
    };
    if (i == 1) {
        var k = ccmi18n_sitemap.copyProgressTitle;
        ccm_triggerProgressiveOperation(CCM_TOOLS_PATH + "/dashboard/sitemap_copy_all", [ {
            name: "origCID",
            value: b
        }, {
            name: "destCID",
            value: d
        } ], k, function() {
            $(".ui-dialog-content").dialog("close");
            openSub(f, c, g, h, function() {
                openSub(f, d, g, h);
            });
        });
    } else {
        jQuery.fn.dialog.showLoader();
        $.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_drag_request.php", params, function(i) {
            ccm_parseJSON(i, function() {
                jQuery.fn.dialog.closeAll();
                jQuery.fn.dialog.hideLoader();
                ccmAlert.hud(i.message, 2e3);
                if (a == true) {
                    if (typeof CCM_LAUNCHER_SITEMAP != "undefined") {
                        if (CCM_LAUNCHER_SITEMAP == "explore") {
                            window.location.href = CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/explore/-/" + d;
                            return false;
                        }
                        if (CCM_LAUNCHER_SITEMAP == "search") {
                            ccm_deactivateSearchResults(CCM_SEARCH_INSTANCE_ID);
                            $("#ccm-" + CCM_SEARCH_INSTANCE_ID + "-advanced-search").ajaxSubmit(function(a) {
                                ccm_parseAdvancedSearchResponse(a, CCM_SEARCH_INSTANCE_ID);
                            });
                        }
                    } else {
                        setTimeout(function() {
                            window.location.href = CCM_DISPATCHER_FILENAME + "?cID=" + i.cID;
                        }, 2e3);
                        return false;
                    }
                }
                switch (e) {
                  case "COPY":
                  case "ALIAS":
                    showBranch(b);
                    break;
                  case "MOVE":
                    deleteBranchDirect(b);
                    break;
                }
                openSub(f, c, g, h, function() {
                    openSub(f, d, g, h);
                });
                jQuery.fn.dialog.closeTop();
                jQuery.fn.dialog.closeTop();
            });
        });
    }
};

toggleSub = function(a, b, c, d) {
    ccm_hideMenus();
    var e = $("ul[tree-root-node-id=" + b + "][sitemap-instance-id=" + a + "]");
    if (e.attr("tree-root-state") == "closed") {
        openSub(a, b, c, d);
    } else {
        closeSub(a, b, c, d);
    }
};

ccm_sitemapDeleteForever = function(a, b, c) {
    var d = c ? ccmi18n_sitemap.emptyTrash : ccmi18n_sitemap.deletePages;
    ccm_triggerProgressiveOperation(CCM_TOOLS_PATH + "/dashboard/sitemap_delete_forever", [ {
        name: "cID",
        value: b
    } ], d, function() {
        if (c) {
            closeSub(a, b, "full", "");
            var d = $("ul[tree-root-node-id=" + b + "]").parent();
            d.find("img.tree-plus").remove();
            d.find("span.ccm-sitemap-num-subpages").remove();
        } else {
            deleteBranchFade(b);
            ccmAlert.hud(ccmi18n_sitemap.deletePageSuccessMsg, 2e3);
        }
    });
};

setLoading = function(a) {
    var b = $("#tree-node" + a);
    b.removeClass("tree-node-" + b.attr("tree-node-type"));
    b.addClass("tree-node-loading");
};

removeLoading = function(a) {
    var b = $("#tree-node" + a);
    b.removeClass("tree-node-loading");
    b.addClass("tree-node-" + b.attr("tree-node-type"));
};

openSub = function(a, b, c, d, e) {
    setLoading(b);
    var f = $("#tree-root" + b);
    cancelReorder();
    ccm_sitemap_html = "";
    $.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?instance_id=" + a + "&node=" + b + "&display_mode=" + c + "&select_mode=" + d + "&selectedPageID=" + f.attr("selected-page-id"), function(c) {
        parseSitemapResponse(a, "full", d, b, c);
        activateLabels(a, "full", d);
        if (d != "move_copy_delete" && d != "select_page") {
            activateReorder();
        }
        setTimeout(function() {
            removeLoading(b);
            if (e != null) {
                e();
            }
        }, 200);
    });
};

closeSub = function(a, b, c, d) {
    var e = $("ul[tree-root-node-id=" + b + "][sitemap-instance-id=" + a + "]");
    if (tr_doAnim) {
        setLoading(b);
        e.slideUp(150, "easeOutExpo", function() {
            removeLoading(b);
            e.attr("tree-root-state", "closed");
            e.html("");
            $("#ccm-tree-search" + b).hide();
            $("#tree-collapse" + b).attr("src", CCM_IMAGE_PATH + "/dashboard/plus.jpg");
            e.removeClass("ccm-sitemap-search-results");
        });
    } else {
        e.hide();
        e.attr("tree-root-state", "closed");
        e.removeClass("ccm-sitemap-search-results");
        $("#ccm-tree-search" + b).hide();
        $("#tree-collapse" + b).attr("src", CCM_IMAGE_PATH + "/dashboard/plus.jpg");
    }
    if (tr_moveCopyMode == true) {
        $("#ccm-tree-search-trigger" + cID).show();
    }
    $.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?instance_id=" + a + "&select_mode=" + d + "&display_mode=" + c + "&node=" + b + "&display_mode=full&ctask=close-node");
};

toggleMove = function() {
    if ($("#copyThisPage").get(0)) {
        $("#copyThisPage").get(0).disabled = true;
        $("#copyChildren").get(0).disabled = true;
        $("#saveOldPagePath").attr("disabled", false);
    }
};

toggleAlias = function() {
    if ($("#copyThisPage").get(0)) {
        $("#copyThisPage").get(0).disabled = true;
        $("#copyChildren").get(0).disabled = true;
        $("#saveOldPagePath").attr("checked", false);
        $("#saveOldPagePath").attr("disabled", "disabled");
    }
};

toggleCopy = function() {
    if ($("#copyThisPage").get(0)) {
        $("#copyThisPage").get(0).disabled = false;
        $("#copyThisPage").get(0).checked = true;
        $("#copyChildren").get(0).disabled = false;
        $("#saveOldPagePath").attr("checked", false);
        $("#saveOldPagePath").attr("disabled", "disabled");
    }
};

showSitemapMessage = function(a) {
    $("#ccm-sitemap-message").addClass("message");
    $("#ccm-sitemap-message").html(a);
    $("#ccm-sitemap-message").fadeIn(200);
};

hideSitemapMessage = function() {
    $("#ccm-sitemap-message").hide();
};

ccmSitemapExploreNode = function(a, b, c, d, e) {
    jQuery.fn.dialog.showLoader();
    $.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php", {
        instance_id: a,
        display_mode: b,
        select_mode: c,
        node: d,
        selectedPageID: e
    }, function(b) {
        parseSitemapResponse(a, "explore", c, 0, b);
        activateLabels(a, "explore", c);
        jQuery.fn.dialog.hideLoader();
        ccm_sitemap_html = "";
    });
};

ccmSitemapLoad = function(a, b, c, d, e, f) {
    if (c == "move_copy_delete" || c == "select_page") {
        ccmSitemapExploreNode(a, b, c, d, e);
    } else if (b == "full") {
        activateLabels(a, b, c);
        if (c != "move_copy_delete" && c != "select_page") {
            activateReorder();
        }
        tr_doAnim = true;
        tr_parseSubnodes = false;
        ccm_sitemap_html = "";
    } else {
        if (c != "move_copy_delete" && c != "select_page") {
            $("ul[sitemap-instance-id=" + a + "]").sortable({
                cursor: "move",
                items: "li[draggable=true]",
                opacity: .5,
                stop: function(b) {
                    var c = $("ul[sitemap-instance-id=" + a + "]").sortable("toArray");
                    var d = "";
                    for (i = 0; i < c.length; i++) {
                        if (c[i] != "") {
                            d += "&cID[]=" + c[i].substring(9);
                        }
                    }
                    $.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_update.php", d, function(a) {
                        ccm_parseJSON(a, function() {});
                    });
                }
            });
        }
        activateLabels(a, b, c);
    }
    if (f) {
        f();
    }
};

ccm_sitemapSetupSearch = function(a) {
    ccm_setupAdvancedSearch(a);
    ccm_sitemapSetupSearchPages(a);
    ccm_searchActivatePostFunction[a] = function() {
        ccm_sitemapSetupSearchPages(a);
        ccm_sitemapSearchSetupCheckboxes(a);
    };
    ccm_sitemapSearchSetupCheckboxes(a);
};

ccm_sitemapSearchSetupCheckboxes = function(a) {
    $("#ccm-" + a + "-list-cb-all").click(function(b) {
        b.stopPropagation();
        if ($(this).prop("checked") == true) {
            $(".ccm-list-record td.ccm-" + a + "-list-cb input[type=checkbox]").attr("checked", true);
            $("#ccm-" + a + "-list-multiple-operations").attr("disabled", false);
        } else {
            $(".ccm-list-record td.ccm-" + a + "-list-cb input[type=checkbox]").attr("checked", false);
            $("#ccm-" + a + "-list-multiple-operations").attr("disabled", true);
        }
    });
    $("td.ccm-" + a + "-list-cb input[type=checkbox]").click(function(b) {
        b.stopPropagation();
        if ($("td.ccm-" + a + "-list-cb input[type=checkbox]:checked").length > 0) {
            $("#ccm-" + a + "-list-multiple-operations").attr("disabled", false);
        } else {
            $("#ccm-" + a + "-list-multiple-operations").attr("disabled", true);
        }
    });
    $("#ccm-" + a + "-list-multiple-operations").change(function() {
        var b = $(this).val();
        cIDstring = "";
        $("td.ccm-" + a + "-list-cb input[type=checkbox]:checked").each(function() {
            cIDstring = cIDstring + "&cID[]=" + $(this).val();
        });
        switch (b) {
          case "delete":
            jQuery.fn.dialog.open({
                width: 500,
                height: 400,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/pages/delete?" + cIDstring + "&searchInstance=" + a,
                title: ccmi18n_sitemap.deletePages
            });
            break;
          case "design":
            jQuery.fn.dialog.open({
                width: 610,
                height: 405,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/pages/design?" + cIDstring + "&searchInstance=" + a,
                title: ccmi18n_sitemap.pageDesign
            });
            break;
          case "move_copy":
            jQuery.fn.dialog.open({
                width: 640,
                height: 340,
                modal: false,
                href: CCM_TOOLS_PATH + "/sitemap_overlay?instance_id=" + a + "&select_mode=move_copy_delete&" + cIDstring,
                title: ccmi18n_sitemap.moveCopyPage
            });
            break;
          case "speed_settings":
            jQuery.fn.dialog.open({
                width: 610,
                height: 340,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/pages/speed_settings?" + cIDstring,
                title: ccmi18n_sitemap.speedSettingsTitle
            });
            break;
          case "permissions":
            jQuery.fn.dialog.open({
                width: 430,
                height: 630,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/pages/permissions?" + cIDstring,
                title: ccmi18n_sitemap.pagePermissionsTitle
            });
            break;
          case "permissions_add_access":
            jQuery.fn.dialog.open({
                width: 440,
                height: 200,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/pages/permissions_access?task=add&" + cIDstring,
                title: ccmi18n_sitemap.pagePermissionsTitle
            });
            break;
          case "permissions_remove_access":
            jQuery.fn.dialog.open({
                width: 440,
                height: 300,
                modal: false,
                appendButtons: true,
                href: CCM_TOOLS_PATH + "/pages/permissions_access?task=remove&" + cIDstring,
                title: ccmi18n_sitemap.pagePermissionsTitle
            });
            break;
          case "properties":
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/pages/bulk_metadata_update?" + cIDstring,
                title: ccmi18n_sitemap.pagePropertiesTitle
            });
            break;
        }
        $(this).get(0).selectedIndex = 0;
    });
};

ccm_sitemapSetupSearchPages = function(instance_id) {
    $("#ccm-" + instance_id + "-list tr").click(function(e) {
        var node = $(this);
        if (node.hasClass("ccm-results-list-header")) {
            return false;
        }
        if (node.attr("sitemap-select-mode") == "select_page") {
            var callback = node.attr("sitemap-select-callback");
            if (callback == null || callback == "" || typeof callback == "undefined") {
                callback = "ccm_selectSitemapNode";
            }
            eval(callback + "(node.attr('cID'), unescape(node.attr('cName')));");
            jQuery.fn.dialog.closeTop();
        } else if (node.attr("sitemap-select-mode") == "move_copy_delete") {
            var destCID = node.attr("cID");
            var origCID = node.attr("selected-page-id");
            selectMoveCopyTarget(node.attr("sitemap-instance-id"), node.attr("sitemap-display-mode"), node.attr("sitemap-select-mode"), destCID, origCID);
        } else {
            params = {
                cID: node.attr("cID"),
                select_mode: node.attr("sitemap-select-mode"),
                display_mode: node.attr("sitemap-display-mode"),
                instance_id: node.attr("sitemap-instance-id"),
                isTrash: node.attr("tree-node-istrash"),
                inTrash: node.attr("tree-node-intrash"),
                canCompose: node.attr("tree-node-cancompose"),
                canEditProperties: node.attr("tree-node-can-edit-properties"),
                canEditSpeedSettings: node.attr("tree-node-can-edit-speed-settings"),
                canEditPermissions: node.attr("tree-node-can-edit-permissions"),
                canEditDesign: node.attr("tree-node-can-edit-design"),
                canViewVersions: node.attr("tree-node-can-view-versions"),
                canDelete: node.attr("tree-node-can-delete"),
                canAddSubpages: node.attr("tree-node-can-add-subpages"),
                canAddExternalLinks: node.attr("tree-node-can-add-external-links"),
                cNumChildren: node.attr("cNumChildren"),
                cAlias: node.attr("cAlias")
            };
            showPageMenu(params, e);
        }
    });
};

ccm_sitemapSelectDisplayMode = function(a, b, c, d) {
    var e = $("ul[sitemap-instance-id=" + a + "]");
    e.html("");
    e.attr("sitemap-display-mode", b);
    e.attr("sitemap-select-mode", c);
    e.attr("sitemap-display-mode", b);
    if (b == "explore") {
        var f = 1;
    } else {
        var f = 0;
    }
    ccmSitemapLoad(a, b, c, f, d, function() {
        if (b == "explore") {
            $("div[sitemap-wrapper=1][sitemap-instance-id=" + a + "]").addClass("ccm-sitemap-explore");
        } else {
            $("div[sitemap-wrapper=1][sitemap-instance-id=" + a + "]").removeClass("ccm-sitemap-explore");
        }
    });
    $.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?task=save_sitemap_display_mode&display_mode=" + b);
};

ccm_sitemapDeletePages = function(a) {
    var b = $("#ccm-" + a + "-delete-form").formToArray(true);
    ccm_triggerProgressiveOperation(CCM_TOOLS_PATH + "/pages/delete", b, ccmi18n_sitemap.deletePages, function() {
        $(".ui-dialog-content").dialog("close");
        ccm_deactivateSearchResults(a);
        $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
            ccm_parseAdvancedSearchResponse(b, a);
        });
        if (isTrash) {
            closeSub(instance_id, nodeID, "full", "");
            var b = $("ul[tree-root-node-id=" + nodeID + "]").parent();
            b.find("img.tree-plus").remove();
            b.find("span.ccm-sitemap-num-subpages").remove();
        } else {
            deleteBranchFade(nodeID);
            ccmAlert.hud(ccmi18n_sitemap.deletePageSuccessMsg, 2e3);
        }
    });
};

ccm_sitemapUpdateDesign = function(a) {
    $("#ccm-" + a + "-design-form").ajaxSubmit(function(b) {
        ccm_parseJSON(b, function() {
            jQuery.fn.dialog.closeTop();
            ccm_deactivateSearchResults(a);
            $("#ccm-" + a + "-advanced-search").ajaxSubmit(function(b) {
                ccm_parseAdvancedSearchResponse(b, a);
            });
        });
    });
};

$(function() {
    $(document).click(function() {
        ccm_hideMenus();
        $("div.tree-label").removeClass("tree-label-selected");
    });
    $("#ccm-show-all-pages-cb").click(function() {
        var a = $(this).get(0).checked == true ? 1 : 0;
        $.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?show_system=" + a, function(a) {
            location.reload();
        });
    });
});

ccm_statusBar = {
    items: [],
    addItem: function(a) {
        this.items.push(a);
    },
    activate: function() {
        if (this.items.length > 0) {
            var a = '<div id="ccm-page-status-bar" class="ccm-ui">';
            for (i = 0; i < this.items.length; i++) {
                var b = this.items[i];
                var c = "";
                var d = b.getButtons();
                for (j = 0; j < d.length; j++) {
                    attribs = "";
                    var e = "";
                    var f = "";
                    if (d[j].getInnerButtonLeftHTML() != "") {
                        e = d[j].getInnerButtonLeftHTML() + " ";
                    }
                    if (d[j].getInnerButtonRightHTML() != "") {
                        f = " " + d[j].getInnerButtonRightHTML();
                    }
                    var g = d[j].getAttributes();
                    for (k in g) {
                        attribs += g[k].key + '="' + g[k].value + '" ';
                    }
                    if (d[j].getURL() != "") {
                        c += '<a href="' + d[j].getURL() + '" ' + attribs + ' class="btn btn-small ' + d[j].getCSSClass() + '">' + e + d[j].getLabel() + f + "</a>";
                    } else {
                        c += '<button type="submit" ' + attribs + ' name="action_' + d[j].getAction() + '" class="btn-small btn ' + d[j].getCSSClass() + '">' + e + d[j].getLabel() + f + "</button>";
                    }
                }
                var h = '<form method="post" action="' + b.getAction() + '" id="ccm-status-bar-form-' + i + '" ' + (b.useAjaxForm ? 'class="ccm-status-bar-ajax-form"' : "") + '><div class="alert-message alert ' + b.getCSSClass() + '"><button type="button" class="close" data-dismiss="alert">×</button><span>' + b.getDescription() + '</span> <div class="ccm-page-status-bar-buttons">' + c + "</div></div></form>";
                a += h;
            }
            a += "</div>";
            $("#ccm-page-controls-wrapper").append(a);
            $("#ccm-page-status-bar .dialog-launch").dialog();
            $("#ccm-page-status-bar .alert").bind("closed", function() {
                $(this).remove();
                var a = $("#ccm-page-status-bar .alert:visible").length;
                if (a == 0) {
                    $("#ccm-page-status-bar").remove();
                }
            });
            $("#ccm-page-status-bar .ccm-status-bar-ajax-form").ajaxForm({
                dataType: "json",
                beforeSubmit: function() {
                    jQuery.fn.dialog.showLoader();
                },
                success: function(a) {
                    if (a.redirect) {
                        window.location.href = a.redirect;
                    }
                }
            });
        }
    }
};

ccm_statusBarItem = function() {
    this.css = "";
    this.description = "";
    this.buttons = [];
    this.action = "";
    this.useAjaxForm = false;
    this.setCSSClass = function(a) {
        this.css = a;
    };
    this.enableAjaxForm = function() {
        this.useAjaxForm = true;
    };
    this.setDescription = function(a) {
        this.description = a;
    };
    this.getCSSClass = function() {
        return this.css;
    };
    this.getDescription = function() {
        return this.description;
    };
    this.addButton = function(a) {
        this.buttons.push(a);
    };
    this.getButtons = function() {
        return this.buttons;
    };
    this.setAction = function(a) {
        this.action = a;
    };
    this.getAction = function() {
        return this.action;
    };
};

ccm_statusBarItemButton = function() {
    this.css = "";
    this.innerbuttonleft = "";
    this.innerbuttonright = "";
    this.label = "";
    this.action = "";
    this.url = "";
    this.attribs = new Array;
    this.setLabel = function(a) {
        this.label = a;
    };
    this.setCSSClass = function(a) {
        this.css = a;
    };
    this.setInnerButtonLeftHTML = function(a) {
        this.innerbuttonleft = a;
    };
    this.setInnerButtonRightHTML = function(a) {
        this.innerbuttonright = a;
    };
    this.setAction = function(a) {
        this.action = a;
    };
    this.getAttributes = function() {
        return this.attribs;
    };
    this.addAttribute = function(a, b) {
        this.attribs.push({
            key: a,
            value: b
        });
    };
    this.getAction = function() {
        return this.action;
    };
    this.setURL = function(a) {
        this.url = a;
    };
    this.getURL = function() {
        return this.url;
    };
    this.getCSSClass = function() {
        return this.css;
    };
    this.getInnerButtonLeftHTML = function() {
        return this.innerbuttonleft;
    };
    this.getInnerButtonRightHTML = function() {
        return this.innerbuttonright;
    };
    this.getLabel = function() {
        return this.label;
    };
};

ccm_activateTabBar = function(a) {
    $("#ccm-tab-content-" + a.find("li[class=active] a").attr("data-tab")).show();
    a.find("a").unbind().click(function() {
        a.find("li").removeClass("active");
        $(this).parent().addClass("active");
        a.find("a").each(function() {
            $("#ccm-tab-content-" + $(this).attr("data-tab")).hide();
        });
        var b = $(this).attr("data-tab");
        $("#ccm-tab-content-" + b).show();
        return false;
    });
};

var ccm_editorCurrentAuxTool = false;

ccm_editorSetupImagePicker = function() {
    tinyMCE.activeEditor.focus();
    var a = tinyMCE.activeEditor.selection.getBookmark();
    ccm_chooseAsset = function(b) {
        var c = tinyMCE.activeEditor;
        c.selection.moveToBookmark(a);
        var d = {};
        tinymce.extend(d, {
            src: b.filePathInline,
            alt: b.title,
            width: b.width,
            height: b.height
        });
        c.execCommand("mceInsertContent", false, '<img id="__mce_tmp" src="javascript:;" />', {
            skip_undo: 1
        });
        c.dom.setAttribs("__mce_tmp", d);
        c.dom.setAttrib("__mce_tmp", "id", "");
        c.undoManager.add();
    };
    return false;
};

ccm_editorSetupFilePicker = function() {
    tinyMCE.activeEditor.focus();
    var a = tinyMCE.activeEditor.selection.getBookmark();
    ccm_chooseAsset = function(b) {
        var c = tinyMCE.activeEditor;
        c.selection.moveToBookmark(a);
        var d = c.selection.getContent();
        if (d != "") {
            c.execCommand("mceInsertLink", false, {
                href: b.filePath,
                title: b.title,
                target: null,
                "class": null
            });
        } else {
            var e = '<a href="' + b.filePath + '">' + b.title + "</a>";
            tinyMCE.execCommand("mceInsertRawHTML", false, e, true);
        }
    };
    return false;
};

ccm_editorSitemapOverlay = function() {
    tinyMCE.activeEditor.focus();
    var a = tinyMCE.activeEditor.selection.getBookmark();
    $.fn.dialog.open({
        title: ccmi18n_sitemap.choosePage,
        href: CCM_TOOLS_PATH + "/sitemap_search_selector.php?sitemap_select_mode=select_page&callback=ccm_editorSelectSitemapNode",
        width: "90%",
        modal: false,
        height: "70%"
    });
    ccm_editorSelectSitemapNode = function(b, c) {
        var d = tinyMCE.activeEditor;
        d.selection.moveToBookmark(a);
        var e = d.selection.getContent();
        var f = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + "?cID=" + b;
        if (e != "") {
            d.execCommand("mceInsertLink", false, {
                href: f,
                title: c,
                target: null,
                "class": null
            });
        } else {
            var e = '<a href="' + CCM_BASE_URL + CCM_DISPATCHER_FILENAME + "?cID=" + b + '" title="' + c + '">' + c + "</a>";
            tinyMCE.execCommand("mceInsertRawHTML", false, e, true);
        }
    };
};

var ccm_arrangeMode = false;

var ccm_selectedDomID = false;

var ccm_isBlockError = false;

var ccm_activeMenu = false;

var ccm_blockError = false;

ccm_menuInit = function(a) {
    if (CCM_EDIT_MODE && !CCM_ARRANGE_MODE) {
        switch (a.type) {
          case "BLOCK":
            $("#b" + a.bID + "-" + a.aID).mouseover(function(b) {
                ccm_activate(a, "#b" + a.bID + "-" + a.aID);
            });
            break;
          case "AREA":
            $("#a" + a.aID + "controls").mouseover(function(b) {
                ccm_activate(a, "#a" + a.aID + "controls");
            });
            break;
        }
    }
};

ccm_showBlockMenu = function(a, b) {
    ccm_hideMenus();
    b.stopPropagation();
    ccm_activeMenu = true;
    var c = document.getElementById("ccm-block-menu" + a.bID + "-" + a.aID);
    if (!c) {
        el = document.createElement("DIV");
        el.id = "ccm-block-menu" + a.bID + "-" + a.aID;
        el.className = "ccm-menu ccm-ui";
        el.style.display = "block";
        el.style.visibility = "hidden";
        document.body.appendChild(el);
        c = $("#ccm-block-menu" + a.bID + "-" + a.aID);
        c.css("position", "absolute");
        var d = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
        d += "<ul>";
        if (a.canWrite && a.hasEditDialog) {
            d += a.editInline ? '<li><a class="ccm-menu-icon ccm-icon-edit-menu" onclick="ccm_hideMenus()" id="menuEdit' + a.bID + "-" + a.aID + '" href="' + CCM_DISPATCHER_FILENAME + "?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + "&btask=edit#_edit" + a.bID + '">' + ccmi18n.editBlock + "</a></li>" : '<li><a class="ccm-menu-icon ccm-icon-edit-menu" onclick="ccm_hideMenus()" dialog-title="' + ccmi18n.editBlock + " " + a.btName + '" dialog-append-buttons="true" dialog-modal="false" dialog-on-close="ccm_blockWindowAfterClose()" dialog-width="' + a.width + '" dialog-height="' + a.height + '" id="menuEdit' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=edit">' + ccmi18n.editBlock + "</a></li>";
        }
        if (a.canWriteStack) {
            d += '<li><a class="ccm-menu-icon ccm-icon-edit-menu" id="menuEdit' + a.bID + "-" + a.aID + '" href="' + CCM_DISPATCHER_FILENAME + "/dashboard/blocks/stacks/-/view_details/" + a.stID + '">' + ccmi18n.editStackContents + "</a></li>";
            d += '<li class="header"></li>';
        }
        if (a.canCopyToScrapbook) {
            d += '<li><a class="ccm-menu-icon ccm-icon-clipboard-menu" id="menuAddToScrapbook' + a.bID + "-" + a.aID + '" href="#" onclick="javascript:ccm_addToScrapbook(' + a.cID + "," + a.bID + ",'" + encodeURIComponent(a.arHandle) + "');return false;\">" + ccmi18n.copyBlockToScrapbook + "</a></li>";
        }
        if (a.canArrange) {
            d += '<li><a class="ccm-menu-icon ccm-icon-move-menu" id="menuArrange' + a.bID + "-" + a.aID + '" href="javascript:ccm_arrangeInit()">' + ccmi18n.arrangeBlock + "</a></li>";
        }
        if (a.canDelete) {
            d += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" id="menuDelete' + a.bID + "-" + a.aID + '" href="#" onclick="javascript:ccm_deleteBlock(' + a.cID + "," + a.bID + "," + a.aID + ", '" + encodeURIComponent(a.arHandle) + "', '" + a.deleteMessage + "');return false;\">" + ccmi18n.deleteBlock + "</a></li>";
        }
        if (a.canDesign || a.canEditBlockCustomTemplate) {
            d += '<li class="ccm-menu-separator"></li>';
        }
        if (a.canDesign) {
            d += '<li><a class="ccm-menu-icon ccm-icon-design-menu" onclick="ccm_hideMenus()" dialog-modal="false" dialog-title="' + ccmi18n.changeBlockBaseStyle + '" dialog-width="475" dialog-height="500" dialog-append-buttons="true" id="menuChangeCSS' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=block_css&modal=true&width=300&height=100" title="' + ccmi18n.changeBlockCSS + '">' + ccmi18n.changeBlockCSS + "</a></li>";
        }
        if (a.canEditBlockCustomTemplate) {
            d += '<li><a class="ccm-menu-icon ccm-icon-custom-template-menu" onclick="ccm_hideMenus()" dialog-append-buttons="true" \tdialog-modal="false" dialog-title="' + ccmi18n.changeBlockTemplate + '" dialog-width="300" dialog-height="275" id="menuChangeTemplate' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=template&modal=true&width=300&height=275" title="' + ccmi18n.changeBlockTemplate + '">' + ccmi18n.changeBlockTemplate + "</a></li>";
        }
        if (a.canModifyGroups || a.canScheduleGuestAccess || a.canAliasBlockOut || a.canSetupComposer) {
            d += '<li class="ccm-menu-separator"></li>';
        }
        if (a.canModifyGroups) {
            d += '<li><a title="' + ccmi18n.setBlockPermissions + '" onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-permissions-menu" dialog-width="420" dialog-height="350" id="menuBlockGroups' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=groups" dialog-append-buttons="true" dialog-title="' + ccmi18n.setBlockPermissions + '">' + ccmi18n.setBlockPermissions + "</a></li>";
        }
        if (a.canScheduleGuestAccess) {
            d += '<li><a title="' + ccmi18n.scheduleGuestAccess + '" onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-clock-menu" dialog-width="500" dialog-height="220" id="menuBlockViewClock' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=guest_timed_access" dialog-append-buttons="true" dialog-title="' + ccmi18n.scheduleGuestAccess + '">' + ccmi18n.scheduleGuestAccess + "</a></li>";
        }
        if (a.canAliasBlockOut) {
            d += '<li><a class="ccm-menu-icon ccm-icon-setup-child-pages-menu" dialog-append-buttons="true" onclick="ccm_hideMenus()" dialog-width="550" dialog-height="450" id="menuBlockAliasOut' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=child_pages" dialog-title="' + ccmi18n.setBlockAlias + '">' + ccmi18n.setBlockAlias + "</a></li>";
        }
        if (a.canSetupComposer) {
            d += '<li><a class="ccm-menu-icon ccm-icon-setup-composer-menu" dialog-append-buttons="true" onclick="ccm_hideMenus()" dialog-width="300" dialog-modal="false" dialog-height="130" id="menuBlockSetupComposer' + a.bID + "-" + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_block_popup.php?cID=" + a.cID + "&bID=" + a.bID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&btask=composer" dialog-title="' + ccmi18n.setBlockComposerSettings + '">' + ccmi18n.setBlockComposerSettings + "</a></li>";
        }
        d += "</ul>";
        d += "</div></div></div>";
        c.append(d);
        if (a.canWrite && !a.editInline) {
            $("a#menuEdit" + a.bID + "-" + a.aID).dialog();
        }
        if (a.canEditBlockCustomTemplate) {
            $("a#menuChangeTemplate" + a.bID + "-" + a.aID).dialog();
        }
        if (a.canDesign) {
            $("a#menuChangeCSS" + a.bID + "-" + a.aID).dialog();
        }
        if (a.canAliasBlockOut) {
            $("a#menuBlockAliasOut" + a.bID + "-" + a.aID).dialog();
        }
        if (a.canSetupComposer) {
            $("a#menuBlockSetupComposer" + a.bID + "-" + a.aID).dialog();
        }
        if (a.canModifyGroups) {
            $("#menuBlockGroups" + a.bID + "-" + a.aID).dialog();
        }
        if (a.canScheduleGuestAccess) {
            $("#menuBlockViewClock" + a.bID + "-" + a.aID).dialog();
        }
    } else {
        c = $("#ccm-block-menu" + a.bID + "-" + a.aID);
    }
    ccm_fadeInMenu(c, b);
};

ccm_reloadAreaMenuPermissions = function(a, b) {
    var c = window["ccm_areaMenuObj" + a];
    if (c) {
        var d = CCM_TOOLS_PATH + "/reload_area_permissions_js.php" + "?arHandle=" + c.arHandle + "&cID=" + b + "&maximumBlocks=" + c.maximumBlocks;
        $.getScript(d);
    }
};

ccm_openAreaAddBlock = function(a, b, c) {
    if (!b) {
        b = 0;
    }
    if (!c) {
        c = CCM_CID;
    }
    $.fn.dialog.open({
        title: ccmi18n.blockAreaMenu,
        href: CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + c + "&atask=add&arHandle=" + a + "&addOnly=" + b,
        width: 550,
        modal: false,
        height: 380
    });
};

ccm_showAreaMenu = function(a, b) {
    var c = a.addOnly ? 1 : 0;
    ccm_activeMenu = true;
    if (b.shiftKey) {
        ccm_openAreaAddBlock(a.arHandle, c);
    } else {
        b.stopPropagation();
        var d = document.getElementById("ccm-area-menu" + a.aID);
        if (!d) {
            el = document.createElement("DIV");
            el.id = "ccm-area-menu" + a.aID;
            el.className = "ccm-menu ccm-ui";
            el.style.display = "block";
            el.style.visibility = "hidden";
            document.body.appendChild(el);
            d = $("#ccm-area-menu" + a.aID);
            d.css("position", "absolute");
            var e = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
            e += "<ul>";
            if (a.canAddBlocks) {
                e += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-add-block-menu" dialog-title="' + ccmi18n.addBlockNew + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddNewBlock' + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(a.arHandle) + "&atask=add&addOnly=" + c + '">' + ccmi18n.addBlockNew + "</a></li>";
                e += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-clipboard-menu" dialog-title="' + ccmi18n.addBlockPaste + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddPaste' + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(a.arHandle) + "&atask=paste&addOnly=" + c + '">' + ccmi18n.addBlockPaste + "</a></li>";
            }
            if (a.canAddStacks) {
                e += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-add-stack-menu" dialog-title="' + ccmi18n.addBlockStack + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddNewStack' + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(a.arHandle) + "&atask=add_from_stack&addOnly=" + c + '">' + ccmi18n.addBlockStack + "</a></li>";
            }
            if (a.canAddBlocks && (a.canDesign || a.canLayout)) {
                e += '<li class="ccm-menu-separator"></li>';
            }
            if (a.canLayout) {
                e += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-add-layout-menu" dialog-title="' + ccmi18n.addAreaLayout + '" dialog-modal="false" dialog-width="400" dialog-height="300" dialog-append-buttons="true" id="menuAreaLayout' + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&atask=layout">' + ccmi18n.addAreaLayout + "</a></li>";
            }
            if (a.canDesign) {
                e += '<li><a onclick="ccm_hideMenus()" class="ccm-menu-icon ccm-icon-design-menu" dialog-title="' + ccmi18n.changeAreaCSS + '" dialog-modal="false" dialog-append-buttons="true" dialog-width="475" dialog-height="500" id="menuAreaStyle' + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&atask=design">' + ccmi18n.changeAreaCSS + "</a></li>";
            }
            if (a.canWrite && a.canModifyGroups) {
                e += '<li class="ccm-menu-separator"></li>';
            }
            if (a.canModifyGroups) {
                e += '<li><a onclick="ccm_hideMenus()" title="' + ccmi18n.setAreaPermissions + '" dialog-append-buttons="true" dialog-modal="false" class="ccm-menu-icon ccm-icon-permissions-menu" dialog-width="420" dialog-height="425" id="menuAreaGroups' + a.aID + '" href="' + CCM_TOOLS_PATH + "/edit_area_popup.php?cID=" + CCM_CID + "&arHandle=" + encodeURIComponent(a.arHandle) + '&atask=groups" dialog-title="' + ccmi18n.setAreaPermissions + '">' + ccmi18n.setAreaPermissions + "</a></li>";
            }
            e += "</ul>";
            e += "</div></div></div>";
            d.append(e);
            if (a.canAddBlocks) {
                $("a#menuAddNewBlock" + a.aID).dialog();
                $("a#menuAddPaste" + a.aID).dialog();
            }
            if (a.canAddStacks) {
                $("a#menuAddNewStack" + a.aID).dialog();
            }
            if (a.canLayout) {
                $("a#menuAreaLayout" + a.aID).dialog();
            }
            if (a.canDesign) {
                $("a#menuAreaStyle" + a.aID).dialog();
            }
            if (a.canModifyGroups) {
                $("a#menuAreaGroups" + a.aID).dialog();
            }
        } else {
            d = $("#ccm-area-menu" + a.aID);
        }
        ccm_fadeInMenu(d, b);
    }
};

ccm_hideHighlighter = function() {
    $("#ccm-highlighter").css("display", "none");
    $("div.ccm-menu-hotspot-active").removeClass("ccm-menu-hotspot-active");
};

ccm_addError = function(a) {
    if (!ccm_isBlockError) {
        ccm_blockError = "";
        ccm_blockError += "<ul>";
    }
    ccm_isBlockError = true;
    ccm_blockError += "<li>" + a + "</li>";
};

ccm_resetBlockErrors = function() {
    ccm_isBlockError = false;
    ccm_blockError = "";
};

ccm_addToScrapbook = function(a, b, c) {
    ccm_mainNavDisableDirectExit();
    ccm_hideHighlighter();
    $.ajax({
        type: "POST",
        url: CCM_TOOLS_PATH + "/pile_manager.php",
        data: "cID=" + a + "&bID=" + b + "&arHandle=" + c + "&btask=add&scrapbookName=userScrapbook",
        success: function(a) {
            ccm_hideHighlighter();
            ccmAlert.hud(ccmi18n.copyBlockToScrapbookMsg, 2e3, "add", ccmi18n.copyBlockToScrapbook);
        }
    });
};

ccm_deleteBlock = function(a, b, c, d, e) {
    if (confirm(e)) {
        ccm_mainNavDisableDirectExit();
        ccm_hideHighlighter();
        $d = $("#b" + b + "-" + c);
        $d.hide();
        ccmAlert.hud(ccmi18n.deleteBlockMsg, 2e3, "delete_small", ccmi18n.deleteBlock);
        $.ajax({
            type: "POST",
            url: CCM_DISPATCHER_FILENAME,
            data: "cID=" + a + "&ccm_token=" + CCM_SECURITY_TOKEN + "&isAjax=true&btask=remove&bID=" + b + "&arHandle=" + d
        });
        ccm_reloadAreaMenuPermissions(c, a);
    }
};

ccm_hideMenus = function() {
    ccm_activeMenu = false;
    $("div.ccm-menu").hide();
    $("div.ccm-menu").css("visibility", "hidden");
    $("div.ccm-menu").show();
};

ccm_parseBlockResponse = function(r, currentBlockID, task) {
    try {
        r = r.replace(/(<([^>]+)>)/ig, "");
        resp = eval("(" + r + ")");
        if (resp.error == true) {
            var message = "<ul>";
            for (i = 0; i < resp.response.length; i++) {
                message += "<li>" + resp.response[i] + "</li>";
            }
            message += "</ul>";
            ccmAlert.notice(ccmi18n.error, message);
        } else {
            ccm_blockWindowClose();
            if (resp.cID) {
                cID = resp.cID;
            } else {
                cID = CCM_CID;
            }
            var action = CCM_TOOLS_PATH + "/edit_block_popup?cID=" + cID + "&bID=" + resp.bID + "&arHandle=" + encodeURIComponent(resp.arHandle) + "&btask=view_edit_mode";
            $.get(action, function(a) {
                if (task == "add") {
                    if ($("#a" + resp.aID + " div.ccm-area-styles-a" + resp.aID).length > 0) {
                        $("#a" + resp.aID + " div.ccm-area-styles-a" + resp.aID).append(a);
                    } else {
                        $("#a" + resp.aID).append(a);
                    }
                } else {
                    $("#b" + currentBlockID + "-" + resp.aID).before(a).remove();
                }
                jQuery.fn.dialog.hideLoader();
                ccm_mainNavDisableDirectExit();
                if (task == "add") {
                    ccmAlert.hud(ccmi18n.addBlockMsg, 2e3, "add", ccmi18n.addBlock);
                    jQuery.fn.dialog.closeAll();
                } else {
                    ccmAlert.hud(ccmi18n.updateBlockMsg, 2e3, "success", ccmi18n.updateBlock);
                }
                if (typeof window.ccm_parseBlockResponsePost == "function") {
                    ccm_parseBlockResponsePost(resp);
                }
            });
            ccm_reloadAreaMenuPermissions(resp.aID, cID);
        }
    } catch (e) {
        ccmAlert.notice(ccmi18n.error, r);
    }
};

ccm_mainNavDisableDirectExit = function(a) {
    $("#ccm-exit-edit-mode-direct").hide();
    if (!a) {
        $("#ccm-exit-edit-mode-comment").show();
    }
};

ccm_setupBlockForm = function(a, b, c) {
    a.ajaxForm({
        type: "POST",
        iframe: true,
        beforeSubmit: function() {
            ccm_hideHighlighter();
            $("input[name=ccm-block-form-method]").val("AJAX");
            jQuery.fn.dialog.showLoader();
            return ccm_blockFormSubmit();
        },
        success: function(a) {
            ccm_parseBlockResponse(a, b, c);
        }
    });
};

ccm_activate = function(a, b) {
    if (ccm_arrangeMode || ccm_activeMenu) {
        return false;
    }
    if (ccm_selectedDomID) {
        $(ccm_selectedDomID).removeClass("ccm-menu-hotspot-active");
    }
    aobj = $(b);
    aobj.addClass("ccm-menu-hotspot-active");
    ccm_selectedDomID = b;
    offs = aobj.offset();
    $("#ccm-highlighter").hide();
    $("#ccm-highlighter").css("width", aobj.outerWidth());
    $("#ccm-highlighter").css("height", aobj.outerHeight());
    $("#ccm-highlighter").css("top", offs.top);
    $("#ccm-highlighter").css("left", offs.left);
    $("#ccm-highlighter").fadeIn(120, "easeOutExpo");
    $("#ccm-highlighter").mouseout(function(a) {
        if (!ccm_activeMenu) {
            if (!a.target) {
                ccm_hideHighlighter();
            } else if ($(a.toElement).parents("div.ccm-menu").length == 0) {
                ccm_hideHighlighter();
            }
        }
    });
    $("#ccm-highlighter").unbind("click");
    $("#ccm-highlighter").click(function(b) {
        switch (a.type) {
          case "BLOCK":
            ccm_showBlockMenu(a, b);
            break;
          case "AREA":
            ccm_showAreaMenu(a, b);
            break;
        }
    });
};

ccm_editInit = function() {
    document.write = function() {
        void 0;
    };
    $(document.body).append('<div style="position: absolute; display:none" id="ccm-highlighter">&nbsp;</div>');
    $(document).click(function() {
        ccm_hideMenus();
    });
    $("div.ccm-menu a").bind("click.hide-menu", function(a) {
        ccm_hideMenus();
        return false;
    });
};

ccm_triggerSelectUser = function(a, b, c) {
    alert(a);
    alert(b);
    alert(c);
};

ccm_setupUserSearch = function(a) {
    $(".chosen-select").chosen();
    $("#ccm-user-list-cb-all").click(function() {
        if ($(this).prop("checked") == true) {
            $(".ccm-list-record td.ccm-user-list-cb input[type=checkbox]").attr("checked", true);
            $("#ccm-user-list-multiple-operations").attr("disabled", false);
        } else {
            $(".ccm-list-record td.ccm-user-list-cb input[type=checkbox]").attr("checked", false);
            $("#ccm-user-list-multiple-operations").attr("disabled", true);
        }
    });
    $("td.ccm-user-list-cb input[type=checkbox]").click(function(a) {
        if ($("td.ccm-user-list-cb input[type=checkbox]:checked").length > 0) {
            $("#ccm-user-list-multiple-operations").attr("disabled", false);
        } else {
            $("#ccm-user-list-multiple-operations").attr("disabled", true);
        }
    });
    $("#ccm-user-list-multiple-operations").change(function() {
        var b = $(this).val();
        switch (b) {
          case "choose":
            var c = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                ccm_triggerSelectUser($(this).val(), $(this).attr("user-name"), $(this).attr("user-email"));
            });
            jQuery.fn.dialog.closeTop();
            break;
          case "properties":
            uIDstring = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                uIDstring = uIDstring + "&uID[]=" + $(this).val();
            });
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/users/bulk_properties?" + uIDstring,
                title: ccmi18n.properties
            });
            break;
          case "activate":
            uIDstring = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                uIDstring = uIDstring + "&uID[]=" + $(this).val();
            });
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/users/bulk_activate?searchInstance=" + a + "&" + uIDstring,
                title: ccmi18n.user_activate
            });
            break;
          case "deactivate":
            uIDstring = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                uIDstring = uIDstring + "&uID[]=" + $(this).val();
            });
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/users/bulk_deactivate?searchInstance=" + a + "&" + uIDstring,
                title: ccmi18n.user_deactivate
            });
            break;
          case "group_add":
            uIDstring = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                uIDstring = uIDstring + "&uID[]=" + $(this).val();
            });
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/users/bulk_group_add?searchInstance=" + a + "&" + uIDstring,
                title: ccmi18n.user_group_add
            });
            break;
          case "group_remove":
            uIDstring = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                uIDstring = uIDstring + "&uID[]=" + $(this).val();
            });
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/users/bulk_group_remove?searchInstance=" + a + "&" + uIDstring,
                title: ccmi18n.user_group_remove
            });
            break;
          case "delete":
            uIDstring = "";
            $("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
                uIDstring = uIDstring + "&uID[]=" + $(this).val();
            });
            jQuery.fn.dialog.open({
                width: 630,
                height: 450,
                modal: false,
                href: CCM_TOOLS_PATH + "/users/bulk_delete?searchInstance=" + a + "&" + uIDstring,
                title: ccmi18n.user_delete
            });
            break;
        }
        $(this).get(0).selectedIndex = 0;
    });
};

ccm_triggerSelectGroup = function(a, b) {
    alert(a);
    alert(b);
};

ccm_setupGroupSearchPaging = function() {
    $("div#ccm-group-paging").each(function() {
        $(this).closest(".ui-dialog-content").dialog("option", "buttons", [ {} ]);
        $(this).closest(".ui-dialog").find(".ui-dialog-buttonpane .ccm-pane-dialog-pagination").remove();
        $(this).appendTo($(this).closest(".ui-dialog").find(".ui-dialog-buttonpane").addClass("ccm-ui"));
    });
};

ccm_setupGroupSearch = function(a) {
    $("div.ccm-group a").unbind();
    if (a) {
        func = window[a];
    } else {
        func = ccm_triggerSelectGroup;
    }
    $("div.ccm-group a").each(function(a) {
        var b = $(this);
        $(this).click(function() {
            func(b.attr("group-id"), b.attr("group-name"));
            $.fn.dialog.closeTop();
            return false;
        });
    });
    $("#ccm-group-search").ajaxForm({
        beforeSubmit: function() {
            $("#ccm-group-search-wrapper").html("");
        },
        success: function(a) {
            $("#ccm-group-search-wrapper").html(a);
        }
    });
    ccm_setupGroupSearchPaging();
    $("div#ccm-group-paging a").click(function() {
        $("#ccm-group-search-wrapper").html("");
        $.ajax({
            type: "GET",
            url: $(this).attr("href"),
            success: function(a) {
                $("#ccm-group-search-wrapper").html(a);
            }
        });
        return false;
    });
};

ccm_saveArrangement = function(a) {
    if (!a) {
        a = CCM_CID;
    }
    ccm_mainNavDisableDirectExit();
    var b = "";
    $("div.ccm-area").each(function() {
        areaStr = "&area[" + $(this).attr("id").substring(1) + "][]=";
        bArray = $(this).sortable("toArray");
        for (i = 0; i < bArray.length; i++) {
            if (bArray[i] != "" && bArray[i].substring(0, 1) == "b") {
                var a = bArray[i].substring(1, bArray[i].indexOf("-"));
                var c = $("#" + bArray[i]);
                if (c.attr("custom-style")) {
                    a += "-" + c.attr("custom-style");
                }
                b += areaStr + a;
            }
        }
    });
    $.ajax({
        type: "POST",
        url: CCM_DISPATCHER_FILENAME,
        data: "cID=" + a + "&ccm_token=" + CCM_SECURITY_TOKEN + "&btask=ajax_do_arrange" + b,
        success: function(a) {
            $("div.ccm-area").removeClass("ccm-move-mode");
            $("div.ccm-block-arrange").each(function() {
                $(this).addClass("ccm-block");
                $(this).removeClass("ccm-block-arrange");
            });
            ccm_arrangeMode = false;
            $(".ccm-main-nav-edit-option").fadeIn(300);
            ccmAlert.hud(ccmi18n.arrangeBlockMsg, 2e3, "up_down", ccmi18n.arrangeBlock);
        }
    });
};

ccm_arrangeInit = function() {
    ccm_arrangeMode = true;
    ccm_hideHighlighter();
    $("div.ccm-block").each(function() {
        $(this).addClass("ccm-block-arrange");
        $(this).removeClass("ccm-block");
    });
    $(".ccm-main-nav-edit-option").fadeOut(300, function() {
        $(".ccm-main-nav-arrange-option").fadeIn(300);
    });
    $("div.ccm-area").each(function() {
        var a = $(this).attr("cID");
        $(this).addClass("ccm-move-mode");
        $(this).sortable({
            items: "div.ccm-block-arrange",
            connectWith: $("div.ccm-area-move-enabled"),
            accept: "div.ccm-block-arrange",
            opacity: .5,
            stop: function() {
                ccm_saveArrangement(a);
            }
        });
    });
};

if (typeof ccm_selectSitemapNode != "function") {
    ccm_selectSitemapNode = function(a, b) {
        alert(a);
        alert(b);
    };
}

ccm_goToSitemapNode = function(a, b) {
    window.location.href = CCM_DISPATCHER_FILENAME + "?cID=" + a;
};

ccm_fadeInMenu = function(a, b) {
    var c = a.find("div.popover div.inner").width();
    var d = a.find("div.popover").height();
    a.hide();
    a.css("visibility", "visible");
    var e = b.pageX + 2;
    var f = b.pageY + 2;
    if ($(window).height() < b.clientY + d) {
        f = f - d - 10;
        e = e - c / 2;
        a.find("div.popover").removeClass("below");
        a.find("div.popover").addClass("above");
    } else {
        e = e - c / 2;
        f = f + 10;
        a.find("div.popover").removeClass("above");
        a.find("div.popover").addClass("below");
    }
    a.css("top", f + "px");
    a.css("left", e + "px");
    a.fadeIn(60);
};

ccm_blockWindowClose = function() {
    jQuery.fn.dialog.closeTop();
    ccm_blockWindowAfterClose();
};

ccm_blockWindowAfterClose = function() {
    ccmValidateBlockForm = function() {
        return true;
    };
};

ccm_blockFormSubmit = function() {
    if (typeof window.ccmValidateBlockForm == "function") {
        r = window.ccmValidateBlockForm();
        if (ccm_isBlockError) {
            jQuery.fn.dialog.hideLoader();
            if (ccm_blockError) {
                ccmAlert.notice(ccmi18n.error, ccm_blockError + "</ul>");
            }
            ccm_resetBlockErrors();
            return false;
        }
    }
    return true;
};

ccm_paneToggleOptions = function(a) {
    var b = $(a).parent().find("div.ccm-pane-options-content");
    if ($(a).hasClass("ccm-icon-option-closed")) {
        $(a).removeClass("ccm-icon-option-closed").addClass("ccm-icon-option-open");
        b.slideDown("fast", "easeOutExpo");
    } else {
        $(a).removeClass("ccm-icon-option-open").addClass("ccm-icon-option-closed");
        b.slideUp("fast", "easeOutExpo");
    }
};

ccm_setupGridStriping = function(a) {
    $("#" + a + " tr").removeClass();
    var b = 0;
    $("#" + a + " tr").each(function() {
        if ($(this).css("display") != "none") {
            if (b % 2 == 0) {
                $(this).addClass("ccm-row-alt");
            }
            b++;
        }
    });
};

ccm_t = function(a) {
    return $("input[name=ccm-string-" + a + "]").val();
};

var ccmCustomStyle = {
    tabs: function(a, b) {
        $(".ccm-styleEditPane").hide();
        $("#ccm-styleEditPane-" + b).show();
        $(a.parentNode.parentNode).find("li").removeClass("ccm-nav-active");
        $(a.parentNode).addClass("ccm-nav-active");
        return false;
    },
    resetAll: function() {
        if (!confirm(ccmi18n.confirmCssReset)) {
            return false;
        }
        jQuery.fn.dialog.showLoader();
        $("#ccm-reset-style").val(1);
        $("#ccmCustomCssForm").get(0).submit();
        return true;
    },
    showPresetDeleteIcon: function() {
        if ($("select[name=cspID]").val() > 0) {
            $("#ccm-style-delete-preset").show();
        } else {
            $("#ccm-style-delete-preset").hide();
        }
    },
    deletePreset: function() {
        var a = $("select[name=cspID]").val();
        if (a > 0) {
            if (!confirm(ccmi18n.confirmCssPresetDelete)) return false;
            var b = $("#ccm-custom-style-refresh-action").val() + "&deleteCspID=" + a + "&subtask=delete_custom_style_preset";
            jQuery.fn.dialog.showLoader();
            $.get(b, function(a) {
                $("#ccm-custom-style-wrapper").html(a);
                jQuery.fn.dialog.hideLoader();
            });
        }
    },
    initForm: function() {
        if ($("#cspFooterPreset").length > 0) {
            $("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select, #ccmCustomCssFormTabs textarea").bind("change click", function() {
                $("#cspFooterPreset").show();
                $("#cspFooterNoPreset").remove();
                $("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select").unbind("change click");
            });
        }
        $("input[name=cspPresetAction]").click(function() {
            if ($(this).val() == "create_new_preset" && $(this).prop("checked")) {
                $("input[name=cspName]").attr("disabled", false).focus();
            } else {
                $("input[name=cspName]").val("").attr("disabled", true);
            }
        });
        ccmCustomStyle.showPresetDeleteIcon();
        ccmCustomStyle.lastPresetID = parseInt($("select[name=cspID]").val());
        $("select[name=cspID]").change(function() {
            var a = parseInt($(this).val());
            var b = parseInt($("input[name=selectedCsrID]").val());
            if (ccmCustomStyle.lastPresetID == a) return false;
            ccmCustomStyle.lastPresetID = a;
            jQuery.fn.dialog.showLoader();
            if (a > 0) {
                var c = $("#ccm-custom-style-refresh-action").val() + "&cspID=" + a;
            } else {
                var c = $("#ccm-custom-style-refresh-action").val() + "&csrID=" + b;
            }
            $.get(c, function(a) {
                $("#ccm-custom-style-wrapper").html(a);
                jQuery.fn.dialog.hideLoader();
            });
        });
        $("#ccmCustomCssForm").submit(function() {
            if ($("input[name=cspCreateNew]").prop("checked") == true) {
                if ($("input[name=cspName]").val() == "") {
                    $("input[name=cspName]").focus();
                    alert(ccmi18n.errorCustomStylePresetNoName);
                    return false;
                }
            }
            jQuery.fn.dialog.showLoader();
            return true;
        });
        if (!parseInt(ccmCustomStyle.lastPresetID)) setTimeout('$("#ccmCustomCssFormTabs input").attr("disabled", false).get(0).focus()', 500);
    },
    validIdCheck: function(a, b) {
        var c = $("#" + a.value);
        if (c && c.get(0) && c.get(0).id != b) {
            $("#ccm-styles-invalid-id").css("display", "block");
        } else {
            $("#ccm-styles-invalid-id").css("display", "none");
        }
    }
};

$(function() {
    if ($("#ccm-toolbar").length > 0) {
        ccm_intelligentSearchActivateResults();
        ccm_intelligentSearchDoRemoteCalls($("#ccm-nav-intelligent-search").val());
    }
});

ccm_togglePopover = function(a, b) {
    if ($(".popover").is(":visible")) {
        $(b).popover("hide");
    } else {
        $(b).popover("show");
        a.stopPropagation();
        $(window).bind("click.popover", function() {
            $(b).popover("hide");
            $(window).unbind("click.popover");
        });
    }
};

ccm_toggleQuickNav = function(a, b) {
    var c = $("#ccm-add-to-quick-nav");
    if (c.hasClass("ccm-icon-favorite-selected")) {
        c.removeClass("ccm-icon-favorite-selected").addClass("ccm-icon-favorite");
    } else {
        c.removeClass("ccm-icon-favorite").addClass("ccm-icon-favorite-selected");
    }
    var d = $("#ccm-nav-dashboard");
    var e = c.parent().parent().parent().find("h3");
    e.css("display", "inline");
    e.effect("transfer", {
        to: d,
        easing: "easeOutExpo"
    }, 600);
    $.get(CCM_TOOLS_PATH + "/dashboard/add_to_quick_nav", {
        cID: a,
        token: b
    }, function(a) {
        var b = $("<div />").html(a);
        $("#ccm-intelligent-search-results").html(b.find("#ccm-intelligent-search-results").html());
        $("#ccm-dashboard-overlay").html(b.find("#ccm-dashboard-overlay").html());
        b = false;
    });
};

var ccm_hideToolbarMenusTimer = false;

ccm_hideToolbarMenus = function() {
    $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
    $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
    $("#ccm-edit-overlay").fadeOut(90, "easeOutExpo");
    $("#ccm-dashboard-overlay").fadeOut(90, "easeOutExpo");
    clearTimeout(ccm_hideToolbarMenusTimer);
};

ccm_activateToolbar = function() {
    $("#ccm-dashboard-overlay").css("visibility", "visible").hide();
    $("#ccm-nav-intelligent-search-wrapper").click(function() {
        $("#ccm-nav-intelligent-search").focus();
    });
    $("#ccm-nav-intelligent-search").focus(function() {
        $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
        $(this).parent().addClass("ccm-system-nav-selected");
        if ($("#ccm-dashboard-overlay").is(":visible")) {
            $("#ccm-dashboard-overlay").fadeOut(90, "easeOutExpo");
            $(window).unbind("click.dashboard-nav");
        }
    });
    $(".ccm-nav-edit-mode-active").click(function() {
        void 0;
        return false;
    });
    $("#ccm-edit-overlay,#ccm-dashboard-overlay").mouseover(function() {
        clearTimeout(ccm_hideToolbarMenusTimer);
    });
    $("#ccm-nav-dashboard").hoverIntent(function() {
        clearTimeout(ccm_hideToolbarMenusTimer);
        $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
        $(this).parent().addClass("ccm-system-nav-selected");
        $("#ccm-nav-intelligent-search").val("");
        $("#ccm-intelligent-search-results").fadeOut(90, "easeOutExpo");
        if ($("#ccm-edit-overlay").is(":visible")) {
            $("#ccm-edit-overlay").fadeOut(90, "easeOutExpo");
            $(window).unbind("click.ccm-edit");
        }
        $("#ccm-dashboard-overlay").fadeIn(160, "easeOutExpo");
        $(window).bind("click.dashboard-nav", function() {
            $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
            $("#ccm-dashboard-overlay").fadeOut(90, "easeOutExpo");
            $(window).unbind("click.dashboard-nav");
        });
        return false;
    }, function() {});
    $("#ccm-nav-dashboard,#ccm-dashboard-overlay,#ccm-nav-edit,#ccm-edit-overlay").mouseout(function() {
        ccm_hideToolbarMenusTimer = setTimeout(function() {
            ccm_hideToolbarMenus();
        }, 1500);
    });
    $("#ccm-nav-intelligent-search").bind("keydown.ccm-intelligent-search", function(a) {
        if (a.keyCode == 13 || a.keyCode == 40 || a.keyCode == 38) {
            a.preventDefault();
            a.stopPropagation();
            if (a.keyCode == 13 && $("a.ccm-intelligent-search-result-selected").length > 0) {
                var b = $("a.ccm-intelligent-search-result-selected").attr("href");
                if (!b || b == "#" || b == "javascript:void(0)") {
                    $("a.ccm-intelligent-search-result-selected").click();
                } else {
                    window.location.href = b;
                }
            }
            var c = $("#ccm-intelligent-search-results li:visible");
            var d;
            if (a.keyCode == 40 || a.keyCode == 38) {
                $.each(c, function(b, f) {
                    if ($(f).children("a").hasClass("ccm-intelligent-search-result-selected")) {
                        if (a.keyCode == 38) {
                            io = c[b - 1];
                        } else {
                            io = c[b + 1];
                        }
                        d = $(io).find("a");
                    }
                });
                if (d && d.length > 0) {
                    $("a.ccm-intelligent-search-result-selected").removeClass();
                    $(d).addClass("ccm-intelligent-search-result-selected");
                }
            }
        }
    });
    $("#ccm-nav-intelligent-search").bind("keyup.ccm-intelligent-search", function(a) {
        ccm_intelligentSearchDoRemoteCalls($(this).val());
    });
    $("#ccm-nav-intelligent-search").blur(function() {
        $(this).parent().removeClass("ccm-system-nav-selected");
    });
    $("#ccm-nav-intelligent-search").liveUpdate("ccm-intelligent-search-results", "intelligent-search");
    $("#ccm-nav-intelligent-search").bind("click", function(a) {
        if (this.value == "") {
            $("#ccm-intelligent-search-results").hide();
        }
    });
    $("#ccm-toolbar-nav-properties").dialog();
    $("#ccm-toolbar-nav-preview-as-user").dialog();
    $("#ccm-toolbar-add-subpage").dialog();
    $("#ccm-toolbar-nav-versions").dialog();
    $("#ccm-toolbar-nav-design").dialog();
    $("#ccm-toolbar-nav-permissions").dialog();
    $("#ccm-toolbar-nav-speed-settings").dialog();
    $("#ccm-toolbar-nav-move-copy").dialog();
    $("#ccm-toolbar-nav-delete").dialog();
    $("#ccm-edit-overlay,#ccm-dashboard-overlay").click(function(a) {
        a.stopPropagation();
    });
    $("#ccm-nav-edit").hoverIntent(function() {
        clearTimeout(ccm_hideToolbarMenusTimer);
        $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
        $(this).parent().addClass("ccm-system-nav-selected");
        $("#ccm-nav-intelligent-search").val("");
        $("#ccm-intelligent-search-results").fadeOut(90, "easeOutExpo");
        if ($("#ccm-dashboard-overlay").is(":visible")) {
            $("#ccm-dashboard-overlay").fadeOut(90, "easeOutExpo");
            $(window).unbind("click.dashboard-nav");
        }
        setTimeout("$('#ccm-check-in-comments').focus();", 300);
        $("#ccm-check-in-preview").click(function() {
            $("#ccm-approve-field").val("PREVIEW");
            $("#ccm-check-in").submit();
        });
        $("#ccm-check-in-discard").click(function() {
            $("#ccm-approve-field").val("DISCARD");
            $("#ccm-check-in").submit();
        });
        $("#ccm-check-in-publish").click(function() {
            $("#ccm-approve-field").val("APPROVE");
            $("#ccm-check-in").submit();
        });
        var a = 30;
        $("#ccm-edit-overlay").css("left", a + "px");
        $("#ccm-edit-overlay").fadeIn(160, "easeOutExpo", function() {
            $(this).find("a").click(function() {
                ccm_toolbarCloseEditMenu();
            });
        });
        $(window).bind("click.ccm-edit", function() {
            ccm_toolbarCloseEditMenu();
        });
        return false;
    }, function() {});
};

var ajaxtimer = null;

var ajaxquery = null;

ccm_toolbarCloseEditMenu = function() {
    $(".ccm-system-nav-selected").removeClass("ccm-system-nav-selected");
    $("#ccm-edit-overlay").fadeOut(90, "easeOutExpo");
    $(window).unbind("click.ccm-edit");
};

ccm_intelligentSearchActivateResults = function() {
    if ($("#ccm-intelligent-search-results div:visible").length == 0) {
        $("#ccm-intelligent-search-results").hide();
    }
    $("#ccm-intelligent-search-results a").hover(function() {
        $("a.ccm-intelligent-search-result-selected").removeClass();
        $(this).addClass("ccm-intelligent-search-result-selected");
    }, function() {
        $(this).removeClass("ccm-intelligent-search-result-selected");
    });
};

ccm_intelligentSearchDoRemoteCalls = function(a) {
    a = jQuery.trim(a);
    if (!a) {
        return;
    }
    if (a.length > 2) {
        if (a == ajaxquery) {
            return;
        }
        if (ajaxtimer) {
            window.clearTimeout(ajaxtimer);
        }
        ajaxquery = a;
        ajaxtimer = window.setTimeout(function() {
            ajaxtimer = null;
            $("#ccm-intelligent-search-results-list-marketplace").parent().show();
            $("#ccm-intelligent-search-results-list-help").parent().show();
            $("#ccm-intelligent-search-results-list-your-site").parent().show();
            $("#ccm-intelligent-search-results-list-marketplace").parent().addClass("ccm-intelligent-search-results-module-loading");
            $("#ccm-intelligent-search-results-list-help").parent().addClass("ccm-intelligent-search-results-module-loading");
            $("#ccm-intelligent-search-results-list-your-site").parent().addClass("ccm-intelligent-search-results-module-loading");
            $.getJSON(CCM_TOOLS_PATH + "/marketplace/intelligent_search", {
                q: ajaxquery
            }, function(a) {
                $("#ccm-intelligent-search-results-list-marketplace").parent().removeClass("ccm-intelligent-search-results-module-loading");
                $("#ccm-intelligent-search-results-list-marketplace").html("");
                for (i = 0; i < a.length; i++) {
                    var b = a[i];
                    var c = "ccm_getMarketplaceItemDetails(" + b.mpID + ")";
                    $("#ccm-intelligent-search-results-list-marketplace").append('<li><a href="javascript:void(0)" onclick="' + c + '"><img src="' + b.img + '" />' + b.name + "</a></li>");
                }
                if (a.length == 0) {
                    $("#ccm-intelligent-search-results-list-marketplace").parent().hide();
                }
                if ($(".ccm-intelligent-search-result-selected").length == 0) {
                    $("#ccm-intelligent-search-results").find("li a").removeClass("ccm-intelligent-search-result-selected");
                    $("#ccm-intelligent-search-results li:visible a:first").addClass("ccm-intelligent-search-result-selected");
                }
                ccm_intelligentSearchActivateResults();
            }).error(function() {
                $("#ccm-intelligent-search-results-list-marketplace").parent().hide();
            });
            $.getJSON(CCM_TOOLS_PATH + "/get_remote_help", {
                q: ajaxquery
            }, function(a) {
                $("#ccm-intelligent-search-results-list-help").parent().removeClass("ccm-intelligent-search-results-module-loading");
                $("#ccm-intelligent-search-results-list-help").html("");
                for (i = 0; i < a.length; i++) {
                    var b = a[i];
                    $("#ccm-intelligent-search-results-list-help").append('<li><a href="' + b.href + '">' + b.name + "</a></li>");
                }
                if (a.length == 0) {
                    $("#ccm-intelligent-search-results-list-help").parent().hide();
                }
                if ($(".ccm-intelligent-search-result-selected").length == 0) {
                    $("#ccm-intelligent-search-results").find("li a").removeClass("ccm-intelligent-search-result-selected");
                    $("#ccm-intelligent-search-results li:visible a:first").addClass("ccm-intelligent-search-result-selected");
                }
                ccm_intelligentSearchActivateResults();
            }).error(function() {
                $("#ccm-intelligent-search-results-list-help").parent().hide();
            });
            $.getJSON(CCM_TOOLS_PATH + "/pages/intelligent_search", {
                q: ajaxquery
            }, function(a) {
                $("#ccm-intelligent-search-results-list-your-site").parent().removeClass("ccm-intelligent-search-results-module-loading");
                $("#ccm-intelligent-search-results-list-your-site").html("");
                for (i = 0; i < a.length; i++) {
                    var b = a[i];
                    $("#ccm-intelligent-search-results-list-your-site").append('<li><a href="' + b.href + '">' + b.name + "</a></li>");
                }
                if (a.length == 0) {
                    $("#ccm-intelligent-search-results-list-your-site").parent().hide();
                }
                if ($(".ccm-intelligent-search-result-selected").length == 0) {
                    $("#ccm-intelligent-search-results").find("li a").removeClass("ccm-intelligent-search-result-selected");
                    $("#ccm-intelligent-search-results li:visible a:first").addClass("ccm-intelligent-search-result-selected");
                }
                ccm_intelligentSearchActivateResults();
            }).error(function() {
                $("#ccm-intelligent-search-results-list-your-site").parent().hide();
            });
        }, 500);
    }
};

ccm_marketplaceDetailShowMore = function() {
    $(".ccm-marketplace-item-information-more").hide();
    $(".ccm-marketplace-item-information-inner").css("max-height", "none");
};

ccm_marketplaceUpdatesShowMore = function(a) {
    $(a).parent().hide();
    $(a).parent().parent().find(".ccm-marketplace-update-changelog").css("max-height", "none");
};

ccm_enableDesignScrollers = function() {
    $("a.ccm-scroller-l").hover(function() {
        $(this).find("img").attr("src", CCM_IMAGE_PATH + "/button_scroller_l_active.png");
    }, function() {
        $(this).find("img").attr("src", CCM_IMAGE_PATH + "/button_scroller_l.png");
    });
    $("a.ccm-scroller-r").hover(function() {
        $(this).find("img").attr("src", CCM_IMAGE_PATH + "/button_scroller_r_active.png");
    }, function() {
        $(this).find("img").attr("src", CCM_IMAGE_PATH + "/button_scroller_r.png");
    });
    var a = 4;
    var b = 132;
    $("a.ccm-scroller-r").unbind("click");
    $("a.ccm-scroller-l").unbind("click");
    $("a.ccm-scroller-r").click(function() {
        var c = $(this).parent().children("div.ccm-scroller-inner").children("ul");
        var d = $(this).parent().attr("current-page");
        var e = $(this).parent().attr("current-pos");
        var f = $(this).parent().attr("num-pages");
        var g = a * b;
        e = parseInt(e) - g;
        d++;
        $(this).parent().attr("current-page", d);
        $(this).parent().attr("current-pos", e);
        if (d == f) {
            $(this).hide();
        }
        if (d > 1) {
            $(this).siblings("a.ccm-scroller-l").show();
        }
        $(c).css("left", e + "px");
    });
    $("a.ccm-scroller-l").click(function() {
        var c = $(this).parent().children("div.ccm-scroller-inner").children("ul");
        var d = $(this).parent().attr("current-page");
        var e = $(this).parent().attr("current-pos");
        var f = $(this).parent().attr("num-pages");
        var g = a * b;
        e = parseInt(e) + g;
        d--;
        $(this).parent().attr("current-page", d);
        $(this).parent().attr("current-pos", e);
        if (d == 1) {
            $(this).hide();
        }
        if (d < f) {
            $(this).siblings("a.ccm-scroller-r").show();
        }
        $(c).css("left", e + "px");
    });
    $("a.ccm-scroller-l").hide();
    $("a.ccm-scroller-r").each(function() {
        if (parseInt($(this).parent().attr("num-pages")) == 1) {
            $(this).hide();
        }
    });
    $("#ccm-select-page-type a").click(function() {
        $("#ccm-select-page-type li").each(function() {
            $(this).removeClass("ccm-item-selected");
        });
        $(this).parent().addClass("ccm-item-selected");
        $("input[name=ctID]").val($(this).attr("ccm-page-type-id"));
    });
    $("#ccm-select-theme a").click(function() {
        $("#ccm-select-theme li").each(function() {
            $(this).removeClass("ccm-item-selected");
        });
        $(this).parent().addClass("ccm-item-selected");
        $("input[name=plID]").val($(this).attr("ccm-theme-id"));
    });
};