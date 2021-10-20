/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/blocks/accordion/accordion.js":
/*!**********************************************!*\
  !*** ./assets/blocks/accordion/accordion.js ***!
  \**********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var ckeditor4_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor4-vue */ "./node_modules/ckeditor4-vue/dist/ckeditor.js");
/* harmony import */ var ckeditor4_vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(ckeditor4_vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuedraggable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuedraggable */ "./node_modules/vuedraggable/dist/vuedraggable.umd.js");
/* harmony import */ var vuedraggable__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vuedraggable__WEBPACK_IMPORTED_MODULE_1__);


window.Concrete.Vue.createContext('accordion', {
  CKEditor: ckeditor4_vue__WEBPACK_IMPORTED_MODULE_0___default.a,
  draggable: vuedraggable__WEBPACK_IMPORTED_MODULE_1___default.a
});

/***/ }),

/***/ "./node_modules/ckeditor4-vue/dist/ckeditor.js":
/*!*****************************************************!*\
  !*** ./node_modules/ckeditor4-vue/dist/ckeditor.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*! For license information please see ckeditor.js.LICENSE.txt */

/*!*
* @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
* For licensing, see LICENSE.md.
*/
!function (t, n) {
  "object" == ( false ? undefined : _typeof(exports)) && "object" == ( false ? undefined : _typeof(module)) ? module.exports = n() :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (n),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(window, function () {
  return function (t) {
    var n = {};

    function e(r) {
      if (n[r]) return n[r].exports;
      var o = n[r] = {
        i: r,
        l: !1,
        exports: {}
      };
      return t[r].call(o.exports, o, o.exports, e), o.l = !0, o.exports;
    }

    return e.m = t, e.c = n, e.d = function (t, n, r) {
      e.o(t, n) || Object.defineProperty(t, n, {
        enumerable: !0,
        get: r
      });
    }, e.r = function (t) {
      "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {
        value: "Module"
      }), Object.defineProperty(t, "__esModule", {
        value: !0
      });
    }, e.t = function (t, n) {
      if (1 & n && (t = e(t)), 8 & n) return t;
      if (4 & n && "object" == _typeof(t) && t && t.__esModule) return t;
      var r = Object.create(null);
      if (e.r(r), Object.defineProperty(r, "default", {
        enumerable: !0,
        value: t
      }), 2 & n && "string" != typeof t) for (var o in t) {
        e.d(r, o, function (n) {
          return t[n];
        }.bind(null, o));
      }
      return r;
    }, e.n = function (t) {
      var n = t && t.__esModule ? function () {
        return t["default"];
      } : function () {
        return t;
      };
      return e.d(n, "a", n), n;
    }, e.o = function (t, n) {
      return Object.prototype.hasOwnProperty.call(t, n);
    }, e.p = "", e(e.s = 1);
  }([function (t, n) {
    /*!*
     * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
     * For licensing, see LICENSE.md.
     */
    !function (t, n) {
      for (var e in n) {
        t[e] = n[e];
      }
    }(n, function (t) {
      var n = {};

      function e(r) {
        if (n[r]) return n[r].exports;
        var o = n[r] = {
          i: r,
          l: !1,
          exports: {}
        };
        return t[r].call(o.exports, o, o.exports, e), o.l = !0, o.exports;
      }

      return e.m = t, e.c = n, e.d = function (t, n, r) {
        e.o(t, n) || Object.defineProperty(t, n, {
          enumerable: !0,
          get: r
        });
      }, e.r = function (t) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {
          value: "Module"
        }), Object.defineProperty(t, "__esModule", {
          value: !0
        });
      }, e.t = function (t, n) {
        if (1 & n && (t = e(t)), 8 & n) return t;
        if (4 & n && "object" == _typeof(t) && t && t.__esModule) return t;
        var r = Object.create(null);
        if (e.r(r), Object.defineProperty(r, "default", {
          enumerable: !0,
          value: t
        }), 2 & n && "string" != typeof t) for (var o in t) {
          e.d(r, o, function (n) {
            return t[n];
          }.bind(null, o));
        }
        return r;
      }, e.n = function (t) {
        var n = t && t.__esModule ? function () {
          return t["default"];
        } : function () {
          return t;
        };
        return e.d(n, "a", n), n;
      }, e.o = function (t, n) {
        return Object.prototype.hasOwnProperty.call(t, n);
      }, e.p = "", e(e.s = 83);
    }([function (t, n, e) {
      (function (n) {
        var e = function e(t) {
          return t && t.Math == Math && t;
        };

        t.exports = e("object" == (typeof globalThis === "undefined" ? "undefined" : _typeof(globalThis)) && globalThis) || e("object" == (typeof window === "undefined" ? "undefined" : _typeof(window)) && window) || e("object" == (typeof self === "undefined" ? "undefined" : _typeof(self)) && self) || e("object" == _typeof(n) && n) || Function("return this")();
      }).call(this, e(45));
    }, function (t, n, e) {
      var r = e(0),
          o = e(22),
          i = e(4),
          c = e(27),
          u = e(28),
          a = e(46),
          f = o("wks"),
          s = r.Symbol,
          l = a ? s : s && s.withoutSetter || c;

      t.exports = function (t) {
        return i(f, t) || (u && i(s, t) ? f[t] = s[t] : f[t] = l("Symbol." + t)), f[t];
      };
    }, function (t, n) {
      t.exports = function (t) {
        try {
          return !!t();
        } catch (t) {
          return !0;
        }
      };
    }, function (t, n) {
      t.exports = function (t) {
        return "object" == _typeof(t) ? null !== t : "function" == typeof t;
      };
    }, function (t, n) {
      var e = {}.hasOwnProperty;

      t.exports = function (t, n) {
        return e.call(t, n);
      };
    }, function (t, n, e) {
      var r = e(3);

      t.exports = function (t) {
        if (!r(t)) throw TypeError(String(t) + " is not an object");
        return t;
      };
    }, function (t, n) {
      var e = {}.toString;

      t.exports = function (t) {
        return e.call(t).slice(8, -1);
      };
    }, function (t, n, e) {
      var r = e(2);
      t.exports = !r(function () {
        return 7 != Object.defineProperty({}, 1, {
          get: function get() {
            return 7;
          }
        })[1];
      });
    }, function (t, n, e) {
      var r = e(7),
          o = e(25),
          i = e(5),
          c = e(15),
          u = Object.defineProperty;
      n.f = r ? u : function (t, n, e) {
        if (i(t), n = c(n, !0), i(e), o) try {
          return u(t, n, e);
        } catch (t) {}
        if ("get" in e || "set" in e) throw TypeError("Accessors not supported");
        return "value" in e && (t[n] = e.value), t;
      };
    }, function (t, n, e) {
      var r = e(55),
          o = e(0),
          i = function i(t) {
        return "function" == typeof t ? t : void 0;
      };

      t.exports = function (t, n) {
        return arguments.length < 2 ? i(r[t]) || i(o[t]) : r[t] && r[t][n] || o[t] && o[t][n];
      };
    }, function (t, n, e) {
      var r = e(7),
          o = e(8),
          i = e(16);
      t.exports = r ? function (t, n, e) {
        return o.f(t, n, i(1, e));
      } : function (t, n, e) {
        return t[n] = e, t;
      };
    }, function (t, n, e) {
      var r = e(0),
          o = e(10),
          i = e(4),
          c = e(14),
          u = e(17),
          a = e(29),
          f = a.get,
          s = a.enforce,
          l = String(String).split("String");
      (t.exports = function (t, n, e, u) {
        var a = !!u && !!u.unsafe,
            f = !!u && !!u.enumerable,
            p = !!u && !!u.noTargetGet;
        "function" == typeof e && ("string" != typeof n || i(e, "name") || o(e, "name", n), s(e).source = l.join("string" == typeof n ? n : "")), t !== r ? (a ? !p && t[n] && (f = !0) : delete t[n], f ? t[n] = e : o(t, n, e)) : f ? t[n] = e : c(n, e);
      })(Function.prototype, "toString", function () {
        return "function" == typeof this && f(this).source || u(this);
      });
    }, function (t, n) {
      t.exports = function (t) {
        if ("function" != typeof t) throw TypeError(String(t) + " is not a function");
        return t;
      };
    }, function (t, n, e) {
      var r = {};
      r[e(1)("toStringTag")] = "z", t.exports = "[object z]" === String(r);
    }, function (t, n, e) {
      var r = e(0),
          o = e(10);

      t.exports = function (t, n) {
        try {
          o(r, t, n);
        } catch (e) {
          r[t] = n;
        }

        return n;
      };
    }, function (t, n, e) {
      var r = e(3);

      t.exports = function (t, n) {
        if (!r(t)) return t;
        var e, o;
        if (n && "function" == typeof (e = t.toString) && !r(o = e.call(t))) return o;
        if ("function" == typeof (e = t.valueOf) && !r(o = e.call(t))) return o;
        if (!n && "function" == typeof (e = t.toString) && !r(o = e.call(t))) return o;
        throw TypeError("Can't convert object to primitive value");
      };
    }, function (t, n) {
      t.exports = function (t, n) {
        return {
          enumerable: !(1 & t),
          configurable: !(2 & t),
          writable: !(4 & t),
          value: n
        };
      };
    }, function (t, n, e) {
      var r = e(24),
          o = Function.toString;
      "function" != typeof r.inspectSource && (r.inspectSource = function (t) {
        return o.call(t);
      }), t.exports = r.inspectSource;
    }, function (t, n, e) {
      var r = e(7),
          o = e(51),
          i = e(16),
          c = e(19),
          u = e(15),
          a = e(4),
          f = e(25),
          s = Object.getOwnPropertyDescriptor;
      n.f = r ? s : function (t, n) {
        if (t = c(t), n = u(n, !0), f) try {
          return s(t, n);
        } catch (t) {}
        if (a(t, n)) return i(!o.f.call(t, n), t[n]);
      };
    }, function (t, n, e) {
      var r = e(52),
          o = e(33);

      t.exports = function (t) {
        return r(o(t));
      };
    }, function (t, n, e) {
      var r = e(34),
          o = Math.min;

      t.exports = function (t) {
        return t > 0 ? o(r(t), 9007199254740991) : 0;
      };
    }, function (t, n, e) {
      var r,
          o,
          i = e(0),
          c = e(40),
          u = i.process,
          a = u && u.versions,
          f = a && a.v8;
      f ? o = (r = f.split("."))[0] + r[1] : c && (!(r = c.match(/Edge\/(\d+)/)) || r[1] >= 74) && (r = c.match(/Chrome\/(\d+)/)) && (o = r[1]), t.exports = o && +o;
    }, function (t, n, e) {
      var r = e(23),
          o = e(24);
      (t.exports = function (t, n) {
        return o[t] || (o[t] = void 0 !== n ? n : {});
      })("versions", []).push({
        version: "3.6.5",
        mode: r ? "pure" : "global",
        copyright: "© 2020 Denis Pushkarev (zloirock.ru)"
      });
    }, function (t, n) {
      t.exports = !1;
    }, function (t, n, e) {
      var r = e(0),
          o = e(14),
          i = r["__core-js_shared__"] || o("__core-js_shared__", {});
      t.exports = i;
    }, function (t, n, e) {
      var r = e(7),
          o = e(2),
          i = e(26);
      t.exports = !r && !o(function () {
        return 7 != Object.defineProperty(i("div"), "a", {
          get: function get() {
            return 7;
          }
        }).a;
      });
    }, function (t, n, e) {
      var r = e(0),
          o = e(3),
          i = r.document,
          c = o(i) && o(i.createElement);

      t.exports = function (t) {
        return c ? i.createElement(t) : {};
      };
    }, function (t, n) {
      var e = 0,
          r = Math.random();

      t.exports = function (t) {
        return "Symbol(" + String(void 0 === t ? "" : t) + ")_" + (++e + r).toString(36);
      };
    }, function (t, n, e) {
      var r = e(2);
      t.exports = !!Object.getOwnPropertySymbols && !r(function () {
        return !String(Symbol());
      });
    }, function (t, n, e) {
      var r,
          o,
          i,
          c = e(47),
          u = e(0),
          a = e(3),
          f = e(10),
          s = e(4),
          l = e(48),
          p = e(30),
          d = u.WeakMap;

      if (c) {
        var v = new d(),
            h = v.get,
            y = v.has,
            m = v.set;
        r = function r(t, n) {
          return m.call(v, t, n), n;
        }, o = function o(t) {
          return h.call(v, t) || {};
        }, i = function i(t) {
          return y.call(v, t);
        };
      } else {
        var g = l("state");
        p[g] = !0, r = function r(t, n) {
          return f(t, g, n), n;
        }, o = function o(t) {
          return s(t, g) ? t[g] : {};
        }, i = function i(t) {
          return s(t, g);
        };
      }

      t.exports = {
        set: r,
        get: o,
        has: i,
        enforce: function enforce(t) {
          return i(t) ? o(t) : r(t, {});
        },
        getterFor: function getterFor(t) {
          return function (n) {
            var e;
            if (!a(n) || (e = o(n)).type !== t) throw TypeError("Incompatible receiver, " + t + " required");
            return e;
          };
        }
      };
    }, function (t, n) {
      t.exports = {};
    }, function (t, n, e) {
      var r = e(13),
          o = e(6),
          i = e(1)("toStringTag"),
          c = "Arguments" == o(function () {
        return arguments;
      }());
      t.exports = r ? o : function (t) {
        var n, e, r;
        return void 0 === t ? "Undefined" : null === t ? "Null" : "string" == typeof (e = function (t, n) {
          try {
            return t[n];
          } catch (t) {}
        }(n = Object(t), i)) ? e : c ? o(n) : "Object" == (r = o(n)) && "function" == typeof n.callee ? "Arguments" : r;
      };
    }, function (t, n, e) {
      var r = e(0),
          o = e(18).f,
          i = e(10),
          c = e(11),
          u = e(14),
          a = e(53),
          f = e(35);

      t.exports = function (t, n) {
        var e,
            s,
            l,
            p,
            d,
            v = t.target,
            h = t.global,
            y = t.stat;
        if (e = h ? r : y ? r[v] || u(v, {}) : (r[v] || {}).prototype) for (s in n) {
          if (p = n[s], l = t.noTargetGet ? (d = o(e, s)) && d.value : e[s], !f(h ? s : v + (y ? "." : "#") + s, t.forced) && void 0 !== l) {
            if (_typeof(p) == _typeof(l)) continue;
            a(p, l);
          }

          (t.sham || l && l.sham) && i(p, "sham", !0), c(e, s, p, t);
        }
      };
    }, function (t, n) {
      t.exports = function (t) {
        if (null == t) throw TypeError("Can't call method on " + t);
        return t;
      };
    }, function (t, n) {
      var e = Math.ceil,
          r = Math.floor;

      t.exports = function (t) {
        return isNaN(t = +t) ? 0 : (t > 0 ? r : e)(t);
      };
    }, function (t, n, e) {
      var r = e(2),
          o = /#|\.prototype\./,
          i = function i(t, n) {
        var e = u[c(t)];
        return e == f || e != a && ("function" == typeof n ? r(n) : !!n);
      },
          c = i.normalize = function (t) {
        return String(t).replace(o, ".").toLowerCase();
      },
          u = i.data = {},
          a = i.NATIVE = "N",
          f = i.POLYFILL = "P";

      t.exports = i;
    }, function (t, n) {
      t.exports = {};
    }, function (t, n, e) {
      var r = e(12);

      t.exports = function (t, n, e) {
        if (r(t), void 0 === n) return t;

        switch (e) {
          case 0:
            return function () {
              return t.call(n);
            };

          case 1:
            return function (e) {
              return t.call(n, e);
            };

          case 2:
            return function (e, r) {
              return t.call(n, e, r);
            };

          case 3:
            return function (e, r, o) {
              return t.call(n, e, r, o);
            };
        }

        return function () {
          return t.apply(n, arguments);
        };
      };
    }, function (t, n, e) {
      var r,
          o,
          i,
          c = e(0),
          u = e(2),
          a = e(6),
          f = e(37),
          s = e(73),
          l = e(26),
          p = e(39),
          d = c.location,
          v = c.setImmediate,
          h = c.clearImmediate,
          y = c.process,
          m = c.MessageChannel,
          g = c.Dispatch,
          x = 0,
          b = {},
          j = function j(t) {
        if (b.hasOwnProperty(t)) {
          var n = b[t];
          delete b[t], n();
        }
      },
          w = function w(t) {
        return function () {
          j(t);
        };
      },
          O = function O(t) {
        j(t.data);
      },
          S = function S(t) {
        c.postMessage(t + "", d.protocol + "//" + d.host);
      };

      v && h || (v = function v(t) {
        for (var n = [], e = 1; arguments.length > e;) {
          n.push(arguments[e++]);
        }

        return b[++x] = function () {
          ("function" == typeof t ? t : Function(t)).apply(void 0, n);
        }, r(x), x;
      }, h = function h(t) {
        delete b[t];
      }, "process" == a(y) ? r = function r(t) {
        y.nextTick(w(t));
      } : g && g.now ? r = function r(t) {
        g.now(w(t));
      } : m && !p ? (i = (o = new m()).port2, o.port1.onmessage = O, r = f(i.postMessage, i, 1)) : !c.addEventListener || "function" != typeof postMessage || c.importScripts || u(S) || "file:" === d.protocol ? r = "onreadystatechange" in l("script") ? function (t) {
        s.appendChild(l("script")).onreadystatechange = function () {
          s.removeChild(this), j(t);
        };
      } : function (t) {
        setTimeout(w(t), 0);
      } : (r = S, c.addEventListener("message", O, !1))), t.exports = {
        set: v,
        clear: h
      };
    }, function (t, n, e) {
      var r = e(40);
      t.exports = /(iphone|ipod|ipad).*applewebkit/i.test(r);
    }, function (t, n, e) {
      var r = e(9);
      t.exports = r("navigator", "userAgent") || "";
    }, function (t, n, e) {
      "use strict";

      var r = e(12),
          o = function o(t) {
        var n, e;
        this.promise = new t(function (t, r) {
          if (void 0 !== n || void 0 !== e) throw TypeError("Bad Promise constructor");
          n = t, e = r;
        }), this.resolve = r(n), this.reject = r(e);
      };

      t.exports.f = function (t) {
        return new o(t);
      };
    }, function (t, n, e) {
      var r = e(6);

      t.exports = Array.isArray || function (t) {
        return "Array" == r(t);
      };
    }, function (t, n) {
      function e(t, n) {
        t.onload = function () {
          this.onerror = this.onload = null, n(null, t);
        }, t.onerror = function () {
          this.onerror = this.onload = null, n(new Error("Failed to load " + this.src), t);
        };
      }

      function r(t, n) {
        t.onreadystatechange = function () {
          "complete" != this.readyState && "loaded" != this.readyState || (this.onreadystatechange = null, n(null, t));
        };
      }

      t.exports = function (t, n, o) {
        var i = document.head || document.getElementsByTagName("head")[0],
            c = document.createElement("script");
        "function" == typeof n && (o = n, n = {}), n = n || {}, o = o || function () {}, c.type = n.type || "text/javascript", c.charset = n.charset || "utf8", c.async = !("async" in n) || !!n.async, c.src = t, n.attrs && function (t, n) {
          for (var e in n) {
            t.setAttribute(e, n[e]);
          }
        }(c, n.attrs), n.text && (c.text = "" + n.text), ("onload" in c ? e : r)(c, o), c.onload || e(c, o), i.appendChild(c);
      };
    }, function (t, n, e) {
      var r = e(13),
          o = e(11),
          i = e(49);
      r || o(Object.prototype, "toString", i, {
        unsafe: !0
      });
    }, function (t, n) {
      var e;

      e = function () {
        return this;
      }();

      try {
        e = e || new Function("return this")();
      } catch (t) {
        "object" == (typeof window === "undefined" ? "undefined" : _typeof(window)) && (e = window);
      }

      t.exports = e;
    }, function (t, n, e) {
      var r = e(28);
      t.exports = r && !Symbol.sham && "symbol" == _typeof(Symbol.iterator);
    }, function (t, n, e) {
      var r = e(0),
          o = e(17),
          i = r.WeakMap;
      t.exports = "function" == typeof i && /native code/.test(o(i));
    }, function (t, n, e) {
      var r = e(22),
          o = e(27),
          i = r("keys");

      t.exports = function (t) {
        return i[t] || (i[t] = o(t));
      };
    }, function (t, n, e) {
      "use strict";

      var r = e(13),
          o = e(31);
      t.exports = r ? {}.toString : function () {
        return "[object " + o(this) + "]";
      };
    }, function (t, n, e) {
      "use strict";

      var r,
          o,
          i,
          c,
          u = e(32),
          a = e(23),
          f = e(0),
          s = e(9),
          l = e(62),
          p = e(11),
          d = e(63),
          v = e(64),
          h = e(65),
          y = e(3),
          m = e(12),
          g = e(66),
          x = e(6),
          b = e(17),
          j = e(67),
          w = e(71),
          O = e(72),
          S = e(38).set,
          E = e(74),
          T = e(75),
          P = e(76),
          M = e(41),
          _ = e(77),
          k = e(29),
          C = e(35),
          D = e(1),
          A = e(21),
          I = D("species"),
          N = "Promise",
          $ = k.get,
          R = k.set,
          K = k.getterFor(N),
          _L = l,
          F = f.TypeError,
          U = f.document,
          z = f.process,
          B = s("fetch"),
          W = M.f,
          q = W,
          G = "process" == x(z),
          H = !!(U && U.createEvent && f.dispatchEvent),
          V = C(N, function () {
        if (b(_L) === String(_L)) {
          if (66 === A) return !0;
          if (!G && "function" != typeof PromiseRejectionEvent) return !0;
        }

        if (a && !_L.prototype["finally"]) return !0;
        if (A >= 51 && /native code/.test(_L)) return !1;

        var t = _L.resolve(1),
            n = function n(t) {
          t(function () {}, function () {});
        };

        return (t.constructor = {})[I] = n, !(t.then(function () {}) instanceof n);
      }),
          Y = V || !w(function (t) {
        _L.all(t)["catch"](function () {});
      }),
          J = function J(t) {
        var n;
        return !(!y(t) || "function" != typeof (n = t.then)) && n;
      },
          Q = function Q(t, n, e) {
        if (!n.notified) {
          n.notified = !0;
          var r = n.reactions;
          E(function () {
            for (var o = n.value, i = 1 == n.state, c = 0; r.length > c;) {
              var u,
                  a,
                  f,
                  s = r[c++],
                  l = i ? s.ok : s.fail,
                  p = s.resolve,
                  d = s.reject,
                  v = s.domain;

              try {
                l ? (i || (2 === n.rejection && nt(t, n), n.rejection = 1), !0 === l ? u = o : (v && v.enter(), u = l(o), v && (v.exit(), f = !0)), u === s.promise ? d(F("Promise-chain cycle")) : (a = J(u)) ? a.call(u, p, d) : p(u)) : d(o);
              } catch (t) {
                v && !f && v.exit(), d(t);
              }
            }

            n.reactions = [], n.notified = !1, e && !n.rejection && Z(t, n);
          });
        }
      },
          X = function X(t, n, e) {
        var r, o;
        H ? ((r = U.createEvent("Event")).promise = n, r.reason = e, r.initEvent(t, !1, !0), f.dispatchEvent(r)) : r = {
          promise: n,
          reason: e
        }, (o = f["on" + t]) ? o(r) : "unhandledrejection" === t && P("Unhandled promise rejection", e);
      },
          Z = function Z(t, n) {
        S.call(f, function () {
          var e,
              r = n.value;
          if (tt(n) && (e = _(function () {
            G ? z.emit("unhandledRejection", r, t) : X("unhandledrejection", t, r);
          }), n.rejection = G || tt(n) ? 2 : 1, e.error)) throw e.value;
        });
      },
          tt = function tt(t) {
        return 1 !== t.rejection && !t.parent;
      },
          nt = function nt(t, n) {
        S.call(f, function () {
          G ? z.emit("rejectionHandled", t) : X("rejectionhandled", t, n.value);
        });
      },
          et = function et(t, n, e, r) {
        return function (o) {
          t(n, e, o, r);
        };
      },
          rt = function rt(t, n, e, r) {
        n.done || (n.done = !0, r && (n = r), n.value = e, n.state = 2, Q(t, n, !0));
      },
          ot = function ot(t, n, e, r) {
        if (!n.done) {
          n.done = !0, r && (n = r);

          try {
            if (t === e) throw F("Promise can't be resolved itself");
            var o = J(e);
            o ? E(function () {
              var r = {
                done: !1
              };

              try {
                o.call(e, et(ot, t, r, n), et(rt, t, r, n));
              } catch (e) {
                rt(t, r, e, n);
              }
            }) : (n.value = e, n.state = 1, Q(t, n, !1));
          } catch (e) {
            rt(t, {
              done: !1
            }, e, n);
          }
        }
      };

      V && (_L = function L(t) {
        g(this, _L, N), m(t), r.call(this);
        var n = $(this);

        try {
          t(et(ot, this, n), et(rt, this, n));
        } catch (t) {
          rt(this, n, t);
        }
      }, (r = function r(t) {
        R(this, {
          type: N,
          done: !1,
          notified: !1,
          parent: !1,
          reactions: [],
          rejection: !1,
          state: 0,
          value: void 0
        });
      }).prototype = d(_L.prototype, {
        then: function then(t, n) {
          var e = K(this),
              r = W(O(this, _L));
          return r.ok = "function" != typeof t || t, r.fail = "function" == typeof n && n, r.domain = G ? z.domain : void 0, e.parent = !0, e.reactions.push(r), 0 != e.state && Q(this, e, !1), r.promise;
        },
        "catch": function _catch(t) {
          return this.then(void 0, t);
        }
      }), o = function o() {
        var t = new r(),
            n = $(t);
        this.promise = t, this.resolve = et(ot, t, n), this.reject = et(rt, t, n);
      }, M.f = W = function W(t) {
        return t === _L || t === i ? new o(t) : q(t);
      }, a || "function" != typeof l || (c = l.prototype.then, p(l.prototype, "then", function (t, n) {
        var e = this;
        return new _L(function (t, n) {
          c.call(e, t, n);
        }).then(t, n);
      }, {
        unsafe: !0
      }), "function" == typeof B && u({
        global: !0,
        enumerable: !0,
        forced: !0
      }, {
        fetch: function fetch(t) {
          return T(_L, B.apply(f, arguments));
        }
      }))), u({
        global: !0,
        wrap: !0,
        forced: V
      }, {
        Promise: _L
      }), v(_L, N, !1, !0), h(N), i = s(N), u({
        target: N,
        stat: !0,
        forced: V
      }, {
        reject: function reject(t) {
          var n = W(this);
          return n.reject.call(void 0, t), n.promise;
        }
      }), u({
        target: N,
        stat: !0,
        forced: a || V
      }, {
        resolve: function resolve(t) {
          return T(a && this === i ? _L : this, t);
        }
      }), u({
        target: N,
        stat: !0,
        forced: Y
      }, {
        all: function all(t) {
          var n = this,
              e = W(n),
              r = e.resolve,
              o = e.reject,
              i = _(function () {
            var e = m(n.resolve),
                i = [],
                c = 0,
                u = 1;
            j(t, function (t) {
              var a = c++,
                  f = !1;
              i.push(void 0), u++, e.call(n, t).then(function (t) {
                f || (f = !0, i[a] = t, --u || r(i));
              }, o);
            }), --u || r(i);
          });

          return i.error && o(i.value), e.promise;
        },
        race: function race(t) {
          var n = this,
              e = W(n),
              r = e.reject,
              o = _(function () {
            var o = m(n.resolve);
            j(t, function (t) {
              o.call(n, t).then(e.resolve, r);
            });
          });

          return o.error && r(o.value), e.promise;
        }
      });
    }, function (t, n, e) {
      "use strict";

      var r = {}.propertyIsEnumerable,
          o = Object.getOwnPropertyDescriptor,
          i = o && !r.call({
        1: 2
      }, 1);
      n.f = i ? function (t) {
        var n = o(this, t);
        return !!n && n.enumerable;
      } : r;
    }, function (t, n, e) {
      var r = e(2),
          o = e(6),
          i = "".split;
      t.exports = r(function () {
        return !Object("z").propertyIsEnumerable(0);
      }) ? function (t) {
        return "String" == o(t) ? i.call(t, "") : Object(t);
      } : Object;
    }, function (t, n, e) {
      var r = e(4),
          o = e(54),
          i = e(18),
          c = e(8);

      t.exports = function (t, n) {
        for (var e = o(n), u = c.f, a = i.f, f = 0; f < e.length; f++) {
          var s = e[f];
          r(t, s) || u(t, s, a(n, s));
        }
      };
    }, function (t, n, e) {
      var r = e(9),
          o = e(56),
          i = e(61),
          c = e(5);

      t.exports = r("Reflect", "ownKeys") || function (t) {
        var n = o.f(c(t)),
            e = i.f;
        return e ? n.concat(e(t)) : n;
      };
    }, function (t, n, e) {
      var r = e(0);
      t.exports = r;
    }, function (t, n, e) {
      var r = e(57),
          o = e(60).concat("length", "prototype");

      n.f = Object.getOwnPropertyNames || function (t) {
        return r(t, o);
      };
    }, function (t, n, e) {
      var r = e(4),
          o = e(19),
          i = e(58).indexOf,
          c = e(30);

      t.exports = function (t, n) {
        var e,
            u = o(t),
            a = 0,
            f = [];

        for (e in u) {
          !r(c, e) && r(u, e) && f.push(e);
        }

        for (; n.length > a;) {
          r(u, e = n[a++]) && (~i(f, e) || f.push(e));
        }

        return f;
      };
    }, function (t, n, e) {
      var r = e(19),
          o = e(20),
          i = e(59),
          c = function c(t) {
        return function (n, e, c) {
          var u,
              a = r(n),
              f = o(a.length),
              s = i(c, f);

          if (t && e != e) {
            for (; f > s;) {
              if ((u = a[s++]) != u) return !0;
            }
          } else for (; f > s; s++) {
            if ((t || s in a) && a[s] === e) return t || s || 0;
          }

          return !t && -1;
        };
      };

      t.exports = {
        includes: c(!0),
        indexOf: c(!1)
      };
    }, function (t, n, e) {
      var r = e(34),
          o = Math.max,
          i = Math.min;

      t.exports = function (t, n) {
        var e = r(t);
        return e < 0 ? o(e + n, 0) : i(e, n);
      };
    }, function (t, n) {
      t.exports = ["constructor", "hasOwnProperty", "isPrototypeOf", "propertyIsEnumerable", "toLocaleString", "toString", "valueOf"];
    }, function (t, n) {
      n.f = Object.getOwnPropertySymbols;
    }, function (t, n, e) {
      var r = e(0);
      t.exports = r.Promise;
    }, function (t, n, e) {
      var r = e(11);

      t.exports = function (t, n, e) {
        for (var o in n) {
          r(t, o, n[o], e);
        }

        return t;
      };
    }, function (t, n, e) {
      var r = e(8).f,
          o = e(4),
          i = e(1)("toStringTag");

      t.exports = function (t, n, e) {
        t && !o(t = e ? t : t.prototype, i) && r(t, i, {
          configurable: !0,
          value: n
        });
      };
    }, function (t, n, e) {
      "use strict";

      var r = e(9),
          o = e(8),
          i = e(1),
          c = e(7),
          u = i("species");

      t.exports = function (t) {
        var n = r(t),
            e = o.f;
        c && n && !n[u] && e(n, u, {
          configurable: !0,
          get: function get() {
            return this;
          }
        });
      };
    }, function (t, n) {
      t.exports = function (t, n, e) {
        if (!(t instanceof n)) throw TypeError("Incorrect " + (e ? e + " " : "") + "invocation");
        return t;
      };
    }, function (t, n, e) {
      var r = e(5),
          o = e(68),
          i = e(20),
          c = e(37),
          u = e(69),
          a = e(70),
          f = function f(t, n) {
        this.stopped = t, this.result = n;
      };

      (t.exports = function (t, n, e, s, l) {
        var p,
            d,
            v,
            h,
            y,
            m,
            g,
            x = c(n, e, s ? 2 : 1);
        if (l) p = t;else {
          if ("function" != typeof (d = u(t))) throw TypeError("Target is not iterable");

          if (o(d)) {
            for (v = 0, h = i(t.length); h > v; v++) {
              if ((y = s ? x(r(g = t[v])[0], g[1]) : x(t[v])) && y instanceof f) return y;
            }

            return new f(!1);
          }

          p = d.call(t);
        }

        for (m = p.next; !(g = m.call(p)).done;) {
          if ("object" == _typeof(y = a(p, x, g.value, s)) && y && y instanceof f) return y;
        }

        return new f(!1);
      }).stop = function (t) {
        return new f(!0, t);
      };
    }, function (t, n, e) {
      var r = e(1),
          o = e(36),
          i = r("iterator"),
          c = Array.prototype;

      t.exports = function (t) {
        return void 0 !== t && (o.Array === t || c[i] === t);
      };
    }, function (t, n, e) {
      var r = e(31),
          o = e(36),
          i = e(1)("iterator");

      t.exports = function (t) {
        if (null != t) return t[i] || t["@@iterator"] || o[r(t)];
      };
    }, function (t, n, e) {
      var r = e(5);

      t.exports = function (t, n, e, o) {
        try {
          return o ? n(r(e)[0], e[1]) : n(e);
        } catch (n) {
          var i = t["return"];
          throw void 0 !== i && r(i.call(t)), n;
        }
      };
    }, function (t, n, e) {
      var r = e(1)("iterator"),
          o = !1;

      try {
        var i = 0,
            c = {
          next: function next() {
            return {
              done: !!i++
            };
          },
          "return": function _return() {
            o = !0;
          }
        };
        c[r] = function () {
          return this;
        }, Array.from(c, function () {
          throw 2;
        });
      } catch (t) {}

      t.exports = function (t, n) {
        if (!n && !o) return !1;
        var e = !1;

        try {
          var i = {};
          i[r] = function () {
            return {
              next: function next() {
                return {
                  done: e = !0
                };
              }
            };
          }, t(i);
        } catch (t) {}

        return e;
      };
    }, function (t, n, e) {
      var r = e(5),
          o = e(12),
          i = e(1)("species");

      t.exports = function (t, n) {
        var e,
            c = r(t).constructor;
        return void 0 === c || null == (e = r(c)[i]) ? n : o(e);
      };
    }, function (t, n, e) {
      var r = e(9);
      t.exports = r("document", "documentElement");
    }, function (t, n, e) {
      var r,
          o,
          i,
          c,
          u,
          a,
          f,
          s,
          l = e(0),
          p = e(18).f,
          d = e(6),
          v = e(38).set,
          h = e(39),
          y = l.MutationObserver || l.WebKitMutationObserver,
          m = l.process,
          g = l.Promise,
          x = "process" == d(m),
          b = p(l, "queueMicrotask"),
          j = b && b.value;
      j || (r = function r() {
        var t, n;

        for (x && (t = m.domain) && t.exit(); o;) {
          n = o.fn, o = o.next;

          try {
            n();
          } catch (t) {
            throw o ? c() : i = void 0, t;
          }
        }

        i = void 0, t && t.enter();
      }, x ? c = function c() {
        m.nextTick(r);
      } : y && !h ? (u = !0, a = document.createTextNode(""), new y(r).observe(a, {
        characterData: !0
      }), c = function c() {
        a.data = u = !u;
      }) : g && g.resolve ? (f = g.resolve(void 0), s = f.then, c = function c() {
        s.call(f, r);
      }) : c = function c() {
        v.call(l, r);
      }), t.exports = j || function (t) {
        var n = {
          fn: t,
          next: void 0
        };
        i && (i.next = n), o || (o = n, c()), i = n;
      };
    }, function (t, n, e) {
      var r = e(5),
          o = e(3),
          i = e(41);

      t.exports = function (t, n) {
        if (r(t), o(n) && n.constructor === t) return n;
        var e = i.f(t);
        return (0, e.resolve)(n), e.promise;
      };
    }, function (t, n, e) {
      var r = e(0);

      t.exports = function (t, n) {
        var e = r.console;
        e && e.error && (1 === arguments.length ? e.error(t) : e.error(t, n));
      };
    }, function (t, n) {
      t.exports = function (t) {
        try {
          return {
            error: !1,
            value: t()
          };
        } catch (t) {
          return {
            error: !0,
            value: t
          };
        }
      };
    }, function (t, n, e) {
      "use strict";

      var r = e(32),
          o = e(2),
          i = e(42),
          c = e(3),
          u = e(79),
          a = e(20),
          f = e(80),
          s = e(81),
          l = e(82),
          p = e(1),
          d = e(21),
          v = p("isConcatSpreadable"),
          h = d >= 51 || !o(function () {
        var t = [];
        return t[v] = !1, t.concat()[0] !== t;
      }),
          y = l("concat"),
          m = function m(t) {
        if (!c(t)) return !1;
        var n = t[v];
        return void 0 !== n ? !!n : i(t);
      };

      r({
        target: "Array",
        proto: !0,
        forced: !h || !y
      }, {
        concat: function concat(t) {
          var n,
              e,
              r,
              o,
              i,
              c = u(this),
              l = s(c, 0),
              p = 0;

          for (n = -1, r = arguments.length; n < r; n++) {
            if (m(i = -1 === n ? c : arguments[n])) {
              if (p + (o = a(i.length)) > 9007199254740991) throw TypeError("Maximum allowed index exceeded");

              for (e = 0; e < o; e++, p++) {
                e in i && f(l, p, i[e]);
              }
            } else {
              if (p >= 9007199254740991) throw TypeError("Maximum allowed index exceeded");
              f(l, p++, i);
            }
          }

          return l.length = p, l;
        }
      });
    }, function (t, n, e) {
      var r = e(33);

      t.exports = function (t) {
        return Object(r(t));
      };
    }, function (t, n, e) {
      "use strict";

      var r = e(15),
          o = e(8),
          i = e(16);

      t.exports = function (t, n, e) {
        var c = r(n);
        c in t ? o.f(t, c, i(0, e)) : t[c] = e;
      };
    }, function (t, n, e) {
      var r = e(3),
          o = e(42),
          i = e(1)("species");

      t.exports = function (t, n) {
        var e;
        return o(t) && ("function" != typeof (e = t.constructor) || e !== Array && !o(e.prototype) ? r(e) && null === (e = e[i]) && (e = void 0) : e = void 0), new (void 0 === e ? Array : e)(0 === n ? 0 : n);
      };
    }, function (t, n, e) {
      var r = e(2),
          o = e(1),
          i = e(21),
          c = o("species");

      t.exports = function (t) {
        return i >= 51 || !r(function () {
          var n = [];
          return (n.constructor = {})[c] = function () {
            return {
              foo: 1
            };
          }, 1 !== n[t](Boolean).foo;
        });
      };
    }, function (t, n, e) {
      "use strict";

      e.r(n), e.d(n, "getEditorNamespace", function () {
        return c;
      }), e.d(n, "debounce", function () {
        return u;
      }), e(44), e(50);
      var r,
          o = e(43),
          i = e.n(o);

      function c(t, n) {
        return "CKEDITOR" in window ? Promise.resolve(CKEDITOR) : t.length < 1 ? Promise.reject(new TypeError("CKEditor URL must be a non-empty string.")) : (r || (r = c.scriptLoader(t).then(function (t) {
          return n && n(t), t;
        })), r);
      }

      c.scriptLoader = function (t) {
        return new Promise(function (n, e) {
          i()(t, function (t) {
            return r = void 0, t ? e(t) : window.CKEDITOR ? void n(CKEDITOR) : e(new Error("Script loaded from editorUrl doesn't provide CKEDITOR namespace."));
          });
        });
      }, e(78);

      var u = function u(t, n) {
        var e,
            r = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {};
        return function () {
          clearTimeout(e);

          for (var o = arguments.length, i = new Array(o), c = 0; c < o; c++) {
            i[c] = arguments[c];
          }

          e = setTimeout(t.bind.apply(t, [r].concat(i)), n);
        };
      };
    }]));
  }, function (t, n, e) {
    "use strict";

    e.r(n);
    var r = e(0),
        o = {
      name: "ckeditor",
      render: function render(t) {
        return t("div", {}, [t(this.tagName)]);
      },
      props: {
        value: {
          type: String,
          "default": ""
        },
        type: {
          type: String,
          "default": "classic",
          validator: function validator(t) {
            return ["classic", "inline"].includes(t);
          }
        },
        editorUrl: {
          type: String,
          "default": "https://cdn.ckeditor.com/4.16.2/standard-all/ckeditor.js"
        },
        config: {
          type: Object,
          "default": function _default() {}
        },
        tagName: {
          type: String,
          "default": "textarea"
        },
        readOnly: {
          type: Boolean,
          "default": null
        },
        throttle: {
          type: Number,
          "default": 80
        }
      },
      mounted: function mounted() {
        var _this = this;

        Object(r.getEditorNamespace)(this.editorUrl, function (t) {
          _this.$emit("namespaceloaded", t);
        }).then(function () {
          if (_this.$_destroyed) return;
          var t = _this.config || {};
          null !== _this.readOnly && (t.readOnly = _this.readOnly);
          var n = "inline" === _this.type ? "inline" : "replace",
              e = _this.$el.firstElementChild,
              r = _this.instance = CKEDITOR[n](e, t);
          r.on("instanceReady", function () {
            var t = _this.value;
            r.fire("lockSnapshot"), r.setData(t, {
              callback: function callback() {
                _this.$_setUpEditorEvents();

                var n = r.getData();
                t !== n ? (_this.$once("input", function () {
                  _this.$emit("ready", r);
                }), _this.$emit("input", n)) : _this.$emit("ready", r), r.fire("unlockSnapshot");
              }
            });
          });
        });
      },
      beforeDestroy: function beforeDestroy() {
        this.instance && this.instance.destroy(), this.$_destroyed = !0;
      },
      watch: {
        value: function value(t) {
          this.instance && this.instance.getData() !== t && this.instance.setData(t);
        },
        readOnly: function readOnly(t) {
          this.instance && this.instance.setReadOnly(t);
        }
      },
      methods: {
        $_setUpEditorEvents: function $_setUpEditorEvents() {
          var _this2 = this;

          var t = this.instance,
              n = Object(r.debounce)(function (n) {
            var e = t.getData();
            _this2.value !== e && _this2.$emit("input", e, n, t);
          }, this.throttle);
          t.on("change", n), t.on("focus", function (n) {
            _this2.$emit("focus", n, t);
          }), t.on("blur", function (n) {
            _this2.$emit("blur", n, t);
          });
        }
      }
    };
    var i = {
      install: function install(t) {
        t.component("ckeditor", o);
      },
      component: o
    };
    n["default"] = i;
  }])["default"];
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/sortablejs/modular/sortable.esm.js":
/*!*********************************************************!*\
  !*** ./node_modules/sortablejs/modular/sortable.esm.js ***!
  \*********************************************************/
/*! exports provided: default, MultiDrag, Sortable, Swap */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MultiDrag", function() { return MultiDragPlugin; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Sortable", function() { return Sortable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Swap", function() { return SwapPlugin; });
function _typeof2(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

/**!
 * Sortable 1.10.2
 * @author	RubaXa   <trash@rubaxa.org>
 * @author	owenm    <owen23355@gmail.com>
 * @license MIT
 */
function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      _defineProperty(target, key, source[key]);
    });
  }

  return target;
}

function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};

  var target = _objectWithoutPropertiesLoose(source, excluded);

  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}

function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}

var version = "1.10.2";

function userAgent(pattern) {
  if (typeof window !== 'undefined' && window.navigator) {
    return !! /*@__PURE__*/navigator.userAgent.match(pattern);
  }
}

var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
var Edge = userAgent(/Edge/i);
var FireFox = userAgent(/firefox/i);
var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
var IOS = userAgent(/iP(ad|od|hone)/i);
var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);
var captureMode = {
  capture: false,
  passive: false
};

function on(el, event, fn) {
  el.addEventListener(event, fn, !IE11OrLess && captureMode);
}

function off(el, event, fn) {
  el.removeEventListener(event, fn, !IE11OrLess && captureMode);
}

function matches(
/**HTMLElement*/
el,
/**String*/
selector) {
  if (!selector) return;
  selector[0] === '>' && (selector = selector.substring(1));

  if (el) {
    try {
      if (el.matches) {
        return el.matches(selector);
      } else if (el.msMatchesSelector) {
        return el.msMatchesSelector(selector);
      } else if (el.webkitMatchesSelector) {
        return el.webkitMatchesSelector(selector);
      }
    } catch (_) {
      return false;
    }
  }

  return false;
}

function getParentOrHost(el) {
  return el.host && el !== document && el.host.nodeType ? el.host : el.parentNode;
}

function closest(
/**HTMLElement*/
el,
/**String*/
selector,
/**HTMLElement*/
ctx, includeCTX) {
  if (el) {
    ctx = ctx || document;

    do {
      if (selector != null && (selector[0] === '>' ? el.parentNode === ctx && matches(el, selector) : matches(el, selector)) || includeCTX && el === ctx) {
        return el;
      }

      if (el === ctx) break;
      /* jshint boss:true */
    } while (el = getParentOrHost(el));
  }

  return null;
}

var R_SPACE = /\s+/g;

function toggleClass(el, name, state) {
  if (el && name) {
    if (el.classList) {
      el.classList[state ? 'add' : 'remove'](name);
    } else {
      var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
      el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
    }
  }
}

function css(el, prop, val) {
  var style = el && el.style;

  if (style) {
    if (val === void 0) {
      if (document.defaultView && document.defaultView.getComputedStyle) {
        val = document.defaultView.getComputedStyle(el, '');
      } else if (el.currentStyle) {
        val = el.currentStyle;
      }

      return prop === void 0 ? val : val[prop];
    } else {
      if (!(prop in style) && prop.indexOf('webkit') === -1) {
        prop = '-webkit-' + prop;
      }

      style[prop] = val + (typeof val === 'string' ? '' : 'px');
    }
  }
}

function matrix(el, selfOnly) {
  var appliedTransforms = '';

  if (typeof el === 'string') {
    appliedTransforms = el;
  } else {
    do {
      var transform = css(el, 'transform');

      if (transform && transform !== 'none') {
        appliedTransforms = transform + ' ' + appliedTransforms;
      }
      /* jshint boss:true */

    } while (!selfOnly && (el = el.parentNode));
  }

  var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
  /*jshint -W056 */

  return matrixFn && new matrixFn(appliedTransforms);
}

function find(ctx, tagName, iterator) {
  if (ctx) {
    var list = ctx.getElementsByTagName(tagName),
        i = 0,
        n = list.length;

    if (iterator) {
      for (; i < n; i++) {
        iterator(list[i], i);
      }
    }

    return list;
  }

  return [];
}

function getWindowScrollingElement() {
  var scrollingElement = document.scrollingElement;

  if (scrollingElement) {
    return scrollingElement;
  } else {
    return document.documentElement;
  }
}
/**
 * Returns the "bounding client rect" of given element
 * @param  {HTMLElement} el                       The element whose boundingClientRect is wanted
 * @param  {[Boolean]} relativeToContainingBlock  Whether the rect should be relative to the containing block of (including) the container
 * @param  {[Boolean]} relativeToNonStaticParent  Whether the rect should be relative to the relative parent of (including) the contaienr
 * @param  {[Boolean]} undoScale                  Whether the container's scale() should be undone
 * @param  {[HTMLElement]} container              The parent the element will be placed in
 * @return {Object}                               The boundingClientRect of el, with specified adjustments
 */


function getRect(el, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
  if (!el.getBoundingClientRect && el !== window) return;
  var elRect, top, left, bottom, right, height, width;

  if (el !== window && el !== getWindowScrollingElement()) {
    elRect = el.getBoundingClientRect();
    top = elRect.top;
    left = elRect.left;
    bottom = elRect.bottom;
    right = elRect.right;
    height = elRect.height;
    width = elRect.width;
  } else {
    top = 0;
    left = 0;
    bottom = window.innerHeight;
    right = window.innerWidth;
    height = window.innerHeight;
    width = window.innerWidth;
  }

  if ((relativeToContainingBlock || relativeToNonStaticParent) && el !== window) {
    // Adjust for translate()
    container = container || el.parentNode; // solves #1123 (see: https://stackoverflow.com/a/37953806/6088312)
    // Not needed on <= IE11

    if (!IE11OrLess) {
      do {
        if (container && container.getBoundingClientRect && (css(container, 'transform') !== 'none' || relativeToNonStaticParent && css(container, 'position') !== 'static')) {
          var containerRect = container.getBoundingClientRect(); // Set relative to edges of padding box of container

          top -= containerRect.top + parseInt(css(container, 'border-top-width'));
          left -= containerRect.left + parseInt(css(container, 'border-left-width'));
          bottom = top + elRect.height;
          right = left + elRect.width;
          break;
        }
        /* jshint boss:true */

      } while (container = container.parentNode);
    }
  }

  if (undoScale && el !== window) {
    // Adjust for scale()
    var elMatrix = matrix(container || el),
        scaleX = elMatrix && elMatrix.a,
        scaleY = elMatrix && elMatrix.d;

    if (elMatrix) {
      top /= scaleY;
      left /= scaleX;
      width /= scaleX;
      height /= scaleY;
      bottom = top + height;
      right = left + width;
    }
  }

  return {
    top: top,
    left: left,
    bottom: bottom,
    right: right,
    width: width,
    height: height
  };
}
/**
 * Checks if a side of an element is scrolled past a side of its parents
 * @param  {HTMLElement}  el           The element who's side being scrolled out of view is in question
 * @param  {String}       elSide       Side of the element in question ('top', 'left', 'right', 'bottom')
 * @param  {String}       parentSide   Side of the parent in question ('top', 'left', 'right', 'bottom')
 * @return {HTMLElement}               The parent scroll element that the el's side is scrolled past, or null if there is no such element
 */


function isScrolledPast(el, elSide, parentSide) {
  var parent = getParentAutoScrollElement(el, true),
      elSideVal = getRect(el)[elSide];
  /* jshint boss:true */

  while (parent) {
    var parentSideVal = getRect(parent)[parentSide],
        visible = void 0;

    if (parentSide === 'top' || parentSide === 'left') {
      visible = elSideVal >= parentSideVal;
    } else {
      visible = elSideVal <= parentSideVal;
    }

    if (!visible) return parent;
    if (parent === getWindowScrollingElement()) break;
    parent = getParentAutoScrollElement(parent, false);
  }

  return false;
}
/**
 * Gets nth child of el, ignoring hidden children, sortable's elements (does not ignore clone if it's visible)
 * and non-draggable elements
 * @param  {HTMLElement} el       The parent element
 * @param  {Number} childNum      The index of the child
 * @param  {Object} options       Parent Sortable's options
 * @return {HTMLElement}          The child at index childNum, or null if not found
 */


function getChild(el, childNum, options) {
  var currentChild = 0,
      i = 0,
      children = el.children;

  while (i < children.length) {
    if (children[i].style.display !== 'none' && children[i] !== Sortable.ghost && children[i] !== Sortable.dragged && closest(children[i], options.draggable, el, false)) {
      if (currentChild === childNum) {
        return children[i];
      }

      currentChild++;
    }

    i++;
  }

  return null;
}
/**
 * Gets the last child in the el, ignoring ghostEl or invisible elements (clones)
 * @param  {HTMLElement} el       Parent element
 * @param  {selector} selector    Any other elements that should be ignored
 * @return {HTMLElement}          The last child, ignoring ghostEl
 */


function lastChild(el, selector) {
  var last = el.lastElementChild;

  while (last && (last === Sortable.ghost || css(last, 'display') === 'none' || selector && !matches(last, selector))) {
    last = last.previousElementSibling;
  }

  return last || null;
}
/**
 * Returns the index of an element within its parent for a selected set of
 * elements
 * @param  {HTMLElement} el
 * @param  {selector} selector
 * @return {number}
 */


function index(el, selector) {
  var index = 0;

  if (!el || !el.parentNode) {
    return -1;
  }
  /* jshint boss:true */


  while (el = el.previousElementSibling) {
    if (el.nodeName.toUpperCase() !== 'TEMPLATE' && el !== Sortable.clone && (!selector || matches(el, selector))) {
      index++;
    }
  }

  return index;
}
/**
 * Returns the scroll offset of the given element, added with all the scroll offsets of parent elements.
 * The value is returned in real pixels.
 * @param  {HTMLElement} el
 * @return {Array}             Offsets in the format of [left, top]
 */


function getRelativeScrollOffset(el) {
  var offsetLeft = 0,
      offsetTop = 0,
      winScroller = getWindowScrollingElement();

  if (el) {
    do {
      var elMatrix = matrix(el),
          scaleX = elMatrix.a,
          scaleY = elMatrix.d;
      offsetLeft += el.scrollLeft * scaleX;
      offsetTop += el.scrollTop * scaleY;
    } while (el !== winScroller && (el = el.parentNode));
  }

  return [offsetLeft, offsetTop];
}
/**
 * Returns the index of the object within the given array
 * @param  {Array} arr   Array that may or may not hold the object
 * @param  {Object} obj  An object that has a key-value pair unique to and identical to a key-value pair in the object you want to find
 * @return {Number}      The index of the object in the array, or -1
 */


function indexOfObject(arr, obj) {
  for (var i in arr) {
    if (!arr.hasOwnProperty(i)) continue;

    for (var key in obj) {
      if (obj.hasOwnProperty(key) && obj[key] === arr[i][key]) return Number(i);
    }
  }

  return -1;
}

function getParentAutoScrollElement(el, includeSelf) {
  // skip to window
  if (!el || !el.getBoundingClientRect) return getWindowScrollingElement();
  var elem = el;
  var gotSelf = false;

  do {
    // we don't need to get elem css if it isn't even overflowing in the first place (performance)
    if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
      var elemCSS = css(elem);

      if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == 'auto' || elemCSS.overflowX == 'scroll') || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == 'auto' || elemCSS.overflowY == 'scroll')) {
        if (!elem.getBoundingClientRect || elem === document.body) return getWindowScrollingElement();
        if (gotSelf || includeSelf) return elem;
        gotSelf = true;
      }
    }
    /* jshint boss:true */

  } while (elem = elem.parentNode);

  return getWindowScrollingElement();
}

function extend(dst, src) {
  if (dst && src) {
    for (var key in src) {
      if (src.hasOwnProperty(key)) {
        dst[key] = src[key];
      }
    }
  }

  return dst;
}

function isRectEqual(rect1, rect2) {
  return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
}

var _throttleTimeout;

function throttle(callback, ms) {
  return function () {
    if (!_throttleTimeout) {
      var args = arguments,
          _this = this;

      if (args.length === 1) {
        callback.call(_this, args[0]);
      } else {
        callback.apply(_this, args);
      }

      _throttleTimeout = setTimeout(function () {
        _throttleTimeout = void 0;
      }, ms);
    }
  };
}

function cancelThrottle() {
  clearTimeout(_throttleTimeout);
  _throttleTimeout = void 0;
}

function scrollBy(el, x, y) {
  el.scrollLeft += x;
  el.scrollTop += y;
}

function clone(el) {
  var Polymer = window.Polymer;
  var $ = window.jQuery || window.Zepto;

  if (Polymer && Polymer.dom) {
    return Polymer.dom(el).cloneNode(true);
  } else if ($) {
    return $(el).clone(true)[0];
  } else {
    return el.cloneNode(true);
  }
}

function setRect(el, rect) {
  css(el, 'position', 'absolute');
  css(el, 'top', rect.top);
  css(el, 'left', rect.left);
  css(el, 'width', rect.width);
  css(el, 'height', rect.height);
}

function unsetRect(el) {
  css(el, 'position', '');
  css(el, 'top', '');
  css(el, 'left', '');
  css(el, 'width', '');
  css(el, 'height', '');
}

var expando = 'Sortable' + new Date().getTime();

function AnimationStateManager() {
  var animationStates = [],
      animationCallbackId;
  return {
    captureAnimationState: function captureAnimationState() {
      animationStates = [];
      if (!this.options.animation) return;
      var children = [].slice.call(this.el.children);
      children.forEach(function (child) {
        if (css(child, 'display') === 'none' || child === Sortable.ghost) return;
        animationStates.push({
          target: child,
          rect: getRect(child)
        });

        var fromRect = _objectSpread({}, animationStates[animationStates.length - 1].rect); // If animating: compensate for current animation


        if (child.thisAnimationDuration) {
          var childMatrix = matrix(child, true);

          if (childMatrix) {
            fromRect.top -= childMatrix.f;
            fromRect.left -= childMatrix.e;
          }
        }

        child.fromRect = fromRect;
      });
    },
    addAnimationState: function addAnimationState(state) {
      animationStates.push(state);
    },
    removeAnimationState: function removeAnimationState(target) {
      animationStates.splice(indexOfObject(animationStates, {
        target: target
      }), 1);
    },
    animateAll: function animateAll(callback) {
      var _this = this;

      if (!this.options.animation) {
        clearTimeout(animationCallbackId);
        if (typeof callback === 'function') callback();
        return;
      }

      var animating = false,
          animationTime = 0;
      animationStates.forEach(function (state) {
        var time = 0,
            target = state.target,
            fromRect = target.fromRect,
            toRect = getRect(target),
            prevFromRect = target.prevFromRect,
            prevToRect = target.prevToRect,
            animatingRect = state.rect,
            targetMatrix = matrix(target, true);

        if (targetMatrix) {
          // Compensate for current animation
          toRect.top -= targetMatrix.f;
          toRect.left -= targetMatrix.e;
        }

        target.toRect = toRect;

        if (target.thisAnimationDuration) {
          // Could also check if animatingRect is between fromRect and toRect
          if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) && // Make sure animatingRect is on line between toRect & fromRect
          (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) {
            // If returning to same place as started from animation and on same axis
            time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
          }
        } // if fromRect != toRect: animate


        if (!isRectEqual(toRect, fromRect)) {
          target.prevFromRect = fromRect;
          target.prevToRect = toRect;

          if (!time) {
            time = _this.options.animation;
          }

          _this.animate(target, animatingRect, toRect, time);
        }

        if (time) {
          animating = true;
          animationTime = Math.max(animationTime, time);
          clearTimeout(target.animationResetTimer);
          target.animationResetTimer = setTimeout(function () {
            target.animationTime = 0;
            target.prevFromRect = null;
            target.fromRect = null;
            target.prevToRect = null;
            target.thisAnimationDuration = null;
          }, time);
          target.thisAnimationDuration = time;
        }
      });
      clearTimeout(animationCallbackId);

      if (!animating) {
        if (typeof callback === 'function') callback();
      } else {
        animationCallbackId = setTimeout(function () {
          if (typeof callback === 'function') callback();
        }, animationTime);
      }

      animationStates = [];
    },
    animate: function animate(target, currentRect, toRect, duration) {
      if (duration) {
        css(target, 'transition', '');
        css(target, 'transform', '');
        var elMatrix = matrix(this.el),
            scaleX = elMatrix && elMatrix.a,
            scaleY = elMatrix && elMatrix.d,
            translateX = (currentRect.left - toRect.left) / (scaleX || 1),
            translateY = (currentRect.top - toRect.top) / (scaleY || 1);
        target.animatingX = !!translateX;
        target.animatingY = !!translateY;
        css(target, 'transform', 'translate3d(' + translateX + 'px,' + translateY + 'px,0)');
        repaint(target); // repaint

        css(target, 'transition', 'transform ' + duration + 'ms' + (this.options.easing ? ' ' + this.options.easing : ''));
        css(target, 'transform', 'translate3d(0,0,0)');
        typeof target.animated === 'number' && clearTimeout(target.animated);
        target.animated = setTimeout(function () {
          css(target, 'transition', '');
          css(target, 'transform', '');
          target.animated = false;
          target.animatingX = false;
          target.animatingY = false;
        }, duration);
      }
    }
  };
}

function repaint(target) {
  return target.offsetWidth;
}

function calculateRealTime(animatingRect, fromRect, toRect, options) {
  return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options.animation;
}

var plugins = [];
var defaults = {
  initializeByDefault: true
};
var PluginManager = {
  mount: function mount(plugin) {
    // Set default static properties
    for (var option in defaults) {
      if (defaults.hasOwnProperty(option) && !(option in plugin)) {
        plugin[option] = defaults[option];
      }
    }

    plugins.push(plugin);
  },
  pluginEvent: function pluginEvent(eventName, sortable, evt) {
    var _this = this;

    this.eventCanceled = false;

    evt.cancel = function () {
      _this.eventCanceled = true;
    };

    var eventNameGlobal = eventName + 'Global';
    plugins.forEach(function (plugin) {
      if (!sortable[plugin.pluginName]) return; // Fire global events if it exists in this sortable

      if (sortable[plugin.pluginName][eventNameGlobal]) {
        sortable[plugin.pluginName][eventNameGlobal](_objectSpread({
          sortable: sortable
        }, evt));
      } // Only fire plugin event if plugin is enabled in this sortable,
      // and plugin has event defined


      if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
        sortable[plugin.pluginName][eventName](_objectSpread({
          sortable: sortable
        }, evt));
      }
    });
  },
  initializePlugins: function initializePlugins(sortable, el, defaults, options) {
    plugins.forEach(function (plugin) {
      var pluginName = plugin.pluginName;
      if (!sortable.options[pluginName] && !plugin.initializeByDefault) return;
      var initialized = new plugin(sortable, el, sortable.options);
      initialized.sortable = sortable;
      initialized.options = sortable.options;
      sortable[pluginName] = initialized; // Add default options from plugin

      _extends(defaults, initialized.defaults);
    });

    for (var option in sortable.options) {
      if (!sortable.options.hasOwnProperty(option)) continue;
      var modified = this.modifyOption(sortable, option, sortable.options[option]);

      if (typeof modified !== 'undefined') {
        sortable.options[option] = modified;
      }
    }
  },
  getEventProperties: function getEventProperties(name, sortable) {
    var eventProperties = {};
    plugins.forEach(function (plugin) {
      if (typeof plugin.eventProperties !== 'function') return;

      _extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
    });
    return eventProperties;
  },
  modifyOption: function modifyOption(sortable, name, value) {
    var modifiedValue;
    plugins.forEach(function (plugin) {
      // Plugin must exist on the Sortable
      if (!sortable[plugin.pluginName]) return; // If static option listener exists for this option, call in the context of the Sortable's instance of this plugin

      if (plugin.optionListeners && typeof plugin.optionListeners[name] === 'function') {
        modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
      }
    });
    return modifiedValue;
  }
};

function dispatchEvent(_ref) {
  var sortable = _ref.sortable,
      rootEl = _ref.rootEl,
      name = _ref.name,
      targetEl = _ref.targetEl,
      cloneEl = _ref.cloneEl,
      toEl = _ref.toEl,
      fromEl = _ref.fromEl,
      oldIndex = _ref.oldIndex,
      newIndex = _ref.newIndex,
      oldDraggableIndex = _ref.oldDraggableIndex,
      newDraggableIndex = _ref.newDraggableIndex,
      originalEvent = _ref.originalEvent,
      putSortable = _ref.putSortable,
      extraEventProperties = _ref.extraEventProperties;
  sortable = sortable || rootEl && rootEl[expando];
  if (!sortable) return;
  var evt,
      options = sortable.options,
      onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1); // Support for new CustomEvent feature

  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent(name, {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent('Event');
    evt.initEvent(name, true, true);
  }

  evt.to = toEl || rootEl;
  evt.from = fromEl || rootEl;
  evt.item = targetEl || rootEl;
  evt.clone = cloneEl;
  evt.oldIndex = oldIndex;
  evt.newIndex = newIndex;
  evt.oldDraggableIndex = oldDraggableIndex;
  evt.newDraggableIndex = newDraggableIndex;
  evt.originalEvent = originalEvent;
  evt.pullMode = putSortable ? putSortable.lastPutMode : undefined;

  var allEventProperties = _objectSpread({}, extraEventProperties, PluginManager.getEventProperties(name, sortable));

  for (var option in allEventProperties) {
    evt[option] = allEventProperties[option];
  }

  if (rootEl) {
    rootEl.dispatchEvent(evt);
  }

  if (options[onName]) {
    options[onName].call(sortable, evt);
  }
}

var pluginEvent = function pluginEvent(eventName, sortable) {
  var _ref = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
      originalEvent = _ref.evt,
      data = _objectWithoutProperties(_ref, ["evt"]);

  PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread({
    dragEl: dragEl,
    parentEl: parentEl,
    ghostEl: ghostEl,
    rootEl: rootEl,
    nextEl: nextEl,
    lastDownEl: lastDownEl,
    cloneEl: cloneEl,
    cloneHidden: cloneHidden,
    dragStarted: moved,
    putSortable: putSortable,
    activeSortable: Sortable.active,
    originalEvent: originalEvent,
    oldIndex: oldIndex,
    oldDraggableIndex: oldDraggableIndex,
    newIndex: newIndex,
    newDraggableIndex: newDraggableIndex,
    hideGhostForTarget: _hideGhostForTarget,
    unhideGhostForTarget: _unhideGhostForTarget,
    cloneNowHidden: function cloneNowHidden() {
      cloneHidden = true;
    },
    cloneNowShown: function cloneNowShown() {
      cloneHidden = false;
    },
    dispatchSortableEvent: function dispatchSortableEvent(name) {
      _dispatchEvent({
        sortable: sortable,
        name: name,
        originalEvent: originalEvent
      });
    }
  }, data));
};

function _dispatchEvent(info) {
  dispatchEvent(_objectSpread({
    putSortable: putSortable,
    cloneEl: cloneEl,
    targetEl: dragEl,
    rootEl: rootEl,
    oldIndex: oldIndex,
    oldDraggableIndex: oldDraggableIndex,
    newIndex: newIndex,
    newDraggableIndex: newDraggableIndex
  }, info));
}

var dragEl,
    parentEl,
    ghostEl,
    rootEl,
    nextEl,
    lastDownEl,
    cloneEl,
    cloneHidden,
    oldIndex,
    newIndex,
    oldDraggableIndex,
    newDraggableIndex,
    activeGroup,
    putSortable,
    awaitingDragStarted = false,
    ignoreNextClick = false,
    sortables = [],
    tapEvt,
    touchEvt,
    lastDx,
    lastDy,
    tapDistanceLeft,
    tapDistanceTop,
    moved,
    lastTarget,
    lastDirection,
    pastFirstInvertThresh = false,
    isCircumstantialInvert = false,
    targetMoveDistance,
    // For positioning ghost absolutely
ghostRelativeParent,
    ghostRelativeParentInitialScroll = [],
    // (left, top)
_silent = false,
    savedInputChecked = [];
/** @const */

var documentExists = typeof document !== 'undefined',
    PositionGhostAbsolutely = IOS,
    CSSFloatProperty = Edge || IE11OrLess ? 'cssFloat' : 'float',
    // This will not pass for IE9, because IE9 DnD only works on anchors
supportDraggable = documentExists && !ChromeForAndroid && !IOS && 'draggable' in document.createElement('div'),
    supportCssPointerEvents = function () {
  if (!documentExists) return; // false when <= IE11

  if (IE11OrLess) {
    return false;
  }

  var el = document.createElement('x');
  el.style.cssText = 'pointer-events:auto';
  return el.style.pointerEvents === 'auto';
}(),
    _detectDirection = function _detectDirection(el, options) {
  var elCSS = css(el),
      elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth),
      child1 = getChild(el, 0, options),
      child2 = getChild(el, 1, options),
      firstChildCSS = child1 && css(child1),
      secondChildCSS = child2 && css(child2),
      firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width,
      secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;

  if (elCSS.display === 'flex') {
    return elCSS.flexDirection === 'column' || elCSS.flexDirection === 'column-reverse' ? 'vertical' : 'horizontal';
  }

  if (elCSS.display === 'grid') {
    return elCSS.gridTemplateColumns.split(' ').length <= 1 ? 'vertical' : 'horizontal';
  }

  if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== 'none') {
    var touchingSideChild2 = firstChildCSS["float"] === 'left' ? 'left' : 'right';
    return child2 && (secondChildCSS.clear === 'both' || secondChildCSS.clear === touchingSideChild2) ? 'vertical' : 'horizontal';
  }

  return child1 && (firstChildCSS.display === 'block' || firstChildCSS.display === 'flex' || firstChildCSS.display === 'table' || firstChildCSS.display === 'grid' || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === 'none' || child2 && elCSS[CSSFloatProperty] === 'none' && firstChildWidth + secondChildWidth > elWidth) ? 'vertical' : 'horizontal';
},
    _dragElInRowColumn = function _dragElInRowColumn(dragRect, targetRect, vertical) {
  var dragElS1Opp = vertical ? dragRect.left : dragRect.top,
      dragElS2Opp = vertical ? dragRect.right : dragRect.bottom,
      dragElOppLength = vertical ? dragRect.width : dragRect.height,
      targetS1Opp = vertical ? targetRect.left : targetRect.top,
      targetS2Opp = vertical ? targetRect.right : targetRect.bottom,
      targetOppLength = vertical ? targetRect.width : targetRect.height;
  return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
},

/**
 * Detects first nearest empty sortable to X and Y position using emptyInsertThreshold.
 * @param  {Number} x      X position
 * @param  {Number} y      Y position
 * @return {HTMLElement}   Element of the first found nearest Sortable
 */
_detectNearestEmptySortable = function _detectNearestEmptySortable(x, y) {
  var ret;
  sortables.some(function (sortable) {
    if (lastChild(sortable)) return;
    var rect = getRect(sortable),
        threshold = sortable[expando].options.emptyInsertThreshold,
        insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold,
        insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;

    if (threshold && insideHorizontally && insideVertically) {
      return ret = sortable;
    }
  });
  return ret;
},
    _prepareGroup = function _prepareGroup(options) {
  function toFn(value, pull) {
    return function (to, from, dragEl, evt) {
      var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;

      if (value == null && (pull || sameGroup)) {
        // Default pull value
        // Default pull and put value if same group
        return true;
      } else if (value == null || value === false) {
        return false;
      } else if (pull && value === 'clone') {
        return value;
      } else if (typeof value === 'function') {
        return toFn(value(to, from, dragEl, evt), pull)(to, from, dragEl, evt);
      } else {
        var otherGroup = (pull ? to : from).options.group.name;
        return value === true || typeof value === 'string' && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
      }
    };
  }

  var group = {};
  var originalGroup = options.group;

  if (!originalGroup || _typeof(originalGroup) != 'object') {
    originalGroup = {
      name: originalGroup
    };
  }

  group.name = originalGroup.name;
  group.checkPull = toFn(originalGroup.pull, true);
  group.checkPut = toFn(originalGroup.put);
  group.revertClone = originalGroup.revertClone;
  options.group = group;
},
    _hideGhostForTarget = function _hideGhostForTarget() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, 'display', 'none');
  }
},
    _unhideGhostForTarget = function _unhideGhostForTarget() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, 'display', '');
  }
}; // #1184 fix - Prevent click event on fallback if dragged but item not changed position


if (documentExists) {
  document.addEventListener('click', function (evt) {
    if (ignoreNextClick) {
      evt.preventDefault();
      evt.stopPropagation && evt.stopPropagation();
      evt.stopImmediatePropagation && evt.stopImmediatePropagation();
      ignoreNextClick = false;
      return false;
    }
  }, true);
}

var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent(evt) {
  if (dragEl) {
    evt = evt.touches ? evt.touches[0] : evt;

    var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);

    if (nearest) {
      // Create imitation event
      var event = {};

      for (var i in evt) {
        if (evt.hasOwnProperty(i)) {
          event[i] = evt[i];
        }
      }

      event.target = event.rootEl = nearest;
      event.preventDefault = void 0;
      event.stopPropagation = void 0;

      nearest[expando]._onDragOver(event);
    }
  }
};

var _checkOutsideTargetEl = function _checkOutsideTargetEl(evt) {
  if (dragEl) {
    dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
  }
};
/**
 * @class  Sortable
 * @param  {HTMLElement}  el
 * @param  {Object}       [options]
 */


function Sortable(el, options) {
  if (!(el && el.nodeType && el.nodeType === 1)) {
    throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el));
  }

  this.el = el; // root element

  this.options = options = _extends({}, options); // Export instance

  el[expando] = this;
  var defaults = {
    group: null,
    sort: true,
    disabled: false,
    store: null,
    handle: null,
    draggable: /^[uo]l$/i.test(el.nodeName) ? '>li' : '>*',
    swapThreshold: 1,
    // percentage; 0 <= x <= 1
    invertSwap: false,
    // invert always
    invertedSwapThreshold: null,
    // will be set to same as swapThreshold if default
    removeCloneOnHide: true,
    direction: function direction() {
      return _detectDirection(el, this.options);
    },
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    dragClass: 'sortable-drag',
    ignore: 'a, img',
    filter: null,
    preventOnFilter: true,
    animation: 0,
    easing: null,
    setData: function setData(dataTransfer, dragEl) {
      dataTransfer.setData('Text', dragEl.textContent);
    },
    dropBubble: false,
    dragoverBubble: false,
    dataIdAttr: 'data-id',
    delay: 0,
    delayOnTouchOnly: false,
    touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
    forceFallback: false,
    fallbackClass: 'sortable-fallback',
    fallbackOnBody: false,
    fallbackTolerance: 0,
    fallbackOffset: {
      x: 0,
      y: 0
    },
    supportPointer: Sortable.supportPointer !== false && 'PointerEvent' in window,
    emptyInsertThreshold: 5
  };
  PluginManager.initializePlugins(this, el, defaults); // Set default options

  for (var name in defaults) {
    !(name in options) && (options[name] = defaults[name]);
  }

  _prepareGroup(options); // Bind all private methods


  for (var fn in this) {
    if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
      this[fn] = this[fn].bind(this);
    }
  } // Setup drag mode


  this.nativeDraggable = options.forceFallback ? false : supportDraggable;

  if (this.nativeDraggable) {
    // Touch start threshold cannot be greater than the native dragstart threshold
    this.options.touchStartThreshold = 1;
  } // Bind events


  if (options.supportPointer) {
    on(el, 'pointerdown', this._onTapStart);
  } else {
    on(el, 'mousedown', this._onTapStart);
    on(el, 'touchstart', this._onTapStart);
  }

  if (this.nativeDraggable) {
    on(el, 'dragover', this);
    on(el, 'dragenter', this);
  }

  sortables.push(this.el); // Restore sorting

  options.store && options.store.get && this.sort(options.store.get(this) || []); // Add animation state manager

  _extends(this, AnimationStateManager());
}

Sortable.prototype =
/** @lends Sortable.prototype */
{
  constructor: Sortable,
  _isOutsideThisEl: function _isOutsideThisEl(target) {
    if (!this.el.contains(target) && target !== this.el) {
      lastTarget = null;
    }
  },
  _getDirection: function _getDirection(evt, target) {
    return typeof this.options.direction === 'function' ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
  },
  _onTapStart: function _onTapStart(
  /** Event|TouchEvent */
  evt) {
    if (!evt.cancelable) return;

    var _this = this,
        el = this.el,
        options = this.options,
        preventOnFilter = options.preventOnFilter,
        type = evt.type,
        touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === 'touch' && evt,
        target = (touch || evt).target,
        originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target,
        filter = options.filter;

    _saveInputCheckedState(el); // Don't trigger start event when an element is been dragged, otherwise the evt.oldindex always wrong when set option.group.


    if (dragEl) {
      return;
    }

    if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) {
      return; // only left button and enabled
    } // cancel dnd if original target is content editable


    if (originalTarget.isContentEditable) {
      return;
    }

    target = closest(target, options.draggable, el, false);

    if (target && target.animated) {
      return;
    }

    if (lastDownEl === target) {
      // Ignoring duplicate `down`
      return;
    } // Get the index of the dragged element within its parent


    oldIndex = index(target);
    oldDraggableIndex = index(target, options.draggable); // Check filter

    if (typeof filter === 'function') {
      if (filter.call(this, evt, target, this)) {
        _dispatchEvent({
          sortable: _this,
          rootEl: originalTarget,
          name: 'filter',
          targetEl: target,
          toEl: el,
          fromEl: el
        });

        pluginEvent('filter', _this, {
          evt: evt
        });
        preventOnFilter && evt.cancelable && evt.preventDefault();
        return; // cancel dnd
      }
    } else if (filter) {
      filter = filter.split(',').some(function (criteria) {
        criteria = closest(originalTarget, criteria.trim(), el, false);

        if (criteria) {
          _dispatchEvent({
            sortable: _this,
            rootEl: criteria,
            name: 'filter',
            targetEl: target,
            fromEl: el,
            toEl: el
          });

          pluginEvent('filter', _this, {
            evt: evt
          });
          return true;
        }
      });

      if (filter) {
        preventOnFilter && evt.cancelable && evt.preventDefault();
        return; // cancel dnd
      }
    }

    if (options.handle && !closest(originalTarget, options.handle, el, false)) {
      return;
    } // Prepare `dragstart`


    this._prepareDragStart(evt, touch, target);
  },
  _prepareDragStart: function _prepareDragStart(
  /** Event */
  evt,
  /** Touch */
  touch,
  /** HTMLElement */
  target) {
    var _this = this,
        el = _this.el,
        options = _this.options,
        ownerDocument = el.ownerDocument,
        dragStartFn;

    if (target && !dragEl && target.parentNode === el) {
      var dragRect = getRect(target);
      rootEl = el;
      dragEl = target;
      parentEl = dragEl.parentNode;
      nextEl = dragEl.nextSibling;
      lastDownEl = target;
      activeGroup = options.group;
      Sortable.dragged = dragEl;
      tapEvt = {
        target: dragEl,
        clientX: (touch || evt).clientX,
        clientY: (touch || evt).clientY
      };
      tapDistanceLeft = tapEvt.clientX - dragRect.left;
      tapDistanceTop = tapEvt.clientY - dragRect.top;
      this._lastX = (touch || evt).clientX;
      this._lastY = (touch || evt).clientY;
      dragEl.style['will-change'] = 'all';

      dragStartFn = function dragStartFn() {
        pluginEvent('delayEnded', _this, {
          evt: evt
        });

        if (Sortable.eventCanceled) {
          _this._onDrop();

          return;
        } // Delayed drag has been triggered
        // we can re-enable the events: touchmove/mousemove


        _this._disableDelayedDragEvents();

        if (!FireFox && _this.nativeDraggable) {
          dragEl.draggable = true;
        } // Bind the events: dragstart/dragend


        _this._triggerDragStart(evt, touch); // Drag start event


        _dispatchEvent({
          sortable: _this,
          name: 'choose',
          originalEvent: evt
        }); // Chosen item


        toggleClass(dragEl, options.chosenClass, true);
      }; // Disable "draggable"


      options.ignore.split(',').forEach(function (criteria) {
        find(dragEl, criteria.trim(), _disableDraggable);
      });
      on(ownerDocument, 'dragover', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'mousemove', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'touchmove', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'mouseup', _this._onDrop);
      on(ownerDocument, 'touchend', _this._onDrop);
      on(ownerDocument, 'touchcancel', _this._onDrop); // Make dragEl draggable (must be before delay for FireFox)

      if (FireFox && this.nativeDraggable) {
        this.options.touchStartThreshold = 4;
        dragEl.draggable = true;
      }

      pluginEvent('delayStart', this, {
        evt: evt
      }); // Delay is impossible for native DnD in Edge or IE

      if (options.delay && (!options.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
        if (Sortable.eventCanceled) {
          this._onDrop();

          return;
        } // If the user moves the pointer or let go the click or touch
        // before the delay has been reached:
        // disable the delayed drag


        on(ownerDocument, 'mouseup', _this._disableDelayedDrag);
        on(ownerDocument, 'touchend', _this._disableDelayedDrag);
        on(ownerDocument, 'touchcancel', _this._disableDelayedDrag);
        on(ownerDocument, 'mousemove', _this._delayedDragTouchMoveHandler);
        on(ownerDocument, 'touchmove', _this._delayedDragTouchMoveHandler);
        options.supportPointer && on(ownerDocument, 'pointermove', _this._delayedDragTouchMoveHandler);
        _this._dragStartTimer = setTimeout(dragStartFn, options.delay);
      } else {
        dragStartFn();
      }
    }
  },
  _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(
  /** TouchEvent|PointerEvent **/
  e) {
    var touch = e.touches ? e.touches[0] : e;

    if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
      this._disableDelayedDrag();
    }
  },
  _disableDelayedDrag: function _disableDelayedDrag() {
    dragEl && _disableDraggable(dragEl);
    clearTimeout(this._dragStartTimer);

    this._disableDelayedDragEvents();
  },
  _disableDelayedDragEvents: function _disableDelayedDragEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, 'mouseup', this._disableDelayedDrag);
    off(ownerDocument, 'touchend', this._disableDelayedDrag);
    off(ownerDocument, 'touchcancel', this._disableDelayedDrag);
    off(ownerDocument, 'mousemove', this._delayedDragTouchMoveHandler);
    off(ownerDocument, 'touchmove', this._delayedDragTouchMoveHandler);
    off(ownerDocument, 'pointermove', this._delayedDragTouchMoveHandler);
  },
  _triggerDragStart: function _triggerDragStart(
  /** Event */
  evt,
  /** Touch */
  touch) {
    touch = touch || evt.pointerType == 'touch' && evt;

    if (!this.nativeDraggable || touch) {
      if (this.options.supportPointer) {
        on(document, 'pointermove', this._onTouchMove);
      } else if (touch) {
        on(document, 'touchmove', this._onTouchMove);
      } else {
        on(document, 'mousemove', this._onTouchMove);
      }
    } else {
      on(dragEl, 'dragend', this);
      on(rootEl, 'dragstart', this._onDragStart);
    }

    try {
      if (document.selection) {
        // Timeout neccessary for IE9
        _nextTick(function () {
          document.selection.empty();
        });
      } else {
        window.getSelection().removeAllRanges();
      }
    } catch (err) {}
  },
  _dragStarted: function _dragStarted(fallback, evt) {
    awaitingDragStarted = false;

    if (rootEl && dragEl) {
      pluginEvent('dragStarted', this, {
        evt: evt
      });

      if (this.nativeDraggable) {
        on(document, 'dragover', _checkOutsideTargetEl);
      }

      var options = this.options; // Apply effect

      !fallback && toggleClass(dragEl, options.dragClass, false);
      toggleClass(dragEl, options.ghostClass, true);
      Sortable.active = this;
      fallback && this._appendGhost(); // Drag start event

      _dispatchEvent({
        sortable: this,
        name: 'start',
        originalEvent: evt
      });
    } else {
      this._nulling();
    }
  },
  _emulateDragOver: function _emulateDragOver() {
    if (touchEvt) {
      this._lastX = touchEvt.clientX;
      this._lastY = touchEvt.clientY;

      _hideGhostForTarget();

      var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
      var parent = target;

      while (target && target.shadowRoot) {
        target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
        if (target === parent) break;
        parent = target;
      }

      dragEl.parentNode[expando]._isOutsideThisEl(target);

      if (parent) {
        do {
          if (parent[expando]) {
            var inserted = void 0;
            inserted = parent[expando]._onDragOver({
              clientX: touchEvt.clientX,
              clientY: touchEvt.clientY,
              target: target,
              rootEl: parent
            });

            if (inserted && !this.options.dragoverBubble) {
              break;
            }
          }

          target = parent; // store last element
        }
        /* jshint boss:true */
        while (parent = parent.parentNode);
      }

      _unhideGhostForTarget();
    }
  },
  _onTouchMove: function _onTouchMove(
  /**TouchEvent*/
  evt) {
    if (tapEvt) {
      var options = this.options,
          fallbackTolerance = options.fallbackTolerance,
          fallbackOffset = options.fallbackOffset,
          touch = evt.touches ? evt.touches[0] : evt,
          ghostMatrix = ghostEl && matrix(ghostEl, true),
          scaleX = ghostEl && ghostMatrix && ghostMatrix.a,
          scaleY = ghostEl && ghostMatrix && ghostMatrix.d,
          relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent),
          dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1),
          dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1); // only set the status to dragging, when we are actually dragging

      if (!Sortable.active && !awaitingDragStarted) {
        if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) {
          return;
        }

        this._onDragStart(evt, true);
      }

      if (ghostEl) {
        if (ghostMatrix) {
          ghostMatrix.e += dx - (lastDx || 0);
          ghostMatrix.f += dy - (lastDy || 0);
        } else {
          ghostMatrix = {
            a: 1,
            b: 0,
            c: 0,
            d: 1,
            e: dx,
            f: dy
          };
        }

        var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
        css(ghostEl, 'webkitTransform', cssMatrix);
        css(ghostEl, 'mozTransform', cssMatrix);
        css(ghostEl, 'msTransform', cssMatrix);
        css(ghostEl, 'transform', cssMatrix);
        lastDx = dx;
        lastDy = dy;
        touchEvt = touch;
      }

      evt.cancelable && evt.preventDefault();
    }
  },
  _appendGhost: function _appendGhost() {
    // Bug if using scale(): https://stackoverflow.com/questions/2637058
    // Not being adjusted for
    if (!ghostEl) {
      var container = this.options.fallbackOnBody ? document.body : rootEl,
          rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container),
          options = this.options; // Position absolutely

      if (PositionGhostAbsolutely) {
        // Get relatively positioned parent
        ghostRelativeParent = container;

        while (css(ghostRelativeParent, 'position') === 'static' && css(ghostRelativeParent, 'transform') === 'none' && ghostRelativeParent !== document) {
          ghostRelativeParent = ghostRelativeParent.parentNode;
        }

        if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
          if (ghostRelativeParent === document) ghostRelativeParent = getWindowScrollingElement();
          rect.top += ghostRelativeParent.scrollTop;
          rect.left += ghostRelativeParent.scrollLeft;
        } else {
          ghostRelativeParent = getWindowScrollingElement();
        }

        ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
      }

      ghostEl = dragEl.cloneNode(true);
      toggleClass(ghostEl, options.ghostClass, false);
      toggleClass(ghostEl, options.fallbackClass, true);
      toggleClass(ghostEl, options.dragClass, true);
      css(ghostEl, 'transition', '');
      css(ghostEl, 'transform', '');
      css(ghostEl, 'box-sizing', 'border-box');
      css(ghostEl, 'margin', 0);
      css(ghostEl, 'top', rect.top);
      css(ghostEl, 'left', rect.left);
      css(ghostEl, 'width', rect.width);
      css(ghostEl, 'height', rect.height);
      css(ghostEl, 'opacity', '0.8');
      css(ghostEl, 'position', PositionGhostAbsolutely ? 'absolute' : 'fixed');
      css(ghostEl, 'zIndex', '100000');
      css(ghostEl, 'pointerEvents', 'none');
      Sortable.ghost = ghostEl;
      container.appendChild(ghostEl); // Set transform-origin

      css(ghostEl, 'transform-origin', tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + '% ' + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + '%');
    }
  },
  _onDragStart: function _onDragStart(
  /**Event*/
  evt,
  /**boolean*/
  fallback) {
    var _this = this;

    var dataTransfer = evt.dataTransfer;
    var options = _this.options;
    pluginEvent('dragStart', this, {
      evt: evt
    });

    if (Sortable.eventCanceled) {
      this._onDrop();

      return;
    }

    pluginEvent('setupClone', this);

    if (!Sortable.eventCanceled) {
      cloneEl = clone(dragEl);
      cloneEl.draggable = false;
      cloneEl.style['will-change'] = '';

      this._hideClone();

      toggleClass(cloneEl, this.options.chosenClass, false);
      Sortable.clone = cloneEl;
    } // #1143: IFrame support workaround


    _this.cloneId = _nextTick(function () {
      pluginEvent('clone', _this);
      if (Sortable.eventCanceled) return;

      if (!_this.options.removeCloneOnHide) {
        rootEl.insertBefore(cloneEl, dragEl);
      }

      _this._hideClone();

      _dispatchEvent({
        sortable: _this,
        name: 'clone'
      });
    });
    !fallback && toggleClass(dragEl, options.dragClass, true); // Set proper drop events

    if (fallback) {
      ignoreNextClick = true;
      _this._loopId = setInterval(_this._emulateDragOver, 50);
    } else {
      // Undo what was set in _prepareDragStart before drag started
      off(document, 'mouseup', _this._onDrop);
      off(document, 'touchend', _this._onDrop);
      off(document, 'touchcancel', _this._onDrop);

      if (dataTransfer) {
        dataTransfer.effectAllowed = 'move';
        options.setData && options.setData.call(_this, dataTransfer, dragEl);
      }

      on(document, 'drop', _this); // #1276 fix:

      css(dragEl, 'transform', 'translateZ(0)');
    }

    awaitingDragStarted = true;
    _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
    on(document, 'selectstart', _this);
    moved = true;

    if (Safari) {
      css(document.body, 'user-select', 'none');
    }
  },
  // Returns true - if no further action is needed (either inserted or another condition)
  _onDragOver: function _onDragOver(
  /**Event*/
  evt) {
    var el = this.el,
        target = evt.target,
        dragRect,
        targetRect,
        revert,
        options = this.options,
        group = options.group,
        activeSortable = Sortable.active,
        isOwner = activeGroup === group,
        canSort = options.sort,
        fromSortable = putSortable || activeSortable,
        vertical,
        _this = this,
        completedFired = false;

    if (_silent) return;

    function dragOverEvent(name, extra) {
      pluginEvent(name, _this, _objectSpread({
        evt: evt,
        isOwner: isOwner,
        axis: vertical ? 'vertical' : 'horizontal',
        revert: revert,
        dragRect: dragRect,
        targetRect: targetRect,
        canSort: canSort,
        fromSortable: fromSortable,
        target: target,
        completed: completed,
        onMove: function onMove(target, after) {
          return _onMove(rootEl, el, dragEl, dragRect, target, getRect(target), evt, after);
        },
        changed: changed
      }, extra));
    } // Capture animation state


    function capture() {
      dragOverEvent('dragOverAnimationCapture');

      _this.captureAnimationState();

      if (_this !== fromSortable) {
        fromSortable.captureAnimationState();
      }
    } // Return invocation when dragEl is inserted (or completed)


    function completed(insertion) {
      dragOverEvent('dragOverCompleted', {
        insertion: insertion
      });

      if (insertion) {
        // Clones must be hidden before folding animation to capture dragRectAbsolute properly
        if (isOwner) {
          activeSortable._hideClone();
        } else {
          activeSortable._showClone(_this);
        }

        if (_this !== fromSortable) {
          // Set ghost class to new sortable's ghost class
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
          toggleClass(dragEl, options.ghostClass, true);
        }

        if (putSortable !== _this && _this !== Sortable.active) {
          putSortable = _this;
        } else if (_this === Sortable.active && putSortable) {
          putSortable = null;
        } // Animation


        if (fromSortable === _this) {
          _this._ignoreWhileAnimating = target;
        }

        _this.animateAll(function () {
          dragOverEvent('dragOverAnimationComplete');
          _this._ignoreWhileAnimating = null;
        });

        if (_this !== fromSortable) {
          fromSortable.animateAll();
          fromSortable._ignoreWhileAnimating = null;
        }
      } // Null lastTarget if it is not inside a previously swapped element


      if (target === dragEl && !dragEl.animated || target === el && !target.animated) {
        lastTarget = null;
      } // no bubbling and not fallback


      if (!options.dragoverBubble && !evt.rootEl && target !== document) {
        dragEl.parentNode[expando]._isOutsideThisEl(evt.target); // Do not detect for empty insert if already inserted


        !insertion && nearestEmptyInsertDetectEvent(evt);
      }

      !options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
      return completedFired = true;
    } // Call when dragEl has been inserted


    function changed() {
      newIndex = index(dragEl);
      newDraggableIndex = index(dragEl, options.draggable);

      _dispatchEvent({
        sortable: _this,
        name: 'change',
        toEl: el,
        newIndex: newIndex,
        newDraggableIndex: newDraggableIndex,
        originalEvent: evt
      });
    }

    if (evt.preventDefault !== void 0) {
      evt.cancelable && evt.preventDefault();
    }

    target = closest(target, options.draggable, el, true);
    dragOverEvent('dragOver');
    if (Sortable.eventCanceled) return completedFired;

    if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) {
      return completed(false);
    }

    ignoreNextClick = false;

    if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = !rootEl.contains(dragEl)) // Reverting item into the original list
    : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
      vertical = this._getDirection(evt, target) === 'vertical';
      dragRect = getRect(dragEl);
      dragOverEvent('dragOverValid');
      if (Sortable.eventCanceled) return completedFired;

      if (revert) {
        parentEl = rootEl; // actualization

        capture();

        this._hideClone();

        dragOverEvent('revert');

        if (!Sortable.eventCanceled) {
          if (nextEl) {
            rootEl.insertBefore(dragEl, nextEl);
          } else {
            rootEl.appendChild(dragEl);
          }
        }

        return completed(true);
      }

      var elLastChild = lastChild(el, options.draggable);

      if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
        // If already at end of list: Do not insert
        if (elLastChild === dragEl) {
          return completed(false);
        } // assign target only if condition is true


        if (elLastChild && el === evt.target) {
          target = elLastChild;
        }

        if (target) {
          targetRect = getRect(target);
        }

        if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
          capture();
          el.appendChild(dragEl);
          parentEl = el; // actualization

          changed();
          return completed(true);
        }
      } else if (target.parentNode === el) {
        targetRect = getRect(target);
        var direction = 0,
            targetBeforeFirstSwap,
            differentLevel = dragEl.parentNode !== el,
            differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical),
            side1 = vertical ? 'top' : 'left',
            scrolledPastTop = isScrolledPast(target, 'top', 'top') || isScrolledPast(dragEl, 'top', 'top'),
            scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;

        if (lastTarget !== target) {
          targetBeforeFirstSwap = targetRect[side1];
          pastFirstInvertThresh = false;
          isCircumstantialInvert = !differentRowCol && options.invertSwap || differentLevel;
        }

        direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
        var sibling;

        if (direction !== 0) {
          // Check if target is beside dragEl in respective direction (ignoring hidden elements)
          var dragIndex = index(dragEl);

          do {
            dragIndex -= direction;
            sibling = parentEl.children[dragIndex];
          } while (sibling && (css(sibling, 'display') === 'none' || sibling === ghostEl));
        } // If dragEl is already beside target: Do not insert


        if (direction === 0 || sibling === target) {
          return completed(false);
        }

        lastTarget = target;
        lastDirection = direction;
        var nextSibling = target.nextElementSibling,
            after = false;
        after = direction === 1;

        var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);

        if (moveVector !== false) {
          if (moveVector === 1 || moveVector === -1) {
            after = moveVector === 1;
          }

          _silent = true;
          setTimeout(_unsilent, 30);
          capture();

          if (after && !nextSibling) {
            el.appendChild(dragEl);
          } else {
            target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
          } // Undo chrome's scroll adjustment (has no effect on other browsers)


          if (scrolledPastTop) {
            scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
          }

          parentEl = dragEl.parentNode; // actualization
          // must be done before animation

          if (targetBeforeFirstSwap !== undefined && !isCircumstantialInvert) {
            targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
          }

          changed();
          return completed(true);
        }
      }

      if (el.contains(dragEl)) {
        return completed(false);
      }
    }

    return false;
  },
  _ignoreWhileAnimating: null,
  _offMoveEvents: function _offMoveEvents() {
    off(document, 'mousemove', this._onTouchMove);
    off(document, 'touchmove', this._onTouchMove);
    off(document, 'pointermove', this._onTouchMove);
    off(document, 'dragover', nearestEmptyInsertDetectEvent);
    off(document, 'mousemove', nearestEmptyInsertDetectEvent);
    off(document, 'touchmove', nearestEmptyInsertDetectEvent);
  },
  _offUpEvents: function _offUpEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, 'mouseup', this._onDrop);
    off(ownerDocument, 'touchend', this._onDrop);
    off(ownerDocument, 'pointerup', this._onDrop);
    off(ownerDocument, 'touchcancel', this._onDrop);
    off(document, 'selectstart', this);
  },
  _onDrop: function _onDrop(
  /**Event*/
  evt) {
    var el = this.el,
        options = this.options; // Get the index of the dragged element within its parent

    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options.draggable);
    pluginEvent('drop', this, {
      evt: evt
    });
    parentEl = dragEl && dragEl.parentNode; // Get again after plugin event

    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options.draggable);

    if (Sortable.eventCanceled) {
      this._nulling();

      return;
    }

    awaitingDragStarted = false;
    isCircumstantialInvert = false;
    pastFirstInvertThresh = false;
    clearInterval(this._loopId);
    clearTimeout(this._dragStartTimer);

    _cancelNextTick(this.cloneId);

    _cancelNextTick(this._dragStartId); // Unbind events


    if (this.nativeDraggable) {
      off(document, 'drop', this);
      off(el, 'dragstart', this._onDragStart);
    }

    this._offMoveEvents();

    this._offUpEvents();

    if (Safari) {
      css(document.body, 'user-select', '');
    }

    css(dragEl, 'transform', '');

    if (evt) {
      if (moved) {
        evt.cancelable && evt.preventDefault();
        !options.dropBubble && evt.stopPropagation();
      }

      ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);

      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
        // Remove clone(s)
        cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
      }

      if (dragEl) {
        if (this.nativeDraggable) {
          off(dragEl, 'dragend', this);
        }

        _disableDraggable(dragEl);

        dragEl.style['will-change'] = ''; // Remove classes
        // ghostClass is added in dragStarted

        if (moved && !awaitingDragStarted) {
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
        }

        toggleClass(dragEl, this.options.chosenClass, false); // Drag stop event

        _dispatchEvent({
          sortable: this,
          name: 'unchoose',
          toEl: parentEl,
          newIndex: null,
          newDraggableIndex: null,
          originalEvent: evt
        });

        if (rootEl !== parentEl) {
          if (newIndex >= 0) {
            // Add event
            _dispatchEvent({
              rootEl: parentEl,
              name: 'add',
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            }); // Remove event


            _dispatchEvent({
              sortable: this,
              name: 'remove',
              toEl: parentEl,
              originalEvent: evt
            }); // drag from one list and drop into another


            _dispatchEvent({
              rootEl: parentEl,
              name: 'sort',
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            });

            _dispatchEvent({
              sortable: this,
              name: 'sort',
              toEl: parentEl,
              originalEvent: evt
            });
          }

          putSortable && putSortable.save();
        } else {
          if (newIndex !== oldIndex) {
            if (newIndex >= 0) {
              // drag & drop within the same list
              _dispatchEvent({
                sortable: this,
                name: 'update',
                toEl: parentEl,
                originalEvent: evt
              });

              _dispatchEvent({
                sortable: this,
                name: 'sort',
                toEl: parentEl,
                originalEvent: evt
              });
            }
          }
        }

        if (Sortable.active) {
          /* jshint eqnull:true */
          if (newIndex == null || newIndex === -1) {
            newIndex = oldIndex;
            newDraggableIndex = oldDraggableIndex;
          }

          _dispatchEvent({
            sortable: this,
            name: 'end',
            toEl: parentEl,
            originalEvent: evt
          }); // Save sorting


          this.save();
        }
      }
    }

    this._nulling();
  },
  _nulling: function _nulling() {
    pluginEvent('nulling', this);
    rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable.dragged = Sortable.ghost = Sortable.clone = Sortable.active = null;
    savedInputChecked.forEach(function (el) {
      el.checked = true;
    });
    savedInputChecked.length = lastDx = lastDy = 0;
  },
  handleEvent: function handleEvent(
  /**Event*/
  evt) {
    switch (evt.type) {
      case 'drop':
      case 'dragend':
        this._onDrop(evt);

        break;

      case 'dragenter':
      case 'dragover':
        if (dragEl) {
          this._onDragOver(evt);

          _globalDragOver(evt);
        }

        break;

      case 'selectstart':
        evt.preventDefault();
        break;
    }
  },

  /**
   * Serializes the item into an array of string.
   * @returns {String[]}
   */
  toArray: function toArray() {
    var order = [],
        el,
        children = this.el.children,
        i = 0,
        n = children.length,
        options = this.options;

    for (; i < n; i++) {
      el = children[i];

      if (closest(el, options.draggable, this.el, false)) {
        order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
      }
    }

    return order;
  },

  /**
   * Sorts the elements according to the array.
   * @param  {String[]}  order  order of the items
   */
  sort: function sort(order) {
    var items = {},
        rootEl = this.el;
    this.toArray().forEach(function (id, i) {
      var el = rootEl.children[i];

      if (closest(el, this.options.draggable, rootEl, false)) {
        items[id] = el;
      }
    }, this);
    order.forEach(function (id) {
      if (items[id]) {
        rootEl.removeChild(items[id]);
        rootEl.appendChild(items[id]);
      }
    });
  },

  /**
   * Save the current sorting
   */
  save: function save() {
    var store = this.options.store;
    store && store.set && store.set(this);
  },

  /**
   * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
   * @param   {HTMLElement}  el
   * @param   {String}       [selector]  default: `options.draggable`
   * @returns {HTMLElement|null}
   */
  closest: function closest$1(el, selector) {
    return closest(el, selector || this.options.draggable, this.el, false);
  },

  /**
   * Set/get option
   * @param   {string} name
   * @param   {*}      [value]
   * @returns {*}
   */
  option: function option(name, value) {
    var options = this.options;

    if (value === void 0) {
      return options[name];
    } else {
      var modifiedValue = PluginManager.modifyOption(this, name, value);

      if (typeof modifiedValue !== 'undefined') {
        options[name] = modifiedValue;
      } else {
        options[name] = value;
      }

      if (name === 'group') {
        _prepareGroup(options);
      }
    }
  },

  /**
   * Destroy
   */
  destroy: function destroy() {
    pluginEvent('destroy', this);
    var el = this.el;
    el[expando] = null;
    off(el, 'mousedown', this._onTapStart);
    off(el, 'touchstart', this._onTapStart);
    off(el, 'pointerdown', this._onTapStart);

    if (this.nativeDraggable) {
      off(el, 'dragover', this);
      off(el, 'dragenter', this);
    } // Remove draggable attributes


    Array.prototype.forEach.call(el.querySelectorAll('[draggable]'), function (el) {
      el.removeAttribute('draggable');
    });

    this._onDrop();

    this._disableDelayedDragEvents();

    sortables.splice(sortables.indexOf(this.el), 1);
    this.el = el = null;
  },
  _hideClone: function _hideClone() {
    if (!cloneHidden) {
      pluginEvent('hideClone', this);
      if (Sortable.eventCanceled) return;
      css(cloneEl, 'display', 'none');

      if (this.options.removeCloneOnHide && cloneEl.parentNode) {
        cloneEl.parentNode.removeChild(cloneEl);
      }

      cloneHidden = true;
    }
  },
  _showClone: function _showClone(putSortable) {
    if (putSortable.lastPutMode !== 'clone') {
      this._hideClone();

      return;
    }

    if (cloneHidden) {
      pluginEvent('showClone', this);
      if (Sortable.eventCanceled) return; // show clone at dragEl or original position

      if (rootEl.contains(dragEl) && !this.options.group.revertClone) {
        rootEl.insertBefore(cloneEl, dragEl);
      } else if (nextEl) {
        rootEl.insertBefore(cloneEl, nextEl);
      } else {
        rootEl.appendChild(cloneEl);
      }

      if (this.options.group.revertClone) {
        this.animate(dragEl, cloneEl);
      }

      css(cloneEl, 'display', '');
      cloneHidden = false;
    }
  }
};

function _globalDragOver(
/**Event*/
evt) {
  if (evt.dataTransfer) {
    evt.dataTransfer.dropEffect = 'move';
  }

  evt.cancelable && evt.preventDefault();
}

function _onMove(fromEl, toEl, dragEl, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
  var evt,
      sortable = fromEl[expando],
      onMoveFn = sortable.options.onMove,
      retVal; // Support for new CustomEvent feature

  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent('move', {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent('Event');
    evt.initEvent('move', true, true);
  }

  evt.to = toEl;
  evt.from = fromEl;
  evt.dragged = dragEl;
  evt.draggedRect = dragRect;
  evt.related = targetEl || toEl;
  evt.relatedRect = targetRect || getRect(toEl);
  evt.willInsertAfter = willInsertAfter;
  evt.originalEvent = originalEvent;
  fromEl.dispatchEvent(evt);

  if (onMoveFn) {
    retVal = onMoveFn.call(sortable, evt, originalEvent);
  }

  return retVal;
}

function _disableDraggable(el) {
  el.draggable = false;
}

function _unsilent() {
  _silent = false;
}

function _ghostIsLast(evt, vertical, sortable) {
  var rect = getRect(lastChild(sortable.el, sortable.options.draggable));
  var spacer = 10;
  return vertical ? evt.clientX > rect.right + spacer || evt.clientX <= rect.right && evt.clientY > rect.bottom && evt.clientX >= rect.left : evt.clientX > rect.right && evt.clientY > rect.top || evt.clientX <= rect.right && evt.clientY > rect.bottom + spacer;
}

function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
  var mouseOnAxis = vertical ? evt.clientY : evt.clientX,
      targetLength = vertical ? targetRect.height : targetRect.width,
      targetS1 = vertical ? targetRect.top : targetRect.left,
      targetS2 = vertical ? targetRect.bottom : targetRect.right,
      invert = false;

  if (!invertSwap) {
    // Never invert or create dragEl shadow when target movemenet causes mouse to move past the end of regular swapThreshold
    if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
      // multiplied only by swapThreshold because mouse will already be inside target by (1 - threshold) * targetLength / 2
      // check if past first invert threshold on side opposite of lastDirection
      if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
        // past first invert threshold, do not restrict inverted threshold to dragEl shadow
        pastFirstInvertThresh = true;
      }

      if (!pastFirstInvertThresh) {
        // dragEl shadow (target move distance shadow)
        if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance // over dragEl shadow
        : mouseOnAxis > targetS2 - targetMoveDistance) {
          return -lastDirection;
        }
      } else {
        invert = true;
      }
    } else {
      // Regular
      if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
        return _getInsertDirection(target);
      }
    }
  }

  invert = invert || invertSwap;

  if (invert) {
    // Invert of regular
    if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
      return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
    }
  }

  return 0;
}
/**
 * Gets the direction dragEl must be swapped relative to target in order to make it
 * seem that dragEl has been "inserted" into that element's position
 * @param  {HTMLElement} target       The target whose position dragEl is being inserted at
 * @return {Number}                   Direction dragEl must be swapped
 */


function _getInsertDirection(target) {
  if (index(dragEl) < index(target)) {
    return 1;
  } else {
    return -1;
  }
}
/**
 * Generate id
 * @param   {HTMLElement} el
 * @returns {String}
 * @private
 */


function _generateId(el) {
  var str = el.tagName + el.className + el.src + el.href + el.textContent,
      i = str.length,
      sum = 0;

  while (i--) {
    sum += str.charCodeAt(i);
  }

  return sum.toString(36);
}

function _saveInputCheckedState(root) {
  savedInputChecked.length = 0;
  var inputs = root.getElementsByTagName('input');
  var idx = inputs.length;

  while (idx--) {
    var el = inputs[idx];
    el.checked && savedInputChecked.push(el);
  }
}

function _nextTick(fn) {
  return setTimeout(fn, 0);
}

function _cancelNextTick(id) {
  return clearTimeout(id);
} // Fixed #973:


if (documentExists) {
  on(document, 'touchmove', function (evt) {
    if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
      evt.preventDefault();
    }
  });
} // Export utils


Sortable.utils = {
  on: on,
  off: off,
  css: css,
  find: find,
  is: function is(el, selector) {
    return !!closest(el, selector, el, false);
  },
  extend: extend,
  throttle: throttle,
  closest: closest,
  toggleClass: toggleClass,
  clone: clone,
  index: index,
  nextTick: _nextTick,
  cancelNextTick: _cancelNextTick,
  detectDirection: _detectDirection,
  getChild: getChild
};
/**
 * Get the Sortable instance of an element
 * @param  {HTMLElement} element The element
 * @return {Sortable|undefined}         The instance of Sortable
 */

Sortable.get = function (element) {
  return element[expando];
};
/**
 * Mount a plugin to Sortable
 * @param  {...SortablePlugin|SortablePlugin[]} plugins       Plugins being mounted
 */


Sortable.mount = function () {
  for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) {
    plugins[_key] = arguments[_key];
  }

  if (plugins[0].constructor === Array) plugins = plugins[0];
  plugins.forEach(function (plugin) {
    if (!plugin.prototype || !plugin.prototype.constructor) {
      throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
    }

    if (plugin.utils) Sortable.utils = _objectSpread({}, Sortable.utils, plugin.utils);
    PluginManager.mount(plugin);
  });
};
/**
 * Create sortable instance
 * @param {HTMLElement}  el
 * @param {Object}      [options]
 */


Sortable.create = function (el, options) {
  return new Sortable(el, options);
}; // Export


Sortable.version = version;
var autoScrolls = [],
    scrollEl,
    scrollRootEl,
    scrolling = false,
    lastAutoScrollX,
    lastAutoScrollY,
    touchEvt$1,
    pointerElemChangedInterval;

function AutoScrollPlugin() {
  function AutoScroll() {
    this.defaults = {
      scroll: true,
      scrollSensitivity: 30,
      scrollSpeed: 10,
      bubbleScroll: true
    }; // Bind all private methods

    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    }
  }

  AutoScroll.prototype = {
    dragStarted: function dragStarted(_ref) {
      var originalEvent = _ref.originalEvent;

      if (this.sortable.nativeDraggable) {
        on(document, 'dragover', this._handleAutoScroll);
      } else {
        if (this.options.supportPointer) {
          on(document, 'pointermove', this._handleFallbackAutoScroll);
        } else if (originalEvent.touches) {
          on(document, 'touchmove', this._handleFallbackAutoScroll);
        } else {
          on(document, 'mousemove', this._handleFallbackAutoScroll);
        }
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref2) {
      var originalEvent = _ref2.originalEvent; // For when bubbling is canceled and using fallback (fallback 'touchmove' always reached)

      if (!this.options.dragOverBubble && !originalEvent.rootEl) {
        this._handleAutoScroll(originalEvent);
      }
    },
    drop: function drop() {
      if (this.sortable.nativeDraggable) {
        off(document, 'dragover', this._handleAutoScroll);
      } else {
        off(document, 'pointermove', this._handleFallbackAutoScroll);
        off(document, 'touchmove', this._handleFallbackAutoScroll);
        off(document, 'mousemove', this._handleFallbackAutoScroll);
      }

      clearPointerElemChangedInterval();
      clearAutoScrolls();
      cancelThrottle();
    },
    nulling: function nulling() {
      touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
      autoScrolls.length = 0;
    },
    _handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
      this._handleAutoScroll(evt, true);
    },
    _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
      var _this = this;

      var x = (evt.touches ? evt.touches[0] : evt).clientX,
          y = (evt.touches ? evt.touches[0] : evt).clientY,
          elem = document.elementFromPoint(x, y);
      touchEvt$1 = evt; // IE does not seem to have native autoscroll,
      // Edge's autoscroll seems too conditional,
      // MACOS Safari does not have autoscroll,
      // Firefox and Chrome are good

      if (fallback || Edge || IE11OrLess || Safari) {
        autoScroll(evt, this.options, elem, fallback); // Listener for pointer element change

        var ogElemScroller = getParentAutoScrollElement(elem, true);

        if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
          pointerElemChangedInterval && clearPointerElemChangedInterval(); // Detect for pointer elem change, emulating native DnD behaviour

          pointerElemChangedInterval = setInterval(function () {
            var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);

            if (newElem !== ogElemScroller) {
              ogElemScroller = newElem;
              clearAutoScrolls();
            }

            autoScroll(evt, _this.options, newElem, fallback);
          }, 10);
          lastAutoScrollX = x;
          lastAutoScrollY = y;
        }
      } else {
        // if DnD is enabled (and browser has good autoscrolling), first autoscroll will already scroll, so get parent autoscroll of first autoscroll
        if (!this.options.bubbleScroll || getParentAutoScrollElement(elem, true) === getWindowScrollingElement()) {
          clearAutoScrolls();
          return;
        }

        autoScroll(evt, this.options, getParentAutoScrollElement(elem, false), false);
      }
    }
  };
  return _extends(AutoScroll, {
    pluginName: 'scroll',
    initializeByDefault: true
  });
}

function clearAutoScrolls() {
  autoScrolls.forEach(function (autoScroll) {
    clearInterval(autoScroll.pid);
  });
  autoScrolls = [];
}

function clearPointerElemChangedInterval() {
  clearInterval(pointerElemChangedInterval);
}

var autoScroll = throttle(function (evt, options, rootEl, isFallback) {
  // Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=505521
  if (!options.scroll) return;
  var x = (evt.touches ? evt.touches[0] : evt).clientX,
      y = (evt.touches ? evt.touches[0] : evt).clientY,
      sens = options.scrollSensitivity,
      speed = options.scrollSpeed,
      winScroller = getWindowScrollingElement();
  var scrollThisInstance = false,
      scrollCustomFn; // New scroll root, set scrollEl

  if (scrollRootEl !== rootEl) {
    scrollRootEl = rootEl;
    clearAutoScrolls();
    scrollEl = options.scroll;
    scrollCustomFn = options.scrollFn;

    if (scrollEl === true) {
      scrollEl = getParentAutoScrollElement(rootEl, true);
    }
  }

  var layersOut = 0;
  var currentParent = scrollEl;

  do {
    var el = currentParent,
        rect = getRect(el),
        top = rect.top,
        bottom = rect.bottom,
        left = rect.left,
        right = rect.right,
        width = rect.width,
        height = rect.height,
        canScrollX = void 0,
        canScrollY = void 0,
        scrollWidth = el.scrollWidth,
        scrollHeight = el.scrollHeight,
        elCSS = css(el),
        scrollPosX = el.scrollLeft,
        scrollPosY = el.scrollTop;

    if (el === winScroller) {
      canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll' || elCSS.overflowX === 'visible');
      canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll' || elCSS.overflowY === 'visible');
    } else {
      canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll');
      canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll');
    }

    var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
    var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);

    if (!autoScrolls[layersOut]) {
      for (var i = 0; i <= layersOut; i++) {
        if (!autoScrolls[i]) {
          autoScrolls[i] = {};
        }
      }
    }

    if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
      autoScrolls[layersOut].el = el;
      autoScrolls[layersOut].vx = vx;
      autoScrolls[layersOut].vy = vy;
      clearInterval(autoScrolls[layersOut].pid);

      if (vx != 0 || vy != 0) {
        scrollThisInstance = true;
        /* jshint loopfunc:true */

        autoScrolls[layersOut].pid = setInterval(function () {
          // emulate drag over during autoscroll (fallback), emulating native DnD behaviour
          if (isFallback && this.layer === 0) {
            Sortable.active._onTouchMove(touchEvt$1); // To move ghost if it is positioned absolutely

          }

          var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
          var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;

          if (typeof scrollCustomFn === 'function') {
            if (scrollCustomFn.call(Sortable.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== 'continue') {
              return;
            }
          }

          scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
        }.bind({
          layer: layersOut
        }), 24);
      }
    }

    layersOut++;
  } while (options.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));

  scrolling = scrollThisInstance; // in case another function catches scrolling as false in between when it is not
}, 30);

var drop = function drop(_ref) {
  var originalEvent = _ref.originalEvent,
      putSortable = _ref.putSortable,
      dragEl = _ref.dragEl,
      activeSortable = _ref.activeSortable,
      dispatchSortableEvent = _ref.dispatchSortableEvent,
      hideGhostForTarget = _ref.hideGhostForTarget,
      unhideGhostForTarget = _ref.unhideGhostForTarget;
  if (!originalEvent) return;
  var toSortable = putSortable || activeSortable;
  hideGhostForTarget();
  var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
  var target = document.elementFromPoint(touch.clientX, touch.clientY);
  unhideGhostForTarget();

  if (toSortable && !toSortable.el.contains(target)) {
    dispatchSortableEvent('spill');
    this.onSpill({
      dragEl: dragEl,
      putSortable: putSortable
    });
  }
};

function Revert() {}

Revert.prototype = {
  startIndex: null,
  dragStart: function dragStart(_ref2) {
    var oldDraggableIndex = _ref2.oldDraggableIndex;
    this.startIndex = oldDraggableIndex;
  },
  onSpill: function onSpill(_ref3) {
    var dragEl = _ref3.dragEl,
        putSortable = _ref3.putSortable;
    this.sortable.captureAnimationState();

    if (putSortable) {
      putSortable.captureAnimationState();
    }

    var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);

    if (nextSibling) {
      this.sortable.el.insertBefore(dragEl, nextSibling);
    } else {
      this.sortable.el.appendChild(dragEl);
    }

    this.sortable.animateAll();

    if (putSortable) {
      putSortable.animateAll();
    }
  },
  drop: drop
};

_extends(Revert, {
  pluginName: 'revertOnSpill'
});

function Remove() {}

Remove.prototype = {
  onSpill: function onSpill(_ref4) {
    var dragEl = _ref4.dragEl,
        putSortable = _ref4.putSortable;
    var parentSortable = putSortable || this.sortable;
    parentSortable.captureAnimationState();
    dragEl.parentNode && dragEl.parentNode.removeChild(dragEl);
    parentSortable.animateAll();
  },
  drop: drop
};

_extends(Remove, {
  pluginName: 'removeOnSpill'
});

var lastSwapEl;

function SwapPlugin() {
  function Swap() {
    this.defaults = {
      swapClass: 'sortable-swap-highlight'
    };
  }

  Swap.prototype = {
    dragStart: function dragStart(_ref) {
      var dragEl = _ref.dragEl;
      lastSwapEl = dragEl;
    },
    dragOverValid: function dragOverValid(_ref2) {
      var completed = _ref2.completed,
          target = _ref2.target,
          onMove = _ref2.onMove,
          activeSortable = _ref2.activeSortable,
          changed = _ref2.changed,
          cancel = _ref2.cancel;
      if (!activeSortable.options.swap) return;
      var el = this.sortable.el,
          options = this.options;

      if (target && target !== el) {
        var prevSwapEl = lastSwapEl;

        if (onMove(target) !== false) {
          toggleClass(target, options.swapClass, true);
          lastSwapEl = target;
        } else {
          lastSwapEl = null;
        }

        if (prevSwapEl && prevSwapEl !== lastSwapEl) {
          toggleClass(prevSwapEl, options.swapClass, false);
        }
      }

      changed();
      completed(true);
      cancel();
    },
    drop: function drop(_ref3) {
      var activeSortable = _ref3.activeSortable,
          putSortable = _ref3.putSortable,
          dragEl = _ref3.dragEl;
      var toSortable = putSortable || this.sortable;
      var options = this.options;
      lastSwapEl && toggleClass(lastSwapEl, options.swapClass, false);

      if (lastSwapEl && (options.swap || putSortable && putSortable.options.swap)) {
        if (dragEl !== lastSwapEl) {
          toSortable.captureAnimationState();
          if (toSortable !== activeSortable) activeSortable.captureAnimationState();
          swapNodes(dragEl, lastSwapEl);
          toSortable.animateAll();
          if (toSortable !== activeSortable) activeSortable.animateAll();
        }
      }
    },
    nulling: function nulling() {
      lastSwapEl = null;
    }
  };
  return _extends(Swap, {
    pluginName: 'swap',
    eventProperties: function eventProperties() {
      return {
        swapItem: lastSwapEl
      };
    }
  });
}

function swapNodes(n1, n2) {
  var p1 = n1.parentNode,
      p2 = n2.parentNode,
      i1,
      i2;
  if (!p1 || !p2 || p1.isEqualNode(n2) || p2.isEqualNode(n1)) return;
  i1 = index(n1);
  i2 = index(n2);

  if (p1.isEqualNode(p2) && i1 < i2) {
    i2++;
  }

  p1.insertBefore(n2, p1.children[i1]);
  p2.insertBefore(n1, p2.children[i2]);
}

var multiDragElements = [],
    multiDragClones = [],
    lastMultiDragSelect,
    // for selection with modifier key down (SHIFT)
multiDragSortable,
    initialFolding = false,
    // Initial multi-drag fold when drag started
folding = false,
    // Folding any other time
dragStarted = false,
    dragEl$1,
    clonesFromRect,
    clonesHidden;

function MultiDragPlugin() {
  function MultiDrag(sortable) {
    // Bind all private methods
    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    }

    if (sortable.options.supportPointer) {
      on(document, 'pointerup', this._deselectMultiDrag);
    } else {
      on(document, 'mouseup', this._deselectMultiDrag);
      on(document, 'touchend', this._deselectMultiDrag);
    }

    on(document, 'keydown', this._checkKeyDown);
    on(document, 'keyup', this._checkKeyUp);
    this.defaults = {
      selectedClass: 'sortable-selected',
      multiDragKey: null,
      setData: function setData(dataTransfer, dragEl) {
        var data = '';

        if (multiDragElements.length && multiDragSortable === sortable) {
          multiDragElements.forEach(function (multiDragElement, i) {
            data += (!i ? '' : ', ') + multiDragElement.textContent;
          });
        } else {
          data = dragEl.textContent;
        }

        dataTransfer.setData('Text', data);
      }
    };
  }

  MultiDrag.prototype = {
    multiDragKeyDown: false,
    isMultiDrag: false,
    delayStartGlobal: function delayStartGlobal(_ref) {
      var dragged = _ref.dragEl;
      dragEl$1 = dragged;
    },
    delayEnded: function delayEnded() {
      this.isMultiDrag = ~multiDragElements.indexOf(dragEl$1);
    },
    setupClone: function setupClone(_ref2) {
      var sortable = _ref2.sortable,
          cancel = _ref2.cancel;
      if (!this.isMultiDrag) return;

      for (var i = 0; i < multiDragElements.length; i++) {
        multiDragClones.push(clone(multiDragElements[i]));
        multiDragClones[i].sortableIndex = multiDragElements[i].sortableIndex;
        multiDragClones[i].draggable = false;
        multiDragClones[i].style['will-change'] = '';
        toggleClass(multiDragClones[i], this.options.selectedClass, false);
        multiDragElements[i] === dragEl$1 && toggleClass(multiDragClones[i], this.options.chosenClass, false);
      }

      sortable._hideClone();

      cancel();
    },
    clone: function clone(_ref3) {
      var sortable = _ref3.sortable,
          rootEl = _ref3.rootEl,
          dispatchSortableEvent = _ref3.dispatchSortableEvent,
          cancel = _ref3.cancel;
      if (!this.isMultiDrag) return;

      if (!this.options.removeCloneOnHide) {
        if (multiDragElements.length && multiDragSortable === sortable) {
          insertMultiDragClones(true, rootEl);
          dispatchSortableEvent('clone');
          cancel();
        }
      }
    },
    showClone: function showClone(_ref4) {
      var cloneNowShown = _ref4.cloneNowShown,
          rootEl = _ref4.rootEl,
          cancel = _ref4.cancel;
      if (!this.isMultiDrag) return;
      insertMultiDragClones(false, rootEl);
      multiDragClones.forEach(function (clone) {
        css(clone, 'display', '');
      });
      cloneNowShown();
      clonesHidden = false;
      cancel();
    },
    hideClone: function hideClone(_ref5) {
      var _this = this;

      var sortable = _ref5.sortable,
          cloneNowHidden = _ref5.cloneNowHidden,
          cancel = _ref5.cancel;
      if (!this.isMultiDrag) return;
      multiDragClones.forEach(function (clone) {
        css(clone, 'display', 'none');

        if (_this.options.removeCloneOnHide && clone.parentNode) {
          clone.parentNode.removeChild(clone);
        }
      });
      cloneNowHidden();
      clonesHidden = true;
      cancel();
    },
    dragStartGlobal: function dragStartGlobal(_ref6) {
      var sortable = _ref6.sortable;

      if (!this.isMultiDrag && multiDragSortable) {
        multiDragSortable.multiDrag._deselectMultiDrag();
      }

      multiDragElements.forEach(function (multiDragElement) {
        multiDragElement.sortableIndex = index(multiDragElement);
      }); // Sort multi-drag elements

      multiDragElements = multiDragElements.sort(function (a, b) {
        return a.sortableIndex - b.sortableIndex;
      });
      dragStarted = true;
    },
    dragStarted: function dragStarted(_ref7) {
      var _this2 = this;

      var sortable = _ref7.sortable;
      if (!this.isMultiDrag) return;

      if (this.options.sort) {
        // Capture rects,
        // hide multi drag elements (by positioning them absolute),
        // set multi drag elements rects to dragRect,
        // show multi drag elements,
        // animate to rects,
        // unset rects & remove from DOM
        sortable.captureAnimationState();

        if (this.options.animation) {
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            css(multiDragElement, 'position', 'absolute');
          });
          var dragRect = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRect);
          });
          folding = true;
          initialFolding = true;
        }
      }

      sortable.animateAll(function () {
        folding = false;
        initialFolding = false;

        if (_this2.options.animation) {
          multiDragElements.forEach(function (multiDragElement) {
            unsetRect(multiDragElement);
          });
        } // Remove all auxiliary multidrag items from el, if sorting enabled


        if (_this2.options.sort) {
          removeMultiDragElements();
        }
      });
    },
    dragOver: function dragOver(_ref8) {
      var target = _ref8.target,
          completed = _ref8.completed,
          cancel = _ref8.cancel;

      if (folding && ~multiDragElements.indexOf(target)) {
        completed(false);
        cancel();
      }
    },
    revert: function revert(_ref9) {
      var fromSortable = _ref9.fromSortable,
          rootEl = _ref9.rootEl,
          sortable = _ref9.sortable,
          dragRect = _ref9.dragRect;

      if (multiDragElements.length > 1) {
        // Setup unfold animation
        multiDragElements.forEach(function (multiDragElement) {
          sortable.addAnimationState({
            target: multiDragElement,
            rect: folding ? getRect(multiDragElement) : dragRect
          });
          unsetRect(multiDragElement);
          multiDragElement.fromRect = dragRect;
          fromSortable.removeAnimationState(multiDragElement);
        });
        folding = false;
        insertMultiDragElements(!this.options.removeCloneOnHide, rootEl);
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref10) {
      var sortable = _ref10.sortable,
          isOwner = _ref10.isOwner,
          insertion = _ref10.insertion,
          activeSortable = _ref10.activeSortable,
          parentEl = _ref10.parentEl,
          putSortable = _ref10.putSortable;
      var options = this.options;

      if (insertion) {
        // Clones must be hidden before folding animation to capture dragRectAbsolute properly
        if (isOwner) {
          activeSortable._hideClone();
        }

        initialFolding = false; // If leaving sort:false root, or already folding - Fold to new location

        if (options.animation && multiDragElements.length > 1 && (folding || !isOwner && !activeSortable.options.sort && !putSortable)) {
          // Fold: Set all multi drag elements's rects to dragEl's rect when multi-drag elements are invisible
          var dragRectAbsolute = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRectAbsolute); // Move element(s) to end of parentEl so that it does not interfere with multi-drag clones insertion if they are inserted
            // while folding, and so that we can capture them again because old sortable will no longer be fromSortable

            parentEl.appendChild(multiDragElement);
          });
          folding = true;
        } // Clones must be shown (and check to remove multi drags) after folding when interfering multiDragElements are moved out


        if (!isOwner) {
          // Only remove if not folding (folding will remove them anyways)
          if (!folding) {
            removeMultiDragElements();
          }

          if (multiDragElements.length > 1) {
            var clonesHiddenBefore = clonesHidden;

            activeSortable._showClone(sortable); // Unfold animation for clones if showing from hidden


            if (activeSortable.options.animation && !clonesHidden && clonesHiddenBefore) {
              multiDragClones.forEach(function (clone) {
                activeSortable.addAnimationState({
                  target: clone,
                  rect: clonesFromRect
                });
                clone.fromRect = clonesFromRect;
                clone.thisAnimationDuration = null;
              });
            }
          } else {
            activeSortable._showClone(sortable);
          }
        }
      }
    },
    dragOverAnimationCapture: function dragOverAnimationCapture(_ref11) {
      var dragRect = _ref11.dragRect,
          isOwner = _ref11.isOwner,
          activeSortable = _ref11.activeSortable;
      multiDragElements.forEach(function (multiDragElement) {
        multiDragElement.thisAnimationDuration = null;
      });

      if (activeSortable.options.animation && !isOwner && activeSortable.multiDrag.isMultiDrag) {
        clonesFromRect = _extends({}, dragRect);
        var dragMatrix = matrix(dragEl$1, true);
        clonesFromRect.top -= dragMatrix.f;
        clonesFromRect.left -= dragMatrix.e;
      }
    },
    dragOverAnimationComplete: function dragOverAnimationComplete() {
      if (folding) {
        folding = false;
        removeMultiDragElements();
      }
    },
    drop: function drop(_ref12) {
      var evt = _ref12.originalEvent,
          rootEl = _ref12.rootEl,
          parentEl = _ref12.parentEl,
          sortable = _ref12.sortable,
          dispatchSortableEvent = _ref12.dispatchSortableEvent,
          oldIndex = _ref12.oldIndex,
          putSortable = _ref12.putSortable;
      var toSortable = putSortable || this.sortable;
      if (!evt) return;
      var options = this.options,
          children = parentEl.children; // Multi-drag selection

      if (!dragStarted) {
        if (options.multiDragKey && !this.multiDragKeyDown) {
          this._deselectMultiDrag();
        }

        toggleClass(dragEl$1, options.selectedClass, !~multiDragElements.indexOf(dragEl$1));

        if (!~multiDragElements.indexOf(dragEl$1)) {
          multiDragElements.push(dragEl$1);
          dispatchEvent({
            sortable: sortable,
            rootEl: rootEl,
            name: 'select',
            targetEl: dragEl$1,
            originalEvt: evt
          }); // Modifier activated, select from last to dragEl

          if (evt.shiftKey && lastMultiDragSelect && sortable.el.contains(lastMultiDragSelect)) {
            var lastIndex = index(lastMultiDragSelect),
                currentIndex = index(dragEl$1);

            if (~lastIndex && ~currentIndex && lastIndex !== currentIndex) {
              // Must include lastMultiDragSelect (select it), in case modified selection from no selection
              // (but previous selection existed)
              var n, i;

              if (currentIndex > lastIndex) {
                i = lastIndex;
                n = currentIndex;
              } else {
                i = currentIndex;
                n = lastIndex + 1;
              }

              for (; i < n; i++) {
                if (~multiDragElements.indexOf(children[i])) continue;
                toggleClass(children[i], options.selectedClass, true);
                multiDragElements.push(children[i]);
                dispatchEvent({
                  sortable: sortable,
                  rootEl: rootEl,
                  name: 'select',
                  targetEl: children[i],
                  originalEvt: evt
                });
              }
            }
          } else {
            lastMultiDragSelect = dragEl$1;
          }

          multiDragSortable = toSortable;
        } else {
          multiDragElements.splice(multiDragElements.indexOf(dragEl$1), 1);
          lastMultiDragSelect = null;
          dispatchEvent({
            sortable: sortable,
            rootEl: rootEl,
            name: 'deselect',
            targetEl: dragEl$1,
            originalEvt: evt
          });
        }
      } // Multi-drag drop


      if (dragStarted && this.isMultiDrag) {
        // Do not "unfold" after around dragEl if reverted
        if ((parentEl[expando].options.sort || parentEl !== rootEl) && multiDragElements.length > 1) {
          var dragRect = getRect(dragEl$1),
              multiDragIndex = index(dragEl$1, ':not(.' + this.options.selectedClass + ')');
          if (!initialFolding && options.animation) dragEl$1.thisAnimationDuration = null;
          toSortable.captureAnimationState();

          if (!initialFolding) {
            if (options.animation) {
              dragEl$1.fromRect = dragRect;
              multiDragElements.forEach(function (multiDragElement) {
                multiDragElement.thisAnimationDuration = null;

                if (multiDragElement !== dragEl$1) {
                  var rect = folding ? getRect(multiDragElement) : dragRect;
                  multiDragElement.fromRect = rect; // Prepare unfold animation

                  toSortable.addAnimationState({
                    target: multiDragElement,
                    rect: rect
                  });
                }
              });
            } // Multi drag elements are not necessarily removed from the DOM on drop, so to reinsert
            // properly they must all be removed


            removeMultiDragElements();
            multiDragElements.forEach(function (multiDragElement) {
              if (children[multiDragIndex]) {
                parentEl.insertBefore(multiDragElement, children[multiDragIndex]);
              } else {
                parentEl.appendChild(multiDragElement);
              }

              multiDragIndex++;
            }); // If initial folding is done, the elements may have changed position because they are now
            // unfolding around dragEl, even though dragEl may not have his index changed, so update event
            // must be fired here as Sortable will not.

            if (oldIndex === index(dragEl$1)) {
              var update = false;
              multiDragElements.forEach(function (multiDragElement) {
                if (multiDragElement.sortableIndex !== index(multiDragElement)) {
                  update = true;
                  return;
                }
              });

              if (update) {
                dispatchSortableEvent('update');
              }
            }
          } // Must be done after capturing individual rects (scroll bar)


          multiDragElements.forEach(function (multiDragElement) {
            unsetRect(multiDragElement);
          });
          toSortable.animateAll();
        }

        multiDragSortable = toSortable;
      } // Remove clones if necessary


      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
        multiDragClones.forEach(function (clone) {
          clone.parentNode && clone.parentNode.removeChild(clone);
        });
      }
    },
    nullingGlobal: function nullingGlobal() {
      this.isMultiDrag = dragStarted = false;
      multiDragClones.length = 0;
    },
    destroyGlobal: function destroyGlobal() {
      this._deselectMultiDrag();

      off(document, 'pointerup', this._deselectMultiDrag);
      off(document, 'mouseup', this._deselectMultiDrag);
      off(document, 'touchend', this._deselectMultiDrag);
      off(document, 'keydown', this._checkKeyDown);
      off(document, 'keyup', this._checkKeyUp);
    },
    _deselectMultiDrag: function _deselectMultiDrag(evt) {
      if (typeof dragStarted !== "undefined" && dragStarted) return; // Only deselect if selection is in this sortable

      if (multiDragSortable !== this.sortable) return; // Only deselect if target is not item in this sortable

      if (evt && closest(evt.target, this.options.draggable, this.sortable.el, false)) return; // Only deselect if left click

      if (evt && evt.button !== 0) return;

      while (multiDragElements.length) {
        var el = multiDragElements[0];
        toggleClass(el, this.options.selectedClass, false);
        multiDragElements.shift();
        dispatchEvent({
          sortable: this.sortable,
          rootEl: this.sortable.el,
          name: 'deselect',
          targetEl: el,
          originalEvt: evt
        });
      }
    },
    _checkKeyDown: function _checkKeyDown(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = true;
      }
    },
    _checkKeyUp: function _checkKeyUp(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = false;
      }
    }
  };
  return _extends(MultiDrag, {
    // Static methods & properties
    pluginName: 'multiDrag',
    utils: {
      /**
       * Selects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be selected
       */
      select: function select(el) {
        var sortable = el.parentNode[expando];
        if (!sortable || !sortable.options.multiDrag || ~multiDragElements.indexOf(el)) return;

        if (multiDragSortable && multiDragSortable !== sortable) {
          multiDragSortable.multiDrag._deselectMultiDrag();

          multiDragSortable = sortable;
        }

        toggleClass(el, sortable.options.selectedClass, true);
        multiDragElements.push(el);
      },

      /**
       * Deselects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be deselected
       */
      deselect: function deselect(el) {
        var sortable = el.parentNode[expando],
            index = multiDragElements.indexOf(el);
        if (!sortable || !sortable.options.multiDrag || !~index) return;
        toggleClass(el, sortable.options.selectedClass, false);
        multiDragElements.splice(index, 1);
      }
    },
    eventProperties: function eventProperties() {
      var _this3 = this;

      var oldIndicies = [],
          newIndicies = [];
      multiDragElements.forEach(function (multiDragElement) {
        oldIndicies.push({
          multiDragElement: multiDragElement,
          index: multiDragElement.sortableIndex
        }); // multiDragElements will already be sorted if folding

        var newIndex;

        if (folding && multiDragElement !== dragEl$1) {
          newIndex = -1;
        } else if (folding) {
          newIndex = index(multiDragElement, ':not(.' + _this3.options.selectedClass + ')');
        } else {
          newIndex = index(multiDragElement);
        }

        newIndicies.push({
          multiDragElement: multiDragElement,
          index: newIndex
        });
      });
      return {
        items: _toConsumableArray(multiDragElements),
        clones: [].concat(multiDragClones),
        oldIndicies: oldIndicies,
        newIndicies: newIndicies
      };
    },
    optionListeners: {
      multiDragKey: function multiDragKey(key) {
        key = key.toLowerCase();

        if (key === 'ctrl') {
          key = 'Control';
        } else if (key.length > 1) {
          key = key.charAt(0).toUpperCase() + key.substr(1);
        }

        return key;
      }
    }
  });
}

function insertMultiDragElements(clonesInserted, rootEl) {
  multiDragElements.forEach(function (multiDragElement, i) {
    var target = rootEl.children[multiDragElement.sortableIndex + (clonesInserted ? Number(i) : 0)];

    if (target) {
      rootEl.insertBefore(multiDragElement, target);
    } else {
      rootEl.appendChild(multiDragElement);
    }
  });
}
/**
 * Insert multi-drag clones
 * @param  {[Boolean]} elementsInserted  Whether the multi-drag elements are inserted
 * @param  {HTMLElement} rootEl
 */


function insertMultiDragClones(elementsInserted, rootEl) {
  multiDragClones.forEach(function (clone, i) {
    var target = rootEl.children[clone.sortableIndex + (elementsInserted ? Number(i) : 0)];

    if (target) {
      rootEl.insertBefore(clone, target);
    } else {
      rootEl.appendChild(clone);
    }
  });
}

function removeMultiDragElements() {
  multiDragElements.forEach(function (multiDragElement) {
    if (multiDragElement === dragEl$1) return;
    multiDragElement.parentNode && multiDragElement.parentNode.removeChild(multiDragElement);
  });
}

Sortable.mount(new AutoScrollPlugin());
Sortable.mount(Remove, Revert);
/* harmony default export */ __webpack_exports__["default"] = (Sortable);


/***/ }),

/***/ "./node_modules/vuedraggable/dist/vuedraggable.umd.js":
/*!************************************************************!*\
  !*** ./node_modules/vuedraggable/dist/vuedraggable.umd.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

(function webpackUniversalModuleDefinition(root, factory) {
  if (( false ? undefined : _typeof(exports)) === 'object' && ( false ? undefined : _typeof(module)) === 'object') module.exports = factory(__webpack_require__(/*! sortablejs */ "./node_modules/sortablejs/modular/sortable.esm.js"));else if (true) !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! sortablejs */ "./node_modules/sortablejs/modular/sortable.esm.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else {}
})(typeof self !== 'undefined' ? self : this, function (__WEBPACK_EXTERNAL_MODULE_a352__) {
  return (
    /******/
    function (modules) {
      // webpackBootstrap

      /******/
      // The module cache

      /******/
      var installedModules = {};
      /******/

      /******/
      // The require function

      /******/

      function __webpack_require__(moduleId) {
        /******/

        /******/
        // Check if module is in cache

        /******/
        if (installedModules[moduleId]) {
          /******/
          return installedModules[moduleId].exports;
          /******/
        }
        /******/
        // Create a new module (and put it into the cache)

        /******/


        var module = installedModules[moduleId] = {
          /******/
          i: moduleId,

          /******/
          l: false,

          /******/
          exports: {}
          /******/

        };
        /******/

        /******/
        // Execute the module function

        /******/

        modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
        /******/

        /******/
        // Flag the module as loaded

        /******/

        module.l = true;
        /******/

        /******/
        // Return the exports of the module

        /******/

        return module.exports;
        /******/
      }
      /******/

      /******/

      /******/
      // expose the modules object (__webpack_modules__)

      /******/


      __webpack_require__.m = modules;
      /******/

      /******/
      // expose the module cache

      /******/

      __webpack_require__.c = installedModules;
      /******/

      /******/
      // define getter function for harmony exports

      /******/

      __webpack_require__.d = function (exports, name, getter) {
        /******/
        if (!__webpack_require__.o(exports, name)) {
          /******/
          Object.defineProperty(exports, name, {
            enumerable: true,
            get: getter
          });
          /******/
        }
        /******/

      };
      /******/

      /******/
      // define __esModule on exports

      /******/


      __webpack_require__.r = function (exports) {
        /******/
        if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
          /******/
          Object.defineProperty(exports, Symbol.toStringTag, {
            value: 'Module'
          });
          /******/
        }
        /******/


        Object.defineProperty(exports, '__esModule', {
          value: true
        });
        /******/
      };
      /******/

      /******/
      // create a fake namespace object

      /******/
      // mode & 1: value is a module id, require it

      /******/
      // mode & 2: merge all properties of value into the ns

      /******/
      // mode & 4: return value when already ns object

      /******/
      // mode & 8|1: behave like require

      /******/


      __webpack_require__.t = function (value, mode) {
        /******/
        if (mode & 1) value = __webpack_require__(value);
        /******/

        if (mode & 8) return value;
        /******/

        if (mode & 4 && _typeof(value) === 'object' && value && value.__esModule) return value;
        /******/

        var ns = Object.create(null);
        /******/

        __webpack_require__.r(ns);
        /******/


        Object.defineProperty(ns, 'default', {
          enumerable: true,
          value: value
        });
        /******/

        if (mode & 2 && typeof value != 'string') for (var key in value) {
          __webpack_require__.d(ns, key, function (key) {
            return value[key];
          }.bind(null, key));
        }
        /******/

        return ns;
        /******/
      };
      /******/

      /******/
      // getDefaultExport function for compatibility with non-harmony modules

      /******/


      __webpack_require__.n = function (module) {
        /******/
        var getter = module && module.__esModule ?
        /******/
        function getDefault() {
          return module['default'];
        } :
        /******/
        function getModuleExports() {
          return module;
        };
        /******/

        __webpack_require__.d(getter, 'a', getter);
        /******/


        return getter;
        /******/
      };
      /******/

      /******/
      // Object.prototype.hasOwnProperty.call

      /******/


      __webpack_require__.o = function (object, property) {
        return Object.prototype.hasOwnProperty.call(object, property);
      };
      /******/

      /******/
      // __webpack_public_path__

      /******/


      __webpack_require__.p = "";
      /******/

      /******/

      /******/
      // Load entry module and return exports

      /******/

      return __webpack_require__(__webpack_require__.s = "fb15");
      /******/
    }({
      /***/
      "01f9": function f9(module, exports, __webpack_require__) {
        "use strict";

        var LIBRARY = __webpack_require__("2d00");

        var $export = __webpack_require__("5ca1");

        var redefine = __webpack_require__("2aba");

        var hide = __webpack_require__("32e9");

        var Iterators = __webpack_require__("84f2");

        var $iterCreate = __webpack_require__("41a0");

        var setToStringTag = __webpack_require__("7f20");

        var getPrototypeOf = __webpack_require__("38fd");

        var ITERATOR = __webpack_require__("2b4c")('iterator');

        var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`

        var FF_ITERATOR = '@@iterator';
        var KEYS = 'keys';
        var VALUES = 'values';

        var returnThis = function returnThis() {
          return this;
        };

        module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
          $iterCreate(Constructor, NAME, next);

          var getMethod = function getMethod(kind) {
            if (!BUGGY && kind in proto) return proto[kind];

            switch (kind) {
              case KEYS:
                return function keys() {
                  return new Constructor(this, kind);
                };

              case VALUES:
                return function values() {
                  return new Constructor(this, kind);
                };
            }

            return function entries() {
              return new Constructor(this, kind);
            };
          };

          var TAG = NAME + ' Iterator';
          var DEF_VALUES = DEFAULT == VALUES;
          var VALUES_BUG = false;
          var proto = Base.prototype;
          var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
          var $default = $native || getMethod(DEFAULT);
          var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
          var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
          var methods, key, IteratorPrototype; // Fix native

          if ($anyNative) {
            IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));

            if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
              // Set @@toStringTag to native iterators
              setToStringTag(IteratorPrototype, TAG, true); // fix for some old engines

              if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
            }
          } // fix Array#{values, @@iterator}.name in V8 / FF


          if (DEF_VALUES && $native && $native.name !== VALUES) {
            VALUES_BUG = true;

            $default = function values() {
              return $native.call(this);
            };
          } // Define iterator


          if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
            hide(proto, ITERATOR, $default);
          } // Plug for library


          Iterators[NAME] = $default;
          Iterators[TAG] = returnThis;

          if (DEFAULT) {
            methods = {
              values: DEF_VALUES ? $default : getMethod(VALUES),
              keys: IS_SET ? $default : getMethod(KEYS),
              entries: $entries
            };
            if (FORCED) for (key in methods) {
              if (!(key in proto)) redefine(proto, key, methods[key]);
            } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
          }

          return methods;
        };
        /***/

      },

      /***/
      "02f4": function f4(module, exports, __webpack_require__) {
        var toInteger = __webpack_require__("4588");

        var defined = __webpack_require__("be13"); // true  -> String#at
        // false -> String#codePointAt


        module.exports = function (TO_STRING) {
          return function (that, pos) {
            var s = String(defined(that));
            var i = toInteger(pos);
            var l = s.length;
            var a, b;
            if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
            a = s.charCodeAt(i);
            return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff ? TO_STRING ? s.charAt(i) : a : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
          };
        };
        /***/

      },

      /***/
      "0390": function _(module, exports, __webpack_require__) {
        "use strict";

        var at = __webpack_require__("02f4")(true); // `AdvanceStringIndex` abstract operation
        // https://tc39.github.io/ecma262/#sec-advancestringindex


        module.exports = function (S, index, unicode) {
          return index + (unicode ? at(S, index).length : 1);
        };
        /***/

      },

      /***/
      "0bfb": function bfb(module, exports, __webpack_require__) {
        "use strict"; // 21.2.5.3 get RegExp.prototype.flags

        var anObject = __webpack_require__("cb7c");

        module.exports = function () {
          var that = anObject(this);
          var result = '';
          if (that.global) result += 'g';
          if (that.ignoreCase) result += 'i';
          if (that.multiline) result += 'm';
          if (that.unicode) result += 'u';
          if (that.sticky) result += 'y';
          return result;
        };
        /***/

      },

      /***/
      "0d58": function d58(module, exports, __webpack_require__) {
        // 19.1.2.14 / 15.2.3.14 Object.keys(O)
        var $keys = __webpack_require__("ce10");

        var enumBugKeys = __webpack_require__("e11e");

        module.exports = Object.keys || function keys(O) {
          return $keys(O, enumBugKeys);
        };
        /***/

      },

      /***/
      "1495": function _(module, exports, __webpack_require__) {
        var dP = __webpack_require__("86cc");

        var anObject = __webpack_require__("cb7c");

        var getKeys = __webpack_require__("0d58");

        module.exports = __webpack_require__("9e1e") ? Object.defineProperties : function defineProperties(O, Properties) {
          anObject(O);
          var keys = getKeys(Properties);
          var length = keys.length;
          var i = 0;
          var P;

          while (length > i) {
            dP.f(O, P = keys[i++], Properties[P]);
          }

          return O;
        };
        /***/
      },

      /***/
      "214f": function f(module, exports, __webpack_require__) {
        "use strict";

        __webpack_require__("b0c5");

        var redefine = __webpack_require__("2aba");

        var hide = __webpack_require__("32e9");

        var fails = __webpack_require__("79e5");

        var defined = __webpack_require__("be13");

        var wks = __webpack_require__("2b4c");

        var regexpExec = __webpack_require__("520a");

        var SPECIES = wks('species');
        var REPLACE_SUPPORTS_NAMED_GROUPS = !fails(function () {
          // #replace needs built-in support for named groups.
          // #match works fine because it just return the exec results, even if it has
          // a "grops" property.
          var re = /./;

          re.exec = function () {
            var result = [];
            result.groups = {
              a: '7'
            };
            return result;
          };

          return ''.replace(re, '$<a>') !== '7';
        });

        var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = function () {
          // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
          var re = /(?:)/;
          var originalExec = re.exec;

          re.exec = function () {
            return originalExec.apply(this, arguments);
          };

          var result = 'ab'.split(re);
          return result.length === 2 && result[0] === 'a' && result[1] === 'b';
        }();

        module.exports = function (KEY, length, exec) {
          var SYMBOL = wks(KEY);
          var DELEGATES_TO_SYMBOL = !fails(function () {
            // String methods call symbol-named RegEp methods
            var O = {};

            O[SYMBOL] = function () {
              return 7;
            };

            return ''[KEY](O) != 7;
          });
          var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL ? !fails(function () {
            // Symbol-named RegExp methods call .exec
            var execCalled = false;
            var re = /a/;

            re.exec = function () {
              execCalled = true;
              return null;
            };

            if (KEY === 'split') {
              // RegExp[@@split] doesn't call the regex's exec method, but first creates
              // a new one. We need to return the patched regex when creating the new one.
              re.constructor = {};

              re.constructor[SPECIES] = function () {
                return re;
              };
            }

            re[SYMBOL]('');
            return !execCalled;
          }) : undefined;

          if (!DELEGATES_TO_SYMBOL || !DELEGATES_TO_EXEC || KEY === 'replace' && !REPLACE_SUPPORTS_NAMED_GROUPS || KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC) {
            var nativeRegExpMethod = /./[SYMBOL];
            var fns = exec(defined, SYMBOL, ''[KEY], function maybeCallNative(nativeMethod, regexp, str, arg2, forceStringMethod) {
              if (regexp.exec === regexpExec) {
                if (DELEGATES_TO_SYMBOL && !forceStringMethod) {
                  // The native String method already delegates to @@method (this
                  // polyfilled function), leasing to infinite recursion.
                  // We avoid it by directly calling the native @@method method.
                  return {
                    done: true,
                    value: nativeRegExpMethod.call(regexp, str, arg2)
                  };
                }

                return {
                  done: true,
                  value: nativeMethod.call(str, regexp, arg2)
                };
              }

              return {
                done: false
              };
            });
            var strfn = fns[0];
            var rxfn = fns[1];
            redefine(String.prototype, KEY, strfn);
            hide(RegExp.prototype, SYMBOL, length == 2 // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
            // 21.2.5.11 RegExp.prototype[@@split](string, limit)
            ? function (string, arg) {
              return rxfn.call(string, this, arg);
            } // 21.2.5.6 RegExp.prototype[@@match](string)
            // 21.2.5.9 RegExp.prototype[@@search](string)
            : function (string) {
              return rxfn.call(string, this);
            });
          }
        };
        /***/

      },

      /***/
      "230e": function e(module, exports, __webpack_require__) {
        var isObject = __webpack_require__("d3f4");

        var document = __webpack_require__("7726").document; // typeof document.createElement is 'object' in old IE


        var is = isObject(document) && isObject(document.createElement);

        module.exports = function (it) {
          return is ? document.createElement(it) : {};
        };
        /***/

      },

      /***/
      "23c6": function c6(module, exports, __webpack_require__) {
        // getting tag from 19.1.3.6 Object.prototype.toString()
        var cof = __webpack_require__("2d95");

        var TAG = __webpack_require__("2b4c")('toStringTag'); // ES3 wrong here


        var ARG = cof(function () {
          return arguments;
        }()) == 'Arguments'; // fallback for IE11 Script Access Denied error

        var tryGet = function tryGet(it, key) {
          try {
            return it[key];
          } catch (e) {
            /* empty */
          }
        };

        module.exports = function (it) {
          var O, T, B;
          return it === undefined ? 'Undefined' : it === null ? 'Null' // @@toStringTag case
          : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T // builtinTag case
          : ARG ? cof(O) // ES3 arguments fallback
          : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
        };
        /***/

      },

      /***/
      "2621": function _(module, exports) {
        exports.f = Object.getOwnPropertySymbols;
        /***/
      },

      /***/
      "2aba": function aba(module, exports, __webpack_require__) {
        var global = __webpack_require__("7726");

        var hide = __webpack_require__("32e9");

        var has = __webpack_require__("69a8");

        var SRC = __webpack_require__("ca5a")('src');

        var $toString = __webpack_require__("fa5b");

        var TO_STRING = 'toString';
        var TPL = ('' + $toString).split(TO_STRING);

        __webpack_require__("8378").inspectSource = function (it) {
          return $toString.call(it);
        };

        (module.exports = function (O, key, val, safe) {
          var isFunction = typeof val == 'function';
          if (isFunction) has(val, 'name') || hide(val, 'name', key);
          if (O[key] === val) return;
          if (isFunction) has(val, SRC) || hide(val, SRC, O[key] ? '' + O[key] : TPL.join(String(key)));

          if (O === global) {
            O[key] = val;
          } else if (!safe) {
            delete O[key];
            hide(O, key, val);
          } else if (O[key]) {
            O[key] = val;
          } else {
            hide(O, key, val);
          } // add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative

        })(Function.prototype, TO_STRING, function toString() {
          return typeof this == 'function' && this[SRC] || $toString.call(this);
        });
        /***/
      },

      /***/
      "2aeb": function aeb(module, exports, __webpack_require__) {
        // 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
        var anObject = __webpack_require__("cb7c");

        var dPs = __webpack_require__("1495");

        var enumBugKeys = __webpack_require__("e11e");

        var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

        var Empty = function Empty() {
          /* empty */
        };

        var PROTOTYPE = 'prototype'; // Create object with fake `null` prototype: use iframe Object with cleared prototype

        var _createDict = function createDict() {
          // Thrash, waste and sodomy: IE GC bug
          var iframe = __webpack_require__("230e")('iframe');

          var i = enumBugKeys.length;
          var lt = '<';
          var gt = '>';
          var iframeDocument;
          iframe.style.display = 'none';

          __webpack_require__("fab2").appendChild(iframe);

          iframe.src = 'javascript:'; // eslint-disable-line no-script-url
          // createDict = iframe.contentWindow.Object;
          // html.removeChild(iframe);

          iframeDocument = iframe.contentWindow.document;
          iframeDocument.open();
          iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
          iframeDocument.close();
          _createDict = iframeDocument.F;

          while (i--) {
            delete _createDict[PROTOTYPE][enumBugKeys[i]];
          }

          return _createDict();
        };

        module.exports = Object.create || function create(O, Properties) {
          var result;

          if (O !== null) {
            Empty[PROTOTYPE] = anObject(O);
            result = new Empty();
            Empty[PROTOTYPE] = null; // add "__proto__" for Object.getPrototypeOf polyfill

            result[IE_PROTO] = O;
          } else result = _createDict();

          return Properties === undefined ? result : dPs(result, Properties);
        };
        /***/

      },

      /***/
      "2b4c": function b4c(module, exports, __webpack_require__) {
        var store = __webpack_require__("5537")('wks');

        var uid = __webpack_require__("ca5a");

        var _Symbol = __webpack_require__("7726").Symbol;

        var USE_SYMBOL = typeof _Symbol == 'function';

        var $exports = module.exports = function (name) {
          return store[name] || (store[name] = USE_SYMBOL && _Symbol[name] || (USE_SYMBOL ? _Symbol : uid)('Symbol.' + name));
        };

        $exports.store = store;
        /***/
      },

      /***/
      "2d00": function d00(module, exports) {
        module.exports = false;
        /***/
      },

      /***/
      "2d95": function d95(module, exports) {
        var toString = {}.toString;

        module.exports = function (it) {
          return toString.call(it).slice(8, -1);
        };
        /***/

      },

      /***/
      "2fdb": function fdb(module, exports, __webpack_require__) {
        "use strict"; // 21.1.3.7 String.prototype.includes(searchString, position = 0)

        var $export = __webpack_require__("5ca1");

        var context = __webpack_require__("d2c8");

        var INCLUDES = 'includes';
        $export($export.P + $export.F * __webpack_require__("5147")(INCLUDES), 'String', {
          includes: function includes(searchString
          /* , position = 0 */
          ) {
            return !!~context(this, searchString, INCLUDES).indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
          }
        });
        /***/
      },

      /***/
      "32e9": function e9(module, exports, __webpack_require__) {
        var dP = __webpack_require__("86cc");

        var createDesc = __webpack_require__("4630");

        module.exports = __webpack_require__("9e1e") ? function (object, key, value) {
          return dP.f(object, key, createDesc(1, value));
        } : function (object, key, value) {
          object[key] = value;
          return object;
        };
        /***/
      },

      /***/
      "38fd": function fd(module, exports, __webpack_require__) {
        // 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
        var has = __webpack_require__("69a8");

        var toObject = __webpack_require__("4bf8");

        var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

        var ObjectProto = Object.prototype;

        module.exports = Object.getPrototypeOf || function (O) {
          O = toObject(O);
          if (has(O, IE_PROTO)) return O[IE_PROTO];

          if (typeof O.constructor == 'function' && O instanceof O.constructor) {
            return O.constructor.prototype;
          }

          return O instanceof Object ? ObjectProto : null;
        };
        /***/

      },

      /***/
      "41a0": function a0(module, exports, __webpack_require__) {
        "use strict";

        var create = __webpack_require__("2aeb");

        var descriptor = __webpack_require__("4630");

        var setToStringTag = __webpack_require__("7f20");

        var IteratorPrototype = {}; // 25.1.2.1.1 %IteratorPrototype%[@@iterator]()

        __webpack_require__("32e9")(IteratorPrototype, __webpack_require__("2b4c")('iterator'), function () {
          return this;
        });

        module.exports = function (Constructor, NAME, next) {
          Constructor.prototype = create(IteratorPrototype, {
            next: descriptor(1, next)
          });
          setToStringTag(Constructor, NAME + ' Iterator');
        };
        /***/

      },

      /***/
      "456d": function d(module, exports, __webpack_require__) {
        // 19.1.2.14 Object.keys(O)
        var toObject = __webpack_require__("4bf8");

        var $keys = __webpack_require__("0d58");

        __webpack_require__("5eda")('keys', function () {
          return function keys(it) {
            return $keys(toObject(it));
          };
        });
        /***/

      },

      /***/
      "4588": function _(module, exports) {
        // 7.1.4 ToInteger
        var ceil = Math.ceil;
        var floor = Math.floor;

        module.exports = function (it) {
          return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
        };
        /***/

      },

      /***/
      "4630": function _(module, exports) {
        module.exports = function (bitmap, value) {
          return {
            enumerable: !(bitmap & 1),
            configurable: !(bitmap & 2),
            writable: !(bitmap & 4),
            value: value
          };
        };
        /***/

      },

      /***/
      "4bf8": function bf8(module, exports, __webpack_require__) {
        // 7.1.13 ToObject(argument)
        var defined = __webpack_require__("be13");

        module.exports = function (it) {
          return Object(defined(it));
        };
        /***/

      },

      /***/
      "5147": function _(module, exports, __webpack_require__) {
        var MATCH = __webpack_require__("2b4c")('match');

        module.exports = function (KEY) {
          var re = /./;

          try {
            '/./'[KEY](re);
          } catch (e) {
            try {
              re[MATCH] = false;
              return !'/./'[KEY](re);
            } catch (f) {
              /* empty */
            }
          }

          return true;
        };
        /***/

      },

      /***/
      "520a": function a(module, exports, __webpack_require__) {
        "use strict";

        var regexpFlags = __webpack_require__("0bfb");

        var nativeExec = RegExp.prototype.exec; // This always refers to the native implementation, because the
        // String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
        // which loads this file before patching the method.

        var nativeReplace = String.prototype.replace;
        var patchedExec = nativeExec;
        var LAST_INDEX = 'lastIndex';

        var UPDATES_LAST_INDEX_WRONG = function () {
          var re1 = /a/,
              re2 = /b*/g;
          nativeExec.call(re1, 'a');
          nativeExec.call(re2, 'a');
          return re1[LAST_INDEX] !== 0 || re2[LAST_INDEX] !== 0;
        }(); // nonparticipating capturing group, copied from es5-shim's String#split patch.


        var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;
        var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED;

        if (PATCH) {
          patchedExec = function exec(str) {
            var re = this;
            var lastIndex, reCopy, match, i;

            if (NPCG_INCLUDED) {
              reCopy = new RegExp('^' + re.source + '$(?!\\s)', regexpFlags.call(re));
            }

            if (UPDATES_LAST_INDEX_WRONG) lastIndex = re[LAST_INDEX];
            match = nativeExec.call(re, str);

            if (UPDATES_LAST_INDEX_WRONG && match) {
              re[LAST_INDEX] = re.global ? match.index + match[0].length : lastIndex;
            }

            if (NPCG_INCLUDED && match && match.length > 1) {
              // Fix browsers whose `exec` methods don't consistently return `undefined`
              // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
              // eslint-disable-next-line no-loop-func
              nativeReplace.call(match[0], reCopy, function () {
                for (i = 1; i < arguments.length - 2; i++) {
                  if (arguments[i] === undefined) match[i] = undefined;
                }
              });
            }

            return match;
          };
        }

        module.exports = patchedExec;
        /***/
      },

      /***/
      "52a7": function a7(module, exports) {
        exports.f = {}.propertyIsEnumerable;
        /***/
      },

      /***/
      "5537": function _(module, exports, __webpack_require__) {
        var core = __webpack_require__("8378");

        var global = __webpack_require__("7726");

        var SHARED = '__core-js_shared__';
        var store = global[SHARED] || (global[SHARED] = {});
        (module.exports = function (key, value) {
          return store[key] || (store[key] = value !== undefined ? value : {});
        })('versions', []).push({
          version: core.version,
          mode: __webpack_require__("2d00") ? 'pure' : 'global',
          copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
        });
        /***/
      },

      /***/
      "5ca1": function ca1(module, exports, __webpack_require__) {
        var global = __webpack_require__("7726");

        var core = __webpack_require__("8378");

        var hide = __webpack_require__("32e9");

        var redefine = __webpack_require__("2aba");

        var ctx = __webpack_require__("9b43");

        var PROTOTYPE = 'prototype';

        var $export = function $export(type, name, source) {
          var IS_FORCED = type & $export.F;
          var IS_GLOBAL = type & $export.G;
          var IS_STATIC = type & $export.S;
          var IS_PROTO = type & $export.P;
          var IS_BIND = type & $export.B;
          var target = IS_GLOBAL ? global : IS_STATIC ? global[name] || (global[name] = {}) : (global[name] || {})[PROTOTYPE];
          var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
          var expProto = exports[PROTOTYPE] || (exports[PROTOTYPE] = {});
          var key, own, out, exp;
          if (IS_GLOBAL) source = name;

          for (key in source) {
            // contains in native
            own = !IS_FORCED && target && target[key] !== undefined; // export native or passed

            out = (own ? target : source)[key]; // bind timers to global for call from export context

            exp = IS_BIND && own ? ctx(out, global) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out; // extend global

            if (target) redefine(target, key, out, type & $export.U); // export

            if (exports[key] != out) hide(exports, key, exp);
            if (IS_PROTO && expProto[key] != out) expProto[key] = out;
          }
        };

        global.core = core; // type bitmap

        $export.F = 1; // forced

        $export.G = 2; // global

        $export.S = 4; // static

        $export.P = 8; // proto

        $export.B = 16; // bind

        $export.W = 32; // wrap

        $export.U = 64; // safe

        $export.R = 128; // real proto method for `library`

        module.exports = $export;
        /***/
      },

      /***/
      "5eda": function eda(module, exports, __webpack_require__) {
        // most Object methods by ES6 should accept primitives
        var $export = __webpack_require__("5ca1");

        var core = __webpack_require__("8378");

        var fails = __webpack_require__("79e5");

        module.exports = function (KEY, exec) {
          var fn = (core.Object || {})[KEY] || Object[KEY];
          var exp = {};
          exp[KEY] = exec(fn);
          $export($export.S + $export.F * fails(function () {
            fn(1);
          }), 'Object', exp);
        };
        /***/

      },

      /***/
      "5f1b": function f1b(module, exports, __webpack_require__) {
        "use strict";

        var classof = __webpack_require__("23c6");

        var builtinExec = RegExp.prototype.exec; // `RegExpExec` abstract operation
        // https://tc39.github.io/ecma262/#sec-regexpexec

        module.exports = function (R, S) {
          var exec = R.exec;

          if (typeof exec === 'function') {
            var result = exec.call(R, S);

            if (_typeof(result) !== 'object') {
              throw new TypeError('RegExp exec method returned something other than an Object or null');
            }

            return result;
          }

          if (classof(R) !== 'RegExp') {
            throw new TypeError('RegExp#exec called on incompatible receiver');
          }

          return builtinExec.call(R, S);
        };
        /***/

      },

      /***/
      "613b": function b(module, exports, __webpack_require__) {
        var shared = __webpack_require__("5537")('keys');

        var uid = __webpack_require__("ca5a");

        module.exports = function (key) {
          return shared[key] || (shared[key] = uid(key));
        };
        /***/

      },

      /***/
      "626a": function a(module, exports, __webpack_require__) {
        // fallback for non-array-like ES3 and non-enumerable old V8 strings
        var cof = __webpack_require__("2d95"); // eslint-disable-next-line no-prototype-builtins


        module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
          return cof(it) == 'String' ? it.split('') : Object(it);
        };
        /***/
      },

      /***/
      "6762": function _(module, exports, __webpack_require__) {
        "use strict"; // https://github.com/tc39/Array.prototype.includes

        var $export = __webpack_require__("5ca1");

        var $includes = __webpack_require__("c366")(true);

        $export($export.P, 'Array', {
          includes: function includes(el
          /* , fromIndex = 0 */
          ) {
            return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
          }
        });

        __webpack_require__("9c6c")('includes');
        /***/

      },

      /***/
      "6821": function _(module, exports, __webpack_require__) {
        // to indexed object, toObject with fallback for non-array-like ES3 strings
        var IObject = __webpack_require__("626a");

        var defined = __webpack_require__("be13");

        module.exports = function (it) {
          return IObject(defined(it));
        };
        /***/

      },

      /***/
      "69a8": function a8(module, exports) {
        var hasOwnProperty = {}.hasOwnProperty;

        module.exports = function (it, key) {
          return hasOwnProperty.call(it, key);
        };
        /***/

      },

      /***/
      "6a99": function a99(module, exports, __webpack_require__) {
        // 7.1.1 ToPrimitive(input [, PreferredType])
        var isObject = __webpack_require__("d3f4"); // instead of the ES6 spec version, we didn't implement @@toPrimitive case
        // and the second argument - flag - preferred type is a string


        module.exports = function (it, S) {
          if (!isObject(it)) return it;
          var fn, val;
          if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
          if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
          if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
          throw TypeError("Can't convert object to primitive value");
        };
        /***/

      },

      /***/
      "7333": function _(module, exports, __webpack_require__) {
        "use strict"; // 19.1.2.1 Object.assign(target, source, ...)

        var getKeys = __webpack_require__("0d58");

        var gOPS = __webpack_require__("2621");

        var pIE = __webpack_require__("52a7");

        var toObject = __webpack_require__("4bf8");

        var IObject = __webpack_require__("626a");

        var $assign = Object.assign; // should work with symbols and should have deterministic property order (V8 bug)

        module.exports = !$assign || __webpack_require__("79e5")(function () {
          var A = {};
          var B = {}; // eslint-disable-next-line no-undef

          var S = Symbol();
          var K = 'abcdefghijklmnopqrst';
          A[S] = 7;
          K.split('').forEach(function (k) {
            B[k] = k;
          });
          return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
        }) ? function assign(target, source) {
          // eslint-disable-line no-unused-vars
          var T = toObject(target);
          var aLen = arguments.length;
          var index = 1;
          var getSymbols = gOPS.f;
          var isEnum = pIE.f;

          while (aLen > index) {
            var S = IObject(arguments[index++]);
            var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
            var length = keys.length;
            var j = 0;
            var key;

            while (length > j) {
              if (isEnum.call(S, key = keys[j++])) T[key] = S[key];
            }
          }

          return T;
        } : $assign;
        /***/
      },

      /***/
      "7726": function _(module, exports) {
        // https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
        var global = module.exports = typeof window != 'undefined' && window.Math == Math ? window : typeof self != 'undefined' && self.Math == Math ? self // eslint-disable-next-line no-new-func
        : Function('return this')();
        if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef

        /***/
      },

      /***/
      "77f1": function f1(module, exports, __webpack_require__) {
        var toInteger = __webpack_require__("4588");

        var max = Math.max;
        var min = Math.min;

        module.exports = function (index, length) {
          index = toInteger(index);
          return index < 0 ? max(index + length, 0) : min(index, length);
        };
        /***/

      },

      /***/
      "79e5": function e5(module, exports) {
        module.exports = function (exec) {
          try {
            return !!exec();
          } catch (e) {
            return true;
          }
        };
        /***/

      },

      /***/
      "7f20": function f20(module, exports, __webpack_require__) {
        var def = __webpack_require__("86cc").f;

        var has = __webpack_require__("69a8");

        var TAG = __webpack_require__("2b4c")('toStringTag');

        module.exports = function (it, tag, stat) {
          if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, {
            configurable: true,
            value: tag
          });
        };
        /***/

      },

      /***/
      "8378": function _(module, exports) {
        var core = module.exports = {
          version: '2.6.5'
        };
        if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef

        /***/
      },

      /***/
      "84f2": function f2(module, exports) {
        module.exports = {};
        /***/
      },

      /***/
      "86cc": function cc(module, exports, __webpack_require__) {
        var anObject = __webpack_require__("cb7c");

        var IE8_DOM_DEFINE = __webpack_require__("c69a");

        var toPrimitive = __webpack_require__("6a99");

        var dP = Object.defineProperty;
        exports.f = __webpack_require__("9e1e") ? Object.defineProperty : function defineProperty(O, P, Attributes) {
          anObject(O);
          P = toPrimitive(P, true);
          anObject(Attributes);
          if (IE8_DOM_DEFINE) try {
            return dP(O, P, Attributes);
          } catch (e) {
            /* empty */
          }
          if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
          if ('value' in Attributes) O[P] = Attributes.value;
          return O;
        };
        /***/
      },

      /***/
      "9b43": function b43(module, exports, __webpack_require__) {
        // optional / simple context binding
        var aFunction = __webpack_require__("d8e8");

        module.exports = function (fn, that, length) {
          aFunction(fn);
          if (that === undefined) return fn;

          switch (length) {
            case 1:
              return function (a) {
                return fn.call(that, a);
              };

            case 2:
              return function (a, b) {
                return fn.call(that, a, b);
              };

            case 3:
              return function (a, b, c) {
                return fn.call(that, a, b, c);
              };
          }

          return function () {
            return fn.apply(that, arguments);
          };
        };
        /***/

      },

      /***/
      "9c6c": function c6c(module, exports, __webpack_require__) {
        // 22.1.3.31 Array.prototype[@@unscopables]
        var UNSCOPABLES = __webpack_require__("2b4c")('unscopables');

        var ArrayProto = Array.prototype;
        if (ArrayProto[UNSCOPABLES] == undefined) __webpack_require__("32e9")(ArrayProto, UNSCOPABLES, {});

        module.exports = function (key) {
          ArrayProto[UNSCOPABLES][key] = true;
        };
        /***/

      },

      /***/
      "9def": function def(module, exports, __webpack_require__) {
        // 7.1.15 ToLength
        var toInteger = __webpack_require__("4588");

        var min = Math.min;

        module.exports = function (it) {
          return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
        };
        /***/

      },

      /***/
      "9e1e": function e1e(module, exports, __webpack_require__) {
        // Thank's IE8 for his funny defineProperty
        module.exports = !__webpack_require__("79e5")(function () {
          return Object.defineProperty({}, 'a', {
            get: function get() {
              return 7;
            }
          }).a != 7;
        });
        /***/
      },

      /***/
      "a352": function a352(module, exports) {
        module.exports = __WEBPACK_EXTERNAL_MODULE_a352__;
        /***/
      },

      /***/
      "a481": function a481(module, exports, __webpack_require__) {
        "use strict";

        var anObject = __webpack_require__("cb7c");

        var toObject = __webpack_require__("4bf8");

        var toLength = __webpack_require__("9def");

        var toInteger = __webpack_require__("4588");

        var advanceStringIndex = __webpack_require__("0390");

        var regExpExec = __webpack_require__("5f1b");

        var max = Math.max;
        var min = Math.min;
        var floor = Math.floor;
        var SUBSTITUTION_SYMBOLS = /\$([$&`']|\d\d?|<[^>]*>)/g;
        var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&`']|\d\d?)/g;

        var maybeToString = function maybeToString(it) {
          return it === undefined ? it : String(it);
        }; // @@replace logic


        __webpack_require__("214f")('replace', 2, function (defined, REPLACE, $replace, maybeCallNative) {
          return [// `String.prototype.replace` method
          // https://tc39.github.io/ecma262/#sec-string.prototype.replace
          function replace(searchValue, replaceValue) {
            var O = defined(this);
            var fn = searchValue == undefined ? undefined : searchValue[REPLACE];
            return fn !== undefined ? fn.call(searchValue, O, replaceValue) : $replace.call(String(O), searchValue, replaceValue);
          }, // `RegExp.prototype[@@replace]` method
          // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@replace
          function (regexp, replaceValue) {
            var res = maybeCallNative($replace, regexp, this, replaceValue);
            if (res.done) return res.value;
            var rx = anObject(regexp);
            var S = String(this);
            var functionalReplace = typeof replaceValue === 'function';
            if (!functionalReplace) replaceValue = String(replaceValue);
            var global = rx.global;

            if (global) {
              var fullUnicode = rx.unicode;
              rx.lastIndex = 0;
            }

            var results = [];

            while (true) {
              var result = regExpExec(rx, S);
              if (result === null) break;
              results.push(result);
              if (!global) break;
              var matchStr = String(result[0]);
              if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
            }

            var accumulatedResult = '';
            var nextSourcePosition = 0;

            for (var i = 0; i < results.length; i++) {
              result = results[i];
              var matched = String(result[0]);
              var position = max(min(toInteger(result.index), S.length), 0);
              var captures = []; // NOTE: This is equivalent to
              //   captures = result.slice(1).map(maybeToString)
              // but for some reason `nativeSlice.call(result, 1, result.length)` (called in
              // the slice polyfill when slicing native arrays) "doesn't work" in safari 9 and
              // causes a crash (https://pastebin.com/N21QzeQA) when trying to debug it.

              for (var j = 1; j < result.length; j++) {
                captures.push(maybeToString(result[j]));
              }

              var namedCaptures = result.groups;

              if (functionalReplace) {
                var replacerArgs = [matched].concat(captures, position, S);
                if (namedCaptures !== undefined) replacerArgs.push(namedCaptures);
                var replacement = String(replaceValue.apply(undefined, replacerArgs));
              } else {
                replacement = getSubstitution(matched, S, position, captures, namedCaptures, replaceValue);
              }

              if (position >= nextSourcePosition) {
                accumulatedResult += S.slice(nextSourcePosition, position) + replacement;
                nextSourcePosition = position + matched.length;
              }
            }

            return accumulatedResult + S.slice(nextSourcePosition);
          }]; // https://tc39.github.io/ecma262/#sec-getsubstitution

          function getSubstitution(matched, str, position, captures, namedCaptures, replacement) {
            var tailPos = position + matched.length;
            var m = captures.length;
            var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;

            if (namedCaptures !== undefined) {
              namedCaptures = toObject(namedCaptures);
              symbols = SUBSTITUTION_SYMBOLS;
            }

            return $replace.call(replacement, symbols, function (match, ch) {
              var capture;

              switch (ch.charAt(0)) {
                case '$':
                  return '$';

                case '&':
                  return matched;

                case '`':
                  return str.slice(0, position);

                case "'":
                  return str.slice(tailPos);

                case '<':
                  capture = namedCaptures[ch.slice(1, -1)];
                  break;

                default:
                  // \d\d?
                  var n = +ch;
                  if (n === 0) return match;

                  if (n > m) {
                    var f = floor(n / 10);
                    if (f === 0) return match;
                    if (f <= m) return captures[f - 1] === undefined ? ch.charAt(1) : captures[f - 1] + ch.charAt(1);
                    return match;
                  }

                  capture = captures[n - 1];
              }

              return capture === undefined ? '' : capture;
            });
          }
        });
        /***/

      },

      /***/
      "aae3": function aae3(module, exports, __webpack_require__) {
        // 7.2.8 IsRegExp(argument)
        var isObject = __webpack_require__("d3f4");

        var cof = __webpack_require__("2d95");

        var MATCH = __webpack_require__("2b4c")('match');

        module.exports = function (it) {
          var isRegExp;
          return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
        };
        /***/

      },

      /***/
      "ac6a": function ac6a(module, exports, __webpack_require__) {
        var $iterators = __webpack_require__("cadf");

        var getKeys = __webpack_require__("0d58");

        var redefine = __webpack_require__("2aba");

        var global = __webpack_require__("7726");

        var hide = __webpack_require__("32e9");

        var Iterators = __webpack_require__("84f2");

        var wks = __webpack_require__("2b4c");

        var ITERATOR = wks('iterator');
        var TO_STRING_TAG = wks('toStringTag');
        var ArrayValues = Iterators.Array;
        var DOMIterables = {
          CSSRuleList: true,
          // TODO: Not spec compliant, should be false.
          CSSStyleDeclaration: false,
          CSSValueList: false,
          ClientRectList: false,
          DOMRectList: false,
          DOMStringList: false,
          DOMTokenList: true,
          DataTransferItemList: false,
          FileList: false,
          HTMLAllCollection: false,
          HTMLCollection: false,
          HTMLFormElement: false,
          HTMLSelectElement: false,
          MediaList: true,
          // TODO: Not spec compliant, should be false.
          MimeTypeArray: false,
          NamedNodeMap: false,
          NodeList: true,
          PaintRequestList: false,
          Plugin: false,
          PluginArray: false,
          SVGLengthList: false,
          SVGNumberList: false,
          SVGPathSegList: false,
          SVGPointList: false,
          SVGStringList: false,
          SVGTransformList: false,
          SourceBufferList: false,
          StyleSheetList: true,
          // TODO: Not spec compliant, should be false.
          TextTrackCueList: false,
          TextTrackList: false,
          TouchList: false
        };

        for (var collections = getKeys(DOMIterables), i = 0; i < collections.length; i++) {
          var NAME = collections[i];
          var explicit = DOMIterables[NAME];
          var Collection = global[NAME];
          var proto = Collection && Collection.prototype;
          var key;

          if (proto) {
            if (!proto[ITERATOR]) hide(proto, ITERATOR, ArrayValues);
            if (!proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
            Iterators[NAME] = ArrayValues;
            if (explicit) for (key in $iterators) {
              if (!proto[key]) redefine(proto, key, $iterators[key], true);
            }
          }
        }
        /***/

      },

      /***/
      "b0c5": function b0c5(module, exports, __webpack_require__) {
        "use strict";

        var regexpExec = __webpack_require__("520a");

        __webpack_require__("5ca1")({
          target: 'RegExp',
          proto: true,
          forced: regexpExec !== /./.exec
        }, {
          exec: regexpExec
        });
        /***/

      },

      /***/
      "be13": function be13(module, exports) {
        // 7.2.1 RequireObjectCoercible(argument)
        module.exports = function (it) {
          if (it == undefined) throw TypeError("Can't call method on  " + it);
          return it;
        };
        /***/

      },

      /***/
      "c366": function c366(module, exports, __webpack_require__) {
        // false -> Array#indexOf
        // true  -> Array#includes
        var toIObject = __webpack_require__("6821");

        var toLength = __webpack_require__("9def");

        var toAbsoluteIndex = __webpack_require__("77f1");

        module.exports = function (IS_INCLUDES) {
          return function ($this, el, fromIndex) {
            var O = toIObject($this);
            var length = toLength(O.length);
            var index = toAbsoluteIndex(fromIndex, length);
            var value; // Array#includes uses SameValueZero equality algorithm
            // eslint-disable-next-line no-self-compare

            if (IS_INCLUDES && el != el) while (length > index) {
              value = O[index++]; // eslint-disable-next-line no-self-compare

              if (value != value) return true; // Array#indexOf ignores holes, Array#includes - not
            } else for (; length > index; index++) {
              if (IS_INCLUDES || index in O) {
                if (O[index] === el) return IS_INCLUDES || index || 0;
              }
            }
            return !IS_INCLUDES && -1;
          };
        };
        /***/

      },

      /***/
      "c649": function c649(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        /* WEBPACK VAR INJECTION */

        (function (global) {
          /* harmony export (binding) */
          __webpack_require__.d(__webpack_exports__, "c", function () {
            return insertNodeAt;
          });
          /* harmony export (binding) */


          __webpack_require__.d(__webpack_exports__, "a", function () {
            return camelize;
          });
          /* harmony export (binding) */


          __webpack_require__.d(__webpack_exports__, "b", function () {
            return console;
          });
          /* harmony export (binding) */


          __webpack_require__.d(__webpack_exports__, "d", function () {
            return removeNode;
          });
          /* harmony import */


          var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a481");
          /* harmony import */


          var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__);

          function getConsole() {
            if (typeof window !== "undefined") {
              return window.console;
            }

            return global.console;
          }

          var console = getConsole();

          function cached(fn) {
            var cache = Object.create(null);
            return function cachedFn(str) {
              var hit = cache[str];
              return hit || (cache[str] = fn(str));
            };
          }

          var regex = /-(\w)/g;
          var camelize = cached(function (str) {
            return str.replace(regex, function (_, c) {
              return c ? c.toUpperCase() : "";
            });
          });

          function removeNode(node) {
            if (node.parentElement !== null) {
              node.parentElement.removeChild(node);
            }
          }

          function insertNodeAt(fatherNode, node, position) {
            var refNode = position === 0 ? fatherNode.children[0] : fatherNode.children[position - 1].nextSibling;
            fatherNode.insertBefore(node, refNode);
          }
          /* WEBPACK VAR INJECTION */

        }).call(this, __webpack_require__("c8ba"));
        /***/
      },

      /***/
      "c69a": function c69a(module, exports, __webpack_require__) {
        module.exports = !__webpack_require__("9e1e") && !__webpack_require__("79e5")(function () {
          return Object.defineProperty(__webpack_require__("230e")('div'), 'a', {
            get: function get() {
              return 7;
            }
          }).a != 7;
        });
        /***/
      },

      /***/
      "c8ba": function c8ba(module, exports) {
        var g; // This works in non-strict mode

        g = function () {
          return this;
        }();

        try {
          // This works if eval is allowed (see CSP)
          g = g || new Function("return this")();
        } catch (e) {
          // This works if the window reference is available
          if ((typeof window === "undefined" ? "undefined" : _typeof(window)) === "object") g = window;
        } // g can still be undefined, but nothing to do about it...
        // We return undefined, instead of nothing here, so it's
        // easier to handle this case. if(!global) { ...}


        module.exports = g;
        /***/
      },

      /***/
      "ca5a": function ca5a(module, exports) {
        var id = 0;
        var px = Math.random();

        module.exports = function (key) {
          return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
        };
        /***/

      },

      /***/
      "cadf": function cadf(module, exports, __webpack_require__) {
        "use strict";

        var addToUnscopables = __webpack_require__("9c6c");

        var step = __webpack_require__("d53b");

        var Iterators = __webpack_require__("84f2");

        var toIObject = __webpack_require__("6821"); // 22.1.3.4 Array.prototype.entries()
        // 22.1.3.13 Array.prototype.keys()
        // 22.1.3.29 Array.prototype.values()
        // 22.1.3.30 Array.prototype[@@iterator]()


        module.exports = __webpack_require__("01f9")(Array, 'Array', function (iterated, kind) {
          this._t = toIObject(iterated); // target

          this._i = 0; // next index

          this._k = kind; // kind
          // 22.1.5.2.1 %ArrayIteratorPrototype%.next()
        }, function () {
          var O = this._t;
          var kind = this._k;
          var index = this._i++;

          if (!O || index >= O.length) {
            this._t = undefined;
            return step(1);
          }

          if (kind == 'keys') return step(0, index);
          if (kind == 'values') return step(0, O[index]);
          return step(0, [index, O[index]]);
        }, 'values'); // argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)

        Iterators.Arguments = Iterators.Array;
        addToUnscopables('keys');
        addToUnscopables('values');
        addToUnscopables('entries');
        /***/
      },

      /***/
      "cb7c": function cb7c(module, exports, __webpack_require__) {
        var isObject = __webpack_require__("d3f4");

        module.exports = function (it) {
          if (!isObject(it)) throw TypeError(it + ' is not an object!');
          return it;
        };
        /***/

      },

      /***/
      "ce10": function ce10(module, exports, __webpack_require__) {
        var has = __webpack_require__("69a8");

        var toIObject = __webpack_require__("6821");

        var arrayIndexOf = __webpack_require__("c366")(false);

        var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

        module.exports = function (object, names) {
          var O = toIObject(object);
          var i = 0;
          var result = [];
          var key;

          for (key in O) {
            if (key != IE_PROTO) has(O, key) && result.push(key);
          } // Don't enum bug & hidden keys


          while (names.length > i) {
            if (has(O, key = names[i++])) {
              ~arrayIndexOf(result, key) || result.push(key);
            }
          }

          return result;
        };
        /***/

      },

      /***/
      "d2c8": function d2c8(module, exports, __webpack_require__) {
        // helper for String#{startsWith, endsWith, includes}
        var isRegExp = __webpack_require__("aae3");

        var defined = __webpack_require__("be13");

        module.exports = function (that, searchString, NAME) {
          if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
          return String(defined(that));
        };
        /***/

      },

      /***/
      "d3f4": function d3f4(module, exports) {
        module.exports = function (it) {
          return _typeof(it) === 'object' ? it !== null : typeof it === 'function';
        };
        /***/

      },

      /***/
      "d53b": function d53b(module, exports) {
        module.exports = function (done, value) {
          return {
            value: value,
            done: !!done
          };
        };
        /***/

      },

      /***/
      "d8e8": function d8e8(module, exports) {
        module.exports = function (it) {
          if (typeof it != 'function') throw TypeError(it + ' is not a function!');
          return it;
        };
        /***/

      },

      /***/
      "e11e": function e11e(module, exports) {
        // IE 8- don't enum bug keys
        module.exports = 'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'.split(',');
        /***/
      },

      /***/
      "f559": function f559(module, exports, __webpack_require__) {
        "use strict"; // 21.1.3.18 String.prototype.startsWith(searchString [, position ])

        var $export = __webpack_require__("5ca1");

        var toLength = __webpack_require__("9def");

        var context = __webpack_require__("d2c8");

        var STARTS_WITH = 'startsWith';
        var $startsWith = ''[STARTS_WITH];
        $export($export.P + $export.F * __webpack_require__("5147")(STARTS_WITH), 'String', {
          startsWith: function startsWith(searchString
          /* , position = 0 */
          ) {
            var that = context(this, searchString, STARTS_WITH);
            var index = toLength(Math.min(arguments.length > 1 ? arguments[1] : undefined, that.length));
            var search = String(searchString);
            return $startsWith ? $startsWith.call(that, search, index) : that.slice(index, index + search.length) === search;
          }
        });
        /***/
      },

      /***/
      "f6fd": function f6fd(module, exports) {
        // document.currentScript polyfill by Adam Miller
        // MIT license
        (function (document) {
          var currentScript = "currentScript",
              scripts = document.getElementsByTagName('script'); // Live NodeList collection
          // If browser needs currentScript polyfill, add get currentScript() to the document object

          if (!(currentScript in document)) {
            Object.defineProperty(document, currentScript, {
              get: function get() {
                // IE 6-10 supports script readyState
                // IE 10+ support stack trace
                try {
                  throw new Error();
                } catch (err) {
                  // Find the second match for the "at" string to get file src url from stack.
                  // Specifically works with the format of stack traces in IE.
                  var i,
                      res = (/.*at [^\(]*\((.*):.+:.+\)$/ig.exec(err.stack) || [false])[1]; // For all scripts on the page, if src matches or if ready state is interactive, return the script tag

                  for (i in scripts) {
                    if (scripts[i].src == res || scripts[i].readyState == "interactive") {
                      return scripts[i];
                    }
                  } // If no match, return null


                  return null;
                }
              }
            });
          }
        })(document);
        /***/

      },

      /***/
      "f751": function f751(module, exports, __webpack_require__) {
        // 19.1.3.1 Object.assign(target, source)
        var $export = __webpack_require__("5ca1");

        $export($export.S + $export.F, 'Object', {
          assign: __webpack_require__("7333")
        });
        /***/
      },

      /***/
      "fa5b": function fa5b(module, exports, __webpack_require__) {
        module.exports = __webpack_require__("5537")('native-function-to-string', Function.toString);
        /***/
      },

      /***/
      "fab2": function fab2(module, exports, __webpack_require__) {
        var document = __webpack_require__("7726").document;

        module.exports = document && document.documentElement;
        /***/
      },

      /***/
      "fb15": function fb15(module, __webpack_exports__, __webpack_require__) {
        "use strict"; // ESM COMPAT FLAG

        __webpack_require__.r(__webpack_exports__); // CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
        // This file is imported into lib/wc client bundles.


        if (typeof window !== 'undefined') {
          if (true) {
            __webpack_require__("f6fd");
          }

          var setPublicPath_i;

          if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
            __webpack_require__.p = setPublicPath_i[1]; // eslint-disable-line
          }
        } // Indicate to webpack that this file can be concatenated

        /* harmony default export */


        var setPublicPath = null; // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.assign.js

        var es6_object_assign = __webpack_require__("f751"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.starts-with.js


        var es6_string_starts_with = __webpack_require__("f559"); // EXTERNAL MODULE: ./node_modules/core-js/modules/web.dom.iterable.js


        var web_dom_iterable = __webpack_require__("ac6a"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.iterator.js


        var es6_array_iterator = __webpack_require__("cadf"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.keys.js


        var es6_object_keys = __webpack_require__("456d"); // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js


        function _arrayWithHoles(arr) {
          if (Array.isArray(arr)) return arr;
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js


        function _iterableToArrayLimit(arr, i) {
          if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
          var _arr = [];
          var _n = true;
          var _d = false;
          var _e = undefined;

          try {
            for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
              _arr.push(_s.value);

              if (i && _arr.length === i) break;
            }
          } catch (err) {
            _d = true;
            _e = err;
          } finally {
            try {
              if (!_n && _i["return"] != null) _i["return"]();
            } finally {
              if (_d) throw _e;
            }
          }

          return _arr;
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js


        function _arrayLikeToArray(arr, len) {
          if (len == null || len > arr.length) len = arr.length;

          for (var i = 0, arr2 = new Array(len); i < len; i++) {
            arr2[i] = arr[i];
          }

          return arr2;
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js


        function _unsupportedIterableToArray(o, minLen) {
          if (!o) return;
          if (typeof o === "string") return _arrayLikeToArray(o, minLen);
          var n = Object.prototype.toString.call(o).slice(8, -1);
          if (n === "Object" && o.constructor) n = o.constructor.name;
          if (n === "Map" || n === "Set") return Array.from(o);
          if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js


        function _nonIterableRest() {
          throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js


        function _slicedToArray(arr, i) {
          return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
        } // EXTERNAL MODULE: ./node_modules/core-js/modules/es7.array.includes.js


        var es7_array_includes = __webpack_require__("6762"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.includes.js


        var es6_string_includes = __webpack_require__("2fdb"); // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js


        function _arrayWithoutHoles(arr) {
          if (Array.isArray(arr)) return _arrayLikeToArray(arr);
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js


        function _iterableToArray(iter) {
          if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js


        function _nonIterableSpread() {
          throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js


        function _toConsumableArray(arr) {
          return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
        } // EXTERNAL MODULE: external {"commonjs":"sortablejs","commonjs2":"sortablejs","amd":"sortablejs","root":"Sortable"}


        var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_ = __webpack_require__("a352");

        var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default = /*#__PURE__*/__webpack_require__.n(external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_); // EXTERNAL MODULE: ./src/util/helper.js


        var helper = __webpack_require__("c649"); // CONCATENATED MODULE: ./src/vuedraggable.js


        function buildAttribute(object, propName, value) {
          if (value === undefined) {
            return object;
          }

          object = object || {};
          object[propName] = value;
          return object;
        }

        function computeVmIndex(vnodes, element) {
          return vnodes.map(function (elt) {
            return elt.elm;
          }).indexOf(element);
        }

        function _computeIndexes(slots, children, isTransition, footerOffset) {
          if (!slots) {
            return [];
          }

          var elmFromNodes = slots.map(function (elt) {
            return elt.elm;
          });
          var footerIndex = children.length - footerOffset;

          var rawIndexes = _toConsumableArray(children).map(function (elt, idx) {
            return idx >= footerIndex ? elmFromNodes.length : elmFromNodes.indexOf(elt);
          });

          return isTransition ? rawIndexes.filter(function (ind) {
            return ind !== -1;
          }) : rawIndexes;
        }

        function emit(evtName, evtData) {
          var _this = this;

          this.$nextTick(function () {
            return _this.$emit(evtName.toLowerCase(), evtData);
          });
        }

        function delegateAndEmit(evtName) {
          var _this2 = this;

          return function (evtData) {
            if (_this2.realList !== null) {
              _this2["onDrag" + evtName](evtData);
            }

            emit.call(_this2, evtName, evtData);
          };
        }

        function isTransitionName(name) {
          return ["transition-group", "TransitionGroup"].includes(name);
        }

        function vuedraggable_isTransition(slots) {
          if (!slots || slots.length !== 1) {
            return false;
          }

          var _slots = _slicedToArray(slots, 1),
              componentOptions = _slots[0].componentOptions;

          if (!componentOptions) {
            return false;
          }

          return isTransitionName(componentOptions.tag);
        }

        function getSlot(slot, scopedSlot, key) {
          return slot[key] || (scopedSlot[key] ? scopedSlot[key]() : undefined);
        }

        function computeChildrenAndOffsets(children, slot, scopedSlot) {
          var headerOffset = 0;
          var footerOffset = 0;
          var header = getSlot(slot, scopedSlot, "header");

          if (header) {
            headerOffset = header.length;
            children = children ? [].concat(_toConsumableArray(header), _toConsumableArray(children)) : _toConsumableArray(header);
          }

          var footer = getSlot(slot, scopedSlot, "footer");

          if (footer) {
            footerOffset = footer.length;
            children = children ? [].concat(_toConsumableArray(children), _toConsumableArray(footer)) : _toConsumableArray(footer);
          }

          return {
            children: children,
            headerOffset: headerOffset,
            footerOffset: footerOffset
          };
        }

        function getComponentAttributes($attrs, componentData) {
          var attributes = null;

          var update = function update(name, value) {
            attributes = buildAttribute(attributes, name, value);
          };

          var attrs = Object.keys($attrs).filter(function (key) {
            return key === "id" || key.startsWith("data-");
          }).reduce(function (res, key) {
            res[key] = $attrs[key];
            return res;
          }, {});
          update("attrs", attrs);

          if (!componentData) {
            return attributes;
          }

          var on = componentData.on,
              props = componentData.props,
              componentDataAttrs = componentData.attrs;
          update("on", on);
          update("props", props);
          Object.assign(attributes.attrs, componentDataAttrs);
          return attributes;
        }

        var eventsListened = ["Start", "Add", "Remove", "Update", "End"];
        var eventsToEmit = ["Choose", "Unchoose", "Sort", "Filter", "Clone"];
        var readonlyProperties = ["Move"].concat(eventsListened, eventsToEmit).map(function (evt) {
          return "on" + evt;
        });
        var draggingElement = null;
        var props = {
          options: Object,
          list: {
            type: Array,
            required: false,
            "default": null
          },
          value: {
            type: Array,
            required: false,
            "default": null
          },
          noTransitionOnDrag: {
            type: Boolean,
            "default": false
          },
          clone: {
            type: Function,
            "default": function _default(original) {
              return original;
            }
          },
          element: {
            type: String,
            "default": "div"
          },
          tag: {
            type: String,
            "default": null
          },
          move: {
            type: Function,
            "default": null
          },
          componentData: {
            type: Object,
            required: false,
            "default": null
          }
        };
        var draggableComponent = {
          name: "draggable",
          inheritAttrs: false,
          props: props,
          data: function data() {
            return {
              transitionMode: false,
              noneFunctionalComponentMode: false
            };
          },
          render: function render(h) {
            var slots = this.$slots["default"];
            this.transitionMode = vuedraggable_isTransition(slots);

            var _computeChildrenAndOf = computeChildrenAndOffsets(slots, this.$slots, this.$scopedSlots),
                children = _computeChildrenAndOf.children,
                headerOffset = _computeChildrenAndOf.headerOffset,
                footerOffset = _computeChildrenAndOf.footerOffset;

            this.headerOffset = headerOffset;
            this.footerOffset = footerOffset;
            var attributes = getComponentAttributes(this.$attrs, this.componentData);
            return h(this.getTag(), attributes, children);
          },
          created: function created() {
            if (this.list !== null && this.value !== null) {
              helper["b"
              /* console */
              ].error("Value and list props are mutually exclusive! Please set one or another.");
            }

            if (this.element !== "div") {
              helper["b"
              /* console */
              ].warn("Element props is deprecated please use tag props instead. See https://github.com/SortableJS/Vue.Draggable/blob/master/documentation/migrate.md#element-props");
            }

            if (this.options !== undefined) {
              helper["b"
              /* console */
              ].warn("Options props is deprecated, add sortable options directly as vue.draggable item, or use v-bind. See https://github.com/SortableJS/Vue.Draggable/blob/master/documentation/migrate.md#options-props");
            }
          },
          mounted: function mounted() {
            var _this3 = this;

            this.noneFunctionalComponentMode = this.getTag().toLowerCase() !== this.$el.nodeName.toLowerCase() && !this.getIsFunctional();

            if (this.noneFunctionalComponentMode && this.transitionMode) {
              throw new Error("Transition-group inside component is not supported. Please alter tag value or remove transition-group. Current tag value: ".concat(this.getTag()));
            }

            var optionsAdded = {};
            eventsListened.forEach(function (elt) {
              optionsAdded["on" + elt] = delegateAndEmit.call(_this3, elt);
            });
            eventsToEmit.forEach(function (elt) {
              optionsAdded["on" + elt] = emit.bind(_this3, elt);
            });
            var attributes = Object.keys(this.$attrs).reduce(function (res, key) {
              res[Object(helper["a"
              /* camelize */
              ])(key)] = _this3.$attrs[key];
              return res;
            }, {});
            var options = Object.assign({}, this.options, attributes, optionsAdded, {
              onMove: function onMove(evt, originalEvent) {
                return _this3.onDragMove(evt, originalEvent);
              }
            });
            !("draggable" in options) && (options.draggable = ">*");
            this._sortable = new external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default.a(this.rootContainer, options);
            this.computeIndexes();
          },
          beforeDestroy: function beforeDestroy() {
            if (this._sortable !== undefined) this._sortable.destroy();
          },
          computed: {
            rootContainer: function rootContainer() {
              return this.transitionMode ? this.$el.children[0] : this.$el;
            },
            realList: function realList() {
              return this.list ? this.list : this.value;
            }
          },
          watch: {
            options: {
              handler: function handler(newOptionValue) {
                this.updateOptions(newOptionValue);
              },
              deep: true
            },
            $attrs: {
              handler: function handler(newOptionValue) {
                this.updateOptions(newOptionValue);
              },
              deep: true
            },
            realList: function realList() {
              this.computeIndexes();
            }
          },
          methods: {
            getIsFunctional: function getIsFunctional() {
              var fnOptions = this._vnode.fnOptions;
              return fnOptions && fnOptions.functional;
            },
            getTag: function getTag() {
              return this.tag || this.element;
            },
            updateOptions: function updateOptions(newOptionValue) {
              for (var property in newOptionValue) {
                var value = Object(helper["a"
                /* camelize */
                ])(property);

                if (readonlyProperties.indexOf(value) === -1) {
                  this._sortable.option(value, newOptionValue[property]);
                }
              }
            },
            getChildrenNodes: function getChildrenNodes() {
              if (this.noneFunctionalComponentMode) {
                return this.$children[0].$slots["default"];
              }

              var rawNodes = this.$slots["default"];
              return this.transitionMode ? rawNodes[0].child.$slots["default"] : rawNodes;
            },
            computeIndexes: function computeIndexes() {
              var _this4 = this;

              this.$nextTick(function () {
                _this4.visibleIndexes = _computeIndexes(_this4.getChildrenNodes(), _this4.rootContainer.children, _this4.transitionMode, _this4.footerOffset);
              });
            },
            getUnderlyingVm: function getUnderlyingVm(htmlElt) {
              var index = computeVmIndex(this.getChildrenNodes() || [], htmlElt);

              if (index === -1) {
                //Edge case during move callback: related element might be
                //an element different from collection
                return null;
              }

              var element = this.realList[index];
              return {
                index: index,
                element: element
              };
            },
            getUnderlyingPotencialDraggableComponent: function getUnderlyingPotencialDraggableComponent(_ref) {
              var vue = _ref.__vue__;

              if (!vue || !vue.$options || !isTransitionName(vue.$options._componentTag)) {
                if (!("realList" in vue) && vue.$children.length === 1 && "realList" in vue.$children[0]) return vue.$children[0];
                return vue;
              }

              return vue.$parent;
            },
            emitChanges: function emitChanges(evt) {
              var _this5 = this;

              this.$nextTick(function () {
                _this5.$emit("change", evt);
              });
            },
            alterList: function alterList(onList) {
              if (this.list) {
                onList(this.list);
                return;
              }

              var newList = _toConsumableArray(this.value);

              onList(newList);
              this.$emit("input", newList);
            },
            spliceList: function spliceList() {
              var _arguments = arguments;

              var spliceList = function spliceList(list) {
                return list.splice.apply(list, _toConsumableArray(_arguments));
              };

              this.alterList(spliceList);
            },
            updatePosition: function updatePosition(oldIndex, newIndex) {
              var updatePosition = function updatePosition(list) {
                return list.splice(newIndex, 0, list.splice(oldIndex, 1)[0]);
              };

              this.alterList(updatePosition);
            },
            getRelatedContextFromMoveEvent: function getRelatedContextFromMoveEvent(_ref2) {
              var to = _ref2.to,
                  related = _ref2.related;
              var component = this.getUnderlyingPotencialDraggableComponent(to);

              if (!component) {
                return {
                  component: component
                };
              }

              var list = component.realList;
              var context = {
                list: list,
                component: component
              };

              if (to !== related && list && component.getUnderlyingVm) {
                var destination = component.getUnderlyingVm(related);

                if (destination) {
                  return Object.assign(destination, context);
                }
              }

              return context;
            },
            getVmIndex: function getVmIndex(domIndex) {
              var indexes = this.visibleIndexes;
              var numberIndexes = indexes.length;
              return domIndex > numberIndexes - 1 ? numberIndexes : indexes[domIndex];
            },
            getComponent: function getComponent() {
              return this.$slots["default"][0].componentInstance;
            },
            resetTransitionData: function resetTransitionData(index) {
              if (!this.noTransitionOnDrag || !this.transitionMode) {
                return;
              }

              var nodes = this.getChildrenNodes();
              nodes[index].data = null;
              var transitionContainer = this.getComponent();
              transitionContainer.children = [];
              transitionContainer.kept = undefined;
            },
            onDragStart: function onDragStart(evt) {
              this.context = this.getUnderlyingVm(evt.item);
              evt.item._underlying_vm_ = this.clone(this.context.element);
              draggingElement = evt.item;
            },
            onDragAdd: function onDragAdd(evt) {
              var element = evt.item._underlying_vm_;

              if (element === undefined) {
                return;
              }

              Object(helper["d"
              /* removeNode */
              ])(evt.item);
              var newIndex = this.getVmIndex(evt.newIndex);
              this.spliceList(newIndex, 0, element);
              this.computeIndexes();
              var added = {
                element: element,
                newIndex: newIndex
              };
              this.emitChanges({
                added: added
              });
            },
            onDragRemove: function onDragRemove(evt) {
              Object(helper["c"
              /* insertNodeAt */
              ])(this.rootContainer, evt.item, evt.oldIndex);

              if (evt.pullMode === "clone") {
                Object(helper["d"
                /* removeNode */
                ])(evt.clone);
                return;
              }

              var oldIndex = this.context.index;
              this.spliceList(oldIndex, 1);
              var removed = {
                element: this.context.element,
                oldIndex: oldIndex
              };
              this.resetTransitionData(oldIndex);
              this.emitChanges({
                removed: removed
              });
            },
            onDragUpdate: function onDragUpdate(evt) {
              Object(helper["d"
              /* removeNode */
              ])(evt.item);
              Object(helper["c"
              /* insertNodeAt */
              ])(evt.from, evt.item, evt.oldIndex);
              var oldIndex = this.context.index;
              var newIndex = this.getVmIndex(evt.newIndex);
              this.updatePosition(oldIndex, newIndex);
              var moved = {
                element: this.context.element,
                oldIndex: oldIndex,
                newIndex: newIndex
              };
              this.emitChanges({
                moved: moved
              });
            },
            updateProperty: function updateProperty(evt, propertyName) {
              evt.hasOwnProperty(propertyName) && (evt[propertyName] += this.headerOffset);
            },
            computeFutureIndex: function computeFutureIndex(relatedContext, evt) {
              if (!relatedContext.element) {
                return 0;
              }

              var domChildren = _toConsumableArray(evt.to.children).filter(function (el) {
                return el.style["display"] !== "none";
              });

              var currentDOMIndex = domChildren.indexOf(evt.related);
              var currentIndex = relatedContext.component.getVmIndex(currentDOMIndex);
              var draggedInList = domChildren.indexOf(draggingElement) !== -1;
              return draggedInList || !evt.willInsertAfter ? currentIndex : currentIndex + 1;
            },
            onDragMove: function onDragMove(evt, originalEvent) {
              var onMove = this.move;

              if (!onMove || !this.realList) {
                return true;
              }

              var relatedContext = this.getRelatedContextFromMoveEvent(evt);
              var draggedContext = this.context;
              var futureIndex = this.computeFutureIndex(relatedContext, evt);
              Object.assign(draggedContext, {
                futureIndex: futureIndex
              });
              var sendEvt = Object.assign({}, evt, {
                relatedContext: relatedContext,
                draggedContext: draggedContext
              });
              return onMove(sendEvt, originalEvent);
            },
            onDragEnd: function onDragEnd() {
              this.computeIndexes();
              draggingElement = null;
            }
          }
        };

        if (typeof window !== "undefined" && "Vue" in window) {
          window.Vue.component("draggable", draggableComponent);
        }
        /* harmony default export */


        var vuedraggable = draggableComponent; // CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib.js

        /* harmony default export */

        var entry_lib = __webpack_exports__["default"] = vuedraggable;
        /***/
      }
      /******/

    })["default"]
  );
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/webpack/buildin/module.js":
/*!***********************************!*\
  !*** (webpack)/buildin/module.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function (module) {
  if (!module.webpackPolyfill) {
    module.deprecate = function () {};

    module.paths = []; // module.parent = undefined by default

    if (!module.children) module.children = [];
    Object.defineProperty(module, "loaded", {
      enumerable: true,
      get: function get() {
        return module.l;
      }
    });
    Object.defineProperty(module, "id", {
      enumerable: true,
      get: function get() {
        return module.i;
      }
    });
    module.webpackPolyfill = 1;
  }

  return module;
};

/***/ }),

/***/ 7:
/*!****************************************************!*\
  !*** multi ./assets/blocks/accordion/accordion.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/assets/blocks/accordion/accordion.js */"./assets/blocks/accordion/accordion.js");


/***/ })

/******/ });