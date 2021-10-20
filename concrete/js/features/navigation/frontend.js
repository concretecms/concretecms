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
/******/ 	return __webpack_require__(__webpack_require__.s = 10);
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
/* harmony import */ var _frontend_top_navigation_bar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/top-navigation-bar */ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/top-navigation-bar.js");
/* harmony import */ var _frontend_top_navigation_bar__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_frontend_top_navigation_bar__WEBPACK_IMPORTED_MODULE_0__);


/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/top-navigation-bar.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/top-navigation-bar.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function (global, $) {
  document.addEventListener('DOMContentLoaded', function () {
    // Enable dropdown menu in navbar
    if (window.innerWidth > 992) {
      document.querySelectorAll('.ccm-block-top-navigation-bar .nav-item').forEach(function (everyitem) {
        everyitem.addEventListener('mouseover', function (e) {
          var linkElement = this.querySelector('a[data-concrete-toggle]');

          if (linkElement != null) {
            var nextElement = linkElement.nextElementSibling;
            linkElement.classList.add('show');
            nextElement.classList.add('show');
          }
        });
        everyitem.addEventListener('mouseleave', function (e) {
          var linkElement = this.querySelector('a[data-concrete-toggle]');

          if (linkElement != null) {
            var nextElement = linkElement.nextElementSibling;
            linkElement.classList.remove('show');
            nextElement.classList.remove('show');
          }
        });
      });
    } else {
      $('a[data-concrete-toggle]').on('click', function (e) {
        if (!$(this).hasClass('show')) {
          e.preventDefault();
          var $nextElement = $(this).next();
          $nextElement.addClass('show');
          $(this).addClass('show');
        }
      });
    } // Enable transparency


    var $transparentNavbar = $('div[data-transparency=navbar]');
    var $toolbar = $('#ccm-toolbar');

    if ($transparentNavbar.length) {
      var _$navbar = $transparentNavbar.find('.navbar'); // Check the next item to see if it supports transparency


      if (_$navbar.hasClass('fixed-top') && $toolbar.length > 0) {
        _$navbar.removeClass('fixed-top');
      }

      var $nextElement = $transparentNavbar.next();

      if ($nextElement.length && $nextElement.is('[data-transparency=element]') && $toolbar.length === 0) {
        $transparentNavbar.addClass('transparency-enabled');

        if (_$navbar.hasClass('fixed-top')) {
          $(window).scroll(function () {
            var isScrolled = $(document).scrollTop() > 5;

            if (isScrolled) {
              $transparentNavbar.removeClass('transparency-enabled');
            } else {
              $transparentNavbar.addClass('transparency-enabled');
            }
          });
        }
      }

      $transparentNavbar.show(); // In phone mode, we need to hook into the expand/collapse event to remove transparency, because
      // we don't want to have transparency when the menu is expanded in phone mode.

      var $toggler = $transparentNavbar.find('[data-bs-toggle]');

      if ($toggler.length) {
        var $target = $($toggler.attr('data-bs-target'));
        $target.on('show.bs.collapse', function () {
          $transparentNavbar.addClass('transparency-temporarily-disabled');
        });
        $target.on('hidden.bs.collapse', function () {
          $transparentNavbar.removeClass('transparency-temporarily-disabled');
        });
      }
    } // Add padding to ccm-page if we're using the fixed bar.


    var $navbar = $('.ccm-block-top-navigation-bar .navbar');

    if ($navbar.hasClass('fixed-top')) {
      if ($transparentNavbar.length === 0 || !$transparentNavbar.hasClass('transparency-enabled')) {
        $('.ccm-page').css('padding-top', $navbar.outerHeight());
      }
    }
  });
})(window, $);

/***/ }),

/***/ 10:
/*!**********************************************************************************!*\
  !*** multi ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js */"./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js");


/***/ })

/******/ });