/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@concretecms/bedrock/assets/express/js/frontend/express-entry-list.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/express/js/frontend/express-entry-list.js ***!
  \********************************************************************************************/
/***/ (function() {

(function (global, $) {
  'use strict';

  function ConcreteExpressEntryList(options) {
    options = options || {};
    options = $.extend({
      bID: 0,
      hideFields: true
    }, options);
    this.options = options;
    this.setupAdvancedSearch();
    this.setupItemsPerPage();
  }
  ConcreteExpressEntryList.prototype.setupItemsPerPage = function () {
    var bID = this.options.bID;
    var $itemsPerPageSelector = $('select[data-express-entry-list-select-items-per-page=' + bID + ']');
    $itemsPerPageSelector.on('change', function () {
      window.location.href = $itemsPerPageSelector.find('option:selected').attr('data-location');
    });
  };
  ConcreteExpressEntryList.prototype.setupAdvancedSearch = function () {
    var bID = this.options.bID;
    var $details = $('div[data-express-entry-list-advanced-search-fields=' + bID + ']');
    $('a[data-express-entry-list-advanced-search]').on('click', function (e) {
      e.preventDefault();
      if ($details.is(':visible')) {
        $(this).removeClass('ccm-block-express-entry-list-advanced-search-open');
        $details.find('input[name=advancedSearchDisplayed]').val('');
        $details.hide();
      } else {
        $(this).addClass('ccm-block-express-entry-list-advanced-search-open');
        $details.find('input[name=advancedSearchDisplayed]').val(1);
        $details.show();
      }
    });
    if (this.options.hideFields) {
      $details.hide();
    } else {
      $details.show();
    }
  };

  // jQuery Plugin
  $.concreteExpressEntryList = function (options) {
    return new ConcreteExpressEntryList(options);
  };
})(this, $);

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
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/express/js/frontend.js ***!
  \*************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_express_entry_list__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/express-entry-list */ "./node_modules/@concretecms/bedrock/assets/express/js/frontend/express-entry-list.js");
/* harmony import */ var _frontend_express_entry_list__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_frontend_express_entry_list__WEBPACK_IMPORTED_MODULE_0__);

})();

/******/ })()
;