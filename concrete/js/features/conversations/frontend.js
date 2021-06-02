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

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/events.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/events.js ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* eslint-disable no-new, no-unused-vars, camelcase */

/* global _ */
;

(function (global, $) {
  'use strict';

  global.Concrete = global.Concrete || {};
  global.console = global.console || {};

  global.ConcreteEvent = function (ns) {
    var target = $('<span />');
    var _debug = false;
    var hasGroup = typeof global.console.group === 'function' && typeof global.console.groupEnd === 'function';
    var hasLog = typeof global.console.log === 'function';

    function groupLog(group, value, dontcall) {
      if (hasGroup) {
        global.console.groupCollapsed(group);

        if (!dontcall && typeof value === 'function') {
          value();
        } else {
          global.console.log(value);
        }

        global.console.groupEnd();
      } else if (hasLog) {
        if (!dontcall && typeof value === 'function') {
          global.console.log('Group: "' + group + '"');
          value();
          global.console.log('GroupEnd: "' + group + '"');
        } else {
          global.console.log(group, value);
        }
      }
    }

    function getTarget(given_target) {
      if (!given_target) given_target = target;
      if (!(given_target instanceof $)) given_target = $(given_target);
      if (!given_target.length) given_target = target;
      return given_target;
    }

    var ConcreteEvent = {
      debug: function debug(enabled) {
        if (typeof enabled === 'undefined') {
          return _debug;
        }

        return _debug = !!enabled;
      },
      subscribe: function subscribe(type, handler, target) {
        var old_handler = handler;
        var bound_stack = new Error('EventStack').stack;

        handler = function handler() {
          if (_debug) {
            groupLog('Handler Fired.', function () {
              groupLog('Type', type, true);
              groupLog('Handler', old_handler, true);
              groupLog('Target', target, true);
              groupLog('Bound Stack', bound_stack, true);

              if (typeof global.console.trace === 'function') {
                global.console.trace();
              } else {
                groupLog('Stack', new Error('EventStack').stack);
              }
            });
          }

          old_handler.apply(this, _(arguments).toArray());
        };

        if (_debug) {
          groupLog('Event Subscribed', function () {
            groupLog('Type', type, true);
            groupLog('Handler', old_handler, true);
            groupLog('Target', target, true);

            if (typeof global.console.trace === 'function') {
              global.console.trace();
            } else {
              groupLog('Stack', new Error('EventStack').stack);
            }
          });
        }

        if (type instanceof Array) {
          return _(type).each(function (v) {
            ConcreteEvent.subscribe(v, handler, target);
          });
        }

        getTarget(target).bind(type.toLowerCase(), handler);
        return ConcreteEvent;
      },
      publish: function publish(type, data, target) {
        if (_debug) {
          groupLog('Event Published', function () {
            groupLog('Type', type, true);
            groupLog('Data', data, true);
            groupLog('Target', target, true);

            if (typeof global.console.trace === 'function') {
              global.console.trace();
            } else {
              groupLog('Stack', new Error('EventStack').stack);
            }
          });
        }

        if (type instanceof Array) {
          return _(type).each(function (v) {
            ConcreteEvent.publish(v, data, target);
          });
        }

        getTarget(target).trigger(type.toLowerCase(), data);
        return ConcreteEvent;
      },
      unsubscribe: function unsubscribe(type, secondary_argument, target) {
        var args;

        if (_debug) {
          groupLog('Event Unsubscribed', function () {
            groupLog('Type', type, true);
            groupLog('Secondary Argument', secondary_argument, true);
            groupLog('Target', target, true);

            if (typeof global.console.trace === 'function') {
              global.console.trace();
            } else {
              groupLog('Stack', new Error('EventStack').stack);
            }
          });
        }

        args = [typeof type.toLowerCase === 'function' ? type.toLowerCase() : type];
        if (typeof secondary_argument !== 'undefined') args.push(secondary_argument);
        $.fn.unbind.apply(getTarget(target), args);
        return ConcreteEvent;
      }
    };
    ConcreteEvent.sub = ConcreteEvent.bind = ConcreteEvent.watch = ConcreteEvent.on = ConcreteEvent.subscribe;
    ConcreteEvent.pub = ConcreteEvent.fire = ConcreteEvent.trigger = ConcreteEvent.publish;
    ConcreteEvent.unsub = ConcreteEvent.unbind = ConcreteEvent.unwatch = ConcreteEvent.off = ConcreteEvent.unsubscribe;
    ns.event = ConcreteEvent;
    return ConcreteEvent;
  }(global.Concrete);
})(window, jQuery); // eslint-disable-line semi

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/conversations/js/frontend.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/conversations/js/frontend.js ***!
  \*******************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_conversations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/conversations */ "./node_modules/@concretecms/bedrock/assets/conversations/js/frontend/conversations.js");
/* harmony import */ var _frontend_attachments__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./frontend/attachments */ "./node_modules/@concretecms/bedrock/assets/conversations/js/frontend/attachments.js");



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/conversations/js/frontend/attachments.js":
/*!*******************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/conversations/js/frontend/attachments.js ***!
  \*******************************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var dropzone_dist_dropzone__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! dropzone/dist/dropzone */ "./node_modules/dropzone/dist/dropzone.js");
/* harmony import */ var dropzone_dist_dropzone__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(dropzone_dist_dropzone__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/* eslint no-unused-vars: 0 */


(function (global, $) {
  'use strict';

  var i18n = {
    Too_many_files: 'Too many files',
    Invalid_file_extension: 'Invalid file extension',
    Max_file_size_exceeded: 'Max file size exceeded',
    Error_deleting_attachment: 'Something went wrong while deleting this attachment, please refresh and try again.',
    Confirm_remove_attachment: 'Remove this attachment?' // Please add new translatable strings to the getConversationsJavascript of /concrete/controllers/frontend/assets_localization.php

  };
  var methods = {
    init: function init(options) {
      var obj = options;
      obj.$element.on('click.cnv', 'a[data-toggle=conversation-reply]', function () {
        $('.ccm-conversation-wrapper').concreteConversationAttachments('clearDropzoneQueues');
      });
      obj.$element.on('click.cnv', 'a.attachment-delete', function (event) {
        event.preventDefault();
        $(this).concreteConversationAttachments('attachmentDeleteTrigger', obj);
      });

      if (obj.$editMessageHolder && !obj.$editMessageHolder.find('.dropzone').attr('data-dropzone-applied')) {
        obj.$editMessageHolder.find('.dropzone').not('[data-drozpone-applied="true"]').dropzone({
          // dropzone reply form
          url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/add_file',
          success: function success(file, response) {
            var self = this;
            $(file.previewTemplate).click(function () {
              self.removeFile(file);
              $('input[rel="' + $(this).attr('rel') + '"]').remove();
            });

            if (!response.error) {
              $(this.element).closest('div.ccm-conversation-edit-message').find('form.aux-reply-form').append('<input rel="' + response.timestamp + '" type="hidden" name="attachments[]" value="' + response.id + '" />');
            } else {
              var $form = $('.preview.processing[rel="' + response.timestamp + '"]').closest('form');
              obj.handlePostError($form, [response.error]);
              $('.preview.processing[rel="' + response.timestamp + '"]').remove();
              $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function () {
                $(this).html('');
              });
            }
          },
          accept: function accept(file, done) {
            var errors = [];
            var attachmentCount = this.files.length;

            if (obj.options.maxFiles > 0 && attachmentCount > obj.options.maxFiles) {
              errors.push(i18n.Too_many_files);
            }

            var requiredExtensions = obj.options.fileExtensions.split(',');

            if (file.name.split('.').pop().toLowerCase() && requiredExtensions.indexOf(file.name.split('.').pop().toLowerCase()) == -1 && requiredExtensions != '') {
              errors.push(i18n.Invalid_file_extension);
            }

            if (obj.options.maxFileSize > 0 && file.size > obj.options.maxFileSize * 1000000) {
              errors.push(i18n.Max_file_size_exceeded);
            }

            if (errors.length > 0) {
              var self = this;
              $('input[rel="' + $(file.previewTemplate).attr('rel') + '"]').remove();
              var $form = $(file.previewTemplate).parent('.dropzone');
              self.removeFile(file);
              obj.handlePostError($form, errors);
              $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function () {
                $(this).html('');
              });
              attachmentCount = -1;
              done('error'); // not displayed, just needs to have argument to trigger.
            } else {
              done();
            }
          },
          sending: function sending(file, xhr, formData) {
            $(file.previewTemplate).attr('rel', new Date().getTime());
            formData.append('timestamp', $(file.previewTemplate).attr('rel'));
            formData.append('tag', $(obj.$editMessageHOlder).parent('div').attr('rel'));
            formData.append('fileCount', $(obj.$editMessageHolder).find('[name="attachments[]"]').length);
          },
          init: function init() {
            $(this.element).data('dropzone', this);
          }
        });
      }

      if (obj.$newmessageform.dropzone && !$(obj.$newmessageform).attr('data-dropzone-applied')) {
        // dropzone new message form
        obj.$newmessageform.dropzone({
          accept: function accept(file, done) {
            var errors = [];
            var attachmentCount = this.files.length;

            if (obj.options.maxFiles > 0 && attachmentCount > obj.options.maxFiles) {
              errors.push(i18n.Too_many_files);
            }

            var requiredExtensions = obj.options.fileExtensions.split(',');

            if (file.name.split('.').pop().toLowerCase() && requiredExtensions.indexOf(file.name.split('.').pop().toLowerCase()) == -1 && requiredExtensions != '') {
              errors.push(i18n.Invalid_file_extension);
            }

            if (obj.options.maxFileSize > 0 && file.size > obj.options.maxFileSize * 1000000) {
              errors.push(i18n.Max_file_size_exceeded);
            }

            if (errors.length > 0) {
              var self = this;
              $('input[rel="' + $(file.previewTemplate).attr('rel') + '"]').remove();
              var $form = $(file.previewTemplate).parent('.dropzone');
              self.removeFile(file);
              obj.handlePostError($form, errors);
              $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function () {
                $(this).html('');
              });
              attachmentCount = -1;
              done('error'); // not displayed, just needs to have argument to trigger.
            } else {
              done();
            }
          },
          url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/add_file',
          success: function success(file, response) {
            var self = this;
            $(file.previewTemplate).click(function () {
              $('input[rel="' + $(this).attr('rel') + '"]').remove();
              self.removeFile(file);
            });

            if (!response.error) {
              $('div[rel="' + response.tag + '"] form.main-reply-form').append('<input rel="' + response.timestamp + '" type="hidden" name="attachments[]" value="' + response.id + '" />');
            } else {
              var $form = $('.preview.processing[rel="' + response.timestamp + '"]').closest('form');
              obj.handlePostError($form, [response.error]);
              $('.preview.processing[rel="' + response.timestamp + '"]').remove();
              $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function () {
                $(this).html('');
              });
            }
          },
          sending: function sending(file, xhr, formData) {
            $(file.previewTemplate).attr('rel', new Date().getTime());
            formData.append('timestamp', $(file.previewTemplate).attr('rel'));
            formData.append('tag', $(obj.$newmessageform).parent('div').attr('rel'));
            formData.append('fileCount', this.files.length);
          },
          init: function init() {
            $(this.element).data('dropzone', this);
          }
        });
        $(obj.$newmessageform).attr('data-dropzone-applied', 'true');
      }

      if (!$(obj.$replyholder.find('.dropzone')).attr('data-dropzone-applied')) {
        obj.$replyholder.find('.dropzone').not('[data-drozpone-applied="true"]').dropzone({
          // dropzone reply form
          url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/add_file',
          success: function success(file, response) {
            var self = this;
            $(file.previewTemplate).click(function () {
              self.removeFile(file);
              $('input[rel="' + $(this).attr('rel') + '"]').remove();
            });

            if (!response.error) {
              $(this.element).closest('div.ccm-conversation-add-reply').find('form.aux-reply-form').append('<input rel="' + response.timestamp + '" type="hidden" name="attachments[]" value="' + response.id + '" />');
            } else {
              var $form = $('.preview.processing[rel="' + response.timestamp + '"]').closest('form');
              obj.handlePostError($form, [response.error]);
              $('.preview.processing[rel="' + response.timestamp + '"]').remove();
              $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function () {
                $(this).html('');
              });
            }
          },
          accept: function accept(file, done) {
            var errors = [];
            var attachmentCount = this.files.length;

            if (obj.options.maxFiles > 0 && attachmentCount > obj.options.maxFiles) {
              errors.push(i18n.Too_many_files);
            }

            var requiredExtensions = obj.options.fileExtensions.split(',');

            if (file.name.split('.').pop().toLowerCase() && requiredExtensions.indexOf(file.name.split('.').pop().toLowerCase()) == -1 && requiredExtensions != '') {
              errors.push(i18n.Invalid_file_extension);
            }

            if (obj.options.maxFileSize > 0 && file.size > obj.options.maxFileSize * 1000000) {
              errors.push(i18n.Max_file_size_exceeded);
            }

            if (errors.length > 0) {
              var self = this;
              $('input[rel="' + $(file.previewTemplate).attr('rel') + '"]').remove();
              var $form = $(file.previewTemplate).parent('.dropzone');
              self.removeFile(file);
              obj.handlePostError($form, errors);
              $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function () {
                $(this).html('');
              });
              attachmentCount = -1;
              done('error'); // not displayed, just needs to have argument to trigger.
            } else {
              done();
            }
          },
          sending: function sending(file, xhr, formData) {
            $(file.previewTemplate).attr('rel', new Date().getTime());
            formData.append('timestamp', $(file.previewTemplate).attr('rel'));
            formData.append('tag', $(obj.$newmessageform).parent('div').attr('rel'));
            formData.append('fileCount', $(obj.$replyHolder).find('[name="attachments[]"]').length);
          },
          init: function init() {
            $(this.element).data('dropzone', this);
          }
        });
      }

      $(obj.$replyholder.find('.dropzone')).attr('data-dropzone-applied', 'true');
      return $.each($(this), function (i, obj) {
        $(this).find('.ccm-conversation-attachment-container').each(function () {
          if ($(this).is(':visible')) {
            $(this).toggle();
          }
        });
      });
    },
    attachmentDeleteTrigger: function attachmentDeleteTrigger(options) {
      var obj = options;
      var link = $(this);
      obj.$attachmentdeletetdialog = obj.$attachmentdeleteholder.clone();

      if (obj.$attachmentdeletetdialog.dialog) {
        obj.$attachmentdeletetdialog.dialog({
          modal: true,
          dialogClass: 'ccm-conversation-dialog',
          title: obj.$attachmentdeletetdialog.attr('data-dialog-title'),
          buttons: [{
            text: obj.$attachmentdeleteholder.attr('data-cancel-button-title'),
            "class": 'btn pull-left',
            click: function click() {
              obj.$attachmentdeletetdialog.dialog('close');
            }
          }, {
            text: obj.$attachmentdeleteholder.attr('data-confirm-button-title'),
            "class": 'btn pull-right btn-danger',
            click: function click() {
              $(this).concreteConversationAttachments('deleteAttachment', {
                cnvMessageAttachmentID: link.attr('rel'),
                cnvObj: obj,
                dialogObj: obj.$attachmentdeletetdialog
              });
            }
          }]
        });
      } else {
        if (window.confirm(i18n.Confirm_remove_attachment)) {
          $(this).concreteConversationAttachments('deleteAttachment', {
            cnvMessageAttachmentID: link.attr('rel'),
            cnvObj: obj,
            dialogObj: obj.$attachmentdeletetdialog
          });
        }
      }

      return false;
    },
    clearDropzoneQueues: function clearDropzoneQueues() {
      $('.preview.processing').each(function () {
        // first remove any previous attachments and hide dropzone if it was open.
        $('input[rel="' + $(this).attr('rel') + '"]').remove();
      });
      $('form.dropzone').each(function () {
        var d = $(this).data('dropzone');
        $.each(d.files, function (k, v) {
          d.removeFile(v);
        });
      });
    },
    deleteAttachment: function deleteAttachment(options) {
      var cnvMessageAttachmentID = options.cnvMessageAttachmentID;
      var obj = options.cnvObj;
      var attachmentsDialog = options.dialogObj;
      /* var obj = this;
      obj.publish('conversationBeforeDeleteAttachment',{cnvMessageAttachmentID:cnvMessageAttachmentID}); */

      var formArray = [{
        name: 'cnvMessageAttachmentID',
        value: cnvMessageAttachmentID
      }];
      $.ajax({
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/delete_file',
        success: function success(response) {
          $('p[rel="' + response.attachmentID + '"]').parent('.attachment-container').fadeOut(300, function () {
            $(this).remove();
          });

          if (attachmentsDialog.dialog) {
            attachmentsDialog.dialog('close');
            obj.publish('conversationDeleteAttachment', {
              cnvMessageAttachmentID: cnvMessageAttachmentID
            });
          }
        },
        error: function error(e) {
          obj.publish('conversationDeleteAttachmentError', {
            cnvMessageAttachmentID: cnvMessageAttachmentID,
            error: arguments
          });
          window.alert(i18n.Error_deleting_attachment);
        }
      });
    }
  };

  $.fn.concreteConversationAttachments = function (method) {
    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (_typeof(method) === 'object' || !method) {
      return methods.init.apply(this, arguments);
    } else {
      $.error('Method ' + method + ' does not exist on concreteConversationAttachments');
    }
  };

  $.fn.concreteConversationAttachments.localize = function (dictionary) {
    $.extend(true, i18n, dictionary);
  };
})(window, jQuery); // eslint-disable-line semi

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/conversations/js/frontend/conversations.js":
/*!*********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/conversations/js/frontend/conversations.js ***!
  \*********************************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _cms_js_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../cms/js/events */ "./node_modules/@concretecms/bedrock/assets/cms/js/events.js");
/* harmony import */ var _cms_js_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_cms_js_events__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var underscore__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! underscore */ "./node_modules/underscore/modules/index-all.js");


window._ = underscore__WEBPACK_IMPORTED_MODULE_1__["default"]
/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

/* global ConcreteEvent */

/*
 * $.fn.concreteConversation
 * Functions for conversation handling
 *
 * Events:
 *    beforeInitializeConversation         : Before Conversation Initialized
 *    initializeConversation               : Conversation Initialized
 *    conversationLoaded                   : Conversation Loaded
 *    conversationPostError                : Error posting message
 *    conversationBeforeDeleteMessage      : Before deleting message
 *    conversationDeleteMessage            : Deleting message
 *    conversationDeleteMessageError       : Error deleting message
 *    conversationBeforeAddMessageFromJSON : Before adding message from json
 *    conversationAddMessageFromJSON       : After adding message from json
 *    conversationBeforeUpdateCount        : Before updating message count
 *    conversationUpdateCount              : After updating message count
 *    conversationBeforeSubmitForm         : Before submitting form
 *    conversationSubmitForm               : After submitting form
 */
;

(function (global, $) {
  'use strict';

  $.extend($.fn, {
    concreteConversation: function concreteConversation(options) {
      return this.each(function () {
        var $obj = $(this);
        var data = $obj.data('concreteConversation');

        if (!data) {
          $obj.data('concreteConversation', data = new ConcreteConversation($obj, options));
        }
      });
    }
  });
  var i18n = {
    Confirm_remove_message: 'Remove this message? Replies to it will not be removed.',
    Confirm_mark_as_spam: 'Are you sure you want to flag this message as spam?',
    Warn_currently_editing: 'Please complete or cancel the current message editing session before editing this message.',
    Unspecified_error_occurred: 'An unspecified error occurred.',
    Error_deleting_message: 'Something went wrong while deleting this message, please refresh and try again.',
    Error_flagging_message: 'Something went wrong while flagging this message, please refresh and try again.' // Please add new translatable strings to the getConversationsJavascript of /concrete/controllers/frontend/assets_localization.php

  };

  $.fn.concreteConversation.localize = function (dictionary) {
    $.extend(true, i18n, dictionary);
  };

  var ConcreteConversation = function ConcreteConversation(element, options) {
    this.publish('beforeInitializeConversation', {
      element: element,
      options: options
    });
    this.init(element, options);
    this.publish('initializeConversation', {
      element: element,
      options: options
    });
  };

  ConcreteConversation.fn = ConcreteConversation.prototype = {
    publish: function publish(t, f) {
      f = f || {};
      f.ConcreteConversation = this;
      window.ConcreteEvent.publish(t, f);
    },
    init: function init(element, options) {
      var obj = this;
      obj.$element = element;
      obj.options = $.extend({
        method: 'ajax',
        paginate: false,
        displayMode: 'threaded',
        itemsPerPage: -1,
        activeUsers: [],
        uninitialized: true,
        deleteMessageToken: null,
        addMessageToken: null,
        editMessageToken: null,
        flagMessageToken: null
      }, options);
      var enablePosting = obj.options.addMessageToken != '' ? 1 : 0;
      var paginate = obj.options.paginate ? 1 : 0;
      var orderBy = obj.options.orderBy;
      var enableOrdering = obj.options.enableOrdering;
      var displayPostingForm = obj.options.displayPostingForm;
      var enableCommentRating = obj.options.enableCommentRating;
      var enableTopCommentReviews = obj.options.enableTopCommentReviews;
      var displaySocialLinks = obj.options.displaySocialLinks;
      var addMessageLabel = obj.options.addMessageLabel ? obj.options.addMessageLabel : '';
      var dateFormat = obj.options.dateFormat;
      var customDateFormat = obj.options.customDateFormat;
      var blockAreaHandle = obj.options.blockAreaHandle; // var maxFiles = (obj.options.maxFiles); unused
      // var maxFileSize = (obj.options.maxFileSize); unused
      // var fileExtensions = (obj.options.fileExtensions); unused

      var attachmentsEnabled = obj.options.attachmentsEnabled;
      var attachmentOverridesEnabled = obj.options.attachmentOverridesEnabled;

      if (obj.options.method == 'ajax') {
        $.post(CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/view_ajax', {
          cnvID: obj.options.cnvID,
          cID: obj.options.cID,
          blockID: obj.options.blockID,
          enablePosting: enablePosting,
          itemsPerPage: obj.options.itemsPerPage,
          addMessageLabel: addMessageLabel,
          paginate: paginate,
          displayMode: obj.options.displayMode,
          orderBy: orderBy,
          enableOrdering: enableOrdering,
          displayPostingForm: displayPostingForm,
          enableCommentRating: enableCommentRating,
          enableTopCommentReviews: enableTopCommentReviews,
          displaySocialLinks: displaySocialLinks,
          dateFormat: dateFormat,
          customDateFormat: customDateFormat,
          blockAreaHandle: blockAreaHandle,
          attachmentsEnabled: attachmentsEnabled,
          attachmentOverridesEnabled: attachmentOverridesEnabled
        }, function (r) {
          var oldobj = window.obj;
          window.obj = obj;
          obj.$element.empty().append(r);
          var hash = window.location.hash.match(/^#cnv([0-9]+)Message[0-9]+$/);

          if (hash !== null && hash[1] == obj.options.cnvID) {
            var target = $('a' + window.location.hash).offset();
            $('html, body').animate({
              scrollTop: target.top
            }, 800, 'linear');
          }

          window.obj = oldobj;
          obj.attachBindings();
          obj.publish('conversationLoaded');
        });
      } else {
        obj.attachBindings();
        obj.finishSetup();
        obj.publish('conversationLoaded');
      }
    },
    mentionList: function mentionList(items, coordinates, bindTo) {
      var obj = this;
      if (!coordinates) return;
      obj.dropdown.parent.css({
        top: coordinates.y,
        left: coordinates.x
      });

      if (items.length == 0) {
        obj.dropdown.handle.dropdown('toggle');
        obj.dropdown.parent.remove();
        obj.dropdown.active = false;
        obj.dropdown.activeItem = -1;
        return;
      }

      obj.dropdown.list.empty();
      items.slice(0, 20).map(function (item) {
        var listitem = $('<li/>');
        var anchor = $('<a/>').appendTo(listitem).text(item.getName());
        anchor.click(function () {
          ConcreteEvent.fire('ConversationMentionSelect', {
            obj: obj,
            item: item
          }, bindTo);
        });
        listitem.appendTo(obj.dropdown.list);
      });

      if (!obj.dropdown.active) {
        obj.dropdown.active = true;
        obj.dropdown.activeItem = -1;
        obj.dropdown.parent.appendTo(obj.$element);
        obj.dropdown.handle.dropdown('toggle');
      }

      if (obj.dropdown.activeItem >= 0) {
        obj.dropdown.list.children().eq(obj.dropdown.activeItem).addClass('active');
      }
    },
    attachSubscriptionBindings: function attachSubscriptionBindings() {
      $('a[data-conversation-subscribe]').magnificPopup({
        type: 'ajax',
        callbacks: {
          updateStatus: function updateStatus(data) {
            if (data.status == 'ready') {
              var $form = $('form[data-conversation-form=subscribe]');
              $('button').on('click', $form, function (e) {
                e.preventDefault();
                e.stopPropagation();
                $.ajax({
                  url: $form.attr('action'),
                  dataType: 'json',
                  success: function success(r) {
                    if (r.subscribed) {
                      $('[data-conversation-subscribe=subscribe]').hide();
                      $('[data-conversation-subscribe=unsubscribe]').show();
                    } else {
                      $('[data-conversation-subscribe=unsubscribe]').hide();
                      $('[data-conversation-subscribe=subscribe]').show();
                    }

                    $.magnificPopup.close();
                  }
                });
              });
            }
          },
          beforeOpen: function beforeOpen() {
            // just a hack that adds mfp-anim class to markup
            this.st.mainClass = 'mfp-zoom-in';
          }
        },
        closeOnContentClick: true,
        midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.

      });
    },
    attachBindings: function attachBindings() {
      var obj = this;
      obj.$element.unbind('.cnv');

      if (obj.options.uninitialized) {
        obj.options.uninitialized = false;
        ConcreteEvent.bind('ConversationMention', function (e, data) {
          obj.mentionList(data.items, data.coordinates || false, data.bindTo || obj.$element.get(0));
        }, obj.$element.get(0) // Bind to this conversation only.
        );
        obj.dropdown = {};
        obj.dropdown.parent = $('<div/>').css({
          position: 'absolute',
          height: 0,
          width: 0
        });
        obj.dropdown.active = false;
        obj.dropdown.handle = $('<a/>').appendTo(obj.dropdown.parent);
        obj.dropdown.list = $('<ul/>').addClass('dropdown-menu').appendTo(obj.dropdown.parent);
        obj.dropdown.handle.dropdown();
        ConcreteEvent.bind('ConversationTextareaKeydownUp', function (e) {
          if (obj.dropdown.activeItem == -1) obj.dropdown.activeItem = obj.dropdown.list.children().length;
          obj.dropdown.activeItem -= 1;
          obj.dropdown.activeItem += obj.dropdown.list.children().length;
          obj.dropdown.activeItem %= obj.dropdown.list.children().length;
          obj.dropdown.list.children().filter('.active').removeClass('active').end().eq(obj.dropdown.activeItem).addClass('active');
        }, obj.$element.get(0));
        ConcreteEvent.bind('ConversationTextareaKeydownDown', function (e) {
          obj.dropdown.activeItem += 1;
          obj.dropdown.activeItem += obj.dropdown.list.children().length;
          obj.dropdown.activeItem %= obj.dropdown.list.children().length;
          obj.dropdown.list.children().filter('.active').removeClass('active').end().eq(obj.dropdown.activeItem).addClass('active');
        }, obj.$element.get(0));
        ConcreteEvent.bind('ConversationTextareaKeydownEnter', function (e) {
          obj.dropdown.list.children().filter('.active').children('a').click();
        }, obj.$element.get(0));
        ConcreteEvent.bind('ConversationPostError', function (e, data) {
          var $form = data.form;
          var messages = data.messages;
          var s = '';
          $.each(messages, function (i, m) {
            s += m + '<br>';
          });
          $form.find('div.ccm-conversation-errors').html(s).show();
        });
        ConcreteEvent.bind('ConversationSubmitForm', function (e, data) {
          data.form.find('div.ccm-conversation-errors').hide();
        });
      }

      var paginate = obj.options.paginate ? 1 : 0;
      var enablePosting = obj.options.addMessageToken != '' ? 1 : 0;
      var addMessageLabel = obj.options.addMessageLabel ? obj.options.addMessageLabel : '';
      obj.$replyholder = obj.$element.find('div.ccm-conversation-add-reply');
      obj.$newmessageform = obj.$element.find('div.ccm-conversation-add-new-message form');
      obj.$deleteholder = obj.$element.find('div.ccm-conversation-delete-message');
      obj.$attachmentdeleteholder = obj.$element.find('div.ccm-conversation-delete-attachment');
      obj.$permalinkholder = obj.$element.find('div.ccm-conversation-message-permalink');
      obj.$messagelist = obj.$element.find('div.ccm-conversation-message-list');
      obj.$messagecnt = obj.$element.find('.ccm-conversation-message-count');
      obj.$postbuttons = obj.$element.find('[data-submit=conversation-message]');
      obj.$sortselect = obj.$element.find('select[data-sort=conversation-message-list]');
      obj.$loadmore = obj.$element.find('[data-load-page=conversation-message-list]');
      obj.$messages = obj.$element.find('.ccm-conversation-messages');
      obj.$messagerating = obj.$element.find('span.ccm-conversation-message-rating');
      obj.$element.on('click.cnv', '[data-submit=conversation-message]', function (e) {
        e.preventDefault();
        obj.submitForm($(this));
      });
      obj.$element.on('click.cnv', '[data-submit=update-conversation-message]', function () {
        obj.submitUpdateForm($(this));
        return false;
      });
      this.attachSubscriptionBindings();
      var replyIterator = 1;
      obj.$element.on('click.cnv', 'a[data-toggle=conversation-reply]', function (event) {
        event.preventDefault();
        $('.ccm-conversation-attachment-container').each(function () {
          if ($(this).is(':visible')) {
            $(this).toggle();
          }
        });
        var $replyform = obj.$replyholder.appendTo($(this).closest('[data-conversation-message-id]'));
        $replyform.attr('data-form', 'conversation-reply').show();
        $replyform.find('[data-submit=conversation-message]').attr('data-post-parent-id', $(this).attr('data-post-parent-id'));
        $replyform.attr('rel', 'new-reply' + replyIterator);
        replyIterator++; // this may not be necessary, but might come in handy if we need to know how many times a new reply box has been triggered.

        return false;
      });
      $('.ccm-conversation-attachment-container').hide();
      $('.ccm-conversation-add-new-message .ccm-conversation-attachment-toggle').off('click.cnv').on('click.cnv', function (event) {
        event.preventDefault();

        if ($('.ccm-conversation-add-reply .ccm-conversation-attachment-container').is(':visible')) {
          $('.ccm-conversation-add-reply .ccm-conversation-attachment-container').toggle();
        }

        $('.ccm-conversation-add-new-message .ccm-conversation-attachment-container').toggle();
      });
      $('.ccm-conversation-add-reply .ccm-conversation-attachment-toggle').off('click.cnv').on('click.cnv', function (event) {
        event.preventDefault();

        if ($('.ccm-conversation-add-new-message .ccm-conversation-attachment-container').is(':visible')) {
          $('.ccm-conversation-add-new-message .ccm-conversation-attachment-container').toggle();
        }

        $('.ccm-conversation-add-reply .ccm-conversation-attachment-container').toggle();
      });
      obj.$element.on('click.cnv', 'a[data-submit=delete-conversation-message]', function () {
        var $link = $(this);
        obj.$deletedialog = obj.$deleteholder.clone();

        if (obj.$deletedialog.dialog) {
          obj.$deletedialog.dialog({
            modal: true,
            dialogClass: 'ccm-conversation-dialog',
            title: obj.$deleteholder.attr('data-dialog-title'),
            buttons: [{
              text: obj.$deleteholder.attr('data-cancel-button-title'),
              "class": 'btn pull-left',
              click: function click() {
                obj.$deletedialog.dialog('close');
              }
            }, {
              text: obj.$deleteholder.attr('data-confirm-button-title'),
              "class": 'btn pull-right btn-danger',
              click: function click() {
                obj.deleteMessage($link.attr('data-conversation-message-id'));
              }
            }]
          });
        } else {
          if (window.confirm(i18n.Confirm_remove_message)) {
            obj.deleteMessage($link.attr('data-conversation-message-id'));
          }
        }

        return false;
      });
      obj.$element.on('click.cnv', 'a[data-submit=flag-conversation-message]', function () {
        var $link = $(this);

        if (window.confirm(i18n.Confirm_mark_as_spam)) {
          obj.flagMessage($link.attr('data-conversation-message-id'));
        }

        return false;
      });
      obj.$element.on('click.cnv', 'a[data-load=edit-conversation-message]', function () {
        if ($('.ccm-conversation-edit-message').is(':visible')) {
          window.alert(i18n.Warn_currently_editing);
          return false;
        }

        var $link = $(this);
        obj.editMessage($link.attr('data-conversation-message-id'));
      });
      obj.$element.on('change.cnv', 'select[data-sort=conversation-message-list]', function () {
        obj.$messagelist.load(CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/view_ajax', {
          cnvID: obj.options.cnvID,
          task: 'get_messages',
          cID: obj.options.cID,
          blockID: obj.options.blockID,
          enablePosting: enablePosting,
          displayMode: obj.options.displayMode,
          itemsPerPage: obj.options.itemsPerPage,
          paginate: paginate,
          addMessageLabel: addMessageLabel,
          orderBy: $(this).val(),
          enableOrdering: obj.options.enableOrdering,
          displayPostingForm: obj.options.displayPostingForm,
          enableCommentRating: obj.options.enableCommentRating,
          enableTopCommentReviews: obj.options.enableTopCommentReviews,
          displaySocialLinks: obj.options.displaySocialLinks,
          dateFormat: obj.options.dateFormat,
          customDateFormat: obj.options.customDateFormat,
          blockAreaHandle: obj.options.blockAreaHandle,
          attachmentsEnabled: obj.options.attachmentsEnabled,
          attachmentOverridesEnabled: obj.options.attachmentOverridesEnabled
        }, function (r) {
          obj.$replyholder.appendTo(obj.$element);
          $('.ccm-conversation-messages .dropdown-toggle').dropdown();
          obj.attachBindings();
        });
      });
      obj.$element.on('click.cnv', '.image-popover-hover', function () {
        $.magnificPopup.open({
          items: {
            src: $(this).attr('data-full-image'),
            // can be a HTML string, jQuery object, or CSS selector
            type: 'image',
            verticalFit: true
          }
        });
      });
      obj.$element.on('click.cnv', '[data-load-page=conversation-message-list]', function () {
        var nextPage = parseInt(obj.$loadmore.attr('data-next-page'));
        var totalPages = parseInt(obj.$loadmore.attr('data-total-pages'));
        var orderBy = obj.$sortselect.length ? obj.$sortselect.val() : obj.options.orderBy;
        var data = {
          cnvID: obj.options.cnvID,
          cID: obj.options.cID,
          blockID: obj.options.blockID,
          itemsPerPage: obj.options.itemsPerPage,
          displayMode: obj.options.displayMode,
          blockAreaHandle: obj.options.blockAreaHandle,
          enablePosting: enablePosting,
          addMessageLabel: addMessageLabel,
          page: nextPage,
          orderBy: orderBy,
          enableCommentRating: obj.options.enableCommentRating,
          enableTopCommentReviews: obj.options.enableTopCommentReviews,
          displaySocialLinks: obj.options.displaySocialLinks,
          dateFormat: obj.options.dateFormat,
          customDateFormat: obj.options.customDateFormat,
          attachmentsEnabled: obj.options.attachmentsEnabled,
          attachmentOverridesEnabled: obj.options.attachmentOverridesEnabled
        };
        $.ajax({
          type: 'post',
          data: data,
          url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/message_page',
          success: function success(html) {
            obj.$messages.append(html);
            $('.ccm-conversation-messages .dropdown-toggle').dropdown();

            if (nextPage + 1 > totalPages) {
              obj.$loadmore.hide();
            } else {
              obj.$loadmore.attr('data-next-page', nextPage + 1);
            }
          }
        });
      });
      obj.$element.on('click.cnv', '.conversation-rate-message', function () {
        var cnvMessageID = $(this).closest('[data-conversation-message-id]').attr('data-conversation-message-id');
        var cnvRatingTypeHandle = $(this).attr('data-conversation-rating-type');
        obj.$messagerating.load(CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/rate');
        var data = {
          cnvID: obj.options.cnvID,
          cID: obj.options.cID,
          blockID: obj.options.blockID,
          cnvMessageID: cnvMessageID,
          cnvRatingTypeHandle: cnvRatingTypeHandle
        };
        $.ajax({
          type: 'post',
          data: data,
          url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/rate',
          success: function success(html) {
            $('span[data-message-rating="' + cnvMessageID + '"]').load(CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/get_rating', {
              cnvMessageID: cnvMessageID
            });
          }
        });
      });
      obj.$element.on('click.cnv', 'a.share-popup', function () {
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screen.top;
        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : window.screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : window.screen.height;
        var left = width / 2 - 300 + dualScreenLeft;
        var top = height / 2 - 125 + dualScreenTop;
        window.open($(this).attr('href'), 'cnvSocialShare', 'left:' + left + ',top:' + top + ',height=250,width=600,toolbar=no,status=no');
        return false;
      });
      obj.$element.on('click.cnv', 'a.share-permalink', function () {
        var $link = $(this);
        var permalink = $link.attr('rel');
        obj.$permalinkdialog = obj.$permalinkholder.clone();
        var $textarea = $('<textarea readonly>').text(decodeURIComponent(permalink));
        obj.$permalinkdialog.append($textarea);
        $textarea.click(function () {
          var $this = $(this);
          $this.select();
          window.setTimeout(function () {
            $this.select();
          }, 1);
          $this.mouseup(function () {
            $this.unbind('mouseup');
            return false;
          });
        });

        if (obj.$permalinkdialog.dialog) {
          obj.$permalinkdialog.dialog({
            modal: true,
            dialogClass: 'ccm-conversation-dialog',
            title: obj.$permalinkholder.attr('data-dialog-title'),
            buttons: [{
              text: obj.$permalinkholder.attr('data-cancel-button-title'),
              "class": 'btn pull-left',
              click: function click() {
                obj.$permalinkdialog.dialog('close');
              }
            }]
          });
        }

        return false;
      });

      if (obj.options.attachmentsEnabled > 0) {
        obj.$element.concreteConversationAttachments(obj);
      }

      $('.dropdown-toggle').dropdown();
    },
    handlePostError: function handlePostError($form, messages) {
      if (!messages) {
        messages = [i18n.Unspecified_error_occurred];
      }

      this.publish('conversationPostError', {
        form: $form,
        messages: messages
      });
    },
    deleteMessage: function deleteMessage(msgID) {
      var obj = this;
      obj.publish('conversationBeforeDeleteMessage', {
        msgID: msgID
      });
      var formArray = [{
        name: 'cnvMessageID',
        value: msgID
      }, {
        name: 'token',
        value: obj.options.deleteMessageToken
      }];
      $.ajax({
        type: 'post',
        data: formArray,
        dataType: 'json',
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/delete_message',
        success: function success(r) {
          if (!r.error) {
            var $parent = $('[data-conversation-message-id=' + msgID + ']');

            if ($parent.length) {
              $parent.remove();
            }

            obj.updateCount();

            if (obj.$deletedialog.dialog) {
              obj.$deletedialog.dialog('close');
            }

            obj.publish('conversationDeleteMessage', {
              msgID: msgID
            });
          } else {
            window.alert(i18n.Error_deleting_message + '\n\n' + r.errors.join('\n'));
          }
        },
        error: function error(e) {
          obj.publish('conversationDeleteMessageError', {
            msgID: msgID,
            error: arguments
          });
          window.alert(i18n.Error_deleting_message);
        }
      });
    },
    editMessage: function editMessage(msgID) {
      var obj = this;
      var formArray = [{
        name: 'cnvMessageID',
        value: msgID
      }, {
        name: 'cID',
        value: this.options.cID
      }, {
        name: 'blockAreaHandle',
        value: this.options.blockAreaHandle
      }, {
        name: 'bID',
        value: this.options.blockID
      }];
      $.ajax({
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/edit_message',
        success: function success(html) {
          var $parent = $('.ccm-conversation-message[data-conversation-message-id=' + msgID + ']');
          var $previousContents = $parent;
          $parent.after(html).remove();
          $('.ccm-conversation-attachment-container').hide();
          $('.ccm-conversation-edit-message .ccm-conversation-attachment-toggle').off('click.cnv').on('click.cnv', function (event) {
            event.preventDefault();
            $('.ccm-conversation-edit-message .ccm-conversation-attachment-container').toggle();
          });
          obj.$editMessageHolder = obj.$element.find('div.ccm-conversation-edit-message');
          obj.$element.concreteConversationAttachments(obj);
          $('button.cancel-update').on('click.cnv', function () {
            $('.ccm-conversation-edit-message').replaceWith($previousContents);
          });
        },
        error: function error(e) {
          obj.publish('conversationEditMessageError', {
            msgID: msgID,
            error: arguments
          });
        }
      });
    },
    flagMessage: function flagMessage(msgID) {
      var obj = this;
      obj.publish('conversationBeforeFlagMessage', {
        msgID: msgID
      });
      var formArray = [{
        name: 'token',
        value: obj.options.flagMessageToken
      }, {
        name: 'cnvMessageID',
        value: msgID
      }];
      $.ajax({
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/flag_message/0',
        success: function success(html) {
          var $parent = $('.ccm-conversation-message[data-conversation-message-id=' + msgID + ']');

          if ($parent.length) {
            $parent.after(html).remove();
          }

          obj.updateCount();
          obj.publish('conversationFlagMessage', {
            msgID: msgID
          });
        },
        error: function error(e) {
          obj.publish('conversationFlagMessageError', {
            msgID: msgID,
            error: arguments
          });
          window.alert(i18n.Error_flagging_message);
        }
      });
    },
    addMessageFromJSON: function addMessageFromJSON($form, json) {
      var obj = this;
      obj.publish('conversationBeforeAddMessageFromJSON', {
        json: json,
        form: $form
      });
      var enablePosting = obj.options.addMessageToken != '' ? 1 : 0;
      var formArray = [{
        name: 'cnvMessageID',
        value: json.cnvMessageID
      }, {
        name: 'enablePosting',
        value: enablePosting
      }, {
        name: 'displayMode',
        value: obj.options.displayMode
      }, {
        name: 'enableCommentRating',
        value: obj.options.enableCommentRating
      }, {
        name: 'displaySocialLinks',
        value: obj.options.displaySocialLinks
      }];
      $.ajax({
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/message_detail',
        success: function success(html) {
          var $parent = $('.ccm-conversation-message[data-conversation-message-id=' + json.cnvMessageParentID + ']');

          if ($parent.length) {
            $parent.after(html);
            obj.$replyholder.appendTo(obj.$element);
            obj.$replyholder.hide();
            obj.$replyholder.find('.conversation-editor').val('');

            try {
              obj.$replyholder.find('.redactor_conversation_editor_' + obj.options.cnvID).redactor('set', '');
            } catch (e) {}
          } else {
            if (obj.options.orderBy == 'date_desc') {
              obj.$messages.prepend(html);
            } else {
              obj.$messages.append(html);
            }

            obj.$element.find('.ccm-conversation-no-messages').hide();
            obj.$newmessageform.find('.conversation-editor').val('');

            try {
              obj.$newmessageform.find('.redactor_conversation_editor_' + obj.options.cnvID).redactor('set', '');
            } catch (e) {}
          }

          obj.publish('conversationAddMessageFromJSON', {
            json: json,
            form: $form
          });
          obj.updateCount();
          var target = $('a#cnv' + obj.options.cnvID + 'Message' + json.cnvMessageID).offset();
          $('.dropdown-toggle').dropdown();
          $('html, body').animate({
            scrollTop: target.top
          }, 800, 'linear');
        }
      });
    },
    updateMessageFromJSON: function updateMessageFromJSON($form, json) {
      var obj = this;
      var enablePosting = obj.options.addMessageToken != '' ? 1 : 0;
      var formArray = [{
        name: 'cnvMessageID',
        value: json.cnvMessageID
      }, {
        name: 'enablePosting',
        value: enablePosting
      }, {
        name: 'displayMode',
        value: obj.options.displayMode
      }, {
        name: 'enableCommentRating',
        value: obj.options.enableCommentRating
      }, {
        name: 'displaySocialLinks',
        value: obj.options.displaySocialLinks
      }];
      $.ajax({
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/message_detail',
        success: function success(html) {
          var $parent = $('[data-conversation-message-id=' + json.cnvMessageID + ']');
          $parent.after(html).remove();
          $('.dropdown-toggle').dropdown();
        }
      });
    },
    updateCount: function updateCount() {
      var obj = this;
      obj.publish('conversationBeforeUpdateCount');
      obj.$messagecnt.load(CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/count_header', {
        cnvID: obj.options.cnvID
      }, function () {
        obj.publish('conversationUpdateCount');
      });
    },
    submitForm: function submitForm($btn) {
      var obj = this;
      obj.publish('conversationBeforeSubmitForm');
      var $form = $btn.closest('form');
      $btn.prop('disabled', true);
      $form.parent().addClass('ccm-conversation-form-submitted');
      var formArray = $form.serializeArray();
      var parentID = $btn.attr('data-post-parent-id');
      formArray.push({
        name: 'token',
        value: obj.options.addMessageToken
      }, {
        name: 'cnvID',
        value: obj.options.cnvID
      }, {
        name: 'cnvMessageParentID',
        value: parentID
      }, {
        name: 'enableRating',
        value: parentID
      });
      $.ajax({
        dataType: 'json',
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/add_message',
        success: function success(r) {
          if (!r) {
            obj.handlePostError($form);
            return false;
          }

          if (r.error) {
            obj.handlePostError($form, r.errors);
            return false;
          }

          $('.preview.processing').each(function () {
            $('input[rel="' + $(this).attr('rel') + '"]').remove();
          });
          $('form.dropzone').each(function () {
            var d = $(this).data('dropzone');
            $.each(d.files, function (k, v) {
              d.removeFile(v);
            });
          });
          obj.addMessageFromJSON($form, r);
          obj.publish('conversationSubmitForm', {
            form: $form,
            response: r
          });
        },
        error: function error(r) {
          obj.handlePostError($form);
          return false;
        },
        complete: function complete(r) {
          $btn.prop('disabled', false);
          $form.parent().closest('.ccm-conversation-form-submitted').removeClass('ccm-conversation-form-submitted');
        }
      });
    },
    submitUpdateForm: function submitUpdateForm($btn) {
      var obj = this;
      obj.publish('conversationBeforeSubmitForm');
      var $form = $btn.closest('form');
      $btn.prop('disabled', true);
      $form.parent().addClass('ccm-conversation-form-submitted');
      var formArray = $form.serializeArray();
      var cnvMessageID = $btn.attr('data-post-message-id');
      formArray.push({
        name: 'token',
        value: obj.options.editMessageToken
      }, {
        name: 'cnvMessageID',
        value: cnvMessageID
      });
      $.ajax({
        dataType: 'json',
        type: 'post',
        data: formArray,
        url: CCM_DISPATCHER_FILENAME + '/ccm/frontend/conversations/update_message',
        success: function success(r) {
          if (!r) {
            obj.handlePostError($form);
            return false;
          }

          if (r.error) {
            obj.handlePostError($form, r.errors);
            return false;
          }

          $('.preview.processing').each(function () {
            $('input[rel="' + $(this).attr('rel') + '"]').remove();
          });
          /*
           $('form.dropzone').each(function(){
           var d = $(this).data('dropzone');
           $.each(d.files,function(k,v){
           d.removeFile(v);
           });
           });
           */

          obj.updateMessageFromJSON($form, r);
          obj.publish('conversationSubmitForm', {
            form: $form,
            response: r
          });
        },
        error: function error(r) {
          obj.handlePostError($form);
          return false;
        },
        complete: function complete(r) {
          $btn.prop('disabled', false);
          $form.parent().closest('.ccm-conversation-form-submitted').removeClass('ccm-conversation-form-submitted');
        }
      });
    },
    tool: {
      setCaretPosition: function setCaretPosition(elem, caretPos) {
        // http://stackoverflow.com/a/512542/950669
        if (elem != null) {
          if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
          } else {
            if (elem.selectionStart) {
              elem.focus();
              elem.setSelectionRange(caretPos, caretPos);
            } else {
              elem.focus();
            }
          }
        }
      },
      getCaretPosition: function getCaretPosition(elem) {
        // http://stackoverflow.com/a/263796/950669
        if (elem.selectionStart) {
          return elem.selectionStart;
        } else if (document.selection) {
          elem.focus();
          var r = document.selection.createRange();

          if (r == null) {
            return 0;
          }

          var re = elem.createTextRange();
          var rc = re.duplicate();
          re.moveToBookmark(r.getBookmark());
          rc.setEndPoint('EndToStart', re);
          return rc.text.length;
        }

        return 0;
      },
      testMentionString: function testMentionString(s) {
        return /^@[a-z0-9]+$/.test(s);
      },
      getMentionMatches: function getMentionMatches(s, u) {
        return u.filter(function (d) {
          return d.indexOf(s) >= 0;
        });
      },
      isSameConversation: function isSameConversation(o, n) {
        return o.options.blockID === n.options.blockID && o.options.cnvID === n.options.cnvID;
      },
      // MentionUser class, use this to pass around data with your @mention names.
      MentionUser: function MentionUser(name) {
        this.getName = function () {
          return name;
        };
      }
    }
  };
})(window, jQuery); // eslint-disable-line semi

/***/ }),

/***/ "./node_modules/dropzone/dist/dropzone.js":
/*!************************************************!*\
  !*** ./node_modules/dropzone/dist/dropzone.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof2(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

(function webpackUniversalModuleDefinition(root, factory) {
  if (( false ? undefined : _typeof2(exports)) === 'object' && ( false ? undefined : _typeof2(module)) === 'object') module.exports = factory();else if (true) !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else { var i, a; }
})(self, function () {
  return (
    /******/
    function () {
      // webpackBootstrap

      /******/
      var __webpack_modules__ = {
        /***/
        3099:
        /***/
        function _(module) {
          module.exports = function (it) {
            if (typeof it != 'function') {
              throw TypeError(String(it) + ' is not a function');
            }

            return it;
          };
          /***/

        },

        /***/
        6077:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var isObject = __webpack_require__(111);

          module.exports = function (it) {
            if (!isObject(it) && it !== null) {
              throw TypeError("Can't set " + String(it) + ' as a prototype');
            }

            return it;
          };
          /***/

        },

        /***/
        1223:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var wellKnownSymbol = __webpack_require__(5112);

          var create = __webpack_require__(30);

          var definePropertyModule = __webpack_require__(3070);

          var UNSCOPABLES = wellKnownSymbol('unscopables');
          var ArrayPrototype = Array.prototype; // Array.prototype[@@unscopables]
          // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables

          if (ArrayPrototype[UNSCOPABLES] == undefined) {
            definePropertyModule.f(ArrayPrototype, UNSCOPABLES, {
              configurable: true,
              value: create(null)
            });
          } // add a key to Array.prototype[@@unscopables]


          module.exports = function (key) {
            ArrayPrototype[UNSCOPABLES][key] = true;
          };
          /***/

        },

        /***/
        1530:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var charAt = __webpack_require__(8710).charAt; // `AdvanceStringIndex` abstract operation
          // https://tc39.es/ecma262/#sec-advancestringindex


          module.exports = function (S, index, unicode) {
            return index + (unicode ? charAt(S, index).length : 1);
          };
          /***/

        },

        /***/
        5787:
        /***/
        function _(module) {
          module.exports = function (it, Constructor, name) {
            if (!(it instanceof Constructor)) {
              throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
            }

            return it;
          };
          /***/

        },

        /***/
        9670:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var isObject = __webpack_require__(111);

          module.exports = function (it) {
            if (!isObject(it)) {
              throw TypeError(String(it) + ' is not an object');
            }

            return it;
          };
          /***/

        },

        /***/
        4019:
        /***/
        function _(module) {
          module.exports = typeof ArrayBuffer !== 'undefined' && typeof DataView !== 'undefined';
          /***/
        },

        /***/
        260:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var NATIVE_ARRAY_BUFFER = __webpack_require__(4019);

          var DESCRIPTORS = __webpack_require__(9781);

          var global = __webpack_require__(7854);

          var isObject = __webpack_require__(111);

          var has = __webpack_require__(6656);

          var classof = __webpack_require__(648);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var redefine = __webpack_require__(1320);

          var defineProperty = __webpack_require__(3070).f;

          var getPrototypeOf = __webpack_require__(9518);

          var setPrototypeOf = __webpack_require__(7674);

          var wellKnownSymbol = __webpack_require__(5112);

          var uid = __webpack_require__(9711);

          var Int8Array = global.Int8Array;
          var Int8ArrayPrototype = Int8Array && Int8Array.prototype;
          var Uint8ClampedArray = global.Uint8ClampedArray;
          var Uint8ClampedArrayPrototype = Uint8ClampedArray && Uint8ClampedArray.prototype;
          var TypedArray = Int8Array && getPrototypeOf(Int8Array);
          var TypedArrayPrototype = Int8ArrayPrototype && getPrototypeOf(Int8ArrayPrototype);
          var ObjectPrototype = Object.prototype;
          var isPrototypeOf = ObjectPrototype.isPrototypeOf;
          var TO_STRING_TAG = wellKnownSymbol('toStringTag');
          var TYPED_ARRAY_TAG = uid('TYPED_ARRAY_TAG'); // Fixing native typed arrays in Opera Presto crashes the browser, see #595

          var NATIVE_ARRAY_BUFFER_VIEWS = NATIVE_ARRAY_BUFFER && !!setPrototypeOf && classof(global.opera) !== 'Opera';
          var TYPED_ARRAY_TAG_REQIRED = false;
          var NAME;
          var TypedArrayConstructorsList = {
            Int8Array: 1,
            Uint8Array: 1,
            Uint8ClampedArray: 1,
            Int16Array: 2,
            Uint16Array: 2,
            Int32Array: 4,
            Uint32Array: 4,
            Float32Array: 4,
            Float64Array: 8
          };
          var BigIntArrayConstructorsList = {
            BigInt64Array: 8,
            BigUint64Array: 8
          };

          var isView = function isView(it) {
            if (!isObject(it)) return false;
            var klass = classof(it);
            return klass === 'DataView' || has(TypedArrayConstructorsList, klass) || has(BigIntArrayConstructorsList, klass);
          };

          var isTypedArray = function isTypedArray(it) {
            if (!isObject(it)) return false;
            var klass = classof(it);
            return has(TypedArrayConstructorsList, klass) || has(BigIntArrayConstructorsList, klass);
          };

          var aTypedArray = function aTypedArray(it) {
            if (isTypedArray(it)) return it;
            throw TypeError('Target is not a typed array');
          };

          var aTypedArrayConstructor = function aTypedArrayConstructor(C) {
            if (setPrototypeOf) {
              if (isPrototypeOf.call(TypedArray, C)) return C;
            } else for (var ARRAY in TypedArrayConstructorsList) {
              if (has(TypedArrayConstructorsList, NAME)) {
                var TypedArrayConstructor = global[ARRAY];

                if (TypedArrayConstructor && (C === TypedArrayConstructor || isPrototypeOf.call(TypedArrayConstructor, C))) {
                  return C;
                }
              }
            }

            throw TypeError('Target is not a typed array constructor');
          };

          var exportTypedArrayMethod = function exportTypedArrayMethod(KEY, property, forced) {
            if (!DESCRIPTORS) return;
            if (forced) for (var ARRAY in TypedArrayConstructorsList) {
              var TypedArrayConstructor = global[ARRAY];

              if (TypedArrayConstructor && has(TypedArrayConstructor.prototype, KEY)) {
                delete TypedArrayConstructor.prototype[KEY];
              }
            }

            if (!TypedArrayPrototype[KEY] || forced) {
              redefine(TypedArrayPrototype, KEY, forced ? property : NATIVE_ARRAY_BUFFER_VIEWS && Int8ArrayPrototype[KEY] || property);
            }
          };

          var exportTypedArrayStaticMethod = function exportTypedArrayStaticMethod(KEY, property, forced) {
            var ARRAY, TypedArrayConstructor;
            if (!DESCRIPTORS) return;

            if (setPrototypeOf) {
              if (forced) for (ARRAY in TypedArrayConstructorsList) {
                TypedArrayConstructor = global[ARRAY];

                if (TypedArrayConstructor && has(TypedArrayConstructor, KEY)) {
                  delete TypedArrayConstructor[KEY];
                }
              }

              if (!TypedArray[KEY] || forced) {
                // V8 ~ Chrome 49-50 `%TypedArray%` methods are non-writable non-configurable
                try {
                  return redefine(TypedArray, KEY, forced ? property : NATIVE_ARRAY_BUFFER_VIEWS && Int8Array[KEY] || property);
                } catch (error) {
                  /* empty */
                }
              } else return;
            }

            for (ARRAY in TypedArrayConstructorsList) {
              TypedArrayConstructor = global[ARRAY];

              if (TypedArrayConstructor && (!TypedArrayConstructor[KEY] || forced)) {
                redefine(TypedArrayConstructor, KEY, property);
              }
            }
          };

          for (NAME in TypedArrayConstructorsList) {
            if (!global[NAME]) NATIVE_ARRAY_BUFFER_VIEWS = false;
          } // WebKit bug - typed arrays constructors prototype is Object.prototype


          if (!NATIVE_ARRAY_BUFFER_VIEWS || typeof TypedArray != 'function' || TypedArray === Function.prototype) {
            // eslint-disable-next-line no-shadow -- safe
            TypedArray = function TypedArray() {
              throw TypeError('Incorrect invocation');
            };

            if (NATIVE_ARRAY_BUFFER_VIEWS) for (NAME in TypedArrayConstructorsList) {
              if (global[NAME]) setPrototypeOf(global[NAME], TypedArray);
            }
          }

          if (!NATIVE_ARRAY_BUFFER_VIEWS || !TypedArrayPrototype || TypedArrayPrototype === ObjectPrototype) {
            TypedArrayPrototype = TypedArray.prototype;
            if (NATIVE_ARRAY_BUFFER_VIEWS) for (NAME in TypedArrayConstructorsList) {
              if (global[NAME]) setPrototypeOf(global[NAME].prototype, TypedArrayPrototype);
            }
          } // WebKit bug - one more object in Uint8ClampedArray prototype chain


          if (NATIVE_ARRAY_BUFFER_VIEWS && getPrototypeOf(Uint8ClampedArrayPrototype) !== TypedArrayPrototype) {
            setPrototypeOf(Uint8ClampedArrayPrototype, TypedArrayPrototype);
          }

          if (DESCRIPTORS && !has(TypedArrayPrototype, TO_STRING_TAG)) {
            TYPED_ARRAY_TAG_REQIRED = true;
            defineProperty(TypedArrayPrototype, TO_STRING_TAG, {
              get: function get() {
                return isObject(this) ? this[TYPED_ARRAY_TAG] : undefined;
              }
            });

            for (NAME in TypedArrayConstructorsList) {
              if (global[NAME]) {
                createNonEnumerableProperty(global[NAME], TYPED_ARRAY_TAG, NAME);
              }
            }
          }

          module.exports = {
            NATIVE_ARRAY_BUFFER_VIEWS: NATIVE_ARRAY_BUFFER_VIEWS,
            TYPED_ARRAY_TAG: TYPED_ARRAY_TAG_REQIRED && TYPED_ARRAY_TAG,
            aTypedArray: aTypedArray,
            aTypedArrayConstructor: aTypedArrayConstructor,
            exportTypedArrayMethod: exportTypedArrayMethod,
            exportTypedArrayStaticMethod: exportTypedArrayStaticMethod,
            isView: isView,
            isTypedArray: isTypedArray,
            TypedArray: TypedArray,
            TypedArrayPrototype: TypedArrayPrototype
          };
          /***/
        },

        /***/
        3331:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var global = __webpack_require__(7854);

          var DESCRIPTORS = __webpack_require__(9781);

          var NATIVE_ARRAY_BUFFER = __webpack_require__(4019);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var redefineAll = __webpack_require__(2248);

          var fails = __webpack_require__(7293);

          var anInstance = __webpack_require__(5787);

          var toInteger = __webpack_require__(9958);

          var toLength = __webpack_require__(7466);

          var toIndex = __webpack_require__(7067);

          var IEEE754 = __webpack_require__(1179);

          var getPrototypeOf = __webpack_require__(9518);

          var setPrototypeOf = __webpack_require__(7674);

          var getOwnPropertyNames = __webpack_require__(8006).f;

          var defineProperty = __webpack_require__(3070).f;

          var arrayFill = __webpack_require__(1285);

          var setToStringTag = __webpack_require__(8003);

          var InternalStateModule = __webpack_require__(9909);

          var getInternalState = InternalStateModule.get;
          var setInternalState = InternalStateModule.set;
          var ARRAY_BUFFER = 'ArrayBuffer';
          var DATA_VIEW = 'DataView';
          var PROTOTYPE = 'prototype';
          var WRONG_LENGTH = 'Wrong length';
          var WRONG_INDEX = 'Wrong index';
          var NativeArrayBuffer = global[ARRAY_BUFFER];
          var $ArrayBuffer = NativeArrayBuffer;
          var $DataView = global[DATA_VIEW];
          var $DataViewPrototype = $DataView && $DataView[PROTOTYPE];
          var ObjectPrototype = Object.prototype;
          var RangeError = global.RangeError;
          var packIEEE754 = IEEE754.pack;
          var unpackIEEE754 = IEEE754.unpack;

          var packInt8 = function packInt8(number) {
            return [number & 0xFF];
          };

          var packInt16 = function packInt16(number) {
            return [number & 0xFF, number >> 8 & 0xFF];
          };

          var packInt32 = function packInt32(number) {
            return [number & 0xFF, number >> 8 & 0xFF, number >> 16 & 0xFF, number >> 24 & 0xFF];
          };

          var unpackInt32 = function unpackInt32(buffer) {
            return buffer[3] << 24 | buffer[2] << 16 | buffer[1] << 8 | buffer[0];
          };

          var packFloat32 = function packFloat32(number) {
            return packIEEE754(number, 23, 4);
          };

          var packFloat64 = function packFloat64(number) {
            return packIEEE754(number, 52, 8);
          };

          var addGetter = function addGetter(Constructor, key) {
            defineProperty(Constructor[PROTOTYPE], key, {
              get: function get() {
                return getInternalState(this)[key];
              }
            });
          };

          var get = function get(view, count, index, isLittleEndian) {
            var intIndex = toIndex(index);
            var store = getInternalState(view);
            if (intIndex + count > store.byteLength) throw RangeError(WRONG_INDEX);
            var bytes = getInternalState(store.buffer).bytes;
            var start = intIndex + store.byteOffset;
            var pack = bytes.slice(start, start + count);
            return isLittleEndian ? pack : pack.reverse();
          };

          var set = function set(view, count, index, conversion, value, isLittleEndian) {
            var intIndex = toIndex(index);
            var store = getInternalState(view);
            if (intIndex + count > store.byteLength) throw RangeError(WRONG_INDEX);
            var bytes = getInternalState(store.buffer).bytes;
            var start = intIndex + store.byteOffset;
            var pack = conversion(+value);

            for (var i = 0; i < count; i++) {
              bytes[start + i] = pack[isLittleEndian ? i : count - i - 1];
            }
          };

          if (!NATIVE_ARRAY_BUFFER) {
            $ArrayBuffer = function ArrayBuffer(length) {
              anInstance(this, $ArrayBuffer, ARRAY_BUFFER);
              var byteLength = toIndex(length);
              setInternalState(this, {
                bytes: arrayFill.call(new Array(byteLength), 0),
                byteLength: byteLength
              });
              if (!DESCRIPTORS) this.byteLength = byteLength;
            };

            $DataView = function DataView(buffer, byteOffset, byteLength) {
              anInstance(this, $DataView, DATA_VIEW);
              anInstance(buffer, $ArrayBuffer, DATA_VIEW);
              var bufferLength = getInternalState(buffer).byteLength;
              var offset = toInteger(byteOffset);
              if (offset < 0 || offset > bufferLength) throw RangeError('Wrong offset');
              byteLength = byteLength === undefined ? bufferLength - offset : toLength(byteLength);
              if (offset + byteLength > bufferLength) throw RangeError(WRONG_LENGTH);
              setInternalState(this, {
                buffer: buffer,
                byteLength: byteLength,
                byteOffset: offset
              });

              if (!DESCRIPTORS) {
                this.buffer = buffer;
                this.byteLength = byteLength;
                this.byteOffset = offset;
              }
            };

            if (DESCRIPTORS) {
              addGetter($ArrayBuffer, 'byteLength');
              addGetter($DataView, 'buffer');
              addGetter($DataView, 'byteLength');
              addGetter($DataView, 'byteOffset');
            }

            redefineAll($DataView[PROTOTYPE], {
              getInt8: function getInt8(byteOffset) {
                return get(this, 1, byteOffset)[0] << 24 >> 24;
              },
              getUint8: function getUint8(byteOffset) {
                return get(this, 1, byteOffset)[0];
              },
              getInt16: function getInt16(byteOffset
              /* , littleEndian */
              ) {
                var bytes = get(this, 2, byteOffset, arguments.length > 1 ? arguments[1] : undefined);
                return (bytes[1] << 8 | bytes[0]) << 16 >> 16;
              },
              getUint16: function getUint16(byteOffset
              /* , littleEndian */
              ) {
                var bytes = get(this, 2, byteOffset, arguments.length > 1 ? arguments[1] : undefined);
                return bytes[1] << 8 | bytes[0];
              },
              getInt32: function getInt32(byteOffset
              /* , littleEndian */
              ) {
                return unpackInt32(get(this, 4, byteOffset, arguments.length > 1 ? arguments[1] : undefined));
              },
              getUint32: function getUint32(byteOffset
              /* , littleEndian */
              ) {
                return unpackInt32(get(this, 4, byteOffset, arguments.length > 1 ? arguments[1] : undefined)) >>> 0;
              },
              getFloat32: function getFloat32(byteOffset
              /* , littleEndian */
              ) {
                return unpackIEEE754(get(this, 4, byteOffset, arguments.length > 1 ? arguments[1] : undefined), 23);
              },
              getFloat64: function getFloat64(byteOffset
              /* , littleEndian */
              ) {
                return unpackIEEE754(get(this, 8, byteOffset, arguments.length > 1 ? arguments[1] : undefined), 52);
              },
              setInt8: function setInt8(byteOffset, value) {
                set(this, 1, byteOffset, packInt8, value);
              },
              setUint8: function setUint8(byteOffset, value) {
                set(this, 1, byteOffset, packInt8, value);
              },
              setInt16: function setInt16(byteOffset, value
              /* , littleEndian */
              ) {
                set(this, 2, byteOffset, packInt16, value, arguments.length > 2 ? arguments[2] : undefined);
              },
              setUint16: function setUint16(byteOffset, value
              /* , littleEndian */
              ) {
                set(this, 2, byteOffset, packInt16, value, arguments.length > 2 ? arguments[2] : undefined);
              },
              setInt32: function setInt32(byteOffset, value
              /* , littleEndian */
              ) {
                set(this, 4, byteOffset, packInt32, value, arguments.length > 2 ? arguments[2] : undefined);
              },
              setUint32: function setUint32(byteOffset, value
              /* , littleEndian */
              ) {
                set(this, 4, byteOffset, packInt32, value, arguments.length > 2 ? arguments[2] : undefined);
              },
              setFloat32: function setFloat32(byteOffset, value
              /* , littleEndian */
              ) {
                set(this, 4, byteOffset, packFloat32, value, arguments.length > 2 ? arguments[2] : undefined);
              },
              setFloat64: function setFloat64(byteOffset, value
              /* , littleEndian */
              ) {
                set(this, 8, byteOffset, packFloat64, value, arguments.length > 2 ? arguments[2] : undefined);
              }
            });
          } else {
            /* eslint-disable no-new -- required for testing */
            if (!fails(function () {
              NativeArrayBuffer(1);
            }) || !fails(function () {
              new NativeArrayBuffer(-1);
            }) || fails(function () {
              new NativeArrayBuffer();
              new NativeArrayBuffer(1.5);
              new NativeArrayBuffer(NaN);
              return NativeArrayBuffer.name != ARRAY_BUFFER;
            })) {
              /* eslint-enable no-new -- required for testing */
              $ArrayBuffer = function ArrayBuffer(length) {
                anInstance(this, $ArrayBuffer);
                return new NativeArrayBuffer(toIndex(length));
              };

              var ArrayBufferPrototype = $ArrayBuffer[PROTOTYPE] = NativeArrayBuffer[PROTOTYPE];

              for (var keys = getOwnPropertyNames(NativeArrayBuffer), j = 0, key; keys.length > j;) {
                if (!((key = keys[j++]) in $ArrayBuffer)) {
                  createNonEnumerableProperty($ArrayBuffer, key, NativeArrayBuffer[key]);
                }
              }

              ArrayBufferPrototype.constructor = $ArrayBuffer;
            } // WebKit bug - the same parent prototype for typed arrays and data view


            if (setPrototypeOf && getPrototypeOf($DataViewPrototype) !== ObjectPrototype) {
              setPrototypeOf($DataViewPrototype, ObjectPrototype);
            } // iOS Safari 7.x bug


            var testView = new $DataView(new $ArrayBuffer(2));
            var nativeSetInt8 = $DataViewPrototype.setInt8;
            testView.setInt8(0, 2147483648);
            testView.setInt8(1, 2147483649);
            if (testView.getInt8(0) || !testView.getInt8(1)) redefineAll($DataViewPrototype, {
              setInt8: function setInt8(byteOffset, value) {
                nativeSetInt8.call(this, byteOffset, value << 24 >> 24);
              },
              setUint8: function setUint8(byteOffset, value) {
                nativeSetInt8.call(this, byteOffset, value << 24 >> 24);
              }
            }, {
              unsafe: true
            });
          }

          setToStringTag($ArrayBuffer, ARRAY_BUFFER);
          setToStringTag($DataView, DATA_VIEW);
          module.exports = {
            ArrayBuffer: $ArrayBuffer,
            DataView: $DataView
          };
          /***/
        },

        /***/
        1048:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var toObject = __webpack_require__(7908);

          var toAbsoluteIndex = __webpack_require__(1400);

          var toLength = __webpack_require__(7466);

          var min = Math.min; // `Array.prototype.copyWithin` method implementation
          // https://tc39.es/ecma262/#sec-array.prototype.copywithin

          module.exports = [].copyWithin || function copyWithin(target
          /* = 0 */
          , start
          /* = 0, end = @length */
          ) {
            var O = toObject(this);
            var len = toLength(O.length);
            var to = toAbsoluteIndex(target, len);
            var from = toAbsoluteIndex(start, len);
            var end = arguments.length > 2 ? arguments[2] : undefined;
            var count = min((end === undefined ? len : toAbsoluteIndex(end, len)) - from, len - to);
            var inc = 1;

            if (from < to && to < from + count) {
              inc = -1;
              from += count - 1;
              to += count - 1;
            }

            while (count-- > 0) {
              if (from in O) O[to] = O[from];else delete O[to];
              to += inc;
              from += inc;
            }

            return O;
          };
          /***/

        },

        /***/
        1285:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var toObject = __webpack_require__(7908);

          var toAbsoluteIndex = __webpack_require__(1400);

          var toLength = __webpack_require__(7466); // `Array.prototype.fill` method implementation
          // https://tc39.es/ecma262/#sec-array.prototype.fill


          module.exports = function fill(value
          /* , start = 0, end = @length */
          ) {
            var O = toObject(this);
            var length = toLength(O.length);
            var argumentsLength = arguments.length;
            var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
            var end = argumentsLength > 2 ? arguments[2] : undefined;
            var endPos = end === undefined ? length : toAbsoluteIndex(end, length);

            while (endPos > index) {
              O[index++] = value;
            }

            return O;
          };
          /***/

        },

        /***/
        8533:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $forEach = __webpack_require__(2092).forEach;

          var arrayMethodIsStrict = __webpack_require__(9341);

          var STRICT_METHOD = arrayMethodIsStrict('forEach'); // `Array.prototype.forEach` method implementation
          // https://tc39.es/ecma262/#sec-array.prototype.foreach

          module.exports = !STRICT_METHOD ? function forEach(callbackfn
          /* , thisArg */
          ) {
            return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
          } : [].forEach;
          /***/
        },

        /***/
        8457:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var bind = __webpack_require__(9974);

          var toObject = __webpack_require__(7908);

          var callWithSafeIterationClosing = __webpack_require__(3411);

          var isArrayIteratorMethod = __webpack_require__(7659);

          var toLength = __webpack_require__(7466);

          var createProperty = __webpack_require__(6135);

          var getIteratorMethod = __webpack_require__(1246); // `Array.from` method implementation
          // https://tc39.es/ecma262/#sec-array.from


          module.exports = function from(arrayLike
          /* , mapfn = undefined, thisArg = undefined */
          ) {
            var O = toObject(arrayLike);
            var C = typeof this == 'function' ? this : Array;
            var argumentsLength = arguments.length;
            var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
            var mapping = mapfn !== undefined;
            var iteratorMethod = getIteratorMethod(O);
            var index = 0;
            var length, result, step, iterator, next, value;
            if (mapping) mapfn = bind(mapfn, argumentsLength > 2 ? arguments[2] : undefined, 2); // if the target is not iterable or it's an array with the default iterator - use a simple case

            if (iteratorMethod != undefined && !(C == Array && isArrayIteratorMethod(iteratorMethod))) {
              iterator = iteratorMethod.call(O);
              next = iterator.next;
              result = new C();

              for (; !(step = next.call(iterator)).done; index++) {
                value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
                createProperty(result, index, value);
              }
            } else {
              length = toLength(O.length);
              result = new C(length);

              for (; length > index; index++) {
                value = mapping ? mapfn(O[index], index) : O[index];
                createProperty(result, index, value);
              }
            }

            result.length = index;
            return result;
          };
          /***/

        },

        /***/
        1318:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toIndexedObject = __webpack_require__(5656);

          var toLength = __webpack_require__(7466);

          var toAbsoluteIndex = __webpack_require__(1400); // `Array.prototype.{ indexOf, includes }` methods implementation


          var createMethod = function createMethod(IS_INCLUDES) {
            return function ($this, el, fromIndex) {
              var O = toIndexedObject($this);
              var length = toLength(O.length);
              var index = toAbsoluteIndex(fromIndex, length);
              var value; // Array#includes uses SameValueZero equality algorithm
              // eslint-disable-next-line no-self-compare -- NaN check

              if (IS_INCLUDES && el != el) while (length > index) {
                value = O[index++]; // eslint-disable-next-line no-self-compare -- NaN check

                if (value != value) return true; // Array#indexOf ignores holes, Array#includes - not
              } else for (; length > index; index++) {
                if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
              }
              return !IS_INCLUDES && -1;
            };
          };

          module.exports = {
            // `Array.prototype.includes` method
            // https://tc39.es/ecma262/#sec-array.prototype.includes
            includes: createMethod(true),
            // `Array.prototype.indexOf` method
            // https://tc39.es/ecma262/#sec-array.prototype.indexof
            indexOf: createMethod(false)
          };
          /***/
        },

        /***/
        2092:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var bind = __webpack_require__(9974);

          var IndexedObject = __webpack_require__(8361);

          var toObject = __webpack_require__(7908);

          var toLength = __webpack_require__(7466);

          var arraySpeciesCreate = __webpack_require__(5417);

          var push = [].push; // `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterOut }` methods implementation

          var createMethod = function createMethod(TYPE) {
            var IS_MAP = TYPE == 1;
            var IS_FILTER = TYPE == 2;
            var IS_SOME = TYPE == 3;
            var IS_EVERY = TYPE == 4;
            var IS_FIND_INDEX = TYPE == 6;
            var IS_FILTER_OUT = TYPE == 7;
            var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
            return function ($this, callbackfn, that, specificCreate) {
              var O = toObject($this);
              var self = IndexedObject(O);
              var boundFunction = bind(callbackfn, that, 3);
              var length = toLength(self.length);
              var index = 0;
              var create = specificCreate || arraySpeciesCreate;
              var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_OUT ? create($this, 0) : undefined;
              var value, result;

              for (; length > index; index++) {
                if (NO_HOLES || index in self) {
                  value = self[index];
                  result = boundFunction(value, index, O);

                  if (TYPE) {
                    if (IS_MAP) target[index] = result; // map
                    else if (result) switch (TYPE) {
                        case 3:
                          return true;
                        // some

                        case 5:
                          return value;
                        // find

                        case 6:
                          return index;
                        // findIndex

                        case 2:
                          push.call(target, value);
                        // filter
                      } else switch (TYPE) {
                        case 4:
                          return false;
                        // every

                        case 7:
                          push.call(target, value);
                        // filterOut
                      }
                  }
                }
              }

              return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
            };
          };

          module.exports = {
            // `Array.prototype.forEach` method
            // https://tc39.es/ecma262/#sec-array.prototype.foreach
            forEach: createMethod(0),
            // `Array.prototype.map` method
            // https://tc39.es/ecma262/#sec-array.prototype.map
            map: createMethod(1),
            // `Array.prototype.filter` method
            // https://tc39.es/ecma262/#sec-array.prototype.filter
            filter: createMethod(2),
            // `Array.prototype.some` method
            // https://tc39.es/ecma262/#sec-array.prototype.some
            some: createMethod(3),
            // `Array.prototype.every` method
            // https://tc39.es/ecma262/#sec-array.prototype.every
            every: createMethod(4),
            // `Array.prototype.find` method
            // https://tc39.es/ecma262/#sec-array.prototype.find
            find: createMethod(5),
            // `Array.prototype.findIndex` method
            // https://tc39.es/ecma262/#sec-array.prototype.findIndex
            findIndex: createMethod(6),
            // `Array.prototype.filterOut` method
            // https://github.com/tc39/proposal-array-filtering
            filterOut: createMethod(7)
          };
          /***/
        },

        /***/
        6583:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var toIndexedObject = __webpack_require__(5656);

          var toInteger = __webpack_require__(9958);

          var toLength = __webpack_require__(7466);

          var arrayMethodIsStrict = __webpack_require__(9341);

          var min = Math.min;
          var nativeLastIndexOf = [].lastIndexOf;
          var NEGATIVE_ZERO = !!nativeLastIndexOf && 1 / [1].lastIndexOf(1, -0) < 0;
          var STRICT_METHOD = arrayMethodIsStrict('lastIndexOf');
          var FORCED = NEGATIVE_ZERO || !STRICT_METHOD; // `Array.prototype.lastIndexOf` method implementation
          // https://tc39.es/ecma262/#sec-array.prototype.lastindexof

          module.exports = FORCED ? function lastIndexOf(searchElement
          /* , fromIndex = @[*-1] */
          ) {
            // convert -0 to +0
            if (NEGATIVE_ZERO) return nativeLastIndexOf.apply(this, arguments) || 0;
            var O = toIndexedObject(this);
            var length = toLength(O.length);
            var index = length - 1;
            if (arguments.length > 1) index = min(index, toInteger(arguments[1]));
            if (index < 0) index = length + index;

            for (; index >= 0; index--) {
              if (index in O && O[index] === searchElement) return index || 0;
            }

            return -1;
          } : nativeLastIndexOf;
          /***/
        },

        /***/
        1194:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          var wellKnownSymbol = __webpack_require__(5112);

          var V8_VERSION = __webpack_require__(7392);

          var SPECIES = wellKnownSymbol('species');

          module.exports = function (METHOD_NAME) {
            // We can't use this feature detection in V8 since it causes
            // deoptimization and serious performance degradation
            // https://github.com/zloirock/core-js/issues/677
            return V8_VERSION >= 51 || !fails(function () {
              var array = [];
              var constructor = array.constructor = {};

              constructor[SPECIES] = function () {
                return {
                  foo: 1
                };
              };

              return array[METHOD_NAME](Boolean).foo !== 1;
            });
          };
          /***/

        },

        /***/
        9341:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var fails = __webpack_require__(7293);

          module.exports = function (METHOD_NAME, argument) {
            var method = [][METHOD_NAME];
            return !!method && fails(function () {
              // eslint-disable-next-line no-useless-call,no-throw-literal -- required for testing
              method.call(null, argument || function () {
                throw 1;
              }, 1);
            });
          };
          /***/

        },

        /***/
        3671:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var aFunction = __webpack_require__(3099);

          var toObject = __webpack_require__(7908);

          var IndexedObject = __webpack_require__(8361);

          var toLength = __webpack_require__(7466); // `Array.prototype.{ reduce, reduceRight }` methods implementation


          var createMethod = function createMethod(IS_RIGHT) {
            return function (that, callbackfn, argumentsLength, memo) {
              aFunction(callbackfn);
              var O = toObject(that);
              var self = IndexedObject(O);
              var length = toLength(O.length);
              var index = IS_RIGHT ? length - 1 : 0;
              var i = IS_RIGHT ? -1 : 1;
              if (argumentsLength < 2) while (true) {
                if (index in self) {
                  memo = self[index];
                  index += i;
                  break;
                }

                index += i;

                if (IS_RIGHT ? index < 0 : length <= index) {
                  throw TypeError('Reduce of empty array with no initial value');
                }
              }

              for (; IS_RIGHT ? index >= 0 : length > index; index += i) {
                if (index in self) {
                  memo = callbackfn(memo, self[index], index, O);
                }
              }

              return memo;
            };
          };

          module.exports = {
            // `Array.prototype.reduce` method
            // https://tc39.es/ecma262/#sec-array.prototype.reduce
            left: createMethod(false),
            // `Array.prototype.reduceRight` method
            // https://tc39.es/ecma262/#sec-array.prototype.reduceright
            right: createMethod(true)
          };
          /***/
        },

        /***/
        5417:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var isObject = __webpack_require__(111);

          var isArray = __webpack_require__(3157);

          var wellKnownSymbol = __webpack_require__(5112);

          var SPECIES = wellKnownSymbol('species'); // `ArraySpeciesCreate` abstract operation
          // https://tc39.es/ecma262/#sec-arrayspeciescreate

          module.exports = function (originalArray, length) {
            var C;

            if (isArray(originalArray)) {
              C = originalArray.constructor; // cross-realm fallback

              if (typeof C == 'function' && (C === Array || isArray(C.prototype))) C = undefined;else if (isObject(C)) {
                C = C[SPECIES];
                if (C === null) C = undefined;
              }
            }

            return new (C === undefined ? Array : C)(length === 0 ? 0 : length);
          };
          /***/

        },

        /***/
        3411:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var anObject = __webpack_require__(9670);

          var iteratorClose = __webpack_require__(9212); // call something on iterator step with safe closing on error


          module.exports = function (iterator, fn, value, ENTRIES) {
            try {
              return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value); // 7.4.6 IteratorClose(iterator, completion)
            } catch (error) {
              iteratorClose(iterator);
              throw error;
            }
          };
          /***/

        },

        /***/
        7072:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var wellKnownSymbol = __webpack_require__(5112);

          var ITERATOR = wellKnownSymbol('iterator');
          var SAFE_CLOSING = false;

          try {
            var called = 0;
            var iteratorWithReturn = {
              next: function next() {
                return {
                  done: !!called++
                };
              },
              'return': function _return() {
                SAFE_CLOSING = true;
              }
            };

            iteratorWithReturn[ITERATOR] = function () {
              return this;
            }; // eslint-disable-next-line no-throw-literal -- required for testing


            Array.from(iteratorWithReturn, function () {
              throw 2;
            });
          } catch (error) {
            /* empty */
          }

          module.exports = function (exec, SKIP_CLOSING) {
            if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
            var ITERATION_SUPPORT = false;

            try {
              var object = {};

              object[ITERATOR] = function () {
                return {
                  next: function next() {
                    return {
                      done: ITERATION_SUPPORT = true
                    };
                  }
                };
              };

              exec(object);
            } catch (error) {
              /* empty */
            }

            return ITERATION_SUPPORT;
          };
          /***/

        },

        /***/
        4326:
        /***/
        function _(module) {
          var toString = {}.toString;

          module.exports = function (it) {
            return toString.call(it).slice(8, -1);
          };
          /***/

        },

        /***/
        648:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var TO_STRING_TAG_SUPPORT = __webpack_require__(1694);

          var classofRaw = __webpack_require__(4326);

          var wellKnownSymbol = __webpack_require__(5112);

          var TO_STRING_TAG = wellKnownSymbol('toStringTag'); // ES3 wrong here

          var CORRECT_ARGUMENTS = classofRaw(function () {
            return arguments;
          }()) == 'Arguments'; // fallback for IE11 Script Access Denied error

          var tryGet = function tryGet(it, key) {
            try {
              return it[key];
            } catch (error) {
              /* empty */
            }
          }; // getting tag from ES6+ `Object.prototype.toString`


          module.exports = TO_STRING_TAG_SUPPORT ? classofRaw : function (it) {
            var O, tag, result;
            return it === undefined ? 'Undefined' : it === null ? 'Null' // @@toStringTag case
            : typeof (tag = tryGet(O = Object(it), TO_STRING_TAG)) == 'string' ? tag // builtinTag case
            : CORRECT_ARGUMENTS ? classofRaw(O) // ES3 arguments fallback
            : (result = classofRaw(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : result;
          };
          /***/
        },

        /***/
        9920:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var has = __webpack_require__(6656);

          var ownKeys = __webpack_require__(3887);

          var getOwnPropertyDescriptorModule = __webpack_require__(1236);

          var definePropertyModule = __webpack_require__(3070);

          module.exports = function (target, source) {
            var keys = ownKeys(source);
            var defineProperty = definePropertyModule.f;
            var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;

            for (var i = 0; i < keys.length; i++) {
              var key = keys[i];
              if (!has(target, key)) defineProperty(target, key, getOwnPropertyDescriptor(source, key));
            }
          };
          /***/

        },

        /***/
        8544:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          module.exports = !fails(function () {
            function F() {
              /* empty */
            }

            F.prototype.constructor = null;
            return Object.getPrototypeOf(new F()) !== F.prototype;
          });
          /***/
        },

        /***/
        4994:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var IteratorPrototype = __webpack_require__(3383).IteratorPrototype;

          var create = __webpack_require__(30);

          var createPropertyDescriptor = __webpack_require__(9114);

          var setToStringTag = __webpack_require__(8003);

          var Iterators = __webpack_require__(7497);

          var returnThis = function returnThis() {
            return this;
          };

          module.exports = function (IteratorConstructor, NAME, next) {
            var TO_STRING_TAG = NAME + ' Iterator';
            IteratorConstructor.prototype = create(IteratorPrototype, {
              next: createPropertyDescriptor(1, next)
            });
            setToStringTag(IteratorConstructor, TO_STRING_TAG, false, true);
            Iterators[TO_STRING_TAG] = returnThis;
            return IteratorConstructor;
          };
          /***/

        },

        /***/
        8880:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var DESCRIPTORS = __webpack_require__(9781);

          var definePropertyModule = __webpack_require__(3070);

          var createPropertyDescriptor = __webpack_require__(9114);

          module.exports = DESCRIPTORS ? function (object, key, value) {
            return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
          } : function (object, key, value) {
            object[key] = value;
            return object;
          };
          /***/
        },

        /***/
        9114:
        /***/
        function _(module) {
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
        6135:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var toPrimitive = __webpack_require__(7593);

          var definePropertyModule = __webpack_require__(3070);

          var createPropertyDescriptor = __webpack_require__(9114);

          module.exports = function (object, key, value) {
            var propertyKey = toPrimitive(key);
            if (propertyKey in object) definePropertyModule.f(object, propertyKey, createPropertyDescriptor(0, value));else object[propertyKey] = value;
          };
          /***/

        },

        /***/
        654:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var createIteratorConstructor = __webpack_require__(4994);

          var getPrototypeOf = __webpack_require__(9518);

          var setPrototypeOf = __webpack_require__(7674);

          var setToStringTag = __webpack_require__(8003);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var redefine = __webpack_require__(1320);

          var wellKnownSymbol = __webpack_require__(5112);

          var IS_PURE = __webpack_require__(1913);

          var Iterators = __webpack_require__(7497);

          var IteratorsCore = __webpack_require__(3383);

          var IteratorPrototype = IteratorsCore.IteratorPrototype;
          var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
          var ITERATOR = wellKnownSymbol('iterator');
          var KEYS = 'keys';
          var VALUES = 'values';
          var ENTRIES = 'entries';

          var returnThis = function returnThis() {
            return this;
          };

          module.exports = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
            createIteratorConstructor(IteratorConstructor, NAME, next);

            var getIterationMethod = function getIterationMethod(KIND) {
              if (KIND === DEFAULT && defaultIterator) return defaultIterator;
              if (!BUGGY_SAFARI_ITERATORS && KIND in IterablePrototype) return IterablePrototype[KIND];

              switch (KIND) {
                case KEYS:
                  return function keys() {
                    return new IteratorConstructor(this, KIND);
                  };

                case VALUES:
                  return function values() {
                    return new IteratorConstructor(this, KIND);
                  };

                case ENTRIES:
                  return function entries() {
                    return new IteratorConstructor(this, KIND);
                  };
              }

              return function () {
                return new IteratorConstructor(this);
              };
            };

            var TO_STRING_TAG = NAME + ' Iterator';
            var INCORRECT_VALUES_NAME = false;
            var IterablePrototype = Iterable.prototype;
            var nativeIterator = IterablePrototype[ITERATOR] || IterablePrototype['@@iterator'] || DEFAULT && IterablePrototype[DEFAULT];
            var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
            var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
            var CurrentIteratorPrototype, methods, KEY; // fix native

            if (anyNativeIterator) {
              CurrentIteratorPrototype = getPrototypeOf(anyNativeIterator.call(new Iterable()));

              if (IteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
                if (!IS_PURE && getPrototypeOf(CurrentIteratorPrototype) !== IteratorPrototype) {
                  if (setPrototypeOf) {
                    setPrototypeOf(CurrentIteratorPrototype, IteratorPrototype);
                  } else if (typeof CurrentIteratorPrototype[ITERATOR] != 'function') {
                    createNonEnumerableProperty(CurrentIteratorPrototype, ITERATOR, returnThis);
                  }
                } // Set @@toStringTag to native iterators


                setToStringTag(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
                if (IS_PURE) Iterators[TO_STRING_TAG] = returnThis;
              }
            } // fix Array#{values, @@iterator}.name in V8 / FF


            if (DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
              INCORRECT_VALUES_NAME = true;

              defaultIterator = function values() {
                return nativeIterator.call(this);
              };
            } // define iterator


            if ((!IS_PURE || FORCED) && IterablePrototype[ITERATOR] !== defaultIterator) {
              createNonEnumerableProperty(IterablePrototype, ITERATOR, defaultIterator);
            }

            Iterators[NAME] = defaultIterator; // export additional methods

            if (DEFAULT) {
              methods = {
                values: getIterationMethod(VALUES),
                keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
                entries: getIterationMethod(ENTRIES)
              };
              if (FORCED) for (KEY in methods) {
                if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
                  redefine(IterablePrototype, KEY, methods[KEY]);
                }
              } else $({
                target: NAME,
                proto: true,
                forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME
              }, methods);
            }

            return methods;
          };
          /***/

        },

        /***/
        9781:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293); // Detect IE8's incomplete defineProperty implementation


          module.exports = !fails(function () {
            return Object.defineProperty({}, 1, {
              get: function get() {
                return 7;
              }
            })[1] != 7;
          });
          /***/
        },

        /***/
        317:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var isObject = __webpack_require__(111);

          var document = global.document; // typeof document.createElement is 'object' in old IE

          var EXISTS = isObject(document) && isObject(document.createElement);

          module.exports = function (it) {
            return EXISTS ? document.createElement(it) : {};
          };
          /***/

        },

        /***/
        8324:
        /***/
        function _(module) {
          // iterable DOM collections
          // flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
          module.exports = {
            CSSRuleList: 0,
            CSSStyleDeclaration: 0,
            CSSValueList: 0,
            ClientRectList: 0,
            DOMRectList: 0,
            DOMStringList: 0,
            DOMTokenList: 1,
            DataTransferItemList: 0,
            FileList: 0,
            HTMLAllCollection: 0,
            HTMLCollection: 0,
            HTMLFormElement: 0,
            HTMLSelectElement: 0,
            MediaList: 0,
            MimeTypeArray: 0,
            NamedNodeMap: 0,
            NodeList: 1,
            PaintRequestList: 0,
            Plugin: 0,
            PluginArray: 0,
            SVGLengthList: 0,
            SVGNumberList: 0,
            SVGPathSegList: 0,
            SVGPointList: 0,
            SVGStringList: 0,
            SVGTransformList: 0,
            SourceBufferList: 0,
            StyleSheetList: 0,
            TextTrackCueList: 0,
            TextTrackList: 0,
            TouchList: 0
          };
          /***/
        },

        /***/
        8113:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var getBuiltIn = __webpack_require__(5005);

          module.exports = getBuiltIn('navigator', 'userAgent') || '';
          /***/
        },

        /***/
        7392:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var userAgent = __webpack_require__(8113);

          var process = global.process;
          var versions = process && process.versions;
          var v8 = versions && versions.v8;
          var match, version;

          if (v8) {
            match = v8.split('.');
            version = match[0] + match[1];
          } else if (userAgent) {
            match = userAgent.match(/Edge\/(\d+)/);

            if (!match || match[1] >= 74) {
              match = userAgent.match(/Chrome\/(\d+)/);
              if (match) version = match[1];
            }
          }

          module.exports = version && +version;
          /***/
        },

        /***/
        748:
        /***/
        function _(module) {
          // IE8- don't enum bug keys
          module.exports = ['constructor', 'hasOwnProperty', 'isPrototypeOf', 'propertyIsEnumerable', 'toLocaleString', 'toString', 'valueOf'];
          /***/
        },

        /***/
        2109:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var getOwnPropertyDescriptor = __webpack_require__(1236).f;

          var createNonEnumerableProperty = __webpack_require__(8880);

          var redefine = __webpack_require__(1320);

          var setGlobal = __webpack_require__(3505);

          var copyConstructorProperties = __webpack_require__(9920);

          var isForced = __webpack_require__(4705);
          /*
            options.target      - name of the target object
            options.global      - target is the global object
            options.stat        - export as static methods of target
            options.proto       - export as prototype methods of target
            options.real        - real prototype method for the `pure` version
            options.forced      - export even if the native feature is available
            options.bind        - bind methods to the target, required for the `pure` version
            options.wrap        - wrap constructors to preventing global pollution, required for the `pure` version
            options.unsafe      - use the simple assignment of property instead of delete + defineProperty
            options.sham        - add a flag to not completely full polyfills
            options.enumerable  - export as enumerable property
            options.noTargetGet - prevent calling a getter on target
          */


          module.exports = function (options, source) {
            var TARGET = options.target;
            var GLOBAL = options.global;
            var STATIC = options.stat;
            var FORCED, target, key, targetProperty, sourceProperty, descriptor;

            if (GLOBAL) {
              target = global;
            } else if (STATIC) {
              target = global[TARGET] || setGlobal(TARGET, {});
            } else {
              target = (global[TARGET] || {}).prototype;
            }

            if (target) for (key in source) {
              sourceProperty = source[key];

              if (options.noTargetGet) {
                descriptor = getOwnPropertyDescriptor(target, key);
                targetProperty = descriptor && descriptor.value;
              } else targetProperty = target[key];

              FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced); // contained in target

              if (!FORCED && targetProperty !== undefined) {
                if (_typeof2(sourceProperty) === _typeof2(targetProperty)) continue;
                copyConstructorProperties(sourceProperty, targetProperty);
              } // add a flag to not completely full polyfills


              if (options.sham || targetProperty && targetProperty.sham) {
                createNonEnumerableProperty(sourceProperty, 'sham', true);
              } // extend global


              redefine(target, key, sourceProperty, options);
            }
          };
          /***/

        },

        /***/
        7293:
        /***/
        function _(module) {
          module.exports = function (exec) {
            try {
              return !!exec();
            } catch (error) {
              return true;
            }
          };
          /***/

        },

        /***/
        7007:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict"; // TODO: Remove from `core-js@4` since it's moved to entry points

          __webpack_require__(4916);

          var redefine = __webpack_require__(1320);

          var fails = __webpack_require__(7293);

          var wellKnownSymbol = __webpack_require__(5112);

          var regexpExec = __webpack_require__(2261);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var SPECIES = wellKnownSymbol('species');
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
          }); // IE <= 11 replaces $0 with the whole match, as if it was $&
          // https://stackoverflow.com/questions/6024666/getting-ie-to-replace-a-regex-with-the-literal-string-0

          var REPLACE_KEEPS_$0 = function () {
            return 'a'.replace(/./, '$0') === '$0';
          }();

          var REPLACE = wellKnownSymbol('replace'); // Safari <= 13.0.3(?) substitutes nth capture where n>m with an empty string

          var REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE = function () {
            if (/./[REPLACE]) {
              return /./[REPLACE]('a', '$0') === '';
            }

            return false;
          }(); // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
          // Weex JS has frozen built-in prototypes, so use try / catch wrapper


          var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = !fails(function () {
            // eslint-disable-next-line regexp/no-empty-group -- required for testing
            var re = /(?:)/;
            var originalExec = re.exec;

            re.exec = function () {
              return originalExec.apply(this, arguments);
            };

            var result = 'ab'.split(re);
            return result.length !== 2 || result[0] !== 'a' || result[1] !== 'b';
          });

          module.exports = function (KEY, length, exec, sham) {
            var SYMBOL = wellKnownSymbol(KEY);
            var DELEGATES_TO_SYMBOL = !fails(function () {
              // String methods call symbol-named RegEp methods
              var O = {};

              O[SYMBOL] = function () {
                return 7;
              };

              return ''[KEY](O) != 7;
            });
            var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL && !fails(function () {
              // Symbol-named RegExp methods call .exec
              var execCalled = false;
              var re = /a/;

              if (KEY === 'split') {
                // We can't use real regex here since it causes deoptimization
                // and serious performance degradation in V8
                // https://github.com/zloirock/core-js/issues/306
                re = {}; // RegExp[@@split] doesn't call the regex's exec method, but first creates
                // a new one. We need to return the patched regex when creating the new one.

                re.constructor = {};

                re.constructor[SPECIES] = function () {
                  return re;
                };

                re.flags = '';
                re[SYMBOL] = /./[SYMBOL];
              }

              re.exec = function () {
                execCalled = true;
                return null;
              };

              re[SYMBOL]('');
              return !execCalled;
            });

            if (!DELEGATES_TO_SYMBOL || !DELEGATES_TO_EXEC || KEY === 'replace' && !(REPLACE_SUPPORTS_NAMED_GROUPS && REPLACE_KEEPS_$0 && !REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE) || KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC) {
              var nativeRegExpMethod = /./[SYMBOL];
              var methods = exec(SYMBOL, ''[KEY], function (nativeMethod, regexp, str, arg2, forceStringMethod) {
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
              }, {
                REPLACE_KEEPS_$0: REPLACE_KEEPS_$0,
                REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE: REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE
              });
              var stringMethod = methods[0];
              var regexMethod = methods[1];
              redefine(String.prototype, KEY, stringMethod);
              redefine(RegExp.prototype, SYMBOL, length == 2 // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
              // 21.2.5.11 RegExp.prototype[@@split](string, limit)
              ? function (string, arg) {
                return regexMethod.call(string, this, arg);
              } // 21.2.5.6 RegExp.prototype[@@match](string)
              // 21.2.5.9 RegExp.prototype[@@search](string)
              : function (string) {
                return regexMethod.call(string, this);
              });
            }

            if (sham) createNonEnumerableProperty(RegExp.prototype[SYMBOL], 'sham', true);
          };
          /***/

        },

        /***/
        9974:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var aFunction = __webpack_require__(3099); // optional / simple context binding


          module.exports = function (fn, that, length) {
            aFunction(fn);
            if (that === undefined) return fn;

            switch (length) {
              case 0:
                return function () {
                  return fn.call(that);
                };

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

            return function ()
            /* ...args */
            {
              return fn.apply(that, arguments);
            };
          };
          /***/

        },

        /***/
        5005:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var path = __webpack_require__(857);

          var global = __webpack_require__(7854);

          var aFunction = function aFunction(variable) {
            return typeof variable == 'function' ? variable : undefined;
          };

          module.exports = function (namespace, method) {
            return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(global[namespace]) : path[namespace] && path[namespace][method] || global[namespace] && global[namespace][method];
          };
          /***/

        },

        /***/
        1246:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var classof = __webpack_require__(648);

          var Iterators = __webpack_require__(7497);

          var wellKnownSymbol = __webpack_require__(5112);

          var ITERATOR = wellKnownSymbol('iterator');

          module.exports = function (it) {
            if (it != undefined) return it[ITERATOR] || it['@@iterator'] || Iterators[classof(it)];
          };
          /***/

        },

        /***/
        8554:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var anObject = __webpack_require__(9670);

          var getIteratorMethod = __webpack_require__(1246);

          module.exports = function (it) {
            var iteratorMethod = getIteratorMethod(it);

            if (typeof iteratorMethod != 'function') {
              throw TypeError(String(it) + ' is not iterable');
            }

            return anObject(iteratorMethod.call(it));
          };
          /***/

        },

        /***/
        647:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toObject = __webpack_require__(7908);

          var floor = Math.floor;
          var replace = ''.replace;
          var SUBSTITUTION_SYMBOLS = /\$([$&'`]|\d\d?|<[^>]*>)/g;
          var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&'`]|\d\d?)/g; // https://tc39.es/ecma262/#sec-getsubstitution

          module.exports = function (matched, str, position, captures, namedCaptures, replacement) {
            var tailPos = position + matched.length;
            var m = captures.length;
            var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;

            if (namedCaptures !== undefined) {
              namedCaptures = toObject(namedCaptures);
              symbols = SUBSTITUTION_SYMBOLS;
            }

            return replace.call(replacement, symbols, function (match, ch) {
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
          };
          /***/

        },

        /***/
        7854:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var check = function check(it) {
            return it && it.Math == Math && it;
          }; // https://github.com/zloirock/core-js/issues/86#issuecomment-115759028


          module.exports =
          /* global globalThis -- safe */
          check((typeof globalThis === "undefined" ? "undefined" : _typeof2(globalThis)) == 'object' && globalThis) || check((typeof window === "undefined" ? "undefined" : _typeof2(window)) == 'object' && window) || check((typeof self === "undefined" ? "undefined" : _typeof2(self)) == 'object' && self) || check(_typeof2(__webpack_require__.g) == 'object' && __webpack_require__.g) || // eslint-disable-next-line no-new-func -- fallback
          function () {
            return this;
          }() || Function('return this')();
          /***/

        },

        /***/
        6656:
        /***/
        function _(module) {
          var hasOwnProperty = {}.hasOwnProperty;

          module.exports = function (it, key) {
            return hasOwnProperty.call(it, key);
          };
          /***/

        },

        /***/
        3501:
        /***/
        function _(module) {
          module.exports = {};
          /***/
        },

        /***/
        490:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var getBuiltIn = __webpack_require__(5005);

          module.exports = getBuiltIn('document', 'documentElement');
          /***/
        },

        /***/
        4664:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var DESCRIPTORS = __webpack_require__(9781);

          var fails = __webpack_require__(7293);

          var createElement = __webpack_require__(317); // Thank's IE8 for his funny defineProperty


          module.exports = !DESCRIPTORS && !fails(function () {
            return Object.defineProperty(createElement('div'), 'a', {
              get: function get() {
                return 7;
              }
            }).a != 7;
          });
          /***/
        },

        /***/
        1179:
        /***/
        function _(module) {
          // IEEE754 conversions based on https://github.com/feross/ieee754
          var abs = Math.abs;
          var pow = Math.pow;
          var floor = Math.floor;
          var log = Math.log;
          var LN2 = Math.LN2;

          var pack = function pack(number, mantissaLength, bytes) {
            var buffer = new Array(bytes);
            var exponentLength = bytes * 8 - mantissaLength - 1;
            var eMax = (1 << exponentLength) - 1;
            var eBias = eMax >> 1;
            var rt = mantissaLength === 23 ? pow(2, -24) - pow(2, -77) : 0;
            var sign = number < 0 || number === 0 && 1 / number < 0 ? 1 : 0;
            var index = 0;
            var exponent, mantissa, c;
            number = abs(number); // eslint-disable-next-line no-self-compare -- NaN check

            if (number != number || number === Infinity) {
              // eslint-disable-next-line no-self-compare -- NaN check
              mantissa = number != number ? 1 : 0;
              exponent = eMax;
            } else {
              exponent = floor(log(number) / LN2);

              if (number * (c = pow(2, -exponent)) < 1) {
                exponent--;
                c *= 2;
              }

              if (exponent + eBias >= 1) {
                number += rt / c;
              } else {
                number += rt * pow(2, 1 - eBias);
              }

              if (number * c >= 2) {
                exponent++;
                c /= 2;
              }

              if (exponent + eBias >= eMax) {
                mantissa = 0;
                exponent = eMax;
              } else if (exponent + eBias >= 1) {
                mantissa = (number * c - 1) * pow(2, mantissaLength);
                exponent = exponent + eBias;
              } else {
                mantissa = number * pow(2, eBias - 1) * pow(2, mantissaLength);
                exponent = 0;
              }
            }

            for (; mantissaLength >= 8; buffer[index++] = mantissa & 255, mantissa /= 256, mantissaLength -= 8) {
              ;
            }

            exponent = exponent << mantissaLength | mantissa;
            exponentLength += mantissaLength;

            for (; exponentLength > 0; buffer[index++] = exponent & 255, exponent /= 256, exponentLength -= 8) {
              ;
            }

            buffer[--index] |= sign * 128;
            return buffer;
          };

          var unpack = function unpack(buffer, mantissaLength) {
            var bytes = buffer.length;
            var exponentLength = bytes * 8 - mantissaLength - 1;
            var eMax = (1 << exponentLength) - 1;
            var eBias = eMax >> 1;
            var nBits = exponentLength - 7;
            var index = bytes - 1;
            var sign = buffer[index--];
            var exponent = sign & 127;
            var mantissa;
            sign >>= 7;

            for (; nBits > 0; exponent = exponent * 256 + buffer[index], index--, nBits -= 8) {
              ;
            }

            mantissa = exponent & (1 << -nBits) - 1;
            exponent >>= -nBits;
            nBits += mantissaLength;

            for (; nBits > 0; mantissa = mantissa * 256 + buffer[index], index--, nBits -= 8) {
              ;
            }

            if (exponent === 0) {
              exponent = 1 - eBias;
            } else if (exponent === eMax) {
              return mantissa ? NaN : sign ? -Infinity : Infinity;
            } else {
              mantissa = mantissa + pow(2, mantissaLength);
              exponent = exponent - eBias;
            }

            return (sign ? -1 : 1) * mantissa * pow(2, exponent - mantissaLength);
          };

          module.exports = {
            pack: pack,
            unpack: unpack
          };
          /***/
        },

        /***/
        8361:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          var classof = __webpack_require__(4326);

          var split = ''.split; // fallback for non-array-like ES3 and non-enumerable old V8 strings

          module.exports = fails(function () {
            // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
            // eslint-disable-next-line no-prototype-builtins -- safe
            return !Object('z').propertyIsEnumerable(0);
          }) ? function (it) {
            return classof(it) == 'String' ? split.call(it, '') : Object(it);
          } : Object;
          /***/
        },

        /***/
        9587:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var isObject = __webpack_require__(111);

          var setPrototypeOf = __webpack_require__(7674); // makes subclassing work correct for wrapped built-ins


          module.exports = function ($this, dummy, Wrapper) {
            var NewTarget, NewTargetPrototype;
            if ( // it can work only with native `setPrototypeOf`
            setPrototypeOf && // we haven't completely correct pre-ES6 way for getting `new.target`, so use this
            typeof (NewTarget = dummy.constructor) == 'function' && NewTarget !== Wrapper && isObject(NewTargetPrototype = NewTarget.prototype) && NewTargetPrototype !== Wrapper.prototype) setPrototypeOf($this, NewTargetPrototype);
            return $this;
          };
          /***/

        },

        /***/
        2788:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var store = __webpack_require__(5465);

          var functionToString = Function.toString; // this helper broken in `3.4.1-3.4.4`, so we can't use `shared` helper

          if (typeof store.inspectSource != 'function') {
            store.inspectSource = function (it) {
              return functionToString.call(it);
            };
          }

          module.exports = store.inspectSource;
          /***/
        },

        /***/
        9909:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var NATIVE_WEAK_MAP = __webpack_require__(8536);

          var global = __webpack_require__(7854);

          var isObject = __webpack_require__(111);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var objectHas = __webpack_require__(6656);

          var shared = __webpack_require__(5465);

          var sharedKey = __webpack_require__(6200);

          var hiddenKeys = __webpack_require__(3501);

          var WeakMap = global.WeakMap;
          var set, get, has;

          var enforce = function enforce(it) {
            return has(it) ? get(it) : set(it, {});
          };

          var getterFor = function getterFor(TYPE) {
            return function (it) {
              var state;

              if (!isObject(it) || (state = get(it)).type !== TYPE) {
                throw TypeError('Incompatible receiver, ' + TYPE + ' required');
              }

              return state;
            };
          };

          if (NATIVE_WEAK_MAP) {
            var store = shared.state || (shared.state = new WeakMap());
            var wmget = store.get;
            var wmhas = store.has;
            var wmset = store.set;

            set = function set(it, metadata) {
              metadata.facade = it;
              wmset.call(store, it, metadata);
              return metadata;
            };

            get = function get(it) {
              return wmget.call(store, it) || {};
            };

            has = function has(it) {
              return wmhas.call(store, it);
            };
          } else {
            var STATE = sharedKey('state');
            hiddenKeys[STATE] = true;

            set = function set(it, metadata) {
              metadata.facade = it;
              createNonEnumerableProperty(it, STATE, metadata);
              return metadata;
            };

            get = function get(it) {
              return objectHas(it, STATE) ? it[STATE] : {};
            };

            has = function has(it) {
              return objectHas(it, STATE);
            };
          }

          module.exports = {
            set: set,
            get: get,
            has: has,
            enforce: enforce,
            getterFor: getterFor
          };
          /***/
        },

        /***/
        7659:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var wellKnownSymbol = __webpack_require__(5112);

          var Iterators = __webpack_require__(7497);

          var ITERATOR = wellKnownSymbol('iterator');
          var ArrayPrototype = Array.prototype; // check on default Array iterator

          module.exports = function (it) {
            return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
          };
          /***/

        },

        /***/
        3157:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var classof = __webpack_require__(4326); // `IsArray` abstract operation
          // https://tc39.es/ecma262/#sec-isarray


          module.exports = Array.isArray || function isArray(arg) {
            return classof(arg) == 'Array';
          };
          /***/

        },

        /***/
        4705:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          var replacement = /#|\.prototype\./;

          var isForced = function isForced(feature, detection) {
            var value = data[normalize(feature)];
            return value == POLYFILL ? true : value == NATIVE ? false : typeof detection == 'function' ? fails(detection) : !!detection;
          };

          var normalize = isForced.normalize = function (string) {
            return String(string).replace(replacement, '.').toLowerCase();
          };

          var data = isForced.data = {};
          var NATIVE = isForced.NATIVE = 'N';
          var POLYFILL = isForced.POLYFILL = 'P';
          module.exports = isForced;
          /***/
        },

        /***/
        111:
        /***/
        function _(module) {
          module.exports = function (it) {
            return _typeof2(it) === 'object' ? it !== null : typeof it === 'function';
          };
          /***/

        },

        /***/
        1913:
        /***/
        function _(module) {
          module.exports = false;
          /***/
        },

        /***/
        7850:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var isObject = __webpack_require__(111);

          var classof = __webpack_require__(4326);

          var wellKnownSymbol = __webpack_require__(5112);

          var MATCH = wellKnownSymbol('match'); // `IsRegExp` abstract operation
          // https://tc39.es/ecma262/#sec-isregexp

          module.exports = function (it) {
            var isRegExp;
            return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : classof(it) == 'RegExp');
          };
          /***/

        },

        /***/
        9212:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var anObject = __webpack_require__(9670);

          module.exports = function (iterator) {
            var returnMethod = iterator['return'];

            if (returnMethod !== undefined) {
              return anObject(returnMethod.call(iterator)).value;
            }
          };
          /***/

        },

        /***/
        3383:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var fails = __webpack_require__(7293);

          var getPrototypeOf = __webpack_require__(9518);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var has = __webpack_require__(6656);

          var wellKnownSymbol = __webpack_require__(5112);

          var IS_PURE = __webpack_require__(1913);

          var ITERATOR = wellKnownSymbol('iterator');
          var BUGGY_SAFARI_ITERATORS = false;

          var returnThis = function returnThis() {
            return this;
          }; // `%IteratorPrototype%` object
          // https://tc39.es/ecma262/#sec-%iteratorprototype%-object


          var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

          if ([].keys) {
            arrayIterator = [].keys(); // Safari 8 has buggy iterators w/o `next`

            if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;else {
              PrototypeOfArrayIteratorPrototype = getPrototypeOf(getPrototypeOf(arrayIterator));
              if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
            }
          }

          var NEW_ITERATOR_PROTOTYPE = IteratorPrototype == undefined || fails(function () {
            var test = {}; // FF44- legacy iterators case

            return IteratorPrototype[ITERATOR].call(test) !== test;
          });
          if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype = {}; // 25.1.2.1.1 %IteratorPrototype%[@@iterator]()

          if ((!IS_PURE || NEW_ITERATOR_PROTOTYPE) && !has(IteratorPrototype, ITERATOR)) {
            createNonEnumerableProperty(IteratorPrototype, ITERATOR, returnThis);
          }

          module.exports = {
            IteratorPrototype: IteratorPrototype,
            BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
          };
          /***/
        },

        /***/
        7497:
        /***/
        function _(module) {
          module.exports = {};
          /***/
        },

        /***/
        133:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          module.exports = !!Object.getOwnPropertySymbols && !fails(function () {
            // Chrome 38 Symbol has incorrect toString conversion

            /* global Symbol -- required for testing */
            return !String(Symbol());
          });
          /***/
        },

        /***/
        590:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          var wellKnownSymbol = __webpack_require__(5112);

          var IS_PURE = __webpack_require__(1913);

          var ITERATOR = wellKnownSymbol('iterator');
          module.exports = !fails(function () {
            var url = new URL('b?a=1&b=2&c=3', 'http://a');
            var searchParams = url.searchParams;
            var result = '';
            url.pathname = 'c%20d';
            searchParams.forEach(function (value, key) {
              searchParams['delete']('b');
              result += key + value;
            });
            return IS_PURE && !url.toJSON || !searchParams.sort || url.href !== 'http://a/c%20d?a=1&c=3' || searchParams.get('c') !== '3' || String(new URLSearchParams('?a=1')) !== 'a=1' || !searchParams[ITERATOR] // throws in Edge
            || new URL('https://a@b').username !== 'a' || new URLSearchParams(new URLSearchParams('a=b')).get('a') !== 'b' // not punycoded in Edge
            || new URL('http://тест').host !== 'xn--e1aybc' // not escaped in Chrome 62-
            || new URL('http://a#б').hash !== '#%D0%B1' // fails in Chrome 66-
            || result !== 'a1c3' // throws in Safari
            || new URL('http://x', undefined).host !== 'x';
          });
          /***/
        },

        /***/
        8536:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var inspectSource = __webpack_require__(2788);

          var WeakMap = global.WeakMap;
          module.exports = typeof WeakMap === 'function' && /native code/.test(inspectSource(WeakMap));
          /***/
        },

        /***/
        1574:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var DESCRIPTORS = __webpack_require__(9781);

          var fails = __webpack_require__(7293);

          var objectKeys = __webpack_require__(1956);

          var getOwnPropertySymbolsModule = __webpack_require__(5181);

          var propertyIsEnumerableModule = __webpack_require__(5296);

          var toObject = __webpack_require__(7908);

          var IndexedObject = __webpack_require__(8361);

          var nativeAssign = Object.assign;
          var defineProperty = Object.defineProperty; // `Object.assign` method
          // https://tc39.es/ecma262/#sec-object.assign

          module.exports = !nativeAssign || fails(function () {
            // should have correct order of operations (Edge bug)
            if (DESCRIPTORS && nativeAssign({
              b: 1
            }, nativeAssign(defineProperty({}, 'a', {
              enumerable: true,
              get: function get() {
                defineProperty(this, 'b', {
                  value: 3,
                  enumerable: false
                });
              }
            }), {
              b: 2
            })).b !== 1) return true; // should work with symbols and should have deterministic property order (V8 bug)

            var A = {};
            var B = {};
            /* global Symbol -- required for testing */

            var symbol = Symbol();
            var alphabet = 'abcdefghijklmnopqrst';
            A[symbol] = 7;
            alphabet.split('').forEach(function (chr) {
              B[chr] = chr;
            });
            return nativeAssign({}, A)[symbol] != 7 || objectKeys(nativeAssign({}, B)).join('') != alphabet;
          }) ? function assign(target, source) {
            // eslint-disable-line no-unused-vars -- required for `.length`
            var T = toObject(target);
            var argumentsLength = arguments.length;
            var index = 1;
            var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
            var propertyIsEnumerable = propertyIsEnumerableModule.f;

            while (argumentsLength > index) {
              var S = IndexedObject(arguments[index++]);
              var keys = getOwnPropertySymbols ? objectKeys(S).concat(getOwnPropertySymbols(S)) : objectKeys(S);
              var length = keys.length;
              var j = 0;
              var key;

              while (length > j) {
                key = keys[j++];
                if (!DESCRIPTORS || propertyIsEnumerable.call(S, key)) T[key] = S[key];
              }
            }

            return T;
          } : nativeAssign;
          /***/
        },

        /***/
        30:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var anObject = __webpack_require__(9670);

          var defineProperties = __webpack_require__(6048);

          var enumBugKeys = __webpack_require__(748);

          var hiddenKeys = __webpack_require__(3501);

          var html = __webpack_require__(490);

          var documentCreateElement = __webpack_require__(317);

          var sharedKey = __webpack_require__(6200);

          var GT = '>';
          var LT = '<';
          var PROTOTYPE = 'prototype';
          var SCRIPT = 'script';
          var IE_PROTO = sharedKey('IE_PROTO');

          var EmptyConstructor = function EmptyConstructor() {
            /* empty */
          };

          var scriptTag = function scriptTag(content) {
            return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
          }; // Create object with fake `null` prototype: use ActiveX Object with cleared prototype


          var NullProtoObjectViaActiveX = function NullProtoObjectViaActiveX(activeXDocument) {
            activeXDocument.write(scriptTag(''));
            activeXDocument.close();
            var temp = activeXDocument.parentWindow.Object;
            activeXDocument = null; // avoid memory leak

            return temp;
          }; // Create object with fake `null` prototype: use iframe Object with cleared prototype


          var NullProtoObjectViaIFrame = function NullProtoObjectViaIFrame() {
            // Thrash, waste and sodomy: IE GC bug
            var iframe = documentCreateElement('iframe');
            var JS = 'java' + SCRIPT + ':';
            var iframeDocument;
            iframe.style.display = 'none';
            html.appendChild(iframe); // https://github.com/zloirock/core-js/issues/475

            iframe.src = String(JS);
            iframeDocument = iframe.contentWindow.document;
            iframeDocument.open();
            iframeDocument.write(scriptTag('document.F=Object'));
            iframeDocument.close();
            return iframeDocument.F;
          }; // Check for document.domain and active x support
          // No need to use active x approach when document.domain is not set
          // see https://github.com/es-shims/es5-shim/issues/150
          // variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
          // avoid IE GC bug


          var activeXDocument;

          var _NullProtoObject = function NullProtoObject() {
            try {
              /* global ActiveXObject -- old IE */
              activeXDocument = document.domain && new ActiveXObject('htmlfile');
            } catch (error) {
              /* ignore */
            }

            _NullProtoObject = activeXDocument ? NullProtoObjectViaActiveX(activeXDocument) : NullProtoObjectViaIFrame();
            var length = enumBugKeys.length;

            while (length--) {
              delete _NullProtoObject[PROTOTYPE][enumBugKeys[length]];
            }

            return _NullProtoObject();
          };

          hiddenKeys[IE_PROTO] = true; // `Object.create` method
          // https://tc39.es/ecma262/#sec-object.create

          module.exports = Object.create || function create(O, Properties) {
            var result;

            if (O !== null) {
              EmptyConstructor[PROTOTYPE] = anObject(O);
              result = new EmptyConstructor();
              EmptyConstructor[PROTOTYPE] = null; // add "__proto__" for Object.getPrototypeOf polyfill

              result[IE_PROTO] = O;
            } else result = _NullProtoObject();

            return Properties === undefined ? result : defineProperties(result, Properties);
          };
          /***/

        },

        /***/
        6048:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var DESCRIPTORS = __webpack_require__(9781);

          var definePropertyModule = __webpack_require__(3070);

          var anObject = __webpack_require__(9670);

          var objectKeys = __webpack_require__(1956); // `Object.defineProperties` method
          // https://tc39.es/ecma262/#sec-object.defineproperties


          module.exports = DESCRIPTORS ? Object.defineProperties : function defineProperties(O, Properties) {
            anObject(O);
            var keys = objectKeys(Properties);
            var length = keys.length;
            var index = 0;
            var key;

            while (length > index) {
              definePropertyModule.f(O, key = keys[index++], Properties[key]);
            }

            return O;
          };
          /***/
        },

        /***/
        3070:
        /***/
        function _(__unused_webpack_module, exports, __webpack_require__) {
          var DESCRIPTORS = __webpack_require__(9781);

          var IE8_DOM_DEFINE = __webpack_require__(4664);

          var anObject = __webpack_require__(9670);

          var toPrimitive = __webpack_require__(7593);

          var nativeDefineProperty = Object.defineProperty; // `Object.defineProperty` method
          // https://tc39.es/ecma262/#sec-object.defineproperty

          exports.f = DESCRIPTORS ? nativeDefineProperty : function defineProperty(O, P, Attributes) {
            anObject(O);
            P = toPrimitive(P, true);
            anObject(Attributes);
            if (IE8_DOM_DEFINE) try {
              return nativeDefineProperty(O, P, Attributes);
            } catch (error) {
              /* empty */
            }
            if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported');
            if ('value' in Attributes) O[P] = Attributes.value;
            return O;
          };
          /***/
        },

        /***/
        1236:
        /***/
        function _(__unused_webpack_module, exports, __webpack_require__) {
          var DESCRIPTORS = __webpack_require__(9781);

          var propertyIsEnumerableModule = __webpack_require__(5296);

          var createPropertyDescriptor = __webpack_require__(9114);

          var toIndexedObject = __webpack_require__(5656);

          var toPrimitive = __webpack_require__(7593);

          var has = __webpack_require__(6656);

          var IE8_DOM_DEFINE = __webpack_require__(4664);

          var nativeGetOwnPropertyDescriptor = Object.getOwnPropertyDescriptor; // `Object.getOwnPropertyDescriptor` method
          // https://tc39.es/ecma262/#sec-object.getownpropertydescriptor

          exports.f = DESCRIPTORS ? nativeGetOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
            O = toIndexedObject(O);
            P = toPrimitive(P, true);
            if (IE8_DOM_DEFINE) try {
              return nativeGetOwnPropertyDescriptor(O, P);
            } catch (error) {
              /* empty */
            }
            if (has(O, P)) return createPropertyDescriptor(!propertyIsEnumerableModule.f.call(O, P), O[P]);
          };
          /***/
        },

        /***/
        8006:
        /***/
        function _(__unused_webpack_module, exports, __webpack_require__) {
          var internalObjectKeys = __webpack_require__(6324);

          var enumBugKeys = __webpack_require__(748);

          var hiddenKeys = enumBugKeys.concat('length', 'prototype'); // `Object.getOwnPropertyNames` method
          // https://tc39.es/ecma262/#sec-object.getownpropertynames

          exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
            return internalObjectKeys(O, hiddenKeys);
          };
          /***/

        },

        /***/
        5181:
        /***/
        function _(__unused_webpack_module, exports) {
          exports.f = Object.getOwnPropertySymbols;
          /***/
        },

        /***/
        9518:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var has = __webpack_require__(6656);

          var toObject = __webpack_require__(7908);

          var sharedKey = __webpack_require__(6200);

          var CORRECT_PROTOTYPE_GETTER = __webpack_require__(8544);

          var IE_PROTO = sharedKey('IE_PROTO');
          var ObjectPrototype = Object.prototype; // `Object.getPrototypeOf` method
          // https://tc39.es/ecma262/#sec-object.getprototypeof

          module.exports = CORRECT_PROTOTYPE_GETTER ? Object.getPrototypeOf : function (O) {
            O = toObject(O);
            if (has(O, IE_PROTO)) return O[IE_PROTO];

            if (typeof O.constructor == 'function' && O instanceof O.constructor) {
              return O.constructor.prototype;
            }

            return O instanceof Object ? ObjectPrototype : null;
          };
          /***/
        },

        /***/
        6324:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var has = __webpack_require__(6656);

          var toIndexedObject = __webpack_require__(5656);

          var indexOf = __webpack_require__(1318).indexOf;

          var hiddenKeys = __webpack_require__(3501);

          module.exports = function (object, names) {
            var O = toIndexedObject(object);
            var i = 0;
            var result = [];
            var key;

            for (key in O) {
              !has(hiddenKeys, key) && has(O, key) && result.push(key);
            } // Don't enum bug & hidden keys


            while (names.length > i) {
              if (has(O, key = names[i++])) {
                ~indexOf(result, key) || result.push(key);
              }
            }

            return result;
          };
          /***/

        },

        /***/
        1956:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var internalObjectKeys = __webpack_require__(6324);

          var enumBugKeys = __webpack_require__(748); // `Object.keys` method
          // https://tc39.es/ecma262/#sec-object.keys


          module.exports = Object.keys || function keys(O) {
            return internalObjectKeys(O, enumBugKeys);
          };
          /***/

        },

        /***/
        5296:
        /***/
        function _(__unused_webpack_module, exports) {
          "use strict";

          var nativePropertyIsEnumerable = {}.propertyIsEnumerable;
          var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor; // Nashorn ~ JDK8 bug

          var NASHORN_BUG = getOwnPropertyDescriptor && !nativePropertyIsEnumerable.call({
            1: 2
          }, 1); // `Object.prototype.propertyIsEnumerable` method implementation
          // https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable

          exports.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
            var descriptor = getOwnPropertyDescriptor(this, V);
            return !!descriptor && descriptor.enumerable;
          } : nativePropertyIsEnumerable;
          /***/
        },

        /***/
        7674:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          /* eslint-disable no-proto -- safe */
          var anObject = __webpack_require__(9670);

          var aPossiblePrototype = __webpack_require__(6077); // `Object.setPrototypeOf` method
          // https://tc39.es/ecma262/#sec-object.setprototypeof
          // Works with __proto__ only. Old v8 can't work with null proto objects.


          module.exports = Object.setPrototypeOf || ('__proto__' in {} ? function () {
            var CORRECT_SETTER = false;
            var test = {};
            var setter;

            try {
              setter = Object.getOwnPropertyDescriptor(Object.prototype, '__proto__').set;
              setter.call(test, []);
              CORRECT_SETTER = test instanceof Array;
            } catch (error) {
              /* empty */
            }

            return function setPrototypeOf(O, proto) {
              anObject(O);
              aPossiblePrototype(proto);
              if (CORRECT_SETTER) setter.call(O, proto);else O.__proto__ = proto;
              return O;
            };
          }() : undefined);
          /***/
        },

        /***/
        288:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var TO_STRING_TAG_SUPPORT = __webpack_require__(1694);

          var classof = __webpack_require__(648); // `Object.prototype.toString` method implementation
          // https://tc39.es/ecma262/#sec-object.prototype.tostring


          module.exports = TO_STRING_TAG_SUPPORT ? {}.toString : function toString() {
            return '[object ' + classof(this) + ']';
          };
          /***/
        },

        /***/
        3887:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var getBuiltIn = __webpack_require__(5005);

          var getOwnPropertyNamesModule = __webpack_require__(8006);

          var getOwnPropertySymbolsModule = __webpack_require__(5181);

          var anObject = __webpack_require__(9670); // all object keys, includes non-enumerable and symbols


          module.exports = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
            var keys = getOwnPropertyNamesModule.f(anObject(it));
            var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
            return getOwnPropertySymbols ? keys.concat(getOwnPropertySymbols(it)) : keys;
          };
          /***/

        },

        /***/
        857:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          module.exports = global;
          /***/
        },

        /***/
        2248:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var redefine = __webpack_require__(1320);

          module.exports = function (target, src, options) {
            for (var key in src) {
              redefine(target, key, src[key], options);
            }

            return target;
          };
          /***/

        },

        /***/
        1320:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var has = __webpack_require__(6656);

          var setGlobal = __webpack_require__(3505);

          var inspectSource = __webpack_require__(2788);

          var InternalStateModule = __webpack_require__(9909);

          var getInternalState = InternalStateModule.get;
          var enforceInternalState = InternalStateModule.enforce;
          var TEMPLATE = String(String).split('String');
          (module.exports = function (O, key, value, options) {
            var unsafe = options ? !!options.unsafe : false;
            var simple = options ? !!options.enumerable : false;
            var noTargetGet = options ? !!options.noTargetGet : false;
            var state;

            if (typeof value == 'function') {
              if (typeof key == 'string' && !has(value, 'name')) {
                createNonEnumerableProperty(value, 'name', key);
              }

              state = enforceInternalState(value);

              if (!state.source) {
                state.source = TEMPLATE.join(typeof key == 'string' ? key : '');
              }
            }

            if (O === global) {
              if (simple) O[key] = value;else setGlobal(key, value);
              return;
            } else if (!unsafe) {
              delete O[key];
            } else if (!noTargetGet && O[key]) {
              simple = true;
            }

            if (simple) O[key] = value;else createNonEnumerableProperty(O, key, value); // add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
          })(Function.prototype, 'toString', function toString() {
            return typeof this == 'function' && getInternalState(this).source || inspectSource(this);
          });
          /***/
        },

        /***/
        7651:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var classof = __webpack_require__(4326);

          var regexpExec = __webpack_require__(2261); // `RegExpExec` abstract operation
          // https://tc39.es/ecma262/#sec-regexpexec


          module.exports = function (R, S) {
            var exec = R.exec;

            if (typeof exec === 'function') {
              var result = exec.call(R, S);

              if (_typeof2(result) !== 'object') {
                throw TypeError('RegExp exec method returned something other than an Object or null');
              }

              return result;
            }

            if (classof(R) !== 'RegExp') {
              throw TypeError('RegExp#exec called on incompatible receiver');
            }

            return regexpExec.call(R, S);
          };
          /***/

        },

        /***/
        2261:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var regexpFlags = __webpack_require__(7066);

          var stickyHelpers = __webpack_require__(2999);

          var nativeExec = RegExp.prototype.exec; // This always refers to the native implementation, because the
          // String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
          // which loads this file before patching the method.

          var nativeReplace = String.prototype.replace;
          var patchedExec = nativeExec;

          var UPDATES_LAST_INDEX_WRONG = function () {
            var re1 = /a/;
            var re2 = /b*/g;
            nativeExec.call(re1, 'a');
            nativeExec.call(re2, 'a');
            return re1.lastIndex !== 0 || re2.lastIndex !== 0;
          }();

          var UNSUPPORTED_Y = stickyHelpers.UNSUPPORTED_Y || stickyHelpers.BROKEN_CARET; // nonparticipating capturing group, copied from es5-shim's String#split patch.
          // eslint-disable-next-line regexp/no-assertion-capturing-group, regexp/no-empty-group -- required for testing

          var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;
          var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED || UNSUPPORTED_Y;

          if (PATCH) {
            patchedExec = function exec(str) {
              var re = this;
              var lastIndex, reCopy, match, i;
              var sticky = UNSUPPORTED_Y && re.sticky;
              var flags = regexpFlags.call(re);
              var source = re.source;
              var charsAdded = 0;
              var strCopy = str;

              if (sticky) {
                flags = flags.replace('y', '');

                if (flags.indexOf('g') === -1) {
                  flags += 'g';
                }

                strCopy = String(str).slice(re.lastIndex); // Support anchored sticky behavior.

                if (re.lastIndex > 0 && (!re.multiline || re.multiline && str[re.lastIndex - 1] !== '\n')) {
                  source = '(?: ' + source + ')';
                  strCopy = ' ' + strCopy;
                  charsAdded++;
                } // ^(? + rx + ) is needed, in combination with some str slicing, to
                // simulate the 'y' flag.


                reCopy = new RegExp('^(?:' + source + ')', flags);
              }

              if (NPCG_INCLUDED) {
                reCopy = new RegExp('^' + source + '$(?!\\s)', flags);
              }

              if (UPDATES_LAST_INDEX_WRONG) lastIndex = re.lastIndex;
              match = nativeExec.call(sticky ? reCopy : re, strCopy);

              if (sticky) {
                if (match) {
                  match.input = match.input.slice(charsAdded);
                  match[0] = match[0].slice(charsAdded);
                  match.index = re.lastIndex;
                  re.lastIndex += match[0].length;
                } else re.lastIndex = 0;
              } else if (UPDATES_LAST_INDEX_WRONG && match) {
                re.lastIndex = re.global ? match.index + match[0].length : lastIndex;
              }

              if (NPCG_INCLUDED && match && match.length > 1) {
                // Fix browsers whose `exec` methods don't consistently return `undefined`
                // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
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
        7066:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var anObject = __webpack_require__(9670); // `RegExp.prototype.flags` getter implementation
          // https://tc39.es/ecma262/#sec-get-regexp.prototype.flags


          module.exports = function () {
            var that = anObject(this);
            var result = '';
            if (that.global) result += 'g';
            if (that.ignoreCase) result += 'i';
            if (that.multiline) result += 'm';
            if (that.dotAll) result += 's';
            if (that.unicode) result += 'u';
            if (that.sticky) result += 'y';
            return result;
          };
          /***/

        },

        /***/
        2999:
        /***/
        function _(__unused_webpack_module, exports, __webpack_require__) {
          "use strict";

          var fails = __webpack_require__(7293); // babel-minify transpiles RegExp('a', 'y') -> /a/y and it causes SyntaxError,
          // so we use an intermediate function.


          function RE(s, f) {
            return RegExp(s, f);
          }

          exports.UNSUPPORTED_Y = fails(function () {
            // babel-minify transpiles RegExp('a', 'y') -> /a/y and it causes SyntaxError
            var re = RE('a', 'y');
            re.lastIndex = 2;
            return re.exec('abcd') != null;
          });
          exports.BROKEN_CARET = fails(function () {
            // https://bugzilla.mozilla.org/show_bug.cgi?id=773687
            var re = RE('^r', 'gy');
            re.lastIndex = 2;
            return re.exec('str') != null;
          });
          /***/
        },

        /***/
        4488:
        /***/
        function _(module) {
          // `RequireObjectCoercible` abstract operation
          // https://tc39.es/ecma262/#sec-requireobjectcoercible
          module.exports = function (it) {
            if (it == undefined) throw TypeError("Can't call method on " + it);
            return it;
          };
          /***/

        },

        /***/
        3505:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var createNonEnumerableProperty = __webpack_require__(8880);

          module.exports = function (key, value) {
            try {
              createNonEnumerableProperty(global, key, value);
            } catch (error) {
              global[key] = value;
            }

            return value;
          };
          /***/

        },

        /***/
        6340:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var getBuiltIn = __webpack_require__(5005);

          var definePropertyModule = __webpack_require__(3070);

          var wellKnownSymbol = __webpack_require__(5112);

          var DESCRIPTORS = __webpack_require__(9781);

          var SPECIES = wellKnownSymbol('species');

          module.exports = function (CONSTRUCTOR_NAME) {
            var Constructor = getBuiltIn(CONSTRUCTOR_NAME);
            var defineProperty = definePropertyModule.f;

            if (DESCRIPTORS && Constructor && !Constructor[SPECIES]) {
              defineProperty(Constructor, SPECIES, {
                configurable: true,
                get: function get() {
                  return this;
                }
              });
            }
          };
          /***/

        },

        /***/
        8003:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var defineProperty = __webpack_require__(3070).f;

          var has = __webpack_require__(6656);

          var wellKnownSymbol = __webpack_require__(5112);

          var TO_STRING_TAG = wellKnownSymbol('toStringTag');

          module.exports = function (it, TAG, STATIC) {
            if (it && !has(it = STATIC ? it : it.prototype, TO_STRING_TAG)) {
              defineProperty(it, TO_STRING_TAG, {
                configurable: true,
                value: TAG
              });
            }
          };
          /***/

        },

        /***/
        6200:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var shared = __webpack_require__(2309);

          var uid = __webpack_require__(9711);

          var keys = shared('keys');

          module.exports = function (key) {
            return keys[key] || (keys[key] = uid(key));
          };
          /***/

        },

        /***/
        5465:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var setGlobal = __webpack_require__(3505);

          var SHARED = '__core-js_shared__';
          var store = global[SHARED] || setGlobal(SHARED, {});
          module.exports = store;
          /***/
        },

        /***/
        2309:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var IS_PURE = __webpack_require__(1913);

          var store = __webpack_require__(5465);

          (module.exports = function (key, value) {
            return store[key] || (store[key] = value !== undefined ? value : {});
          })('versions', []).push({
            version: '3.9.0',
            mode: IS_PURE ? 'pure' : 'global',
            copyright: '© 2021 Denis Pushkarev (zloirock.ru)'
          });
          /***/
        },

        /***/
        6707:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var anObject = __webpack_require__(9670);

          var aFunction = __webpack_require__(3099);

          var wellKnownSymbol = __webpack_require__(5112);

          var SPECIES = wellKnownSymbol('species'); // `SpeciesConstructor` abstract operation
          // https://tc39.es/ecma262/#sec-speciesconstructor

          module.exports = function (O, defaultConstructor) {
            var C = anObject(O).constructor;
            var S;
            return C === undefined || (S = anObject(C)[SPECIES]) == undefined ? defaultConstructor : aFunction(S);
          };
          /***/

        },

        /***/
        8710:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          var requireObjectCoercible = __webpack_require__(4488); // `String.prototype.{ codePointAt, at }` methods implementation


          var createMethod = function createMethod(CONVERT_TO_STRING) {
            return function ($this, pos) {
              var S = String(requireObjectCoercible($this));
              var position = toInteger(pos);
              var size = S.length;
              var first, second;
              if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
              first = S.charCodeAt(position);
              return first < 0xD800 || first > 0xDBFF || position + 1 === size || (second = S.charCodeAt(position + 1)) < 0xDC00 || second > 0xDFFF ? CONVERT_TO_STRING ? S.charAt(position) : first : CONVERT_TO_STRING ? S.slice(position, position + 2) : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
            };
          };

          module.exports = {
            // `String.prototype.codePointAt` method
            // https://tc39.es/ecma262/#sec-string.prototype.codepointat
            codeAt: createMethod(false),
            // `String.prototype.at` method
            // https://github.com/mathiasbynens/String.prototype.at
            charAt: createMethod(true)
          };
          /***/
        },

        /***/
        3197:
        /***/
        function _(module) {
          "use strict"; // based on https://github.com/bestiejs/punycode.js/blob/master/punycode.js

          var maxInt = 2147483647; // aka. 0x7FFFFFFF or 2^31-1

          var base = 36;
          var tMin = 1;
          var tMax = 26;
          var skew = 38;
          var damp = 700;
          var initialBias = 72;
          var initialN = 128; // 0x80

          var delimiter = '-'; // '\x2D'

          var regexNonASCII = /[^\0-\u007E]/; // non-ASCII chars

          var regexSeparators = /[.\u3002\uFF0E\uFF61]/g; // RFC 3490 separators

          var OVERFLOW_ERROR = 'Overflow: input needs wider integers to process';
          var baseMinusTMin = base - tMin;
          var floor = Math.floor;
          var stringFromCharCode = String.fromCharCode;
          /**
           * Creates an array containing the numeric code points of each Unicode
           * character in the string. While JavaScript uses UCS-2 internally,
           * this function will convert a pair of surrogate halves (each of which
           * UCS-2 exposes as separate characters) into a single code point,
           * matching UTF-16.
           */

          var ucs2decode = function ucs2decode(string) {
            var output = [];
            var counter = 0;
            var length = string.length;

            while (counter < length) {
              var value = string.charCodeAt(counter++);

              if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
                // It's a high surrogate, and there is a next character.
                var extra = string.charCodeAt(counter++);

                if ((extra & 0xFC00) == 0xDC00) {
                  // Low surrogate.
                  output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
                } else {
                  // It's an unmatched surrogate; only append this code unit, in case the
                  // next code unit is the high surrogate of a surrogate pair.
                  output.push(value);
                  counter--;
                }
              } else {
                output.push(value);
              }
            }

            return output;
          };
          /**
           * Converts a digit/integer into a basic code point.
           */


          var digitToBasic = function digitToBasic(digit) {
            //  0..25 map to ASCII a..z or A..Z
            // 26..35 map to ASCII 0..9
            return digit + 22 + 75 * (digit < 26);
          };
          /**
           * Bias adaptation function as per section 3.4 of RFC 3492.
           * https://tools.ietf.org/html/rfc3492#section-3.4
           */


          var adapt = function adapt(delta, numPoints, firstTime) {
            var k = 0;
            delta = firstTime ? floor(delta / damp) : delta >> 1;
            delta += floor(delta / numPoints);

            for (; delta > baseMinusTMin * tMax >> 1; k += base) {
              delta = floor(delta / baseMinusTMin);
            }

            return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
          };
          /**
           * Converts a string of Unicode symbols (e.g. a domain name label) to a
           * Punycode string of ASCII-only symbols.
           */
          // eslint-disable-next-line max-statements -- TODO


          var encode = function encode(input) {
            var output = []; // Convert the input in UCS-2 to an array of Unicode code points.

            input = ucs2decode(input); // Cache the length.

            var inputLength = input.length; // Initialize the state.

            var n = initialN;
            var delta = 0;
            var bias = initialBias;
            var i, currentValue; // Handle the basic code points.

            for (i = 0; i < input.length; i++) {
              currentValue = input[i];

              if (currentValue < 0x80) {
                output.push(stringFromCharCode(currentValue));
              }
            }

            var basicLength = output.length; // number of basic code points.

            var handledCPCount = basicLength; // number of code points that have been handled;
            // Finish the basic string with a delimiter unless it's empty.

            if (basicLength) {
              output.push(delimiter);
            } // Main encoding loop:


            while (handledCPCount < inputLength) {
              // All non-basic code points < n have been handled already. Find the next larger one:
              var m = maxInt;

              for (i = 0; i < input.length; i++) {
                currentValue = input[i];

                if (currentValue >= n && currentValue < m) {
                  m = currentValue;
                }
              } // Increase `delta` enough to advance the decoder's <n,i> state to <m,0>, but guard against overflow.


              var handledCPCountPlusOne = handledCPCount + 1;

              if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
                throw RangeError(OVERFLOW_ERROR);
              }

              delta += (m - n) * handledCPCountPlusOne;
              n = m;

              for (i = 0; i < input.length; i++) {
                currentValue = input[i];

                if (currentValue < n && ++delta > maxInt) {
                  throw RangeError(OVERFLOW_ERROR);
                }

                if (currentValue == n) {
                  // Represent delta as a generalized variable-length integer.
                  var q = delta;

                  for (var k = base;;
                  /* no condition */
                  k += base) {
                    var t = k <= bias ? tMin : k >= bias + tMax ? tMax : k - bias;
                    if (q < t) break;
                    var qMinusT = q - t;
                    var baseMinusT = base - t;
                    output.push(stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT)));
                    q = floor(qMinusT / baseMinusT);
                  }

                  output.push(stringFromCharCode(digitToBasic(q)));
                  bias = adapt(delta, handledCPCountPlusOne, handledCPCount == basicLength);
                  delta = 0;
                  ++handledCPCount;
                }
              }

              ++delta;
              ++n;
            }

            return output.join('');
          };

          module.exports = function (input) {
            var encoded = [];
            var labels = input.toLowerCase().replace(regexSeparators, ".").split('.');
            var i, label;

            for (i = 0; i < labels.length; i++) {
              label = labels[i];
              encoded.push(regexNonASCII.test(label) ? 'xn--' + encode(label) : label);
            }

            return encoded.join('.');
          };
          /***/

        },

        /***/
        6091:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          var whitespaces = __webpack_require__(1361);

          var non = "\u200B\x85\u180E"; // check that a method works with the correct list
          // of whitespaces and has a correct name

          module.exports = function (METHOD_NAME) {
            return fails(function () {
              return !!whitespaces[METHOD_NAME]() || non[METHOD_NAME]() != non || whitespaces[METHOD_NAME].name !== METHOD_NAME;
            });
          };
          /***/

        },

        /***/
        3111:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var requireObjectCoercible = __webpack_require__(4488);

          var whitespaces = __webpack_require__(1361);

          var whitespace = '[' + whitespaces + ']';
          var ltrim = RegExp('^' + whitespace + whitespace + '*');
          var rtrim = RegExp(whitespace + whitespace + '*$'); // `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation

          var createMethod = function createMethod(TYPE) {
            return function ($this) {
              var string = String(requireObjectCoercible($this));
              if (TYPE & 1) string = string.replace(ltrim, '');
              if (TYPE & 2) string = string.replace(rtrim, '');
              return string;
            };
          };

          module.exports = {
            // `String.prototype.{ trimLeft, trimStart }` methods
            // https://tc39.es/ecma262/#sec-string.prototype.trimstart
            start: createMethod(1),
            // `String.prototype.{ trimRight, trimEnd }` methods
            // https://tc39.es/ecma262/#sec-string.prototype.trimend
            end: createMethod(2),
            // `String.prototype.trim` method
            // https://tc39.es/ecma262/#sec-string.prototype.trim
            trim: createMethod(3)
          };
          /***/
        },

        /***/
        1400:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          var max = Math.max;
          var min = Math.min; // Helper for a popular repeating case of the spec:
          // Let integer be ? ToInteger(index).
          // If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).

          module.exports = function (index, length) {
            var integer = toInteger(index);
            return integer < 0 ? max(integer + length, 0) : min(integer, length);
          };
          /***/

        },

        /***/
        7067:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          var toLength = __webpack_require__(7466); // `ToIndex` abstract operation
          // https://tc39.es/ecma262/#sec-toindex


          module.exports = function (it) {
            if (it === undefined) return 0;
            var number = toInteger(it);
            var length = toLength(number);
            if (number !== length) throw RangeError('Wrong length or index');
            return length;
          };
          /***/

        },

        /***/
        5656:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          // toObject with fallback for non-array-like ES3 strings
          var IndexedObject = __webpack_require__(8361);

          var requireObjectCoercible = __webpack_require__(4488);

          module.exports = function (it) {
            return IndexedObject(requireObjectCoercible(it));
          };
          /***/

        },

        /***/
        9958:
        /***/
        function _(module) {
          var ceil = Math.ceil;
          var floor = Math.floor; // `ToInteger` abstract operation
          // https://tc39.es/ecma262/#sec-tointeger

          module.exports = function (argument) {
            return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
          };
          /***/

        },

        /***/
        7466:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          var min = Math.min; // `ToLength` abstract operation
          // https://tc39.es/ecma262/#sec-tolength

          module.exports = function (argument) {
            return argument > 0 ? min(toInteger(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
          };
          /***/

        },

        /***/
        7908:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var requireObjectCoercible = __webpack_require__(4488); // `ToObject` abstract operation
          // https://tc39.es/ecma262/#sec-toobject


          module.exports = function (argument) {
            return Object(requireObjectCoercible(argument));
          };
          /***/

        },

        /***/
        4590:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toPositiveInteger = __webpack_require__(3002);

          module.exports = function (it, BYTES) {
            var offset = toPositiveInteger(it);
            if (offset % BYTES) throw RangeError('Wrong offset');
            return offset;
          };
          /***/

        },

        /***/
        3002:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          module.exports = function (it) {
            var result = toInteger(it);
            if (result < 0) throw RangeError("The argument can't be less than 0");
            return result;
          };
          /***/

        },

        /***/
        7593:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var isObject = __webpack_require__(111); // `ToPrimitive` abstract operation
          // https://tc39.es/ecma262/#sec-toprimitive
          // instead of the ES6 spec version, we didn't implement @@toPrimitive case
          // and the second argument - flag - preferred type is a string


          module.exports = function (input, PREFERRED_STRING) {
            if (!isObject(input)) return input;
            var fn, val;
            if (PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
            if (typeof (fn = input.valueOf) == 'function' && !isObject(val = fn.call(input))) return val;
            if (!PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
            throw TypeError("Can't convert object to primitive value");
          };
          /***/

        },

        /***/
        1694:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var wellKnownSymbol = __webpack_require__(5112);

          var TO_STRING_TAG = wellKnownSymbol('toStringTag');
          var test = {};
          test[TO_STRING_TAG] = 'z';
          module.exports = String(test) === '[object z]';
          /***/
        },

        /***/
        9843:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var global = __webpack_require__(7854);

          var DESCRIPTORS = __webpack_require__(9781);

          var TYPED_ARRAYS_CONSTRUCTORS_REQUIRES_WRAPPERS = __webpack_require__(3832);

          var ArrayBufferViewCore = __webpack_require__(260);

          var ArrayBufferModule = __webpack_require__(3331);

          var anInstance = __webpack_require__(5787);

          var createPropertyDescriptor = __webpack_require__(9114);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var toLength = __webpack_require__(7466);

          var toIndex = __webpack_require__(7067);

          var toOffset = __webpack_require__(4590);

          var toPrimitive = __webpack_require__(7593);

          var has = __webpack_require__(6656);

          var classof = __webpack_require__(648);

          var isObject = __webpack_require__(111);

          var create = __webpack_require__(30);

          var setPrototypeOf = __webpack_require__(7674);

          var getOwnPropertyNames = __webpack_require__(8006).f;

          var typedArrayFrom = __webpack_require__(7321);

          var forEach = __webpack_require__(2092).forEach;

          var setSpecies = __webpack_require__(6340);

          var definePropertyModule = __webpack_require__(3070);

          var getOwnPropertyDescriptorModule = __webpack_require__(1236);

          var InternalStateModule = __webpack_require__(9909);

          var inheritIfRequired = __webpack_require__(9587);

          var getInternalState = InternalStateModule.get;
          var setInternalState = InternalStateModule.set;
          var nativeDefineProperty = definePropertyModule.f;
          var nativeGetOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
          var round = Math.round;
          var RangeError = global.RangeError;
          var ArrayBuffer = ArrayBufferModule.ArrayBuffer;
          var DataView = ArrayBufferModule.DataView;
          var NATIVE_ARRAY_BUFFER_VIEWS = ArrayBufferViewCore.NATIVE_ARRAY_BUFFER_VIEWS;
          var TYPED_ARRAY_TAG = ArrayBufferViewCore.TYPED_ARRAY_TAG;
          var TypedArray = ArrayBufferViewCore.TypedArray;
          var TypedArrayPrototype = ArrayBufferViewCore.TypedArrayPrototype;
          var aTypedArrayConstructor = ArrayBufferViewCore.aTypedArrayConstructor;
          var isTypedArray = ArrayBufferViewCore.isTypedArray;
          var BYTES_PER_ELEMENT = 'BYTES_PER_ELEMENT';
          var WRONG_LENGTH = 'Wrong length';

          var fromList = function fromList(C, list) {
            var index = 0;
            var length = list.length;
            var result = new (aTypedArrayConstructor(C))(length);

            while (length > index) {
              result[index] = list[index++];
            }

            return result;
          };

          var addGetter = function addGetter(it, key) {
            nativeDefineProperty(it, key, {
              get: function get() {
                return getInternalState(this)[key];
              }
            });
          };

          var isArrayBuffer = function isArrayBuffer(it) {
            var klass;
            return it instanceof ArrayBuffer || (klass = classof(it)) == 'ArrayBuffer' || klass == 'SharedArrayBuffer';
          };

          var isTypedArrayIndex = function isTypedArrayIndex(target, key) {
            return isTypedArray(target) && _typeof2(key) != 'symbol' && key in target && String(+key) == String(key);
          };

          var wrappedGetOwnPropertyDescriptor = function getOwnPropertyDescriptor(target, key) {
            return isTypedArrayIndex(target, key = toPrimitive(key, true)) ? createPropertyDescriptor(2, target[key]) : nativeGetOwnPropertyDescriptor(target, key);
          };

          var wrappedDefineProperty = function defineProperty(target, key, descriptor) {
            if (isTypedArrayIndex(target, key = toPrimitive(key, true)) && isObject(descriptor) && has(descriptor, 'value') && !has(descriptor, 'get') && !has(descriptor, 'set') // TODO: add validation descriptor w/o calling accessors
            && !descriptor.configurable && (!has(descriptor, 'writable') || descriptor.writable) && (!has(descriptor, 'enumerable') || descriptor.enumerable)) {
              target[key] = descriptor.value;
              return target;
            }

            return nativeDefineProperty(target, key, descriptor);
          };

          if (DESCRIPTORS) {
            if (!NATIVE_ARRAY_BUFFER_VIEWS) {
              getOwnPropertyDescriptorModule.f = wrappedGetOwnPropertyDescriptor;
              definePropertyModule.f = wrappedDefineProperty;
              addGetter(TypedArrayPrototype, 'buffer');
              addGetter(TypedArrayPrototype, 'byteOffset');
              addGetter(TypedArrayPrototype, 'byteLength');
              addGetter(TypedArrayPrototype, 'length');
            }

            $({
              target: 'Object',
              stat: true,
              forced: !NATIVE_ARRAY_BUFFER_VIEWS
            }, {
              getOwnPropertyDescriptor: wrappedGetOwnPropertyDescriptor,
              defineProperty: wrappedDefineProperty
            });

            module.exports = function (TYPE, wrapper, CLAMPED) {
              var BYTES = TYPE.match(/\d+$/)[0] / 8;
              var CONSTRUCTOR_NAME = TYPE + (CLAMPED ? 'Clamped' : '') + 'Array';
              var GETTER = 'get' + TYPE;
              var SETTER = 'set' + TYPE;
              var NativeTypedArrayConstructor = global[CONSTRUCTOR_NAME];
              var TypedArrayConstructor = NativeTypedArrayConstructor;
              var TypedArrayConstructorPrototype = TypedArrayConstructor && TypedArrayConstructor.prototype;
              var exported = {};

              var getter = function getter(that, index) {
                var data = getInternalState(that);
                return data.view[GETTER](index * BYTES + data.byteOffset, true);
              };

              var setter = function setter(that, index, value) {
                var data = getInternalState(that);
                if (CLAMPED) value = (value = round(value)) < 0 ? 0 : value > 0xFF ? 0xFF : value & 0xFF;
                data.view[SETTER](index * BYTES + data.byteOffset, value, true);
              };

              var addElement = function addElement(that, index) {
                nativeDefineProperty(that, index, {
                  get: function get() {
                    return getter(this, index);
                  },
                  set: function set(value) {
                    return setter(this, index, value);
                  },
                  enumerable: true
                });
              };

              if (!NATIVE_ARRAY_BUFFER_VIEWS) {
                TypedArrayConstructor = wrapper(function (that, data, offset, $length) {
                  anInstance(that, TypedArrayConstructor, CONSTRUCTOR_NAME);
                  var index = 0;
                  var byteOffset = 0;
                  var buffer, byteLength, length;

                  if (!isObject(data)) {
                    length = toIndex(data);
                    byteLength = length * BYTES;
                    buffer = new ArrayBuffer(byteLength);
                  } else if (isArrayBuffer(data)) {
                    buffer = data;
                    byteOffset = toOffset(offset, BYTES);
                    var $len = data.byteLength;

                    if ($length === undefined) {
                      if ($len % BYTES) throw RangeError(WRONG_LENGTH);
                      byteLength = $len - byteOffset;
                      if (byteLength < 0) throw RangeError(WRONG_LENGTH);
                    } else {
                      byteLength = toLength($length) * BYTES;
                      if (byteLength + byteOffset > $len) throw RangeError(WRONG_LENGTH);
                    }

                    length = byteLength / BYTES;
                  } else if (isTypedArray(data)) {
                    return fromList(TypedArrayConstructor, data);
                  } else {
                    return typedArrayFrom.call(TypedArrayConstructor, data);
                  }

                  setInternalState(that, {
                    buffer: buffer,
                    byteOffset: byteOffset,
                    byteLength: byteLength,
                    length: length,
                    view: new DataView(buffer)
                  });

                  while (index < length) {
                    addElement(that, index++);
                  }
                });
                if (setPrototypeOf) setPrototypeOf(TypedArrayConstructor, TypedArray);
                TypedArrayConstructorPrototype = TypedArrayConstructor.prototype = create(TypedArrayPrototype);
              } else if (TYPED_ARRAYS_CONSTRUCTORS_REQUIRES_WRAPPERS) {
                TypedArrayConstructor = wrapper(function (dummy, data, typedArrayOffset, $length) {
                  anInstance(dummy, TypedArrayConstructor, CONSTRUCTOR_NAME);
                  return inheritIfRequired(function () {
                    if (!isObject(data)) return new NativeTypedArrayConstructor(toIndex(data));
                    if (isArrayBuffer(data)) return $length !== undefined ? new NativeTypedArrayConstructor(data, toOffset(typedArrayOffset, BYTES), $length) : typedArrayOffset !== undefined ? new NativeTypedArrayConstructor(data, toOffset(typedArrayOffset, BYTES)) : new NativeTypedArrayConstructor(data);
                    if (isTypedArray(data)) return fromList(TypedArrayConstructor, data);
                    return typedArrayFrom.call(TypedArrayConstructor, data);
                  }(), dummy, TypedArrayConstructor);
                });
                if (setPrototypeOf) setPrototypeOf(TypedArrayConstructor, TypedArray);
                forEach(getOwnPropertyNames(NativeTypedArrayConstructor), function (key) {
                  if (!(key in TypedArrayConstructor)) {
                    createNonEnumerableProperty(TypedArrayConstructor, key, NativeTypedArrayConstructor[key]);
                  }
                });
                TypedArrayConstructor.prototype = TypedArrayConstructorPrototype;
              }

              if (TypedArrayConstructorPrototype.constructor !== TypedArrayConstructor) {
                createNonEnumerableProperty(TypedArrayConstructorPrototype, 'constructor', TypedArrayConstructor);
              }

              if (TYPED_ARRAY_TAG) {
                createNonEnumerableProperty(TypedArrayConstructorPrototype, TYPED_ARRAY_TAG, CONSTRUCTOR_NAME);
              }

              exported[CONSTRUCTOR_NAME] = TypedArrayConstructor;
              $({
                global: true,
                forced: TypedArrayConstructor != NativeTypedArrayConstructor,
                sham: !NATIVE_ARRAY_BUFFER_VIEWS
              }, exported);

              if (!(BYTES_PER_ELEMENT in TypedArrayConstructor)) {
                createNonEnumerableProperty(TypedArrayConstructor, BYTES_PER_ELEMENT, BYTES);
              }

              if (!(BYTES_PER_ELEMENT in TypedArrayConstructorPrototype)) {
                createNonEnumerableProperty(TypedArrayConstructorPrototype, BYTES_PER_ELEMENT, BYTES);
              }

              setSpecies(CONSTRUCTOR_NAME);
            };
          } else module.exports = function () {
            /* empty */
          };
          /***/

        },

        /***/
        3832:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          /* eslint-disable no-new -- required for testing */
          var global = __webpack_require__(7854);

          var fails = __webpack_require__(7293);

          var checkCorrectnessOfIteration = __webpack_require__(7072);

          var NATIVE_ARRAY_BUFFER_VIEWS = __webpack_require__(260).NATIVE_ARRAY_BUFFER_VIEWS;

          var ArrayBuffer = global.ArrayBuffer;
          var Int8Array = global.Int8Array;
          module.exports = !NATIVE_ARRAY_BUFFER_VIEWS || !fails(function () {
            Int8Array(1);
          }) || !fails(function () {
            new Int8Array(-1);
          }) || !checkCorrectnessOfIteration(function (iterable) {
            new Int8Array();
            new Int8Array(null);
            new Int8Array(1.5);
            new Int8Array(iterable);
          }, true) || fails(function () {
            // Safari (11+) bug - a reason why even Safari 13 should load a typed array polyfill
            return new Int8Array(new ArrayBuffer(2), 1, undefined).length !== 1;
          });
          /***/
        },

        /***/
        3074:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var aTypedArrayConstructor = __webpack_require__(260).aTypedArrayConstructor;

          var speciesConstructor = __webpack_require__(6707);

          module.exports = function (instance, list) {
            var C = speciesConstructor(instance, instance.constructor);
            var index = 0;
            var length = list.length;
            var result = new (aTypedArrayConstructor(C))(length);

            while (length > index) {
              result[index] = list[index++];
            }

            return result;
          };
          /***/

        },

        /***/
        7321:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var toObject = __webpack_require__(7908);

          var toLength = __webpack_require__(7466);

          var getIteratorMethod = __webpack_require__(1246);

          var isArrayIteratorMethod = __webpack_require__(7659);

          var bind = __webpack_require__(9974);

          var aTypedArrayConstructor = __webpack_require__(260).aTypedArrayConstructor;

          module.exports = function from(source
          /* , mapfn, thisArg */
          ) {
            var O = toObject(source);
            var argumentsLength = arguments.length;
            var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
            var mapping = mapfn !== undefined;
            var iteratorMethod = getIteratorMethod(O);
            var i, length, result, step, iterator, next;

            if (iteratorMethod != undefined && !isArrayIteratorMethod(iteratorMethod)) {
              iterator = iteratorMethod.call(O);
              next = iterator.next;
              O = [];

              while (!(step = next.call(iterator)).done) {
                O.push(step.value);
              }
            }

            if (mapping && argumentsLength > 2) {
              mapfn = bind(mapfn, arguments[2], 2);
            }

            length = toLength(O.length);
            result = new (aTypedArrayConstructor(this))(length);

            for (i = 0; length > i; i++) {
              result[i] = mapping ? mapfn(O[i], i) : O[i];
            }

            return result;
          };
          /***/

        },

        /***/
        9711:
        /***/
        function _(module) {
          var id = 0;
          var postfix = Math.random();

          module.exports = function (key) {
            return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id + postfix).toString(36);
          };
          /***/

        },

        /***/
        3307:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var NATIVE_SYMBOL = __webpack_require__(133);

          module.exports = NATIVE_SYMBOL
          /* global Symbol -- safe */
          && !Symbol.sham && _typeof2(Symbol.iterator) == 'symbol';
          /***/
        },

        /***/
        5112:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var shared = __webpack_require__(2309);

          var has = __webpack_require__(6656);

          var uid = __webpack_require__(9711);

          var NATIVE_SYMBOL = __webpack_require__(133);

          var USE_SYMBOL_AS_UID = __webpack_require__(3307);

          var WellKnownSymbolsStore = shared('wks');
          var _Symbol = global.Symbol;
          var createWellKnownSymbol = USE_SYMBOL_AS_UID ? _Symbol : _Symbol && _Symbol.withoutSetter || uid;

          module.exports = function (name) {
            if (!has(WellKnownSymbolsStore, name)) {
              if (NATIVE_SYMBOL && has(_Symbol, name)) WellKnownSymbolsStore[name] = _Symbol[name];else WellKnownSymbolsStore[name] = createWellKnownSymbol('Symbol.' + name);
            }

            return WellKnownSymbolsStore[name];
          };
          /***/

        },

        /***/
        1361:
        /***/
        function _(module) {
          // a string of all valid unicode whitespaces
          module.exports = "\t\n\x0B\f\r \xA0\u1680\u2000\u2001\u2002" + "\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF";
          /***/
        },

        /***/
        8264:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var global = __webpack_require__(7854);

          var arrayBufferModule = __webpack_require__(3331);

          var setSpecies = __webpack_require__(6340);

          var ARRAY_BUFFER = 'ArrayBuffer';
          var ArrayBuffer = arrayBufferModule[ARRAY_BUFFER];
          var NativeArrayBuffer = global[ARRAY_BUFFER]; // `ArrayBuffer` constructor
          // https://tc39.es/ecma262/#sec-arraybuffer-constructor

          $({
            global: true,
            forced: NativeArrayBuffer !== ArrayBuffer
          }, {
            ArrayBuffer: ArrayBuffer
          });
          setSpecies(ARRAY_BUFFER);
          /***/
        },

        /***/
        2222:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var fails = __webpack_require__(7293);

          var isArray = __webpack_require__(3157);

          var isObject = __webpack_require__(111);

          var toObject = __webpack_require__(7908);

          var toLength = __webpack_require__(7466);

          var createProperty = __webpack_require__(6135);

          var arraySpeciesCreate = __webpack_require__(5417);

          var arrayMethodHasSpeciesSupport = __webpack_require__(1194);

          var wellKnownSymbol = __webpack_require__(5112);

          var V8_VERSION = __webpack_require__(7392);

          var IS_CONCAT_SPREADABLE = wellKnownSymbol('isConcatSpreadable');
          var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF;
          var MAXIMUM_ALLOWED_INDEX_EXCEEDED = 'Maximum allowed index exceeded'; // We can't use this feature detection in V8 since it causes
          // deoptimization and serious performance degradation
          // https://github.com/zloirock/core-js/issues/679

          var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION >= 51 || !fails(function () {
            var array = [];
            array[IS_CONCAT_SPREADABLE] = false;
            return array.concat()[0] !== array;
          });
          var SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('concat');

          var isConcatSpreadable = function isConcatSpreadable(O) {
            if (!isObject(O)) return false;
            var spreadable = O[IS_CONCAT_SPREADABLE];
            return spreadable !== undefined ? !!spreadable : isArray(O);
          };

          var FORCED = !IS_CONCAT_SPREADABLE_SUPPORT || !SPECIES_SUPPORT; // `Array.prototype.concat` method
          // https://tc39.es/ecma262/#sec-array.prototype.concat
          // with adding support of @@isConcatSpreadable and @@species

          $({
            target: 'Array',
            proto: true,
            forced: FORCED
          }, {
            // eslint-disable-next-line no-unused-vars -- required for `.length`
            concat: function concat(arg) {
              var O = toObject(this);
              var A = arraySpeciesCreate(O, 0);
              var n = 0;
              var i, k, length, len, E;

              for (i = -1, length = arguments.length; i < length; i++) {
                E = i === -1 ? O : arguments[i];

                if (isConcatSpreadable(E)) {
                  len = toLength(E.length);
                  if (n + len > MAX_SAFE_INTEGER) throw TypeError(MAXIMUM_ALLOWED_INDEX_EXCEEDED);

                  for (k = 0; k < len; k++, n++) {
                    if (k in E) createProperty(A, n, E[k]);
                  }
                } else {
                  if (n >= MAX_SAFE_INTEGER) throw TypeError(MAXIMUM_ALLOWED_INDEX_EXCEEDED);
                  createProperty(A, n++, E);
                }
              }

              A.length = n;
              return A;
            }
          });
          /***/
        },

        /***/
        7327:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var $filter = __webpack_require__(2092).filter;

          var arrayMethodHasSpeciesSupport = __webpack_require__(1194);

          var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter'); // `Array.prototype.filter` method
          // https://tc39.es/ecma262/#sec-array.prototype.filter
          // with adding support of @@species

          $({
            target: 'Array',
            proto: true,
            forced: !HAS_SPECIES_SUPPORT
          }, {
            filter: function filter(callbackfn
            /* , thisArg */
            ) {
              return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
            }
          });
          /***/
        },

        /***/
        2772:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var $indexOf = __webpack_require__(1318).indexOf;

          var arrayMethodIsStrict = __webpack_require__(9341);

          var nativeIndexOf = [].indexOf;
          var NEGATIVE_ZERO = !!nativeIndexOf && 1 / [1].indexOf(1, -0) < 0;
          var STRICT_METHOD = arrayMethodIsStrict('indexOf'); // `Array.prototype.indexOf` method
          // https://tc39.es/ecma262/#sec-array.prototype.indexof

          $({
            target: 'Array',
            proto: true,
            forced: NEGATIVE_ZERO || !STRICT_METHOD
          }, {
            indexOf: function indexOf(searchElement
            /* , fromIndex = 0 */
            ) {
              return NEGATIVE_ZERO // convert -0 to +0
              ? nativeIndexOf.apply(this, arguments) || 0 : $indexOf(this, searchElement, arguments.length > 1 ? arguments[1] : undefined);
            }
          });
          /***/
        },

        /***/
        6992:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var toIndexedObject = __webpack_require__(5656);

          var addToUnscopables = __webpack_require__(1223);

          var Iterators = __webpack_require__(7497);

          var InternalStateModule = __webpack_require__(9909);

          var defineIterator = __webpack_require__(654);

          var ARRAY_ITERATOR = 'Array Iterator';
          var setInternalState = InternalStateModule.set;
          var getInternalState = InternalStateModule.getterFor(ARRAY_ITERATOR); // `Array.prototype.entries` method
          // https://tc39.es/ecma262/#sec-array.prototype.entries
          // `Array.prototype.keys` method
          // https://tc39.es/ecma262/#sec-array.prototype.keys
          // `Array.prototype.values` method
          // https://tc39.es/ecma262/#sec-array.prototype.values
          // `Array.prototype[@@iterator]` method
          // https://tc39.es/ecma262/#sec-array.prototype-@@iterator
          // `CreateArrayIterator` internal method
          // https://tc39.es/ecma262/#sec-createarrayiterator

          module.exports = defineIterator(Array, 'Array', function (iterated, kind) {
            setInternalState(this, {
              type: ARRAY_ITERATOR,
              target: toIndexedObject(iterated),
              // target
              index: 0,
              // next index
              kind: kind // kind

            }); // `%ArrayIteratorPrototype%.next` method
            // https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
          }, function () {
            var state = getInternalState(this);
            var target = state.target;
            var kind = state.kind;
            var index = state.index++;

            if (!target || index >= target.length) {
              state.target = undefined;
              return {
                value: undefined,
                done: true
              };
            }

            if (kind == 'keys') return {
              value: index,
              done: false
            };
            if (kind == 'values') return {
              value: target[index],
              done: false
            };
            return {
              value: [index, target[index]],
              done: false
            };
          }, 'values'); // argumentsList[@@iterator] is %ArrayProto_values%
          // https://tc39.es/ecma262/#sec-createunmappedargumentsobject
          // https://tc39.es/ecma262/#sec-createmappedargumentsobject

          Iterators.Arguments = Iterators.Array; // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables

          addToUnscopables('keys');
          addToUnscopables('values');
          addToUnscopables('entries');
          /***/
        },

        /***/
        1249:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var $map = __webpack_require__(2092).map;

          var arrayMethodHasSpeciesSupport = __webpack_require__(1194);

          var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('map'); // `Array.prototype.map` method
          // https://tc39.es/ecma262/#sec-array.prototype.map
          // with adding support of @@species

          $({
            target: 'Array',
            proto: true,
            forced: !HAS_SPECIES_SUPPORT
          }, {
            map: function map(callbackfn
            /* , thisArg */
            ) {
              return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
            }
          });
          /***/
        },

        /***/
        7042:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var isObject = __webpack_require__(111);

          var isArray = __webpack_require__(3157);

          var toAbsoluteIndex = __webpack_require__(1400);

          var toLength = __webpack_require__(7466);

          var toIndexedObject = __webpack_require__(5656);

          var createProperty = __webpack_require__(6135);

          var wellKnownSymbol = __webpack_require__(5112);

          var arrayMethodHasSpeciesSupport = __webpack_require__(1194);

          var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('slice');
          var SPECIES = wellKnownSymbol('species');
          var nativeSlice = [].slice;
          var max = Math.max; // `Array.prototype.slice` method
          // https://tc39.es/ecma262/#sec-array.prototype.slice
          // fallback for not array-like ES3 strings and DOM objects

          $({
            target: 'Array',
            proto: true,
            forced: !HAS_SPECIES_SUPPORT
          }, {
            slice: function slice(start, end) {
              var O = toIndexedObject(this);
              var length = toLength(O.length);
              var k = toAbsoluteIndex(start, length);
              var fin = toAbsoluteIndex(end === undefined ? length : end, length); // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible

              var Constructor, result, n;

              if (isArray(O)) {
                Constructor = O.constructor; // cross-realm fallback

                if (typeof Constructor == 'function' && (Constructor === Array || isArray(Constructor.prototype))) {
                  Constructor = undefined;
                } else if (isObject(Constructor)) {
                  Constructor = Constructor[SPECIES];
                  if (Constructor === null) Constructor = undefined;
                }

                if (Constructor === Array || Constructor === undefined) {
                  return nativeSlice.call(O, k, fin);
                }
              }

              result = new (Constructor === undefined ? Array : Constructor)(max(fin - k, 0));

              for (n = 0; k < fin; k++, n++) {
                if (k in O) createProperty(result, n, O[k]);
              }

              result.length = n;
              return result;
            }
          });
          /***/
        },

        /***/
        561:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var toAbsoluteIndex = __webpack_require__(1400);

          var toInteger = __webpack_require__(9958);

          var toLength = __webpack_require__(7466);

          var toObject = __webpack_require__(7908);

          var arraySpeciesCreate = __webpack_require__(5417);

          var createProperty = __webpack_require__(6135);

          var arrayMethodHasSpeciesSupport = __webpack_require__(1194);

          var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');
          var max = Math.max;
          var min = Math.min;
          var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF;
          var MAXIMUM_ALLOWED_LENGTH_EXCEEDED = 'Maximum allowed length exceeded'; // `Array.prototype.splice` method
          // https://tc39.es/ecma262/#sec-array.prototype.splice
          // with adding support of @@species

          $({
            target: 'Array',
            proto: true,
            forced: !HAS_SPECIES_SUPPORT
          }, {
            splice: function splice(start, deleteCount
            /* , ...items */
            ) {
              var O = toObject(this);
              var len = toLength(O.length);
              var actualStart = toAbsoluteIndex(start, len);
              var argumentsLength = arguments.length;
              var insertCount, actualDeleteCount, A, k, from, to;

              if (argumentsLength === 0) {
                insertCount = actualDeleteCount = 0;
              } else if (argumentsLength === 1) {
                insertCount = 0;
                actualDeleteCount = len - actualStart;
              } else {
                insertCount = argumentsLength - 2;
                actualDeleteCount = min(max(toInteger(deleteCount), 0), len - actualStart);
              }

              if (len + insertCount - actualDeleteCount > MAX_SAFE_INTEGER) {
                throw TypeError(MAXIMUM_ALLOWED_LENGTH_EXCEEDED);
              }

              A = arraySpeciesCreate(O, actualDeleteCount);

              for (k = 0; k < actualDeleteCount; k++) {
                from = actualStart + k;
                if (from in O) createProperty(A, k, O[from]);
              }

              A.length = actualDeleteCount;

              if (insertCount < actualDeleteCount) {
                for (k = actualStart; k < len - actualDeleteCount; k++) {
                  from = k + actualDeleteCount;
                  to = k + insertCount;
                  if (from in O) O[to] = O[from];else delete O[to];
                }

                for (k = len; k > len - actualDeleteCount + insertCount; k--) {
                  delete O[k - 1];
                }
              } else if (insertCount > actualDeleteCount) {
                for (k = len - actualDeleteCount; k > actualStart; k--) {
                  from = k + actualDeleteCount - 1;
                  to = k + insertCount - 1;
                  if (from in O) O[to] = O[from];else delete O[to];
                }
              }

              for (k = 0; k < insertCount; k++) {
                O[k + actualStart] = arguments[k + 2];
              }

              O.length = len - actualDeleteCount + insertCount;
              return A;
            }
          });
          /***/
        },

        /***/
        8309:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          var DESCRIPTORS = __webpack_require__(9781);

          var defineProperty = __webpack_require__(3070).f;

          var FunctionPrototype = Function.prototype;
          var FunctionPrototypeToString = FunctionPrototype.toString;
          var nameRE = /^\s*function ([^ (]*)/;
          var NAME = 'name'; // Function instances `.name` property
          // https://tc39.es/ecma262/#sec-function-instances-name

          if (DESCRIPTORS && !(NAME in FunctionPrototype)) {
            defineProperty(FunctionPrototype, NAME, {
              configurable: true,
              get: function get() {
                try {
                  return FunctionPrototypeToString.call(this).match(nameRE)[1];
                } catch (error) {
                  return '';
                }
              }
            });
          }
          /***/

        },

        /***/
        489:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          var $ = __webpack_require__(2109);

          var fails = __webpack_require__(7293);

          var toObject = __webpack_require__(7908);

          var nativeGetPrototypeOf = __webpack_require__(9518);

          var CORRECT_PROTOTYPE_GETTER = __webpack_require__(8544);

          var FAILS_ON_PRIMITIVES = fails(function () {
            nativeGetPrototypeOf(1);
          }); // `Object.getPrototypeOf` method
          // https://tc39.es/ecma262/#sec-object.getprototypeof

          $({
            target: 'Object',
            stat: true,
            forced: FAILS_ON_PRIMITIVES,
            sham: !CORRECT_PROTOTYPE_GETTER
          }, {
            getPrototypeOf: function getPrototypeOf(it) {
              return nativeGetPrototypeOf(toObject(it));
            }
          });
          /***/
        },

        /***/
        1539:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          var TO_STRING_TAG_SUPPORT = __webpack_require__(1694);

          var redefine = __webpack_require__(1320);

          var toString = __webpack_require__(288); // `Object.prototype.toString` method
          // https://tc39.es/ecma262/#sec-object.prototype.tostring


          if (!TO_STRING_TAG_SUPPORT) {
            redefine(Object.prototype, 'toString', toString, {
              unsafe: true
            });
          }
          /***/

        },

        /***/
        4916:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var exec = __webpack_require__(2261); // `RegExp.prototype.exec` method
          // https://tc39.es/ecma262/#sec-regexp.prototype.exec


          $({
            target: 'RegExp',
            proto: true,
            forced: /./.exec !== exec
          }, {
            exec: exec
          });
          /***/
        },

        /***/
        9714:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var redefine = __webpack_require__(1320);

          var anObject = __webpack_require__(9670);

          var fails = __webpack_require__(7293);

          var flags = __webpack_require__(7066);

          var TO_STRING = 'toString';
          var RegExpPrototype = RegExp.prototype;
          var nativeToString = RegExpPrototype[TO_STRING];
          var NOT_GENERIC = fails(function () {
            return nativeToString.call({
              source: 'a',
              flags: 'b'
            }) != '/a/b';
          }); // FF44- RegExp#toString has a wrong name

          var INCORRECT_NAME = nativeToString.name != TO_STRING; // `RegExp.prototype.toString` method
          // https://tc39.es/ecma262/#sec-regexp.prototype.tostring

          if (NOT_GENERIC || INCORRECT_NAME) {
            redefine(RegExp.prototype, TO_STRING, function toString() {
              var R = anObject(this);
              var p = String(R.source);
              var rf = R.flags;
              var f = String(rf === undefined && R instanceof RegExp && !('flags' in RegExpPrototype) ? flags.call(R) : rf);
              return '/' + p + '/' + f;
            }, {
              unsafe: true
            });
          }
          /***/

        },

        /***/
        8783:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var charAt = __webpack_require__(8710).charAt;

          var InternalStateModule = __webpack_require__(9909);

          var defineIterator = __webpack_require__(654);

          var STRING_ITERATOR = 'String Iterator';
          var setInternalState = InternalStateModule.set;
          var getInternalState = InternalStateModule.getterFor(STRING_ITERATOR); // `String.prototype[@@iterator]` method
          // https://tc39.es/ecma262/#sec-string.prototype-@@iterator

          defineIterator(String, 'String', function (iterated) {
            setInternalState(this, {
              type: STRING_ITERATOR,
              string: String(iterated),
              index: 0
            }); // `%StringIteratorPrototype%.next` method
            // https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
          }, function next() {
            var state = getInternalState(this);
            var string = state.string;
            var index = state.index;
            var point;
            if (index >= string.length) return {
              value: undefined,
              done: true
            };
            point = charAt(string, index);
            state.index += point.length;
            return {
              value: point,
              done: false
            };
          });
          /***/
        },

        /***/
        4723:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var fixRegExpWellKnownSymbolLogic = __webpack_require__(7007);

          var anObject = __webpack_require__(9670);

          var toLength = __webpack_require__(7466);

          var requireObjectCoercible = __webpack_require__(4488);

          var advanceStringIndex = __webpack_require__(1530);

          var regExpExec = __webpack_require__(7651); // @@match logic


          fixRegExpWellKnownSymbolLogic('match', 1, function (MATCH, nativeMatch, maybeCallNative) {
            return [// `String.prototype.match` method
            // https://tc39.es/ecma262/#sec-string.prototype.match
            function match(regexp) {
              var O = requireObjectCoercible(this);
              var matcher = regexp == undefined ? undefined : regexp[MATCH];
              return matcher !== undefined ? matcher.call(regexp, O) : new RegExp(regexp)[MATCH](String(O));
            }, // `RegExp.prototype[@@match]` method
            // https://tc39.es/ecma262/#sec-regexp.prototype-@@match
            function (regexp) {
              var res = maybeCallNative(nativeMatch, regexp, this);
              if (res.done) return res.value;
              var rx = anObject(regexp);
              var S = String(this);
              if (!rx.global) return regExpExec(rx, S);
              var fullUnicode = rx.unicode;
              rx.lastIndex = 0;
              var A = [];
              var n = 0;
              var result;

              while ((result = regExpExec(rx, S)) !== null) {
                var matchStr = String(result[0]);
                A[n] = matchStr;
                if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
                n++;
              }

              return n === 0 ? null : A;
            }];
          });
          /***/
        },

        /***/
        5306:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var fixRegExpWellKnownSymbolLogic = __webpack_require__(7007);

          var anObject = __webpack_require__(9670);

          var toLength = __webpack_require__(7466);

          var toInteger = __webpack_require__(9958);

          var requireObjectCoercible = __webpack_require__(4488);

          var advanceStringIndex = __webpack_require__(1530);

          var getSubstitution = __webpack_require__(647);

          var regExpExec = __webpack_require__(7651);

          var max = Math.max;
          var min = Math.min;

          var maybeToString = function maybeToString(it) {
            return it === undefined ? it : String(it);
          }; // @@replace logic


          fixRegExpWellKnownSymbolLogic('replace', 2, function (REPLACE, nativeReplace, maybeCallNative, reason) {
            var REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE = reason.REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE;
            var REPLACE_KEEPS_$0 = reason.REPLACE_KEEPS_$0;
            var UNSAFE_SUBSTITUTE = REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE ? '$' : '$0';
            return [// `String.prototype.replace` method
            // https://tc39.es/ecma262/#sec-string.prototype.replace
            function replace(searchValue, replaceValue) {
              var O = requireObjectCoercible(this);
              var replacer = searchValue == undefined ? undefined : searchValue[REPLACE];
              return replacer !== undefined ? replacer.call(searchValue, O, replaceValue) : nativeReplace.call(String(O), searchValue, replaceValue);
            }, // `RegExp.prototype[@@replace]` method
            // https://tc39.es/ecma262/#sec-regexp.prototype-@@replace
            function (regexp, replaceValue) {
              if (!REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE && REPLACE_KEEPS_$0 || typeof replaceValue === 'string' && replaceValue.indexOf(UNSAFE_SUBSTITUTE) === -1) {
                var res = maybeCallNative(nativeReplace, regexp, this, replaceValue);
                if (res.done) return res.value;
              }

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
            }];
          });
          /***/
        },

        /***/
        3123:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var fixRegExpWellKnownSymbolLogic = __webpack_require__(7007);

          var isRegExp = __webpack_require__(7850);

          var anObject = __webpack_require__(9670);

          var requireObjectCoercible = __webpack_require__(4488);

          var speciesConstructor = __webpack_require__(6707);

          var advanceStringIndex = __webpack_require__(1530);

          var toLength = __webpack_require__(7466);

          var callRegExpExec = __webpack_require__(7651);

          var regexpExec = __webpack_require__(2261);

          var fails = __webpack_require__(7293);

          var arrayPush = [].push;
          var min = Math.min;
          var MAX_UINT32 = 0xFFFFFFFF; // babel-minify transpiles RegExp('x', 'y') -> /x/y and it causes SyntaxError

          var SUPPORTS_Y = !fails(function () {
            return !RegExp(MAX_UINT32, 'y');
          }); // @@split logic

          fixRegExpWellKnownSymbolLogic('split', 2, function (SPLIT, nativeSplit, maybeCallNative) {
            var internalSplit;

            if ('abbc'.split(/(b)*/)[1] == 'c' || // eslint-disable-next-line regexp/no-empty-group -- required for testing
            'test'.split(/(?:)/, -1).length != 4 || 'ab'.split(/(?:ab)*/).length != 2 || '.'.split(/(.?)(.?)/).length != 4 || // eslint-disable-next-line regexp/no-assertion-capturing-group, regexp/no-empty-group -- required for testing
            '.'.split(/()()/).length > 1 || ''.split(/.?/).length) {
              // based on es5-shim implementation, need to rework it
              internalSplit = function internalSplit(separator, limit) {
                var string = String(requireObjectCoercible(this));
                var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
                if (lim === 0) return [];
                if (separator === undefined) return [string]; // If `separator` is not a regex, use native split

                if (!isRegExp(separator)) {
                  return nativeSplit.call(string, separator, lim);
                }

                var output = [];
                var flags = (separator.ignoreCase ? 'i' : '') + (separator.multiline ? 'm' : '') + (separator.unicode ? 'u' : '') + (separator.sticky ? 'y' : '');
                var lastLastIndex = 0; // Make `global` and avoid `lastIndex` issues by working with a copy

                var separatorCopy = new RegExp(separator.source, flags + 'g');
                var match, lastIndex, lastLength;

                while (match = regexpExec.call(separatorCopy, string)) {
                  lastIndex = separatorCopy.lastIndex;

                  if (lastIndex > lastLastIndex) {
                    output.push(string.slice(lastLastIndex, match.index));
                    if (match.length > 1 && match.index < string.length) arrayPush.apply(output, match.slice(1));
                    lastLength = match[0].length;
                    lastLastIndex = lastIndex;
                    if (output.length >= lim) break;
                  }

                  if (separatorCopy.lastIndex === match.index) separatorCopy.lastIndex++; // Avoid an infinite loop
                }

                if (lastLastIndex === string.length) {
                  if (lastLength || !separatorCopy.test('')) output.push('');
                } else output.push(string.slice(lastLastIndex));

                return output.length > lim ? output.slice(0, lim) : output;
              }; // Chakra, V8

            } else if ('0'.split(undefined, 0).length) {
              internalSplit = function internalSplit(separator, limit) {
                return separator === undefined && limit === 0 ? [] : nativeSplit.call(this, separator, limit);
              };
            } else internalSplit = nativeSplit;

            return [// `String.prototype.split` method
            // https://tc39.es/ecma262/#sec-string.prototype.split
            function split(separator, limit) {
              var O = requireObjectCoercible(this);
              var splitter = separator == undefined ? undefined : separator[SPLIT];
              return splitter !== undefined ? splitter.call(separator, O, limit) : internalSplit.call(String(O), separator, limit);
            }, // `RegExp.prototype[@@split]` method
            // https://tc39.es/ecma262/#sec-regexp.prototype-@@split
            //
            // NOTE: This cannot be properly polyfilled in engines that don't support
            // the 'y' flag.
            function (regexp, limit) {
              var res = maybeCallNative(internalSplit, regexp, this, limit, internalSplit !== nativeSplit);
              if (res.done) return res.value;
              var rx = anObject(regexp);
              var S = String(this);
              var C = speciesConstructor(rx, RegExp);
              var unicodeMatching = rx.unicode;
              var flags = (rx.ignoreCase ? 'i' : '') + (rx.multiline ? 'm' : '') + (rx.unicode ? 'u' : '') + (SUPPORTS_Y ? 'y' : 'g'); // ^(? + rx + ) is needed, in combination with some S slicing, to
              // simulate the 'y' flag.

              var splitter = new C(SUPPORTS_Y ? rx : '^(?:' + rx.source + ')', flags);
              var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
              if (lim === 0) return [];
              if (S.length === 0) return callRegExpExec(splitter, S) === null ? [S] : [];
              var p = 0;
              var q = 0;
              var A = [];

              while (q < S.length) {
                splitter.lastIndex = SUPPORTS_Y ? q : 0;
                var z = callRegExpExec(splitter, SUPPORTS_Y ? S : S.slice(q));
                var e;

                if (z === null || (e = min(toLength(splitter.lastIndex + (SUPPORTS_Y ? 0 : q)), S.length)) === p) {
                  q = advanceStringIndex(S, q, unicodeMatching);
                } else {
                  A.push(S.slice(p, q));
                  if (A.length === lim) return A;

                  for (var i = 1; i <= z.length - 1; i++) {
                    A.push(z[i]);
                    if (A.length === lim) return A;
                  }

                  q = p = e;
                }
              }

              A.push(S.slice(p));
              return A;
            }];
          }, !SUPPORTS_Y);
          /***/
        },

        /***/
        3210:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var $ = __webpack_require__(2109);

          var $trim = __webpack_require__(3111).trim;

          var forcedStringTrimMethod = __webpack_require__(6091); // `String.prototype.trim` method
          // https://tc39.es/ecma262/#sec-string.prototype.trim


          $({
            target: 'String',
            proto: true,
            forced: forcedStringTrimMethod('trim')
          }, {
            trim: function trim() {
              return $trim(this);
            }
          });
          /***/
        },

        /***/
        2990:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $copyWithin = __webpack_require__(1048);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.copyWithin` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.copywithin

          exportTypedArrayMethod('copyWithin', function copyWithin(target, start
          /* , end */
          ) {
            return $copyWithin.call(aTypedArray(this), target, start, arguments.length > 2 ? arguments[2] : undefined);
          });
          /***/
        },

        /***/
        8927:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $every = __webpack_require__(2092).every;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.every` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.every

          exportTypedArrayMethod('every', function every(callbackfn
          /* , thisArg */
          ) {
            return $every(aTypedArray(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        3105:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $fill = __webpack_require__(1285);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.fill` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.fill
          // eslint-disable-next-line no-unused-vars -- required for `.length`

          exportTypedArrayMethod('fill', function fill(value
          /* , start, end */
          ) {
            return $fill.apply(aTypedArray(this), arguments);
          });
          /***/
        },

        /***/
        5035:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $filter = __webpack_require__(2092).filter;

          var fromSpeciesAndList = __webpack_require__(3074);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.filter` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.filter

          exportTypedArrayMethod('filter', function filter(callbackfn
          /* , thisArg */
          ) {
            var list = $filter(aTypedArray(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
            return fromSpeciesAndList(this, list);
          });
          /***/
        },

        /***/
        7174:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $findIndex = __webpack_require__(2092).findIndex;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.findIndex` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.findindex

          exportTypedArrayMethod('findIndex', function findIndex(predicate
          /* , thisArg */
          ) {
            return $findIndex(aTypedArray(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        4345:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $find = __webpack_require__(2092).find;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.find` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.find

          exportTypedArrayMethod('find', function find(predicate
          /* , thisArg */
          ) {
            return $find(aTypedArray(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        2846:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $forEach = __webpack_require__(2092).forEach;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.forEach` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.foreach

          exportTypedArrayMethod('forEach', function forEach(callbackfn
          /* , thisArg */
          ) {
            $forEach(aTypedArray(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        4731:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $includes = __webpack_require__(1318).includes;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.includes` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.includes

          exportTypedArrayMethod('includes', function includes(searchElement
          /* , fromIndex */
          ) {
            return $includes(aTypedArray(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        7209:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $indexOf = __webpack_require__(1318).indexOf;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.indexOf` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.indexof

          exportTypedArrayMethod('indexOf', function indexOf(searchElement
          /* , fromIndex */
          ) {
            return $indexOf(aTypedArray(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        6319:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var global = __webpack_require__(7854);

          var ArrayBufferViewCore = __webpack_require__(260);

          var ArrayIterators = __webpack_require__(6992);

          var wellKnownSymbol = __webpack_require__(5112);

          var ITERATOR = wellKnownSymbol('iterator');
          var Uint8Array = global.Uint8Array;
          var arrayValues = ArrayIterators.values;
          var arrayKeys = ArrayIterators.keys;
          var arrayEntries = ArrayIterators.entries;
          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var nativeTypedArrayIterator = Uint8Array && Uint8Array.prototype[ITERATOR];
          var CORRECT_ITER_NAME = !!nativeTypedArrayIterator && (nativeTypedArrayIterator.name == 'values' || nativeTypedArrayIterator.name == undefined);

          var typedArrayValues = function values() {
            return arrayValues.call(aTypedArray(this));
          }; // `%TypedArray%.prototype.entries` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.entries


          exportTypedArrayMethod('entries', function entries() {
            return arrayEntries.call(aTypedArray(this));
          }); // `%TypedArray%.prototype.keys` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.keys

          exportTypedArrayMethod('keys', function keys() {
            return arrayKeys.call(aTypedArray(this));
          }); // `%TypedArray%.prototype.values` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.values

          exportTypedArrayMethod('values', typedArrayValues, !CORRECT_ITER_NAME); // `%TypedArray%.prototype[@@iterator]` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype-@@iterator

          exportTypedArrayMethod(ITERATOR, typedArrayValues, !CORRECT_ITER_NAME);
          /***/
        },

        /***/
        8867:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var $join = [].join; // `%TypedArray%.prototype.join` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.join
          // eslint-disable-next-line no-unused-vars -- required for `.length`

          exportTypedArrayMethod('join', function join(separator) {
            return $join.apply(aTypedArray(this), arguments);
          });
          /***/
        },

        /***/
        7789:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $lastIndexOf = __webpack_require__(6583);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.lastIndexOf` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.lastindexof
          // eslint-disable-next-line no-unused-vars -- required for `.length`

          exportTypedArrayMethod('lastIndexOf', function lastIndexOf(searchElement
          /* , fromIndex */
          ) {
            return $lastIndexOf.apply(aTypedArray(this), arguments);
          });
          /***/
        },

        /***/
        3739:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $map = __webpack_require__(2092).map;

          var speciesConstructor = __webpack_require__(6707);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var aTypedArrayConstructor = ArrayBufferViewCore.aTypedArrayConstructor;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.map` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.map

          exportTypedArrayMethod('map', function map(mapfn
          /* , thisArg */
          ) {
            return $map(aTypedArray(this), mapfn, arguments.length > 1 ? arguments[1] : undefined, function (O, length) {
              return new (aTypedArrayConstructor(speciesConstructor(O, O.constructor)))(length);
            });
          });
          /***/
        },

        /***/
        4483:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $reduceRight = __webpack_require__(3671).right;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.reduceRicht` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.reduceright

          exportTypedArrayMethod('reduceRight', function reduceRight(callbackfn
          /* , initialValue */
          ) {
            return $reduceRight(aTypedArray(this), callbackfn, arguments.length, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        9368:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $reduce = __webpack_require__(3671).left;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.reduce` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.reduce

          exportTypedArrayMethod('reduce', function reduce(callbackfn
          /* , initialValue */
          ) {
            return $reduce(aTypedArray(this), callbackfn, arguments.length, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        2056:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var floor = Math.floor; // `%TypedArray%.prototype.reverse` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.reverse

          exportTypedArrayMethod('reverse', function reverse() {
            var that = this;
            var length = aTypedArray(that).length;
            var middle = floor(length / 2);
            var index = 0;
            var value;

            while (index < middle) {
              value = that[index];
              that[index++] = that[--length];
              that[length] = value;
            }

            return that;
          });
          /***/
        },

        /***/
        3462:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var toLength = __webpack_require__(7466);

          var toOffset = __webpack_require__(4590);

          var toObject = __webpack_require__(7908);

          var fails = __webpack_require__(7293);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var FORCED = fails(function () {
            /* global Int8Array -- safe */
            new Int8Array(1).set({});
          }); // `%TypedArray%.prototype.set` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.set

          exportTypedArrayMethod('set', function set(arrayLike
          /* , offset */
          ) {
            aTypedArray(this);
            var offset = toOffset(arguments.length > 1 ? arguments[1] : undefined, 1);
            var length = this.length;
            var src = toObject(arrayLike);
            var len = toLength(src.length);
            var index = 0;
            if (len + offset > length) throw RangeError('Wrong length');

            while (index < len) {
              this[offset + index] = src[index++];
            }
          }, FORCED);
          /***/
        },

        /***/
        678:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var speciesConstructor = __webpack_require__(6707);

          var fails = __webpack_require__(7293);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var aTypedArrayConstructor = ArrayBufferViewCore.aTypedArrayConstructor;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var $slice = [].slice;
          var FORCED = fails(function () {
            /* global Int8Array -- safe */
            new Int8Array(1).slice();
          }); // `%TypedArray%.prototype.slice` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.slice

          exportTypedArrayMethod('slice', function slice(start, end) {
            var list = $slice.call(aTypedArray(this), start, end);
            var C = speciesConstructor(this, this.constructor);
            var index = 0;
            var length = list.length;
            var result = new (aTypedArrayConstructor(C))(length);

            while (length > index) {
              result[index] = list[index++];
            }

            return result;
          }, FORCED);
          /***/
        },

        /***/
        7462:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var $some = __webpack_require__(2092).some;

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.some` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.some

          exportTypedArrayMethod('some', function some(callbackfn
          /* , thisArg */
          ) {
            return $some(aTypedArray(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
          });
          /***/
        },

        /***/
        3824:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var $sort = [].sort; // `%TypedArray%.prototype.sort` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.sort

          exportTypedArrayMethod('sort', function sort(comparefn) {
            return $sort.call(aTypedArray(this), comparefn);
          });
          /***/
        },

        /***/
        5021:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var ArrayBufferViewCore = __webpack_require__(260);

          var toLength = __webpack_require__(7466);

          var toAbsoluteIndex = __webpack_require__(1400);

          var speciesConstructor = __webpack_require__(6707);

          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod; // `%TypedArray%.prototype.subarray` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.subarray

          exportTypedArrayMethod('subarray', function subarray(begin, end) {
            var O = aTypedArray(this);
            var length = O.length;
            var beginIndex = toAbsoluteIndex(begin, length);
            return new (speciesConstructor(O, O.constructor))(O.buffer, O.byteOffset + beginIndex * O.BYTES_PER_ELEMENT, toLength((end === undefined ? length : toAbsoluteIndex(end, length)) - beginIndex));
          });
          /***/
        },

        /***/
        2974:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var global = __webpack_require__(7854);

          var ArrayBufferViewCore = __webpack_require__(260);

          var fails = __webpack_require__(7293);

          var Int8Array = global.Int8Array;
          var aTypedArray = ArrayBufferViewCore.aTypedArray;
          var exportTypedArrayMethod = ArrayBufferViewCore.exportTypedArrayMethod;
          var $toLocaleString = [].toLocaleString;
          var $slice = [].slice; // iOS Safari 6.x fails here

          var TO_LOCALE_STRING_BUG = !!Int8Array && fails(function () {
            $toLocaleString.call(new Int8Array(1));
          });
          var FORCED = fails(function () {
            return [1, 2].toLocaleString() != new Int8Array([1, 2]).toLocaleString();
          }) || !fails(function () {
            Int8Array.prototype.toLocaleString.call([1, 2]);
          }); // `%TypedArray%.prototype.toLocaleString` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.tolocalestring

          exportTypedArrayMethod('toLocaleString', function toLocaleString() {
            return $toLocaleString.apply(TO_LOCALE_STRING_BUG ? $slice.call(aTypedArray(this)) : aTypedArray(this), arguments);
          }, FORCED);
          /***/
        },

        /***/
        5016:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var exportTypedArrayMethod = __webpack_require__(260).exportTypedArrayMethod;

          var fails = __webpack_require__(7293);

          var global = __webpack_require__(7854);

          var Uint8Array = global.Uint8Array;
          var Uint8ArrayPrototype = Uint8Array && Uint8Array.prototype || {};
          var arrayToString = [].toString;
          var arrayJoin = [].join;

          if (fails(function () {
            arrayToString.call({});
          })) {
            arrayToString = function toString() {
              return arrayJoin.call(this);
            };
          }

          var IS_NOT_ARRAY_METHOD = Uint8ArrayPrototype.toString != arrayToString; // `%TypedArray%.prototype.toString` method
          // https://tc39.es/ecma262/#sec-%typedarray%.prototype.tostring

          exportTypedArrayMethod('toString', arrayToString, IS_NOT_ARRAY_METHOD);
          /***/
        },

        /***/
        2472:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          var createTypedArrayConstructor = __webpack_require__(9843); // `Uint8Array` constructor
          // https://tc39.es/ecma262/#sec-typedarray-objects


          createTypedArrayConstructor('Uint8', function (init) {
            return function Uint8Array(data, byteOffset, length) {
              return init(this, data, byteOffset, length);
            };
          });
          /***/
        },

        /***/
        4747:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var DOMIterables = __webpack_require__(8324);

          var forEach = __webpack_require__(8533);

          var createNonEnumerableProperty = __webpack_require__(8880);

          for (var COLLECTION_NAME in DOMIterables) {
            var Collection = global[COLLECTION_NAME];
            var CollectionPrototype = Collection && Collection.prototype; // some Chrome versions have non-configurable methods on DOMTokenList

            if (CollectionPrototype && CollectionPrototype.forEach !== forEach) try {
              createNonEnumerableProperty(CollectionPrototype, 'forEach', forEach);
            } catch (error) {
              CollectionPrototype.forEach = forEach;
            }
          }
          /***/

        },

        /***/
        3948:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var DOMIterables = __webpack_require__(8324);

          var ArrayIteratorMethods = __webpack_require__(6992);

          var createNonEnumerableProperty = __webpack_require__(8880);

          var wellKnownSymbol = __webpack_require__(5112);

          var ITERATOR = wellKnownSymbol('iterator');
          var TO_STRING_TAG = wellKnownSymbol('toStringTag');
          var ArrayValues = ArrayIteratorMethods.values;

          for (var COLLECTION_NAME in DOMIterables) {
            var Collection = global[COLLECTION_NAME];
            var CollectionPrototype = Collection && Collection.prototype;

            if (CollectionPrototype) {
              // some Chrome versions have non-configurable methods on DOMTokenList
              if (CollectionPrototype[ITERATOR] !== ArrayValues) try {
                createNonEnumerableProperty(CollectionPrototype, ITERATOR, ArrayValues);
              } catch (error) {
                CollectionPrototype[ITERATOR] = ArrayValues;
              }

              if (!CollectionPrototype[TO_STRING_TAG]) {
                createNonEnumerableProperty(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
              }

              if (DOMIterables[COLLECTION_NAME]) for (var METHOD_NAME in ArrayIteratorMethods) {
                // some Chrome versions have non-configurable methods on DOMTokenList
                if (CollectionPrototype[METHOD_NAME] !== ArrayIteratorMethods[METHOD_NAME]) try {
                  createNonEnumerableProperty(CollectionPrototype, METHOD_NAME, ArrayIteratorMethods[METHOD_NAME]);
                } catch (error) {
                  CollectionPrototype[METHOD_NAME] = ArrayIteratorMethods[METHOD_NAME];
                }
              }
            }
          }
          /***/

        },

        /***/
        1637:
        /***/
        function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict"; // TODO: in core-js@4, move /modules/ dependencies to public entries for better optimization by tools like `preset-env`

          __webpack_require__(6992);

          var $ = __webpack_require__(2109);

          var getBuiltIn = __webpack_require__(5005);

          var USE_NATIVE_URL = __webpack_require__(590);

          var redefine = __webpack_require__(1320);

          var redefineAll = __webpack_require__(2248);

          var setToStringTag = __webpack_require__(8003);

          var createIteratorConstructor = __webpack_require__(4994);

          var InternalStateModule = __webpack_require__(9909);

          var anInstance = __webpack_require__(5787);

          var hasOwn = __webpack_require__(6656);

          var bind = __webpack_require__(9974);

          var classof = __webpack_require__(648);

          var anObject = __webpack_require__(9670);

          var isObject = __webpack_require__(111);

          var create = __webpack_require__(30);

          var createPropertyDescriptor = __webpack_require__(9114);

          var getIterator = __webpack_require__(8554);

          var getIteratorMethod = __webpack_require__(1246);

          var wellKnownSymbol = __webpack_require__(5112);

          var $fetch = getBuiltIn('fetch');
          var Headers = getBuiltIn('Headers');
          var ITERATOR = wellKnownSymbol('iterator');
          var URL_SEARCH_PARAMS = 'URLSearchParams';
          var URL_SEARCH_PARAMS_ITERATOR = URL_SEARCH_PARAMS + 'Iterator';
          var setInternalState = InternalStateModule.set;
          var getInternalParamsState = InternalStateModule.getterFor(URL_SEARCH_PARAMS);
          var getInternalIteratorState = InternalStateModule.getterFor(URL_SEARCH_PARAMS_ITERATOR);
          var plus = /\+/g;
          var sequences = Array(4);

          var percentSequence = function percentSequence(bytes) {
            return sequences[bytes - 1] || (sequences[bytes - 1] = RegExp('((?:%[\\da-f]{2}){' + bytes + '})', 'gi'));
          };

          var percentDecode = function percentDecode(sequence) {
            try {
              return decodeURIComponent(sequence);
            } catch (error) {
              return sequence;
            }
          };

          var deserialize = function deserialize(it) {
            var result = it.replace(plus, ' ');
            var bytes = 4;

            try {
              return decodeURIComponent(result);
            } catch (error) {
              while (bytes) {
                result = result.replace(percentSequence(bytes--), percentDecode);
              }

              return result;
            }
          };

          var find = /[!'()~]|%20/g;
          var replace = {
            '!': '%21',
            "'": '%27',
            '(': '%28',
            ')': '%29',
            '~': '%7E',
            '%20': '+'
          };

          var replacer = function replacer(match) {
            return replace[match];
          };

          var serialize = function serialize(it) {
            return encodeURIComponent(it).replace(find, replacer);
          };

          var parseSearchParams = function parseSearchParams(result, query) {
            if (query) {
              var attributes = query.split('&');
              var index = 0;
              var attribute, entry;

              while (index < attributes.length) {
                attribute = attributes[index++];

                if (attribute.length) {
                  entry = attribute.split('=');
                  result.push({
                    key: deserialize(entry.shift()),
                    value: deserialize(entry.join('='))
                  });
                }
              }
            }
          };

          var updateSearchParams = function updateSearchParams(query) {
            this.entries.length = 0;
            parseSearchParams(this.entries, query);
          };

          var validateArgumentsLength = function validateArgumentsLength(passed, required) {
            if (passed < required) throw TypeError('Not enough arguments');
          };

          var URLSearchParamsIterator = createIteratorConstructor(function Iterator(params, kind) {
            setInternalState(this, {
              type: URL_SEARCH_PARAMS_ITERATOR,
              iterator: getIterator(getInternalParamsState(params).entries),
              kind: kind
            });
          }, 'Iterator', function next() {
            var state = getInternalIteratorState(this);
            var kind = state.kind;
            var step = state.iterator.next();
            var entry = step.value;

            if (!step.done) {
              step.value = kind === 'keys' ? entry.key : kind === 'values' ? entry.value : [entry.key, entry.value];
            }

            return step;
          }); // `URLSearchParams` constructor
          // https://url.spec.whatwg.org/#interface-urlsearchparams

          var URLSearchParamsConstructor = function URLSearchParams()
          /* init */
          {
            anInstance(this, URLSearchParamsConstructor, URL_SEARCH_PARAMS);
            var init = arguments.length > 0 ? arguments[0] : undefined;
            var that = this;
            var entries = [];
            var iteratorMethod, iterator, next, step, entryIterator, entryNext, first, second, key;
            setInternalState(that, {
              type: URL_SEARCH_PARAMS,
              entries: entries,
              updateURL: function updateURL() {
                /* empty */
              },
              updateSearchParams: updateSearchParams
            });

            if (init !== undefined) {
              if (isObject(init)) {
                iteratorMethod = getIteratorMethod(init);

                if (typeof iteratorMethod === 'function') {
                  iterator = iteratorMethod.call(init);
                  next = iterator.next;

                  while (!(step = next.call(iterator)).done) {
                    entryIterator = getIterator(anObject(step.value));
                    entryNext = entryIterator.next;
                    if ((first = entryNext.call(entryIterator)).done || (second = entryNext.call(entryIterator)).done || !entryNext.call(entryIterator).done) throw TypeError('Expected sequence with length 2');
                    entries.push({
                      key: first.value + '',
                      value: second.value + ''
                    });
                  }
                } else for (key in init) {
                  if (hasOwn(init, key)) entries.push({
                    key: key,
                    value: init[key] + ''
                  });
                }
              } else {
                parseSearchParams(entries, typeof init === 'string' ? init.charAt(0) === '?' ? init.slice(1) : init : init + '');
              }
            }
          };

          var URLSearchParamsPrototype = URLSearchParamsConstructor.prototype;
          redefineAll(URLSearchParamsPrototype, {
            // `URLSearchParams.prototype.append` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-append
            append: function append(name, value) {
              validateArgumentsLength(arguments.length, 2);
              var state = getInternalParamsState(this);
              state.entries.push({
                key: name + '',
                value: value + ''
              });
              state.updateURL();
            },
            // `URLSearchParams.prototype.delete` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-delete
            'delete': function _delete(name) {
              validateArgumentsLength(arguments.length, 1);
              var state = getInternalParamsState(this);
              var entries = state.entries;
              var key = name + '';
              var index = 0;

              while (index < entries.length) {
                if (entries[index].key === key) entries.splice(index, 1);else index++;
              }

              state.updateURL();
            },
            // `URLSearchParams.prototype.get` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-get
            get: function get(name) {
              validateArgumentsLength(arguments.length, 1);
              var entries = getInternalParamsState(this).entries;
              var key = name + '';
              var index = 0;

              for (; index < entries.length; index++) {
                if (entries[index].key === key) return entries[index].value;
              }

              return null;
            },
            // `URLSearchParams.prototype.getAll` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-getall
            getAll: function getAll(name) {
              validateArgumentsLength(arguments.length, 1);
              var entries = getInternalParamsState(this).entries;
              var key = name + '';
              var result = [];
              var index = 0;

              for (; index < entries.length; index++) {
                if (entries[index].key === key) result.push(entries[index].value);
              }

              return result;
            },
            // `URLSearchParams.prototype.has` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-has
            has: function has(name) {
              validateArgumentsLength(arguments.length, 1);
              var entries = getInternalParamsState(this).entries;
              var key = name + '';
              var index = 0;

              while (index < entries.length) {
                if (entries[index++].key === key) return true;
              }

              return false;
            },
            // `URLSearchParams.prototype.set` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-set
            set: function set(name, value) {
              validateArgumentsLength(arguments.length, 1);
              var state = getInternalParamsState(this);
              var entries = state.entries;
              var found = false;
              var key = name + '';
              var val = value + '';
              var index = 0;
              var entry;

              for (; index < entries.length; index++) {
                entry = entries[index];

                if (entry.key === key) {
                  if (found) entries.splice(index--, 1);else {
                    found = true;
                    entry.value = val;
                  }
                }
              }

              if (!found) entries.push({
                key: key,
                value: val
              });
              state.updateURL();
            },
            // `URLSearchParams.prototype.sort` method
            // https://url.spec.whatwg.org/#dom-urlsearchparams-sort
            sort: function sort() {
              var state = getInternalParamsState(this);
              var entries = state.entries; // Array#sort is not stable in some engines

              var slice = entries.slice();
              var entry, entriesIndex, sliceIndex;
              entries.length = 0;

              for (sliceIndex = 0; sliceIndex < slice.length; sliceIndex++) {
                entry = slice[sliceIndex];

                for (entriesIndex = 0; entriesIndex < sliceIndex; entriesIndex++) {
                  if (entries[entriesIndex].key > entry.key) {
                    entries.splice(entriesIndex, 0, entry);
                    break;
                  }
                }

                if (entriesIndex === sliceIndex) entries.push(entry);
              }

              state.updateURL();
            },
            // `URLSearchParams.prototype.forEach` method
            forEach: function forEach(callback
            /* , thisArg */
            ) {
              var entries = getInternalParamsState(this).entries;
              var boundFunction = bind(callback, arguments.length > 1 ? arguments[1] : undefined, 3);
              var index = 0;
              var entry;

              while (index < entries.length) {
                entry = entries[index++];
                boundFunction(entry.value, entry.key, this);
              }
            },
            // `URLSearchParams.prototype.keys` method
            keys: function keys() {
              return new URLSearchParamsIterator(this, 'keys');
            },
            // `URLSearchParams.prototype.values` method
            values: function values() {
              return new URLSearchParamsIterator(this, 'values');
            },
            // `URLSearchParams.prototype.entries` method
            entries: function entries() {
              return new URLSearchParamsIterator(this, 'entries');
            }
          }, {
            enumerable: true
          }); // `URLSearchParams.prototype[@@iterator]` method

          redefine(URLSearchParamsPrototype, ITERATOR, URLSearchParamsPrototype.entries); // `URLSearchParams.prototype.toString` method
          // https://url.spec.whatwg.org/#urlsearchparams-stringification-behavior

          redefine(URLSearchParamsPrototype, 'toString', function toString() {
            var entries = getInternalParamsState(this).entries;
            var result = [];
            var index = 0;
            var entry;

            while (index < entries.length) {
              entry = entries[index++];
              result.push(serialize(entry.key) + '=' + serialize(entry.value));
            }

            return result.join('&');
          }, {
            enumerable: true
          });
          setToStringTag(URLSearchParamsConstructor, URL_SEARCH_PARAMS);
          $({
            global: true,
            forced: !USE_NATIVE_URL
          }, {
            URLSearchParams: URLSearchParamsConstructor
          }); // Wrap `fetch` for correct work with polyfilled `URLSearchParams`
          // https://github.com/zloirock/core-js/issues/674

          if (!USE_NATIVE_URL && typeof $fetch == 'function' && typeof Headers == 'function') {
            $({
              global: true,
              enumerable: true,
              forced: true
            }, {
              fetch: function fetch(input
              /* , init */
              ) {
                var args = [input];
                var init, body, headers;

                if (arguments.length > 1) {
                  init = arguments[1];

                  if (isObject(init)) {
                    body = init.body;

                    if (classof(body) === URL_SEARCH_PARAMS) {
                      headers = init.headers ? new Headers(init.headers) : new Headers();

                      if (!headers.has('content-type')) {
                        headers.set('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
                      }

                      init = create(init, {
                        body: createPropertyDescriptor(0, String(body)),
                        headers: createPropertyDescriptor(0, headers)
                      });
                    }
                  }

                  args.push(init);
                }

                return $fetch.apply(this, args);
              }
            });
          }

          module.exports = {
            URLSearchParams: URLSearchParamsConstructor,
            getState: getInternalParamsState
          };
          /***/
        },

        /***/
        285:
        /***/
        function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
          "use strict"; // TODO: in core-js@4, move /modules/ dependencies to public entries for better optimization by tools like `preset-env`

          __webpack_require__(8783);

          var $ = __webpack_require__(2109);

          var DESCRIPTORS = __webpack_require__(9781);

          var USE_NATIVE_URL = __webpack_require__(590);

          var global = __webpack_require__(7854);

          var defineProperties = __webpack_require__(6048);

          var redefine = __webpack_require__(1320);

          var anInstance = __webpack_require__(5787);

          var has = __webpack_require__(6656);

          var assign = __webpack_require__(1574);

          var arrayFrom = __webpack_require__(8457);

          var codeAt = __webpack_require__(8710).codeAt;

          var toASCII = __webpack_require__(3197);

          var setToStringTag = __webpack_require__(8003);

          var URLSearchParamsModule = __webpack_require__(1637);

          var InternalStateModule = __webpack_require__(9909);

          var NativeURL = global.URL;
          var URLSearchParams = URLSearchParamsModule.URLSearchParams;
          var getInternalSearchParamsState = URLSearchParamsModule.getState;
          var setInternalState = InternalStateModule.set;
          var getInternalURLState = InternalStateModule.getterFor('URL');
          var floor = Math.floor;
          var pow = Math.pow;
          var INVALID_AUTHORITY = 'Invalid authority';
          var INVALID_SCHEME = 'Invalid scheme';
          var INVALID_HOST = 'Invalid host';
          var INVALID_PORT = 'Invalid port';
          var ALPHA = /[A-Za-z]/;
          var ALPHANUMERIC = /[\d+-.A-Za-z]/;
          var DIGIT = /\d/;
          var HEX_START = /^(0x|0X)/;
          var OCT = /^[0-7]+$/;
          var DEC = /^\d+$/;
          var HEX = /^[\dA-Fa-f]+$/;
          /* eslint-disable no-control-regex -- safe */

          var FORBIDDEN_HOST_CODE_POINT = /[\u0000\t\u000A\u000D #%/:?@[\\]]/;
          var FORBIDDEN_HOST_CODE_POINT_EXCLUDING_PERCENT = /[\u0000\t\u000A\u000D #/:?@[\\]]/;
          var LEADING_AND_TRAILING_C0_CONTROL_OR_SPACE = /^[\u0000-\u001F ]+|[\u0000-\u001F ]+$/g;
          var TAB_AND_NEW_LINE = /[\t\u000A\u000D]/g;
          /* eslint-enable no-control-regex -- safe */

          var EOF;

          var parseHost = function parseHost(url, input) {
            var result, codePoints, index;

            if (input.charAt(0) == '[') {
              if (input.charAt(input.length - 1) != ']') return INVALID_HOST;
              result = parseIPv6(input.slice(1, -1));
              if (!result) return INVALID_HOST;
              url.host = result; // opaque host
            } else if (!isSpecial(url)) {
              if (FORBIDDEN_HOST_CODE_POINT_EXCLUDING_PERCENT.test(input)) return INVALID_HOST;
              result = '';
              codePoints = arrayFrom(input);

              for (index = 0; index < codePoints.length; index++) {
                result += percentEncode(codePoints[index], C0ControlPercentEncodeSet);
              }

              url.host = result;
            } else {
              input = toASCII(input);
              if (FORBIDDEN_HOST_CODE_POINT.test(input)) return INVALID_HOST;
              result = parseIPv4(input);
              if (result === null) return INVALID_HOST;
              url.host = result;
            }
          };

          var parseIPv4 = function parseIPv4(input) {
            var parts = input.split('.');
            var partsLength, numbers, index, part, radix, number, ipv4;

            if (parts.length && parts[parts.length - 1] == '') {
              parts.pop();
            }

            partsLength = parts.length;
            if (partsLength > 4) return input;
            numbers = [];

            for (index = 0; index < partsLength; index++) {
              part = parts[index];
              if (part == '') return input;
              radix = 10;

              if (part.length > 1 && part.charAt(0) == '0') {
                radix = HEX_START.test(part) ? 16 : 8;
                part = part.slice(radix == 8 ? 1 : 2);
              }

              if (part === '') {
                number = 0;
              } else {
                if (!(radix == 10 ? DEC : radix == 8 ? OCT : HEX).test(part)) return input;
                number = parseInt(part, radix);
              }

              numbers.push(number);
            }

            for (index = 0; index < partsLength; index++) {
              number = numbers[index];

              if (index == partsLength - 1) {
                if (number >= pow(256, 5 - partsLength)) return null;
              } else if (number > 255) return null;
            }

            ipv4 = numbers.pop();

            for (index = 0; index < numbers.length; index++) {
              ipv4 += numbers[index] * pow(256, 3 - index);
            }

            return ipv4;
          }; // eslint-disable-next-line max-statements -- TODO


          var parseIPv6 = function parseIPv6(input) {
            var address = [0, 0, 0, 0, 0, 0, 0, 0];
            var pieceIndex = 0;
            var compress = null;
            var pointer = 0;
            var value, length, numbersSeen, ipv4Piece, number, swaps, swap;

            var _char = function _char() {
              return input.charAt(pointer);
            };

            if (_char() == ':') {
              if (input.charAt(1) != ':') return;
              pointer += 2;
              pieceIndex++;
              compress = pieceIndex;
            }

            while (_char()) {
              if (pieceIndex == 8) return;

              if (_char() == ':') {
                if (compress !== null) return;
                pointer++;
                pieceIndex++;
                compress = pieceIndex;
                continue;
              }

              value = length = 0;

              while (length < 4 && HEX.test(_char())) {
                value = value * 16 + parseInt(_char(), 16);
                pointer++;
                length++;
              }

              if (_char() == '.') {
                if (length == 0) return;
                pointer -= length;
                if (pieceIndex > 6) return;
                numbersSeen = 0;

                while (_char()) {
                  ipv4Piece = null;

                  if (numbersSeen > 0) {
                    if (_char() == '.' && numbersSeen < 4) pointer++;else return;
                  }

                  if (!DIGIT.test(_char())) return;

                  while (DIGIT.test(_char())) {
                    number = parseInt(_char(), 10);
                    if (ipv4Piece === null) ipv4Piece = number;else if (ipv4Piece == 0) return;else ipv4Piece = ipv4Piece * 10 + number;
                    if (ipv4Piece > 255) return;
                    pointer++;
                  }

                  address[pieceIndex] = address[pieceIndex] * 256 + ipv4Piece;
                  numbersSeen++;
                  if (numbersSeen == 2 || numbersSeen == 4) pieceIndex++;
                }

                if (numbersSeen != 4) return;
                break;
              } else if (_char() == ':') {
                pointer++;
                if (!_char()) return;
              } else if (_char()) return;

              address[pieceIndex++] = value;
            }

            if (compress !== null) {
              swaps = pieceIndex - compress;
              pieceIndex = 7;

              while (pieceIndex != 0 && swaps > 0) {
                swap = address[pieceIndex];
                address[pieceIndex--] = address[compress + swaps - 1];
                address[compress + --swaps] = swap;
              }
            } else if (pieceIndex != 8) return;

            return address;
          };

          var findLongestZeroSequence = function findLongestZeroSequence(ipv6) {
            var maxIndex = null;
            var maxLength = 1;
            var currStart = null;
            var currLength = 0;
            var index = 0;

            for (; index < 8; index++) {
              if (ipv6[index] !== 0) {
                if (currLength > maxLength) {
                  maxIndex = currStart;
                  maxLength = currLength;
                }

                currStart = null;
                currLength = 0;
              } else {
                if (currStart === null) currStart = index;
                ++currLength;
              }
            }

            if (currLength > maxLength) {
              maxIndex = currStart;
              maxLength = currLength;
            }

            return maxIndex;
          };

          var serializeHost = function serializeHost(host) {
            var result, index, compress, ignore0; // ipv4

            if (typeof host == 'number') {
              result = [];

              for (index = 0; index < 4; index++) {
                result.unshift(host % 256);
                host = floor(host / 256);
              }

              return result.join('.'); // ipv6
            } else if (_typeof2(host) == 'object') {
              result = '';
              compress = findLongestZeroSequence(host);

              for (index = 0; index < 8; index++) {
                if (ignore0 && host[index] === 0) continue;
                if (ignore0) ignore0 = false;

                if (compress === index) {
                  result += index ? ':' : '::';
                  ignore0 = true;
                } else {
                  result += host[index].toString(16);
                  if (index < 7) result += ':';
                }
              }

              return '[' + result + ']';
            }

            return host;
          };

          var C0ControlPercentEncodeSet = {};
          var fragmentPercentEncodeSet = assign({}, C0ControlPercentEncodeSet, {
            ' ': 1,
            '"': 1,
            '<': 1,
            '>': 1,
            '`': 1
          });
          var pathPercentEncodeSet = assign({}, fragmentPercentEncodeSet, {
            '#': 1,
            '?': 1,
            '{': 1,
            '}': 1
          });
          var userinfoPercentEncodeSet = assign({}, pathPercentEncodeSet, {
            '/': 1,
            ':': 1,
            ';': 1,
            '=': 1,
            '@': 1,
            '[': 1,
            '\\': 1,
            ']': 1,
            '^': 1,
            '|': 1
          });

          var percentEncode = function percentEncode(_char2, set) {
            var code = codeAt(_char2, 0);
            return code > 0x20 && code < 0x7F && !has(set, _char2) ? _char2 : encodeURIComponent(_char2);
          };

          var specialSchemes = {
            ftp: 21,
            file: null,
            http: 80,
            https: 443,
            ws: 80,
            wss: 443
          };

          var isSpecial = function isSpecial(url) {
            return has(specialSchemes, url.scheme);
          };

          var includesCredentials = function includesCredentials(url) {
            return url.username != '' || url.password != '';
          };

          var cannotHaveUsernamePasswordPort = function cannotHaveUsernamePasswordPort(url) {
            return !url.host || url.cannotBeABaseURL || url.scheme == 'file';
          };

          var isWindowsDriveLetter = function isWindowsDriveLetter(string, normalized) {
            var second;
            return string.length == 2 && ALPHA.test(string.charAt(0)) && ((second = string.charAt(1)) == ':' || !normalized && second == '|');
          };

          var startsWithWindowsDriveLetter = function startsWithWindowsDriveLetter(string) {
            var third;
            return string.length > 1 && isWindowsDriveLetter(string.slice(0, 2)) && (string.length == 2 || (third = string.charAt(2)) === '/' || third === '\\' || third === '?' || third === '#');
          };

          var shortenURLsPath = function shortenURLsPath(url) {
            var path = url.path;
            var pathSize = path.length;

            if (pathSize && (url.scheme != 'file' || pathSize != 1 || !isWindowsDriveLetter(path[0], true))) {
              path.pop();
            }
          };

          var isSingleDot = function isSingleDot(segment) {
            return segment === '.' || segment.toLowerCase() === '%2e';
          };

          var isDoubleDot = function isDoubleDot(segment) {
            segment = segment.toLowerCase();
            return segment === '..' || segment === '%2e.' || segment === '.%2e' || segment === '%2e%2e';
          }; // States:


          var SCHEME_START = {};
          var SCHEME = {};
          var NO_SCHEME = {};
          var SPECIAL_RELATIVE_OR_AUTHORITY = {};
          var PATH_OR_AUTHORITY = {};
          var RELATIVE = {};
          var RELATIVE_SLASH = {};
          var SPECIAL_AUTHORITY_SLASHES = {};
          var SPECIAL_AUTHORITY_IGNORE_SLASHES = {};
          var AUTHORITY = {};
          var HOST = {};
          var HOSTNAME = {};
          var PORT = {};
          var FILE = {};
          var FILE_SLASH = {};
          var FILE_HOST = {};
          var PATH_START = {};
          var PATH = {};
          var CANNOT_BE_A_BASE_URL_PATH = {};
          var QUERY = {};
          var FRAGMENT = {}; // eslint-disable-next-line max-statements -- TODO

          var parseURL = function parseURL(url, input, stateOverride, base) {
            var state = stateOverride || SCHEME_START;
            var pointer = 0;
            var buffer = '';
            var seenAt = false;
            var seenBracket = false;
            var seenPasswordToken = false;

            var codePoints, _char3, bufferCodePoints, failure;

            if (!stateOverride) {
              url.scheme = '';
              url.username = '';
              url.password = '';
              url.host = null;
              url.port = null;
              url.path = [];
              url.query = null;
              url.fragment = null;
              url.cannotBeABaseURL = false;
              input = input.replace(LEADING_AND_TRAILING_C0_CONTROL_OR_SPACE, '');
            }

            input = input.replace(TAB_AND_NEW_LINE, '');
            codePoints = arrayFrom(input);

            while (pointer <= codePoints.length) {
              _char3 = codePoints[pointer];

              switch (state) {
                case SCHEME_START:
                  if (_char3 && ALPHA.test(_char3)) {
                    buffer += _char3.toLowerCase();
                    state = SCHEME;
                  } else if (!stateOverride) {
                    state = NO_SCHEME;
                    continue;
                  } else return INVALID_SCHEME;

                  break;

                case SCHEME:
                  if (_char3 && (ALPHANUMERIC.test(_char3) || _char3 == '+' || _char3 == '-' || _char3 == '.')) {
                    buffer += _char3.toLowerCase();
                  } else if (_char3 == ':') {
                    if (stateOverride && (isSpecial(url) != has(specialSchemes, buffer) || buffer == 'file' && (includesCredentials(url) || url.port !== null) || url.scheme == 'file' && !url.host)) return;
                    url.scheme = buffer;

                    if (stateOverride) {
                      if (isSpecial(url) && specialSchemes[url.scheme] == url.port) url.port = null;
                      return;
                    }

                    buffer = '';

                    if (url.scheme == 'file') {
                      state = FILE;
                    } else if (isSpecial(url) && base && base.scheme == url.scheme) {
                      state = SPECIAL_RELATIVE_OR_AUTHORITY;
                    } else if (isSpecial(url)) {
                      state = SPECIAL_AUTHORITY_SLASHES;
                    } else if (codePoints[pointer + 1] == '/') {
                      state = PATH_OR_AUTHORITY;
                      pointer++;
                    } else {
                      url.cannotBeABaseURL = true;
                      url.path.push('');
                      state = CANNOT_BE_A_BASE_URL_PATH;
                    }
                  } else if (!stateOverride) {
                    buffer = '';
                    state = NO_SCHEME;
                    pointer = 0;
                    continue;
                  } else return INVALID_SCHEME;

                  break;

                case NO_SCHEME:
                  if (!base || base.cannotBeABaseURL && _char3 != '#') return INVALID_SCHEME;

                  if (base.cannotBeABaseURL && _char3 == '#') {
                    url.scheme = base.scheme;
                    url.path = base.path.slice();
                    url.query = base.query;
                    url.fragment = '';
                    url.cannotBeABaseURL = true;
                    state = FRAGMENT;
                    break;
                  }

                  state = base.scheme == 'file' ? FILE : RELATIVE;
                  continue;

                case SPECIAL_RELATIVE_OR_AUTHORITY:
                  if (_char3 == '/' && codePoints[pointer + 1] == '/') {
                    state = SPECIAL_AUTHORITY_IGNORE_SLASHES;
                    pointer++;
                  } else {
                    state = RELATIVE;
                    continue;
                  }

                  break;

                case PATH_OR_AUTHORITY:
                  if (_char3 == '/') {
                    state = AUTHORITY;
                    break;
                  } else {
                    state = PATH;
                    continue;
                  }

                case RELATIVE:
                  url.scheme = base.scheme;

                  if (_char3 == EOF) {
                    url.username = base.username;
                    url.password = base.password;
                    url.host = base.host;
                    url.port = base.port;
                    url.path = base.path.slice();
                    url.query = base.query;
                  } else if (_char3 == '/' || _char3 == '\\' && isSpecial(url)) {
                    state = RELATIVE_SLASH;
                  } else if (_char3 == '?') {
                    url.username = base.username;
                    url.password = base.password;
                    url.host = base.host;
                    url.port = base.port;
                    url.path = base.path.slice();
                    url.query = '';
                    state = QUERY;
                  } else if (_char3 == '#') {
                    url.username = base.username;
                    url.password = base.password;
                    url.host = base.host;
                    url.port = base.port;
                    url.path = base.path.slice();
                    url.query = base.query;
                    url.fragment = '';
                    state = FRAGMENT;
                  } else {
                    url.username = base.username;
                    url.password = base.password;
                    url.host = base.host;
                    url.port = base.port;
                    url.path = base.path.slice();
                    url.path.pop();
                    state = PATH;
                    continue;
                  }

                  break;

                case RELATIVE_SLASH:
                  if (isSpecial(url) && (_char3 == '/' || _char3 == '\\')) {
                    state = SPECIAL_AUTHORITY_IGNORE_SLASHES;
                  } else if (_char3 == '/') {
                    state = AUTHORITY;
                  } else {
                    url.username = base.username;
                    url.password = base.password;
                    url.host = base.host;
                    url.port = base.port;
                    state = PATH;
                    continue;
                  }

                  break;

                case SPECIAL_AUTHORITY_SLASHES:
                  state = SPECIAL_AUTHORITY_IGNORE_SLASHES;
                  if (_char3 != '/' || buffer.charAt(pointer + 1) != '/') continue;
                  pointer++;
                  break;

                case SPECIAL_AUTHORITY_IGNORE_SLASHES:
                  if (_char3 != '/' && _char3 != '\\') {
                    state = AUTHORITY;
                    continue;
                  }

                  break;

                case AUTHORITY:
                  if (_char3 == '@') {
                    if (seenAt) buffer = '%40' + buffer;
                    seenAt = true;
                    bufferCodePoints = arrayFrom(buffer);

                    for (var i = 0; i < bufferCodePoints.length; i++) {
                      var codePoint = bufferCodePoints[i];

                      if (codePoint == ':' && !seenPasswordToken) {
                        seenPasswordToken = true;
                        continue;
                      }

                      var encodedCodePoints = percentEncode(codePoint, userinfoPercentEncodeSet);
                      if (seenPasswordToken) url.password += encodedCodePoints;else url.username += encodedCodePoints;
                    }

                    buffer = '';
                  } else if (_char3 == EOF || _char3 == '/' || _char3 == '?' || _char3 == '#' || _char3 == '\\' && isSpecial(url)) {
                    if (seenAt && buffer == '') return INVALID_AUTHORITY;
                    pointer -= arrayFrom(buffer).length + 1;
                    buffer = '';
                    state = HOST;
                  } else buffer += _char3;

                  break;

                case HOST:
                case HOSTNAME:
                  if (stateOverride && url.scheme == 'file') {
                    state = FILE_HOST;
                    continue;
                  } else if (_char3 == ':' && !seenBracket) {
                    if (buffer == '') return INVALID_HOST;
                    failure = parseHost(url, buffer);
                    if (failure) return failure;
                    buffer = '';
                    state = PORT;
                    if (stateOverride == HOSTNAME) return;
                  } else if (_char3 == EOF || _char3 == '/' || _char3 == '?' || _char3 == '#' || _char3 == '\\' && isSpecial(url)) {
                    if (isSpecial(url) && buffer == '') return INVALID_HOST;
                    if (stateOverride && buffer == '' && (includesCredentials(url) || url.port !== null)) return;
                    failure = parseHost(url, buffer);
                    if (failure) return failure;
                    buffer = '';
                    state = PATH_START;
                    if (stateOverride) return;
                    continue;
                  } else {
                    if (_char3 == '[') seenBracket = true;else if (_char3 == ']') seenBracket = false;
                    buffer += _char3;
                  }

                  break;

                case PORT:
                  if (DIGIT.test(_char3)) {
                    buffer += _char3;
                  } else if (_char3 == EOF || _char3 == '/' || _char3 == '?' || _char3 == '#' || _char3 == '\\' && isSpecial(url) || stateOverride) {
                    if (buffer != '') {
                      var port = parseInt(buffer, 10);
                      if (port > 0xFFFF) return INVALID_PORT;
                      url.port = isSpecial(url) && port === specialSchemes[url.scheme] ? null : port;
                      buffer = '';
                    }

                    if (stateOverride) return;
                    state = PATH_START;
                    continue;
                  } else return INVALID_PORT;

                  break;

                case FILE:
                  url.scheme = 'file';
                  if (_char3 == '/' || _char3 == '\\') state = FILE_SLASH;else if (base && base.scheme == 'file') {
                    if (_char3 == EOF) {
                      url.host = base.host;
                      url.path = base.path.slice();
                      url.query = base.query;
                    } else if (_char3 == '?') {
                      url.host = base.host;
                      url.path = base.path.slice();
                      url.query = '';
                      state = QUERY;
                    } else if (_char3 == '#') {
                      url.host = base.host;
                      url.path = base.path.slice();
                      url.query = base.query;
                      url.fragment = '';
                      state = FRAGMENT;
                    } else {
                      if (!startsWithWindowsDriveLetter(codePoints.slice(pointer).join(''))) {
                        url.host = base.host;
                        url.path = base.path.slice();
                        shortenURLsPath(url);
                      }

                      state = PATH;
                      continue;
                    }
                  } else {
                    state = PATH;
                    continue;
                  }
                  break;

                case FILE_SLASH:
                  if (_char3 == '/' || _char3 == '\\') {
                    state = FILE_HOST;
                    break;
                  }

                  if (base && base.scheme == 'file' && !startsWithWindowsDriveLetter(codePoints.slice(pointer).join(''))) {
                    if (isWindowsDriveLetter(base.path[0], true)) url.path.push(base.path[0]);else url.host = base.host;
                  }

                  state = PATH;
                  continue;

                case FILE_HOST:
                  if (_char3 == EOF || _char3 == '/' || _char3 == '\\' || _char3 == '?' || _char3 == '#') {
                    if (!stateOverride && isWindowsDriveLetter(buffer)) {
                      state = PATH;
                    } else if (buffer == '') {
                      url.host = '';
                      if (stateOverride) return;
                      state = PATH_START;
                    } else {
                      failure = parseHost(url, buffer);
                      if (failure) return failure;
                      if (url.host == 'localhost') url.host = '';
                      if (stateOverride) return;
                      buffer = '';
                      state = PATH_START;
                    }

                    continue;
                  } else buffer += _char3;

                  break;

                case PATH_START:
                  if (isSpecial(url)) {
                    state = PATH;
                    if (_char3 != '/' && _char3 != '\\') continue;
                  } else if (!stateOverride && _char3 == '?') {
                    url.query = '';
                    state = QUERY;
                  } else if (!stateOverride && _char3 == '#') {
                    url.fragment = '';
                    state = FRAGMENT;
                  } else if (_char3 != EOF) {
                    state = PATH;
                    if (_char3 != '/') continue;
                  }

                  break;

                case PATH:
                  if (_char3 == EOF || _char3 == '/' || _char3 == '\\' && isSpecial(url) || !stateOverride && (_char3 == '?' || _char3 == '#')) {
                    if (isDoubleDot(buffer)) {
                      shortenURLsPath(url);

                      if (_char3 != '/' && !(_char3 == '\\' && isSpecial(url))) {
                        url.path.push('');
                      }
                    } else if (isSingleDot(buffer)) {
                      if (_char3 != '/' && !(_char3 == '\\' && isSpecial(url))) {
                        url.path.push('');
                      }
                    } else {
                      if (url.scheme == 'file' && !url.path.length && isWindowsDriveLetter(buffer)) {
                        if (url.host) url.host = '';
                        buffer = buffer.charAt(0) + ':'; // normalize windows drive letter
                      }

                      url.path.push(buffer);
                    }

                    buffer = '';

                    if (url.scheme == 'file' && (_char3 == EOF || _char3 == '?' || _char3 == '#')) {
                      while (url.path.length > 1 && url.path[0] === '') {
                        url.path.shift();
                      }
                    }

                    if (_char3 == '?') {
                      url.query = '';
                      state = QUERY;
                    } else if (_char3 == '#') {
                      url.fragment = '';
                      state = FRAGMENT;
                    }
                  } else {
                    buffer += percentEncode(_char3, pathPercentEncodeSet);
                  }

                  break;

                case CANNOT_BE_A_BASE_URL_PATH:
                  if (_char3 == '?') {
                    url.query = '';
                    state = QUERY;
                  } else if (_char3 == '#') {
                    url.fragment = '';
                    state = FRAGMENT;
                  } else if (_char3 != EOF) {
                    url.path[0] += percentEncode(_char3, C0ControlPercentEncodeSet);
                  }

                  break;

                case QUERY:
                  if (!stateOverride && _char3 == '#') {
                    url.fragment = '';
                    state = FRAGMENT;
                  } else if (_char3 != EOF) {
                    if (_char3 == "'" && isSpecial(url)) url.query += '%27';else if (_char3 == '#') url.query += '%23';else url.query += percentEncode(_char3, C0ControlPercentEncodeSet);
                  }

                  break;

                case FRAGMENT:
                  if (_char3 != EOF) url.fragment += percentEncode(_char3, fragmentPercentEncodeSet);
                  break;
              }

              pointer++;
            }
          }; // `URL` constructor
          // https://url.spec.whatwg.org/#url-class


          var URLConstructor = function URL(url
          /* , base */
          ) {
            var that = anInstance(this, URLConstructor, 'URL');
            var base = arguments.length > 1 ? arguments[1] : undefined;
            var urlString = String(url);
            var state = setInternalState(that, {
              type: 'URL'
            });
            var baseState, failure;

            if (base !== undefined) {
              if (base instanceof URLConstructor) baseState = getInternalURLState(base);else {
                failure = parseURL(baseState = {}, String(base));
                if (failure) throw TypeError(failure);
              }
            }

            failure = parseURL(state, urlString, null, baseState);
            if (failure) throw TypeError(failure);
            var searchParams = state.searchParams = new URLSearchParams();
            var searchParamsState = getInternalSearchParamsState(searchParams);
            searchParamsState.updateSearchParams(state.query);

            searchParamsState.updateURL = function () {
              state.query = String(searchParams) || null;
            };

            if (!DESCRIPTORS) {
              that.href = serializeURL.call(that);
              that.origin = getOrigin.call(that);
              that.protocol = getProtocol.call(that);
              that.username = getUsername.call(that);
              that.password = getPassword.call(that);
              that.host = getHost.call(that);
              that.hostname = getHostname.call(that);
              that.port = getPort.call(that);
              that.pathname = getPathname.call(that);
              that.search = getSearch.call(that);
              that.searchParams = getSearchParams.call(that);
              that.hash = getHash.call(that);
            }
          };

          var URLPrototype = URLConstructor.prototype;

          var serializeURL = function serializeURL() {
            var url = getInternalURLState(this);
            var scheme = url.scheme;
            var username = url.username;
            var password = url.password;
            var host = url.host;
            var port = url.port;
            var path = url.path;
            var query = url.query;
            var fragment = url.fragment;
            var output = scheme + ':';

            if (host !== null) {
              output += '//';

              if (includesCredentials(url)) {
                output += username + (password ? ':' + password : '') + '@';
              }

              output += serializeHost(host);
              if (port !== null) output += ':' + port;
            } else if (scheme == 'file') output += '//';

            output += url.cannotBeABaseURL ? path[0] : path.length ? '/' + path.join('/') : '';
            if (query !== null) output += '?' + query;
            if (fragment !== null) output += '#' + fragment;
            return output;
          };

          var getOrigin = function getOrigin() {
            var url = getInternalURLState(this);
            var scheme = url.scheme;
            var port = url.port;
            if (scheme == 'blob') try {
              return new URL(scheme.path[0]).origin;
            } catch (error) {
              return 'null';
            }
            if (scheme == 'file' || !isSpecial(url)) return 'null';
            return scheme + '://' + serializeHost(url.host) + (port !== null ? ':' + port : '');
          };

          var getProtocol = function getProtocol() {
            return getInternalURLState(this).scheme + ':';
          };

          var getUsername = function getUsername() {
            return getInternalURLState(this).username;
          };

          var getPassword = function getPassword() {
            return getInternalURLState(this).password;
          };

          var getHost = function getHost() {
            var url = getInternalURLState(this);
            var host = url.host;
            var port = url.port;
            return host === null ? '' : port === null ? serializeHost(host) : serializeHost(host) + ':' + port;
          };

          var getHostname = function getHostname() {
            var host = getInternalURLState(this).host;
            return host === null ? '' : serializeHost(host);
          };

          var getPort = function getPort() {
            var port = getInternalURLState(this).port;
            return port === null ? '' : String(port);
          };

          var getPathname = function getPathname() {
            var url = getInternalURLState(this);
            var path = url.path;
            return url.cannotBeABaseURL ? path[0] : path.length ? '/' + path.join('/') : '';
          };

          var getSearch = function getSearch() {
            var query = getInternalURLState(this).query;
            return query ? '?' + query : '';
          };

          var getSearchParams = function getSearchParams() {
            return getInternalURLState(this).searchParams;
          };

          var getHash = function getHash() {
            var fragment = getInternalURLState(this).fragment;
            return fragment ? '#' + fragment : '';
          };

          var accessorDescriptor = function accessorDescriptor(getter, setter) {
            return {
              get: getter,
              set: setter,
              configurable: true,
              enumerable: true
            };
          };

          if (DESCRIPTORS) {
            defineProperties(URLPrototype, {
              // `URL.prototype.href` accessors pair
              // https://url.spec.whatwg.org/#dom-url-href
              href: accessorDescriptor(serializeURL, function (href) {
                var url = getInternalURLState(this);
                var urlString = String(href);
                var failure = parseURL(url, urlString);
                if (failure) throw TypeError(failure);
                getInternalSearchParamsState(url.searchParams).updateSearchParams(url.query);
              }),
              // `URL.prototype.origin` getter
              // https://url.spec.whatwg.org/#dom-url-origin
              origin: accessorDescriptor(getOrigin),
              // `URL.prototype.protocol` accessors pair
              // https://url.spec.whatwg.org/#dom-url-protocol
              protocol: accessorDescriptor(getProtocol, function (protocol) {
                var url = getInternalURLState(this);
                parseURL(url, String(protocol) + ':', SCHEME_START);
              }),
              // `URL.prototype.username` accessors pair
              // https://url.spec.whatwg.org/#dom-url-username
              username: accessorDescriptor(getUsername, function (username) {
                var url = getInternalURLState(this);
                var codePoints = arrayFrom(String(username));
                if (cannotHaveUsernamePasswordPort(url)) return;
                url.username = '';

                for (var i = 0; i < codePoints.length; i++) {
                  url.username += percentEncode(codePoints[i], userinfoPercentEncodeSet);
                }
              }),
              // `URL.prototype.password` accessors pair
              // https://url.spec.whatwg.org/#dom-url-password
              password: accessorDescriptor(getPassword, function (password) {
                var url = getInternalURLState(this);
                var codePoints = arrayFrom(String(password));
                if (cannotHaveUsernamePasswordPort(url)) return;
                url.password = '';

                for (var i = 0; i < codePoints.length; i++) {
                  url.password += percentEncode(codePoints[i], userinfoPercentEncodeSet);
                }
              }),
              // `URL.prototype.host` accessors pair
              // https://url.spec.whatwg.org/#dom-url-host
              host: accessorDescriptor(getHost, function (host) {
                var url = getInternalURLState(this);
                if (url.cannotBeABaseURL) return;
                parseURL(url, String(host), HOST);
              }),
              // `URL.prototype.hostname` accessors pair
              // https://url.spec.whatwg.org/#dom-url-hostname
              hostname: accessorDescriptor(getHostname, function (hostname) {
                var url = getInternalURLState(this);
                if (url.cannotBeABaseURL) return;
                parseURL(url, String(hostname), HOSTNAME);
              }),
              // `URL.prototype.port` accessors pair
              // https://url.spec.whatwg.org/#dom-url-port
              port: accessorDescriptor(getPort, function (port) {
                var url = getInternalURLState(this);
                if (cannotHaveUsernamePasswordPort(url)) return;
                port = String(port);
                if (port == '') url.port = null;else parseURL(url, port, PORT);
              }),
              // `URL.prototype.pathname` accessors pair
              // https://url.spec.whatwg.org/#dom-url-pathname
              pathname: accessorDescriptor(getPathname, function (pathname) {
                var url = getInternalURLState(this);
                if (url.cannotBeABaseURL) return;
                url.path = [];
                parseURL(url, pathname + '', PATH_START);
              }),
              // `URL.prototype.search` accessors pair
              // https://url.spec.whatwg.org/#dom-url-search
              search: accessorDescriptor(getSearch, function (search) {
                var url = getInternalURLState(this);
                search = String(search);

                if (search == '') {
                  url.query = null;
                } else {
                  if ('?' == search.charAt(0)) search = search.slice(1);
                  url.query = '';
                  parseURL(url, search, QUERY);
                }

                getInternalSearchParamsState(url.searchParams).updateSearchParams(url.query);
              }),
              // `URL.prototype.searchParams` getter
              // https://url.spec.whatwg.org/#dom-url-searchparams
              searchParams: accessorDescriptor(getSearchParams),
              // `URL.prototype.hash` accessors pair
              // https://url.spec.whatwg.org/#dom-url-hash
              hash: accessorDescriptor(getHash, function (hash) {
                var url = getInternalURLState(this);
                hash = String(hash);

                if (hash == '') {
                  url.fragment = null;
                  return;
                }

                if ('#' == hash.charAt(0)) hash = hash.slice(1);
                url.fragment = '';
                parseURL(url, hash, FRAGMENT);
              })
            });
          } // `URL.prototype.toJSON` method
          // https://url.spec.whatwg.org/#dom-url-tojson


          redefine(URLPrototype, 'toJSON', function toJSON() {
            return serializeURL.call(this);
          }, {
            enumerable: true
          }); // `URL.prototype.toString` method
          // https://url.spec.whatwg.org/#URL-stringification-behavior

          redefine(URLPrototype, 'toString', function toString() {
            return serializeURL.call(this);
          }, {
            enumerable: true
          });

          if (NativeURL) {
            var nativeCreateObjectURL = NativeURL.createObjectURL;
            var nativeRevokeObjectURL = NativeURL.revokeObjectURL; // `URL.createObjectURL` method
            // https://developer.mozilla.org/en-US/docs/Web/API/URL/createObjectURL
            // eslint-disable-next-line no-unused-vars -- required for `.length`

            if (nativeCreateObjectURL) redefine(URLConstructor, 'createObjectURL', function createObjectURL(blob) {
              return nativeCreateObjectURL.apply(NativeURL, arguments);
            }); // `URL.revokeObjectURL` method
            // https://developer.mozilla.org/en-US/docs/Web/API/URL/revokeObjectURL
            // eslint-disable-next-line no-unused-vars -- required for `.length`

            if (nativeRevokeObjectURL) redefine(URLConstructor, 'revokeObjectURL', function revokeObjectURL(url) {
              return nativeRevokeObjectURL.apply(NativeURL, arguments);
            });
          }

          setToStringTag(URLConstructor, 'URL');
          $({
            global: true,
            forced: !USE_NATIVE_URL,
            sham: !DESCRIPTORS
          }, {
            URL: URLConstructor
          });
          /***/
        }
        /******/

      };
      /************************************************************************/

      /******/
      // The module cache

      /******/

      var __webpack_module_cache__ = {};
      /******/

      /******/
      // The require function

      /******/

      function __webpack_require__(moduleId) {
        /******/
        // Check if module is in cache

        /******/
        if (__webpack_module_cache__[moduleId]) {
          /******/
          return __webpack_module_cache__[moduleId].exports;
          /******/
        }
        /******/
        // Create a new module (and put it into the cache)

        /******/


        var module = __webpack_module_cache__[moduleId] = {
          /******/
          // no module.id needed

          /******/
          // no module.loaded needed

          /******/
          exports: {}
          /******/

        };
        /******/

        /******/
        // Execute the module function

        /******/

        __webpack_modules__[moduleId](module, module.exports, __webpack_require__);
        /******/

        /******/
        // Return the exports of the module

        /******/


        return module.exports;
        /******/
      }
      /******/

      /************************************************************************/

      /******/

      /* webpack/runtime/define property getters */

      /******/


      !function () {
        /******/
        // define getter functions for harmony exports

        /******/
        __webpack_require__.d = function (exports, definition) {
          /******/
          for (var key in definition) {
            /******/
            if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
              /******/
              Object.defineProperty(exports, key, {
                enumerable: true,
                get: definition[key]
              });
              /******/
            }
            /******/

          }
          /******/

        };
        /******/

      }();
      /******/

      /******/

      /* webpack/runtime/global */

      /******/

      !function () {
        /******/
        __webpack_require__.g = function () {
          /******/
          if ((typeof globalThis === "undefined" ? "undefined" : _typeof2(globalThis)) === 'object') return globalThis;
          /******/

          try {
            /******/
            return this || new Function('return this')();
            /******/
          } catch (e) {
            /******/
            if ((typeof window === "undefined" ? "undefined" : _typeof2(window)) === 'object') return window;
            /******/
          }
          /******/

        }();
        /******/

      }();
      /******/

      /******/

      /* webpack/runtime/hasOwnProperty shorthand */

      /******/

      !function () {
        /******/
        __webpack_require__.o = function (obj, prop) {
          return Object.prototype.hasOwnProperty.call(obj, prop);
        };
        /******/

      }();
      /******/

      /******/

      /* webpack/runtime/make namespace object */

      /******/

      !function () {
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

      }();
      /******/

      /************************************************************************/

      var __webpack_exports__ = {}; // This entry need to be wrapped in an IIFE because it need to be in strict mode.

      !function () {
        "use strict"; // ESM COMPAT FLAG

        __webpack_require__.r(__webpack_exports__); // EXPORTS


        __webpack_require__.d(__webpack_exports__, {
          "Dropzone": function Dropzone() {
            return (
              /* reexport */
              _Dropzone
            );
          },
          "default": function _default() {
            return (
              /* binding */
              dropzone_dist
            );
          }
        }); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.concat.js


        var es_array_concat = __webpack_require__(2222); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.filter.js


        var es_array_filter = __webpack_require__(7327); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.index-of.js


        var es_array_index_of = __webpack_require__(2772); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.iterator.js


        var es_array_iterator = __webpack_require__(6992); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.map.js


        var es_array_map = __webpack_require__(1249); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.slice.js


        var es_array_slice = __webpack_require__(7042); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array.splice.js


        var es_array_splice = __webpack_require__(561); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.array-buffer.constructor.js


        var es_array_buffer_constructor = __webpack_require__(8264); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.function.name.js


        var es_function_name = __webpack_require__(8309); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.object.get-prototype-of.js


        var es_object_get_prototype_of = __webpack_require__(489); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.object.to-string.js


        var es_object_to_string = __webpack_require__(1539); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.regexp.exec.js


        var es_regexp_exec = __webpack_require__(4916); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.regexp.to-string.js


        var es_regexp_to_string = __webpack_require__(9714); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.string.iterator.js


        var es_string_iterator = __webpack_require__(8783); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.string.match.js


        var es_string_match = __webpack_require__(4723); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.string.replace.js


        var es_string_replace = __webpack_require__(5306); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.string.split.js


        var es_string_split = __webpack_require__(3123); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.string.trim.js


        var es_string_trim = __webpack_require__(3210); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.uint8-array.js


        var es_typed_array_uint8_array = __webpack_require__(2472); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.copy-within.js


        var es_typed_array_copy_within = __webpack_require__(2990); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.every.js


        var es_typed_array_every = __webpack_require__(8927); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.fill.js


        var es_typed_array_fill = __webpack_require__(3105); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.filter.js


        var es_typed_array_filter = __webpack_require__(5035); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.find.js


        var es_typed_array_find = __webpack_require__(4345); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.find-index.js


        var es_typed_array_find_index = __webpack_require__(7174); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.for-each.js


        var es_typed_array_for_each = __webpack_require__(2846); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.includes.js


        var es_typed_array_includes = __webpack_require__(4731); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.index-of.js


        var es_typed_array_index_of = __webpack_require__(7209); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.iterator.js


        var es_typed_array_iterator = __webpack_require__(6319); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.join.js


        var es_typed_array_join = __webpack_require__(8867); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.last-index-of.js


        var es_typed_array_last_index_of = __webpack_require__(7789); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.map.js


        var es_typed_array_map = __webpack_require__(3739); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.reduce.js


        var es_typed_array_reduce = __webpack_require__(9368); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.reduce-right.js


        var es_typed_array_reduce_right = __webpack_require__(4483); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.reverse.js


        var es_typed_array_reverse = __webpack_require__(2056); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.set.js


        var es_typed_array_set = __webpack_require__(3462); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.slice.js


        var es_typed_array_slice = __webpack_require__(678); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.some.js


        var es_typed_array_some = __webpack_require__(7462); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.sort.js


        var es_typed_array_sort = __webpack_require__(3824); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.subarray.js


        var es_typed_array_subarray = __webpack_require__(5021); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.to-locale-string.js


        var es_typed_array_to_locale_string = __webpack_require__(2974); // EXTERNAL MODULE: ./node_modules/core-js/modules/es.typed-array.to-string.js


        var es_typed_array_to_string = __webpack_require__(5016); // EXTERNAL MODULE: ./node_modules/core-js/modules/web.dom-collections.for-each.js


        var web_dom_collections_for_each = __webpack_require__(4747); // EXTERNAL MODULE: ./node_modules/core-js/modules/web.dom-collections.iterator.js


        var web_dom_collections_iterator = __webpack_require__(3948); // EXTERNAL MODULE: ./node_modules/core-js/modules/web.url.js


        var web_url = __webpack_require__(285);

        ; // CONCATENATED MODULE: ./src/emitter.js

        function _createForOfIteratorHelper(o, allowArrayLike) {
          var it;

          if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
            if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
              if (it) o = it;
              var i = 0;

              var F = function F() {};

              return {
                s: F,
                n: function n() {
                  if (i >= o.length) return {
                    done: true
                  };
                  return {
                    done: false,
                    value: o[i++]
                  };
                },
                e: function e(_e) {
                  throw _e;
                },
                f: F
              };
            }

            throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
          }

          var normalCompletion = true,
              didErr = false,
              err;
          return {
            s: function s() {
              it = o[Symbol.iterator]();
            },
            n: function n() {
              var step = it.next();
              normalCompletion = step.done;
              return step;
            },
            e: function e(_e2) {
              didErr = true;
              err = _e2;
            },
            f: function f() {
              try {
                if (!normalCompletion && it["return"] != null) it["return"]();
              } finally {
                if (didErr) throw err;
              }
            }
          };
        }

        function _unsupportedIterableToArray(o, minLen) {
          if (!o) return;
          if (typeof o === "string") return _arrayLikeToArray(o, minLen);
          var n = Object.prototype.toString.call(o).slice(8, -1);
          if (n === "Object" && o.constructor) n = o.constructor.name;
          if (n === "Map" || n === "Set") return Array.from(o);
          if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }

        function _arrayLikeToArray(arr, len) {
          if (len == null || len > arr.length) len = arr.length;

          for (var i = 0, arr2 = new Array(len); i < len; i++) {
            arr2[i] = arr[i];
          }

          return arr2;
        }

        function _classCallCheck(instance, Constructor) {
          if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
          }
        }

        function _defineProperties(target, props) {
          for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
          }
        }

        function _createClass(Constructor, protoProps, staticProps) {
          if (protoProps) _defineProperties(Constructor.prototype, protoProps);
          if (staticProps) _defineProperties(Constructor, staticProps);
          return Constructor;
        } // The Emitter class provides the ability to call `.on()` on Dropzone to listen
        // to events.
        // It is strongly based on component's emitter class, and I removed the
        // functionality because of the dependency hell with different frameworks.


        var Emitter = /*#__PURE__*/function () {
          function Emitter() {
            _classCallCheck(this, Emitter);
          }

          _createClass(Emitter, [{
            key: "on",
            value: // Add an event listener for given event
            function on(event, fn) {
              this._callbacks = this._callbacks || {}; // Create namespace for this event

              if (!this._callbacks[event]) {
                this._callbacks[event] = [];
              }

              this._callbacks[event].push(fn);

              return this;
            }
          }, {
            key: "emit",
            value: function emit(event) {
              this._callbacks = this._callbacks || {};
              var callbacks = this._callbacks[event];

              for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
                args[_key - 1] = arguments[_key];
              }

              if (callbacks) {
                var _iterator = _createForOfIteratorHelper(callbacks, true),
                    _step;

                try {
                  for (_iterator.s(); !(_step = _iterator.n()).done;) {
                    var callback = _step.value;
                    callback.apply(this, args);
                  }
                } catch (err) {
                  _iterator.e(err);
                } finally {
                  _iterator.f();
                }
              } // trigger a corresponding DOM event


              if (this.element) {
                this.element.dispatchEvent(this.makeEvent("dropzone:" + event, {
                  args: args
                }));
              }

              return this;
            }
          }, {
            key: "makeEvent",
            value: function makeEvent(eventName, detail) {
              var params = {
                bubbles: true,
                cancelable: true,
                detail: detail
              };

              if (typeof window.CustomEvent === "function") {
                return new CustomEvent(eventName, params);
              } else {
                // IE 11 support
                // https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
                var evt = document.createEvent("CustomEvent");
                evt.initCustomEvent(eventName, params.bubbles, params.cancelable, params.detail);
                return evt;
              }
            } // Remove event listener for given event. If fn is not provided, all event
            // listeners for that event will be removed. If neither is provided, all
            // event listeners will be removed.

          }, {
            key: "off",
            value: function off(event, fn) {
              if (!this._callbacks || arguments.length === 0) {
                this._callbacks = {};
                return this;
              } // specific event


              var callbacks = this._callbacks[event];

              if (!callbacks) {
                return this;
              } // remove all handlers


              if (arguments.length === 1) {
                delete this._callbacks[event];
                return this;
              } // remove specific handler


              for (var i = 0; i < callbacks.length; i++) {
                var callback = callbacks[i];

                if (callback === fn) {
                  callbacks.splice(i, 1);
                  break;
                }
              }

              return this;
            }
          }]);

          return Emitter;
        }();

        ; // CONCATENATED MODULE: ./src/preview-template.html
        // Module

        var code = "<div class=\"dz-preview dz-file-preview\"> <div class=\"dz-image\"><img data-dz-thumbnail/></div> <div class=\"dz-details\"> <div class=\"dz-size\"><span data-dz-size></span></div> <div class=\"dz-filename\"><span data-dz-name></span></div> </div> <div class=\"dz-progress\"> <span class=\"dz-upload\" data-dz-uploadprogress></span> </div> <div class=\"dz-error-message\"><span data-dz-errormessage></span></div> <div class=\"dz-success-mark\"> <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> <title>Check</title> <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\"> <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" stroke-opacity=\"0.198794158\" stroke=\"#747474\" fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path> </g> </svg> </div> <div class=\"dz-error-mark\"> <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> <title>Error</title> <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\"> <g stroke=\"#747474\" stroke-opacity=\"0.198794158\" fill=\"#FFFFFF\" fill-opacity=\"0.816519475\"> <path d=\"M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"></path> </g> </g> </svg> </div> </div> "; // Exports

        /* harmony default export */

        var preview_template = code;
        ; // CONCATENATED MODULE: ./src/options.js

        function options_createForOfIteratorHelper(o, allowArrayLike) {
          var it;

          if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
            if (Array.isArray(o) || (it = options_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
              if (it) o = it;
              var i = 0;

              var F = function F() {};

              return {
                s: F,
                n: function n() {
                  if (i >= o.length) return {
                    done: true
                  };
                  return {
                    done: false,
                    value: o[i++]
                  };
                },
                e: function e(_e) {
                  throw _e;
                },
                f: F
              };
            }

            throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
          }

          var normalCompletion = true,
              didErr = false,
              err;
          return {
            s: function s() {
              it = o[Symbol.iterator]();
            },
            n: function n() {
              var step = it.next();
              normalCompletion = step.done;
              return step;
            },
            e: function e(_e2) {
              didErr = true;
              err = _e2;
            },
            f: function f() {
              try {
                if (!normalCompletion && it["return"] != null) it["return"]();
              } finally {
                if (didErr) throw err;
              }
            }
          };
        }

        function options_unsupportedIterableToArray(o, minLen) {
          if (!o) return;
          if (typeof o === "string") return options_arrayLikeToArray(o, minLen);
          var n = Object.prototype.toString.call(o).slice(8, -1);
          if (n === "Object" && o.constructor) n = o.constructor.name;
          if (n === "Map" || n === "Set") return Array.from(o);
          if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return options_arrayLikeToArray(o, minLen);
        }

        function options_arrayLikeToArray(arr, len) {
          if (len == null || len > arr.length) len = arr.length;

          for (var i = 0, arr2 = new Array(len); i < len; i++) {
            arr2[i] = arr[i];
          }

          return arr2;
        }

        var defaultOptions = {
          /**
           * Has to be specified on elements other than form (or when the form
           * doesn't have an `action` attribute). You can also
           * provide a function that will be called with `files` and
           * must return the url (since `v3.12.0`)
           */
          url: null,

          /**
           * Can be changed to `"put"` if necessary. You can also provide a function
           * that will be called with `files` and must return the method (since `v3.12.0`).
           */
          method: "post",

          /**
           * Will be set on the XHRequest.
           */
          withCredentials: false,

          /**
           * The timeout for the XHR requests in milliseconds (since `v4.4.0`).
           * If set to null or 0, no timeout is going to be set.
           */
          timeout: null,

          /**
           * How many file uploads to process in parallel (See the
           * Enqueuing file uploads documentation section for more info)
           */
          parallelUploads: 2,

          /**
           * Whether to send multiple files in one request. If
           * this it set to true, then the fallback file input element will
           * have the `multiple` attribute as well. This option will
           * also trigger additional events (like `processingmultiple`). See the events
           * documentation section for more information.
           */
          uploadMultiple: false,

          /**
           * Whether you want files to be uploaded in chunks to your server. This can't be
           * used in combination with `uploadMultiple`.
           *
           * See [chunksUploaded](#config-chunksUploaded) for the callback to finalise an upload.
           */
          chunking: false,

          /**
           * If `chunking` is enabled, this defines whether **every** file should be chunked,
           * even if the file size is below chunkSize. This means, that the additional chunk
           * form data will be submitted and the `chunksUploaded` callback will be invoked.
           */
          forceChunking: false,

          /**
           * If `chunking` is `true`, then this defines the chunk size in bytes.
           */
          chunkSize: 2000000,

          /**
           * If `true`, the individual chunks of a file are being uploaded simultaneously.
           */
          parallelChunkUploads: false,

          /**
           * Whether a chunk should be retried if it fails.
           */
          retryChunks: false,

          /**
           * If `retryChunks` is true, how many times should it be retried.
           */
          retryChunksLimit: 3,

          /**
           * The maximum filesize (in bytes) that is allowed to be uploaded.
           */
          maxFilesize: 256,

          /**
           * The name of the file param that gets transferred.
           * **NOTE**: If you have the option  `uploadMultiple` set to `true`, then
           * Dropzone will append `[]` to the name.
           */
          paramName: "file",

          /**
           * Whether thumbnails for images should be generated
           */
          createImageThumbnails: true,

          /**
           * In MB. When the filename exceeds this limit, the thumbnail will not be generated.
           */
          maxThumbnailFilesize: 10,

          /**
           * If `null`, the ratio of the image will be used to calculate it.
           */
          thumbnailWidth: 120,

          /**
           * The same as `thumbnailWidth`. If both are null, images will not be resized.
           */
          thumbnailHeight: 120,

          /**
           * How the images should be scaled down in case both, `thumbnailWidth` and `thumbnailHeight` are provided.
           * Can be either `contain` or `crop`.
           */
          thumbnailMethod: "crop",

          /**
           * If set, images will be resized to these dimensions before being **uploaded**.
           * If only one, `resizeWidth` **or** `resizeHeight` is provided, the original aspect
           * ratio of the file will be preserved.
           *
           * The `options.transformFile` function uses these options, so if the `transformFile` function
           * is overridden, these options don't do anything.
           */
          resizeWidth: null,

          /**
           * See `resizeWidth`.
           */
          resizeHeight: null,

          /**
           * The mime type of the resized image (before it gets uploaded to the server).
           * If `null` the original mime type will be used. To force jpeg, for example, use `image/jpeg`.
           * See `resizeWidth` for more information.
           */
          resizeMimeType: null,

          /**
           * The quality of the resized images. See `resizeWidth`.
           */
          resizeQuality: 0.8,

          /**
           * How the images should be scaled down in case both, `resizeWidth` and `resizeHeight` are provided.
           * Can be either `contain` or `crop`.
           */
          resizeMethod: "contain",

          /**
           * The base that is used to calculate the **displayed** filesize. You can
           * change this to 1024 if you would rather display kibibytes, mebibytes,
           * etc... 1024 is technically incorrect, because `1024 bytes` are `1 kibibyte`
           * not `1 kilobyte`. You can change this to `1024` if you don't care about
           * validity.
           */
          filesizeBase: 1000,

          /**
           * If not `null` defines how many files this Dropzone handles. If it exceeds,
           * the event `maxfilesexceeded` will be called. The dropzone element gets the
           * class `dz-max-files-reached` accordingly so you can provide visual
           * feedback.
           */
          maxFiles: null,

          /**
           * An optional object to send additional headers to the server. Eg:
           * `{ "My-Awesome-Header": "header value" }`
           */
          headers: null,

          /**
           * If `true`, the dropzone element itself will be clickable, if `false`
           * nothing will be clickable.
           *
           * You can also pass an HTML element, a CSS selector (for multiple elements)
           * or an array of those. In that case, all of those elements will trigger an
           * upload when clicked.
           */
          clickable: true,

          /**
           * Whether hidden files in directories should be ignored.
           */
          ignoreHiddenFiles: true,

          /**
           * The default implementation of `accept` checks the file's mime type or
           * extension against this list. This is a comma separated list of mime
           * types or file extensions.
           *
           * Eg.: `image/*,application/pdf,.psd`
           *
           * If the Dropzone is `clickable` this option will also be used as
           * [`accept`](https://developer.mozilla.org/en-US/docs/HTML/Element/input#attr-accept)
           * parameter on the hidden file input as well.
           */
          acceptedFiles: null,

          /**
           * **Deprecated!**
           * Use acceptedFiles instead.
           */
          acceptedMimeTypes: null,

          /**
           * If false, files will be added to the queue but the queue will not be
           * processed automatically.
           * This can be useful if you need some additional user input before sending
           * files (or if you want want all files sent at once).
           * If you're ready to send the file simply call `myDropzone.processQueue()`.
           *
           * See the [enqueuing file uploads](#enqueuing-file-uploads) documentation
           * section for more information.
           */
          autoProcessQueue: true,

          /**
           * If false, files added to the dropzone will not be queued by default.
           * You'll have to call `enqueueFile(file)` manually.
           */
          autoQueue: true,

          /**
           * If `true`, this will add a link to every file preview to remove or cancel (if
           * already uploading) the file. The `dictCancelUpload`, `dictCancelUploadConfirmation`
           * and `dictRemoveFile` options are used for the wording.
           */
          addRemoveLinks: false,

          /**
           * Defines where to display the file previews – if `null` the
           * Dropzone element itself is used. Can be a plain `HTMLElement` or a CSS
           * selector. The element should have the `dropzone-previews` class so
           * the previews are displayed properly.
           */
          previewsContainer: null,

          /**
           * Set this to `true` if you don't want previews to be shown.
           */
          disablePreviews: false,

          /**
           * This is the element the hidden input field (which is used when clicking on the
           * dropzone to trigger file selection) will be appended to. This might
           * be important in case you use frameworks to switch the content of your page.
           *
           * Can be a selector string, or an element directly.
           */
          hiddenInputContainer: "body",

          /**
           * If null, no capture type will be specified
           * If camera, mobile devices will skip the file selection and choose camera
           * If microphone, mobile devices will skip the file selection and choose the microphone
           * If camcorder, mobile devices will skip the file selection and choose the camera in video mode
           * On apple devices multiple must be set to false.  AcceptedFiles may need to
           * be set to an appropriate mime type (e.g. "image/*", "audio/*", or "video/*").
           */
          capture: null,

          /**
           * **Deprecated**. Use `renameFile` instead.
           */
          renameFilename: null,

          /**
           * A function that is invoked before the file is uploaded to the server and renames the file.
           * This function gets the `File` as argument and can use the `file.name`. The actual name of the
           * file that gets used during the upload can be accessed through `file.upload.filename`.
           */
          renameFile: null,

          /**
           * If `true` the fallback will be forced. This is very useful to test your server
           * implementations first and make sure that everything works as
           * expected without dropzone if you experience problems, and to test
           * how your fallbacks will look.
           */
          forceFallback: false,

          /**
           * The text used before any files are dropped.
           */
          dictDefaultMessage: "Drop files here to upload",

          /**
           * The text that replaces the default message text it the browser is not supported.
           */
          dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",

          /**
           * The text that will be added before the fallback form.
           * If you provide a  fallback element yourself, or if this option is `null` this will
           * be ignored.
           */
          dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",

          /**
           * If the filesize is too big.
           * `{{filesize}}` and `{{maxFilesize}}` will be replaced with the respective configuration values.
           */
          dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",

          /**
           * If the file doesn't match the file type.
           */
          dictInvalidFileType: "You can't upload files of this type.",

          /**
           * If the server response was invalid.
           * `{{statusCode}}` will be replaced with the servers status code.
           */
          dictResponseError: "Server responded with {{statusCode}} code.",

          /**
           * If `addRemoveLinks` is true, the text to be used for the cancel upload link.
           */
          dictCancelUpload: "Cancel upload",

          /**
           * The text that is displayed if an upload was manually canceled
           */
          dictUploadCanceled: "Upload canceled.",

          /**
           * If `addRemoveLinks` is true, the text to be used for confirmation when cancelling upload.
           */
          dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",

          /**
           * If `addRemoveLinks` is true, the text to be used to remove a file.
           */
          dictRemoveFile: "Remove file",

          /**
           * If this is not null, then the user will be prompted before removing a file.
           */
          dictRemoveFileConfirmation: null,

          /**
           * Displayed if `maxFiles` is st and exceeded.
           * The string `{{maxFiles}}` will be replaced by the configuration value.
           */
          dictMaxFilesExceeded: "You can not upload any more files.",

          /**
           * Allows you to translate the different units. Starting with `tb` for terabytes and going down to
           * `b` for bytes.
           */
          dictFileSizeUnits: {
            tb: "TB",
            gb: "GB",
            mb: "MB",
            kb: "KB",
            b: "b"
          },

          /**
           * Called when dropzone initialized
           * You can add event listeners here
           */
          init: function init() {},

          /**
           * Can be an **object** of additional parameters to transfer to the server, **or** a `Function`
           * that gets invoked with the `files`, `xhr` and, if it's a chunked upload, `chunk` arguments. In case
           * of a function, this needs to return a map.
           *
           * The default implementation does nothing for normal uploads, but adds relevant information for
           * chunked uploads.
           *
           * This is the same as adding hidden input fields in the form element.
           */
          params: function params(files, xhr, chunk) {
            if (chunk) {
              return {
                dzuuid: chunk.file.upload.uuid,
                dzchunkindex: chunk.index,
                dztotalfilesize: chunk.file.size,
                dzchunksize: this.options.chunkSize,
                dztotalchunkcount: chunk.file.upload.totalChunkCount,
                dzchunkbyteoffset: chunk.index * this.options.chunkSize
              };
            }
          },

          /**
           * A function that gets a [file](https://developer.mozilla.org/en-US/docs/DOM/File)
           * and a `done` function as parameters.
           *
           * If the done function is invoked without arguments, the file is "accepted" and will
           * be processed. If you pass an error message, the file is rejected, and the error
           * message will be displayed.
           * This function will not be called if the file is too big or doesn't match the mime types.
           */
          accept: function accept(file, done) {
            return done();
          },

          /**
           * The callback that will be invoked when all chunks have been uploaded for a file.
           * It gets the file for which the chunks have been uploaded as the first parameter,
           * and the `done` function as second. `done()` needs to be invoked when everything
           * needed to finish the upload process is done.
           */
          chunksUploaded: function chunksUploaded(file, done) {
            done();
          },

          /**
           * Gets called when the browser is not supported.
           * The default implementation shows the fallback input field and adds
           * a text.
           */
          fallback: function fallback() {
            // This code should pass in IE7... :(
            var messageElement;
            this.element.className = "".concat(this.element.className, " dz-browser-not-supported");

            var _iterator = options_createForOfIteratorHelper(this.element.getElementsByTagName("div"), true),
                _step;

            try {
              for (_iterator.s(); !(_step = _iterator.n()).done;) {
                var child = _step.value;

                if (/(^| )dz-message($| )/.test(child.className)) {
                  messageElement = child;
                  child.className = "dz-message"; // Removes the 'dz-default' class

                  break;
                }
              }
            } catch (err) {
              _iterator.e(err);
            } finally {
              _iterator.f();
            }

            if (!messageElement) {
              messageElement = _Dropzone.createElement('<div class="dz-message"><span></span></div>');
              this.element.appendChild(messageElement);
            }

            var span = messageElement.getElementsByTagName("span")[0];

            if (span) {
              if (span.textContent != null) {
                span.textContent = this.options.dictFallbackMessage;
              } else if (span.innerText != null) {
                span.innerText = this.options.dictFallbackMessage;
              }
            }

            return this.element.appendChild(this.getFallbackForm());
          },

          /**
           * Gets called to calculate the thumbnail dimensions.
           *
           * It gets `file`, `width` and `height` (both may be `null`) as parameters and must return an object containing:
           *
           *  - `srcWidth` & `srcHeight` (required)
           *  - `trgWidth` & `trgHeight` (required)
           *  - `srcX` & `srcY` (optional, default `0`)
           *  - `trgX` & `trgY` (optional, default `0`)
           *
           * Those values are going to be used by `ctx.drawImage()`.
           */
          resize: function resize(file, width, height, resizeMethod) {
            var info = {
              srcX: 0,
              srcY: 0,
              srcWidth: file.width,
              srcHeight: file.height
            };
            var srcRatio = file.width / file.height; // Automatically calculate dimensions if not specified

            if (width == null && height == null) {
              width = info.srcWidth;
              height = info.srcHeight;
            } else if (width == null) {
              width = height * srcRatio;
            } else if (height == null) {
              height = width / srcRatio;
            } // Make sure images aren't upscaled


            width = Math.min(width, info.srcWidth);
            height = Math.min(height, info.srcHeight);
            var trgRatio = width / height;

            if (info.srcWidth > width || info.srcHeight > height) {
              // Image is bigger and needs rescaling
              if (resizeMethod === "crop") {
                if (srcRatio > trgRatio) {
                  info.srcHeight = file.height;
                  info.srcWidth = info.srcHeight * trgRatio;
                } else {
                  info.srcWidth = file.width;
                  info.srcHeight = info.srcWidth / trgRatio;
                }
              } else if (resizeMethod === "contain") {
                // Method 'contain'
                if (srcRatio > trgRatio) {
                  height = width / srcRatio;
                } else {
                  width = height * srcRatio;
                }
              } else {
                throw new Error("Unknown resizeMethod '".concat(resizeMethod, "'"));
              }
            }

            info.srcX = (file.width - info.srcWidth) / 2;
            info.srcY = (file.height - info.srcHeight) / 2;
            info.trgWidth = width;
            info.trgHeight = height;
            return info;
          },

          /**
           * Can be used to transform the file (for example, resize an image if necessary).
           *
           * The default implementation uses `resizeWidth` and `resizeHeight` (if provided) and resizes
           * images according to those dimensions.
           *
           * Gets the `file` as the first parameter, and a `done()` function as the second, that needs
           * to be invoked with the file when the transformation is done.
           */
          transformFile: function transformFile(file, done) {
            if ((this.options.resizeWidth || this.options.resizeHeight) && file.type.match(/image.*/)) {
              return this.resizeImage(file, this.options.resizeWidth, this.options.resizeHeight, this.options.resizeMethod, done);
            } else {
              return done(file);
            }
          },

          /**
           * A string that contains the template used for each dropped
           * file. Change it to fulfill your needs but make sure to properly
           * provide all elements.
           *
           * If you want to use an actual HTML element instead of providing a String
           * as a config option, you could create a div with the id `tpl`,
           * put the template inside it and provide the element like this:
           *
           *     document
           *       .querySelector('#tpl')
           *       .innerHTML
           *
           */
          previewTemplate: preview_template,

          /*
           Those functions register themselves to the events on init and handle all
           the user interface specific stuff. Overwriting them won't break the upload
           but can break the way it's displayed.
           You can overwrite them if you don't like the default behavior. If you just
           want to add an additional event handler, register it on the dropzone object
           and don't overwrite those options.
           */
          // Those are self explanatory and simply concern the DragnDrop.
          drop: function drop(e) {
            return this.element.classList.remove("dz-drag-hover");
          },
          dragstart: function dragstart(e) {},
          dragend: function dragend(e) {
            return this.element.classList.remove("dz-drag-hover");
          },
          dragenter: function dragenter(e) {
            return this.element.classList.add("dz-drag-hover");
          },
          dragover: function dragover(e) {
            return this.element.classList.add("dz-drag-hover");
          },
          dragleave: function dragleave(e) {
            return this.element.classList.remove("dz-drag-hover");
          },
          paste: function paste(e) {},
          // Called whenever there are no files left in the dropzone anymore, and the
          // dropzone should be displayed as if in the initial state.
          reset: function reset() {
            return this.element.classList.remove("dz-started");
          },
          // Called when a file is added to the queue
          // Receives `file`
          addedfile: function addedfile(file) {
            var _this = this;

            if (this.element === this.previewsContainer) {
              this.element.classList.add("dz-started");
            }

            if (this.previewsContainer && !this.options.disablePreviews) {
              file.previewElement = _Dropzone.createElement(this.options.previewTemplate.trim());
              file.previewTemplate = file.previewElement; // Backwards compatibility

              this.previewsContainer.appendChild(file.previewElement);

              var _iterator2 = options_createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-name]"), true),
                  _step2;

              try {
                for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
                  var node = _step2.value;
                  node.textContent = file.name;
                }
              } catch (err) {
                _iterator2.e(err);
              } finally {
                _iterator2.f();
              }

              var _iterator3 = options_createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-size]"), true),
                  _step3;

              try {
                for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
                  node = _step3.value;
                  node.innerHTML = this.filesize(file.size);
                }
              } catch (err) {
                _iterator3.e(err);
              } finally {
                _iterator3.f();
              }

              if (this.options.addRemoveLinks) {
                file._removeLink = _Dropzone.createElement("<a class=\"dz-remove\" href=\"javascript:undefined;\" data-dz-remove>".concat(this.options.dictRemoveFile, "</a>"));
                file.previewElement.appendChild(file._removeLink);
              }

              var removeFileEvent = function removeFileEvent(e) {
                e.preventDefault();
                e.stopPropagation();

                if (file.status === _Dropzone.UPLOADING) {
                  return _Dropzone.confirm(_this.options.dictCancelUploadConfirmation, function () {
                    return _this.removeFile(file);
                  });
                } else {
                  if (_this.options.dictRemoveFileConfirmation) {
                    return _Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function () {
                      return _this.removeFile(file);
                    });
                  } else {
                    return _this.removeFile(file);
                  }
                }
              };

              var _iterator4 = options_createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-remove]"), true),
                  _step4;

              try {
                for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
                  var removeLink = _step4.value;
                  removeLink.addEventListener("click", removeFileEvent);
                }
              } catch (err) {
                _iterator4.e(err);
              } finally {
                _iterator4.f();
              }
            }
          },
          // Called whenever a file is removed.
          removedfile: function removedfile(file) {
            if (file.previewElement != null && file.previewElement.parentNode != null) {
              file.previewElement.parentNode.removeChild(file.previewElement);
            }

            return this._updateMaxFilesReachedClass();
          },
          // Called when a thumbnail has been generated
          // Receives `file` and `dataUrl`
          thumbnail: function thumbnail(file, dataUrl) {
            if (file.previewElement) {
              file.previewElement.classList.remove("dz-file-preview");

              var _iterator5 = options_createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-thumbnail]"), true),
                  _step5;

              try {
                for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
                  var thumbnailElement = _step5.value;
                  thumbnailElement.alt = file.name;
                  thumbnailElement.src = dataUrl;
                }
              } catch (err) {
                _iterator5.e(err);
              } finally {
                _iterator5.f();
              }

              return setTimeout(function () {
                return file.previewElement.classList.add("dz-image-preview");
              }, 1);
            }
          },
          // Called whenever an error occurs
          // Receives `file` and `message`
          error: function error(file, message) {
            if (file.previewElement) {
              file.previewElement.classList.add("dz-error");

              if (typeof message !== "string" && message.error) {
                message = message.error;
              }

              var _iterator6 = options_createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-errormessage]"), true),
                  _step6;

              try {
                for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
                  var node = _step6.value;
                  node.textContent = message;
                }
              } catch (err) {
                _iterator6.e(err);
              } finally {
                _iterator6.f();
              }
            }
          },
          errormultiple: function errormultiple() {},
          // Called when a file gets processed. Since there is a cue, not all added
          // files are processed immediately.
          // Receives `file`
          processing: function processing(file) {
            if (file.previewElement) {
              file.previewElement.classList.add("dz-processing");

              if (file._removeLink) {
                return file._removeLink.innerHTML = this.options.dictCancelUpload;
              }
            }
          },
          processingmultiple: function processingmultiple() {},
          // Called whenever the upload progress gets updated.
          // Receives `file`, `progress` (percentage 0-100) and `bytesSent`.
          // To get the total number of bytes of the file, use `file.size`
          uploadprogress: function uploadprogress(file, progress, bytesSent) {
            if (file.previewElement) {
              var _iterator7 = options_createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-uploadprogress]"), true),
                  _step7;

              try {
                for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
                  var node = _step7.value;
                  node.nodeName === "PROGRESS" ? node.value = progress : node.style.width = "".concat(progress, "%");
                }
              } catch (err) {
                _iterator7.e(err);
              } finally {
                _iterator7.f();
              }
            }
          },
          // Called whenever the total upload progress gets updated.
          // Called with totalUploadProgress (0-100), totalBytes and totalBytesSent
          totaluploadprogress: function totaluploadprogress() {},
          // Called just before the file is sent. Gets the `xhr` object as second
          // parameter, so you can modify it (for example to add a CSRF token) and a
          // `formData` object to add additional information.
          sending: function sending() {},
          sendingmultiple: function sendingmultiple() {},
          // When the complete upload is finished and successful
          // Receives `file`
          success: function success(file) {
            if (file.previewElement) {
              return file.previewElement.classList.add("dz-success");
            }
          },
          successmultiple: function successmultiple() {},
          // When the upload is canceled.
          canceled: function canceled(file) {
            return this.emit("error", file, this.options.dictUploadCanceled);
          },
          canceledmultiple: function canceledmultiple() {},
          // When the upload is finished, either with success or an error.
          // Receives `file`
          complete: function complete(file) {
            if (file._removeLink) {
              file._removeLink.innerHTML = this.options.dictRemoveFile;
            }

            if (file.previewElement) {
              return file.previewElement.classList.add("dz-complete");
            }
          },
          completemultiple: function completemultiple() {},
          maxfilesexceeded: function maxfilesexceeded() {},
          maxfilesreached: function maxfilesreached() {},
          queuecomplete: function queuecomplete() {},
          addedfiles: function addedfiles() {}
        };
        /* harmony default export */

        var src_options = defaultOptions;
        ; // CONCATENATED MODULE: ./src/dropzone.js

        function _typeof(obj) {
          "@babel/helpers - typeof";

          if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
            _typeof = function _typeof(obj) {
              return typeof obj;
            };
          } else {
            _typeof = function _typeof(obj) {
              return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
            };
          }

          return _typeof(obj);
        }

        function dropzone_createForOfIteratorHelper(o, allowArrayLike) {
          var it;

          if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
            if (Array.isArray(o) || (it = dropzone_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
              if (it) o = it;
              var i = 0;

              var F = function F() {};

              return {
                s: F,
                n: function n() {
                  if (i >= o.length) return {
                    done: true
                  };
                  return {
                    done: false,
                    value: o[i++]
                  };
                },
                e: function e(_e) {
                  throw _e;
                },
                f: F
              };
            }

            throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
          }

          var normalCompletion = true,
              didErr = false,
              err;
          return {
            s: function s() {
              it = o[Symbol.iterator]();
            },
            n: function n() {
              var step = it.next();
              normalCompletion = step.done;
              return step;
            },
            e: function e(_e2) {
              didErr = true;
              err = _e2;
            },
            f: function f() {
              try {
                if (!normalCompletion && it["return"] != null) it["return"]();
              } finally {
                if (didErr) throw err;
              }
            }
          };
        }

        function dropzone_unsupportedIterableToArray(o, minLen) {
          if (!o) return;
          if (typeof o === "string") return dropzone_arrayLikeToArray(o, minLen);
          var n = Object.prototype.toString.call(o).slice(8, -1);
          if (n === "Object" && o.constructor) n = o.constructor.name;
          if (n === "Map" || n === "Set") return Array.from(o);
          if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return dropzone_arrayLikeToArray(o, minLen);
        }

        function dropzone_arrayLikeToArray(arr, len) {
          if (len == null || len > arr.length) len = arr.length;

          for (var i = 0, arr2 = new Array(len); i < len; i++) {
            arr2[i] = arr[i];
          }

          return arr2;
        }

        function dropzone_classCallCheck(instance, Constructor) {
          if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
          }
        }

        function dropzone_defineProperties(target, props) {
          for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
          }
        }

        function dropzone_createClass(Constructor, protoProps, staticProps) {
          if (protoProps) dropzone_defineProperties(Constructor.prototype, protoProps);
          if (staticProps) dropzone_defineProperties(Constructor, staticProps);
          return Constructor;
        }

        function _inherits(subClass, superClass) {
          if (typeof superClass !== "function" && superClass !== null) {
            throw new TypeError("Super expression must either be null or a function");
          }

          subClass.prototype = Object.create(superClass && superClass.prototype, {
            constructor: {
              value: subClass,
              writable: true,
              configurable: true
            }
          });
          if (superClass) _setPrototypeOf(subClass, superClass);
        }

        function _setPrototypeOf(o, p) {
          _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
            o.__proto__ = p;
            return o;
          };

          return _setPrototypeOf(o, p);
        }

        function _createSuper(Derived) {
          var hasNativeReflectConstruct = _isNativeReflectConstruct();

          return function _createSuperInternal() {
            var Super = _getPrototypeOf(Derived),
                result;

            if (hasNativeReflectConstruct) {
              var NewTarget = _getPrototypeOf(this).constructor;

              result = Reflect.construct(Super, arguments, NewTarget);
            } else {
              result = Super.apply(this, arguments);
            }

            return _possibleConstructorReturn(this, result);
          };
        }

        function _possibleConstructorReturn(self, call) {
          if (call && (_typeof(call) === "object" || typeof call === "function")) {
            return call;
          }

          return _assertThisInitialized(self);
        }

        function _assertThisInitialized(self) {
          if (self === void 0) {
            throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
          }

          return self;
        }

        function _isNativeReflectConstruct() {
          if (typeof Reflect === "undefined" || !Reflect.construct) return false;
          if (Reflect.construct.sham) return false;
          if (typeof Proxy === "function") return true;

          try {
            Date.prototype.toString.call(Reflect.construct(Date, [], function () {}));
            return true;
          } catch (e) {
            return false;
          }
        }

        function _getPrototypeOf(o) {
          _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
            return o.__proto__ || Object.getPrototypeOf(o);
          };
          return _getPrototypeOf(o);
        }

        var _Dropzone = /*#__PURE__*/function (_Emitter) {
          _inherits(Dropzone, _Emitter);

          var _super = _createSuper(Dropzone);

          function Dropzone(el, options) {
            var _this;

            dropzone_classCallCheck(this, Dropzone);
            _this = _super.call(this);
            var fallback, left;
            _this.element = el; // For backwards compatibility since the version was in the prototype previously

            _this.version = Dropzone.version;
            _this.clickableElements = [];
            _this.listeners = [];
            _this.files = []; // All files

            if (typeof _this.element === "string") {
              _this.element = document.querySelector(_this.element);
            } // Not checking if instance of HTMLElement or Element since IE9 is extremely weird.


            if (!_this.element || _this.element.nodeType == null) {
              throw new Error("Invalid dropzone element.");
            }

            if (_this.element.dropzone) {
              throw new Error("Dropzone already attached.");
            } // Now add this dropzone to the instances.


            Dropzone.instances.push(_assertThisInitialized(_this)); // Put the dropzone inside the element itself.

            _this.element.dropzone = _assertThisInitialized(_this);
            var elementOptions = (left = Dropzone.optionsForElement(_this.element)) != null ? left : {};
            _this.options = Dropzone.extend({}, src_options, elementOptions, options != null ? options : {});
            _this.options.previewTemplate = _this.options.previewTemplate.replace(/\n*/g, ""); // If the browser failed, just call the fallback and leave

            if (_this.options.forceFallback || !Dropzone.isBrowserSupported()) {
              return _possibleConstructorReturn(_this, _this.options.fallback.call(_assertThisInitialized(_this)));
            } // @options.url = @element.getAttribute "action" unless @options.url?


            if (_this.options.url == null) {
              _this.options.url = _this.element.getAttribute("action");
            }

            if (!_this.options.url) {
              throw new Error("No URL provided.");
            }

            if (_this.options.acceptedFiles && _this.options.acceptedMimeTypes) {
              throw new Error("You can't provide both 'acceptedFiles' and 'acceptedMimeTypes'. 'acceptedMimeTypes' is deprecated.");
            }

            if (_this.options.uploadMultiple && _this.options.chunking) {
              throw new Error("You cannot set both: uploadMultiple and chunking.");
            } // Backwards compatibility


            if (_this.options.acceptedMimeTypes) {
              _this.options.acceptedFiles = _this.options.acceptedMimeTypes;
              delete _this.options.acceptedMimeTypes;
            } // Backwards compatibility


            if (_this.options.renameFilename != null) {
              _this.options.renameFile = function (file) {
                return _this.options.renameFilename.call(_assertThisInitialized(_this), file.name, file);
              };
            }

            if (typeof _this.options.method === "string") {
              _this.options.method = _this.options.method.toUpperCase();
            }

            if ((fallback = _this.getExistingFallback()) && fallback.parentNode) {
              // Remove the fallback
              fallback.parentNode.removeChild(fallback);
            } // Display previews in the previewsContainer element or the Dropzone element unless explicitly set to false


            if (_this.options.previewsContainer !== false) {
              if (_this.options.previewsContainer) {
                _this.previewsContainer = Dropzone.getElement(_this.options.previewsContainer, "previewsContainer");
              } else {
                _this.previewsContainer = _this.element;
              }
            }

            if (_this.options.clickable) {
              if (_this.options.clickable === true) {
                _this.clickableElements = [_this.element];
              } else {
                _this.clickableElements = Dropzone.getElements(_this.options.clickable, "clickable");
              }
            }

            _this.init();

            return _this;
          } // Returns all files that have been accepted


          dropzone_createClass(Dropzone, [{
            key: "getAcceptedFiles",
            value: function getAcceptedFiles() {
              return this.files.filter(function (file) {
                return file.accepted;
              }).map(function (file) {
                return file;
              });
            } // Returns all files that have been rejected
            // Not sure when that's going to be useful, but added for completeness.

          }, {
            key: "getRejectedFiles",
            value: function getRejectedFiles() {
              return this.files.filter(function (file) {
                return !file.accepted;
              }).map(function (file) {
                return file;
              });
            }
          }, {
            key: "getFilesWithStatus",
            value: function getFilesWithStatus(status) {
              return this.files.filter(function (file) {
                return file.status === status;
              }).map(function (file) {
                return file;
              });
            } // Returns all files that are in the queue

          }, {
            key: "getQueuedFiles",
            value: function getQueuedFiles() {
              return this.getFilesWithStatus(Dropzone.QUEUED);
            }
          }, {
            key: "getUploadingFiles",
            value: function getUploadingFiles() {
              return this.getFilesWithStatus(Dropzone.UPLOADING);
            }
          }, {
            key: "getAddedFiles",
            value: function getAddedFiles() {
              return this.getFilesWithStatus(Dropzone.ADDED);
            } // Files that are either queued or uploading

          }, {
            key: "getActiveFiles",
            value: function getActiveFiles() {
              return this.files.filter(function (file) {
                return file.status === Dropzone.UPLOADING || file.status === Dropzone.QUEUED;
              }).map(function (file) {
                return file;
              });
            } // The function that gets called when Dropzone is initialized. You
            // can (and should) setup event listeners inside this function.

          }, {
            key: "init",
            value: function init() {
              var _this2 = this; // In case it isn't set already


              if (this.element.tagName === "form") {
                this.element.setAttribute("enctype", "multipart/form-data");
              }

              if (this.element.classList.contains("dropzone") && !this.element.querySelector(".dz-message")) {
                this.element.appendChild(Dropzone.createElement("<div class=\"dz-default dz-message\"><button class=\"dz-button\" type=\"button\">".concat(this.options.dictDefaultMessage, "</button></div>")));
              }

              if (this.clickableElements.length) {
                var setupHiddenFileInput = function setupHiddenFileInput() {
                  if (_this2.hiddenFileInput) {
                    _this2.hiddenFileInput.parentNode.removeChild(_this2.hiddenFileInput);
                  }

                  _this2.hiddenFileInput = document.createElement("input");

                  _this2.hiddenFileInput.setAttribute("type", "file");

                  if (_this2.options.maxFiles === null || _this2.options.maxFiles > 1) {
                    _this2.hiddenFileInput.setAttribute("multiple", "multiple");
                  }

                  _this2.hiddenFileInput.className = "dz-hidden-input";

                  if (_this2.options.acceptedFiles !== null) {
                    _this2.hiddenFileInput.setAttribute("accept", _this2.options.acceptedFiles);
                  }

                  if (_this2.options.capture !== null) {
                    _this2.hiddenFileInput.setAttribute("capture", _this2.options.capture);
                  } // Making sure that no one can "tab" into this field.


                  _this2.hiddenFileInput.setAttribute("tabindex", "-1"); // Not setting `display="none"` because some browsers don't accept clicks
                  // on elements that aren't displayed.


                  _this2.hiddenFileInput.style.visibility = "hidden";
                  _this2.hiddenFileInput.style.position = "absolute";
                  _this2.hiddenFileInput.style.top = "0";
                  _this2.hiddenFileInput.style.left = "0";
                  _this2.hiddenFileInput.style.height = "0";
                  _this2.hiddenFileInput.style.width = "0";
                  Dropzone.getElement(_this2.options.hiddenInputContainer, "hiddenInputContainer").appendChild(_this2.hiddenFileInput);

                  _this2.hiddenFileInput.addEventListener("change", function () {
                    var files = _this2.hiddenFileInput.files;

                    if (files.length) {
                      var _iterator = dropzone_createForOfIteratorHelper(files, true),
                          _step;

                      try {
                        for (_iterator.s(); !(_step = _iterator.n()).done;) {
                          var file = _step.value;

                          _this2.addFile(file);
                        }
                      } catch (err) {
                        _iterator.e(err);
                      } finally {
                        _iterator.f();
                      }
                    }

                    _this2.emit("addedfiles", files);

                    setupHiddenFileInput();
                  });
                };

                setupHiddenFileInput();
              }

              this.URL = window.URL !== null ? window.URL : window.webkitURL; // Setup all event listeners on the Dropzone object itself.
              // They're not in @setupEventListeners() because they shouldn't be removed
              // again when the dropzone gets disabled.

              var _iterator2 = dropzone_createForOfIteratorHelper(this.events, true),
                  _step2;

              try {
                for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
                  var eventName = _step2.value;
                  this.on(eventName, this.options[eventName]);
                }
              } catch (err) {
                _iterator2.e(err);
              } finally {
                _iterator2.f();
              }

              this.on("uploadprogress", function () {
                return _this2.updateTotalUploadProgress();
              });
              this.on("removedfile", function () {
                return _this2.updateTotalUploadProgress();
              });
              this.on("canceled", function (file) {
                return _this2.emit("complete", file);
              }); // Emit a `queuecomplete` event if all files finished uploading.

              this.on("complete", function (file) {
                if (_this2.getAddedFiles().length === 0 && _this2.getUploadingFiles().length === 0 && _this2.getQueuedFiles().length === 0) {
                  // This needs to be deferred so that `queuecomplete` really triggers after `complete`
                  return setTimeout(function () {
                    return _this2.emit("queuecomplete");
                  }, 0);
                }
              });

              var containsFiles = function containsFiles(e) {
                if (e.dataTransfer.types) {
                  // Because e.dataTransfer.types is an Object in
                  // IE, we need to iterate like this instead of
                  // using e.dataTransfer.types.some()
                  for (var i = 0; i < e.dataTransfer.types.length; i++) {
                    if (e.dataTransfer.types[i] === "Files") return true;
                  }
                }

                return false;
              };

              var noPropagation = function noPropagation(e) {
                // If there are no files, we don't want to stop
                // propagation so we don't interfere with other
                // drag and drop behaviour.
                if (!containsFiles(e)) return;
                e.stopPropagation();

                if (e.preventDefault) {
                  return e.preventDefault();
                } else {
                  return e.returnValue = false;
                }
              }; // Create the listeners


              this.listeners = [{
                element: this.element,
                events: {
                  dragstart: function dragstart(e) {
                    return _this2.emit("dragstart", e);
                  },
                  dragenter: function dragenter(e) {
                    noPropagation(e);
                    return _this2.emit("dragenter", e);
                  },
                  dragover: function dragover(e) {
                    // Makes it possible to drag files from chrome's download bar
                    // http://stackoverflow.com/questions/19526430/drag-and-drop-file-uploads-from-chrome-downloads-bar
                    // Try is required to prevent bug in Internet Explorer 11 (SCRIPT65535 exception)
                    var efct;

                    try {
                      efct = e.dataTransfer.effectAllowed;
                    } catch (error) {}

                    e.dataTransfer.dropEffect = "move" === efct || "linkMove" === efct ? "move" : "copy";
                    noPropagation(e);
                    return _this2.emit("dragover", e);
                  },
                  dragleave: function dragleave(e) {
                    return _this2.emit("dragleave", e);
                  },
                  drop: function drop(e) {
                    noPropagation(e);
                    return _this2.drop(e);
                  },
                  dragend: function dragend(e) {
                    return _this2.emit("dragend", e);
                  }
                } // This is disabled right now, because the browsers don't implement it properly.
                // "paste": (e) =>
                //   noPropagation e
                //   @paste e

              }];
              this.clickableElements.forEach(function (clickableElement) {
                return _this2.listeners.push({
                  element: clickableElement,
                  events: {
                    click: function click(evt) {
                      // Only the actual dropzone or the message element should trigger file selection
                      if (clickableElement !== _this2.element || evt.target === _this2.element || Dropzone.elementInside(evt.target, _this2.element.querySelector(".dz-message"))) {
                        _this2.hiddenFileInput.click(); // Forward the click

                      }

                      return true;
                    }
                  }
                });
              });
              this.enable();
              return this.options.init.call(this);
            } // Not fully tested yet

          }, {
            key: "destroy",
            value: function destroy() {
              this.disable();
              this.removeAllFiles(true);

              if (this.hiddenFileInput != null ? this.hiddenFileInput.parentNode : undefined) {
                this.hiddenFileInput.parentNode.removeChild(this.hiddenFileInput);
                this.hiddenFileInput = null;
              }

              delete this.element.dropzone;
              return Dropzone.instances.splice(Dropzone.instances.indexOf(this), 1);
            }
          }, {
            key: "updateTotalUploadProgress",
            value: function updateTotalUploadProgress() {
              var totalUploadProgress;
              var totalBytesSent = 0;
              var totalBytes = 0;
              var activeFiles = this.getActiveFiles();

              if (activeFiles.length) {
                var _iterator3 = dropzone_createForOfIteratorHelper(this.getActiveFiles(), true),
                    _step3;

                try {
                  for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
                    var file = _step3.value;
                    totalBytesSent += file.upload.bytesSent;
                    totalBytes += file.upload.total;
                  }
                } catch (err) {
                  _iterator3.e(err);
                } finally {
                  _iterator3.f();
                }

                totalUploadProgress = 100 * totalBytesSent / totalBytes;
              } else {
                totalUploadProgress = 100;
              }

              return this.emit("totaluploadprogress", totalUploadProgress, totalBytes, totalBytesSent);
            } // @options.paramName can be a function taking one parameter rather than a string.
            // A parameter name for a file is obtained simply by calling this with an index number.

          }, {
            key: "_getParamName",
            value: function _getParamName(n) {
              if (typeof this.options.paramName === "function") {
                return this.options.paramName(n);
              } else {
                return "".concat(this.options.paramName).concat(this.options.uploadMultiple ? "[".concat(n, "]") : "");
              }
            } // If @options.renameFile is a function,
            // the function will be used to rename the file.name before appending it to the formData

          }, {
            key: "_renameFile",
            value: function _renameFile(file) {
              if (typeof this.options.renameFile !== "function") {
                return file.name;
              }

              return this.options.renameFile(file);
            } // Returns a form that can be used as fallback if the browser does not support DragnDrop
            //
            // If the dropzone is already a form, only the input field and button are returned. Otherwise a complete form element is provided.
            // This code has to pass in IE7 :(

          }, {
            key: "getFallbackForm",
            value: function getFallbackForm() {
              var existingFallback, form;

              if (existingFallback = this.getExistingFallback()) {
                return existingFallback;
              }

              var fieldsString = '<div class="dz-fallback">';

              if (this.options.dictFallbackText) {
                fieldsString += "<p>".concat(this.options.dictFallbackText, "</p>");
              }

              fieldsString += "<input type=\"file\" name=\"".concat(this._getParamName(0), "\" ").concat(this.options.uploadMultiple ? 'multiple="multiple"' : undefined, " /><input type=\"submit\" value=\"Upload!\"></div>");
              var fields = Dropzone.createElement(fieldsString);

              if (this.element.tagName !== "FORM") {
                form = Dropzone.createElement("<form action=\"".concat(this.options.url, "\" enctype=\"multipart/form-data\" method=\"").concat(this.options.method, "\"></form>"));
                form.appendChild(fields);
              } else {
                // Make sure that the enctype and method attributes are set properly
                this.element.setAttribute("enctype", "multipart/form-data");
                this.element.setAttribute("method", this.options.method);
              }

              return form != null ? form : fields;
            } // Returns the fallback elements if they exist already
            //
            // This code has to pass in IE7 :(

          }, {
            key: "getExistingFallback",
            value: function getExistingFallback() {
              var getFallback = function getFallback(elements) {
                var _iterator4 = dropzone_createForOfIteratorHelper(elements, true),
                    _step4;

                try {
                  for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
                    var el = _step4.value;

                    if (/(^| )fallback($| )/.test(el.className)) {
                      return el;
                    }
                  }
                } catch (err) {
                  _iterator4.e(err);
                } finally {
                  _iterator4.f();
                }
              };

              for (var _i = 0, _arr = ["div", "form"]; _i < _arr.length; _i++) {
                var tagName = _arr[_i];
                var fallback;

                if (fallback = getFallback(this.element.getElementsByTagName(tagName))) {
                  return fallback;
                }
              }
            } // Activates all listeners stored in @listeners

          }, {
            key: "setupEventListeners",
            value: function setupEventListeners() {
              return this.listeners.map(function (elementListeners) {
                return function () {
                  var result = [];

                  for (var event in elementListeners.events) {
                    var listener = elementListeners.events[event];
                    result.push(elementListeners.element.addEventListener(event, listener, false));
                  }

                  return result;
                }();
              });
            } // Deactivates all listeners stored in @listeners

          }, {
            key: "removeEventListeners",
            value: function removeEventListeners() {
              return this.listeners.map(function (elementListeners) {
                return function () {
                  var result = [];

                  for (var event in elementListeners.events) {
                    var listener = elementListeners.events[event];
                    result.push(elementListeners.element.removeEventListener(event, listener, false));
                  }

                  return result;
                }();
              });
            } // Removes all event listeners and cancels all files in the queue or being processed.

          }, {
            key: "disable",
            value: function disable() {
              var _this3 = this;

              this.clickableElements.forEach(function (element) {
                return element.classList.remove("dz-clickable");
              });
              this.removeEventListeners();
              this.disabled = true;
              return this.files.map(function (file) {
                return _this3.cancelUpload(file);
              });
            }
          }, {
            key: "enable",
            value: function enable() {
              delete this.disabled;
              this.clickableElements.forEach(function (element) {
                return element.classList.add("dz-clickable");
              });
              return this.setupEventListeners();
            } // Returns a nicely formatted filesize

          }, {
            key: "filesize",
            value: function filesize(size) {
              var selectedSize = 0;
              var selectedUnit = "b";

              if (size > 0) {
                var units = ["tb", "gb", "mb", "kb", "b"];

                for (var i = 0; i < units.length; i++) {
                  var unit = units[i];
                  var cutoff = Math.pow(this.options.filesizeBase, 4 - i) / 10;

                  if (size >= cutoff) {
                    selectedSize = size / Math.pow(this.options.filesizeBase, 4 - i);
                    selectedUnit = unit;
                    break;
                  }
                }

                selectedSize = Math.round(10 * selectedSize) / 10; // Cutting of digits
              }

              return "<strong>".concat(selectedSize, "</strong> ").concat(this.options.dictFileSizeUnits[selectedUnit]);
            } // Adds or removes the `dz-max-files-reached` class from the form.

          }, {
            key: "_updateMaxFilesReachedClass",
            value: function _updateMaxFilesReachedClass() {
              if (this.options.maxFiles != null && this.getAcceptedFiles().length >= this.options.maxFiles) {
                if (this.getAcceptedFiles().length === this.options.maxFiles) {
                  this.emit("maxfilesreached", this.files);
                }

                return this.element.classList.add("dz-max-files-reached");
              } else {
                return this.element.classList.remove("dz-max-files-reached");
              }
            }
          }, {
            key: "drop",
            value: function drop(e) {
              if (!e.dataTransfer) {
                return;
              }

              this.emit("drop", e); // Convert the FileList to an Array
              // This is necessary for IE11

              var files = [];

              for (var i = 0; i < e.dataTransfer.files.length; i++) {
                files[i] = e.dataTransfer.files[i];
              } // Even if it's a folder, files.length will contain the folders.


              if (files.length) {
                var items = e.dataTransfer.items;

                if (items && items.length && items[0].webkitGetAsEntry != null) {
                  // The browser supports dropping of folders, so handle items instead of files
                  this._addFilesFromItems(items);
                } else {
                  this.handleFiles(files);
                }
              }

              this.emit("addedfiles", files);
            }
          }, {
            key: "paste",
            value: function paste(e) {
              if (__guard__(e != null ? e.clipboardData : undefined, function (x) {
                return x.items;
              }) == null) {
                return;
              }

              this.emit("paste", e);
              var items = e.clipboardData.items;

              if (items.length) {
                return this._addFilesFromItems(items);
              }
            }
          }, {
            key: "handleFiles",
            value: function handleFiles(files) {
              var _iterator5 = dropzone_createForOfIteratorHelper(files, true),
                  _step5;

              try {
                for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
                  var file = _step5.value;
                  this.addFile(file);
                }
              } catch (err) {
                _iterator5.e(err);
              } finally {
                _iterator5.f();
              }
            } // When a folder is dropped (or files are pasted), items must be handled
            // instead of files.

          }, {
            key: "_addFilesFromItems",
            value: function _addFilesFromItems(items) {
              var _this4 = this;

              return function () {
                var result = [];

                var _iterator6 = dropzone_createForOfIteratorHelper(items, true),
                    _step6;

                try {
                  for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
                    var item = _step6.value;
                    var entry;

                    if (item.webkitGetAsEntry != null && (entry = item.webkitGetAsEntry())) {
                      if (entry.isFile) {
                        result.push(_this4.addFile(item.getAsFile()));
                      } else if (entry.isDirectory) {
                        // Append all files from that directory to files
                        result.push(_this4._addFilesFromDirectory(entry, entry.name));
                      } else {
                        result.push(undefined);
                      }
                    } else if (item.getAsFile != null) {
                      if (item.kind == null || item.kind === "file") {
                        result.push(_this4.addFile(item.getAsFile()));
                      } else {
                        result.push(undefined);
                      }
                    } else {
                      result.push(undefined);
                    }
                  }
                } catch (err) {
                  _iterator6.e(err);
                } finally {
                  _iterator6.f();
                }

                return result;
              }();
            } // Goes through the directory, and adds each file it finds recursively

          }, {
            key: "_addFilesFromDirectory",
            value: function _addFilesFromDirectory(directory, path) {
              var _this5 = this;

              var dirReader = directory.createReader();

              var errorHandler = function errorHandler(error) {
                return __guardMethod__(console, "log", function (o) {
                  return o.log(error);
                });
              };

              var readEntries = function readEntries() {
                return dirReader.readEntries(function (entries) {
                  if (entries.length > 0) {
                    var _iterator7 = dropzone_createForOfIteratorHelper(entries, true),
                        _step7;

                    try {
                      for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
                        var entry = _step7.value;

                        if (entry.isFile) {
                          entry.file(function (file) {
                            if (_this5.options.ignoreHiddenFiles && file.name.substring(0, 1) === ".") {
                              return;
                            }

                            file.fullPath = "".concat(path, "/").concat(file.name);
                            return _this5.addFile(file);
                          });
                        } else if (entry.isDirectory) {
                          _this5._addFilesFromDirectory(entry, "".concat(path, "/").concat(entry.name));
                        }
                      } // Recursively call readEntries() again, since browser only handle
                      // the first 100 entries.
                      // See: https://developer.mozilla.org/en-US/docs/Web/API/DirectoryReader#readEntries

                    } catch (err) {
                      _iterator7.e(err);
                    } finally {
                      _iterator7.f();
                    }

                    readEntries();
                  }

                  return null;
                }, errorHandler);
              };

              return readEntries();
            } // If `done()` is called without argument the file is accepted
            // If you call it with an error message, the file is rejected
            // (This allows for asynchronous validation)
            //
            // This function checks the filesize, and if the file.type passes the
            // `acceptedFiles` check.

          }, {
            key: "accept",
            value: function accept(file, done) {
              if (this.options.maxFilesize && file.size > this.options.maxFilesize * 1024 * 1024) {
                done(this.options.dictFileTooBig.replace("{{filesize}}", Math.round(file.size / 1024 / 10.24) / 100).replace("{{maxFilesize}}", this.options.maxFilesize));
              } else if (!Dropzone.isValidFile(file, this.options.acceptedFiles)) {
                done(this.options.dictInvalidFileType);
              } else if (this.options.maxFiles != null && this.getAcceptedFiles().length >= this.options.maxFiles) {
                done(this.options.dictMaxFilesExceeded.replace("{{maxFiles}}", this.options.maxFiles));
                this.emit("maxfilesexceeded", file);
              } else {
                this.options.accept.call(this, file, done);
              }
            }
          }, {
            key: "addFile",
            value: function addFile(file) {
              var _this6 = this;

              file.upload = {
                uuid: Dropzone.uuidv4(),
                progress: 0,
                // Setting the total upload size to file.size for the beginning
                // It's actual different than the size to be transmitted.
                total: file.size,
                bytesSent: 0,
                filename: this._renameFile(file) // Not setting chunking information here, because the acutal data — and
                // thus the chunks — might change if `options.transformFile` is set
                // and does something to the data.

              };
              this.files.push(file);
              file.status = Dropzone.ADDED;
              this.emit("addedfile", file);

              this._enqueueThumbnail(file);

              this.accept(file, function (error) {
                if (error) {
                  file.accepted = false;

                  _this6._errorProcessing([file], error); // Will set the file.status

                } else {
                  file.accepted = true;

                  if (_this6.options.autoQueue) {
                    _this6.enqueueFile(file);
                  } // Will set .accepted = true

                }

                _this6._updateMaxFilesReachedClass();
              });
            } // Wrapper for enqueueFile

          }, {
            key: "enqueueFiles",
            value: function enqueueFiles(files) {
              var _iterator8 = dropzone_createForOfIteratorHelper(files, true),
                  _step8;

              try {
                for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
                  var file = _step8.value;
                  this.enqueueFile(file);
                }
              } catch (err) {
                _iterator8.e(err);
              } finally {
                _iterator8.f();
              }

              return null;
            }
          }, {
            key: "enqueueFile",
            value: function enqueueFile(file) {
              var _this7 = this;

              if (file.status === Dropzone.ADDED && file.accepted === true) {
                file.status = Dropzone.QUEUED;

                if (this.options.autoProcessQueue) {
                  return setTimeout(function () {
                    return _this7.processQueue();
                  }, 0); // Deferring the call
                }
              } else {
                throw new Error("This file can't be queued because it has already been processed or was rejected.");
              }
            }
          }, {
            key: "_enqueueThumbnail",
            value: function _enqueueThumbnail(file) {
              var _this8 = this;

              if (this.options.createImageThumbnails && file.type.match(/image.*/) && file.size <= this.options.maxThumbnailFilesize * 1024 * 1024) {
                this._thumbnailQueue.push(file);

                return setTimeout(function () {
                  return _this8._processThumbnailQueue();
                }, 0); // Deferring the call
              }
            }
          }, {
            key: "_processThumbnailQueue",
            value: function _processThumbnailQueue() {
              var _this9 = this;

              if (this._processingThumbnail || this._thumbnailQueue.length === 0) {
                return;
              }

              this._processingThumbnail = true;

              var file = this._thumbnailQueue.shift();

              return this.createThumbnail(file, this.options.thumbnailWidth, this.options.thumbnailHeight, this.options.thumbnailMethod, true, function (dataUrl) {
                _this9.emit("thumbnail", file, dataUrl);

                _this9._processingThumbnail = false;
                return _this9._processThumbnailQueue();
              });
            } // Can be called by the user to remove a file

          }, {
            key: "removeFile",
            value: function removeFile(file) {
              if (file.status === Dropzone.UPLOADING) {
                this.cancelUpload(file);
              }

              this.files = without(this.files, file);
              this.emit("removedfile", file);

              if (this.files.length === 0) {
                return this.emit("reset");
              }
            } // Removes all files that aren't currently processed from the list

          }, {
            key: "removeAllFiles",
            value: function removeAllFiles(cancelIfNecessary) {
              // Create a copy of files since removeFile() changes the @files array.
              if (cancelIfNecessary == null) {
                cancelIfNecessary = false;
              }

              var _iterator9 = dropzone_createForOfIteratorHelper(this.files.slice(), true),
                  _step9;

              try {
                for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
                  var file = _step9.value;

                  if (file.status !== Dropzone.UPLOADING || cancelIfNecessary) {
                    this.removeFile(file);
                  }
                }
              } catch (err) {
                _iterator9.e(err);
              } finally {
                _iterator9.f();
              }

              return null;
            } // Resizes an image before it gets sent to the server. This function is the default behavior of
            // `options.transformFile` if `resizeWidth` or `resizeHeight` are set. The callback is invoked with
            // the resized blob.

          }, {
            key: "resizeImage",
            value: function resizeImage(file, width, height, resizeMethod, callback) {
              var _this10 = this;

              return this.createThumbnail(file, width, height, resizeMethod, true, function (dataUrl, canvas) {
                if (canvas == null) {
                  // The image has not been resized
                  return callback(file);
                } else {
                  var resizeMimeType = _this10.options.resizeMimeType;

                  if (resizeMimeType == null) {
                    resizeMimeType = file.type;
                  }

                  var resizedDataURL = canvas.toDataURL(resizeMimeType, _this10.options.resizeQuality);

                  if (resizeMimeType === "image/jpeg" || resizeMimeType === "image/jpg") {
                    // Now add the original EXIF information
                    resizedDataURL = ExifRestore.restore(file.dataURL, resizedDataURL);
                  }

                  return callback(Dropzone.dataURItoBlob(resizedDataURL));
                }
              });
            }
          }, {
            key: "createThumbnail",
            value: function createThumbnail(file, width, height, resizeMethod, fixOrientation, callback) {
              var _this11 = this;

              var fileReader = new FileReader();

              fileReader.onload = function () {
                file.dataURL = fileReader.result; // Don't bother creating a thumbnail for SVG images since they're vector

                if (file.type === "image/svg+xml") {
                  if (callback != null) {
                    callback(fileReader.result);
                  }

                  return;
                }

                _this11.createThumbnailFromUrl(file, width, height, resizeMethod, fixOrientation, callback);
              };

              fileReader.readAsDataURL(file);
            } // `mockFile` needs to have these attributes:
            //
            //     { name: 'name', size: 12345, imageUrl: '' }
            //
            // `callback` will be invoked when the image has been downloaded and displayed.
            // `crossOrigin` will be added to the `img` tag when accessing the file.

          }, {
            key: "displayExistingFile",
            value: function displayExistingFile(mockFile, imageUrl, callback, crossOrigin) {
              var _this12 = this;

              var resizeThumbnail = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
              this.emit("addedfile", mockFile);
              this.emit("complete", mockFile);

              if (!resizeThumbnail) {
                this.emit("thumbnail", mockFile, imageUrl);
                if (callback) callback();
              } else {
                var onDone = function onDone(thumbnail) {
                  _this12.emit("thumbnail", mockFile, thumbnail);

                  if (callback) callback();
                };

                mockFile.dataURL = imageUrl;
                this.createThumbnailFromUrl(mockFile, this.options.thumbnailWidth, this.options.thumbnailHeight, this.options.resizeMethod, this.options.fixOrientation, onDone, crossOrigin);
              }
            }
          }, {
            key: "createThumbnailFromUrl",
            value: function createThumbnailFromUrl(file, width, height, resizeMethod, fixOrientation, callback, crossOrigin) {
              var _this13 = this; // Not using `new Image` here because of a bug in latest Chrome versions.
              // See https://github.com/enyo/dropzone/pull/226


              var img = document.createElement("img");

              if (crossOrigin) {
                img.crossOrigin = crossOrigin;
              } // fixOrientation is not needed anymore with browsers handling imageOrientation


              fixOrientation = getComputedStyle(document.body)["imageOrientation"] == "from-image" ? false : fixOrientation;

              img.onload = function () {
                var loadExif = function loadExif(callback) {
                  return callback(1);
                };

                if (typeof EXIF !== "undefined" && EXIF !== null && fixOrientation) {
                  loadExif = function loadExif(callback) {
                    return EXIF.getData(img, function () {
                      return callback(EXIF.getTag(this, "Orientation"));
                    });
                  };
                }

                return loadExif(function (orientation) {
                  file.width = img.width;
                  file.height = img.height;

                  var resizeInfo = _this13.options.resize.call(_this13, file, width, height, resizeMethod);

                  var canvas = document.createElement("canvas");
                  var ctx = canvas.getContext("2d");
                  canvas.width = resizeInfo.trgWidth;
                  canvas.height = resizeInfo.trgHeight;

                  if (orientation > 4) {
                    canvas.width = resizeInfo.trgHeight;
                    canvas.height = resizeInfo.trgWidth;
                  }

                  switch (orientation) {
                    case 2:
                      // horizontal flip
                      ctx.translate(canvas.width, 0);
                      ctx.scale(-1, 1);
                      break;

                    case 3:
                      // 180° rotate left
                      ctx.translate(canvas.width, canvas.height);
                      ctx.rotate(Math.PI);
                      break;

                    case 4:
                      // vertical flip
                      ctx.translate(0, canvas.height);
                      ctx.scale(1, -1);
                      break;

                    case 5:
                      // vertical flip + 90 rotate right
                      ctx.rotate(0.5 * Math.PI);
                      ctx.scale(1, -1);
                      break;

                    case 6:
                      // 90° rotate right
                      ctx.rotate(0.5 * Math.PI);
                      ctx.translate(0, -canvas.width);
                      break;

                    case 7:
                      // horizontal flip + 90 rotate right
                      ctx.rotate(0.5 * Math.PI);
                      ctx.translate(canvas.height, -canvas.width);
                      ctx.scale(-1, 1);
                      break;

                    case 8:
                      // 90° rotate left
                      ctx.rotate(-0.5 * Math.PI);
                      ctx.translate(-canvas.height, 0);
                      break;
                  } // This is a bugfix for iOS' scaling bug.


                  drawImageIOSFix(ctx, img, resizeInfo.srcX != null ? resizeInfo.srcX : 0, resizeInfo.srcY != null ? resizeInfo.srcY : 0, resizeInfo.srcWidth, resizeInfo.srcHeight, resizeInfo.trgX != null ? resizeInfo.trgX : 0, resizeInfo.trgY != null ? resizeInfo.trgY : 0, resizeInfo.trgWidth, resizeInfo.trgHeight);
                  var thumbnail = canvas.toDataURL("image/png");

                  if (callback != null) {
                    return callback(thumbnail, canvas);
                  }
                });
              };

              if (callback != null) {
                img.onerror = callback;
              }

              return img.src = file.dataURL;
            } // Goes through the queue and processes files if there aren't too many already.

          }, {
            key: "processQueue",
            value: function processQueue() {
              var parallelUploads = this.options.parallelUploads;
              var processingLength = this.getUploadingFiles().length;
              var i = processingLength; // There are already at least as many files uploading than should be

              if (processingLength >= parallelUploads) {
                return;
              }

              var queuedFiles = this.getQueuedFiles();

              if (!(queuedFiles.length > 0)) {
                return;
              }

              if (this.options.uploadMultiple) {
                // The files should be uploaded in one request
                return this.processFiles(queuedFiles.slice(0, parallelUploads - processingLength));
              } else {
                while (i < parallelUploads) {
                  if (!queuedFiles.length) {
                    return;
                  } // Nothing left to process


                  this.processFile(queuedFiles.shift());
                  i++;
                }
              }
            } // Wrapper for `processFiles`

          }, {
            key: "processFile",
            value: function processFile(file) {
              return this.processFiles([file]);
            } // Loads the file, then calls finishedLoading()

          }, {
            key: "processFiles",
            value: function processFiles(files) {
              var _iterator10 = dropzone_createForOfIteratorHelper(files, true),
                  _step10;

              try {
                for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
                  var file = _step10.value;
                  file.processing = true; // Backwards compatibility

                  file.status = Dropzone.UPLOADING;
                  this.emit("processing", file);
                }
              } catch (err) {
                _iterator10.e(err);
              } finally {
                _iterator10.f();
              }

              if (this.options.uploadMultiple) {
                this.emit("processingmultiple", files);
              }

              return this.uploadFiles(files);
            }
          }, {
            key: "_getFilesWithXhr",
            value: function _getFilesWithXhr(xhr) {
              var files;
              return files = this.files.filter(function (file) {
                return file.xhr === xhr;
              }).map(function (file) {
                return file;
              });
            } // Cancels the file upload and sets the status to CANCELED
            // **if** the file is actually being uploaded.
            // If it's still in the queue, the file is being removed from it and the status
            // set to CANCELED.

          }, {
            key: "cancelUpload",
            value: function cancelUpload(file) {
              if (file.status === Dropzone.UPLOADING) {
                var groupedFiles = this._getFilesWithXhr(file.xhr);

                var _iterator11 = dropzone_createForOfIteratorHelper(groupedFiles, true),
                    _step11;

                try {
                  for (_iterator11.s(); !(_step11 = _iterator11.n()).done;) {
                    var groupedFile = _step11.value;
                    groupedFile.status = Dropzone.CANCELED;
                  }
                } catch (err) {
                  _iterator11.e(err);
                } finally {
                  _iterator11.f();
                }

                if (typeof file.xhr !== "undefined") {
                  file.xhr.abort();
                }

                var _iterator12 = dropzone_createForOfIteratorHelper(groupedFiles, true),
                    _step12;

                try {
                  for (_iterator12.s(); !(_step12 = _iterator12.n()).done;) {
                    var _groupedFile = _step12.value;
                    this.emit("canceled", _groupedFile);
                  }
                } catch (err) {
                  _iterator12.e(err);
                } finally {
                  _iterator12.f();
                }

                if (this.options.uploadMultiple) {
                  this.emit("canceledmultiple", groupedFiles);
                }
              } else if (file.status === Dropzone.ADDED || file.status === Dropzone.QUEUED) {
                file.status = Dropzone.CANCELED;
                this.emit("canceled", file);

                if (this.options.uploadMultiple) {
                  this.emit("canceledmultiple", [file]);
                }
              }

              if (this.options.autoProcessQueue) {
                return this.processQueue();
              }
            }
          }, {
            key: "resolveOption",
            value: function resolveOption(option) {
              if (typeof option === "function") {
                for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
                  args[_key - 1] = arguments[_key];
                }

                return option.apply(this, args);
              }

              return option;
            }
          }, {
            key: "uploadFile",
            value: function uploadFile(file) {
              return this.uploadFiles([file]);
            }
          }, {
            key: "uploadFiles",
            value: function uploadFiles(files) {
              var _this14 = this;

              this._transformFiles(files, function (transformedFiles) {
                if (_this14.options.chunking) {
                  // Chunking is not allowed to be used with `uploadMultiple` so we know
                  // that there is only __one__file.
                  var transformedFile = transformedFiles[0];
                  files[0].upload.chunked = _this14.options.chunking && (_this14.options.forceChunking || transformedFile.size > _this14.options.chunkSize);
                  files[0].upload.totalChunkCount = Math.ceil(transformedFile.size / _this14.options.chunkSize);
                }

                if (files[0].upload.chunked) {
                  // This file should be sent in chunks!
                  // If the chunking option is set, we **know** that there can only be **one** file, since
                  // uploadMultiple is not allowed with this option.
                  var file = files[0];
                  var _transformedFile = transformedFiles[0];
                  var startedChunkCount = 0;
                  file.upload.chunks = [];

                  var handleNextChunk = function handleNextChunk() {
                    var chunkIndex = 0; // Find the next item in file.upload.chunks that is not defined yet.

                    while (file.upload.chunks[chunkIndex] !== undefined) {
                      chunkIndex++;
                    } // This means, that all chunks have already been started.


                    if (chunkIndex >= file.upload.totalChunkCount) return;
                    startedChunkCount++;
                    var start = chunkIndex * _this14.options.chunkSize;
                    var end = Math.min(start + _this14.options.chunkSize, _transformedFile.size);
                    var dataBlock = {
                      name: _this14._getParamName(0),
                      data: _transformedFile.webkitSlice ? _transformedFile.webkitSlice(start, end) : _transformedFile.slice(start, end),
                      filename: file.upload.filename,
                      chunkIndex: chunkIndex
                    };
                    file.upload.chunks[chunkIndex] = {
                      file: file,
                      index: chunkIndex,
                      dataBlock: dataBlock,
                      // In case we want to retry.
                      status: Dropzone.UPLOADING,
                      progress: 0,
                      retries: 0 // The number of times this block has been retried.

                    };

                    _this14._uploadData(files, [dataBlock]);
                  };

                  file.upload.finishedChunkUpload = function (chunk, response) {
                    var allFinished = true;
                    chunk.status = Dropzone.SUCCESS; // Clear the data from the chunk

                    chunk.dataBlock = null; // Leaving this reference to xhr intact here will cause memory leaks in some browsers

                    chunk.xhr = null;

                    for (var i = 0; i < file.upload.totalChunkCount; i++) {
                      if (file.upload.chunks[i] === undefined) {
                        return handleNextChunk();
                      }

                      if (file.upload.chunks[i].status !== Dropzone.SUCCESS) {
                        allFinished = false;
                      }
                    }

                    if (allFinished) {
                      _this14.options.chunksUploaded(file, function () {
                        _this14._finished(files, response, null);
                      });
                    }
                  };

                  if (_this14.options.parallelChunkUploads) {
                    for (var i = 0; i < file.upload.totalChunkCount; i++) {
                      handleNextChunk();
                    }
                  } else {
                    handleNextChunk();
                  }
                } else {
                  var dataBlocks = [];

                  for (var _i2 = 0; _i2 < files.length; _i2++) {
                    dataBlocks[_i2] = {
                      name: _this14._getParamName(_i2),
                      data: transformedFiles[_i2],
                      filename: files[_i2].upload.filename
                    };
                  }

                  _this14._uploadData(files, dataBlocks);
                }
              });
            } /// Returns the right chunk for given file and xhr

          }, {
            key: "_getChunk",
            value: function _getChunk(file, xhr) {
              for (var i = 0; i < file.upload.totalChunkCount; i++) {
                if (file.upload.chunks[i] !== undefined && file.upload.chunks[i].xhr === xhr) {
                  return file.upload.chunks[i];
                }
              }
            } // This function actually uploads the file(s) to the server.
            // If dataBlocks contains the actual data to upload (meaning, that this could either be transformed
            // files, or individual chunks for chunked upload).

          }, {
            key: "_uploadData",
            value: function _uploadData(files, dataBlocks) {
              var _this15 = this;

              var xhr = new XMLHttpRequest(); // Put the xhr object in the file objects to be able to reference it later.

              var _iterator13 = dropzone_createForOfIteratorHelper(files, true),
                  _step13;

              try {
                for (_iterator13.s(); !(_step13 = _iterator13.n()).done;) {
                  var file = _step13.value;
                  file.xhr = xhr;
                }
              } catch (err) {
                _iterator13.e(err);
              } finally {
                _iterator13.f();
              }

              if (files[0].upload.chunked) {
                // Put the xhr object in the right chunk object, so it can be associated later, and found with _getChunk
                files[0].upload.chunks[dataBlocks[0].chunkIndex].xhr = xhr;
              }

              var method = this.resolveOption(this.options.method, files);
              var url = this.resolveOption(this.options.url, files);
              xhr.open(method, url, true); // Setting the timeout after open because of IE11 issue: https://gitlab.com/meno/dropzone/issues/8

              var timeout = this.resolveOption(this.options.timeout, files);
              if (timeout) xhr.timeout = this.resolveOption(this.options.timeout, files); // Has to be after `.open()`. See https://github.com/enyo/dropzone/issues/179

              xhr.withCredentials = !!this.options.withCredentials;

              xhr.onload = function (e) {
                _this15._finishedUploading(files, xhr, e);
              };

              xhr.ontimeout = function () {
                _this15._handleUploadError(files, xhr, "Request timedout after ".concat(_this15.options.timeout / 1000, " seconds"));
              };

              xhr.onerror = function () {
                _this15._handleUploadError(files, xhr);
              }; // Some browsers do not have the .upload property


              var progressObj = xhr.upload != null ? xhr.upload : xhr;

              progressObj.onprogress = function (e) {
                return _this15._updateFilesUploadProgress(files, xhr, e);
              };

              var headers = {
                Accept: "application/json",
                "Cache-Control": "no-cache",
                "X-Requested-With": "XMLHttpRequest"
              };

              if (this.options.headers) {
                Dropzone.extend(headers, this.options.headers);
              }

              for (var headerName in headers) {
                var headerValue = headers[headerName];

                if (headerValue) {
                  xhr.setRequestHeader(headerName, headerValue);
                }
              }

              var formData = new FormData(); // Adding all @options parameters

              if (this.options.params) {
                var additionalParams = this.options.params;

                if (typeof additionalParams === "function") {
                  additionalParams = additionalParams.call(this, files, xhr, files[0].upload.chunked ? this._getChunk(files[0], xhr) : null);
                }

                for (var key in additionalParams) {
                  var value = additionalParams[key];

                  if (Array.isArray(value)) {
                    // The additional parameter contains an array,
                    // so lets iterate over it to attach each value
                    // individually.
                    for (var i = 0; i < value.length; i++) {
                      formData.append(key, value[i]);
                    }
                  } else {
                    formData.append(key, value);
                  }
                }
              } // Let the user add additional data if necessary


              var _iterator14 = dropzone_createForOfIteratorHelper(files, true),
                  _step14;

              try {
                for (_iterator14.s(); !(_step14 = _iterator14.n()).done;) {
                  var _file = _step14.value;
                  this.emit("sending", _file, xhr, formData);
                }
              } catch (err) {
                _iterator14.e(err);
              } finally {
                _iterator14.f();
              }

              if (this.options.uploadMultiple) {
                this.emit("sendingmultiple", files, xhr, formData);
              }

              this._addFormElementData(formData); // Finally add the files
              // Has to be last because some servers (eg: S3) expect the file to be the last parameter


              for (var _i3 = 0; _i3 < dataBlocks.length; _i3++) {
                var dataBlock = dataBlocks[_i3];
                formData.append(dataBlock.name, dataBlock.data, dataBlock.filename);
              }

              this.submitRequest(xhr, formData, files);
            } // Transforms all files with this.options.transformFile and invokes done with the transformed files when done.

          }, {
            key: "_transformFiles",
            value: function _transformFiles(files, done) {
              var _this16 = this;

              var transformedFiles = []; // Clumsy way of handling asynchronous calls, until I get to add a proper Future library.

              var doneCounter = 0;

              var _loop = function _loop(i) {
                _this16.options.transformFile.call(_this16, files[i], function (transformedFile) {
                  transformedFiles[i] = transformedFile;

                  if (++doneCounter === files.length) {
                    done(transformedFiles);
                  }
                });
              };

              for (var i = 0; i < files.length; i++) {
                _loop(i);
              }
            } // Takes care of adding other input elements of the form to the AJAX request

          }, {
            key: "_addFormElementData",
            value: function _addFormElementData(formData) {
              // Take care of other input elements
              if (this.element.tagName === "FORM") {
                var _iterator15 = dropzone_createForOfIteratorHelper(this.element.querySelectorAll("input, textarea, select, button"), true),
                    _step15;

                try {
                  for (_iterator15.s(); !(_step15 = _iterator15.n()).done;) {
                    var input = _step15.value;
                    var inputName = input.getAttribute("name");
                    var inputType = input.getAttribute("type");
                    if (inputType) inputType = inputType.toLowerCase(); // If the input doesn't have a name, we can't use it.

                    if (typeof inputName === "undefined" || inputName === null) continue;

                    if (input.tagName === "SELECT" && input.hasAttribute("multiple")) {
                      // Possibly multiple values
                      var _iterator16 = dropzone_createForOfIteratorHelper(input.options, true),
                          _step16;

                      try {
                        for (_iterator16.s(); !(_step16 = _iterator16.n()).done;) {
                          var option = _step16.value;

                          if (option.selected) {
                            formData.append(inputName, option.value);
                          }
                        }
                      } catch (err) {
                        _iterator16.e(err);
                      } finally {
                        _iterator16.f();
                      }
                    } else if (!inputType || inputType !== "checkbox" && inputType !== "radio" || input.checked) {
                      formData.append(inputName, input.value);
                    }
                  }
                } catch (err) {
                  _iterator15.e(err);
                } finally {
                  _iterator15.f();
                }
              }
            } // Invoked when there is new progress information about given files.
            // If e is not provided, it is assumed that the upload is finished.

          }, {
            key: "_updateFilesUploadProgress",
            value: function _updateFilesUploadProgress(files, xhr, e) {
              if (!files[0].upload.chunked) {
                // Handle file uploads without chunking
                var _iterator17 = dropzone_createForOfIteratorHelper(files, true),
                    _step17;

                try {
                  for (_iterator17.s(); !(_step17 = _iterator17.n()).done;) {
                    var file = _step17.value;

                    if (file.upload.total && file.upload.bytesSent && file.upload.bytesSent == file.upload.total) {
                      // If both, the `total` and `bytesSent` have already been set, and
                      // they are equal (meaning progress is at 100%), we can skip this
                      // file, since an upload progress shouldn't go down.
                      continue;
                    }

                    if (e) {
                      file.upload.progress = 100 * e.loaded / e.total;
                      file.upload.total = e.total;
                      file.upload.bytesSent = e.loaded;
                    } else {
                      // No event, so we're at 100%
                      file.upload.progress = 100;
                      file.upload.bytesSent = file.upload.total;
                    }

                    this.emit("uploadprogress", file, file.upload.progress, file.upload.bytesSent);
                  }
                } catch (err) {
                  _iterator17.e(err);
                } finally {
                  _iterator17.f();
                }
              } else {
                // Handle chunked file uploads
                // Chunked upload is not compatible with uploading multiple files in one
                // request, so we know there's only one file.
                var _file2 = files[0]; // Since this is a chunked upload, we need to update the appropriate chunk
                // progress.

                var chunk = this._getChunk(_file2, xhr);

                if (e) {
                  chunk.progress = 100 * e.loaded / e.total;
                  chunk.total = e.total;
                  chunk.bytesSent = e.loaded;
                } else {
                  // No event, so we're at 100%
                  chunk.progress = 100;
                  chunk.bytesSent = chunk.total;
                } // Now tally the *file* upload progress from its individual chunks


                _file2.upload.progress = 0;
                _file2.upload.total = 0;
                _file2.upload.bytesSent = 0;

                for (var i = 0; i < _file2.upload.totalChunkCount; i++) {
                  if (_file2.upload.chunks[i] && typeof _file2.upload.chunks[i].progress !== "undefined") {
                    _file2.upload.progress += _file2.upload.chunks[i].progress;
                    _file2.upload.total += _file2.upload.chunks[i].total;
                    _file2.upload.bytesSent += _file2.upload.chunks[i].bytesSent;
                  }
                } // Since the process is a percentage, we need to divide by the amount of
                // chunks we've used.


                _file2.upload.progress = _file2.upload.progress / _file2.upload.totalChunkCount;
                this.emit("uploadprogress", _file2, _file2.upload.progress, _file2.upload.bytesSent);
              }
            }
          }, {
            key: "_finishedUploading",
            value: function _finishedUploading(files, xhr, e) {
              var response;

              if (files[0].status === Dropzone.CANCELED) {
                return;
              }

              if (xhr.readyState !== 4) {
                return;
              }

              if (xhr.responseType !== "arraybuffer" && xhr.responseType !== "blob") {
                response = xhr.responseText;

                if (xhr.getResponseHeader("content-type") && ~xhr.getResponseHeader("content-type").indexOf("application/json")) {
                  try {
                    response = JSON.parse(response);
                  } catch (error) {
                    e = error;
                    response = "Invalid JSON response from server.";
                  }
                }
              }

              this._updateFilesUploadProgress(files, xhr);

              if (!(200 <= xhr.status && xhr.status < 300)) {
                this._handleUploadError(files, xhr, response);
              } else {
                if (files[0].upload.chunked) {
                  files[0].upload.finishedChunkUpload(this._getChunk(files[0], xhr), response);
                } else {
                  this._finished(files, response, e);
                }
              }
            }
          }, {
            key: "_handleUploadError",
            value: function _handleUploadError(files, xhr, response) {
              if (files[0].status === Dropzone.CANCELED) {
                return;
              }

              if (files[0].upload.chunked && this.options.retryChunks) {
                var chunk = this._getChunk(files[0], xhr);

                if (chunk.retries++ < this.options.retryChunksLimit) {
                  this._uploadData(files, [chunk.dataBlock]);

                  return;
                } else {
                  console.warn("Retried this chunk too often. Giving up.");
                }
              }

              this._errorProcessing(files, response || this.options.dictResponseError.replace("{{statusCode}}", xhr.status), xhr);
            }
          }, {
            key: "submitRequest",
            value: function submitRequest(xhr, formData, files) {
              if (xhr.readyState != 1) {
                console.warn("Cannot send this request because the XMLHttpRequest.readyState is not OPENED.");
                return;
              }

              xhr.send(formData);
            } // Called internally when processing is finished.
            // Individual callbacks have to be called in the appropriate sections.

          }, {
            key: "_finished",
            value: function _finished(files, responseText, e) {
              var _iterator18 = dropzone_createForOfIteratorHelper(files, true),
                  _step18;

              try {
                for (_iterator18.s(); !(_step18 = _iterator18.n()).done;) {
                  var file = _step18.value;
                  file.status = Dropzone.SUCCESS;
                  this.emit("success", file, responseText, e);
                  this.emit("complete", file);
                }
              } catch (err) {
                _iterator18.e(err);
              } finally {
                _iterator18.f();
              }

              if (this.options.uploadMultiple) {
                this.emit("successmultiple", files, responseText, e);
                this.emit("completemultiple", files);
              }

              if (this.options.autoProcessQueue) {
                return this.processQueue();
              }
            } // Called internally when processing is finished.
            // Individual callbacks have to be called in the appropriate sections.

          }, {
            key: "_errorProcessing",
            value: function _errorProcessing(files, message, xhr) {
              var _iterator19 = dropzone_createForOfIteratorHelper(files, true),
                  _step19;

              try {
                for (_iterator19.s(); !(_step19 = _iterator19.n()).done;) {
                  var file = _step19.value;
                  file.status = Dropzone.ERROR;
                  this.emit("error", file, message, xhr);
                  this.emit("complete", file);
                }
              } catch (err) {
                _iterator19.e(err);
              } finally {
                _iterator19.f();
              }

              if (this.options.uploadMultiple) {
                this.emit("errormultiple", files, message, xhr);
                this.emit("completemultiple", files);
              }

              if (this.options.autoProcessQueue) {
                return this.processQueue();
              }
            }
          }], [{
            key: "initClass",
            value: function initClass() {
              // Exposing the emitter class, mainly for tests
              this.prototype.Emitter = Emitter;
              /*
               This is a list of all available events you can register on a dropzone object.
                You can register an event handler like this:
                dropzone.on("dragEnter", function() { });
                */

              this.prototype.events = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile", "addedfiles", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple", "uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled", "canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached", "queuecomplete"];
              this.prototype._thumbnailQueue = [];
              this.prototype._processingThumbnail = false;
            } // global utility

          }, {
            key: "extend",
            value: function extend(target) {
              for (var _len2 = arguments.length, objects = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
                objects[_key2 - 1] = arguments[_key2];
              }

              for (var _i4 = 0, _objects = objects; _i4 < _objects.length; _i4++) {
                var object = _objects[_i4];

                for (var key in object) {
                  var val = object[key];
                  target[key] = val;
                }
              }

              return target;
            }
          }, {
            key: "uuidv4",
            value: function uuidv4() {
              return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
                var r = Math.random() * 16 | 0,
                    v = c === "x" ? r : r & 0x3 | 0x8;
                return v.toString(16);
              });
            }
          }]);
          return Dropzone;
        }(Emitter);

        _Dropzone.initClass();

        _Dropzone.version = "5.9.2"; // This is a map of options for your different dropzones. Add configurations
        // to this object for your different dropzone elemens.
        //
        // Example:
        //
        //     Dropzone.options.myDropzoneElementId = { maxFilesize: 1 };
        //
        // To disable autoDiscover for a specific element, you can set `false` as an option:
        //
        //     Dropzone.options.myDisabledElementId = false;
        //
        // And in html:
        //
        //     <form action="/upload" id="my-dropzone-element-id" class="dropzone"></form>

        _Dropzone.options = {}; // Returns the options for an element or undefined if none available.

        _Dropzone.optionsForElement = function (element) {
          // Get the `Dropzone.options.elementId` for this element if it exists
          if (element.getAttribute("id")) {
            return _Dropzone.options[camelize(element.getAttribute("id"))];
          } else {
            return undefined;
          }
        }; // Holds a list of all dropzone instances


        _Dropzone.instances = []; // Returns the dropzone for given element if any

        _Dropzone.forElement = function (element) {
          if (typeof element === "string") {
            element = document.querySelector(element);
          }

          if ((element != null ? element.dropzone : undefined) == null) {
            throw new Error("No Dropzone found for given element. This is probably because you're trying to access it before Dropzone had the time to initialize. Use the `init` option to setup any additional observers on your Dropzone.");
          }

          return element.dropzone;
        }; // Set to false if you don't want Dropzone to automatically find and attach to .dropzone elements.


        _Dropzone.autoDiscover = true; // Looks for all .dropzone elements and creates a dropzone for them

        _Dropzone.discover = function () {
          var dropzones;

          if (document.querySelectorAll) {
            dropzones = document.querySelectorAll(".dropzone");
          } else {
            dropzones = []; // IE :(

            var checkElements = function checkElements(elements) {
              return function () {
                var result = [];

                var _iterator20 = dropzone_createForOfIteratorHelper(elements, true),
                    _step20;

                try {
                  for (_iterator20.s(); !(_step20 = _iterator20.n()).done;) {
                    var el = _step20.value;

                    if (/(^| )dropzone($| )/.test(el.className)) {
                      result.push(dropzones.push(el));
                    } else {
                      result.push(undefined);
                    }
                  }
                } catch (err) {
                  _iterator20.e(err);
                } finally {
                  _iterator20.f();
                }

                return result;
              }();
            };

            checkElements(document.getElementsByTagName("div"));
            checkElements(document.getElementsByTagName("form"));
          }

          return function () {
            var result = [];

            var _iterator21 = dropzone_createForOfIteratorHelper(dropzones, true),
                _step21;

            try {
              for (_iterator21.s(); !(_step21 = _iterator21.n()).done;) {
                var dropzone = _step21.value; // Create a dropzone unless auto discover has been disabled for specific element

                if (_Dropzone.optionsForElement(dropzone) !== false) {
                  result.push(new _Dropzone(dropzone));
                } else {
                  result.push(undefined);
                }
              }
            } catch (err) {
              _iterator21.e(err);
            } finally {
              _iterator21.f();
            }

            return result;
          }();
        }; // Some browsers support drag and drog functionality, but not correctly.
        //
        // So I created a blocklist of userAgents. Yes, yes. Browser sniffing, I know.
        // But what to do when browsers *theoretically* support an API, but crash
        // when using it.
        //
        // This is a list of regular expressions tested against navigator.userAgent
        //
        // ** It should only be used on browser that *do* support the API, but
        // incorrectly **


        _Dropzone.blockedBrowsers = [// The mac os and windows phone version of opera 12 seems to have a problem with the File drag'n'drop API.
        /opera.*(Macintosh|Windows Phone).*version\/12/i]; // Checks if the browser is supported

        _Dropzone.isBrowserSupported = function () {
          var capableBrowser = true;

          if (window.File && window.FileReader && window.FileList && window.Blob && window.FormData && document.querySelector) {
            if (!("classList" in document.createElement("a"))) {
              capableBrowser = false;
            } else {
              if (_Dropzone.blacklistedBrowsers !== undefined) {
                // Since this has been renamed, this makes sure we don't break older
                // configuration.
                _Dropzone.blockedBrowsers = _Dropzone.blacklistedBrowsers;
              } // The browser supports the API, but may be blocked.


              var _iterator22 = dropzone_createForOfIteratorHelper(_Dropzone.blockedBrowsers, true),
                  _step22;

              try {
                for (_iterator22.s(); !(_step22 = _iterator22.n()).done;) {
                  var regex = _step22.value;

                  if (regex.test(navigator.userAgent)) {
                    capableBrowser = false;
                    continue;
                  }
                }
              } catch (err) {
                _iterator22.e(err);
              } finally {
                _iterator22.f();
              }
            }
          } else {
            capableBrowser = false;
          }

          return capableBrowser;
        };

        _Dropzone.dataURItoBlob = function (dataURI) {
          // convert base64 to raw binary data held in a string
          // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
          var byteString = atob(dataURI.split(",")[1]); // separate out the mime component

          var mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0]; // write the bytes of the string to an ArrayBuffer

          var ab = new ArrayBuffer(byteString.length);
          var ia = new Uint8Array(ab);

          for (var i = 0, end = byteString.length, asc = 0 <= end; asc ? i <= end : i >= end; asc ? i++ : i--) {
            ia[i] = byteString.charCodeAt(i);
          } // write the ArrayBuffer to a blob


          return new Blob([ab], {
            type: mimeString
          });
        }; // Returns an array without the rejected item


        var without = function without(list, rejectedItem) {
          return list.filter(function (item) {
            return item !== rejectedItem;
          }).map(function (item) {
            return item;
          });
        }; // abc-def_ghi -> abcDefGhi


        var camelize = function camelize(str) {
          return str.replace(/[\-_](\w)/g, function (match) {
            return match.charAt(1).toUpperCase();
          });
        }; // Creates an element from string


        _Dropzone.createElement = function (string) {
          var div = document.createElement("div");
          div.innerHTML = string;
          return div.childNodes[0];
        }; // Tests if given element is inside (or simply is) the container


        _Dropzone.elementInside = function (element, container) {
          if (element === container) {
            return true;
          } // Coffeescript doesn't support do/while loops


          while (element = element.parentNode) {
            if (element === container) {
              return true;
            }
          }

          return false;
        };

        _Dropzone.getElement = function (el, name) {
          var element;

          if (typeof el === "string") {
            element = document.querySelector(el);
          } else if (el.nodeType != null) {
            element = el;
          }

          if (element == null) {
            throw new Error("Invalid `".concat(name, "` option provided. Please provide a CSS selector or a plain HTML element."));
          }

          return element;
        };

        _Dropzone.getElements = function (els, name) {
          var el, elements;

          if (els instanceof Array) {
            elements = [];

            try {
              var _iterator23 = dropzone_createForOfIteratorHelper(els, true),
                  _step23;

              try {
                for (_iterator23.s(); !(_step23 = _iterator23.n()).done;) {
                  el = _step23.value;
                  elements.push(this.getElement(el, name));
                }
              } catch (err) {
                _iterator23.e(err);
              } finally {
                _iterator23.f();
              }
            } catch (e) {
              elements = null;
            }
          } else if (typeof els === "string") {
            elements = [];

            var _iterator24 = dropzone_createForOfIteratorHelper(document.querySelectorAll(els), true),
                _step24;

            try {
              for (_iterator24.s(); !(_step24 = _iterator24.n()).done;) {
                el = _step24.value;
                elements.push(el);
              }
            } catch (err) {
              _iterator24.e(err);
            } finally {
              _iterator24.f();
            }
          } else if (els.nodeType != null) {
            elements = [els];
          }

          if (elements == null || !elements.length) {
            throw new Error("Invalid `".concat(name, "` option provided. Please provide a CSS selector, a plain HTML element or a list of those."));
          }

          return elements;
        }; // Asks the user the question and calls accepted or rejected accordingly
        //
        // The default implementation just uses `window.confirm` and then calls the
        // appropriate callback.


        _Dropzone.confirm = function (question, accepted, rejected) {
          if (window.confirm(question)) {
            return accepted();
          } else if (rejected != null) {
            return rejected();
          }
        }; // Validates the mime type like this:
        //
        // https://developer.mozilla.org/en-US/docs/HTML/Element/input#attr-accept


        _Dropzone.isValidFile = function (file, acceptedFiles) {
          if (!acceptedFiles) {
            return true;
          } // If there are no accepted mime types, it's OK


          acceptedFiles = acceptedFiles.split(",");
          var mimeType = file.type;
          var baseMimeType = mimeType.replace(/\/.*$/, "");

          var _iterator25 = dropzone_createForOfIteratorHelper(acceptedFiles, true),
              _step25;

          try {
            for (_iterator25.s(); !(_step25 = _iterator25.n()).done;) {
              var validType = _step25.value;
              validType = validType.trim();

              if (validType.charAt(0) === ".") {
                if (file.name.toLowerCase().indexOf(validType.toLowerCase(), file.name.length - validType.length) !== -1) {
                  return true;
                }
              } else if (/\/\*$/.test(validType)) {
                // This is something like a image/* mime type
                if (baseMimeType === validType.replace(/\/.*$/, "")) {
                  return true;
                }
              } else {
                if (mimeType === validType) {
                  return true;
                }
              }
            }
          } catch (err) {
            _iterator25.e(err);
          } finally {
            _iterator25.f();
          }

          return false;
        }; // Augment jQuery


        if (typeof jQuery !== "undefined" && jQuery !== null) {
          jQuery.fn.dropzone = function (options) {
            return this.each(function () {
              return new _Dropzone(this, options);
            });
          };
        } // Dropzone file status codes


        _Dropzone.ADDED = "added";
        _Dropzone.QUEUED = "queued"; // For backwards compatibility. Now, if a file is accepted, it's either queued
        // or uploading.

        _Dropzone.ACCEPTED = _Dropzone.QUEUED;
        _Dropzone.UPLOADING = "uploading";
        _Dropzone.PROCESSING = _Dropzone.UPLOADING; // alias

        _Dropzone.CANCELED = "canceled";
        _Dropzone.ERROR = "error";
        _Dropzone.SUCCESS = "success";
        /*
        
         Bugfix for iOS 6 and 7
         Source: http://stackoverflow.com/questions/11929099/html5-canvas-drawimage-ratio-bug-ios
         based on the work of https://github.com/stomita/ios-imagefile-megapixel
        
         */
        // Detecting vertical squash in loaded image.
        // Fixes a bug which squash image vertically while drawing into canvas for some images.
        // This is a bug in iOS6 devices. This function from https://github.com/stomita/ios-imagefile-megapixel

        var detectVerticalSquash = function detectVerticalSquash(img) {
          var iw = img.naturalWidth;
          var ih = img.naturalHeight;
          var canvas = document.createElement("canvas");
          canvas.width = 1;
          canvas.height = ih;
          var ctx = canvas.getContext("2d");
          ctx.drawImage(img, 0, 0);

          var _ctx$getImageData = ctx.getImageData(1, 0, 1, ih),
              data = _ctx$getImageData.data; // search image edge pixel position in case it is squashed vertically.


          var sy = 0;
          var ey = ih;
          var py = ih;

          while (py > sy) {
            var alpha = data[(py - 1) * 4 + 3];

            if (alpha === 0) {
              ey = py;
            } else {
              sy = py;
            }

            py = ey + sy >> 1;
          }

          var ratio = py / ih;

          if (ratio === 0) {
            return 1;
          } else {
            return ratio;
          }
        }; // A replacement for context.drawImage
        // (args are for source and destination).


        var drawImageIOSFix = function drawImageIOSFix(ctx, img, sx, sy, sw, sh, dx, dy, dw, dh) {
          var vertSquashRatio = detectVerticalSquash(img);
          return ctx.drawImage(img, sx, sy, sw, sh, dx, dy, dw, dh / vertSquashRatio);
        }; // Based on MinifyJpeg
        // Source: http://www.perry.cz/files/ExifRestorer.js
        // http://elicon.blog57.fc2.com/blog-entry-206.html


        var ExifRestore = /*#__PURE__*/function () {
          function ExifRestore() {
            dropzone_classCallCheck(this, ExifRestore);
          }

          dropzone_createClass(ExifRestore, null, [{
            key: "initClass",
            value: function initClass() {
              this.KEY_STR = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
            }
          }, {
            key: "encode64",
            value: function encode64(input) {
              var output = "";
              var chr1 = undefined;
              var chr2 = undefined;
              var chr3 = "";
              var enc1 = undefined;
              var enc2 = undefined;
              var enc3 = undefined;
              var enc4 = "";
              var i = 0;

              while (true) {
                chr1 = input[i++];
                chr2 = input[i++];
                chr3 = input[i++];
                enc1 = chr1 >> 2;
                enc2 = (chr1 & 3) << 4 | chr2 >> 4;
                enc3 = (chr2 & 15) << 2 | chr3 >> 6;
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                  enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                  enc4 = 64;
                }

                output = output + this.KEY_STR.charAt(enc1) + this.KEY_STR.charAt(enc2) + this.KEY_STR.charAt(enc3) + this.KEY_STR.charAt(enc4);
                chr1 = chr2 = chr3 = "";
                enc1 = enc2 = enc3 = enc4 = "";

                if (!(i < input.length)) {
                  break;
                }
              }

              return output;
            }
          }, {
            key: "restore",
            value: function restore(origFileBase64, resizedFileBase64) {
              if (!origFileBase64.match("data:image/jpeg;base64,")) {
                return resizedFileBase64;
              }

              var rawImage = this.decode64(origFileBase64.replace("data:image/jpeg;base64,", ""));
              var segments = this.slice2Segments(rawImage);
              var image = this.exifManipulation(resizedFileBase64, segments);
              return "data:image/jpeg;base64,".concat(this.encode64(image));
            }
          }, {
            key: "exifManipulation",
            value: function exifManipulation(resizedFileBase64, segments) {
              var exifArray = this.getExifArray(segments);
              var newImageArray = this.insertExif(resizedFileBase64, exifArray);
              var aBuffer = new Uint8Array(newImageArray);
              return aBuffer;
            }
          }, {
            key: "getExifArray",
            value: function getExifArray(segments) {
              var seg = undefined;
              var x = 0;

              while (x < segments.length) {
                seg = segments[x];

                if (seg[0] === 255 & seg[1] === 225) {
                  return seg;
                }

                x++;
              }

              return [];
            }
          }, {
            key: "insertExif",
            value: function insertExif(resizedFileBase64, exifArray) {
              var imageData = resizedFileBase64.replace("data:image/jpeg;base64,", "");
              var buf = this.decode64(imageData);
              var separatePoint = buf.indexOf(255, 3);
              var mae = buf.slice(0, separatePoint);
              var ato = buf.slice(separatePoint);
              var array = mae;
              array = array.concat(exifArray);
              array = array.concat(ato);
              return array;
            }
          }, {
            key: "slice2Segments",
            value: function slice2Segments(rawImageArray) {
              var head = 0;
              var segments = [];

              while (true) {
                var length;

                if (rawImageArray[head] === 255 & rawImageArray[head + 1] === 218) {
                  break;
                }

                if (rawImageArray[head] === 255 & rawImageArray[head + 1] === 216) {
                  head += 2;
                } else {
                  length = rawImageArray[head + 2] * 256 + rawImageArray[head + 3];
                  var endPoint = head + length + 2;
                  var seg = rawImageArray.slice(head, endPoint);
                  segments.push(seg);
                  head = endPoint;
                }

                if (head > rawImageArray.length) {
                  break;
                }
              }

              return segments;
            }
          }, {
            key: "decode64",
            value: function decode64(input) {
              var output = "";
              var chr1 = undefined;
              var chr2 = undefined;
              var chr3 = "";
              var enc1 = undefined;
              var enc2 = undefined;
              var enc3 = undefined;
              var enc4 = "";
              var i = 0;
              var buf = []; // remove all characters that are not A-Z, a-z, 0-9, +, /, or =

              var base64test = /[^A-Za-z0-9\+\/\=]/g;

              if (base64test.exec(input)) {
                console.warn("There were invalid base64 characters in the input text.\nValid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\nExpect errors in decoding.");
              }

              input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

              while (true) {
                enc1 = this.KEY_STR.indexOf(input.charAt(i++));
                enc2 = this.KEY_STR.indexOf(input.charAt(i++));
                enc3 = this.KEY_STR.indexOf(input.charAt(i++));
                enc4 = this.KEY_STR.indexOf(input.charAt(i++));
                chr1 = enc1 << 2 | enc2 >> 4;
                chr2 = (enc2 & 15) << 4 | enc3 >> 2;
                chr3 = (enc3 & 3) << 6 | enc4;
                buf.push(chr1);

                if (enc3 !== 64) {
                  buf.push(chr2);
                }

                if (enc4 !== 64) {
                  buf.push(chr3);
                }

                chr1 = chr2 = chr3 = "";
                enc1 = enc2 = enc3 = enc4 = "";

                if (!(i < input.length)) {
                  break;
                }
              }

              return buf;
            }
          }]);
          return ExifRestore;
        }();

        ExifRestore.initClass();
        /*
         * contentloaded.js
         *
         * Author: Diego Perini (diego.perini at gmail.com)
         * Summary: cross-browser wrapper for DOMContentLoaded
         * Updated: 20101020
         * License: MIT
         * Version: 1.2
         *
         * URL:
         * http://javascript.nwbox.com/ContentLoaded/
         * http://javascript.nwbox.com/ContentLoaded/MIT-LICENSE
         */
        // @win window reference
        // @fn function reference

        var contentLoaded = function contentLoaded(win, fn) {
          var done = false;
          var top = true;
          var doc = win.document;
          var root = doc.documentElement;
          var add = doc.addEventListener ? "addEventListener" : "attachEvent";
          var rem = doc.addEventListener ? "removeEventListener" : "detachEvent";
          var pre = doc.addEventListener ? "" : "on";

          var init = function init(e) {
            if (e.type === "readystatechange" && doc.readyState !== "complete") {
              return;
            }

            (e.type === "load" ? win : doc)[rem](pre + e.type, init, false);

            if (!done && (done = true)) {
              return fn.call(win, e.type || e);
            }
          };

          var poll = function poll() {
            try {
              root.doScroll("left");
            } catch (e) {
              setTimeout(poll, 50);
              return;
            }

            return init("poll");
          };

          if (doc.readyState !== "complete") {
            if (doc.createEventObject && root.doScroll) {
              try {
                top = !win.frameElement;
              } catch (error) {}

              if (top) {
                poll();
              }
            }

            doc[add](pre + "DOMContentLoaded", init, false);
            doc[add](pre + "readystatechange", init, false);
            return win[add](pre + "load", init, false);
          }
        }; // As a single function to be able to write tests.


        _Dropzone._autoDiscoverFunction = function () {
          if (_Dropzone.autoDiscover) {
            return _Dropzone.discover();
          }
        };

        contentLoaded(window, _Dropzone._autoDiscoverFunction);

        function __guard__(value, transform) {
          return typeof value !== "undefined" && value !== null ? transform(value) : undefined;
        }

        function __guardMethod__(obj, methodName, transform) {
          if (typeof obj !== "undefined" && obj !== null && typeof obj[methodName] === "function") {
            return transform(obj, methodName);
          } else {
            return undefined;
          }
        }

        ; // CONCATENATED MODULE: ./tool/dropzone.dist.js
        /// Make Dropzone a global variable.

        window.Dropzone = _Dropzone;
        /* harmony default export */

        var dropzone_dist = _Dropzone;
      }();
      /******/

      return __webpack_exports__;
      /******/
    }()
  );
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/underscore/modules/_baseCreate.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/_baseCreate.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return baseCreate; });
/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");

 // Create a naked function reference for surrogate-prototype-swapping.

function ctor() {
  return function () {};
} // An internal function for creating a new object that inherits from another.


function baseCreate(prototype) {
  if (!Object(_isObject_js__WEBPACK_IMPORTED_MODULE_0__["default"])(prototype)) return {};
  if (_setup_js__WEBPACK_IMPORTED_MODULE_1__["nativeCreate"]) return Object(_setup_js__WEBPACK_IMPORTED_MODULE_1__["nativeCreate"])(prototype);
  var Ctor = ctor();
  Ctor.prototype = prototype;
  var result = new Ctor();
  Ctor.prototype = null;
  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/_baseIteratee.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_baseIteratee.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return baseIteratee; });
/* harmony import */ var _identity_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./identity.js */ "./node_modules/underscore/modules/identity.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");
/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");
/* harmony import */ var _matcher_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./matcher.js */ "./node_modules/underscore/modules/matcher.js");
/* harmony import */ var _property_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./property.js */ "./node_modules/underscore/modules/property.js");
/* harmony import */ var _optimizeCb_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./_optimizeCb.js */ "./node_modules/underscore/modules/_optimizeCb.js");






 // An internal function to generate callbacks that can be applied to each
// element in a collection, returning the desired result — either `_.identity`,
// an arbitrary callback, a property matcher, or a property accessor.

function baseIteratee(value, context, argCount) {
  if (value == null) return _identity_js__WEBPACK_IMPORTED_MODULE_0__["default"];
  if (Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(value)) return Object(_optimizeCb_js__WEBPACK_IMPORTED_MODULE_6__["default"])(value, context, argCount);
  if (Object(_isObject_js__WEBPACK_IMPORTED_MODULE_2__["default"])(value) && !Object(_isArray_js__WEBPACK_IMPORTED_MODULE_3__["default"])(value)) return Object(_matcher_js__WEBPACK_IMPORTED_MODULE_4__["default"])(value);
  return Object(_property_js__WEBPACK_IMPORTED_MODULE_5__["default"])(value);
}

/***/ }),

/***/ "./node_modules/underscore/modules/_cb.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/_cb.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return cb; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _baseIteratee_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_baseIteratee.js */ "./node_modules/underscore/modules/_baseIteratee.js");
/* harmony import */ var _iteratee_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./iteratee.js */ "./node_modules/underscore/modules/iteratee.js");


 // The function we call internally to generate a callback. It invokes
// `_.iteratee` if overridden, otherwise `baseIteratee`.

function cb(value, context, argCount) {
  if (_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].iteratee !== _iteratee_js__WEBPACK_IMPORTED_MODULE_2__["default"]) return _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].iteratee(value, context);
  return Object(_baseIteratee_js__WEBPACK_IMPORTED_MODULE_1__["default"])(value, context, argCount);
}

/***/ }),

/***/ "./node_modules/underscore/modules/_chainResult.js":
/*!*********************************************************!*\
  !*** ./node_modules/underscore/modules/_chainResult.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return chainResult; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
 // Helper function to continue chaining intermediate results.

function chainResult(instance, obj) {
  return instance._chain ? Object(_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj).chain() : obj;
}

/***/ }),

/***/ "./node_modules/underscore/modules/_collectNonEnumProps.js":
/*!*****************************************************************!*\
  !*** ./node_modules/underscore/modules/_collectNonEnumProps.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return collectNonEnumProps; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");


 // Internal helper to create a simple lookup structure.
// `collectNonEnumProps` used to depend on `_.contains`, but this led to
// circular imports. `emulatedSet` is a one-off solution that only works for
// arrays of strings.

function emulatedSet(keys) {
  var hash = {};

  for (var l = keys.length, i = 0; i < l; ++i) {
    hash[keys[i]] = true;
  }

  return {
    contains: function contains(key) {
      return hash[key];
    },
    push: function push(key) {
      hash[key] = true;
      return keys.push(key);
    }
  };
} // Internal helper. Checks `keys` for the presence of keys in IE < 9 that won't
// be iterated by `for key in ...` and thus missed. Extends `keys` in place if
// needed.


function collectNonEnumProps(obj, keys) {
  keys = emulatedSet(keys);
  var nonEnumIdx = _setup_js__WEBPACK_IMPORTED_MODULE_0__["nonEnumerableProps"].length;
  var constructor = obj.constructor;
  var proto = Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(constructor) && constructor.prototype || _setup_js__WEBPACK_IMPORTED_MODULE_0__["ObjProto"]; // Constructor is a special case.

  var prop = 'constructor';
  if (Object(_has_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj, prop) && !keys.contains(prop)) keys.push(prop);

  while (nonEnumIdx--) {
    prop = _setup_js__WEBPACK_IMPORTED_MODULE_0__["nonEnumerableProps"][nonEnumIdx];

    if (prop in obj && obj[prop] !== proto[prop] && !keys.contains(prop)) {
      keys.push(prop);
    }
  }
}

/***/ }),

/***/ "./node_modules/underscore/modules/_createAssigner.js":
/*!************************************************************!*\
  !*** ./node_modules/underscore/modules/_createAssigner.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createAssigner; });
// An internal function for creating assigner functions.
function createAssigner(keysFunc, defaults) {
  return function (obj) {
    var length = arguments.length;
    if (defaults) obj = Object(obj);
    if (length < 2 || obj == null) return obj;

    for (var index = 1; index < length; index++) {
      var source = arguments[index],
          keys = keysFunc(source),
          l = keys.length;

      for (var i = 0; i < l; i++) {
        var key = keys[i];
        if (!defaults || obj[key] === void 0) obj[key] = source[key];
      }
    }

    return obj;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_createEscaper.js":
/*!***********************************************************!*\
  !*** ./node_modules/underscore/modules/_createEscaper.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createEscaper; });
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
 // Internal helper to generate functions for escaping and unescaping strings
// to/from HTML interpolation.

function createEscaper(map) {
  var escaper = function escaper(match) {
    return map[match];
  }; // Regexes for identifying a key that needs to be escaped.


  var source = '(?:' + Object(_keys_js__WEBPACK_IMPORTED_MODULE_0__["default"])(map).join('|') + ')';
  var testRegexp = RegExp(source);
  var replaceRegexp = RegExp(source, 'g');
  return function (string) {
    string = string == null ? '' : '' + string;
    return testRegexp.test(string) ? string.replace(replaceRegexp, escaper) : string;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_createIndexFinder.js":
/*!***************************************************************!*\
  !*** ./node_modules/underscore/modules/_createIndexFinder.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createIndexFinder; });
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _isNaN_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isNaN.js */ "./node_modules/underscore/modules/isNaN.js");


 // Internal function to generate the `_.indexOf` and `_.lastIndexOf` functions.

function createIndexFinder(dir, predicateFind, sortedIndex) {
  return function (array, item, idx) {
    var i = 0,
        length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array);

    if (typeof idx == 'number') {
      if (dir > 0) {
        i = idx >= 0 ? idx : Math.max(idx + length, i);
      } else {
        length = idx >= 0 ? Math.min(idx + 1, length) : idx + length + 1;
      }
    } else if (sortedIndex && idx && length) {
      idx = sortedIndex(array, item);
      return array[idx] === item ? idx : -1;
    }

    if (item !== item) {
      idx = predicateFind(_setup_js__WEBPACK_IMPORTED_MODULE_1__["slice"].call(array, i, length), _isNaN_js__WEBPACK_IMPORTED_MODULE_2__["default"]);
      return idx >= 0 ? idx + i : -1;
    }

    for (idx = dir > 0 ? i : length - 1; idx >= 0 && idx < length; idx += dir) {
      if (array[idx] === item) return idx;
    }

    return -1;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_createPredicateIndexFinder.js":
/*!************************************************************************!*\
  !*** ./node_modules/underscore/modules/_createPredicateIndexFinder.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createPredicateIndexFinder; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");

 // Internal function to generate `_.findIndex` and `_.findLastIndex`.

function createPredicateIndexFinder(dir) {
  return function (array, predicate, context) {
    predicate = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(predicate, context);
    var length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_1__["default"])(array);
    var index = dir > 0 ? 0 : length - 1;

    for (; index >= 0 && index < length; index += dir) {
      if (predicate(array[index], index, array)) return index;
    }

    return -1;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_createReduce.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_createReduce.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createReduce; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
/* harmony import */ var _optimizeCb_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_optimizeCb.js */ "./node_modules/underscore/modules/_optimizeCb.js");


 // Internal helper to create a reducing function, iterating left or right.

function createReduce(dir) {
  // Wrap code that reassigns argument variables in a separate function than
  // the one that accesses `arguments.length` to avoid a perf hit. (#1991)
  var reducer = function reducer(obj, iteratee, memo, initial) {
    var _keys = !Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj) && Object(_keys_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj),
        length = (_keys || obj).length,
        index = dir > 0 ? 0 : length - 1;

    if (!initial) {
      memo = obj[_keys ? _keys[index] : index];
      index += dir;
    }

    for (; index >= 0 && index < length; index += dir) {
      var currentKey = _keys ? _keys[index] : index;
      memo = iteratee(memo, obj[currentKey], currentKey, obj);
    }

    return memo;
  };

  return function (obj, iteratee, memo, context) {
    var initial = arguments.length >= 3;
    return reducer(obj, Object(_optimizeCb_js__WEBPACK_IMPORTED_MODULE_2__["default"])(iteratee, context, 4), memo, initial);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_createSizePropertyCheck.js":
/*!*********************************************************************!*\
  !*** ./node_modules/underscore/modules/_createSizePropertyCheck.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createSizePropertyCheck; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Common internal logic for `isArrayLike` and `isBufferLike`.

function createSizePropertyCheck(getSizeProperty) {
  return function (collection) {
    var sizeProperty = getSizeProperty(collection);
    return typeof sizeProperty == 'number' && sizeProperty >= 0 && sizeProperty <= _setup_js__WEBPACK_IMPORTED_MODULE_0__["MAX_ARRAY_INDEX"];
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_deepGet.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/_deepGet.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return deepGet; });
// Internal function to obtain a nested property in `obj` along `path`.
function deepGet(obj, path) {
  var length = path.length;

  for (var i = 0; i < length; i++) {
    if (obj == null) return void 0;
    obj = obj[path[i]];
  }

  return length ? obj : void 0;
}

/***/ }),

/***/ "./node_modules/underscore/modules/_escapeMap.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/_escapeMap.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// Internal list of HTML entities for escaping.
/* harmony default export */ __webpack_exports__["default"] = ({
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#x27;',
  '`': '&#x60;'
});

/***/ }),

/***/ "./node_modules/underscore/modules/_executeBound.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_executeBound.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return executeBound; });
/* harmony import */ var _baseCreate_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_baseCreate.js */ "./node_modules/underscore/modules/_baseCreate.js");
/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");

 // Internal function to execute `sourceFunc` bound to `context` with optional
// `args`. Determines whether to execute a function as a constructor or as a
// normal function.

function executeBound(sourceFunc, boundFunc, context, callingContext, args) {
  if (!(callingContext instanceof boundFunc)) return sourceFunc.apply(context, args);
  var self = Object(_baseCreate_js__WEBPACK_IMPORTED_MODULE_0__["default"])(sourceFunc.prototype);
  var result = sourceFunc.apply(self, args);
  if (Object(_isObject_js__WEBPACK_IMPORTED_MODULE_1__["default"])(result)) return result;
  return self;
}

/***/ }),

/***/ "./node_modules/underscore/modules/_flatten.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/_flatten.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return flatten; });
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");
/* harmony import */ var _isArguments_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./isArguments.js */ "./node_modules/underscore/modules/isArguments.js");



 // Internal implementation of a recursive `flatten` function.

function flatten(input, depth, strict, output) {
  output = output || [];

  if (!depth && depth !== 0) {
    depth = Infinity;
  } else if (depth <= 0) {
    return output.concat(input);
  }

  var idx = output.length;

  for (var i = 0, length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(input); i < length; i++) {
    var value = input[i];

    if (Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__["default"])(value) && (Object(_isArray_js__WEBPACK_IMPORTED_MODULE_2__["default"])(value) || Object(_isArguments_js__WEBPACK_IMPORTED_MODULE_3__["default"])(value))) {
      // Flatten current level of array or arguments object.
      if (depth > 1) {
        flatten(value, depth - 1, strict, output);
        idx = output.length;
      } else {
        var j = 0,
            len = value.length;

        while (j < len) {
          output[idx++] = value[j++];
        }
      }
    } else if (!strict) {
      output[idx++] = value;
    }
  }

  return output;
}

/***/ }),

/***/ "./node_modules/underscore/modules/_getByteLength.js":
/*!***********************************************************!*\
  !*** ./node_modules/underscore/modules/_getByteLength.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shallowProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_shallowProperty.js */ "./node_modules/underscore/modules/_shallowProperty.js");
 // Internal helper to obtain the `byteLength` property of an object.

/* harmony default export */ __webpack_exports__["default"] = (Object(_shallowProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])('byteLength'));

/***/ }),

/***/ "./node_modules/underscore/modules/_getLength.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/_getLength.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shallowProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_shallowProperty.js */ "./node_modules/underscore/modules/_shallowProperty.js");
 // Internal helper to obtain the `length` property of an object.

/* harmony default export */ __webpack_exports__["default"] = (Object(_shallowProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])('length'));

/***/ }),

/***/ "./node_modules/underscore/modules/_group.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/_group.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return group; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");

 // An internal function used for aggregate "group by" operations.

function group(behavior, partition) {
  return function (obj, iteratee, context) {
    var result = partition ? [[], []] : {};
    iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context);
    Object(_each_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj, function (value, index) {
      var key = iteratee(value, index, obj);
      behavior(result, value, key);
    });
    return result;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_has.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/_has.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return has; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Internal function to check whether `key` is an own property name of `obj`.

function has(obj, key) {
  return obj != null && _setup_js__WEBPACK_IMPORTED_MODULE_0__["hasOwnProperty"].call(obj, key);
}

/***/ }),

/***/ "./node_modules/underscore/modules/_hasObjectTag.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_hasObjectTag.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Object'));

/***/ }),

/***/ "./node_modules/underscore/modules/_isArrayLike.js":
/*!*********************************************************!*\
  !*** ./node_modules/underscore/modules/_isArrayLike.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createSizePropertyCheck_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createSizePropertyCheck.js */ "./node_modules/underscore/modules/_createSizePropertyCheck.js");
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");

 // Internal helper for collection methods to determine whether a collection
// should be iterated as an array or as an object.
// Related: https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength
// Avoids a very nasty iOS 8 JIT bug on ARM-64. #2094

/* harmony default export */ __webpack_exports__["default"] = (Object(_createSizePropertyCheck_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_getLength_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/_isBufferLike.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_isBufferLike.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createSizePropertyCheck_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createSizePropertyCheck.js */ "./node_modules/underscore/modules/_createSizePropertyCheck.js");
/* harmony import */ var _getByteLength_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_getByteLength.js */ "./node_modules/underscore/modules/_getByteLength.js");

 // Internal helper to determine whether we should spend extensive checks against
// `ArrayBuffer` et al.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createSizePropertyCheck_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_getByteLength_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/_keyInObj.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/_keyInObj.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return keyInObj; });
// Internal `_.pick` helper function to determine whether `key` is an enumerable
// property name of `obj`.
function keyInObj(value, key, obj) {
  return key in obj;
}

/***/ }),

/***/ "./node_modules/underscore/modules/_methodFingerprint.js":
/*!***************************************************************!*\
  !*** ./node_modules/underscore/modules/_methodFingerprint.js ***!
  \***************************************************************/
/*! exports provided: ie11fingerprint, mapMethods, weakMapMethods, setMethods */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ie11fingerprint", function() { return ie11fingerprint; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapMethods", function() { return mapMethods; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "weakMapMethods", function() { return weakMapMethods; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setMethods", function() { return setMethods; });
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _allKeys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./allKeys.js */ "./node_modules/underscore/modules/allKeys.js");


 // Since the regular `Object.prototype.toString` type tests don't work for
// some types in IE 11, we use a fingerprinting heuristic instead, based
// on the methods. It's not great, but it's the best we got.
// The fingerprint method lists are defined below.

function ie11fingerprint(methods) {
  var length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(methods);
  return function (obj) {
    if (obj == null) return false; // `Map`, `WeakMap` and `Set` have no enumerable keys.

    var keys = Object(_allKeys_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj);
    if (Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(keys)) return false;

    for (var i = 0; i < length; i++) {
      if (!Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj[methods[i]])) return false;
    } // If we are testing against `WeakMap`, we need to ensure that
    // `obj` doesn't have a `forEach` method in order to distinguish
    // it from a regular `Map`.


    return methods !== weakMapMethods || !Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj[forEachName]);
  };
} // In the interest of compact minification, we write
// each string in the fingerprints only once.

var forEachName = 'forEach',
    hasName = 'has',
    commonInit = ['clear', 'delete'],
    mapTail = ['get', hasName, 'set']; // `Map`, `WeakMap` and `Set` each have slightly different
// combinations of the above sublists.

var mapMethods = commonInit.concat(forEachName, mapTail),
    weakMapMethods = commonInit.concat(mapTail),
    setMethods = ['add'].concat(commonInit, forEachName, hasName);

/***/ }),

/***/ "./node_modules/underscore/modules/_optimizeCb.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/_optimizeCb.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return optimizeCb; });
// Internal function that returns an efficient (for current engines) version
// of the passed-in callback, to be repeatedly applied in other Underscore
// functions.
function optimizeCb(func, context, argCount) {
  if (context === void 0) return func;

  switch (argCount == null ? 3 : argCount) {
    case 1:
      return function (value) {
        return func.call(context, value);
      };
    // The 2-argument case is omitted because we’re not using it.

    case 3:
      return function (value, index, collection) {
        return func.call(context, value, index, collection);
      };

    case 4:
      return function (accumulator, value, index, collection) {
        return func.call(context, accumulator, value, index, collection);
      };
  }

  return function () {
    return func.apply(context, arguments);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_setup.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/_setup.js ***!
  \***************************************************/
/*! exports provided: VERSION, root, ArrayProto, ObjProto, SymbolProto, push, slice, toString, hasOwnProperty, supportsArrayBuffer, supportsDataView, nativeIsArray, nativeKeys, nativeCreate, nativeIsView, _isNaN, _isFinite, hasEnumBug, nonEnumerableProps, MAX_ARRAY_INDEX */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "VERSION", function() { return VERSION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "root", function() { return root; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ArrayProto", function() { return ArrayProto; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ObjProto", function() { return ObjProto; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SymbolProto", function() { return SymbolProto; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "push", function() { return push; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "slice", function() { return slice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toString", function() { return toString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "hasOwnProperty", function() { return hasOwnProperty; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "supportsArrayBuffer", function() { return supportsArrayBuffer; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "supportsDataView", function() { return supportsDataView; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "nativeIsArray", function() { return nativeIsArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "nativeKeys", function() { return nativeKeys; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "nativeCreate", function() { return nativeCreate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "nativeIsView", function() { return nativeIsView; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_isNaN", function() { return _isNaN; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_isFinite", function() { return _isFinite; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "hasEnumBug", function() { return hasEnumBug; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "nonEnumerableProps", function() { return nonEnumerableProps; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MAX_ARRAY_INDEX", function() { return MAX_ARRAY_INDEX; });
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

// Current version.
var VERSION = '1.13.1'; // Establish the root object, `window` (`self`) in the browser, `global`
// on the server, or `this` in some virtual machines. We use `self`
// instead of `window` for `WebWorker` support.

var root = (typeof self === "undefined" ? "undefined" : _typeof(self)) == 'object' && self.self === self && self || (typeof global === "undefined" ? "undefined" : _typeof(global)) == 'object' && global.global === global && global || Function('return this')() || {}; // Save bytes in the minified (but not gzipped) version:

var ArrayProto = Array.prototype,
    ObjProto = Object.prototype;
var SymbolProto = typeof Symbol !== 'undefined' ? Symbol.prototype : null; // Create quick reference variables for speed access to core prototypes.

var push = ArrayProto.push,
    slice = ArrayProto.slice,
    toString = ObjProto.toString,
    hasOwnProperty = ObjProto.hasOwnProperty; // Modern feature detection.

var supportsArrayBuffer = typeof ArrayBuffer !== 'undefined',
    supportsDataView = typeof DataView !== 'undefined'; // All **ECMAScript 5+** native function implementations that we hope to use
// are declared here.

var nativeIsArray = Array.isArray,
    nativeKeys = Object.keys,
    nativeCreate = Object.create,
    nativeIsView = supportsArrayBuffer && ArrayBuffer.isView; // Create references to these builtin functions because we override them.

var _isNaN = isNaN,
    _isFinite = isFinite; // Keys in IE < 9 that won't be iterated by `for key in ...` and thus missed.

var hasEnumBug = !{
  toString: null
}.propertyIsEnumerable('toString');
var nonEnumerableProps = ['valueOf', 'isPrototypeOf', 'toString', 'propertyIsEnumerable', 'hasOwnProperty', 'toLocaleString']; // The largest integer that can be represented exactly.

var MAX_ARRAY_INDEX = Math.pow(2, 53) - 1;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/underscore/modules/_shallowProperty.js":
/*!*************************************************************!*\
  !*** ./node_modules/underscore/modules/_shallowProperty.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return shallowProperty; });
// Internal helper to generate a function to obtain property `key` from `obj`.
function shallowProperty(key) {
  return function (obj) {
    return obj == null ? void 0 : obj[key];
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_stringTagBug.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_stringTagBug.js ***!
  \**********************************************************/
/*! exports provided: hasStringTagBug, isIE11 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "hasStringTagBug", function() { return hasStringTagBug; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isIE11", function() { return isIE11; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _hasObjectTag_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_hasObjectTag.js */ "./node_modules/underscore/modules/_hasObjectTag.js");

 // In IE 10 - Edge 13, `DataView` has string tag `'[object Object]'`.
// In IE 11, the most common among them, this problem also applies to
// `Map`, `WeakMap` and `Set`.

var hasStringTagBug = _setup_js__WEBPACK_IMPORTED_MODULE_0__["supportsDataView"] && Object(_hasObjectTag_js__WEBPACK_IMPORTED_MODULE_1__["default"])(new DataView(new ArrayBuffer(8))),
    isIE11 = typeof Map !== 'undefined' && Object(_hasObjectTag_js__WEBPACK_IMPORTED_MODULE_1__["default"])(new Map());

/***/ }),

/***/ "./node_modules/underscore/modules/_tagTester.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/_tagTester.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return tagTester; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Internal function for creating a `toString`-based type tester.

function tagTester(name) {
  var tag = '[object ' + name + ']';
  return function (obj) {
    return _setup_js__WEBPACK_IMPORTED_MODULE_0__["toString"].call(obj) === tag;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/_toBufferView.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/_toBufferView.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return toBufferView; });
/* harmony import */ var _getByteLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getByteLength.js */ "./node_modules/underscore/modules/_getByteLength.js");
 // Internal function to wrap or shallow-copy an ArrayBuffer,
// typed array or DataView to a new view, reusing the buffer.

function toBufferView(bufferSource) {
  return new Uint8Array(bufferSource.buffer || bufferSource, bufferSource.byteOffset || 0, Object(_getByteLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(bufferSource));
}

/***/ }),

/***/ "./node_modules/underscore/modules/_toPath.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/_toPath.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return toPath; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./toPath.js */ "./node_modules/underscore/modules/toPath.js");

 // Internal wrapper for `_.toPath` to enable minification.
// Similar to `cb` for `_.iteratee`.

function toPath(path) {
  return _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].toPath(path);
}

/***/ }),

/***/ "./node_modules/underscore/modules/_unescapeMap.js":
/*!*********************************************************!*\
  !*** ./node_modules/underscore/modules/_unescapeMap.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _invert_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./invert.js */ "./node_modules/underscore/modules/invert.js");
/* harmony import */ var _escapeMap_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_escapeMap.js */ "./node_modules/underscore/modules/_escapeMap.js");

 // Internal list of HTML entities for unescaping.

/* harmony default export */ __webpack_exports__["default"] = (Object(_invert_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_escapeMap_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/after.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/after.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return after; });
// Returns a function that will only be executed on and after the Nth call.
function after(times, func) {
  return function () {
    if (--times < 1) {
      return func.apply(this, arguments);
    }
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/allKeys.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/allKeys.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return allKeys; });
/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _collectNonEnumProps_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_collectNonEnumProps.js */ "./node_modules/underscore/modules/_collectNonEnumProps.js");


 // Retrieve all the enumerable property names of an object.

function allKeys(obj) {
  if (!Object(_isObject_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj)) return [];
  var keys = [];

  for (var key in obj) {
    keys.push(key);
  } // Ahem, IE < 9.


  if (_setup_js__WEBPACK_IMPORTED_MODULE_1__["hasEnumBug"]) Object(_collectNonEnumProps_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj, keys);
  return keys;
}

/***/ }),

/***/ "./node_modules/underscore/modules/before.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/before.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return before; });
// Returns a function that will only be executed up to (but not including) the
// Nth call.
function before(times, func) {
  var memo;
  return function () {
    if (--times > 0) {
      memo = func.apply(this, arguments);
    }

    if (times <= 1) func = null;
    return memo;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/bind.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/bind.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _executeBound_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_executeBound.js */ "./node_modules/underscore/modules/_executeBound.js");


 // Create a function bound to a given object (assigning `this`, and arguments,
// optionally).

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (func, context, args) {
  if (!Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(func)) throw new TypeError('Bind must be called on a function');
  var bound = Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (callArgs) {
    return Object(_executeBound_js__WEBPACK_IMPORTED_MODULE_2__["default"])(func, bound, context, this, args.concat(callArgs));
  });
  return bound;
}));

/***/ }),

/***/ "./node_modules/underscore/modules/bindAll.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/bindAll.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_flatten.js */ "./node_modules/underscore/modules/_flatten.js");
/* harmony import */ var _bind_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./bind.js */ "./node_modules/underscore/modules/bind.js");


 // Bind a number of an object's methods to that object. Remaining arguments
// are the method names to be bound. Useful for ensuring that all callbacks
// defined on an object belong to it.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (obj, keys) {
  keys = Object(_flatten_js__WEBPACK_IMPORTED_MODULE_1__["default"])(keys, false, false);
  var index = keys.length;
  if (index < 1) throw new Error('bindAll must be passed function names');

  while (index--) {
    var key = keys[index];
    obj[key] = Object(_bind_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj[key], obj);
  }

  return obj;
}));

/***/ }),

/***/ "./node_modules/underscore/modules/chain.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/chain.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return chain; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
 // Start chaining a wrapped Underscore object.

function chain(obj) {
  var instance = Object(_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj);

  instance._chain = true;
  return instance;
}

/***/ }),

/***/ "./node_modules/underscore/modules/chunk.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/chunk.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return chunk; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Chunk a single array into multiple arrays, each containing `count` or fewer
// items.

function chunk(array, count) {
  if (count == null || count < 1) return [];
  var result = [];
  var i = 0,
      length = array.length;

  while (i < length) {
    result.push(_setup_js__WEBPACK_IMPORTED_MODULE_0__["slice"].call(array, i, i += count));
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/clone.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/clone.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return clone; });
/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");
/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");
/* harmony import */ var _extend_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./extend.js */ "./node_modules/underscore/modules/extend.js");


 // Create a (shallow-cloned) duplicate of an object.

function clone(obj) {
  if (!Object(_isObject_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj)) return obj;
  return Object(_isArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) ? obj.slice() : Object(_extend_js__WEBPACK_IMPORTED_MODULE_2__["default"])({}, obj);
}

/***/ }),

/***/ "./node_modules/underscore/modules/compact.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/compact.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return compact; });
/* harmony import */ var _filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./filter.js */ "./node_modules/underscore/modules/filter.js");
 // Trim out all falsy values from an array.

function compact(array) {
  return Object(_filter_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array, Boolean);
}

/***/ }),

/***/ "./node_modules/underscore/modules/compose.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/compose.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return compose; });
// Returns a function that is the composition of a list of functions, each
// consuming the return value of the function that follows.
function compose() {
  var args = arguments;
  var start = args.length - 1;
  return function () {
    var i = start;
    var result = args[start].apply(this, arguments);

    while (i--) {
      result = args[i].call(this, result);
    }

    return result;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/constant.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/constant.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return constant; });
// Predicate-generating function. Often useful outside of Underscore.
function constant(value) {
  return function () {
    return value;
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/contains.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/contains.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return contains; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _values_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./values.js */ "./node_modules/underscore/modules/values.js");
/* harmony import */ var _indexOf_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./indexOf.js */ "./node_modules/underscore/modules/indexOf.js");


 // Determine if the array or object contains a given item (using `===`).

function contains(obj, item, fromIndex, guard) {
  if (!Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj)) obj = Object(_values_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj);
  if (typeof fromIndex != 'number' || guard) fromIndex = 0;
  return Object(_indexOf_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj, item, fromIndex) >= 0;
}

/***/ }),

/***/ "./node_modules/underscore/modules/countBy.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/countBy.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _group_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_group.js */ "./node_modules/underscore/modules/_group.js");
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");

 // Counts instances of an object that group by a certain criterion. Pass
// either a string attribute to count by, or a function that returns the
// criterion.

/* harmony default export */ __webpack_exports__["default"] = (Object(_group_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (result, value, key) {
  if (Object(_has_js__WEBPACK_IMPORTED_MODULE_1__["default"])(result, key)) result[key]++;else result[key] = 1;
}));

/***/ }),

/***/ "./node_modules/underscore/modules/create.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/create.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return create; });
/* harmony import */ var _baseCreate_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_baseCreate.js */ "./node_modules/underscore/modules/_baseCreate.js");
/* harmony import */ var _extendOwn_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./extendOwn.js */ "./node_modules/underscore/modules/extendOwn.js");

 // Creates an object that inherits from the given prototype object.
// If additional properties are provided then they will be added to the
// created object.

function create(prototype, props) {
  var result = Object(_baseCreate_js__WEBPACK_IMPORTED_MODULE_0__["default"])(prototype);
  if (props) Object(_extendOwn_js__WEBPACK_IMPORTED_MODULE_1__["default"])(result, props);
  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/debounce.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/debounce.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return debounce; });
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _now_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./now.js */ "./node_modules/underscore/modules/now.js");

 // When a sequence of calls of the returned function ends, the argument
// function is triggered. The end of a sequence is defined by the `wait`
// parameter. If `immediate` is passed, the argument function will be
// triggered at the beginning of the sequence instead of at the end.

function debounce(func, wait, immediate) {
  var timeout, previous, args, result, context;

  var later = function later() {
    var passed = Object(_now_js__WEBPACK_IMPORTED_MODULE_1__["default"])() - previous;

    if (wait > passed) {
      timeout = setTimeout(later, wait - passed);
    } else {
      timeout = null;
      if (!immediate) result = func.apply(context, args); // This check is needed because `func` can recursively invoke `debounced`.

      if (!timeout) args = context = null;
    }
  };

  var debounced = Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (_args) {
    context = this;
    args = _args;
    previous = Object(_now_js__WEBPACK_IMPORTED_MODULE_1__["default"])();

    if (!timeout) {
      timeout = setTimeout(later, wait);
      if (immediate) result = func.apply(context, args);
    }

    return result;
  });

  debounced.cancel = function () {
    clearTimeout(timeout);
    timeout = args = context = null;
  };

  return debounced;
}

/***/ }),

/***/ "./node_modules/underscore/modules/defaults.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/defaults.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createAssigner_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createAssigner.js */ "./node_modules/underscore/modules/_createAssigner.js");
/* harmony import */ var _allKeys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./allKeys.js */ "./node_modules/underscore/modules/allKeys.js");

 // Fill in a given object with default properties.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createAssigner_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_allKeys_js__WEBPACK_IMPORTED_MODULE_1__["default"], true));

/***/ }),

/***/ "./node_modules/underscore/modules/defer.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/defer.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _partial_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./partial.js */ "./node_modules/underscore/modules/partial.js");
/* harmony import */ var _delay_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./delay.js */ "./node_modules/underscore/modules/delay.js");
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");


 // Defers a function, scheduling it to run after the current call stack has
// cleared.

/* harmony default export */ __webpack_exports__["default"] = (Object(_partial_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_delay_js__WEBPACK_IMPORTED_MODULE_1__["default"], _underscore_js__WEBPACK_IMPORTED_MODULE_2__["default"], 1));

/***/ }),

/***/ "./node_modules/underscore/modules/delay.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/delay.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
 // Delays a function for the given number of milliseconds, and then calls
// it with the arguments supplied.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (func, wait, args) {
  return setTimeout(function () {
    return func.apply(null, args);
  }, wait);
}));

/***/ }),

/***/ "./node_modules/underscore/modules/difference.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/difference.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_flatten.js */ "./node_modules/underscore/modules/_flatten.js");
/* harmony import */ var _filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./filter.js */ "./node_modules/underscore/modules/filter.js");
/* harmony import */ var _contains_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./contains.js */ "./node_modules/underscore/modules/contains.js");



 // Take the difference between one array and a number of other arrays.
// Only the elements present in just the first array will remain.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (array, rest) {
  rest = Object(_flatten_js__WEBPACK_IMPORTED_MODULE_1__["default"])(rest, true, true);
  return Object(_filter_js__WEBPACK_IMPORTED_MODULE_2__["default"])(array, function (value) {
    return !Object(_contains_js__WEBPACK_IMPORTED_MODULE_3__["default"])(rest, value);
  });
}));

/***/ }),

/***/ "./node_modules/underscore/modules/each.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/each.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return each; });
/* harmony import */ var _optimizeCb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_optimizeCb.js */ "./node_modules/underscore/modules/_optimizeCb.js");
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");


 // The cornerstone for collection functions, an `each`
// implementation, aka `forEach`.
// Handles raw objects in addition to array-likes. Treats all
// sparse array-likes as if they were dense.

function each(obj, iteratee, context) {
  iteratee = Object(_optimizeCb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context);
  var i, length;

  if (Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj)) {
    for (i = 0, length = obj.length; i < length; i++) {
      iteratee(obj[i], i, obj);
    }
  } else {
    var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj);

    for (i = 0, length = _keys.length; i < length; i++) {
      iteratee(obj[_keys[i]], _keys[i], obj);
    }
  }

  return obj;
}

/***/ }),

/***/ "./node_modules/underscore/modules/escape.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/escape.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createEscaper_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createEscaper.js */ "./node_modules/underscore/modules/_createEscaper.js");
/* harmony import */ var _escapeMap_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_escapeMap.js */ "./node_modules/underscore/modules/_escapeMap.js");

 // Function for escaping strings to HTML interpolation.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createEscaper_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_escapeMap_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/every.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/every.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return every; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");


 // Determine whether all of the elements pass a truth test.

function every(obj, predicate, context) {
  predicate = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(predicate, context);

  var _keys = !Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) && Object(_keys_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj),
      length = (_keys || obj).length;

  for (var index = 0; index < length; index++) {
    var currentKey = _keys ? _keys[index] : index;
    if (!predicate(obj[currentKey], currentKey, obj)) return false;
  }

  return true;
}

/***/ }),

/***/ "./node_modules/underscore/modules/extend.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/extend.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createAssigner_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createAssigner.js */ "./node_modules/underscore/modules/_createAssigner.js");
/* harmony import */ var _allKeys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./allKeys.js */ "./node_modules/underscore/modules/allKeys.js");

 // Extend a given object with all the properties in passed-in object(s).

/* harmony default export */ __webpack_exports__["default"] = (Object(_createAssigner_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_allKeys_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/extendOwn.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/extendOwn.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createAssigner_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createAssigner.js */ "./node_modules/underscore/modules/_createAssigner.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");

 // Assigns a given object with all the own properties in the passed-in
// object(s).
// (https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Object/assign)

/* harmony default export */ __webpack_exports__["default"] = (Object(_createAssigner_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_keys_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/filter.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/filter.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return filter; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");

 // Return all the elements that pass a truth test.

function filter(obj, predicate, context) {
  var results = [];
  predicate = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(predicate, context);
  Object(_each_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj, function (value, index, list) {
    if (predicate(value, index, list)) results.push(value);
  });
  return results;
}

/***/ }),

/***/ "./node_modules/underscore/modules/find.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/find.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return find; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _findIndex_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./findIndex.js */ "./node_modules/underscore/modules/findIndex.js");
/* harmony import */ var _findKey_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./findKey.js */ "./node_modules/underscore/modules/findKey.js");


 // Return the first value which passes a truth test.

function find(obj, predicate, context) {
  var keyFinder = Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj) ? _findIndex_js__WEBPACK_IMPORTED_MODULE_1__["default"] : _findKey_js__WEBPACK_IMPORTED_MODULE_2__["default"];
  var key = keyFinder(obj, predicate, context);
  if (key !== void 0 && key !== -1) return obj[key];
}

/***/ }),

/***/ "./node_modules/underscore/modules/findIndex.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/findIndex.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createPredicateIndexFinder_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createPredicateIndexFinder.js */ "./node_modules/underscore/modules/_createPredicateIndexFinder.js");
 // Returns the first index on an array-like that passes a truth test.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createPredicateIndexFinder_js__WEBPACK_IMPORTED_MODULE_0__["default"])(1));

/***/ }),

/***/ "./node_modules/underscore/modules/findKey.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/findKey.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return findKey; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");

 // Returns the first key on an object that passes a truth test.

function findKey(obj, predicate, context) {
  predicate = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(predicate, context);

  var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj),
      key;

  for (var i = 0, length = _keys.length; i < length; i++) {
    key = _keys[i];
    if (predicate(obj[key], key, obj)) return key;
  }
}

/***/ }),

/***/ "./node_modules/underscore/modules/findLastIndex.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/findLastIndex.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createPredicateIndexFinder_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createPredicateIndexFinder.js */ "./node_modules/underscore/modules/_createPredicateIndexFinder.js");
 // Returns the last index on an array-like that passes a truth test.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createPredicateIndexFinder_js__WEBPACK_IMPORTED_MODULE_0__["default"])(-1));

/***/ }),

/***/ "./node_modules/underscore/modules/findWhere.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/findWhere.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return findWhere; });
/* harmony import */ var _find_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./find.js */ "./node_modules/underscore/modules/find.js");
/* harmony import */ var _matcher_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./matcher.js */ "./node_modules/underscore/modules/matcher.js");

 // Convenience version of a common use case of `_.find`: getting the first
// object containing specific `key:value` pairs.

function findWhere(obj, attrs) {
  return Object(_find_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, Object(_matcher_js__WEBPACK_IMPORTED_MODULE_1__["default"])(attrs));
}

/***/ }),

/***/ "./node_modules/underscore/modules/first.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/first.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return first; });
/* harmony import */ var _initial_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./initial.js */ "./node_modules/underscore/modules/initial.js");
 // Get the first element of an array. Passing **n** will return the first N
// values in the array. The **guard** check allows it to work with `_.map`.

function first(array, n, guard) {
  if (array == null || array.length < 1) return n == null || guard ? void 0 : [];
  if (n == null || guard) return array[0];
  return Object(_initial_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array, array.length - n);
}

/***/ }),

/***/ "./node_modules/underscore/modules/flatten.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/flatten.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return flatten; });
/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_flatten.js */ "./node_modules/underscore/modules/_flatten.js");
 // Flatten out an array, either recursively (by default), or up to `depth`.
// Passing `true` or `false` as `depth` means `1` or `Infinity`, respectively.

function flatten(array, depth) {
  return Object(_flatten_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array, depth, false);
}

/***/ }),

/***/ "./node_modules/underscore/modules/functions.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/functions.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return functions; });
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
 // Return a sorted list of the function names available on the object.

function functions(obj) {
  var names = [];

  for (var key in obj) {
    if (Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj[key])) names.push(key);
  }

  return names.sort();
}

/***/ }),

/***/ "./node_modules/underscore/modules/get.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/get.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return get; });
/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_toPath.js */ "./node_modules/underscore/modules/_toPath.js");
/* harmony import */ var _deepGet_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_deepGet.js */ "./node_modules/underscore/modules/_deepGet.js");
/* harmony import */ var _isUndefined_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isUndefined.js */ "./node_modules/underscore/modules/isUndefined.js");


 // Get the value of the (deep) property on `path` from `object`.
// If any property in `path` does not exist or if the value is
// `undefined`, return `defaultValue` instead.
// The `path` is normalized through `_.toPath`.

function get(object, path, defaultValue) {
  var value = Object(_deepGet_js__WEBPACK_IMPORTED_MODULE_1__["default"])(object, Object(_toPath_js__WEBPACK_IMPORTED_MODULE_0__["default"])(path));
  return Object(_isUndefined_js__WEBPACK_IMPORTED_MODULE_2__["default"])(value) ? defaultValue : value;
}

/***/ }),

/***/ "./node_modules/underscore/modules/groupBy.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/groupBy.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _group_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_group.js */ "./node_modules/underscore/modules/_group.js");
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");

 // Groups the object's values by a criterion. Pass either a string attribute
// to group by, or a function that returns the criterion.

/* harmony default export */ __webpack_exports__["default"] = (Object(_group_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (result, value, key) {
  if (Object(_has_js__WEBPACK_IMPORTED_MODULE_1__["default"])(result, key)) result[key].push(value);else result[key] = [value];
}));

/***/ }),

/***/ "./node_modules/underscore/modules/has.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/has.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return has; });
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");
/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_toPath.js */ "./node_modules/underscore/modules/_toPath.js");

 // Shortcut function for checking if an object has a given property directly on
// itself (in other words, not on a prototype). Unlike the internal `has`
// function, this public version can also traverse nested properties.

function has(obj, path) {
  path = Object(_toPath_js__WEBPACK_IMPORTED_MODULE_1__["default"])(path);
  var length = path.length;

  for (var i = 0; i < length; i++) {
    var key = path[i];
    if (!Object(_has_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, key)) return false;
    obj = obj[key];
  }

  return !!length;
}

/***/ }),

/***/ "./node_modules/underscore/modules/identity.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/identity.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return identity; });
// Keep the identity function around for default iteratees.
function identity(value) {
  return value;
}

/***/ }),

/***/ "./node_modules/underscore/modules/index-all.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/index-all.js ***!
  \******************************************************/
/*! exports provided: default, VERSION, restArguments, isObject, isNull, isUndefined, isBoolean, isElement, isString, isNumber, isDate, isRegExp, isError, isSymbol, isArrayBuffer, isDataView, isArray, isFunction, isArguments, isFinite, isNaN, isTypedArray, isEmpty, isMatch, isEqual, isMap, isWeakMap, isSet, isWeakSet, keys, allKeys, values, pairs, invert, functions, methods, extend, extendOwn, assign, defaults, create, clone, tap, get, has, mapObject, identity, constant, noop, toPath, property, propertyOf, matcher, matches, times, random, now, escape, unescape, templateSettings, template, result, uniqueId, chain, iteratee, partial, bind, bindAll, memoize, delay, defer, throttle, debounce, wrap, negate, compose, after, before, once, findKey, findIndex, findLastIndex, sortedIndex, indexOf, lastIndexOf, find, detect, findWhere, each, forEach, map, collect, reduce, foldl, inject, reduceRight, foldr, filter, select, reject, every, all, some, any, contains, includes, include, invoke, pluck, where, max, min, shuffle, sample, sortBy, groupBy, indexBy, countBy, partition, toArray, size, pick, omit, first, head, take, initial, last, rest, tail, drop, compact, flatten, without, uniq, unique, union, intersection, difference, unzip, transpose, zip, object, range, chunk, mixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_default_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index-default.js */ "./node_modules/underscore/modules/index-default.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _index_default_js__WEBPACK_IMPORTED_MODULE_0__["default"]; });

/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.js */ "./node_modules/underscore/modules/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "VERSION", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["VERSION"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "restArguments", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["restArguments"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isObject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNull", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isNull"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isUndefined", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isUndefined"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isBoolean", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isBoolean"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isElement", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isElement"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isString", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isString"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNumber", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isNumber"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isDate", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isDate"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isRegExp", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isRegExp"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isError", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isError"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isSymbol", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isSymbol"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArrayBuffer", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isArrayBuffer"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isDataView", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isDataView"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArray", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isArray"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isFunction", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isFunction"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArguments", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isArguments"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isFinite", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isFinite"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNaN", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isNaN"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isTypedArray", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isTypedArray"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isEmpty"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isMatch", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isMatch"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEqual", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isEqual"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isMap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isMap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isWeakMap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isWeakMap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isSet", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isSet"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isWeakSet", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isWeakSet"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "keys", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["keys"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "allKeys", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["allKeys"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "values", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["values"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pairs", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["pairs"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "invert", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["invert"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "functions", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["functions"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "methods", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["methods"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "extend", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["extend"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "extendOwn", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["extendOwn"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "assign", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["assign"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "defaults", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["defaults"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "create", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["create"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "clone", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["clone"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "tap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["tap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "get", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["get"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "has", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["has"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "mapObject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["mapObject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "identity", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["identity"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "constant", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["constant"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "noop", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["noop"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toPath", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["toPath"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "property", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["property"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "propertyOf", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["propertyOf"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "matcher", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["matcher"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "matches", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["matches"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "times", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["times"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "random", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["random"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "now", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["now"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "escape", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["escape"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unescape", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["unescape"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "templateSettings", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["templateSettings"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "template", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["template"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "result", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["result"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "uniqueId", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["uniqueId"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "chain", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["chain"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "iteratee", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["iteratee"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "partial", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["partial"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "bind", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["bind"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "bindAll", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["bindAll"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "memoize", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["memoize"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "delay", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["delay"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "defer", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["defer"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["throttle"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["debounce"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "wrap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["wrap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "negate", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["negate"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "compose", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["compose"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "after", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["after"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "before", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["before"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "once", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["once"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findKey", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findKey"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findIndex", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findIndex"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findLastIndex", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findLastIndex"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sortedIndex", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["sortedIndex"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "indexOf", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["indexOf"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "lastIndexOf", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["lastIndexOf"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "find", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["find"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "detect", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["detect"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findWhere", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findWhere"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "each", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["each"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "forEach", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["forEach"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "map", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["map"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "collect", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["collect"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reduce", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["reduce"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "foldl", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["foldl"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "inject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["inject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reduceRight", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["reduceRight"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "foldr", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["foldr"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "filter", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["filter"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "select", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["select"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["reject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "every", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["every"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "all", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["all"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "some", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["some"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "any", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["any"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "contains", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["contains"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "includes", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["includes"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "include", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["include"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "invoke", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["invoke"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pluck", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["pluck"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "where", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["where"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "max", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["max"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "min", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["min"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "shuffle", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["shuffle"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sample", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["sample"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sortBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["sortBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "groupBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["groupBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "indexBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["indexBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "countBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["countBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "partition", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["partition"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toArray", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["toArray"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "size", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["size"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pick", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["pick"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "omit", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["omit"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "first", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["first"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "head", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["head"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "take", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["take"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "initial", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["initial"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "last", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["last"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "rest", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["rest"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "tail", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["tail"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "drop", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["drop"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "compact", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["compact"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "flatten", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["flatten"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "without", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["without"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "uniq", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["uniq"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unique", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["unique"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "union", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["union"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "intersection", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["intersection"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "difference", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["difference"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unzip", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["unzip"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "transpose", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["transpose"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "zip", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["zip"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "object", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["object"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "range", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["range"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "chunk", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["chunk"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "mixin", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["mixin"]; });

// ESM Exports
// ===========
// This module is the package entry point for ES module users. In other words,
// it is the module they are interfacing with when they import from the whole
// package instead of from a submodule, like this:
//
// ```js
// import { map } from 'underscore';
// ```
//
// The difference with `./index-default`, which is the package entry point for
// CommonJS, AMD and UMD users, is purely technical. In ES modules, named and
// default exports are considered to be siblings, so when you have a default
// export, its properties are not automatically available as named exports. For
// this reason, we re-export the named exports in addition to providing the same
// default export as in `./index-default`.



/***/ }),

/***/ "./node_modules/underscore/modules/index-default.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/index-default.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.js */ "./node_modules/underscore/modules/index.js");
// Default Export
// ==============
// In this module, we mix our bundled exports into the `_` object and export
// the result. This is analogous to setting `module.exports = _` in CommonJS.
// Hence, this module is also the entry point of our UMD bundle and the package
// entry point for CommonJS and AMD users. In other words, this is (the source
// of) the module you are interfacing with when you do any of the following:
//
// ```js
// // CommonJS
// var _ = require('underscore');
//
// // AMD
// define(['underscore'], function(_) {...});
//
// // UMD in the browser
// // _ is available as a global variable
// ```

 // Add all of the Underscore functions to the wrapper object.

var _ = Object(_index_js__WEBPACK_IMPORTED_MODULE_0__["mixin"])(_index_js__WEBPACK_IMPORTED_MODULE_0__); // Legacy Node.js API.


_._ = _; // Export the Underscore API.

/* harmony default export */ __webpack_exports__["default"] = (_);

/***/ }),

/***/ "./node_modules/underscore/modules/index.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/index.js ***!
  \**************************************************/
/*! exports provided: VERSION, restArguments, isObject, isNull, isUndefined, isBoolean, isElement, isString, isNumber, isDate, isRegExp, isError, isSymbol, isArrayBuffer, isDataView, isArray, isFunction, isArguments, isFinite, isNaN, isTypedArray, isEmpty, isMatch, isEqual, isMap, isWeakMap, isSet, isWeakSet, keys, allKeys, values, pairs, invert, functions, methods, extend, extendOwn, assign, defaults, create, clone, tap, get, has, mapObject, identity, constant, noop, toPath, property, propertyOf, matcher, matches, times, random, now, escape, unescape, templateSettings, template, result, uniqueId, chain, iteratee, partial, bind, bindAll, memoize, delay, defer, throttle, debounce, wrap, negate, compose, after, before, once, findKey, findIndex, findLastIndex, sortedIndex, indexOf, lastIndexOf, find, detect, findWhere, each, forEach, map, collect, reduce, foldl, inject, reduceRight, foldr, filter, select, reject, every, all, some, any, contains, includes, include, invoke, pluck, where, max, min, shuffle, sample, sortBy, groupBy, indexBy, countBy, partition, toArray, size, pick, omit, first, head, take, initial, last, rest, tail, drop, compact, flatten, without, uniq, unique, union, intersection, difference, unzip, transpose, zip, object, range, chunk, mixin, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "VERSION", function() { return _setup_js__WEBPACK_IMPORTED_MODULE_0__["VERSION"]; });

/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "restArguments", function() { return _restArguments_js__WEBPACK_IMPORTED_MODULE_1__["default"]; });

/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return _isObject_js__WEBPACK_IMPORTED_MODULE_2__["default"]; });

/* harmony import */ var _isNull_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./isNull.js */ "./node_modules/underscore/modules/isNull.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNull", function() { return _isNull_js__WEBPACK_IMPORTED_MODULE_3__["default"]; });

/* harmony import */ var _isUndefined_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./isUndefined.js */ "./node_modules/underscore/modules/isUndefined.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isUndefined", function() { return _isUndefined_js__WEBPACK_IMPORTED_MODULE_4__["default"]; });

/* harmony import */ var _isBoolean_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./isBoolean.js */ "./node_modules/underscore/modules/isBoolean.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isBoolean", function() { return _isBoolean_js__WEBPACK_IMPORTED_MODULE_5__["default"]; });

/* harmony import */ var _isElement_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./isElement.js */ "./node_modules/underscore/modules/isElement.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isElement", function() { return _isElement_js__WEBPACK_IMPORTED_MODULE_6__["default"]; });

/* harmony import */ var _isString_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./isString.js */ "./node_modules/underscore/modules/isString.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isString", function() { return _isString_js__WEBPACK_IMPORTED_MODULE_7__["default"]; });

/* harmony import */ var _isNumber_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./isNumber.js */ "./node_modules/underscore/modules/isNumber.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNumber", function() { return _isNumber_js__WEBPACK_IMPORTED_MODULE_8__["default"]; });

/* harmony import */ var _isDate_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./isDate.js */ "./node_modules/underscore/modules/isDate.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isDate", function() { return _isDate_js__WEBPACK_IMPORTED_MODULE_9__["default"]; });

/* harmony import */ var _isRegExp_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./isRegExp.js */ "./node_modules/underscore/modules/isRegExp.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isRegExp", function() { return _isRegExp_js__WEBPACK_IMPORTED_MODULE_10__["default"]; });

/* harmony import */ var _isError_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./isError.js */ "./node_modules/underscore/modules/isError.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isError", function() { return _isError_js__WEBPACK_IMPORTED_MODULE_11__["default"]; });

/* harmony import */ var _isSymbol_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./isSymbol.js */ "./node_modules/underscore/modules/isSymbol.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isSymbol", function() { return _isSymbol_js__WEBPACK_IMPORTED_MODULE_12__["default"]; });

/* harmony import */ var _isArrayBuffer_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./isArrayBuffer.js */ "./node_modules/underscore/modules/isArrayBuffer.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArrayBuffer", function() { return _isArrayBuffer_js__WEBPACK_IMPORTED_MODULE_13__["default"]; });

/* harmony import */ var _isDataView_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./isDataView.js */ "./node_modules/underscore/modules/isDataView.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isDataView", function() { return _isDataView_js__WEBPACK_IMPORTED_MODULE_14__["default"]; });

/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArray", function() { return _isArray_js__WEBPACK_IMPORTED_MODULE_15__["default"]; });

/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isFunction", function() { return _isFunction_js__WEBPACK_IMPORTED_MODULE_16__["default"]; });

/* harmony import */ var _isArguments_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./isArguments.js */ "./node_modules/underscore/modules/isArguments.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArguments", function() { return _isArguments_js__WEBPACK_IMPORTED_MODULE_17__["default"]; });

/* harmony import */ var _isFinite_js__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./isFinite.js */ "./node_modules/underscore/modules/isFinite.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isFinite", function() { return _isFinite_js__WEBPACK_IMPORTED_MODULE_18__["default"]; });

/* harmony import */ var _isNaN_js__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./isNaN.js */ "./node_modules/underscore/modules/isNaN.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNaN", function() { return _isNaN_js__WEBPACK_IMPORTED_MODULE_19__["default"]; });

/* harmony import */ var _isTypedArray_js__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./isTypedArray.js */ "./node_modules/underscore/modules/isTypedArray.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isTypedArray", function() { return _isTypedArray_js__WEBPACK_IMPORTED_MODULE_20__["default"]; });

/* harmony import */ var _isEmpty_js__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./isEmpty.js */ "./node_modules/underscore/modules/isEmpty.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return _isEmpty_js__WEBPACK_IMPORTED_MODULE_21__["default"]; });

/* harmony import */ var _isMatch_js__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./isMatch.js */ "./node_modules/underscore/modules/isMatch.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isMatch", function() { return _isMatch_js__WEBPACK_IMPORTED_MODULE_22__["default"]; });

/* harmony import */ var _isEqual_js__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./isEqual.js */ "./node_modules/underscore/modules/isEqual.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEqual", function() { return _isEqual_js__WEBPACK_IMPORTED_MODULE_23__["default"]; });

/* harmony import */ var _isMap_js__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./isMap.js */ "./node_modules/underscore/modules/isMap.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isMap", function() { return _isMap_js__WEBPACK_IMPORTED_MODULE_24__["default"]; });

/* harmony import */ var _isWeakMap_js__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./isWeakMap.js */ "./node_modules/underscore/modules/isWeakMap.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isWeakMap", function() { return _isWeakMap_js__WEBPACK_IMPORTED_MODULE_25__["default"]; });

/* harmony import */ var _isSet_js__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./isSet.js */ "./node_modules/underscore/modules/isSet.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isSet", function() { return _isSet_js__WEBPACK_IMPORTED_MODULE_26__["default"]; });

/* harmony import */ var _isWeakSet_js__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./isWeakSet.js */ "./node_modules/underscore/modules/isWeakSet.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isWeakSet", function() { return _isWeakSet_js__WEBPACK_IMPORTED_MODULE_27__["default"]; });

/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "keys", function() { return _keys_js__WEBPACK_IMPORTED_MODULE_28__["default"]; });

/* harmony import */ var _allKeys_js__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./allKeys.js */ "./node_modules/underscore/modules/allKeys.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "allKeys", function() { return _allKeys_js__WEBPACK_IMPORTED_MODULE_29__["default"]; });

/* harmony import */ var _values_js__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./values.js */ "./node_modules/underscore/modules/values.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "values", function() { return _values_js__WEBPACK_IMPORTED_MODULE_30__["default"]; });

/* harmony import */ var _pairs_js__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./pairs.js */ "./node_modules/underscore/modules/pairs.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pairs", function() { return _pairs_js__WEBPACK_IMPORTED_MODULE_31__["default"]; });

/* harmony import */ var _invert_js__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./invert.js */ "./node_modules/underscore/modules/invert.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "invert", function() { return _invert_js__WEBPACK_IMPORTED_MODULE_32__["default"]; });

/* harmony import */ var _functions_js__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./functions.js */ "./node_modules/underscore/modules/functions.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "functions", function() { return _functions_js__WEBPACK_IMPORTED_MODULE_33__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "methods", function() { return _functions_js__WEBPACK_IMPORTED_MODULE_33__["default"]; });

/* harmony import */ var _extend_js__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./extend.js */ "./node_modules/underscore/modules/extend.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "extend", function() { return _extend_js__WEBPACK_IMPORTED_MODULE_34__["default"]; });

/* harmony import */ var _extendOwn_js__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./extendOwn.js */ "./node_modules/underscore/modules/extendOwn.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "extendOwn", function() { return _extendOwn_js__WEBPACK_IMPORTED_MODULE_35__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "assign", function() { return _extendOwn_js__WEBPACK_IMPORTED_MODULE_35__["default"]; });

/* harmony import */ var _defaults_js__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./defaults.js */ "./node_modules/underscore/modules/defaults.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "defaults", function() { return _defaults_js__WEBPACK_IMPORTED_MODULE_36__["default"]; });

/* harmony import */ var _create_js__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./create.js */ "./node_modules/underscore/modules/create.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "create", function() { return _create_js__WEBPACK_IMPORTED_MODULE_37__["default"]; });

/* harmony import */ var _clone_js__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./clone.js */ "./node_modules/underscore/modules/clone.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "clone", function() { return _clone_js__WEBPACK_IMPORTED_MODULE_38__["default"]; });

/* harmony import */ var _tap_js__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./tap.js */ "./node_modules/underscore/modules/tap.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "tap", function() { return _tap_js__WEBPACK_IMPORTED_MODULE_39__["default"]; });

/* harmony import */ var _get_js__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./get.js */ "./node_modules/underscore/modules/get.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "get", function() { return _get_js__WEBPACK_IMPORTED_MODULE_40__["default"]; });

/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./has.js */ "./node_modules/underscore/modules/has.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "has", function() { return _has_js__WEBPACK_IMPORTED_MODULE_41__["default"]; });

/* harmony import */ var _mapObject_js__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./mapObject.js */ "./node_modules/underscore/modules/mapObject.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "mapObject", function() { return _mapObject_js__WEBPACK_IMPORTED_MODULE_42__["default"]; });

/* harmony import */ var _identity_js__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./identity.js */ "./node_modules/underscore/modules/identity.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "identity", function() { return _identity_js__WEBPACK_IMPORTED_MODULE_43__["default"]; });

/* harmony import */ var _constant_js__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! ./constant.js */ "./node_modules/underscore/modules/constant.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "constant", function() { return _constant_js__WEBPACK_IMPORTED_MODULE_44__["default"]; });

/* harmony import */ var _noop_js__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! ./noop.js */ "./node_modules/underscore/modules/noop.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "noop", function() { return _noop_js__WEBPACK_IMPORTED_MODULE_45__["default"]; });

/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! ./toPath.js */ "./node_modules/underscore/modules/toPath.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toPath", function() { return _toPath_js__WEBPACK_IMPORTED_MODULE_46__["default"]; });

/* harmony import */ var _property_js__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! ./property.js */ "./node_modules/underscore/modules/property.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "property", function() { return _property_js__WEBPACK_IMPORTED_MODULE_47__["default"]; });

/* harmony import */ var _propertyOf_js__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! ./propertyOf.js */ "./node_modules/underscore/modules/propertyOf.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "propertyOf", function() { return _propertyOf_js__WEBPACK_IMPORTED_MODULE_48__["default"]; });

/* harmony import */ var _matcher_js__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! ./matcher.js */ "./node_modules/underscore/modules/matcher.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "matcher", function() { return _matcher_js__WEBPACK_IMPORTED_MODULE_49__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "matches", function() { return _matcher_js__WEBPACK_IMPORTED_MODULE_49__["default"]; });

/* harmony import */ var _times_js__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! ./times.js */ "./node_modules/underscore/modules/times.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "times", function() { return _times_js__WEBPACK_IMPORTED_MODULE_50__["default"]; });

/* harmony import */ var _random_js__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! ./random.js */ "./node_modules/underscore/modules/random.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "random", function() { return _random_js__WEBPACK_IMPORTED_MODULE_51__["default"]; });

/* harmony import */ var _now_js__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! ./now.js */ "./node_modules/underscore/modules/now.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "now", function() { return _now_js__WEBPACK_IMPORTED_MODULE_52__["default"]; });

/* harmony import */ var _escape_js__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! ./escape.js */ "./node_modules/underscore/modules/escape.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "escape", function() { return _escape_js__WEBPACK_IMPORTED_MODULE_53__["default"]; });

/* harmony import */ var _unescape_js__WEBPACK_IMPORTED_MODULE_54__ = __webpack_require__(/*! ./unescape.js */ "./node_modules/underscore/modules/unescape.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unescape", function() { return _unescape_js__WEBPACK_IMPORTED_MODULE_54__["default"]; });

/* harmony import */ var _templateSettings_js__WEBPACK_IMPORTED_MODULE_55__ = __webpack_require__(/*! ./templateSettings.js */ "./node_modules/underscore/modules/templateSettings.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "templateSettings", function() { return _templateSettings_js__WEBPACK_IMPORTED_MODULE_55__["default"]; });

/* harmony import */ var _template_js__WEBPACK_IMPORTED_MODULE_56__ = __webpack_require__(/*! ./template.js */ "./node_modules/underscore/modules/template.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "template", function() { return _template_js__WEBPACK_IMPORTED_MODULE_56__["default"]; });

/* harmony import */ var _result_js__WEBPACK_IMPORTED_MODULE_57__ = __webpack_require__(/*! ./result.js */ "./node_modules/underscore/modules/result.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "result", function() { return _result_js__WEBPACK_IMPORTED_MODULE_57__["default"]; });

/* harmony import */ var _uniqueId_js__WEBPACK_IMPORTED_MODULE_58__ = __webpack_require__(/*! ./uniqueId.js */ "./node_modules/underscore/modules/uniqueId.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "uniqueId", function() { return _uniqueId_js__WEBPACK_IMPORTED_MODULE_58__["default"]; });

/* harmony import */ var _chain_js__WEBPACK_IMPORTED_MODULE_59__ = __webpack_require__(/*! ./chain.js */ "./node_modules/underscore/modules/chain.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "chain", function() { return _chain_js__WEBPACK_IMPORTED_MODULE_59__["default"]; });

/* harmony import */ var _iteratee_js__WEBPACK_IMPORTED_MODULE_60__ = __webpack_require__(/*! ./iteratee.js */ "./node_modules/underscore/modules/iteratee.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "iteratee", function() { return _iteratee_js__WEBPACK_IMPORTED_MODULE_60__["default"]; });

/* harmony import */ var _partial_js__WEBPACK_IMPORTED_MODULE_61__ = __webpack_require__(/*! ./partial.js */ "./node_modules/underscore/modules/partial.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "partial", function() { return _partial_js__WEBPACK_IMPORTED_MODULE_61__["default"]; });

/* harmony import */ var _bind_js__WEBPACK_IMPORTED_MODULE_62__ = __webpack_require__(/*! ./bind.js */ "./node_modules/underscore/modules/bind.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "bind", function() { return _bind_js__WEBPACK_IMPORTED_MODULE_62__["default"]; });

/* harmony import */ var _bindAll_js__WEBPACK_IMPORTED_MODULE_63__ = __webpack_require__(/*! ./bindAll.js */ "./node_modules/underscore/modules/bindAll.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "bindAll", function() { return _bindAll_js__WEBPACK_IMPORTED_MODULE_63__["default"]; });

/* harmony import */ var _memoize_js__WEBPACK_IMPORTED_MODULE_64__ = __webpack_require__(/*! ./memoize.js */ "./node_modules/underscore/modules/memoize.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "memoize", function() { return _memoize_js__WEBPACK_IMPORTED_MODULE_64__["default"]; });

/* harmony import */ var _delay_js__WEBPACK_IMPORTED_MODULE_65__ = __webpack_require__(/*! ./delay.js */ "./node_modules/underscore/modules/delay.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "delay", function() { return _delay_js__WEBPACK_IMPORTED_MODULE_65__["default"]; });

/* harmony import */ var _defer_js__WEBPACK_IMPORTED_MODULE_66__ = __webpack_require__(/*! ./defer.js */ "./node_modules/underscore/modules/defer.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "defer", function() { return _defer_js__WEBPACK_IMPORTED_MODULE_66__["default"]; });

/* harmony import */ var _throttle_js__WEBPACK_IMPORTED_MODULE_67__ = __webpack_require__(/*! ./throttle.js */ "./node_modules/underscore/modules/throttle.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return _throttle_js__WEBPACK_IMPORTED_MODULE_67__["default"]; });

/* harmony import */ var _debounce_js__WEBPACK_IMPORTED_MODULE_68__ = __webpack_require__(/*! ./debounce.js */ "./node_modules/underscore/modules/debounce.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return _debounce_js__WEBPACK_IMPORTED_MODULE_68__["default"]; });

/* harmony import */ var _wrap_js__WEBPACK_IMPORTED_MODULE_69__ = __webpack_require__(/*! ./wrap.js */ "./node_modules/underscore/modules/wrap.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "wrap", function() { return _wrap_js__WEBPACK_IMPORTED_MODULE_69__["default"]; });

/* harmony import */ var _negate_js__WEBPACK_IMPORTED_MODULE_70__ = __webpack_require__(/*! ./negate.js */ "./node_modules/underscore/modules/negate.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "negate", function() { return _negate_js__WEBPACK_IMPORTED_MODULE_70__["default"]; });

/* harmony import */ var _compose_js__WEBPACK_IMPORTED_MODULE_71__ = __webpack_require__(/*! ./compose.js */ "./node_modules/underscore/modules/compose.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "compose", function() { return _compose_js__WEBPACK_IMPORTED_MODULE_71__["default"]; });

/* harmony import */ var _after_js__WEBPACK_IMPORTED_MODULE_72__ = __webpack_require__(/*! ./after.js */ "./node_modules/underscore/modules/after.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "after", function() { return _after_js__WEBPACK_IMPORTED_MODULE_72__["default"]; });

/* harmony import */ var _before_js__WEBPACK_IMPORTED_MODULE_73__ = __webpack_require__(/*! ./before.js */ "./node_modules/underscore/modules/before.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "before", function() { return _before_js__WEBPACK_IMPORTED_MODULE_73__["default"]; });

/* harmony import */ var _once_js__WEBPACK_IMPORTED_MODULE_74__ = __webpack_require__(/*! ./once.js */ "./node_modules/underscore/modules/once.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "once", function() { return _once_js__WEBPACK_IMPORTED_MODULE_74__["default"]; });

/* harmony import */ var _findKey_js__WEBPACK_IMPORTED_MODULE_75__ = __webpack_require__(/*! ./findKey.js */ "./node_modules/underscore/modules/findKey.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findKey", function() { return _findKey_js__WEBPACK_IMPORTED_MODULE_75__["default"]; });

/* harmony import */ var _findIndex_js__WEBPACK_IMPORTED_MODULE_76__ = __webpack_require__(/*! ./findIndex.js */ "./node_modules/underscore/modules/findIndex.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findIndex", function() { return _findIndex_js__WEBPACK_IMPORTED_MODULE_76__["default"]; });

/* harmony import */ var _findLastIndex_js__WEBPACK_IMPORTED_MODULE_77__ = __webpack_require__(/*! ./findLastIndex.js */ "./node_modules/underscore/modules/findLastIndex.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findLastIndex", function() { return _findLastIndex_js__WEBPACK_IMPORTED_MODULE_77__["default"]; });

/* harmony import */ var _sortedIndex_js__WEBPACK_IMPORTED_MODULE_78__ = __webpack_require__(/*! ./sortedIndex.js */ "./node_modules/underscore/modules/sortedIndex.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sortedIndex", function() { return _sortedIndex_js__WEBPACK_IMPORTED_MODULE_78__["default"]; });

/* harmony import */ var _indexOf_js__WEBPACK_IMPORTED_MODULE_79__ = __webpack_require__(/*! ./indexOf.js */ "./node_modules/underscore/modules/indexOf.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "indexOf", function() { return _indexOf_js__WEBPACK_IMPORTED_MODULE_79__["default"]; });

/* harmony import */ var _lastIndexOf_js__WEBPACK_IMPORTED_MODULE_80__ = __webpack_require__(/*! ./lastIndexOf.js */ "./node_modules/underscore/modules/lastIndexOf.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "lastIndexOf", function() { return _lastIndexOf_js__WEBPACK_IMPORTED_MODULE_80__["default"]; });

/* harmony import */ var _find_js__WEBPACK_IMPORTED_MODULE_81__ = __webpack_require__(/*! ./find.js */ "./node_modules/underscore/modules/find.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "find", function() { return _find_js__WEBPACK_IMPORTED_MODULE_81__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "detect", function() { return _find_js__WEBPACK_IMPORTED_MODULE_81__["default"]; });

/* harmony import */ var _findWhere_js__WEBPACK_IMPORTED_MODULE_82__ = __webpack_require__(/*! ./findWhere.js */ "./node_modules/underscore/modules/findWhere.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findWhere", function() { return _findWhere_js__WEBPACK_IMPORTED_MODULE_82__["default"]; });

/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_83__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "each", function() { return _each_js__WEBPACK_IMPORTED_MODULE_83__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "forEach", function() { return _each_js__WEBPACK_IMPORTED_MODULE_83__["default"]; });

/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_84__ = __webpack_require__(/*! ./map.js */ "./node_modules/underscore/modules/map.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "map", function() { return _map_js__WEBPACK_IMPORTED_MODULE_84__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "collect", function() { return _map_js__WEBPACK_IMPORTED_MODULE_84__["default"]; });

/* harmony import */ var _reduce_js__WEBPACK_IMPORTED_MODULE_85__ = __webpack_require__(/*! ./reduce.js */ "./node_modules/underscore/modules/reduce.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reduce", function() { return _reduce_js__WEBPACK_IMPORTED_MODULE_85__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "foldl", function() { return _reduce_js__WEBPACK_IMPORTED_MODULE_85__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "inject", function() { return _reduce_js__WEBPACK_IMPORTED_MODULE_85__["default"]; });

/* harmony import */ var _reduceRight_js__WEBPACK_IMPORTED_MODULE_86__ = __webpack_require__(/*! ./reduceRight.js */ "./node_modules/underscore/modules/reduceRight.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reduceRight", function() { return _reduceRight_js__WEBPACK_IMPORTED_MODULE_86__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "foldr", function() { return _reduceRight_js__WEBPACK_IMPORTED_MODULE_86__["default"]; });

/* harmony import */ var _filter_js__WEBPACK_IMPORTED_MODULE_87__ = __webpack_require__(/*! ./filter.js */ "./node_modules/underscore/modules/filter.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "filter", function() { return _filter_js__WEBPACK_IMPORTED_MODULE_87__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "select", function() { return _filter_js__WEBPACK_IMPORTED_MODULE_87__["default"]; });

/* harmony import */ var _reject_js__WEBPACK_IMPORTED_MODULE_88__ = __webpack_require__(/*! ./reject.js */ "./node_modules/underscore/modules/reject.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reject", function() { return _reject_js__WEBPACK_IMPORTED_MODULE_88__["default"]; });

/* harmony import */ var _every_js__WEBPACK_IMPORTED_MODULE_89__ = __webpack_require__(/*! ./every.js */ "./node_modules/underscore/modules/every.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "every", function() { return _every_js__WEBPACK_IMPORTED_MODULE_89__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "all", function() { return _every_js__WEBPACK_IMPORTED_MODULE_89__["default"]; });

/* harmony import */ var _some_js__WEBPACK_IMPORTED_MODULE_90__ = __webpack_require__(/*! ./some.js */ "./node_modules/underscore/modules/some.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "some", function() { return _some_js__WEBPACK_IMPORTED_MODULE_90__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "any", function() { return _some_js__WEBPACK_IMPORTED_MODULE_90__["default"]; });

/* harmony import */ var _contains_js__WEBPACK_IMPORTED_MODULE_91__ = __webpack_require__(/*! ./contains.js */ "./node_modules/underscore/modules/contains.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "contains", function() { return _contains_js__WEBPACK_IMPORTED_MODULE_91__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "includes", function() { return _contains_js__WEBPACK_IMPORTED_MODULE_91__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "include", function() { return _contains_js__WEBPACK_IMPORTED_MODULE_91__["default"]; });

/* harmony import */ var _invoke_js__WEBPACK_IMPORTED_MODULE_92__ = __webpack_require__(/*! ./invoke.js */ "./node_modules/underscore/modules/invoke.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "invoke", function() { return _invoke_js__WEBPACK_IMPORTED_MODULE_92__["default"]; });

/* harmony import */ var _pluck_js__WEBPACK_IMPORTED_MODULE_93__ = __webpack_require__(/*! ./pluck.js */ "./node_modules/underscore/modules/pluck.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pluck", function() { return _pluck_js__WEBPACK_IMPORTED_MODULE_93__["default"]; });

/* harmony import */ var _where_js__WEBPACK_IMPORTED_MODULE_94__ = __webpack_require__(/*! ./where.js */ "./node_modules/underscore/modules/where.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "where", function() { return _where_js__WEBPACK_IMPORTED_MODULE_94__["default"]; });

/* harmony import */ var _max_js__WEBPACK_IMPORTED_MODULE_95__ = __webpack_require__(/*! ./max.js */ "./node_modules/underscore/modules/max.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "max", function() { return _max_js__WEBPACK_IMPORTED_MODULE_95__["default"]; });

/* harmony import */ var _min_js__WEBPACK_IMPORTED_MODULE_96__ = __webpack_require__(/*! ./min.js */ "./node_modules/underscore/modules/min.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "min", function() { return _min_js__WEBPACK_IMPORTED_MODULE_96__["default"]; });

/* harmony import */ var _shuffle_js__WEBPACK_IMPORTED_MODULE_97__ = __webpack_require__(/*! ./shuffle.js */ "./node_modules/underscore/modules/shuffle.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "shuffle", function() { return _shuffle_js__WEBPACK_IMPORTED_MODULE_97__["default"]; });

/* harmony import */ var _sample_js__WEBPACK_IMPORTED_MODULE_98__ = __webpack_require__(/*! ./sample.js */ "./node_modules/underscore/modules/sample.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sample", function() { return _sample_js__WEBPACK_IMPORTED_MODULE_98__["default"]; });

/* harmony import */ var _sortBy_js__WEBPACK_IMPORTED_MODULE_99__ = __webpack_require__(/*! ./sortBy.js */ "./node_modules/underscore/modules/sortBy.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sortBy", function() { return _sortBy_js__WEBPACK_IMPORTED_MODULE_99__["default"]; });

/* harmony import */ var _groupBy_js__WEBPACK_IMPORTED_MODULE_100__ = __webpack_require__(/*! ./groupBy.js */ "./node_modules/underscore/modules/groupBy.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "groupBy", function() { return _groupBy_js__WEBPACK_IMPORTED_MODULE_100__["default"]; });

/* harmony import */ var _indexBy_js__WEBPACK_IMPORTED_MODULE_101__ = __webpack_require__(/*! ./indexBy.js */ "./node_modules/underscore/modules/indexBy.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "indexBy", function() { return _indexBy_js__WEBPACK_IMPORTED_MODULE_101__["default"]; });

/* harmony import */ var _countBy_js__WEBPACK_IMPORTED_MODULE_102__ = __webpack_require__(/*! ./countBy.js */ "./node_modules/underscore/modules/countBy.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "countBy", function() { return _countBy_js__WEBPACK_IMPORTED_MODULE_102__["default"]; });

/* harmony import */ var _partition_js__WEBPACK_IMPORTED_MODULE_103__ = __webpack_require__(/*! ./partition.js */ "./node_modules/underscore/modules/partition.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "partition", function() { return _partition_js__WEBPACK_IMPORTED_MODULE_103__["default"]; });

/* harmony import */ var _toArray_js__WEBPACK_IMPORTED_MODULE_104__ = __webpack_require__(/*! ./toArray.js */ "./node_modules/underscore/modules/toArray.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toArray", function() { return _toArray_js__WEBPACK_IMPORTED_MODULE_104__["default"]; });

/* harmony import */ var _size_js__WEBPACK_IMPORTED_MODULE_105__ = __webpack_require__(/*! ./size.js */ "./node_modules/underscore/modules/size.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "size", function() { return _size_js__WEBPACK_IMPORTED_MODULE_105__["default"]; });

/* harmony import */ var _pick_js__WEBPACK_IMPORTED_MODULE_106__ = __webpack_require__(/*! ./pick.js */ "./node_modules/underscore/modules/pick.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pick", function() { return _pick_js__WEBPACK_IMPORTED_MODULE_106__["default"]; });

/* harmony import */ var _omit_js__WEBPACK_IMPORTED_MODULE_107__ = __webpack_require__(/*! ./omit.js */ "./node_modules/underscore/modules/omit.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "omit", function() { return _omit_js__WEBPACK_IMPORTED_MODULE_107__["default"]; });

/* harmony import */ var _first_js__WEBPACK_IMPORTED_MODULE_108__ = __webpack_require__(/*! ./first.js */ "./node_modules/underscore/modules/first.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "first", function() { return _first_js__WEBPACK_IMPORTED_MODULE_108__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "head", function() { return _first_js__WEBPACK_IMPORTED_MODULE_108__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "take", function() { return _first_js__WEBPACK_IMPORTED_MODULE_108__["default"]; });

/* harmony import */ var _initial_js__WEBPACK_IMPORTED_MODULE_109__ = __webpack_require__(/*! ./initial.js */ "./node_modules/underscore/modules/initial.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "initial", function() { return _initial_js__WEBPACK_IMPORTED_MODULE_109__["default"]; });

/* harmony import */ var _last_js__WEBPACK_IMPORTED_MODULE_110__ = __webpack_require__(/*! ./last.js */ "./node_modules/underscore/modules/last.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "last", function() { return _last_js__WEBPACK_IMPORTED_MODULE_110__["default"]; });

/* harmony import */ var _rest_js__WEBPACK_IMPORTED_MODULE_111__ = __webpack_require__(/*! ./rest.js */ "./node_modules/underscore/modules/rest.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "rest", function() { return _rest_js__WEBPACK_IMPORTED_MODULE_111__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "tail", function() { return _rest_js__WEBPACK_IMPORTED_MODULE_111__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "drop", function() { return _rest_js__WEBPACK_IMPORTED_MODULE_111__["default"]; });

/* harmony import */ var _compact_js__WEBPACK_IMPORTED_MODULE_112__ = __webpack_require__(/*! ./compact.js */ "./node_modules/underscore/modules/compact.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "compact", function() { return _compact_js__WEBPACK_IMPORTED_MODULE_112__["default"]; });

/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_113__ = __webpack_require__(/*! ./flatten.js */ "./node_modules/underscore/modules/flatten.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "flatten", function() { return _flatten_js__WEBPACK_IMPORTED_MODULE_113__["default"]; });

/* harmony import */ var _without_js__WEBPACK_IMPORTED_MODULE_114__ = __webpack_require__(/*! ./without.js */ "./node_modules/underscore/modules/without.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "without", function() { return _without_js__WEBPACK_IMPORTED_MODULE_114__["default"]; });

/* harmony import */ var _uniq_js__WEBPACK_IMPORTED_MODULE_115__ = __webpack_require__(/*! ./uniq.js */ "./node_modules/underscore/modules/uniq.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "uniq", function() { return _uniq_js__WEBPACK_IMPORTED_MODULE_115__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unique", function() { return _uniq_js__WEBPACK_IMPORTED_MODULE_115__["default"]; });

/* harmony import */ var _union_js__WEBPACK_IMPORTED_MODULE_116__ = __webpack_require__(/*! ./union.js */ "./node_modules/underscore/modules/union.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "union", function() { return _union_js__WEBPACK_IMPORTED_MODULE_116__["default"]; });

/* harmony import */ var _intersection_js__WEBPACK_IMPORTED_MODULE_117__ = __webpack_require__(/*! ./intersection.js */ "./node_modules/underscore/modules/intersection.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "intersection", function() { return _intersection_js__WEBPACK_IMPORTED_MODULE_117__["default"]; });

/* harmony import */ var _difference_js__WEBPACK_IMPORTED_MODULE_118__ = __webpack_require__(/*! ./difference.js */ "./node_modules/underscore/modules/difference.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "difference", function() { return _difference_js__WEBPACK_IMPORTED_MODULE_118__["default"]; });

/* harmony import */ var _unzip_js__WEBPACK_IMPORTED_MODULE_119__ = __webpack_require__(/*! ./unzip.js */ "./node_modules/underscore/modules/unzip.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unzip", function() { return _unzip_js__WEBPACK_IMPORTED_MODULE_119__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "transpose", function() { return _unzip_js__WEBPACK_IMPORTED_MODULE_119__["default"]; });

/* harmony import */ var _zip_js__WEBPACK_IMPORTED_MODULE_120__ = __webpack_require__(/*! ./zip.js */ "./node_modules/underscore/modules/zip.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "zip", function() { return _zip_js__WEBPACK_IMPORTED_MODULE_120__["default"]; });

/* harmony import */ var _object_js__WEBPACK_IMPORTED_MODULE_121__ = __webpack_require__(/*! ./object.js */ "./node_modules/underscore/modules/object.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "object", function() { return _object_js__WEBPACK_IMPORTED_MODULE_121__["default"]; });

/* harmony import */ var _range_js__WEBPACK_IMPORTED_MODULE_122__ = __webpack_require__(/*! ./range.js */ "./node_modules/underscore/modules/range.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "range", function() { return _range_js__WEBPACK_IMPORTED_MODULE_122__["default"]; });

/* harmony import */ var _chunk_js__WEBPACK_IMPORTED_MODULE_123__ = __webpack_require__(/*! ./chunk.js */ "./node_modules/underscore/modules/chunk.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "chunk", function() { return _chunk_js__WEBPACK_IMPORTED_MODULE_123__["default"]; });

/* harmony import */ var _mixin_js__WEBPACK_IMPORTED_MODULE_124__ = __webpack_require__(/*! ./mixin.js */ "./node_modules/underscore/modules/mixin.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "mixin", function() { return _mixin_js__WEBPACK_IMPORTED_MODULE_124__["default"]; });

/* harmony import */ var _underscore_array_methods_js__WEBPACK_IMPORTED_MODULE_125__ = __webpack_require__(/*! ./underscore-array-methods.js */ "./node_modules/underscore/modules/underscore-array-methods.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _underscore_array_methods_js__WEBPACK_IMPORTED_MODULE_125__["default"]; });

// Named Exports
// =============
//     Underscore.js 1.13.1
//     https://underscorejs.org
//     (c) 2009-2021 Jeremy Ashkenas, Julian Gonggrijp, and DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.
// Baseline setup.

 // Object Functions
// ----------------
// Our most fundamental functions operate on any JavaScript object.
// Most functions in Underscore depend on at least one function in this section.
// A group of functions that check the types of core JavaScript values.
// These are often informally referred to as the "isType" functions.


























 // Functions that treat an object as a dictionary of key-value pairs.















 // Utility Functions
// -----------------
// A bit of a grab bag: Predicate-generating functions for use with filters and
// loops, string escaping and templating, create random numbers and unique ids,
// and functions that facilitate Underscore's chaining and iteration conventions.


















 // Function (ahem) Functions
// -------------------------
// These functions take a function as an argument and return a new function
// as the result. Also known as higher-order functions.














 // Finders
// -------
// Functions that extract (the position of) a single element from an object
// or array based on some criterion.








 // Collection Functions
// --------------------
// Functions that work on any collection of elements: either an array, or
// an object of key-value pairs.























 // `_.pick` and `_.omit` are actually object functions, but we put
// them here in order to create a more natural reading order in the
// monolithic build as they depend on `_.contains`.


 // Array Functions
// ---------------
// Functions that operate on arrays (and array-likes) only, because they’re
// expressed in terms of operations on an ordered list of values.
















 // OOP
// ---
// These modules support the "object-oriented" calling style. See also
// `underscore.js` and `index-default.js`.




/***/ }),

/***/ "./node_modules/underscore/modules/indexBy.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/indexBy.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _group_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_group.js */ "./node_modules/underscore/modules/_group.js");
 // Indexes the object's values by a criterion, similar to `_.groupBy`, but for
// when you know that your index values will be unique.

/* harmony default export */ __webpack_exports__["default"] = (Object(_group_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (result, value, key) {
  result[key] = value;
}));

/***/ }),

/***/ "./node_modules/underscore/modules/indexOf.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/indexOf.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _sortedIndex_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./sortedIndex.js */ "./node_modules/underscore/modules/sortedIndex.js");
/* harmony import */ var _findIndex_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./findIndex.js */ "./node_modules/underscore/modules/findIndex.js");
/* harmony import */ var _createIndexFinder_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_createIndexFinder.js */ "./node_modules/underscore/modules/_createIndexFinder.js");


 // Return the position of the first occurrence of an item in an array,
// or -1 if the item is not included in the array.
// If the array is large and already in sort order, pass `true`
// for **isSorted** to use binary search.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createIndexFinder_js__WEBPACK_IMPORTED_MODULE_2__["default"])(1, _findIndex_js__WEBPACK_IMPORTED_MODULE_1__["default"], _sortedIndex_js__WEBPACK_IMPORTED_MODULE_0__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/initial.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/initial.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return initial; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Returns everything but the last entry of the array. Especially useful on
// the arguments object. Passing **n** will return all the values in
// the array, excluding the last N.

function initial(array, n, guard) {
  return _setup_js__WEBPACK_IMPORTED_MODULE_0__["slice"].call(array, 0, Math.max(0, array.length - (n == null || guard ? 1 : n)));
}

/***/ }),

/***/ "./node_modules/underscore/modules/intersection.js":
/*!*********************************************************!*\
  !*** ./node_modules/underscore/modules/intersection.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return intersection; });
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _contains_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./contains.js */ "./node_modules/underscore/modules/contains.js");

 // Produce an array that contains every item shared between all the
// passed-in arrays.

function intersection(array) {
  var result = [];
  var argsLength = arguments.length;

  for (var i = 0, length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array); i < length; i++) {
    var item = array[i];
    if (Object(_contains_js__WEBPACK_IMPORTED_MODULE_1__["default"])(result, item)) continue;
    var j;

    for (j = 1; j < argsLength; j++) {
      if (!Object(_contains_js__WEBPACK_IMPORTED_MODULE_1__["default"])(arguments[j], item)) break;
    }

    if (j === argsLength) result.push(item);
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/invert.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/invert.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return invert; });
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
 // Invert the keys and values of an object. The values must be serializable.

function invert(obj) {
  var result = {};

  var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj);

  for (var i = 0, length = _keys.length; i < length; i++) {
    result[obj[_keys[i]]] = _keys[i];
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/invoke.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/invoke.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./map.js */ "./node_modules/underscore/modules/map.js");
/* harmony import */ var _deepGet_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_deepGet.js */ "./node_modules/underscore/modules/_deepGet.js");
/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./_toPath.js */ "./node_modules/underscore/modules/_toPath.js");




 // Invoke a method (with arguments) on every item in a collection.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (obj, path, args) {
  var contextPath, func;

  if (Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(path)) {
    func = path;
  } else {
    path = Object(_toPath_js__WEBPACK_IMPORTED_MODULE_4__["default"])(path);
    contextPath = path.slice(0, -1);
    path = path[path.length - 1];
  }

  return Object(_map_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj, function (context) {
    var method = func;

    if (!method) {
      if (contextPath && contextPath.length) {
        context = Object(_deepGet_js__WEBPACK_IMPORTED_MODULE_3__["default"])(context, contextPath);
      }

      if (context == null) return void 0;
      method = context[path];
    }

    return method == null ? method : method.apply(context, args);
  });
}));

/***/ }),

/***/ "./node_modules/underscore/modules/isArguments.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/isArguments.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");


var isArguments = Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Arguments'); // Define a fallback version of the method in browsers (ahem, IE < 9), where
// there isn't any inspectable "Arguments" type.

(function () {
  if (!isArguments(arguments)) {
    isArguments = function isArguments(obj) {
      return Object(_has_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj, 'callee');
    };
  }
})();

/* harmony default export */ __webpack_exports__["default"] = (isArguments);

/***/ }),

/***/ "./node_modules/underscore/modules/isArray.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/isArray.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

 // Is a given value an array?
// Delegates to ECMA5's native `Array.isArray`.

/* harmony default export */ __webpack_exports__["default"] = (_setup_js__WEBPACK_IMPORTED_MODULE_0__["nativeIsArray"] || Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_1__["default"])('Array'));

/***/ }),

/***/ "./node_modules/underscore/modules/isArrayBuffer.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/isArrayBuffer.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('ArrayBuffer'));

/***/ }),

/***/ "./node_modules/underscore/modules/isBoolean.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/isBoolean.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isBoolean; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Is a given value a boolean?

function isBoolean(obj) {
  return obj === true || obj === false || _setup_js__WEBPACK_IMPORTED_MODULE_0__["toString"].call(obj) === '[object Boolean]';
}

/***/ }),

/***/ "./node_modules/underscore/modules/isDataView.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/isDataView.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _isArrayBuffer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isArrayBuffer.js */ "./node_modules/underscore/modules/isArrayBuffer.js");
/* harmony import */ var _stringTagBug_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_stringTagBug.js */ "./node_modules/underscore/modules/_stringTagBug.js");




var isDataView = Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('DataView'); // In IE 10 - Edge 13, we need a different heuristic
// to determine whether an object is a `DataView`.

function ie10IsDataView(obj) {
  return obj != null && Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj.getInt8) && Object(_isArrayBuffer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj.buffer);
}

/* harmony default export */ __webpack_exports__["default"] = (_stringTagBug_js__WEBPACK_IMPORTED_MODULE_3__["hasStringTagBug"] ? ie10IsDataView : isDataView);

/***/ }),

/***/ "./node_modules/underscore/modules/isDate.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/isDate.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Date'));

/***/ }),

/***/ "./node_modules/underscore/modules/isElement.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/isElement.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isElement; });
// Is a given value a DOM element?
function isElement(obj) {
  return !!(obj && obj.nodeType === 1);
}

/***/ }),

/***/ "./node_modules/underscore/modules/isEmpty.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/isEmpty.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isEmpty; });
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");
/* harmony import */ var _isString_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isString.js */ "./node_modules/underscore/modules/isString.js");
/* harmony import */ var _isArguments_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./isArguments.js */ "./node_modules/underscore/modules/isArguments.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");




 // Is a given array, string, or object empty?
// An "empty" object has no enumerable own-properties.

function isEmpty(obj) {
  if (obj == null) return true; // Skip the more expensive `toString`-based type checks if `obj` has no
  // `.length`.

  var length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj);
  if (typeof length == 'number' && (Object(_isArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) || Object(_isString_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj) || Object(_isArguments_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj))) return length === 0;
  return Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(_keys_js__WEBPACK_IMPORTED_MODULE_4__["default"])(obj)) === 0;
}

/***/ }),

/***/ "./node_modules/underscore/modules/isEqual.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/isEqual.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isEqual; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _getByteLength_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_getByteLength.js */ "./node_modules/underscore/modules/_getByteLength.js");
/* harmony import */ var _isTypedArray_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./isTypedArray.js */ "./node_modules/underscore/modules/isTypedArray.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _stringTagBug_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./_stringTagBug.js */ "./node_modules/underscore/modules/_stringTagBug.js");
/* harmony import */ var _isDataView_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./isDataView.js */ "./node_modules/underscore/modules/isDataView.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");
/* harmony import */ var _toBufferView_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./_toBufferView.js */ "./node_modules/underscore/modules/_toBufferView.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }










 // We use this string twice, so give it a name for minification.

var tagDataView = '[object DataView]'; // Internal recursive comparison function for `_.isEqual`.

function eq(a, b, aStack, bStack) {
  // Identical objects are equal. `0 === -0`, but they aren't identical.
  // See the [Harmony `egal` proposal](https://wiki.ecmascript.org/doku.php?id=harmony:egal).
  if (a === b) return a !== 0 || 1 / a === 1 / b; // `null` or `undefined` only equal to itself (strict comparison).

  if (a == null || b == null) return false; // `NaN`s are equivalent, but non-reflexive.

  if (a !== a) return b !== b; // Exhaust primitive checks

  var type = _typeof(a);

  if (type !== 'function' && type !== 'object' && _typeof(b) != 'object') return false;
  return deepEq(a, b, aStack, bStack);
} // Internal recursive comparison function for `_.isEqual`.


function deepEq(a, b, aStack, bStack) {
  // Unwrap any wrapped objects.
  if (a instanceof _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"]) a = a._wrapped;
  if (b instanceof _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"]) b = b._wrapped; // Compare `[[Class]]` names.

  var className = _setup_js__WEBPACK_IMPORTED_MODULE_1__["toString"].call(a);
  if (className !== _setup_js__WEBPACK_IMPORTED_MODULE_1__["toString"].call(b)) return false; // Work around a bug in IE 10 - Edge 13.

  if (_stringTagBug_js__WEBPACK_IMPORTED_MODULE_5__["hasStringTagBug"] && className == '[object Object]' && Object(_isDataView_js__WEBPACK_IMPORTED_MODULE_6__["default"])(a)) {
    if (!Object(_isDataView_js__WEBPACK_IMPORTED_MODULE_6__["default"])(b)) return false;
    className = tagDataView;
  }

  switch (className) {
    // These types are compared by value.
    case '[object RegExp]': // RegExps are coerced to strings for comparison (Note: '' + /a/i === '/a/i')

    case '[object String]':
      // Primitives and their corresponding object wrappers are equivalent; thus, `"5"` is
      // equivalent to `new String("5")`.
      return '' + a === '' + b;

    case '[object Number]':
      // `NaN`s are equivalent, but non-reflexive.
      // Object(NaN) is equivalent to NaN.
      if (+a !== +a) return +b !== +b; // An `egal` comparison is performed for other numeric values.

      return +a === 0 ? 1 / +a === 1 / b : +a === +b;

    case '[object Date]':
    case '[object Boolean]':
      // Coerce dates and booleans to numeric primitive values. Dates are compared by their
      // millisecond representations. Note that invalid dates with millisecond representations
      // of `NaN` are not equivalent.
      return +a === +b;

    case '[object Symbol]':
      return _setup_js__WEBPACK_IMPORTED_MODULE_1__["SymbolProto"].valueOf.call(a) === _setup_js__WEBPACK_IMPORTED_MODULE_1__["SymbolProto"].valueOf.call(b);

    case '[object ArrayBuffer]':
    case tagDataView:
      // Coerce to typed array so we can fall through.
      return deepEq(Object(_toBufferView_js__WEBPACK_IMPORTED_MODULE_9__["default"])(a), Object(_toBufferView_js__WEBPACK_IMPORTED_MODULE_9__["default"])(b), aStack, bStack);
  }

  var areArrays = className === '[object Array]';

  if (!areArrays && Object(_isTypedArray_js__WEBPACK_IMPORTED_MODULE_3__["default"])(a)) {
    var byteLength = Object(_getByteLength_js__WEBPACK_IMPORTED_MODULE_2__["default"])(a);
    if (byteLength !== Object(_getByteLength_js__WEBPACK_IMPORTED_MODULE_2__["default"])(b)) return false;
    if (a.buffer === b.buffer && a.byteOffset === b.byteOffset) return true;
    areArrays = true;
  }

  if (!areArrays) {
    if (_typeof(a) != 'object' || _typeof(b) != 'object') return false; // Objects with different constructors are not equivalent, but `Object`s or `Array`s
    // from different frames are.

    var aCtor = a.constructor,
        bCtor = b.constructor;

    if (aCtor !== bCtor && !(Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_4__["default"])(aCtor) && aCtor instanceof aCtor && Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_4__["default"])(bCtor) && bCtor instanceof bCtor) && 'constructor' in a && 'constructor' in b) {
      return false;
    }
  } // Assume equality for cyclic structures. The algorithm for detecting cyclic
  // structures is adapted from ES 5.1 section 15.12.3, abstract operation `JO`.
  // Initializing stack of traversed objects.
  // It's done here since we only need them for objects and arrays comparison.


  aStack = aStack || [];
  bStack = bStack || [];
  var length = aStack.length;

  while (length--) {
    // Linear search. Performance is inversely proportional to the number of
    // unique nested structures.
    if (aStack[length] === a) return bStack[length] === b;
  } // Add the first object to the stack of traversed objects.


  aStack.push(a);
  bStack.push(b); // Recursively compare objects and arrays.

  if (areArrays) {
    // Compare array lengths to determine if a deep comparison is necessary.
    length = a.length;
    if (length !== b.length) return false; // Deep compare the contents, ignoring non-numeric properties.

    while (length--) {
      if (!eq(a[length], b[length], aStack, bStack)) return false;
    }
  } else {
    // Deep compare objects.
    var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_7__["default"])(a),
        key;

    length = _keys.length; // Ensure that both objects contain the same number of properties before comparing deep equality.

    if (Object(_keys_js__WEBPACK_IMPORTED_MODULE_7__["default"])(b).length !== length) return false;

    while (length--) {
      // Deep compare each member
      key = _keys[length];
      if (!(Object(_has_js__WEBPACK_IMPORTED_MODULE_8__["default"])(b, key) && eq(a[key], b[key], aStack, bStack))) return false;
    }
  } // Remove the first object from the stack of traversed objects.


  aStack.pop();
  bStack.pop();
  return true;
} // Perform a deep comparison to check if two objects are equal.


function isEqual(a, b) {
  return eq(a, b);
}

/***/ }),

/***/ "./node_modules/underscore/modules/isError.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/isError.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Error'));

/***/ }),

/***/ "./node_modules/underscore/modules/isFinite.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/isFinite.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isFinite; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _isSymbol_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isSymbol.js */ "./node_modules/underscore/modules/isSymbol.js");

 // Is a given object a finite number?

function isFinite(obj) {
  return !Object(_isSymbol_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) && Object(_setup_js__WEBPACK_IMPORTED_MODULE_0__["_isFinite"])(obj) && !isNaN(parseFloat(obj));
}

/***/ }),

/***/ "./node_modules/underscore/modules/isFunction.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/isFunction.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }



var isFunction = Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Function'); // Optimize `isFunction` if appropriate. Work around some `typeof` bugs in old
// v8, IE 11 (#1621), Safari 8 (#1929), and PhantomJS (#2236).

var nodelist = _setup_js__WEBPACK_IMPORTED_MODULE_1__["root"].document && _setup_js__WEBPACK_IMPORTED_MODULE_1__["root"].document.childNodes;

if ( true && (typeof Int8Array === "undefined" ? "undefined" : _typeof(Int8Array)) != 'object' && typeof nodelist != 'function') {
  isFunction = function isFunction(obj) {
    return typeof obj == 'function' || false;
  };
}

/* harmony default export */ __webpack_exports__["default"] = (isFunction);

/***/ }),

/***/ "./node_modules/underscore/modules/isMap.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/isMap.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");
/* harmony import */ var _stringTagBug_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_stringTagBug.js */ "./node_modules/underscore/modules/_stringTagBug.js");
/* harmony import */ var _methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_methodFingerprint.js */ "./node_modules/underscore/modules/_methodFingerprint.js");



/* harmony default export */ __webpack_exports__["default"] = (_stringTagBug_js__WEBPACK_IMPORTED_MODULE_1__["isIE11"] ? Object(_methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__["ie11fingerprint"])(_methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__["mapMethods"]) : Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Map'));

/***/ }),

/***/ "./node_modules/underscore/modules/isMatch.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/isMatch.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isMatch; });
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
 // Returns whether an object has a given set of `key:value` pairs.

function isMatch(object, attrs) {
  var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_0__["default"])(attrs),
      length = _keys.length;

  if (object == null) return !length;
  var obj = Object(object);

  for (var i = 0; i < length; i++) {
    var key = _keys[i];
    if (attrs[key] !== obj[key] || !(key in obj)) return false;
  }

  return true;
}

/***/ }),

/***/ "./node_modules/underscore/modules/isNaN.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/isNaN.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isNaN; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _isNumber_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isNumber.js */ "./node_modules/underscore/modules/isNumber.js");

 // Is the given value `NaN`?

function isNaN(obj) {
  return Object(_isNumber_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) && Object(_setup_js__WEBPACK_IMPORTED_MODULE_0__["_isNaN"])(obj);
}

/***/ }),

/***/ "./node_modules/underscore/modules/isNull.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/isNull.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isNull; });
// Is a given value equal to null?
function isNull(obj) {
  return obj === null;
}

/***/ }),

/***/ "./node_modules/underscore/modules/isNumber.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/isNumber.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Number'));

/***/ }),

/***/ "./node_modules/underscore/modules/isObject.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/isObject.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isObject; });
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

// Is a given variable an object?
function isObject(obj) {
  var type = _typeof(obj);

  return type === 'function' || type === 'object' && !!obj;
}

/***/ }),

/***/ "./node_modules/underscore/modules/isRegExp.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/isRegExp.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('RegExp'));

/***/ }),

/***/ "./node_modules/underscore/modules/isSet.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/isSet.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");
/* harmony import */ var _stringTagBug_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_stringTagBug.js */ "./node_modules/underscore/modules/_stringTagBug.js");
/* harmony import */ var _methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_methodFingerprint.js */ "./node_modules/underscore/modules/_methodFingerprint.js");



/* harmony default export */ __webpack_exports__["default"] = (_stringTagBug_js__WEBPACK_IMPORTED_MODULE_1__["isIE11"] ? Object(_methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__["ie11fingerprint"])(_methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__["setMethods"]) : Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Set'));

/***/ }),

/***/ "./node_modules/underscore/modules/isString.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/isString.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('String'));

/***/ }),

/***/ "./node_modules/underscore/modules/isSymbol.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/isSymbol.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('Symbol'));

/***/ }),

/***/ "./node_modules/underscore/modules/isTypedArray.js":
/*!*********************************************************!*\
  !*** ./node_modules/underscore/modules/isTypedArray.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _isDataView_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isDataView.js */ "./node_modules/underscore/modules/isDataView.js");
/* harmony import */ var _constant_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./constant.js */ "./node_modules/underscore/modules/constant.js");
/* harmony import */ var _isBufferLike_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_isBufferLike.js */ "./node_modules/underscore/modules/_isBufferLike.js");



 // Is a given value a typed array?

var typedArrayPattern = /\[object ((I|Ui)nt(8|16|32)|Float(32|64)|Uint8Clamped|Big(I|Ui)nt64)Array\]/;

function isTypedArray(obj) {
  // `ArrayBuffer.isView` is the most future-proof, so use it when available.
  // Otherwise, fall back on the above regular expression.
  return _setup_js__WEBPACK_IMPORTED_MODULE_0__["nativeIsView"] ? Object(_setup_js__WEBPACK_IMPORTED_MODULE_0__["nativeIsView"])(obj) && !Object(_isDataView_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) : Object(_isBufferLike_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj) && typedArrayPattern.test(_setup_js__WEBPACK_IMPORTED_MODULE_0__["toString"].call(obj));
}

/* harmony default export */ __webpack_exports__["default"] = (_setup_js__WEBPACK_IMPORTED_MODULE_0__["supportsArrayBuffer"] ? isTypedArray : Object(_constant_js__WEBPACK_IMPORTED_MODULE_2__["default"])(false));

/***/ }),

/***/ "./node_modules/underscore/modules/isUndefined.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/isUndefined.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isUndefined; });
// Is a given variable undefined?
function isUndefined(obj) {
  return obj === void 0;
}

/***/ }),

/***/ "./node_modules/underscore/modules/isWeakMap.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/isWeakMap.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");
/* harmony import */ var _stringTagBug_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_stringTagBug.js */ "./node_modules/underscore/modules/_stringTagBug.js");
/* harmony import */ var _methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_methodFingerprint.js */ "./node_modules/underscore/modules/_methodFingerprint.js");



/* harmony default export */ __webpack_exports__["default"] = (_stringTagBug_js__WEBPACK_IMPORTED_MODULE_1__["isIE11"] ? Object(_methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__["ie11fingerprint"])(_methodFingerprint_js__WEBPACK_IMPORTED_MODULE_2__["weakMapMethods"]) : Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('WeakMap'));

/***/ }),

/***/ "./node_modules/underscore/modules/isWeakSet.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/isWeakSet.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _tagTester_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_tagTester.js */ "./node_modules/underscore/modules/_tagTester.js");

/* harmony default export */ __webpack_exports__["default"] = (Object(_tagTester_js__WEBPACK_IMPORTED_MODULE_0__["default"])('WeakSet'));

/***/ }),

/***/ "./node_modules/underscore/modules/iteratee.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/iteratee.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return iteratee; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _baseIteratee_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_baseIteratee.js */ "./node_modules/underscore/modules/_baseIteratee.js");

 // External wrapper for our callback generator. Users may customize
// `_.iteratee` if they want additional predicate/iteratee shorthand styles.
// This abstraction hides the internal-only `argCount` argument.

function iteratee(value, context) {
  return Object(_baseIteratee_js__WEBPACK_IMPORTED_MODULE_1__["default"])(value, context, Infinity);
}
_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].iteratee = iteratee;

/***/ }),

/***/ "./node_modules/underscore/modules/keys.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/keys.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return keys; });
/* harmony import */ var _isObject_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isObject.js */ "./node_modules/underscore/modules/isObject.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");
/* harmony import */ var _collectNonEnumProps_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_collectNonEnumProps.js */ "./node_modules/underscore/modules/_collectNonEnumProps.js");



 // Retrieve the names of an object's own properties.
// Delegates to **ECMAScript 5**'s native `Object.keys`.

function keys(obj) {
  if (!Object(_isObject_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj)) return [];
  if (_setup_js__WEBPACK_IMPORTED_MODULE_1__["nativeKeys"]) return Object(_setup_js__WEBPACK_IMPORTED_MODULE_1__["nativeKeys"])(obj);
  var keys = [];

  for (var key in obj) {
    if (Object(_has_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj, key)) keys.push(key);
  } // Ahem, IE < 9.


  if (_setup_js__WEBPACK_IMPORTED_MODULE_1__["hasEnumBug"]) Object(_collectNonEnumProps_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj, keys);
  return keys;
}

/***/ }),

/***/ "./node_modules/underscore/modules/last.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/last.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return last; });
/* harmony import */ var _rest_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./rest.js */ "./node_modules/underscore/modules/rest.js");
 // Get the last element of an array. Passing **n** will return the last N
// values in the array.

function last(array, n, guard) {
  if (array == null || array.length < 1) return n == null || guard ? void 0 : [];
  if (n == null || guard) return array[array.length - 1];
  return Object(_rest_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array, Math.max(0, array.length - n));
}

/***/ }),

/***/ "./node_modules/underscore/modules/lastIndexOf.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/lastIndexOf.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _findLastIndex_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./findLastIndex.js */ "./node_modules/underscore/modules/findLastIndex.js");
/* harmony import */ var _createIndexFinder_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_createIndexFinder.js */ "./node_modules/underscore/modules/_createIndexFinder.js");

 // Return the position of the last occurrence of an item in an array,
// or -1 if the item is not included in the array.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createIndexFinder_js__WEBPACK_IMPORTED_MODULE_1__["default"])(-1, _findLastIndex_js__WEBPACK_IMPORTED_MODULE_0__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/map.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/map.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return map; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");


 // Return the results of applying the iteratee to each element.

function map(obj, iteratee, context) {
  iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context);

  var _keys = !Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) && Object(_keys_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj),
      length = (_keys || obj).length,
      results = Array(length);

  for (var index = 0; index < length; index++) {
    var currentKey = _keys ? _keys[index] : index;
    results[index] = iteratee(obj[currentKey], currentKey, obj);
  }

  return results;
}

/***/ }),

/***/ "./node_modules/underscore/modules/mapObject.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/mapObject.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return mapObject; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");

 // Returns the results of applying the `iteratee` to each element of `obj`.
// In contrast to `_.map` it returns an object.

function mapObject(obj, iteratee, context) {
  iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context);

  var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj),
      length = _keys.length,
      results = {};

  for (var index = 0; index < length; index++) {
    var currentKey = _keys[index];
    results[currentKey] = iteratee(obj[currentKey], currentKey, obj);
  }

  return results;
}

/***/ }),

/***/ "./node_modules/underscore/modules/matcher.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/matcher.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return matcher; });
/* harmony import */ var _extendOwn_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./extendOwn.js */ "./node_modules/underscore/modules/extendOwn.js");
/* harmony import */ var _isMatch_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isMatch.js */ "./node_modules/underscore/modules/isMatch.js");

 // Returns a predicate for checking whether an object has a given set of
// `key:value` pairs.

function matcher(attrs) {
  attrs = Object(_extendOwn_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, attrs);
  return function (obj) {
    return Object(_isMatch_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj, attrs);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/max.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/max.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return max; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _values_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./values.js */ "./node_modules/underscore/modules/values.js");
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }




 // Return the maximum element (or element-based computation).

function max(obj, iteratee, context) {
  var result = -Infinity,
      lastComputed = -Infinity,
      value,
      computed;

  if (iteratee == null || typeof iteratee == 'number' && _typeof(obj[0]) != 'object' && obj != null) {
    obj = Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj) ? obj : Object(_values_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj);

    for (var i = 0, length = obj.length; i < length; i++) {
      value = obj[i];

      if (value != null && value > result) {
        result = value;
      }
    }
  } else {
    iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_2__["default"])(iteratee, context);
    Object(_each_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj, function (v, index, list) {
      computed = iteratee(v, index, list);

      if (computed > lastComputed || computed === -Infinity && result === -Infinity) {
        result = v;
        lastComputed = computed;
      }
    });
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/memoize.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/memoize.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return memoize; });
/* harmony import */ var _has_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_has.js */ "./node_modules/underscore/modules/_has.js");
 // Memoize an expensive function by storing its results.

function memoize(func, hasher) {
  var memoize = function memoize(key) {
    var cache = memoize.cache;
    var address = '' + (hasher ? hasher.apply(this, arguments) : key);
    if (!Object(_has_js__WEBPACK_IMPORTED_MODULE_0__["default"])(cache, address)) cache[address] = func.apply(this, arguments);
    return cache[address];
  };

  memoize.cache = {};
  return memoize;
}

/***/ }),

/***/ "./node_modules/underscore/modules/min.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/min.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return min; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _values_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./values.js */ "./node_modules/underscore/modules/values.js");
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }




 // Return the minimum element (or element-based computation).

function min(obj, iteratee, context) {
  var result = Infinity,
      lastComputed = Infinity,
      value,
      computed;

  if (iteratee == null || typeof iteratee == 'number' && _typeof(obj[0]) != 'object' && obj != null) {
    obj = Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj) ? obj : Object(_values_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj);

    for (var i = 0, length = obj.length; i < length; i++) {
      value = obj[i];

      if (value != null && value < result) {
        result = value;
      }
    }
  } else {
    iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_2__["default"])(iteratee, context);
    Object(_each_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj, function (v, index, list) {
      computed = iteratee(v, index, list);

      if (computed < lastComputed || computed === Infinity && result === Infinity) {
        result = v;
        lastComputed = computed;
      }
    });
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/mixin.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/mixin.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return mixin; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");
/* harmony import */ var _functions_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./functions.js */ "./node_modules/underscore/modules/functions.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _chainResult_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./_chainResult.js */ "./node_modules/underscore/modules/_chainResult.js");




 // Add your own custom functions to the Underscore object.

function mixin(obj) {
  Object(_each_js__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_functions_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj), function (name) {
    var func = _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"][name] = obj[name];

    _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].prototype[name] = function () {
      var args = [this._wrapped];
      _setup_js__WEBPACK_IMPORTED_MODULE_3__["push"].apply(args, arguments);
      return Object(_chainResult_js__WEBPACK_IMPORTED_MODULE_4__["default"])(this, func.apply(_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"], args));
    };
  });
  return _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"];
}

/***/ }),

/***/ "./node_modules/underscore/modules/negate.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/negate.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return negate; });
// Returns a negated version of the passed-in predicate.
function negate(predicate) {
  return function () {
    return !predicate.apply(this, arguments);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/noop.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/noop.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return noop; });
// Predicate-generating function. Often useful outside of Underscore.
function noop() {}

/***/ }),

/***/ "./node_modules/underscore/modules/now.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/now.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// A (possibly faster) way to get the current timestamp as an integer.
/* harmony default export */ __webpack_exports__["default"] = (Date.now || function () {
  return new Date().getTime();
});

/***/ }),

/***/ "./node_modules/underscore/modules/object.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/object.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return object; });
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
 // Converts lists into objects. Pass either a single array of `[key, value]`
// pairs, or two parallel arrays of the same length -- one of keys, and one of
// the corresponding values. Passing by pairs is the reverse of `_.pairs`.

function object(list, values) {
  var result = {};

  for (var i = 0, length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_0__["default"])(list); i < length; i++) {
    if (values) {
      result[list[i]] = values[i];
    } else {
      result[list[i][0]] = list[i][1];
    }
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/omit.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/omit.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _negate_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./negate.js */ "./node_modules/underscore/modules/negate.js");
/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./map.js */ "./node_modules/underscore/modules/map.js");
/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./_flatten.js */ "./node_modules/underscore/modules/_flatten.js");
/* harmony import */ var _contains_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./contains.js */ "./node_modules/underscore/modules/contains.js");
/* harmony import */ var _pick_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./pick.js */ "./node_modules/underscore/modules/pick.js");






 // Return a copy of the object without the disallowed properties.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (obj, keys) {
  var iteratee = keys[0],
      context;

  if (Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(iteratee)) {
    iteratee = Object(_negate_js__WEBPACK_IMPORTED_MODULE_2__["default"])(iteratee);
    if (keys.length > 1) context = keys[1];
  } else {
    keys = Object(_map_js__WEBPACK_IMPORTED_MODULE_3__["default"])(Object(_flatten_js__WEBPACK_IMPORTED_MODULE_4__["default"])(keys, false, false), String);

    iteratee = function iteratee(value, key) {
      return !Object(_contains_js__WEBPACK_IMPORTED_MODULE_5__["default"])(keys, key);
    };
  }

  return Object(_pick_js__WEBPACK_IMPORTED_MODULE_6__["default"])(obj, iteratee, context);
}));

/***/ }),

/***/ "./node_modules/underscore/modules/once.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/once.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _partial_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./partial.js */ "./node_modules/underscore/modules/partial.js");
/* harmony import */ var _before_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./before.js */ "./node_modules/underscore/modules/before.js");

 // Returns a function that will be executed at most one time, no matter how
// often you call it. Useful for lazy initialization.

/* harmony default export */ __webpack_exports__["default"] = (Object(_partial_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_before_js__WEBPACK_IMPORTED_MODULE_1__["default"], 2));

/***/ }),

/***/ "./node_modules/underscore/modules/pairs.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/pairs.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return pairs; });
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
 // Convert an object into a list of `[key, value]` pairs.
// The opposite of `_.object` with one argument.

function pairs(obj) {
  var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj);

  var length = _keys.length;
  var pairs = Array(length);

  for (var i = 0; i < length; i++) {
    pairs[i] = [_keys[i], obj[_keys[i]]];
  }

  return pairs;
}

/***/ }),

/***/ "./node_modules/underscore/modules/partial.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/partial.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _executeBound_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_executeBound.js */ "./node_modules/underscore/modules/_executeBound.js");
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");


 // Partially apply a function by creating a version that has had some of its
// arguments pre-filled, without changing its dynamic `this` context. `_` acts
// as a placeholder by default, allowing any combination of arguments to be
// pre-filled. Set `_.partial.placeholder` for a custom placeholder argument.

var partial = Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (func, boundArgs) {
  var placeholder = partial.placeholder;

  var bound = function bound() {
    var position = 0,
        length = boundArgs.length;
    var args = Array(length);

    for (var i = 0; i < length; i++) {
      args[i] = boundArgs[i] === placeholder ? arguments[position++] : boundArgs[i];
    }

    while (position < arguments.length) {
      args.push(arguments[position++]);
    }

    return Object(_executeBound_js__WEBPACK_IMPORTED_MODULE_1__["default"])(func, bound, this, this, args);
  };

  return bound;
});
partial.placeholder = _underscore_js__WEBPACK_IMPORTED_MODULE_2__["default"];
/* harmony default export */ __webpack_exports__["default"] = (partial);

/***/ }),

/***/ "./node_modules/underscore/modules/partition.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/partition.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _group_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_group.js */ "./node_modules/underscore/modules/_group.js");
 // Split a collection into two arrays: one whose elements all pass the given
// truth test, and one whose elements all do not pass the truth test.

/* harmony default export */ __webpack_exports__["default"] = (Object(_group_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (result, value, pass) {
  result[pass ? 0 : 1].push(value);
}, true));

/***/ }),

/***/ "./node_modules/underscore/modules/pick.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/pick.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _optimizeCb_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_optimizeCb.js */ "./node_modules/underscore/modules/_optimizeCb.js");
/* harmony import */ var _allKeys_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./allKeys.js */ "./node_modules/underscore/modules/allKeys.js");
/* harmony import */ var _keyInObj_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./_keyInObj.js */ "./node_modules/underscore/modules/_keyInObj.js");
/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./_flatten.js */ "./node_modules/underscore/modules/_flatten.js");





 // Return a copy of the object only containing the allowed properties.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (obj, keys) {
  var result = {},
      iteratee = keys[0];
  if (obj == null) return result;

  if (Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_1__["default"])(iteratee)) {
    if (keys.length > 1) iteratee = Object(_optimizeCb_js__WEBPACK_IMPORTED_MODULE_2__["default"])(iteratee, keys[1]);
    keys = Object(_allKeys_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj);
  } else {
    iteratee = _keyInObj_js__WEBPACK_IMPORTED_MODULE_4__["default"];
    keys = Object(_flatten_js__WEBPACK_IMPORTED_MODULE_5__["default"])(keys, false, false);
    obj = Object(obj);
  }

  for (var i = 0, length = keys.length; i < length; i++) {
    var key = keys[i];
    var value = obj[key];
    if (iteratee(value, key, obj)) result[key] = value;
  }

  return result;
}));

/***/ }),

/***/ "./node_modules/underscore/modules/pluck.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/pluck.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return pluck; });
/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./map.js */ "./node_modules/underscore/modules/map.js");
/* harmony import */ var _property_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./property.js */ "./node_modules/underscore/modules/property.js");

 // Convenience version of a common use case of `_.map`: fetching a property.

function pluck(obj, key) {
  return Object(_map_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, Object(_property_js__WEBPACK_IMPORTED_MODULE_1__["default"])(key));
}

/***/ }),

/***/ "./node_modules/underscore/modules/property.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/property.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return property; });
/* harmony import */ var _deepGet_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_deepGet.js */ "./node_modules/underscore/modules/_deepGet.js");
/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_toPath.js */ "./node_modules/underscore/modules/_toPath.js");

 // Creates a function that, when passed an object, will traverse that object’s
// properties down the given `path`, specified as an array of keys or indices.

function property(path) {
  path = Object(_toPath_js__WEBPACK_IMPORTED_MODULE_1__["default"])(path);
  return function (obj) {
    return Object(_deepGet_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, path);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/propertyOf.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/propertyOf.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return propertyOf; });
/* harmony import */ var _noop_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./noop.js */ "./node_modules/underscore/modules/noop.js");
/* harmony import */ var _get_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get.js */ "./node_modules/underscore/modules/get.js");

 // Generates a function for a given object that returns a given property.

function propertyOf(obj) {
  if (obj == null) return _noop_js__WEBPACK_IMPORTED_MODULE_0__["default"];
  return function (path) {
    return Object(_get_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj, path);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/random.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/random.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return random; });
// Return a random integer between `min` and `max` (inclusive).
function random(min, max) {
  if (max == null) {
    max = min;
    min = 0;
  }

  return min + Math.floor(Math.random() * (max - min + 1));
}

/***/ }),

/***/ "./node_modules/underscore/modules/range.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/range.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return range; });
// Generate an integer Array containing an arithmetic progression. A port of
// the native Python `range()` function. See
// [the Python documentation](https://docs.python.org/library/functions.html#range).
function range(start, stop, step) {
  if (stop == null) {
    stop = start || 0;
    start = 0;
  }

  if (!step) {
    step = stop < start ? -1 : 1;
  }

  var length = Math.max(Math.ceil((stop - start) / step), 0);
  var range = Array(length);

  for (var idx = 0; idx < length; idx++, start += step) {
    range[idx] = start;
  }

  return range;
}

/***/ }),

/***/ "./node_modules/underscore/modules/reduce.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/reduce.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createReduce_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createReduce.js */ "./node_modules/underscore/modules/_createReduce.js");
 // **Reduce** builds up a single result from a list of values, aka `inject`,
// or `foldl`.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createReduce_js__WEBPACK_IMPORTED_MODULE_0__["default"])(1));

/***/ }),

/***/ "./node_modules/underscore/modules/reduceRight.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/reduceRight.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createReduce_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createReduce.js */ "./node_modules/underscore/modules/_createReduce.js");
 // The right-associative version of reduce, also known as `foldr`.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createReduce_js__WEBPACK_IMPORTED_MODULE_0__["default"])(-1));

/***/ }),

/***/ "./node_modules/underscore/modules/reject.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/reject.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return reject; });
/* harmony import */ var _filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./filter.js */ "./node_modules/underscore/modules/filter.js");
/* harmony import */ var _negate_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./negate.js */ "./node_modules/underscore/modules/negate.js");
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");


 // Return all the elements for which a truth test fails.

function reject(obj, predicate, context) {
  return Object(_filter_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, Object(_negate_js__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_cb_js__WEBPACK_IMPORTED_MODULE_2__["default"])(predicate)), context);
}

/***/ }),

/***/ "./node_modules/underscore/modules/rest.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/rest.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return rest; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // Returns everything but the first entry of the `array`. Especially useful on
// the `arguments` object. Passing an **n** will return the rest N values in the
// `array`.

function rest(array, n, guard) {
  return _setup_js__WEBPACK_IMPORTED_MODULE_0__["slice"].call(array, n == null || guard ? 1 : n);
}

/***/ }),

/***/ "./node_modules/underscore/modules/restArguments.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/restArguments.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return restArguments; });
// Some functions take a variable number of arguments, or a few expected
// arguments at the beginning and then a variable number of values to operate
// on. This helper accumulates all remaining arguments past the function’s
// argument length (or an explicit `startIndex`), into an array that becomes
// the last argument. Similar to ES6’s "rest parameter".
function restArguments(func, startIndex) {
  startIndex = startIndex == null ? func.length - 1 : +startIndex;
  return function () {
    var length = Math.max(arguments.length - startIndex, 0),
        rest = Array(length),
        index = 0;

    for (; index < length; index++) {
      rest[index] = arguments[index + startIndex];
    }

    switch (startIndex) {
      case 0:
        return func.call(this, rest);

      case 1:
        return func.call(this, arguments[0], rest);

      case 2:
        return func.call(this, arguments[0], arguments[1], rest);
    }

    var args = Array(startIndex + 1);

    for (index = 0; index < startIndex; index++) {
      args[index] = arguments[index];
    }

    args[startIndex] = rest;
    return func.apply(this, args);
  };
}

/***/ }),

/***/ "./node_modules/underscore/modules/result.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/result.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return result; });
/* harmony import */ var _isFunction_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isFunction.js */ "./node_modules/underscore/modules/isFunction.js");
/* harmony import */ var _toPath_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_toPath.js */ "./node_modules/underscore/modules/_toPath.js");

 // Traverses the children of `obj` along `path`. If a child is a function, it
// is invoked with its parent as context. Returns the value of the final
// child, or `fallback` if any child is undefined.

function result(obj, path, fallback) {
  path = Object(_toPath_js__WEBPACK_IMPORTED_MODULE_1__["default"])(path);
  var length = path.length;

  if (!length) {
    return Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_0__["default"])(fallback) ? fallback.call(obj) : fallback;
  }

  for (var i = 0; i < length; i++) {
    var prop = obj == null ? void 0 : obj[path[i]];

    if (prop === void 0) {
      prop = fallback;
      i = length; // Ensure we don't continue iterating.
    }

    obj = Object(_isFunction_js__WEBPACK_IMPORTED_MODULE_0__["default"])(prop) ? prop.call(obj) : prop;
  }

  return obj;
}

/***/ }),

/***/ "./node_modules/underscore/modules/sample.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/sample.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return sample; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _clone_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./clone.js */ "./node_modules/underscore/modules/clone.js");
/* harmony import */ var _values_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./values.js */ "./node_modules/underscore/modules/values.js");
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _random_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./random.js */ "./node_modules/underscore/modules/random.js");




 // Sample **n** random values from a collection using the modern version of the
// [Fisher-Yates shuffle](https://en.wikipedia.org/wiki/Fisher–Yates_shuffle).
// If **n** is not specified, returns a single random element.
// The internal `guard` argument allows it to work with `_.map`.

function sample(obj, n, guard) {
  if (n == null || guard) {
    if (!Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj)) obj = Object(_values_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj);
    return obj[Object(_random_js__WEBPACK_IMPORTED_MODULE_4__["default"])(obj.length - 1)];
  }

  var sample = Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj) ? Object(_clone_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) : Object(_values_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj);
  var length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_3__["default"])(sample);
  n = Math.max(Math.min(n, length), 0);
  var last = length - 1;

  for (var index = 0; index < n; index++) {
    var rand = Object(_random_js__WEBPACK_IMPORTED_MODULE_4__["default"])(index, last);
    var temp = sample[index];
    sample[index] = sample[rand];
    sample[rand] = temp;
  }

  return sample.slice(0, n);
}

/***/ }),

/***/ "./node_modules/underscore/modules/shuffle.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/shuffle.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return shuffle; });
/* harmony import */ var _sample_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./sample.js */ "./node_modules/underscore/modules/sample.js");
 // Shuffle a collection.

function shuffle(obj) {
  return Object(_sample_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, Infinity);
}

/***/ }),

/***/ "./node_modules/underscore/modules/size.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/size.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return size; });
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");

 // Return the number of elements in a collection.

function size(obj) {
  if (obj == null) return 0;
  return Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj) ? obj.length : Object(_keys_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj).length;
}

/***/ }),

/***/ "./node_modules/underscore/modules/some.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/some.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return some; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");


 // Determine if at least one element in the object passes a truth test.

function some(obj, predicate, context) {
  predicate = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(predicate, context);

  var _keys = !Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) && Object(_keys_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj),
      length = (_keys || obj).length;

  for (var index = 0; index < length; index++) {
    var currentKey = _keys ? _keys[index] : index;
    if (predicate(obj[currentKey], currentKey, obj)) return true;
  }

  return false;
}

/***/ }),

/***/ "./node_modules/underscore/modules/sortBy.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/sortBy.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return sortBy; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _pluck_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./pluck.js */ "./node_modules/underscore/modules/pluck.js");
/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./map.js */ "./node_modules/underscore/modules/map.js");


 // Sort the object's values by a criterion produced by an iteratee.

function sortBy(obj, iteratee, context) {
  var index = 0;
  iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context);
  return Object(_pluck_js__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_map_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj, function (value, key, list) {
    return {
      value: value,
      index: index++,
      criteria: iteratee(value, key, list)
    };
  }).sort(function (left, right) {
    var a = left.criteria;
    var b = right.criteria;

    if (a !== b) {
      if (a > b || a === void 0) return 1;
      if (a < b || b === void 0) return -1;
    }

    return left.index - right.index;
  }), 'value');
}

/***/ }),

/***/ "./node_modules/underscore/modules/sortedIndex.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore/modules/sortedIndex.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return sortedIndex; });
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");

 // Use a comparator function to figure out the smallest index at which
// an object should be inserted so as to maintain order. Uses binary search.

function sortedIndex(array, obj, iteratee, context) {
  iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context, 1);
  var value = iteratee(obj);
  var low = 0,
      high = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_1__["default"])(array);

  while (low < high) {
    var mid = Math.floor((low + high) / 2);
    if (iteratee(array[mid]) < value) low = mid + 1;else high = mid;
  }

  return low;
}

/***/ }),

/***/ "./node_modules/underscore/modules/tap.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/tap.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return tap; });
// Invokes `interceptor` with the `obj` and then returns `obj`.
// The primary purpose of this method is to "tap into" a method chain, in
// order to perform operations on intermediate results within the chain.
function tap(obj, interceptor) {
  interceptor(obj);
  return obj;
}

/***/ }),

/***/ "./node_modules/underscore/modules/template.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/template.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return template; });
/* harmony import */ var _defaults_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defaults.js */ "./node_modules/underscore/modules/defaults.js");
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _templateSettings_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./templateSettings.js */ "./node_modules/underscore/modules/templateSettings.js");


 // When customizing `_.templateSettings`, if you don't want to define an
// interpolation, evaluation or escaping regex, we need one that is
// guaranteed not to match.

var noMatch = /(.)^/; // Certain characters need to be escaped so that they can be put into a
// string literal.

var escapes = {
  "'": "'",
  '\\': '\\',
  '\r': 'r',
  '\n': 'n',
  "\u2028": 'u2028',
  "\u2029": 'u2029'
};
var escapeRegExp = /\\|'|\r|\n|\u2028|\u2029/g;

function escapeChar(match) {
  return '\\' + escapes[match];
} // In order to prevent third-party code injection through
// `_.templateSettings.variable`, we test it against the following regular
// expression. It is intentionally a bit more liberal than just matching valid
// identifiers, but still prevents possible loopholes through defaults or
// destructuring assignment.


var bareIdentifier = /^\s*(\w|\$)+\s*$/; // JavaScript micro-templating, similar to John Resig's implementation.
// Underscore templating handles arbitrary delimiters, preserves whitespace,
// and correctly escapes quotes within interpolated code.
// NB: `oldSettings` only exists for backwards compatibility.

function template(text, settings, oldSettings) {
  if (!settings && oldSettings) settings = oldSettings;
  settings = Object(_defaults_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, settings, _underscore_js__WEBPACK_IMPORTED_MODULE_1__["default"].templateSettings); // Combine delimiters into one regular expression via alternation.

  var matcher = RegExp([(settings.escape || noMatch).source, (settings.interpolate || noMatch).source, (settings.evaluate || noMatch).source].join('|') + '|$', 'g'); // Compile the template source, escaping string literals appropriately.

  var index = 0;
  var source = "__p+='";
  text.replace(matcher, function (match, escape, interpolate, evaluate, offset) {
    source += text.slice(index, offset).replace(escapeRegExp, escapeChar);
    index = offset + match.length;

    if (escape) {
      source += "'+\n((__t=(" + escape + "))==null?'':_.escape(__t))+\n'";
    } else if (interpolate) {
      source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
    } else if (evaluate) {
      source += "';\n" + evaluate + "\n__p+='";
    } // Adobe VMs need the match returned to produce the correct offset.


    return match;
  });
  source += "';\n";
  var argument = settings.variable;

  if (argument) {
    // Insure against third-party code injection. (CVE-2021-23358)
    if (!bareIdentifier.test(argument)) throw new Error('variable is not a bare identifier: ' + argument);
  } else {
    // If a variable is not specified, place data values in local scope.
    source = 'with(obj||{}){\n' + source + '}\n';
    argument = 'obj';
  }

  source = "var __t,__p='',__j=Array.prototype.join," + "print=function(){__p+=__j.call(arguments,'');};\n" + source + 'return __p;\n';
  var render;

  try {
    render = new Function(argument, '_', source);
  } catch (e) {
    e.source = source;
    throw e;
  }

  var template = function template(data) {
    return render.call(this, data, _underscore_js__WEBPACK_IMPORTED_MODULE_1__["default"]);
  }; // Provide the compiled source as a convenience for precompilation.


  template.source = 'function(' + argument + '){\n' + source + '}';
  return template;
}

/***/ }),

/***/ "./node_modules/underscore/modules/templateSettings.js":
/*!*************************************************************!*\
  !*** ./node_modules/underscore/modules/templateSettings.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
 // By default, Underscore uses ERB-style template delimiters. Change the
// following template settings to use alternative delimiters.

/* harmony default export */ __webpack_exports__["default"] = (_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].templateSettings = {
  evaluate: /<%([\s\S]+?)%>/g,
  interpolate: /<%=([\s\S]+?)%>/g,
  escape: /<%-([\s\S]+?)%>/g
});

/***/ }),

/***/ "./node_modules/underscore/modules/throttle.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/throttle.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return throttle; });
/* harmony import */ var _now_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./now.js */ "./node_modules/underscore/modules/now.js");
 // Returns a function, that, when invoked, will only be triggered at most once
// during a given window of time. Normally, the throttled function will run
// as much as it can, without ever going more than once per `wait` duration;
// but if you'd like to disable the execution on the leading edge, pass
// `{leading: false}`. To disable execution on the trailing edge, ditto.

function throttle(func, wait, options) {
  var timeout, context, args, result;
  var previous = 0;
  if (!options) options = {};

  var later = function later() {
    previous = options.leading === false ? 0 : Object(_now_js__WEBPACK_IMPORTED_MODULE_0__["default"])();
    timeout = null;
    result = func.apply(context, args);
    if (!timeout) context = args = null;
  };

  var throttled = function throttled() {
    var _now = Object(_now_js__WEBPACK_IMPORTED_MODULE_0__["default"])();

    if (!previous && options.leading === false) previous = _now;
    var remaining = wait - (_now - previous);
    context = this;
    args = arguments;

    if (remaining <= 0 || remaining > wait) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }

      previous = _now;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }

    return result;
  };

  throttled.cancel = function () {
    clearTimeout(timeout);
    previous = 0;
    timeout = context = args = null;
  };

  return throttled;
}

/***/ }),

/***/ "./node_modules/underscore/modules/times.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/times.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return times; });
/* harmony import */ var _optimizeCb_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_optimizeCb.js */ "./node_modules/underscore/modules/_optimizeCb.js");
 // Run a function **n** times.

function times(n, iteratee, context) {
  var accum = Array(Math.max(0, n));
  iteratee = Object(_optimizeCb_js__WEBPACK_IMPORTED_MODULE_0__["default"])(iteratee, context, 1);

  for (var i = 0; i < n; i++) {
    accum[i] = iteratee(i);
  }

  return accum;
}

/***/ }),

/***/ "./node_modules/underscore/modules/toArray.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/toArray.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return toArray; });
/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _isString_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./isString.js */ "./node_modules/underscore/modules/isString.js");
/* harmony import */ var _isArrayLike_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_isArrayLike.js */ "./node_modules/underscore/modules/_isArrayLike.js");
/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./map.js */ "./node_modules/underscore/modules/map.js");
/* harmony import */ var _identity_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./identity.js */ "./node_modules/underscore/modules/identity.js");
/* harmony import */ var _values_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./values.js */ "./node_modules/underscore/modules/values.js");






 // Safely create a real, live array from anything iterable.

var reStrSymbol = /[^\ud800-\udfff]|[\ud800-\udbff][\udc00-\udfff]|[\ud800-\udfff]/g;
function toArray(obj) {
  if (!obj) return [];
  if (Object(_isArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj)) return _setup_js__WEBPACK_IMPORTED_MODULE_1__["slice"].call(obj);

  if (Object(_isString_js__WEBPACK_IMPORTED_MODULE_2__["default"])(obj)) {
    // Keep surrogate pair characters together.
    return obj.match(reStrSymbol);
  }

  if (Object(_isArrayLike_js__WEBPACK_IMPORTED_MODULE_3__["default"])(obj)) return Object(_map_js__WEBPACK_IMPORTED_MODULE_4__["default"])(obj, _identity_js__WEBPACK_IMPORTED_MODULE_5__["default"]);
  return Object(_values_js__WEBPACK_IMPORTED_MODULE_6__["default"])(obj);
}

/***/ }),

/***/ "./node_modules/underscore/modules/toPath.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/toPath.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return toPath; });
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _isArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isArray.js */ "./node_modules/underscore/modules/isArray.js");

 // Normalize a (deep) property `path` to array.
// Like `_.iteratee`, this function can be customized.

function toPath(path) {
  return Object(_isArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(path) ? path : [path];
}
_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].toPath = toPath;

/***/ }),

/***/ "./node_modules/underscore/modules/underscore-array-methods.js":
/*!*********************************************************************!*\
  !*** ./node_modules/underscore/modules/underscore-array-methods.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _underscore_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./underscore.js */ "./node_modules/underscore/modules/underscore.js");
/* harmony import */ var _each_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./each.js */ "./node_modules/underscore/modules/each.js");
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
/* harmony import */ var _chainResult_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./_chainResult.js */ "./node_modules/underscore/modules/_chainResult.js");



 // Add all mutator `Array` functions to the wrapper.

Object(_each_js__WEBPACK_IMPORTED_MODULE_1__["default"])(['pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift'], function (name) {
  var method = _setup_js__WEBPACK_IMPORTED_MODULE_2__["ArrayProto"][name];

  _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].prototype[name] = function () {
    var obj = this._wrapped;

    if (obj != null) {
      method.apply(obj, arguments);

      if ((name === 'shift' || name === 'splice') && obj.length === 0) {
        delete obj[0];
      }
    }

    return Object(_chainResult_js__WEBPACK_IMPORTED_MODULE_3__["default"])(this, obj);
  };
}); // Add all accessor `Array` functions to the wrapper.

Object(_each_js__WEBPACK_IMPORTED_MODULE_1__["default"])(['concat', 'join', 'slice'], function (name) {
  var method = _setup_js__WEBPACK_IMPORTED_MODULE_2__["ArrayProto"][name];

  _underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"].prototype[name] = function () {
    var obj = this._wrapped;
    if (obj != null) obj = method.apply(obj, arguments);
    return Object(_chainResult_js__WEBPACK_IMPORTED_MODULE_3__["default"])(this, obj);
  };
});
/* harmony default export */ __webpack_exports__["default"] = (_underscore_js__WEBPACK_IMPORTED_MODULE_0__["default"]);

/***/ }),

/***/ "./node_modules/underscore/modules/underscore.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore/modules/underscore.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _; });
/* harmony import */ var _setup_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_setup.js */ "./node_modules/underscore/modules/_setup.js");
 // If Underscore is called as a function, it returns a wrapped object that can
// be used OO-style. This wrapper holds altered versions of all functions added
// through `_.mixin`. Wrapped objects may be chained.

function _(obj) {
  if (obj instanceof _) return obj;
  if (!(this instanceof _)) return new _(obj);
  this._wrapped = obj;
}
_.VERSION = _setup_js__WEBPACK_IMPORTED_MODULE_0__["VERSION"]; // Extracts the result from a wrapped and chained object.

_.prototype.value = function () {
  return this._wrapped;
}; // Provide unwrapping proxies for some methods used in engine operations
// such as arithmetic and JSON stringification.


_.prototype.valueOf = _.prototype.toJSON = _.prototype.value;

_.prototype.toString = function () {
  return String(this._wrapped);
};

/***/ }),

/***/ "./node_modules/underscore/modules/unescape.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/unescape.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createEscaper_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./_createEscaper.js */ "./node_modules/underscore/modules/_createEscaper.js");
/* harmony import */ var _unescapeMap_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_unescapeMap.js */ "./node_modules/underscore/modules/_unescapeMap.js");

 // Function for unescaping strings from HTML interpolation.

/* harmony default export */ __webpack_exports__["default"] = (Object(_createEscaper_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_unescapeMap_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./node_modules/underscore/modules/union.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/union.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _uniq_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./uniq.js */ "./node_modules/underscore/modules/uniq.js");
/* harmony import */ var _flatten_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_flatten.js */ "./node_modules/underscore/modules/_flatten.js");


 // Produce an array that contains the union: each distinct element from all of
// the passed-in arrays.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (arrays) {
  return Object(_uniq_js__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_flatten_js__WEBPACK_IMPORTED_MODULE_2__["default"])(arrays, true, true));
}));

/***/ }),

/***/ "./node_modules/underscore/modules/uniq.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/uniq.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return uniq; });
/* harmony import */ var _isBoolean_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isBoolean.js */ "./node_modules/underscore/modules/isBoolean.js");
/* harmony import */ var _cb_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_cb.js */ "./node_modules/underscore/modules/_cb.js");
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _contains_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./contains.js */ "./node_modules/underscore/modules/contains.js");



 // Produce a duplicate-free version of the array. If the array has already
// been sorted, you have the option of using a faster algorithm.
// The faster algorithm will not work with an iteratee if the iteratee
// is not a one-to-one function, so providing an iteratee will disable
// the faster algorithm.

function uniq(array, isSorted, iteratee, context) {
  if (!Object(_isBoolean_js__WEBPACK_IMPORTED_MODULE_0__["default"])(isSorted)) {
    context = iteratee;
    iteratee = isSorted;
    isSorted = false;
  }

  if (iteratee != null) iteratee = Object(_cb_js__WEBPACK_IMPORTED_MODULE_1__["default"])(iteratee, context);
  var result = [];
  var seen = [];

  for (var i = 0, length = Object(_getLength_js__WEBPACK_IMPORTED_MODULE_2__["default"])(array); i < length; i++) {
    var value = array[i],
        computed = iteratee ? iteratee(value, i, array) : value;

    if (isSorted && !iteratee) {
      if (!i || seen !== computed) result.push(value);
      seen = computed;
    } else if (iteratee) {
      if (!Object(_contains_js__WEBPACK_IMPORTED_MODULE_3__["default"])(seen, computed)) {
        seen.push(computed);
        result.push(value);
      }
    } else if (!Object(_contains_js__WEBPACK_IMPORTED_MODULE_3__["default"])(result, value)) {
      result.push(value);
    }
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/uniqueId.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore/modules/uniqueId.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return uniqueId; });
// Generate a unique integer id (unique within the entire client session).
// Useful for temporary DOM ids.
var idCounter = 0;
function uniqueId(prefix) {
  var id = ++idCounter + '';
  return prefix ? prefix + id : id;
}

/***/ }),

/***/ "./node_modules/underscore/modules/unzip.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/unzip.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return unzip; });
/* harmony import */ var _max_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./max.js */ "./node_modules/underscore/modules/max.js");
/* harmony import */ var _getLength_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./_getLength.js */ "./node_modules/underscore/modules/_getLength.js");
/* harmony import */ var _pluck_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./pluck.js */ "./node_modules/underscore/modules/pluck.js");


 // Complement of zip. Unzip accepts an array of arrays and groups
// each array's elements on shared indices.

function unzip(array) {
  var length = array && Object(_max_js__WEBPACK_IMPORTED_MODULE_0__["default"])(array, _getLength_js__WEBPACK_IMPORTED_MODULE_1__["default"]).length || 0;
  var result = Array(length);

  for (var index = 0; index < length; index++) {
    result[index] = Object(_pluck_js__WEBPACK_IMPORTED_MODULE_2__["default"])(array, index);
  }

  return result;
}

/***/ }),

/***/ "./node_modules/underscore/modules/values.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore/modules/values.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return values; });
/* harmony import */ var _keys_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./keys.js */ "./node_modules/underscore/modules/keys.js");
 // Retrieve the values of an object's properties.

function values(obj) {
  var _keys = Object(_keys_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj);

  var length = _keys.length;
  var values = Array(length);

  for (var i = 0; i < length; i++) {
    values[i] = obj[_keys[i]];
  }

  return values;
}

/***/ }),

/***/ "./node_modules/underscore/modules/where.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/where.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return where; });
/* harmony import */ var _filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./filter.js */ "./node_modules/underscore/modules/filter.js");
/* harmony import */ var _matcher_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./matcher.js */ "./node_modules/underscore/modules/matcher.js");

 // Convenience version of a common use case of `_.filter`: selecting only
// objects containing specific `key:value` pairs.

function where(obj, attrs) {
  return Object(_filter_js__WEBPACK_IMPORTED_MODULE_0__["default"])(obj, Object(_matcher_js__WEBPACK_IMPORTED_MODULE_1__["default"])(attrs));
}

/***/ }),

/***/ "./node_modules/underscore/modules/without.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore/modules/without.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _difference_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./difference.js */ "./node_modules/underscore/modules/difference.js");

 // Return a version of the array that does not contain the specified value(s).

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(function (array, otherArrays) {
  return Object(_difference_js__WEBPACK_IMPORTED_MODULE_1__["default"])(array, otherArrays);
}));

/***/ }),

/***/ "./node_modules/underscore/modules/wrap.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore/modules/wrap.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return wrap; });
/* harmony import */ var _partial_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./partial.js */ "./node_modules/underscore/modules/partial.js");
 // Returns the first function passed as an argument to the second,
// allowing you to adjust arguments, run code before and after, and
// conditionally execute the original function.

function wrap(func, wrapper) {
  return Object(_partial_js__WEBPACK_IMPORTED_MODULE_0__["default"])(wrapper, func);
}

/***/ }),

/***/ "./node_modules/underscore/modules/zip.js":
/*!************************************************!*\
  !*** ./node_modules/underscore/modules/zip.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _restArguments_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./restArguments.js */ "./node_modules/underscore/modules/restArguments.js");
/* harmony import */ var _unzip_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./unzip.js */ "./node_modules/underscore/modules/unzip.js");

 // Zip together multiple lists into a single array -- elements that share
// an index go together.

/* harmony default export */ __webpack_exports__["default"] = (Object(_restArguments_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_unzip_js__WEBPACK_IMPORTED_MODULE_1__["default"]));

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

/***/ 15:
/*!*************************************************************************************!*\
  !*** multi ./node_modules/@concretecms/bedrock/assets/conversations/js/frontend.js ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\wamp64\www\concrete5\build\node_modules\@concretecms\bedrock\assets\conversations\js\frontend.js */"./node_modules/@concretecms/bedrock/assets/conversations/js/frontend.js");


/***/ })

/******/ });