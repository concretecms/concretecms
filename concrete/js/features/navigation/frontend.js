/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/top-navigation-bar.js":
/*!***********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/top-navigation-bar.js ***!
  \***********************************************************************************************/
/***/ (() => {

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
    }

    // Enable transparency
    var $transparentNavbar = $('div[data-transparency=navbar]');
    var $toolbar = $('#ccm-toolbar');
    if ($transparentNavbar.length) {
      var _$navbar = $transparentNavbar.find('.navbar');
      // Check the next item to see if it supports transparency

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
      $transparentNavbar.show();

      // In phone mode, we need to hook into the expand/collapse event to remove transparency, because
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
    }

    // Add padding to ccm-page if we're using the fixed bar.
    var $navbar = $('.ccm-block-top-navigation-bar .navbar');
    if ($navbar.hasClass('fixed-top')) {
      if ($transparentNavbar.length === 0 || !$transparentNavbar.hasClass('transparency-enabled')) {
        $('.ccm-page').css('padding-top', $navbar.outerHeight());
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
/*!****************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js ***!
  \****************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_top_navigation_bar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/top-navigation-bar */ "./node_modules/@concretecms/bedrock/assets/navigation/js/frontend/top-navigation-bar.js");
/* harmony import */ var _frontend_top_navigation_bar__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_frontend_top_navigation_bar__WEBPACK_IMPORTED_MODULE_0__);

})();

/******/ })()
;