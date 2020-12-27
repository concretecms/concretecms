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
/******/ 	return __webpack_require__(__webpack_require__.s = 12);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js ***!
  \****************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_responsive_navigation__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/responsive-navigation */ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/responsive-navigation.js");
/* harmony import */ var _frontend_responsive_navigation__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_frontend_responsive_navigation__WEBPACK_IMPORTED_MODULE_0__);


/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/responsive-navigation.js":
/*!**************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/responsive-navigation.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function (global, $) {
  var originalNav = $('.ccm-responsive-navigation');

  if (!$('.ccm-responsive-overlay').length) {
    $('body').append('<div class="ccm-responsive-overlay"></div>');
  }

  var clonedNavigation = originalNav.clone();
  $(clonedNavigation).removeClass('original');
  $(clonedNavigation).find('*').each(function () {
    var t = $(this).attr('id');

    if (t !== undefined && t !== null && t !== '') {
      $(this).attr('id', 'cloned-ccm-ro_' + t);
    }
  });
  $('.ccm-responsive-overlay').append(clonedNavigation);
  $('.ccm-responsive-menu-launch').click(function () {
    $('.ccm-responsive-menu-launch').toggleClass('responsive-button-close'); // slide out mobile nav

    $('.ccm-responsive-overlay').slideToggle();
  });
  $('.ccm-responsive-overlay ul li').children('ul').hide();
  $('.ccm-responsive-overlay li').each(function (index) {
    if ($(this).children('ul').length > 0) {
      $(this).addClass('parent-ul');
    } else {
      $(this).addClass('last-li');
    }
  });
  $('.ccm-responsive-overlay .parent-ul a').click(function (event) {
    if (!$(this).parent('li').hasClass('last-li')) {
      $(this).parent('li').siblings().children('ul').hide();

      if ($(this).parent('li').children('ul').is(':visible')) {} else {
        $(this).next('ul').show();
        event.preventDefault();
      }
    }
  });
})(window, $);

/***/ }),

/***/ 12:
/*!**********************************************************************************!*\
  !*** multi ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js */"./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js");


/***/ })

/******/ });