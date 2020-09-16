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
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! nprogress */ "./node_modules/nprogress/nprogress.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(nprogress__WEBPACK_IMPORTED_MODULE_3__);

 //import BackgroundImage from './background-image';
// Handle profile picture



window.NProgress = nprogress__WEBPACK_IMPORTED_MODULE_3___default.a;
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
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.js");
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var interactjs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! interactjs */ "./node_modules/interactjs/dist/interact.min.js");
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
/* harmony import */ var dropzone__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! dropzone */ "./node_modules/dropzone/dist/dropzone.js");
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

/***/ "./node_modules/bootstrap/dist/js/bootstrap.js":
/*!*****************************************************!*\
  !*** ./node_modules/bootstrap/dist/js/bootstrap.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*!
  * Bootstrap v4.5.2 (https://getbootstrap.com/)
  * Copyright 2011-2020 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
  */
(function (global, factory) {
  ( false ? undefined : _typeof(exports)) === 'object' && typeof module !== 'undefined' ? factory(exports, __webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js")) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, __webpack_require__(/*! jquery */ "jquery"), __webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : (undefined);
})(this, function (exports, $, Popper) {
  'use strict';

  $ = $ && Object.prototype.hasOwnProperty.call($, 'default') ? $['default'] : $;
  Popper = Popper && Object.prototype.hasOwnProperty.call(Popper, 'default') ? Popper['default'] : Popper;

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

  function _extends() {
    _extends = Object.assign || function (target) {
      for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];

        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }

      return target;
    };

    return _extends.apply(this, arguments);
  }

  function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    subClass.__proto__ = superClass;
  }
  /**
   * --------------------------------------------------------------------------
   * Bootstrap (v4.5.2): util.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
   * --------------------------------------------------------------------------
   */

  /**
   * ------------------------------------------------------------------------
   * Private TransitionEnd Helpers
   * ------------------------------------------------------------------------
   */


  var TRANSITION_END = 'transitionend';
  var MAX_UID = 1000000;
  var MILLISECONDS_MULTIPLIER = 1000; // Shoutout AngusCroll (https://goo.gl/pxwQGp)

  function toType(obj) {
    if (obj === null || typeof obj === 'undefined') {
      return "" + obj;
    }

    return {}.toString.call(obj).match(/\s([a-z]+)/i)[1].toLowerCase();
  }

  function getSpecialTransitionEndEvent() {
    return {
      bindType: TRANSITION_END,
      delegateType: TRANSITION_END,
      handle: function handle(event) {
        if ($(event.target).is(this)) {
          return event.handleObj.handler.apply(this, arguments); // eslint-disable-line prefer-rest-params
        }

        return undefined;
      }
    };
  }

  function transitionEndEmulator(duration) {
    var _this = this;

    var called = false;
    $(this).one(Util.TRANSITION_END, function () {
      called = true;
    });
    setTimeout(function () {
      if (!called) {
        Util.triggerTransitionEnd(_this);
      }
    }, duration);
    return this;
  }

  function setTransitionEndSupport() {
    $.fn.emulateTransitionEnd = transitionEndEmulator;
    $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
  }
  /**
   * --------------------------------------------------------------------------
   * Public Util Api
   * --------------------------------------------------------------------------
   */


  var Util = {
    TRANSITION_END: 'bsTransitionEnd',
    getUID: function getUID(prefix) {
      do {
        // eslint-disable-next-line no-bitwise
        prefix += ~~(Math.random() * MAX_UID); // "~~" acts like a faster Math.floor() here
      } while (document.getElementById(prefix));

      return prefix;
    },
    getSelectorFromElement: function getSelectorFromElement(element) {
      var selector = element.getAttribute('data-target');

      if (!selector || selector === '#') {
        var hrefAttr = element.getAttribute('href');
        selector = hrefAttr && hrefAttr !== '#' ? hrefAttr.trim() : '';
      }

      try {
        return document.querySelector(selector) ? selector : null;
      } catch (err) {
        return null;
      }
    },
    getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
      if (!element) {
        return 0;
      } // Get transition-duration of the element


      var transitionDuration = $(element).css('transition-duration');
      var transitionDelay = $(element).css('transition-delay');
      var floatTransitionDuration = parseFloat(transitionDuration);
      var floatTransitionDelay = parseFloat(transitionDelay); // Return 0 if element or transition duration is not found

      if (!floatTransitionDuration && !floatTransitionDelay) {
        return 0;
      } // If multiple durations are defined, take the first


      transitionDuration = transitionDuration.split(',')[0];
      transitionDelay = transitionDelay.split(',')[0];
      return (parseFloat(transitionDuration) + parseFloat(transitionDelay)) * MILLISECONDS_MULTIPLIER;
    },
    reflow: function reflow(element) {
      return element.offsetHeight;
    },
    triggerTransitionEnd: function triggerTransitionEnd(element) {
      $(element).trigger(TRANSITION_END);
    },
    // TODO: Remove in v5
    supportsTransitionEnd: function supportsTransitionEnd() {
      return Boolean(TRANSITION_END);
    },
    isElement: function isElement(obj) {
      return (obj[0] || obj).nodeType;
    },
    typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
      for (var property in configTypes) {
        if (Object.prototype.hasOwnProperty.call(configTypes, property)) {
          var expectedTypes = configTypes[property];
          var value = config[property];
          var valueType = value && Util.isElement(value) ? 'element' : toType(value);

          if (!new RegExp(expectedTypes).test(valueType)) {
            throw new Error(componentName.toUpperCase() + ": " + ("Option \"" + property + "\" provided type \"" + valueType + "\" ") + ("but expected type \"" + expectedTypes + "\"."));
          }
        }
      }
    },
    findShadowRoot: function findShadowRoot(element) {
      if (!document.documentElement.attachShadow) {
        return null;
      } // Can find the shadow root otherwise it'll return the document


      if (typeof element.getRootNode === 'function') {
        var root = element.getRootNode();
        return root instanceof ShadowRoot ? root : null;
      }

      if (element instanceof ShadowRoot) {
        return element;
      } // when we don't find a shadow root


      if (!element.parentNode) {
        return null;
      }

      return Util.findShadowRoot(element.parentNode);
    },
    jQueryDetection: function jQueryDetection() {
      if (typeof $ === 'undefined') {
        throw new TypeError('Bootstrap\'s JavaScript requires jQuery. jQuery must be included before Bootstrap\'s JavaScript.');
      }

      var version = $.fn.jquery.split(' ')[0].split('.');
      var minMajor = 1;
      var ltMajor = 2;
      var minMinor = 9;
      var minPatch = 1;
      var maxMajor = 4;

      if (version[0] < ltMajor && version[1] < minMinor || version[0] === minMajor && version[1] === minMinor && version[2] < minPatch || version[0] >= maxMajor) {
        throw new Error('Bootstrap\'s JavaScript requires at least jQuery v1.9.1 but less than v4.0.0');
      }
    }
  };
  Util.jQueryDetection();
  setTransitionEndSupport();
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME = 'alert';
  var VERSION = '4.5.2';
  var DATA_KEY = 'bs.alert';
  var EVENT_KEY = "." + DATA_KEY;
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = $.fn[NAME];
  var SELECTOR_DISMISS = '[data-dismiss="alert"]';
  var EVENT_CLOSE = "close" + EVENT_KEY;
  var EVENT_CLOSED = "closed" + EVENT_KEY;
  var EVENT_CLICK_DATA_API = "click" + EVENT_KEY + DATA_API_KEY;
  var CLASS_NAME_ALERT = 'alert';
  var CLASS_NAME_FADE = 'fade';
  var CLASS_NAME_SHOW = 'show';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Alert = /*#__PURE__*/function () {
    function Alert(element) {
      this._element = element;
    } // Getters


    var _proto = Alert.prototype; // Public

    _proto.close = function close(element) {
      var rootElement = this._element;

      if (element) {
        rootElement = this._getRootElement(element);
      }

      var customEvent = this._triggerCloseEvent(rootElement);

      if (customEvent.isDefaultPrevented()) {
        return;
      }

      this._removeElement(rootElement);
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY);
      this._element = null;
    } // Private
    ;

    _proto._getRootElement = function _getRootElement(element) {
      var selector = Util.getSelectorFromElement(element);
      var parent = false;

      if (selector) {
        parent = document.querySelector(selector);
      }

      if (!parent) {
        parent = $(element).closest("." + CLASS_NAME_ALERT)[0];
      }

      return parent;
    };

    _proto._triggerCloseEvent = function _triggerCloseEvent(element) {
      var closeEvent = $.Event(EVENT_CLOSE);
      $(element).trigger(closeEvent);
      return closeEvent;
    };

    _proto._removeElement = function _removeElement(element) {
      var _this = this;

      $(element).removeClass(CLASS_NAME_SHOW);

      if (!$(element).hasClass(CLASS_NAME_FADE)) {
        this._destroyElement(element);

        return;
      }

      var transitionDuration = Util.getTransitionDurationFromElement(element);
      $(element).one(Util.TRANSITION_END, function (event) {
        return _this._destroyElement(element, event);
      }).emulateTransitionEnd(transitionDuration);
    };

    _proto._destroyElement = function _destroyElement(element) {
      $(element).detach().trigger(EVENT_CLOSED).remove();
    } // Static
    ;

    Alert._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $element = $(this);
        var data = $element.data(DATA_KEY);

        if (!data) {
          data = new Alert(this);
          $element.data(DATA_KEY, data);
        }

        if (config === 'close') {
          data[config](this);
        }
      });
    };

    Alert._handleDismiss = function _handleDismiss(alertInstance) {
      return function (event) {
        if (event) {
          event.preventDefault();
        }

        alertInstance.close(this);
      };
    };

    _createClass(Alert, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION;
      }
    }]);

    return Alert;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API, SELECTOR_DISMISS, Alert._handleDismiss(new Alert()));
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME] = Alert._jQueryInterface;
  $.fn[NAME].Constructor = Alert;

  $.fn[NAME].noConflict = function () {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Alert._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$1 = 'button';
  var VERSION$1 = '4.5.2';
  var DATA_KEY$1 = 'bs.button';
  var EVENT_KEY$1 = "." + DATA_KEY$1;
  var DATA_API_KEY$1 = '.data-api';
  var JQUERY_NO_CONFLICT$1 = $.fn[NAME$1];
  var CLASS_NAME_ACTIVE = 'active';
  var CLASS_NAME_BUTTON = 'btn';
  var CLASS_NAME_FOCUS = 'focus';
  var SELECTOR_DATA_TOGGLE_CARROT = '[data-toggle^="button"]';
  var SELECTOR_DATA_TOGGLES = '[data-toggle="buttons"]';
  var SELECTOR_DATA_TOGGLE = '[data-toggle="button"]';
  var SELECTOR_DATA_TOGGLES_BUTTONS = '[data-toggle="buttons"] .btn';
  var SELECTOR_INPUT = 'input:not([type="hidden"])';
  var SELECTOR_ACTIVE = '.active';
  var SELECTOR_BUTTON = '.btn';
  var EVENT_CLICK_DATA_API$1 = "click" + EVENT_KEY$1 + DATA_API_KEY$1;
  var EVENT_FOCUS_BLUR_DATA_API = "focus" + EVENT_KEY$1 + DATA_API_KEY$1 + " " + ("blur" + EVENT_KEY$1 + DATA_API_KEY$1);
  var EVENT_LOAD_DATA_API = "load" + EVENT_KEY$1 + DATA_API_KEY$1;
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Button = /*#__PURE__*/function () {
    function Button(element) {
      this._element = element;
    } // Getters


    var _proto = Button.prototype; // Public

    _proto.toggle = function toggle() {
      var triggerChangeEvent = true;
      var addAriaPressed = true;
      var rootElement = $(this._element).closest(SELECTOR_DATA_TOGGLES)[0];

      if (rootElement) {
        var input = this._element.querySelector(SELECTOR_INPUT);

        if (input) {
          if (input.type === 'radio') {
            if (input.checked && this._element.classList.contains(CLASS_NAME_ACTIVE)) {
              triggerChangeEvent = false;
            } else {
              var activeElement = rootElement.querySelector(SELECTOR_ACTIVE);

              if (activeElement) {
                $(activeElement).removeClass(CLASS_NAME_ACTIVE);
              }
            }
          }

          if (triggerChangeEvent) {
            // if it's not a radio button or checkbox don't add a pointless/invalid checked property to the input
            if (input.type === 'checkbox' || input.type === 'radio') {
              input.checked = !this._element.classList.contains(CLASS_NAME_ACTIVE);
            }

            $(input).trigger('change');
          }

          input.focus();
          addAriaPressed = false;
        }
      }

      if (!(this._element.hasAttribute('disabled') || this._element.classList.contains('disabled'))) {
        if (addAriaPressed) {
          this._element.setAttribute('aria-pressed', !this._element.classList.contains(CLASS_NAME_ACTIVE));
        }

        if (triggerChangeEvent) {
          $(this._element).toggleClass(CLASS_NAME_ACTIVE);
        }
      }
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$1);
      this._element = null;
    } // Static
    ;

    Button._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$1);

        if (!data) {
          data = new Button(this);
          $(this).data(DATA_KEY$1, data);
        }

        if (config === 'toggle') {
          data[config]();
        }
      });
    };

    _createClass(Button, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$1;
      }
    }]);

    return Button;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$1, SELECTOR_DATA_TOGGLE_CARROT, function (event) {
    var button = event.target;
    var initialButton = button;

    if (!$(button).hasClass(CLASS_NAME_BUTTON)) {
      button = $(button).closest(SELECTOR_BUTTON)[0];
    }

    if (!button || button.hasAttribute('disabled') || button.classList.contains('disabled')) {
      event.preventDefault(); // work around Firefox bug #1540995
    } else {
      var inputBtn = button.querySelector(SELECTOR_INPUT);

      if (inputBtn && (inputBtn.hasAttribute('disabled') || inputBtn.classList.contains('disabled'))) {
        event.preventDefault(); // work around Firefox bug #1540995

        return;
      }

      if (initialButton.tagName !== 'LABEL' || inputBtn && inputBtn.type !== 'checkbox') {
        Button._jQueryInterface.call($(button), 'toggle');
      }
    }
  }).on(EVENT_FOCUS_BLUR_DATA_API, SELECTOR_DATA_TOGGLE_CARROT, function (event) {
    var button = $(event.target).closest(SELECTOR_BUTTON)[0];
    $(button).toggleClass(CLASS_NAME_FOCUS, /^focus(in)?$/.test(event.type));
  });
  $(window).on(EVENT_LOAD_DATA_API, function () {
    // ensure correct active class is set to match the controls' actual values/states
    // find all checkboxes/readio buttons inside data-toggle groups
    var buttons = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLES_BUTTONS));

    for (var i = 0, len = buttons.length; i < len; i++) {
      var button = buttons[i];
      var input = button.querySelector(SELECTOR_INPUT);

      if (input.checked || input.hasAttribute('checked')) {
        button.classList.add(CLASS_NAME_ACTIVE);
      } else {
        button.classList.remove(CLASS_NAME_ACTIVE);
      }
    } // find all button toggles


    buttons = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLE));

    for (var _i = 0, _len = buttons.length; _i < _len; _i++) {
      var _button = buttons[_i];

      if (_button.getAttribute('aria-pressed') === 'true') {
        _button.classList.add(CLASS_NAME_ACTIVE);
      } else {
        _button.classList.remove(CLASS_NAME_ACTIVE);
      }
    }
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$1] = Button._jQueryInterface;
  $.fn[NAME$1].Constructor = Button;

  $.fn[NAME$1].noConflict = function () {
    $.fn[NAME$1] = JQUERY_NO_CONFLICT$1;
    return Button._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$2 = 'carousel';
  var VERSION$2 = '4.5.2';
  var DATA_KEY$2 = 'bs.carousel';
  var EVENT_KEY$2 = "." + DATA_KEY$2;
  var DATA_API_KEY$2 = '.data-api';
  var JQUERY_NO_CONFLICT$2 = $.fn[NAME$2];
  var ARROW_LEFT_KEYCODE = 37; // KeyboardEvent.which value for left arrow key

  var ARROW_RIGHT_KEYCODE = 39; // KeyboardEvent.which value for right arrow key

  var TOUCHEVENT_COMPAT_WAIT = 500; // Time for mouse compat events to fire after touch

  var SWIPE_THRESHOLD = 40;
  var Default = {
    interval: 5000,
    keyboard: true,
    slide: false,
    pause: 'hover',
    wrap: true,
    touch: true
  };
  var DefaultType = {
    interval: '(number|boolean)',
    keyboard: 'boolean',
    slide: '(boolean|string)',
    pause: '(string|boolean)',
    wrap: 'boolean',
    touch: 'boolean'
  };
  var DIRECTION_NEXT = 'next';
  var DIRECTION_PREV = 'prev';
  var DIRECTION_LEFT = 'left';
  var DIRECTION_RIGHT = 'right';
  var EVENT_SLIDE = "slide" + EVENT_KEY$2;
  var EVENT_SLID = "slid" + EVENT_KEY$2;
  var EVENT_KEYDOWN = "keydown" + EVENT_KEY$2;
  var EVENT_MOUSEENTER = "mouseenter" + EVENT_KEY$2;
  var EVENT_MOUSELEAVE = "mouseleave" + EVENT_KEY$2;
  var EVENT_TOUCHSTART = "touchstart" + EVENT_KEY$2;
  var EVENT_TOUCHMOVE = "touchmove" + EVENT_KEY$2;
  var EVENT_TOUCHEND = "touchend" + EVENT_KEY$2;
  var EVENT_POINTERDOWN = "pointerdown" + EVENT_KEY$2;
  var EVENT_POINTERUP = "pointerup" + EVENT_KEY$2;
  var EVENT_DRAG_START = "dragstart" + EVENT_KEY$2;
  var EVENT_LOAD_DATA_API$1 = "load" + EVENT_KEY$2 + DATA_API_KEY$2;
  var EVENT_CLICK_DATA_API$2 = "click" + EVENT_KEY$2 + DATA_API_KEY$2;
  var CLASS_NAME_CAROUSEL = 'carousel';
  var CLASS_NAME_ACTIVE$1 = 'active';
  var CLASS_NAME_SLIDE = 'slide';
  var CLASS_NAME_RIGHT = 'carousel-item-right';
  var CLASS_NAME_LEFT = 'carousel-item-left';
  var CLASS_NAME_NEXT = 'carousel-item-next';
  var CLASS_NAME_PREV = 'carousel-item-prev';
  var CLASS_NAME_POINTER_EVENT = 'pointer-event';
  var SELECTOR_ACTIVE$1 = '.active';
  var SELECTOR_ACTIVE_ITEM = '.active.carousel-item';
  var SELECTOR_ITEM = '.carousel-item';
  var SELECTOR_ITEM_IMG = '.carousel-item img';
  var SELECTOR_NEXT_PREV = '.carousel-item-next, .carousel-item-prev';
  var SELECTOR_INDICATORS = '.carousel-indicators';
  var SELECTOR_DATA_SLIDE = '[data-slide], [data-slide-to]';
  var SELECTOR_DATA_RIDE = '[data-ride="carousel"]';
  var PointerType = {
    TOUCH: 'touch',
    PEN: 'pen'
  };
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Carousel = /*#__PURE__*/function () {
    function Carousel(element, config) {
      this._items = null;
      this._interval = null;
      this._activeElement = null;
      this._isPaused = false;
      this._isSliding = false;
      this.touchTimeout = null;
      this.touchStartX = 0;
      this.touchDeltaX = 0;
      this._config = this._getConfig(config);
      this._element = element;
      this._indicatorsElement = this._element.querySelector(SELECTOR_INDICATORS);
      this._touchSupported = 'ontouchstart' in document.documentElement || navigator.maxTouchPoints > 0;
      this._pointerEvent = Boolean(window.PointerEvent || window.MSPointerEvent);

      this._addEventListeners();
    } // Getters


    var _proto = Carousel.prototype; // Public

    _proto.next = function next() {
      if (!this._isSliding) {
        this._slide(DIRECTION_NEXT);
      }
    };

    _proto.nextWhenVisible = function nextWhenVisible() {
      // Don't call next when the page isn't visible
      // or the carousel or its parent isn't visible
      if (!document.hidden && $(this._element).is(':visible') && $(this._element).css('visibility') !== 'hidden') {
        this.next();
      }
    };

    _proto.prev = function prev() {
      if (!this._isSliding) {
        this._slide(DIRECTION_PREV);
      }
    };

    _proto.pause = function pause(event) {
      if (!event) {
        this._isPaused = true;
      }

      if (this._element.querySelector(SELECTOR_NEXT_PREV)) {
        Util.triggerTransitionEnd(this._element);
        this.cycle(true);
      }

      clearInterval(this._interval);
      this._interval = null;
    };

    _proto.cycle = function cycle(event) {
      if (!event) {
        this._isPaused = false;
      }

      if (this._interval) {
        clearInterval(this._interval);
        this._interval = null;
      }

      if (this._config.interval && !this._isPaused) {
        this._interval = setInterval((document.visibilityState ? this.nextWhenVisible : this.next).bind(this), this._config.interval);
      }
    };

    _proto.to = function to(index) {
      var _this = this;

      this._activeElement = this._element.querySelector(SELECTOR_ACTIVE_ITEM);

      var activeIndex = this._getItemIndex(this._activeElement);

      if (index > this._items.length - 1 || index < 0) {
        return;
      }

      if (this._isSliding) {
        $(this._element).one(EVENT_SLID, function () {
          return _this.to(index);
        });
        return;
      }

      if (activeIndex === index) {
        this.pause();
        this.cycle();
        return;
      }

      var direction = index > activeIndex ? DIRECTION_NEXT : DIRECTION_PREV;

      this._slide(direction, this._items[index]);
    };

    _proto.dispose = function dispose() {
      $(this._element).off(EVENT_KEY$2);
      $.removeData(this._element, DATA_KEY$2);
      this._items = null;
      this._config = null;
      this._element = null;
      this._interval = null;
      this._isPaused = null;
      this._isSliding = null;
      this._activeElement = null;
      this._indicatorsElement = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _extends({}, Default, config);
      Util.typeCheckConfig(NAME$2, config, DefaultType);
      return config;
    };

    _proto._handleSwipe = function _handleSwipe() {
      var absDeltax = Math.abs(this.touchDeltaX);

      if (absDeltax <= SWIPE_THRESHOLD) {
        return;
      }

      var direction = absDeltax / this.touchDeltaX;
      this.touchDeltaX = 0; // swipe left

      if (direction > 0) {
        this.prev();
      } // swipe right


      if (direction < 0) {
        this.next();
      }
    };

    _proto._addEventListeners = function _addEventListeners() {
      var _this2 = this;

      if (this._config.keyboard) {
        $(this._element).on(EVENT_KEYDOWN, function (event) {
          return _this2._keydown(event);
        });
      }

      if (this._config.pause === 'hover') {
        $(this._element).on(EVENT_MOUSEENTER, function (event) {
          return _this2.pause(event);
        }).on(EVENT_MOUSELEAVE, function (event) {
          return _this2.cycle(event);
        });
      }

      if (this._config.touch) {
        this._addTouchEventListeners();
      }
    };

    _proto._addTouchEventListeners = function _addTouchEventListeners() {
      var _this3 = this;

      if (!this._touchSupported) {
        return;
      }

      var start = function start(event) {
        if (_this3._pointerEvent && PointerType[event.originalEvent.pointerType.toUpperCase()]) {
          _this3.touchStartX = event.originalEvent.clientX;
        } else if (!_this3._pointerEvent) {
          _this3.touchStartX = event.originalEvent.touches[0].clientX;
        }
      };

      var move = function move(event) {
        // ensure swiping with one touch and not pinching
        if (event.originalEvent.touches && event.originalEvent.touches.length > 1) {
          _this3.touchDeltaX = 0;
        } else {
          _this3.touchDeltaX = event.originalEvent.touches[0].clientX - _this3.touchStartX;
        }
      };

      var end = function end(event) {
        if (_this3._pointerEvent && PointerType[event.originalEvent.pointerType.toUpperCase()]) {
          _this3.touchDeltaX = event.originalEvent.clientX - _this3.touchStartX;
        }

        _this3._handleSwipe();

        if (_this3._config.pause === 'hover') {
          // If it's a touch-enabled device, mouseenter/leave are fired as
          // part of the mouse compatibility events on first tap - the carousel
          // would stop cycling until user tapped out of it;
          // here, we listen for touchend, explicitly pause the carousel
          // (as if it's the second time we tap on it, mouseenter compat event
          // is NOT fired) and after a timeout (to allow for mouse compatibility
          // events to fire) we explicitly restart cycling
          _this3.pause();

          if (_this3.touchTimeout) {
            clearTimeout(_this3.touchTimeout);
          }

          _this3.touchTimeout = setTimeout(function (event) {
            return _this3.cycle(event);
          }, TOUCHEVENT_COMPAT_WAIT + _this3._config.interval);
        }
      };

      $(this._element.querySelectorAll(SELECTOR_ITEM_IMG)).on(EVENT_DRAG_START, function (e) {
        return e.preventDefault();
      });

      if (this._pointerEvent) {
        $(this._element).on(EVENT_POINTERDOWN, function (event) {
          return start(event);
        });
        $(this._element).on(EVENT_POINTERUP, function (event) {
          return end(event);
        });

        this._element.classList.add(CLASS_NAME_POINTER_EVENT);
      } else {
        $(this._element).on(EVENT_TOUCHSTART, function (event) {
          return start(event);
        });
        $(this._element).on(EVENT_TOUCHMOVE, function (event) {
          return move(event);
        });
        $(this._element).on(EVENT_TOUCHEND, function (event) {
          return end(event);
        });
      }
    };

    _proto._keydown = function _keydown(event) {
      if (/input|textarea/i.test(event.target.tagName)) {
        return;
      }

      switch (event.which) {
        case ARROW_LEFT_KEYCODE:
          event.preventDefault();
          this.prev();
          break;

        case ARROW_RIGHT_KEYCODE:
          event.preventDefault();
          this.next();
          break;
      }
    };

    _proto._getItemIndex = function _getItemIndex(element) {
      this._items = element && element.parentNode ? [].slice.call(element.parentNode.querySelectorAll(SELECTOR_ITEM)) : [];
      return this._items.indexOf(element);
    };

    _proto._getItemByDirection = function _getItemByDirection(direction, activeElement) {
      var isNextDirection = direction === DIRECTION_NEXT;
      var isPrevDirection = direction === DIRECTION_PREV;

      var activeIndex = this._getItemIndex(activeElement);

      var lastItemIndex = this._items.length - 1;
      var isGoingToWrap = isPrevDirection && activeIndex === 0 || isNextDirection && activeIndex === lastItemIndex;

      if (isGoingToWrap && !this._config.wrap) {
        return activeElement;
      }

      var delta = direction === DIRECTION_PREV ? -1 : 1;
      var itemIndex = (activeIndex + delta) % this._items.length;
      return itemIndex === -1 ? this._items[this._items.length - 1] : this._items[itemIndex];
    };

    _proto._triggerSlideEvent = function _triggerSlideEvent(relatedTarget, eventDirectionName) {
      var targetIndex = this._getItemIndex(relatedTarget);

      var fromIndex = this._getItemIndex(this._element.querySelector(SELECTOR_ACTIVE_ITEM));

      var slideEvent = $.Event(EVENT_SLIDE, {
        relatedTarget: relatedTarget,
        direction: eventDirectionName,
        from: fromIndex,
        to: targetIndex
      });
      $(this._element).trigger(slideEvent);
      return slideEvent;
    };

    _proto._setActiveIndicatorElement = function _setActiveIndicatorElement(element) {
      if (this._indicatorsElement) {
        var indicators = [].slice.call(this._indicatorsElement.querySelectorAll(SELECTOR_ACTIVE$1));
        $(indicators).removeClass(CLASS_NAME_ACTIVE$1);

        var nextIndicator = this._indicatorsElement.children[this._getItemIndex(element)];

        if (nextIndicator) {
          $(nextIndicator).addClass(CLASS_NAME_ACTIVE$1);
        }
      }
    };

    _proto._slide = function _slide(direction, element) {
      var _this4 = this;

      var activeElement = this._element.querySelector(SELECTOR_ACTIVE_ITEM);

      var activeElementIndex = this._getItemIndex(activeElement);

      var nextElement = element || activeElement && this._getItemByDirection(direction, activeElement);

      var nextElementIndex = this._getItemIndex(nextElement);

      var isCycling = Boolean(this._interval);
      var directionalClassName;
      var orderClassName;
      var eventDirectionName;

      if (direction === DIRECTION_NEXT) {
        directionalClassName = CLASS_NAME_LEFT;
        orderClassName = CLASS_NAME_NEXT;
        eventDirectionName = DIRECTION_LEFT;
      } else {
        directionalClassName = CLASS_NAME_RIGHT;
        orderClassName = CLASS_NAME_PREV;
        eventDirectionName = DIRECTION_RIGHT;
      }

      if (nextElement && $(nextElement).hasClass(CLASS_NAME_ACTIVE$1)) {
        this._isSliding = false;
        return;
      }

      var slideEvent = this._triggerSlideEvent(nextElement, eventDirectionName);

      if (slideEvent.isDefaultPrevented()) {
        return;
      }

      if (!activeElement || !nextElement) {
        // Some weirdness is happening, so we bail
        return;
      }

      this._isSliding = true;

      if (isCycling) {
        this.pause();
      }

      this._setActiveIndicatorElement(nextElement);

      var slidEvent = $.Event(EVENT_SLID, {
        relatedTarget: nextElement,
        direction: eventDirectionName,
        from: activeElementIndex,
        to: nextElementIndex
      });

      if ($(this._element).hasClass(CLASS_NAME_SLIDE)) {
        $(nextElement).addClass(orderClassName);
        Util.reflow(nextElement);
        $(activeElement).addClass(directionalClassName);
        $(nextElement).addClass(directionalClassName);
        var nextElementInterval = parseInt(nextElement.getAttribute('data-interval'), 10);

        if (nextElementInterval) {
          this._config.defaultInterval = this._config.defaultInterval || this._config.interval;
          this._config.interval = nextElementInterval;
        } else {
          this._config.interval = this._config.defaultInterval || this._config.interval;
        }

        var transitionDuration = Util.getTransitionDurationFromElement(activeElement);
        $(activeElement).one(Util.TRANSITION_END, function () {
          $(nextElement).removeClass(directionalClassName + " " + orderClassName).addClass(CLASS_NAME_ACTIVE$1);
          $(activeElement).removeClass(CLASS_NAME_ACTIVE$1 + " " + orderClassName + " " + directionalClassName);
          _this4._isSliding = false;
          setTimeout(function () {
            return $(_this4._element).trigger(slidEvent);
          }, 0);
        }).emulateTransitionEnd(transitionDuration);
      } else {
        $(activeElement).removeClass(CLASS_NAME_ACTIVE$1);
        $(nextElement).addClass(CLASS_NAME_ACTIVE$1);
        this._isSliding = false;
        $(this._element).trigger(slidEvent);
      }

      if (isCycling) {
        this.cycle();
      }
    } // Static
    ;

    Carousel._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$2);

        var _config = _extends({}, Default, $(this).data());

        if (_typeof(config) === 'object') {
          _config = _extends({}, _config, config);
        }

        var action = typeof config === 'string' ? config : _config.slide;

        if (!data) {
          data = new Carousel(this, _config);
          $(this).data(DATA_KEY$2, data);
        }

        if (typeof config === 'number') {
          data.to(config);
        } else if (typeof action === 'string') {
          if (typeof data[action] === 'undefined') {
            throw new TypeError("No method named \"" + action + "\"");
          }

          data[action]();
        } else if (_config.interval && _config.ride) {
          data.pause();
          data.cycle();
        }
      });
    };

    Carousel._dataApiClickHandler = function _dataApiClickHandler(event) {
      var selector = Util.getSelectorFromElement(this);

      if (!selector) {
        return;
      }

      var target = $(selector)[0];

      if (!target || !$(target).hasClass(CLASS_NAME_CAROUSEL)) {
        return;
      }

      var config = _extends({}, $(target).data(), $(this).data());

      var slideIndex = this.getAttribute('data-slide-to');

      if (slideIndex) {
        config.interval = false;
      }

      Carousel._jQueryInterface.call($(target), config);

      if (slideIndex) {
        $(target).data(DATA_KEY$2).to(slideIndex);
      }

      event.preventDefault();
    };

    _createClass(Carousel, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$2;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default;
      }
    }]);

    return Carousel;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$2, SELECTOR_DATA_SLIDE, Carousel._dataApiClickHandler);
  $(window).on(EVENT_LOAD_DATA_API$1, function () {
    var carousels = [].slice.call(document.querySelectorAll(SELECTOR_DATA_RIDE));

    for (var i = 0, len = carousels.length; i < len; i++) {
      var $carousel = $(carousels[i]);

      Carousel._jQueryInterface.call($carousel, $carousel.data());
    }
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$2] = Carousel._jQueryInterface;
  $.fn[NAME$2].Constructor = Carousel;

  $.fn[NAME$2].noConflict = function () {
    $.fn[NAME$2] = JQUERY_NO_CONFLICT$2;
    return Carousel._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$3 = 'collapse';
  var VERSION$3 = '4.5.2';
  var DATA_KEY$3 = 'bs.collapse';
  var EVENT_KEY$3 = "." + DATA_KEY$3;
  var DATA_API_KEY$3 = '.data-api';
  var JQUERY_NO_CONFLICT$3 = $.fn[NAME$3];
  var Default$1 = {
    toggle: true,
    parent: ''
  };
  var DefaultType$1 = {
    toggle: 'boolean',
    parent: '(string|element)'
  };
  var EVENT_SHOW = "show" + EVENT_KEY$3;
  var EVENT_SHOWN = "shown" + EVENT_KEY$3;
  var EVENT_HIDE = "hide" + EVENT_KEY$3;
  var EVENT_HIDDEN = "hidden" + EVENT_KEY$3;
  var EVENT_CLICK_DATA_API$3 = "click" + EVENT_KEY$3 + DATA_API_KEY$3;
  var CLASS_NAME_SHOW$1 = 'show';
  var CLASS_NAME_COLLAPSE = 'collapse';
  var CLASS_NAME_COLLAPSING = 'collapsing';
  var CLASS_NAME_COLLAPSED = 'collapsed';
  var DIMENSION_WIDTH = 'width';
  var DIMENSION_HEIGHT = 'height';
  var SELECTOR_ACTIVES = '.show, .collapsing';
  var SELECTOR_DATA_TOGGLE$1 = '[data-toggle="collapse"]';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Collapse = /*#__PURE__*/function () {
    function Collapse(element, config) {
      this._isTransitioning = false;
      this._element = element;
      this._config = this._getConfig(config);
      this._triggerArray = [].slice.call(document.querySelectorAll("[data-toggle=\"collapse\"][href=\"#" + element.id + "\"]," + ("[data-toggle=\"collapse\"][data-target=\"#" + element.id + "\"]")));
      var toggleList = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLE$1));

      for (var i = 0, len = toggleList.length; i < len; i++) {
        var elem = toggleList[i];
        var selector = Util.getSelectorFromElement(elem);
        var filterElement = [].slice.call(document.querySelectorAll(selector)).filter(function (foundElem) {
          return foundElem === element;
        });

        if (selector !== null && filterElement.length > 0) {
          this._selector = selector;

          this._triggerArray.push(elem);
        }
      }

      this._parent = this._config.parent ? this._getParent() : null;

      if (!this._config.parent) {
        this._addAriaAndCollapsedClass(this._element, this._triggerArray);
      }

      if (this._config.toggle) {
        this.toggle();
      }
    } // Getters


    var _proto = Collapse.prototype; // Public

    _proto.toggle = function toggle() {
      if ($(this._element).hasClass(CLASS_NAME_SHOW$1)) {
        this.hide();
      } else {
        this.show();
      }
    };

    _proto.show = function show() {
      var _this = this;

      if (this._isTransitioning || $(this._element).hasClass(CLASS_NAME_SHOW$1)) {
        return;
      }

      var actives;
      var activesData;

      if (this._parent) {
        actives = [].slice.call(this._parent.querySelectorAll(SELECTOR_ACTIVES)).filter(function (elem) {
          if (typeof _this._config.parent === 'string') {
            return elem.getAttribute('data-parent') === _this._config.parent;
          }

          return elem.classList.contains(CLASS_NAME_COLLAPSE);
        });

        if (actives.length === 0) {
          actives = null;
        }
      }

      if (actives) {
        activesData = $(actives).not(this._selector).data(DATA_KEY$3);

        if (activesData && activesData._isTransitioning) {
          return;
        }
      }

      var startEvent = $.Event(EVENT_SHOW);
      $(this._element).trigger(startEvent);

      if (startEvent.isDefaultPrevented()) {
        return;
      }

      if (actives) {
        Collapse._jQueryInterface.call($(actives).not(this._selector), 'hide');

        if (!activesData) {
          $(actives).data(DATA_KEY$3, null);
        }
      }

      var dimension = this._getDimension();

      $(this._element).removeClass(CLASS_NAME_COLLAPSE).addClass(CLASS_NAME_COLLAPSING);
      this._element.style[dimension] = 0;

      if (this._triggerArray.length) {
        $(this._triggerArray).removeClass(CLASS_NAME_COLLAPSED).attr('aria-expanded', true);
      }

      this.setTransitioning(true);

      var complete = function complete() {
        $(_this._element).removeClass(CLASS_NAME_COLLAPSING).addClass(CLASS_NAME_COLLAPSE + " " + CLASS_NAME_SHOW$1);
        _this._element.style[dimension] = '';

        _this.setTransitioning(false);

        $(_this._element).trigger(EVENT_SHOWN);
      };

      var capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
      var scrollSize = "scroll" + capitalizedDimension;
      var transitionDuration = Util.getTransitionDurationFromElement(this._element);
      $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      this._element.style[dimension] = this._element[scrollSize] + "px";
    };

    _proto.hide = function hide() {
      var _this2 = this;

      if (this._isTransitioning || !$(this._element).hasClass(CLASS_NAME_SHOW$1)) {
        return;
      }

      var startEvent = $.Event(EVENT_HIDE);
      $(this._element).trigger(startEvent);

      if (startEvent.isDefaultPrevented()) {
        return;
      }

      var dimension = this._getDimension();

      this._element.style[dimension] = this._element.getBoundingClientRect()[dimension] + "px";
      Util.reflow(this._element);
      $(this._element).addClass(CLASS_NAME_COLLAPSING).removeClass(CLASS_NAME_COLLAPSE + " " + CLASS_NAME_SHOW$1);
      var triggerArrayLength = this._triggerArray.length;

      if (triggerArrayLength > 0) {
        for (var i = 0; i < triggerArrayLength; i++) {
          var trigger = this._triggerArray[i];
          var selector = Util.getSelectorFromElement(trigger);

          if (selector !== null) {
            var $elem = $([].slice.call(document.querySelectorAll(selector)));

            if (!$elem.hasClass(CLASS_NAME_SHOW$1)) {
              $(trigger).addClass(CLASS_NAME_COLLAPSED).attr('aria-expanded', false);
            }
          }
        }
      }

      this.setTransitioning(true);

      var complete = function complete() {
        _this2.setTransitioning(false);

        $(_this2._element).removeClass(CLASS_NAME_COLLAPSING).addClass(CLASS_NAME_COLLAPSE).trigger(EVENT_HIDDEN);
      };

      this._element.style[dimension] = '';
      var transitionDuration = Util.getTransitionDurationFromElement(this._element);
      $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
    };

    _proto.setTransitioning = function setTransitioning(isTransitioning) {
      this._isTransitioning = isTransitioning;
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$3);
      this._config = null;
      this._parent = null;
      this._element = null;
      this._triggerArray = null;
      this._isTransitioning = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _extends({}, Default$1, config);
      config.toggle = Boolean(config.toggle); // Coerce string values

      Util.typeCheckConfig(NAME$3, config, DefaultType$1);
      return config;
    };

    _proto._getDimension = function _getDimension() {
      var hasWidth = $(this._element).hasClass(DIMENSION_WIDTH);
      return hasWidth ? DIMENSION_WIDTH : DIMENSION_HEIGHT;
    };

    _proto._getParent = function _getParent() {
      var _this3 = this;

      var parent;

      if (Util.isElement(this._config.parent)) {
        parent = this._config.parent; // It's a jQuery object

        if (typeof this._config.parent.jquery !== 'undefined') {
          parent = this._config.parent[0];
        }
      } else {
        parent = document.querySelector(this._config.parent);
      }

      var selector = "[data-toggle=\"collapse\"][data-parent=\"" + this._config.parent + "\"]";
      var children = [].slice.call(parent.querySelectorAll(selector));
      $(children).each(function (i, element) {
        _this3._addAriaAndCollapsedClass(Collapse._getTargetFromElement(element), [element]);
      });
      return parent;
    };

    _proto._addAriaAndCollapsedClass = function _addAriaAndCollapsedClass(element, triggerArray) {
      var isOpen = $(element).hasClass(CLASS_NAME_SHOW$1);

      if (triggerArray.length) {
        $(triggerArray).toggleClass(CLASS_NAME_COLLAPSED, !isOpen).attr('aria-expanded', isOpen);
      }
    } // Static
    ;

    Collapse._getTargetFromElement = function _getTargetFromElement(element) {
      var selector = Util.getSelectorFromElement(element);
      return selector ? document.querySelector(selector) : null;
    };

    Collapse._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $this = $(this);
        var data = $this.data(DATA_KEY$3);

        var _config = _extends({}, Default$1, $this.data(), _typeof(config) === 'object' && config ? config : {});

        if (!data && _config.toggle && typeof config === 'string' && /show|hide/.test(config)) {
          _config.toggle = false;
        }

        if (!data) {
          data = new Collapse(this, _config);
          $this.data(DATA_KEY$3, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Collapse, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$3;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$1;
      }
    }]);

    return Collapse;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$3, SELECTOR_DATA_TOGGLE$1, function (event) {
    // preventDefault only for <a> elements (which change the URL) not inside the collapsible element
    if (event.currentTarget.tagName === 'A') {
      event.preventDefault();
    }

    var $trigger = $(this);
    var selector = Util.getSelectorFromElement(this);
    var selectors = [].slice.call(document.querySelectorAll(selector));
    $(selectors).each(function () {
      var $target = $(this);
      var data = $target.data(DATA_KEY$3);
      var config = data ? 'toggle' : $trigger.data();

      Collapse._jQueryInterface.call($target, config);
    });
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$3] = Collapse._jQueryInterface;
  $.fn[NAME$3].Constructor = Collapse;

  $.fn[NAME$3].noConflict = function () {
    $.fn[NAME$3] = JQUERY_NO_CONFLICT$3;
    return Collapse._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$4 = 'dropdown';
  var VERSION$4 = '4.5.2';
  var DATA_KEY$4 = 'bs.dropdown';
  var EVENT_KEY$4 = "." + DATA_KEY$4;
  var DATA_API_KEY$4 = '.data-api';
  var JQUERY_NO_CONFLICT$4 = $.fn[NAME$4];
  var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key

  var SPACE_KEYCODE = 32; // KeyboardEvent.which value for space key

  var TAB_KEYCODE = 9; // KeyboardEvent.which value for tab key

  var ARROW_UP_KEYCODE = 38; // KeyboardEvent.which value for up arrow key

  var ARROW_DOWN_KEYCODE = 40; // KeyboardEvent.which value for down arrow key

  var RIGHT_MOUSE_BUTTON_WHICH = 3; // MouseEvent.which value for the right button (assuming a right-handed mouse)

  var REGEXP_KEYDOWN = new RegExp(ARROW_UP_KEYCODE + "|" + ARROW_DOWN_KEYCODE + "|" + ESCAPE_KEYCODE);
  var EVENT_HIDE$1 = "hide" + EVENT_KEY$4;
  var EVENT_HIDDEN$1 = "hidden" + EVENT_KEY$4;
  var EVENT_SHOW$1 = "show" + EVENT_KEY$4;
  var EVENT_SHOWN$1 = "shown" + EVENT_KEY$4;
  var EVENT_CLICK = "click" + EVENT_KEY$4;
  var EVENT_CLICK_DATA_API$4 = "click" + EVENT_KEY$4 + DATA_API_KEY$4;
  var EVENT_KEYDOWN_DATA_API = "keydown" + EVENT_KEY$4 + DATA_API_KEY$4;
  var EVENT_KEYUP_DATA_API = "keyup" + EVENT_KEY$4 + DATA_API_KEY$4;
  var CLASS_NAME_DISABLED = 'disabled';
  var CLASS_NAME_SHOW$2 = 'show';
  var CLASS_NAME_DROPUP = 'dropup';
  var CLASS_NAME_DROPRIGHT = 'dropright';
  var CLASS_NAME_DROPLEFT = 'dropleft';
  var CLASS_NAME_MENURIGHT = 'dropdown-menu-right';
  var CLASS_NAME_POSITION_STATIC = 'position-static';
  var SELECTOR_DATA_TOGGLE$2 = '[data-toggle="dropdown"]';
  var SELECTOR_FORM_CHILD = '.dropdown form';
  var SELECTOR_MENU = '.dropdown-menu';
  var SELECTOR_NAVBAR_NAV = '.navbar-nav';
  var SELECTOR_VISIBLE_ITEMS = '.dropdown-menu .dropdown-item:not(.disabled):not(:disabled)';
  var PLACEMENT_TOP = 'top-start';
  var PLACEMENT_TOPEND = 'top-end';
  var PLACEMENT_BOTTOM = 'bottom-start';
  var PLACEMENT_BOTTOMEND = 'bottom-end';
  var PLACEMENT_RIGHT = 'right-start';
  var PLACEMENT_LEFT = 'left-start';
  var Default$2 = {
    offset: 0,
    flip: true,
    boundary: 'scrollParent',
    reference: 'toggle',
    display: 'dynamic',
    popperConfig: null
  };
  var DefaultType$2 = {
    offset: '(number|string|function)',
    flip: 'boolean',
    boundary: '(string|element)',
    reference: '(string|element)',
    display: 'string',
    popperConfig: '(null|object)'
  };
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Dropdown = /*#__PURE__*/function () {
    function Dropdown(element, config) {
      this._element = element;
      this._popper = null;
      this._config = this._getConfig(config);
      this._menu = this._getMenuElement();
      this._inNavbar = this._detectNavbar();

      this._addEventListeners();
    } // Getters


    var _proto = Dropdown.prototype; // Public

    _proto.toggle = function toggle() {
      if (this._element.disabled || $(this._element).hasClass(CLASS_NAME_DISABLED)) {
        return;
      }

      var isActive = $(this._menu).hasClass(CLASS_NAME_SHOW$2);

      Dropdown._clearMenus();

      if (isActive) {
        return;
      }

      this.show(true);
    };

    _proto.show = function show(usePopper) {
      if (usePopper === void 0) {
        usePopper = false;
      }

      if (this._element.disabled || $(this._element).hasClass(CLASS_NAME_DISABLED) || $(this._menu).hasClass(CLASS_NAME_SHOW$2)) {
        return;
      }

      var relatedTarget = {
        relatedTarget: this._element
      };
      var showEvent = $.Event(EVENT_SHOW$1, relatedTarget);

      var parent = Dropdown._getParentFromElement(this._element);

      $(parent).trigger(showEvent);

      if (showEvent.isDefaultPrevented()) {
        return;
      } // Disable totally Popper.js for Dropdown in Navbar


      if (!this._inNavbar && usePopper) {
        /**
         * Check for Popper dependency
         * Popper - https://popper.js.org
         */
        if (typeof Popper === 'undefined') {
          throw new TypeError('Bootstrap\'s dropdowns require Popper.js (https://popper.js.org/)');
        }

        var referenceElement = this._element;

        if (this._config.reference === 'parent') {
          referenceElement = parent;
        } else if (Util.isElement(this._config.reference)) {
          referenceElement = this._config.reference; // Check if it's jQuery element

          if (typeof this._config.reference.jquery !== 'undefined') {
            referenceElement = this._config.reference[0];
          }
        } // If boundary is not `scrollParent`, then set position to `static`
        // to allow the menu to "escape" the scroll parent's boundaries
        // https://github.com/twbs/bootstrap/issues/24251


        if (this._config.boundary !== 'scrollParent') {
          $(parent).addClass(CLASS_NAME_POSITION_STATIC);
        }

        this._popper = new Popper(referenceElement, this._menu, this._getPopperConfig());
      } // If this is a touch-enabled device we add extra
      // empty mouseover listeners to the body's immediate children;
      // only needed because of broken event delegation on iOS
      // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html


      if ('ontouchstart' in document.documentElement && $(parent).closest(SELECTOR_NAVBAR_NAV).length === 0) {
        $(document.body).children().on('mouseover', null, $.noop);
      }

      this._element.focus();

      this._element.setAttribute('aria-expanded', true);

      $(this._menu).toggleClass(CLASS_NAME_SHOW$2);
      $(parent).toggleClass(CLASS_NAME_SHOW$2).trigger($.Event(EVENT_SHOWN$1, relatedTarget));
    };

    _proto.hide = function hide() {
      if (this._element.disabled || $(this._element).hasClass(CLASS_NAME_DISABLED) || !$(this._menu).hasClass(CLASS_NAME_SHOW$2)) {
        return;
      }

      var relatedTarget = {
        relatedTarget: this._element
      };
      var hideEvent = $.Event(EVENT_HIDE$1, relatedTarget);

      var parent = Dropdown._getParentFromElement(this._element);

      $(parent).trigger(hideEvent);

      if (hideEvent.isDefaultPrevented()) {
        return;
      }

      if (this._popper) {
        this._popper.destroy();
      }

      $(this._menu).toggleClass(CLASS_NAME_SHOW$2);
      $(parent).toggleClass(CLASS_NAME_SHOW$2).trigger($.Event(EVENT_HIDDEN$1, relatedTarget));
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$4);
      $(this._element).off(EVENT_KEY$4);
      this._element = null;
      this._menu = null;

      if (this._popper !== null) {
        this._popper.destroy();

        this._popper = null;
      }
    };

    _proto.update = function update() {
      this._inNavbar = this._detectNavbar();

      if (this._popper !== null) {
        this._popper.scheduleUpdate();
      }
    } // Private
    ;

    _proto._addEventListeners = function _addEventListeners() {
      var _this = this;

      $(this._element).on(EVENT_CLICK, function (event) {
        event.preventDefault();
        event.stopPropagation();

        _this.toggle();
      });
    };

    _proto._getConfig = function _getConfig(config) {
      config = _extends({}, this.constructor.Default, $(this._element).data(), config);
      Util.typeCheckConfig(NAME$4, config, this.constructor.DefaultType);
      return config;
    };

    _proto._getMenuElement = function _getMenuElement() {
      if (!this._menu) {
        var parent = Dropdown._getParentFromElement(this._element);

        if (parent) {
          this._menu = parent.querySelector(SELECTOR_MENU);
        }
      }

      return this._menu;
    };

    _proto._getPlacement = function _getPlacement() {
      var $parentDropdown = $(this._element.parentNode);
      var placement = PLACEMENT_BOTTOM; // Handle dropup

      if ($parentDropdown.hasClass(CLASS_NAME_DROPUP)) {
        placement = $(this._menu).hasClass(CLASS_NAME_MENURIGHT) ? PLACEMENT_TOPEND : PLACEMENT_TOP;
      } else if ($parentDropdown.hasClass(CLASS_NAME_DROPRIGHT)) {
        placement = PLACEMENT_RIGHT;
      } else if ($parentDropdown.hasClass(CLASS_NAME_DROPLEFT)) {
        placement = PLACEMENT_LEFT;
      } else if ($(this._menu).hasClass(CLASS_NAME_MENURIGHT)) {
        placement = PLACEMENT_BOTTOMEND;
      }

      return placement;
    };

    _proto._detectNavbar = function _detectNavbar() {
      return $(this._element).closest('.navbar').length > 0;
    };

    _proto._getOffset = function _getOffset() {
      var _this2 = this;

      var offset = {};

      if (typeof this._config.offset === 'function') {
        offset.fn = function (data) {
          data.offsets = _extends({}, data.offsets, _this2._config.offset(data.offsets, _this2._element) || {});
          return data;
        };
      } else {
        offset.offset = this._config.offset;
      }

      return offset;
    };

    _proto._getPopperConfig = function _getPopperConfig() {
      var popperConfig = {
        placement: this._getPlacement(),
        modifiers: {
          offset: this._getOffset(),
          flip: {
            enabled: this._config.flip
          },
          preventOverflow: {
            boundariesElement: this._config.boundary
          }
        }
      }; // Disable Popper.js if we have a static display

      if (this._config.display === 'static') {
        popperConfig.modifiers.applyStyle = {
          enabled: false
        };
      }

      return _extends({}, popperConfig, this._config.popperConfig);
    } // Static
    ;

    Dropdown._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$4);

        var _config = _typeof(config) === 'object' ? config : null;

        if (!data) {
          data = new Dropdown(this, _config);
          $(this).data(DATA_KEY$4, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    Dropdown._clearMenus = function _clearMenus(event) {
      if (event && (event.which === RIGHT_MOUSE_BUTTON_WHICH || event.type === 'keyup' && event.which !== TAB_KEYCODE)) {
        return;
      }

      var toggles = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLE$2));

      for (var i = 0, len = toggles.length; i < len; i++) {
        var parent = Dropdown._getParentFromElement(toggles[i]);

        var context = $(toggles[i]).data(DATA_KEY$4);
        var relatedTarget = {
          relatedTarget: toggles[i]
        };

        if (event && event.type === 'click') {
          relatedTarget.clickEvent = event;
        }

        if (!context) {
          continue;
        }

        var dropdownMenu = context._menu;

        if (!$(parent).hasClass(CLASS_NAME_SHOW$2)) {
          continue;
        }

        if (event && (event.type === 'click' && /input|textarea/i.test(event.target.tagName) || event.type === 'keyup' && event.which === TAB_KEYCODE) && $.contains(parent, event.target)) {
          continue;
        }

        var hideEvent = $.Event(EVENT_HIDE$1, relatedTarget);
        $(parent).trigger(hideEvent);

        if (hideEvent.isDefaultPrevented()) {
          continue;
        } // If this is a touch-enabled device we remove the extra
        // empty mouseover listeners we added for iOS support


        if ('ontouchstart' in document.documentElement) {
          $(document.body).children().off('mouseover', null, $.noop);
        }

        toggles[i].setAttribute('aria-expanded', 'false');

        if (context._popper) {
          context._popper.destroy();
        }

        $(dropdownMenu).removeClass(CLASS_NAME_SHOW$2);
        $(parent).removeClass(CLASS_NAME_SHOW$2).trigger($.Event(EVENT_HIDDEN$1, relatedTarget));
      }
    };

    Dropdown._getParentFromElement = function _getParentFromElement(element) {
      var parent;
      var selector = Util.getSelectorFromElement(element);

      if (selector) {
        parent = document.querySelector(selector);
      }

      return parent || element.parentNode;
    } // eslint-disable-next-line complexity
    ;

    Dropdown._dataApiKeydownHandler = function _dataApiKeydownHandler(event) {
      // If not input/textarea:
      //  - And not a key in REGEXP_KEYDOWN => not a dropdown command
      // If input/textarea:
      //  - If space key => not a dropdown command
      //  - If key is other than escape
      //    - If key is not up or down => not a dropdown command
      //    - If trigger inside the menu => not a dropdown command
      if (/input|textarea/i.test(event.target.tagName) ? event.which === SPACE_KEYCODE || event.which !== ESCAPE_KEYCODE && (event.which !== ARROW_DOWN_KEYCODE && event.which !== ARROW_UP_KEYCODE || $(event.target).closest(SELECTOR_MENU).length) : !REGEXP_KEYDOWN.test(event.which)) {
        return;
      }

      if (this.disabled || $(this).hasClass(CLASS_NAME_DISABLED)) {
        return;
      }

      var parent = Dropdown._getParentFromElement(this);

      var isActive = $(parent).hasClass(CLASS_NAME_SHOW$2);

      if (!isActive && event.which === ESCAPE_KEYCODE) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();

      if (!isActive || isActive && (event.which === ESCAPE_KEYCODE || event.which === SPACE_KEYCODE)) {
        if (event.which === ESCAPE_KEYCODE) {
          $(parent.querySelector(SELECTOR_DATA_TOGGLE$2)).trigger('focus');
        }

        $(this).trigger('click');
        return;
      }

      var items = [].slice.call(parent.querySelectorAll(SELECTOR_VISIBLE_ITEMS)).filter(function (item) {
        return $(item).is(':visible');
      });

      if (items.length === 0) {
        return;
      }

      var index = items.indexOf(event.target);

      if (event.which === ARROW_UP_KEYCODE && index > 0) {
        // Up
        index--;
      }

      if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
        // Down
        index++;
      }

      if (index < 0) {
        index = 0;
      }

      items[index].focus();
    };

    _createClass(Dropdown, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$4;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$2;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$2;
      }
    }]);

    return Dropdown;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_KEYDOWN_DATA_API, SELECTOR_DATA_TOGGLE$2, Dropdown._dataApiKeydownHandler).on(EVENT_KEYDOWN_DATA_API, SELECTOR_MENU, Dropdown._dataApiKeydownHandler).on(EVENT_CLICK_DATA_API$4 + " " + EVENT_KEYUP_DATA_API, Dropdown._clearMenus).on(EVENT_CLICK_DATA_API$4, SELECTOR_DATA_TOGGLE$2, function (event) {
    event.preventDefault();
    event.stopPropagation();

    Dropdown._jQueryInterface.call($(this), 'toggle');
  }).on(EVENT_CLICK_DATA_API$4, SELECTOR_FORM_CHILD, function (e) {
    e.stopPropagation();
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$4] = Dropdown._jQueryInterface;
  $.fn[NAME$4].Constructor = Dropdown;

  $.fn[NAME$4].noConflict = function () {
    $.fn[NAME$4] = JQUERY_NO_CONFLICT$4;
    return Dropdown._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$5 = 'modal';
  var VERSION$5 = '4.5.2';
  var DATA_KEY$5 = 'bs.modal';
  var EVENT_KEY$5 = "." + DATA_KEY$5;
  var DATA_API_KEY$5 = '.data-api';
  var JQUERY_NO_CONFLICT$5 = $.fn[NAME$5];
  var ESCAPE_KEYCODE$1 = 27; // KeyboardEvent.which value for Escape (Esc) key

  var Default$3 = {
    backdrop: true,
    keyboard: true,
    focus: true,
    show: true
  };
  var DefaultType$3 = {
    backdrop: '(boolean|string)',
    keyboard: 'boolean',
    focus: 'boolean',
    show: 'boolean'
  };
  var EVENT_HIDE$2 = "hide" + EVENT_KEY$5;
  var EVENT_HIDE_PREVENTED = "hidePrevented" + EVENT_KEY$5;
  var EVENT_HIDDEN$2 = "hidden" + EVENT_KEY$5;
  var EVENT_SHOW$2 = "show" + EVENT_KEY$5;
  var EVENT_SHOWN$2 = "shown" + EVENT_KEY$5;
  var EVENT_FOCUSIN = "focusin" + EVENT_KEY$5;
  var EVENT_RESIZE = "resize" + EVENT_KEY$5;
  var EVENT_CLICK_DISMISS = "click.dismiss" + EVENT_KEY$5;
  var EVENT_KEYDOWN_DISMISS = "keydown.dismiss" + EVENT_KEY$5;
  var EVENT_MOUSEUP_DISMISS = "mouseup.dismiss" + EVENT_KEY$5;
  var EVENT_MOUSEDOWN_DISMISS = "mousedown.dismiss" + EVENT_KEY$5;
  var EVENT_CLICK_DATA_API$5 = "click" + EVENT_KEY$5 + DATA_API_KEY$5;
  var CLASS_NAME_SCROLLABLE = 'modal-dialog-scrollable';
  var CLASS_NAME_SCROLLBAR_MEASURER = 'modal-scrollbar-measure';
  var CLASS_NAME_BACKDROP = 'modal-backdrop';
  var CLASS_NAME_OPEN = 'modal-open';
  var CLASS_NAME_FADE$1 = 'fade';
  var CLASS_NAME_SHOW$3 = 'show';
  var CLASS_NAME_STATIC = 'modal-static';
  var SELECTOR_DIALOG = '.modal-dialog';
  var SELECTOR_MODAL_BODY = '.modal-body';
  var SELECTOR_DATA_TOGGLE$3 = '[data-toggle="modal"]';
  var SELECTOR_DATA_DISMISS = '[data-dismiss="modal"]';
  var SELECTOR_FIXED_CONTENT = '.fixed-top, .fixed-bottom, .is-fixed, .sticky-top';
  var SELECTOR_STICKY_CONTENT = '.sticky-top';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Modal = /*#__PURE__*/function () {
    function Modal(element, config) {
      this._config = this._getConfig(config);
      this._element = element;
      this._dialog = element.querySelector(SELECTOR_DIALOG);
      this._backdrop = null;
      this._isShown = false;
      this._isBodyOverflowing = false;
      this._ignoreBackdropClick = false;
      this._isTransitioning = false;
      this._scrollbarWidth = 0;
    } // Getters


    var _proto = Modal.prototype; // Public

    _proto.toggle = function toggle(relatedTarget) {
      return this._isShown ? this.hide() : this.show(relatedTarget);
    };

    _proto.show = function show(relatedTarget) {
      var _this = this;

      if (this._isShown || this._isTransitioning) {
        return;
      }

      if ($(this._element).hasClass(CLASS_NAME_FADE$1)) {
        this._isTransitioning = true;
      }

      var showEvent = $.Event(EVENT_SHOW$2, {
        relatedTarget: relatedTarget
      });
      $(this._element).trigger(showEvent);

      if (this._isShown || showEvent.isDefaultPrevented()) {
        return;
      }

      this._isShown = true;

      this._checkScrollbar();

      this._setScrollbar();

      this._adjustDialog();

      this._setEscapeEvent();

      this._setResizeEvent();

      $(this._element).on(EVENT_CLICK_DISMISS, SELECTOR_DATA_DISMISS, function (event) {
        return _this.hide(event);
      });
      $(this._dialog).on(EVENT_MOUSEDOWN_DISMISS, function () {
        $(_this._element).one(EVENT_MOUSEUP_DISMISS, function (event) {
          if ($(event.target).is(_this._element)) {
            _this._ignoreBackdropClick = true;
          }
        });
      });

      this._showBackdrop(function () {
        return _this._showElement(relatedTarget);
      });
    };

    _proto.hide = function hide(event) {
      var _this2 = this;

      if (event) {
        event.preventDefault();
      }

      if (!this._isShown || this._isTransitioning) {
        return;
      }

      var hideEvent = $.Event(EVENT_HIDE$2);
      $(this._element).trigger(hideEvent);

      if (!this._isShown || hideEvent.isDefaultPrevented()) {
        return;
      }

      this._isShown = false;
      var transition = $(this._element).hasClass(CLASS_NAME_FADE$1);

      if (transition) {
        this._isTransitioning = true;
      }

      this._setEscapeEvent();

      this._setResizeEvent();

      $(document).off(EVENT_FOCUSIN);
      $(this._element).removeClass(CLASS_NAME_SHOW$3);
      $(this._element).off(EVENT_CLICK_DISMISS);
      $(this._dialog).off(EVENT_MOUSEDOWN_DISMISS);

      if (transition) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, function (event) {
          return _this2._hideModal(event);
        }).emulateTransitionEnd(transitionDuration);
      } else {
        this._hideModal();
      }
    };

    _proto.dispose = function dispose() {
      [window, this._element, this._dialog].forEach(function (htmlElement) {
        return $(htmlElement).off(EVENT_KEY$5);
      });
      /**
       * `document` has 2 events `EVENT_FOCUSIN` and `EVENT_CLICK_DATA_API`
       * Do not move `document` in `htmlElements` array
       * It will remove `EVENT_CLICK_DATA_API` event that should remain
       */

      $(document).off(EVENT_FOCUSIN);
      $.removeData(this._element, DATA_KEY$5);
      this._config = null;
      this._element = null;
      this._dialog = null;
      this._backdrop = null;
      this._isShown = null;
      this._isBodyOverflowing = null;
      this._ignoreBackdropClick = null;
      this._isTransitioning = null;
      this._scrollbarWidth = null;
    };

    _proto.handleUpdate = function handleUpdate() {
      this._adjustDialog();
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _extends({}, Default$3, config);
      Util.typeCheckConfig(NAME$5, config, DefaultType$3);
      return config;
    };

    _proto._triggerBackdropTransition = function _triggerBackdropTransition() {
      var _this3 = this;

      if (this._config.backdrop === 'static') {
        var hideEventPrevented = $.Event(EVENT_HIDE_PREVENTED);
        $(this._element).trigger(hideEventPrevented);

        if (hideEventPrevented.defaultPrevented) {
          return;
        }

        var isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;

        if (!isModalOverflowing) {
          this._element.style.overflowY = 'hidden';
        }

        this._element.classList.add(CLASS_NAME_STATIC);

        var modalTransitionDuration = Util.getTransitionDurationFromElement(this._dialog);
        $(this._element).off(Util.TRANSITION_END);
        $(this._element).one(Util.TRANSITION_END, function () {
          _this3._element.classList.remove(CLASS_NAME_STATIC);

          if (!isModalOverflowing) {
            $(_this3._element).one(Util.TRANSITION_END, function () {
              _this3._element.style.overflowY = '';
            }).emulateTransitionEnd(_this3._element, modalTransitionDuration);
          }
        }).emulateTransitionEnd(modalTransitionDuration);

        this._element.focus();
      } else {
        this.hide();
      }
    };

    _proto._showElement = function _showElement(relatedTarget) {
      var _this4 = this;

      var transition = $(this._element).hasClass(CLASS_NAME_FADE$1);
      var modalBody = this._dialog ? this._dialog.querySelector(SELECTOR_MODAL_BODY) : null;

      if (!this._element.parentNode || this._element.parentNode.nodeType !== Node.ELEMENT_NODE) {
        // Don't move modal's DOM position
        document.body.appendChild(this._element);
      }

      this._element.style.display = 'block';

      this._element.removeAttribute('aria-hidden');

      this._element.setAttribute('aria-modal', true);

      this._element.setAttribute('role', 'dialog');

      if ($(this._dialog).hasClass(CLASS_NAME_SCROLLABLE) && modalBody) {
        modalBody.scrollTop = 0;
      } else {
        this._element.scrollTop = 0;
      }

      if (transition) {
        Util.reflow(this._element);
      }

      $(this._element).addClass(CLASS_NAME_SHOW$3);

      if (this._config.focus) {
        this._enforceFocus();
      }

      var shownEvent = $.Event(EVENT_SHOWN$2, {
        relatedTarget: relatedTarget
      });

      var transitionComplete = function transitionComplete() {
        if (_this4._config.focus) {
          _this4._element.focus();
        }

        _this4._isTransitioning = false;
        $(_this4._element).trigger(shownEvent);
      };

      if (transition) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._dialog);
        $(this._dialog).one(Util.TRANSITION_END, transitionComplete).emulateTransitionEnd(transitionDuration);
      } else {
        transitionComplete();
      }
    };

    _proto._enforceFocus = function _enforceFocus() {
      var _this5 = this;

      $(document).off(EVENT_FOCUSIN) // Guard against infinite focus loop
      .on(EVENT_FOCUSIN, function (event) {
        if (document !== event.target && _this5._element !== event.target && $(_this5._element).has(event.target).length === 0) {
          _this5._element.focus();
        }
      });
    };

    _proto._setEscapeEvent = function _setEscapeEvent() {
      var _this6 = this;

      if (this._isShown) {
        $(this._element).on(EVENT_KEYDOWN_DISMISS, function (event) {
          if (_this6._config.keyboard && event.which === ESCAPE_KEYCODE$1) {
            event.preventDefault();

            _this6.hide();
          } else if (!_this6._config.keyboard && event.which === ESCAPE_KEYCODE$1) {
            _this6._triggerBackdropTransition();
          }
        });
      } else if (!this._isShown) {
        $(this._element).off(EVENT_KEYDOWN_DISMISS);
      }
    };

    _proto._setResizeEvent = function _setResizeEvent() {
      var _this7 = this;

      if (this._isShown) {
        $(window).on(EVENT_RESIZE, function (event) {
          return _this7.handleUpdate(event);
        });
      } else {
        $(window).off(EVENT_RESIZE);
      }
    };

    _proto._hideModal = function _hideModal() {
      var _this8 = this;

      this._element.style.display = 'none';

      this._element.setAttribute('aria-hidden', true);

      this._element.removeAttribute('aria-modal');

      this._element.removeAttribute('role');

      this._isTransitioning = false;

      this._showBackdrop(function () {
        $(document.body).removeClass(CLASS_NAME_OPEN);

        _this8._resetAdjustments();

        _this8._resetScrollbar();

        $(_this8._element).trigger(EVENT_HIDDEN$2);
      });
    };

    _proto._removeBackdrop = function _removeBackdrop() {
      if (this._backdrop) {
        $(this._backdrop).remove();
        this._backdrop = null;
      }
    };

    _proto._showBackdrop = function _showBackdrop(callback) {
      var _this9 = this;

      var animate = $(this._element).hasClass(CLASS_NAME_FADE$1) ? CLASS_NAME_FADE$1 : '';

      if (this._isShown && this._config.backdrop) {
        this._backdrop = document.createElement('div');
        this._backdrop.className = CLASS_NAME_BACKDROP;

        if (animate) {
          this._backdrop.classList.add(animate);
        }

        $(this._backdrop).appendTo(document.body);
        $(this._element).on(EVENT_CLICK_DISMISS, function (event) {
          if (_this9._ignoreBackdropClick) {
            _this9._ignoreBackdropClick = false;
            return;
          }

          if (event.target !== event.currentTarget) {
            return;
          }

          _this9._triggerBackdropTransition();
        });

        if (animate) {
          Util.reflow(this._backdrop);
        }

        $(this._backdrop).addClass(CLASS_NAME_SHOW$3);

        if (!callback) {
          return;
        }

        if (!animate) {
          callback();
          return;
        }

        var backdropTransitionDuration = Util.getTransitionDurationFromElement(this._backdrop);
        $(this._backdrop).one(Util.TRANSITION_END, callback).emulateTransitionEnd(backdropTransitionDuration);
      } else if (!this._isShown && this._backdrop) {
        $(this._backdrop).removeClass(CLASS_NAME_SHOW$3);

        var callbackRemove = function callbackRemove() {
          _this9._removeBackdrop();

          if (callback) {
            callback();
          }
        };

        if ($(this._element).hasClass(CLASS_NAME_FADE$1)) {
          var _backdropTransitionDuration = Util.getTransitionDurationFromElement(this._backdrop);

          $(this._backdrop).one(Util.TRANSITION_END, callbackRemove).emulateTransitionEnd(_backdropTransitionDuration);
        } else {
          callbackRemove();
        }
      } else if (callback) {
        callback();
      }
    } // ----------------------------------------------------------------------
    // the following methods are used to handle overflowing modals
    // todo (fat): these should probably be refactored out of modal.js
    // ----------------------------------------------------------------------
    ;

    _proto._adjustDialog = function _adjustDialog() {
      var isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;

      if (!this._isBodyOverflowing && isModalOverflowing) {
        this._element.style.paddingLeft = this._scrollbarWidth + "px";
      }

      if (this._isBodyOverflowing && !isModalOverflowing) {
        this._element.style.paddingRight = this._scrollbarWidth + "px";
      }
    };

    _proto._resetAdjustments = function _resetAdjustments() {
      this._element.style.paddingLeft = '';
      this._element.style.paddingRight = '';
    };

    _proto._checkScrollbar = function _checkScrollbar() {
      var rect = document.body.getBoundingClientRect();
      this._isBodyOverflowing = Math.round(rect.left + rect.right) < window.innerWidth;
      this._scrollbarWidth = this._getScrollbarWidth();
    };

    _proto._setScrollbar = function _setScrollbar() {
      var _this10 = this;

      if (this._isBodyOverflowing) {
        // Note: DOMNode.style.paddingRight returns the actual value or '' if not set
        //   while $(DOMNode).css('padding-right') returns the calculated value or 0 if not set
        var fixedContent = [].slice.call(document.querySelectorAll(SELECTOR_FIXED_CONTENT));
        var stickyContent = [].slice.call(document.querySelectorAll(SELECTOR_STICKY_CONTENT)); // Adjust fixed content padding

        $(fixedContent).each(function (index, element) {
          var actualPadding = element.style.paddingRight;
          var calculatedPadding = $(element).css('padding-right');
          $(element).data('padding-right', actualPadding).css('padding-right', parseFloat(calculatedPadding) + _this10._scrollbarWidth + "px");
        }); // Adjust sticky content margin

        $(stickyContent).each(function (index, element) {
          var actualMargin = element.style.marginRight;
          var calculatedMargin = $(element).css('margin-right');
          $(element).data('margin-right', actualMargin).css('margin-right', parseFloat(calculatedMargin) - _this10._scrollbarWidth + "px");
        }); // Adjust body padding

        var actualPadding = document.body.style.paddingRight;
        var calculatedPadding = $(document.body).css('padding-right');
        $(document.body).data('padding-right', actualPadding).css('padding-right', parseFloat(calculatedPadding) + this._scrollbarWidth + "px");
      }

      $(document.body).addClass(CLASS_NAME_OPEN);
    };

    _proto._resetScrollbar = function _resetScrollbar() {
      // Restore fixed content padding
      var fixedContent = [].slice.call(document.querySelectorAll(SELECTOR_FIXED_CONTENT));
      $(fixedContent).each(function (index, element) {
        var padding = $(element).data('padding-right');
        $(element).removeData('padding-right');
        element.style.paddingRight = padding ? padding : '';
      }); // Restore sticky content

      var elements = [].slice.call(document.querySelectorAll("" + SELECTOR_STICKY_CONTENT));
      $(elements).each(function (index, element) {
        var margin = $(element).data('margin-right');

        if (typeof margin !== 'undefined') {
          $(element).css('margin-right', margin).removeData('margin-right');
        }
      }); // Restore body padding

      var padding = $(document.body).data('padding-right');
      $(document.body).removeData('padding-right');
      document.body.style.paddingRight = padding ? padding : '';
    };

    _proto._getScrollbarWidth = function _getScrollbarWidth() {
      // thx d.walsh
      var scrollDiv = document.createElement('div');
      scrollDiv.className = CLASS_NAME_SCROLLBAR_MEASURER;
      document.body.appendChild(scrollDiv);
      var scrollbarWidth = scrollDiv.getBoundingClientRect().width - scrollDiv.clientWidth;
      document.body.removeChild(scrollDiv);
      return scrollbarWidth;
    } // Static
    ;

    Modal._jQueryInterface = function _jQueryInterface(config, relatedTarget) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$5);

        var _config = _extends({}, Default$3, $(this).data(), _typeof(config) === 'object' && config ? config : {});

        if (!data) {
          data = new Modal(this, _config);
          $(this).data(DATA_KEY$5, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config](relatedTarget);
        } else if (_config.show) {
          data.show(relatedTarget);
        }
      });
    };

    _createClass(Modal, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$5;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$3;
      }
    }]);

    return Modal;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$5, SELECTOR_DATA_TOGGLE$3, function (event) {
    var _this11 = this;

    var target;
    var selector = Util.getSelectorFromElement(this);

    if (selector) {
      target = document.querySelector(selector);
    }

    var config = $(target).data(DATA_KEY$5) ? 'toggle' : _extends({}, $(target).data(), $(this).data());

    if (this.tagName === 'A' || this.tagName === 'AREA') {
      event.preventDefault();
    }

    var $target = $(target).one(EVENT_SHOW$2, function (showEvent) {
      if (showEvent.isDefaultPrevented()) {
        // Only register focus restorer if modal will actually get shown
        return;
      }

      $target.one(EVENT_HIDDEN$2, function () {
        if ($(_this11).is(':visible')) {
          _this11.focus();
        }
      });
    });

    Modal._jQueryInterface.call($(target), config, this);
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$5] = Modal._jQueryInterface;
  $.fn[NAME$5].Constructor = Modal;

  $.fn[NAME$5].noConflict = function () {
    $.fn[NAME$5] = JQUERY_NO_CONFLICT$5;
    return Modal._jQueryInterface;
  };
  /**
   * --------------------------------------------------------------------------
   * Bootstrap (v4.5.2): tools/sanitizer.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
   * --------------------------------------------------------------------------
   */


  var uriAttrs = ['background', 'cite', 'href', 'itemtype', 'longdesc', 'poster', 'src', 'xlink:href'];
  var ARIA_ATTRIBUTE_PATTERN = /^aria-[\w-]*$/i;
  var DefaultWhitelist = {
    // Global attributes allowed on any supplied element below.
    '*': ['class', 'dir', 'id', 'lang', 'role', ARIA_ATTRIBUTE_PATTERN],
    a: ['target', 'href', 'title', 'rel'],
    area: [],
    b: [],
    br: [],
    col: [],
    code: [],
    div: [],
    em: [],
    hr: [],
    h1: [],
    h2: [],
    h3: [],
    h4: [],
    h5: [],
    h6: [],
    i: [],
    img: ['src', 'srcset', 'alt', 'title', 'width', 'height'],
    li: [],
    ol: [],
    p: [],
    pre: [],
    s: [],
    small: [],
    span: [],
    sub: [],
    sup: [],
    strong: [],
    u: [],
    ul: []
  };
  /**
   * A pattern that recognizes a commonly useful subset of URLs that are safe.
   *
   * Shoutout to Angular 7 https://github.com/angular/angular/blob/7.2.4/packages/core/src/sanitization/url_sanitizer.ts
   */

  var SAFE_URL_PATTERN = /^(?:(?:https?|mailto|ftp|tel|file):|[^#&/:?]*(?:[#/?]|$))/gi;
  /**
   * A pattern that matches safe data URLs. Only matches image, video and audio types.
   *
   * Shoutout to Angular 7 https://github.com/angular/angular/blob/7.2.4/packages/core/src/sanitization/url_sanitizer.ts
   */

  var DATA_URL_PATTERN = /^data:(?:image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp)|video\/(?:mpeg|mp4|ogg|webm)|audio\/(?:mp3|oga|ogg|opus));base64,[\d+/a-z]+=*$/i;

  function allowedAttribute(attr, allowedAttributeList) {
    var attrName = attr.nodeName.toLowerCase();

    if (allowedAttributeList.indexOf(attrName) !== -1) {
      if (uriAttrs.indexOf(attrName) !== -1) {
        return Boolean(attr.nodeValue.match(SAFE_URL_PATTERN) || attr.nodeValue.match(DATA_URL_PATTERN));
      }

      return true;
    }

    var regExp = allowedAttributeList.filter(function (attrRegex) {
      return attrRegex instanceof RegExp;
    }); // Check if a regular expression validates the attribute.

    for (var i = 0, len = regExp.length; i < len; i++) {
      if (attrName.match(regExp[i])) {
        return true;
      }
    }

    return false;
  }

  function sanitizeHtml(unsafeHtml, whiteList, sanitizeFn) {
    if (unsafeHtml.length === 0) {
      return unsafeHtml;
    }

    if (sanitizeFn && typeof sanitizeFn === 'function') {
      return sanitizeFn(unsafeHtml);
    }

    var domParser = new window.DOMParser();
    var createdDocument = domParser.parseFromString(unsafeHtml, 'text/html');
    var whitelistKeys = Object.keys(whiteList);
    var elements = [].slice.call(createdDocument.body.querySelectorAll('*'));

    var _loop = function _loop(i, len) {
      var el = elements[i];
      var elName = el.nodeName.toLowerCase();

      if (whitelistKeys.indexOf(el.nodeName.toLowerCase()) === -1) {
        el.parentNode.removeChild(el);
        return "continue";
      }

      var attributeList = [].slice.call(el.attributes);
      var whitelistedAttributes = [].concat(whiteList['*'] || [], whiteList[elName] || []);
      attributeList.forEach(function (attr) {
        if (!allowedAttribute(attr, whitelistedAttributes)) {
          el.removeAttribute(attr.nodeName);
        }
      });
    };

    for (var i = 0, len = elements.length; i < len; i++) {
      var _ret = _loop(i);

      if (_ret === "continue") continue;
    }

    return createdDocument.body.innerHTML;
  }
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$6 = 'tooltip';
  var VERSION$6 = '4.5.2';
  var DATA_KEY$6 = 'bs.tooltip';
  var EVENT_KEY$6 = "." + DATA_KEY$6;
  var JQUERY_NO_CONFLICT$6 = $.fn[NAME$6];
  var CLASS_PREFIX = 'bs-tooltip';
  var BSCLS_PREFIX_REGEX = new RegExp("(^|\\s)" + CLASS_PREFIX + "\\S+", 'g');
  var DISALLOWED_ATTRIBUTES = ['sanitize', 'whiteList', 'sanitizeFn'];
  var DefaultType$4 = {
    animation: 'boolean',
    template: 'string',
    title: '(string|element|function)',
    trigger: 'string',
    delay: '(number|object)',
    html: 'boolean',
    selector: '(string|boolean)',
    placement: '(string|function)',
    offset: '(number|string|function)',
    container: '(string|element|boolean)',
    fallbackPlacement: '(string|array)',
    boundary: '(string|element)',
    sanitize: 'boolean',
    sanitizeFn: '(null|function)',
    whiteList: 'object',
    popperConfig: '(null|object)'
  };
  var AttachmentMap = {
    AUTO: 'auto',
    TOP: 'top',
    RIGHT: 'right',
    BOTTOM: 'bottom',
    LEFT: 'left'
  };
  var Default$4 = {
    animation: true,
    template: '<div class="tooltip" role="tooltip">' + '<div class="arrow"></div>' + '<div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    selector: false,
    placement: 'top',
    offset: 0,
    container: false,
    fallbackPlacement: 'flip',
    boundary: 'scrollParent',
    sanitize: true,
    sanitizeFn: null,
    whiteList: DefaultWhitelist,
    popperConfig: null
  };
  var HOVER_STATE_SHOW = 'show';
  var HOVER_STATE_OUT = 'out';
  var Event = {
    HIDE: "hide" + EVENT_KEY$6,
    HIDDEN: "hidden" + EVENT_KEY$6,
    SHOW: "show" + EVENT_KEY$6,
    SHOWN: "shown" + EVENT_KEY$6,
    INSERTED: "inserted" + EVENT_KEY$6,
    CLICK: "click" + EVENT_KEY$6,
    FOCUSIN: "focusin" + EVENT_KEY$6,
    FOCUSOUT: "focusout" + EVENT_KEY$6,
    MOUSEENTER: "mouseenter" + EVENT_KEY$6,
    MOUSELEAVE: "mouseleave" + EVENT_KEY$6
  };
  var CLASS_NAME_FADE$2 = 'fade';
  var CLASS_NAME_SHOW$4 = 'show';
  var SELECTOR_TOOLTIP_INNER = '.tooltip-inner';
  var SELECTOR_ARROW = '.arrow';
  var TRIGGER_HOVER = 'hover';
  var TRIGGER_FOCUS = 'focus';
  var TRIGGER_CLICK = 'click';
  var TRIGGER_MANUAL = 'manual';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Tooltip = /*#__PURE__*/function () {
    function Tooltip(element, config) {
      if (typeof Popper === 'undefined') {
        throw new TypeError('Bootstrap\'s tooltips require Popper.js (https://popper.js.org/)');
      } // private


      this._isEnabled = true;
      this._timeout = 0;
      this._hoverState = '';
      this._activeTrigger = {};
      this._popper = null; // Protected

      this.element = element;
      this.config = this._getConfig(config);
      this.tip = null;

      this._setListeners();
    } // Getters


    var _proto = Tooltip.prototype; // Public

    _proto.enable = function enable() {
      this._isEnabled = true;
    };

    _proto.disable = function disable() {
      this._isEnabled = false;
    };

    _proto.toggleEnabled = function toggleEnabled() {
      this._isEnabled = !this._isEnabled;
    };

    _proto.toggle = function toggle(event) {
      if (!this._isEnabled) {
        return;
      }

      if (event) {
        var dataKey = this.constructor.DATA_KEY;
        var context = $(event.currentTarget).data(dataKey);

        if (!context) {
          context = new this.constructor(event.currentTarget, this._getDelegateConfig());
          $(event.currentTarget).data(dataKey, context);
        }

        context._activeTrigger.click = !context._activeTrigger.click;

        if (context._isWithActiveTrigger()) {
          context._enter(null, context);
        } else {
          context._leave(null, context);
        }
      } else {
        if ($(this.getTipElement()).hasClass(CLASS_NAME_SHOW$4)) {
          this._leave(null, this);

          return;
        }

        this._enter(null, this);
      }
    };

    _proto.dispose = function dispose() {
      clearTimeout(this._timeout);
      $.removeData(this.element, this.constructor.DATA_KEY);
      $(this.element).off(this.constructor.EVENT_KEY);
      $(this.element).closest('.modal').off('hide.bs.modal', this._hideModalHandler);

      if (this.tip) {
        $(this.tip).remove();
      }

      this._isEnabled = null;
      this._timeout = null;
      this._hoverState = null;
      this._activeTrigger = null;

      if (this._popper) {
        this._popper.destroy();
      }

      this._popper = null;
      this.element = null;
      this.config = null;
      this.tip = null;
    };

    _proto.show = function show() {
      var _this = this;

      if ($(this.element).css('display') === 'none') {
        throw new Error('Please use show on visible elements');
      }

      var showEvent = $.Event(this.constructor.Event.SHOW);

      if (this.isWithContent() && this._isEnabled) {
        $(this.element).trigger(showEvent);
        var shadowRoot = Util.findShadowRoot(this.element);
        var isInTheDom = $.contains(shadowRoot !== null ? shadowRoot : this.element.ownerDocument.documentElement, this.element);

        if (showEvent.isDefaultPrevented() || !isInTheDom) {
          return;
        }

        var tip = this.getTipElement();
        var tipId = Util.getUID(this.constructor.NAME);
        tip.setAttribute('id', tipId);
        this.element.setAttribute('aria-describedby', tipId);
        this.setContent();

        if (this.config.animation) {
          $(tip).addClass(CLASS_NAME_FADE$2);
        }

        var placement = typeof this.config.placement === 'function' ? this.config.placement.call(this, tip, this.element) : this.config.placement;

        var attachment = this._getAttachment(placement);

        this.addAttachmentClass(attachment);

        var container = this._getContainer();

        $(tip).data(this.constructor.DATA_KEY, this);

        if (!$.contains(this.element.ownerDocument.documentElement, this.tip)) {
          $(tip).appendTo(container);
        }

        $(this.element).trigger(this.constructor.Event.INSERTED);
        this._popper = new Popper(this.element, tip, this._getPopperConfig(attachment));
        $(tip).addClass(CLASS_NAME_SHOW$4); // If this is a touch-enabled device we add extra
        // empty mouseover listeners to the body's immediate children;
        // only needed because of broken event delegation on iOS
        // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html

        if ('ontouchstart' in document.documentElement) {
          $(document.body).children().on('mouseover', null, $.noop);
        }

        var complete = function complete() {
          if (_this.config.animation) {
            _this._fixTransition();
          }

          var prevHoverState = _this._hoverState;
          _this._hoverState = null;
          $(_this.element).trigger(_this.constructor.Event.SHOWN);

          if (prevHoverState === HOVER_STATE_OUT) {
            _this._leave(null, _this);
          }
        };

        if ($(this.tip).hasClass(CLASS_NAME_FADE$2)) {
          var transitionDuration = Util.getTransitionDurationFromElement(this.tip);
          $(this.tip).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
        } else {
          complete();
        }
      }
    };

    _proto.hide = function hide(callback) {
      var _this2 = this;

      var tip = this.getTipElement();
      var hideEvent = $.Event(this.constructor.Event.HIDE);

      var complete = function complete() {
        if (_this2._hoverState !== HOVER_STATE_SHOW && tip.parentNode) {
          tip.parentNode.removeChild(tip);
        }

        _this2._cleanTipClass();

        _this2.element.removeAttribute('aria-describedby');

        $(_this2.element).trigger(_this2.constructor.Event.HIDDEN);

        if (_this2._popper !== null) {
          _this2._popper.destroy();
        }

        if (callback) {
          callback();
        }
      };

      $(this.element).trigger(hideEvent);

      if (hideEvent.isDefaultPrevented()) {
        return;
      }

      $(tip).removeClass(CLASS_NAME_SHOW$4); // If this is a touch-enabled device we remove the extra
      // empty mouseover listeners we added for iOS support

      if ('ontouchstart' in document.documentElement) {
        $(document.body).children().off('mouseover', null, $.noop);
      }

      this._activeTrigger[TRIGGER_CLICK] = false;
      this._activeTrigger[TRIGGER_FOCUS] = false;
      this._activeTrigger[TRIGGER_HOVER] = false;

      if ($(this.tip).hasClass(CLASS_NAME_FADE$2)) {
        var transitionDuration = Util.getTransitionDurationFromElement(tip);
        $(tip).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }

      this._hoverState = '';
    };

    _proto.update = function update() {
      if (this._popper !== null) {
        this._popper.scheduleUpdate();
      }
    } // Protected
    ;

    _proto.isWithContent = function isWithContent() {
      return Boolean(this.getTitle());
    };

    _proto.addAttachmentClass = function addAttachmentClass(attachment) {
      $(this.getTipElement()).addClass(CLASS_PREFIX + "-" + attachment);
    };

    _proto.getTipElement = function getTipElement() {
      this.tip = this.tip || $(this.config.template)[0];
      return this.tip;
    };

    _proto.setContent = function setContent() {
      var tip = this.getTipElement();
      this.setElementContent($(tip.querySelectorAll(SELECTOR_TOOLTIP_INNER)), this.getTitle());
      $(tip).removeClass(CLASS_NAME_FADE$2 + " " + CLASS_NAME_SHOW$4);
    };

    _proto.setElementContent = function setElementContent($element, content) {
      if (_typeof(content) === 'object' && (content.nodeType || content.jquery)) {
        // Content is a DOM node or a jQuery
        if (this.config.html) {
          if (!$(content).parent().is($element)) {
            $element.empty().append(content);
          }
        } else {
          $element.text($(content).text());
        }

        return;
      }

      if (this.config.html) {
        if (this.config.sanitize) {
          content = sanitizeHtml(content, this.config.whiteList, this.config.sanitizeFn);
        }

        $element.html(content);
      } else {
        $element.text(content);
      }
    };

    _proto.getTitle = function getTitle() {
      var title = this.element.getAttribute('data-original-title');

      if (!title) {
        title = typeof this.config.title === 'function' ? this.config.title.call(this.element) : this.config.title;
      }

      return title;
    } // Private
    ;

    _proto._getPopperConfig = function _getPopperConfig(attachment) {
      var _this3 = this;

      var defaultBsConfig = {
        placement: attachment,
        modifiers: {
          offset: this._getOffset(),
          flip: {
            behavior: this.config.fallbackPlacement
          },
          arrow: {
            element: SELECTOR_ARROW
          },
          preventOverflow: {
            boundariesElement: this.config.boundary
          }
        },
        onCreate: function onCreate(data) {
          if (data.originalPlacement !== data.placement) {
            _this3._handlePopperPlacementChange(data);
          }
        },
        onUpdate: function onUpdate(data) {
          return _this3._handlePopperPlacementChange(data);
        }
      };
      return _extends({}, defaultBsConfig, this.config.popperConfig);
    };

    _proto._getOffset = function _getOffset() {
      var _this4 = this;

      var offset = {};

      if (typeof this.config.offset === 'function') {
        offset.fn = function (data) {
          data.offsets = _extends({}, data.offsets, _this4.config.offset(data.offsets, _this4.element) || {});
          return data;
        };
      } else {
        offset.offset = this.config.offset;
      }

      return offset;
    };

    _proto._getContainer = function _getContainer() {
      if (this.config.container === false) {
        return document.body;
      }

      if (Util.isElement(this.config.container)) {
        return $(this.config.container);
      }

      return $(document).find(this.config.container);
    };

    _proto._getAttachment = function _getAttachment(placement) {
      return AttachmentMap[placement.toUpperCase()];
    };

    _proto._setListeners = function _setListeners() {
      var _this5 = this;

      var triggers = this.config.trigger.split(' ');
      triggers.forEach(function (trigger) {
        if (trigger === 'click') {
          $(_this5.element).on(_this5.constructor.Event.CLICK, _this5.config.selector, function (event) {
            return _this5.toggle(event);
          });
        } else if (trigger !== TRIGGER_MANUAL) {
          var eventIn = trigger === TRIGGER_HOVER ? _this5.constructor.Event.MOUSEENTER : _this5.constructor.Event.FOCUSIN;
          var eventOut = trigger === TRIGGER_HOVER ? _this5.constructor.Event.MOUSELEAVE : _this5.constructor.Event.FOCUSOUT;
          $(_this5.element).on(eventIn, _this5.config.selector, function (event) {
            return _this5._enter(event);
          }).on(eventOut, _this5.config.selector, function (event) {
            return _this5._leave(event);
          });
        }
      });

      this._hideModalHandler = function () {
        if (_this5.element) {
          _this5.hide();
        }
      };

      $(this.element).closest('.modal').on('hide.bs.modal', this._hideModalHandler);

      if (this.config.selector) {
        this.config = _extends({}, this.config, {
          trigger: 'manual',
          selector: ''
        });
      } else {
        this._fixTitle();
      }
    };

    _proto._fixTitle = function _fixTitle() {
      var titleType = _typeof(this.element.getAttribute('data-original-title'));

      if (this.element.getAttribute('title') || titleType !== 'string') {
        this.element.setAttribute('data-original-title', this.element.getAttribute('title') || '');
        this.element.setAttribute('title', '');
      }
    };

    _proto._enter = function _enter(event, context) {
      var dataKey = this.constructor.DATA_KEY;
      context = context || $(event.currentTarget).data(dataKey);

      if (!context) {
        context = new this.constructor(event.currentTarget, this._getDelegateConfig());
        $(event.currentTarget).data(dataKey, context);
      }

      if (event) {
        context._activeTrigger[event.type === 'focusin' ? TRIGGER_FOCUS : TRIGGER_HOVER] = true;
      }

      if ($(context.getTipElement()).hasClass(CLASS_NAME_SHOW$4) || context._hoverState === HOVER_STATE_SHOW) {
        context._hoverState = HOVER_STATE_SHOW;
        return;
      }

      clearTimeout(context._timeout);
      context._hoverState = HOVER_STATE_SHOW;

      if (!context.config.delay || !context.config.delay.show) {
        context.show();
        return;
      }

      context._timeout = setTimeout(function () {
        if (context._hoverState === HOVER_STATE_SHOW) {
          context.show();
        }
      }, context.config.delay.show);
    };

    _proto._leave = function _leave(event, context) {
      var dataKey = this.constructor.DATA_KEY;
      context = context || $(event.currentTarget).data(dataKey);

      if (!context) {
        context = new this.constructor(event.currentTarget, this._getDelegateConfig());
        $(event.currentTarget).data(dataKey, context);
      }

      if (event) {
        context._activeTrigger[event.type === 'focusout' ? TRIGGER_FOCUS : TRIGGER_HOVER] = false;
      }

      if (context._isWithActiveTrigger()) {
        return;
      }

      clearTimeout(context._timeout);
      context._hoverState = HOVER_STATE_OUT;

      if (!context.config.delay || !context.config.delay.hide) {
        context.hide();
        return;
      }

      context._timeout = setTimeout(function () {
        if (context._hoverState === HOVER_STATE_OUT) {
          context.hide();
        }
      }, context.config.delay.hide);
    };

    _proto._isWithActiveTrigger = function _isWithActiveTrigger() {
      for (var trigger in this._activeTrigger) {
        if (this._activeTrigger[trigger]) {
          return true;
        }
      }

      return false;
    };

    _proto._getConfig = function _getConfig(config) {
      var dataAttributes = $(this.element).data();
      Object.keys(dataAttributes).forEach(function (dataAttr) {
        if (DISALLOWED_ATTRIBUTES.indexOf(dataAttr) !== -1) {
          delete dataAttributes[dataAttr];
        }
      });
      config = _extends({}, this.constructor.Default, dataAttributes, _typeof(config) === 'object' && config ? config : {});

      if (typeof config.delay === 'number') {
        config.delay = {
          show: config.delay,
          hide: config.delay
        };
      }

      if (typeof config.title === 'number') {
        config.title = config.title.toString();
      }

      if (typeof config.content === 'number') {
        config.content = config.content.toString();
      }

      Util.typeCheckConfig(NAME$6, config, this.constructor.DefaultType);

      if (config.sanitize) {
        config.template = sanitizeHtml(config.template, config.whiteList, config.sanitizeFn);
      }

      return config;
    };

    _proto._getDelegateConfig = function _getDelegateConfig() {
      var config = {};

      if (this.config) {
        for (var key in this.config) {
          if (this.constructor.Default[key] !== this.config[key]) {
            config[key] = this.config[key];
          }
        }
      }

      return config;
    };

    _proto._cleanTipClass = function _cleanTipClass() {
      var $tip = $(this.getTipElement());
      var tabClass = $tip.attr('class').match(BSCLS_PREFIX_REGEX);

      if (tabClass !== null && tabClass.length) {
        $tip.removeClass(tabClass.join(''));
      }
    };

    _proto._handlePopperPlacementChange = function _handlePopperPlacementChange(popperData) {
      this.tip = popperData.instance.popper;

      this._cleanTipClass();

      this.addAttachmentClass(this._getAttachment(popperData.placement));
    };

    _proto._fixTransition = function _fixTransition() {
      var tip = this.getTipElement();
      var initConfigAnimation = this.config.animation;

      if (tip.getAttribute('x-placement') !== null) {
        return;
      }

      $(tip).removeClass(CLASS_NAME_FADE$2);
      this.config.animation = false;
      this.hide();
      this.show();
      this.config.animation = initConfigAnimation;
    } // Static
    ;

    Tooltip._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$6);

        var _config = _typeof(config) === 'object' && config;

        if (!data && /dispose|hide/.test(config)) {
          return;
        }

        if (!data) {
          data = new Tooltip(this, _config);
          $(this).data(DATA_KEY$6, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Tooltip, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$6;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$4;
      }
    }, {
      key: "NAME",
      get: function get() {
        return NAME$6;
      }
    }, {
      key: "DATA_KEY",
      get: function get() {
        return DATA_KEY$6;
      }
    }, {
      key: "Event",
      get: function get() {
        return Event;
      }
    }, {
      key: "EVENT_KEY",
      get: function get() {
        return EVENT_KEY$6;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$4;
      }
    }]);

    return Tooltip;
  }();
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */


  $.fn[NAME$6] = Tooltip._jQueryInterface;
  $.fn[NAME$6].Constructor = Tooltip;

  $.fn[NAME$6].noConflict = function () {
    $.fn[NAME$6] = JQUERY_NO_CONFLICT$6;
    return Tooltip._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$7 = 'popover';
  var VERSION$7 = '4.5.2';
  var DATA_KEY$7 = 'bs.popover';
  var EVENT_KEY$7 = "." + DATA_KEY$7;
  var JQUERY_NO_CONFLICT$7 = $.fn[NAME$7];
  var CLASS_PREFIX$1 = 'bs-popover';
  var BSCLS_PREFIX_REGEX$1 = new RegExp("(^|\\s)" + CLASS_PREFIX$1 + "\\S+", 'g');

  var Default$5 = _extends({}, Tooltip.Default, {
    placement: 'right',
    trigger: 'click',
    content: '',
    template: '<div class="popover" role="tooltip">' + '<div class="arrow"></div>' + '<h3 class="popover-header"></h3>' + '<div class="popover-body"></div></div>'
  });

  var DefaultType$5 = _extends({}, Tooltip.DefaultType, {
    content: '(string|element|function)'
  });

  var CLASS_NAME_FADE$3 = 'fade';
  var CLASS_NAME_SHOW$5 = 'show';
  var SELECTOR_TITLE = '.popover-header';
  var SELECTOR_CONTENT = '.popover-body';
  var Event$1 = {
    HIDE: "hide" + EVENT_KEY$7,
    HIDDEN: "hidden" + EVENT_KEY$7,
    SHOW: "show" + EVENT_KEY$7,
    SHOWN: "shown" + EVENT_KEY$7,
    INSERTED: "inserted" + EVENT_KEY$7,
    CLICK: "click" + EVENT_KEY$7,
    FOCUSIN: "focusin" + EVENT_KEY$7,
    FOCUSOUT: "focusout" + EVENT_KEY$7,
    MOUSEENTER: "mouseenter" + EVENT_KEY$7,
    MOUSELEAVE: "mouseleave" + EVENT_KEY$7
  };
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Popover = /*#__PURE__*/function (_Tooltip) {
    _inheritsLoose(Popover, _Tooltip);

    function Popover() {
      return _Tooltip.apply(this, arguments) || this;
    }

    var _proto = Popover.prototype; // Overrides

    _proto.isWithContent = function isWithContent() {
      return this.getTitle() || this._getContent();
    };

    _proto.addAttachmentClass = function addAttachmentClass(attachment) {
      $(this.getTipElement()).addClass(CLASS_PREFIX$1 + "-" + attachment);
    };

    _proto.getTipElement = function getTipElement() {
      this.tip = this.tip || $(this.config.template)[0];
      return this.tip;
    };

    _proto.setContent = function setContent() {
      var $tip = $(this.getTipElement()); // We use append for html objects to maintain js events

      this.setElementContent($tip.find(SELECTOR_TITLE), this.getTitle());

      var content = this._getContent();

      if (typeof content === 'function') {
        content = content.call(this.element);
      }

      this.setElementContent($tip.find(SELECTOR_CONTENT), content);
      $tip.removeClass(CLASS_NAME_FADE$3 + " " + CLASS_NAME_SHOW$5);
    } // Private
    ;

    _proto._getContent = function _getContent() {
      return this.element.getAttribute('data-content') || this.config.content;
    };

    _proto._cleanTipClass = function _cleanTipClass() {
      var $tip = $(this.getTipElement());
      var tabClass = $tip.attr('class').match(BSCLS_PREFIX_REGEX$1);

      if (tabClass !== null && tabClass.length > 0) {
        $tip.removeClass(tabClass.join(''));
      }
    } // Static
    ;

    Popover._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$7);

        var _config = _typeof(config) === 'object' ? config : null;

        if (!data && /dispose|hide/.test(config)) {
          return;
        }

        if (!data) {
          data = new Popover(this, _config);
          $(this).data(DATA_KEY$7, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Popover, null, [{
      key: "VERSION",
      // Getters
      get: function get() {
        return VERSION$7;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$5;
      }
    }, {
      key: "NAME",
      get: function get() {
        return NAME$7;
      }
    }, {
      key: "DATA_KEY",
      get: function get() {
        return DATA_KEY$7;
      }
    }, {
      key: "Event",
      get: function get() {
        return Event$1;
      }
    }, {
      key: "EVENT_KEY",
      get: function get() {
        return EVENT_KEY$7;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$5;
      }
    }]);

    return Popover;
  }(Tooltip);
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */


  $.fn[NAME$7] = Popover._jQueryInterface;
  $.fn[NAME$7].Constructor = Popover;

  $.fn[NAME$7].noConflict = function () {
    $.fn[NAME$7] = JQUERY_NO_CONFLICT$7;
    return Popover._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$8 = 'scrollspy';
  var VERSION$8 = '4.5.2';
  var DATA_KEY$8 = 'bs.scrollspy';
  var EVENT_KEY$8 = "." + DATA_KEY$8;
  var DATA_API_KEY$6 = '.data-api';
  var JQUERY_NO_CONFLICT$8 = $.fn[NAME$8];
  var Default$6 = {
    offset: 10,
    method: 'auto',
    target: ''
  };
  var DefaultType$6 = {
    offset: 'number',
    method: 'string',
    target: '(string|element)'
  };
  var EVENT_ACTIVATE = "activate" + EVENT_KEY$8;
  var EVENT_SCROLL = "scroll" + EVENT_KEY$8;
  var EVENT_LOAD_DATA_API$2 = "load" + EVENT_KEY$8 + DATA_API_KEY$6;
  var CLASS_NAME_DROPDOWN_ITEM = 'dropdown-item';
  var CLASS_NAME_ACTIVE$2 = 'active';
  var SELECTOR_DATA_SPY = '[data-spy="scroll"]';
  var SELECTOR_NAV_LIST_GROUP = '.nav, .list-group';
  var SELECTOR_NAV_LINKS = '.nav-link';
  var SELECTOR_NAV_ITEMS = '.nav-item';
  var SELECTOR_LIST_ITEMS = '.list-group-item';
  var SELECTOR_DROPDOWN = '.dropdown';
  var SELECTOR_DROPDOWN_ITEMS = '.dropdown-item';
  var SELECTOR_DROPDOWN_TOGGLE = '.dropdown-toggle';
  var METHOD_OFFSET = 'offset';
  var METHOD_POSITION = 'position';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var ScrollSpy = /*#__PURE__*/function () {
    function ScrollSpy(element, config) {
      var _this = this;

      this._element = element;
      this._scrollElement = element.tagName === 'BODY' ? window : element;
      this._config = this._getConfig(config);
      this._selector = this._config.target + " " + SELECTOR_NAV_LINKS + "," + (this._config.target + " " + SELECTOR_LIST_ITEMS + ",") + (this._config.target + " " + SELECTOR_DROPDOWN_ITEMS);
      this._offsets = [];
      this._targets = [];
      this._activeTarget = null;
      this._scrollHeight = 0;
      $(this._scrollElement).on(EVENT_SCROLL, function (event) {
        return _this._process(event);
      });
      this.refresh();

      this._process();
    } // Getters


    var _proto = ScrollSpy.prototype; // Public

    _proto.refresh = function refresh() {
      var _this2 = this;

      var autoMethod = this._scrollElement === this._scrollElement.window ? METHOD_OFFSET : METHOD_POSITION;
      var offsetMethod = this._config.method === 'auto' ? autoMethod : this._config.method;
      var offsetBase = offsetMethod === METHOD_POSITION ? this._getScrollTop() : 0;
      this._offsets = [];
      this._targets = [];
      this._scrollHeight = this._getScrollHeight();
      var targets = [].slice.call(document.querySelectorAll(this._selector));
      targets.map(function (element) {
        var target;
        var targetSelector = Util.getSelectorFromElement(element);

        if (targetSelector) {
          target = document.querySelector(targetSelector);
        }

        if (target) {
          var targetBCR = target.getBoundingClientRect();

          if (targetBCR.width || targetBCR.height) {
            // TODO (fat): remove sketch reliance on jQuery position/offset
            return [$(target)[offsetMethod]().top + offsetBase, targetSelector];
          }
        }

        return null;
      }).filter(function (item) {
        return item;
      }).sort(function (a, b) {
        return a[0] - b[0];
      }).forEach(function (item) {
        _this2._offsets.push(item[0]);

        _this2._targets.push(item[1]);
      });
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$8);
      $(this._scrollElement).off(EVENT_KEY$8);
      this._element = null;
      this._scrollElement = null;
      this._config = null;
      this._selector = null;
      this._offsets = null;
      this._targets = null;
      this._activeTarget = null;
      this._scrollHeight = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _extends({}, Default$6, _typeof(config) === 'object' && config ? config : {});

      if (typeof config.target !== 'string' && Util.isElement(config.target)) {
        var id = $(config.target).attr('id');

        if (!id) {
          id = Util.getUID(NAME$8);
          $(config.target).attr('id', id);
        }

        config.target = "#" + id;
      }

      Util.typeCheckConfig(NAME$8, config, DefaultType$6);
      return config;
    };

    _proto._getScrollTop = function _getScrollTop() {
      return this._scrollElement === window ? this._scrollElement.pageYOffset : this._scrollElement.scrollTop;
    };

    _proto._getScrollHeight = function _getScrollHeight() {
      return this._scrollElement.scrollHeight || Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    };

    _proto._getOffsetHeight = function _getOffsetHeight() {
      return this._scrollElement === window ? window.innerHeight : this._scrollElement.getBoundingClientRect().height;
    };

    _proto._process = function _process() {
      var scrollTop = this._getScrollTop() + this._config.offset;

      var scrollHeight = this._getScrollHeight();

      var maxScroll = this._config.offset + scrollHeight - this._getOffsetHeight();

      if (this._scrollHeight !== scrollHeight) {
        this.refresh();
      }

      if (scrollTop >= maxScroll) {
        var target = this._targets[this._targets.length - 1];

        if (this._activeTarget !== target) {
          this._activate(target);
        }

        return;
      }

      if (this._activeTarget && scrollTop < this._offsets[0] && this._offsets[0] > 0) {
        this._activeTarget = null;

        this._clear();

        return;
      }

      for (var i = this._offsets.length; i--;) {
        var isActiveTarget = this._activeTarget !== this._targets[i] && scrollTop >= this._offsets[i] && (typeof this._offsets[i + 1] === 'undefined' || scrollTop < this._offsets[i + 1]);

        if (isActiveTarget) {
          this._activate(this._targets[i]);
        }
      }
    };

    _proto._activate = function _activate(target) {
      this._activeTarget = target;

      this._clear();

      var queries = this._selector.split(',').map(function (selector) {
        return selector + "[data-target=\"" + target + "\"]," + selector + "[href=\"" + target + "\"]";
      });

      var $link = $([].slice.call(document.querySelectorAll(queries.join(','))));

      if ($link.hasClass(CLASS_NAME_DROPDOWN_ITEM)) {
        $link.closest(SELECTOR_DROPDOWN).find(SELECTOR_DROPDOWN_TOGGLE).addClass(CLASS_NAME_ACTIVE$2);
        $link.addClass(CLASS_NAME_ACTIVE$2);
      } else {
        // Set triggered link as active
        $link.addClass(CLASS_NAME_ACTIVE$2); // Set triggered links parents as active
        // With both <ul> and <nav> markup a parent is the previous sibling of any nav ancestor

        $link.parents(SELECTOR_NAV_LIST_GROUP).prev(SELECTOR_NAV_LINKS + ", " + SELECTOR_LIST_ITEMS).addClass(CLASS_NAME_ACTIVE$2); // Handle special case when .nav-link is inside .nav-item

        $link.parents(SELECTOR_NAV_LIST_GROUP).prev(SELECTOR_NAV_ITEMS).children(SELECTOR_NAV_LINKS).addClass(CLASS_NAME_ACTIVE$2);
      }

      $(this._scrollElement).trigger(EVENT_ACTIVATE, {
        relatedTarget: target
      });
    };

    _proto._clear = function _clear() {
      [].slice.call(document.querySelectorAll(this._selector)).filter(function (node) {
        return node.classList.contains(CLASS_NAME_ACTIVE$2);
      }).forEach(function (node) {
        return node.classList.remove(CLASS_NAME_ACTIVE$2);
      });
    } // Static
    ;

    ScrollSpy._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$8);

        var _config = _typeof(config) === 'object' && config;

        if (!data) {
          data = new ScrollSpy(this, _config);
          $(this).data(DATA_KEY$8, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(ScrollSpy, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$8;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$6;
      }
    }]);

    return ScrollSpy;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(window).on(EVENT_LOAD_DATA_API$2, function () {
    var scrollSpys = [].slice.call(document.querySelectorAll(SELECTOR_DATA_SPY));
    var scrollSpysLength = scrollSpys.length;

    for (var i = scrollSpysLength; i--;) {
      var $spy = $(scrollSpys[i]);

      ScrollSpy._jQueryInterface.call($spy, $spy.data());
    }
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$8] = ScrollSpy._jQueryInterface;
  $.fn[NAME$8].Constructor = ScrollSpy;

  $.fn[NAME$8].noConflict = function () {
    $.fn[NAME$8] = JQUERY_NO_CONFLICT$8;
    return ScrollSpy._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$9 = 'tab';
  var VERSION$9 = '4.5.2';
  var DATA_KEY$9 = 'bs.tab';
  var EVENT_KEY$9 = "." + DATA_KEY$9;
  var DATA_API_KEY$7 = '.data-api';
  var JQUERY_NO_CONFLICT$9 = $.fn[NAME$9];
  var EVENT_HIDE$3 = "hide" + EVENT_KEY$9;
  var EVENT_HIDDEN$3 = "hidden" + EVENT_KEY$9;
  var EVENT_SHOW$3 = "show" + EVENT_KEY$9;
  var EVENT_SHOWN$3 = "shown" + EVENT_KEY$9;
  var EVENT_CLICK_DATA_API$6 = "click" + EVENT_KEY$9 + DATA_API_KEY$7;
  var CLASS_NAME_DROPDOWN_MENU = 'dropdown-menu';
  var CLASS_NAME_ACTIVE$3 = 'active';
  var CLASS_NAME_DISABLED$1 = 'disabled';
  var CLASS_NAME_FADE$4 = 'fade';
  var CLASS_NAME_SHOW$6 = 'show';
  var SELECTOR_DROPDOWN$1 = '.dropdown';
  var SELECTOR_NAV_LIST_GROUP$1 = '.nav, .list-group';
  var SELECTOR_ACTIVE$2 = '.active';
  var SELECTOR_ACTIVE_UL = '> li > .active';
  var SELECTOR_DATA_TOGGLE$4 = '[data-toggle="tab"], [data-toggle="pill"], [data-toggle="list"]';
  var SELECTOR_DROPDOWN_TOGGLE$1 = '.dropdown-toggle';
  var SELECTOR_DROPDOWN_ACTIVE_CHILD = '> .dropdown-menu .active';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Tab = /*#__PURE__*/function () {
    function Tab(element) {
      this._element = element;
    } // Getters


    var _proto = Tab.prototype; // Public

    _proto.show = function show() {
      var _this = this;

      if (this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && $(this._element).hasClass(CLASS_NAME_ACTIVE$3) || $(this._element).hasClass(CLASS_NAME_DISABLED$1)) {
        return;
      }

      var target;
      var previous;
      var listElement = $(this._element).closest(SELECTOR_NAV_LIST_GROUP$1)[0];
      var selector = Util.getSelectorFromElement(this._element);

      if (listElement) {
        var itemSelector = listElement.nodeName === 'UL' || listElement.nodeName === 'OL' ? SELECTOR_ACTIVE_UL : SELECTOR_ACTIVE$2;
        previous = $.makeArray($(listElement).find(itemSelector));
        previous = previous[previous.length - 1];
      }

      var hideEvent = $.Event(EVENT_HIDE$3, {
        relatedTarget: this._element
      });
      var showEvent = $.Event(EVENT_SHOW$3, {
        relatedTarget: previous
      });

      if (previous) {
        $(previous).trigger(hideEvent);
      }

      $(this._element).trigger(showEvent);

      if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) {
        return;
      }

      if (selector) {
        target = document.querySelector(selector);
      }

      this._activate(this._element, listElement);

      var complete = function complete() {
        var hiddenEvent = $.Event(EVENT_HIDDEN$3, {
          relatedTarget: _this._element
        });
        var shownEvent = $.Event(EVENT_SHOWN$3, {
          relatedTarget: previous
        });
        $(previous).trigger(hiddenEvent);
        $(_this._element).trigger(shownEvent);
      };

      if (target) {
        this._activate(target, target.parentNode, complete);
      } else {
        complete();
      }
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$9);
      this._element = null;
    } // Private
    ;

    _proto._activate = function _activate(element, container, callback) {
      var _this2 = this;

      var activeElements = container && (container.nodeName === 'UL' || container.nodeName === 'OL') ? $(container).find(SELECTOR_ACTIVE_UL) : $(container).children(SELECTOR_ACTIVE$2);
      var active = activeElements[0];
      var isTransitioning = callback && active && $(active).hasClass(CLASS_NAME_FADE$4);

      var complete = function complete() {
        return _this2._transitionComplete(element, active, callback);
      };

      if (active && isTransitioning) {
        var transitionDuration = Util.getTransitionDurationFromElement(active);
        $(active).removeClass(CLASS_NAME_SHOW$6).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }
    };

    _proto._transitionComplete = function _transitionComplete(element, active, callback) {
      if (active) {
        $(active).removeClass(CLASS_NAME_ACTIVE$3);
        var dropdownChild = $(active.parentNode).find(SELECTOR_DROPDOWN_ACTIVE_CHILD)[0];

        if (dropdownChild) {
          $(dropdownChild).removeClass(CLASS_NAME_ACTIVE$3);
        }

        if (active.getAttribute('role') === 'tab') {
          active.setAttribute('aria-selected', false);
        }
      }

      $(element).addClass(CLASS_NAME_ACTIVE$3);

      if (element.getAttribute('role') === 'tab') {
        element.setAttribute('aria-selected', true);
      }

      Util.reflow(element);

      if (element.classList.contains(CLASS_NAME_FADE$4)) {
        element.classList.add(CLASS_NAME_SHOW$6);
      }

      if (element.parentNode && $(element.parentNode).hasClass(CLASS_NAME_DROPDOWN_MENU)) {
        var dropdownElement = $(element).closest(SELECTOR_DROPDOWN$1)[0];

        if (dropdownElement) {
          var dropdownToggleList = [].slice.call(dropdownElement.querySelectorAll(SELECTOR_DROPDOWN_TOGGLE$1));
          $(dropdownToggleList).addClass(CLASS_NAME_ACTIVE$3);
        }

        element.setAttribute('aria-expanded', true);
      }

      if (callback) {
        callback();
      }
    } // Static
    ;

    Tab._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $this = $(this);
        var data = $this.data(DATA_KEY$9);

        if (!data) {
          data = new Tab(this);
          $this.data(DATA_KEY$9, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Tab, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$9;
      }
    }]);

    return Tab;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$6, SELECTOR_DATA_TOGGLE$4, function (event) {
    event.preventDefault();

    Tab._jQueryInterface.call($(this), 'show');
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$9] = Tab._jQueryInterface;
  $.fn[NAME$9].Constructor = Tab;

  $.fn[NAME$9].noConflict = function () {
    $.fn[NAME$9] = JQUERY_NO_CONFLICT$9;
    return Tab._jQueryInterface;
  };
  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */


  var NAME$a = 'toast';
  var VERSION$a = '4.5.2';
  var DATA_KEY$a = 'bs.toast';
  var EVENT_KEY$a = "." + DATA_KEY$a;
  var JQUERY_NO_CONFLICT$a = $.fn[NAME$a];
  var EVENT_CLICK_DISMISS$1 = "click.dismiss" + EVENT_KEY$a;
  var EVENT_HIDE$4 = "hide" + EVENT_KEY$a;
  var EVENT_HIDDEN$4 = "hidden" + EVENT_KEY$a;
  var EVENT_SHOW$4 = "show" + EVENT_KEY$a;
  var EVENT_SHOWN$4 = "shown" + EVENT_KEY$a;
  var CLASS_NAME_FADE$5 = 'fade';
  var CLASS_NAME_HIDE = 'hide';
  var CLASS_NAME_SHOW$7 = 'show';
  var CLASS_NAME_SHOWING = 'showing';
  var DefaultType$7 = {
    animation: 'boolean',
    autohide: 'boolean',
    delay: 'number'
  };
  var Default$7 = {
    animation: true,
    autohide: true,
    delay: 500
  };
  var SELECTOR_DATA_DISMISS$1 = '[data-dismiss="toast"]';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Toast = /*#__PURE__*/function () {
    function Toast(element, config) {
      this._element = element;
      this._config = this._getConfig(config);
      this._timeout = null;

      this._setListeners();
    } // Getters


    var _proto = Toast.prototype; // Public

    _proto.show = function show() {
      var _this = this;

      var showEvent = $.Event(EVENT_SHOW$4);
      $(this._element).trigger(showEvent);

      if (showEvent.isDefaultPrevented()) {
        return;
      }

      this._clearTimeout();

      if (this._config.animation) {
        this._element.classList.add(CLASS_NAME_FADE$5);
      }

      var complete = function complete() {
        _this._element.classList.remove(CLASS_NAME_SHOWING);

        _this._element.classList.add(CLASS_NAME_SHOW$7);

        $(_this._element).trigger(EVENT_SHOWN$4);

        if (_this._config.autohide) {
          _this._timeout = setTimeout(function () {
            _this.hide();
          }, _this._config.delay);
        }
      };

      this._element.classList.remove(CLASS_NAME_HIDE);

      Util.reflow(this._element);

      this._element.classList.add(CLASS_NAME_SHOWING);

      if (this._config.animation) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }
    };

    _proto.hide = function hide() {
      if (!this._element.classList.contains(CLASS_NAME_SHOW$7)) {
        return;
      }

      var hideEvent = $.Event(EVENT_HIDE$4);
      $(this._element).trigger(hideEvent);

      if (hideEvent.isDefaultPrevented()) {
        return;
      }

      this._close();
    };

    _proto.dispose = function dispose() {
      this._clearTimeout();

      if (this._element.classList.contains(CLASS_NAME_SHOW$7)) {
        this._element.classList.remove(CLASS_NAME_SHOW$7);
      }

      $(this._element).off(EVENT_CLICK_DISMISS$1);
      $.removeData(this._element, DATA_KEY$a);
      this._element = null;
      this._config = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _extends({}, Default$7, $(this._element).data(), _typeof(config) === 'object' && config ? config : {});
      Util.typeCheckConfig(NAME$a, config, this.constructor.DefaultType);
      return config;
    };

    _proto._setListeners = function _setListeners() {
      var _this2 = this;

      $(this._element).on(EVENT_CLICK_DISMISS$1, SELECTOR_DATA_DISMISS$1, function () {
        return _this2.hide();
      });
    };

    _proto._close = function _close() {
      var _this3 = this;

      var complete = function complete() {
        _this3._element.classList.add(CLASS_NAME_HIDE);

        $(_this3._element).trigger(EVENT_HIDDEN$4);
      };

      this._element.classList.remove(CLASS_NAME_SHOW$7);

      if (this._config.animation) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }
    };

    _proto._clearTimeout = function _clearTimeout() {
      clearTimeout(this._timeout);
      this._timeout = null;
    } // Static
    ;

    Toast._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $element = $(this);
        var data = $element.data(DATA_KEY$a);

        var _config = _typeof(config) === 'object' && config;

        if (!data) {
          data = new Toast(this, _config);
          $element.data(DATA_KEY$a, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config](this);
        }
      });
    };

    _createClass(Toast, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$a;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$7;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$7;
      }
    }]);

    return Toast;
  }();
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */


  $.fn[NAME$a] = Toast._jQueryInterface;
  $.fn[NAME$a].Constructor = Toast;

  $.fn[NAME$a].noConflict = function () {
    $.fn[NAME$a] = JQUERY_NO_CONFLICT$a;
    return Toast._jQueryInterface;
  };

  exports.Alert = Alert;
  exports.Button = Button;
  exports.Carousel = Carousel;
  exports.Collapse = Collapse;
  exports.Dropdown = Dropdown;
  exports.Modal = Modal;
  exports.Popover = Popover;
  exports.Scrollspy = ScrollSpy;
  exports.Tab = Tab;
  exports.Toast = Toast;
  exports.Tooltip = Tooltip;
  exports.Util = Util;
  Object.defineProperty(exports, '__esModule', {
    value: true
  });
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

/***/ "./node_modules/dropzone/dist/dropzone.js":
/*!************************************************!*\
  !*** ./node_modules/dropzone/dist/dropzone.js ***!
  \************************************************/
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

        var _iterator = _createForOfIteratorHelper(callbacks),
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

  var _super = _createSuper(Dropzone);

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

          var _iterator2 = _createForOfIteratorHelper(this.element.getElementsByTagName("div")),
              _step2;

          try {
            for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
              var child = _step2.value;

              if (/(^| )dz-message($| )/.test(child.className)) {
                messageElement = child;
                child.className = "dz-message"; // Removes the 'dz-default' class

                break;
              }
            }
          } catch (err) {
            _iterator2.e(err);
          } finally {
            _iterator2.f();
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

            var _iterator3 = _createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-name]")),
                _step3;

            try {
              for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
                var node = _step3.value;
                node.textContent = file.name;
              }
            } catch (err) {
              _iterator3.e(err);
            } finally {
              _iterator3.f();
            }

            var _iterator4 = _createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-size]")),
                _step4;

            try {
              for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
                node = _step4.value;
                node.innerHTML = this.filesize(file.size);
              }
            } catch (err) {
              _iterator4.e(err);
            } finally {
              _iterator4.f();
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

            var _iterator5 = _createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-remove]")),
                _step5;

            try {
              for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
                var removeLink = _step5.value;
                removeLink.addEventListener("click", removeFileEvent);
              }
            } catch (err) {
              _iterator5.e(err);
            } finally {
              _iterator5.f();
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

            var _iterator6 = _createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-thumbnail]")),
                _step6;

            try {
              for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
                var thumbnailElement = _step6.value;
                thumbnailElement.alt = file.name;
                thumbnailElement.src = dataUrl;
              }
            } catch (err) {
              _iterator6.e(err);
            } finally {
              _iterator6.f();
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

            var _iterator7 = _createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-errormessage]")),
                _step7;

            try {
              for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
                var node = _step7.value;
                node.textContent = message;
              }
            } catch (err) {
              _iterator7.e(err);
            } finally {
              _iterator7.f();
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
            var _iterator8 = _createForOfIteratorHelper(file.previewElement.querySelectorAll("[data-dz-uploadprogress]")),
                _step8;

            try {
              for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
                var node = _step8.value;
                node.nodeName === 'PROGRESS' ? node.value = progress : node.style.width = "".concat(progress, "%");
              }
            } catch (err) {
              _iterator8.e(err);
            } finally {
              _iterator8.f();
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

    _this = _super.call(this);
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

    if (typeof _this.options.method === 'string') {
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
              var _iterator9 = _createForOfIteratorHelper(files),
                  _step9;

              try {
                for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
                  var file = _step9.value;

                  _this3.addFile(file);
                }
              } catch (err) {
                _iterator9.e(err);
              } finally {
                _iterator9.f();
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

      var _iterator10 = _createForOfIteratorHelper(this.events),
          _step10;

      try {
        for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
          var eventName = _step10.value;
          this.on(eventName, this.options[eventName]);
        }
      } catch (err) {
        _iterator10.e(err);
      } finally {
        _iterator10.f();
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
        var _iterator11 = _createForOfIteratorHelper(this.getActiveFiles()),
            _step11;

        try {
          for (_iterator11.s(); !(_step11 = _iterator11.n()).done;) {
            var file = _step11.value;
            totalBytesSent += file.upload.bytesSent;
            totalBytes += file.upload.total;
          }
        } catch (err) {
          _iterator11.e(err);
        } finally {
          _iterator11.f();
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
        var _iterator12 = _createForOfIteratorHelper(elements),
            _step12;

        try {
          for (_iterator12.s(); !(_step12 = _iterator12.n()).done;) {
            var el = _step12.value;

            if (/(^| )fallback($| )/.test(el.className)) {
              return el;
            }
          }
        } catch (err) {
          _iterator12.e(err);
        } finally {
          _iterator12.f();
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
      var _iterator13 = _createForOfIteratorHelper(files),
          _step13;

      try {
        for (_iterator13.s(); !(_step13 = _iterator13.n()).done;) {
          var file = _step13.value;
          this.addFile(file);
        }
      } catch (err) {
        _iterator13.e(err);
      } finally {
        _iterator13.f();
      }
    } // When a folder is dropped (or files are pasted), items must be handled
    // instead of files.

  }, {
    key: "_addFilesFromItems",
    value: function _addFilesFromItems(items) {
      var _this5 = this;

      return function () {
        var result = [];

        var _iterator14 = _createForOfIteratorHelper(items),
            _step14;

        try {
          for (_iterator14.s(); !(_step14 = _iterator14.n()).done;) {
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
          _iterator14.e(err);
        } finally {
          _iterator14.f();
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
            var _iterator15 = _createForOfIteratorHelper(entries),
                _step15;

            try {
              for (_iterator15.s(); !(_step15 = _iterator15.n()).done;) {
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
              _iterator15.e(err);
            } finally {
              _iterator15.f();
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
      var _iterator16 = _createForOfIteratorHelper(files),
          _step16;

      try {
        for (_iterator16.s(); !(_step16 = _iterator16.n()).done;) {
          var file = _step16.value;
          this.enqueueFile(file);
        }
      } catch (err) {
        _iterator16.e(err);
      } finally {
        _iterator16.f();
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

      var _iterator17 = _createForOfIteratorHelper(this.files.slice()),
          _step17;

      try {
        for (_iterator17.s(); !(_step17 = _iterator17.n()).done;) {
          var file = _step17.value;

          if (file.status !== Dropzone.UPLOADING || cancelIfNecessary) {
            this.removeFile(file);
          }
        }
      } catch (err) {
        _iterator17.e(err);
      } finally {
        _iterator17.f();
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
      } // fixOrientation is not needed anymore with browsers handling imageOrientation


      fixOrientation = getComputedStyle(document.body)['imageOrientation'] == 'from-image' ? false : fixOrientation;

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
      var _iterator18 = _createForOfIteratorHelper(files),
          _step18;

      try {
        for (_iterator18.s(); !(_step18 = _iterator18.n()).done;) {
          var file = _step18.value;
          file.processing = true; // Backwards compatibility

          file.status = Dropzone.UPLOADING;
          this.emit("processing", file);
        }
      } catch (err) {
        _iterator18.e(err);
      } finally {
        _iterator18.f();
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

        var _iterator19 = _createForOfIteratorHelper(groupedFiles),
            _step19;

        try {
          for (_iterator19.s(); !(_step19 = _iterator19.n()).done;) {
            var groupedFile = _step19.value;
            groupedFile.status = Dropzone.CANCELED;
          }
        } catch (err) {
          _iterator19.e(err);
        } finally {
          _iterator19.f();
        }

        if (typeof file.xhr !== 'undefined') {
          file.xhr.abort();
        }

        var _iterator20 = _createForOfIteratorHelper(groupedFiles),
            _step20;

        try {
          for (_iterator20.s(); !(_step20 = _iterator20.n()).done;) {
            var _groupedFile = _step20.value;
            this.emit("canceled", _groupedFile);
          }
        } catch (err) {
          _iterator20.e(err);
        } finally {
          _iterator20.f();
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
            var end = Math.min(start + _this15.options.chunkSize, _transformedFile.size);
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

      var _iterator21 = _createForOfIteratorHelper(files),
          _step21;

      try {
        for (_iterator21.s(); !(_step21 = _iterator21.n()).done;) {
          var file = _step21.value;
          file.xhr = xhr;
        }
      } catch (err) {
        _iterator21.e(err);
      } finally {
        _iterator21.f();
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
        _this16._handleUploadError(files, xhr, "Request timedout after ".concat(_this16.options.timeout / 1000, " seconds"));
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


      var _iterator22 = _createForOfIteratorHelper(files),
          _step22;

      try {
        for (_iterator22.s(); !(_step22 = _iterator22.n()).done;) {
          var _file = _step22.value;
          this.emit("sending", _file, xhr, formData);
        }
      } catch (err) {
        _iterator22.e(err);
      } finally {
        _iterator22.f();
      }

      if (this.options.uploadMultiple) {
        this.emit("sendingmultiple", files, xhr, formData);
      }

      this._addFormElementData(formData); // Finally add the files
      // Has to be last because some servers (eg: S3) expect the file to be the last parameter


      for (var _i4 = 0; _i4 < dataBlocks.length; _i4++) {
        var dataBlock = dataBlocks[_i4];
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
        var _iterator23 = _createForOfIteratorHelper(this.element.querySelectorAll("input, textarea, select, button")),
            _step23;

        try {
          for (_iterator23.s(); !(_step23 = _iterator23.n()).done;) {
            var input = _step23.value;
            var inputName = input.getAttribute("name");
            var inputType = input.getAttribute("type");
            if (inputType) inputType = inputType.toLowerCase(); // If the input doesn't have a name, we can't use it.

            if (typeof inputName === 'undefined' || inputName === null) continue;

            if (input.tagName === "SELECT" && input.hasAttribute("multiple")) {
              // Possibly multiple values
              var _iterator24 = _createForOfIteratorHelper(input.options),
                  _step24;

              try {
                for (_iterator24.s(); !(_step24 = _iterator24.n()).done;) {
                  var option = _step24.value;

                  if (option.selected) {
                    formData.append(inputName, option.value);
                  }
                }
              } catch (err) {
                _iterator24.e(err);
              } finally {
                _iterator24.f();
              }
            } else if (!inputType || inputType !== "checkbox" && inputType !== "radio" || input.checked) {
              formData.append(inputName, input.value);
            }
          }
        } catch (err) {
          _iterator23.e(err);
        } finally {
          _iterator23.f();
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
          var _iterator25 = _createForOfIteratorHelper(files),
              _step25;

          try {
            for (_iterator25.s(); !(_step25 = _iterator25.n()).done;) {
              var _file2 = _step25.value;
              _file2.upload.progress = progress;
              _file2.upload.total = e.total;
              _file2.upload.bytesSent = e.loaded;
            }
          } catch (err) {
            _iterator25.e(err);
          } finally {
            _iterator25.f();
          }
        }

        var _iterator26 = _createForOfIteratorHelper(files),
            _step26;

        try {
          for (_iterator26.s(); !(_step26 = _iterator26.n()).done;) {
            var _file3 = _step26.value;
            this.emit("uploadprogress", _file3, _file3.upload.progress, _file3.upload.bytesSent);
          }
        } catch (err) {
          _iterator26.e(err);
        } finally {
          _iterator26.f();
        }
      } else {
        // Called when the file finished uploading
        var allFilesFinished = true;
        progress = 100;

        var _iterator27 = _createForOfIteratorHelper(files),
            _step27;

        try {
          for (_iterator27.s(); !(_step27 = _iterator27.n()).done;) {
            var _file4 = _step27.value;

            if (_file4.upload.progress !== 100 || _file4.upload.bytesSent !== _file4.upload.total) {
              allFilesFinished = false;
            }

            _file4.upload.progress = progress;
            _file4.upload.bytesSent = _file4.upload.total;
          } // Nothing to do, all files already at 100%

        } catch (err) {
          _iterator27.e(err);
        } finally {
          _iterator27.f();
        }

        if (allFilesFinished) {
          return;
        }

        var _iterator28 = _createForOfIteratorHelper(files),
            _step28;

        try {
          for (_iterator28.s(); !(_step28 = _iterator28.n()).done;) {
            var _file5 = _step28.value;
            this.emit("uploadprogress", _file5, progress, _file5.upload.bytesSent);
          }
        } catch (err) {
          _iterator28.e(err);
        } finally {
          _iterator28.f();
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
      var _iterator29 = _createForOfIteratorHelper(files),
          _step29;

      try {
        for (_iterator29.s(); !(_step29 = _iterator29.n()).done;) {
          var file = _step29.value;
          file.status = Dropzone.SUCCESS;
          this.emit("success", file, responseText, e);
          this.emit("complete", file);
        }
      } catch (err) {
        _iterator29.e(err);
      } finally {
        _iterator29.f();
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
      var _iterator30 = _createForOfIteratorHelper(files),
          _step30;

      try {
        for (_iterator30.s(); !(_step30 = _iterator30.n()).done;) {
          var file = _step30.value;
          file.status = Dropzone.ERROR;
          this.emit("error", file, message, xhr);
          this.emit("complete", file);
        }
      } catch (err) {
        _iterator30.e(err);
      } finally {
        _iterator30.f();
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
Dropzone.version = "5.7.2"; // This is a map of options for your different dropzones. Add configurations
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

        var _iterator31 = _createForOfIteratorHelper(elements),
            _step31;

        try {
          for (_iterator31.s(); !(_step31 = _iterator31.n()).done;) {
            var el = _step31.value;

            if (/(^| )dropzone($| )/.test(el.className)) {
              result.push(dropzones.push(el));
            } else {
              result.push(undefined);
            }
          }
        } catch (err) {
          _iterator31.e(err);
        } finally {
          _iterator31.f();
        }

        return result;
      }();
    };

    checkElements(document.getElementsByTagName("div"));
    checkElements(document.getElementsByTagName("form"));
  }

  return function () {
    var result = [];

    var _iterator32 = _createForOfIteratorHelper(dropzones),
        _step32;

    try {
      for (_iterator32.s(); !(_step32 = _iterator32.n()).done;) {
        var dropzone = _step32.value; // Create a dropzone unless auto discover has been disabled for specific element

        if (Dropzone.optionsForElement(dropzone) !== false) {
          result.push(new Dropzone(dropzone));
        } else {
          result.push(undefined);
        }
      }
    } catch (err) {
      _iterator32.e(err);
    } finally {
      _iterator32.f();
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
      var _iterator33 = _createForOfIteratorHelper(Dropzone.blacklistedBrowsers),
          _step33;

      try {
        for (_iterator33.s(); !(_step33 = _iterator33.n()).done;) {
          var regex = _step33.value;

          if (regex.test(navigator.userAgent)) {
            capableBrowser = false;
            continue;
          }
        }
      } catch (err) {
        _iterator33.e(err);
      } finally {
        _iterator33.f();
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
      var _iterator34 = _createForOfIteratorHelper(els),
          _step34;

      try {
        for (_iterator34.s(); !(_step34 = _iterator34.n()).done;) {
          el = _step34.value;
          elements.push(this.getElement(el, name));
        }
      } catch (err) {
        _iterator34.e(err);
      } finally {
        _iterator34.f();
      }
    } catch (e) {
      elements = null;
    }
  } else if (typeof els === "string") {
    elements = [];

    var _iterator35 = _createForOfIteratorHelper(document.querySelectorAll(els)),
        _step35;

    try {
      for (_iterator35.s(); !(_step35 = _iterator35.n()).done;) {
        el = _step35.value;
        elements.push(el);
      }
    } catch (err) {
      _iterator35.e(err);
    } finally {
      _iterator35.f();
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

  var _iterator36 = _createForOfIteratorHelper(acceptedFiles),
      _step36;

  try {
    for (_iterator36.s(); !(_step36 = _iterator36.n()).done;) {
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
    _iterator36.e(err);
  } finally {
    _iterator36.f();
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
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/interactjs/dist/interact.min.js":
/*!******************************************************!*\
  !*** ./node_modules/interactjs/dist/interact.min.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/* interact.js 1.9.22 | https://raw.github.com/taye/interact.js/master/LICENSE */
!function (t) {
  if ("object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module) module.exports = t();else if (true) !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else {}
}(function () {
  var t = {};
  Object.defineProperty(t, "__esModule", {
    value: !0
  }), t["default"] = void 0;

  t["default"] = function (t) {
    return !(!t || !t.Window) && t instanceof t.Window;
  };

  var e = {};
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.init = o, e.getWindow = function (e) {
    if ((0, t["default"])(e)) return e;
    return (e.ownerDocument || e).defaultView || r.window;
  }, e.window = e.realWindow = void 0;
  var n = void 0;
  e.realWindow = n;
  var r = void 0;

  function o(t) {
    e.realWindow = n = t;
    var o = t.document.createTextNode("");
    o.ownerDocument !== t.document && "function" == typeof t.wrap && t.wrap(o) === o && (t = t.wrap(t)), e.window = r = t;
  }

  e.window = r, "undefined" != typeof window && window && o(window);
  var i = {};

  function a(t) {
    return (a = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(i, "__esModule", {
    value: !0
  }), i["default"] = void 0;

  var s = function s(t) {
    return !!t && "object" === a(t);
  },
      l = function l(t) {
    return "function" == typeof t;
  },
      u = {
    window: function window(n) {
      return n === e.window || (0, t["default"])(n);
    },
    docFrag: function docFrag(t) {
      return s(t) && 11 === t.nodeType;
    },
    object: s,
    func: l,
    number: function number(t) {
      return "number" == typeof t;
    },
    bool: function bool(t) {
      return "boolean" == typeof t;
    },
    string: function string(t) {
      return "string" == typeof t;
    },
    element: function element(t) {
      if (!t || "object" !== a(t)) return !1;
      var n = e.getWindow(t) || e.window;
      return /object|function/.test(a(n.Element)) ? t instanceof n.Element : 1 === t.nodeType && "string" == typeof t.nodeName;
    },
    plainObject: function plainObject(t) {
      return s(t) && !!t.constructor && /function Object\b/.test(t.constructor.toString());
    },
    array: function array(t) {
      return s(t) && void 0 !== t.length && l(t.splice);
    }
  };

  i["default"] = u;
  var c = {};

  function f(t) {
    var e = t.interaction;

    if ("drag" === e.prepared.name) {
      var n = e.prepared.axis;
      "x" === n ? (e.coords.cur.page.y = e.coords.start.page.y, e.coords.cur.client.y = e.coords.start.client.y, e.coords.velocity.client.y = 0, e.coords.velocity.page.y = 0) : "y" === n && (e.coords.cur.page.x = e.coords.start.page.x, e.coords.cur.client.x = e.coords.start.client.x, e.coords.velocity.client.x = 0, e.coords.velocity.page.x = 0);
    }
  }

  function d(t) {
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

  Object.defineProperty(c, "__esModule", {
    value: !0
  }), c["default"] = void 0;
  var p = {
    id: "actions/drag",
    install: function install(t) {
      var e = t.actions,
          n = t.Interactable,
          r = t.defaults;
      n.prototype.draggable = p.draggable, e.map.drag = p, e.methodDict.drag = "draggable", r.actions.drag = p.defaults;
    },
    listeners: {
      "interactions:before-action-move": f,
      "interactions:action-resume": f,
      "interactions:action-move": d,
      "auto-start:check": function autoStartCheck(t) {
        var e = t.interaction,
            n = t.interactable,
            r = t.buttons,
            o = n.options.drag;
        if (o && o.enabled && (!e.pointerIsDown || !/mouse|pointer/.test(e.pointerType) || 0 != (r & n.options.drag.mouseButtons))) return t.action = {
          name: "drag",
          axis: "start" === o.lockAxis ? o.startAxis : o.lockAxis
        }, !1;
      }
    },
    draggable: function draggable(t) {
      return i["default"].object(t) ? (this.options.drag.enabled = !1 !== t.enabled, this.setPerAction("drag", t), this.setOnEvents("drag", t), /^(xy|x|y|start)$/.test(t.lockAxis) && (this.options.drag.lockAxis = t.lockAxis), /^(xy|x|y)$/.test(t.startAxis) && (this.options.drag.startAxis = t.startAxis), this) : i["default"].bool(t) ? (this.options.drag.enabled = t, this) : this.options.drag;
    },
    beforeMove: f,
    move: d,
    defaults: {
      startAxis: "xy",
      lockAxis: "xy"
    },
    getCursor: function getCursor() {
      return "move";
    }
  },
      v = p;
  c["default"] = v;
  var h = {};
  Object.defineProperty(h, "__esModule", {
    value: !0
  }), h["default"] = void 0;
  var g = {
    init: function init(t) {
      var e = t;
      g.document = e.document, g.DocumentFragment = e.DocumentFragment || y, g.SVGElement = e.SVGElement || y, g.SVGSVGElement = e.SVGSVGElement || y, g.SVGElementInstance = e.SVGElementInstance || y, g.Element = e.Element || y, g.HTMLElement = e.HTMLElement || g.Element, g.Event = e.Event, g.Touch = e.Touch || y, g.PointerEvent = e.PointerEvent || e.MSPointerEvent;
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

  function y() {}

  var m = g;
  h["default"] = m;
  var b = {};
  Object.defineProperty(b, "__esModule", {
    value: !0
  }), b["default"] = void 0;
  var x = {
    init: function init(t) {
      var n = h["default"].Element,
          r = e.window.navigator;
      x.supportsTouch = "ontouchstart" in t || i["default"].func(t.DocumentTouch) && h["default"].document instanceof t.DocumentTouch, x.supportsPointerEvent = !1 !== r.pointerEnabled && !!h["default"].PointerEvent, x.isIOS = /iP(hone|od|ad)/.test(r.platform), x.isIOS7 = /iP(hone|od|ad)/.test(r.platform) && /OS 7[^\d]/.test(r.appVersion), x.isIe9 = /MSIE 9/.test(r.userAgent), x.isOperaMobile = "Opera" === r.appName && x.supportsTouch && /Presto/.test(r.userAgent), x.prefixedMatchesSelector = "matches" in n.prototype ? "matches" : "webkitMatchesSelector" in n.prototype ? "webkitMatchesSelector" : "mozMatchesSelector" in n.prototype ? "mozMatchesSelector" : "oMatchesSelector" in n.prototype ? "oMatchesSelector" : "msMatchesSelector", x.pEventTypes = x.supportsPointerEvent ? h["default"].PointerEvent === t.MSPointerEvent ? {
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
      } : null, x.wheelEvent = "onmousewheel" in h["default"].document ? "mousewheel" : "wheel";
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
  var w = x;
  b["default"] = w;
  var _ = {};

  function S(t) {
    var e = t.parentNode;

    if (i["default"].docFrag(e)) {
      for (; (e = e.host) && i["default"].docFrag(e);) {
        ;
      }

      return e;
    }

    return e;
  }

  function P(t, n) {
    return e.window !== e.realWindow && (n = n.replace(/\/deep\//g, " ")), t[b["default"].prefixedMatchesSelector](n);
  }

  Object.defineProperty(_, "__esModule", {
    value: !0
  }), _.nodeContains = function (t, e) {
    if (t.contains) return t.contains(e);

    for (; e;) {
      if (e === t) return !0;
      e = e.parentNode;
    }

    return !1;
  }, _.closest = function (t, e) {
    for (; i["default"].element(t);) {
      if (P(t, e)) return t;
      t = S(t);
    }

    return null;
  }, _.parentNode = S, _.matchesSelector = P, _.indexOfDeepestElement = function (t) {
    for (var n, r = [], o = 0; o < t.length; o++) {
      var i = t[o],
          a = t[n];
      if (i && o !== n) if (a) {
        var s = O(i),
            l = O(a);
        if (s !== i.ownerDocument) if (l !== i.ownerDocument) {
          if (s !== l) {
            r = r.length ? r : E(a);
            var u = void 0;

            if (a instanceof h["default"].HTMLElement && i instanceof h["default"].SVGElement && !(i instanceof h["default"].SVGSVGElement)) {
              if (i === l) continue;
              u = i.ownerSVGElement;
            } else u = i;

            for (var c = E(u, a.ownerDocument), f = 0; c[f] && c[f] === r[f];) {
              f++;
            }

            for (var d = [c[f - 1], c[f], r[f]], p = d[0].lastChild; p;) {
              if (p === d[1]) {
                n = o, r = c;
                break;
              }

              if (p === d[2]) break;
              p = p.previousSibling;
            }
          } else v = i, g = a, y = void 0, m = void 0, y = parseInt(e.getWindow(v).getComputedStyle(v).zIndex, 10) || 0, m = parseInt(e.getWindow(g).getComputedStyle(g).zIndex, 10) || 0, y >= m && (n = o);
        } else n = o;
      } else n = o;
    }

    var v, g, y, m;
    return n;
  }, _.matchesUpTo = function (t, e, n) {
    for (; i["default"].element(t);) {
      if (P(t, e)) return !0;
      if ((t = S(t)) === n) return P(t, e);
    }

    return !1;
  }, _.getActualElement = function (t) {
    return t.correspondingUseElement || t;
  }, _.getScrollXY = T, _.getElementClientRect = M, _.getElementRect = function (t) {
    var n = M(t);

    if (!b["default"].isIOS7 && n) {
      var r = T(e.getWindow(t));
      n.left += r.x, n.right += r.x, n.top += r.y, n.bottom += r.y;
    }

    return n;
  }, _.getPath = function (t) {
    var e = [];

    for (; t;) {
      e.push(t), t = S(t);
    }

    return e;
  }, _.trySelector = function (t) {
    if (!i["default"].string(t)) return !1;
    return h["default"].document.querySelector(t), !0;
  };

  var O = function O(t) {
    return t.parentNode || t.host;
  };

  function E(t, e) {
    for (var n, r = [], o = t; (n = O(o)) && o !== e && n !== o.ownerDocument;) {
      r.unshift(o), o = n;
    }

    return r;
  }

  function T(t) {
    return {
      x: (t = t || e.window).scrollX || t.document.documentElement.scrollLeft,
      y: t.scrollY || t.document.documentElement.scrollTop
    };
  }

  function M(t) {
    var e = t instanceof h["default"].SVGElement ? t.getBoundingClientRect() : t.getClientRects()[0];
    return e && {
      left: e.left,
      right: e.right,
      top: e.top,
      bottom: e.bottom,
      width: e.width || e.right - e.left,
      height: e.height || e.bottom - e.top
    };
  }

  var j = {};
  Object.defineProperty(j, "__esModule", {
    value: !0
  }), j["default"] = function (t, e) {
    for (var n in e) {
      t[n] = e[n];
    }

    return t;
  };
  var k = {};

  function I(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function D(t, e, n) {
    return "parent" === t ? (0, _.parentNode)(n) : "self" === t ? e.getRect(n) : (0, _.closest)(n, t);
  }

  Object.defineProperty(k, "__esModule", {
    value: !0
  }), k.getStringOptionResult = D, k.resolveRectLike = function (t, e, n, r) {
    var o = t;
    i["default"].string(o) ? o = D(o, e, n) : i["default"].func(o) && (o = o.apply(void 0, function (t) {
      if (Array.isArray(t)) return I(t);
    }(a = r) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(a) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return I(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? I(t, e) : void 0;
      }
    }(a) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }()));
    var a;
    i["default"].element(o) && (o = (0, _.getElementRect)(o));
    return o;
  }, k.rectToXY = function (t) {
    return t && {
      x: "x" in t ? t.x : t.left,
      y: "y" in t ? t.y : t.top
    };
  }, k.xywhToTlbr = function (t) {
    !t || "left" in t && "top" in t || ((t = (0, j["default"])({}, t)).left = t.x || 0, t.top = t.y || 0, t.right = t.right || t.left + t.width, t.bottom = t.bottom || t.top + t.height);
    return t;
  }, k.tlbrToXywh = function (t) {
    !t || "x" in t && "y" in t || ((t = (0, j["default"])({}, t)).x = t.left || 0, t.y = t.top || 0, t.width = t.width || (t.right || 0) - t.x, t.height = t.height || (t.bottom || 0) - t.y);
    return t;
  }, k.addEdges = function (t, e, n) {
    t.left && (e.left += n.x);
    t.right && (e.right += n.x);
    t.top && (e.top += n.y);
    t.bottom && (e.bottom += n.y);
    e.width = e.right - e.left, e.height = e.bottom - e.top;
  };
  var A = {};
  Object.defineProperty(A, "__esModule", {
    value: !0
  }), A["default"] = function (t, e, n) {
    var r = t.options[n],
        o = r && r.origin || t.options.origin,
        i = (0, k.resolveRectLike)(o, t, e, [t && e]);
    return (0, k.rectToXY)(i) || {
      x: 0,
      y: 0
    };
  };
  var z = {};

  function C(t) {
    return t.trim().split(/ +/);
  }

  Object.defineProperty(z, "__esModule", {
    value: !0
  }), z["default"] = function t(e, n, r) {
    r = r || {}, i["default"].string(e) && -1 !== e.search(" ") && (e = C(e));
    if (i["default"].array(e)) return e.reduce(function (e, o) {
      return (0, j["default"])(e, t(o, n, r));
    }, r);
    i["default"].object(e) && (n = e, e = "");
    if (i["default"].func(n)) r[e] = r[e] || [], r[e].push(n);else if (i["default"].array(n)) for (var o = 0; o < n.length; o++) {
      var a;
      a = n[o], t(e, a, r);
    } else if (i["default"].object(n)) for (var s in n) {
      var l = C(s).map(function (t) {
        return "".concat(e).concat(t);
      });
      t(l, n[s], r);
    }
    return r;
  };
  var R = {};
  Object.defineProperty(R, "__esModule", {
    value: !0
  }), R["default"] = void 0;

  R["default"] = function (t, e) {
    return Math.sqrt(t * t + e * e);
  };

  var F = {};

  function X(t, e) {
    for (var n in e) {
      var r = X.prefixedPropREs,
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

  Object.defineProperty(F, "__esModule", {
    value: !0
  }), F["default"] = void 0, X.prefixedPropREs = {
    webkit: /(Movement[XY]|Radius[XY]|RotationAngle|Force)$/,
    moz: /(Pressure)$/
  };
  var Y = X;
  F["default"] = Y;
  var W = {};

  function L(t) {
    return t instanceof h["default"].Event || t instanceof h["default"].Touch;
  }

  function B(t, e, n) {
    return t = t || "page", (n = n || {}).x = e[t + "X"], n.y = e[t + "Y"], n;
  }

  function U(t, e) {
    return e = e || {
      x: 0,
      y: 0
    }, b["default"].isOperaMobile && L(t) ? (B("screen", t, e), e.x += window.scrollX, e.y += window.scrollY) : B("page", t, e), e;
  }

  function N(t, e) {
    return e = e || {}, b["default"].isOperaMobile && L(t) ? B("screen", t, e) : B("client", t, e), e;
  }

  function V(t) {
    var e = [];
    return i["default"].array(t) ? (e[0] = t[0], e[1] = t[1]) : "touchend" === t.type ? 1 === t.touches.length ? (e[0] = t.touches[0], e[1] = t.changedTouches[0]) : 0 === t.touches.length && (e[0] = t.changedTouches[0], e[1] = t.changedTouches[1]) : (e[0] = t.touches[0], e[1] = t.touches[1]), e;
  }

  function q(t) {
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

  Object.defineProperty(W, "__esModule", {
    value: !0
  }), W.copyCoords = function (t, e) {
    t.page = t.page || {}, t.page.x = e.page.x, t.page.y = e.page.y, t.client = t.client || {}, t.client.x = e.client.x, t.client.y = e.client.y, t.timeStamp = e.timeStamp;
  }, W.setCoordDeltas = function (t, e, n) {
    t.page.x = n.page.x - e.page.x, t.page.y = n.page.y - e.page.y, t.client.x = n.client.x - e.client.x, t.client.y = n.client.y - e.client.y, t.timeStamp = n.timeStamp - e.timeStamp;
  }, W.setCoordVelocity = function (t, e) {
    var n = Math.max(e.timeStamp / 1e3, .001);
    t.page.x = e.page.x / n, t.page.y = e.page.y / n, t.client.x = e.client.x / n, t.client.y = e.client.y / n, t.timeStamp = n;
  }, W.setZeroCoords = function (t) {
    t.page.x = 0, t.page.y = 0, t.client.x = 0, t.client.y = 0;
  }, W.isNativePointer = L, W.getXY = B, W.getPageXY = U, W.getClientXY = N, W.getPointerId = function (t) {
    return i["default"].number(t.pointerId) ? t.pointerId : t.identifier;
  }, W.setCoords = function (t, e, n) {
    var r = e.length > 1 ? q(e) : e[0];
    U(r, t.page), N(r, t.client), t.timeStamp = n;
  }, W.getTouchPair = V, W.pointerAverage = q, W.touchBBox = function (t) {
    if (!t.length) return null;
    var e = V(t),
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
  }, W.touchDistance = function (t, e) {
    var n = e + "X",
        r = e + "Y",
        o = V(t),
        i = o[0][n] - o[1][n],
        a = o[0][r] - o[1][r];
    return (0, R["default"])(i, a);
  }, W.touchAngle = function (t, e) {
    var n = e + "X",
        r = e + "Y",
        o = V(t),
        i = o[1][n] - o[0][n],
        a = o[1][r] - o[0][r];
    return 180 * Math.atan2(a, i) / Math.PI;
  }, W.getPointerType = function (t) {
    return i["default"].string(t.pointerType) ? t.pointerType : i["default"].number(t.pointerType) ? [void 0, void 0, "touch", "pen", "mouse"][t.pointerType] : /touch/.test(t.type) || t instanceof h["default"].Touch ? "touch" : "mouse";
  }, W.getEventTargets = function (t) {
    var e = i["default"].func(t.composedPath) ? t.composedPath() : t.path;
    return [_.getActualElement(e ? e[0] : t.target), _.getActualElement(t.currentTarget)];
  }, W.newCoords = function () {
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
  }, W.coordsToEvent = function (t) {
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
  }, Object.defineProperty(W, "pointerExtend", {
    enumerable: !0,
    get: function get() {
      return F["default"];
    }
  });
  var $ = {};

  function G(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  Object.defineProperty($, "__esModule", {
    value: !0
  }), $.BaseEvent = void 0;

  var H = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.type = void 0, this.target = void 0, this.currentTarget = void 0, this.interactable = void 0, this._interaction = void 0, this.timeStamp = void 0, this.immediatePropagationStopped = !1, this.propagationStopped = !1, this._interaction = e;
    }

    var e, n, r;
    return e = t, (n = [{
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
    }]) && G(e.prototype, n), r && G(e, r), t;
  }();

  $.BaseEvent = H, Object.defineProperty(H.prototype, "interaction", {
    get: function get() {
      return this._interaction._proxy;
    },
    set: function set() {}
  });
  var K = {};
  Object.defineProperty(K, "__esModule", {
    value: !0
  }), K.find = K.findIndex = K.from = K.merge = K.remove = K.contains = void 0;

  K.contains = function (t, e) {
    return -1 !== t.indexOf(e);
  };

  K.remove = function (t, e) {
    return t.splice(t.indexOf(e), 1);
  };

  var Z = function Z(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      t.push(r);
    }

    return t;
  };

  K.merge = Z;

  K.from = function (t) {
    return Z([], t);
  };

  var J = function J(t, e) {
    for (var n = 0; n < t.length; n++) {
      if (e(t[n], n, t)) return n;
    }

    return -1;
  };

  K.findIndex = J;

  K.find = function (t, e) {
    return t[J(t, e)];
  };

  var Q = {};

  function tt(t) {
    return (tt = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function et(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function nt(t, e) {
    return (nt = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function rt(t) {
    var e = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }();

    return function () {
      var n,
          r = it(t);

      if (e) {
        var o = it(this).constructor;
        n = Reflect.construct(r, arguments, o);
      } else n = r.apply(this, arguments);

      return ot(this, n);
    };
  }

  function ot(t, e) {
    return !e || "object" !== tt(e) && "function" != typeof e ? function (t) {
      if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      return t;
    }(t) : e;
  }

  function it(t) {
    return (it = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  Object.defineProperty(Q, "__esModule", {
    value: !0
  }), Q.DropEvent = void 0;

  var at = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && nt(t, e);
    }(i, t);
    var e,
        n,
        r,
        o = rt(i);

    function i(t, e, n) {
      var r;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, i), (r = o.call(this, e._interaction)).target = void 0, r.dropzone = void 0, r.dragEvent = void 0, r.relatedTarget = void 0, r.draggable = void 0, r.timeStamp = void 0, r.propagationStopped = !1, r.immediatePropagationStopped = !1;
      var a = "dragleave" === n ? t.prev : t.cur,
          s = a.element,
          l = a.dropzone;
      return r.type = n, r.target = s, r.currentTarget = s, r.dropzone = l, r.dragEvent = e, r.relatedTarget = e.target, r.draggable = e.interactable, r.timeStamp = e.timeStamp, r;
    }

    return e = i, (n = [{
      key: "reject",
      value: function value() {
        var t = this,
            e = this._interaction.dropState;
        if ("dropactivate" === this.type || this.dropzone && e.cur.dropzone === this.dropzone && e.cur.element === this.target) if (e.prev.dropzone = this.dropzone, e.prev.element = this.target, e.rejected = !0, e.events.enter = null, this.stopImmediatePropagation(), "dropactivate" === this.type) {
          var n = e.activeDrops,
              r = K.findIndex(n, function (e) {
            var n = e.dropzone,
                r = e.element;
            return n === t.dropzone && r === t.target;
          });
          e.activeDrops.splice(r, 1);
          var o = new i(e, this.dragEvent, "dropdeactivate");
          o.dropzone = this.dropzone, o.target = this.target, this.dropzone.fire(o);
        } else this.dropzone.fire(new i(e, this.dragEvent, "dragleave"));
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
    }]) && et(e.prototype, n), r && et(e, r), i;
  }($.BaseEvent);

  Q.DropEvent = at;
  var st = {};

  function lt(t, e) {
    for (var n = 0; n < t.slice().length; n++) {
      var r = t.slice()[n],
          o = r.dropzone,
          i = r.element;
      e.dropzone = o, e.target = i, o.fire(e), e.propagationStopped = e.immediatePropagationStopped = !1;
    }
  }

  function ut(t, e) {
    for (var n = function (t, e) {
      for (var n = t.interactables, r = [], o = 0; o < n.list.length; o++) {
        var a = n.list[o];

        if (a.options.drop.enabled) {
          var s = a.options.drop.accept;
          if (!(i["default"].element(s) && s !== e || i["default"].string(s) && !_.matchesSelector(e, s) || i["default"].func(s) && !s({
            dropzone: a,
            draggableElement: e
          }))) for (var l = i["default"].string(a.target) ? a._context.querySelectorAll(a.target) : i["default"].array(a.target) ? a.target : [a.target], u = 0; u < l.length; u++) {
            var c = l[u];
            c !== e && r.push({
              dropzone: a,
              element: c
            });
          }
        }
      }

      return r;
    }(t, e), r = 0; r < n.length; r++) {
      var o = n[r];
      o.rect = o.dropzone.getRect(o.element);
    }

    return n;
  }

  function ct(t, e, n) {
    for (var r = t.dropState, o = t.interactable, i = t.element, a = [], s = 0; s < r.activeDrops.length; s++) {
      var l = r.activeDrops[s],
          u = l.dropzone,
          c = l.element,
          f = l.rect;
      a.push(u.dropCheck(e, n, o, i, c, f) ? c : null);
    }

    var d = _.indexOfDeepestElement(a);

    return r.activeDrops[d] || null;
  }

  function ft(t, e, n) {
    var r = t.dropState,
        o = {
      enter: null,
      leave: null,
      activate: null,
      deactivate: null,
      move: null,
      drop: null
    };
    return "dragstart" === n.type && (o.activate = new Q.DropEvent(r, n, "dropactivate"), o.activate.target = null, o.activate.dropzone = null), "dragend" === n.type && (o.deactivate = new Q.DropEvent(r, n, "dropdeactivate"), o.deactivate.target = null, o.deactivate.dropzone = null), r.rejected || (r.cur.element !== r.prev.element && (r.prev.dropzone && (o.leave = new Q.DropEvent(r, n, "dragleave"), n.dragLeave = o.leave.target = r.prev.element, n.prevDropzone = o.leave.dropzone = r.prev.dropzone), r.cur.dropzone && (o.enter = new Q.DropEvent(r, n, "dragenter"), n.dragEnter = r.cur.element, n.dropzone = r.cur.dropzone)), "dragend" === n.type && r.cur.dropzone && (o.drop = new Q.DropEvent(r, n, "drop"), n.dropzone = r.cur.dropzone, n.relatedTarget = r.cur.element), "dragmove" === n.type && r.cur.dropzone && (o.move = new Q.DropEvent(r, n, "dropmove"), o.move.dragmove = n, n.dropzone = r.cur.dropzone)), o;
  }

  function dt(t, e) {
    var n = t.dropState,
        r = n.activeDrops,
        o = n.cur,
        i = n.prev;
    e.leave && i.dropzone.fire(e.leave), e.enter && o.dropzone.fire(e.enter), e.move && o.dropzone.fire(e.move), e.drop && o.dropzone.fire(e.drop), e.deactivate && lt(r, e.deactivate), n.prev.dropzone = o.dropzone, n.prev.element = o.element;
  }

  function pt(t, e) {
    var n = t.interaction,
        r = t.iEvent,
        o = t.event;

    if ("dragmove" === r.type || "dragend" === r.type) {
      var i = n.dropState;
      e.dynamicDrop && (i.activeDrops = ut(e, n.element));
      var a = r,
          s = ct(n, a, o);
      i.rejected = i.rejected && !!s && s.dropzone === i.cur.dropzone && s.element === i.cur.element, i.cur.dropzone = s && s.dropzone, i.cur.element = s && s.element, i.events = ft(n, 0, a);
    }
  }

  Object.defineProperty(st, "__esModule", {
    value: !0
  }), st["default"] = void 0;
  var vt = {
    id: "actions/drop",
    install: function install(t) {
      var e = t.actions,
          n = t.interactStatic,
          r = t.Interactable,
          o = t.defaults;
      t.usePlugin(c["default"]), r.prototype.dropzone = function (t) {
        return function (t, e) {
          if (i["default"].object(e)) {
            if (t.options.drop.enabled = !1 !== e.enabled, e.listeners) {
              var n = (0, z["default"])(e.listeners),
                  r = Object.keys(n).reduce(function (t, e) {
                return t[/^(enter|leave)/.test(e) ? "drag".concat(e) : /^(activate|deactivate|move)/.test(e) ? "drop".concat(e) : e] = n[e], t;
              }, {});
              t.off(t.options.drop.listeners), t.on(r), t.options.drop.listeners = r;
            }

            return i["default"].func(e.ondrop) && t.on("drop", e.ondrop), i["default"].func(e.ondropactivate) && t.on("dropactivate", e.ondropactivate), i["default"].func(e.ondropdeactivate) && t.on("dropdeactivate", e.ondropdeactivate), i["default"].func(e.ondragenter) && t.on("dragenter", e.ondragenter), i["default"].func(e.ondragleave) && t.on("dragleave", e.ondragleave), i["default"].func(e.ondropmove) && t.on("dropmove", e.ondropmove), /^(pointer|center)$/.test(e.overlap) ? t.options.drop.overlap = e.overlap : i["default"].number(e.overlap) && (t.options.drop.overlap = Math.max(Math.min(1, e.overlap), 0)), "accept" in e && (t.options.drop.accept = e.accept), "checker" in e && (t.options.drop.checker = e.checker), t;
          }

          if (i["default"].bool(e)) return t.options.drop.enabled = e, t;
          return t.options.drop;
        }(this, t);
      }, r.prototype.dropCheck = function (t, e, n, r, o, a) {
        return function (t, e, n, r, o, a, s) {
          var l = !1;
          if (!(s = s || t.getRect(a))) return !!t.options.drop.checker && t.options.drop.checker(e, n, l, t, a, r, o);
          var u = t.options.drop.overlap;

          if ("pointer" === u) {
            var c = (0, A["default"])(r, o, "drag"),
                f = W.getPageXY(e);
            f.x += c.x, f.y += c.y;
            var d = f.x > s.left && f.x < s.right,
                p = f.y > s.top && f.y < s.bottom;
            l = d && p;
          }

          var v = r.getRect(o);

          if (v && "center" === u) {
            var h = v.left + v.width / 2,
                g = v.top + v.height / 2;
            l = h >= s.left && h <= s.right && g >= s.top && g <= s.bottom;
          }

          if (v && i["default"].number(u)) {
            var y = Math.max(0, Math.min(s.right, v.right) - Math.max(s.left, v.left)) * Math.max(0, Math.min(s.bottom, v.bottom) - Math.max(s.top, v.top)) / (v.width * v.height);
            l = y >= u;
          }

          t.options.drop.checker && (l = t.options.drop.checker(e, n, l, t, a, r, o));
          return l;
        }(this, t, e, n, r, o, a);
      }, n.dynamicDrop = function (e) {
        return i["default"].bool(e) ? (t.dynamicDrop = e, n) : t.dynamicDrop;
      }, (0, j["default"])(e.phaselessTypes, {
        dragenter: !0,
        dragleave: !0,
        dropactivate: !0,
        dropdeactivate: !0,
        dropmove: !0,
        drop: !0
      }), e.methodDict.drop = "dropzone", t.dynamicDrop = !1, o.actions.drop = vt.defaults;
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
          o.activeDrops = null, o.events = null, o.activeDrops = ut(e, n.element), o.events = ft(n, 0, r), o.events.activate && (lt(o.activeDrops, o.events.activate), e.fire("actions/drop:start", {
            interaction: n,
            dragEvent: r
          }));
        }
      },
      "interactions:action-move": pt,
      "interactions:after-action-move": function interactionsAfterActionMove(t, e) {
        var n = t.interaction,
            r = t.iEvent;
        "drag" === n.prepared.name && (dt(n, n.dropState.events), e.fire("actions/drop:move", {
          interaction: n,
          dragEvent: r
        }), n.dropState.events = {});
      },
      "interactions:action-end": function interactionsActionEnd(t, e) {
        if ("drag" === t.interaction.prepared.name) {
          var n = t.interaction,
              r = t.iEvent;
          pt(t, e), dt(n, n.dropState.events), e.fire("actions/drop:end", {
            interaction: n,
            dragEvent: r
          });
        }
      },
      "interactions:stop": function interactionsStop(t) {
        var e = t.interaction;

        if ("drag" === e.prepared.name) {
          var n = e.dropState;
          n && (n.activeDrops = null, n.events = null, n.cur.dropzone = null, n.cur.element = null, n.prev.dropzone = null, n.prev.element = null, n.rejected = !1);
        }
      }
    },
    getActiveDrops: ut,
    getDrop: ct,
    getDropEvents: ft,
    fireDropEvents: dt,
    defaults: {
      enabled: !1,
      accept: null,
      overlap: "pointer"
    }
  },
      ht = vt;
  st["default"] = ht;
  var gt = {};

  function yt(t) {
    var e = t.interaction,
        n = t.iEvent,
        r = t.phase;

    if ("gesture" === e.prepared.name) {
      var o = e.pointers.map(function (t) {
        return t.pointer;
      }),
          a = "start" === r,
          s = "end" === r,
          l = e.interactable.options.deltaSource;
      if (n.touches = [o[0], o[1]], a) n.distance = W.touchDistance(o, l), n.box = W.touchBBox(o), n.scale = 1, n.ds = 0, n.angle = W.touchAngle(o, l), n.da = 0, e.gesture.startDistance = n.distance, e.gesture.startAngle = n.angle;else if (s) {
        var u = e.prevEvent;
        n.distance = u.distance, n.box = u.box, n.scale = u.scale, n.ds = 0, n.angle = u.angle, n.da = 0;
      } else n.distance = W.touchDistance(o, l), n.box = W.touchBBox(o), n.scale = n.distance / e.gesture.startDistance, n.angle = W.touchAngle(o, l), n.ds = n.scale - e.gesture.scale, n.da = n.angle - e.gesture.angle;
      e.gesture.distance = n.distance, e.gesture.angle = n.angle, i["default"].number(n.scale) && n.scale !== 1 / 0 && !isNaN(n.scale) && (e.gesture.scale = n.scale);
    }
  }

  Object.defineProperty(gt, "__esModule", {
    value: !0
  }), gt["default"] = void 0;
  var mt = {
    id: "actions/gesture",
    before: ["actions/drag", "actions/resize"],
    install: function install(t) {
      var e = t.actions,
          n = t.Interactable,
          r = t.defaults;
      n.prototype.gesturable = function (t) {
        return i["default"].object(t) ? (this.options.gesture.enabled = !1 !== t.enabled, this.setPerAction("gesture", t), this.setOnEvents("gesture", t), this) : i["default"].bool(t) ? (this.options.gesture.enabled = t, this) : this.options.gesture;
      }, e.map.gesture = mt, e.methodDict.gesture = "gesturable", r.actions.gesture = mt.defaults;
    },
    listeners: {
      "interactions:action-start": yt,
      "interactions:action-move": yt,
      "interactions:action-end": yt,
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
          if (e && e.enabled) return t.action = {
            name: "gesture"
          }, !1;
        }
      }
    },
    defaults: {},
    getCursor: function getCursor() {
      return "";
    }
  },
      bt = mt;
  gt["default"] = bt;
  var xt = {};

  function wt(t, e, n, r, o, a, s) {
    if (!e) return !1;

    if (!0 === e) {
      var l = i["default"].number(a.width) ? a.width : a.right - a.left,
          u = i["default"].number(a.height) ? a.height : a.bottom - a.top;
      if (s = Math.min(s, Math.abs(("left" === t || "right" === t ? l : u) / 2)), l < 0 && ("left" === t ? t = "right" : "right" === t && (t = "left")), u < 0 && ("top" === t ? t = "bottom" : "bottom" === t && (t = "top")), "left" === t) return n.x < (l >= 0 ? a.left : a.right) + s;
      if ("top" === t) return n.y < (u >= 0 ? a.top : a.bottom) + s;
      if ("right" === t) return n.x > (l >= 0 ? a.right : a.left) - s;
      if ("bottom" === t) return n.y > (u >= 0 ? a.bottom : a.top) - s;
    }

    return !!i["default"].element(r) && (i["default"].element(e) ? e === r : _.matchesUpTo(r, e, o));
  }

  function _t(t) {
    var e = t.iEvent,
        n = t.interaction;

    if ("resize" === n.prepared.name && n.resizeAxes) {
      var r = e;
      n.interactable.options.resize.square ? ("y" === n.resizeAxes ? r.delta.x = r.delta.y : r.delta.y = r.delta.x, r.axes = "xy") : (r.axes = n.resizeAxes, "x" === n.resizeAxes ? r.delta.y = 0 : "y" === n.resizeAxes && (r.delta.x = 0));
    }
  }

  Object.defineProperty(xt, "__esModule", {
    value: !0
  }), xt["default"] = void 0;
  var St = {
    id: "actions/resize",
    before: ["actions/drag"],
    install: function install(t) {
      var e = t.actions,
          n = t.browser,
          r = t.Interactable,
          o = t.defaults;
      St.cursors = function (t) {
        return t.isIe9 ? {
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
        };
      }(n), St.defaultMargin = n.supportsTouch || n.supportsPointerEvent ? 20 : 10, r.prototype.resizable = function (e) {
        return function (t, e, n) {
          if (i["default"].object(e)) return t.options.resize.enabled = !1 !== e.enabled, t.setPerAction("resize", e), t.setOnEvents("resize", e), i["default"].string(e.axis) && /^x$|^y$|^xy$/.test(e.axis) ? t.options.resize.axis = e.axis : null === e.axis && (t.options.resize.axis = n.defaults.actions.resize.axis), i["default"].bool(e.preserveAspectRatio) ? t.options.resize.preserveAspectRatio = e.preserveAspectRatio : i["default"].bool(e.square) && (t.options.resize.square = e.square), t;
          if (i["default"].bool(e)) return t.options.resize.enabled = e, t;
          return t.options.resize;
        }(this, e, t);
      }, e.map.resize = St, e.methodDict.resize = "resizable", o.actions.resize = St.defaults;
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
              start: (0, j["default"])({}, o),
              corrected: (0, j["default"])({}, o),
              previous: (0, j["default"])({}, o),
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
        }(t), _t(t);
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
                s = n._rects,
                l = s.start,
                u = s.corrected,
                c = s.delta,
                f = s.previous;

            if ((0, j["default"])(f, u), i) {
              if ((0, j["default"])(u, a), "reposition" === o) {
                if (u.top > u.bottom) {
                  var d = u.top;
                  u.top = u.bottom, u.bottom = d;
                }

                if (u.left > u.right) {
                  var p = u.left;
                  u.left = u.right, u.right = p;
                }
              }
            } else u.top = Math.min(a.top, l.bottom), u.bottom = Math.max(a.bottom, l.top), u.left = Math.min(a.left, l.right), u.right = Math.max(a.right, l.left);

            for (var v in u.width = u.right - u.left, u.height = u.bottom - u.top, u) {
              c[v] = u[v] - f[v];
            }

            r.edges = n.prepared.edges, r.rect = u, r.deltaRect = c;
          }
        }(t), _t(t);
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
            a = t.buttons;

        if (o) {
          var s = (0, j["default"])({}, e.coords.cur.page),
              l = n.options.resize;

          if (l && l.enabled && (!e.pointerIsDown || !/mouse|pointer/.test(e.pointerType) || 0 != (a & l.mouseButtons))) {
            if (i["default"].object(l.edges)) {
              var u = {
                left: !1,
                right: !1,
                top: !1,
                bottom: !1
              };

              for (var c in u) {
                u[c] = wt(c, l.edges[c], s, e._latestPointer.eventTarget, r, o, l.margin || St.defaultMargin);
              }

              u.left = u.left && !u.right, u.top = u.top && !u.bottom, (u.left || u.right || u.top || u.bottom) && (t.action = {
                name: "resize",
                edges: u
              });
            } else {
              var f = "y" !== l.axis && s.x > o.right - St.defaultMargin,
                  d = "x" !== l.axis && s.y > o.bottom - St.defaultMargin;
              (f || d) && (t.action = {
                name: "resize",
                axes: (f ? "x" : "") + (d ? "y" : "")
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
          o = St.cursors,
          i = null;
      if (n) i = o[r + n];else if (e) {
        for (var a = "", s = ["top", "bottom", "left", "right"], l = 0; l < s.length; l++) {
          var u = s[l];
          e[u] && (a += u);
        }

        i = o[a];
      }
      return i;
    },
    defaultMargin: null
  },
      Pt = St;
  xt["default"] = Pt;
  var Ot = {};
  Object.defineProperty(Ot, "__esModule", {
    value: !0
  }), Ot["default"] = void 0;
  var Et = {
    id: "actions",
    install: function install(t) {
      t.usePlugin(gt["default"]), t.usePlugin(xt["default"]), t.usePlugin(c["default"]), t.usePlugin(st["default"]);
    }
  };
  Ot["default"] = Et;
  var Tt = {};
  Object.defineProperty(Tt, "__esModule", {
    value: !0
  }), Tt["default"] = void 0;
  Tt["default"] = {};
  var Mt = {};
  Object.defineProperty(Mt, "__esModule", {
    value: !0
  }), Mt["default"] = void 0;
  var jt,
      kt,
      It = 0;
  var Dt = {
    request: function request(t) {
      return jt(t);
    },
    cancel: function cancel(t) {
      return kt(t);
    },
    init: function init(t) {
      if (jt = t.requestAnimationFrame, kt = t.cancelAnimationFrame, !jt) for (var e = ["ms", "moz", "webkit", "o"], n = 0; n < e.length; n++) {
        var r = e[n];
        jt = t["".concat(r, "RequestAnimationFrame")], kt = t["".concat(r, "CancelAnimationFrame")] || t["".concat(r, "CancelRequestAnimationFrame")];
      }
      jt = jt && jt.bind(t), kt = kt && kt.bind(t), jt || (jt = function jt(e) {
        var n = Date.now(),
            r = Math.max(0, 16 - (n - It)),
            o = t.setTimeout(function () {
          e(n + r);
        }, r);
        return It = n + r, o;
      }, kt = function kt(t) {
        return clearTimeout(t);
      });
    }
  };
  Mt["default"] = Dt;
  var At = {};
  Object.defineProperty(At, "__esModule", {
    value: !0
  }), At.getContainer = Ct, At.getScroll = Rt, At.getScrollSize = function (t) {
    i["default"].window(t) && (t = window.document.body);
    return {
      x: t.scrollWidth,
      y: t.scrollHeight
    };
  }, At.getScrollSizeDelta = function (t, e) {
    var n = t.interaction,
        r = t.element,
        o = n && n.interactable.options[n.prepared.name].autoScroll;
    if (!o || !o.enabled) return e(), {
      x: 0,
      y: 0
    };
    var i = Ct(o.container, n.interactable, r),
        a = Rt(i);
    e();
    var s = Rt(i);
    return {
      x: s.x - a.x,
      y: s.y - a.y
    };
  }, At["default"] = void 0;
  var zt = {
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
      zt.isScrolling = !0, Mt["default"].cancel(zt.i), t.autoScroll = zt, zt.interaction = t, zt.prevTime = zt.now(), zt.i = Mt["default"].request(zt.scroll);
    },
    stop: function stop() {
      zt.isScrolling = !1, zt.interaction && (zt.interaction.autoScroll = null), Mt["default"].cancel(zt.i);
    },
    scroll: function scroll() {
      var t = zt.interaction,
          e = t.interactable,
          n = t.element,
          r = t.prepared.name,
          o = e.options[r].autoScroll,
          a = Ct(o.container, e, n),
          s = zt.now(),
          l = (s - zt.prevTime) / 1e3,
          u = o.speed * l;

      if (u >= 1) {
        var c = {
          x: zt.x * u,
          y: zt.y * u
        };

        if (c.x || c.y) {
          var f = Rt(a);
          i["default"].window(a) ? a.scrollBy(c.x, c.y) : a && (a.scrollLeft += c.x, a.scrollTop += c.y);
          var d = Rt(a),
              p = {
            x: d.x - f.x,
            y: d.y - f.y
          };
          (p.x || p.y) && e.fire({
            type: "autoscroll",
            target: n,
            interactable: e,
            delta: p,
            interaction: t,
            container: a
          });
        }

        zt.prevTime = s;
      }

      zt.isScrolling && (Mt["default"].cancel(zt.i), zt.i = Mt["default"].request(zt.scroll));
    },
    check: function check(t, e) {
      var n = t.options;
      return n[e].autoScroll && n[e].autoScroll.enabled;
    },
    onInteractionMove: function onInteractionMove(t) {
      var e = t.interaction,
          n = t.pointer;
      if (e.interacting() && zt.check(e.interactable, e.prepared.name)) if (e.simulation) zt.x = zt.y = 0;else {
        var r,
            o,
            a,
            s,
            l = e.interactable,
            u = e.element,
            c = e.prepared.name,
            f = l.options[c].autoScroll,
            d = Ct(f.container, l, u);
        if (i["default"].window(d)) s = n.clientX < zt.margin, r = n.clientY < zt.margin, o = n.clientX > d.innerWidth - zt.margin, a = n.clientY > d.innerHeight - zt.margin;else {
          var p = _.getElementClientRect(d);

          s = n.clientX < p.left + zt.margin, r = n.clientY < p.top + zt.margin, o = n.clientX > p.right - zt.margin, a = n.clientY > p.bottom - zt.margin;
        }
        zt.x = o ? 1 : s ? -1 : 0, zt.y = a ? 1 : r ? -1 : 0, zt.isScrolling || (zt.margin = f.margin, zt.speed = f.speed, zt.start(e));
      }
    }
  };

  function Ct(t, n, r) {
    return (i["default"].string(t) ? (0, k.getStringOptionResult)(t, n, r) : t) || (0, e.getWindow)(r);
  }

  function Rt(t) {
    return i["default"].window(t) && (t = window.document.body), {
      x: t.scrollLeft,
      y: t.scrollTop
    };
  }

  var Ft = {
    id: "auto-scroll",
    install: function install(t) {
      var e = t.defaults,
          n = t.actions;
      t.autoScroll = zt, zt.now = function () {
        return t.now();
      }, n.phaselessTypes.autoscroll = !0, e.perAction.autoScroll = zt.defaults;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.autoScroll = null;
      },
      "interactions:destroy": function interactionsDestroy(t) {
        t.interaction.autoScroll = null, zt.stop(), zt.interaction && (zt.interaction = null);
      },
      "interactions:stop": zt.stop,
      "interactions:action-move": function interactionsActionMove(t) {
        return zt.onInteractionMove(t);
      }
    }
  };
  At["default"] = Ft;
  var Xt = {};
  Object.defineProperty(Xt, "__esModule", {
    value: !0
  }), Xt.warnOnce = function (t, n) {
    var r = !1;
    return function () {
      return r || (e.window.console.warn(n), r = !0), t.apply(this, arguments);
    };
  }, Xt.copyAction = function (t, e) {
    return t.name = e.name, t.axis = e.axis, t.edges = e.edges, t;
  };
  var Yt = {};

  function Wt(t) {
    return i["default"].bool(t) ? (this.options.styleCursor = t, this) : null === t ? (delete this.options.styleCursor, this) : this.options.styleCursor;
  }

  function Lt(t) {
    return i["default"].func(t) ? (this.options.actionChecker = t, this) : null === t ? (delete this.options.actionChecker, this) : this.options.actionChecker;
  }

  Object.defineProperty(Yt, "__esModule", {
    value: !0
  }), Yt["default"] = void 0;
  var Bt = {
    id: "auto-start/interactableMethods",
    install: function install(t) {
      var e = t.Interactable;
      e.prototype.getAction = function (e, n, r, o) {
        var i = function (t, e, n, r, o) {
          var i = t.getRect(r),
              a = e.buttons || {
            0: 1,
            1: 4,
            3: 8,
            4: 16
          }[e.button],
              s = {
            action: null,
            interactable: t,
            interaction: n,
            element: r,
            rect: i,
            buttons: a
          };
          return o.fire("auto-start:check", s), s.action;
        }(this, n, r, o, t);

        return this.options.actionChecker ? this.options.actionChecker(e, n, i, this, o, r) : i;
      }, e.prototype.ignoreFrom = (0, Xt.warnOnce)(function (t) {
        return this._backCompatOption("ignoreFrom", t);
      }, "Interactable.ignoreFrom() has been deprecated. Use Interactble.draggable({ignoreFrom: newValue})."), e.prototype.allowFrom = (0, Xt.warnOnce)(function (t) {
        return this._backCompatOption("allowFrom", t);
      }, "Interactable.allowFrom() has been deprecated. Use Interactble.draggable({allowFrom: newValue})."), e.prototype.actionChecker = Lt, e.prototype.styleCursor = Wt;
    }
  };
  Yt["default"] = Bt;
  var Ut = {};

  function Nt(t, e, n, r, o) {
    return e.testIgnoreAllow(e.options[t.name], n, r) && e.options[t.name].enabled && Gt(e, n, t, o) ? t : null;
  }

  function Vt(t, e, n, r, o, i, a) {
    for (var s = 0, l = r.length; s < l; s++) {
      var u = r[s],
          c = o[s],
          f = u.getAction(e, n, t, c);

      if (f) {
        var d = Nt(f, u, c, i, a);
        if (d) return {
          action: d,
          interactable: u,
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

  function qt(t, e, n, r, o) {
    var a = [],
        s = [],
        l = r;

    function u(t) {
      a.push(t), s.push(l);
    }

    for (; i["default"].element(l);) {
      a = [], s = [], o.interactables.forEachMatch(l, u);
      var c = Vt(t, e, n, a, s, r, o);
      if (c.action && !c.interactable.options[c.action.name].manualStart) return c;
      l = _.parentNode(l);
    }

    return {
      action: null,
      interactable: null,
      element: null
    };
  }

  function $t(t, e, n) {
    var r = e.action,
        o = e.interactable,
        i = e.element;
    r = r || {
      name: null
    }, t.interactable = o, t.element = i, (0, Xt.copyAction)(t.prepared, r), t.rect = o && r.name ? o.getRect(i) : null, Zt(t, n), n.fire("autoStart:prepared", {
      interaction: t
    });
  }

  function Gt(t, e, n, r) {
    var o = t.options,
        i = o[n.name].max,
        a = o[n.name].maxPerElement,
        s = r.autoStart.maxInteractions,
        l = 0,
        u = 0,
        c = 0;
    if (!(i && a && s)) return !1;

    for (var f = 0; f < r.interactions.list.length; f++) {
      var d = r.interactions.list[f],
          p = d.prepared.name;

      if (d.interacting()) {
        if (++l >= s) return !1;

        if (d.interactable === t) {
          if ((u += p === n.name ? 1 : 0) >= i) return !1;
          if (d.element === e && (c++, p === n.name && c >= a)) return !1;
        }
      }
    }

    return s > 0;
  }

  function Ht(t, e) {
    return i["default"].number(t) ? (e.autoStart.maxInteractions = t, this) : e.autoStart.maxInteractions;
  }

  function Kt(t, e, n) {
    var r = n.autoStart.cursorElement;
    r && r !== t && (r.style.cursor = ""), t.ownerDocument.documentElement.style.cursor = e, t.style.cursor = e, n.autoStart.cursorElement = e ? t : null;
  }

  function Zt(t, e) {
    var n = t.interactable,
        r = t.element,
        o = t.prepared;

    if ("mouse" === t.pointerType && n && n.options.styleCursor) {
      var a = "";

      if (o.name) {
        var s = n.options[o.name].cursorChecker;
        a = i["default"].func(s) ? s(o, n, r, t._interacting) : e.actions.map[o.name].getCursor(o);
      }

      Kt(t.element, a || "", e);
    } else e.autoStart.cursorElement && Kt(e.autoStart.cursorElement, "", e);
  }

  Object.defineProperty(Ut, "__esModule", {
    value: !0
  }), Ut["default"] = void 0;
  var Jt = {
    id: "auto-start/base",
    before: ["actions"],
    install: function install(t) {
      var e = t.interactStatic,
          n = t.defaults;
      t.usePlugin(Yt["default"]), n.base.actionChecker = null, n.base.styleCursor = !0, (0, j["default"])(n.perAction, {
        manualStart: !1,
        max: 1 / 0,
        maxPerElement: 1,
        allowFrom: null,
        ignoreFrom: null,
        mouseButtons: 1
      }), e.maxInteractions = function (e) {
        return Ht(e, t);
      }, t.autoStart = {
        maxInteractions: 1 / 0,
        withinInteractionLimit: Gt,
        cursorElement: null
      };
    },
    listeners: {
      "interactions:down": function interactionsDown(t, e) {
        var n = t.interaction,
            r = t.pointer,
            o = t.event,
            i = t.eventTarget;
        n.interacting() || $t(n, qt(n, r, o, i, e), e);
      },
      "interactions:move": function interactionsMove(t, e) {
        !function (t, e) {
          var n = t.interaction,
              r = t.pointer,
              o = t.event,
              i = t.eventTarget;
          "mouse" !== n.pointerType || n.pointerIsDown || n.interacting() || $t(n, qt(n, r, o, i, e), e);
        }(t, e), function (t, e) {
          var n = t.interaction;

          if (n.pointerIsDown && !n.interacting() && n.pointerWasMoved && n.prepared.name) {
            e.fire("autoStart:before-start", t);
            var r = n.interactable,
                o = n.prepared.name;
            o && r && (r.options[o].manualStart || !Gt(r, n.element, n.prepared, e) ? n.stop() : (n.start(n.prepared, r, n.element), Zt(n, e)));
          }
        }(t, e);
      },
      "interactions:stop": function interactionsStop(t, e) {
        var n = t.interaction,
            r = n.interactable;
        r && r.options.styleCursor && Kt(n.element, "", e);
      }
    },
    maxInteractions: Ht,
    withinInteractionLimit: Gt,
    validateAction: Nt
  };
  Ut["default"] = Jt;
  var Qt = {};
  Object.defineProperty(Qt, "__esModule", {
    value: !0
  }), Qt["default"] = void 0;
  var te = {
    id: "auto-start/dragAxis",
    listeners: {
      "autoStart:before-start": function autoStartBeforeStart(t, e) {
        var n = t.interaction,
            r = t.eventTarget,
            o = t.dx,
            a = t.dy;

        if ("drag" === n.prepared.name) {
          var s = Math.abs(o),
              l = Math.abs(a),
              u = n.interactable.options.drag,
              c = u.startAxis,
              f = s > l ? "x" : s < l ? "y" : "xy";

          if (n.prepared.axis = "start" === u.lockAxis ? f[0] : u.lockAxis, "xy" !== f && "xy" !== c && c !== f) {
            n.prepared.name = null;

            for (var d = r, p = function p(t) {
              if (t !== n.interactable) {
                var o = n.interactable.options.drag;

                if (!o.manualStart && t.testIgnoreAllow(o, d, r)) {
                  var i = t.getAction(n.downPointer, n.downEvent, n, d);
                  if (i && "drag" === i.name && function (t, e) {
                    if (!e) return !1;
                    var n = e.options.drag.startAxis;
                    return "xy" === t || "xy" === n || n === t;
                  }(f, t) && Ut["default"].validateAction(i, t, d, r, e)) return t;
                }
              }
            }; i["default"].element(d);) {
              var v = e.interactables.forEachMatch(d, p);

              if (v) {
                n.prepared.name = "drag", n.interactable = v, n.element = d;
                break;
              }

              d = (0, _.parentNode)(d);
            }
          }
        }
      }
    }
  };
  Qt["default"] = te;
  var ee = {};

  function ne(t) {
    var e = t.prepared && t.prepared.name;
    if (!e) return null;
    var n = t.interactable.options;
    return n[e].hold || n[e].delay;
  }

  Object.defineProperty(ee, "__esModule", {
    value: !0
  }), ee["default"] = void 0;
  var re = {
    id: "auto-start/hold",
    install: function install(t) {
      var e = t.defaults;
      t.usePlugin(Ut["default"]), e.perAction.hold = 0, e.perAction.delay = 0;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.autoStartHoldTimer = null;
      },
      "autoStart:prepared": function autoStartPrepared(t) {
        var e = t.interaction,
            n = ne(e);
        n > 0 && (e.autoStartHoldTimer = setTimeout(function () {
          e.start(e.prepared, e.interactable, e.element);
        }, n));
      },
      "interactions:move": function interactionsMove(t) {
        var e = t.interaction,
            n = t.duplicate;
        e.autoStartHoldTimer && e.pointerWasMoved && !n && (clearTimeout(e.autoStartHoldTimer), e.autoStartHoldTimer = null);
      },
      "autoStart:before-start": function autoStartBeforeStart(t) {
        var e = t.interaction;
        ne(e) > 0 && (e.prepared.name = null);
      }
    },
    getHoldDuration: ne
  };
  ee["default"] = re;
  var oe = {};
  Object.defineProperty(oe, "__esModule", {
    value: !0
  }), oe["default"] = void 0;
  var ie = {
    id: "auto-start",
    install: function install(t) {
      t.usePlugin(Ut["default"]), t.usePlugin(ee["default"]), t.usePlugin(Qt["default"]);
    }
  };
  oe["default"] = ie;
  var ae = {};
  Object.defineProperty(ae, "__esModule", {
    value: !0
  }), ae["default"] = void 0;
  ae["default"] = {};
  var se = {};

  function le(t) {
    return /^(always|never|auto)$/.test(t) ? (this.options.preventDefault = t, this) : i["default"].bool(t) ? (this.options.preventDefault = t ? "always" : "never", this) : this.options.preventDefault;
  }

  function ue(t) {
    var e = t.interaction,
        n = t.event;
    e.interactable && e.interactable.checkAndPreventDefault(n);
  }

  function ce(t) {
    var n = t.Interactable;
    n.prototype.preventDefault = le, n.prototype.checkAndPreventDefault = function (n) {
      return function (t, n, r) {
        var o = t.options.preventDefault;
        if ("never" !== o) if ("always" !== o) {
          if (n.events.supportsPassive && /^touch(start|move)$/.test(r.type)) {
            var a = (0, e.getWindow)(r.target).document,
                s = n.getDocOptions(a);
            if (!s || !s.events || !1 !== s.events.passive) return;
          }

          /^(mouse|pointer|touch)*(down|start)/i.test(r.type) || i["default"].element(r.target) && (0, _.matchesSelector)(r.target, "input,select,textarea,[contenteditable=true],[contenteditable=true] *") || r.preventDefault();
        } else r.preventDefault();
      }(this, t, n);
    }, t.interactions.docEvents.push({
      type: "dragstart",
      listener: function listener(e) {
        for (var n = 0; n < t.interactions.list.length; n++) {
          var r = t.interactions.list[n];
          if (r.element && (r.element === e.target || (0, _.nodeContains)(r.element, e.target))) return void r.interactable.checkAndPreventDefault(e);
        }
      }
    });
  }

  Object.defineProperty(se, "__esModule", {
    value: !0
  }), se.install = ce, se["default"] = void 0;
  var fe = {
    id: "core/interactablePreventDefault",
    install: ce,
    listeners: ["down", "move", "up", "cancel"].reduce(function (t, e) {
      return t["interactions:".concat(e)] = ue, t;
    }, {})
  };
  se["default"] = fe;
  var de,
      pe = {};

  function ve(t) {
    return function (t) {
      if (Array.isArray(t)) return he(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(t) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return he(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(t);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return he(t, e);
    }(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function he(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(pe, "__esModule", {
    value: !0
  }), pe["default"] = void 0, function (t) {
    t.touchAction = "touchAction", t.boxSizing = "boxSizing", t.noListeners = "noListeners";
  }(de || (de = {}));
  var ge = {
    touchAction: "https://developer.mozilla.org/en-US/docs/Web/CSS/touch-action",
    boxSizing: "https://developer.mozilla.org/en-US/docs/Web/CSS/box-sizing"
  };
  var ye = [{
    name: de.touchAction,
    perform: function perform(t) {
      return !function (t, e, n) {
        var r = t;

        for (; i["default"].element(r);) {
          if (me(r, e, n)) return !0;
          r = (0, _.parentNode)(r);
        }

        return !1;
      }(t.element, "touchAction", /pan-|pinch|none/);
    },
    getInfo: function getInfo(t) {
      return [t.element, ge.touchAction];
    },
    text: 'Consider adding CSS "touch-action: none" to this element\n'
  }, {
    name: de.boxSizing,
    perform: function perform(t) {
      var e = t.element;
      return "resize" === t.prepared.name && e instanceof h["default"].HTMLElement && !me(e, "boxSizing", /border-box/);
    },
    text: 'Consider adding CSS "box-sizing: border-box" to this resizable element',
    getInfo: function getInfo(t) {
      return [t.element, ge.boxSizing];
    }
  }, {
    name: de.noListeners,
    perform: function perform(t) {
      var e = t.prepared.name;
      return !(t.interactable.events.types["".concat(e, "move")] || []).length;
    },
    getInfo: function getInfo(t) {
      return [t.prepared.name, t.interactable];
    },
    text: "There are no listeners set for this action"
  }];

  function me(t, n, r) {
    var o = t.style[n] || e.window.getComputedStyle(t)[n];
    return r.test((o || "").toString());
  }

  var be = "dev-tools",
      xe = {
    id: be,
    install: function install(t) {
      var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
          n = e.logger,
          r = t.Interactable,
          o = t.defaults;
      t.logger = n || console, o.base.devTools = {
        ignore: {}
      }, r.prototype.devTools = function (t) {
        return t ? ((0, j["default"])(this.options.devTools, t), this) : this.options.devTools;
      };
    },
    listeners: {
      "interactions:action-start": function interactionsActionStart(t, e) {
        for (var n = t.interaction, r = 0; r < ye.length; r++) {
          var o,
              i = ye[r],
              a = n.interactable && n.interactable.options;
          if (!(a && a.devTools && a.devTools.ignore[i.name]) && i.perform(n)) (o = e.logger).warn.apply(o, ["[interact.js] " + i.text].concat(ve(i.getInfo(n))));
        }
      }
    },
    checks: ye,
    CheckName: de,
    links: ge,
    prefix: "[interact.js] "
  };
  pe["default"] = xe;
  var we = {};
  Object.defineProperty(we, "__esModule", {
    value: !0
  }), we["default"] = void 0;
  we["default"] = {};
  var _e = {};
  Object.defineProperty(_e, "__esModule", {
    value: !0
  }), _e["default"] = function t(e) {
    var n = {};

    for (var r in e) {
      var o = e[r];
      i["default"].plainObject(o) ? n[r] = t(o) : i["default"].array(o) ? n[r] = K.from(o) : n[r] = o;
    }

    return n;
  };
  var Se = {};

  function Pe(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" == typeof Symbol || !(Symbol.iterator in Object(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, s = t[Symbol.iterator](); !(r = (a = s.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == s["return"] || s["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return Oe(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(t);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Oe(t, e);
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function Oe(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function Ee(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  Object.defineProperty(Se, "__esModule", {
    value: !0
  }), Se.getRectOffset = je, Se["default"] = void 0;

  var Te = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.states = [], this.startOffset = {
        left: 0,
        right: 0,
        top: 0,
        bottom: 0
      }, this.startDelta = null, this.result = null, this.endResult = null, this.edges = void 0, this.interaction = void 0, this.interaction = e, this.result = Me();
    }

    var e, n, r;
    return e = t, (n = [{
      key: "start",
      value: function value(t, e) {
        var n = t.phase,
            r = this.interaction,
            o = function (t) {
          var e = t.interactable.options[t.prepared.name],
              n = e.modifiers;
          return n && n.length ? n : ["snap", "snapSize", "snapEdges", "restrict", "restrictEdges", "restrictSize"].map(function (t) {
            var n = e[t];
            return n && n.enabled && {
              options: n,
              methods: n._methods
            };
          }).filter(function (t) {
            return !!t;
          });
        }(r);

        this.prepareStates(o), this.edges = (0, j["default"])({}, r.edges), this.startOffset = je(r.rect, e), this.startDelta = {
          x: 0,
          y: 0
        };
        var i = {
          phase: n,
          pageCoords: e,
          preEnd: !1
        };
        return this.result = Me(), this.startAll(i), this.result = this.setAll(i);
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
          n.methods.start && (t.state = n, n.methods.start(t));
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
        t.coords = (0, j["default"])({}, t.pageCoords), t.rect = (0, j["default"])({}, o);

        for (var i = r ? this.states.slice(r) : this.states, a = Me(t.coords, t.rect), s = 0; s < i.length; s++) {
          var l = i[s],
              u = l.options,
              c = (0, j["default"])({}, t.coords),
              f = null;
          l.methods.set && this.shouldDo(u, n, e) && (t.state = l, f = l.methods.set(t), k.addEdges(this.interaction.edges, t.rect, {
            x: t.coords.x - c.x,
            y: t.coords.y - c.y
          })), a.eventProps.push(f);
        }

        a.delta.x = t.coords.x - t.pageCoords.x, a.delta.y = t.coords.y - t.pageCoords.y, a.rectDelta.left = t.rect.left - o.left, a.rectDelta.right = t.rect.right - o.right, a.rectDelta.top = t.rect.top - o.top, a.rectDelta.bottom = t.rect.bottom - o.bottom;
        var d = this.result.coords,
            p = this.result.rect;

        if (d && p) {
          var v = a.rect.left !== p.left || a.rect.right !== p.right || a.rect.top !== p.top || a.rect.bottom !== p.bottom;
          a.changed = v || d.x !== a.coords.x || d.y !== a.coords.y;
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
            s = i.delta;
        "start" === n && (0, j["default"])(this.startDelta, i.delta);

        for (var l = [[o, a], [r, s]], u = 0; u < l.length; u++) {
          var c = Pe(l[u], 2),
              f = c[0],
              d = c[1];
          f.page.x += d.x, f.page.y += d.y, f.client.x += d.x, f.client.y += d.y;
        }

        var p = this.result.rectDelta,
            v = t.rect || e.rect;
        v.left += p.left, v.right += p.right, v.top += p.top, v.bottom += p.bottom, v.width = v.right - v.left, v.height = v.bottom - v.top;
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
        if (this.result = i, !i.changed && (!o || o < this.states.length) && e.interacting()) return !1;

        if (t.modifiedCoords) {
          var a = e.coords.cur.page,
              s = {
            x: t.modifiedCoords.x - a.x,
            y: t.modifiedCoords.y - a.y
          };
          i.coords.x += s.x, i.coords.y += s.y, i.delta.x += s.x, i.delta.y += s.y;
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
            var a = r[i];
            t.state = a;
            var s = a.options,
                l = a.methods,
                u = l.beforeEnd && l.beforeEnd(t);
            if (u) return this.endResult = u, !1;
            o = o || !o && this.shouldDo(s, !0, t.phase, !0);
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
          var n = (0, j["default"])({
            states: this.states,
            interactable: e.interactable,
            element: e.element,
            rect: null
          }, t);
          this.fillArg(n);

          for (var r = 0; r < this.states.length; r++) {
            var o = this.states[r];
            n.state = o, o.methods.stop && o.methods.stop(n);
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
          this.states.push({
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
          for (var i = o.startDelta, a = o.result, s = a.delta, l = a.rectDelta, u = [[n.start, i], [n.cur, s]], c = 0; c < u.length; c++) {
            var f = Pe(u[c], 2),
                d = f[0],
                p = f[1];
            d.page.x -= p.x, d.page.y -= p.y, d.client.x -= p.x, d.client.y -= p.y;
          }

          r.left -= l.left, r.right -= l.right, r.top -= l.top, r.bottom -= l.bottom;
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
          return (0, _e["default"])(t);
        }), this.result = Me((0, j["default"])({}, t.result.coords), (0, j["default"])({}, t.result.rect));
      }
    }, {
      key: "destroy",
      value: function value() {
        for (var t in this) {
          this[t] = null;
        }
      }
    }]) && Ee(e.prototype, n), r && Ee(e, r), t;
  }();

  function Me(t, e) {
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

  function je(t, e) {
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

  Se["default"] = Te;
  var ke = {};

  function Ie(t) {
    var e = t.iEvent,
        n = t.interaction.modification.result;
    n && (e.modifiers = n.eventProps);
  }

  Object.defineProperty(ke, "__esModule", {
    value: !0
  }), ke.makeModifier = function (t, e) {
    var n = t.defaults,
        r = {
      start: t.start,
      set: t.set,
      beforeEnd: t.beforeEnd,
      stop: t.stop
    },
        o = function o(t) {
      var o = t || {};

      for (var i in o.enabled = !1 !== o.enabled, n) {
        i in o || (o[i] = n[i]);
      }

      var a = {
        options: o,
        methods: r,
        name: e,
        enable: function enable() {
          return o.enabled = !0, a;
        },
        disable: function disable() {
          return o.enabled = !1, a;
        }
      };
      return a;
    };

    e && "string" == typeof e && (o._defaults = n, o._methods = r);
    return o;
  }, ke.addEventModifiers = Ie, ke["default"] = void 0;
  var De = {
    id: "modifiers/base",
    before: ["actions"],
    install: function install(t) {
      t.defaults.perAction.modifiers = [];
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        var e = t.interaction;
        e.modification = new Se["default"](e);
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
      "interactions:action-start": Ie,
      "interactions:action-move": Ie,
      "interactions:action-end": Ie,
      "interactions:after-action-start": function interactionsAfterActionStart(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:after-action-move": function interactionsAfterActionMove(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:stop": function interactionsStop(t) {
        return t.interaction.modification.stop(t);
      }
    }
  };
  ke["default"] = De;
  var Ae = {};
  Object.defineProperty(Ae, "__esModule", {
    value: !0
  }), Ae.defaults = void 0;
  Ae.defaults = {
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
  };
  var ze = {};

  function Ce(t) {
    return (Ce = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function Re(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Fe(t, e) {
    return (Fe = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function Xe(t) {
    var e = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }();

    return function () {
      var n,
          r = Le(t);

      if (e) {
        var o = Le(this).constructor;
        n = Reflect.construct(r, arguments, o);
      } else n = r.apply(this, arguments);

      return Ye(this, n);
    };
  }

  function Ye(t, e) {
    return !e || "object" !== Ce(e) && "function" != typeof e ? We(t) : e;
  }

  function We(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function Le(t) {
    return (Le = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  Object.defineProperty(ze, "__esModule", {
    value: !0
  }), ze.InteractEvent = void 0;

  var Be = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && Fe(t, e);
    }(i, t);
    var e,
        n,
        r,
        o = Xe(i);

    function i(t, e, n, r, a, s, l) {
      var u;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, i), (u = o.call(this, t)).target = void 0, u.currentTarget = void 0, u.relatedTarget = null, u.screenX = void 0, u.screenY = void 0, u.button = void 0, u.buttons = void 0, u.ctrlKey = void 0, u.shiftKey = void 0, u.altKey = void 0, u.metaKey = void 0, u.page = void 0, u.client = void 0, u.delta = void 0, u.rect = void 0, u.x0 = void 0, u.y0 = void 0, u.t0 = void 0, u.dt = void 0, u.duration = void 0, u.clientX0 = void 0, u.clientY0 = void 0, u.velocity = void 0, u.speed = void 0, u.swipe = void 0, u.timeStamp = void 0, u.dragEnter = void 0, u.dragLeave = void 0, u.axes = void 0, u.preEnd = void 0, a = a || t.element;
      var c = t.interactable,
          f = (c && c.options || Ae.defaults).deltaSource,
          d = (0, A["default"])(c, a, n),
          p = "start" === r,
          v = "end" === r,
          h = p ? We(u) : t.prevEvent,
          g = p ? t.coords.start : v ? {
        page: h.page,
        client: h.client,
        timeStamp: t.coords.cur.timeStamp
      } : t.coords.cur;
      return u.page = (0, j["default"])({}, g.page), u.client = (0, j["default"])({}, g.client), u.rect = (0, j["default"])({}, t.rect), u.timeStamp = g.timeStamp, v || (u.page.x -= d.x, u.page.y -= d.y, u.client.x -= d.x, u.client.y -= d.y), u.ctrlKey = e.ctrlKey, u.altKey = e.altKey, u.shiftKey = e.shiftKey, u.metaKey = e.metaKey, u.button = e.button, u.buttons = e.buttons, u.target = a, u.currentTarget = a, u.preEnd = s, u.type = l || n + (r || ""), u.interactable = c, u.t0 = p ? t.pointers[t.pointers.length - 1].downTime : h.t0, u.x0 = t.coords.start.page.x - d.x, u.y0 = t.coords.start.page.y - d.y, u.clientX0 = t.coords.start.client.x - d.x, u.clientY0 = t.coords.start.client.y - d.y, u.delta = p || v ? {
        x: 0,
        y: 0
      } : {
        x: u[f].x - h[f].x,
        y: u[f].y - h[f].y
      }, u.dt = t.coords.delta.timeStamp, u.duration = u.timeStamp - u.t0, u.velocity = (0, j["default"])({}, t.coords.velocity[f]), u.speed = (0, R["default"])(u.velocity.x, u.velocity.y), u.swipe = v || "inertiastart" === r ? u.getSwipe() : null, u;
    }

    return e = i, (n = [{
      key: "getSwipe",
      value: function value() {
        var t = this._interaction;
        if (t.prevEvent.speed < 600 || this.timeStamp - t.prevEvent.timeStamp > 150) return null;
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
    }]) && Re(e.prototype, n), r && Re(e, r), i;
  }($.BaseEvent);

  ze.InteractEvent = Be, Object.defineProperties(Be.prototype, {
    pageX: {
      get: function get() {
        return this.page.x;
      },
      set: function set(t) {
        this.page.x = t;
      }
    },
    pageY: {
      get: function get() {
        return this.page.y;
      },
      set: function set(t) {
        this.page.y = t;
      }
    },
    clientX: {
      get: function get() {
        return this.client.x;
      },
      set: function set(t) {
        this.client.x = t;
      }
    },
    clientY: {
      get: function get() {
        return this.client.y;
      },
      set: function set(t) {
        this.client.y = t;
      }
    },
    dx: {
      get: function get() {
        return this.delta.x;
      },
      set: function set(t) {
        this.delta.x = t;
      }
    },
    dy: {
      get: function get() {
        return this.delta.y;
      },
      set: function set(t) {
        this.delta.y = t;
      }
    },
    velocityX: {
      get: function get() {
        return this.velocity.x;
      },
      set: function set(t) {
        this.velocity.x = t;
      }
    },
    velocityY: {
      get: function get() {
        return this.velocity.y;
      },
      set: function set(t) {
        this.velocity.y = t;
      }
    }
  });
  var Ue = {};
  Object.defineProperty(Ue, "__esModule", {
    value: !0
  }), Ue.PointerInfo = void 0;

  Ue.PointerInfo = function t(e, n, r, o, i) {
    !function (t, e) {
      if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
    }(this, t), this.id = void 0, this.pointer = void 0, this.event = void 0, this.downTime = void 0, this.downTarget = void 0, this.id = e, this.pointer = n, this.event = r, this.downTime = o, this.downTarget = i;
  };

  var Ne,
      Ve,
      qe = {};

  function $e(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Ge(t, e, n) {
    return e && $e(t.prototype, e), n && $e(t, n), t;
  }

  Object.defineProperty(qe, "__esModule", {
    value: !0
  }), Object.defineProperty(qe, "PointerInfo", {
    enumerable: !0,
    get: function get() {
      return Ue.PointerInfo;
    }
  }), qe["default"] = qe.Interaction = qe._ProxyMethods = qe._ProxyValues = void 0, qe._ProxyValues = Ne, function (t) {
    t.interactable = "", t.element = "", t.prepared = "", t.pointerIsDown = "", t.pointerWasMoved = "", t._proxy = "";
  }(Ne || (qe._ProxyValues = Ne = {})), qe._ProxyMethods = Ve, function (t) {
    t.start = "", t.move = "", t.end = "", t.stop = "", t.interacting = "";
  }(Ve || (qe._ProxyMethods = Ve = {}));

  var He = 0,
      Ke = function () {
    function t(e) {
      var n = this,
          r = e.pointerType,
          o = e.scopeFire;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.interactable = null, this.element = null, this.rect = void 0, this._rects = void 0, this.edges = void 0, this._scopeFire = void 0, this.prepared = {
        name: null,
        axis: null,
        edges: null
      }, this.pointerType = void 0, this.pointers = [], this.downEvent = null, this.downPointer = {}, this._latestPointer = {
        pointer: null,
        event: null,
        eventTarget: null
      }, this.prevEvent = null, this.pointerIsDown = !1, this.pointerWasMoved = !1, this._interacting = !1, this._ending = !1, this._stopped = !0, this._proxy = null, this.simulation = null, this.doMove = (0, Xt.warnOnce)(function (t) {
        this.move(t);
      }, "The interaction.doMove() method has been renamed to interaction.move()"), this.coords = {
        start: W.newCoords(),
        prev: W.newCoords(),
        cur: W.newCoords(),
        delta: W.newCoords(),
        velocity: W.newCoords()
      }, this._id = He++, this._scopeFire = o, this.pointerType = r;
      var i = this;
      this._proxy = {};

      var a = function a(t) {
        Object.defineProperty(n._proxy, t, {
          get: function get() {
            return i[t];
          }
        });
      };

      for (var s in Ne) {
        a(s);
      }

      var l = function l(t) {
        Object.defineProperty(n._proxy, t, {
          value: function value() {
            return i[t].apply(i, arguments);
          }
        });
      };

      for (var u in Ve) {
        l(u);
      }

      this._scopeFire("interactions:new", {
        interaction: this
      });
    }

    return Ge(t, [{
      key: "pointerMoveTolerance",
      get: function get() {
        return 1;
      }
    }]), Ge(t, [{
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
        return !(this.interacting() || !this.pointerIsDown || this.pointers.length < ("gesture" === t.name ? 2 : 1) || !e.options[t.name].enabled) && ((0, Xt.copyAction)(this.prepared, t), this.interactable = e, this.element = n, this.rect = e.getRect(n), this.edges = this.prepared.edges ? (0, j["default"])({}, this.prepared.edges) : {
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
        this.pointerIsDown && !this.pointerWasMoved && (r = this.coords.cur.client.x - this.coords.start.client.x, o = this.coords.cur.client.y - this.coords.start.client.y, this.pointerWasMoved = (0, R["default"])(r, o) > this.pointerMoveTolerance);
        var a = this.getPointerIndex(t),
            s = {
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
        i || W.setCoordVelocity(this.coords.velocity, this.coords.delta), this._scopeFire("interactions:move", s), i || this.simulation || (this.interacting() && (s.type = null, this.move(s)), this.pointerWasMoved && W.copyCoords(this.coords.prev, this.coords.cur));
      }
    }, {
      key: "move",
      value: function value(t) {
        t && t.event || W.setZeroCoords(this.coords.delta), (t = (0, j["default"])({
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
        }), this.simulation || this.end(e), this.removePointer(t, e);
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
        })), this._ending = !1, !0 === e && this.stop();
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
        var e = W.getPointerId(t);
        return "mouse" === this.pointerType || "pen" === this.pointerType ? this.pointers.length - 1 : K.findIndex(this.pointers, function (t) {
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
        var o = W.getPointerId(t),
            i = this.getPointerIndex(t),
            a = this.pointers[i];
        return r = !1 !== r && (r || /(down|start)$/i.test(e.type)), a ? a.pointer = t : (a = new Ue.PointerInfo(o, t, e, null, null), i = this.pointers.length, this.pointers.push(a)), W.setCoords(this.coords.cur, this.pointers.map(function (t) {
          return t.pointer;
        }), this._now()), W.setCoordDeltas(this.coords.delta, this.coords.prev, this.coords.cur), r && (this.pointerIsDown = !0, a.downTime = this.coords.cur.timeStamp, a.downTarget = n, W.pointerExtend(this.downPointer, t), this.interacting() || (W.copyCoords(this.coords.start, this.coords.cur), W.copyCoords(this.coords.prev, this.coords.cur), this.downEvent = e, this.pointerWasMoved = !1)), this._updateLatestPointer(t, e, n), this._scopeFire("interactions:update-pointer", {
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
          }), this.pointers.splice(n, 1), this.pointerIsDown = !1;
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
        return new ze.InteractEvent(this, t, this.prepared.name, e, this.element, n, r);
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
        if (i && "move" === n && (k.addEdges(this.edges, i, this.coords.delta[this.interactable.options.deltaSource]), i.width = i.right - i.left, i.height = i.bottom - i.top), !1 === this._scopeFire("interactions:before-action-".concat(n), t)) return !1;

        var a = t.iEvent = this._createPreparedEvent(e, n, r, o);

        return this._scopeFire("interactions:action-".concat(n), t), "start" === n && (this.prevEvent = a), this._fireEvent(a), this._scopeFire("interactions:after-action-".concat(n), t), !0;
      }
    }, {
      key: "_now",
      value: function value() {
        return Date.now();
      }
    }]), t;
  }();

  qe.Interaction = Ke;
  var Ze = Ke;
  qe["default"] = Ze;
  var Je = {};

  function Qe(t) {
    t.pointerIsDown && (rn(t.coords.cur, t.offset.total), t.offset.pending.x = 0, t.offset.pending.y = 0);
  }

  function tn(t) {
    en(t.interaction);
  }

  function en(t) {
    if (!function (t) {
      return !(!t.offset.pending.x && !t.offset.pending.y);
    }(t)) return !1;
    var e = t.offset.pending;
    return rn(t.coords.cur, e), rn(t.coords.delta, e), k.addEdges(t.edges, t.rect, e), e.x = 0, e.y = 0, !0;
  }

  function nn(t) {
    var e = t.x,
        n = t.y;
    this.offset.pending.x += e, this.offset.pending.y += n, this.offset.total.x += e, this.offset.total.y += n;
  }

  function rn(t, e) {
    var n = t.page,
        r = t.client,
        o = e.x,
        i = e.y;
    n.x += o, n.y += i, r.x += o, r.y += i;
  }

  Object.defineProperty(Je, "__esModule", {
    value: !0
  }), Je.addTotal = Qe, Je.applyPending = en, Je["default"] = void 0, qe._ProxyMethods.offsetBy = "";
  var on = {
    id: "offset",
    before: ["modifiers", "pointer-events", "actions", "inertia"],
    install: function install(t) {
      t.Interaction.prototype.offsetBy = nn;
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
        return Qe(t.interaction);
      },
      "interactions:before-action-start": tn,
      "interactions:before-action-move": tn,
      "interactions:before-action-end": function interactionsBeforeActionEnd(t) {
        var e = t.interaction;
        if (en(e)) return e.move({
          offset: !0
        }), e.end(), !1;
      },
      "interactions:stop": function interactionsStop(t) {
        var e = t.interaction;
        e.offset.total.x = 0, e.offset.total.y = 0, e.offset.pending.x = 0, e.offset.pending.y = 0;
      }
    }
  };
  Je["default"] = on;
  var an = {};

  function sn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  Object.defineProperty(an, "__esModule", {
    value: !0
  }), an["default"] = an.InertiaState = void 0;

  var ln = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.active = !1, this.isModified = !1, this.smoothEnd = !1, this.allowResume = !1, this.modification = null, this.modifierCount = 0, this.modifierArg = null, this.startCoords = null, this.t0 = 0, this.v0 = 0, this.te = 0, this.targetOffset = null, this.modifiedOffset = null, this.currentOffset = null, this.lambda_v0 = 0, this.one_ve_v0 = 0, this.timeout = null, this.interaction = void 0, this.interaction = e;
    }

    var e, n, r;
    return e = t, (n = [{
      key: "start",
      value: function value(t) {
        var e = this.interaction,
            n = un(e);
        if (!n || !n.enabled) return !1;
        var r = e.coords.velocity.client,
            o = (0, R["default"])(r.x, r.y),
            i = this.modification || (this.modification = new Se["default"](e));
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
            n = un(this.interaction),
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
        }), this.onNextFrame(function () {
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
        }, this.onNextFrame(function () {
          return t.smoothEndTick();
        });
      }
    }, {
      key: "onNextFrame",
      value: function value(t) {
        var e = this;
        this.timeout = Mt["default"].request(function () {
          e.active && t();
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
            s = this,
            l = this.interaction,
            u = un(l).resistance,
            c = (l._now() - this.t0) / 1e3;

        if (c < this.te) {
          var f,
              d = 1 - (Math.exp(-u * c) - this.lambda_v0) / this.one_ve_v0;
          this.isModified ? (t = 0, e = 0, n = this.targetOffset.x, r = this.targetOffset.y, o = this.modifiedOffset.x, i = this.modifiedOffset.y, f = {
            x: cn(a = d, t, n, o),
            y: cn(a, e, r, i)
          }) : f = {
            x: this.targetOffset.x * d,
            y: this.targetOffset.y * d
          };
          var p = {
            x: f.x - this.currentOffset.x,
            y: f.y - this.currentOffset.y
          };
          this.currentOffset.x += p.x, this.currentOffset.y += p.y, l.offsetBy(p), l.move(), this.onNextFrame(function () {
            return s.inertiaTick();
          });
        } else l.offsetBy({
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
            r = un(e).smoothEndDuration;

        if (n < r) {
          var o = {
            x: fn(n, 0, this.targetOffset.x, r),
            y: fn(n, 0, this.targetOffset.y, r)
          },
              i = {
            x: o.x - this.currentOffset.x,
            y: o.y - this.currentOffset.y
          };
          this.currentOffset.x += i.x, this.currentOffset.y += i.y, e.offsetBy(i), e.move({
            skipModifiers: this.modifierCount
          }), this.onNextFrame(function () {
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
        }), (0, W.copyCoords)(o.coords.prev, o.coords.cur), this.stop();
      }
    }, {
      key: "end",
      value: function value() {
        this.interaction.move(), this.interaction.end(), this.stop();
      }
    }, {
      key: "stop",
      value: function value() {
        this.active = this.smoothEnd = !1, this.interaction.simulation = null, Mt["default"].cancel(this.timeout);
      }
    }]) && sn(e.prototype, n), r && sn(e, r), t;
  }();

  function un(t) {
    var e = t.interactable,
        n = t.prepared;
    return e && e.options && n.name && e.options[n.name].inertia;
  }

  function cn(t, e, n, r) {
    var o = 1 - t;
    return o * o * e + 2 * o * t * n + t * t * r;
  }

  function fn(t, e, n, r) {
    return -n * (t /= r) * (t - 2) + e;
  }

  an.InertiaState = ln;
  var dn = {
    id: "inertia",
    before: ["modifiers", "actions"],
    install: function install(t) {
      var e = t.defaults;
      t.usePlugin(Je["default"]), t.usePlugin(ke["default"]), t.actions.phases.inertiastart = !0, t.actions.phases.resume = !0, e.perAction.inertia = {
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
        e.inertia = new ln(e);
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
        if (r.active) for (var o = n; i["default"].element(o);) {
          if (o === e.element) {
            r.resume(t);
            break;
          }

          o = _.parentNode(o);
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
      "interactions:action-resume": ke.addEventModifiers,
      "interactions:action-inertiastart": ke.addEventModifiers,
      "interactions:after-action-inertiastart": function interactionsAfterActionInertiastart(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:after-action-resume": function interactionsAfterActionResume(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      }
    }
  };
  an["default"] = dn;
  var pn = {};

  function vn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function hn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      if (t.immediatePropagationStopped) break;
      r(t);
    }
  }

  Object.defineProperty(pn, "__esModule", {
    value: !0
  }), pn.Eventable = void 0;

  var gn = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.options = void 0, this.types = {}, this.propagationStopped = !1, this.immediatePropagationStopped = !1, this.global = void 0, this.options = (0, j["default"])({}, e || {});
    }

    var e, n, r;
    return e = t, (n = [{
      key: "fire",
      value: function value(t) {
        var e,
            n = this.global;
        (e = this.types[t.type]) && hn(t, e), !t.propagationStopped && n && (e = n[t.type]) && hn(t, e);
      }
    }, {
      key: "on",
      value: function value(t, e) {
        var n = (0, z["default"])(t, e);

        for (t in n) {
          this.types[t] = K.merge(this.types[t] || [], n[t]);
        }
      }
    }, {
      key: "off",
      value: function value(t, e) {
        var n = (0, z["default"])(t, e);

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
      value: function value(t) {
        return null;
      }
    }]) && vn(e.prototype, n), r && vn(e, r), t;
  }();

  pn.Eventable = gn;
  var yn = {};
  Object.defineProperty(yn, "__esModule", {
    value: !0
  }), yn["default"] = function (t, e) {
    if (e.phaselessTypes[t]) return !0;

    for (var n in e.map) {
      if (0 === t.indexOf(n) && t.substr(n.length) in e.phases) return !0;
    }

    return !1;
  };
  var mn = {};

  function bn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function xn(t, e, n) {
    return e && bn(t.prototype, e), n && bn(t, n), t;
  }

  Object.defineProperty(mn, "__esModule", {
    value: !0
  }), mn.Interactable = void 0;

  var wn = function () {
    function t(n, r, o, i) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.options = void 0, this._actions = void 0, this.target = void 0, this.events = new pn.Eventable(), this._context = void 0, this._win = void 0, this._doc = void 0, this._scopeEvents = void 0, this._rectChecker = void 0, this._actions = r.actions, this.target = n, this._context = r.context || o, this._win = (0, e.getWindow)((0, _.trySelector)(n) ? this._context : n), this._doc = this._win.document, this._scopeEvents = i, this.set(r);
    }

    return xn(t, [{
      key: "_defaults",
      get: function get() {
        return {
          base: {},
          perAction: {},
          actions: {}
        };
      }
    }]), xn(t, [{
      key: "setOnEvents",
      value: function value(t, e) {
        return i["default"].func(e.onstart) && this.on("".concat(t, "start"), e.onstart), i["default"].func(e.onmove) && this.on("".concat(t, "move"), e.onmove), i["default"].func(e.onend) && this.on("".concat(t, "end"), e.onend), i["default"].func(e.oninertiastart) && this.on("".concat(t, "inertiastart"), e.oninertiastart), this;
      }
    }, {
      key: "updatePerActionListeners",
      value: function value(t, e, n) {
        (i["default"].array(e) || i["default"].object(e)) && this.off(t, e), (i["default"].array(n) || i["default"].object(n)) && this.on(t, n);
      }
    }, {
      key: "setPerAction",
      value: function value(t, e) {
        var n = this._defaults;

        for (var r in e) {
          var o = r,
              a = this.options[t],
              s = e[o];
          "listeners" === o && this.updatePerActionListeners(t, a.listeners, s), i["default"].array(s) ? a[o] = K.from(s) : i["default"].plainObject(s) ? (a[o] = (0, j["default"])(a[o] || {}, (0, _e["default"])(s)), i["default"].object(n.perAction[o]) && "enabled" in n.perAction[o] && (a[o].enabled = !1 !== s.enabled)) : i["default"].bool(s) && i["default"].object(n.perAction[o]) ? a[o].enabled = s : a[o] = s;
        }
      }
    }, {
      key: "getRect",
      value: function value(t) {
        return t = t || (i["default"].element(this.target) ? this.target : null), i["default"].string(this.target) && (t = t || this._context.querySelector(this.target)), (0, _.getElementRect)(t);
      }
    }, {
      key: "rectChecker",
      value: function value(t) {
        var e = this;
        return i["default"].func(t) ? (this._rectChecker = t, this.getRect = function (t) {
          var n = (0, j["default"])({}, e._rectChecker(t));
          return "width" in n || (n.width = n.right - n.left, n.height = n.bottom - n.top), n;
        }, this) : null === t ? (delete this.getRect, delete this._rectChecker, this) : this.getRect;
      }
    }, {
      key: "_backCompatOption",
      value: function value(t, e) {
        if ((0, _.trySelector)(e) || i["default"].object(e)) {
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
        return this._context === t.ownerDocument || (0, _.nodeContains)(this._context, t);
      }
    }, {
      key: "testIgnoreAllow",
      value: function value(t, e, n) {
        return !this.testIgnore(t.ignoreFrom, e, n) && this.testAllow(t.allowFrom, e, n);
      }
    }, {
      key: "testAllow",
      value: function value(t, e, n) {
        return !t || !!i["default"].element(n) && (i["default"].string(t) ? (0, _.matchesUpTo)(n, t, e) : !!i["default"].element(t) && (0, _.nodeContains)(t, n));
      }
    }, {
      key: "testIgnore",
      value: function value(t, e, n) {
        return !(!t || !i["default"].element(n)) && (i["default"].string(t) ? (0, _.matchesUpTo)(n, t, e) : !!i["default"].element(t) && (0, _.nodeContains)(t, n));
      }
    }, {
      key: "fire",
      value: function value(t) {
        return this.events.fire(t), this;
      }
    }, {
      key: "_onOff",
      value: function value(t, e, n, r) {
        i["default"].object(e) && !i["default"].array(e) && (r = n, n = null);
        var o = "on" === t ? "add" : "remove",
            a = (0, z["default"])(e, n);

        for (var s in a) {
          "wheel" === s && (s = b["default"].wheelEvent);

          for (var l = 0; l < a[s].length; l++) {
            var u = a[s][l];
            (0, yn["default"])(s, this._actions) ? this.events[t](s, u) : i["default"].string(this.target) ? this._scopeEvents["".concat(o, "Delegate")](this.target, this._context, s, u, r) : this._scopeEvents[o](this.target, s, u, r);
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

        for (var n in i["default"].object(t) || (t = {}), this.options = (0, _e["default"])(e.base), this._actions.methodDict) {
          var r = n,
              o = this._actions.methodDict[r];
          this.options[r] = {}, this.setPerAction(r, (0, j["default"])((0, j["default"])({}, e.perAction), e.actions[r])), this[o](t[r]);
        }

        for (var a in t) {
          i["default"].func(this[a]) && this[a](t[a]);
        }

        return this;
      }
    }, {
      key: "unset",
      value: function value() {
        if (i["default"].string(this.target)) for (var t in this._scopeEvents.delegatedEvents) {
          for (var e = this._scopeEvents.delegatedEvents[t], n = e.length - 1; n >= 0; n--) {
            var r = e[n],
                o = r.selector,
                a = r.context,
                s = r.listeners;
            o === this.target && a === this._context && e.splice(n, 1);

            for (var l = s.length - 1; l >= 0; l--) {
              this._scopeEvents.removeDelegate(this.target, this._context, t, s[l][0], s[l][1]);
            }
          }
        } else this._scopeEvents.remove(this.target, "all");
      }
    }]), t;
  }();

  mn.Interactable = wn;
  var _n = {};

  function Sn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  Object.defineProperty(_n, "__esModule", {
    value: !0
  }), _n.InteractableSet = void 0;

  var Pn = function () {
    function t(e) {
      var n = this;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.list = [], this.selectorMap = {}, this.scope = void 0, this.scope = e, e.addListeners({
        "interactable:unset": function interactableUnset(t) {
          var e = t.interactable,
              r = e.target,
              o = e._context,
              a = i["default"].string(r) ? n.selectorMap[r] : r[n.scope.id],
              s = K.findIndex(a, function (t) {
            return t.context === o;
          });
          a[s] && (a[s].context = null, a[s].interactable = null), a.splice(s, 1);
        }
      });
    }

    var e, n, r;
    return e = t, (n = [{
      key: "new",
      value: function value(t, e) {
        e = (0, j["default"])(e || {}, {
          actions: this.scope.actions
        });
        var n = new this.scope.Interactable(t, e, this.scope.document, this.scope.events),
            r = {
          context: n._context,
          interactable: n
        };
        return this.scope.addDocument(n._doc), this.list.push(n), i["default"].string(t) ? (this.selectorMap[t] || (this.selectorMap[t] = []), this.selectorMap[t].push(r)) : (n.target[this.scope.id] || Object.defineProperty(t, this.scope.id, {
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
      value: function value(t, e) {
        var n = e && e.context || this.scope.document,
            r = i["default"].string(t),
            o = r ? this.selectorMap[t] : t[this.scope.id];
        if (!o) return null;
        var a = K.find(o, function (e) {
          return e.context === n && (r || e.interactable.inContext(t));
        });
        return a && a.interactable;
      }
    }, {
      key: "forEachMatch",
      value: function value(t, e) {
        for (var n = 0; n < this.list.length; n++) {
          var r = this.list[n],
              o = void 0;
          if ((i["default"].string(r.target) ? i["default"].element(t) && _.matchesSelector(t, r.target) : t === r.target) && r.inContext(t) && (o = e(r)), void 0 !== o) return o;
        }
      }
    }]) && Sn(e.prototype, n), r && Sn(e, r), t;
  }();

  _n.InteractableSet = Pn;
  var On = {};

  function En(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Tn(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" == typeof Symbol || !(Symbol.iterator in Object(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, s = t[Symbol.iterator](); !(r = (a = s.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == s["return"] || s["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return Mn(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(t);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Mn(t, e);
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function Mn(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(On, "__esModule", {
    value: !0
  }), On["default"] = void 0;

  var jn = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), this.currentTarget = void 0, this.originalEvent = void 0, this.type = void 0, this.originalEvent = e, (0, F["default"])(this, e);
    }

    var e, n, r;
    return e = t, (n = [{
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
    }]) && En(e.prototype, n), r && En(e, r), t;
  }();

  function kn(t) {
    if (!i["default"].object(t)) return {
      capture: !!t,
      passive: !1
    };
    var e = (0, j["default"])({}, t);
    return e.capture = !!t.capture, e.passive = !!t.passive, e;
  }

  var In = {
    id: "events",
    install: function install(t) {
      var e = [],
          n = {},
          r = [],
          o = {
        add: a,
        remove: s,
        addDelegate: function addDelegate(t, e, o, i, s) {
          var c = kn(s);

          if (!n[o]) {
            n[o] = [];

            for (var f = 0; f < r.length; f++) {
              var d = r[f];
              a(d, o, l), a(d, o, u, !0);
            }
          }

          var p = n[o],
              v = K.find(p, function (n) {
            return n.selector === t && n.context === e;
          });
          v || (v = {
            selector: t,
            context: e,
            listeners: []
          }, p.push(v));
          v.listeners.push([i, c]);
        },
        removeDelegate: function removeDelegate(t, e, r, o, i) {
          var a,
              c = kn(i),
              f = n[r],
              d = !1;
          if (!f) return;

          for (a = f.length - 1; a >= 0; a--) {
            var p = f[a];

            if (p.selector === t && p.context === e) {
              for (var v = p.listeners, h = v.length - 1; h >= 0; h--) {
                var g = Tn(v[h], 2),
                    y = g[0],
                    m = g[1],
                    b = m.capture,
                    x = m.passive;

                if (y === o && b === c.capture && x === c.passive) {
                  v.splice(h, 1), v.length || (f.splice(a, 1), s(e, r, l), s(e, r, u, !0)), d = !0;
                  break;
                }
              }

              if (d) break;
            }
          }
        },
        delegateListener: l,
        delegateUseCapture: u,
        delegatedEvents: n,
        documents: r,
        targets: e,
        supportsOptions: !1,
        supportsPassive: !1
      };

      function a(t, n, r, i) {
        var a = kn(i),
            s = K.find(e, function (e) {
          return e.eventTarget === t;
        });
        s || (s = {
          eventTarget: t,
          events: {}
        }, e.push(s)), s.events[n] || (s.events[n] = []), t.addEventListener && !K.contains(s.events[n], r) && (t.addEventListener(n, r, o.supportsOptions ? a : a.capture), s.events[n].push(r));
      }

      function s(t, n, r, i) {
        var a = kn(i),
            l = K.findIndex(e, function (e) {
          return e.eventTarget === t;
        }),
            u = e[l];
        if (u && u.events) if ("all" !== n) {
          var c = !1,
              f = u.events[n];

          if (f) {
            if ("all" === r) {
              for (var d = f.length - 1; d >= 0; d--) {
                s(t, n, f[d], a);
              }

              return;
            }

            for (var p = 0; p < f.length; p++) {
              if (f[p] === r) {
                t.removeEventListener(n, r, o.supportsOptions ? a : a.capture), f.splice(p, 1), 0 === f.length && (delete u.events[n], c = !0);
                break;
              }
            }
          }

          c && !Object.keys(u.events).length && e.splice(l, 1);
        } else for (n in u.events) {
          u.events.hasOwnProperty(n) && s(t, n, "all");
        }
      }

      function l(t, e) {
        for (var r = kn(e), o = new jn(t), a = n[t.type], s = Tn(W.getEventTargets(t), 1)[0], l = s; i["default"].element(l);) {
          for (var u = 0; u < a.length; u++) {
            var c = a[u],
                f = c.selector,
                d = c.context;

            if (_.matchesSelector(l, f) && _.nodeContains(d, s) && _.nodeContains(d, l)) {
              var p = c.listeners;
              o.currentTarget = l;

              for (var v = 0; v < p.length; v++) {
                var h = Tn(p[v], 2),
                    g = h[0],
                    y = h[1],
                    m = y.capture,
                    b = y.passive;
                m === r.capture && b === r.passive && g(o);
              }
            }
          }

          l = _.parentNode(l);
        }
      }

      function u(t) {
        return l(t, !0);
      }

      return t.document.createElement("div").addEventListener("test", null, {
        get capture() {
          return o.supportsOptions = !0;
        },

        get passive() {
          return o.supportsPassive = !0;
        }

      }), t.events = o, o;
    }
  };
  On["default"] = In;
  var Dn = {};
  Object.defineProperty(Dn, "__esModule", {
    value: !0
  }), Dn.createInteractStatic = function (t) {
    var e = function e(n, r) {
      var o = t.interactables.get(n, r);
      return o || ((o = t.interactables["new"](n, r)).events.global = e.globalEvents), o;
    };

    return e.getPointerAverage = W.pointerAverage, e.getTouchBBox = W.touchBBox, e.getTouchDistance = W.touchDistance, e.getTouchAngle = W.touchAngle, e.getElementRect = _.getElementRect, e.getElementClientRect = _.getElementClientRect, e.matchesSelector = _.matchesSelector, e.closest = _.closest, e.globalEvents = {}, e.version = void 0, e.scope = t, e.use = function (t, e) {
      return this.scope.usePlugin(t, e), this;
    }, e.isSet = function (t, e) {
      return !!this.scope.interactables.get(t, e && e.context);
    }, e.on = function (t, e, n) {
      if (i["default"].string(t) && -1 !== t.search(" ") && (t = t.trim().split(/ +/)), i["default"].array(t)) {
        for (var r = 0; r < t.length; r++) {
          var o = t[r];
          this.on(o, e, n);
        }

        return this;
      }

      if (i["default"].object(t)) {
        for (var a in t) {
          this.on(a, t[a], e);
        }

        return this;
      }

      return (0, yn["default"])(t, this.scope.actions) ? this.globalEvents[t] ? this.globalEvents[t].push(e) : this.globalEvents[t] = [e] : this.scope.events.add(this.scope.document, t, e, {
        options: n
      }), this;
    }, e.off = function (t, e, n) {
      if (i["default"].string(t) && -1 !== t.search(" ") && (t = t.trim().split(/ +/)), i["default"].array(t)) {
        for (var r = 0; r < t.length; r++) {
          var o = t[r];
          this.off(o, e, n);
        }

        return this;
      }

      if (i["default"].object(t)) {
        for (var a in t) {
          this.off(a, t[a], e);
        }

        return this;
      }

      var s;
      (0, yn["default"])(t, this.scope.actions) ? t in this.globalEvents && -1 !== (s = this.globalEvents[t].indexOf(e)) && this.globalEvents[t].splice(s, 1) : this.scope.events.remove(this.scope.document, t, e, n);
      return this;
    }, e.debug = function () {
      return this.scope;
    }, e.supportsTouch = function () {
      return b["default"].supportsTouch;
    }, e.supportsPointerEvent = function () {
      return b["default"].supportsPointerEvent;
    }, e.stop = function () {
      for (var t = 0; t < this.scope.interactions.list.length; t++) {
        this.scope.interactions.list[t].stop();
      }

      return this;
    }, e.pointerMoveTolerance = function (t) {
      return i["default"].number(t) ? (this.scope.interactions.pointerMoveTolerance = t, this) : this.scope.interactions.pointerMoveTolerance;
    }, e.addDocument = function (t, e) {
      this.scope.addDocument(t, e);
    }, e.removeDocument = function (t) {
      this.scope.removeDocument(t);
    }, e;
  };
  var An = {};
  Object.defineProperty(An, "__esModule", {
    value: !0
  }), An["default"] = void 0;
  var zn = {
    methodOrder: ["simulationResume", "mouseOrPen", "hasPointer", "idle"],
    search: function search(t) {
      for (var e = 0; e < zn.methodOrder.length; e++) {
        var n;
        n = zn.methodOrder[e];
        var r = zn[n](t);
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
            s = r;
        if (a.simulation && a.simulation.allowResume && a.pointerType === e) for (; s;) {
          if (s === a.element) return a;
          s = _.parentNode(s);
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
        var s = i.interactions.list[a];

        if (s.pointerType === r) {
          if (s.simulation && !Cn(s, n)) continue;
          if (s.interacting()) return s;
          e || (e = s);
        }
      }

      if (e) return e;

      for (var l = 0; l < i.interactions.list.length; l++) {
        var u = i.interactions.list[l];
        if (!(u.pointerType !== r || /down/i.test(o) && u.simulation)) return u;
      }

      return null;
    },
    hasPointer: function hasPointer(t) {
      for (var e = t.pointerId, n = t.scope, r = 0; r < n.interactions.list.length; r++) {
        var o = n.interactions.list[r];
        if (Cn(o, e)) return o;
      }

      return null;
    },
    idle: function idle(t) {
      for (var e = t.pointerType, n = t.scope, r = 0; r < n.interactions.list.length; r++) {
        var o = n.interactions.list[r];

        if (1 === o.pointers.length) {
          var i = o.interactable;
          if (i && (!i.options.gesture || !i.options.gesture.enabled)) continue;
        } else if (o.pointers.length >= 2) continue;

        if (!o.interacting() && e === o.pointerType) return o;
      }

      return null;
    }
  };

  function Cn(t, e) {
    return t.pointers.some(function (t) {
      return t.id === e;
    });
  }

  var Rn = zn;
  An["default"] = Rn;
  var Fn = {};

  function Xn(t) {
    return (Xn = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function Yn(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" == typeof Symbol || !(Symbol.iterator in Object(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, s = t[Symbol.iterator](); !(r = (a = s.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == s["return"] || s["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return Wn(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(t);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Wn(t, e);
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function Wn(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function Ln(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function Bn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Un(t, e) {
    return (Un = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function Nn(t) {
    var e = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }();

    return function () {
      var n,
          r = qn(t);

      if (e) {
        var o = qn(this).constructor;
        n = Reflect.construct(r, arguments, o);
      } else n = r.apply(this, arguments);

      return Vn(this, n);
    };
  }

  function Vn(t, e) {
    return !e || "object" !== Xn(e) && "function" != typeof e ? function (t) {
      if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      return t;
    }(t) : e;
  }

  function qn(t) {
    return (qn = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  Object.defineProperty(Fn, "__esModule", {
    value: !0
  }), Fn["default"] = void 0;
  var $n = ["pointerDown", "pointerMove", "pointerUp", "updatePointer", "removePointer", "windowBlur"];

  function Gn(t, e) {
    return function (n) {
      var r = e.interactions.list,
          o = W.getPointerType(n),
          i = Yn(W.getEventTargets(n), 2),
          a = i[0],
          s = i[1],
          l = [];

      if (/^touch/.test(n.type)) {
        e.prevTouchTime = e.now();

        for (var u = 0; u < n.changedTouches.length; u++) {
          var c = n.changedTouches[u],
              f = {
            pointer: c,
            pointerId: W.getPointerId(c),
            pointerType: o,
            eventType: n.type,
            eventTarget: a,
            curEventTarget: s,
            scope: e
          },
              d = Hn(f);
          l.push([f.pointer, f.eventTarget, f.curEventTarget, d]);
        }
      } else {
        var p = !1;

        if (!b["default"].supportsPointerEvent && /mouse/.test(n.type)) {
          for (var v = 0; v < r.length && !p; v++) {
            p = "mouse" !== r[v].pointerType && r[v].pointerIsDown;
          }

          p = p || e.now() - e.prevTouchTime < 500 || 0 === n.timeStamp;
        }

        if (!p) {
          var h = {
            pointer: n,
            pointerId: W.getPointerId(n),
            pointerType: o,
            eventType: n.type,
            curEventTarget: s,
            eventTarget: a,
            scope: e
          },
              g = Hn(h);
          l.push([h.pointer, h.eventTarget, h.curEventTarget, g]);
        }
      }

      for (var y = 0; y < l.length; y++) {
        var m = Yn(l[y], 4),
            x = m[0],
            w = m[1],
            _ = m[2];
        m[3][t](x, n, w, _);
      }
    };
  }

  function Hn(t) {
    var e = t.pointerType,
        n = t.scope,
        r = {
      interaction: An["default"].search(t),
      searchDetails: t
    };
    return n.fire("interactions:find", r), r.interaction || n.interactions["new"]({
      pointerType: e
    });
  }

  function Kn(t, e) {
    var n = t.doc,
        r = t.scope,
        o = t.options,
        i = r.interactions.docEvents,
        a = r.events,
        s = a[e];

    for (var l in r.browser.isIOS && !o.events && (o.events = {
      passive: !1
    }), a.delegatedEvents) {
      s(n, l, a.delegateListener), s(n, l, a.delegateUseCapture, !0);
    }

    for (var u = o && o.events, c = 0; c < i.length; c++) {
      var f = i[c];
      s(n, f.type, f.listener, u);
    }
  }

  var Zn = {
    id: "core/interactions",
    install: function install(t) {
      for (var e = {}, n = 0; n < $n.length; n++) {
        var r = $n[n];
        e[r] = Gn(r, t);
      }

      var o,
          i = b["default"].pEventTypes;

      function a() {
        for (var e = 0; e < t.interactions.list.length; e++) {
          var n = t.interactions.list[e];
          if (n.pointerIsDown && "touch" === n.pointerType && !n._interacting) for (var r = function r() {
            var e = n.pointers[o];
            t.documents.some(function (t) {
              var n = t.doc;
              return (0, _.nodeContains)(n, e.downTarget);
            }) || n.removePointer(e.pointer, e.event);
          }, o = 0; o < n.pointers.length; o++) {
            r();
          }
        }
      }

      (o = h["default"].PointerEvent ? [{
        type: i.down,
        listener: a
      }, {
        type: i.down,
        listener: e.pointerDown
      }, {
        type: i.move,
        listener: e.pointerMove
      }, {
        type: i.up,
        listener: e.pointerUp
      }, {
        type: i.cancel,
        listener: e.pointerUp
      }] : [{
        type: "mousedown",
        listener: e.pointerDown
      }, {
        type: "mousemove",
        listener: e.pointerMove
      }, {
        type: "mouseup",
        listener: e.pointerUp
      }, {
        type: "touchstart",
        listener: a
      }, {
        type: "touchstart",
        listener: e.pointerDown
      }, {
        type: "touchmove",
        listener: e.pointerMove
      }, {
        type: "touchend",
        listener: e.pointerUp
      }, {
        type: "touchcancel",
        listener: e.pointerUp
      }]).push({
        type: "blur",
        listener: function listener(e) {
          for (var n = 0; n < t.interactions.list.length; n++) {
            t.interactions.list[n].documentBlur(e);
          }
        }
      }), t.prevTouchTime = 0, t.Interaction = function (e) {
        !function (t, e) {
          if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
          t.prototype = Object.create(e && e.prototype, {
            constructor: {
              value: t,
              writable: !0,
              configurable: !0
            }
          }), e && Un(t, e);
        }(a, e);
        var n,
            r,
            o,
            i = Nn(a);

        function a() {
          return Ln(this, a), i.apply(this, arguments);
        }

        return n = a, (r = [{
          key: "_now",
          value: function value() {
            return t.now();
          }
        }, {
          key: "pointerMoveTolerance",
          get: function get() {
            return t.interactions.pointerMoveTolerance;
          },
          set: function set(e) {
            t.interactions.pointerMoveTolerance = e;
          }
        }]) && Bn(n.prototype, r), o && Bn(n, o), a;
      }(qe["default"]), t.interactions = {
        list: [],
        "new": function _new(e) {
          e.scopeFire = function (e, n) {
            return t.fire(e, n);
          };

          var n = new t.Interaction(e);
          return t.interactions.list.push(n), n;
        },
        listeners: e,
        docEvents: o,
        pointerMoveTolerance: 1
      }, t.usePlugin(se["default"]);
    },
    listeners: {
      "scope:add-document": function scopeAddDocument(t) {
        return Kn(t, "add");
      },
      "scope:remove-document": function scopeRemoveDocument(t) {
        return Kn(t, "remove");
      },
      "interactable:unset": function interactableUnset(t, e) {
        for (var n = t.interactable, r = e.interactions.list.length - 1; r >= 0; r--) {
          var o = e.interactions.list[r];
          o.interactable === n && (o.stop(), e.fire("interactions:destroy", {
            interaction: o
          }), o.destroy(), e.interactions.list.length > 2 && e.interactions.list.splice(r, 1));
        }
      }
    },
    onDocSignal: Kn,
    doOnInteractions: Gn,
    methodNames: $n
  };
  Fn["default"] = Zn;
  var Jn = {};

  function Qn(t) {
    return (Qn = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function tr(t, e, n) {
    return (tr = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (t, e, n) {
      var r = function (t, e) {
        for (; !Object.prototype.hasOwnProperty.call(t, e) && null !== (t = or(t));) {
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

  function er(t, e) {
    return (er = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function nr(t) {
    var e = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }();

    return function () {
      var n,
          r = or(t);

      if (e) {
        var o = or(this).constructor;
        n = Reflect.construct(r, arguments, o);
      } else n = r.apply(this, arguments);

      return rr(this, n);
    };
  }

  function rr(t, e) {
    return !e || "object" !== Qn(e) && "function" != typeof e ? function (t) {
      if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      return t;
    }(t) : e;
  }

  function or(t) {
    return (or = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function ir(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function ar(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function sr(t, e, n) {
    return e && ar(t.prototype, e), n && ar(t, n), t;
  }

  Object.defineProperty(Jn, "__esModule", {
    value: !0
  }), Jn.initScope = ur, Jn.Scope = void 0;

  var lr = function () {
    function t() {
      var e = this;
      ir(this, t), this.id = "__interact_scope_".concat(Math.floor(100 * Math.random())), this.isInitialized = !1, this.listenerMaps = [], this.browser = b["default"], this.defaults = (0, _e["default"])(Ae.defaults), this.Eventable = pn.Eventable, this.actions = {
        map: {},
        phases: {
          start: !0,
          move: !0,
          end: !0
        },
        methodDict: {},
        phaselessTypes: {}
      }, this.interactStatic = (0, Dn.createInteractStatic)(this), this.InteractEvent = ze.InteractEvent, this.Interactable = void 0, this.interactables = new _n.InteractableSet(this), this._win = void 0, this.document = void 0, this.window = void 0, this.documents = [], this._plugins = {
        list: [],
        map: {}
      }, this.onWindowUnload = function (t) {
        return e.removeDocument(t.target);
      };
      var n = this;

      this.Interactable = function (t) {
        !function (t, e) {
          if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
          t.prototype = Object.create(e && e.prototype, {
            constructor: {
              value: t,
              writable: !0,
              configurable: !0
            }
          }), e && er(t, e);
        }(r, t);
        var e = nr(r);

        function r() {
          return ir(this, r), e.apply(this, arguments);
        }

        return sr(r, [{
          key: "set",
          value: function value(t) {
            return tr(or(r.prototype), "set", this).call(this, t), n.fire("interactable:set", {
              options: t,
              interactable: this
            }), this;
          }
        }, {
          key: "unset",
          value: function value() {
            tr(or(r.prototype), "unset", this).call(this), n.interactables.list.splice(n.interactables.list.indexOf(this), 1), n.fire("interactable:unset", {
              interactable: this
            });
          }
        }, {
          key: "_defaults",
          get: function get() {
            return n.defaults;
          }
        }]), r;
      }(mn.Interactable);
    }

    return sr(t, [{
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
        return this.isInitialized ? this : ur(this, t);
      }
    }, {
      key: "pluginIsInstalled",
      value: function value(t) {
        return this._plugins.map[t.id] || -1 !== this._plugins.list.indexOf(t);
      }
    }, {
      key: "usePlugin",
      value: function value(t, e) {
        if (!this.isInitialized) return this;
        if (this.pluginIsInstalled(t)) return this;

        if (t.id && (this._plugins.map[t.id] = t), this._plugins.list.push(t), t.install && t.install(this, e), t.listeners && t.before) {
          for (var n = 0, r = this.listenerMaps.length, o = t.before.reduce(function (t, e) {
            return t[e] = !0, t[cr(e)] = !0, t;
          }, {}); n < r; n++) {
            var i = this.listenerMaps[n].id;
            if (o[i] || o[cr(i)]) break;
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
      value: function value(t, n) {
        if (-1 !== this.getDocIndex(t)) return !1;
        var r = e.getWindow(t);
        n = n ? (0, j["default"])({}, n) : {}, this.documents.push({
          doc: t,
          options: n
        }), this.events.documents.push(t), t !== this.document && this.events.add(r, "unload", this.onWindowUnload), this.fire("scope:add-document", {
          doc: t,
          window: r,
          scope: this,
          options: n
        });
      }
    }, {
      key: "removeDocument",
      value: function value(t) {
        var n = this.getDocIndex(t),
            r = e.getWindow(t),
            o = this.documents[n].options;
        this.events.remove(r, "unload", this.onWindowUnload), this.documents.splice(n, 1), this.events.documents.splice(n, 1), this.fire("scope:remove-document", {
          doc: t,
          window: r,
          scope: this,
          options: o
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

  function ur(t, n) {
    return t.isInitialized = !0, e.init(n), h["default"].init(n), b["default"].init(n), Mt["default"].init(n), t.window = n, t.document = n.document, t.usePlugin(Fn["default"]), t.usePlugin(On["default"]), t;
  }

  function cr(t) {
    return t && t.replace(/\/.*$/, "");
  }

  Jn.Scope = lr;
  var fr = {};

  function dr(t) {
    return (dr = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(fr, "__esModule", {
    value: !0
  }), fr.init = fr["default"] = void 0;
  var pr = new Jn.Scope(),
      vr = pr.interactStatic;
  fr["default"] = vr;

  var hr = function hr(t) {
    return pr.init(t);
  };

  fr.init = hr, "object" === ("undefined" == typeof window ? "undefined" : dr(window)) && window && hr(window);
  var gr = {};
  Object.defineProperty(gr, "__esModule", {
    value: !0
  }), gr["default"] = void 0;

  gr["default"] = function () {};

  var yr = {};
  Object.defineProperty(yr, "__esModule", {
    value: !0
  }), yr["default"] = void 0;

  yr["default"] = function () {};

  var mr = {};

  function br(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" == typeof Symbol || !(Symbol.iterator in Object(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, s = t[Symbol.iterator](); !(r = (a = s.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == s["return"] || s["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return xr(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(t);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return xr(t, e);
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function xr(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(mr, "__esModule", {
    value: !0
  }), mr["default"] = void 0;

  mr["default"] = function (t) {
    var e = [["x", "y"], ["left", "top"], ["right", "bottom"], ["width", "height"]].filter(function (e) {
      var n = br(e, 2),
          r = n[0],
          o = n[1];
      return r in t || o in t;
    }),
        n = function n(_n2, r) {
      for (var o = t.range, i = t.limits, a = void 0 === i ? {
        left: -1 / 0,
        right: 1 / 0,
        top: -1 / 0,
        bottom: 1 / 0
      } : i, s = t.offset, l = void 0 === s ? {
        x: 0,
        y: 0
      } : s, u = {
        range: o,
        grid: t,
        x: null,
        y: null
      }, c = 0; c < e.length; c++) {
        var f = br(e[c], 2),
            d = f[0],
            p = f[1],
            v = Math.round((_n2 - l.x) / t[d]),
            h = Math.round((r - l.y) / t[p]);
        u[d] = Math.max(a.left, Math.min(a.right, v * t[d] + l.x)), u[p] = Math.max(a.top, Math.min(a.bottom, h * t[p] + l.y));
      }

      return u;
    };

    return n.grid = t, n.coordFields = e, n;
  };

  var wr = {};
  Object.defineProperty(wr, "__esModule", {
    value: !0
  }), Object.defineProperty(wr, "edgeTarget", {
    enumerable: !0,
    get: function get() {
      return gr["default"];
    }
  }), Object.defineProperty(wr, "elements", {
    enumerable: !0,
    get: function get() {
      return yr["default"];
    }
  }), Object.defineProperty(wr, "grid", {
    enumerable: !0,
    get: function get() {
      return mr["default"];
    }
  });
  var _r = {};
  Object.defineProperty(_r, "__esModule", {
    value: !0
  }), _r["default"] = void 0;
  var Sr = {
    id: "snappers",
    install: function install(t) {
      var e = t.interactStatic;
      e.snappers = (0, j["default"])(e.snappers || {}, wr), e.createSnapGrid = e.snappers.grid;
    }
  };
  _r["default"] = Sr;
  var Pr = {};

  function Or(t, e) {
    var n = Object.keys(t);

    if (Object.getOwnPropertySymbols) {
      var r = Object.getOwnPropertySymbols(t);
      e && (r = r.filter(function (e) {
        return Object.getOwnPropertyDescriptor(t, e).enumerable;
      })), n.push.apply(n, r);
    }

    return n;
  }

  function Er(t) {
    for (var e = 1; e < arguments.length; e++) {
      var n = null != arguments[e] ? arguments[e] : {};
      e % 2 ? Or(Object(n), !0).forEach(function (e) {
        Tr(t, e, n[e]);
      }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(n)) : Or(Object(n)).forEach(function (e) {
        Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(n, e));
      });
    }

    return t;
  }

  function Tr(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(Pr, "__esModule", {
    value: !0
  }), Pr.aspectRatio = Pr["default"] = void 0;
  var Mr = {
    start: function start(t) {
      var e = t.state,
          n = t.rect,
          r = t.edges,
          o = t.pageCoords,
          i = e.options.ratio,
          a = e.options,
          s = a.equalDelta,
          l = a.modifiers;
      "preserve" === i && (i = n.width / n.height), e.startCoords = (0, j["default"])({}, o), e.startRect = (0, j["default"])({}, n), e.ratio = i, e.equalDelta = s;
      var u = e.linkedEdges = {
        top: r.top || r.left && !r.bottom,
        left: r.left || r.top && !r.right,
        bottom: r.bottom || r.right && !r.top,
        right: r.right || r.bottom && !r.left
      };
      if (e.xIsPrimaryAxis = !(!r.left && !r.right), e.equalDelta) e.edgeSign = (u.left ? 1 : -1) * (u.top ? 1 : -1);else {
        var c = e.xIsPrimaryAxis ? u.top : u.left;
        e.edgeSign = c ? -1 : 1;
      }

      if ((0, j["default"])(t.edges, u), l && l.length) {
        var f = new Se["default"](t.interaction);
        f.copyFrom(t.interaction.modification), f.prepareStates(l), e.subModification = f, f.startAll(Er({}, t));
      }
    },
    set: function set(t) {
      var e = t.state,
          n = t.rect,
          r = t.coords,
          o = (0, j["default"])({}, r),
          i = e.equalDelta ? jr : kr;
      if (i(e, e.xIsPrimaryAxis, r, n), !e.subModification) return null;
      var a = (0, j["default"])({}, n);
      (0, k.addEdges)(e.linkedEdges, a, {
        x: r.x - o.x,
        y: r.y - o.y
      });
      var s = e.subModification.setAll(Er(Er({}, t), {}, {
        rect: a,
        edges: e.linkedEdges,
        pageCoords: r,
        prevCoords: r,
        prevRect: a
      })),
          l = s.delta;
      s.changed && (i(e, Math.abs(l.x) > Math.abs(l.y), s.coords, s.rect), (0, j["default"])(r, s.coords));
      return s.eventProps;
    },
    defaults: {
      ratio: "preserve",
      equalDelta: !1,
      modifiers: [],
      enabled: !1
    }
  };

  function jr(t, e, n) {
    var r = t.startCoords,
        o = t.edgeSign;
    e ? n.y = r.y + (n.x - r.x) * o : n.x = r.x + (n.y - r.y) * o;
  }

  function kr(t, e, n, r) {
    var o = t.startRect,
        i = t.startCoords,
        a = t.ratio,
        s = t.edgeSign;

    if (e) {
      var l = r.width / a;
      n.y = i.y + (l - o.height) * s;
    } else {
      var u = r.height * a;
      n.x = i.x + (u - o.width) * s;
    }
  }

  Pr.aspectRatio = Mr;
  var Ir = (0, ke.makeModifier)(Mr, "aspectRatio");
  Pr["default"] = Ir;
  var Dr = {};
  Object.defineProperty(Dr, "__esModule", {
    value: !0
  }), Dr["default"] = void 0;

  var Ar = function Ar() {};

  Ar._defaults = {};
  var zr = Ar;
  Dr["default"] = zr;
  var Cr = {};
  Object.defineProperty(Cr, "__esModule", {
    value: !0
  }), Object.defineProperty(Cr, "default", {
    enumerable: !0,
    get: function get() {
      return Dr["default"];
    }
  });
  var Rr = {};

  function Fr(t, e, n) {
    return i["default"].func(t) ? k.resolveRectLike(t, e.interactable, e.element, [n.x, n.y, e]) : k.resolveRectLike(t, e.interactable, e.element);
  }

  Object.defineProperty(Rr, "__esModule", {
    value: !0
  }), Rr.getRestrictionRect = Fr, Rr.restrict = Rr["default"] = void 0;
  var Xr = {
    start: function start(t) {
      var e = t.rect,
          n = t.startOffset,
          r = t.state,
          o = t.interaction,
          i = t.pageCoords,
          a = r.options,
          s = a.elementRect,
          l = (0, j["default"])({
        left: 0,
        top: 0,
        right: 0,
        bottom: 0
      }, a.offset || {});

      if (e && s) {
        var u = Fr(a.restriction, o, i);

        if (u) {
          var c = u.right - u.left - e.width,
              f = u.bottom - u.top - e.height;
          c < 0 && (l.left += c, l.right += c), f < 0 && (l.top += f, l.bottom += f);
        }

        l.left += n.left - e.width * s.left, l.top += n.top - e.height * s.top, l.right += n.right - e.width * (1 - s.right), l.bottom += n.bottom - e.height * (1 - s.bottom);
      }

      r.offset = l;
    },
    set: function set(t) {
      var e = t.coords,
          n = t.interaction,
          r = t.state,
          o = r.options,
          i = r.offset,
          a = Fr(o.restriction, n, e);

      if (a) {
        var s = k.xywhToTlbr(a);
        e.x = Math.max(Math.min(s.right - i.right, e.x), s.left + i.left), e.y = Math.max(Math.min(s.bottom - i.bottom, e.y), s.top + i.top);
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
  Rr.restrict = Xr;
  var Yr = (0, ke.makeModifier)(Xr, "restrict");
  Rr["default"] = Yr;
  var Wr = {};
  Object.defineProperty(Wr, "__esModule", {
    value: !0
  }), Wr.restrictEdges = Wr["default"] = void 0;
  var Lr = {
    top: 1 / 0,
    left: 1 / 0,
    bottom: -1 / 0,
    right: -1 / 0
  },
      Br = {
    top: -1 / 0,
    left: -1 / 0,
    bottom: 1 / 0,
    right: 1 / 0
  };

  function Ur(t, e) {
    for (var n = ["top", "left", "bottom", "right"], r = 0; r < n.length; r++) {
      var o = n[r];
      o in t || (t[o] = e[o]);
    }

    return t;
  }

  var Nr = {
    noInner: Lr,
    noOuter: Br,
    start: function start(t) {
      var e,
          n = t.interaction,
          r = t.startOffset,
          o = t.state,
          i = o.options;

      if (i) {
        var a = (0, Rr.getRestrictionRect)(i.offset, n, n.coords.start.page);
        e = k.rectToXY(a);
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
        var s = (0, j["default"])({}, e),
            l = (0, Rr.getRestrictionRect)(a.inner, r, s) || {},
            u = (0, Rr.getRestrictionRect)(a.outer, r, s) || {};
        Ur(l, Lr), Ur(u, Br), n.top ? e.y = Math.min(Math.max(u.top + i.top, s.y), l.top + i.top) : n.bottom && (e.y = Math.max(Math.min(u.bottom + i.bottom, s.y), l.bottom + i.bottom)), n.left ? e.x = Math.min(Math.max(u.left + i.left, s.x), l.left + i.left) : n.right && (e.x = Math.max(Math.min(u.right + i.right, s.x), l.right + i.right));
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
  Wr.restrictEdges = Nr;
  var Vr = (0, ke.makeModifier)(Nr, "restrictEdges");
  Wr["default"] = Vr;
  var qr = {};
  Object.defineProperty(qr, "__esModule", {
    value: !0
  }), qr.restrictRect = qr["default"] = void 0;
  var $r = (0, j["default"])({
    get elementRect() {
      return {
        top: 0,
        left: 0,
        bottom: 1,
        right: 1
      };
    },

    set elementRect(t) {}

  }, Rr.restrict.defaults),
      Gr = {
    start: Rr.restrict.start,
    set: Rr.restrict.set,
    defaults: $r
  };
  qr.restrictRect = Gr;
  var Hr = (0, ke.makeModifier)(Gr, "restrictRect");
  qr["default"] = Hr;
  var Kr = {};
  Object.defineProperty(Kr, "__esModule", {
    value: !0
  }), Kr.restrictSize = Kr["default"] = void 0;
  var Zr = {
    width: -1 / 0,
    height: -1 / 0
  },
      Jr = {
    width: 1 / 0,
    height: 1 / 0
  };
  var Qr = {
    start: function start(t) {
      return Wr.restrictEdges.start(t);
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.state,
          r = t.rect,
          o = t.edges,
          i = n.options;

      if (o) {
        var a = k.tlbrToXywh((0, Rr.getRestrictionRect)(i.min, e, t.coords)) || Zr,
            s = k.tlbrToXywh((0, Rr.getRestrictionRect)(i.max, e, t.coords)) || Jr;
        n.options = {
          endOnly: i.endOnly,
          inner: (0, j["default"])({}, Wr.restrictEdges.noInner),
          outer: (0, j["default"])({}, Wr.restrictEdges.noOuter)
        }, o.top ? (n.options.inner.top = r.bottom - a.height, n.options.outer.top = r.bottom - s.height) : o.bottom && (n.options.inner.bottom = r.top + a.height, n.options.outer.bottom = r.top + s.height), o.left ? (n.options.inner.left = r.right - a.width, n.options.outer.left = r.right - s.width) : o.right && (n.options.inner.right = r.left + a.width, n.options.outer.right = r.left + s.width), Wr.restrictEdges.set(t), n.options = i;
      }
    },
    defaults: {
      min: null,
      max: null,
      endOnly: !1,
      enabled: !1
    }
  };
  Kr.restrictSize = Qr;
  var to = (0, ke.makeModifier)(Qr, "restrictSize");
  Kr["default"] = to;
  var eo = {};
  Object.defineProperty(eo, "__esModule", {
    value: !0
  }), Object.defineProperty(eo, "default", {
    enumerable: !0,
    get: function get() {
      return Dr["default"];
    }
  });
  var no = {};
  Object.defineProperty(no, "__esModule", {
    value: !0
  }), no.snap = no["default"] = void 0;
  var ro = {
    start: function start(t) {
      var e,
          n = t.interaction,
          r = t.interactable,
          o = t.element,
          i = t.rect,
          a = t.state,
          s = t.startOffset,
          l = a.options,
          u = l.offsetWithOrigin ? function (t) {
        var e = t.interaction.element;
        return (0, k.rectToXY)((0, k.resolveRectLike)(t.state.options.origin, null, null, [e])) || (0, A["default"])(t.interactable, e, t.interaction.prepared.name);
      }(t) : {
        x: 0,
        y: 0
      };
      if ("startCoords" === l.offset) e = {
        x: n.coords.start.page.x,
        y: n.coords.start.page.y
      };else {
        var c = (0, k.resolveRectLike)(l.offset, r, o, [n]);
        (e = (0, k.rectToXY)(c) || {
          x: 0,
          y: 0
        }).x += u.x, e.y += u.y;
      }
      var f = l.relativePoints;
      a.offsets = i && f && f.length ? f.map(function (t, n) {
        return {
          index: n,
          relativePoint: t,
          x: s.left - i.width * t.x + e.x,
          y: s.top - i.height * t.y + e.y
        };
      }) : [(0, j["default"])({
        index: 0,
        relativePoint: null
      }, e)];
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.coords,
          r = t.state,
          o = r.options,
          a = r.offsets,
          s = (0, A["default"])(e.interactable, e.element, e.prepared.name),
          l = (0, j["default"])({}, n),
          u = [];
      o.offsetWithOrigin || (l.x -= s.x, l.y -= s.y);

      for (var c = 0; c < a.length; c++) {
        for (var f = a[c], d = l.x - f.x, p = l.y - f.y, v = 0, h = o.targets.length; v < h; v++) {
          var g = o.targets[v],
              y = void 0;
          (y = i["default"].func(g) ? g(d, p, e._proxy, f, v) : g) && u.push({
            x: (i["default"].number(y.x) ? y.x : d) + f.x,
            y: (i["default"].number(y.y) ? y.y : p) + f.y,
            range: i["default"].number(y.range) ? y.range : o.range,
            source: g,
            index: v,
            offset: f
          });
        }
      }

      for (var m = {
        target: null,
        inRange: !1,
        distance: 0,
        range: 0,
        delta: {
          x: 0,
          y: 0
        }
      }, b = 0; b < u.length; b++) {
        var x = u[b],
            w = x.range,
            _ = x.x - l.x,
            S = x.y - l.y,
            P = (0, R["default"])(_, S),
            O = P <= w;

        w === 1 / 0 && m.inRange && m.range !== 1 / 0 && (O = !1), m.target && !(O ? m.inRange && w !== 1 / 0 ? P / w < m.distance / m.range : w === 1 / 0 && m.range !== 1 / 0 || P < m.distance : !m.inRange && P < m.distance) || (m.target = x, m.distance = P, m.range = w, m.inRange = O, m.delta.x = _, m.delta.y = S);
      }

      return m.inRange && (n.x = m.target.x, n.y = m.target.y), r.closest = m, m;
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
  no.snap = ro;
  var oo = (0, ke.makeModifier)(ro, "snap");
  no["default"] = oo;
  var io = {};

  function ao(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" == typeof Symbol || !(Symbol.iterator in Object(t))) return;
      var n = [],
          r = !0,
          o = !1,
          i = void 0;

      try {
        for (var a, s = t[Symbol.iterator](); !(r = (a = s.next()).done) && (n.push(a.value), !e || n.length !== e); r = !0) {
          ;
        }
      } catch (t) {
        o = !0, i = t;
      } finally {
        try {
          r || null == s["return"] || s["return"]();
        } finally {
          if (o) throw i;
        }
      }

      return n;
    }(t, e) || function (t, e) {
      if (!t) return;
      if ("string" == typeof t) return so(t, e);
      var n = Object.prototype.toString.call(t).slice(8, -1);
      "Object" === n && t.constructor && (n = t.constructor.name);
      if ("Map" === n || "Set" === n) return Array.from(t);
      if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return so(t, e);
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function so(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(io, "__esModule", {
    value: !0
  }), io.snapSize = io["default"] = void 0;
  var lo = {
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
      }, e.targetFields = e.targetFields || [["width", "height"], ["x", "y"]], no.snap.start(t), e.offsets = t.state.offsets, t.state = e;
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.state,
          r = t.coords,
          o = n.options,
          a = n.offsets,
          s = {
        x: r.x - a[0].x,
        y: r.y - a[0].y
      };
      n.options = (0, j["default"])({}, o), n.options.targets = [];

      for (var l = 0; l < (o.targets || []).length; l++) {
        var u = (o.targets || [])[l],
            c = void 0;

        if (c = i["default"].func(u) ? u(s.x, s.y, e) : u) {
          for (var f = 0; f < n.targetFields.length; f++) {
            var d = ao(n.targetFields[f], 2),
                p = d[0],
                v = d[1];

            if (p in c || v in c) {
              c.x = c[p], c.y = c[v];
              break;
            }
          }

          n.options.targets.push(c);
        }
      }

      var h = no.snap.set(t);
      return n.options = o, h;
    },
    defaults: {
      range: 1 / 0,
      targets: null,
      offset: null,
      endOnly: !1,
      enabled: !1
    }
  };
  io.snapSize = lo;
  var uo = (0, ke.makeModifier)(lo, "snapSize");
  io["default"] = uo;
  var co = {};
  Object.defineProperty(co, "__esModule", {
    value: !0
  }), co.snapEdges = co["default"] = void 0;
  var fo = {
    start: function start(t) {
      var e = t.edges;
      return e ? (t.state.targetFields = t.state.targetFields || [[e.left ? "left" : "right", e.top ? "top" : "bottom"]], io.snapSize.start(t)) : null;
    },
    set: io.snapSize.set,
    defaults: (0, j["default"])((0, _e["default"])(io.snapSize.defaults), {
      targets: null,
      range: null,
      offset: {
        x: 0,
        y: 0
      }
    })
  };
  co.snapEdges = fo;
  var po = (0, ke.makeModifier)(fo, "snapEdges");
  co["default"] = po;
  var vo = {};
  Object.defineProperty(vo, "__esModule", {
    value: !0
  }), Object.defineProperty(vo, "default", {
    enumerable: !0,
    get: function get() {
      return Dr["default"];
    }
  });
  var ho = {};
  Object.defineProperty(ho, "__esModule", {
    value: !0
  }), Object.defineProperty(ho, "default", {
    enumerable: !0,
    get: function get() {
      return Dr["default"];
    }
  });
  var go = {};
  Object.defineProperty(go, "__esModule", {
    value: !0
  }), go["default"] = void 0;
  var yo = {
    aspectRatio: Pr["default"],
    restrictEdges: Wr["default"],
    restrict: Rr["default"],
    restrictRect: qr["default"],
    restrictSize: Kr["default"],
    snapEdges: co["default"],
    snap: no["default"],
    snapSize: io["default"],
    spring: vo["default"],
    avoid: Cr["default"],
    transform: ho["default"],
    rubberband: eo["default"]
  };
  go["default"] = yo;
  var mo = {};
  Object.defineProperty(mo, "__esModule", {
    value: !0
  }), mo["default"] = void 0;
  var bo = {
    id: "modifiers",
    install: function install(t) {
      var e = t.interactStatic;

      for (var n in t.usePlugin(ke["default"]), t.usePlugin(_r["default"]), e.modifiers = go["default"], go["default"]) {
        var r = go["default"][n],
            o = r._defaults,
            i = r._methods;
        o._methods = i, t.defaults.perAction[n] = o;
      }
    }
  };
  mo["default"] = bo;
  var xo = {};
  Object.defineProperty(xo, "__esModule", {
    value: !0
  }), xo["default"] = void 0;
  xo["default"] = {};
  var wo = {};

  function _o(t) {
    return (_o = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function So(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Po(t, e) {
    return (Po = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function Oo(t) {
    var e = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }();

    return function () {
      var n,
          r = Mo(t);

      if (e) {
        var o = Mo(this).constructor;
        n = Reflect.construct(r, arguments, o);
      } else n = r.apply(this, arguments);

      return Eo(this, n);
    };
  }

  function Eo(t, e) {
    return !e || "object" !== _o(e) && "function" != typeof e ? To(t) : e;
  }

  function To(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function Mo(t) {
    return (Mo = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  Object.defineProperty(wo, "__esModule", {
    value: !0
  }), wo.PointerEvent = wo["default"] = void 0;

  var jo = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && Po(t, e);
    }(i, t);
    var e,
        n,
        r,
        o = Oo(i);

    function i(t, e, n, r, a, s) {
      var l;

      if (function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, i), (l = o.call(this, a)).type = void 0, l.originalEvent = void 0, l.pointerId = void 0, l.pointerType = void 0, l["double"] = void 0, l.pageX = void 0, l.pageY = void 0, l.clientX = void 0, l.clientY = void 0, l.dt = void 0, l.eventable = void 0, W.pointerExtend(To(l), n), n !== e && W.pointerExtend(To(l), e), l.timeStamp = s, l.originalEvent = n, l.type = t, l.pointerId = W.getPointerId(e), l.pointerType = W.getPointerType(e), l.target = r, l.currentTarget = null, "tap" === t) {
        var u = a.getPointerIndex(e);
        l.dt = l.timeStamp - a.pointers[u].downTime;
        var c = l.timeStamp - a.tapTime;
        l["double"] = !!(a.prevTap && "doubletap" !== a.prevTap.type && a.prevTap.target === l.target && c < 500);
      } else "doubletap" === t && (l.dt = e.timeStamp - a.tapTime);

      return l;
    }

    return e = i, (n = [{
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
    }]) && So(e.prototype, n), r && So(e, r), i;
  }($.BaseEvent);

  wo.PointerEvent = wo["default"] = jo;
  var ko = {};
  Object.defineProperty(ko, "__esModule", {
    value: !0
  }), ko["default"] = void 0;
  var Io = {
    id: "pointer-events/base",
    before: ["inertia", "modifiers", "auto-start", "actions"],
    install: function install(t) {
      t.pointerEvents = Io, t.defaults.actions.pointerEvents = Io.defaults, (0, j["default"])(t.actions.phaselessTypes, Io.types);
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
            i = t.eventTarget;
        t.duplicate || n.pointerIsDown && !n.pointerWasMoved || (n.pointerIsDown && zo(t), Do({
          interaction: n,
          pointer: r,
          event: o,
          eventTarget: i,
          type: "move"
        }, e));
      },
      "interactions:down": function interactionsDown(t, e) {
        !function (t, e) {
          for (var n = t.interaction, r = t.pointer, o = t.event, i = t.eventTarget, a = t.pointerIndex, s = n.pointers[a].hold, l = _.getPath(i), u = {
            interaction: n,
            pointer: r,
            event: o,
            eventTarget: i,
            type: "hold",
            targets: [],
            path: l,
            node: null
          }, c = 0; c < l.length; c++) {
            var f = l[c];
            u.node = f, e.fire("pointerEvents:collect-targets", u);
          }

          if (!u.targets.length) return;

          for (var d = 1 / 0, p = 0; p < u.targets.length; p++) {
            var v = u.targets[p].eventable.options.holdDuration;
            v < d && (d = v);
          }

          s.duration = d, s.timeout = setTimeout(function () {
            Do({
              interaction: n,
              eventTarget: i,
              pointer: r,
              event: o,
              type: "hold"
            }, e);
          }, d);
        }(t, e), Do(t, e);
      },
      "interactions:up": function interactionsUp(t, e) {
        zo(t), Do(t, e), function (t, e) {
          var n = t.interaction,
              r = t.pointer,
              o = t.event,
              i = t.eventTarget;
          n.pointerWasMoved || Do({
            interaction: n,
            eventTarget: i,
            pointer: r,
            event: o,
            type: "tap"
          }, e);
        }(t, e);
      },
      "interactions:cancel": function interactionsCancel(t, e) {
        zo(t), Do(t, e);
      }
    },
    PointerEvent: wo.PointerEvent,
    fire: Do,
    collectEventTargets: Ao,
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

  function Do(t, e) {
    var n = t.interaction,
        r = t.pointer,
        o = t.event,
        i = t.eventTarget,
        a = t.type,
        s = t.targets,
        l = void 0 === s ? Ao(t, e) : s,
        u = new wo.PointerEvent(a, r, o, i, n, e.now());
    e.fire("pointerEvents:new", {
      pointerEvent: u
    });

    for (var c = {
      interaction: n,
      pointer: r,
      event: o,
      eventTarget: i,
      targets: l,
      type: a,
      pointerEvent: u
    }, f = 0; f < l.length; f++) {
      var d = l[f];

      for (var p in d.props || {}) {
        u[p] = d.props[p];
      }

      var v = (0, A["default"])(d.eventable, d.node);
      if (u._subtractOrigin(v), u.eventable = d.eventable, u.currentTarget = d.node, d.eventable.fire(u), u._addOrigin(v), u.immediatePropagationStopped || u.propagationStopped && f + 1 < l.length && l[f + 1].node !== u.currentTarget) break;
    }

    if (e.fire("pointerEvents:fired", c), "tap" === a) {
      var h = u["double"] ? Do({
        interaction: n,
        pointer: r,
        event: o,
        eventTarget: i,
        type: "doubletap"
      }, e) : u;
      n.prevTap = h, n.tapTime = h.timeStamp;
    }

    return u;
  }

  function Ao(t, e) {
    var n = t.interaction,
        r = t.pointer,
        o = t.event,
        i = t.eventTarget,
        a = t.type,
        s = n.getPointerIndex(r),
        l = n.pointers[s];
    if ("tap" === a && (n.pointerWasMoved || !l || l.downTarget !== i)) return [];

    for (var u = _.getPath(i), c = {
      interaction: n,
      pointer: r,
      event: o,
      eventTarget: i,
      type: a,
      path: u,
      targets: [],
      node: null
    }, f = 0; f < u.length; f++) {
      var d = u[f];
      c.node = d, e.fire("pointerEvents:collect-targets", c);
    }

    return "hold" === a && (c.targets = c.targets.filter(function (t) {
      return t.eventable.options.holdDuration === n.pointers[s].hold.duration;
    })), c.targets;
  }

  function zo(t) {
    var e = t.interaction,
        n = t.pointerIndex,
        r = e.pointers[n].hold;
    r && r.timeout && (clearTimeout(r.timeout), r.timeout = null);
  }

  var Co = Io;
  ko["default"] = Co;
  var Ro = {};

  function Fo(t) {
    var e = t.interaction;
    e.holdIntervalHandle && (clearInterval(e.holdIntervalHandle), e.holdIntervalHandle = null);
  }

  Object.defineProperty(Ro, "__esModule", {
    value: !0
  }), Ro["default"] = void 0;
  var Xo = {
    id: "pointer-events/holdRepeat",
    install: function install(t) {
      t.usePlugin(ko["default"]);
      var e = t.pointerEvents;
      e.defaults.holdRepeatInterval = 0, e.types.holdrepeat = t.actions.phaselessTypes.holdrepeat = !0;
    },
    listeners: ["move", "up", "cancel", "endall"].reduce(function (t, e) {
      return t["pointerEvents:".concat(e)] = Fo, t;
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
  Ro["default"] = Xo;
  var Yo = {};

  function Wo(t) {
    return (0, j["default"])(this.events.options, t), this;
  }

  Object.defineProperty(Yo, "__esModule", {
    value: !0
  }), Yo["default"] = void 0;
  var Lo = {
    id: "pointer-events/interactableTargets",
    install: function install(t) {
      var e = t.Interactable;
      e.prototype.pointerEvents = Wo;
      var n = e.prototype._backCompatOption;

      e.prototype._backCompatOption = function (t, e) {
        var r = n.call(this, t, e);
        return r === this && (this.events.options[t] = e), r;
      };
    },
    listeners: {
      "pointerEvents:collect-targets": function pointerEventsCollectTargets(t, e) {
        var n = t.targets,
            r = t.node,
            o = t.type,
            i = t.eventTarget;
        e.interactables.forEachMatch(r, function (t) {
          var e = t.events,
              a = e.options;
          e.types[o] && e.types[o].length && t.testIgnoreAllow(a, r, i) && n.push({
            node: r,
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
        (0, j["default"])(n.events.options, e.pointerEvents.defaults), (0, j["default"])(n.events.options, r.pointerEvents || {});
      }
    }
  };
  Yo["default"] = Lo;
  var Bo = {};
  Object.defineProperty(Bo, "__esModule", {
    value: !0
  }), Bo["default"] = void 0;
  var Uo = {
    id: "pointer-events",
    install: function install(t) {
      t.usePlugin(ko), t.usePlugin(Ro["default"]), t.usePlugin(Yo["default"]);
    }
  };
  Bo["default"] = Uo;
  var No = {};
  Object.defineProperty(No, "__esModule", {
    value: !0
  }), No["default"] = void 0;
  No["default"] = {};
  var Vo = {};

  function qo(t) {
    var e = t.Interactable;
    t.actions.phases.reflow = !0, e.prototype.reflow = function (e) {
      return function (t, e, n) {
        for (var r = i["default"].string(t.target) ? K.from(t._context.querySelectorAll(t.target)) : [t.target], o = n.window.Promise, a = o ? [] : null, s = function s() {
          var i = r[l],
              s = t.getRect(i);
          if (!s) return "break";
          var u = K.find(n.interactions.list, function (n) {
            return n.interacting() && n.interactable === t && n.element === i && n.prepared.name === e.name;
          }),
              c = void 0;
          if (u) u.move(), a && (c = u._reflowPromise || new o(function (t) {
            u._reflowResolve = t;
          }));else {
            var f = (0, k.tlbrToXywh)(s),
                d = {
              page: {
                x: f.x,
                y: f.y
              },
              client: {
                x: f.x,
                y: f.y
              },
              timeStamp: n.now()
            },
                p = W.coordsToEvent(d);

            c = function (t, e, n, r, o) {
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
              i.interactable = e, i.element = n, i.prevEvent = o, i.updatePointer(o, o, n, !0), W.setZeroCoords(i.coords.delta), (0, Xt.copyAction)(i.prepared, r), i._doPhase(a);
              var s = t.window.Promise,
                  l = s ? new s(function (t) {
                i._reflowResolve = t;
              }) : void 0;
              i._reflowPromise = l, i.start(r, e, n), i._interacting ? (i.move(a), i.end(o)) : (i.stop(), i._reflowResolve());
              return i.removePointer(o, o), l;
            }(n, t, i, e, p);
          }
          a && a.push(c);
        }, l = 0; l < r.length; l++) {
          if ("break" === s()) break;
        }

        return a && o.all(a).then(function () {
          return t;
        });
      }(this, e, t);
    };
  }

  Object.defineProperty(Vo, "__esModule", {
    value: !0
  }), Vo.install = qo, Vo["default"] = void 0;
  var $o = {
    id: "reflow",
    install: qo,
    listeners: {
      "interactions:stop": function interactionsStop(t, e) {
        var n = t.interaction;
        "reflow" === n.pointerType && (n._reflowResolve && n._reflowResolve(), K.remove(e.interactions.list, n));
      }
    }
  };
  Vo["default"] = $o;
  var Go = {};
  Object.defineProperty(Go, "__esModule", {
    value: !0
  }), Go["default"] = void 0;
  Go["default"] = {};
  var Ho = {};
  Object.defineProperty(Ho, "__esModule", {
    value: !0
  }), Ho.exchange = void 0;
  Ho.exchange = {};
  var Ko = {};
  Object.defineProperty(Ko, "__esModule", {
    value: !0
  }), Ko["default"] = void 0;
  Ko["default"] = {};
  var Zo = {
    exports: {}
  };

  function Jo(t) {
    return (Jo = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Zo.exports, "__esModule", {
    value: !0
  }), Zo.exports["default"] = void 0, fr["default"].use(xo["default"]), fr["default"].use(se["default"]), fr["default"].use(Je["default"]), fr["default"].use(ae["default"]), fr["default"].use(Tt["default"]), fr["default"].use(Bo["default"]), fr["default"].use(an["default"]), fr["default"].use(mo["default"]), fr["default"].use(oe["default"]), fr["default"].use(Ot["default"]), fr["default"].use(At["default"]), fr["default"].use(Vo["default"]), fr["default"].use(we["default"]), fr["default"].use(Ko["default"]), fr["default"].use(No["default"]), fr["default"].__utils = {
    exchange: Ho.exchange,
    displace: Go,
    pointer: W
  }, fr["default"].use(pe["default"]);
  var Qo = fr["default"];
  if (Zo.exports["default"] = Qo, "object" === Jo(Zo) && Zo) try {
    Zo.exports = fr["default"];
  } catch (t) {}
  fr["default"]["default"] = fr["default"], Zo = Zo.exports;
  var ti = {
    exports: {}
  };

  function ei(t) {
    return (ei = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(ti.exports, "__esModule", {
    value: !0
  }), ti.exports["default"] = void 0;
  var ni = Zo["default"];
  if (ti.exports["default"] = ni, "object" === ei(ti) && ti) try {
    ti.exports = Zo["default"];
  } catch (t) {}
  return Zo["default"]["default"] = Zo["default"], ti = ti.exports;
});

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

/***/ "./node_modules/popper.js/dist/esm/popper.js":
/*!***************************************************!*\
  !*** ./node_modules/popper.js/dist/esm/popper.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/**!
 * @fileOverview Kickass library to create and place poppers near their reference elements.
 * @version 1.16.1
 * @license
 * Copyright (c) 2016 Federico Zivolo and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined' && typeof navigator !== 'undefined';

var timeoutDuration = function () {
  var longerTimeoutBrowsers = ['Edge', 'Trident', 'Firefox'];

  for (var i = 0; i < longerTimeoutBrowsers.length; i += 1) {
    if (isBrowser && navigator.userAgent.indexOf(longerTimeoutBrowsers[i]) >= 0) {
      return 1;
    }
  }

  return 0;
}();

function microtaskDebounce(fn) {
  var called = false;
  return function () {
    if (called) {
      return;
    }

    called = true;
    window.Promise.resolve().then(function () {
      called = false;
      fn();
    });
  };
}

function taskDebounce(fn) {
  var scheduled = false;
  return function () {
    if (!scheduled) {
      scheduled = true;
      setTimeout(function () {
        scheduled = false;
        fn();
      }, timeoutDuration);
    }
  };
}

var supportsMicroTasks = isBrowser && window.Promise;
/**
* Create a debounced version of a method, that's asynchronously deferred
* but called in the minimum time possible.
*
* @method
* @memberof Popper.Utils
* @argument {Function} fn
* @returns {Function}
*/

var debounce = supportsMicroTasks ? microtaskDebounce : taskDebounce;
/**
 * Check if the given variable is a function
 * @method
 * @memberof Popper.Utils
 * @argument {Any} functionToCheck - variable to check
 * @returns {Boolean} answer to: is a function?
 */

function isFunction(functionToCheck) {
  var getType = {};
  return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}
/**
 * Get CSS computed property of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Eement} element
 * @argument {String} property
 */


function getStyleComputedProperty(element, property) {
  if (element.nodeType !== 1) {
    return [];
  } // NOTE: 1 DOM access here


  var window = element.ownerDocument.defaultView;
  var css = window.getComputedStyle(element, null);
  return property ? css[property] : css;
}
/**
 * Returns the parentNode or the host of the element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} parent
 */


function getParentNode(element) {
  if (element.nodeName === 'HTML') {
    return element;
  }

  return element.parentNode || element.host;
}
/**
 * Returns the scrolling parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} scroll parent
 */


function getScrollParent(element) {
  // Return body, `getScroll` will take care to get the correct `scrollTop` from it
  if (!element) {
    return document.body;
  }

  switch (element.nodeName) {
    case 'HTML':
    case 'BODY':
      return element.ownerDocument.body;

    case '#document':
      return element.body;
  } // Firefox want us to check `-x` and `-y` variations as well


  var _getStyleComputedProp = getStyleComputedProperty(element),
      overflow = _getStyleComputedProp.overflow,
      overflowX = _getStyleComputedProp.overflowX,
      overflowY = _getStyleComputedProp.overflowY;

  if (/(auto|scroll|overlay)/.test(overflow + overflowY + overflowX)) {
    return element;
  }

  return getScrollParent(getParentNode(element));
}
/**
 * Returns the reference node of the reference object, or the reference object itself.
 * @method
 * @memberof Popper.Utils
 * @param {Element|Object} reference - the reference element (the popper will be relative to this)
 * @returns {Element} parent
 */


function getReferenceNode(reference) {
  return reference && reference.referenceNode ? reference.referenceNode : reference;
}

var isIE11 = isBrowser && !!(window.MSInputMethodContext && document.documentMode);
var isIE10 = isBrowser && /MSIE 10/.test(navigator.userAgent);
/**
 * Determines if the browser is Internet Explorer
 * @method
 * @memberof Popper.Utils
 * @param {Number} version to check
 * @returns {Boolean} isIE
 */

function isIE(version) {
  if (version === 11) {
    return isIE11;
  }

  if (version === 10) {
    return isIE10;
  }

  return isIE11 || isIE10;
}
/**
 * Returns the offset parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} offset parent
 */


function getOffsetParent(element) {
  if (!element) {
    return document.documentElement;
  }

  var noOffsetParent = isIE(10) ? document.body : null; // NOTE: 1 DOM access here

  var offsetParent = element.offsetParent || null; // Skip hidden elements which don't have an offsetParent

  while (offsetParent === noOffsetParent && element.nextElementSibling) {
    offsetParent = (element = element.nextElementSibling).offsetParent;
  }

  var nodeName = offsetParent && offsetParent.nodeName;

  if (!nodeName || nodeName === 'BODY' || nodeName === 'HTML') {
    return element ? element.ownerDocument.documentElement : document.documentElement;
  } // .offsetParent will return the closest TH, TD or TABLE in case
  // no offsetParent is present, I hate this job...


  if (['TH', 'TD', 'TABLE'].indexOf(offsetParent.nodeName) !== -1 && getStyleComputedProperty(offsetParent, 'position') === 'static') {
    return getOffsetParent(offsetParent);
  }

  return offsetParent;
}

function isOffsetContainer(element) {
  var nodeName = element.nodeName;

  if (nodeName === 'BODY') {
    return false;
  }

  return nodeName === 'HTML' || getOffsetParent(element.firstElementChild) === element;
}
/**
 * Finds the root node (document, shadowDOM root) of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} node
 * @returns {Element} root node
 */


function getRoot(node) {
  if (node.parentNode !== null) {
    return getRoot(node.parentNode);
  }

  return node;
}
/**
 * Finds the offset parent common to the two provided nodes
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element1
 * @argument {Element} element2
 * @returns {Element} common offset parent
 */


function findCommonOffsetParent(element1, element2) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element1 || !element1.nodeType || !element2 || !element2.nodeType) {
    return document.documentElement;
  } // Here we make sure to give as "start" the element that comes first in the DOM


  var order = element1.compareDocumentPosition(element2) & Node.DOCUMENT_POSITION_FOLLOWING;
  var start = order ? element1 : element2;
  var end = order ? element2 : element1; // Get common ancestor container

  var range = document.createRange();
  range.setStart(start, 0);
  range.setEnd(end, 0);
  var commonAncestorContainer = range.commonAncestorContainer; // Both nodes are inside #document

  if (element1 !== commonAncestorContainer && element2 !== commonAncestorContainer || start.contains(end)) {
    if (isOffsetContainer(commonAncestorContainer)) {
      return commonAncestorContainer;
    }

    return getOffsetParent(commonAncestorContainer);
  } // one of the nodes is inside shadowDOM, find which one


  var element1root = getRoot(element1);

  if (element1root.host) {
    return findCommonOffsetParent(element1root.host, element2);
  } else {
    return findCommonOffsetParent(element1, getRoot(element2).host);
  }
}
/**
 * Gets the scroll value of the given element in the given side (top and left)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {String} side `top` or `left`
 * @returns {number} amount of scrolled pixels
 */


function getScroll(element) {
  var side = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'top';
  var upperSide = side === 'top' ? 'scrollTop' : 'scrollLeft';
  var nodeName = element.nodeName;

  if (nodeName === 'BODY' || nodeName === 'HTML') {
    var html = element.ownerDocument.documentElement;
    var scrollingElement = element.ownerDocument.scrollingElement || html;
    return scrollingElement[upperSide];
  }

  return element[upperSide];
}
/*
 * Sum or subtract the element scroll values (left and top) from a given rect object
 * @method
 * @memberof Popper.Utils
 * @param {Object} rect - Rect object you want to change
 * @param {HTMLElement} element - The element from the function reads the scroll values
 * @param {Boolean} subtract - set to true if you want to subtract the scroll values
 * @return {Object} rect - The modifier rect object
 */


function includeScroll(rect, element) {
  var subtract = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var scrollTop = getScroll(element, 'top');
  var scrollLeft = getScroll(element, 'left');
  var modifier = subtract ? -1 : 1;
  rect.top += scrollTop * modifier;
  rect.bottom += scrollTop * modifier;
  rect.left += scrollLeft * modifier;
  rect.right += scrollLeft * modifier;
  return rect;
}
/*
 * Helper to detect borders of a given element
 * @method
 * @memberof Popper.Utils
 * @param {CSSStyleDeclaration} styles
 * Result of `getStyleComputedProperty` on the given element
 * @param {String} axis - `x` or `y`
 * @return {number} borders - The borders size of the given axis
 */


function getBordersSize(styles, axis) {
  var sideA = axis === 'x' ? 'Left' : 'Top';
  var sideB = sideA === 'Left' ? 'Right' : 'Bottom';
  return parseFloat(styles['border' + sideA + 'Width']) + parseFloat(styles['border' + sideB + 'Width']);
}

function getSize(axis, body, html, computedStyle) {
  return Math.max(body['offset' + axis], body['scroll' + axis], html['client' + axis], html['offset' + axis], html['scroll' + axis], isIE(10) ? parseInt(html['offset' + axis]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Top' : 'Left')]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Bottom' : 'Right')]) : 0);
}

function getWindowSizes(document) {
  var body = document.body;
  var html = document.documentElement;
  var computedStyle = isIE(10) && getComputedStyle(html);
  return {
    height: getSize('Height', body, html, computedStyle),
    width: getSize('Width', body, html, computedStyle)
  };
}

var classCallCheck = function classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

var createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

var defineProperty = function defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
};

var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];

    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }

  return target;
};
/**
 * Given element offsets, generate an output similar to getBoundingClientRect
 * @method
 * @memberof Popper.Utils
 * @argument {Object} offsets
 * @returns {Object} ClientRect like output
 */


function getClientRect(offsets) {
  return _extends({}, offsets, {
    right: offsets.left + offsets.width,
    bottom: offsets.top + offsets.height
  });
}
/**
 * Get bounding client rect of given element
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} element
 * @return {Object} client rect
 */


function getBoundingClientRect(element) {
  var rect = {}; // IE10 10 FIX: Please, don't ask, the element isn't
  // considered in DOM in some circumstances...
  // This isn't reproducible in IE10 compatibility mode of IE11

  try {
    if (isIE(10)) {
      rect = element.getBoundingClientRect();
      var scrollTop = getScroll(element, 'top');
      var scrollLeft = getScroll(element, 'left');
      rect.top += scrollTop;
      rect.left += scrollLeft;
      rect.bottom += scrollTop;
      rect.right += scrollLeft;
    } else {
      rect = element.getBoundingClientRect();
    }
  } catch (e) {}

  var result = {
    left: rect.left,
    top: rect.top,
    width: rect.right - rect.left,
    height: rect.bottom - rect.top
  }; // subtract scrollbar size from sizes

  var sizes = element.nodeName === 'HTML' ? getWindowSizes(element.ownerDocument) : {};
  var width = sizes.width || element.clientWidth || result.width;
  var height = sizes.height || element.clientHeight || result.height;
  var horizScrollbar = element.offsetWidth - width;
  var vertScrollbar = element.offsetHeight - height; // if an hypothetical scrollbar is detected, we must be sure it's not a `border`
  // we make this check conditional for performance reasons

  if (horizScrollbar || vertScrollbar) {
    var styles = getStyleComputedProperty(element);
    horizScrollbar -= getBordersSize(styles, 'x');
    vertScrollbar -= getBordersSize(styles, 'y');
    result.width -= horizScrollbar;
    result.height -= vertScrollbar;
  }

  return getClientRect(result);
}

function getOffsetRectRelativeToArbitraryNode(children, parent) {
  var fixedPosition = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var isIE10 = isIE(10);
  var isHTML = parent.nodeName === 'HTML';
  var childrenRect = getBoundingClientRect(children);
  var parentRect = getBoundingClientRect(parent);
  var scrollParent = getScrollParent(children);
  var styles = getStyleComputedProperty(parent);
  var borderTopWidth = parseFloat(styles.borderTopWidth);
  var borderLeftWidth = parseFloat(styles.borderLeftWidth); // In cases where the parent is fixed, we must ignore negative scroll in offset calc

  if (fixedPosition && isHTML) {
    parentRect.top = Math.max(parentRect.top, 0);
    parentRect.left = Math.max(parentRect.left, 0);
  }

  var offsets = getClientRect({
    top: childrenRect.top - parentRect.top - borderTopWidth,
    left: childrenRect.left - parentRect.left - borderLeftWidth,
    width: childrenRect.width,
    height: childrenRect.height
  });
  offsets.marginTop = 0;
  offsets.marginLeft = 0; // Subtract margins of documentElement in case it's being used as parent
  // we do this only on HTML because it's the only element that behaves
  // differently when margins are applied to it. The margins are included in
  // the box of the documentElement, in the other cases not.

  if (!isIE10 && isHTML) {
    var marginTop = parseFloat(styles.marginTop);
    var marginLeft = parseFloat(styles.marginLeft);
    offsets.top -= borderTopWidth - marginTop;
    offsets.bottom -= borderTopWidth - marginTop;
    offsets.left -= borderLeftWidth - marginLeft;
    offsets.right -= borderLeftWidth - marginLeft; // Attach marginTop and marginLeft because in some circumstances we may need them

    offsets.marginTop = marginTop;
    offsets.marginLeft = marginLeft;
  }

  if (isIE10 && !fixedPosition ? parent.contains(scrollParent) : parent === scrollParent && scrollParent.nodeName !== 'BODY') {
    offsets = includeScroll(offsets, parent);
  }

  return offsets;
}

function getViewportOffsetRectRelativeToArtbitraryNode(element) {
  var excludeScroll = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var html = element.ownerDocument.documentElement;
  var relativeOffset = getOffsetRectRelativeToArbitraryNode(element, html);
  var width = Math.max(html.clientWidth, window.innerWidth || 0);
  var height = Math.max(html.clientHeight, window.innerHeight || 0);
  var scrollTop = !excludeScroll ? getScroll(html) : 0;
  var scrollLeft = !excludeScroll ? getScroll(html, 'left') : 0;
  var offset = {
    top: scrollTop - relativeOffset.top + relativeOffset.marginTop,
    left: scrollLeft - relativeOffset.left + relativeOffset.marginLeft,
    width: width,
    height: height
  };
  return getClientRect(offset);
}
/**
 * Check if the given element is fixed or is inside a fixed parent
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {Element} customContainer
 * @returns {Boolean} answer to "isFixed?"
 */


function isFixed(element) {
  var nodeName = element.nodeName;

  if (nodeName === 'BODY' || nodeName === 'HTML') {
    return false;
  }

  if (getStyleComputedProperty(element, 'position') === 'fixed') {
    return true;
  }

  var parentNode = getParentNode(element);

  if (!parentNode) {
    return false;
  }

  return isFixed(parentNode);
}
/**
 * Finds the first parent of an element that has a transformed property defined
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} first transformed parent or documentElement
 */


function getFixedPositionOffsetParent(element) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element || !element.parentElement || isIE()) {
    return document.documentElement;
  }

  var el = element.parentElement;

  while (el && getStyleComputedProperty(el, 'transform') === 'none') {
    el = el.parentElement;
  }

  return el || document.documentElement;
}
/**
 * Computed the boundaries limits and return them
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} popper
 * @param {HTMLElement} reference
 * @param {number} padding
 * @param {HTMLElement} boundariesElement - Element used to define the boundaries
 * @param {Boolean} fixedPosition - Is in fixed position mode
 * @returns {Object} Coordinates of the boundaries
 */


function getBoundaries(popper, reference, padding, boundariesElement) {
  var fixedPosition = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false; // NOTE: 1 DOM access here

  var boundaries = {
    top: 0,
    left: 0
  };
  var offsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, getReferenceNode(reference)); // Handle viewport case

  if (boundariesElement === 'viewport') {
    boundaries = getViewportOffsetRectRelativeToArtbitraryNode(offsetParent, fixedPosition);
  } else {
    // Handle other cases based on DOM element used as boundaries
    var boundariesNode = void 0;

    if (boundariesElement === 'scrollParent') {
      boundariesNode = getScrollParent(getParentNode(reference));

      if (boundariesNode.nodeName === 'BODY') {
        boundariesNode = popper.ownerDocument.documentElement;
      }
    } else if (boundariesElement === 'window') {
      boundariesNode = popper.ownerDocument.documentElement;
    } else {
      boundariesNode = boundariesElement;
    }

    var offsets = getOffsetRectRelativeToArbitraryNode(boundariesNode, offsetParent, fixedPosition); // In case of HTML, we need a different computation

    if (boundariesNode.nodeName === 'HTML' && !isFixed(offsetParent)) {
      var _getWindowSizes = getWindowSizes(popper.ownerDocument),
          height = _getWindowSizes.height,
          width = _getWindowSizes.width;

      boundaries.top += offsets.top - offsets.marginTop;
      boundaries.bottom = height + offsets.top;
      boundaries.left += offsets.left - offsets.marginLeft;
      boundaries.right = width + offsets.left;
    } else {
      // for all the other DOM elements, this one is good
      boundaries = offsets;
    }
  } // Add paddings


  padding = padding || 0;
  var isPaddingNumber = typeof padding === 'number';
  boundaries.left += isPaddingNumber ? padding : padding.left || 0;
  boundaries.top += isPaddingNumber ? padding : padding.top || 0;
  boundaries.right -= isPaddingNumber ? padding : padding.right || 0;
  boundaries.bottom -= isPaddingNumber ? padding : padding.bottom || 0;
  return boundaries;
}

function getArea(_ref) {
  var width = _ref.width,
      height = _ref.height;
  return width * height;
}
/**
 * Utility used to transform the `auto` placement to the placement with more
 * available space.
 * @method
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function computeAutoPlacement(placement, refRect, popper, reference, boundariesElement) {
  var padding = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;

  if (placement.indexOf('auto') === -1) {
    return placement;
  }

  var boundaries = getBoundaries(popper, reference, padding, boundariesElement);
  var rects = {
    top: {
      width: boundaries.width,
      height: refRect.top - boundaries.top
    },
    right: {
      width: boundaries.right - refRect.right,
      height: boundaries.height
    },
    bottom: {
      width: boundaries.width,
      height: boundaries.bottom - refRect.bottom
    },
    left: {
      width: refRect.left - boundaries.left,
      height: boundaries.height
    }
  };
  var sortedAreas = Object.keys(rects).map(function (key) {
    return _extends({
      key: key
    }, rects[key], {
      area: getArea(rects[key])
    });
  }).sort(function (a, b) {
    return b.area - a.area;
  });
  var filteredAreas = sortedAreas.filter(function (_ref2) {
    var width = _ref2.width,
        height = _ref2.height;
    return width >= popper.clientWidth && height >= popper.clientHeight;
  });
  var computedPlacement = filteredAreas.length > 0 ? filteredAreas[0].key : sortedAreas[0].key;
  var variation = placement.split('-')[1];
  return computedPlacement + (variation ? '-' + variation : '');
}
/**
 * Get offsets to the reference element
 * @method
 * @memberof Popper.Utils
 * @param {Object} state
 * @param {Element} popper - the popper element
 * @param {Element} reference - the reference element (the popper will be relative to this)
 * @param {Element} fixedPosition - is in fixed position mode
 * @returns {Object} An object containing the offsets which will be applied to the popper
 */


function getReferenceOffsets(state, popper, reference) {
  var fixedPosition = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
  var commonOffsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, getReferenceNode(reference));
  return getOffsetRectRelativeToArbitraryNode(reference, commonOffsetParent, fixedPosition);
}
/**
 * Get the outer sizes of the given element (offset size + margins)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Object} object containing width and height properties
 */


function getOuterSizes(element) {
  var window = element.ownerDocument.defaultView;
  var styles = window.getComputedStyle(element);
  var x = parseFloat(styles.marginTop || 0) + parseFloat(styles.marginBottom || 0);
  var y = parseFloat(styles.marginLeft || 0) + parseFloat(styles.marginRight || 0);
  var result = {
    width: element.offsetWidth + y,
    height: element.offsetHeight + x
  };
  return result;
}
/**
 * Get the opposite placement of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement
 * @returns {String} flipped placement
 */


function getOppositePlacement(placement) {
  var hash = {
    left: 'right',
    right: 'left',
    bottom: 'top',
    top: 'bottom'
  };
  return placement.replace(/left|right|bottom|top/g, function (matched) {
    return hash[matched];
  });
}
/**
 * Get offsets to the popper
 * @method
 * @memberof Popper.Utils
 * @param {Object} position - CSS position the Popper will get applied
 * @param {HTMLElement} popper - the popper element
 * @param {Object} referenceOffsets - the reference offsets (the popper will be relative to this)
 * @param {String} placement - one of the valid placement options
 * @returns {Object} popperOffsets - An object containing the offsets which will be applied to the popper
 */


function getPopperOffsets(popper, referenceOffsets, placement) {
  placement = placement.split('-')[0]; // Get popper node sizes

  var popperRect = getOuterSizes(popper); // Add position, width and height to our offsets object

  var popperOffsets = {
    width: popperRect.width,
    height: popperRect.height
  }; // depending by the popper placement we have to compute its offsets slightly differently

  var isHoriz = ['right', 'left'].indexOf(placement) !== -1;
  var mainSide = isHoriz ? 'top' : 'left';
  var secondarySide = isHoriz ? 'left' : 'top';
  var measurement = isHoriz ? 'height' : 'width';
  var secondaryMeasurement = !isHoriz ? 'height' : 'width';
  popperOffsets[mainSide] = referenceOffsets[mainSide] + referenceOffsets[measurement] / 2 - popperRect[measurement] / 2;

  if (placement === secondarySide) {
    popperOffsets[secondarySide] = referenceOffsets[secondarySide] - popperRect[secondaryMeasurement];
  } else {
    popperOffsets[secondarySide] = referenceOffsets[getOppositePlacement(secondarySide)];
  }

  return popperOffsets;
}
/**
 * Mimics the `find` method of Array
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */


function find(arr, check) {
  // use native find if supported
  if (Array.prototype.find) {
    return arr.find(check);
  } // use `filter` to obtain the same behavior of `find`


  return arr.filter(check)[0];
}
/**
 * Return the index of the matching object
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */


function findIndex(arr, prop, value) {
  // use native findIndex if supported
  if (Array.prototype.findIndex) {
    return arr.findIndex(function (cur) {
      return cur[prop] === value;
    });
  } // use `find` + `indexOf` if `findIndex` isn't supported


  var match = find(arr, function (obj) {
    return obj[prop] === value;
  });
  return arr.indexOf(match);
}
/**
 * Loop trough the list of modifiers and run them in order,
 * each of them will then edit the data object.
 * @method
 * @memberof Popper.Utils
 * @param {dataObject} data
 * @param {Array} modifiers
 * @param {String} ends - Optional modifier name used as stopper
 * @returns {dataObject}
 */


function runModifiers(modifiers, data, ends) {
  var modifiersToRun = ends === undefined ? modifiers : modifiers.slice(0, findIndex(modifiers, 'name', ends));
  modifiersToRun.forEach(function (modifier) {
    if (modifier['function']) {
      // eslint-disable-line dot-notation
      console.warn('`modifier.function` is deprecated, use `modifier.fn`!');
    }

    var fn = modifier['function'] || modifier.fn; // eslint-disable-line dot-notation

    if (modifier.enabled && isFunction(fn)) {
      // Add properties to offsets to make them a complete clientRect object
      // we do this before each modifier to make sure the previous one doesn't
      // mess with these values
      data.offsets.popper = getClientRect(data.offsets.popper);
      data.offsets.reference = getClientRect(data.offsets.reference);
      data = fn(data, modifier);
    }
  });
  return data;
}
/**
 * Updates the position of the popper, computing the new offsets and applying
 * the new style.<br />
 * Prefer `scheduleUpdate` over `update` because of performance reasons.
 * @method
 * @memberof Popper
 */


function update() {
  // if popper is destroyed, don't perform any further update
  if (this.state.isDestroyed) {
    return;
  }

  var data = {
    instance: this,
    styles: {},
    arrowStyles: {},
    attributes: {},
    flipped: false,
    offsets: {}
  }; // compute reference element offsets

  data.offsets.reference = getReferenceOffsets(this.state, this.popper, this.reference, this.options.positionFixed); // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value

  data.placement = computeAutoPlacement(this.options.placement, data.offsets.reference, this.popper, this.reference, this.options.modifiers.flip.boundariesElement, this.options.modifiers.flip.padding); // store the computed placement inside `originalPlacement`

  data.originalPlacement = data.placement;
  data.positionFixed = this.options.positionFixed; // compute the popper offsets

  data.offsets.popper = getPopperOffsets(this.popper, data.offsets.reference, data.placement);
  data.offsets.popper.position = this.options.positionFixed ? 'fixed' : 'absolute'; // run the modifiers

  data = runModifiers(this.modifiers, data); // the first `update` will call `onCreate` callback
  // the other ones will call `onUpdate` callback

  if (!this.state.isCreated) {
    this.state.isCreated = true;
    this.options.onCreate(data);
  } else {
    this.options.onUpdate(data);
  }
}
/**
 * Helper used to know if the given modifier is enabled.
 * @method
 * @memberof Popper.Utils
 * @returns {Boolean}
 */


function isModifierEnabled(modifiers, modifierName) {
  return modifiers.some(function (_ref) {
    var name = _ref.name,
        enabled = _ref.enabled;
    return enabled && name === modifierName;
  });
}
/**
 * Get the prefixed supported property name
 * @method
 * @memberof Popper.Utils
 * @argument {String} property (camelCase)
 * @returns {String} prefixed property (camelCase or PascalCase, depending on the vendor prefix)
 */


function getSupportedPropertyName(property) {
  var prefixes = [false, 'ms', 'Webkit', 'Moz', 'O'];
  var upperProp = property.charAt(0).toUpperCase() + property.slice(1);

  for (var i = 0; i < prefixes.length; i++) {
    var prefix = prefixes[i];
    var toCheck = prefix ? '' + prefix + upperProp : property;

    if (typeof document.body.style[toCheck] !== 'undefined') {
      return toCheck;
    }
  }

  return null;
}
/**
 * Destroys the popper.
 * @method
 * @memberof Popper
 */


function destroy() {
  this.state.isDestroyed = true; // touch DOM only if `applyStyle` modifier is enabled

  if (isModifierEnabled(this.modifiers, 'applyStyle')) {
    this.popper.removeAttribute('x-placement');
    this.popper.style.position = '';
    this.popper.style.top = '';
    this.popper.style.left = '';
    this.popper.style.right = '';
    this.popper.style.bottom = '';
    this.popper.style.willChange = '';
    this.popper.style[getSupportedPropertyName('transform')] = '';
  }

  this.disableEventListeners(); // remove the popper if user explicitly asked for the deletion on destroy
  // do not use `remove` because IE11 doesn't support it

  if (this.options.removeOnDestroy) {
    this.popper.parentNode.removeChild(this.popper);
  }

  return this;
}
/**
 * Get the window associated with the element
 * @argument {Element} element
 * @returns {Window}
 */


function getWindow(element) {
  var ownerDocument = element.ownerDocument;
  return ownerDocument ? ownerDocument.defaultView : window;
}

function attachToScrollParents(scrollParent, event, callback, scrollParents) {
  var isBody = scrollParent.nodeName === 'BODY';
  var target = isBody ? scrollParent.ownerDocument.defaultView : scrollParent;
  target.addEventListener(event, callback, {
    passive: true
  });

  if (!isBody) {
    attachToScrollParents(getScrollParent(target.parentNode), event, callback, scrollParents);
  }

  scrollParents.push(target);
}
/**
 * Setup needed event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */


function setupEventListeners(reference, options, state, updateBound) {
  // Resize event listener on window
  state.updateBound = updateBound;
  getWindow(reference).addEventListener('resize', state.updateBound, {
    passive: true
  }); // Scroll event listener on scroll parents

  var scrollElement = getScrollParent(reference);
  attachToScrollParents(scrollElement, 'scroll', state.updateBound, state.scrollParents);
  state.scrollElement = scrollElement;
  state.eventsEnabled = true;
  return state;
}
/**
 * It will add resize/scroll events and start recalculating
 * position of the popper element when they are triggered.
 * @method
 * @memberof Popper
 */


function enableEventListeners() {
  if (!this.state.eventsEnabled) {
    this.state = setupEventListeners(this.reference, this.options, this.state, this.scheduleUpdate);
  }
}
/**
 * Remove event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */


function removeEventListeners(reference, state) {
  // Remove resize event listener on window
  getWindow(reference).removeEventListener('resize', state.updateBound); // Remove scroll event listener on scroll parents

  state.scrollParents.forEach(function (target) {
    target.removeEventListener('scroll', state.updateBound);
  }); // Reset state

  state.updateBound = null;
  state.scrollParents = [];
  state.scrollElement = null;
  state.eventsEnabled = false;
  return state;
}
/**
 * It will remove resize/scroll events and won't recalculate popper position
 * when they are triggered. It also won't trigger `onUpdate` callback anymore,
 * unless you call `update` method manually.
 * @method
 * @memberof Popper
 */


function disableEventListeners() {
  if (this.state.eventsEnabled) {
    cancelAnimationFrame(this.scheduleUpdate);
    this.state = removeEventListeners(this.reference, this.state);
  }
}
/**
 * Tells if a given input is a number
 * @method
 * @memberof Popper.Utils
 * @param {*} input to check
 * @return {Boolean}
 */


function isNumeric(n) {
  return n !== '' && !isNaN(parseFloat(n)) && isFinite(n);
}
/**
 * Set the style to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the style to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */


function setStyles(element, styles) {
  Object.keys(styles).forEach(function (prop) {
    var unit = ''; // add unit if the value is numeric and is one of the following

    if (['width', 'height', 'top', 'right', 'bottom', 'left'].indexOf(prop) !== -1 && isNumeric(styles[prop])) {
      unit = 'px';
    }

    element.style[prop] = styles[prop] + unit;
  });
}
/**
 * Set the attributes to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the attributes to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */


function setAttributes(element, attributes) {
  Object.keys(attributes).forEach(function (prop) {
    var value = attributes[prop];

    if (value !== false) {
      element.setAttribute(prop, attributes[prop]);
    } else {
      element.removeAttribute(prop);
    }
  });
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} data.styles - List of style properties - values to apply to popper element
 * @argument {Object} data.attributes - List of attribute properties - values to apply to popper element
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The same data object
 */


function applyStyle(data) {
  // any property present in `data.styles` will be applied to the popper,
  // in this way we can make the 3rd party modifiers add custom styles to it
  // Be aware, modifiers could override the properties defined in the previous
  // lines of this modifier!
  setStyles(data.instance.popper, data.styles); // any property present in `data.attributes` will be applied to the popper,
  // they will be set as HTML attributes of the element

  setAttributes(data.instance.popper, data.attributes); // if arrowElement is defined and arrowStyles has some properties

  if (data.arrowElement && Object.keys(data.arrowStyles).length) {
    setStyles(data.arrowElement, data.arrowStyles);
  }

  return data;
}
/**
 * Set the x-placement attribute before everything else because it could be used
 * to add margins to the popper margins needs to be calculated to get the
 * correct popper offsets.
 * @method
 * @memberof Popper.modifiers
 * @param {HTMLElement} reference - The reference element used to position the popper
 * @param {HTMLElement} popper - The HTML element used as popper
 * @param {Object} options - Popper.js options
 */


function applyStyleOnLoad(reference, popper, options, modifierOptions, state) {
  // compute reference element offsets
  var referenceOffsets = getReferenceOffsets(state, popper, reference, options.positionFixed); // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value

  var placement = computeAutoPlacement(options.placement, referenceOffsets, popper, reference, options.modifiers.flip.boundariesElement, options.modifiers.flip.padding);
  popper.setAttribute('x-placement', placement); // Apply `position` to popper before anything else because
  // without the position applied we can't guarantee correct computations

  setStyles(popper, {
    position: options.positionFixed ? 'fixed' : 'absolute'
  });
  return options;
}
/**
 * @function
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Boolean} shouldRound - If the offsets should be rounded at all
 * @returns {Object} The popper's position offsets rounded
 *
 * The tale of pixel-perfect positioning. It's still not 100% perfect, but as
 * good as it can be within reason.
 * Discussion here: https://github.com/FezVrasta/popper.js/pull/715
 *
 * Low DPI screens cause a popper to be blurry if not using full pixels (Safari
 * as well on High DPI screens).
 *
 * Firefox prefers no rounding for positioning and does not have blurriness on
 * high DPI screens.
 *
 * Only horizontal placement and left/right values need to be considered.
 */


function getRoundedOffsets(data, shouldRound) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var round = Math.round,
      floor = Math.floor;

  var noRound = function noRound(v) {
    return v;
  };

  var referenceWidth = round(reference.width);
  var popperWidth = round(popper.width);
  var isVertical = ['left', 'right'].indexOf(data.placement) !== -1;
  var isVariation = data.placement.indexOf('-') !== -1;
  var sameWidthParity = referenceWidth % 2 === popperWidth % 2;
  var bothOddWidth = referenceWidth % 2 === 1 && popperWidth % 2 === 1;
  var horizontalToInteger = !shouldRound ? noRound : isVertical || isVariation || sameWidthParity ? round : floor;
  var verticalToInteger = !shouldRound ? noRound : round;
  return {
    left: horizontalToInteger(bothOddWidth && !isVariation && shouldRound ? popper.left - 1 : popper.left),
    top: verticalToInteger(popper.top),
    bottom: verticalToInteger(popper.bottom),
    right: horizontalToInteger(popper.right)
  };
}

var isFirefox = isBrowser && /Firefox/i.test(navigator.userAgent);
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */

function computeStyle(data, options) {
  var x = options.x,
      y = options.y;
  var popper = data.offsets.popper; // Remove this legacy support in Popper.js v2

  var legacyGpuAccelerationOption = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'applyStyle';
  }).gpuAcceleration;

  if (legacyGpuAccelerationOption !== undefined) {
    console.warn('WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!');
  }

  var gpuAcceleration = legacyGpuAccelerationOption !== undefined ? legacyGpuAccelerationOption : options.gpuAcceleration;
  var offsetParent = getOffsetParent(data.instance.popper);
  var offsetParentRect = getBoundingClientRect(offsetParent); // Styles

  var styles = {
    position: popper.position
  };
  var offsets = getRoundedOffsets(data, window.devicePixelRatio < 2 || !isFirefox);
  var sideA = x === 'bottom' ? 'top' : 'bottom';
  var sideB = y === 'right' ? 'left' : 'right'; // if gpuAcceleration is set to `true` and transform is supported,
  //  we use `translate3d` to apply the position to the popper we
  // automatically use the supported prefixed version if needed

  var prefixedProperty = getSupportedPropertyName('transform'); // now, let's make a step back and look at this code closely (wtf?)
  // If the content of the popper grows once it's been positioned, it
  // may happen that the popper gets misplaced because of the new content
  // overflowing its reference element
  // To avoid this problem, we provide two options (x and y), which allow
  // the consumer to define the offset origin.
  // If we position a popper on top of a reference element, we can set
  // `x` to `top` to make the popper grow towards its top instead of
  // its bottom.

  var left = void 0,
      top = void 0;

  if (sideA === 'bottom') {
    // when offsetParent is <html> the positioning is relative to the bottom of the screen (excluding the scrollbar)
    // and not the bottom of the html element
    if (offsetParent.nodeName === 'HTML') {
      top = -offsetParent.clientHeight + offsets.bottom;
    } else {
      top = -offsetParentRect.height + offsets.bottom;
    }
  } else {
    top = offsets.top;
  }

  if (sideB === 'right') {
    if (offsetParent.nodeName === 'HTML') {
      left = -offsetParent.clientWidth + offsets.right;
    } else {
      left = -offsetParentRect.width + offsets.right;
    }
  } else {
    left = offsets.left;
  }

  if (gpuAcceleration && prefixedProperty) {
    styles[prefixedProperty] = 'translate3d(' + left + 'px, ' + top + 'px, 0)';
    styles[sideA] = 0;
    styles[sideB] = 0;
    styles.willChange = 'transform';
  } else {
    // othwerise, we use the standard `top`, `left`, `bottom` and `right` properties
    var invertTop = sideA === 'bottom' ? -1 : 1;
    var invertLeft = sideB === 'right' ? -1 : 1;
    styles[sideA] = top * invertTop;
    styles[sideB] = left * invertLeft;
    styles.willChange = sideA + ', ' + sideB;
  } // Attributes


  var attributes = {
    'x-placement': data.placement
  }; // Update `data` attributes, styles and arrowStyles

  data.attributes = _extends({}, attributes, data.attributes);
  data.styles = _extends({}, styles, data.styles);
  data.arrowStyles = _extends({}, data.offsets.arrow, data.arrowStyles);
  return data;
}
/**
 * Helper used to know if the given modifier depends from another one.<br />
 * It checks if the needed modifier is listed and enabled.
 * @method
 * @memberof Popper.Utils
 * @param {Array} modifiers - list of modifiers
 * @param {String} requestingName - name of requesting modifier
 * @param {String} requestedName - name of requested modifier
 * @returns {Boolean}
 */


function isModifierRequired(modifiers, requestingName, requestedName) {
  var requesting = find(modifiers, function (_ref) {
    var name = _ref.name;
    return name === requestingName;
  });
  var isRequired = !!requesting && modifiers.some(function (modifier) {
    return modifier.name === requestedName && modifier.enabled && modifier.order < requesting.order;
  });

  if (!isRequired) {
    var _requesting = '`' + requestingName + '`';

    var requested = '`' + requestedName + '`';
    console.warn(requested + ' modifier is required by ' + _requesting + ' modifier in order to work, be sure to include it before ' + _requesting + '!');
  }

  return isRequired;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function arrow(data, options) {
  var _data$offsets$arrow; // arrow depends on keepTogether in order to work


  if (!isModifierRequired(data.instance.modifiers, 'arrow', 'keepTogether')) {
    return data;
  }

  var arrowElement = options.element; // if arrowElement is a string, suppose it's a CSS selector

  if (typeof arrowElement === 'string') {
    arrowElement = data.instance.popper.querySelector(arrowElement); // if arrowElement is not found, don't run the modifier

    if (!arrowElement) {
      return data;
    }
  } else {
    // if the arrowElement isn't a query selector we must check that the
    // provided DOM node is child of its popper node
    if (!data.instance.popper.contains(arrowElement)) {
      console.warn('WARNING: `arrow.element` must be child of its popper element!');
      return data;
    }
  }

  var placement = data.placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var isVertical = ['left', 'right'].indexOf(placement) !== -1;
  var len = isVertical ? 'height' : 'width';
  var sideCapitalized = isVertical ? 'Top' : 'Left';
  var side = sideCapitalized.toLowerCase();
  var altSide = isVertical ? 'left' : 'top';
  var opSide = isVertical ? 'bottom' : 'right';
  var arrowElementSize = getOuterSizes(arrowElement)[len]; //
  // extends keepTogether behavior making sure the popper and its
  // reference have enough pixels in conjunction
  //
  // top/left side

  if (reference[opSide] - arrowElementSize < popper[side]) {
    data.offsets.popper[side] -= popper[side] - (reference[opSide] - arrowElementSize);
  } // bottom/right side


  if (reference[side] + arrowElementSize > popper[opSide]) {
    data.offsets.popper[side] += reference[side] + arrowElementSize - popper[opSide];
  }

  data.offsets.popper = getClientRect(data.offsets.popper); // compute center of the popper

  var center = reference[side] + reference[len] / 2 - arrowElementSize / 2; // Compute the sideValue using the updated popper offsets
  // take popper margin in account because we don't have this info available

  var css = getStyleComputedProperty(data.instance.popper);
  var popperMarginSide = parseFloat(css['margin' + sideCapitalized]);
  var popperBorderSide = parseFloat(css['border' + sideCapitalized + 'Width']);
  var sideValue = center - data.offsets.popper[side] - popperMarginSide - popperBorderSide; // prevent arrowElement from being placed not contiguously to its popper

  sideValue = Math.max(Math.min(popper[len] - arrowElementSize, sideValue), 0);
  data.arrowElement = arrowElement;
  data.offsets.arrow = (_data$offsets$arrow = {}, defineProperty(_data$offsets$arrow, side, Math.round(sideValue)), defineProperty(_data$offsets$arrow, altSide, ''), _data$offsets$arrow);
  return data;
}
/**
 * Get the opposite placement variation of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement variation
 * @returns {String} flipped placement variation
 */


function getOppositeVariation(variation) {
  if (variation === 'end') {
    return 'start';
  } else if (variation === 'start') {
    return 'end';
  }

  return variation;
}
/**
 * List of accepted placements to use as values of the `placement` option.<br />
 * Valid placements are:
 * - `auto`
 * - `top`
 * - `right`
 * - `bottom`
 * - `left`
 *
 * Each placement can have a variation from this list:
 * - `-start`
 * - `-end`
 *
 * Variations are interpreted easily if you think of them as the left to right
 * written languages. Horizontally (`top` and `bottom`), `start` is left and `end`
 * is right.<br />
 * Vertically (`left` and `right`), `start` is top and `end` is bottom.
 *
 * Some valid examples are:
 * - `top-end` (on top of reference, right aligned)
 * - `right-start` (on right of reference, top aligned)
 * - `bottom` (on bottom, centered)
 * - `auto-end` (on the side with more space available, alignment depends by placement)
 *
 * @static
 * @type {Array}
 * @enum {String}
 * @readonly
 * @method placements
 * @memberof Popper
 */


var placements = ['auto-start', 'auto', 'auto-end', 'top-start', 'top', 'top-end', 'right-start', 'right', 'right-end', 'bottom-end', 'bottom', 'bottom-start', 'left-end', 'left', 'left-start']; // Get rid of `auto` `auto-start` and `auto-end`

var validPlacements = placements.slice(3);
/**
 * Given an initial placement, returns all the subsequent placements
 * clockwise (or counter-clockwise).
 *
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement - A valid placement (it accepts variations)
 * @argument {Boolean} counter - Set to true to walk the placements counterclockwise
 * @returns {Array} placements including their variations
 */

function clockwise(placement) {
  var counter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var index = validPlacements.indexOf(placement);
  var arr = validPlacements.slice(index + 1).concat(validPlacements.slice(0, index));
  return counter ? arr.reverse() : arr;
}

var BEHAVIORS = {
  FLIP: 'flip',
  CLOCKWISE: 'clockwise',
  COUNTERCLOCKWISE: 'counterclockwise'
};
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */

function flip(data, options) {
  // if `inner` modifier is enabled, we can't use the `flip` modifier
  if (isModifierEnabled(data.instance.modifiers, 'inner')) {
    return data;
  }

  if (data.flipped && data.placement === data.originalPlacement) {
    // seems like flip is trying to loop, probably there's not enough space on any of the flippable sides
    return data;
  }

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, options.boundariesElement, data.positionFixed);
  var placement = data.placement.split('-')[0];
  var placementOpposite = getOppositePlacement(placement);
  var variation = data.placement.split('-')[1] || '';
  var flipOrder = [];

  switch (options.behavior) {
    case BEHAVIORS.FLIP:
      flipOrder = [placement, placementOpposite];
      break;

    case BEHAVIORS.CLOCKWISE:
      flipOrder = clockwise(placement);
      break;

    case BEHAVIORS.COUNTERCLOCKWISE:
      flipOrder = clockwise(placement, true);
      break;

    default:
      flipOrder = options.behavior;
  }

  flipOrder.forEach(function (step, index) {
    if (placement !== step || flipOrder.length === index + 1) {
      return data;
    }

    placement = data.placement.split('-')[0];
    placementOpposite = getOppositePlacement(placement);
    var popperOffsets = data.offsets.popper;
    var refOffsets = data.offsets.reference; // using floor because the reference offsets may contain decimals we are not going to consider here

    var floor = Math.floor;
    var overlapsRef = placement === 'left' && floor(popperOffsets.right) > floor(refOffsets.left) || placement === 'right' && floor(popperOffsets.left) < floor(refOffsets.right) || placement === 'top' && floor(popperOffsets.bottom) > floor(refOffsets.top) || placement === 'bottom' && floor(popperOffsets.top) < floor(refOffsets.bottom);
    var overflowsLeft = floor(popperOffsets.left) < floor(boundaries.left);
    var overflowsRight = floor(popperOffsets.right) > floor(boundaries.right);
    var overflowsTop = floor(popperOffsets.top) < floor(boundaries.top);
    var overflowsBottom = floor(popperOffsets.bottom) > floor(boundaries.bottom);
    var overflowsBoundaries = placement === 'left' && overflowsLeft || placement === 'right' && overflowsRight || placement === 'top' && overflowsTop || placement === 'bottom' && overflowsBottom; // flip the variation if required

    var isVertical = ['top', 'bottom'].indexOf(placement) !== -1; // flips variation if reference element overflows boundaries

    var flippedVariationByRef = !!options.flipVariations && (isVertical && variation === 'start' && overflowsLeft || isVertical && variation === 'end' && overflowsRight || !isVertical && variation === 'start' && overflowsTop || !isVertical && variation === 'end' && overflowsBottom); // flips variation if popper content overflows boundaries

    var flippedVariationByContent = !!options.flipVariationsByContent && (isVertical && variation === 'start' && overflowsRight || isVertical && variation === 'end' && overflowsLeft || !isVertical && variation === 'start' && overflowsBottom || !isVertical && variation === 'end' && overflowsTop);
    var flippedVariation = flippedVariationByRef || flippedVariationByContent;

    if (overlapsRef || overflowsBoundaries || flippedVariation) {
      // this boolean to detect any flip loop
      data.flipped = true;

      if (overlapsRef || overflowsBoundaries) {
        placement = flipOrder[index + 1];
      }

      if (flippedVariation) {
        variation = getOppositeVariation(variation);
      }

      data.placement = placement + (variation ? '-' + variation : ''); // this object contains `position`, we want to preserve it along with
      // any additional property we may add in the future

      data.offsets.popper = _extends({}, data.offsets.popper, getPopperOffsets(data.instance.popper, data.offsets.reference, data.placement));
      data = runModifiers(data.instance.modifiers, data, 'flip');
    }
  });
  return data;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function keepTogether(data) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var placement = data.placement.split('-')[0];
  var floor = Math.floor;
  var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;
  var side = isVertical ? 'right' : 'bottom';
  var opSide = isVertical ? 'left' : 'top';
  var measurement = isVertical ? 'width' : 'height';

  if (popper[side] < floor(reference[opSide])) {
    data.offsets.popper[opSide] = floor(reference[opSide]) - popper[measurement];
  }

  if (popper[opSide] > floor(reference[side])) {
    data.offsets.popper[opSide] = floor(reference[side]);
  }

  return data;
}
/**
 * Converts a string containing value + unit into a px value number
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} str - Value + unit string
 * @argument {String} measurement - `height` or `width`
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @returns {Number|String}
 * Value in pixels, or original string if no values were extracted
 */


function toValue(str, measurement, popperOffsets, referenceOffsets) {
  // separate value from unit
  var split = str.match(/((?:\-|\+)?\d*\.?\d*)(.*)/);
  var value = +split[1];
  var unit = split[2]; // If it's not a number it's an operator, I guess

  if (!value) {
    return str;
  }

  if (unit.indexOf('%') === 0) {
    var element = void 0;

    switch (unit) {
      case '%p':
        element = popperOffsets;
        break;

      case '%':
      case '%r':
      default:
        element = referenceOffsets;
    }

    var rect = getClientRect(element);
    return rect[measurement] / 100 * value;
  } else if (unit === 'vh' || unit === 'vw') {
    // if is a vh or vw, we calculate the size based on the viewport
    var size = void 0;

    if (unit === 'vh') {
      size = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    } else {
      size = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    }

    return size / 100 * value;
  } else {
    // if is an explicit pixel unit, we get rid of the unit and keep the value
    // if is an implicit unit, it's px, and we return just the value
    return value;
  }
}
/**
 * Parse an `offset` string to extrapolate `x` and `y` numeric offsets.
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} offset
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @argument {String} basePlacement
 * @returns {Array} a two cells array with x and y offsets in numbers
 */


function parseOffset(offset, popperOffsets, referenceOffsets, basePlacement) {
  var offsets = [0, 0]; // Use height if placement is left or right and index is 0 otherwise use width
  // in this way the first offset will use an axis and the second one
  // will use the other one

  var useHeight = ['right', 'left'].indexOf(basePlacement) !== -1; // Split the offset string to obtain a list of values and operands
  // The regex addresses values with the plus or minus sign in front (+10, -20, etc)

  var fragments = offset.split(/(\+|\-)/).map(function (frag) {
    return frag.trim();
  }); // Detect if the offset string contains a pair of values or a single one
  // they could be separated by comma or space

  var divider = fragments.indexOf(find(fragments, function (frag) {
    return frag.search(/,|\s/) !== -1;
  }));

  if (fragments[divider] && fragments[divider].indexOf(',') === -1) {
    console.warn('Offsets separated by white space(s) are deprecated, use a comma (,) instead.');
  } // If divider is found, we divide the list of values and operands to divide
  // them by ofset X and Y.


  var splitRegex = /\s*,\s*|\s+/;
  var ops = divider !== -1 ? [fragments.slice(0, divider).concat([fragments[divider].split(splitRegex)[0]]), [fragments[divider].split(splitRegex)[1]].concat(fragments.slice(divider + 1))] : [fragments]; // Convert the values with units to absolute pixels to allow our computations

  ops = ops.map(function (op, index) {
    // Most of the units rely on the orientation of the popper
    var measurement = (index === 1 ? !useHeight : useHeight) ? 'height' : 'width';
    var mergeWithPrevious = false;
    return op // This aggregates any `+` or `-` sign that aren't considered operators
    // e.g.: 10 + +5 => [10, +, +5]
    .reduce(function (a, b) {
      if (a[a.length - 1] === '' && ['+', '-'].indexOf(b) !== -1) {
        a[a.length - 1] = b;
        mergeWithPrevious = true;
        return a;
      } else if (mergeWithPrevious) {
        a[a.length - 1] += b;
        mergeWithPrevious = false;
        return a;
      } else {
        return a.concat(b);
      }
    }, []) // Here we convert the string values into number values (in px)
    .map(function (str) {
      return toValue(str, measurement, popperOffsets, referenceOffsets);
    });
  }); // Loop trough the offsets arrays and execute the operations

  ops.forEach(function (op, index) {
    op.forEach(function (frag, index2) {
      if (isNumeric(frag)) {
        offsets[index] += frag * (op[index2 - 1] === '-' ? -1 : 1);
      }
    });
  });
  return offsets;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @argument {Number|String} options.offset=0
 * The offset value as described in the modifier description
 * @returns {Object} The data object, properly modified
 */


function offset(data, _ref) {
  var offset = _ref.offset;
  var placement = data.placement,
      _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var basePlacement = placement.split('-')[0];
  var offsets = void 0;

  if (isNumeric(+offset)) {
    offsets = [+offset, 0];
  } else {
    offsets = parseOffset(offset, popper, reference, basePlacement);
  }

  if (basePlacement === 'left') {
    popper.top += offsets[0];
    popper.left -= offsets[1];
  } else if (basePlacement === 'right') {
    popper.top += offsets[0];
    popper.left += offsets[1];
  } else if (basePlacement === 'top') {
    popper.left += offsets[0];
    popper.top -= offsets[1];
  } else if (basePlacement === 'bottom') {
    popper.left += offsets[0];
    popper.top += offsets[1];
  }

  data.popper = popper;
  return data;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function preventOverflow(data, options) {
  var boundariesElement = options.boundariesElement || getOffsetParent(data.instance.popper); // If offsetParent is the reference element, we really want to
  // go one step up and use the next offsetParent as reference to
  // avoid to make this modifier completely useless and look like broken

  if (data.instance.reference === boundariesElement) {
    boundariesElement = getOffsetParent(boundariesElement);
  } // NOTE: DOM access here
  // resets the popper's position so that the document size can be calculated excluding
  // the size of the popper element itself


  var transformProp = getSupportedPropertyName('transform');
  var popperStyles = data.instance.popper.style; // assignment to help minification

  var top = popperStyles.top,
      left = popperStyles.left,
      transform = popperStyles[transformProp];
  popperStyles.top = '';
  popperStyles.left = '';
  popperStyles[transformProp] = '';
  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, boundariesElement, data.positionFixed); // NOTE: DOM access here
  // restores the original style properties after the offsets have been computed

  popperStyles.top = top;
  popperStyles.left = left;
  popperStyles[transformProp] = transform;
  options.boundaries = boundaries;
  var order = options.priority;
  var popper = data.offsets.popper;
  var check = {
    primary: function primary(placement) {
      var value = popper[placement];

      if (popper[placement] < boundaries[placement] && !options.escapeWithReference) {
        value = Math.max(popper[placement], boundaries[placement]);
      }

      return defineProperty({}, placement, value);
    },
    secondary: function secondary(placement) {
      var mainSide = placement === 'right' ? 'left' : 'top';
      var value = popper[mainSide];

      if (popper[placement] > boundaries[placement] && !options.escapeWithReference) {
        value = Math.min(popper[mainSide], boundaries[placement] - (placement === 'right' ? popper.width : popper.height));
      }

      return defineProperty({}, mainSide, value);
    }
  };
  order.forEach(function (placement) {
    var side = ['left', 'top'].indexOf(placement) !== -1 ? 'primary' : 'secondary';
    popper = _extends({}, popper, check[side](placement));
  });
  data.offsets.popper = popper;
  return data;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function shift(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var shiftvariation = placement.split('-')[1]; // if shift shiftvariation is specified, run the modifier

  if (shiftvariation) {
    var _data$offsets = data.offsets,
        reference = _data$offsets.reference,
        popper = _data$offsets.popper;
    var isVertical = ['bottom', 'top'].indexOf(basePlacement) !== -1;
    var side = isVertical ? 'left' : 'top';
    var measurement = isVertical ? 'width' : 'height';
    var shiftOffsets = {
      start: defineProperty({}, side, reference[side]),
      end: defineProperty({}, side, reference[side] + reference[measurement] - popper[measurement])
    };
    data.offsets.popper = _extends({}, popper, shiftOffsets[shiftvariation]);
  }

  return data;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function hide(data) {
  if (!isModifierRequired(data.instance.modifiers, 'hide', 'preventOverflow')) {
    return data;
  }

  var refRect = data.offsets.reference;
  var bound = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'preventOverflow';
  }).boundaries;

  if (refRect.bottom < bound.top || refRect.left > bound.right || refRect.top > bound.bottom || refRect.right < bound.left) {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === true) {
      return data;
    }

    data.hide = true;
    data.attributes['x-out-of-boundaries'] = '';
  } else {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === false) {
      return data;
    }

    data.hide = false;
    data.attributes['x-out-of-boundaries'] = false;
  }

  return data;
}
/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */


function inner(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var isHoriz = ['left', 'right'].indexOf(basePlacement) !== -1;
  var subtractLength = ['top', 'left'].indexOf(basePlacement) === -1;
  popper[isHoriz ? 'left' : 'top'] = reference[basePlacement] - (subtractLength ? popper[isHoriz ? 'width' : 'height'] : 0);
  data.placement = getOppositePlacement(placement);
  data.offsets.popper = getClientRect(popper);
  return data;
}
/**
 * Modifier function, each modifier can have a function of this type assigned
 * to its `fn` property.<br />
 * These functions will be called on each update, this means that you must
 * make sure they are performant enough to avoid performance bottlenecks.
 *
 * @function ModifierFn
 * @argument {dataObject} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {dataObject} The data object, properly modified
 */

/**
 * Modifiers are plugins used to alter the behavior of your poppers.<br />
 * Popper.js uses a set of 9 modifiers to provide all the basic functionalities
 * needed by the library.
 *
 * Usually you don't want to override the `order`, `fn` and `onLoad` props.
 * All the other properties are configurations that could be tweaked.
 * @namespace modifiers
 */


var modifiers = {
  /**
   * Modifier used to shift the popper on the start or end of its reference
   * element.<br />
   * It will read the variation of the `placement` property.<br />
   * It can be one either `-end` or `-start`.
   * @memberof modifiers
   * @inner
   */
  shift: {
    /** @prop {number} order=100 - Index used to define the order of execution */
    order: 100,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: shift
  },

  /**
   * The `offset` modifier can shift your popper on both its axis.
   *
   * It accepts the following units:
   * - `px` or unit-less, interpreted as pixels
   * - `%` or `%r`, percentage relative to the length of the reference element
   * - `%p`, percentage relative to the length of the popper element
   * - `vw`, CSS viewport width unit
   * - `vh`, CSS viewport height unit
   *
   * For length is intended the main axis relative to the placement of the popper.<br />
   * This means that if the placement is `top` or `bottom`, the length will be the
   * `width`. In case of `left` or `right`, it will be the `height`.
   *
   * You can provide a single value (as `Number` or `String`), or a pair of values
   * as `String` divided by a comma or one (or more) white spaces.<br />
   * The latter is a deprecated method because it leads to confusion and will be
   * removed in v2.<br />
   * Additionally, it accepts additions and subtractions between different units.
   * Note that multiplications and divisions aren't supported.
   *
   * Valid examples are:
   * ```
   * 10
   * '10%'
   * '10, 10'
   * '10%, 10'
   * '10 + 10%'
   * '10 - 5vh + 3%'
   * '-10px + 5vh, 5px - 6%'
   * ```
   * > **NB**: If you desire to apply offsets to your poppers in a way that may make them overlap
   * > with their reference element, unfortunately, you will have to disable the `flip` modifier.
   * > You can read more on this at this [issue](https://github.com/FezVrasta/popper.js/issues/373).
   *
   * @memberof modifiers
   * @inner
   */
  offset: {
    /** @prop {number} order=200 - Index used to define the order of execution */
    order: 200,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: offset,

    /** @prop {Number|String} offset=0
     * The offset value as described in the modifier description
     */
    offset: 0
  },

  /**
   * Modifier used to prevent the popper from being positioned outside the boundary.
   *
   * A scenario exists where the reference itself is not within the boundaries.<br />
   * We can say it has "escaped the boundaries" — or just "escaped".<br />
   * In this case we need to decide whether the popper should either:
   *
   * - detach from the reference and remain "trapped" in the boundaries, or
   * - if it should ignore the boundary and "escape with its reference"
   *
   * When `escapeWithReference` is set to`true` and reference is completely
   * outside its boundaries, the popper will overflow (or completely leave)
   * the boundaries in order to remain attached to the edge of the reference.
   *
   * @memberof modifiers
   * @inner
   */
  preventOverflow: {
    /** @prop {number} order=300 - Index used to define the order of execution */
    order: 300,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: preventOverflow,

    /**
     * @prop {Array} [priority=['left','right','top','bottom']]
     * Popper will try to prevent overflow following these priorities by default,
     * then, it could overflow on the left and on top of the `boundariesElement`
     */
    priority: ['left', 'right', 'top', 'bottom'],

    /**
     * @prop {number} padding=5
     * Amount of pixel used to define a minimum distance between the boundaries
     * and the popper. This makes sure the popper always has a little padding
     * between the edges of its container
     */
    padding: 5,

    /**
     * @prop {String|HTMLElement} boundariesElement='scrollParent'
     * Boundaries used by the modifier. Can be `scrollParent`, `window`,
     * `viewport` or any DOM element.
     */
    boundariesElement: 'scrollParent'
  },

  /**
   * Modifier used to make sure the reference and its popper stay near each other
   * without leaving any gap between the two. Especially useful when the arrow is
   * enabled and you want to ensure that it points to its reference element.
   * It cares only about the first axis. You can still have poppers with margin
   * between the popper and its reference element.
   * @memberof modifiers
   * @inner
   */
  keepTogether: {
    /** @prop {number} order=400 - Index used to define the order of execution */
    order: 400,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: keepTogether
  },

  /**
   * This modifier is used to move the `arrowElement` of the popper to make
   * sure it is positioned between the reference element and its popper element.
   * It will read the outer size of the `arrowElement` node to detect how many
   * pixels of conjunction are needed.
   *
   * It has no effect if no `arrowElement` is provided.
   * @memberof modifiers
   * @inner
   */
  arrow: {
    /** @prop {number} order=500 - Index used to define the order of execution */
    order: 500,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: arrow,

    /** @prop {String|HTMLElement} element='[x-arrow]' - Selector or node used as arrow */
    element: '[x-arrow]'
  },

  /**
   * Modifier used to flip the popper's placement when it starts to overlap its
   * reference element.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   *
   * **NOTE:** this modifier will interrupt the current update cycle and will
   * restart it if it detects the need to flip the placement.
   * @memberof modifiers
   * @inner
   */
  flip: {
    /** @prop {number} order=600 - Index used to define the order of execution */
    order: 600,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: flip,

    /**
     * @prop {String|Array} behavior='flip'
     * The behavior used to change the popper's placement. It can be one of
     * `flip`, `clockwise`, `counterclockwise` or an array with a list of valid
     * placements (with optional variations)
     */
    behavior: 'flip',

    /**
     * @prop {number} padding=5
     * The popper will flip if it hits the edges of the `boundariesElement`
     */
    padding: 5,

    /**
     * @prop {String|HTMLElement} boundariesElement='viewport'
     * The element which will define the boundaries of the popper position.
     * The popper will never be placed outside of the defined boundaries
     * (except if `keepTogether` is enabled)
     */
    boundariesElement: 'viewport',

    /**
     * @prop {Boolean} flipVariations=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the reference element overlaps its boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariations: false,

    /**
     * @prop {Boolean} flipVariationsByContent=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the popper element overlaps its reference boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariationsByContent: false
  },

  /**
   * Modifier used to make the popper flow toward the inner of the reference element.
   * By default, when this modifier is disabled, the popper will be placed outside
   * the reference element.
   * @memberof modifiers
   * @inner
   */
  inner: {
    /** @prop {number} order=700 - Index used to define the order of execution */
    order: 700,

    /** @prop {Boolean} enabled=false - Whether the modifier is enabled or not */
    enabled: false,

    /** @prop {ModifierFn} */
    fn: inner
  },

  /**
   * Modifier used to hide the popper when its reference element is outside of the
   * popper boundaries. It will set a `x-out-of-boundaries` attribute which can
   * be used to hide with a CSS selector the popper when its reference is
   * out of boundaries.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   * @memberof modifiers
   * @inner
   */
  hide: {
    /** @prop {number} order=800 - Index used to define the order of execution */
    order: 800,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: hide
  },

  /**
   * Computes the style that will be applied to the popper element to gets
   * properly positioned.
   *
   * Note that this modifier will not touch the DOM, it just prepares the styles
   * so that `applyStyle` modifier can apply it. This separation is useful
   * in case you need to replace `applyStyle` with a custom implementation.
   *
   * This modifier has `850` as `order` value to maintain backward compatibility
   * with previous versions of Popper.js. Expect the modifiers ordering method
   * to change in future major versions of the library.
   *
   * @memberof modifiers
   * @inner
   */
  computeStyle: {
    /** @prop {number} order=850 - Index used to define the order of execution */
    order: 850,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: computeStyle,

    /**
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: true,

    /**
     * @prop {string} [x='bottom']
     * Where to anchor the X axis (`bottom` or `top`). AKA X offset origin.
     * Change this if your popper should grow in a direction different from `bottom`
     */
    x: 'bottom',

    /**
     * @prop {string} [x='left']
     * Where to anchor the Y axis (`left` or `right`). AKA Y offset origin.
     * Change this if your popper should grow in a direction different from `right`
     */
    y: 'right'
  },

  /**
   * Applies the computed styles to the popper element.
   *
   * All the DOM manipulations are limited to this modifier. This is useful in case
   * you want to integrate Popper.js inside a framework or view library and you
   * want to delegate all the DOM manipulations to it.
   *
   * Note that if you disable this modifier, you must make sure the popper element
   * has its position set to `absolute` before Popper.js can do its work!
   *
   * Just disable this modifier and define your own to achieve the desired effect.
   *
   * @memberof modifiers
   * @inner
   */
  applyStyle: {
    /** @prop {number} order=900 - Index used to define the order of execution */
    order: 900,

    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,

    /** @prop {ModifierFn} */
    fn: applyStyle,

    /** @prop {Function} */
    onLoad: applyStyleOnLoad,

    /**
     * @deprecated since version 1.10.0, the property moved to `computeStyle` modifier
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: undefined
  }
};
/**
 * The `dataObject` is an object containing all the information used by Popper.js.
 * This object is passed to modifiers and to the `onCreate` and `onUpdate` callbacks.
 * @name dataObject
 * @property {Object} data.instance The Popper.js instance
 * @property {String} data.placement Placement applied to popper
 * @property {String} data.originalPlacement Placement originally defined on init
 * @property {Boolean} data.flipped True if popper has been flipped by flip modifier
 * @property {Boolean} data.hide True if the reference element is out of boundaries, useful to know when to hide the popper
 * @property {HTMLElement} data.arrowElement Node used as arrow by arrow modifier
 * @property {Object} data.styles Any CSS property defined here will be applied to the popper. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.arrowStyles Any CSS property defined here will be applied to the popper arrow. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.boundaries Offsets of the popper boundaries
 * @property {Object} data.offsets The measurements of popper, reference and arrow elements
 * @property {Object} data.offsets.popper `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.reference `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.arrow] `top` and `left` offsets, only one of them will be different from 0
 */

/**
 * Default options provided to Popper.js constructor.<br />
 * These can be overridden using the `options` argument of Popper.js.<br />
 * To override an option, simply pass an object with the same
 * structure of the `options` object, as the 3rd argument. For example:
 * ```
 * new Popper(ref, pop, {
 *   modifiers: {
 *     preventOverflow: { enabled: false }
 *   }
 * })
 * ```
 * @type {Object}
 * @static
 * @memberof Popper
 */

var Defaults = {
  /**
   * Popper's placement.
   * @prop {Popper.placements} placement='bottom'
   */
  placement: 'bottom',

  /**
   * Set this to true if you want popper to position it self in 'fixed' mode
   * @prop {Boolean} positionFixed=false
   */
  positionFixed: false,

  /**
   * Whether events (resize, scroll) are initially enabled.
   * @prop {Boolean} eventsEnabled=true
   */
  eventsEnabled: true,

  /**
   * Set to true if you want to automatically remove the popper when
   * you call the `destroy` method.
   * @prop {Boolean} removeOnDestroy=false
   */
  removeOnDestroy: false,

  /**
   * Callback called when the popper is created.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onCreate}
   */
  onCreate: function onCreate() {},

  /**
   * Callback called when the popper is updated. This callback is not called
   * on the initialization/creation of the popper, but only on subsequent
   * updates.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onUpdate}
   */
  onUpdate: function onUpdate() {},

  /**
   * List of modifiers used to modify the offsets before they are applied to the popper.
   * They provide most of the functionalities of Popper.js.
   * @prop {modifiers}
   */
  modifiers: modifiers
};
/**
 * @callback onCreate
 * @param {dataObject} data
 */

/**
 * @callback onUpdate
 * @param {dataObject} data
 */
// Utils
// Methods

var Popper = function () {
  /**
   * Creates a new Popper.js instance.
   * @class Popper
   * @param {Element|referenceObject} reference - The reference element used to position the popper
   * @param {Element} popper - The HTML / XML element used as the popper
   * @param {Object} options - Your custom options to override the ones defined in [Defaults](#defaults)
   * @return {Object} instance - The generated Popper.js instance
   */
  function Popper(reference, popper) {
    var _this = this;

    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    classCallCheck(this, Popper);

    this.scheduleUpdate = function () {
      return requestAnimationFrame(_this.update);
    }; // make update() debounced, so that it only runs at most once-per-tick


    this.update = debounce(this.update.bind(this)); // with {} we create a new object with the options inside it

    this.options = _extends({}, Popper.Defaults, options); // init state

    this.state = {
      isDestroyed: false,
      isCreated: false,
      scrollParents: []
    }; // get reference and popper elements (allow jQuery wrappers)

    this.reference = reference && reference.jquery ? reference[0] : reference;
    this.popper = popper && popper.jquery ? popper[0] : popper; // Deep merge modifiers options

    this.options.modifiers = {};
    Object.keys(_extends({}, Popper.Defaults.modifiers, options.modifiers)).forEach(function (name) {
      _this.options.modifiers[name] = _extends({}, Popper.Defaults.modifiers[name] || {}, options.modifiers ? options.modifiers[name] : {});
    }); // Refactoring modifiers' list (Object => Array)

    this.modifiers = Object.keys(this.options.modifiers).map(function (name) {
      return _extends({
        name: name
      }, _this.options.modifiers[name]);
    }) // sort the modifiers by order
    .sort(function (a, b) {
      return a.order - b.order;
    }); // modifiers have the ability to execute arbitrary code when Popper.js get inited
    // such code is executed in the same order of its modifier
    // they could add new properties to their options configuration
    // BE AWARE: don't add options to `options.modifiers.name` but to `modifierOptions`!

    this.modifiers.forEach(function (modifierOptions) {
      if (modifierOptions.enabled && isFunction(modifierOptions.onLoad)) {
        modifierOptions.onLoad(_this.reference, _this.popper, _this.options, modifierOptions, _this.state);
      }
    }); // fire the first update to position the popper in the right place

    this.update();
    var eventsEnabled = this.options.eventsEnabled;

    if (eventsEnabled) {
      // setup event listeners, they will take care of update the position in specific situations
      this.enableEventListeners();
    }

    this.state.eventsEnabled = eventsEnabled;
  } // We can't use class properties because they don't get listed in the
  // class prototype and break stuff like Sinon stubs


  createClass(Popper, [{
    key: 'update',
    value: function update$$1() {
      return update.call(this);
    }
  }, {
    key: 'destroy',
    value: function destroy$$1() {
      return destroy.call(this);
    }
  }, {
    key: 'enableEventListeners',
    value: function enableEventListeners$$1() {
      return enableEventListeners.call(this);
    }
  }, {
    key: 'disableEventListeners',
    value: function disableEventListeners$$1() {
      return disableEventListeners.call(this);
    }
    /**
     * Schedules an update. It will run on the next UI update available.
     * @method scheduleUpdate
     * @memberof Popper
     */

    /**
     * Collection of utilities useful when writing custom modifiers.
     * Starting from version 1.7, this method is available only if you
     * include `popper-utils.js` before `popper.js`.
     *
     * **DEPRECATION**: This way to access PopperUtils is deprecated
     * and will be removed in v2! Use the PopperUtils module directly instead.
     * Due to the high instability of the methods contained in Utils, we can't
     * guarantee them to follow semver. Use them at your own risk!
     * @static
     * @private
     * @type {Object}
     * @deprecated since version 1.8
     * @member Utils
     * @memberof Popper
     */

  }]);
  return Popper;
}();
/**
 * The `referenceObject` is an object that provides an interface compatible with Popper.js
 * and lets you use it as replacement of a real DOM node.<br />
 * You can use this method to position a popper relatively to a set of coordinates
 * in case you don't have a DOM node to use as reference.
 *
 * ```
 * new Popper(referenceObject, popperNode);
 * ```
 *
 * NB: This feature isn't supported in Internet Explorer 10.
 * @name referenceObject
 * @property {Function} data.getBoundingClientRect
 * A function that returns a set of coordinates compatible with the native `getBoundingClientRect` method.
 * @property {number} data.clientWidth
 * An ES6 getter that will return the width of the virtual reference element.
 * @property {number} data.clientHeight
 * An ES6 getter that will return the height of the virtual reference element.
 */


Popper.Utils = (typeof window !== 'undefined' ? window : global).PopperUtils;
Popper.placements = placements;
Popper.Defaults = Defaults;
/* harmony default export */ __webpack_exports__["default"] = (Popper);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

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