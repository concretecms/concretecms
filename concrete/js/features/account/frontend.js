/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js":
/*!******************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js ***!
  \******************************************************************************/
/***/ ((module) => {



/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (cssWithMappingToString) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item);
      if (item[2]) {
        return "@media ".concat(item[2], " {").concat(content, "}");
      }
      return content;
    }).join("");
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names

  list.i = function (modules, mediaQuery, dedupe) {
    if (typeof modules === "string") {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, ""]];
    }
    var alreadyImportedModules = {};
    if (dedupe) {
      for (var i = 0; i < this.length; i++) {
        // eslint-disable-next-line prefer-destructuring
        var id = this[i][0];
        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }
    for (var _i = 0; _i < modules.length; _i++) {
      var item = [].concat(modules[_i]);
      if (dedupe && alreadyImportedModules[item[0]]) {
        // eslint-disable-next-line no-continue
        continue;
      }
      if (mediaQuery) {
        if (!item[2]) {
          item[2] = mediaQuery;
        } else {
          item[2] = "".concat(mediaQuery, " and ").concat(item[2]);
        }
      }
      list.push(item);
    }
  };
  return list;
};

/***/ }),

/***/ "./node_modules/vue-advanced-cropper/dist/index.es.js":
/*!************************************************************!*\
  !*** ./node_modules/vue-advanced-cropper/dist/index.es.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "BackgroundWrapper": () => (/* binding */ Nt),
/* harmony export */   "BoundingBox": () => (/* binding */ Pt),
/* harmony export */   "CircleStencil": () => (/* binding */ Jt),
/* harmony export */   "Cropper": () => (/* binding */ ne),
/* harmony export */   "DragEvent": () => (/* binding */ W),
/* harmony export */   "DraggableArea": () => (/* binding */ Lt),
/* harmony export */   "DraggableElement": () => (/* binding */ O),
/* harmony export */   "HandlerWrapper": () => (/* binding */ H),
/* harmony export */   "LineWrapper": () => (/* binding */ P),
/* harmony export */   "MoveEvent": () => (/* binding */ E),
/* harmony export */   "Preview": () => (/* binding */ Zt),
/* harmony export */   "PreviewResult": () => (/* binding */ Yt),
/* harmony export */   "RectangleStencil": () => (/* binding */ Qt),
/* harmony export */   "ResizeEvent": () => (/* binding */ C),
/* harmony export */   "SimpleHandler": () => (/* binding */ Et),
/* harmony export */   "SimpleLine": () => (/* binding */ Ot),
/* harmony export */   "StencilPreview": () => (/* binding */ qt),
/* harmony export */   "TransformableImage": () => (/* binding */ Ut)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function e(t, e) {
  var i = Object.keys(t);
  if (Object.getOwnPropertySymbols) {
    var n = Object.getOwnPropertySymbols(t);
    e && (n = n.filter(function (e) {
      return Object.getOwnPropertyDescriptor(t, e).enumerable;
    })), i.push.apply(i, n);
  }
  return i;
}
function i(t) {
  for (var i = 1; i < arguments.length; i++) {
    var s = null != arguments[i] ? arguments[i] : {};
    i % 2 ? e(Object(s), !0).forEach(function (e) {
      n(t, e, s[e]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(s)) : e(Object(s)).forEach(function (e) {
      Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(s, e));
    });
  }
  return t;
}
function n(t, e, i) {
  return e in t ? Object.defineProperty(t, e, {
    value: i,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : t[e] = i, t;
}
function s(t, e) {
  if (null == t) return {};
  var i,
    n,
    s = function (t, e) {
      if (null == t) return {};
      var i,
        n,
        s = {},
        o = Object.keys(t);
      for (n = 0; n < o.length; n++) i = o[n], e.indexOf(i) >= 0 || (s[i] = t[i]);
      return s;
    }(t, e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(t);
    for (n = 0; n < o.length; n++) i = o[n], e.indexOf(i) >= 0 || Object.prototype.propertyIsEnumerable.call(t, i) && (s[i] = t[i]);
  }
  return s;
}
function o(t) {
  return function (t) {
    if (Array.isArray(t)) return r(t);
  }(t) || function (t) {
    if ("undefined" != typeof Symbol && null != t[Symbol.iterator] || null != t["@@iterator"]) return Array.from(t);
  }(t) || function (t, e) {
    if (!t) return;
    if ("string" == typeof t) return r(t, e);
    var i = Object.prototype.toString.call(t).slice(8, -1);
    "Object" === i && t.constructor && (i = t.constructor.name);
    if ("Map" === i || "Set" === i) return Array.from(t);
    if ("Arguments" === i || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)) return r(t, e);
  }(t) || function () {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }();
}
function r(t, e) {
  (null == e || e > t.length) && (e = t.length);
  for (var i = 0, n = new Array(e); i < e; i++) n[i] = t[i];
  return n;
}
var a,
  h,
  c,
  l = (a = function a(t) {
    /*!
      Copyright (c) 2018 Jed Watson.
      Licensed under the MIT License (MIT), see
      http://jedwatson.github.io/classnames
    */
    !function () {
      var e = {}.hasOwnProperty;
      function i() {
        for (var t = [], n = 0; n < arguments.length; n++) {
          var s = arguments[n];
          if (s) {
            var o = _typeof(s);
            if ("string" === o || "number" === o) t.push(s);else if (Array.isArray(s)) {
              if (s.length) {
                var r = i.apply(null, s);
                r && t.push(r);
              }
            } else if ("object" === o) if (s.toString === Object.prototype.toString) for (var a in s) e.call(s, a) && s[a] && t.push(a);else t.push(s.toString());
          }
        }
        return t.join(" ");
      }
      t.exports ? (i["default"] = i, t.exports = i) : window.classNames = i;
    }();
  }, a(c = {
    path: h,
    exports: {},
    require: function require(t, e) {
      return function () {
        throw new Error("Dynamic requires are not currently supported by @rollup/plugin-commonjs");
      }(null == e && c.path);
    }
  }, c.exports), c.exports),
  d = function d(t) {
    return function (e, i) {
      if (!e) return t;
      var n;
      "string" == typeof e ? n = e : i = e;
      var s = t;
      return n && (s += "__" + n), s + (i ? Object.keys(i).reduce(function (t, e) {
        var n = i[e];
        return n && (t += " " + ("boolean" == typeof n ? s + "--" + e : s + "--" + e + "_" + n)), t;
      }, "") : "");
    };
  };
function u(t, e, i) {
  var n, s, o, r, a;
  function h() {
    var c = Date.now() - r;
    c < e && c >= 0 ? n = setTimeout(h, e - c) : (n = null, i || (a = t.apply(o, s), o = s = null));
  }
  null == e && (e = 100);
  var c = function c() {
    o = this, s = arguments, r = Date.now();
    var c = i && !n;
    return n || (n = setTimeout(h, e)), c && (a = t.apply(o, s), o = s = null), a;
  };
  return c.clear = function () {
    n && (clearTimeout(n), n = null);
  }, c.flush = function () {
    n && (a = t.apply(o, s), o = s = null, clearTimeout(n), n = null);
  }, c;
}
u.debounce = u;
var m = u,
  _f = function f() {
    return _f = Object.assign || function (t) {
      for (var e, i = 1, n = arguments.length; i < n; i++) for (var s in e = arguments[i]) Object.prototype.hasOwnProperty.call(e, s) && (t[s] = e[s]);
      return t;
    }, _f.apply(this, arguments);
  };
/*! *****************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
function p(t, e) {
  var i, n;
  return t && e ? (i = "" + t + e[0].toUpperCase() + e.slice(1), n = t + "-" + e) : (i = t || e, n = t || e), {
    name: i,
    classname: n
  };
}
function g(t) {
  return /^blob:/.test(t);
}
function v(t) {
  return g(t) || function (t) {
    return /^data:/.test(t);
  }(t);
}
function b(t) {
  return !!(t && t.constructor && t.call && t.apply);
}
function w(t) {
  return void 0 === t;
}
function y(t) {
  return "object" == _typeof(t) && null !== t;
}
function z(t, e, i) {
  var n = {};
  return y(t) ? (Object.keys(e).forEach(function (s) {
    w(t[s]) ? n[s] = e[s] : y(e[s]) ? y(t[s]) ? n[s] = z(t[s], e[s], i[s]) : n[s] = t[s] ? e[s] : i[s] : !0 === e[s] || !1 === e[s] ? n[s] = Boolean(t[s]) : n[s] = t[s];
  }), n) : t ? e : i;
}
function R(t) {
  var e = Number(t);
  return Number.isNaN(e) ? t : e;
}
function A(t) {
  return _typeof("number" == t || function (t) {
    return "object" == _typeof(t) && null !== t;
  }(t) && "[object Number]" == toString.call(t)) && !S(t);
}
function S(t) {
  return t != t;
}
function x(t, e) {
  return Math.sqrt(Math.pow(t.x - e.x, 2) + Math.pow(t.y - e.y, 2));
}
var M = function M(t, e) {
    void 0 === t && (t = {}), void 0 === e && (e = {}), this.type = "manipulateImage", this.move = t, this.scale = e;
  },
  C = function C(t, e) {
    void 0 === e && (e = {}), this.type = "resize", this.directions = t, this.params = e;
  },
  E = function E(t) {
    this.type = "move", this.directions = t;
  },
  W = function () {
    function t(t, e, i, n, s) {
      this.type = "drag", this.nativeEvent = t, this.position = i, this.previousPosition = n, this.element = e, this.anchor = s;
    }
    return t.prototype.shift = function () {
      var t = this,
        e = t.element,
        i = t.anchor,
        n = t.position,
        s = e.getBoundingClientRect(),
        o = s.left,
        r = s.top;
      return {
        left: n.left - o - i.left,
        top: n.top - r - i.top
      };
    }, t;
  }();
function T(t, e, i, n, s, o, r, a, h, c) {
  "boolean" != typeof r && (h = a, a = r, r = !1);
  var l = "function" == typeof i ? i.options : i;
  var d;
  if (t && t.render && (l.render = t.render, l.staticRenderFns = t.staticRenderFns, l._compiled = !0, s && (l.functional = !0)), n && (l._scopeId = n), o ? (d = function d(t) {
    (t = t || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) || "undefined" == typeof __VUE_SSR_CONTEXT__ || (t = __VUE_SSR_CONTEXT__), e && e.call(this, h(t)), t && t._registeredComponents && t._registeredComponents.add(o);
  }, l._ssrRegister = d) : e && (d = r ? function (t) {
    e.call(this, c(t, this.$root.$options.shadowRoot));
  } : function (t) {
    e.call(this, a(t));
  }), d) if (l.functional) {
    var _t2 = l.render;
    l.render = function (e, i) {
      return d.call(i), _t2(e, i);
    };
  } else {
    var _t3 = l.beforeCreate;
    l.beforeCreate = _t3 ? [].concat(_t3, d) : [d];
  }
  return i;
}
var O = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("div", {
        ref: "draggable",
        "class": t.classname,
        on: {
          touchstart: t.onTouchStart,
          mousedown: t.onMouseDown,
          mouseover: t.onMouseOver,
          mouseleave: t.onMouseLeave
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "DraggableElement",
    mixins: [{
      beforeMount: function beforeMount() {
        window.addEventListener("mouseup", this.onMouseUp, {
          passive: !1
        }), window.addEventListener("mousemove", this.onMouseMove, {
          passive: !1
        }), window.addEventListener("touchmove", this.onTouchMove, {
          passive: !1
        }), window.addEventListener("touchend", this.onTouchEnd, {
          passive: !1
        });
      },
      beforeDestroy: function beforeDestroy() {
        window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
      },
      mounted: function mounted() {
        if (!this.$refs.draggable) throw new Error('You should add ref "draggable" to your root element to use draggable mixin');
        this.touches = [], this.hovered = !1;
      },
      methods: {
        onMouseOver: function onMouseOver() {
          this.hovered || (this.hovered = !0, this.$emit("enter"));
        },
        onMouseLeave: function onMouseLeave() {
          this.hovered && !this.touches.length && (this.hovered = !1, this.$emit("leave"));
        },
        onTouchStart: function onTouchStart(t) {
          t.cancelable && !this.disabled && 1 === t.touches.length && (this.touches = o(t.touches), this.hovered || (this.$emit("enter"), this.hovered = !0), t.touches.length && this.initAnchor(this.touches.reduce(function (e, i) {
            return {
              clientX: e.clientX + i.clientX / t.touches.length,
              clientY: e.clientY + i.clientY / t.touches.length
            };
          }, {
            clientX: 0,
            clientY: 0
          })), t.preventDefault && t.preventDefault(), t.stopPropagation());
        },
        onTouchEnd: function onTouchEnd() {
          this.processEnd();
        },
        onTouchMove: function onTouchMove(t) {
          this.touches.length && (this.processMove(t, t.touches), t.preventDefault && t.preventDefault(), t.stopPropagation && t.stopPropagation());
        },
        onMouseDown: function onMouseDown(t) {
          if (!this.disabled && 0 === t.button) {
            var e = {
              fake: !0,
              clientX: t.clientX,
              clientY: t.clientY
            };
            this.touches = [e], this.initAnchor(e), t.stopPropagation();
          }
        },
        onMouseMove: function onMouseMove(t) {
          this.touches.length && (this.processMove(t, [{
            fake: !0,
            clientX: t.clientX,
            clientY: t.clientY
          }]), t.preventDefault && t.preventDefault());
        },
        onMouseUp: function onMouseUp() {
          this.processEnd();
        },
        initAnchor: function initAnchor(t) {
          var e = this.$refs.draggable.getBoundingClientRect(),
            i = e.left,
            n = e.right,
            s = e.bottom,
            o = e.top;
          this.anchor = {
            left: t.clientX - i,
            top: t.clientY - o,
            bottom: s - t.clientY,
            right: n - t.clientX
          };
        },
        processMove: function processMove(t, e) {
          var i = o(e);
          if (this.touches.length) {
            if (1 === this.touches.length && 1 === i.length) {
              var n = this.$refs.draggable;
              this.$emit("drag", new W(t, n, {
                left: i[0].clientX,
                top: i[0].clientY
              }, {
                left: this.touches[0].clientX,
                top: this.touches[0].clientY
              }, this.anchor));
            }
            this.touches = i;
          }
        },
        processEnd: function processEnd() {
          this.touches.length && this.$emit("drag-end"), this.hovered && (this.$emit("leave"), this.hovered = !1), this.touches = [];
        }
      }
    }],
    props: {
      classname: {
        type: String
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  $ = d("vue-handler-wrapper"),
  H = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.root
      }, [i("DraggableElement", {
        "class": t.classes.draggable,
        on: {
          drag: function drag(e) {
            return t.$emit("drag", e);
          },
          "drag-end": function dragEnd(e) {
            return t.$emit("drag-end");
          },
          leave: function leave(e) {
            return t.$emit("leave");
          },
          enter: function enter(e) {
            return t.$emit("enter");
          }
        }
      }, [t._t("default")], 2)], 1);
    },
    staticRenderFns: []
  }, undefined, {
    name: "HandlerWrapper",
    components: {
      DraggableElement: O
    },
    props: {
      horizontalPosition: {
        type: String
      },
      verticalPosition: {
        type: String
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    computed: {
      classes: function classes() {
        var t;
        if (this.horizontalPosition || this.verticalPosition) {
          var e,
            i = p(this.horizontalPosition, this.verticalPosition);
          t = $((n(e = {}, i.classname, !0), n(e, "disabled", this.disabled), e));
        } else t = $({
          disabled: this.disabled
        });
        return {
          root: t,
          draggable: $("draggable")
        };
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  j = d("vue-line-wrapper"),
  P = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("DraggableElement", {
        "class": t.classname,
        on: {
          drag: function drag(e) {
            return t.$emit("drag", e);
          },
          "drag-end": function dragEnd(e) {
            return t.$emit("drag-end");
          },
          leave: function leave(e) {
            return t.$emit("leave");
          },
          enter: function enter(e) {
            return t.$emit("enter");
          }
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "LineWrapper",
    components: {
      DraggableElement: O
    },
    props: {
      position: {
        type: String,
        required: !0
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    computed: {
      classname: function classname() {
        var t;
        return j((n(t = {}, this.position, !0), n(t, "disabled", this.disabled), t));
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  D = ["left", "right", "top", "bottom"],
  L = ["left", "right"],
  I = ["top", "bottom"],
  B = ["left", "top"],
  _ = ["fill-area", "fit-area", "stencil", "none"],
  F = {
    left: 0,
    top: 0,
    width: 0,
    height: 0
  };
function X(t, e, i) {
  return !(i = i || ["width", "height", "left", "top"]).some(function (i) {
    return t[i] !== e[i];
  });
}
function Y(t) {
  return {
    left: t.left,
    top: t.top,
    right: t.left + t.width,
    bottom: t.top + t.height
  };
}
function k(t, e) {
  return {
    left: t.left - e.left,
    top: t.top - e.top
  };
}
function U(t) {
  return {
    left: t.left + t.width / 2,
    top: t.top + t.height / 2
  };
}
function N(t, e) {
  var i = {
    left: 0,
    top: 0,
    right: 0,
    bottom: 0
  };
  return D.forEach(function (n) {
    var s = e[n],
      o = Y(t)[n];
    i[n] = void 0 !== s && void 0 !== o ? "left" === n || "top" === n ? Math.max(0, s - o) : Math.max(0, o - s) : 0;
  }), i;
}
function V(t, e) {
  return {
    left: t.left - e.left,
    top: t.top - e.top,
    width: t.width + e.left + e.right,
    height: t.height + e.top + e.bottom
  };
}
function Z(t) {
  return {
    left: -t.left,
    top: -t.top
  };
}
function q(t, e) {
  return _f(_f({}, t), {
    left: t.left + e.left,
    top: t.top + e.top
  });
}
function G(t, e, i, n) {
  if (1 !== e) {
    if (i) {
      var s = U(t);
      return {
        width: t.width * e,
        height: t.height * e,
        left: t.left + t.width * (1 - e) / 2 + (i.left - s.left) * (n || 1 - e),
        top: t.top + t.height * (1 - e) / 2 + (i.top - s.top) * (n || 1 - e)
      };
    }
    return {
      width: t.width * e,
      height: t.height * e,
      left: t.left + t.width * (1 - e) / 2,
      top: t.top + t.height * (1 - e) / 2
    };
  }
  return t;
}
function Q(t) {
  return t.width / t.height;
}
function K(t, e) {
  return Math.min(void 0 !== e.right && void 0 !== e.left ? (e.right - e.left) / t.width : 1 / 0, void 0 !== e.bottom && void 0 !== e.top ? (e.bottom - e.top) / t.height : 1 / 0);
}
function J(t, e) {
  var i = {
      left: 0,
      top: 0
    },
    n = N(t, e);
  return n.left && n.left > 0 ? i.left = n.left : n.right && n.right > 0 && (i.left = -n.right), n.top && n.top > 0 ? i.top = n.top : n.bottom && n.bottom > 0 && (i.top = -n.bottom), i;
}
function tt(t, e) {
  var i;
  return e.minimum && t < e.minimum ? i = e.minimum : e.maximum && t > e.maximum && (i = e.maximum), i;
}
function et(t, e) {
  var i = Q(t),
    n = Q(e);
  return e.width < 1 / 0 && e.height < 1 / 0 ? i > n ? {
    width: e.width,
    height: e.width / i
  } : {
    width: e.height * i,
    height: e.height
  } : e.width < 1 / 0 ? {
    width: e.width,
    height: e.width / i
  } : e.height < 1 / 0 ? {
    width: e.height * i,
    height: e.height
  } : t;
}
function it(t, e) {
  var i = e * Math.PI / 180;
  return {
    width: Math.abs(t.width * Math.cos(i)) + Math.abs(t.height * Math.sin(i)),
    height: Math.abs(t.width * Math.sin(i)) + Math.abs(t.height * Math.cos(i))
  };
}
function nt(t, e) {
  var i = e * Math.PI / 180;
  return {
    left: t.left * Math.cos(i) - t.top * Math.sin(i),
    top: t.left * Math.sin(i) + t.top * Math.cos(i)
  };
}
function st(t, e) {
  var i = N(ot(t, e), e);
  return i.left + i.right + i.top + i.bottom ? i.left + i.right > i.top + i.bottom ? Math.min((t.width + i.left + i.right) / t.width, K(t, e)) : Math.min((t.height + i.top + i.bottom) / t.height, K(t, e)) : 1;
}
function ot(t, e, i) {
  void 0 === i && (i = !1);
  var n = J(t, e);
  return q(t, i ? Z(n) : n);
}
function rt(t) {
  return {
    width: void 0 !== t.right && void 0 !== t.left ? t.right - t.left : 1 / 0,
    height: void 0 !== t.bottom && void 0 !== t.top ? t.bottom - t.top : 1 / 0
  };
}
function at(t, e) {
  return _f(_f({}, t), {
    minWidth: Math.min(e.width, t.minWidth),
    minHeight: Math.min(e.height, t.minHeight),
    maxWidth: Math.min(e.width, t.maxWidth),
    maxHeight: Math.min(e.height, t.maxHeight)
  });
}
function ht(t, e, i) {
  void 0 === i && (i = !0);
  var n = {};
  return D.forEach(function (s) {
    var o = t[s],
      r = e[s];
    void 0 !== o && void 0 !== r ? n[s] = "left" === s || "top" === s ? i ? Math.max(o, r) : Math.min(o, r) : i ? Math.min(o, r) : Math.max(o, r) : void 0 !== r ? n[s] = r : void 0 !== o && (n[s] = o);
  }), n;
}
function ct(t, e) {
  return ht(t, e, !0);
}
function lt(t) {
  var e = t.size,
    i = t.aspectRatio,
    n = t.ignoreMinimum,
    s = t.sizeRestrictions;
  return Boolean((e.correctRatio || Q(e) >= i.minimum && Q(e) <= i.maximum) && e.height <= s.maxHeight && e.width <= s.maxWidth && e.width && e.height && (n || e.height >= s.minHeight && e.width >= s.minWidth));
}
function dt(t, e) {
  return Math.pow(t.width - e.width, 2) + Math.pow(t.height - e.height, 2);
}
function ut(t) {
  var e = t.width,
    i = t.height,
    n = t.sizeRestrictions,
    s = {
      minimum: t.aspectRatio && t.aspectRatio.minimum || 0,
      maximum: t.aspectRatio && t.aspectRatio.maximum || 1 / 0
    },
    o = {
      width: Math.max(n.minWidth, Math.min(n.maxWidth, e)),
      height: Math.max(n.minHeight, Math.min(n.maxHeight, i))
    };
  function r(t, o) {
    return void 0 === o && (o = !1), t.reduce(function (t, r) {
      return lt({
        size: r,
        aspectRatio: s,
        sizeRestrictions: n,
        ignoreMinimum: o
      }) && (!t || dt(r, {
        width: e,
        height: i
      }) < dt(t, {
        width: e,
        height: i
      })) ? r : t;
    }, null);
  }
  var a = [];
  s && [s.minimum, s.maximum].forEach(function (t) {
    t && a.push({
      width: o.width,
      height: o.width / t,
      correctRatio: !0
    }, {
      width: o.height * t,
      height: o.height,
      correctRatio: !0
    });
  }), lt({
    size: o,
    aspectRatio: s,
    sizeRestrictions: n
  }) && a.push(o);
  var h = r(a) || r(a, !0);
  return h && {
    width: h.width,
    height: h.height
  };
}
function mt(t) {
  var e = t.event,
    i = t.coordinates,
    n = t.positionRestrictions,
    s = void 0 === n ? {} : n,
    o = q(i, e.directions);
  return q(o, J(o, s));
}
function ft(t) {
  var e = t.coordinates,
    i = t.transform,
    n = t.imageSize,
    s = t.sizeRestrictions,
    o = t.positionRestrictions,
    r = t.aspectRatio,
    a = t.visibleArea,
    h = function h(t, e) {
      return mt({
        coordinates: t,
        positionRestrictions: o,
        event: new E({
          left: e.left - t.left,
          top: e.top - t.top
        })
      });
    },
    c = _f({}, e);
  return (Array.isArray(i) ? i : [i]).forEach(function (t) {
    var e = {};
    w((e = "function" == typeof t ? t({
      coordinates: c,
      imageSize: n,
      visibleArea: a
    }) : t).width) && w(e.height) || (c = function (t, e) {
      var i = _f(_f(_f({}, t), ut({
        width: e.width,
        height: e.height,
        sizeRestrictions: s,
        aspectRatio: r
      })), {
        left: 0,
        top: 0
      });
      return h(i, {
        left: t.left,
        top: t.top
      });
    }(c, _f(_f({}, c), e))), w(e.left) && w(e.top) || (c = h(c, _f(_f({}, c), e)));
  }), c;
}
function pt(t) {
  t.event;
  var e = t.getAreaRestrictions,
    i = t.boundaries,
    n = t.coordinates,
    s = t.visibleArea;
  t.aspectRatio;
  var o = t.stencilSize,
    r = t.sizeRestrictions,
    a = t.positionRestrictions;
  t.stencilReference;
  var h,
    c,
    l,
    d = _f({}, n),
    u = _f({}, s),
    m = _f({}, o);
  h = Q(m), c = Q(d), void 0 === l && (l = .001), (0 === h || 0 === c ? Math.abs(c - h) < l : Math.abs(c / h) < 1 + l && Math.abs(c / h) > 1 - l) || (d = _f(_f({}, d), ut({
    sizeRestrictions: r,
    width: d.width,
    height: d.height,
    aspectRatio: {
      minimum: Q(m),
      maximum: Q(m)
    }
  })));
  var p = st(u = G(u, d.width * i.width / (u.width * m.width)), e({
    visibleArea: u,
    type: "resize"
  }));
  return 1 !== p && (u = G(u, p), d = G(d, p)), u = ot(u = q(u, k(U(d), U(u))), e({
    visibleArea: u,
    type: "move"
  })), {
    coordinates: d = ot(d, ct(Y(u), a)),
    visibleArea: u
  };
}
function gt(t) {
  var e = t.event,
    i = t.getAreaRestrictions,
    n = t.boundaries,
    s = t.coordinates,
    o = t.visibleArea;
  t.aspectRatio, t.stencilSize, t.sizeRestrictions;
  var r = t.positionRestrictions;
  t.stencilReference;
  var a = _f({}, s),
    h = _f({}, o);
  if (s && o && "manipulateImage" !== e.type) {
    var c = {
      width: 0,
      height: 0
    };
    h.width, n.width, Q(n) > Q(a) ? (c.height = .8 * n.height, c.width = c.height * Q(a)) : (c.width = .8 * n.width, c.height = c.width * Q(a));
    var l = st(h = G(h, a.width * n.width / (h.width * c.width)), i({
      visibleArea: h,
      type: "resize"
    }));
    h = G(h, l), 1 !== l && (c.height /= l, c.width /= l), h = ot(h = q(h, k(U(a), U(h))), i({
      visibleArea: h,
      type: "move"
    })), a = ot(a, ct(Y(h), r));
  }
  return {
    coordinates: a,
    visibleArea: h
  };
}
function vt(t) {
  var e = t.event,
    i = t.coordinates,
    n = t.visibleArea,
    s = t.getAreaRestrictions,
    o = _f({}, n),
    r = _f({}, i);
  if ("setCoordinates" === e.type) {
    var a = Math.max(0, r.width - o.width),
      h = Math.max(0, r.height - o.height);
    a > h ? o = G(o, Math.min(r.width / o.width, K(o, s({
      visibleArea: o,
      type: "resize"
    })))) : h > a && (o = G(o, Math.min(r.height / o.height, K(o, s({
      visibleArea: o,
      type: "resize"
    }))))), o = ot(o = q(o, Z(J(r, Y(o)))), s({
      visibleArea: o,
      type: "move"
    }));
  }
  return {
    visibleArea: o,
    coordinates: r
  };
}
function bt(t) {
  var e = t.imageSize,
    i = t.visibleArea,
    n = t.aspectRatio,
    s = t.sizeRestrictions,
    o = i || e,
    r = Math.min(n.maximum || 1 / 0, Math.max(n.minimum || 0, Q(o))),
    a = o.width < o.height ? {
      width: .8 * o.width,
      height: .8 * o.width / r
    } : {
      height: .8 * o.height,
      width: .8 * o.height * r
    };
  return ut(_f(_f({}, a), {
    aspectRatio: n,
    sizeRestrictions: s
  }));
}
function wt(t) {
  var e,
    i,
    n = t.imageSize,
    s = t.visibleArea,
    o = t.boundaries,
    r = t.aspectRatio,
    a = t.sizeRestrictions,
    h = t.stencilSize,
    c = s || n;
  return Q(c) > Q(o) ? i = (e = h.height * c.height / o.height) * Q(h) : e = (i = h.width * c.width / o.width) / Q(h), ut({
    width: i,
    height: e,
    aspectRatio: r,
    sizeRestrictions: a
  });
}
function yt(t, e) {
  return ht(t, Y(e));
}
function zt(t) {
  var e = t.event,
    i = t.coordinates,
    n = t.visibleArea,
    s = t.sizeRestrictions,
    o = t.getAreaRestrictions,
    r = t.positionRestrictions,
    a = t.adjustStencil,
    h = e.scale,
    c = e.move,
    l = _f({}, n),
    d = _f({}, i),
    u = 1,
    m = 1,
    p = h.factor && Math.abs(h.factor - 1) > .001;
  l = q(l, {
    left: c.left || 0,
    top: c.top || 0
  });
  var g = {
    stencil: {
      minimum: Math.max(s.minWidth ? s.minWidth / d.width : 0, s.minHeight ? s.minHeight / d.height : 0),
      maximum: Math.min(s.maxWidth ? s.maxWidth / d.width : 1 / 0, s.maxHeight ? s.maxHeight / d.height : 1 / 0, K(d, r))
    },
    area: {
      maximum: K(l, o({
        visibleArea: l,
        type: "resize"
      }))
    }
  };
  h.factor && p && (h.factor < 1 ? (m = Math.max(h.factor, g.stencil.minimum)) > 1 && (m = 1) : h.factor > 1 && (m = Math.min(h.factor, Math.min(g.area.maximum, g.stencil.maximum))) < 1 && (m = 1)), m && (l = G(l, m, h.center));
  var v = i.left - n.left,
    b = n.width + n.left - (i.width + i.left),
    w = i.top - n.top,
    y = n.height + n.top - (i.height + i.top);
  return l = ot(l = q(l, J(l, {
    left: void 0 !== r.left ? r.left - v * m : void 0,
    top: void 0 !== r.top ? r.top - w * m : void 0,
    bottom: void 0 !== r.bottom ? r.bottom + y * m : void 0,
    right: void 0 !== r.right ? r.right + b * m : void 0
  })), o({
    visibleArea: l,
    type: "move"
  })), d.width = d.width * m, d.height = d.height * m, d.left = l.left + v * m, d.top = l.top + w * m, d = ot(d, ct(Y(l), r)), h.factor && p && a && (h.factor > 1 ? u = Math.min(g.area.maximum, h.factor) / m : h.factor < 1 && (u = Math.max(d.height / l.height, d.width / l.width, h.factor / m)), 1 !== u && (l = q(l = ot(l = G(l, u, h.factor > 1 ? h.center : U(d)), o({
    visibleArea: l,
    type: "move"
  })), Z(J(d, Y(l)))))), {
    coordinates: d,
    visibleArea: l
  };
}
function Rt(t) {
  var e = t.aspectRatio,
    i = t.getAreaRestrictions,
    n = t.coordinates,
    s = t.visibleArea,
    o = t.sizeRestrictions,
    r = t.positionRestrictions,
    a = t.imageSize,
    h = t.previousImageSize,
    c = t.angle,
    l = _f({}, n),
    d = _f({}, s),
    u = nt(U(_f({
      left: 0,
      top: 0
    }, h)), c);
  return (l = _f(_f({}, ut({
    sizeRestrictions: o,
    aspectRatio: e,
    width: l.width,
    height: l.height
  })), nt(U(l), c))).left -= u.left - a.width / 2 + l.width / 2, l.top -= u.top - a.height / 2 + l.height / 2, d = G(d, st(d, i({
    visibleArea: d,
    type: "resize"
  }))), {
    coordinates: l = ot(l, r),
    visibleArea: d = ot(d = q(d, k(U(l), U(n))), i({
      visibleArea: d,
      type: "move"
    }))
  };
}
function At(t) {
  var e = t.flip,
    i = t.previousFlip,
    n = t.rotate;
  t.aspectRatio;
  var s = t.getAreaRestrictions,
    o = t.coordinates,
    r = t.visibleArea,
    a = t.imageSize,
    h = _f({}, o),
    c = _f({}, r),
    l = i.horizontal !== e.horizontal,
    d = i.vertical !== e.vertical;
  if (l || d) {
    var u = nt({
        left: a.width / 2,
        top: a.height / 2
      }, -n),
      m = nt(U(h), -n),
      p = nt({
        left: l ? u.left - (m.left - u.left) : m.left,
        top: d ? u.top - (m.top - u.top) : m.top
      }, n);
    h = q(h, k(p, U(h))), m = nt(U(c), -n), c = ot(c = q(c, k(p = nt({
      left: l ? u.left - (m.left - u.left) : m.left,
      top: d ? u.top - (m.top - u.top) : m.top
    }, n), U(c))), s({
      visibleArea: c,
      type: "move"
    }));
  }
  return {
    coordinates: h,
    visibleArea: c
  };
}
function St(t) {
  var e = t.directions,
    i = t.coordinates,
    n = t.positionRestrictions,
    s = void 0 === n ? {} : n,
    o = t.sizeRestrictions,
    r = t.preserveRatio,
    a = t.compensate,
    h = _f({}, e),
    c = V(i, h).width,
    l = V(i, h).height;
  c < 0 && (h.left < 0 && h.right < 0 ? (h.left = -(i.width - o.minWidth) / (h.left / h.right), h.right = -(i.width - o.minWidth) / (h.right / h.left)) : h.left < 0 ? h.left = -(i.width - o.minWidth) : h.right < 0 && (h.right = -(i.width - o.minWidth))), l < 0 && (h.top < 0 && h.bottom < 0 ? (h.top = -(i.height - o.minHeight) / (h.top / h.bottom), h.bottom = -(i.height - o.minHeight) / (h.bottom / h.top)) : h.top < 0 ? h.top = -(i.height - o.minHeight) : h.bottom < 0 && (h.bottom = -(i.height - o.minHeight)));
  var d = N(V(i, h), s);
  a && (d.left && d.left > 0 && 0 === d.right ? (h.right += d.left, h.left -= d.left) : d.right && d.right > 0 && 0 === d.left && (h.left += d.right, h.right -= d.right), d.top && d.top > 0 && 0 === d.bottom ? (h.bottom += d.top, h.top -= d.top) : d.bottom && d.bottom > 0 && 0 === d.top && (h.top += d.bottom, h.bottom -= d.bottom), d = N(V(i, h), s));
  var u = {
    width: 1 / 0,
    height: 1 / 0,
    left: 1 / 0,
    right: 1 / 0,
    top: 1 / 0,
    bottom: 1 / 0
  };
  if (D.forEach(function (t) {
    var e = d[t];
    e && h[t] && (u[t] = Math.max(0, 1 - e / h[t]));
  }), r) {
    var m = Math.min.apply(null, D.map(function (t) {
      return u[t];
    }));
    m !== 1 / 0 && D.forEach(function (t) {
      h[t] *= m;
    });
  } else D.forEach(function (t) {
    u[t] !== 1 / 0 && (h[t] *= u[t]);
  });
  if (c = V(i, h).width, l = V(i, h).height, h.right + h.left && (c > o.maxWidth ? u.width = (o.maxWidth - i.width) / (h.right + h.left) : c < o.minWidth && (u.width = (o.minWidth - i.width) / (h.right + h.left))), h.bottom + h.top && (l > o.maxHeight ? u.height = (o.maxHeight - i.height) / (h.bottom + h.top) : l < o.minHeight && (u.height = (o.minHeight - i.height) / (h.bottom + h.top))), r) {
    var p = Math.min(u.width, u.height);
    p !== 1 / 0 && D.forEach(function (t) {
      h[t] *= p;
    });
  } else u.width !== 1 / 0 && L.forEach(function (t) {
    h[t] *= u.width;
  }), u.height !== 1 / 0 && I.forEach(function (t) {
    h[t] *= u.height;
  });
  return h;
}
function xt(t, e, i) {
  return 0 == e && 0 == i ? t / 2 : 0 == e ? 0 : 0 == i ? t : t * Math.abs(e / (e + i));
}
var Mt = d("vue-simple-handler"),
  Ct = d("vue-simple-handler-wrapper"),
  Et = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("HandlerWrapper", {
        "class": t.classes.wrapper,
        attrs: {
          "vertical-position": t.verticalPosition,
          "horizontal-position": t.horizontalPosition,
          disabled: t.disabled
        },
        on: {
          drag: t.onDrag,
          "drag-end": t.onDragEnd,
          enter: t.onEnter,
          leave: t.onLeave
        }
      }, [i("div", {
        "class": t.classes["default"]
      })]);
    },
    staticRenderFns: []
  }, undefined, {
    name: "SimpleHandler",
    components: {
      HandlerWrapper: H
    },
    props: {
      defaultClass: {
        type: String
      },
      hoverClass: {
        type: String
      },
      wrapperClass: {
        type: String
      },
      horizontalPosition: {
        type: String
      },
      verticalPosition: {
        type: String
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    data: function data() {
      return {
        hover: !1
      };
    },
    computed: {
      classes: function classes() {
        var t,
          e = (n(t = {}, this.horizontalPosition, Boolean(this.horizontalPosition)), n(t, this.verticalPosition, Boolean(this.verticalPosition)), n(t, "".concat(this.horizontalPosition, "-").concat(this.verticalPosition), Boolean(this.verticalPosition && this.horizontalPosition)), n(t, "hover", this.hover), t);
        return {
          "default": l(Mt(e), this.defaultClass, this.hover && this.hoverClass),
          wrapper: l(Ct(e), this.wrapperClass)
        };
      }
    },
    methods: {
      onDrag: function onDrag(t) {
        this.$emit("drag", t);
      },
      onEnter: function onEnter() {
        this.hover = !0;
      },
      onLeave: function onLeave() {
        this.hover = !1;
      },
      onDragEnd: function onDragEnd() {
        this.$emit("drag-end");
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Wt = d("vue-simple-line"),
  Tt = d("vue-simple-line-wrapper"),
  Ot = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("LineWrapper", {
        "class": t.classes.wrapper,
        attrs: {
          position: t.position,
          disabled: t.disabled
        },
        on: {
          drag: t.onDrag,
          "drag-end": t.onDragEnd,
          enter: t.onEnter,
          leave: t.onLeave
        }
      }, [i("div", {
        "class": t.classes.root
      })]);
    },
    staticRenderFns: []
  }, undefined, {
    name: "SimpleLine",
    components: {
      LineWrapper: P
    },
    props: {
      defaultClass: {
        type: String
      },
      hoverClass: {
        type: String
      },
      wrapperClass: {
        type: String
      },
      position: {
        type: String
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    data: function data() {
      return {
        hover: !1
      };
    },
    computed: {
      classes: function classes() {
        return {
          root: l(Wt(n({}, this.position, !0)), this.defaultClass, this.hover && this.hoverClass),
          wrapper: l(Tt(n({}, this.position, !0)), this.wrapperClass)
        };
      }
    },
    methods: {
      onDrag: function onDrag(t) {
        this.$emit("drag", t);
      },
      onEnter: function onEnter() {
        this.hover = !0;
      },
      onLeave: function onLeave() {
        this.hover = !1;
      },
      onDragEnd: function onDragEnd() {
        this.$emit("drag-end");
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  $t = d("vue-bounding-box"),
  Ht = ["east", "west", null],
  jt = ["south", "north", null],
  Pt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        ref: "box",
        "class": t.classes.root,
        style: t.style
      }, [t._t("default"), t._v(" "), i("div", t._l(t.lineNodes, function (e) {
        return i(e.component, {
          key: e.name,
          tag: "component",
          attrs: {
            "default-class": e["class"],
            "hover-class": e.hoverClass,
            "wrapper-class": e.wrapperClass,
            position: e.name,
            disabled: e.disabled
          },
          on: {
            drag: function drag(i) {
              return t.onHandlerDrag(i, e.horizontalDirection, e.verticalDirection);
            },
            "drag-end": function dragEnd(e) {
              return t.onEnd();
            }
          }
        });
      }), 1), t._v(" "), t._l(t.handlerNodes, function (e) {
        return i("div", {
          key: e.name,
          "class": e.wrapperClass,
          style: e.wrapperStyle
        }, [i(e.component, {
          tag: "component",
          attrs: {
            "default-class": e["class"],
            "hover-class": e.hoverClass,
            "wrapper-class": e.wrapperClass,
            "horizontal-position": e.horizontalDirection,
            "vertical-position": e.verticalDirection,
            disabled: e.disabled
          },
          on: {
            drag: function drag(i) {
              return t.onHandlerDrag(i, e.horizontalDirection, e.verticalDirection);
            },
            "drag-end": function dragEnd(e) {
              return t.onEnd();
            }
          }
        })], 1);
      })], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "BoundingBox",
    props: {
      width: {
        type: Number
      },
      height: {
        type: Number
      },
      transitions: {
        type: Object
      },
      handlers: {
        type: Object,
        "default": function _default() {
          return {
            eastNorth: !0,
            north: !0,
            westNorth: !0,
            west: !0,
            westSouth: !0,
            south: !0,
            eastSouth: !0,
            east: !0
          };
        }
      },
      handlersComponent: {
        type: [Object, String],
        "default": function _default() {
          return Et;
        }
      },
      handlersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      lines: {
        type: Object,
        "default": function _default() {
          return {
            west: !0,
            north: !0,
            east: !0,
            south: !0
          };
        }
      },
      linesComponent: {
        type: [Object, String],
        "default": function _default() {
          return Ot;
        }
      },
      linesClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      linesWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      resizable: {
        type: Boolean,
        "default": !0
      }
    },
    data: function data() {
      var t = [];
      return Ht.forEach(function (e) {
        jt.forEach(function (i) {
          if (e !== i) {
            var n = p(e, i),
              s = n.name,
              o = n.classname;
            t.push({
              name: s,
              classname: o,
              verticalDirection: i,
              horizontalDirection: e
            });
          }
        });
      }), {
        points: t
      };
    },
    computed: {
      style: function style() {
        var t = {};
        return this.width && this.height && (t.width = "".concat(this.width, "px"), t.height = "".concat(this.height, "px"), this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction))), t;
      },
      classes: function classes() {
        var t = this.handlersClasses,
          e = this.handlersWrappersClasses,
          i = this.linesClasses,
          n = this.linesWrappersClasses;
        return {
          root: $t(),
          handlers: t,
          handlersWrappers: e,
          lines: i,
          linesWrappers: n
        };
      },
      lineNodes: function lineNodes() {
        var t = this,
          e = [];
        return this.points.forEach(function (i) {
          i.horizontalDirection && i.verticalDirection || !t.lines[i.name] || e.push({
            name: i.name,
            component: t.linesComponent,
            "class": l(t.classes.lines["default"], t.classes.lines[i.name], !t.resizable && t.classes.lines.disabled),
            wrapperClass: l(t.classes.linesWrappers["default"], t.classes.linesWrappers[i.name], !t.resizable && t.classes.linesWrappers.disabled),
            hoverClass: t.classes.lines.hover,
            verticalDirection: i.verticalDirection,
            horizontalDirection: i.horizontalDirection,
            disabled: !t.resizable
          });
        }), e;
      },
      handlerNodes: function handlerNodes() {
        var t = this,
          e = [],
          i = this.width,
          s = this.height;
        return this.points.forEach(function (o) {
          if (t.handlers[o.name]) {
            var r = {
              name: o.name,
              component: t.handlersComponent,
              "class": l(t.classes.handlers["default"], t.classes.handlers[o.name]),
              wrapperClass: l(t.classes.handlersWrappers["default"], t.classes.handlersWrappers[o.name]),
              hoverClass: t.classes.handlers.hover,
              verticalDirection: o.verticalDirection,
              horizontalDirection: o.horizontalDirection,
              disabled: !t.resizable
            };
            if (i && s) {
              var a = o.horizontalDirection,
                h = o.verticalDirection,
                c = "east" === a ? i : "west" === a ? 0 : i / 2,
                d = "south" === h ? s : "north" === h ? 0 : s / 2;
              r.wrapperClass = $t("handler"), r.wrapperStyle = {
                transform: "translate(".concat(c, "px, ").concat(d, "px)")
              }, t.transitions && t.transitions.enabled && (r.wrapperStyle.transition = "".concat(t.transitions.time, "ms ").concat(t.transitions.timingFunction));
            } else r.wrapperClass = $t("handler", n({}, o.classname, !0));
            e.push(r);
          }
        }), e;
      }
    },
    beforeMount: function beforeMount() {
      window.addEventListener("mouseup", this.onMouseUp, {
        passive: !1
      }), window.addEventListener("mousemove", this.onMouseMove, {
        passive: !1
      }), window.addEventListener("touchmove", this.onTouchMove, {
        passive: !1
      }), window.addEventListener("touchend", this.onTouchEnd, {
        passive: !1
      });
    },
    beforeDestroy: function beforeDestroy() {
      window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
    },
    mounted: function mounted() {
      this.touches = [];
    },
    methods: {
      onEnd: function onEnd() {
        this.$emit("resize-end");
      },
      onHandlerDrag: function onHandlerDrag(t, e, i) {
        var n,
          s = t.shift(),
          o = s.left,
          r = s.top,
          a = {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0
          };
        "west" === e ? a.left -= o : "east" === e && (a.right += o), "north" === i ? a.top -= r : "south" === i && (a.bottom += r), !i && e ? n = "width" : i && !e && (n = "height"), this.resizable && this.$emit("resize", new C(a, {
          allowedDirections: {
            left: "west" === e || !e,
            right: "east" === e || !e,
            bottom: "south" === i || !i,
            top: "north" === i || !i
          },
          preserveAspectRatio: t.nativeEvent && t.nativeEvent.shiftKey,
          respectDirection: n
        }));
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Dt = d("vue-draggable-area"),
  Lt = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("div", {
        ref: "container",
        on: {
          touchstart: t.onTouchStart,
          mousedown: t.onMouseDown
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "DraggableArea",
    props: {
      movable: {
        type: Boolean,
        "default": !0
      },
      activationDistance: {
        type: Number,
        "default": 20
      }
    },
    computed: {
      classnames: function classnames() {
        return {
          "default": Dt()
        };
      }
    },
    beforeMount: function beforeMount() {
      window.addEventListener("mouseup", this.onMouseUp, {
        passive: !1
      }), window.addEventListener("mousemove", this.onMouseMove, {
        passive: !1
      }), window.addEventListener("touchmove", this.onTouchMove, {
        passive: !1
      }), window.addEventListener("touchend", this.onTouchEnd, {
        passive: !1
      });
    },
    beforeDestroy: function beforeDestroy() {
      window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
    },
    mounted: function mounted() {
      this.touches = [], this.touchStarted = !1;
    },
    methods: {
      onTouchStart: function onTouchStart(t) {
        if (t.cancelable) {
          var e = this.movable && 1 === t.touches.length;
          e && (this.touches = o(t.touches)), (this.touchStarted || e) && (t.preventDefault(), t.stopPropagation());
        }
      },
      onTouchEnd: function onTouchEnd() {
        this.touchStarted = !1, this.processEnd();
      },
      onTouchMove: function onTouchMove(t) {
        this.touches.length >= 1 && (this.touchStarted ? (this.processMove(t, t.touches), t.preventDefault(), t.stopPropagation()) : x({
          x: this.touches[0].clientX,
          y: this.touches[0].clientY
        }, {
          x: t.touches[0].clientX,
          y: t.touches[0].clientY
        }) > this.activationDistance && (this.initAnchor({
          clientX: t.touches[0].clientX,
          clientY: t.touches[0].clientY
        }), this.touchStarted = !0));
      },
      onMouseDown: function onMouseDown(t) {
        if (this.movable && 0 === t.button) {
          var e = {
            fake: !0,
            clientX: t.clientX,
            clientY: t.clientY
          };
          this.touches = [e], this.initAnchor(e), t.stopPropagation();
        }
      },
      onMouseMove: function onMouseMove(t) {
        this.touches.length && (this.processMove(t, [{
          fake: !0,
          clientX: t.clientX,
          clientY: t.clientY
        }]), t.preventDefault && t.cancelable && t.preventDefault(), t.stopPropagation());
      },
      onMouseUp: function onMouseUp() {
        this.processEnd();
      },
      initAnchor: function initAnchor(t) {
        var e = this.$refs.container.getBoundingClientRect(),
          i = e.left,
          n = e.top;
        this.anchor = {
          x: t.clientX - i,
          y: t.clientY - n
        };
      },
      processMove: function processMove(t, e) {
        var i = o(e);
        if (this.touches.length) {
          var n = this.$refs.container.getBoundingClientRect(),
            s = n.left,
            r = n.top;
          1 === this.touches.length && 1 === i.length && this.$emit("move", new E({
            left: i[0].clientX - (s + this.anchor.x),
            top: i[0].clientY - (r + this.anchor.y)
          }));
        }
      },
      processEnd: function processEnd() {
        this.touches.length && this.$emit("move-end"), this.touches = [];
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0);
function It(t) {
  var e, i;
  return {
    rotate: t.rotate || 0,
    flip: {
      horizontal: (null === (e = null == t ? void 0 : t.flip) || void 0 === e ? void 0 : e.horizontal) || !1,
      vertical: (null === (i = null == t ? void 0 : t.flip) || void 0 === i ? void 0 : i.vertical) || !1
    }
  };
}
function Bt(t) {
  return new Promise(function (e, i) {
    try {
      if (t) {
        if (/^data:/i.test(t)) e(function (t) {
          t = t.replace(/^data:([^;]+);base64,/gim, "");
          for (var e = atob(t), i = e.length, n = new ArrayBuffer(i), s = new Uint8Array(n), o = 0; o < i; o++) s[o] = e.charCodeAt(o);
          return n;
        }(t));else if (/^blob:/i.test(t)) {
          var n = new FileReader();
          n.onload = function (t) {
            e(t.target.result);
          }, o = t, r = function r(t) {
            n.readAsArrayBuffer(t);
          }, (a = new XMLHttpRequest()).open("GET", o, !0), a.responseType = "blob", a.onload = function () {
            200 != this.status && 0 !== this.status || r(this.response);
          }, a.send();
        } else {
          var s = new XMLHttpRequest();
          s.onreadystatechange = function () {
            4 === s.readyState && (200 === s.status || 0 === s.status ? e(s.response) : i("Warning: could not load an image to parse its orientation"), s = null);
          }, s.onprogress = function () {
            "image/jpeg" !== s.getResponseHeader("content-type") && s.abort();
          }, s.withCredentials = !1, s.open("GET", t, !0), s.responseType = "arraybuffer", s.send(null);
        }
      } else i("Error: the image is empty");
    } catch (t) {
      i(t);
    }
    var o, r, a;
  });
}
function _t(t) {
  var e = t.rotate,
    i = t.flip,
    n = t.scaleX,
    s = t.scaleY,
    o = "";
  return o += " rotate(" + e + "deg) ", o += " scaleX(" + n * (i.horizontal ? -1 : 1) + ") ", o += " scaleY(" + s * (i.vertical ? -1 : 1) + ") ";
}
function Ft(t) {
  try {
    var e,
      i = new DataView(t),
      n = void 0,
      s = void 0,
      o = void 0,
      r = void 0;
    if (255 === i.getUint8(0) && 216 === i.getUint8(1)) for (var a = i.byteLength, h = 2; h + 1 < a;) {
      if (255 === i.getUint8(h) && 225 === i.getUint8(h + 1)) {
        o = h;
        break;
      }
      h++;
    }
    if (o && (n = o + 10, "Exif" === function (t, e, i) {
      var n,
        s = "";
      for (n = e, i += e; n < i; n++) s += String.fromCharCode(t.getUint8(n));
      return s;
    }(i, o + 4, 4))) {
      var c = i.getUint16(n);
      if (((s = 18761 === c) || 19789 === c) && 42 === i.getUint16(n + 2, s)) {
        var l = i.getUint32(n + 4, s);
        l >= 8 && (r = n + l);
      }
    }
    if (r) for (var d = i.getUint16(r, s), u = 0; u < d; u++) {
      h = r + 12 * u + 2;
      if (274 === i.getUint16(h, s)) {
        h += 8, e = i.getUint16(h, s), i.setUint16(h, 1, s);
        break;
      }
    }
    return e;
  } catch (t) {
    return null;
  }
}
var Xt = d("vue-preview-result"),
  Yt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.root
      }, [i("div", {
        ref: "wrapper",
        "class": t.classes.wrapper,
        style: t.wrapperStyle
      }, [i("img", {
        ref: "image",
        "class": t.classes.image,
        style: t.imageStyle,
        attrs: {
          src: t.image.src
        }
      })])]);
    },
    staticRenderFns: []
  }, undefined, {
    name: "PreviewResult",
    props: {
      image: {
        type: Object
      },
      transitions: {
        type: Object
      },
      stencilCoordinates: {
        type: Object,
        "default": function _default() {
          return {
            width: 0,
            height: 0,
            left: 0,
            top: 0
          };
        }
      },
      imageClass: {
        type: String
      }
    },
    computed: {
      classes: function classes() {
        return {
          root: Xt(),
          wrapper: Xt("wrapper"),
          imageWrapper: Xt("image-wrapper"),
          image: l(Xt("image"), this.imageClass)
        };
      },
      wrapperStyle: function wrapperStyle() {
        var t = {
          width: "".concat(this.stencilCoordinates.width, "px"),
          height: "".concat(this.stencilCoordinates.height, "px"),
          left: "calc(50% - ".concat(this.stencilCoordinates.width / 2, "px)"),
          top: "calc(50% - ".concat(this.stencilCoordinates.height / 2, "px)")
        };
        return this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), t;
      },
      imageStyle: function imageStyle() {
        var t = this.image.transforms,
          e = it({
            width: this.image.width,
            height: this.image.height
          }, t.rotate),
          i = {
            width: "".concat(this.image.width, "px"),
            height: "".concat(this.image.height, "px"),
            left: "0px",
            top: "0px"
          },
          n = {
            left: (this.image.width - e.width) * t.scaleX / 2,
            top: (this.image.height - e.height) * t.scaleY / 2
          },
          s = {
            left: (1 - t.scaleX) * this.image.width / 2,
            top: (1 - t.scaleY) * this.image.height / 2
          };
        return i.transform = "translate(\n\t\t\t\t".concat(-this.stencilCoordinates.left - t.translateX - n.left - s.left, "px,").concat(-this.stencilCoordinates.top - t.translateY - n.top - s.top, "px) ") + _t(t), this.transitions && this.transitions.enabled && (i.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), i;
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0);
function kt(t, e) {
  var i = e.getBoundingClientRect(),
    n = i.left,
    s = i.top,
    o = {
      left: 0,
      top: 0
    },
    r = 0;
  return t.forEach(function (e) {
    o.left += (e.clientX - n) / t.length, o.top += (e.clientY - s) / t.length;
  }), t.forEach(function (t) {
    r += x({
      x: o.left,
      y: o.top
    }, {
      x: t.clientX - n,
      y: t.clientY - s
    });
  }), {
    centerMass: o,
    spread: r,
    count: t.length
  };
}
var Ut = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("div", {
        ref: "container",
        on: {
          touchstart: t.onTouchStart,
          mousedown: t.onMouseDown,
          wheel: t.onWheel
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    props: {
      touchMove: {
        type: Boolean,
        required: !0
      },
      mouseMove: {
        type: Boolean,
        required: !0
      },
      touchResize: {
        type: Boolean,
        required: !0
      },
      wheelResize: {
        type: [Boolean, Object],
        required: !0
      },
      eventsFilter: {
        type: Function,
        required: !1
      }
    },
    beforeMount: function beforeMount() {
      window.addEventListener("mouseup", this.onMouseUp, {
        passive: !1
      }), window.addEventListener("mousemove", this.onMouseMove, {
        passive: !1
      }), window.addEventListener("touchmove", this.onTouchMove, {
        passive: !1
      }), window.addEventListener("touchend", this.onTouchEnd, {
        passive: !1
      });
    },
    beforeDestroy: function beforeDestroy() {
      window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
    },
    created: function created() {
      this.transforming = !1, this.debouncedProcessEnd = m(this.processEnd), this.touches = [];
    },
    methods: {
      processMove: function processMove(t, e) {
        if (this.touches.length) {
          if (1 === this.touches.length && 1 === e.length) this.$emit("move", new M({
            left: this.touches[0].clientX - e[0].clientX,
            top: this.touches[0].clientY - e[0].clientY
          }));else if (this.touches.length > 1 && this.touchResize) {
            var i = kt(e, this.$refs.container),
              n = this.oldGeometricProperties;
            n.count === i.count && n.count > 1 && this.$emit("resize", new M({
              left: n.centerMass.left - i.centerMass.left,
              top: n.centerMass.top - i.centerMass.top
            }, {
              factor: n.spread / i.spread,
              center: i.centerMass
            })), this.oldGeometricProperties = i;
          }
          this.touches = e;
        }
      },
      processEnd: function processEnd() {
        this.transforming && (this.transforming = !1, this.$emit("transform-end"));
      },
      processStart: function processStart() {
        this.transforming = !0, this.debouncedProcessEnd.clear();
      },
      processEvent: function processEvent(t) {
        return this.eventsFilter ? !1 !== this.eventsFilter(t, this.transforming) : (t.preventDefault(), t.stopPropagation(), !0);
      },
      onTouchStart: function onTouchStart(t) {
        if (t.cancelable && (this.touchMove || this.touchResize && t.touches.length > 1) && this.processEvent(t)) {
          var e = this.$refs.container,
            i = e.getBoundingClientRect(),
            n = i.left,
            s = i.top,
            r = i.bottom,
            a = i.right;
          this.touches = o(t.touches).filter(function (t) {
            return t.clientX > n && t.clientX < a && t.clientY > s && t.clientY < r;
          }), this.oldGeometricProperties = kt(this.touches, e);
        }
      },
      onTouchEnd: function onTouchEnd(t) {
        0 === t.touches.length && (this.touches = [], this.processEnd());
      },
      onTouchMove: function onTouchMove(t) {
        var e = this;
        if (this.touches.length) {
          var i = o(t.touches).filter(function (t) {
            return !t.identifier || e.touches.find(function (e) {
              return e.identifier === t.identifier;
            });
          });
          this.processEvent(t) && (this.processMove(t, i), this.processStart());
        }
      },
      onMouseDown: function onMouseDown(t) {
        if (this.mouseMove && "buttons" in t && 1 === t.buttons && this.processEvent(t)) {
          var e = {
            fake: !0,
            clientX: t.clientX,
            clientY: t.clientY
          };
          this.touches = [e], this.processStart();
        }
      },
      onMouseMove: function onMouseMove(t) {
        this.touches.length && this.processEvent(t) && this.processMove(t, [{
          clientX: t.clientX,
          clientY: t.clientY
        }]);
      },
      onMouseUp: function onMouseUp() {
        this.touches = [], this.processEnd();
      },
      onWheel: function onWheel(t) {
        if (this.wheelResize && this.processEvent(t)) {
          var e = this.$refs.container.getBoundingClientRect(),
            i = e.left,
            n = e.top,
            s = 1 + this.wheelResize.ratio * (r = t.deltaY || t.detail || t.wheelDelta, 0 === (a = +r) || S(a) ? a : a > 0 ? 1 : -1),
            o = {
              left: t.clientX - i,
              top: t.clientY - n
            };
          this.$emit("resize", new M({}, {
            factor: s,
            center: o
          })), this.touches.length || this.debouncedProcessEnd();
        }
        var r, a;
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Nt = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("transformable-image", {
        attrs: {
          "touch-move": t.touchMove,
          "touch-resize": t.touchResize,
          "mouse-move": t.mouseMove,
          "wheel-resize": t.wheelResize
        },
        on: {
          move: function move(e) {
            return t.$emit("move", e);
          },
          resize: function resize(e) {
            return t.$emit("resize", e);
          }
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    components: {
      TransformableImage: Ut
    },
    props: {
      touchMove: {
        type: Boolean,
        required: !0
      },
      mouseMove: {
        type: Boolean,
        required: !0
      },
      touchResize: {
        type: Boolean,
        required: !0
      },
      wheelResize: {
        type: [Boolean, Object],
        required: !0
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Vt = d("vue-preview"),
  Zt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        ref: "root",
        "class": t.classes.root,
        style: t.style
      }, [i("div", {
        ref: "wrapper",
        "class": t.classes.wrapper,
        style: t.wrapperStyle
      }, [i("img", {
        directives: [{
          name: "show",
          rawName: "v-show",
          value: t.image && t.image.src,
          expression: "image && image.src"
        }],
        ref: "image",
        "class": t.classes.image,
        style: t.imageStyle,
        attrs: {
          src: t.image && t.image.src
        }
      })])]);
    },
    staticRenderFns: []
  }, undefined, {
    props: {
      coordinates: {
        type: Object
      },
      transitions: {
        type: Object
      },
      image: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      imageClass: {
        type: String
      },
      width: {
        type: Number
      },
      height: {
        type: Number
      },
      fill: {
        type: Boolean
      }
    },
    data: function data() {
      return {
        calculatedImageSize: {
          width: 0,
          height: 0
        },
        calculatedSize: {
          width: 0,
          height: 0
        }
      };
    },
    computed: {
      classes: function classes() {
        return {
          root: Vt({
            fill: this.fill
          }),
          wrapper: Vt("wrapper"),
          imageWrapper: Vt("image-wrapper"),
          image: l(Vt("image"), this.imageClass)
        };
      },
      style: function style() {
        if (this.fill) return {};
        var t = {};
        return this.width && (t.width = "".concat(this.size.width, "px")), this.height && (t.height = "".concat(this.size.height, "px")), this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), t;
      },
      wrapperStyle: function wrapperStyle() {
        var t = {
          width: "".concat(this.size.width, "px"),
          height: "".concat(this.size.height, "px"),
          left: "calc(50% - ".concat(this.size.width / 2, "px)"),
          top: "calc(50% - ".concat(this.size.height / 2, "px)")
        };
        return this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), t;
      },
      imageStyle: function imageStyle() {
        if (this.coordinates && this.image) {
          var t = this.coordinates.width / this.size.width,
            e = i(i({
              rotate: 0,
              flip: {
                horizontal: !1,
                vertical: !1
              }
            }, this.image.transforms), {}, {
              scaleX: 1 / t,
              scaleY: 1 / t
            }),
            n = this.imageSize.width,
            s = this.imageSize.height,
            o = it({
              width: n,
              height: s
            }, e.rotate),
            r = {
              width: "".concat(n, "px"),
              height: "".concat(s, "px"),
              left: "0px",
              top: "0px"
            },
            a = {
              rotate: {
                left: (n - o.width) * e.scaleX / 2,
                top: (s - o.height) * e.scaleY / 2
              },
              scale: {
                left: (1 - e.scaleX) * n / 2,
                top: (1 - e.scaleY) * s / 2
              }
            };
          return r.transform = "translate(\n\t\t\t\t".concat(-this.coordinates.left / t - a.rotate.left - a.scale.left, "px,").concat(-this.coordinates.top / t - a.rotate.top - a.scale.top, "px) ") + _t(e), this.transitions && this.transitions.enabled && (r.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), r;
        }
        return {};
      },
      size: function size() {
        return {
          width: this.width || this.calculatedSize.width,
          height: this.height || this.calculatedSize.height
        };
      },
      imageSize: function imageSize() {
        return {
          width: this.image.width || this.calculatedImageSize.width,
          height: this.image.height || this.calculatedImageSize.height
        };
      }
    },
    watch: {
      image: function image(t) {
        (t.width || t.height) && this.onChangeImage();
      }
    },
    mounted: function mounted() {
      var t = this;
      this.onChangeImage(), this.$refs.image.addEventListener("load", function () {
        t.refreshImage();
      }), window.addEventListener("resize", this.refresh), window.addEventListener("orientationchange", this.refresh);
    },
    destroyed: function destroyed() {
      window.removeEventListener("resize", this.refresh), window.removeEventListener("orientationchange", this.refresh);
    },
    methods: {
      refreshImage: function refreshImage() {
        var t = this.$refs.image;
        this.calculatedImageSize.height = t.naturalHeight, this.calculatedImageSize.width = t.naturalWidth;
      },
      refresh: function refresh() {
        var t = this.$refs.root;
        this.width || (this.calculatedSize.width = t.clientWidth), this.height || (this.calculatedSize.height = t.clientHeight);
      },
      onChangeImage: function onChangeImage() {
        var t = this.$refs.image;
        t && t.complete && this.refreshImage(), this.refresh();
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  qt = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("preview", t._b({
        attrs: {
          fill: !0
        }
      }, "preview", t.$attrs, !1));
    },
    staticRenderFns: []
  }, undefined, {
    components: {
      Preview: Zt
    },
    inheritAttrs: !1
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Gt = d("vue-rectangle-stencil"),
  Qt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.stencil,
        style: t.style
      }, [i("bounding-box", {
        "class": t.classes.boundingBox,
        attrs: {
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions,
          handlers: t.handlers,
          "handlers-component": t.handlersComponent,
          "handlers-classes": t.handlersClasses,
          "handlers-wrappers-classes": t.handlersWrappersClasses,
          lines: t.lines,
          "lines-component": t.linesComponent,
          "lines-classes": t.linesClasses,
          "lines-wrappers-classes": t.linesWrappersClasses,
          resizable: t.resizable
        },
        on: {
          resize: t.onResize,
          "resize-end": t.onResizeEnd
        }
      }, [i("draggable-area", {
        attrs: {
          movable: t.movable
        },
        on: {
          move: t.onMove,
          "move-end": t.onMoveEnd
        }
      }, [i("stencil-preview", {
        "class": t.classes.preview,
        attrs: {
          image: t.image,
          coordinates: t.coordinates,
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions
        }
      })], 1)], 1)], 1);
    },
    staticRenderFns: []
  }, undefined, {
    name: "RectangleStencil",
    components: {
      StencilPreview: qt,
      BoundingBox: Pt,
      DraggableArea: Lt
    },
    props: {
      image: {
        type: Object
      },
      coordinates: {
        type: Object
      },
      stencilCoordinates: {
        type: Object
      },
      handlers: {
        type: Object
      },
      handlersComponent: {
        type: [Object, String],
        "default": function _default() {
          return Et;
        }
      },
      lines: {
        type: Object
      },
      linesComponent: {
        type: [Object, String],
        "default": function _default() {
          return Ot;
        }
      },
      aspectRatio: {
        type: [Number, String]
      },
      minAspectRatio: {
        type: [Number, String]
      },
      maxAspectRatio: {
        type: [Number, String]
      },
      movable: {
        type: Boolean,
        "default": !0
      },
      resizable: {
        type: Boolean,
        "default": !0
      },
      transitions: {
        type: Object
      },
      movingClass: {
        type: String
      },
      resizingClass: {
        type: String
      },
      previewClass: {
        type: String
      },
      boundingBoxClass: {
        type: String
      },
      linesClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      linesWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      }
    },
    data: function data() {
      return {
        moving: !1,
        resizing: !1
      };
    },
    computed: {
      classes: function classes() {
        return {
          stencil: l(Gt({
            movable: this.movable,
            moving: this.moving,
            resizing: this.resizing
          }), this.moving && this.movingClass, this.resizing && this.resizingClass),
          preview: l(Gt("preview"), this.previewClass),
          boundingBox: l(Gt("bounding-box"), this.boundingBoxClass)
        };
      },
      style: function style() {
        var t = this.stencilCoordinates,
          e = t.height,
          i = t.width,
          n = t.left,
          s = t.top,
          o = {
            width: "".concat(i, "px"),
            height: "".concat(e, "px"),
            transform: "translate(".concat(n, "px, ").concat(s, "px)")
          };
        return this.transitions && this.transitions.enabled && (o.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), o;
      }
    },
    methods: {
      onMove: function onMove(t) {
        this.$emit("move", t), this.moving = !0;
      },
      onMoveEnd: function onMoveEnd() {
        this.$emit("move-end"), this.moving = !1;
      },
      onResize: function onResize(t) {
        this.$emit("resize", t), this.resizing = !0;
      },
      onResizeEnd: function onResizeEnd() {
        this.$emit("resize-end"), this.resizing = !1;
      },
      aspectRatios: function aspectRatios() {
        return {
          minimum: this.aspectRatio || this.minAspectRatio,
          maximum: this.aspectRatio || this.maxAspectRatio
        };
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Kt = d("vue-circle-stencil"),
  Jt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.stencil,
        style: t.style
      }, [i("bounding-box", {
        "class": t.classes.boundingBox,
        attrs: {
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions,
          handlers: t.handlers,
          "handlers-component": t.handlersComponent,
          "handlers-classes": t.handlersClasses,
          "handlers-wrappers-classes": t.handlersWrappersClasses,
          lines: t.lines,
          "lines-component": t.linesComponent,
          "lines-classes": t.linesClasses,
          "lines-wrappers-classes": t.linesWrappersClasses,
          resizable: t.resizable
        },
        on: {
          resize: t.onResize,
          "resize-end": t.onResizeEnd
        }
      }, [i("draggable-area", {
        attrs: {
          movable: t.movable
        },
        on: {
          move: t.onMove,
          "move-end": t.onMoveEnd
        }
      }, [i("stencil-preview", {
        "class": t.classes.preview,
        attrs: {
          image: t.image,
          coordinates: t.coordinates,
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions
        }
      })], 1)], 1)], 1);
    },
    staticRenderFns: []
  }, undefined, {
    components: {
      StencilPreview: qt,
      BoundingBox: Pt,
      DraggableArea: Lt
    },
    props: {
      image: {
        type: Object
      },
      coordinates: {
        type: Object
      },
      stencilCoordinates: {
        type: Object
      },
      handlers: {
        type: Object,
        "default": function _default() {
          return {
            eastNorth: !0,
            westNorth: !0,
            westSouth: !0,
            eastSouth: !0
          };
        }
      },
      handlersComponent: {
        type: [Object, String],
        "default": function _default() {
          return Et;
        }
      },
      handlersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      lines: {
        type: Object
      },
      linesComponent: {
        type: [Object, String],
        "default": function _default() {
          return Ot;
        }
      },
      linesClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      linesWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      movable: {
        type: Boolean,
        "default": !0
      },
      resizable: {
        type: Boolean,
        "default": !0
      },
      transitions: {
        type: Object
      },
      movingClass: {
        type: String
      },
      resizingClass: {
        type: String
      },
      previewClass: {
        type: String
      },
      boundingBoxClass: {
        type: String
      }
    },
    data: function data() {
      return {
        moving: !1,
        resizing: !1
      };
    },
    computed: {
      classes: function classes() {
        return {
          stencil: l(Kt({
            movable: this.movable,
            moving: this.moving,
            resizing: this.resizing
          }), this.moving && this.movingClass, this.resizing && this.resizingClass),
          preview: l(Kt("preview"), this.previewClass),
          boundingBox: l(Kt("bounding-box"), this.boundingBoxClass)
        };
      },
      style: function style() {
        var t = this.stencilCoordinates,
          e = t.height,
          i = t.width,
          n = t.left,
          s = t.top,
          o = {
            width: "".concat(i, "px"),
            height: "".concat(e, "px"),
            transform: "translate(".concat(n, "px, ").concat(s, "px)")
          };
        return this.transitions && this.transitions.enabled && (o.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), o;
      }
    },
    methods: {
      onMove: function onMove(t) {
        this.$emit("move", t), this.moving = !0;
      },
      onMoveEnd: function onMoveEnd() {
        this.$emit("move-end"), this.moving = !1;
      },
      onResize: function onResize(t) {
        this.$emit("resize", t), this.resizing = !0;
      },
      onResizeEnd: function onResizeEnd() {
        this.$emit("resize-end"), this.resizing = !1;
      },
      aspectRatios: function aspectRatios() {
        return {
          minimum: 1,
          maximum: 1
        };
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0);
var te = ["transitions"],
  ee = d("vue-advanced-cropper"),
  ie = {
    name: "Cropper",
    components: {
      BackgroundWrapper: Nt
    },
    props: {
      src: {
        type: String,
        "default": null
      },
      stencilComponent: {
        type: [Object, String],
        "default": function _default() {
          return Qt;
        }
      },
      backgroundWrapperComponent: {
        type: [Object, String],
        "default": function _default() {
          return Nt;
        }
      },
      stencilProps: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      autoZoom: {
        type: Boolean,
        "default": !1
      },
      imageClass: {
        type: String
      },
      boundariesClass: {
        type: String
      },
      backgroundClass: {
        type: String
      },
      foregroundClass: {
        type: String
      },
      minWidth: {
        type: [Number, String]
      },
      minHeight: {
        type: [Number, String]
      },
      maxWidth: {
        type: [Number, String]
      },
      maxHeight: {
        type: [Number, String]
      },
      debounce: {
        type: [Boolean, Number],
        "default": 500
      },
      transitions: {
        type: Boolean,
        "default": !0
      },
      checkOrientation: {
        type: Boolean,
        "default": !0
      },
      canvas: {
        type: [Object, Boolean],
        "default": !0
      },
      crossOrigin: {
        type: [Boolean, String],
        "default": void 0
      },
      transitionTime: {
        type: Number,
        "default": 300
      },
      imageRestriction: {
        type: String,
        "default": "fit-area",
        validator: function validator(t) {
          return -1 !== _.indexOf(t);
        }
      },
      roundResult: {
        type: Boolean,
        "default": !0
      },
      defaultSize: {
        type: [Function, Object]
      },
      defaultPosition: {
        type: [Function, Object],
        "default": function _default(t) {
          var e = t.imageSize,
            i = t.visibleArea,
            n = t.coordinates,
            s = i || e;
          return {
            left: (i ? i.left : 0) + s.width / 2 - n.width / 2,
            top: (i ? i.top : 0) + s.height / 2 - n.height / 2
          };
        }
      },
      defaultVisibleArea: {
        type: [Function, Object],
        "default": function _default(t) {
          var e = t.getAreaRestrictions,
            i = t.coordinates,
            n = t.imageSize,
            s = Q(t.boundaries);
          if (i) {
            var o = {
                height: Math.max(i.height, n.height),
                width: Math.max(i.width, n.width)
              },
              r = et({
                width: Q(o) > s ? o.width : o.height * s,
                height: Q(o) > s ? o.width / s : o.height
              }, rt(e())),
              a = {
                left: i.left + i.width / 2 - r.width / 2,
                top: i.top + i.height / 2 - r.height / 2,
                width: r.width,
                height: r.height
              },
              h = N(i, Y(_f({
                left: 0,
                top: 0
              }, n))),
              c = {};
            return !h.left && !h.right && a.width <= n.width && (c.left = 0, c.right = n.width), !h.top && !h.bottom && a.height <= n.height && (c.top = 0, c.bottom = n.height), ot(a, c);
          }
          var l = Q(n);
          return r = {
            height: l > s ? n.height : n.width / s,
            width: l > s ? n.height * s : n.width
          }, {
            left: n.width / 2 - r.width / 2,
            top: n.height / 2 - r.height / 2,
            width: r.width,
            height: r.height
          };
        }
      },
      defaultTransforms: {
        type: [Function, Object]
      },
      defaultBoundaries: {
        type: [Function, String],
        validator: function validator(t) {
          return !("string" == typeof t && "fill" !== t && "fit" !== t);
        }
      },
      priority: {
        type: String,
        "default": "coordinates"
      },
      stencilSize: {
        type: [Object, Function]
      },
      resizeImage: {
        type: [Boolean, Object],
        "default": !0
      },
      moveImage: {
        type: [Boolean, Object],
        "default": !0
      },
      autoZoomAlgorithm: {
        type: Function
      },
      resizeAlgorithm: {
        type: Function,
        "default": function _default(t) {
          var e = t.event,
            i = t.coordinates,
            n = t.aspectRatio,
            s = t.positionRestrictions,
            o = t.sizeRestrictions,
            r = _f(_f({}, i), {
              right: i.left + i.width,
              bottom: i.top + i.height
            }),
            a = e.params || {},
            h = _f({}, e.directions),
            c = a.allowedDirections || {
              left: !0,
              right: !0,
              bottom: !0,
              top: !0
            };
          o.widthFrozen && (h.left = 0, h.right = 0), o.heightFrozen && (h.top = 0, h.bottom = 0), D.forEach(function (t) {
            c[t] || (h[t] = 0);
          });
          var l = V(r, h = St({
              coordinates: r,
              directions: h,
              sizeRestrictions: o,
              positionRestrictions: s
            })).width,
            d = V(r, h).height,
            u = a.preserveRatio ? Q(r) : tt(l / d, n);
          if (u) {
            var m = a.respectDirection;
            if (m || (m = r.width >= r.height || 1 === u ? "width" : "height"), "width" === m) {
              var p = l / u - r.height;
              if (c.top && c.bottom) {
                var g = h.top,
                  v = h.bottom;
                h.bottom = xt(p, v, g), h.top = xt(p, g, v);
              } else c.bottom ? h.bottom = p : c.top ? h.top = p : c.right ? h.right = 0 : c.left && (h.left = 0);
            } else if ("height" === m) {
              var b = r.width - d * u;
              if (c.left && c.right) {
                var w = h.left,
                  y = h.right;
                h.left = -xt(b, w, y), h.right = -xt(b, y, w);
              } else c.left ? h.left = -b : c.right ? h.right = -b : c.top ? h.top = 0 : c.bottom && (h.bottom = 0);
            }
            h = St({
              directions: h,
              coordinates: r,
              sizeRestrictions: o,
              positionRestrictions: s,
              preserveRatio: !0,
              compensate: a.compensate
            });
          }
          return l = V(r, h).width, d = V(r, h).height, (u = a.preserveRatio ? Q(r) : tt(l / d, n)) && Math.abs(u - l / d) > .001 && D.forEach(function (t) {
            c[t] || (h[t] = 0);
          }), mt({
            event: new E({
              left: -h.left,
              top: -h.top
            }),
            coordinates: {
              width: i.width + h.right + h.left,
              height: i.height + h.top + h.bottom,
              left: i.left,
              top: i.top
            },
            positionRestrictions: s
          });
        }
      },
      moveAlgorithm: {
        type: Function,
        "default": mt
      },
      initStretcher: {
        type: Function,
        "default": function _default(t) {
          var e = t.stretcher,
            i = t.imageSize,
            n = Q(i);
          e.style.width = i.width + "px", e.style.height = e.clientWidth / n + "px", e.style.width = e.clientWidth + "px";
        }
      },
      fitCoordinates: {
        type: Function,
        "default": function _default(t) {
          var e = t.visibleArea,
            i = t.coordinates,
            n = t.aspectRatio,
            s = t.sizeRestrictions,
            o = t.positionRestrictions,
            r = _f(_f({}, i), ut({
              width: i.width,
              height: i.height,
              aspectRatio: n,
              sizeRestrictions: {
                maxWidth: e.width,
                maxHeight: e.height,
                minHeight: Math.min(e.height, s.minHeight),
                minWidth: Math.min(e.width, s.minWidth)
              }
            }));
          return r = ot(r = q(r, k(U(i), U(r))), ct(Y(e), o));
        }
      },
      fitVisibleArea: {
        type: Function,
        "default": function _default(t) {
          var e = t.visibleArea,
            i = t.boundaries,
            n = t.getAreaRestrictions,
            s = t.coordinates,
            o = _f({}, e);
          o.height = o.width / Q(i), o.top += (e.height - o.height) / 2, (s.height - o.height > 0 || s.width - o.width > 0) && (o = G(o, Math.max(s.height / o.height, s.width / o.width)));
          var r = Z(J(s, Y(o = G(o, st(o, n({
            visibleArea: o,
            type: "resize"
          }))))));
          return o.width < s.width && (r.left = 0), o.height < s.height && (r.top = 0), o = ot(o = q(o, r), n({
            visibleArea: o,
            type: "move"
          }));
        }
      },
      areaRestrictionsAlgorithm: {
        type: Function,
        "default": function _default(t) {
          var e = t.visibleArea,
            i = t.boundaries,
            n = t.imageSize,
            s = t.imageRestriction,
            o = t.type,
            r = {};
          return "fill-area" === s ? r = {
            left: 0,
            top: 0,
            right: n.width,
            bottom: n.height
          } : "fit-area" === s && (Q(i) > Q(n) ? (r = {
            top: 0,
            bottom: n.height
          }, e && "move" === o && (e.width > n.width ? (r.left = -(e.width - n.width) / 2, r.right = n.width - r.left) : (r.left = 0, r.right = n.width))) : (r = {
            left: 0,
            right: n.width
          }, e && "move" === o && (e.height > n.height ? (r.top = -(e.height - n.height) / 2, r.bottom = n.height - r.top) : (r.top = 0, r.bottom = n.height)))), r;
        }
      },
      sizeRestrictionsAlgorithm: {
        type: Function,
        "default": function _default(t) {
          return {
            minWidth: t.minWidth,
            minHeight: t.minHeight,
            maxWidth: t.maxWidth,
            maxHeight: t.maxHeight
          };
        }
      },
      positionRestrictionsAlgorithm: {
        type: Function,
        "default": function _default(t) {
          var e = t.imageSize,
            i = {};
          return "none" !== t.imageRestriction && (i = {
            left: 0,
            top: 0,
            right: e.width,
            bottom: e.height
          }), i;
        }
      }
    },
    data: function data() {
      return {
        transitionsActive: !1,
        imageLoaded: !1,
        imageAttributes: {
          width: null,
          height: null,
          crossOrigin: !1,
          src: null
        },
        defaultImageTransforms: {
          rotate: 0,
          flip: {
            horizontal: !1,
            vertical: !1
          }
        },
        appliedImageTransforms: {
          rotate: 0,
          flip: {
            horizontal: !1,
            vertical: !1
          }
        },
        boundaries: {
          width: 0,
          height: 0
        },
        visibleArea: null,
        coordinates: i({}, F)
      };
    },
    computed: {
      image: function image() {
        return {
          src: this.imageAttributes.src,
          width: this.imageAttributes.width,
          height: this.imageAttributes.height,
          transforms: this.imageTransforms
        };
      },
      imageTransforms: function imageTransforms() {
        return {
          rotate: this.appliedImageTransforms.rotate,
          flip: {
            horizontal: this.appliedImageTransforms.flip.horizontal,
            vertical: this.appliedImageTransforms.flip.vertical
          },
          translateX: this.visibleArea ? this.visibleArea.left / this.coefficient : 0,
          translateY: this.visibleArea ? this.visibleArea.top / this.coefficient : 0,
          scaleX: 1 / this.coefficient,
          scaleY: 1 / this.coefficient
        };
      },
      imageSize: function imageSize() {
        var t = function (t) {
          return t * Math.PI / 180;
        }(this.imageTransforms.rotate);
        return {
          width: Math.abs(this.imageAttributes.width * Math.cos(t)) + Math.abs(this.imageAttributes.height * Math.sin(t)),
          height: Math.abs(this.imageAttributes.width * Math.sin(t)) + Math.abs(this.imageAttributes.height * Math.cos(t))
        };
      },
      initialized: function initialized() {
        return Boolean(this.visibleArea && this.imageLoaded);
      },
      settings: function settings() {
        var t = z(this.resizeImage, {
          touch: !0,
          wheel: {
            ratio: .1
          },
          adjustStencil: !0
        }, {
          touch: !1,
          wheel: !1,
          adjustStencil: !1
        });
        return {
          moveImage: z(this.moveImage, {
            touch: !0,
            mouse: !0
          }, {
            touch: !1,
            mouse: !1
          }),
          resizeImage: t
        };
      },
      coefficient: function coefficient() {
        return this.visibleArea ? this.visibleArea.width / this.boundaries.width : 0;
      },
      areaRestrictions: function areaRestrictions() {
        return this.imageLoaded ? this.areaRestrictionsAlgorithm({
          imageSize: this.imageSize,
          imageRestriction: this.imageRestriction,
          boundaries: this.boundaries
        }) : {};
      },
      transitionsOptions: function transitionsOptions() {
        return {
          enabled: this.transitionsActive,
          timingFunction: "ease-in-out",
          time: 350
        };
      },
      sizeRestrictions: function sizeRestrictions() {
        if (this.boundaries.width && this.boundaries.height && this.imageSize.width && this.imageSize.height) {
          var t = this.sizeRestrictionsAlgorithm({
            imageSize: this.imageSize,
            minWidth: w(this.minWidth) ? 0 : R(this.minWidth),
            minHeight: w(this.minHeight) ? 0 : R(this.minHeight),
            maxWidth: w(this.maxWidth) ? 1 / 0 : R(this.maxWidth),
            maxHeight: w(this.maxHeight) ? 1 / 0 : R(this.maxHeight)
          });
          if (t = function (t) {
            var e = t.areaRestrictions,
              i = t.sizeRestrictions;
            t.imageSize;
            var n = t.boundaries,
              s = t.positionRestrictions;
            t.imageRestriction;
            var o = _f(_f({}, i), {
              minWidth: void 0 !== i.minWidth ? i.minWidth : 0,
              minHeight: void 0 !== i.minHeight ? i.minHeight : 0,
              maxWidth: void 0 !== i.maxWidth ? i.maxWidth : 1 / 0,
              maxHeight: void 0 !== i.maxHeight ? i.maxHeight : 1 / 0
            });
            void 0 !== s.left && void 0 !== s.right && (o.maxWidth = Math.min(o.maxWidth, s.right - s.left)), void 0 !== s.bottom && void 0 !== s.top && (o.maxHeight = Math.min(o.maxHeight, s.bottom - s.top));
            var r = rt(e),
              a = et(n, r);
            return r.width < 1 / 0 && (!o.maxWidth || o.maxWidth > a.width) && (o.maxWidth = Math.min(o.maxWidth, a.width)), r.height < 1 / 0 && (!o.maxHeight || o.maxHeight > a.height) && (o.maxHeight = Math.min(o.maxHeight, a.height)), o.minWidth > o.maxWidth && (o.minWidth = o.maxWidth, o.widthFrozen = !0), o.minHeight > o.maxHeight && (o.minHeight = o.maxHeight, o.heightFrozen = !0), o;
          }({
            sizeRestrictions: t,
            areaRestrictions: this.getAreaRestrictions({
              visibleArea: this.visibleArea,
              type: "resize"
            }),
            imageSize: this.imageSize,
            boundaries: this.boundaries,
            positionRestrictions: this.positionRestrictions,
            imageRestriction: this.imageRestriction,
            visibleArea: this.visibleArea,
            stencilSize: this.getStencilSize()
          }), this.visibleArea && this.stencilSize) {
            var e = this.getStencilSize(),
              i = rt(this.getAreaRestrictions({
                visibleArea: this.visibleArea,
                type: "resize"
              }));
            t.maxWidth = Math.min(t.maxWidth, i.width * e.width / this.boundaries.width), t.maxHeight = Math.min(t.maxHeight, i.height * e.height / this.boundaries.height), t.maxWidth < t.minWidth && (t.minWidth = t.maxWidth), t.maxHeight < t.minHeight && (t.minHeight = t.maxHeight);
          }
          return t;
        }
        return {
          minWidth: 0,
          minHeight: 0,
          maxWidth: 0,
          maxHeight: 0
        };
      },
      positionRestrictions: function positionRestrictions() {
        return this.positionRestrictionsAlgorithm({
          imageSize: this.imageSize,
          imageRestriction: this.imageRestriction
        });
      },
      classes: function classes() {
        return {
          cropper: ee(),
          image: l(ee("image"), this.imageClass),
          stencil: ee("stencil"),
          boundaries: l(ee("boundaries"), this.boundariesClass),
          stretcher: l(ee("stretcher")),
          background: l(ee("background"), this.backgroundClass),
          foreground: l(ee("foreground"), this.foregroundClass),
          imageWrapper: l(ee("image-wrapper")),
          cropperWrapper: l(ee("cropper-wrapper"))
        };
      },
      stencilCoordinates: function stencilCoordinates() {
        if (this.initialized) {
          var t = this.coordinates,
            e = t.width,
            i = t.height,
            n = t.left,
            s = t.top;
          return {
            width: e / this.coefficient,
            height: i / this.coefficient,
            left: (n - this.visibleArea.left) / this.coefficient,
            top: (s - this.visibleArea.top) / this.coefficient
          };
        }
        return this.defaultCoordinates();
      },
      boundariesStyle: function boundariesStyle() {
        var t = {
          width: this.boundaries.width ? "".concat(Math.round(this.boundaries.width), "px") : "auto",
          height: this.boundaries.height ? "".concat(Math.round(this.boundaries.height), "px") : "auto",
          transition: "opacity ".concat(this.transitionTime, "ms"),
          pointerEvents: this.imageLoaded ? "all" : "none"
        };
        return this.imageLoaded || (t.opacity = "0"), t;
      },
      imageStyle: function imageStyle() {
        var t = this.imageAttributes.width > this.imageAttributes.height ? {
            width: Math.min(1024, this.imageAttributes.width),
            height: Math.min(1024, this.imageAttributes.width) / (this.imageAttributes.width / this.imageAttributes.height)
          } : {
            height: Math.min(1024, this.imageAttributes.height),
            width: Math.min(1024, this.imageAttributes.height) * (this.imageAttributes.width / this.imageAttributes.height)
          },
          e = {
            left: (t.width - this.imageSize.width) / (2 * this.coefficient),
            top: (t.height - this.imageSize.height) / (2 * this.coefficient)
          },
          n = {
            left: (1 - 1 / this.coefficient) * t.width / 2,
            top: (1 - 1 / this.coefficient) * t.height / 2
          },
          s = i(i({}, this.imageTransforms), {}, {
            scaleX: this.imageTransforms.scaleX * (this.imageAttributes.width / t.width),
            scaleY: this.imageTransforms.scaleY * (this.imageAttributes.height / t.height)
          }),
          o = {
            width: "".concat(t.width, "px"),
            height: "".concat(t.height, "px"),
            left: "0px",
            top: "0px",
            transform: "translate(".concat(-e.left - n.left - this.imageTransforms.translateX, "px, ").concat(-e.top - n.top - this.imageTransforms.translateY, "px)") + _t(s)
          };
        return this.transitionsOptions.enabled && (o.transition = "".concat(this.transitionsOptions.time, "ms ").concat(this.transitionsOptions.timingFunction)), o;
      }
    },
    watch: {
      src: function src() {
        this.onChangeImage();
      },
      stencilComponent: function stencilComponent() {
        var t = this;
        this.$nextTick(function () {
          t.resetCoordinates(), t.runAutoZoom("setCoordinates"), t.onChange();
        });
      },
      minWidth: function minWidth() {
        this.onPropsChange();
      },
      maxWidth: function maxWidth() {
        this.onPropsChange();
      },
      minHeight: function minHeight() {
        this.onPropsChange();
      },
      maxHeight: function maxHeight() {
        this.onPropsChange();
      },
      imageRestriction: function imageRestriction() {
        this.reset();
      },
      stencilProps: function stencilProps(t, e) {
        ["aspectRatio", "minAspectRatio", "maxAspectRatio"].find(function (i) {
          return t[i] !== e[i];
        }) && this.$nextTick(this.onPropsChange);
      }
    },
    created: function created() {
      this.debouncedUpdate = m(this.update, this.debounce), this.debouncedDisableTransitions = m(this.disableTransitions, this.transitionsOptions.time), this.awaiting = !1;
    },
    mounted: function mounted() {
      this.$refs.image.addEventListener("load", this.onSuccessLoadImage), this.$refs.image.addEventListener("error", this.onFailLoadImage), this.onChangeImage(), window.addEventListener("resize", this.refresh), window.addEventListener("orientationchange", this.refresh);
    },
    destroyed: function destroyed() {
      window.removeEventListener("resize", this.refresh), window.removeEventListener("orientationchange", this.refresh), this.imageAttributes.revoke && this.imageAttributes.src && URL.revokeObjectURL(this.imageAttributes.src), this.debouncedUpdate.clear(), this.debouncedDisableTransitions.clear();
    },
    methods: {
      getResult: function getResult() {
        var t = this.initialized ? this.prepareResult(i({}, this.coordinates)) : this.defaultCoordinates(),
          e = {
            rotate: this.imageTransforms.rotate % 360,
            flip: i({}, this.imageTransforms.flip)
          };
        if (this.src && this.imageLoaded) {
          var n = this;
          return {
            image: this.image,
            coordinates: t,
            visibleArea: this.visibleArea ? i({}, this.visibleArea) : null,
            imageTransforms: e,
            get canvas() {
              return n.canvas ? n.getCanvas() : void 0;
            }
          };
        }
        return {
          image: this.image,
          coordinates: t,
          visibleArea: this.visibleArea ? i({}, this.visibleArea) : null,
          canvas: void 0,
          imageTransforms: e
        };
      },
      zoom: function zoom(t, e) {
        var i = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
          n = i.transitions,
          s = void 0 === n || n;
        this.onManipulateImage(new M({}, {
          factor: 1 / t,
          center: e
        }), {
          normalize: !1,
          transitions: s
        });
      },
      move: function move(t, e) {
        var i = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
          n = i.transitions,
          s = void 0 === n || n;
        this.onManipulateImage(new M({
          left: t || 0,
          top: e || 0
        }), {
          normalize: !1,
          transitions: s
        });
      },
      setCoordinates: function setCoordinates(t) {
        var e = this,
          i = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          n = i.autoZoom,
          s = void 0 === n || n,
          o = i.transitions,
          r = void 0 === o || o;
        this.$nextTick(function () {
          e.imageLoaded ? (e.transitionsActive || (r && e.enableTransitions(), e.coordinates = e.applyTransform(t), s && e.runAutoZoom("setCoordinates"), r && e.debouncedDisableTransitions()), e.onChange()) : e.delayedTransforms = t;
        });
      },
      refresh: function refresh() {
        var t = this,
          e = this.$refs.image;
        if (this.src && e) return this.initialized ? this.updateVisibleArea().then(function () {
          t.onChange();
        }) : this.resetVisibleArea().then(function () {
          t.onChange();
        });
      },
      reset: function reset() {
        var t = this;
        return this.resetVisibleArea().then(function () {
          t.onChange(!1);
        });
      },
      awaitRender: function awaitRender(t) {
        var e = this;
        this.awaiting || (this.awaiting = !0, this.$nextTick(function () {
          t(), e.awaiting = !1;
        }));
      },
      prepareResult: function prepareResult(t) {
        return this.roundResult ? function (t) {
          var e = t.coordinates,
            i = t.sizeRestrictions,
            n = t.positionRestrictions,
            s = {
              width: Math.round(e.width),
              height: Math.round(e.height),
              left: Math.round(e.left),
              top: Math.round(e.top)
            };
          return s.width > i.maxWidth ? s.width = Math.floor(e.width) : s.width < i.minWidth && (s.width = Math.ceil(e.width)), s.height > i.maxHeight ? s.height = Math.floor(e.height) : s.height < i.minHeight && (s.height = Math.ceil(e.height)), ot(s, n);
        }(i(i({}, this.getPublicProperties()), {}, {
          positionRestrictions: yt(this.positionRestrictions, this.visibleArea),
          coordinates: t
        })) : t;
      },
      processAutoZoom: function processAutoZoom(t, e, n, s) {
        var o = this.autoZoomAlgorithm;
        o || (o = this.stencilSize ? pt : this.autoZoom ? gt : vt);
        var r = o({
          event: {
            type: t,
            params: s
          },
          visibleArea: e,
          coordinates: n,
          boundaries: this.boundaries,
          aspectRatio: this.getAspectRatio(),
          positionRestrictions: this.positionRestrictions,
          getAreaRestrictions: this.getAreaRestrictions,
          sizeRestrictions: this.sizeRestrictions,
          stencilSize: this.getStencilSize()
        });
        return i(i({}, r), {}, {
          changed: !X(r.visibleArea, e) || !X(r.coordinates, n)
        });
      },
      runAutoZoom: function runAutoZoom(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          i = e.transitions,
          n = void 0 !== i && i,
          o = s(e, te),
          r = this.processAutoZoom(t, this.visibleArea, this.coordinates, o),
          a = r.visibleArea,
          h = r.coordinates,
          c = r.changed;
        n && c && this.enableTransitions(), this.visibleArea = a, this.coordinates = h, n && c && this.debouncedDisableTransitions();
      },
      normalizeEvent: function normalizeEvent(t) {
        return function (t) {
          var e = t.event,
            i = t.visibleArea,
            n = t.coefficient;
          if ("manipulateImage" === e.type) return _f(_f({}, e), {
            move: {
              left: e.move && e.move.left ? n * e.move.left : 0,
              top: e.move && e.move.top ? n * e.move.top : 0
            },
            scale: {
              factor: e.scale && e.scale.factor ? e.scale.factor : 1,
              center: e.scale && e.scale.center ? {
                left: e.scale.center.left * n + i.left,
                top: e.scale.center.top * n + i.top
              } : null
            }
          });
          if ("resize" === e.type) {
            var s = _f(_f({}, e), {
              directions: _f({}, e.directions)
            });
            return D.forEach(function (t) {
              s.directions[t] *= n;
            }), s;
          }
          if ("move" === e.type) {
            var o = _f(_f({}, e), {
              directions: _f({}, e.directions)
            });
            return B.forEach(function (t) {
              o.directions[t] *= n;
            }), o;
          }
          return e;
        }(i(i({}, this.getPublicProperties()), {}, {
          event: t
        }));
      },
      getCanvas: function getCanvas() {
        if (this.$refs.canvas) {
          var t = this.$refs.canvas,
            e = this.$refs.image,
            n = 0 !== this.imageTransforms.rotate || this.imageTransforms.flip.horizontal || this.imageTransforms.flip.vertical ? function (t, e, i) {
              var n = i.rotate,
                s = i.flip,
                o = {
                  width: e.naturalWidth,
                  height: e.naturalHeight
                },
                r = it(o, n),
                a = t.getContext("2d");
              t.height = r.height, t.width = r.width, a.save();
              var h = nt(U(_f({
                left: 0,
                top: 0
              }, o)), n);
              return a.translate(-(h.left - r.width / 2), -(h.top - r.height / 2)), a.rotate(n * Math.PI / 180), a.translate(s.horizontal ? o.width : 0, s.vertical ? o.height : 0), a.scale(s.horizontal ? -1 : 1, s.vertical ? -1 : 1), a.drawImage(e, 0, 0, o.width, o.height), a.restore(), t;
            }(this.$refs.sourceCanvas, e, this.imageTransforms) : e,
            s = i({
              minWidth: 0,
              minHeight: 0,
              maxWidth: 1 / 0,
              maxHeight: 1 / 0,
              maxArea: this.maxCanvasSize,
              imageSmoothingEnabled: !0,
              imageSmoothingQuality: "high",
              fillColor: "transparent"
            }, this.canvas),
            o = function o(t) {
              return t.find(function (t) {
                return e = t, !Number.isNaN(parseFloat(e)) && isFinite(e);
                var e;
              });
            },
            r = ut({
              sizeRestrictions: {
                minWidth: o([s.width, s.minWidth]) || 0,
                minHeight: o([s.height, s.minHeight]) || 0,
                maxWidth: o([s.width, s.maxWidth]) || 1 / 0,
                maxHeight: o([s.height, s.maxHeight]) || 1 / 0
              },
              width: this.coordinates.width,
              height: this.coordinates.height,
              aspectRatio: {
                minimum: this.coordinates.width / this.coordinates.height,
                maximum: this.coordinates.width / this.coordinates.height
              }
            });
          if (s.maxArea && r.width * r.height > s.maxArea) {
            var a = Math.sqrt(s.maxArea / (r.width * r.height));
            r = {
              width: Math.round(a * r.width),
              height: Math.round(a * r.height)
            };
          }
          return function (t, e, i, n, s) {
            t.width = n ? n.width : i.width, t.height = n ? n.height : i.height;
            var o = t.getContext("2d");
            o.clearRect(0, 0, t.width, t.height), s && (s.imageSmoothingEnabled && (o.imageSmoothingEnabled = s.imageSmoothingEnabled), s.imageSmoothingQuality && (o.imageSmoothingQuality = s.imageSmoothingQuality), s.fillColor && (o.fillStyle = s.fillColor, o.fillRect(0, 0, t.width, t.height), o.save()));
            var r = i.left < 0 ? -i.left : 0,
              a = i.top < 0 ? -i.top : 0;
            o.drawImage(e, i.left + r, i.top + a, i.width, i.height, r, a, t.width, t.height);
          }(t, n, this.coordinates, r, s), t;
        }
      },
      update: function update() {
        this.$emit("change", this.getResult());
      },
      applyTransform: function applyTransform(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] && arguments[1],
          i = this.visibleArea && e ? at(this.sizeRestrictions, this.visibleArea) : this.sizeRestrictions,
          n = this.visibleArea && e ? yt(this.positionRestrictions, this.visibleArea) : this.positionRestrictions;
        return ft({
          transform: t,
          coordinates: this.coordinates,
          imageSize: this.imageSize,
          sizeRestrictions: i,
          positionRestrictions: n,
          aspectRatio: this.getAspectRatio(),
          visibleArea: this.visibleArea
        });
      },
      resetCoordinates: function resetCoordinates() {
        var t = this;
        if (this.$refs.image) {
          this.$refs.cropper, this.$refs.image;
          var e = this.defaultSize;
          e || (e = this.stencilSize ? wt : bt);
          var n = this.sizeRestrictions;
          n.minWidth, n.minHeight, n.maxWidth, n.maxHeight;
          var s = [b(e) ? e({
            boundaries: this.boundaries,
            imageSize: this.imageSize,
            aspectRatio: this.getAspectRatio(),
            sizeRestrictions: this.sizeRestrictions,
            stencilSize: this.getStencilSize(),
            visibleArea: this.visibleArea
          }) : e, function (e) {
            var n = e.coordinates;
            return i({}, b(t.defaultPosition) ? t.defaultPosition({
              coordinates: n,
              imageSize: t.imageSize,
              visibleArea: t.visibleArea
            }) : t.defaultPosition);
          }];
          this.delayedTransforms && s.push.apply(s, o(Array.isArray(this.delayedTransforms) ? this.delayedTransforms : [this.delayedTransforms])), this.coordinates = this.applyTransform(s, !0), this.delayedTransforms = null;
        }
      },
      clearImage: function clearImage() {
        var t = this;
        this.imageLoaded = !1, setTimeout(function () {
          var e = t.$refs.stretcher;
          e && (e.style.height = "auto", e.style.width = "auto"), t.coordinates = t.defaultCoordinates(), t.boundaries = {
            width: 0,
            height: 0
          };
        }, this.transitionTime);
      },
      enableTransitions: function enableTransitions() {
        this.transitions && (this.transitionsActive = !0);
      },
      disableTransitions: function disableTransitions() {
        this.transitionsActive = !1;
      },
      updateBoundaries: function updateBoundaries() {
        var t = this,
          e = this.$refs.stretcher,
          i = this.$refs.cropper;
        return this.initStretcher({
          cropper: i,
          stretcher: e,
          imageSize: this.imageSize
        }), this.$nextTick().then(function () {
          var e = {
            cropper: i,
            imageSize: t.imageSize
          };
          if (b(t.defaultBoundaries) ? t.boundaries = t.defaultBoundaries(e) : "fit" === t.defaultBoundaries ? t.boundaries = function (t) {
            var e = t.cropper,
              i = t.imageSize,
              n = e.clientHeight,
              s = e.clientWidth,
              o = n,
              r = i.width * n / i.height;
            return r > s && (r = s, o = i.height * s / i.width), {
              width: r,
              height: o
            };
          }(e) : t.boundaries = function (t) {
            var e = t.cropper;
            return {
              width: e.clientWidth,
              height: e.clientHeight
            };
          }(e), !t.boundaries.width || !t.boundaries.height) throw new Error("It's impossible to fit the cropper in the current container");
        });
      },
      resetVisibleArea: function resetVisibleArea() {
        var t = this;
        return this.appliedImageTransforms = i(i({}, this.defaultImageTransforms), {}, {
          flip: i({}, this.defaultImageTransforms.flip)
        }), this.updateBoundaries().then(function () {
          var e, i, n, s, o, r;
          "visible-area" !== t.priority && (t.visibleArea = null, t.resetCoordinates()), t.visibleArea = b(t.defaultVisibleArea) ? t.defaultVisibleArea({
            imageSize: t.imageSize,
            boundaries: t.boundaries,
            coordinates: "visible-area" !== t.priority ? t.coordinates : null,
            getAreaRestrictions: t.getAreaRestrictions,
            stencilSize: t.getStencilSize()
          }) : t.defaultVisibleArea, t.visibleArea = (e = {
            visibleArea: t.visibleArea,
            boundaries: t.boundaries,
            getAreaRestrictions: t.getAreaRestrictions
          }, i = e.visibleArea, n = e.boundaries, s = e.getAreaRestrictions, o = _f({}, i), r = Q(n), o.width / o.height !== r && (o.height = o.width / r), ot(o, s({
            visibleArea: o,
            type: "move"
          }))), "visible-area" === t.priority ? t.resetCoordinates() : t.coordinates = t.fitCoordinates({
            visibleArea: t.visibleArea,
            coordinates: t.coordinates,
            aspectRatio: t.getAspectRatio(),
            positionRestrictions: t.positionRestrictions,
            sizeRestrictions: t.sizeRestrictions
          }), t.runAutoZoom("resetVisibleArea");
        })["catch"](function () {
          t.visibleArea = null;
        });
      },
      updateVisibleArea: function updateVisibleArea() {
        var t = this;
        return this.updateBoundaries().then(function () {
          t.visibleArea = t.fitVisibleArea({
            imageSize: t.imageSize,
            boundaries: t.boundaries,
            visibleArea: t.visibleArea,
            coordinates: t.coordinates,
            getAreaRestrictions: t.getAreaRestrictions
          }), t.coordinates = t.fitCoordinates({
            visibleArea: t.visibleArea,
            coordinates: t.coordinates,
            aspectRatio: t.getAspectRatio(),
            positionRestrictions: t.positionRestrictions,
            sizeRestrictions: t.sizeRestrictions
          }), t.runAutoZoom("updateVisibleArea");
        })["catch"](function () {
          t.visibleArea = null;
        });
      },
      onChange: function onChange() {
        var t = !(arguments.length > 0 && void 0 !== arguments[0]) || arguments[0];
        this.$listeners && this.$listeners.change && (t && this.debounce ? this.debouncedUpdate() : this.update());
      },
      onChangeImage: function onChangeImage() {
        var t,
          e = this;
        if (this.imageLoaded = !1, this.delayedTransforms = null, this.src) {
          if (function (t) {
            if (v(t)) return !1;
            var e = window.location,
              i = /(\w+:)?(?:\/\/)([\w.-]+)?(?::(\d+))?\/?/.exec(t) || [],
              n = {
                protocol: i[1] || "",
                host: i[2] || "",
                port: i[3] || ""
              },
              s = function s(t) {
                return t.port || ("http" === (t.protocol || e.protocol) ? 80 : 433);
              };
            return !(!n.protocol && !n.host && !n.port || Boolean(n.protocol && n.protocol == e.protocol && n.host && n.host == e.host && n.host && s(n) == s(e)));
          }(this.src)) {
            var i = w(this.crossOrigin) ? this.canvas : this.crossOrigin;
            !0 === i && (i = "anonymous"), this.imageAttributes.crossOrigin = i;
          }
          if (this.checkOrientation) {
            var n = (t = this.src, new Promise(function (e) {
              Bt(t).then(function (i) {
                var n = Ft(i);
                e(i ? {
                  source: t,
                  arrayBuffer: i,
                  orientation: n
                } : {
                  source: t,
                  arrayBuffer: null,
                  orientation: null
                });
              })["catch"](function (i) {
                console.warn(i), e({
                  source: t,
                  arrayBuffer: null,
                  orientation: null
                });
              });
            }));
            setTimeout(function () {
              n.then(e.onParseImage);
            }, this.transitionTime);
          } else setTimeout(function () {
            e.onParseImage({
              source: e.src
            });
          }, this.transitionTime);
        } else this.clearImage();
      },
      onFailLoadImage: function onFailLoadImage() {
        this.imageAttributes.src && (this.clearImage(), this.$emit("error"));
      },
      onSuccessLoadImage: function onSuccessLoadImage() {
        var t = this,
          e = this.$refs.image;
        e && !this.imageLoaded && (this.imageAttributes.height = e.naturalHeight, this.imageAttributes.width = e.naturalWidth, this.imageLoaded = !0, this.resetVisibleArea().then(function () {
          t.$emit("ready"), t.onChange(!1);
        }));
      },
      onParseImage: function onParseImage(t) {
        var e = this,
          n = t.source,
          s = t.arrayBuffer,
          o = t.orientation;
        this.imageAttributes.revoke && this.imageAttributes.src && URL.revokeObjectURL(this.imageAttributes.src), this.imageAttributes.revoke = !1, s && o && o > 1 ? g(n) || !v(n) ? (this.imageAttributes.src = URL.createObjectURL(new Blob([s])), this.imageAttributes.revoke = !0) : this.imageAttributes.src = function (t) {
          for (var e = [], i = new Uint8Array(t); i.length > 0;) {
            var n = i.subarray(0, 8192);
            e.push(String.fromCharCode.apply(null, Array.from ? Array.from(n) : n.slice())), i = i.subarray(8192);
          }
          return "data:image/jpeg;base64," + btoa(e.join(""));
        }(s) : this.imageAttributes.src = n, b(this.defaultTransforms) ? this.appliedImageTransforms = It(this.defaultTransforms()) : y(this.defaultTransforms) ? this.appliedImageTransforms = It(this.defaultTransforms) : this.appliedImageTransforms = function (t) {
          var e = It({});
          if (t) switch (t) {
            case 2:
              e.flip.horizontal = !0;
              break;
            case 3:
              e.rotate = -180;
              break;
            case 4:
              e.flip.vertical = !0;
              break;
            case 5:
              e.rotate = 90, e.flip.vertical = !0;
              break;
            case 6:
              e.rotate = 90;
              break;
            case 7:
              e.rotate = 90, e.flip.horizontal = !0;
              break;
            case 8:
              e.rotate = -90;
          }
          return e;
        }(o), this.defaultImageTransforms = i(i({}, this.appliedImageTransforms), {}, {
          flip: i({}, this.appliedImageTransforms.flip)
        }), this.$nextTick(function () {
          var t = e.$refs.image;
          t && t.complete && (!function (t) {
            return Boolean(t.naturalWidth);
          }(t) ? e.onFailLoadImage() : e.onSuccessLoadImage());
        });
      },
      onResizeEnd: function onResizeEnd() {
        this.runAutoZoom("resize", {
          transitions: !0
        });
      },
      onMoveEnd: function onMoveEnd() {
        this.runAutoZoom("move", {
          transitions: !0
        });
      },
      onMove: function onMove(t) {
        var e = this;
        this.transitionsOptions.enabled || this.awaitRender(function () {
          e.coordinates = e.moveAlgorithm(i(i({}, e.getPublicProperties()), {}, {
            positionRestrictions: yt(e.positionRestrictions, e.visibleArea),
            coordinates: e.coordinates,
            event: e.normalizeEvent(t)
          })), e.onChange();
        });
      },
      onResize: function onResize(t) {
        var e = this;
        this.transitionsOptions.enabled || this.stencilSize && !this.autoZoom || this.awaitRender(function () {
          var n = e.sizeRestrictions,
            s = Math.min(e.coordinates.width, e.coordinates.height, 20 * e.coefficient);
          e.coordinates = e.resizeAlgorithm(i(i({}, e.getPublicProperties()), {}, {
            positionRestrictions: yt(e.positionRestrictions, e.visibleArea),
            sizeRestrictions: {
              maxWidth: Math.min(n.maxWidth, e.visibleArea.width),
              maxHeight: Math.min(n.maxHeight, e.visibleArea.height),
              minWidth: Math.max(n.minWidth, s),
              minHeight: Math.max(n.minHeight, s)
            },
            event: e.normalizeEvent(t)
          })), e.onChange(), e.ticking = !1;
        });
      },
      onManipulateImage: function onManipulateImage(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
        if (!this.transitionsOptions.enabled) {
          var n = e.transitions,
            s = void 0 !== n && n,
            o = e.normalize,
            r = void 0 === o || o;
          s && this.enableTransitions();
          var a = zt(i(i({}, this.getPublicProperties()), {}, {
              event: r ? this.normalizeEvent(t) : t,
              getAreaRestrictions: this.getAreaRestrictions,
              imageRestriction: this.imageRestriction,
              adjustStencil: !this.stencilSize && this.settings.resizeImage.adjustStencil
            })),
            h = a.visibleArea,
            c = a.coordinates;
          this.visibleArea = h, this.coordinates = c, this.runAutoZoom("manipulateImage"), this.onChange(), s && this.debouncedDisableTransitions();
        }
      },
      onPropsChange: function onPropsChange() {
        this.coordinates = this.applyTransform(this.coordinates, !0), this.onChange(!1);
      },
      getAreaRestrictions: function getAreaRestrictions() {
        var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
          e = t.visibleArea,
          i = t.type,
          n = void 0 === i ? "move" : i;
        return this.areaRestrictionsAlgorithm({
          boundaries: this.boundaries,
          imageSize: this.imageSize,
          imageRestriction: this.imageRestriction,
          visibleArea: e,
          type: n
        });
      },
      getAspectRatio: function getAspectRatio(t) {
        var e,
          i,
          n = this.stencilProps,
          s = n.aspectRatio,
          o = n.minAspectRatio,
          r = n.maxAspectRatio;
        if (this.$refs.stencil && this.$refs.stencil.aspectRatios) {
          var a = this.$refs.stencil.aspectRatios();
          e = a.minimum, i = a.maximum;
        }
        if (w(e) && (e = w(s) ? o : s), w(i) && (i = w(s) ? r : s), !t && (w(e) || w(i))) {
          var h = this.getStencilSize(),
            c = h ? Q(h) : null;
          w(e) && (e = A(c) ? c : void 0), w(i) && (i = A(c) ? c : void 0);
        }
        return {
          minimum: e,
          maximum: i
        };
      },
      getStencilSize: function getStencilSize() {
        if (this.stencilSize) return t = {
          currentStencilSize: {
            width: this.stencilCoordinates.width,
            height: this.stencilCoordinates.height
          },
          stencilSize: this.stencilSize,
          boundaries: this.boundaries,
          coefficient: this.coefficient,
          coordinates: this.coordinates,
          aspectRatio: this.getAspectRatio(!0)
        }, e = t.boundaries, i = t.stencilSize, n = t.aspectRatio, tt(Q(s = b(i) ? i({
          boundaries: e,
          aspectRatio: n
        }) : i), n) && (s = ut({
          sizeRestrictions: {
            maxWidth: e.width,
            maxHeight: e.height,
            minWidth: 0,
            minHeight: 0
          },
          width: s.width,
          height: s.height,
          aspectRatio: {
            minimum: n.minimum,
            maximum: n.maximum
          }
        })), (s.width > e.width || s.height > e.height) && (s = ut({
          sizeRestrictions: {
            maxWidth: e.width,
            maxHeight: e.height,
            minWidth: 0,
            minHeight: 0
          },
          width: s.width,
          height: s.height,
          aspectRatio: {
            minimum: Q(s),
            maximum: Q(s)
          }
        })), s;
        var t, e, i, n, s;
      },
      getPublicProperties: function getPublicProperties() {
        return {
          coefficient: this.coefficient,
          visibleArea: this.visibleArea,
          coordinates: this.coordinates,
          boundaries: this.boundaries,
          sizeRestrictions: this.sizeRestrictions,
          positionRestrictions: this.positionRestrictions,
          aspectRatio: this.getAspectRatio(),
          imageRestriction: this.imageRestriction
        };
      },
      defaultCoordinates: function defaultCoordinates() {
        return i({}, F);
      },
      flip: function flip(t, e) {
        var n = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
          s = n.transitions,
          o = void 0 === s || s;
        if (!this.transitionsActive) {
          o && this.enableTransitions();
          var r = i({}, this.imageTransforms.flip),
            a = At({
              flip: {
                horizontal: t ? !r.horizontal : r.horizontal,
                vertical: e ? !r.vertical : r.vertical
              },
              previousFlip: r,
              rotate: this.imageTransforms.rotate,
              visibleArea: this.visibleArea,
              coordinates: this.coordinates,
              imageSize: this.imageSize,
              positionRestrictions: this.positionRestrictions,
              sizeRestrictions: this.sizeRestrictions,
              getAreaRestrictions: this.getAreaRestrictions,
              aspectRatio: this.getAspectRatio()
            }),
            h = a.visibleArea,
            c = a.coordinates;
          t && (this.appliedImageTransforms.flip.horizontal = !this.appliedImageTransforms.flip.horizontal), e && (this.appliedImageTransforms.flip.vertical = !this.appliedImageTransforms.flip.vertical), this.visibleArea = h, this.coordinates = c, this.onChange(), o && this.debouncedDisableTransitions();
        }
      },
      rotate: function rotate(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          n = e.transitions,
          s = void 0 === n || n;
        if (!this.transitionsActive) {
          s && this.enableTransitions();
          var o = i({}, this.imageSize);
          this.appliedImageTransforms.rotate += t;
          var r = Rt({
              visibleArea: this.visibleArea,
              coordinates: this.coordinates,
              previousImageSize: o,
              imageSize: this.imageSize,
              angle: t,
              positionRestrictions: this.positionRestrictions,
              sizeRestrictions: this.sizeRestrictions,
              getAreaRestrictions: this.getAreaRestrictions,
              aspectRatio: this.getAspectRatio()
            }),
            a = r.visibleArea,
            h = r.coordinates,
            c = this.processAutoZoom("rotateImage", a, h);
          a = c.visibleArea, h = c.coordinates, this.visibleArea = a, this.coordinates = h, this.onChange(), s && this.debouncedDisableTransitions();
        }
      }
    }
  },
  ne = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        ref: "cropper",
        "class": t.classes.cropper
      }, [i("div", {
        ref: "stretcher",
        "class": t.classes.stretcher
      }), t._v(" "), i("div", {
        "class": t.classes.boundaries,
        style: t.boundariesStyle
      }, [i(t.backgroundWrapperComponent, {
        tag: "component",
        "class": t.classes.cropperWrapper,
        attrs: {
          "wheel-resize": t.settings.resizeImage.wheel,
          "touch-resize": t.settings.resizeImage.touch,
          "touch-move": t.settings.moveImage.touch,
          "mouse-move": t.settings.moveImage.mouse
        },
        on: {
          move: t.onManipulateImage,
          resize: t.onManipulateImage
        }
      }, [i("div", {
        "class": t.classes.background,
        style: t.boundariesStyle
      }), t._v(" "), i("div", {
        "class": t.classes.imageWrapper
      }, [i("img", {
        ref: "image",
        "class": t.classes.image,
        style: t.imageStyle,
        attrs: {
          crossorigin: t.imageAttributes.crossOrigin,
          src: t.imageAttributes.src
        },
        on: {
          mousedown: function mousedown(t) {
            t.preventDefault();
          }
        }
      })]), t._v(" "), i("div", {
        "class": t.classes.foreground,
        style: t.boundariesStyle
      }), t._v(" "), i(t.stencilComponent, t._b({
        directives: [{
          name: "show",
          rawName: "v-show",
          value: t.imageLoaded,
          expression: "imageLoaded"
        }],
        ref: "stencil",
        tag: "component",
        attrs: {
          image: t.image,
          coordinates: t.coordinates,
          "stencil-coordinates": t.stencilCoordinates,
          transitions: t.transitionsOptions
        },
        on: {
          resize: t.onResize,
          "resize-end": t.onResizeEnd,
          move: t.onMove,
          "move-end": t.onMoveEnd
        }
      }, "component", t.stencilProps, !1)), t._v(" "), t.canvas ? i("canvas", {
        ref: "canvas",
        style: {
          display: "none"
        }
      }) : t._e(), t._v(" "), t.canvas ? i("canvas", {
        ref: "sourceCanvas",
        style: {
          display: "none"
        }
      }) : t._e()], 1)], 1)]);
    },
    staticRenderFns: []
  }, undefined, ie, undefined, false, undefined, !1, void 0, void 0, void 0);
vue__WEBPACK_IMPORTED_MODULE_0___default().component("cropper", ne), vue__WEBPACK_IMPORTED_MODULE_0___default().component("rectangle-stencil", Qt), vue__WEBPACK_IMPORTED_MODULE_0___default().component("circle-stencil", Jt), vue__WEBPACK_IMPORTED_MODULE_0___default().component("simple-handler", Et), vue__WEBPACK_IMPORTED_MODULE_0___default().component("simple-line", Ot);


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-47.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-47.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue_advanced_cropper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-advanced-cropper */ "./node_modules/vue-advanced-cropper/dist/index.es.js");
/* harmony import */ var vue_advanced_cropper_dist_style_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-advanced-cropper/dist/style.css */ "./node_modules/vue-advanced-cropper/dist/style.css");


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {
    Cropper: vue_advanced_cropper__WEBPACK_IMPORTED_MODULE_0__.Cropper
  },
  props: {
    width: {
      type: Number,
      required: true
    },
    height: {
      type: Number,
      required: true
    },
    accessToken: {
      type: String,
      required: true
    },
    lang: {
      type: Object,
      required: true
    },
    uploadUrl: {
      type: String,
      required: true
    }
  },
  data: function data() {
    return {
      isDragging: false,
      image: null,
      saveInProgress: false
    };
  },
  computed: {
    stencilSize: function stencilSize() {
      var stencilSize = {};
      stencilSize.width = this.width;
      stencilSize.height = this.height;
      return stencilSize;
    },
    canvas: function canvas() {
      var canvas = {};
      canvas.maxWidth = this.width;
      canvas.maxHeight = this.height;
      return canvas;
    }
  },
  methods: {
    zoomIn: function zoomIn() {
      this.$refs.cropper.zoom(1.2);
    },
    zoomOut: function zoomOut() {
      this.$refs.cropper.zoom(0.8);
    },
    dragover: function dragover(e) {
      e.preventDefault();
      this.isDragging = true;
    },
    dragleave: function dragleave() {
      this.isDragging = false;
    },
    drop: function drop(e) {
      e.preventDefault();
      this.$refs.file.files = e.dataTransfer.files;
      this.onChange();
      this.isDragging = false;
    },
    onChange: function onChange() {
      var _this = this;
      var files = this.$refs.file.files;
      if (files && files[0]) {
        if (this.image && this.image.src) {
          URL.revokeObjectURL(this.image.src);
        }
        var blob = URL.createObjectURL(files[0]);
        var reader = new FileReader();
        reader.onload = function (e) {
          _this.image = {
            src: blob,
            type: files[0].type
          };
        };
        reader.readAsArrayBuffer(files[0]);
      }
    },
    saveAvatar: function saveAvatar() {
      var _this2 = this;
      this.saveInProgress = true;
      var _this$$refs$cropper$g = this.$refs.cropper.getResult(),
        canvas = _this$$refs$cropper$g.canvas;
      if (canvas) {
        var form = new FormData();
        form.append('ccm_token', this.accessToken);
        canvas.toBlob(function (blob) {
          form.append('file', blob);
          // You can use axios, superagent and other libraries instead here
          fetch(_this2.uploadUrl, {
            method: 'POST',
            body: form
          }).then(function () {
            window.location.reload();
          });
        });
      }
    }
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-47.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-47.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "ccm-account-avatar-cropper"
  }, [_c("div", {
    staticClass: "card"
  }, [_c("div", {
    staticClass: "card-header"
  }, [_vm._v(_vm._s(_vm.lang.header))]), _vm._v(" "), _c("div", {
    staticClass: "card-body"
  }, [_vm.image ? [_c("cropper", {
    ref: "cropper",
    attrs: {
      "stencil-props": {
        handlers: {},
        resizable: false
      },
      "stencil-size": _vm.stencilSize,
      canvas: _vm.canvas,
      "image-restriction": "stencil",
      src: _vm.image.src
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "ccm-account-avatar-cropper-controls"
  }, [_c("a", {
    on: {
      click: _vm.zoomIn
    }
  }, [_c("svg", {
    attrs: {
      width: "24px",
      height: "24px",
      viewBox: "0 0 24 24",
      "stroke-width": "1.5",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      color: "#ffffff"
    }
  }, [_c("path", {
    attrs: {
      d: "M8 11h3m3 0h-3m0 0V8m0 3v3M17 17l4 4M3 11a8 8 0 1016 0 8 8 0 00-16 0z",
      stroke: "#ffffff",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round"
    }
  })])]), _vm._v(" "), _c("a", {
    on: {
      click: _vm.zoomOut
    }
  }, [_c("svg", {
    attrs: {
      width: "24px",
      height: "24px",
      viewBox: "0 0 24 24",
      "stroke-width": "1.5",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      color: "#ffffff"
    }
  }, [_c("path", {
    attrs: {
      d: "M17 17l4 4M3 11a8 8 0 1016 0 8 8 0 00-16 0zM8 11h6",
      stroke: "#ffffff",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round"
    }
  })])])]), _vm._v(" "), _vm.image ? _c("div", {
    staticClass: "ccm-account-avatar-cropper-save"
  }, [_c("button", {
    staticClass: "btn btn-secondary float-start",
    on: {
      click: function click($event) {
        _vm.image = null;
      }
    }
  }, [_vm._v(_vm._s(_vm.lang.reset))]), _vm._v(" "), _vm.saveInProgress ? [_c("button", {
    staticClass: "btn btn-primary float-end",
    attrs: {
      disabled: ""
    }
  }, [_c("span", {
    staticClass: "spinner-border spinner-border-sm",
    attrs: {
      role: "status",
      "aria-hidden": "true"
    }
  }), _vm._v("\n                            " + _vm._s(_vm.lang.saveInProgress) + "\n                        ")])] : _c("button", {
    staticClass: "btn btn-primary float-end",
    on: {
      click: _vm.saveAvatar
    }
  }, [_vm._v(_vm._s(_vm.lang.save))])], 2) : _vm._e()] : [_c("div", {
    "class": {
      "ccm-account-avatar-cropper-drop": true,
      "ccm-account-avatar-crop-drop-hover": _vm.isDragging
    },
    on: {
      dragover: _vm.dragover,
      dragleave: _vm.dragleave,
      drop: _vm.drop,
      click: function click($event) {
        return _vm.$refs.file.click();
      }
    }
  }, [_c("input", {
    ref: "file",
    attrs: {
      type: "file",
      accept: "image/*"
    },
    on: {
      change: _vm.onChange
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "ccm-account-avatar-cropper-focus"
  }, [_c("div", {
    staticClass: "ccm-account-avatar-cropper-icon"
  }, [_c("svg", {
    attrs: {
      width: "100%",
      height: "100%",
      "stroke-width": "1.5",
      viewBox: "0 0 24 24",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      color: "#000000"
    }
  }, [_c("path", {
    attrs: {
      d: "M3 20.4V3.6a.6.6 0 01.6-.6h16.8a.6.6 0 01.6.6v16.8a.6.6 0 01-.6.6H3.6a.6.6 0 01-.6-.6z",
      stroke: "#000000",
      "stroke-width": "1.5"
    }
  }), _c("path", {
    attrs: {
      d: "M6 18h12M12 14V6m0 0l3.5 3.5M12 6L8.5 9.5",
      stroke: "#000000",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round"
    }
  })])]), _vm._v(" "), _c("div", {
    staticClass: "ccm-account-avatar-cropper-text"
  }, [_vm._v(_vm._s(_vm.lang.upload))])])]], 2)])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-34.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-34.use[2]!./node_modules/vue-advanced-cropper/dist/style.css":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-34.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-34.use[2]!./node_modules/vue-advanced-cropper/dist/style.css ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../laravel-mix/node_modules/css-loader/dist/runtime/api.js */ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, ".vue-advanced-cropper {\n  text-align: center;\n  position: relative;\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  max-height: 100%;\n  max-width: 100%;\n  direction: ltr;\n}\n\n.vue-advanced-cropper__stretcher {\n  pointer-events: none;\n  position: relative;\n  max-width: 100%;\n  max-height: 100%;\n}\n\n.vue-advanced-cropper__image {\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  position: absolute;\n  transform-origin: center;\n  max-width: none !important;\n}\n\n.vue-advanced-cropper__background, .vue-advanced-cropper__foreground {\n  opacity: 1;\n  background: #000;\n  transform: translate(-50%, -50%);\n  position: absolute;\n  top: 50%;\n  left: 50%;\n}\n\n.vue-advanced-cropper__foreground {\n  opacity: 0.5;\n}\n\n.vue-advanced-cropper__boundaries {\n  opacity: 1;\n  transform: translate(-50%, -50%);\n  position: absolute;\n  left: 50%;\n  top: 50%;\n}\n\n.vue-advanced-cropper__cropper-wrapper {\n  width: 100%;\n  height: 100%;\n}\n\n.vue-advanced-cropper__image-wrapper {\n  overflow: hidden;\n  position: absolute;\n  width: 100%;\n  height: 100%;\n}\n\n.vue-advanced-cropper__stencil-wrapper {\n  position: absolute;\n}\n\n.vue-rectangle-stencil {\n  position: absolute;\n  height: 100%;\n  width: 100%;\n  box-sizing: border-box;\n}\n\n.vue-rectangle-stencil__preview {\n  position: absolute;\n  width: 100%;\n  height: 100%;\n}\n\n.vue-rectangle-stencil--movable {\n  cursor: move;\n}\n\n.vue-preview {\n  overflow: hidden;\n  box-sizing: border-box;\n  position: relative;\n}\n\n.vue-preview--fill {\n  width: 100%;\n  height: 100%;\n  position: absolute;\n}\n\n.vue-preview__wrapper {\n  position: absolute;\n  height: 100%;\n  width: 100%;\n}\n\n.vue-preview__image {\n  pointer-events: none;\n  position: absolute;\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  transform-origin: center;\n  max-width: none !important;\n}\n\n.vue-circle-stencil {\n  position: absolute;\n  height: 100%;\n  width: 100%;\n  box-sizing: content-box;\n  cursor: move;\n}\n\n.vue-circle-stencil__preview {\n  border-radius: 50%;\n  position: absolute;\n  width: 100%;\n  height: 100%;\n}\n\n.vue-circle-stencil--movable {\n  cursor: move;\n}\n\n.vue-simple-handler {\n  display: block;\n  background: #fff;\n  height: 10px;\n  width: 10px;\n}\n\n.vue-line-wrapper {\n  background: 0 0;\n  position: absolute;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n}\n\n.vue-line-wrapper--north, .vue-line-wrapper--south {\n  height: 12px;\n  width: 100%;\n  left: 0;\n  transform: translateY(-50%);\n}\n\n.vue-line-wrapper--north {\n  top: 0;\n  cursor: n-resize;\n}\n\n.vue-line-wrapper--south {\n  top: 100%;\n  cursor: s-resize;\n}\n\n.vue-line-wrapper--east, .vue-line-wrapper--west {\n  width: 12px;\n  height: 100%;\n  transform: translateX(-50%);\n  top: 0;\n}\n\n.vue-line-wrapper--east {\n  left: 100%;\n  cursor: e-resize;\n}\n\n.vue-line-wrapper--west {\n  left: 0;\n  cursor: w-resize;\n}\n\n.vue-line-wrapper--disabled {\n  cursor: auto;\n}\n\n.vue-handler-wrapper {\n  position: absolute;\n  transform: translate(-50%, -50%);\n  width: 30px;\n  height: 30px;\n}\n\n.vue-handler-wrapper__draggable {\n  width: 100%;\n  height: 100%;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n}\n\n.vue-handler-wrapper--west-north {\n  cursor: nw-resize;\n}\n\n.vue-handler-wrapper--north {\n  cursor: n-resize;\n}\n\n.vue-handler-wrapper--east-north {\n  cursor: ne-resize;\n}\n\n.vue-handler-wrapper--east {\n  cursor: e-resize;\n}\n\n.vue-handler-wrapper--east-south {\n  cursor: se-resize;\n}\n\n.vue-handler-wrapper--south {\n  cursor: s-resize;\n}\n\n.vue-handler-wrapper--west-south {\n  cursor: sw-resize;\n}\n\n.vue-handler-wrapper--west {\n  cursor: w-resize;\n}\n\n.vue-handler-wrapper--disabled {\n  cursor: auto;\n}\n\n.vue-draggable-area {\n  position: relative;\n}\n\n.vue-bounding-box {\n  position: relative;\n  height: 100%;\n  width: 100%;\n}\n\n.vue-bounding-box__handler {\n  position: absolute;\n}\n\n.vue-bounding-box__handler--west-north {\n  left: 0;\n  top: 0;\n}\n\n.vue-bounding-box__handler--north {\n  left: 50%;\n  top: 0;\n}\n\n.vue-bounding-box__handler--east-north {\n  left: 100%;\n  top: 0;\n}\n\n.vue-bounding-box__handler--east {\n  left: 100%;\n  top: 50%;\n}\n\n.vue-bounding-box__handler--east-south {\n  left: 100%;\n  top: 100%;\n}\n\n.vue-bounding-box__handler--south {\n  left: 50%;\n  top: 100%;\n}\n\n.vue-bounding-box__handler--west-south {\n  left: 0;\n  top: 100%;\n}\n\n.vue-bounding-box__handler--west {\n  left: 0;\n  top: 50%;\n}\n\n.vue-preview-result {\n  overflow: hidden;\n  box-sizing: border-box;\n  position: absolute;\n  height: 100%;\n  width: 100%;\n}\n\n.vue-preview-result__wrapper {\n  position: absolute;\n}\n\n.vue-preview-result__image {\n  pointer-events: none;\n  position: relative;\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  transform-origin: center;\n  max-width: none !important;\n}\n\n.vue-simple-line {\n  background: 0 0;\n  transition: border 0.5s;\n  border-color: rgba(255, 255, 255, 0.3);\n  border-width: 0;\n  border-style: solid;\n}\n\n.vue-simple-line--north, .vue-simple-line--south {\n  height: 0;\n  width: 100%;\n}\n\n.vue-simple-line--east, .vue-simple-line--west {\n  height: 100%;\n  width: 0;\n}\n\n.vue-simple-line--east {\n  border-right-width: 1px;\n}\n\n.vue-simple-line--west {\n  border-left-width: 1px;\n}\n\n.vue-simple-line--south {\n  border-bottom-width: 1px;\n}\n\n.vue-simple-line--north {\n  border-top-width: 1px;\n}\n\n.vue-simple-line--hover {\n  opacity: 1;\n  border-color: #fff;\n}", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/vue-advanced-cropper/dist/style.css":
/*!**********************************************************!*\
  !*** ./node_modules/vue-advanced-cropper/dist/style.css ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_34_use_1_postcss_loader_dist_cjs_js_clonedRuleSet_34_use_2_style_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-34.use[1]!../../postcss-loader/dist/cjs.js??clonedRuleSet-34.use[2]!./style.css */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-34.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-34.use[2]!./node_modules/vue-advanced-cropper/dist/style.css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_34_use_1_postcss_loader_dist_cjs_js_clonedRuleSet_34_use_2_style_css__WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_34_use_1_postcss_loader_dist_cjs_js_clonedRuleSet_34_use_2_style_css__WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {



var isOldIE = function isOldIE() {
  var memo;
  return function memorize() {
    if (typeof memo === 'undefined') {
      // Test for IE <= 9 as proposed by Browserhacks
      // @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
      // Tests for existence of standard globals is to allow style-loader
      // to operate correctly into non-standard environments
      // @see https://github.com/webpack-contrib/style-loader/issues/177
      memo = Boolean(window && document && document.all && !window.atob);
    }

    return memo;
  };
}();

var getTarget = function getTarget() {
  var memo = {};
  return function memorize(target) {
    if (typeof memo[target] === 'undefined') {
      var styleTarget = document.querySelector(target); // Special case to return head of iframe instead of iframe itself

      if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
        try {
          // This will throw an exception if access to iframe is blocked
          // due to cross-origin restrictions
          styleTarget = styleTarget.contentDocument.head;
        } catch (e) {
          // istanbul ignore next
          styleTarget = null;
        }
      }

      memo[target] = styleTarget;
    }

    return memo[target];
  };
}();

var stylesInDom = [];

function getIndexByIdentifier(identifier) {
  var result = -1;

  for (var i = 0; i < stylesInDom.length; i++) {
    if (stylesInDom[i].identifier === identifier) {
      result = i;
      break;
    }
  }

  return result;
}

function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];

  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var index = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3]
    };

    if (index !== -1) {
      stylesInDom[index].references++;
      stylesInDom[index].updater(obj);
    } else {
      stylesInDom.push({
        identifier: identifier,
        updater: addStyle(obj, options),
        references: 1
      });
    }

    identifiers.push(identifier);
  }

  return identifiers;
}

function insertStyleElement(options) {
  var style = document.createElement('style');
  var attributes = options.attributes || {};

  if (typeof attributes.nonce === 'undefined') {
    var nonce =  true ? __webpack_require__.nc : 0;

    if (nonce) {
      attributes.nonce = nonce;
    }
  }

  Object.keys(attributes).forEach(function (key) {
    style.setAttribute(key, attributes[key]);
  });

  if (typeof options.insert === 'function') {
    options.insert(style);
  } else {
    var target = getTarget(options.insert || 'head');

    if (!target) {
      throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
    }

    target.appendChild(style);
  }

  return style;
}

function removeStyleElement(style) {
  // istanbul ignore if
  if (style.parentNode === null) {
    return false;
  }

  style.parentNode.removeChild(style);
}
/* istanbul ignore next  */


var replaceText = function replaceText() {
  var textStore = [];
  return function replace(index, replacement) {
    textStore[index] = replacement;
    return textStore.filter(Boolean).join('\n');
  };
}();

function applyToSingletonTag(style, index, remove, obj) {
  var css = remove ? '' : obj.media ? "@media ".concat(obj.media, " {").concat(obj.css, "}") : obj.css; // For old IE

  /* istanbul ignore if  */

  if (style.styleSheet) {
    style.styleSheet.cssText = replaceText(index, css);
  } else {
    var cssNode = document.createTextNode(css);
    var childNodes = style.childNodes;

    if (childNodes[index]) {
      style.removeChild(childNodes[index]);
    }

    if (childNodes.length) {
      style.insertBefore(cssNode, childNodes[index]);
    } else {
      style.appendChild(cssNode);
    }
  }
}

function applyToTag(style, options, obj) {
  var css = obj.css;
  var media = obj.media;
  var sourceMap = obj.sourceMap;

  if (media) {
    style.setAttribute('media', media);
  } else {
    style.removeAttribute('media');
  }

  if (sourceMap && typeof btoa !== 'undefined') {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  } // For old IE

  /* istanbul ignore if  */


  if (style.styleSheet) {
    style.styleSheet.cssText = css;
  } else {
    while (style.firstChild) {
      style.removeChild(style.firstChild);
    }

    style.appendChild(document.createTextNode(css));
  }
}

var singleton = null;
var singletonCounter = 0;

function addStyle(obj, options) {
  var style;
  var update;
  var remove;

  if (options.singleton) {
    var styleIndex = singletonCounter++;
    style = singleton || (singleton = insertStyleElement(options));
    update = applyToSingletonTag.bind(null, style, styleIndex, false);
    remove = applyToSingletonTag.bind(null, style, styleIndex, true);
  } else {
    style = insertStyleElement(options);
    update = applyToTag.bind(null, style, options);

    remove = function remove() {
      removeStyleElement(style);
    };
  }

  update(obj);
  return function updateStyle(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) {
        return;
      }

      update(obj = newObj);
    } else {
      remove();
    }
  };
}

module.exports = function (list, options) {
  options = options || {}; // Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
  // tags it will allow on a page

  if (!options.singleton && typeof options.singleton !== 'boolean') {
    options.singleton = isOldIE();
  }

  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];

    if (Object.prototype.toString.call(newList) !== '[object Array]') {
      return;
    }

    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDom[index].references--;
    }

    var newLastIdentifiers = modulesToDom(newList, options);

    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];

      var _index = getIndexByIdentifier(_identifier);

      if (stylesInDom[_index].references === 0) {
        stylesInDom[_index].updater();

        stylesInDom.splice(_index, 1);
      }
    }

    lastIdentifiers = newLastIdentifiers;
  };
};

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue ***!
  \***************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AvatarCropper.vue?vue&type=template&id=753b3f13& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&");
/* harmony import */ var _AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AvatarCropper.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.render,
  _AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_lib_index_js_clonedRuleSet_47_use_0_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../babel-loader/lib/index.js??clonedRuleSet-47.use[0]!../../../../../../../vue-loader/lib/index.js??vue-loader-options!./AvatarCropper.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-47.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_babel_loader_lib_index_js_clonedRuleSet_47_use_0_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&":
/*!**********************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13& ***!
  \**********************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _babel_loader_lib_index_js_clonedRuleSet_47_use_0_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _babel_loader_lib_index_js_clonedRuleSet_47_use_0_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _babel_loader_lib_index_js_clonedRuleSet_47_use_0_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../babel-loader/lib/index.js??clonedRuleSet-47.use[0]!../../../../../../../vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../vue-loader/lib/index.js??vue-loader-options!./AvatarCropper.vue?vue&type=template&id=753b3f13& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-47.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ normalizeComponent)
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent(
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */,
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options =
    typeof scriptExports === 'function' ? scriptExports.options : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) {
    // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () {
          injectStyles.call(
            this,
            (options.functional ? this.parent : this).$root.$options.shadowRoot
          )
        }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection(h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing ? [].concat(existing, hook) : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

module.exports = Vue;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/nonce */
/******/ 	(() => {
/******/ 		__webpack_require__.nc = undefined;
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend.js ***!
  \*************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_components_AvatarCropper_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/components/AvatarCropper.vue */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue");
/* eslint-disable no-new */


$(function () {
  window.Concrete.Vue.createContext('frontend', {
    AvatarCropper: _frontend_components_AvatarCropper_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  });
  if (document.querySelectorAll('[data-view=account]').length) {
    Concrete.Vue.activateContext('frontend', function (Vue, config) {
      new Vue({
        el: '[data-view=account]',
        components: config.components
      });
    });
  }
});
})();

/******/ })()
;