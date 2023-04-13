/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/themes/elemental/js/responsive-navigation.js":
/*!*************************************************************!*\
  !*** ./assets/themes/elemental/js/responsive-navigation.js ***!
  \*************************************************************/
/***/ (() => {

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
/******/ 			// no module.id needed
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!********************************************!*\
  !*** ./assets/themes/elemental/js/main.js ***!
  \********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _responsive_navigation__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./responsive-navigation */ "./assets/themes/elemental/js/responsive-navigation.js");
/* harmony import */ var _responsive_navigation__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_responsive_navigation__WEBPACK_IMPORTED_MODULE_0__);
// Navigation feature support for old custom template

})();

/******/ })()
;