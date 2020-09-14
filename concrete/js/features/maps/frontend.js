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
/******/ 	return __webpack_require__(__webpack_require__.s = 15);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@concretecms/bedrock/assets/maps/js/frontend.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/maps/js/frontend.js ***!
  \**********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_google_map__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/google-map */ "./node_modules/@concretecms/bedrock/assets/maps/js/frontend/google-map.js");
/* harmony import */ var _frontend_google_map__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_frontend_google_map__WEBPACK_IMPORTED_MODULE_0__);


/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/maps/js/frontend/google-map.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/maps/js/frontend/google-map.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

window.concreteGoogleMapInit = function () {
  /* eslint-disable no-new */
  $('.googleMapCanvas').each(function () {
    try {
      var latitude = $(this).data('latitude');
      var longitude = $(this).data('longitude');
      var zoom = $(this).data('zoom');
      var scrollwheel = $(this).data('scrollwheel');
      var draggable = $(this).data('draggable');
      var latlng = new google.maps.LatLng(latitude, longitude);
      var mapOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false,
        scrollwheel: scrollwheel,
        draggable: draggable,
        mapTypeControl: false
      };
      var map = new google.maps.Map(this, mapOptions);
      new google.maps.Marker({
        position: latlng,
        map: map
      });
    } catch (e) {
      $(this).replaceWith($('<p />').text(e.message));
    }
  });
};

/***/ }),

/***/ 15:
/*!****************************************************************************!*\
  !*** multi ./node_modules/@concretecms/bedrock/assets/maps/js/frontend.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/fabianbitter/Projekte/concrete5/core/9.0.0/build/node_modules/@concretecms/bedrock/assets/maps/js/frontend.js */"./node_modules/@concretecms/bedrock/assets/maps/js/frontend.js");


/***/ })

/******/ });