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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 282);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

if (false) {
  module.exports = require('./vue.common.prod.js')
} else {
  module.exports = __webpack_require__(14)
}


/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global, setImmediate) {/*!
 * Vue.js v2.6.10
 * (c) 2014-2019 Evan You
 * Released under the MIT License.
 */


/*  */

var emptyObject = Object.freeze({});

// These helpers produce better VM code in JS engines due to their
// explicitness and function inlining.
function isUndef (v) {
  return v === undefined || v === null
}

function isDef (v) {
  return v !== undefined && v !== null
}

function isTrue (v) {
  return v === true
}

function isFalse (v) {
  return v === false
}

/**
 * Check if value is primitive.
 */
function isPrimitive (value) {
  return (
    typeof value === 'string' ||
    typeof value === 'number' ||
    // $flow-disable-line
    typeof value === 'symbol' ||
    typeof value === 'boolean'
  )
}

/**
 * Quick object check - this is primarily used to tell
 * Objects from primitive values when we know the value
 * is a JSON-compliant type.
 */
function isObject (obj) {
  return obj !== null && typeof obj === 'object'
}

/**
 * Get the raw type string of a value, e.g., [object Object].
 */
var _toString = Object.prototype.toString;

function toRawType (value) {
  return _toString.call(value).slice(8, -1)
}

/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */
function isPlainObject (obj) {
  return _toString.call(obj) === '[object Object]'
}

function isRegExp (v) {
  return _toString.call(v) === '[object RegExp]'
}

/**
 * Check if val is a valid array index.
 */
function isValidArrayIndex (val) {
  var n = parseFloat(String(val));
  return n >= 0 && Math.floor(n) === n && isFinite(val)
}

function isPromise (val) {
  return (
    isDef(val) &&
    typeof val.then === 'function' &&
    typeof val.catch === 'function'
  )
}

/**
 * Convert a value to a string that is actually rendered.
 */
function toString (val) {
  return val == null
    ? ''
    : Array.isArray(val) || (isPlainObject(val) && val.toString === _toString)
      ? JSON.stringify(val, null, 2)
      : String(val)
}

/**
 * Convert an input value to a number for persistence.
 * If the conversion fails, return original string.
 */
function toNumber (val) {
  var n = parseFloat(val);
  return isNaN(n) ? val : n
}

/**
 * Make a map and return a function for checking if a key
 * is in that map.
 */
function makeMap (
  str,
  expectsLowerCase
) {
  var map = Object.create(null);
  var list = str.split(',');
  for (var i = 0; i < list.length; i++) {
    map[list[i]] = true;
  }
  return expectsLowerCase
    ? function (val) { return map[val.toLowerCase()]; }
    : function (val) { return map[val]; }
}

/**
 * Check if a tag is a built-in tag.
 */
var isBuiltInTag = makeMap('slot,component', true);

/**
 * Check if an attribute is a reserved attribute.
 */
var isReservedAttribute = makeMap('key,ref,slot,slot-scope,is');

/**
 * Remove an item from an array.
 */
function remove (arr, item) {
  if (arr.length) {
    var index = arr.indexOf(item);
    if (index > -1) {
      return arr.splice(index, 1)
    }
  }
}

/**
 * Check whether an object has the property.
 */
var hasOwnProperty = Object.prototype.hasOwnProperty;
function hasOwn (obj, key) {
  return hasOwnProperty.call(obj, key)
}

/**
 * Create a cached version of a pure function.
 */
function cached (fn) {
  var cache = Object.create(null);
  return (function cachedFn (str) {
    var hit = cache[str];
    return hit || (cache[str] = fn(str))
  })
}

/**
 * Camelize a hyphen-delimited string.
 */
var camelizeRE = /-(\w)/g;
var camelize = cached(function (str) {
  return str.replace(camelizeRE, function (_, c) { return c ? c.toUpperCase() : ''; })
});

/**
 * Capitalize a string.
 */
var capitalize = cached(function (str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
});

/**
 * Hyphenate a camelCase string.
 */
var hyphenateRE = /\B([A-Z])/g;
var hyphenate = cached(function (str) {
  return str.replace(hyphenateRE, '-$1').toLowerCase()
});

/**
 * Simple bind polyfill for environments that do not support it,
 * e.g., PhantomJS 1.x. Technically, we don't need this anymore
 * since native bind is now performant enough in most browsers.
 * But removing it would mean breaking code that was able to run in
 * PhantomJS 1.x, so this must be kept for backward compatibility.
 */

/* istanbul ignore next */
function polyfillBind (fn, ctx) {
  function boundFn (a) {
    var l = arguments.length;
    return l
      ? l > 1
        ? fn.apply(ctx, arguments)
        : fn.call(ctx, a)
      : fn.call(ctx)
  }

  boundFn._length = fn.length;
  return boundFn
}

function nativeBind (fn, ctx) {
  return fn.bind(ctx)
}

var bind = Function.prototype.bind
  ? nativeBind
  : polyfillBind;

/**
 * Convert an Array-like object to a real Array.
 */
function toArray (list, start) {
  start = start || 0;
  var i = list.length - start;
  var ret = new Array(i);
  while (i--) {
    ret[i] = list[i + start];
  }
  return ret
}

/**
 * Mix properties into target object.
 */
function extend (to, _from) {
  for (var key in _from) {
    to[key] = _from[key];
  }
  return to
}

/**
 * Merge an Array of Objects into a single Object.
 */
function toObject (arr) {
  var res = {};
  for (var i = 0; i < arr.length; i++) {
    if (arr[i]) {
      extend(res, arr[i]);
    }
  }
  return res
}

/* eslint-disable no-unused-vars */

/**
 * Perform no operation.
 * Stubbing args to make Flow happy without leaving useless transpiled code
 * with ...rest (https://flow.org/blog/2017/05/07/Strict-Function-Call-Arity/).
 */
function noop (a, b, c) {}

/**
 * Always return false.
 */
var no = function (a, b, c) { return false; };

/* eslint-enable no-unused-vars */

/**
 * Return the same value.
 */
var identity = function (_) { return _; };

/**
 * Generate a string containing static keys from compiler modules.
 */
function genStaticKeys (modules) {
  return modules.reduce(function (keys, m) {
    return keys.concat(m.staticKeys || [])
  }, []).join(',')
}

/**
 * Check if two values are loosely equal - that is,
 * if they are plain objects, do they have the same shape?
 */
function looseEqual (a, b) {
  if (a === b) { return true }
  var isObjectA = isObject(a);
  var isObjectB = isObject(b);
  if (isObjectA && isObjectB) {
    try {
      var isArrayA = Array.isArray(a);
      var isArrayB = Array.isArray(b);
      if (isArrayA && isArrayB) {
        return a.length === b.length && a.every(function (e, i) {
          return looseEqual(e, b[i])
        })
      } else if (a instanceof Date && b instanceof Date) {
        return a.getTime() === b.getTime()
      } else if (!isArrayA && !isArrayB) {
        var keysA = Object.keys(a);
        var keysB = Object.keys(b);
        return keysA.length === keysB.length && keysA.every(function (key) {
          return looseEqual(a[key], b[key])
        })
      } else {
        /* istanbul ignore next */
        return false
      }
    } catch (e) {
      /* istanbul ignore next */
      return false
    }
  } else if (!isObjectA && !isObjectB) {
    return String(a) === String(b)
  } else {
    return false
  }
}

/**
 * Return the first index at which a loosely equal value can be
 * found in the array (if value is a plain object, the array must
 * contain an object of the same shape), or -1 if it is not present.
 */
function looseIndexOf (arr, val) {
  for (var i = 0; i < arr.length; i++) {
    if (looseEqual(arr[i], val)) { return i }
  }
  return -1
}

/**
 * Ensure a function is called only once.
 */
function once (fn) {
  var called = false;
  return function () {
    if (!called) {
      called = true;
      fn.apply(this, arguments);
    }
  }
}

var SSR_ATTR = 'data-server-rendered';

var ASSET_TYPES = [
  'component',
  'directive',
  'filter'
];

var LIFECYCLE_HOOKS = [
  'beforeCreate',
  'created',
  'beforeMount',
  'mounted',
  'beforeUpdate',
  'updated',
  'beforeDestroy',
  'destroyed',
  'activated',
  'deactivated',
  'errorCaptured',
  'serverPrefetch'
];

/*  */



var config = ({
  /**
   * Option merge strategies (used in core/util/options)
   */
  // $flow-disable-line
  optionMergeStrategies: Object.create(null),

  /**
   * Whether to suppress warnings.
   */
  silent: false,

  /**
   * Show production mode tip message on boot?
   */
  productionTip: "development" !== 'production',

  /**
   * Whether to enable devtools
   */
  devtools: "development" !== 'production',

  /**
   * Whether to record perf
   */
  performance: false,

  /**
   * Error handler for watcher errors
   */
  errorHandler: null,

  /**
   * Warn handler for watcher warns
   */
  warnHandler: null,

  /**
   * Ignore certain custom elements
   */
  ignoredElements: [],

  /**
   * Custom user key aliases for v-on
   */
  // $flow-disable-line
  keyCodes: Object.create(null),

  /**
   * Check if a tag is reserved so that it cannot be registered as a
   * component. This is platform-dependent and may be overwritten.
   */
  isReservedTag: no,

  /**
   * Check if an attribute is reserved so that it cannot be used as a component
   * prop. This is platform-dependent and may be overwritten.
   */
  isReservedAttr: no,

  /**
   * Check if a tag is an unknown element.
   * Platform-dependent.
   */
  isUnknownElement: no,

  /**
   * Get the namespace of an element
   */
  getTagNamespace: noop,

  /**
   * Parse the real tag name for the specific platform.
   */
  parsePlatformTagName: identity,

  /**
   * Check if an attribute must be bound using property, e.g. value
   * Platform-dependent.
   */
  mustUseProp: no,

  /**
   * Perform updates asynchronously. Intended to be used by Vue Test Utils
   * This will significantly reduce performance if set to false.
   */
  async: true,

  /**
   * Exposed for legacy reasons
   */
  _lifecycleHooks: LIFECYCLE_HOOKS
});

/*  */

/**
 * unicode letters used for parsing html tags, component names and property paths.
 * using https://www.w3.org/TR/html53/semantics-scripting.html#potentialcustomelementname
 * skipping \u10000-\uEFFFF due to it freezing up PhantomJS
 */
var unicodeRegExp = /a-zA-Z\u00B7\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u037D\u037F-\u1FFF\u200C-\u200D\u203F-\u2040\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD/;

/**
 * Check if a string starts with $ or _
 */
function isReserved (str) {
  var c = (str + '').charCodeAt(0);
  return c === 0x24 || c === 0x5F
}

/**
 * Define a property.
 */
function def (obj, key, val, enumerable) {
  Object.defineProperty(obj, key, {
    value: val,
    enumerable: !!enumerable,
    writable: true,
    configurable: true
  });
}

/**
 * Parse simple path.
 */
var bailRE = new RegExp(("[^" + (unicodeRegExp.source) + ".$_\\d]"));
function parsePath (path) {
  if (bailRE.test(path)) {
    return
  }
  var segments = path.split('.');
  return function (obj) {
    for (var i = 0; i < segments.length; i++) {
      if (!obj) { return }
      obj = obj[segments[i]];
    }
    return obj
  }
}

/*  */

// can we use __proto__?
var hasProto = '__proto__' in {};

// Browser environment sniffing
var inBrowser = typeof window !== 'undefined';
var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
var UA = inBrowser && window.navigator.userAgent.toLowerCase();
var isIE = UA && /msie|trident/.test(UA);
var isIE9 = UA && UA.indexOf('msie 9.0') > 0;
var isEdge = UA && UA.indexOf('edge/') > 0;
var isAndroid = (UA && UA.indexOf('android') > 0) || (weexPlatform === 'android');
var isIOS = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');
var isChrome = UA && /chrome\/\d+/.test(UA) && !isEdge;
var isPhantomJS = UA && /phantomjs/.test(UA);
var isFF = UA && UA.match(/firefox\/(\d+)/);

// Firefox has a "watch" function on Object.prototype...
var nativeWatch = ({}).watch;

var supportsPassive = false;
if (inBrowser) {
  try {
    var opts = {};
    Object.defineProperty(opts, 'passive', ({
      get: function get () {
        /* istanbul ignore next */
        supportsPassive = true;
      }
    })); // https://github.com/facebook/flow/issues/285
    window.addEventListener('test-passive', null, opts);
  } catch (e) {}
}

// this needs to be lazy-evaled because vue may be required before
// vue-server-renderer can set VUE_ENV
var _isServer;
var isServerRendering = function () {
  if (_isServer === undefined) {
    /* istanbul ignore if */
    if (!inBrowser && !inWeex && typeof global !== 'undefined') {
      // detect presence of vue-server-renderer and avoid
      // Webpack shimming the process
      _isServer = global['process'] && global['process'].env.VUE_ENV === 'server';
    } else {
      _isServer = false;
    }
  }
  return _isServer
};

// detect devtools
var devtools = inBrowser && window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

/* istanbul ignore next */
function isNative (Ctor) {
  return typeof Ctor === 'function' && /native code/.test(Ctor.toString())
}

var hasSymbol =
  typeof Symbol !== 'undefined' && isNative(Symbol) &&
  typeof Reflect !== 'undefined' && isNative(Reflect.ownKeys);

var _Set;
/* istanbul ignore if */ // $flow-disable-line
if (typeof Set !== 'undefined' && isNative(Set)) {
  // use native Set when available.
  _Set = Set;
} else {
  // a non-standard Set polyfill that only works with primitive keys.
  _Set = /*@__PURE__*/(function () {
    function Set () {
      this.set = Object.create(null);
    }
    Set.prototype.has = function has (key) {
      return this.set[key] === true
    };
    Set.prototype.add = function add (key) {
      this.set[key] = true;
    };
    Set.prototype.clear = function clear () {
      this.set = Object.create(null);
    };

    return Set;
  }());
}

/*  */

var warn = noop;
var tip = noop;
var generateComponentTrace = (noop); // work around flow check
var formatComponentName = (noop);

{
  var hasConsole = typeof console !== 'undefined';
  var classifyRE = /(?:^|[-_])(\w)/g;
  var classify = function (str) { return str
    .replace(classifyRE, function (c) { return c.toUpperCase(); })
    .replace(/[-_]/g, ''); };

  warn = function (msg, vm) {
    var trace = vm ? generateComponentTrace(vm) : '';

    if (config.warnHandler) {
      config.warnHandler.call(null, msg, vm, trace);
    } else if (hasConsole && (!config.silent)) {
      console.error(("[Vue warn]: " + msg + trace));
    }
  };

  tip = function (msg, vm) {
    if (hasConsole && (!config.silent)) {
      console.warn("[Vue tip]: " + msg + (
        vm ? generateComponentTrace(vm) : ''
      ));
    }
  };

  formatComponentName = function (vm, includeFile) {
    if (vm.$root === vm) {
      return '<Root>'
    }
    var options = typeof vm === 'function' && vm.cid != null
      ? vm.options
      : vm._isVue
        ? vm.$options || vm.constructor.options
        : vm;
    var name = options.name || options._componentTag;
    var file = options.__file;
    if (!name && file) {
      var match = file.match(/([^/\\]+)\.vue$/);
      name = match && match[1];
    }

    return (
      (name ? ("<" + (classify(name)) + ">") : "<Anonymous>") +
      (file && includeFile !== false ? (" at " + file) : '')
    )
  };

  var repeat = function (str, n) {
    var res = '';
    while (n) {
      if (n % 2 === 1) { res += str; }
      if (n > 1) { str += str; }
      n >>= 1;
    }
    return res
  };

  generateComponentTrace = function (vm) {
    if (vm._isVue && vm.$parent) {
      var tree = [];
      var currentRecursiveSequence = 0;
      while (vm) {
        if (tree.length > 0) {
          var last = tree[tree.length - 1];
          if (last.constructor === vm.constructor) {
            currentRecursiveSequence++;
            vm = vm.$parent;
            continue
          } else if (currentRecursiveSequence > 0) {
            tree[tree.length - 1] = [last, currentRecursiveSequence];
            currentRecursiveSequence = 0;
          }
        }
        tree.push(vm);
        vm = vm.$parent;
      }
      return '\n\nfound in\n\n' + tree
        .map(function (vm, i) { return ("" + (i === 0 ? '---> ' : repeat(' ', 5 + i * 2)) + (Array.isArray(vm)
            ? ((formatComponentName(vm[0])) + "... (" + (vm[1]) + " recursive calls)")
            : formatComponentName(vm))); })
        .join('\n')
    } else {
      return ("\n\n(found in " + (formatComponentName(vm)) + ")")
    }
  };
}

/*  */

var uid = 0;

/**
 * A dep is an observable that can have multiple
 * directives subscribing to it.
 */
var Dep = function Dep () {
  this.id = uid++;
  this.subs = [];
};

Dep.prototype.addSub = function addSub (sub) {
  this.subs.push(sub);
};

Dep.prototype.removeSub = function removeSub (sub) {
  remove(this.subs, sub);
};

Dep.prototype.depend = function depend () {
  if (Dep.target) {
    Dep.target.addDep(this);
  }
};

Dep.prototype.notify = function notify () {
  // stabilize the subscriber list first
  var subs = this.subs.slice();
  if (!config.async) {
    // subs aren't sorted in scheduler if not running async
    // we need to sort them now to make sure they fire in correct
    // order
    subs.sort(function (a, b) { return a.id - b.id; });
  }
  for (var i = 0, l = subs.length; i < l; i++) {
    subs[i].update();
  }
};

// The current target watcher being evaluated.
// This is globally unique because only one watcher
// can be evaluated at a time.
Dep.target = null;
var targetStack = [];

function pushTarget (target) {
  targetStack.push(target);
  Dep.target = target;
}

function popTarget () {
  targetStack.pop();
  Dep.target = targetStack[targetStack.length - 1];
}

/*  */

var VNode = function VNode (
  tag,
  data,
  children,
  text,
  elm,
  context,
  componentOptions,
  asyncFactory
) {
  this.tag = tag;
  this.data = data;
  this.children = children;
  this.text = text;
  this.elm = elm;
  this.ns = undefined;
  this.context = context;
  this.fnContext = undefined;
  this.fnOptions = undefined;
  this.fnScopeId = undefined;
  this.key = data && data.key;
  this.componentOptions = componentOptions;
  this.componentInstance = undefined;
  this.parent = undefined;
  this.raw = false;
  this.isStatic = false;
  this.isRootInsert = true;
  this.isComment = false;
  this.isCloned = false;
  this.isOnce = false;
  this.asyncFactory = asyncFactory;
  this.asyncMeta = undefined;
  this.isAsyncPlaceholder = false;
};

var prototypeAccessors = { child: { configurable: true } };

// DEPRECATED: alias for componentInstance for backwards compat.
/* istanbul ignore next */
prototypeAccessors.child.get = function () {
  return this.componentInstance
};

Object.defineProperties( VNode.prototype, prototypeAccessors );

var createEmptyVNode = function (text) {
  if ( text === void 0 ) text = '';

  var node = new VNode();
  node.text = text;
  node.isComment = true;
  return node
};

function createTextVNode (val) {
  return new VNode(undefined, undefined, undefined, String(val))
}

// optimized shallow clone
// used for static nodes and slot nodes because they may be reused across
// multiple renders, cloning them avoids errors when DOM manipulations rely
// on their elm reference.
function cloneVNode (vnode) {
  var cloned = new VNode(
    vnode.tag,
    vnode.data,
    // #7975
    // clone children array to avoid mutating original in case of cloning
    // a child.
    vnode.children && vnode.children.slice(),
    vnode.text,
    vnode.elm,
    vnode.context,
    vnode.componentOptions,
    vnode.asyncFactory
  );
  cloned.ns = vnode.ns;
  cloned.isStatic = vnode.isStatic;
  cloned.key = vnode.key;
  cloned.isComment = vnode.isComment;
  cloned.fnContext = vnode.fnContext;
  cloned.fnOptions = vnode.fnOptions;
  cloned.fnScopeId = vnode.fnScopeId;
  cloned.asyncMeta = vnode.asyncMeta;
  cloned.isCloned = true;
  return cloned
}

/*
 * not type checking this file because flow doesn't play well with
 * dynamically accessing methods on Array prototype
 */

var arrayProto = Array.prototype;
var arrayMethods = Object.create(arrayProto);

var methodsToPatch = [
  'push',
  'pop',
  'shift',
  'unshift',
  'splice',
  'sort',
  'reverse'
];

/**
 * Intercept mutating methods and emit events
 */
methodsToPatch.forEach(function (method) {
  // cache original method
  var original = arrayProto[method];
  def(arrayMethods, method, function mutator () {
    var args = [], len = arguments.length;
    while ( len-- ) args[ len ] = arguments[ len ];

    var result = original.apply(this, args);
    var ob = this.__ob__;
    var inserted;
    switch (method) {
      case 'push':
      case 'unshift':
        inserted = args;
        break
      case 'splice':
        inserted = args.slice(2);
        break
    }
    if (inserted) { ob.observeArray(inserted); }
    // notify change
    ob.dep.notify();
    return result
  });
});

/*  */

var arrayKeys = Object.getOwnPropertyNames(arrayMethods);

/**
 * In some cases we may want to disable observation inside a component's
 * update computation.
 */
var shouldObserve = true;

function toggleObserving (value) {
  shouldObserve = value;
}

/**
 * Observer class that is attached to each observed
 * object. Once attached, the observer converts the target
 * object's property keys into getter/setters that
 * collect dependencies and dispatch updates.
 */
var Observer = function Observer (value) {
  this.value = value;
  this.dep = new Dep();
  this.vmCount = 0;
  def(value, '__ob__', this);
  if (Array.isArray(value)) {
    if (hasProto) {
      protoAugment(value, arrayMethods);
    } else {
      copyAugment(value, arrayMethods, arrayKeys);
    }
    this.observeArray(value);
  } else {
    this.walk(value);
  }
};

/**
 * Walk through all properties and convert them into
 * getter/setters. This method should only be called when
 * value type is Object.
 */
Observer.prototype.walk = function walk (obj) {
  var keys = Object.keys(obj);
  for (var i = 0; i < keys.length; i++) {
    defineReactive$$1(obj, keys[i]);
  }
};

/**
 * Observe a list of Array items.
 */
Observer.prototype.observeArray = function observeArray (items) {
  for (var i = 0, l = items.length; i < l; i++) {
    observe(items[i]);
  }
};

// helpers

/**
 * Augment a target Object or Array by intercepting
 * the prototype chain using __proto__
 */
function protoAugment (target, src) {
  /* eslint-disable no-proto */
  target.__proto__ = src;
  /* eslint-enable no-proto */
}

/**
 * Augment a target Object or Array by defining
 * hidden properties.
 */
/* istanbul ignore next */
function copyAugment (target, src, keys) {
  for (var i = 0, l = keys.length; i < l; i++) {
    var key = keys[i];
    def(target, key, src[key]);
  }
}

/**
 * Attempt to create an observer instance for a value,
 * returns the new observer if successfully observed,
 * or the existing observer if the value already has one.
 */
function observe (value, asRootData) {
  if (!isObject(value) || value instanceof VNode) {
    return
  }
  var ob;
  if (hasOwn(value, '__ob__') && value.__ob__ instanceof Observer) {
    ob = value.__ob__;
  } else if (
    shouldObserve &&
    !isServerRendering() &&
    (Array.isArray(value) || isPlainObject(value)) &&
    Object.isExtensible(value) &&
    !value._isVue
  ) {
    ob = new Observer(value);
  }
  if (asRootData && ob) {
    ob.vmCount++;
  }
  return ob
}

/**
 * Define a reactive property on an Object.
 */
function defineReactive$$1 (
  obj,
  key,
  val,
  customSetter,
  shallow
) {
  var dep = new Dep();

  var property = Object.getOwnPropertyDescriptor(obj, key);
  if (property && property.configurable === false) {
    return
  }

  // cater for pre-defined getter/setters
  var getter = property && property.get;
  var setter = property && property.set;
  if ((!getter || setter) && arguments.length === 2) {
    val = obj[key];
  }

  var childOb = !shallow && observe(val);
  Object.defineProperty(obj, key, {
    enumerable: true,
    configurable: true,
    get: function reactiveGetter () {
      var value = getter ? getter.call(obj) : val;
      if (Dep.target) {
        dep.depend();
        if (childOb) {
          childOb.dep.depend();
          if (Array.isArray(value)) {
            dependArray(value);
          }
        }
      }
      return value
    },
    set: function reactiveSetter (newVal) {
      var value = getter ? getter.call(obj) : val;
      /* eslint-disable no-self-compare */
      if (newVal === value || (newVal !== newVal && value !== value)) {
        return
      }
      /* eslint-enable no-self-compare */
      if (customSetter) {
        customSetter();
      }
      // #7981: for accessor properties without setter
      if (getter && !setter) { return }
      if (setter) {
        setter.call(obj, newVal);
      } else {
        val = newVal;
      }
      childOb = !shallow && observe(newVal);
      dep.notify();
    }
  });
}

/**
 * Set a property on an object. Adds the new property and
 * triggers change notification if the property doesn't
 * already exist.
 */
function set (target, key, val) {
  if (isUndef(target) || isPrimitive(target)
  ) {
    warn(("Cannot set reactive property on undefined, null, or primitive value: " + ((target))));
  }
  if (Array.isArray(target) && isValidArrayIndex(key)) {
    target.length = Math.max(target.length, key);
    target.splice(key, 1, val);
    return val
  }
  if (key in target && !(key in Object.prototype)) {
    target[key] = val;
    return val
  }
  var ob = (target).__ob__;
  if (target._isVue || (ob && ob.vmCount)) {
    warn(
      'Avoid adding reactive properties to a Vue instance or its root $data ' +
      'at runtime - declare it upfront in the data option.'
    );
    return val
  }
  if (!ob) {
    target[key] = val;
    return val
  }
  defineReactive$$1(ob.value, key, val);
  ob.dep.notify();
  return val
}

/**
 * Delete a property and trigger change if necessary.
 */
function del (target, key) {
  if (isUndef(target) || isPrimitive(target)
  ) {
    warn(("Cannot delete reactive property on undefined, null, or primitive value: " + ((target))));
  }
  if (Array.isArray(target) && isValidArrayIndex(key)) {
    target.splice(key, 1);
    return
  }
  var ob = (target).__ob__;
  if (target._isVue || (ob && ob.vmCount)) {
    warn(
      'Avoid deleting properties on a Vue instance or its root $data ' +
      '- just set it to null.'
    );
    return
  }
  if (!hasOwn(target, key)) {
    return
  }
  delete target[key];
  if (!ob) {
    return
  }
  ob.dep.notify();
}

/**
 * Collect dependencies on array elements when the array is touched, since
 * we cannot intercept array element access like property getters.
 */
function dependArray (value) {
  for (var e = (void 0), i = 0, l = value.length; i < l; i++) {
    e = value[i];
    e && e.__ob__ && e.__ob__.dep.depend();
    if (Array.isArray(e)) {
      dependArray(e);
    }
  }
}

/*  */

/**
 * Option overwriting strategies are functions that handle
 * how to merge a parent option value and a child option
 * value into the final value.
 */
var strats = config.optionMergeStrategies;

/**
 * Options with restrictions
 */
{
  strats.el = strats.propsData = function (parent, child, vm, key) {
    if (!vm) {
      warn(
        "option \"" + key + "\" can only be used during instance " +
        'creation with the `new` keyword.'
      );
    }
    return defaultStrat(parent, child)
  };
}

/**
 * Helper that recursively merges two data objects together.
 */
function mergeData (to, from) {
  if (!from) { return to }
  var key, toVal, fromVal;

  var keys = hasSymbol
    ? Reflect.ownKeys(from)
    : Object.keys(from);

  for (var i = 0; i < keys.length; i++) {
    key = keys[i];
    // in case the object is already observed...
    if (key === '__ob__') { continue }
    toVal = to[key];
    fromVal = from[key];
    if (!hasOwn(to, key)) {
      set(to, key, fromVal);
    } else if (
      toVal !== fromVal &&
      isPlainObject(toVal) &&
      isPlainObject(fromVal)
    ) {
      mergeData(toVal, fromVal);
    }
  }
  return to
}

/**
 * Data
 */
function mergeDataOrFn (
  parentVal,
  childVal,
  vm
) {
  if (!vm) {
    // in a Vue.extend merge, both should be functions
    if (!childVal) {
      return parentVal
    }
    if (!parentVal) {
      return childVal
    }
    // when parentVal & childVal are both present,
    // we need to return a function that returns the
    // merged result of both functions... no need to
    // check if parentVal is a function here because
    // it has to be a function to pass previous merges.
    return function mergedDataFn () {
      return mergeData(
        typeof childVal === 'function' ? childVal.call(this, this) : childVal,
        typeof parentVal === 'function' ? parentVal.call(this, this) : parentVal
      )
    }
  } else {
    return function mergedInstanceDataFn () {
      // instance merge
      var instanceData = typeof childVal === 'function'
        ? childVal.call(vm, vm)
        : childVal;
      var defaultData = typeof parentVal === 'function'
        ? parentVal.call(vm, vm)
        : parentVal;
      if (instanceData) {
        return mergeData(instanceData, defaultData)
      } else {
        return defaultData
      }
    }
  }
}

strats.data = function (
  parentVal,
  childVal,
  vm
) {
  if (!vm) {
    if (childVal && typeof childVal !== 'function') {
      warn(
        'The "data" option should be a function ' +
        'that returns a per-instance value in component ' +
        'definitions.',
        vm
      );

      return parentVal
    }
    return mergeDataOrFn(parentVal, childVal)
  }

  return mergeDataOrFn(parentVal, childVal, vm)
};

/**
 * Hooks and props are merged as arrays.
 */
function mergeHook (
  parentVal,
  childVal
) {
  var res = childVal
    ? parentVal
      ? parentVal.concat(childVal)
      : Array.isArray(childVal)
        ? childVal
        : [childVal]
    : parentVal;
  return res
    ? dedupeHooks(res)
    : res
}

function dedupeHooks (hooks) {
  var res = [];
  for (var i = 0; i < hooks.length; i++) {
    if (res.indexOf(hooks[i]) === -1) {
      res.push(hooks[i]);
    }
  }
  return res
}

LIFECYCLE_HOOKS.forEach(function (hook) {
  strats[hook] = mergeHook;
});

/**
 * Assets
 *
 * When a vm is present (instance creation), we need to do
 * a three-way merge between constructor options, instance
 * options and parent options.
 */
function mergeAssets (
  parentVal,
  childVal,
  vm,
  key
) {
  var res = Object.create(parentVal || null);
  if (childVal) {
    assertObjectType(key, childVal, vm);
    return extend(res, childVal)
  } else {
    return res
  }
}

ASSET_TYPES.forEach(function (type) {
  strats[type + 's'] = mergeAssets;
});

/**
 * Watchers.
 *
 * Watchers hashes should not overwrite one
 * another, so we merge them as arrays.
 */
strats.watch = function (
  parentVal,
  childVal,
  vm,
  key
) {
  // work around Firefox's Object.prototype.watch...
  if (parentVal === nativeWatch) { parentVal = undefined; }
  if (childVal === nativeWatch) { childVal = undefined; }
  /* istanbul ignore if */
  if (!childVal) { return Object.create(parentVal || null) }
  {
    assertObjectType(key, childVal, vm);
  }
  if (!parentVal) { return childVal }
  var ret = {};
  extend(ret, parentVal);
  for (var key$1 in childVal) {
    var parent = ret[key$1];
    var child = childVal[key$1];
    if (parent && !Array.isArray(parent)) {
      parent = [parent];
    }
    ret[key$1] = parent
      ? parent.concat(child)
      : Array.isArray(child) ? child : [child];
  }
  return ret
};

/**
 * Other object hashes.
 */
strats.props =
strats.methods =
strats.inject =
strats.computed = function (
  parentVal,
  childVal,
  vm,
  key
) {
  if (childVal && "development" !== 'production') {
    assertObjectType(key, childVal, vm);
  }
  if (!parentVal) { return childVal }
  var ret = Object.create(null);
  extend(ret, parentVal);
  if (childVal) { extend(ret, childVal); }
  return ret
};
strats.provide = mergeDataOrFn;

/**
 * Default strategy.
 */
var defaultStrat = function (parentVal, childVal) {
  return childVal === undefined
    ? parentVal
    : childVal
};

/**
 * Validate component names
 */
function checkComponents (options) {
  for (var key in options.components) {
    validateComponentName(key);
  }
}

function validateComponentName (name) {
  if (!new RegExp(("^[a-zA-Z][\\-\\.0-9_" + (unicodeRegExp.source) + "]*$")).test(name)) {
    warn(
      'Invalid component name: "' + name + '". Component names ' +
      'should conform to valid custom element name in html5 specification.'
    );
  }
  if (isBuiltInTag(name) || config.isReservedTag(name)) {
    warn(
      'Do not use built-in or reserved HTML elements as component ' +
      'id: ' + name
    );
  }
}

/**
 * Ensure all props option syntax are normalized into the
 * Object-based format.
 */
function normalizeProps (options, vm) {
  var props = options.props;
  if (!props) { return }
  var res = {};
  var i, val, name;
  if (Array.isArray(props)) {
    i = props.length;
    while (i--) {
      val = props[i];
      if (typeof val === 'string') {
        name = camelize(val);
        res[name] = { type: null };
      } else {
        warn('props must be strings when using array syntax.');
      }
    }
  } else if (isPlainObject(props)) {
    for (var key in props) {
      val = props[key];
      name = camelize(key);
      res[name] = isPlainObject(val)
        ? val
        : { type: val };
    }
  } else {
    warn(
      "Invalid value for option \"props\": expected an Array or an Object, " +
      "but got " + (toRawType(props)) + ".",
      vm
    );
  }
  options.props = res;
}

/**
 * Normalize all injections into Object-based format
 */
function normalizeInject (options, vm) {
  var inject = options.inject;
  if (!inject) { return }
  var normalized = options.inject = {};
  if (Array.isArray(inject)) {
    for (var i = 0; i < inject.length; i++) {
      normalized[inject[i]] = { from: inject[i] };
    }
  } else if (isPlainObject(inject)) {
    for (var key in inject) {
      var val = inject[key];
      normalized[key] = isPlainObject(val)
        ? extend({ from: key }, val)
        : { from: val };
    }
  } else {
    warn(
      "Invalid value for option \"inject\": expected an Array or an Object, " +
      "but got " + (toRawType(inject)) + ".",
      vm
    );
  }
}

/**
 * Normalize raw function directives into object format.
 */
function normalizeDirectives (options) {
  var dirs = options.directives;
  if (dirs) {
    for (var key in dirs) {
      var def$$1 = dirs[key];
      if (typeof def$$1 === 'function') {
        dirs[key] = { bind: def$$1, update: def$$1 };
      }
    }
  }
}

function assertObjectType (name, value, vm) {
  if (!isPlainObject(value)) {
    warn(
      "Invalid value for option \"" + name + "\": expected an Object, " +
      "but got " + (toRawType(value)) + ".",
      vm
    );
  }
}

/**
 * Merge two option objects into a new one.
 * Core utility used in both instantiation and inheritance.
 */
function mergeOptions (
  parent,
  child,
  vm
) {
  {
    checkComponents(child);
  }

  if (typeof child === 'function') {
    child = child.options;
  }

  normalizeProps(child, vm);
  normalizeInject(child, vm);
  normalizeDirectives(child);

  // Apply extends and mixins on the child options,
  // but only if it is a raw options object that isn't
  // the result of another mergeOptions call.
  // Only merged options has the _base property.
  if (!child._base) {
    if (child.extends) {
      parent = mergeOptions(parent, child.extends, vm);
    }
    if (child.mixins) {
      for (var i = 0, l = child.mixins.length; i < l; i++) {
        parent = mergeOptions(parent, child.mixins[i], vm);
      }
    }
  }

  var options = {};
  var key;
  for (key in parent) {
    mergeField(key);
  }
  for (key in child) {
    if (!hasOwn(parent, key)) {
      mergeField(key);
    }
  }
  function mergeField (key) {
    var strat = strats[key] || defaultStrat;
    options[key] = strat(parent[key], child[key], vm, key);
  }
  return options
}

/**
 * Resolve an asset.
 * This function is used because child instances need access
 * to assets defined in its ancestor chain.
 */
function resolveAsset (
  options,
  type,
  id,
  warnMissing
) {
  /* istanbul ignore if */
  if (typeof id !== 'string') {
    return
  }
  var assets = options[type];
  // check local registration variations first
  if (hasOwn(assets, id)) { return assets[id] }
  var camelizedId = camelize(id);
  if (hasOwn(assets, camelizedId)) { return assets[camelizedId] }
  var PascalCaseId = capitalize(camelizedId);
  if (hasOwn(assets, PascalCaseId)) { return assets[PascalCaseId] }
  // fallback to prototype chain
  var res = assets[id] || assets[camelizedId] || assets[PascalCaseId];
  if (warnMissing && !res) {
    warn(
      'Failed to resolve ' + type.slice(0, -1) + ': ' + id,
      options
    );
  }
  return res
}

/*  */



function validateProp (
  key,
  propOptions,
  propsData,
  vm
) {
  var prop = propOptions[key];
  var absent = !hasOwn(propsData, key);
  var value = propsData[key];
  // boolean casting
  var booleanIndex = getTypeIndex(Boolean, prop.type);
  if (booleanIndex > -1) {
    if (absent && !hasOwn(prop, 'default')) {
      value = false;
    } else if (value === '' || value === hyphenate(key)) {
      // only cast empty string / same name to boolean if
      // boolean has higher priority
      var stringIndex = getTypeIndex(String, prop.type);
      if (stringIndex < 0 || booleanIndex < stringIndex) {
        value = true;
      }
    }
  }
  // check default value
  if (value === undefined) {
    value = getPropDefaultValue(vm, prop, key);
    // since the default value is a fresh copy,
    // make sure to observe it.
    var prevShouldObserve = shouldObserve;
    toggleObserving(true);
    observe(value);
    toggleObserving(prevShouldObserve);
  }
  {
    assertProp(prop, key, value, vm, absent);
  }
  return value
}

/**
 * Get the default value of a prop.
 */
function getPropDefaultValue (vm, prop, key) {
  // no default, return undefined
  if (!hasOwn(prop, 'default')) {
    return undefined
  }
  var def = prop.default;
  // warn against non-factory defaults for Object & Array
  if (isObject(def)) {
    warn(
      'Invalid default value for prop "' + key + '": ' +
      'Props with type Object/Array must use a factory function ' +
      'to return the default value.',
      vm
    );
  }
  // the raw prop value was also undefined from previous render,
  // return previous default value to avoid unnecessary watcher trigger
  if (vm && vm.$options.propsData &&
    vm.$options.propsData[key] === undefined &&
    vm._props[key] !== undefined
  ) {
    return vm._props[key]
  }
  // call factory function for non-Function types
  // a value is Function if its prototype is function even across different execution context
  return typeof def === 'function' && getType(prop.type) !== 'Function'
    ? def.call(vm)
    : def
}

/**
 * Assert whether a prop is valid.
 */
function assertProp (
  prop,
  name,
  value,
  vm,
  absent
) {
  if (prop.required && absent) {
    warn(
      'Missing required prop: "' + name + '"',
      vm
    );
    return
  }
  if (value == null && !prop.required) {
    return
  }
  var type = prop.type;
  var valid = !type || type === true;
  var expectedTypes = [];
  if (type) {
    if (!Array.isArray(type)) {
      type = [type];
    }
    for (var i = 0; i < type.length && !valid; i++) {
      var assertedType = assertType(value, type[i]);
      expectedTypes.push(assertedType.expectedType || '');
      valid = assertedType.valid;
    }
  }

  if (!valid) {
    warn(
      getInvalidTypeMessage(name, value, expectedTypes),
      vm
    );
    return
  }
  var validator = prop.validator;
  if (validator) {
    if (!validator(value)) {
      warn(
        'Invalid prop: custom validator check failed for prop "' + name + '".',
        vm
      );
    }
  }
}

var simpleCheckRE = /^(String|Number|Boolean|Function|Symbol)$/;

function assertType (value, type) {
  var valid;
  var expectedType = getType(type);
  if (simpleCheckRE.test(expectedType)) {
    var t = typeof value;
    valid = t === expectedType.toLowerCase();
    // for primitive wrapper objects
    if (!valid && t === 'object') {
      valid = value instanceof type;
    }
  } else if (expectedType === 'Object') {
    valid = isPlainObject(value);
  } else if (expectedType === 'Array') {
    valid = Array.isArray(value);
  } else {
    valid = value instanceof type;
  }
  return {
    valid: valid,
    expectedType: expectedType
  }
}

/**
 * Use function string name to check built-in types,
 * because a simple equality check will fail when running
 * across different vms / iframes.
 */
function getType (fn) {
  var match = fn && fn.toString().match(/^\s*function (\w+)/);
  return match ? match[1] : ''
}

function isSameType (a, b) {
  return getType(a) === getType(b)
}

function getTypeIndex (type, expectedTypes) {
  if (!Array.isArray(expectedTypes)) {
    return isSameType(expectedTypes, type) ? 0 : -1
  }
  for (var i = 0, len = expectedTypes.length; i < len; i++) {
    if (isSameType(expectedTypes[i], type)) {
      return i
    }
  }
  return -1
}

function getInvalidTypeMessage (name, value, expectedTypes) {
  var message = "Invalid prop: type check failed for prop \"" + name + "\"." +
    " Expected " + (expectedTypes.map(capitalize).join(', '));
  var expectedType = expectedTypes[0];
  var receivedType = toRawType(value);
  var expectedValue = styleValue(value, expectedType);
  var receivedValue = styleValue(value, receivedType);
  // check if we need to specify expected value
  if (expectedTypes.length === 1 &&
      isExplicable(expectedType) &&
      !isBoolean(expectedType, receivedType)) {
    message += " with value " + expectedValue;
  }
  message += ", got " + receivedType + " ";
  // check if we need to specify received value
  if (isExplicable(receivedType)) {
    message += "with value " + receivedValue + ".";
  }
  return message
}

function styleValue (value, type) {
  if (type === 'String') {
    return ("\"" + value + "\"")
  } else if (type === 'Number') {
    return ("" + (Number(value)))
  } else {
    return ("" + value)
  }
}

function isExplicable (value) {
  var explicitTypes = ['string', 'number', 'boolean'];
  return explicitTypes.some(function (elem) { return value.toLowerCase() === elem; })
}

function isBoolean () {
  var args = [], len = arguments.length;
  while ( len-- ) args[ len ] = arguments[ len ];

  return args.some(function (elem) { return elem.toLowerCase() === 'boolean'; })
}

/*  */

function handleError (err, vm, info) {
  // Deactivate deps tracking while processing error handler to avoid possible infinite rendering.
  // See: https://github.com/vuejs/vuex/issues/1505
  pushTarget();
  try {
    if (vm) {
      var cur = vm;
      while ((cur = cur.$parent)) {
        var hooks = cur.$options.errorCaptured;
        if (hooks) {
          for (var i = 0; i < hooks.length; i++) {
            try {
              var capture = hooks[i].call(cur, err, vm, info) === false;
              if (capture) { return }
            } catch (e) {
              globalHandleError(e, cur, 'errorCaptured hook');
            }
          }
        }
      }
    }
    globalHandleError(err, vm, info);
  } finally {
    popTarget();
  }
}

function invokeWithErrorHandling (
  handler,
  context,
  args,
  vm,
  info
) {
  var res;
  try {
    res = args ? handler.apply(context, args) : handler.call(context);
    if (res && !res._isVue && isPromise(res) && !res._handled) {
      res.catch(function (e) { return handleError(e, vm, info + " (Promise/async)"); });
      // issue #9511
      // avoid catch triggering multiple times when nested calls
      res._handled = true;
    }
  } catch (e) {
    handleError(e, vm, info);
  }
  return res
}

function globalHandleError (err, vm, info) {
  if (config.errorHandler) {
    try {
      return config.errorHandler.call(null, err, vm, info)
    } catch (e) {
      // if the user intentionally throws the original error in the handler,
      // do not log it twice
      if (e !== err) {
        logError(e, null, 'config.errorHandler');
      }
    }
  }
  logError(err, vm, info);
}

function logError (err, vm, info) {
  {
    warn(("Error in " + info + ": \"" + (err.toString()) + "\""), vm);
  }
  /* istanbul ignore else */
  if ((inBrowser || inWeex) && typeof console !== 'undefined') {
    console.error(err);
  } else {
    throw err
  }
}

/*  */

var isUsingMicroTask = false;

var callbacks = [];
var pending = false;

function flushCallbacks () {
  pending = false;
  var copies = callbacks.slice(0);
  callbacks.length = 0;
  for (var i = 0; i < copies.length; i++) {
    copies[i]();
  }
}

// Here we have async deferring wrappers using microtasks.
// In 2.5 we used (macro) tasks (in combination with microtasks).
// However, it has subtle problems when state is changed right before repaint
// (e.g. #6813, out-in transitions).
// Also, using (macro) tasks in event handler would cause some weird behaviors
// that cannot be circumvented (e.g. #7109, #7153, #7546, #7834, #8109).
// So we now use microtasks everywhere, again.
// A major drawback of this tradeoff is that there are some scenarios
// where microtasks have too high a priority and fire in between supposedly
// sequential events (e.g. #4521, #6690, which have workarounds)
// or even between bubbling of the same event (#6566).
var timerFunc;

// The nextTick behavior leverages the microtask queue, which can be accessed
// via either native Promise.then or MutationObserver.
// MutationObserver has wider support, however it is seriously bugged in
// UIWebView in iOS >= 9.3.3 when triggered in touch event handlers. It
// completely stops working after triggering a few times... so, if native
// Promise is available, we will use it:
/* istanbul ignore next, $flow-disable-line */
if (typeof Promise !== 'undefined' && isNative(Promise)) {
  var p = Promise.resolve();
  timerFunc = function () {
    p.then(flushCallbacks);
    // In problematic UIWebViews, Promise.then doesn't completely break, but
    // it can get stuck in a weird state where callbacks are pushed into the
    // microtask queue but the queue isn't being flushed, until the browser
    // needs to do some other work, e.g. handle a timer. Therefore we can
    // "force" the microtask queue to be flushed by adding an empty timer.
    if (isIOS) { setTimeout(noop); }
  };
  isUsingMicroTask = true;
} else if (!isIE && typeof MutationObserver !== 'undefined' && (
  isNative(MutationObserver) ||
  // PhantomJS and iOS 7.x
  MutationObserver.toString() === '[object MutationObserverConstructor]'
)) {
  // Use MutationObserver where native Promise is not available,
  // e.g. PhantomJS, iOS7, Android 4.4
  // (#6466 MutationObserver is unreliable in IE11)
  var counter = 1;
  var observer = new MutationObserver(flushCallbacks);
  var textNode = document.createTextNode(String(counter));
  observer.observe(textNode, {
    characterData: true
  });
  timerFunc = function () {
    counter = (counter + 1) % 2;
    textNode.data = String(counter);
  };
  isUsingMicroTask = true;
} else if (typeof setImmediate !== 'undefined' && isNative(setImmediate)) {
  // Fallback to setImmediate.
  // Techinically it leverages the (macro) task queue,
  // but it is still a better choice than setTimeout.
  timerFunc = function () {
    setImmediate(flushCallbacks);
  };
} else {
  // Fallback to setTimeout.
  timerFunc = function () {
    setTimeout(flushCallbacks, 0);
  };
}

function nextTick (cb, ctx) {
  var _resolve;
  callbacks.push(function () {
    if (cb) {
      try {
        cb.call(ctx);
      } catch (e) {
        handleError(e, ctx, 'nextTick');
      }
    } else if (_resolve) {
      _resolve(ctx);
    }
  });
  if (!pending) {
    pending = true;
    timerFunc();
  }
  // $flow-disable-line
  if (!cb && typeof Promise !== 'undefined') {
    return new Promise(function (resolve) {
      _resolve = resolve;
    })
  }
}

/*  */

var mark;
var measure;

{
  var perf = inBrowser && window.performance;
  /* istanbul ignore if */
  if (
    perf &&
    perf.mark &&
    perf.measure &&
    perf.clearMarks &&
    perf.clearMeasures
  ) {
    mark = function (tag) { return perf.mark(tag); };
    measure = function (name, startTag, endTag) {
      perf.measure(name, startTag, endTag);
      perf.clearMarks(startTag);
      perf.clearMarks(endTag);
      // perf.clearMeasures(name)
    };
  }
}

/* not type checking this file because flow doesn't play well with Proxy */

var initProxy;

{
  var allowedGlobals = makeMap(
    'Infinity,undefined,NaN,isFinite,isNaN,' +
    'parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,' +
    'Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,' +
    'require' // for Webpack/Browserify
  );

  var warnNonPresent = function (target, key) {
    warn(
      "Property or method \"" + key + "\" is not defined on the instance but " +
      'referenced during render. Make sure that this property is reactive, ' +
      'either in the data option, or for class-based components, by ' +
      'initializing the property. ' +
      'See: https://vuejs.org/v2/guide/reactivity.html#Declaring-Reactive-Properties.',
      target
    );
  };

  var warnReservedPrefix = function (target, key) {
    warn(
      "Property \"" + key + "\" must be accessed with \"$data." + key + "\" because " +
      'properties starting with "$" or "_" are not proxied in the Vue instance to ' +
      'prevent conflicts with Vue internals' +
      'See: https://vuejs.org/v2/api/#data',
      target
    );
  };

  var hasProxy =
    typeof Proxy !== 'undefined' && isNative(Proxy);

  if (hasProxy) {
    var isBuiltInModifier = makeMap('stop,prevent,self,ctrl,shift,alt,meta,exact');
    config.keyCodes = new Proxy(config.keyCodes, {
      set: function set (target, key, value) {
        if (isBuiltInModifier(key)) {
          warn(("Avoid overwriting built-in modifier in config.keyCodes: ." + key));
          return false
        } else {
          target[key] = value;
          return true
        }
      }
    });
  }

  var hasHandler = {
    has: function has (target, key) {
      var has = key in target;
      var isAllowed = allowedGlobals(key) ||
        (typeof key === 'string' && key.charAt(0) === '_' && !(key in target.$data));
      if (!has && !isAllowed) {
        if (key in target.$data) { warnReservedPrefix(target, key); }
        else { warnNonPresent(target, key); }
      }
      return has || !isAllowed
    }
  };

  var getHandler = {
    get: function get (target, key) {
      if (typeof key === 'string' && !(key in target)) {
        if (key in target.$data) { warnReservedPrefix(target, key); }
        else { warnNonPresent(target, key); }
      }
      return target[key]
    }
  };

  initProxy = function initProxy (vm) {
    if (hasProxy) {
      // determine which proxy handler to use
      var options = vm.$options;
      var handlers = options.render && options.render._withStripped
        ? getHandler
        : hasHandler;
      vm._renderProxy = new Proxy(vm, handlers);
    } else {
      vm._renderProxy = vm;
    }
  };
}

/*  */

var seenObjects = new _Set();

/**
 * Recursively traverse an object to evoke all converted
 * getters, so that every nested property inside the object
 * is collected as a "deep" dependency.
 */
function traverse (val) {
  _traverse(val, seenObjects);
  seenObjects.clear();
}

function _traverse (val, seen) {
  var i, keys;
  var isA = Array.isArray(val);
  if ((!isA && !isObject(val)) || Object.isFrozen(val) || val instanceof VNode) {
    return
  }
  if (val.__ob__) {
    var depId = val.__ob__.dep.id;
    if (seen.has(depId)) {
      return
    }
    seen.add(depId);
  }
  if (isA) {
    i = val.length;
    while (i--) { _traverse(val[i], seen); }
  } else {
    keys = Object.keys(val);
    i = keys.length;
    while (i--) { _traverse(val[keys[i]], seen); }
  }
}

/*  */

var normalizeEvent = cached(function (name) {
  var passive = name.charAt(0) === '&';
  name = passive ? name.slice(1) : name;
  var once$$1 = name.charAt(0) === '~'; // Prefixed last, checked first
  name = once$$1 ? name.slice(1) : name;
  var capture = name.charAt(0) === '!';
  name = capture ? name.slice(1) : name;
  return {
    name: name,
    once: once$$1,
    capture: capture,
    passive: passive
  }
});

function createFnInvoker (fns, vm) {
  function invoker () {
    var arguments$1 = arguments;

    var fns = invoker.fns;
    if (Array.isArray(fns)) {
      var cloned = fns.slice();
      for (var i = 0; i < cloned.length; i++) {
        invokeWithErrorHandling(cloned[i], null, arguments$1, vm, "v-on handler");
      }
    } else {
      // return handler return value for single handlers
      return invokeWithErrorHandling(fns, null, arguments, vm, "v-on handler")
    }
  }
  invoker.fns = fns;
  return invoker
}

function updateListeners (
  on,
  oldOn,
  add,
  remove$$1,
  createOnceHandler,
  vm
) {
  var name, def$$1, cur, old, event;
  for (name in on) {
    def$$1 = cur = on[name];
    old = oldOn[name];
    event = normalizeEvent(name);
    if (isUndef(cur)) {
      warn(
        "Invalid handler for event \"" + (event.name) + "\": got " + String(cur),
        vm
      );
    } else if (isUndef(old)) {
      if (isUndef(cur.fns)) {
        cur = on[name] = createFnInvoker(cur, vm);
      }
      if (isTrue(event.once)) {
        cur = on[name] = createOnceHandler(event.name, cur, event.capture);
      }
      add(event.name, cur, event.capture, event.passive, event.params);
    } else if (cur !== old) {
      old.fns = cur;
      on[name] = old;
    }
  }
  for (name in oldOn) {
    if (isUndef(on[name])) {
      event = normalizeEvent(name);
      remove$$1(event.name, oldOn[name], event.capture);
    }
  }
}

/*  */

function mergeVNodeHook (def, hookKey, hook) {
  if (def instanceof VNode) {
    def = def.data.hook || (def.data.hook = {});
  }
  var invoker;
  var oldHook = def[hookKey];

  function wrappedHook () {
    hook.apply(this, arguments);
    // important: remove merged hook to ensure it's called only once
    // and prevent memory leak
    remove(invoker.fns, wrappedHook);
  }

  if (isUndef(oldHook)) {
    // no existing hook
    invoker = createFnInvoker([wrappedHook]);
  } else {
    /* istanbul ignore if */
    if (isDef(oldHook.fns) && isTrue(oldHook.merged)) {
      // already a merged invoker
      invoker = oldHook;
      invoker.fns.push(wrappedHook);
    } else {
      // existing plain hook
      invoker = createFnInvoker([oldHook, wrappedHook]);
    }
  }

  invoker.merged = true;
  def[hookKey] = invoker;
}

/*  */

function extractPropsFromVNodeData (
  data,
  Ctor,
  tag
) {
  // we are only extracting raw values here.
  // validation and default values are handled in the child
  // component itself.
  var propOptions = Ctor.options.props;
  if (isUndef(propOptions)) {
    return
  }
  var res = {};
  var attrs = data.attrs;
  var props = data.props;
  if (isDef(attrs) || isDef(props)) {
    for (var key in propOptions) {
      var altKey = hyphenate(key);
      {
        var keyInLowerCase = key.toLowerCase();
        if (
          key !== keyInLowerCase &&
          attrs && hasOwn(attrs, keyInLowerCase)
        ) {
          tip(
            "Prop \"" + keyInLowerCase + "\" is passed to component " +
            (formatComponentName(tag || Ctor)) + ", but the declared prop name is" +
            " \"" + key + "\". " +
            "Note that HTML attributes are case-insensitive and camelCased " +
            "props need to use their kebab-case equivalents when using in-DOM " +
            "templates. You should probably use \"" + altKey + "\" instead of \"" + key + "\"."
          );
        }
      }
      checkProp(res, props, key, altKey, true) ||
      checkProp(res, attrs, key, altKey, false);
    }
  }
  return res
}

function checkProp (
  res,
  hash,
  key,
  altKey,
  preserve
) {
  if (isDef(hash)) {
    if (hasOwn(hash, key)) {
      res[key] = hash[key];
      if (!preserve) {
        delete hash[key];
      }
      return true
    } else if (hasOwn(hash, altKey)) {
      res[key] = hash[altKey];
      if (!preserve) {
        delete hash[altKey];
      }
      return true
    }
  }
  return false
}

/*  */

// The template compiler attempts to minimize the need for normalization by
// statically analyzing the template at compile time.
//
// For plain HTML markup, normalization can be completely skipped because the
// generated render function is guaranteed to return Array<VNode>. There are
// two cases where extra normalization is needed:

// 1. When the children contains components - because a functional component
// may return an Array instead of a single root. In this case, just a simple
// normalization is needed - if any child is an Array, we flatten the whole
// thing with Array.prototype.concat. It is guaranteed to be only 1-level deep
// because functional components already normalize their own children.
function simpleNormalizeChildren (children) {
  for (var i = 0; i < children.length; i++) {
    if (Array.isArray(children[i])) {
      return Array.prototype.concat.apply([], children)
    }
  }
  return children
}

// 2. When the children contains constructs that always generated nested Arrays,
// e.g. <template>, <slot>, v-for, or when the children is provided by user
// with hand-written render functions / JSX. In such cases a full normalization
// is needed to cater to all possible types of children values.
function normalizeChildren (children) {
  return isPrimitive(children)
    ? [createTextVNode(children)]
    : Array.isArray(children)
      ? normalizeArrayChildren(children)
      : undefined
}

function isTextNode (node) {
  return isDef(node) && isDef(node.text) && isFalse(node.isComment)
}

function normalizeArrayChildren (children, nestedIndex) {
  var res = [];
  var i, c, lastIndex, last;
  for (i = 0; i < children.length; i++) {
    c = children[i];
    if (isUndef(c) || typeof c === 'boolean') { continue }
    lastIndex = res.length - 1;
    last = res[lastIndex];
    //  nested
    if (Array.isArray(c)) {
      if (c.length > 0) {
        c = normalizeArrayChildren(c, ((nestedIndex || '') + "_" + i));
        // merge adjacent text nodes
        if (isTextNode(c[0]) && isTextNode(last)) {
          res[lastIndex] = createTextVNode(last.text + (c[0]).text);
          c.shift();
        }
        res.push.apply(res, c);
      }
    } else if (isPrimitive(c)) {
      if (isTextNode(last)) {
        // merge adjacent text nodes
        // this is necessary for SSR hydration because text nodes are
        // essentially merged when rendered to HTML strings
        res[lastIndex] = createTextVNode(last.text + c);
      } else if (c !== '') {
        // convert primitive to vnode
        res.push(createTextVNode(c));
      }
    } else {
      if (isTextNode(c) && isTextNode(last)) {
        // merge adjacent text nodes
        res[lastIndex] = createTextVNode(last.text + c.text);
      } else {
        // default key for nested array children (likely generated by v-for)
        if (isTrue(children._isVList) &&
          isDef(c.tag) &&
          isUndef(c.key) &&
          isDef(nestedIndex)) {
          c.key = "__vlist" + nestedIndex + "_" + i + "__";
        }
        res.push(c);
      }
    }
  }
  return res
}

/*  */

function initProvide (vm) {
  var provide = vm.$options.provide;
  if (provide) {
    vm._provided = typeof provide === 'function'
      ? provide.call(vm)
      : provide;
  }
}

function initInjections (vm) {
  var result = resolveInject(vm.$options.inject, vm);
  if (result) {
    toggleObserving(false);
    Object.keys(result).forEach(function (key) {
      /* istanbul ignore else */
      {
        defineReactive$$1(vm, key, result[key], function () {
          warn(
            "Avoid mutating an injected value directly since the changes will be " +
            "overwritten whenever the provided component re-renders. " +
            "injection being mutated: \"" + key + "\"",
            vm
          );
        });
      }
    });
    toggleObserving(true);
  }
}

function resolveInject (inject, vm) {
  if (inject) {
    // inject is :any because flow is not smart enough to figure out cached
    var result = Object.create(null);
    var keys = hasSymbol
      ? Reflect.ownKeys(inject)
      : Object.keys(inject);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      // #6574 in case the inject object is observed...
      if (key === '__ob__') { continue }
      var provideKey = inject[key].from;
      var source = vm;
      while (source) {
        if (source._provided && hasOwn(source._provided, provideKey)) {
          result[key] = source._provided[provideKey];
          break
        }
        source = source.$parent;
      }
      if (!source) {
        if ('default' in inject[key]) {
          var provideDefault = inject[key].default;
          result[key] = typeof provideDefault === 'function'
            ? provideDefault.call(vm)
            : provideDefault;
        } else {
          warn(("Injection \"" + key + "\" not found"), vm);
        }
      }
    }
    return result
  }
}

/*  */



/**
 * Runtime helper for resolving raw children VNodes into a slot object.
 */
function resolveSlots (
  children,
  context
) {
  if (!children || !children.length) {
    return {}
  }
  var slots = {};
  for (var i = 0, l = children.length; i < l; i++) {
    var child = children[i];
    var data = child.data;
    // remove slot attribute if the node is resolved as a Vue slot node
    if (data && data.attrs && data.attrs.slot) {
      delete data.attrs.slot;
    }
    // named slots should only be respected if the vnode was rendered in the
    // same context.
    if ((child.context === context || child.fnContext === context) &&
      data && data.slot != null
    ) {
      var name = data.slot;
      var slot = (slots[name] || (slots[name] = []));
      if (child.tag === 'template') {
        slot.push.apply(slot, child.children || []);
      } else {
        slot.push(child);
      }
    } else {
      (slots.default || (slots.default = [])).push(child);
    }
  }
  // ignore slots that contains only whitespace
  for (var name$1 in slots) {
    if (slots[name$1].every(isWhitespace)) {
      delete slots[name$1];
    }
  }
  return slots
}

function isWhitespace (node) {
  return (node.isComment && !node.asyncFactory) || node.text === ' '
}

/*  */

function normalizeScopedSlots (
  slots,
  normalSlots,
  prevSlots
) {
  var res;
  var hasNormalSlots = Object.keys(normalSlots).length > 0;
  var isStable = slots ? !!slots.$stable : !hasNormalSlots;
  var key = slots && slots.$key;
  if (!slots) {
    res = {};
  } else if (slots._normalized) {
    // fast path 1: child component re-render only, parent did not change
    return slots._normalized
  } else if (
    isStable &&
    prevSlots &&
    prevSlots !== emptyObject &&
    key === prevSlots.$key &&
    !hasNormalSlots &&
    !prevSlots.$hasNormal
  ) {
    // fast path 2: stable scoped slots w/ no normal slots to proxy,
    // only need to normalize once
    return prevSlots
  } else {
    res = {};
    for (var key$1 in slots) {
      if (slots[key$1] && key$1[0] !== '$') {
        res[key$1] = normalizeScopedSlot(normalSlots, key$1, slots[key$1]);
      }
    }
  }
  // expose normal slots on scopedSlots
  for (var key$2 in normalSlots) {
    if (!(key$2 in res)) {
      res[key$2] = proxyNormalSlot(normalSlots, key$2);
    }
  }
  // avoriaz seems to mock a non-extensible $scopedSlots object
  // and when that is passed down this would cause an error
  if (slots && Object.isExtensible(slots)) {
    (slots)._normalized = res;
  }
  def(res, '$stable', isStable);
  def(res, '$key', key);
  def(res, '$hasNormal', hasNormalSlots);
  return res
}

function normalizeScopedSlot(normalSlots, key, fn) {
  var normalized = function () {
    var res = arguments.length ? fn.apply(null, arguments) : fn({});
    res = res && typeof res === 'object' && !Array.isArray(res)
      ? [res] // single vnode
      : normalizeChildren(res);
    return res && (
      res.length === 0 ||
      (res.length === 1 && res[0].isComment) // #9658
    ) ? undefined
      : res
  };
  // this is a slot using the new v-slot syntax without scope. although it is
  // compiled as a scoped slot, render fn users would expect it to be present
  // on this.$slots because the usage is semantically a normal slot.
  if (fn.proxy) {
    Object.defineProperty(normalSlots, key, {
      get: normalized,
      enumerable: true,
      configurable: true
    });
  }
  return normalized
}

function proxyNormalSlot(slots, key) {
  return function () { return slots[key]; }
}

/*  */

/**
 * Runtime helper for rendering v-for lists.
 */
function renderList (
  val,
  render
) {
  var ret, i, l, keys, key;
  if (Array.isArray(val) || typeof val === 'string') {
    ret = new Array(val.length);
    for (i = 0, l = val.length; i < l; i++) {
      ret[i] = render(val[i], i);
    }
  } else if (typeof val === 'number') {
    ret = new Array(val);
    for (i = 0; i < val; i++) {
      ret[i] = render(i + 1, i);
    }
  } else if (isObject(val)) {
    if (hasSymbol && val[Symbol.iterator]) {
      ret = [];
      var iterator = val[Symbol.iterator]();
      var result = iterator.next();
      while (!result.done) {
        ret.push(render(result.value, ret.length));
        result = iterator.next();
      }
    } else {
      keys = Object.keys(val);
      ret = new Array(keys.length);
      for (i = 0, l = keys.length; i < l; i++) {
        key = keys[i];
        ret[i] = render(val[key], key, i);
      }
    }
  }
  if (!isDef(ret)) {
    ret = [];
  }
  (ret)._isVList = true;
  return ret
}

/*  */

/**
 * Runtime helper for rendering <slot>
 */
function renderSlot (
  name,
  fallback,
  props,
  bindObject
) {
  var scopedSlotFn = this.$scopedSlots[name];
  var nodes;
  if (scopedSlotFn) { // scoped slot
    props = props || {};
    if (bindObject) {
      if (!isObject(bindObject)) {
        warn(
          'slot v-bind without argument expects an Object',
          this
        );
      }
      props = extend(extend({}, bindObject), props);
    }
    nodes = scopedSlotFn(props) || fallback;
  } else {
    nodes = this.$slots[name] || fallback;
  }

  var target = props && props.slot;
  if (target) {
    return this.$createElement('template', { slot: target }, nodes)
  } else {
    return nodes
  }
}

/*  */

/**
 * Runtime helper for resolving filters
 */
function resolveFilter (id) {
  return resolveAsset(this.$options, 'filters', id, true) || identity
}

/*  */

function isKeyNotMatch (expect, actual) {
  if (Array.isArray(expect)) {
    return expect.indexOf(actual) === -1
  } else {
    return expect !== actual
  }
}

/**
 * Runtime helper for checking keyCodes from config.
 * exposed as Vue.prototype._k
 * passing in eventKeyName as last argument separately for backwards compat
 */
function checkKeyCodes (
  eventKeyCode,
  key,
  builtInKeyCode,
  eventKeyName,
  builtInKeyName
) {
  var mappedKeyCode = config.keyCodes[key] || builtInKeyCode;
  if (builtInKeyName && eventKeyName && !config.keyCodes[key]) {
    return isKeyNotMatch(builtInKeyName, eventKeyName)
  } else if (mappedKeyCode) {
    return isKeyNotMatch(mappedKeyCode, eventKeyCode)
  } else if (eventKeyName) {
    return hyphenate(eventKeyName) !== key
  }
}

/*  */

/**
 * Runtime helper for merging v-bind="object" into a VNode's data.
 */
function bindObjectProps (
  data,
  tag,
  value,
  asProp,
  isSync
) {
  if (value) {
    if (!isObject(value)) {
      warn(
        'v-bind without argument expects an Object or Array value',
        this
      );
    } else {
      if (Array.isArray(value)) {
        value = toObject(value);
      }
      var hash;
      var loop = function ( key ) {
        if (
          key === 'class' ||
          key === 'style' ||
          isReservedAttribute(key)
        ) {
          hash = data;
        } else {
          var type = data.attrs && data.attrs.type;
          hash = asProp || config.mustUseProp(tag, type, key)
            ? data.domProps || (data.domProps = {})
            : data.attrs || (data.attrs = {});
        }
        var camelizedKey = camelize(key);
        var hyphenatedKey = hyphenate(key);
        if (!(camelizedKey in hash) && !(hyphenatedKey in hash)) {
          hash[key] = value[key];

          if (isSync) {
            var on = data.on || (data.on = {});
            on[("update:" + key)] = function ($event) {
              value[key] = $event;
            };
          }
        }
      };

      for (var key in value) loop( key );
    }
  }
  return data
}

/*  */

/**
 * Runtime helper for rendering static trees.
 */
function renderStatic (
  index,
  isInFor
) {
  var cached = this._staticTrees || (this._staticTrees = []);
  var tree = cached[index];
  // if has already-rendered static tree and not inside v-for,
  // we can reuse the same tree.
  if (tree && !isInFor) {
    return tree
  }
  // otherwise, render a fresh tree.
  tree = cached[index] = this.$options.staticRenderFns[index].call(
    this._renderProxy,
    null,
    this // for render fns generated for functional component templates
  );
  markStatic(tree, ("__static__" + index), false);
  return tree
}

/**
 * Runtime helper for v-once.
 * Effectively it means marking the node as static with a unique key.
 */
function markOnce (
  tree,
  index,
  key
) {
  markStatic(tree, ("__once__" + index + (key ? ("_" + key) : "")), true);
  return tree
}

function markStatic (
  tree,
  key,
  isOnce
) {
  if (Array.isArray(tree)) {
    for (var i = 0; i < tree.length; i++) {
      if (tree[i] && typeof tree[i] !== 'string') {
        markStaticNode(tree[i], (key + "_" + i), isOnce);
      }
    }
  } else {
    markStaticNode(tree, key, isOnce);
  }
}

function markStaticNode (node, key, isOnce) {
  node.isStatic = true;
  node.key = key;
  node.isOnce = isOnce;
}

/*  */

function bindObjectListeners (data, value) {
  if (value) {
    if (!isPlainObject(value)) {
      warn(
        'v-on without argument expects an Object value',
        this
      );
    } else {
      var on = data.on = data.on ? extend({}, data.on) : {};
      for (var key in value) {
        var existing = on[key];
        var ours = value[key];
        on[key] = existing ? [].concat(existing, ours) : ours;
      }
    }
  }
  return data
}

/*  */

function resolveScopedSlots (
  fns, // see flow/vnode
  res,
  // the following are added in 2.6
  hasDynamicKeys,
  contentHashKey
) {
  res = res || { $stable: !hasDynamicKeys };
  for (var i = 0; i < fns.length; i++) {
    var slot = fns[i];
    if (Array.isArray(slot)) {
      resolveScopedSlots(slot, res, hasDynamicKeys);
    } else if (slot) {
      // marker for reverse proxying v-slot without scope on this.$slots
      if (slot.proxy) {
        slot.fn.proxy = true;
      }
      res[slot.key] = slot.fn;
    }
  }
  if (contentHashKey) {
    (res).$key = contentHashKey;
  }
  return res
}

/*  */

function bindDynamicKeys (baseObj, values) {
  for (var i = 0; i < values.length; i += 2) {
    var key = values[i];
    if (typeof key === 'string' && key) {
      baseObj[values[i]] = values[i + 1];
    } else if (key !== '' && key !== null) {
      // null is a speical value for explicitly removing a binding
      warn(
        ("Invalid value for dynamic directive argument (expected string or null): " + key),
        this
      );
    }
  }
  return baseObj
}

// helper to dynamically append modifier runtime markers to event names.
// ensure only append when value is already string, otherwise it will be cast
// to string and cause the type check to miss.
function prependModifier (value, symbol) {
  return typeof value === 'string' ? symbol + value : value
}

/*  */

function installRenderHelpers (target) {
  target._o = markOnce;
  target._n = toNumber;
  target._s = toString;
  target._l = renderList;
  target._t = renderSlot;
  target._q = looseEqual;
  target._i = looseIndexOf;
  target._m = renderStatic;
  target._f = resolveFilter;
  target._k = checkKeyCodes;
  target._b = bindObjectProps;
  target._v = createTextVNode;
  target._e = createEmptyVNode;
  target._u = resolveScopedSlots;
  target._g = bindObjectListeners;
  target._d = bindDynamicKeys;
  target._p = prependModifier;
}

/*  */

function FunctionalRenderContext (
  data,
  props,
  children,
  parent,
  Ctor
) {
  var this$1 = this;

  var options = Ctor.options;
  // ensure the createElement function in functional components
  // gets a unique context - this is necessary for correct named slot check
  var contextVm;
  if (hasOwn(parent, '_uid')) {
    contextVm = Object.create(parent);
    // $flow-disable-line
    contextVm._original = parent;
  } else {
    // the context vm passed in is a functional context as well.
    // in this case we want to make sure we are able to get a hold to the
    // real context instance.
    contextVm = parent;
    // $flow-disable-line
    parent = parent._original;
  }
  var isCompiled = isTrue(options._compiled);
  var needNormalization = !isCompiled;

  this.data = data;
  this.props = props;
  this.children = children;
  this.parent = parent;
  this.listeners = data.on || emptyObject;
  this.injections = resolveInject(options.inject, parent);
  this.slots = function () {
    if (!this$1.$slots) {
      normalizeScopedSlots(
        data.scopedSlots,
        this$1.$slots = resolveSlots(children, parent)
      );
    }
    return this$1.$slots
  };

  Object.defineProperty(this, 'scopedSlots', ({
    enumerable: true,
    get: function get () {
      return normalizeScopedSlots(data.scopedSlots, this.slots())
    }
  }));

  // support for compiled functional template
  if (isCompiled) {
    // exposing $options for renderStatic()
    this.$options = options;
    // pre-resolve slots for renderSlot()
    this.$slots = this.slots();
    this.$scopedSlots = normalizeScopedSlots(data.scopedSlots, this.$slots);
  }

  if (options._scopeId) {
    this._c = function (a, b, c, d) {
      var vnode = createElement(contextVm, a, b, c, d, needNormalization);
      if (vnode && !Array.isArray(vnode)) {
        vnode.fnScopeId = options._scopeId;
        vnode.fnContext = parent;
      }
      return vnode
    };
  } else {
    this._c = function (a, b, c, d) { return createElement(contextVm, a, b, c, d, needNormalization); };
  }
}

installRenderHelpers(FunctionalRenderContext.prototype);

function createFunctionalComponent (
  Ctor,
  propsData,
  data,
  contextVm,
  children
) {
  var options = Ctor.options;
  var props = {};
  var propOptions = options.props;
  if (isDef(propOptions)) {
    for (var key in propOptions) {
      props[key] = validateProp(key, propOptions, propsData || emptyObject);
    }
  } else {
    if (isDef(data.attrs)) { mergeProps(props, data.attrs); }
    if (isDef(data.props)) { mergeProps(props, data.props); }
  }

  var renderContext = new FunctionalRenderContext(
    data,
    props,
    children,
    contextVm,
    Ctor
  );

  var vnode = options.render.call(null, renderContext._c, renderContext);

  if (vnode instanceof VNode) {
    return cloneAndMarkFunctionalResult(vnode, data, renderContext.parent, options, renderContext)
  } else if (Array.isArray(vnode)) {
    var vnodes = normalizeChildren(vnode) || [];
    var res = new Array(vnodes.length);
    for (var i = 0; i < vnodes.length; i++) {
      res[i] = cloneAndMarkFunctionalResult(vnodes[i], data, renderContext.parent, options, renderContext);
    }
    return res
  }
}

function cloneAndMarkFunctionalResult (vnode, data, contextVm, options, renderContext) {
  // #7817 clone node before setting fnContext, otherwise if the node is reused
  // (e.g. it was from a cached normal slot) the fnContext causes named slots
  // that should not be matched to match.
  var clone = cloneVNode(vnode);
  clone.fnContext = contextVm;
  clone.fnOptions = options;
  {
    (clone.devtoolsMeta = clone.devtoolsMeta || {}).renderContext = renderContext;
  }
  if (data.slot) {
    (clone.data || (clone.data = {})).slot = data.slot;
  }
  return clone
}

function mergeProps (to, from) {
  for (var key in from) {
    to[camelize(key)] = from[key];
  }
}

/*  */

/*  */

/*  */

/*  */

// inline hooks to be invoked on component VNodes during patch
var componentVNodeHooks = {
  init: function init (vnode, hydrating) {
    if (
      vnode.componentInstance &&
      !vnode.componentInstance._isDestroyed &&
      vnode.data.keepAlive
    ) {
      // kept-alive components, treat as a patch
      var mountedNode = vnode; // work around flow
      componentVNodeHooks.prepatch(mountedNode, mountedNode);
    } else {
      var child = vnode.componentInstance = createComponentInstanceForVnode(
        vnode,
        activeInstance
      );
      child.$mount(hydrating ? vnode.elm : undefined, hydrating);
    }
  },

  prepatch: function prepatch (oldVnode, vnode) {
    var options = vnode.componentOptions;
    var child = vnode.componentInstance = oldVnode.componentInstance;
    updateChildComponent(
      child,
      options.propsData, // updated props
      options.listeners, // updated listeners
      vnode, // new parent vnode
      options.children // new children
    );
  },

  insert: function insert (vnode) {
    var context = vnode.context;
    var componentInstance = vnode.componentInstance;
    if (!componentInstance._isMounted) {
      componentInstance._isMounted = true;
      callHook(componentInstance, 'mounted');
    }
    if (vnode.data.keepAlive) {
      if (context._isMounted) {
        // vue-router#1212
        // During updates, a kept-alive component's child components may
        // change, so directly walking the tree here may call activated hooks
        // on incorrect children. Instead we push them into a queue which will
        // be processed after the whole patch process ended.
        queueActivatedComponent(componentInstance);
      } else {
        activateChildComponent(componentInstance, true /* direct */);
      }
    }
  },

  destroy: function destroy (vnode) {
    var componentInstance = vnode.componentInstance;
    if (!componentInstance._isDestroyed) {
      if (!vnode.data.keepAlive) {
        componentInstance.$destroy();
      } else {
        deactivateChildComponent(componentInstance, true /* direct */);
      }
    }
  }
};

var hooksToMerge = Object.keys(componentVNodeHooks);

function createComponent (
  Ctor,
  data,
  context,
  children,
  tag
) {
  if (isUndef(Ctor)) {
    return
  }

  var baseCtor = context.$options._base;

  // plain options object: turn it into a constructor
  if (isObject(Ctor)) {
    Ctor = baseCtor.extend(Ctor);
  }

  // if at this stage it's not a constructor or an async component factory,
  // reject.
  if (typeof Ctor !== 'function') {
    {
      warn(("Invalid Component definition: " + (String(Ctor))), context);
    }
    return
  }

  // async component
  var asyncFactory;
  if (isUndef(Ctor.cid)) {
    asyncFactory = Ctor;
    Ctor = resolveAsyncComponent(asyncFactory, baseCtor);
    if (Ctor === undefined) {
      // return a placeholder node for async component, which is rendered
      // as a comment node but preserves all the raw information for the node.
      // the information will be used for async server-rendering and hydration.
      return createAsyncPlaceholder(
        asyncFactory,
        data,
        context,
        children,
        tag
      )
    }
  }

  data = data || {};

  // resolve constructor options in case global mixins are applied after
  // component constructor creation
  resolveConstructorOptions(Ctor);

  // transform component v-model data into props & events
  if (isDef(data.model)) {
    transformModel(Ctor.options, data);
  }

  // extract props
  var propsData = extractPropsFromVNodeData(data, Ctor, tag);

  // functional component
  if (isTrue(Ctor.options.functional)) {
    return createFunctionalComponent(Ctor, propsData, data, context, children)
  }

  // extract listeners, since these needs to be treated as
  // child component listeners instead of DOM listeners
  var listeners = data.on;
  // replace with listeners with .native modifier
  // so it gets processed during parent component patch.
  data.on = data.nativeOn;

  if (isTrue(Ctor.options.abstract)) {
    // abstract components do not keep anything
    // other than props & listeners & slot

    // work around flow
    var slot = data.slot;
    data = {};
    if (slot) {
      data.slot = slot;
    }
  }

  // install component management hooks onto the placeholder node
  installComponentHooks(data);

  // return a placeholder vnode
  var name = Ctor.options.name || tag;
  var vnode = new VNode(
    ("vue-component-" + (Ctor.cid) + (name ? ("-" + name) : '')),
    data, undefined, undefined, undefined, context,
    { Ctor: Ctor, propsData: propsData, listeners: listeners, tag: tag, children: children },
    asyncFactory
  );

  return vnode
}

function createComponentInstanceForVnode (
  vnode, // we know it's MountedComponentVNode but flow doesn't
  parent // activeInstance in lifecycle state
) {
  var options = {
    _isComponent: true,
    _parentVnode: vnode,
    parent: parent
  };
  // check inline-template render functions
  var inlineTemplate = vnode.data.inlineTemplate;
  if (isDef(inlineTemplate)) {
    options.render = inlineTemplate.render;
    options.staticRenderFns = inlineTemplate.staticRenderFns;
  }
  return new vnode.componentOptions.Ctor(options)
}

function installComponentHooks (data) {
  var hooks = data.hook || (data.hook = {});
  for (var i = 0; i < hooksToMerge.length; i++) {
    var key = hooksToMerge[i];
    var existing = hooks[key];
    var toMerge = componentVNodeHooks[key];
    if (existing !== toMerge && !(existing && existing._merged)) {
      hooks[key] = existing ? mergeHook$1(toMerge, existing) : toMerge;
    }
  }
}

function mergeHook$1 (f1, f2) {
  var merged = function (a, b) {
    // flow complains about extra args which is why we use any
    f1(a, b);
    f2(a, b);
  };
  merged._merged = true;
  return merged
}

// transform component v-model info (value and callback) into
// prop and event handler respectively.
function transformModel (options, data) {
  var prop = (options.model && options.model.prop) || 'value';
  var event = (options.model && options.model.event) || 'input'
  ;(data.attrs || (data.attrs = {}))[prop] = data.model.value;
  var on = data.on || (data.on = {});
  var existing = on[event];
  var callback = data.model.callback;
  if (isDef(existing)) {
    if (
      Array.isArray(existing)
        ? existing.indexOf(callback) === -1
        : existing !== callback
    ) {
      on[event] = [callback].concat(existing);
    }
  } else {
    on[event] = callback;
  }
}

/*  */

var SIMPLE_NORMALIZE = 1;
var ALWAYS_NORMALIZE = 2;

// wrapper function for providing a more flexible interface
// without getting yelled at by flow
function createElement (
  context,
  tag,
  data,
  children,
  normalizationType,
  alwaysNormalize
) {
  if (Array.isArray(data) || isPrimitive(data)) {
    normalizationType = children;
    children = data;
    data = undefined;
  }
  if (isTrue(alwaysNormalize)) {
    normalizationType = ALWAYS_NORMALIZE;
  }
  return _createElement(context, tag, data, children, normalizationType)
}

function _createElement (
  context,
  tag,
  data,
  children,
  normalizationType
) {
  if (isDef(data) && isDef((data).__ob__)) {
    warn(
      "Avoid using observed data object as vnode data: " + (JSON.stringify(data)) + "\n" +
      'Always create fresh vnode data objects in each render!',
      context
    );
    return createEmptyVNode()
  }
  // object syntax in v-bind
  if (isDef(data) && isDef(data.is)) {
    tag = data.is;
  }
  if (!tag) {
    // in case of component :is set to falsy value
    return createEmptyVNode()
  }
  // warn against non-primitive key
  if (isDef(data) && isDef(data.key) && !isPrimitive(data.key)
  ) {
    {
      warn(
        'Avoid using non-primitive value as key, ' +
        'use string/number value instead.',
        context
      );
    }
  }
  // support single function children as default scoped slot
  if (Array.isArray(children) &&
    typeof children[0] === 'function'
  ) {
    data = data || {};
    data.scopedSlots = { default: children[0] };
    children.length = 0;
  }
  if (normalizationType === ALWAYS_NORMALIZE) {
    children = normalizeChildren(children);
  } else if (normalizationType === SIMPLE_NORMALIZE) {
    children = simpleNormalizeChildren(children);
  }
  var vnode, ns;
  if (typeof tag === 'string') {
    var Ctor;
    ns = (context.$vnode && context.$vnode.ns) || config.getTagNamespace(tag);
    if (config.isReservedTag(tag)) {
      // platform built-in elements
      vnode = new VNode(
        config.parsePlatformTagName(tag), data, children,
        undefined, undefined, context
      );
    } else if ((!data || !data.pre) && isDef(Ctor = resolveAsset(context.$options, 'components', tag))) {
      // component
      vnode = createComponent(Ctor, data, context, children, tag);
    } else {
      // unknown or unlisted namespaced elements
      // check at runtime because it may get assigned a namespace when its
      // parent normalizes children
      vnode = new VNode(
        tag, data, children,
        undefined, undefined, context
      );
    }
  } else {
    // direct component options / constructor
    vnode = createComponent(tag, data, context, children);
  }
  if (Array.isArray(vnode)) {
    return vnode
  } else if (isDef(vnode)) {
    if (isDef(ns)) { applyNS(vnode, ns); }
    if (isDef(data)) { registerDeepBindings(data); }
    return vnode
  } else {
    return createEmptyVNode()
  }
}

function applyNS (vnode, ns, force) {
  vnode.ns = ns;
  if (vnode.tag === 'foreignObject') {
    // use default namespace inside foreignObject
    ns = undefined;
    force = true;
  }
  if (isDef(vnode.children)) {
    for (var i = 0, l = vnode.children.length; i < l; i++) {
      var child = vnode.children[i];
      if (isDef(child.tag) && (
        isUndef(child.ns) || (isTrue(force) && child.tag !== 'svg'))) {
        applyNS(child, ns, force);
      }
    }
  }
}

// ref #5318
// necessary to ensure parent re-render when deep bindings like :style and
// :class are used on slot nodes
function registerDeepBindings (data) {
  if (isObject(data.style)) {
    traverse(data.style);
  }
  if (isObject(data.class)) {
    traverse(data.class);
  }
}

/*  */

function initRender (vm) {
  vm._vnode = null; // the root of the child tree
  vm._staticTrees = null; // v-once cached trees
  var options = vm.$options;
  var parentVnode = vm.$vnode = options._parentVnode; // the placeholder node in parent tree
  var renderContext = parentVnode && parentVnode.context;
  vm.$slots = resolveSlots(options._renderChildren, renderContext);
  vm.$scopedSlots = emptyObject;
  // bind the createElement fn to this instance
  // so that we get proper render context inside it.
  // args order: tag, data, children, normalizationType, alwaysNormalize
  // internal version is used by render functions compiled from templates
  vm._c = function (a, b, c, d) { return createElement(vm, a, b, c, d, false); };
  // normalization is always applied for the public version, used in
  // user-written render functions.
  vm.$createElement = function (a, b, c, d) { return createElement(vm, a, b, c, d, true); };

  // $attrs & $listeners are exposed for easier HOC creation.
  // they need to be reactive so that HOCs using them are always updated
  var parentData = parentVnode && parentVnode.data;

  /* istanbul ignore else */
  {
    defineReactive$$1(vm, '$attrs', parentData && parentData.attrs || emptyObject, function () {
      !isUpdatingChildComponent && warn("$attrs is readonly.", vm);
    }, true);
    defineReactive$$1(vm, '$listeners', options._parentListeners || emptyObject, function () {
      !isUpdatingChildComponent && warn("$listeners is readonly.", vm);
    }, true);
  }
}

var currentRenderingInstance = null;

function renderMixin (Vue) {
  // install runtime convenience helpers
  installRenderHelpers(Vue.prototype);

  Vue.prototype.$nextTick = function (fn) {
    return nextTick(fn, this)
  };

  Vue.prototype._render = function () {
    var vm = this;
    var ref = vm.$options;
    var render = ref.render;
    var _parentVnode = ref._parentVnode;

    if (_parentVnode) {
      vm.$scopedSlots = normalizeScopedSlots(
        _parentVnode.data.scopedSlots,
        vm.$slots,
        vm.$scopedSlots
      );
    }

    // set parent vnode. this allows render functions to have access
    // to the data on the placeholder node.
    vm.$vnode = _parentVnode;
    // render self
    var vnode;
    try {
      // There's no need to maintain a stack becaues all render fns are called
      // separately from one another. Nested component's render fns are called
      // when parent component is patched.
      currentRenderingInstance = vm;
      vnode = render.call(vm._renderProxy, vm.$createElement);
    } catch (e) {
      handleError(e, vm, "render");
      // return error render result,
      // or previous vnode to prevent render error causing blank component
      /* istanbul ignore else */
      if (vm.$options.renderError) {
        try {
          vnode = vm.$options.renderError.call(vm._renderProxy, vm.$createElement, e);
        } catch (e) {
          handleError(e, vm, "renderError");
          vnode = vm._vnode;
        }
      } else {
        vnode = vm._vnode;
      }
    } finally {
      currentRenderingInstance = null;
    }
    // if the returned array contains only a single node, allow it
    if (Array.isArray(vnode) && vnode.length === 1) {
      vnode = vnode[0];
    }
    // return empty vnode in case the render function errored out
    if (!(vnode instanceof VNode)) {
      if (Array.isArray(vnode)) {
        warn(
          'Multiple root nodes returned from render function. Render function ' +
          'should return a single root node.',
          vm
        );
      }
      vnode = createEmptyVNode();
    }
    // set parent
    vnode.parent = _parentVnode;
    return vnode
  };
}

/*  */

function ensureCtor (comp, base) {
  if (
    comp.__esModule ||
    (hasSymbol && comp[Symbol.toStringTag] === 'Module')
  ) {
    comp = comp.default;
  }
  return isObject(comp)
    ? base.extend(comp)
    : comp
}

function createAsyncPlaceholder (
  factory,
  data,
  context,
  children,
  tag
) {
  var node = createEmptyVNode();
  node.asyncFactory = factory;
  node.asyncMeta = { data: data, context: context, children: children, tag: tag };
  return node
}

function resolveAsyncComponent (
  factory,
  baseCtor
) {
  if (isTrue(factory.error) && isDef(factory.errorComp)) {
    return factory.errorComp
  }

  if (isDef(factory.resolved)) {
    return factory.resolved
  }

  var owner = currentRenderingInstance;
  if (owner && isDef(factory.owners) && factory.owners.indexOf(owner) === -1) {
    // already pending
    factory.owners.push(owner);
  }

  if (isTrue(factory.loading) && isDef(factory.loadingComp)) {
    return factory.loadingComp
  }

  if (owner && !isDef(factory.owners)) {
    var owners = factory.owners = [owner];
    var sync = true;
    var timerLoading = null;
    var timerTimeout = null

    ;(owner).$on('hook:destroyed', function () { return remove(owners, owner); });

    var forceRender = function (renderCompleted) {
      for (var i = 0, l = owners.length; i < l; i++) {
        (owners[i]).$forceUpdate();
      }

      if (renderCompleted) {
        owners.length = 0;
        if (timerLoading !== null) {
          clearTimeout(timerLoading);
          timerLoading = null;
        }
        if (timerTimeout !== null) {
          clearTimeout(timerTimeout);
          timerTimeout = null;
        }
      }
    };

    var resolve = once(function (res) {
      // cache resolved
      factory.resolved = ensureCtor(res, baseCtor);
      // invoke callbacks only if this is not a synchronous resolve
      // (async resolves are shimmed as synchronous during SSR)
      if (!sync) {
        forceRender(true);
      } else {
        owners.length = 0;
      }
    });

    var reject = once(function (reason) {
      warn(
        "Failed to resolve async component: " + (String(factory)) +
        (reason ? ("\nReason: " + reason) : '')
      );
      if (isDef(factory.errorComp)) {
        factory.error = true;
        forceRender(true);
      }
    });

    var res = factory(resolve, reject);

    if (isObject(res)) {
      if (isPromise(res)) {
        // () => Promise
        if (isUndef(factory.resolved)) {
          res.then(resolve, reject);
        }
      } else if (isPromise(res.component)) {
        res.component.then(resolve, reject);

        if (isDef(res.error)) {
          factory.errorComp = ensureCtor(res.error, baseCtor);
        }

        if (isDef(res.loading)) {
          factory.loadingComp = ensureCtor(res.loading, baseCtor);
          if (res.delay === 0) {
            factory.loading = true;
          } else {
            timerLoading = setTimeout(function () {
              timerLoading = null;
              if (isUndef(factory.resolved) && isUndef(factory.error)) {
                factory.loading = true;
                forceRender(false);
              }
            }, res.delay || 200);
          }
        }

        if (isDef(res.timeout)) {
          timerTimeout = setTimeout(function () {
            timerTimeout = null;
            if (isUndef(factory.resolved)) {
              reject(
                "timeout (" + (res.timeout) + "ms)"
              );
            }
          }, res.timeout);
        }
      }
    }

    sync = false;
    // return in case resolved synchronously
    return factory.loading
      ? factory.loadingComp
      : factory.resolved
  }
}

/*  */

function isAsyncPlaceholder (node) {
  return node.isComment && node.asyncFactory
}

/*  */

function getFirstComponentChild (children) {
  if (Array.isArray(children)) {
    for (var i = 0; i < children.length; i++) {
      var c = children[i];
      if (isDef(c) && (isDef(c.componentOptions) || isAsyncPlaceholder(c))) {
        return c
      }
    }
  }
}

/*  */

/*  */

function initEvents (vm) {
  vm._events = Object.create(null);
  vm._hasHookEvent = false;
  // init parent attached events
  var listeners = vm.$options._parentListeners;
  if (listeners) {
    updateComponentListeners(vm, listeners);
  }
}

var target;

function add (event, fn) {
  target.$on(event, fn);
}

function remove$1 (event, fn) {
  target.$off(event, fn);
}

function createOnceHandler (event, fn) {
  var _target = target;
  return function onceHandler () {
    var res = fn.apply(null, arguments);
    if (res !== null) {
      _target.$off(event, onceHandler);
    }
  }
}

function updateComponentListeners (
  vm,
  listeners,
  oldListeners
) {
  target = vm;
  updateListeners(listeners, oldListeners || {}, add, remove$1, createOnceHandler, vm);
  target = undefined;
}

function eventsMixin (Vue) {
  var hookRE = /^hook:/;
  Vue.prototype.$on = function (event, fn) {
    var vm = this;
    if (Array.isArray(event)) {
      for (var i = 0, l = event.length; i < l; i++) {
        vm.$on(event[i], fn);
      }
    } else {
      (vm._events[event] || (vm._events[event] = [])).push(fn);
      // optimize hook:event cost by using a boolean flag marked at registration
      // instead of a hash lookup
      if (hookRE.test(event)) {
        vm._hasHookEvent = true;
      }
    }
    return vm
  };

  Vue.prototype.$once = function (event, fn) {
    var vm = this;
    function on () {
      vm.$off(event, on);
      fn.apply(vm, arguments);
    }
    on.fn = fn;
    vm.$on(event, on);
    return vm
  };

  Vue.prototype.$off = function (event, fn) {
    var vm = this;
    // all
    if (!arguments.length) {
      vm._events = Object.create(null);
      return vm
    }
    // array of events
    if (Array.isArray(event)) {
      for (var i$1 = 0, l = event.length; i$1 < l; i$1++) {
        vm.$off(event[i$1], fn);
      }
      return vm
    }
    // specific event
    var cbs = vm._events[event];
    if (!cbs) {
      return vm
    }
    if (!fn) {
      vm._events[event] = null;
      return vm
    }
    // specific handler
    var cb;
    var i = cbs.length;
    while (i--) {
      cb = cbs[i];
      if (cb === fn || cb.fn === fn) {
        cbs.splice(i, 1);
        break
      }
    }
    return vm
  };

  Vue.prototype.$emit = function (event) {
    var vm = this;
    {
      var lowerCaseEvent = event.toLowerCase();
      if (lowerCaseEvent !== event && vm._events[lowerCaseEvent]) {
        tip(
          "Event \"" + lowerCaseEvent + "\" is emitted in component " +
          (formatComponentName(vm)) + " but the handler is registered for \"" + event + "\". " +
          "Note that HTML attributes are case-insensitive and you cannot use " +
          "v-on to listen to camelCase events when using in-DOM templates. " +
          "You should probably use \"" + (hyphenate(event)) + "\" instead of \"" + event + "\"."
        );
      }
    }
    var cbs = vm._events[event];
    if (cbs) {
      cbs = cbs.length > 1 ? toArray(cbs) : cbs;
      var args = toArray(arguments, 1);
      var info = "event handler for \"" + event + "\"";
      for (var i = 0, l = cbs.length; i < l; i++) {
        invokeWithErrorHandling(cbs[i], vm, args, vm, info);
      }
    }
    return vm
  };
}

/*  */

var activeInstance = null;
var isUpdatingChildComponent = false;

function setActiveInstance(vm) {
  var prevActiveInstance = activeInstance;
  activeInstance = vm;
  return function () {
    activeInstance = prevActiveInstance;
  }
}

function initLifecycle (vm) {
  var options = vm.$options;

  // locate first non-abstract parent
  var parent = options.parent;
  if (parent && !options.abstract) {
    while (parent.$options.abstract && parent.$parent) {
      parent = parent.$parent;
    }
    parent.$children.push(vm);
  }

  vm.$parent = parent;
  vm.$root = parent ? parent.$root : vm;

  vm.$children = [];
  vm.$refs = {};

  vm._watcher = null;
  vm._inactive = null;
  vm._directInactive = false;
  vm._isMounted = false;
  vm._isDestroyed = false;
  vm._isBeingDestroyed = false;
}

function lifecycleMixin (Vue) {
  Vue.prototype._update = function (vnode, hydrating) {
    var vm = this;
    var prevEl = vm.$el;
    var prevVnode = vm._vnode;
    var restoreActiveInstance = setActiveInstance(vm);
    vm._vnode = vnode;
    // Vue.prototype.__patch__ is injected in entry points
    // based on the rendering backend used.
    if (!prevVnode) {
      // initial render
      vm.$el = vm.__patch__(vm.$el, vnode, hydrating, false /* removeOnly */);
    } else {
      // updates
      vm.$el = vm.__patch__(prevVnode, vnode);
    }
    restoreActiveInstance();
    // update __vue__ reference
    if (prevEl) {
      prevEl.__vue__ = null;
    }
    if (vm.$el) {
      vm.$el.__vue__ = vm;
    }
    // if parent is an HOC, update its $el as well
    if (vm.$vnode && vm.$parent && vm.$vnode === vm.$parent._vnode) {
      vm.$parent.$el = vm.$el;
    }
    // updated hook is called by the scheduler to ensure that children are
    // updated in a parent's updated hook.
  };

  Vue.prototype.$forceUpdate = function () {
    var vm = this;
    if (vm._watcher) {
      vm._watcher.update();
    }
  };

  Vue.prototype.$destroy = function () {
    var vm = this;
    if (vm._isBeingDestroyed) {
      return
    }
    callHook(vm, 'beforeDestroy');
    vm._isBeingDestroyed = true;
    // remove self from parent
    var parent = vm.$parent;
    if (parent && !parent._isBeingDestroyed && !vm.$options.abstract) {
      remove(parent.$children, vm);
    }
    // teardown watchers
    if (vm._watcher) {
      vm._watcher.teardown();
    }
    var i = vm._watchers.length;
    while (i--) {
      vm._watchers[i].teardown();
    }
    // remove reference from data ob
    // frozen object may not have observer.
    if (vm._data.__ob__) {
      vm._data.__ob__.vmCount--;
    }
    // call the last hook...
    vm._isDestroyed = true;
    // invoke destroy hooks on current rendered tree
    vm.__patch__(vm._vnode, null);
    // fire destroyed hook
    callHook(vm, 'destroyed');
    // turn off all instance listeners.
    vm.$off();
    // remove __vue__ reference
    if (vm.$el) {
      vm.$el.__vue__ = null;
    }
    // release circular reference (#6759)
    if (vm.$vnode) {
      vm.$vnode.parent = null;
    }
  };
}

function mountComponent (
  vm,
  el,
  hydrating
) {
  vm.$el = el;
  if (!vm.$options.render) {
    vm.$options.render = createEmptyVNode;
    {
      /* istanbul ignore if */
      if ((vm.$options.template && vm.$options.template.charAt(0) !== '#') ||
        vm.$options.el || el) {
        warn(
          'You are using the runtime-only build of Vue where the template ' +
          'compiler is not available. Either pre-compile the templates into ' +
          'render functions, or use the compiler-included build.',
          vm
        );
      } else {
        warn(
          'Failed to mount component: template or render function not defined.',
          vm
        );
      }
    }
  }
  callHook(vm, 'beforeMount');

  var updateComponent;
  /* istanbul ignore if */
  if (config.performance && mark) {
    updateComponent = function () {
      var name = vm._name;
      var id = vm._uid;
      var startTag = "vue-perf-start:" + id;
      var endTag = "vue-perf-end:" + id;

      mark(startTag);
      var vnode = vm._render();
      mark(endTag);
      measure(("vue " + name + " render"), startTag, endTag);

      mark(startTag);
      vm._update(vnode, hydrating);
      mark(endTag);
      measure(("vue " + name + " patch"), startTag, endTag);
    };
  } else {
    updateComponent = function () {
      vm._update(vm._render(), hydrating);
    };
  }

  // we set this to vm._watcher inside the watcher's constructor
  // since the watcher's initial patch may call $forceUpdate (e.g. inside child
  // component's mounted hook), which relies on vm._watcher being already defined
  new Watcher(vm, updateComponent, noop, {
    before: function before () {
      if (vm._isMounted && !vm._isDestroyed) {
        callHook(vm, 'beforeUpdate');
      }
    }
  }, true /* isRenderWatcher */);
  hydrating = false;

  // manually mounted instance, call mounted on self
  // mounted is called for render-created child components in its inserted hook
  if (vm.$vnode == null) {
    vm._isMounted = true;
    callHook(vm, 'mounted');
  }
  return vm
}

function updateChildComponent (
  vm,
  propsData,
  listeners,
  parentVnode,
  renderChildren
) {
  {
    isUpdatingChildComponent = true;
  }

  // determine whether component has slot children
  // we need to do this before overwriting $options._renderChildren.

  // check if there are dynamic scopedSlots (hand-written or compiled but with
  // dynamic slot names). Static scoped slots compiled from template has the
  // "$stable" marker.
  var newScopedSlots = parentVnode.data.scopedSlots;
  var oldScopedSlots = vm.$scopedSlots;
  var hasDynamicScopedSlot = !!(
    (newScopedSlots && !newScopedSlots.$stable) ||
    (oldScopedSlots !== emptyObject && !oldScopedSlots.$stable) ||
    (newScopedSlots && vm.$scopedSlots.$key !== newScopedSlots.$key)
  );

  // Any static slot children from the parent may have changed during parent's
  // update. Dynamic scoped slots may also have changed. In such cases, a forced
  // update is necessary to ensure correctness.
  var needsForceUpdate = !!(
    renderChildren ||               // has new static slots
    vm.$options._renderChildren ||  // has old static slots
    hasDynamicScopedSlot
  );

  vm.$options._parentVnode = parentVnode;
  vm.$vnode = parentVnode; // update vm's placeholder node without re-render

  if (vm._vnode) { // update child tree's parent
    vm._vnode.parent = parentVnode;
  }
  vm.$options._renderChildren = renderChildren;

  // update $attrs and $listeners hash
  // these are also reactive so they may trigger child update if the child
  // used them during render
  vm.$attrs = parentVnode.data.attrs || emptyObject;
  vm.$listeners = listeners || emptyObject;

  // update props
  if (propsData && vm.$options.props) {
    toggleObserving(false);
    var props = vm._props;
    var propKeys = vm.$options._propKeys || [];
    for (var i = 0; i < propKeys.length; i++) {
      var key = propKeys[i];
      var propOptions = vm.$options.props; // wtf flow?
      props[key] = validateProp(key, propOptions, propsData, vm);
    }
    toggleObserving(true);
    // keep a copy of raw propsData
    vm.$options.propsData = propsData;
  }

  // update listeners
  listeners = listeners || emptyObject;
  var oldListeners = vm.$options._parentListeners;
  vm.$options._parentListeners = listeners;
  updateComponentListeners(vm, listeners, oldListeners);

  // resolve slots + force update if has children
  if (needsForceUpdate) {
    vm.$slots = resolveSlots(renderChildren, parentVnode.context);
    vm.$forceUpdate();
  }

  {
    isUpdatingChildComponent = false;
  }
}

function isInInactiveTree (vm) {
  while (vm && (vm = vm.$parent)) {
    if (vm._inactive) { return true }
  }
  return false
}

function activateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = false;
    if (isInInactiveTree(vm)) {
      return
    }
  } else if (vm._directInactive) {
    return
  }
  if (vm._inactive || vm._inactive === null) {
    vm._inactive = false;
    for (var i = 0; i < vm.$children.length; i++) {
      activateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'activated');
  }
}

function deactivateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = true;
    if (isInInactiveTree(vm)) {
      return
    }
  }
  if (!vm._inactive) {
    vm._inactive = true;
    for (var i = 0; i < vm.$children.length; i++) {
      deactivateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'deactivated');
  }
}

function callHook (vm, hook) {
  // #7573 disable dep collection when invoking lifecycle hooks
  pushTarget();
  var handlers = vm.$options[hook];
  var info = hook + " hook";
  if (handlers) {
    for (var i = 0, j = handlers.length; i < j; i++) {
      invokeWithErrorHandling(handlers[i], vm, null, vm, info);
    }
  }
  if (vm._hasHookEvent) {
    vm.$emit('hook:' + hook);
  }
  popTarget();
}

/*  */

var MAX_UPDATE_COUNT = 100;

var queue = [];
var activatedChildren = [];
var has = {};
var circular = {};
var waiting = false;
var flushing = false;
var index = 0;

/**
 * Reset the scheduler's state.
 */
function resetSchedulerState () {
  index = queue.length = activatedChildren.length = 0;
  has = {};
  {
    circular = {};
  }
  waiting = flushing = false;
}

// Async edge case #6566 requires saving the timestamp when event listeners are
// attached. However, calling performance.now() has a perf overhead especially
// if the page has thousands of event listeners. Instead, we take a timestamp
// every time the scheduler flushes and use that for all event listeners
// attached during that flush.
var currentFlushTimestamp = 0;

// Async edge case fix requires storing an event listener's attach timestamp.
var getNow = Date.now;

// Determine what event timestamp the browser is using. Annoyingly, the
// timestamp can either be hi-res (relative to page load) or low-res
// (relative to UNIX epoch), so in order to compare time we have to use the
// same timestamp type when saving the flush timestamp.
// All IE versions use low-res event timestamps, and have problematic clock
// implementations (#9632)
if (inBrowser && !isIE) {
  var performance = window.performance;
  if (
    performance &&
    typeof performance.now === 'function' &&
    getNow() > document.createEvent('Event').timeStamp
  ) {
    // if the event timestamp, although evaluated AFTER the Date.now(), is
    // smaller than it, it means the event is using a hi-res timestamp,
    // and we need to use the hi-res version for event listener timestamps as
    // well.
    getNow = function () { return performance.now(); };
  }
}

/**
 * Flush both queues and run the watchers.
 */
function flushSchedulerQueue () {
  currentFlushTimestamp = getNow();
  flushing = true;
  var watcher, id;

  // Sort queue before flush.
  // This ensures that:
  // 1. Components are updated from parent to child. (because parent is always
  //    created before the child)
  // 2. A component's user watchers are run before its render watcher (because
  //    user watchers are created before the render watcher)
  // 3. If a component is destroyed during a parent component's watcher run,
  //    its watchers can be skipped.
  queue.sort(function (a, b) { return a.id - b.id; });

  // do not cache length because more watchers might be pushed
  // as we run existing watchers
  for (index = 0; index < queue.length; index++) {
    watcher = queue[index];
    if (watcher.before) {
      watcher.before();
    }
    id = watcher.id;
    has[id] = null;
    watcher.run();
    // in dev build, check and stop circular updates.
    if (has[id] != null) {
      circular[id] = (circular[id] || 0) + 1;
      if (circular[id] > MAX_UPDATE_COUNT) {
        warn(
          'You may have an infinite update loop ' + (
            watcher.user
              ? ("in watcher with expression \"" + (watcher.expression) + "\"")
              : "in a component render function."
          ),
          watcher.vm
        );
        break
      }
    }
  }

  // keep copies of post queues before resetting state
  var activatedQueue = activatedChildren.slice();
  var updatedQueue = queue.slice();

  resetSchedulerState();

  // call component updated and activated hooks
  callActivatedHooks(activatedQueue);
  callUpdatedHooks(updatedQueue);

  // devtool hook
  /* istanbul ignore if */
  if (devtools && config.devtools) {
    devtools.emit('flush');
  }
}

function callUpdatedHooks (queue) {
  var i = queue.length;
  while (i--) {
    var watcher = queue[i];
    var vm = watcher.vm;
    if (vm._watcher === watcher && vm._isMounted && !vm._isDestroyed) {
      callHook(vm, 'updated');
    }
  }
}

/**
 * Queue a kept-alive component that was activated during patch.
 * The queue will be processed after the entire tree has been patched.
 */
function queueActivatedComponent (vm) {
  // setting _inactive to false here so that a render function can
  // rely on checking whether it's in an inactive tree (e.g. router-view)
  vm._inactive = false;
  activatedChildren.push(vm);
}

function callActivatedHooks (queue) {
  for (var i = 0; i < queue.length; i++) {
    queue[i]._inactive = true;
    activateChildComponent(queue[i], true /* true */);
  }
}

/**
 * Push a watcher into the watcher queue.
 * Jobs with duplicate IDs will be skipped unless it's
 * pushed when the queue is being flushed.
 */
function queueWatcher (watcher) {
  var id = watcher.id;
  if (has[id] == null) {
    has[id] = true;
    if (!flushing) {
      queue.push(watcher);
    } else {
      // if already flushing, splice the watcher based on its id
      // if already past its id, it will be run next immediately.
      var i = queue.length - 1;
      while (i > index && queue[i].id > watcher.id) {
        i--;
      }
      queue.splice(i + 1, 0, watcher);
    }
    // queue the flush
    if (!waiting) {
      waiting = true;

      if (!config.async) {
        flushSchedulerQueue();
        return
      }
      nextTick(flushSchedulerQueue);
    }
  }
}

/*  */



var uid$2 = 0;

/**
 * A watcher parses an expression, collects dependencies,
 * and fires callback when the expression value changes.
 * This is used for both the $watch() api and directives.
 */
var Watcher = function Watcher (
  vm,
  expOrFn,
  cb,
  options,
  isRenderWatcher
) {
  this.vm = vm;
  if (isRenderWatcher) {
    vm._watcher = this;
  }
  vm._watchers.push(this);
  // options
  if (options) {
    this.deep = !!options.deep;
    this.user = !!options.user;
    this.lazy = !!options.lazy;
    this.sync = !!options.sync;
    this.before = options.before;
  } else {
    this.deep = this.user = this.lazy = this.sync = false;
  }
  this.cb = cb;
  this.id = ++uid$2; // uid for batching
  this.active = true;
  this.dirty = this.lazy; // for lazy watchers
  this.deps = [];
  this.newDeps = [];
  this.depIds = new _Set();
  this.newDepIds = new _Set();
  this.expression = expOrFn.toString();
  // parse expression for getter
  if (typeof expOrFn === 'function') {
    this.getter = expOrFn;
  } else {
    this.getter = parsePath(expOrFn);
    if (!this.getter) {
      this.getter = noop;
      warn(
        "Failed watching path: \"" + expOrFn + "\" " +
        'Watcher only accepts simple dot-delimited paths. ' +
        'For full control, use a function instead.',
        vm
      );
    }
  }
  this.value = this.lazy
    ? undefined
    : this.get();
};

/**
 * Evaluate the getter, and re-collect dependencies.
 */
Watcher.prototype.get = function get () {
  pushTarget(this);
  var value;
  var vm = this.vm;
  try {
    value = this.getter.call(vm, vm);
  } catch (e) {
    if (this.user) {
      handleError(e, vm, ("getter for watcher \"" + (this.expression) + "\""));
    } else {
      throw e
    }
  } finally {
    // "touch" every property so they are all tracked as
    // dependencies for deep watching
    if (this.deep) {
      traverse(value);
    }
    popTarget();
    this.cleanupDeps();
  }
  return value
};

/**
 * Add a dependency to this directive.
 */
Watcher.prototype.addDep = function addDep (dep) {
  var id = dep.id;
  if (!this.newDepIds.has(id)) {
    this.newDepIds.add(id);
    this.newDeps.push(dep);
    if (!this.depIds.has(id)) {
      dep.addSub(this);
    }
  }
};

/**
 * Clean up for dependency collection.
 */
Watcher.prototype.cleanupDeps = function cleanupDeps () {
  var i = this.deps.length;
  while (i--) {
    var dep = this.deps[i];
    if (!this.newDepIds.has(dep.id)) {
      dep.removeSub(this);
    }
  }
  var tmp = this.depIds;
  this.depIds = this.newDepIds;
  this.newDepIds = tmp;
  this.newDepIds.clear();
  tmp = this.deps;
  this.deps = this.newDeps;
  this.newDeps = tmp;
  this.newDeps.length = 0;
};

/**
 * Subscriber interface.
 * Will be called when a dependency changes.
 */
Watcher.prototype.update = function update () {
  /* istanbul ignore else */
  if (this.lazy) {
    this.dirty = true;
  } else if (this.sync) {
    this.run();
  } else {
    queueWatcher(this);
  }
};

/**
 * Scheduler job interface.
 * Will be called by the scheduler.
 */
Watcher.prototype.run = function run () {
  if (this.active) {
    var value = this.get();
    if (
      value !== this.value ||
      // Deep watchers and watchers on Object/Arrays should fire even
      // when the value is the same, because the value may
      // have mutated.
      isObject(value) ||
      this.deep
    ) {
      // set new value
      var oldValue = this.value;
      this.value = value;
      if (this.user) {
        try {
          this.cb.call(this.vm, value, oldValue);
        } catch (e) {
          handleError(e, this.vm, ("callback for watcher \"" + (this.expression) + "\""));
        }
      } else {
        this.cb.call(this.vm, value, oldValue);
      }
    }
  }
};

/**
 * Evaluate the value of the watcher.
 * This only gets called for lazy watchers.
 */
Watcher.prototype.evaluate = function evaluate () {
  this.value = this.get();
  this.dirty = false;
};

/**
 * Depend on all deps collected by this watcher.
 */
Watcher.prototype.depend = function depend () {
  var i = this.deps.length;
  while (i--) {
    this.deps[i].depend();
  }
};

/**
 * Remove self from all dependencies' subscriber list.
 */
Watcher.prototype.teardown = function teardown () {
  if (this.active) {
    // remove self from vm's watcher list
    // this is a somewhat expensive operation so we skip it
    // if the vm is being destroyed.
    if (!this.vm._isBeingDestroyed) {
      remove(this.vm._watchers, this);
    }
    var i = this.deps.length;
    while (i--) {
      this.deps[i].removeSub(this);
    }
    this.active = false;
  }
};

/*  */

var sharedPropertyDefinition = {
  enumerable: true,
  configurable: true,
  get: noop,
  set: noop
};

function proxy (target, sourceKey, key) {
  sharedPropertyDefinition.get = function proxyGetter () {
    return this[sourceKey][key]
  };
  sharedPropertyDefinition.set = function proxySetter (val) {
    this[sourceKey][key] = val;
  };
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function initState (vm) {
  vm._watchers = [];
  var opts = vm.$options;
  if (opts.props) { initProps(vm, opts.props); }
  if (opts.methods) { initMethods(vm, opts.methods); }
  if (opts.data) {
    initData(vm);
  } else {
    observe(vm._data = {}, true /* asRootData */);
  }
  if (opts.computed) { initComputed(vm, opts.computed); }
  if (opts.watch && opts.watch !== nativeWatch) {
    initWatch(vm, opts.watch);
  }
}

function initProps (vm, propsOptions) {
  var propsData = vm.$options.propsData || {};
  var props = vm._props = {};
  // cache prop keys so that future props updates can iterate using Array
  // instead of dynamic object key enumeration.
  var keys = vm.$options._propKeys = [];
  var isRoot = !vm.$parent;
  // root instance props should be converted
  if (!isRoot) {
    toggleObserving(false);
  }
  var loop = function ( key ) {
    keys.push(key);
    var value = validateProp(key, propsOptions, propsData, vm);
    /* istanbul ignore else */
    {
      var hyphenatedKey = hyphenate(key);
      if (isReservedAttribute(hyphenatedKey) ||
          config.isReservedAttr(hyphenatedKey)) {
        warn(
          ("\"" + hyphenatedKey + "\" is a reserved attribute and cannot be used as component prop."),
          vm
        );
      }
      defineReactive$$1(props, key, value, function () {
        if (!isRoot && !isUpdatingChildComponent) {
          warn(
            "Avoid mutating a prop directly since the value will be " +
            "overwritten whenever the parent component re-renders. " +
            "Instead, use a data or computed property based on the prop's " +
            "value. Prop being mutated: \"" + key + "\"",
            vm
          );
        }
      });
    }
    // static props are already proxied on the component's prototype
    // during Vue.extend(). We only need to proxy props defined at
    // instantiation here.
    if (!(key in vm)) {
      proxy(vm, "_props", key);
    }
  };

  for (var key in propsOptions) loop( key );
  toggleObserving(true);
}

function initData (vm) {
  var data = vm.$options.data;
  data = vm._data = typeof data === 'function'
    ? getData(data, vm)
    : data || {};
  if (!isPlainObject(data)) {
    data = {};
    warn(
      'data functions should return an object:\n' +
      'https://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function',
      vm
    );
  }
  // proxy data on instance
  var keys = Object.keys(data);
  var props = vm.$options.props;
  var methods = vm.$options.methods;
  var i = keys.length;
  while (i--) {
    var key = keys[i];
    {
      if (methods && hasOwn(methods, key)) {
        warn(
          ("Method \"" + key + "\" has already been defined as a data property."),
          vm
        );
      }
    }
    if (props && hasOwn(props, key)) {
      warn(
        "The data property \"" + key + "\" is already declared as a prop. " +
        "Use prop default value instead.",
        vm
      );
    } else if (!isReserved(key)) {
      proxy(vm, "_data", key);
    }
  }
  // observe data
  observe(data, true /* asRootData */);
}

function getData (data, vm) {
  // #7573 disable dep collection when invoking data getters
  pushTarget();
  try {
    return data.call(vm, vm)
  } catch (e) {
    handleError(e, vm, "data()");
    return {}
  } finally {
    popTarget();
  }
}

var computedWatcherOptions = { lazy: true };

function initComputed (vm, computed) {
  // $flow-disable-line
  var watchers = vm._computedWatchers = Object.create(null);
  // computed properties are just getters during SSR
  var isSSR = isServerRendering();

  for (var key in computed) {
    var userDef = computed[key];
    var getter = typeof userDef === 'function' ? userDef : userDef.get;
    if (getter == null) {
      warn(
        ("Getter is missing for computed property \"" + key + "\"."),
        vm
      );
    }

    if (!isSSR) {
      // create internal watcher for the computed property.
      watchers[key] = new Watcher(
        vm,
        getter || noop,
        noop,
        computedWatcherOptions
      );
    }

    // component-defined computed properties are already defined on the
    // component prototype. We only need to define computed properties defined
    // at instantiation here.
    if (!(key in vm)) {
      defineComputed(vm, key, userDef);
    } else {
      if (key in vm.$data) {
        warn(("The computed property \"" + key + "\" is already defined in data."), vm);
      } else if (vm.$options.props && key in vm.$options.props) {
        warn(("The computed property \"" + key + "\" is already defined as a prop."), vm);
      }
    }
  }
}

function defineComputed (
  target,
  key,
  userDef
) {
  var shouldCache = !isServerRendering();
  if (typeof userDef === 'function') {
    sharedPropertyDefinition.get = shouldCache
      ? createComputedGetter(key)
      : createGetterInvoker(userDef);
    sharedPropertyDefinition.set = noop;
  } else {
    sharedPropertyDefinition.get = userDef.get
      ? shouldCache && userDef.cache !== false
        ? createComputedGetter(key)
        : createGetterInvoker(userDef.get)
      : noop;
    sharedPropertyDefinition.set = userDef.set || noop;
  }
  if (sharedPropertyDefinition.set === noop) {
    sharedPropertyDefinition.set = function () {
      warn(
        ("Computed property \"" + key + "\" was assigned to but it has no setter."),
        this
      );
    };
  }
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function createComputedGetter (key) {
  return function computedGetter () {
    var watcher = this._computedWatchers && this._computedWatchers[key];
    if (watcher) {
      if (watcher.dirty) {
        watcher.evaluate();
      }
      if (Dep.target) {
        watcher.depend();
      }
      return watcher.value
    }
  }
}

function createGetterInvoker(fn) {
  return function computedGetter () {
    return fn.call(this, this)
  }
}

function initMethods (vm, methods) {
  var props = vm.$options.props;
  for (var key in methods) {
    {
      if (typeof methods[key] !== 'function') {
        warn(
          "Method \"" + key + "\" has type \"" + (typeof methods[key]) + "\" in the component definition. " +
          "Did you reference the function correctly?",
          vm
        );
      }
      if (props && hasOwn(props, key)) {
        warn(
          ("Method \"" + key + "\" has already been defined as a prop."),
          vm
        );
      }
      if ((key in vm) && isReserved(key)) {
        warn(
          "Method \"" + key + "\" conflicts with an existing Vue instance method. " +
          "Avoid defining component methods that start with _ or $."
        );
      }
    }
    vm[key] = typeof methods[key] !== 'function' ? noop : bind(methods[key], vm);
  }
}

function initWatch (vm, watch) {
  for (var key in watch) {
    var handler = watch[key];
    if (Array.isArray(handler)) {
      for (var i = 0; i < handler.length; i++) {
        createWatcher(vm, key, handler[i]);
      }
    } else {
      createWatcher(vm, key, handler);
    }
  }
}

function createWatcher (
  vm,
  expOrFn,
  handler,
  options
) {
  if (isPlainObject(handler)) {
    options = handler;
    handler = handler.handler;
  }
  if (typeof handler === 'string') {
    handler = vm[handler];
  }
  return vm.$watch(expOrFn, handler, options)
}

function stateMixin (Vue) {
  // flow somehow has problems with directly declared definition object
  // when using Object.defineProperty, so we have to procedurally build up
  // the object here.
  var dataDef = {};
  dataDef.get = function () { return this._data };
  var propsDef = {};
  propsDef.get = function () { return this._props };
  {
    dataDef.set = function () {
      warn(
        'Avoid replacing instance root $data. ' +
        'Use nested data properties instead.',
        this
      );
    };
    propsDef.set = function () {
      warn("$props is readonly.", this);
    };
  }
  Object.defineProperty(Vue.prototype, '$data', dataDef);
  Object.defineProperty(Vue.prototype, '$props', propsDef);

  Vue.prototype.$set = set;
  Vue.prototype.$delete = del;

  Vue.prototype.$watch = function (
    expOrFn,
    cb,
    options
  ) {
    var vm = this;
    if (isPlainObject(cb)) {
      return createWatcher(vm, expOrFn, cb, options)
    }
    options = options || {};
    options.user = true;
    var watcher = new Watcher(vm, expOrFn, cb, options);
    if (options.immediate) {
      try {
        cb.call(vm, watcher.value);
      } catch (error) {
        handleError(error, vm, ("callback for immediate watcher \"" + (watcher.expression) + "\""));
      }
    }
    return function unwatchFn () {
      watcher.teardown();
    }
  };
}

/*  */

var uid$3 = 0;

function initMixin (Vue) {
  Vue.prototype._init = function (options) {
    var vm = this;
    // a uid
    vm._uid = uid$3++;

    var startTag, endTag;
    /* istanbul ignore if */
    if (config.performance && mark) {
      startTag = "vue-perf-start:" + (vm._uid);
      endTag = "vue-perf-end:" + (vm._uid);
      mark(startTag);
    }

    // a flag to avoid this being observed
    vm._isVue = true;
    // merge options
    if (options && options._isComponent) {
      // optimize internal component instantiation
      // since dynamic options merging is pretty slow, and none of the
      // internal component options needs special treatment.
      initInternalComponent(vm, options);
    } else {
      vm.$options = mergeOptions(
        resolveConstructorOptions(vm.constructor),
        options || {},
        vm
      );
    }
    /* istanbul ignore else */
    {
      initProxy(vm);
    }
    // expose real self
    vm._self = vm;
    initLifecycle(vm);
    initEvents(vm);
    initRender(vm);
    callHook(vm, 'beforeCreate');
    initInjections(vm); // resolve injections before data/props
    initState(vm);
    initProvide(vm); // resolve provide after data/props
    callHook(vm, 'created');

    /* istanbul ignore if */
    if (config.performance && mark) {
      vm._name = formatComponentName(vm, false);
      mark(endTag);
      measure(("vue " + (vm._name) + " init"), startTag, endTag);
    }

    if (vm.$options.el) {
      vm.$mount(vm.$options.el);
    }
  };
}

function initInternalComponent (vm, options) {
  var opts = vm.$options = Object.create(vm.constructor.options);
  // doing this because it's faster than dynamic enumeration.
  var parentVnode = options._parentVnode;
  opts.parent = options.parent;
  opts._parentVnode = parentVnode;

  var vnodeComponentOptions = parentVnode.componentOptions;
  opts.propsData = vnodeComponentOptions.propsData;
  opts._parentListeners = vnodeComponentOptions.listeners;
  opts._renderChildren = vnodeComponentOptions.children;
  opts._componentTag = vnodeComponentOptions.tag;

  if (options.render) {
    opts.render = options.render;
    opts.staticRenderFns = options.staticRenderFns;
  }
}

function resolveConstructorOptions (Ctor) {
  var options = Ctor.options;
  if (Ctor.super) {
    var superOptions = resolveConstructorOptions(Ctor.super);
    var cachedSuperOptions = Ctor.superOptions;
    if (superOptions !== cachedSuperOptions) {
      // super option changed,
      // need to resolve new options.
      Ctor.superOptions = superOptions;
      // check if there are any late-modified/attached options (#4976)
      var modifiedOptions = resolveModifiedOptions(Ctor);
      // update base extend options
      if (modifiedOptions) {
        extend(Ctor.extendOptions, modifiedOptions);
      }
      options = Ctor.options = mergeOptions(superOptions, Ctor.extendOptions);
      if (options.name) {
        options.components[options.name] = Ctor;
      }
    }
  }
  return options
}

function resolveModifiedOptions (Ctor) {
  var modified;
  var latest = Ctor.options;
  var sealed = Ctor.sealedOptions;
  for (var key in latest) {
    if (latest[key] !== sealed[key]) {
      if (!modified) { modified = {}; }
      modified[key] = latest[key];
    }
  }
  return modified
}

function Vue (options) {
  if (!(this instanceof Vue)
  ) {
    warn('Vue is a constructor and should be called with the `new` keyword');
  }
  this._init(options);
}

initMixin(Vue);
stateMixin(Vue);
eventsMixin(Vue);
lifecycleMixin(Vue);
renderMixin(Vue);

/*  */

function initUse (Vue) {
  Vue.use = function (plugin) {
    var installedPlugins = (this._installedPlugins || (this._installedPlugins = []));
    if (installedPlugins.indexOf(plugin) > -1) {
      return this
    }

    // additional parameters
    var args = toArray(arguments, 1);
    args.unshift(this);
    if (typeof plugin.install === 'function') {
      plugin.install.apply(plugin, args);
    } else if (typeof plugin === 'function') {
      plugin.apply(null, args);
    }
    installedPlugins.push(plugin);
    return this
  };
}

/*  */

function initMixin$1 (Vue) {
  Vue.mixin = function (mixin) {
    this.options = mergeOptions(this.options, mixin);
    return this
  };
}

/*  */

function initExtend (Vue) {
  /**
   * Each instance constructor, including Vue, has a unique
   * cid. This enables us to create wrapped "child
   * constructors" for prototypal inheritance and cache them.
   */
  Vue.cid = 0;
  var cid = 1;

  /**
   * Class inheritance
   */
  Vue.extend = function (extendOptions) {
    extendOptions = extendOptions || {};
    var Super = this;
    var SuperId = Super.cid;
    var cachedCtors = extendOptions._Ctor || (extendOptions._Ctor = {});
    if (cachedCtors[SuperId]) {
      return cachedCtors[SuperId]
    }

    var name = extendOptions.name || Super.options.name;
    if (name) {
      validateComponentName(name);
    }

    var Sub = function VueComponent (options) {
      this._init(options);
    };
    Sub.prototype = Object.create(Super.prototype);
    Sub.prototype.constructor = Sub;
    Sub.cid = cid++;
    Sub.options = mergeOptions(
      Super.options,
      extendOptions
    );
    Sub['super'] = Super;

    // For props and computed properties, we define the proxy getters on
    // the Vue instances at extension time, on the extended prototype. This
    // avoids Object.defineProperty calls for each instance created.
    if (Sub.options.props) {
      initProps$1(Sub);
    }
    if (Sub.options.computed) {
      initComputed$1(Sub);
    }

    // allow further extension/mixin/plugin usage
    Sub.extend = Super.extend;
    Sub.mixin = Super.mixin;
    Sub.use = Super.use;

    // create asset registers, so extended classes
    // can have their private assets too.
    ASSET_TYPES.forEach(function (type) {
      Sub[type] = Super[type];
    });
    // enable recursive self-lookup
    if (name) {
      Sub.options.components[name] = Sub;
    }

    // keep a reference to the super options at extension time.
    // later at instantiation we can check if Super's options have
    // been updated.
    Sub.superOptions = Super.options;
    Sub.extendOptions = extendOptions;
    Sub.sealedOptions = extend({}, Sub.options);

    // cache constructor
    cachedCtors[SuperId] = Sub;
    return Sub
  };
}

function initProps$1 (Comp) {
  var props = Comp.options.props;
  for (var key in props) {
    proxy(Comp.prototype, "_props", key);
  }
}

function initComputed$1 (Comp) {
  var computed = Comp.options.computed;
  for (var key in computed) {
    defineComputed(Comp.prototype, key, computed[key]);
  }
}

/*  */

function initAssetRegisters (Vue) {
  /**
   * Create asset registration methods.
   */
  ASSET_TYPES.forEach(function (type) {
    Vue[type] = function (
      id,
      definition
    ) {
      if (!definition) {
        return this.options[type + 's'][id]
      } else {
        /* istanbul ignore if */
        if (type === 'component') {
          validateComponentName(id);
        }
        if (type === 'component' && isPlainObject(definition)) {
          definition.name = definition.name || id;
          definition = this.options._base.extend(definition);
        }
        if (type === 'directive' && typeof definition === 'function') {
          definition = { bind: definition, update: definition };
        }
        this.options[type + 's'][id] = definition;
        return definition
      }
    };
  });
}

/*  */



function getComponentName (opts) {
  return opts && (opts.Ctor.options.name || opts.tag)
}

function matches (pattern, name) {
  if (Array.isArray(pattern)) {
    return pattern.indexOf(name) > -1
  } else if (typeof pattern === 'string') {
    return pattern.split(',').indexOf(name) > -1
  } else if (isRegExp(pattern)) {
    return pattern.test(name)
  }
  /* istanbul ignore next */
  return false
}

function pruneCache (keepAliveInstance, filter) {
  var cache = keepAliveInstance.cache;
  var keys = keepAliveInstance.keys;
  var _vnode = keepAliveInstance._vnode;
  for (var key in cache) {
    var cachedNode = cache[key];
    if (cachedNode) {
      var name = getComponentName(cachedNode.componentOptions);
      if (name && !filter(name)) {
        pruneCacheEntry(cache, key, keys, _vnode);
      }
    }
  }
}

function pruneCacheEntry (
  cache,
  key,
  keys,
  current
) {
  var cached$$1 = cache[key];
  if (cached$$1 && (!current || cached$$1.tag !== current.tag)) {
    cached$$1.componentInstance.$destroy();
  }
  cache[key] = null;
  remove(keys, key);
}

var patternTypes = [String, RegExp, Array];

var KeepAlive = {
  name: 'keep-alive',
  abstract: true,

  props: {
    include: patternTypes,
    exclude: patternTypes,
    max: [String, Number]
  },

  created: function created () {
    this.cache = Object.create(null);
    this.keys = [];
  },

  destroyed: function destroyed () {
    for (var key in this.cache) {
      pruneCacheEntry(this.cache, key, this.keys);
    }
  },

  mounted: function mounted () {
    var this$1 = this;

    this.$watch('include', function (val) {
      pruneCache(this$1, function (name) { return matches(val, name); });
    });
    this.$watch('exclude', function (val) {
      pruneCache(this$1, function (name) { return !matches(val, name); });
    });
  },

  render: function render () {
    var slot = this.$slots.default;
    var vnode = getFirstComponentChild(slot);
    var componentOptions = vnode && vnode.componentOptions;
    if (componentOptions) {
      // check pattern
      var name = getComponentName(componentOptions);
      var ref = this;
      var include = ref.include;
      var exclude = ref.exclude;
      if (
        // not included
        (include && (!name || !matches(include, name))) ||
        // excluded
        (exclude && name && matches(exclude, name))
      ) {
        return vnode
      }

      var ref$1 = this;
      var cache = ref$1.cache;
      var keys = ref$1.keys;
      var key = vnode.key == null
        // same constructor may get registered as different local components
        // so cid alone is not enough (#3269)
        ? componentOptions.Ctor.cid + (componentOptions.tag ? ("::" + (componentOptions.tag)) : '')
        : vnode.key;
      if (cache[key]) {
        vnode.componentInstance = cache[key].componentInstance;
        // make current key freshest
        remove(keys, key);
        keys.push(key);
      } else {
        cache[key] = vnode;
        keys.push(key);
        // prune oldest entry
        if (this.max && keys.length > parseInt(this.max)) {
          pruneCacheEntry(cache, keys[0], keys, this._vnode);
        }
      }

      vnode.data.keepAlive = true;
    }
    return vnode || (slot && slot[0])
  }
};

var builtInComponents = {
  KeepAlive: KeepAlive
};

/*  */

function initGlobalAPI (Vue) {
  // config
  var configDef = {};
  configDef.get = function () { return config; };
  {
    configDef.set = function () {
      warn(
        'Do not replace the Vue.config object, set individual fields instead.'
      );
    };
  }
  Object.defineProperty(Vue, 'config', configDef);

  // exposed util methods.
  // NOTE: these are not considered part of the public API - avoid relying on
  // them unless you are aware of the risk.
  Vue.util = {
    warn: warn,
    extend: extend,
    mergeOptions: mergeOptions,
    defineReactive: defineReactive$$1
  };

  Vue.set = set;
  Vue.delete = del;
  Vue.nextTick = nextTick;

  // 2.6 explicit observable API
  Vue.observable = function (obj) {
    observe(obj);
    return obj
  };

  Vue.options = Object.create(null);
  ASSET_TYPES.forEach(function (type) {
    Vue.options[type + 's'] = Object.create(null);
  });

  // this is used to identify the "base" constructor to extend all plain-object
  // components with in Weex's multi-instance scenarios.
  Vue.options._base = Vue;

  extend(Vue.options.components, builtInComponents);

  initUse(Vue);
  initMixin$1(Vue);
  initExtend(Vue);
  initAssetRegisters(Vue);
}

initGlobalAPI(Vue);

Object.defineProperty(Vue.prototype, '$isServer', {
  get: isServerRendering
});

Object.defineProperty(Vue.prototype, '$ssrContext', {
  get: function get () {
    /* istanbul ignore next */
    return this.$vnode && this.$vnode.ssrContext
  }
});

// expose FunctionalRenderContext for ssr runtime helper installation
Object.defineProperty(Vue, 'FunctionalRenderContext', {
  value: FunctionalRenderContext
});

Vue.version = '2.6.10';

/*  */

// these are reserved for web because they are directly compiled away
// during template compilation
var isReservedAttr = makeMap('style,class');

// attributes that should be using props for binding
var acceptValue = makeMap('input,textarea,option,select,progress');
var mustUseProp = function (tag, type, attr) {
  return (
    (attr === 'value' && acceptValue(tag)) && type !== 'button' ||
    (attr === 'selected' && tag === 'option') ||
    (attr === 'checked' && tag === 'input') ||
    (attr === 'muted' && tag === 'video')
  )
};

var isEnumeratedAttr = makeMap('contenteditable,draggable,spellcheck');

var isValidContentEditableValue = makeMap('events,caret,typing,plaintext-only');

var convertEnumeratedValue = function (key, value) {
  return isFalsyAttrValue(value) || value === 'false'
    ? 'false'
    // allow arbitrary string value for contenteditable
    : key === 'contenteditable' && isValidContentEditableValue(value)
      ? value
      : 'true'
};

var isBooleanAttr = makeMap(
  'allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,' +
  'default,defaultchecked,defaultmuted,defaultselected,defer,disabled,' +
  'enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,' +
  'muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,' +
  'required,reversed,scoped,seamless,selected,sortable,translate,' +
  'truespeed,typemustmatch,visible'
);

var xlinkNS = 'http://www.w3.org/1999/xlink';

var isXlink = function (name) {
  return name.charAt(5) === ':' && name.slice(0, 5) === 'xlink'
};

var getXlinkProp = function (name) {
  return isXlink(name) ? name.slice(6, name.length) : ''
};

var isFalsyAttrValue = function (val) {
  return val == null || val === false
};

/*  */

function genClassForVnode (vnode) {
  var data = vnode.data;
  var parentNode = vnode;
  var childNode = vnode;
  while (isDef(childNode.componentInstance)) {
    childNode = childNode.componentInstance._vnode;
    if (childNode && childNode.data) {
      data = mergeClassData(childNode.data, data);
    }
  }
  while (isDef(parentNode = parentNode.parent)) {
    if (parentNode && parentNode.data) {
      data = mergeClassData(data, parentNode.data);
    }
  }
  return renderClass(data.staticClass, data.class)
}

function mergeClassData (child, parent) {
  return {
    staticClass: concat(child.staticClass, parent.staticClass),
    class: isDef(child.class)
      ? [child.class, parent.class]
      : parent.class
  }
}

function renderClass (
  staticClass,
  dynamicClass
) {
  if (isDef(staticClass) || isDef(dynamicClass)) {
    return concat(staticClass, stringifyClass(dynamicClass))
  }
  /* istanbul ignore next */
  return ''
}

function concat (a, b) {
  return a ? b ? (a + ' ' + b) : a : (b || '')
}

function stringifyClass (value) {
  if (Array.isArray(value)) {
    return stringifyArray(value)
  }
  if (isObject(value)) {
    return stringifyObject(value)
  }
  if (typeof value === 'string') {
    return value
  }
  /* istanbul ignore next */
  return ''
}

function stringifyArray (value) {
  var res = '';
  var stringified;
  for (var i = 0, l = value.length; i < l; i++) {
    if (isDef(stringified = stringifyClass(value[i])) && stringified !== '') {
      if (res) { res += ' '; }
      res += stringified;
    }
  }
  return res
}

function stringifyObject (value) {
  var res = '';
  for (var key in value) {
    if (value[key]) {
      if (res) { res += ' '; }
      res += key;
    }
  }
  return res
}

/*  */

var namespaceMap = {
  svg: 'http://www.w3.org/2000/svg',
  math: 'http://www.w3.org/1998/Math/MathML'
};

var isHTMLTag = makeMap(
  'html,body,base,head,link,meta,style,title,' +
  'address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,' +
  'div,dd,dl,dt,figcaption,figure,picture,hr,img,li,main,ol,p,pre,ul,' +
  'a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,' +
  's,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,' +
  'embed,object,param,source,canvas,script,noscript,del,ins,' +
  'caption,col,colgroup,table,thead,tbody,td,th,tr,' +
  'button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,' +
  'output,progress,select,textarea,' +
  'details,dialog,menu,menuitem,summary,' +
  'content,element,shadow,template,blockquote,iframe,tfoot'
);

// this map is intentionally selective, only covering SVG elements that may
// contain child elements.
var isSVG = makeMap(
  'svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,' +
  'foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,' +
  'polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view',
  true
);

var isPreTag = function (tag) { return tag === 'pre'; };

var isReservedTag = function (tag) {
  return isHTMLTag(tag) || isSVG(tag)
};

function getTagNamespace (tag) {
  if (isSVG(tag)) {
    return 'svg'
  }
  // basic support for MathML
  // note it doesn't support other MathML elements being component roots
  if (tag === 'math') {
    return 'math'
  }
}

var unknownElementCache = Object.create(null);
function isUnknownElement (tag) {
  /* istanbul ignore if */
  if (!inBrowser) {
    return true
  }
  if (isReservedTag(tag)) {
    return false
  }
  tag = tag.toLowerCase();
  /* istanbul ignore if */
  if (unknownElementCache[tag] != null) {
    return unknownElementCache[tag]
  }
  var el = document.createElement(tag);
  if (tag.indexOf('-') > -1) {
    // http://stackoverflow.com/a/28210364/1070244
    return (unknownElementCache[tag] = (
      el.constructor === window.HTMLUnknownElement ||
      el.constructor === window.HTMLElement
    ))
  } else {
    return (unknownElementCache[tag] = /HTMLUnknownElement/.test(el.toString()))
  }
}

var isTextInputType = makeMap('text,number,password,search,email,tel,url');

/*  */

/**
 * Query an element selector if it's not an element already.
 */
function query (el) {
  if (typeof el === 'string') {
    var selected = document.querySelector(el);
    if (!selected) {
      warn(
        'Cannot find element: ' + el
      );
      return document.createElement('div')
    }
    return selected
  } else {
    return el
  }
}

/*  */

function createElement$1 (tagName, vnode) {
  var elm = document.createElement(tagName);
  if (tagName !== 'select') {
    return elm
  }
  // false or null will remove the attribute but undefined will not
  if (vnode.data && vnode.data.attrs && vnode.data.attrs.multiple !== undefined) {
    elm.setAttribute('multiple', 'multiple');
  }
  return elm
}

function createElementNS (namespace, tagName) {
  return document.createElementNS(namespaceMap[namespace], tagName)
}

function createTextNode (text) {
  return document.createTextNode(text)
}

function createComment (text) {
  return document.createComment(text)
}

function insertBefore (parentNode, newNode, referenceNode) {
  parentNode.insertBefore(newNode, referenceNode);
}

function removeChild (node, child) {
  node.removeChild(child);
}

function appendChild (node, child) {
  node.appendChild(child);
}

function parentNode (node) {
  return node.parentNode
}

function nextSibling (node) {
  return node.nextSibling
}

function tagName (node) {
  return node.tagName
}

function setTextContent (node, text) {
  node.textContent = text;
}

function setStyleScope (node, scopeId) {
  node.setAttribute(scopeId, '');
}

var nodeOps = /*#__PURE__*/Object.freeze({
  createElement: createElement$1,
  createElementNS: createElementNS,
  createTextNode: createTextNode,
  createComment: createComment,
  insertBefore: insertBefore,
  removeChild: removeChild,
  appendChild: appendChild,
  parentNode: parentNode,
  nextSibling: nextSibling,
  tagName: tagName,
  setTextContent: setTextContent,
  setStyleScope: setStyleScope
});

/*  */

var ref = {
  create: function create (_, vnode) {
    registerRef(vnode);
  },
  update: function update (oldVnode, vnode) {
    if (oldVnode.data.ref !== vnode.data.ref) {
      registerRef(oldVnode, true);
      registerRef(vnode);
    }
  },
  destroy: function destroy (vnode) {
    registerRef(vnode, true);
  }
};

function registerRef (vnode, isRemoval) {
  var key = vnode.data.ref;
  if (!isDef(key)) { return }

  var vm = vnode.context;
  var ref = vnode.componentInstance || vnode.elm;
  var refs = vm.$refs;
  if (isRemoval) {
    if (Array.isArray(refs[key])) {
      remove(refs[key], ref);
    } else if (refs[key] === ref) {
      refs[key] = undefined;
    }
  } else {
    if (vnode.data.refInFor) {
      if (!Array.isArray(refs[key])) {
        refs[key] = [ref];
      } else if (refs[key].indexOf(ref) < 0) {
        // $flow-disable-line
        refs[key].push(ref);
      }
    } else {
      refs[key] = ref;
    }
  }
}

/**
 * Virtual DOM patching algorithm based on Snabbdom by
 * Simon Friis Vindum (@paldepind)
 * Licensed under the MIT License
 * https://github.com/paldepind/snabbdom/blob/master/LICENSE
 *
 * modified by Evan You (@yyx990803)
 *
 * Not type-checking this because this file is perf-critical and the cost
 * of making flow understand it is not worth it.
 */

var emptyNode = new VNode('', {}, []);

var hooks = ['create', 'activate', 'update', 'remove', 'destroy'];

function sameVnode (a, b) {
  return (
    a.key === b.key && (
      (
        a.tag === b.tag &&
        a.isComment === b.isComment &&
        isDef(a.data) === isDef(b.data) &&
        sameInputType(a, b)
      ) || (
        isTrue(a.isAsyncPlaceholder) &&
        a.asyncFactory === b.asyncFactory &&
        isUndef(b.asyncFactory.error)
      )
    )
  )
}

function sameInputType (a, b) {
  if (a.tag !== 'input') { return true }
  var i;
  var typeA = isDef(i = a.data) && isDef(i = i.attrs) && i.type;
  var typeB = isDef(i = b.data) && isDef(i = i.attrs) && i.type;
  return typeA === typeB || isTextInputType(typeA) && isTextInputType(typeB)
}

function createKeyToOldIdx (children, beginIdx, endIdx) {
  var i, key;
  var map = {};
  for (i = beginIdx; i <= endIdx; ++i) {
    key = children[i].key;
    if (isDef(key)) { map[key] = i; }
  }
  return map
}

function createPatchFunction (backend) {
  var i, j;
  var cbs = {};

  var modules = backend.modules;
  var nodeOps = backend.nodeOps;

  for (i = 0; i < hooks.length; ++i) {
    cbs[hooks[i]] = [];
    for (j = 0; j < modules.length; ++j) {
      if (isDef(modules[j][hooks[i]])) {
        cbs[hooks[i]].push(modules[j][hooks[i]]);
      }
    }
  }

  function emptyNodeAt (elm) {
    return new VNode(nodeOps.tagName(elm).toLowerCase(), {}, [], undefined, elm)
  }

  function createRmCb (childElm, listeners) {
    function remove$$1 () {
      if (--remove$$1.listeners === 0) {
        removeNode(childElm);
      }
    }
    remove$$1.listeners = listeners;
    return remove$$1
  }

  function removeNode (el) {
    var parent = nodeOps.parentNode(el);
    // element may have already been removed due to v-html / v-text
    if (isDef(parent)) {
      nodeOps.removeChild(parent, el);
    }
  }

  function isUnknownElement$$1 (vnode, inVPre) {
    return (
      !inVPre &&
      !vnode.ns &&
      !(
        config.ignoredElements.length &&
        config.ignoredElements.some(function (ignore) {
          return isRegExp(ignore)
            ? ignore.test(vnode.tag)
            : ignore === vnode.tag
        })
      ) &&
      config.isUnknownElement(vnode.tag)
    )
  }

  var creatingElmInVPre = 0;

  function createElm (
    vnode,
    insertedVnodeQueue,
    parentElm,
    refElm,
    nested,
    ownerArray,
    index
  ) {
    if (isDef(vnode.elm) && isDef(ownerArray)) {
      // This vnode was used in a previous render!
      // now it's used as a new node, overwriting its elm would cause
      // potential patch errors down the road when it's used as an insertion
      // reference node. Instead, we clone the node on-demand before creating
      // associated DOM element for it.
      vnode = ownerArray[index] = cloneVNode(vnode);
    }

    vnode.isRootInsert = !nested; // for transition enter check
    if (createComponent(vnode, insertedVnodeQueue, parentElm, refElm)) {
      return
    }

    var data = vnode.data;
    var children = vnode.children;
    var tag = vnode.tag;
    if (isDef(tag)) {
      {
        if (data && data.pre) {
          creatingElmInVPre++;
        }
        if (isUnknownElement$$1(vnode, creatingElmInVPre)) {
          warn(
            'Unknown custom element: <' + tag + '> - did you ' +
            'register the component correctly? For recursive components, ' +
            'make sure to provide the "name" option.',
            vnode.context
          );
        }
      }

      vnode.elm = vnode.ns
        ? nodeOps.createElementNS(vnode.ns, tag)
        : nodeOps.createElement(tag, vnode);
      setScope(vnode);

      /* istanbul ignore if */
      {
        createChildren(vnode, children, insertedVnodeQueue);
        if (isDef(data)) {
          invokeCreateHooks(vnode, insertedVnodeQueue);
        }
        insert(parentElm, vnode.elm, refElm);
      }

      if (data && data.pre) {
        creatingElmInVPre--;
      }
    } else if (isTrue(vnode.isComment)) {
      vnode.elm = nodeOps.createComment(vnode.text);
      insert(parentElm, vnode.elm, refElm);
    } else {
      vnode.elm = nodeOps.createTextNode(vnode.text);
      insert(parentElm, vnode.elm, refElm);
    }
  }

  function createComponent (vnode, insertedVnodeQueue, parentElm, refElm) {
    var i = vnode.data;
    if (isDef(i)) {
      var isReactivated = isDef(vnode.componentInstance) && i.keepAlive;
      if (isDef(i = i.hook) && isDef(i = i.init)) {
        i(vnode, false /* hydrating */);
      }
      // after calling the init hook, if the vnode is a child component
      // it should've created a child instance and mounted it. the child
      // component also has set the placeholder vnode's elm.
      // in that case we can just return the element and be done.
      if (isDef(vnode.componentInstance)) {
        initComponent(vnode, insertedVnodeQueue);
        insert(parentElm, vnode.elm, refElm);
        if (isTrue(isReactivated)) {
          reactivateComponent(vnode, insertedVnodeQueue, parentElm, refElm);
        }
        return true
      }
    }
  }

  function initComponent (vnode, insertedVnodeQueue) {
    if (isDef(vnode.data.pendingInsert)) {
      insertedVnodeQueue.push.apply(insertedVnodeQueue, vnode.data.pendingInsert);
      vnode.data.pendingInsert = null;
    }
    vnode.elm = vnode.componentInstance.$el;
    if (isPatchable(vnode)) {
      invokeCreateHooks(vnode, insertedVnodeQueue);
      setScope(vnode);
    } else {
      // empty component root.
      // skip all element-related modules except for ref (#3455)
      registerRef(vnode);
      // make sure to invoke the insert hook
      insertedVnodeQueue.push(vnode);
    }
  }

  function reactivateComponent (vnode, insertedVnodeQueue, parentElm, refElm) {
    var i;
    // hack for #4339: a reactivated component with inner transition
    // does not trigger because the inner node's created hooks are not called
    // again. It's not ideal to involve module-specific logic in here but
    // there doesn't seem to be a better way to do it.
    var innerNode = vnode;
    while (innerNode.componentInstance) {
      innerNode = innerNode.componentInstance._vnode;
      if (isDef(i = innerNode.data) && isDef(i = i.transition)) {
        for (i = 0; i < cbs.activate.length; ++i) {
          cbs.activate[i](emptyNode, innerNode);
        }
        insertedVnodeQueue.push(innerNode);
        break
      }
    }
    // unlike a newly created component,
    // a reactivated keep-alive component doesn't insert itself
    insert(parentElm, vnode.elm, refElm);
  }

  function insert (parent, elm, ref$$1) {
    if (isDef(parent)) {
      if (isDef(ref$$1)) {
        if (nodeOps.parentNode(ref$$1) === parent) {
          nodeOps.insertBefore(parent, elm, ref$$1);
        }
      } else {
        nodeOps.appendChild(parent, elm);
      }
    }
  }

  function createChildren (vnode, children, insertedVnodeQueue) {
    if (Array.isArray(children)) {
      {
        checkDuplicateKeys(children);
      }
      for (var i = 0; i < children.length; ++i) {
        createElm(children[i], insertedVnodeQueue, vnode.elm, null, true, children, i);
      }
    } else if (isPrimitive(vnode.text)) {
      nodeOps.appendChild(vnode.elm, nodeOps.createTextNode(String(vnode.text)));
    }
  }

  function isPatchable (vnode) {
    while (vnode.componentInstance) {
      vnode = vnode.componentInstance._vnode;
    }
    return isDef(vnode.tag)
  }

  function invokeCreateHooks (vnode, insertedVnodeQueue) {
    for (var i$1 = 0; i$1 < cbs.create.length; ++i$1) {
      cbs.create[i$1](emptyNode, vnode);
    }
    i = vnode.data.hook; // Reuse variable
    if (isDef(i)) {
      if (isDef(i.create)) { i.create(emptyNode, vnode); }
      if (isDef(i.insert)) { insertedVnodeQueue.push(vnode); }
    }
  }

  // set scope id attribute for scoped CSS.
  // this is implemented as a special case to avoid the overhead
  // of going through the normal attribute patching process.
  function setScope (vnode) {
    var i;
    if (isDef(i = vnode.fnScopeId)) {
      nodeOps.setStyleScope(vnode.elm, i);
    } else {
      var ancestor = vnode;
      while (ancestor) {
        if (isDef(i = ancestor.context) && isDef(i = i.$options._scopeId)) {
          nodeOps.setStyleScope(vnode.elm, i);
        }
        ancestor = ancestor.parent;
      }
    }
    // for slot content they should also get the scopeId from the host instance.
    if (isDef(i = activeInstance) &&
      i !== vnode.context &&
      i !== vnode.fnContext &&
      isDef(i = i.$options._scopeId)
    ) {
      nodeOps.setStyleScope(vnode.elm, i);
    }
  }

  function addVnodes (parentElm, refElm, vnodes, startIdx, endIdx, insertedVnodeQueue) {
    for (; startIdx <= endIdx; ++startIdx) {
      createElm(vnodes[startIdx], insertedVnodeQueue, parentElm, refElm, false, vnodes, startIdx);
    }
  }

  function invokeDestroyHook (vnode) {
    var i, j;
    var data = vnode.data;
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.destroy)) { i(vnode); }
      for (i = 0; i < cbs.destroy.length; ++i) { cbs.destroy[i](vnode); }
    }
    if (isDef(i = vnode.children)) {
      for (j = 0; j < vnode.children.length; ++j) {
        invokeDestroyHook(vnode.children[j]);
      }
    }
  }

  function removeVnodes (parentElm, vnodes, startIdx, endIdx) {
    for (; startIdx <= endIdx; ++startIdx) {
      var ch = vnodes[startIdx];
      if (isDef(ch)) {
        if (isDef(ch.tag)) {
          removeAndInvokeRemoveHook(ch);
          invokeDestroyHook(ch);
        } else { // Text node
          removeNode(ch.elm);
        }
      }
    }
  }

  function removeAndInvokeRemoveHook (vnode, rm) {
    if (isDef(rm) || isDef(vnode.data)) {
      var i;
      var listeners = cbs.remove.length + 1;
      if (isDef(rm)) {
        // we have a recursively passed down rm callback
        // increase the listeners count
        rm.listeners += listeners;
      } else {
        // directly removing
        rm = createRmCb(vnode.elm, listeners);
      }
      // recursively invoke hooks on child component root node
      if (isDef(i = vnode.componentInstance) && isDef(i = i._vnode) && isDef(i.data)) {
        removeAndInvokeRemoveHook(i, rm);
      }
      for (i = 0; i < cbs.remove.length; ++i) {
        cbs.remove[i](vnode, rm);
      }
      if (isDef(i = vnode.data.hook) && isDef(i = i.remove)) {
        i(vnode, rm);
      } else {
        rm();
      }
    } else {
      removeNode(vnode.elm);
    }
  }

  function updateChildren (parentElm, oldCh, newCh, insertedVnodeQueue, removeOnly) {
    var oldStartIdx = 0;
    var newStartIdx = 0;
    var oldEndIdx = oldCh.length - 1;
    var oldStartVnode = oldCh[0];
    var oldEndVnode = oldCh[oldEndIdx];
    var newEndIdx = newCh.length - 1;
    var newStartVnode = newCh[0];
    var newEndVnode = newCh[newEndIdx];
    var oldKeyToIdx, idxInOld, vnodeToMove, refElm;

    // removeOnly is a special flag used only by <transition-group>
    // to ensure removed elements stay in correct relative positions
    // during leaving transitions
    var canMove = !removeOnly;

    {
      checkDuplicateKeys(newCh);
    }

    while (oldStartIdx <= oldEndIdx && newStartIdx <= newEndIdx) {
      if (isUndef(oldStartVnode)) {
        oldStartVnode = oldCh[++oldStartIdx]; // Vnode has been moved left
      } else if (isUndef(oldEndVnode)) {
        oldEndVnode = oldCh[--oldEndIdx];
      } else if (sameVnode(oldStartVnode, newStartVnode)) {
        patchVnode(oldStartVnode, newStartVnode, insertedVnodeQueue, newCh, newStartIdx);
        oldStartVnode = oldCh[++oldStartIdx];
        newStartVnode = newCh[++newStartIdx];
      } else if (sameVnode(oldEndVnode, newEndVnode)) {
        patchVnode(oldEndVnode, newEndVnode, insertedVnodeQueue, newCh, newEndIdx);
        oldEndVnode = oldCh[--oldEndIdx];
        newEndVnode = newCh[--newEndIdx];
      } else if (sameVnode(oldStartVnode, newEndVnode)) { // Vnode moved right
        patchVnode(oldStartVnode, newEndVnode, insertedVnodeQueue, newCh, newEndIdx);
        canMove && nodeOps.insertBefore(parentElm, oldStartVnode.elm, nodeOps.nextSibling(oldEndVnode.elm));
        oldStartVnode = oldCh[++oldStartIdx];
        newEndVnode = newCh[--newEndIdx];
      } else if (sameVnode(oldEndVnode, newStartVnode)) { // Vnode moved left
        patchVnode(oldEndVnode, newStartVnode, insertedVnodeQueue, newCh, newStartIdx);
        canMove && nodeOps.insertBefore(parentElm, oldEndVnode.elm, oldStartVnode.elm);
        oldEndVnode = oldCh[--oldEndIdx];
        newStartVnode = newCh[++newStartIdx];
      } else {
        if (isUndef(oldKeyToIdx)) { oldKeyToIdx = createKeyToOldIdx(oldCh, oldStartIdx, oldEndIdx); }
        idxInOld = isDef(newStartVnode.key)
          ? oldKeyToIdx[newStartVnode.key]
          : findIdxInOld(newStartVnode, oldCh, oldStartIdx, oldEndIdx);
        if (isUndef(idxInOld)) { // New element
          createElm(newStartVnode, insertedVnodeQueue, parentElm, oldStartVnode.elm, false, newCh, newStartIdx);
        } else {
          vnodeToMove = oldCh[idxInOld];
          if (sameVnode(vnodeToMove, newStartVnode)) {
            patchVnode(vnodeToMove, newStartVnode, insertedVnodeQueue, newCh, newStartIdx);
            oldCh[idxInOld] = undefined;
            canMove && nodeOps.insertBefore(parentElm, vnodeToMove.elm, oldStartVnode.elm);
          } else {
            // same key but different element. treat as new element
            createElm(newStartVnode, insertedVnodeQueue, parentElm, oldStartVnode.elm, false, newCh, newStartIdx);
          }
        }
        newStartVnode = newCh[++newStartIdx];
      }
    }
    if (oldStartIdx > oldEndIdx) {
      refElm = isUndef(newCh[newEndIdx + 1]) ? null : newCh[newEndIdx + 1].elm;
      addVnodes(parentElm, refElm, newCh, newStartIdx, newEndIdx, insertedVnodeQueue);
    } else if (newStartIdx > newEndIdx) {
      removeVnodes(parentElm, oldCh, oldStartIdx, oldEndIdx);
    }
  }

  function checkDuplicateKeys (children) {
    var seenKeys = {};
    for (var i = 0; i < children.length; i++) {
      var vnode = children[i];
      var key = vnode.key;
      if (isDef(key)) {
        if (seenKeys[key]) {
          warn(
            ("Duplicate keys detected: '" + key + "'. This may cause an update error."),
            vnode.context
          );
        } else {
          seenKeys[key] = true;
        }
      }
    }
  }

  function findIdxInOld (node, oldCh, start, end) {
    for (var i = start; i < end; i++) {
      var c = oldCh[i];
      if (isDef(c) && sameVnode(node, c)) { return i }
    }
  }

  function patchVnode (
    oldVnode,
    vnode,
    insertedVnodeQueue,
    ownerArray,
    index,
    removeOnly
  ) {
    if (oldVnode === vnode) {
      return
    }

    if (isDef(vnode.elm) && isDef(ownerArray)) {
      // clone reused vnode
      vnode = ownerArray[index] = cloneVNode(vnode);
    }

    var elm = vnode.elm = oldVnode.elm;

    if (isTrue(oldVnode.isAsyncPlaceholder)) {
      if (isDef(vnode.asyncFactory.resolved)) {
        hydrate(oldVnode.elm, vnode, insertedVnodeQueue);
      } else {
        vnode.isAsyncPlaceholder = true;
      }
      return
    }

    // reuse element for static trees.
    // note we only do this if the vnode is cloned -
    // if the new node is not cloned it means the render functions have been
    // reset by the hot-reload-api and we need to do a proper re-render.
    if (isTrue(vnode.isStatic) &&
      isTrue(oldVnode.isStatic) &&
      vnode.key === oldVnode.key &&
      (isTrue(vnode.isCloned) || isTrue(vnode.isOnce))
    ) {
      vnode.componentInstance = oldVnode.componentInstance;
      return
    }

    var i;
    var data = vnode.data;
    if (isDef(data) && isDef(i = data.hook) && isDef(i = i.prepatch)) {
      i(oldVnode, vnode);
    }

    var oldCh = oldVnode.children;
    var ch = vnode.children;
    if (isDef(data) && isPatchable(vnode)) {
      for (i = 0; i < cbs.update.length; ++i) { cbs.update[i](oldVnode, vnode); }
      if (isDef(i = data.hook) && isDef(i = i.update)) { i(oldVnode, vnode); }
    }
    if (isUndef(vnode.text)) {
      if (isDef(oldCh) && isDef(ch)) {
        if (oldCh !== ch) { updateChildren(elm, oldCh, ch, insertedVnodeQueue, removeOnly); }
      } else if (isDef(ch)) {
        {
          checkDuplicateKeys(ch);
        }
        if (isDef(oldVnode.text)) { nodeOps.setTextContent(elm, ''); }
        addVnodes(elm, null, ch, 0, ch.length - 1, insertedVnodeQueue);
      } else if (isDef(oldCh)) {
        removeVnodes(elm, oldCh, 0, oldCh.length - 1);
      } else if (isDef(oldVnode.text)) {
        nodeOps.setTextContent(elm, '');
      }
    } else if (oldVnode.text !== vnode.text) {
      nodeOps.setTextContent(elm, vnode.text);
    }
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.postpatch)) { i(oldVnode, vnode); }
    }
  }

  function invokeInsertHook (vnode, queue, initial) {
    // delay insert hooks for component root nodes, invoke them after the
    // element is really inserted
    if (isTrue(initial) && isDef(vnode.parent)) {
      vnode.parent.data.pendingInsert = queue;
    } else {
      for (var i = 0; i < queue.length; ++i) {
        queue[i].data.hook.insert(queue[i]);
      }
    }
  }

  var hydrationBailed = false;
  // list of modules that can skip create hook during hydration because they
  // are already rendered on the client or has no need for initialization
  // Note: style is excluded because it relies on initial clone for future
  // deep updates (#7063).
  var isRenderedModule = makeMap('attrs,class,staticClass,staticStyle,key');

  // Note: this is a browser-only function so we can assume elms are DOM nodes.
  function hydrate (elm, vnode, insertedVnodeQueue, inVPre) {
    var i;
    var tag = vnode.tag;
    var data = vnode.data;
    var children = vnode.children;
    inVPre = inVPre || (data && data.pre);
    vnode.elm = elm;

    if (isTrue(vnode.isComment) && isDef(vnode.asyncFactory)) {
      vnode.isAsyncPlaceholder = true;
      return true
    }
    // assert node match
    {
      if (!assertNodeMatch(elm, vnode, inVPre)) {
        return false
      }
    }
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.init)) { i(vnode, true /* hydrating */); }
      if (isDef(i = vnode.componentInstance)) {
        // child component. it should have hydrated its own tree.
        initComponent(vnode, insertedVnodeQueue);
        return true
      }
    }
    if (isDef(tag)) {
      if (isDef(children)) {
        // empty element, allow client to pick up and populate children
        if (!elm.hasChildNodes()) {
          createChildren(vnode, children, insertedVnodeQueue);
        } else {
          // v-html and domProps: innerHTML
          if (isDef(i = data) && isDef(i = i.domProps) && isDef(i = i.innerHTML)) {
            if (i !== elm.innerHTML) {
              /* istanbul ignore if */
              if (typeof console !== 'undefined' &&
                !hydrationBailed
              ) {
                hydrationBailed = true;
                console.warn('Parent: ', elm);
                console.warn('server innerHTML: ', i);
                console.warn('client innerHTML: ', elm.innerHTML);
              }
              return false
            }
          } else {
            // iterate and compare children lists
            var childrenMatch = true;
            var childNode = elm.firstChild;
            for (var i$1 = 0; i$1 < children.length; i$1++) {
              if (!childNode || !hydrate(childNode, children[i$1], insertedVnodeQueue, inVPre)) {
                childrenMatch = false;
                break
              }
              childNode = childNode.nextSibling;
            }
            // if childNode is not null, it means the actual childNodes list is
            // longer than the virtual children list.
            if (!childrenMatch || childNode) {
              /* istanbul ignore if */
              if (typeof console !== 'undefined' &&
                !hydrationBailed
              ) {
                hydrationBailed = true;
                console.warn('Parent: ', elm);
                console.warn('Mismatching childNodes vs. VNodes: ', elm.childNodes, children);
              }
              return false
            }
          }
        }
      }
      if (isDef(data)) {
        var fullInvoke = false;
        for (var key in data) {
          if (!isRenderedModule(key)) {
            fullInvoke = true;
            invokeCreateHooks(vnode, insertedVnodeQueue);
            break
          }
        }
        if (!fullInvoke && data['class']) {
          // ensure collecting deps for deep class bindings for future updates
          traverse(data['class']);
        }
      }
    } else if (elm.data !== vnode.text) {
      elm.data = vnode.text;
    }
    return true
  }

  function assertNodeMatch (node, vnode, inVPre) {
    if (isDef(vnode.tag)) {
      return vnode.tag.indexOf('vue-component') === 0 || (
        !isUnknownElement$$1(vnode, inVPre) &&
        vnode.tag.toLowerCase() === (node.tagName && node.tagName.toLowerCase())
      )
    } else {
      return node.nodeType === (vnode.isComment ? 8 : 3)
    }
  }

  return function patch (oldVnode, vnode, hydrating, removeOnly) {
    if (isUndef(vnode)) {
      if (isDef(oldVnode)) { invokeDestroyHook(oldVnode); }
      return
    }

    var isInitialPatch = false;
    var insertedVnodeQueue = [];

    if (isUndef(oldVnode)) {
      // empty mount (likely as component), create new root element
      isInitialPatch = true;
      createElm(vnode, insertedVnodeQueue);
    } else {
      var isRealElement = isDef(oldVnode.nodeType);
      if (!isRealElement && sameVnode(oldVnode, vnode)) {
        // patch existing root node
        patchVnode(oldVnode, vnode, insertedVnodeQueue, null, null, removeOnly);
      } else {
        if (isRealElement) {
          // mounting to a real element
          // check if this is server-rendered content and if we can perform
          // a successful hydration.
          if (oldVnode.nodeType === 1 && oldVnode.hasAttribute(SSR_ATTR)) {
            oldVnode.removeAttribute(SSR_ATTR);
            hydrating = true;
          }
          if (isTrue(hydrating)) {
            if (hydrate(oldVnode, vnode, insertedVnodeQueue)) {
              invokeInsertHook(vnode, insertedVnodeQueue, true);
              return oldVnode
            } else {
              warn(
                'The client-side rendered virtual DOM tree is not matching ' +
                'server-rendered content. This is likely caused by incorrect ' +
                'HTML markup, for example nesting block-level elements inside ' +
                '<p>, or missing <tbody>. Bailing hydration and performing ' +
                'full client-side render.'
              );
            }
          }
          // either not server-rendered, or hydration failed.
          // create an empty node and replace it
          oldVnode = emptyNodeAt(oldVnode);
        }

        // replacing existing element
        var oldElm = oldVnode.elm;
        var parentElm = nodeOps.parentNode(oldElm);

        // create new node
        createElm(
          vnode,
          insertedVnodeQueue,
          // extremely rare edge case: do not insert if old element is in a
          // leaving transition. Only happens when combining transition +
          // keep-alive + HOCs. (#4590)
          oldElm._leaveCb ? null : parentElm,
          nodeOps.nextSibling(oldElm)
        );

        // update parent placeholder node element, recursively
        if (isDef(vnode.parent)) {
          var ancestor = vnode.parent;
          var patchable = isPatchable(vnode);
          while (ancestor) {
            for (var i = 0; i < cbs.destroy.length; ++i) {
              cbs.destroy[i](ancestor);
            }
            ancestor.elm = vnode.elm;
            if (patchable) {
              for (var i$1 = 0; i$1 < cbs.create.length; ++i$1) {
                cbs.create[i$1](emptyNode, ancestor);
              }
              // #6513
              // invoke insert hooks that may have been merged by create hooks.
              // e.g. for directives that uses the "inserted" hook.
              var insert = ancestor.data.hook.insert;
              if (insert.merged) {
                // start at index 1 to avoid re-invoking component mounted hook
                for (var i$2 = 1; i$2 < insert.fns.length; i$2++) {
                  insert.fns[i$2]();
                }
              }
            } else {
              registerRef(ancestor);
            }
            ancestor = ancestor.parent;
          }
        }

        // destroy old node
        if (isDef(parentElm)) {
          removeVnodes(parentElm, [oldVnode], 0, 0);
        } else if (isDef(oldVnode.tag)) {
          invokeDestroyHook(oldVnode);
        }
      }
    }

    invokeInsertHook(vnode, insertedVnodeQueue, isInitialPatch);
    return vnode.elm
  }
}

/*  */

var directives = {
  create: updateDirectives,
  update: updateDirectives,
  destroy: function unbindDirectives (vnode) {
    updateDirectives(vnode, emptyNode);
  }
};

function updateDirectives (oldVnode, vnode) {
  if (oldVnode.data.directives || vnode.data.directives) {
    _update(oldVnode, vnode);
  }
}

function _update (oldVnode, vnode) {
  var isCreate = oldVnode === emptyNode;
  var isDestroy = vnode === emptyNode;
  var oldDirs = normalizeDirectives$1(oldVnode.data.directives, oldVnode.context);
  var newDirs = normalizeDirectives$1(vnode.data.directives, vnode.context);

  var dirsWithInsert = [];
  var dirsWithPostpatch = [];

  var key, oldDir, dir;
  for (key in newDirs) {
    oldDir = oldDirs[key];
    dir = newDirs[key];
    if (!oldDir) {
      // new directive, bind
      callHook$1(dir, 'bind', vnode, oldVnode);
      if (dir.def && dir.def.inserted) {
        dirsWithInsert.push(dir);
      }
    } else {
      // existing directive, update
      dir.oldValue = oldDir.value;
      dir.oldArg = oldDir.arg;
      callHook$1(dir, 'update', vnode, oldVnode);
      if (dir.def && dir.def.componentUpdated) {
        dirsWithPostpatch.push(dir);
      }
    }
  }

  if (dirsWithInsert.length) {
    var callInsert = function () {
      for (var i = 0; i < dirsWithInsert.length; i++) {
        callHook$1(dirsWithInsert[i], 'inserted', vnode, oldVnode);
      }
    };
    if (isCreate) {
      mergeVNodeHook(vnode, 'insert', callInsert);
    } else {
      callInsert();
    }
  }

  if (dirsWithPostpatch.length) {
    mergeVNodeHook(vnode, 'postpatch', function () {
      for (var i = 0; i < dirsWithPostpatch.length; i++) {
        callHook$1(dirsWithPostpatch[i], 'componentUpdated', vnode, oldVnode);
      }
    });
  }

  if (!isCreate) {
    for (key in oldDirs) {
      if (!newDirs[key]) {
        // no longer present, unbind
        callHook$1(oldDirs[key], 'unbind', oldVnode, oldVnode, isDestroy);
      }
    }
  }
}

var emptyModifiers = Object.create(null);

function normalizeDirectives$1 (
  dirs,
  vm
) {
  var res = Object.create(null);
  if (!dirs) {
    // $flow-disable-line
    return res
  }
  var i, dir;
  for (i = 0; i < dirs.length; i++) {
    dir = dirs[i];
    if (!dir.modifiers) {
      // $flow-disable-line
      dir.modifiers = emptyModifiers;
    }
    res[getRawDirName(dir)] = dir;
    dir.def = resolveAsset(vm.$options, 'directives', dir.name, true);
  }
  // $flow-disable-line
  return res
}

function getRawDirName (dir) {
  return dir.rawName || ((dir.name) + "." + (Object.keys(dir.modifiers || {}).join('.')))
}

function callHook$1 (dir, hook, vnode, oldVnode, isDestroy) {
  var fn = dir.def && dir.def[hook];
  if (fn) {
    try {
      fn(vnode.elm, dir, vnode, oldVnode, isDestroy);
    } catch (e) {
      handleError(e, vnode.context, ("directive " + (dir.name) + " " + hook + " hook"));
    }
  }
}

var baseModules = [
  ref,
  directives
];

/*  */

function updateAttrs (oldVnode, vnode) {
  var opts = vnode.componentOptions;
  if (isDef(opts) && opts.Ctor.options.inheritAttrs === false) {
    return
  }
  if (isUndef(oldVnode.data.attrs) && isUndef(vnode.data.attrs)) {
    return
  }
  var key, cur, old;
  var elm = vnode.elm;
  var oldAttrs = oldVnode.data.attrs || {};
  var attrs = vnode.data.attrs || {};
  // clone observed objects, as the user probably wants to mutate it
  if (isDef(attrs.__ob__)) {
    attrs = vnode.data.attrs = extend({}, attrs);
  }

  for (key in attrs) {
    cur = attrs[key];
    old = oldAttrs[key];
    if (old !== cur) {
      setAttr(elm, key, cur);
    }
  }
  // #4391: in IE9, setting type can reset value for input[type=radio]
  // #6666: IE/Edge forces progress value down to 1 before setting a max
  /* istanbul ignore if */
  if ((isIE || isEdge) && attrs.value !== oldAttrs.value) {
    setAttr(elm, 'value', attrs.value);
  }
  for (key in oldAttrs) {
    if (isUndef(attrs[key])) {
      if (isXlink(key)) {
        elm.removeAttributeNS(xlinkNS, getXlinkProp(key));
      } else if (!isEnumeratedAttr(key)) {
        elm.removeAttribute(key);
      }
    }
  }
}

function setAttr (el, key, value) {
  if (el.tagName.indexOf('-') > -1) {
    baseSetAttr(el, key, value);
  } else if (isBooleanAttr(key)) {
    // set attribute for blank value
    // e.g. <option disabled>Select one</option>
    if (isFalsyAttrValue(value)) {
      el.removeAttribute(key);
    } else {
      // technically allowfullscreen is a boolean attribute for <iframe>,
      // but Flash expects a value of "true" when used on <embed> tag
      value = key === 'allowfullscreen' && el.tagName === 'EMBED'
        ? 'true'
        : key;
      el.setAttribute(key, value);
    }
  } else if (isEnumeratedAttr(key)) {
    el.setAttribute(key, convertEnumeratedValue(key, value));
  } else if (isXlink(key)) {
    if (isFalsyAttrValue(value)) {
      el.removeAttributeNS(xlinkNS, getXlinkProp(key));
    } else {
      el.setAttributeNS(xlinkNS, key, value);
    }
  } else {
    baseSetAttr(el, key, value);
  }
}

function baseSetAttr (el, key, value) {
  if (isFalsyAttrValue(value)) {
    el.removeAttribute(key);
  } else {
    // #7138: IE10 & 11 fires input event when setting placeholder on
    // <textarea>... block the first input event and remove the blocker
    // immediately.
    /* istanbul ignore if */
    if (
      isIE && !isIE9 &&
      el.tagName === 'TEXTAREA' &&
      key === 'placeholder' && value !== '' && !el.__ieph
    ) {
      var blocker = function (e) {
        e.stopImmediatePropagation();
        el.removeEventListener('input', blocker);
      };
      el.addEventListener('input', blocker);
      // $flow-disable-line
      el.__ieph = true; /* IE placeholder patched */
    }
    el.setAttribute(key, value);
  }
}

var attrs = {
  create: updateAttrs,
  update: updateAttrs
};

/*  */

function updateClass (oldVnode, vnode) {
  var el = vnode.elm;
  var data = vnode.data;
  var oldData = oldVnode.data;
  if (
    isUndef(data.staticClass) &&
    isUndef(data.class) && (
      isUndef(oldData) || (
        isUndef(oldData.staticClass) &&
        isUndef(oldData.class)
      )
    )
  ) {
    return
  }

  var cls = genClassForVnode(vnode);

  // handle transition classes
  var transitionClass = el._transitionClasses;
  if (isDef(transitionClass)) {
    cls = concat(cls, stringifyClass(transitionClass));
  }

  // set the class
  if (cls !== el._prevClass) {
    el.setAttribute('class', cls);
    el._prevClass = cls;
  }
}

var klass = {
  create: updateClass,
  update: updateClass
};

/*  */

var validDivisionCharRE = /[\w).+\-_$\]]/;

function parseFilters (exp) {
  var inSingle = false;
  var inDouble = false;
  var inTemplateString = false;
  var inRegex = false;
  var curly = 0;
  var square = 0;
  var paren = 0;
  var lastFilterIndex = 0;
  var c, prev, i, expression, filters;

  for (i = 0; i < exp.length; i++) {
    prev = c;
    c = exp.charCodeAt(i);
    if (inSingle) {
      if (c === 0x27 && prev !== 0x5C) { inSingle = false; }
    } else if (inDouble) {
      if (c === 0x22 && prev !== 0x5C) { inDouble = false; }
    } else if (inTemplateString) {
      if (c === 0x60 && prev !== 0x5C) { inTemplateString = false; }
    } else if (inRegex) {
      if (c === 0x2f && prev !== 0x5C) { inRegex = false; }
    } else if (
      c === 0x7C && // pipe
      exp.charCodeAt(i + 1) !== 0x7C &&
      exp.charCodeAt(i - 1) !== 0x7C &&
      !curly && !square && !paren
    ) {
      if (expression === undefined) {
        // first filter, end of expression
        lastFilterIndex = i + 1;
        expression = exp.slice(0, i).trim();
      } else {
        pushFilter();
      }
    } else {
      switch (c) {
        case 0x22: inDouble = true; break         // "
        case 0x27: inSingle = true; break         // '
        case 0x60: inTemplateString = true; break // `
        case 0x28: paren++; break                 // (
        case 0x29: paren--; break                 // )
        case 0x5B: square++; break                // [
        case 0x5D: square--; break                // ]
        case 0x7B: curly++; break                 // {
        case 0x7D: curly--; break                 // }
      }
      if (c === 0x2f) { // /
        var j = i - 1;
        var p = (void 0);
        // find first non-whitespace prev char
        for (; j >= 0; j--) {
          p = exp.charAt(j);
          if (p !== ' ') { break }
        }
        if (!p || !validDivisionCharRE.test(p)) {
          inRegex = true;
        }
      }
    }
  }

  if (expression === undefined) {
    expression = exp.slice(0, i).trim();
  } else if (lastFilterIndex !== 0) {
    pushFilter();
  }

  function pushFilter () {
    (filters || (filters = [])).push(exp.slice(lastFilterIndex, i).trim());
    lastFilterIndex = i + 1;
  }

  if (filters) {
    for (i = 0; i < filters.length; i++) {
      expression = wrapFilter(expression, filters[i]);
    }
  }

  return expression
}

function wrapFilter (exp, filter) {
  var i = filter.indexOf('(');
  if (i < 0) {
    // _f: resolveFilter
    return ("_f(\"" + filter + "\")(" + exp + ")")
  } else {
    var name = filter.slice(0, i);
    var args = filter.slice(i + 1);
    return ("_f(\"" + name + "\")(" + exp + (args !== ')' ? ',' + args : args))
  }
}

/*  */



/* eslint-disable no-unused-vars */
function baseWarn (msg, range) {
  console.error(("[Vue compiler]: " + msg));
}
/* eslint-enable no-unused-vars */

function pluckModuleFunction (
  modules,
  key
) {
  return modules
    ? modules.map(function (m) { return m[key]; }).filter(function (_) { return _; })
    : []
}

function addProp (el, name, value, range, dynamic) {
  (el.props || (el.props = [])).push(rangeSetItem({ name: name, value: value, dynamic: dynamic }, range));
  el.plain = false;
}

function addAttr (el, name, value, range, dynamic) {
  var attrs = dynamic
    ? (el.dynamicAttrs || (el.dynamicAttrs = []))
    : (el.attrs || (el.attrs = []));
  attrs.push(rangeSetItem({ name: name, value: value, dynamic: dynamic }, range));
  el.plain = false;
}

// add a raw attr (use this in preTransforms)
function addRawAttr (el, name, value, range) {
  el.attrsMap[name] = value;
  el.attrsList.push(rangeSetItem({ name: name, value: value }, range));
}

function addDirective (
  el,
  name,
  rawName,
  value,
  arg,
  isDynamicArg,
  modifiers,
  range
) {
  (el.directives || (el.directives = [])).push(rangeSetItem({
    name: name,
    rawName: rawName,
    value: value,
    arg: arg,
    isDynamicArg: isDynamicArg,
    modifiers: modifiers
  }, range));
  el.plain = false;
}

function prependModifierMarker (symbol, name, dynamic) {
  return dynamic
    ? ("_p(" + name + ",\"" + symbol + "\")")
    : symbol + name // mark the event as captured
}

function addHandler (
  el,
  name,
  value,
  modifiers,
  important,
  warn,
  range,
  dynamic
) {
  modifiers = modifiers || emptyObject;
  // warn prevent and passive modifier
  /* istanbul ignore if */
  if (
    warn &&
    modifiers.prevent && modifiers.passive
  ) {
    warn(
      'passive and prevent can\'t be used together. ' +
      'Passive handler can\'t prevent default event.',
      range
    );
  }

  // normalize click.right and click.middle since they don't actually fire
  // this is technically browser-specific, but at least for now browsers are
  // the only target envs that have right/middle clicks.
  if (modifiers.right) {
    if (dynamic) {
      name = "(" + name + ")==='click'?'contextmenu':(" + name + ")";
    } else if (name === 'click') {
      name = 'contextmenu';
      delete modifiers.right;
    }
  } else if (modifiers.middle) {
    if (dynamic) {
      name = "(" + name + ")==='click'?'mouseup':(" + name + ")";
    } else if (name === 'click') {
      name = 'mouseup';
    }
  }

  // check capture modifier
  if (modifiers.capture) {
    delete modifiers.capture;
    name = prependModifierMarker('!', name, dynamic);
  }
  if (modifiers.once) {
    delete modifiers.once;
    name = prependModifierMarker('~', name, dynamic);
  }
  /* istanbul ignore if */
  if (modifiers.passive) {
    delete modifiers.passive;
    name = prependModifierMarker('&', name, dynamic);
  }

  var events;
  if (modifiers.native) {
    delete modifiers.native;
    events = el.nativeEvents || (el.nativeEvents = {});
  } else {
    events = el.events || (el.events = {});
  }

  var newHandler = rangeSetItem({ value: value.trim(), dynamic: dynamic }, range);
  if (modifiers !== emptyObject) {
    newHandler.modifiers = modifiers;
  }

  var handlers = events[name];
  /* istanbul ignore if */
  if (Array.isArray(handlers)) {
    important ? handlers.unshift(newHandler) : handlers.push(newHandler);
  } else if (handlers) {
    events[name] = important ? [newHandler, handlers] : [handlers, newHandler];
  } else {
    events[name] = newHandler;
  }

  el.plain = false;
}

function getRawBindingAttr (
  el,
  name
) {
  return el.rawAttrsMap[':' + name] ||
    el.rawAttrsMap['v-bind:' + name] ||
    el.rawAttrsMap[name]
}

function getBindingAttr (
  el,
  name,
  getStatic
) {
  var dynamicValue =
    getAndRemoveAttr(el, ':' + name) ||
    getAndRemoveAttr(el, 'v-bind:' + name);
  if (dynamicValue != null) {
    return parseFilters(dynamicValue)
  } else if (getStatic !== false) {
    var staticValue = getAndRemoveAttr(el, name);
    if (staticValue != null) {
      return JSON.stringify(staticValue)
    }
  }
}

// note: this only removes the attr from the Array (attrsList) so that it
// doesn't get processed by processAttrs.
// By default it does NOT remove it from the map (attrsMap) because the map is
// needed during codegen.
function getAndRemoveAttr (
  el,
  name,
  removeFromMap
) {
  var val;
  if ((val = el.attrsMap[name]) != null) {
    var list = el.attrsList;
    for (var i = 0, l = list.length; i < l; i++) {
      if (list[i].name === name) {
        list.splice(i, 1);
        break
      }
    }
  }
  if (removeFromMap) {
    delete el.attrsMap[name];
  }
  return val
}

function getAndRemoveAttrByRegex (
  el,
  name
) {
  var list = el.attrsList;
  for (var i = 0, l = list.length; i < l; i++) {
    var attr = list[i];
    if (name.test(attr.name)) {
      list.splice(i, 1);
      return attr
    }
  }
}

function rangeSetItem (
  item,
  range
) {
  if (range) {
    if (range.start != null) {
      item.start = range.start;
    }
    if (range.end != null) {
      item.end = range.end;
    }
  }
  return item
}

/*  */

/**
 * Cross-platform code generation for component v-model
 */
function genComponentModel (
  el,
  value,
  modifiers
) {
  var ref = modifiers || {};
  var number = ref.number;
  var trim = ref.trim;

  var baseValueExpression = '$$v';
  var valueExpression = baseValueExpression;
  if (trim) {
    valueExpression =
      "(typeof " + baseValueExpression + " === 'string'" +
      "? " + baseValueExpression + ".trim()" +
      ": " + baseValueExpression + ")";
  }
  if (number) {
    valueExpression = "_n(" + valueExpression + ")";
  }
  var assignment = genAssignmentCode(value, valueExpression);

  el.model = {
    value: ("(" + value + ")"),
    expression: JSON.stringify(value),
    callback: ("function (" + baseValueExpression + ") {" + assignment + "}")
  };
}

/**
 * Cross-platform codegen helper for generating v-model value assignment code.
 */
function genAssignmentCode (
  value,
  assignment
) {
  var res = parseModel(value);
  if (res.key === null) {
    return (value + "=" + assignment)
  } else {
    return ("$set(" + (res.exp) + ", " + (res.key) + ", " + assignment + ")")
  }
}

/**
 * Parse a v-model expression into a base path and a final key segment.
 * Handles both dot-path and possible square brackets.
 *
 * Possible cases:
 *
 * - test
 * - test[key]
 * - test[test1[key]]
 * - test["a"][key]
 * - xxx.test[a[a].test1[key]]
 * - test.xxx.a["asa"][test1[key]]
 *
 */

var len, str, chr, index$1, expressionPos, expressionEndPos;



function parseModel (val) {
  // Fix https://github.com/vuejs/vue/pull/7730
  // allow v-model="obj.val " (trailing whitespace)
  val = val.trim();
  len = val.length;

  if (val.indexOf('[') < 0 || val.lastIndexOf(']') < len - 1) {
    index$1 = val.lastIndexOf('.');
    if (index$1 > -1) {
      return {
        exp: val.slice(0, index$1),
        key: '"' + val.slice(index$1 + 1) + '"'
      }
    } else {
      return {
        exp: val,
        key: null
      }
    }
  }

  str = val;
  index$1 = expressionPos = expressionEndPos = 0;

  while (!eof()) {
    chr = next();
    /* istanbul ignore if */
    if (isStringStart(chr)) {
      parseString(chr);
    } else if (chr === 0x5B) {
      parseBracket(chr);
    }
  }

  return {
    exp: val.slice(0, expressionPos),
    key: val.slice(expressionPos + 1, expressionEndPos)
  }
}

function next () {
  return str.charCodeAt(++index$1)
}

function eof () {
  return index$1 >= len
}

function isStringStart (chr) {
  return chr === 0x22 || chr === 0x27
}

function parseBracket (chr) {
  var inBracket = 1;
  expressionPos = index$1;
  while (!eof()) {
    chr = next();
    if (isStringStart(chr)) {
      parseString(chr);
      continue
    }
    if (chr === 0x5B) { inBracket++; }
    if (chr === 0x5D) { inBracket--; }
    if (inBracket === 0) {
      expressionEndPos = index$1;
      break
    }
  }
}

function parseString (chr) {
  var stringQuote = chr;
  while (!eof()) {
    chr = next();
    if (chr === stringQuote) {
      break
    }
  }
}

/*  */

var warn$1;

// in some cases, the event used has to be determined at runtime
// so we used some reserved tokens during compile.
var RANGE_TOKEN = '__r';
var CHECKBOX_RADIO_TOKEN = '__c';

function model (
  el,
  dir,
  _warn
) {
  warn$1 = _warn;
  var value = dir.value;
  var modifiers = dir.modifiers;
  var tag = el.tag;
  var type = el.attrsMap.type;

  {
    // inputs with type="file" are read only and setting the input's
    // value will throw an error.
    if (tag === 'input' && type === 'file') {
      warn$1(
        "<" + (el.tag) + " v-model=\"" + value + "\" type=\"file\">:\n" +
        "File inputs are read only. Use a v-on:change listener instead.",
        el.rawAttrsMap['v-model']
      );
    }
  }

  if (el.component) {
    genComponentModel(el, value, modifiers);
    // component v-model doesn't need extra runtime
    return false
  } else if (tag === 'select') {
    genSelect(el, value, modifiers);
  } else if (tag === 'input' && type === 'checkbox') {
    genCheckboxModel(el, value, modifiers);
  } else if (tag === 'input' && type === 'radio') {
    genRadioModel(el, value, modifiers);
  } else if (tag === 'input' || tag === 'textarea') {
    genDefaultModel(el, value, modifiers);
  } else if (!config.isReservedTag(tag)) {
    genComponentModel(el, value, modifiers);
    // component v-model doesn't need extra runtime
    return false
  } else {
    warn$1(
      "<" + (el.tag) + " v-model=\"" + value + "\">: " +
      "v-model is not supported on this element type. " +
      'If you are working with contenteditable, it\'s recommended to ' +
      'wrap a library dedicated for that purpose inside a custom component.',
      el.rawAttrsMap['v-model']
    );
  }

  // ensure runtime directive metadata
  return true
}

function genCheckboxModel (
  el,
  value,
  modifiers
) {
  var number = modifiers && modifiers.number;
  var valueBinding = getBindingAttr(el, 'value') || 'null';
  var trueValueBinding = getBindingAttr(el, 'true-value') || 'true';
  var falseValueBinding = getBindingAttr(el, 'false-value') || 'false';
  addProp(el, 'checked',
    "Array.isArray(" + value + ")" +
    "?_i(" + value + "," + valueBinding + ")>-1" + (
      trueValueBinding === 'true'
        ? (":(" + value + ")")
        : (":_q(" + value + "," + trueValueBinding + ")")
    )
  );
  addHandler(el, 'change',
    "var $$a=" + value + "," +
        '$$el=$event.target,' +
        "$$c=$$el.checked?(" + trueValueBinding + "):(" + falseValueBinding + ");" +
    'if(Array.isArray($$a)){' +
      "var $$v=" + (number ? '_n(' + valueBinding + ')' : valueBinding) + "," +
          '$$i=_i($$a,$$v);' +
      "if($$el.checked){$$i<0&&(" + (genAssignmentCode(value, '$$a.concat([$$v])')) + ")}" +
      "else{$$i>-1&&(" + (genAssignmentCode(value, '$$a.slice(0,$$i).concat($$a.slice($$i+1))')) + ")}" +
    "}else{" + (genAssignmentCode(value, '$$c')) + "}",
    null, true
  );
}

function genRadioModel (
  el,
  value,
  modifiers
) {
  var number = modifiers && modifiers.number;
  var valueBinding = getBindingAttr(el, 'value') || 'null';
  valueBinding = number ? ("_n(" + valueBinding + ")") : valueBinding;
  addProp(el, 'checked', ("_q(" + value + "," + valueBinding + ")"));
  addHandler(el, 'change', genAssignmentCode(value, valueBinding), null, true);
}

function genSelect (
  el,
  value,
  modifiers
) {
  var number = modifiers && modifiers.number;
  var selectedVal = "Array.prototype.filter" +
    ".call($event.target.options,function(o){return o.selected})" +
    ".map(function(o){var val = \"_value\" in o ? o._value : o.value;" +
    "return " + (number ? '_n(val)' : 'val') + "})";

  var assignment = '$event.target.multiple ? $$selectedVal : $$selectedVal[0]';
  var code = "var $$selectedVal = " + selectedVal + ";";
  code = code + " " + (genAssignmentCode(value, assignment));
  addHandler(el, 'change', code, null, true);
}

function genDefaultModel (
  el,
  value,
  modifiers
) {
  var type = el.attrsMap.type;

  // warn if v-bind:value conflicts with v-model
  // except for inputs with v-bind:type
  {
    var value$1 = el.attrsMap['v-bind:value'] || el.attrsMap[':value'];
    var typeBinding = el.attrsMap['v-bind:type'] || el.attrsMap[':type'];
    if (value$1 && !typeBinding) {
      var binding = el.attrsMap['v-bind:value'] ? 'v-bind:value' : ':value';
      warn$1(
        binding + "=\"" + value$1 + "\" conflicts with v-model on the same element " +
        'because the latter already expands to a value binding internally',
        el.rawAttrsMap[binding]
      );
    }
  }

  var ref = modifiers || {};
  var lazy = ref.lazy;
  var number = ref.number;
  var trim = ref.trim;
  var needCompositionGuard = !lazy && type !== 'range';
  var event = lazy
    ? 'change'
    : type === 'range'
      ? RANGE_TOKEN
      : 'input';

  var valueExpression = '$event.target.value';
  if (trim) {
    valueExpression = "$event.target.value.trim()";
  }
  if (number) {
    valueExpression = "_n(" + valueExpression + ")";
  }

  var code = genAssignmentCode(value, valueExpression);
  if (needCompositionGuard) {
    code = "if($event.target.composing)return;" + code;
  }

  addProp(el, 'value', ("(" + value + ")"));
  addHandler(el, event, code, null, true);
  if (trim || number) {
    addHandler(el, 'blur', '$forceUpdate()');
  }
}

/*  */

// normalize v-model event tokens that can only be determined at runtime.
// it's important to place the event as the first in the array because
// the whole point is ensuring the v-model callback gets called before
// user-attached handlers.
function normalizeEvents (on) {
  /* istanbul ignore if */
  if (isDef(on[RANGE_TOKEN])) {
    // IE input[type=range] only supports `change` event
    var event = isIE ? 'change' : 'input';
    on[event] = [].concat(on[RANGE_TOKEN], on[event] || []);
    delete on[RANGE_TOKEN];
  }
  // This was originally intended to fix #4521 but no longer necessary
  // after 2.5. Keeping it for backwards compat with generated code from < 2.4
  /* istanbul ignore if */
  if (isDef(on[CHECKBOX_RADIO_TOKEN])) {
    on.change = [].concat(on[CHECKBOX_RADIO_TOKEN], on.change || []);
    delete on[CHECKBOX_RADIO_TOKEN];
  }
}

var target$1;

function createOnceHandler$1 (event, handler, capture) {
  var _target = target$1; // save current target element in closure
  return function onceHandler () {
    var res = handler.apply(null, arguments);
    if (res !== null) {
      remove$2(event, onceHandler, capture, _target);
    }
  }
}

// #9446: Firefox <= 53 (in particular, ESR 52) has incorrect Event.timeStamp
// implementation and does not fire microtasks in between event propagation, so
// safe to exclude.
var useMicrotaskFix = isUsingMicroTask && !(isFF && Number(isFF[1]) <= 53);

function add$1 (
  name,
  handler,
  capture,
  passive
) {
  // async edge case #6566: inner click event triggers patch, event handler
  // attached to outer element during patch, and triggered again. This
  // happens because browsers fire microtask ticks between event propagation.
  // the solution is simple: we save the timestamp when a handler is attached,
  // and the handler would only fire if the event passed to it was fired
  // AFTER it was attached.
  if (useMicrotaskFix) {
    var attachedTimestamp = currentFlushTimestamp;
    var original = handler;
    handler = original._wrapper = function (e) {
      if (
        // no bubbling, should always fire.
        // this is just a safety net in case event.timeStamp is unreliable in
        // certain weird environments...
        e.target === e.currentTarget ||
        // event is fired after handler attachment
        e.timeStamp >= attachedTimestamp ||
        // bail for environments that have buggy event.timeStamp implementations
        // #9462 iOS 9 bug: event.timeStamp is 0 after history.pushState
        // #9681 QtWebEngine event.timeStamp is negative value
        e.timeStamp <= 0 ||
        // #9448 bail if event is fired in another document in a multi-page
        // electron/nw.js app, since event.timeStamp will be using a different
        // starting reference
        e.target.ownerDocument !== document
      ) {
        return original.apply(this, arguments)
      }
    };
  }
  target$1.addEventListener(
    name,
    handler,
    supportsPassive
      ? { capture: capture, passive: passive }
      : capture
  );
}

function remove$2 (
  name,
  handler,
  capture,
  _target
) {
  (_target || target$1).removeEventListener(
    name,
    handler._wrapper || handler,
    capture
  );
}

function updateDOMListeners (oldVnode, vnode) {
  if (isUndef(oldVnode.data.on) && isUndef(vnode.data.on)) {
    return
  }
  var on = vnode.data.on || {};
  var oldOn = oldVnode.data.on || {};
  target$1 = vnode.elm;
  normalizeEvents(on);
  updateListeners(on, oldOn, add$1, remove$2, createOnceHandler$1, vnode.context);
  target$1 = undefined;
}

var events = {
  create: updateDOMListeners,
  update: updateDOMListeners
};

/*  */

var svgContainer;

function updateDOMProps (oldVnode, vnode) {
  if (isUndef(oldVnode.data.domProps) && isUndef(vnode.data.domProps)) {
    return
  }
  var key, cur;
  var elm = vnode.elm;
  var oldProps = oldVnode.data.domProps || {};
  var props = vnode.data.domProps || {};
  // clone observed objects, as the user probably wants to mutate it
  if (isDef(props.__ob__)) {
    props = vnode.data.domProps = extend({}, props);
  }

  for (key in oldProps) {
    if (!(key in props)) {
      elm[key] = '';
    }
  }

  for (key in props) {
    cur = props[key];
    // ignore children if the node has textContent or innerHTML,
    // as these will throw away existing DOM nodes and cause removal errors
    // on subsequent patches (#3360)
    if (key === 'textContent' || key === 'innerHTML') {
      if (vnode.children) { vnode.children.length = 0; }
      if (cur === oldProps[key]) { continue }
      // #6601 work around Chrome version <= 55 bug where single textNode
      // replaced by innerHTML/textContent retains its parentNode property
      if (elm.childNodes.length === 1) {
        elm.removeChild(elm.childNodes[0]);
      }
    }

    if (key === 'value' && elm.tagName !== 'PROGRESS') {
      // store value as _value as well since
      // non-string values will be stringified
      elm._value = cur;
      // avoid resetting cursor position when value is the same
      var strCur = isUndef(cur) ? '' : String(cur);
      if (shouldUpdateValue(elm, strCur)) {
        elm.value = strCur;
      }
    } else if (key === 'innerHTML' && isSVG(elm.tagName) && isUndef(elm.innerHTML)) {
      // IE doesn't support innerHTML for SVG elements
      svgContainer = svgContainer || document.createElement('div');
      svgContainer.innerHTML = "<svg>" + cur + "</svg>";
      var svg = svgContainer.firstChild;
      while (elm.firstChild) {
        elm.removeChild(elm.firstChild);
      }
      while (svg.firstChild) {
        elm.appendChild(svg.firstChild);
      }
    } else if (
      // skip the update if old and new VDOM state is the same.
      // `value` is handled separately because the DOM value may be temporarily
      // out of sync with VDOM state due to focus, composition and modifiers.
      // This  #4521 by skipping the unnecesarry `checked` update.
      cur !== oldProps[key]
    ) {
      // some property updates can throw
      // e.g. `value` on <progress> w/ non-finite value
      try {
        elm[key] = cur;
      } catch (e) {}
    }
  }
}

// check platforms/web/util/attrs.js acceptValue


function shouldUpdateValue (elm, checkVal) {
  return (!elm.composing && (
    elm.tagName === 'OPTION' ||
    isNotInFocusAndDirty(elm, checkVal) ||
    isDirtyWithModifiers(elm, checkVal)
  ))
}

function isNotInFocusAndDirty (elm, checkVal) {
  // return true when textbox (.number and .trim) loses focus and its value is
  // not equal to the updated value
  var notInFocus = true;
  // #6157
  // work around IE bug when accessing document.activeElement in an iframe
  try { notInFocus = document.activeElement !== elm; } catch (e) {}
  return notInFocus && elm.value !== checkVal
}

function isDirtyWithModifiers (elm, newVal) {
  var value = elm.value;
  var modifiers = elm._vModifiers; // injected by v-model runtime
  if (isDef(modifiers)) {
    if (modifiers.number) {
      return toNumber(value) !== toNumber(newVal)
    }
    if (modifiers.trim) {
      return value.trim() !== newVal.trim()
    }
  }
  return value !== newVal
}

var domProps = {
  create: updateDOMProps,
  update: updateDOMProps
};

/*  */

var parseStyleText = cached(function (cssText) {
  var res = {};
  var listDelimiter = /;(?![^(]*\))/g;
  var propertyDelimiter = /:(.+)/;
  cssText.split(listDelimiter).forEach(function (item) {
    if (item) {
      var tmp = item.split(propertyDelimiter);
      tmp.length > 1 && (res[tmp[0].trim()] = tmp[1].trim());
    }
  });
  return res
});

// merge static and dynamic style data on the same vnode
function normalizeStyleData (data) {
  var style = normalizeStyleBinding(data.style);
  // static style is pre-processed into an object during compilation
  // and is always a fresh object, so it's safe to merge into it
  return data.staticStyle
    ? extend(data.staticStyle, style)
    : style
}

// normalize possible array / string values into Object
function normalizeStyleBinding (bindingStyle) {
  if (Array.isArray(bindingStyle)) {
    return toObject(bindingStyle)
  }
  if (typeof bindingStyle === 'string') {
    return parseStyleText(bindingStyle)
  }
  return bindingStyle
}

/**
 * parent component style should be after child's
 * so that parent component's style could override it
 */
function getStyle (vnode, checkChild) {
  var res = {};
  var styleData;

  if (checkChild) {
    var childNode = vnode;
    while (childNode.componentInstance) {
      childNode = childNode.componentInstance._vnode;
      if (
        childNode && childNode.data &&
        (styleData = normalizeStyleData(childNode.data))
      ) {
        extend(res, styleData);
      }
    }
  }

  if ((styleData = normalizeStyleData(vnode.data))) {
    extend(res, styleData);
  }

  var parentNode = vnode;
  while ((parentNode = parentNode.parent)) {
    if (parentNode.data && (styleData = normalizeStyleData(parentNode.data))) {
      extend(res, styleData);
    }
  }
  return res
}

/*  */

var cssVarRE = /^--/;
var importantRE = /\s*!important$/;
var setProp = function (el, name, val) {
  /* istanbul ignore if */
  if (cssVarRE.test(name)) {
    el.style.setProperty(name, val);
  } else if (importantRE.test(val)) {
    el.style.setProperty(hyphenate(name), val.replace(importantRE, ''), 'important');
  } else {
    var normalizedName = normalize(name);
    if (Array.isArray(val)) {
      // Support values array created by autoprefixer, e.g.
      // {display: ["-webkit-box", "-ms-flexbox", "flex"]}
      // Set them one by one, and the browser will only set those it can recognize
      for (var i = 0, len = val.length; i < len; i++) {
        el.style[normalizedName] = val[i];
      }
    } else {
      el.style[normalizedName] = val;
    }
  }
};

var vendorNames = ['Webkit', 'Moz', 'ms'];

var emptyStyle;
var normalize = cached(function (prop) {
  emptyStyle = emptyStyle || document.createElement('div').style;
  prop = camelize(prop);
  if (prop !== 'filter' && (prop in emptyStyle)) {
    return prop
  }
  var capName = prop.charAt(0).toUpperCase() + prop.slice(1);
  for (var i = 0; i < vendorNames.length; i++) {
    var name = vendorNames[i] + capName;
    if (name in emptyStyle) {
      return name
    }
  }
});

function updateStyle (oldVnode, vnode) {
  var data = vnode.data;
  var oldData = oldVnode.data;

  if (isUndef(data.staticStyle) && isUndef(data.style) &&
    isUndef(oldData.staticStyle) && isUndef(oldData.style)
  ) {
    return
  }

  var cur, name;
  var el = vnode.elm;
  var oldStaticStyle = oldData.staticStyle;
  var oldStyleBinding = oldData.normalizedStyle || oldData.style || {};

  // if static style exists, stylebinding already merged into it when doing normalizeStyleData
  var oldStyle = oldStaticStyle || oldStyleBinding;

  var style = normalizeStyleBinding(vnode.data.style) || {};

  // store normalized style under a different key for next diff
  // make sure to clone it if it's reactive, since the user likely wants
  // to mutate it.
  vnode.data.normalizedStyle = isDef(style.__ob__)
    ? extend({}, style)
    : style;

  var newStyle = getStyle(vnode, true);

  for (name in oldStyle) {
    if (isUndef(newStyle[name])) {
      setProp(el, name, '');
    }
  }
  for (name in newStyle) {
    cur = newStyle[name];
    if (cur !== oldStyle[name]) {
      // ie9 setting to null has no effect, must use empty string
      setProp(el, name, cur == null ? '' : cur);
    }
  }
}

var style = {
  create: updateStyle,
  update: updateStyle
};

/*  */

var whitespaceRE = /\s+/;

/**
 * Add class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */
function addClass (el, cls) {
  /* istanbul ignore if */
  if (!cls || !(cls = cls.trim())) {
    return
  }

  /* istanbul ignore else */
  if (el.classList) {
    if (cls.indexOf(' ') > -1) {
      cls.split(whitespaceRE).forEach(function (c) { return el.classList.add(c); });
    } else {
      el.classList.add(cls);
    }
  } else {
    var cur = " " + (el.getAttribute('class') || '') + " ";
    if (cur.indexOf(' ' + cls + ' ') < 0) {
      el.setAttribute('class', (cur + cls).trim());
    }
  }
}

/**
 * Remove class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */
function removeClass (el, cls) {
  /* istanbul ignore if */
  if (!cls || !(cls = cls.trim())) {
    return
  }

  /* istanbul ignore else */
  if (el.classList) {
    if (cls.indexOf(' ') > -1) {
      cls.split(whitespaceRE).forEach(function (c) { return el.classList.remove(c); });
    } else {
      el.classList.remove(cls);
    }
    if (!el.classList.length) {
      el.removeAttribute('class');
    }
  } else {
    var cur = " " + (el.getAttribute('class') || '') + " ";
    var tar = ' ' + cls + ' ';
    while (cur.indexOf(tar) >= 0) {
      cur = cur.replace(tar, ' ');
    }
    cur = cur.trim();
    if (cur) {
      el.setAttribute('class', cur);
    } else {
      el.removeAttribute('class');
    }
  }
}

/*  */

function resolveTransition (def$$1) {
  if (!def$$1) {
    return
  }
  /* istanbul ignore else */
  if (typeof def$$1 === 'object') {
    var res = {};
    if (def$$1.css !== false) {
      extend(res, autoCssTransition(def$$1.name || 'v'));
    }
    extend(res, def$$1);
    return res
  } else if (typeof def$$1 === 'string') {
    return autoCssTransition(def$$1)
  }
}

var autoCssTransition = cached(function (name) {
  return {
    enterClass: (name + "-enter"),
    enterToClass: (name + "-enter-to"),
    enterActiveClass: (name + "-enter-active"),
    leaveClass: (name + "-leave"),
    leaveToClass: (name + "-leave-to"),
    leaveActiveClass: (name + "-leave-active")
  }
});

var hasTransition = inBrowser && !isIE9;
var TRANSITION = 'transition';
var ANIMATION = 'animation';

// Transition property/event sniffing
var transitionProp = 'transition';
var transitionEndEvent = 'transitionend';
var animationProp = 'animation';
var animationEndEvent = 'animationend';
if (hasTransition) {
  /* istanbul ignore if */
  if (window.ontransitionend === undefined &&
    window.onwebkittransitionend !== undefined
  ) {
    transitionProp = 'WebkitTransition';
    transitionEndEvent = 'webkitTransitionEnd';
  }
  if (window.onanimationend === undefined &&
    window.onwebkitanimationend !== undefined
  ) {
    animationProp = 'WebkitAnimation';
    animationEndEvent = 'webkitAnimationEnd';
  }
}

// binding to window is necessary to make hot reload work in IE in strict mode
var raf = inBrowser
  ? window.requestAnimationFrame
    ? window.requestAnimationFrame.bind(window)
    : setTimeout
  : /* istanbul ignore next */ function (fn) { return fn(); };

function nextFrame (fn) {
  raf(function () {
    raf(fn);
  });
}

function addTransitionClass (el, cls) {
  var transitionClasses = el._transitionClasses || (el._transitionClasses = []);
  if (transitionClasses.indexOf(cls) < 0) {
    transitionClasses.push(cls);
    addClass(el, cls);
  }
}

function removeTransitionClass (el, cls) {
  if (el._transitionClasses) {
    remove(el._transitionClasses, cls);
  }
  removeClass(el, cls);
}

function whenTransitionEnds (
  el,
  expectedType,
  cb
) {
  var ref = getTransitionInfo(el, expectedType);
  var type = ref.type;
  var timeout = ref.timeout;
  var propCount = ref.propCount;
  if (!type) { return cb() }
  var event = type === TRANSITION ? transitionEndEvent : animationEndEvent;
  var ended = 0;
  var end = function () {
    el.removeEventListener(event, onEnd);
    cb();
  };
  var onEnd = function (e) {
    if (e.target === el) {
      if (++ended >= propCount) {
        end();
      }
    }
  };
  setTimeout(function () {
    if (ended < propCount) {
      end();
    }
  }, timeout + 1);
  el.addEventListener(event, onEnd);
}

var transformRE = /\b(transform|all)(,|$)/;

function getTransitionInfo (el, expectedType) {
  var styles = window.getComputedStyle(el);
  // JSDOM may return undefined for transition properties
  var transitionDelays = (styles[transitionProp + 'Delay'] || '').split(', ');
  var transitionDurations = (styles[transitionProp + 'Duration'] || '').split(', ');
  var transitionTimeout = getTimeout(transitionDelays, transitionDurations);
  var animationDelays = (styles[animationProp + 'Delay'] || '').split(', ');
  var animationDurations = (styles[animationProp + 'Duration'] || '').split(', ');
  var animationTimeout = getTimeout(animationDelays, animationDurations);

  var type;
  var timeout = 0;
  var propCount = 0;
  /* istanbul ignore if */
  if (expectedType === TRANSITION) {
    if (transitionTimeout > 0) {
      type = TRANSITION;
      timeout = transitionTimeout;
      propCount = transitionDurations.length;
    }
  } else if (expectedType === ANIMATION) {
    if (animationTimeout > 0) {
      type = ANIMATION;
      timeout = animationTimeout;
      propCount = animationDurations.length;
    }
  } else {
    timeout = Math.max(transitionTimeout, animationTimeout);
    type = timeout > 0
      ? transitionTimeout > animationTimeout
        ? TRANSITION
        : ANIMATION
      : null;
    propCount = type
      ? type === TRANSITION
        ? transitionDurations.length
        : animationDurations.length
      : 0;
  }
  var hasTransform =
    type === TRANSITION &&
    transformRE.test(styles[transitionProp + 'Property']);
  return {
    type: type,
    timeout: timeout,
    propCount: propCount,
    hasTransform: hasTransform
  }
}

function getTimeout (delays, durations) {
  /* istanbul ignore next */
  while (delays.length < durations.length) {
    delays = delays.concat(delays);
  }

  return Math.max.apply(null, durations.map(function (d, i) {
    return toMs(d) + toMs(delays[i])
  }))
}

// Old versions of Chromium (below 61.0.3163.100) formats floating pointer numbers
// in a locale-dependent way, using a comma instead of a dot.
// If comma is not replaced with a dot, the input will be rounded down (i.e. acting
// as a floor function) causing unexpected behaviors
function toMs (s) {
  return Number(s.slice(0, -1).replace(',', '.')) * 1000
}

/*  */

function enter (vnode, toggleDisplay) {
  var el = vnode.elm;

  // call leave callback now
  if (isDef(el._leaveCb)) {
    el._leaveCb.cancelled = true;
    el._leaveCb();
  }

  var data = resolveTransition(vnode.data.transition);
  if (isUndef(data)) {
    return
  }

  /* istanbul ignore if */
  if (isDef(el._enterCb) || el.nodeType !== 1) {
    return
  }

  var css = data.css;
  var type = data.type;
  var enterClass = data.enterClass;
  var enterToClass = data.enterToClass;
  var enterActiveClass = data.enterActiveClass;
  var appearClass = data.appearClass;
  var appearToClass = data.appearToClass;
  var appearActiveClass = data.appearActiveClass;
  var beforeEnter = data.beforeEnter;
  var enter = data.enter;
  var afterEnter = data.afterEnter;
  var enterCancelled = data.enterCancelled;
  var beforeAppear = data.beforeAppear;
  var appear = data.appear;
  var afterAppear = data.afterAppear;
  var appearCancelled = data.appearCancelled;
  var duration = data.duration;

  // activeInstance will always be the <transition> component managing this
  // transition. One edge case to check is when the <transition> is placed
  // as the root node of a child component. In that case we need to check
  // <transition>'s parent for appear check.
  var context = activeInstance;
  var transitionNode = activeInstance.$vnode;
  while (transitionNode && transitionNode.parent) {
    context = transitionNode.context;
    transitionNode = transitionNode.parent;
  }

  var isAppear = !context._isMounted || !vnode.isRootInsert;

  if (isAppear && !appear && appear !== '') {
    return
  }

  var startClass = isAppear && appearClass
    ? appearClass
    : enterClass;
  var activeClass = isAppear && appearActiveClass
    ? appearActiveClass
    : enterActiveClass;
  var toClass = isAppear && appearToClass
    ? appearToClass
    : enterToClass;

  var beforeEnterHook = isAppear
    ? (beforeAppear || beforeEnter)
    : beforeEnter;
  var enterHook = isAppear
    ? (typeof appear === 'function' ? appear : enter)
    : enter;
  var afterEnterHook = isAppear
    ? (afterAppear || afterEnter)
    : afterEnter;
  var enterCancelledHook = isAppear
    ? (appearCancelled || enterCancelled)
    : enterCancelled;

  var explicitEnterDuration = toNumber(
    isObject(duration)
      ? duration.enter
      : duration
  );

  if (explicitEnterDuration != null) {
    checkDuration(explicitEnterDuration, 'enter', vnode);
  }

  var expectsCSS = css !== false && !isIE9;
  var userWantsControl = getHookArgumentsLength(enterHook);

  var cb = el._enterCb = once(function () {
    if (expectsCSS) {
      removeTransitionClass(el, toClass);
      removeTransitionClass(el, activeClass);
    }
    if (cb.cancelled) {
      if (expectsCSS) {
        removeTransitionClass(el, startClass);
      }
      enterCancelledHook && enterCancelledHook(el);
    } else {
      afterEnterHook && afterEnterHook(el);
    }
    el._enterCb = null;
  });

  if (!vnode.data.show) {
    // remove pending leave element on enter by injecting an insert hook
    mergeVNodeHook(vnode, 'insert', function () {
      var parent = el.parentNode;
      var pendingNode = parent && parent._pending && parent._pending[vnode.key];
      if (pendingNode &&
        pendingNode.tag === vnode.tag &&
        pendingNode.elm._leaveCb
      ) {
        pendingNode.elm._leaveCb();
      }
      enterHook && enterHook(el, cb);
    });
  }

  // start enter transition
  beforeEnterHook && beforeEnterHook(el);
  if (expectsCSS) {
    addTransitionClass(el, startClass);
    addTransitionClass(el, activeClass);
    nextFrame(function () {
      removeTransitionClass(el, startClass);
      if (!cb.cancelled) {
        addTransitionClass(el, toClass);
        if (!userWantsControl) {
          if (isValidDuration(explicitEnterDuration)) {
            setTimeout(cb, explicitEnterDuration);
          } else {
            whenTransitionEnds(el, type, cb);
          }
        }
      }
    });
  }

  if (vnode.data.show) {
    toggleDisplay && toggleDisplay();
    enterHook && enterHook(el, cb);
  }

  if (!expectsCSS && !userWantsControl) {
    cb();
  }
}

function leave (vnode, rm) {
  var el = vnode.elm;

  // call enter callback now
  if (isDef(el._enterCb)) {
    el._enterCb.cancelled = true;
    el._enterCb();
  }

  var data = resolveTransition(vnode.data.transition);
  if (isUndef(data) || el.nodeType !== 1) {
    return rm()
  }

  /* istanbul ignore if */
  if (isDef(el._leaveCb)) {
    return
  }

  var css = data.css;
  var type = data.type;
  var leaveClass = data.leaveClass;
  var leaveToClass = data.leaveToClass;
  var leaveActiveClass = data.leaveActiveClass;
  var beforeLeave = data.beforeLeave;
  var leave = data.leave;
  var afterLeave = data.afterLeave;
  var leaveCancelled = data.leaveCancelled;
  var delayLeave = data.delayLeave;
  var duration = data.duration;

  var expectsCSS = css !== false && !isIE9;
  var userWantsControl = getHookArgumentsLength(leave);

  var explicitLeaveDuration = toNumber(
    isObject(duration)
      ? duration.leave
      : duration
  );

  if (isDef(explicitLeaveDuration)) {
    checkDuration(explicitLeaveDuration, 'leave', vnode);
  }

  var cb = el._leaveCb = once(function () {
    if (el.parentNode && el.parentNode._pending) {
      el.parentNode._pending[vnode.key] = null;
    }
    if (expectsCSS) {
      removeTransitionClass(el, leaveToClass);
      removeTransitionClass(el, leaveActiveClass);
    }
    if (cb.cancelled) {
      if (expectsCSS) {
        removeTransitionClass(el, leaveClass);
      }
      leaveCancelled && leaveCancelled(el);
    } else {
      rm();
      afterLeave && afterLeave(el);
    }
    el._leaveCb = null;
  });

  if (delayLeave) {
    delayLeave(performLeave);
  } else {
    performLeave();
  }

  function performLeave () {
    // the delayed leave may have already been cancelled
    if (cb.cancelled) {
      return
    }
    // record leaving element
    if (!vnode.data.show && el.parentNode) {
      (el.parentNode._pending || (el.parentNode._pending = {}))[(vnode.key)] = vnode;
    }
    beforeLeave && beforeLeave(el);
    if (expectsCSS) {
      addTransitionClass(el, leaveClass);
      addTransitionClass(el, leaveActiveClass);
      nextFrame(function () {
        removeTransitionClass(el, leaveClass);
        if (!cb.cancelled) {
          addTransitionClass(el, leaveToClass);
          if (!userWantsControl) {
            if (isValidDuration(explicitLeaveDuration)) {
              setTimeout(cb, explicitLeaveDuration);
            } else {
              whenTransitionEnds(el, type, cb);
            }
          }
        }
      });
    }
    leave && leave(el, cb);
    if (!expectsCSS && !userWantsControl) {
      cb();
    }
  }
}

// only used in dev mode
function checkDuration (val, name, vnode) {
  if (typeof val !== 'number') {
    warn(
      "<transition> explicit " + name + " duration is not a valid number - " +
      "got " + (JSON.stringify(val)) + ".",
      vnode.context
    );
  } else if (isNaN(val)) {
    warn(
      "<transition> explicit " + name + " duration is NaN - " +
      'the duration expression might be incorrect.',
      vnode.context
    );
  }
}

function isValidDuration (val) {
  return typeof val === 'number' && !isNaN(val)
}

/**
 * Normalize a transition hook's argument length. The hook may be:
 * - a merged hook (invoker) with the original in .fns
 * - a wrapped component method (check ._length)
 * - a plain function (.length)
 */
function getHookArgumentsLength (fn) {
  if (isUndef(fn)) {
    return false
  }
  var invokerFns = fn.fns;
  if (isDef(invokerFns)) {
    // invoker
    return getHookArgumentsLength(
      Array.isArray(invokerFns)
        ? invokerFns[0]
        : invokerFns
    )
  } else {
    return (fn._length || fn.length) > 1
  }
}

function _enter (_, vnode) {
  if (vnode.data.show !== true) {
    enter(vnode);
  }
}

var transition = inBrowser ? {
  create: _enter,
  activate: _enter,
  remove: function remove$$1 (vnode, rm) {
    /* istanbul ignore else */
    if (vnode.data.show !== true) {
      leave(vnode, rm);
    } else {
      rm();
    }
  }
} : {};

var platformModules = [
  attrs,
  klass,
  events,
  domProps,
  style,
  transition
];

/*  */

// the directive module should be applied last, after all
// built-in modules have been applied.
var modules = platformModules.concat(baseModules);

var patch = createPatchFunction({ nodeOps: nodeOps, modules: modules });

/**
 * Not type checking this file because flow doesn't like attaching
 * properties to Elements.
 */

/* istanbul ignore if */
if (isIE9) {
  // http://www.matts411.com/post/internet-explorer-9-oninput/
  document.addEventListener('selectionchange', function () {
    var el = document.activeElement;
    if (el && el.vmodel) {
      trigger(el, 'input');
    }
  });
}

var directive = {
  inserted: function inserted (el, binding, vnode, oldVnode) {
    if (vnode.tag === 'select') {
      // #6903
      if (oldVnode.elm && !oldVnode.elm._vOptions) {
        mergeVNodeHook(vnode, 'postpatch', function () {
          directive.componentUpdated(el, binding, vnode);
        });
      } else {
        setSelected(el, binding, vnode.context);
      }
      el._vOptions = [].map.call(el.options, getValue);
    } else if (vnode.tag === 'textarea' || isTextInputType(el.type)) {
      el._vModifiers = binding.modifiers;
      if (!binding.modifiers.lazy) {
        el.addEventListener('compositionstart', onCompositionStart);
        el.addEventListener('compositionend', onCompositionEnd);
        // Safari < 10.2 & UIWebView doesn't fire compositionend when
        // switching focus before confirming composition choice
        // this also fixes the issue where some browsers e.g. iOS Chrome
        // fires "change" instead of "input" on autocomplete.
        el.addEventListener('change', onCompositionEnd);
        /* istanbul ignore if */
        if (isIE9) {
          el.vmodel = true;
        }
      }
    }
  },

  componentUpdated: function componentUpdated (el, binding, vnode) {
    if (vnode.tag === 'select') {
      setSelected(el, binding, vnode.context);
      // in case the options rendered by v-for have changed,
      // it's possible that the value is out-of-sync with the rendered options.
      // detect such cases and filter out values that no longer has a matching
      // option in the DOM.
      var prevOptions = el._vOptions;
      var curOptions = el._vOptions = [].map.call(el.options, getValue);
      if (curOptions.some(function (o, i) { return !looseEqual(o, prevOptions[i]); })) {
        // trigger change event if
        // no matching option found for at least one value
        var needReset = el.multiple
          ? binding.value.some(function (v) { return hasNoMatchingOption(v, curOptions); })
          : binding.value !== binding.oldValue && hasNoMatchingOption(binding.value, curOptions);
        if (needReset) {
          trigger(el, 'change');
        }
      }
    }
  }
};

function setSelected (el, binding, vm) {
  actuallySetSelected(el, binding, vm);
  /* istanbul ignore if */
  if (isIE || isEdge) {
    setTimeout(function () {
      actuallySetSelected(el, binding, vm);
    }, 0);
  }
}

function actuallySetSelected (el, binding, vm) {
  var value = binding.value;
  var isMultiple = el.multiple;
  if (isMultiple && !Array.isArray(value)) {
    warn(
      "<select multiple v-model=\"" + (binding.expression) + "\"> " +
      "expects an Array value for its binding, but got " + (Object.prototype.toString.call(value).slice(8, -1)),
      vm
    );
    return
  }
  var selected, option;
  for (var i = 0, l = el.options.length; i < l; i++) {
    option = el.options[i];
    if (isMultiple) {
      selected = looseIndexOf(value, getValue(option)) > -1;
      if (option.selected !== selected) {
        option.selected = selected;
      }
    } else {
      if (looseEqual(getValue(option), value)) {
        if (el.selectedIndex !== i) {
          el.selectedIndex = i;
        }
        return
      }
    }
  }
  if (!isMultiple) {
    el.selectedIndex = -1;
  }
}

function hasNoMatchingOption (value, options) {
  return options.every(function (o) { return !looseEqual(o, value); })
}

function getValue (option) {
  return '_value' in option
    ? option._value
    : option.value
}

function onCompositionStart (e) {
  e.target.composing = true;
}

function onCompositionEnd (e) {
  // prevent triggering an input event for no reason
  if (!e.target.composing) { return }
  e.target.composing = false;
  trigger(e.target, 'input');
}

function trigger (el, type) {
  var e = document.createEvent('HTMLEvents');
  e.initEvent(type, true, true);
  el.dispatchEvent(e);
}

/*  */

// recursively search for possible transition defined inside the component root
function locateNode (vnode) {
  return vnode.componentInstance && (!vnode.data || !vnode.data.transition)
    ? locateNode(vnode.componentInstance._vnode)
    : vnode
}

var show = {
  bind: function bind (el, ref, vnode) {
    var value = ref.value;

    vnode = locateNode(vnode);
    var transition$$1 = vnode.data && vnode.data.transition;
    var originalDisplay = el.__vOriginalDisplay =
      el.style.display === 'none' ? '' : el.style.display;
    if (value && transition$$1) {
      vnode.data.show = true;
      enter(vnode, function () {
        el.style.display = originalDisplay;
      });
    } else {
      el.style.display = value ? originalDisplay : 'none';
    }
  },

  update: function update (el, ref, vnode) {
    var value = ref.value;
    var oldValue = ref.oldValue;

    /* istanbul ignore if */
    if (!value === !oldValue) { return }
    vnode = locateNode(vnode);
    var transition$$1 = vnode.data && vnode.data.transition;
    if (transition$$1) {
      vnode.data.show = true;
      if (value) {
        enter(vnode, function () {
          el.style.display = el.__vOriginalDisplay;
        });
      } else {
        leave(vnode, function () {
          el.style.display = 'none';
        });
      }
    } else {
      el.style.display = value ? el.__vOriginalDisplay : 'none';
    }
  },

  unbind: function unbind (
    el,
    binding,
    vnode,
    oldVnode,
    isDestroy
  ) {
    if (!isDestroy) {
      el.style.display = el.__vOriginalDisplay;
    }
  }
};

var platformDirectives = {
  model: directive,
  show: show
};

/*  */

var transitionProps = {
  name: String,
  appear: Boolean,
  css: Boolean,
  mode: String,
  type: String,
  enterClass: String,
  leaveClass: String,
  enterToClass: String,
  leaveToClass: String,
  enterActiveClass: String,
  leaveActiveClass: String,
  appearClass: String,
  appearActiveClass: String,
  appearToClass: String,
  duration: [Number, String, Object]
};

// in case the child is also an abstract component, e.g. <keep-alive>
// we want to recursively retrieve the real component to be rendered
function getRealChild (vnode) {
  var compOptions = vnode && vnode.componentOptions;
  if (compOptions && compOptions.Ctor.options.abstract) {
    return getRealChild(getFirstComponentChild(compOptions.children))
  } else {
    return vnode
  }
}

function extractTransitionData (comp) {
  var data = {};
  var options = comp.$options;
  // props
  for (var key in options.propsData) {
    data[key] = comp[key];
  }
  // events.
  // extract listeners and pass them directly to the transition methods
  var listeners = options._parentListeners;
  for (var key$1 in listeners) {
    data[camelize(key$1)] = listeners[key$1];
  }
  return data
}

function placeholder (h, rawChild) {
  if (/\d-keep-alive$/.test(rawChild.tag)) {
    return h('keep-alive', {
      props: rawChild.componentOptions.propsData
    })
  }
}

function hasParentTransition (vnode) {
  while ((vnode = vnode.parent)) {
    if (vnode.data.transition) {
      return true
    }
  }
}

function isSameChild (child, oldChild) {
  return oldChild.key === child.key && oldChild.tag === child.tag
}

var isNotTextNode = function (c) { return c.tag || isAsyncPlaceholder(c); };

var isVShowDirective = function (d) { return d.name === 'show'; };

var Transition = {
  name: 'transition',
  props: transitionProps,
  abstract: true,

  render: function render (h) {
    var this$1 = this;

    var children = this.$slots.default;
    if (!children) {
      return
    }

    // filter out text nodes (possible whitespaces)
    children = children.filter(isNotTextNode);
    /* istanbul ignore if */
    if (!children.length) {
      return
    }

    // warn multiple elements
    if (children.length > 1) {
      warn(
        '<transition> can only be used on a single element. Use ' +
        '<transition-group> for lists.',
        this.$parent
      );
    }

    var mode = this.mode;

    // warn invalid mode
    if (mode && mode !== 'in-out' && mode !== 'out-in'
    ) {
      warn(
        'invalid <transition> mode: ' + mode,
        this.$parent
      );
    }

    var rawChild = children[0];

    // if this is a component root node and the component's
    // parent container node also has transition, skip.
    if (hasParentTransition(this.$vnode)) {
      return rawChild
    }

    // apply transition data to child
    // use getRealChild() to ignore abstract components e.g. keep-alive
    var child = getRealChild(rawChild);
    /* istanbul ignore if */
    if (!child) {
      return rawChild
    }

    if (this._leaving) {
      return placeholder(h, rawChild)
    }

    // ensure a key that is unique to the vnode type and to this transition
    // component instance. This key will be used to remove pending leaving nodes
    // during entering.
    var id = "__transition-" + (this._uid) + "-";
    child.key = child.key == null
      ? child.isComment
        ? id + 'comment'
        : id + child.tag
      : isPrimitive(child.key)
        ? (String(child.key).indexOf(id) === 0 ? child.key : id + child.key)
        : child.key;

    var data = (child.data || (child.data = {})).transition = extractTransitionData(this);
    var oldRawChild = this._vnode;
    var oldChild = getRealChild(oldRawChild);

    // mark v-show
    // so that the transition module can hand over the control to the directive
    if (child.data.directives && child.data.directives.some(isVShowDirective)) {
      child.data.show = true;
    }

    if (
      oldChild &&
      oldChild.data &&
      !isSameChild(child, oldChild) &&
      !isAsyncPlaceholder(oldChild) &&
      // #6687 component root is a comment node
      !(oldChild.componentInstance && oldChild.componentInstance._vnode.isComment)
    ) {
      // replace old child transition data with fresh one
      // important for dynamic transitions!
      var oldData = oldChild.data.transition = extend({}, data);
      // handle transition mode
      if (mode === 'out-in') {
        // return placeholder node and queue update when leave finishes
        this._leaving = true;
        mergeVNodeHook(oldData, 'afterLeave', function () {
          this$1._leaving = false;
          this$1.$forceUpdate();
        });
        return placeholder(h, rawChild)
      } else if (mode === 'in-out') {
        if (isAsyncPlaceholder(child)) {
          return oldRawChild
        }
        var delayedLeave;
        var performLeave = function () { delayedLeave(); };
        mergeVNodeHook(data, 'afterEnter', performLeave);
        mergeVNodeHook(data, 'enterCancelled', performLeave);
        mergeVNodeHook(oldData, 'delayLeave', function (leave) { delayedLeave = leave; });
      }
    }

    return rawChild
  }
};

/*  */

var props = extend({
  tag: String,
  moveClass: String
}, transitionProps);

delete props.mode;

var TransitionGroup = {
  props: props,

  beforeMount: function beforeMount () {
    var this$1 = this;

    var update = this._update;
    this._update = function (vnode, hydrating) {
      var restoreActiveInstance = setActiveInstance(this$1);
      // force removing pass
      this$1.__patch__(
        this$1._vnode,
        this$1.kept,
        false, // hydrating
        true // removeOnly (!important, avoids unnecessary moves)
      );
      this$1._vnode = this$1.kept;
      restoreActiveInstance();
      update.call(this$1, vnode, hydrating);
    };
  },

  render: function render (h) {
    var tag = this.tag || this.$vnode.data.tag || 'span';
    var map = Object.create(null);
    var prevChildren = this.prevChildren = this.children;
    var rawChildren = this.$slots.default || [];
    var children = this.children = [];
    var transitionData = extractTransitionData(this);

    for (var i = 0; i < rawChildren.length; i++) {
      var c = rawChildren[i];
      if (c.tag) {
        if (c.key != null && String(c.key).indexOf('__vlist') !== 0) {
          children.push(c);
          map[c.key] = c
          ;(c.data || (c.data = {})).transition = transitionData;
        } else {
          var opts = c.componentOptions;
          var name = opts ? (opts.Ctor.options.name || opts.tag || '') : c.tag;
          warn(("<transition-group> children must be keyed: <" + name + ">"));
        }
      }
    }

    if (prevChildren) {
      var kept = [];
      var removed = [];
      for (var i$1 = 0; i$1 < prevChildren.length; i$1++) {
        var c$1 = prevChildren[i$1];
        c$1.data.transition = transitionData;
        c$1.data.pos = c$1.elm.getBoundingClientRect();
        if (map[c$1.key]) {
          kept.push(c$1);
        } else {
          removed.push(c$1);
        }
      }
      this.kept = h(tag, null, kept);
      this.removed = removed;
    }

    return h(tag, null, children)
  },

  updated: function updated () {
    var children = this.prevChildren;
    var moveClass = this.moveClass || ((this.name || 'v') + '-move');
    if (!children.length || !this.hasMove(children[0].elm, moveClass)) {
      return
    }

    // we divide the work into three loops to avoid mixing DOM reads and writes
    // in each iteration - which helps prevent layout thrashing.
    children.forEach(callPendingCbs);
    children.forEach(recordPosition);
    children.forEach(applyTranslation);

    // force reflow to put everything in position
    // assign to this to avoid being removed in tree-shaking
    // $flow-disable-line
    this._reflow = document.body.offsetHeight;

    children.forEach(function (c) {
      if (c.data.moved) {
        var el = c.elm;
        var s = el.style;
        addTransitionClass(el, moveClass);
        s.transform = s.WebkitTransform = s.transitionDuration = '';
        el.addEventListener(transitionEndEvent, el._moveCb = function cb (e) {
          if (e && e.target !== el) {
            return
          }
          if (!e || /transform$/.test(e.propertyName)) {
            el.removeEventListener(transitionEndEvent, cb);
            el._moveCb = null;
            removeTransitionClass(el, moveClass);
          }
        });
      }
    });
  },

  methods: {
    hasMove: function hasMove (el, moveClass) {
      /* istanbul ignore if */
      if (!hasTransition) {
        return false
      }
      /* istanbul ignore if */
      if (this._hasMove) {
        return this._hasMove
      }
      // Detect whether an element with the move class applied has
      // CSS transitions. Since the element may be inside an entering
      // transition at this very moment, we make a clone of it and remove
      // all other transition classes applied to ensure only the move class
      // is applied.
      var clone = el.cloneNode();
      if (el._transitionClasses) {
        el._transitionClasses.forEach(function (cls) { removeClass(clone, cls); });
      }
      addClass(clone, moveClass);
      clone.style.display = 'none';
      this.$el.appendChild(clone);
      var info = getTransitionInfo(clone);
      this.$el.removeChild(clone);
      return (this._hasMove = info.hasTransform)
    }
  }
};

function callPendingCbs (c) {
  /* istanbul ignore if */
  if (c.elm._moveCb) {
    c.elm._moveCb();
  }
  /* istanbul ignore if */
  if (c.elm._enterCb) {
    c.elm._enterCb();
  }
}

function recordPosition (c) {
  c.data.newPos = c.elm.getBoundingClientRect();
}

function applyTranslation (c) {
  var oldPos = c.data.pos;
  var newPos = c.data.newPos;
  var dx = oldPos.left - newPos.left;
  var dy = oldPos.top - newPos.top;
  if (dx || dy) {
    c.data.moved = true;
    var s = c.elm.style;
    s.transform = s.WebkitTransform = "translate(" + dx + "px," + dy + "px)";
    s.transitionDuration = '0s';
  }
}

var platformComponents = {
  Transition: Transition,
  TransitionGroup: TransitionGroup
};

/*  */

// install platform specific utils
Vue.config.mustUseProp = mustUseProp;
Vue.config.isReservedTag = isReservedTag;
Vue.config.isReservedAttr = isReservedAttr;
Vue.config.getTagNamespace = getTagNamespace;
Vue.config.isUnknownElement = isUnknownElement;

// install platform runtime directives & components
extend(Vue.options.directives, platformDirectives);
extend(Vue.options.components, platformComponents);

// install platform patch function
Vue.prototype.__patch__ = inBrowser ? patch : noop;

// public mount method
Vue.prototype.$mount = function (
  el,
  hydrating
) {
  el = el && inBrowser ? query(el) : undefined;
  return mountComponent(this, el, hydrating)
};

// devtools global hook
/* istanbul ignore next */
if (inBrowser) {
  setTimeout(function () {
    if (config.devtools) {
      if (devtools) {
        devtools.emit('init', Vue);
      } else {
        console[console.info ? 'info' : 'log'](
          'Download the Vue Devtools extension for a better development experience:\n' +
          'https://github.com/vuejs/vue-devtools'
        );
      }
    }
    if (config.productionTip !== false &&
      typeof console !== 'undefined'
    ) {
      console[console.info ? 'info' : 'log'](
        "You are running Vue in development mode.\n" +
        "Make sure to turn on production mode when deploying for production.\n" +
        "See more tips at https://vuejs.org/guide/deployment.html"
      );
    }
  }, 0);
}

/*  */

var defaultTagRE = /\{\{((?:.|\r?\n)+?)\}\}/g;
var regexEscapeRE = /[-.*+?^${}()|[\]\/\\]/g;

var buildRegex = cached(function (delimiters) {
  var open = delimiters[0].replace(regexEscapeRE, '\\$&');
  var close = delimiters[1].replace(regexEscapeRE, '\\$&');
  return new RegExp(open + '((?:.|\\n)+?)' + close, 'g')
});



function parseText (
  text,
  delimiters
) {
  var tagRE = delimiters ? buildRegex(delimiters) : defaultTagRE;
  if (!tagRE.test(text)) {
    return
  }
  var tokens = [];
  var rawTokens = [];
  var lastIndex = tagRE.lastIndex = 0;
  var match, index, tokenValue;
  while ((match = tagRE.exec(text))) {
    index = match.index;
    // push text token
    if (index > lastIndex) {
      rawTokens.push(tokenValue = text.slice(lastIndex, index));
      tokens.push(JSON.stringify(tokenValue));
    }
    // tag token
    var exp = parseFilters(match[1].trim());
    tokens.push(("_s(" + exp + ")"));
    rawTokens.push({ '@binding': exp });
    lastIndex = index + match[0].length;
  }
  if (lastIndex < text.length) {
    rawTokens.push(tokenValue = text.slice(lastIndex));
    tokens.push(JSON.stringify(tokenValue));
  }
  return {
    expression: tokens.join('+'),
    tokens: rawTokens
  }
}

/*  */

function transformNode (el, options) {
  var warn = options.warn || baseWarn;
  var staticClass = getAndRemoveAttr(el, 'class');
  if (staticClass) {
    var res = parseText(staticClass, options.delimiters);
    if (res) {
      warn(
        "class=\"" + staticClass + "\": " +
        'Interpolation inside attributes has been removed. ' +
        'Use v-bind or the colon shorthand instead. For example, ' +
        'instead of <div class="{{ val }}">, use <div :class="val">.',
        el.rawAttrsMap['class']
      );
    }
  }
  if (staticClass) {
    el.staticClass = JSON.stringify(staticClass);
  }
  var classBinding = getBindingAttr(el, 'class', false /* getStatic */);
  if (classBinding) {
    el.classBinding = classBinding;
  }
}

function genData (el) {
  var data = '';
  if (el.staticClass) {
    data += "staticClass:" + (el.staticClass) + ",";
  }
  if (el.classBinding) {
    data += "class:" + (el.classBinding) + ",";
  }
  return data
}

var klass$1 = {
  staticKeys: ['staticClass'],
  transformNode: transformNode,
  genData: genData
};

/*  */

function transformNode$1 (el, options) {
  var warn = options.warn || baseWarn;
  var staticStyle = getAndRemoveAttr(el, 'style');
  if (staticStyle) {
    /* istanbul ignore if */
    {
      var res = parseText(staticStyle, options.delimiters);
      if (res) {
        warn(
          "style=\"" + staticStyle + "\": " +
          'Interpolation inside attributes has been removed. ' +
          'Use v-bind or the colon shorthand instead. For example, ' +
          'instead of <div style="{{ val }}">, use <div :style="val">.',
          el.rawAttrsMap['style']
        );
      }
    }
    el.staticStyle = JSON.stringify(parseStyleText(staticStyle));
  }

  var styleBinding = getBindingAttr(el, 'style', false /* getStatic */);
  if (styleBinding) {
    el.styleBinding = styleBinding;
  }
}

function genData$1 (el) {
  var data = '';
  if (el.staticStyle) {
    data += "staticStyle:" + (el.staticStyle) + ",";
  }
  if (el.styleBinding) {
    data += "style:(" + (el.styleBinding) + "),";
  }
  return data
}

var style$1 = {
  staticKeys: ['staticStyle'],
  transformNode: transformNode$1,
  genData: genData$1
};

/*  */

var decoder;

var he = {
  decode: function decode (html) {
    decoder = decoder || document.createElement('div');
    decoder.innerHTML = html;
    return decoder.textContent
  }
};

/*  */

var isUnaryTag = makeMap(
  'area,base,br,col,embed,frame,hr,img,input,isindex,keygen,' +
  'link,meta,param,source,track,wbr'
);

// Elements that you can, intentionally, leave open
// (and which close themselves)
var canBeLeftOpenTag = makeMap(
  'colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source'
);

// HTML5 tags https://html.spec.whatwg.org/multipage/indices.html#elements-3
// Phrasing Content https://html.spec.whatwg.org/multipage/dom.html#phrasing-content
var isNonPhrasingTag = makeMap(
  'address,article,aside,base,blockquote,body,caption,col,colgroup,dd,' +
  'details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,' +
  'h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,' +
  'optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,' +
  'title,tr,track'
);

/**
 * Not type-checking this file because it's mostly vendor code.
 */

// Regular Expressions for parsing tags and attributes
var attribute = /^\s*([^\s"'<>\/=]+)(?:\s*(=)\s*(?:"([^"]*)"+|'([^']*)'+|([^\s"'=<>`]+)))?/;
var dynamicArgAttribute = /^\s*((?:v-[\w-]+:|@|:|#)\[[^=]+\][^\s"'<>\/=]*)(?:\s*(=)\s*(?:"([^"]*)"+|'([^']*)'+|([^\s"'=<>`]+)))?/;
var ncname = "[a-zA-Z_][\\-\\.0-9_a-zA-Z" + (unicodeRegExp.source) + "]*";
var qnameCapture = "((?:" + ncname + "\\:)?" + ncname + ")";
var startTagOpen = new RegExp(("^<" + qnameCapture));
var startTagClose = /^\s*(\/?)>/;
var endTag = new RegExp(("^<\\/" + qnameCapture + "[^>]*>"));
var doctype = /^<!DOCTYPE [^>]+>/i;
// #7298: escape - to avoid being pased as HTML comment when inlined in page
var comment = /^<!\--/;
var conditionalComment = /^<!\[/;

// Special Elements (can contain anything)
var isPlainTextElement = makeMap('script,style,textarea', true);
var reCache = {};

var decodingMap = {
  '&lt;': '<',
  '&gt;': '>',
  '&quot;': '"',
  '&amp;': '&',
  '&#10;': '\n',
  '&#9;': '\t',
  '&#39;': "'"
};
var encodedAttr = /&(?:lt|gt|quot|amp|#39);/g;
var encodedAttrWithNewLines = /&(?:lt|gt|quot|amp|#39|#10|#9);/g;

// #5992
var isIgnoreNewlineTag = makeMap('pre,textarea', true);
var shouldIgnoreFirstNewline = function (tag, html) { return tag && isIgnoreNewlineTag(tag) && html[0] === '\n'; };

function decodeAttr (value, shouldDecodeNewlines) {
  var re = shouldDecodeNewlines ? encodedAttrWithNewLines : encodedAttr;
  return value.replace(re, function (match) { return decodingMap[match]; })
}

function parseHTML (html, options) {
  var stack = [];
  var expectHTML = options.expectHTML;
  var isUnaryTag$$1 = options.isUnaryTag || no;
  var canBeLeftOpenTag$$1 = options.canBeLeftOpenTag || no;
  var index = 0;
  var last, lastTag;
  while (html) {
    last = html;
    // Make sure we're not in a plaintext content element like script/style
    if (!lastTag || !isPlainTextElement(lastTag)) {
      var textEnd = html.indexOf('<');
      if (textEnd === 0) {
        // Comment:
        if (comment.test(html)) {
          var commentEnd = html.indexOf('-->');

          if (commentEnd >= 0) {
            if (options.shouldKeepComment) {
              options.comment(html.substring(4, commentEnd), index, index + commentEnd + 3);
            }
            advance(commentEnd + 3);
            continue
          }
        }

        // http://en.wikipedia.org/wiki/Conditional_comment#Downlevel-revealed_conditional_comment
        if (conditionalComment.test(html)) {
          var conditionalEnd = html.indexOf(']>');

          if (conditionalEnd >= 0) {
            advance(conditionalEnd + 2);
            continue
          }
        }

        // Doctype:
        var doctypeMatch = html.match(doctype);
        if (doctypeMatch) {
          advance(doctypeMatch[0].length);
          continue
        }

        // End tag:
        var endTagMatch = html.match(endTag);
        if (endTagMatch) {
          var curIndex = index;
          advance(endTagMatch[0].length);
          parseEndTag(endTagMatch[1], curIndex, index);
          continue
        }

        // Start tag:
        var startTagMatch = parseStartTag();
        if (startTagMatch) {
          handleStartTag(startTagMatch);
          if (shouldIgnoreFirstNewline(startTagMatch.tagName, html)) {
            advance(1);
          }
          continue
        }
      }

      var text = (void 0), rest = (void 0), next = (void 0);
      if (textEnd >= 0) {
        rest = html.slice(textEnd);
        while (
          !endTag.test(rest) &&
          !startTagOpen.test(rest) &&
          !comment.test(rest) &&
          !conditionalComment.test(rest)
        ) {
          // < in plain text, be forgiving and treat it as text
          next = rest.indexOf('<', 1);
          if (next < 0) { break }
          textEnd += next;
          rest = html.slice(textEnd);
        }
        text = html.substring(0, textEnd);
      }

      if (textEnd < 0) {
        text = html;
      }

      if (text) {
        advance(text.length);
      }

      if (options.chars && text) {
        options.chars(text, index - text.length, index);
      }
    } else {
      var endTagLength = 0;
      var stackedTag = lastTag.toLowerCase();
      var reStackedTag = reCache[stackedTag] || (reCache[stackedTag] = new RegExp('([\\s\\S]*?)(</' + stackedTag + '[^>]*>)', 'i'));
      var rest$1 = html.replace(reStackedTag, function (all, text, endTag) {
        endTagLength = endTag.length;
        if (!isPlainTextElement(stackedTag) && stackedTag !== 'noscript') {
          text = text
            .replace(/<!\--([\s\S]*?)-->/g, '$1') // #7298
            .replace(/<!\[CDATA\[([\s\S]*?)]]>/g, '$1');
        }
        if (shouldIgnoreFirstNewline(stackedTag, text)) {
          text = text.slice(1);
        }
        if (options.chars) {
          options.chars(text);
        }
        return ''
      });
      index += html.length - rest$1.length;
      html = rest$1;
      parseEndTag(stackedTag, index - endTagLength, index);
    }

    if (html === last) {
      options.chars && options.chars(html);
      if (!stack.length && options.warn) {
        options.warn(("Mal-formatted tag at end of template: \"" + html + "\""), { start: index + html.length });
      }
      break
    }
  }

  // Clean up any remaining tags
  parseEndTag();

  function advance (n) {
    index += n;
    html = html.substring(n);
  }

  function parseStartTag () {
    var start = html.match(startTagOpen);
    if (start) {
      var match = {
        tagName: start[1],
        attrs: [],
        start: index
      };
      advance(start[0].length);
      var end, attr;
      while (!(end = html.match(startTagClose)) && (attr = html.match(dynamicArgAttribute) || html.match(attribute))) {
        attr.start = index;
        advance(attr[0].length);
        attr.end = index;
        match.attrs.push(attr);
      }
      if (end) {
        match.unarySlash = end[1];
        advance(end[0].length);
        match.end = index;
        return match
      }
    }
  }

  function handleStartTag (match) {
    var tagName = match.tagName;
    var unarySlash = match.unarySlash;

    if (expectHTML) {
      if (lastTag === 'p' && isNonPhrasingTag(tagName)) {
        parseEndTag(lastTag);
      }
      if (canBeLeftOpenTag$$1(tagName) && lastTag === tagName) {
        parseEndTag(tagName);
      }
    }

    var unary = isUnaryTag$$1(tagName) || !!unarySlash;

    var l = match.attrs.length;
    var attrs = new Array(l);
    for (var i = 0; i < l; i++) {
      var args = match.attrs[i];
      var value = args[3] || args[4] || args[5] || '';
      var shouldDecodeNewlines = tagName === 'a' && args[1] === 'href'
        ? options.shouldDecodeNewlinesForHref
        : options.shouldDecodeNewlines;
      attrs[i] = {
        name: args[1],
        value: decodeAttr(value, shouldDecodeNewlines)
      };
      if (options.outputSourceRange) {
        attrs[i].start = args.start + args[0].match(/^\s*/).length;
        attrs[i].end = args.end;
      }
    }

    if (!unary) {
      stack.push({ tag: tagName, lowerCasedTag: tagName.toLowerCase(), attrs: attrs, start: match.start, end: match.end });
      lastTag = tagName;
    }

    if (options.start) {
      options.start(tagName, attrs, unary, match.start, match.end);
    }
  }

  function parseEndTag (tagName, start, end) {
    var pos, lowerCasedTagName;
    if (start == null) { start = index; }
    if (end == null) { end = index; }

    // Find the closest opened tag of the same type
    if (tagName) {
      lowerCasedTagName = tagName.toLowerCase();
      for (pos = stack.length - 1; pos >= 0; pos--) {
        if (stack[pos].lowerCasedTag === lowerCasedTagName) {
          break
        }
      }
    } else {
      // If no tag name is provided, clean shop
      pos = 0;
    }

    if (pos >= 0) {
      // Close all the open elements, up the stack
      for (var i = stack.length - 1; i >= pos; i--) {
        if (i > pos || !tagName &&
          options.warn
        ) {
          options.warn(
            ("tag <" + (stack[i].tag) + "> has no matching end tag."),
            { start: stack[i].start, end: stack[i].end }
          );
        }
        if (options.end) {
          options.end(stack[i].tag, start, end);
        }
      }

      // Remove the open elements from the stack
      stack.length = pos;
      lastTag = pos && stack[pos - 1].tag;
    } else if (lowerCasedTagName === 'br') {
      if (options.start) {
        options.start(tagName, [], true, start, end);
      }
    } else if (lowerCasedTagName === 'p') {
      if (options.start) {
        options.start(tagName, [], false, start, end);
      }
      if (options.end) {
        options.end(tagName, start, end);
      }
    }
  }
}

/*  */

var onRE = /^@|^v-on:/;
var dirRE = /^v-|^@|^:/;
var forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;
var forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/;
var stripParensRE = /^\(|\)$/g;
var dynamicArgRE = /^\[.*\]$/;

var argRE = /:(.*)$/;
var bindRE = /^:|^\.|^v-bind:/;
var modifierRE = /\.[^.\]]+(?=[^\]]*$)/g;

var slotRE = /^v-slot(:|$)|^#/;

var lineBreakRE = /[\r\n]/;
var whitespaceRE$1 = /\s+/g;

var invalidAttributeRE = /[\s"'<>\/=]/;

var decodeHTMLCached = cached(he.decode);

var emptySlotScopeToken = "_empty_";

// configurable state
var warn$2;
var delimiters;
var transforms;
var preTransforms;
var postTransforms;
var platformIsPreTag;
var platformMustUseProp;
var platformGetTagNamespace;
var maybeComponent;

function createASTElement (
  tag,
  attrs,
  parent
) {
  return {
    type: 1,
    tag: tag,
    attrsList: attrs,
    attrsMap: makeAttrsMap(attrs),
    rawAttrsMap: {},
    parent: parent,
    children: []
  }
}

/**
 * Convert HTML string to AST.
 */
function parse (
  template,
  options
) {
  warn$2 = options.warn || baseWarn;

  platformIsPreTag = options.isPreTag || no;
  platformMustUseProp = options.mustUseProp || no;
  platformGetTagNamespace = options.getTagNamespace || no;
  var isReservedTag = options.isReservedTag || no;
  maybeComponent = function (el) { return !!el.component || !isReservedTag(el.tag); };

  transforms = pluckModuleFunction(options.modules, 'transformNode');
  preTransforms = pluckModuleFunction(options.modules, 'preTransformNode');
  postTransforms = pluckModuleFunction(options.modules, 'postTransformNode');

  delimiters = options.delimiters;

  var stack = [];
  var preserveWhitespace = options.preserveWhitespace !== false;
  var whitespaceOption = options.whitespace;
  var root;
  var currentParent;
  var inVPre = false;
  var inPre = false;
  var warned = false;

  function warnOnce (msg, range) {
    if (!warned) {
      warned = true;
      warn$2(msg, range);
    }
  }

  function closeElement (element) {
    trimEndingWhitespace(element);
    if (!inVPre && !element.processed) {
      element = processElement(element, options);
    }
    // tree management
    if (!stack.length && element !== root) {
      // allow root elements with v-if, v-else-if and v-else
      if (root.if && (element.elseif || element.else)) {
        {
          checkRootConstraints(element);
        }
        addIfCondition(root, {
          exp: element.elseif,
          block: element
        });
      } else {
        warnOnce(
          "Component template should contain exactly one root element. " +
          "If you are using v-if on multiple elements, " +
          "use v-else-if to chain them instead.",
          { start: element.start }
        );
      }
    }
    if (currentParent && !element.forbidden) {
      if (element.elseif || element.else) {
        processIfConditions(element, currentParent);
      } else {
        if (element.slotScope) {
          // scoped slot
          // keep it in the children list so that v-else(-if) conditions can
          // find it as the prev node.
          var name = element.slotTarget || '"default"'
          ;(currentParent.scopedSlots || (currentParent.scopedSlots = {}))[name] = element;
        }
        currentParent.children.push(element);
        element.parent = currentParent;
      }
    }

    // final children cleanup
    // filter out scoped slots
    element.children = element.children.filter(function (c) { return !(c).slotScope; });
    // remove trailing whitespace node again
    trimEndingWhitespace(element);

    // check pre state
    if (element.pre) {
      inVPre = false;
    }
    if (platformIsPreTag(element.tag)) {
      inPre = false;
    }
    // apply post-transforms
    for (var i = 0; i < postTransforms.length; i++) {
      postTransforms[i](element, options);
    }
  }

  function trimEndingWhitespace (el) {
    // remove trailing whitespace node
    if (!inPre) {
      var lastNode;
      while (
        (lastNode = el.children[el.children.length - 1]) &&
        lastNode.type === 3 &&
        lastNode.text === ' '
      ) {
        el.children.pop();
      }
    }
  }

  function checkRootConstraints (el) {
    if (el.tag === 'slot' || el.tag === 'template') {
      warnOnce(
        "Cannot use <" + (el.tag) + "> as component root element because it may " +
        'contain multiple nodes.',
        { start: el.start }
      );
    }
    if (el.attrsMap.hasOwnProperty('v-for')) {
      warnOnce(
        'Cannot use v-for on stateful component root element because ' +
        'it renders multiple elements.',
        el.rawAttrsMap['v-for']
      );
    }
  }

  parseHTML(template, {
    warn: warn$2,
    expectHTML: options.expectHTML,
    isUnaryTag: options.isUnaryTag,
    canBeLeftOpenTag: options.canBeLeftOpenTag,
    shouldDecodeNewlines: options.shouldDecodeNewlines,
    shouldDecodeNewlinesForHref: options.shouldDecodeNewlinesForHref,
    shouldKeepComment: options.comments,
    outputSourceRange: options.outputSourceRange,
    start: function start (tag, attrs, unary, start$1, end) {
      // check namespace.
      // inherit parent ns if there is one
      var ns = (currentParent && currentParent.ns) || platformGetTagNamespace(tag);

      // handle IE svg bug
      /* istanbul ignore if */
      if (isIE && ns === 'svg') {
        attrs = guardIESVGBug(attrs);
      }

      var element = createASTElement(tag, attrs, currentParent);
      if (ns) {
        element.ns = ns;
      }

      {
        if (options.outputSourceRange) {
          element.start = start$1;
          element.end = end;
          element.rawAttrsMap = element.attrsList.reduce(function (cumulated, attr) {
            cumulated[attr.name] = attr;
            return cumulated
          }, {});
        }
        attrs.forEach(function (attr) {
          if (invalidAttributeRE.test(attr.name)) {
            warn$2(
              "Invalid dynamic argument expression: attribute names cannot contain " +
              "spaces, quotes, <, >, / or =.",
              {
                start: attr.start + attr.name.indexOf("["),
                end: attr.start + attr.name.length
              }
            );
          }
        });
      }

      if (isForbiddenTag(element) && !isServerRendering()) {
        element.forbidden = true;
        warn$2(
          'Templates should only be responsible for mapping the state to the ' +
          'UI. Avoid placing tags with side-effects in your templates, such as ' +
          "<" + tag + ">" + ', as they will not be parsed.',
          { start: element.start }
        );
      }

      // apply pre-transforms
      for (var i = 0; i < preTransforms.length; i++) {
        element = preTransforms[i](element, options) || element;
      }

      if (!inVPre) {
        processPre(element);
        if (element.pre) {
          inVPre = true;
        }
      }
      if (platformIsPreTag(element.tag)) {
        inPre = true;
      }
      if (inVPre) {
        processRawAttrs(element);
      } else if (!element.processed) {
        // structural directives
        processFor(element);
        processIf(element);
        processOnce(element);
      }

      if (!root) {
        root = element;
        {
          checkRootConstraints(root);
        }
      }

      if (!unary) {
        currentParent = element;
        stack.push(element);
      } else {
        closeElement(element);
      }
    },

    end: function end (tag, start, end$1) {
      var element = stack[stack.length - 1];
      // pop stack
      stack.length -= 1;
      currentParent = stack[stack.length - 1];
      if (options.outputSourceRange) {
        element.end = end$1;
      }
      closeElement(element);
    },

    chars: function chars (text, start, end) {
      if (!currentParent) {
        {
          if (text === template) {
            warnOnce(
              'Component template requires a root element, rather than just text.',
              { start: start }
            );
          } else if ((text = text.trim())) {
            warnOnce(
              ("text \"" + text + "\" outside root element will be ignored."),
              { start: start }
            );
          }
        }
        return
      }
      // IE textarea placeholder bug
      /* istanbul ignore if */
      if (isIE &&
        currentParent.tag === 'textarea' &&
        currentParent.attrsMap.placeholder === text
      ) {
        return
      }
      var children = currentParent.children;
      if (inPre || text.trim()) {
        text = isTextTag(currentParent) ? text : decodeHTMLCached(text);
      } else if (!children.length) {
        // remove the whitespace-only node right after an opening tag
        text = '';
      } else if (whitespaceOption) {
        if (whitespaceOption === 'condense') {
          // in condense mode, remove the whitespace node if it contains
          // line break, otherwise condense to a single space
          text = lineBreakRE.test(text) ? '' : ' ';
        } else {
          text = ' ';
        }
      } else {
        text = preserveWhitespace ? ' ' : '';
      }
      if (text) {
        if (!inPre && whitespaceOption === 'condense') {
          // condense consecutive whitespaces into single space
          text = text.replace(whitespaceRE$1, ' ');
        }
        var res;
        var child;
        if (!inVPre && text !== ' ' && (res = parseText(text, delimiters))) {
          child = {
            type: 2,
            expression: res.expression,
            tokens: res.tokens,
            text: text
          };
        } else if (text !== ' ' || !children.length || children[children.length - 1].text !== ' ') {
          child = {
            type: 3,
            text: text
          };
        }
        if (child) {
          if (options.outputSourceRange) {
            child.start = start;
            child.end = end;
          }
          children.push(child);
        }
      }
    },
    comment: function comment (text, start, end) {
      // adding anyting as a sibling to the root node is forbidden
      // comments should still be allowed, but ignored
      if (currentParent) {
        var child = {
          type: 3,
          text: text,
          isComment: true
        };
        if (options.outputSourceRange) {
          child.start = start;
          child.end = end;
        }
        currentParent.children.push(child);
      }
    }
  });
  return root
}

function processPre (el) {
  if (getAndRemoveAttr(el, 'v-pre') != null) {
    el.pre = true;
  }
}

function processRawAttrs (el) {
  var list = el.attrsList;
  var len = list.length;
  if (len) {
    var attrs = el.attrs = new Array(len);
    for (var i = 0; i < len; i++) {
      attrs[i] = {
        name: list[i].name,
        value: JSON.stringify(list[i].value)
      };
      if (list[i].start != null) {
        attrs[i].start = list[i].start;
        attrs[i].end = list[i].end;
      }
    }
  } else if (!el.pre) {
    // non root node in pre blocks with no attributes
    el.plain = true;
  }
}

function processElement (
  element,
  options
) {
  processKey(element);

  // determine whether this is a plain element after
  // removing structural attributes
  element.plain = (
    !element.key &&
    !element.scopedSlots &&
    !element.attrsList.length
  );

  processRef(element);
  processSlotContent(element);
  processSlotOutlet(element);
  processComponent(element);
  for (var i = 0; i < transforms.length; i++) {
    element = transforms[i](element, options) || element;
  }
  processAttrs(element);
  return element
}

function processKey (el) {
  var exp = getBindingAttr(el, 'key');
  if (exp) {
    {
      if (el.tag === 'template') {
        warn$2(
          "<template> cannot be keyed. Place the key on real elements instead.",
          getRawBindingAttr(el, 'key')
        );
      }
      if (el.for) {
        var iterator = el.iterator2 || el.iterator1;
        var parent = el.parent;
        if (iterator && iterator === exp && parent && parent.tag === 'transition-group') {
          warn$2(
            "Do not use v-for index as key on <transition-group> children, " +
            "this is the same as not using keys.",
            getRawBindingAttr(el, 'key'),
            true /* tip */
          );
        }
      }
    }
    el.key = exp;
  }
}

function processRef (el) {
  var ref = getBindingAttr(el, 'ref');
  if (ref) {
    el.ref = ref;
    el.refInFor = checkInFor(el);
  }
}

function processFor (el) {
  var exp;
  if ((exp = getAndRemoveAttr(el, 'v-for'))) {
    var res = parseFor(exp);
    if (res) {
      extend(el, res);
    } else {
      warn$2(
        ("Invalid v-for expression: " + exp),
        el.rawAttrsMap['v-for']
      );
    }
  }
}



function parseFor (exp) {
  var inMatch = exp.match(forAliasRE);
  if (!inMatch) { return }
  var res = {};
  res.for = inMatch[2].trim();
  var alias = inMatch[1].trim().replace(stripParensRE, '');
  var iteratorMatch = alias.match(forIteratorRE);
  if (iteratorMatch) {
    res.alias = alias.replace(forIteratorRE, '').trim();
    res.iterator1 = iteratorMatch[1].trim();
    if (iteratorMatch[2]) {
      res.iterator2 = iteratorMatch[2].trim();
    }
  } else {
    res.alias = alias;
  }
  return res
}

function processIf (el) {
  var exp = getAndRemoveAttr(el, 'v-if');
  if (exp) {
    el.if = exp;
    addIfCondition(el, {
      exp: exp,
      block: el
    });
  } else {
    if (getAndRemoveAttr(el, 'v-else') != null) {
      el.else = true;
    }
    var elseif = getAndRemoveAttr(el, 'v-else-if');
    if (elseif) {
      el.elseif = elseif;
    }
  }
}

function processIfConditions (el, parent) {
  var prev = findPrevElement(parent.children);
  if (prev && prev.if) {
    addIfCondition(prev, {
      exp: el.elseif,
      block: el
    });
  } else {
    warn$2(
      "v-" + (el.elseif ? ('else-if="' + el.elseif + '"') : 'else') + " " +
      "used on element <" + (el.tag) + "> without corresponding v-if.",
      el.rawAttrsMap[el.elseif ? 'v-else-if' : 'v-else']
    );
  }
}

function findPrevElement (children) {
  var i = children.length;
  while (i--) {
    if (children[i].type === 1) {
      return children[i]
    } else {
      if (children[i].text !== ' ') {
        warn$2(
          "text \"" + (children[i].text.trim()) + "\" between v-if and v-else(-if) " +
          "will be ignored.",
          children[i]
        );
      }
      children.pop();
    }
  }
}

function addIfCondition (el, condition) {
  if (!el.ifConditions) {
    el.ifConditions = [];
  }
  el.ifConditions.push(condition);
}

function processOnce (el) {
  var once$$1 = getAndRemoveAttr(el, 'v-once');
  if (once$$1 != null) {
    el.once = true;
  }
}

// handle content being passed to a component as slot,
// e.g. <template slot="xxx">, <div slot-scope="xxx">
function processSlotContent (el) {
  var slotScope;
  if (el.tag === 'template') {
    slotScope = getAndRemoveAttr(el, 'scope');
    /* istanbul ignore if */
    if (slotScope) {
      warn$2(
        "the \"scope\" attribute for scoped slots have been deprecated and " +
        "replaced by \"slot-scope\" since 2.5. The new \"slot-scope\" attribute " +
        "can also be used on plain elements in addition to <template> to " +
        "denote scoped slots.",
        el.rawAttrsMap['scope'],
        true
      );
    }
    el.slotScope = slotScope || getAndRemoveAttr(el, 'slot-scope');
  } else if ((slotScope = getAndRemoveAttr(el, 'slot-scope'))) {
    /* istanbul ignore if */
    if (el.attrsMap['v-for']) {
      warn$2(
        "Ambiguous combined usage of slot-scope and v-for on <" + (el.tag) + "> " +
        "(v-for takes higher priority). Use a wrapper <template> for the " +
        "scoped slot to make it clearer.",
        el.rawAttrsMap['slot-scope'],
        true
      );
    }
    el.slotScope = slotScope;
  }

  // slot="xxx"
  var slotTarget = getBindingAttr(el, 'slot');
  if (slotTarget) {
    el.slotTarget = slotTarget === '""' ? '"default"' : slotTarget;
    el.slotTargetDynamic = !!(el.attrsMap[':slot'] || el.attrsMap['v-bind:slot']);
    // preserve slot as an attribute for native shadow DOM compat
    // only for non-scoped slots.
    if (el.tag !== 'template' && !el.slotScope) {
      addAttr(el, 'slot', slotTarget, getRawBindingAttr(el, 'slot'));
    }
  }

  // 2.6 v-slot syntax
  {
    if (el.tag === 'template') {
      // v-slot on <template>
      var slotBinding = getAndRemoveAttrByRegex(el, slotRE);
      if (slotBinding) {
        {
          if (el.slotTarget || el.slotScope) {
            warn$2(
              "Unexpected mixed usage of different slot syntaxes.",
              el
            );
          }
          if (el.parent && !maybeComponent(el.parent)) {
            warn$2(
              "<template v-slot> can only appear at the root level inside " +
              "the receiving the component",
              el
            );
          }
        }
        var ref = getSlotName(slotBinding);
        var name = ref.name;
        var dynamic = ref.dynamic;
        el.slotTarget = name;
        el.slotTargetDynamic = dynamic;
        el.slotScope = slotBinding.value || emptySlotScopeToken; // force it into a scoped slot for perf
      }
    } else {
      // v-slot on component, denotes default slot
      var slotBinding$1 = getAndRemoveAttrByRegex(el, slotRE);
      if (slotBinding$1) {
        {
          if (!maybeComponent(el)) {
            warn$2(
              "v-slot can only be used on components or <template>.",
              slotBinding$1
            );
          }
          if (el.slotScope || el.slotTarget) {
            warn$2(
              "Unexpected mixed usage of different slot syntaxes.",
              el
            );
          }
          if (el.scopedSlots) {
            warn$2(
              "To avoid scope ambiguity, the default slot should also use " +
              "<template> syntax when there are other named slots.",
              slotBinding$1
            );
          }
        }
        // add the component's children to its default slot
        var slots = el.scopedSlots || (el.scopedSlots = {});
        var ref$1 = getSlotName(slotBinding$1);
        var name$1 = ref$1.name;
        var dynamic$1 = ref$1.dynamic;
        var slotContainer = slots[name$1] = createASTElement('template', [], el);
        slotContainer.slotTarget = name$1;
        slotContainer.slotTargetDynamic = dynamic$1;
        slotContainer.children = el.children.filter(function (c) {
          if (!c.slotScope) {
            c.parent = slotContainer;
            return true
          }
        });
        slotContainer.slotScope = slotBinding$1.value || emptySlotScopeToken;
        // remove children as they are returned from scopedSlots now
        el.children = [];
        // mark el non-plain so data gets generated
        el.plain = false;
      }
    }
  }
}

function getSlotName (binding) {
  var name = binding.name.replace(slotRE, '');
  if (!name) {
    if (binding.name[0] !== '#') {
      name = 'default';
    } else {
      warn$2(
        "v-slot shorthand syntax requires a slot name.",
        binding
      );
    }
  }
  return dynamicArgRE.test(name)
    // dynamic [name]
    ? { name: name.slice(1, -1), dynamic: true }
    // static name
    : { name: ("\"" + name + "\""), dynamic: false }
}

// handle <slot/> outlets
function processSlotOutlet (el) {
  if (el.tag === 'slot') {
    el.slotName = getBindingAttr(el, 'name');
    if (el.key) {
      warn$2(
        "`key` does not work on <slot> because slots are abstract outlets " +
        "and can possibly expand into multiple elements. " +
        "Use the key on a wrapping element instead.",
        getRawBindingAttr(el, 'key')
      );
    }
  }
}

function processComponent (el) {
  var binding;
  if ((binding = getBindingAttr(el, 'is'))) {
    el.component = binding;
  }
  if (getAndRemoveAttr(el, 'inline-template') != null) {
    el.inlineTemplate = true;
  }
}

function processAttrs (el) {
  var list = el.attrsList;
  var i, l, name, rawName, value, modifiers, syncGen, isDynamic;
  for (i = 0, l = list.length; i < l; i++) {
    name = rawName = list[i].name;
    value = list[i].value;
    if (dirRE.test(name)) {
      // mark element as dynamic
      el.hasBindings = true;
      // modifiers
      modifiers = parseModifiers(name.replace(dirRE, ''));
      // support .foo shorthand syntax for the .prop modifier
      if (modifiers) {
        name = name.replace(modifierRE, '');
      }
      if (bindRE.test(name)) { // v-bind
        name = name.replace(bindRE, '');
        value = parseFilters(value);
        isDynamic = dynamicArgRE.test(name);
        if (isDynamic) {
          name = name.slice(1, -1);
        }
        if (
          value.trim().length === 0
        ) {
          warn$2(
            ("The value for a v-bind expression cannot be empty. Found in \"v-bind:" + name + "\"")
          );
        }
        if (modifiers) {
          if (modifiers.prop && !isDynamic) {
            name = camelize(name);
            if (name === 'innerHtml') { name = 'innerHTML'; }
          }
          if (modifiers.camel && !isDynamic) {
            name = camelize(name);
          }
          if (modifiers.sync) {
            syncGen = genAssignmentCode(value, "$event");
            if (!isDynamic) {
              addHandler(
                el,
                ("update:" + (camelize(name))),
                syncGen,
                null,
                false,
                warn$2,
                list[i]
              );
              if (hyphenate(name) !== camelize(name)) {
                addHandler(
                  el,
                  ("update:" + (hyphenate(name))),
                  syncGen,
                  null,
                  false,
                  warn$2,
                  list[i]
                );
              }
            } else {
              // handler w/ dynamic event name
              addHandler(
                el,
                ("\"update:\"+(" + name + ")"),
                syncGen,
                null,
                false,
                warn$2,
                list[i],
                true // dynamic
              );
            }
          }
        }
        if ((modifiers && modifiers.prop) || (
          !el.component && platformMustUseProp(el.tag, el.attrsMap.type, name)
        )) {
          addProp(el, name, value, list[i], isDynamic);
        } else {
          addAttr(el, name, value, list[i], isDynamic);
        }
      } else if (onRE.test(name)) { // v-on
        name = name.replace(onRE, '');
        isDynamic = dynamicArgRE.test(name);
        if (isDynamic) {
          name = name.slice(1, -1);
        }
        addHandler(el, name, value, modifiers, false, warn$2, list[i], isDynamic);
      } else { // normal directives
        name = name.replace(dirRE, '');
        // parse arg
        var argMatch = name.match(argRE);
        var arg = argMatch && argMatch[1];
        isDynamic = false;
        if (arg) {
          name = name.slice(0, -(arg.length + 1));
          if (dynamicArgRE.test(arg)) {
            arg = arg.slice(1, -1);
            isDynamic = true;
          }
        }
        addDirective(el, name, rawName, value, arg, isDynamic, modifiers, list[i]);
        if (name === 'model') {
          checkForAliasModel(el, value);
        }
      }
    } else {
      // literal attribute
      {
        var res = parseText(value, delimiters);
        if (res) {
          warn$2(
            name + "=\"" + value + "\": " +
            'Interpolation inside attributes has been removed. ' +
            'Use v-bind or the colon shorthand instead. For example, ' +
            'instead of <div id="{{ val }}">, use <div :id="val">.',
            list[i]
          );
        }
      }
      addAttr(el, name, JSON.stringify(value), list[i]);
      // #6887 firefox doesn't update muted state if set via attribute
      // even immediately after element creation
      if (!el.component &&
          name === 'muted' &&
          platformMustUseProp(el.tag, el.attrsMap.type, name)) {
        addProp(el, name, 'true', list[i]);
      }
    }
  }
}

function checkInFor (el) {
  var parent = el;
  while (parent) {
    if (parent.for !== undefined) {
      return true
    }
    parent = parent.parent;
  }
  return false
}

function parseModifiers (name) {
  var match = name.match(modifierRE);
  if (match) {
    var ret = {};
    match.forEach(function (m) { ret[m.slice(1)] = true; });
    return ret
  }
}

function makeAttrsMap (attrs) {
  var map = {};
  for (var i = 0, l = attrs.length; i < l; i++) {
    if (
      map[attrs[i].name] && !isIE && !isEdge
    ) {
      warn$2('duplicate attribute: ' + attrs[i].name, attrs[i]);
    }
    map[attrs[i].name] = attrs[i].value;
  }
  return map
}

// for script (e.g. type="x/template") or style, do not decode content
function isTextTag (el) {
  return el.tag === 'script' || el.tag === 'style'
}

function isForbiddenTag (el) {
  return (
    el.tag === 'style' ||
    (el.tag === 'script' && (
      !el.attrsMap.type ||
      el.attrsMap.type === 'text/javascript'
    ))
  )
}

var ieNSBug = /^xmlns:NS\d+/;
var ieNSPrefix = /^NS\d+:/;

/* istanbul ignore next */
function guardIESVGBug (attrs) {
  var res = [];
  for (var i = 0; i < attrs.length; i++) {
    var attr = attrs[i];
    if (!ieNSBug.test(attr.name)) {
      attr.name = attr.name.replace(ieNSPrefix, '');
      res.push(attr);
    }
  }
  return res
}

function checkForAliasModel (el, value) {
  var _el = el;
  while (_el) {
    if (_el.for && _el.alias === value) {
      warn$2(
        "<" + (el.tag) + " v-model=\"" + value + "\">: " +
        "You are binding v-model directly to a v-for iteration alias. " +
        "This will not be able to modify the v-for source array because " +
        "writing to the alias is like modifying a function local variable. " +
        "Consider using an array of objects and use v-model on an object property instead.",
        el.rawAttrsMap['v-model']
      );
    }
    _el = _el.parent;
  }
}

/*  */

function preTransformNode (el, options) {
  if (el.tag === 'input') {
    var map = el.attrsMap;
    if (!map['v-model']) {
      return
    }

    var typeBinding;
    if (map[':type'] || map['v-bind:type']) {
      typeBinding = getBindingAttr(el, 'type');
    }
    if (!map.type && !typeBinding && map['v-bind']) {
      typeBinding = "(" + (map['v-bind']) + ").type";
    }

    if (typeBinding) {
      var ifCondition = getAndRemoveAttr(el, 'v-if', true);
      var ifConditionExtra = ifCondition ? ("&&(" + ifCondition + ")") : "";
      var hasElse = getAndRemoveAttr(el, 'v-else', true) != null;
      var elseIfCondition = getAndRemoveAttr(el, 'v-else-if', true);
      // 1. checkbox
      var branch0 = cloneASTElement(el);
      // process for on the main node
      processFor(branch0);
      addRawAttr(branch0, 'type', 'checkbox');
      processElement(branch0, options);
      branch0.processed = true; // prevent it from double-processed
      branch0.if = "(" + typeBinding + ")==='checkbox'" + ifConditionExtra;
      addIfCondition(branch0, {
        exp: branch0.if,
        block: branch0
      });
      // 2. add radio else-if condition
      var branch1 = cloneASTElement(el);
      getAndRemoveAttr(branch1, 'v-for', true);
      addRawAttr(branch1, 'type', 'radio');
      processElement(branch1, options);
      addIfCondition(branch0, {
        exp: "(" + typeBinding + ")==='radio'" + ifConditionExtra,
        block: branch1
      });
      // 3. other
      var branch2 = cloneASTElement(el);
      getAndRemoveAttr(branch2, 'v-for', true);
      addRawAttr(branch2, ':type', typeBinding);
      processElement(branch2, options);
      addIfCondition(branch0, {
        exp: ifCondition,
        block: branch2
      });

      if (hasElse) {
        branch0.else = true;
      } else if (elseIfCondition) {
        branch0.elseif = elseIfCondition;
      }

      return branch0
    }
  }
}

function cloneASTElement (el) {
  return createASTElement(el.tag, el.attrsList.slice(), el.parent)
}

var model$1 = {
  preTransformNode: preTransformNode
};

var modules$1 = [
  klass$1,
  style$1,
  model$1
];

/*  */

function text (el, dir) {
  if (dir.value) {
    addProp(el, 'textContent', ("_s(" + (dir.value) + ")"), dir);
  }
}

/*  */

function html (el, dir) {
  if (dir.value) {
    addProp(el, 'innerHTML', ("_s(" + (dir.value) + ")"), dir);
  }
}

var directives$1 = {
  model: model,
  text: text,
  html: html
};

/*  */

var baseOptions = {
  expectHTML: true,
  modules: modules$1,
  directives: directives$1,
  isPreTag: isPreTag,
  isUnaryTag: isUnaryTag,
  mustUseProp: mustUseProp,
  canBeLeftOpenTag: canBeLeftOpenTag,
  isReservedTag: isReservedTag,
  getTagNamespace: getTagNamespace,
  staticKeys: genStaticKeys(modules$1)
};

/*  */

var isStaticKey;
var isPlatformReservedTag;

var genStaticKeysCached = cached(genStaticKeys$1);

/**
 * Goal of the optimizer: walk the generated template AST tree
 * and detect sub-trees that are purely static, i.e. parts of
 * the DOM that never needs to change.
 *
 * Once we detect these sub-trees, we can:
 *
 * 1. Hoist them into constants, so that we no longer need to
 *    create fresh nodes for them on each re-render;
 * 2. Completely skip them in the patching process.
 */
function optimize (root, options) {
  if (!root) { return }
  isStaticKey = genStaticKeysCached(options.staticKeys || '');
  isPlatformReservedTag = options.isReservedTag || no;
  // first pass: mark all non-static nodes.
  markStatic$1(root);
  // second pass: mark static roots.
  markStaticRoots(root, false);
}

function genStaticKeys$1 (keys) {
  return makeMap(
    'type,tag,attrsList,attrsMap,plain,parent,children,attrs,start,end,rawAttrsMap' +
    (keys ? ',' + keys : '')
  )
}

function markStatic$1 (node) {
  node.static = isStatic(node);
  if (node.type === 1) {
    // do not make component slot content static. this avoids
    // 1. components not able to mutate slot nodes
    // 2. static slot content fails for hot-reloading
    if (
      !isPlatformReservedTag(node.tag) &&
      node.tag !== 'slot' &&
      node.attrsMap['inline-template'] == null
    ) {
      return
    }
    for (var i = 0, l = node.children.length; i < l; i++) {
      var child = node.children[i];
      markStatic$1(child);
      if (!child.static) {
        node.static = false;
      }
    }
    if (node.ifConditions) {
      for (var i$1 = 1, l$1 = node.ifConditions.length; i$1 < l$1; i$1++) {
        var block = node.ifConditions[i$1].block;
        markStatic$1(block);
        if (!block.static) {
          node.static = false;
        }
      }
    }
  }
}

function markStaticRoots (node, isInFor) {
  if (node.type === 1) {
    if (node.static || node.once) {
      node.staticInFor = isInFor;
    }
    // For a node to qualify as a static root, it should have children that
    // are not just static text. Otherwise the cost of hoisting out will
    // outweigh the benefits and it's better off to just always render it fresh.
    if (node.static && node.children.length && !(
      node.children.length === 1 &&
      node.children[0].type === 3
    )) {
      node.staticRoot = true;
      return
    } else {
      node.staticRoot = false;
    }
    if (node.children) {
      for (var i = 0, l = node.children.length; i < l; i++) {
        markStaticRoots(node.children[i], isInFor || !!node.for);
      }
    }
    if (node.ifConditions) {
      for (var i$1 = 1, l$1 = node.ifConditions.length; i$1 < l$1; i$1++) {
        markStaticRoots(node.ifConditions[i$1].block, isInFor);
      }
    }
  }
}

function isStatic (node) {
  if (node.type === 2) { // expression
    return false
  }
  if (node.type === 3) { // text
    return true
  }
  return !!(node.pre || (
    !node.hasBindings && // no dynamic bindings
    !node.if && !node.for && // not v-if or v-for or v-else
    !isBuiltInTag(node.tag) && // not a built-in
    isPlatformReservedTag(node.tag) && // not a component
    !isDirectChildOfTemplateFor(node) &&
    Object.keys(node).every(isStaticKey)
  ))
}

function isDirectChildOfTemplateFor (node) {
  while (node.parent) {
    node = node.parent;
    if (node.tag !== 'template') {
      return false
    }
    if (node.for) {
      return true
    }
  }
  return false
}

/*  */

var fnExpRE = /^([\w$_]+|\([^)]*?\))\s*=>|^function\s*(?:[\w$]+)?\s*\(/;
var fnInvokeRE = /\([^)]*?\);*$/;
var simplePathRE = /^[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['[^']*?']|\["[^"]*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*$/;

// KeyboardEvent.keyCode aliases
var keyCodes = {
  esc: 27,
  tab: 9,
  enter: 13,
  space: 32,
  up: 38,
  left: 37,
  right: 39,
  down: 40,
  'delete': [8, 46]
};

// KeyboardEvent.key aliases
var keyNames = {
  // #7880: IE11 and Edge use `Esc` for Escape key name.
  esc: ['Esc', 'Escape'],
  tab: 'Tab',
  enter: 'Enter',
  // #9112: IE11 uses `Spacebar` for Space key name.
  space: [' ', 'Spacebar'],
  // #7806: IE11 uses key names without `Arrow` prefix for arrow keys.
  up: ['Up', 'ArrowUp'],
  left: ['Left', 'ArrowLeft'],
  right: ['Right', 'ArrowRight'],
  down: ['Down', 'ArrowDown'],
  // #9112: IE11 uses `Del` for Delete key name.
  'delete': ['Backspace', 'Delete', 'Del']
};

// #4868: modifiers that prevent the execution of the listener
// need to explicitly return null so that we can determine whether to remove
// the listener for .once
var genGuard = function (condition) { return ("if(" + condition + ")return null;"); };

var modifierCode = {
  stop: '$event.stopPropagation();',
  prevent: '$event.preventDefault();',
  self: genGuard("$event.target !== $event.currentTarget"),
  ctrl: genGuard("!$event.ctrlKey"),
  shift: genGuard("!$event.shiftKey"),
  alt: genGuard("!$event.altKey"),
  meta: genGuard("!$event.metaKey"),
  left: genGuard("'button' in $event && $event.button !== 0"),
  middle: genGuard("'button' in $event && $event.button !== 1"),
  right: genGuard("'button' in $event && $event.button !== 2")
};

function genHandlers (
  events,
  isNative
) {
  var prefix = isNative ? 'nativeOn:' : 'on:';
  var staticHandlers = "";
  var dynamicHandlers = "";
  for (var name in events) {
    var handlerCode = genHandler(events[name]);
    if (events[name] && events[name].dynamic) {
      dynamicHandlers += name + "," + handlerCode + ",";
    } else {
      staticHandlers += "\"" + name + "\":" + handlerCode + ",";
    }
  }
  staticHandlers = "{" + (staticHandlers.slice(0, -1)) + "}";
  if (dynamicHandlers) {
    return prefix + "_d(" + staticHandlers + ",[" + (dynamicHandlers.slice(0, -1)) + "])"
  } else {
    return prefix + staticHandlers
  }
}

function genHandler (handler) {
  if (!handler) {
    return 'function(){}'
  }

  if (Array.isArray(handler)) {
    return ("[" + (handler.map(function (handler) { return genHandler(handler); }).join(',')) + "]")
  }

  var isMethodPath = simplePathRE.test(handler.value);
  var isFunctionExpression = fnExpRE.test(handler.value);
  var isFunctionInvocation = simplePathRE.test(handler.value.replace(fnInvokeRE, ''));

  if (!handler.modifiers) {
    if (isMethodPath || isFunctionExpression) {
      return handler.value
    }
    return ("function($event){" + (isFunctionInvocation ? ("return " + (handler.value)) : handler.value) + "}") // inline statement
  } else {
    var code = '';
    var genModifierCode = '';
    var keys = [];
    for (var key in handler.modifiers) {
      if (modifierCode[key]) {
        genModifierCode += modifierCode[key];
        // left/right
        if (keyCodes[key]) {
          keys.push(key);
        }
      } else if (key === 'exact') {
        var modifiers = (handler.modifiers);
        genModifierCode += genGuard(
          ['ctrl', 'shift', 'alt', 'meta']
            .filter(function (keyModifier) { return !modifiers[keyModifier]; })
            .map(function (keyModifier) { return ("$event." + keyModifier + "Key"); })
            .join('||')
        );
      } else {
        keys.push(key);
      }
    }
    if (keys.length) {
      code += genKeyFilter(keys);
    }
    // Make sure modifiers like prevent and stop get executed after key filtering
    if (genModifierCode) {
      code += genModifierCode;
    }
    var handlerCode = isMethodPath
      ? ("return " + (handler.value) + "($event)")
      : isFunctionExpression
        ? ("return (" + (handler.value) + ")($event)")
        : isFunctionInvocation
          ? ("return " + (handler.value))
          : handler.value;
    return ("function($event){" + code + handlerCode + "}")
  }
}

function genKeyFilter (keys) {
  return (
    // make sure the key filters only apply to KeyboardEvents
    // #9441: can't use 'keyCode' in $event because Chrome autofill fires fake
    // key events that do not have keyCode property...
    "if(!$event.type.indexOf('key')&&" +
    (keys.map(genFilterCode).join('&&')) + ")return null;"
  )
}

function genFilterCode (key) {
  var keyVal = parseInt(key, 10);
  if (keyVal) {
    return ("$event.keyCode!==" + keyVal)
  }
  var keyCode = keyCodes[key];
  var keyName = keyNames[key];
  return (
    "_k($event.keyCode," +
    (JSON.stringify(key)) + "," +
    (JSON.stringify(keyCode)) + "," +
    "$event.key," +
    "" + (JSON.stringify(keyName)) +
    ")"
  )
}

/*  */

function on (el, dir) {
  if (dir.modifiers) {
    warn("v-on without argument does not support modifiers.");
  }
  el.wrapListeners = function (code) { return ("_g(" + code + "," + (dir.value) + ")"); };
}

/*  */

function bind$1 (el, dir) {
  el.wrapData = function (code) {
    return ("_b(" + code + ",'" + (el.tag) + "'," + (dir.value) + "," + (dir.modifiers && dir.modifiers.prop ? 'true' : 'false') + (dir.modifiers && dir.modifiers.sync ? ',true' : '') + ")")
  };
}

/*  */

var baseDirectives = {
  on: on,
  bind: bind$1,
  cloak: noop
};

/*  */





var CodegenState = function CodegenState (options) {
  this.options = options;
  this.warn = options.warn || baseWarn;
  this.transforms = pluckModuleFunction(options.modules, 'transformCode');
  this.dataGenFns = pluckModuleFunction(options.modules, 'genData');
  this.directives = extend(extend({}, baseDirectives), options.directives);
  var isReservedTag = options.isReservedTag || no;
  this.maybeComponent = function (el) { return !!el.component || !isReservedTag(el.tag); };
  this.onceId = 0;
  this.staticRenderFns = [];
  this.pre = false;
};



function generate (
  ast,
  options
) {
  var state = new CodegenState(options);
  var code = ast ? genElement(ast, state) : '_c("div")';
  return {
    render: ("with(this){return " + code + "}"),
    staticRenderFns: state.staticRenderFns
  }
}

function genElement (el, state) {
  if (el.parent) {
    el.pre = el.pre || el.parent.pre;
  }

  if (el.staticRoot && !el.staticProcessed) {
    return genStatic(el, state)
  } else if (el.once && !el.onceProcessed) {
    return genOnce(el, state)
  } else if (el.for && !el.forProcessed) {
    return genFor(el, state)
  } else if (el.if && !el.ifProcessed) {
    return genIf(el, state)
  } else if (el.tag === 'template' && !el.slotTarget && !state.pre) {
    return genChildren(el, state) || 'void 0'
  } else if (el.tag === 'slot') {
    return genSlot(el, state)
  } else {
    // component or element
    var code;
    if (el.component) {
      code = genComponent(el.component, el, state);
    } else {
      var data;
      if (!el.plain || (el.pre && state.maybeComponent(el))) {
        data = genData$2(el, state);
      }

      var children = el.inlineTemplate ? null : genChildren(el, state, true);
      code = "_c('" + (el.tag) + "'" + (data ? ("," + data) : '') + (children ? ("," + children) : '') + ")";
    }
    // module transforms
    for (var i = 0; i < state.transforms.length; i++) {
      code = state.transforms[i](el, code);
    }
    return code
  }
}

// hoist static sub-trees out
function genStatic (el, state) {
  el.staticProcessed = true;
  // Some elements (templates) need to behave differently inside of a v-pre
  // node.  All pre nodes are static roots, so we can use this as a location to
  // wrap a state change and reset it upon exiting the pre node.
  var originalPreState = state.pre;
  if (el.pre) {
    state.pre = el.pre;
  }
  state.staticRenderFns.push(("with(this){return " + (genElement(el, state)) + "}"));
  state.pre = originalPreState;
  return ("_m(" + (state.staticRenderFns.length - 1) + (el.staticInFor ? ',true' : '') + ")")
}

// v-once
function genOnce (el, state) {
  el.onceProcessed = true;
  if (el.if && !el.ifProcessed) {
    return genIf(el, state)
  } else if (el.staticInFor) {
    var key = '';
    var parent = el.parent;
    while (parent) {
      if (parent.for) {
        key = parent.key;
        break
      }
      parent = parent.parent;
    }
    if (!key) {
      state.warn(
        "v-once can only be used inside v-for that is keyed. ",
        el.rawAttrsMap['v-once']
      );
      return genElement(el, state)
    }
    return ("_o(" + (genElement(el, state)) + "," + (state.onceId++) + "," + key + ")")
  } else {
    return genStatic(el, state)
  }
}

function genIf (
  el,
  state,
  altGen,
  altEmpty
) {
  el.ifProcessed = true; // avoid recursion
  return genIfConditions(el.ifConditions.slice(), state, altGen, altEmpty)
}

function genIfConditions (
  conditions,
  state,
  altGen,
  altEmpty
) {
  if (!conditions.length) {
    return altEmpty || '_e()'
  }

  var condition = conditions.shift();
  if (condition.exp) {
    return ("(" + (condition.exp) + ")?" + (genTernaryExp(condition.block)) + ":" + (genIfConditions(conditions, state, altGen, altEmpty)))
  } else {
    return ("" + (genTernaryExp(condition.block)))
  }

  // v-if with v-once should generate code like (a)?_m(0):_m(1)
  function genTernaryExp (el) {
    return altGen
      ? altGen(el, state)
      : el.once
        ? genOnce(el, state)
        : genElement(el, state)
  }
}

function genFor (
  el,
  state,
  altGen,
  altHelper
) {
  var exp = el.for;
  var alias = el.alias;
  var iterator1 = el.iterator1 ? ("," + (el.iterator1)) : '';
  var iterator2 = el.iterator2 ? ("," + (el.iterator2)) : '';

  if (state.maybeComponent(el) &&
    el.tag !== 'slot' &&
    el.tag !== 'template' &&
    !el.key
  ) {
    state.warn(
      "<" + (el.tag) + " v-for=\"" + alias + " in " + exp + "\">: component lists rendered with " +
      "v-for should have explicit keys. " +
      "See https://vuejs.org/guide/list.html#key for more info.",
      el.rawAttrsMap['v-for'],
      true /* tip */
    );
  }

  el.forProcessed = true; // avoid recursion
  return (altHelper || '_l') + "((" + exp + ")," +
    "function(" + alias + iterator1 + iterator2 + "){" +
      "return " + ((altGen || genElement)(el, state)) +
    '})'
}

function genData$2 (el, state) {
  var data = '{';

  // directives first.
  // directives may mutate the el's other properties before they are generated.
  var dirs = genDirectives(el, state);
  if (dirs) { data += dirs + ','; }

  // key
  if (el.key) {
    data += "key:" + (el.key) + ",";
  }
  // ref
  if (el.ref) {
    data += "ref:" + (el.ref) + ",";
  }
  if (el.refInFor) {
    data += "refInFor:true,";
  }
  // pre
  if (el.pre) {
    data += "pre:true,";
  }
  // record original tag name for components using "is" attribute
  if (el.component) {
    data += "tag:\"" + (el.tag) + "\",";
  }
  // module data generation functions
  for (var i = 0; i < state.dataGenFns.length; i++) {
    data += state.dataGenFns[i](el);
  }
  // attributes
  if (el.attrs) {
    data += "attrs:" + (genProps(el.attrs)) + ",";
  }
  // DOM props
  if (el.props) {
    data += "domProps:" + (genProps(el.props)) + ",";
  }
  // event handlers
  if (el.events) {
    data += (genHandlers(el.events, false)) + ",";
  }
  if (el.nativeEvents) {
    data += (genHandlers(el.nativeEvents, true)) + ",";
  }
  // slot target
  // only for non-scoped slots
  if (el.slotTarget && !el.slotScope) {
    data += "slot:" + (el.slotTarget) + ",";
  }
  // scoped slots
  if (el.scopedSlots) {
    data += (genScopedSlots(el, el.scopedSlots, state)) + ",";
  }
  // component v-model
  if (el.model) {
    data += "model:{value:" + (el.model.value) + ",callback:" + (el.model.callback) + ",expression:" + (el.model.expression) + "},";
  }
  // inline-template
  if (el.inlineTemplate) {
    var inlineTemplate = genInlineTemplate(el, state);
    if (inlineTemplate) {
      data += inlineTemplate + ",";
    }
  }
  data = data.replace(/,$/, '') + '}';
  // v-bind dynamic argument wrap
  // v-bind with dynamic arguments must be applied using the same v-bind object
  // merge helper so that class/style/mustUseProp attrs are handled correctly.
  if (el.dynamicAttrs) {
    data = "_b(" + data + ",\"" + (el.tag) + "\"," + (genProps(el.dynamicAttrs)) + ")";
  }
  // v-bind data wrap
  if (el.wrapData) {
    data = el.wrapData(data);
  }
  // v-on data wrap
  if (el.wrapListeners) {
    data = el.wrapListeners(data);
  }
  return data
}

function genDirectives (el, state) {
  var dirs = el.directives;
  if (!dirs) { return }
  var res = 'directives:[';
  var hasRuntime = false;
  var i, l, dir, needRuntime;
  for (i = 0, l = dirs.length; i < l; i++) {
    dir = dirs[i];
    needRuntime = true;
    var gen = state.directives[dir.name];
    if (gen) {
      // compile-time directive that manipulates AST.
      // returns true if it also needs a runtime counterpart.
      needRuntime = !!gen(el, dir, state.warn);
    }
    if (needRuntime) {
      hasRuntime = true;
      res += "{name:\"" + (dir.name) + "\",rawName:\"" + (dir.rawName) + "\"" + (dir.value ? (",value:(" + (dir.value) + "),expression:" + (JSON.stringify(dir.value))) : '') + (dir.arg ? (",arg:" + (dir.isDynamicArg ? dir.arg : ("\"" + (dir.arg) + "\""))) : '') + (dir.modifiers ? (",modifiers:" + (JSON.stringify(dir.modifiers))) : '') + "},";
    }
  }
  if (hasRuntime) {
    return res.slice(0, -1) + ']'
  }
}

function genInlineTemplate (el, state) {
  var ast = el.children[0];
  if (el.children.length !== 1 || ast.type !== 1) {
    state.warn(
      'Inline-template components must have exactly one child element.',
      { start: el.start }
    );
  }
  if (ast && ast.type === 1) {
    var inlineRenderFns = generate(ast, state.options);
    return ("inlineTemplate:{render:function(){" + (inlineRenderFns.render) + "},staticRenderFns:[" + (inlineRenderFns.staticRenderFns.map(function (code) { return ("function(){" + code + "}"); }).join(',')) + "]}")
  }
}

function genScopedSlots (
  el,
  slots,
  state
) {
  // by default scoped slots are considered "stable", this allows child
  // components with only scoped slots to skip forced updates from parent.
  // but in some cases we have to bail-out of this optimization
  // for example if the slot contains dynamic names, has v-if or v-for on them...
  var needsForceUpdate = el.for || Object.keys(slots).some(function (key) {
    var slot = slots[key];
    return (
      slot.slotTargetDynamic ||
      slot.if ||
      slot.for ||
      containsSlotChild(slot) // is passing down slot from parent which may be dynamic
    )
  });

  // #9534: if a component with scoped slots is inside a conditional branch,
  // it's possible for the same component to be reused but with different
  // compiled slot content. To avoid that, we generate a unique key based on
  // the generated code of all the slot contents.
  var needsKey = !!el.if;

  // OR when it is inside another scoped slot or v-for (the reactivity may be
  // disconnected due to the intermediate scope variable)
  // #9438, #9506
  // TODO: this can be further optimized by properly analyzing in-scope bindings
  // and skip force updating ones that do not actually use scope variables.
  if (!needsForceUpdate) {
    var parent = el.parent;
    while (parent) {
      if (
        (parent.slotScope && parent.slotScope !== emptySlotScopeToken) ||
        parent.for
      ) {
        needsForceUpdate = true;
        break
      }
      if (parent.if) {
        needsKey = true;
      }
      parent = parent.parent;
    }
  }

  var generatedSlots = Object.keys(slots)
    .map(function (key) { return genScopedSlot(slots[key], state); })
    .join(',');

  return ("scopedSlots:_u([" + generatedSlots + "]" + (needsForceUpdate ? ",null,true" : "") + (!needsForceUpdate && needsKey ? (",null,false," + (hash(generatedSlots))) : "") + ")")
}

function hash(str) {
  var hash = 5381;
  var i = str.length;
  while(i) {
    hash = (hash * 33) ^ str.charCodeAt(--i);
  }
  return hash >>> 0
}

function containsSlotChild (el) {
  if (el.type === 1) {
    if (el.tag === 'slot') {
      return true
    }
    return el.children.some(containsSlotChild)
  }
  return false
}

function genScopedSlot (
  el,
  state
) {
  var isLegacySyntax = el.attrsMap['slot-scope'];
  if (el.if && !el.ifProcessed && !isLegacySyntax) {
    return genIf(el, state, genScopedSlot, "null")
  }
  if (el.for && !el.forProcessed) {
    return genFor(el, state, genScopedSlot)
  }
  var slotScope = el.slotScope === emptySlotScopeToken
    ? ""
    : String(el.slotScope);
  var fn = "function(" + slotScope + "){" +
    "return " + (el.tag === 'template'
      ? el.if && isLegacySyntax
        ? ("(" + (el.if) + ")?" + (genChildren(el, state) || 'undefined') + ":undefined")
        : genChildren(el, state) || 'undefined'
      : genElement(el, state)) + "}";
  // reverse proxy v-slot without scope on this.$slots
  var reverseProxy = slotScope ? "" : ",proxy:true";
  return ("{key:" + (el.slotTarget || "\"default\"") + ",fn:" + fn + reverseProxy + "}")
}

function genChildren (
  el,
  state,
  checkSkip,
  altGenElement,
  altGenNode
) {
  var children = el.children;
  if (children.length) {
    var el$1 = children[0];
    // optimize single v-for
    if (children.length === 1 &&
      el$1.for &&
      el$1.tag !== 'template' &&
      el$1.tag !== 'slot'
    ) {
      var normalizationType = checkSkip
        ? state.maybeComponent(el$1) ? ",1" : ",0"
        : "";
      return ("" + ((altGenElement || genElement)(el$1, state)) + normalizationType)
    }
    var normalizationType$1 = checkSkip
      ? getNormalizationType(children, state.maybeComponent)
      : 0;
    var gen = altGenNode || genNode;
    return ("[" + (children.map(function (c) { return gen(c, state); }).join(',')) + "]" + (normalizationType$1 ? ("," + normalizationType$1) : ''))
  }
}

// determine the normalization needed for the children array.
// 0: no normalization needed
// 1: simple normalization needed (possible 1-level deep nested array)
// 2: full normalization needed
function getNormalizationType (
  children,
  maybeComponent
) {
  var res = 0;
  for (var i = 0; i < children.length; i++) {
    var el = children[i];
    if (el.type !== 1) {
      continue
    }
    if (needsNormalization(el) ||
        (el.ifConditions && el.ifConditions.some(function (c) { return needsNormalization(c.block); }))) {
      res = 2;
      break
    }
    if (maybeComponent(el) ||
        (el.ifConditions && el.ifConditions.some(function (c) { return maybeComponent(c.block); }))) {
      res = 1;
    }
  }
  return res
}

function needsNormalization (el) {
  return el.for !== undefined || el.tag === 'template' || el.tag === 'slot'
}

function genNode (node, state) {
  if (node.type === 1) {
    return genElement(node, state)
  } else if (node.type === 3 && node.isComment) {
    return genComment(node)
  } else {
    return genText(node)
  }
}

function genText (text) {
  return ("_v(" + (text.type === 2
    ? text.expression // no need for () because already wrapped in _s()
    : transformSpecialNewlines(JSON.stringify(text.text))) + ")")
}

function genComment (comment) {
  return ("_e(" + (JSON.stringify(comment.text)) + ")")
}

function genSlot (el, state) {
  var slotName = el.slotName || '"default"';
  var children = genChildren(el, state);
  var res = "_t(" + slotName + (children ? ("," + children) : '');
  var attrs = el.attrs || el.dynamicAttrs
    ? genProps((el.attrs || []).concat(el.dynamicAttrs || []).map(function (attr) { return ({
        // slot props are camelized
        name: camelize(attr.name),
        value: attr.value,
        dynamic: attr.dynamic
      }); }))
    : null;
  var bind$$1 = el.attrsMap['v-bind'];
  if ((attrs || bind$$1) && !children) {
    res += ",null";
  }
  if (attrs) {
    res += "," + attrs;
  }
  if (bind$$1) {
    res += (attrs ? '' : ',null') + "," + bind$$1;
  }
  return res + ')'
}

// componentName is el.component, take it as argument to shun flow's pessimistic refinement
function genComponent (
  componentName,
  el,
  state
) {
  var children = el.inlineTemplate ? null : genChildren(el, state, true);
  return ("_c(" + componentName + "," + (genData$2(el, state)) + (children ? ("," + children) : '') + ")")
}

function genProps (props) {
  var staticProps = "";
  var dynamicProps = "";
  for (var i = 0; i < props.length; i++) {
    var prop = props[i];
    var value = transformSpecialNewlines(prop.value);
    if (prop.dynamic) {
      dynamicProps += (prop.name) + "," + value + ",";
    } else {
      staticProps += "\"" + (prop.name) + "\":" + value + ",";
    }
  }
  staticProps = "{" + (staticProps.slice(0, -1)) + "}";
  if (dynamicProps) {
    return ("_d(" + staticProps + ",[" + (dynamicProps.slice(0, -1)) + "])")
  } else {
    return staticProps
  }
}

// #3895, #4268
function transformSpecialNewlines (text) {
  return text
    .replace(/\u2028/g, '\\u2028')
    .replace(/\u2029/g, '\\u2029')
}

/*  */



// these keywords should not appear inside expressions, but operators like
// typeof, instanceof and in are allowed
var prohibitedKeywordRE = new RegExp('\\b' + (
  'do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,' +
  'super,throw,while,yield,delete,export,import,return,switch,default,' +
  'extends,finally,continue,debugger,function,arguments'
).split(',').join('\\b|\\b') + '\\b');

// these unary operators should not be used as property/method names
var unaryOperatorsRE = new RegExp('\\b' + (
  'delete,typeof,void'
).split(',').join('\\s*\\([^\\)]*\\)|\\b') + '\\s*\\([^\\)]*\\)');

// strip strings in expressions
var stripStringRE = /'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*\$\{|\}(?:[^`\\]|\\.)*`|`(?:[^`\\]|\\.)*`/g;

// detect problematic expressions in a template
function detectErrors (ast, warn) {
  if (ast) {
    checkNode(ast, warn);
  }
}

function checkNode (node, warn) {
  if (node.type === 1) {
    for (var name in node.attrsMap) {
      if (dirRE.test(name)) {
        var value = node.attrsMap[name];
        if (value) {
          var range = node.rawAttrsMap[name];
          if (name === 'v-for') {
            checkFor(node, ("v-for=\"" + value + "\""), warn, range);
          } else if (onRE.test(name)) {
            checkEvent(value, (name + "=\"" + value + "\""), warn, range);
          } else {
            checkExpression(value, (name + "=\"" + value + "\""), warn, range);
          }
        }
      }
    }
    if (node.children) {
      for (var i = 0; i < node.children.length; i++) {
        checkNode(node.children[i], warn);
      }
    }
  } else if (node.type === 2) {
    checkExpression(node.expression, node.text, warn, node);
  }
}

function checkEvent (exp, text, warn, range) {
  var stipped = exp.replace(stripStringRE, '');
  var keywordMatch = stipped.match(unaryOperatorsRE);
  if (keywordMatch && stipped.charAt(keywordMatch.index - 1) !== '$') {
    warn(
      "avoid using JavaScript unary operator as property name: " +
      "\"" + (keywordMatch[0]) + "\" in expression " + (text.trim()),
      range
    );
  }
  checkExpression(exp, text, warn, range);
}

function checkFor (node, text, warn, range) {
  checkExpression(node.for || '', text, warn, range);
  checkIdentifier(node.alias, 'v-for alias', text, warn, range);
  checkIdentifier(node.iterator1, 'v-for iterator', text, warn, range);
  checkIdentifier(node.iterator2, 'v-for iterator', text, warn, range);
}

function checkIdentifier (
  ident,
  type,
  text,
  warn,
  range
) {
  if (typeof ident === 'string') {
    try {
      new Function(("var " + ident + "=_"));
    } catch (e) {
      warn(("invalid " + type + " \"" + ident + "\" in expression: " + (text.trim())), range);
    }
  }
}

function checkExpression (exp, text, warn, range) {
  try {
    new Function(("return " + exp));
  } catch (e) {
    var keywordMatch = exp.replace(stripStringRE, '').match(prohibitedKeywordRE);
    if (keywordMatch) {
      warn(
        "avoid using JavaScript keyword as property name: " +
        "\"" + (keywordMatch[0]) + "\"\n  Raw expression: " + (text.trim()),
        range
      );
    } else {
      warn(
        "invalid expression: " + (e.message) + " in\n\n" +
        "    " + exp + "\n\n" +
        "  Raw expression: " + (text.trim()) + "\n",
        range
      );
    }
  }
}

/*  */

var range = 2;

function generateCodeFrame (
  source,
  start,
  end
) {
  if ( start === void 0 ) start = 0;
  if ( end === void 0 ) end = source.length;

  var lines = source.split(/\r?\n/);
  var count = 0;
  var res = [];
  for (var i = 0; i < lines.length; i++) {
    count += lines[i].length + 1;
    if (count >= start) {
      for (var j = i - range; j <= i + range || end > count; j++) {
        if (j < 0 || j >= lines.length) { continue }
        res.push(("" + (j + 1) + (repeat$1(" ", 3 - String(j + 1).length)) + "|  " + (lines[j])));
        var lineLength = lines[j].length;
        if (j === i) {
          // push underline
          var pad = start - (count - lineLength) + 1;
          var length = end > count ? lineLength - pad : end - start;
          res.push("   |  " + repeat$1(" ", pad) + repeat$1("^", length));
        } else if (j > i) {
          if (end > count) {
            var length$1 = Math.min(end - count, lineLength);
            res.push("   |  " + repeat$1("^", length$1));
          }
          count += lineLength + 1;
        }
      }
      break
    }
  }
  return res.join('\n')
}

function repeat$1 (str, n) {
  var result = '';
  if (n > 0) {
    while (true) { // eslint-disable-line
      if (n & 1) { result += str; }
      n >>>= 1;
      if (n <= 0) { break }
      str += str;
    }
  }
  return result
}

/*  */



function createFunction (code, errors) {
  try {
    return new Function(code)
  } catch (err) {
    errors.push({ err: err, code: code });
    return noop
  }
}

function createCompileToFunctionFn (compile) {
  var cache = Object.create(null);

  return function compileToFunctions (
    template,
    options,
    vm
  ) {
    options = extend({}, options);
    var warn$$1 = options.warn || warn;
    delete options.warn;

    /* istanbul ignore if */
    {
      // detect possible CSP restriction
      try {
        new Function('return 1');
      } catch (e) {
        if (e.toString().match(/unsafe-eval|CSP/)) {
          warn$$1(
            'It seems you are using the standalone build of Vue.js in an ' +
            'environment with Content Security Policy that prohibits unsafe-eval. ' +
            'The template compiler cannot work in this environment. Consider ' +
            'relaxing the policy to allow unsafe-eval or pre-compiling your ' +
            'templates into render functions.'
          );
        }
      }
    }

    // check cache
    var key = options.delimiters
      ? String(options.delimiters) + template
      : template;
    if (cache[key]) {
      return cache[key]
    }

    // compile
    var compiled = compile(template, options);

    // check compilation errors/tips
    {
      if (compiled.errors && compiled.errors.length) {
        if (options.outputSourceRange) {
          compiled.errors.forEach(function (e) {
            warn$$1(
              "Error compiling template:\n\n" + (e.msg) + "\n\n" +
              generateCodeFrame(template, e.start, e.end),
              vm
            );
          });
        } else {
          warn$$1(
            "Error compiling template:\n\n" + template + "\n\n" +
            compiled.errors.map(function (e) { return ("- " + e); }).join('\n') + '\n',
            vm
          );
        }
      }
      if (compiled.tips && compiled.tips.length) {
        if (options.outputSourceRange) {
          compiled.tips.forEach(function (e) { return tip(e.msg, vm); });
        } else {
          compiled.tips.forEach(function (msg) { return tip(msg, vm); });
        }
      }
    }

    // turn code into functions
    var res = {};
    var fnGenErrors = [];
    res.render = createFunction(compiled.render, fnGenErrors);
    res.staticRenderFns = compiled.staticRenderFns.map(function (code) {
      return createFunction(code, fnGenErrors)
    });

    // check function generation errors.
    // this should only happen if there is a bug in the compiler itself.
    // mostly for codegen development use
    /* istanbul ignore if */
    {
      if ((!compiled.errors || !compiled.errors.length) && fnGenErrors.length) {
        warn$$1(
          "Failed to generate render function:\n\n" +
          fnGenErrors.map(function (ref) {
            var err = ref.err;
            var code = ref.code;

            return ((err.toString()) + " in\n\n" + code + "\n");
        }).join('\n'),
          vm
        );
      }
    }

    return (cache[key] = res)
  }
}

/*  */

function createCompilerCreator (baseCompile) {
  return function createCompiler (baseOptions) {
    function compile (
      template,
      options
    ) {
      var finalOptions = Object.create(baseOptions);
      var errors = [];
      var tips = [];

      var warn = function (msg, range, tip) {
        (tip ? tips : errors).push(msg);
      };

      if (options) {
        if (options.outputSourceRange) {
          // $flow-disable-line
          var leadingSpaceLength = template.match(/^\s*/)[0].length;

          warn = function (msg, range, tip) {
            var data = { msg: msg };
            if (range) {
              if (range.start != null) {
                data.start = range.start + leadingSpaceLength;
              }
              if (range.end != null) {
                data.end = range.end + leadingSpaceLength;
              }
            }
            (tip ? tips : errors).push(data);
          };
        }
        // merge custom modules
        if (options.modules) {
          finalOptions.modules =
            (baseOptions.modules || []).concat(options.modules);
        }
        // merge custom directives
        if (options.directives) {
          finalOptions.directives = extend(
            Object.create(baseOptions.directives || null),
            options.directives
          );
        }
        // copy other options
        for (var key in options) {
          if (key !== 'modules' && key !== 'directives') {
            finalOptions[key] = options[key];
          }
        }
      }

      finalOptions.warn = warn;

      var compiled = baseCompile(template.trim(), finalOptions);
      {
        detectErrors(compiled.ast, warn);
      }
      compiled.errors = errors;
      compiled.tips = tips;
      return compiled
    }

    return {
      compile: compile,
      compileToFunctions: createCompileToFunctionFn(compile)
    }
  }
}

/*  */

// `createCompilerCreator` allows creating compilers that use alternative
// parser/optimizer/codegen, e.g the SSR optimizing compiler.
// Here we just export a default compiler using the default parts.
var createCompiler = createCompilerCreator(function baseCompile (
  template,
  options
) {
  var ast = parse(template.trim(), options);
  if (options.optimize !== false) {
    optimize(ast, options);
  }
  var code = generate(ast, options);
  return {
    ast: ast,
    render: code.render,
    staticRenderFns: code.staticRenderFns
  }
});

/*  */

var ref$1 = createCompiler(baseOptions);
var compile = ref$1.compile;
var compileToFunctions = ref$1.compileToFunctions;

/*  */

// check whether current browser encodes a char inside attribute values
var div;
function getShouldDecode (href) {
  div = div || document.createElement('div');
  div.innerHTML = href ? "<a href=\"\n\"/>" : "<div a=\"\n\"/>";
  return div.innerHTML.indexOf('&#10;') > 0
}

// #3663: IE encodes newlines inside attribute values while other browsers don't
var shouldDecodeNewlines = inBrowser ? getShouldDecode(false) : false;
// #6828: chrome encodes content in a[href]
var shouldDecodeNewlinesForHref = inBrowser ? getShouldDecode(true) : false;

/*  */

var idToTemplate = cached(function (id) {
  var el = query(id);
  return el && el.innerHTML
});

var mount = Vue.prototype.$mount;
Vue.prototype.$mount = function (
  el,
  hydrating
) {
  el = el && query(el);

  /* istanbul ignore if */
  if (el === document.body || el === document.documentElement) {
    warn(
      "Do not mount Vue to <html> or <body> - mount to normal elements instead."
    );
    return this
  }

  var options = this.$options;
  // resolve template/el and convert to render function
  if (!options.render) {
    var template = options.template;
    if (template) {
      if (typeof template === 'string') {
        if (template.charAt(0) === '#') {
          template = idToTemplate(template);
          /* istanbul ignore if */
          if (!template) {
            warn(
              ("Template element not found or is empty: " + (options.template)),
              this
            );
          }
        }
      } else if (template.nodeType) {
        template = template.innerHTML;
      } else {
        {
          warn('invalid template option:' + template, this);
        }
        return this
      }
    } else if (el) {
      template = getOuterHTML(el);
    }
    if (template) {
      /* istanbul ignore if */
      if (config.performance && mark) {
        mark('compile');
      }

      var ref = compileToFunctions(template, {
        outputSourceRange: "development" !== 'production',
        shouldDecodeNewlines: shouldDecodeNewlines,
        shouldDecodeNewlinesForHref: shouldDecodeNewlinesForHref,
        delimiters: options.delimiters,
        comments: options.comments
      }, this);
      var render = ref.render;
      var staticRenderFns = ref.staticRenderFns;
      options.render = render;
      options.staticRenderFns = staticRenderFns;

      /* istanbul ignore if */
      if (config.performance && mark) {
        mark('compile end');
        measure(("vue " + (this._name) + " compile"), 'compile', 'compile end');
      }
    }
  }
  return mount.call(this, el, hydrating)
};

/**
 * Get outerHTML of elements, taking care
 * of SVG elements in IE as well.
 */
function getOuterHTML (el) {
  if (el.outerHTML) {
    return el.outerHTML
  } else {
    var container = document.createElement('div');
    container.appendChild(el.cloneNode(true));
    return container.innerHTML
  }
}

Vue.compile = compileToFunctions;

module.exports = Vue;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1), __webpack_require__(15).setImmediate))

/***/ }),

/***/ 15:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var scope = (typeof global !== "undefined" && global) ||
            (typeof self !== "undefined" && self) ||
            window;
var apply = Function.prototype.apply;

// DOM APIs, for completeness

exports.setTimeout = function() {
  return new Timeout(apply.call(setTimeout, scope, arguments), clearTimeout);
};
exports.setInterval = function() {
  return new Timeout(apply.call(setInterval, scope, arguments), clearInterval);
};
exports.clearTimeout =
exports.clearInterval = function(timeout) {
  if (timeout) {
    timeout.close();
  }
};

function Timeout(id, clearFn) {
  this._id = id;
  this._clearFn = clearFn;
}
Timeout.prototype.unref = Timeout.prototype.ref = function() {};
Timeout.prototype.close = function() {
  this._clearFn.call(scope, this._id);
};

// Does not start the time, just sets up the members needed.
exports.enroll = function(item, msecs) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = msecs;
};

exports.unenroll = function(item) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = -1;
};

exports._unrefActive = exports.active = function(item) {
  clearTimeout(item._idleTimeoutId);

  var msecs = item._idleTimeout;
  if (msecs >= 0) {
    item._idleTimeoutId = setTimeout(function onTimeout() {
      if (item._onTimeout)
        item._onTimeout();
    }, msecs);
  }
};

// setimmediate attaches itself to the global object
__webpack_require__(16);
// On some exotic environments, it's not clear which object `setimmediate` was
// able to install onto.  Search each possibility in the same order as the
// `setimmediate` library.
exports.setImmediate = (typeof self !== "undefined" && self.setImmediate) ||
                       (typeof global !== "undefined" && global.setImmediate) ||
                       (this && this.setImmediate);
exports.clearImmediate = (typeof self !== "undefined" && self.clearImmediate) ||
                         (typeof global !== "undefined" && global.clearImmediate) ||
                         (this && this.clearImmediate);

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 152:
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),

/***/ 153:
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(287)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}
var options = null
var ssrIdKey = 'data-vue-ssr-id'

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction, _options) {
  isProduction = _isProduction

  options = _options || {}

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[' + ssrIdKey + '~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }
  if (options.ssrId) {
    styleElement.setAttribute(ssrIdKey, obj.id)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global, process) {(function (global, undefined) {
    "use strict";

    if (global.setImmediate) {
        return;
    }

    var nextHandle = 1; // Spec says greater than zero
    var tasksByHandle = {};
    var currentlyRunningATask = false;
    var doc = global.document;
    var registerImmediate;

    function setImmediate(callback) {
      // Callback can either be a function or a string
      if (typeof callback !== "function") {
        callback = new Function("" + callback);
      }
      // Copy function arguments
      var args = new Array(arguments.length - 1);
      for (var i = 0; i < args.length; i++) {
          args[i] = arguments[i + 1];
      }
      // Store and register the task
      var task = { callback: callback, args: args };
      tasksByHandle[nextHandle] = task;
      registerImmediate(nextHandle);
      return nextHandle++;
    }

    function clearImmediate(handle) {
        delete tasksByHandle[handle];
    }

    function run(task) {
        var callback = task.callback;
        var args = task.args;
        switch (args.length) {
        case 0:
            callback();
            break;
        case 1:
            callback(args[0]);
            break;
        case 2:
            callback(args[0], args[1]);
            break;
        case 3:
            callback(args[0], args[1], args[2]);
            break;
        default:
            callback.apply(undefined, args);
            break;
        }
    }

    function runIfPresent(handle) {
        // From the spec: "Wait until any invocations of this algorithm started before this one have completed."
        // So if we're currently running a task, we'll need to delay this invocation.
        if (currentlyRunningATask) {
            // Delay by doing a setTimeout. setImmediate was tried instead, but in Firefox 7 it generated a
            // "too much recursion" error.
            setTimeout(runIfPresent, 0, handle);
        } else {
            var task = tasksByHandle[handle];
            if (task) {
                currentlyRunningATask = true;
                try {
                    run(task);
                } finally {
                    clearImmediate(handle);
                    currentlyRunningATask = false;
                }
            }
        }
    }

    function installNextTickImplementation() {
        registerImmediate = function(handle) {
            process.nextTick(function () { runIfPresent(handle); });
        };
    }

    function canUsePostMessage() {
        // The test against `importScripts` prevents this implementation from being installed inside a web worker,
        // where `global.postMessage` means something completely different and can't be used for this purpose.
        if (global.postMessage && !global.importScripts) {
            var postMessageIsAsynchronous = true;
            var oldOnMessage = global.onmessage;
            global.onmessage = function() {
                postMessageIsAsynchronous = false;
            };
            global.postMessage("", "*");
            global.onmessage = oldOnMessage;
            return postMessageIsAsynchronous;
        }
    }

    function installPostMessageImplementation() {
        // Installs an event handler on `global` for the `message` event: see
        // * https://developer.mozilla.org/en/DOM/window.postMessage
        // * http://www.whatwg.org/specs/web-apps/current-work/multipage/comms.html#crossDocumentMessages

        var messagePrefix = "setImmediate$" + Math.random() + "$";
        var onGlobalMessage = function(event) {
            if (event.source === global &&
                typeof event.data === "string" &&
                event.data.indexOf(messagePrefix) === 0) {
                runIfPresent(+event.data.slice(messagePrefix.length));
            }
        };

        if (global.addEventListener) {
            global.addEventListener("message", onGlobalMessage, false);
        } else {
            global.attachEvent("onmessage", onGlobalMessage);
        }

        registerImmediate = function(handle) {
            global.postMessage(messagePrefix + handle, "*");
        };
    }

    function installMessageChannelImplementation() {
        var channel = new MessageChannel();
        channel.port1.onmessage = function(event) {
            var handle = event.data;
            runIfPresent(handle);
        };

        registerImmediate = function(handle) {
            channel.port2.postMessage(handle);
        };
    }

    function installReadyStateChangeImplementation() {
        var html = doc.documentElement;
        registerImmediate = function(handle) {
            // Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
            // into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
            var script = doc.createElement("script");
            script.onreadystatechange = function () {
                runIfPresent(handle);
                script.onreadystatechange = null;
                html.removeChild(script);
                script = null;
            };
            html.appendChild(script);
        };
    }

    function installSetTimeoutImplementation() {
        registerImmediate = function(handle) {
            setTimeout(runIfPresent, 0, handle);
        };
    }

    // If supported, we should attach to the prototype of global, since that is where setTimeout et al. live.
    var attachTo = Object.getPrototypeOf && Object.getPrototypeOf(global);
    attachTo = attachTo && attachTo.setTimeout ? attachTo : global;

    // Don't get fooled by e.g. browserify environments.
    if ({}.toString.call(global.process) === "[object process]") {
        // For Node.js before 0.9
        installNextTickImplementation();

    } else if (canUsePostMessage()) {
        // For non-IE10 modern browsers
        installPostMessageImplementation();

    } else if (global.MessageChannel) {
        // For web workers, where supported
        installMessageChannelImplementation();

    } else if (doc && "onreadystatechange" in doc.createElement("script")) {
        // For IE 6–8
        installReadyStateChangeImplementation();

    } else {
        // For older browsers
        installSetTimeoutImplementation();
    }

    attachTo.setImmediate = setImmediate;
    attachTo.clearImmediate = clearImmediate;
}(typeof self === "undefined" ? typeof global === "undefined" ? this : global : self));

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1), __webpack_require__(17)))

/***/ }),

/***/ 17:
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ 282:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(283);
__webpack_require__(297);
__webpack_require__(298);
__webpack_require__(299);
__webpack_require__(300);
module.exports = __webpack_require__(301);


/***/ }),

/***/ 283:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);


// Load up our cropper
__WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('avatar-cropper', __webpack_require__(284));

var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a({
    el: '[vue-enabled]'
});

/***/ }),

/***/ 284:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(285)
}
var normalizeComponent = __webpack_require__(9)
/* script */
var __vue_script__ = __webpack_require__(288)
/* template */
var __vue_template__ = __webpack_require__(296)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-04b89432"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "components/avatar/Cropper.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-04b89432", Component.options)
  } else {
    hotAPI.reload("data-v-04b89432", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 285:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(286);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(153)("7bbca45a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-04b89432\",\"scoped\":true,\"hasInlineConfig\":true}!../../node_modules/less-loader/dist/cjs.js!./Cropper.less", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-04b89432\",\"scoped\":true,\"hasInlineConfig\":true}!../../node_modules/less-loader/dist/cjs.js!./Cropper.less");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 286:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(152)(false);
// imports


// module
exports.push([module.i, "\n.ccm-avatar-creator-container[data-v-04b89432] {\n  position: relative;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions[data-v-04b89432] {\n  z-index: 20000;\n  position: absolute;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-04b89432] {\n  width: 50%;\n  height: 16px;\n  display: inline-block;\n  text-align: center;\n  opacity: 0.3;\n  -webkit-transition: all 0.5s;\n  transition: all 0.5s;\n  text-decoration: none;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-04b89432]:hover {\n  opacity: 1;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-04b89432]:before {\n  font-size: 16px;\n  font-family: FontAwesome;\n  text-align: center;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-cancel[data-v-04b89432] {\n  float: right;\n  color: #FF4136;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-cancel[data-v-04b89432]:before {\n  content: '\\F00D';\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-okay[data-v-04b89432] {\n  color: #3D9970;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-okay[data-v-04b89432]:before {\n  content: '\\F00C';\n}\n.ccm-avatar-creator-container .ccm-avatar-creator[data-v-04b89432] {\n  border: solid 1px #999;\n  -webkit-transition: all 0.3s;\n  transition: all 0.3s;\n  z-index: 10000;\n  overflow: hidden;\n  position: relative;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator > img.ccm-avatar-current[data-v-04b89432] {\n  z-index: 998;\n  display: inline;\n  width: 100%;\n  height: 100%;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator > div.saving[data-v-04b89432] {\n  position: absolute;\n  top: 0;\n  left: 0;\n  width: 100%;\n  height: 100%;\n  background: rgba(127, 219, 255, 0.5);\n  font-weight: bolder;\n  font-size: 16px;\n  text-align: center;\n  color: #111111;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator[data-v-04b89432]:before {\n  -webkit-transition: all 0.3s;\n  transition: all 0.3s;\n  content: '\\F093';\n  font-family: FontAwesome;\n  opacity: 0;\n  font-size: 16px;\n  margin: 0 auto;\n  padding-top: 50%;\n  line-height: 0%;\n  display: block;\n  width: 100%;\n  text-align: center;\n  height: 100%;\n  vertical-align: middle;\n  color: #3D9970;\n  background-color: rgba(238, 238, 238, 0.8);\n  z-index: 999;\n  position: absolute;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-started[data-v-04b89432]:before {\n  opacity: 1;\n  -webkit-animation: pulse-data-v-04b89432 1s infinite;\n  animation: pulse-data-v-04b89432 1s infinite;\n}\n@-webkit-keyframes pulse-data-v-04b89432 {\n0% {\n    -webkit-transform: scale(1);\n            transform: scale(1);\n}\n50% {\n    -webkit-transform: scale(1.3);\n            transform: scale(1.3);\n}\n100% {\n    -webkit-transform: scale(1);\n            transform: scale(1);\n}\n}\n@keyframes pulse-data-v-04b89432 {\n0% {\n    -webkit-transform: scale(1);\n            transform: scale(1);\n}\n50% {\n    -webkit-transform: scale(1.3);\n            transform: scale(1.3);\n}\n100% {\n    -webkit-transform: scale(1);\n            transform: scale(1);\n}\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.editing[data-v-04b89432]:before {\n  display: none;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-04b89432] {\n  pointer: cursor;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-04b89432]:hover,\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-drag-hover[data-v-04b89432] {\n  border-color: #3D9970;\n  -webkit-box-shadow: 0 0 20px -10px #2ECC40;\n          box-shadow: 0 0 20px -10px #2ECC40;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-04b89432]:hover:before,\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-drag-hover[data-v-04b89432]:before {\n  opacity: 1;\n}\n", ""]);

// exports


/***/ }),

/***/ 287:
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),

/***/ 288:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_dropzone__ = __webpack_require__(289);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_dropzone___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_dropzone__);


// Disable dropzone discovery
__WEBPACK_IMPORTED_MODULE_0_dropzone___default.a.autoDiscover = false;

/* harmony default export */ __webpack_exports__["default"] = ({
    // Our element tagname
    name: "avatar-cropper",

    // Properties tied to our parent
    props: {
        width: Number,
        height: Number,
        uploadurl: String,
        src: String
    },

    // Our internal state
    data: function data() {
        return {
            img: null,
            x: 10,
            y: 5,
            cropWidth: 0,
            cropHeight: 0,
            imageHeight: 0,
            imageWidth: 0,
            saving: false,
            currentimage: null,
            hasError: false,
            errorMessage: ''
        };
    },

    /**
     * Handle mounting to our element
     */
    mounted: function mounted() {
        // Attach the current image
        this.currentimage = this.src;

        // Setup Uploading
        this.setupUploading();
    },

    methods: {
        setError: function setError(error) {
            this.hasError = true;
            this.errorMessage = error;
        },
        clearError: function clearError() {
            this.hasError = false;
            this.errorMessage = '';
        },

        /**
         * Setup dropzone for uploading
         */
        setupUploading: function setupUploading() {
            if (this.dropzone) {
                return;
            }

            var component = this;
            this.dropzone = new __WEBPACK_IMPORTED_MODULE_0_dropzone___default.a(this.$refs.dropzone, {
                url: this.uploadurl,
                maxFiles: 1,
                previewTemplate: '<span></span>',
                transformFileSync: false,

                // Accept uploaded files from user
                accept: function accept(file, done) {
                    component.file = file;
                    component.done = done;
                },


                // Give the component a chance to finalize the file
                transformFile: function transformFile(file, done) {
                    return component.finalize(file, done);
                },


                // Initialize dropzone
                init: function init() {
                    // Capture thumbnail details
                    this.on('thumbnail', function () {
                        component.img = component.file.dataURL;
                        component.imageWidth = component.file.width;
                        component.imageHeight = component.file.height;
                    });

                    this.on('success', function (event, data) {
                        if (!component.hasError) {
                            component.currentimage = data.avatar;
                        }
                    });

                    this.on('error', function (event, data) {
                        component.setError(data.message);
                        component.currentimage = component.src;
                    });

                    this.on('complete', function () {
                        component.saving = false;
                        component.img = null;
                        component.dropzone.destroy();
                        component.dropzone = null;

                        setTimeout(function () {
                            component.setupUploading();
                        }, 0);
                    });
                },


                /**
                 * Request full size thumbnails
                 * @param file
                 * @param int width
                 * @param int height
                 * @returns {{int srcWidth, int srcHeight, int trgWidth, int trgHeight}}
                 */
                resize: function resize(file, width, height) {
                    return {
                        srcWidth: file.width,
                        srcHeight: file.height,
                        trgWidth: file.width,
                        trgHeight: file.height
                    };
                }
            });
        },


        /**
         * Handle finalizing a user provided image.
         * This is where we actually do the cropping and rendering.
         *
         * @param file
         * @param bool done
         * @returns {*}
         */
        finalize: function finalize(file, done) {
            var canvas = document.createElement('canvas'),
                ctx = canvas.getContext('2d'),
                img = new Image();

            img.src = file.dataURL;

            canvas.width = this.width;
            canvas.height = this.height;

            // Draw the image cropped
            ctx.drawImage(img, this.$refs.image.x, this.$refs.image.y, this.$refs.image.resizeWidth, this.$refs.image.resizeHeight);

            this.saving = true;
            // Complete the upload
            var data = canvas.toDataURL(),
                result = done(__WEBPACK_IMPORTED_MODULE_0_dropzone___default.a.dataURItoBlob(data));

            this.currentimage = data;
            return result;
        },


        /**
         * Attach our shadow and our image together
         */
        attachShadow: function attachShadow() {
            // Attach the shadow
            this.$refs.shadow.setViewport(this.$refs.image);
        },


        /**
         * Handle checkmark click
         */
        handleOkay: function handleOkay() {
            this.done.call(this.dropzone);
        },


        /**
         * Handle x mark click
         */
        handleCancel: function handleCancel() {
            if (window.confirm('Are you sure you want to quit?')) {
                this.done.call(this.dropzone, 'Cancelled upload.');
                this.img = null;
                this.saving = false;
                this.dropzone.destroy();
                this.clearError();
                this.dropzone = null;
                this.setupUploading();
            }
        }
    },
    components: {
        'avatar-image': __webpack_require__(290)
    }
});

/***/ }),

/***/ 289:
/***/ (function(module, exports) {

module.exports = Dropzone;

/***/ }),

/***/ 290:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(291)
}
var normalizeComponent = __webpack_require__(9)
/* script */
var __vue_script__ = __webpack_require__(293)
/* template */
var __vue_template__ = __webpack_require__(295)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-d4119378"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "components/avatar/Avatar.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-d4119378", Component.options)
  } else {
    hotAPI.reload("data-v-d4119378", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 291:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(292);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(153)("fe35a646", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-d4119378\",\"scoped\":true,\"hasInlineConfig\":true}!./Avatar.less", function() {
     var newContent = require("!!../../node_modules/css-loader/index.js!../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-d4119378\",\"scoped\":true,\"hasInlineConfig\":true}!./Avatar.less");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 292:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(152)(false);
// imports


// module
exports.push([module.i, "\nimg[data-v-d4119378] {\n    max-width: inherit;\n    min-width: inherit;\n    top: 0;\n    left: 0;\n}\n.shadow[data-v-d4119378] {\n    top: 1px;\n    left: 1px;\n    opacity: 0.3;\n    z-index: 1;\n    position: absolute;\n    -webkit-box-shadow: 0 0 10px black;\n            box-shadow: 0 0 10px black;\n    outline: solid 1px #333;\n    -webkit-box-sizing: border-box;\n            box-sizing: border-box;\n}", ""]);

// exports


/***/ }),

/***/ 293:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_interactjs__ = __webpack_require__(294);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_interactjs___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_interactjs__);


/* harmony default export */ __webpack_exports__["default"] = ({

    // Our component name
    name: "avatar-image",

    // The properties available for our parent to edit
    props: {
        img: String,
        imageHeight: Number,
        imageWidth: Number,
        cropperWidth: Number,
        cropperHeight: Number,
        shadow: Boolean
    },

    // Our state
    data: function data() {
        return {
            x: 0,
            y: 0,
            adjX: 0,
            adjY: 0,
            resizeHeight: 0,
            resizeWidth: 0,
            viewport: null,
            outer: null
        };
    },


    /**
     * Prepare to render by setting up our viewport
     */
    beforeUpdate: function beforeUpdate() {
        if (this.viewport) {
            this.viewport.x = this.x;
            this.viewport.y = this.y;
            this.viewport.adjX = this.adjX;
            this.viewport.adjY = this.adjY;
            this.viewport.resizeWidth = this.resizeWidth;
            this.viewport.resizeHeight = this.resizeHeight;
        }
    },


    /**
     * Once we are attached
     */
    mounted: function mounted() {
        if (this.shadow === true) {
            this.guessPosition();
            this.setupResizing();
            this.setupDragging();
        }

        // Emit an event
        this.$emit('mount', this);
    },


    methods: {

        /**
         */
        guessPosition: function guessPosition() {
            var adjustedHeight = this.adjustedDimensions(this.imageWidth, this.imageHeight),
                adjX = 0,
                adjY = 0;

            this.resizeHeight = adjustedHeight.height;
            this.resizeWidth = adjustedHeight.width;

            if (this.resizeWidth > this.cropperWidth) {
                adjX = -Math.round((this.resizeWidth - this.cropperWidth) / 2);
            }
            if (this.resizeHeight > this.cropperHeight) {
                adjY = -Math.round((this.resizeHeight - this.cropperHeight) / 2);
            }

            var coords = this.adjustedCoordinates(adjX, adjY, this.resizeWidth, this.resizeHeight);
            this.x += coords.x;
            this.y += coords.y;
        },


        /**
         * Make the avatar resizable
         */
        setupResizing: function setupResizing() {
            var me = this;
            this.interact = __WEBPACK_IMPORTED_MODULE_0_interactjs___default()(this.$refs.image).resizable({
                preserveAspectRatio: true,
                edges: { left: true, right: true, bottom: true, top: true }
            }).on('resizemove', function (event) {
                return me.handleResizeMove(event);
            });
        },


        /**
         * Handle dimensions adjusting
         * @param int width
         * @param int height
         */
        adjustedDimensions: function adjustedDimensions(width, height) {
            var bestFactor = 1,
                maxFactor = Math.sqrt(Math.min(width, height));

            // Find the best factor to downsize by
            for (var i = 2; i <= maxFactor; i++) {
                if (width / i % 2 === 0 && height / i % 2 === 0) {
                    if (width / i > this.cropperWidth && height / i > this.cropperHeight) {
                        bestFactor = i;
                    }
                }
            }

            return {
                width: width / bestFactor,
                height: height / bestFactor,
                factor: bestFactor,
                adjusted: bestFactor !== 1
            };
        },


        /**
         * Handle coordinates adjusting
         * @param int x
         * @param int y
         * @param int width
         * @param int height
         */
        adjustedCoordinates: function adjustedCoordinates(x, y, width, height) {
            var renderedX = this.x + x,
                renderedY = this.y + y,
                coords = {
                min: {
                    x: -1 * (width - this.cropperWidth),
                    y: -1 * (height - this.cropperHeight)
                },
                max: {
                    x: 0,
                    y: 0
                }
            },
                adjustedX = Math.max(coords.min.x, Math.min(coords.max.x, renderedX)) - this.x,
                adjustedY = Math.max(coords.min.y, Math.min(coords.max.y, renderedY)) - this.y;

            return {
                x: adjustedX,
                y: adjustedY,
                adjusted: adjustedY !== y || adjustedX !== x
            };
        },


        /**
         * Attach a parent Avatar if we're a shadow
         * @param Avatar viewport
         */
        setViewport: function setViewport(viewport) {
            this.viewport = viewport;
            viewport.outer = this;
            viewport.setupDragging();
        },


        /**
         * Setup interactjs dragging
         */
        setupDragging: function setupDragging() {
            var me = this;
            this.interact = __WEBPACK_IMPORTED_MODULE_0_interactjs___default()(this.$refs.image).draggable({
                intertia: false,
                restrict: false,

                // Send on move events to component
                onmove: function onmove(e) {
                    if (me.outer) {
                        return me.outer.handleDragMove(e);
                    }
                    return me.handleDragMove(e);
                },

                // Send onstart events to component
                onstart: function onstart(e) {
                    if (me.outer) {
                        return me.outer.handleDragStart(e);
                    }
                    return me.handleDragStart(e);
                },

                // Send onend events to component
                onend: function onend(e) {
                    if (me.outer) {
                        return me.outer.handleDragEnd(e);
                    }
                    return me.handleDragEnd(e);
                }
            });
        },


        /**
         * Handle interactjs drag event
         * @param event
         */
        handleDragMove: function handleDragMove(event) {
            var coords = this.adjustedCoordinates(event.pageX - this.startEvent.pageX, event.pageY - this.startEvent.pageY, this.resizeWidth, this.resizeHeight);

            this.adjX = coords.x;
            this.adjY = coords.y;
        },


        /**
         * Handle interactjs starting to drag
         * @param Event event
         */
        handleDragStart: function handleDragStart(event) {
            this.startEvent = event;
            this.coords = {
                min: {
                    x: -this.resizeWidth + this.cropperWidth,
                    y: -this.resizeHeight + this.cropperHeight
                },
                max: {
                    x: 0,
                    y: 0
                }
            };
        },


        /**
         * Handle interactjs stopping dragging
         * @param event
         */
        handleDragEnd: function handleDragEnd(event) {
            this.x += this.adjX;
            this.y += this.adjY;
            this.adjX = 0;
            this.adjY = 0;
        },


        /**
         * Handle resizing
         * @param event
         */
        handleResizeMove: function handleResizeMove(event) {
            var coordinates = this.adjustedCoordinates(event.deltaRect.left, event.deltaRect.top, event.rect.width, event.rect.height);

            // Don't resize too small
            if (event.rect.width < this.cropperWidth || event.rect.height < this.cropperHeight) {
                // If the image is square
                if (this.imageWidth === this.imageHeight) {
                    this.resizeWidth = this.cropperWidth;
                    this.resizeHeight = this.cropperHeight;
                    this.x = 0;
                    this.y = 0;
                }
                return;
            }

            // update the element's style
            this.resizeWidth = Math.max(event.rect.width, this.cropperWidth);
            this.resizeHeight = Math.max(event.rect.height, this.cropperHeight);

            // translate when resizing from top or left edges
            this.x += coordinates.x;
            this.y += coordinates.y;
        }
    }
});

/***/ }),

/***/ 294:
/***/ (function(module, exports, __webpack_require__) {

/**
 * interact.js 1.5.4
 *
 * Copyright (c) 2012-2019 Taye Adeyemi <dev@taye.me>
 * Released under the MIT License.
 * https://raw.github.com/taye/interact.js/master/LICENSE
 */
(function(f){if(true){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.interact = f()}})(function(){var define,module,exports;
var createModuleFactory = function createModuleFactory(t){var e;return function(r){return e||t(e={exports:{},parent:r},e.exports),e.exports}};
var _$scope_24 = createModuleFactory(function (module, exports) {
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
/* common-shake removed: exports.createScope = */ void createScope;
/* common-shake removed: exports.initScope = */ void initScope;
exports.Scope = exports.ActionName = void 0;

var utils = _interopRequireWildcard(_$utils_56);

var _domObjects = _interopRequireDefault(_$domObjects_50);

var _defaultOptions = _interopRequireDefault(_$defaultOptions_20);

var _Eventable = _interopRequireDefault(_$Eventable_14);

var _Interactable = _interopRequireDefault(_$Interactable_16);

var _InteractableSet = _interopRequireDefault(_$InteractableSet_17);

var _InteractEvent = _interopRequireDefault(_$InteractEvent_15);

var _interactions = _interopRequireDefault(_$interactions_23({}));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var win = utils.win,
    browser = utils.browser,
    raf = utils.raf,
    Signals = utils.Signals,
    events = utils.events;
var ActionName;
exports.ActionName = ActionName;

(function (ActionName) {})(ActionName || (exports.ActionName = ActionName = {}));

function createScope() {
  return new Scope();
}

var Scope =
/*#__PURE__*/
function () {
  function Scope() {
    var _this = this;

    _classCallCheck(this, Scope);

    this.id = "__interact_scope_".concat(Math.floor(Math.random() * 100));
    this.signals = new Signals();
    this.browser = browser;
    this.events = events;
    this.utils = utils;
    this.defaults = utils.clone(_defaultOptions["default"]);
    this.Eventable = _Eventable["default"];
    this.actions = {
      names: [],
      methodDict: {},
      eventTypes: []
    };
    this.InteractEvent = _InteractEvent["default"];
    this.interactables = new _InteractableSet["default"](this); // all documents being listened to

    this.documents = [];
    this._plugins = [];
    this._pluginMap = {};

    this.onWindowUnload = function (event) {
      return _this.removeDocument(event.target);
    };

    var scope = this;

    this.Interactable =
    /*#__PURE__*/
    function (_InteractableBase) {
      _inherits(Interactable, _InteractableBase);

      function Interactable() {
        _classCallCheck(this, Interactable);

        return _possibleConstructorReturn(this, _getPrototypeOf(Interactable).apply(this, arguments));
      }

      _createClass(Interactable, [{
        key: "set",
        value: function set(options) {
          _get(_getPrototypeOf(Interactable.prototype), "set", this).call(this, options);

          scope.interactables.signals.fire('set', {
            options: options,
            interactable: this
          });
          return this;
        }
      }, {
        key: "unset",
        value: function unset() {
          _get(_getPrototypeOf(Interactable.prototype), "unset", this).call(this);

          for (var i = scope.interactions.list.length - 1; i >= 0; i--) {
            var interaction = scope.interactions.list[i];

            if (interaction.interactable === this) {
              interaction.stop();
              scope.interactions.signals.fire('destroy', {
                interaction: interaction
              });
              interaction.destroy();

              if (scope.interactions.list.length > 2) {
                scope.interactions.list.splice(i, 1);
              }
            }
          }

          scope.interactables.signals.fire('unset', {
            interactable: this
          });
        }
      }, {
        key: "_defaults",
        get: function get() {
          return scope.defaults;
        }
      }]);

      return Interactable;
    }(_Interactable["default"]);
  }

  _createClass(Scope, [{
    key: "init",
    value: function init(window) {
      return initScope(this, window);
    }
  }, {
    key: "pluginIsInstalled",
    value: function pluginIsInstalled(plugin) {
      return this._pluginMap[plugin.id] || this._plugins.indexOf(plugin) !== -1;
    }
  }, {
    key: "usePlugin",
    value: function usePlugin(plugin, options) {
      if (this.pluginIsInstalled(plugin)) {
        return this;
      }

      if (plugin.id) {
        this._pluginMap[plugin.id] = plugin;
      }

      plugin.install(this, options);

      this._plugins.push(plugin);

      return this;
    }
  }, {
    key: "addDocument",
    value: function addDocument(doc, options) {
      // do nothing if document is already known
      if (this.getDocIndex(doc) !== -1) {
        return false;
      }

      var window = win.getWindow(doc);
      options = options ? utils.extend({}, options) : {};
      this.documents.push({
        doc: doc,
        options: options
      });
      events.documents.push(doc); // don't add an unload event for the main document
      // so that the page may be cached in browser history

      if (doc !== this.document) {
        events.add(window, 'unload', this.onWindowUnload);
      }

      this.signals.fire('add-document', {
        doc: doc,
        window: window,
        scope: this,
        options: options
      });
    }
  }, {
    key: "removeDocument",
    value: function removeDocument(doc) {
      var index = this.getDocIndex(doc);
      var window = win.getWindow(doc);
      var options = this.documents[index].options;
      events.remove(window, 'unload', this.onWindowUnload);
      this.documents.splice(index, 1);
      events.documents.splice(index, 1);
      this.signals.fire('remove-document', {
        doc: doc,
        window: window,
        scope: this,
        options: options
      });
    }
  }, {
    key: "getDocIndex",
    value: function getDocIndex(doc) {
      for (var i = 0; i < this.documents.length; i++) {
        if (this.documents[i].doc === doc) {
          return i;
        }
      }

      return -1;
    }
  }, {
    key: "getDocOptions",
    value: function getDocOptions(doc) {
      var docIndex = this.getDocIndex(doc);
      return docIndex === -1 ? null : this.documents[docIndex].options;
    }
  }, {
    key: "now",
    value: function now() {
      return (this.window.Date || Date).now();
    }
  }]);

  return Scope;
}();

exports.Scope = Scope;

function initScope(scope, window) {
  win.init(window);

  _domObjects["default"].init(window);

  browser.init(window);
  raf.init(window);
  events.init(window);
  scope.usePlugin(_interactions["default"]);
  scope.document = window.document;
  scope.window = window;
  return scope;
}

});
var _$interactions_23 = createModuleFactory(function (module, exports) {
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _browser = _interopRequireDefault(_$browser_48);

var _domObjects = _interopRequireDefault(_$domObjects_50);

/* removed: var _$domUtils_51 = require("@interactjs/utils/domUtils"); */;

var _events = _interopRequireDefault(_$events_52);

var _pointerUtils = _interopRequireDefault(_$pointerUtils_61);

var _Signals = _interopRequireDefault(_$Signals_46);

var _Interaction = _interopRequireDefault(_$Interaction_18({}));

var _interactionFinder = _interopRequireDefault(_$interactionFinder_22);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var methodNames = ['pointerDown', 'pointerMove', 'pointerUp', 'updatePointer', 'removePointer', 'windowBlur'];

function install(scope) {
  var signals = new _Signals["default"]();
  var listeners = {};

  for (var _i = 0; _i < methodNames.length; _i++) {
    var method = methodNames[_i];
    listeners[method] = doOnInteractions(method, scope);
  }

  var pEventTypes = _browser["default"].pEventTypes;
  var docEvents;

  if (_domObjects["default"].PointerEvent) {
    docEvents = [{
      type: pEventTypes.down,
      listener: releasePointersOnRemovedEls
    }, {
      type: pEventTypes.down,
      listener: listeners.pointerDown
    }, {
      type: pEventTypes.move,
      listener: listeners.pointerMove
    }, {
      type: pEventTypes.up,
      listener: listeners.pointerUp
    }, {
      type: pEventTypes.cancel,
      listener: listeners.pointerUp
    }];
  } else {
    docEvents = [{
      type: 'mousedown',
      listener: listeners.pointerDown
    }, {
      type: 'mousemove',
      listener: listeners.pointerMove
    }, {
      type: 'mouseup',
      listener: listeners.pointerUp
    }, {
      type: 'touchstart',
      listener: releasePointersOnRemovedEls
    }, {
      type: 'touchstart',
      listener: listeners.pointerDown
    }, {
      type: 'touchmove',
      listener: listeners.pointerMove
    }, {
      type: 'touchend',
      listener: listeners.pointerUp
    }, {
      type: 'touchcancel',
      listener: listeners.pointerUp
    }];
  }

  docEvents.push({
    type: 'blur',
    listener: function listener(event) {
      for (var _i2 = 0; _i2 < scope.interactions.list.length; _i2++) {
        var _ref;

        _ref = scope.interactions.list[_i2];
        var interaction = _ref;
        interaction.documentBlur(event);
      }
    }
  });
  scope.signals.on('add-document', onDocSignal);
  scope.signals.on('remove-document', onDocSignal); // for ignoring browser's simulated mouse events

  scope.prevTouchTime = 0;

  scope.Interaction =
  /*#__PURE__*/
  function (_InteractionBase) {
    _inherits(Interaction, _InteractionBase);

    function Interaction() {
      _classCallCheck(this, Interaction);

      return _possibleConstructorReturn(this, _getPrototypeOf(Interaction).apply(this, arguments));
    }

    _createClass(Interaction, [{
      key: "_now",
      value: function _now() {
        return scope.now();
      }
    }, {
      key: "pointerMoveTolerance",
      get: function get() {
        return scope.interactions.pointerMoveTolerance;
      },
      set: function set(value) {
        scope.interactions.pointerMoveTolerance = value;
      }
    }]);

    return Interaction;
  }(_Interaction["default"]);

  scope.interactions = {
    signals: signals,
    // all active and idle interactions
    list: [],
    "new": function _new(options) {
      options.signals = signals;
      var interaction = new scope.Interaction(options);
      scope.interactions.list.push(interaction);
      return interaction;
    },
    listeners: listeners,
    docEvents: docEvents,
    pointerMoveTolerance: 1
  };

  function releasePointersOnRemovedEls() {
    // for all inactive touch interactions with pointers down
    for (var _i3 = 0; _i3 < scope.interactions.list.length; _i3++) {
      var _ref2;

      _ref2 = scope.interactions.list[_i3];
      var interaction = _ref2;

      if (!interaction.pointerIsDown || interaction.pointerType !== 'touch' || interaction._interacting) {
        continue;
      } // if a pointer is down on an element that is no longer in the DOM tree


      var _loop = function _loop() {
        _ref3 = interaction.pointers[_i4];
        var pointer = _ref3;

        if (!scope.documents.some(function (_ref4) {
          var doc = _ref4.doc;
          return (0, _$domUtils_51.nodeContains)(doc, pointer.downTarget);
        })) {
          // remove the pointer from the interaction
          interaction.removePointer(pointer.pointer, pointer.event);
        }
      };

      for (var _i4 = 0; _i4 < interaction.pointers.length; _i4++) {
        var _ref3;

        _loop();
      }
    }
  }
}

function doOnInteractions(method, scope) {
  return function (event) {
    var interactions = scope.interactions.list;

    var pointerType = _pointerUtils["default"].getPointerType(event);

    var _pointerUtils$getEven = _pointerUtils["default"].getEventTargets(event),
        _pointerUtils$getEven2 = _slicedToArray(_pointerUtils$getEven, 2),
        eventTarget = _pointerUtils$getEven2[0],
        curEventTarget = _pointerUtils$getEven2[1];

    var matches = []; // [ [pointer, interaction], ...]

    if (/^touch/.test(event.type)) {
      scope.prevTouchTime = scope.now();

      for (var _i5 = 0; _i5 < event.changedTouches.length; _i5++) {
        var _ref5;

        _ref5 = event.changedTouches[_i5];
        var changedTouch = _ref5;
        var pointer = changedTouch;

        var pointerId = _pointerUtils["default"].getPointerId(pointer);

        var searchDetails = {
          pointer: pointer,
          pointerId: pointerId,
          pointerType: pointerType,
          eventType: event.type,
          eventTarget: eventTarget,
          curEventTarget: curEventTarget,
          scope: scope
        };
        var interaction = getInteraction(searchDetails);
        matches.push([searchDetails.pointer, searchDetails.eventTarget, searchDetails.curEventTarget, interaction]);
      }
    } else {
      var invalidPointer = false;

      if (!_browser["default"].supportsPointerEvent && /mouse/.test(event.type)) {
        // ignore mouse events while touch interactions are active
        for (var i = 0; i < interactions.length && !invalidPointer; i++) {
          invalidPointer = interactions[i].pointerType !== 'mouse' && interactions[i].pointerIsDown;
        } // try to ignore mouse events that are simulated by the browser
        // after a touch event


        invalidPointer = invalidPointer || scope.now() - scope.prevTouchTime < 500 || // on iOS and Firefox Mobile, MouseEvent.timeStamp is zero if simulated
        event.timeStamp === 0;
      }

      if (!invalidPointer) {
        var _searchDetails = {
          pointer: event,
          pointerId: _pointerUtils["default"].getPointerId(event),
          pointerType: pointerType,
          eventType: event.type,
          curEventTarget: curEventTarget,
          eventTarget: eventTarget,
          scope: scope
        };

        var _interaction = getInteraction(_searchDetails);

        matches.push([_searchDetails.pointer, _searchDetails.eventTarget, _searchDetails.curEventTarget, _interaction]);
      }
    } // eslint-disable-next-line no-shadow


    for (var _i6 = 0; _i6 < matches.length; _i6++) {
      var _matches$_i = _slicedToArray(matches[_i6], 4),
          _pointer = _matches$_i[0],
          _eventTarget = _matches$_i[1],
          _curEventTarget = _matches$_i[2],
          _interaction2 = _matches$_i[3];

      _interaction2[method](_pointer, event, _eventTarget, _curEventTarget);
    }
  };
}

function getInteraction(searchDetails) {
  var pointerType = searchDetails.pointerType,
      scope = searchDetails.scope;

  var foundInteraction = _interactionFinder["default"].search(searchDetails);

  var signalArg = {
    interaction: foundInteraction,
    searchDetails: searchDetails
  };
  scope.interactions.signals.fire('find', signalArg);
  return signalArg.interaction || scope.interactions["new"]({
    pointerType: pointerType
  });
}

function onDocSignal(_ref6, signalName) {
  var doc = _ref6.doc,
      scope = _ref6.scope,
      options = _ref6.options;
  var docEvents = scope.interactions.docEvents;
  var eventMethod = signalName.indexOf('add') === 0 ? _events["default"].add : _events["default"].remove;

  if (scope.browser.isIOS && !options.events) {
    options.events = {
      passive: false
    };
  } // delegate event listener


  for (var eventType in _events["default"].delegatedEvents) {
    eventMethod(doc, eventType, _events["default"].delegateListener);
    eventMethod(doc, eventType, _events["default"].delegateUseCapture, true);
  }

  var eventOptions = options && options.events;

  for (var _i7 = 0; _i7 < docEvents.length; _i7++) {
    var _ref7;

    _ref7 = docEvents[_i7];
    var _ref8 = _ref7,
        type = _ref8.type,
        listener = _ref8.listener;
    eventMethod(doc, type, listener, eventOptions);
  }
}

var _default = {
  id: 'core/interactions',
  install: install,
  onDocSignal: onDocSignal,
  doOnInteractions: doOnInteractions,
  methodNames: methodNames
};
exports["default"] = _default;

});
var _$Interaction_18 = createModuleFactory(function (module, exports) {
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
Object.defineProperty(exports, "PointerInfo", {
  enumerable: true,
  get: function get() {
    return _PointerInfo["default"];
  }
});
exports["default"] = exports.Interaction = exports._ProxyMethods = exports._ProxyValues = void 0;

var utils = _interopRequireWildcard(_$utils_56);

var _InteractEvent = _interopRequireWildcard(_$InteractEvent_15);

var _PointerInfo = _interopRequireDefault(_$PointerInfo_19);

var _scope = _$scope_24({});

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var _ProxyValues;

exports._ProxyValues = _ProxyValues;

(function (_ProxyValues) {
  _ProxyValues["interactable"] = "";
  _ProxyValues["element"] = "";
  _ProxyValues["prepared"] = "";
  _ProxyValues["pointerIsDown"] = "";
  _ProxyValues["pointerWasMoved"] = "";
  _ProxyValues["_proxy"] = "";
})(_ProxyValues || (exports._ProxyValues = _ProxyValues = {}));

var _ProxyMethods;

exports._ProxyMethods = _ProxyMethods;

(function (_ProxyMethods) {
  _ProxyMethods["start"] = "";
  _ProxyMethods["move"] = "";
  _ProxyMethods["end"] = "";
  _ProxyMethods["stop"] = "";
  _ProxyMethods["interacting"] = "";
})(_ProxyMethods || (exports._ProxyMethods = _ProxyMethods = {}));

var Interaction =
/*#__PURE__*/
function () {
  /** */
  function Interaction(_ref) {
    var _this = this;

    var pointerType = _ref.pointerType,
        signals = _ref.signals;

    _classCallCheck(this, Interaction);

    // current interactable being interacted with
    this.interactable = null; // the target element of the interactable

    this.element = null; // action that's ready to be fired on next move event

    this.prepared = {
      name: null,
      axis: null,
      edges: null
    }; // keep track of added pointers

    this.pointers = []; // pointerdown/mousedown/touchstart event

    this.downEvent = null;
    this.downPointer = {};
    this._latestPointer = {
      pointer: null,
      event: null,
      eventTarget: null
    }; // previous action event

    this.prevEvent = null;
    this.pointerIsDown = false;
    this.pointerWasMoved = false;
    this._interacting = false;
    this._ending = false;
    this._stopped = true;
    this._proxy = null;
    this.simulation = null;
    /**
     * @alias Interaction.prototype.move
     */

    this.doMove = utils.warnOnce(function (signalArg) {
      this.move(signalArg);
    }, 'The interaction.doMove() method has been renamed to interaction.move()');
    this.coords = {
      // Starting InteractEvent pointer coordinates
      start: utils.pointer.newCoords(),
      // Previous native pointer move event coordinates
      prev: utils.pointer.newCoords(),
      // current native pointer move event coordinates
      cur: utils.pointer.newCoords(),
      // Change in coordinates and time of the pointer
      delta: utils.pointer.newCoords(),
      // pointer velocity
      velocity: utils.pointer.newCoords()
    };
    this._signals = signals;
    this.pointerType = pointerType;
    var that = this;
    this._proxy = {};

    var _loop = function _loop(key) {
      Object.defineProperty(_this._proxy, key, {
        get: function get() {
          return that[key];
        }
      });
    };

    for (var key in _ProxyValues) {
      _loop(key);
    }

    var _loop2 = function _loop2(key) {
      Object.defineProperty(_this._proxy, key, {
        value: function value() {
          return that[key].apply(that, arguments);
        }
      });
    };

    for (var key in _ProxyMethods) {
      _loop2(key);
    }

    this._signals.fire('new', {
      interaction: this
    });
  }

  _createClass(Interaction, [{
    key: "pointerDown",
    value: function pointerDown(pointer, event, eventTarget) {
      var pointerIndex = this.updatePointer(pointer, event, eventTarget, true);

      this._signals.fire('down', {
        pointer: pointer,
        event: event,
        eventTarget: eventTarget,
        pointerIndex: pointerIndex,
        interaction: this
      });
    }
    /**
     * ```js
     * interact(target)
     *   .draggable({
     *     // disable the default drag start by down->move
     *     manualStart: true
     *   })
     *   // start dragging after the user holds the pointer down
     *   .on('hold', function (event) {
     *     var interaction = event.interaction
     *
     *     if (!interaction.interacting()) {
     *       interaction.start({ name: 'drag' },
     *                         event.interactable,
     *                         event.currentTarget)
     *     }
     * })
     * ```
     *
     * Start an action with the given Interactable and Element as tartgets. The
     * action must be enabled for the target Interactable and an appropriate
     * number of pointers must be held down - 1 for drag/resize, 2 for gesture.
     *
     * Use it with `interactable.<action>able({ manualStart: false })` to always
     * [start actions manually](https://github.com/taye/interact.js/issues/114)
     *
     * @param {object} action   The action to be performed - drag, resize, etc.
     * @param {Interactable} target  The Interactable to target
     * @param {Element} element The DOM Element to target
     * @return {object} interact
     */

  }, {
    key: "start",
    value: function start(action, interactable, element) {
      if (this.interacting() || !this.pointerIsDown || this.pointers.length < (action.name === _scope.ActionName.Gesture ? 2 : 1) || !interactable.options[action.name].enabled) {
        return false;
      }

      utils.copyAction(this.prepared, action);
      this.interactable = interactable;
      this.element = element;
      this.rect = interactable.getRect(element);
      this.edges = this.prepared.edges;
      this._stopped = false;
      this._interacting = this._doPhase({
        interaction: this,
        event: this.downEvent,
        phase: _InteractEvent.EventPhase.Start
      }) && !this._stopped;
      return this._interacting;
    }
  }, {
    key: "pointerMove",
    value: function pointerMove(pointer, event, eventTarget) {
      if (!this.simulation && !(this.modifiers && this.modifiers.endPrevented)) {
        this.updatePointer(pointer, event, eventTarget, false);
        utils.pointer.setCoords(this.coords.cur, this.pointers.map(function (p) {
          return p.pointer;
        }), this._now());
      }

      var duplicateMove = this.coords.cur.page.x === this.coords.prev.page.x && this.coords.cur.page.y === this.coords.prev.page.y && this.coords.cur.client.x === this.coords.prev.client.x && this.coords.cur.client.y === this.coords.prev.client.y;
      var dx;
      var dy; // register movement greater than pointerMoveTolerance

      if (this.pointerIsDown && !this.pointerWasMoved) {
        dx = this.coords.cur.client.x - this.coords.start.client.x;
        dy = this.coords.cur.client.y - this.coords.start.client.y;
        this.pointerWasMoved = utils.hypot(dx, dy) > this.pointerMoveTolerance;
      }

      var signalArg = {
        pointer: pointer,
        pointerIndex: this.getPointerIndex(pointer),
        event: event,
        eventTarget: eventTarget,
        dx: dx,
        dy: dy,
        duplicate: duplicateMove,
        interaction: this
      };

      if (!duplicateMove) {
        // set pointer coordinate, time changes and velocity
        utils.pointer.setCoordDeltas(this.coords.delta, this.coords.prev, this.coords.cur);
        utils.pointer.setCoordVelocity(this.coords.velocity, this.coords.delta);
      }

      this._signals.fire('move', signalArg);

      if (!duplicateMove) {
        // if interacting, fire an 'action-move' signal etc
        if (this.interacting()) {
          this.move(signalArg);
        }

        if (this.pointerWasMoved) {
          utils.pointer.copyCoords(this.coords.prev, this.coords.cur);
        }
      }
    }
    /**
     * ```js
     * interact(target)
     *   .draggable(true)
     *   .on('dragmove', function (event) {
     *     if (someCondition) {
     *       // change the snap settings
     *       event.interactable.draggable({ snap: { targets: [] }})
     *       // fire another move event with re-calculated snap
     *       event.interaction.move()
     *     }
     *   })
     * ```
     *
     * Force a move of the current action at the same coordinates. Useful if
     * snap/restrict has been changed and you want a movement with the new
     * settings.
     */

  }, {
    key: "move",
    value: function move(signalArg) {
      signalArg = utils.extend({
        pointer: this._latestPointer.pointer,
        event: this._latestPointer.event,
        eventTarget: this._latestPointer.eventTarget,
        interaction: this
      }, signalArg || {});
      signalArg.phase = _InteractEvent.EventPhase.Move;

      this._doPhase(signalArg);
    } // End interact move events and stop auto-scroll unless simulation is running

  }, {
    key: "pointerUp",
    value: function pointerUp(pointer, event, eventTarget, curEventTarget) {
      var pointerIndex = this.getPointerIndex(pointer);

      if (pointerIndex === -1) {
        pointerIndex = this.updatePointer(pointer, event, eventTarget, false);
      }

      this._signals.fire(/cancel$/i.test(event.type) ? 'cancel' : 'up', {
        pointer: pointer,
        pointerIndex: pointerIndex,
        event: event,
        eventTarget: eventTarget,
        curEventTarget: curEventTarget,
        interaction: this
      });

      if (!this.simulation) {
        this.end(event);
      }

      this.pointerIsDown = false;
      this.removePointer(pointer, event);
    }
  }, {
    key: "documentBlur",
    value: function documentBlur(event) {
      this.end(event);

      this._signals.fire('blur', {
        event: event,
        interaction: this
      });
    }
    /**
     * ```js
     * interact(target)
     *   .draggable(true)
     *   .on('move', function (event) {
     *     if (event.pageX > 1000) {
     *       // end the current action
     *       event.interaction.end()
     *       // stop all further listeners from being called
     *       event.stopImmediatePropagation()
     *     }
     *   })
     * ```
     *
     * @param {PointerEvent} [event]
     */

  }, {
    key: "end",
    value: function end(event) {
      this._ending = true;
      event = event || this._latestPointer.event;
      var endPhaseResult;

      if (this.interacting()) {
        endPhaseResult = this._doPhase({
          event: event,
          interaction: this,
          phase: _InteractEvent.EventPhase.End
        });
      }

      this._ending = false;

      if (endPhaseResult === true) {
        this.stop();
      }
    }
  }, {
    key: "currentAction",
    value: function currentAction() {
      return this._interacting ? this.prepared.name : null;
    }
  }, {
    key: "interacting",
    value: function interacting() {
      return this._interacting;
    }
    /** */

  }, {
    key: "stop",
    value: function stop() {
      this._signals.fire('stop', {
        interaction: this
      });

      this.interactable = this.element = null;
      this._interacting = false;
      this._stopped = true;
      this.prepared.name = this.prevEvent = null;
    }
  }, {
    key: "getPointerIndex",
    value: function getPointerIndex(pointer) {
      var pointerId = utils.pointer.getPointerId(pointer); // mouse and pen interactions may have only one pointer

      return this.pointerType === 'mouse' || this.pointerType === 'pen' ? this.pointers.length - 1 : utils.arr.findIndex(this.pointers, function (curPointer) {
        return curPointer.id === pointerId;
      });
    }
  }, {
    key: "getPointerInfo",
    value: function getPointerInfo(pointer) {
      return this.pointers[this.getPointerIndex(pointer)];
    }
  }, {
    key: "updatePointer",
    value: function updatePointer(pointer, event, eventTarget, down) {
      var id = utils.pointer.getPointerId(pointer);
      var pointerIndex = this.getPointerIndex(pointer);
      var pointerInfo = this.pointers[pointerIndex];
      down = down === false ? false : down || /(down|start)$/i.test(event.type);

      if (!pointerInfo) {
        pointerInfo = new _PointerInfo["default"](id, pointer, event, null, null);
        pointerIndex = this.pointers.length;
        this.pointers.push(pointerInfo);
      } else {
        pointerInfo.pointer = pointer;
      }

      if (down) {
        this.pointerIsDown = true;

        if (!this.interacting()) {
          utils.pointer.setCoords(this.coords.start, this.pointers.map(function (p) {
            return p.pointer;
          }), this._now());
          utils.pointer.copyCoords(this.coords.cur, this.coords.start);
          utils.pointer.copyCoords(this.coords.prev, this.coords.start);
          utils.pointer.pointerExtend(this.downPointer, pointer);
          this.downEvent = event;
          pointerInfo.downTime = this.coords.cur.timeStamp;
          pointerInfo.downTarget = eventTarget;
          this.pointerWasMoved = false;
        }
      }

      this._updateLatestPointer(pointer, event, eventTarget);

      this._signals.fire('update-pointer', {
        pointer: pointer,
        event: event,
        eventTarget: eventTarget,
        down: down,
        pointerInfo: pointerInfo,
        pointerIndex: pointerIndex,
        interaction: this
      });

      return pointerIndex;
    }
  }, {
    key: "removePointer",
    value: function removePointer(pointer, event) {
      var pointerIndex = this.getPointerIndex(pointer);

      if (pointerIndex === -1) {
        return;
      }

      var pointerInfo = this.pointers[pointerIndex];

      this._signals.fire('remove-pointer', {
        pointer: pointer,
        event: event,
        pointerIndex: pointerIndex,
        pointerInfo: pointerInfo,
        interaction: this
      });

      this.pointers.splice(pointerIndex, 1);
    }
  }, {
    key: "_updateLatestPointer",
    value: function _updateLatestPointer(pointer, event, eventTarget) {
      this._latestPointer.pointer = pointer;
      this._latestPointer.event = event;
      this._latestPointer.eventTarget = eventTarget;
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this._latestPointer.pointer = null;
      this._latestPointer.event = null;
      this._latestPointer.eventTarget = null;
    }
  }, {
    key: "_createPreparedEvent",
    value: function _createPreparedEvent(event, phase, preEnd, type) {
      var actionName = this.prepared.name;
      return new _InteractEvent["default"](this, event, actionName, phase, this.element, null, preEnd, type);
    }
  }, {
    key: "_fireEvent",
    value: function _fireEvent(iEvent) {
      this.interactable.fire(iEvent);

      if (!this.prevEvent || iEvent.timeStamp >= this.prevEvent.timeStamp) {
        this.prevEvent = iEvent;
      }
    }
  }, {
    key: "_doPhase",
    value: function _doPhase(signalArg) {
      var event = signalArg.event,
          phase = signalArg.phase,
          preEnd = signalArg.preEnd,
          type = signalArg.type;

      var beforeResult = this._signals.fire("before-action-".concat(phase), signalArg);

      if (beforeResult === false) {
        return false;
      }

      var iEvent = signalArg.iEvent = this._createPreparedEvent(event, phase, preEnd, type);

      var rect = this.rect;

      if (rect) {
        // update the rect modifications
        var edges = this.edges || this.prepared.edges || {
          left: true,
          right: true,
          top: true,
          bottom: true
        };

        if (edges.top) {
          rect.top += iEvent.delta.y;
        }

        if (edges.bottom) {
          rect.bottom += iEvent.delta.y;
        }

        if (edges.left) {
          rect.left += iEvent.delta.x;
        }

        if (edges.right) {
          rect.right += iEvent.delta.x;
        }

        rect.width = rect.right - rect.left;
        rect.height = rect.bottom - rect.top;
      }

      this._signals.fire("action-".concat(phase), signalArg);

      this._fireEvent(iEvent);

      this._signals.fire("after-action-".concat(phase), signalArg);

      return true;
    }
  }, {
    key: "_now",
    value: function _now() {
      return Date.now();
    }
  }, {
    key: "pointerMoveTolerance",
    get: function get() {
      return 1;
    }
  }]);

  return Interaction;
}();

exports.Interaction = Interaction;
var _default = Interaction;
exports["default"] = _default;

});
var _$arr_47 = {};
"use strict";

Object.defineProperty(_$arr_47, "__esModule", {
  value: true
});
_$arr_47.contains = contains;
_$arr_47.remove = remove;
_$arr_47.merge = merge;
_$arr_47.from = from;
_$arr_47.findIndex = findIndex;
_$arr_47.find = find;

function contains(array, target) {
  return array.indexOf(target) !== -1;
}

function remove(array, target) {
  return array.splice(array.indexOf(target), 1);
}

function merge(target, source) {
  for (var _i = 0; _i < source.length; _i++) {
    var _ref;

    _ref = source[_i];
    var item = _ref;
    target.push(item);
  }

  return target;
}

function from(source) {
  return merge([], source);
}

function findIndex(array, func) {
  for (var i = 0; i < array.length; i++) {
    if (func(array[i], i, array)) {
      return i;
    }
  }

  return -1;
}

function find(array, func) {
  return array[findIndex(array, func)];
}

var _$domObjects_50 = {};
"use strict";

Object.defineProperty(_$domObjects_50, "__esModule", {
  value: true
});
_$domObjects_50["default"] = void 0;
var domObjects = {
  init: init,
  document: null,
  DocumentFragment: null,
  SVGElement: null,
  SVGSVGElement: null,
  // eslint-disable-next-line no-undef
  SVGElementInstance: null,
  Element: null,
  HTMLElement: null,
  Event: null,
  Touch: null,
  PointerEvent: null
};

function blank() {}

var _default = domObjects;
_$domObjects_50["default"] = _default;

function init(window) {
  var win = window;
  domObjects.document = win.document;
  domObjects.DocumentFragment = win.DocumentFragment || blank;
  domObjects.SVGElement = win.SVGElement || blank;
  domObjects.SVGSVGElement = win.SVGSVGElement || blank;
  domObjects.SVGElementInstance = win.SVGElementInstance || blank;
  domObjects.Element = win.Element || blank;
  domObjects.HTMLElement = win.HTMLElement || domObjects.Element;
  domObjects.Event = win.Event;
  domObjects.Touch = win.Touch || blank;
  domObjects.PointerEvent = win.PointerEvent || win.MSPointerEvent;
}

var _$isWindow_58 = {};
"use strict";

Object.defineProperty(_$isWindow_58, "__esModule", {
  value: true
});
_$isWindow_58["default"] = void 0;

var ___default_58 = function _default(thing) {
  return !!(thing && thing.Window) && thing instanceof thing.Window;
};

_$isWindow_58["default"] = ___default_58;

var _$window_66 = {};
"use strict";

Object.defineProperty(_$window_66, "__esModule", {
  value: true
});
_$window_66.init = __init_66;
_$window_66.getWindow = getWindow;
_$window_66["default"] = void 0;

var _isWindow = _interopRequireDefault(_$isWindow_58);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var win = {
  realWindow: undefined,
  window: undefined,
  getWindow: getWindow,
  init: __init_66
};

function __init_66(window) {
  // get wrapped window if using Shadow DOM polyfill
  win.realWindow = window; // create a TextNode

  var el = window.document.createTextNode(''); // check if it's wrapped by a polyfill

  if (el.ownerDocument !== window.document && typeof window.wrap === 'function' && window.wrap(el) === el) {
    // use wrapped window
    window = window.wrap(window);
  }

  win.window = window;
}

if (typeof window === 'undefined') {
  win.window = undefined;
  win.realWindow = undefined;
} else {
  __init_66(window);
}

function getWindow(node) {
  if ((0, _isWindow["default"])(node)) {
    return node;
  }

  var rootNode = node.ownerDocument || node;
  return rootNode.defaultView || win.window;
}

win.init = __init_66;
var ___default_66 = win;
_$window_66["default"] = ___default_66;

var _$is_57 = {};
"use strict";

Object.defineProperty(_$is_57, "__esModule", {
  value: true
});
_$is_57.array = _$is_57.plainObject = _$is_57.element = _$is_57.string = _$is_57.bool = _$is_57.number = _$is_57.func = _$is_57.object = _$is_57.docFrag = _$is_57.window = void 0;

var ___isWindow_57 = ___interopRequireDefault_57(_$isWindow_58);

var _window2 = ___interopRequireDefault_57(_$window_66);

function ___interopRequireDefault_57(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

var __window_57 = function window(thing) {
  return thing === _window2["default"].window || (0, ___isWindow_57["default"])(thing);
};

_$is_57.window = __window_57;

var docFrag = function docFrag(thing) {
  return object(thing) && thing.nodeType === 11;
};

_$is_57.docFrag = docFrag;

var object = function object(thing) {
  return !!thing && _typeof(thing) === 'object';
};

_$is_57.object = object;

var func = function func(thing) {
  return typeof thing === 'function';
};

_$is_57.func = func;

var number = function number(thing) {
  return typeof thing === 'number';
};

_$is_57.number = number;

var bool = function bool(thing) {
  return typeof thing === 'boolean';
};

_$is_57.bool = bool;

var string = function string(thing) {
  return typeof thing === 'string';
};

_$is_57.string = string;

var element = function element(thing) {
  if (!thing || _typeof(thing) !== 'object') {
    return false;
  }

  var _window = _window2["default"].getWindow(thing) || _window2["default"].window;

  return /object|function/.test(_typeof(_window.Element)) ? thing instanceof _window.Element // DOM2
  : thing.nodeType === 1 && typeof thing.nodeName === 'string';
};

_$is_57.element = element;

var plainObject = function plainObject(thing) {
  return object(thing) && !!thing.constructor && /function Object\b/.test(thing.constructor.toString());
};

_$is_57.plainObject = plainObject;

var array = function array(thing) {
  return object(thing) && typeof thing.length !== 'undefined' && func(thing.splice);
};

_$is_57.array = array;

var _$browser_48 = {};
"use strict";

Object.defineProperty(_$browser_48, "__esModule", {
  value: true
});
_$browser_48["default"] = void 0;

var _domObjects = ___interopRequireDefault_48(_$domObjects_50);

var is = _interopRequireWildcard(_$is_57);

var _window = ___interopRequireDefault_48(_$window_66);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_48(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var browser = {
  init: __init_48,
  supportsTouch: null,
  supportsPointerEvent: null,
  isIOS7: null,
  isIOS: null,
  isIe9: null,
  isOperaMobile: null,
  prefixedMatchesSelector: null,
  pEventTypes: null,
  wheelEvent: null
};

function __init_48(window) {
  var Element = _domObjects["default"].Element;
  var navigator = _window["default"].window.navigator; // Does the browser support touch input?

  browser.supportsTouch = 'ontouchstart' in window || is.func(window.DocumentTouch) && _domObjects["default"].document instanceof window.DocumentTouch; // Does the browser support PointerEvents

  browser.supportsPointerEvent = navigator.pointerEnabled !== false && !!_domObjects["default"].PointerEvent;
  browser.isIOS = /iP(hone|od|ad)/.test(navigator.platform); // scrolling doesn't change the result of getClientRects on iOS 7

  browser.isIOS7 = /iP(hone|od|ad)/.test(navigator.platform) && /OS 7[^\d]/.test(navigator.appVersion);
  browser.isIe9 = /MSIE 9/.test(navigator.userAgent); // Opera Mobile must be handled differently

  browser.isOperaMobile = navigator.appName === 'Opera' && browser.supportsTouch && /Presto/.test(navigator.userAgent); // prefix matchesSelector

  browser.prefixedMatchesSelector = 'matches' in Element.prototype ? 'matches' : 'webkitMatchesSelector' in Element.prototype ? 'webkitMatchesSelector' : 'mozMatchesSelector' in Element.prototype ? 'mozMatchesSelector' : 'oMatchesSelector' in Element.prototype ? 'oMatchesSelector' : 'msMatchesSelector';
  browser.pEventTypes = browser.supportsPointerEvent ? _domObjects["default"].PointerEvent === window.MSPointerEvent ? {
    up: 'MSPointerUp',
    down: 'MSPointerDown',
    over: 'mouseover',
    out: 'mouseout',
    move: 'MSPointerMove',
    cancel: 'MSPointerCancel'
  } : {
    up: 'pointerup',
    down: 'pointerdown',
    over: 'pointerover',
    out: 'pointerout',
    move: 'pointermove',
    cancel: 'pointercancel'
  } : null; // because Webkit and Opera still use 'mousewheel' event type

  browser.wheelEvent = 'onmousewheel' in _domObjects["default"].document ? 'mousewheel' : 'wheel';
}

var ___default_48 = browser;
_$browser_48["default"] = ___default_48;

var _$domUtils_51 = {};
"use strict";

Object.defineProperty(_$domUtils_51, "__esModule", {
  value: true
});
_$domUtils_51.nodeContains = nodeContains;
_$domUtils_51.closest = closest;
_$domUtils_51.parentNode = parentNode;
_$domUtils_51.matchesSelector = matchesSelector;
_$domUtils_51.indexOfDeepestElement = indexOfDeepestElement;
_$domUtils_51.matchesUpTo = matchesUpTo;
_$domUtils_51.getActualElement = getActualElement;
_$domUtils_51.getScrollXY = getScrollXY;
_$domUtils_51.getElementClientRect = getElementClientRect;
_$domUtils_51.getElementRect = getElementRect;
_$domUtils_51.getPath = getPath;
_$domUtils_51.trySelector = trySelector;

var _browser = ___interopRequireDefault_51(_$browser_48);

var ___domObjects_51 = ___interopRequireDefault_51(_$domObjects_50);

var __is_51 = ___interopRequireWildcard_51(_$is_57);

var ___window_51 = ___interopRequireDefault_51(_$window_66);

function ___interopRequireWildcard_51(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_51(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function nodeContains(parent, child) {
  while (child) {
    if (child === parent) {
      return true;
    }

    child = child.parentNode;
  }

  return false;
}

function closest(element, selector) {
  while (__is_51.element(element)) {
    if (matchesSelector(element, selector)) {
      return element;
    }

    element = parentNode(element);
  }

  return null;
}

function parentNode(node) {
  var parent = node.parentNode;

  if (__is_51.docFrag(parent)) {
    // skip past #shado-root fragments
    // tslint:disable-next-line
    while ((parent = parent.host) && __is_51.docFrag(parent)) {
      continue;
    }

    return parent;
  }

  return parent;
}

function matchesSelector(element, selector) {
  // remove /deep/ from selectors if shadowDOM polyfill is used
  if (___window_51["default"].window !== ___window_51["default"].realWindow) {
    selector = selector.replace(/\/deep\//g, ' ');
  }

  return element[_browser["default"].prefixedMatchesSelector](selector);
}

var getParent = function getParent(el) {
  return el.parentNode ? el.parentNode : el.host;
}; // Test for the element that's "above" all other qualifiers


function indexOfDeepestElement(elements) {
  var deepestZoneParents = [];
  var dropzoneParents = [];
  var dropzone;
  var deepestZone = elements[0];
  var index = deepestZone ? 0 : -1;
  var parent;
  var child;
  var i;
  var n;

  for (i = 1; i < elements.length; i++) {
    dropzone = elements[i]; // an element might belong to multiple selector dropzones

    if (!dropzone || dropzone === deepestZone) {
      continue;
    }

    if (!deepestZone) {
      deepestZone = dropzone;
      index = i;
      continue;
    } // check if the deepest or current are document.documentElement or document.rootElement
    // - if the current dropzone is, do nothing and continue


    if (dropzone.parentNode === dropzone.ownerDocument) {
      continue;
    } // - if deepest is, update with the current dropzone and continue to next
    else if (deepestZone.parentNode === dropzone.ownerDocument) {
        deepestZone = dropzone;
        index = i;
        continue;
      }

    if (!deepestZoneParents.length) {
      parent = deepestZone;

      while (getParent(parent) && getParent(parent) !== parent.ownerDocument) {
        deepestZoneParents.unshift(parent);
        parent = getParent(parent);
      }
    } // if this element is an svg element and the current deepest is
    // an HTMLElement


    if (deepestZone instanceof ___domObjects_51["default"].HTMLElement && dropzone instanceof ___domObjects_51["default"].SVGElement && !(dropzone instanceof ___domObjects_51["default"].SVGSVGElement)) {
      if (dropzone === deepestZone.parentNode) {
        continue;
      }

      parent = dropzone.ownerSVGElement;
    } else {
      parent = dropzone;
    }

    dropzoneParents = [];

    while (parent.parentNode !== parent.ownerDocument) {
      dropzoneParents.unshift(parent);
      parent = getParent(parent);
    }

    n = 0; // get (position of last common ancestor) + 1

    while (dropzoneParents[n] && dropzoneParents[n] === deepestZoneParents[n]) {
      n++;
    }

    var parents = [dropzoneParents[n - 1], dropzoneParents[n], deepestZoneParents[n]];
    child = parents[0].lastChild;

    while (child) {
      if (child === parents[1]) {
        deepestZone = dropzone;
        index = i;
        deepestZoneParents = [];
        break;
      } else if (child === parents[2]) {
        break;
      }

      child = child.previousSibling;
    }
  }

  return index;
}

function matchesUpTo(element, selector, limit) {
  while (__is_51.element(element)) {
    if (matchesSelector(element, selector)) {
      return true;
    }

    element = parentNode(element);

    if (element === limit) {
      return matchesSelector(element, selector);
    }
  }

  return false;
}

function getActualElement(element) {
  return element instanceof ___domObjects_51["default"].SVGElementInstance ? element.correspondingUseElement : element;
}

function getScrollXY(relevantWindow) {
  relevantWindow = relevantWindow || ___window_51["default"].window;
  return {
    x: relevantWindow.scrollX || relevantWindow.document.documentElement.scrollLeft,
    y: relevantWindow.scrollY || relevantWindow.document.documentElement.scrollTop
  };
}

function getElementClientRect(element) {
  var clientRect = element instanceof ___domObjects_51["default"].SVGElement ? element.getBoundingClientRect() : element.getClientRects()[0];
  return clientRect && {
    left: clientRect.left,
    right: clientRect.right,
    top: clientRect.top,
    bottom: clientRect.bottom,
    width: clientRect.width || clientRect.right - clientRect.left,
    height: clientRect.height || clientRect.bottom - clientRect.top
  };
}

function getElementRect(element) {
  var clientRect = getElementClientRect(element);

  if (!_browser["default"].isIOS7 && clientRect) {
    var scroll = getScrollXY(___window_51["default"].getWindow(element));
    clientRect.left += scroll.x;
    clientRect.right += scroll.x;
    clientRect.top += scroll.y;
    clientRect.bottom += scroll.y;
  }

  return clientRect;
}

function getPath(node) {
  var path = [];

  while (node) {
    path.push(node);
    node = parentNode(node);
  }

  return path;
}

function trySelector(value) {
  if (!__is_51.string(value)) {
    return false;
  } // an exception will be raised if it is invalid


  ___domObjects_51["default"].document.querySelector(value);

  return true;
}

var _$clone_49 = {};
"use strict";

Object.defineProperty(_$clone_49, "__esModule", {
  value: true
});
_$clone_49["default"] = clone;

var arr = ___interopRequireWildcard_49(_$arr_47);

var __is_49 = ___interopRequireWildcard_49(_$is_57);

function ___interopRequireWildcard_49(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function clone(source) {
  var dest = {};

  for (var prop in source) {
    var value = source[prop];

    if (__is_49.plainObject(value)) {
      dest[prop] = clone(value);
    } else if (__is_49.array(value)) {
      dest[prop] = arr.from(value);
    } else {
      dest[prop] = value;
    }
  }

  return dest;
}

var _$pointerExtend_60 = {};
"use strict";

Object.defineProperty(_$pointerExtend_60, "__esModule", {
  value: true
});
_$pointerExtend_60["default"] = void 0;

function pointerExtend(dest, source) {
  for (var prop in source) {
    var prefixedPropREs = pointerExtend.prefixedPropREs;
    var deprecated = false; // skip deprecated prefixed properties

    for (var vendor in prefixedPropREs) {
      if (prop.indexOf(vendor) === 0 && prefixedPropREs[vendor].test(prop)) {
        deprecated = true;
        break;
      }
    }

    if (!deprecated && typeof source[prop] !== 'function') {
      dest[prop] = source[prop];
    }
  }

  return dest;
}

pointerExtend.prefixedPropREs = {
  webkit: /(Movement[XY]|Radius[XY]|RotationAngle|Force)$/
};
var ___default_60 = pointerExtend;
_$pointerExtend_60["default"] = ___default_60;

var _$hypot_55 = {};
"use strict";

Object.defineProperty(_$hypot_55, "__esModule", {
  value: true
});
_$hypot_55["default"] = void 0;

var ___default_55 = function _default(x, y) {
  return Math.sqrt(x * x + y * y);
};

_$hypot_55["default"] = ___default_55;

var _$pointerUtils_61 = {};
"use strict";

Object.defineProperty(_$pointerUtils_61, "__esModule", {
  value: true
});
_$pointerUtils_61["default"] = void 0;

var ___browser_61 = ___interopRequireDefault_61(_$browser_48);

var ___domObjects_61 = ___interopRequireDefault_61(_$domObjects_50);

var domUtils = ___interopRequireWildcard_61(_$domUtils_51);

var _hypot = ___interopRequireDefault_61(_$hypot_55);

var __is_61 = ___interopRequireWildcard_61(_$is_57);

var _pointerExtend = ___interopRequireDefault_61(_$pointerExtend_60);

function ___interopRequireWildcard_61(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_61(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var pointerUtils = {
  copyCoords: function copyCoords(dest, src) {
    dest.page = dest.page || {};
    dest.page.x = src.page.x;
    dest.page.y = src.page.y;
    dest.client = dest.client || {};
    dest.client.x = src.client.x;
    dest.client.y = src.client.y;
    dest.timeStamp = src.timeStamp;
  },
  setCoordDeltas: function setCoordDeltas(targetObj, prev, cur) {
    targetObj.page.x = cur.page.x - prev.page.x;
    targetObj.page.y = cur.page.y - prev.page.y;
    targetObj.client.x = cur.client.x - prev.client.x;
    targetObj.client.y = cur.client.y - prev.client.y;
    targetObj.timeStamp = cur.timeStamp - prev.timeStamp;
  },
  setCoordVelocity: function setCoordVelocity(targetObj, delta) {
    var dt = Math.max(delta.timeStamp / 1000, 0.001);
    targetObj.page.x = delta.page.x / dt;
    targetObj.page.y = delta.page.y / dt;
    targetObj.client.x = delta.client.x / dt;
    targetObj.client.y = delta.client.y / dt;
    targetObj.timeStamp = dt;
  },
  isNativePointer: function isNativePointer(pointer) {
    return pointer instanceof ___domObjects_61["default"].Event || pointer instanceof ___domObjects_61["default"].Touch;
  },
  // Get specified X/Y coords for mouse or event.touches[0]
  getXY: function getXY(type, pointer, xy) {
    xy = xy || {};
    type = type || 'page';
    xy.x = pointer[type + 'X'];
    xy.y = pointer[type + 'Y'];
    return xy;
  },
  getPageXY: function getPageXY(pointer, page) {
    page = page || {
      x: 0,
      y: 0
    }; // Opera Mobile handles the viewport and scrolling oddly

    if (___browser_61["default"].isOperaMobile && pointerUtils.isNativePointer(pointer)) {
      pointerUtils.getXY('screen', pointer, page);
      page.x += window.scrollX;
      page.y += window.scrollY;
    } else {
      pointerUtils.getXY('page', pointer, page);
    }

    return page;
  },
  getClientXY: function getClientXY(pointer, client) {
    client = client || {};

    if (___browser_61["default"].isOperaMobile && pointerUtils.isNativePointer(pointer)) {
      // Opera Mobile handles the viewport and scrolling oddly
      pointerUtils.getXY('screen', pointer, client);
    } else {
      pointerUtils.getXY('client', pointer, client);
    }

    return client;
  },
  getPointerId: function getPointerId(pointer) {
    return __is_61.number(pointer.pointerId) ? pointer.pointerId : pointer.identifier;
  },
  setCoords: function setCoords(targetObj, pointers, timeStamp) {
    var pointer = pointers.length > 1 ? pointerUtils.pointerAverage(pointers) : pointers[0];
    var tmpXY = {};
    pointerUtils.getPageXY(pointer, tmpXY);
    targetObj.page.x = tmpXY.x;
    targetObj.page.y = tmpXY.y;
    pointerUtils.getClientXY(pointer, tmpXY);
    targetObj.client.x = tmpXY.x;
    targetObj.client.y = tmpXY.y;
    targetObj.timeStamp = timeStamp;
  },
  pointerExtend: _pointerExtend["default"],
  getTouchPair: function getTouchPair(event) {
    var touches = []; // array of touches is supplied

    if (__is_61.array(event)) {
      touches[0] = event[0];
      touches[1] = event[1];
    } // an event
    else {
        if (event.type === 'touchend') {
          if (event.touches.length === 1) {
            touches[0] = event.touches[0];
            touches[1] = event.changedTouches[0];
          } else if (event.touches.length === 0) {
            touches[0] = event.changedTouches[0];
            touches[1] = event.changedTouches[1];
          }
        } else {
          touches[0] = event.touches[0];
          touches[1] = event.touches[1];
        }
      }

    return touches;
  },
  pointerAverage: function pointerAverage(pointers) {
    var average = {
      pageX: 0,
      pageY: 0,
      clientX: 0,
      clientY: 0,
      screenX: 0,
      screenY: 0
    };

    for (var _i = 0; _i < pointers.length; _i++) {
      var _ref;

      _ref = pointers[_i];
      var pointer = _ref;

      for (var _prop in average) {
        average[_prop] += pointer[_prop];
      }
    }

    for (var prop in average) {
      average[prop] /= pointers.length;
    }

    return average;
  },
  touchBBox: function touchBBox(event) {
    if (!event.length && !(event.touches && event.touches.length > 1)) {
      return null;
    }

    var touches = pointerUtils.getTouchPair(event);
    var minX = Math.min(touches[0].pageX, touches[1].pageX);
    var minY = Math.min(touches[0].pageY, touches[1].pageY);
    var maxX = Math.max(touches[0].pageX, touches[1].pageX);
    var maxY = Math.max(touches[0].pageY, touches[1].pageY);
    return {
      x: minX,
      y: minY,
      left: minX,
      top: minY,
      right: maxX,
      bottom: maxY,
      width: maxX - minX,
      height: maxY - minY
    };
  },
  touchDistance: function touchDistance(event, deltaSource) {
    var sourceX = deltaSource + 'X';
    var sourceY = deltaSource + 'Y';
    var touches = pointerUtils.getTouchPair(event);
    var dx = touches[0][sourceX] - touches[1][sourceX];
    var dy = touches[0][sourceY] - touches[1][sourceY];
    return (0, _hypot["default"])(dx, dy);
  },
  touchAngle: function touchAngle(event, deltaSource) {
    var sourceX = deltaSource + 'X';
    var sourceY = deltaSource + 'Y';
    var touches = pointerUtils.getTouchPair(event);
    var dx = touches[1][sourceX] - touches[0][sourceX];
    var dy = touches[1][sourceY] - touches[0][sourceY];
    var angle = 180 * Math.atan2(dy, dx) / Math.PI;
    return angle;
  },
  getPointerType: function getPointerType(pointer) {
    return __is_61.string(pointer.pointerType) ? pointer.pointerType : __is_61.number(pointer.pointerType) ? [undefined, undefined, 'touch', 'pen', 'mouse'][pointer.pointerType] // if the PointerEvent API isn't available, then the "pointer" must
    // be either a MouseEvent, TouchEvent, or Touch object
    : /touch/.test(pointer.type) || pointer instanceof ___domObjects_61["default"].Touch ? 'touch' : 'mouse';
  },
  // [ event.target, event.currentTarget ]
  getEventTargets: function getEventTargets(event) {
    var path = __is_61.func(event.composedPath) ? event.composedPath() : event.path;
    return [domUtils.getActualElement(path ? path[0] : event.target), domUtils.getActualElement(event.currentTarget)];
  },
  newCoords: function newCoords() {
    return {
      page: {
        x: 0,
        y: 0
      },
      client: {
        x: 0,
        y: 0
      },
      timeStamp: 0
    };
  },
  coordsToEvent: function coordsToEvent(coords) {
    var event = {
      coords: coords,

      get page() {
        return this.coords.page;
      },

      get client() {
        return this.coords.client;
      },

      get timeStamp() {
        return this.coords.timeStamp;
      },

      get pageX() {
        return this.coords.page.x;
      },

      get pageY() {
        return this.coords.page.y;
      },

      get clientX() {
        return this.coords.client.x;
      },

      get clientY() {
        return this.coords.client.y;
      },

      get pointerId() {
        return this.coords.pointerId;
      },

      get target() {
        return this.coords.target;
      },

      get type() {
        return this.coords.type;
      },

      get pointerType() {
        return this.coords.pointerType;
      },

      get buttons() {
        return this.coords.buttons;
      }

    };
    return event;
  }
};
var ___default_61 = pointerUtils;
_$pointerUtils_61["default"] = ___default_61;

var _$events_52 = {};
"use strict";

Object.defineProperty(_$events_52, "__esModule", {
  value: true
});
_$events_52["default"] = _$events_52.FakeEvent = void 0;

/* removed: var _$arr_47 = require("./arr"); */;

var __domUtils_52 = ___interopRequireWildcard_52(_$domUtils_51);

var __is_52 = ___interopRequireWildcard_52(_$is_57);

var ___pointerExtend_52 = ___interopRequireDefault_52(_$pointerExtend_60);

var _pointerUtils = ___interopRequireDefault_52(_$pointerUtils_61);

function ___interopRequireDefault_52(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_52(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

var elements = [];
var targets = [];
var delegatedEvents = {};
var documents = [];

function add(element, type, listener, optionalArg) {
  var options = getOptions(optionalArg);
  var elementIndex = elements.indexOf(element);
  var target = targets[elementIndex];

  if (!target) {
    target = {
      events: {},
      typeCount: 0
    };
    elementIndex = elements.push(element) - 1;
    targets.push(target);
  }

  if (!target.events[type]) {
    target.events[type] = [];
    target.typeCount++;
  }

  if (!(0, _$arr_47.contains)(target.events[type], listener)) {
    element.addEventListener(type, listener, events.supportsOptions ? options : !!options.capture);
    target.events[type].push(listener);
  }
}

function __remove_52(element, type, listener, optionalArg) {
  var options = getOptions(optionalArg);
  var elementIndex = elements.indexOf(element);
  var target = targets[elementIndex];

  if (!target || !target.events) {
    return;
  }

  if (type === 'all') {
    for (type in target.events) {
      if (target.events.hasOwnProperty(type)) {
        __remove_52(element, type, 'all');
      }
    }

    return;
  }

  if (target.events[type]) {
    var len = target.events[type].length;

    if (listener === 'all') {
      for (var i = 0; i < len; i++) {
        __remove_52(element, type, target.events[type][i], options);
      }

      return;
    } else {
      for (var _i = 0; _i < len; _i++) {
        if (target.events[type][_i] === listener) {
          element.removeEventListener(type, listener, events.supportsOptions ? options : !!options.capture);
          target.events[type].splice(_i, 1);
          break;
        }
      }
    }

    if (target.events[type] && target.events[type].length === 0) {
      target.events[type] = null;
      target.typeCount--;
    }
  }

  if (!target.typeCount) {
    targets.splice(elementIndex, 1);
    elements.splice(elementIndex, 1);
  }
}

function addDelegate(selector, context, type, listener, optionalArg) {
  var options = getOptions(optionalArg);

  if (!delegatedEvents[type]) {
    delegatedEvents[type] = {
      contexts: [],
      listeners: [],
      selectors: []
    }; // add delegate listener functions

    for (var _i2 = 0; _i2 < documents.length; _i2++) {
      var doc = documents[_i2];
      add(doc, type, delegateListener);
      add(doc, type, delegateUseCapture, true);
    }
  }

  var delegated = delegatedEvents[type];
  var index;

  for (index = delegated.selectors.length - 1; index >= 0; index--) {
    if (delegated.selectors[index] === selector && delegated.contexts[index] === context) {
      break;
    }
  }

  if (index === -1) {
    index = delegated.selectors.length;
    delegated.selectors.push(selector);
    delegated.contexts.push(context);
    delegated.listeners.push([]);
  } // keep listener and capture and passive flags


  delegated.listeners[index].push([listener, !!options.capture, options.passive]);
}

function removeDelegate(selector, context, type, listener, optionalArg) {
  var options = getOptions(optionalArg);
  var delegated = delegatedEvents[type];
  var matchFound = false;
  var index;

  if (!delegated) {
    return;
  } // count from last index of delegated to 0


  for (index = delegated.selectors.length - 1; index >= 0; index--) {
    // look for matching selector and context Node
    if (delegated.selectors[index] === selector && delegated.contexts[index] === context) {
      var listeners = delegated.listeners[index]; // each item of the listeners array is an array: [function, capture, passive]

      for (var i = listeners.length - 1; i >= 0; i--) {
        var _listeners$i = _slicedToArray(listeners[i], 3),
            fn = _listeners$i[0],
            capture = _listeners$i[1],
            passive = _listeners$i[2]; // check if the listener functions and capture and passive flags match


        if (fn === listener && capture === !!options.capture && passive === options.passive) {
          // remove the listener from the array of listeners
          listeners.splice(i, 1); // if all listeners for this interactable have been removed
          // remove the interactable from the delegated arrays

          if (!listeners.length) {
            delegated.selectors.splice(index, 1);
            delegated.contexts.splice(index, 1);
            delegated.listeners.splice(index, 1); // remove delegate function from context

            __remove_52(context, type, delegateListener);
            __remove_52(context, type, delegateUseCapture, true); // remove the arrays if they are empty

            if (!delegated.selectors.length) {
              delegatedEvents[type] = null;
            }
          } // only remove one listener


          matchFound = true;
          break;
        }
      }

      if (matchFound) {
        break;
      }
    }
  }
} // bound to the interactable context when a DOM event
// listener is added to a selector interactable


function delegateListener(event, optionalArg) {
  var options = getOptions(optionalArg);
  var fakeEvent = new FakeEvent(event);
  var delegated = delegatedEvents[event.type];

  var _pointerUtils$getEven = _pointerUtils["default"].getEventTargets(event),
      _pointerUtils$getEven2 = _slicedToArray(_pointerUtils$getEven, 1),
      eventTarget = _pointerUtils$getEven2[0];

  var element = eventTarget; // climb up document tree looking for selector matches

  while (__is_52.element(element)) {
    for (var i = 0; i < delegated.selectors.length; i++) {
      var selector = delegated.selectors[i];
      var context = delegated.contexts[i];

      if (__domUtils_52.matchesSelector(element, selector) && __domUtils_52.nodeContains(context, eventTarget) && __domUtils_52.nodeContains(context, element)) {
        var listeners = delegated.listeners[i];
        fakeEvent.currentTarget = element;

        for (var _i3 = 0; _i3 < listeners.length; _i3++) {
          var _ref;

          _ref = listeners[_i3];

          var _ref2 = _ref,
              _ref3 = _slicedToArray(_ref2, 3),
              fn = _ref3[0],
              capture = _ref3[1],
              passive = _ref3[2];

          if (capture === !!options.capture && passive === options.passive) {
            fn(fakeEvent);
          }
        }
      }
    }

    element = __domUtils_52.parentNode(element);
  }
}

function delegateUseCapture(event) {
  return delegateListener.call(this, event, true);
}

function getOptions(param) {
  return __is_52.object(param) ? param : {
    capture: param
  };
}

var FakeEvent =
/*#__PURE__*/
function () {
  function FakeEvent(originalEvent) {
    _classCallCheck(this, FakeEvent);

    this.originalEvent = originalEvent; // duplicate the event so that currentTarget can be changed

    (0, ___pointerExtend_52["default"])(this, originalEvent);
  }

  _createClass(FakeEvent, [{
    key: "preventOriginalDefault",
    value: function preventOriginalDefault() {
      this.originalEvent.preventDefault();
    }
  }, {
    key: "stopPropagation",
    value: function stopPropagation() {
      this.originalEvent.stopPropagation();
    }
  }, {
    key: "stopImmediatePropagation",
    value: function stopImmediatePropagation() {
      this.originalEvent.stopImmediatePropagation();
    }
  }]);

  return FakeEvent;
}();

_$events_52.FakeEvent = FakeEvent;
var events = {
  add: add,
  remove: __remove_52,
  addDelegate: addDelegate,
  removeDelegate: removeDelegate,
  delegateListener: delegateListener,
  delegateUseCapture: delegateUseCapture,
  delegatedEvents: delegatedEvents,
  documents: documents,
  supportsOptions: false,
  supportsPassive: false,
  _elements: elements,
  _targets: targets,
  init: function init(window) {
    window.document.createElement('div').addEventListener('test', null, {
      get capture() {
        return events.supportsOptions = true;
      },

      get passive() {
        return events.supportsPassive = true;
      }

    });
  }
};
var ___default_52 = events;
_$events_52["default"] = ___default_52;

var _$extend_53 = {};
"use strict";

Object.defineProperty(_$extend_53, "__esModule", {
  value: true
});
_$extend_53["default"] = extend;

function extend(dest, source) {
  for (var prop in source) {
    dest[prop] = source[prop];
  }

  return dest;
}

var _$rect_63 = {};
"use strict";

Object.defineProperty(_$rect_63, "__esModule", {
  value: true
});
_$rect_63.getStringOptionResult = getStringOptionResult;
_$rect_63.resolveRectLike = resolveRectLike;
_$rect_63.rectToXY = rectToXY;
_$rect_63.xywhToTlbr = xywhToTlbr;
_$rect_63.tlbrToXywh = tlbrToXywh;
_$rect_63["default"] = void 0;

/* removed: var _$domUtils_51 = require("./domUtils"); */;

var _extend = ___interopRequireDefault_63(_$extend_53);

var __is_63 = ___interopRequireWildcard_63(_$is_57);

function ___interopRequireWildcard_63(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_63(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

function getStringOptionResult(value, interactable, element) {
  if (value === 'parent') {
    return (0, _$domUtils_51.parentNode)(element);
  }

  if (value === 'self') {
    return interactable.getRect(element);
  }

  return (0, _$domUtils_51.closest)(element, value);
}

function resolveRectLike(value, interactable, element, functionArgs) {
  if (__is_63.string(value)) {
    value = getStringOptionResult(value, interactable, element);
  } else if (__is_63.func(value)) {
    value = value.apply(void 0, _toConsumableArray(functionArgs));
  }

  if (__is_63.element(value)) {
    value = (0, _$domUtils_51.getElementRect)(value);
  }

  return value;
}

function rectToXY(rect) {
  return rect && {
    x: 'x' in rect ? rect.x : rect.left,
    y: 'y' in rect ? rect.y : rect.top
  };
}

function xywhToTlbr(rect) {
  if (rect && !('left' in rect && 'top' in rect)) {
    rect = (0, _extend["default"])({}, rect);
    rect.left = rect.x || 0;
    rect.top = rect.y || 0;
    rect.right = rect.right || rect.left + rect.width;
    rect.bottom = rect.bottom || rect.top + rect.height;
  }

  return rect;
}

function tlbrToXywh(rect) {
  if (rect && !('x' in rect && 'y' in rect)) {
    rect = (0, _extend["default"])({}, rect);
    rect.x = rect.left || 0;
    rect.y = rect.top || 0;
    rect.width = rect.width || rect.right - rect.x;
    rect.height = rect.height || rect.bottom - rect.y;
  }

  return rect;
}

var ___default_63 = {
  getStringOptionResult: getStringOptionResult,
  resolveRectLike: resolveRectLike,
  rectToXY: rectToXY,
  xywhToTlbr: xywhToTlbr,
  tlbrToXywh: tlbrToXywh
};
_$rect_63["default"] = ___default_63;

var _$getOriginXY_54 = {};
"use strict";

Object.defineProperty(_$getOriginXY_54, "__esModule", {
  value: true
});
_$getOriginXY_54["default"] = ___default_54;

/* removed: var _$rect_63 = require("./rect"); */;

function ___default_54(target, element, action) {
  var actionOptions = target.options[action];
  var actionOrigin = actionOptions && actionOptions.origin;
  var origin = actionOrigin || target.options.origin;
  var originRect = (0, _$rect_63.resolveRectLike)(origin, target, element, [target && element]);
  return (0, _$rect_63.rectToXY)(originRect) || {
    x: 0,
    y: 0
  };
}

var _$normalizeListeners_59 = {};
"use strict";

Object.defineProperty(_$normalizeListeners_59, "__esModule", {
  value: true
});
_$normalizeListeners_59["default"] = normalize;

var ___extend_59 = ___interopRequireDefault_59(_$extend_53);

var __is_59 = ___interopRequireWildcard_59(_$is_57);

function ___interopRequireWildcard_59(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_59(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function normalize(type, listeners, result) {
  result = result || {};

  if (__is_59.string(type) && type.search(' ') !== -1) {
    type = split(type);
  }

  if (__is_59.array(type)) {
    return type.reduce(function (acc, t) {
      return (0, ___extend_59["default"])(acc, normalize(t, listeners, result));
    }, result);
  } // ({ type: fn }) -> ('', { type: fn })


  if (__is_59.object(type)) {
    listeners = type;
    type = '';
  }

  if (__is_59.func(listeners)) {
    result[type] = result[type] || [];
    result[type].push(listeners);
  } else if (__is_59.array(listeners)) {
    for (var _i = 0; _i < listeners.length; _i++) {
      var _ref;

      _ref = listeners[_i];
      var l = _ref;
      normalize(type, l, result);
    }
  } else if (__is_59.object(listeners)) {
    for (var prefix in listeners) {
      var combinedTypes = split(prefix).map(function (p) {
        return "".concat(type).concat(p);
      });
      normalize(combinedTypes, listeners[prefix], result);
    }
  }

  return result;
}

function split(type) {
  return type.trim().split(/ +/);
}

var _$raf_62 = {};
"use strict";

Object.defineProperty(_$raf_62, "__esModule", {
  value: true
});
_$raf_62["default"] = void 0;
var lastTime = 0;

var _request;

var _cancel;

function __init_62(window) {
  _request = window.requestAnimationFrame;
  _cancel = window.cancelAnimationFrame;

  if (!_request) {
    var vendors = ['ms', 'moz', 'webkit', 'o'];

    for (var _i = 0; _i < vendors.length; _i++) {
      var vendor = vendors[_i];
      _request = window["".concat(vendor, "RequestAnimationFrame")];
      _cancel = window["".concat(vendor, "CancelAnimationFrame")] || window["".concat(vendor, "CancelRequestAnimationFrame")];
    }
  }

  if (!_request) {
    _request = function request(callback) {
      var currTime = Date.now();
      var timeToCall = Math.max(0, 16 - (currTime - lastTime)); // eslint-disable-next-line standard/no-callback-literal

      var token = setTimeout(function () {
        callback(currTime + timeToCall);
      }, timeToCall);
      lastTime = currTime + timeToCall;
      return token;
    };

    _cancel = function cancel(token) {
      return clearTimeout(token);
    };
  }
}

var ___default_62 = {
  request: function request(callback) {
    return _request(callback);
  },
  cancel: function cancel(token) {
    return _cancel(token);
  },
  init: __init_62
};
_$raf_62["default"] = ___default_62;

var _$Signals_46 = {};
"use strict";

Object.defineProperty(_$Signals_46, "__esModule", {
  value: true
});
_$Signals_46["default"] = void 0;

function ___classCallCheck_46(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_46(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_46(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_46(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_46(Constructor, staticProps); return Constructor; }

var Signals =
/*#__PURE__*/
function () {
  function Signals() {
    ___classCallCheck_46(this, Signals);

    this.listeners = {};
  }

  ___createClass_46(Signals, [{
    key: "on",
    value: function on(name, listener) {
      if (!this.listeners[name]) {
        this.listeners[name] = [listener];
        return;
      }

      this.listeners[name].push(listener);
    }
  }, {
    key: "off",
    value: function off(name, listener) {
      if (!this.listeners[name]) {
        return;
      }

      var index = this.listeners[name].indexOf(listener);

      if (index !== -1) {
        this.listeners[name].splice(index, 1);
      }
    }
  }, {
    key: "fire",
    value: function fire(name, arg) {
      var targetListeners = this.listeners[name];

      if (!targetListeners) {
        return;
      }

      for (var _i = 0; _i < targetListeners.length; _i++) {
        var _ref;

        _ref = targetListeners[_i];
        var listener = _ref;

        if (listener(arg, name) === false) {
          return false;
        }
      }
    }
  }]);

  return Signals;
}();

var ___default_46 = Signals;
_$Signals_46["default"] = ___default_46;

var _$utils_56 = {};
"use strict";

Object.defineProperty(_$utils_56, "__esModule", {
  value: true
});
_$utils_56.warnOnce = warnOnce;
_$utils_56._getQBezierValue = _getQBezierValue;
_$utils_56.getQuadraticCurvePoint = getQuadraticCurvePoint;
_$utils_56.easeOutQuad = easeOutQuad;
_$utils_56.copyAction = copyAction;
Object.defineProperty(_$utils_56, "win", {
  enumerable: true,
  get: function get() {
    return ___window_56["default"];
  }
});
Object.defineProperty(_$utils_56, "browser", {
  enumerable: true,
  get: function get() {
    return ___browser_56["default"];
  }
});
Object.defineProperty(_$utils_56, "clone", {
  enumerable: true,
  get: function get() {
    return _clone["default"];
  }
});
Object.defineProperty(_$utils_56, "events", {
  enumerable: true,
  get: function get() {
    return _events["default"];
  }
});
Object.defineProperty(_$utils_56, "extend", {
  enumerable: true,
  get: function get() {
    return ___extend_56["default"];
  }
});
Object.defineProperty(_$utils_56, "getOriginXY", {
  enumerable: true,
  get: function get() {
    return _getOriginXY["default"];
  }
});
Object.defineProperty(_$utils_56, "hypot", {
  enumerable: true,
  get: function get() {
    return ___hypot_56["default"];
  }
});
Object.defineProperty(_$utils_56, "normalizeListeners", {
  enumerable: true,
  get: function get() {
    return _normalizeListeners["default"];
  }
});
Object.defineProperty(_$utils_56, "pointer", {
  enumerable: true,
  get: function get() {
    return ___pointerUtils_56["default"];
  }
});
Object.defineProperty(_$utils_56, "raf", {
  enumerable: true,
  get: function get() {
    return _raf["default"];
  }
});
Object.defineProperty(_$utils_56, "rect", {
  enumerable: true,
  get: function get() {
    return ___rect_56["default"];
  }
});
Object.defineProperty(_$utils_56, "Signals", {
  enumerable: true,
  get: function get() {
    return _Signals["default"];
  }
});
_$utils_56.is = _$utils_56.dom = _$utils_56.arr = void 0;

var __arr_56 = ___interopRequireWildcard_56(_$arr_47);

_$utils_56.arr = __arr_56;

var dom = ___interopRequireWildcard_56(_$domUtils_51);

_$utils_56.dom = dom;

var __is_56 = ___interopRequireWildcard_56(_$is_57);

_$utils_56.is = __is_56;

var ___window_56 = ___interopRequireDefault_56(_$window_66);

var ___browser_56 = ___interopRequireDefault_56(_$browser_48);

var _clone = ___interopRequireDefault_56(_$clone_49);

var _events = ___interopRequireDefault_56(_$events_52);

var ___extend_56 = ___interopRequireDefault_56(_$extend_53);

var _getOriginXY = ___interopRequireDefault_56(_$getOriginXY_54);

var ___hypot_56 = ___interopRequireDefault_56(_$hypot_55);

var _normalizeListeners = ___interopRequireDefault_56(_$normalizeListeners_59);

var ___pointerUtils_56 = ___interopRequireDefault_56(_$pointerUtils_61);

var _raf = ___interopRequireDefault_56(_$raf_62);

var ___rect_56 = ___interopRequireDefault_56(_$rect_63);

var _Signals = ___interopRequireDefault_56(_$Signals_46);

function ___interopRequireDefault_56(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_56(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function warnOnce(method, message) {
  var warned = false; // eslint-disable-next-line no-shadow

  return function () {
    if (!warned) {
      ___window_56["default"].window.console.warn(message);

      warned = true;
    }

    return method.apply(this, arguments);
  };
} // http://stackoverflow.com/a/5634528/2280888


function _getQBezierValue(t, p1, p2, p3) {
  var iT = 1 - t;
  return iT * iT * p1 + 2 * iT * t * p2 + t * t * p3;
}

function getQuadraticCurvePoint(startX, startY, cpX, cpY, endX, endY, position) {
  return {
    x: _getQBezierValue(position, startX, cpX, endX),
    y: _getQBezierValue(position, startY, cpY, endY)
  };
} // http://gizma.com/easing/


function easeOutQuad(t, b, c, d) {
  t /= d;
  return -c * t * (t - 2) + b;
}

function copyAction(dest, src) {
  dest.name = src.name;
  dest.axis = src.axis;
  dest.edges = src.edges;
  return dest;
}

var _$defaultOptions_20 = {};
"use strict";

Object.defineProperty(_$defaultOptions_20, "__esModule", {
  value: true
});
_$defaultOptions_20["default"] = _$defaultOptions_20.defaults = void 0;
// tslint:disable no-empty-interface
var defaults = {
  base: {
    preventDefault: 'auto',
    deltaSource: 'page'
  },
  perAction: {
    enabled: false,
    origin: {
      x: 0,
      y: 0
    }
  },
  actions: {}
};
_$defaultOptions_20.defaults = defaults;
var ___default_20 = defaults;
_$defaultOptions_20["default"] = ___default_20;

var _$Eventable_14 = {};
"use strict";

Object.defineProperty(_$Eventable_14, "__esModule", {
  value: true
});
_$Eventable_14["default"] = void 0;

var __arr_14 = ___interopRequireWildcard_14(_$arr_47);

var ___extend_14 = ___interopRequireDefault_14(_$extend_53);

var ___normalizeListeners_14 = ___interopRequireDefault_14(_$normalizeListeners_59);

function ___interopRequireDefault_14(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_14(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___classCallCheck_14(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_14(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_14(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_14(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_14(Constructor, staticProps); return Constructor; }

function fireUntilImmediateStopped(event, listeners) {
  for (var _i = 0; _i < listeners.length; _i++) {
    var _ref;

    _ref = listeners[_i];
    var listener = _ref;

    if (event.immediatePropagationStopped) {
      break;
    }

    listener(event);
  }
}

var Eventable =
/*#__PURE__*/
function () {
  function Eventable(options) {
    ___classCallCheck_14(this, Eventable);

    this.types = {};
    this.propagationStopped = false;
    this.immediatePropagationStopped = false;
    this.options = (0, ___extend_14["default"])({}, options || {});
  }

  ___createClass_14(Eventable, [{
    key: "fire",
    value: function fire(event) {
      var listeners;
      var global = this.global; // Interactable#on() listeners
      // tslint:disable no-conditional-assignment

      if (listeners = this.types[event.type]) {
        fireUntilImmediateStopped(event, listeners);
      } // interact.on() listeners


      if (!event.propagationStopped && global && (listeners = global[event.type])) {
        fireUntilImmediateStopped(event, listeners);
      }
    }
  }, {
    key: "on",
    value: function on(type, listener) {
      var listeners = (0, ___normalizeListeners_14["default"])(type, listener);

      for (type in listeners) {
        this.types[type] = __arr_14.merge(this.types[type] || [], listeners[type]);
      }
    }
  }, {
    key: "off",
    value: function off(type, listener) {
      var listeners = (0, ___normalizeListeners_14["default"])(type, listener);

      for (type in listeners) {
        var eventList = this.types[type];

        if (!eventList || !eventList.length) {
          continue;
        }

        for (var _i2 = 0; _i2 < listeners[type].length; _i2++) {
          var _ref2;

          _ref2 = listeners[type][_i2];
          var subListener = _ref2;
          var index = eventList.indexOf(subListener);

          if (index !== -1) {
            eventList.splice(index, 1);
          }
        }
      }
    }
  }]);

  return Eventable;
}();

var ___default_14 = Eventable;
_$Eventable_14["default"] = ___default_14;

var _$Interactable_16 = {};
"use strict";

Object.defineProperty(_$Interactable_16, "__esModule", {
  value: true
});
_$Interactable_16["default"] = _$Interactable_16.Interactable = void 0;

var __arr_16 = ___interopRequireWildcard_16(_$arr_47);

var ___browser_16 = ___interopRequireDefault_16(_$browser_48);

var ___clone_16 = ___interopRequireDefault_16(_$clone_49);

/* removed: var _$domUtils_51 = require("@interactjs/utils/domUtils"); */;

var ___events_16 = ___interopRequireDefault_16(_$events_52);

var ___extend_16 = ___interopRequireDefault_16(_$extend_53);

var __is_16 = ___interopRequireWildcard_16(_$is_57);

var ___normalizeListeners_16 = ___interopRequireDefault_16(_$normalizeListeners_59);

/* removed: var _$window_66 = require("@interactjs/utils/window"); */;

var _Eventable = ___interopRequireDefault_16(_$Eventable_14);

function ___interopRequireDefault_16(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_16(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___classCallCheck_16(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_16(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_16(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_16(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_16(Constructor, staticProps); return Constructor; }

/** */
var Interactable =
/*#__PURE__*/
function () {
  /** */
  function Interactable(target, options, defaultContext) {
    ___classCallCheck_16(this, Interactable);

    this.events = new _Eventable["default"]();
    this._actions = options.actions;
    this.target = target;
    this._context = options.context || defaultContext;
    this._win = (0, _$window_66.getWindow)((0, _$domUtils_51.trySelector)(target) ? this._context : target);
    this._doc = this._win.document;
    this.set(options);
  }

  ___createClass_16(Interactable, [{
    key: "setOnEvents",
    value: function setOnEvents(actionName, phases) {
      if (__is_16.func(phases.onstart)) {
        this.on("".concat(actionName, "start"), phases.onstart);
      }

      if (__is_16.func(phases.onmove)) {
        this.on("".concat(actionName, "move"), phases.onmove);
      }

      if (__is_16.func(phases.onend)) {
        this.on("".concat(actionName, "end"), phases.onend);
      }

      if (__is_16.func(phases.oninertiastart)) {
        this.on("".concat(actionName, "inertiastart"), phases.oninertiastart);
      }

      return this;
    }
  }, {
    key: "updatePerActionListeners",
    value: function updatePerActionListeners(actionName, prev, cur) {
      if (__is_16.array(prev) || __is_16.object(prev)) {
        this.off(actionName, prev);
      }

      if (__is_16.array(cur) || __is_16.object(cur)) {
        this.on(actionName, cur);
      }
    }
  }, {
    key: "setPerAction",
    value: function setPerAction(actionName, options) {
      var defaults = this._defaults; // for all the default per-action options

      for (var optionName in options) {
        var actionOptions = this.options[actionName];
        var optionValue = options[optionName];
        var isArray = __is_16.array(optionValue); // remove old event listeners and add new ones

        if (optionName === 'listeners') {
          this.updatePerActionListeners(actionName, actionOptions.listeners, optionValue);
        } // if the option value is an array


        if (isArray) {
          actionOptions[optionName] = __arr_16.from(optionValue);
        } // if the option value is an object
        else if (!isArray && __is_16.plainObject(optionValue)) {
            // copy the object
            actionOptions[optionName] = (0, ___extend_16["default"])(actionOptions[optionName] || {}, (0, ___clone_16["default"])(optionValue)); // set anabled field to true if it exists in the defaults

            if (__is_16.object(defaults.perAction[optionName]) && 'enabled' in defaults.perAction[optionName]) {
              actionOptions[optionName].enabled = optionValue.enabled !== false;
            }
          } // if the option value is a boolean and the default is an object
          else if (__is_16.bool(optionValue) && __is_16.object(defaults.perAction[optionName])) {
              actionOptions[optionName].enabled = optionValue;
            } // if it's anything else, do a plain assignment
            else {
                actionOptions[optionName] = optionValue;
              }
      }
    }
    /**
     * The default function to get an Interactables bounding rect. Can be
     * overridden using {@link Interactable.rectChecker}.
     *
     * @param {Element} [element] The element to measure.
     * @return {object} The object's bounding rectangle.
     */

  }, {
    key: "getRect",
    value: function getRect(element) {
      element = element || (__is_16.element(this.target) ? this.target : null);

      if (__is_16.string(this.target)) {
        element = element || this._context.querySelector(this.target);
      }

      return (0, _$domUtils_51.getElementRect)(element);
    }
    /**
     * Returns or sets the function used to calculate the interactable's
     * element's rectangle
     *
     * @param {function} [checker] A function which returns this Interactable's
     * bounding rectangle. See {@link Interactable.getRect}
     * @return {function | object} The checker function or this Interactable
     */

  }, {
    key: "rectChecker",
    value: function rectChecker(checker) {
      if (__is_16.func(checker)) {
        this.getRect = checker;
        return this;
      }

      if (checker === null) {
        delete this.getRect;
        return this;
      }

      return this.getRect;
    }
  }, {
    key: "_backCompatOption",
    value: function _backCompatOption(optionName, newValue) {
      if ((0, _$domUtils_51.trySelector)(newValue) || __is_16.object(newValue)) {
        this.options[optionName] = newValue;

        for (var _i = 0; _i < this._actions.names.length; _i++) {
          var _ref;

          _ref = this._actions.names[_i];
          var action = _ref;
          this.options[action][optionName] = newValue;
        }

        return this;
      }

      return this.options[optionName];
    }
    /**
     * Gets or sets the origin of the Interactable's element.  The x and y
     * of the origin will be subtracted from action event coordinates.
     *
     * @param {Element | object | string} [origin] An HTML or SVG Element whose
     * rect will be used, an object eg. { x: 0, y: 0 } or string 'parent', 'self'
     * or any CSS selector
     *
     * @return {object} The current origin or this Interactable
     */

  }, {
    key: "origin",
    value: function origin(newValue) {
      return this._backCompatOption('origin', newValue);
    }
    /**
     * Returns or sets the mouse coordinate types used to calculate the
     * movement of the pointer.
     *
     * @param {string} [newValue] Use 'client' if you will be scrolling while
     * interacting; Use 'page' if you want autoScroll to work
     * @return {string | object} The current deltaSource or this Interactable
     */

  }, {
    key: "deltaSource",
    value: function deltaSource(newValue) {
      if (newValue === 'page' || newValue === 'client') {
        this.options.deltaSource = newValue;
        return this;
      }

      return this.options.deltaSource;
    }
    /**
     * Gets the selector context Node of the Interactable. The default is
     * `window.document`.
     *
     * @return {Node} The context Node of this Interactable
     */

  }, {
    key: "context",
    value: function context() {
      return this._context;
    }
  }, {
    key: "inContext",
    value: function inContext(element) {
      return this._context === element.ownerDocument || (0, _$domUtils_51.nodeContains)(this._context, element);
    }
  }, {
    key: "testIgnoreAllow",
    value: function testIgnoreAllow(options, targetNode, eventTarget) {
      return !this.testIgnore(options.ignoreFrom, targetNode, eventTarget) && this.testAllow(options.allowFrom, targetNode, eventTarget);
    }
  }, {
    key: "testAllow",
    value: function testAllow(allowFrom, targetNode, element) {
      if (!allowFrom) {
        return true;
      }

      if (!__is_16.element(element)) {
        return false;
      }

      if (__is_16.string(allowFrom)) {
        return (0, _$domUtils_51.matchesUpTo)(element, allowFrom, targetNode);
      } else if (__is_16.element(allowFrom)) {
        return (0, _$domUtils_51.nodeContains)(allowFrom, element);
      }

      return false;
    }
  }, {
    key: "testIgnore",
    value: function testIgnore(ignoreFrom, targetNode, element) {
      if (!ignoreFrom || !__is_16.element(element)) {
        return false;
      }

      if (__is_16.string(ignoreFrom)) {
        return (0, _$domUtils_51.matchesUpTo)(element, ignoreFrom, targetNode);
      } else if (__is_16.element(ignoreFrom)) {
        return (0, _$domUtils_51.nodeContains)(ignoreFrom, element);
      }

      return false;
    }
    /**
     * Calls listeners for the given InteractEvent type bound globally
     * and directly to this Interactable
     *
     * @param {InteractEvent} iEvent The InteractEvent object to be fired on this
     * Interactable
     * @return {Interactable} this Interactable
     */

  }, {
    key: "fire",
    value: function fire(iEvent) {
      this.events.fire(iEvent);
      return this;
    }
  }, {
    key: "_onOff",
    value: function _onOff(method, typeArg, listenerArg, options) {
      if (__is_16.object(typeArg) && !__is_16.array(typeArg)) {
        options = listenerArg;
        listenerArg = null;
      }

      var addRemove = method === 'on' ? 'add' : 'remove';
      var listeners = (0, ___normalizeListeners_16["default"])(typeArg, listenerArg);

      for (var type in listeners) {
        if (type === 'wheel') {
          type = ___browser_16["default"].wheelEvent;
        }

        for (var _i2 = 0; _i2 < listeners[type].length; _i2++) {
          var _ref2;

          _ref2 = listeners[type][_i2];
          var listener = _ref2;

          // if it is an action event type
          if (__arr_16.contains(this._actions.eventTypes, type)) {
            this.events[method](type, listener);
          } // delegated event
          else if (__is_16.string(this.target)) {
              ___events_16["default"]["".concat(addRemove, "Delegate")](this.target, this._context, type, listener, options);
            } // remove listener from this Interatable's element
            else {
                ___events_16["default"][addRemove](this.target, type, listener, options);
              }
        }
      }

      return this;
    }
    /**
     * Binds a listener for an InteractEvent, pointerEvent or DOM event.
     *
     * @param {string | array | object} types The types of events to listen
     * for
     * @param {function | array | object} [listener] The event listener function(s)
     * @param {object | boolean} [options] options object or useCapture flag for
     * addEventListener
     * @return {Interactable} This Interactable
     */

  }, {
    key: "on",
    value: function on(types, listener, options) {
      return this._onOff('on', types, listener, options);
    }
    /**
     * Removes an InteractEvent, pointerEvent or DOM event listener.
     *
     * @param {string | array | object} types The types of events that were
     * listened for
     * @param {function | array | object} [listener] The event listener function(s)
     * @param {object | boolean} [options] options object or useCapture flag for
     * removeEventListener
     * @return {Interactable} This Interactable
     */

  }, {
    key: "off",
    value: function off(types, listener, options) {
      return this._onOff('off', types, listener, options);
    }
    /**
     * Reset the options of this Interactable
     *
     * @param {object} options The new settings to apply
     * @return {object} This Interactable
     */

  }, {
    key: "set",
    value: function set(options) {
      var defaults = this._defaults;

      if (!__is_16.object(options)) {
        options = {};
      }

      this.options = (0, ___clone_16["default"])(defaults.base);

      for (var actionName in this._actions.methodDict) {
        var methodName = this._actions.methodDict[actionName];
        this.options[actionName] = {};
        this.setPerAction(actionName, (0, ___extend_16["default"])((0, ___extend_16["default"])({}, defaults.perAction), defaults.actions[actionName]));
        this[methodName](options[actionName]);
      }

      for (var setting in options) {
        if (__is_16.func(this[setting])) {
          this[setting](options[setting]);
        }
      }

      return this;
    }
    /**
     * Remove this interactable from the list of interactables and remove it's
     * action capabilities and event listeners
     *
     * @return {interact}
     */

  }, {
    key: "unset",
    value: function unset() {
      ___events_16["default"].remove(this.target, 'all');

      if (__is_16.string(this.target)) {
        // remove delegated events
        for (var type in ___events_16["default"].delegatedEvents) {
          var delegated = ___events_16["default"].delegatedEvents[type];

          if (delegated.selectors[0] === this.target && delegated.contexts[0] === this._context) {
            delegated.selectors.splice(0, 1);
            delegated.contexts.splice(0, 1);
            delegated.listeners.splice(0, 1); // remove the arrays if they are empty

            if (!delegated.selectors.length) {
              delegated[type] = null;
            }
          }

          ___events_16["default"].remove(this._context, type, ___events_16["default"].delegateListener);

          ___events_16["default"].remove(this._context, type, ___events_16["default"].delegateUseCapture, true);
        }
      } else {
        ___events_16["default"].remove(this.target, 'all');
      }
    }
  }, {
    key: "_defaults",
    get: function get() {
      return {
        base: {},
        perAction: {},
        actions: {}
      };
    }
  }]);

  return Interactable;
}();

_$Interactable_16.Interactable = Interactable;
var ___default_16 = Interactable;
_$Interactable_16["default"] = ___default_16;

var _$InteractableSet_17 = {};
"use strict";

Object.defineProperty(_$InteractableSet_17, "__esModule", {
  value: true
});
_$InteractableSet_17["default"] = void 0;

var __arr_17 = ___interopRequireWildcard_17(_$arr_47);

var __domUtils_17 = ___interopRequireWildcard_17(_$domUtils_51);

var ___extend_17 = ___interopRequireDefault_17(_$extend_53);

var __is_17 = ___interopRequireWildcard_17(_$is_57);

var ___Signals_17 = ___interopRequireDefault_17(_$Signals_46);

function ___interopRequireDefault_17(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_17(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___classCallCheck_17(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_17(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_17(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_17(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_17(Constructor, staticProps); return Constructor; }

var InteractableSet =
/*#__PURE__*/
function () {
  function InteractableSet(scope) {
    var _this = this;

    ___classCallCheck_17(this, InteractableSet);

    this.scope = scope;
    this.signals = new ___Signals_17["default"](); // all set interactables

    this.list = [];
    this.selectorMap = {};
    this.signals.on('unset', function (_ref) {
      var interactable = _ref.interactable;
      var target = interactable.target,
          context = interactable._context;
      var targetMappings = __is_17.string(target) ? _this.selectorMap[target] : target[_this.scope.id];
      var targetIndex = targetMappings.findIndex(function (m) {
        return m.context === context;
      });

      if (targetMappings[targetIndex]) {
        // Destroying mappingInfo's context and interactable
        targetMappings[targetIndex].context = null;
        targetMappings[targetIndex].interactable = null;
      }

      targetMappings.splice(targetIndex, 1);
    });
  }

  ___createClass_17(InteractableSet, [{
    key: "new",
    value: function _new(target, options) {
      options = (0, ___extend_17["default"])(options || {}, {
        actions: this.scope.actions
      });
      var interactable = new this.scope.Interactable(target, options, this.scope.document);
      var mappingInfo = {
        context: interactable._context,
        interactable: interactable
      };
      this.scope.addDocument(interactable._doc);
      this.list.push(interactable);

      if (__is_17.string(target)) {
        if (!this.selectorMap[target]) {
          this.selectorMap[target] = [];
        }

        this.selectorMap[target].push(mappingInfo);
      } else {
        if (!interactable.target[this.scope.id]) {
          Object.defineProperty(target, this.scope.id, {
            value: [],
            configurable: true
          });
        }

        target[this.scope.id].push(mappingInfo);
      }

      this.signals.fire('new', {
        target: target,
        options: options,
        interactable: interactable,
        win: this.scope._win
      });
      return interactable;
    }
  }, {
    key: "get",
    value: function get(target, options) {
      var context = options && options.context || this.scope.document;
      var isSelector = __is_17.string(target);
      var targetMappings = isSelector ? this.selectorMap[target] : target[this.scope.id];

      if (!targetMappings) {
        return null;
      }

      var found = __arr_17.find(targetMappings, function (m) {
        return m.context === context && (isSelector || m.interactable.inContext(target));
      });
      return found && found.interactable;
    }
  }, {
    key: "forEachMatch",
    value: function forEachMatch(node, callback) {
      for (var _i = 0; _i < this.list.length; _i++) {
        var _ref2;

        _ref2 = this.list[_i];
        var interactable = _ref2;
        var ret = void 0;

        if ((__is_17.string(interactable.target) // target is a selector and the element matches
        ? __is_17.element(node) && __domUtils_17.matchesSelector(node, interactable.target) : // target is the element
        node === interactable.target) && // the element is in context
        interactable.inContext(node)) {
          ret = callback(interactable);
        }

        if (ret !== undefined) {
          return ret;
        }
      }
    }
  }]);

  return InteractableSet;
}();

_$InteractableSet_17["default"] = InteractableSet;

var _$BaseEvent_13 = {};
"use strict";

Object.defineProperty(_$BaseEvent_13, "__esModule", {
  value: true
});
_$BaseEvent_13["default"] = _$BaseEvent_13.BaseEvent = _$BaseEvent_13.EventPhase = void 0;

function ___classCallCheck_13(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_13(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_13(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_13(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_13(Constructor, staticProps); return Constructor; }

var EventPhase;
_$BaseEvent_13.EventPhase = EventPhase;

(function (EventPhase) {
  EventPhase["Start"] = "start";
  EventPhase["Move"] = "move";
  EventPhase["End"] = "end";
  EventPhase["_NONE"] = "";
})(EventPhase || (_$BaseEvent_13.EventPhase = EventPhase = {}));

var BaseEvent =
/*#__PURE__*/
function () {
  function BaseEvent(interaction) {
    ___classCallCheck_13(this, BaseEvent);

    this.immediatePropagationStopped = false;
    this.propagationStopped = false;
    this._interaction = interaction;
  }

  ___createClass_13(BaseEvent, [{
    key: "preventDefault",
    value: function preventDefault() {}
    /**
     * Don't call any other listeners (even on the current target)
     */

  }, {
    key: "stopPropagation",
    value: function stopPropagation() {
      this.propagationStopped = true;
    }
    /**
     * Don't call listeners on the remaining targets
     */

  }, {
    key: "stopImmediatePropagation",
    value: function stopImmediatePropagation() {
      this.immediatePropagationStopped = this.propagationStopped = true;
    }
  }, {
    key: "interaction",
    get: function get() {
      return this._interaction._proxy;
    }
  }]);

  return BaseEvent;
}();

_$BaseEvent_13.BaseEvent = BaseEvent;
var ___default_13 = BaseEvent;
_$BaseEvent_13["default"] = ___default_13;

var _$InteractEvent_15 = {};
"use strict";

Object.defineProperty(_$InteractEvent_15, "__esModule", {
  value: true
});
_$InteractEvent_15["default"] = _$InteractEvent_15.InteractEvent = _$InteractEvent_15.EventPhase = void 0;

var ___extend_15 = ___interopRequireDefault_15(_$extend_53);

var ___getOriginXY_15 = ___interopRequireDefault_15(_$getOriginXY_54);

var ___hypot_15 = ___interopRequireDefault_15(_$hypot_55);

var _BaseEvent2 = ___interopRequireDefault_15(_$BaseEvent_13);

var _defaultOptions = ___interopRequireDefault_15(_$defaultOptions_20);

function ___interopRequireDefault_15(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___typeof_15(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { ___typeof_15 = function _typeof(obj) { return typeof obj; }; } else { ___typeof_15 = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return ___typeof_15(obj); }

function ___classCallCheck_15(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_15(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_15(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_15(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_15(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (___typeof_15(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var __EventPhase_15;
_$InteractEvent_15.EventPhase = __EventPhase_15;

(function (EventPhase) {
  EventPhase["Start"] = "start";
  EventPhase["Move"] = "move";
  EventPhase["End"] = "end";
  EventPhase["_NONE"] = "";
})(__EventPhase_15 || (_$InteractEvent_15.EventPhase = __EventPhase_15 = {}));

var InteractEvent =
/*#__PURE__*/
function (_BaseEvent) {
  _inherits(InteractEvent, _BaseEvent);

  /** */
  function InteractEvent(interaction, event, actionName, phase, element, related, preEnd, type) {
    var _this;

    ___classCallCheck_15(this, InteractEvent);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(InteractEvent).call(this, interaction));
    element = element || interaction.element;
    var target = interaction.interactable;
    var deltaSource = (target && target.options || _defaultOptions["default"]).deltaSource;
    var origin = (0, ___getOriginXY_15["default"])(target, element, actionName);
    var starting = phase === 'start';
    var ending = phase === 'end';
    var prevEvent = starting ? _assertThisInitialized(_this) : interaction.prevEvent;
    var coords = starting ? interaction.coords.start : ending ? {
      page: prevEvent.page,
      client: prevEvent.client,
      timeStamp: interaction.coords.cur.timeStamp
    } : interaction.coords.cur;
    _this.page = (0, ___extend_15["default"])({}, coords.page);
    _this.client = (0, ___extend_15["default"])({}, coords.client);
    _this.rect = (0, ___extend_15["default"])({}, interaction.rect);
    _this.timeStamp = coords.timeStamp;

    if (!ending) {
      _this.page.x -= origin.x;
      _this.page.y -= origin.y;
      _this.client.x -= origin.x;
      _this.client.y -= origin.y;
    }

    _this.ctrlKey = event.ctrlKey;
    _this.altKey = event.altKey;
    _this.shiftKey = event.shiftKey;
    _this.metaKey = event.metaKey;
    _this.button = event.button;
    _this.buttons = event.buttons;
    _this.target = element;
    _this.currentTarget = element;
    _this.relatedTarget = related || null;
    _this.preEnd = preEnd;
    _this.type = type || actionName + (phase || '');
    _this.interactable = target;
    _this.t0 = starting ? interaction.pointers[interaction.pointers.length - 1].downTime : prevEvent.t0;
    _this.x0 = interaction.coords.start.page.x - origin.x;
    _this.y0 = interaction.coords.start.page.y - origin.y;
    _this.clientX0 = interaction.coords.start.client.x - origin.x;
    _this.clientY0 = interaction.coords.start.client.y - origin.y;

    if (starting || ending) {
      _this.delta = {
        x: 0,
        y: 0
      };
    } else {
      _this.delta = {
        x: _this[deltaSource].x - prevEvent[deltaSource].x,
        y: _this[deltaSource].y - prevEvent[deltaSource].y
      };
    }

    _this.dt = interaction.coords.delta.timeStamp;
    _this.duration = _this.timeStamp - _this.t0; // velocity and speed in pixels per second

    _this.velocity = (0, ___extend_15["default"])({}, interaction.coords.velocity[deltaSource]);
    _this.speed = (0, ___hypot_15["default"])(_this.velocity.x, _this.velocity.y);
    _this.swipe = ending || phase === 'inertiastart' ? _this.getSwipe() : null;
    return _this;
  }

  ___createClass_15(InteractEvent, [{
    key: "getSwipe",
    value: function getSwipe() {
      var interaction = this._interaction;

      if (interaction.prevEvent.speed < 600 || this.timeStamp - interaction.prevEvent.timeStamp > 150) {
        return null;
      }

      var angle = 180 * Math.atan2(interaction.prevEvent.velocityY, interaction.prevEvent.velocityX) / Math.PI;
      var overlap = 22.5;

      if (angle < 0) {
        angle += 360;
      }

      var left = 135 - overlap <= angle && angle < 225 + overlap;
      var up = 225 - overlap <= angle && angle < 315 + overlap;
      var right = !left && (315 - overlap <= angle || angle < 45 + overlap);
      var down = !up && 45 - overlap <= angle && angle < 135 + overlap;
      return {
        up: up,
        down: down,
        left: left,
        right: right,
        angle: angle,
        speed: interaction.prevEvent.speed,
        velocity: {
          x: interaction.prevEvent.velocityX,
          y: interaction.prevEvent.velocityY
        }
      };
    }
  }, {
    key: "preventDefault",
    value: function preventDefault() {}
    /**
     * Don't call listeners on the remaining targets
     */

  }, {
    key: "stopImmediatePropagation",
    value: function stopImmediatePropagation() {
      this.immediatePropagationStopped = this.propagationStopped = true;
    }
    /**
     * Don't call any other listeners (even on the current target)
     */

  }, {
    key: "stopPropagation",
    value: function stopPropagation() {
      this.propagationStopped = true;
    }
  }, {
    key: "pageX",
    get: function get() {
      return this.page.x;
    },
    set: function set(value) {
      this.page.x = value;
    }
  }, {
    key: "pageY",
    get: function get() {
      return this.page.y;
    },
    set: function set(value) {
      this.page.y = value;
    }
  }, {
    key: "clientX",
    get: function get() {
      return this.client.x;
    },
    set: function set(value) {
      this.client.x = value;
    }
  }, {
    key: "clientY",
    get: function get() {
      return this.client.y;
    },
    set: function set(value) {
      this.client.y = value;
    }
  }, {
    key: "dx",
    get: function get() {
      return this.delta.x;
    },
    set: function set(value) {
      this.delta.x = value;
    }
  }, {
    key: "dy",
    get: function get() {
      return this.delta.y;
    },
    set: function set(value) {
      this.delta.y = value;
    }
  }, {
    key: "velocityX",
    get: function get() {
      return this.velocity.x;
    },
    set: function set(value) {
      this.velocity.x = value;
    }
  }, {
    key: "velocityY",
    get: function get() {
      return this.velocity.y;
    },
    set: function set(value) {
      this.velocity.y = value;
    }
  }]);

  return InteractEvent;
}(_BaseEvent2["default"]);

_$InteractEvent_15.InteractEvent = InteractEvent;
var ___default_15 = InteractEvent;
_$InteractEvent_15["default"] = ___default_15;

var _$PointerInfo_19 = {};
"use strict";

Object.defineProperty(_$PointerInfo_19, "__esModule", {
  value: true
});
_$PointerInfo_19["default"] = _$PointerInfo_19.PointerInfo = void 0;

function ___classCallCheck_19(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* eslint-disable @typescript-eslint/no-parameter-properties */
var PointerInfo = function PointerInfo(id, pointer, event, downTime, downTarget) {
  ___classCallCheck_19(this, PointerInfo);

  this.id = id;
  this.pointer = pointer;
  this.event = event;
  this.downTime = downTime;
  this.downTarget = downTarget;
};

_$PointerInfo_19.PointerInfo = PointerInfo;
var ___default_19 = PointerInfo;
_$PointerInfo_19["default"] = ___default_19;

var _$interactionFinder_22 = {};
"use strict";

Object.defineProperty(_$interactionFinder_22, "__esModule", {
  value: true
});
_$interactionFinder_22["default"] = void 0;

var __dom_22 = ___interopRequireWildcard_22(_$domUtils_51);

function ___interopRequireWildcard_22(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

var finder = {
  methodOrder: ['simulationResume', 'mouseOrPen', 'hasPointer', 'idle'],
  search: function search(details) {
    for (var _i = 0; _i < finder.methodOrder.length; _i++) {
      var _ref;

      _ref = finder.methodOrder[_i];
      var method = _ref;
      var interaction = finder[method](details);

      if (interaction) {
        return interaction;
      }
    }
  },
  // try to resume simulation with a new pointer
  simulationResume: function simulationResume(_ref2) {
    var pointerType = _ref2.pointerType,
        eventType = _ref2.eventType,
        eventTarget = _ref2.eventTarget,
        scope = _ref2.scope;

    if (!/down|start/i.test(eventType)) {
      return null;
    }

    for (var _i2 = 0; _i2 < scope.interactions.list.length; _i2++) {
      var _ref3;

      _ref3 = scope.interactions.list[_i2];
      var interaction = _ref3;
      var element = eventTarget;

      if (interaction.simulation && interaction.simulation.allowResume && interaction.pointerType === pointerType) {
        while (element) {
          // if the element is the interaction element
          if (element === interaction.element) {
            return interaction;
          }

          element = __dom_22.parentNode(element);
        }
      }
    }

    return null;
  },
  // if it's a mouse or pen interaction
  mouseOrPen: function mouseOrPen(_ref4) {
    var pointerId = _ref4.pointerId,
        pointerType = _ref4.pointerType,
        eventType = _ref4.eventType,
        scope = _ref4.scope;

    if (pointerType !== 'mouse' && pointerType !== 'pen') {
      return null;
    }

    var firstNonActive;

    for (var _i3 = 0; _i3 < scope.interactions.list.length; _i3++) {
      var _ref5;

      _ref5 = scope.interactions.list[_i3];
      var interaction = _ref5;

      if (interaction.pointerType === pointerType) {
        // if it's a down event, skip interactions with running simulations
        if (interaction.simulation && !hasPointerId(interaction, pointerId)) {
          continue;
        } // if the interaction is active, return it immediately


        if (interaction.interacting()) {
          return interaction;
        } // otherwise save it and look for another active interaction
        else if (!firstNonActive) {
            firstNonActive = interaction;
          }
      }
    } // if no active mouse interaction was found use the first inactive mouse
    // interaction


    if (firstNonActive) {
      return firstNonActive;
    } // find any mouse or pen interaction.
    // ignore the interaction if the eventType is a *down, and a simulation
    // is active


    for (var _i4 = 0; _i4 < scope.interactions.list.length; _i4++) {
      var _ref6;

      _ref6 = scope.interactions.list[_i4];
      var _interaction = _ref6;

      if (_interaction.pointerType === pointerType && !(/down/i.test(eventType) && _interaction.simulation)) {
        return _interaction;
      }
    }

    return null;
  },
  // get interaction that has this pointer
  hasPointer: function hasPointer(_ref7) {
    var pointerId = _ref7.pointerId,
        scope = _ref7.scope;

    for (var _i5 = 0; _i5 < scope.interactions.list.length; _i5++) {
      var _ref8;

      _ref8 = scope.interactions.list[_i5];
      var interaction = _ref8;

      if (hasPointerId(interaction, pointerId)) {
        return interaction;
      }
    }

    return null;
  },
  // get first idle interaction with a matching pointerType
  idle: function idle(_ref9) {
    var pointerType = _ref9.pointerType,
        scope = _ref9.scope;

    for (var _i6 = 0; _i6 < scope.interactions.list.length; _i6++) {
      var _ref10;

      _ref10 = scope.interactions.list[_i6];
      var interaction = _ref10;

      // if there's already a pointer held down
      if (interaction.pointers.length === 1) {
        var target = interaction.interactable; // don't add this pointer if there is a target interactable and it
        // isn't gesturable

        if (target && !(target.options.gesture && target.options.gesture.enabled)) {
          continue;
        }
      } // maximum of 2 pointers per interaction
      else if (interaction.pointers.length >= 2) {
          continue;
        }

      if (!interaction.interacting() && pointerType === interaction.pointerType) {
        return interaction;
      }
    }

    return null;
  }
};

function hasPointerId(interaction, pointerId) {
  return interaction.pointers.some(function (_ref11) {
    var id = _ref11.id;
    return id === pointerId;
  });
}

var ___default_22 = finder;
_$interactionFinder_22["default"] = ___default_22;

var _$drag_1 = {};
"use strict";

Object.defineProperty(_$drag_1, "__esModule", {
  value: true
});
_$drag_1["default"] = void 0;

var ___scope_1 = _$scope_24({});

var __arr_1 = ___interopRequireWildcard_1(_$arr_47);

var __is_1 = ___interopRequireWildcard_1(_$is_57);

function ___interopRequireWildcard_1(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

___scope_1.ActionName.Drag = 'drag';

function __install_1(scope) {
  var actions = scope.actions,
      Interactable = scope.Interactable,
      interactions = scope.interactions,
      defaults = scope.defaults;
  interactions.signals.on('before-action-move', beforeMove);
  interactions.signals.on('action-resume', beforeMove); // dragmove

  interactions.signals.on('action-move', move);
  Interactable.prototype.draggable = drag.draggable;
  actions[___scope_1.ActionName.Drag] = drag;
  actions.names.push(___scope_1.ActionName.Drag);
  __arr_1.merge(actions.eventTypes, ['dragstart', 'dragmove', 'draginertiastart', 'dragresume', 'dragend']);
  actions.methodDict.drag = 'draggable';
  defaults.actions.drag = drag.defaults;
}

function beforeMove(_ref) {
  var interaction = _ref.interaction;

  if (interaction.prepared.name !== 'drag') {
    return;
  }

  var axis = interaction.prepared.axis;

  if (axis === 'x') {
    interaction.coords.cur.page.y = interaction.coords.start.page.y;
    interaction.coords.cur.client.y = interaction.coords.start.client.y;
    interaction.coords.velocity.client.y = 0;
    interaction.coords.velocity.page.y = 0;
  } else if (axis === 'y') {
    interaction.coords.cur.page.x = interaction.coords.start.page.x;
    interaction.coords.cur.client.x = interaction.coords.start.client.x;
    interaction.coords.velocity.client.x = 0;
    interaction.coords.velocity.page.x = 0;
  }
}

function move(_ref2) {
  var iEvent = _ref2.iEvent,
      interaction = _ref2.interaction;

  if (interaction.prepared.name !== 'drag') {
    return;
  }

  var axis = interaction.prepared.axis;

  if (axis === 'x' || axis === 'y') {
    var opposite = axis === 'x' ? 'y' : 'x';
    iEvent.page[opposite] = interaction.coords.start.page[opposite];
    iEvent.client[opposite] = interaction.coords.start.client[opposite];
    iEvent.delta[opposite] = 0;
  }
}
/**
 * ```js
 * interact(element).draggable({
 *     onstart: function (event) {},
 *     onmove : function (event) {},
 *     onend  : function (event) {},
 *
 *     // the axis in which the first movement must be
 *     // for the drag sequence to start
 *     // 'xy' by default - any direction
 *     startAxis: 'x' || 'y' || 'xy',
 *
 *     // 'xy' by default - don't restrict to one axis (move in any direction)
 *     // 'x' or 'y' to restrict movement to either axis
 *     // 'start' to restrict movement to the axis the drag started in
 *     lockAxis: 'x' || 'y' || 'xy' || 'start',
 *
 *     // max number of drags that can happen concurrently
 *     // with elements of this Interactable. Infinity by default
 *     max: Infinity,
 *
 *     // max number of drags that can target the same element+Interactable
 *     // 1 by default
 *     maxPerElement: 2
 * })
 *
 * var isDraggable = interact('element').draggable(); // true
 * ```
 *
 * Get or set whether drag actions can be performed on the target
 *
 * @alias Interactable.prototype.draggable
 *
 * @param {boolean | object} [options] true/false or An object with event
 * listeners to be fired on drag events (object makes the Interactable
 * draggable)
 * @return {boolean | Interactable} boolean indicating if this can be the
 * target of drag events, or this Interctable
 */


var draggable = function draggable(options) {
  if (__is_1.object(options)) {
    this.options.drag.enabled = options.enabled !== false;
    this.setPerAction('drag', options);
    this.setOnEvents('drag', options);

    if (/^(xy|x|y|start)$/.test(options.lockAxis)) {
      this.options.drag.lockAxis = options.lockAxis;
    }

    if (/^(xy|x|y)$/.test(options.startAxis)) {
      this.options.drag.startAxis = options.startAxis;
    }

    return this;
  }

  if (__is_1.bool(options)) {
    this.options.drag.enabled = options;
    return this;
  }

  return this.options.drag;
};

var drag = {
  id: 'actions/drag',
  install: __install_1,
  draggable: draggable,
  beforeMove: beforeMove,
  move: move,
  defaults: {
    startAxis: 'xy',
    lockAxis: 'xy'
  },
  checker: function checker(_pointer, _event, interactable) {
    var dragOptions = interactable.options.drag;
    return dragOptions.enabled ? {
      name: 'drag',
      axis: dragOptions.lockAxis === 'start' ? dragOptions.startAxis : dragOptions.lockAxis
    } : null;
  },
  getCursor: function getCursor() {
    return 'move';
  }
};
var ___default_1 = drag;
_$drag_1["default"] = ___default_1;

var _$DropEvent_2 = {};
"use strict";

Object.defineProperty(_$DropEvent_2, "__esModule", {
  value: true
});
_$DropEvent_2["default"] = void 0;

var ___BaseEvent2_2 = ___interopRequireDefault_2(_$BaseEvent_13);

var __arr_2 = ___interopRequireWildcard_2(_$arr_47);

function ___interopRequireWildcard_2(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_2(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___typeof_2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { ___typeof_2 = function _typeof(obj) { return typeof obj; }; } else { ___typeof_2 = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return ___typeof_2(obj); }

function ___toConsumableArray_2(arr) { return ___arrayWithoutHoles_2(arr) || ___iterableToArray_2(arr) || ___nonIterableSpread_2(); }

function ___nonIterableSpread_2() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function ___iterableToArray_2(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function ___arrayWithoutHoles_2(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

function ___classCallCheck_2(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_2(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_2(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_2(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_2(Constructor, staticProps); return Constructor; }

function ___possibleConstructorReturn_2(self, call) { if (call && (___typeof_2(call) === "object" || typeof call === "function")) { return call; } return ___assertThisInitialized_2(self); }

function ___assertThisInitialized_2(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function ___getPrototypeOf_2(o) { ___getPrototypeOf_2 = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return ___getPrototypeOf_2(o); }

function ___inherits_2(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) ___setPrototypeOf_2(subClass, superClass); }

function ___setPrototypeOf_2(o, p) { ___setPrototypeOf_2 = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return ___setPrototypeOf_2(o, p); }

var DropEvent =
/*#__PURE__*/
function (_BaseEvent) {
  ___inherits_2(DropEvent, _BaseEvent);

  /**
   * Class of events fired on dropzones during drags with acceptable targets.
   */
  function DropEvent(dropState, dragEvent, type) {
    var _this;

    ___classCallCheck_2(this, DropEvent);

    _this = ___possibleConstructorReturn_2(this, ___getPrototypeOf_2(DropEvent).call(this, dragEvent._interaction));
    _this.propagationStopped = false;
    _this.immediatePropagationStopped = false;

    var _ref = type === 'dragleave' ? dropState.prev : dropState.cur,
        element = _ref.element,
        dropzone = _ref.dropzone;

    _this.type = type;
    _this.target = element;
    _this.currentTarget = element;
    _this.dropzone = dropzone;
    _this.dragEvent = dragEvent;
    _this.relatedTarget = dragEvent.target;
    _this.draggable = dragEvent.interactable;
    _this.timeStamp = dragEvent.timeStamp;
    return _this;
  }
  /**
   * If this is a `dropactivate` event, the dropzone element will be
   * deactivated.
   *
   * If this is a `dragmove` or `dragenter`, a `dragleave` will be fired on the
   * dropzone element and more.
   */


  ___createClass_2(DropEvent, [{
    key: "reject",
    value: function reject() {
      var _this2 = this;

      var dropState = this._interaction.dropState;

      if (this.type !== 'dropactivate' && (!this.dropzone || dropState.cur.dropzone !== this.dropzone || dropState.cur.element !== this.target)) {
        return;
      }

      dropState.prev.dropzone = this.dropzone;
      dropState.prev.element = this.target;
      dropState.rejected = true;
      dropState.events.enter = null;
      this.stopImmediatePropagation();

      if (this.type === 'dropactivate') {
        var activeDrops = dropState.activeDrops;
        var index = __arr_2.findIndex(activeDrops, function (_ref2) {
          var dropzone = _ref2.dropzone,
              element = _ref2.element;
          return dropzone === _this2.dropzone && element === _this2.target;
        });
        dropState.activeDrops = [].concat(___toConsumableArray_2(activeDrops.slice(0, index)), ___toConsumableArray_2(activeDrops.slice(index + 1)));
        var deactivateEvent = new DropEvent(dropState, this.dragEvent, 'dropdeactivate');
        deactivateEvent.dropzone = this.dropzone;
        deactivateEvent.target = this.target;
        this.dropzone.fire(deactivateEvent);
      } else {
        this.dropzone.fire(new DropEvent(dropState, this.dragEvent, 'dragleave'));
      }
    }
  }, {
    key: "preventDefault",
    value: function preventDefault() {}
  }, {
    key: "stopPropagation",
    value: function stopPropagation() {
      this.propagationStopped = true;
    }
  }, {
    key: "stopImmediatePropagation",
    value: function stopImmediatePropagation() {
      this.immediatePropagationStopped = this.propagationStopped = true;
    }
  }]);

  return DropEvent;
}(___BaseEvent2_2["default"]);

var ___default_2 = DropEvent;
_$DropEvent_2["default"] = ___default_2;

var _$drop_3 = {};
"use strict";

Object.defineProperty(_$drop_3, "__esModule", {
  value: true
});
_$drop_3["default"] = void 0;

var __utils_3 = ___interopRequireWildcard_3(_$utils_56);

var _drag = ___interopRequireDefault_3(_$drag_1);

var _DropEvent = ___interopRequireDefault_3(_$DropEvent_2);

function ___interopRequireDefault_3(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_3(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __install_3(scope) {
  var actions = scope.actions,
      interact = scope.interact,
      Interactable = scope.Interactable,
      interactions = scope.interactions,
      defaults = scope.defaults;
  scope.usePlugin(_drag["default"]);
  interactions.signals.on('before-action-start', function (_ref) {
    var interaction = _ref.interaction;

    if (interaction.prepared.name !== 'drag') {
      return;
    }

    interaction.dropState = {
      cur: {
        dropzone: null,
        element: null
      },
      prev: {
        dropzone: null,
        element: null
      },
      rejected: null,
      events: null,
      activeDrops: null
    };
  });
  interactions.signals.on('after-action-start', function (_ref2) {
    var interaction = _ref2.interaction,
        event = _ref2.event,
        dragEvent = _ref2.iEvent;

    if (interaction.prepared.name !== 'drag') {
      return;
    }

    var dropState = interaction.dropState; // reset active dropzones

    dropState.activeDrops = null;
    dropState.events = null;
    dropState.activeDrops = getActiveDrops(scope, interaction.element);
    dropState.events = getDropEvents(interaction, event, dragEvent);

    if (dropState.events.activate) {
      fireActivationEvents(dropState.activeDrops, dropState.events.activate);
    }
  }); // FIXME proper signal types

  interactions.signals.on('action-move', function (arg) {
    return onEventCreated(arg, scope);
  });
  interactions.signals.on('action-end', function (arg) {
    return onEventCreated(arg, scope);
  });
  interactions.signals.on('after-action-move', function (_ref3) {
    var interaction = _ref3.interaction;

    if (interaction.prepared.name !== 'drag') {
      return;
    }

    fireDropEvents(interaction, interaction.dropState.events);
    interaction.dropState.events = {};
  });
  interactions.signals.on('after-action-end', function (_ref4) {
    var interaction = _ref4.interaction;

    if (interaction.prepared.name !== 'drag') {
      return;
    }

    fireDropEvents(interaction, interaction.dropState.events);
  });
  interactions.signals.on('stop', function (_ref5) {
    var interaction = _ref5.interaction;

    if (interaction.prepared.name !== 'drag') {
      return;
    }

    var dropState = interaction.dropState;

    if (dropState) {
      dropState.activeDrops = null;
      dropState.events = null;
      dropState.cur.dropzone = null;
      dropState.cur.element = null;
      dropState.prev.dropzone = null;
      dropState.prev.element = null;
      dropState.rejected = false;
    }
  });
  /**
   *
   * ```js
   * interact('.drop').dropzone({
   *   accept: '.can-drop' || document.getElementById('single-drop'),
   *   overlap: 'pointer' || 'center' || zeroToOne
   * }
   * ```
   *
   * Returns or sets whether draggables can be dropped onto this target to
   * trigger drop events
   *
   * Dropzones can receive the following events:
   *  - `dropactivate` and `dropdeactivate` when an acceptable drag starts and ends
   *  - `dragenter` and `dragleave` when a draggable enters and leaves the dropzone
   *  - `dragmove` when a draggable that has entered the dropzone is moved
   *  - `drop` when a draggable is dropped into this dropzone
   *
   * Use the `accept` option to allow only elements that match the given CSS
   * selector or element. The value can be:
   *
   *  - **an Element** - only that element can be dropped into this dropzone.
   *  - **a string**, - the element being dragged must match it as a CSS selector.
   *  - **`null`** - accept options is cleared - it accepts any element.
   *
   * Use the `overlap` option to set how drops are checked for. The allowed
   * values are:
   *
   *   - `'pointer'`, the pointer must be over the dropzone (default)
   *   - `'center'`, the draggable element's center must be over the dropzone
   *   - a number from 0-1 which is the `(intersection area) / (draggable area)`.
   *   e.g. `0.5` for drop to happen when half of the area of the draggable is
   *   over the dropzone
   *
   * Use the `checker` option to specify a function to check if a dragged element
   * is over this Interactable.
   *
   * @param {boolean | object | null} [options] The new options to be set.
   * @return {boolean | Interactable} The current setting or this Interactable
   */

  Interactable.prototype.dropzone = function (options) {
    return dropzoneMethod(this, options);
  };
  /**
   * ```js
   * interact(target)
   * .dropChecker(function(dragEvent,         // related dragmove or dragend event
   *                       event,             // TouchEvent/PointerEvent/MouseEvent
   *                       dropped,           // bool result of the default checker
   *                       dropzone,          // dropzone Interactable
   *                       dropElement,       // dropzone elemnt
   *                       draggable,         // draggable Interactable
   *                       draggableElement) {// draggable element
   *
   *   return dropped && event.target.hasAttribute('allow-drop')
   * }
   * ```
   */


  Interactable.prototype.dropCheck = function (dragEvent, event, draggable, draggableElement, dropElement, rect) {
    return dropCheckMethod(this, dragEvent, event, draggable, draggableElement, dropElement, rect);
  };
  /**
   * Returns or sets whether the dimensions of dropzone elements are calculated
   * on every dragmove or only on dragstart for the default dropChecker
   *
   * @param {boolean} [newValue] True to check on each move. False to check only
   * before start
   * @return {boolean | interact} The current setting or interact
   */


  interact.dynamicDrop = function (newValue) {
    if (__utils_3.is.bool(newValue)) {
      // if (dragging && scope.dynamicDrop !== newValue && !newValue) {
      //  calcRects(dropzones)
      // }
      scope.dynamicDrop = newValue;
      return interact;
    }

    return scope.dynamicDrop;
  };

  __utils_3.arr.merge(actions.eventTypes, ['dragenter', 'dragleave', 'dropactivate', 'dropdeactivate', 'dropmove', 'drop']);
  actions.methodDict.drop = 'dropzone';
  scope.dynamicDrop = false;
  defaults.actions.drop = drop.defaults;
}

function collectDrops(_ref6, draggableElement) {
  var interactables = _ref6.interactables;
  var drops = []; // collect all dropzones and their elements which qualify for a drop

  for (var _i = 0; _i < interactables.list.length; _i++) {
    var _ref7;

    _ref7 = interactables.list[_i];
    var dropzone = _ref7;

    if (!dropzone.options.drop.enabled) {
      continue;
    }

    var accept = dropzone.options.drop.accept; // test the draggable draggableElement against the dropzone's accept setting

    if (__utils_3.is.element(accept) && accept !== draggableElement || __utils_3.is.string(accept) && !__utils_3.dom.matchesSelector(draggableElement, accept) || __utils_3.is.func(accept) && !accept({
      dropzone: dropzone,
      draggableElement: draggableElement
    })) {
      continue;
    } // query for new elements if necessary


    var dropElements = __utils_3.is.string(dropzone.target) ? dropzone._context.querySelectorAll(dropzone.target) : __utils_3.is.array(dropzone.target) ? dropzone.target : [dropzone.target];

    for (var _i2 = 0; _i2 < dropElements.length; _i2++) {
      var _ref8;

      _ref8 = dropElements[_i2];
      var dropzoneElement = _ref8;

      if (dropzoneElement !== draggableElement) {
        drops.push({
          dropzone: dropzone,
          element: dropzoneElement
        });
      }
    }
  }

  return drops;
}

function fireActivationEvents(activeDrops, event) {
  // loop through all active dropzones and trigger event
  for (var _i3 = 0; _i3 < activeDrops.length; _i3++) {
    var _ref9;

    _ref9 = activeDrops[_i3];
    var _ref10 = _ref9,
        dropzone = _ref10.dropzone,
        element = _ref10.element;
    event.dropzone = dropzone; // set current element as event target

    event.target = element;
    dropzone.fire(event);
    event.propagationStopped = event.immediatePropagationStopped = false;
  }
} // return a new array of possible drops. getActiveDrops should always be
// called when a drag has just started or a drag event happens while
// dynamicDrop is true


function getActiveDrops(scope, dragElement) {
  // get dropzones and their elements that could receive the draggable
  var activeDrops = collectDrops(scope, dragElement);

  for (var _i4 = 0; _i4 < activeDrops.length; _i4++) {
    var _ref11;

    _ref11 = activeDrops[_i4];
    var activeDrop = _ref11;
    activeDrop.rect = activeDrop.dropzone.getRect(activeDrop.element);
  }

  return activeDrops;
}

function getDrop(_ref12, dragEvent, pointerEvent) {
  var dropState = _ref12.dropState,
      draggable = _ref12.interactable,
      dragElement = _ref12.element;
  var validDrops = []; // collect all dropzones and their elements which qualify for a drop

  for (var _i5 = 0; _i5 < dropState.activeDrops.length; _i5++) {
    var _ref13;

    _ref13 = dropState.activeDrops[_i5];
    var _ref14 = _ref13,
        dropzone = _ref14.dropzone,
        dropzoneElement = _ref14.element,
        rect = _ref14.rect;
    validDrops.push(dropzone.dropCheck(dragEvent, pointerEvent, draggable, dragElement, dropzoneElement, rect) ? dropzoneElement : null);
  } // get the most appropriate dropzone based on DOM depth and order


  var dropIndex = __utils_3.dom.indexOfDeepestElement(validDrops);
  return dropState.activeDrops[dropIndex] || null;
}

function getDropEvents(interaction, _pointerEvent, dragEvent) {
  var dropState = interaction.dropState;
  var dropEvents = {
    enter: null,
    leave: null,
    activate: null,
    deactivate: null,
    move: null,
    drop: null
  };

  if (dragEvent.type === 'dragstart') {
    dropEvents.activate = new _DropEvent["default"](dropState, dragEvent, 'dropactivate');
    dropEvents.activate.target = null;
    dropEvents.activate.dropzone = null;
  }

  if (dragEvent.type === 'dragend') {
    dropEvents.deactivate = new _DropEvent["default"](dropState, dragEvent, 'dropdeactivate');
    dropEvents.deactivate.target = null;
    dropEvents.deactivate.dropzone = null;
  }

  if (dropState.rejected) {
    return dropEvents;
  }

  if (dropState.cur.element !== dropState.prev.element) {
    // if there was a previous dropzone, create a dragleave event
    if (dropState.prev.dropzone) {
      dropEvents.leave = new _DropEvent["default"](dropState, dragEvent, 'dragleave');
      dragEvent.dragLeave = dropEvents.leave.target = dropState.prev.element;
      dragEvent.prevDropzone = dropEvents.leave.dropzone = dropState.prev.dropzone;
    } // if dropzone is not null, create a dragenter event


    if (dropState.cur.dropzone) {
      dropEvents.enter = new _DropEvent["default"](dropState, dragEvent, 'dragenter');
      dragEvent.dragEnter = dropState.cur.element;
      dragEvent.dropzone = dropState.cur.dropzone;
    }
  }

  if (dragEvent.type === 'dragend' && dropState.cur.dropzone) {
    dropEvents.drop = new _DropEvent["default"](dropState, dragEvent, 'drop');
    dragEvent.dropzone = dropState.cur.dropzone;
    dragEvent.relatedTarget = dropState.cur.element;
  }

  if (dragEvent.type === 'dragmove' && dropState.cur.dropzone) {
    dropEvents.move = new _DropEvent["default"](dropState, dragEvent, 'dropmove');
    dropEvents.move.dragmove = dragEvent;
    dragEvent.dropzone = dropState.cur.dropzone;
  }

  return dropEvents;
}

function fireDropEvents(interaction, events) {
  var dropState = interaction.dropState;
  var activeDrops = dropState.activeDrops,
      cur = dropState.cur,
      prev = dropState.prev;

  if (events.leave) {
    prev.dropzone.fire(events.leave);
  }

  if (events.move) {
    cur.dropzone.fire(events.move);
  }

  if (events.enter) {
    cur.dropzone.fire(events.enter);
  }

  if (events.drop) {
    cur.dropzone.fire(events.drop);
  }

  if (events.deactivate) {
    fireActivationEvents(activeDrops, events.deactivate);
  }

  dropState.prev.dropzone = cur.dropzone;
  dropState.prev.element = cur.element;
}

function onEventCreated(_ref15, scope) {
  var interaction = _ref15.interaction,
      iEvent = _ref15.iEvent,
      event = _ref15.event;

  if (iEvent.type !== 'dragmove' && iEvent.type !== 'dragend') {
    return;
  }

  var dropState = interaction.dropState;

  if (scope.dynamicDrop) {
    dropState.activeDrops = getActiveDrops(scope, interaction.element);
  }

  var dragEvent = iEvent;
  var dropResult = getDrop(interaction, dragEvent, event); // update rejected status

  dropState.rejected = dropState.rejected && !!dropResult && dropResult.dropzone === dropState.cur.dropzone && dropResult.element === dropState.cur.element;
  dropState.cur.dropzone = dropResult && dropResult.dropzone;
  dropState.cur.element = dropResult && dropResult.element;
  dropState.events = getDropEvents(interaction, event, dragEvent);
}

function dropzoneMethod(interactable, options) {
  if (__utils_3.is.object(options)) {
    interactable.options.drop.enabled = options.enabled !== false;

    if (options.listeners) {
      var normalized = __utils_3.normalizeListeners(options.listeners); // rename 'drop' to '' as it will be prefixed with 'drop'

      var corrected = Object.keys(normalized).reduce(function (acc, type) {
        var correctedType = /^(enter|leave)/.test(type) ? "drag".concat(type) : /^(activate|deactivate|move)/.test(type) ? "drop".concat(type) : type;
        acc[correctedType] = normalized[type];
        return acc;
      }, {});
      interactable.off(interactable.options.drop.listeners);
      interactable.on(corrected);
      interactable.options.drop.listeners = corrected;
    }

    if (__utils_3.is.func(options.ondrop)) {
      interactable.on('drop', options.ondrop);
    }

    if (__utils_3.is.func(options.ondropactivate)) {
      interactable.on('dropactivate', options.ondropactivate);
    }

    if (__utils_3.is.func(options.ondropdeactivate)) {
      interactable.on('dropdeactivate', options.ondropdeactivate);
    }

    if (__utils_3.is.func(options.ondragenter)) {
      interactable.on('dragenter', options.ondragenter);
    }

    if (__utils_3.is.func(options.ondragleave)) {
      interactable.on('dragleave', options.ondragleave);
    }

    if (__utils_3.is.func(options.ondropmove)) {
      interactable.on('dropmove', options.ondropmove);
    }

    if (/^(pointer|center)$/.test(options.overlap)) {
      interactable.options.drop.overlap = options.overlap;
    } else if (__utils_3.is.number(options.overlap)) {
      interactable.options.drop.overlap = Math.max(Math.min(1, options.overlap), 0);
    }

    if ('accept' in options) {
      interactable.options.drop.accept = options.accept;
    }

    if ('checker' in options) {
      interactable.options.drop.checker = options.checker;
    }

    return interactable;
  }

  if (__utils_3.is.bool(options)) {
    interactable.options.drop.enabled = options;
    return interactable;
  }

  return interactable.options.drop;
}

function dropCheckMethod(interactable, dragEvent, event, draggable, draggableElement, dropElement, rect) {
  var dropped = false; // if the dropzone has no rect (eg. display: none)
  // call the custom dropChecker or just return false

  if (!(rect = rect || interactable.getRect(dropElement))) {
    return interactable.options.drop.checker ? interactable.options.drop.checker(dragEvent, event, dropped, interactable, dropElement, draggable, draggableElement) : false;
  }

  var dropOverlap = interactable.options.drop.overlap;

  if (dropOverlap === 'pointer') {
    var origin = __utils_3.getOriginXY(draggable, draggableElement, 'drag');
    var page = __utils_3.pointer.getPageXY(dragEvent);
    page.x += origin.x;
    page.y += origin.y;
    var horizontal = page.x > rect.left && page.x < rect.right;
    var vertical = page.y > rect.top && page.y < rect.bottom;
    dropped = horizontal && vertical;
  }

  var dragRect = draggable.getRect(draggableElement);

  if (dragRect && dropOverlap === 'center') {
    var cx = dragRect.left + dragRect.width / 2;
    var cy = dragRect.top + dragRect.height / 2;
    dropped = cx >= rect.left && cx <= rect.right && cy >= rect.top && cy <= rect.bottom;
  }

  if (dragRect && __utils_3.is.number(dropOverlap)) {
    var overlapArea = Math.max(0, Math.min(rect.right, dragRect.right) - Math.max(rect.left, dragRect.left)) * Math.max(0, Math.min(rect.bottom, dragRect.bottom) - Math.max(rect.top, dragRect.top));
    var overlapRatio = overlapArea / (dragRect.width * dragRect.height);
    dropped = overlapRatio >= dropOverlap;
  }

  if (interactable.options.drop.checker) {
    dropped = interactable.options.drop.checker(dragEvent, event, dropped, interactable, dropElement, draggable, draggableElement);
  }

  return dropped;
}

var drop = {
  id: 'actions/drop',
  install: __install_3,
  getActiveDrops: getActiveDrops,
  getDrop: getDrop,
  getDropEvents: getDropEvents,
  fireDropEvents: fireDropEvents,
  defaults: {
    enabled: false,
    accept: null,
    overlap: 'pointer'
  }
};
var ___default_3 = drop;
_$drop_3["default"] = ___default_3;

var _$gesture_4 = {};
"use strict";

Object.defineProperty(_$gesture_4, "__esModule", {
  value: true
});
_$gesture_4["default"] = void 0;

var ___InteractEvent_4 = ___interopRequireDefault_4(_$InteractEvent_15);

var ___scope_4 = _$scope_24({});

var __utils_4 = ___interopRequireWildcard_4(_$utils_56);

function ___interopRequireWildcard_4(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_4(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

___scope_4.ActionName.Gesture = 'gesture';

function __install_4(scope) {
  var actions = scope.actions,
      Interactable = scope.Interactable,
      interactions = scope.interactions,
      defaults = scope.defaults;
  /**
   * ```js
   * interact(element).gesturable({
   *     onstart: function (event) {},
   *     onmove : function (event) {},
   *     onend  : function (event) {},
   *
   *     // limit multiple gestures.
   *     // See the explanation in {@link Interactable.draggable} example
   *     max: Infinity,
   *     maxPerElement: 1,
   * })
   *
   * var isGestureable = interact(element).gesturable()
   * ```
   *
   * Gets or sets whether multitouch gestures can be performed on the target
   *
   * @param {boolean | object} [options] true/false or An object with event
   * listeners to be fired on gesture events (makes the Interactable gesturable)
   * @return {boolean | Interactable} A boolean indicating if this can be the
   * target of gesture events, or this Interactable
   */

  Interactable.prototype.gesturable = function (options) {
    if (__utils_4.is.object(options)) {
      this.options.gesture.enabled = options.enabled !== false;
      this.setPerAction('gesture', options);
      this.setOnEvents('gesture', options);
      return this;
    }

    if (__utils_4.is.bool(options)) {
      this.options.gesture.enabled = options;
      return this;
    }

    return this.options.gesture;
  };

  interactions.signals.on('action-start', updateGestureProps);
  interactions.signals.on('action-move', updateGestureProps);
  interactions.signals.on('action-end', updateGestureProps);
  interactions.signals.on('new', function (_ref) {
    var interaction = _ref.interaction;
    interaction.gesture = {
      angle: 0,
      distance: 0,
      scale: 1,
      startAngle: 0,
      startDistance: 0
    };
  });
  actions[___scope_4.ActionName.Gesture] = gesture;
  actions.names.push(___scope_4.ActionName.Gesture);
  __utils_4.arr.merge(actions.eventTypes, ['gesturestart', 'gesturemove', 'gestureend']);
  actions.methodDict.gesture = 'gesturable';
  defaults.actions.gesture = gesture.defaults;
}

var gesture = {
  id: 'actions/gesture',
  install: __install_4,
  defaults: {},
  checker: function checker(_pointer, _event, _interactable, _element, interaction) {
    if (interaction.pointers.length >= 2) {
      return {
        name: 'gesture'
      };
    }

    return null;
  },
  getCursor: function getCursor() {
    return '';
  }
};

function updateGestureProps(_ref2) {
  var interaction = _ref2.interaction,
      iEvent = _ref2.iEvent,
      event = _ref2.event,
      phase = _ref2.phase;

  if (interaction.prepared.name !== 'gesture') {
    return;
  }

  var pointers = interaction.pointers.map(function (p) {
    return p.pointer;
  });
  var starting = phase === 'start';
  var ending = phase === 'end';
  var deltaSource = interaction.interactable.options.deltaSource;
  iEvent.touches = [pointers[0], pointers[1]];

  if (starting) {
    iEvent.distance = __utils_4.pointer.touchDistance(pointers, deltaSource);
    iEvent.box = __utils_4.pointer.touchBBox(pointers);
    iEvent.scale = 1;
    iEvent.ds = 0;
    iEvent.angle = __utils_4.pointer.touchAngle(pointers, deltaSource);
    iEvent.da = 0;
    interaction.gesture.startDistance = iEvent.distance;
    interaction.gesture.startAngle = iEvent.angle;
  } else if (ending || event instanceof ___InteractEvent_4["default"]) {
    var prevEvent = interaction.prevEvent;
    iEvent.distance = prevEvent.distance;
    iEvent.box = prevEvent.box;
    iEvent.scale = prevEvent.scale;
    iEvent.ds = 0;
    iEvent.angle = prevEvent.angle;
    iEvent.da = 0;
  } else {
    iEvent.distance = __utils_4.pointer.touchDistance(pointers, deltaSource);
    iEvent.box = __utils_4.pointer.touchBBox(pointers);
    iEvent.scale = iEvent.distance / interaction.gesture.startDistance;
    iEvent.angle = __utils_4.pointer.touchAngle(pointers, deltaSource);
    iEvent.ds = iEvent.scale - interaction.gesture.scale;
    iEvent.da = iEvent.angle - interaction.gesture.angle;
  }

  interaction.gesture.distance = iEvent.distance;
  interaction.gesture.angle = iEvent.angle;

  if (__utils_4.is.number(iEvent.scale) && iEvent.scale !== Infinity && !isNaN(iEvent.scale)) {
    interaction.gesture.scale = iEvent.scale;
  }
}

var ___default_4 = gesture;
_$gesture_4["default"] = ___default_4;

var _$resize_6 = {};
"use strict";

Object.defineProperty(_$resize_6, "__esModule", {
  value: true
});
_$resize_6["default"] = void 0;

var ___scope_6 = _$scope_24({});

var __arr_6 = ___interopRequireWildcard_6(_$arr_47);

var __dom_6 = ___interopRequireWildcard_6(_$domUtils_51);

var ___extend_6 = ___interopRequireDefault_6(_$extend_53);

var __is_6 = ___interopRequireWildcard_6(_$is_57);

function ___interopRequireDefault_6(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_6(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

___scope_6.ActionName.Resize = 'resize';

function __install_6(scope) {
  var actions = scope.actions,
      browser = scope.browser,
      Interactable = scope.Interactable,
      interactions = scope.interactions,
      defaults = scope.defaults; // Less Precision with touch input

  interactions.signals.on('new', function (interaction) {
    interaction.resizeAxes = 'xy';
  });
  interactions.signals.on('action-start', start);
  interactions.signals.on('action-move', __move_6);
  interactions.signals.on('action-start', updateEventAxes);
  interactions.signals.on('action-move', updateEventAxes);
  resize.cursors = initCursors(browser);
  resize.defaultMargin = browser.supportsTouch || browser.supportsPointerEvent ? 20 : 10;
  /**
   * ```js
   * interact(element).resizable({
   *   onstart: function (event) {},
   *   onmove : function (event) {},
   *   onend  : function (event) {},
   *
   *   edges: {
   *     top   : true,       // Use pointer coords to check for resize.
   *     left  : false,      // Disable resizing from left edge.
   *     bottom: '.resize-s',// Resize if pointer target matches selector
   *     right : handleEl    // Resize if pointer target is the given Element
   *   },
   *
   *     // Width and height can be adjusted independently. When `true`, width and
   *     // height are adjusted at a 1:1 ratio.
   *     square: false,
   *
   *     // Width and height can be adjusted independently. When `true`, width and
   *     // height maintain the aspect ratio they had when resizing started.
   *     preserveAspectRatio: false,
   *
   *   // a value of 'none' will limit the resize rect to a minimum of 0x0
   *   // 'negate' will allow the rect to have negative width/height
   *   // 'reposition' will keep the width/height positive by swapping
   *   // the top and bottom edges and/or swapping the left and right edges
   *   invert: 'none' || 'negate' || 'reposition'
   *
   *   // limit multiple resizes.
   *   // See the explanation in the {@link Interactable.draggable} example
   *   max: Infinity,
   *   maxPerElement: 1,
   * })
   *
   * var isResizeable = interact(element).resizable()
   * ```
   *
   * Gets or sets whether resize actions can be performed on the target
   *
   * @param {boolean | object} [options] true/false or An object with event
   * listeners to be fired on resize events (object makes the Interactable
   * resizable)
   * @return {boolean | Interactable} A boolean indicating if this can be the
   * target of resize elements, or this Interactable
   */

  Interactable.prototype.resizable = function (options) {
    return resizable(this, options, scope);
  };

  actions[___scope_6.ActionName.Resize] = resize;
  actions.names.push(___scope_6.ActionName.Resize);
  __arr_6.merge(actions.eventTypes, ['resizestart', 'resizemove', 'resizeinertiastart', 'resizeresume', 'resizeend']);
  actions.methodDict.resize = 'resizable';
  defaults.actions.resize = resize.defaults;
}

var resize = {
  id: 'actions/resize',
  install: __install_6,
  defaults: {
    square: false,
    preserveAspectRatio: false,
    axis: 'xy',
    // use default margin
    margin: NaN,
    // object with props left, right, top, bottom which are
    // true/false values to resize when the pointer is over that edge,
    // CSS selectors to match the handles for each direction
    // or the Elements for each handle
    edges: null,
    // a value of 'none' will limit the resize rect to a minimum of 0x0
    // 'negate' will alow the rect to have negative width/height
    // 'reposition' will keep the width/height positive by swapping
    // the top and bottom edges and/or swapping the left and right edges
    invert: 'none'
  },
  checker: function checker(_pointer, _event, interactable, element, interaction, rect) {
    if (!rect) {
      return null;
    }

    var page = (0, ___extend_6["default"])({}, interaction.coords.cur.page);
    var options = interactable.options;

    if (options.resize.enabled) {
      var resizeOptions = options.resize;
      var resizeEdges = {
        left: false,
        right: false,
        top: false,
        bottom: false
      }; // if using resize.edges

      if (__is_6.object(resizeOptions.edges)) {
        for (var edge in resizeEdges) {
          resizeEdges[edge] = checkResizeEdge(edge, resizeOptions.edges[edge], page, interaction._latestPointer.eventTarget, element, rect, resizeOptions.margin || this.defaultMargin);
        }

        resizeEdges.left = resizeEdges.left && !resizeEdges.right;
        resizeEdges.top = resizeEdges.top && !resizeEdges.bottom;

        if (resizeEdges.left || resizeEdges.right || resizeEdges.top || resizeEdges.bottom) {
          return {
            name: 'resize',
            edges: resizeEdges
          };
        }
      } else {
        var right = options.resize.axis !== 'y' && page.x > rect.right - this.defaultMargin;
        var bottom = options.resize.axis !== 'x' && page.y > rect.bottom - this.defaultMargin;

        if (right || bottom) {
          return {
            name: 'resize',
            axes: (right ? 'x' : '') + (bottom ? 'y' : '')
          };
        }
      }
    }

    return null;
  },
  cursors: null,
  getCursor: function getCursor(_ref) {
    var edges = _ref.edges,
        axis = _ref.axis,
        name = _ref.name;
    var cursors = resize.cursors;
    var result = null;

    if (axis) {
      result = cursors[name + axis];
    } else if (edges) {
      var cursorKey = '';
      var _arr = ['top', 'bottom', 'left', 'right'];

      for (var _i = 0; _i < _arr.length; _i++) {
        var edge = _arr[_i];

        if (edges[edge]) {
          cursorKey += edge;
        }
      }

      result = cursors[cursorKey];
    }

    return result;
  },
  defaultMargin: null
};

function resizable(interactable, options, scope) {
  if (__is_6.object(options)) {
    interactable.options.resize.enabled = options.enabled !== false;
    interactable.setPerAction('resize', options);
    interactable.setOnEvents('resize', options);

    if (__is_6.string(options.axis) && /^x$|^y$|^xy$/.test(options.axis)) {
      interactable.options.resize.axis = options.axis;
    } else if (options.axis === null) {
      interactable.options.resize.axis = scope.defaults.actions.resize.axis;
    }

    if (__is_6.bool(options.preserveAspectRatio)) {
      interactable.options.resize.preserveAspectRatio = options.preserveAspectRatio;
    } else if (__is_6.bool(options.square)) {
      interactable.options.resize.square = options.square;
    }

    return interactable;
  }

  if (__is_6.bool(options)) {
    interactable.options.resize.enabled = options;
    return interactable;
  }

  return interactable.options.resize;
}

function checkResizeEdge(name, value, page, element, interactableElement, rect, margin) {
  // false, '', undefined, null
  if (!value) {
    return false;
  } // true value, use pointer coords and element rect


  if (value === true) {
    // if dimensions are negative, "switch" edges
    var width = __is_6.number(rect.width) ? rect.width : rect.right - rect.left;
    var height = __is_6.number(rect.height) ? rect.height : rect.bottom - rect.top; // don't use margin greater than half the relevent dimension

    margin = Math.min(margin, (name === 'left' || name === 'right' ? width : height) / 2);

    if (width < 0) {
      if (name === 'left') {
        name = 'right';
      } else if (name === 'right') {
        name = 'left';
      }
    }

    if (height < 0) {
      if (name === 'top') {
        name = 'bottom';
      } else if (name === 'bottom') {
        name = 'top';
      }
    }

    if (name === 'left') {
      return page.x < (width >= 0 ? rect.left : rect.right) + margin;
    }

    if (name === 'top') {
      return page.y < (height >= 0 ? rect.top : rect.bottom) + margin;
    }

    if (name === 'right') {
      return page.x > (width >= 0 ? rect.right : rect.left) - margin;
    }

    if (name === 'bottom') {
      return page.y > (height >= 0 ? rect.bottom : rect.top) - margin;
    }
  } // the remaining checks require an element


  if (!__is_6.element(element)) {
    return false;
  }

  return __is_6.element(value) // the value is an element to use as a resize handle
  ? value === element // otherwise check if element matches value as selector
  : __dom_6.matchesUpTo(element, value, interactableElement);
}

function initCursors(browser) {
  return browser.isIe9 ? {
    x: 'e-resize',
    y: 's-resize',
    xy: 'se-resize',
    top: 'n-resize',
    left: 'w-resize',
    bottom: 's-resize',
    right: 'e-resize',
    topleft: 'se-resize',
    bottomright: 'se-resize',
    topright: 'ne-resize',
    bottomleft: 'ne-resize'
  } : {
    x: 'ew-resize',
    y: 'ns-resize',
    xy: 'nwse-resize',
    top: 'ns-resize',
    left: 'ew-resize',
    bottom: 'ns-resize',
    right: 'ew-resize',
    topleft: 'nwse-resize',
    bottomright: 'nwse-resize',
    topright: 'nesw-resize',
    bottomleft: 'nesw-resize'
  };
}

function start(_ref2) {
  var iEvent = _ref2.iEvent,
      interaction = _ref2.interaction;

  if (interaction.prepared.name !== 'resize' || !interaction.prepared.edges) {
    return;
  }

  var startRect = interaction.rect;
  var resizeOptions = interaction.interactable.options.resize;
  /*
   * When using the `resizable.square` or `resizable.preserveAspectRatio` options, resizing from one edge
   * will affect another. E.g. with `resizable.square`, resizing to make the right edge larger will make
   * the bottom edge larger by the same amount. We call these 'linked' edges. Any linked edges will depend
   * on the active edges and the edge being interacted with.
   */

  if (resizeOptions.square || resizeOptions.preserveAspectRatio) {
    var linkedEdges = (0, ___extend_6["default"])({}, interaction.prepared.edges);
    linkedEdges.top = linkedEdges.top || linkedEdges.left && !linkedEdges.bottom;
    linkedEdges.left = linkedEdges.left || linkedEdges.top && !linkedEdges.right;
    linkedEdges.bottom = linkedEdges.bottom || linkedEdges.right && !linkedEdges.top;
    linkedEdges.right = linkedEdges.right || linkedEdges.bottom && !linkedEdges.left;
    interaction.prepared._linkedEdges = linkedEdges;
  } else {
    interaction.prepared._linkedEdges = null;
  } // if using `resizable.preserveAspectRatio` option, record aspect ratio at the start of the resize


  if (resizeOptions.preserveAspectRatio) {
    interaction.resizeStartAspectRatio = startRect.width / startRect.height;
  }

  interaction.resizeRects = {
    start: startRect,
    current: (0, ___extend_6["default"])({}, startRect),
    inverted: (0, ___extend_6["default"])({}, startRect),
    previous: (0, ___extend_6["default"])({}, startRect),
    delta: {
      left: 0,
      right: 0,
      width: 0,
      top: 0,
      bottom: 0,
      height: 0
    }
  };
  iEvent.rect = interaction.resizeRects.inverted;
  iEvent.deltaRect = interaction.resizeRects.delta;
}

function __move_6(_ref3) {
  var iEvent = _ref3.iEvent,
      interaction = _ref3.interaction;

  if (interaction.prepared.name !== 'resize' || !interaction.prepared.edges) {
    return;
  }

  var resizeOptions = interaction.interactable.options.resize;
  var invert = resizeOptions.invert;
  var invertible = invert === 'reposition' || invert === 'negate';
  var edges = interaction.prepared.edges; // eslint-disable-next-line no-shadow

  var start = interaction.resizeRects.start;
  var current = interaction.resizeRects.current;
  var inverted = interaction.resizeRects.inverted;
  var deltaRect = interaction.resizeRects.delta;
  var previous = (0, ___extend_6["default"])(interaction.resizeRects.previous, inverted);
  var originalEdges = edges;
  var eventDelta = (0, ___extend_6["default"])({}, iEvent.delta);

  if (resizeOptions.preserveAspectRatio || resizeOptions.square) {
    // `resize.preserveAspectRatio` takes precedence over `resize.square`
    var startAspectRatio = resizeOptions.preserveAspectRatio ? interaction.resizeStartAspectRatio : 1;
    edges = interaction.prepared._linkedEdges;

    if (originalEdges.left && originalEdges.bottom || originalEdges.right && originalEdges.top) {
      eventDelta.y = -eventDelta.x / startAspectRatio;
    } else if (originalEdges.left || originalEdges.right) {
      eventDelta.y = eventDelta.x / startAspectRatio;
    } else if (originalEdges.top || originalEdges.bottom) {
      eventDelta.x = eventDelta.y * startAspectRatio;
    }
  } // update the 'current' rect without modifications


  if (edges.top) {
    current.top += eventDelta.y;
  }

  if (edges.bottom) {
    current.bottom += eventDelta.y;
  }

  if (edges.left) {
    current.left += eventDelta.x;
  }

  if (edges.right) {
    current.right += eventDelta.x;
  }

  if (invertible) {
    // if invertible, copy the current rect
    (0, ___extend_6["default"])(inverted, current);

    if (invert === 'reposition') {
      // swap edge values if necessary to keep width/height positive
      var swap;

      if (inverted.top > inverted.bottom) {
        swap = inverted.top;
        inverted.top = inverted.bottom;
        inverted.bottom = swap;
      }

      if (inverted.left > inverted.right) {
        swap = inverted.left;
        inverted.left = inverted.right;
        inverted.right = swap;
      }
    }
  } else {
    // if not invertible, restrict to minimum of 0x0 rect
    inverted.top = Math.min(current.top, start.bottom);
    inverted.bottom = Math.max(current.bottom, start.top);
    inverted.left = Math.min(current.left, start.right);
    inverted.right = Math.max(current.right, start.left);
  }

  inverted.width = inverted.right - inverted.left;
  inverted.height = inverted.bottom - inverted.top;

  for (var edge in inverted) {
    deltaRect[edge] = inverted[edge] - previous[edge];
  }

  iEvent.edges = interaction.prepared.edges;
  iEvent.rect = inverted;
  iEvent.deltaRect = deltaRect;
}

function updateEventAxes(_ref4) {
  var interaction = _ref4.interaction,
      iEvent = _ref4.iEvent,
      action = _ref4.action;

  if (action !== 'resize' || !interaction.resizeAxes) {
    return;
  }

  var options = interaction.interactable.options;

  if (options.resize.square) {
    if (interaction.resizeAxes === 'y') {
      iEvent.delta.x = iEvent.delta.y;
    } else {
      iEvent.delta.y = iEvent.delta.x;
    }

    iEvent.axes = 'xy';
  } else {
    iEvent.axes = interaction.resizeAxes;

    if (interaction.resizeAxes === 'x') {
      iEvent.delta.y = 0;
    } else if (interaction.resizeAxes === 'y') {
      iEvent.delta.x = 0;
    }
  }
}

var ___default_6 = resize;
_$resize_6["default"] = ___default_6;

var _$actions_5 = {};
"use strict";

Object.defineProperty(_$actions_5, "__esModule", {
  value: true
});
_$actions_5.install = __install_5;
Object.defineProperty(_$actions_5, "drag", {
  enumerable: true,
  get: function get() {
    return ___drag_5["default"];
  }
});
Object.defineProperty(_$actions_5, "drop", {
  enumerable: true,
  get: function get() {
    return _drop["default"];
  }
});
Object.defineProperty(_$actions_5, "gesture", {
  enumerable: true,
  get: function get() {
    return _gesture["default"];
  }
});
Object.defineProperty(_$actions_5, "resize", {
  enumerable: true,
  get: function get() {
    return _resize["default"];
  }
});
_$actions_5.id = void 0;

var ___drag_5 = ___interopRequireDefault_5(_$drag_1);

var _drop = ___interopRequireDefault_5(_$drop_3);

var _gesture = ___interopRequireDefault_5(_$gesture_4);

var _resize = ___interopRequireDefault_5(_$resize_6);

function ___interopRequireDefault_5(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __install_5(scope) {
  scope.usePlugin(_gesture["default"]);
  scope.usePlugin(_resize["default"]);
  scope.usePlugin(___drag_5["default"]);
  scope.usePlugin(_drop["default"]);
}

var id = 'actions';
_$actions_5.id = id;

var _$autoScroll_7 = {};
"use strict";

Object.defineProperty(_$autoScroll_7, "__esModule", {
  value: true
});
_$autoScroll_7.getContainer = getContainer;
_$autoScroll_7.getScroll = getScroll;
_$autoScroll_7.getScrollSize = getScrollSize;
_$autoScroll_7.getScrollSizeDelta = getScrollSizeDelta;
_$autoScroll_7["default"] = void 0;

var __domUtils_7 = ___interopRequireWildcard_7(_$domUtils_51);

var __is_7 = ___interopRequireWildcard_7(_$is_57);

var ___raf_7 = ___interopRequireDefault_7(_$raf_62);

/* removed: var _$rect_63 = require("@interactjs/utils/rect"); */;

/* removed: var _$window_66 = require("@interactjs/utils/window"); */;

function ___interopRequireDefault_7(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_7(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __install_7(scope) {
  var interactions = scope.interactions,
      defaults = scope.defaults,
      actions = scope.actions;
  scope.autoScroll = autoScroll;

  autoScroll.now = function () {
    return scope.now();
  };

  interactions.signals.on('new', function (_ref) {
    var interaction = _ref.interaction;
    interaction.autoScroll = null;
  });
  interactions.signals.on('destroy', function (_ref2) {
    var interaction = _ref2.interaction;
    interaction.autoScroll = null;
    autoScroll.stop();

    if (autoScroll.interaction) {
      autoScroll.interaction = null;
    }
  });
  interactions.signals.on('stop', autoScroll.stop);
  interactions.signals.on('action-move', function (arg) {
    return autoScroll.onInteractionMove(arg);
  });
  actions.eventTypes.push('autoscroll');
  defaults.perAction.autoScroll = autoScroll.defaults;
}

var autoScroll = {
  defaults: {
    enabled: false,
    margin: 60,
    // the item that is scrolled (Window or HTMLElement)
    container: null,
    // the scroll speed in pixels per second
    speed: 300
  },
  now: Date.now,
  interaction: null,
  i: null,
  x: 0,
  y: 0,
  isScrolling: false,
  prevTime: 0,
  margin: 0,
  speed: 0,
  start: function start(interaction) {
    autoScroll.isScrolling = true;

    ___raf_7["default"].cancel(autoScroll.i);

    interaction.autoScroll = autoScroll;
    autoScroll.interaction = interaction;
    autoScroll.prevTime = autoScroll.now();
    autoScroll.i = ___raf_7["default"].request(autoScroll.scroll);
  },
  stop: function stop() {
    autoScroll.isScrolling = false;

    if (autoScroll.interaction) {
      autoScroll.interaction.autoScroll = null;
    }

    ___raf_7["default"].cancel(autoScroll.i);
  },
  // scroll the window by the values in scroll.x/y
  scroll: function scroll() {
    var interaction = autoScroll.interaction;
    var interactable = interaction.interactable,
        element = interaction.element;
    var options = interactable.options[autoScroll.interaction.prepared.name].autoScroll;
    var container = getContainer(options.container, interactable, element);
    var now = autoScroll.now(); // change in time in seconds

    var dt = (now - autoScroll.prevTime) / 1000; // displacement

    var s = options.speed * dt;

    if (s >= 1) {
      var scrollBy = {
        x: autoScroll.x * s,
        y: autoScroll.y * s
      };

      if (scrollBy.x || scrollBy.y) {
        var prevScroll = getScroll(container);

        if (__is_7.window(container)) {
          container.scrollBy(scrollBy.x, scrollBy.y);
        } else if (container) {
          container.scrollLeft += scrollBy.x;
          container.scrollTop += scrollBy.y;
        }

        var curScroll = getScroll(container);
        var delta = {
          x: curScroll.x - prevScroll.x,
          y: curScroll.y - prevScroll.y
        };

        if (delta.x || delta.y) {
          interactable.fire({
            type: 'autoscroll',
            target: element,
            interactable: interactable,
            delta: delta,
            interaction: interaction,
            container: container
          });
        }
      }

      autoScroll.prevTime = now;
    }

    if (autoScroll.isScrolling) {
      ___raf_7["default"].cancel(autoScroll.i);

      autoScroll.i = ___raf_7["default"].request(autoScroll.scroll);
    }
  },
  check: function check(interactable, actionName) {
    var options = interactable.options;
    return options[actionName].autoScroll && options[actionName].autoScroll.enabled;
  },
  onInteractionMove: function onInteractionMove(_ref3) {
    var interaction = _ref3.interaction,
        pointer = _ref3.pointer;

    if (!(interaction.interacting() && autoScroll.check(interaction.interactable, interaction.prepared.name))) {
      return;
    }

    if (interaction.simulation) {
      autoScroll.x = autoScroll.y = 0;
      return;
    }

    var top;
    var right;
    var bottom;
    var left;
    var interactable = interaction.interactable,
        element = interaction.element;
    var options = interactable.options[interaction.prepared.name].autoScroll;
    var container = getContainer(options.container, interactable, element);

    if (__is_7.window(container)) {
      left = pointer.clientX < autoScroll.margin;
      top = pointer.clientY < autoScroll.margin;
      right = pointer.clientX > container.innerWidth - autoScroll.margin;
      bottom = pointer.clientY > container.innerHeight - autoScroll.margin;
    } else {
      var rect = __domUtils_7.getElementClientRect(container);
      left = pointer.clientX < rect.left + autoScroll.margin;
      top = pointer.clientY < rect.top + autoScroll.margin;
      right = pointer.clientX > rect.right - autoScroll.margin;
      bottom = pointer.clientY > rect.bottom - autoScroll.margin;
    }

    autoScroll.x = right ? 1 : left ? -1 : 0;
    autoScroll.y = bottom ? 1 : top ? -1 : 0;

    if (!autoScroll.isScrolling) {
      // set the autoScroll properties to those of the target
      autoScroll.margin = options.margin;
      autoScroll.speed = options.speed;
      autoScroll.start(interaction);
    }
  }
};

function getContainer(value, interactable, element) {
  return (__is_7.string(value) ? (0, _$rect_63.getStringOptionResult)(value, interactable, element) : value) || (0, _$window_66.getWindow)(element);
}

function getScroll(container) {
  if (__is_7.window(container)) {
    container = window.document.body;
  }

  return {
    x: container.scrollLeft,
    y: container.scrollTop
  };
}

function getScrollSize(container) {
  if (__is_7.window(container)) {
    container = window.document.body;
  }

  return {
    x: container.scrollWidth,
    y: container.scrollHeight
  };
}

function getScrollSizeDelta(_ref4, func) {
  var interaction = _ref4.interaction,
      element = _ref4.element;
  var scrollOptions = interaction && interaction.interactable.options[interaction.prepared.name].autoScroll;

  if (!scrollOptions || !scrollOptions.enabled) {
    func();
    return {
      x: 0,
      y: 0
    };
  }

  var scrollContainer = getContainer(scrollOptions.container, interaction.interactable, element);
  var prevSize = getScroll(scrollContainer);
  func();
  var curSize = getScroll(scrollContainer);
  return {
    x: curSize.x - prevSize.x,
    y: curSize.y - prevSize.y
  };
}

var ___default_7 = {
  id: 'auto-scroll',
  install: __install_7
};
_$autoScroll_7["default"] = ___default_7;

var _$InteractableMethods_8 = {};
"use strict";

Object.defineProperty(_$InteractableMethods_8, "__esModule", {
  value: true
});
_$InteractableMethods_8["default"] = void 0;

/* removed: var _$utils_56 = require("@interactjs/utils"); */;

var __is_8 = ___interopRequireWildcard_8(_$is_57);

function ___interopRequireWildcard_8(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __install_8(scope) {
  var Interactable = scope.Interactable,
      actions = scope.actions;
  Interactable.prototype.getAction = getAction;
  /**
   * ```js
   * interact(element, { ignoreFrom: document.getElementById('no-action') })
   * // or
   * interact(element).ignoreFrom('input, textarea, a')
   * ```
   * @deprecated
   * If the target of the `mousedown`, `pointerdown` or `touchstart` event or any
   * of it's parents match the given CSS selector or Element, no
   * drag/resize/gesture is started.
   *
   * Don't use this method. Instead set the `ignoreFrom` option for each action
   * or for `pointerEvents`
   *
   * @example
   * interact(targett)
   *   .draggable({
   *     ignoreFrom: 'input, textarea, a[href]'',
   *   })
   *   .pointerEvents({
   *     ignoreFrom: '[no-pointer]',
   *   })
   *
   * @param {string | Element | null} [newValue] a CSS selector string, an
   * Element or `null` to not ignore any elements
   * @return {string | Element | object} The current ignoreFrom value or this
   * Interactable
   */

  Interactable.prototype.ignoreFrom = (0, _$utils_56.warnOnce)(function (newValue) {
    return this._backCompatOption('ignoreFrom', newValue);
  }, 'Interactable.ignoreFrom() has been deprecated. Use Interactble.draggable({ignoreFrom: newValue}).');
  /**
   * @deprecated
   *
   * A drag/resize/gesture is started only If the target of the `mousedown`,
   * `pointerdown` or `touchstart` event or any of it's parents match the given
   * CSS selector or Element.
   *
   * Don't use this method. Instead set the `allowFrom` option for each action
   * or for `pointerEvents`
   *
   * @example
   * interact(targett)
   *   .resizable({
   *     allowFrom: '.resize-handle',
   *   .pointerEvents({
   *     allowFrom: '.handle',,
   *   })
   *
   * @param {string | Element | null} [newValue] a CSS selector string, an
   * Element or `null` to allow from any element
   * @return {string | Element | object} The current allowFrom value or this
   * Interactable
   */

  Interactable.prototype.allowFrom = (0, _$utils_56.warnOnce)(function (newValue) {
    return this._backCompatOption('allowFrom', newValue);
  }, 'Interactable.allowFrom() has been deprecated. Use Interactble.draggable({allowFrom: newValue}).');
  /**
   * ```js
   * interact('.resize-drag')
   *   .resizable(true)
   *   .draggable(true)
   *   .actionChecker(function (pointer, event, action, interactable, element, interaction) {
   *
   *   if (interact.matchesSelector(event.target, '.drag-handle')) {
   *     // force drag with handle target
   *     action.name = drag
   *   }
   *   else {
   *     // resize from the top and right edges
   *     action.name  = 'resize'
   *     action.edges = { top: true, right: true }
   *   }
   *
   *   return action
   * })
   * ```
   *
   * Returns or sets the function used to check action to be performed on
   * pointerDown
   *
   * @param {function | null} [checker] A function which takes a pointer event,
   * defaultAction string, interactable, element and interaction as parameters
   * and returns an object with name property 'drag' 'resize' or 'gesture' and
   * optionally an `edges` object with boolean 'top', 'left', 'bottom' and right
   * props.
   * @return {Function | Interactable} The checker function or this Interactable
   */

  Interactable.prototype.actionChecker = actionChecker;
  /**
   * Returns or sets whether the the cursor should be changed depending on the
   * action that would be performed if the mouse were pressed and dragged.
   *
   * @param {boolean} [newValue]
   * @return {boolean | Interactable} The current setting or this Interactable
   */

  Interactable.prototype.styleCursor = styleCursor;

  Interactable.prototype.defaultActionChecker = function (pointer, event, interaction, element) {
    return defaultActionChecker(this, pointer, event, interaction, element, actions);
  };
}

function getAction(pointer, event, interaction, element) {
  var action = this.defaultActionChecker(pointer, event, interaction, element);

  if (this.options.actionChecker) {
    return this.options.actionChecker(pointer, event, action, this, element, interaction);
  }

  return action;
}

function defaultActionChecker(interactable, pointer, event, interaction, element, actions) {
  var rect = interactable.getRect(element);
  var buttons = event.buttons || {
    0: 1,
    1: 4,
    3: 8,
    4: 16
  }[event.button];
  var action = null;

  for (var _i = 0; _i < actions.names.length; _i++) {
    var _ref;

    _ref = actions.names[_i];
    var actionName = _ref;

    // check mouseButton setting if the pointer is down
    if (interaction.pointerIsDown && /mouse|pointer/.test(interaction.pointerType) && (buttons & interactable.options[actionName].mouseButtons) === 0) {
      continue;
    }

    action = actions[actionName].checker(pointer, event, interactable, element, interaction, rect);

    if (action) {
      return action;
    }
  }
}

function styleCursor(newValue) {
  if (__is_8.bool(newValue)) {
    this.options.styleCursor = newValue;
    return this;
  }

  if (newValue === null) {
    delete this.options.styleCursor;
    return this;
  }

  return this.options.styleCursor;
}

function actionChecker(checker) {
  if (__is_8.func(checker)) {
    this.options.actionChecker = checker;
    return this;
  }

  if (checker === null) {
    delete this.options.actionChecker;
    return this;
  }

  return this.options.actionChecker;
}

var ___default_8 = {
  id: 'auto-start/interactableMethods',
  install: __install_8
};
_$InteractableMethods_8["default"] = ___default_8;

var _$base_9 = {};
"use strict";

Object.defineProperty(_$base_9, "__esModule", {
  value: true
});
_$base_9["default"] = void 0;

var __utils_9 = ___interopRequireWildcard_9(_$utils_56);

var _InteractableMethods = ___interopRequireDefault_9(_$InteractableMethods_8);

function ___interopRequireDefault_9(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_9(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __install_9(scope) {
  var interact = scope.interact,
      interactions = scope.interactions,
      defaults = scope.defaults;
  scope.usePlugin(_InteractableMethods["default"]); // set cursor style on mousedown

  interactions.signals.on('down', function (_ref) {
    var interaction = _ref.interaction,
        pointer = _ref.pointer,
        event = _ref.event,
        eventTarget = _ref.eventTarget;

    if (interaction.interacting()) {
      return;
    }

    var actionInfo = getActionInfo(interaction, pointer, event, eventTarget, scope);
    prepare(interaction, actionInfo, scope);
  }); // set cursor style on mousemove

  interactions.signals.on('move', function (_ref2) {
    var interaction = _ref2.interaction,
        pointer = _ref2.pointer,
        event = _ref2.event,
        eventTarget = _ref2.eventTarget;

    if (interaction.pointerType !== 'mouse' || interaction.pointerIsDown || interaction.interacting()) {
      return;
    }

    var actionInfo = getActionInfo(interaction, pointer, event, eventTarget, scope);
    prepare(interaction, actionInfo, scope);
  });
  interactions.signals.on('move', function (arg) {
    var interaction = arg.interaction;

    if (!interaction.pointerIsDown || interaction.interacting() || !interaction.pointerWasMoved || !interaction.prepared.name) {
      return;
    }

    scope.autoStart.signals.fire('before-start', arg);
    var interactable = interaction.interactable;

    if (interaction.prepared.name && interactable) {
      // check manualStart and interaction limit
      if (interactable.options[interaction.prepared.name].manualStart || !withinInteractionLimit(interactable, interaction.element, interaction.prepared, scope)) {
        interaction.stop();
      } else {
        interaction.start(interaction.prepared, interactable, interaction.element);
      }
    }
  });
  interactions.signals.on('stop', function (_ref3) {
    var interaction = _ref3.interaction;
    var interactable = interaction.interactable;

    if (interactable && interactable.options.styleCursor) {
      setCursor(interaction.element, '', scope);
    }
  });
  defaults.base.actionChecker = null;
  defaults.base.styleCursor = true;
  __utils_9.extend(defaults.perAction, {
    manualStart: false,
    max: Infinity,
    maxPerElement: 1,
    allowFrom: null,
    ignoreFrom: null,
    // only allow left button by default
    // see https://developer.mozilla.org/en-US/docs/Web/API/MouseEvent/buttons#Return_value
    mouseButtons: 1
  });
  /**
   * Returns or sets the maximum number of concurrent interactions allowed.  By
   * default only 1 interaction is allowed at a time (for backwards
   * compatibility). To allow multiple interactions on the same Interactables and
   * elements, you need to enable it in the draggable, resizable and gesturable
   * `'max'` and `'maxPerElement'` options.
   *
   * @alias module:interact.maxInteractions
   *
   * @param {number} [newValue] Any number. newValue <= 0 means no interactions.
   */

  interact.maxInteractions = function (newValue) {
    return maxInteractions(newValue, scope);
  };

  scope.autoStart = {
    // Allow this many interactions to happen simultaneously
    maxInteractions: Infinity,
    withinInteractionLimit: withinInteractionLimit,
    cursorElement: null,
    signals: new __utils_9.Signals()
  };
} // Check if the current interactable supports the action.
// If so, return the validated action. Otherwise, return null


function validateAction(action, interactable, element, eventTarget, scope) {
  if (interactable.testIgnoreAllow(interactable.options[action.name], element, eventTarget) && interactable.options[action.name].enabled && withinInteractionLimit(interactable, element, action, scope)) {
    return action;
  }

  return null;
}

function validateMatches(interaction, pointer, event, matches, matchElements, eventTarget, scope) {
  for (var i = 0, len = matches.length; i < len; i++) {
    var match = matches[i];
    var matchElement = matchElements[i];
    var matchAction = match.getAction(pointer, event, interaction, matchElement);

    if (!matchAction) {
      continue;
    }

    var action = validateAction(matchAction, match, matchElement, eventTarget, scope);

    if (action) {
      return {
        action: action,
        interactable: match,
        element: matchElement
      };
    }
  }

  return {
    action: null,
    interactable: null,
    element: null
  };
}

function getActionInfo(interaction, pointer, event, eventTarget, scope) {
  var matches = [];
  var matchElements = [];
  var element = eventTarget;

  function pushMatches(interactable) {
    matches.push(interactable);
    matchElements.push(element);
  }

  while (__utils_9.is.element(element)) {
    matches = [];
    matchElements = [];
    scope.interactables.forEachMatch(element, pushMatches);
    var actionInfo = validateMatches(interaction, pointer, event, matches, matchElements, eventTarget, scope);

    if (actionInfo.action && !actionInfo.interactable.options[actionInfo.action.name].manualStart) {
      return actionInfo;
    }

    element = __utils_9.dom.parentNode(element);
  }

  return {
    action: null,
    interactable: null,
    element: null
  };
}

function prepare(interaction, _ref4, scope) {
  var action = _ref4.action,
      interactable = _ref4.interactable,
      element = _ref4.element;
  action = action || {};

  if (interaction.interactable && interaction.interactable.options.styleCursor) {
    setCursor(interaction.element, '', scope);
  }

  interaction.interactable = interactable;
  interaction.element = element;
  __utils_9.copyAction(interaction.prepared, action);
  interaction.rect = interactable && action.name ? interactable.getRect(element) : null;

  if (interaction.pointerType === 'mouse' && interactable && interactable.options.styleCursor) {
    var cursor = '';

    if (action) {
      var cursorChecker = interactable.options[action.name].cursorChecker;

      if (__utils_9.is.func(cursorChecker)) {
        cursor = cursorChecker(action, interactable, element);
      } else {
        cursor = scope.actions[action.name].getCursor(action);
      }
    }

    setCursor(interaction.element, cursor || '', scope);
  }

  scope.autoStart.signals.fire('prepared', {
    interaction: interaction
  });
}

function withinInteractionLimit(interactable, element, action, scope) {
  var options = interactable.options;
  var maxActions = options[action.name].max;
  var maxPerElement = options[action.name].maxPerElement;
  var autoStartMax = scope.autoStart.maxInteractions;
  var activeInteractions = 0;
  var interactableCount = 0;
  var elementCount = 0; // no actions if any of these values == 0

  if (!(maxActions && maxPerElement && autoStartMax)) {
    return false;
  }

  for (var _i = 0; _i < scope.interactions.list.length; _i++) {
    var _ref5;

    _ref5 = scope.interactions.list[_i];
    var interaction = _ref5;
    var otherAction = interaction.prepared.name;

    if (!interaction.interacting()) {
      continue;
    }

    activeInteractions++;

    if (activeInteractions >= autoStartMax) {
      return false;
    }

    if (interaction.interactable !== interactable) {
      continue;
    }

    interactableCount += otherAction === action.name ? 1 : 0;

    if (interactableCount >= maxActions) {
      return false;
    }

    if (interaction.element === element) {
      elementCount++;

      if (otherAction === action.name && elementCount >= maxPerElement) {
        return false;
      }
    }
  }

  return autoStartMax > 0;
}

function maxInteractions(newValue, scope) {
  if (__utils_9.is.number(newValue)) {
    scope.autoStart.maxInteractions = newValue;
    return this;
  }

  return scope.autoStart.maxInteractions;
}

function setCursor(element, cursor, scope) {
  if (scope.autoStart.cursorElement) {
    scope.autoStart.cursorElement.style.cursor = '';
  }

  element.ownerDocument.documentElement.style.cursor = cursor;
  element.style.cursor = cursor;
  scope.autoStart.cursorElement = cursor ? element : null;
}

var ___default_9 = {
  id: 'auto-start/base',
  install: __install_9,
  maxInteractions: maxInteractions,
  withinInteractionLimit: withinInteractionLimit,
  validateAction: validateAction
};
_$base_9["default"] = ___default_9;

var _$dragAxis_10 = {};
"use strict";

Object.defineProperty(_$dragAxis_10, "__esModule", {
  value: true
});
_$dragAxis_10["default"] = void 0;

var ___scope_10 = _$scope_24({});

/* removed: var _$domUtils_51 = require("@interactjs/utils/domUtils"); */;

var __is_10 = ___interopRequireWildcard_10(_$is_57);

var _base = ___interopRequireDefault_10(_$base_9);

function ___interopRequireDefault_10(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_10(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __install_10(scope) {
  scope.autoStart.signals.on('before-start', function (_ref) {
    var interaction = _ref.interaction,
        eventTarget = _ref.eventTarget,
        dx = _ref.dx,
        dy = _ref.dy;

    if (interaction.prepared.name !== 'drag') {
      return;
    } // check if a drag is in the correct axis


    var absX = Math.abs(dx);
    var absY = Math.abs(dy);
    var targetOptions = interaction.interactable.options.drag;
    var startAxis = targetOptions.startAxis;
    var currentAxis = absX > absY ? 'x' : absX < absY ? 'y' : 'xy';
    interaction.prepared.axis = targetOptions.lockAxis === 'start' ? currentAxis[0] // always lock to one axis even if currentAxis === 'xy'
    : targetOptions.lockAxis; // if the movement isn't in the startAxis of the interactable

    if (currentAxis !== 'xy' && startAxis !== 'xy' && startAxis !== currentAxis) {
      // cancel the prepared action
      interaction.prepared.name = null; // then try to get a drag from another ineractable

      var element = eventTarget;

      var getDraggable = function getDraggable(interactable) {
        if (interactable === interaction.interactable) {
          return;
        }

        var options = interaction.interactable.options.drag;

        if (!options.manualStart && interactable.testIgnoreAllow(options, element, eventTarget)) {
          var action = interactable.getAction(interaction.downPointer, interaction.downEvent, interaction, element);

          if (action && action.name === ___scope_10.ActionName.Drag && checkStartAxis(currentAxis, interactable) && _base["default"].validateAction(action, interactable, element, eventTarget, scope)) {
            return interactable;
          }
        }
      }; // check all interactables


      while (__is_10.element(element)) {
        var interactable = scope.interactables.forEachMatch(element, getDraggable);

        if (interactable) {
          interaction.prepared.name = ___scope_10.ActionName.Drag;
          interaction.interactable = interactable;
          interaction.element = element;
          break;
        }

        element = (0, _$domUtils_51.parentNode)(element);
      }
    }
  });

  function checkStartAxis(startAxis, interactable) {
    if (!interactable) {
      return false;
    }

    var thisAxis = interactable.options[___scope_10.ActionName.Drag].startAxis;
    return startAxis === 'xy' || thisAxis === 'xy' || thisAxis === startAxis;
  }
}

var ___default_10 = {
  id: 'auto-start/dragAxis',
  install: __install_10
};
_$dragAxis_10["default"] = ___default_10;

var _$hold_11 = {};
"use strict";

Object.defineProperty(_$hold_11, "__esModule", {
  value: true
});
_$hold_11["default"] = void 0;

var ___base_11 = ___interopRequireDefault_11(_$base_9);

function ___interopRequireDefault_11(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __install_11(scope) {
  var autoStart = scope.autoStart,
      interactions = scope.interactions,
      defaults = scope.defaults;
  scope.usePlugin(___base_11["default"]);
  defaults.perAction.hold = 0;
  defaults.perAction.delay = 0;
  interactions.signals.on('new', function (interaction) {
    interaction.autoStartHoldTimer = null;
  });
  autoStart.signals.on('prepared', function (_ref) {
    var interaction = _ref.interaction;
    var hold = getHoldDuration(interaction);

    if (hold > 0) {
      interaction.autoStartHoldTimer = setTimeout(function () {
        interaction.start(interaction.prepared, interaction.interactable, interaction.element);
      }, hold);
    }
  });
  interactions.signals.on('move', function (_ref2) {
    var interaction = _ref2.interaction,
        duplicate = _ref2.duplicate;

    if (interaction.pointerWasMoved && !duplicate) {
      clearTimeout(interaction.autoStartHoldTimer);
    }
  }); // prevent regular down->move autoStart

  autoStart.signals.on('before-start', function (_ref3) {
    var interaction = _ref3.interaction;
    var hold = getHoldDuration(interaction);

    if (hold > 0) {
      interaction.prepared.name = null;
    }
  });
}

function getHoldDuration(interaction) {
  var actionName = interaction.prepared && interaction.prepared.name;

  if (!actionName) {
    return null;
  }

  var options = interaction.interactable.options;
  return options[actionName].hold || options[actionName].delay;
}

var ___default_11 = {
  id: 'auto-start/hold',
  install: __install_11,
  getHoldDuration: getHoldDuration
};
_$hold_11["default"] = ___default_11;

var _$autoStart_12 = {};
"use strict";

Object.defineProperty(_$autoStart_12, "__esModule", {
  value: true
});
_$autoStart_12.install = __install_12;
Object.defineProperty(_$autoStart_12, "autoStart", {
  enumerable: true,
  get: function get() {
    return ___base_12["default"];
  }
});
Object.defineProperty(_$autoStart_12, "dragAxis", {
  enumerable: true,
  get: function get() {
    return _dragAxis["default"];
  }
});
Object.defineProperty(_$autoStart_12, "hold", {
  enumerable: true,
  get: function get() {
    return _hold["default"];
  }
});
_$autoStart_12.id = void 0;

var ___base_12 = ___interopRequireDefault_12(_$base_9);

var _dragAxis = ___interopRequireDefault_12(_$dragAxis_10);

var _hold = ___interopRequireDefault_12(_$hold_11);

function ___interopRequireDefault_12(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __install_12(scope) {
  scope.usePlugin(___base_12["default"]);
  scope.usePlugin(_hold["default"]);
  scope.usePlugin(_dragAxis["default"]);
}

var __id_12 = 'auto-start';
_$autoStart_12.id = __id_12;

var _$interactablePreventDefault_21 = {};
"use strict";

Object.defineProperty(_$interactablePreventDefault_21, "__esModule", {
  value: true
});
_$interactablePreventDefault_21.install = __install_21;
_$interactablePreventDefault_21["default"] = void 0;

/* removed: var _$domUtils_51 = require("@interactjs/utils/domUtils"); */;

var ___events_21 = ___interopRequireDefault_21(_$events_52);

var __is_21 = ___interopRequireWildcard_21(_$is_57);

/* removed: var _$window_66 = require("@interactjs/utils/window"); */;

function ___interopRequireWildcard_21(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_21(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function preventDefault(newValue) {
  if (/^(always|never|auto)$/.test(newValue)) {
    this.options.preventDefault = newValue;
    return this;
  }

  if (__is_21.bool(newValue)) {
    this.options.preventDefault = newValue ? 'always' : 'never';
    return this;
  }

  return this.options.preventDefault;
}

function checkAndPreventDefault(interactable, scope, event) {
  var setting = interactable.options.preventDefault;

  if (setting === 'never') {
    return;
  }

  if (setting === 'always') {
    event.preventDefault();
    return;
  } // setting === 'auto'
  // if the browser supports passive event listeners and isn't running on iOS,
  // don't preventDefault of touch{start,move} events. CSS touch-action and
  // user-select should be used instead of calling event.preventDefault().


  if (___events_21["default"].supportsPassive && /^touch(start|move)$/.test(event.type)) {
    var doc = (0, _$window_66.getWindow)(event.target).document;
    var docOptions = scope.getDocOptions(doc);

    if (!(docOptions && docOptions.events) || docOptions.events.passive !== false) {
      return;
    }
  } // don't preventDefault of pointerdown events


  if (/^(mouse|pointer|touch)*(down|start)/i.test(event.type)) {
    return;
  } // don't preventDefault on editable elements


  if (__is_21.element(event.target) && (0, _$domUtils_51.matchesSelector)(event.target, 'input,select,textarea,[contenteditable=true],[contenteditable=true] *')) {
    return;
  }

  event.preventDefault();
}

function onInteractionEvent(_ref) {
  var interaction = _ref.interaction,
      event = _ref.event;

  if (interaction.interactable) {
    interaction.interactable.checkAndPreventDefault(event);
  }
}

function __install_21(scope) {
  /** @lends Interactable */
  var Interactable = scope.Interactable;
  /**
   * Returns or sets whether to prevent the browser's default behaviour in
   * response to pointer events. Can be set to:
   *  - `'always'` to always prevent
   *  - `'never'` to never prevent
   *  - `'auto'` to let interact.js try to determine what would be best
   *
   * @param {string} [newValue] `'always'`, `'never'` or `'auto'`
   * @return {string | Interactable} The current setting or this Interactable
   */

  Interactable.prototype.preventDefault = preventDefault;

  Interactable.prototype.checkAndPreventDefault = function (event) {
    return checkAndPreventDefault(this, scope, event);
  };

  var _arr = ['down', 'move', 'up', 'cancel'];

  for (var _i = 0; _i < _arr.length; _i++) {
    var eventSignal = _arr[_i];
    scope.interactions.signals.on(eventSignal, onInteractionEvent);
  } // prevent native HTML5 drag on interact.js target elements


  scope.interactions.docEvents.push({
    type: 'dragstart',
    listener: function listener(event) {
      for (var _i2 = 0; _i2 < scope.interactions.list.length; _i2++) {
        var _ref2;

        _ref2 = scope.interactions.list[_i2];
        var interaction = _ref2;

        if (interaction.element && (interaction.element === event.target || (0, _$domUtils_51.nodeContains)(interaction.element, event.target))) {
          interaction.interactable.checkAndPreventDefault(event);
          return;
        }
      }
    }
  });
}

var ___default_21 = {
  id: 'core/interactablePreventDefault',
  install: __install_21
};
_$interactablePreventDefault_21["default"] = ___default_21;

var _$devTools_25 = {};
"use strict";

Object.defineProperty(_$devTools_25, "__esModule", {
  value: true
});
_$devTools_25["default"] = void 0;

var ___domObjects_25 = ___interopRequireDefault_25(_$domObjects_50);

/* removed: var _$domUtils_51 = require("@interactjs/utils/domUtils"); */;

var ___extend_25 = ___interopRequireDefault_25(_$extend_53);

var __is_25 = ___interopRequireWildcard_25(_$is_57);

var ___window_25 = ___interopRequireDefault_25(_$window_66);

function ___interopRequireWildcard_25(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_25(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___toConsumableArray_25(arr) { return ___arrayWithoutHoles_25(arr) || ___iterableToArray_25(arr) || ___nonIterableSpread_25(); }

function ___nonIterableSpread_25() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function ___iterableToArray_25(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function ___arrayWithoutHoles_25(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

var CheckName;

(function (CheckName) {
  CheckName["touchAction"] = "";
  CheckName["boxSizing"] = "";
  CheckName["noListeners"] = "";
})(CheckName || (CheckName = {}));

var prefix = '[interact.js] ';
var links = {
  touchAction: 'https://developer.mozilla.org/en-US/docs/Web/CSS/touch-action',
  boxSizing: 'https://developer.mozilla.org/en-US/docs/Web/CSS/box-sizing'
};
var isProduction = "production" === 'production'; // eslint-disable-next-line no-restricted-syntax

function __install_25(scope) {
  var _ref = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
      logger = _ref.logger;

  var interactions = scope.interactions,
      Interactable = scope.Interactable,
      defaults = scope.defaults;
  logger = logger || console;
  interactions.signals.on('action-start', function (_ref2) {
    var interaction = _ref2.interaction;

    for (var _i = 0; _i < checks.length; _i++) {
      var _ref3;

      _ref3 = checks[_i];
      var check = _ref3;
      var options = interaction.interactable && interaction.interactable.options[interaction.prepared.name];

      if (!(options && options.devTools && options.devTools.ignore[check.name]) && check.perform(interaction)) {
        var _logger;

        (_logger = logger).warn.apply(_logger, [prefix + check.text].concat(___toConsumableArray_25(check.getInfo(interaction))));
      }
    }
  });
  defaults.base.devTools = {
    ignore: {}
  };

  Interactable.prototype.devTools = function (options) {
    if (options) {
      (0, ___extend_25["default"])(this.options.devTools, options);
      return this;
    }

    return this.options.devTools;
  };
}

var checks = [{
  name: 'touchAction',
  perform: function perform(_ref4) {
    var element = _ref4.element;
    return !parentHasStyle(element, 'touchAction', /pan-|pinch|none/);
  },
  getInfo: function getInfo(_ref5) {
    var element = _ref5.element;
    return [element, links.touchAction];
  },
  text: 'Consider adding CSS "touch-action: none" to this element\n'
}, {
  name: 'boxSizing',
  perform: function perform(interaction) {
    var element = interaction.element;
    return interaction.prepared.name === 'resize' && element instanceof ___domObjects_25["default"].HTMLElement && !hasStyle(element, 'boxSizing', /border-box/);
  },
  text: 'Consider adding CSS "box-sizing: border-box" to this resizable element',
  getInfo: function getInfo(_ref6) {
    var element = _ref6.element;
    return [element, links.boxSizing];
  }
}, {
  name: 'noListeners',
  perform: function perform(interaction) {
    var actionName = interaction.prepared.name;
    var moveListeners = interaction.interactable.events.types["".concat(actionName, "move")] || [];
    return !moveListeners.length;
  },
  getInfo: function getInfo(interaction) {
    return [interaction.prepared.name, interaction.interactable];
  },
  text: 'There are no listeners set for this action'
}];

function hasStyle(element, prop, styleRe) {
  return styleRe.test(element.style[prop] || ___window_25["default"].window.getComputedStyle(element)[prop]);
}

function parentHasStyle(element, prop, styleRe) {
  var parent = element;

  while (__is_25.element(parent)) {
    if (hasStyle(parent, prop, styleRe)) {
      return true;
    }

    parent = (0, _$domUtils_51.parentNode)(parent);
  }

  return false;
}

var __id_25 = 'dev-tools';
var defaultExport = isProduction ? {
  id: __id_25,
  install: function install() {}
} : {
  id: __id_25,
  install: __install_25,
  checks: checks,
  CheckName: CheckName,
  links: links,
  prefix: prefix
};
var ___default_25 = defaultExport;
_$devTools_25["default"] = ___default_25;

var _$base_30 = {};
"use strict";

Object.defineProperty(_$base_30, "__esModule", {
  value: true
});
_$base_30.startAll = startAll;
_$base_30.setAll = setAll;
_$base_30.prepareStates = prepareStates;
_$base_30.makeModifier = makeModifier;
_$base_30["default"] = void 0;

var ___extend_30 = ___interopRequireDefault_30(_$extend_53);

function ___interopRequireDefault_30(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___slicedToArray_30(arr, i) { return ___arrayWithHoles_30(arr) || ___iterableToArrayLimit_30(arr, i) || ___nonIterableRest_30(); }

function ___nonIterableRest_30() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function ___iterableToArrayLimit_30(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function ___arrayWithHoles_30(arr) { if (Array.isArray(arr)) return arr; }

function __install_30(scope) {
  var interactions = scope.interactions;
  scope.defaults.perAction.modifiers = [];
  interactions.signals.on('new', function (_ref) {
    var interaction = _ref.interaction;
    interaction.modifiers = {
      startOffset: {
        left: 0,
        right: 0,
        top: 0,
        bottom: 0
      },
      offsets: {},
      states: null,
      result: null,
      endPrevented: false,
      startDelta: null
    };
  });
  interactions.signals.on('before-action-start', function (arg) {
    __start_30(arg, arg.interaction.coords.start.page);
  });
  interactions.signals.on('action-resume', function (arg) {
    stop(arg);
    __start_30(arg, arg.interaction.coords.cur.page);
    __beforeMove_30(arg);
  });
  interactions.signals.on('after-action-move', restoreCoords);
  interactions.signals.on('before-action-move', __beforeMove_30);
  interactions.signals.on('before-action-start', setCoords);
  interactions.signals.on('after-action-start', restoreCoords);
  interactions.signals.on('before-action-end', beforeEnd);
  interactions.signals.on('stop', stop);
}

function __start_30(_ref2, pageCoords) {
  var interaction = _ref2.interaction,
      phase = _ref2.phase;
  var interactable = interaction.interactable,
      element = interaction.element;
  var modifierList = getModifierList(interaction);
  var states = prepareStates(modifierList);
  var rect = (0, ___extend_30["default"])({}, interaction.rect);

  if (!('width' in rect)) {
    rect.width = rect.right - rect.left;
  }

  if (!('height' in rect)) {
    rect.height = rect.bottom - rect.top;
  }

  var startOffset = getRectOffset(rect, pageCoords);
  interaction.modifiers.startOffset = startOffset;
  interaction.modifiers.startDelta = {
    x: 0,
    y: 0
  };
  var arg = {
    interaction: interaction,
    interactable: interactable,
    element: element,
    pageCoords: pageCoords,
    phase: phase,
    rect: rect,
    startOffset: startOffset,
    states: states,
    preEnd: false,
    requireEndOnly: false
  };
  interaction.modifiers.states = states;
  interaction.modifiers.result = null;
  startAll(arg);
  arg.pageCoords = (0, ___extend_30["default"])({}, interaction.coords.start.page);
  var result = interaction.modifiers.result = setAll(arg);
  return result;
}

function startAll(arg) {
  var states = arg.states;

  for (var _i = 0; _i < states.length; _i++) {
    var _ref3;

    _ref3 = states[_i];
    var state = _ref3;

    if (state.methods.start) {
      arg.state = state;
      state.methods.start(arg);
    }
  }
}

function setAll(arg) {
  var interaction = arg.interaction,
      _arg$modifiersState = arg.modifiersState,
      modifiersState = _arg$modifiersState === void 0 ? interaction.modifiers : _arg$modifiersState,
      _arg$prevCoords = arg.prevCoords,
      prevCoords = _arg$prevCoords === void 0 ? modifiersState.result ? modifiersState.result.coords : interaction.coords.prev.page : _arg$prevCoords,
      phase = arg.phase,
      preEnd = arg.preEnd,
      requireEndOnly = arg.requireEndOnly,
      rect = arg.rect,
      skipModifiers = arg.skipModifiers;
  var states = skipModifiers ? arg.states.slice(skipModifiers) : arg.states;
  arg.coords = (0, ___extend_30["default"])({}, arg.pageCoords);
  arg.rect = (0, ___extend_30["default"])({}, rect);
  var result = {
    delta: {
      x: 0,
      y: 0
    },
    rectDelta: {
      left: 0,
      right: 0,
      top: 0,
      bottom: 0
    },
    coords: arg.coords,
    changed: true
  };

  for (var _i2 = 0; _i2 < states.length; _i2++) {
    var _ref4;

    _ref4 = states[_i2];
    var state = _ref4;
    var options = state.options;

    if (!state.methods.set || !shouldDo(options, preEnd, requireEndOnly, phase)) {
      continue;
    }

    arg.state = state;
    state.methods.set(arg);
  }

  result.delta.x = arg.coords.x - arg.pageCoords.x;
  result.delta.y = arg.coords.y - arg.pageCoords.y;
  var rectChanged = false;

  if (rect) {
    result.rectDelta.left = arg.rect.left - rect.left;
    result.rectDelta.right = arg.rect.right - rect.right;
    result.rectDelta.top = arg.rect.top - rect.top;
    result.rectDelta.bottom = arg.rect.bottom - rect.bottom;
    rectChanged = result.rectDelta.left !== 0 || result.rectDelta.right !== 0 || result.rectDelta.top !== 0 || result.rectDelta.bottom !== 0;
  }

  result.changed = prevCoords.x !== result.coords.x || prevCoords.y !== result.coords.y || rectChanged;
  return result;
}

function __beforeMove_30(arg) {
  var interaction = arg.interaction,
      phase = arg.phase,
      preEnd = arg.preEnd,
      skipModifiers = arg.skipModifiers;
  var interactable = interaction.interactable,
      element = interaction.element;
  var modifierResult = setAll({
    interaction: interaction,
    interactable: interactable,
    element: element,
    preEnd: preEnd,
    phase: phase,
    pageCoords: interaction.coords.cur.page,
    rect: interaction.rect,
    states: interaction.modifiers.states,
    requireEndOnly: false,
    skipModifiers: skipModifiers
  });
  interaction.modifiers.result = modifierResult; // don't fire an action move if a modifier would keep the event in the same
  // cordinates as before

  if (!modifierResult.changed && interaction.interacting()) {
    return false;
  }

  setCoords(arg);
}

function beforeEnd(arg) {
  var interaction = arg.interaction,
      event = arg.event,
      noPreEnd = arg.noPreEnd;
  var states = interaction.modifiers.states;

  if (noPreEnd || !states || !states.length) {
    return;
  }

  var didPreEnd = false;

  for (var _i3 = 0; _i3 < states.length; _i3++) {
    var _ref5;

    _ref5 = states[_i3];
    var state = _ref5;
    arg.state = state;
    var options = state.options,
        methods = state.methods;
    var endResult = methods.beforeEnd && methods.beforeEnd(arg);

    if (endResult === false) {
      interaction.modifiers.endPrevented = true;
      return false;
    } // if the endOnly option is true for any modifier


    if (!didPreEnd && shouldDo(options, true, true)) {
      // fire a move event at the modified coordinates
      interaction.move({
        event: event,
        preEnd: true
      });
      didPreEnd = true;
    }
  }
}

function stop(arg) {
  var interaction = arg.interaction;
  var states = interaction.modifiers.states;

  if (!states || !states.length) {
    return;
  }

  var modifierArg = (0, ___extend_30["default"])({
    states: states,
    interactable: interaction.interactable,
    element: interaction.element
  }, arg);
  restoreCoords(arg);

  for (var _i4 = 0; _i4 < states.length; _i4++) {
    var _ref6;

    _ref6 = states[_i4];
    var state = _ref6;
    modifierArg.state = state;

    if (state.methods.stop) {
      state.methods.stop(modifierArg);
    }
  }

  arg.interaction.modifiers.states = null;
  arg.interaction.modifiers.endPrevented = false;
}

function getModifierList(interaction) {
  var actionOptions = interaction.interactable.options[interaction.prepared.name];
  var actionModifiers = actionOptions.modifiers;

  if (actionModifiers && actionModifiers.length) {
    return actionModifiers.filter(function (modifier) {
      return !modifier.options || modifier.options.enabled !== false;
    });
  }

  return ['snap', 'snapSize', 'snapEdges', 'restrict', 'restrictEdges', 'restrictSize'].map(function (type) {
    var options = actionOptions[type];
    return options && options.enabled && {
      options: options,
      methods: options._methods
    };
  }).filter(function (m) {
    return !!m;
  });
}

function prepareStates(modifierList) {
  var states = [];

  for (var index = 0; index < modifierList.length; index++) {
    var _modifierList$index = modifierList[index],
        options = _modifierList$index.options,
        methods = _modifierList$index.methods,
        name = _modifierList$index.name;

    if (options && options.enabled === false) {
      continue;
    }

    states.push({
      options: options,
      methods: methods,
      index: index,
      name: name
    });
  }

  return states;
}

function setCoords(arg) {
  var interaction = arg.interaction,
      phase = arg.phase;
  var curCoords = arg.curCoords || interaction.coords.cur;
  var startCoords = arg.startCoords || interaction.coords.start;
  var _interaction$modifier = interaction.modifiers,
      result = _interaction$modifier.result,
      startDelta = _interaction$modifier.startDelta;
  var curDelta = result.delta;

  if (phase === 'start') {
    (0, ___extend_30["default"])(interaction.modifiers.startDelta, result.delta);
  }

  var _arr = [[startCoords, startDelta], [curCoords, curDelta]];

  for (var _i5 = 0; _i5 < _arr.length; _i5++) {
    var _arr$_i = ___slicedToArray_30(_arr[_i5], 2),
        coordsSet = _arr$_i[0],
        delta = _arr$_i[1];

    coordsSet.page.x += delta.x;
    coordsSet.page.y += delta.y;
    coordsSet.client.x += delta.x;
    coordsSet.client.y += delta.y;
  }

  var rectDelta = interaction.modifiers.result.rectDelta;
  var rect = arg.rect || interaction.rect;
  rect.left += rectDelta.left;
  rect.right += rectDelta.right;
  rect.top += rectDelta.top;
  rect.bottom += rectDelta.bottom;
  rect.width = rect.right - rect.left;
  rect.height = rect.bottom - rect.top;
}

function restoreCoords(_ref7) {
  var _ref7$interaction = _ref7.interaction,
      coords = _ref7$interaction.coords,
      rect = _ref7$interaction.rect,
      modifiers = _ref7$interaction.modifiers;

  if (!modifiers.result) {
    return;
  }

  var startDelta = modifiers.startDelta;
  var _modifiers$result = modifiers.result,
      curDelta = _modifiers$result.delta,
      rectDelta = _modifiers$result.rectDelta;
  var coordsAndDeltas = [[coords.start, startDelta], [coords.cur, curDelta]];

  for (var _i6 = 0; _i6 < coordsAndDeltas.length; _i6++) {
    var _coordsAndDeltas$_i = ___slicedToArray_30(coordsAndDeltas[_i6], 2),
        coordsSet = _coordsAndDeltas$_i[0],
        delta = _coordsAndDeltas$_i[1];

    coordsSet.page.x -= delta.x;
    coordsSet.page.y -= delta.y;
    coordsSet.client.x -= delta.x;
    coordsSet.client.y -= delta.y;
  }

  rect.left -= rectDelta.left;
  rect.right -= rectDelta.right;
  rect.top -= rectDelta.top;
  rect.bottom -= rectDelta.bottom;
}

function shouldDo(options, preEnd, requireEndOnly, phase) {
  return options ? options.enabled !== false && (preEnd || !options.endOnly) && (!requireEndOnly || options.endOnly || options.alwaysOnEnd) && (options.setStart || phase !== 'start') : !requireEndOnly;
}

function getRectOffset(rect, coords) {
  return rect ? {
    left: coords.x - rect.left,
    top: coords.y - rect.top,
    right: rect.right - coords.x,
    bottom: rect.bottom - coords.y
  } : {
    left: 0,
    top: 0,
    right: 0,
    bottom: 0
  };
}

function makeModifier(module, name) {
  var defaults = module.defaults;
  var methods = {
    start: module.start,
    set: module.set,
    beforeEnd: module.beforeEnd,
    stop: module.stop
  };

  var modifier = function modifier(_options) {
    var options = _options || {};
    options.enabled = options.enabled !== false; // add missing defaults to options

    for (var prop in defaults) {
      if (!(prop in options)) {
        options[prop] = defaults[prop];
      }
    }

    var m = {
      options: options,
      methods: methods,
      name: name
    };
    return m;
  };

  if (name && typeof name === 'string') {
    // for backwrads compatibility
    modifier._defaults = defaults;
    modifier._methods = methods;
  }

  return modifier;
}

var ___default_30 = {
  id: 'modifiers/base',
  install: __install_30,
  startAll: startAll,
  setAll: setAll,
  prepareStates: prepareStates,
  start: __start_30,
  beforeMove: __beforeMove_30,
  beforeEnd: beforeEnd,
  stop: stop,
  shouldDo: shouldDo,
  getModifierList: getModifierList,
  getRectOffset: getRectOffset,
  makeModifier: makeModifier
};
_$base_30["default"] = ___default_30;

var _$inertia_26 = {};
"use strict";

Object.defineProperty(_$inertia_26, "__esModule", {
  value: true
});
_$inertia_26["default"] = void 0;

/* removed: var _$InteractEvent_15 = require("@interactjs/core/InteractEvent"); */;

var ___base_26 = ___interopRequireDefault_26(_$base_30);

var __utils_26 = ___interopRequireWildcard_26(_$utils_56);

var ___raf_26 = ___interopRequireDefault_26(_$raf_62);

function ___interopRequireWildcard_26(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_26(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

_$InteractEvent_15.EventPhase.Resume = 'resume';
_$InteractEvent_15.EventPhase.InertiaStart = 'inertiastart';

function __install_26(scope) {
  var interactions = scope.interactions,
      defaults = scope.defaults;
  interactions.signals.on('new', function (_ref) {
    var interaction = _ref.interaction;
    interaction.inertia = {
      active: false,
      smoothEnd: false,
      allowResume: false,
      upCoords: {},
      timeout: null
    };
  }); // FIXME proper signal typing

  interactions.signals.on('before-action-end', function (arg) {
    return release(arg, scope);
  });
  interactions.signals.on('down', function (arg) {
    return resume(arg, scope);
  });
  interactions.signals.on('stop', function (arg) {
    return __stop_26(arg);
  });
  defaults.perAction.inertia = {
    enabled: false,
    resistance: 10,
    minSpeed: 100,
    endSpeed: 10,
    allowResume: true,
    smoothEndDuration: 300
  };
  scope.usePlugin(___base_26["default"]);
}

function resume(_ref2, scope) {
  var interaction = _ref2.interaction,
      event = _ref2.event,
      pointer = _ref2.pointer,
      eventTarget = _ref2.eventTarget;
  var state = interaction.inertia; // Check if the down event hits the current inertia target

  if (state.active) {
    var element = eventTarget; // climb up the DOM tree from the event target

    while (__utils_26.is.element(element)) {
      // if interaction element is the current inertia target element
      if (element === interaction.element) {
        // stop inertia
        ___raf_26["default"].cancel(state.timeout);

        state.active = false;
        interaction.simulation = null; // update pointers to the down event's coordinates

        interaction.updatePointer(pointer, event, eventTarget, true);
        __utils_26.pointer.setCoords(interaction.coords.cur, interaction.pointers.map(function (p) {
          return p.pointer;
        }), interaction._now()); // fire appropriate signals

        var signalArg = {
          interaction: interaction
        };
        scope.interactions.signals.fire('action-resume', signalArg); // fire a reume event

        var resumeEvent = new scope.InteractEvent(interaction, event, interaction.prepared.name, _$InteractEvent_15.EventPhase.Resume, interaction.element);

        interaction._fireEvent(resumeEvent);

        __utils_26.pointer.copyCoords(interaction.coords.prev, interaction.coords.cur);
        break;
      }

      element = __utils_26.dom.parentNode(element);
    }
  }
}

function release(_ref3, scope) {
  var interaction = _ref3.interaction,
      event = _ref3.event,
      noPreEnd = _ref3.noPreEnd;
  var state = interaction.inertia;

  if (!interaction.interacting() || interaction.simulation && interaction.simulation.active || noPreEnd) {
    return null;
  }

  var options = __getOptions_26(interaction);

  var now = interaction._now();

  var velocityClient = interaction.coords.velocity.client;
  var pointerSpeed = __utils_26.hypot(velocityClient.x, velocityClient.y);
  var smoothEnd = false;
  var modifierResult; // check if inertia should be started

  var inertiaPossible = options && options.enabled && interaction.prepared.name !== 'gesture' && event !== state.startEvent;
  var inertia = inertiaPossible && now - interaction.coords.cur.timeStamp < 50 && pointerSpeed > options.minSpeed && pointerSpeed > options.endSpeed;
  var modifierArg = {
    interaction: interaction,
    pageCoords: __utils_26.extend({}, interaction.coords.cur.page),
    states: inertiaPossible && interaction.modifiers.states.map(function (modifierStatus) {
      return __utils_26.extend({}, modifierStatus);
    }),
    preEnd: true,
    prevCoords: undefined,
    requireEndOnly: null
  }; // smoothEnd

  if (inertiaPossible && !inertia) {
    modifierArg.prevCoords = interaction.prevEvent.page;
    modifierArg.requireEndOnly = false;
    modifierResult = ___base_26["default"].setAll(modifierArg);

    if (modifierResult.changed) {
      smoothEnd = true;
    }
  }

  if (!(inertia || smoothEnd)) {
    return null;
  }

  __utils_26.pointer.copyCoords(state.upCoords, interaction.coords.cur);
  interaction.pointers[0].pointer = state.startEvent = new scope.InteractEvent(interaction, event, // FIXME add proper typing Action.name
  interaction.prepared.name, _$InteractEvent_15.EventPhase.InertiaStart, interaction.element);
  state.t0 = now;
  state.active = true;
  state.allowResume = options.allowResume;
  interaction.simulation = state;
  interaction.interactable.fire(state.startEvent);

  if (inertia) {
    state.vx0 = interaction.coords.velocity.client.x;
    state.vy0 = interaction.coords.velocity.client.y;
    state.v0 = pointerSpeed;
    calcInertia(interaction, state);
    __utils_26.extend(modifierArg.pageCoords, interaction.coords.cur.page);
    modifierArg.pageCoords.x += state.xe;
    modifierArg.pageCoords.y += state.ye;
    modifierArg.prevCoords = undefined;
    modifierArg.requireEndOnly = true;
    modifierResult = ___base_26["default"].setAll(modifierArg);
    state.modifiedXe += modifierResult.delta.x;
    state.modifiedYe += modifierResult.delta.y;
    state.timeout = ___raf_26["default"].request(function () {
      return inertiaTick(interaction);
    });
  } else {
    state.smoothEnd = true;
    state.xe = modifierResult.delta.x;
    state.ye = modifierResult.delta.y;
    state.sx = state.sy = 0;
    state.timeout = ___raf_26["default"].request(function () {
      return smothEndTick(interaction);
    });
  }

  return false;
}

function __stop_26(_ref4) {
  var interaction = _ref4.interaction;
  var state = interaction.inertia;

  if (state.active) {
    ___raf_26["default"].cancel(state.timeout);

    state.active = false;
    interaction.simulation = null;
  }
}

function calcInertia(interaction, state) {
  var options = __getOptions_26(interaction);
  var lambda = options.resistance;
  var inertiaDur = -Math.log(options.endSpeed / state.v0) / lambda;
  state.x0 = interaction.prevEvent.page.x;
  state.y0 = interaction.prevEvent.page.y;
  state.t0 = state.startEvent.timeStamp / 1000;
  state.sx = state.sy = 0;
  state.modifiedXe = state.xe = (state.vx0 - inertiaDur) / lambda;
  state.modifiedYe = state.ye = (state.vy0 - inertiaDur) / lambda;
  state.te = inertiaDur;
  state.lambda_v0 = lambda / state.v0;
  state.one_ve_v0 = 1 - options.endSpeed / state.v0;
}

function inertiaTick(interaction) {
  updateInertiaCoords(interaction);
  __utils_26.pointer.setCoordDeltas(interaction.coords.delta, interaction.coords.prev, interaction.coords.cur);
  __utils_26.pointer.setCoordVelocity(interaction.coords.velocity, interaction.coords.delta);
  var state = interaction.inertia;
  var options = __getOptions_26(interaction);
  var lambda = options.resistance;
  var t = interaction._now() / 1000 - state.t0;

  if (t < state.te) {
    var progress = 1 - (Math.exp(-lambda * t) - state.lambda_v0) / state.one_ve_v0;

    if (state.modifiedXe === state.xe && state.modifiedYe === state.ye) {
      state.sx = state.xe * progress;
      state.sy = state.ye * progress;
    } else {
      var quadPoint = __utils_26.getQuadraticCurvePoint(0, 0, state.xe, state.ye, state.modifiedXe, state.modifiedYe, progress);
      state.sx = quadPoint.x;
      state.sy = quadPoint.y;
    }

    interaction.move();
    state.timeout = ___raf_26["default"].request(function () {
      return inertiaTick(interaction);
    });
  } else {
    state.sx = state.modifiedXe;
    state.sy = state.modifiedYe;
    interaction.move();
    interaction.end(state.startEvent);
    state.active = false;
    interaction.simulation = null;
  }

  __utils_26.pointer.copyCoords(interaction.coords.prev, interaction.coords.cur);
}

function smothEndTick(interaction) {
  updateInertiaCoords(interaction);
  var state = interaction.inertia;
  var t = interaction._now() - state.t0;

  var _getOptions = __getOptions_26(interaction),
      duration = _getOptions.smoothEndDuration;

  if (t < duration) {
    state.sx = __utils_26.easeOutQuad(t, 0, state.xe, duration);
    state.sy = __utils_26.easeOutQuad(t, 0, state.ye, duration);
    interaction.move();
    state.timeout = ___raf_26["default"].request(function () {
      return smothEndTick(interaction);
    });
  } else {
    state.sx = state.xe;
    state.sy = state.ye;
    interaction.move();
    interaction.end(state.startEvent);
    state.smoothEnd = state.active = false;
    interaction.simulation = null;
  }
}

function updateInertiaCoords(interaction) {
  var state = interaction.inertia; // return if inertia isn't running

  if (!state.active) {
    return;
  }

  var pageUp = state.upCoords.page;
  var clientUp = state.upCoords.client;
  __utils_26.pointer.setCoords(interaction.coords.cur, [{
    pageX: pageUp.x + state.sx,
    pageY: pageUp.y + state.sy,
    clientX: clientUp.x + state.sx,
    clientY: clientUp.y + state.sy
  }], interaction._now());
}

function __getOptions_26(_ref5) {
  var interactable = _ref5.interactable,
      prepared = _ref5.prepared;
  return interactable && interactable.options && prepared.name && interactable.options[prepared.name].inertia;
}

var ___default_26 = {
  id: 'inertia',
  install: __install_26,
  calcInertia: calcInertia,
  inertiaTick: inertiaTick,
  smothEndTick: smothEndTick,
  updateInertiaCoords: updateInertiaCoords
};
_$inertia_26["default"] = ___default_26;

var _$pointer_33 = {};
"use strict";

Object.defineProperty(_$pointer_33, "__esModule", {
  value: true
});
_$pointer_33["default"] = void 0;

var ___extend_33 = ___interopRequireDefault_33(_$extend_53);

var __is_33 = ___interopRequireWildcard_33(_$is_57);

var ___rect_33 = ___interopRequireDefault_33(_$rect_63);

function ___interopRequireWildcard_33(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_33(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __start_33(_ref) {
  var rect = _ref.rect,
      startOffset = _ref.startOffset,
      state = _ref.state,
      interaction = _ref.interaction,
      pageCoords = _ref.pageCoords;
  var options = state.options;
  var elementRect = options.elementRect;
  var offset = (0, ___extend_33["default"])({
    left: 0,
    top: 0,
    right: 0,
    bottom: 0
  }, options.offset || {});

  if (rect && elementRect) {
    var restriction = getRestrictionRect(options.restriction, interaction, pageCoords);

    if (restriction) {
      var widthDiff = restriction.right - restriction.left - rect.width;
      var heightDiff = restriction.bottom - restriction.top - rect.height;

      if (widthDiff < 0) {
        offset.left += widthDiff;
        offset.right += widthDiff;
      }

      if (heightDiff < 0) {
        offset.top += heightDiff;
        offset.bottom += heightDiff;
      }
    }

    offset.left += startOffset.left - rect.width * elementRect.left;
    offset.top += startOffset.top - rect.height * elementRect.top;
    offset.right += startOffset.right - rect.width * (1 - elementRect.right);
    offset.bottom += startOffset.bottom - rect.height * (1 - elementRect.bottom);
  }

  state.offset = offset;
}

function set(_ref2) {
  var coords = _ref2.coords,
      interaction = _ref2.interaction,
      state = _ref2.state;
  var options = state.options,
      offset = state.offset;
  var restriction = getRestrictionRect(options.restriction, interaction, coords);

  if (!restriction) {
    return;
  }

  var rect = ___rect_33["default"].xywhToTlbr(restriction);

  coords.x = Math.max(Math.min(rect.right - offset.right, coords.x), rect.left + offset.left);
  coords.y = Math.max(Math.min(rect.bottom - offset.bottom, coords.y), rect.top + offset.top);
}

function getRestrictionRect(value, interaction, coords) {
  if (__is_33.func(value)) {
    return ___rect_33["default"].resolveRectLike(value, interaction.interactable, interaction.element, [coords.x, coords.y, interaction]);
  } else {
    return ___rect_33["default"].resolveRectLike(value, interaction.interactable, interaction.element);
  }
}

var __defaults_33 = {
  restriction: null,
  elementRect: null,
  offset: null,
  endOnly: false,
  enabled: false
};
var restrict = {
  start: __start_33,
  set: set,
  getRestrictionRect: getRestrictionRect,
  defaults: __defaults_33
};
var ___default_33 = restrict;
_$pointer_33["default"] = ___default_33;

var _$edges_32 = {};
"use strict";

Object.defineProperty(_$edges_32, "__esModule", {
  value: true
});
_$edges_32["default"] = void 0;

var ___extend_32 = ___interopRequireDefault_32(_$extend_53);

var ___rect_32 = ___interopRequireDefault_32(_$rect_63);

var _pointer = ___interopRequireDefault_32(_$pointer_33);

function ___interopRequireDefault_32(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

// This module adds the options.resize.restrictEdges setting which sets min and
// max for the top, left, bottom and right edges of the target being resized.
//
// interact(target).resize({
//   edges: { top: true, left: true },
//   restrictEdges: {
//     inner: { top: 200, left: 200, right: 400, bottom: 400 },
//     outer: { top:   0, left:   0, right: 600, bottom: 600 },
//   },
// })
var __getRestrictionRect_32 = _pointer["default"].getRestrictionRect;
var noInner = {
  top: +Infinity,
  left: +Infinity,
  bottom: -Infinity,
  right: -Infinity
};
var noOuter = {
  top: -Infinity,
  left: -Infinity,
  bottom: +Infinity,
  right: +Infinity
};

function __start_32(_ref) {
  var interaction = _ref.interaction,
      state = _ref.state;
  var options = state.options;
  var startOffset = interaction.modifiers.startOffset;
  var offset;

  if (options) {
    var offsetRect = __getRestrictionRect_32(options.offset, interaction, interaction.coords.start.page);
    offset = ___rect_32["default"].rectToXY(offsetRect);
  }

  offset = offset || {
    x: 0,
    y: 0
  };
  state.offset = {
    top: offset.y + startOffset.top,
    left: offset.x + startOffset.left,
    bottom: offset.y - startOffset.bottom,
    right: offset.x - startOffset.right
  };
}

function __set_32(_ref2) {
  var coords = _ref2.coords,
      interaction = _ref2.interaction,
      state = _ref2.state;
  var offset = state.offset,
      options = state.options;
  var edges = interaction.prepared._linkedEdges || interaction.prepared.edges;

  if (!edges) {
    return;
  }

  var page = (0, ___extend_32["default"])({}, coords);
  var inner = __getRestrictionRect_32(options.inner, interaction, page) || {};
  var outer = __getRestrictionRect_32(options.outer, interaction, page) || {};
  fixRect(inner, noInner);
  fixRect(outer, noOuter);

  if (edges.top) {
    coords.y = Math.min(Math.max(outer.top + offset.top, page.y), inner.top + offset.top);
  } else if (edges.bottom) {
    coords.y = Math.max(Math.min(outer.bottom + offset.bottom, page.y), inner.bottom + offset.bottom);
  }

  if (edges.left) {
    coords.x = Math.min(Math.max(outer.left + offset.left, page.x), inner.left + offset.left);
  } else if (edges.right) {
    coords.x = Math.max(Math.min(outer.right + offset.right, page.x), inner.right + offset.right);
  }
}

function fixRect(rect, defaults) {
  var _arr = ['top', 'left', 'bottom', 'right'];

  for (var _i = 0; _i < _arr.length; _i++) {
    var edge = _arr[_i];

    if (!(edge in rect)) {
      rect[edge] = defaults[edge];
    }
  }

  return rect;
}

var __defaults_32 = {
  inner: null,
  outer: null,
  offset: null,
  endOnly: false,
  enabled: false
};
var restrictEdges = {
  noInner: noInner,
  noOuter: noOuter,
  getRestrictionRect: __getRestrictionRect_32,
  start: __start_32,
  set: __set_32,
  defaults: __defaults_32
};
var ___default_32 = restrictEdges;
_$edges_32["default"] = ___default_32;

var _$rect_34 = {};
"use strict";

Object.defineProperty(_$rect_34, "__esModule", {
  value: true
});
_$rect_34["default"] = void 0;

var ___extend_34 = ___interopRequireDefault_34(_$extend_53);

var ___pointer_34 = ___interopRequireDefault_34(_$pointer_33);

function ___interopRequireDefault_34(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var __defaults_34 = (0, ___extend_34["default"])({
  get elementRect() {
    return {
      top: 0,
      left: 0,
      bottom: 1,
      right: 1
    };
  },

  set elementRect(_) {}

}, ___pointer_34["default"].defaults);
var restrictRect = {
  start: ___pointer_34["default"].start,
  set: ___pointer_34["default"].set,
  defaults: __defaults_34
};
var ___default_34 = restrictRect;
_$rect_34["default"] = ___default_34;

var _$size_35 = {};
"use strict";

Object.defineProperty(_$size_35, "__esModule", {
  value: true
});
_$size_35["default"] = void 0;

var ___extend_35 = ___interopRequireDefault_35(_$extend_53);

var ___rect_35 = ___interopRequireDefault_35(_$rect_63);

var _edges = ___interopRequireDefault_35(_$edges_32);

function ___interopRequireDefault_35(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var noMin = {
  width: -Infinity,
  height: -Infinity
};
var noMax = {
  width: +Infinity,
  height: +Infinity
};

function __start_35(arg) {
  return _edges["default"].start(arg);
}

function __set_35(arg) {
  var interaction = arg.interaction,
      state = arg.state;
  var options = state.options;
  var edges = interaction.prepared._linkedEdges || interaction.prepared.edges;

  if (!edges) {
    return;
  }

  var rect = ___rect_35["default"].xywhToTlbr(interaction.resizeRects.inverted);

  var minSize = ___rect_35["default"].tlbrToXywh(_edges["default"].getRestrictionRect(options.min, interaction, arg.coords)) || noMin;
  var maxSize = ___rect_35["default"].tlbrToXywh(_edges["default"].getRestrictionRect(options.max, interaction, arg.coords)) || noMax;
  state.options = {
    endOnly: options.endOnly,
    inner: (0, ___extend_35["default"])({}, _edges["default"].noInner),
    outer: (0, ___extend_35["default"])({}, _edges["default"].noOuter)
  };

  if (edges.top) {
    state.options.inner.top = rect.bottom - minSize.height;
    state.options.outer.top = rect.bottom - maxSize.height;
  } else if (edges.bottom) {
    state.options.inner.bottom = rect.top + minSize.height;
    state.options.outer.bottom = rect.top + maxSize.height;
  }

  if (edges.left) {
    state.options.inner.left = rect.right - minSize.width;
    state.options.outer.left = rect.right - maxSize.width;
  } else if (edges.right) {
    state.options.inner.right = rect.left + minSize.width;
    state.options.outer.right = rect.left + maxSize.width;
  }

  _edges["default"].set(arg);

  state.options = options;
}

var __defaults_35 = {
  min: null,
  max: null,
  endOnly: false,
  enabled: false
};
var restrictSize = {
  start: __start_35,
  set: __set_35,
  defaults: __defaults_35
};
var ___default_35 = restrictSize;
_$size_35["default"] = ___default_35;

var _$pointer_37 = {};
"use strict";

Object.defineProperty(_$pointer_37, "__esModule", {
  value: true
});
_$pointer_37["default"] = void 0;

var __utils_37 = ___interopRequireWildcard_37(_$utils_56);

function ___interopRequireWildcard_37(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __start_37(arg) {
  var interaction = arg.interaction,
      interactable = arg.interactable,
      element = arg.element,
      rect = arg.rect,
      state = arg.state,
      startOffset = arg.startOffset;
  var options = state.options;
  var offsets = [];
  var origin = options.offsetWithOrigin ? getOrigin(arg) : {
    x: 0,
    y: 0
  };
  var snapOffset;

  if (options.offset === 'startCoords') {
    snapOffset = {
      x: interaction.coords.start.page.x,
      y: interaction.coords.start.page.y
    };
  } else {
    var offsetRect = __utils_37.rect.resolveRectLike(options.offset, interactable, element, [interaction]);
    snapOffset = __utils_37.rect.rectToXY(offsetRect) || {
      x: 0,
      y: 0
    };
    snapOffset.x += origin.x;
    snapOffset.y += origin.y;
  }

  var relativePoints = options.relativePoints || [];

  if (rect && options.relativePoints && options.relativePoints.length) {
    for (var index = 0; index < relativePoints.length; index++) {
      var relativePoint = relativePoints[index];
      offsets.push({
        index: index,
        relativePoint: relativePoint,
        x: startOffset.left - rect.width * relativePoint.x + snapOffset.x,
        y: startOffset.top - rect.height * relativePoint.y + snapOffset.y
      });
    }
  } else {
    offsets.push(__utils_37.extend({
      index: 0,
      relativePoint: null
    }, snapOffset));
  }

  state.offsets = offsets;
}

function __set_37(arg) {
  var interaction = arg.interaction,
      coords = arg.coords,
      state = arg.state;
  var options = state.options,
      offsets = state.offsets;
  var origin = __utils_37.getOriginXY(interaction.interactable, interaction.element, interaction.prepared.name);
  var page = __utils_37.extend({}, coords);
  var targets = [];
  var target;

  if (!options.offsetWithOrigin) {
    page.x -= origin.x;
    page.y -= origin.y;
  }

  state.realX = page.x;
  state.realY = page.y;

  for (var _i = 0; _i < offsets.length; _i++) {
    var _ref;

    _ref = offsets[_i];
    var offset = _ref;
    var relativeX = page.x - offset.x;
    var relativeY = page.y - offset.y;

    for (var index = 0, _len = options.targets.length; index < _len; index++) {
      var snapTarget = options.targets[index];

      if (__utils_37.is.func(snapTarget)) {
        target = snapTarget(relativeX, relativeY, interaction, offset, index);
      } else {
        target = snapTarget;
      }

      if (!target) {
        continue;
      }

      targets.push({
        x: (__utils_37.is.number(target.x) ? target.x : relativeX) + offset.x,
        y: (__utils_37.is.number(target.y) ? target.y : relativeY) + offset.y,
        range: __utils_37.is.number(target.range) ? target.range : options.range
      });
    }
  }

  var closest = {
    target: null,
    inRange: false,
    distance: 0,
    range: 0,
    dx: 0,
    dy: 0
  };

  for (var i = 0, len = targets.length; i < len; i++) {
    target = targets[i];
    var range = target.range;
    var dx = target.x - page.x;
    var dy = target.y - page.y;
    var distance = __utils_37.hypot(dx, dy);
    var inRange = distance <= range; // Infinite targets count as being out of range
    // compared to non infinite ones that are in range

    if (range === Infinity && closest.inRange && closest.range !== Infinity) {
      inRange = false;
    }

    if (!closest.target || (inRange // is the closest target in range?
    ? closest.inRange && range !== Infinity // the pointer is relatively deeper in this target
    ? distance / range < closest.distance / closest.range // this target has Infinite range and the closest doesn't
    : range === Infinity && closest.range !== Infinity || // OR this target is closer that the previous closest
    distance < closest.distance : // The other is not in range and the pointer is closer to this target
    !closest.inRange && distance < closest.distance)) {
      closest.target = target;
      closest.distance = distance;
      closest.range = range;
      closest.inRange = inRange;
      closest.dx = dx;
      closest.dy = dy;
      state.range = range;
    }
  }

  if (closest.inRange) {
    coords.x = closest.target.x;
    coords.y = closest.target.y;
  }

  state.closest = closest;
}

function getOrigin(arg) {
  var optionsOrigin = __utils_37.rect.rectToXY(__utils_37.rect.resolveRectLike(arg.state.options.origin, [arg.interaction.element]));
  var origin = optionsOrigin || __utils_37.getOriginXY(arg.interactable, arg.interaction.element, arg.interaction.prepared.name);
  return origin;
}

var __defaults_37 = {
  range: Infinity,
  targets: null,
  offset: null,
  offsetWithOrigin: true,
  origin: null,
  relativePoints: null,
  endOnly: false,
  enabled: false
};
var snap = {
  start: __start_37,
  set: __set_37,
  defaults: __defaults_37
};
var ___default_37 = snap;
_$pointer_37["default"] = ___default_37;

var _$size_38 = {};
"use strict";

Object.defineProperty(_$size_38, "__esModule", {
  value: true
});
_$size_38["default"] = void 0;

var ___extend_38 = ___interopRequireDefault_38(_$extend_53);

var __is_38 = ___interopRequireWildcard_38(_$is_57);

var ___pointer_38 = ___interopRequireDefault_38(_$pointer_37);

function ___interopRequireWildcard_38(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___interopRequireDefault_38(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___slicedToArray_38(arr, i) { return ___arrayWithHoles_38(arr) || ___iterableToArrayLimit_38(arr, i) || ___nonIterableRest_38(); }

function ___nonIterableRest_38() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function ___iterableToArrayLimit_38(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function ___arrayWithHoles_38(arr) { if (Array.isArray(arr)) return arr; }

function __start_38(arg) {
  var interaction = arg.interaction,
      state = arg.state;
  var options = state.options;
  var edges = interaction.prepared.edges;

  if (!edges) {
    return null;
  }

  arg.state = {
    options: {
      targets: null,
      relativePoints: [{
        x: edges.left ? 0 : 1,
        y: edges.top ? 0 : 1
      }],
      offset: options.offset || 'self',
      origin: {
        x: 0,
        y: 0
      },
      range: options.range
    }
  };
  state.targetFields = state.targetFields || [['width', 'height'], ['x', 'y']];

  ___pointer_38["default"].start(arg);

  state.offsets = arg.state.offsets;
  arg.state = state;
}

function __set_38(arg) {
  var interaction = arg.interaction,
      state = arg.state,
      coords = arg.coords;
  var options = state.options,
      offsets = state.offsets;
  var relative = {
    x: coords.x - offsets[0].x,
    y: coords.y - offsets[0].y
  };
  state.options = (0, ___extend_38["default"])({}, options);
  state.options.targets = [];

  for (var _i = 0; _i < (options.targets || []).length; _i++) {
    var _ref;

    _ref = (options.targets || [])[_i];
    var snapTarget = _ref;
    var target = void 0;

    if (__is_38.func(snapTarget)) {
      target = snapTarget(relative.x, relative.y, interaction);
    } else {
      target = snapTarget;
    }

    if (!target) {
      continue;
    }

    for (var _i2 = 0; _i2 < state.targetFields.length; _i2++) {
      var _ref2;

      _ref2 = state.targetFields[_i2];

      var _ref3 = _ref2,
          _ref4 = ___slicedToArray_38(_ref3, 2),
          xField = _ref4[0],
          yField = _ref4[1];

      if (xField in target || yField in target) {
        target.x = target[xField];
        target.y = target[yField];
        break;
      }
    }

    state.options.targets.push(target);
  }

  ___pointer_38["default"].set(arg);

  state.options = options;
}

var __defaults_38 = {
  range: Infinity,
  targets: null,
  offset: null,
  endOnly: false,
  enabled: false
};
var snapSize = {
  start: __start_38,
  set: __set_38,
  defaults: __defaults_38
};
var ___default_38 = snapSize;
_$size_38["default"] = ___default_38;

var _$edges_36 = {};
"use strict";

Object.defineProperty(_$edges_36, "__esModule", {
  value: true
});
_$edges_36["default"] = void 0;

var ___clone_36 = ___interopRequireDefault_36(_$clone_49);

var ___extend_36 = ___interopRequireDefault_36(_$extend_53);

var _size = ___interopRequireDefault_36(_$size_38);

function ___interopRequireDefault_36(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

/**
 * @module modifiers/snapEdges
 *
 * @description
 * This module allows snapping of the edges of targets during resize
 * interactions.
 *
 * @example
 * interact(target).resizable({
 *   snapEdges: {
 *     targets: [interact.snappers.grid({ x: 100, y: 50 })],
 *   },
 * })
 *
 * interact(target).resizable({
 *   snapEdges: {
 *     targets: [
 *       interact.snappers.grid({
 *        top: 50,
 *        left: 50,
 *        bottom: 100,
 *        right: 100,
 *       }),
 *     ],
 *   },
 * })
 */
function __start_36(arg) {
  var edges = arg.interaction.prepared.edges;

  if (!edges) {
    return null;
  }

  arg.state.targetFields = arg.state.targetFields || [[edges.left ? 'left' : 'right', edges.top ? 'top' : 'bottom']];
  return _size["default"].start(arg);
}

function __set_36(arg) {
  return _size["default"].set(arg);
}

var snapEdges = {
  start: __start_36,
  set: __set_36,
  defaults: (0, ___extend_36["default"])((0, ___clone_36["default"])(_size["default"].defaults), {
    offset: {
      x: 0,
      y: 0
    }
  })
};
var ___default_36 = snapEdges;
_$edges_36["default"] = ___default_36;

var _$modifiers_31 = {};
"use strict";

Object.defineProperty(_$modifiers_31, "__esModule", {
  value: true
});
_$modifiers_31.restrictSize = _$modifiers_31.restrictEdges = _$modifiers_31.restrictRect = _$modifiers_31.restrict = _$modifiers_31.snapEdges = _$modifiers_31.snapSize = _$modifiers_31.snap = void 0;

var ___base_31 = ___interopRequireDefault_31(_$base_30);

var ___edges_31 = ___interopRequireDefault_31(_$edges_32);

var ___pointer_31 = ___interopRequireDefault_31(_$pointer_33);

var ___rect_31 = ___interopRequireDefault_31(_$rect_34);

var ___size_31 = ___interopRequireDefault_31(_$size_35);

var _edges2 = ___interopRequireDefault_31(_$edges_36);

var _pointer2 = ___interopRequireDefault_31(_$pointer_37);

var _size2 = ___interopRequireDefault_31(_$size_38);

function ___interopRequireDefault_31(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var __makeModifier_31 = ___base_31["default"].makeModifier;
var __snap_31 = __makeModifier_31(_pointer2["default"], 'snap');
_$modifiers_31.snap = __snap_31;
var __snapSize_31 = __makeModifier_31(_size2["default"], 'snapSize');
_$modifiers_31.snapSize = __snapSize_31;
var __snapEdges_31 = __makeModifier_31(_edges2["default"], 'snapEdges');
_$modifiers_31.snapEdges = __snapEdges_31;
var __restrict_31 = __makeModifier_31(___pointer_31["default"], 'restrict');
_$modifiers_31.restrict = __restrict_31;
var __restrictRect_31 = __makeModifier_31(___rect_31["default"], 'restrictRect');
_$modifiers_31.restrictRect = __restrictRect_31;
var __restrictEdges_31 = __makeModifier_31(___edges_31["default"], 'restrictEdges');
_$modifiers_31.restrictEdges = __restrictEdges_31;
var __restrictSize_31 = __makeModifier_31(___size_31["default"], 'restrictSize');
_$modifiers_31.restrictSize = __restrictSize_31;

var _$PointerEvent_39 = {};
"use strict";

Object.defineProperty(_$PointerEvent_39, "__esModule", {
  value: true
});
_$PointerEvent_39["default"] = void 0;

var ___BaseEvent2_39 = ___interopRequireDefault_39(_$BaseEvent_13);

var ___pointerUtils_39 = ___interopRequireDefault_39(_$pointerUtils_61);

function ___interopRequireDefault_39(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___typeof_39(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { ___typeof_39 = function _typeof(obj) { return typeof obj; }; } else { ___typeof_39 = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return ___typeof_39(obj); }

function ___classCallCheck_39(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function ___defineProperties_39(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function ___createClass_39(Constructor, protoProps, staticProps) { if (protoProps) ___defineProperties_39(Constructor.prototype, protoProps); if (staticProps) ___defineProperties_39(Constructor, staticProps); return Constructor; }

function ___possibleConstructorReturn_39(self, call) { if (call && (___typeof_39(call) === "object" || typeof call === "function")) { return call; } return ___assertThisInitialized_39(self); }

function ___getPrototypeOf_39(o) { ___getPrototypeOf_39 = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return ___getPrototypeOf_39(o); }

function ___assertThisInitialized_39(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function ___inherits_39(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) ___setPrototypeOf_39(subClass, superClass); }

function ___setPrototypeOf_39(o, p) { ___setPrototypeOf_39 = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return ___setPrototypeOf_39(o, p); }

/** */
var PointerEvent =
/*#__PURE__*/
function (_BaseEvent) {
  ___inherits_39(PointerEvent, _BaseEvent);

  /** */
  function PointerEvent(type, pointer, event, eventTarget, interaction, timeStamp) {
    var _this;

    ___classCallCheck_39(this, PointerEvent);

    _this = ___possibleConstructorReturn_39(this, ___getPrototypeOf_39(PointerEvent).call(this, interaction));

    ___pointerUtils_39["default"].pointerExtend(___assertThisInitialized_39(_this), event);

    if (event !== pointer) {
      ___pointerUtils_39["default"].pointerExtend(___assertThisInitialized_39(_this), pointer);
    }

    _this.timeStamp = timeStamp;
    _this.originalEvent = event;
    _this.type = type;
    _this.pointerId = ___pointerUtils_39["default"].getPointerId(pointer);
    _this.pointerType = ___pointerUtils_39["default"].getPointerType(pointer);
    _this.target = eventTarget;
    _this.currentTarget = null;

    if (type === 'tap') {
      var pointerIndex = interaction.getPointerIndex(pointer);
      _this.dt = _this.timeStamp - interaction.pointers[pointerIndex].downTime;
      var interval = _this.timeStamp - interaction.tapTime;
      _this["double"] = !!(interaction.prevTap && interaction.prevTap.type !== 'doubletap' && interaction.prevTap.target === _this.target && interval < 500);
    } else if (type === 'doubletap') {
      _this.dt = pointer.timeStamp - interaction.tapTime;
    }

    return _this;
  }

  ___createClass_39(PointerEvent, [{
    key: "_subtractOrigin",
    value: function _subtractOrigin(_ref) {
      var originX = _ref.x,
          originY = _ref.y;
      this.pageX -= originX;
      this.pageY -= originY;
      this.clientX -= originX;
      this.clientY -= originY;
      return this;
    }
  }, {
    key: "_addOrigin",
    value: function _addOrigin(_ref2) {
      var originX = _ref2.x,
          originY = _ref2.y;
      this.pageX += originX;
      this.pageY += originY;
      this.clientX += originX;
      this.clientY += originY;
      return this;
    }
    /**
     * Prevent the default behaviour of the original Event
     */

  }, {
    key: "preventDefault",
    value: function preventDefault() {
      this.originalEvent.preventDefault();
    }
  }]);

  return PointerEvent;
}(___BaseEvent2_39["default"]);

_$PointerEvent_39["default"] = PointerEvent;

var _$base_40 = {};
"use strict";

Object.defineProperty(_$base_40, "__esModule", {
  value: true
});
_$base_40["default"] = void 0;

var __utils_40 = ___interopRequireWildcard_40(_$utils_56);

var _PointerEvent = ___interopRequireDefault_40(_$PointerEvent_39);

function ___interopRequireDefault_40(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_40(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

var signals = new __utils_40.Signals();
var simpleSignals = ['down', 'up', 'cancel'];
var simpleEvents = ['down', 'up', 'cancel'];
var __defaults_40 = {
  holdDuration: 600,
  ignoreFrom: null,
  allowFrom: null,
  origin: {
    x: 0,
    y: 0
  }
};
var pointerEvents = {
  id: 'pointer-events/base',
  install: __install_40,
  signals: signals,
  PointerEvent: _PointerEvent["default"],
  fire: fire,
  collectEventTargets: collectEventTargets,
  createSignalListener: createSignalListener,
  defaults: __defaults_40,
  types: ['down', 'move', 'up', 'cancel', 'tap', 'doubletap', 'hold']
};

function fire(arg, scope) {
  var interaction = arg.interaction,
      pointer = arg.pointer,
      event = arg.event,
      eventTarget = arg.eventTarget,
      _arg$type = arg.type,
      type = _arg$type === void 0 ? arg.pointerEvent.type : _arg$type,
      _arg$targets = arg.targets,
      targets = _arg$targets === void 0 ? collectEventTargets(arg) : _arg$targets;
  var _arg$pointerEvent = arg.pointerEvent,
      pointerEvent = _arg$pointerEvent === void 0 ? new _PointerEvent["default"](type, pointer, event, eventTarget, interaction, scope.now()) : _arg$pointerEvent;
  var signalArg = {
    interaction: interaction,
    pointer: pointer,
    event: event,
    eventTarget: eventTarget,
    targets: targets,
    type: type,
    pointerEvent: pointerEvent
  };

  for (var i = 0; i < targets.length; i++) {
    var target = targets[i];

    for (var prop in target.props || {}) {
      pointerEvent[prop] = target.props[prop];
    }

    var origin = __utils_40.getOriginXY(target.eventable, target.node);

    pointerEvent._subtractOrigin(origin);

    pointerEvent.eventable = target.eventable;
    pointerEvent.currentTarget = target.node;
    target.eventable.fire(pointerEvent);

    pointerEvent._addOrigin(origin);

    if (pointerEvent.immediatePropagationStopped || pointerEvent.propagationStopped && i + 1 < targets.length && targets[i + 1].node !== pointerEvent.currentTarget) {
      break;
    }
  }

  signals.fire('fired', signalArg);

  if (type === 'tap') {
    // if pointerEvent should make a double tap, create and fire a doubletap
    // PointerEvent and use that as the prevTap
    var prevTap = pointerEvent["double"] ? fire({
      interaction: interaction,
      pointer: pointer,
      event: event,
      eventTarget: eventTarget,
      type: 'doubletap'
    }, scope) : pointerEvent;
    interaction.prevTap = prevTap;
    interaction.tapTime = prevTap.timeStamp;
  }

  return pointerEvent;
}

function collectEventTargets(_ref) {
  var interaction = _ref.interaction,
      pointer = _ref.pointer,
      event = _ref.event,
      eventTarget = _ref.eventTarget,
      type = _ref.type;
  var pointerIndex = interaction.getPointerIndex(pointer);
  var pointerInfo = interaction.pointers[pointerIndex]; // do not fire a tap event if the pointer was moved before being lifted

  if (type === 'tap' && (interaction.pointerWasMoved || // or if the pointerup target is different to the pointerdown target
  !(pointerInfo && pointerInfo.downTarget === eventTarget))) {
    return [];
  }

  var path = __utils_40.dom.getPath(eventTarget);
  var signalArg = {
    interaction: interaction,
    pointer: pointer,
    event: event,
    eventTarget: eventTarget,
    type: type,
    path: path,
    targets: [],
    node: null
  };

  for (var _i = 0; _i < path.length; _i++) {
    var _ref2;

    _ref2 = path[_i];
    var node = _ref2;
    signalArg.node = node;
    signals.fire('collect-targets', signalArg);
  }

  if (type === 'hold') {
    signalArg.targets = signalArg.targets.filter(function (target) {
      return target.eventable.options.holdDuration === interaction.pointers[pointerIndex].hold.duration;
    });
  }

  return signalArg.targets;
}

function __install_40(scope) {
  var interactions = scope.interactions;
  scope.pointerEvents = pointerEvents;
  scope.defaults.actions.pointerEvents = pointerEvents.defaults;
  interactions.signals.on('new', function (_ref3) {
    var interaction = _ref3.interaction;
    interaction.prevTap = null; // the most recent tap event on this interaction

    interaction.tapTime = 0; // time of the most recent tap event
  });
  interactions.signals.on('update-pointer', function (_ref4) {
    var down = _ref4.down,
        pointerInfo = _ref4.pointerInfo;

    if (!down && pointerInfo.hold) {
      return;
    }

    pointerInfo.hold = {
      duration: Infinity,
      timeout: null
    };
  });
  interactions.signals.on('move', function (_ref5) {
    var interaction = _ref5.interaction,
        pointer = _ref5.pointer,
        event = _ref5.event,
        eventTarget = _ref5.eventTarget,
        duplicateMove = _ref5.duplicateMove;
    var pointerIndex = interaction.getPointerIndex(pointer);

    if (!duplicateMove && (!interaction.pointerIsDown || interaction.pointerWasMoved)) {
      if (interaction.pointerIsDown) {
        clearTimeout(interaction.pointers[pointerIndex].hold.timeout);
      }

      fire({
        interaction: interaction,
        pointer: pointer,
        event: event,
        eventTarget: eventTarget,
        type: 'move'
      }, scope);
    }
  });
  interactions.signals.on('down', function (_ref6) {
    var interaction = _ref6.interaction,
        pointer = _ref6.pointer,
        event = _ref6.event,
        eventTarget = _ref6.eventTarget,
        pointerIndex = _ref6.pointerIndex;
    var timer = interaction.pointers[pointerIndex].hold;
    var path = __utils_40.dom.getPath(eventTarget);
    var signalArg = {
      interaction: interaction,
      pointer: pointer,
      event: event,
      eventTarget: eventTarget,
      type: 'hold',
      targets: [],
      path: path,
      node: null
    };

    for (var _i2 = 0; _i2 < path.length; _i2++) {
      var _ref7;

      _ref7 = path[_i2];
      var node = _ref7;
      signalArg.node = node;
      signals.fire('collect-targets', signalArg);
    }

    if (!signalArg.targets.length) {
      return;
    }

    var minDuration = Infinity;

    for (var _i3 = 0; _i3 < signalArg.targets.length; _i3++) {
      var _ref8;

      _ref8 = signalArg.targets[_i3];
      var target = _ref8;
      var holdDuration = target.eventable.options.holdDuration;

      if (holdDuration < minDuration) {
        minDuration = holdDuration;
      }
    }

    timer.duration = minDuration;
    timer.timeout = setTimeout(function () {
      fire({
        interaction: interaction,
        eventTarget: eventTarget,
        pointer: pointer,
        event: event,
        type: 'hold'
      }, scope);
    }, minDuration);
  });
  var _arr = ['up', 'cancel'];

  for (var _i4 = 0; _i4 < _arr.length; _i4++) {
    var signalName = _arr[_i4];
    interactions.signals.on(signalName, function (_ref10) {
      var interaction = _ref10.interaction,
          pointerIndex = _ref10.pointerIndex;

      if (interaction.pointers[pointerIndex].hold) {
        clearTimeout(interaction.pointers[pointerIndex].hold.timeout);
      }
    });
  }

  for (var i = 0; i < simpleSignals.length; i++) {
    interactions.signals.on(simpleSignals[i], createSignalListener(simpleEvents[i], scope));
  }

  interactions.signals.on('up', function (_ref9) {
    var interaction = _ref9.interaction,
        pointer = _ref9.pointer,
        event = _ref9.event,
        eventTarget = _ref9.eventTarget;

    if (!interaction.pointerWasMoved) {
      fire({
        interaction: interaction,
        eventTarget: eventTarget,
        pointer: pointer,
        event: event,
        type: 'tap'
      }, scope);
    }
  });
}

function createSignalListener(type, scope) {
  return function (_ref11) {
    var interaction = _ref11.interaction,
        pointer = _ref11.pointer,
        event = _ref11.event,
        eventTarget = _ref11.eventTarget;
    fire({
      interaction: interaction,
      eventTarget: eventTarget,
      pointer: pointer,
      event: event,
      type: type
    }, scope);
  };
}

var ___default_40 = pointerEvents;
_$base_40["default"] = ___default_40;

var _$holdRepeat_41 = {};
"use strict";

Object.defineProperty(_$holdRepeat_41, "__esModule", {
  value: true
});
_$holdRepeat_41["default"] = void 0;

var ___base_41 = ___interopRequireDefault_41(_$base_40);

function ___interopRequireDefault_41(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __install_41(scope) {
  var pointerEvents = scope.pointerEvents,
      interactions = scope.interactions;
  scope.usePlugin(___base_41["default"]);
  pointerEvents.signals.on('new', onNew);
  pointerEvents.signals.on('fired', function (arg) {
    return onFired(arg, scope);
  });
  var _arr = ['move', 'up', 'cancel', 'endall'];

  for (var _i = 0; _i < _arr.length; _i++) {
    var signal = _arr[_i];
    interactions.signals.on(signal, endHoldRepeat);
  } // don't repeat by default


  pointerEvents.defaults.holdRepeatInterval = 0;
  pointerEvents.types.push('holdrepeat');
}

function onNew(_ref) {
  var pointerEvent = _ref.pointerEvent;

  if (pointerEvent.type !== 'hold') {
    return;
  }

  pointerEvent.count = (pointerEvent.count || 0) + 1;
}

function onFired(_ref2, scope) {
  var interaction = _ref2.interaction,
      pointerEvent = _ref2.pointerEvent,
      eventTarget = _ref2.eventTarget,
      targets = _ref2.targets;

  if (pointerEvent.type !== 'hold' || !targets.length) {
    return;
  } // get the repeat interval from the first eventable


  var interval = targets[0].eventable.options.holdRepeatInterval; // don't repeat if the interval is 0 or less

  if (interval <= 0) {
    return;
  } // set a timeout to fire the holdrepeat event


  interaction.holdIntervalHandle = setTimeout(function () {
    scope.pointerEvents.fire({
      interaction: interaction,
      eventTarget: eventTarget,
      type: 'hold',
      pointer: pointerEvent,
      event: pointerEvent
    }, scope);
  }, interval);
}

function endHoldRepeat(_ref3) {
  var interaction = _ref3.interaction;

  // set the interaction's holdStopTime property
  // to stop further holdRepeat events
  if (interaction.holdIntervalHandle) {
    clearInterval(interaction.holdIntervalHandle);
    interaction.holdIntervalHandle = null;
  }
}

var ___default_41 = {
  id: 'pointer-events/holdRepeat',
  install: __install_41
};
_$holdRepeat_41["default"] = ___default_41;

var _$interactableTargets_43 = {};
"use strict";

Object.defineProperty(_$interactableTargets_43, "__esModule", {
  value: true
});
_$interactableTargets_43["default"] = void 0;

/* removed: var _$arr_47 = require("@interactjs/utils/arr"); */;

var ___extend_43 = ___interopRequireDefault_43(_$extend_53);

function ___interopRequireDefault_43(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __install_43(scope) {
  var pointerEvents = scope.pointerEvents,
      actions = scope.actions,
      Interactable = scope.Interactable,
      interactables = scope.interactables;
  pointerEvents.signals.on('collect-targets', function (_ref) {
    var targets = _ref.targets,
        node = _ref.node,
        type = _ref.type,
        eventTarget = _ref.eventTarget;
    scope.interactables.forEachMatch(node, function (interactable) {
      var eventable = interactable.events;
      var options = eventable.options;

      if (eventable.types[type] && eventable.types[type].length && interactable.testIgnoreAllow(options, node, eventTarget)) {
        targets.push({
          node: node,
          eventable: eventable,
          props: {
            interactable: interactable
          }
        });
      }
    });
  });
  interactables.signals.on('new', function (_ref2) {
    var interactable = _ref2.interactable;

    interactable.events.getRect = function (element) {
      return interactable.getRect(element);
    };
  });
  interactables.signals.on('set', function (_ref3) {
    var interactable = _ref3.interactable,
        options = _ref3.options;
    (0, ___extend_43["default"])(interactable.events.options, pointerEvents.defaults);
    (0, ___extend_43["default"])(interactable.events.options, options.pointerEvents || {});
  });
  (0, _$arr_47.merge)(actions.eventTypes, pointerEvents.types);
  Interactable.prototype.pointerEvents = pointerEventsMethod;
  var __backCompatOption = Interactable.prototype._backCompatOption;

  Interactable.prototype._backCompatOption = function (optionName, newValue) {
    var ret = __backCompatOption.call(this, optionName, newValue);

    if (ret === this) {
      this.events.options[optionName] = newValue;
    }

    return ret;
  };
}

function pointerEventsMethod(options) {
  (0, ___extend_43["default"])(this.events.options, options);
  return this;
}

var ___default_43 = {
  id: 'pointer-events/interactableTargets',
  install: __install_43
};
_$interactableTargets_43["default"] = ___default_43;

var _$pointerEvents_42 = {};
"use strict";

Object.defineProperty(_$pointerEvents_42, "__esModule", {
  value: true
});
_$pointerEvents_42.install = __install_42;
Object.defineProperty(_$pointerEvents_42, "pointerEvents", {
  enumerable: true,
  get: function get() {
    return ___base_42["default"];
  }
});
Object.defineProperty(_$pointerEvents_42, "holdRepeat", {
  enumerable: true,
  get: function get() {
    return _holdRepeat["default"];
  }
});
Object.defineProperty(_$pointerEvents_42, "interactableTargets", {
  enumerable: true,
  get: function get() {
    return _interactableTargets["default"];
  }
});
_$pointerEvents_42.id = void 0;

var ___base_42 = ___interopRequireDefault_42(_$base_40);

var _holdRepeat = ___interopRequireDefault_42(_$holdRepeat_41);

var _interactableTargets = ___interopRequireDefault_42(_$interactableTargets_43);

function ___interopRequireDefault_42(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function __install_42(scope) {
  scope.usePlugin(___base_42["default"]);
  scope.usePlugin(_holdRepeat["default"]);
  scope.usePlugin(_interactableTargets["default"]);
}

var __id_42 = 'pointer-events';
_$pointerEvents_42.id = __id_42;

var _$reflow_44 = {};
"use strict";

Object.defineProperty(_$reflow_44, "__esModule", {
  value: true
});
_$reflow_44.install = __install_44;
_$reflow_44["default"] = void 0;

/* removed: var _$InteractEvent_15 = require("@interactjs/core/InteractEvent"); */;

/* removed: var _$utils_56 = require("@interactjs/utils"); */;

_$InteractEvent_15.EventPhase.Reflow = 'reflow';

function __install_44(scope) {
  var actions = scope.actions,
      interactions = scope.interactions,
      Interactable = scope.Interactable; // add action reflow event types

  for (var _i = 0; _i < actions.names.length; _i++) {
    var _ref;

    _ref = actions.names[_i];
    var actionName = _ref;
    actions.eventTypes.push("".concat(actionName, "reflow"));
  } // remove completed reflow interactions


  interactions.signals.on('stop', function (_ref2) {
    var interaction = _ref2.interaction;

    if (interaction.pointerType === _$InteractEvent_15.EventPhase.Reflow) {
      if (interaction._reflowResolve) {
        interaction._reflowResolve();
      }

      _$utils_56.arr.remove(scope.interactions.list, interaction);
    }
  });
  /**
   * ```js
   * const interactable = interact(target)
   * const drag = { name: drag, axis: 'x' }
   * const resize = { name: resize, edges: { left: true, bottom: true }
   *
   * interactable.reflow(drag)
   * interactable.reflow(resize)
   * ```
   *
   * Start an action sequence to re-apply modifiers, check drops, etc.
   *
   * @param { Object } action The action to begin
   * @param { string } action.name The name of the action
   * @returns { Promise<Interactable> }
   */

  Interactable.prototype.reflow = function (action) {
    return reflow(this, action, scope);
  };
}

function reflow(interactable, action, scope) {
  var elements = _$utils_56.is.string(interactable.target) ? _$utils_56.arr.from(interactable._context.querySelectorAll(interactable.target)) : [interactable.target]; // tslint:disable-next-line variable-name

  var Promise = _$utils_56.win.window.Promise;
  var promises = Promise ? [] : null;

  var _loop = function _loop() {
    _ref3 = elements[_i2];
    var element = _ref3;
    var rect = interactable.getRect(element);

    if (!rect) {
      return "break";
    }

    var runningInteraction = _$utils_56.arr.find(scope.interactions.list, function (interaction) {
      return interaction.interacting() && interaction.interactable === interactable && interaction.element === element && interaction.prepared.name === action.name;
    });

    var reflowPromise = void 0;

    if (runningInteraction) {
      runningInteraction.move();

      if (promises) {
        reflowPromise = runningInteraction._reflowPromise || new Promise(function (resolve) {
          runningInteraction._reflowResolve = resolve;
        });
      }
    } else {
      var xywh = _$utils_56.rect.tlbrToXywh(rect);

      var coords = {
        page: {
          x: xywh.x,
          y: xywh.y
        },
        client: {
          x: xywh.x,
          y: xywh.y
        },
        timeStamp: scope.now()
      };

      var event = _$utils_56.pointer.coordsToEvent(coords);

      reflowPromise = startReflow(scope, interactable, element, action, event);
    }

    if (promises) {
      promises.push(reflowPromise);
    }
  };

  for (var _i2 = 0; _i2 < elements.length; _i2++) {
    var _ref3;

    var _ret = _loop();

    if (_ret === "break") break;
  }

  return promises && Promise.all(promises).then(function () {
    return interactable;
  });
}

function startReflow(scope, interactable, element, action, event) {
  var interaction = scope.interactions["new"]({
    pointerType: 'reflow'
  });
  var signalArg = {
    interaction: interaction,
    event: event,
    pointer: event,
    eventTarget: element,
    phase: _$InteractEvent_15.EventPhase.Reflow
  };
  interaction.interactable = interactable;
  interaction.element = element;
  interaction.prepared = (0, _$utils_56.extend)({}, action);
  interaction.prevEvent = event;
  interaction.updatePointer(event, event, element, true);

  interaction._doPhase(signalArg);

  var reflowPromise = _$utils_56.win.window.Promise ? new _$utils_56.win.window.Promise(function (resolve) {
    interaction._reflowResolve = resolve;
  }) : null;
  interaction._reflowPromise = reflowPromise;
  interaction.start(action, interactable, element);

  if (interaction._interacting) {
    interaction.move(signalArg);
    interaction.end(event);
  } else {
    interaction.stop();
  }

  interaction.removePointer(event, event);
  interaction.pointerIsDown = false;
  return reflowPromise;
}

var ___default_44 = {
  id: 'reflow',
  install: __install_44
};
_$reflow_44["default"] = ___default_44;

var _$interact_28 = {};
"use strict";

Object.defineProperty(_$interact_28, "__esModule", {
  value: true
});
_$interact_28["default"] = _$interact_28.scope = _$interact_28.interact = void 0;

var ___scope_28 = _$scope_24({});

var __utils_28 = ___interopRequireWildcard_28(_$utils_56);

var ___browser_28 = ___interopRequireDefault_28(_$browser_48);

var ___events_28 = ___interopRequireDefault_28(_$events_52);

function ___interopRequireDefault_28(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_28(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

/** @module interact */
var globalEvents = {};
var scope = new ___scope_28.Scope();
/**
 * ```js
 * interact('#draggable').draggable(true)
 *
 * var rectables = interact('rect')
 * rectables
 *   .gesturable(true)
 *   .on('gesturemove', function (event) {
 *       // ...
 *   })
 * ```
 *
 * The methods of this variable can be used to set elements as interactables
 * and also to change various default settings.
 *
 * Calling it as a function and passing an element or a valid CSS selector
 * string returns an Interactable object which has various methods to configure
 * it.
 *
 * @global
 *
 * @param {Element | string} target The HTML or SVG Element to interact with
 * or CSS selector
 * @return {Interactable}
 */

_$interact_28.scope = scope;

var interact = function interact(target, options) {
  var interactable = scope.interactables.get(target, options);

  if (!interactable) {
    interactable = scope.interactables["new"](target, options);
    interactable.events.global = globalEvents;
  }

  return interactable;
};
/**
 * Use a plugin
 *
 * @alias module:interact.use
 *
 * @param {Object} plugin
 * @param {function} plugin.install
 * @return {interact}
 */


_$interact_28.interact = interact;
interact.use = use;

function use(plugin, options) {
  scope.usePlugin(plugin, options);
  return interact;
}
/**
 * Check if an element or selector has been set with the {@link interact}
 * function
 *
 * @alias module:interact.isSet
 *
 * @param {Element} element The Element being searched for
 * @return {boolean} Indicates if the element or CSS selector was previously
 * passed to interact
 */


interact.isSet = isSet;

function isSet(target, options) {
  return !!scope.interactables.get(target, options && options.context);
}
/**
 * Add a global listener for an InteractEvent or adds a DOM event to `document`
 *
 * @alias module:interact.on
 *
 * @param {string | array | object} type The types of events to listen for
 * @param {function} listener The function event (s)
 * @param {object | boolean} [options] object or useCapture flag for
 * addEventListener
 * @return {object} interact
 */


interact.on = on;

function on(type, listener, options) {
  if (__utils_28.is.string(type) && type.search(' ') !== -1) {
    type = type.trim().split(/ +/);
  }

  if (__utils_28.is.array(type)) {
    for (var _i = 0; _i < type.length; _i++) {
      var _ref;

      _ref = type[_i];
      var eventType = _ref;
      interact.on(eventType, listener, options);
    }

    return interact;
  }

  if (__utils_28.is.object(type)) {
    for (var prop in type) {
      interact.on(prop, type[prop], listener);
    }

    return interact;
  } // if it is an InteractEvent type, add listener to globalEvents


  if (__utils_28.arr.contains(scope.actions.eventTypes, type)) {
    // if this type of event was never bound
    if (!globalEvents[type]) {
      globalEvents[type] = [listener];
    } else {
      globalEvents[type].push(listener);
    }
  } // If non InteractEvent type, addEventListener to document
  else {
      ___events_28["default"].add(scope.document, type, listener, {
        options: options
      });
    }

  return interact;
}
/**
 * Removes a global InteractEvent listener or DOM event from `document`
 *
 * @alias module:interact.off
 *
 * @param {string | array | object} type The types of events that were listened
 * for
 * @param {function} listener The listener function to be removed
 * @param {object | boolean} options [options] object or useCapture flag for
 * removeEventListener
 * @return {object} interact
 */


interact.off = off;

function off(type, listener, options) {
  if (__utils_28.is.string(type) && type.search(' ') !== -1) {
    type = type.trim().split(/ +/);
  }

  if (__utils_28.is.array(type)) {
    for (var _i2 = 0; _i2 < type.length; _i2++) {
      var _ref2;

      _ref2 = type[_i2];
      var eventType = _ref2;
      interact.off(eventType, listener, options);
    }

    return interact;
  }

  if (__utils_28.is.object(type)) {
    for (var prop in type) {
      interact.off(prop, type[prop], listener);
    }

    return interact;
  }

  if (!__utils_28.arr.contains(scope.actions.eventTypes, type)) {
    ___events_28["default"].remove(scope.document, type, listener, options);
  } else {
    var index;

    if (type in globalEvents && (index = globalEvents[type].indexOf(listener)) !== -1) {
      globalEvents[type].splice(index, 1);
    }
  }

  return interact;
}
/**
 * Returns an object which exposes internal data
 * @alias module:interact.debug
 *
 * @return {object} An object with properties that outline the current state
 * and expose internal functions and variables
 */


interact.debug = debug;

function debug() {
  return scope;
} // expose the functions used to calculate multi-touch properties


interact.getPointerAverage = __utils_28.pointer.pointerAverage;
interact.getTouchBBox = __utils_28.pointer.touchBBox;
interact.getTouchDistance = __utils_28.pointer.touchDistance;
interact.getTouchAngle = __utils_28.pointer.touchAngle;
interact.getElementRect = __utils_28.dom.getElementRect;
interact.getElementClientRect = __utils_28.dom.getElementClientRect;
interact.matchesSelector = __utils_28.dom.matchesSelector;
interact.closest = __utils_28.dom.closest;
/**
 * @alias module:interact.supportsTouch
 *
 * @return {boolean} Whether or not the browser supports touch input
 */

interact.supportsTouch = supportsTouch;

function supportsTouch() {
  return ___browser_28["default"].supportsTouch;
}
/**
 * @alias module:interact.supportsPointerEvent
 *
 * @return {boolean} Whether or not the browser supports PointerEvents
 */


interact.supportsPointerEvent = supportsPointerEvent;

function supportsPointerEvent() {
  return ___browser_28["default"].supportsPointerEvent;
}
/**
 * Cancels all interactions (end events are not fired)
 *
 * @alias module:interact.stop
 *
 * @return {object} interact
 */


interact.stop = __stop_28;

function __stop_28() {
  for (var _i3 = 0; _i3 < scope.interactions.list.length; _i3++) {
    var _ref3;

    _ref3 = scope.interactions.list[_i3];
    var interaction = _ref3;
    interaction.stop();
  }

  return interact;
}
/**
 * Returns or sets the distance the pointer must be moved before an action
 * sequence occurs. This also affects tolerance for tap events.
 *
 * @alias module:interact.pointerMoveTolerance
 *
 * @param {number} [newValue] The movement from the start position must be greater than this value
 * @return {interact | number}
 */


interact.pointerMoveTolerance = pointerMoveTolerance;

function pointerMoveTolerance(newValue) {
  if (__utils_28.is.number(newValue)) {
    scope.interactions.pointerMoveTolerance = newValue;
    return interact;
  }

  return scope.interactions.pointerMoveTolerance;
}

scope.interactables.signals.on('unset', function (_ref4) {
  var interactable = _ref4.interactable;
  scope.interactables.list.splice(scope.interactables.list.indexOf(interactable), 1); // Stop related interactions when an Interactable is unset

  for (var _i4 = 0; _i4 < scope.interactions.list.length; _i4++) {
    var _ref5;

    _ref5 = scope.interactions.list[_i4];
    var interaction = _ref5;

    if (interaction.interactable === interactable && interaction.interacting() && !interaction._ending) {
      interaction.stop();
    }
  }
});

interact.addDocument = function (doc, options) {
  return scope.addDocument(doc, options);
};

interact.removeDocument = function (doc) {
  return scope.removeDocument(doc);
};

scope.interact = interact;
var ___default_28 = interact;
_$interact_28["default"] = ___default_28;

var _$interact_27 = {};
"use strict";

Object.defineProperty(_$interact_27, "__esModule", {
  value: true
});
_$interact_27.init = __init_27;
Object.defineProperty(_$interact_27, "autoScroll", {
  enumerable: true,
  get: function get() {
    return _autoScroll["default"];
  }
});
Object.defineProperty(_$interact_27, "interactablePreventDefault", {
  enumerable: true,
  get: function get() {
    return _interactablePreventDefault["default"];
  }
});
Object.defineProperty(_$interact_27, "inertia", {
  enumerable: true,
  get: function get() {
    return _inertia["default"];
  }
});
Object.defineProperty(_$interact_27, "modifiers", {
  enumerable: true,
  get: function get() {
    return ___base_27["default"];
  }
});
Object.defineProperty(_$interact_27, "reflow", {
  enumerable: true,
  get: function get() {
    return _reflow["default"];
  }
});
Object.defineProperty(_$interact_27, "interact", {
  enumerable: true,
  get: function get() {
    return _interact["default"];
  }
});
_$interact_27.pointerEvents = _$interact_27.actions = _$interact_27["default"] = void 0;

var actions = ___interopRequireWildcard_27(_$actions_5);

_$interact_27.actions = actions;

var _autoScroll = ___interopRequireDefault_27(_$autoScroll_7);

var autoStart = ___interopRequireWildcard_27(_$autoStart_12);

var _interactablePreventDefault = ___interopRequireDefault_27(_$interactablePreventDefault_21);

var _devTools = ___interopRequireDefault_27(_$devTools_25);

var _inertia = ___interopRequireDefault_27(_$inertia_26);

var modifiers = ___interopRequireWildcard_27(_$modifiers_31);

var ___base_27 = ___interopRequireDefault_27(_$base_30);

var __pointerEvents_27 = ___interopRequireWildcard_27(_$pointerEvents_42);

_$interact_27.pointerEvents = __pointerEvents_27;

var _reflow = ___interopRequireDefault_27(_$reflow_44);

var _interact = ___interopRequireWildcard_27(_$interact_28);

function ___interopRequireDefault_27(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_27(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function __init_27(window) {
  _interact.scope.init(window);

  _interact["default"].use(_interactablePreventDefault["default"]); // pointerEvents


  _interact["default"].use(__pointerEvents_27); // inertia


  _interact["default"].use(_inertia["default"]); // autoStart, hold


  _interact["default"].use(autoStart); // drag and drop, resize, gesture


  _interact["default"].use(actions); // snap, resize, etc.


  _interact["default"].use(___base_27["default"]); // for backwrads compatibility


  for (var type in modifiers) {
    var _modifiers$type = modifiers[type],
        _defaults = _modifiers$type._defaults,
        _methods = _modifiers$type._methods;
    _defaults._methods = _methods;
    _interact.scope.defaults.perAction[type] = _defaults;
  } // autoScroll


  _interact["default"].use(_autoScroll["default"]); // reflow


  _interact["default"].use(_reflow["default"]); // eslint-disable-next-line no-undef


  if (false) {
    _interact["default"].use(_devTools["default"]);
  }

  return _interact["default"];
} // eslint-disable-next-line no-undef


_interact["default"].version = "1.5.4";
var ___default_27 = _interact["default"];
_$interact_27["default"] = ___default_27;

var _$types_45 = {};
/// <reference path="./types.d.ts" />
"use strict";

var _$grid_64 = {};
"use strict";

Object.defineProperty(_$grid_64, "__esModule", {
  value: true
});
_$grid_64["default"] = void 0;

function ___slicedToArray_64(arr, i) { return ___arrayWithHoles_64(arr) || ___iterableToArrayLimit_64(arr, i) || ___nonIterableRest_64(); }

function ___nonIterableRest_64() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function ___iterableToArrayLimit_64(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function ___arrayWithHoles_64(arr) { if (Array.isArray(arr)) return arr; }

function createGrid(grid) {
  var coordFields = [['x', 'y'], ['left', 'top'], ['right', 'bottom'], ['width', 'height']].filter(function (_ref) {
    var _ref2 = ___slicedToArray_64(_ref, 2),
        xField = _ref2[0],
        yField = _ref2[1];

    return xField in grid || yField in grid;
  });
  return function (x, y) {
    var range = grid.range,
        _grid$limits = grid.limits,
        limits = _grid$limits === void 0 ? {
      left: -Infinity,
      right: Infinity,
      top: -Infinity,
      bottom: Infinity
    } : _grid$limits,
        _grid$offset = grid.offset,
        offset = _grid$offset === void 0 ? {
      x: 0,
      y: 0
    } : _grid$offset;
    var result = {
      range: range
    };

    for (var _i2 = 0; _i2 < coordFields.length; _i2++) {
      var _ref3;

      _ref3 = coordFields[_i2];

      var _ref4 = _ref3,
          _ref5 = ___slicedToArray_64(_ref4, 2),
          xField = _ref5[0],
          yField = _ref5[1];

      var gridx = Math.round((x - offset.x) / grid[xField]);
      var gridy = Math.round((y - offset.y) / grid[yField]);
      result[xField] = Math.max(limits.left, Math.min(limits.right, gridx * grid[xField] + offset.x));
      result[yField] = Math.max(limits.top, Math.min(limits.bottom, gridy * grid[yField] + offset.y));
    }

    return result;
  };
}

var ___default_64 = createGrid;
_$grid_64["default"] = ___default_64;

var _$snappers_65 = {};
"use strict";

Object.defineProperty(_$snappers_65, "__esModule", {
  value: true
});
Object.defineProperty(_$snappers_65, "grid", {
  enumerable: true,
  get: function get() {
    return _grid["default"];
  }
});

var _grid = ___interopRequireDefault_65(_$grid_64);

function ___interopRequireDefault_65(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var _$index_29 = { exports: {} };
"use strict";

Object.defineProperty(_$index_29.exports, "__esModule", {
  value: true
});
_$index_29.exports.init = __init_29;
_$index_29.exports["default"] = void 0;

var ___interact_29 = ___interopRequireWildcard_29(_$interact_27);

var __modifiers_29 = ___interopRequireWildcard_29(_$modifiers_31);

_$types_45;

var ___extend_29 = ___interopRequireDefault_29(_$extend_53);

var snappers = ___interopRequireWildcard_29(_$snappers_65);

function ___interopRequireDefault_29(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function ___interopRequireWildcard_29(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {}; if (desc.get || desc.set) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } } newObj["default"] = obj; return newObj; } }

function ___typeof_29(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { ___typeof_29 = function _typeof(obj) { return typeof obj; }; } else { ___typeof_29 = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return ___typeof_29(obj); }

if ((typeof window === "undefined" ? "undefined" : ___typeof_29(window)) === 'object' && !!window) {
  __init_29(window);
}

function __init_29(win) {
  (0, ___interact_29.init)(win);
  return ___interact_29["default"].use({
    id: 'interactjs',
    install: function install() {
      ___interact_29["default"].modifiers = (0, ___extend_29["default"])({}, __modifiers_29);
      ___interact_29["default"].snappers = snappers;
      ___interact_29["default"].createSnapGrid = ___interact_29["default"].snappers.grid;
    }
  });
}

var ___default_29 = ___interact_29["default"];
_$index_29.exports["default"] = ___default_29;
___interact_29["default"]['default'] = ___interact_29["default"]; // tslint:disable-line no-string-literal

___interact_29["default"]['init'] = __init_29; // tslint:disable-line no-string-literal

if (( false ? "undefined" : ___typeof_29(_$index_29)) === 'object' && !!_$index_29) {
  _$index_29.exports = ___interact_29["default"];
}

_$index_29 = _$index_29.exports
return _$index_29;

});


//# sourceMappingURL=interact.js.map


/***/ }),

/***/ 295:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("img", {
      ref: "image",
      class: {
        shadow: _vm.shadow
      },
      style: {
        width: _vm.resizeWidth + "px",
        height: _vm.resizeHeight + "px",
        transform:
          "translate(" +
          (_vm.x + _vm.adjX) +
          "px, " +
          (_vm.y + _vm.adjY) +
          "px)"
      },
      attrs: { src: _vm.img, width: _vm.imageWidth, height: _vm.imageHeight }
    })
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-d4119378", module.exports)
  }
}

/***/ }),

/***/ 296:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "ccm-avatar-creator-container" },
    [
      _vm.img !== null
        ? _c("avatar-image", {
            ref: "shadow",
            attrs: {
              shadow: true,
              img: _vm.img,
              imageHeight: _vm.imageHeight,
              imageWidth: _vm.imageWidth,
              cropperWidth: _vm.width,
              cropperHeight: _vm.height
            },
            on: { mount: _vm.attachShadow }
          })
        : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        {
          ref: "dropzone",
          staticClass: "ccm-avatar-creator",
          class: { editing: _vm.img !== null },
          style: { width: _vm.width + "px", height: _vm.height + "px" }
        },
        [
          _vm.img
            ? _c("avatar-image", {
                ref: "image",
                attrs: {
                  img: _vm.img,
                  imageHeight: _vm.imageHeight,
                  imageWidth: _vm.imageWidth,
                  cropperWidth: _vm.width,
                  cropperHeight: _vm.height
                }
              })
            : _vm._e(),
          _vm._v(" "),
          !_vm.img
            ? _c("img", {
                staticClass: "ccm-avatar-current",
                attrs: { src: _vm.currentimage }
              })
            : _vm._e(),
          _vm._v(" "),
          _vm.saving
            ? _c(
                "div",
                {
                  staticClass: "saving",
                  style: { lineHeight: _vm.height + "px" }
                },
                [
                  _c("i", {
                    staticClass: "fa fa-spin fa-spinner fa-circle-o-notch"
                  })
                ]
              )
            : _vm._e()
        ],
        1
      ),
      _vm._v(" "),
      _vm.img
        ? _c("div", { staticClass: "ccm-avatar-actions" }, [
            _c("a", {
              staticClass: "ccm-avatar-okay",
              style: { width: _vm.width / 2 + "px" },
              on: { click: _vm.handleOkay }
            }),
            _vm._v(" "),
            _c("a", {
              staticClass: "ccm-avatar-cancel",
              style: { width: _vm.width / 2 + "px" },
              on: { click: _vm.handleCancel }
            })
          ])
        : _vm._e(),
      _vm._v(" "),
      _c("canvas", { ref: "canvas", staticStyle: { height: "0px" } }),
      _vm._v(" "),
      _vm.hasError
        ? _c("div", { staticClass: "alert alert-danger" }, [
            _vm._v("\n        " + _vm._s(_vm.errorMessage) + "\n    ")
          ])
        : _vm._e()
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-04b89432", module.exports)
  }
}

/***/ }),

/***/ 297:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 298:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 299:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 300:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 301:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 9:
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
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
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ })

/******/ });