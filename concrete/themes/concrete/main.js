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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/themes/concrete/js/login-tabs.js":
/*!*************************************************!*\
  !*** ./assets/themes/concrete/js/login-tabs.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

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

/***/ "./assets/themes/concrete/js/main.js":
/*!*******************************************!*\
  !*** ./assets/themes/concrete/js/main.js ***!
  \*******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
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



window.NProgress = nprogress__WEBPACK_IMPORTED_MODULE_4___default.a;
$('.launch-tooltip').tooltip({
  placement: 'bottom'
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend.js ***!
  \*************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_components_Avatar_Cropper_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/components/Avatar/Cropper.vue */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue");
/* eslint-disable no-new */

window.Concrete.Vue.createContext('frontend', {
  AvatarCropper: _frontend_components_Avatar_Cropper_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
});

if (document.querySelectorAll('[data-view=account]').length) {
  Concrete.Vue.activateContext('frontend', function (Vue, config) {
    new Vue({
      el: '[data-view=account]',
      components: config.components
    });
  });
}

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&":
/*!***************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_Avatar_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../babel-loader/lib??ref--32-0!./Avatar.js?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_Avatar_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&":
/*!**************************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& ***!
  \**************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../style-loader!../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--28-2!../../../../../../../../sass-loader/dist/cjs.js??ref--28-3!./Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Avatar_vue_vue_type_template_id_547cd8e4_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Avatar.vue?vue&type=template&id=547cd8e4&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue?vue&type=template&id=547cd8e4&scoped=true&");
/* harmony import */ var _Avatar_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Avatar.js?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Avatar_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Avatar_vue_vue_type_template_id_547cd8e4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Avatar_vue_vue_type_template_id_547cd8e4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "547cd8e4",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue?vue&type=template&id=547cd8e4&scoped=true&":
/*!**********************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue?vue&type=template&id=547cd8e4&scoped=true& ***!
  \**********************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Avatar_vue_vue_type_template_id_547cd8e4_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../vue-loader/lib??vue-loader-options!./Avatar.vue?vue&type=template&id=547cd8e4&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue?vue&type=template&id=547cd8e4&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Avatar_vue_vue_type_template_id_547cd8e4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Avatar_vue_vue_type_template_id_547cd8e4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js&":
/*!****************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_Cropper_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../babel-loader/lib??ref--32-0!./Cropper.js?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_Cropper_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../style-loader!../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--28-2!../../../../../../../../sass-loader/dist/cjs.js??ref--28-3!./Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue":
/*!****************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Cropper_vue_vue_type_template_id_838c3fb0_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Cropper.vue?vue&type=template&id=838c3fb0&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue?vue&type=template&id=838c3fb0&scoped=true&");
/* harmony import */ var _Cropper_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Cropper.js?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Cropper_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Cropper_vue_vue_type_template_id_838c3fb0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Cropper_vue_vue_type_template_id_838c3fb0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "838c3fb0",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue?vue&type=template&id=838c3fb0&scoped=true&":
/*!***********************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue?vue&type=template&id=838c3fb0&scoped=true& ***!
  \***********************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Cropper_vue_vue_type_template_id_838c3fb0_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../vue-loader/lib??vue-loader-options!./Cropper.vue?vue&type=template&id=838c3fb0&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue?vue&type=template&id=838c3fb0&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Cropper_vue_vue_type_template_id_838c3fb0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Cropper_vue_vue_type_template_id_838c3fb0_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend.js ***!
  \*************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap */ "bootstrap");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(bootstrap__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _frontend_async_thumbnail_builder__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./frontend/async-thumbnail-builder */ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/async-thumbnail-builder.js");
/* harmony import */ var _frontend_async_thumbnail_builder__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_frontend_async_thumbnail_builder__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _frontend_locations_country_data_link__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./frontend/locations/country-data-link */ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-data-link.js");
/* harmony import */ var _frontend_locations_country_data_link__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_frontend_locations_country_data_link__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _frontend_locations_country_stateprovince_link__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./frontend/locations/country-stateprovince-link */ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-stateprovince-link.js");
/* harmony import */ var _frontend_locations_country_stateprovince_link__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_frontend_locations_country_stateprovince_link__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _assets_cms_js_vue_Manager__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../assets/cms/js/vue/Manager */ "./node_modules/@concretecms/bedrock/assets/cms/js/vue/Manager.js");




 // Let us use Vue with our theme JS


_assets_cms_js_vue_Manager__WEBPACK_IMPORTED_MODULE_5__["default"].bindToWindow(window);
window.$ = window.jQuery = jquery__WEBPACK_IMPORTED_MODULE_0___default.a;

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/async-thumbnail-builder.js":
/*!*************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/async-thumbnail-builder.js ***!
  \*************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var ConcreteThumbnailBuilder = {
  build: function build() {
    $.post(CCM_DISPATCHER_FILENAME + '/ccm/system/file/thumbnailer', function (result) {
      if (result.built === true) {
        if (result.path) {
          $('[src$="' + result.path + '"]').each(function () {
            var me = $(this);
            me.attr('src', me.attr('src'));
          });
        }

        setTimeout(ConcreteThumbnailBuilder.build, 50);
      }
    });
  }
};
ConcreteThumbnailBuilder.build();

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-data-link.js":
/*!*****************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-data-link.js ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var USE_MUTATIONOBSERVER = !!(window.MutationObserver && window.MutationObserver.prototype && window.MutationObserver.prototype.observe);

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

global.ConcreteCountryDataLink = Link;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-stateprovince-link.js":
/*!**************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/bedrock/js/frontend/locations/country-stateprovince-link.js ***!
  \**************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var USE_MUTATIONOBSERVER = !!(window.MutationObserver && window.MutationObserver.prototype && window.MutationObserver.prototype.observe);

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

global.ConcreteCountryStateprovinceLink = Link;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/js/vue/Manager.js":
/*!************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/js/vue/Manager.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Manager; });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


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

    /**
     * Returns a list of components for the current string `context`
     *
     * @param {String} context
     * @returns {{[key: String]: {}}} A list of components keyed by their handle
     */
    value: function getContext(context) {
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
      return callback(vue__WEBPACK_IMPORTED_MODULE_0___default.a, {
        components: this.getContext(context)
      });
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
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_draft_list__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/draft-list */ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/draft-list.js");
/* harmony import */ var _frontend_draft_list__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_frontend_draft_list__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _frontend_notification__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./frontend/notification */ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/notification.js");
/* harmony import */ var _frontend_notification__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_frontend_notification__WEBPACK_IMPORTED_MODULE_1__);



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/draft-list.js":
/*!************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/draft-list.js ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/* eslint-disable no-new, no-unused-vars, camelcase */

/* global ConcreteEvent */
;

(function (global, $) {
  'use strict';

  function ConcreteDraftList($element, options) {
    var my = this;
    options = $.extend({}, options);
    my.$element = $element;
    my.options = options;
    ConcreteEvent.unsubscribe('SitemapDeleteRequestComplete');
    ConcreteEvent.subscribe('SitemapDeleteRequestComplete', function (e) {
      my.hideLoader();
      $.concreteAjax({
        dataType: 'html',
        url: my.options.reloadUrl,
        method: 'get',
        success: function success(r) {
          my.$element.replaceWith(r);
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
      my.$element.find('.ccm-block-desktop-draft-list-for-me-loader').removeClass('hidden');
    },
    hideLoader: function hideLoader() {
      var my = this;
      my.$element.find('.ccm-block-desktop-draft-list-for-me-loader').addClass('hidden');
    }
  }; // jQuery Plugin

  $.fn.concreteDraftList = function (options) {
    return $.each($(this), function (i, obj) {
      new ConcreteDraftList($(this), options);
    });
  };

  global.ConcreteDraftList = ConcreteDraftList;
})(global, jQuery);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/notification.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/desktop/js/frontend/notification.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/* eslint-disable no-new, no-unused-vars, camelcase */

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
  }; // jQuery Plugin

  $.fn.concreteNotificationList = function (options) {
    return $.each($(this), function (i, obj) {
      new ConcreteNotificationList($(this), options);
    });
  };

  global.ConcreteNotificationList = ConcreteNotificationList;
})(global, jQuery);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/dropzone/dist/dropzone.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/dropzone/dist/dropzone.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(module) {

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

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return _assertThisInitialized(self);
}

function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
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
}
/*
 *
 * More info at [www.dropzonejs.com](http://www.dropzonejs.com)
 *
 * Copyright (c) 2012, Matias Meno
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
// The Emitter class provides the ability to call `.on()` on Dropzone to listen
// to events.
// It is strongly based on component's emitter class, and I removed the
// functionality because of the dependency hell with different frameworks.


var Emitter = /*#__PURE__*/function () {
  function Emitter() {
    _classCallCheck(this, Emitter);
  }

  _createClass(Emitter, [{
    key: "on",
    // Add an event listener for given event
    value: function on(event, fn) {
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

      if (callbacks) {
        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = callbacks[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var callback = _step.value;
            callback.apply(this, args);
          }
        } catch (err) {
          _didIteratorError = true;
          _iteratorError = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion && _iterator["return"] != null) {
              _iterator["return"]();
            }
          } finally {
            if (_didIteratorError) {
              throw _iteratorError;
            }
          }
        }
      }

      return this;
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

var Dropzone = /*#__PURE__*/function (_Emitter) {
  _inherits(Dropzone, _Emitter);

  _createClass(Dropzone, null, [{
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
      this.prototype.defaultOptions = {
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
         */
        timeout: 30000,

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
         * If not `null` defines how many files this Dropzone handles. If it exceeds,
         * the event `maxfilesexceeded` will be called. The dropzone element gets the
         * class `dz-max-files-reached` accordingly so you can provide visual feedback.
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
        thumbnailMethod: 'crop',

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
        resizeMethod: 'contain',

        /**
         * The base that is used to calculate the filesize. You can change this to
         * 1024 if you would rather display kibibytes, mebibytes, etc...
         * 1024 is technically incorrect, because `1024 bytes` are `1 kibibyte` not `1 kilobyte`.
         * You can change this to `1024` if you don't care about validity.
         */
        filesizeBase: 1000,

        /**
         * Can be used to limit the maximum number of files that will be handled by this Dropzone
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
          var _iteratorNormalCompletion2 = true;
          var _didIteratorError2 = false;
          var _iteratorError2 = undefined;

          try {
            for (var _iterator2 = this.element.getElementsByTagName("div")[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
              var child = _step2.value;

              if (/(^| )dz-message($| )/.test(child.className)) {
                messageElement = child;
                child.className = "dz-message"; // Removes the 'dz-default' class

                break;
              }
            }
          } catch (err) {
            _didIteratorError2 = true;
            _iteratorError2 = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion2 && _iterator2["return"] != null) {
                _iterator2["return"]();
              }
            } finally {
              if (_didIteratorError2) {
                throw _iteratorError2;
              }
            }
          }

          if (!messageElement) {
            messageElement = Dropzone.createElement("<div class=\"dz-message\"><span></span></div>");
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
            if (resizeMethod === 'crop') {
              if (srcRatio > trgRatio) {
                info.srcHeight = file.height;
                info.srcWidth = info.srcHeight * trgRatio;
              } else {
                info.srcWidth = file.width;
                info.srcHeight = info.srcWidth / trgRatio;
              }
            } else if (resizeMethod === 'contain') {
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
        previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-image\"><img data-dz-thumbnail /></div>\n  <div class=\"dz-details\">\n    <div class=\"dz-size\"><span data-dz-size></span></div>\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n  <div class=\"dz-success-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n      <title>Check</title>\n      <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n        <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" stroke-opacity=\"0.198794158\" stroke=\"#747474\" fill-opacity=\"0.816519475\" fill=\"#FFFFFF\"></path>\n      </g>\n    </svg>\n  </div>\n  <div class=\"dz-error-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n      <title>Error</title>\n      <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n        <g stroke=\"#747474\" stroke-opacity=\"0.198794158\" fill=\"#FFFFFF\" fill-opacity=\"0.816519475\">\n          <path d=\"M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\"></path>\n        </g>\n      </g>\n    </svg>\n  </div>\n</div>",
        // END OPTIONS
        // (Required by the dropzone documentation parser)

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
          var _this2 = this;

          if (this.element === this.previewsContainer) {
            this.element.classList.add("dz-started");
          }

          if (this.previewsContainer) {
            file.previewElement = Dropzone.createElement(this.options.previewTemplate.trim());
            file.previewTemplate = file.previewElement; // Backwards compatibility

            this.previewsContainer.appendChild(file.previewElement);
            var _iteratorNormalCompletion3 = true;
            var _didIteratorError3 = false;
            var _iteratorError3 = undefined;

            try {
              for (var _iterator3 = file.previewElement.querySelectorAll("[data-dz-name]")[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                var node = _step3.value;
                node.textContent = file.name;
              }
            } catch (err) {
              _didIteratorError3 = true;
              _iteratorError3 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion3 && _iterator3["return"] != null) {
                  _iterator3["return"]();
                }
              } finally {
                if (_didIteratorError3) {
                  throw _iteratorError3;
                }
              }
            }

            var _iteratorNormalCompletion4 = true;
            var _didIteratorError4 = false;
            var _iteratorError4 = undefined;

            try {
              for (var _iterator4 = file.previewElement.querySelectorAll("[data-dz-size]")[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
                node = _step4.value;
                node.innerHTML = this.filesize(file.size);
              }
            } catch (err) {
              _didIteratorError4 = true;
              _iteratorError4 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion4 && _iterator4["return"] != null) {
                  _iterator4["return"]();
                }
              } finally {
                if (_didIteratorError4) {
                  throw _iteratorError4;
                }
              }
            }

            if (this.options.addRemoveLinks) {
              file._removeLink = Dropzone.createElement("<a class=\"dz-remove\" href=\"javascript:undefined;\" data-dz-remove>".concat(this.options.dictRemoveFile, "</a>"));
              file.previewElement.appendChild(file._removeLink);
            }

            var removeFileEvent = function removeFileEvent(e) {
              e.preventDefault();
              e.stopPropagation();

              if (file.status === Dropzone.UPLOADING) {
                return Dropzone.confirm(_this2.options.dictCancelUploadConfirmation, function () {
                  return _this2.removeFile(file);
                });
              } else {
                if (_this2.options.dictRemoveFileConfirmation) {
                  return Dropzone.confirm(_this2.options.dictRemoveFileConfirmation, function () {
                    return _this2.removeFile(file);
                  });
                } else {
                  return _this2.removeFile(file);
                }
              }
            };

            var _iteratorNormalCompletion5 = true;
            var _didIteratorError5 = false;
            var _iteratorError5 = undefined;

            try {
              for (var _iterator5 = file.previewElement.querySelectorAll("[data-dz-remove]")[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
                var removeLink = _step5.value;
                removeLink.addEventListener("click", removeFileEvent);
              }
            } catch (err) {
              _didIteratorError5 = true;
              _iteratorError5 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion5 && _iterator5["return"] != null) {
                  _iterator5["return"]();
                }
              } finally {
                if (_didIteratorError5) {
                  throw _iteratorError5;
                }
              }
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
            var _iteratorNormalCompletion6 = true;
            var _didIteratorError6 = false;
            var _iteratorError6 = undefined;

            try {
              for (var _iterator6 = file.previewElement.querySelectorAll("[data-dz-thumbnail]")[Symbol.iterator](), _step6; !(_iteratorNormalCompletion6 = (_step6 = _iterator6.next()).done); _iteratorNormalCompletion6 = true) {
                var thumbnailElement = _step6.value;
                thumbnailElement.alt = file.name;
                thumbnailElement.src = dataUrl;
              }
            } catch (err) {
              _didIteratorError6 = true;
              _iteratorError6 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion6 && _iterator6["return"] != null) {
                  _iterator6["return"]();
                }
              } finally {
                if (_didIteratorError6) {
                  throw _iteratorError6;
                }
              }
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

            if (typeof message !== "String" && message.error) {
              message = message.error;
            }

            var _iteratorNormalCompletion7 = true;
            var _didIteratorError7 = false;
            var _iteratorError7 = undefined;

            try {
              for (var _iterator7 = file.previewElement.querySelectorAll("[data-dz-errormessage]")[Symbol.iterator](), _step7; !(_iteratorNormalCompletion7 = (_step7 = _iterator7.next()).done); _iteratorNormalCompletion7 = true) {
                var node = _step7.value;
                node.textContent = message;
              }
            } catch (err) {
              _didIteratorError7 = true;
              _iteratorError7 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion7 && _iterator7["return"] != null) {
                  _iterator7["return"]();
                }
              } finally {
                if (_didIteratorError7) {
                  throw _iteratorError7;
                }
              }
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
            var _iteratorNormalCompletion8 = true;
            var _didIteratorError8 = false;
            var _iteratorError8 = undefined;

            try {
              for (var _iterator8 = file.previewElement.querySelectorAll("[data-dz-uploadprogress]")[Symbol.iterator](), _step8; !(_iteratorNormalCompletion8 = (_step8 = _iterator8.next()).done); _iteratorNormalCompletion8 = true) {
                var node = _step8.value;
                node.nodeName === 'PROGRESS' ? node.value = progress : node.style.width = "".concat(progress, "%");
              }
            } catch (err) {
              _didIteratorError8 = true;
              _iteratorError8 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion8 && _iterator8["return"] != null) {
                  _iterator8["return"]();
                }
              } finally {
                if (_didIteratorError8) {
                  throw _iteratorError8;
                }
              }
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
      this.prototype._thumbnailQueue = [];
      this.prototype._processingThumbnail = false;
    } // global utility

  }, {
    key: "extend",
    value: function extend(target) {
      for (var _len2 = arguments.length, objects = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        objects[_key2 - 1] = arguments[_key2];
      }

      for (var _i = 0, _objects = objects; _i < _objects.length; _i++) {
        var object = _objects[_i];

        for (var key in object) {
          var val = object[key];
          target[key] = val;
        }
      }

      return target;
    }
  }]);

  function Dropzone(el, options) {
    var _this;

    _classCallCheck(this, Dropzone);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Dropzone).call(this));
    var fallback, left;
    _this.element = el; // For backwards compatibility since the version was in the prototype previously

    _this.version = Dropzone.version;
    _this.defaultOptions.previewTemplate = _this.defaultOptions.previewTemplate.replace(/\n*/g, "");
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
    _this.options = Dropzone.extend({}, _this.defaultOptions, elementOptions, options != null ? options : {}); // If the browser failed, just call the fallback and leave

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
      throw new Error('You cannot set both: uploadMultiple and chunking.');
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

    _this.options.method = _this.options.method.toUpperCase();

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


  _createClass(Dropzone, [{
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
      var _this3 = this; // In case it isn't set already


      if (this.element.tagName === "form") {
        this.element.setAttribute("enctype", "multipart/form-data");
      }

      if (this.element.classList.contains("dropzone") && !this.element.querySelector(".dz-message")) {
        this.element.appendChild(Dropzone.createElement("<div class=\"dz-default dz-message\"><button class=\"dz-button\" type=\"button\">".concat(this.options.dictDefaultMessage, "</button></div>")));
      }

      if (this.clickableElements.length) {
        var setupHiddenFileInput = function setupHiddenFileInput() {
          if (_this3.hiddenFileInput) {
            _this3.hiddenFileInput.parentNode.removeChild(_this3.hiddenFileInput);
          }

          _this3.hiddenFileInput = document.createElement("input");

          _this3.hiddenFileInput.setAttribute("type", "file");

          if (_this3.options.maxFiles === null || _this3.options.maxFiles > 1) {
            _this3.hiddenFileInput.setAttribute("multiple", "multiple");
          }

          _this3.hiddenFileInput.className = "dz-hidden-input";

          if (_this3.options.acceptedFiles !== null) {
            _this3.hiddenFileInput.setAttribute("accept", _this3.options.acceptedFiles);
          }

          if (_this3.options.capture !== null) {
            _this3.hiddenFileInput.setAttribute("capture", _this3.options.capture);
          } // Not setting `display="none"` because some browsers don't accept clicks
          // on elements that aren't displayed.


          _this3.hiddenFileInput.style.visibility = "hidden";
          _this3.hiddenFileInput.style.position = "absolute";
          _this3.hiddenFileInput.style.top = "0";
          _this3.hiddenFileInput.style.left = "0";
          _this3.hiddenFileInput.style.height = "0";
          _this3.hiddenFileInput.style.width = "0";
          Dropzone.getElement(_this3.options.hiddenInputContainer, 'hiddenInputContainer').appendChild(_this3.hiddenFileInput);
          return _this3.hiddenFileInput.addEventListener("change", function () {
            var files = _this3.hiddenFileInput.files;

            if (files.length) {
              var _iteratorNormalCompletion9 = true;
              var _didIteratorError9 = false;
              var _iteratorError9 = undefined;

              try {
                for (var _iterator9 = files[Symbol.iterator](), _step9; !(_iteratorNormalCompletion9 = (_step9 = _iterator9.next()).done); _iteratorNormalCompletion9 = true) {
                  var file = _step9.value;

                  _this3.addFile(file);
                }
              } catch (err) {
                _didIteratorError9 = true;
                _iteratorError9 = err;
              } finally {
                try {
                  if (!_iteratorNormalCompletion9 && _iterator9["return"] != null) {
                    _iterator9["return"]();
                  }
                } finally {
                  if (_didIteratorError9) {
                    throw _iteratorError9;
                  }
                }
              }
            }

            _this3.emit("addedfiles", files);

            return setupHiddenFileInput();
          });
        };

        setupHiddenFileInput();
      }

      this.URL = window.URL !== null ? window.URL : window.webkitURL; // Setup all event listeners on the Dropzone object itself.
      // They're not in @setupEventListeners() because they shouldn't be removed
      // again when the dropzone gets disabled.

      var _iteratorNormalCompletion10 = true;
      var _didIteratorError10 = false;
      var _iteratorError10 = undefined;

      try {
        for (var _iterator10 = this.events[Symbol.iterator](), _step10; !(_iteratorNormalCompletion10 = (_step10 = _iterator10.next()).done); _iteratorNormalCompletion10 = true) {
          var eventName = _step10.value;
          this.on(eventName, this.options[eventName]);
        }
      } catch (err) {
        _didIteratorError10 = true;
        _iteratorError10 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion10 && _iterator10["return"] != null) {
            _iterator10["return"]();
          }
        } finally {
          if (_didIteratorError10) {
            throw _iteratorError10;
          }
        }
      }

      this.on("uploadprogress", function () {
        return _this3.updateTotalUploadProgress();
      });
      this.on("removedfile", function () {
        return _this3.updateTotalUploadProgress();
      });
      this.on("canceled", function (file) {
        return _this3.emit("complete", file);
      }); // Emit a `queuecomplete` event if all files finished uploading.

      this.on("complete", function (file) {
        if (_this3.getAddedFiles().length === 0 && _this3.getUploadingFiles().length === 0 && _this3.getQueuedFiles().length === 0) {
          // This needs to be deferred so that `queuecomplete` really triggers after `complete`
          return setTimeout(function () {
            return _this3.emit("queuecomplete");
          }, 0);
        }
      });

      var containsFiles = function containsFiles(e) {
        return e.dataTransfer.types && e.dataTransfer.types.some(function (type) {
          return type == "Files";
        });
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
          "dragstart": function dragstart(e) {
            return _this3.emit("dragstart", e);
          },
          "dragenter": function dragenter(e) {
            noPropagation(e);
            return _this3.emit("dragenter", e);
          },
          "dragover": function dragover(e) {
            // Makes it possible to drag files from chrome's download bar
            // http://stackoverflow.com/questions/19526430/drag-and-drop-file-uploads-from-chrome-downloads-bar
            // Try is required to prevent bug in Internet Explorer 11 (SCRIPT65535 exception)
            var efct;

            try {
              efct = e.dataTransfer.effectAllowed;
            } catch (error) {}

            e.dataTransfer.dropEffect = 'move' === efct || 'linkMove' === efct ? 'move' : 'copy';
            noPropagation(e);
            return _this3.emit("dragover", e);
          },
          "dragleave": function dragleave(e) {
            return _this3.emit("dragleave", e);
          },
          "drop": function drop(e) {
            noPropagation(e);
            return _this3.drop(e);
          },
          "dragend": function dragend(e) {
            return _this3.emit("dragend", e);
          }
        } // This is disabled right now, because the browsers don't implement it properly.
        // "paste": (e) =>
        //   noPropagation e
        //   @paste e

      }];
      this.clickableElements.forEach(function (clickableElement) {
        return _this3.listeners.push({
          element: clickableElement,
          events: {
            "click": function click(evt) {
              // Only the actual dropzone or the message element should trigger file selection
              if (clickableElement !== _this3.element || evt.target === _this3.element || Dropzone.elementInside(evt.target, _this3.element.querySelector(".dz-message"))) {
                _this3.hiddenFileInput.click(); // Forward the click

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
        var _iteratorNormalCompletion11 = true;
        var _didIteratorError11 = false;
        var _iteratorError11 = undefined;

        try {
          for (var _iterator11 = this.getActiveFiles()[Symbol.iterator](), _step11; !(_iteratorNormalCompletion11 = (_step11 = _iterator11.next()).done); _iteratorNormalCompletion11 = true) {
            var file = _step11.value;
            totalBytesSent += file.upload.bytesSent;
            totalBytes += file.upload.total;
          }
        } catch (err) {
          _didIteratorError11 = true;
          _iteratorError11 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion11 && _iterator11["return"] != null) {
              _iterator11["return"]();
            }
          } finally {
            if (_didIteratorError11) {
              throw _iteratorError11;
            }
          }
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

      var fieldsString = "<div class=\"dz-fallback\">";

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
        var _iteratorNormalCompletion12 = true;
        var _didIteratorError12 = false;
        var _iteratorError12 = undefined;

        try {
          for (var _iterator12 = elements[Symbol.iterator](), _step12; !(_iteratorNormalCompletion12 = (_step12 = _iterator12.next()).done); _iteratorNormalCompletion12 = true) {
            var el = _step12.value;

            if (/(^| )fallback($| )/.test(el.className)) {
              return el;
            }
          }
        } catch (err) {
          _didIteratorError12 = true;
          _iteratorError12 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion12 && _iterator12["return"] != null) {
              _iterator12["return"]();
            }
          } finally {
            if (_didIteratorError12) {
              throw _iteratorError12;
            }
          }
        }
      };

      for (var _i2 = 0, _arr = ["div", "form"]; _i2 < _arr.length; _i2++) {
        var tagName = _arr[_i2];
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
      var _this4 = this;

      this.clickableElements.forEach(function (element) {
        return element.classList.remove("dz-clickable");
      });
      this.removeEventListeners();
      this.disabled = true;
      return this.files.map(function (file) {
        return _this4.cancelUpload(file);
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
        var units = ['tb', 'gb', 'mb', 'kb', 'b'];

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
          this.emit('maxfilesreached', this.files);
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
      var _iteratorNormalCompletion13 = true;
      var _didIteratorError13 = false;
      var _iteratorError13 = undefined;

      try {
        for (var _iterator13 = files[Symbol.iterator](), _step13; !(_iteratorNormalCompletion13 = (_step13 = _iterator13.next()).done); _iteratorNormalCompletion13 = true) {
          var file = _step13.value;
          this.addFile(file);
        }
      } catch (err) {
        _didIteratorError13 = true;
        _iteratorError13 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion13 && _iterator13["return"] != null) {
            _iterator13["return"]();
          }
        } finally {
          if (_didIteratorError13) {
            throw _iteratorError13;
          }
        }
      }
    } // When a folder is dropped (or files are pasted), items must be handled
    // instead of files.

  }, {
    key: "_addFilesFromItems",
    value: function _addFilesFromItems(items) {
      var _this5 = this;

      return function () {
        var result = [];
        var _iteratorNormalCompletion14 = true;
        var _didIteratorError14 = false;
        var _iteratorError14 = undefined;

        try {
          for (var _iterator14 = items[Symbol.iterator](), _step14; !(_iteratorNormalCompletion14 = (_step14 = _iterator14.next()).done); _iteratorNormalCompletion14 = true) {
            var item = _step14.value;
            var entry;

            if (item.webkitGetAsEntry != null && (entry = item.webkitGetAsEntry())) {
              if (entry.isFile) {
                result.push(_this5.addFile(item.getAsFile()));
              } else if (entry.isDirectory) {
                // Append all files from that directory to files
                result.push(_this5._addFilesFromDirectory(entry, entry.name));
              } else {
                result.push(undefined);
              }
            } else if (item.getAsFile != null) {
              if (item.kind == null || item.kind === "file") {
                result.push(_this5.addFile(item.getAsFile()));
              } else {
                result.push(undefined);
              }
            } else {
              result.push(undefined);
            }
          }
        } catch (err) {
          _didIteratorError14 = true;
          _iteratorError14 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion14 && _iterator14["return"] != null) {
              _iterator14["return"]();
            }
          } finally {
            if (_didIteratorError14) {
              throw _iteratorError14;
            }
          }
        }

        return result;
      }();
    } // Goes through the directory, and adds each file it finds recursively

  }, {
    key: "_addFilesFromDirectory",
    value: function _addFilesFromDirectory(directory, path) {
      var _this6 = this;

      var dirReader = directory.createReader();

      var errorHandler = function errorHandler(error) {
        return __guardMethod__(console, 'log', function (o) {
          return o.log(error);
        });
      };

      var readEntries = function readEntries() {
        return dirReader.readEntries(function (entries) {
          if (entries.length > 0) {
            var _iteratorNormalCompletion15 = true;
            var _didIteratorError15 = false;
            var _iteratorError15 = undefined;

            try {
              for (var _iterator15 = entries[Symbol.iterator](), _step15; !(_iteratorNormalCompletion15 = (_step15 = _iterator15.next()).done); _iteratorNormalCompletion15 = true) {
                var entry = _step15.value;

                if (entry.isFile) {
                  entry.file(function (file) {
                    if (_this6.options.ignoreHiddenFiles && file.name.substring(0, 1) === '.') {
                      return;
                    }

                    file.fullPath = "".concat(path, "/").concat(file.name);
                    return _this6.addFile(file);
                  });
                } else if (entry.isDirectory) {
                  _this6._addFilesFromDirectory(entry, "".concat(path, "/").concat(entry.name));
                }
              } // Recursively call readEntries() again, since browser only handle
              // the first 100 entries.
              // See: https://developer.mozilla.org/en-US/docs/Web/API/DirectoryReader#readEntries

            } catch (err) {
              _didIteratorError15 = true;
              _iteratorError15 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion15 && _iterator15["return"] != null) {
                  _iterator15["return"]();
                }
              } finally {
                if (_didIteratorError15) {
                  throw _iteratorError15;
                }
              }
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
      var _this7 = this;

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

          _this7._errorProcessing([file], error); // Will set the file.status

        } else {
          file.accepted = true;

          if (_this7.options.autoQueue) {
            _this7.enqueueFile(file);
          } // Will set .accepted = true

        }

        _this7._updateMaxFilesReachedClass();
      });
    } // Wrapper for enqueueFile

  }, {
    key: "enqueueFiles",
    value: function enqueueFiles(files) {
      var _iteratorNormalCompletion16 = true;
      var _didIteratorError16 = false;
      var _iteratorError16 = undefined;

      try {
        for (var _iterator16 = files[Symbol.iterator](), _step16; !(_iteratorNormalCompletion16 = (_step16 = _iterator16.next()).done); _iteratorNormalCompletion16 = true) {
          var file = _step16.value;
          this.enqueueFile(file);
        }
      } catch (err) {
        _didIteratorError16 = true;
        _iteratorError16 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion16 && _iterator16["return"] != null) {
            _iterator16["return"]();
          }
        } finally {
          if (_didIteratorError16) {
            throw _iteratorError16;
          }
        }
      }

      return null;
    }
  }, {
    key: "enqueueFile",
    value: function enqueueFile(file) {
      var _this8 = this;

      if (file.status === Dropzone.ADDED && file.accepted === true) {
        file.status = Dropzone.QUEUED;

        if (this.options.autoProcessQueue) {
          return setTimeout(function () {
            return _this8.processQueue();
          }, 0); // Deferring the call
        }
      } else {
        throw new Error("This file can't be queued because it has already been processed or was rejected.");
      }
    }
  }, {
    key: "_enqueueThumbnail",
    value: function _enqueueThumbnail(file) {
      var _this9 = this;

      if (this.options.createImageThumbnails && file.type.match(/image.*/) && file.size <= this.options.maxThumbnailFilesize * 1024 * 1024) {
        this._thumbnailQueue.push(file);

        return setTimeout(function () {
          return _this9._processThumbnailQueue();
        }, 0); // Deferring the call
      }
    }
  }, {
    key: "_processThumbnailQueue",
    value: function _processThumbnailQueue() {
      var _this10 = this;

      if (this._processingThumbnail || this._thumbnailQueue.length === 0) {
        return;
      }

      this._processingThumbnail = true;

      var file = this._thumbnailQueue.shift();

      return this.createThumbnail(file, this.options.thumbnailWidth, this.options.thumbnailHeight, this.options.thumbnailMethod, true, function (dataUrl) {
        _this10.emit("thumbnail", file, dataUrl);

        _this10._processingThumbnail = false;
        return _this10._processThumbnailQueue();
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

      var _iteratorNormalCompletion17 = true;
      var _didIteratorError17 = false;
      var _iteratorError17 = undefined;

      try {
        for (var _iterator17 = this.files.slice()[Symbol.iterator](), _step17; !(_iteratorNormalCompletion17 = (_step17 = _iterator17.next()).done); _iteratorNormalCompletion17 = true) {
          var file = _step17.value;

          if (file.status !== Dropzone.UPLOADING || cancelIfNecessary) {
            this.removeFile(file);
          }
        }
      } catch (err) {
        _didIteratorError17 = true;
        _iteratorError17 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion17 && _iterator17["return"] != null) {
            _iterator17["return"]();
          }
        } finally {
          if (_didIteratorError17) {
            throw _iteratorError17;
          }
        }
      }

      return null;
    } // Resizes an image before it gets sent to the server. This function is the default behavior of
    // `options.transformFile` if `resizeWidth` or `resizeHeight` are set. The callback is invoked with
    // the resized blob.

  }, {
    key: "resizeImage",
    value: function resizeImage(file, width, height, resizeMethod, callback) {
      var _this11 = this;

      return this.createThumbnail(file, width, height, resizeMethod, true, function (dataUrl, canvas) {
        if (canvas == null) {
          // The image has not been resized
          return callback(file);
        } else {
          var resizeMimeType = _this11.options.resizeMimeType;

          if (resizeMimeType == null) {
            resizeMimeType = file.type;
          }

          var resizedDataURL = canvas.toDataURL(resizeMimeType, _this11.options.resizeQuality);

          if (resizeMimeType === 'image/jpeg' || resizeMimeType === 'image/jpg') {
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
      var _this12 = this;

      var fileReader = new FileReader();

      fileReader.onload = function () {
        file.dataURL = fileReader.result; // Don't bother creating a thumbnail for SVG images since they're vector

        if (file.type === "image/svg+xml") {
          if (callback != null) {
            callback(fileReader.result);
          }

          return;
        }

        _this12.createThumbnailFromUrl(file, width, height, resizeMethod, fixOrientation, callback);
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
      var _this13 = this;

      var resizeThumbnail = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
      this.emit("addedfile", mockFile);
      this.emit("complete", mockFile);

      if (!resizeThumbnail) {
        this.emit("thumbnail", mockFile, imageUrl);
        if (callback) callback();
      } else {
        var onDone = function onDone(thumbnail) {
          _this13.emit('thumbnail', mockFile, thumbnail);

          if (callback) callback();
        };

        mockFile.dataURL = imageUrl;
        this.createThumbnailFromUrl(mockFile, this.options.thumbnailWidth, this.options.thumbnailHeight, this.options.resizeMethod, this.options.fixOrientation, onDone, crossOrigin);
      }
    }
  }, {
    key: "createThumbnailFromUrl",
    value: function createThumbnailFromUrl(file, width, height, resizeMethod, fixOrientation, callback, crossOrigin) {
      var _this14 = this; // Not using `new Image` here because of a bug in latest Chrome versions.
      // See https://github.com/enyo/dropzone/pull/226


      var img = document.createElement("img");

      if (crossOrigin) {
        img.crossOrigin = crossOrigin;
      }

      img.onload = function () {
        var loadExif = function loadExif(callback) {
          return callback(1);
        };

        if (typeof EXIF !== 'undefined' && EXIF !== null && fixOrientation) {
          loadExif = function loadExif(callback) {
            return EXIF.getData(img, function () {
              return callback(EXIF.getTag(this, 'Orientation'));
            });
          };
        }

        return loadExif(function (orientation) {
          file.width = img.width;
          file.height = img.height;

          var resizeInfo = _this14.options.resize.call(_this14, file, width, height, resizeMethod);

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
      var _iteratorNormalCompletion18 = true;
      var _didIteratorError18 = false;
      var _iteratorError18 = undefined;

      try {
        for (var _iterator18 = files[Symbol.iterator](), _step18; !(_iteratorNormalCompletion18 = (_step18 = _iterator18.next()).done); _iteratorNormalCompletion18 = true) {
          var file = _step18.value;
          file.processing = true; // Backwards compatibility

          file.status = Dropzone.UPLOADING;
          this.emit("processing", file);
        }
      } catch (err) {
        _didIteratorError18 = true;
        _iteratorError18 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion18 && _iterator18["return"] != null) {
            _iterator18["return"]();
          }
        } finally {
          if (_didIteratorError18) {
            throw _iteratorError18;
          }
        }
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

        var _iteratorNormalCompletion19 = true;
        var _didIteratorError19 = false;
        var _iteratorError19 = undefined;

        try {
          for (var _iterator19 = groupedFiles[Symbol.iterator](), _step19; !(_iteratorNormalCompletion19 = (_step19 = _iterator19.next()).done); _iteratorNormalCompletion19 = true) {
            var groupedFile = _step19.value;
            groupedFile.status = Dropzone.CANCELED;
          }
        } catch (err) {
          _didIteratorError19 = true;
          _iteratorError19 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion19 && _iterator19["return"] != null) {
              _iterator19["return"]();
            }
          } finally {
            if (_didIteratorError19) {
              throw _iteratorError19;
            }
          }
        }

        if (typeof file.xhr !== 'undefined') {
          file.xhr.abort();
        }

        var _iteratorNormalCompletion20 = true;
        var _didIteratorError20 = false;
        var _iteratorError20 = undefined;

        try {
          for (var _iterator20 = groupedFiles[Symbol.iterator](), _step20; !(_iteratorNormalCompletion20 = (_step20 = _iterator20.next()).done); _iteratorNormalCompletion20 = true) {
            var _groupedFile = _step20.value;
            this.emit("canceled", _groupedFile);
          }
        } catch (err) {
          _didIteratorError20 = true;
          _iteratorError20 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion20 && _iterator20["return"] != null) {
              _iterator20["return"]();
            }
          } finally {
            if (_didIteratorError20) {
              throw _iteratorError20;
            }
          }
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
      if (typeof option === 'function') {
        for (var _len3 = arguments.length, args = new Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
          args[_key3 - 1] = arguments[_key3];
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
      var _this15 = this;

      this._transformFiles(files, function (transformedFiles) {
        if (_this15.options.chunking) {
          // Chunking is not allowed to be used with `uploadMultiple` so we know
          // that there is only __one__file.
          var transformedFile = transformedFiles[0];
          files[0].upload.chunked = _this15.options.chunking && (_this15.options.forceChunking || transformedFile.size > _this15.options.chunkSize);
          files[0].upload.totalChunkCount = Math.ceil(transformedFile.size / _this15.options.chunkSize);
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
            var start = chunkIndex * _this15.options.chunkSize;
            var end = Math.min(start + _this15.options.chunkSize, file.size);
            var dataBlock = {
              name: _this15._getParamName(0),
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

            _this15._uploadData(files, [dataBlock]);
          };

          file.upload.finishedChunkUpload = function (chunk) {
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
              _this15.options.chunksUploaded(file, function () {
                _this15._finished(files, '', null);
              });
            }
          };

          if (_this15.options.parallelChunkUploads) {
            for (var i = 0; i < file.upload.totalChunkCount; i++) {
              handleNextChunk();
            }
          } else {
            handleNextChunk();
          }
        } else {
          var dataBlocks = [];

          for (var _i3 = 0; _i3 < files.length; _i3++) {
            dataBlocks[_i3] = {
              name: _this15._getParamName(_i3),
              data: transformedFiles[_i3],
              filename: files[_i3].upload.filename
            };
          }

          _this15._uploadData(files, dataBlocks);
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
      var _this16 = this;

      var xhr = new XMLHttpRequest(); // Put the xhr object in the file objects to be able to reference it later.

      var _iteratorNormalCompletion21 = true;
      var _didIteratorError21 = false;
      var _iteratorError21 = undefined;

      try {
        for (var _iterator21 = files[Symbol.iterator](), _step21; !(_iteratorNormalCompletion21 = (_step21 = _iterator21.next()).done); _iteratorNormalCompletion21 = true) {
          var file = _step21.value;
          file.xhr = xhr;
        }
      } catch (err) {
        _didIteratorError21 = true;
        _iteratorError21 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion21 && _iterator21["return"] != null) {
            _iterator21["return"]();
          }
        } finally {
          if (_didIteratorError21) {
            throw _iteratorError21;
          }
        }
      }

      if (files[0].upload.chunked) {
        // Put the xhr object in the right chunk object, so it can be associated later, and found with _getChunk
        files[0].upload.chunks[dataBlocks[0].chunkIndex].xhr = xhr;
      }

      var method = this.resolveOption(this.options.method, files);
      var url = this.resolveOption(this.options.url, files);
      xhr.open(method, url, true); // Setting the timeout after open because of IE11 issue: https://gitlab.com/meno/dropzone/issues/8

      xhr.timeout = this.resolveOption(this.options.timeout, files); // Has to be after `.open()`. See https://github.com/enyo/dropzone/issues/179

      xhr.withCredentials = !!this.options.withCredentials;

      xhr.onload = function (e) {
        _this16._finishedUploading(files, xhr, e);
      };

      xhr.ontimeout = function () {
        _this16._handleUploadError(files, xhr, "Request timedout after ".concat(_this16.options.timeout, " seconds"));
      };

      xhr.onerror = function () {
        _this16._handleUploadError(files, xhr);
      }; // Some browsers do not have the .upload property


      var progressObj = xhr.upload != null ? xhr.upload : xhr;

      progressObj.onprogress = function (e) {
        return _this16._updateFilesUploadProgress(files, xhr, e);
      };

      var headers = {
        "Accept": "application/json",
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

        if (typeof additionalParams === 'function') {
          additionalParams = additionalParams.call(this, files, xhr, files[0].upload.chunked ? this._getChunk(files[0], xhr) : null);
        }

        for (var key in additionalParams) {
          var value = additionalParams[key];
          formData.append(key, value);
        }
      } // Let the user add additional data if necessary


      var _iteratorNormalCompletion22 = true;
      var _didIteratorError22 = false;
      var _iteratorError22 = undefined;

      try {
        for (var _iterator22 = files[Symbol.iterator](), _step22; !(_iteratorNormalCompletion22 = (_step22 = _iterator22.next()).done); _iteratorNormalCompletion22 = true) {
          var _file = _step22.value;
          this.emit("sending", _file, xhr, formData);
        }
      } catch (err) {
        _didIteratorError22 = true;
        _iteratorError22 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion22 && _iterator22["return"] != null) {
            _iterator22["return"]();
          }
        } finally {
          if (_didIteratorError22) {
            throw _iteratorError22;
          }
        }
      }

      if (this.options.uploadMultiple) {
        this.emit("sendingmultiple", files, xhr, formData);
      }

      this._addFormElementData(formData); // Finally add the files
      // Has to be last because some servers (eg: S3) expect the file to be the last parameter


      for (var i = 0; i < dataBlocks.length; i++) {
        var dataBlock = dataBlocks[i];
        formData.append(dataBlock.name, dataBlock.data, dataBlock.filename);
      }

      this.submitRequest(xhr, formData, files);
    } // Transforms all files with this.options.transformFile and invokes done with the transformed files when done.

  }, {
    key: "_transformFiles",
    value: function _transformFiles(files, done) {
      var _this17 = this;

      var transformedFiles = []; // Clumsy way of handling asynchronous calls, until I get to add a proper Future library.

      var doneCounter = 0;

      var _loop = function _loop(i) {
        _this17.options.transformFile.call(_this17, files[i], function (transformedFile) {
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
        var _iteratorNormalCompletion23 = true;
        var _didIteratorError23 = false;
        var _iteratorError23 = undefined;

        try {
          for (var _iterator23 = this.element.querySelectorAll("input, textarea, select, button")[Symbol.iterator](), _step23; !(_iteratorNormalCompletion23 = (_step23 = _iterator23.next()).done); _iteratorNormalCompletion23 = true) {
            var input = _step23.value;
            var inputName = input.getAttribute("name");
            var inputType = input.getAttribute("type");
            if (inputType) inputType = inputType.toLowerCase(); // If the input doesn't have a name, we can't use it.

            if (typeof inputName === 'undefined' || inputName === null) continue;

            if (input.tagName === "SELECT" && input.hasAttribute("multiple")) {
              // Possibly multiple values
              var _iteratorNormalCompletion24 = true;
              var _didIteratorError24 = false;
              var _iteratorError24 = undefined;

              try {
                for (var _iterator24 = input.options[Symbol.iterator](), _step24; !(_iteratorNormalCompletion24 = (_step24 = _iterator24.next()).done); _iteratorNormalCompletion24 = true) {
                  var option = _step24.value;

                  if (option.selected) {
                    formData.append(inputName, option.value);
                  }
                }
              } catch (err) {
                _didIteratorError24 = true;
                _iteratorError24 = err;
              } finally {
                try {
                  if (!_iteratorNormalCompletion24 && _iterator24["return"] != null) {
                    _iterator24["return"]();
                  }
                } finally {
                  if (_didIteratorError24) {
                    throw _iteratorError24;
                  }
                }
              }
            } else if (!inputType || inputType !== "checkbox" && inputType !== "radio" || input.checked) {
              formData.append(inputName, input.value);
            }
          }
        } catch (err) {
          _didIteratorError23 = true;
          _iteratorError23 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion23 && _iterator23["return"] != null) {
              _iterator23["return"]();
            }
          } finally {
            if (_didIteratorError23) {
              throw _iteratorError23;
            }
          }
        }
      }
    } // Invoked when there is new progress information about given files.
    // If e is not provided, it is assumed that the upload is finished.

  }, {
    key: "_updateFilesUploadProgress",
    value: function _updateFilesUploadProgress(files, xhr, e) {
      var progress;

      if (typeof e !== 'undefined') {
        progress = 100 * e.loaded / e.total;

        if (files[0].upload.chunked) {
          var file = files[0]; // Since this is a chunked upload, we need to update the appropriate chunk progress.

          var chunk = this._getChunk(file, xhr);

          chunk.progress = progress;
          chunk.total = e.total;
          chunk.bytesSent = e.loaded;
          var fileProgress = 0,
              fileTotal,
              fileBytesSent;
          file.upload.progress = 0;
          file.upload.total = 0;
          file.upload.bytesSent = 0;

          for (var i = 0; i < file.upload.totalChunkCount; i++) {
            if (file.upload.chunks[i] !== undefined && file.upload.chunks[i].progress !== undefined) {
              file.upload.progress += file.upload.chunks[i].progress;
              file.upload.total += file.upload.chunks[i].total;
              file.upload.bytesSent += file.upload.chunks[i].bytesSent;
            }
          }

          file.upload.progress = file.upload.progress / file.upload.totalChunkCount;
        } else {
          var _iteratorNormalCompletion25 = true;
          var _didIteratorError25 = false;
          var _iteratorError25 = undefined;

          try {
            for (var _iterator25 = files[Symbol.iterator](), _step25; !(_iteratorNormalCompletion25 = (_step25 = _iterator25.next()).done); _iteratorNormalCompletion25 = true) {
              var _file2 = _step25.value;
              _file2.upload.progress = progress;
              _file2.upload.total = e.total;
              _file2.upload.bytesSent = e.loaded;
            }
          } catch (err) {
            _didIteratorError25 = true;
            _iteratorError25 = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion25 && _iterator25["return"] != null) {
                _iterator25["return"]();
              }
            } finally {
              if (_didIteratorError25) {
                throw _iteratorError25;
              }
            }
          }
        }

        var _iteratorNormalCompletion26 = true;
        var _didIteratorError26 = false;
        var _iteratorError26 = undefined;

        try {
          for (var _iterator26 = files[Symbol.iterator](), _step26; !(_iteratorNormalCompletion26 = (_step26 = _iterator26.next()).done); _iteratorNormalCompletion26 = true) {
            var _file3 = _step26.value;
            this.emit("uploadprogress", _file3, _file3.upload.progress, _file3.upload.bytesSent);
          }
        } catch (err) {
          _didIteratorError26 = true;
          _iteratorError26 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion26 && _iterator26["return"] != null) {
              _iterator26["return"]();
            }
          } finally {
            if (_didIteratorError26) {
              throw _iteratorError26;
            }
          }
        }
      } else {
        // Called when the file finished uploading
        var allFilesFinished = true;
        progress = 100;
        var _iteratorNormalCompletion27 = true;
        var _didIteratorError27 = false;
        var _iteratorError27 = undefined;

        try {
          for (var _iterator27 = files[Symbol.iterator](), _step27; !(_iteratorNormalCompletion27 = (_step27 = _iterator27.next()).done); _iteratorNormalCompletion27 = true) {
            var _file4 = _step27.value;

            if (_file4.upload.progress !== 100 || _file4.upload.bytesSent !== _file4.upload.total) {
              allFilesFinished = false;
            }

            _file4.upload.progress = progress;
            _file4.upload.bytesSent = _file4.upload.total;
          } // Nothing to do, all files already at 100%

        } catch (err) {
          _didIteratorError27 = true;
          _iteratorError27 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion27 && _iterator27["return"] != null) {
              _iterator27["return"]();
            }
          } finally {
            if (_didIteratorError27) {
              throw _iteratorError27;
            }
          }
        }

        if (allFilesFinished) {
          return;
        }

        var _iteratorNormalCompletion28 = true;
        var _didIteratorError28 = false;
        var _iteratorError28 = undefined;

        try {
          for (var _iterator28 = files[Symbol.iterator](), _step28; !(_iteratorNormalCompletion28 = (_step28 = _iterator28.next()).done); _iteratorNormalCompletion28 = true) {
            var _file5 = _step28.value;
            this.emit("uploadprogress", _file5, progress, _file5.upload.bytesSent);
          }
        } catch (err) {
          _didIteratorError28 = true;
          _iteratorError28 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion28 && _iterator28["return"] != null) {
              _iterator28["return"]();
            }
          } finally {
            if (_didIteratorError28) {
              throw _iteratorError28;
            }
          }
        }
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

      if (xhr.responseType !== 'arraybuffer' && xhr.responseType !== 'blob') {
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

      this._updateFilesUploadProgress(files);

      if (!(200 <= xhr.status && xhr.status < 300)) {
        this._handleUploadError(files, xhr, response);
      } else {
        if (files[0].upload.chunked) {
          files[0].upload.finishedChunkUpload(this._getChunk(files[0], xhr));
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
          console.warn('Retried this chunk too often. Giving up.');
        }
      }

      this._errorProcessing(files, response || this.options.dictResponseError.replace("{{statusCode}}", xhr.status), xhr);
    }
  }, {
    key: "submitRequest",
    value: function submitRequest(xhr, formData, files) {
      xhr.send(formData);
    } // Called internally when processing is finished.
    // Individual callbacks have to be called in the appropriate sections.

  }, {
    key: "_finished",
    value: function _finished(files, responseText, e) {
      var _iteratorNormalCompletion29 = true;
      var _didIteratorError29 = false;
      var _iteratorError29 = undefined;

      try {
        for (var _iterator29 = files[Symbol.iterator](), _step29; !(_iteratorNormalCompletion29 = (_step29 = _iterator29.next()).done); _iteratorNormalCompletion29 = true) {
          var file = _step29.value;
          file.status = Dropzone.SUCCESS;
          this.emit("success", file, responseText, e);
          this.emit("complete", file);
        }
      } catch (err) {
        _didIteratorError29 = true;
        _iteratorError29 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion29 && _iterator29["return"] != null) {
            _iterator29["return"]();
          }
        } finally {
          if (_didIteratorError29) {
            throw _iteratorError29;
          }
        }
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
      var _iteratorNormalCompletion30 = true;
      var _didIteratorError30 = false;
      var _iteratorError30 = undefined;

      try {
        for (var _iterator30 = files[Symbol.iterator](), _step30; !(_iteratorNormalCompletion30 = (_step30 = _iterator30.next()).done); _iteratorNormalCompletion30 = true) {
          var file = _step30.value;
          file.status = Dropzone.ERROR;
          this.emit("error", file, message, xhr);
          this.emit("complete", file);
        }
      } catch (err) {
        _didIteratorError30 = true;
        _iteratorError30 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion30 && _iterator30["return"] != null) {
            _iterator30["return"]();
          }
        } finally {
          if (_didIteratorError30) {
            throw _iteratorError30;
          }
        }
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
    key: "uuidv4",
    value: function uuidv4() {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0,
            v = c === 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
      });
    }
  }]);

  return Dropzone;
}(Emitter);

Dropzone.initClass();
Dropzone.version = "5.7.0"; // This is a map of options for your different dropzones. Add configurations
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

Dropzone.options = {}; // Returns the options for an element or undefined if none available.

Dropzone.optionsForElement = function (element) {
  // Get the `Dropzone.options.elementId` for this element if it exists
  if (element.getAttribute("id")) {
    return Dropzone.options[camelize(element.getAttribute("id"))];
  } else {
    return undefined;
  }
}; // Holds a list of all dropzone instances


Dropzone.instances = []; // Returns the dropzone for given element if any

Dropzone.forElement = function (element) {
  if (typeof element === "string") {
    element = document.querySelector(element);
  }

  if ((element != null ? element.dropzone : undefined) == null) {
    throw new Error("No Dropzone found for given element. This is probably because you're trying to access it before Dropzone had the time to initialize. Use the `init` option to setup any additional observers on your Dropzone.");
  }

  return element.dropzone;
}; // Set to false if you don't want Dropzone to automatically find and attach to .dropzone elements.


Dropzone.autoDiscover = true; // Looks for all .dropzone elements and creates a dropzone for them

Dropzone.discover = function () {
  var dropzones;

  if (document.querySelectorAll) {
    dropzones = document.querySelectorAll(".dropzone");
  } else {
    dropzones = []; // IE :(

    var checkElements = function checkElements(elements) {
      return function () {
        var result = [];
        var _iteratorNormalCompletion31 = true;
        var _didIteratorError31 = false;
        var _iteratorError31 = undefined;

        try {
          for (var _iterator31 = elements[Symbol.iterator](), _step31; !(_iteratorNormalCompletion31 = (_step31 = _iterator31.next()).done); _iteratorNormalCompletion31 = true) {
            var el = _step31.value;

            if (/(^| )dropzone($| )/.test(el.className)) {
              result.push(dropzones.push(el));
            } else {
              result.push(undefined);
            }
          }
        } catch (err) {
          _didIteratorError31 = true;
          _iteratorError31 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion31 && _iterator31["return"] != null) {
              _iterator31["return"]();
            }
          } finally {
            if (_didIteratorError31) {
              throw _iteratorError31;
            }
          }
        }

        return result;
      }();
    };

    checkElements(document.getElementsByTagName("div"));
    checkElements(document.getElementsByTagName("form"));
  }

  return function () {
    var result = [];
    var _iteratorNormalCompletion32 = true;
    var _didIteratorError32 = false;
    var _iteratorError32 = undefined;

    try {
      for (var _iterator32 = dropzones[Symbol.iterator](), _step32; !(_iteratorNormalCompletion32 = (_step32 = _iterator32.next()).done); _iteratorNormalCompletion32 = true) {
        var dropzone = _step32.value; // Create a dropzone unless auto discover has been disabled for specific element

        if (Dropzone.optionsForElement(dropzone) !== false) {
          result.push(new Dropzone(dropzone));
        } else {
          result.push(undefined);
        }
      }
    } catch (err) {
      _didIteratorError32 = true;
      _iteratorError32 = err;
    } finally {
      try {
        if (!_iteratorNormalCompletion32 && _iterator32["return"] != null) {
          _iterator32["return"]();
        }
      } finally {
        if (_didIteratorError32) {
          throw _iteratorError32;
        }
      }
    }

    return result;
  }();
}; // Since the whole Drag'n'Drop API is pretty new, some browsers implement it,
// but not correctly.
// So I created a blacklist of userAgents. Yes, yes. Browser sniffing, I know.
// But what to do when browsers *theoretically* support an API, but crash
// when using it.
//
// This is a list of regular expressions tested against navigator.userAgent
//
// ** It should only be used on browser that *do* support the API, but
// incorrectly **
//


Dropzone.blacklistedBrowsers = [// The mac os and windows phone version of opera 12 seems to have a problem with the File drag'n'drop API.
/opera.*(Macintosh|Windows Phone).*version\/12/i]; // Checks if the browser is supported

Dropzone.isBrowserSupported = function () {
  var capableBrowser = true;

  if (window.File && window.FileReader && window.FileList && window.Blob && window.FormData && document.querySelector) {
    if (!("classList" in document.createElement("a"))) {
      capableBrowser = false;
    } else {
      // The browser supports the API, but may be blacklisted.
      var _iteratorNormalCompletion33 = true;
      var _didIteratorError33 = false;
      var _iteratorError33 = undefined;

      try {
        for (var _iterator33 = Dropzone.blacklistedBrowsers[Symbol.iterator](), _step33; !(_iteratorNormalCompletion33 = (_step33 = _iterator33.next()).done); _iteratorNormalCompletion33 = true) {
          var regex = _step33.value;

          if (regex.test(navigator.userAgent)) {
            capableBrowser = false;
            continue;
          }
        }
      } catch (err) {
        _didIteratorError33 = true;
        _iteratorError33 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion33 && _iterator33["return"] != null) {
            _iterator33["return"]();
          }
        } finally {
          if (_didIteratorError33) {
            throw _iteratorError33;
          }
        }
      }
    }
  } else {
    capableBrowser = false;
  }

  return capableBrowser;
};

Dropzone.dataURItoBlob = function (dataURI) {
  // convert base64 to raw binary data held in a string
  // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
  var byteString = atob(dataURI.split(',')[1]); // separate out the mime component

  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]; // write the bytes of the string to an ArrayBuffer

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


Dropzone.createElement = function (string) {
  var div = document.createElement("div");
  div.innerHTML = string;
  return div.childNodes[0];
}; // Tests if given element is inside (or simply is) the container


Dropzone.elementInside = function (element, container) {
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

Dropzone.getElement = function (el, name) {
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

Dropzone.getElements = function (els, name) {
  var el, elements;

  if (els instanceof Array) {
    elements = [];

    try {
      var _iteratorNormalCompletion34 = true;
      var _didIteratorError34 = false;
      var _iteratorError34 = undefined;

      try {
        for (var _iterator34 = els[Symbol.iterator](), _step34; !(_iteratorNormalCompletion34 = (_step34 = _iterator34.next()).done); _iteratorNormalCompletion34 = true) {
          el = _step34.value;
          elements.push(this.getElement(el, name));
        }
      } catch (err) {
        _didIteratorError34 = true;
        _iteratorError34 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion34 && _iterator34["return"] != null) {
            _iterator34["return"]();
          }
        } finally {
          if (_didIteratorError34) {
            throw _iteratorError34;
          }
        }
      }
    } catch (e) {
      elements = null;
    }
  } else if (typeof els === "string") {
    elements = [];
    var _iteratorNormalCompletion35 = true;
    var _didIteratorError35 = false;
    var _iteratorError35 = undefined;

    try {
      for (var _iterator35 = document.querySelectorAll(els)[Symbol.iterator](), _step35; !(_iteratorNormalCompletion35 = (_step35 = _iterator35.next()).done); _iteratorNormalCompletion35 = true) {
        el = _step35.value;
        elements.push(el);
      }
    } catch (err) {
      _didIteratorError35 = true;
      _iteratorError35 = err;
    } finally {
      try {
        if (!_iteratorNormalCompletion35 && _iterator35["return"] != null) {
          _iterator35["return"]();
        }
      } finally {
        if (_didIteratorError35) {
          throw _iteratorError35;
        }
      }
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


Dropzone.confirm = function (question, accepted, rejected) {
  if (window.confirm(question)) {
    return accepted();
  } else if (rejected != null) {
    return rejected();
  }
}; // Validates the mime type like this:
//
// https://developer.mozilla.org/en-US/docs/HTML/Element/input#attr-accept


Dropzone.isValidFile = function (file, acceptedFiles) {
  if (!acceptedFiles) {
    return true;
  } // If there are no accepted mime types, it's OK


  acceptedFiles = acceptedFiles.split(",");
  var mimeType = file.type;
  var baseMimeType = mimeType.replace(/\/.*$/, "");
  var _iteratorNormalCompletion36 = true;
  var _didIteratorError36 = false;
  var _iteratorError36 = undefined;

  try {
    for (var _iterator36 = acceptedFiles[Symbol.iterator](), _step36; !(_iteratorNormalCompletion36 = (_step36 = _iterator36.next()).done); _iteratorNormalCompletion36 = true) {
      var validType = _step36.value;
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
    _didIteratorError36 = true;
    _iteratorError36 = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion36 && _iterator36["return"] != null) {
        _iterator36["return"]();
      }
    } finally {
      if (_didIteratorError36) {
        throw _iteratorError36;
      }
    }
  }

  return false;
}; // Augment jQuery


if (typeof jQuery !== 'undefined' && jQuery !== null) {
  jQuery.fn.dropzone = function (options) {
    return this.each(function () {
      return new Dropzone(this, options);
    });
  };
}

if ( true && module !== null) {
  module.exports = Dropzone;
} else {
  window.Dropzone = Dropzone;
} // Dropzone file status codes


Dropzone.ADDED = "added";
Dropzone.QUEUED = "queued"; // For backwards compatibility. Now, if a file is accepted, it's either queued
// or uploading.

Dropzone.ACCEPTED = Dropzone.QUEUED;
Dropzone.UPLOADING = "uploading";
Dropzone.PROCESSING = Dropzone.UPLOADING; // alias

Dropzone.CANCELED = "canceled";
Dropzone.ERROR = "error";
Dropzone.SUCCESS = "success";
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
    _classCallCheck(this, ExifRestore);
  }

  _createClass(ExifRestore, null, [{
    key: "initClass",
    value: function initClass() {
      this.KEY_STR = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    }
  }, {
    key: "encode64",
    value: function encode64(input) {
      var output = '';
      var chr1 = undefined;
      var chr2 = undefined;
      var chr3 = '';
      var enc1 = undefined;
      var enc2 = undefined;
      var enc3 = undefined;
      var enc4 = '';
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
        chr1 = chr2 = chr3 = '';
        enc1 = enc2 = enc3 = enc4 = '';

        if (!(i < input.length)) {
          break;
        }
      }

      return output;
    }
  }, {
    key: "restore",
    value: function restore(origFileBase64, resizedFileBase64) {
      if (!origFileBase64.match('data:image/jpeg;base64,')) {
        return resizedFileBase64;
      }

      var rawImage = this.decode64(origFileBase64.replace('data:image/jpeg;base64,', ''));
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
      var imageData = resizedFileBase64.replace('data:image/jpeg;base64,', '');
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
      var output = '';
      var chr1 = undefined;
      var chr2 = undefined;
      var chr3 = '';
      var enc1 = undefined;
      var enc2 = undefined;
      var enc3 = undefined;
      var enc4 = '';
      var i = 0;
      var buf = []; // remove all characters that are not A-Z, a-z, 0-9, +, /, or =

      var base64test = /[^A-Za-z0-9\+\/\=]/g;

      if (base64test.exec(input)) {
        console.warn('There were invalid base64 characters in the input text.\nValid base64 characters are A-Z, a-z, 0-9, \'+\', \'/\',and \'=\'\nExpect errors in decoding.');
      }

      input = input.replace(/[^A-Za-z0-9\+\/\=]/g, '');

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

        chr1 = chr2 = chr3 = '';
        enc1 = enc2 = enc3 = enc4 = '';

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


Dropzone._autoDiscoverFunction = function () {
  if (Dropzone.autoDiscover) {
    return Dropzone.discover();
  }
};

contentLoaded(window, Dropzone._autoDiscoverFunction);

function __guard__(value, transform) {
  return typeof value !== 'undefined' && value !== null ? transform(value) : undefined;
}

function __guardMethod__(obj, methodName, transform) {
  if (typeof obj !== 'undefined' && obj !== null && typeof obj[methodName] === 'function') {
    return transform(obj, methodName);
  } else {
    return undefined;
  }
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/interactjs/dist/interact.min.js":
/*!****************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/interactjs/dist/interact.min.js ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/* interact.js 1.9.9 | https://raw.github.com/taye/interact.js/master/LICENSE */
!function (t) {
  if ("object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module) module.exports = t();else if (true) !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else {}
}(function () {
  function t(e) {
    var n;
    return function (t) {
      return n || e(n = {
        exports: {},
        parent: t
      }, n.exports), n.exports;
    };
  }

  var k = t(function (t, e) {
    "use strict";

    function a(t) {
      return (a = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
        return _typeof(t);
      } : function (t) {
        return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
      })(t);
    }

    Object.defineProperty(e, "__esModule", {
      value: !0
    }), e["default"] = e.Interactable = void 0;
    var u = r(S),
        l = n(C),
        s = n(V),
        c = n(ct),
        f = r(w),
        p = n(ft),
        i = n(bt),
        d = m({});

    function n(t) {
      return t && t.__esModule ? t : {
        "default": t
      };
    }

    function v() {
      if ("function" != typeof WeakMap) return null;
      var t = new WeakMap();
      return v = function v() {
        return t;
      }, t;
    }

    function r(t) {
      if (t && t.__esModule) return t;
      if (null === t || "object" !== a(t) && "function" != typeof t) return {
        "default": t
      };
      var e = v();
      if (e && e.has(t)) return e.get(t);
      var n = {},
          r = Object.defineProperty && Object.getOwnPropertyDescriptor;

      for (var o in t) {
        if (Object.prototype.hasOwnProperty.call(t, o)) {
          var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
          i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
        }
      }

      return n["default"] = t, e && e.set(t, n), n;
    }

    function o(t, e) {
      for (var n = 0; n < e.length; n++) {
        var r = e[n];
        r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
      }
    }

    function y(t, e, n) {
      return e && o(t.prototype, e), n && o(t, n), t;
    }

    function h(t, e, n) {
      return e in t ? Object.defineProperty(t, e, {
        value: n,
        enumerable: !0,
        configurable: !0,
        writable: !0
      }) : t[e] = n, t;
    }

    var g = function () {
      function o(t, e, n, r) {
        !function (t, e) {
          if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
        }(this, o), this._scopeEvents = r, h(this, "options", void 0), h(this, "_actions", void 0), h(this, "target", void 0), h(this, "events", new i["default"]()), h(this, "_context", void 0), h(this, "_win", void 0), h(this, "_doc", void 0), this._actions = e.actions, this.target = t, this._context = e.context || n, this._win = (0, O.getWindow)((0, $.trySelector)(t) ? this._context : t), this._doc = this._win.document, this.set(e);
      }

      return y(o, [{
        key: "_defaults",
        get: function get() {
          return {
            base: {},
            perAction: {},
            actions: {}
          };
        }
      }]), y(o, [{
        key: "setOnEvents",
        value: function value(t, e) {
          return f.func(e.onstart) && this.on("".concat(t, "start"), e.onstart), f.func(e.onmove) && this.on("".concat(t, "move"), e.onmove), f.func(e.onend) && this.on("".concat(t, "end"), e.onend), f.func(e.oninertiastart) && this.on("".concat(t, "inertiastart"), e.oninertiastart), this;
        }
      }, {
        key: "updatePerActionListeners",
        value: function value(t, e, n) {
          (f.array(e) || f.object(e)) && this.off(t, e), (f.array(n) || f.object(n)) && this.on(t, n);
        }
      }, {
        key: "setPerAction",
        value: function value(t, e) {
          var n = this._defaults;

          for (var r in e) {
            var o = r,
                i = this.options[t],
                a = e[o];
            "listeners" === o && this.updatePerActionListeners(t, i.listeners, a), f.array(a) ? i[o] = u.from(a) : f.plainObject(a) ? (i[o] = (0, c["default"])(i[o] || {}, (0, s["default"])(a)), f.object(n.perAction[o]) && "enabled" in n.perAction[o] && (i[o].enabled = !1 !== a.enabled)) : f.bool(a) && f.object(n.perAction[o]) ? i[o].enabled = a : i[o] = a;
          }
        }
      }, {
        key: "getRect",
        value: function value(t) {
          return t = t || (f.element(this.target) ? this.target : null), f.string(this.target) && (t = t || this._context.querySelector(this.target)), (0, $.getElementRect)(t);
        }
      }, {
        key: "rectChecker",
        value: function value(t) {
          return f.func(t) ? (this.getRect = t, this) : null === t ? (delete this.getRect, this) : this.getRect;
        }
      }, {
        key: "_backCompatOption",
        value: function value(t, e) {
          if ((0, $.trySelector)(e) || f.object(e)) {
            for (var n in this.options[t] = e, this._actions.map) {
              this.options[n][t] = e;
            }

            return this;
          }

          return this.options[t];
        }
      }, {
        key: "origin",
        value: function value(t) {
          return this._backCompatOption("origin", t);
        }
      }, {
        key: "deltaSource",
        value: function value(t) {
          return "page" === t || "client" === t ? (this.options.deltaSource = t, this) : this.options.deltaSource;
        }
      }, {
        key: "context",
        value: function value() {
          return this._context;
        }
      }, {
        key: "inContext",
        value: function value(t) {
          return this._context === t.ownerDocument || (0, $.nodeContains)(this._context, t);
        }
      }, {
        key: "testIgnoreAllow",
        value: function value(t, e, n) {
          return !this.testIgnore(t.ignoreFrom, e, n) && this.testAllow(t.allowFrom, e, n);
        }
      }, {
        key: "testAllow",
        value: function value(t, e, n) {
          return !t || !!f.element(n) && (f.string(t) ? (0, $.matchesUpTo)(n, t, e) : !!f.element(t) && (0, $.nodeContains)(t, n));
        }
      }, {
        key: "testIgnore",
        value: function value(t, e, n) {
          return !(!t || !f.element(n)) && (f.string(t) ? (0, $.matchesUpTo)(n, t, e) : !!f.element(t) && (0, $.nodeContains)(t, n));
        }
      }, {
        key: "fire",
        value: function value(t) {
          return this.events.fire(t), this;
        }
      }, {
        key: "_onOff",
        value: function value(t, e, n, r) {
          f.object(e) && !f.array(e) && (r = n, n = null);
          var o = "on" === t ? "add" : "remove",
              i = (0, p["default"])(e, n);

          for (var a in i) {
            "wheel" === a && (a = l["default"].wheelEvent);

            for (var u = 0; u < i[a].length; u++) {
              var s = i[a][u];
              (0, d.isNonNativeEvent)(a, this._actions) ? this.events[t](a, s) : f.string(this.target) ? this._scopeEvents["".concat(o, "Delegate")](this.target, this._context, a, s, r) : this._scopeEvents[o](this.target, a, s, r);
            }
          }

          return this;
        }
      }, {
        key: "on",
        value: function value(t, e, n) {
          return this._onOff("on", t, e, n);
        }
      }, {
        key: "off",
        value: function value(t, e, n) {
          return this._onOff("off", t, e, n);
        }
      }, {
        key: "set",
        value: function value(t) {
          var e = this._defaults;

          for (var n in f.object(t) || (t = {}), this.options = (0, s["default"])(e.base), this._actions.methodDict) {
            var r = n,
                o = this._actions.methodDict[r];
            this.options[r] = {}, this.setPerAction(r, (0, c["default"])((0, c["default"])({}, e.perAction), e.actions[r])), this[o](t[r]);
          }

          for (var i in t) {
            f.func(this[i]) && this[i](t[i]);
          }

          return this;
        }
      }, {
        key: "unset",
        value: function value() {
          if (f.string(this.target)) for (var t in this._scopeEvents.delegatedEvents) {
            for (var e = this._scopeEvents.delegatedEvents[t], n = e.length - 1; 0 <= n; n--) {
              var r = e[n],
                  o = r.selector,
                  i = r.context,
                  a = r.listeners;
              o === this.target && i === this._context && e.splice(n, 1);

              for (var u = a.length - 1; 0 <= u; u--) {
                this._scopeEvents.removeDelegate(this.target, this._context, t, a[u][0], a[u][1]);
              }
            }
          } else this._scopeEvents.remove(this.target, "all");
        }
      }]), o;
    }(),
        b = e.Interactable = g;

    e["default"] = b;
  }),
      m = t(function (t, e) {
    "use strict";

    Object.defineProperty(e, "__esModule", {
      value: !0
    }), e.isNonNativeEvent = function (t, e) {
      if (e.phaselessTypes[t]) return !0;

      for (var n in e.map) {
        if (0 === t.indexOf(n) && t.substr(n.length) in e.phases) return !0;
      }

      return !1;
    }, e.initScope = M, e.Scope = e["default"] = void 0;

    var n = d(D),
        r = function (t) {
      if (t && t.__esModule) return t;
      if (null === t || "object" !== v(t) && "function" != typeof t) return {
        "default": t
      };
      var e = p();
      if (e && e.has(t)) return e.get(t);
      var n = {},
          r = Object.defineProperty && Object.getOwnPropertyDescriptor;

      for (var o in t) {
        if (Object.prototype.hasOwnProperty.call(t, o)) {
          var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
          i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
        }
      }

      n["default"] = t, e && e.set(t, n);
      return n;
    }(le),
        o = d(bt),
        i = d(We),
        a = d(T({})),
        u = d(k({})),
        s = d(Ze),
        l = d(ze),
        c = d(cn),
        f = d(E({}));

    function p() {
      if ("function" != typeof WeakMap) return null;
      var t = new WeakMap();
      return p = function p() {
        return t;
      }, t;
    }

    function d(t) {
      return t && t.__esModule ? t : {
        "default": t
      };
    }

    function v(t) {
      return (v = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
        return _typeof(t);
      } : function (t) {
        return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
      })(t);
    }

    function y(t, e) {
      return !e || "object" !== v(e) && "function" != typeof e ? function (t) {
        if (void 0 !== t) return t;
        throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      }(t) : e;
    }

    function h(t, e, n) {
      return (h = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (t, e, n) {
        var r = function (t, e) {
          for (; !Object.prototype.hasOwnProperty.call(t, e) && null !== (t = g(t));) {
            ;
          }

          return t;
        }(t, e);

        if (r) {
          var o = Object.getOwnPropertyDescriptor(r, e);
          return o.get ? o.get.call(n) : o.value;
        }
      })(t, e, n || t);
    }

    function g(t) {
      return (g = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
        return t.__proto__ || Object.getPrototypeOf(t);
      })(t);
    }

    function b(t, e) {
      return (b = Object.setPrototypeOf || function (t, e) {
        return t.__proto__ = e, t;
      })(t, e);
    }

    function m(t, e) {
      if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
    }

    function O(t, e) {
      for (var n = 0; n < e.length; n++) {
        var r = e[n];
        r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
      }
    }

    function w(t, e, n) {
      return e && O(t.prototype, e), n && O(t, n), t;
    }

    function _(t, e, n) {
      return e in t ? Object.defineProperty(t, e, {
        value: n,
        enumerable: !0,
        configurable: !0,
        writable: !0
      }) : t[e] = n, t;
    }

    var P = r.win,
        x = r.browser,
        S = r.raf,
        j = function () {
      function t() {
        var e = this;
        m(this, t), _(this, "id", "__interact_scope_".concat(Math.floor(100 * Math.random()))), _(this, "isInitialized", !1), _(this, "listenerMaps", []), _(this, "browser", x), _(this, "utils", r), _(this, "defaults", r.clone(l["default"])), _(this, "Eventable", o["default"]), _(this, "actions", {
          map: {},
          phases: {
            start: !0,
            move: !0,
            end: !0
          },
          methodDict: {},
          phaselessTypes: {}
        }), _(this, "interactStatic", new a["default"](this)), _(this, "InteractEvent", i["default"]), _(this, "Interactable", void 0), _(this, "interactables", new s["default"](this)), _(this, "_win", void 0), _(this, "document", void 0), _(this, "window", void 0), _(this, "documents", []), _(this, "_plugins", {
          list: [],
          map: {}
        }), _(this, "onWindowUnload", function (t) {
          return e.removeDocument(t.target);
        });
        var n = this;

        this.Interactable = function () {
          function e() {
            return m(this, e), y(this, g(e).apply(this, arguments));
          }

          return function (t, e) {
            if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
            t.prototype = Object.create(e && e.prototype, {
              constructor: {
                value: t,
                writable: !0,
                configurable: !0
              }
            }), e && b(t, e);
          }(e, u["default"]), w(e, [{
            key: "set",
            value: function value(t) {
              return h(g(e.prototype), "set", this).call(this, t), n.fire("interactable:set", {
                options: t,
                interactable: this
              }), this;
            }
          }, {
            key: "unset",
            value: function value() {
              h(g(e.prototype), "unset", this).call(this), n.interactables.list.splice(n.interactables.list.indexOf(this), 1), n.fire("interactable:unset", {
                interactable: this
              });
            }
          }, {
            key: "_defaults",
            get: function get() {
              return n.defaults;
            }
          }]), e;
        }();
      }

      return w(t, [{
        key: "addListeners",
        value: function value(t, e) {
          this.listenerMaps.push({
            id: e,
            map: t
          });
        }
      }, {
        key: "fire",
        value: function value(t, e) {
          for (var n = 0; n < this.listenerMaps.length; n++) {
            var r = this.listenerMaps[n].map[t];
            if (r && !1 === r(e, this, t)) return !1;
          }
        }
      }, {
        key: "init",
        value: function value(t) {
          return this.isInitialized ? this : M(this, t);
        }
      }, {
        key: "pluginIsInstalled",
        value: function value(t) {
          return this._plugins.map[t.id] || -1 !== this._plugins.list.indexOf(t);
        }
      }, {
        key: "usePlugin",
        value: function value(t, e) {
          if (this.pluginIsInstalled(t)) return this;

          if (t.id && (this._plugins.map[t.id] = t), this._plugins.list.push(t), t.install && t.install(this, e), t.listeners && t.before) {
            for (var n = 0, r = this.listenerMaps.length, o = t.before.reduce(function (t, e) {
              return t[e] = !0, t;
            }, {}); n < r; n++) {
              if (o[this.listenerMaps[n].id]) break;
            }

            this.listenerMaps.splice(n, 0, {
              id: t.id,
              map: t.listeners
            });
          } else t.listeners && this.listenerMaps.push({
            id: t.id,
            map: t.listeners
          });

          return this;
        }
      }, {
        key: "addDocument",
        value: function value(t, e) {
          if (-1 !== this.getDocIndex(t)) return !1;
          var n = P.getWindow(t);
          e = e ? r.extend({}, e) : {}, this.documents.push({
            doc: t,
            options: e
          }), this.events.documents.push(t), t !== this.document && this.events.add(n, "unload", this.onWindowUnload), this.fire("scope:add-document", {
            doc: t,
            window: n,
            scope: this,
            options: e
          });
        }
      }, {
        key: "removeDocument",
        value: function value(t) {
          var e = this.getDocIndex(t),
              n = P.getWindow(t),
              r = this.documents[e].options;
          this.events.remove(n, "unload", this.onWindowUnload), this.documents.splice(e, 1), this.events.documents.splice(e, 1), this.fire("scope:remove-document", {
            doc: t,
            window: n,
            scope: this,
            options: r
          });
        }
      }, {
        key: "getDocIndex",
        value: function value(t) {
          for (var e = 0; e < this.documents.length; e++) {
            if (this.documents[e].doc === t) return e;
          }

          return -1;
        }
      }, {
        key: "getDocOptions",
        value: function value(t) {
          var e = this.getDocIndex(t);
          return -1 === e ? null : this.documents[e].options;
        }
      }, {
        key: "now",
        value: function value() {
          return (this.window.Date || Date).now();
        }
      }]), t;
    }();

    function M(t, e) {
      return t.isInitialized = !0, P.init(e), n["default"].init(e), x.init(e), S.init(e), t.window = e, t.document = e.document, t.usePlugin(f["default"]), t.usePlugin(c["default"]), t;
    }

    e.Scope = e["default"] = j;
  }),
      E = t(function (t, e) {
    "use strict";

    Object.defineProperty(e, "__esModule", {
      value: !0
    }), e["default"] = void 0;

    var _ = n(C),
        u = n(D),
        P = function (t) {
      if (t && t.__esModule) return t;
      if (null === t || "object" !== c(t) && "function" != typeof t) return {
        "default": t
      };
      var e = a();
      if (e && e.has(t)) return e.get(t);
      var n = {},
          r = Object.defineProperty && Object.getOwnPropertyDescriptor;

      for (var o in t) {
        if (Object.prototype.hasOwnProperty.call(t, o)) {
          var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
          i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
        }
      }

      n["default"] = t, e && e.set(t, n);
      return n;
    }(zt),
        s = n(En),
        l = n(Un),
        o = n(tr);

    n(m({}));

    function a() {
      if ("function" != typeof WeakMap) return null;
      var t = new WeakMap();
      return a = function a() {
        return t;
      }, t;
    }

    function n(t) {
      return t && t.__esModule ? t : {
        "default": t
      };
    }

    function c(t) {
      return (c = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
        return _typeof(t);
      } : function (t) {
        return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
      })(t);
    }

    function x(t, e) {
      return function (t) {
        if (Array.isArray(t)) return t;
      }(t) || function (t, e) {
        if (!(Symbol.iterator in Object(t) || "[object Arguments]" === Object.prototype.toString.call(t))) return;
        var n = [],
            r = !0,
            o = !1,
            i = void 0;

        try {
          for (var a, u = t[Symbol.iterator](); !(r = (a = u.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
            ;
          }
        } catch (t) {
          o = !0, i = t;
        } finally {
          try {
            r || null == u["return"] || u["return"]();
          } finally {
            if (o) throw i;
          }
        }

        return n;
      }(t, e) || function () {
        throw new TypeError("Invalid attempt to destructure non-iterable instance");
      }();
    }

    function f(t, e) {
      for (var n = 0; n < e.length; n++) {
        var r = e[n];
        r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
      }
    }

    function p(t, e) {
      return !e || "object" !== c(e) && "function" != typeof e ? function (t) {
        if (void 0 !== t) return t;
        throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      }(t) : e;
    }

    function d(t) {
      return (d = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
        return t.__proto__ || Object.getPrototypeOf(t);
      })(t);
    }

    function v(t, e) {
      return (v = Object.setPrototypeOf || function (t, e) {
        return t.__proto__ = e, t;
      })(t, e);
    }

    var y = ["pointerDown", "pointerMove", "pointerUp", "updatePointer", "removePointer", "windowBlur"];

    function h(O, w) {
      return function (t) {
        var e = w.interactions.list,
            n = P.getPointerType(t),
            r = x(P.getEventTargets(t), 2),
            o = r[0],
            i = r[1],
            a = [];

        if (/^touch/.test(t.type)) {
          w.prevTouchTime = w.now();

          for (var u = 0; u < t.changedTouches.length; u++) {
            s = t.changedTouches[u];
            var s,
                l = {
              pointer: s,
              pointerId: P.getPointerId(s),
              pointerType: n,
              eventType: t.type,
              eventTarget: o,
              curEventTarget: i,
              scope: w
            },
                c = S(l);
            a.push([l.pointer, l.eventTarget, l.curEventTarget, c]);
          }
        } else {
          var f = !1;

          if (!_["default"].supportsPointerEvent && /mouse/.test(t.type)) {
            for (var p = 0; p < e.length && !f; p++) {
              f = "mouse" !== e[p].pointerType && e[p].pointerIsDown;
            }

            f = f || w.now() - w.prevTouchTime < 500 || 0 === t.timeStamp;
          }

          if (!f) {
            var d = {
              pointer: t,
              pointerId: P.getPointerId(t),
              pointerType: n,
              eventType: t.type,
              curEventTarget: i,
              eventTarget: o,
              scope: w
            },
                v = S(d);
            a.push([d.pointer, d.eventTarget, d.curEventTarget, v]);
          }
        }

        for (var y = 0; y < a.length; y++) {
          var h = x(a[y], 4),
              g = h[0],
              b = h[1],
              m = h[2];
          h[3][O](g, t, b, m);
        }
      };
    }

    function S(t) {
      var e = t.pointerType,
          n = t.scope,
          r = {
        interaction: o["default"].search(t),
        searchDetails: t
      };
      return n.fire("interactions:find", r), r.interaction || n.interactions["new"]({
        pointerType: e
      });
    }

    function r(t, e) {
      var n = t.doc,
          r = t.scope,
          o = t.options,
          i = r.interactions.docEvents,
          a = r.events,
          u = a[e];

      for (var s in r.browser.isIOS && !o.events && (o.events = {
        passive: !1
      }), a.delegatedEvents) {
        u(n, s, a.delegateListener), u(n, s, a.delegateUseCapture, !0);
      }

      for (var l = o && o.events, c = 0; c < i.length; c++) {
        var f;
        f = i[c];
        u(n, f.type, f.listener, l);
      }
    }

    var i = {
      id: "core/interactions",
      install: function install(o) {
        for (var t = {}, e = 0; e < y.length; e++) {
          var n;
          n = y[e];
          t[n] = h(n, o);
        }

        var r,
            i = _["default"].pEventTypes;

        function a() {
          for (var t = 0; t < o.interactions.list.length; t++) {
            var e = o.interactions.list[t];
            if (e.pointerIsDown && "touch" === e.pointerType && !e._interacting) for (var n = function n() {
              var n = e.pointers[r];
              o.documents.some(function (t) {
                var e = t.doc;
                return (0, $.nodeContains)(e, n.downTarget);
              }) || e.removePointer(n.pointer, n.event);
            }, r = 0; r < e.pointers.length; r++) {
              n();
            }
          }
        }

        (r = u["default"].PointerEvent ? [{
          type: i.down,
          listener: a
        }, {
          type: i.down,
          listener: t.pointerDown
        }, {
          type: i.move,
          listener: t.pointerMove
        }, {
          type: i.up,
          listener: t.pointerUp
        }, {
          type: i.cancel,
          listener: t.pointerUp
        }] : [{
          type: "mousedown",
          listener: t.pointerDown
        }, {
          type: "mousemove",
          listener: t.pointerMove
        }, {
          type: "mouseup",
          listener: t.pointerUp
        }, {
          type: "touchstart",
          listener: a
        }, {
          type: "touchstart",
          listener: t.pointerDown
        }, {
          type: "touchmove",
          listener: t.pointerMove
        }, {
          type: "touchend",
          listener: t.pointerUp
        }, {
          type: "touchcancel",
          listener: t.pointerUp
        }]).push({
          type: "blur",
          listener: function listener(t) {
            for (var e = 0; e < o.interactions.list.length; e++) {
              o.interactions.list[e].documentBlur(t);
            }
          }
        }), o.prevTouchTime = 0, o.Interaction = function () {
          function t() {
            return function (t, e) {
              if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
            }(this, t), p(this, d(t).apply(this, arguments));
          }

          var e, n, r;
          return function (t, e) {
            if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
            t.prototype = Object.create(e && e.prototype, {
              constructor: {
                value: t,
                writable: !0,
                configurable: !0
              }
            }), e && v(t, e);
          }(t, s["default"]), e = t, (n = [{
            key: "_now",
            value: function value() {
              return o.now();
            }
          }, {
            key: "pointerMoveTolerance",
            get: function get() {
              return o.interactions.pointerMoveTolerance;
            },
            set: function set(t) {
              o.interactions.pointerMoveTolerance = t;
            }
          }]) && f(e.prototype, n), r && f(e, r), t;
        }(), o.interactions = {
          list: [],
          "new": function _new(t) {
            t.scopeFire = function (t, e) {
              return o.fire(t, e);
            };

            var e = new o.Interaction(t);
            return o.interactions.list.push(e), e;
          },
          listeners: t,
          docEvents: r,
          pointerMoveTolerance: 1
        }, o.usePlugin(l["default"]);
      },
      listeners: {
        "scope:add-document": function scopeAddDocument(t) {
          return r(t, "add");
        },
        "scope:remove-document": function scopeRemoveDocument(t) {
          return r(t, "remove");
        },
        "interactable:unset": function interactableUnset(t, e) {
          for (var n = t.interactable, r = e.interactions.list.length - 1; 0 <= r; r--) {
            var o = e.interactions.list[r];
            o.interactable === n && (o.stop(), e.fire("interactions:destroy", {
              interaction: o
            }), o.destroy(), 2 < e.interactions.list.length && e.interactions.list.splice(r, 1));
          }
        }
      },
      onDocSignal: r,
      doOnInteractions: h,
      methodNames: y
    };
    e["default"] = i;
  }),
      T = t(function (t, e) {
    "use strict";

    function a(t) {
      return (a = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
        return _typeof(t);
      } : function (t) {
        return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
      })(t);
    }

    Object.defineProperty(e, "__esModule", {
      value: !0
    }), e["default"] = e.InteractStatic = void 0;

    var n,
        r = (n = C) && n.__esModule ? n : {
      "default": n
    },
        u = function (t) {
      if (t && t.__esModule) return t;
      if (null === t || "object" !== a(t) && "function" != typeof t) return {
        "default": t
      };
      var e = l();
      if (e && e.has(t)) return e.get(t);
      var n = {},
          r = Object.defineProperty && Object.getOwnPropertyDescriptor;

      for (var o in t) {
        if (Object.prototype.hasOwnProperty.call(t, o)) {
          var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
          i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
        }
      }

      n["default"] = t, e && e.set(t, n);
      return n;
    }(le),
        s = m({});

    function l() {
      if ("function" != typeof WeakMap) return null;
      var t = new WeakMap();
      return l = function l() {
        return t;
      }, t;
    }

    function o(t, e) {
      for (var n = 0; n < e.length; n++) {
        var r = e[n];
        r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
      }
    }

    function c(t, e, n) {
      return e in t ? Object.defineProperty(t, e, {
        value: n,
        enumerable: !0,
        configurable: !0,
        writable: !0
      }) : t[e] = n, t;
    }

    var i = function () {
      function a(r) {
        var o = this;
        !function (t, e) {
          if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
        }(this, a), this.scope = r, c(this, "getPointerAverage", u.pointer.pointerAverage), c(this, "getTouchBBox", u.pointer.touchBBox), c(this, "getTouchDistance", u.pointer.touchDistance), c(this, "getTouchAngle", u.pointer.touchAngle), c(this, "getElementRect", u.dom.getElementRect), c(this, "getElementClientRect", u.dom.getElementClientRect), c(this, "matchesSelector", u.dom.matchesSelector), c(this, "closest", u.dom.closest), c(this, "globalEvents", {}), c(this, "dynamicDrop", void 0), c(this, "version", "1.9.9"), c(this, "interact", void 0);

        for (var t = this.constructor.prototype, e = function e(t, _e2) {
          var n = r.interactables.get(t, _e2);
          return n || ((n = r.interactables["new"](t, _e2)).events.global = o.globalEvents), n;
        }, n = 0; n < Object.getOwnPropertyNames(this.constructor.prototype).length; n++) {
          var i;
          i = Object.getOwnPropertyNames(this.constructor.prototype)[n];
          e[i] = t[i];
        }

        return u.extend(e, this), e.constructor = this.constructor, this.interact = e;
      }

      var t, e, n;
      return t = a, (e = [{
        key: "use",
        value: function value(t, e) {
          return this.scope.usePlugin(t, e), this;
        }
      }, {
        key: "isSet",
        value: function value(t, e) {
          return !!this.scope.interactables.get(t, e && e.context);
        }
      }, {
        key: "on",
        value: function value(t, e, n) {
          if (u.is.string(t) && -1 !== t.search(" ") && (t = t.trim().split(/ +/)), u.is.array(t)) {
            for (var r = 0; r < t.length; r++) {
              var o = t[r];
              this.on(o, e, n);
            }

            return this;
          }

          if (u.is.object(t)) {
            for (var i in t) {
              this.on(i, t[i], e);
            }

            return this;
          }

          return (0, s.isNonNativeEvent)(t, this.scope.actions) ? this.globalEvents[t] ? this.globalEvents[t].push(e) : this.globalEvents[t] = [e] : this.scope.events.add(this.scope.document, t, e, {
            options: n
          }), this;
        }
      }, {
        key: "off",
        value: function value(t, e, n) {
          if (u.is.string(t) && -1 !== t.search(" ") && (t = t.trim().split(/ +/)), u.is.array(t)) {
            for (var r = 0; r < t.length; r++) {
              var o = t[r];
              this.off(o, e, n);
            }

            return this;
          }

          if (u.is.object(t)) {
            for (var i in t) {
              this.off(i, t[i], e);
            }

            return this;
          }

          var a;
          (0, s.isNonNativeEvent)(t, this.scope.actions) ? t in this.globalEvents && -1 !== (a = this.globalEvents[t].indexOf(e)) && this.globalEvents[t].splice(a, 1) : this.scope.events.remove(this.scope.document, t, e, n);
          return this;
        }
      }, {
        key: "debug",
        value: function value() {
          return this.scope;
        }
      }, {
        key: "supportsTouch",
        value: function value() {
          return r["default"].supportsTouch;
        }
      }, {
        key: "supportsPointerEvent",
        value: function value() {
          return r["default"].supportsPointerEvent;
        }
      }, {
        key: "stop",
        value: function value() {
          for (var t = 0; t < this.scope.interactions.list.length; t++) {
            this.scope.interactions.list[t].stop();
          }

          return this;
        }
      }, {
        key: "pointerMoveTolerance",
        value: function value(t) {
          return u.is.number(t) ? (this.scope.interactions.pointerMoveTolerance = t, this) : this.scope.interactions.pointerMoveTolerance;
        }
      }, {
        key: "addDocument",
        value: function value(t, e) {
          this.scope.addDocument(t, e);
        }
      }, {
        key: "removeDocument",
        value: function value(t) {
          this.scope.removeDocument(t);
        }
      }]) && o(t.prototype, e), n && o(t, n), a;
    }(),
        f = e.InteractStatic = i;

    e["default"] = f;
  }),
      e = {};
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e["default"] = void 0;

  e["default"] = function (t) {
    return !(!t || !t.Window) && t instanceof t.Window;
  };

  var O = {};
  Object.defineProperty(O, "__esModule", {
    value: !0
  }), O.init = i, O.getWindow = a, O["default"] = void 0;
  var n,
      r = (n = e) && n.__esModule ? n : {
    "default": n
  };
  var o = {
    realWindow: void 0,
    window: void 0,
    getWindow: a,
    init: i
  };

  function i(t) {
    var e = (o.realWindow = t).document.createTextNode("");
    e.ownerDocument !== t.document && "function" == typeof t.wrap && t.wrap(e) === e && (t = t.wrap(t)), o.window = t;
  }

  function a(t) {
    return (0, r["default"])(t) ? t : (t.ownerDocument || t).defaultView || o.window;
  }

  "undefined" == typeof window ? (o.window = void 0, o.realWindow = void 0) : i(window), o.init = i;
  var u = o;
  O["default"] = u;
  var w = {};
  Object.defineProperty(w, "__esModule", {
    value: !0
  }), w.array = w.plainObject = w.element = w.string = w.bool = w.number = w.func = w.object = w.docFrag = w.window = void 0;
  var s = c(e),
      l = c(O);

  function c(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function f(t) {
    return (f = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  w.window = function (t) {
    return t === l["default"].window || (0, s["default"])(t);
  };

  w.docFrag = function (t) {
    return p(t) && 11 === t.nodeType;
  };

  var p = function p(t) {
    return !!t && "object" === f(t);
  };

  w.object = p;

  function d(t) {
    return "function" == typeof t;
  }

  w.func = d;

  w.number = function (t) {
    return "number" == typeof t;
  };

  w.bool = function (t) {
    return "boolean" == typeof t;
  };

  w.string = function (t) {
    return "string" == typeof t;
  };

  w.element = function (t) {
    if (!t || "object" !== f(t)) return !1;
    var e = l["default"].getWindow(t) || l["default"].window;
    return /object|function/.test(f(e.Element)) ? t instanceof e.Element : 1 === t.nodeType && "string" == typeof t.nodeName;
  };

  w.plainObject = function (t) {
    return p(t) && !!t.constructor && /function Object\b/.test(t.constructor.toString());
  };

  w.array = function (t) {
    return p(t) && void 0 !== t.length && d(t.splice);
  };

  var v = {};

  function y(t) {
    return (y = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(v, "__esModule", {
    value: !0
  }), v["default"] = void 0;

  var h = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== y(t) && "function" != typeof t) return {
      "default": t
    };
    var e = g();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w);

  function g() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return g = function g() {
      return t;
    }, t;
  }

  function b(t) {
    var e = t.interaction;

    if ("drag" === e.prepared.name) {
      var n = e.prepared.axis;
      "x" === n ? (e.coords.cur.page.y = e.coords.start.page.y, e.coords.cur.client.y = e.coords.start.client.y, e.coords.velocity.client.y = 0, e.coords.velocity.page.y = 0) : "y" === n && (e.coords.cur.page.x = e.coords.start.page.x, e.coords.cur.client.x = e.coords.start.client.x, e.coords.velocity.client.x = 0, e.coords.velocity.page.x = 0);
    }
  }

  function _(t) {
    var e = t.iEvent,
        n = t.interaction;

    if ("drag" === n.prepared.name) {
      var r = n.prepared.axis;

      if ("x" === r || "y" === r) {
        var o = "x" === r ? "y" : "x";
        e.page[o] = n.coords.start.page[o], e.client[o] = n.coords.start.client[o], e.delta[o] = 0;
      }
    }
  }

  var P = {
    id: "actions/drag",
    install: function install(t) {
      var e = t.actions,
          n = t.Interactable,
          r = t.defaults;
      n.prototype.draggable = P.draggable, e.map.drag = P, e.methodDict.drag = "draggable", r.actions.drag = P.defaults;
    },
    listeners: {
      "interactions:before-action-move": b,
      "interactions:action-resume": b,
      "interactions:action-move": _,
      "auto-start:check": function autoStartCheck(t) {
        var e = t.interaction,
            n = t.interactable,
            r = t.buttons,
            o = n.options.drag;
        if (o && o.enabled && (!e.pointerIsDown || !/mouse|pointer/.test(e.pointerType) || 0 != (r & n.options.drag.mouseButtons))) return !(t.action = {
          name: "drag",
          axis: "start" === o.lockAxis ? o.startAxis : o.lockAxis
        });
      }
    },
    draggable: function draggable(t) {
      return h.object(t) ? (this.options.drag.enabled = !1 !== t.enabled, this.setPerAction("drag", t), this.setOnEvents("drag", t), /^(xy|x|y|start)$/.test(t.lockAxis) && (this.options.drag.lockAxis = t.lockAxis), /^(xy|x|y)$/.test(t.startAxis) && (this.options.drag.startAxis = t.startAxis), this) : h.bool(t) ? (this.options.drag.enabled = t, this) : this.options.drag;
    },
    beforeMove: b,
    move: _,
    defaults: {
      startAxis: "xy",
      lockAxis: "xy"
    },
    getCursor: function getCursor() {
      return "move";
    }
  },
      x = P;
  v["default"] = x;
  var S = {};
  Object.defineProperty(S, "__esModule", {
    value: !0
  }), S.find = S.findIndex = S.from = S.merge = S.remove = S.contains = void 0;

  S.contains = function (t, e) {
    return -1 !== t.indexOf(e);
  };

  S.remove = function (t, e) {
    return t.splice(t.indexOf(e), 1);
  };

  function j(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      t.push(r);
    }

    return t;
  }

  S.merge = j;

  S.from = function (t) {
    return j([], t);
  };

  function M(t, e) {
    for (var n = 0; n < t.length; n++) {
      if (e(t[n], n, t)) return n;
    }

    return -1;
  }

  S.findIndex = M;

  S.find = function (t, e) {
    return t[M(t, e)];
  };

  var D = {};
  Object.defineProperty(D, "__esModule", {
    value: !0
  }), D["default"] = void 0;
  var I = {
    init: function init(t) {
      var e = t;
      I.document = e.document, I.DocumentFragment = e.DocumentFragment || z, I.SVGElement = e.SVGElement || z, I.SVGSVGElement = e.SVGSVGElement || z, I.SVGElementInstance = e.SVGElementInstance || z, I.Element = e.Element || z, I.HTMLElement = e.HTMLElement || I.Element, I.Event = e.Event, I.Touch = e.Touch || z, I.PointerEvent = e.PointerEvent || e.MSPointerEvent;
    },
    document: null,
    DocumentFragment: null,
    SVGElement: null,
    SVGSVGElement: null,
    SVGElementInstance: null,
    Element: null,
    HTMLElement: null,
    Event: null,
    Touch: null,
    PointerEvent: null
  };

  function z() {}

  var A = I;
  D["default"] = A;
  var C = {};

  function W(t) {
    return (W = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(C, "__esModule", {
    value: !0
  }), C["default"] = void 0;

  var R = N(D),
      F = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== W(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Y();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w),
      X = N(O);

  function Y() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Y = function Y() {
      return t;
    }, t;
  }

  function N(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var L = {
    init: function init(t) {
      var e = R["default"].Element,
          n = X["default"].window.navigator;
      L.supportsTouch = "ontouchstart" in t || F.func(t.DocumentTouch) && R["default"].document instanceof t.DocumentTouch, L.supportsPointerEvent = !1 !== n.pointerEnabled && !!R["default"].PointerEvent, L.isIOS = /iP(hone|od|ad)/.test(n.platform), L.isIOS7 = /iP(hone|od|ad)/.test(n.platform) && /OS 7[^\d]/.test(n.appVersion), L.isIe9 = /MSIE 9/.test(n.userAgent), L.isOperaMobile = "Opera" === n.appName && L.supportsTouch && /Presto/.test(n.userAgent), L.prefixedMatchesSelector = "matches" in e.prototype ? "matches" : "webkitMatchesSelector" in e.prototype ? "webkitMatchesSelector" : "mozMatchesSelector" in e.prototype ? "mozMatchesSelector" : "oMatchesSelector" in e.prototype ? "oMatchesSelector" : "msMatchesSelector", L.pEventTypes = L.supportsPointerEvent ? R["default"].PointerEvent === t.MSPointerEvent ? {
        up: "MSPointerUp",
        down: "MSPointerDown",
        over: "mouseover",
        out: "mouseout",
        move: "MSPointerMove",
        cancel: "MSPointerCancel"
      } : {
        up: "pointerup",
        down: "pointerdown",
        over: "pointerover",
        out: "pointerout",
        move: "pointermove",
        cancel: "pointercancel"
      } : null, L.wheelEvent = "onmousewheel" in R["default"].document ? "mousewheel" : "wheel";
    },
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
  var B = L;
  C["default"] = B;
  var V = {};

  function q(t) {
    return (q = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(V, "__esModule", {
    value: !0
  }), V["default"] = function t(e) {
    var n = {};

    for (var r in e) {
      var o = e[r];
      G.plainObject(o) ? n[r] = t(o) : G.array(o) ? n[r] = U.from(o) : n[r] = o;
    }

    return n;
  };
  var U = K(S),
      G = K(w);

  function H() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return H = function H() {
      return t;
    }, t;
  }

  function K(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== q(t) && "function" != typeof t) return {
      "default": t
    };
    var e = H();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  var $ = {};

  function Z(t) {
    return (Z = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty($, "__esModule", {
    value: !0
  }), $.nodeContains = function (t, e) {
    for (; e;) {
      if (e === t) return !0;
      e = e.parentNode;
    }

    return !1;
  }, $.closest = function (t, e) {
    for (; tt.element(t);) {
      if (at(t, e)) return t;
      t = it(t);
    }

    return null;
  }, $.parentNode = it, $.matchesSelector = at, $.indexOfDeepestElement = function (t) {
    var e,
        n,
        r = [],
        o = t[0],
        i = o ? 0 : -1;

    for (e = 1; e < t.length; e++) {
      var a = t[e];
      if (a && a !== o) if (o) {
        if (a.parentNode !== a.ownerDocument) if (o.parentNode !== a.ownerDocument) {
          if (a.parentNode !== o.parentNode) {
            if (!r.length) for (var u = o, s = void 0; (s = ut(u)) && s !== u.ownerDocument;) {
              r.unshift(u), u = s;
            }
            var l = void 0;

            if (o instanceof Q["default"].HTMLElement && a instanceof Q["default"].SVGElement && !(a instanceof Q["default"].SVGSVGElement)) {
              if (a === o.parentNode) continue;
              l = a.ownerSVGElement;
            } else l = a;

            for (var c = []; l.parentNode !== l.ownerDocument;) {
              c.unshift(l), l = ut(l);
            }

            for (n = 0; c[n] && c[n] === r[n];) {
              n++;
            }

            for (var f = [c[n - 1], c[n], r[n]], p = f[0].lastChild; p;) {
              if (p === f[1]) {
                o = a, i = e, r = c;
                break;
              }

              if (p === f[2]) break;
              p = p.previousSibling;
            }
          } else {
            var d = parseInt((0, et.getWindow)(o).getComputedStyle(o).zIndex, 10) || 0,
                v = parseInt((0, et.getWindow)(a).getComputedStyle(a).zIndex, 10) || 0;
            d <= v && (o = a, i = e);
          }
        } else o = a, i = e;
      } else o = a, i = e;
    }

    return i;
  }, $.matchesUpTo = function (t, e, n) {
    for (; tt.element(t);) {
      if (at(t, e)) return !0;
      if ((t = it(t)) === n) return at(t, e);
    }

    return !1;
  }, $.getActualElement = function (t) {
    return t instanceof Q["default"].SVGElementInstance ? t.correspondingUseElement : t;
  }, $.getScrollXY = st, $.getElementClientRect = lt, $.getElementRect = function (t) {
    var e = lt(t);

    if (!J["default"].isIOS7 && e) {
      var n = st(et["default"].getWindow(t));
      e.left += n.x, e.right += n.x, e.top += n.y, e.bottom += n.y;
    }

    return e;
  }, $.getPath = function (t) {
    var e = [];

    for (; t;) {
      e.push(t), t = it(t);
    }

    return e;
  }, $.trySelector = function (t) {
    return !!tt.string(t) && (Q["default"].document.querySelector(t), !0);
  };
  var J = ot(C),
      Q = ot(D),
      tt = rt(w),
      et = rt(O);

  function nt() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return nt = function nt() {
      return t;
    }, t;
  }

  function rt(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Z(t) && "function" != typeof t) return {
      "default": t
    };
    var e = nt();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function ot(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function it(t) {
    var e = t.parentNode;

    if (tt.docFrag(e)) {
      for (; (e = e.host) && tt.docFrag(e);) {
        ;
      }

      return e;
    }

    return e;
  }

  function at(t, e) {
    return et["default"].window !== et["default"].realWindow && (e = e.replace(/\/deep\//g, " ")), t[J["default"].prefixedMatchesSelector](e);
  }

  var ut = function ut(t) {
    return t.parentNode ? t.parentNode : t.host;
  };

  function st(t) {
    return {
      x: (t = t || et["default"].window).scrollX || t.document.documentElement.scrollLeft,
      y: t.scrollY || t.document.documentElement.scrollTop
    };
  }

  function lt(t) {
    var e = t instanceof Q["default"].SVGElement ? t.getBoundingClientRect() : t.getClientRects()[0];
    return e && {
      left: e.left,
      right: e.right,
      top: e.top,
      bottom: e.bottom,
      width: e.width || e.right - e.left,
      height: e.height || e.bottom - e.top
    };
  }

  var ct = {};
  Object.defineProperty(ct, "__esModule", {
    value: !0
  }), ct["default"] = function (t, e) {
    for (var n in e) {
      t[n] = e[n];
    }

    return t;
  };
  var ft = {};

  function pt(t) {
    return (pt = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(ft, "__esModule", {
    value: !0
  }), ft["default"] = function n(e, r, o) {
    o = o || {};
    yt.string(e) && -1 !== e.search(" ") && (e = gt(e));
    if (yt.array(e)) return e.reduce(function (t, e) {
      return (0, vt["default"])(t, n(e, r, o));
    }, o);
    yt.object(e) && (r = e, e = "");
    if (yt.func(r)) o[e] = o[e] || [], o[e].push(r);else if (yt.array(r)) for (var t = 0; t < r.length; t++) {
      var i = r[t];
      n(e, i, o);
    } else if (yt.object(r)) for (var a in r) {
      var u = gt(a).map(function (t) {
        return "".concat(e).concat(t);
      });
      n(u, r[a], o);
    }
    return o;
  };

  var dt,
      vt = (dt = ct) && dt.__esModule ? dt : {
    "default": dt
  },
      yt = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== pt(t) && "function" != typeof t) return {
      "default": t
    };
    var e = ht();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w);

  function ht() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return ht = function ht() {
      return t;
    }, t;
  }

  function gt(t) {
    return t.trim().split(/ +/);
  }

  var bt = {};

  function mt(t) {
    return (mt = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(bt, "__esModule", {
    value: !0
  }), bt["default"] = void 0;

  var Ot = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== mt(t) && "function" != typeof t) return {
      "default": t
    };
    var e = xt();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(S),
      wt = Pt(ct),
      _t = Pt(ft);

  function Pt(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function xt() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return xt = function xt() {
      return t;
    }, t;
  }

  function St(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function jt(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  function Mt(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      if (t.immediatePropagationStopped) break;
      r(t);
    }
  }

  var kt = function () {
    function e(t) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, e), jt(this, "options", void 0), jt(this, "types", {}), jt(this, "propagationStopped", !1), jt(this, "immediatePropagationStopped", !1), jt(this, "global", void 0), this.options = (0, wt["default"])({}, t || {});
    }

    var t, n, r;
    return t = e, (n = [{
      key: "fire",
      value: function value(t) {
        var e,
            n = this.global;
        (e = this.types[t.type]) && Mt(t, e), !t.propagationStopped && n && (e = n[t.type]) && Mt(t, e);
      }
    }, {
      key: "on",
      value: function value(t, e) {
        var n = (0, _t["default"])(t, e);

        for (t in n) {
          this.types[t] = Ot.merge(this.types[t] || [], n[t]);
        }
      }
    }, {
      key: "off",
      value: function value(t, e) {
        var n = (0, _t["default"])(t, e);

        for (t in n) {
          var r = this.types[t];
          if (r && r.length) for (var o = 0; o < n[t].length; o++) {
            var i = n[t][o],
                a = r.indexOf(i);
            -1 !== a && r.splice(a, 1);
          }
        }
      }
    }, {
      key: "getRect",
      value: function value() {
        return null;
      }
    }]) && St(t.prototype, n), r && St(t, r), e;
  }();

  bt["default"] = kt;
  var Et = {};
  Object.defineProperty(Et, "__esModule", {
    value: !0
  }), Et["default"] = void 0;

  Et["default"] = function (t, e) {
    return Math.sqrt(t * t + e * e);
  };

  var Tt = {};

  function Dt(t, e) {
    for (var n in e) {
      var r = Dt.prefixedPropREs,
          o = !1;

      for (var i in r) {
        if (0 === n.indexOf(i) && r[i].test(n)) {
          o = !0;
          break;
        }
      }

      o || "function" == typeof e[n] || (t[n] = e[n]);
    }

    return t;
  }

  Object.defineProperty(Tt, "__esModule", {
    value: !0
  }), Tt["default"] = void 0, Dt.prefixedPropREs = {
    webkit: /(Movement[XY]|Radius[XY]|RotationAngle|Force)$/,
    moz: /(Pressure)$/
  };
  var It = Dt;
  Tt["default"] = It;
  var zt = {};

  function At(t) {
    return (At = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(zt, "__esModule", {
    value: !0
  }), zt.copyCoords = function (t, e) {
    t.page = t.page || {}, t.page.x = e.page.x, t.page.y = e.page.y, t.client = t.client || {}, t.client.x = e.client.x, t.client.y = e.client.y, t.timeStamp = e.timeStamp;
  }, zt.setCoordDeltas = function (t, e, n) {
    t.page.x = n.page.x - e.page.x, t.page.y = n.page.y - e.page.y, t.client.x = n.client.x - e.client.x, t.client.y = n.client.y - e.client.y, t.timeStamp = n.timeStamp - e.timeStamp;
  }, zt.setCoordVelocity = function (t, e) {
    var n = Math.max(e.timeStamp / 1e3, .001);
    t.page.x = e.page.x / n, t.page.y = e.page.y / n, t.client.x = e.client.x / n, t.client.y = e.client.y / n, t.timeStamp = n;
  }, zt.setZeroCoords = function (t) {
    t.page.x = 0, t.page.y = 0, t.client.x = 0, t.client.y = 0;
  }, zt.isNativePointer = Vt, zt.getXY = qt, zt.getPageXY = Ut, zt.getClientXY = Gt, zt.getPointerId = function (t) {
    return Xt.number(t.pointerId) ? t.pointerId : t.identifier;
  }, zt.setCoords = function (t, e, n) {
    var r = 1 < e.length ? Kt(e) : e[0],
        o = {};
    Ut(r, o), t.page.x = o.x, t.page.y = o.y, Gt(r, o), t.client.x = o.x, t.client.y = o.y, t.timeStamp = n;
  }, zt.getTouchPair = Ht, zt.pointerAverage = Kt, zt.touchBBox = function (t) {
    if (!(t.length || t.touches && 1 < t.touches.length)) return null;
    var e = Ht(t),
        n = Math.min(e[0].pageX, e[1].pageX),
        r = Math.min(e[0].pageY, e[1].pageY),
        o = Math.max(e[0].pageX, e[1].pageX),
        i = Math.max(e[0].pageY, e[1].pageY);
    return {
      x: n,
      y: r,
      left: n,
      top: r,
      right: o,
      bottom: i,
      width: o - n,
      height: i - r
    };
  }, zt.touchDistance = function (t, e) {
    var n = e + "X",
        r = e + "Y",
        o = Ht(t),
        i = o[0][n] - o[1][n],
        a = o[0][r] - o[1][r];
    return (0, Ft["default"])(i, a);
  }, zt.touchAngle = function (t, e) {
    var n = e + "X",
        r = e + "Y",
        o = Ht(t),
        i = o[1][n] - o[0][n],
        a = o[1][r] - o[0][r];
    return 180 * Math.atan2(a, i) / Math.PI;
  }, zt.getPointerType = function (t) {
    return Xt.string(t.pointerType) ? t.pointerType : Xt.number(t.pointerType) ? [void 0, void 0, "touch", "pen", "mouse"][t.pointerType] : /touch/.test(t.type) || t instanceof Wt["default"].Touch ? "touch" : "mouse";
  }, zt.getEventTargets = function (t) {
    var e = Xt.func(t.composedPath) ? t.composedPath() : t.path;
    return [Rt.getActualElement(e ? e[0] : t.target), Rt.getActualElement(t.currentTarget)];
  }, zt.newCoords = function () {
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
  }, zt.coordsToEvent = function (t) {
    return {
      coords: t,

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
      },

      preventDefault: function preventDefault() {}
    };
  }, Object.defineProperty(zt, "pointerExtend", {
    enumerable: !0,
    get: function get() {
      return Yt["default"];
    }
  });
  var Ct = Bt(C),
      Wt = Bt(D),
      Rt = Lt($),
      Ft = Bt(Et),
      Xt = Lt(w),
      Yt = Bt(Tt);

  function Nt() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Nt = function Nt() {
      return t;
    }, t;
  }

  function Lt(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== At(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Nt();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function Bt(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function Vt(t) {
    return t instanceof Wt["default"].Event || t instanceof Wt["default"].Touch;
  }

  function qt(t, e, n) {
    return (n = n || {}).x = e[(t = t || "page") + "X"], n.y = e[t + "Y"], n;
  }

  function Ut(t, e) {
    return e = e || {
      x: 0,
      y: 0
    }, Ct["default"].isOperaMobile && Vt(t) ? (qt("screen", t, e), e.x += window.scrollX, e.y += window.scrollY) : qt("page", t, e), e;
  }

  function Gt(t, e) {
    return e = e || {}, Ct["default"].isOperaMobile && Vt(t) ? qt("screen", t, e) : qt("client", t, e), e;
  }

  function Ht(t) {
    var e = [];
    return Xt.array(t) ? (e[0] = t[0], e[1] = t[1]) : "touchend" === t.type ? 1 === t.touches.length ? (e[0] = t.touches[0], e[1] = t.changedTouches[0]) : 0 === t.touches.length && (e[0] = t.changedTouches[0], e[1] = t.changedTouches[1]) : (e[0] = t.touches[0], e[1] = t.touches[1]), e;
  }

  function Kt(t) {
    for (var e = {
      pageX: 0,
      pageY: 0,
      clientX: 0,
      clientY: 0,
      screenX: 0,
      screenY: 0
    }, n = 0; n < t.length; n++) {
      var r = t[n];

      for (var o in e) {
        e[o] += r[o];
      }
    }

    for (var i in e) {
      e[i] /= t.length;
    }

    return e;
  }

  var $t = {};

  function Zt(t) {
    return (Zt = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty($t, "__esModule", {
    value: !0
  }), $t.getStringOptionResult = ne, $t.resolveRectLike = function (t, e, n, r) {
    var o = t;
    te.string(o) ? o = ne(o, e, n) : te.func(o) && (o = o.apply(void 0, function (t) {
      return function (t) {
        if (Array.isArray(t)) {
          for (var e = 0, n = new Array(t.length); e < t.length; e++) {
            n[e] = t[e];
          }

          return n;
        }
      }(t) || function (t) {
        if (Symbol.iterator in Object(t) || "[object Arguments]" === Object.prototype.toString.call(t)) return Array.from(t);
      }(t) || function () {
        throw new TypeError("Invalid attempt to spread non-iterable instance");
      }();
    }(r)));
    te.element(o) && (o = (0, $.getElementRect)(o));
    return o;
  }, $t.rectToXY = function (t) {
    return t && {
      x: "x" in t ? t.x : t.left,
      y: "y" in t ? t.y : t.top
    };
  }, $t.xywhToTlbr = function (t) {
    !t || "left" in t && "top" in t || ((t = (0, Qt["default"])({}, t)).left = t.x || 0, t.top = t.y || 0, t.right = t.right || t.left + t.width, t.bottom = t.bottom || t.top + t.height);
    return t;
  }, $t.tlbrToXywh = function (t) {
    !t || "x" in t && "y" in t || ((t = (0, Qt["default"])({}, t)).x = t.left || 0, t.y = t.top || 0, t.width = t.width || t.right || 0 - t.x, t.height = t.height || t.bottom || 0 - t.y);
    return t;
  }, $t.addEdges = function (t, e, n) {
    t.left && (e.left += n.x);
    t.right && (e.right += n.x);
    t.top && (e.top += n.y);
    t.bottom && (e.bottom += n.y);
    e.width = e.right - e.left, e.height = e.bottom - e.top;
  };

  var Jt,
      Qt = (Jt = ct) && Jt.__esModule ? Jt : {
    "default": Jt
  },
      te = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Zt(t) && "function" != typeof t) return {
      "default": t
    };
    var e = ee();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w);

  function ee() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return ee = function ee() {
      return t;
    }, t;
  }

  function ne(t, e, n) {
    return "parent" === t ? (0, $.parentNode)(n) : "self" === t ? e.getRect(n) : (0, $.closest)(n, t);
  }

  var re = {};
  Object.defineProperty(re, "__esModule", {
    value: !0
  }), re["default"] = function (t, e, n) {
    var r = t.options[n],
        o = r && r.origin || t.options.origin,
        i = (0, $t.resolveRectLike)(o, t, e, [t && e]);
    return (0, $t.rectToXY)(i) || {
      x: 0,
      y: 0
    };
  };
  var oe = {};
  Object.defineProperty(oe, "__esModule", {
    value: !0
  }), oe["default"] = void 0;
  var ie,
      ae,
      ue = 0;
  var se = {
    request: function request(t) {
      return ie(t);
    },
    cancel: function cancel(t) {
      return ae(t);
    },
    init: function init(t) {
      if (ie = t.requestAnimationFrame, ae = t.cancelAnimationFrame, !ie) for (var e = ["ms", "moz", "webkit", "o"], n = 0; n < e.length; n++) {
        var r = e[n];
        ie = t["".concat(r, "RequestAnimationFrame")], ae = t["".concat(r, "CancelAnimationFrame")] || t["".concat(r, "CancelRequestAnimationFrame")];
      }
      ie || (ie = function ie(t) {
        var e = Date.now(),
            n = Math.max(0, 16 - (e - ue)),
            r = setTimeout(function () {
          t(e + n);
        }, n);
        return ue = e + n, r;
      }, ae = function ae(t) {
        return clearTimeout(t);
      });
    }
  };
  oe["default"] = se;
  var le = {};

  function ce(t) {
    return (ce = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(le, "__esModule", {
    value: !0
  }), le.warnOnce = function (t, e) {
    var n = !1;
    return function () {
      return n || (he["default"].window.console.warn(e), n = !0), t.apply(this, arguments);
    };
  }, le.copyAction = function (t, e) {
    return t.name = e.name, t.axis = e.axis, t.edges = e.edges, t;
  }, Object.defineProperty(le, "win", {
    enumerable: !0,
    get: function get() {
      return he["default"];
    }
  }), Object.defineProperty(le, "browser", {
    enumerable: !0,
    get: function get() {
      return ge["default"];
    }
  }), Object.defineProperty(le, "clone", {
    enumerable: !0,
    get: function get() {
      return be["default"];
    }
  }), Object.defineProperty(le, "extend", {
    enumerable: !0,
    get: function get() {
      return me["default"];
    }
  }), Object.defineProperty(le, "getOriginXY", {
    enumerable: !0,
    get: function get() {
      return Oe["default"];
    }
  }), Object.defineProperty(le, "hypot", {
    enumerable: !0,
    get: function get() {
      return we["default"];
    }
  }), Object.defineProperty(le, "normalizeListeners", {
    enumerable: !0,
    get: function get() {
      return _e["default"];
    }
  }), Object.defineProperty(le, "raf", {
    enumerable: !0,
    get: function get() {
      return Pe["default"];
    }
  }), le.rect = le.pointer = le.is = le.dom = le.arr = void 0;
  var fe = je(S);
  le.arr = fe;
  var pe = je($);
  le.dom = pe;
  var de = je(w);
  le.is = de;
  var ve = je(zt);
  le.pointer = ve;
  var ye = je($t);
  le.rect = ye;

  var he = xe(O),
      ge = xe(C),
      be = xe(V),
      me = xe(ct),
      Oe = xe(re),
      we = xe(Et),
      _e = xe(ft),
      Pe = xe(oe);

  function xe(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function Se() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Se = function Se() {
      return t;
    }, t;
  }

  function je(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== ce(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Se();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  var Me = {};

  function ke(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Ee(t, e, n) {
    return e && ke(t.prototype, e), n && ke(t, n), t;
  }

  function Te(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(Me, "__esModule", {
    value: !0
  }), Me["default"] = Me.BaseEvent = void 0;

  var De = function () {
    function e(t) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, e), Te(this, "type", void 0), Te(this, "target", void 0), Te(this, "currentTarget", void 0), Te(this, "interactable", void 0), Te(this, "_interaction", void 0), Te(this, "timeStamp", void 0), Te(this, "immediatePropagationStopped", !1), Te(this, "propagationStopped", !1), this._interaction = t;
    }

    return Ee(e, [{
      key: "interaction",
      get: function get() {
        return this._interaction._proxy;
      }
    }]), Ee(e, [{
      key: "preventDefault",
      value: function value() {}
    }, {
      key: "stopPropagation",
      value: function value() {
        this.propagationStopped = !0;
      }
    }, {
      key: "stopImmediatePropagation",
      value: function value() {
        this.immediatePropagationStopped = this.propagationStopped = !0;
      }
    }]), e;
  }(),
      Ie = Me.BaseEvent = De;

  Me["default"] = Ie;
  var ze = {};
  Object.defineProperty(ze, "__esModule", {
    value: !0
  }), ze["default"] = ze.defaults = void 0;
  var Ae = {
    base: {
      preventDefault: "auto",
      deltaSource: "page"
    },
    perAction: {
      enabled: !1,
      origin: {
        x: 0,
        y: 0
      }
    },
    actions: {}
  },
      Ce = ze.defaults = Ae;
  ze["default"] = Ce;
  var We = {};
  Object.defineProperty(We, "__esModule", {
    value: !0
  }), We["default"] = We.InteractEvent = void 0;
  var Re = Le(ct),
      Fe = Le(re),
      Xe = Le(Et),
      Ye = Le(Me),
      Ne = Le(ze);

  function Le(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function Be(t) {
    return (Be = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function Ve(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function qe(t) {
    return (qe = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function Ue(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function Ge(t, e) {
    return (Ge = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function He(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var Ke = function () {
    function g(t, e, n, r, o, i, a) {
      var u, s, l;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, g), s = this, u = !(l = qe(g).call(this, t)) || "object" !== Be(l) && "function" != typeof l ? Ue(s) : l, He(Ue(u), "target", void 0), He(Ue(u), "currentTarget", void 0), He(Ue(u), "relatedTarget", null), He(Ue(u), "screenX", void 0), He(Ue(u), "screenY", void 0), He(Ue(u), "button", void 0), He(Ue(u), "buttons", void 0), He(Ue(u), "ctrlKey", void 0), He(Ue(u), "shiftKey", void 0), He(Ue(u), "altKey", void 0), He(Ue(u), "metaKey", void 0), He(Ue(u), "page", void 0), He(Ue(u), "client", void 0), He(Ue(u), "delta", void 0), He(Ue(u), "rect", void 0), He(Ue(u), "x0", void 0), He(Ue(u), "y0", void 0), He(Ue(u), "t0", void 0), He(Ue(u), "dt", void 0), He(Ue(u), "duration", void 0), He(Ue(u), "clientX0", void 0), He(Ue(u), "clientY0", void 0), He(Ue(u), "velocity", void 0), He(Ue(u), "speed", void 0), He(Ue(u), "swipe", void 0), He(Ue(u), "timeStamp", void 0), He(Ue(u), "dragEnter", void 0), He(Ue(u), "dragLeave", void 0), He(Ue(u), "axes", void 0), He(Ue(u), "preEnd", void 0), o = o || t.element;
      var c = t.interactable,
          f = (c && c.options || Ne["default"]).deltaSource,
          p = (0, Fe["default"])(c, o, n),
          d = "start" === r,
          v = "end" === r,
          y = d ? Ue(u) : t.prevEvent,
          h = d ? t.coords.start : v ? {
        page: y.page,
        client: y.client,
        timeStamp: t.coords.cur.timeStamp
      } : t.coords.cur;
      return u.page = (0, Re["default"])({}, h.page), u.client = (0, Re["default"])({}, h.client), u.rect = (0, Re["default"])({}, t.rect), u.timeStamp = h.timeStamp, v || (u.page.x -= p.x, u.page.y -= p.y, u.client.x -= p.x, u.client.y -= p.y), u.ctrlKey = e.ctrlKey, u.altKey = e.altKey, u.shiftKey = e.shiftKey, u.metaKey = e.metaKey, u.button = e.button, u.buttons = e.buttons, u.target = o, u.currentTarget = o, u.preEnd = i, u.type = a || n + (r || ""), u.interactable = c, u.t0 = d ? t.pointers[t.pointers.length - 1].downTime : y.t0, u.x0 = t.coords.start.page.x - p.x, u.y0 = t.coords.start.page.y - p.y, u.clientX0 = t.coords.start.client.x - p.x, u.clientY0 = t.coords.start.client.y - p.y, u.delta = d || v ? {
        x: 0,
        y: 0
      } : {
        x: u[f].x - y[f].x,
        y: u[f].y - y[f].y
      }, u.dt = t.coords.delta.timeStamp, u.duration = u.timeStamp - u.t0, u.velocity = (0, Re["default"])({}, t.coords.velocity[f]), u.speed = (0, Xe["default"])(u.velocity.x, u.velocity.y), u.swipe = v || "inertiastart" === r ? u.getSwipe() : null, u;
    }

    var t, e, n;
    return function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && Ge(t, e);
    }(g, Ye["default"]), t = g, (e = [{
      key: "getSwipe",
      value: function value() {
        var t = this._interaction;
        if (t.prevEvent.speed < 600 || 150 < this.timeStamp - t.prevEvent.timeStamp) return null;
        var e = 180 * Math.atan2(t.prevEvent.velocityY, t.prevEvent.velocityX) / Math.PI;
        e < 0 && (e += 360);
        var n = 112.5 <= e && e < 247.5,
            r = 202.5 <= e && e < 337.5;
        return {
          up: r,
          down: !r && 22.5 <= e && e < 157.5,
          left: n,
          right: !n && (292.5 <= e || e < 67.5),
          angle: e,
          speed: t.prevEvent.speed,
          velocity: {
            x: t.prevEvent.velocityX,
            y: t.prevEvent.velocityY
          }
        };
      }
    }, {
      key: "preventDefault",
      value: function value() {}
    }, {
      key: "stopImmediatePropagation",
      value: function value() {
        this.immediatePropagationStopped = this.propagationStopped = !0;
      }
    }, {
      key: "stopPropagation",
      value: function value() {
        this.propagationStopped = !0;
      }
    }, {
      key: "pageX",
      get: function get() {
        return this.page.x;
      },
      set: function set(t) {
        this.page.x = t;
      }
    }, {
      key: "pageY",
      get: function get() {
        return this.page.y;
      },
      set: function set(t) {
        this.page.y = t;
      }
    }, {
      key: "clientX",
      get: function get() {
        return this.client.x;
      },
      set: function set(t) {
        this.client.x = t;
      }
    }, {
      key: "clientY",
      get: function get() {
        return this.client.y;
      },
      set: function set(t) {
        this.client.y = t;
      }
    }, {
      key: "dx",
      get: function get() {
        return this.delta.x;
      },
      set: function set(t) {
        this.delta.x = t;
      }
    }, {
      key: "dy",
      get: function get() {
        return this.delta.y;
      },
      set: function set(t) {
        this.delta.y = t;
      }
    }, {
      key: "velocityX",
      get: function get() {
        return this.velocity.x;
      },
      set: function set(t) {
        this.velocity.x = t;
      }
    }, {
      key: "velocityY",
      get: function get() {
        return this.velocity.y;
      },
      set: function set(t) {
        this.velocity.y = t;
      }
    }]) && Ve(t.prototype, e), n && Ve(t, n), g;
  }(),
      $e = We.InteractEvent = Ke;

  We["default"] = $e;
  var Ze = {};

  function Je(t) {
    return (Je = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Ze, "__esModule", {
    value: !0
  }), Ze["default"] = void 0;
  var Qe,
      tn = an(S),
      en = an($),
      nn = (Qe = ct) && Qe.__esModule ? Qe : {
    "default": Qe
  },
      rn = an(w);

  function on() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return on = function on() {
      return t;
    }, t;
  }

  function an(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Je(t) && "function" != typeof t) return {
      "default": t
    };
    var e = on();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function un(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function sn(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var ln = function () {
    function e(t) {
      var a = this;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, e), this.scope = t, sn(this, "list", []), sn(this, "selectorMap", {}), t.addListeners({
        "interactable:unset": function interactableUnset(t) {
          var e = t.interactable,
              n = e.target,
              r = e._context,
              o = rn.string(n) ? a.selectorMap[n] : n[a.scope.id],
              i = o.findIndex(function (t) {
            return t.context === r;
          });
          o[i] && (o[i].context = null, o[i].interactable = null), o.splice(i, 1);
        }
      });
    }

    var t, n, r;
    return t = e, (n = [{
      key: "new",
      value: function value(t, e) {
        e = (0, nn["default"])(e || {}, {
          actions: this.scope.actions
        });
        var n = new this.scope.Interactable(t, e, this.scope.document, this.scope.events),
            r = {
          context: n._context,
          interactable: n
        };
        return this.scope.addDocument(n._doc), this.list.push(n), rn.string(t) ? (this.selectorMap[t] || (this.selectorMap[t] = []), this.selectorMap[t].push(r)) : (n.target[this.scope.id] || Object.defineProperty(t, this.scope.id, {
          value: [],
          configurable: !0
        }), t[this.scope.id].push(r)), this.scope.fire("interactable:new", {
          target: t,
          options: e,
          interactable: n,
          win: this.scope._win
        }), n;
      }
    }, {
      key: "get",
      value: function value(e, t) {
        var n = t && t.context || this.scope.document,
            r = rn.string(e),
            o = r ? this.selectorMap[e] : e[this.scope.id];
        if (!o) return null;
        var i = tn.find(o, function (t) {
          return t.context === n && (r || t.interactable.inContext(e));
        });
        return i && i.interactable;
      }
    }, {
      key: "forEachMatch",
      value: function value(t, e) {
        for (var n = 0; n < this.list.length; n++) {
          var r = this.list[n],
              o = void 0;
          if ((rn.string(r.target) ? rn.element(t) && en.matchesSelector(t, r.target) : t === r.target) && r.inContext(t) && (o = e(r)), void 0 !== o) return o;
        }
      }
    }]) && un(t.prototype, n), r && un(t, r), e;
  }();

  Ze["default"] = ln;
  var cn = {};

  function fn(t) {
    return (fn = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(cn, "__esModule", {
    value: !0
  }), cn["default"] = cn.FakeEvent = void 0;
  var pn = On(S),
      dn = On($),
      vn = bn(ct),
      yn = On(w),
      hn = bn(Tt),
      gn = On(zt);

  function bn(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function mn() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return mn = function mn() {
      return t;
    }, t;
  }

  function On(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== fn(t) && "function" != typeof t) return {
      "default": t
    };
    var e = mn();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function wn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function _n(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if (!(Symbol.iterator in Object(t) || "[object Arguments]" === Object.prototype.toString.call(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, u = t[Symbol.iterator](); !(r = (a = u.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == u["return"] || u["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance");
    }();
  }

  var Pn = function () {
    function o(t) {
      var e, n, r;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, o), this.originalEvent = t, r = void 0, (n = "currentTarget") in (e = this) ? Object.defineProperty(e, n, {
        value: r,
        enumerable: !0,
        configurable: !0,
        writable: !0
      }) : e[n] = r, (0, hn["default"])(this, t);
    }

    var t, e, n;
    return t = o, (e = [{
      key: "preventOriginalDefault",
      value: function value() {
        this.originalEvent.preventDefault();
      }
    }, {
      key: "stopPropagation",
      value: function value() {
        this.originalEvent.stopPropagation();
      }
    }, {
      key: "stopImmediatePropagation",
      value: function value() {
        this.originalEvent.stopImmediatePropagation();
      }
    }]) && wn(t.prototype, e), n && wn(t, n), o;
  }();

  function xn(t) {
    if (!yn.object(t)) return {
      capture: !!t,
      passive: !1
    };
    var e = (0, vn["default"])({}, t);
    return e.capture = !!t.capture, e.passive = !!t.passive, e;
  }

  cn.FakeEvent = Pn;
  var Sn = {
    id: "events",
    install: function install(t) {
      var f = [],
          b = {},
          c = [],
          p = {
        add: d,
        remove: g,
        addDelegate: function addDelegate(e, n, t, r, o) {
          var i = xn(o);

          if (!b[t]) {
            b[t] = [];

            for (var a = 0; a < c.length; a++) {
              var u = c[a];
              d(u, t, m), d(u, t, O, !0);
            }
          }

          var s = b[t],
              l = pn.find(s, function (t) {
            return t.selector === e && t.context === n;
          });
          l || (l = {
            selector: e,
            context: n,
            listeners: []
          }, s.push(l));
          l.listeners.push([r, i]);
        },
        removeDelegate: function removeDelegate(t, e, n, r, o) {
          var i,
              a = xn(o),
              u = b[n],
              s = !1;
          if (!u) return;

          for (i = u.length - 1; 0 <= i; i--) {
            var l = u[i];

            if (l.selector === t && l.context === e) {
              for (var c = l.listeners, f = c.length - 1; 0 <= f; f--) {
                var p = _n(c[f], 2),
                    d = p[0],
                    v = p[1],
                    y = v.capture,
                    h = v.passive;

                if (d === r && y === a.capture && h === a.passive) {
                  c.splice(f, 1), c.length || (u.splice(i, 1), g(e, n, m), g(e, n, O, !0)), s = !0;
                  break;
                }
              }

              if (s) break;
            }
          }
        },
        delegateListener: m,
        delegateUseCapture: O,
        delegatedEvents: b,
        documents: c,
        targets: f,
        supportsOptions: !1,
        supportsPassive: !1
      };

      function d(e, t, n, r) {
        var o = xn(r),
            i = pn.find(f, function (t) {
          return t.eventTarget === e;
        });
        i || (i = {
          eventTarget: e,
          events: {}
        }, f.push(i)), i.events[t] || (i.events[t] = []), e.addEventListener && !pn.contains(i.events[t], n) && (e.addEventListener(t, n, p.supportsOptions ? o : o.capture), i.events[t].push(n));
      }

      function g(e, t, n, r) {
        var o = xn(r),
            i = pn.findIndex(f, function (t) {
          return t.eventTarget === e;
        }),
            a = f[i];
        if (a && a.events) if ("all" !== t) {
          var u = !1,
              s = a.events[t];

          if (s) {
            if ("all" === n) {
              for (var l = s.length - 1; 0 <= l; l--) {
                g(e, t, s[l], o);
              }

              return;
            }

            for (var c = 0; c < s.length; c++) {
              if (s[c] === n) {
                e.removeEventListener(t, n, p.supportsOptions ? o : o.capture), s.splice(c, 1), 0 === s.length && (delete a.events[t], u = !0);
                break;
              }
            }
          }

          u && !Object.keys(a.events).length && f.splice(i, 1);
        } else for (t in a.events) {
          a.events.hasOwnProperty(t) && g(e, t, "all");
        }
      }

      function m(t, e) {
        for (var n = xn(e), r = new Pn(t), o = b[t.type], i = _n(gn.getEventTargets(t), 1)[0], a = i; yn.element(a);) {
          for (var u = 0; u < o.length; u++) {
            var s = o[u],
                l = s.selector,
                c = s.context;

            if (dn.matchesSelector(a, l) && dn.nodeContains(c, i) && dn.nodeContains(c, a)) {
              var f = s.listeners;
              r.currentTarget = a;

              for (var p = 0; p < f.length; p++) {
                var d = _n(f[p], 2),
                    v = d[0],
                    y = d[1],
                    h = y.capture,
                    g = y.passive;

                h === n.capture && g === n.passive && v(r);
              }
            }
          }

          a = dn.parentNode(a);
        }
      }

      function O(t) {
        return m.call(this, t, !0);
      }

      return t.document.createElement("div").addEventListener("test", null, {
        get capture() {
          return p.supportsOptions = !0;
        },

        get passive() {
          return p.supportsPassive = !0;
        }

      }), t.events = p;
    }
  };
  cn["default"] = Sn;
  var jn = {};
  Object.defineProperty(jn, "__esModule", {
    value: !0
  }), jn["default"] = jn.PointerInfo = void 0;

  function Mn(t, e, n, r, o) {
    !function (t, e) {
      if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
    }(this, Mn), this.id = t, this.pointer = e, this.event = n, this.downTime = r, this.downTarget = o;
  }

  var kn = jn.PointerInfo = Mn;
  jn["default"] = kn;
  var En = {};

  function Tn(t) {
    return (Tn = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(En, "__esModule", {
    value: !0
  }), Object.defineProperty(En, "PointerInfo", {
    enumerable: !0,
    get: function get() {
      return Rn["default"];
    }
  }), En["default"] = En.Interaction = En._ProxyMethods = En._ProxyValues = void 0;

  var Dn,
      In,
      zn,
      An,
      Cn = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Tn(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Xn();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(le),
      Wn = Fn(We),
      Rn = Fn(jn);

  function Fn(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function Xn() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Xn = function Xn() {
      return t;
    }, t;
  }

  function Yn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Nn(t, e, n) {
    return e && Yn(t.prototype, e), n && Yn(t, n), t;
  }

  function Ln(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  En._ProxyValues = Dn, (In = Dn || (En._ProxyValues = Dn = {})).interactable = "", In.element = "", In.prepared = "", In.pointerIsDown = "", In.pointerWasMoved = "", In._proxy = "", En._ProxyMethods = zn, (An = zn || (En._ProxyMethods = zn = {})).start = "", An.move = "", An.end = "", An.stop = "", An.interacting = "";

  var Bn = 0,
      Vn = function () {
    function l(t) {
      var e = this,
          n = t.pointerType,
          r = t.scopeFire;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, l), Ln(this, "interactable", null), Ln(this, "element", null), Ln(this, "rect", void 0), Ln(this, "_rects", void 0), Ln(this, "edges", void 0), Ln(this, "_scopeFire", void 0), Ln(this, "prepared", {
        name: null,
        axis: null,
        edges: null
      }), Ln(this, "pointerType", void 0), Ln(this, "pointers", []), Ln(this, "downEvent", null), Ln(this, "downPointer", {}), Ln(this, "_latestPointer", {
        pointer: null,
        event: null,
        eventTarget: null
      }), Ln(this, "prevEvent", null), Ln(this, "pointerIsDown", !1), Ln(this, "pointerWasMoved", !1), Ln(this, "_interacting", !1), Ln(this, "_ending", !1), Ln(this, "_stopped", !0), Ln(this, "_proxy", null), Ln(this, "simulation", null), Ln(this, "doMove", Cn.warnOnce(function (t) {
        this.move(t);
      }, "The interaction.doMove() method has been renamed to interaction.move()")), Ln(this, "coords", {
        start: Cn.pointer.newCoords(),
        prev: Cn.pointer.newCoords(),
        cur: Cn.pointer.newCoords(),
        delta: Cn.pointer.newCoords(),
        velocity: Cn.pointer.newCoords()
      }), Ln(this, "_id", Bn++), this._scopeFire = r, this.pointerType = n;
      var o = this;
      this._proxy = {};

      function i(t) {
        Object.defineProperty(e._proxy, t, {
          get: function get() {
            return o[t];
          }
        });
      }

      for (var a in Dn) {
        i(a);
      }

      function u(t) {
        Object.defineProperty(e._proxy, t, {
          value: function value() {
            return o[t].apply(o, arguments);
          }
        });
      }

      for (var s in zn) {
        u(s);
      }

      this._scopeFire("interactions:new", {
        interaction: this
      });
    }

    return Nn(l, [{
      key: "pointerMoveTolerance",
      get: function get() {
        return 1;
      }
    }]), Nn(l, [{
      key: "pointerDown",
      value: function value(t, e, n) {
        var r = this.updatePointer(t, e, n, !0),
            o = this.pointers[r];

        this._scopeFire("interactions:down", {
          pointer: t,
          event: e,
          eventTarget: n,
          pointerIndex: r,
          pointerInfo: o,
          type: "down",
          interaction: this
        });
      }
    }, {
      key: "start",
      value: function value(t, e, n) {
        return !(this.interacting() || !this.pointerIsDown || this.pointers.length < ("gesture" === t.name ? 2 : 1) || !e.options[t.name].enabled) && (Cn.copyAction(this.prepared, t), this.interactable = e, this.element = n, this.rect = e.getRect(n), this.edges = this.prepared.edges ? Cn.extend({}, this.prepared.edges) : {
          left: !0,
          right: !0,
          top: !0,
          bottom: !0
        }, this._stopped = !1, this._interacting = this._doPhase({
          interaction: this,
          event: this.downEvent,
          phase: "start"
        }) && !this._stopped, this._interacting);
      }
    }, {
      key: "pointerMove",
      value: function value(t, e, n) {
        this.simulation || this.modification && this.modification.endResult || this.updatePointer(t, e, n, !1);
        var r,
            o,
            i = this.coords.cur.page.x === this.coords.prev.page.x && this.coords.cur.page.y === this.coords.prev.page.y && this.coords.cur.client.x === this.coords.prev.client.x && this.coords.cur.client.y === this.coords.prev.client.y;
        this.pointerIsDown && !this.pointerWasMoved && (r = this.coords.cur.client.x - this.coords.start.client.x, o = this.coords.cur.client.y - this.coords.start.client.y, this.pointerWasMoved = Cn.hypot(r, o) > this.pointerMoveTolerance);
        var a = this.getPointerIndex(t),
            u = {
          pointer: t,
          pointerIndex: a,
          pointerInfo: this.pointers[a],
          event: e,
          type: "move",
          eventTarget: n,
          dx: r,
          dy: o,
          duplicate: i,
          interaction: this
        };
        i || Cn.pointer.setCoordVelocity(this.coords.velocity, this.coords.delta), this._scopeFire("interactions:move", u), i || this.simulation || (this.interacting() && (u.type = null, this.move(u)), this.pointerWasMoved && Cn.pointer.copyCoords(this.coords.prev, this.coords.cur));
      }
    }, {
      key: "move",
      value: function value(t) {
        t && t.event || Cn.pointer.setZeroCoords(this.coords.delta), (t = Cn.extend({
          pointer: this._latestPointer.pointer,
          event: this._latestPointer.event,
          eventTarget: this._latestPointer.eventTarget,
          interaction: this
        }, t || {})).phase = "move", this._doPhase(t);
      }
    }, {
      key: "pointerUp",
      value: function value(t, e, n, r) {
        var o = this.getPointerIndex(t);
        -1 === o && (o = this.updatePointer(t, e, n, !1));
        var i = /cancel$/i.test(e.type) ? "cancel" : "up";
        this._scopeFire("interactions:".concat(i), {
          pointer: t,
          pointerIndex: o,
          pointerInfo: this.pointers[o],
          event: e,
          eventTarget: n,
          type: i,
          curEventTarget: r,
          interaction: this
        }), this.simulation || this.end(e), this.pointerIsDown = !1, this.removePointer(t, e);
      }
    }, {
      key: "documentBlur",
      value: function value(t) {
        this.end(t), this._scopeFire("interactions:blur", {
          event: t,
          type: "blur",
          interaction: this
        });
      }
    }, {
      key: "end",
      value: function value(t) {
        var e;
        this._ending = !0, t = t || this._latestPointer.event, this.interacting() && (e = this._doPhase({
          event: t,
          interaction: this,
          phase: "end"
        })), !(this._ending = !1) === e && this.stop();
      }
    }, {
      key: "currentAction",
      value: function value() {
        return this._interacting ? this.prepared.name : null;
      }
    }, {
      key: "interacting",
      value: function value() {
        return this._interacting;
      }
    }, {
      key: "stop",
      value: function value() {
        this._scopeFire("interactions:stop", {
          interaction: this
        }), this.interactable = this.element = null, this._interacting = !1, this._stopped = !0, this.prepared.name = this.prevEvent = null;
      }
    }, {
      key: "getPointerIndex",
      value: function value(t) {
        var e = Cn.pointer.getPointerId(t);
        return "mouse" === this.pointerType || "pen" === this.pointerType ? this.pointers.length - 1 : Cn.arr.findIndex(this.pointers, function (t) {
          return t.id === e;
        });
      }
    }, {
      key: "getPointerInfo",
      value: function value(t) {
        return this.pointers[this.getPointerIndex(t)];
      }
    }, {
      key: "updatePointer",
      value: function value(t, e, n, r) {
        var o = Cn.pointer.getPointerId(t),
            i = this.getPointerIndex(t),
            a = this.pointers[i];
        return r = !1 !== r && (r || /(down|start)$/i.test(e.type)), a ? a.pointer = t : (a = new Rn["default"](o, t, e, null, null), i = this.pointers.length, this.pointers.push(a)), Cn.pointer.setCoords(this.coords.cur, this.pointers.map(function (t) {
          return t.pointer;
        }), this._now()), Cn.pointer.setCoordDeltas(this.coords.delta, this.coords.prev, this.coords.cur), r && (this.pointerIsDown = !0, a.downTime = this.coords.cur.timeStamp, a.downTarget = n, Cn.pointer.pointerExtend(this.downPointer, t), this.interacting() || (Cn.pointer.copyCoords(this.coords.start, this.coords.cur), Cn.pointer.copyCoords(this.coords.prev, this.coords.cur), this.downEvent = e, this.pointerWasMoved = !1)), this._updateLatestPointer(t, e, n), this._scopeFire("interactions:update-pointer", {
          pointer: t,
          event: e,
          eventTarget: n,
          down: r,
          pointerInfo: a,
          pointerIndex: i,
          interaction: this
        }), i;
      }
    }, {
      key: "removePointer",
      value: function value(t, e) {
        var n = this.getPointerIndex(t);

        if (-1 !== n) {
          var r = this.pointers[n];
          this._scopeFire("interactions:remove-pointer", {
            pointer: t,
            event: e,
            eventTarget: null,
            pointerIndex: n,
            pointerInfo: r,
            interaction: this
          }), this.pointers.splice(n, 1);
        }
      }
    }, {
      key: "_updateLatestPointer",
      value: function value(t, e, n) {
        this._latestPointer.pointer = t, this._latestPointer.event = e, this._latestPointer.eventTarget = n;
      }
    }, {
      key: "destroy",
      value: function value() {
        this._latestPointer.pointer = null, this._latestPointer.event = null, this._latestPointer.eventTarget = null;
      }
    }, {
      key: "_createPreparedEvent",
      value: function value(t, e, n, r) {
        return new Wn["default"](this, t, this.prepared.name, e, this.element, n, r);
      }
    }, {
      key: "_fireEvent",
      value: function value(t) {
        this.interactable.fire(t), (!this.prevEvent || t.timeStamp >= this.prevEvent.timeStamp) && (this.prevEvent = t);
      }
    }, {
      key: "_doPhase",
      value: function value(t) {
        var e = t.event,
            n = t.phase,
            r = t.preEnd,
            o = t.type,
            i = this.rect;
        if (i && "move" === n && (Cn.rect.addEdges(this.edges, i, this.coords.delta[this.interactable.options.deltaSource]), i.width = i.right - i.left, i.height = i.bottom - i.top), !1 === this._scopeFire("interactions:before-action-".concat(n), t)) return !1;

        var a = t.iEvent = this._createPreparedEvent(e, n, r, o);

        return this._scopeFire("interactions:action-".concat(n), t), "start" === n && (this.prevEvent = a), this._fireEvent(a), this._scopeFire("interactions:after-action-".concat(n), t), !0;
      }
    }, {
      key: "_now",
      value: function value() {
        return Date.now();
      }
    }]), l;
  }(),
      qn = En.Interaction = Vn;

  En["default"] = qn;
  var Un = {};

  function Gn(t) {
    return (Gn = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Un, "__esModule", {
    value: !0
  }), Un.install = Jn, Un["default"] = void 0;

  var Hn = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Gn(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Kn();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w);

  function Kn() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Kn = function Kn() {
      return t;
    }, t;
  }

  function $n(t) {
    return /^(always|never|auto)$/.test(t) ? (this.options.preventDefault = t, this) : Hn.bool(t) ? (this.options.preventDefault = t ? "always" : "never", this) : this.options.preventDefault;
  }

  function Zn(t) {
    var e = t.interaction,
        n = t.event;
    e.interactable && e.interactable.checkAndPreventDefault(n);
  }

  function Jn(r) {
    var t = r.Interactable;
    t.prototype.preventDefault = $n, t.prototype.checkAndPreventDefault = function (t) {
      return function (t, e, n) {
        var r = t.options.preventDefault;
        if ("never" !== r) if ("always" !== r) {
          if (e.events.supportsPassive && /^touch(start|move)$/.test(n.type)) {
            var o = (0, O.getWindow)(n.target).document,
                i = e.getDocOptions(o);
            if (!i || !i.events || !1 !== i.events.passive) return;
          }

          /^(mouse|pointer|touch)*(down|start)/i.test(n.type) || Hn.element(n.target) && (0, $.matchesSelector)(n.target, "input,select,textarea,[contenteditable=true],[contenteditable=true] *") || n.preventDefault();
        } else n.preventDefault();
      }(this, r, t);
    }, r.interactions.docEvents.push({
      type: "dragstart",
      listener: function listener(t) {
        for (var e = 0; e < r.interactions.list.length; e++) {
          var n = r.interactions.list[e];
          if (n.element && (n.element === t.target || (0, $.nodeContains)(n.element, t.target))) return void n.interactable.checkAndPreventDefault(t);
        }
      }
    });
  }

  var Qn = {
    id: "core/interactablePreventDefault",
    install: Jn,
    listeners: ["down", "move", "up", "cancel"].reduce(function (t, e) {
      return t["interactions:".concat(e)] = Zn, t;
    }, {})
  };
  Un["default"] = Qn;
  var tr = {};

  function er(t) {
    return (er = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(tr, "__esModule", {
    value: !0
  }), tr["default"] = void 0;

  var nr = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== er(t) && "function" != typeof t) return {
      "default": t
    };
    var e = rr();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }($);

  function rr() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return rr = function rr() {
      return t;
    }, t;
  }

  var or = {
    methodOrder: ["simulationResume", "mouseOrPen", "hasPointer", "idle"],
    search: function search(t) {
      for (var e = 0; e < or.methodOrder.length; e++) {
        var n;
        n = or.methodOrder[e];
        var r = or[n](t);
        if (r) return r;
      }

      return null;
    },
    simulationResume: function simulationResume(t) {
      var e = t.pointerType,
          n = t.eventType,
          r = t.eventTarget,
          o = t.scope;
      if (!/down|start/i.test(n)) return null;

      for (var i = 0; i < o.interactions.list.length; i++) {
        var a = o.interactions.list[i],
            u = r;
        if (a.simulation && a.simulation.allowResume && a.pointerType === e) for (; u;) {
          if (u === a.element) return a;
          u = nr.parentNode(u);
        }
      }

      return null;
    },
    mouseOrPen: function mouseOrPen(t) {
      var e,
          n = t.pointerId,
          r = t.pointerType,
          o = t.eventType,
          i = t.scope;
      if ("mouse" !== r && "pen" !== r) return null;

      for (var a = 0; a < i.interactions.list.length; a++) {
        var u = i.interactions.list[a];

        if (u.pointerType === r) {
          if (u.simulation && !ir(u, n)) continue;
          if (u.interacting()) return u;
          e = e || u;
        }
      }

      if (e) return e;

      for (var s = 0; s < i.interactions.list.length; s++) {
        var l = i.interactions.list[s];
        if (!(l.pointerType !== r || /down/i.test(o) && l.simulation)) return l;
      }

      return null;
    },
    hasPointer: function hasPointer(t) {
      for (var e = t.pointerId, n = t.scope, r = 0; r < n.interactions.list.length; r++) {
        var o = n.interactions.list[r];
        if (ir(o, e)) return o;
      }

      return null;
    },
    idle: function idle(t) {
      for (var e = t.pointerType, n = t.scope, r = 0; r < n.interactions.list.length; r++) {
        var o = n.interactions.list[r];

        if (1 === o.pointers.length) {
          var i = o.interactable;
          if (i && (!i.options.gesture || !i.options.gesture.enabled)) continue;
        } else if (2 <= o.pointers.length) continue;

        if (!o.interacting() && e === o.pointerType) return o;
      }

      return null;
    }
  };

  function ir(t, e) {
    return t.pointers.some(function (t) {
      return t.id === e;
    });
  }

  var ar = or;
  tr["default"] = ar;
  var ur = {};
  Object.defineProperty(ur, "__esModule", {
    value: !0
  }), ur["default"] = void 0;

  var sr,
      lr = (sr = Me) && sr.__esModule ? sr : {
    "default": sr
  },
      cr = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== pr(t) && "function" != typeof t) return {
      "default": t
    };
    var e = fr();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(S);

  function fr() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return fr = function fr() {
      return t;
    }, t;
  }

  function pr(t) {
    return (pr = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function dr(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function vr(t) {
    return (vr = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function yr(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function hr(t, e) {
    return (hr = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function gr(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var br = function () {
    function l(t, e, n) {
      var r, o, i;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, l), o = this, r = !(i = vr(l).call(this, e._interaction)) || "object" !== pr(i) && "function" != typeof i ? yr(o) : i, gr(yr(r), "target", void 0), gr(yr(r), "dropzone", void 0), gr(yr(r), "dragEvent", void 0), gr(yr(r), "relatedTarget", void 0), gr(yr(r), "draggable", void 0), gr(yr(r), "timeStamp", void 0), gr(yr(r), "propagationStopped", !1), gr(yr(r), "immediatePropagationStopped", !1);
      var a = "dragleave" === n ? t.prev : t.cur,
          u = a.element,
          s = a.dropzone;
      return r.type = n, r.target = u, r.currentTarget = u, r.dropzone = s, r.dragEvent = e, r.relatedTarget = e.target, r.draggable = e.interactable, r.timeStamp = e.timeStamp, r;
    }

    var t, e, n;
    return function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && hr(t, e);
    }(l, lr["default"]), t = l, (e = [{
      key: "reject",
      value: function value() {
        var r = this,
            t = this._interaction.dropState;
        if ("dropactivate" === this.type || this.dropzone && t.cur.dropzone === this.dropzone && t.cur.element === this.target) if (t.prev.dropzone = this.dropzone, t.prev.element = this.target, t.rejected = !0, t.events.enter = null, this.stopImmediatePropagation(), "dropactivate" === this.type) {
          var e = t.activeDrops,
              n = cr.findIndex(e, function (t) {
            var e = t.dropzone,
                n = t.element;
            return e === r.dropzone && n === r.target;
          });
          t.activeDrops.splice(n, 1);
          var o = new l(t, this.dragEvent, "dropdeactivate");
          o.dropzone = this.dropzone, o.target = this.target, this.dropzone.fire(o);
        } else this.dropzone.fire(new l(t, this.dragEvent, "dragleave"));
      }
    }, {
      key: "preventDefault",
      value: function value() {}
    }, {
      key: "stopPropagation",
      value: function value() {
        this.propagationStopped = !0;
      }
    }, {
      key: "stopImmediatePropagation",
      value: function value() {
        this.immediatePropagationStopped = this.propagationStopped = !0;
      }
    }]) && dr(t.prototype, e), n && dr(t, n), l;
  }();

  ur["default"] = br;
  var mr = {};

  function Or(t) {
    return (Or = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(mr, "__esModule", {
    value: !0
  }), mr["default"] = void 0;
  Sr(k({})), Sr(m({}));

  var wr = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Or(t) && "function" != typeof t) return {
      "default": t
    };
    var e = xr();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(le),
      _r = Sr(v),
      Pr = Sr(ur);

  function xr() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return xr = function xr() {
      return t;
    }, t;
  }

  function Sr(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function jr(t, e) {
    for (var n = 0; n < t.slice().length; n++) {
      r = t.slice()[n];
      var r,
          o = r.dropzone,
          i = r.element;
      e.dropzone = o, e.target = i, o.fire(e), e.propagationStopped = e.immediatePropagationStopped = !1;
    }
  }

  function Mr(t, e) {
    for (var n = function (t, e) {
      for (var n = t.interactables, r = [], o = 0; o < n.list.length; o++) {
        var i = n.list[o];

        if (i.options.drop.enabled) {
          var a = i.options.drop.accept;
          if (!(wr.is.element(a) && a !== e || wr.is.string(a) && !wr.dom.matchesSelector(e, a) || wr.is.func(a) && !a({
            dropzone: i,
            draggableElement: e
          }))) for (var u = wr.is.string(i.target) ? i._context.querySelectorAll(i.target) : wr.is.array(i.target) ? i.target : [i.target], s = 0; s < u.length; s++) {
            var l;
            l = u[s];
            l !== e && r.push({
              dropzone: i,
              element: l
            });
          }
        }
      }

      return r;
    }(t, e), r = 0; r < n.length; r++) {
      var o;
      o = n[r];
      o.rect = o.dropzone.getRect(o.element);
    }

    return n;
  }

  function kr(t, e, n) {
    for (var r = t.dropState, o = t.interactable, i = t.element, a = [], u = 0; u < r.activeDrops.length; u++) {
      s = r.activeDrops[u];
      var s,
          l = s.dropzone,
          c = s.element,
          f = s.rect;
      a.push(l.dropCheck(e, n, o, i, c, f) ? c : null);
    }

    var p = wr.dom.indexOfDeepestElement(a);
    return r.activeDrops[p] || null;
  }

  function Er(t, e, n) {
    var r = t.dropState,
        o = {
      enter: null,
      leave: null,
      activate: null,
      deactivate: null,
      move: null,
      drop: null
    };
    return "dragstart" === n.type && (o.activate = new Pr["default"](r, n, "dropactivate"), o.activate.target = null, o.activate.dropzone = null), "dragend" === n.type && (o.deactivate = new Pr["default"](r, n, "dropdeactivate"), o.deactivate.target = null, o.deactivate.dropzone = null), r.rejected || (r.cur.element !== r.prev.element && (r.prev.dropzone && (o.leave = new Pr["default"](r, n, "dragleave"), n.dragLeave = o.leave.target = r.prev.element, n.prevDropzone = o.leave.dropzone = r.prev.dropzone), r.cur.dropzone && (o.enter = new Pr["default"](r, n, "dragenter"), n.dragEnter = r.cur.element, n.dropzone = r.cur.dropzone)), "dragend" === n.type && r.cur.dropzone && (o.drop = new Pr["default"](r, n, "drop"), n.dropzone = r.cur.dropzone, n.relatedTarget = r.cur.element), "dragmove" === n.type && r.cur.dropzone && (o.move = new Pr["default"](r, n, "dropmove"), (o.move.dragmove = n).dropzone = r.cur.dropzone)), o;
  }

  function Tr(t, e) {
    var n = t.dropState,
        r = n.activeDrops,
        o = n.cur,
        i = n.prev;
    e.leave && i.dropzone.fire(e.leave), e.move && o.dropzone.fire(e.move), e.enter && o.dropzone.fire(e.enter), e.drop && o.dropzone.fire(e.drop), e.deactivate && jr(r, e.deactivate), n.prev.dropzone = o.dropzone, n.prev.element = o.element;
  }

  function Dr(t, e) {
    var n = t.interaction,
        r = t.iEvent,
        o = t.event;

    if ("dragmove" === r.type || "dragend" === r.type) {
      var i = n.dropState;
      e.dynamicDrop && (i.activeDrops = Mr(e, n.element));
      var a = r,
          u = kr(n, a, o);
      i.rejected = i.rejected && !!u && u.dropzone === i.cur.dropzone && u.element === i.cur.element, i.cur.dropzone = u && u.dropzone, i.cur.element = u && u.element, i.events = Er(n, 0, a);
    }
  }

  var Ir = {
    id: "actions/drop",
    install: function install(e) {
      var t = e.actions,
          n = e.interactStatic,
          r = e.Interactable,
          o = e.defaults;
      e.usePlugin(_r["default"]), r.prototype.dropzone = function (t) {
        return function (t, e) {
          if (wr.is.object(e)) {
            if (t.options.drop.enabled = !1 !== e.enabled, e.listeners) {
              var n = wr.normalizeListeners(e.listeners),
                  r = Object.keys(n).reduce(function (t, e) {
                return t[/^(enter|leave)/.test(e) ? "drag".concat(e) : /^(activate|deactivate|move)/.test(e) ? "drop".concat(e) : e] = n[e], t;
              }, {});
              t.off(t.options.drop.listeners), t.on(r), t.options.drop.listeners = r;
            }

            return wr.is.func(e.ondrop) && t.on("drop", e.ondrop), wr.is.func(e.ondropactivate) && t.on("dropactivate", e.ondropactivate), wr.is.func(e.ondropdeactivate) && t.on("dropdeactivate", e.ondropdeactivate), wr.is.func(e.ondragenter) && t.on("dragenter", e.ondragenter), wr.is.func(e.ondragleave) && t.on("dragleave", e.ondragleave), wr.is.func(e.ondropmove) && t.on("dropmove", e.ondropmove), /^(pointer|center)$/.test(e.overlap) ? t.options.drop.overlap = e.overlap : wr.is.number(e.overlap) && (t.options.drop.overlap = Math.max(Math.min(1, e.overlap), 0)), "accept" in e && (t.options.drop.accept = e.accept), "checker" in e && (t.options.drop.checker = e.checker), t;
          }

          if (wr.is.bool(e)) return t.options.drop.enabled = e, t;
          return t.options.drop;
        }(this, t);
      }, r.prototype.dropCheck = function (t, e, n, r, o, i) {
        return function (t, e, n, r, o, i, a) {
          var u = !1;
          if (!(a = a || t.getRect(i))) return !!t.options.drop.checker && t.options.drop.checker(e, n, u, t, i, r, o);
          var s = t.options.drop.overlap;

          if ("pointer" === s) {
            var l = wr.getOriginXY(r, o, "drag"),
                c = wr.pointer.getPageXY(e);
            c.x += l.x, c.y += l.y;
            var f = c.x > a.left && c.x < a.right,
                p = c.y > a.top && c.y < a.bottom;
            u = f && p;
          }

          var d = r.getRect(o);

          if (d && "center" === s) {
            var v = d.left + d.width / 2,
                y = d.top + d.height / 2;
            u = v >= a.left && v <= a.right && y >= a.top && y <= a.bottom;
          }

          if (d && wr.is.number(s)) {
            var h = Math.max(0, Math.min(a.right, d.right) - Math.max(a.left, d.left)) * Math.max(0, Math.min(a.bottom, d.bottom) - Math.max(a.top, d.top)) / (d.width * d.height);
            u = s <= h;
          }

          t.options.drop.checker && (u = t.options.drop.checker(e, n, u, t, i, r, o));
          return u;
        }(this, t, e, n, r, o, i);
      }, n.dynamicDrop = function (t) {
        return wr.is.bool(t) ? (e.dynamicDrop = t, n) : e.dynamicDrop;
      }, wr.extend(t.phaselessTypes, {
        dragenter: !0,
        dragleave: !0,
        dropactivate: !0,
        dropdeactivate: !0,
        dropmove: !0,
        drop: !0
      }), t.methodDict.drop = "dropzone", e.dynamicDrop = !1, o.actions.drop = Ir.defaults;
    },
    listeners: {
      "interactions:before-action-start": function interactionsBeforeActionStart(t) {
        var e = t.interaction;
        "drag" === e.prepared.name && (e.dropState = {
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
          activeDrops: []
        });
      },
      "interactions:after-action-start": function interactionsAfterActionStart(t, e) {
        var n = t.interaction,
            r = (t.event, t.iEvent);

        if ("drag" === n.prepared.name) {
          var o = n.dropState;
          o.activeDrops = null, o.events = null, o.activeDrops = Mr(e, n.element), o.events = Er(n, 0, r), o.events.activate && (jr(o.activeDrops, o.events.activate), e.fire("actions/drop:start", {
            interaction: n,
            dragEvent: r
          }));
        }
      },
      "interactions:action-move": Dr,
      "interactions:action-end": Dr,
      "interactions:after-action-move": function interactionsAfterActionMove(t, e) {
        var n = t.interaction,
            r = t.iEvent;
        "drag" === n.prepared.name && (Tr(n, n.dropState.events), e.fire("actions/drop:move", {
          interaction: n,
          dragEvent: r
        }), n.dropState.events = {});
      },
      "interactions:after-action-end": function interactionsAfterActionEnd(t, e) {
        var n = t.interaction,
            r = t.iEvent;
        "drag" === n.prepared.name && (Tr(n, n.dropState.events), e.fire("actions/drop:end", {
          interaction: n,
          dragEvent: r
        }));
      },
      "interactions:stop": function interactionsStop(t) {
        var e = t.interaction;

        if ("drag" === e.prepared.name) {
          var n = e.dropState;
          n && (n.activeDrops = null, n.events = null, n.cur.dropzone = null, n.cur.element = null, n.prev.dropzone = null, n.prev.element = null, n.rejected = !1);
        }
      }
    },
    getActiveDrops: Mr,
    getDrop: kr,
    getDropEvents: Er,
    fireDropEvents: Tr,
    defaults: {
      enabled: !1,
      accept: null,
      overlap: "pointer"
    }
  },
      zr = Ir;
  mr["default"] = zr;
  var Ar = {};

  function Cr(t) {
    return (Cr = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Ar, "__esModule", {
    value: !0
  }), Ar["default"] = void 0;

  var Wr = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Cr(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Rr();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(le);

  function Rr() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Rr = function Rr() {
      return t;
    }, t;
  }

  function Fr(t) {
    var e = t.interaction,
        n = t.iEvent,
        r = t.phase;

    if ("gesture" === e.prepared.name) {
      var o = e.pointers.map(function (t) {
        return t.pointer;
      }),
          i = "start" === r,
          a = "end" === r,
          u = e.interactable.options.deltaSource;
      if (n.touches = [o[0], o[1]], i) n.distance = Wr.pointer.touchDistance(o, u), n.box = Wr.pointer.touchBBox(o), n.scale = 1, n.ds = 0, n.angle = Wr.pointer.touchAngle(o, u), n.da = 0, e.gesture.startDistance = n.distance, e.gesture.startAngle = n.angle;else if (a) {
        var s = e.prevEvent;
        n.distance = s.distance, n.box = s.box, n.scale = s.scale, n.ds = 0, n.angle = s.angle, n.da = 0;
      } else n.distance = Wr.pointer.touchDistance(o, u), n.box = Wr.pointer.touchBBox(o), n.scale = n.distance / e.gesture.startDistance, n.angle = Wr.pointer.touchAngle(o, u), n.ds = n.scale - e.gesture.scale, n.da = n.angle - e.gesture.angle;
      e.gesture.distance = n.distance, e.gesture.angle = n.angle, Wr.is.number(n.scale) && n.scale !== 1 / 0 && !isNaN(n.scale) && (e.gesture.scale = n.scale);
    }
  }

  var Xr = {
    id: "actions/gesture",
    before: ["actions/drag", "actions/resize"],
    install: function install(t) {
      var e = t.actions,
          n = t.Interactable,
          r = t.defaults;
      n.prototype.gesturable = function (t) {
        return Wr.is.object(t) ? (this.options.gesture.enabled = !1 !== t.enabled, this.setPerAction("gesture", t), this.setOnEvents("gesture", t), this) : Wr.is.bool(t) ? (this.options.gesture.enabled = t, this) : this.options.gesture;
      }, e.map.gesture = Xr, e.methodDict.gesture = "gesturable", r.actions.gesture = Xr.defaults;
    },
    listeners: {
      "interactions:action-start": Fr,
      "interactions:action-move": Fr,
      "interactions:action-end": Fr,
      "interactions:new": function interactionsNew(t) {
        t.interaction.gesture = {
          angle: 0,
          distance: 0,
          scale: 1,
          startAngle: 0,
          startDistance: 0
        };
      },
      "auto-start:check": function autoStartCheck(t) {
        if (!(t.interaction.pointers.length < 2)) {
          var e = t.interactable.options.gesture;
          if (e && e.enabled) return !(t.action = {
            name: "gesture"
          });
        }
      }
    },
    defaults: {},
    getCursor: function getCursor() {
      return "";
    }
  },
      Yr = Xr;
  Ar["default"] = Yr;
  var Nr = {};

  function Lr(t) {
    return (Lr = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Nr, "__esModule", {
    value: !0
  }), Nr["default"] = void 0;
  var Br,
      Vr = Hr($),
      qr = (Br = ct) && Br.__esModule ? Br : {
    "default": Br
  },
      Ur = Hr(w);

  function Gr() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Gr = function Gr() {
      return t;
    }, t;
  }

  function Hr(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Lr(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Gr();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function Kr(t, e, n, r, o, i, a) {
    if (!e) return !1;

    if (!0 === e) {
      var u = Ur.number(i.width) ? i.width : i.right - i.left,
          s = Ur.number(i.height) ? i.height : i.bottom - i.top;
      if (a = Math.min(a, ("left" === t || "right" === t ? u : s) / 2), u < 0 && ("left" === t ? t = "right" : "right" === t && (t = "left")), s < 0 && ("top" === t ? t = "bottom" : "bottom" === t && (t = "top")), "left" === t) return n.x < (0 <= u ? i.left : i.right) + a;
      if ("top" === t) return n.y < (0 <= s ? i.top : i.bottom) + a;
      if ("right" === t) return n.x > (0 <= u ? i.right : i.left) - a;
      if ("bottom" === t) return n.y > (0 <= s ? i.bottom : i.top) - a;
    }

    return !!Ur.element(r) && (Ur.element(e) ? e === r : Vr.matchesUpTo(r, e, o));
  }

  function $r(t) {
    var e = t.iEvent,
        n = t.interaction;

    if ("resize" === n.prepared.name && n.resizeAxes) {
      var r = e;
      n.interactable.options.resize.square ? ("y" === n.resizeAxes ? r.delta.x = r.delta.y : r.delta.y = r.delta.x, r.axes = "xy") : (r.axes = n.resizeAxes, "x" === n.resizeAxes ? r.delta.y = 0 : "y" === n.resizeAxes && (r.delta.x = 0));
    }
  }

  var Zr = {
    id: "actions/resize",
    before: ["actions/drag"],
    install: function install(e) {
      var t = e.actions,
          n = e.browser,
          r = e.Interactable,
          o = e.defaults;
      Zr.cursors = n.isIe9 ? {
        x: "e-resize",
        y: "s-resize",
        xy: "se-resize",
        top: "n-resize",
        left: "w-resize",
        bottom: "s-resize",
        right: "e-resize",
        topleft: "se-resize",
        bottomright: "se-resize",
        topright: "ne-resize",
        bottomleft: "ne-resize"
      } : {
        x: "ew-resize",
        y: "ns-resize",
        xy: "nwse-resize",
        top: "ns-resize",
        left: "ew-resize",
        bottom: "ns-resize",
        right: "ew-resize",
        topleft: "nwse-resize",
        bottomright: "nwse-resize",
        topright: "nesw-resize",
        bottomleft: "nesw-resize"
      }, Zr.defaultMargin = n.supportsTouch || n.supportsPointerEvent ? 20 : 10, r.prototype.resizable = function (t) {
        return function (t, e, n) {
          if (Ur.object(e)) return t.options.resize.enabled = !1 !== e.enabled, t.setPerAction("resize", e), t.setOnEvents("resize", e), Ur.string(e.axis) && /^x$|^y$|^xy$/.test(e.axis) ? t.options.resize.axis = e.axis : null === e.axis && (t.options.resize.axis = n.defaults.actions.resize.axis), Ur.bool(e.preserveAspectRatio) ? t.options.resize.preserveAspectRatio = e.preserveAspectRatio : Ur.bool(e.square) && (t.options.resize.square = e.square), t;
          if (Ur.bool(e)) return t.options.resize.enabled = e, t;
          return t.options.resize;
        }(this, t, e);
      }, t.map.resize = Zr, t.methodDict.resize = "resizable", o.actions.resize = Zr.defaults;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.resizeAxes = "xy";
      },
      "interactions:action-start": function interactionsActionStart(t) {
        !function (t) {
          var e = t.iEvent,
              n = t.interaction;

          if ("resize" === n.prepared.name && n.prepared.edges) {
            var r = e,
                o = n.rect;
            n._rects = {
              start: (0, qr["default"])({}, o),
              corrected: (0, qr["default"])({}, o),
              previous: (0, qr["default"])({}, o),
              delta: {
                left: 0,
                right: 0,
                width: 0,
                top: 0,
                bottom: 0,
                height: 0
              }
            }, r.edges = n.prepared.edges, r.rect = n._rects.corrected, r.deltaRect = n._rects.delta;
          }
        }(t), $r(t);
      },
      "interactions:action-move": function interactionsActionMove(t) {
        !function (t) {
          var e = t.iEvent,
              n = t.interaction;

          if ("resize" === n.prepared.name && n.prepared.edges) {
            var r = e,
                o = n.interactable.options.resize.invert,
                i = "reposition" === o || "negate" === o,
                a = n.rect,
                u = n._rects,
                s = u.start,
                l = u.corrected,
                c = u.delta,
                f = u.previous;

            if ((0, qr["default"])(f, l), i) {
              if ((0, qr["default"])(l, a), "reposition" === o) {
                if (l.top > l.bottom) {
                  var p = l.top;
                  l.top = l.bottom, l.bottom = p;
                }

                if (l.left > l.right) {
                  var d = l.left;
                  l.left = l.right, l.right = d;
                }
              }
            } else l.top = Math.min(a.top, s.bottom), l.bottom = Math.max(a.bottom, s.top), l.left = Math.min(a.left, s.right), l.right = Math.max(a.right, s.left);

            for (var v in l.width = l.right - l.left, l.height = l.bottom - l.top, l) {
              c[v] = l[v] - f[v];
            }

            r.edges = n.prepared.edges, r.rect = l, r.deltaRect = c;
          }
        }(t), $r(t);
      },
      "interactions:action-end": function interactionsActionEnd(t) {
        var e = t.iEvent,
            n = t.interaction;

        if ("resize" === n.prepared.name && n.prepared.edges) {
          var r = e;
          r.edges = n.prepared.edges, r.rect = n._rects.corrected, r.deltaRect = n._rects.delta;
        }
      },
      "auto-start:check": function autoStartCheck(t) {
        var e = t.interaction,
            n = t.interactable,
            r = t.element,
            o = t.rect,
            i = t.buttons;

        if (o) {
          var a = (0, qr["default"])({}, e.coords.cur.page),
              u = n.options.resize;

          if (u && u.enabled && (!e.pointerIsDown || !/mouse|pointer/.test(e.pointerType) || 0 != (i & u.mouseButtons))) {
            if (Ur.object(u.edges)) {
              var s = {
                left: !1,
                right: !1,
                top: !1,
                bottom: !1
              };

              for (var l in s) {
                s[l] = Kr(l, u.edges[l], a, e._latestPointer.eventTarget, r, o, u.margin || Zr.defaultMargin);
              }

              s.left = s.left && !s.right, s.top = s.top && !s.bottom, (s.left || s.right || s.top || s.bottom) && (t.action = {
                name: "resize",
                edges: s
              });
            } else {
              var c = "y" !== u.axis && a.x > o.right - Zr.defaultMargin,
                  f = "x" !== u.axis && a.y > o.bottom - Zr.defaultMargin;
              (c || f) && (t.action = {
                name: "resize",
                axes: (c ? "x" : "") + (f ? "y" : "")
              });
            }

            return !t.action && void 0;
          }
        }
      }
    },
    defaults: {
      square: !1,
      preserveAspectRatio: !1,
      axis: "xy",
      margin: NaN,
      edges: null,
      invert: "none"
    },
    cursors: null,
    getCursor: function getCursor(t) {
      var e = t.edges,
          n = t.axis,
          r = t.name,
          o = Zr.cursors,
          i = null;
      if (n) i = o[r + n];else if (e) {
        for (var a = "", u = ["top", "bottom", "left", "right"], s = 0; s < u.length; s++) {
          var l = u[s];
          e[l] && (a += l);
        }

        i = o[a];
      }
      return i;
    },
    defaultMargin: null
  },
      Jr = Zr;
  Nr["default"] = Jr;
  var Qr = {};
  Object.defineProperty(Qr, "__esModule", {
    value: !0
  }), Object.defineProperty(Qr, "drag", {
    enumerable: !0,
    get: function get() {
      return to["default"];
    }
  }), Object.defineProperty(Qr, "drop", {
    enumerable: !0,
    get: function get() {
      return eo["default"];
    }
  }), Object.defineProperty(Qr, "gesture", {
    enumerable: !0,
    get: function get() {
      return no["default"];
    }
  }), Object.defineProperty(Qr, "resize", {
    enumerable: !0,
    get: function get() {
      return ro["default"];
    }
  }), Qr["default"] = void 0;
  var to = oo(v),
      eo = oo(mr),
      no = oo(Ar),
      ro = oo(Nr);

  function oo(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var io = {
    id: "actions",
    install: function install(t) {
      t.usePlugin(no["default"]), t.usePlugin(ro["default"]), t.usePlugin(to["default"]), t.usePlugin(eo["default"]);
    }
  };
  Qr["default"] = io;
  var ao = {};
  Object.defineProperty(ao, "__esModule", {
    value: !0
  }), ao["default"] = void 0;
  ao["default"] = {};
  var uo = {};

  function so(t) {
    return (so = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(uo, "__esModule", {
    value: !0
  }), uo.getContainer = go, uo.getScroll = bo, uo.getScrollSize = function (t) {
    fo.window(t) && (t = window.document.body);
    return {
      x: t.scrollWidth,
      y: t.scrollHeight
    };
  }, uo.getScrollSizeDelta = function (t, e) {
    var n = t.interaction,
        r = t.element,
        o = n && n.interactable.options[n.prepared.name].autoScroll;
    if (!o || !o.enabled) return e(), {
      x: 0,
      y: 0
    };
    var i = go(o.container, n.interactable, r),
        a = bo(i);
    e();
    var u = bo(i);
    return {
      x: u.x - a.x,
      y: u.y - a.y
    };
  }, uo["default"] = void 0;
  var lo,
      co = yo($),
      fo = yo(w),
      po = (lo = oe) && lo.__esModule ? lo : {
    "default": lo
  };

  function vo() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return vo = function vo() {
      return t;
    }, t;
  }

  function yo(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== so(t) && "function" != typeof t) return {
      "default": t
    };
    var e = vo();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  var ho = {
    defaults: {
      enabled: !1,
      margin: 60,
      container: null,
      speed: 300
    },
    now: Date.now,
    interaction: null,
    i: 0,
    x: 0,
    y: 0,
    isScrolling: !1,
    prevTime: 0,
    margin: 0,
    speed: 0,
    start: function start(t) {
      ho.isScrolling = !0, po["default"].cancel(ho.i), (t.autoScroll = ho).interaction = t, ho.prevTime = ho.now(), ho.i = po["default"].request(ho.scroll);
    },
    stop: function stop() {
      ho.isScrolling = !1, ho.interaction && (ho.interaction.autoScroll = null), po["default"].cancel(ho.i);
    },
    scroll: function scroll() {
      var t = ho.interaction,
          e = t.interactable,
          n = t.element,
          r = t.prepared.name,
          o = e.options[r].autoScroll,
          i = go(o.container, e, n),
          a = ho.now(),
          u = (a - ho.prevTime) / 1e3,
          s = o.speed * u;

      if (1 <= s) {
        var l = {
          x: ho.x * s,
          y: ho.y * s
        };

        if (l.x || l.y) {
          var c = bo(i);
          fo.window(i) ? i.scrollBy(l.x, l.y) : i && (i.scrollLeft += l.x, i.scrollTop += l.y);
          var f = bo(i),
              p = {
            x: f.x - c.x,
            y: f.y - c.y
          };
          (p.x || p.y) && e.fire({
            type: "autoscroll",
            target: n,
            interactable: e,
            delta: p,
            interaction: t,
            container: i
          });
        }

        ho.prevTime = a;
      }

      ho.isScrolling && (po["default"].cancel(ho.i), ho.i = po["default"].request(ho.scroll));
    },
    check: function check(t, e) {
      var n = t.options;
      return n[e].autoScroll && n[e].autoScroll.enabled;
    },
    onInteractionMove: function onInteractionMove(t) {
      var e = t.interaction,
          n = t.pointer;
      if (e.interacting() && ho.check(e.interactable, e.prepared.name)) if (e.simulation) ho.x = ho.y = 0;else {
        var r,
            o,
            i,
            a,
            u = e.interactable,
            s = e.element,
            l = e.prepared.name,
            c = u.options[l].autoScroll,
            f = go(c.container, u, s);
        if (fo.window(f)) a = n.clientX < ho.margin, r = n.clientY < ho.margin, o = n.clientX > f.innerWidth - ho.margin, i = n.clientY > f.innerHeight - ho.margin;else {
          var p = co.getElementClientRect(f);
          a = n.clientX < p.left + ho.margin, r = n.clientY < p.top + ho.margin, o = n.clientX > p.right - ho.margin, i = n.clientY > p.bottom - ho.margin;
        }
        ho.x = o ? 1 : a ? -1 : 0, ho.y = i ? 1 : r ? -1 : 0, ho.isScrolling || (ho.margin = c.margin, ho.speed = c.speed, ho.start(e));
      }
    }
  };

  function go(t, e, n) {
    return (fo.string(t) ? (0, $t.getStringOptionResult)(t, e, n) : t) || (0, O.getWindow)(n);
  }

  function bo(t) {
    return fo.window(t) && (t = window.document.body), {
      x: t.scrollLeft,
      y: t.scrollTop
    };
  }

  var mo = {
    id: "auto-scroll",
    install: function install(t) {
      var e = t.defaults,
          n = t.actions;
      (t.autoScroll = ho).now = function () {
        return t.now();
      }, n.phaselessTypes.autoscroll = !0, e.perAction.autoScroll = ho.defaults;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.autoScroll = null;
      },
      "interactions:destroy": function interactionsDestroy(t) {
        t.interaction.autoScroll = null, ho.stop(), ho.interaction && (ho.interaction = null);
      },
      "interactions:stop": ho.stop,
      "interactions:action-move": function interactionsActionMove(t) {
        return ho.onInteractionMove(t);
      }
    }
  };
  uo["default"] = mo;
  var Oo = {};

  function wo(t) {
    return (wo = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Oo, "__esModule", {
    value: !0
  }), Oo["default"] = void 0;

  var _o = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== wo(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Po();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w);

  function Po() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Po = function Po() {
      return t;
    }, t;
  }

  function xo(t) {
    return _o.bool(t) ? (this.options.styleCursor = t, this) : null === t ? (delete this.options.styleCursor, this) : this.options.styleCursor;
  }

  function So(t) {
    return _o.func(t) ? (this.options.actionChecker = t, this) : null === t ? (delete this.options.actionChecker, this) : this.options.actionChecker;
  }

  var jo = {
    id: "auto-start/interactableMethods",
    install: function install(d) {
      var t = d.Interactable;
      t.prototype.getAction = function (t, e, n, r) {
        var o,
            i,
            a,
            u,
            s,
            l,
            c,
            f,
            p = (i = e, a = n, u = r, s = d, l = (o = this).getRect(u), c = i.buttons || {
          0: 1,
          1: 4,
          3: 8,
          4: 16
        }[i.button], f = {
          action: null,
          interactable: o,
          interaction: a,
          element: u,
          rect: l,
          buttons: c
        }, s.fire("auto-start:check", f), f.action);
        return this.options.actionChecker ? this.options.actionChecker(t, e, p, this, r, n) : p;
      }, t.prototype.ignoreFrom = (0, le.warnOnce)(function (t) {
        return this._backCompatOption("ignoreFrom", t);
      }, "Interactable.ignoreFrom() has been deprecated. Use Interactble.draggable({ignoreFrom: newValue})."), t.prototype.allowFrom = (0, le.warnOnce)(function (t) {
        return this._backCompatOption("allowFrom", t);
      }, "Interactable.allowFrom() has been deprecated. Use Interactble.draggable({allowFrom: newValue})."), t.prototype.actionChecker = So, t.prototype.styleCursor = xo;
    }
  };
  Oo["default"] = jo;
  var Mo = {};

  function ko(t) {
    return (ko = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Mo, "__esModule", {
    value: !0
  }), Mo["default"] = void 0;

  var Eo,
      To = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== ko(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Io();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(le),
      Do = (Eo = Oo) && Eo.__esModule ? Eo : {
    "default": Eo
  };

  function Io() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Io = function Io() {
      return t;
    }, t;
  }

  function zo(t, e, n, r, o) {
    return e.testIgnoreAllow(e.options[t.name], n, r) && e.options[t.name].enabled && Ro(e, n, t, o) ? t : null;
  }

  function Ao(t, e, n, r, o, i, a) {
    for (var u = 0, s = r.length; u < s; u++) {
      var l = r[u],
          c = o[u],
          f = l.getAction(e, n, t, c);

      if (f) {
        var p = zo(f, l, c, i, a);
        if (p) return {
          action: p,
          interactable: l,
          element: c
        };
      }
    }

    return {
      action: null,
      interactable: null,
      element: null
    };
  }

  function Co(t, e, n, r, o) {
    var i = [],
        a = [],
        u = r;

    function s(t) {
      i.push(t), a.push(u);
    }

    for (; To.is.element(u);) {
      i = [], a = [], o.interactables.forEachMatch(u, s);
      var l = Ao(t, e, n, i, a, r, o);
      if (l.action && !l.interactable.options[l.action.name].manualStart) return l;
      u = To.dom.parentNode(u);
    }

    return {
      action: null,
      interactable: null,
      element: null
    };
  }

  function Wo(t, e, n) {
    var r = e.action,
        o = e.interactable,
        i = e.element;
    r = r || {
      name: null
    }, t.interactable = o, t.element = i, To.copyAction(t.prepared, r), t.rect = o && r.name ? o.getRect(i) : null, Yo(t, n), n.fire("autoStart:prepared", {
      interaction: t
    });
  }

  function Ro(t, e, n, r) {
    var o = t.options,
        i = o[n.name].max,
        a = o[n.name].maxPerElement,
        u = r.autoStart.maxInteractions,
        s = 0,
        l = 0,
        c = 0;
    if (!(i && a && u)) return !1;

    for (var f = 0; f < r.interactions.list.length; f++) {
      var p = r.interactions.list[f],
          d = p.prepared.name;

      if (p.interacting()) {
        if (u <= ++s) return !1;

        if (p.interactable === t) {
          if (i <= (l += d === n.name ? 1 : 0)) return !1;
          if (p.element === e && (c++, d === n.name && a <= c)) return !1;
        }
      }
    }

    return 0 < u;
  }

  function Fo(t, e) {
    return To.is.number(t) ? (e.autoStart.maxInteractions = t, this) : e.autoStart.maxInteractions;
  }

  function Xo(t, e, n) {
    var r = n.autoStart.cursorElement;
    r && r !== t && (r.style.cursor = ""), t.ownerDocument.documentElement.style.cursor = e, t.style.cursor = e, n.autoStart.cursorElement = e ? t : null;
  }

  function Yo(t, e) {
    var n = t.interactable,
        r = t.element,
        o = t.prepared;

    if ("mouse" === t.pointerType && n && n.options.styleCursor) {
      var i = "";

      if (o.name) {
        var a = n.options[o.name].cursorChecker;
        i = To.is.func(a) ? a(o, n, r, t._interacting) : e.actions.map[o.name].getCursor(o);
      }

      Xo(t.element, i || "", e);
    } else e.autoStart.cursorElement && Xo(e.autoStart.cursorElement, "", e);
  }

  var No = {
    id: "auto-start/base",
    before: ["actions", "actions/drag", "actions/resize", "actions/gesture"],
    install: function install(e) {
      var t = e.interactStatic,
          n = e.defaults;
      e.usePlugin(Do["default"]), n.base.actionChecker = null, n.base.styleCursor = !0, To.extend(n.perAction, {
        manualStart: !1,
        max: 1 / 0,
        maxPerElement: 1,
        allowFrom: null,
        ignoreFrom: null,
        mouseButtons: 1
      }), t.maxInteractions = function (t) {
        return Fo(t, e);
      }, e.autoStart = {
        maxInteractions: 1 / 0,
        withinInteractionLimit: Ro,
        cursorElement: null
      };
    },
    listeners: {
      "interactions:down": function interactionsDown(t, e) {
        var n = t.interaction,
            r = t.pointer,
            o = t.event,
            i = t.eventTarget;
        n.interacting() || Wo(n, Co(n, r, o, i, e), e);
      },
      "interactions:move": function interactionsMove(t, e) {
        var n, r, o, i, a, u;
        r = e, o = (n = t).interaction, i = n.pointer, a = n.event, u = n.eventTarget, "mouse" !== o.pointerType || o.pointerIsDown || o.interacting() || Wo(o, Co(o, i, a, u, r), r), function (t, e) {
          var n = t.interaction;

          if (n.pointerIsDown && !n.interacting() && n.pointerWasMoved && n.prepared.name) {
            e.fire("autoStart:before-start", t);
            var r = n.interactable,
                o = n.prepared.name;
            o && r && (r.options[o].manualStart || !Ro(r, n.element, n.prepared, e) ? n.stop() : (n.start(n.prepared, r, n.element), Yo(n, e)));
          }
        }(t, e);
      },
      "interactions:stop": function interactionsStop(t, e) {
        var n = t.interaction,
            r = n.interactable;
        r && r.options.styleCursor && Xo(n.element, "", e);
      }
    },
    maxInteractions: Fo,
    withinInteractionLimit: Ro,
    validateAction: zo
  };
  Mo["default"] = No;
  var Lo = {};

  function Bo(t) {
    return (Bo = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Lo, "__esModule", {
    value: !0
  }), Lo["default"] = void 0;

  var Vo,
      qo = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Bo(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Go();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w),
      Uo = (Vo = Mo) && Vo.__esModule ? Vo : {
    "default": Vo
  };

  function Go() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Go = function Go() {
      return t;
    }, t;
  }

  var Ho = {
    id: "auto-start/dragAxis",
    listeners: {
      "autoStart:before-start": function autoStartBeforeStart(t, r) {
        var o = t.interaction,
            i = t.eventTarget,
            e = t.dx,
            n = t.dy;

        if ("drag" === o.prepared.name) {
          var a = Math.abs(e),
              u = Math.abs(n),
              s = o.interactable.options.drag,
              l = s.startAxis,
              c = u < a ? "x" : a < u ? "y" : "xy";

          if (o.prepared.axis = "start" === s.lockAxis ? c[0] : s.lockAxis, "xy" != c && "xy" !== l && l !== c) {
            var _f = function _f(t) {
              if (t !== o.interactable) {
                var e = o.interactable.options.drag;

                if (!e.manualStart && t.testIgnoreAllow(e, p, i)) {
                  var n = t.getAction(o.downPointer, o.downEvent, o, p);
                  if (n && "drag" === n.name && function (t, e) {
                    if (!e) return;
                    var n = e.options.drag.startAxis;
                    return "xy" === t || "xy" === n || n === t;
                  }(c, t) && Uo["default"].validateAction(n, t, p, i, r)) return t;
                }
              }
            };

            o.prepared.name = null;

            for (var p = i; qo.element(p);) {
              var d = r.interactables.forEachMatch(p, _f);

              if (d) {
                o.prepared.name = "drag", o.interactable = d, o.element = p;
                break;
              }

              p = (0, $.parentNode)(p);
            }
          }
        }
      }
    }
  };
  Lo["default"] = Ho;
  var Ko = {};
  Object.defineProperty(Ko, "__esModule", {
    value: !0
  }), Ko["default"] = void 0;
  var $o,
      Zo = ($o = Mo) && $o.__esModule ? $o : {
    "default": $o
  };

  function Jo(t) {
    var e = t.prepared && t.prepared.name;
    if (!e) return null;
    var n = t.interactable.options;
    return n[e].hold || n[e].delay;
  }

  var Qo = {
    id: "auto-start/hold",
    install: function install(t) {
      var e = t.defaults;
      t.usePlugin(Zo["default"]), e.perAction.hold = 0, e.perAction.delay = 0;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.autoStartHoldTimer = null;
      },
      "autoStart:prepared": function autoStartPrepared(t) {
        var e = t.interaction,
            n = Jo(e);
        0 < n && (e.autoStartHoldTimer = setTimeout(function () {
          e.start(e.prepared, e.interactable, e.element);
        }, n));
      },
      "interactions:move": function interactionsMove(t) {
        var e = t.interaction,
            n = t.duplicate;
        e.pointerWasMoved && !n && clearTimeout(e.autoStartHoldTimer);
      },
      "autoStart:before-start": function autoStartBeforeStart(t) {
        var e = t.interaction;
        0 < Jo(e) && (e.prepared.name = null);
      }
    },
    getHoldDuration: Jo
  };
  Ko["default"] = Qo;
  var ti = {};
  Object.defineProperty(ti, "__esModule", {
    value: !0
  }), Object.defineProperty(ti, "autoStart", {
    enumerable: !0,
    get: function get() {
      return ei["default"];
    }
  }), Object.defineProperty(ti, "dragAxis", {
    enumerable: !0,
    get: function get() {
      return ni["default"];
    }
  }), Object.defineProperty(ti, "hold", {
    enumerable: !0,
    get: function get() {
      return ri["default"];
    }
  }), ti["default"] = void 0;
  var ei = oi(Mo),
      ni = oi(Lo),
      ri = oi(Ko);

  function oi(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var ii = {
    id: "auto-start",
    install: function install(t) {
      t.usePlugin(ei["default"]), t.usePlugin(ri["default"]), t.usePlugin(ni["default"]);
    }
  };
  ti["default"] = ii;
  var ai = {};
  Object.defineProperty(ai, "__esModule", {
    value: !0
  }), ai["default"] = void 0;
  ai["default"] = {};
  var ui = {};
  Object.defineProperty(ui, "__esModule", {
    value: !0
  }), ui["default"] = void 0;
  ui["default"] = {};
  var si = {};

  function li(t) {
    return (li = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(si, "__esModule", {
    value: !0
  }), si["default"] = void 0;
  var ci,
      fi,
      pi = hi(D),
      di = (hi(ct), function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== li(t) && "function" != typeof t) return {
      "default": t
    };
    var e = yi();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w)),
      vi = hi(O);

  function yi() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return yi = function yi() {
      return t;
    }, t;
  }

  function hi(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  (fi = ci = ci || {}).touchAction = "touchAction", fi.boxSizing = "boxSizing", fi.noListeners = "noListeners";
  var gi = {
    touchAction: "https://developer.mozilla.org/en-US/docs/Web/CSS/touch-action",
    boxSizing: "https://developer.mozilla.org/en-US/docs/Web/CSS/box-sizing"
  };
  ci.touchAction, ci.boxSizing, ci.noListeners;

  function bi(t, e, n) {
    return n.test(t.style[e] || vi["default"].window.getComputedStyle(t)[e]);
  }

  var mi = "dev-tools",
      Oi = {
    id: mi,
    install: function install() {}
  };
  si["default"] = Oi;
  var wi = {};
  Object.defineProperty(wi, "__esModule", {
    value: !0
  }), wi["default"] = void 0;
  wi["default"] = {};
  var _i = {};

  function Pi(t) {
    return (Pi = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(_i, "__esModule", {
    value: !0
  }), _i.getRectOffset = Ai, _i["default"] = void 0;

  var xi = ki(V),
      Si = ki(ct),
      ji = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Pi(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Mi();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }($t);

  function Mi() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Mi = function Mi() {
      return t;
    }, t;
  }

  function ki(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function Ei(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if (!(Symbol.iterator in Object(t) || "[object Arguments]" === Object.prototype.toString.call(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, u = t[Symbol.iterator](); !(r = (a = u.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == u["return"] || u["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance");
    }();
  }

  function Ti(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Di(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var Ii = function () {
    function e(t) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, e), this.interaction = t, Di(this, "states", []), Di(this, "startOffset", {
        left: 0,
        right: 0,
        top: 0,
        bottom: 0
      }), Di(this, "startDelta", null), Di(this, "result", null), Di(this, "endResult", null), Di(this, "edges", void 0), this.result = zi();
    }

    var t, n, r;
    return t = e, (n = [{
      key: "start",
      value: function value(t, e) {
        var n = t.phase,
            r = this.interaction,
            o = function (t) {
          var n = t.interactable.options[t.prepared.name],
              e = n.modifiers;
          if (e && e.length) return e.filter(function (t) {
            return !t.options || !1 !== t.options.enabled;
          });
          return ["snap", "snapSize", "snapEdges", "restrict", "restrictEdges", "restrictSize"].map(function (t) {
            var e = n[t];
            return e && e.enabled && {
              options: e,
              methods: e._methods
            };
          }).filter(function (t) {
            return !!t;
          });
        }(r);

        this.prepareStates(o), this.edges = (0, Si["default"])({}, r.edges), this.startOffset = Ai(r.rect, e);
        var i = {
          phase: n,
          pageCoords: e,
          preEnd: !(this.startDelta = {
            x: 0,
            y: 0
          })
        };
        return this.result = zi(), this.startAll(i), this.result = this.setAll(i);
      }
    }, {
      key: "fillArg",
      value: function value(t) {
        var e = this.interaction;
        t.interaction = e, t.interactable = e.interactable, t.element = e.element, t.rect = t.rect || e.rect, t.edges = this.edges, t.startOffset = this.startOffset;
      }
    }, {
      key: "startAll",
      value: function value(t) {
        this.fillArg(t);

        for (var e = 0; e < this.states.length; e++) {
          var n = this.states[e];
          n.methods.start && (t.state = n).methods.start(t);
        }
      }
    }, {
      key: "setAll",
      value: function value(t) {
        this.fillArg(t);
        var e = t.phase,
            n = t.preEnd,
            r = t.skipModifiers,
            o = t.rect;
        t.coords = (0, Si["default"])({}, t.pageCoords), t.rect = (0, Si["default"])({}, o);

        for (var i = r ? this.states.slice(r) : this.states, a = zi(t.coords, t.rect), u = 0; u < i.length; u++) {
          var s = i[u],
              l = s.options,
              c = (0, Si["default"])({}, t.coords),
              f = null;
          s.methods.set && this.shouldDo(l, n, e) && (f = (t.state = s).methods.set(t), ji.addEdges(this.interaction.edges, t.rect, {
            x: t.coords.x - c.x,
            y: t.coords.y - c.y
          })), a.eventProps.push(f);
        }

        a.delta.x = t.coords.x - t.pageCoords.x, a.delta.y = t.coords.y - t.pageCoords.y, a.rectDelta.left = t.rect.left - o.left, a.rectDelta.right = t.rect.right - o.right, a.rectDelta.top = t.rect.top - o.top, a.rectDelta.bottom = t.rect.bottom - o.bottom;
        var p = this.result.coords,
            d = this.result.rect;

        if (p && d) {
          var v = a.rect.left !== d.left || a.rect.right !== d.right || a.rect.top !== d.top || a.rect.bottom !== d.bottom;
          a.changed = v || p.x !== a.coords.x || p.y !== a.coords.y;
        }

        return a;
      }
    }, {
      key: "applyToInteraction",
      value: function value(t) {
        var e = this.interaction,
            n = t.phase,
            r = e.coords.cur,
            o = e.coords.start,
            i = this.result,
            a = this.startDelta,
            u = i.delta;
        "start" === n && (0, Si["default"])(this.startDelta, i.delta);

        for (var s = 0; s < [[o, a], [r, u]].length; s++) {
          var l = Ei([[o, a], [r, u]][s], 2),
              c = l[0],
              f = l[1];
          c.page.x += f.x, c.page.y += f.y, c.client.x += f.x, c.client.y += f.y;
        }

        var p = this.result.rectDelta,
            d = t.rect || e.rect;
        d.left += p.left, d.right += p.right, d.top += p.top, d.bottom += p.bottom, d.width = d.right - d.left, d.height = d.bottom - d.top;
      }
    }, {
      key: "setAndApply",
      value: function value(t) {
        var e = this.interaction,
            n = t.phase,
            r = t.preEnd,
            o = t.skipModifiers,
            i = this.setAll({
          preEnd: r,
          phase: n,
          pageCoords: t.modifiedCoords || e.coords.cur.page
        });
        if (!(this.result = i).changed && (!o || o < this.states.length) && e.interacting()) return !1;

        if (t.modifiedCoords) {
          var a = e.coords.cur.page,
              u = t.modifiedCoords.x - a.x,
              s = t.modifiedCoords.y - a.y;
          i.coords.x += u, i.coords.y += s, i.delta.x += u, i.delta.y += s;
        }

        this.applyToInteraction(t);
      }
    }, {
      key: "beforeEnd",
      value: function value(t) {
        var e = t.interaction,
            n = t.event,
            r = this.states;

        if (r && r.length) {
          for (var o = !1, i = 0; i < r.length; i++) {
            var a = r[i],
                u = (t.state = a).options,
                s = a.methods,
                l = s.beforeEnd && s.beforeEnd(t);
            if (l) return this.endResult = l, !1;
            o = o || !o && this.shouldDo(u, !0, t.phase, !0);
          }

          o && e.move({
            event: n,
            preEnd: !0
          });
        }
      }
    }, {
      key: "stop",
      value: function value(t) {
        var e = t.interaction;

        if (this.states && this.states.length) {
          var n = (0, Si["default"])({
            states: this.states,
            interactable: e.interactable,
            element: e.element,
            rect: null
          }, t);
          this.fillArg(n);

          for (var r = 0; r < this.states.length; r++) {
            var o = this.states[r];
            (n.state = o).methods.stop && o.methods.stop(n);
          }

          this.states = null, this.endResult = null;
        }
      }
    }, {
      key: "prepareStates",
      value: function value(t) {
        this.states = [];

        for (var e = 0; e < t.length; e++) {
          var n = t[e],
              r = n.options,
              o = n.methods,
              i = n.name;
          r && !1 === r.enabled || this.states.push({
            options: r,
            methods: o,
            index: e,
            name: i
          });
        }

        return this.states;
      }
    }, {
      key: "restoreInteractionCoords",
      value: function value(t) {
        var e = t.interaction,
            n = e.coords,
            r = e.rect,
            o = e.modification;

        if (o.result) {
          for (var i = o.startDelta, a = o.result, u = a.delta, s = a.rectDelta, l = [[n.start, i], [n.cur, u]], c = 0; c < l.length; c++) {
            var f = Ei(l[c], 2),
                p = f[0],
                d = f[1];
            p.page.x -= d.x, p.page.y -= d.y, p.client.x -= d.x, p.client.y -= d.y;
          }

          r.left -= s.left, r.right -= s.right, r.top -= s.top, r.bottom -= s.bottom;
        }
      }
    }, {
      key: "shouldDo",
      value: function value(t, e, n, r) {
        return !(!t || !1 === t.enabled || r && !t.endOnly || t.endOnly && !e || "start" === n && !t.setStart);
      }
    }, {
      key: "copyFrom",
      value: function value(t) {
        this.startOffset = t.startOffset, this.startDelta = t.startDelta, this.edges = t.edges, this.states = t.states.map(function (t) {
          return (0, xi["default"])(t);
        }), this.result = zi((0, Si["default"])({}, t.result.coords), (0, Si["default"])({}, t.result.rect));
      }
    }, {
      key: "destroy",
      value: function value() {
        for (var t in this) {
          this[t] = null;
        }
      }
    }]) && Ti(t.prototype, n), r && Ti(t, r), e;
  }();

  function zi(t, e) {
    return {
      rect: e,
      coords: t,
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
      eventProps: [],
      changed: !0
    };
  }

  function Ai(t, e) {
    return t ? {
      left: e.x - t.left,
      top: e.y - t.top,
      right: t.right - e.x,
      bottom: t.bottom - e.y
    } : {
      left: 0,
      top: 0,
      right: 0,
      bottom: 0
    };
  }

  _i["default"] = Ii;
  var Ci = {};
  Object.defineProperty(Ci, "__esModule", {
    value: !0
  }), Ci.makeModifier = function (t, r) {
    function e(t) {
      var e = t || {};

      for (var n in e.enabled = !1 !== e.enabled, o) {
        n in e || (e[n] = o[n]);
      }

      return {
        options: e,
        methods: i,
        name: r
      };
    }

    var o = t.defaults,
        i = {
      start: t.start,
      set: t.set,
      beforeEnd: t.beforeEnd,
      stop: t.stop
    };
    r && "string" == typeof r && (e._defaults = o, e._methods = i);
    return e;
  }, Ci.addEventModifiers = Fi, Ci["default"] = void 0;
  var Wi,
      Ri = (Wi = _i) && Wi.__esModule ? Wi : {
    "default": Wi
  };

  function Fi(t) {
    var e = t.iEvent,
        n = t.interaction.modification.result;
    n && (e.modifiers = n.eventProps);
  }

  var Xi = {
    id: "modifiers/base",
    install: function install(t) {
      t.defaults.perAction.modifiers = [];
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        var e = t.interaction;
        e.modification = new Ri["default"](e);
      },
      "interactions:before-action-start": function interactionsBeforeActionStart(t) {
        var e = t.interaction.modification;
        e.start(t, t.interaction.coords.start.page), t.interaction.edges = e.edges, e.applyToInteraction(t);
      },
      "interactions:before-action-move": function interactionsBeforeActionMove(t) {
        return t.interaction.modification.setAndApply(t);
      },
      "interactions:before-action-end": function interactionsBeforeActionEnd(t) {
        return t.interaction.modification.beforeEnd(t);
      },
      "interactions:action-start": Fi,
      "interactions:action-move": Fi,
      "interactions:action-end": Fi,
      "interactions:after-action-start": function interactionsAfterActionStart(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:after-action-move": function interactionsAfterActionMove(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:stop": function interactionsStop(t) {
        return t.interaction.modification.stop(t);
      }
    },
    before: ["actions", "action/drag", "actions/resize", "actions/gesture"]
  };
  Ci["default"] = Xi;
  var Yi = {};

  function Ni(t) {
    return (Ni = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Yi, "__esModule", {
    value: !0
  }), Yi.addTotal = Vi, Yi.applyPending = Ui, Yi["default"] = void 0;

  var Li = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Ni(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Bi();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }($t);

  function Bi() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Bi = function Bi() {
      return t;
    }, t;
  }

  function Vi(t) {
    t.pointerIsDown && (Hi(t.coords.cur, t.offset.total), t.offset.pending.x = 0, t.offset.pending.y = 0);
  }

  function qi(t) {
    Ui(t.interaction);
  }

  function Ui(t) {
    if (!(e = t).offset.pending.x && !e.offset.pending.y) return !1;
    var e,
        n = t.offset.pending;
    return Hi(t.coords.cur, n), Hi(t.coords.delta, n), Li.addEdges(t.edges, t.rect, n), n.x = 0, !(n.y = 0);
  }

  function Gi(t) {
    var e = t.x,
        n = t.y;
    this.offset.pending.x += e, this.offset.pending.y += n, this.offset.total.x += e, this.offset.total.y += n;
  }

  function Hi(t, e) {
    var n = t.page,
        r = t.client,
        o = e.x,
        i = e.y;
    n.x += o, n.y += i, r.x += o, r.y += i;
  }

  En._ProxyMethods.offsetBy = "";
  var Ki = {
    id: "offset",
    install: function install(t) {
      t.Interaction.prototype.offsetBy = Gi;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.offset = {
          total: {
            x: 0,
            y: 0
          },
          pending: {
            x: 0,
            y: 0
          }
        };
      },
      "interactions:update-pointer": function interactionsUpdatePointer(t) {
        return Vi(t.interaction);
      },
      "interactions:before-action-start": qi,
      "interactions:before-action-move": qi,
      "interactions:before-action-end": function interactionsBeforeActionEnd(t) {
        var e = t.interaction;
        if (Ui(e)) return e.move({
          offset: !0
        }), e.end(), !1;
      },
      "interactions:stop": function interactionsStop(t) {
        var e = t.interaction;
        e.offset.total.x = 0, e.offset.total.y = 0, e.offset.pending.x = 0, e.offset.pending.y = 0;
      }
    }
  };
  Yi["default"] = Ki;
  var $i = {};

  function Zi(t) {
    return (Zi = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty($i, "__esModule", {
    value: !0
  }), $i["default"] = $i.InertiaState = void 0;
  var Ji = ua(_i),
      Qi = aa(Ci),
      ta = ua(Yi),
      ea = aa($),
      na = ua(Et),
      ra = aa(w),
      oa = ua(oe);

  function ia() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return ia = function ia() {
      return t;
    }, t;
  }

  function aa(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Zi(t) && "function" != typeof t) return {
      "default": t
    };
    var e = ia();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function ua(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function sa(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function la(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var ca = function () {
    function e(t) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, e), this.interaction = t, la(this, "active", !1), la(this, "isModified", !1), la(this, "smoothEnd", !1), la(this, "allowResume", !1), la(this, "modification", null), la(this, "modifierCount", 0), la(this, "modifierArg", null), la(this, "startCoords", null), la(this, "t0", 0), la(this, "v0", 0), la(this, "te", 0), la(this, "targetOffset", null), la(this, "modifiedOffset", null), la(this, "currentOffset", null), la(this, "lambda_v0", 0), la(this, "one_ve_v0", 0), la(this, "timeout", null);
    }

    var t, n, r;
    return t = e, (n = [{
      key: "start",
      value: function value(t) {
        var e = this.interaction,
            n = fa(e);
        if (!n || !n.enabled) return !1;
        var r = e.coords.velocity.client,
            o = (0, na["default"])(r.x, r.y),
            i = this.modification || (this.modification = new Ji["default"](e));
        if (i.copyFrom(e.modification), this.t0 = e._now(), this.allowResume = n.allowResume, this.v0 = o, this.currentOffset = {
          x: 0,
          y: 0
        }, this.startCoords = e.coords.cur.page, this.modifierArg = {
          interaction: e,
          interactable: e.interactable,
          element: e.element,
          rect: e.rect,
          edges: e.edges,
          pageCoords: this.startCoords,
          preEnd: !0,
          phase: "inertiastart"
        }, this.t0 - e.coords.cur.timeStamp < 50 && o > n.minSpeed && o > n.endSpeed) this.startInertia();else {
          if (i.result = i.setAll(this.modifierArg), !i.result.changed) return !1;
          this.startSmoothEnd();
        }
        return e.modification.result.rect = null, e.offsetBy(this.targetOffset), e._doPhase({
          interaction: e,
          event: t,
          phase: "inertiastart"
        }), e.offsetBy({
          x: -this.targetOffset.x,
          y: -this.targetOffset.y
        }), e.modification.result.rect = null, this.active = !0, e.simulation = this, !0;
      }
    }, {
      key: "startInertia",
      value: function value() {
        var t = this,
            e = this.interaction.coords.velocity.client,
            n = fa(this.interaction),
            r = n.resistance,
            o = -Math.log(n.endSpeed / this.v0) / r;
        this.targetOffset = {
          x: (e.x - o) / r,
          y: (e.y - o) / r
        }, this.te = o, this.lambda_v0 = r / this.v0, this.one_ve_v0 = 1 - n.endSpeed / this.v0;
        var i = this.modification,
            a = this.modifierArg;
        a.pageCoords = {
          x: this.startCoords.x + this.targetOffset.x,
          y: this.startCoords.y + this.targetOffset.y
        }, i.result = i.setAll(a), i.result.changed && (this.isModified = !0, this.modifiedOffset = {
          x: this.targetOffset.x + i.result.delta.x,
          y: this.targetOffset.y + i.result.delta.y
        }), this.timeout = oa["default"].request(function () {
          return t.inertiaTick();
        });
      }
    }, {
      key: "startSmoothEnd",
      value: function value() {
        var t = this;
        this.smoothEnd = !0, this.isModified = !0, this.targetOffset = {
          x: this.modification.result.delta.x,
          y: this.modification.result.delta.y
        }, this.timeout = oa["default"].request(function () {
          return t.smoothEndTick();
        });
      }
    }, {
      key: "inertiaTick",
      value: function value() {
        var t,
            e,
            n,
            r,
            o,
            i,
            a,
            u = this,
            s = this.interaction,
            l = fa(s).resistance,
            c = (s._now() - this.t0) / 1e3;

        if (c < this.te) {
          var f,
              p = 1 - (Math.exp(-l * c) - this.lambda_v0) / this.one_ve_v0,
              d = {
            x: (f = this.isModified ? (e = t = 0, n = this.targetOffset.x, r = this.targetOffset.y, o = this.modifiedOffset.x, i = this.modifiedOffset.y, {
              x: pa(a = p, t, n, o),
              y: pa(a, e, r, i)
            }) : {
              x: this.targetOffset.x * p,
              y: this.targetOffset.y * p
            }).x - this.currentOffset.x,
            y: f.y - this.currentOffset.y
          };
          this.currentOffset.x += d.x, this.currentOffset.y += d.y, s.offsetBy(d), s.move(), this.timeout = oa["default"].request(function () {
            return u.inertiaTick();
          });
        } else s.offsetBy({
          x: this.modifiedOffset.x - this.currentOffset.x,
          y: this.modifiedOffset.y - this.currentOffset.y
        }), this.end();
      }
    }, {
      key: "smoothEndTick",
      value: function value() {
        var t = this,
            e = this.interaction,
            n = e._now() - this.t0,
            r = fa(e).smoothEndDuration;

        if (n < r) {
          var o = da(n, 0, this.targetOffset.x, r),
              i = da(n, 0, this.targetOffset.y, r),
              a = {
            x: o - this.currentOffset.x,
            y: i - this.currentOffset.y
          };
          this.currentOffset.x += a.x, this.currentOffset.y += a.y, e.offsetBy(a), e.move({
            skipModifiers: this.modifierCount
          }), this.timeout = oa["default"].request(function () {
            return t.smoothEndTick();
          });
        } else e.offsetBy({
          x: this.targetOffset.x - this.currentOffset.x,
          y: this.targetOffset.y - this.currentOffset.y
        }), this.end();
      }
    }, {
      key: "resume",
      value: function value(t) {
        var e = t.pointer,
            n = t.event,
            r = t.eventTarget,
            o = this.interaction;
        o.offsetBy({
          x: -this.currentOffset.x,
          y: -this.currentOffset.y
        }), o.updatePointer(e, n, r, !0), o._doPhase({
          interaction: o,
          event: n,
          phase: "resume"
        }), (0, zt.copyCoords)(o.coords.prev, o.coords.cur), this.stop();
      }
    }, {
      key: "end",
      value: function value() {
        this.interaction.move(), this.interaction.end(), this.stop();
      }
    }, {
      key: "stop",
      value: function value() {
        this.active = this.smoothEnd = !1, this.interaction.simulation = null, oa["default"].cancel(this.timeout);
      }
    }]) && sa(t.prototype, n), r && sa(t, r), e;
  }();

  function fa(t) {
    var e = t.interactable,
        n = t.prepared;
    return e && e.options && n.name && e.options[n.name].inertia;
  }

  function pa(t, e, n, r) {
    var o = 1 - t;
    return o * o * e + 2 * o * t * n + t * t * r;
  }

  function da(t, e, n, r) {
    return -n * (t /= r) * (t - 2) + e;
  }

  $i.InertiaState = ca;
  var va = {
    id: "inertia",
    before: ["modifiers/base"],
    install: function install(t) {
      var e = t.defaults;
      t.usePlugin(ta["default"]), t.usePlugin(Qi["default"]), t.actions.phases.inertiastart = !0, t.actions.phases.resume = !0, e.perAction.inertia = {
        enabled: !1,
        resistance: 10,
        minSpeed: 100,
        endSpeed: 10,
        allowResume: !0,
        smoothEndDuration: 300
      };
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        var e = t.interaction;
        e.inertia = new ca(e);
      },
      "interactions:before-action-end": function interactionsBeforeActionEnd(t) {
        var e = t.interaction,
            n = t.event;
        return (!e._interacting || e.simulation || !e.inertia.start(n)) && null;
      },
      "interactions:down": function interactionsDown(t) {
        var e = t.interaction,
            n = t.eventTarget,
            r = e.inertia;
        if (r.active) for (var o = n; ra.element(o);) {
          if (o === e.element) {
            r.resume(t);
            break;
          }

          o = ea.parentNode(o);
        }
      },
      "interactions:stop": function interactionsStop(t) {
        var e = t.interaction.inertia;
        e.active && e.stop();
      },
      "interactions:before-action-resume": function interactionsBeforeActionResume(t) {
        var e = t.interaction.modification;
        e.stop(t), e.start(t, t.interaction.coords.cur.page), e.applyToInteraction(t);
      },
      "interactions:before-action-inertiastart": function interactionsBeforeActionInertiastart(t) {
        return t.interaction.modification.setAndApply(t);
      },
      "interactions:action-resume": Qi.addEventModifiers,
      "interactions:action-inertiastart": Qi.addEventModifiers,
      "interactions:after-action-inertiastart": function interactionsAfterActionInertiastart(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:after-action-resume": function interactionsAfterActionResume(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      }
    }
  };
  $i["default"] = va;
  var ya,
      ha = {};

  function ga(t) {
    return (ga = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(ha, "__esModule", {
    value: !0
  }), ha.init = ha["default"] = void 0;
  var ba = new ((ya = m({})) && ya.__esModule ? ya : {
    "default": ya
  })["default"](),
      ma = ba.interactStatic;
  ha["default"] = ma;

  function Oa(t) {
    return ba.init(t);
  }

  ha.init = Oa, "object" === ("undefined" == typeof window ? "undefined" : ga(window)) && window && Oa(window);
  var wa = {};
  Object.defineProperty(wa, "__esModule", {
    value: !0
  }), wa["default"] = void 0;
  wa["default"] = {};
  var _a = {};
  Object.defineProperty(_a, "__esModule", {
    value: !0
  }), _a["default"] = void 0;
  _a["default"] = {};
  var Pa = {};

  function xa(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if (!(Symbol.iterator in Object(t) || "[object Arguments]" === Object.prototype.toString.call(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, u = t[Symbol.iterator](); !(r = (a = u.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == u["return"] || u["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance");
    }();
  }

  Object.defineProperty(Pa, "__esModule", {
    value: !0
  }), Pa["default"] = void 0;

  Pa["default"] = function (v) {
    function t(t, e) {
      for (var n = v.range, r = v.limits, o = void 0 === r ? {
        left: -1 / 0,
        right: 1 / 0,
        top: -1 / 0,
        bottom: 1 / 0
      } : r, i = v.offset, a = void 0 === i ? {
        x: 0,
        y: 0
      } : i, u = {
        range: n,
        grid: v,
        x: null,
        y: null
      }, s = 0; s < y.length; s++) {
        var l = xa(y[s], 2),
            c = l[0],
            f = l[1],
            p = Math.round((t - a.x) / v[c]),
            d = Math.round((e - a.y) / v[f]);
        u[c] = Math.max(o.left, Math.min(o.right, p * v[c] + a.x)), u[f] = Math.max(o.top, Math.min(o.bottom, d * v[f] + a.y));
      }

      return u;
    }

    var y = [["x", "y"], ["left", "top"], ["right", "bottom"], ["width", "height"]].filter(function (t) {
      var e = xa(t, 2),
          n = e[0],
          r = e[1];
      return n in v || r in v;
    });
    return t.grid = v, t.coordFields = y, t;
  };

  var Sa = {};
  Object.defineProperty(Sa, "__esModule", {
    value: !0
  }), Object.defineProperty(Sa, "edgeTarget", {
    enumerable: !0,
    get: function get() {
      return ja["default"];
    }
  }), Object.defineProperty(Sa, "elements", {
    enumerable: !0,
    get: function get() {
      return Ma["default"];
    }
  }), Object.defineProperty(Sa, "grid", {
    enumerable: !0,
    get: function get() {
      return ka["default"];
    }
  });
  var ja = Ea(wa),
      Ma = Ea(_a),
      ka = Ea(Pa);

  function Ea(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var Ta = {};

  function Da(t) {
    return (Da = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Ta, "__esModule", {
    value: !0
  }), Ta["default"] = void 0;

  var Ia,
      za = (Ia = ct) && Ia.__esModule ? Ia : {
    "default": Ia
  },
      Aa = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Da(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Ca();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(Sa);

  function Ca() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Ca = function Ca() {
      return t;
    }, t;
  }

  var Wa = {
    id: "snappers",
    install: function install(t) {
      var e = t.interactStatic;
      e.snappers = (0, za["default"])(e.snappers || {}, Aa), e.createSnapGrid = e.snappers.grid;
    }
  };
  Ta["default"] = Wa;
  var Ra = {};
  Object.defineProperty(Ra, "__esModule", {
    value: !0
  }), Ra.aspectRatio = Ra["default"] = void 0;
  var Fa = Ya(ct),
      Xa = Ya(_i);

  function Ya(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function Na(e, t) {
    var n = Object.keys(e);

    if (Object.getOwnPropertySymbols) {
      var r = Object.getOwnPropertySymbols(e);
      t && (r = r.filter(function (t) {
        return Object.getOwnPropertyDescriptor(e, t).enumerable;
      })), n.push.apply(n, r);
    }

    return n;
  }

  function La(e) {
    for (var t = 1; t < arguments.length; t++) {
      var n = null != arguments[t] ? arguments[t] : {};
      t % 2 ? Na(Object(n), !0).forEach(function (t) {
        Ba(e, t, n[t]);
      }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(n)) : Na(Object(n)).forEach(function (t) {
        Object.defineProperty(e, t, Object.getOwnPropertyDescriptor(n, t));
      });
    }

    return e;
  }

  function Ba(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var Va = {
    start: function start(t) {
      var e = t.state,
          n = t.rect,
          r = t.edges,
          o = t.pageCoords,
          i = e.options.ratio,
          a = e.options,
          u = a.equalDelta,
          s = a.modifiers;
      "preserve" === i && (i = n.width / n.height), e.startCoords = (0, Fa["default"])({}, o), e.startRect = (0, Fa["default"])({}, n), e.ratio = i, e.equalDelta = u;
      var l = e.linkedEdges = {
        top: r.top || r.left && !r.bottom,
        left: r.left || r.top && !r.right,
        bottom: r.bottom || r.right && !r.top,
        right: r.right || r.bottom && !r.left
      };
      if (e.xIsPrimaryAxis = !(!r.left && !r.right), e.equalDelta) e.edgeSign = (l.left ? 1 : -1) * (l.top ? 1 : -1);else {
        var c = e.xIsPrimaryAxis ? l.top : l.left;
        e.edgeSign = c ? -1 : 1;
      }

      if ((0, Fa["default"])(t.edges, l), s && s.length) {
        var f = new Xa["default"](t.interaction);
        f.copyFrom(t.interaction.modification), f.prepareStates(s), (e.subModification = f).startAll(La({}, t));
      }
    },
    set: function set(t) {
      var e = t.state,
          n = t.rect,
          r = t.coords,
          o = (0, Fa["default"])({}, r),
          i = e.equalDelta ? qa : Ua;
      if (i(e, e.xIsPrimaryAxis, r, n), !e.subModification) return null;
      var a = (0, Fa["default"])({}, n);
      (0, $t.addEdges)(e.linkedEdges, a, {
        x: r.x - o.x,
        y: r.y - o.y
      });
      var u = e.subModification.setAll(La({}, t, {
        rect: a,
        edges: e.linkedEdges,
        pageCoords: r,
        prevCoords: r,
        prevRect: a
      })),
          s = u.delta;
      u.changed && (i(e, Math.abs(s.x) > Math.abs(s.y), u.coords, u.rect), (0, Fa["default"])(r, u.coords));
      return u.eventProps;
    },
    defaults: {
      ratio: "preserve",
      equalDelta: !1,
      modifiers: [],
      enabled: !1
    }
  };

  function qa(t, e, n) {
    var r = t.startCoords,
        o = t.edgeSign;
    e ? n.y = r.y + (n.x - r.x) * o : n.x = r.x + (n.y - r.y) * o;
  }

  function Ua(t, e, n, r) {
    var o = t.startRect,
        i = t.startCoords,
        a = t.ratio,
        u = t.edgeSign;

    if (e) {
      var s = r.width / a;
      n.y = i.y + (s - o.height) * u;
    } else {
      var l = r.height * a;
      n.x = i.x + (l - o.width) * u;
    }
  }

  Ra.aspectRatio = Va;
  var Ga = (0, Ci.makeModifier)(Va, "aspectRatio");
  Ra["default"] = Ga;
  var Ha = {};
  Object.defineProperty(Ha, "__esModule", {
    value: !0
  }), Ha["default"] = void 0;

  function Ka() {}

  Ka._defaults = {};
  var $a = Ka;
  Ha["default"] = $a;
  var Za = {};

  function Ja(t) {
    return (Ja = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Za, "__esModule", {
    value: !0
  }), Za.getRestrictionRect = iu, Za.restrict = Za["default"] = void 0;
  var Qa,
      tu = (Qa = ct) && Qa.__esModule ? Qa : {
    "default": Qa
  },
      eu = ou(w),
      nu = ou($t);

  function ru() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return ru = function ru() {
      return t;
    }, t;
  }

  function ou(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Ja(t) && "function" != typeof t) return {
      "default": t
    };
    var e = ru();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function iu(t, e, n) {
    return eu.func(t) ? nu.resolveRectLike(t, e.interactable, e.element, [n.x, n.y, e]) : nu.resolveRectLike(t, e.interactable, e.element);
  }

  var au = {
    start: function start(t) {
      var e = t.rect,
          n = t.startOffset,
          r = t.state,
          o = t.interaction,
          i = t.pageCoords,
          a = r.options,
          u = a.elementRect,
          s = (0, tu["default"])({
        left: 0,
        top: 0,
        right: 0,
        bottom: 0
      }, a.offset || {});

      if (e && u) {
        var l = iu(a.restriction, o, i);

        if (l) {
          var c = l.right - l.left - e.width,
              f = l.bottom - l.top - e.height;
          c < 0 && (s.left += c, s.right += c), f < 0 && (s.top += f, s.bottom += f);
        }

        s.left += n.left - e.width * u.left, s.top += n.top - e.height * u.top, s.right += n.right - e.width * (1 - u.right), s.bottom += n.bottom - e.height * (1 - u.bottom);
      }

      r.offset = s;
    },
    set: function set(t) {
      var e = t.coords,
          n = t.interaction,
          r = t.state,
          o = r.options,
          i = r.offset,
          a = iu(o.restriction, n, e);

      if (a) {
        var u = nu.xywhToTlbr(a);
        e.x = Math.max(Math.min(u.right - i.right, e.x), u.left + i.left), e.y = Math.max(Math.min(u.bottom - i.bottom, e.y), u.top + i.top);
      }
    },
    defaults: {
      restriction: null,
      elementRect: null,
      offset: null,
      endOnly: !1,
      enabled: !1
    }
  };
  Za.restrict = au;
  var uu = (0, Ci.makeModifier)(au, "restrict");
  Za["default"] = uu;
  var su = {};

  function lu(t) {
    return (lu = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(su, "__esModule", {
    value: !0
  }), su.restrictEdges = su["default"] = void 0;

  var cu,
      fu = (cu = ct) && cu.__esModule ? cu : {
    "default": cu
  },
      pu = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== lu(t) && "function" != typeof t) return {
      "default": t
    };
    var e = du();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }($t);

  function du() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return du = function du() {
      return t;
    }, t;
  }

  var vu = {
    top: 1 / 0,
    left: 1 / 0,
    bottom: -1 / 0,
    right: -1 / 0
  },
      yu = {
    top: -1 / 0,
    left: -1 / 0,
    bottom: 1 / 0,
    right: 1 / 0
  };

  function hu(t, e) {
    for (var n = ["top", "left", "bottom", "right"], r = 0; r < n.length; r++) {
      var o = n[r];
      o in t || (t[o] = e[o]);
    }

    return t;
  }

  var gu = {
    noInner: vu,
    noOuter: yu,
    start: function start(t) {
      var e,
          n = t.interaction,
          r = t.startOffset,
          o = t.state,
          i = o.options;

      if (i) {
        var a = (0, Za.getRestrictionRect)(i.offset, n, n.coords.start.page);
        e = pu.rectToXY(a);
      }

      e = e || {
        x: 0,
        y: 0
      }, o.offset = {
        top: e.y + r.top,
        left: e.x + r.left,
        bottom: e.y - r.bottom,
        right: e.x - r.right
      };
    },
    set: function set(t) {
      var e = t.coords,
          n = t.edges,
          r = t.interaction,
          o = t.state,
          i = o.offset,
          a = o.options;

      if (n) {
        var u = (0, fu["default"])({}, e),
            s = (0, Za.getRestrictionRect)(a.inner, r, u) || {},
            l = (0, Za.getRestrictionRect)(a.outer, r, u) || {};
        hu(s, vu), hu(l, yu), n.top ? e.y = Math.min(Math.max(l.top + i.top, u.y), s.top + i.top) : n.bottom && (e.y = Math.max(Math.min(l.bottom + i.bottom, u.y), s.bottom + i.bottom)), n.left ? e.x = Math.min(Math.max(l.left + i.left, u.x), s.left + i.left) : n.right && (e.x = Math.max(Math.min(l.right + i.right, u.x), s.right + i.right));
      }
    },
    defaults: {
      inner: null,
      outer: null,
      offset: null,
      endOnly: !1,
      enabled: !1
    }
  };
  su.restrictEdges = gu;
  var bu = (0, Ci.makeModifier)(gu, "restrictEdges");
  su["default"] = bu;
  var mu,
      Ou = {};
  Object.defineProperty(Ou, "__esModule", {
    value: !0
  }), Ou.restrictRect = Ou["default"] = void 0;
  var wu = (0, ((mu = ct) && mu.__esModule ? mu : {
    "default": mu
  })["default"])({
    get elementRect() {
      return {
        top: 0,
        left: 0,
        bottom: 1,
        right: 1
      };
    },

    set elementRect(t) {}

  }, Za.restrict.defaults),
      _u = {
    start: Za.restrict.start,
    set: Za.restrict.set,
    defaults: wu
  };
  Ou.restrictRect = _u;
  var Pu = (0, Ci.makeModifier)(_u, "restrictRect");
  Ou["default"] = Pu;
  var xu = {};

  function Su(t) {
    return (Su = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(xu, "__esModule", {
    value: !0
  }), xu.restrictSize = xu["default"] = void 0;

  var ju,
      Mu = (ju = ct) && ju.__esModule ? ju : {
    "default": ju
  },
      ku = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Su(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Eu();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }($t);

  function Eu() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Eu = function Eu() {
      return t;
    }, t;
  }

  var Tu = {
    width: -1 / 0,
    height: -1 / 0
  },
      Du = {
    width: 1 / 0,
    height: 1 / 0
  };
  var Iu = {
    start: function start(t) {
      return su.restrictEdges.start(t);
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.state,
          r = t.rect,
          o = t.edges,
          i = n.options;

      if (o) {
        var a = ku.tlbrToXywh((0, Za.getRestrictionRect)(i.min, e, t.coords)) || Tu,
            u = ku.tlbrToXywh((0, Za.getRestrictionRect)(i.max, e, t.coords)) || Du;
        n.options = {
          endOnly: i.endOnly,
          inner: (0, Mu["default"])({}, su.restrictEdges.noInner),
          outer: (0, Mu["default"])({}, su.restrictEdges.noOuter)
        }, o.top ? (n.options.inner.top = r.bottom - a.height, n.options.outer.top = r.bottom - u.height) : o.bottom && (n.options.inner.bottom = r.top + a.height, n.options.outer.bottom = r.top + u.height), o.left ? (n.options.inner.left = r.right - a.width, n.options.outer.left = r.right - u.width) : o.right && (n.options.inner.right = r.left + a.width, n.options.outer.right = r.left + u.width), su.restrictEdges.set(t), n.options = i;
      }
    },
    defaults: {
      min: null,
      max: null,
      endOnly: !1,
      enabled: !1
    }
  };
  xu.restrictSize = Iu;
  var zu = (0, Ci.makeModifier)(Iu, "restrictSize");
  xu["default"] = zu;
  var Au = {};
  Object.defineProperty(Au, "__esModule", {
    value: !0
  }), Au["default"] = void 0;

  function Cu() {}

  Cu._defaults = {};
  var Wu = Cu;
  Au["default"] = Wu;
  var Ru = {};

  function Fu(t) {
    return (Fu = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Ru, "__esModule", {
    value: !0
  }), Ru.snap = Ru["default"] = void 0;

  var Xu = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Fu(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Yu();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(le);

  function Yu() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Yu = function Yu() {
      return t;
    }, t;
  }

  var Nu = {
    start: function start(t) {
      var e,
          n,
          r,
          o = t.interaction,
          i = t.interactable,
          a = t.element,
          u = t.rect,
          s = t.state,
          l = t.startOffset,
          c = s.options,
          f = c.offsetWithOrigin ? (n = (e = t).interaction.element, Xu.rect.rectToXY(Xu.rect.resolveRectLike(e.state.options.origin, null, null, [n])) || Xu.getOriginXY(e.interactable, n, e.interaction.prepared.name)) : {
        x: 0,
        y: 0
      };
      if ("startCoords" === c.offset) r = {
        x: o.coords.start.page.x,
        y: o.coords.start.page.y
      };else {
        var p = Xu.rect.resolveRectLike(c.offset, i, a, [o]);
        (r = Xu.rect.rectToXY(p) || {
          x: 0,
          y: 0
        }).x += f.x, r.y += f.y;
      }
      var d = c.relativePoints;
      s.offsets = u && d && d.length ? d.map(function (t, e) {
        return {
          index: e,
          relativePoint: t,
          x: l.left - u.width * t.x + r.x,
          y: l.top - u.height * t.y + r.y
        };
      }) : [Xu.extend({
        index: 0,
        relativePoint: null
      }, r)];
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.coords,
          r = t.state,
          o = r.options,
          i = r.offsets,
          a = Xu.getOriginXY(e.interactable, e.element, e.prepared.name),
          u = Xu.extend({}, n),
          s = [];
      o.offsetWithOrigin || (u.x -= a.x, u.y -= a.y);

      for (var l = 0; l < i.length; l++) {
        for (var c = i[l], f = u.x - c.x, p = u.y - c.y, d = 0, v = o.targets.length; d < v; d++) {
          var y = o.targets[d],
              h = void 0;
          (h = Xu.is.func(y) ? y(f, p, e, c, d) : y) && s.push({
            x: (Xu.is.number(h.x) ? h.x : f) + c.x,
            y: (Xu.is.number(h.y) ? h.y : p) + c.y,
            range: Xu.is.number(h.range) ? h.range : o.range,
            source: y,
            index: d,
            offset: c
          });
        }
      }

      for (var g = {
        target: null,
        inRange: !1,
        distance: 0,
        range: 0,
        delta: {
          x: 0,
          y: 0
        }
      }, b = 0; b < s.length; b++) {
        var m = s[b],
            O = m.range,
            w = m.x - u.x,
            _ = m.y - u.y,
            P = Xu.hypot(w, _),
            x = P <= O;

        O === 1 / 0 && g.inRange && g.range !== 1 / 0 && (x = !1), g.target && !(x ? g.inRange && O !== 1 / 0 ? P / O < g.distance / g.range : O === 1 / 0 && g.range !== 1 / 0 || P < g.distance : !g.inRange && P < g.distance) || (g.target = m, g.distance = P, g.range = O, g.inRange = x, g.delta.x = w, g.delta.y = _);
      }

      return g.inRange && (n.x = g.target.x, n.y = g.target.y), r.closest = g;
    },
    defaults: {
      range: 1 / 0,
      targets: null,
      offset: null,
      offsetWithOrigin: !0,
      origin: null,
      relativePoints: null,
      endOnly: !1,
      enabled: !1
    }
  };
  Ru.snap = Nu;
  var Lu = (0, Ci.makeModifier)(Nu, "snap");
  Ru["default"] = Lu;
  var Bu = {};

  function Vu(t) {
    return (Vu = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Bu, "__esModule", {
    value: !0
  }), Bu.snapSize = Bu["default"] = void 0;

  var qu,
      Uu = (qu = ct) && qu.__esModule ? qu : {
    "default": qu
  },
      Gu = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Vu(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Hu();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(w);

  function Hu() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Hu = function Hu() {
      return t;
    }, t;
  }

  function Ku(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if (!(Symbol.iterator in Object(t) || "[object Arguments]" === Object.prototype.toString.call(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, u = t[Symbol.iterator](); !(r = (a = u.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == u["return"] || u["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance");
    }();
  }

  var $u = {
    start: function start(t) {
      var e = t.state,
          n = t.edges,
          r = e.options;
      if (!n) return null;
      t.state = {
        options: {
          targets: null,
          relativePoints: [{
            x: n.left ? 0 : 1,
            y: n.top ? 0 : 1
          }],
          offset: r.offset || "self",
          origin: {
            x: 0,
            y: 0
          },
          range: r.range
        }
      }, e.targetFields = e.targetFields || [["width", "height"], ["x", "y"]], Ru.snap.start(t), e.offsets = t.state.offsets, t.state = e;
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.state,
          r = t.coords,
          o = n.options,
          i = n.offsets,
          a = {
        x: r.x - i[0].x,
        y: r.y - i[0].y
      };
      n.options = (0, Uu["default"])({}, o), n.options.targets = [];

      for (var u = 0; u < (o.targets || []).length; u++) {
        var s = (o.targets || [])[u],
            l = void 0;

        if (l = Gu.func(s) ? s(a.x, a.y, e) : s) {
          for (var c = 0; c < n.targetFields.length; c++) {
            var f = Ku(n.targetFields[c], 2),
                p = f[0],
                d = f[1];

            if (p in l || d in l) {
              l.x = l[p], l.y = l[d];
              break;
            }
          }

          n.options.targets.push(l);
        }
      }

      var v = Ru.snap.set(t);
      return n.options = o, v;
    },
    defaults: {
      range: 1 / 0,
      targets: null,
      offset: null,
      endOnly: !1,
      enabled: !1
    }
  };
  Bu.snapSize = $u;
  var Zu = (0, Ci.makeModifier)($u, "snapSize");
  Bu["default"] = Zu;
  var Ju = {};
  Object.defineProperty(Ju, "__esModule", {
    value: !0
  }), Ju.snapEdges = Ju["default"] = void 0;
  var Qu = es(V),
      ts = es(ct);

  function es(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var ns = {
    start: function start(t) {
      var e = t.edges;
      return e ? (t.state.targetFields = t.state.targetFields || [[e.left ? "left" : "right", e.top ? "top" : "bottom"]], Bu.snapSize.start(t)) : null;
    },
    set: Bu.snapSize.set,
    defaults: (0, ts["default"])((0, Qu["default"])(Bu.snapSize.defaults), {
      targets: null,
      range: null,
      offset: {
        x: 0,
        y: 0
      }
    })
  };
  Ju.snapEdges = ns;
  var rs = (0, Ci.makeModifier)(ns, "snapEdges");
  Ju["default"] = rs;
  var os = {};
  Object.defineProperty(os, "__esModule", {
    value: !0
  }), os["default"] = void 0;

  function is() {}

  is._defaults = {};
  var as = is;
  os["default"] = as;
  var us = {};
  Object.defineProperty(us, "__esModule", {
    value: !0
  }), us["default"] = void 0;

  function ss() {}

  ss._defaults = {};
  var ls = ss;
  us["default"] = ls;
  var cs = {};
  Object.defineProperty(cs, "__esModule", {
    value: !0
  }), cs["default"] = void 0;

  var fs = Ps(Ra),
      ps = Ps(Ha),
      ds = Ps(su),
      vs = Ps(Za),
      ys = Ps(Ou),
      hs = Ps(xu),
      gs = Ps(Au),
      bs = Ps(Ju),
      ms = Ps(Ru),
      Os = Ps(Bu),
      ws = Ps(os),
      _s = Ps(us);

  function Ps(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var xs = {
    aspectRatio: fs["default"],
    restrictEdges: ds["default"],
    restrict: vs["default"],
    restrictRect: ys["default"],
    restrictSize: hs["default"],
    snapEdges: bs["default"],
    snap: ms["default"],
    snapSize: Os["default"],
    spring: ws["default"],
    avoid: ps["default"],
    transform: _s["default"],
    rubberband: gs["default"]
  };
  cs["default"] = xs;
  var Ss = {};
  Object.defineProperty(Ss, "__esModule", {
    value: !0
  }), Ss["default"] = void 0;
  var js = Es(Ta),
      Ms = Es(cs),
      ks = Es(Ci);

  function Es(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var Ts = {
    id: "modifiers",
    install: function install(t) {
      var e = t.interactStatic;

      for (var n in t.usePlugin(ks["default"]), t.usePlugin(js["default"]), e.modifiers = Ms["default"], Ms["default"]) {
        var r = Ms["default"][n],
            o = r._defaults,
            i = r._methods;
        o._methods = i, t.defaults.perAction[n] = o;
      }
    }
  };
  Ss["default"] = Ts;
  var Ds = {};
  Object.defineProperty(Ds, "__esModule", {
    value: !0
  }), Ds["default"] = void 0;
  Ds["default"] = {};
  var Is = {};
  Object.defineProperty(Is, "__esModule", {
    value: !0
  }), Is.PointerEvent = Is["default"] = void 0;

  var zs,
      As = (zs = Me) && zs.__esModule ? zs : {
    "default": zs
  },
      Cs = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Rs(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Ws();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(zt);

  function Ws() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Ws = function Ws() {
      return t;
    }, t;
  }

  function Rs(t) {
    return (Rs = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function Fs(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Xs(t) {
    return (Xs = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function Ys(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function Ns(t, e) {
    return (Ns = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function Ls(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  var Bs = function () {
    function f(t, e, n, r, o, i) {
      var a, u, s;

      if (!function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, f), u = this, a = !(s = Xs(f).call(this, o)) || "object" !== Rs(s) && "function" != typeof s ? Ys(u) : s, Ls(Ys(a), "type", void 0), Ls(Ys(a), "originalEvent", void 0), Ls(Ys(a), "pointerId", void 0), Ls(Ys(a), "pointerType", void 0), Ls(Ys(a), "double", void 0), Ls(Ys(a), "pageX", void 0), Ls(Ys(a), "pageY", void 0), Ls(Ys(a), "clientX", void 0), Ls(Ys(a), "clientY", void 0), Ls(Ys(a), "dt", void 0), Ls(Ys(a), "eventable", void 0), Cs.pointerExtend(Ys(a), n), n !== e && Cs.pointerExtend(Ys(a), e), a.timeStamp = i, a.originalEvent = n, a.type = t, a.pointerId = Cs.getPointerId(e), a.pointerType = Cs.getPointerType(e), a.target = r, a.currentTarget = null, "tap" === t) {
        var l = o.getPointerIndex(e);
        a.dt = a.timeStamp - o.pointers[l].downTime;
        var c = a.timeStamp - o.tapTime;
        a["double"] = !!(o.prevTap && "doubletap" !== o.prevTap.type && o.prevTap.target === a.target && c < 500);
      } else "doubletap" === t && (a.dt = e.timeStamp - o.tapTime);

      return a;
    }

    var t, e, n;
    return function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && Ns(t, e);
    }(f, As["default"]), t = f, (e = [{
      key: "_subtractOrigin",
      value: function value(t) {
        var e = t.x,
            n = t.y;
        return this.pageX -= e, this.pageY -= n, this.clientX -= e, this.clientY -= n, this;
      }
    }, {
      key: "_addOrigin",
      value: function value(t) {
        var e = t.x,
            n = t.y;
        return this.pageX += e, this.pageY += n, this.clientX += e, this.clientY += n, this;
      }
    }, {
      key: "preventDefault",
      value: function value() {
        this.originalEvent.preventDefault();
      }
    }]) && Fs(t.prototype, e), n && Fs(t, n), f;
  }();

  Is.PointerEvent = Is["default"] = Bs;
  var Vs = {};

  function qs(t) {
    return (qs = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Vs, "__esModule", {
    value: !0
  }), Vs["default"] = void 0;
  Ks(En), Ks(m({}));

  var Us = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== qs(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Hs();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(le),
      Gs = Ks(Is);

  function Hs() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Hs = function Hs() {
      return t;
    }, t;
  }

  function Ks(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  var $s = {
    id: "pointer-events/base",
    install: function install(t) {
      t.pointerEvents = $s, t.defaults.actions.pointerEvents = $s.defaults, Us.extend(t.actions.phaselessTypes, $s.types);
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        var e = t.interaction;
        e.prevTap = null, e.tapTime = 0;
      },
      "interactions:update-pointer": function interactionsUpdatePointer(t) {
        var e = t.down,
            n = t.pointerInfo;
        if (!e && n.hold) return;
        n.hold = {
          duration: 1 / 0,
          timeout: null
        };
      },
      "interactions:move": function interactionsMove(t, e) {
        var n = t.interaction,
            r = t.pointer,
            o = t.event,
            i = t.eventTarget,
            a = t.duplicate,
            u = n.getPointerIndex(r);
        a || n.pointerIsDown && !n.pointerWasMoved || (n.pointerIsDown && clearTimeout(n.pointers[u].hold.timeout), Zs({
          interaction: n,
          pointer: r,
          event: o,
          eventTarget: i,
          type: "move"
        }, e));
      },
      "interactions:down": function interactionsDown(t, e) {
        !function (t, e) {
          for (var n = t.interaction, r = t.pointer, o = t.event, i = t.eventTarget, a = t.pointerIndex, u = n.pointers[a].hold, s = Us.dom.getPath(i), l = {
            interaction: n,
            pointer: r,
            event: o,
            eventTarget: i,
            type: "hold",
            targets: [],
            path: s,
            node: null
          }, c = 0; c < s.length; c++) {
            var f = s[c];
            l.node = f, e.fire("pointerEvents:collect-targets", l);
          }

          if (!l.targets.length) return;

          for (var p = 1 / 0, d = 0; d < l.targets.length; d++) {
            var v = l.targets[d].eventable.options.holdDuration;
            v < p && (p = v);
          }

          u.duration = p, u.timeout = setTimeout(function () {
            Zs({
              interaction: n,
              eventTarget: i,
              pointer: r,
              event: o,
              type: "hold"
            }, e);
          }, p);
        }(t, e), Zs(t, e);
      },
      "interactions:up": function interactionsUp(t, e) {
        var n, r, o, i, a, u;
        Qs(t), Zs(t, e), r = e, o = (n = t).interaction, i = n.pointer, a = n.event, u = n.eventTarget, o.pointerWasMoved || Zs({
          interaction: o,
          eventTarget: u,
          pointer: i,
          event: a,
          type: "tap"
        }, r);
      },
      "interactions:cancel": function interactionsCancel(t, e) {
        Qs(t), Zs(t, e);
      }
    },
    PointerEvent: Gs["default"],
    fire: Zs,
    collectEventTargets: Js,
    defaults: {
      holdDuration: 600,
      ignoreFrom: null,
      allowFrom: null,
      origin: {
        x: 0,
        y: 0
      }
    },
    types: {
      down: !0,
      move: !0,
      up: !0,
      cancel: !0,
      tap: !0,
      doubletap: !0,
      hold: !0
    }
  };

  function Zs(t, e) {
    var n = t.interaction,
        r = t.pointer,
        o = t.event,
        i = t.eventTarget,
        a = t.type,
        u = t.targets,
        s = void 0 === u ? Js(t, e) : u,
        l = new Gs["default"](a, r, o, i, n, e.now());
    e.fire("pointerEvents:new", {
      pointerEvent: l
    });

    for (var c = {
      interaction: n,
      pointer: r,
      event: o,
      eventTarget: i,
      targets: s,
      type: a,
      pointerEvent: l
    }, f = 0; f < s.length; f++) {
      var p = s[f];

      for (var d in p.props || {}) {
        l[d] = p.props[d];
      }

      var v = Us.getOriginXY(p.eventable, p.node);
      if (l._subtractOrigin(v), l.eventable = p.eventable, l.currentTarget = p.node, p.eventable.fire(l), l._addOrigin(v), l.immediatePropagationStopped || l.propagationStopped && f + 1 < s.length && s[f + 1].node !== l.currentTarget) break;
    }

    if (e.fire("pointerEvents:fired", c), "tap" === a) {
      var y = l["double"] ? Zs({
        interaction: n,
        pointer: r,
        event: o,
        eventTarget: i,
        type: "doubletap"
      }, e) : l;
      n.prevTap = y, n.tapTime = y.timeStamp;
    }

    return l;
  }

  function Js(t, e) {
    var n = t.interaction,
        r = t.pointer,
        o = t.event,
        i = t.eventTarget,
        a = t.type,
        u = n.getPointerIndex(r),
        s = n.pointers[u];
    if ("tap" === a && (n.pointerWasMoved || !s || s.downTarget !== i)) return [];

    for (var l = Us.dom.getPath(i), c = {
      interaction: n,
      pointer: r,
      event: o,
      eventTarget: i,
      type: a,
      path: l,
      targets: [],
      node: null
    }, f = 0; f < l.length; f++) {
      var p = l[f];
      c.node = p, e.fire("pointerEvents:collect-targets", c);
    }

    return "hold" === a && (c.targets = c.targets.filter(function (t) {
      return t.eventable.options.holdDuration === n.pointers[u].hold.duration;
    })), c.targets;
  }

  function Qs(t) {
    var e = t.interaction,
        n = t.pointerIndex;
    e.pointers[n].hold && clearTimeout(e.pointers[n].hold.timeout);
  }

  var tl = $s;
  Vs["default"] = tl;
  var el = {};
  Object.defineProperty(el, "__esModule", {
    value: !0
  }), el["default"] = void 0;
  rl(Is);
  var nl = rl(Vs);

  function rl(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function ol(t) {
    var e = t.interaction;
    e.holdIntervalHandle && (clearInterval(e.holdIntervalHandle), e.holdIntervalHandle = null);
  }

  var il = {
    id: "pointer-events/holdRepeat",
    install: function install(t) {
      t.usePlugin(nl["default"]);
      var e = t.pointerEvents;
      e.defaults.holdRepeatInterval = 0, e.types.holdrepeat = t.actions.phaselessTypes.holdrepeat = !0;
    },
    listeners: ["move", "up", "cancel", "endall"].reduce(function (t, e) {
      return t["pointerEvents:".concat(e)] = ol, t;
    }, {
      "pointerEvents:new": function pointerEventsNew(t) {
        var e = t.pointerEvent;
        "hold" === e.type && (e.count = (e.count || 0) + 1);
      },
      "pointerEvents:fired": function pointerEventsFired(t, e) {
        var n = t.interaction,
            r = t.pointerEvent,
            o = t.eventTarget,
            i = t.targets;

        if ("hold" === r.type && i.length) {
          var a = i[0].eventable.options.holdRepeatInterval;
          a <= 0 || (n.holdIntervalHandle = setTimeout(function () {
            e.pointerEvents.fire({
              interaction: n,
              eventTarget: o,
              type: "hold",
              pointer: r,
              event: r
            }, e);
          }, a));
        }
      }
    })
  };
  el["default"] = il;
  var al = {};
  Object.defineProperty(al, "__esModule", {
    value: !0
  }), al["default"] = void 0;
  var ul,
      sl = (ul = ct) && ul.__esModule ? ul : {
    "default": ul
  };

  function ll(t) {
    return (0, sl["default"])(this.events.options, t), this;
  }

  var cl = {
    id: "pointer-events/interactableTargets",
    install: function install(t) {
      var e = t.Interactable;
      e.prototype.pointerEvents = ll;
      var r = e.prototype._backCompatOption;

      e.prototype._backCompatOption = function (t, e) {
        var n = r.call(this, t, e);
        return n === this && (this.events.options[t] = e), n;
      };
    },
    listeners: {
      "pointerEvents:collect-targets": function pointerEventsCollectTargets(t, e) {
        var r = t.targets,
            o = t.node,
            i = t.type,
            a = t.eventTarget;
        e.interactables.forEachMatch(o, function (t) {
          var e = t.events,
              n = e.options;
          e.types[i] && e.types[i].length && t.testIgnoreAllow(n, o, a) && r.push({
            node: o,
            eventable: e,
            props: {
              interactable: t
            }
          });
        });
      },
      "interactable:new": function interactableNew(t) {
        var e = t.interactable;

        e.events.getRect = function (t) {
          return e.getRect(t);
        };
      },
      "interactable:set": function interactableSet(t, e) {
        var n = t.interactable,
            r = t.options;
        (0, sl["default"])(n.events.options, e.pointerEvents.defaults), (0, sl["default"])(n.events.options, r.pointerEvents || {});
      }
    }
  };
  al["default"] = cl;
  var fl = {};

  function pl(t) {
    return (pl = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(fl, "__esModule", {
    value: !0
  }), Object.defineProperty(fl, "holdRepeat", {
    enumerable: !0,
    get: function get() {
      return vl["default"];
    }
  }), Object.defineProperty(fl, "interactableTargets", {
    enumerable: !0,
    get: function get() {
      return yl["default"];
    }
  }), fl.pointerEvents = fl["default"] = void 0;

  var dl = function (t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== pl(t) && "function" != typeof t) return {
      "default": t
    };
    var e = gl();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    n["default"] = t, e && e.set(t, n);
    return n;
  }(Vs);

  fl.pointerEvents = dl;
  var vl = hl(el),
      yl = hl(al);

  function hl(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  function gl() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return gl = function gl() {
      return t;
    }, t;
  }

  var bl = {
    id: "pointer-events",
    install: function install(t) {
      t.usePlugin(dl), t.usePlugin(vl["default"]), t.usePlugin(yl["default"]);
    }
  };
  fl["default"] = bl;
  var ml = {};
  Object.defineProperty(ml, "__esModule", {
    value: !0
  }), ml.install = wl, ml["default"] = void 0;
  var Ol;
  (Ol = k({})) && Ol.__esModule;

  function wl(e) {
    var t = e.Interactable;
    e.actions.phases.reflow = !0, t.prototype.reflow = function (t) {
      return function (u, s, l) {
        function t() {
          var e = c[d],
              t = u.getRect(e);
          if (!t) return "break";
          var n = le.arr.find(l.interactions.list, function (t) {
            return t.interacting() && t.interactable === u && t.element === e && t.prepared.name === s.name;
          }),
              r = void 0;
          if (n) n.move(), p && (r = n._reflowPromise || new f(function (t) {
            n._reflowResolve = t;
          }));else {
            var o = le.rect.tlbrToXywh(t),
                i = {
              page: {
                x: o.x,
                y: o.y
              },
              client: {
                x: o.x,
                y: o.y
              },
              timeStamp: l.now()
            },
                a = le.pointer.coordsToEvent(i);

            r = function (t, e, n, r, o) {
              var i = t.interactions["new"]({
                pointerType: "reflow"
              }),
                  a = {
                interaction: i,
                event: o,
                pointer: o,
                eventTarget: n,
                phase: "reflow"
              };
              i.interactable = e, i.element = n, i.prepared = (0, le.extend)({}, r), i.prevEvent = o, i.updatePointer(o, o, n, !0), i._doPhase(a);
              var u = le.win.window.Promise ? new le.win.window.Promise(function (t) {
                i._reflowResolve = t;
              }) : null;
              i._reflowPromise = u, i.start(r, e, n), i._interacting ? (i.move(a), i.end(o)) : i.stop();
              return i.removePointer(o, o), i.pointerIsDown = !1, u;
            }(l, u, e, s, a);
          }
          p && p.push(r);
        }

        for (var c = le.is.string(u.target) ? le.arr.from(u._context.querySelectorAll(u.target)) : [u.target], f = le.win.window.Promise, p = f ? [] : null, d = 0; d < c.length; d++) {
          if ("break" === t()) break;
        }

        return p && f.all(p).then(function () {
          return u;
        });
      }(this, t, e);
    };
  }

  var _l = {
    id: "reflow",
    install: wl,
    listeners: {
      "interactions:stop": function interactionsStop(t, e) {
        var n = t.interaction;
        "reflow" === n.pointerType && (n._reflowResolve && n._reflowResolve(), le.arr.remove(e.interactions.list, n));
      }
    }
  };
  ml["default"] = _l;
  var Pl = {};
  Object.defineProperty(Pl, "__esModule", {
    value: !0
  }), Pl["default"] = void 0;
  Pl["default"] = {};
  var xl = {};
  Object.defineProperty(xl, "__esModule", {
    value: !0
  }), xl.exchange = void 0;
  xl.exchange = {};
  var Sl = {};
  Object.defineProperty(Sl, "__esModule", {
    value: !0
  }), Sl["default"] = void 0;
  Sl["default"] = {};
  var jl = {};

  function Ml(t) {
    return (Ml = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(jl, "__esModule", {
    value: !0
  }), jl["default"] = void 0;
  var kl = Hl(Qr),
      El = Hl(ao),
      Tl = Hl(uo),
      Dl = Hl(ti),
      Il = Hl(ai),
      zl = Hl(ui),
      Al = Hl(Un),
      Cl = (Hl(si), Gl(wi)),
      Wl = Hl($i),
      Rl = Hl(ha),
      Fl = Hl(Ss),
      Xl = Hl(Ds),
      Yl = Hl(Yi),
      Nl = Hl(fl),
      Ll = Hl(ml),
      Bl = Gl(Pl),
      Vl = Gl(zt),
      ql = Gl(Sl);

  function Ul() {
    if ("function" != typeof WeakMap) return null;
    var t = new WeakMap();
    return Ul = function Ul() {
      return t;
    }, t;
  }

  function Gl(t) {
    if (t && t.__esModule) return t;
    if (null === t || "object" !== Ml(t) && "function" != typeof t) return {
      "default": t
    };
    var e = Ul();
    if (e && e.has(t)) return e.get(t);
    var n = {},
        r = Object.defineProperty && Object.getOwnPropertyDescriptor;

    for (var o in t) {
      if (Object.prototype.hasOwnProperty.call(t, o)) {
        var i = r ? Object.getOwnPropertyDescriptor(t, o) : null;
        i && (i.get || i.set) ? Object.defineProperty(n, o, i) : n[o] = t[o];
      }
    }

    return n["default"] = t, e && e.set(t, n), n;
  }

  function Hl(t) {
    return t && t.__esModule ? t : {
      "default": t
    };
  }

  Rl["default"].use(Xl["default"]), Rl["default"].use(Al["default"]), Rl["default"].use(Yl["default"]), Rl["default"].use(Il["default"]), Rl["default"].use(El["default"]), Rl["default"].use(Nl["default"]), Rl["default"].use(Wl["default"]), Rl["default"].use(Fl["default"]), Rl["default"].use(Dl["default"]), Rl["default"].use(kl["default"]), Rl["default"].use(Tl["default"]), Rl["default"].use(Ll["default"]), Rl["default"].feedback = Cl, Rl["default"].use(zl["default"]), Rl["default"].vue = {
    components: ql
  }, Rl["default"].__utils = {
    exchange: xl.exchange,
    displace: Bl,
    pointer: Vl
  };
  var Kl = Rl["default"];
  jl["default"] = Kl;
  var $l = {
    exports: {}
  };
  Object.defineProperty($l.exports, "__esModule", {
    value: !0
  }), $l.exports["default"] = void 0;
  var Zl,
      Jl = (Zl = jl) && Zl.__esModule ? Zl : {
    "default": Zl
  };

  function Ql(t) {
    return (Ql = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  if ("object" === Ql($l) && $l) try {
    $l.exports = Jl["default"];
  } catch (t) {}
  Jl["default"]["default"] = Jl["default"];
  var tc = Jl["default"];
  return $l.exports["default"] = tc, $l = $l.exports;
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var interactjs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! interactjs */ "./node_modules/@concretecms/bedrock/node_modules/interactjs/dist/interact.min.js");
/* harmony import */ var interactjs__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(interactjs__WEBPACK_IMPORTED_MODULE_0__);

/* harmony default export */ __webpack_exports__["default"] = ({
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
    } // Emit an event


    this.$emit('mount', this);
  },
  methods: {
    /**
     */
    guessPosition: function guessPosition() {
      var adjustedHeight = this.adjustedDimensions(this.imageWidth, this.imageHeight);
      var adjX = 0;
      var adjY = 0;
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
      this.interact = interactjs__WEBPACK_IMPORTED_MODULE_0___default()(this.$refs.image).resizable({
        preserveAspectRatio: true,
        edges: {
          left: true,
          right: true,
          bottom: true,
          top: true
        }
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
      var bestFactor = 1;
      var maxFactor = Math.sqrt(Math.min(width, height)); // Find the best factor to downsize by

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
      var renderedX = this.x + x;
      var renderedY = this.y + y;
      var coords = {
        min: {
          x: -1 * (width - this.cropperWidth),
          y: -1 * (height - this.cropperHeight)
        },
        max: {
          x: 0,
          y: 0
        }
      };
      var adjustedX = Math.max(coords.min.x, Math.min(coords.max.x, renderedX)) - this.x;
      var adjustedY = Math.max(coords.min.y, Math.min(coords.max.y, renderedY)) - this.y;
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
      this.interact = interactjs__WEBPACK_IMPORTED_MODULE_0___default()(this.$refs.image).draggable({
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
      var coordinates = this.adjustedCoordinates(event.deltaRect.left, event.deltaRect.top, event.rect.width, event.rect.height); // Don't resize too small

      if (event.rect.width < this.cropperWidth || event.rect.height < this.cropperHeight) {
        // If the image is square
        if (this.imageWidth === this.imageHeight) {
          this.resizeWidth = this.cropperWidth;
          this.resizeHeight = this.cropperHeight;
          this.x = 0;
          this.y = 0;
        }

        return;
      } // update the element's style


      this.resizeWidth = Math.max(event.rect.width, this.cropperWidth);
      this.resizeHeight = Math.max(event.rect.height, this.cropperHeight); // translate when resizing from top or left edges

      this.x += coordinates.x;
      this.y += coordinates.y;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var dropzone__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! dropzone */ "./node_modules/@concretecms/bedrock/node_modules/dropzone/dist/dropzone.js");
/* harmony import */ var dropzone__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(dropzone__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Avatar_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Avatar.vue */ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue");

 // Disable dropzone discovery

dropzone__WEBPACK_IMPORTED_MODULE_0___default.a.autoDiscover = false;
/* harmony default export */ __webpack_exports__["default"] = ({
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
    this.currentimage = this.src; // Setup Uploading

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
      this.dropzone = new dropzone__WEBPACK_IMPORTED_MODULE_0___default.a(this.$refs.dropzone, {
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
      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext('2d');
      var img = new Image();
      img.src = file.dataURL;
      canvas.width = this.width;
      canvas.height = this.height; // Draw the image cropped

      ctx.drawImage(img, this.$refs.image.x, this.$refs.image.y, this.$refs.image.resizeWidth, this.$refs.image.resizeHeight);
      this.saving = true; // Complete the upload

      var data = canvas.toDataURL();
      var result = done(dropzone__WEBPACK_IMPORTED_MODULE_0___default.a.dataURItoBlob(data));
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
    Avatar: _Avatar_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "img[data-v-547cd8e4] {\n  left: 0;\n  max-width: inherit;\n  min-width: inherit;\n  top: 0;\n}\n.shadow[data-v-547cd8e4] {\n  box-shadow: 0 0 10px black;\n  box-sizing: border-box;\n  left: 1px;\n  opacity: 0.3;\n  outline: solid 1px #333;\n  position: absolute;\n  top: 1px;\n  z-index: 1;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "@charset \"UTF-8\";\n/* stylelint-disable property-no-vendor-prefix, property-no-unkown */\n.ccm-avatar-creator-container[data-v-838c3fb0] {\n  position: relative;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions[data-v-838c3fb0] {\n  position: absolute;\n  z-index: 20000;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-838c3fb0] {\n  display: inline-block;\n  font-weight: 600;\n  opacity: 0.8;\n  text-align: center;\n  text-decoration: none;\n  transition: all 0.5s;\n  width: 50%;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-838c3fb0]:hover {\n  opacity: 1;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-838c3fb0]::before {\n  font-family: \"Font Awesome 5 Free\";\n  font-size: 16px;\n  text-align: center;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-cancel[data-v-838c3fb0] {\n  color: #ff4136;\n  float: right;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-cancel[data-v-838c3fb0]::before {\n  content: \"\\F00D\";\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-okay[data-v-838c3fb0] {\n  color: #3d9970;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-okay[data-v-838c3fb0]::before {\n  content: \"\\F00C\";\n}\n.ccm-avatar-creator-container .ccm-avatar-creator[data-v-838c3fb0] {\n  border: solid 1px #999;\n  overflow: hidden;\n  position: relative;\n  transition: all 0.3s;\n  z-index: 10000;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator > img.ccm-avatar-current[data-v-838c3fb0] {\n  display: inline;\n  height: 100%;\n  width: 100%;\n  z-index: 998;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator > div.saving[data-v-838c3fb0] {\n  background: rgba(127, 219, 255, 0.5);\n  color: #111;\n  font-size: 16px;\n  font-weight: bolder;\n  height: 100%;\n  left: 0;\n  position: absolute;\n  text-align: center;\n  top: 0;\n  width: 100%;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator[data-v-838c3fb0]::before {\n  background-color: rgba(238, 238, 238, 0.8);\n  color: #3d9970;\n  content: \"\\F303\";\n  display: block;\n  font-family: \"Font Awesome 5 Free\";\n  font-weight: 600;\n  height: 100%;\n  line-height: 0%;\n  margin: 0 auto;\n  opacity: 0;\n  padding-top: 50%;\n  position: absolute;\n  text-align: center;\n  transition: all 0.3s;\n  vertical-align: middle;\n  width: 100%;\n  z-index: 999;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-started[data-v-838c3fb0]::before {\n  -webkit-animation: pulse-data-v-838c3fb0 1s infinite;\n  animation: pulse-data-v-838c3fb0 1s infinite;\n  opacity: 1;\n}\n@-webkit-keyframes pulse-data-v-838c3fb0 {\n0% {\n    transform: scale(1);\n}\n50% {\n    transform: scale(1.3);\n}\n100% {\n    transform: scale(1);\n}\n}\n@keyframes pulse-data-v-838c3fb0 {\n0% {\n    transform: scale(1);\n}\n50% {\n    transform: scale(1.3);\n}\n100% {\n    transform: scale(1);\n}\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.editing[data-v-838c3fb0]::before {\n  display: none;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-838c3fb0] {\n  cursor: \"pointer\";\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-838c3fb0]:hover, .ccm-avatar-creator-container .ccm-avatar-creator.dz-drag-hover[data-v-838c3fb0] {\n  border-color: #3d9970;\n  box-shadow: 0 0 20px -10px #2ecc40;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-838c3fb0]:hover::before, .ccm-avatar-creator-container .ccm-avatar-creator.dz-drag-hover[data-v-838c3fb0]::before {\n  opacity: 1;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/lib/css-base.js":
/*!*************************************************!*\
  !*** ./node_modules/css-loader/lib/css-base.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function (useSourceMap) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item, useSourceMap);

      if (item[2]) {
        return "@media " + item[2] + "{" + content + "}";
      } else {
        return content;
      }
    }).join("");
  }; // import a list of modules into the list


  list.i = function (modules, mediaQuery) {
    if (typeof modules === "string") modules = [[null, modules, ""]];
    var alreadyImportedModules = {};

    for (var i = 0; i < this.length; i++) {
      var id = this[i][0];
      if (typeof id === "number") alreadyImportedModules[id] = true;
    }

    for (i = 0; i < modules.length; i++) {
      var item = modules[i]; // skip already imported module
      // this implementation is not 100% perfect for weird media query combinations
      //  when a module is imported multiple times with different media queries.
      //  I hope this will never occur (Hey this way we have smaller bundles)

      if (typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
        if (mediaQuery && !item[2]) {
          item[2] = mediaQuery;
        } else if (mediaQuery) {
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
      return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */';
    });
    return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
  }

  return [content].join('\n');
} // Adapted from convert-source-map (MIT)


function toComment(sourceMap) {
  // eslint-disable-next-line no-undef
  var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
  var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;
  return '/*# ' + data + ' */';
}

/***/ }),

/***/ "./node_modules/nprogress/nprogress.js":
/*!*********************************************!*\
  !*** ./node_modules/nprogress/nprogress.js ***!
  \*********************************************/
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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--28-2!../../../../../../../../sass-loader/dist/cjs.js??ref--28-3!./Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../../style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--28-2!../../../../../../../../sass-loader/dist/cjs.js??ref--28-3!./Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../../style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/lib/addStyles.js":
/*!****************************************************!*\
  !*** ./node_modules/style-loader/lib/addStyles.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

var stylesInDom = {};

var	memoize = function (fn) {
	var memo;

	return function () {
		if (typeof memo === "undefined") memo = fn.apply(this, arguments);
		return memo;
	};
};

var isOldIE = memoize(function () {
	// Test for IE <= 9 as proposed by Browserhacks
	// @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
	// Tests for existence of standard globals is to allow style-loader
	// to operate correctly into non-standard environments
	// @see https://github.com/webpack-contrib/style-loader/issues/177
	return window && document && document.all && !window.atob;
});

var getTarget = function (target, parent) {
  if (parent){
    return parent.querySelector(target);
  }
  return document.querySelector(target);
};

var getElement = (function (fn) {
	var memo = {};

	return function(target, parent) {
                // If passing function in options, then use it for resolve "head" element.
                // Useful for Shadow Root style i.e
                // {
                //   insertInto: function () { return document.querySelector("#foo").shadowRoot }
                // }
                if (typeof target === 'function') {
                        return target();
                }
                if (typeof memo[target] === "undefined") {
			var styleTarget = getTarget.call(this, target, parent);
			// Special case to return head of iframe instead of iframe itself
			if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
				try {
					// This will throw an exception if access to iframe is blocked
					// due to cross-origin restrictions
					styleTarget = styleTarget.contentDocument.head;
				} catch(e) {
					styleTarget = null;
				}
			}
			memo[target] = styleTarget;
		}
		return memo[target]
	};
})();

var singleton = null;
var	singletonCounter = 0;
var	stylesInsertedAtTop = [];

var	fixUrls = __webpack_require__(/*! ./urls */ "./node_modules/style-loader/lib/urls.js");

module.exports = function(list, options) {
	if (typeof DEBUG !== "undefined" && DEBUG) {
		if (typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};

	options.attrs = typeof options.attrs === "object" ? options.attrs : {};

	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (!options.singleton && typeof options.singleton !== "boolean") options.singleton = isOldIE();

	// By default, add <style> tags to the <head> element
        if (!options.insertInto) options.insertInto = "head";

	// By default, add <style> tags to the bottom of the target
	if (!options.insertAt) options.insertAt = "bottom";

	var styles = listToStyles(list, options);

	addStylesToDom(styles, options);

	return function update (newList) {
		var mayRemove = [];

		for (var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];

			domStyle.refs--;
			mayRemove.push(domStyle);
		}

		if(newList) {
			var newStyles = listToStyles(newList, options);
			addStylesToDom(newStyles, options);
		}

		for (var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];

			if(domStyle.refs === 0) {
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();

				delete stylesInDom[domStyle.id];
			}
		}
	};
};

function addStylesToDom (styles, options) {
	for (var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];

		if(domStyle) {
			domStyle.refs++;

			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}

			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];

			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}

			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles (list, options) {
	var styles = [];
	var newStyles = {};

	for (var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = options.base ? item[0] + options.base : item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};

		if(!newStyles[id]) styles.push(newStyles[id] = {id: id, parts: [part]});
		else newStyles[id].parts.push(part);
	}

	return styles;
}

function insertStyleElement (options, style) {
	var target = getElement(options.insertInto)

	if (!target) {
		throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
	}

	var lastStyleElementInsertedAtTop = stylesInsertedAtTop[stylesInsertedAtTop.length - 1];

	if (options.insertAt === "top") {
		if (!lastStyleElementInsertedAtTop) {
			target.insertBefore(style, target.firstChild);
		} else if (lastStyleElementInsertedAtTop.nextSibling) {
			target.insertBefore(style, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			target.appendChild(style);
		}
		stylesInsertedAtTop.push(style);
	} else if (options.insertAt === "bottom") {
		target.appendChild(style);
	} else if (typeof options.insertAt === "object" && options.insertAt.before) {
		var nextSibling = getElement(options.insertAt.before, target);
		target.insertBefore(style, nextSibling);
	} else {
		throw new Error("[Style Loader]\n\n Invalid value for parameter 'insertAt' ('options.insertAt') found.\n Must be 'top', 'bottom', or Object.\n (https://github.com/webpack-contrib/style-loader#insertat)\n");
	}
}

function removeStyleElement (style) {
	if (style.parentNode === null) return false;
	style.parentNode.removeChild(style);

	var idx = stylesInsertedAtTop.indexOf(style);
	if(idx >= 0) {
		stylesInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement (options) {
	var style = document.createElement("style");

	if(options.attrs.type === undefined) {
		options.attrs.type = "text/css";
	}

	if(options.attrs.nonce === undefined) {
		var nonce = getNonce();
		if (nonce) {
			options.attrs.nonce = nonce;
		}
	}

	addAttrs(style, options.attrs);
	insertStyleElement(options, style);

	return style;
}

function createLinkElement (options) {
	var link = document.createElement("link");

	if(options.attrs.type === undefined) {
		options.attrs.type = "text/css";
	}
	options.attrs.rel = "stylesheet";

	addAttrs(link, options.attrs);
	insertStyleElement(options, link);

	return link;
}

function addAttrs (el, attrs) {
	Object.keys(attrs).forEach(function (key) {
		el.setAttribute(key, attrs[key]);
	});
}

function getNonce() {
	if (false) {}

	return __webpack_require__.nc;
}

function addStyle (obj, options) {
	var style, update, remove, result;

	// If a transform function was defined, run it on the css
	if (options.transform && obj.css) {
	    result = typeof options.transform === 'function'
		 ? options.transform(obj.css) 
		 : options.transform.default(obj.css);

	    if (result) {
	    	// If transform returns a value, use that instead of the original css.
	    	// This allows running runtime transformations on the css.
	    	obj.css = result;
	    } else {
	    	// If the transform function returns a falsy value, don't add this css.
	    	// This allows conditional loading of css
	    	return function() {
	    		// noop
	    	};
	    }
	}

	if (options.singleton) {
		var styleIndex = singletonCounter++;

		style = singleton || (singleton = createStyleElement(options));

		update = applyToSingletonTag.bind(null, style, styleIndex, false);
		remove = applyToSingletonTag.bind(null, style, styleIndex, true);

	} else if (
		obj.sourceMap &&
		typeof URL === "function" &&
		typeof URL.createObjectURL === "function" &&
		typeof URL.revokeObjectURL === "function" &&
		typeof Blob === "function" &&
		typeof btoa === "function"
	) {
		style = createLinkElement(options);
		update = updateLink.bind(null, style, options);
		remove = function () {
			removeStyleElement(style);

			if(style.href) URL.revokeObjectURL(style.href);
		};
	} else {
		style = createStyleElement(options);
		update = applyToTag.bind(null, style);
		remove = function () {
			removeStyleElement(style);
		};
	}

	update(obj);

	return function updateStyle (newObj) {
		if (newObj) {
			if (
				newObj.css === obj.css &&
				newObj.media === obj.media &&
				newObj.sourceMap === obj.sourceMap
			) {
				return;
			}

			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;

		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag (style, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (style.styleSheet) {
		style.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = style.childNodes;

		if (childNodes[index]) style.removeChild(childNodes[index]);

		if (childNodes.length) {
			style.insertBefore(cssNode, childNodes[index]);
		} else {
			style.appendChild(cssNode);
		}
	}
}

function applyToTag (style, obj) {
	var css = obj.css;
	var media = obj.media;

	if(media) {
		style.setAttribute("media", media)
	}

	if(style.styleSheet) {
		style.styleSheet.cssText = css;
	} else {
		while(style.firstChild) {
			style.removeChild(style.firstChild);
		}

		style.appendChild(document.createTextNode(css));
	}
}

function updateLink (link, options, obj) {
	var css = obj.css;
	var sourceMap = obj.sourceMap;

	/*
		If convertToAbsoluteUrls isn't defined, but sourcemaps are enabled
		and there is no publicPath defined then lets turn convertToAbsoluteUrls
		on by default.  Otherwise default to the convertToAbsoluteUrls option
		directly
	*/
	var autoFixUrls = options.convertToAbsoluteUrls === undefined && sourceMap;

	if (options.convertToAbsoluteUrls || autoFixUrls) {
		css = fixUrls(css);
	}

	if (sourceMap) {
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	var blob = new Blob([css], { type: "text/css" });

	var oldSrc = link.href;

	link.href = URL.createObjectURL(blob);

	if(oldSrc) URL.revokeObjectURL(oldSrc);
}


/***/ }),

/***/ "./node_modules/style-loader/lib/urls.js":
/*!***********************************************!*\
  !*** ./node_modules/style-loader/lib/urls.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * When source maps are enabled, `style-loader` uses a link element with a data-uri to
 * embed the css on the page. This breaks all relative urls because now they are relative to a
 * bundle instead of the current page.
 *
 * One solution is to only use full urls, but that may be impossible.
 *
 * Instead, this function "fixes" the relative urls to be absolute according to the current page location.
 *
 * A rudimentary test suite is located at `test/fixUrls.js` and can be run via the `npm test` command.
 *
 */
module.exports = function (css) {
  // get current location
  var location = typeof window !== "undefined" && window.location;

  if (!location) {
    throw new Error("fixUrls requires window.location");
  } // blank or null?


  if (!css || typeof css !== "string") {
    return css;
  }

  var baseUrl = location.protocol + "//" + location.host;
  var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/"); // convert each url(...)

  /*
  This regular expression is just a way to recursively match brackets within
  a string.
  	 /url\s*\(  = Match on the word "url" with any whitespace after it and then a parens
     (  = Start a capturing group
       (?:  = Start a non-capturing group
           [^)(]  = Match anything that isn't a parentheses
           |  = OR
           \(  = Match a start parentheses
               (?:  = Start another non-capturing groups
                   [^)(]+  = Match anything that isn't a parentheses
                   |  = OR
                   \(  = Match a start parentheses
                       [^)(]*  = Match anything that isn't a parentheses
                   \)  = Match a end parentheses
               )  = End Group
               *\) = Match anything and then a close parens
           )  = Close non-capturing group
           *  = Match anything
        )  = Close capturing group
   \)  = Match a close parens
  	 /gi  = Get all matches, not the first.  Be case insensitive.
   */

  var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function (fullMatch, origUrl) {
    // strip quotes (if they exist)
    var unquotedOrigUrl = origUrl.trim().replace(/^"(.*)"$/, function (o, $1) {
      return $1;
    }).replace(/^'(.*)'$/, function (o, $1) {
      return $1;
    }); // already a full url? no change

    if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(unquotedOrigUrl)) {
      return fullMatch;
    } // convert the url to a full url


    var newUrl;

    if (unquotedOrigUrl.indexOf("//") === 0) {
      //TODO: should we add protocol?
      newUrl = unquotedOrigUrl;
    } else if (unquotedOrigUrl.indexOf("/") === 0) {
      // path should be relative to the base url
      newUrl = baseUrl + unquotedOrigUrl; // already starts with '/'
    } else {
      // path should be relative to current directory
      newUrl = currentDir + unquotedOrigUrl.replace(/^\.\//, ""); // Strip leading './'
    } // send back the fixed url(...)


    return "url(" + JSON.stringify(newUrl) + ")";
  }); // send back the fixed css

  return fixedCss;
};

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue?vue&type=template&id=547cd8e4&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.vue?vue&type=template&id=547cd8e4&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
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



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue?vue&type=template&id=838c3fb0&scoped=true&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.vue?vue&type=template&id=838c3fb0&scoped=true& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "ccm-avatar-creator-container" },
    [
      _vm.img !== null
        ? _c("avatar", {
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
            ? _c("avatar", {
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
      _c("canvas", { ref: "canvas", staticStyle: { height: "0" } }),
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



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return normalizeComponent; });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

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
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


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

/***/ 3:
/*!*************************************************!*\
  !*** multi ./assets/themes/concrete/js/main.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/assets/themes/concrete/js/main.js */"./assets/themes/concrete/js/main.js");


/***/ }),

/***/ "bootstrap":
/*!****************************!*\
  !*** external "bootstrap" ***!
  \****************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = bootstrap;

/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = Vue;

/***/ })

/******/ });