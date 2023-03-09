/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/themes/concrete/js/login-tabs.js":
/*!*************************************************!*\
  !*** ./assets/themes/concrete/js/login-tabs.js ***!
  \*************************************************/
/***/ (() => {

(function ($) {
  "use strict";

  var forms = $('div.controls').find('div.authentication-type').hide(),
    select = $('div.ccm-authentication-type-select > select');
  var types = $('ul.auth-types > li').each(function () {
    var me = $(this),
      form = forms.filter('[data-handle="' + me.data('handle') + '"]');
    me.click(function () {
      select.val(me.data('handle'));
      if (typeof Concrete !== 'undefined') {
        Concrete.event.fire('AuthenticationTypeSelected', me.data('handle'));
      }
      if (form.hasClass('active')) return;
      types.removeClass('active');
      me.addClass('active');
      if (forms.filter('.active').length) {
        forms.stop().filter('.active').removeClass('active').fadeOut(250, function () {
          form.addClass('active').fadeIn(250);
        });
      } else {
        form.addClass('active').show();
      }
    });
  });
  select.change(function () {
    types.filter('[data-handle="' + $(this).val() + '"]').click();
  });
  types.first().click();
  $('ul.nav.nav-tabs > li > a').on('click', function () {
    var me = $(this);
    if (me.parent().hasClass('active')) return false;
    $('ul.nav.nav-tabs > li.active').removeClass('active');
    var at = me.attr('data-authType');
    me.parent().addClass('active');
    $('div.authTypes > div').hide().filter('[data-authType="' + at + '"]').show();
    return false;
  });
})(jQuery);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_components_AvatarCropper_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/components/AvatarCropper.vue */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue");
/* eslint-disable no-new */


$(function () {
  window.Concrete.Vue.createContext('frontend', {
    AvatarCropper: _frontend_components_AvatarCropper_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  });
  if (document.querySelectorAll('[data-view=account]').length) {
    Concrete.Vue.activateContext('frontend', function (Vue, config) {
      new Vue({
        el: '[data-view=account]',
        components: config.components
      });
    });
  }
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap */ "bootstrap");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(bootstrap__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _frontend_locations_country_data_link__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./frontend/locations/country-data-link */ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-data-link.js");
/* harmony import */ var _frontend_locations_country_data_link__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_frontend_locations_country_data_link__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _frontend_locations_country_stateprovince_link__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./frontend/locations/country-stateprovince-link */ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-stateprovince-link.js");
/* harmony import */ var _frontend_locations_country_stateprovince_link__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_frontend_locations_country_stateprovince_link__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _assets_cms_js_vue_Manager__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../assets/cms/js/vue/Manager */ "./node_modules/@concretecms/bedrock/assets/cms/js/vue/Manager.js");


// We will be refactoring this, also it causes problems with installation.
// import './frontend/async-thumbnail-builder'



// Let us use Vue with our theme JS

_assets_cms_js_vue_Manager__WEBPACK_IMPORTED_MODULE_4__["default"].bindToWindow(window);
window.$ = window.jQuery = (jquery__WEBPACK_IMPORTED_MODULE_0___default());

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-data-link.js":
/*!*****************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-data-link.js ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var USE_MUTATIONOBSERVER = !!(window.MutationObserver && window.MutationObserver.prototype && window.MutationObserver.prototype.observe);
function loadDataForCountry(countryCode, callback) {
  if (typeof countryCode !== 'string' || $.trim(countryCode) === '') {
    callback(countryCode, {
      statesProvices: {},
      addressUsedFields: []
    });
    return;
  }
  if (Object.prototype.hasOwnProperty.call(loadDataForCountry.cache, countryCode)) {
    callback(countryCode, loadDataForCountry.cache[countryCode]);
    return;
  }
  callback(countryCode, {
    statesProvices: {},
    addressUsedFields: []
  });
  $.ajax({
    cache: true,
    // Needed because we may change the current locale
    data: {
      countryCode: countryCode,
      activeLocale: CCM_ACTIVE_LOCALE
    },
    dataType: 'json',
    method: 'GET',
    url: CCM_DISPATCHER_FILENAME + '/ccm/system/country-data-link/all'
  }).done(function (data) {
    var statesProvinces = {};
    if (data.statesProvices instanceof Object) {
      statesProvinces = data.statesProvices;
    }
    var addressUsedFields = [];
    if (data.addressUsedFields instanceof Array) {
      addressUsedFields = data.addressUsedFields;
    }
    loadDataForCountry.cache[countryCode] = {
      statesProvices: statesProvinces,
      addressUsedFields: addressUsedFields
    };
  }).fail(function (xhr, status, error) {
    if (window.console && window.console.error) {
      window.console.error(xhr.responseJSON || error);
    }
    loadDataForCountry.cache[countryCode] = {
      statesProvices: {},
      addressUsedFields: []
    };
  }).always(function () {
    callback(countryCode, loadDataForCountry.cache[countryCode]);
  });
}
loadDataForCountry.cache = {};
function TextReplacer($text) {
  var me = this;
  me.enabled = false;
  me.$text = $text;
  me.$select = $('<select />');
  if (USE_MUTATIONOBSERVER) {
    me.mutationObserver = new window.MutationObserver(function (records) {
      me.updateSelectAttributes();
      me.$text.hide();
      me.$select.show();
    });
  } else {
    me.mutationObserver = null;
  }
  me.originalFocus = me.$text[0].focus;
  me.$text[0].focus = function () {
    if (me.enabled) {
      me.$select.focus();
    } else {
      me.originalFocus.apply(me.$text[0]);
    }
  };
}
TextReplacer.prototype = {
  updateSelectAttributes: function updateSelectAttributes() {
    var me = this;
    $.each(['class', 'style', 'required'], function (index, attributeName) {
      var attributeValue = me.$text.attr(attributeName);
      if (typeof attributeValue === 'string') {
        me.$select.attr(attributeName, attributeValue);
      }
    });
  },
  setEnabled: function setEnabled(enable) {
    var me = this;
    enable = !!enable;
    if (enable === me.enabled) {
      return;
    }
    if (enable) {
      me.updateSelectAttributes();
      me.$text.before(me.$select);
      me.$text.hide();
      me.enabled = true;
      if (me.mutationObserver !== null) {
        setTimeout(function () {
          if (me.enabled !== true) {
            return;
          }
          me.mutationObserver.disconnect();
          me.mutationObserver.observe(me.$text[0], {
            attributes: true
          });
        }, 0);
      }
    } else {
      if (me.mutationObserver !== null) {
        me.mutationObserver.disconnect();
      }
      me.enabled = false;
      me.$select.detach();
      me.$text.show();
    }
  }
};
function Link($country, $stateprovince, config) {
  var me = this;
  me.$country = $country;
  me.$stateprovinceWrapper = $stateprovince;
  if ($stateprovince.is('input')) {
    me.$stateprovince = $stateprovince;
  } else {
    me.$stateprovince = $stateprovince.find('input:first');
  }
  me.config = config;
  me.replacer = new TextReplacer(me.$stateprovince);
  me.$stateprovinceSelect = me.replacer.$select;
  me.$country.on('change', function () {
    me.countryChanged();
  });
  me.$stateprovinceSelect.on('change', function () {
    me.$stateprovince.val(me.$stateprovinceSelect.val()).trigger('change');
  });
  me.countryChanged(true);
}
Link.prototype = {
  countryChanged: function countryChanged(initializing) {
    var me = this;
    loadDataForCountry(me.$country.val(), function (countryCode, countryData) {
      if (me.$country.val() !== countryCode) {
        return;
      }
      me.$stateprovinceSelect.empty();
      if (!initializing && me.config.clearStateProvinceOnChange) {
        me.$stateprovince.val('');
      }
      if (me.config.hideUnusedStateProvinceField) {
        if (countryData.addressUsedFields.indexOf('state_province') > -1) {
          me.$stateprovinceWrapper.show();
        } else {
          me.$stateprovinceWrapper.hide();
        }
      }
      var n = Object.keys(countryData.statesProvices).length;
      if (n === 0) {
        me.replacer.setEnabled(false);
      } else {
        var selectedStateprovince = $.trim(me.$stateprovince.val());
        me.$stateprovinceSelect.append($('<option value="" selected="selected" />').text(''));
        $.each(countryData.statesProvices, function (spCode, name) {
          var $o = $('<option />').val(spCode).text(name);
          if (spCode === selectedStateprovince) {
            $o.attr('selected', 'selected');
          }
          me.$stateprovinceSelect.append($o);
        });
        me.replacer.setEnabled(true);
      }
      me.$country.trigger('country-data', [countryData]);
    });
  }
};
Link.withCountryField = function ($country, config) {
  config = $.extend({
    hideUnusedStateProvinceField: false,
    clearStateProvinceOnChange: false
  }, config);
  var $parent = $country.closest('form');
  if ($parent.length === 0) {
    $parent = $(document.body);
  }
  var result = [];
  $parent.find('[data-countryfield="' + $country.attr('id') + '"]').each(function () {
    result.push(new Link($country, $(this), config));
  });
  switch (result.length) {
    case 0:
      return null;
    case 1:
      return result[0];
    default:
      return result;
  }
};
__webpack_require__.g.ConcreteCountryDataLink = Link;

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-stateprovince-link.js":
/*!**************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-stateprovince-link.js ***!
  \**************************************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var USE_MUTATIONOBSERVER = !!(window.MutationObserver && window.MutationObserver.prototype && window.MutationObserver.prototype.observe);
function loadStateprovincesForCountry(countryCode, callback) {
  if (typeof countryCode !== 'string' || $.trim(countryCode) === '') {
    callback(countryCode, []);
    return;
  }
  if (Object.prototype.hasOwnProperty.call(loadStateprovincesForCountry.cache, countryCode)) {
    callback(countryCode, loadStateprovincesForCountry.cache[countryCode]);
    return;
  }
  callback(countryCode, []);
  $.ajax({
    cache: true,
    // Needed because we may change the current locale
    data: {
      countryCode: countryCode,
      activeLocale: CCM_ACTIVE_LOCALE
    },
    dataType: 'json',
    method: 'GET',
    url: CCM_DISPATCHER_FILENAME + '/ccm/system/country-stateprovince-link/get_stateprovinces'
  }).fail(function (xhr, status, error) {
    if (window.console && window.console.error) {
      window.console.error(xhr.responseJSON || error);
    }
    loadStateprovincesForCountry.cache[countryCode] = [];
  }).success(function (data) {
    loadStateprovincesForCountry.cache[countryCode] = data instanceof Array ? data : [];
  }).always(function () {
    callback(countryCode, loadStateprovincesForCountry.cache[countryCode]);
  });
}
loadStateprovincesForCountry.cache = {};
function TextReplacer($text) {
  var me = this;
  me.enabled = false;
  me.$text = $text;
  me.$select = $('<select />');
  if (USE_MUTATIONOBSERVER) {
    me.mutationObserver = new window.MutationObserver(function (records) {
      me.updateSelectAttributes();
      me.$text.hide();
      me.$select.show();
    });
  } else {
    me.mutationObserver = null;
  }
  me.originalFocus = me.$text[0].focus;
  me.$text[0].focus = function () {
    if (me.enabled) {
      me.$select.focus();
    } else {
      me.originalFocus.apply(me.$text[0]);
    }
  };
}
TextReplacer.prototype = {
  updateSelectAttributes: function updateSelectAttributes() {
    var me = this;
    $.each(['class', 'style', 'required'], function (index, attributeName) {
      var attributeValue = me.$text.attr(attributeName);
      if (typeof attributeValue === 'string') {
        me.$select.attr(attributeName, attributeValue);
      }
    });
  },
  setEnabled: function setEnabled(enable) {
    var me = this;
    enable = !!enable;
    if (enable === me.enabled) {
      return;
    }
    if (enable) {
      me.updateSelectAttributes();
      me.$text.before(me.$select);
      me.$text.hide();
      me.enabled = true;
      if (me.mutationObserver !== null) {
        setTimeout(function () {
          if (me.enabled !== true) {
            return;
          }
          me.mutationObserver.disconnect();
          me.mutationObserver.observe(me.$text[0], {
            attributes: true
          });
        }, 0);
      }
    } else {
      if (me.mutationObserver !== null) {
        me.mutationObserver.disconnect();
      }
      me.enabled = false;
      me.$select.detach();
      me.$text.show();
    }
  }
};
function Link($country, $stateprovince) {
  var me = this;
  me.$country = $country;
  me.$stateprovince = $stateprovince;
  me.replacer = new TextReplacer(me.$stateprovince);
  me.$stateprovinceSelect = me.replacer.$select;
  me.$country.on('change', function () {
    me.countryChanged();
  }).trigger('change');
  me.$stateprovinceSelect.on('change', function () {
    me.$stateprovince.val(me.$stateprovinceSelect.val()).trigger('change');
  });
}
Link.prototype = {
  countryChanged: function countryChanged() {
    var me = this;
    loadStateprovincesForCountry(me.$country.val(), function (countryCode, stateprovinceList) {
      if (me.$country.val() !== countryCode) {
        return;
      }
      me.$stateprovinceSelect.empty();
      var n = stateprovinceList.length;
      if (n === 0) {
        me.replacer.setEnabled(false);
      } else {
        var selectedStateprovince = $.trim(me.$stateprovince.val());
        me.$stateprovinceSelect.append($('<option value="" selected="selected" />').text(''));
        for (var i = 0, $o; i < n; i++) {
          $o = $('<option />').val(stateprovinceList[i][0]).text(stateprovinceList[i][1]);
          if (stateprovinceList[i][0] === selectedStateprovince) {
            $o.attr('selected', 'selected');
          }
          me.$stateprovinceSelect.append($o);
        }
        me.replacer.setEnabled(true);
      }
    });
  }
};
Link.withCountryField = function ($country) {
  var $parent = $country.closest('form');
  if ($parent.length === 0) {
    $parent = $(document.body);
  }
  var result = [];
  $parent.find('input[data-countryfield="' + $country.attr('id') + '"]').each(function () {
    result.push(new Link($country, $(this)));
  });
  switch (result.length) {
    case 0:
      return null;
    case 1:
      return result[0];
    default:
      return result;
  }
};
__webpack_require__.g.ConcreteCountryStateprovinceLink = Link;

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/legacy-dialog.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/legacy-dialog.js ***!
  \**************************************************************************/
/***/ (() => {

/* eslint-disable no-new, no-unused-vars, camelcase, no-eval, eqeqeq */
/* global NProgress, ccmi18n, ConcreteMenuManager, ConcreteAjaxRequest, ConcreteAlert, bootstrap */

;
(function (global, $) {
  'use strict';

  /* Concrete wrapper for jQuery UI */
  $.widget('concrete.dialog', $.ui.dialog, {
    _allowInteraction: function _allowInteraction(event) {
      return !!$(event.target).closest('.ccm-interaction-dialog').length || !!$(event.target).closest('.cke_dialog').length || this._super(event);
    }
  });
  function onDialogCreate($dialog) {
    // $dialog.parent().addClass('animated fadeIn')
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
      var overlayFunction = function overlayFunction() {
        var overlay = $('.ui-widget-overlay').get(0);
        overlay.classList.add('ui-widget-overlay-active');
      };
      requestAnimationFrame(overlayFunction);
    }
    var $close = $dialog.parent().find('.ui-dialog-titlebar-close');
    $close.addClass('btn-close btn-close-white');
    $.fn.dialog.activateDialogContents($dialog);

    // on some brother (eg: Chrome) the resizable get hidden because the button pane
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
    // $ccmButtons.find('[data-dialog-action=cancel]').addClass('me-auto')
    $ccmButtons.children().appendTo($dialogParent.find('.ui-dialog-buttonpane').empty());
  }
  $.widget.bridge('jqdialog', $.concrete.dialog);
  // wrap our old dialog function in the new dialog() function.
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
    }
    // LEGACY SUPPORT
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
          $.fn.dialog.hideLoader();
          // note the order here is very important in order to actually run javascript in
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
    setTimeout(function () {
      // handle buttons
      $dialog.find('button[data-dialog-action=cancel]').on('click', function () {
        $.fn.dialog.closeTop();
      });
      $dialog.find('[data-dialog-form]').each(function () {
        var $form = $(this);
        var options = {};
        if ($form.attr('data-dialog-form-processing') == 'progressive') {
          options.progressiveOperation = true;
          options.progressiveOperationElement = 'div[data-dialog-form-element=progress-bar]';
        }
        $form.concreteAjaxForm(options);
      });
      $dialog.find('button[data-dialog-action=submit]').on('click', function () {
        $dialog.find('[data-dialog-form]').submit();
      });
      fixDialogButtons($dialog);

      // make dialogs
      $dialog.find('.dialog-launch').dialog();

      // Handle vue components within
      $dialog.find('[data-vue]').each(function () {
        $(this).concreteVue({
          context: $(this).attr('data-vue')
        });
      });

      // automated close handling
      $dialog.find('.ccm-dialog-close').on('click', function () {
        $dialog.dialog('close');
      });
      var tooltipTriggerList = [].slice.call($dialog.find('.launch-tooltip'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
          container: '#ccm-tooltip-holder'
        });
      });

      // help handling
      if ($dialog.find('.dialog-help').length > 0) {
        $dialog.find('.dialog-help').hide();
        var helpContent = $dialog.find('.dialog-help').html();
        var helpText;
        if (ccmi18n.helpPopup) {
          helpText = ccmi18n.helpPopup;
        } else {
          helpText = 'Help';
        }
        var button = $('<button class="btn-help"><svg><use xlink:href="#icon-dialog-help" /></svg></button>');
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
    }, 10);
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

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/vue/Manager.js":
/*!************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/vue/Manager.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Manager)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }


/**
 * Typescript interface
 *\/
 interface Context {
    [key: String]: {} | Component
}
 /**/
var Manager = /*#__PURE__*/function () {
  /**
   * Create a new Manager
   *
   * @param {{[key: String]: Context}} contexts A list of component lists keyed by context name
   */
  function Manager(contexts) {
    _classCallCheck(this, Manager);
    this.contexts = contexts || {};
  }

  /**
   * Ensures that our Concrete.Vue manager is available on the window object.
   * Note: Do NOT call this before the global Concrete object is created in the CMS context.
   *
   * @param {Window} window
   */
  _createClass(Manager, [{
    key: "getContext",
    value:
    /**
     * Returns a list of components for the current string `context`
     *
     * @param {String} context
     * @returns {{[key: String]: {}}} A list of components keyed by their handle
     */
    function getContext(context) {
      return this.contexts[context] || {};
    }

    /**
     * Actives a particular context (and its components) for a particular selector.
     *
     * @param {String} context
     * @param {Function} callback (Vue, options) => new Vue(options)
     */
  }, {
    key: "activateContext",
    value: function activateContext(context, callback) {
      var _this = this;
      // This is stupid but sometimes activateContext and extendContext are called essentially simultaneously and we need this to fire after
      setTimeout(function () {
        return callback((vue__WEBPACK_IMPORTED_MODULE_0___default()), {
          components: _this.getContext(context)
        });
      }, 10);
    }

    /**
     * For a given string `context`, adds the passed components to make them available within that context.
     *
     * @param {String} context The name of the context to extend
     * @param {{[key: String]: {}}} components A list of component objects to add into the context
     * @param {String} newContext The new name of the context if different from context
     */
  }, {
    key: "extendContext",
    value: function extendContext(context, components, newContext) {
      newContext = newContext || context;
      this.contexts[newContext] = _objectSpread(_objectSpread({}, this.getContext(context)), components);
    }

    /**
     * Creates a Context object that has access to the specified components. If `fromContext` is passed, the new
     * context object will be created with the same components as the `fromContext` object.
     *
     * @param context
     * @param components
     * @param fromContext
     */
  }, {
    key: "createContext",
    value: function createContext(context, components, fromContext) {
      this.extendContext(fromContext, components, context);
    }
  }], [{
    key: "bindToWindow",
    value: function bindToWindow(window) {
      window.Concrete = window.Concrete || {};
      if (!window.Concrete.Vue) {
        window.Concrete.Vue = new Manager();
        window.dispatchEvent(new CustomEvent('concrete.vue.ready', {
          detail: window.Concrete.Vue
        }));
      }
    }
  }]);
  return Manager;
}();


/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/desktop/js/frontend.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery_form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery-form */ "./node_modules/@concretecms/bedrock/node_modules/jquery-form/dist/jquery.form.min.js");
/* harmony import */ var jquery_form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery_form__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery_ui_ui_widgets_dialog__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery-ui/ui/widgets/dialog */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/dialog.js");
/* harmony import */ var jquery_ui_ui_widgets_dialog__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery_ui_ui_widgets_dialog__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _cms_js_legacy_dialog__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../cms/js/legacy-dialog */ "./node_modules/@concretecms/bedrock/assets/cms/js/legacy-dialog.js");
/* harmony import */ var _cms_js_legacy_dialog__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_cms_js_legacy_dialog__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _frontend_draft_list__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./frontend/draft-list */ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/draft-list.js");
/* harmony import */ var _frontend_draft_list__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_frontend_draft_list__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _frontend_notification__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./frontend/notification */ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/notification.js");
/* harmony import */ var _frontend_notification__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_frontend_notification__WEBPACK_IMPORTED_MODULE_4__);
// draft list and notification make use ajaxSubmit


// draft list and notification make use of the CMS dialog.



// Components



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/draft-list.js":
/*!************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/draft-list.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

/* eslint-disable no-new, no-unused-vars, camelcase */

;
(function (global, $) {
  'use strict';

  function ConcreteDraftList($element, options) {
    var my = this;
    options = $.extend({}, options);
    my.$element = $element;
    my.options = options;
    my.$element.on('click', 'div.ccm-pagination-wrapper a', function (e) {
      e.preventDefault();
      my.showLoader();
      window.scrollTo(0, 0);
      $.concreteAjax({
        loader: false,
        dataType: 'html',
        url: $(this).attr('href'),
        method: 'get',
        success: function success(r) {
          my.$element.replaceWith(r);
        },
        complete: function complete() {
          my.hideLoader();
        }
      });
    });
    my.$element.find('.dialog-launch').dialog();
  }
  ConcreteDraftList.prototype = {
    showLoader: function showLoader() {
      var my = this;
      my.$element.find('.ccm-block-desktop-draft-list-for-me-loader').removeClass('invisible');
    },
    hideLoader: function hideLoader() {
      var my = this;
      my.$element.find('.ccm-block-desktop-draft-list-for-me-loader').addClass('invisible');
    }
  };

  // jQuery Plugin
  $.fn.concreteDraftList = function (options) {
    return $.each($(this), function (i, obj) {
      new ConcreteDraftList($(this), options);
    });
  };
  global.ConcreteDraftList = ConcreteDraftList;
})(__webpack_require__.g, jQuery);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/notification.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/notification.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

/* eslint-disable no-new, no-unused-vars, camelcase */
/* global CCM_DISPATCHER_FILENAME */

;
(function (global, $) {
  'use strict';

  function ConcreteNotificationList($element, options) {
    var my = this;
    options = $.extend({}, options);
    my.$element = $element;
    my.options = options;
    my.$element.on('click', '[data-notification-action=archive]', function (e) {
      e.preventDefault();
      var $item = $(this).closest('div[data-notification-alert-id]');
      var alertID = $item.attr('data-notification-alert-id');
      var token = $item.attr('data-token');
      $.ajax({
        url: CCM_DISPATCHER_FILENAME + '/ccm/system/notification/alert/archive',
        dataType: 'json',
        data: {
          naID: alertID,
          ccm_token: token
        },
        type: 'post'
      });
      $item.queue(function () {
        $item.addClass('animated fadeOut');
        $item.dequeue();
      }).delay(500).queue(function () {
        $item.remove();
        $item.dequeue();
        my.handleEmpty();
      });
    });
    my.$element.on('change', 'div[data-form=notification] select', function (e) {
      var $form = $(this).closest('form');
      $form.ajaxSubmit({
        dataType: 'html',
        beforeSubmit: function beforeSubmit() {
          my.showLoader();
        },
        success: function success(r) {
          $('div[data-wrapper=desktop-waiting-for-me]').replaceWith(r);
        },
        complete: function complete() {
          my.hideLoader();
        }
      });
    });
    my.$element.on('click', 'div.ccm-pagination-wrapper a', function (e) {
      e.preventDefault();
      my.showLoader();
      window.scrollTo(0, 0);
      $.concreteAjax({
        loader: false,
        dataType: 'html',
        url: $(this).attr('href'),
        method: 'get',
        success: function success(r) {
          $('div[data-wrapper=desktop-waiting-for-me]').replaceWith(r);
        },
        complete: function complete() {
          my.hideLoader();
        }
      });
    });
    my.$element.on('click', 'a[data-workflow-task]', function (e) {
      var action = $(this).attr('data-workflow-task');
      var $form = $(this).closest('form');
      var $notification = $(this).closest('div[data-notification-alert-id]');
      e.preventDefault();
      $form.append('<input type="hidden" name="action_' + action + '" value="' + action + '">');
      $form.ajaxSubmit({
        dataType: 'json',
        beforeSubmit: function beforeSubmit() {
          my.showLoader();
        },
        success: function success(r) {
          $notification.addClass('animated fadeOut');
          setTimeout(function () {
            $notification.remove();
            my.handleEmpty();
          }, 500);
        },
        complete: function complete() {
          my.hideLoader();
        }
      });
    });
    my.$element.find('.dialog-launch').dialog();
  }
  ConcreteNotificationList.prototype = {
    handleEmpty: function handleEmpty() {
      var my = this;
      var $items = my.$element.find('div[data-notification-alert-id]');
      if ($items.length < 1) {
        my.$element.find('[data-notification-description=empty]').show();
      }
    },
    showLoader: function showLoader() {
      $('div[data-list=notification]').addClass('ccm-block-desktop-waiting-for-me-loading');
    },
    hideLoader: function hideLoader() {
      $('div[data-list=notification]').removeClass();
    }
  };

  // jQuery Plugin
  $.fn.concreteNotificationList = function (options) {
    return $.each($(this), function (i, obj) {
      new ConcreteNotificationList($(this), options);
    });
  };
  global.ConcreteNotificationList = ConcreteNotificationList;
})(__webpack_require__.g, jQuery);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-form/dist/jquery.form.min.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-form/dist/jquery.form.min.js ***!
  \********************************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/*!
 * jQuery Form Plugin
 * version: 4.3.0
 * Requires jQuery v1.7.2 or later
 * Project repository: https://github.com/jquery-form/form

 * Copyright 2017 Kevin Morris
 * Copyright 2006 M. Alsup

 * Dual licensed under the LGPL-2.1+ or MIT licenses
 * https://github.com/jquery-form/form#license

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 */
!function (r) {
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery")], __WEBPACK_AMD_DEFINE_FACTORY__ = (r),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : 0;
}(function (q) {
  "use strict";

  var m = /\r?\n/g,
    S = {};
  S.fileapi = void 0 !== q('<input type="file">').get(0).files, S.formdata = void 0 !== window.FormData;
  var _ = !!q.fn.prop;
  function o(e) {
    var t = e.data;
    e.isDefaultPrevented() || (e.preventDefault(), q(e.target).closest("form").ajaxSubmit(t));
  }
  function i(e) {
    var t = e.target,
      r = q(t);
    if (!r.is("[type=submit],[type=image]")) {
      var a = r.closest("[type=submit]");
      if (0 === a.length) return;
      t = a[0];
    }
    var n,
      o = t.form;
    "image" === (o.clk = t).type && (void 0 !== e.offsetX ? (o.clk_x = e.offsetX, o.clk_y = e.offsetY) : "function" == typeof q.fn.offset ? (n = r.offset(), o.clk_x = e.pageX - n.left, o.clk_y = e.pageY - n.top) : (o.clk_x = e.pageX - t.offsetLeft, o.clk_y = e.pageY - t.offsetTop)), setTimeout(function () {
      o.clk = o.clk_x = o.clk_y = null;
    }, 100);
  }
  function N() {
    var e;
    q.fn.ajaxSubmit.debug && (e = "[jquery.form] " + Array.prototype.join.call(arguments, ""), window.console && window.console.log ? window.console.log(e) : window.opera && window.opera.postError && window.opera.postError(e));
  }
  q.fn.attr2 = function () {
    if (!_) return this.attr.apply(this, arguments);
    var e = this.prop.apply(this, arguments);
    return e && e.jquery || "string" == typeof e ? e : this.attr.apply(this, arguments);
  }, q.fn.ajaxSubmit = function (M, e, t, r) {
    if (!this.length) return N("ajaxSubmit: skipping submit process - no element selected"), this;
    var O,
      a,
      n,
      o,
      X = this;
    "function" == typeof M ? M = {
      success: M
    } : "string" == typeof M || !1 === M && 0 < arguments.length ? (M = {
      url: M,
      data: e,
      dataType: t
    }, "function" == typeof r && (M.success = r)) : void 0 === M && (M = {}), O = M.method || M.type || this.attr2("method"), n = (n = (n = "string" == typeof (a = M.url || this.attr2("action")) ? q.trim(a) : "") || window.location.href || "") && (n.match(/^([^#]+)/) || [])[1], o = /(MSIE|Trident)/.test(navigator.userAgent || "") && /^https/i.test(window.location.href || "") ? "javascript:false" : "about:blank", M = q.extend(!0, {
      url: n,
      success: q.ajaxSettings.success,
      type: O || q.ajaxSettings.type,
      iframeSrc: o
    }, M);
    var i = {};
    if (this.trigger("form-pre-serialize", [this, M, i]), i.veto) return N("ajaxSubmit: submit vetoed via form-pre-serialize trigger"), this;
    if (M.beforeSerialize && !1 === M.beforeSerialize(this, M)) return N("ajaxSubmit: submit aborted via beforeSerialize callback"), this;
    var s = M.traditional;
    void 0 === s && (s = q.ajaxSettings.traditional);
    var u,
      c,
      C = [],
      l = this.formToArray(M.semantic, C, M.filtering);
    if (M.data && (c = q.isFunction(M.data) ? M.data(l) : M.data, M.extraData = c, u = q.param(c, s)), M.beforeSubmit && !1 === M.beforeSubmit(l, this, M)) return N("ajaxSubmit: submit aborted via beforeSubmit callback"), this;
    if (this.trigger("form-submit-validate", [l, this, M, i]), i.veto) return N("ajaxSubmit: submit vetoed via form-submit-validate trigger"), this;
    var f = q.param(l, s);
    u && (f = f ? f + "&" + u : u), "GET" === M.type.toUpperCase() ? (M.url += (0 <= M.url.indexOf("?") ? "&" : "?") + f, M.data = null) : M.data = f;
    var d,
      m,
      p,
      h = [];
    M.resetForm && h.push(function () {
      X.resetForm();
    }), M.clearForm && h.push(function () {
      X.clearForm(M.includeHidden);
    }), !M.dataType && M.target ? (d = M.success || function () {}, h.push(function (e, t, r) {
      var a = arguments,
        n = M.replaceTarget ? "replaceWith" : "html";
      q(M.target)[n](e).each(function () {
        d.apply(this, a);
      });
    })) : M.success && (q.isArray(M.success) ? q.merge(h, M.success) : h.push(M.success)), M.success = function (e, t, r) {
      for (var a = M.context || this, n = 0, o = h.length; n < o; n++) h[n].apply(a, [e, t, r || X, X]);
    }, M.error && (m = M.error, M.error = function (e, t, r) {
      var a = M.context || this;
      m.apply(a, [e, t, r, X]);
    }), M.complete && (p = M.complete, M.complete = function (e, t) {
      var r = M.context || this;
      p.apply(r, [e, t, X]);
    });
    var v = 0 < q("input[type=file]:enabled", this).filter(function () {
        return "" !== q(this).val();
      }).length,
      g = "multipart/form-data",
      x = X.attr("enctype") === g || X.attr("encoding") === g,
      y = S.fileapi && S.formdata;
    N("fileAPI :" + y);
    var b,
      T = (v || x) && !y;
    !1 !== M.iframe && (M.iframe || T) ? M.closeKeepAlive ? q.get(M.closeKeepAlive, function () {
      b = w(l);
    }) : b = w(l) : b = (v || x) && y ? function (e) {
      for (var r = new FormData(), t = 0; t < e.length; t++) r.append(e[t].name, e[t].value);
      if (M.extraData) {
        var a = function (e) {
          var t,
            r,
            a = q.param(e, M.traditional).split("&"),
            n = a.length,
            o = [];
          for (t = 0; t < n; t++) a[t] = a[t].replace(/\+/g, " "), r = a[t].split("="), o.push([decodeURIComponent(r[0]), decodeURIComponent(r[1])]);
          return o;
        }(M.extraData);
        for (t = 0; t < a.length; t++) a[t] && r.append(a[t][0], a[t][1]);
      }
      M.data = null;
      var n = q.extend(!0, {}, q.ajaxSettings, M, {
        contentType: !1,
        processData: !1,
        cache: !1,
        type: O || "POST"
      });
      M.uploadProgress && (n.xhr = function () {
        var e = q.ajaxSettings.xhr();
        return e.upload && e.upload.addEventListener("progress", function (e) {
          var t = 0,
            r = e.loaded || e.position,
            a = e.total;
          e.lengthComputable && (t = Math.ceil(r / a * 100)), M.uploadProgress(e, r, a, t);
        }, !1), e;
      });
      n.data = null;
      var o = n.beforeSend;
      return n.beforeSend = function (e, t) {
        M.formData ? t.data = M.formData : t.data = r, o && o.call(this, e, t);
      }, q.ajax(n);
    }(l) : q.ajax(M), X.removeData("jqxhr").data("jqxhr", b);
    for (var j = 0; j < C.length; j++) C[j] = null;
    return this.trigger("form-submit-notify", [this, M]), this;
    function w(e) {
      var t,
        r,
        l,
        f,
        o,
        d,
        m,
        p,
        a,
        n,
        h,
        v,
        i = X[0],
        g = q.Deferred();
      if (g.abort = function (e) {
        p.abort(e);
      }, e) for (r = 0; r < C.length; r++) t = q(C[r]), _ ? t.prop("disabled", !1) : t.removeAttr("disabled");
      (l = q.extend(!0, {}, q.ajaxSettings, M)).context = l.context || l, o = "jqFormIO" + new Date().getTime();
      var s = i.ownerDocument,
        u = X.closest("body");
      if (l.iframeTarget ? (n = (d = q(l.iframeTarget, s)).attr2("name")) ? o = n : d.attr2("name", o) : (d = q('<iframe name="' + o + '" src="' + l.iframeSrc + '" />', s)).css({
        position: "absolute",
        top: "-1000px",
        left: "-1000px"
      }), m = d[0], p = {
        aborted: 0,
        responseText: null,
        responseXML: null,
        status: 0,
        statusText: "n/a",
        getAllResponseHeaders: function getAllResponseHeaders() {},
        getResponseHeader: function getResponseHeader() {},
        setRequestHeader: function setRequestHeader() {},
        abort: function abort(e) {
          var t = "timeout" === e ? "timeout" : "aborted";
          N("aborting upload... " + t), this.aborted = 1;
          try {
            m.contentWindow.document.execCommand && m.contentWindow.document.execCommand("Stop");
          } catch (e) {}
          d.attr("src", l.iframeSrc), p.error = t, l.error && l.error.call(l.context, p, t, e), f && q.event.trigger("ajaxError", [p, l, t]), l.complete && l.complete.call(l.context, p, t);
        }
      }, (f = l.global) && 0 == q.active++ && q.event.trigger("ajaxStart"), f && q.event.trigger("ajaxSend", [p, l]), l.beforeSend && !1 === l.beforeSend.call(l.context, p, l)) return l.global && q.active--, g.reject(), g;
      if (p.aborted) return g.reject(), g;
      (a = i.clk) && (n = a.name) && !a.disabled && (l.extraData = l.extraData || {}, l.extraData[n] = a.value, "image" === a.type && (l.extraData[n + ".x"] = i.clk_x, l.extraData[n + ".y"] = i.clk_y));
      var x = 1,
        y = 2;
      function b(t) {
        var r = null;
        try {
          t.contentWindow && (r = t.contentWindow.document);
        } catch (e) {
          N("cannot get iframe.contentWindow document: " + e);
        }
        if (r) return r;
        try {
          r = t.contentDocument ? t.contentDocument : t.document;
        } catch (e) {
          N("cannot get iframe.contentDocument: " + e), r = t.document;
        }
        return r;
      }
      var c = q("meta[name=csrf-token]").attr("content"),
        T = q("meta[name=csrf-param]").attr("content");
      function j() {
        var e = X.attr2("target"),
          t = X.attr2("action"),
          r = X.attr("enctype") || X.attr("encoding") || "multipart/form-data";
        i.setAttribute("target", o), O && !/post/i.test(O) || i.setAttribute("method", "POST"), t !== l.url && i.setAttribute("action", l.url), l.skipEncodingOverride || O && !/post/i.test(O) || X.attr({
          encoding: "multipart/form-data",
          enctype: "multipart/form-data"
        }), l.timeout && (v = setTimeout(function () {
          h = !0, A(x);
        }, l.timeout));
        var a = [];
        try {
          if (l.extraData) for (var n in l.extraData) l.extraData.hasOwnProperty(n) && (q.isPlainObject(l.extraData[n]) && l.extraData[n].hasOwnProperty("name") && l.extraData[n].hasOwnProperty("value") ? a.push(q('<input type="hidden" name="' + l.extraData[n].name + '">', s).val(l.extraData[n].value).appendTo(i)[0]) : a.push(q('<input type="hidden" name="' + n + '">', s).val(l.extraData[n]).appendTo(i)[0]));
          l.iframeTarget || d.appendTo(u), m.attachEvent ? m.attachEvent("onload", A) : m.addEventListener("load", A, !1), setTimeout(function e() {
            try {
              var t = b(m).readyState;
              N("state = " + t), t && "uninitialized" === t.toLowerCase() && setTimeout(e, 50);
            } catch (e) {
              N("Server abort: ", e, " (", e.name, ")"), A(y), v && clearTimeout(v), v = void 0;
            }
          }, 15);
          try {
            i.submit();
          } catch (e) {
            document.createElement("form").submit.apply(i);
          }
        } finally {
          i.setAttribute("action", t), i.setAttribute("enctype", r), e ? i.setAttribute("target", e) : X.removeAttr("target"), q(a).remove();
        }
      }
      T && c && (l.extraData = l.extraData || {}, l.extraData[T] = c), l.forceSync ? j() : setTimeout(j, 10);
      var w,
        S,
        k,
        D = 50;
      function A(e) {
        if (!p.aborted && !k) {
          if ((S = b(m)) || (N("cannot access response document"), e = y), e === x && p) return p.abort("timeout"), void g.reject(p, "timeout");
          if (e === y && p) return p.abort("server abort"), void g.reject(p, "error", "server abort");
          if (S && S.location.href !== l.iframeSrc || h) {
            m.detachEvent ? m.detachEvent("onload", A) : m.removeEventListener("load", A, !1);
            var t,
              r = "success";
            try {
              if (h) throw "timeout";
              var a = "xml" === l.dataType || S.XMLDocument || q.isXMLDoc(S);
              if (N("isXml=" + a), !a && window.opera && (null === S.body || !S.body.innerHTML) && --D) return N("requeing onLoad callback, DOM not available"), void setTimeout(A, 250);
              var n = S.body ? S.body : S.documentElement;
              p.responseText = n ? n.innerHTML : null, p.responseXML = S.XMLDocument ? S.XMLDocument : S, a && (l.dataType = "xml"), p.getResponseHeader = function (e) {
                return {
                  "content-type": l.dataType
                }[e.toLowerCase()];
              }, n && (p.status = Number(n.getAttribute("status")) || p.status, p.statusText = n.getAttribute("statusText") || p.statusText);
              var o,
                i,
                s,
                u = (l.dataType || "").toLowerCase(),
                c = /(json|script|text)/.test(u);
              c || l.textarea ? (o = S.getElementsByTagName("textarea")[0]) ? (p.responseText = o.value, p.status = Number(o.getAttribute("status")) || p.status, p.statusText = o.getAttribute("statusText") || p.statusText) : c && (i = S.getElementsByTagName("pre")[0], s = S.getElementsByTagName("body")[0], i ? p.responseText = i.textContent ? i.textContent : i.innerText : s && (p.responseText = s.textContent ? s.textContent : s.innerText)) : "xml" === u && !p.responseXML && p.responseText && (p.responseXML = F(p.responseText));
              try {
                w = E(p, u, l);
              } catch (e) {
                r = "parsererror", p.error = t = e || r;
              }
            } catch (e) {
              N("error caught: ", e), r = "error", p.error = t = e || r;
            }
            p.aborted && (N("upload aborted"), r = null), p.status && (r = 200 <= p.status && p.status < 300 || 304 === p.status ? "success" : "error"), "success" === r ? (l.success && l.success.call(l.context, w, "success", p), g.resolve(p.responseText, "success", p), f && q.event.trigger("ajaxSuccess", [p, l])) : r && (void 0 === t && (t = p.statusText), l.error && l.error.call(l.context, p, r, t), g.reject(p, "error", t), f && q.event.trigger("ajaxError", [p, l, t])), f && q.event.trigger("ajaxComplete", [p, l]), f && ! --q.active && q.event.trigger("ajaxStop"), l.complete && l.complete.call(l.context, p, r), k = !0, l.timeout && clearTimeout(v), setTimeout(function () {
              l.iframeTarget ? d.attr("src", l.iframeSrc) : d.remove(), p.responseXML = null;
            }, 100);
          }
        }
      }
      var F = q.parseXML || function (e, t) {
          return window.ActiveXObject ? ((t = new ActiveXObject("Microsoft.XMLDOM")).async = "false", t.loadXML(e)) : t = new DOMParser().parseFromString(e, "text/xml"), t && t.documentElement && "parsererror" !== t.documentElement.nodeName ? t : null;
        },
        L = q.parseJSON || function (e) {
          return window.eval("(" + e + ")");
        },
        E = function E(e, t, r) {
          var a = e.getResponseHeader("content-type") || "",
            n = ("xml" === t || !t) && 0 <= a.indexOf("xml"),
            o = n ? e.responseXML : e.responseText;
          return n && "parsererror" === o.documentElement.nodeName && q.error && q.error("parsererror"), r && r.dataFilter && (o = r.dataFilter(o, t)), "string" == typeof o && (("json" === t || !t) && 0 <= a.indexOf("json") ? o = L(o) : ("script" === t || !t) && 0 <= a.indexOf("javascript") && q.globalEval(o)), o;
        };
      return g;
    }
  }, q.fn.ajaxForm = function (e, t, r, a) {
    if (("string" == typeof e || !1 === e && 0 < arguments.length) && (e = {
      url: e,
      data: t,
      dataType: r
    }, "function" == typeof a && (e.success = a)), (e = e || {}).delegation = e.delegation && q.isFunction(q.fn.on), e.delegation || 0 !== this.length) return e.delegation ? (q(document).off("submit.form-plugin", this.selector, o).off("click.form-plugin", this.selector, i).on("submit.form-plugin", this.selector, e, o).on("click.form-plugin", this.selector, e, i), this) : (e.beforeFormUnbind && e.beforeFormUnbind(this, e), this.ajaxFormUnbind().on("submit.form-plugin", e, o).on("click.form-plugin", e, i));
    var n = {
      s: this.selector,
      c: this.context
    };
    return !q.isReady && n.s ? (N("DOM not ready, queuing ajaxForm"), q(function () {
      q(n.s, n.c).ajaxForm(e);
    })) : N("terminating; zero elements found by selector" + (q.isReady ? "" : " (DOM not ready)")), this;
  }, q.fn.ajaxFormUnbind = function () {
    return this.off("submit.form-plugin click.form-plugin");
  }, q.fn.formToArray = function (e, t, r) {
    var a = [];
    if (0 === this.length) return a;
    var n,
      o,
      i,
      s,
      u,
      c,
      l,
      f,
      d,
      m,
      p = this[0],
      h = this.attr("id"),
      v = (v = e || void 0 === p.elements ? p.getElementsByTagName("*") : p.elements) && q.makeArray(v);
    if (h && (e || /(Edge|Trident)\//.test(navigator.userAgent)) && (n = q(':input[form="' + h + '"]').get()).length && (v = (v || []).concat(n)), !v || !v.length) return a;
    for (q.isFunction(r) && (v = q.map(v, r)), o = 0, c = v.length; o < c; o++) if ((m = (u = v[o]).name) && !u.disabled) if (e && p.clk && "image" === u.type) p.clk === u && (a.push({
      name: m,
      value: q(u).val(),
      type: u.type
    }), a.push({
      name: m + ".x",
      value: p.clk_x
    }, {
      name: m + ".y",
      value: p.clk_y
    }));else if ((s = q.fieldValue(u, !0)) && s.constructor === Array) for (t && t.push(u), i = 0, l = s.length; i < l; i++) a.push({
      name: m,
      value: s[i]
    });else if (S.fileapi && "file" === u.type) {
      t && t.push(u);
      var g = u.files;
      if (g.length) for (i = 0; i < g.length; i++) a.push({
        name: m,
        value: g[i],
        type: u.type
      });else a.push({
        name: m,
        value: "",
        type: u.type
      });
    } else null != s && (t && t.push(u), a.push({
      name: m,
      value: s,
      type: u.type,
      required: u.required
    }));
    return e || !p.clk || (m = (d = (f = q(p.clk))[0]).name) && !d.disabled && "image" === d.type && (a.push({
      name: m,
      value: f.val()
    }), a.push({
      name: m + ".x",
      value: p.clk_x
    }, {
      name: m + ".y",
      value: p.clk_y
    })), a;
  }, q.fn.formSerialize = function (e) {
    return q.param(this.formToArray(e));
  }, q.fn.fieldSerialize = function (n) {
    var o = [];
    return this.each(function () {
      var e = this.name;
      if (e) {
        var t = q.fieldValue(this, n);
        if (t && t.constructor === Array) for (var r = 0, a = t.length; r < a; r++) o.push({
          name: e,
          value: t[r]
        });else null != t && o.push({
          name: this.name,
          value: t
        });
      }
    }), q.param(o);
  }, q.fn.fieldValue = function (e) {
    for (var t = [], r = 0, a = this.length; r < a; r++) {
      var n = this[r],
        o = q.fieldValue(n, e);
      null == o || o.constructor === Array && !o.length || (o.constructor === Array ? q.merge(t, o) : t.push(o));
    }
    return t;
  }, q.fieldValue = function (e, t) {
    var r = e.name,
      a = e.type,
      n = e.tagName.toLowerCase();
    if (void 0 === t && (t = !0), t && (!r || e.disabled || "reset" === a || "button" === a || ("checkbox" === a || "radio" === a) && !e.checked || ("submit" === a || "image" === a) && e.form && e.form.clk !== e || "select" === n && -1 === e.selectedIndex)) return null;
    if ("select" !== n) return q(e).val().replace(m, "\r\n");
    var o = e.selectedIndex;
    if (o < 0) return null;
    for (var i = [], s = e.options, u = "select-one" === a, c = u ? o + 1 : s.length, l = u ? o : 0; l < c; l++) {
      var f = s[l];
      if (f.selected && !f.disabled) {
        var d = (d = f.value) || (f.attributes && f.attributes.value && !f.attributes.value.specified ? f.text : f.value);
        if (u) return d;
        i.push(d);
      }
    }
    return i;
  }, q.fn.clearForm = function (e) {
    return this.each(function () {
      q("input,select,textarea", this).clearFields(e);
    });
  }, q.fn.clearFields = q.fn.clearInputs = function (r) {
    var a = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;
    return this.each(function () {
      var e = this.type,
        t = this.tagName.toLowerCase();
      a.test(e) || "textarea" === t ? this.value = "" : "checkbox" === e || "radio" === e ? this.checked = !1 : "select" === t ? this.selectedIndex = -1 : "file" === e ? /MSIE/.test(navigator.userAgent) ? q(this).replaceWith(q(this).clone(!0)) : q(this).val("") : r && (!0 === r && /hidden/.test(e) || "string" == typeof r && q(this).is(r)) && (this.value = "");
    });
  }, q.fn.resetForm = function () {
    return this.each(function () {
      var t = q(this),
        e = this.tagName.toLowerCase();
      switch (e) {
        case "input":
          this.checked = this.defaultChecked;
        case "textarea":
          return this.value = this.defaultValue, !0;
        case "option":
        case "optgroup":
          var r = t.parents("select");
          return r.length && r[0].multiple ? "option" === e ? this.selected = this.defaultSelected : t.find("option").resetForm() : r.resetForm(), !0;
        case "select":
          return t.find("option").each(function (e) {
            if (this.selected = this.defaultSelected, this.defaultSelected && !t[0].multiple) return t[0].selectedIndex = e, !1;
          }), !0;
        case "label":
          var a = q(t.attr("for")),
            n = t.find("input,select,textarea");
          return a[0] && n.unshift(a[0]), n.resetForm(), !0;
        case "form":
          return "function" != typeof this.reset && ("object" != _typeof(this.reset) || this.reset.nodeType) || this.reset(), !0;
        default:
          return t.find("form,input,label,select,textarea").resetForm(), !0;
      }
    });
  }, q.fn.enable = function (e) {
    return void 0 === e && (e = !0), this.each(function () {
      this.disabled = !e;
    });
  }, q.fn.selected = function (r) {
    return void 0 === r && (r = !0), this.each(function () {
      var e,
        t = this.type;
      "checkbox" === t || "radio" === t ? this.checked = r : "option" === this.tagName.toLowerCase() && (e = q(this).parent("select"), r && e[0] && "select-one" === e[0].type && e.find("option").selected(!1), this.selected = r);
    });
  }, q.fn.ajaxSubmit.debug = !1;
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/data.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/data.js ***!
  \*****************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI :data 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  return $.extend($.expr.pseudos, {
    data: $.expr.createPseudo ? $.expr.createPseudo(function (dataName) {
      return function (elem) {
        return !!$.data(elem, dataName);
      };
    }) :
    // Support: jQuery <1.8
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Disable Selection 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js ***!
  \**********************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Focusable 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
  };

  // Support: IE 8 only
  // IE 8 doesn't resolve inherit to visible/hidden for computed values
  function visible(element) {
    var visibility = element.css("visibility");
    while (visibility === "inherit") {
      element = element.parent();
      visibility = element.css("visibility");
    }
    return visibility === "visible";
  }
  $.extend($.expr.pseudos, {
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Form Reset Mixin 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./form */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form.js"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  return $.ui.formResetMixin = {
    _formResetHandler: function _formResetHandler() {
      var form = $(this);

      // Wait for the form reset to actually happen before refreshing
      setTimeout(function () {
        var instances = form.data("ui-form-reset-instances");
        $.each(instances, function () {
          this.refresh();
        });
      });
    },
    _bindFormResetHandler: function _bindFormResetHandler() {
      this.form = this.element._form();
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  // Support: IE8 Only
  // IE8 does not support the form attribute and when it is supplied. It overwrites the form prop
  // with a string, so we need to find the proper form.
  return $.fn._form = function () {
    return typeof this[0].form === "string" ? this.closest("form") : $(this[0].form);
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/ie.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/ie.js ***!
  \***************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  // This file is deprecated
  return $.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js ***!
  \********************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Keycode 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Labels 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  return $.fn.labels = function () {
    var ancestor, selector, id, labels, ancestors;
    if (!this.length) {
      return this.pushStack([]);
    }

    // Check control.labels first
    if (this[0].labels && this[0].labels.length) {
      return this.pushStack(this[0].labels);
    }

    // Support: IE <= 11, FF <= 37, Android <= 2.3 only
    // Above browsers do not support control.labels. Everything below is to support them
    // as well as document fragments. control.labels does not work on document fragments
    labels = this.eq(0).parents("label");

    // Look for the label based on the id
    id = this.attr("id");
    if (id) {
      // We don't search against the document in case the element
      // is disconnected from the DOM
      ancestor = this.eq(0).parents().last();

      // Get a full set of top level ancestors
      ancestors = ancestor.add(ancestor.length ? ancestor.siblings() : this.siblings());

      // Create a selector for the label based on the id
      selector = "label[for='" + $.escapeSelector(id) + "']";
      labels = labels.add(ancestors.find(selector).addBack(selector));
    }

    // Return whatever we have found for labels
    return this.pushStack(labels);
  };
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js ***!
  \*******************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Position 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
    function isWindow(obj) {
      return obj != null && obj === obj.window;
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
      if (isWindow(raw)) {
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
          div = $("<div style=" + "'display:block;position:absolute;width:200px;height:200px;overflow:hidden;'>" + "<div style='height:300px;width:auto;'></div></div>"),
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
          isElemWindow = isWindow(withinElement[0]),
          isDocument = !!withinElement[0] && withinElement[0].nodeType === 9,
          hasOffset = !isElemWindow && !isDocument;
        return {
          element: withinElement,
          isWindow: isElemWindow,
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
      }

      // Make a copy, we don't want to modify arguments
      options = $.extend({}, options);
      var atOffset,
        targetWidth,
        targetHeight,
        targetOffset,
        basePosition,
        dimensions,
        // Make sure string options are treated as CSS selectors
        target = typeof options.of === "string" ? $(document).find(options.of) : $(options.of),
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
      targetOffset = dimensions.offset;

      // Clone to reuse original targetOffset later
      basePosition = $.extend({}, targetOffset);

      // Force my and at to have valid horizontal and vertical positions
      // if a value is missing or invalid, it will be converted to center
      $.each(["my", "at"], function () {
        var pos = (options[this] || "").split(" "),
          horizontalOffset,
          verticalOffset;
        if (pos.length === 1) {
          pos = rhorizontal.test(pos[0]) ? pos.concat(["center"]) : rvertical.test(pos[0]) ? ["center"].concat(pos) : ["center", "center"];
        }
        pos[0] = rhorizontal.test(pos[0]) ? pos[0] : "center";
        pos[1] = rvertical.test(pos[1]) ? pos[1] : "center";

        // Calculate offsets
        horizontalOffset = roffset.exec(pos[0]);
        verticalOffset = roffset.exec(pos[1]);
        offsets[this] = [horizontalOffset ? horizontalOffset[0] : 0, verticalOffset ? verticalOffset[0] : 0];

        // Reduce to just the positions without the offsets
        options[this] = [rposition.exec(pos[0])[0], rposition.exec(pos[1])[0]];
      });

      // Normalize collision option
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
            newOverRight;

          // Element is wider than within
          if (data.collisionWidth > outerWidth) {
            // Element is initially over the left side of within
            if (overLeft > 0 && overRight <= 0) {
              newOverRight = position.left + overLeft + data.collisionWidth - outerWidth - withinOffset;
              position.left += overLeft - newOverRight;

              // Element is initially over right side of within
            } else if (overRight > 0 && overLeft <= 0) {
              position.left = withinOffset;

              // Element is initially over both left and right sides of within
            } else {
              if (overLeft > overRight) {
                position.left = withinOffset + outerWidth - data.collisionWidth;
              } else {
                position.left = withinOffset;
              }
            }

            // Too far left -> align with left edge
          } else if (overLeft > 0) {
            position.left += overLeft;

            // Too far right -> align with right edge
          } else if (overRight > 0) {
            position.left -= overRight;

            // Adjust based on position and margin
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
            newOverBottom;

          // Element is taller than within
          if (data.collisionHeight > outerHeight) {
            // Element is initially over the top of within
            if (overTop > 0 && overBottom <= 0) {
              newOverBottom = position.top + overTop + data.collisionHeight - outerHeight - withinOffset;
              position.top += overTop - newOverBottom;

              // Element is initially over bottom of within
            } else if (overBottom > 0 && overTop <= 0) {
              position.top = withinOffset;

              // Element is initially over both top and bottom of within
            } else {
              if (overTop > overBottom) {
                position.top = withinOffset + outerHeight - data.collisionHeight;
              } else {
                position.top = withinOffset;
              }
            }

            // Too far up -> align with top
          } else if (overTop > 0) {
            position.top += overTop;

            // Too far down -> align with bottom edge
          } else if (overBottom > 0) {
            position.top -= overBottom;

            // Adjust based on position and margin
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  return $.ui.safeActiveElement = function (document) {
    var activeElement;

    // Support: IE 9 only
    // IE9 throws an "Unspecified error" accessing document.activeElement from an <iframe>
    try {
      activeElement = document.activeElement;
    } catch (error) {
      activeElement = document.body;
    }

    // Support: IE 9 - 11 only
    // IE may return null instead of an element
    // Interestingly, this only seems to occur when NOT in an iframe
    if (!activeElement) {
      activeElement = document.body;
    }

    // Support: IE 11 only
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Scroll Parent 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Tabbable 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ./focusable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  return $.extend($.expr.pseudos, {
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Unique ID 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (factory) {
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  $.ui = $.ui || {};
  return $.ui.version = "1.13.2";
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js ***!
  \*******************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Widget 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  var widgetUuid = 0;
  var widgetHasOwnProperty = Array.prototype.hasOwnProperty;
  var widgetSlice = Array.prototype.slice;
  $.cleanData = function (orig) {
    return function (elems) {
      var events, elem, i;
      for (i = 0; (elem = elems[i]) != null; i++) {
        // Only trigger remove when necessary to save time
        events = $._data(elem, "events");
        if (events && events.remove) {
          $(elem).triggerHandler("remove");
        }
      }
      orig(elems);
    };
  }($.cleanData);
  $.widget = function (name, base, prototype) {
    var existingConstructor, constructor, basePrototype;

    // ProxiedPrototype allows the provided prototype to remain unmodified
    // so that it can be used as a mixin for multiple widgets (#8876)
    var proxiedPrototype = {};
    var namespace = name.split(".")[0];
    name = name.split(".")[1];
    var fullName = namespace + "-" + name;
    if (!prototype) {
      prototype = base;
      base = $.Widget;
    }
    if (Array.isArray(prototype)) {
      prototype = $.extend.apply(null, [{}].concat(prototype));
    }

    // Create selector for plugin
    $.expr.pseudos[fullName.toLowerCase()] = function (elem) {
      return !!$.data(elem, fullName);
    };
    $[namespace] = $[namespace] || {};
    existingConstructor = $[namespace][name];
    constructor = $[namespace][name] = function (options, element) {
      // Allow instantiation without "new" keyword
      if (!this || !this._createWidget) {
        return new constructor(options, element);
      }

      // Allow instantiation without initializing for simple inheritance
      // must use "new" keyword (the code above always passes args)
      if (arguments.length) {
        this._createWidget(options, element);
      }
    };

    // Extend with the existing constructor to carry over any static properties
    $.extend(constructor, existingConstructor, {
      version: prototype.version,
      // Copy the object used to create the prototype in case we need to
      // redefine the widget later
      _proto: $.extend({}, prototype),
      // Track widgets that inherit from this widget in case this widget is
      // redefined after a widget inherits from it
      _childConstructors: []
    });
    basePrototype = new base();

    // We need to make the options hash a property directly on the new instance
    // otherwise we'll modify the options hash on the prototype that we're
    // inheriting from
    basePrototype.options = $.widget.extend({}, basePrototype.options);
    $.each(prototype, function (prop, value) {
      if (typeof value !== "function") {
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
    });

    // If this widget is being redefined then we need to find all widgets that
    // are inheriting from it and redefine all of them so that they inherit from
    // the new version of this widget. We're essentially trying to replace one
    // level in the prototype chain.
    if (existingConstructor) {
      $.each(existingConstructor._childConstructors, function (i, child) {
        var childPrototype = child.prototype;

        // Redefine the child widget using the same prototype that was
        // originally used, but inherit from the new version of the base
        $.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto);
      });

      // Remove the list of existing child constructors from the old constructor
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
        if (widgetHasOwnProperty.call(input[inputIndex], key) && value !== undefined) {
          // Clone objects
          if ($.isPlainObject(value)) {
            target[key] = $.isPlainObject(target[key]) ? $.widget.extend({}, target[key], value) :
            // Don't extend strings, arrays, etc. with objects
            $.widget.extend({}, value);

            // Copy everything else by reference
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
            if (typeof instance[options] !== "function" || options.charAt(0) === "_") {
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
  $.Widget = function /* options, element */ () {};
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
        this.document = $(element.style ?
        // Element within the document
        element.ownerDocument :
        // Element is window or document
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
      });

      // We can probably remove the unbind calls in 2.0
      // all event bindings should go through this._on()
      this.element.off(this.eventNamespace).removeData(this.widgetFullName);
      this.widget().off(this.eventNamespace).removeAttr("aria-disabled");

      // Clean up events and states
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
        }

        // We are doing this to create a new jQuery object because the _removeClass() call
        // on the next line is going to destroy the reference to the current elements being
        // tracked. We need to save a copy of this collection so that we can add the new classes
        // below.
        elements = $(currentElements.get());
        this._removeClass(currentElements, classKey);

        // We don't use _addClass() here, because that uses this.options.classes
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
      this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!value);

      // If the widget is becoming disabled, then nothing is interactive
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
      function bindRemoveEvent() {
        var nodesToBind = [];
        options.element.each(function (_, element) {
          var isTracked = $.map(that.classesElementLookup, function (elements) {
            return elements;
          }).some(function (elements) {
            return elements.is(element);
          });
          if (!isTracked) {
            nodesToBind.push(element);
          }
        });
        that._on($(nodesToBind), {
          remove: "_untrackClassesElement"
        });
      }
      function processClassString(classes, checkOption) {
        var current, i;
        for (i = 0; i < classes.length; i++) {
          current = that.classesElementLookup[classes[i]] || $();
          if (options.add) {
            bindRemoveEvent();
            current = $($.uniqueSort(current.get().concat(options.element.get())));
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
      this._off($(event.target));
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
      var instance = this;

      // No suppressDisabledCheck flag, shuffle arguments
      if (typeof suppressDisabledCheck !== "boolean") {
        handlers = element;
        element = suppressDisabledCheck;
        suppressDisabledCheck = false;
      }

      // No element argument, shuffle and use this.element
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
        }

        // Copy the guid so direct unbinding works
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
      element.off(eventName);

      // Clear the stack to avoid memory leaks (#10056)
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
      event.type = (type === this.widgetEventPrefix ? type : this.widgetEventPrefix + type).toLowerCase();

      // The original event may come from any element
      // so we need to reset the target on the new event
      event.target = this.element[0];

      // Copy original event properties over to the new event
      orig = event.originalEvent;
      if (orig) {
        for (prop in orig) {
          if (!(prop in event)) {
            event[prop] = orig[prop];
          }
        }
      }
      this.element.trigger(event, data);
      return !(typeof callback === "function" && callback.apply(this.element[0], [event].concat(data)) === false || event.isDefaultPrevented());
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
      } else if (options === true) {
        options = {};
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/*!
 * jQuery UI Button 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"),
    // These are only for backcompat
    // TODO: Remove after 1.12
    __webpack_require__(/*! ./controlgroup */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/controlgroup.js"), __webpack_require__(/*! ./checkboxradio */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/checkboxradio.js"), __webpack_require__(/*! ../keycode */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  $.widget("ui.button", {
    version: "1.13.2",
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
      }

      // We have to check the option again here even though we did in _getCreateOptions,
      // because null may have been passed on init which would override what was set in
      // _getCreateOptions
      if (this.options.disabled == null) {
        this.options.disabled = this.element[0].disabled || false;
      }
      this.hasTitle = !!this.element.attr("title");

      // Check to see if the label needs to be set or if its already correct
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
              event.preventDefault();

              // Support: PhantomJS <= 1.9, IE 8 Only
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
        displayBlock = position === "top" || position === "bottom";

      // Create icon
      if (!this.icon) {
        this.icon = $("<span>");
        this._addClass(this.icon, "ui-button-icon", "ui-icon");
        if (!this.options.showLabel) {
          this._addClass("ui-button-icon-only");
        }
      } else if (icon) {
        // If we are updating the icon remove the old icon class
        this._removeClass(this.icon, null, this.options.icon);
      }

      // If we are updating the icon add the new icon class
      if (icon) {
        this._addClass(this.icon, null, value);
      }
      this._attachIcon(position);

      // If the icon is on top or bottom we need to add the ui-widget-icon-block class and remove
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
      }

      // Make sure we can't end up with a button that has neither text nor icon
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
          this.element.trigger("blur");
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
  });

  // DEPRECATED
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
      return function (options) {
        var isMethodCall = typeof options === "string";
        var args = Array.prototype.slice.call(arguments, 1);
        var returnValue = this;
        if (isMethodCall) {
          // If this is an empty collection, we need to have the instance method
          // return undefined instead of the jQuery instance
          if (!this.length && options === "instance") {
            returnValue = undefined;
          } else {
            this.each(function () {
              var methodValue;
              var type = $(this).attr("type");
              var name = type !== "checkbox" && type !== "radio" ? "button" : "checkboxradio";
              var instance = $.data(this, "ui-" + name);
              if (options === "instance") {
                returnValue = instance;
                return false;
              }
              if (!instance) {
                return $.error("cannot call methods on button" + " prior to initialization; " + "attempted to call method '" + options + "'");
              }
              if (typeof instance[options] !== "function" || options.charAt(0) === "_") {
                return $.error("no such method '" + options + "' for button" + " widget instance");
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
            var type = $(this).attr("type");
            var name = type !== "checkbox" && type !== "radio" ? "button" : "checkboxradio";
            var instance = $.data(this, "ui-" + name);
            if (instance) {
              instance.option(options || {});
              if (instance._init) {
                instance._init();
              }
            } else {
              if (name === "button") {
                orig.call($(this), options);
                return;
              }
              $(this).checkboxradio($.extend({
                icon: false
              }, options));
            }
          });
        }
        return returnValue;
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Checkboxradio 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ../form-reset-mixin */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/form-reset-mixin.js"), __webpack_require__(/*! ../labels */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/labels.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  $.widget("ui.checkboxradio", [$.ui.formResetMixin, {
    version: "1.13.2",
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
      var disabled, labels, labelContents;
      var options = this._super() || {};

      // We read the type here, because it makes more sense to throw a element type error first,
      // rather then the error for lack of a label. Often if its the wrong type, it
      // won't have a label (e.g. calling on a div, btn, etc)
      this._readType();
      labels = this.element.labels();

      // If there are multiple labels, use the last one
      this.label = $(labels[labels.length - 1]);
      if (!this.label.length) {
        $.error("No label found for checkboxradio widget");
      }
      this.originalLabel = "";

      // We need to get the label text but this may also need to make sure it does not contain the
      // input itself.
      // The label contents could be text, html, or a mix. We wrap all elements
      // and read the wrapper's `innerHTML` to get a string representation of
      // the label, without the input as part of it.
      labelContents = this.label.contents().not(this.element[0]);
      if (labelContents.length) {
        this.originalLabel += labelContents.clone().wrapAll("<div></div>").parent().html();
      }

      // Set the label option if we found label text
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
      var nameSelector = "input[name='" + $.escapeSelector(name) + "']";
      if (!name) {
        return $([]);
      }
      if (this.form.length) {
        group = $(this.form[0].elements).filter(nameSelector);
      } else {
        // Not inside a form, check all inputs that also are not inside a form
        group = $(nameSelector).filter(function () {
          return $(this)._form().length === 0;
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
        this.element[0].disabled = value;

        // Don't refresh when setting disabled
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Controlgroup 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  var controlgroupCornerRegex = /ui-corner-([a-z]){2,6}/g;
  return $.widget("ui.controlgroup", {
    version: "1.13.2",
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
        childWidgets = [];

      // First we iterate over each of the items options
      $.each(this.options.items, function (widget, selector) {
        var labels;
        var options = {};

        // Make sure the widget has a selector set
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
        }

        // Make sure the widget actually exists
        if (!$.fn[widget]) {
          return;
        }

        // We assume everything is in the middle to start because we can't determine
        // first / last elements until all enhancments are done.
        if (that["_" + widget + "Options"]) {
          options = that["_" + widget + "Options"]("middle");
        } else {
          options = {
            classes: {}
          };
        }

        // Find instances of this widget inside controlgroup and init them
        that.element.find(selector).each(function () {
          var element = $(this);
          var instance = element[widget]("instance");

          // We need to clone the default options for this type of widget to avoid
          // polluting the variable options which has a wider scope than a single widget.
          var instanceOptions = $.widget.extend({}, options);

          // If the button is the child of a spinner ignore it
          // TODO: Find a more generic solution
          if (widget === "button" && element.parent(".ui-spinner").length) {
            return;
          }

          // Create the widget if it doesn't exist
          if (!instance) {
            instance = element[widget]()[widget]("instance");
          }
          if (instance) {
            instanceOptions.classes = that._resolveClassesValues(instanceOptions.classes, instance);
          }
          element[widget](instanceOptions);

          // Store an instance of the controlgroup to be able to reference
          // from the outermost element for changing options and refresh
          var widgetElement = element[widget]("widget");
          $.data(widgetElement[0], "ui-controlgroup-data", instance ? instance : element[widget]("instance"));
          childWidgets.push(widgetElement[0]);
        });
      });
      this.childWidgets = $($.uniqueSort(childWidgets));
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
        current = String.prototype.trim.call(current.replace(controlgroupCornerRegex, ""));
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
      children = this.childWidgets;

      // We filter here because we need to track all childWidgets not just the visible ones
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
        });

        // Finally call the refresh method on each of the child widgets.
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Dialog 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./button */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/button.js"), __webpack_require__(/*! ./draggable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/draggable.js"), __webpack_require__(/*! ./mouse */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js"), __webpack_require__(/*! ./resizable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/resizable.js"), __webpack_require__(/*! ../focusable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/focusable.js"), __webpack_require__(/*! ../keycode */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/keycode.js"), __webpack_require__(/*! ../position */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/position.js"), __webpack_require__(/*! ../safe-active-element */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-active-element.js"), __webpack_require__(/*! ../safe-blur */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-blur.js"), __webpack_require__(/*! ../tabbable */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/tabbable.js"), __webpack_require__(/*! ../unique-id */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/unique-id.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  $.widget("ui.dialog", {
    version: "1.13.2",
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
      }

      // Dialogs can't be disabled
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
      this.element.removeUniqueId().css(this.originalCss)

      // Without detaching first, the following becomes really slow
      .detach();
      this.uiDialog.remove();
      if (this.originalTitle) {
        this.element.attr("title", this.originalTitle);
      }
      next = originalPosition.parent.children().eq(originalPosition.index);

      // Don't try to place the dialog next to itself (#8613)
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
      this._moveToTop(null, true);

      // Ensure the overlay is moved to the top with the dialog, but only when
      // opening. The overlay shouldn't move after the dialog is open so that
      // modeless dialogs opened after the modal dialog stack properly.
      if (this.overlay) {
        this.overlay.css("z-index", this.uiDialog.css("z-index") - 1);
      }
      this._show(this.uiDialog, this.options.show, function () {
        that._focusTabbable();
        that._trigger("focus");
      });

      // Track the dialog immediately upon opening in case a focus event
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
    _restoreTabbableFocus: function _restoreTabbableFocus() {
      var activeElement = $.ui.safeActiveElement(this.document[0]),
        isActive = this.uiDialog[0] === activeElement || $.contains(this.uiDialog[0], activeElement);
      if (!isActive) {
        this._focusTabbable();
      }
    },
    _keepFocus: function _keepFocus(event) {
      event.preventDefault();
      this._restoreTabbableFocus();

      // support: IE
      // IE <= 8 doesn't prevent moving focus even with event.preventDefault()
      // so we check again later
      this._delay(this._restoreTabbableFocus);
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
          }

          // Prevent tabbing out of dialogs
          if (event.keyCode !== $.ui.keyCode.TAB || event.isDefaultPrevented()) {
            return;
          }
          var tabbables = this.uiDialog.find(":tabbable"),
            first = tabbables.first(),
            last = tabbables.last();
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
      });

      // We assume that any existing aria-describedby attribute means
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
      });

      // Support: IE
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
        buttons = this.options.buttons;

      // If we already have a button pane, remove it
      this.uiDialogButtonPane.remove();
      this.uiButtonSet.empty();
      if ($.isEmptyObject(buttons) || Array.isArray(buttons) && !buttons.length) {
        this._removeClass(this.uiDialog, "ui-dialog-buttons");
        return;
      }
      $.each(buttons, function (name, props) {
        var click, buttonOptions;
        props = typeof props === "function" ? {
          click: props,
          text: name
        } : props;

        // Default to a non-submitting button
        props = $.extend({
          type: "button"
        }, props);

        // Change the context for the click callback to be the main element
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
        delete props.showLabel;

        // Deprecated options
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
        }

        // Currently resizable, changing handles
        if (isResizable && typeof value === "string") {
          uiDialog.resizable("option", "handles", value);
        }

        // Currently non-resizable, becoming resizable
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
        options = this.options;

      // Reset content sizing
      this.element.show().css({
        width: "auto",
        minHeight: 0,
        maxHeight: "none",
        height: 0
      });
      if (options.minWidth > options.width) {
        options.width = options.minWidth;
      }

      // Reset wrapper sizing
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
      }

      // TODO: Remove hack when datepicker implements
      // the .ui-front logic (#8989)
      return !!$(event.target).closest(".ui-datepicker").length;
    },
    _createOverlay: function _createOverlay() {
      if (!this.options.modal) {
        return;
      }
      var jqMinor = $.fn.jquery.substring(0, 4);

      // We use a delay in case the overlay is created from an
      // event that we're going to be cancelling (#2804)
      var isOpening = true;
      this._delay(function () {
        isOpening = false;
      });
      if (!this.document.data("ui-dialog-overlays")) {
        // Prevent use of anchors and inputs
        // This doesn't use `_on()` because it is a shared event handler
        // across all open modal dialogs.
        this.document.on("focusin.ui-dialog", function (event) {
          if (isOpening) {
            return;
          }
          var instance = this._trackingInstances()[0];
          if (!instance._allowInteraction(event)) {
            event.preventDefault();
            instance._focusTabbable();

            // Support: jQuery >=3.4 <3.6 only
            // Focus re-triggering in jQuery 3.4/3.5 makes the original element
            // have its focus event propagated last, breaking the re-targeting.
            // Trigger focus in a delay in addition if needed to avoid the issue
            // See https://github.com/jquery/jquery/issues/4382
            if (jqMinor === "3.4." || jqMinor === "3.5.") {
              instance._delay(instance._restoreTabbableFocus);
            }
          }
        }.bind(this));
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
          this.document.off("focusin.ui-dialog");
          this.document.removeData("ui-dialog-overlays");
        } else {
          this.document.data("ui-dialog-overlays", overlays);
        }
        this.overlay.remove();
        this.overlay = null;
      }
    }
  });

  // DEPRECATED
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Draggable 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./mouse */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js"), __webpack_require__(/*! ../data */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/data.js"), __webpack_require__(/*! ../plugin */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js"), __webpack_require__(/*! ../safe-active-element */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-active-element.js"), __webpack_require__(/*! ../safe-blur */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/safe-blur.js"), __webpack_require__(/*! ../scroll-parent */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/scroll-parent.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  $.widget("ui.draggable", $.ui.mouse, {
    version: "1.13.2",
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
      var o = this.options;

      // Among others, prevent a drag on a resizable-handle
      if (this.helper || o.disabled || $(event.target).closest(".ui-resizable-handle").length > 0) {
        return false;
      }

      //Quit if we're not on a valid handle
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
        target = $(event.target);

      // Don't blur if the event occurred on an element that is within
      // the currently focused element
      // See #10527, #12472
      if (target.closest(activeElement).length) {
        return;
      }

      // Blur any element that currently has focus, see #4261
      $.ui.safeBlur(activeElement);
    },
    _mouseStart: function _mouseStart(event) {
      var o = this.options;

      //Create and append the visible helper
      this.helper = this._createHelper(event);
      this._addClass(this.helper, "ui-draggable-dragging");

      //Cache the helper size
      this._cacheHelperProportions();

      //If ddmanager is used for droppables, set the global draggable
      if ($.ui.ddmanager) {
        $.ui.ddmanager.current = this;
      }

      /*
       * - Position generation -
       * This block generates everything position related - it's the core of draggables.
       */

      //Cache the margins of the original element
      this._cacheMargins();

      //Store the helper's css position
      this.cssPosition = this.helper.css("position");
      this.scrollParent = this.helper.scrollParent(true);
      this.offsetParent = this.helper.offsetParent();
      this.hasFixedAncestor = this.helper.parents().filter(function () {
        return $(this).css("position") === "fixed";
      }).length > 0;

      //The element's absolute position on the page minus margins
      this.positionAbs = this.element.offset();
      this._refreshOffsets(event);

      //Generate the original position
      this.originalPosition = this.position = this._generatePosition(event, false);
      this.originalPageX = event.pageX;
      this.originalPageY = event.pageY;

      //Adjust the mouse offset relative to the helper if "cursorAt" is supplied
      if (o.cursorAt) {
        this._adjustOffsetFromHelper(o.cursorAt);
      }

      //Set a containment if given in the options
      this._setContainment();

      //Trigger event + callbacks
      if (this._trigger("start", event) === false) {
        this._clear();
        return false;
      }

      //Recache the helper size
      this._cacheHelperProportions();

      //Prepare the droppable offsets
      if ($.ui.ddmanager && !o.dropBehaviour) {
        $.ui.ddmanager.prepareOffsets(this, event);
      }

      // Execute the drag once - this causes the helper not to be visible before getting its
      // correct position
      this._mouseDrag(event, true);

      // If the ddmanager is used for droppables, inform the manager that dragging has started
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
      }

      //Compute the helpers position
      this.position = this._generatePosition(event, true);
      this.positionAbs = this._convertPositionTo("absolute");

      //Call plugins and callbacks and use the resulting position if something is returned
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
      }

      //if a drop comes from outside (a sortable)
      if (this.dropped) {
        dropped = this.dropped;
        this.dropped = false;
      }
      if (this.options.revert === "invalid" && !dropped || this.options.revert === "valid" && dropped || this.options.revert === true || typeof this.options.revert === "function" && this.options.revert.call(this.element, dropped)) {
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
      this._unblockFrames();

      // If the ddmanager is used for droppables, inform the manager that dragging has stopped
      // (see #5003)
      if ($.ui.ddmanager) {
        $.ui.ddmanager.dragStop(this, event);
      }

      // Only need to focus if the event occurred on the draggable itself, see #10527
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
        helperIsFunction = typeof o.helper === "function",
        helper = helperIsFunction ? $(o.helper.apply(this.element[0], [event])) : o.helper === "clone" ? this.element.clone().removeAttr("id") : this.element;
      if (!helper.parents("body").length) {
        helper.appendTo(o.appendTo === "parent" ? this.element[0].parentNode : o.appendTo);
      }

      // Http://bugs.jqueryui.com/ticket/9446
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
      if (Array.isArray(obj)) {
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
        document = this.document[0];

      // This is a special case where we need to modify a offset calculated on start, since the
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
        top:
        // The absolute mouse position
        pos.top +
        // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.top * mod +
        // The offsetParent's offset without borders (offset + border)
        this.offset.parent.top * mod - (this.cssPosition === "fixed" ? -this.offset.scroll.top : scrollIsRootNode ? 0 : this.offset.scroll.top) * mod,
        left:
        // The absolute mouse position
        pos.left +
        // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.left * mod +
        // The offsetParent's offset without borders (offset + border)
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
        pageY = event.pageY;

      // Cache the scroll
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
        top:
        // The absolute mouse position
        pageY -
        // Click offset (relative to the element)
        this.offset.click.top -
        // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.top -
        // The offsetParent's offset without borders (offset + border)
        this.offset.parent.top + (this.cssPosition === "fixed" ? -this.offset.scroll.top : scrollIsRootNode ? 0 : this.offset.scroll.top),
        left:
        // The absolute mouse position
        pageX -
        // Click offset (relative to the element)
        this.offset.click.left -
        // Only for relative positioned nodes: Relative offset from element to offset parent
        this.offset.relative.left -
        // The offsetParent's offset without borders (offset + border)
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
      $.ui.plugin.call(this, type, [event, ui, this], true);

      // Absolute position and offset (see #6884 ) have to be recalculated after plugins
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
          draggable.sortables.push(sortable);

          // RefreshPositions is called at drag start to refresh the containerCache
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
          sortable.isOver = 0;

          // Allow this sortable to handle removing the helper
          draggable.cancelHelperRemoval = true;
          sortable.cancelHelperRemoval = false;

          // Use _storedCSS To restore properties in the sortable,
          // as this also handles revert (#9675) since the draggable
          // may have modified them in unexpected ways (#8809)
          sortable._storedCSS = {
            position: sortable.placeholder.css("position"),
            top: sortable.placeholder.css("top"),
            left: sortable.placeholder.css("left")
          };
          sortable._mouseStop(event);

          // Once drag has ended, the sortable should return to using
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
          sortable = this;

        // Copy over variables that sortable's _intersectsWith uses
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
            sortable.isOver = 1;

            // Store draggable's parent in case we need to reappend to it later.
            draggable._parent = ui.helper.parent();
            sortable.currentItem = ui.helper.appendTo(sortable.element).data("ui-sortable-item", true);

            // Store helper option to later restore it
            sortable.options._helper = sortable.options.helper;
            sortable.options.helper = function () {
              return ui.helper[0];
            };

            // Fire the start events of the sortable with our passed browser event,
            // and our own helper (so it doesn't create a new one)
            event.target = sortable.currentItem[0];
            sortable._mouseCapture(event, true);
            sortable._mouseStart(event, true, true);

            // Because the browser event is way off the new appended portlet,
            // modify necessary variables to reflect the changes
            sortable.offset.click.top = draggable.offset.click.top;
            sortable.offset.click.left = draggable.offset.click.left;
            sortable.offset.parent.left -= draggable.offset.parent.left - sortable.offset.parent.left;
            sortable.offset.parent.top -= draggable.offset.parent.top - sortable.offset.parent.top;
            draggable._trigger("toSortable", event);

            // Inform draggable that the helper is in a valid drop zone,
            // used solely in the revert option to handle "valid/invalid".
            draggable.dropped = sortable.element;

            // Need to refreshPositions of all sortables in the case that
            // adding to one sortable changes the location of the other sortables (#9675)
            $.each(draggable.sortables, function () {
              this.refreshPositions();
            });

            // Hack so receive/update callbacks work (mostly)
            draggable.currentItem = draggable.element;
            sortable.fromOutside = draggable;
          }
          if (sortable.currentItem) {
            sortable._mouseDrag(event);

            // Copy the sortable's position because the draggable's can potentially reflect
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
            sortable.cancelHelperRemoval = true;

            // Calling sortable's mouseStop would trigger a revert,
            // so revert must be temporarily false until after mouseStop is called.
            sortable.options._revert = sortable.options.revert;
            sortable.options.revert = false;
            sortable._trigger("out", event, sortable._uiHash(sortable));
            sortable._mouseStop(event, true);

            // Restore sortable behaviors that were modfied
            // when the draggable entered the sortable area (#9481)
            sortable.options.revert = sortable.options._revert;
            sortable.options.helper = sortable.options._helper;
            if (sortable.placeholder) {
              sortable.placeholder.remove();
            }

            // Restore and recalculate the draggable's offset considering the sortable
            // may have modified them in unexpected ways. (#8809, #10669)
            ui.helper.appendTo(draggable._parent);
            draggable._refreshOffsets(event);
            ui.position = draggable._generatePosition(event, true);
            draggable._trigger("fromSortable", event);

            // Inform draggable that the helper is no longer in a valid drop zone
            draggable.dropped = false;

            // Need to refreshPositions of all sortables just in case removing
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
            if (inst.options.snap.release) {
              inst.options.snap.release.call(inst.element, event, $.extend(inst._uiHash(), {
                snapItem: inst.snapElements[i].item
              }));
            }
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
          if (inst.options.snap.snap) {
            inst.options.snap.snap.call(inst.element, event, $.extend(inst._uiHash(), {
              snapItem: inst.snapElements[i].item
            }));
          }
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
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Mouse 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ../ie */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/ie.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  var mouseHandled = false;
  $(document).on("mouseup", function () {
    mouseHandled = false;
  });
  return $.widget("ui.mouse", {
    version: "1.13.2",
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
      this._mouseMoved = false;

      // We may have missed mouseup (out of window)
      if (this._mouseStarted) {
        this._mouseUp(event);
      }
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
      }

      // Click event may never have fired (Gecko & Opera)
      if (true === $.data(event.target, this.widgetName + ".preventClickEvent")) {
        $.removeData(event.target, this.widgetName + ".preventClickEvent");
      }

      // These delegates are required to keep context
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
          return this._mouseUp(event);

          // Iframe mouseup check - mouseup occurred in another document
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
        if (this._mouseStarted) {
          this._mouseDrag(event);
        } else {
          this._mouseUp(event);
        }
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
    _mouseDelayMet: function _mouseDelayMet( /* event */
    ) {
      return this.mouseDelayMet;
    },
    // These are placeholder methods, to be overriden by extending plugin
    _mouseStart: function _mouseStart( /* event */) {},
    _mouseDrag: function _mouseDrag( /* event */) {},
    _mouseStop: function _mouseStop( /* event */) {},
    _mouseCapture: function _mouseCapture( /* event */
    ) {
      return true;
    }
  });
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/resizable.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/resizable.js ***!
  \******************************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * jQuery UI Resizable 1.13.2
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
  "use strict";

  if (true) {
    // AMD. Register as an anonymous module.
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! ./mouse */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widgets/mouse.js"), __webpack_require__(/*! ../disable-selection */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/disable-selection.js"), __webpack_require__(/*! ../plugin */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/plugin.js"), __webpack_require__(/*! ../version */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/version.js"), __webpack_require__(/*! ../widget */ "./node_modules/@concretecms/bedrock/node_modules/jquery-ui/ui/widget.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else {}
})(function ($) {
  "use strict";

  $.widget("ui.resizable", $.ui.mouse, {
    version: "1.13.2",
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
      }

      // TODO: determine which cases actually cause this to happen
      // if the element doesn't have the scroll set, see if it's possible to
      // set the scroll
      try {
        el[scroll] = 1;
        has = el[scroll] > 0;
        el[scroll] = 0;
      } catch (e) {

        // `el` might be a string, then setting `scroll` will throw
        // an error in strict mode; ignore it.
      }
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
      });

      // Wrap the element if it cannot hold child nodes
      if (this.element[0].nodeName.match(/^(canvas|textarea|input|select|button|img)$/i)) {
        this.element.wrap($("<div class='ui-wrapper'></div>").css({
          overflow: "hidden",
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
        this.originalElement.css("margin", 0);

        // support: Safari
        // Prevent Safari textarea resize
        this.originalResizeStyle = this.originalElement.css("resize");
        this.originalElement.css("resize", "none");
        this._proportionallyResizeElements.push(this.originalElement.css({
          position: "static",
          zoom: 1,
          display: "block"
        }));

        // Support: IE9
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
      this._addedHandles.remove();
      var wrapper,
        _destroy = function _destroy(exp) {
          $(exp).removeData("resizable").removeData("ui-resizable").off(".resizable");
        };

      // TODO: Unwrap at same DOM position
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
        case "aspectRatio":
          this._aspectRatio = !!value;
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
      this._addedHandles = $();
      if (this.handles.constructor === String) {
        if (this.handles === "all") {
          this.handles = "n,e,s,w,se,sw,ne,nw";
        }
        n = this.handles.split(",");
        this.handles = {};
        for (i = 0; i < n.length; i++) {
          handle = String.prototype.trim.call(n[i]);
          hname = "ui-resizable-" + handle;
          axis = $("<div>");
          this._addClass(axis, "ui-resizable-handle " + hname);
          axis.css({
            zIndex: o.zIndex
          });
          this.handles[handle] = ".ui-resizable-" + handle;
          if (!this.element.children(this.handles[handle]).length) {
            this.element.append(axis);
            this._addedHandles = this._addedHandles.add(axis);
          }
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
      };

      // TODO: make renderAxis a prototype function
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
      this._addedHandles.remove();
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
      }

      // Fixing jump error on top/left - bug #2330
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
        prel = this._proportionallyResizeElements[i];

        // TODO: Seems like a bug to cache this.outerDimensions
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
        this.helper = this.helper || $("<div></div>").css({
          overflow: "hidden"
        });
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
      if (n !== "resize") {
        this._trigger(n, event, this.ui());
      }
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
          }

          // Propagating resize, and updating values for each animation step
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
      that._addClass(that.ghost, "ui-resizable-ghost");

      // DEPRECATED
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

/***/ "./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/index.es.js":
/*!**********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/index.es.js ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "BackgroundWrapper": () => (/* binding */ Nt),
/* harmony export */   "BoundingBox": () => (/* binding */ Pt),
/* harmony export */   "CircleStencil": () => (/* binding */ Jt),
/* harmony export */   "Cropper": () => (/* binding */ ne),
/* harmony export */   "DragEvent": () => (/* binding */ W),
/* harmony export */   "DraggableArea": () => (/* binding */ Lt),
/* harmony export */   "DraggableElement": () => (/* binding */ O),
/* harmony export */   "HandlerWrapper": () => (/* binding */ H),
/* harmony export */   "LineWrapper": () => (/* binding */ P),
/* harmony export */   "MoveEvent": () => (/* binding */ E),
/* harmony export */   "Preview": () => (/* binding */ Zt),
/* harmony export */   "PreviewResult": () => (/* binding */ Yt),
/* harmony export */   "RectangleStencil": () => (/* binding */ Qt),
/* harmony export */   "ResizeEvent": () => (/* binding */ C),
/* harmony export */   "SimpleHandler": () => (/* binding */ Et),
/* harmony export */   "SimpleLine": () => (/* binding */ Ot),
/* harmony export */   "StencilPreview": () => (/* binding */ qt),
/* harmony export */   "TransformableImage": () => (/* binding */ Ut)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function e(t, e) {
  var i = Object.keys(t);
  if (Object.getOwnPropertySymbols) {
    var n = Object.getOwnPropertySymbols(t);
    e && (n = n.filter(function (e) {
      return Object.getOwnPropertyDescriptor(t, e).enumerable;
    })), i.push.apply(i, n);
  }
  return i;
}
function i(t) {
  for (var i = 1; i < arguments.length; i++) {
    var s = null != arguments[i] ? arguments[i] : {};
    i % 2 ? e(Object(s), !0).forEach(function (e) {
      n(t, e, s[e]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(s)) : e(Object(s)).forEach(function (e) {
      Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(s, e));
    });
  }
  return t;
}
function n(t, e, i) {
  return e in t ? Object.defineProperty(t, e, {
    value: i,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : t[e] = i, t;
}
function s(t, e) {
  if (null == t) return {};
  var i,
    n,
    s = function (t, e) {
      if (null == t) return {};
      var i,
        n,
        s = {},
        o = Object.keys(t);
      for (n = 0; n < o.length; n++) i = o[n], e.indexOf(i) >= 0 || (s[i] = t[i]);
      return s;
    }(t, e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(t);
    for (n = 0; n < o.length; n++) i = o[n], e.indexOf(i) >= 0 || Object.prototype.propertyIsEnumerable.call(t, i) && (s[i] = t[i]);
  }
  return s;
}
function o(t) {
  return function (t) {
    if (Array.isArray(t)) return r(t);
  }(t) || function (t) {
    if ("undefined" != typeof Symbol && null != t[Symbol.iterator] || null != t["@@iterator"]) return Array.from(t);
  }(t) || function (t, e) {
    if (!t) return;
    if ("string" == typeof t) return r(t, e);
    var i = Object.prototype.toString.call(t).slice(8, -1);
    "Object" === i && t.constructor && (i = t.constructor.name);
    if ("Map" === i || "Set" === i) return Array.from(t);
    if ("Arguments" === i || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)) return r(t, e);
  }(t) || function () {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }();
}
function r(t, e) {
  (null == e || e > t.length) && (e = t.length);
  for (var i = 0, n = new Array(e); i < e; i++) n[i] = t[i];
  return n;
}
var a,
  h,
  c,
  l = (a = function a(t) {
    /*!
      Copyright (c) 2018 Jed Watson.
      Licensed under the MIT License (MIT), see
      http://jedwatson.github.io/classnames
    */
    !function () {
      var e = {}.hasOwnProperty;
      function i() {
        for (var t = [], n = 0; n < arguments.length; n++) {
          var s = arguments[n];
          if (s) {
            var o = _typeof(s);
            if ("string" === o || "number" === o) t.push(s);else if (Array.isArray(s)) {
              if (s.length) {
                var r = i.apply(null, s);
                r && t.push(r);
              }
            } else if ("object" === o) if (s.toString === Object.prototype.toString) for (var a in s) e.call(s, a) && s[a] && t.push(a);else t.push(s.toString());
          }
        }
        return t.join(" ");
      }
      t.exports ? (i["default"] = i, t.exports = i) : window.classNames = i;
    }();
  }, a(c = {
    path: h,
    exports: {},
    require: function require(t, e) {
      return function () {
        throw new Error("Dynamic requires are not currently supported by @rollup/plugin-commonjs");
      }(null == e && c.path);
    }
  }, c.exports), c.exports),
  d = function d(t) {
    return function (e, i) {
      if (!e) return t;
      var n;
      "string" == typeof e ? n = e : i = e;
      var s = t;
      return n && (s += "__" + n), s + (i ? Object.keys(i).reduce(function (t, e) {
        var n = i[e];
        return n && (t += " " + ("boolean" == typeof n ? s + "--" + e : s + "--" + e + "_" + n)), t;
      }, "") : "");
    };
  };
function u(t, e, i) {
  var n, s, o, r, a;
  function h() {
    var c = Date.now() - r;
    c < e && c >= 0 ? n = setTimeout(h, e - c) : (n = null, i || (a = t.apply(o, s), o = s = null));
  }
  null == e && (e = 100);
  var c = function c() {
    o = this, s = arguments, r = Date.now();
    var c = i && !n;
    return n || (n = setTimeout(h, e)), c && (a = t.apply(o, s), o = s = null), a;
  };
  return c.clear = function () {
    n && (clearTimeout(n), n = null);
  }, c.flush = function () {
    n && (a = t.apply(o, s), o = s = null, clearTimeout(n), n = null);
  }, c;
}
u.debounce = u;
var m = u,
  _f = function f() {
    return _f = Object.assign || function (t) {
      for (var e, i = 1, n = arguments.length; i < n; i++) for (var s in e = arguments[i]) Object.prototype.hasOwnProperty.call(e, s) && (t[s] = e[s]);
      return t;
    }, _f.apply(this, arguments);
  };
/*! *****************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
function p(t, e) {
  var i, n;
  return t && e ? (i = "" + t + e[0].toUpperCase() + e.slice(1), n = t + "-" + e) : (i = t || e, n = t || e), {
    name: i,
    classname: n
  };
}
function g(t) {
  return /^blob:/.test(t);
}
function v(t) {
  return g(t) || function (t) {
    return /^data:/.test(t);
  }(t);
}
function b(t) {
  return !!(t && t.constructor && t.call && t.apply);
}
function w(t) {
  return void 0 === t;
}
function y(t) {
  return "object" == _typeof(t) && null !== t;
}
function z(t, e, i) {
  var n = {};
  return y(t) ? (Object.keys(e).forEach(function (s) {
    w(t[s]) ? n[s] = e[s] : y(e[s]) ? y(t[s]) ? n[s] = z(t[s], e[s], i[s]) : n[s] = t[s] ? e[s] : i[s] : !0 === e[s] || !1 === e[s] ? n[s] = Boolean(t[s]) : n[s] = t[s];
  }), n) : t ? e : i;
}
function R(t) {
  var e = Number(t);
  return Number.isNaN(e) ? t : e;
}
function A(t) {
  return _typeof("number" == t || function (t) {
    return "object" == _typeof(t) && null !== t;
  }(t) && "[object Number]" == toString.call(t)) && !S(t);
}
function S(t) {
  return t != t;
}
function x(t, e) {
  return Math.sqrt(Math.pow(t.x - e.x, 2) + Math.pow(t.y - e.y, 2));
}
var M = function M(t, e) {
    void 0 === t && (t = {}), void 0 === e && (e = {}), this.type = "manipulateImage", this.move = t, this.scale = e;
  },
  C = function C(t, e) {
    void 0 === e && (e = {}), this.type = "resize", this.directions = t, this.params = e;
  },
  E = function E(t) {
    this.type = "move", this.directions = t;
  },
  W = function () {
    function t(t, e, i, n, s) {
      this.type = "drag", this.nativeEvent = t, this.position = i, this.previousPosition = n, this.element = e, this.anchor = s;
    }
    return t.prototype.shift = function () {
      var t = this,
        e = t.element,
        i = t.anchor,
        n = t.position,
        s = e.getBoundingClientRect(),
        o = s.left,
        r = s.top;
      return {
        left: n.left - o - i.left,
        top: n.top - r - i.top
      };
    }, t;
  }();
function T(t, e, i, n, s, o, r, a, h, c) {
  "boolean" != typeof r && (h = a, a = r, r = !1);
  var l = "function" == typeof i ? i.options : i;
  var d;
  if (t && t.render && (l.render = t.render, l.staticRenderFns = t.staticRenderFns, l._compiled = !0, s && (l.functional = !0)), n && (l._scopeId = n), o ? (d = function d(t) {
    (t = t || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) || "undefined" == typeof __VUE_SSR_CONTEXT__ || (t = __VUE_SSR_CONTEXT__), e && e.call(this, h(t)), t && t._registeredComponents && t._registeredComponents.add(o);
  }, l._ssrRegister = d) : e && (d = r ? function (t) {
    e.call(this, c(t, this.$root.$options.shadowRoot));
  } : function (t) {
    e.call(this, a(t));
  }), d) if (l.functional) {
    var _t2 = l.render;
    l.render = function (e, i) {
      return d.call(i), _t2(e, i);
    };
  } else {
    var _t3 = l.beforeCreate;
    l.beforeCreate = _t3 ? [].concat(_t3, d) : [d];
  }
  return i;
}
var O = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("div", {
        ref: "draggable",
        "class": t.classname,
        on: {
          touchstart: t.onTouchStart,
          mousedown: t.onMouseDown,
          mouseover: t.onMouseOver,
          mouseleave: t.onMouseLeave
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "DraggableElement",
    mixins: [{
      beforeMount: function beforeMount() {
        window.addEventListener("mouseup", this.onMouseUp, {
          passive: !1
        }), window.addEventListener("mousemove", this.onMouseMove, {
          passive: !1
        }), window.addEventListener("touchmove", this.onTouchMove, {
          passive: !1
        }), window.addEventListener("touchend", this.onTouchEnd, {
          passive: !1
        });
      },
      beforeDestroy: function beforeDestroy() {
        window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
      },
      mounted: function mounted() {
        if (!this.$refs.draggable) throw new Error('You should add ref "draggable" to your root element to use draggable mixin');
        this.touches = [], this.hovered = !1;
      },
      methods: {
        onMouseOver: function onMouseOver() {
          this.hovered || (this.hovered = !0, this.$emit("enter"));
        },
        onMouseLeave: function onMouseLeave() {
          this.hovered && !this.touches.length && (this.hovered = !1, this.$emit("leave"));
        },
        onTouchStart: function onTouchStart(t) {
          t.cancelable && !this.disabled && 1 === t.touches.length && (this.touches = o(t.touches), this.hovered || (this.$emit("enter"), this.hovered = !0), t.touches.length && this.initAnchor(this.touches.reduce(function (e, i) {
            return {
              clientX: e.clientX + i.clientX / t.touches.length,
              clientY: e.clientY + i.clientY / t.touches.length
            };
          }, {
            clientX: 0,
            clientY: 0
          })), t.preventDefault && t.preventDefault(), t.stopPropagation());
        },
        onTouchEnd: function onTouchEnd() {
          this.processEnd();
        },
        onTouchMove: function onTouchMove(t) {
          this.touches.length && (this.processMove(t, t.touches), t.preventDefault && t.preventDefault(), t.stopPropagation && t.stopPropagation());
        },
        onMouseDown: function onMouseDown(t) {
          if (!this.disabled && 0 === t.button) {
            var e = {
              fake: !0,
              clientX: t.clientX,
              clientY: t.clientY
            };
            this.touches = [e], this.initAnchor(e), t.stopPropagation();
          }
        },
        onMouseMove: function onMouseMove(t) {
          this.touches.length && (this.processMove(t, [{
            fake: !0,
            clientX: t.clientX,
            clientY: t.clientY
          }]), t.preventDefault && t.preventDefault());
        },
        onMouseUp: function onMouseUp() {
          this.processEnd();
        },
        initAnchor: function initAnchor(t) {
          var e = this.$refs.draggable.getBoundingClientRect(),
            i = e.left,
            n = e.right,
            s = e.bottom,
            o = e.top;
          this.anchor = {
            left: t.clientX - i,
            top: t.clientY - o,
            bottom: s - t.clientY,
            right: n - t.clientX
          };
        },
        processMove: function processMove(t, e) {
          var i = o(e);
          if (this.touches.length) {
            if (1 === this.touches.length && 1 === i.length) {
              var n = this.$refs.draggable;
              this.$emit("drag", new W(t, n, {
                left: i[0].clientX,
                top: i[0].clientY
              }, {
                left: this.touches[0].clientX,
                top: this.touches[0].clientY
              }, this.anchor));
            }
            this.touches = i;
          }
        },
        processEnd: function processEnd() {
          this.touches.length && this.$emit("drag-end"), this.hovered && (this.$emit("leave"), this.hovered = !1), this.touches = [];
        }
      }
    }],
    props: {
      classname: {
        type: String
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  $ = d("vue-handler-wrapper"),
  H = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.root
      }, [i("DraggableElement", {
        "class": t.classes.draggable,
        on: {
          drag: function drag(e) {
            return t.$emit("drag", e);
          },
          "drag-end": function dragEnd(e) {
            return t.$emit("drag-end");
          },
          leave: function leave(e) {
            return t.$emit("leave");
          },
          enter: function enter(e) {
            return t.$emit("enter");
          }
        }
      }, [t._t("default")], 2)], 1);
    },
    staticRenderFns: []
  }, undefined, {
    name: "HandlerWrapper",
    components: {
      DraggableElement: O
    },
    props: {
      horizontalPosition: {
        type: String
      },
      verticalPosition: {
        type: String
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    computed: {
      classes: function classes() {
        var t;
        if (this.horizontalPosition || this.verticalPosition) {
          var e,
            i = p(this.horizontalPosition, this.verticalPosition);
          t = $((n(e = {}, i.classname, !0), n(e, "disabled", this.disabled), e));
        } else t = $({
          disabled: this.disabled
        });
        return {
          root: t,
          draggable: $("draggable")
        };
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  j = d("vue-line-wrapper"),
  P = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("DraggableElement", {
        "class": t.classname,
        on: {
          drag: function drag(e) {
            return t.$emit("drag", e);
          },
          "drag-end": function dragEnd(e) {
            return t.$emit("drag-end");
          },
          leave: function leave(e) {
            return t.$emit("leave");
          },
          enter: function enter(e) {
            return t.$emit("enter");
          }
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "LineWrapper",
    components: {
      DraggableElement: O
    },
    props: {
      position: {
        type: String,
        required: !0
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    computed: {
      classname: function classname() {
        var t;
        return j((n(t = {}, this.position, !0), n(t, "disabled", this.disabled), t));
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  D = ["left", "right", "top", "bottom"],
  L = ["left", "right"],
  I = ["top", "bottom"],
  B = ["left", "top"],
  _ = ["fill-area", "fit-area", "stencil", "none"],
  F = {
    left: 0,
    top: 0,
    width: 0,
    height: 0
  };
function X(t, e, i) {
  return !(i = i || ["width", "height", "left", "top"]).some(function (i) {
    return t[i] !== e[i];
  });
}
function Y(t) {
  return {
    left: t.left,
    top: t.top,
    right: t.left + t.width,
    bottom: t.top + t.height
  };
}
function k(t, e) {
  return {
    left: t.left - e.left,
    top: t.top - e.top
  };
}
function U(t) {
  return {
    left: t.left + t.width / 2,
    top: t.top + t.height / 2
  };
}
function N(t, e) {
  var i = {
    left: 0,
    top: 0,
    right: 0,
    bottom: 0
  };
  return D.forEach(function (n) {
    var s = e[n],
      o = Y(t)[n];
    i[n] = void 0 !== s && void 0 !== o ? "left" === n || "top" === n ? Math.max(0, s - o) : Math.max(0, o - s) : 0;
  }), i;
}
function V(t, e) {
  return {
    left: t.left - e.left,
    top: t.top - e.top,
    width: t.width + e.left + e.right,
    height: t.height + e.top + e.bottom
  };
}
function Z(t) {
  return {
    left: -t.left,
    top: -t.top
  };
}
function q(t, e) {
  return _f(_f({}, t), {
    left: t.left + e.left,
    top: t.top + e.top
  });
}
function G(t, e, i, n) {
  if (1 !== e) {
    if (i) {
      var s = U(t);
      return {
        width: t.width * e,
        height: t.height * e,
        left: t.left + t.width * (1 - e) / 2 + (i.left - s.left) * (n || 1 - e),
        top: t.top + t.height * (1 - e) / 2 + (i.top - s.top) * (n || 1 - e)
      };
    }
    return {
      width: t.width * e,
      height: t.height * e,
      left: t.left + t.width * (1 - e) / 2,
      top: t.top + t.height * (1 - e) / 2
    };
  }
  return t;
}
function Q(t) {
  return t.width / t.height;
}
function K(t, e) {
  return Math.min(void 0 !== e.right && void 0 !== e.left ? (e.right - e.left) / t.width : 1 / 0, void 0 !== e.bottom && void 0 !== e.top ? (e.bottom - e.top) / t.height : 1 / 0);
}
function J(t, e) {
  var i = {
      left: 0,
      top: 0
    },
    n = N(t, e);
  return n.left && n.left > 0 ? i.left = n.left : n.right && n.right > 0 && (i.left = -n.right), n.top && n.top > 0 ? i.top = n.top : n.bottom && n.bottom > 0 && (i.top = -n.bottom), i;
}
function tt(t, e) {
  var i;
  return e.minimum && t < e.minimum ? i = e.minimum : e.maximum && t > e.maximum && (i = e.maximum), i;
}
function et(t, e) {
  var i = Q(t),
    n = Q(e);
  return e.width < 1 / 0 && e.height < 1 / 0 ? i > n ? {
    width: e.width,
    height: e.width / i
  } : {
    width: e.height * i,
    height: e.height
  } : e.width < 1 / 0 ? {
    width: e.width,
    height: e.width / i
  } : e.height < 1 / 0 ? {
    width: e.height * i,
    height: e.height
  } : t;
}
function it(t, e) {
  var i = e * Math.PI / 180;
  return {
    width: Math.abs(t.width * Math.cos(i)) + Math.abs(t.height * Math.sin(i)),
    height: Math.abs(t.width * Math.sin(i)) + Math.abs(t.height * Math.cos(i))
  };
}
function nt(t, e) {
  var i = e * Math.PI / 180;
  return {
    left: t.left * Math.cos(i) - t.top * Math.sin(i),
    top: t.left * Math.sin(i) + t.top * Math.cos(i)
  };
}
function st(t, e) {
  var i = N(ot(t, e), e);
  return i.left + i.right + i.top + i.bottom ? i.left + i.right > i.top + i.bottom ? Math.min((t.width + i.left + i.right) / t.width, K(t, e)) : Math.min((t.height + i.top + i.bottom) / t.height, K(t, e)) : 1;
}
function ot(t, e, i) {
  void 0 === i && (i = !1);
  var n = J(t, e);
  return q(t, i ? Z(n) : n);
}
function rt(t) {
  return {
    width: void 0 !== t.right && void 0 !== t.left ? t.right - t.left : 1 / 0,
    height: void 0 !== t.bottom && void 0 !== t.top ? t.bottom - t.top : 1 / 0
  };
}
function at(t, e) {
  return _f(_f({}, t), {
    minWidth: Math.min(e.width, t.minWidth),
    minHeight: Math.min(e.height, t.minHeight),
    maxWidth: Math.min(e.width, t.maxWidth),
    maxHeight: Math.min(e.height, t.maxHeight)
  });
}
function ht(t, e, i) {
  void 0 === i && (i = !0);
  var n = {};
  return D.forEach(function (s) {
    var o = t[s],
      r = e[s];
    void 0 !== o && void 0 !== r ? n[s] = "left" === s || "top" === s ? i ? Math.max(o, r) : Math.min(o, r) : i ? Math.min(o, r) : Math.max(o, r) : void 0 !== r ? n[s] = r : void 0 !== o && (n[s] = o);
  }), n;
}
function ct(t, e) {
  return ht(t, e, !0);
}
function lt(t) {
  var e = t.size,
    i = t.aspectRatio,
    n = t.ignoreMinimum,
    s = t.sizeRestrictions;
  return Boolean((e.correctRatio || Q(e) >= i.minimum && Q(e) <= i.maximum) && e.height <= s.maxHeight && e.width <= s.maxWidth && e.width && e.height && (n || e.height >= s.minHeight && e.width >= s.minWidth));
}
function dt(t, e) {
  return Math.pow(t.width - e.width, 2) + Math.pow(t.height - e.height, 2);
}
function ut(t) {
  var e = t.width,
    i = t.height,
    n = t.sizeRestrictions,
    s = {
      minimum: t.aspectRatio && t.aspectRatio.minimum || 0,
      maximum: t.aspectRatio && t.aspectRatio.maximum || 1 / 0
    },
    o = {
      width: Math.max(n.minWidth, Math.min(n.maxWidth, e)),
      height: Math.max(n.minHeight, Math.min(n.maxHeight, i))
    };
  function r(t, o) {
    return void 0 === o && (o = !1), t.reduce(function (t, r) {
      return lt({
        size: r,
        aspectRatio: s,
        sizeRestrictions: n,
        ignoreMinimum: o
      }) && (!t || dt(r, {
        width: e,
        height: i
      }) < dt(t, {
        width: e,
        height: i
      })) ? r : t;
    }, null);
  }
  var a = [];
  s && [s.minimum, s.maximum].forEach(function (t) {
    t && a.push({
      width: o.width,
      height: o.width / t,
      correctRatio: !0
    }, {
      width: o.height * t,
      height: o.height,
      correctRatio: !0
    });
  }), lt({
    size: o,
    aspectRatio: s,
    sizeRestrictions: n
  }) && a.push(o);
  var h = r(a) || r(a, !0);
  return h && {
    width: h.width,
    height: h.height
  };
}
function mt(t) {
  var e = t.event,
    i = t.coordinates,
    n = t.positionRestrictions,
    s = void 0 === n ? {} : n,
    o = q(i, e.directions);
  return q(o, J(o, s));
}
function ft(t) {
  var e = t.coordinates,
    i = t.transform,
    n = t.imageSize,
    s = t.sizeRestrictions,
    o = t.positionRestrictions,
    r = t.aspectRatio,
    a = t.visibleArea,
    h = function h(t, e) {
      return mt({
        coordinates: t,
        positionRestrictions: o,
        event: new E({
          left: e.left - t.left,
          top: e.top - t.top
        })
      });
    },
    c = _f({}, e);
  return (Array.isArray(i) ? i : [i]).forEach(function (t) {
    var e = {};
    w((e = "function" == typeof t ? t({
      coordinates: c,
      imageSize: n,
      visibleArea: a
    }) : t).width) && w(e.height) || (c = function (t, e) {
      var i = _f(_f(_f({}, t), ut({
        width: e.width,
        height: e.height,
        sizeRestrictions: s,
        aspectRatio: r
      })), {
        left: 0,
        top: 0
      });
      return h(i, {
        left: t.left,
        top: t.top
      });
    }(c, _f(_f({}, c), e))), w(e.left) && w(e.top) || (c = h(c, _f(_f({}, c), e)));
  }), c;
}
function pt(t) {
  t.event;
  var e = t.getAreaRestrictions,
    i = t.boundaries,
    n = t.coordinates,
    s = t.visibleArea;
  t.aspectRatio;
  var o = t.stencilSize,
    r = t.sizeRestrictions,
    a = t.positionRestrictions;
  t.stencilReference;
  var h,
    c,
    l,
    d = _f({}, n),
    u = _f({}, s),
    m = _f({}, o);
  h = Q(m), c = Q(d), void 0 === l && (l = .001), (0 === h || 0 === c ? Math.abs(c - h) < l : Math.abs(c / h) < 1 + l && Math.abs(c / h) > 1 - l) || (d = _f(_f({}, d), ut({
    sizeRestrictions: r,
    width: d.width,
    height: d.height,
    aspectRatio: {
      minimum: Q(m),
      maximum: Q(m)
    }
  })));
  var p = st(u = G(u, d.width * i.width / (u.width * m.width)), e({
    visibleArea: u,
    type: "resize"
  }));
  return 1 !== p && (u = G(u, p), d = G(d, p)), u = ot(u = q(u, k(U(d), U(u))), e({
    visibleArea: u,
    type: "move"
  })), {
    coordinates: d = ot(d, ct(Y(u), a)),
    visibleArea: u
  };
}
function gt(t) {
  var e = t.event,
    i = t.getAreaRestrictions,
    n = t.boundaries,
    s = t.coordinates,
    o = t.visibleArea;
  t.aspectRatio, t.stencilSize, t.sizeRestrictions;
  var r = t.positionRestrictions;
  t.stencilReference;
  var a = _f({}, s),
    h = _f({}, o);
  if (s && o && "manipulateImage" !== e.type) {
    var c = {
      width: 0,
      height: 0
    };
    h.width, n.width, Q(n) > Q(a) ? (c.height = .8 * n.height, c.width = c.height * Q(a)) : (c.width = .8 * n.width, c.height = c.width * Q(a));
    var l = st(h = G(h, a.width * n.width / (h.width * c.width)), i({
      visibleArea: h,
      type: "resize"
    }));
    h = G(h, l), 1 !== l && (c.height /= l, c.width /= l), h = ot(h = q(h, k(U(a), U(h))), i({
      visibleArea: h,
      type: "move"
    })), a = ot(a, ct(Y(h), r));
  }
  return {
    coordinates: a,
    visibleArea: h
  };
}
function vt(t) {
  var e = t.event,
    i = t.coordinates,
    n = t.visibleArea,
    s = t.getAreaRestrictions,
    o = _f({}, n),
    r = _f({}, i);
  if ("setCoordinates" === e.type) {
    var a = Math.max(0, r.width - o.width),
      h = Math.max(0, r.height - o.height);
    a > h ? o = G(o, Math.min(r.width / o.width, K(o, s({
      visibleArea: o,
      type: "resize"
    })))) : h > a && (o = G(o, Math.min(r.height / o.height, K(o, s({
      visibleArea: o,
      type: "resize"
    }))))), o = ot(o = q(o, Z(J(r, Y(o)))), s({
      visibleArea: o,
      type: "move"
    }));
  }
  return {
    visibleArea: o,
    coordinates: r
  };
}
function bt(t) {
  var e = t.imageSize,
    i = t.visibleArea,
    n = t.aspectRatio,
    s = t.sizeRestrictions,
    o = i || e,
    r = Math.min(n.maximum || 1 / 0, Math.max(n.minimum || 0, Q(o))),
    a = o.width < o.height ? {
      width: .8 * o.width,
      height: .8 * o.width / r
    } : {
      height: .8 * o.height,
      width: .8 * o.height * r
    };
  return ut(_f(_f({}, a), {
    aspectRatio: n,
    sizeRestrictions: s
  }));
}
function wt(t) {
  var e,
    i,
    n = t.imageSize,
    s = t.visibleArea,
    o = t.boundaries,
    r = t.aspectRatio,
    a = t.sizeRestrictions,
    h = t.stencilSize,
    c = s || n;
  return Q(c) > Q(o) ? i = (e = h.height * c.height / o.height) * Q(h) : e = (i = h.width * c.width / o.width) / Q(h), ut({
    width: i,
    height: e,
    aspectRatio: r,
    sizeRestrictions: a
  });
}
function yt(t, e) {
  return ht(t, Y(e));
}
function zt(t) {
  var e = t.event,
    i = t.coordinates,
    n = t.visibleArea,
    s = t.sizeRestrictions,
    o = t.getAreaRestrictions,
    r = t.positionRestrictions,
    a = t.adjustStencil,
    h = e.scale,
    c = e.move,
    l = _f({}, n),
    d = _f({}, i),
    u = 1,
    m = 1,
    p = h.factor && Math.abs(h.factor - 1) > .001;
  l = q(l, {
    left: c.left || 0,
    top: c.top || 0
  });
  var g = {
    stencil: {
      minimum: Math.max(s.minWidth ? s.minWidth / d.width : 0, s.minHeight ? s.minHeight / d.height : 0),
      maximum: Math.min(s.maxWidth ? s.maxWidth / d.width : 1 / 0, s.maxHeight ? s.maxHeight / d.height : 1 / 0, K(d, r))
    },
    area: {
      maximum: K(l, o({
        visibleArea: l,
        type: "resize"
      }))
    }
  };
  h.factor && p && (h.factor < 1 ? (m = Math.max(h.factor, g.stencil.minimum)) > 1 && (m = 1) : h.factor > 1 && (m = Math.min(h.factor, Math.min(g.area.maximum, g.stencil.maximum))) < 1 && (m = 1)), m && (l = G(l, m, h.center));
  var v = i.left - n.left,
    b = n.width + n.left - (i.width + i.left),
    w = i.top - n.top,
    y = n.height + n.top - (i.height + i.top);
  return l = ot(l = q(l, J(l, {
    left: void 0 !== r.left ? r.left - v * m : void 0,
    top: void 0 !== r.top ? r.top - w * m : void 0,
    bottom: void 0 !== r.bottom ? r.bottom + y * m : void 0,
    right: void 0 !== r.right ? r.right + b * m : void 0
  })), o({
    visibleArea: l,
    type: "move"
  })), d.width = d.width * m, d.height = d.height * m, d.left = l.left + v * m, d.top = l.top + w * m, d = ot(d, ct(Y(l), r)), h.factor && p && a && (h.factor > 1 ? u = Math.min(g.area.maximum, h.factor) / m : h.factor < 1 && (u = Math.max(d.height / l.height, d.width / l.width, h.factor / m)), 1 !== u && (l = q(l = ot(l = G(l, u, h.factor > 1 ? h.center : U(d)), o({
    visibleArea: l,
    type: "move"
  })), Z(J(d, Y(l)))))), {
    coordinates: d,
    visibleArea: l
  };
}
function Rt(t) {
  var e = t.aspectRatio,
    i = t.getAreaRestrictions,
    n = t.coordinates,
    s = t.visibleArea,
    o = t.sizeRestrictions,
    r = t.positionRestrictions,
    a = t.imageSize,
    h = t.previousImageSize,
    c = t.angle,
    l = _f({}, n),
    d = _f({}, s),
    u = nt(U(_f({
      left: 0,
      top: 0
    }, h)), c);
  return (l = _f(_f({}, ut({
    sizeRestrictions: o,
    aspectRatio: e,
    width: l.width,
    height: l.height
  })), nt(U(l), c))).left -= u.left - a.width / 2 + l.width / 2, l.top -= u.top - a.height / 2 + l.height / 2, d = G(d, st(d, i({
    visibleArea: d,
    type: "resize"
  }))), {
    coordinates: l = ot(l, r),
    visibleArea: d = ot(d = q(d, k(U(l), U(n))), i({
      visibleArea: d,
      type: "move"
    }))
  };
}
function At(t) {
  var e = t.flip,
    i = t.previousFlip,
    n = t.rotate;
  t.aspectRatio;
  var s = t.getAreaRestrictions,
    o = t.coordinates,
    r = t.visibleArea,
    a = t.imageSize,
    h = _f({}, o),
    c = _f({}, r),
    l = i.horizontal !== e.horizontal,
    d = i.vertical !== e.vertical;
  if (l || d) {
    var u = nt({
        left: a.width / 2,
        top: a.height / 2
      }, -n),
      m = nt(U(h), -n),
      p = nt({
        left: l ? u.left - (m.left - u.left) : m.left,
        top: d ? u.top - (m.top - u.top) : m.top
      }, n);
    h = q(h, k(p, U(h))), m = nt(U(c), -n), c = ot(c = q(c, k(p = nt({
      left: l ? u.left - (m.left - u.left) : m.left,
      top: d ? u.top - (m.top - u.top) : m.top
    }, n), U(c))), s({
      visibleArea: c,
      type: "move"
    }));
  }
  return {
    coordinates: h,
    visibleArea: c
  };
}
function St(t) {
  var e = t.directions,
    i = t.coordinates,
    n = t.positionRestrictions,
    s = void 0 === n ? {} : n,
    o = t.sizeRestrictions,
    r = t.preserveRatio,
    a = t.compensate,
    h = _f({}, e),
    c = V(i, h).width,
    l = V(i, h).height;
  c < 0 && (h.left < 0 && h.right < 0 ? (h.left = -(i.width - o.minWidth) / (h.left / h.right), h.right = -(i.width - o.minWidth) / (h.right / h.left)) : h.left < 0 ? h.left = -(i.width - o.minWidth) : h.right < 0 && (h.right = -(i.width - o.minWidth))), l < 0 && (h.top < 0 && h.bottom < 0 ? (h.top = -(i.height - o.minHeight) / (h.top / h.bottom), h.bottom = -(i.height - o.minHeight) / (h.bottom / h.top)) : h.top < 0 ? h.top = -(i.height - o.minHeight) : h.bottom < 0 && (h.bottom = -(i.height - o.minHeight)));
  var d = N(V(i, h), s);
  a && (d.left && d.left > 0 && 0 === d.right ? (h.right += d.left, h.left -= d.left) : d.right && d.right > 0 && 0 === d.left && (h.left += d.right, h.right -= d.right), d.top && d.top > 0 && 0 === d.bottom ? (h.bottom += d.top, h.top -= d.top) : d.bottom && d.bottom > 0 && 0 === d.top && (h.top += d.bottom, h.bottom -= d.bottom), d = N(V(i, h), s));
  var u = {
    width: 1 / 0,
    height: 1 / 0,
    left: 1 / 0,
    right: 1 / 0,
    top: 1 / 0,
    bottom: 1 / 0
  };
  if (D.forEach(function (t) {
    var e = d[t];
    e && h[t] && (u[t] = Math.max(0, 1 - e / h[t]));
  }), r) {
    var m = Math.min.apply(null, D.map(function (t) {
      return u[t];
    }));
    m !== 1 / 0 && D.forEach(function (t) {
      h[t] *= m;
    });
  } else D.forEach(function (t) {
    u[t] !== 1 / 0 && (h[t] *= u[t]);
  });
  if (c = V(i, h).width, l = V(i, h).height, h.right + h.left && (c > o.maxWidth ? u.width = (o.maxWidth - i.width) / (h.right + h.left) : c < o.minWidth && (u.width = (o.minWidth - i.width) / (h.right + h.left))), h.bottom + h.top && (l > o.maxHeight ? u.height = (o.maxHeight - i.height) / (h.bottom + h.top) : l < o.minHeight && (u.height = (o.minHeight - i.height) / (h.bottom + h.top))), r) {
    var p = Math.min(u.width, u.height);
    p !== 1 / 0 && D.forEach(function (t) {
      h[t] *= p;
    });
  } else u.width !== 1 / 0 && L.forEach(function (t) {
    h[t] *= u.width;
  }), u.height !== 1 / 0 && I.forEach(function (t) {
    h[t] *= u.height;
  });
  return h;
}
function xt(t, e, i) {
  return 0 == e && 0 == i ? t / 2 : 0 == e ? 0 : 0 == i ? t : t * Math.abs(e / (e + i));
}
var Mt = d("vue-simple-handler"),
  Ct = d("vue-simple-handler-wrapper"),
  Et = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("HandlerWrapper", {
        "class": t.classes.wrapper,
        attrs: {
          "vertical-position": t.verticalPosition,
          "horizontal-position": t.horizontalPosition,
          disabled: t.disabled
        },
        on: {
          drag: t.onDrag,
          "drag-end": t.onDragEnd,
          enter: t.onEnter,
          leave: t.onLeave
        }
      }, [i("div", {
        "class": t.classes["default"]
      })]);
    },
    staticRenderFns: []
  }, undefined, {
    name: "SimpleHandler",
    components: {
      HandlerWrapper: H
    },
    props: {
      defaultClass: {
        type: String
      },
      hoverClass: {
        type: String
      },
      wrapperClass: {
        type: String
      },
      horizontalPosition: {
        type: String
      },
      verticalPosition: {
        type: String
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    data: function data() {
      return {
        hover: !1
      };
    },
    computed: {
      classes: function classes() {
        var t,
          e = (n(t = {}, this.horizontalPosition, Boolean(this.horizontalPosition)), n(t, this.verticalPosition, Boolean(this.verticalPosition)), n(t, "".concat(this.horizontalPosition, "-").concat(this.verticalPosition), Boolean(this.verticalPosition && this.horizontalPosition)), n(t, "hover", this.hover), t);
        return {
          "default": l(Mt(e), this.defaultClass, this.hover && this.hoverClass),
          wrapper: l(Ct(e), this.wrapperClass)
        };
      }
    },
    methods: {
      onDrag: function onDrag(t) {
        this.$emit("drag", t);
      },
      onEnter: function onEnter() {
        this.hover = !0;
      },
      onLeave: function onLeave() {
        this.hover = !1;
      },
      onDragEnd: function onDragEnd() {
        this.$emit("drag-end");
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Wt = d("vue-simple-line"),
  Tt = d("vue-simple-line-wrapper"),
  Ot = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("LineWrapper", {
        "class": t.classes.wrapper,
        attrs: {
          position: t.position,
          disabled: t.disabled
        },
        on: {
          drag: t.onDrag,
          "drag-end": t.onDragEnd,
          enter: t.onEnter,
          leave: t.onLeave
        }
      }, [i("div", {
        "class": t.classes.root
      })]);
    },
    staticRenderFns: []
  }, undefined, {
    name: "SimpleLine",
    components: {
      LineWrapper: P
    },
    props: {
      defaultClass: {
        type: String
      },
      hoverClass: {
        type: String
      },
      wrapperClass: {
        type: String
      },
      position: {
        type: String
      },
      disabled: {
        type: Boolean,
        "default": !1
      }
    },
    data: function data() {
      return {
        hover: !1
      };
    },
    computed: {
      classes: function classes() {
        return {
          root: l(Wt(n({}, this.position, !0)), this.defaultClass, this.hover && this.hoverClass),
          wrapper: l(Tt(n({}, this.position, !0)), this.wrapperClass)
        };
      }
    },
    methods: {
      onDrag: function onDrag(t) {
        this.$emit("drag", t);
      },
      onEnter: function onEnter() {
        this.hover = !0;
      },
      onLeave: function onLeave() {
        this.hover = !1;
      },
      onDragEnd: function onDragEnd() {
        this.$emit("drag-end");
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  $t = d("vue-bounding-box"),
  Ht = ["east", "west", null],
  jt = ["south", "north", null],
  Pt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        ref: "box",
        "class": t.classes.root,
        style: t.style
      }, [t._t("default"), t._v(" "), i("div", t._l(t.lineNodes, function (e) {
        return i(e.component, {
          key: e.name,
          tag: "component",
          attrs: {
            "default-class": e["class"],
            "hover-class": e.hoverClass,
            "wrapper-class": e.wrapperClass,
            position: e.name,
            disabled: e.disabled
          },
          on: {
            drag: function drag(i) {
              return t.onHandlerDrag(i, e.horizontalDirection, e.verticalDirection);
            },
            "drag-end": function dragEnd(e) {
              return t.onEnd();
            }
          }
        });
      }), 1), t._v(" "), t._l(t.handlerNodes, function (e) {
        return i("div", {
          key: e.name,
          "class": e.wrapperClass,
          style: e.wrapperStyle
        }, [i(e.component, {
          tag: "component",
          attrs: {
            "default-class": e["class"],
            "hover-class": e.hoverClass,
            "wrapper-class": e.wrapperClass,
            "horizontal-position": e.horizontalDirection,
            "vertical-position": e.verticalDirection,
            disabled: e.disabled
          },
          on: {
            drag: function drag(i) {
              return t.onHandlerDrag(i, e.horizontalDirection, e.verticalDirection);
            },
            "drag-end": function dragEnd(e) {
              return t.onEnd();
            }
          }
        })], 1);
      })], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "BoundingBox",
    props: {
      width: {
        type: Number
      },
      height: {
        type: Number
      },
      transitions: {
        type: Object
      },
      handlers: {
        type: Object,
        "default": function _default() {
          return {
            eastNorth: !0,
            north: !0,
            westNorth: !0,
            west: !0,
            westSouth: !0,
            south: !0,
            eastSouth: !0,
            east: !0
          };
        }
      },
      handlersComponent: {
        type: [Object, String],
        "default": function _default() {
          return Et;
        }
      },
      handlersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      lines: {
        type: Object,
        "default": function _default() {
          return {
            west: !0,
            north: !0,
            east: !0,
            south: !0
          };
        }
      },
      linesComponent: {
        type: [Object, String],
        "default": function _default() {
          return Ot;
        }
      },
      linesClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      linesWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      resizable: {
        type: Boolean,
        "default": !0
      }
    },
    data: function data() {
      var t = [];
      return Ht.forEach(function (e) {
        jt.forEach(function (i) {
          if (e !== i) {
            var n = p(e, i),
              s = n.name,
              o = n.classname;
            t.push({
              name: s,
              classname: o,
              verticalDirection: i,
              horizontalDirection: e
            });
          }
        });
      }), {
        points: t
      };
    },
    computed: {
      style: function style() {
        var t = {};
        return this.width && this.height && (t.width = "".concat(this.width, "px"), t.height = "".concat(this.height, "px"), this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction))), t;
      },
      classes: function classes() {
        var t = this.handlersClasses,
          e = this.handlersWrappersClasses,
          i = this.linesClasses,
          n = this.linesWrappersClasses;
        return {
          root: $t(),
          handlers: t,
          handlersWrappers: e,
          lines: i,
          linesWrappers: n
        };
      },
      lineNodes: function lineNodes() {
        var t = this,
          e = [];
        return this.points.forEach(function (i) {
          i.horizontalDirection && i.verticalDirection || !t.lines[i.name] || e.push({
            name: i.name,
            component: t.linesComponent,
            "class": l(t.classes.lines["default"], t.classes.lines[i.name], !t.resizable && t.classes.lines.disabled),
            wrapperClass: l(t.classes.linesWrappers["default"], t.classes.linesWrappers[i.name], !t.resizable && t.classes.linesWrappers.disabled),
            hoverClass: t.classes.lines.hover,
            verticalDirection: i.verticalDirection,
            horizontalDirection: i.horizontalDirection,
            disabled: !t.resizable
          });
        }), e;
      },
      handlerNodes: function handlerNodes() {
        var t = this,
          e = [],
          i = this.width,
          s = this.height;
        return this.points.forEach(function (o) {
          if (t.handlers[o.name]) {
            var r = {
              name: o.name,
              component: t.handlersComponent,
              "class": l(t.classes.handlers["default"], t.classes.handlers[o.name]),
              wrapperClass: l(t.classes.handlersWrappers["default"], t.classes.handlersWrappers[o.name]),
              hoverClass: t.classes.handlers.hover,
              verticalDirection: o.verticalDirection,
              horizontalDirection: o.horizontalDirection,
              disabled: !t.resizable
            };
            if (i && s) {
              var a = o.horizontalDirection,
                h = o.verticalDirection,
                c = "east" === a ? i : "west" === a ? 0 : i / 2,
                d = "south" === h ? s : "north" === h ? 0 : s / 2;
              r.wrapperClass = $t("handler"), r.wrapperStyle = {
                transform: "translate(".concat(c, "px, ").concat(d, "px)")
              }, t.transitions && t.transitions.enabled && (r.wrapperStyle.transition = "".concat(t.transitions.time, "ms ").concat(t.transitions.timingFunction));
            } else r.wrapperClass = $t("handler", n({}, o.classname, !0));
            e.push(r);
          }
        }), e;
      }
    },
    beforeMount: function beforeMount() {
      window.addEventListener("mouseup", this.onMouseUp, {
        passive: !1
      }), window.addEventListener("mousemove", this.onMouseMove, {
        passive: !1
      }), window.addEventListener("touchmove", this.onTouchMove, {
        passive: !1
      }), window.addEventListener("touchend", this.onTouchEnd, {
        passive: !1
      });
    },
    beforeDestroy: function beforeDestroy() {
      window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
    },
    mounted: function mounted() {
      this.touches = [];
    },
    methods: {
      onEnd: function onEnd() {
        this.$emit("resize-end");
      },
      onHandlerDrag: function onHandlerDrag(t, e, i) {
        var n,
          s = t.shift(),
          o = s.left,
          r = s.top,
          a = {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0
          };
        "west" === e ? a.left -= o : "east" === e && (a.right += o), "north" === i ? a.top -= r : "south" === i && (a.bottom += r), !i && e ? n = "width" : i && !e && (n = "height"), this.resizable && this.$emit("resize", new C(a, {
          allowedDirections: {
            left: "west" === e || !e,
            right: "east" === e || !e,
            bottom: "south" === i || !i,
            top: "north" === i || !i
          },
          preserveAspectRatio: t.nativeEvent && t.nativeEvent.shiftKey,
          respectDirection: n
        }));
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Dt = d("vue-draggable-area"),
  Lt = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("div", {
        ref: "container",
        on: {
          touchstart: t.onTouchStart,
          mousedown: t.onMouseDown
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    name: "DraggableArea",
    props: {
      movable: {
        type: Boolean,
        "default": !0
      },
      activationDistance: {
        type: Number,
        "default": 20
      }
    },
    computed: {
      classnames: function classnames() {
        return {
          "default": Dt()
        };
      }
    },
    beforeMount: function beforeMount() {
      window.addEventListener("mouseup", this.onMouseUp, {
        passive: !1
      }), window.addEventListener("mousemove", this.onMouseMove, {
        passive: !1
      }), window.addEventListener("touchmove", this.onTouchMove, {
        passive: !1
      }), window.addEventListener("touchend", this.onTouchEnd, {
        passive: !1
      });
    },
    beforeDestroy: function beforeDestroy() {
      window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
    },
    mounted: function mounted() {
      this.touches = [], this.touchStarted = !1;
    },
    methods: {
      onTouchStart: function onTouchStart(t) {
        if (t.cancelable) {
          var e = this.movable && 1 === t.touches.length;
          e && (this.touches = o(t.touches)), (this.touchStarted || e) && (t.preventDefault(), t.stopPropagation());
        }
      },
      onTouchEnd: function onTouchEnd() {
        this.touchStarted = !1, this.processEnd();
      },
      onTouchMove: function onTouchMove(t) {
        this.touches.length >= 1 && (this.touchStarted ? (this.processMove(t, t.touches), t.preventDefault(), t.stopPropagation()) : x({
          x: this.touches[0].clientX,
          y: this.touches[0].clientY
        }, {
          x: t.touches[0].clientX,
          y: t.touches[0].clientY
        }) > this.activationDistance && (this.initAnchor({
          clientX: t.touches[0].clientX,
          clientY: t.touches[0].clientY
        }), this.touchStarted = !0));
      },
      onMouseDown: function onMouseDown(t) {
        if (this.movable && 0 === t.button) {
          var e = {
            fake: !0,
            clientX: t.clientX,
            clientY: t.clientY
          };
          this.touches = [e], this.initAnchor(e), t.stopPropagation();
        }
      },
      onMouseMove: function onMouseMove(t) {
        this.touches.length && (this.processMove(t, [{
          fake: !0,
          clientX: t.clientX,
          clientY: t.clientY
        }]), t.preventDefault && t.cancelable && t.preventDefault(), t.stopPropagation());
      },
      onMouseUp: function onMouseUp() {
        this.processEnd();
      },
      initAnchor: function initAnchor(t) {
        var e = this.$refs.container.getBoundingClientRect(),
          i = e.left,
          n = e.top;
        this.anchor = {
          x: t.clientX - i,
          y: t.clientY - n
        };
      },
      processMove: function processMove(t, e) {
        var i = o(e);
        if (this.touches.length) {
          var n = this.$refs.container.getBoundingClientRect(),
            s = n.left,
            r = n.top;
          1 === this.touches.length && 1 === i.length && this.$emit("move", new E({
            left: i[0].clientX - (s + this.anchor.x),
            top: i[0].clientY - (r + this.anchor.y)
          }));
        }
      },
      processEnd: function processEnd() {
        this.touches.length && this.$emit("move-end"), this.touches = [];
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0);
function It(t) {
  var e, i;
  return {
    rotate: t.rotate || 0,
    flip: {
      horizontal: (null === (e = null == t ? void 0 : t.flip) || void 0 === e ? void 0 : e.horizontal) || !1,
      vertical: (null === (i = null == t ? void 0 : t.flip) || void 0 === i ? void 0 : i.vertical) || !1
    }
  };
}
function Bt(t) {
  return new Promise(function (e, i) {
    try {
      if (t) {
        if (/^data:/i.test(t)) e(function (t) {
          t = t.replace(/^data:([^;]+);base64,/gim, "");
          for (var e = atob(t), i = e.length, n = new ArrayBuffer(i), s = new Uint8Array(n), o = 0; o < i; o++) s[o] = e.charCodeAt(o);
          return n;
        }(t));else if (/^blob:/i.test(t)) {
          var n = new FileReader();
          n.onload = function (t) {
            e(t.target.result);
          }, o = t, r = function r(t) {
            n.readAsArrayBuffer(t);
          }, (a = new XMLHttpRequest()).open("GET", o, !0), a.responseType = "blob", a.onload = function () {
            200 != this.status && 0 !== this.status || r(this.response);
          }, a.send();
        } else {
          var s = new XMLHttpRequest();
          s.onreadystatechange = function () {
            4 === s.readyState && (200 === s.status || 0 === s.status ? e(s.response) : i("Warning: could not load an image to parse its orientation"), s = null);
          }, s.onprogress = function () {
            "image/jpeg" !== s.getResponseHeader("content-type") && s.abort();
          }, s.withCredentials = !1, s.open("GET", t, !0), s.responseType = "arraybuffer", s.send(null);
        }
      } else i("Error: the image is empty");
    } catch (t) {
      i(t);
    }
    var o, r, a;
  });
}
function _t(t) {
  var e = t.rotate,
    i = t.flip,
    n = t.scaleX,
    s = t.scaleY,
    o = "";
  return o += " rotate(" + e + "deg) ", o += " scaleX(" + n * (i.horizontal ? -1 : 1) + ") ", o += " scaleY(" + s * (i.vertical ? -1 : 1) + ") ";
}
function Ft(t) {
  try {
    var e,
      i = new DataView(t),
      n = void 0,
      s = void 0,
      o = void 0,
      r = void 0;
    if (255 === i.getUint8(0) && 216 === i.getUint8(1)) for (var a = i.byteLength, h = 2; h + 1 < a;) {
      if (255 === i.getUint8(h) && 225 === i.getUint8(h + 1)) {
        o = h;
        break;
      }
      h++;
    }
    if (o && (n = o + 10, "Exif" === function (t, e, i) {
      var n,
        s = "";
      for (n = e, i += e; n < i; n++) s += String.fromCharCode(t.getUint8(n));
      return s;
    }(i, o + 4, 4))) {
      var c = i.getUint16(n);
      if (((s = 18761 === c) || 19789 === c) && 42 === i.getUint16(n + 2, s)) {
        var l = i.getUint32(n + 4, s);
        l >= 8 && (r = n + l);
      }
    }
    if (r) for (var d = i.getUint16(r, s), u = 0; u < d; u++) {
      h = r + 12 * u + 2;
      if (274 === i.getUint16(h, s)) {
        h += 8, e = i.getUint16(h, s), i.setUint16(h, 1, s);
        break;
      }
    }
    return e;
  } catch (t) {
    return null;
  }
}
var Xt = d("vue-preview-result"),
  Yt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.root
      }, [i("div", {
        ref: "wrapper",
        "class": t.classes.wrapper,
        style: t.wrapperStyle
      }, [i("img", {
        ref: "image",
        "class": t.classes.image,
        style: t.imageStyle,
        attrs: {
          src: t.image.src
        }
      })])]);
    },
    staticRenderFns: []
  }, undefined, {
    name: "PreviewResult",
    props: {
      image: {
        type: Object
      },
      transitions: {
        type: Object
      },
      stencilCoordinates: {
        type: Object,
        "default": function _default() {
          return {
            width: 0,
            height: 0,
            left: 0,
            top: 0
          };
        }
      },
      imageClass: {
        type: String
      }
    },
    computed: {
      classes: function classes() {
        return {
          root: Xt(),
          wrapper: Xt("wrapper"),
          imageWrapper: Xt("image-wrapper"),
          image: l(Xt("image"), this.imageClass)
        };
      },
      wrapperStyle: function wrapperStyle() {
        var t = {
          width: "".concat(this.stencilCoordinates.width, "px"),
          height: "".concat(this.stencilCoordinates.height, "px"),
          left: "calc(50% - ".concat(this.stencilCoordinates.width / 2, "px)"),
          top: "calc(50% - ".concat(this.stencilCoordinates.height / 2, "px)")
        };
        return this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), t;
      },
      imageStyle: function imageStyle() {
        var t = this.image.transforms,
          e = it({
            width: this.image.width,
            height: this.image.height
          }, t.rotate),
          i = {
            width: "".concat(this.image.width, "px"),
            height: "".concat(this.image.height, "px"),
            left: "0px",
            top: "0px"
          },
          n = {
            left: (this.image.width - e.width) * t.scaleX / 2,
            top: (this.image.height - e.height) * t.scaleY / 2
          },
          s = {
            left: (1 - t.scaleX) * this.image.width / 2,
            top: (1 - t.scaleY) * this.image.height / 2
          };
        return i.transform = "translate(\n\t\t\t\t".concat(-this.stencilCoordinates.left - t.translateX - n.left - s.left, "px,").concat(-this.stencilCoordinates.top - t.translateY - n.top - s.top, "px) ") + _t(t), this.transitions && this.transitions.enabled && (i.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), i;
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0);
function kt(t, e) {
  var i = e.getBoundingClientRect(),
    n = i.left,
    s = i.top,
    o = {
      left: 0,
      top: 0
    },
    r = 0;
  return t.forEach(function (e) {
    o.left += (e.clientX - n) / t.length, o.top += (e.clientY - s) / t.length;
  }), t.forEach(function (t) {
    r += x({
      x: o.left,
      y: o.top
    }, {
      x: t.clientX - n,
      y: t.clientY - s
    });
  }), {
    centerMass: o,
    spread: r,
    count: t.length
  };
}
var Ut = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("div", {
        ref: "container",
        on: {
          touchstart: t.onTouchStart,
          mousedown: t.onMouseDown,
          wheel: t.onWheel
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    props: {
      touchMove: {
        type: Boolean,
        required: !0
      },
      mouseMove: {
        type: Boolean,
        required: !0
      },
      touchResize: {
        type: Boolean,
        required: !0
      },
      wheelResize: {
        type: [Boolean, Object],
        required: !0
      },
      eventsFilter: {
        type: Function,
        required: !1
      }
    },
    beforeMount: function beforeMount() {
      window.addEventListener("mouseup", this.onMouseUp, {
        passive: !1
      }), window.addEventListener("mousemove", this.onMouseMove, {
        passive: !1
      }), window.addEventListener("touchmove", this.onTouchMove, {
        passive: !1
      }), window.addEventListener("touchend", this.onTouchEnd, {
        passive: !1
      });
    },
    beforeDestroy: function beforeDestroy() {
      window.removeEventListener("mouseup", this.onMouseUp), window.removeEventListener("mousemove", this.onMouseMove), window.removeEventListener("touchmove", this.onTouchMove), window.removeEventListener("touchend", this.onTouchEnd);
    },
    created: function created() {
      this.transforming = !1, this.debouncedProcessEnd = m(this.processEnd), this.touches = [];
    },
    methods: {
      processMove: function processMove(t, e) {
        if (this.touches.length) {
          if (1 === this.touches.length && 1 === e.length) this.$emit("move", new M({
            left: this.touches[0].clientX - e[0].clientX,
            top: this.touches[0].clientY - e[0].clientY
          }));else if (this.touches.length > 1 && this.touchResize) {
            var i = kt(e, this.$refs.container),
              n = this.oldGeometricProperties;
            n.count === i.count && n.count > 1 && this.$emit("resize", new M({
              left: n.centerMass.left - i.centerMass.left,
              top: n.centerMass.top - i.centerMass.top
            }, {
              factor: n.spread / i.spread,
              center: i.centerMass
            })), this.oldGeometricProperties = i;
          }
          this.touches = e;
        }
      },
      processEnd: function processEnd() {
        this.transforming && (this.transforming = !1, this.$emit("transform-end"));
      },
      processStart: function processStart() {
        this.transforming = !0, this.debouncedProcessEnd.clear();
      },
      processEvent: function processEvent(t) {
        return this.eventsFilter ? !1 !== this.eventsFilter(t, this.transforming) : (t.preventDefault(), t.stopPropagation(), !0);
      },
      onTouchStart: function onTouchStart(t) {
        if (t.cancelable && (this.touchMove || this.touchResize && t.touches.length > 1) && this.processEvent(t)) {
          var e = this.$refs.container,
            i = e.getBoundingClientRect(),
            n = i.left,
            s = i.top,
            r = i.bottom,
            a = i.right;
          this.touches = o(t.touches).filter(function (t) {
            return t.clientX > n && t.clientX < a && t.clientY > s && t.clientY < r;
          }), this.oldGeometricProperties = kt(this.touches, e);
        }
      },
      onTouchEnd: function onTouchEnd(t) {
        0 === t.touches.length && (this.touches = [], this.processEnd());
      },
      onTouchMove: function onTouchMove(t) {
        var e = this;
        if (this.touches.length) {
          var i = o(t.touches).filter(function (t) {
            return !t.identifier || e.touches.find(function (e) {
              return e.identifier === t.identifier;
            });
          });
          this.processEvent(t) && (this.processMove(t, i), this.processStart());
        }
      },
      onMouseDown: function onMouseDown(t) {
        if (this.mouseMove && "buttons" in t && 1 === t.buttons && this.processEvent(t)) {
          var e = {
            fake: !0,
            clientX: t.clientX,
            clientY: t.clientY
          };
          this.touches = [e], this.processStart();
        }
      },
      onMouseMove: function onMouseMove(t) {
        this.touches.length && this.processEvent(t) && this.processMove(t, [{
          clientX: t.clientX,
          clientY: t.clientY
        }]);
      },
      onMouseUp: function onMouseUp() {
        this.touches = [], this.processEnd();
      },
      onWheel: function onWheel(t) {
        if (this.wheelResize && this.processEvent(t)) {
          var e = this.$refs.container.getBoundingClientRect(),
            i = e.left,
            n = e.top,
            s = 1 + this.wheelResize.ratio * (r = t.deltaY || t.detail || t.wheelDelta, 0 === (a = +r) || S(a) ? a : a > 0 ? 1 : -1),
            o = {
              left: t.clientX - i,
              top: t.clientY - n
            };
          this.$emit("resize", new M({}, {
            factor: s,
            center: o
          })), this.touches.length || this.debouncedProcessEnd();
        }
        var r, a;
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Nt = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("transformable-image", {
        attrs: {
          "touch-move": t.touchMove,
          "touch-resize": t.touchResize,
          "mouse-move": t.mouseMove,
          "wheel-resize": t.wheelResize
        },
        on: {
          move: function move(e) {
            return t.$emit("move", e);
          },
          resize: function resize(e) {
            return t.$emit("resize", e);
          }
        }
      }, [t._t("default")], 2);
    },
    staticRenderFns: []
  }, undefined, {
    components: {
      TransformableImage: Ut
    },
    props: {
      touchMove: {
        type: Boolean,
        required: !0
      },
      mouseMove: {
        type: Boolean,
        required: !0
      },
      touchResize: {
        type: Boolean,
        required: !0
      },
      wheelResize: {
        type: [Boolean, Object],
        required: !0
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Vt = d("vue-preview"),
  Zt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        ref: "root",
        "class": t.classes.root,
        style: t.style
      }, [i("div", {
        ref: "wrapper",
        "class": t.classes.wrapper,
        style: t.wrapperStyle
      }, [i("img", {
        directives: [{
          name: "show",
          rawName: "v-show",
          value: t.image && t.image.src,
          expression: "image && image.src"
        }],
        ref: "image",
        "class": t.classes.image,
        style: t.imageStyle,
        attrs: {
          src: t.image && t.image.src
        }
      })])]);
    },
    staticRenderFns: []
  }, undefined, {
    props: {
      coordinates: {
        type: Object
      },
      transitions: {
        type: Object
      },
      image: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      imageClass: {
        type: String
      },
      width: {
        type: Number
      },
      height: {
        type: Number
      },
      fill: {
        type: Boolean
      }
    },
    data: function data() {
      return {
        calculatedImageSize: {
          width: 0,
          height: 0
        },
        calculatedSize: {
          width: 0,
          height: 0
        }
      };
    },
    computed: {
      classes: function classes() {
        return {
          root: Vt({
            fill: this.fill
          }),
          wrapper: Vt("wrapper"),
          imageWrapper: Vt("image-wrapper"),
          image: l(Vt("image"), this.imageClass)
        };
      },
      style: function style() {
        if (this.fill) return {};
        var t = {};
        return this.width && (t.width = "".concat(this.size.width, "px")), this.height && (t.height = "".concat(this.size.height, "px")), this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), t;
      },
      wrapperStyle: function wrapperStyle() {
        var t = {
          width: "".concat(this.size.width, "px"),
          height: "".concat(this.size.height, "px"),
          left: "calc(50% - ".concat(this.size.width / 2, "px)"),
          top: "calc(50% - ".concat(this.size.height / 2, "px)")
        };
        return this.transitions && this.transitions.enabled && (t.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), t;
      },
      imageStyle: function imageStyle() {
        if (this.coordinates && this.image) {
          var t = this.coordinates.width / this.size.width,
            e = i(i({
              rotate: 0,
              flip: {
                horizontal: !1,
                vertical: !1
              }
            }, this.image.transforms), {}, {
              scaleX: 1 / t,
              scaleY: 1 / t
            }),
            n = this.imageSize.width,
            s = this.imageSize.height,
            o = it({
              width: n,
              height: s
            }, e.rotate),
            r = {
              width: "".concat(n, "px"),
              height: "".concat(s, "px"),
              left: "0px",
              top: "0px"
            },
            a = {
              rotate: {
                left: (n - o.width) * e.scaleX / 2,
                top: (s - o.height) * e.scaleY / 2
              },
              scale: {
                left: (1 - e.scaleX) * n / 2,
                top: (1 - e.scaleY) * s / 2
              }
            };
          return r.transform = "translate(\n\t\t\t\t".concat(-this.coordinates.left / t - a.rotate.left - a.scale.left, "px,").concat(-this.coordinates.top / t - a.rotate.top - a.scale.top, "px) ") + _t(e), this.transitions && this.transitions.enabled && (r.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), r;
        }
        return {};
      },
      size: function size() {
        return {
          width: this.width || this.calculatedSize.width,
          height: this.height || this.calculatedSize.height
        };
      },
      imageSize: function imageSize() {
        return {
          width: this.image.width || this.calculatedImageSize.width,
          height: this.image.height || this.calculatedImageSize.height
        };
      }
    },
    watch: {
      image: function image(t) {
        (t.width || t.height) && this.onChangeImage();
      }
    },
    mounted: function mounted() {
      var t = this;
      this.onChangeImage(), this.$refs.image.addEventListener("load", function () {
        t.refreshImage();
      }), window.addEventListener("resize", this.refresh), window.addEventListener("orientationchange", this.refresh);
    },
    destroyed: function destroyed() {
      window.removeEventListener("resize", this.refresh), window.removeEventListener("orientationchange", this.refresh);
    },
    methods: {
      refreshImage: function refreshImage() {
        var t = this.$refs.image;
        this.calculatedImageSize.height = t.naturalHeight, this.calculatedImageSize.width = t.naturalWidth;
      },
      refresh: function refresh() {
        var t = this.$refs.root;
        this.width || (this.calculatedSize.width = t.clientWidth), this.height || (this.calculatedSize.height = t.clientHeight);
      },
      onChangeImage: function onChangeImage() {
        var t = this.$refs.image;
        t && t.complete && this.refreshImage(), this.refresh();
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  qt = T({
    render: function render() {
      var t = this,
        e = t.$createElement;
      return (t._self._c || e)("preview", t._b({
        attrs: {
          fill: !0
        }
      }, "preview", t.$attrs, !1));
    },
    staticRenderFns: []
  }, undefined, {
    components: {
      Preview: Zt
    },
    inheritAttrs: !1
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Gt = d("vue-rectangle-stencil"),
  Qt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.stencil,
        style: t.style
      }, [i("bounding-box", {
        "class": t.classes.boundingBox,
        attrs: {
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions,
          handlers: t.handlers,
          "handlers-component": t.handlersComponent,
          "handlers-classes": t.handlersClasses,
          "handlers-wrappers-classes": t.handlersWrappersClasses,
          lines: t.lines,
          "lines-component": t.linesComponent,
          "lines-classes": t.linesClasses,
          "lines-wrappers-classes": t.linesWrappersClasses,
          resizable: t.resizable
        },
        on: {
          resize: t.onResize,
          "resize-end": t.onResizeEnd
        }
      }, [i("draggable-area", {
        attrs: {
          movable: t.movable
        },
        on: {
          move: t.onMove,
          "move-end": t.onMoveEnd
        }
      }, [i("stencil-preview", {
        "class": t.classes.preview,
        attrs: {
          image: t.image,
          coordinates: t.coordinates,
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions
        }
      })], 1)], 1)], 1);
    },
    staticRenderFns: []
  }, undefined, {
    name: "RectangleStencil",
    components: {
      StencilPreview: qt,
      BoundingBox: Pt,
      DraggableArea: Lt
    },
    props: {
      image: {
        type: Object
      },
      coordinates: {
        type: Object
      },
      stencilCoordinates: {
        type: Object
      },
      handlers: {
        type: Object
      },
      handlersComponent: {
        type: [Object, String],
        "default": function _default() {
          return Et;
        }
      },
      lines: {
        type: Object
      },
      linesComponent: {
        type: [Object, String],
        "default": function _default() {
          return Ot;
        }
      },
      aspectRatio: {
        type: [Number, String]
      },
      minAspectRatio: {
        type: [Number, String]
      },
      maxAspectRatio: {
        type: [Number, String]
      },
      movable: {
        type: Boolean,
        "default": !0
      },
      resizable: {
        type: Boolean,
        "default": !0
      },
      transitions: {
        type: Object
      },
      movingClass: {
        type: String
      },
      resizingClass: {
        type: String
      },
      previewClass: {
        type: String
      },
      boundingBoxClass: {
        type: String
      },
      linesClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      linesWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      }
    },
    data: function data() {
      return {
        moving: !1,
        resizing: !1
      };
    },
    computed: {
      classes: function classes() {
        return {
          stencil: l(Gt({
            movable: this.movable,
            moving: this.moving,
            resizing: this.resizing
          }), this.moving && this.movingClass, this.resizing && this.resizingClass),
          preview: l(Gt("preview"), this.previewClass),
          boundingBox: l(Gt("bounding-box"), this.boundingBoxClass)
        };
      },
      style: function style() {
        var t = this.stencilCoordinates,
          e = t.height,
          i = t.width,
          n = t.left,
          s = t.top,
          o = {
            width: "".concat(i, "px"),
            height: "".concat(e, "px"),
            transform: "translate(".concat(n, "px, ").concat(s, "px)")
          };
        return this.transitions && this.transitions.enabled && (o.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), o;
      }
    },
    methods: {
      onMove: function onMove(t) {
        this.$emit("move", t), this.moving = !0;
      },
      onMoveEnd: function onMoveEnd() {
        this.$emit("move-end"), this.moving = !1;
      },
      onResize: function onResize(t) {
        this.$emit("resize", t), this.resizing = !0;
      },
      onResizeEnd: function onResizeEnd() {
        this.$emit("resize-end"), this.resizing = !1;
      },
      aspectRatios: function aspectRatios() {
        return {
          minimum: this.aspectRatio || this.minAspectRatio,
          maximum: this.aspectRatio || this.maxAspectRatio
        };
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0),
  Kt = d("vue-circle-stencil"),
  Jt = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        "class": t.classes.stencil,
        style: t.style
      }, [i("bounding-box", {
        "class": t.classes.boundingBox,
        attrs: {
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions,
          handlers: t.handlers,
          "handlers-component": t.handlersComponent,
          "handlers-classes": t.handlersClasses,
          "handlers-wrappers-classes": t.handlersWrappersClasses,
          lines: t.lines,
          "lines-component": t.linesComponent,
          "lines-classes": t.linesClasses,
          "lines-wrappers-classes": t.linesWrappersClasses,
          resizable: t.resizable
        },
        on: {
          resize: t.onResize,
          "resize-end": t.onResizeEnd
        }
      }, [i("draggable-area", {
        attrs: {
          movable: t.movable
        },
        on: {
          move: t.onMove,
          "move-end": t.onMoveEnd
        }
      }, [i("stencil-preview", {
        "class": t.classes.preview,
        attrs: {
          image: t.image,
          coordinates: t.coordinates,
          width: t.stencilCoordinates.width,
          height: t.stencilCoordinates.height,
          transitions: t.transitions
        }
      })], 1)], 1)], 1);
    },
    staticRenderFns: []
  }, undefined, {
    components: {
      StencilPreview: qt,
      BoundingBox: Pt,
      DraggableArea: Lt
    },
    props: {
      image: {
        type: Object
      },
      coordinates: {
        type: Object
      },
      stencilCoordinates: {
        type: Object
      },
      handlers: {
        type: Object,
        "default": function _default() {
          return {
            eastNorth: !0,
            westNorth: !0,
            westSouth: !0,
            eastSouth: !0
          };
        }
      },
      handlersComponent: {
        type: [Object, String],
        "default": function _default() {
          return Et;
        }
      },
      handlersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      handlersWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      lines: {
        type: Object
      },
      linesComponent: {
        type: [Object, String],
        "default": function _default() {
          return Ot;
        }
      },
      linesClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      linesWrappersClasses: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      movable: {
        type: Boolean,
        "default": !0
      },
      resizable: {
        type: Boolean,
        "default": !0
      },
      transitions: {
        type: Object
      },
      movingClass: {
        type: String
      },
      resizingClass: {
        type: String
      },
      previewClass: {
        type: String
      },
      boundingBoxClass: {
        type: String
      }
    },
    data: function data() {
      return {
        moving: !1,
        resizing: !1
      };
    },
    computed: {
      classes: function classes() {
        return {
          stencil: l(Kt({
            movable: this.movable,
            moving: this.moving,
            resizing: this.resizing
          }), this.moving && this.movingClass, this.resizing && this.resizingClass),
          preview: l(Kt("preview"), this.previewClass),
          boundingBox: l(Kt("bounding-box"), this.boundingBoxClass)
        };
      },
      style: function style() {
        var t = this.stencilCoordinates,
          e = t.height,
          i = t.width,
          n = t.left,
          s = t.top,
          o = {
            width: "".concat(i, "px"),
            height: "".concat(e, "px"),
            transform: "translate(".concat(n, "px, ").concat(s, "px)")
          };
        return this.transitions && this.transitions.enabled && (o.transition = "".concat(this.transitions.time, "ms ").concat(this.transitions.timingFunction)), o;
      }
    },
    methods: {
      onMove: function onMove(t) {
        this.$emit("move", t), this.moving = !0;
      },
      onMoveEnd: function onMoveEnd() {
        this.$emit("move-end"), this.moving = !1;
      },
      onResize: function onResize(t) {
        this.$emit("resize", t), this.resizing = !0;
      },
      onResizeEnd: function onResizeEnd() {
        this.$emit("resize-end"), this.resizing = !1;
      },
      aspectRatios: function aspectRatios() {
        return {
          minimum: 1,
          maximum: 1
        };
      }
    }
  }, undefined, false, undefined, !1, void 0, void 0, void 0);
var te = ["transitions"],
  ee = d("vue-advanced-cropper"),
  ie = {
    name: "Cropper",
    components: {
      BackgroundWrapper: Nt
    },
    props: {
      src: {
        type: String,
        "default": null
      },
      stencilComponent: {
        type: [Object, String],
        "default": function _default() {
          return Qt;
        }
      },
      backgroundWrapperComponent: {
        type: [Object, String],
        "default": function _default() {
          return Nt;
        }
      },
      stencilProps: {
        type: Object,
        "default": function _default() {
          return {};
        }
      },
      autoZoom: {
        type: Boolean,
        "default": !1
      },
      imageClass: {
        type: String
      },
      boundariesClass: {
        type: String
      },
      backgroundClass: {
        type: String
      },
      foregroundClass: {
        type: String
      },
      minWidth: {
        type: [Number, String]
      },
      minHeight: {
        type: [Number, String]
      },
      maxWidth: {
        type: [Number, String]
      },
      maxHeight: {
        type: [Number, String]
      },
      debounce: {
        type: [Boolean, Number],
        "default": 500
      },
      transitions: {
        type: Boolean,
        "default": !0
      },
      checkOrientation: {
        type: Boolean,
        "default": !0
      },
      canvas: {
        type: [Object, Boolean],
        "default": !0
      },
      crossOrigin: {
        type: [Boolean, String],
        "default": void 0
      },
      transitionTime: {
        type: Number,
        "default": 300
      },
      imageRestriction: {
        type: String,
        "default": "fit-area",
        validator: function validator(t) {
          return -1 !== _.indexOf(t);
        }
      },
      roundResult: {
        type: Boolean,
        "default": !0
      },
      defaultSize: {
        type: [Function, Object]
      },
      defaultPosition: {
        type: [Function, Object],
        "default": function _default(t) {
          var e = t.imageSize,
            i = t.visibleArea,
            n = t.coordinates,
            s = i || e;
          return {
            left: (i ? i.left : 0) + s.width / 2 - n.width / 2,
            top: (i ? i.top : 0) + s.height / 2 - n.height / 2
          };
        }
      },
      defaultVisibleArea: {
        type: [Function, Object],
        "default": function _default(t) {
          var e = t.getAreaRestrictions,
            i = t.coordinates,
            n = t.imageSize,
            s = Q(t.boundaries);
          if (i) {
            var o = {
                height: Math.max(i.height, n.height),
                width: Math.max(i.width, n.width)
              },
              r = et({
                width: Q(o) > s ? o.width : o.height * s,
                height: Q(o) > s ? o.width / s : o.height
              }, rt(e())),
              a = {
                left: i.left + i.width / 2 - r.width / 2,
                top: i.top + i.height / 2 - r.height / 2,
                width: r.width,
                height: r.height
              },
              h = N(i, Y(_f({
                left: 0,
                top: 0
              }, n))),
              c = {};
            return !h.left && !h.right && a.width <= n.width && (c.left = 0, c.right = n.width), !h.top && !h.bottom && a.height <= n.height && (c.top = 0, c.bottom = n.height), ot(a, c);
          }
          var l = Q(n);
          return r = {
            height: l > s ? n.height : n.width / s,
            width: l > s ? n.height * s : n.width
          }, {
            left: n.width / 2 - r.width / 2,
            top: n.height / 2 - r.height / 2,
            width: r.width,
            height: r.height
          };
        }
      },
      defaultTransforms: {
        type: [Function, Object]
      },
      defaultBoundaries: {
        type: [Function, String],
        validator: function validator(t) {
          return !("string" == typeof t && "fill" !== t && "fit" !== t);
        }
      },
      priority: {
        type: String,
        "default": "coordinates"
      },
      stencilSize: {
        type: [Object, Function]
      },
      resizeImage: {
        type: [Boolean, Object],
        "default": !0
      },
      moveImage: {
        type: [Boolean, Object],
        "default": !0
      },
      autoZoomAlgorithm: {
        type: Function
      },
      resizeAlgorithm: {
        type: Function,
        "default": function _default(t) {
          var e = t.event,
            i = t.coordinates,
            n = t.aspectRatio,
            s = t.positionRestrictions,
            o = t.sizeRestrictions,
            r = _f(_f({}, i), {
              right: i.left + i.width,
              bottom: i.top + i.height
            }),
            a = e.params || {},
            h = _f({}, e.directions),
            c = a.allowedDirections || {
              left: !0,
              right: !0,
              bottom: !0,
              top: !0
            };
          o.widthFrozen && (h.left = 0, h.right = 0), o.heightFrozen && (h.top = 0, h.bottom = 0), D.forEach(function (t) {
            c[t] || (h[t] = 0);
          });
          var l = V(r, h = St({
              coordinates: r,
              directions: h,
              sizeRestrictions: o,
              positionRestrictions: s
            })).width,
            d = V(r, h).height,
            u = a.preserveRatio ? Q(r) : tt(l / d, n);
          if (u) {
            var m = a.respectDirection;
            if (m || (m = r.width >= r.height || 1 === u ? "width" : "height"), "width" === m) {
              var p = l / u - r.height;
              if (c.top && c.bottom) {
                var g = h.top,
                  v = h.bottom;
                h.bottom = xt(p, v, g), h.top = xt(p, g, v);
              } else c.bottom ? h.bottom = p : c.top ? h.top = p : c.right ? h.right = 0 : c.left && (h.left = 0);
            } else if ("height" === m) {
              var b = r.width - d * u;
              if (c.left && c.right) {
                var w = h.left,
                  y = h.right;
                h.left = -xt(b, w, y), h.right = -xt(b, y, w);
              } else c.left ? h.left = -b : c.right ? h.right = -b : c.top ? h.top = 0 : c.bottom && (h.bottom = 0);
            }
            h = St({
              directions: h,
              coordinates: r,
              sizeRestrictions: o,
              positionRestrictions: s,
              preserveRatio: !0,
              compensate: a.compensate
            });
          }
          return l = V(r, h).width, d = V(r, h).height, (u = a.preserveRatio ? Q(r) : tt(l / d, n)) && Math.abs(u - l / d) > .001 && D.forEach(function (t) {
            c[t] || (h[t] = 0);
          }), mt({
            event: new E({
              left: -h.left,
              top: -h.top
            }),
            coordinates: {
              width: i.width + h.right + h.left,
              height: i.height + h.top + h.bottom,
              left: i.left,
              top: i.top
            },
            positionRestrictions: s
          });
        }
      },
      moveAlgorithm: {
        type: Function,
        "default": mt
      },
      initStretcher: {
        type: Function,
        "default": function _default(t) {
          var e = t.stretcher,
            i = t.imageSize,
            n = Q(i);
          e.style.width = i.width + "px", e.style.height = e.clientWidth / n + "px", e.style.width = e.clientWidth + "px";
        }
      },
      fitCoordinates: {
        type: Function,
        "default": function _default(t) {
          var e = t.visibleArea,
            i = t.coordinates,
            n = t.aspectRatio,
            s = t.sizeRestrictions,
            o = t.positionRestrictions,
            r = _f(_f({}, i), ut({
              width: i.width,
              height: i.height,
              aspectRatio: n,
              sizeRestrictions: {
                maxWidth: e.width,
                maxHeight: e.height,
                minHeight: Math.min(e.height, s.minHeight),
                minWidth: Math.min(e.width, s.minWidth)
              }
            }));
          return r = ot(r = q(r, k(U(i), U(r))), ct(Y(e), o));
        }
      },
      fitVisibleArea: {
        type: Function,
        "default": function _default(t) {
          var e = t.visibleArea,
            i = t.boundaries,
            n = t.getAreaRestrictions,
            s = t.coordinates,
            o = _f({}, e);
          o.height = o.width / Q(i), o.top += (e.height - o.height) / 2, (s.height - o.height > 0 || s.width - o.width > 0) && (o = G(o, Math.max(s.height / o.height, s.width / o.width)));
          var r = Z(J(s, Y(o = G(o, st(o, n({
            visibleArea: o,
            type: "resize"
          }))))));
          return o.width < s.width && (r.left = 0), o.height < s.height && (r.top = 0), o = ot(o = q(o, r), n({
            visibleArea: o,
            type: "move"
          }));
        }
      },
      areaRestrictionsAlgorithm: {
        type: Function,
        "default": function _default(t) {
          var e = t.visibleArea,
            i = t.boundaries,
            n = t.imageSize,
            s = t.imageRestriction,
            o = t.type,
            r = {};
          return "fill-area" === s ? r = {
            left: 0,
            top: 0,
            right: n.width,
            bottom: n.height
          } : "fit-area" === s && (Q(i) > Q(n) ? (r = {
            top: 0,
            bottom: n.height
          }, e && "move" === o && (e.width > n.width ? (r.left = -(e.width - n.width) / 2, r.right = n.width - r.left) : (r.left = 0, r.right = n.width))) : (r = {
            left: 0,
            right: n.width
          }, e && "move" === o && (e.height > n.height ? (r.top = -(e.height - n.height) / 2, r.bottom = n.height - r.top) : (r.top = 0, r.bottom = n.height)))), r;
        }
      },
      sizeRestrictionsAlgorithm: {
        type: Function,
        "default": function _default(t) {
          return {
            minWidth: t.minWidth,
            minHeight: t.minHeight,
            maxWidth: t.maxWidth,
            maxHeight: t.maxHeight
          };
        }
      },
      positionRestrictionsAlgorithm: {
        type: Function,
        "default": function _default(t) {
          var e = t.imageSize,
            i = {};
          return "none" !== t.imageRestriction && (i = {
            left: 0,
            top: 0,
            right: e.width,
            bottom: e.height
          }), i;
        }
      }
    },
    data: function data() {
      return {
        transitionsActive: !1,
        imageLoaded: !1,
        imageAttributes: {
          width: null,
          height: null,
          crossOrigin: !1,
          src: null
        },
        defaultImageTransforms: {
          rotate: 0,
          flip: {
            horizontal: !1,
            vertical: !1
          }
        },
        appliedImageTransforms: {
          rotate: 0,
          flip: {
            horizontal: !1,
            vertical: !1
          }
        },
        boundaries: {
          width: 0,
          height: 0
        },
        visibleArea: null,
        coordinates: i({}, F)
      };
    },
    computed: {
      image: function image() {
        return {
          src: this.imageAttributes.src,
          width: this.imageAttributes.width,
          height: this.imageAttributes.height,
          transforms: this.imageTransforms
        };
      },
      imageTransforms: function imageTransforms() {
        return {
          rotate: this.appliedImageTransforms.rotate,
          flip: {
            horizontal: this.appliedImageTransforms.flip.horizontal,
            vertical: this.appliedImageTransforms.flip.vertical
          },
          translateX: this.visibleArea ? this.visibleArea.left / this.coefficient : 0,
          translateY: this.visibleArea ? this.visibleArea.top / this.coefficient : 0,
          scaleX: 1 / this.coefficient,
          scaleY: 1 / this.coefficient
        };
      },
      imageSize: function imageSize() {
        var t = function (t) {
          return t * Math.PI / 180;
        }(this.imageTransforms.rotate);
        return {
          width: Math.abs(this.imageAttributes.width * Math.cos(t)) + Math.abs(this.imageAttributes.height * Math.sin(t)),
          height: Math.abs(this.imageAttributes.width * Math.sin(t)) + Math.abs(this.imageAttributes.height * Math.cos(t))
        };
      },
      initialized: function initialized() {
        return Boolean(this.visibleArea && this.imageLoaded);
      },
      settings: function settings() {
        var t = z(this.resizeImage, {
          touch: !0,
          wheel: {
            ratio: .1
          },
          adjustStencil: !0
        }, {
          touch: !1,
          wheel: !1,
          adjustStencil: !1
        });
        return {
          moveImage: z(this.moveImage, {
            touch: !0,
            mouse: !0
          }, {
            touch: !1,
            mouse: !1
          }),
          resizeImage: t
        };
      },
      coefficient: function coefficient() {
        return this.visibleArea ? this.visibleArea.width / this.boundaries.width : 0;
      },
      areaRestrictions: function areaRestrictions() {
        return this.imageLoaded ? this.areaRestrictionsAlgorithm({
          imageSize: this.imageSize,
          imageRestriction: this.imageRestriction,
          boundaries: this.boundaries
        }) : {};
      },
      transitionsOptions: function transitionsOptions() {
        return {
          enabled: this.transitionsActive,
          timingFunction: "ease-in-out",
          time: 350
        };
      },
      sizeRestrictions: function sizeRestrictions() {
        if (this.boundaries.width && this.boundaries.height && this.imageSize.width && this.imageSize.height) {
          var t = this.sizeRestrictionsAlgorithm({
            imageSize: this.imageSize,
            minWidth: w(this.minWidth) ? 0 : R(this.minWidth),
            minHeight: w(this.minHeight) ? 0 : R(this.minHeight),
            maxWidth: w(this.maxWidth) ? 1 / 0 : R(this.maxWidth),
            maxHeight: w(this.maxHeight) ? 1 / 0 : R(this.maxHeight)
          });
          if (t = function (t) {
            var e = t.areaRestrictions,
              i = t.sizeRestrictions;
            t.imageSize;
            var n = t.boundaries,
              s = t.positionRestrictions;
            t.imageRestriction;
            var o = _f(_f({}, i), {
              minWidth: void 0 !== i.minWidth ? i.minWidth : 0,
              minHeight: void 0 !== i.minHeight ? i.minHeight : 0,
              maxWidth: void 0 !== i.maxWidth ? i.maxWidth : 1 / 0,
              maxHeight: void 0 !== i.maxHeight ? i.maxHeight : 1 / 0
            });
            void 0 !== s.left && void 0 !== s.right && (o.maxWidth = Math.min(o.maxWidth, s.right - s.left)), void 0 !== s.bottom && void 0 !== s.top && (o.maxHeight = Math.min(o.maxHeight, s.bottom - s.top));
            var r = rt(e),
              a = et(n, r);
            return r.width < 1 / 0 && (!o.maxWidth || o.maxWidth > a.width) && (o.maxWidth = Math.min(o.maxWidth, a.width)), r.height < 1 / 0 && (!o.maxHeight || o.maxHeight > a.height) && (o.maxHeight = Math.min(o.maxHeight, a.height)), o.minWidth > o.maxWidth && (o.minWidth = o.maxWidth, o.widthFrozen = !0), o.minHeight > o.maxHeight && (o.minHeight = o.maxHeight, o.heightFrozen = !0), o;
          }({
            sizeRestrictions: t,
            areaRestrictions: this.getAreaRestrictions({
              visibleArea: this.visibleArea,
              type: "resize"
            }),
            imageSize: this.imageSize,
            boundaries: this.boundaries,
            positionRestrictions: this.positionRestrictions,
            imageRestriction: this.imageRestriction,
            visibleArea: this.visibleArea,
            stencilSize: this.getStencilSize()
          }), this.visibleArea && this.stencilSize) {
            var e = this.getStencilSize(),
              i = rt(this.getAreaRestrictions({
                visibleArea: this.visibleArea,
                type: "resize"
              }));
            t.maxWidth = Math.min(t.maxWidth, i.width * e.width / this.boundaries.width), t.maxHeight = Math.min(t.maxHeight, i.height * e.height / this.boundaries.height), t.maxWidth < t.minWidth && (t.minWidth = t.maxWidth), t.maxHeight < t.minHeight && (t.minHeight = t.maxHeight);
          }
          return t;
        }
        return {
          minWidth: 0,
          minHeight: 0,
          maxWidth: 0,
          maxHeight: 0
        };
      },
      positionRestrictions: function positionRestrictions() {
        return this.positionRestrictionsAlgorithm({
          imageSize: this.imageSize,
          imageRestriction: this.imageRestriction
        });
      },
      classes: function classes() {
        return {
          cropper: ee(),
          image: l(ee("image"), this.imageClass),
          stencil: ee("stencil"),
          boundaries: l(ee("boundaries"), this.boundariesClass),
          stretcher: l(ee("stretcher")),
          background: l(ee("background"), this.backgroundClass),
          foreground: l(ee("foreground"), this.foregroundClass),
          imageWrapper: l(ee("image-wrapper")),
          cropperWrapper: l(ee("cropper-wrapper"))
        };
      },
      stencilCoordinates: function stencilCoordinates() {
        if (this.initialized) {
          var t = this.coordinates,
            e = t.width,
            i = t.height,
            n = t.left,
            s = t.top;
          return {
            width: e / this.coefficient,
            height: i / this.coefficient,
            left: (n - this.visibleArea.left) / this.coefficient,
            top: (s - this.visibleArea.top) / this.coefficient
          };
        }
        return this.defaultCoordinates();
      },
      boundariesStyle: function boundariesStyle() {
        var t = {
          width: this.boundaries.width ? "".concat(Math.round(this.boundaries.width), "px") : "auto",
          height: this.boundaries.height ? "".concat(Math.round(this.boundaries.height), "px") : "auto",
          transition: "opacity ".concat(this.transitionTime, "ms"),
          pointerEvents: this.imageLoaded ? "all" : "none"
        };
        return this.imageLoaded || (t.opacity = "0"), t;
      },
      imageStyle: function imageStyle() {
        var t = this.imageAttributes.width > this.imageAttributes.height ? {
            width: Math.min(1024, this.imageAttributes.width),
            height: Math.min(1024, this.imageAttributes.width) / (this.imageAttributes.width / this.imageAttributes.height)
          } : {
            height: Math.min(1024, this.imageAttributes.height),
            width: Math.min(1024, this.imageAttributes.height) * (this.imageAttributes.width / this.imageAttributes.height)
          },
          e = {
            left: (t.width - this.imageSize.width) / (2 * this.coefficient),
            top: (t.height - this.imageSize.height) / (2 * this.coefficient)
          },
          n = {
            left: (1 - 1 / this.coefficient) * t.width / 2,
            top: (1 - 1 / this.coefficient) * t.height / 2
          },
          s = i(i({}, this.imageTransforms), {}, {
            scaleX: this.imageTransforms.scaleX * (this.imageAttributes.width / t.width),
            scaleY: this.imageTransforms.scaleY * (this.imageAttributes.height / t.height)
          }),
          o = {
            width: "".concat(t.width, "px"),
            height: "".concat(t.height, "px"),
            left: "0px",
            top: "0px",
            transform: "translate(".concat(-e.left - n.left - this.imageTransforms.translateX, "px, ").concat(-e.top - n.top - this.imageTransforms.translateY, "px)") + _t(s)
          };
        return this.transitionsOptions.enabled && (o.transition = "".concat(this.transitionsOptions.time, "ms ").concat(this.transitionsOptions.timingFunction)), o;
      }
    },
    watch: {
      src: function src() {
        this.onChangeImage();
      },
      stencilComponent: function stencilComponent() {
        var t = this;
        this.$nextTick(function () {
          t.resetCoordinates(), t.runAutoZoom("setCoordinates"), t.onChange();
        });
      },
      minWidth: function minWidth() {
        this.onPropsChange();
      },
      maxWidth: function maxWidth() {
        this.onPropsChange();
      },
      minHeight: function minHeight() {
        this.onPropsChange();
      },
      maxHeight: function maxHeight() {
        this.onPropsChange();
      },
      imageRestriction: function imageRestriction() {
        this.reset();
      },
      stencilProps: function stencilProps(t, e) {
        ["aspectRatio", "minAspectRatio", "maxAspectRatio"].find(function (i) {
          return t[i] !== e[i];
        }) && this.$nextTick(this.onPropsChange);
      }
    },
    created: function created() {
      this.debouncedUpdate = m(this.update, this.debounce), this.debouncedDisableTransitions = m(this.disableTransitions, this.transitionsOptions.time), this.awaiting = !1;
    },
    mounted: function mounted() {
      this.$refs.image.addEventListener("load", this.onSuccessLoadImage), this.$refs.image.addEventListener("error", this.onFailLoadImage), this.onChangeImage(), window.addEventListener("resize", this.refresh), window.addEventListener("orientationchange", this.refresh);
    },
    destroyed: function destroyed() {
      window.removeEventListener("resize", this.refresh), window.removeEventListener("orientationchange", this.refresh), this.imageAttributes.revoke && this.imageAttributes.src && URL.revokeObjectURL(this.imageAttributes.src), this.debouncedUpdate.clear(), this.debouncedDisableTransitions.clear();
    },
    methods: {
      getResult: function getResult() {
        var t = this.initialized ? this.prepareResult(i({}, this.coordinates)) : this.defaultCoordinates(),
          e = {
            rotate: this.imageTransforms.rotate % 360,
            flip: i({}, this.imageTransforms.flip)
          };
        if (this.src && this.imageLoaded) {
          var n = this;
          return {
            image: this.image,
            coordinates: t,
            visibleArea: this.visibleArea ? i({}, this.visibleArea) : null,
            imageTransforms: e,
            get canvas() {
              return n.canvas ? n.getCanvas() : void 0;
            }
          };
        }
        return {
          image: this.image,
          coordinates: t,
          visibleArea: this.visibleArea ? i({}, this.visibleArea) : null,
          canvas: void 0,
          imageTransforms: e
        };
      },
      zoom: function zoom(t, e) {
        var i = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
          n = i.transitions,
          s = void 0 === n || n;
        this.onManipulateImage(new M({}, {
          factor: 1 / t,
          center: e
        }), {
          normalize: !1,
          transitions: s
        });
      },
      move: function move(t, e) {
        var i = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
          n = i.transitions,
          s = void 0 === n || n;
        this.onManipulateImage(new M({
          left: t || 0,
          top: e || 0
        }), {
          normalize: !1,
          transitions: s
        });
      },
      setCoordinates: function setCoordinates(t) {
        var e = this,
          i = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          n = i.autoZoom,
          s = void 0 === n || n,
          o = i.transitions,
          r = void 0 === o || o;
        this.$nextTick(function () {
          e.imageLoaded ? (e.transitionsActive || (r && e.enableTransitions(), e.coordinates = e.applyTransform(t), s && e.runAutoZoom("setCoordinates"), r && e.debouncedDisableTransitions()), e.onChange()) : e.delayedTransforms = t;
        });
      },
      refresh: function refresh() {
        var t = this,
          e = this.$refs.image;
        if (this.src && e) return this.initialized ? this.updateVisibleArea().then(function () {
          t.onChange();
        }) : this.resetVisibleArea().then(function () {
          t.onChange();
        });
      },
      reset: function reset() {
        var t = this;
        return this.resetVisibleArea().then(function () {
          t.onChange(!1);
        });
      },
      awaitRender: function awaitRender(t) {
        var e = this;
        this.awaiting || (this.awaiting = !0, this.$nextTick(function () {
          t(), e.awaiting = !1;
        }));
      },
      prepareResult: function prepareResult(t) {
        return this.roundResult ? function (t) {
          var e = t.coordinates,
            i = t.sizeRestrictions,
            n = t.positionRestrictions,
            s = {
              width: Math.round(e.width),
              height: Math.round(e.height),
              left: Math.round(e.left),
              top: Math.round(e.top)
            };
          return s.width > i.maxWidth ? s.width = Math.floor(e.width) : s.width < i.minWidth && (s.width = Math.ceil(e.width)), s.height > i.maxHeight ? s.height = Math.floor(e.height) : s.height < i.minHeight && (s.height = Math.ceil(e.height)), ot(s, n);
        }(i(i({}, this.getPublicProperties()), {}, {
          positionRestrictions: yt(this.positionRestrictions, this.visibleArea),
          coordinates: t
        })) : t;
      },
      processAutoZoom: function processAutoZoom(t, e, n, s) {
        var o = this.autoZoomAlgorithm;
        o || (o = this.stencilSize ? pt : this.autoZoom ? gt : vt);
        var r = o({
          event: {
            type: t,
            params: s
          },
          visibleArea: e,
          coordinates: n,
          boundaries: this.boundaries,
          aspectRatio: this.getAspectRatio(),
          positionRestrictions: this.positionRestrictions,
          getAreaRestrictions: this.getAreaRestrictions,
          sizeRestrictions: this.sizeRestrictions,
          stencilSize: this.getStencilSize()
        });
        return i(i({}, r), {}, {
          changed: !X(r.visibleArea, e) || !X(r.coordinates, n)
        });
      },
      runAutoZoom: function runAutoZoom(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          i = e.transitions,
          n = void 0 !== i && i,
          o = s(e, te),
          r = this.processAutoZoom(t, this.visibleArea, this.coordinates, o),
          a = r.visibleArea,
          h = r.coordinates,
          c = r.changed;
        n && c && this.enableTransitions(), this.visibleArea = a, this.coordinates = h, n && c && this.debouncedDisableTransitions();
      },
      normalizeEvent: function normalizeEvent(t) {
        return function (t) {
          var e = t.event,
            i = t.visibleArea,
            n = t.coefficient;
          if ("manipulateImage" === e.type) return _f(_f({}, e), {
            move: {
              left: e.move && e.move.left ? n * e.move.left : 0,
              top: e.move && e.move.top ? n * e.move.top : 0
            },
            scale: {
              factor: e.scale && e.scale.factor ? e.scale.factor : 1,
              center: e.scale && e.scale.center ? {
                left: e.scale.center.left * n + i.left,
                top: e.scale.center.top * n + i.top
              } : null
            }
          });
          if ("resize" === e.type) {
            var s = _f(_f({}, e), {
              directions: _f({}, e.directions)
            });
            return D.forEach(function (t) {
              s.directions[t] *= n;
            }), s;
          }
          if ("move" === e.type) {
            var o = _f(_f({}, e), {
              directions: _f({}, e.directions)
            });
            return B.forEach(function (t) {
              o.directions[t] *= n;
            }), o;
          }
          return e;
        }(i(i({}, this.getPublicProperties()), {}, {
          event: t
        }));
      },
      getCanvas: function getCanvas() {
        if (this.$refs.canvas) {
          var t = this.$refs.canvas,
            e = this.$refs.image,
            n = 0 !== this.imageTransforms.rotate || this.imageTransforms.flip.horizontal || this.imageTransforms.flip.vertical ? function (t, e, i) {
              var n = i.rotate,
                s = i.flip,
                o = {
                  width: e.naturalWidth,
                  height: e.naturalHeight
                },
                r = it(o, n),
                a = t.getContext("2d");
              t.height = r.height, t.width = r.width, a.save();
              var h = nt(U(_f({
                left: 0,
                top: 0
              }, o)), n);
              return a.translate(-(h.left - r.width / 2), -(h.top - r.height / 2)), a.rotate(n * Math.PI / 180), a.translate(s.horizontal ? o.width : 0, s.vertical ? o.height : 0), a.scale(s.horizontal ? -1 : 1, s.vertical ? -1 : 1), a.drawImage(e, 0, 0, o.width, o.height), a.restore(), t;
            }(this.$refs.sourceCanvas, e, this.imageTransforms) : e,
            s = i({
              minWidth: 0,
              minHeight: 0,
              maxWidth: 1 / 0,
              maxHeight: 1 / 0,
              maxArea: this.maxCanvasSize,
              imageSmoothingEnabled: !0,
              imageSmoothingQuality: "high",
              fillColor: "transparent"
            }, this.canvas),
            o = function o(t) {
              return t.find(function (t) {
                return e = t, !Number.isNaN(parseFloat(e)) && isFinite(e);
                var e;
              });
            },
            r = ut({
              sizeRestrictions: {
                minWidth: o([s.width, s.minWidth]) || 0,
                minHeight: o([s.height, s.minHeight]) || 0,
                maxWidth: o([s.width, s.maxWidth]) || 1 / 0,
                maxHeight: o([s.height, s.maxHeight]) || 1 / 0
              },
              width: this.coordinates.width,
              height: this.coordinates.height,
              aspectRatio: {
                minimum: this.coordinates.width / this.coordinates.height,
                maximum: this.coordinates.width / this.coordinates.height
              }
            });
          if (s.maxArea && r.width * r.height > s.maxArea) {
            var a = Math.sqrt(s.maxArea / (r.width * r.height));
            r = {
              width: Math.round(a * r.width),
              height: Math.round(a * r.height)
            };
          }
          return function (t, e, i, n, s) {
            t.width = n ? n.width : i.width, t.height = n ? n.height : i.height;
            var o = t.getContext("2d");
            o.clearRect(0, 0, t.width, t.height), s && (s.imageSmoothingEnabled && (o.imageSmoothingEnabled = s.imageSmoothingEnabled), s.imageSmoothingQuality && (o.imageSmoothingQuality = s.imageSmoothingQuality), s.fillColor && (o.fillStyle = s.fillColor, o.fillRect(0, 0, t.width, t.height), o.save()));
            var r = i.left < 0 ? -i.left : 0,
              a = i.top < 0 ? -i.top : 0;
            o.drawImage(e, i.left + r, i.top + a, i.width, i.height, r, a, t.width, t.height);
          }(t, n, this.coordinates, r, s), t;
        }
      },
      update: function update() {
        this.$emit("change", this.getResult());
      },
      applyTransform: function applyTransform(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] && arguments[1],
          i = this.visibleArea && e ? at(this.sizeRestrictions, this.visibleArea) : this.sizeRestrictions,
          n = this.visibleArea && e ? yt(this.positionRestrictions, this.visibleArea) : this.positionRestrictions;
        return ft({
          transform: t,
          coordinates: this.coordinates,
          imageSize: this.imageSize,
          sizeRestrictions: i,
          positionRestrictions: n,
          aspectRatio: this.getAspectRatio(),
          visibleArea: this.visibleArea
        });
      },
      resetCoordinates: function resetCoordinates() {
        var t = this;
        if (this.$refs.image) {
          this.$refs.cropper, this.$refs.image;
          var e = this.defaultSize;
          e || (e = this.stencilSize ? wt : bt);
          var n = this.sizeRestrictions;
          n.minWidth, n.minHeight, n.maxWidth, n.maxHeight;
          var s = [b(e) ? e({
            boundaries: this.boundaries,
            imageSize: this.imageSize,
            aspectRatio: this.getAspectRatio(),
            sizeRestrictions: this.sizeRestrictions,
            stencilSize: this.getStencilSize(),
            visibleArea: this.visibleArea
          }) : e, function (e) {
            var n = e.coordinates;
            return i({}, b(t.defaultPosition) ? t.defaultPosition({
              coordinates: n,
              imageSize: t.imageSize,
              visibleArea: t.visibleArea
            }) : t.defaultPosition);
          }];
          this.delayedTransforms && s.push.apply(s, o(Array.isArray(this.delayedTransforms) ? this.delayedTransforms : [this.delayedTransforms])), this.coordinates = this.applyTransform(s, !0), this.delayedTransforms = null;
        }
      },
      clearImage: function clearImage() {
        var t = this;
        this.imageLoaded = !1, setTimeout(function () {
          var e = t.$refs.stretcher;
          e && (e.style.height = "auto", e.style.width = "auto"), t.coordinates = t.defaultCoordinates(), t.boundaries = {
            width: 0,
            height: 0
          };
        }, this.transitionTime);
      },
      enableTransitions: function enableTransitions() {
        this.transitions && (this.transitionsActive = !0);
      },
      disableTransitions: function disableTransitions() {
        this.transitionsActive = !1;
      },
      updateBoundaries: function updateBoundaries() {
        var t = this,
          e = this.$refs.stretcher,
          i = this.$refs.cropper;
        return this.initStretcher({
          cropper: i,
          stretcher: e,
          imageSize: this.imageSize
        }), this.$nextTick().then(function () {
          var e = {
            cropper: i,
            imageSize: t.imageSize
          };
          if (b(t.defaultBoundaries) ? t.boundaries = t.defaultBoundaries(e) : "fit" === t.defaultBoundaries ? t.boundaries = function (t) {
            var e = t.cropper,
              i = t.imageSize,
              n = e.clientHeight,
              s = e.clientWidth,
              o = n,
              r = i.width * n / i.height;
            return r > s && (r = s, o = i.height * s / i.width), {
              width: r,
              height: o
            };
          }(e) : t.boundaries = function (t) {
            var e = t.cropper;
            return {
              width: e.clientWidth,
              height: e.clientHeight
            };
          }(e), !t.boundaries.width || !t.boundaries.height) throw new Error("It's impossible to fit the cropper in the current container");
        });
      },
      resetVisibleArea: function resetVisibleArea() {
        var t = this;
        return this.appliedImageTransforms = i(i({}, this.defaultImageTransforms), {}, {
          flip: i({}, this.defaultImageTransforms.flip)
        }), this.updateBoundaries().then(function () {
          var e, i, n, s, o, r;
          "visible-area" !== t.priority && (t.visibleArea = null, t.resetCoordinates()), t.visibleArea = b(t.defaultVisibleArea) ? t.defaultVisibleArea({
            imageSize: t.imageSize,
            boundaries: t.boundaries,
            coordinates: "visible-area" !== t.priority ? t.coordinates : null,
            getAreaRestrictions: t.getAreaRestrictions,
            stencilSize: t.getStencilSize()
          }) : t.defaultVisibleArea, t.visibleArea = (e = {
            visibleArea: t.visibleArea,
            boundaries: t.boundaries,
            getAreaRestrictions: t.getAreaRestrictions
          }, i = e.visibleArea, n = e.boundaries, s = e.getAreaRestrictions, o = _f({}, i), r = Q(n), o.width / o.height !== r && (o.height = o.width / r), ot(o, s({
            visibleArea: o,
            type: "move"
          }))), "visible-area" === t.priority ? t.resetCoordinates() : t.coordinates = t.fitCoordinates({
            visibleArea: t.visibleArea,
            coordinates: t.coordinates,
            aspectRatio: t.getAspectRatio(),
            positionRestrictions: t.positionRestrictions,
            sizeRestrictions: t.sizeRestrictions
          }), t.runAutoZoom("resetVisibleArea");
        })["catch"](function () {
          t.visibleArea = null;
        });
      },
      updateVisibleArea: function updateVisibleArea() {
        var t = this;
        return this.updateBoundaries().then(function () {
          t.visibleArea = t.fitVisibleArea({
            imageSize: t.imageSize,
            boundaries: t.boundaries,
            visibleArea: t.visibleArea,
            coordinates: t.coordinates,
            getAreaRestrictions: t.getAreaRestrictions
          }), t.coordinates = t.fitCoordinates({
            visibleArea: t.visibleArea,
            coordinates: t.coordinates,
            aspectRatio: t.getAspectRatio(),
            positionRestrictions: t.positionRestrictions,
            sizeRestrictions: t.sizeRestrictions
          }), t.runAutoZoom("updateVisibleArea");
        })["catch"](function () {
          t.visibleArea = null;
        });
      },
      onChange: function onChange() {
        var t = !(arguments.length > 0 && void 0 !== arguments[0]) || arguments[0];
        this.$listeners && this.$listeners.change && (t && this.debounce ? this.debouncedUpdate() : this.update());
      },
      onChangeImage: function onChangeImage() {
        var t,
          e = this;
        if (this.imageLoaded = !1, this.delayedTransforms = null, this.src) {
          if (function (t) {
            if (v(t)) return !1;
            var e = window.location,
              i = /(\w+:)?(?:\/\/)([\w.-]+)?(?::(\d+))?\/?/.exec(t) || [],
              n = {
                protocol: i[1] || "",
                host: i[2] || "",
                port: i[3] || ""
              },
              s = function s(t) {
                return t.port || ("http" === (t.protocol || e.protocol) ? 80 : 433);
              };
            return !(!n.protocol && !n.host && !n.port || Boolean(n.protocol && n.protocol == e.protocol && n.host && n.host == e.host && n.host && s(n) == s(e)));
          }(this.src)) {
            var i = w(this.crossOrigin) ? this.canvas : this.crossOrigin;
            !0 === i && (i = "anonymous"), this.imageAttributes.crossOrigin = i;
          }
          if (this.checkOrientation) {
            var n = (t = this.src, new Promise(function (e) {
              Bt(t).then(function (i) {
                var n = Ft(i);
                e(i ? {
                  source: t,
                  arrayBuffer: i,
                  orientation: n
                } : {
                  source: t,
                  arrayBuffer: null,
                  orientation: null
                });
              })["catch"](function (i) {
                console.warn(i), e({
                  source: t,
                  arrayBuffer: null,
                  orientation: null
                });
              });
            }));
            setTimeout(function () {
              n.then(e.onParseImage);
            }, this.transitionTime);
          } else setTimeout(function () {
            e.onParseImage({
              source: e.src
            });
          }, this.transitionTime);
        } else this.clearImage();
      },
      onFailLoadImage: function onFailLoadImage() {
        this.imageAttributes.src && (this.clearImage(), this.$emit("error"));
      },
      onSuccessLoadImage: function onSuccessLoadImage() {
        var t = this,
          e = this.$refs.image;
        e && !this.imageLoaded && (this.imageAttributes.height = e.naturalHeight, this.imageAttributes.width = e.naturalWidth, this.imageLoaded = !0, this.resetVisibleArea().then(function () {
          t.$emit("ready"), t.onChange(!1);
        }));
      },
      onParseImage: function onParseImage(t) {
        var e = this,
          n = t.source,
          s = t.arrayBuffer,
          o = t.orientation;
        this.imageAttributes.revoke && this.imageAttributes.src && URL.revokeObjectURL(this.imageAttributes.src), this.imageAttributes.revoke = !1, s && o && o > 1 ? g(n) || !v(n) ? (this.imageAttributes.src = URL.createObjectURL(new Blob([s])), this.imageAttributes.revoke = !0) : this.imageAttributes.src = function (t) {
          for (var e = [], i = new Uint8Array(t); i.length > 0;) {
            var n = i.subarray(0, 8192);
            e.push(String.fromCharCode.apply(null, Array.from ? Array.from(n) : n.slice())), i = i.subarray(8192);
          }
          return "data:image/jpeg;base64," + btoa(e.join(""));
        }(s) : this.imageAttributes.src = n, b(this.defaultTransforms) ? this.appliedImageTransforms = It(this.defaultTransforms()) : y(this.defaultTransforms) ? this.appliedImageTransforms = It(this.defaultTransforms) : this.appliedImageTransforms = function (t) {
          var e = It({});
          if (t) switch (t) {
            case 2:
              e.flip.horizontal = !0;
              break;
            case 3:
              e.rotate = -180;
              break;
            case 4:
              e.flip.vertical = !0;
              break;
            case 5:
              e.rotate = 90, e.flip.vertical = !0;
              break;
            case 6:
              e.rotate = 90;
              break;
            case 7:
              e.rotate = 90, e.flip.horizontal = !0;
              break;
            case 8:
              e.rotate = -90;
          }
          return e;
        }(o), this.defaultImageTransforms = i(i({}, this.appliedImageTransforms), {}, {
          flip: i({}, this.appliedImageTransforms.flip)
        }), this.$nextTick(function () {
          var t = e.$refs.image;
          t && t.complete && (!function (t) {
            return Boolean(t.naturalWidth);
          }(t) ? e.onFailLoadImage() : e.onSuccessLoadImage());
        });
      },
      onResizeEnd: function onResizeEnd() {
        this.runAutoZoom("resize", {
          transitions: !0
        });
      },
      onMoveEnd: function onMoveEnd() {
        this.runAutoZoom("move", {
          transitions: !0
        });
      },
      onMove: function onMove(t) {
        var e = this;
        this.transitionsOptions.enabled || this.awaitRender(function () {
          e.coordinates = e.moveAlgorithm(i(i({}, e.getPublicProperties()), {}, {
            positionRestrictions: yt(e.positionRestrictions, e.visibleArea),
            coordinates: e.coordinates,
            event: e.normalizeEvent(t)
          })), e.onChange();
        });
      },
      onResize: function onResize(t) {
        var e = this;
        this.transitionsOptions.enabled || this.stencilSize && !this.autoZoom || this.awaitRender(function () {
          var n = e.sizeRestrictions,
            s = Math.min(e.coordinates.width, e.coordinates.height, 20 * e.coefficient);
          e.coordinates = e.resizeAlgorithm(i(i({}, e.getPublicProperties()), {}, {
            positionRestrictions: yt(e.positionRestrictions, e.visibleArea),
            sizeRestrictions: {
              maxWidth: Math.min(n.maxWidth, e.visibleArea.width),
              maxHeight: Math.min(n.maxHeight, e.visibleArea.height),
              minWidth: Math.max(n.minWidth, s),
              minHeight: Math.max(n.minHeight, s)
            },
            event: e.normalizeEvent(t)
          })), e.onChange(), e.ticking = !1;
        });
      },
      onManipulateImage: function onManipulateImage(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
        if (!this.transitionsOptions.enabled) {
          var n = e.transitions,
            s = void 0 !== n && n,
            o = e.normalize,
            r = void 0 === o || o;
          s && this.enableTransitions();
          var a = zt(i(i({}, this.getPublicProperties()), {}, {
              event: r ? this.normalizeEvent(t) : t,
              getAreaRestrictions: this.getAreaRestrictions,
              imageRestriction: this.imageRestriction,
              adjustStencil: !this.stencilSize && this.settings.resizeImage.adjustStencil
            })),
            h = a.visibleArea,
            c = a.coordinates;
          this.visibleArea = h, this.coordinates = c, this.runAutoZoom("manipulateImage"), this.onChange(), s && this.debouncedDisableTransitions();
        }
      },
      onPropsChange: function onPropsChange() {
        this.coordinates = this.applyTransform(this.coordinates, !0), this.onChange(!1);
      },
      getAreaRestrictions: function getAreaRestrictions() {
        var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
          e = t.visibleArea,
          i = t.type,
          n = void 0 === i ? "move" : i;
        return this.areaRestrictionsAlgorithm({
          boundaries: this.boundaries,
          imageSize: this.imageSize,
          imageRestriction: this.imageRestriction,
          visibleArea: e,
          type: n
        });
      },
      getAspectRatio: function getAspectRatio(t) {
        var e,
          i,
          n = this.stencilProps,
          s = n.aspectRatio,
          o = n.minAspectRatio,
          r = n.maxAspectRatio;
        if (this.$refs.stencil && this.$refs.stencil.aspectRatios) {
          var a = this.$refs.stencil.aspectRatios();
          e = a.minimum, i = a.maximum;
        }
        if (w(e) && (e = w(s) ? o : s), w(i) && (i = w(s) ? r : s), !t && (w(e) || w(i))) {
          var h = this.getStencilSize(),
            c = h ? Q(h) : null;
          w(e) && (e = A(c) ? c : void 0), w(i) && (i = A(c) ? c : void 0);
        }
        return {
          minimum: e,
          maximum: i
        };
      },
      getStencilSize: function getStencilSize() {
        if (this.stencilSize) return t = {
          currentStencilSize: {
            width: this.stencilCoordinates.width,
            height: this.stencilCoordinates.height
          },
          stencilSize: this.stencilSize,
          boundaries: this.boundaries,
          coefficient: this.coefficient,
          coordinates: this.coordinates,
          aspectRatio: this.getAspectRatio(!0)
        }, e = t.boundaries, i = t.stencilSize, n = t.aspectRatio, tt(Q(s = b(i) ? i({
          boundaries: e,
          aspectRatio: n
        }) : i), n) && (s = ut({
          sizeRestrictions: {
            maxWidth: e.width,
            maxHeight: e.height,
            minWidth: 0,
            minHeight: 0
          },
          width: s.width,
          height: s.height,
          aspectRatio: {
            minimum: n.minimum,
            maximum: n.maximum
          }
        })), (s.width > e.width || s.height > e.height) && (s = ut({
          sizeRestrictions: {
            maxWidth: e.width,
            maxHeight: e.height,
            minWidth: 0,
            minHeight: 0
          },
          width: s.width,
          height: s.height,
          aspectRatio: {
            minimum: Q(s),
            maximum: Q(s)
          }
        })), s;
        var t, e, i, n, s;
      },
      getPublicProperties: function getPublicProperties() {
        return {
          coefficient: this.coefficient,
          visibleArea: this.visibleArea,
          coordinates: this.coordinates,
          boundaries: this.boundaries,
          sizeRestrictions: this.sizeRestrictions,
          positionRestrictions: this.positionRestrictions,
          aspectRatio: this.getAspectRatio(),
          imageRestriction: this.imageRestriction
        };
      },
      defaultCoordinates: function defaultCoordinates() {
        return i({}, F);
      },
      flip: function flip(t, e) {
        var n = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
          s = n.transitions,
          o = void 0 === s || s;
        if (!this.transitionsActive) {
          o && this.enableTransitions();
          var r = i({}, this.imageTransforms.flip),
            a = At({
              flip: {
                horizontal: t ? !r.horizontal : r.horizontal,
                vertical: e ? !r.vertical : r.vertical
              },
              previousFlip: r,
              rotate: this.imageTransforms.rotate,
              visibleArea: this.visibleArea,
              coordinates: this.coordinates,
              imageSize: this.imageSize,
              positionRestrictions: this.positionRestrictions,
              sizeRestrictions: this.sizeRestrictions,
              getAreaRestrictions: this.getAreaRestrictions,
              aspectRatio: this.getAspectRatio()
            }),
            h = a.visibleArea,
            c = a.coordinates;
          t && (this.appliedImageTransforms.flip.horizontal = !this.appliedImageTransforms.flip.horizontal), e && (this.appliedImageTransforms.flip.vertical = !this.appliedImageTransforms.flip.vertical), this.visibleArea = h, this.coordinates = c, this.onChange(), o && this.debouncedDisableTransitions();
        }
      },
      rotate: function rotate(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          n = e.transitions,
          s = void 0 === n || n;
        if (!this.transitionsActive) {
          s && this.enableTransitions();
          var o = i({}, this.imageSize);
          this.appliedImageTransforms.rotate += t;
          var r = Rt({
              visibleArea: this.visibleArea,
              coordinates: this.coordinates,
              previousImageSize: o,
              imageSize: this.imageSize,
              angle: t,
              positionRestrictions: this.positionRestrictions,
              sizeRestrictions: this.sizeRestrictions,
              getAreaRestrictions: this.getAreaRestrictions,
              aspectRatio: this.getAspectRatio()
            }),
            a = r.visibleArea,
            h = r.coordinates,
            c = this.processAutoZoom("rotateImage", a, h);
          a = c.visibleArea, h = c.coordinates, this.visibleArea = a, this.coordinates = h, this.onChange(), s && this.debouncedDisableTransitions();
        }
      }
    }
  },
  ne = T({
    render: function render() {
      var t = this,
        e = t.$createElement,
        i = t._self._c || e;
      return i("div", {
        ref: "cropper",
        "class": t.classes.cropper
      }, [i("div", {
        ref: "stretcher",
        "class": t.classes.stretcher
      }), t._v(" "), i("div", {
        "class": t.classes.boundaries,
        style: t.boundariesStyle
      }, [i(t.backgroundWrapperComponent, {
        tag: "component",
        "class": t.classes.cropperWrapper,
        attrs: {
          "wheel-resize": t.settings.resizeImage.wheel,
          "touch-resize": t.settings.resizeImage.touch,
          "touch-move": t.settings.moveImage.touch,
          "mouse-move": t.settings.moveImage.mouse
        },
        on: {
          move: t.onManipulateImage,
          resize: t.onManipulateImage
        }
      }, [i("div", {
        "class": t.classes.background,
        style: t.boundariesStyle
      }), t._v(" "), i("div", {
        "class": t.classes.imageWrapper
      }, [i("img", {
        ref: "image",
        "class": t.classes.image,
        style: t.imageStyle,
        attrs: {
          crossorigin: t.imageAttributes.crossOrigin,
          src: t.imageAttributes.src
        },
        on: {
          mousedown: function mousedown(t) {
            t.preventDefault();
          }
        }
      })]), t._v(" "), i("div", {
        "class": t.classes.foreground,
        style: t.boundariesStyle
      }), t._v(" "), i(t.stencilComponent, t._b({
        directives: [{
          name: "show",
          rawName: "v-show",
          value: t.imageLoaded,
          expression: "imageLoaded"
        }],
        ref: "stencil",
        tag: "component",
        attrs: {
          image: t.image,
          coordinates: t.coordinates,
          "stencil-coordinates": t.stencilCoordinates,
          transitions: t.transitionsOptions
        },
        on: {
          resize: t.onResize,
          "resize-end": t.onResizeEnd,
          move: t.onMove,
          "move-end": t.onMoveEnd
        }
      }, "component", t.stencilProps, !1)), t._v(" "), t.canvas ? i("canvas", {
        ref: "canvas",
        style: {
          display: "none"
        }
      }) : t._e(), t._v(" "), t.canvas ? i("canvas", {
        ref: "sourceCanvas",
        style: {
          display: "none"
        }
      }) : t._e()], 1)], 1)]);
    },
    staticRenderFns: []
  }, undefined, ie, undefined, false, undefined, !1, void 0, void 0, void 0);
vue__WEBPACK_IMPORTED_MODULE_0___default().component("cropper", ne), vue__WEBPACK_IMPORTED_MODULE_0___default().component("rectangle-stencil", Qt), vue__WEBPACK_IMPORTED_MODULE_0___default().component("circle-stencil", Jt), vue__WEBPACK_IMPORTED_MODULE_0___default().component("simple-handler", Et), vue__WEBPACK_IMPORTED_MODULE_0___default().component("simple-line", Ot);


/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js":
/*!******************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js ***!
  \******************************************************************************/
/***/ ((module) => {

"use strict";


/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (cssWithMappingToString) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item);
      if (item[2]) {
        return "@media ".concat(item[2], " {").concat(content, "}");
      }
      return content;
    }).join("");
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names

  list.i = function (modules, mediaQuery, dedupe) {
    if (typeof modules === "string") {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, ""]];
    }
    var alreadyImportedModules = {};
    if (dedupe) {
      for (var i = 0; i < this.length; i++) {
        // eslint-disable-next-line prefer-destructuring
        var id = this[i][0];
        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }
    for (var _i = 0; _i < modules.length; _i++) {
      var item = [].concat(modules[_i]);
      if (dedupe && alreadyImportedModules[item[0]]) {
        // eslint-disable-next-line no-continue
        continue;
      }
      if (mediaQuery) {
        if (!item[2]) {
          item[2] = mediaQuery;
        } else {
          item[2] = "".concat(mediaQuery, " and ").concat(item[2]);
        }
      }
      list.push(item);
    }
  };
  return list;
};

/***/ }),

/***/ "./node_modules/nprogress/nprogress.js":
/*!*********************************************!*\
  !*** ./node_modules/nprogress/nprogress.js ***!
  \*********************************************/
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
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
    progress.offsetWidth; /* Repaint */

    queue(function (next) {
      // Set positionUsing if it hasn't already been set
      if (Settings.positionUsing === '') Settings.positionUsing = NProgress.getPositioningCSS();

      // Add transition
      css(bar, barPositionCSS(n, speed, ease));
      if (n === 1) {
        // Fade out
        css(progress, {
          transition: 'none',
          opacity: 1
        });
        progress.offsetWidth; /* Repaint */

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
    var bodyStyle = document.body.style;

    // Sniff prefixes
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
    if (hasClass(oldList, name)) return;

    // Trim the opening space.
    element.className = newList.substring(1);
  }

  /**
   * (Internal) Removes a class from an element.
   */

  function removeClass(element, name) {
    var oldList = classList(element),
      newList;
    if (!hasClass(element, name)) return;

    // Replace the class name.
    newList = oldList.replace(' ' + name + ' ', ' ');

    // Trim the opening and closing spaces.
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

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-51.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-51.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue_advanced_cropper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-advanced-cropper */ "./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/index.es.js");
/* harmony import */ var vue_advanced_cropper_dist_style_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-advanced-cropper/dist/style.css */ "./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/style.css");


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {
    Cropper: vue_advanced_cropper__WEBPACK_IMPORTED_MODULE_0__.Cropper
  },
  props: {
    width: {
      type: Number,
      required: true
    },
    height: {
      type: Number,
      required: true
    },
    accessToken: {
      type: String,
      required: true
    },
    lang: {
      type: Object,
      required: true
    },
    uploadUrl: {
      type: String,
      required: true
    }
  },
  data: function data() {
    return {
      isDragging: false,
      image: null,
      saveInProgress: false
    };
  },
  computed: {
    stencilSize: function stencilSize() {
      var stencilSize = {};
      stencilSize.width = this.width;
      stencilSize.height = this.height;
      return stencilSize;
    },
    canvas: function canvas() {
      var canvas = {};
      canvas.maxWidth = this.width;
      canvas.maxHeight = this.height;
      return canvas;
    }
  },
  methods: {
    zoomIn: function zoomIn() {
      this.$refs.cropper.zoom(1.2);
    },
    zoomOut: function zoomOut() {
      this.$refs.cropper.zoom(0.8);
    },
    dragover: function dragover(e) {
      e.preventDefault();
      this.isDragging = true;
    },
    dragleave: function dragleave() {
      this.isDragging = false;
    },
    drop: function drop(e) {
      e.preventDefault();
      this.$refs.file.files = e.dataTransfer.files;
      this.onChange();
      this.isDragging = false;
    },
    onChange: function onChange() {
      var _this = this;
      var files = this.$refs.file.files;
      if (files && files[0]) {
        if (this.image && this.image.src) {
          URL.revokeObjectURL(this.image.src);
        }
        var blob = URL.createObjectURL(files[0]);
        var reader = new FileReader();
        reader.onload = function (e) {
          _this.image = {
            src: blob,
            type: files[0].type
          };
        };
        reader.readAsArrayBuffer(files[0]);
      }
    },
    saveAvatar: function saveAvatar() {
      var _this2 = this;
      this.saveInProgress = true;
      var _this$$refs$cropper$g = this.$refs.cropper.getResult(),
        canvas = _this$$refs$cropper$g.canvas;
      if (canvas) {
        var form = new FormData();
        form.append('ccm_token', this.accessToken);
        canvas.toBlob(function (blob) {
          form.append('file', blob);
          // You can use axios, superagent and other libraries instead here
          fetch(_this2.uploadUrl, {
            method: 'POST',
            body: form
          }).then(function () {
            window.location.reload();
          });
        });
      }
    }
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-51.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-51.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "ccm-account-avatar-cropper"
  }, [_c("div", {
    staticClass: "card"
  }, [_c("div", {
    staticClass: "card-header"
  }, [_vm._v(_vm._s(_vm.lang.header))]), _vm._v(" "), _c("div", {
    staticClass: "card-body"
  }, [_vm.image ? [_c("cropper", {
    ref: "cropper",
    attrs: {
      "stencil-props": {
        handlers: {},
        resizable: false
      },
      "stencil-size": _vm.stencilSize,
      canvas: _vm.canvas,
      "image-restriction": "stencil",
      src: _vm.image.src
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "ccm-account-avatar-cropper-controls"
  }, [_c("a", {
    on: {
      click: _vm.zoomIn
    }
  }, [_c("svg", {
    attrs: {
      width: "24px",
      height: "24px",
      viewBox: "0 0 24 24",
      "stroke-width": "1.5",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      color: "#ffffff"
    }
  }, [_c("path", {
    attrs: {
      d: "M8 11h3m3 0h-3m0 0V8m0 3v3M17 17l4 4M3 11a8 8 0 1016 0 8 8 0 00-16 0z",
      stroke: "#ffffff",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round"
    }
  })])]), _vm._v(" "), _c("a", {
    on: {
      click: _vm.zoomOut
    }
  }, [_c("svg", {
    attrs: {
      width: "24px",
      height: "24px",
      viewBox: "0 0 24 24",
      "stroke-width": "1.5",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      color: "#ffffff"
    }
  }, [_c("path", {
    attrs: {
      d: "M17 17l4 4M3 11a8 8 0 1016 0 8 8 0 00-16 0zM8 11h6",
      stroke: "#ffffff",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round"
    }
  })])])]), _vm._v(" "), _vm.image ? _c("div", {
    staticClass: "ccm-account-avatar-cropper-save"
  }, [_c("button", {
    staticClass: "btn btn-secondary float-start",
    on: {
      click: function click($event) {
        _vm.image = null;
      }
    }
  }, [_vm._v(_vm._s(_vm.lang.reset))]), _vm._v(" "), _vm.saveInProgress ? [_c("button", {
    staticClass: "btn btn-primary float-end",
    attrs: {
      disabled: ""
    }
  }, [_c("span", {
    staticClass: "spinner-border spinner-border-sm",
    attrs: {
      role: "status",
      "aria-hidden": "true"
    }
  }), _vm._v("\n                            " + _vm._s(_vm.lang.saveInProgress) + "\n                        ")])] : _c("button", {
    staticClass: "btn btn-primary float-end",
    on: {
      click: _vm.saveAvatar
    }
  }, [_vm._v(_vm._s(_vm.lang.save))])], 2) : _vm._e()] : [_c("div", {
    "class": {
      "ccm-account-avatar-cropper-drop": true,
      "ccm-account-avatar-crop-drop-hover": _vm.isDragging
    },
    on: {
      dragover: _vm.dragover,
      dragleave: _vm.dragleave,
      drop: _vm.drop,
      click: function click($event) {
        return _vm.$refs.file.click();
      }
    }
  }, [_c("input", {
    ref: "file",
    attrs: {
      type: "file",
      accept: "image/*"
    },
    on: {
      change: _vm.onChange
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "ccm-account-avatar-cropper-focus"
  }, [_c("div", {
    staticClass: "ccm-account-avatar-cropper-icon"
  }, [_c("svg", {
    attrs: {
      width: "100%",
      height: "100%",
      "stroke-width": "1.5",
      viewBox: "0 0 24 24",
      fill: "none",
      xmlns: "http://www.w3.org/2000/svg",
      color: "#000000"
    }
  }, [_c("path", {
    attrs: {
      d: "M3 20.4V3.6a.6.6 0 01.6-.6h16.8a.6.6 0 01.6.6v16.8a.6.6 0 01-.6.6H3.6a.6.6 0 01-.6-.6z",
      stroke: "#000000",
      "stroke-width": "1.5"
    }
  }), _c("path", {
    attrs: {
      d: "M6 18h12M12 14V6m0 0l3.5 3.5M12 6L8.5 9.5",
      stroke: "#000000",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      "stroke-linejoin": "round"
    }
  })])]), _vm._v(" "), _c("div", {
    staticClass: "ccm-account-avatar-cropper-text"
  }, [_vm._v(_vm._s(_vm.lang.upload))])])]], 2)])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-38.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-38.use[2]!./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/style.css":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-38.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-38.use[2]!./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/style.css ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../../laravel-mix/node_modules/css-loader/dist/runtime/api.js */ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, ".vue-advanced-cropper {\n  text-align: center;\n  position: relative;\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  max-height: 100%;\n  max-width: 100%;\n  direction: ltr;\n}\n\n.vue-advanced-cropper__stretcher {\n  pointer-events: none;\n  position: relative;\n  max-width: 100%;\n  max-height: 100%;\n}\n\n.vue-advanced-cropper__image {\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  position: absolute;\n  transform-origin: center;\n  max-width: none !important;\n}\n\n.vue-advanced-cropper__background, .vue-advanced-cropper__foreground {\n  opacity: 1;\n  background: #000;\n  transform: translate(-50%, -50%);\n  position: absolute;\n  top: 50%;\n  left: 50%;\n}\n\n.vue-advanced-cropper__foreground {\n  opacity: 0.5;\n}\n\n.vue-advanced-cropper__boundaries {\n  opacity: 1;\n  transform: translate(-50%, -50%);\n  position: absolute;\n  left: 50%;\n  top: 50%;\n}\n\n.vue-advanced-cropper__cropper-wrapper {\n  width: 100%;\n  height: 100%;\n}\n\n.vue-advanced-cropper__image-wrapper {\n  overflow: hidden;\n  position: absolute;\n  width: 100%;\n  height: 100%;\n}\n\n.vue-advanced-cropper__stencil-wrapper {\n  position: absolute;\n}\n\n.vue-rectangle-stencil {\n  position: absolute;\n  height: 100%;\n  width: 100%;\n  box-sizing: border-box;\n}\n\n.vue-rectangle-stencil__preview {\n  position: absolute;\n  width: 100%;\n  height: 100%;\n}\n\n.vue-rectangle-stencil--movable {\n  cursor: move;\n}\n\n.vue-preview {\n  overflow: hidden;\n  box-sizing: border-box;\n  position: relative;\n}\n\n.vue-preview--fill {\n  width: 100%;\n  height: 100%;\n  position: absolute;\n}\n\n.vue-preview__wrapper {\n  position: absolute;\n  height: 100%;\n  width: 100%;\n}\n\n.vue-preview__image {\n  pointer-events: none;\n  position: absolute;\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  transform-origin: center;\n  max-width: none !important;\n}\n\n.vue-circle-stencil {\n  position: absolute;\n  height: 100%;\n  width: 100%;\n  box-sizing: content-box;\n  cursor: move;\n}\n\n.vue-circle-stencil__preview {\n  border-radius: 50%;\n  position: absolute;\n  width: 100%;\n  height: 100%;\n}\n\n.vue-circle-stencil--movable {\n  cursor: move;\n}\n\n.vue-simple-handler {\n  display: block;\n  background: #fff;\n  height: 10px;\n  width: 10px;\n}\n\n.vue-line-wrapper {\n  background: 0 0;\n  position: absolute;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n}\n\n.vue-line-wrapper--north, .vue-line-wrapper--south {\n  height: 12px;\n  width: 100%;\n  left: 0;\n  transform: translateY(-50%);\n}\n\n.vue-line-wrapper--north {\n  top: 0;\n  cursor: n-resize;\n}\n\n.vue-line-wrapper--south {\n  top: 100%;\n  cursor: s-resize;\n}\n\n.vue-line-wrapper--east, .vue-line-wrapper--west {\n  width: 12px;\n  height: 100%;\n  transform: translateX(-50%);\n  top: 0;\n}\n\n.vue-line-wrapper--east {\n  left: 100%;\n  cursor: e-resize;\n}\n\n.vue-line-wrapper--west {\n  left: 0;\n  cursor: w-resize;\n}\n\n.vue-line-wrapper--disabled {\n  cursor: auto;\n}\n\n.vue-handler-wrapper {\n  position: absolute;\n  transform: translate(-50%, -50%);\n  width: 30px;\n  height: 30px;\n}\n\n.vue-handler-wrapper__draggable {\n  width: 100%;\n  height: 100%;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n}\n\n.vue-handler-wrapper--west-north {\n  cursor: nw-resize;\n}\n\n.vue-handler-wrapper--north {\n  cursor: n-resize;\n}\n\n.vue-handler-wrapper--east-north {\n  cursor: ne-resize;\n}\n\n.vue-handler-wrapper--east {\n  cursor: e-resize;\n}\n\n.vue-handler-wrapper--east-south {\n  cursor: se-resize;\n}\n\n.vue-handler-wrapper--south {\n  cursor: s-resize;\n}\n\n.vue-handler-wrapper--west-south {\n  cursor: sw-resize;\n}\n\n.vue-handler-wrapper--west {\n  cursor: w-resize;\n}\n\n.vue-handler-wrapper--disabled {\n  cursor: auto;\n}\n\n.vue-draggable-area {\n  position: relative;\n}\n\n.vue-bounding-box {\n  position: relative;\n  height: 100%;\n  width: 100%;\n}\n\n.vue-bounding-box__handler {\n  position: absolute;\n}\n\n.vue-bounding-box__handler--west-north {\n  left: 0;\n  top: 0;\n}\n\n.vue-bounding-box__handler--north {\n  left: 50%;\n  top: 0;\n}\n\n.vue-bounding-box__handler--east-north {\n  left: 100%;\n  top: 0;\n}\n\n.vue-bounding-box__handler--east {\n  left: 100%;\n  top: 50%;\n}\n\n.vue-bounding-box__handler--east-south {\n  left: 100%;\n  top: 100%;\n}\n\n.vue-bounding-box__handler--south {\n  left: 50%;\n  top: 100%;\n}\n\n.vue-bounding-box__handler--west-south {\n  left: 0;\n  top: 100%;\n}\n\n.vue-bounding-box__handler--west {\n  left: 0;\n  top: 50%;\n}\n\n.vue-preview-result {\n  overflow: hidden;\n  box-sizing: border-box;\n  position: absolute;\n  height: 100%;\n  width: 100%;\n}\n\n.vue-preview-result__wrapper {\n  position: absolute;\n}\n\n.vue-preview-result__image {\n  pointer-events: none;\n  position: relative;\n  -webkit-user-select: none;\n  -moz-user-select: none;\n  user-select: none;\n  transform-origin: center;\n  max-width: none !important;\n}\n\n.vue-simple-line {\n  background: 0 0;\n  transition: border 0.5s;\n  border-color: rgba(255, 255, 255, 0.3);\n  border-width: 0;\n  border-style: solid;\n}\n\n.vue-simple-line--north, .vue-simple-line--south {\n  height: 0;\n  width: 100%;\n}\n\n.vue-simple-line--east, .vue-simple-line--west {\n  height: 100%;\n  width: 0;\n}\n\n.vue-simple-line--east {\n  border-right-width: 1px;\n}\n\n.vue-simple-line--west {\n  border-left-width: 1px;\n}\n\n.vue-simple-line--south {\n  border-bottom-width: 1px;\n}\n\n.vue-simple-line--north {\n  border-top-width: 1px;\n}\n\n.vue-simple-line--hover {\n  opacity: 1;\n  border-color: #fff;\n}", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/style.css":
/*!********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/style.css ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../../style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_38_use_1_postcss_loader_dist_cjs_js_clonedRuleSet_38_use_2_style_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-38.use[1]!../../../../../postcss-loader/dist/cjs.js??clonedRuleSet-38.use[2]!./style.css */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-38.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-38.use[2]!./node_modules/@concretecms/bedrock/node_modules/vue-advanced-cropper/dist/style.css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_38_use_1_postcss_loader_dist_cjs_js_clonedRuleSet_38_use_2_style_css__WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_38_use_1_postcss_loader_dist_cjs_js_clonedRuleSet_38_use_2_style_css__WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var isOldIE = function isOldIE() {
  var memo;
  return function memorize() {
    if (typeof memo === 'undefined') {
      // Test for IE <= 9 as proposed by Browserhacks
      // @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
      // Tests for existence of standard globals is to allow style-loader
      // to operate correctly into non-standard environments
      // @see https://github.com/webpack-contrib/style-loader/issues/177
      memo = Boolean(window && document && document.all && !window.atob);
    }

    return memo;
  };
}();

var getTarget = function getTarget() {
  var memo = {};
  return function memorize(target) {
    if (typeof memo[target] === 'undefined') {
      var styleTarget = document.querySelector(target); // Special case to return head of iframe instead of iframe itself

      if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
        try {
          // This will throw an exception if access to iframe is blocked
          // due to cross-origin restrictions
          styleTarget = styleTarget.contentDocument.head;
        } catch (e) {
          // istanbul ignore next
          styleTarget = null;
        }
      }

      memo[target] = styleTarget;
    }

    return memo[target];
  };
}();

var stylesInDom = [];

function getIndexByIdentifier(identifier) {
  var result = -1;

  for (var i = 0; i < stylesInDom.length; i++) {
    if (stylesInDom[i].identifier === identifier) {
      result = i;
      break;
    }
  }

  return result;
}

function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];

  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var index = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3]
    };

    if (index !== -1) {
      stylesInDom[index].references++;
      stylesInDom[index].updater(obj);
    } else {
      stylesInDom.push({
        identifier: identifier,
        updater: addStyle(obj, options),
        references: 1
      });
    }

    identifiers.push(identifier);
  }

  return identifiers;
}

function insertStyleElement(options) {
  var style = document.createElement('style');
  var attributes = options.attributes || {};

  if (typeof attributes.nonce === 'undefined') {
    var nonce =  true ? __webpack_require__.nc : 0;

    if (nonce) {
      attributes.nonce = nonce;
    }
  }

  Object.keys(attributes).forEach(function (key) {
    style.setAttribute(key, attributes[key]);
  });

  if (typeof options.insert === 'function') {
    options.insert(style);
  } else {
    var target = getTarget(options.insert || 'head');

    if (!target) {
      throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
    }

    target.appendChild(style);
  }

  return style;
}

function removeStyleElement(style) {
  // istanbul ignore if
  if (style.parentNode === null) {
    return false;
  }

  style.parentNode.removeChild(style);
}
/* istanbul ignore next  */


var replaceText = function replaceText() {
  var textStore = [];
  return function replace(index, replacement) {
    textStore[index] = replacement;
    return textStore.filter(Boolean).join('\n');
  };
}();

function applyToSingletonTag(style, index, remove, obj) {
  var css = remove ? '' : obj.media ? "@media ".concat(obj.media, " {").concat(obj.css, "}") : obj.css; // For old IE

  /* istanbul ignore if  */

  if (style.styleSheet) {
    style.styleSheet.cssText = replaceText(index, css);
  } else {
    var cssNode = document.createTextNode(css);
    var childNodes = style.childNodes;

    if (childNodes[index]) {
      style.removeChild(childNodes[index]);
    }

    if (childNodes.length) {
      style.insertBefore(cssNode, childNodes[index]);
    } else {
      style.appendChild(cssNode);
    }
  }
}

function applyToTag(style, options, obj) {
  var css = obj.css;
  var media = obj.media;
  var sourceMap = obj.sourceMap;

  if (media) {
    style.setAttribute('media', media);
  } else {
    style.removeAttribute('media');
  }

  if (sourceMap && typeof btoa !== 'undefined') {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  } // For old IE

  /* istanbul ignore if  */


  if (style.styleSheet) {
    style.styleSheet.cssText = css;
  } else {
    while (style.firstChild) {
      style.removeChild(style.firstChild);
    }

    style.appendChild(document.createTextNode(css));
  }
}

var singleton = null;
var singletonCounter = 0;

function addStyle(obj, options) {
  var style;
  var update;
  var remove;

  if (options.singleton) {
    var styleIndex = singletonCounter++;
    style = singleton || (singleton = insertStyleElement(options));
    update = applyToSingletonTag.bind(null, style, styleIndex, false);
    remove = applyToSingletonTag.bind(null, style, styleIndex, true);
  } else {
    style = insertStyleElement(options);
    update = applyToTag.bind(null, style, options);

    remove = function remove() {
      removeStyleElement(style);
    };
  }

  update(obj);
  return function updateStyle(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) {
        return;
      }

      update(obj = newObj);
    } else {
      remove();
    }
  };
}

module.exports = function (list, options) {
  options = options || {}; // Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
  // tags it will allow on a page

  if (!options.singleton && typeof options.singleton !== 'boolean') {
    options.singleton = isOldIE();
  }

  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];

    if (Object.prototype.toString.call(newList) !== '[object Array]') {
      return;
    }

    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDom[index].references--;
    }

    var newLastIdentifiers = modulesToDom(newList, options);

    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];

      var _index = getIndexByIdentifier(_identifier);

      if (stylesInDom[_index].references === 0) {
        stylesInDom[_index].updater();

        stylesInDom.splice(_index, 1);
      }
    }

    lastIdentifiers = newLastIdentifiers;
  };
};

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue ***!
  \***************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AvatarCropper.vue?vue&type=template&id=753b3f13& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&");
/* harmony import */ var _AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AvatarCropper.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.render,
  _AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_lib_index_js_clonedRuleSet_51_use_0_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../babel-loader/lib/index.js??clonedRuleSet-51.use[0]!../../../../../../../vue-loader/lib/index.js??vue-loader-options!./AvatarCropper.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-51.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_babel_loader_lib_index_js_clonedRuleSet_51_use_0_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&":
/*!**********************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13& ***!
  \**********************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _babel_loader_lib_index_js_clonedRuleSet_51_use_0_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _babel_loader_lib_index_js_clonedRuleSet_51_use_0_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _babel_loader_lib_index_js_clonedRuleSet_51_use_0_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_vue_loader_lib_index_js_vue_loader_options_AvatarCropper_vue_vue_type_template_id_753b3f13___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../babel-loader/lib/index.js??clonedRuleSet-51.use[0]!../../../../../../../vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../vue-loader/lib/index.js??vue-loader-options!./AvatarCropper.vue?vue&type=template&id=753b3f13& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-51.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/AvatarCropper.vue?vue&type=template&id=753b3f13&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ normalizeComponent)
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent(
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */,
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options =
    typeof scriptExports === 'function' ? scriptExports.options : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) {
    // server build
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
    hook = shadowMode
      ? function () {
          injectStyles.call(
            this,
            (options.functional ? this.parent : this).$root.$options.shadowRoot
          )
        }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection(h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing ? [].concat(existing, hook) : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

"use strict";
module.exports = Vue;

/***/ }),

/***/ "bootstrap":
/*!****************************!*\
  !*** external "bootstrap" ***!
  \****************************/
/***/ ((module) => {

"use strict";
module.exports = bootstrap;

/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = jQuery;

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
/******/ 			id: moduleId,
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
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
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
/******/ 	/* webpack/runtime/nonce */
/******/ 	(() => {
/******/ 		__webpack_require__.nc = undefined;
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!*******************************************!*\
  !*** ./assets/themes/concrete/js/main.js ***!
  \*******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _concretecms_bedrock_assets_bedrock_js_frontend__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @concretecms/bedrock/assets/bedrock/js/frontend */ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend.js");
/* harmony import */ var _login_tabs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./login-tabs */ "./assets/themes/concrete/js/login-tabs.js");
/* harmony import */ var _login_tabs__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_login_tabs__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _concretecms_bedrock_assets_account_js_frontend__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @concretecms/bedrock/assets/account/js/frontend */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend.js");
/* harmony import */ var _concretecms_bedrock_assets_desktop_js_frontend__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @concretecms/bedrock/assets/desktop/js/frontend */ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! nprogress */ "./node_modules/nprogress/nprogress.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(nprogress__WEBPACK_IMPORTED_MODULE_4__);


//import BackgroundImage from './background-image';

// Handle profile picture


// Handle desktop


window.NProgress = (nprogress__WEBPACK_IMPORTED_MODULE_4___default());
var tooltipTriggerList = [].slice.call(document.querySelectorAll('.launch-tooltip'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return bootstrap.Tooltip.getInstance(tooltipTriggerEl) || new bootstrap.Tooltip(tooltipTriggerEl, {
    placement: 'bottom'
  });
});
})();

/******/ })()
;