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

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/ajax-request/base.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/ajax-request/base.js ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/* eslint-disable no-new, no-unused-vars, camelcase, standard/no-callback-literal, eqeqeq */

/* global ccmi18n, ConcreteEvent, ConcreteAlert */
;

(function (global, $) {
  'use strict';

  function ConcreteAjaxRequest(options) {
    var my = this;
    options = options || {};
    options = $.extend({
      dataType: 'json',
      type: 'post',
      loader: 'standard',
      error: function error(r) {
        my.error(r, my);
      },
      complete: function complete() {
        my.complete(my);
      },
      skipResponseValidation: false
    }, options);
    my.options = options;
    my.execute();
  }

  ConcreteAjaxRequest.prototype = {
    execute: function execute() {
      var my = this;
      var options = my.options;
      var successCallback = options.success;

      options.success = function (r) {
        my.success(r, my, successCallback);
      };

      my.before(my);
      $.ajax(options);
    },
    before: function before(my) {
      if (my.options.loader) {
        $.fn.dialog.showLoader();
      }
    },
    errorResponseToString: function errorResponseToString(r) {
      return ConcreteAjaxRequest.renderErrorResponse(r, true);
    },
    error: function error(r, my) {
      ConcreteEvent.fire('AjaxRequestError', {
        response: r
      });
      ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderErrorResponse(r, true));
    },
    validateResponse: function validateResponse(r, callback) {
      if (r.error) {
        ConcreteEvent.fire('AjaxRequestError', {
          response: r
        });
        ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderJsonError(r), function () {
          if (callback) {
            callback(false, r);
          }
        });
        return false;
      } else if (callback) {
        callback(true, r);
      }

      return true;
    },
    success: function success(r, my, callback) {
      if (my.options.dataType != 'json' || my.options.skipResponseValidation || my.validateResponse(r)) {
        if (callback) {
          callback(r);
        }
      }
    },
    complete: function complete(my) {
      if (my.options.loader) {
        $.fn.dialog.hideLoader();
      }
    }
  }; // static methods

  ConcreteAjaxRequest.renderJsonError = function (json, asHtml) {
    if (!json) {
      return '';
    }

    var toHtml = function toHtml(text, index) {
      if (typeof index === 'number' && $.isArray(json.htmlErrorIndexes) && $.inArray(index, json.htmlErrorIndexes) >= 0) {
        return text;
      }

      return $('<div />').text(text).html().replace(/\n/g, '<br />');
    };

    var result = '';

    if (_typeof(json.error) === 'object' && $.isArray(json.error.trace)) {
      result = '<p class="text-danger"><strong>' + toHtml(json.error.message) + '</strong></p>';
      result += '<p class="text-muted">' + ccmi18n.errorDetails + '</p>';
      result += '<table class="table"><tbody>';

      for (var i = 0, trace; i < json.error.trace.length; i++) {
        trace = json.error.trace[i];
        result += '<tr><td>' + trace.file + '(' + trace.line + '): ' + trace["class"] + '->' + trace["function"] + '<td></tr>';
      }

      result += '</tbody></table>';
    } else if ($.isArray(json.errors) && json.errors.length > 0 && typeof json.errors[0] === 'string') {
      $.each(json.errors, function (index, text) {
        result += '<p class="text-danger"><strong>' + toHtml(text, index) + '</strong></p>';
      });
    } else if (typeof json.error === 'string' && json.error !== '') {
      result = '<p class="text-danger" style="word-break: break-all"><strong>' + toHtml(json.error) + '</strong></p>';
    }

    return result;
  };

  ConcreteAjaxRequest.renderErrorResponse = function (xhr, asHtml) {
    return ConcreteAjaxRequest.renderJsonError(xhr.responseJSON, asHtml) || xhr.responseText;
  };

  ConcreteAjaxRequest.validateResponse = ConcreteAjaxRequest.prototype.validateResponse;
  ConcreteAjaxRequest.errorResponseToString = ConcreteAjaxRequest.prototype.errorResponseToString; // jQuery Plugin

  $.concreteAjax = function (options) {
    new ConcreteAjaxRequest(options);
  };

  global.ConcreteAjaxRequest = ConcreteAjaxRequest;
})(global, jQuery);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/alert.js":
/*!******************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/alert.js ***!
  \******************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var _pnotify__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./pnotify */ "./node_modules/@concretecms/bedrock/assets/cms/js/pnotify.js");
/* harmony import */ var _pnotify_animate__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @pnotify/animate */ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/animate/dist/PNotifyAnimate.js");
/* harmony import */ var _pnotify_animate__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_pnotify_animate__WEBPACK_IMPORTED_MODULE_1__);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/* global ccmi18n */


var modules = new Map([].concat(_toConsumableArray(_pnotify__WEBPACK_IMPORTED_MODULE_0__["default"].defaultModules), [[_pnotify_animate__WEBPACK_IMPORTED_MODULE_1__, {
  inClass: 'fadeIn',
  outClass: 'bounceOutRight'
}]]));
var stackBottomRight = new _pnotify__WEBPACK_IMPORTED_MODULE_0__["default"].Stack({
  dir1: 'up',
  // The primary stacking direction. Can be 'up', 'down', 'right', or 'left'.
  firstpos1: 25,
  // The notices will appear 25 pixels from the bottom of the context.
  spacing1: 15,
  // Number of pixels between notices along dir1.
  dir2: 'left',
  // The secondary stacking direction. Should be a perpendicular direction to dir1.
  firstpos2: 25,
  // The notices will appear 25 pixels from the right of the context.
  spacing2: 15,
  // Number of pixels between notices along dir2.
  push: 'bottom',
  // Push new notices on top of previous ones.
  maxOpen: 3,
  modal: false
});

var ConcreteAlert = /*#__PURE__*/function () {
  function ConcreteAlert() {
    _classCallCheck(this, ConcreteAlert);
  }

  _createClass(ConcreteAlert, null, [{
    key: "dialog",
    value: function dialog(title, message, onCloseFn) {
      var $div = $("<div id=\"ccm-popup-alert\" class=\"ccm-ui\"><div id=\"ccm-popup-alert-message\">".concat(message, "</div></div>"));
      $div.dialog({
        title: title,
        width: 500,
        maxHeight: 500,
        modal: true,
        dialogClass: 'ccm-ui',
        close: function close() {
          $div.remove();

          if (onCloseFn) {
            onCloseFn();
          }
        }
      });
    }
  }, {
    key: "confirm",
    value: function confirm(message, onConfirmation, btnClass, btnText) {
      var $div = $("<div id=\"ccm-popup-confirmation\" class=\"ccm-ui\"><div id=\"ccm-popup-confirmation-message\">".concat(message, "</div>"));
      btnClass = btnClass ? "btn ".concat(btnClass) : 'btn btn-primary';
      btnText = btnText || ccmi18n.go;
      $div.dialog({
        title: ccmi18n.confirm,
        width: 500,
        maxHeight: 500,
        modal: true,
        dialogClass: 'ccm-ui',
        close: function close() {
          $div.remove();
        },
        buttons: [{}],
        open: function open() {
          var $btnPane = $(this).parent().find('.ui-dialog-buttonpane');
          $btnPane.addClass('ccm-ui').html('');
          $btnPane.append("\n                    <button onclick=\"jQuery.fn.dialog.closeTop()\" class=\"btn btn-secondary\">".concat(ccmi18n.cancel, "</button>\n                    <button data-dialog-action=\"submit-confirmation-dialog\" class=\"btn ").concat(btnClass, " float-right\">").concat(btnText, "</button></div>\n                "));
        }
      });
      $div.parent().on('click', 'button[data-dialog-action=submit-confirmation-dialog]', function () {
        if (typeof onConfirmation === 'function') {
          return onConfirmation();
        }
      });
    }
  }, {
    key: "info",
    value: function info(defaults) {
      var options = $.extend({
        type: 'info',
        icon: 'info-circle'
      }, defaults);
      return this.notify(options);
    }
  }, {
    key: "error",
    value: function error(defaults) {
      var options = $.extend({
        type: 'error',
        icon: 'exclamation-circle'
      }, defaults);
      return this.notify(options);
    }
  }, {
    key: "notify",
    value: function notify(defaults) {
      var options = $.extend({
        type: 'success',
        icon: 'check-circle',
        title: false,
        message: false,
        delay: 2000,
        callback: function callback() {}
      }, defaults);
      var notifyOptions = {
        text: options.message,
        textTrusted: true,
        icon: 'fas fa-' + options.icon,
        type: options.type,
        delay: options.delay,
        stack: stackBottomRight,
        modules: modules,
        labels: {
          close: ccmi18n.closeWindow
        }
      };

      if (options.title) {
        notifyOptions.title = options.title;
      }

      if (options.hide === false) {
        notifyOptions.hide = options.hide;
      }

      var notice = _pnotify__WEBPACK_IMPORTED_MODULE_0__["default"].alert(notifyOptions);
      notice.on('pnotify:afterClose', options.callback);
      return notice;
    }
  }]);

  return ConcreteAlert;
}();

global.ConcreteAlert = ConcreteAlert;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/file-manager/file-manager.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/file-manager/file-manager.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

/* global _, ccmi18n, ccmi18n_filemanager, CCM_DISPATCHER_FILENAME, ConcreteAlert, ConcreteAjaxRequest, ConcreteAjaxSearch, ConcreteEvent, ConcreteFileMenu, ConcreteTree */
var ConcreteFileManager = /*#__PURE__*/function () {
  function ConcreteFileManager() {
    _classCallCheck(this, ConcreteFileManager);
  }

  _createClass(ConcreteFileManager, null, [{
    key: "launchDialog",
    value: function launchDialog(callback, opts) {
      var w = '90%';
      var h = '75%';
      var data = {};
      var i;
      var options = $.extend({
        filters: [],
        multipleSelection: false // Multiple selection switch

      }, opts);

      if (options.multipleSelection) {
        data.multipleSelection = true;
      }

      if (options.filters.length > 0) {
        data['field[]'] = [];

        for (i = 0; i < options.filters.length; i++) {
          var filter = $.extend(true, {}, options.filters[i]); // clone

          data['field[]'].push(filter.field);
          delete filter.field;
          $.extend(data, filter); // add all remaining fields to the data
        }
      }

      $.fn.dialog.open({
        width: w,
        height: h,
        href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/search',
        modal: true,
        data: data,
        title: ccmi18n_filemanager.chooseFile,
        onOpen: function onOpen(dialog) {
          ConcreteEvent.unsubscribe('FileManagerSelectFile');
          ConcreteEvent.subscribe('FileManagerSelectFile', function (e, r) {
            var response = r || {};

            if (!options.multipleSelection) {
              response.fID = r.fID[0];
            } else {
              response.fID = r.fID;
            }

            $.fn.dialog.closeTop();
            callback(response);
          });
        }
      });
    }
  }, {
    key: "getFileDetails",
    value: function getFileDetails(fID, callback) {
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/get_json',
        data: {
          fID: fID
        },
        error: function error(r) {
          ConcreteAlert.dialog(ccmi18n.error, r.responseText);
        },
        success: function success(r) {
          callback(r);
        }
      });
    }
  }]);

  return ConcreteFileManager;
}();

window.ConcreteFileManager = ConcreteFileManager;

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/legacy-dialog.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/legacy-dialog.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* eslint-disable no-new, no-unused-vars, camelcase, no-eval, eqeqeq */

/* global NProgress, ccmi18n, ConcreteMenuManager, ConcreteAjaxRequest, ConcreteAlert */
;

(function (global, $) {
  'use strict';
  /* concrete5 wrapper for jQuery UI */

  $.widget('concrete.dialog', $.ui.dialog, {
    _allowInteraction: function _allowInteraction(event) {
      return !!$(event.target).closest('.ccm-interaction-dialog').length || !!$(event.target).closest('.cke_dialog').length || this._super(event);
    }
  });

  function onDialogCreate($dialog) {
    $dialog.parent().addClass('animated fadeIn');
  }

  function onDialogOpen($dialog) {
    /*
     * This code causes problems with dialogs that have long dropdowns in them like the files advanced
     * search. Commenting out for now.
     */

    /*
    var nd = $('.ui-dialog').length
    if (nd == 1) {
        $('body').attr('data-last-overflow', $('body').css('overflow'))
        $('body').css('overflow', 'hidden')
     */
    var overlays = $('.ui-widget-overlay').length;

    if (overlays == 1) {
      $('.ui-widget-overlay').addClass('ui-widget-overlay-active');
    }

    var $close = $dialog.parent().find('.ui-dialog-titlebar-close');
    $close.html('<svg><use xlink:href="#icon-dialog-close" /></svg>');
    $.fn.dialog.activateDialogContents($dialog); // on some brother (eg: Chrome) the resizable get hidden because the button pane
    // in on top of it, here is a fix for this:

    if ($dialog.jqdialog('option', 'resizable')) {
      var $wrapper = $($dialog.parent());
      var z = parseInt($wrapper.find('.ui-dialog-buttonpane').css('z-index'));
      $wrapper.find('.ui-resizable-handle').css('z-index', z + 1000);
    }
  }

  function fixDialogButtons($dialog) {
    var $ccmButtons = $dialog.find('.dialog-buttons').eq(0);

    if ($ccmButtons.length === 0) {
      return;
    }

    if ($.trim($ccmButtons.html()).length === 0) {
      return;
    }

    var $dialogParent = $dialog.parent();

    if ($dialogParent.find('.ui-dialog-buttonset').length !== 0) {
      return;
    }

    $dialog.jqdialog('option', 'buttons', [{}]);
    $dialogParent.find('.ui-dialog-buttonset').remove();
    /*
     * This keeps our buttons left and right, but we're not sure we want that, so let's not do that yet.
     */
    // $ccmButtons.find('[data-dialog-action=cancel]').addClass('mr-auto')

    $ccmButtons.children().appendTo($dialogParent.find('.ui-dialog-buttonpane').empty());
  }

  $.widget.bridge('jqdialog', $.concrete.dialog); // wrap our old dialog function in the new dialog() function.

  $.fn.dialog = function () {
    // Pass this over to jQuery UI Dialog in a few circumstances
    switch (arguments.length) {
      case 0:
        if ($(this).is('div')) {
          $(this).jqdialog();
          return;
        }

        break;

      case 1:
        var arg = arguments[0];

        if ($.isPlainObject(arg)) {
          var originalOpen = arg.open || null;
          var originalCreate = arg.create || null;

          arg.create = function (e) {
            onDialogCreate($(this));

            if (originalCreate) {
              originalCreate.call(this);
            }
          };

          arg.dialogClass = 'ccm-ui';

          arg.open = function (e, ui) {
            onDialogOpen($(this));

            if (originalOpen) {
              originalOpen.call(this, e, ui);
            }
          };
        }

        $.fn.jqdialog.call($(this), arg);
        return;

      default:
        $.fn.jqdialog.apply($(this), arguments);
        return;
    } // LEGACY SUPPORT


    return $(this).each(function () {
      $(this).unbind('click.make-dialog').bind('click.make-dialog', function (e) {
        e.preventDefault();

        if ($(this).hasClass('ccm-dialog-launching')) {
          return;
        }

        $(this).addClass('ccm-dialog-launching');
        var href = $(this).attr('href');
        var width = $(this).attr('dialog-width');
        var height = $(this).attr('dialog-height');
        var title = $(this).attr('dialog-title');
        var onOpen = $(this).attr('dialog-on-open');
        var dialogClass = $(this).attr('dialog-class');
        var onDestroy = $(this).attr('dialog-on-destroy');
        /*
         * no longer necessary. we auto detect
            var appendButtons = $(this).attr('dialog-append-buttons');
        */

        var onClose = $(this).attr('dialog-on-close');
        var onDirectClose = $(this).attr('dialog-on-direct-close');
        var obj = {
          modal: true,
          href: href,
          width: width,
          height: height,
          title: title,
          onOpen: onOpen,
          onDestroy: onDestroy,
          dialogClass: dialogClass,
          onClose: onClose,
          onDirectClose: onDirectClose,
          launcher: $(this)
        };
        $.fn.dialog.open(obj);
      });
    });
  };

  $.fn.dialog.close = function (num) {
    num++;
    $('#ccm-dialog-content' + num).jqdialog('close');
  };

  $.fn.dialog.open = function (options) {
    if (typeof ConcreteMenu !== 'undefined') {
      var activeMenu = ConcreteMenuManager.getActiveMenu();

      if (activeMenu) {
        activeMenu.hide();
      }
    }

    var w;

    if (typeof options.width === 'string') {
      if (options.width == 'auto') {
        w = 'auto';
      } else {
        if (options.width.indexOf('%', 0) > 0) {
          w = options.width.replace('%', '');
          w = $(window).width() * (w / 100);
          w = w + 50;
        } else {
          w = parseInt(options.width) + 50;
        }
      }
    } else if (options.width) {
      w = parseInt(options.width) + 50;
    } else {
      w = 550;
    }

    var h;

    if (typeof options.height === 'string') {
      if (options.height == 'auto') {
        h = 'auto';
      } else {
        if (options.height.indexOf('%', 0) > 0) {
          h = options.height.replace('%', '');
          h = $(window).height() * (h / 100);
          h = h + 100;
        } else {
          h = parseInt(options.height) + 100;
        }
      }
    } else if (options.height) {
      h = parseInt(options.height) + 100;
    } else {
      h = 400;
    }

    if (h !== 'auto' && h > $(window).height()) {
      h = $(window).height();
    }

    options.width = w;
    options.height = h;
    var defaults = {
      modal: true,
      escapeClose: true,
      width: w,
      height: h,
      type: 'GET',
      dialogClass: 'ccm-ui',
      resizable: true,
      create: function create() {
        onDialogCreate($(this));
      },
      open: function open() {
        // jshint -W061
        var $dialog = $(this);
        onDialogOpen($dialog);

        if (typeof options.onOpen !== 'undefined') {
          if (typeof options.onOpen === 'function') {
            options.onOpen($dialog);
          } else {
            eval(options.onOpen);
          }
        }

        if (options.launcher) {
          options.launcher.removeClass('ccm-dialog-launching');
        }
      },
      beforeClose: function beforeClose() {
        var nd = $('.ui-dialog:visible').length;

        if (nd == 1) {
          $('body').css('overflow', $('body').attr('data-last-overflow'));
        }
      },
      close: function close(ev, u) {
        // jshint -W061
        if (!options.element) {
          $(this).jqdialog('destroy').remove();
        }

        if (typeof options.onClose !== 'undefined') {
          if (typeof options.onClose === 'function') {
            options.onClose($(this));
          } else {
            eval(options.onClose);
          }
        }

        if (typeof options.onDirectClose !== 'undefined' && ev.handleObj && (ev.handleObj.type == 'keydown' || ev.handleObj.type == 'click')) {
          if (typeof options.onDirectClose === 'function') {
            options.onDirectClose();
          } else {
            eval(options.onDirectClose);
          }
        }

        if (typeof options.onDestroy !== 'undefined') {
          if (typeof options.onDestroy === 'function') {
            options.onDestroy();
          } else {
            eval(options.onDestroy);
          }
        }
      }
    };
    var finalSettings = {
      autoOpen: false,
      data: {}
    };
    $.extend(finalSettings, defaults, options);

    if (finalSettings.element) {
      $(finalSettings.element).jqdialog(finalSettings).jqdialog();
      $(finalSettings.element).jqdialog('open');
    } else {
      $.fn.dialog.showLoader();
      $.ajax({
        type: finalSettings.type,
        url: finalSettings.href,
        data: finalSettings.data,
        success: function success(r) {
          $.fn.dialog.hideLoader(); // note the order here is very important in order to actually run javascript in
          // the pages we load while having access to the jqdialog object.
          // Ensure that the dialog is open prior to evaluating javascript.

          $('<div />').jqdialog(finalSettings).html(r).jqdialog('open');
        },
        error: function error(xhr, status, _error) {
          $.fn.dialog.hideLoader();
          ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderErrorResponse(xhr, true));
        }
      });
    }
  };

  $.fn.dialog.activateDialogContents = function ($dialog) {
    // handle buttons
    $dialog.find('button[data-dialog-action=cancel]').on('click', function () {
      $.fn.dialog.closeTop();
    });
    $dialog.find('[data-dialog-form]').each(function () {
      var $form = $(this);
      var options = {};

      if ($form.attr('data-dialog-form-processing') == 'progressive') {
        options.progressiveOperation = true;
      }

      $form.concreteAjaxForm(options);
    });
    $dialog.find('button[data-dialog-action=submit]').on('click', function () {
      $dialog.find('[data-dialog-form]').submit();
    });
    fixDialogButtons($dialog); // make dialogs

    $dialog.find('.dialog-launch').dialog(); // automated close handling

    $dialog.find('.ccm-dialog-close').on('click', function () {
      $dialog.dialog('close');
    });
    $dialog.find('.launch-tooltip').tooltip({
      container: '#ccm-tooltip-holder'
    }); // help handling

    if ($dialog.find('.dialog-help').length > 0) {
      $dialog.find('.dialog-help').hide();
      var helpContent = $dialog.find('.dialog-help').html();
      var helpText;

      if (ccmi18n.helpPopup) {
        helpText = ccmi18n.helpPopup;
      } else {
        helpText = 'Help';
      }

      var button = $('<button class="ui-dialog-titlebar-help ccm-menu-help-trigger"><svg><use xlink:href="#icon-dialog-help" /></svg></button>');
      var container = $('#ccm-tooltip-holder');
      button.insertBefore($dialog.parent().find('.ui-dialog-titlebar-close'));
      button.popover({
        content: function content() {
          return helpContent;
        },
        placement: 'bottom',
        html: true,
        container: container,
        trigger: 'click'
      });
      button.on('shown.bs.popover', function () {
        var _binding = function binding() {
          button.popover('hide', button);
          _binding = $.noop;
        };

        button.on('hide.bs.popover', function (event) {
          button.unbind(event);
          _binding = $.noop;
        });
        $('body').mousedown(function (e) {
          if ($(e.target).closest(container).length || $(e.target).closest(button).length) {
            return;
          }

          $(this).unbind(e);

          _binding();
        });
      });
    }
  };

  $.fn.dialog.getTop = function () {
    var nd = $('.ui-dialog:visible').length;
    return $($('.ui-dialog:visible')[nd - 1]).find('.ui-dialog-content');
  };

  $.fn.dialog.replaceTop = function (html) {
    var $dialog = $.fn.dialog.getTop();
    $dialog.html(html);
    $.fn.dialog.activateDialogContents($dialog);
  };

  $.fn.dialog.showLoader = function (text) {
    NProgress.start();
  };

  $.fn.dialog.hideLoader = function () {
    NProgress.done();
  };

  $.fn.dialog.closeTop = function () {
    var $dialog = $.fn.dialog.getTop();
    $dialog.jqdialog('close');
  };

  $.fn.dialog.closeAll = function () {
    $($('.ui-dialog-content').get().reverse()).jqdialog('close');
  };

  $.ui.dialog.prototype._focusTabbable = $.noop;
})(window, jQuery); // eslint-disable-line semi

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/pnotify.js":
/*!********************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/pnotify.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _pnotify_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @pnotify/core */ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/core/dist/PNotify.js");
/* harmony import */ var _pnotify_core__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_pnotify_core__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _pnotify_mobile__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @pnotify/mobile */ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/mobile/dist/PNotifyMobile.js");
/* harmony import */ var _pnotify_mobile__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_pnotify_mobile__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _pnotify_font_awesome5_fix__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @pnotify/font-awesome5-fix */ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/font-awesome5-fix/dist/PNotifyFontAwesome5Fix.js");
/* harmony import */ var _pnotify_font_awesome5_fix__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_pnotify_font_awesome5_fix__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _pnotify_font_awesome5__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @pnotify/font-awesome5 */ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/font-awesome5/dist/PNotifyFontAwesome5.js");
/* harmony import */ var _pnotify_font_awesome5__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_pnotify_font_awesome5__WEBPACK_IMPORTED_MODULE_3__);




_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaultModules"].set(_pnotify_mobile__WEBPACK_IMPORTED_MODULE_1__, {});
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaultModules"].set(_pnotify_font_awesome5_fix__WEBPACK_IMPORTED_MODULE_2__, {});
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaultModules"].set(_pnotify_font_awesome5__WEBPACK_IMPORTED_MODULE_3__, {});
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaults"].styling = {
  prefix: 'ccm-notification',
  container: 'ccm-notification',
  notice: 'ccm-notification-warning',
  info: 'ccm-notification-info',
  success: 'ccm-notification-success',
  error: 'ccm-notification-danger',
  closer: 'ccm-notification-closer',
  // Confirm Module
  'action-bar': 'ccm-notification-ml',
  'prompt-bar': 'ccm-notification-ml',
  btn: 'btn mx-1',
  'btn-primary': 'btn-primary',
  'btn-secondary': 'btn-secondary',
  input: 'form-control'
};
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaults"].addClass = 'ccm-ui';
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaults"].closerHover = false;
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaults"].sticker = false;
_pnotify_core__WEBPACK_IMPORTED_MODULE_0__["defaults"].width = '360px';
/* harmony default export */ __webpack_exports__["default"] = (_pnotify_core__WEBPACK_IMPORTED_MODULE_0__);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/documents/js/frontend.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/documents/js/frontend.js ***!
  \***************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! nprogress */ "./node_modules/@concretecms/bedrock/node_modules/nprogress/nprogress.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(nprogress__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery_ui_ui_widgets_dialog__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery-ui/ui/widgets/dialog */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/dialog.js");
/* harmony import */ var jquery_ui_ui_widgets_dialog__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery_ui_ui_widgets_dialog__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _cms_js_legacy_dialog__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../cms/js/legacy-dialog */ "./node_modules/@concretecms/bedrock/assets/cms/js/legacy-dialog.js");
/* harmony import */ var _cms_js_legacy_dialog__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_cms_js_legacy_dialog__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _cms_js_ajax_request_base__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../cms/js/ajax-request/base */ "./node_modules/@concretecms/bedrock/assets/cms/js/ajax-request/base.js");
/* harmony import */ var _cms_js_ajax_request_base__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_cms_js_ajax_request_base__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _cms_js_alert__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../cms/js/alert */ "./node_modules/@concretecms/bedrock/assets/cms/js/alert.js");
/* harmony import */ var _frontend_document_library__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./frontend/document-library */ "./node_modules/@concretecms/bedrock/assets/documents/js/frontend/document-library.js");






window.NProgress = nprogress__WEBPACK_IMPORTED_MODULE_0___default.a;

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/documents/js/frontend/document-library.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/documents/js/frontend/document-library.js ***!
  \********************************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var blueimp_file_upload__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! blueimp-file-upload */ "./node_modules/@concretecms/bedrock/node_modules/blueimp-file-upload/js/jquery.fileupload.js");
/* harmony import */ var blueimp_file_upload__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(blueimp_file_upload__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _cms_js_file_manager_file_manager__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../cms/js/file-manager/file-manager */ "./node_modules/@concretecms/bedrock/assets/cms/js/file-manager/file-manager.js");
/* harmony import */ var _cms_js_file_manager_file_manager__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_cms_js_file_manager_file_manager__WEBPACK_IMPORTED_MODULE_1__);



(function (global, $) {
  'use strict';

  function ConcreteDocumentLibrary(options) {
    options = options || {};
    options = $.extend({
      bID: 0,
      allowFileUploading: false,
      allowInPageFileManagement: false
    }, options);
    this.options = options;
    this.setupDetails();
    this.setupAdvancedSearch();
    this.setupEditProperties();

    if (options.allowFileUploading) {
      this.setupFileUploading();
    }
  }

  ConcreteDocumentLibrary.prototype.setupFileUploading = function () {
    var obj = this;
    $('a[data-document-library-add-files]').on('click', function (e) {
      e.preventDefault();
      var bID = obj.options.bID;
      var $details = $('div[data-document-library-add-files=' + bID + ']');
      var $uploader = $('div[data-document-library-add-files=' + bID + ']');
      var $pending = $uploader.find('.ccm-block-document-library-add-files-pending');
      var $progress = $uploader.find('.ccm-block-document-library-add-files-uploading');
      var uploadAction = $details.attr('data-document-library-upload-action');
      var securityToken = $uploader.find('input[name=ccm_token]').val();
      var errors = [];
      var files = [];

      if ($details.is(':visible')) {
        $uploader.fileupload('destroy');
        $(this).removeClass('ccm-block-document-library-add-files-open');
        $details.hide();
      } else {
        $(this).addClass('ccm-block-document-library-add-files-open');
        $details.show();
        $uploader.fileupload({
          url: uploadAction,
          dataType: 'json',
          formData: {
            ccm_token: securityToken
          },
          error: function error(r) {
            var message = r.responseText;

            try {
              message = jQuery.parseJSON(message).errors;
              var name = this.files[0].name;

              _(message).each(function (error) {
                errors.push({
                  name: name,
                  error: error
                });
              });
            } catch (e) {}
          },
          start: function start() {
            $pending.hide();
            $progress.show();
            errors = [];
          },
          done: function done(e, data) {
            files.push(data.result.files[0]);
          },
          stop: function stop() {
            if (obj.options.allowInPageFileManagement) {
              $progress.hide();
              $pending.show();

              if (errors.length) {
                var str = '';
                $.each(errors, function (i, o) {
                  str += o.error + '\n';
                });
                window.alert(str);
              } else {
                ConcreteEvent.unsubscribe('FileManagerUploadCompleteDialogClose.documentLibrary');
                ConcreteEvent.subscribe('FileManagerUploadCompleteDialogClose.documentLibrary', function (e, data) {
                  window.location.reload();
                });
                ConcreteFileManager.launchUploadCompleteDialog(files);
                files = [];
              }
            } else {
              window.location.reload();
            }
          }
        });
      }
    });
  };

  ConcreteDocumentLibrary.prototype.setupDetails = function () {
    $('a[data-document-library-show-details]').on('click', function (e) {
      e.preventDefault();
      var fID = $(this).attr('data-document-library-show-details');
      var $details = $(this).closest('table').find('tr[data-document-library-details=' + fID + ']');

      if ($details.is(':visible')) {
        $(this).removeClass('ccm-block-document-library-details-open');
        $details.hide();
      } else {
        $(this).addClass('ccm-block-document-library-details-open');
        $details.show();
      }
    });
  };

  ConcreteDocumentLibrary.prototype.setupAdvancedSearch = function () {
    $('a[data-document-library-advanced-search]').on('click', function (e) {
      e.preventDefault();
      var bID = $(this).attr('data-document-library-advanced-search');
      var $details = $('div[data-document-library-advanced-search-fields=' + bID + ']');

      if ($details.is(':visible')) {
        $(this).removeClass('ccm-block-document-library-advanced-search-open');
        $details.find('input[name=advancedSearchDisplayed]').val('');
        $details.hide();
      } else {
        $(this).addClass('ccm-block-document-library-advanced-search-open');
        $details.find('input[name=advancedSearchDisplayed]').val(1);
        $details.show();
      }
    });
  };

  ConcreteDocumentLibrary.prototype.setupEditProperties = function () {
    $('a[data-document-library-edit-properties]').on('click', function (e) {
      e.preventDefault();
      var fID = $(this).attr('data-document-library-edit-properties');
      jQuery.fn.dialog.open({
        href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/properties?fID=' + fID,
        modal: true,
        width: 680,
        height: 450,
        title: ccmi18n_filemanager.properties,
        onClose: function onClose() {
          window.location.reload();
        }
      });
    });
  }; // jQuery Plugin


  $.concreteDocumentLibrary = function (options) {
    return new ConcreteDocumentLibrary(options);
  };
})(undefined, $);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/animate/dist/PNotifyAnimate.js":
/*!************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/@pnotify/animate/dist/PNotifyAnimate.js ***!
  \************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (t, n) {
  "object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module ? n(exports) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (n),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(this, function (t) {
  "use strict";

  function n(t) {
    return (n = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function e(t, n) {
    if (!(t instanceof n)) throw new TypeError("Cannot call a class as a function");
  }

  function r(t, n) {
    for (var e = 0; e < n.length; e++) {
      var r = n[e];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function o(t) {
    return (o = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function a(t, n) {
    return (a = Object.setPrototypeOf || function (t, n) {
      return t.__proto__ = n, t;
    })(t, n);
  }

  function i() {
    if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
    if (Reflect.construct.sham) return !1;
    if ("function" == typeof Proxy) return !0;

    try {
      return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
    } catch (t) {
      return !1;
    }
  }

  function u(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function f(t, n) {
    return !n || "object" != _typeof(n) && "function" != typeof n ? u(t) : n;
  }

  function c(t) {
    return function (t) {
      if (Array.isArray(t)) return l(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(t) || function (t, n) {
      if (!t) return;
      if ("string" == typeof t) return l(t, n);
      var e = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === e && t.constructor && (e = t.constructor.name);
      if ("Map" === e || "Set" === e) return Array.from(e);
      if ("Arguments" === e || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e)) return l(t, n);
    }(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function l(t, n) {
    (null == n || n > t.length) && (n = t.length);

    for (var e = 0, r = new Array(n); e < n; e++) {
      r[e] = t[e];
    }

    return r;
  }

  function s() {}

  function p(t) {
    return t();
  }

  function m() {
    return Object.create(null);
  }

  function d(t) {
    t.forEach(p);
  }

  function y(t) {
    return "function" == typeof t;
  }

  function h(t, e) {
    return t != t ? e == e : t !== e || t && "object" === n(t) || "function" == typeof t;
  }

  function v(t) {
    t.parentNode.removeChild(t);
  }

  function g(t) {
    return Array.from(t.childNodes);
  }

  var b;

  function $(t) {
    b = t;
  }

  function _(t) {
    (function () {
      if (!b) throw new Error("Function called outside component initialization");
      return b;
    })().$$.on_destroy.push(t);
  }

  var C = [],
      O = [],
      w = [],
      A = [],
      S = Promise.resolve(),
      x = !1;

  function j(t) {
    w.push(t);
  }

  var P = !1,
      E = new Set();

  function k() {
    if (!P) {
      P = !0;

      do {
        for (var t = 0; t < C.length; t += 1) {
          var n = C[t];
          $(n), I(n.$$);
        }

        for (C.length = 0; O.length;) {
          O.pop()();
        }

        for (var e = 0; e < w.length; e += 1) {
          var r = w[e];
          E.has(r) || (E.add(r), r());
        }

        w.length = 0;
      } while (C.length);

      for (; A.length;) {
        A.pop()();
      }

      x = !1, P = !1, E.clear();
    }
  }

  function I(t) {
    if (null !== t.fragment) {
      t.update(), d(t.before_update);
      var n = t.dirty;
      t.dirty = [-1], t.fragment && t.fragment.p(t.ctx, n), t.after_update.forEach(j);
    }
  }

  var R = new Set();

  function M(t, n) {
    t && t.i && (R["delete"](t), t.i(n));
  }

  function D(t, n, e) {
    var r = t.$$,
        o = r.fragment,
        a = r.on_mount,
        i = r.on_destroy,
        u = r.after_update;
    o && o.m(n, e), j(function () {
      var n = a.map(p).filter(y);
      i ? i.push.apply(i, c(n)) : d(n), t.$$.on_mount = [];
    }), u.forEach(j);
  }

  function N(t, n) {
    -1 === t.$$.dirty[0] && (C.push(t), x || (x = !0, S.then(k)), t.$$.dirty.fill(0)), t.$$.dirty[n / 31 | 0] |= 1 << n % 31;
  }

  var T = {
    inClass: null,
    outClass: null
  };

  function q(t, n, e) {
    var r = n.self,
        o = void 0 === r ? null : r,
        a = n.inClass,
        i = void 0 === a ? T.inClass : a,
        u = n.outClass,
        f = void 0 === u ? T.outClass : u,
        c = o.animation,
        l = o.animateIn,
        s = o.animateOut;

    function p(t, n) {
      var e;
      o.setAnimating("in");

      var r = function r(n) {
        n && o.refs.elem && n.target !== o.refs.elem || (e(), o.setAnimatingClass("pnotify-in animated"), t && t.call(), o.setAnimating(!1));
      };

      e = o.on("animationend", r), n ? r() : o.setAnimatingClass("pnotify-in animated ".concat(i || f));
    }

    function m(t, n) {
      var e;
      o.setAnimating("out");

      var r = function r(n) {
        n && o.refs.elem && n.target !== o.refs.elem || (e(), o.setAnimatingClass("animated"), t && t.call(), o.setAnimating && o.setAnimating(!1));
      };

      e = o.on("animationend", r), n ? r() : o.setAnimatingClass("pnotify-in animated ".concat(f || i));
    }

    return _(function () {
      o.$set({
        animation: c,
        animateIn: l,
        animateOut: s
      });
    }), o.on("pnotify:update", function () {
      if (o.refs.elem) {
        var t = 250;
        "slow" === o.animateSpeed ? t = 400 : "fast" === o.animateSpeed ? t = 100 : o.animateSpeed > 0 && (t = o.animateSpeed), t /= 1e3, o.refs.elem.style.animationDuration !== "".concat(t, "s") && e(0, o.refs.elem.style.animationDuration = "".concat(t, "s"), o);
      }
    }), o.attention = function (t, n) {
      var e;
      e = o.on("animationend", function () {
        e(), o.removeModuleClass("container", "animated", t), n && n.call(o);
      }), o.addModuleClass("container", "animated", t);
    }, t.$set = function (t) {
      "self" in t && e(0, o = t.self), "inClass" in t && e(1, i = t.inClass), "outClass" in t && e(2, f = t.outClass);
    }, t.$$.update = function () {
      7 & t.$$.dirty && (i || f ? o.$set({
        animation: "none",
        animateIn: p,
        animateOut: m
      }) : o.$set({
        animation: c,
        animateIn: l,
        animateOut: s
      }));
    }, [o, i, f];
  }

  var z = function (t) {
    !function (t, n) {
      if ("function" != typeof n && null !== n) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(n && n.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), n && a(t, n);
    }(c, t);
    var n,
        r = (n = c, function () {
      var t,
          e = o(n);

      if (i()) {
        var r = o(this).constructor;
        t = Reflect.construct(e, arguments, r);
      } else t = e.apply(this, arguments);

      return f(this, t);
    });

    function c(t) {
      var n;
      return e(this, c), function (t, n, e, r, o, a) {
        var i = arguments.length > 6 && void 0 !== arguments[6] ? arguments[6] : [-1],
            u = b;
        $(t);
        var f = n.props || {},
            c = t.$$ = {
          fragment: null,
          ctx: null,
          props: a,
          update: s,
          not_equal: o,
          bound: m(),
          on_mount: [],
          on_destroy: [],
          before_update: [],
          after_update: [],
          context: new Map(u ? u.$$.context : []),
          callbacks: m(),
          dirty: i
        },
            l = !1;

        if (c.ctx = e ? e(t, f, function (n, e) {
          var r = !(arguments.length <= 2) && arguments.length - 2 ? arguments.length <= 2 ? void 0 : arguments[2] : e;
          return c.ctx && o(c.ctx[n], c.ctx[n] = r) && (c.bound[n] && c.bound[n](r), l && N(t, n)), e;
        }) : [], c.update(), l = !0, d(c.before_update), c.fragment = !!r && r(c.ctx), n.target) {
          if (n.hydrate) {
            var p = g(n.target);
            c.fragment && c.fragment.l(p), p.forEach(v);
          } else c.fragment && c.fragment.c();

          n.intro && M(t.$$.fragment), D(t, n.target, n.anchor), k();
        }

        $(u);
      }(u(n = r.call(this)), t, q, null, h, {
        self: 0,
        inClass: 1,
        outClass: 2
      }), n;
    }

    return c;
  }(function () {
    function t() {
      e(this, t);
    }

    var n, o, a;
    return n = t, (o = [{
      key: "$destroy",
      value: function value() {
        var t, n;
        t = 1, null !== (n = this.$$).fragment && (d(n.on_destroy), n.fragment && n.fragment.d(t), n.on_destroy = n.fragment = null, n.ctx = []), this.$destroy = s;
      }
    }, {
      key: "$on",
      value: function value(t, n) {
        var e = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
        return e.push(n), function () {
          var t = e.indexOf(n);
          -1 !== t && e.splice(t, 1);
        };
      }
    }, {
      key: "$set",
      value: function value() {}
    }]) && r(n.prototype, o), a && r(n, a), t;
  }());

  t["default"] = z, t.defaults = T, t.position = "PrependContainer", Object.defineProperty(t, "__esModule", {
    value: !0
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/core/dist/PNotify.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/@pnotify/core/dist/PNotify.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (t, e) {
  "object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module ? e(exports) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (e),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(this, function (t) {
  "use strict";

  function e(t) {
    return (e = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function n(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function i(t, e) {
    for (var n = 0; n < e.length; n++) {
      var i = e[n];
      i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(t, i.key, i);
    }
  }

  function o(t, e, n) {
    return e && i(t.prototype, e), n && i(t, n), t;
  }

  function r(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  function s(t, e) {
    var n = Object.keys(t);

    if (Object.getOwnPropertySymbols) {
      var i = Object.getOwnPropertySymbols(t);
      e && (i = i.filter(function (e) {
        return Object.getOwnPropertyDescriptor(t, e).enumerable;
      })), n.push.apply(n, i);
    }

    return n;
  }

  function a(t) {
    for (var e = 1; e < arguments.length; e++) {
      var n = null != arguments[e] ? arguments[e] : {};
      e % 2 ? s(Object(n), !0).forEach(function (e) {
        r(t, e, n[e]);
      }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(n)) : s(Object(n)).forEach(function (e) {
        Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(n, e));
      });
    }

    return t;
  }

  function c(t) {
    return (c = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function u(t, e) {
    return (u = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function l() {
    if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
    if (Reflect.construct.sham) return !1;
    if ("function" == typeof Proxy) return !0;

    try {
      return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
    } catch (t) {
      return !1;
    }
  }

  function f(t, e, n) {
    return (f = l() ? Reflect.construct : function (t, e, n) {
      var i = [null];
      i.push.apply(i, e);
      var o = new (Function.bind.apply(t, i))();
      return n && u(o, n.prototype), o;
    }).apply(null, arguments);
  }

  function d(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function h(t, e) {
    return !e || "object" != _typeof(e) && "function" != typeof e ? d(t) : e;
  }

  function p(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" == typeof Symbol || !(Symbol.iterator in Object(t))) return;
      var n = [],
          i = !0,
          o = !1,
          r = void 0;

      try {
        for (var s, a = t[Symbol.iterator](); !(i = (s = a.next()).done) && (n.push(s.value), !e || n.length !== e); i = !0) {
          ;
        }
      } catch (t) {
        o = !0, r = t;
      } finally {
        try {
          i || null == a["return"] || a["return"]();
        } finally {
          if (o) throw r;
        }
      }

      return n;
    }(t, e) || v(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function m(t) {
    return function (t) {
      if (Array.isArray(t)) return y(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(t) || v(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function v(t, e) {
    if (t) {
      if ("string" == typeof t) return y(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(n) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? y(t, e) : void 0;
    }
  }

  function y(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, i = new Array(e); n < e; n++) {
      i[n] = t[n];
    }

    return i;
  }

  function g() {}

  function $(t, e) {
    for (var n in e) {
      t[n] = e[n];
    }

    return t;
  }

  function _(t) {
    return t();
  }

  function k() {
    return Object.create(null);
  }

  function x(t) {
    t.forEach(_);
  }

  function b(t) {
    return "function" == typeof t;
  }

  function w(t, n) {
    return t != t ? n == n : t !== n || t && "object" === e(t) || "function" == typeof t;
  }

  function O(t, e) {
    t.appendChild(e);
  }

  function C(t, e, n) {
    t.insertBefore(e, n || null);
  }

  function M(t) {
    t.parentNode.removeChild(t);
  }

  function T(t) {
    return document.createElement(t);
  }

  function H(t) {
    return document.createTextNode(t);
  }

  function E() {
    return H(" ");
  }

  function S() {
    return H("");
  }

  function N(t, e, n, i) {
    return t.addEventListener(e, n, i), function () {
      return t.removeEventListener(e, n, i);
    };
  }

  function L(t, e, n) {
    null == n ? t.removeAttribute(e) : t.getAttribute(e) !== n && t.setAttribute(e, n);
  }

  function A(t) {
    return Array.from(t.childNodes);
  }

  function j(t, e) {
    e = "" + e, t.data !== e && (t.data = e);
  }

  var P,
      W = function () {
    function t(e) {
      var i = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : null;
      n(this, t), this.e = T("div"), this.a = i, this.u(e);
    }

    return o(t, [{
      key: "m",
      value: function value(t) {
        for (var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : null, n = 0; n < this.n.length; n += 1) {
          C(t, this.n[n], e);
        }

        this.t = t;
      }
    }, {
      key: "u",
      value: function value(t) {
        this.e.innerHTML = t, this.n = Array.from(this.e.childNodes);
      }
    }, {
      key: "p",
      value: function value(t) {
        this.d(), this.u(t), this.m(this.t, this.a);
      }
    }, {
      key: "d",
      value: function value() {
        this.n.forEach(M);
      }
    }]), t;
  }();

  function R(t) {
    P = t;
  }

  function I() {
    if (!P) throw new Error("Function called outside component initialization");
    return P;
  }

  function D() {
    var t = I();
    return function (e, n) {
      var i = t.$$.callbacks[e];

      if (i) {
        var o = function (t, e) {
          var n = document.createEvent("CustomEvent");
          return n.initCustomEvent(t, !1, !1, e), n;
        }(e, n);

        i.slice().forEach(function (e) {
          e.call(t, o);
        });
      }
    };
  }

  function F(t, e) {
    var n = t.$$.callbacks[e.type];
    n && n.slice().forEach(function (t) {
      return t(e);
    });
  }

  var q = [],
      B = [],
      z = [],
      U = [],
      G = Promise.resolve(),
      J = !1;

  function K() {
    J || (J = !0, G.then(Z));
  }

  function Q() {
    return K(), G;
  }

  function V(t) {
    z.push(t);
  }

  var X = !1,
      Y = new Set();

  function Z() {
    if (!X) {
      X = !0;

      do {
        for (var t = 0; t < q.length; t += 1) {
          var e = q[t];
          R(e), tt(e.$$);
        }

        for (q.length = 0; B.length;) {
          B.pop()();
        }

        for (var n = 0; n < z.length; n += 1) {
          var i = z[n];
          Y.has(i) || (Y.add(i), i());
        }

        z.length = 0;
      } while (q.length);

      for (; U.length;) {
        U.pop()();
      }

      J = !1, X = !1, Y.clear();
    }
  }

  function tt(t) {
    if (null !== t.fragment) {
      t.update(), x(t.before_update);
      var e = t.dirty;
      t.dirty = [-1], t.fragment && t.fragment.p(t.ctx, e), t.after_update.forEach(V);
    }
  }

  var et,
      nt = new Set();

  function it() {
    et = {
      r: 0,
      c: [],
      p: et
    };
  }

  function ot() {
    et.r || x(et.c), et = et.p;
  }

  function rt(t, e) {
    t && t.i && (nt["delete"](t), t.i(e));
  }

  function st(t, e, n, i) {
    if (t && t.o) {
      if (nt.has(t)) return;
      nt.add(t), et.c.push(function () {
        nt["delete"](t), i && (n && t.d(1), i());
      }), t.o(e);
    }
  }

  var at = "undefined" != typeof window ? window : global;

  function ct(t, e) {
    st(t, 1, 1, function () {
      e["delete"](t.key);
    });
  }

  function ut(t, e, n, i, o, r, s, a, c, u, l, f) {
    for (var d = t.length, h = r.length, p = d, m = {}; p--;) {
      m[t[p].key] = p;
    }

    var v = [],
        y = new Map(),
        g = new Map();

    for (p = h; p--;) {
      var $ = f(o, r, p),
          _ = n($),
          k = s.get(_);

      k ? i && k.p($, e) : (k = u(_, $)).c(), y.set(_, v[p] = k), _ in m && g.set(_, Math.abs(p - m[_]));
    }

    var x = new Set(),
        b = new Set();

    function w(t) {
      rt(t, 1), t.m(a, l, s.has(t.key)), s.set(t.key, t), l = t.first, h--;
    }

    for (; d && h;) {
      var O = v[h - 1],
          C = t[d - 1],
          M = O.key,
          T = C.key;
      O === C ? (l = O.first, d--, h--) : y.has(T) ? !s.has(M) || x.has(M) ? w(O) : b.has(T) ? d-- : g.get(M) > g.get(T) ? (b.add(M), w(O)) : (x.add(T), d--) : (c(C, s), d--);
    }

    for (; d--;) {
      var H = t[d];
      y.has(H.key) || c(H, s);
    }

    for (; h;) {
      w(v[h - 1]);
    }

    return v;
  }

  function lt(t, e) {
    for (var n = {}, i = {}, o = {
      $$scope: 1
    }, r = t.length; r--;) {
      var s = t[r],
          a = e[r];

      if (a) {
        for (var c in s) {
          c in a || (i[c] = 1);
        }

        for (var u in a) {
          o[u] || (n[u] = a[u], o[u] = 1);
        }

        t[r] = a;
      } else for (var l in s) {
        o[l] = 1;
      }
    }

    for (var f in i) {
      f in n || (n[f] = void 0);
    }

    return n;
  }

  function ft(t) {
    return "object" === e(t) && null !== t ? t : {};
  }

  function dt(t) {
    t && t.c();
  }

  function ht(t, e, n) {
    var i = t.$$,
        o = i.fragment,
        r = i.on_mount,
        s = i.on_destroy,
        a = i.after_update;
    o && o.m(e, n), V(function () {
      var e = r.map(_).filter(b);
      s ? s.push.apply(s, m(e)) : x(e), t.$$.on_mount = [];
    }), a.forEach(V);
  }

  function pt(t, e) {
    var n = t.$$;
    null !== n.fragment && (x(n.on_destroy), n.fragment && n.fragment.d(e), n.on_destroy = n.fragment = null, n.ctx = []);
  }

  function mt(t, e) {
    -1 === t.$$.dirty[0] && (q.push(t), K(), t.$$.dirty.fill(0)), t.$$.dirty[e / 31 | 0] |= 1 << e % 31;
  }

  var vt = function () {
    function t() {
      n(this, t);
    }

    return o(t, [{
      key: "$destroy",
      value: function value() {
        pt(this, 1), this.$destroy = g;
      }
    }, {
      key: "$on",
      value: function value(t, e) {
        var n = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
        return n.push(e), function () {
          var t = n.indexOf(e);
          -1 !== t && n.splice(t, 1);
        };
      }
    }, {
      key: "$set",
      value: function value() {}
    }]), t;
  }(),
      yt = function () {
    function t(e) {
      if (n(this, t), Object.assign(this, {
        dir1: null,
        dir2: null,
        firstpos1: null,
        firstpos2: null,
        spacing1: 25,
        spacing2: 25,
        push: "bottom",
        maxOpen: 1,
        maxStrategy: "wait",
        maxClosureCausesWait: !0,
        modal: "ish",
        modalishFlash: !0,
        overlayClose: !0,
        overlayClosesPinned: !1,
        context: window && document.body || null
      }, e), "ish" === this.modal && 1 !== this.maxOpen) throw new Error("A modalish stack must have a maxOpen value of 1.");
      if ("ish" === this.modal && !this.dir1) throw new Error("A modalish stack must have a direction.");
      if ("top" === this.push && "ish" === this.modal && "close" !== this.maxStrategy) throw new Error("A modalish stack that pushes to the top must use the close maxStrategy.");
      this._noticeHead = {
        notice: null,
        prev: null,
        next: null
      }, this._noticeTail = {
        notice: null,
        prev: this._noticeHead,
        next: null
      }, this._noticeHead.next = this._noticeTail, this._noticeMap = new WeakMap(), this._length = 0, this._addpos2 = 0, this._animation = !0, this._posTimer = null, this._openNotices = 0, this._listener = null, this._overlayOpen = !1, this._overlayInserted = !1, this._collapsingModalState = !1, this._leader = null, this._leaderOff = null, this._masking = null, this._maskingOff = null;
    }

    return o(t, [{
      key: "forEach",
      value: function value(t) {
        var e,
            n = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
            i = n.start,
            o = void 0 === i ? "oldest" : i,
            r = n.dir,
            s = void 0 === r ? "newer" : r,
            a = n.skipModuleHandled,
            c = void 0 !== a && a;
        if ("head" === o || "newest" === o && "top" === this.push || "oldest" === o && "bottom" === this.push) e = this._noticeHead.next;else if ("tail" === o || "newest" === o && "bottom" === this.push || "oldest" === o && "top" === this.push) e = this._noticeTail.prev;else {
          if (!this._noticeMap.has(o)) throw new Error("Invalid start param.");
          e = this._noticeMap.get(o);
        }

        for (; e.notice;) {
          var u = e.notice;
          if ("prev" === s || "top" === this.push && "newer" === s || "bottom" === this.push && "older" === s) e = e.prev;else {
            if (!("next" === s || "top" === this.push && "older" === s || "bottom" === this.push && "newer" === s)) throw new Error("Invalid dir param.");
            e = e.next;
          }
          if (!(c && u.getModuleHandled() || !1 !== t(u))) break;
        }
      }
    }, {
      key: "close",
      value: function value(t) {
        this.forEach(function (e) {
          return e.close(t, !1, !1);
        });
      }
    }, {
      key: "open",
      value: function value(t) {
        this.forEach(function (e) {
          return e.open(t);
        });
      }
    }, {
      key: "openLast",
      value: function value() {
        this.forEach(function (t) {
          if (-1 === ["opening", "open", "waiting"].indexOf(t.getState())) return t.open(), !1;
        }, {
          start: "newest",
          dir: "older"
        });
      }
    }, {
      key: "position",
      value: function value() {
        var t = this;
        this._length > 0 ? (this._resetPositionData(), this.forEach(function (e) {
          t._positionNotice(e);
        }, {
          start: "head",
          dir: "next",
          skipModuleHandled: !0
        })) : (delete this._nextpos1, delete this._nextpos2);
      }
    }, {
      key: "queuePosition",
      value: function value() {
        var t = this,
            e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : 10;
        this._posTimer && clearTimeout(this._posTimer), this._posTimer = setTimeout(function () {
          return t.position();
        }, e);
      }
    }, {
      key: "_resetPositionData",
      value: function value() {
        this._nextpos1 = this.firstpos1, this._nextpos2 = this.firstpos2, this._addpos2 = 0;
      }
    }, {
      key: "_positionNotice",
      value: function value(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : t === this._masking,
            n = t.refs.elem;

        if (n && (n.classList.contains("pnotify-in") || n.classList.contains("pnotify-initial") || e)) {
          var i = [this.firstpos1, this.firstpos2, this._nextpos1, this._nextpos2, this._addpos2],
              o = i[0],
              r = i[1],
              s = i[2],
              a = i[3],
              c = i[4];
          n.getBoundingClientRect(), !this._animation || e || this._collapsingModalState ? t._setMoveClass("") : t._setMoveClass("pnotify-move");
          var u,
              l = this.context === document.body ? window.innerHeight : this.context.scrollHeight,
              f = this.context === document.body ? window.innerWidth : this.context.scrollWidth;

          if (this.dir1) {
            var d;

            switch (u = {
              down: "top",
              up: "bottom",
              left: "right",
              right: "left"
            }[this.dir1], this.dir1) {
              case "down":
                d = n.offsetTop;
                break;

              case "up":
                d = l - n.scrollHeight - n.offsetTop;
                break;

              case "left":
                d = f - n.scrollWidth - n.offsetLeft;
                break;

              case "right":
                d = n.offsetLeft;
            }

            null == o && (s = o = d);
          }

          if (this.dir1 && this.dir2) {
            var h,
                p = {
              down: "top",
              up: "bottom",
              left: "right",
              right: "left"
            }[this.dir2];

            switch (this.dir2) {
              case "down":
                h = n.offsetTop;
                break;

              case "up":
                h = l - n.scrollHeight - n.offsetTop;
                break;

              case "left":
                h = f - n.scrollWidth - n.offsetLeft;
                break;

              case "right":
                h = n.offsetLeft;
            }

            if (null == r && (a = r = h), !e) {
              var m = s + n.offsetHeight + this.spacing1,
                  v = s + n.offsetWidth + this.spacing1;
              (("down" === this.dir1 || "up" === this.dir1) && m > l || ("left" === this.dir1 || "right" === this.dir1) && v > f) && (s = o, a += c + this.spacing2, c = 0);
            }

            switch (null != a && (n.style[p] = "".concat(a, "px"), this._animation || n.style[p]), this.dir2) {
              case "down":
              case "up":
                n.offsetHeight + (parseFloat(n.style.marginTop, 10) || 0) + (parseFloat(n.style.marginBottom, 10) || 0) > c && (c = n.offsetHeight);
                break;

              case "left":
              case "right":
                n.offsetWidth + (parseFloat(n.style.marginLeft, 10) || 0) + (parseFloat(n.style.marginRight, 10) || 0) > c && (c = n.offsetWidth);
            }
          } else if (this.dir1) {
            var y, g;

            switch (this.dir1) {
              case "down":
              case "up":
                g = ["left", "right"], y = this.context.scrollWidth / 2 - n.offsetWidth / 2;
                break;

              case "left":
              case "right":
                g = ["top", "bottom"], y = l / 2 - n.offsetHeight / 2;
            }

            n.style[g[0]] = "".concat(y, "px"), n.style[g[1]] = "auto", this._animation || n.style[g[0]];
          }

          if (this.dir1) switch (null != s && (n.style[u] = "".concat(s, "px"), this._animation || n.style[u]), this.dir1) {
            case "down":
            case "up":
              s += n.offsetHeight + this.spacing1;
              break;

            case "left":
            case "right":
              s += n.offsetWidth + this.spacing1;
          } else {
            var $ = f / 2 - n.offsetWidth / 2,
                _ = l / 2 - n.offsetHeight / 2;

            n.style.left = "".concat($, "px"), n.style.top = "".concat(_, "px"), this._animation || n.style.left;
          }
          e || (this.firstpos1 = o, this.firstpos2 = r, this._nextpos1 = s, this._nextpos2 = a, this._addpos2 = c);
        }
      }
    }, {
      key: "_addNotice",
      value: function value(t) {
        var e = this,
            n = {
          notice: t,
          prev: null,
          next: null
        };
        if ("top" === this.push ? (n.next = this._noticeHead.next, n.prev = this._noticeHead, n.next.prev = n, n.prev.next = n) : (n.prev = this._noticeTail.prev, n.next = this._noticeTail, n.prev.next = n, n.next.prev = n), this._noticeMap.set(t, n), this._length++, this._listener || (this._listener = function () {
          return e.position();
        }, this.context.addEventListener("pnotify:position", this._listener)), -1 !== ["open", "opening", "closing"].indexOf(t.getState())) this._handleNoticeOpened(t);else if ("ish" === this.modal && this.modalishFlash && this._shouldNoticeWait()) var i = t.on("pnotify:mount", function () {
          i(), t._setMasking(!0, !1, function () {
            t._setMasking(!1);
          }), e._resetPositionData(), e._positionNotice(e._leader), window.requestAnimationFrame(function () {
            e._positionNotice(t, !0);
          });
        });
      }
    }, {
      key: "_removeNotice",
      value: function value(t) {
        if (this._noticeMap.has(t)) {
          var e = this._noticeMap.get(t);

          this._leader === t && this._setLeader(null), this._masking === t && this._setMasking(null), e.prev.next = e.next, e.next.prev = e.prev, e.prev = null, e.next = null, this._noticeMap["delete"](t), this._length--, !this._length && this._listener && (this.context.removeEventListener("pnotify:position", this._listener), this._listener = null), !this._length && this._overlayOpen && this._removeOverlay(), -1 !== ["open", "opening", "closing"].indexOf(t.getState()) && this._handleNoticeClosed(t);
        }
      }
    }, {
      key: "_setLeader",
      value: function value(t) {
        var e = this;

        if (this._leaderOff && (this._leaderOff(), this._leaderOff = null), this._leader = t, this._leader) {
          var n,
              i = function i() {
            var t = null;
            e._overlayOpen && (e._collapsingModalState = !0, e.forEach(function (n) {
              n._preventTimerClose(!1), n !== e._leader && -1 !== ["opening", "open"].indexOf(n.getState()) && (t || (t = n), n.close(n === t, !1, !0));
            }, {
              start: e._leader,
              dir: "next",
              skipModuleHandled: !0
            }), e._removeOverlay()), o && (clearTimeout(o), o = null), e.forEach(function (n) {
              if (n !== e._leader) return "waiting" === n.getState() || n === t ? (e._setMasking(n, !!t), !1) : void 0;
            }, {
              start: e._leader,
              dir: "next",
              skipModuleHandled: !0
            });
          },
              o = null,
              r = function r() {
            o && (clearTimeout(o), o = null), o = setTimeout(function () {
              o = null, e._setMasking(null);
            }, 750);
          };

          this._leaderOff = (n = [this._leader.on("mouseenter", i), this._leader.on("focusin", i), this._leader.on("mouseleave", r), this._leader.on("focusout", r)], function () {
            return n.map(function (t) {
              return t();
            });
          });
        }
      }
    }, {
      key: "_setMasking",
      value: function value(t, e) {
        var n = this;

        if (this._masking) {
          if (this._masking === t) return;

          this._masking._setMasking(!1, e);
        }

        if (this._maskingOff && (this._maskingOff(), this._maskingOff = null), this._masking = t, this._masking) {
          this._resetPositionData(), this._leader && this._positionNotice(this._leader), this._masking._setMasking(!0, e), window.requestAnimationFrame(function () {
            n._masking && n._positionNotice(n._masking);
          });

          var i,
              o = function o() {
            "ish" === n.modal && (n._insertOverlay(), n._setMasking(null, !0), n.forEach(function (t) {
              t._preventTimerClose(!0), "waiting" === t.getState() && t.open();
            }, {
              start: n._leader,
              dir: "next",
              skipModuleHandled: !0
            }));
          };

          this._maskingOff = (i = [this._masking.on("mouseenter", o), this._masking.on("focusin", o)], function () {
            return i.map(function (t) {
              return t();
            });
          });
        }
      }
    }, {
      key: "_handleNoticeClosed",
      value: function value(t) {
        var e = this;

        if (!t.getModuleHandled()) {
          if (this._openNotices--, "ish" === this.modal && t === this._leader && (this._setLeader(null), this._masking && this._setMasking(null)), this.maxOpen !== 1 / 0 && this._openNotices < this.maxOpen) {
            var n = function n(t) {
              if ("waiting" === t.getState() && (t.open(), e._openNotices >= e.maxOpen)) return !1;
            };

            "wait" === this.maxStrategy ? this.forEach(n, {
              start: t,
              dir: "next"
            }) : "close" === this.maxStrategy && this.maxClosureCausesWait && this.forEach(n, {
              start: t,
              dir: "older"
            });
          }

          this._openNotices <= 0 ? (this._openNotices = 0, this._overlayOpen && this._removeOverlay()) : this._collapsingModalState || this.queuePosition(0);
        }
      }
    }, {
      key: "_handleNoticeOpened",
      value: function value(t) {
        var e = this;

        if (!t.getModuleHandled()) {
          if (this._openNotices++, ("ish" !== this.modal || !this._overlayOpen) && this.maxOpen !== 1 / 0 && this._openNotices > this.maxOpen && "close" === this.maxStrategy) {
            var n = this._openNotices - this.maxOpen;
            this.forEach(function (t) {
              if (-1 !== ["opening", "open"].indexOf(t.getState())) return t.close(!1, !1, e.maxClosureCausesWait), t === e._leader && e._setLeader(null), !! --n;
            });
          }

          !0 === this.modal && this._insertOverlay(), "ish" !== this.modal || this._leader && -1 !== ["opening", "open", "closing"].indexOf(this._leader.getState()) || this._setLeader(t), "ish" === this.modal && this._overlayOpen && t._preventTimerClose(!0);
        }
      }
    }, {
      key: "_shouldNoticeWait",
      value: function value() {
        return !("ish" === this.modal && this._overlayOpen) && this.maxOpen !== 1 / 0 && this._openNotices >= this.maxOpen && "wait" === this.maxStrategy;
      }
    }, {
      key: "_insertOverlay",
      value: function value() {
        var t = this;
        this._overlay || (this._overlay = document.createElement("div"), this._overlay.classList.add("pnotify-modal-overlay"), this.dir1 && this._overlay.classList.add("pnotify-modal-overlay-".concat(this.dir1)), this.overlayClose && this._overlay.classList.add("pnotify-modal-overlay-closes"), this.context !== document.body && (this._overlay.style.height = "".concat(this.context.scrollHeight, "px"), this._overlay.style.width = "".concat(this.context.scrollWidth, "px")), this._overlay.addEventListener("click", function () {
          t.overlayClose && (t._leader && t._setLeader(null), t.forEach(function (e) {
            -1 === ["closed", "closing", "waiting"].indexOf(e.getState()) && (e.hide || t.overlayClosesPinned ? e.close() : e.hide || "ish" !== t.modal || (t._leader ? e.close(!1, !1, !0) : t._setLeader(e)));
          }, {
            skipModuleHandled: !0
          }), t._overlayOpen && t._removeOverlay());
        })), this._overlay.parentNode !== this.context && (this._overlay.classList.remove("pnotify-modal-overlay-in"), this._overlay = this.context.insertBefore(this._overlay, this.context.firstChild), this._overlayOpen = !0, this._overlayInserted = !0, window.requestAnimationFrame(function () {
          t._overlay.classList.add("pnotify-modal-overlay-in");
        })), this._collapsingModalState = !1;
      }
    }, {
      key: "_removeOverlay",
      value: function value() {
        var t = this;
        this._overlay.parentNode && (this._overlay.classList.remove("pnotify-modal-overlay-in"), this._overlayOpen = !1, setTimeout(function () {
          t._overlayInserted = !1, t._overlay.parentNode && t._overlay.parentNode.removeChild(t._overlay);
        }, 250), setTimeout(function () {
          t._collapsingModalState = !1;
        }, 400));
      }
    }, {
      key: "notices",
      get: function get() {
        var t = [];
        return this.forEach(function (e) {
          return t.push(e);
        }), t;
      }
    }, {
      key: "length",
      get: function get() {
        return this._length;
      }
    }, {
      key: "leader",
      get: function get() {
        return this._leader;
      }
    }]), t;
  }(),
      gt = function gt() {
    for (var t = arguments.length, e = new Array(t), n = 0; n < t; n++) {
      e[n] = arguments[n];
    }

    return f(Jt, e);
  };

  var $t = at.Map;

  function _t(t, e, n) {
    var i = t.slice();
    return i[106] = e[n][0], i[107] = e[n][1], i;
  }

  function kt(t, e, n) {
    var i = t.slice();
    return i[106] = e[n][0], i[107] = e[n][1], i;
  }

  function xt(t, e, n) {
    var i = t.slice();
    return i[106] = e[n][0], i[107] = e[n][1], i;
  }

  function bt(t, e, n) {
    var i = t.slice();
    return i[106] = e[n][0], i[107] = e[n][1], i;
  }

  function wt(t, e) {
    var n,
        i,
        _o,
        r = [{
      self: e[41]
    }, e[107]],
        s = e[106]["default"];

    function a(t) {
      for (var e = {}, n = 0; n < r.length; n += 1) {
        e = $(e, r[n]);
      }

      return {
        props: e
      };
    }

    if (s) var _c = new s(a());
    return {
      key: t,
      first: null,
      c: function c() {
        n = S(), _c && dt(_c.$$.fragment), i = S(), this.first = n;
      },
      m: function m(t, e) {
        C(t, n, e), _c && ht(_c, t, e), C(t, i, e), _o = !0;
      },
      p: function p(t, e) {
        var n = 1088 & e[1] ? lt(r, [1024 & e[1] && {
          self: t[41]
        }, 64 & e[1] && ft(t[107])]) : {};

        if (s !== (s = t[106]["default"])) {
          if (_c) {
            it();
            var o = _c;
            st(o.$$.fragment, 1, 0, function () {
              pt(o, 1);
            }), ot();
          }

          s ? (dt((_c = new s(a())).$$.fragment), rt(_c.$$.fragment, 1), ht(_c, i.parentNode, i)) : _c = null;
        } else s && _c.$set(n);
      },
      i: function i(t) {
        _o || (_c && rt(_c.$$.fragment, t), _o = !0);
      },
      o: function o(t) {
        _c && st(_c.$$.fragment, t), _o = !1;
      },
      d: function d(t) {
        t && M(n), t && M(i), _c && pt(_c, t);
      }
    };
  }

  function Ot(t) {
    var e, n, i, o, r;
    return {
      c: function c() {
        e = T("div"), L(n = T("span"), "class", t[21]("closer")), L(e, "class", i = "pnotify-closer ".concat(t[20]("closer"), " ").concat(!t[16] || t[25] ? "" : "pnotify-hidden")), L(e, "role", "button"), L(e, "tabindex", "0"), L(e, "title", o = t[19].close);
      },
      m: function m(i, o, s) {
        C(i, e, o), O(e, n), s && r(), r = N(e, "click", t[98]);
      },
      p: function p(t, n) {
        33619968 & n[0] && i !== (i = "pnotify-closer ".concat(t[20]("closer"), " ").concat(!t[16] || t[25] ? "" : "pnotify-hidden")) && L(e, "class", i), 524288 & n[0] && o !== (o = t[19].close) && L(e, "title", o);
      },
      d: function d(t) {
        t && M(e), r();
      }
    };
  }

  function Ct(t) {
    var e, n, i, o, r, s, a;
    return {
      c: function c() {
        e = T("div"), L(n = T("span"), "class", i = "".concat(t[21]("sticker"), " ").concat(t[1] ? t[21]("unstuck") : t[21]("stuck"))), L(e, "class", o = "pnotify-sticker ".concat(t[20]("sticker"), " ").concat(!t[18] || t[25] ? "" : "pnotify-hidden")), L(e, "role", "button"), L(e, "aria-pressed", r = !t[1]), L(e, "tabindex", "0"), L(e, "title", s = t[1] ? t[19].stick : t[19].unstick);
      },
      m: function m(i, o, r) {
        C(i, e, o), O(e, n), r && a(), a = N(e, "click", t[99]);
      },
      p: function p(t, a) {
        2 & a[0] && i !== (i = "".concat(t[21]("sticker"), " ").concat(t[1] ? t[21]("unstuck") : t[21]("stuck"))) && L(n, "class", i), 33816576 & a[0] && o !== (o = "pnotify-sticker ".concat(t[20]("sticker"), " ").concat(!t[18] || t[25] ? "" : "pnotify-hidden")) && L(e, "class", o), 2 & a[0] && r !== (r = !t[1]) && L(e, "aria-pressed", r), 524290 & a[0] && s !== (s = t[1] ? t[19].stick : t[19].unstick) && L(e, "title", s);
      },
      d: function d(t) {
        t && M(e), a();
      }
    };
  }

  function Mt(t) {
    var e, n, i;
    return {
      c: function c() {
        e = T("div"), L(n = T("span"), "class", i = !0 === t[11] ? t[21](t[2]) : t[11]), L(e, "class", "pnotify-icon ".concat(t[20]("icon")));
      },
      m: function m(i, o) {
        C(i, e, o), O(e, n), t[100](e);
      },
      p: function p(t, e) {
        2052 & e[0] && i !== (i = !0 === t[11] ? t[21](t[2]) : t[11]) && L(n, "class", i);
      },
      d: function d(n) {
        n && M(e), t[100](null);
      }
    };
  }

  function Tt(t, e) {
    var n,
        i,
        _o2,
        r = [{
      self: e[41]
    }, e[107]],
        s = e[106]["default"];

    function a(t) {
      for (var e = {}, n = 0; n < r.length; n += 1) {
        e = $(e, r[n]);
      }

      return {
        props: e
      };
    }

    if (s) var _c2 = new s(a());
    return {
      key: t,
      first: null,
      c: function c() {
        n = S(), _c2 && dt(_c2.$$.fragment), i = S(), this.first = n;
      },
      m: function m(t, e) {
        C(t, n, e), _c2 && ht(_c2, t, e), C(t, i, e), _o2 = !0;
      },
      p: function p(t, e) {
        var n = 1152 & e[1] ? lt(r, [1024 & e[1] && {
          self: t[41]
        }, 128 & e[1] && ft(t[107])]) : {};

        if (s !== (s = t[106]["default"])) {
          if (_c2) {
            it();
            var o = _c2;
            st(o.$$.fragment, 1, 0, function () {
              pt(o, 1);
            }), ot();
          }

          s ? (dt((_c2 = new s(a())).$$.fragment), rt(_c2.$$.fragment, 1), ht(_c2, i.parentNode, i)) : _c2 = null;
        } else s && _c2.$set(n);
      },
      i: function i(t) {
        _o2 || (_c2 && rt(_c2.$$.fragment, t), _o2 = !0);
      },
      o: function o(t) {
        _c2 && st(_c2.$$.fragment, t), _o2 = !1;
      },
      d: function d(t) {
        t && M(n), t && M(i), _c2 && pt(_c2, t);
      }
    };
  }

  function Ht(t) {
    var e,
        n = !t[32] && Et(t);
    return {
      c: function c() {
        e = T("div"), n && n.c(), L(e, "class", "pnotify-title ".concat(t[20]("title")));
      },
      m: function m(i, o) {
        C(i, e, o), n && n.m(e, null), t[101](e);
      },
      p: function p(t, i) {
        t[32] ? n && (n.d(1), n = null) : n ? n.p(t, i) : ((n = Et(t)).c(), n.m(e, null));
      },
      d: function d(i) {
        i && M(e), n && n.d(), t[101](null);
      }
    };
  }

  function Et(t) {
    var e;

    function n(t, e) {
      return t[4] ? Nt : St;
    }

    var i = n(t),
        o = i(t);
    return {
      c: function c() {
        o.c(), e = S();
      },
      m: function m(t, n) {
        o.m(t, n), C(t, e, n);
      },
      p: function p(t, r) {
        i === (i = n(t)) && o ? o.p(t, r) : (o.d(1), (o = i(t)) && (o.c(), o.m(e.parentNode, e)));
      },
      d: function d(t) {
        o.d(t), t && M(e);
      }
    };
  }

  function St(t) {
    var e, n;
    return {
      c: function c() {
        e = T("span"), n = H(t[3]), L(e, "class", "pnotify-pre-line");
      },
      m: function m(t, i) {
        C(t, e, i), O(e, n);
      },
      p: function p(t, e) {
        8 & e[0] && j(n, t[3]);
      },
      d: function d(t) {
        t && M(e);
      }
    };
  }

  function Nt(t) {
    var e;
    return {
      c: function c() {
        e = new W(t[3], null);
      },
      m: function m(t, n) {
        e.m(t, n);
      },
      p: function p(t, n) {
        8 & n[0] && e.p(t[3]);
      },
      d: function d(t) {
        t && e.d();
      }
    };
  }

  function Lt(t) {
    var e,
        n = !t[33] && At(t);
    return {
      c: function c() {
        e = T("div"), n && n.c(), L(e, "class", "pnotify-text ".concat(t[20]("text"))), L(e, "style", t[31]), L(e, "role", "alert");
      },
      m: function m(i, o) {
        C(i, e, o), n && n.m(e, null), t[102](e);
      },
      p: function p(t, i) {
        t[33] ? n && (n.d(1), n = null) : n ? n.p(t, i) : ((n = At(t)).c(), n.m(e, null)), 1 & i[1] && L(e, "style", t[31]);
      },
      d: function d(i) {
        i && M(e), n && n.d(), t[102](null);
      }
    };
  }

  function At(t) {
    var e;

    function n(t, e) {
      return t[6] ? Pt : jt;
    }

    var i = n(t),
        o = i(t);
    return {
      c: function c() {
        o.c(), e = S();
      },
      m: function m(t, n) {
        o.m(t, n), C(t, e, n);
      },
      p: function p(t, r) {
        i === (i = n(t)) && o ? o.p(t, r) : (o.d(1), (o = i(t)) && (o.c(), o.m(e.parentNode, e)));
      },
      d: function d(t) {
        o.d(t), t && M(e);
      }
    };
  }

  function jt(t) {
    var e, n;
    return {
      c: function c() {
        e = T("span"), n = H(t[5]), L(e, "class", "pnotify-pre-line");
      },
      m: function m(t, i) {
        C(t, e, i), O(e, n);
      },
      p: function p(t, e) {
        32 & e[0] && j(n, t[5]);
      },
      d: function d(t) {
        t && M(e);
      }
    };
  }

  function Pt(t) {
    var e;
    return {
      c: function c() {
        e = new W(t[5], null);
      },
      m: function m(t, n) {
        e.m(t, n);
      },
      p: function p(t, n) {
        32 & n[0] && e.p(t[5]);
      },
      d: function d(t) {
        t && e.d();
      }
    };
  }

  function Wt(t, e) {
    var n,
        i,
        _o3,
        r = [{
      self: e[41]
    }, e[107]],
        s = e[106]["default"];

    function a(t) {
      for (var e = {}, n = 0; n < r.length; n += 1) {
        e = $(e, r[n]);
      }

      return {
        props: e
      };
    }

    if (s) var _c3 = new s(a());
    return {
      key: t,
      first: null,
      c: function c() {
        n = S(), _c3 && dt(_c3.$$.fragment), i = S(), this.first = n;
      },
      m: function m(t, e) {
        C(t, n, e), _c3 && ht(_c3, t, e), C(t, i, e), _o3 = !0;
      },
      p: function p(t, e) {
        var n = 1280 & e[1] ? lt(r, [1024 & e[1] && {
          self: t[41]
        }, 256 & e[1] && ft(t[107])]) : {};

        if (s !== (s = t[106]["default"])) {
          if (_c3) {
            it();
            var o = _c3;
            st(o.$$.fragment, 1, 0, function () {
              pt(o, 1);
            }), ot();
          }

          s ? (dt((_c3 = new s(a())).$$.fragment), rt(_c3.$$.fragment, 1), ht(_c3, i.parentNode, i)) : _c3 = null;
        } else s && _c3.$set(n);
      },
      i: function i(t) {
        _o3 || (_c3 && rt(_c3.$$.fragment, t), _o3 = !0);
      },
      o: function o(t) {
        _c3 && st(_c3.$$.fragment, t), _o3 = !1;
      },
      d: function d(t) {
        t && M(n), t && M(i), _c3 && pt(_c3, t);
      }
    };
  }

  function Rt(t, e) {
    var n,
        i,
        _o4,
        r = [{
      self: e[41]
    }, e[107]],
        s = e[106]["default"];

    function a(t) {
      for (var e = {}, n = 0; n < r.length; n += 1) {
        e = $(e, r[n]);
      }

      return {
        props: e
      };
    }

    if (s) var _c4 = new s(a());
    return {
      key: t,
      first: null,
      c: function c() {
        n = S(), _c4 && dt(_c4.$$.fragment), i = S(), this.first = n;
      },
      m: function m(t, e) {
        C(t, n, e), _c4 && ht(_c4, t, e), C(t, i, e), _o4 = !0;
      },
      p: function p(t, e) {
        var n = 1536 & e[1] ? lt(r, [1024 & e[1] && {
          self: t[41]
        }, 512 & e[1] && ft(t[107])]) : {};

        if (s !== (s = t[106]["default"])) {
          if (_c4) {
            it();
            var o = _c4;
            st(o.$$.fragment, 1, 0, function () {
              pt(o, 1);
            }), ot();
          }

          s ? (dt((_c4 = new s(a())).$$.fragment), rt(_c4.$$.fragment, 1), ht(_c4, i.parentNode, i)) : _c4 = null;
        } else s && _c4.$set(n);
      },
      i: function i(t) {
        _o4 || (_c4 && rt(_c4.$$.fragment, t), _o4 = !0);
      },
      o: function o(t) {
        _c4 && st(_c4.$$.fragment, t), _o4 = !1;
      },
      d: function d(t) {
        t && M(n), t && M(i), _c4 && pt(_c4, t);
      }
    };
  }

  function It(t) {
    for (var e, n, i, o, r, s, a, _c5, u, l, f, d, h, _p, _m, v, y = [], $ = new $t(), _ = [], k = new $t(), w = [], H = new $t(), S = [], A = new $t(), j = t[37], P = function P(t) {
      return t[106];
    }, W = 0; W < j.length; W += 1) {
      var R = bt(t, j, W),
          I = P(R);
      $.set(I, y[W] = wt(I, R));
    }

    for (var D = t[15] && !t[35] && Ot(t), F = t[17] && !t[35] && Ct(t), q = !1 !== t[11] && Mt(t), B = t[38], z = function z(t) {
      return t[106];
    }, U = 0; U < B.length; U += 1) {
      var G = xt(t, B, U),
          J = z(G);
      k.set(J, _[U] = Tt(J, G));
    }

    for (var K = !1 !== t[3] && Ht(t), Q = !1 !== t[5] && Lt(t), V = t[39], X = function X(t) {
      return t[106];
    }, Y = 0; Y < V.length; Y += 1) {
      var Z = kt(t, V, Y),
          tt = X(Z);
      H.set(tt, w[Y] = Wt(tt, Z));
    }

    for (var et = t[40], nt = function nt(t) {
      return t[106];
    }, at = 0; at < et.length; at += 1) {
      var lt = _t(t, et, at),
          ft = nt(lt);

      A.set(ft, S[at] = Rt(ft, lt));
    }

    return {
      c: function c() {
        e = T("div"), n = T("div");

        for (var m = 0; m < y.length; m += 1) {
          y[m].c();
        }

        i = E(), D && D.c(), o = E(), F && F.c(), r = E(), q && q.c(), s = E(), a = T("div");

        for (var v = 0; v < _.length; v += 1) {
          _[v].c();
        }

        _c5 = E(), K && K.c(), u = E(), Q && Q.c(), l = E();

        for (var g = 0; g < w.length; g += 1) {
          w[g].c();
        }

        f = E();

        for (var $ = 0; $ < S.length; $ += 1) {
          S[$].c();
        }

        L(a, "class", "pnotify-content ".concat(t[20]("content"))), L(n, "class", d = "pnotify-container ".concat(t[20]("container"), " ").concat(t[20](t[2]), " ").concat(t[14] ? "pnotify-shadow" : "", " ").concat(t[26].container.join(" "))), L(n, "style", h = "".concat(t[29], " ").concat(t[30])), L(n, "role", "alert"), L(e, "data-pnotify", ""), L(e, "class", _p = "pnotify ".concat(!1 !== t[11] ? "pnotify-with-icon" : "", " ").concat(t[20]("elem"), " pnotify-mode-").concat(t[7], " ").concat(t[8], " ").concat(t[23], " ").concat(t[24], " ").concat(t[36], " ").concat("fade" === t[12] ? "pnotify-fade-".concat(t[13]) : "", " ").concat(t[34] ? "pnotify-modal ".concat(t[9]) : t[10], " ").concat(t[27] ? "pnotify-masking" : "", " ").concat(t[28] ? "pnotify-masking-in" : "", " ").concat(t[26].elem.join(" "))), L(e, "aria-live", "assertive"), L(e, "role", "alertdialog");
      },
      m: function m(d, h, p) {
        C(d, e, h), O(e, n);

        for (var $ = 0; $ < y.length; $ += 1) {
          y[$].m(n, null);
        }

        O(n, i), D && D.m(n, null), O(n, o), F && F.m(n, null), O(n, r), q && q.m(n, null), O(n, s), O(n, a);

        for (var k = 0; k < _.length; k += 1) {
          _[k].m(a, null);
        }

        O(a, _c5), K && K.m(a, null), O(a, u), Q && Q.m(a, null), O(a, l);

        for (var M = 0; M < w.length; M += 1) {
          w[M].m(a, null);
        }

        t[103](a), O(n, f);

        for (var T = 0; T < S.length; T += 1) {
          S[T].m(n, null);
        }

        var H;
        t[104](n), t[105](e), _m = !0, p && x(v), v = [(H = t[42].call(null, e), H && b(H.destroy) ? H.destroy : g), N(e, "mouseenter", t[43]), N(e, "mouseleave", t[44]), N(e, "focusin", t[43]), N(e, "focusout", t[44])];
      },
      p: function p(t, f) {
        if (1088 & f[1]) {
          var v = t[37];
          it(), y = ut(y, f, P, 1, t, v, $, n, ct, wt, i, bt), ot();
        }

        if (t[15] && !t[35] ? D ? D.p(t, f) : ((D = Ot(t)).c(), D.m(n, o)) : D && (D.d(1), D = null), t[17] && !t[35] ? F ? F.p(t, f) : ((F = Ct(t)).c(), F.m(n, r)) : F && (F.d(1), F = null), !1 !== t[11] ? q ? q.p(t, f) : ((q = Mt(t)).c(), q.m(n, s)) : q && (q.d(1), q = null), 1152 & f[1]) {
          var g = t[38];
          it(), _ = ut(_, f, z, 1, t, g, k, a, ct, Tt, _c5, xt), ot();
        }

        if (!1 !== t[3] ? K ? K.p(t, f) : ((K = Ht(t)).c(), K.m(a, u)) : K && (K.d(1), K = null), !1 !== t[5] ? Q ? Q.p(t, f) : ((Q = Lt(t)).c(), Q.m(a, l)) : Q && (Q.d(1), Q = null), 1280 & f[1]) {
          var x = t[39];
          it(), w = ut(w, f, X, 1, t, x, H, a, ct, Wt, null, kt), ot();
        }

        if (1536 & f[1]) {
          var b = t[40];
          it(), S = ut(S, f, nt, 1, t, b, A, n, ct, Rt, null, _t), ot();
        }

        (!_m || 67125252 & f[0] && d !== (d = "pnotify-container ".concat(t[20]("container"), " ").concat(t[20](t[2]), " ").concat(t[14] ? "pnotify-shadow" : "", " ").concat(t[26].container.join(" ")))) && L(n, "class", d), (!_m || 1610612736 & f[0] && h !== (h = "".concat(t[29], " ").concat(t[30]))) && L(n, "style", h), (!_m || 494944128 & f[0] | 40 & f[1] && _p !== (_p = "pnotify ".concat(!1 !== t[11] ? "pnotify-with-icon" : "", " ").concat(t[20]("elem"), " pnotify-mode-").concat(t[7], " ").concat(t[8], " ").concat(t[23], " ").concat(t[24], " ").concat(t[36], " ").concat("fade" === t[12] ? "pnotify-fade-".concat(t[13]) : "", " ").concat(t[34] ? "pnotify-modal ".concat(t[9]) : t[10], " ").concat(t[27] ? "pnotify-masking" : "", " ").concat(t[28] ? "pnotify-masking-in" : "", " ").concat(t[26].elem.join(" ")))) && L(e, "class", _p);
      },
      i: function i(t) {
        if (!_m) {
          for (var e = 0; e < j.length; e += 1) {
            rt(y[e]);
          }

          for (var n = 0; n < B.length; n += 1) {
            rt(_[n]);
          }

          for (var i = 0; i < V.length; i += 1) {
            rt(w[i]);
          }

          for (var o = 0; o < et.length; o += 1) {
            rt(S[o]);
          }

          _m = !0;
        }
      },
      o: function o(t) {
        for (var e = 0; e < y.length; e += 1) {
          st(y[e]);
        }

        for (var n = 0; n < _.length; n += 1) {
          st(_[n]);
        }

        for (var i = 0; i < w.length; i += 1) {
          st(w[i]);
        }

        for (var o = 0; o < S.length; o += 1) {
          st(S[o]);
        }

        _m = !1;
      },
      d: function d(n) {
        n && M(e);

        for (var i = 0; i < y.length; i += 1) {
          y[i].d();
        }

        D && D.d(), F && F.d(), q && q.d();

        for (var o = 0; o < _.length; o += 1) {
          _[o].d();
        }

        K && K.d(), Q && Q.d();

        for (var r = 0; r < w.length; r += 1) {
          w[r].d();
        }

        t[103](null);

        for (var s = 0; s < S.length; s += 1) {
          S[s].d();
        }

        t[104](null), t[105](null), x(v);
      }
    };
  }

  function Dt(t, n) {
    "object" !== e(t) && (t = {
      text: t
    }), n && (t.type = n);
    var i = document.body;
    return "stack" in t && t.stack && t.stack.context && (i = t.stack.context), {
      target: i,
      props: t
    };
  }

  var Ft,
      qt = new yt({
    dir1: "down",
    dir2: "left",
    firstpos1: 25,
    firstpos2: 25,
    spacing1: 36,
    spacing2: 36,
    push: "bottom"
  }),
      Bt = new Map(),
      zt = {
    type: "notice",
    title: !1,
    titleTrusted: !1,
    text: !1,
    textTrusted: !1,
    styling: "brighttheme",
    icons: "brighttheme",
    mode: "no-preference",
    addClass: "",
    addModalClass: "",
    addModelessClass: "",
    autoOpen: !0,
    width: "360px",
    minHeight: "16px",
    maxTextHeight: "200px",
    icon: !0,
    animation: "fade",
    animateSpeed: "normal",
    shadow: !0,
    hide: !0,
    delay: 8e3,
    mouseReset: !0,
    closer: !0,
    closerHover: !0,
    sticker: !0,
    stickerHover: !0,
    labels: {
      close: "Close",
      stick: "Pin",
      unstick: "Unpin"
    },
    remove: !0,
    destroy: !0,
    stack: qt,
    modules: Bt
  };

  function Ut() {
    qt.context || (qt.context = document.body), window.addEventListener("resize", function () {
      Ft && clearTimeout(Ft), Ft = setTimeout(function () {
        var t = new Event("pnotify:position");
        document.body.dispatchEvent(t), Ft = null;
      }, 10);
    });
  }

  function Gt(t, e, n) {
    var i = I(),
        o = D(),
        r = function (t) {
      var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : [],
          n = ["focus", "blur", "fullscreenchange", "fullscreenerror", "scroll", "cut", "copy", "paste", "keydown", "keypress", "keyup", "auxclick", "click", "contextmenu", "dblclick", "mousedown", "mouseenter", "mouseleave", "mousemove", "mouseover", "mouseout", "mouseup", "pointerlockchange", "pointerlockerror", "select", "wheel", "drag", "dragend", "dragenter", "dragstart", "dragleave", "dragover", "drop", "touchcancel", "touchend", "touchmove", "touchstart", "pointerover", "pointerenter", "pointerdown", "pointermove", "pointerup", "pointercancel", "pointerout", "pointerleave", "gotpointercapture", "lostpointercapture"].concat(m(e));

      function i(e) {
        F(t, e);
      }

      return function (t) {
        for (var e = [], o = 0; o < n.length; o++) {
          e.push(N(t, n[o], i));
        }

        return {
          destroy: function destroy() {
            for (var t = 0; t < e.length; t++) {
              e[t]();
            }
          }
        };
      };
    }(i, ["pnotify:init", "pnotify:mount", "pnotify:update", "pnotify:beforeOpen", "pnotify:afterOpen", "pnotify:enterModal", "pnotify:leaveModal", "pnotify:beforeClose", "pnotify:afterClose", "pnotify:beforeDestroy", "pnotify:afterDestroy", "focusin", "focusout", "animationend", "transitionend"]),
        s = e.modules,
        c = void 0 === s ? new Map(zt.modules) : s,
        u = e.stack,
        l = void 0 === u ? zt.stack : u,
        f = {
      elem: null,
      container: null,
      content: null,
      iconContainer: null,
      titleContainer: null,
      textContainer: null
    },
        d = a({}, zt);

    Ut("init", {
      notice: i,
      defaults: d
    });
    var h,
        v = e.type,
        y = void 0 === v ? d.type : v,
        g = e.title,
        $ = void 0 === g ? d.title : g,
        _ = e.titleTrusted,
        k = void 0 === _ ? d.titleTrusted : _,
        x = e.text,
        b = void 0 === x ? d.text : x,
        w = e.textTrusted,
        O = void 0 === w ? d.textTrusted : w,
        C = e.styling,
        M = void 0 === C ? d.styling : C,
        T = e.icons,
        H = void 0 === T ? d.icons : T,
        E = e.mode,
        S = void 0 === E ? d.mode : E,
        L = e.addClass,
        A = void 0 === L ? d.addClass : L,
        j = e.addModalClass,
        P = void 0 === j ? d.addModalClass : j,
        W = e.addModelessClass,
        R = void 0 === W ? d.addModelessClass : W,
        q = e.autoOpen,
        z = void 0 === q ? d.autoOpen : q,
        U = e.width,
        G = void 0 === U ? d.width : U,
        J = e.minHeight,
        K = void 0 === J ? d.minHeight : J,
        V = e.maxTextHeight,
        X = void 0 === V ? d.maxTextHeight : V,
        Y = e.icon,
        Z = void 0 === Y ? d.icon : Y,
        tt = e.animation,
        et = void 0 === tt ? d.animation : tt,
        nt = e.animateSpeed,
        it = void 0 === nt ? d.animateSpeed : nt,
        ot = e.shadow,
        rt = void 0 === ot ? d.shadow : ot,
        st = e.hide,
        at = void 0 === st ? d.hide : st,
        ct = e.delay,
        ut = void 0 === ct ? d.delay : ct,
        lt = e.mouseReset,
        ft = void 0 === lt ? d.mouseReset : lt,
        dt = e.closer,
        ht = void 0 === dt ? d.closer : dt,
        pt = e.closerHover,
        mt = void 0 === pt ? d.closerHover : pt,
        vt = e.sticker,
        yt = void 0 === vt ? d.sticker : vt,
        gt = e.stickerHover,
        $t = void 0 === gt ? d.stickerHover : gt,
        _t = e.labels,
        kt = void 0 === _t ? d.labels : _t,
        xt = e.remove,
        bt = void 0 === xt ? d.remove : xt,
        wt = e.destroy,
        Ot = void 0 === wt ? d.destroy : wt,
        Ct = "closed",
        Mt = null,
        Tt = null,
        Ht = null,
        Et = !1,
        St = "",
        Nt = "",
        Lt = !1,
        At = !1,
        jt = {
      elem: [],
      container: []
    },
        Pt = !1,
        Wt = !1,
        Rt = !1,
        It = !1,
        Dt = null,
        Ft = at,
        qt = NaN,
        Bt = !1;

    function Ut(t) {
      var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          n = a({
        notice: i
      }, e);
      "init" === t && Array.from(c).forEach(function (t) {
        var e = p(t, 2),
            i = e[0];
        e[1];
        return "init" in i && i.init(n);
      });
      var r = f.elem || l && l.context || document.body;
      if (!r) return o("pnotify:".concat(t), n), !0;
      var s = new Event("pnotify:".concat(t), {
        bubbles: "init" === t || "mount" === t,
        cancelable: t.startsWith("before")
      });
      return s.detail = n, r.dispatchEvent(s), !s.defaultPrevented;
    }

    function Gt() {
      var t = l && l.context || document.body;
      if (!t) throw new Error("No context to insert this notice into.");
      if (!f.elem) throw new Error("Trying to insert notice before element is available.");
      f.elem.parentNode !== t && t.appendChild(f.elem);
    }

    function Jt() {
      f.elem && f.elem.parentNode.removeChild(f.elem);
    }

    h = function h() {
      Ut("mount"), z && Qt();
    }, I().$$.on_mount.push(h), function (t) {
      I().$$.before_update.push(t);
    }(function () {
      Ut("update"), "closed" !== Ct && "waiting" !== Ct && at !== Ft && (at ? Ft || ie() : ne()), "closed" !== Ct && "closing" !== Ct && l && !l._collapsingModalState && l.queuePosition(), Ft = at;
    });
    var Kt = e.open,
        Qt = void 0 === Kt ? function (t) {
      if ("opening" !== Ct) if ("open" !== Ct) {
        if (!Pt && l && l._shouldNoticeWait()) n(81, Ct = "waiting");else if (Ut("beforeOpen", {
          immediate: t
        })) {
          n(81, Ct = "opening"), n(27, Rt = !1), n(23, St = "pnotify-initial pnotify-hidden");

          var e = function e() {
            at && ie(), n(81, Ct = "open"), Ut("afterOpen", {
              immediate: t
            });
          };

          l && l._handleNoticeOpened(i), Wt ? e() : (Gt(), window.requestAnimationFrame(function () {
            "opening" === Ct && (l && (n(45, l._animation = !1, l), "top" === l.push && l._resetPositionData(), l._positionNotice(i), l.queuePosition(0), n(45, l._animation = !0, l)), Zt(e, t));
          }));
        }
      } else at && ie();
    } : Kt,
        Vt = e.close,
        Xt = void 0 === Vt ? function (t, e, o) {
      if ("closing" !== Ct && "closed" !== Ct) {
        var r = function r() {
          Ut("beforeDestroy") && (l && l._removeNotice(i), i.$destroy(), Ut("afterDestroy"));
        };

        if ("waiting" === Ct) {
          if (o) return;
          return n(81, Ct = "closed"), void (Ot && !o && r());
        }

        Ut("beforeClose", {
          immediate: t,
          timerHide: e,
          waitAfterward: o
        }) && (n(81, Ct = "closing"), Lt = !!e, Mt && "prevented" !== Mt && clearTimeout && clearTimeout(Mt), n(82, Mt = null), ee(function () {
          n(25, At = !1), Lt = !1, n(81, Ct = o ? "waiting" : "closed"), Ut("afterClose", {
            immediate: t,
            timerHide: e,
            waitAfterward: o
          }), l && l._handleNoticeClosed(i), Ot && !o ? r() : bt && !o && Jt();
        }, t));
      }
    } : Vt,
        Yt = e.animateIn,
        Zt = void 0 === Yt ? function (t, e) {
      Et = "in";

      var i = function e(n) {
        if (!(n && f.elem && n.target !== f.elem || (f.elem && f.elem.removeEventListener("transitionend", e), Tt && clearTimeout(Tt), "in" !== Et))) {
          var i = Wt;

          if (!i && f.elem) {
            var o = f.elem.getBoundingClientRect();

            for (var r in o) {
              if (o[r] > 0) {
                i = !0;
                break;
              }
            }
          }

          i ? (t && t.call(), Et = !1) : Tt = setTimeout(e, 40);
        }
      };

      "fade" !== et || e ? (n(23, St = "pnotify-in"), Q().then(function () {
        i();
      })) : (f.elem && f.elem.addEventListener("transitionend", i), n(23, St = "pnotify-in"), Q().then(function () {
        n(23, St = "pnotify-in pnotify-fade-in"), Tt = setTimeout(i, 650);
      }));
    } : Yt,
        te = e.animateOut,
        ee = void 0 === te ? function (t, e) {
      Et = "out";

      var i = function e(i) {
        if (!(i && f.elem && i.target !== f.elem || (f.elem && f.elem.removeEventListener("transitionend", e), Ht && clearTimeout(Ht), "out" !== Et))) {
          var o = Wt;

          if (!o && f.elem) {
            var r = f.elem.getBoundingClientRect();

            for (var s in r) {
              if (r[s] > 0) {
                o = !0;
                break;
              }
            }
          }

          f.elem && f.elem.style.opacity && "0" !== f.elem.style.opacity && o ? Ht = setTimeout(e, 40) : (n(23, St = ""), t && t.call(), Et = !1);
        }
      };

      "fade" !== et || e ? (n(23, St = ""), Q().then(function () {
        i();
      })) : (f.elem && f.elem.addEventListener("transitionend", i), n(23, St = "pnotify-in"), Ht = setTimeout(i, 650));
    } : te;

    function ne() {
      Mt && "prevented" !== Mt && (clearTimeout(Mt), n(82, Mt = null)), Ht && clearTimeout(Ht), "closing" === Ct && (n(81, Ct = "open"), Et = !1, n(23, St = "fade" === et ? "pnotify-in pnotify-fade-in" : "pnotify-in"));
    }

    function ie() {
      "prevented" !== Mt && (ne(), ut !== 1 / 0 && n(82, Mt = setTimeout(function () {
        return Xt(!1, !0);
      }, isNaN(ut) ? 0 : ut)));
    }

    var oe, re, se, ae, ce, ue, le, fe, de, he, pe, me;
    return t.$set = function (t) {
      "modules" in t && n(46, c = t.modules), "stack" in t && n(45, l = t.stack), "type" in t && n(2, y = t.type), "title" in t && n(3, $ = t.title), "titleTrusted" in t && n(4, k = t.titleTrusted), "text" in t && n(5, b = t.text), "textTrusted" in t && n(6, O = t.textTrusted), "styling" in t && n(47, M = t.styling), "icons" in t && n(48, H = t.icons), "mode" in t && n(7, S = t.mode), "addClass" in t && n(8, A = t.addClass), "addModalClass" in t && n(9, P = t.addModalClass), "addModelessClass" in t && n(10, R = t.addModelessClass), "autoOpen" in t && n(49, z = t.autoOpen), "width" in t && n(50, G = t.width), "minHeight" in t && n(51, K = t.minHeight), "maxTextHeight" in t && n(52, X = t.maxTextHeight), "icon" in t && n(11, Z = t.icon), "animation" in t && n(12, et = t.animation), "animateSpeed" in t && n(13, it = t.animateSpeed), "shadow" in t && n(14, rt = t.shadow), "hide" in t && n(1, at = t.hide), "delay" in t && n(53, ut = t.delay), "mouseReset" in t && n(54, ft = t.mouseReset), "closer" in t && n(15, ht = t.closer), "closerHover" in t && n(16, mt = t.closerHover), "sticker" in t && n(17, yt = t.sticker), "stickerHover" in t && n(18, $t = t.stickerHover), "labels" in t && n(19, kt = t.labels), "remove" in t && n(55, bt = t.remove), "destroy" in t && n(56, Ot = t.destroy), "open" in t && n(59, Qt = t.open), "close" in t && n(22, Xt = t.close), "animateIn" in t && n(60, Zt = t.animateIn), "animateOut" in t && n(61, ee = t.animateOut);
    }, t.$$.update = function () {
      524288 & t.$$.dirty[1] && n(29, oe = "string" == typeof G ? "width: ".concat(G, ";") : ""), 1048576 & t.$$.dirty[1] && n(30, re = "string" == typeof K ? "min-height: ".concat(K, ";") : ""), 2097152 & t.$$.dirty[1] && n(31, se = "string" == typeof X ? "max-height: ".concat(X, "; overflow-y: auto; overscroll-behavior: contain; padding-bottom:.03em;") : ""), 8 & t.$$.dirty[0] && n(32, ae = $ instanceof HTMLElement), 32 & t.$$.dirty[0] && n(33, ce = b instanceof HTMLElement), 16384 & t.$$.dirty[1] | 1572864 & t.$$.dirty[2] && n(34, ue = l && (!0 === l.modal || "ish" === l.modal && "prevented" === Mt) && -1 !== ["open", "opening", "closing"].indexOf(Ct)), 1792 & t.$$.dirty[0] | 8 & t.$$.dirty[1] && n(35, le = A.match(/\bnonblock\b/) || P.match(/\bnonblock\b/) && ue || R.match(/\bnonblock\b/) && !ue), 16384 & t.$$.dirty[1] && n(36, fe = l && l.dir1 ? "pnotify-stack-".concat(l.dir1) : ""), 32768 & t.$$.dirty[1] && n(37, de = Array.from(c).filter(function (t) {
        var e = p(t, 2),
            n = e[0];
        e[1];
        return "PrependContainer" === n.position;
      })), 32768 & t.$$.dirty[1] && n(38, he = Array.from(c).filter(function (t) {
        var e = p(t, 2),
            n = e[0];
        e[1];
        return "PrependContent" === n.position;
      })), 32768 & t.$$.dirty[1] && n(39, pe = Array.from(c).filter(function (t) {
        var e = p(t, 2),
            n = e[0];
        e[1];
        return "AppendContent" === n.position;
      })), 32768 & t.$$.dirty[1] && n(40, me = Array.from(c).filter(function (t) {
        var e = p(t, 2),
            n = e[0];
        e[1];
        return "AppendContainer" === n.position;
      })), 9 & t.$$.dirty[0] | 2 & t.$$.dirty[1] && ae && f.titleContainer && f.titleContainer.appendChild($), 33 & t.$$.dirty[0] | 4 & t.$$.dirty[1] && ce && f.textContainer && f.textContainer.appendChild(b), 16384 & t.$$.dirty[1] | 536870912 & t.$$.dirty[2] && qt !== l && (qt && qt._removeNotice(i), l && l._addNotice(i), n(91, qt = l)), 8 & t.$$.dirty[1] | 1073741824 & t.$$.dirty[2] && Bt !== ue && (Ut(ue ? "enterModal" : "leaveModal"), n(92, Bt = ue));
    }, [f, at, y, $, k, b, O, S, A, P, R, Z, et, it, rt, ht, mt, yt, $t, kt, function (t) {
      return "string" == typeof M ? "".concat(M, "-").concat(t) : t in M ? M[t] : "".concat(M.prefix, "-").concat(t);
    }, function (t) {
      return "string" == typeof H ? "".concat(H, "-icon-").concat(t) : t in H ? H[t] : "".concat(H.prefix, "-icon-").concat(t);
    }, Xt, St, Nt, At, jt, Rt, It, oe, re, se, ae, ce, ue, le, fe, de, he, pe, me, i, r, function (t) {
      if (n(25, At = !0), ft && "closing" === Ct) {
        if (!Lt) return;
        ne();
      }

      at && ft && ne();
    }, function (t) {
      n(25, At = !1), at && ft && "out" !== Et && ie();
    }, l, c, M, H, z, G, K, X, ut, ft, bt, Ot, function () {
      return Ct;
    }, function () {
      return Mt;
    }, Qt, Zt, ee, ne, ie, function (t) {
      t ? (ne(), n(82, Mt = "prevented")) : "prevented" === Mt && (n(82, Mt = null), "open" === Ct && at && ie());
    }, function () {
      return i.$on.apply(i, arguments);
    }, function () {
      return i.$set.apply(i, arguments);
    }, function (t, e) {
      o(t, e);
    }, function (t) {
      for (var e = 0; e < (arguments.length <= 1 ? 0 : arguments.length - 1); e++) {
        var i = e + 1 < 1 || arguments.length <= e + 1 ? void 0 : arguments[e + 1];
        -1 === jt[t].indexOf(i) && jt[t].push(i);
      }

      n(26, jt);
    }, function (t) {
      for (var e = 0; e < (arguments.length <= 1 ? 0 : arguments.length - 1); e++) {
        var i = e + 1 < 1 || arguments.length <= e + 1 ? void 0 : arguments[e + 1],
            o = jt[t].indexOf(i);
        -1 !== o && jt[t].splice(o, 1);
      }

      n(26, jt);
    }, function (t) {
      for (var e = 0; e < (arguments.length <= 1 ? 0 : arguments.length - 1); e++) {
        var n = e + 1 < 1 || arguments.length <= e + 1 ? void 0 : arguments[e + 1];
        if (-1 === jt[t].indexOf(n)) return !1;
      }

      return !0;
    }, function () {
      return Pt;
    }, function (t) {
      return Pt = t;
    }, function () {
      return Wt;
    }, function (t) {
      return Wt = t;
    }, function (t) {
      return Et = t;
    }, function () {
      return St;
    }, function (t) {
      return n(23, St = t);
    }, function () {
      return Nt;
    }, function (t) {
      return n(24, Nt = t);
    }, function (t, e, i) {
      if (Dt && clearTimeout(Dt), Rt !== t) if (t) n(27, Rt = !0), n(28, It = !!e), Gt(), Q().then(function () {
        window.requestAnimationFrame(function () {
          if (Rt) if (e && i) i();else {
            n(28, It = !0);

            var t = function t() {
              f.elem && f.elem.removeEventListener("transitionend", t), Dt && clearTimeout(Dt), It && i && i();
            };

            f.elem && f.elem.addEventListener("transitionend", t), Dt = setTimeout(t, 650);
          }
        });
      });else if (e) n(27, Rt = !1), n(28, It = !1), bt && -1 === ["open", "opening", "closing"].indexOf(Ct) && Jt(), i && i();else {
        var o = function t() {
          f.elem && f.elem.removeEventListener("transitionend", t), Dt && clearTimeout(Dt), It || (n(27, Rt = !1), bt && -1 === ["open", "opening", "closing"].indexOf(Ct) && Jt(), i && i());
        };

        n(28, It = !1), f.elem && f.elem.addEventListener("transitionend", o), f.elem && f.elem.style.opacity, Dt = setTimeout(o, 650);
      }
    }, Ct, Mt, Tt, Ht, Et, Lt, Pt, Wt, Dt, Ft, qt, Bt, o, d, Ut, Gt, Jt, function () {
      return Xt(!1);
    }, function () {
      return n(1, at = !at);
    }, function (t) {
      B[t ? "unshift" : "push"](function () {
        f.iconContainer = t, n(0, f);
      });
    }, function (t) {
      B[t ? "unshift" : "push"](function () {
        f.titleContainer = t, n(0, f);
      });
    }, function (t) {
      B[t ? "unshift" : "push"](function () {
        f.textContainer = t, n(0, f);
      });
    }, function (t) {
      B[t ? "unshift" : "push"](function () {
        f.content = t, n(0, f);
      });
    }, function (t) {
      B[t ? "unshift" : "push"](function () {
        f.container = t, n(0, f);
      });
    }, function (t) {
      B[t ? "unshift" : "push"](function () {
        f.elem = t, n(0, f);
      });
    }];
  }

  window && document.body ? Ut() : document.addEventListener("DOMContentLoaded", Ut);

  var Jt = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && u(t, e);
    }(r, t);
    var e,
        i = (e = r, function () {
      var t,
          n = c(e);

      if (l()) {
        var i = c(this).constructor;
        t = Reflect.construct(n, arguments, i);
      } else t = n.apply(this, arguments);

      return h(this, t);
    });

    function r(t) {
      var e;
      return n(this, r), function (t, e, n, i, o, r) {
        var s = arguments.length > 6 && void 0 !== arguments[6] ? arguments[6] : [-1],
            a = P;
        R(t);
        var c = e.props || {},
            u = t.$$ = {
          fragment: null,
          ctx: null,
          props: r,
          update: g,
          not_equal: o,
          bound: k(),
          on_mount: [],
          on_destroy: [],
          before_update: [],
          after_update: [],
          context: new Map(a ? a.$$.context : []),
          callbacks: k(),
          dirty: s
        },
            l = !1;

        if (u.ctx = n ? n(t, c, function (e, n) {
          var i = !(arguments.length <= 2) && arguments.length - 2 ? arguments.length <= 2 ? void 0 : arguments[2] : n;
          return u.ctx && o(u.ctx[e], u.ctx[e] = i) && (u.bound[e] && u.bound[e](i), l && mt(t, e)), n;
        }) : [], u.update(), l = !0, x(u.before_update), u.fragment = !!i && i(u.ctx), e.target) {
          if (e.hydrate) {
            var f = A(e.target);
            u.fragment && u.fragment.l(f), f.forEach(M);
          } else u.fragment && u.fragment.c();

          e.intro && rt(t.$$.fragment), ht(t, e.target, e.anchor), Z();
        }

        R(a);
      }(d(e = i.call(this)), t, Gt, It, w, {
        modules: 46,
        stack: 45,
        refs: 0,
        type: 2,
        title: 3,
        titleTrusted: 4,
        text: 5,
        textTrusted: 6,
        styling: 47,
        icons: 48,
        mode: 7,
        addClass: 8,
        addModalClass: 9,
        addModelessClass: 10,
        autoOpen: 49,
        width: 50,
        minHeight: 51,
        maxTextHeight: 52,
        icon: 11,
        animation: 12,
        animateSpeed: 13,
        shadow: 14,
        hide: 1,
        delay: 53,
        mouseReset: 54,
        closer: 15,
        closerHover: 16,
        sticker: 17,
        stickerHover: 18,
        labels: 19,
        remove: 55,
        destroy: 56,
        getState: 57,
        getTimer: 58,
        getStyle: 20,
        getIcon: 21,
        open: 59,
        close: 22,
        animateIn: 60,
        animateOut: 61,
        cancelClose: 62,
        queueClose: 63,
        _preventTimerClose: 64,
        on: 65,
        update: 66,
        fire: 67,
        addModuleClass: 68,
        removeModuleClass: 69,
        hasModuleClass: 70,
        getModuleHandled: 71,
        setModuleHandled: 72,
        getModuleOpen: 73,
        setModuleOpen: 74,
        setAnimating: 75,
        getAnimatingClass: 76,
        setAnimatingClass: 77,
        _getMoveClass: 78,
        _setMoveClass: 79,
        _setMasking: 80
      }, [-1, -1, -1, -1]), e;
    }

    return o(r, [{
      key: "modules",
      get: function get() {
        return this.$$.ctx[46];
      },
      set: function set(t) {
        this.$set({
          modules: t
        }), Z();
      }
    }, {
      key: "stack",
      get: function get() {
        return this.$$.ctx[45];
      },
      set: function set(t) {
        this.$set({
          stack: t
        }), Z();
      }
    }, {
      key: "refs",
      get: function get() {
        return this.$$.ctx[0];
      }
    }, {
      key: "type",
      get: function get() {
        return this.$$.ctx[2];
      },
      set: function set(t) {
        this.$set({
          type: t
        }), Z();
      }
    }, {
      key: "title",
      get: function get() {
        return this.$$.ctx[3];
      },
      set: function set(t) {
        this.$set({
          title: t
        }), Z();
      }
    }, {
      key: "titleTrusted",
      get: function get() {
        return this.$$.ctx[4];
      },
      set: function set(t) {
        this.$set({
          titleTrusted: t
        }), Z();
      }
    }, {
      key: "text",
      get: function get() {
        return this.$$.ctx[5];
      },
      set: function set(t) {
        this.$set({
          text: t
        }), Z();
      }
    }, {
      key: "textTrusted",
      get: function get() {
        return this.$$.ctx[6];
      },
      set: function set(t) {
        this.$set({
          textTrusted: t
        }), Z();
      }
    }, {
      key: "styling",
      get: function get() {
        return this.$$.ctx[47];
      },
      set: function set(t) {
        this.$set({
          styling: t
        }), Z();
      }
    }, {
      key: "icons",
      get: function get() {
        return this.$$.ctx[48];
      },
      set: function set(t) {
        this.$set({
          icons: t
        }), Z();
      }
    }, {
      key: "mode",
      get: function get() {
        return this.$$.ctx[7];
      },
      set: function set(t) {
        this.$set({
          mode: t
        }), Z();
      }
    }, {
      key: "addClass",
      get: function get() {
        return this.$$.ctx[8];
      },
      set: function set(t) {
        this.$set({
          addClass: t
        }), Z();
      }
    }, {
      key: "addModalClass",
      get: function get() {
        return this.$$.ctx[9];
      },
      set: function set(t) {
        this.$set({
          addModalClass: t
        }), Z();
      }
    }, {
      key: "addModelessClass",
      get: function get() {
        return this.$$.ctx[10];
      },
      set: function set(t) {
        this.$set({
          addModelessClass: t
        }), Z();
      }
    }, {
      key: "autoOpen",
      get: function get() {
        return this.$$.ctx[49];
      },
      set: function set(t) {
        this.$set({
          autoOpen: t
        }), Z();
      }
    }, {
      key: "width",
      get: function get() {
        return this.$$.ctx[50];
      },
      set: function set(t) {
        this.$set({
          width: t
        }), Z();
      }
    }, {
      key: "minHeight",
      get: function get() {
        return this.$$.ctx[51];
      },
      set: function set(t) {
        this.$set({
          minHeight: t
        }), Z();
      }
    }, {
      key: "maxTextHeight",
      get: function get() {
        return this.$$.ctx[52];
      },
      set: function set(t) {
        this.$set({
          maxTextHeight: t
        }), Z();
      }
    }, {
      key: "icon",
      get: function get() {
        return this.$$.ctx[11];
      },
      set: function set(t) {
        this.$set({
          icon: t
        }), Z();
      }
    }, {
      key: "animation",
      get: function get() {
        return this.$$.ctx[12];
      },
      set: function set(t) {
        this.$set({
          animation: t
        }), Z();
      }
    }, {
      key: "animateSpeed",
      get: function get() {
        return this.$$.ctx[13];
      },
      set: function set(t) {
        this.$set({
          animateSpeed: t
        }), Z();
      }
    }, {
      key: "shadow",
      get: function get() {
        return this.$$.ctx[14];
      },
      set: function set(t) {
        this.$set({
          shadow: t
        }), Z();
      }
    }, {
      key: "hide",
      get: function get() {
        return this.$$.ctx[1];
      },
      set: function set(t) {
        this.$set({
          hide: t
        }), Z();
      }
    }, {
      key: "delay",
      get: function get() {
        return this.$$.ctx[53];
      },
      set: function set(t) {
        this.$set({
          delay: t
        }), Z();
      }
    }, {
      key: "mouseReset",
      get: function get() {
        return this.$$.ctx[54];
      },
      set: function set(t) {
        this.$set({
          mouseReset: t
        }), Z();
      }
    }, {
      key: "closer",
      get: function get() {
        return this.$$.ctx[15];
      },
      set: function set(t) {
        this.$set({
          closer: t
        }), Z();
      }
    }, {
      key: "closerHover",
      get: function get() {
        return this.$$.ctx[16];
      },
      set: function set(t) {
        this.$set({
          closerHover: t
        }), Z();
      }
    }, {
      key: "sticker",
      get: function get() {
        return this.$$.ctx[17];
      },
      set: function set(t) {
        this.$set({
          sticker: t
        }), Z();
      }
    }, {
      key: "stickerHover",
      get: function get() {
        return this.$$.ctx[18];
      },
      set: function set(t) {
        this.$set({
          stickerHover: t
        }), Z();
      }
    }, {
      key: "labels",
      get: function get() {
        return this.$$.ctx[19];
      },
      set: function set(t) {
        this.$set({
          labels: t
        }), Z();
      }
    }, {
      key: "remove",
      get: function get() {
        return this.$$.ctx[55];
      },
      set: function set(t) {
        this.$set({
          remove: t
        }), Z();
      }
    }, {
      key: "destroy",
      get: function get() {
        return this.$$.ctx[56];
      },
      set: function set(t) {
        this.$set({
          destroy: t
        }), Z();
      }
    }, {
      key: "getState",
      get: function get() {
        return this.$$.ctx[57];
      }
    }, {
      key: "getTimer",
      get: function get() {
        return this.$$.ctx[58];
      }
    }, {
      key: "getStyle",
      get: function get() {
        return this.$$.ctx[20];
      }
    }, {
      key: "getIcon",
      get: function get() {
        return this.$$.ctx[21];
      }
    }, {
      key: "open",
      get: function get() {
        return this.$$.ctx[59];
      },
      set: function set(t) {
        this.$set({
          open: t
        }), Z();
      }
    }, {
      key: "close",
      get: function get() {
        return this.$$.ctx[22];
      },
      set: function set(t) {
        this.$set({
          close: t
        }), Z();
      }
    }, {
      key: "animateIn",
      get: function get() {
        return this.$$.ctx[60];
      },
      set: function set(t) {
        this.$set({
          animateIn: t
        }), Z();
      }
    }, {
      key: "animateOut",
      get: function get() {
        return this.$$.ctx[61];
      },
      set: function set(t) {
        this.$set({
          animateOut: t
        }), Z();
      }
    }, {
      key: "cancelClose",
      get: function get() {
        return this.$$.ctx[62];
      }
    }, {
      key: "queueClose",
      get: function get() {
        return this.$$.ctx[63];
      }
    }, {
      key: "_preventTimerClose",
      get: function get() {
        return this.$$.ctx[64];
      }
    }, {
      key: "on",
      get: function get() {
        return this.$$.ctx[65];
      }
    }, {
      key: "update",
      get: function get() {
        return this.$$.ctx[66];
      }
    }, {
      key: "fire",
      get: function get() {
        return this.$$.ctx[67];
      }
    }, {
      key: "addModuleClass",
      get: function get() {
        return this.$$.ctx[68];
      }
    }, {
      key: "removeModuleClass",
      get: function get() {
        return this.$$.ctx[69];
      }
    }, {
      key: "hasModuleClass",
      get: function get() {
        return this.$$.ctx[70];
      }
    }, {
      key: "getModuleHandled",
      get: function get() {
        return this.$$.ctx[71];
      }
    }, {
      key: "setModuleHandled",
      get: function get() {
        return this.$$.ctx[72];
      }
    }, {
      key: "getModuleOpen",
      get: function get() {
        return this.$$.ctx[73];
      }
    }, {
      key: "setModuleOpen",
      get: function get() {
        return this.$$.ctx[74];
      }
    }, {
      key: "setAnimating",
      get: function get() {
        return this.$$.ctx[75];
      }
    }, {
      key: "getAnimatingClass",
      get: function get() {
        return this.$$.ctx[76];
      }
    }, {
      key: "setAnimatingClass",
      get: function get() {
        return this.$$.ctx[77];
      }
    }, {
      key: "_getMoveClass",
      get: function get() {
        return this.$$.ctx[78];
      }
    }, {
      key: "_setMoveClass",
      get: function get() {
        return this.$$.ctx[79];
      }
    }, {
      key: "_setMasking",
      get: function get() {
        return this.$$.ctx[80];
      }
    }]), r;
  }(vt);

  t.Stack = yt, t.alert = function (t) {
    return gt(Dt(t));
  }, t["default"] = Jt, t.defaultModules = Bt, t.defaultStack = qt, t.defaults = zt, t.error = function (t) {
    return gt(Dt(t, "error"));
  }, t.info = function (t) {
    return gt(Dt(t, "info"));
  }, t.notice = function (t) {
    return gt(Dt(t, "notice"));
  }, t.success = function (t) {
    return gt(Dt(t, "success"));
  }, Object.defineProperty(t, "__esModule", {
    value: !0
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/font-awesome5-fix/dist/PNotifyFontAwesome5Fix.js":
/*!******************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/@pnotify/font-awesome5-fix/dist/PNotifyFontAwesome5Fix.js ***!
  \******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (t, n) {
  "object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module ? n(exports) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (n),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(this, function (t) {
  "use strict";

  function n(t) {
    return (n = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function e(t, n) {
    if (!(t instanceof n)) throw new TypeError("Cannot call a class as a function");
  }

  function r(t, n) {
    for (var e = 0; e < n.length; e++) {
      var r = n[e];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function o(t) {
    return (o = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function c(t, n) {
    return (c = Object.setPrototypeOf || function (t, n) {
      return t.__proto__ = n, t;
    })(t, n);
  }

  function i() {
    if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
    if (Reflect.construct.sham) return !1;
    if ("function" == typeof Proxy) return !0;

    try {
      return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
    } catch (t) {
      return !1;
    }
  }

  function u(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function f(t, n) {
    return !n || "object" != _typeof(n) && "function" != typeof n ? u(t) : n;
  }

  function a(t) {
    return function (t) {
      if (Array.isArray(t)) return l(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(t) || function (t, n) {
      if (!t) return;
      if ("string" == typeof t) return l(t, n);
      var e = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === e && t.constructor && (e = t.constructor.name);
      if ("Map" === e || "Set" === e) return Array.from(e);
      if ("Arguments" === e || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e)) return l(t, n);
    }(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function l(t, n) {
    (null == n || n > t.length) && (n = t.length);

    for (var e = 0, r = new Array(n); e < n; e++) {
      r[e] = t[e];
    }

    return r;
  }

  function s() {}

  function p(t) {
    return t();
  }

  function y() {
    return Object.create(null);
  }

  function d(t) {
    t.forEach(p);
  }

  function h(t) {
    return "function" == typeof t;
  }

  function m(t, e) {
    return t != t ? e == e : t !== e || t && "object" === n(t) || "function" == typeof t;
  }

  function g(t) {
    t.parentNode.removeChild(t);
  }

  function b(t) {
    return Array.from(t.childNodes);
  }

  var v;

  function $(t) {
    v = t;
  }

  function _(t) {
    (function () {
      if (!v) throw new Error("Function called outside component initialization");
      return v;
    })().$$.on_destroy.push(t);
  }

  var w = [],
      x = [],
      k = [],
      O = [],
      j = Promise.resolve(),
      S = !1;

  function I() {
    S || (S = !0, j.then(C));
  }

  function P() {
    return I(), j;
  }

  function A(t) {
    k.push(t);
  }

  var E = !1,
      R = new Set();

  function C() {
    if (!E) {
      E = !0;

      do {
        for (var t = 0; t < w.length; t += 1) {
          var n = w[t];
          $(n), F(n.$$);
        }

        for (w.length = 0; x.length;) {
          x.pop()();
        }

        for (var e = 0; e < k.length; e += 1) {
          var r = k[e];
          R.has(r) || (R.add(r), r());
        }

        k.length = 0;
      } while (w.length);

      for (; O.length;) {
        O.pop()();
      }

      S = !1, E = !1, R.clear();
    }
  }

  function F(t) {
    if (null !== t.fragment) {
      t.update(), d(t.before_update);
      var n = t.dirty;
      t.dirty = [-1], t.fragment && t.fragment.p(t.ctx, n), t.after_update.forEach(A);
    }
  }

  var M = new Set();

  function N(t, n) {
    t && t.i && (M["delete"](t), t.i(n));
  }

  function T(t, n, e) {
    var r = t.$$,
        o = r.fragment,
        c = r.on_mount,
        i = r.on_destroy,
        u = r.after_update;
    o && o.m(n, e), A(function () {
      var n = c.map(p).filter(h);
      i ? i.push.apply(i, a(n)) : d(n), t.$$.on_mount = [];
    }), u.forEach(A);
  }

  function D(t, n) {
    -1 === t.$$.dirty[0] && (w.push(t), I(), t.$$.dirty.fill(0)), t.$$.dirty[n / 31 | 0] |= 1 << n % 31;
  }

  function q(t, n, e) {
    var r,
        o,
        c,
        i = n.self,
        u = void 0 === i ? null : i,
        f = !1,
        a = !1,
        l = (u.icon, !0 === u.icon ? u.getIcon(u.type) : u.icon),
        s = "".concat(u.getIcon("sticker"), " ").concat(u.hide ? u.getIcon("unstuck") : u.getIcon("stuck")),
        p = u.on("pnotify:update", function () {
      f || (r = u.icon, (o = !0 === u.icon ? u.getIcon(u.type) : u.icon) !== l && ("string" == typeof o && o.match(/(^| )fa[srlb]($| )/) || "string" == typeof l && l.match(/(^| )fa[srlb]($| )/)) ? (e(0, u.icon = !1, u), f = !0, P().then(function () {
        e(0, u.icon = r, u), f = !1, r, l = o;
      })) : (r, l = o));
    }),
        y = u.on("pnotify:update", function () {
      a || (c = "".concat(u.getIcon("sticker"), " ").concat(u.hide ? u.getIcon("unstuck") : u.getIcon("stuck")), u.sticker && c !== s && "string" == typeof c && c.match(/(^| )fa[srlb]($| )/) ? (e(0, u.sticker = !1, u), a = !0, P().then(function () {
        e(0, u.sticker = !0, u), a = !1, s = c;
      })) : s = c);
    });
    return _(function () {
      p && p(), y && y();
    }), t.$set = function (t) {
      "self" in t && e(0, u = t.self);
    }, [u];
  }

  var z = function (t) {
    !function (t, n) {
      if ("function" != typeof n && null !== n) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(n && n.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), n && c(t, n);
    }(a, t);
    var n,
        r = (n = a, function () {
      var t,
          e = o(n);

      if (i()) {
        var r = o(this).constructor;
        t = Reflect.construct(e, arguments, r);
      } else t = e.apply(this, arguments);

      return f(this, t);
    });

    function a(t) {
      var n;
      return e(this, a), function (t, n, e, r, o, c) {
        var i = arguments.length > 6 && void 0 !== arguments[6] ? arguments[6] : [-1],
            u = v;
        $(t);
        var f = n.props || {},
            a = t.$$ = {
          fragment: null,
          ctx: null,
          props: c,
          update: s,
          not_equal: o,
          bound: y(),
          on_mount: [],
          on_destroy: [],
          before_update: [],
          after_update: [],
          context: new Map(u ? u.$$.context : []),
          callbacks: y(),
          dirty: i
        },
            l = !1;

        if (a.ctx = e ? e(t, f, function (n, e) {
          var r = !(arguments.length <= 2) && arguments.length - 2 ? arguments.length <= 2 ? void 0 : arguments[2] : e;
          return a.ctx && o(a.ctx[n], a.ctx[n] = r) && (a.bound[n] && a.bound[n](r), l && D(t, n)), e;
        }) : [], a.update(), l = !0, d(a.before_update), a.fragment = !!r && r(a.ctx), n.target) {
          if (n.hydrate) {
            var p = b(n.target);
            a.fragment && a.fragment.l(p), p.forEach(g);
          } else a.fragment && a.fragment.c();

          n.intro && N(t.$$.fragment), T(t, n.target, n.anchor), C();
        }

        $(u);
      }(u(n = r.call(this)), t, q, null, m, {
        self: 0
      }), n;
    }

    return a;
  }(function () {
    function t() {
      e(this, t);
    }

    var n, o, c;
    return n = t, (o = [{
      key: "$destroy",
      value: function value() {
        var t, n;
        t = 1, null !== (n = this.$$).fragment && (d(n.on_destroy), n.fragment && n.fragment.d(t), n.on_destroy = n.fragment = null, n.ctx = []), this.$destroy = s;
      }
    }, {
      key: "$on",
      value: function value(t, n) {
        var e = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
        return e.push(n), function () {
          var t = e.indexOf(n);
          -1 !== t && e.splice(t, 1);
        };
      }
    }, {
      key: "$set",
      value: function value() {}
    }]) && r(n.prototype, o), c && r(n, c), t;
  }());

  t["default"] = z, t.defaults = {}, t.position = "PrependContainer", Object.defineProperty(t, "__esModule", {
    value: !0
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/font-awesome5/dist/PNotifyFontAwesome5.js":
/*!***********************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/@pnotify/font-awesome5/dist/PNotifyFontAwesome5.js ***!
  \***********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (t, e) {
  "object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module ? e(exports) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (e),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(this, function (t) {
  "use strict";

  function e(t) {
    return (e = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function n(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function r(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function o(t) {
    return (o = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function f(t, e) {
    return (f = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function a() {
    if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
    if (Reflect.construct.sham) return !1;
    if ("function" == typeof Proxy) return !0;

    try {
      return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
    } catch (t) {
      return !1;
    }
  }

  function i(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function u(t, e) {
    return !e || "object" != _typeof(e) && "function" != typeof e ? i(t) : e;
  }

  function c(t) {
    return function (t) {
      if (Array.isArray(t)) return l(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(t) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return l(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(n);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return l(t, e);
    }(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function l(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = new Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function s() {}

  function p(t) {
    return t();
  }

  function y() {
    return Object.create(null);
  }

  function d(t) {
    t.forEach(p);
  }

  function h(t) {
    return "function" == typeof t;
  }

  function m(t, n) {
    return t != t ? n == n : t !== n || t && "object" === e(t) || "function" == typeof t;
  }

  function b(t) {
    t.parentNode.removeChild(t);
  }

  function g(t) {
    return Array.from(t.childNodes);
  }

  var v;

  function $(t) {
    v = t;
  }

  var _ = [],
      x = [],
      w = [],
      O = [],
      j = Promise.resolve(),
      S = !1;

  function k(t) {
    w.push(t);
  }

  var P = !1,
      A = new Set();

  function E() {
    if (!P) {
      P = !0;

      do {
        for (var t = 0; t < _.length; t += 1) {
          var e = _[t];
          $(e), R(e.$$);
        }

        for (_.length = 0; x.length;) {
          x.pop()();
        }

        for (var n = 0; n < w.length; n += 1) {
          var r = w[n];
          A.has(r) || (A.add(r), r());
        }

        w.length = 0;
      } while (_.length);

      for (; O.length;) {
        O.pop()();
      }

      S = !1, P = !1, A.clear();
    }
  }

  function R(t) {
    if (null !== t.fragment) {
      t.update(), d(t.before_update);
      var e = t.dirty;
      t.dirty = [-1], t.fragment && t.fragment.p(t.ctx, e), t.after_update.forEach(k);
    }
  }

  var C = new Set();

  function I(t, e) {
    t && t.i && (C["delete"](t), t.i(e));
  }

  function M(t, e, n) {
    var r = t.$$,
        o = r.fragment,
        f = r.on_mount,
        a = r.on_destroy,
        i = r.after_update;
    o && o.m(e, n), k(function () {
      var e = f.map(p).filter(h);
      a ? a.push.apply(a, c(e)) : d(e), t.$$.on_mount = [];
    }), i.forEach(k);
  }

  function N(t, e) {
    -1 === t.$$.dirty[0] && (_.push(t), S || (S = !0, j.then(E)), t.$$.dirty.fill(0)), t.$$.dirty[e / 31 | 0] |= 1 << e % 31;
  }

  var T = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && f(t, e);
    }(c, t);
    var e,
        r = (e = c, function () {
      var t,
          n = o(e);

      if (a()) {
        var r = o(this).constructor;
        t = Reflect.construct(n, arguments, r);
      } else t = n.apply(this, arguments);

      return u(this, t);
    });

    function c(t) {
      var e;
      return n(this, c), function (t, e, n, r, o, f) {
        var a = arguments.length > 6 && void 0 !== arguments[6] ? arguments[6] : [-1],
            i = v;
        $(t);
        var u = e.props || {},
            c = t.$$ = {
          fragment: null,
          ctx: null,
          props: f,
          update: s,
          not_equal: o,
          bound: y(),
          on_mount: [],
          on_destroy: [],
          before_update: [],
          after_update: [],
          context: new Map(i ? i.$$.context : []),
          callbacks: y(),
          dirty: a
        },
            l = !1;

        if (c.ctx = n ? n(t, u, function (e, n) {
          var r = !(arguments.length <= 2) && arguments.length - 2 ? arguments.length <= 2 ? void 0 : arguments[2] : n;
          return c.ctx && o(c.ctx[e], c.ctx[e] = r) && (c.bound[e] && c.bound[e](r), l && N(t, e)), n;
        }) : [], c.update(), l = !0, d(c.before_update), c.fragment = !!r && r(c.ctx), e.target) {
          if (e.hydrate) {
            var p = g(e.target);
            c.fragment && c.fragment.l(p), p.forEach(b);
          } else c.fragment && c.fragment.c();

          e.intro && I(t.$$.fragment), M(t, e.target, e.anchor), E();
        }

        $(i);
      }(i(e = r.call(this)), t, null, null, m, {}), e;
    }

    return c;
  }(function () {
    function t() {
      n(this, t);
    }

    var e, o, f;
    return e = t, (o = [{
      key: "$destroy",
      value: function value() {
        var t, e;
        t = 1, null !== (e = this.$$).fragment && (d(e.on_destroy), e.fragment && e.fragment.d(t), e.on_destroy = e.fragment = null, e.ctx = []), this.$destroy = s;
      }
    }, {
      key: "$on",
      value: function value(t, e) {
        var n = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
        return n.push(e), function () {
          var t = n.indexOf(e);
          -1 !== t && n.splice(t, 1);
        };
      }
    }, {
      key: "$set",
      value: function value() {}
    }]) && r(e.prototype, o), f && r(e, f), t;
  }());

  t["default"] = T, t.defaults = {}, t.init = function (t) {
    t.defaults.icons = {
      prefix: "fontawesome5",
      notice: "fas fa-exclamation-circle",
      info: "fas fa-info-circle",
      success: "fas fa-check-circle",
      error: "fas fa-exclamation-triangle",
      closer: "fas fa-times",
      sticker: "fas",
      stuck: "fa-play",
      unstuck: "fa-pause",
      refresh: "fas fa-sync"
    };
  }, t.position = "PrependContainer", Object.defineProperty(t, "__esModule", {
    value: !0
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/@pnotify/mobile/dist/PNotifyMobile.js":
/*!**********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/@pnotify/mobile/dist/PNotifyMobile.js ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (t, e) {
  "object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module ? e(exports) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (e),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(this, function (t) {
  "use strict";

  function e(t) {
    return (e = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function n(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function r(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function o(t) {
    return (o = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function i(t, e) {
    return (i = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function f() {
    if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
    if (Reflect.construct.sham) return !1;
    if ("function" == typeof Proxy) return !0;

    try {
      return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
    } catch (t) {
      return !1;
    }
  }

  function c(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function u(t, e) {
    return !e || "object" != _typeof(e) && "function" != typeof e ? c(t) : e;
  }

  function a(t) {
    return function (t) {
      if (Array.isArray(t)) return s(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(t) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return s(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(n);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return s(t, e);
    }(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function s(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = new Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function l() {}

  function p(t) {
    return t();
  }

  function y() {
    return Object.create(null);
  }

  function d(t) {
    t.forEach(p);
  }

  function m(t) {
    return "function" == typeof t;
  }

  function h(t, n) {
    return t != t ? n == n : t !== n || t && "object" === e(t) || "function" == typeof t;
  }

  function v(t) {
    t.parentNode.removeChild(t);
  }

  function g(t) {
    return Array.from(t.childNodes);
  }

  var _;

  function b(t) {
    _ = t;
  }

  function $() {
    if (!_) throw new Error("Function called outside component initialization");
    return _;
  }

  var w = [],
      P = [],
      x = [],
      O = [],
      j = Promise.resolve(),
      S = !1;

  function E(t) {
    x.push(t);
  }

  var k = !1,
      A = new Set();

  function D() {
    if (!k) {
      k = !0;

      do {
        for (var t = 0; t < w.length; t += 1) {
          var e = w[t];
          b(e), C(e.$$);
        }

        for (w.length = 0; P.length;) {
          P.pop()();
        }

        for (var n = 0; n < x.length; n += 1) {
          var r = x[n];
          A.has(r) || (A.add(r), r());
        }

        x.length = 0;
      } while (w.length);

      for (; O.length;) {
        O.pop()();
      }

      S = !1, k = !1, A.clear();
    }
  }

  function C(t) {
    if (null !== t.fragment) {
      t.update(), d(t.before_update);
      var e = t.dirty;
      t.dirty = [-1], t.fragment && t.fragment.p(t.ctx, e), t.after_update.forEach(E);
    }
  }

  var M = new Set();

  function R(t, e) {
    t && t.i && (M["delete"](t), t.i(e));
  }

  var W = "undefined" != typeof window ? window : global;

  function q(t, e, n) {
    var r = t.$$,
        o = r.fragment,
        i = r.on_mount,
        f = r.on_destroy,
        c = r.after_update;
    o && o.m(e, n), E(function () {
      var e = i.map(p).filter(m);
      f ? f.push.apply(f, a(e)) : d(e), t.$$.on_mount = [];
    }), c.forEach(E);
  }

  function I(t, e) {
    -1 === t.$$.dirty[0] && (w.push(t), S || (S = !0, j.then(D)), t.$$.dirty.fill(0)), t.$$.dirty[e / 31 | 0] |= 1 << e % 31;
  }

  var L = function () {
    function t() {
      n(this, t);
    }

    var e, o, i;
    return e = t, (o = [{
      key: "$destroy",
      value: function value() {
        var t, e;
        t = 1, null !== (e = this.$$).fragment && (d(e.on_destroy), e.fragment && e.fragment.d(t), e.on_destroy = e.fragment = null, e.ctx = []), this.$destroy = l;
      }
    }, {
      key: "$on",
      value: function value(t, e) {
        var n = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
        return n.push(e), function () {
          var t = n.indexOf(e);
          -1 !== t && n.splice(t, 1);
        };
      }
    }, {
      key: "$set",
      value: function value() {}
    }]) && r(e.prototype, o), i && r(e, i), t;
  }(),
      N = W.window;

  function T(t) {
    var e;
    return {
      c: l,
      m: function m(n, r, o) {
        var i, f, c, u;
        o && e(), i = N, f = "resize", c = t[11], i.addEventListener(f, c, u), e = function e() {
          return i.removeEventListener(f, c, u);
        };
      },
      p: l,
      i: l,
      o: l,
      d: function d(t) {
        e();
      }
    };
  }

  var z = {
    swipeDismiss: !0
  };

  function X(t, e, n) {
    var r,
        o = e.self,
        i = void 0 === o ? null : o,
        f = e.swipeDismiss,
        c = void 0 === f ? z.swipeDismiss : f,
        u = null,
        a = null,
        s = null,
        l = null,
        p = "left",
        y = "X",
        d = "Width",
        m = window.innerWidth,
        h = [];
    r = function r() {
      h = [i.on("touchstart", function (t) {
        if (c) {
          var e = i.stack;
          if (e) switch (e.dir1) {
            case "up":
            case "down":
              p = "left", y = "X", d = "Width";
              break;

            case "left":
            case "right":
              p = "top", y = "Y", d = "Height";
          }
          u = t.touches[0]["screen".concat(y)], s = i.refs.elem["scroll".concat(d)], l = window.getComputedStyle(i.refs.elem).opacity, n(1, i.refs.container.style[p] = 0, i);
        }
      }), i.on("touchmove", function (t) {
        if (u && c) {
          var e = t.touches[0]["screen".concat(y)];
          a = e - u;
          var r = (1 - Math.abs(a) / s) * l;
          n(1, i.refs.elem.style.opacity = r, i), n(1, i.refs.container.style[p] = "".concat(a, "px"), i);
        }
      }), i.on("touchend", function () {
        if (u && c) {
          if (i.refs.container.classList.add("pnotify-mobile-animate-left"), Math.abs(a) > 40) {
            var t = a < 0 ? -2 * s : 2 * s;
            n(1, i.refs.elem.style.opacity = 0, i), n(1, i.refs.container.style[p] = "".concat(t, "px"), i), i.close();
          } else i.refs.elem.style.removeProperty("opacity"), i.refs.container.style.removeProperty(p);

          u = null, a = null, s = null, l = null;
        }
      }), i.on("touchcancel", function () {
        u && c && (i.refs.elem.style.removeProperty("opacity"), i.refs.container.style.removeProperty(p), u = null, a = null, s = null, l = null);
      }), i.on("pnotify:afterClose", function () {
        c && (i.refs.elem.style.removeProperty("opacity"), i.refs.container.style.removeProperty("left"), i.refs.container.style.removeProperty("top"));
      })];
    }, $().$$.on_mount.push(r), function (t) {
      $().$$.on_destroy.push(t);
    }(function () {
      h.forEach(function (t) {
        return t();
      });
    });
    return t.$set = function (t) {
      "self" in t && n(1, i = t.self), "swipeDismiss" in t && n(2, c = t.swipeDismiss);
    }, t.$$.update = function () {
      if (3 & t.$$.dirty) {
        var e = i.stack;
        e && (m <= 480 ? "_m_spacing1" in e || (e._m_spacing1 = e.spacing1, e._m_firstpos1 = e.firstpos1, e._m_spacing2 = e.spacing2, e._m_firstpos2 = e.firstpos2, e.spacing1 = 0, e.firstpos1 = 0, e.spacing2 = 0, e.firstpos2 = 0, e.queuePosition()) : "_m_spacing1" in e && (e.spacing1 = e._m_spacing1, delete e._m_spacing1, e.firstpos1 = e._m_firstpos1, delete e._m_firstpos1, e.spacing2 = e._m_spacing2, delete e._m_spacing2, e.firstpos2 = e._m_firstpos2, delete e._m_firstpos2, e.queuePosition()));
      }
    }, [m, i, c, u, a, s, l, p, y, d, h, function () {
      return n(0, m = window.innerWidth);
    }];
  }

  var F = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && i(t, e);
    }(a, t);
    var e,
        r = (e = a, function () {
      var t,
          n = o(e);

      if (f()) {
        var r = o(this).constructor;
        t = Reflect.construct(n, arguments, r);
      } else t = n.apply(this, arguments);

      return u(this, t);
    });

    function a(t) {
      var e;
      return n(this, a), function (t, e, n, r, o, i) {
        var f = arguments.length > 6 && void 0 !== arguments[6] ? arguments[6] : [-1],
            c = _;
        b(t);
        var u = e.props || {},
            a = t.$$ = {
          fragment: null,
          ctx: null,
          props: i,
          update: l,
          not_equal: o,
          bound: y(),
          on_mount: [],
          on_destroy: [],
          before_update: [],
          after_update: [],
          context: new Map(c ? c.$$.context : []),
          callbacks: y(),
          dirty: f
        },
            s = !1;

        if (a.ctx = n ? n(t, u, function (e, n) {
          var r = !(arguments.length <= 2) && arguments.length - 2 ? arguments.length <= 2 ? void 0 : arguments[2] : n;
          return a.ctx && o(a.ctx[e], a.ctx[e] = r) && (a.bound[e] && a.bound[e](r), s && I(t, e)), n;
        }) : [], a.update(), s = !0, d(a.before_update), a.fragment = !!r && r(a.ctx), e.target) {
          if (e.hydrate) {
            var p = g(e.target);
            a.fragment && a.fragment.l(p), p.forEach(v);
          } else a.fragment && a.fragment.c();

          e.intro && R(t.$$.fragment), q(t, e.target, e.anchor), D();
        }

        b(c);
      }(c(e = r.call(this)), t, X, T, h, {
        self: 1,
        swipeDismiss: 2
      }), e;
    }

    return a;
  }(L);

  t["default"] = F, t.defaults = z, t.position = "PrependContainer", Object.defineProperty(t, "__esModule", {
    value: !0
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/blueimp-file-upload/js/jquery.fileupload.js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/blueimp-file-upload/js/jquery.fileupload.js ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*
 * jQuery File Upload Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global define, require */

/* eslint-disable new-cap */
(function (factory) {
  'use strict';

  if (true) {
    // Register as an anonymous AMD module:
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! jquery-ui/ui/widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  'use strict'; // Detect file input support, based on
  // https://viljamis.com/2012/file-upload-support-on-mobile/

  $.support.fileInput = !(new RegExp( // Handle devices which give false positives for the feature detection:
  '(Android (1\\.[0156]|2\\.[01]))' + '|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)' + '|(w(eb)?OSBrowser)|(webOS)' + '|(Kindle/(1\\.0|2\\.[05]|3\\.0))').test(window.navigator.userAgent) || // Feature detection for all other devices:
  $('<input type="file"/>').prop('disabled')); // The FileReader API is not actually used, but works as feature detection,
  // as some Safari versions (5?) support XHR file uploads via the FormData API,
  // but not non-multipart XHR file uploads.
  // window.XMLHttpRequestUpload is not available on IE10, so we check for
  // window.ProgressEvent instead to detect XHR2 file upload capability:

  $.support.xhrFileUpload = !!(window.ProgressEvent && window.FileReader);
  $.support.xhrFormDataFileUpload = !!window.FormData; // Detect support for Blob slicing (required for chunked uploads):

  $.support.blobSlice = window.Blob && (Blob.prototype.slice || Blob.prototype.webkitSlice || Blob.prototype.mozSlice);
  /**
   * Helper function to create drag handlers for dragover/dragenter/dragleave
   *
   * @param {string} type Event type
   * @returns {Function} Drag handler
   */

  function getDragHandler(type) {
    var isDragOver = type === 'dragover';
    return function (e) {
      e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
      var dataTransfer = e.dataTransfer;

      if (dataTransfer && $.inArray('Files', dataTransfer.types) !== -1 && this._trigger(type, $.Event(type, {
        delegatedEvent: e
      })) !== false) {
        e.preventDefault();

        if (isDragOver) {
          dataTransfer.dropEffect = 'copy';
        }
      }
    };
  } // The fileupload widget listens for change events on file input fields defined
  // via fileInput setting and paste or drop events of the given dropZone.
  // In addition to the default jQuery Widget methods, the fileupload widget
  // exposes the "add" and "send" methods, to add or directly send files using
  // the fileupload API.
  // By default, files added via file input selection, paste, drag & drop or
  // "add" method are uploaded immediately, but it is possible to override
  // the "add" callback option to queue file uploads.


  $.widget('blueimp.fileupload', {
    options: {
      // The drop target element(s), by the default the complete document.
      // Set to null to disable drag & drop support:
      dropZone: $(document),
      // The paste target element(s), by the default undefined.
      // Set to a DOM node or jQuery object to enable file pasting:
      pasteZone: undefined,
      // The file input field(s), that are listened to for change events.
      // If undefined, it is set to the file input fields inside
      // of the widget element on plugin initialization.
      // Set to null to disable the change listener.
      fileInput: undefined,
      // By default, the file input field is replaced with a clone after
      // each input field change event. This is required for iframe transport
      // queues and allows change events to be fired for the same file
      // selection, but can be disabled by setting the following option to false:
      replaceFileInput: true,
      // The parameter name for the file form data (the request argument name).
      // If undefined or empty, the name property of the file input field is
      // used, or "files[]" if the file input name property is also empty,
      // can be a string or an array of strings:
      paramName: undefined,
      // By default, each file of a selection is uploaded using an individual
      // request for XHR type uploads. Set to false to upload file
      // selections in one request each:
      singleFileUploads: true,
      // To limit the number of files uploaded with one XHR request,
      // set the following option to an integer greater than 0:
      limitMultiFileUploads: undefined,
      // The following option limits the number of files uploaded with one
      // XHR request to keep the request size under or equal to the defined
      // limit in bytes:
      limitMultiFileUploadSize: undefined,
      // Multipart file uploads add a number of bytes to each uploaded file,
      // therefore the following option adds an overhead for each file used
      // in the limitMultiFileUploadSize configuration:
      limitMultiFileUploadSizeOverhead: 512,
      // Set the following option to true to issue all file upload requests
      // in a sequential order:
      sequentialUploads: false,
      // To limit the number of concurrent uploads,
      // set the following option to an integer greater than 0:
      limitConcurrentUploads: undefined,
      // Set the following option to true to force iframe transport uploads:
      forceIframeTransport: false,
      // Set the following option to the location of a redirect url on the
      // origin server, for cross-domain iframe transport uploads:
      redirect: undefined,
      // The parameter name for the redirect url, sent as part of the form
      // data and set to 'redirect' if this option is empty:
      redirectParamName: undefined,
      // Set the following option to the location of a postMessage window,
      // to enable postMessage transport uploads:
      postMessage: undefined,
      // By default, XHR file uploads are sent as multipart/form-data.
      // The iframe transport is always using multipart/form-data.
      // Set to false to enable non-multipart XHR uploads:
      multipart: true,
      // To upload large files in smaller chunks, set the following option
      // to a preferred maximum chunk size. If set to 0, null or undefined,
      // or the browser does not support the required Blob API, files will
      // be uploaded as a whole.
      maxChunkSize: undefined,
      // When a non-multipart upload or a chunked multipart upload has been
      // aborted, this option can be used to resume the upload by setting
      // it to the size of the already uploaded bytes. This option is most
      // useful when modifying the options object inside of the "add" or
      // "send" callbacks, as the options are cloned for each file upload.
      uploadedBytes: undefined,
      // By default, failed (abort or error) file uploads are removed from the
      // global progress calculation. Set the following option to false to
      // prevent recalculating the global progress data:
      recalculateProgress: true,
      // Interval in milliseconds to calculate and trigger progress events:
      progressInterval: 100,
      // Interval in milliseconds to calculate progress bitrate:
      bitrateInterval: 500,
      // By default, uploads are started automatically when adding files:
      autoUpload: true,
      // By default, duplicate file names are expected to be handled on
      // the server-side. If this is not possible (e.g. when uploading
      // files directly to Amazon S3), the following option can be set to
      // an empty object or an object mapping existing filenames, e.g.:
      // { "image.jpg": true, "image (1).jpg": true }
      // If it is set, all files will be uploaded with unique filenames,
      // adding increasing number suffixes if necessary, e.g.:
      // "image (2).jpg"
      uniqueFilenames: undefined,
      // Error and info messages:
      messages: {
        uploadedBytes: 'Uploaded bytes exceed file size'
      },
      // Translation function, gets the message key to be translated
      // and an object with context specific data as arguments:
      i18n: function i18n(message, context) {
        // eslint-disable-next-line no-param-reassign
        message = this.messages[message] || message.toString();

        if (context) {
          $.each(context, function (key, value) {
            // eslint-disable-next-line no-param-reassign
            message = message.replace('{' + key + '}', value);
          });
        }

        return message;
      },
      // Additional form data to be sent along with the file uploads can be set
      // using this option, which accepts an array of objects with name and
      // value properties, a function returning such an array, a FormData
      // object (for XHR file uploads), or a simple object.
      // The form of the first fileInput is given as parameter to the function:
      formData: function formData(form) {
        return form.serializeArray();
      },
      // The add callback is invoked as soon as files are added to the fileupload
      // widget (via file input selection, drag & drop, paste or add API call).
      // If the singleFileUploads option is enabled, this callback will be
      // called once for each file in the selection for XHR file uploads, else
      // once for each file selection.
      //
      // The upload starts when the submit method is invoked on the data parameter.
      // The data object contains a files property holding the added files
      // and allows you to override plugin options as well as define ajax settings.
      //
      // Listeners for this callback can also be bound the following way:
      // .on('fileuploadadd', func);
      //
      // data.submit() returns a Promise object and allows to attach additional
      // handlers using jQuery's Deferred callbacks:
      // data.submit().done(func).fail(func).always(func);
      add: function add(e, data) {
        if (e.isDefaultPrevented()) {
          return false;
        }

        if (data.autoUpload || data.autoUpload !== false && $(this).fileupload('option', 'autoUpload')) {
          data.process().done(function () {
            data.submit();
          });
        }
      },
      // Other callbacks:
      // Callback for the submit event of each file upload:
      // submit: function (e, data) {}, // .on('fileuploadsubmit', func);
      // Callback for the start of each file upload request:
      // send: function (e, data) {}, // .on('fileuploadsend', func);
      // Callback for successful uploads:
      // done: function (e, data) {}, // .on('fileuploaddone', func);
      // Callback for failed (abort or error) uploads:
      // fail: function (e, data) {}, // .on('fileuploadfail', func);
      // Callback for completed (success, abort or error) requests:
      // always: function (e, data) {}, // .on('fileuploadalways', func);
      // Callback for upload progress events:
      // progress: function (e, data) {}, // .on('fileuploadprogress', func);
      // Callback for global upload progress events:
      // progressall: function (e, data) {}, // .on('fileuploadprogressall', func);
      // Callback for uploads start, equivalent to the global ajaxStart event:
      // start: function (e) {}, // .on('fileuploadstart', func);
      // Callback for uploads stop, equivalent to the global ajaxStop event:
      // stop: function (e) {}, // .on('fileuploadstop', func);
      // Callback for change events of the fileInput(s):
      // change: function (e, data) {}, // .on('fileuploadchange', func);
      // Callback for paste events to the pasteZone(s):
      // paste: function (e, data) {}, // .on('fileuploadpaste', func);
      // Callback for drop events of the dropZone(s):
      // drop: function (e, data) {}, // .on('fileuploaddrop', func);
      // Callback for dragover events of the dropZone(s):
      // dragover: function (e) {}, // .on('fileuploaddragover', func);
      // Callback before the start of each chunk upload request (before form data initialization):
      // chunkbeforesend: function (e, data) {}, // .on('fileuploadchunkbeforesend', func);
      // Callback for the start of each chunk upload request:
      // chunksend: function (e, data) {}, // .on('fileuploadchunksend', func);
      // Callback for successful chunk uploads:
      // chunkdone: function (e, data) {}, // .on('fileuploadchunkdone', func);
      // Callback for failed (abort or error) chunk uploads:
      // chunkfail: function (e, data) {}, // .on('fileuploadchunkfail', func);
      // Callback for completed (success, abort or error) chunk upload requests:
      // chunkalways: function (e, data) {}, // .on('fileuploadchunkalways', func);
      // The plugin options are used as settings object for the ajax calls.
      // The following are jQuery ajax settings required for the file uploads:
      processData: false,
      contentType: false,
      cache: false,
      timeout: 0
    },
    // jQuery versions before 1.8 require promise.pipe if the return value is
    // used, as promise.then in older versions has a different behavior, see:
    // https://blog.jquery.com/2012/08/09/jquery-1-8-released/
    // https://bugs.jquery.com/ticket/11010
    // https://github.com/blueimp/jQuery-File-Upload/pull/3435
    _promisePipe: function () {
      var parts = $.fn.jquery.split('.');
      return Number(parts[0]) > 1 || Number(parts[1]) > 7 ? 'then' : 'pipe';
    }(),
    // A list of options that require reinitializing event listeners and/or
    // special initialization code:
    _specialOptions: ['fileInput', 'dropZone', 'pasteZone', 'multipart', 'forceIframeTransport'],
    _blobSlice: $.support.blobSlice && function () {
      var slice = this.slice || this.webkitSlice || this.mozSlice;
      return slice.apply(this, arguments);
    },
    _BitrateTimer: function _BitrateTimer() {
      this.timestamp = Date.now ? Date.now() : new Date().getTime();
      this.loaded = 0;
      this.bitrate = 0;

      this.getBitrate = function (now, loaded, interval) {
        var timeDiff = now - this.timestamp;

        if (!this.bitrate || !interval || timeDiff > interval) {
          this.bitrate = (loaded - this.loaded) * (1000 / timeDiff) * 8;
          this.loaded = loaded;
          this.timestamp = now;
        }

        return this.bitrate;
      };
    },
    _isXHRUpload: function _isXHRUpload(options) {
      return !options.forceIframeTransport && (!options.multipart && $.support.xhrFileUpload || $.support.xhrFormDataFileUpload);
    },
    _getFormData: function _getFormData(options) {
      var formData;

      if ($.type(options.formData) === 'function') {
        return options.formData(options.form);
      }

      if ($.isArray(options.formData)) {
        return options.formData;
      }

      if ($.type(options.formData) === 'object') {
        formData = [];
        $.each(options.formData, function (name, value) {
          formData.push({
            name: name,
            value: value
          });
        });
        return formData;
      }

      return [];
    },
    _getTotal: function _getTotal(files) {
      var total = 0;
      $.each(files, function (index, file) {
        total += file.size || 1;
      });
      return total;
    },
    _initProgressObject: function _initProgressObject(obj) {
      var progress = {
        loaded: 0,
        total: 0,
        bitrate: 0
      };

      if (obj._progress) {
        $.extend(obj._progress, progress);
      } else {
        obj._progress = progress;
      }
    },
    _initResponseObject: function _initResponseObject(obj) {
      var prop;

      if (obj._response) {
        for (prop in obj._response) {
          if (Object.prototype.hasOwnProperty.call(obj._response, prop)) {
            delete obj._response[prop];
          }
        }
      } else {
        obj._response = {};
      }
    },
    _onProgress: function _onProgress(e, data) {
      if (e.lengthComputable) {
        var now = Date.now ? Date.now() : new Date().getTime(),
            loaded;

        if (data._time && data.progressInterval && now - data._time < data.progressInterval && e.loaded !== e.total) {
          return;
        }

        data._time = now;
        loaded = Math.floor(e.loaded / e.total * (data.chunkSize || data._progress.total)) + (data.uploadedBytes || 0); // Add the difference from the previously loaded state
        // to the global loaded counter:

        this._progress.loaded += loaded - data._progress.loaded;
        this._progress.bitrate = this._bitrateTimer.getBitrate(now, this._progress.loaded, data.bitrateInterval);
        data._progress.loaded = data.loaded = loaded;
        data._progress.bitrate = data.bitrate = data._bitrateTimer.getBitrate(now, loaded, data.bitrateInterval); // Trigger a custom progress event with a total data property set
        // to the file size(s) of the current upload and a loaded data
        // property calculated accordingly:

        this._trigger('progress', $.Event('progress', {
          delegatedEvent: e
        }), data); // Trigger a global progress event for all current file uploads,
        // including ajax calls queued for sequential file uploads:


        this._trigger('progressall', $.Event('progressall', {
          delegatedEvent: e
        }), this._progress);
      }
    },
    _initProgressListener: function _initProgressListener(options) {
      var that = this,
          xhr = options.xhr ? options.xhr() : $.ajaxSettings.xhr(); // Accesss to the native XHR object is required to add event listeners
      // for the upload progress event:

      if (xhr.upload) {
        $(xhr.upload).on('progress', function (e) {
          var oe = e.originalEvent; // Make sure the progress event properties get copied over:

          e.lengthComputable = oe.lengthComputable;
          e.loaded = oe.loaded;
          e.total = oe.total;

          that._onProgress(e, options);
        });

        options.xhr = function () {
          return xhr;
        };
      }
    },
    _deinitProgressListener: function _deinitProgressListener(options) {
      var xhr = options.xhr ? options.xhr() : $.ajaxSettings.xhr();

      if (xhr.upload) {
        $(xhr.upload).off('progress');
      }
    },
    _isInstanceOf: function _isInstanceOf(type, obj) {
      // Cross-frame instanceof check
      return Object.prototype.toString.call(obj) === '[object ' + type + ']';
    },
    _getUniqueFilename: function _getUniqueFilename(name, map) {
      // eslint-disable-next-line no-param-reassign
      name = String(name);

      if (map[name]) {
        // eslint-disable-next-line no-param-reassign
        name = name.replace(/(?: \(([\d]+)\))?(\.[^.]+)?$/, function (_, p1, p2) {
          var index = p1 ? Number(p1) + 1 : 1;
          var ext = p2 || '';
          return ' (' + index + ')' + ext;
        });
        return this._getUniqueFilename(name, map);
      }

      map[name] = true;
      return name;
    },
    _initXHRData: function _initXHRData(options) {
      var that = this,
          formData,
          file = options.files[0],
          // Ignore non-multipart setting if not supported:
      multipart = options.multipart || !$.support.xhrFileUpload,
          paramName = $.type(options.paramName) === 'array' ? options.paramName[0] : options.paramName;
      options.headers = $.extend({}, options.headers);

      if (options.contentRange) {
        options.headers['Content-Range'] = options.contentRange;
      }

      if (!multipart || options.blob || !this._isInstanceOf('File', file)) {
        options.headers['Content-Disposition'] = 'attachment; filename="' + encodeURI(file.uploadName || file.name) + '"';
      }

      if (!multipart) {
        options.contentType = file.type || 'application/octet-stream';
        options.data = options.blob || file;
      } else if ($.support.xhrFormDataFileUpload) {
        if (options.postMessage) {
          // window.postMessage does not allow sending FormData
          // objects, so we just add the File/Blob objects to
          // the formData array and let the postMessage window
          // create the FormData object out of this array:
          formData = this._getFormData(options);

          if (options.blob) {
            formData.push({
              name: paramName,
              value: options.blob
            });
          } else {
            $.each(options.files, function (index, file) {
              formData.push({
                name: $.type(options.paramName) === 'array' && options.paramName[index] || paramName,
                value: file
              });
            });
          }
        } else {
          if (that._isInstanceOf('FormData', options.formData)) {
            formData = options.formData;
          } else {
            formData = new FormData();
            $.each(this._getFormData(options), function (index, field) {
              formData.append(field.name, field.value);
            });
          }

          if (options.blob) {
            formData.append(paramName, options.blob, file.uploadName || file.name);
          } else {
            $.each(options.files, function (index, file) {
              // This check allows the tests to run with
              // dummy objects:
              if (that._isInstanceOf('File', file) || that._isInstanceOf('Blob', file)) {
                var fileName = file.uploadName || file.name;

                if (options.uniqueFilenames) {
                  fileName = that._getUniqueFilename(fileName, options.uniqueFilenames);
                }

                formData.append($.type(options.paramName) === 'array' && options.paramName[index] || paramName, file, fileName);
              }
            });
          }
        }

        options.data = formData;
      } // Blob reference is not needed anymore, free memory:


      options.blob = null;
    },
    _initIframeSettings: function _initIframeSettings(options) {
      var targetHost = $('<a></a>').prop('href', options.url).prop('host'); // Setting the dataType to iframe enables the iframe transport:

      options.dataType = 'iframe ' + (options.dataType || ''); // The iframe transport accepts a serialized array as form data:

      options.formData = this._getFormData(options); // Add redirect url to form data on cross-domain uploads:

      if (options.redirect && targetHost && targetHost !== location.host) {
        options.formData.push({
          name: options.redirectParamName || 'redirect',
          value: options.redirect
        });
      }
    },
    _initDataSettings: function _initDataSettings(options) {
      if (this._isXHRUpload(options)) {
        if (!this._chunkedUpload(options, true)) {
          if (!options.data) {
            this._initXHRData(options);
          }

          this._initProgressListener(options);
        }

        if (options.postMessage) {
          // Setting the dataType to postmessage enables the
          // postMessage transport:
          options.dataType = 'postmessage ' + (options.dataType || '');
        }
      } else {
        this._initIframeSettings(options);
      }
    },
    _getParamName: function _getParamName(options) {
      var fileInput = $(options.fileInput),
          paramName = options.paramName;

      if (!paramName) {
        paramName = [];
        fileInput.each(function () {
          var input = $(this),
              name = input.prop('name') || 'files[]',
              i = (input.prop('files') || [1]).length;

          while (i) {
            paramName.push(name);
            i -= 1;
          }
        });

        if (!paramName.length) {
          paramName = [fileInput.prop('name') || 'files[]'];
        }
      } else if (!$.isArray(paramName)) {
        paramName = [paramName];
      }

      return paramName;
    },
    _initFormSettings: function _initFormSettings(options) {
      // Retrieve missing options from the input field and the
      // associated form, if available:
      if (!options.form || !options.form.length) {
        options.form = $(options.fileInput.prop('form')); // If the given file input doesn't have an associated form,
        // use the default widget file input's form:

        if (!options.form.length) {
          options.form = $(this.options.fileInput.prop('form'));
        }
      }

      options.paramName = this._getParamName(options);

      if (!options.url) {
        options.url = options.form.prop('action') || location.href;
      } // The HTTP request method must be "POST" or "PUT":


      options.type = (options.type || $.type(options.form.prop('method')) === 'string' && options.form.prop('method') || '').toUpperCase();

      if (options.type !== 'POST' && options.type !== 'PUT' && options.type !== 'PATCH') {
        options.type = 'POST';
      }

      if (!options.formAcceptCharset) {
        options.formAcceptCharset = options.form.attr('accept-charset');
      }
    },
    _getAJAXSettings: function _getAJAXSettings(data) {
      var options = $.extend({}, this.options, data);

      this._initFormSettings(options);

      this._initDataSettings(options);

      return options;
    },
    // jQuery 1.6 doesn't provide .state(),
    // while jQuery 1.8+ removed .isRejected() and .isResolved():
    _getDeferredState: function _getDeferredState(deferred) {
      if (deferred.state) {
        return deferred.state();
      }

      if (deferred.isResolved()) {
        return 'resolved';
      }

      if (deferred.isRejected()) {
        return 'rejected';
      }

      return 'pending';
    },
    // Maps jqXHR callbacks to the equivalent
    // methods of the given Promise object:
    _enhancePromise: function _enhancePromise(promise) {
      promise.success = promise.done;
      promise.error = promise.fail;
      promise.complete = promise.always;
      return promise;
    },
    // Creates and returns a Promise object enhanced with
    // the jqXHR methods abort, success, error and complete:
    _getXHRPromise: function _getXHRPromise(resolveOrReject, context, args) {
      var dfd = $.Deferred(),
          promise = dfd.promise(); // eslint-disable-next-line no-param-reassign

      context = context || this.options.context || promise;

      if (resolveOrReject === true) {
        dfd.resolveWith(context, args);
      } else if (resolveOrReject === false) {
        dfd.rejectWith(context, args);
      }

      promise.abort = dfd.promise;
      return this._enhancePromise(promise);
    },
    // Adds convenience methods to the data callback argument:
    _addConvenienceMethods: function _addConvenienceMethods(e, data) {
      var that = this,
          getPromise = function getPromise(args) {
        return $.Deferred().resolveWith(that, args).promise();
      };

      data.process = function (resolveFunc, rejectFunc) {
        if (resolveFunc || rejectFunc) {
          data._processQueue = this._processQueue = (this._processQueue || getPromise([this]))[that._promisePipe](function () {
            if (data.errorThrown) {
              return $.Deferred().rejectWith(that, [data]).promise();
            }

            return getPromise(arguments);
          })[that._promisePipe](resolveFunc, rejectFunc);
        }

        return this._processQueue || getPromise([this]);
      };

      data.submit = function () {
        if (this.state() !== 'pending') {
          data.jqXHR = this.jqXHR = that._trigger('submit', $.Event('submit', {
            delegatedEvent: e
          }), this) !== false && that._onSend(e, this);
        }

        return this.jqXHR || that._getXHRPromise();
      };

      data.abort = function () {
        if (this.jqXHR) {
          return this.jqXHR.abort();
        }

        this.errorThrown = 'abort';

        that._trigger('fail', null, this);

        return that._getXHRPromise(false);
      };

      data.state = function () {
        if (this.jqXHR) {
          return that._getDeferredState(this.jqXHR);
        }

        if (this._processQueue) {
          return that._getDeferredState(this._processQueue);
        }
      };

      data.processing = function () {
        return !this.jqXHR && this._processQueue && that._getDeferredState(this._processQueue) === 'pending';
      };

      data.progress = function () {
        return this._progress;
      };

      data.response = function () {
        return this._response;
      };
    },
    // Parses the Range header from the server response
    // and returns the uploaded bytes:
    _getUploadedBytes: function _getUploadedBytes(jqXHR) {
      var range = jqXHR.getResponseHeader('Range'),
          parts = range && range.split('-'),
          upperBytesPos = parts && parts.length > 1 && parseInt(parts[1], 10);
      return upperBytesPos && upperBytesPos + 1;
    },
    // Uploads a file in multiple, sequential requests
    // by splitting the file up in multiple blob chunks.
    // If the second parameter is true, only tests if the file
    // should be uploaded in chunks, but does not invoke any
    // upload requests:
    _chunkedUpload: function _chunkedUpload(options, testOnly) {
      options.uploadedBytes = options.uploadedBytes || 0;

      var that = this,
          file = options.files[0],
          fs = file.size,
          ub = options.uploadedBytes,
          mcs = options.maxChunkSize || fs,
          slice = this._blobSlice,
          dfd = $.Deferred(),
          promise = dfd.promise(),
          jqXHR,
          _upload;

      if (!(this._isXHRUpload(options) && slice && (ub || ($.type(mcs) === 'function' ? mcs(options) : mcs) < fs)) || options.data) {
        return false;
      }

      if (testOnly) {
        return true;
      }

      if (ub >= fs) {
        file.error = options.i18n('uploadedBytes');
        return this._getXHRPromise(false, options.context, [null, 'error', file.error]);
      } // The chunk upload method:


      _upload = function upload() {
        // Clone the options object for each chunk upload:
        var o = $.extend({}, options),
            currentLoaded = o._progress.loaded;
        o.blob = slice.call(file, ub, ub + ($.type(mcs) === 'function' ? mcs(o) : mcs), file.type); // Store the current chunk size, as the blob itself
        // will be dereferenced after data processing:

        o.chunkSize = o.blob.size; // Expose the chunk bytes position range:

        o.contentRange = 'bytes ' + ub + '-' + (ub + o.chunkSize - 1) + '/' + fs; // Trigger chunkbeforesend to allow form data to be updated for this chunk

        that._trigger('chunkbeforesend', null, o); // Process the upload data (the blob and potential form data):


        that._initXHRData(o); // Add progress listeners for this chunk upload:


        that._initProgressListener(o);

        jqXHR = (that._trigger('chunksend', null, o) !== false && $.ajax(o) || that._getXHRPromise(false, o.context)).done(function (result, textStatus, jqXHR) {
          ub = that._getUploadedBytes(jqXHR) || ub + o.chunkSize; // Create a progress event if no final progress event
          // with loaded equaling total has been triggered
          // for this chunk:

          if (currentLoaded + o.chunkSize - o._progress.loaded) {
            that._onProgress($.Event('progress', {
              lengthComputable: true,
              loaded: ub - o.uploadedBytes,
              total: ub - o.uploadedBytes
            }), o);
          }

          options.uploadedBytes = o.uploadedBytes = ub;
          o.result = result;
          o.textStatus = textStatus;
          o.jqXHR = jqXHR;

          that._trigger('chunkdone', null, o);

          that._trigger('chunkalways', null, o);

          if (ub < fs) {
            // File upload not yet complete,
            // continue with the next chunk:
            _upload();
          } else {
            dfd.resolveWith(o.context, [result, textStatus, jqXHR]);
          }
        }).fail(function (jqXHR, textStatus, errorThrown) {
          o.jqXHR = jqXHR;
          o.textStatus = textStatus;
          o.errorThrown = errorThrown;

          that._trigger('chunkfail', null, o);

          that._trigger('chunkalways', null, o);

          dfd.rejectWith(o.context, [jqXHR, textStatus, errorThrown]);
        }).always(function () {
          that._deinitProgressListener(o);
        });
      };

      this._enhancePromise(promise);

      promise.abort = function () {
        return jqXHR.abort();
      };

      _upload();

      return promise;
    },
    _beforeSend: function _beforeSend(e, data) {
      if (this._active === 0) {
        // the start callback is triggered when an upload starts
        // and no other uploads are currently running,
        // equivalent to the global ajaxStart event:
        this._trigger('start'); // Set timer for global bitrate progress calculation:


        this._bitrateTimer = new this._BitrateTimer(); // Reset the global progress values:

        this._progress.loaded = this._progress.total = 0;
        this._progress.bitrate = 0;
      } // Make sure the container objects for the .response() and
      // .progress() methods on the data object are available
      // and reset to their initial state:


      this._initResponseObject(data);

      this._initProgressObject(data);

      data._progress.loaded = data.loaded = data.uploadedBytes || 0;
      data._progress.total = data.total = this._getTotal(data.files) || 1;
      data._progress.bitrate = data.bitrate = 0;
      this._active += 1; // Initialize the global progress values:

      this._progress.loaded += data.loaded;
      this._progress.total += data.total;
    },
    _onDone: function _onDone(result, textStatus, jqXHR, options) {
      var total = options._progress.total,
          response = options._response;

      if (options._progress.loaded < total) {
        // Create a progress event if no final progress event
        // with loaded equaling total has been triggered:
        this._onProgress($.Event('progress', {
          lengthComputable: true,
          loaded: total,
          total: total
        }), options);
      }

      response.result = options.result = result;
      response.textStatus = options.textStatus = textStatus;
      response.jqXHR = options.jqXHR = jqXHR;

      this._trigger('done', null, options);
    },
    _onFail: function _onFail(jqXHR, textStatus, errorThrown, options) {
      var response = options._response;

      if (options.recalculateProgress) {
        // Remove the failed (error or abort) file upload from
        // the global progress calculation:
        this._progress.loaded -= options._progress.loaded;
        this._progress.total -= options._progress.total;
      }

      response.jqXHR = options.jqXHR = jqXHR;
      response.textStatus = options.textStatus = textStatus;
      response.errorThrown = options.errorThrown = errorThrown;

      this._trigger('fail', null, options);
    },
    _onAlways: function _onAlways(jqXHRorResult, textStatus, jqXHRorError, options) {
      // jqXHRorResult, textStatus and jqXHRorError are added to the
      // options object via done and fail callbacks
      this._trigger('always', null, options);
    },
    _onSend: function _onSend(e, data) {
      if (!data.submit) {
        this._addConvenienceMethods(e, data);
      }

      var that = this,
          jqXHR,
          aborted,
          slot,
          pipe,
          options = that._getAJAXSettings(data),
          send = function send() {
        that._sending += 1; // Set timer for bitrate progress calculation:

        options._bitrateTimer = new that._BitrateTimer();
        jqXHR = jqXHR || ((aborted || that._trigger('send', $.Event('send', {
          delegatedEvent: e
        }), options) === false) && that._getXHRPromise(false, options.context, aborted) || that._chunkedUpload(options) || $.ajax(options)).done(function (result, textStatus, jqXHR) {
          that._onDone(result, textStatus, jqXHR, options);
        }).fail(function (jqXHR, textStatus, errorThrown) {
          that._onFail(jqXHR, textStatus, errorThrown, options);
        }).always(function (jqXHRorResult, textStatus, jqXHRorError) {
          that._deinitProgressListener(options);

          that._onAlways(jqXHRorResult, textStatus, jqXHRorError, options);

          that._sending -= 1;
          that._active -= 1;

          if (options.limitConcurrentUploads && options.limitConcurrentUploads > that._sending) {
            // Start the next queued upload,
            // that has not been aborted:
            var nextSlot = that._slots.shift();

            while (nextSlot) {
              if (that._getDeferredState(nextSlot) === 'pending') {
                nextSlot.resolve();
                break;
              }

              nextSlot = that._slots.shift();
            }
          }

          if (that._active === 0) {
            // The stop callback is triggered when all uploads have
            // been completed, equivalent to the global ajaxStop event:
            that._trigger('stop');
          }
        });
        return jqXHR;
      };

      this._beforeSend(e, options);

      if (this.options.sequentialUploads || this.options.limitConcurrentUploads && this.options.limitConcurrentUploads <= this._sending) {
        if (this.options.limitConcurrentUploads > 1) {
          slot = $.Deferred();

          this._slots.push(slot);

          pipe = slot[that._promisePipe](send);
        } else {
          this._sequence = this._sequence[that._promisePipe](send, send);
          pipe = this._sequence;
        } // Return the piped Promise object, enhanced with an abort method,
        // which is delegated to the jqXHR object of the current upload,
        // and jqXHR callbacks mapped to the equivalent Promise methods:


        pipe.abort = function () {
          aborted = [undefined, 'abort', 'abort'];

          if (!jqXHR) {
            if (slot) {
              slot.rejectWith(options.context, aborted);
            }

            return send();
          }

          return jqXHR.abort();
        };

        return this._enhancePromise(pipe);
      }

      return send();
    },
    _onAdd: function _onAdd(e, data) {
      var that = this,
          result = true,
          options = $.extend({}, this.options, data),
          files = data.files,
          filesLength = files.length,
          limit = options.limitMultiFileUploads,
          limitSize = options.limitMultiFileUploadSize,
          overhead = options.limitMultiFileUploadSizeOverhead,
          batchSize = 0,
          paramName = this._getParamName(options),
          paramNameSet,
          paramNameSlice,
          fileSet,
          i,
          j = 0;

      if (!filesLength) {
        return false;
      }

      if (limitSize && files[0].size === undefined) {
        limitSize = undefined;
      }

      if (!(options.singleFileUploads || limit || limitSize) || !this._isXHRUpload(options)) {
        fileSet = [files];
        paramNameSet = [paramName];
      } else if (!(options.singleFileUploads || limitSize) && limit) {
        fileSet = [];
        paramNameSet = [];

        for (i = 0; i < filesLength; i += limit) {
          fileSet.push(files.slice(i, i + limit));
          paramNameSlice = paramName.slice(i, i + limit);

          if (!paramNameSlice.length) {
            paramNameSlice = paramName;
          }

          paramNameSet.push(paramNameSlice);
        }
      } else if (!options.singleFileUploads && limitSize) {
        fileSet = [];
        paramNameSet = [];

        for (i = 0; i < filesLength; i = i + 1) {
          batchSize += files[i].size + overhead;

          if (i + 1 === filesLength || batchSize + files[i + 1].size + overhead > limitSize || limit && i + 1 - j >= limit) {
            fileSet.push(files.slice(j, i + 1));
            paramNameSlice = paramName.slice(j, i + 1);

            if (!paramNameSlice.length) {
              paramNameSlice = paramName;
            }

            paramNameSet.push(paramNameSlice);
            j = i + 1;
            batchSize = 0;
          }
        }
      } else {
        paramNameSet = paramName;
      }

      data.originalFiles = files;
      $.each(fileSet || files, function (index, element) {
        var newData = $.extend({}, data);
        newData.files = fileSet ? element : [element];
        newData.paramName = paramNameSet[index];

        that._initResponseObject(newData);

        that._initProgressObject(newData);

        that._addConvenienceMethods(e, newData);

        result = that._trigger('add', $.Event('add', {
          delegatedEvent: e
        }), newData);
        return result;
      });
      return result;
    },
    _replaceFileInput: function _replaceFileInput(data) {
      var input = data.fileInput,
          inputClone = input.clone(true),
          restoreFocus = input.is(document.activeElement); // Add a reference for the new cloned file input to the data argument:

      data.fileInputClone = inputClone;
      $('<form></form>').append(inputClone)[0].reset(); // Detaching allows to insert the fileInput on another form
      // without loosing the file input value:

      input.after(inputClone).detach(); // If the fileInput had focus before it was detached,
      // restore focus to the inputClone.

      if (restoreFocus) {
        inputClone.trigger('focus');
      } // Avoid memory leaks with the detached file input:


      $.cleanData(input.off('remove')); // Replace the original file input element in the fileInput
      // elements set with the clone, which has been copied including
      // event handlers:

      this.options.fileInput = this.options.fileInput.map(function (i, el) {
        if (el === input[0]) {
          return inputClone[0];
        }

        return el;
      }); // If the widget has been initialized on the file input itself,
      // override this.element with the file input clone:

      if (input[0] === this.element[0]) {
        this.element = inputClone;
      }
    },
    _handleFileTreeEntry: function _handleFileTreeEntry(entry, path) {
      var that = this,
          dfd = $.Deferred(),
          entries = [],
          dirReader,
          errorHandler = function errorHandler(e) {
        if (e && !e.entry) {
          e.entry = entry;
        } // Since $.when returns immediately if one
        // Deferred is rejected, we use resolve instead.
        // This allows valid files and invalid items
        // to be returned together in one set:


        dfd.resolve([e]);
      },
          successHandler = function successHandler(entries) {
        that._handleFileTreeEntries(entries, path + entry.name + '/').done(function (files) {
          dfd.resolve(files);
        }).fail(errorHandler);
      },
          readEntries = function readEntries() {
        dirReader.readEntries(function (results) {
          if (!results.length) {
            successHandler(entries);
          } else {
            entries = entries.concat(results);
            readEntries();
          }
        }, errorHandler);
      }; // eslint-disable-next-line no-param-reassign


      path = path || '';

      if (entry.isFile) {
        if (entry._file) {
          // Workaround for Chrome bug #149735
          entry._file.relativePath = path;
          dfd.resolve(entry._file);
        } else {
          entry.file(function (file) {
            file.relativePath = path;
            dfd.resolve(file);
          }, errorHandler);
        }
      } else if (entry.isDirectory) {
        dirReader = entry.createReader();
        readEntries();
      } else {
        // Return an empty list for file system items
        // other than files or directories:
        dfd.resolve([]);
      }

      return dfd.promise();
    },
    _handleFileTreeEntries: function _handleFileTreeEntries(entries, path) {
      var that = this;
      return $.when.apply($, $.map(entries, function (entry) {
        return that._handleFileTreeEntry(entry, path);
      }))[this._promisePipe](function () {
        return Array.prototype.concat.apply([], arguments);
      });
    },
    _getDroppedFiles: function _getDroppedFiles(dataTransfer) {
      // eslint-disable-next-line no-param-reassign
      dataTransfer = dataTransfer || {};
      var items = dataTransfer.items;

      if (items && items.length && (items[0].webkitGetAsEntry || items[0].getAsEntry)) {
        return this._handleFileTreeEntries($.map(items, function (item) {
          var entry;

          if (item.webkitGetAsEntry) {
            entry = item.webkitGetAsEntry();

            if (entry) {
              // Workaround for Chrome bug #149735:
              entry._file = item.getAsFile();
            }

            return entry;
          }

          return item.getAsEntry();
        }));
      }

      return $.Deferred().resolve($.makeArray(dataTransfer.files)).promise();
    },
    _getSingleFileInputFiles: function _getSingleFileInputFiles(fileInput) {
      // eslint-disable-next-line no-param-reassign
      fileInput = $(fileInput);
      var entries = fileInput.prop('webkitEntries') || fileInput.prop('entries'),
          files,
          value;

      if (entries && entries.length) {
        return this._handleFileTreeEntries(entries);
      }

      files = $.makeArray(fileInput.prop('files'));

      if (!files.length) {
        value = fileInput.prop('value');

        if (!value) {
          return $.Deferred().resolve([]).promise();
        } // If the files property is not available, the browser does not
        // support the File API and we add a pseudo File object with
        // the input value as name with path information removed:


        files = [{
          name: value.replace(/^.*\\/, '')
        }];
      } else if (files[0].name === undefined && files[0].fileName) {
        // File normalization for Safari 4 and Firefox 3:
        $.each(files, function (index, file) {
          file.name = file.fileName;
          file.size = file.fileSize;
        });
      }

      return $.Deferred().resolve(files).promise();
    },
    _getFileInputFiles: function _getFileInputFiles(fileInput) {
      if (!(fileInput instanceof $) || fileInput.length === 1) {
        return this._getSingleFileInputFiles(fileInput);
      }

      return $.when.apply($, $.map(fileInput, this._getSingleFileInputFiles))[this._promisePipe](function () {
        return Array.prototype.concat.apply([], arguments);
      });
    },
    _onChange: function _onChange(e) {
      var that = this,
          data = {
        fileInput: $(e.target),
        form: $(e.target.form)
      };

      this._getFileInputFiles(data.fileInput).always(function (files) {
        data.files = files;

        if (that.options.replaceFileInput) {
          that._replaceFileInput(data);
        }

        if (that._trigger('change', $.Event('change', {
          delegatedEvent: e
        }), data) !== false) {
          that._onAdd(e, data);
        }
      });
    },
    _onPaste: function _onPaste(e) {
      var items = e.originalEvent && e.originalEvent.clipboardData && e.originalEvent.clipboardData.items,
          data = {
        files: []
      };

      if (items && items.length) {
        $.each(items, function (index, item) {
          var file = item.getAsFile && item.getAsFile();

          if (file) {
            data.files.push(file);
          }
        });

        if (this._trigger('paste', $.Event('paste', {
          delegatedEvent: e
        }), data) !== false) {
          this._onAdd(e, data);
        }
      }
    },
    _onDrop: function _onDrop(e) {
      e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
      var that = this,
          dataTransfer = e.dataTransfer,
          data = {};

      if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
        e.preventDefault();

        this._getDroppedFiles(dataTransfer).always(function (files) {
          data.files = files;

          if (that._trigger('drop', $.Event('drop', {
            delegatedEvent: e
          }), data) !== false) {
            that._onAdd(e, data);
          }
        });
      }
    },
    _onDragOver: getDragHandler('dragover'),
    _onDragEnter: getDragHandler('dragenter'),
    _onDragLeave: getDragHandler('dragleave'),
    _initEventHandlers: function _initEventHandlers() {
      if (this._isXHRUpload(this.options)) {
        this._on(this.options.dropZone, {
          dragover: this._onDragOver,
          drop: this._onDrop,
          // event.preventDefault() on dragenter is required for IE10+:
          dragenter: this._onDragEnter,
          // dragleave is not required, but added for completeness:
          dragleave: this._onDragLeave
        });

        this._on(this.options.pasteZone, {
          paste: this._onPaste
        });
      }

      if ($.support.fileInput) {
        this._on(this.options.fileInput, {
          change: this._onChange
        });
      }
    },
    _destroyEventHandlers: function _destroyEventHandlers() {
      this._off(this.options.dropZone, 'dragenter dragleave dragover drop');

      this._off(this.options.pasteZone, 'paste');

      this._off(this.options.fileInput, 'change');
    },
    _destroy: function _destroy() {
      this._destroyEventHandlers();
    },
    _setOption: function _setOption(key, value) {
      var reinit = $.inArray(key, this._specialOptions) !== -1;

      if (reinit) {
        this._destroyEventHandlers();
      }

      this._super(key, value);

      if (reinit) {
        this._initSpecialOptions();

        this._initEventHandlers();
      }
    },
    _initSpecialOptions: function _initSpecialOptions() {
      var options = this.options;

      if (options.fileInput === undefined) {
        options.fileInput = this.element.is('input[type="file"]') ? this.element : this.element.find('input[type="file"]');
      } else if (!(options.fileInput instanceof $)) {
        options.fileInput = $(options.fileInput);
      }

      if (!(options.dropZone instanceof $)) {
        options.dropZone = $(options.dropZone);
      }

      if (!(options.pasteZone instanceof $)) {
        options.pasteZone = $(options.pasteZone);
      }
    },
    _getRegExp: function _getRegExp(str) {
      var parts = str.split('/'),
          modifiers = parts.pop();
      parts.shift();
      return new RegExp(parts.join('/'), modifiers);
    },
    _isRegExpOption: function _isRegExpOption(key, value) {
      return key !== 'url' && $.type(value) === 'string' && /^\/.*\/[igm]{0,3}$/.test(value);
    },
    _initDataAttributes: function _initDataAttributes() {
      var that = this,
          options = this.options,
          data = this.element.data(); // Initialize options set via HTML5 data-attributes:

      $.each(this.element[0].attributes, function (index, attr) {
        var key = attr.name.toLowerCase(),
            value;

        if (/^data-/.test(key)) {
          // Convert hyphen-ated key to camelCase:
          key = key.slice(5).replace(/-[a-z]/g, function (str) {
            return str.charAt(1).toUpperCase();
          });
          value = data[key];

          if (that._isRegExpOption(key, value)) {
            value = that._getRegExp(value);
          }

          options[key] = value;
        }
      });
    },
    _create: function _create() {
      this._initDataAttributes();

      this._initSpecialOptions();

      this._slots = [];
      this._sequence = this._getXHRPromise(true);
      this._sending = this._active = 0;

      this._initProgressObject(this);

      this._initEventHandlers();
    },
    // This method is exposed to the widget API and allows to query
    // the number of active uploads:
    active: function active() {
      return this._active;
    },
    // This method is exposed to the widget API and allows to query
    // the widget upload progress.
    // It returns an object with loaded, total and bitrate properties
    // for the running uploads:
    progress: function progress() {
      return this._progress;
    },
    // This method is exposed to the widget API and allows adding files
    // using the fileupload API. The data parameter accepts an object which
    // must have a files property and can contain additional options:
    // .fileupload('add', {files: filesList});
    add: function add(data) {
      var that = this;

      if (!data || this.options.disabled) {
        return;
      }

      if (data.fileInput && !data.files) {
        this._getFileInputFiles(data.fileInput).always(function (files) {
          data.files = files;

          that._onAdd(null, data);
        });
      } else {
        data.files = $.makeArray(data.files);

        this._onAdd(null, data);
      }
    },
    // This method is exposed to the widget API and allows sending files
    // using the fileupload API. The data parameter accepts an object which
    // must have a files or fileInput property and can contain additional options:
    // .fileupload('send', {files: filesList});
    // The method returns a Promise object for the file upload call.
    send: function send(data) {
      if (data && !this.options.disabled) {
        if (data.fileInput && !data.files) {
          var that = this,
              dfd = $.Deferred(),
              promise = dfd.promise(),
              jqXHR,
              aborted;

          promise.abort = function () {
            aborted = true;

            if (jqXHR) {
              return jqXHR.abort();
            }

            dfd.reject(null, 'abort', 'abort');
            return promise;
          };

          this._getFileInputFiles(data.fileInput).always(function (files) {
            if (aborted) {
              return;
            }

            if (!files.length) {
              dfd.reject();
              return;
            }

            data.files = files;
            jqXHR = that._onSend(null, data);
            jqXHR.then(function (result, textStatus, jqXHR) {
              dfd.resolve(result, textStatus, jqXHR);
            }, function (jqXHR, textStatus, errorThrown) {
              dfd.reject(jqXHR, textStatus, errorThrown);
            });
          });

          return this._enhancePromise(promise);
        }

        data.files = $.makeArray(data.files);

        if (data.files.length) {
          return this._onSend(null, data);
        }
      }

      return this._getXHRPromise(false, data && data.context);
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/data.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/data.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI :data 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: :data Selector
//>>group: Core
//>>description: Selects elements which have data stored under the specified key.
//>>docs: http://api.jqueryui.com/data-selector/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.extend($.expr[":"], {
    data: $.expr.createPseudo ? $.expr.createPseudo(function (dataName) {
      return function (elem) {
        return !!$.data(elem, dataName);
      };
    }) : // Support: jQuery <1.8
    function (elem, i, match) {
      return !!$.data(elem, match[3]);
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/disable-selection.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/disable-selection.js ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Disable Selection 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: disableSelection
//>>group: Core
//>>description: Disable selection of text content within the set of matched elements.
//>>docs: http://api.jqueryui.com/disableSelection/
// This file is deprecated
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.fn.extend({
    disableSelection: function () {
      var eventType = "onselectstart" in document.createElement("div") ? "selectstart" : "mousedown";
      return function () {
        return this.on(eventType + ".ui-disableSelection", function (event) {
          event.preventDefault();
        });
      };
    }(),
    enableSelection: function enableSelection() {
      return this.off(".ui-disableSelection");
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/escape-selector.js":
/*!****************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/escape-selector.js ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  // Internal use only
  return $.ui.escapeSelector = function () {
    var selectorEscape = /([!"#$%&'()*+,./:;<=>?@[\]^`{|}~])/g;
    return function (selector) {
      return selector.replace(selectorEscape, "\\$1");
    };
  }();
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Focusable 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: :focusable Selector
//>>group: Core
//>>description: Selects elements which can be focused.
//>>docs: http://api.jqueryui.com/focusable-selector/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  // Selectors
  $.ui.focusable = function (element, hasTabindex) {
    var map,
        mapName,
        img,
        focusableIfVisible,
        fieldset,
        nodeName = element.nodeName.toLowerCase();

    if ("area" === nodeName) {
      map = element.parentNode;
      mapName = map.name;

      if (!element.href || !mapName || map.nodeName.toLowerCase() !== "map") {
        return false;
      }

      img = $("img[usemap='#" + mapName + "']");
      return img.length > 0 && img.is(":visible");
    }

    if (/^(input|select|textarea|button|object)$/.test(nodeName)) {
      focusableIfVisible = !element.disabled;

      if (focusableIfVisible) {
        // Form controls within a disabled fieldset are disabled.
        // However, controls within the fieldset's legend do not get disabled.
        // Since controls generally aren't placed inside legends, we skip
        // this portion of the check.
        fieldset = $(element).closest("fieldset")[0];

        if (fieldset) {
          focusableIfVisible = !fieldset.disabled;
        }
      }
    } else if ("a" === nodeName) {
      focusableIfVisible = element.href || hasTabindex;
    } else {
      focusableIfVisible = hasTabindex;
    }

    return focusableIfVisible && $(element).is(":visible") && visible($(element));
  }; // Support: IE 8 only
  // IE 8 doesn't resolve inherit to visible/hidden for computed values


  function visible(element) {
    var visibility = element.css("visibility");

    while (visibility === "inherit") {
      element = element.parent();
      visibility = element.css("visibility");
    }

    return visibility !== "hidden";
  }

  $.extend($.expr[":"], {
    focusable: function focusable(element) {
      return $.ui.focusable(element, $.attr(element, "tabindex") != null);
    }
  });
  return $.ui.focusable;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form-reset-mixin.js":
/*!*****************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form-reset-mixin.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Form Reset Mixin 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Form Reset Mixin
//>>group: Core
//>>description: Refresh input widgets when their form is reset
//>>docs: http://api.jqueryui.com/form-reset-mixin/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./form */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form.js"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.ui.formResetMixin = {
    _formResetHandler: function _formResetHandler() {
      var form = $(this); // Wait for the form reset to actually happen before refreshing

      setTimeout(function () {
        var instances = form.data("ui-form-reset-instances");
        $.each(instances, function () {
          this.refresh();
        });
      });
    },
    _bindFormResetHandler: function _bindFormResetHandler() {
      this.form = this.element.form();

      if (!this.form.length) {
        return;
      }

      var instances = this.form.data("ui-form-reset-instances") || [];

      if (!instances.length) {
        // We don't use _on() here because we use a single event handler per form
        this.form.on("reset.ui-form-reset", this._formResetHandler);
      }

      instances.push(this);
      this.form.data("ui-form-reset-instances", instances);
    },
    _unbindFormResetHandler: function _unbindFormResetHandler() {
      if (!this.form.length) {
        return;
      }

      var instances = this.form.data("ui-form-reset-instances");
      instances.splice($.inArray(this, instances), 1);

      if (instances.length) {
        this.form.data("ui-form-reset-instances", instances);
      } else {
        this.form.removeData("ui-form-reset-instances").off("reset.ui-form-reset");
      }
    }
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  // Support: IE8 Only
  // IE8 does not support the form attribute and when it is supplied. It overwrites the form prop
  // with a string, so we need to find the proper form.
  return $.fn.form = function () {
    return typeof this[0].form === "string" ? this.closest("form") : $(this[0].form);
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/ie.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/ie.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  // This file is deprecated
  return $.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Keycode 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Keycode
//>>group: Core
//>>description: Provide keycodes as keynames
//>>docs: http://api.jqueryui.com/jQuery.ui.keyCode/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.ui.keyCode = {
    BACKSPACE: 8,
    COMMA: 188,
    DELETE: 46,
    DOWN: 40,
    END: 35,
    ENTER: 13,
    ESCAPE: 27,
    HOME: 36,
    LEFT: 37,
    PAGE_DOWN: 34,
    PAGE_UP: 33,
    PERIOD: 190,
    RIGHT: 39,
    SPACE: 32,
    TAB: 9,
    UP: 38
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/labels.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/labels.js ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Labels 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: labels
//>>group: Core
//>>description: Find all the labels associated with a given input
//>>docs: http://api.jqueryui.com/labels/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ./escape-selector */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/escape-selector.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.fn.labels = function () {
    var ancestor, selector, id, labels, ancestors; // Check control.labels first

    if (this[0].labels && this[0].labels.length) {
      return this.pushStack(this[0].labels);
    } // Support: IE <= 11, FF <= 37, Android <= 2.3 only
    // Above browsers do not support control.labels. Everything below is to support them
    // as well as document fragments. control.labels does not work on document fragments


    labels = this.eq(0).parents("label"); // Look for the label based on the id

    id = this.attr("id");

    if (id) {
      // We don't search against the document in case the element
      // is disconnected from the DOM
      ancestor = this.eq(0).parents().last(); // Get a full set of top level ancestors

      ancestors = ancestor.add(ancestor.length ? ancestor.siblings() : this.siblings()); // Create a selector for the label based on the id

      selector = "label[for='" + $.ui.escapeSelector(id) + "']";
      labels = labels.add(ancestors.find(selector).addBack(selector));
    } // Return whatever we have found for labels


    return this.pushStack(labels);
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  // $.ui.plugin is deprecated. Use $.widget() extensions instead.
  return $.ui.plugin = {
    add: function add(module, option, set) {
      var i,
          proto = $.ui[module].prototype;

      for (i in set) {
        proto.plugins[i] = proto.plugins[i] || [];
        proto.plugins[i].push([option, set[i]]);
      }
    },
    call: function call(instance, name, args, allowDisconnected) {
      var i,
          set = instance.plugins[name];

      if (!set) {
        return;
      }

      if (!allowDisconnected && (!instance.element[0].parentNode || instance.element[0].parentNode.nodeType === 11)) {
        return;
      }

      for (i = 0; i < set.length; i++) {
        if (instance.options[set[i][0]]) {
          set[i][1].apply(instance.element, args);
        }
      }
    }
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/position.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/position.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Position 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/position/
 */
//>>label: Position
//>>group: Core
//>>description: Positions elements relative to other elements.
//>>docs: http://api.jqueryui.com/position/
//>>demos: http://jqueryui.com/position/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  (function () {
    var cachedScrollbarWidth,
        max = Math.max,
        abs = Math.abs,
        rhorizontal = /left|center|right/,
        rvertical = /top|center|bottom/,
        roffset = /[\+\-]\d+(\.[\d]+)?%?/,
        rposition = /^\w+/,
        rpercent = /%$/,
        _position = $.fn.position;

    function getOffsets(offsets, width, height) {
      return [parseFloat(offsets[0]) * (rpercent.test(offsets[0]) ? width / 100 : 1), parseFloat(offsets[1]) * (rpercent.test(offsets[1]) ? height / 100 : 1)];
    }

    function parseCss(element, property) {
      return parseInt($.css(element, property), 10) || 0;
    }

    function getDimensions(elem) {
      var raw = elem[0];

      if (raw.nodeType === 9) {
        return {
          width: elem.width(),
          height: elem.height(),
          offset: {
            top: 0,
            left: 0
          }
        };
      }

      if ($.isWindow(raw)) {
        return {
          width: elem.width(),
          height: elem.height(),
          offset: {
            top: elem.scrollTop(),
            left: elem.scrollLeft()
          }
        };
      }

      if (raw.preventDefault) {
        return {
          width: 0,
          height: 0,
          offset: {
            top: raw.pageY,
            left: raw.pageX
          }
        };
      }

      return {
        width: elem.outerWidth(),
        height: elem.outerHeight(),
        offset: elem.offset()
      };
    }

    $.position = {
      scrollbarWidth: function scrollbarWidth() {
        if (cachedScrollbarWidth !== undefined) {
          return cachedScrollbarWidth;
        }

        var w1,
            w2,
            div = $("<div " + "style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" + "<div style='height:100px;width:auto;'></div></div>"),
            innerDiv = div.children()[0];
        $("body").append(div);
        w1 = innerDiv.offsetWidth;
        div.css("overflow", "scroll");
        w2 = innerDiv.offsetWidth;

        if (w1 === w2) {
          w2 = div[0].clientWidth;
        }

        div.remove();
        return cachedScrollbarWidth = w1 - w2;
      },
      getScrollInfo: function getScrollInfo(within) {
        var overflowX = within.isWindow || within.isDocument ? "" : within.element.css("overflow-x"),
            overflowY = within.isWindow || within.isDocument ? "" : within.element.css("overflow-y"),
            hasOverflowX = overflowX === "scroll" || overflowX === "auto" && within.width < within.element[0].scrollWidth,
            hasOverflowY = overflowY === "scroll" || overflowY === "auto" && within.height < within.element[0].scrollHeight;
        return {
          width: hasOverflowY ? $.position.scrollbarWidth() : 0,
          height: hasOverflowX ? $.position.scrollbarWidth() : 0
        };
      },
      getWithinInfo: function getWithinInfo(element) {
        var withinElement = $(element || window),
            isWindow = $.isWindow(withinElement[0]),
            isDocument = !!withinElement[0] && withinElement[0].nodeType === 9,
            hasOffset = !isWindow && !isDocument;
        return {
          element: withinElement,
          isWindow: isWindow,
          isDocument: isDocument,
          offset: hasOffset ? $(element).offset() : {
            left: 0,
            top: 0
          },
          scrollLeft: withinElement.scrollLeft(),
          scrollTop: withinElement.scrollTop(),
          width: withinElement.outerWidth(),
          height: withinElement.outerHeight()
        };
      }
    };

    $.fn.position = function (options) {
      if (!options || !options.of) {
        return _position.apply(this, arguments);
      } // Make a copy, we don't want to modify arguments


      options = $.extend({}, options);
      var atOffset,
          targetWidth,
          targetHeight,
          targetOffset,
          basePosition,
          dimensions,
          target = $(options.of),
          within = $.position.getWithinInfo(options.within),
          scrollInfo = $.position.getScrollInfo(within),
          collision = (options.collision || "flip").split(" "),
          offsets = {};
      dimensions = getDimensions(target);

      if (target[0].preventDefault) {
        // Force left top to allow flipping
        options.at = "left top";
      }

      targetWidth = dimensions.width;
      targetHeight = dimensions.height;
      targetOffset = dimensions.offset; // Clone to reuse original targetOffset later

      basePosition = $.extend({}, targetOffset); // Force my and at to have valid horizontal and vertical positions
      // if a value is missing or invalid, it will be converted to center

      $.each(["my", "at"], function () {
        var pos = (options[this] || "").split(" "),
            horizontalOffset,
            verticalOffset;

        if (pos.length === 1) {
          pos = rhorizontal.test(pos[0]) ? pos.concat(["center"]) : rvertical.test(pos[0]) ? ["center"].concat(pos) : ["center", "center"];
        }

        pos[0] = rhorizontal.test(pos[0]) ? pos[0] : "center";
        pos[1] = rvertical.test(pos[1]) ? pos[1] : "center"; // Calculate offsets

        horizontalOffset = roffset.exec(pos[0]);
        verticalOffset = roffset.exec(pos[1]);
        offsets[this] = [horizontalOffset ? horizontalOffset[0] : 0, verticalOffset ? verticalOffset[0] : 0]; // Reduce to just the positions without the offsets

        options[this] = [rposition.exec(pos[0])[0], rposition.exec(pos[1])[0]];
      }); // Normalize collision option

      if (collision.length === 1) {
        collision[1] = collision[0];
      }

      if (options.at[0] === "right") {
        basePosition.left += targetWidth;
      } else if (options.at[0] === "center") {
        basePosition.left += targetWidth / 2;
      }

      if (options.at[1] === "bottom") {
        basePosition.top += targetHeight;
      } else if (options.at[1] === "center") {
        basePosition.top += targetHeight / 2;
      }

      atOffset = getOffsets(offsets.at, targetWidth, targetHeight);
      basePosition.left += atOffset[0];
      basePosition.top += atOffset[1];
      return this.each(function () {
        var collisionPosition,
            using,
            elem = $(this),
            elemWidth = elem.outerWidth(),
            elemHeight = elem.outerHeight(),
            marginLeft = parseCss(this, "marginLeft"),
            marginTop = parseCss(this, "marginTop"),
            collisionWidth = elemWidth + marginLeft + parseCss(this, "marginRight") + scrollInfo.width,
            collisionHeight = elemHeight + marginTop + parseCss(this, "marginBottom") + scrollInfo.height,
            position = $.extend({}, basePosition),
            myOffset = getOffsets(offsets.my, elem.outerWidth(), elem.outerHeight());

        if (options.my[0] === "right") {
          position.left -= elemWidth;
        } else if (options.my[0] === "center") {
          position.left -= elemWidth / 2;
        }

        if (options.my[1] === "bottom") {
          position.top -= elemHeight;
        } else if (options.my[1] === "center") {
          position.top -= elemHeight / 2;
        }

        position.left += myOffset[0];
        position.top += myOffset[1];
        collisionPosition = {
          marginLeft: marginLeft,
          marginTop: marginTop
        };
        $.each(["left", "top"], function (i, dir) {
          if ($.ui.position[collision[i]]) {
            $.ui.position[collision[i]][dir](position, {
              targetWidth: targetWidth,
              targetHeight: targetHeight,
              elemWidth: elemWidth,
              elemHeight: elemHeight,
              collisionPosition: collisionPosition,
              collisionWidth: collisionWidth,
              collisionHeight: collisionHeight,
              offset: [atOffset[0] + myOffset[0], atOffset[1] + myOffset[1]],
              my: options.my,
              at: options.at,
              within: within,
              elem: elem
            });
          }
        });

        if (options.using) {
          // Adds feedback as second argument to using callback, if present
          using = function using(props) {
            var left = targetOffset.left - position.left,
                right = left + targetWidth - elemWidth,
                top = targetOffset.top - position.top,
                bottom = top + targetHeight - elemHeight,
                feedback = {
              target: {
                element: target,
                left: targetOffset.left,
                top: targetOffset.top,
                width: targetWidth,
                height: targetHeight
              },
              element: {
                element: elem,
                left: position.left,
                top: position.top,
                width: elemWidth,
                height: elemHeight
              },
              horizontal: right < 0 ? "left" : left > 0 ? "right" : "center",
              vertical: bottom < 0 ? "top" : top > 0 ? "bottom" : "middle"
            };

            if (targetWidth < elemWidth && abs(left + right) < targetWidth) {
              feedback.horizontal = "center";
            }

            if (targetHeight < elemHeight && abs(top + bottom) < targetHeight) {
              feedback.vertical = "middle";
            }

            if (max(abs(left), abs(right)) > max(abs(top), abs(bottom))) {
              feedback.important = "horizontal";
            } else {
              feedback.important = "vertical";
            }

            options.using.call(this, props, feedback);
          };
        }

        elem.offset($.extend(position, {
          using: using
        }));
      });
    };

    $.ui.position = {
      fit: {
        left: function left(position, data) {
          var within = data.within,
              withinOffset = within.isWindow ? within.scrollLeft : within.offset.left,
              outerWidth = within.width,
              collisionPosLeft = position.left - data.collisionPosition.marginLeft,
              overLeft = withinOffset - collisionPosLeft,
              overRight = collisionPosLeft + data.collisionWidth - outerWidth - withinOffset,
              newOverRight; // Element is wider than within

          if (data.collisionWidth > outerWidth) {
            // Element is initially over the left side of within
            if (overLeft > 0 && overRight <= 0) {
              newOverRight = position.left + overLeft + data.collisionWidth - outerWidth - withinOffset;
              position.left += overLeft - newOverRight; // Element is initially over right side of within
            } else if (overRight > 0 && overLeft <= 0) {
              position.left = withinOffset; // Element is initially over both left and right sides of within
            } else {
              if (overLeft > overRight) {
                position.left = withinOffset + outerWidth - data.collisionWidth;
              } else {
                position.left = withinOffset;
              }
            } // Too far left -> align with left edge

          } else if (overLeft > 0) {
            position.left += overLeft; // Too far right -> align with right edge
          } else if (overRight > 0) {
            position.left -= overRight; // Adjust based on position and margin
          } else {
            position.left = max(position.left - collisionPosLeft, position.left);
          }
        },
        top: function top(position, data) {
          var within = data.within,
              withinOffset = within.isWindow ? within.scrollTop : within.offset.top,
              outerHeight = data.within.height,
              collisionPosTop = position.top - data.collisionPosition.marginTop,
              overTop = withinOffset - collisionPosTop,
              overBottom = collisionPosTop + data.collisionHeight - outerHeight - withinOffset,
              newOverBottom; // Element is taller than within

          if (data.collisionHeight > outerHeight) {
            // Element is initially over the top of within
            if (overTop > 0 && overBottom <= 0) {
              newOverBottom = position.top + overTop + data.collisionHeight - outerHeight - withinOffset;
              position.top += overTop - newOverBottom; // Element is initially over bottom of within
            } else if (overBottom > 0 && overTop <= 0) {
              position.top = withinOffset; // Element is initially over both top and bottom of within
            } else {
              if (overTop > overBottom) {
                position.top = withinOffset + outerHeight - data.collisionHeight;
              } else {
                position.top = withinOffset;
              }
            } // Too far up -> align with top

          } else if (overTop > 0) {
            position.top += overTop; // Too far down -> align with bottom edge
          } else if (overBottom > 0) {
            position.top -= overBottom; // Adjust based on position and margin
          } else {
            position.top = max(position.top - collisionPosTop, position.top);
          }
        }
      },
      flip: {
        left: function left(position, data) {
          var within = data.within,
              withinOffset = within.offset.left + within.scrollLeft,
              outerWidth = within.width,
              offsetLeft = within.isWindow ? within.scrollLeft : within.offset.left,
              collisionPosLeft = position.left - data.collisionPosition.marginLeft,
              overLeft = collisionPosLeft - offsetLeft,
              overRight = collisionPosLeft + data.collisionWidth - outerWidth - offsetLeft,
              myOffset = data.my[0] === "left" ? -data.elemWidth : data.my[0] === "right" ? data.elemWidth : 0,
              atOffset = data.at[0] === "left" ? data.targetWidth : data.at[0] === "right" ? -data.targetWidth : 0,
              offset = -2 * data.offset[0],
              newOverRight,
              newOverLeft;

          if (overLeft < 0) {
            newOverRight = position.left + myOffset + atOffset + offset + data.collisionWidth - outerWidth - withinOffset;

            if (newOverRight < 0 || newOverRight < abs(overLeft)) {
              position.left += myOffset + atOffset + offset;
            }
          } else if (overRight > 0) {
            newOverLeft = position.left - data.collisionPosition.marginLeft + myOffset + atOffset + offset - offsetLeft;

            if (newOverLeft > 0 || abs(newOverLeft) < overRight) {
              position.left += myOffset + atOffset + offset;
            }
          }
        },
        top: function top(position, data) {
          var within = data.within,
              withinOffset = within.offset.top + within.scrollTop,
              outerHeight = within.height,
              offsetTop = within.isWindow ? within.scrollTop : within.offset.top,
              collisionPosTop = position.top - data.collisionPosition.marginTop,
              overTop = collisionPosTop - offsetTop,
              overBottom = collisionPosTop + data.collisionHeight - outerHeight - offsetTop,
              top = data.my[1] === "top",
              myOffset = top ? -data.elemHeight : data.my[1] === "bottom" ? data.elemHeight : 0,
              atOffset = data.at[1] === "top" ? data.targetHeight : data.at[1] === "bottom" ? -data.targetHeight : 0,
              offset = -2 * data.offset[1],
              newOverTop,
              newOverBottom;

          if (overTop < 0) {
            newOverBottom = position.top + myOffset + atOffset + offset + data.collisionHeight - outerHeight - withinOffset;

            if (newOverBottom < 0 || newOverBottom < abs(overTop)) {
              position.top += myOffset + atOffset + offset;
            }
          } else if (overBottom > 0) {
            newOverTop = position.top - data.collisionPosition.marginTop + myOffset + atOffset + offset - offsetTop;

            if (newOverTop > 0 || abs(newOverTop) < overBottom) {
              position.top += myOffset + atOffset + offset;
            }
          }
        }
      },
      flipfit: {
        left: function left() {
          $.ui.position.flip.left.apply(this, arguments);
          $.ui.position.fit.left.apply(this, arguments);
        },
        top: function top() {
          $.ui.position.flip.top.apply(this, arguments);
          $.ui.position.fit.top.apply(this, arguments);
        }
      }
    };
  })();

  return $.ui.position;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-active-element.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-active-element.js ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.ui.safeActiveElement = function (document) {
    var activeElement; // Support: IE 9 only
    // IE9 throws an "Unspecified error" accessing document.activeElement from an <iframe>

    try {
      activeElement = document.activeElement;
    } catch (error) {
      activeElement = document.body;
    } // Support: IE 9 - 11 only
    // IE may return null instead of an element
    // Interestingly, this only seems to occur when NOT in an iframe


    if (!activeElement) {
      activeElement = document.body;
    } // Support: IE 11 only
    // IE11 returns a seemingly empty object in some cases when accessing
    // document.activeElement from an <iframe>


    if (!activeElement.nodeName) {
      activeElement = document.body;
    }

    return activeElement;
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-blur.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-blur.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.ui.safeBlur = function (element) {
    // Support: IE9 - 10 only
    // If the <body> is blurred, IE will switch windows, see #9420
    if (element && element.nodeName.toLowerCase() !== "body") {
      $(element).trigger("blur");
    }
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/scroll-parent.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/scroll-parent.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Scroll Parent 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: scrollParent
//>>group: Core
//>>description: Get the closest ancestor element that is scrollable.
//>>docs: http://api.jqueryui.com/scrollParent/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.fn.scrollParent = function (includeHidden) {
    var position = this.css("position"),
        excludeStaticParent = position === "absolute",
        overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/,
        scrollParent = this.parents().filter(function () {
      var parent = $(this);

      if (excludeStaticParent && parent.css("position") === "static") {
        return false;
      }

      return overflowRegex.test(parent.css("overflow") + parent.css("overflow-y") + parent.css("overflow-x"));
    }).eq(0);
    return position === "fixed" || !scrollParent.length ? $(this[0].ownerDocument || document) : scrollParent;
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/tabbable.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/tabbable.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Tabbable 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: :tabbable Selector
//>>group: Core
//>>description: Selects elements which can be tabbed to.
//>>docs: http://api.jqueryui.com/tabbable-selector/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ./focusable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.extend($.expr[":"], {
    tabbable: function tabbable(element) {
      var tabIndex = $.attr(element, "tabindex"),
          hasTabindex = tabIndex != null;
      return (!hasTabindex || tabIndex >= 0) && $.ui.focusable(element, hasTabindex);
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/unique-id.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/unique-id.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Unique ID 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: uniqueId
//>>group: Core
//>>description: Functions to generate and remove uniqueId's
//>>docs: http://api.jqueryui.com/uniqueId/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  return $.fn.extend({
    uniqueId: function () {
      var uuid = 0;
      return function () {
        return this.each(function () {
          if (!this.id) {
            this.id = "ui-id-" + ++uuid;
          }
        });
      };
    }(),
    removeUniqueId: function removeUniqueId() {
      return this.each(function () {
        if (/^ui-id-\d+$/.test(this.id)) {
          $(this).removeAttr("id");
        }
      });
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  $.ui = $.ui || {};
  return $.ui.version = "1.12.1";
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Widget 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Widget
//>>group: Core
//>>description: Provides a factory for creating stateful widgets with a common API.
//>>docs: http://api.jqueryui.com/jQuery.widget/
//>>demos: http://jqueryui.com/widget/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  var widgetUuid = 0;
  var widgetSlice = Array.prototype.slice;

  $.cleanData = function (orig) {
    return function (elems) {
      var events, elem, i;

      for (i = 0; (elem = elems[i]) != null; i++) {
        try {
          // Only trigger remove when necessary to save time
          events = $._data(elem, "events");

          if (events && events.remove) {
            $(elem).triggerHandler("remove");
          } // Http://bugs.jquery.com/ticket/8235

        } catch (e) {}
      }

      orig(elems);
    };
  }($.cleanData);

  $.widget = function (name, base, prototype) {
    var existingConstructor, constructor, basePrototype; // ProxiedPrototype allows the provided prototype to remain unmodified
    // so that it can be used as a mixin for multiple widgets (#8876)

    var proxiedPrototype = {};
    var namespace = name.split(".")[0];
    name = name.split(".")[1];
    var fullName = namespace + "-" + name;

    if (!prototype) {
      prototype = base;
      base = $.Widget;
    }

    if ($.isArray(prototype)) {
      prototype = $.extend.apply(null, [{}].concat(prototype));
    } // Create selector for plugin


    $.expr[":"][fullName.toLowerCase()] = function (elem) {
      return !!$.data(elem, fullName);
    };

    $[namespace] = $[namespace] || {};
    existingConstructor = $[namespace][name];

    constructor = $[namespace][name] = function (options, element) {
      // Allow instantiation without "new" keyword
      if (!this._createWidget) {
        return new constructor(options, element);
      } // Allow instantiation without initializing for simple inheritance
      // must use "new" keyword (the code above always passes args)


      if (arguments.length) {
        this._createWidget(options, element);
      }
    }; // Extend with the existing constructor to carry over any static properties


    $.extend(constructor, existingConstructor, {
      version: prototype.version,
      // Copy the object used to create the prototype in case we need to
      // redefine the widget later
      _proto: $.extend({}, prototype),
      // Track widgets that inherit from this widget in case this widget is
      // redefined after a widget inherits from it
      _childConstructors: []
    });
    basePrototype = new base(); // We need to make the options hash a property directly on the new instance
    // otherwise we'll modify the options hash on the prototype that we're
    // inheriting from

    basePrototype.options = $.widget.extend({}, basePrototype.options);
    $.each(prototype, function (prop, value) {
      if (!$.isFunction(value)) {
        proxiedPrototype[prop] = value;
        return;
      }

      proxiedPrototype[prop] = function () {
        function _super() {
          return base.prototype[prop].apply(this, arguments);
        }

        function _superApply(args) {
          return base.prototype[prop].apply(this, args);
        }

        return function () {
          var __super = this._super;
          var __superApply = this._superApply;
          var returnValue;
          this._super = _super;
          this._superApply = _superApply;
          returnValue = value.apply(this, arguments);
          this._super = __super;
          this._superApply = __superApply;
          return returnValue;
        };
      }();
    });
    constructor.prototype = $.widget.extend(basePrototype, {
      // TODO: remove support for widgetEventPrefix
      // always use the name + a colon as the prefix, e.g., draggable:start
      // don't prefix for widgets that aren't DOM-based
      widgetEventPrefix: existingConstructor ? basePrototype.widgetEventPrefix || name : name
    }, proxiedPrototype, {
      constructor: constructor,
      namespace: namespace,
      widgetName: name,
      widgetFullName: fullName
    }); // If this widget is being redefined then we need to find all widgets that
    // are inheriting from it and redefine all of them so that they inherit from
    // the new version of this widget. We're essentially trying to replace one
    // level in the prototype chain.

    if (existingConstructor) {
      $.each(existingConstructor._childConstructors, function (i, child) {
        var childPrototype = child.prototype; // Redefine the child widget using the same prototype that was
        // originally used, but inherit from the new version of the base

        $.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto);
      }); // Remove the list of existing child constructors from the old constructor
      // so the old child constructors can be garbage collected

      delete existingConstructor._childConstructors;
    } else {
      base._childConstructors.push(constructor);
    }

    $.widget.bridge(name, constructor);
    return constructor;
  };

  $.widget.extend = function (target) {
    var input = widgetSlice.call(arguments, 1);
    var inputIndex = 0;
    var inputLength = input.length;
    var key;
    var value;

    for (; inputIndex < inputLength; inputIndex++) {
      for (key in input[inputIndex]) {
        value = input[inputIndex][key];

        if (input[inputIndex].hasOwnProperty(key) && value !== undefined) {
          // Clone objects
          if ($.isPlainObject(value)) {
            target[key] = $.isPlainObject(target[key]) ? $.widget.extend({}, target[key], value) : // Don't extend strings, arrays, etc. with objects
            $.widget.extend({}, value); // Copy everything else by reference
          } else {
            target[key] = value;
          }
        }
      }
    }

    return target;
  };

  $.widget.bridge = function (name, object) {
    var fullName = object.prototype.widgetFullName || name;

    $.fn[name] = function (options) {
      var isMethodCall = typeof options === "string";
      var args = widgetSlice.call(arguments, 1);
      var returnValue = this;

      if (isMethodCall) {
        // If this is an empty collection, we need to have the instance method
        // return undefined instead of the jQuery instance
        if (!this.length && options === "instance") {
          returnValue = undefined;
        } else {
          this.each(function () {
            var methodValue;
            var instance = $.data(this, fullName);

            if (options === "instance") {
              returnValue = instance;
              return false;
            }

            if (!instance) {
              return $.error("cannot call methods on " + name + " prior to initialization; " + "attempted to call method '" + options + "'");
            }

            if (!$.isFunction(instance[options]) || options.charAt(0) === "_") {
              return $.error("no such method '" + options + "' for " + name + " widget instance");
            }

            methodValue = instance[options].apply(instance, args);

            if (methodValue !== instance && methodValue !== undefined) {
              returnValue = methodValue && methodValue.jquery ? returnValue.pushStack(methodValue.get()) : methodValue;
              return false;
            }
          });
        }
      } else {
        // Allow multiple hashes to be passed on init
        if (args.length) {
          options = $.widget.extend.apply(null, [options].concat(args));
        }

        this.each(function () {
          var instance = $.data(this, fullName);

          if (instance) {
            instance.option(options || {});

            if (instance._init) {
              instance._init();
            }
          } else {
            $.data(this, fullName, new object(options, this));
          }
        });
      }

      return returnValue;
    };
  };

  $.Widget = function ()
  /* options, element */
  {};

  $.Widget._childConstructors = [];
  $.Widget.prototype = {
    widgetName: "widget",
    widgetEventPrefix: "",
    defaultElement: "<div>",
    options: {
      classes: {},
      disabled: false,
      // Callbacks
      create: null
    },
    _createWidget: function _createWidget(options, element) {
      element = $(element || this.defaultElement || this)[0];
      this.element = $(element);
      this.uuid = widgetUuid++;
      this.eventNamespace = "." + this.widgetName + this.uuid;
      this.bindings = $();
      this.hoverable = $();
      this.focusable = $();
      this.classesElementLookup = {};

      if (element !== this) {
        $.data(element, this.widgetFullName, this);

        this._on(true, this.element, {
          remove: function remove(event) {
            if (event.target === element) {
              this.destroy();
            }
          }
        });

        this.document = $(element.style ? // Element within the document
        element.ownerDocument : // Element is window or document
        element.document || element);
        this.window = $(this.document[0].defaultView || this.document[0].parentWindow);
      }

      this.options = $.widget.extend({}, this.options, this._getCreateOptions(), options);

      this._create();

      if (this.options.disabled) {
        this._setOptionDisabled(this.options.disabled);
      }

      this._trigger("create", null, this._getCreateEventData());

      this._init();
    },
    _getCreateOptions: function _getCreateOptions() {
      return {};
    },
    _getCreateEventData: $.noop,
    _create: $.noop,
    _init: $.noop,
    destroy: function destroy() {
      var that = this;

      this._destroy();

      $.each(this.classesElementLookup, function (key, value) {
        that._removeClass(value, key);
      }); // We can probably remove the unbind calls in 2.0
      // all event bindings should go through this._on()

      this.element.off(this.eventNamespace).removeData(this.widgetFullName);
      this.widget().off(this.eventNamespace).removeAttr("aria-disabled"); // Clean up events and states

      this.bindings.off(this.eventNamespace);
    },
    _destroy: $.noop,
    widget: function widget() {
      return this.element;
    },
    option: function option(key, value) {
      var options = key;
      var parts;
      var curOption;
      var i;

      if (arguments.length === 0) {
        // Don't return a reference to the internal hash
        return $.widget.extend({}, this.options);
      }

      if (typeof key === "string") {
        // Handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
        options = {};
        parts = key.split(".");
        key = parts.shift();

        if (parts.length) {
          curOption = options[key] = $.widget.extend({}, this.options[key]);

          for (i = 0; i < parts.length - 1; i++) {
            curOption[parts[i]] = curOption[parts[i]] || {};
            curOption = curOption[parts[i]];
          }

          key = parts.pop();

          if (arguments.length === 1) {
            return curOption[key] === undefined ? null : curOption[key];
          }

          curOption[key] = value;
        } else {
          if (arguments.length === 1) {
            return this.options[key] === undefined ? null : this.options[key];
          }

          options[key] = value;
        }
      }

      this._setOptions(options);

      return this;
    },
    _setOptions: function _setOptions(options) {
      var key;

      for (key in options) {
        this._setOption(key, options[key]);
      }

      return this;
    },
    _setOption: function _setOption(key, value) {
      if (key === "classes") {
        this._setOptionClasses(value);
      }

      this.options[key] = value;

      if (key === "disabled") {
        this._setOptionDisabled(value);
      }

      return this;
    },
    _setOptionClasses: function _setOptionClasses(value) {
      var classKey, elements, currentElements;

      for (classKey in value) {
        currentElements = this.classesElementLookup[classKey];

        if (value[classKey] === this.options.classes[classKey] || !currentElements || !currentElements.length) {
          continue;
        } // We are doing this to create a new jQuery object because the _removeClass() call
        // on the next line is going to destroy the reference to the current elements being
        // tracked. We need to save a copy of this collection so that we can add the new classes
        // below.


        elements = $(currentElements.get());

        this._removeClass(currentElements, classKey); // We don't use _addClass() here, because that uses this.options.classes
        // for generating the string of classes. We want to use the value passed in from
        // _setOption(), this is the new value of the classes option which was passed to
        // _setOption(). We pass this value directly to _classes().


        elements.addClass(this._classes({
          element: elements,
          keys: classKey,
          classes: value,
          add: true
        }));
      }
    },
    _setOptionDisabled: function _setOptionDisabled(value) {
      this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!value); // If the widget is becoming disabled, then nothing is interactive


      if (value) {
        this._removeClass(this.hoverable, null, "ui-state-hover");

        this._removeClass(this.focusable, null, "ui-state-focus");
      }
    },
    enable: function enable() {
      return this._setOptions({
        disabled: false
      });
    },
    disable: function disable() {
      return this._setOptions({
        disabled: true
      });
    },
    _classes: function _classes(options) {
      var full = [];
      var that = this;
      options = $.extend({
        element: this.element,
        classes: this.options.classes || {}
      }, options);

      function processClassString(classes, checkOption) {
        var current, i;

        for (i = 0; i < classes.length; i++) {
          current = that.classesElementLookup[classes[i]] || $();

          if (options.add) {
            current = $($.unique(current.get().concat(options.element.get())));
          } else {
            current = $(current.not(options.element).get());
          }

          that.classesElementLookup[classes[i]] = current;
          full.push(classes[i]);

          if (checkOption && options.classes[classes[i]]) {
            full.push(options.classes[classes[i]]);
          }
        }
      }

      this._on(options.element, {
        "remove": "_untrackClassesElement"
      });

      if (options.keys) {
        processClassString(options.keys.match(/\S+/g) || [], true);
      }

      if (options.extra) {
        processClassString(options.extra.match(/\S+/g) || []);
      }

      return full.join(" ");
    },
    _untrackClassesElement: function _untrackClassesElement(event) {
      var that = this;
      $.each(that.classesElementLookup, function (key, value) {
        if ($.inArray(event.target, value) !== -1) {
          that.classesElementLookup[key] = $(value.not(event.target).get());
        }
      });
    },
    _removeClass: function _removeClass(element, keys, extra) {
      return this._toggleClass(element, keys, extra, false);
    },
    _addClass: function _addClass(element, keys, extra) {
      return this._toggleClass(element, keys, extra, true);
    },
    _toggleClass: function _toggleClass(element, keys, extra, add) {
      add = typeof add === "boolean" ? add : extra;
      var shift = typeof element === "string" || element === null,
          options = {
        extra: shift ? keys : extra,
        keys: shift ? element : keys,
        element: shift ? this.element : element,
        add: add
      };
      options.element.toggleClass(this._classes(options), add);
      return this;
    },
    _on: function _on(suppressDisabledCheck, element, handlers) {
      var delegateElement;
      var instance = this; // No suppressDisabledCheck flag, shuffle arguments

      if (typeof suppressDisabledCheck !== "boolean") {
        handlers = element;
        element = suppressDisabledCheck;
        suppressDisabledCheck = false;
      } // No element argument, shuffle and use this.element


      if (!handlers) {
        handlers = element;
        element = this.element;
        delegateElement = this.widget();
      } else {
        element = delegateElement = $(element);
        this.bindings = this.bindings.add(element);
      }

      $.each(handlers, function (event, handler) {
        function handlerProxy() {
          // Allow widgets to customize the disabled handling
          // - disabled as an array instead of boolean
          // - disabled class as method for disabling individual parts
          if (!suppressDisabledCheck && (instance.options.disabled === true || $(this).hasClass("ui-state-disabled"))) {
            return;
          }

          return (typeof handler === "string" ? instance[handler] : handler).apply(instance, arguments);
        } // Copy the guid so direct unbinding works


        if (typeof handler !== "string") {
          handlerProxy.guid = handler.guid = handler.guid || handlerProxy.guid || $.guid++;
        }

        var match = event.match(/^([\w:-]*)\s*(.*)$/);
        var eventName = match[1] + instance.eventNamespace;
        var selector = match[2];

        if (selector) {
          delegateElement.on(eventName, selector, handlerProxy);
        } else {
          element.on(eventName, handlerProxy);
        }
      });
    },
    _off: function _off(element, eventName) {
      eventName = (eventName || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace;
      element.off(eventName).off(eventName); // Clear the stack to avoid memory leaks (#10056)

      this.bindings = $(this.bindings.not(element).get());
      this.focusable = $(this.focusable.not(element).get());
      this.hoverable = $(this.hoverable.not(element).get());
    },
    _delay: function _delay(handler, delay) {
      function handlerProxy() {
        return (typeof handler === "string" ? instance[handler] : handler).apply(instance, arguments);
      }

      var instance = this;
      return setTimeout(handlerProxy, delay || 0);
    },
    _hoverable: function _hoverable(element) {
      this.hoverable = this.hoverable.add(element);

      this._on(element, {
        mouseenter: function mouseenter(event) {
          this._addClass($(event.currentTarget), null, "ui-state-hover");
        },
        mouseleave: function mouseleave(event) {
          this._removeClass($(event.currentTarget), null, "ui-state-hover");
        }
      });
    },
    _focusable: function _focusable(element) {
      this.focusable = this.focusable.add(element);

      this._on(element, {
        focusin: function focusin(event) {
          this._addClass($(event.currentTarget), null, "ui-state-focus");
        },
        focusout: function focusout(event) {
          this._removeClass($(event.currentTarget), null, "ui-state-focus");
        }
      });
    },
    _trigger: function _trigger(type, event, data) {
      var prop, orig;
      var callback = this.options[type];
      data = data || {};
      event = $.Event(event);
      event.type = (type === this.widgetEventPrefix ? type : this.widgetEventPrefix + type).toLowerCase(); // The original event may come from any element
      // so we need to reset the target on the new event

      event.target = this.element[0]; // Copy original event properties over to the new event

      orig = event.originalEvent;

      if (orig) {
        for (prop in orig) {
          if (!(prop in event)) {
            event[prop] = orig[prop];
          }
        }
      }

      this.element.trigger(event, data);
      return !($.isFunction(callback) && callback.apply(this.element[0], [event].concat(data)) === false || event.isDefaultPrevented());
    }
  };
  $.each({
    show: "fadeIn",
    hide: "fadeOut"
  }, function (method, defaultEffect) {
    $.Widget.prototype["_" + method] = function (element, options, callback) {
      if (typeof options === "string") {
        options = {
          effect: options
        };
      }

      var hasOptions;
      var effectName = !options ? method : options === true || typeof options === "number" ? defaultEffect : options.effect || defaultEffect;
      options = options || {};

      if (typeof options === "number") {
        options = {
          duration: options
        };
      }

      hasOptions = !$.isEmptyObject(options);
      options.complete = callback;

      if (options.delay) {
        element.delay(options.delay);
      }

      if (hasOptions && $.effects && $.effects.effect[effectName]) {
        element[method](options);
      } else if (effectName !== method && element[effectName]) {
        element[effectName](options.duration, options.easing, callback);
      } else {
        element.queue(function (next) {
          $(this)[method]();

          if (callback) {
            callback.call(element[0]);
          }

          next();
        });
      }
    };
  });
  return $.widget;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/button.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/button.js ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*!
 * jQuery UI Button 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Button
//>>group: Widgets
//>>description: Enhances a form with themeable buttons.
//>>docs: http://api.jqueryui.com/button/
//>>demos: http://jqueryui.com/button/
//>>css.structure: ../../themes/base/core.css
//>>css.structure: ../../themes/base/button.css
//>>css.theme: ../../themes/base/theme.css
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), // These are only for backcompat
    // TODO: Remove after 1.12
    __webpack_require__(/*! ./controlgroup */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/controlgroup.js"), __webpack_require__(/*! ./checkboxradio */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/checkboxradio.js"), __webpack_require__(/*! ../keycode */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  $.widget("ui.button", {
    version: "1.12.1",
    defaultElement: "<button>",
    options: {
      classes: {
        "ui-button": "ui-corner-all"
      },
      disabled: null,
      icon: null,
      iconPosition: "beginning",
      label: null,
      showLabel: true
    },
    _getCreateOptions: function _getCreateOptions() {
      var disabled,
          // This is to support cases like in jQuery Mobile where the base widget does have
      // an implementation of _getCreateOptions
      options = this._super() || {};
      this.isInput = this.element.is("input");
      disabled = this.element[0].disabled;

      if (disabled != null) {
        options.disabled = disabled;
      }

      this.originalLabel = this.isInput ? this.element.val() : this.element.html();

      if (this.originalLabel) {
        options.label = this.originalLabel;
      }

      return options;
    },
    _create: function _create() {
      if (!this.option.showLabel & !this.options.icon) {
        this.options.showLabel = true;
      } // We have to check the option again here even though we did in _getCreateOptions,
      // because null may have been passed on init which would override what was set in
      // _getCreateOptions


      if (this.options.disabled == null) {
        this.options.disabled = this.element[0].disabled || false;
      }

      this.hasTitle = !!this.element.attr("title"); // Check to see if the label needs to be set or if its already correct

      if (this.options.label && this.options.label !== this.originalLabel) {
        if (this.isInput) {
          this.element.val(this.options.label);
        } else {
          this.element.html(this.options.label);
        }
      }

      this._addClass("ui-button", "ui-widget");

      this._setOption("disabled", this.options.disabled);

      this._enhance();

      if (this.element.is("a")) {
        this._on({
          "keyup": function keyup(event) {
            if (event.keyCode === $.ui.keyCode.SPACE) {
              event.preventDefault(); // Support: PhantomJS <= 1.9, IE 8 Only
              // If a native click is available use it so we actually cause navigation
              // otherwise just trigger a click event

              if (this.element[0].click) {
                this.element[0].click();
              } else {
                this.element.trigger("click");
              }
            }
          }
        });
      }
    },
    _enhance: function _enhance() {
      if (!this.element.is("button")) {
        this.element.attr("role", "button");
      }

      if (this.options.icon) {
        this._updateIcon("icon", this.options.icon);

        this._updateTooltip();
      }
    },
    _updateTooltip: function _updateTooltip() {
      this.title = this.element.attr("title");

      if (!this.options.showLabel && !this.title) {
        this.element.attr("title", this.options.label);
      }
    },
    _updateIcon: function _updateIcon(option, value) {
      var icon = option !== "iconPosition",
          position = icon ? this.options.iconPosition : value,
          displayBlock = position === "top" || position === "bottom"; // Create icon

      if (!this.icon) {
        this.icon = $("<span>");

        this._addClass(this.icon, "ui-button-icon", "ui-icon");

        if (!this.options.showLabel) {
          this._addClass("ui-button-icon-only");
        }
      } else if (icon) {
        // If we are updating the icon remove the old icon class
        this._removeClass(this.icon, null, this.options.icon);
      } // If we are updating the icon add the new icon class


      if (icon) {
        this._addClass(this.icon, null, value);
      }

      this._attachIcon(position); // If the icon is on top or bottom we need to add the ui-widget-icon-block class and remove
      // the iconSpace if there is one.


      if (displayBlock) {
        this._addClass(this.icon, null, "ui-widget-icon-block");

        if (this.iconSpace) {
          this.iconSpace.remove();
        }
      } else {
        // Position is beginning or end so remove the ui-widget-icon-block class and add the
        // space if it does not exist
        if (!this.iconSpace) {
          this.iconSpace = $("<span> </span>");

          this._addClass(this.iconSpace, "ui-button-icon-space");
        }

        this._removeClass(this.icon, null, "ui-wiget-icon-block");

        this._attachIconSpace(position);
      }
    },
    _destroy: function _destroy() {
      this.element.removeAttr("role");

      if (this.icon) {
        this.icon.remove();
      }

      if (this.iconSpace) {
        this.iconSpace.remove();
      }

      if (!this.hasTitle) {
        this.element.removeAttr("title");
      }
    },
    _attachIconSpace: function _attachIconSpace(iconPosition) {
      this.icon[/^(?:end|bottom)/.test(iconPosition) ? "before" : "after"](this.iconSpace);
    },
    _attachIcon: function _attachIcon(iconPosition) {
      this.element[/^(?:end|bottom)/.test(iconPosition) ? "append" : "prepend"](this.icon);
    },
    _setOptions: function _setOptions(options) {
      var newShowLabel = options.showLabel === undefined ? this.options.showLabel : options.showLabel,
          newIcon = options.icon === undefined ? this.options.icon : options.icon;

      if (!newShowLabel && !newIcon) {
        options.showLabel = true;
      }

      this._super(options);
    },
    _setOption: function _setOption(key, value) {
      if (key === "icon") {
        if (value) {
          this._updateIcon(key, value);
        } else if (this.icon) {
          this.icon.remove();

          if (this.iconSpace) {
            this.iconSpace.remove();
          }
        }
      }

      if (key === "iconPosition") {
        this._updateIcon(key, value);
      } // Make sure we can't end up with a button that has neither text nor icon


      if (key === "showLabel") {
        this._toggleClass("ui-button-icon-only", null, !value);

        this._updateTooltip();
      }

      if (key === "label") {
        if (this.isInput) {
          this.element.val(value);
        } else {
          // If there is an icon, append it, else nothing then append the value
          // this avoids removal of the icon when setting label text
          this.element.html(value);

          if (this.icon) {
            this._attachIcon(this.options.iconPosition);

            this._attachIconSpace(this.options.iconPosition);
          }
        }
      }

      this._super(key, value);

      if (key === "disabled") {
        this._toggleClass(null, "ui-state-disabled", value);

        this.element[0].disabled = value;

        if (value) {
          this.element.blur();
        }
      }
    },
    refresh: function refresh() {
      // Make sure to only check disabled if its an element that supports this otherwise
      // check for the disabled class to determine state
      var isDisabled = this.element.is("input, button") ? this.element[0].disabled : this.element.hasClass("ui-button-disabled");

      if (isDisabled !== this.options.disabled) {
        this._setOptions({
          disabled: isDisabled
        });
      }

      this._updateTooltip();
    }
  }); // DEPRECATED

  if ($.uiBackCompat !== false) {
    // Text and Icons options
    $.widget("ui.button", $.ui.button, {
      options: {
        text: true,
        icons: {
          primary: null,
          secondary: null
        }
      },
      _create: function _create() {
        if (this.options.showLabel && !this.options.text) {
          this.options.showLabel = this.options.text;
        }

        if (!this.options.showLabel && this.options.text) {
          this.options.text = this.options.showLabel;
        }

        if (!this.options.icon && (this.options.icons.primary || this.options.icons.secondary)) {
          if (this.options.icons.primary) {
            this.options.icon = this.options.icons.primary;
          } else {
            this.options.icon = this.options.icons.secondary;
            this.options.iconPosition = "end";
          }
        } else if (this.options.icon) {
          this.options.icons.primary = this.options.icon;
        }

        this._super();
      },
      _setOption: function _setOption(key, value) {
        if (key === "text") {
          this._super("showLabel", value);

          return;
        }

        if (key === "showLabel") {
          this.options.text = value;
        }

        if (key === "icon") {
          this.options.icons.primary = value;
        }

        if (key === "icons") {
          if (value.primary) {
            this._super("icon", value.primary);

            this._super("iconPosition", "beginning");
          } else if (value.secondary) {
            this._super("icon", value.secondary);

            this._super("iconPosition", "end");
          }
        }

        this._superApply(arguments);
      }
    });

    $.fn.button = function (orig) {
      return function () {
        if (!this.length || this.length && this[0].tagName !== "INPUT" || this.length && this[0].tagName === "INPUT" && this.attr("type") !== "checkbox" && this.attr("type") !== "radio") {
          return orig.apply(this, arguments);
        }

        if (!$.ui.checkboxradio) {
          $.error("Checkboxradio widget missing");
        }

        if (arguments.length === 0) {
          return this.checkboxradio({
            "icon": false
          });
        }

        return this.checkboxradio.apply(this, arguments);
      };
    }($.fn.button);

    $.fn.buttonset = function () {
      if (!$.ui.controlgroup) {
        $.error("Controlgroup widget missing");
      }

      if (arguments[0] === "option" && arguments[1] === "items" && arguments[2]) {
        return this.controlgroup.apply(this, [arguments[0], "items.button", arguments[2]]);
      }

      if (arguments[0] === "option" && arguments[1] === "items") {
        return this.controlgroup.apply(this, [arguments[0], "items.button"]);
      }

      if (_typeof(arguments[0]) === "object" && arguments[0].items) {
        arguments[0].items = {
          button: arguments[0].items
        };
      }

      return this.controlgroup.apply(this, arguments);
    };
  }

  return $.ui.button;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/checkboxradio.js":
/*!**********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/checkboxradio.js ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Checkboxradio 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Checkboxradio
//>>group: Widgets
//>>description: Enhances a form with multiple themeable checkboxes or radio buttons.
//>>docs: http://api.jqueryui.com/checkboxradio/
//>>demos: http://jqueryui.com/checkboxradio/
//>>css.structure: ../../themes/base/core.css
//>>css.structure: ../../themes/base/button.css
//>>css.structure: ../../themes/base/checkboxradio.css
//>>css.theme: ../../themes/base/theme.css
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ../escape-selector */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/escape-selector.js"), __webpack_require__(/*! ../form-reset-mixin */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form-reset-mixin.js"), __webpack_require__(/*! ../labels */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/labels.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  $.widget("ui.checkboxradio", [$.ui.formResetMixin, {
    version: "1.12.1",
    options: {
      disabled: null,
      label: null,
      icon: true,
      classes: {
        "ui-checkboxradio-label": "ui-corner-all",
        "ui-checkboxradio-icon": "ui-corner-all"
      }
    },
    _getCreateOptions: function _getCreateOptions() {
      var disabled, labels;
      var that = this;
      var options = this._super() || {}; // We read the type here, because it makes more sense to throw a element type error first,
      // rather then the error for lack of a label. Often if its the wrong type, it
      // won't have a label (e.g. calling on a div, btn, etc)

      this._readType();

      labels = this.element.labels(); // If there are multiple labels, use the last one

      this.label = $(labels[labels.length - 1]);

      if (!this.label.length) {
        $.error("No label found for checkboxradio widget");
      }

      this.originalLabel = ""; // We need to get the label text but this may also need to make sure it does not contain the
      // input itself.

      this.label.contents().not(this.element[0]).each(function () {
        // The label contents could be text, html, or a mix. We concat each element to get a
        // string representation of the label, without the input as part of it.
        that.originalLabel += this.nodeType === 3 ? $(this).text() : this.outerHTML;
      }); // Set the label option if we found label text

      if (this.originalLabel) {
        options.label = this.originalLabel;
      }

      disabled = this.element[0].disabled;

      if (disabled != null) {
        options.disabled = disabled;
      }

      return options;
    },
    _create: function _create() {
      var checked = this.element[0].checked;

      this._bindFormResetHandler();

      if (this.options.disabled == null) {
        this.options.disabled = this.element[0].disabled;
      }

      this._setOption("disabled", this.options.disabled);

      this._addClass("ui-checkboxradio", "ui-helper-hidden-accessible");

      this._addClass(this.label, "ui-checkboxradio-label", "ui-button ui-widget");

      if (this.type === "radio") {
        this._addClass(this.label, "ui-checkboxradio-radio-label");
      }

      if (this.options.label && this.options.label !== this.originalLabel) {
        this._updateLabel();
      } else if (this.originalLabel) {
        this.options.label = this.originalLabel;
      }

      this._enhance();

      if (checked) {
        this._addClass(this.label, "ui-checkboxradio-checked", "ui-state-active");

        if (this.icon) {
          this._addClass(this.icon, null, "ui-state-hover");
        }
      }

      this._on({
        change: "_toggleClasses",
        focus: function focus() {
          this._addClass(this.label, null, "ui-state-focus ui-visual-focus");
        },
        blur: function blur() {
          this._removeClass(this.label, null, "ui-state-focus ui-visual-focus");
        }
      });
    },
    _readType: function _readType() {
      var nodeName = this.element[0].nodeName.toLowerCase();
      this.type = this.element[0].type;

      if (nodeName !== "input" || !/radio|checkbox/.test(this.type)) {
        $.error("Can't create checkboxradio on element.nodeName=" + nodeName + " and element.type=" + this.type);
      }
    },
    // Support jQuery Mobile enhanced option
    _enhance: function _enhance() {
      this._updateIcon(this.element[0].checked);
    },
    widget: function widget() {
      return this.label;
    },
    _getRadioGroup: function _getRadioGroup() {
      var group;
      var name = this.element[0].name;
      var nameSelector = "input[name='" + $.ui.escapeSelector(name) + "']";

      if (!name) {
        return $([]);
      }

      if (this.form.length) {
        group = $(this.form[0].elements).filter(nameSelector);
      } else {
        // Not inside a form, check all inputs that also are not inside a form
        group = $(nameSelector).filter(function () {
          return $(this).form().length === 0;
        });
      }

      return group.not(this.element);
    },
    _toggleClasses: function _toggleClasses() {
      var checked = this.element[0].checked;

      this._toggleClass(this.label, "ui-checkboxradio-checked", "ui-state-active", checked);

      if (this.options.icon && this.type === "checkbox") {
        this._toggleClass(this.icon, null, "ui-icon-check ui-state-checked", checked)._toggleClass(this.icon, null, "ui-icon-blank", !checked);
      }

      if (this.type === "radio") {
        this._getRadioGroup().each(function () {
          var instance = $(this).checkboxradio("instance");

          if (instance) {
            instance._removeClass(instance.label, "ui-checkboxradio-checked", "ui-state-active");
          }
        });
      }
    },
    _destroy: function _destroy() {
      this._unbindFormResetHandler();

      if (this.icon) {
        this.icon.remove();
        this.iconSpace.remove();
      }
    },
    _setOption: function _setOption(key, value) {
      // We don't allow the value to be set to nothing
      if (key === "label" && !value) {
        return;
      }

      this._super(key, value);

      if (key === "disabled") {
        this._toggleClass(this.label, null, "ui-state-disabled", value);

        this.element[0].disabled = value; // Don't refresh when setting disabled

        return;
      }

      this.refresh();
    },
    _updateIcon: function _updateIcon(checked) {
      var toAdd = "ui-icon ui-icon-background ";

      if (this.options.icon) {
        if (!this.icon) {
          this.icon = $("<span>");
          this.iconSpace = $("<span> </span>");

          this._addClass(this.iconSpace, "ui-checkboxradio-icon-space");
        }

        if (this.type === "checkbox") {
          toAdd += checked ? "ui-icon-check ui-state-checked" : "ui-icon-blank";

          this._removeClass(this.icon, null, checked ? "ui-icon-blank" : "ui-icon-check");
        } else {
          toAdd += "ui-icon-blank";
        }

        this._addClass(this.icon, "ui-checkboxradio-icon", toAdd);

        if (!checked) {
          this._removeClass(this.icon, null, "ui-icon-check ui-state-checked");
        }

        this.icon.prependTo(this.label).after(this.iconSpace);
      } else if (this.icon !== undefined) {
        this.icon.remove();
        this.iconSpace.remove();
        delete this.icon;
      }
    },
    _updateLabel: function _updateLabel() {
      // Remove the contents of the label ( minus the icon, icon space, and input )
      var contents = this.label.contents().not(this.element[0]);

      if (this.icon) {
        contents = contents.not(this.icon[0]);
      }

      if (this.iconSpace) {
        contents = contents.not(this.iconSpace[0]);
      }

      contents.remove();
      this.label.append(this.options.label);
    },
    refresh: function refresh() {
      var checked = this.element[0].checked,
          isDisabled = this.element[0].disabled;

      this._updateIcon(checked);

      this._toggleClass(this.label, "ui-checkboxradio-checked", "ui-state-active", checked);

      if (this.options.label !== null) {
        this._updateLabel();
      }

      if (isDisabled !== this.options.disabled) {
        this._setOptions({
          "disabled": isDisabled
        });
      }
    }
  }]);
  return $.ui.checkboxradio;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/controlgroup.js":
/*!*********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/controlgroup.js ***!
  \*********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Controlgroup 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Controlgroup
//>>group: Widgets
//>>description: Visually groups form control widgets
//>>docs: http://api.jqueryui.com/controlgroup/
//>>demos: http://jqueryui.com/controlgroup/
//>>css.structure: ../../themes/base/core.css
//>>css.structure: ../../themes/base/controlgroup.css
//>>css.theme: ../../themes/base/theme.css
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  var controlgroupCornerRegex = /ui-corner-([a-z]){2,6}/g;
  return $.widget("ui.controlgroup", {
    version: "1.12.1",
    defaultElement: "<div>",
    options: {
      direction: "horizontal",
      disabled: null,
      onlyVisible: true,
      items: {
        "button": "input[type=button], input[type=submit], input[type=reset], button, a",
        "controlgroupLabel": ".ui-controlgroup-label",
        "checkboxradio": "input[type='checkbox'], input[type='radio']",
        "selectmenu": "select",
        "spinner": ".ui-spinner-input"
      }
    },
    _create: function _create() {
      this._enhance();
    },
    // To support the enhanced option in jQuery Mobile, we isolate DOM manipulation
    _enhance: function _enhance() {
      this.element.attr("role", "toolbar");
      this.refresh();
    },
    _destroy: function _destroy() {
      this._callChildMethod("destroy");

      this.childWidgets.removeData("ui-controlgroup-data");
      this.element.removeAttr("role");

      if (this.options.items.controlgroupLabel) {
        this.element.find(this.options.items.controlgroupLabel).find(".ui-controlgroup-label-contents").contents().unwrap();
      }
    },
    _initWidgets: function _initWidgets() {
      var that = this,
          childWidgets = []; // First we iterate over each of the items options

      $.each(this.options.items, function (widget, selector) {
        var labels;
        var options = {}; // Make sure the widget has a selector set

        if (!selector) {
          return;
        }

        if (widget === "controlgroupLabel") {
          labels = that.element.find(selector);
          labels.each(function () {
            var element = $(this);

            if (element.children(".ui-controlgroup-label-contents").length) {
              return;
            }

            element.contents().wrapAll("<span class='ui-controlgroup-label-contents'></span>");
          });

          that._addClass(labels, null, "ui-widget ui-widget-content ui-state-default");

          childWidgets = childWidgets.concat(labels.get());
          return;
        } // Make sure the widget actually exists


        if (!$.fn[widget]) {
          return;
        } // We assume everything is in the middle to start because we can't determine
        // first / last elements until all enhancments are done.


        if (that["_" + widget + "Options"]) {
          options = that["_" + widget + "Options"]("middle");
        } else {
          options = {
            classes: {}
          };
        } // Find instances of this widget inside controlgroup and init them


        that.element.find(selector).each(function () {
          var element = $(this);
          var instance = element[widget]("instance"); // We need to clone the default options for this type of widget to avoid
          // polluting the variable options which has a wider scope than a single widget.

          var instanceOptions = $.widget.extend({}, options); // If the button is the child of a spinner ignore it
          // TODO: Find a more generic solution

          if (widget === "button" && element.parent(".ui-spinner").length) {
            return;
          } // Create the widget if it doesn't exist


          if (!instance) {
            instance = element[widget]()[widget]("instance");
          }

          if (instance) {
            instanceOptions.classes = that._resolveClassesValues(instanceOptions.classes, instance);
          }

          element[widget](instanceOptions); // Store an instance of the controlgroup to be able to reference
          // from the outermost element for changing options and refresh

          var widgetElement = element[widget]("widget");
          $.data(widgetElement[0], "ui-controlgroup-data", instance ? instance : element[widget]("instance"));
          childWidgets.push(widgetElement[0]);
        });
      });
      this.childWidgets = $($.unique(childWidgets));

      this._addClass(this.childWidgets, "ui-controlgroup-item");
    },
    _callChildMethod: function _callChildMethod(method) {
      this.childWidgets.each(function () {
        var element = $(this),
            data = element.data("ui-controlgroup-data");

        if (data && data[method]) {
          data[method]();
        }
      });
    },
    _updateCornerClass: function _updateCornerClass(element, position) {
      var remove = "ui-corner-top ui-corner-bottom ui-corner-left ui-corner-right ui-corner-all";

      var add = this._buildSimpleOptions(position, "label").classes.label;

      this._removeClass(element, null, remove);

      this._addClass(element, null, add);
    },
    _buildSimpleOptions: function _buildSimpleOptions(position, key) {
      var direction = this.options.direction === "vertical";
      var result = {
        classes: {}
      };
      result.classes[key] = {
        "middle": "",
        "first": "ui-corner-" + (direction ? "top" : "left"),
        "last": "ui-corner-" + (direction ? "bottom" : "right"),
        "only": "ui-corner-all"
      }[position];
      return result;
    },
    _spinnerOptions: function _spinnerOptions(position) {
      var options = this._buildSimpleOptions(position, "ui-spinner");

      options.classes["ui-spinner-up"] = "";
      options.classes["ui-spinner-down"] = "";
      return options;
    },
    _buttonOptions: function _buttonOptions(position) {
      return this._buildSimpleOptions(position, "ui-button");
    },
    _checkboxradioOptions: function _checkboxradioOptions(position) {
      return this._buildSimpleOptions(position, "ui-checkboxradio-label");
    },
    _selectmenuOptions: function _selectmenuOptions(position) {
      var direction = this.options.direction === "vertical";
      return {
        width: direction ? "auto" : false,
        classes: {
          middle: {
            "ui-selectmenu-button-open": "",
            "ui-selectmenu-button-closed": ""
          },
          first: {
            "ui-selectmenu-button-open": "ui-corner-" + (direction ? "top" : "tl"),
            "ui-selectmenu-button-closed": "ui-corner-" + (direction ? "top" : "left")
          },
          last: {
            "ui-selectmenu-button-open": direction ? "" : "ui-corner-tr",
            "ui-selectmenu-button-closed": "ui-corner-" + (direction ? "bottom" : "right")
          },
          only: {
            "ui-selectmenu-button-open": "ui-corner-top",
            "ui-selectmenu-button-closed": "ui-corner-all"
          }
        }[position]
      };
    },
    _resolveClassesValues: function _resolveClassesValues(classes, instance) {
      var result = {};
      $.each(classes, function (key) {
        var current = instance.options.classes[key] || "";
        current = $.trim(current.replace(controlgroupCornerRegex, ""));
        result[key] = (current + " " + classes[key]).replace(/\s+/g, " ");
      });
      return result;
    },
    _setOption: function _setOption(key, value) {
      if (key === "direction") {
        this._removeClass("ui-controlgroup-" + this.options.direction);
      }

      this._super(key, value);

      if (key === "disabled") {
        this._callChildMethod(value ? "disable" : "enable");

        return;
      }

      this.refresh();
    },
    refresh: function refresh() {
      var children,
          that = this;

      this._addClass("ui-controlgroup ui-controlgroup-" + this.options.direction);

      if (this.options.direction === "horizontal") {
        this._addClass(null, "ui-helper-clearfix");
      }

      this._initWidgets();

      children = this.childWidgets; // We filter here because we need to track all childWidgets not just the visible ones

      if (this.options.onlyVisible) {
        children = children.filter(":visible");
      }

      if (children.length) {
        // We do this last because we need to make sure all enhancment is done
        // before determining first and last
        $.each(["first", "last"], function (index, value) {
          var instance = children[value]().data("ui-controlgroup-data");

          if (instance && that["_" + instance.widgetName + "Options"]) {
            var options = that["_" + instance.widgetName + "Options"](children.length === 1 ? "only" : value);
            options.classes = that._resolveClassesValues(options.classes, instance);
            instance.element[instance.widgetName](options);
          } else {
            that._updateCornerClass(children[value](), value);
          }
        }); // Finally call the refresh method on each of the child widgets.

        this._callChildMethod("refresh");
      }
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/dialog.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/dialog.js ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Dialog 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Dialog
//>>group: Widgets
//>>description: Displays customizable dialog windows.
//>>docs: http://api.jqueryui.com/dialog/
//>>demos: http://jqueryui.com/dialog/
//>>css.structure: ../../themes/base/core.css
//>>css.structure: ../../themes/base/dialog.css
//>>css.theme: ../../themes/base/theme.css
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./button */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/button.js"), __webpack_require__(/*! ./draggable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/draggable.js"), __webpack_require__(/*! ./mouse */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js"), __webpack_require__(/*! ./resizable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/resizable.js"), __webpack_require__(/*! ../focusable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js"), __webpack_require__(/*! ../keycode */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js"), __webpack_require__(/*! ../position */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/position.js"), __webpack_require__(/*! ../safe-active-element */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-active-element.js"), __webpack_require__(/*! ../safe-blur */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-blur.js"), __webpack_require__(/*! ../tabbable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/tabbable.js"), __webpack_require__(/*! ../unique-id */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/unique-id.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  $.widget("ui.dialog", {
    version: "1.12.1",
    options: {
      appendTo: "body",
      autoOpen: true,
      buttons: [],
      classes: {
        "ui-dialog": "ui-corner-all",
        "ui-dialog-titlebar": "ui-corner-all"
      },
      closeOnEscape: true,
      closeText: "Close",
      draggable: true,
      hide: null,
      height: "auto",
      maxHeight: null,
      maxWidth: null,
      minHeight: 150,
      minWidth: 150,
      modal: false,
      position: {
        my: "center",
        at: "center",
        of: window,
        collision: "fit",
        // Ensure the titlebar is always visible
        using: function using(pos) {
          var topOffset = $(this).css(pos).offset().top;

          if (topOffset < 0) {
            $(this).css("top", pos.top - topOffset);
          }
        }
      },
      resizable: true,
      show: null,
      title: null,
      width: 300,
      // Callbacks
      beforeClose: null,
      close: null,
      drag: null,
      dragStart: null,
      dragStop: null,
      focus: null,
      open: null,
      resize: null,
      resizeStart: null,
      resizeStop: null
    },
    sizeRelatedOptions: {
      buttons: true,
      height: true,
      maxHeight: true,
      maxWidth: true,
      minHeight: true,
      minWidth: true,
      width: true
    },
    resizableRelatedOptions: {
      maxHeight: true,
      maxWidth: true,
      minHeight: true,
      minWidth: true
    },
    _create: function _create() {
      this.originalCss = {
        display: this.element[0].style.display,
        width: this.element[0].style.width,
        minHeight: this.element[0].style.minHeight,
        maxHeight: this.element[0].style.maxHeight,
        height: this.element[0].style.height
      };
      this.originalPosition = {
        parent: this.element.parent(),
        index: this.element.parent().children().index(this.element)
      };
      this.originalTitle = this.element.attr("title");

      if (this.options.title == null && this.originalTitle != null) {
        this.options.title = this.originalTitle;
      } // Dialogs can't be disabled


      if (this.options.disabled) {
        this.options.disabled = false;
      }

      this._createWrapper();

      this.element.show().removeAttr("title").appendTo(this.uiDialog);

      this._addClass("ui-dialog-content", "ui-widget-content");

      this._createTitlebar();

      this._createButtonPane();

      if (this.options.draggable && $.fn.draggable) {
        this._makeDraggable();
      }

      if (this.options.resizable && $.fn.resizable) {
        this._makeResizable();
      }

      this._isOpen = false;

      this._trackFocus();
    },
    _init: function _init() {
      if (this.options.autoOpen) {
        this.open();
      }
    },
    _appendTo: function _appendTo() {
      var element = this.options.appendTo;

      if (element && (element.jquery || element.nodeType)) {
        return $(element);
      }

      return this.document.find(element || "body").eq(0);
    },
    _destroy: function _destroy() {
      var next,
          originalPosition = this.originalPosition;

      this._untrackInstance();

      this._destroyOverlay();

      this.element.removeUniqueId().css(this.originalCss) // Without detaching first, the following becomes really slow
      .detach();
      this.uiDialog.remove();

      if (this.originalTitle) {
        this.element.attr("title", this.originalTitle);
      }

      next = originalPosition.parent.children().eq(originalPosition.index); // Don't try to place the dialog next to itself (#8613)

      if (next.length && next[0] !== this.element[0]) {
        next.before(this.element);
      } else {
        originalPosition.parent.append(this.element);
      }
    },
    widget: function widget() {
      return this.uiDialog;
    },
    disable: $.noop,
    enable: $.noop,
    close: function close(event) {
      var that = this;

      if (!this._isOpen || this._trigger("beforeClose", event) === false) {
        return;
      }

      this._isOpen = false;
      this._focusedElement = null;

      this._destroyOverlay();

      this._untrackInstance();

      if (!this.opener.filter(":focusable").trigger("focus").length) {
        // Hiding a focused element doesn't trigger blur in WebKit
        // so in case we have nothing to focus on, explicitly blur the active element
        // https://bugs.webkit.org/show_bug.cgi?id=47182
        $.ui.safeBlur($.ui.safeActiveElement(this.document[0]));
      }

      this._hide(this.uiDialog, this.options.hide, function () {
        that._trigger("close", event);
      });
    },
    isOpen: function isOpen() {
      return this._isOpen;
    },
    moveToTop: function moveToTop() {
      this._moveToTop();
    },
    _moveToTop: function _moveToTop(event, silent) {
      var moved = false,
          zIndices = this.uiDialog.siblings(".ui-front:visible").map(function () {
        return +$(this).css("z-index");
      }).get(),
          zIndexMax = Math.max.apply(null, zIndices);

      if (zIndexMax >= +this.uiDialog.css("z-index")) {
        this.uiDialog.css("z-index", zIndexMax + 1);
        moved = true;
      }

      if (moved && !silent) {
        this._trigger("focus", event);
      }

      return moved;
    },
    open: function open() {
      var that = this;

      if (this._isOpen) {
        if (this._moveToTop()) {
          this._focusTabbable();
        }

        return;
      }

      this._isOpen = true;
      this.opener = $($.ui.safeActiveElement(this.document[0]));

      this._size();

      this._position();

      this._createOverlay();

      this._moveToTop(null, true); // Ensure the overlay is moved to the top with the dialog, but only when
      // opening. The overlay shouldn't move after the dialog is open so that
      // modeless dialogs opened after the modal dialog stack properly.


      if (this.overlay) {
        this.overlay.css("z-index", this.uiDialog.css("z-index") - 1);
      }

      this._show(this.uiDialog, this.options.show, function () {
        that._focusTabbable();

        that._trigger("focus");
      }); // Track the dialog immediately upon openening in case a focus event
      // somehow occurs outside of the dialog before an element inside the
      // dialog is focused (#10152)


      this._makeFocusTarget();

      this._trigger("open");
    },
    _focusTabbable: function _focusTabbable() {
      // Set focus to the first match:
      // 1. An element that was focused previously
      // 2. First element inside the dialog matching [autofocus]
      // 3. Tabbable element inside the content element
      // 4. Tabbable element inside the buttonpane
      // 5. The close button
      // 6. The dialog itself
      var hasFocus = this._focusedElement;

      if (!hasFocus) {
        hasFocus = this.element.find("[autofocus]");
      }

      if (!hasFocus.length) {
        hasFocus = this.element.find(":tabbable");
      }

      if (!hasFocus.length) {
        hasFocus = this.uiDialogButtonPane.find(":tabbable");
      }

      if (!hasFocus.length) {
        hasFocus = this.uiDialogTitlebarClose.filter(":tabbable");
      }

      if (!hasFocus.length) {
        hasFocus = this.uiDialog;
      }

      hasFocus.eq(0).trigger("focus");
    },
    _keepFocus: function _keepFocus(event) {
      function checkFocus() {
        var activeElement = $.ui.safeActiveElement(this.document[0]),
            isActive = this.uiDialog[0] === activeElement || $.contains(this.uiDialog[0], activeElement);

        if (!isActive) {
          this._focusTabbable();
        }
      }

      event.preventDefault();
      checkFocus.call(this); // support: IE
      // IE <= 8 doesn't prevent moving focus even with event.preventDefault()
      // so we check again later

      this._delay(checkFocus);
    },
    _createWrapper: function _createWrapper() {
      this.uiDialog = $("<div>").hide().attr({
        // Setting tabIndex makes the div focusable
        tabIndex: -1,
        role: "dialog"
      }).appendTo(this._appendTo());

      this._addClass(this.uiDialog, "ui-dialog", "ui-widget ui-widget-content ui-front");

      this._on(this.uiDialog, {
        keydown: function keydown(event) {
          if (this.options.closeOnEscape && !event.isDefaultPrevented() && event.keyCode && event.keyCode === $.ui.keyCode.ESCAPE) {
            event.preventDefault();
            this.close(event);
            return;
          } // Prevent tabbing out of dialogs


          if (event.keyCode !== $.ui.keyCode.TAB || event.isDefaultPrevented()) {
            return;
          }

          var tabbables = this.uiDialog.find(":tabbable"),
              first = tabbables.filter(":first"),
              last = tabbables.filter(":last");

          if ((event.target === last[0] || event.target === this.uiDialog[0]) && !event.shiftKey) {
            this._delay(function () {
              first.trigger("focus");
            });

            event.preventDefault();
          } else if ((event.target === first[0] || event.target === this.uiDialog[0]) && event.shiftKey) {
            this._delay(function () {
              last.trigger("focus");
            });

            event.preventDefault();
          }
        },
        mousedown: function mousedown(event) {
          if (this._moveToTop(event)) {
            this._focusTabbable();
          }
        }
      }); // We assume that any existing aria-describedby attribute means
      // that the dialog content is marked up properly
      // otherwise we brute force the content as the description


      if (!this.element.find("[aria-describedby]").length) {
        this.uiDialog.attr({
          "aria-describedby": this.element.uniqueId().attr("id")
        });
      }
    },
    _createTitlebar: function _createTitlebar() {
      var uiDialogTitle;
      this.uiDialogTitlebar = $("<div>");

      this._addClass(this.uiDialogTitlebar, "ui-dialog-titlebar", "ui-widget-header ui-helper-clearfix");

      this._on(this.uiDialogTitlebar, {
        mousedown: function mousedown(event) {
          // Don't prevent click on close button (#8838)
          // Focusing a dialog that is partially scrolled out of view
          // causes the browser to scroll it into view, preventing the click event
          if (!$(event.target).closest(".ui-dialog-titlebar-close")) {
            // Dialog isn't getting focus when dragging (#8063)
            this.uiDialog.trigger("focus");
          }
        }
      }); // Support: IE
      // Use type="button" to prevent enter keypresses in textboxes from closing the
      // dialog in IE (#9312)


      this.uiDialogTitlebarClose = $("<button type='button'></button>").button({
        label: $("<a>").text(this.options.closeText).html(),
        icon: "ui-icon-closethick",
        showLabel: false
      }).appendTo(this.uiDialogTitlebar);

      this._addClass(this.uiDialogTitlebarClose, "ui-dialog-titlebar-close");

      this._on(this.uiDialogTitlebarClose, {
        click: function click(event) {
          event.preventDefault();
          this.close(event);
        }
      });

      uiDialogTitle = $("<span>").uniqueId().prependTo(this.uiDialogTitlebar);

      this._addClass(uiDialogTitle, "ui-dialog-title");

      this._title(uiDialogTitle);

      this.uiDialogTitlebar.prependTo(this.uiDialog);
      this.uiDialog.attr({
        "aria-labelledby": uiDialogTitle.attr("id")
      });
    },
    _title: function _title(title) {
      if (this.options.title) {
        title.text(this.options.title);
      } else {
        title.html("&#160;");
      }
    },
    _createButtonPane: function _createButtonPane() {
      this.uiDialogButtonPane = $("<div>");

      this._addClass(this.uiDialogButtonPane, "ui-dialog-buttonpane", "ui-widget-content ui-helper-clearfix");

      this.uiButtonSet = $("<div>").appendTo(this.uiDialogButtonPane);

      this._addClass(this.uiButtonSet, "ui-dialog-buttonset");

      this._createButtons();
    },
    _createButtons: function _createButtons() {
      var that = this,
          buttons = this.options.buttons; // If we already have a button pane, remove it

      this.uiDialogButtonPane.remove();
      this.uiButtonSet.empty();

      if ($.isEmptyObject(buttons) || $.isArray(buttons) && !buttons.length) {
        this._removeClass(this.uiDialog, "ui-dialog-buttons");

        return;
      }

      $.each(buttons, function (name, props) {
        var click, buttonOptions;
        props = $.isFunction(props) ? {
          click: props,
          text: name
        } : props; // Default to a non-submitting button

        props = $.extend({
          type: "button"
        }, props); // Change the context for the click callback to be the main element

        click = props.click;
        buttonOptions = {
          icon: props.icon,
          iconPosition: props.iconPosition,
          showLabel: props.showLabel,
          // Deprecated options
          icons: props.icons,
          text: props.text
        };
        delete props.click;
        delete props.icon;
        delete props.iconPosition;
        delete props.showLabel; // Deprecated options

        delete props.icons;

        if (typeof props.text === "boolean") {
          delete props.text;
        }

        $("<button></button>", props).button(buttonOptions).appendTo(that.uiButtonSet).on("click", function () {
          click.apply(that.element[0], arguments);
        });
      });

      this._addClass(this.uiDialog, "ui-dialog-buttons");

      this.uiDialogButtonPane.appendTo(this.uiDialog);
    },
    _makeDraggable: function _makeDraggable() {
      var that = this,
          options = this.options;

      function filteredUi(ui) {
        return {
          position: ui.position,
          offset: ui.offset
        };
      }

      this.uiDialog.draggable({
        cancel: ".ui-dialog-content, .ui-dialog-titlebar-close",
        handle: ".ui-dialog-titlebar",
        containment: "document",
        start: function start(event, ui) {
          that._addClass($(this), "ui-dialog-dragging");

          that._blockFrames();

          that._trigger("dragStart", event, filteredUi(ui));
        },
        drag: function drag(event, ui) {
          that._trigger("drag", event, filteredUi(ui));
        },
        stop: function stop(event, ui) {
          var left = ui.offset.left - that.document.scrollLeft(),
              top = ui.offset.top - that.document.scrollTop();
          options.position = {
            my: "left top",
            at: "left" + (left >= 0 ? "+" : "") + left + " " + "top" + (top >= 0 ? "+" : "") + top,
            of: that.window
          };

          that._removeClass($(this), "ui-dialog-dragging");

          that._unblockFrames();

          that._trigger("dragStop", event, filteredUi(ui));
        }
      });
    },
    _makeResizable: function _makeResizable() {
      var that = this,
          options = this.options,
          handles = options.resizable,
          // .ui-resizable has position: relative defined in the stylesheet
      // but dialogs have to use absolute or fixed positioning
      position = this.uiDialog.css("position"),
          resizeHandles = typeof handles === "string" ? handles : "n,e,s,w,se,sw,ne,nw";

      function filteredUi(ui) {
        return {
          originalPosition: ui.originalPosition,
          originalSize: ui.originalSize,
          position: ui.position,
          size: ui.size
        };
      }

      this.uiDialog.resizable({
        cancel: ".ui-dialog-content",
        containment: "document",
        alsoResize: this.element,
        maxWidth: options.maxWidth,
        maxHeight: options.maxHeight,
        minWidth: options.minWidth,
        minHeight: this._minHeight(),
        handles: resizeHandles,
        start: function start(event, ui) {
          that._addClass($(this), "ui-dialog-resizing");

          that._blockFrames();

          that._trigger("resizeStart", event, filteredUi(ui));
        },
        resize: function resize(event, ui) {
          that._trigger("resize", event, filteredUi(ui));
        },
        stop: function stop(event, ui) {
          var offset = that.uiDialog.offset(),
              left = offset.left - that.document.scrollLeft(),
              top = offset.top - that.document.scrollTop();
          options.height = that.uiDialog.height();
          options.width = that.uiDialog.width();
          options.position = {
            my: "left top",
            at: "left" + (left >= 0 ? "+" : "") + left + " " + "top" + (top >= 0 ? "+" : "") + top,
            of: that.window
          };

          that._removeClass($(this), "ui-dialog-resizing");

          that._unblockFrames();

          that._trigger("resizeStop", event, filteredUi(ui));
        }
      }).css("position", position);
    },
    _trackFocus: function _trackFocus() {
      this._on(this.widget(), {
        focusin: function focusin(event) {
          this._makeFocusTarget();

          this._focusedElement = $(event.target);
        }
      });
    },
    _makeFocusTarget: function _makeFocusTarget() {
      this._untrackInstance();

      this._trackingInstances().unshift(this);
    },
    _untrackInstance: function _untrackInstance() {
      var instances = this._trackingInstances(),
          exists = $.inArray(this, instances);

      if (exists !== -1) {
        instances.splice(exists, 1);
      }
    },
    _trackingInstances: function _trackingInstances() {
      var instances = this.document.data("ui-dialog-instances");

      if (!instances) {
        instances = [];
        this.document.data("ui-dialog-instances", instances);
      }

      return instances;
    },
    _minHeight: function _minHeight() {
      var options = this.options;
      return options.height === "auto" ? options.minHeight : Math.min(options.minHeight, options.height);
    },
    _position: function _position() {
      // Need to show the dialog to get the actual offset in the position plugin
      var isVisible = this.uiDialog.is(":visible");

      if (!isVisible) {
        this.uiDialog.show();
      }

      this.uiDialog.position(this.options.position);

      if (!isVisible) {
        this.uiDialog.hide();
      }
    },
    _setOptions: function _setOptions(options) {
      var that = this,
          resize = false,
          resizableOptions = {};
      $.each(options, function (key, value) {
        that._setOption(key, value);

        if (key in that.sizeRelatedOptions) {
          resize = true;
        }

        if (key in that.resizableRelatedOptions) {
          resizableOptions[key] = value;
        }
      });

      if (resize) {
        this._size();

        this._position();
      }

      if (this.uiDialog.is(":data(ui-resizable)")) {
        this.uiDialog.resizable("option", resizableOptions);
      }
    },
    _setOption: function _setOption(key, value) {
      var isDraggable,
          isResizable,
          uiDialog = this.uiDialog;

      if (key === "disabled") {
        return;
      }

      this._super(key, value);

      if (key === "appendTo") {
        this.uiDialog.appendTo(this._appendTo());
      }

      if (key === "buttons") {
        this._createButtons();
      }

      if (key === "closeText") {
        this.uiDialogTitlebarClose.button({
          // Ensure that we always pass a string
          label: $("<a>").text("" + this.options.closeText).html()
        });
      }

      if (key === "draggable") {
        isDraggable = uiDialog.is(":data(ui-draggable)");

        if (isDraggable && !value) {
          uiDialog.draggable("destroy");
        }

        if (!isDraggable && value) {
          this._makeDraggable();
        }
      }

      if (key === "position") {
        this._position();
      }

      if (key === "resizable") {
        // currently resizable, becoming non-resizable
        isResizable = uiDialog.is(":data(ui-resizable)");

        if (isResizable && !value) {
          uiDialog.resizable("destroy");
        } // Currently resizable, changing handles


        if (isResizable && typeof value === "string") {
          uiDialog.resizable("option", "handles", value);
        } // Currently non-resizable, becoming resizable


        if (!isResizable && value !== false) {
          this._makeResizable();
        }
      }

      if (key === "title") {
        this._title(this.uiDialogTitlebar.find(".ui-dialog-title"));
      }
    },
    _size: function _size() {
      // If the user has resized the dialog, the .ui-dialog and .ui-dialog-content
      // divs will both have width and height set, so we need to reset them
      var nonContentHeight,
          minContentHeight,
          maxContentHeight,
          options = this.options; // Reset content sizing

      this.element.show().css({
        width: "auto",
        minHeight: 0,
        maxHeight: "none",
        height: 0
      });

      if (options.minWidth > options.width) {
        options.width = options.minWidth;
      } // Reset wrapper sizing
      // determine the height of all the non-content elements


      nonContentHeight = this.uiDialog.css({
        height: "auto",
        width: options.width
      }).outerHeight();
      minContentHeight = Math.max(0, options.minHeight - nonContentHeight);
      maxContentHeight = typeof options.maxHeight === "number" ? Math.max(0, options.maxHeight - nonContentHeight) : "none";

      if (options.height === "auto") {
        this.element.css({
          minHeight: minContentHeight,
          maxHeight: maxContentHeight,
          height: "auto"
        });
      } else {
        this.element.height(Math.max(0, options.height - nonContentHeight));
      }

      if (this.uiDialog.is(":data(ui-resizable)")) {
        this.uiDialog.resizable("option", "minHeight", this._minHeight());
      }
    },
    _blockFrames: function _blockFrames() {
      this.iframeBlocks = this.document.find("iframe").map(function () {
        var iframe = $(this);
        return $("<div>").css({
          position: "absolute",
          width: iframe.outerWidth(),
          height: iframe.outerHeight()
        }).appendTo(iframe.parent()).offset(iframe.offset())[0];
      });
    },
    _unblockFrames: function _unblockFrames() {
      if (this.iframeBlocks) {
        this.iframeBlocks.remove();
        delete this.iframeBlocks;
      }
    },
    _allowInteraction: function _allowInteraction(event) {
      if ($(event.target).closest(".ui-dialog").length) {
        return true;
      } // TODO: Remove hack when datepicker implements
      // the .ui-front logic (#8989)


      return !!$(event.target).closest(".ui-datepicker").length;
    },
    _createOverlay: function _createOverlay() {
      if (!this.options.modal) {
        return;
      } // We use a delay in case the overlay is created from an
      // event that we're going to be cancelling (#2804)


      var isOpening = true;

      this._delay(function () {
        isOpening = false;
      });

      if (!this.document.data("ui-dialog-overlays")) {
        // Prevent use of anchors and inputs
        // Using _on() for an event handler shared across many instances is
        // safe because the dialogs stack and must be closed in reverse order
        this._on(this.document, {
          focusin: function focusin(event) {
            if (isOpening) {
              return;
            }

            if (!this._allowInteraction(event)) {
              event.preventDefault();

              this._trackingInstances()[0]._focusTabbable();
            }
          }
        });
      }

      this.overlay = $("<div>").appendTo(this._appendTo());

      this._addClass(this.overlay, null, "ui-widget-overlay ui-front");

      this._on(this.overlay, {
        mousedown: "_keepFocus"
      });

      this.document.data("ui-dialog-overlays", (this.document.data("ui-dialog-overlays") || 0) + 1);
    },
    _destroyOverlay: function _destroyOverlay() {
      if (!this.options.modal) {
        return;
      }

      if (this.overlay) {
        var overlays = this.document.data("ui-dialog-overlays") - 1;

        if (!overlays) {
          this._off(this.document, "focusin");

          this.document.removeData("ui-dialog-overlays");
        } else {
          this.document.data("ui-dialog-overlays", overlays);
        }

        this.overlay.remove();
        this.overlay = null;
      }
    }
  }); // DEPRECATED
  // TODO: switch return back to widget declaration at top of file when this is removed

  if ($.uiBackCompat !== false) {
    // Backcompat for dialogClass option
    $.widget("ui.dialog", $.ui.dialog, {
      options: {
        dialogClass: ""
      },
      _createWrapper: function _createWrapper() {
        this._super();

        this.uiDialog.addClass(this.options.dialogClass);
      },
      _setOption: function _setOption(key, value) {
        if (key === "dialogClass") {
          this.uiDialog.removeClass(this.options.dialogClass).addClass(value);
        }

        this._superApply(arguments);
      }
    });
  }

  return $.ui.dialog;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/draggable.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/draggable.js ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Draggable 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Draggable
//>>group: Interactions
//>>description: Enables dragging functionality for any element.
//>>docs: http://api.jqueryui.com/draggable/
//>>demos: http://jqueryui.com/draggable/
//>>css.structure: ../../themes/base/draggable.css
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./mouse */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js"), __webpack_require__(/*! ../data */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/data.js"), __webpack_require__(/*! ../plugin */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js"), __webpack_require__(/*! ../safe-active-element */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-active-element.js"), __webpack_require__(/*! ../safe-blur */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-blur.js"), __webpack_require__(/*! ../scroll-parent */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/scroll-parent.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  $.widget("ui.draggable", $.ui.mouse, {
    version: "1.12.1",
    widgetEventPrefix: "drag",
    options: {
      addClasses: true,
      appendTo: "parent",
      axis: false,
      connectToSortable: false,
      containment: false,
      cursor: "auto",
      cursorAt: false,
      grid: false,
      handle: false,
      helper: "original",
      iframeFix: false,
      opacity: false,
      refreshPositions: false,
      revert: false,
      revertDuration: 500,
      scope: "default",
      scroll: true,
      scrollSensitivity: 20,
      scrollSpeed: 20,
      snap: false,
      snapMode: "both",
      snapTolerance: 20,
      stack: false,
      zIndex: false,
      // Callbacks
      drag: null,
      start: null,
      stop: null
    },
    _create: function _create() {
      if (this.options.helper === "original") {
        this._setPositionRelative();
      }

      if (this.options.addClasses) {
        this._addClass("ui-draggable");
      }

      this._setHandleClassName();

      this._mouseInit();
    },
    _setOption: function _setOption(key, value) {
      this._super(key, value);

      if (key === "handle") {
        this._removeHandleClassName();

        this._setHandleClassName();
      }
    },
    _destroy: function _destroy() {
      if ((this.helper || this.element).is(".ui-draggable-dragging")) {
        this.destroyOnClear = true;
        return;
      }

      this._removeHandleClassName();

      this._mouseDestroy();
    },
    _mouseCapture: function _mouseCapture(event) {
      var o = this.options; // Among others, prevent a drag on a resizable-handle

      if (this.helper || o.disabled || $(event.target).closest(".ui-resizable-handle").length > 0) {
        return false;
      } //Quit if we're not on a valid handle


      this.handle = this._getHandle(event);

      if (!this.handle) {
        return false;
      }

      this._blurActiveElement(event);

      this._blockFrames(o.iframeFix === true ? "iframe" : o.iframeFix);

      return true;
    },
    _blockFrames: function _blockFrames(selector) {
      this.iframeBlocks = this.document.find(selector).map(function () {
        var iframe = $(this);
        return $("<div>").css("position", "absolute").appendTo(iframe.parent()).outerWidth(iframe.outerWidth()).outerHeight(iframe.outerHeight()).offset(iframe.offset())[0];
      });
    },
    _unblockFrames: function _unblockFrames() {
      if (this.iframeBlocks) {
        this.iframeBlocks.remove();
        delete this.iframeBlocks;
      }
    },
    _blurActiveElement: function _blurActiveElement(event) {
      var activeElement = $.ui.safeActiveElement(this.document[0]),
          target = $(event.target); // Don't blur if the event occurred on an element that is within
      // the currently focused element
      // See #10527, #12472

      if (target.closest(activeElement).length) {
        return;
      } // Blur any element that currently has focus, see #4261


      $.ui.safeBlur(activeElement);
    },
    _mouseStart: function _mouseStart(event) {
      var o = this.options; //Create and append the visible helper

      this.helper = this._createHelper(event);

      this._addClass(this.helper, "ui-draggable-dragging"); //Cache the helper size


      this._cacheHelperProportions(); //If ddmanager is used for droppables, set the global draggable


      if ($.ui.ddmanager) {
        $.ui.ddmanager.current = this;
      }
      /*
       * - Position generation -
       * This block generates everything position related - it's the core of draggables.
       */
      //Cache the margins of the original element


      this._cacheMargins(); //Store the helper's css position


      this.cssPosition = this.helper.css("position");
      this.scrollParent = this.helper.scrollParent(true);
      this.offsetParent = this.helper.offsetParent();
      this.hasFixedAncestor = this.helper.parents().filter(function () {
        return $(this).css("position") === "fixed";
      }).length > 0; //The element's absolute position on the page minus margins

      this.positionAbs = this.element.offset();

      this._refreshOffsets(event); //Generate the original position


      this.originalPosition = this.position = this._generatePosition(event, false);
      this.originalPageX = event.pageX;
      this.originalPageY = event.pageY; //Adjust the mouse offset relative to the helper if "cursorAt" is supplied

      o.cursorAt && this._adjustOffsetFromHelper(o.cursorAt); //Set a containment if given in the options

      this._setContainment(); //Trigger event + callbacks


      if (this._trigger("start", event) === false) {
        this._clear();

        return false;
      } //Recache the helper size


      this._cacheHelperProportions(); //Prepare the droppable offsets


      if ($.ui.ddmanager && !o.dropBehaviour) {
        $.ui.ddmanager.prepareOffsets(this, event);
      } // Execute the drag once - this causes the helper not to be visible before getting its
      // correct position


      this._mouseDrag(event, true); // If the ddmanager is used for droppables, inform the manager that dragging has started
      // (see #5003)


      if ($.ui.ddmanager) {
        $.ui.ddmanager.dragStart(this, event);
      }

      return true;
    },
    _refreshOffsets: function _refreshOffsets(event) {
      this.offset = {
        top: this.positionAbs.top - this.margins.top,
        left: this.positionAbs.left - this.margins.left,
        scroll: false,
        parent: this._getParentOffset(),
        relative: this._getRelativeOffset()
      };
      this.offset.click = {
        left: event.pageX - this.offset.left,
        top: event.pageY - this.offset.top
      };
    },
    _mouseDrag: function _mouseDrag(event, noPropagation) {
      // reset any necessary cached properties (see #5009)
      if (this.hasFixedAncestor) {
        this.offset.parent = this._getParentOffset();
      } //Compute the helpers position


      this.position = this._generatePosition(event, true);
      this.positionAbs = this._convertPositionTo("absolute"); //Call plugins and callbacks and use the resulting position if something is returned

      if (!noPropagation) {
        var ui = this._uiHash();

        if (this._trigger("drag", event, ui) === false) {
          this._mouseUp(new $.Event("mouseup", event));

          return false;
        }

        this.position = ui.position;
      }

      this.helper[0].style.left = this.position.left + "px";
      this.helper[0].style.top = this.position.top + "px";

      if ($.ui.ddmanager) {
        $.ui.ddmanager.drag(this, event);
      }

      return false;
    },
    _mouseStop: function _mouseStop(event) {
      //If we are using droppables, inform the manager about the drop
      var that = this,
          dropped = false;

      if ($.ui.ddmanager && !this.options.dropBehaviour) {
        dropped = $.ui.ddmanager.drop(this, event);
      } //if a drop comes from outside (a sortable)


      if (this.dropped) {
        dropped = this.dropped;
        this.dropped = false;
      }

      if (this.options.revert === "invalid" && !dropped || this.options.revert === "valid" && dropped || this.options.revert === true || $.isFunction(this.options.revert) && this.options.revert.call(this.element, dropped)) {
        $(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function () {
          if (that._trigger("stop", event) !== false) {
            that._clear();
          }
        });
      } else {
        if (this._trigger("stop", event) !== false) {
          this._clear();
        }
      }

      return false;
    },
    _mouseUp: function _mouseUp(event) {
      this._unblockFrames(); // If the ddmanager is used for droppables, inform the manager that dragging has stopped
      // (see #5003)


      if ($.ui.ddmanager) {
        $.ui.ddmanager.dragStop(this, event);
      } // Only need to focus if the event occurred on the draggable itself, see #10527


      if (this.handleElement.is(event.target)) {
        // The interaction is over; whether or not the click resulted in a drag,
        // focus the element
        this.element.trigger("focus");
      }

      return $.ui.mouse.prototype._mouseUp.call(this, event);
    },
    cancel: function cancel() {
      if (this.helper.is(".ui-draggable-dragging")) {
        this._mouseUp(new $.Event("mouseup", {
          target: this.element[0]
        }));
      } else {
        this._clear();
      }

      return this;
    },
    _getHandle: function _getHandle(event) {
      return this.options.handle ? !!$(event.target).closest(this.element.find(this.options.handle)).length : true;
    },
    _setHandleClassName: function _setHandleClassName() {
      this.handleElement = this.options.handle ? this.element.find(this.options.handle) : this.element;

      this._addClass(this.handleElement, "ui-draggable-handle");
    },
    _removeHandleClassName: function _removeHandleClassName() {
      this._removeClass(this.handleElement, "ui-draggable-handle");
    },
    _createHelper: function _createHelper(event) {
      var o = this.options,
          helperIsFunction = $.isFunction(o.helper),
          helper = helperIsFunction ? $(o.helper.apply(this.element[0], [event])) : o.helper === "clone" ? this.element.clone().removeAttr("id") : this.element;

      if (!helper.parents("body").length) {
        helper.appendTo(o.appendTo === "parent" ? this.element[0].parentNode : o.appendTo);
      } // Http://bugs.jqueryui.com/ticket/9446
      // a helper function can return the original element
      // which wouldn't have been set to relative in _create


      if (helperIsFunction && helper[0] === this.element[0]) {
        this._setPositionRelative();
      }

      if (helper[0] !== this.element[0] && !/(fixed|absolute)/.test(helper.css("position"))) {
        helper.css("position", "absolute");
      }

      return helper;
    },
    _setPositionRelative: function _setPositionRelative() {
      if (!/^(?:r|a|f)/.test(this.element.css("position"))) {
        this.element[0].style.position = "relative";
      }
    },
    _adjustOffsetFromHelper: function _adjustOffsetFromHelper(obj) {
      if (typeof obj === "string") {
        obj = obj.split(" ");
      }

      if ($.isArray(obj)) {
        obj = {
          left: +obj[0],
          top: +obj[1] || 0
        };
      }

      if ("left" in obj) {
        this.offset.click.left = obj.left + this.margins.left;
      }

      if ("right" in obj) {
        this.offset.click.left = this.helperProportions.width - obj.right + this.margins.left;
      }

      if ("top" in obj) {
        this.offset.click.top = obj.top + this.margins.top;
      }

      if ("bottom" in obj) {
        this.offset.click.top = this.helperProportions.height - obj.bottom + this.margins.top;
      }
    },
    _isRootNode: function _isRootNode(element) {
      return /(html|body)/i.test(element.tagName) || element === this.document[0];
    },
    _getParentOffset: function _getParentOffset() {
      //Get the offsetParent and cache its position
      var po = this.offsetParent.offset(),
          document = this.document[0]; // This is a special case where we need to modify a offset calculated on start, since the
      // following happened:
      // 1. The position of the helper is absolute, so it's position is calculated based on the
      // next positioned parent
      // 2. The actual offset parent is a child of the scroll parent, and the scroll parent isn't
      // the document, which means that the scroll is included in the initial calculation of the
      // offset of the parent, and never recalculated upon drag

      if (this.cssPosition === "absolute" && this.scrollParent[0] !== document && $.contains(this.scrollParent[0], this.offsetParent[0])) {
        po.left += this.scrollParent.scrollLeft();
        po.top += this.scrollParent.scrollTop();
      }

      if (this._isRootNode(this.offsetParent[0])) {
        po = {
          top: 0,
          left: 0
        };
      }

      return {
        top: po.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
        left: po.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
      };
    },
    _getRelativeOffset: function _getRelativeOffset() {
      if (this.cssPosition !== "relative") {
        return {
          top: 0,
          left: 0
        };
      }

      var p = this.element.position(),
          scrollIsRootNode = this._isRootNode(this.scrollParent[0]);

      return {
        top: p.top - (parseInt(this.helper.css("top"), 10) || 0) + (!scrollIsRootNode ? this.scrollParent.scrollTop() : 0),
        left: p.left - (parseInt(this.helper.css("left"), 10) || 0) + (!scrollIsRootNode ? this.scrollParent.scrollLeft() : 0)
      };
    },
    _cacheMargins: function _cacheMargins() {
      this.margins = {
        left: parseInt(this.element.css("marginLeft"), 10) || 0,
        top: parseInt(this.element.css("marginTop"), 10) || 0,
        right: parseInt(this.element.css("marginRight"), 10) || 0,
        bottom: parseInt(this.element.css("marginBottom"), 10) || 0
      };
    },
    _cacheHelperProportions: function _cacheHelperProportions() {
      this.helperProportions = {
        width: this.helper.outerWidth(),
        height: this.helper.outerHeight()
      };
    },
    _setContainment: function _setContainment() {
      var isUserScrollable,
          c,
          ce,
          o = this.options,
          document = this.document[0];
      this.relativeContainer = null;

      if (!o.containment) {
        this.containment = null;
        return;
      }

      if (o.containment === "window") {
        this.containment = [$(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left, $(window).scrollTop() - this.offset.relative.top - this.offset.parent.top, $(window).scrollLeft() + $(window).width() - this.helperProportions.width - this.margins.left, $(window).scrollTop() + ($(window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top];
        return;
      }

      if (o.containment === "document") {
        this.containment = [0, 0, $(document).width() - this.helperProportions.width - this.margins.left, ($(document).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top];
        return;
      }

      if (o.containment.constructor === Array) {
        this.containment = o.containment;
        return;
      }

      if (o.containment === "parent") {
        o.containment = this.helper[0].parentNode;
      }

      c = $(o.containment);
      ce = c[0];

      if (!ce) {
        return;
      }

      isUserScrollable = /(scroll|auto)/.test(c.css("overflow"));
      this.containment = [(parseInt(c.css("borderLeftWidth"), 10) || 0) + (parseInt(c.css("paddingLeft"), 10) || 0), (parseInt(c.css("borderTopWidth"), 10) || 0) + (parseInt(c.css("paddingTop"), 10) || 0), (isUserScrollable ? Math.max(ce.scrollWidth, ce.offsetWidth) : ce.offsetWidth) - (parseInt(c.css("borderRightWidth"), 10) || 0) - (parseInt(c.css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left - this.margins.right, (isUserScrollable ? Math.max(ce.scrollHeight, ce.offsetHeight) : ce.offsetHeight) - (parseInt(c.css("borderBottomWidth"), 10) || 0) - (parseInt(c.css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top - this.margins.bottom];
      this.relativeContainer = c;
    },
    _convertPositionTo: function _convertPositionTo(d, pos) {
      if (!pos) {
        pos = this.position;
      }

      var mod = d === "absolute" ? 1 : -1,
          scrollIsRootNode = this._isRootNode(this.scrollParent[0]);

      return {
        top: // The absolute mouse position
        pos.top + // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.top * mod + // The offsetParent's offset without borders (offset + border)
        this.offset.parent.top * mod - (this.cssPosition === "fixed" ? -this.offset.scroll.top : scrollIsRootNode ? 0 : this.offset.scroll.top) * mod,
        left: // The absolute mouse position
        pos.left + // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.left * mod + // The offsetParent's offset without borders (offset + border)
        this.offset.parent.left * mod - (this.cssPosition === "fixed" ? -this.offset.scroll.left : scrollIsRootNode ? 0 : this.offset.scroll.left) * mod
      };
    },
    _generatePosition: function _generatePosition(event, constrainPosition) {
      var containment,
          co,
          top,
          left,
          o = this.options,
          scrollIsRootNode = this._isRootNode(this.scrollParent[0]),
          pageX = event.pageX,
          pageY = event.pageY; // Cache the scroll


      if (!scrollIsRootNode || !this.offset.scroll) {
        this.offset.scroll = {
          top: this.scrollParent.scrollTop(),
          left: this.scrollParent.scrollLeft()
        };
      }
      /*
       * - Position constraining -
       * Constrain the position to a mix of grid, containment.
       */
      // If we are not dragging yet, we won't check for options


      if (constrainPosition) {
        if (this.containment) {
          if (this.relativeContainer) {
            co = this.relativeContainer.offset();
            containment = [this.containment[0] + co.left, this.containment[1] + co.top, this.containment[2] + co.left, this.containment[3] + co.top];
          } else {
            containment = this.containment;
          }

          if (event.pageX - this.offset.click.left < containment[0]) {
            pageX = containment[0] + this.offset.click.left;
          }

          if (event.pageY - this.offset.click.top < containment[1]) {
            pageY = containment[1] + this.offset.click.top;
          }

          if (event.pageX - this.offset.click.left > containment[2]) {
            pageX = containment[2] + this.offset.click.left;
          }

          if (event.pageY - this.offset.click.top > containment[3]) {
            pageY = containment[3] + this.offset.click.top;
          }
        }

        if (o.grid) {
          //Check for grid elements set to 0 to prevent divide by 0 error causing invalid
          // argument errors in IE (see ticket #6950)
          top = o.grid[1] ? this.originalPageY + Math.round((pageY - this.originalPageY) / o.grid[1]) * o.grid[1] : this.originalPageY;
          pageY = containment ? top - this.offset.click.top >= containment[1] || top - this.offset.click.top > containment[3] ? top : top - this.offset.click.top >= containment[1] ? top - o.grid[1] : top + o.grid[1] : top;
          left = o.grid[0] ? this.originalPageX + Math.round((pageX - this.originalPageX) / o.grid[0]) * o.grid[0] : this.originalPageX;
          pageX = containment ? left - this.offset.click.left >= containment[0] || left - this.offset.click.left > containment[2] ? left : left - this.offset.click.left >= containment[0] ? left - o.grid[0] : left + o.grid[0] : left;
        }

        if (o.axis === "y") {
          pageX = this.originalPageX;
        }

        if (o.axis === "x") {
          pageY = this.originalPageY;
        }
      }

      return {
        top: // The absolute mouse position
        pageY - // Click offset (relative to the element)
        this.offset.click.top - // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.top - // The offsetParent's offset without borders (offset + border)
        this.offset.parent.top + (this.cssPosition === "fixed" ? -this.offset.scroll.top : scrollIsRootNode ? 0 : this.offset.scroll.top),
        left: // The absolute mouse position
        pageX - // Click offset (relative to the element)
        this.offset.click.left - // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.left - // The offsetParent's offset without borders (offset + border)
        this.offset.parent.left + (this.cssPosition === "fixed" ? -this.offset.scroll.left : scrollIsRootNode ? 0 : this.offset.scroll.left)
      };
    },
    _clear: function _clear() {
      this._removeClass(this.helper, "ui-draggable-dragging");

      if (this.helper[0] !== this.element[0] && !this.cancelHelperRemoval) {
        this.helper.remove();
      }

      this.helper = null;
      this.cancelHelperRemoval = false;

      if (this.destroyOnClear) {
        this.destroy();
      }
    },
    // From now on bulk stuff - mainly helpers
    _trigger: function _trigger(type, event, ui) {
      ui = ui || this._uiHash();
      $.ui.plugin.call(this, type, [event, ui, this], true); // Absolute position and offset (see #6884 ) have to be recalculated after plugins

      if (/^(drag|start|stop)/.test(type)) {
        this.positionAbs = this._convertPositionTo("absolute");
        ui.offset = this.positionAbs;
      }

      return $.Widget.prototype._trigger.call(this, type, event, ui);
    },
    plugins: {},
    _uiHash: function _uiHash() {
      return {
        helper: this.helper,
        position: this.position,
        originalPosition: this.originalPosition,
        offset: this.positionAbs
      };
    }
  });
  $.ui.plugin.add("draggable", "connectToSortable", {
    start: function start(event, ui, draggable) {
      var uiSortable = $.extend({}, ui, {
        item: draggable.element
      });
      draggable.sortables = [];
      $(draggable.options.connectToSortable).each(function () {
        var sortable = $(this).sortable("instance");

        if (sortable && !sortable.options.disabled) {
          draggable.sortables.push(sortable); // RefreshPositions is called at drag start to refresh the containerCache
          // which is used in drag. This ensures it's initialized and synchronized
          // with any changes that might have happened on the page since initialization.

          sortable.refreshPositions();

          sortable._trigger("activate", event, uiSortable);
        }
      });
    },
    stop: function stop(event, ui, draggable) {
      var uiSortable = $.extend({}, ui, {
        item: draggable.element
      });
      draggable.cancelHelperRemoval = false;
      $.each(draggable.sortables, function () {
        var sortable = this;

        if (sortable.isOver) {
          sortable.isOver = 0; // Allow this sortable to handle removing the helper

          draggable.cancelHelperRemoval = true;
          sortable.cancelHelperRemoval = false; // Use _storedCSS To restore properties in the sortable,
          // as this also handles revert (#9675) since the draggable
          // may have modified them in unexpected ways (#8809)

          sortable._storedCSS = {
            position: sortable.placeholder.css("position"),
            top: sortable.placeholder.css("top"),
            left: sortable.placeholder.css("left")
          };

          sortable._mouseStop(event); // Once drag has ended, the sortable should return to using
          // its original helper, not the shared helper from draggable


          sortable.options.helper = sortable.options._helper;
        } else {
          // Prevent this Sortable from removing the helper.
          // However, don't set the draggable to remove the helper
          // either as another connected Sortable may yet handle the removal.
          sortable.cancelHelperRemoval = true;

          sortable._trigger("deactivate", event, uiSortable);
        }
      });
    },
    drag: function drag(event, ui, draggable) {
      $.each(draggable.sortables, function () {
        var innermostIntersecting = false,
            sortable = this; // Copy over variables that sortable's _intersectsWith uses

        sortable.positionAbs = draggable.positionAbs;
        sortable.helperProportions = draggable.helperProportions;
        sortable.offset.click = draggable.offset.click;

        if (sortable._intersectsWith(sortable.containerCache)) {
          innermostIntersecting = true;
          $.each(draggable.sortables, function () {
            // Copy over variables that sortable's _intersectsWith uses
            this.positionAbs = draggable.positionAbs;
            this.helperProportions = draggable.helperProportions;
            this.offset.click = draggable.offset.click;

            if (this !== sortable && this._intersectsWith(this.containerCache) && $.contains(sortable.element[0], this.element[0])) {
              innermostIntersecting = false;
            }

            return innermostIntersecting;
          });
        }

        if (innermostIntersecting) {
          // If it intersects, we use a little isOver variable and set it once,
          // so that the move-in stuff gets fired only once.
          if (!sortable.isOver) {
            sortable.isOver = 1; // Store draggable's parent in case we need to reappend to it later.

            draggable._parent = ui.helper.parent();
            sortable.currentItem = ui.helper.appendTo(sortable.element).data("ui-sortable-item", true); // Store helper option to later restore it

            sortable.options._helper = sortable.options.helper;

            sortable.options.helper = function () {
              return ui.helper[0];
            }; // Fire the start events of the sortable with our passed browser event,
            // and our own helper (so it doesn't create a new one)


            event.target = sortable.currentItem[0];

            sortable._mouseCapture(event, true);

            sortable._mouseStart(event, true, true); // Because the browser event is way off the new appended portlet,
            // modify necessary variables to reflect the changes


            sortable.offset.click.top = draggable.offset.click.top;
            sortable.offset.click.left = draggable.offset.click.left;
            sortable.offset.parent.left -= draggable.offset.parent.left - sortable.offset.parent.left;
            sortable.offset.parent.top -= draggable.offset.parent.top - sortable.offset.parent.top;

            draggable._trigger("toSortable", event); // Inform draggable that the helper is in a valid drop zone,
            // used solely in the revert option to handle "valid/invalid".


            draggable.dropped = sortable.element; // Need to refreshPositions of all sortables in the case that
            // adding to one sortable changes the location of the other sortables (#9675)

            $.each(draggable.sortables, function () {
              this.refreshPositions();
            }); // Hack so receive/update callbacks work (mostly)

            draggable.currentItem = draggable.element;
            sortable.fromOutside = draggable;
          }

          if (sortable.currentItem) {
            sortable._mouseDrag(event); // Copy the sortable's position because the draggable's can potentially reflect
            // a relative position, while sortable is always absolute, which the dragged
            // element has now become. (#8809)


            ui.position = sortable.position;
          }
        } else {
          // If it doesn't intersect with the sortable, and it intersected before,
          // we fake the drag stop of the sortable, but make sure it doesn't remove
          // the helper by using cancelHelperRemoval.
          if (sortable.isOver) {
            sortable.isOver = 0;
            sortable.cancelHelperRemoval = true; // Calling sortable's mouseStop would trigger a revert,
            // so revert must be temporarily false until after mouseStop is called.

            sortable.options._revert = sortable.options.revert;
            sortable.options.revert = false;

            sortable._trigger("out", event, sortable._uiHash(sortable));

            sortable._mouseStop(event, true); // Restore sortable behaviors that were modfied
            // when the draggable entered the sortable area (#9481)


            sortable.options.revert = sortable.options._revert;
            sortable.options.helper = sortable.options._helper;

            if (sortable.placeholder) {
              sortable.placeholder.remove();
            } // Restore and recalculate the draggable's offset considering the sortable
            // may have modified them in unexpected ways. (#8809, #10669)


            ui.helper.appendTo(draggable._parent);

            draggable._refreshOffsets(event);

            ui.position = draggable._generatePosition(event, true);

            draggable._trigger("fromSortable", event); // Inform draggable that the helper is no longer in a valid drop zone


            draggable.dropped = false; // Need to refreshPositions of all sortables just in case removing
            // from one sortable changes the location of other sortables (#9675)

            $.each(draggable.sortables, function () {
              this.refreshPositions();
            });
          }
        }
      });
    }
  });
  $.ui.plugin.add("draggable", "cursor", {
    start: function start(event, ui, instance) {
      var t = $("body"),
          o = instance.options;

      if (t.css("cursor")) {
        o._cursor = t.css("cursor");
      }

      t.css("cursor", o.cursor);
    },
    stop: function stop(event, ui, instance) {
      var o = instance.options;

      if (o._cursor) {
        $("body").css("cursor", o._cursor);
      }
    }
  });
  $.ui.plugin.add("draggable", "opacity", {
    start: function start(event, ui, instance) {
      var t = $(ui.helper),
          o = instance.options;

      if (t.css("opacity")) {
        o._opacity = t.css("opacity");
      }

      t.css("opacity", o.opacity);
    },
    stop: function stop(event, ui, instance) {
      var o = instance.options;

      if (o._opacity) {
        $(ui.helper).css("opacity", o._opacity);
      }
    }
  });
  $.ui.plugin.add("draggable", "scroll", {
    start: function start(event, ui, i) {
      if (!i.scrollParentNotHidden) {
        i.scrollParentNotHidden = i.helper.scrollParent(false);
      }

      if (i.scrollParentNotHidden[0] !== i.document[0] && i.scrollParentNotHidden[0].tagName !== "HTML") {
        i.overflowOffset = i.scrollParentNotHidden.offset();
      }
    },
    drag: function drag(event, ui, i) {
      var o = i.options,
          scrolled = false,
          scrollParent = i.scrollParentNotHidden[0],
          document = i.document[0];

      if (scrollParent !== document && scrollParent.tagName !== "HTML") {
        if (!o.axis || o.axis !== "x") {
          if (i.overflowOffset.top + scrollParent.offsetHeight - event.pageY < o.scrollSensitivity) {
            scrollParent.scrollTop = scrolled = scrollParent.scrollTop + o.scrollSpeed;
          } else if (event.pageY - i.overflowOffset.top < o.scrollSensitivity) {
            scrollParent.scrollTop = scrolled = scrollParent.scrollTop - o.scrollSpeed;
          }
        }

        if (!o.axis || o.axis !== "y") {
          if (i.overflowOffset.left + scrollParent.offsetWidth - event.pageX < o.scrollSensitivity) {
            scrollParent.scrollLeft = scrolled = scrollParent.scrollLeft + o.scrollSpeed;
          } else if (event.pageX - i.overflowOffset.left < o.scrollSensitivity) {
            scrollParent.scrollLeft = scrolled = scrollParent.scrollLeft - o.scrollSpeed;
          }
        }
      } else {
        if (!o.axis || o.axis !== "x") {
          if (event.pageY - $(document).scrollTop() < o.scrollSensitivity) {
            scrolled = $(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
          } else if ($(window).height() - (event.pageY - $(document).scrollTop()) < o.scrollSensitivity) {
            scrolled = $(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
          }
        }

        if (!o.axis || o.axis !== "y") {
          if (event.pageX - $(document).scrollLeft() < o.scrollSensitivity) {
            scrolled = $(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
          } else if ($(window).width() - (event.pageX - $(document).scrollLeft()) < o.scrollSensitivity) {
            scrolled = $(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);
          }
        }
      }

      if (scrolled !== false && $.ui.ddmanager && !o.dropBehaviour) {
        $.ui.ddmanager.prepareOffsets(i, event);
      }
    }
  });
  $.ui.plugin.add("draggable", "snap", {
    start: function start(event, ui, i) {
      var o = i.options;
      i.snapElements = [];
      $(o.snap.constructor !== String ? o.snap.items || ":data(ui-draggable)" : o.snap).each(function () {
        var $t = $(this),
            $o = $t.offset();

        if (this !== i.element[0]) {
          i.snapElements.push({
            item: this,
            width: $t.outerWidth(),
            height: $t.outerHeight(),
            top: $o.top,
            left: $o.left
          });
        }
      });
    },
    drag: function drag(event, ui, inst) {
      var ts,
          bs,
          ls,
          rs,
          l,
          r,
          t,
          b,
          i,
          first,
          o = inst.options,
          d = o.snapTolerance,
          x1 = ui.offset.left,
          x2 = x1 + inst.helperProportions.width,
          y1 = ui.offset.top,
          y2 = y1 + inst.helperProportions.height;

      for (i = inst.snapElements.length - 1; i >= 0; i--) {
        l = inst.snapElements[i].left - inst.margins.left;
        r = l + inst.snapElements[i].width;
        t = inst.snapElements[i].top - inst.margins.top;
        b = t + inst.snapElements[i].height;

        if (x2 < l - d || x1 > r + d || y2 < t - d || y1 > b + d || !$.contains(inst.snapElements[i].item.ownerDocument, inst.snapElements[i].item)) {
          if (inst.snapElements[i].snapping) {
            inst.options.snap.release && inst.options.snap.release.call(inst.element, event, $.extend(inst._uiHash(), {
              snapItem: inst.snapElements[i].item
            }));
          }

          inst.snapElements[i].snapping = false;
          continue;
        }

        if (o.snapMode !== "inner") {
          ts = Math.abs(t - y2) <= d;
          bs = Math.abs(b - y1) <= d;
          ls = Math.abs(l - x2) <= d;
          rs = Math.abs(r - x1) <= d;

          if (ts) {
            ui.position.top = inst._convertPositionTo("relative", {
              top: t - inst.helperProportions.height,
              left: 0
            }).top;
          }

          if (bs) {
            ui.position.top = inst._convertPositionTo("relative", {
              top: b,
              left: 0
            }).top;
          }

          if (ls) {
            ui.position.left = inst._convertPositionTo("relative", {
              top: 0,
              left: l - inst.helperProportions.width
            }).left;
          }

          if (rs) {
            ui.position.left = inst._convertPositionTo("relative", {
              top: 0,
              left: r
            }).left;
          }
        }

        first = ts || bs || ls || rs;

        if (o.snapMode !== "outer") {
          ts = Math.abs(t - y1) <= d;
          bs = Math.abs(b - y2) <= d;
          ls = Math.abs(l - x1) <= d;
          rs = Math.abs(r - x2) <= d;

          if (ts) {
            ui.position.top = inst._convertPositionTo("relative", {
              top: t,
              left: 0
            }).top;
          }

          if (bs) {
            ui.position.top = inst._convertPositionTo("relative", {
              top: b - inst.helperProportions.height,
              left: 0
            }).top;
          }

          if (ls) {
            ui.position.left = inst._convertPositionTo("relative", {
              top: 0,
              left: l
            }).left;
          }

          if (rs) {
            ui.position.left = inst._convertPositionTo("relative", {
              top: 0,
              left: r - inst.helperProportions.width
            }).left;
          }
        }

        if (!inst.snapElements[i].snapping && (ts || bs || ls || rs || first)) {
          inst.options.snap.snap && inst.options.snap.snap.call(inst.element, event, $.extend(inst._uiHash(), {
            snapItem: inst.snapElements[i].item
          }));
        }

        inst.snapElements[i].snapping = ts || bs || ls || rs || first;
      }
    }
  });
  $.ui.plugin.add("draggable", "stack", {
    start: function start(event, ui, instance) {
      var min,
          o = instance.options,
          group = $.makeArray($(o.stack)).sort(function (a, b) {
        return (parseInt($(a).css("zIndex"), 10) || 0) - (parseInt($(b).css("zIndex"), 10) || 0);
      });

      if (!group.length) {
        return;
      }

      min = parseInt($(group[0]).css("zIndex"), 10) || 0;
      $(group).each(function (i) {
        $(this).css("zIndex", min + i);
      });
      this.css("zIndex", min + group.length);
    }
  });
  $.ui.plugin.add("draggable", "zIndex", {
    start: function start(event, ui, instance) {
      var t = $(ui.helper),
          o = instance.options;

      if (t.css("zIndex")) {
        o._zIndex = t.css("zIndex");
      }

      t.css("zIndex", o.zIndex);
    },
    stop: function stop(event, ui, instance) {
      var o = instance.options;

      if (o._zIndex) {
        $(ui.helper).css("zIndex", o._zIndex);
      }
    }
  });
  return $.ui.draggable;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Mouse 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Mouse
//>>group: Widgets
//>>description: Abstracts mouse-based interactions to assist in creating certain widgets.
//>>docs: http://api.jqueryui.com/mouse/
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ../ie */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/ie.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  var mouseHandled = false;
  $(document).on("mouseup", function () {
    mouseHandled = false;
  });
  return $.widget("ui.mouse", {
    version: "1.12.1",
    options: {
      cancel: "input, textarea, button, select, option",
      distance: 1,
      delay: 0
    },
    _mouseInit: function _mouseInit() {
      var that = this;
      this.element.on("mousedown." + this.widgetName, function (event) {
        return that._mouseDown(event);
      }).on("click." + this.widgetName, function (event) {
        if (true === $.data(event.target, that.widgetName + ".preventClickEvent")) {
          $.removeData(event.target, that.widgetName + ".preventClickEvent");
          event.stopImmediatePropagation();
          return false;
        }
      });
      this.started = false;
    },
    // TODO: make sure destroying one instance of mouse doesn't mess with
    // other instances of mouse
    _mouseDestroy: function _mouseDestroy() {
      this.element.off("." + this.widgetName);

      if (this._mouseMoveDelegate) {
        this.document.off("mousemove." + this.widgetName, this._mouseMoveDelegate).off("mouseup." + this.widgetName, this._mouseUpDelegate);
      }
    },
    _mouseDown: function _mouseDown(event) {
      // don't let more than one widget handle mouseStart
      if (mouseHandled) {
        return;
      }

      this._mouseMoved = false; // We may have missed mouseup (out of window)

      this._mouseStarted && this._mouseUp(event);
      this._mouseDownEvent = event;
      var that = this,
          btnIsLeft = event.which === 1,
          // event.target.nodeName works around a bug in IE 8 with
      // disabled inputs (#7620)
      elIsCancel = typeof this.options.cancel === "string" && event.target.nodeName ? $(event.target).closest(this.options.cancel).length : false;

      if (!btnIsLeft || elIsCancel || !this._mouseCapture(event)) {
        return true;
      }

      this.mouseDelayMet = !this.options.delay;

      if (!this.mouseDelayMet) {
        this._mouseDelayTimer = setTimeout(function () {
          that.mouseDelayMet = true;
        }, this.options.delay);
      }

      if (this._mouseDistanceMet(event) && this._mouseDelayMet(event)) {
        this._mouseStarted = this._mouseStart(event) !== false;

        if (!this._mouseStarted) {
          event.preventDefault();
          return true;
        }
      } // Click event may never have fired (Gecko & Opera)


      if (true === $.data(event.target, this.widgetName + ".preventClickEvent")) {
        $.removeData(event.target, this.widgetName + ".preventClickEvent");
      } // These delegates are required to keep context


      this._mouseMoveDelegate = function (event) {
        return that._mouseMove(event);
      };

      this._mouseUpDelegate = function (event) {
        return that._mouseUp(event);
      };

      this.document.on("mousemove." + this.widgetName, this._mouseMoveDelegate).on("mouseup." + this.widgetName, this._mouseUpDelegate);
      event.preventDefault();
      mouseHandled = true;
      return true;
    },
    _mouseMove: function _mouseMove(event) {
      // Only check for mouseups outside the document if you've moved inside the document
      // at least once. This prevents the firing of mouseup in the case of IE<9, which will
      // fire a mousemove event if content is placed under the cursor. See #7778
      // Support: IE <9
      if (this._mouseMoved) {
        // IE mouseup check - mouseup happened when mouse was out of window
        if ($.ui.ie && (!document.documentMode || document.documentMode < 9) && !event.button) {
          return this._mouseUp(event); // Iframe mouseup check - mouseup occurred in another document
        } else if (!event.which) {
          // Support: Safari <=8 - 9
          // Safari sets which to 0 if you press any of the following keys
          // during a drag (#14461)
          if (event.originalEvent.altKey || event.originalEvent.ctrlKey || event.originalEvent.metaKey || event.originalEvent.shiftKey) {
            this.ignoreMissingWhich = true;
          } else if (!this.ignoreMissingWhich) {
            return this._mouseUp(event);
          }
        }
      }

      if (event.which || event.button) {
        this._mouseMoved = true;
      }

      if (this._mouseStarted) {
        this._mouseDrag(event);

        return event.preventDefault();
      }

      if (this._mouseDistanceMet(event) && this._mouseDelayMet(event)) {
        this._mouseStarted = this._mouseStart(this._mouseDownEvent, event) !== false;
        this._mouseStarted ? this._mouseDrag(event) : this._mouseUp(event);
      }

      return !this._mouseStarted;
    },
    _mouseUp: function _mouseUp(event) {
      this.document.off("mousemove." + this.widgetName, this._mouseMoveDelegate).off("mouseup." + this.widgetName, this._mouseUpDelegate);

      if (this._mouseStarted) {
        this._mouseStarted = false;

        if (event.target === this._mouseDownEvent.target) {
          $.data(event.target, this.widgetName + ".preventClickEvent", true);
        }

        this._mouseStop(event);
      }

      if (this._mouseDelayTimer) {
        clearTimeout(this._mouseDelayTimer);
        delete this._mouseDelayTimer;
      }

      this.ignoreMissingWhich = false;
      mouseHandled = false;
      event.preventDefault();
    },
    _mouseDistanceMet: function _mouseDistanceMet(event) {
      return Math.max(Math.abs(this._mouseDownEvent.pageX - event.pageX), Math.abs(this._mouseDownEvent.pageY - event.pageY)) >= this.options.distance;
    },
    _mouseDelayMet: function _mouseDelayMet()
    /* event */
    {
      return this.mouseDelayMet;
    },
    // These are placeholder methods, to be overriden by extending plugin
    _mouseStart: function _mouseStart()
    /* event */
    {},
    _mouseDrag: function _mouseDrag()
    /* event */
    {},
    _mouseStop: function _mouseStop()
    /* event */
    {},
    _mouseCapture: function _mouseCapture()
    /* event */
    {
      return true;
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/resizable.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/resizable.js ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Resizable 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
//>>label: Resizable
//>>group: Interactions
//>>description: Enables resize functionality for any element.
//>>docs: http://api.jqueryui.com/resizable/
//>>demos: http://jqueryui.com/resizable/
//>>css.structure: ../../themes/base/core.css
//>>css.structure: ../../themes/base/resizable.css
//>>css.theme: ../../themes/base/theme.css
(function (factory) {
  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./mouse */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js"), __webpack_require__(/*! ../disable-selection */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/disable-selection.js"), __webpack_require__(/*! ../plugin */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  $.widget("ui.resizable", $.ui.mouse, {
    version: "1.12.1",
    widgetEventPrefix: "resize",
    options: {
      alsoResize: false,
      animate: false,
      animateDuration: "slow",
      animateEasing: "swing",
      aspectRatio: false,
      autoHide: false,
      classes: {
        "ui-resizable-se": "ui-icon ui-icon-gripsmall-diagonal-se"
      },
      containment: false,
      ghost: false,
      grid: false,
      handles: "e,s,se",
      helper: false,
      maxHeight: null,
      maxWidth: null,
      minHeight: 10,
      minWidth: 10,
      // See #7960
      zIndex: 90,
      // Callbacks
      resize: null,
      start: null,
      stop: null
    },
    _num: function _num(value) {
      return parseFloat(value) || 0;
    },
    _isNumber: function _isNumber(value) {
      return !isNaN(parseFloat(value));
    },
    _hasScroll: function _hasScroll(el, a) {
      if ($(el).css("overflow") === "hidden") {
        return false;
      }

      var scroll = a && a === "left" ? "scrollLeft" : "scrollTop",
          has = false;

      if (el[scroll] > 0) {
        return true;
      } // TODO: determine which cases actually cause this to happen
      // if the element doesn't have the scroll set, see if it's possible to
      // set the scroll


      el[scroll] = 1;
      has = el[scroll] > 0;
      el[scroll] = 0;
      return has;
    },
    _create: function _create() {
      var margins,
          o = this.options,
          that = this;

      this._addClass("ui-resizable");

      $.extend(this, {
        _aspectRatio: !!o.aspectRatio,
        aspectRatio: o.aspectRatio,
        originalElement: this.element,
        _proportionallyResizeElements: [],
        _helper: o.helper || o.ghost || o.animate ? o.helper || "ui-resizable-helper" : null
      }); // Wrap the element if it cannot hold child nodes

      if (this.element[0].nodeName.match(/^(canvas|textarea|input|select|button|img)$/i)) {
        this.element.wrap($("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({
          position: this.element.css("position"),
          width: this.element.outerWidth(),
          height: this.element.outerHeight(),
          top: this.element.css("top"),
          left: this.element.css("left")
        }));
        this.element = this.element.parent().data("ui-resizable", this.element.resizable("instance"));
        this.elementIsWrapper = true;
        margins = {
          marginTop: this.originalElement.css("marginTop"),
          marginRight: this.originalElement.css("marginRight"),
          marginBottom: this.originalElement.css("marginBottom"),
          marginLeft: this.originalElement.css("marginLeft")
        };
        this.element.css(margins);
        this.originalElement.css("margin", 0); // support: Safari
        // Prevent Safari textarea resize

        this.originalResizeStyle = this.originalElement.css("resize");
        this.originalElement.css("resize", "none");

        this._proportionallyResizeElements.push(this.originalElement.css({
          position: "static",
          zoom: 1,
          display: "block"
        })); // Support: IE9
        // avoid IE jump (hard set the margin)


        this.originalElement.css(margins);

        this._proportionallyResize();
      }

      this._setupHandles();

      if (o.autoHide) {
        $(this.element).on("mouseenter", function () {
          if (o.disabled) {
            return;
          }

          that._removeClass("ui-resizable-autohide");

          that._handles.show();
        }).on("mouseleave", function () {
          if (o.disabled) {
            return;
          }

          if (!that.resizing) {
            that._addClass("ui-resizable-autohide");

            that._handles.hide();
          }
        });
      }

      this._mouseInit();
    },
    _destroy: function _destroy() {
      this._mouseDestroy();

      var wrapper,
          _destroy = function _destroy(exp) {
        $(exp).removeData("resizable").removeData("ui-resizable").off(".resizable").find(".ui-resizable-handle").remove();
      }; // TODO: Unwrap at same DOM position


      if (this.elementIsWrapper) {
        _destroy(this.element);

        wrapper = this.element;
        this.originalElement.css({
          position: wrapper.css("position"),
          width: wrapper.outerWidth(),
          height: wrapper.outerHeight(),
          top: wrapper.css("top"),
          left: wrapper.css("left")
        }).insertAfter(wrapper);
        wrapper.remove();
      }

      this.originalElement.css("resize", this.originalResizeStyle);

      _destroy(this.originalElement);

      return this;
    },
    _setOption: function _setOption(key, value) {
      this._super(key, value);

      switch (key) {
        case "handles":
          this._removeHandles();

          this._setupHandles();

          break;

        default:
          break;
      }
    },
    _setupHandles: function _setupHandles() {
      var o = this.options,
          handle,
          i,
          n,
          hname,
          axis,
          that = this;
      this.handles = o.handles || (!$(".ui-resizable-handle", this.element).length ? "e,s,se" : {
        n: ".ui-resizable-n",
        e: ".ui-resizable-e",
        s: ".ui-resizable-s",
        w: ".ui-resizable-w",
        se: ".ui-resizable-se",
        sw: ".ui-resizable-sw",
        ne: ".ui-resizable-ne",
        nw: ".ui-resizable-nw"
      });
      this._handles = $();

      if (this.handles.constructor === String) {
        if (this.handles === "all") {
          this.handles = "n,e,s,w,se,sw,ne,nw";
        }

        n = this.handles.split(",");
        this.handles = {};

        for (i = 0; i < n.length; i++) {
          handle = $.trim(n[i]);
          hname = "ui-resizable-" + handle;
          axis = $("<div>");

          this._addClass(axis, "ui-resizable-handle " + hname);

          axis.css({
            zIndex: o.zIndex
          });
          this.handles[handle] = ".ui-resizable-" + handle;
          this.element.append(axis);
        }
      }

      this._renderAxis = function (target) {
        var i, axis, padPos, padWrapper;
        target = target || this.element;

        for (i in this.handles) {
          if (this.handles[i].constructor === String) {
            this.handles[i] = this.element.children(this.handles[i]).first().show();
          } else if (this.handles[i].jquery || this.handles[i].nodeType) {
            this.handles[i] = $(this.handles[i]);

            this._on(this.handles[i], {
              "mousedown": that._mouseDown
            });
          }

          if (this.elementIsWrapper && this.originalElement[0].nodeName.match(/^(textarea|input|select|button)$/i)) {
            axis = $(this.handles[i], this.element);
            padWrapper = /sw|ne|nw|se|n|s/.test(i) ? axis.outerHeight() : axis.outerWidth();
            padPos = ["padding", /ne|nw|n/.test(i) ? "Top" : /se|sw|s/.test(i) ? "Bottom" : /^e$/.test(i) ? "Right" : "Left"].join("");
            target.css(padPos, padWrapper);

            this._proportionallyResize();
          }

          this._handles = this._handles.add(this.handles[i]);
        }
      }; // TODO: make renderAxis a prototype function


      this._renderAxis(this.element);

      this._handles = this._handles.add(this.element.find(".ui-resizable-handle"));

      this._handles.disableSelection();

      this._handles.on("mouseover", function () {
        if (!that.resizing) {
          if (this.className) {
            axis = this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);
          }

          that.axis = axis && axis[1] ? axis[1] : "se";
        }
      });

      if (o.autoHide) {
        this._handles.hide();

        this._addClass("ui-resizable-autohide");
      }
    },
    _removeHandles: function _removeHandles() {
      this._handles.remove();
    },
    _mouseCapture: function _mouseCapture(event) {
      var i,
          handle,
          capture = false;

      for (i in this.handles) {
        handle = $(this.handles[i])[0];

        if (handle === event.target || $.contains(handle, event.target)) {
          capture = true;
        }
      }

      return !this.options.disabled && capture;
    },
    _mouseStart: function _mouseStart(event) {
      var curleft,
          curtop,
          cursor,
          o = this.options,
          el = this.element;
      this.resizing = true;

      this._renderProxy();

      curleft = this._num(this.helper.css("left"));
      curtop = this._num(this.helper.css("top"));

      if (o.containment) {
        curleft += $(o.containment).scrollLeft() || 0;
        curtop += $(o.containment).scrollTop() || 0;
      }

      this.offset = this.helper.offset();
      this.position = {
        left: curleft,
        top: curtop
      };
      this.size = this._helper ? {
        width: this.helper.width(),
        height: this.helper.height()
      } : {
        width: el.width(),
        height: el.height()
      };
      this.originalSize = this._helper ? {
        width: el.outerWidth(),
        height: el.outerHeight()
      } : {
        width: el.width(),
        height: el.height()
      };
      this.sizeDiff = {
        width: el.outerWidth() - el.width(),
        height: el.outerHeight() - el.height()
      };
      this.originalPosition = {
        left: curleft,
        top: curtop
      };
      this.originalMousePosition = {
        left: event.pageX,
        top: event.pageY
      };
      this.aspectRatio = typeof o.aspectRatio === "number" ? o.aspectRatio : this.originalSize.width / this.originalSize.height || 1;
      cursor = $(".ui-resizable-" + this.axis).css("cursor");
      $("body").css("cursor", cursor === "auto" ? this.axis + "-resize" : cursor);

      this._addClass("ui-resizable-resizing");

      this._propagate("start", event);

      return true;
    },
    _mouseDrag: function _mouseDrag(event) {
      var data,
          props,
          smp = this.originalMousePosition,
          a = this.axis,
          dx = event.pageX - smp.left || 0,
          dy = event.pageY - smp.top || 0,
          trigger = this._change[a];

      this._updatePrevProperties();

      if (!trigger) {
        return false;
      }

      data = trigger.apply(this, [event, dx, dy]);

      this._updateVirtualBoundaries(event.shiftKey);

      if (this._aspectRatio || event.shiftKey) {
        data = this._updateRatio(data, event);
      }

      data = this._respectSize(data, event);

      this._updateCache(data);

      this._propagate("resize", event);

      props = this._applyChanges();

      if (!this._helper && this._proportionallyResizeElements.length) {
        this._proportionallyResize();
      }

      if (!$.isEmptyObject(props)) {
        this._updatePrevProperties();

        this._trigger("resize", event, this.ui());

        this._applyChanges();
      }

      return false;
    },
    _mouseStop: function _mouseStop(event) {
      this.resizing = false;
      var pr,
          ista,
          soffseth,
          soffsetw,
          s,
          left,
          top,
          o = this.options,
          that = this;

      if (this._helper) {
        pr = this._proportionallyResizeElements;
        ista = pr.length && /textarea/i.test(pr[0].nodeName);
        soffseth = ista && this._hasScroll(pr[0], "left") ? 0 : that.sizeDiff.height;
        soffsetw = ista ? 0 : that.sizeDiff.width;
        s = {
          width: that.helper.width() - soffsetw,
          height: that.helper.height() - soffseth
        };
        left = parseFloat(that.element.css("left")) + (that.position.left - that.originalPosition.left) || null;
        top = parseFloat(that.element.css("top")) + (that.position.top - that.originalPosition.top) || null;

        if (!o.animate) {
          this.element.css($.extend(s, {
            top: top,
            left: left
          }));
        }

        that.helper.height(that.size.height);
        that.helper.width(that.size.width);

        if (this._helper && !o.animate) {
          this._proportionallyResize();
        }
      }

      $("body").css("cursor", "auto");

      this._removeClass("ui-resizable-resizing");

      this._propagate("stop", event);

      if (this._helper) {
        this.helper.remove();
      }

      return false;
    },
    _updatePrevProperties: function _updatePrevProperties() {
      this.prevPosition = {
        top: this.position.top,
        left: this.position.left
      };
      this.prevSize = {
        width: this.size.width,
        height: this.size.height
      };
    },
    _applyChanges: function _applyChanges() {
      var props = {};

      if (this.position.top !== this.prevPosition.top) {
        props.top = this.position.top + "px";
      }

      if (this.position.left !== this.prevPosition.left) {
        props.left = this.position.left + "px";
      }

      if (this.size.width !== this.prevSize.width) {
        props.width = this.size.width + "px";
      }

      if (this.size.height !== this.prevSize.height) {
        props.height = this.size.height + "px";
      }

      this.helper.css(props);
      return props;
    },
    _updateVirtualBoundaries: function _updateVirtualBoundaries(forceAspectRatio) {
      var pMinWidth,
          pMaxWidth,
          pMinHeight,
          pMaxHeight,
          b,
          o = this.options;
      b = {
        minWidth: this._isNumber(o.minWidth) ? o.minWidth : 0,
        maxWidth: this._isNumber(o.maxWidth) ? o.maxWidth : Infinity,
        minHeight: this._isNumber(o.minHeight) ? o.minHeight : 0,
        maxHeight: this._isNumber(o.maxHeight) ? o.maxHeight : Infinity
      };

      if (this._aspectRatio || forceAspectRatio) {
        pMinWidth = b.minHeight * this.aspectRatio;
        pMinHeight = b.minWidth / this.aspectRatio;
        pMaxWidth = b.maxHeight * this.aspectRatio;
        pMaxHeight = b.maxWidth / this.aspectRatio;

        if (pMinWidth > b.minWidth) {
          b.minWidth = pMinWidth;
        }

        if (pMinHeight > b.minHeight) {
          b.minHeight = pMinHeight;
        }

        if (pMaxWidth < b.maxWidth) {
          b.maxWidth = pMaxWidth;
        }

        if (pMaxHeight < b.maxHeight) {
          b.maxHeight = pMaxHeight;
        }
      }

      this._vBoundaries = b;
    },
    _updateCache: function _updateCache(data) {
      this.offset = this.helper.offset();

      if (this._isNumber(data.left)) {
        this.position.left = data.left;
      }

      if (this._isNumber(data.top)) {
        this.position.top = data.top;
      }

      if (this._isNumber(data.height)) {
        this.size.height = data.height;
      }

      if (this._isNumber(data.width)) {
        this.size.width = data.width;
      }
    },
    _updateRatio: function _updateRatio(data) {
      var cpos = this.position,
          csize = this.size,
          a = this.axis;

      if (this._isNumber(data.height)) {
        data.width = data.height * this.aspectRatio;
      } else if (this._isNumber(data.width)) {
        data.height = data.width / this.aspectRatio;
      }

      if (a === "sw") {
        data.left = cpos.left + (csize.width - data.width);
        data.top = null;
      }

      if (a === "nw") {
        data.top = cpos.top + (csize.height - data.height);
        data.left = cpos.left + (csize.width - data.width);
      }

      return data;
    },
    _respectSize: function _respectSize(data) {
      var o = this._vBoundaries,
          a = this.axis,
          ismaxw = this._isNumber(data.width) && o.maxWidth && o.maxWidth < data.width,
          ismaxh = this._isNumber(data.height) && o.maxHeight && o.maxHeight < data.height,
          isminw = this._isNumber(data.width) && o.minWidth && o.minWidth > data.width,
          isminh = this._isNumber(data.height) && o.minHeight && o.minHeight > data.height,
          dw = this.originalPosition.left + this.originalSize.width,
          dh = this.originalPosition.top + this.originalSize.height,
          cw = /sw|nw|w/.test(a),
          ch = /nw|ne|n/.test(a);

      if (isminw) {
        data.width = o.minWidth;
      }

      if (isminh) {
        data.height = o.minHeight;
      }

      if (ismaxw) {
        data.width = o.maxWidth;
      }

      if (ismaxh) {
        data.height = o.maxHeight;
      }

      if (isminw && cw) {
        data.left = dw - o.minWidth;
      }

      if (ismaxw && cw) {
        data.left = dw - o.maxWidth;
      }

      if (isminh && ch) {
        data.top = dh - o.minHeight;
      }

      if (ismaxh && ch) {
        data.top = dh - o.maxHeight;
      } // Fixing jump error on top/left - bug #2330


      if (!data.width && !data.height && !data.left && data.top) {
        data.top = null;
      } else if (!data.width && !data.height && !data.top && data.left) {
        data.left = null;
      }

      return data;
    },
    _getPaddingPlusBorderDimensions: function _getPaddingPlusBorderDimensions(element) {
      var i = 0,
          widths = [],
          borders = [element.css("borderTopWidth"), element.css("borderRightWidth"), element.css("borderBottomWidth"), element.css("borderLeftWidth")],
          paddings = [element.css("paddingTop"), element.css("paddingRight"), element.css("paddingBottom"), element.css("paddingLeft")];

      for (; i < 4; i++) {
        widths[i] = parseFloat(borders[i]) || 0;
        widths[i] += parseFloat(paddings[i]) || 0;
      }

      return {
        height: widths[0] + widths[2],
        width: widths[1] + widths[3]
      };
    },
    _proportionallyResize: function _proportionallyResize() {
      if (!this._proportionallyResizeElements.length) {
        return;
      }

      var prel,
          i = 0,
          element = this.helper || this.element;

      for (; i < this._proportionallyResizeElements.length; i++) {
        prel = this._proportionallyResizeElements[i]; // TODO: Seems like a bug to cache this.outerDimensions
        // considering that we are in a loop.

        if (!this.outerDimensions) {
          this.outerDimensions = this._getPaddingPlusBorderDimensions(prel);
        }

        prel.css({
          height: element.height() - this.outerDimensions.height || 0,
          width: element.width() - this.outerDimensions.width || 0
        });
      }
    },
    _renderProxy: function _renderProxy() {
      var el = this.element,
          o = this.options;
      this.elementOffset = el.offset();

      if (this._helper) {
        this.helper = this.helper || $("<div style='overflow:hidden;'></div>");

        this._addClass(this.helper, this._helper);

        this.helper.css({
          width: this.element.outerWidth(),
          height: this.element.outerHeight(),
          position: "absolute",
          left: this.elementOffset.left + "px",
          top: this.elementOffset.top + "px",
          zIndex: ++o.zIndex //TODO: Don't modify option

        });
        this.helper.appendTo("body").disableSelection();
      } else {
        this.helper = this.element;
      }
    },
    _change: {
      e: function e(event, dx) {
        return {
          width: this.originalSize.width + dx
        };
      },
      w: function w(event, dx) {
        var cs = this.originalSize,
            sp = this.originalPosition;
        return {
          left: sp.left + dx,
          width: cs.width - dx
        };
      },
      n: function n(event, dx, dy) {
        var cs = this.originalSize,
            sp = this.originalPosition;
        return {
          top: sp.top + dy,
          height: cs.height - dy
        };
      },
      s: function s(event, dx, dy) {
        return {
          height: this.originalSize.height + dy
        };
      },
      se: function se(event, dx, dy) {
        return $.extend(this._change.s.apply(this, arguments), this._change.e.apply(this, [event, dx, dy]));
      },
      sw: function sw(event, dx, dy) {
        return $.extend(this._change.s.apply(this, arguments), this._change.w.apply(this, [event, dx, dy]));
      },
      ne: function ne(event, dx, dy) {
        return $.extend(this._change.n.apply(this, arguments), this._change.e.apply(this, [event, dx, dy]));
      },
      nw: function nw(event, dx, dy) {
        return $.extend(this._change.n.apply(this, arguments), this._change.w.apply(this, [event, dx, dy]));
      }
    },
    _propagate: function _propagate(n, event) {
      $.ui.plugin.call(this, n, [event, this.ui()]);
      n !== "resize" && this._trigger(n, event, this.ui());
    },
    plugins: {},
    ui: function ui() {
      return {
        originalElement: this.originalElement,
        element: this.element,
        helper: this.helper,
        position: this.position,
        size: this.size,
        originalSize: this.originalSize,
        originalPosition: this.originalPosition
      };
    }
  });
  /*
   * Resizable Extensions
   */

  $.ui.plugin.add("resizable", "animate", {
    stop: function stop(event) {
      var that = $(this).resizable("instance"),
          o = that.options,
          pr = that._proportionallyResizeElements,
          ista = pr.length && /textarea/i.test(pr[0].nodeName),
          soffseth = ista && that._hasScroll(pr[0], "left") ? 0 : that.sizeDiff.height,
          soffsetw = ista ? 0 : that.sizeDiff.width,
          style = {
        width: that.size.width - soffsetw,
        height: that.size.height - soffseth
      },
          left = parseFloat(that.element.css("left")) + (that.position.left - that.originalPosition.left) || null,
          top = parseFloat(that.element.css("top")) + (that.position.top - that.originalPosition.top) || null;
      that.element.animate($.extend(style, top && left ? {
        top: top,
        left: left
      } : {}), {
        duration: o.animateDuration,
        easing: o.animateEasing,
        step: function step() {
          var data = {
            width: parseFloat(that.element.css("width")),
            height: parseFloat(that.element.css("height")),
            top: parseFloat(that.element.css("top")),
            left: parseFloat(that.element.css("left"))
          };

          if (pr && pr.length) {
            $(pr[0]).css({
              width: data.width,
              height: data.height
            });
          } // Propagating resize, and updating values for each animation step


          that._updateCache(data);

          that._propagate("resize", event);
        }
      });
    }
  });
  $.ui.plugin.add("resizable", "containment", {
    start: function start() {
      var element,
          p,
          co,
          ch,
          cw,
          width,
          height,
          that = $(this).resizable("instance"),
          o = that.options,
          el = that.element,
          oc = o.containment,
          ce = oc instanceof $ ? oc.get(0) : /parent/.test(oc) ? el.parent().get(0) : oc;

      if (!ce) {
        return;
      }

      that.containerElement = $(ce);

      if (/document/.test(oc) || oc === document) {
        that.containerOffset = {
          left: 0,
          top: 0
        };
        that.containerPosition = {
          left: 0,
          top: 0
        };
        that.parentData = {
          element: $(document),
          left: 0,
          top: 0,
          width: $(document).width(),
          height: $(document).height() || document.body.parentNode.scrollHeight
        };
      } else {
        element = $(ce);
        p = [];
        $(["Top", "Right", "Left", "Bottom"]).each(function (i, name) {
          p[i] = that._num(element.css("padding" + name));
        });
        that.containerOffset = element.offset();
        that.containerPosition = element.position();
        that.containerSize = {
          height: element.innerHeight() - p[3],
          width: element.innerWidth() - p[1]
        };
        co = that.containerOffset;
        ch = that.containerSize.height;
        cw = that.containerSize.width;
        width = that._hasScroll(ce, "left") ? ce.scrollWidth : cw;
        height = that._hasScroll(ce) ? ce.scrollHeight : ch;
        that.parentData = {
          element: ce,
          left: co.left,
          top: co.top,
          width: width,
          height: height
        };
      }
    },
    resize: function resize(event) {
      var woset,
          hoset,
          isParent,
          isOffsetRelative,
          that = $(this).resizable("instance"),
          o = that.options,
          co = that.containerOffset,
          cp = that.position,
          pRatio = that._aspectRatio || event.shiftKey,
          cop = {
        top: 0,
        left: 0
      },
          ce = that.containerElement,
          continueResize = true;

      if (ce[0] !== document && /static/.test(ce.css("position"))) {
        cop = co;
      }

      if (cp.left < (that._helper ? co.left : 0)) {
        that.size.width = that.size.width + (that._helper ? that.position.left - co.left : that.position.left - cop.left);

        if (pRatio) {
          that.size.height = that.size.width / that.aspectRatio;
          continueResize = false;
        }

        that.position.left = o.helper ? co.left : 0;
      }

      if (cp.top < (that._helper ? co.top : 0)) {
        that.size.height = that.size.height + (that._helper ? that.position.top - co.top : that.position.top);

        if (pRatio) {
          that.size.width = that.size.height * that.aspectRatio;
          continueResize = false;
        }

        that.position.top = that._helper ? co.top : 0;
      }

      isParent = that.containerElement.get(0) === that.element.parent().get(0);
      isOffsetRelative = /relative|absolute/.test(that.containerElement.css("position"));

      if (isParent && isOffsetRelative) {
        that.offset.left = that.parentData.left + that.position.left;
        that.offset.top = that.parentData.top + that.position.top;
      } else {
        that.offset.left = that.element.offset().left;
        that.offset.top = that.element.offset().top;
      }

      woset = Math.abs(that.sizeDiff.width + (that._helper ? that.offset.left - cop.left : that.offset.left - co.left));
      hoset = Math.abs(that.sizeDiff.height + (that._helper ? that.offset.top - cop.top : that.offset.top - co.top));

      if (woset + that.size.width >= that.parentData.width) {
        that.size.width = that.parentData.width - woset;

        if (pRatio) {
          that.size.height = that.size.width / that.aspectRatio;
          continueResize = false;
        }
      }

      if (hoset + that.size.height >= that.parentData.height) {
        that.size.height = that.parentData.height - hoset;

        if (pRatio) {
          that.size.width = that.size.height * that.aspectRatio;
          continueResize = false;
        }
      }

      if (!continueResize) {
        that.position.left = that.prevPosition.left;
        that.position.top = that.prevPosition.top;
        that.size.width = that.prevSize.width;
        that.size.height = that.prevSize.height;
      }
    },
    stop: function stop() {
      var that = $(this).resizable("instance"),
          o = that.options,
          co = that.containerOffset,
          cop = that.containerPosition,
          ce = that.containerElement,
          helper = $(that.helper),
          ho = helper.offset(),
          w = helper.outerWidth() - that.sizeDiff.width,
          h = helper.outerHeight() - that.sizeDiff.height;

      if (that._helper && !o.animate && /relative/.test(ce.css("position"))) {
        $(this).css({
          left: ho.left - cop.left - co.left,
          width: w,
          height: h
        });
      }

      if (that._helper && !o.animate && /static/.test(ce.css("position"))) {
        $(this).css({
          left: ho.left - cop.left - co.left,
          width: w,
          height: h
        });
      }
    }
  });
  $.ui.plugin.add("resizable", "alsoResize", {
    start: function start() {
      var that = $(this).resizable("instance"),
          o = that.options;
      $(o.alsoResize).each(function () {
        var el = $(this);
        el.data("ui-resizable-alsoresize", {
          width: parseFloat(el.width()),
          height: parseFloat(el.height()),
          left: parseFloat(el.css("left")),
          top: parseFloat(el.css("top"))
        });
      });
    },
    resize: function resize(event, ui) {
      var that = $(this).resizable("instance"),
          o = that.options,
          os = that.originalSize,
          op = that.originalPosition,
          delta = {
        height: that.size.height - os.height || 0,
        width: that.size.width - os.width || 0,
        top: that.position.top - op.top || 0,
        left: that.position.left - op.left || 0
      };
      $(o.alsoResize).each(function () {
        var el = $(this),
            start = $(this).data("ui-resizable-alsoresize"),
            style = {},
            css = el.parents(ui.originalElement[0]).length ? ["width", "height"] : ["width", "height", "top", "left"];
        $.each(css, function (i, prop) {
          var sum = (start[prop] || 0) + (delta[prop] || 0);

          if (sum && sum >= 0) {
            style[prop] = sum || null;
          }
        });
        el.css(style);
      });
    },
    stop: function stop() {
      $(this).removeData("ui-resizable-alsoresize");
    }
  });
  $.ui.plugin.add("resizable", "ghost", {
    start: function start() {
      var that = $(this).resizable("instance"),
          cs = that.size;
      that.ghost = that.originalElement.clone();
      that.ghost.css({
        opacity: 0.25,
        display: "block",
        position: "relative",
        height: cs.height,
        width: cs.width,
        margin: 0,
        left: 0,
        top: 0
      });

      that._addClass(that.ghost, "ui-resizable-ghost"); // DEPRECATED
      // TODO: remove after 1.12


      if ($.uiBackCompat !== false && typeof that.options.ghost === "string") {
        // Ghost option
        that.ghost.addClass(this.options.ghost);
      }

      that.ghost.appendTo(that.helper);
    },
    resize: function resize() {
      var that = $(this).resizable("instance");

      if (that.ghost) {
        that.ghost.css({
          position: "relative",
          height: that.size.height,
          width: that.size.width
        });
      }
    },
    stop: function stop() {
      var that = $(this).resizable("instance");

      if (that.ghost && that.helper) {
        that.helper.get(0).removeChild(that.ghost.get(0));
      }
    }
  });
  $.ui.plugin.add("resizable", "grid", {
    resize: function resize() {
      var outerDimensions,
          that = $(this).resizable("instance"),
          o = that.options,
          cs = that.size,
          os = that.originalSize,
          op = that.originalPosition,
          a = that.axis,
          grid = typeof o.grid === "number" ? [o.grid, o.grid] : o.grid,
          gridX = grid[0] || 1,
          gridY = grid[1] || 1,
          ox = Math.round((cs.width - os.width) / gridX) * gridX,
          oy = Math.round((cs.height - os.height) / gridY) * gridY,
          newWidth = os.width + ox,
          newHeight = os.height + oy,
          isMaxWidth = o.maxWidth && o.maxWidth < newWidth,
          isMaxHeight = o.maxHeight && o.maxHeight < newHeight,
          isMinWidth = o.minWidth && o.minWidth > newWidth,
          isMinHeight = o.minHeight && o.minHeight > newHeight;
      o.grid = grid;

      if (isMinWidth) {
        newWidth += gridX;
      }

      if (isMinHeight) {
        newHeight += gridY;
      }

      if (isMaxWidth) {
        newWidth -= gridX;
      }

      if (isMaxHeight) {
        newHeight -= gridY;
      }

      if (/^(se|s|e)$/.test(a)) {
        that.size.width = newWidth;
        that.size.height = newHeight;
      } else if (/^(ne)$/.test(a)) {
        that.size.width = newWidth;
        that.size.height = newHeight;
        that.position.top = op.top - oy;
      } else if (/^(sw)$/.test(a)) {
        that.size.width = newWidth;
        that.size.height = newHeight;
        that.position.left = op.left - ox;
      } else {
        if (newHeight - gridY <= 0 || newWidth - gridX <= 0) {
          outerDimensions = that._getPaddingPlusBorderDimensions(this);
        }

        if (newHeight - gridY > 0) {
          that.size.height = newHeight;
          that.position.top = op.top - oy;
        } else {
          newHeight = gridY - outerDimensions.height;
          that.size.height = newHeight;
          that.position.top = op.top + os.height - newHeight;
        }

        if (newWidth - gridX > 0) {
          that.size.width = newWidth;
          that.position.left = op.left - ox;
        } else {
          newWidth = gridX - outerDimensions.width;
          that.size.width = newWidth;
          that.position.left = op.left + os.width - newWidth;
        }
      }
    }
  });
  return $.ui.resizable;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/nprogress/nprogress.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/nprogress/nprogress.js ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/* NProgress, (c) 2013, 2014 Rico Sta. Cruz - http://ricostacruz.com/nprogress
 * @license MIT */
;

(function (root, factory) {
  if (true) {
    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
				__WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(this, function () {
  var NProgress = {};
  NProgress.version = '0.2.0';
  var Settings = NProgress.settings = {
    minimum: 0.08,
    easing: 'ease',
    positionUsing: '',
    speed: 200,
    trickle: true,
    trickleRate: 0.02,
    trickleSpeed: 800,
    showSpinner: true,
    barSelector: '[role="bar"]',
    spinnerSelector: '[role="spinner"]',
    parent: 'body',
    template: '<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>'
  };
  /**
   * Updates configuration.
   *
   *     NProgress.configure({
   *       minimum: 0.1
   *     });
   */

  NProgress.configure = function (options) {
    var key, value;

    for (key in options) {
      value = options[key];
      if (value !== undefined && options.hasOwnProperty(key)) Settings[key] = value;
    }

    return this;
  };
  /**
   * Last number.
   */


  NProgress.status = null;
  /**
   * Sets the progress bar status, where `n` is a number from `0.0` to `1.0`.
   *
   *     NProgress.set(0.4);
   *     NProgress.set(1.0);
   */

  NProgress.set = function (n) {
    var started = NProgress.isStarted();
    n = clamp(n, Settings.minimum, 1);
    NProgress.status = n === 1 ? null : n;
    var progress = NProgress.render(!started),
        bar = progress.querySelector(Settings.barSelector),
        speed = Settings.speed,
        ease = Settings.easing;
    progress.offsetWidth;
    /* Repaint */

    queue(function (next) {
      // Set positionUsing if it hasn't already been set
      if (Settings.positionUsing === '') Settings.positionUsing = NProgress.getPositioningCSS(); // Add transition

      css(bar, barPositionCSS(n, speed, ease));

      if (n === 1) {
        // Fade out
        css(progress, {
          transition: 'none',
          opacity: 1
        });
        progress.offsetWidth;
        /* Repaint */

        setTimeout(function () {
          css(progress, {
            transition: 'all ' + speed + 'ms linear',
            opacity: 0
          });
          setTimeout(function () {
            NProgress.remove();
            next();
          }, speed);
        }, speed);
      } else {
        setTimeout(next, speed);
      }
    });
    return this;
  };

  NProgress.isStarted = function () {
    return typeof NProgress.status === 'number';
  };
  /**
   * Shows the progress bar.
   * This is the same as setting the status to 0%, except that it doesn't go backwards.
   *
   *     NProgress.start();
   *
   */


  NProgress.start = function () {
    if (!NProgress.status) NProgress.set(0);

    var work = function work() {
      setTimeout(function () {
        if (!NProgress.status) return;
        NProgress.trickle();
        work();
      }, Settings.trickleSpeed);
    };

    if (Settings.trickle) work();
    return this;
  };
  /**
   * Hides the progress bar.
   * This is the *sort of* the same as setting the status to 100%, with the
   * difference being `done()` makes some placebo effect of some realistic motion.
   *
   *     NProgress.done();
   *
   * If `true` is passed, it will show the progress bar even if its hidden.
   *
   *     NProgress.done(true);
   */


  NProgress.done = function (force) {
    if (!force && !NProgress.status) return this;
    return NProgress.inc(0.3 + 0.5 * Math.random()).set(1);
  };
  /**
   * Increments by a random amount.
   */


  NProgress.inc = function (amount) {
    var n = NProgress.status;

    if (!n) {
      return NProgress.start();
    } else {
      if (typeof amount !== 'number') {
        amount = (1 - n) * clamp(Math.random() * n, 0.1, 0.95);
      }

      n = clamp(n + amount, 0, 0.994);
      return NProgress.set(n);
    }
  };

  NProgress.trickle = function () {
    return NProgress.inc(Math.random() * Settings.trickleRate);
  };
  /**
   * Waits for all supplied jQuery promises and
   * increases the progress as the promises resolve.
   *
   * @param $promise jQUery Promise
   */


  (function () {
    var initial = 0,
        current = 0;

    NProgress.promise = function ($promise) {
      if (!$promise || $promise.state() === "resolved") {
        return this;
      }

      if (current === 0) {
        NProgress.start();
      }

      initial++;
      current++;
      $promise.always(function () {
        current--;

        if (current === 0) {
          initial = 0;
          NProgress.done();
        } else {
          NProgress.set((initial - current) / initial);
        }
      });
      return this;
    };
  })();
  /**
   * (Internal) renders the progress bar markup based on the `template`
   * setting.
   */


  NProgress.render = function (fromStart) {
    if (NProgress.isRendered()) return document.getElementById('nprogress');
    addClass(document.documentElement, 'nprogress-busy');
    var progress = document.createElement('div');
    progress.id = 'nprogress';
    progress.innerHTML = Settings.template;
    var bar = progress.querySelector(Settings.barSelector),
        perc = fromStart ? '-100' : toBarPerc(NProgress.status || 0),
        parent = document.querySelector(Settings.parent),
        spinner;
    css(bar, {
      transition: 'all 0 linear',
      transform: 'translate3d(' + perc + '%,0,0)'
    });

    if (!Settings.showSpinner) {
      spinner = progress.querySelector(Settings.spinnerSelector);
      spinner && removeElement(spinner);
    }

    if (parent != document.body) {
      addClass(parent, 'nprogress-custom-parent');
    }

    parent.appendChild(progress);
    return progress;
  };
  /**
   * Removes the element. Opposite of render().
   */


  NProgress.remove = function () {
    removeClass(document.documentElement, 'nprogress-busy');
    removeClass(document.querySelector(Settings.parent), 'nprogress-custom-parent');
    var progress = document.getElementById('nprogress');
    progress && removeElement(progress);
  };
  /**
   * Checks if the progress bar is rendered.
   */


  NProgress.isRendered = function () {
    return !!document.getElementById('nprogress');
  };
  /**
   * Determine which positioning CSS rule to use.
   */


  NProgress.getPositioningCSS = function () {
    // Sniff on document.body.style
    var bodyStyle = document.body.style; // Sniff prefixes

    var vendorPrefix = 'WebkitTransform' in bodyStyle ? 'Webkit' : 'MozTransform' in bodyStyle ? 'Moz' : 'msTransform' in bodyStyle ? 'ms' : 'OTransform' in bodyStyle ? 'O' : '';

    if (vendorPrefix + 'Perspective' in bodyStyle) {
      // Modern browsers with 3D support, e.g. Webkit, IE10
      return 'translate3d';
    } else if (vendorPrefix + 'Transform' in bodyStyle) {
      // Browsers without 3D support, e.g. IE9
      return 'translate';
    } else {
      // Browsers without translate() support, e.g. IE7-8
      return 'margin';
    }
  };
  /**
   * Helpers
   */


  function clamp(n, min, max) {
    if (n < min) return min;
    if (n > max) return max;
    return n;
  }
  /**
   * (Internal) converts a percentage (`0..1`) to a bar translateX
   * percentage (`-100%..0%`).
   */


  function toBarPerc(n) {
    return (-1 + n) * 100;
  }
  /**
   * (Internal) returns the correct CSS for changing the bar's
   * position given an n percentage, and speed and ease from Settings
   */


  function barPositionCSS(n, speed, ease) {
    var barCSS;

    if (Settings.positionUsing === 'translate3d') {
      barCSS = {
        transform: 'translate3d(' + toBarPerc(n) + '%,0,0)'
      };
    } else if (Settings.positionUsing === 'translate') {
      barCSS = {
        transform: 'translate(' + toBarPerc(n) + '%,0)'
      };
    } else {
      barCSS = {
        'margin-left': toBarPerc(n) + '%'
      };
    }

    barCSS.transition = 'all ' + speed + 'ms ' + ease;
    return barCSS;
  }
  /**
   * (Internal) Queues a function to be executed.
   */


  var queue = function () {
    var pending = [];

    function next() {
      var fn = pending.shift();

      if (fn) {
        fn(next);
      }
    }

    return function (fn) {
      pending.push(fn);
      if (pending.length == 1) next();
    };
  }();
  /**
   * (Internal) Applies css properties to an element, similar to the jQuery 
   * css method.
   *
   * While this helper does assist with vendor prefixed property names, it 
   * does not perform any manipulation of values prior to setting styles.
   */


  var css = function () {
    var cssPrefixes = ['Webkit', 'O', 'Moz', 'ms'],
        cssProps = {};

    function camelCase(string) {
      return string.replace(/^-ms-/, 'ms-').replace(/-([\da-z])/gi, function (match, letter) {
        return letter.toUpperCase();
      });
    }

    function getVendorProp(name) {
      var style = document.body.style;
      if (name in style) return name;
      var i = cssPrefixes.length,
          capName = name.charAt(0).toUpperCase() + name.slice(1),
          vendorName;

      while (i--) {
        vendorName = cssPrefixes[i] + capName;
        if (vendorName in style) return vendorName;
      }

      return name;
    }

    function getStyleProp(name) {
      name = camelCase(name);
      return cssProps[name] || (cssProps[name] = getVendorProp(name));
    }

    function applyCss(element, prop, value) {
      prop = getStyleProp(prop);
      element.style[prop] = value;
    }

    return function (element, properties) {
      var args = arguments,
          prop,
          value;

      if (args.length == 2) {
        for (prop in properties) {
          value = properties[prop];
          if (value !== undefined && properties.hasOwnProperty(prop)) applyCss(element, prop, value);
        }
      } else {
        applyCss(element, args[1], args[2]);
      }
    };
  }();
  /**
   * (Internal) Determines if an element or space separated list of class names contains a class name.
   */


  function hasClass(element, name) {
    var list = typeof element == 'string' ? element : classList(element);
    return list.indexOf(' ' + name + ' ') >= 0;
  }
  /**
   * (Internal) Adds a class to an element.
   */


  function addClass(element, name) {
    var oldList = classList(element),
        newList = oldList + name;
    if (hasClass(oldList, name)) return; // Trim the opening space.

    element.className = newList.substring(1);
  }
  /**
   * (Internal) Removes a class from an element.
   */


  function removeClass(element, name) {
    var oldList = classList(element),
        newList;
    if (!hasClass(element, name)) return; // Replace the class name.

    newList = oldList.replace(' ' + name + ' ', ' '); // Trim the opening and closing spaces.

    element.className = newList.substring(1, newList.length - 1);
  }
  /**
   * (Internal) Gets a space separated list of the class names on the element. 
   * The list is wrapped with a single space on each end to facilitate finding 
   * matches within the list.
   */


  function classList(element) {
    return (' ' + (element.className || '') + ' ').replace(/\s+/gi, ' ');
  }
  /**
   * (Internal) Removes an element from the DOM.
   */


  function removeElement(element) {
    element && element.parentNode && element.parentNode.removeChild(element);
  }

  return NProgress;
});

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

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

/***/ }),

/***/ 12:
/*!*********************************************************************************!*\
  !*** multi ./node_modules/@concretecms/bedrock/assets/documents/js/frontend.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/node_modules/@concretecms/bedrock/assets/documents/js/frontend.js */"./node_modules/@concretecms/bedrock/assets/documents/js/frontend.js");


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });