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
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
/******/ })
/************************************************************************/
/******/ ({

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
/* harmony import */ var _babel_loader_lib_index_js_ref_36_0_Avatar_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../babel-loader/lib??ref--36-0!./Avatar.js?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_36_0_Avatar_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&":
/*!**************************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& ***!
  \**************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../style-loader!../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--32-2!../../../../../../../../sass-loader/dist/cjs.js??ref--32-3!./Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Avatar_scss_vue_type_style_index_0_id_547cd8e4_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


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
/* harmony import */ var _babel_loader_lib_index_js_ref_36_0_Cropper_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../babel-loader/lib??ref--36-0!./Cropper.js?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_36_0_Cropper_js_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../style-loader!../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--32-2!../../../../../../../../sass-loader/dist/cjs.js??ref--32-3!./Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_32_2_sass_loader_dist_cjs_js_ref_32_3_Cropper_scss_vue_type_style_index_0_id_838c3fb0_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


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

/***/ "./node_modules/@concretecms/bedrock/node_modules/dropzone/dist/dropzone.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/dropzone/dist/dropzone.js ***!
  \**********************************************************************************/
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
        3099: function _(module) {
          module.exports = function (it) {
            if (typeof it != 'function') {
              throw TypeError(String(it) + ' is not a function');
            }

            return it;
          };
          /***/

        },

        /***/
        6077: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1223: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1530: function _(module, __unused_webpack_exports, __webpack_require__) {
          "use strict";

          var charAt = __webpack_require__(8710).charAt; // `AdvanceStringIndex` abstract operation
          // https://tc39.es/ecma262/#sec-advancestringindex


          module.exports = function (S, index, unicode) {
            return index + (unicode ? charAt(S, index).length : 1);
          };
          /***/

        },

        /***/
        5787: function _(module) {
          module.exports = function (it, Constructor, name) {
            if (!(it instanceof Constructor)) {
              throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
            }

            return it;
          };
          /***/

        },

        /***/
        9670: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        4019: function _(module) {
          module.exports = typeof ArrayBuffer !== 'undefined' && typeof DataView !== 'undefined';
          /***/
        },

        /***/
        260: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3331: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1048: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1285: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8533: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8457: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1318: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        2092: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6583: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1194: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9341: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3671: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        5417: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3411: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7072: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        4326: function _(module) {
          var toString = {}.toString;

          module.exports = function (it) {
            return toString.call(it).slice(8, -1);
          };
          /***/

        },

        /***/
        648: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9920: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8544: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        4994: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8880: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9114: function _(module) {
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
        6135: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        654: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9781: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        317: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8324: function _(module) {
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
        8113: function _(module, __unused_webpack_exports, __webpack_require__) {
          var getBuiltIn = __webpack_require__(5005);

          module.exports = getBuiltIn('navigator', 'userAgent') || '';
          /***/
        },

        /***/
        7392: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        748: function _(module) {
          // IE8- don't enum bug keys
          module.exports = ['constructor', 'hasOwnProperty', 'isPrototypeOf', 'propertyIsEnumerable', 'toLocaleString', 'toString', 'valueOf'];
          /***/
        },

        /***/
        2109: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7293: function _(module) {
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
        7007: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9974: function _(module, __unused_webpack_exports, __webpack_require__) {
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

            return function () {
              return fn.apply(that, arguments);
            };
          };
          /***/

        },

        /***/
        5005: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1246: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8554: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        647: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7854: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6656: function _(module) {
          var hasOwnProperty = {}.hasOwnProperty;

          module.exports = function (it, key) {
            return hasOwnProperty.call(it, key);
          };
          /***/

        },

        /***/
        3501: function _(module) {
          module.exports = {};
          /***/
        },

        /***/
        490: function _(module, __unused_webpack_exports, __webpack_require__) {
          var getBuiltIn = __webpack_require__(5005);

          module.exports = getBuiltIn('document', 'documentElement');
          /***/
        },

        /***/
        4664: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1179: function _(module) {
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
        8361: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9587: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        2788: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9909: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7659: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3157: function _(module, __unused_webpack_exports, __webpack_require__) {
          var classof = __webpack_require__(4326); // `IsArray` abstract operation
          // https://tc39.es/ecma262/#sec-isarray


          module.exports = Array.isArray || function isArray(arg) {
            return classof(arg) == 'Array';
          };
          /***/

        },

        /***/
        4705: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        111: function _(module) {
          module.exports = function (it) {
            return _typeof2(it) === 'object' ? it !== null : typeof it === 'function';
          };
          /***/

        },

        /***/
        1913: function _(module) {
          module.exports = false;
          /***/
        },

        /***/
        7850: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9212: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3383: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7497: function _(module) {
          module.exports = {};
          /***/
        },

        /***/
        133: function _(module, __unused_webpack_exports, __webpack_require__) {
          var fails = __webpack_require__(7293);

          module.exports = !!Object.getOwnPropertySymbols && !fails(function () {
            // Chrome 38 Symbol has incorrect toString conversion

            /* global Symbol -- required for testing */
            return !String(Symbol());
          });
          /***/
        },

        /***/
        590: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8536: function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var inspectSource = __webpack_require__(2788);

          var WeakMap = global.WeakMap;
          module.exports = typeof WeakMap === 'function' && /native code/.test(inspectSource(WeakMap));
          /***/
        },

        /***/
        1574: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        30: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6048: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3070: function _(__unused_webpack_module, exports, __webpack_require__) {
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
        1236: function _(__unused_webpack_module, exports, __webpack_require__) {
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
        8006: function _(__unused_webpack_module, exports, __webpack_require__) {
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
        5181: function _(__unused_webpack_module, exports) {
          exports.f = Object.getOwnPropertySymbols;
          /***/
        },

        /***/
        9518: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6324: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1956: function _(module, __unused_webpack_exports, __webpack_require__) {
          var internalObjectKeys = __webpack_require__(6324);

          var enumBugKeys = __webpack_require__(748); // `Object.keys` method
          // https://tc39.es/ecma262/#sec-object.keys


          module.exports = Object.keys || function keys(O) {
            return internalObjectKeys(O, enumBugKeys);
          };
          /***/

        },

        /***/
        5296: function _(__unused_webpack_module, exports) {
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
        7674: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        288: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3887: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        857: function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          module.exports = global;
          /***/
        },

        /***/
        2248: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1320: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7651: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        2261: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7066: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        2999: function _(__unused_webpack_module, exports, __webpack_require__) {
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
        4488: function _(module) {
          // `RequireObjectCoercible` abstract operation
          // https://tc39.es/ecma262/#sec-requireobjectcoercible
          module.exports = function (it) {
            if (it == undefined) throw TypeError("Can't call method on " + it);
            return it;
          };
          /***/

        },

        /***/
        3505: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6340: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8003: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6200: function _(module, __unused_webpack_exports, __webpack_require__) {
          var shared = __webpack_require__(2309);

          var uid = __webpack_require__(9711);

          var keys = shared('keys');

          module.exports = function (key) {
            return keys[key] || (keys[key] = uid(key));
          };
          /***/

        },

        /***/
        5465: function _(module, __unused_webpack_exports, __webpack_require__) {
          var global = __webpack_require__(7854);

          var setGlobal = __webpack_require__(3505);

          var SHARED = '__core-js_shared__';
          var store = global[SHARED] || setGlobal(SHARED, {});
          module.exports = store;
          /***/
        },

        /***/
        2309: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        6707: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        8710: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3197: function _(module) {
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

                  for (var k = base;; k += base) {
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
        6091: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3111: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1400: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7067: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        5656: function _(module, __unused_webpack_exports, __webpack_require__) {
          // toObject with fallback for non-array-like ES3 strings
          var IndexedObject = __webpack_require__(8361);

          var requireObjectCoercible = __webpack_require__(4488);

          module.exports = function (it) {
            return IndexedObject(requireObjectCoercible(it));
          };
          /***/

        },

        /***/
        9958: function _(module) {
          var ceil = Math.ceil;
          var floor = Math.floor; // `ToInteger` abstract operation
          // https://tc39.es/ecma262/#sec-tointeger

          module.exports = function (argument) {
            return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
          };
          /***/

        },

        /***/
        7466: function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          var min = Math.min; // `ToLength` abstract operation
          // https://tc39.es/ecma262/#sec-tolength

          module.exports = function (argument) {
            return argument > 0 ? min(toInteger(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
          };
          /***/

        },

        /***/
        7908: function _(module, __unused_webpack_exports, __webpack_require__) {
          var requireObjectCoercible = __webpack_require__(4488); // `ToObject` abstract operation
          // https://tc39.es/ecma262/#sec-toobject


          module.exports = function (argument) {
            return Object(requireObjectCoercible(argument));
          };
          /***/

        },

        /***/
        4590: function _(module, __unused_webpack_exports, __webpack_require__) {
          var toPositiveInteger = __webpack_require__(3002);

          module.exports = function (it, BYTES) {
            var offset = toPositiveInteger(it);
            if (offset % BYTES) throw RangeError('Wrong offset');
            return offset;
          };
          /***/

        },

        /***/
        3002: function _(module, __unused_webpack_exports, __webpack_require__) {
          var toInteger = __webpack_require__(9958);

          module.exports = function (it) {
            var result = toInteger(it);
            if (result < 0) throw RangeError("The argument can't be less than 0");
            return result;
          };
          /***/

        },

        /***/
        7593: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1694: function _(module, __unused_webpack_exports, __webpack_require__) {
          var wellKnownSymbol = __webpack_require__(5112);

          var TO_STRING_TAG = wellKnownSymbol('toStringTag');
          var test = {};
          test[TO_STRING_TAG] = 'z';
          module.exports = String(test) === '[object z]';
          /***/
        },

        /***/
        9843: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3832: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        3074: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        7321: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        9711: function _(module) {
          var id = 0;
          var postfix = Math.random();

          module.exports = function (key) {
            return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id + postfix).toString(36);
          };
          /***/

        },

        /***/
        3307: function _(module, __unused_webpack_exports, __webpack_require__) {
          var NATIVE_SYMBOL = __webpack_require__(133);

          module.exports = NATIVE_SYMBOL
          /* global Symbol -- safe */
          && !Symbol.sham && _typeof2(Symbol.iterator) == 'symbol';
          /***/
        },

        /***/
        5112: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1361: function _(module) {
          // a string of all valid unicode whitespaces
          module.exports = "\t\n\x0B\f\r \xA0\u1680\u2000\u2001\u2002" + "\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF";
          /***/
        },

        /***/
        8264: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2222: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        7327: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2772: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        6992: function _(module, __unused_webpack_exports, __webpack_require__) {
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
        1249: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        7042: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        561: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        8309: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        489: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        1539: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        4916: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        9714: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        8783: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        4723: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        5306: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3123: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3210: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2990: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        8927: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3105: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        5035: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        7174: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        4345: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2846: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        4731: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        7209: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        6319: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        8867: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        7789: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3739: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        4483: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        9368: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2056: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3462: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        678: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        7462: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3824: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        5021: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2974: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        5016: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        2472: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        4747: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        3948: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
        1637: function _(module, __unused_webpack_exports, __webpack_require__) {
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

          var URLSearchParamsConstructor = function URLSearchParams() {
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
        285: function _(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {
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
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/interactjs/dist/interact.min.js":
/*!****************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/interactjs/dist/interact.min.js ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/* interact.js 1.10.11 | https://interactjs.io/license */
!function (t) {
  "object" == ( false ? undefined : _typeof(exports)) && "undefined" != typeof module ? module.exports = t() :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(function () {
  var t = {};
  Object.defineProperty(t, "__esModule", {
    value: !0
  }), t["default"] = void 0, t["default"] = function (t) {
    return !(!t || !t.Window) && t instanceof t.Window;
  };
  var e = {};
  Object.defineProperty(e, "__esModule", {
    value: !0
  }), e.init = o, e.getWindow = function (e) {
    return (0, t["default"])(e) ? e : (e.ownerDocument || e).defaultView || r.window;
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
      var e = h["default"].Element,
          n = t.navigator || {};
      x.supportsTouch = "ontouchstart" in t || i["default"].func(t.DocumentTouch) && h["default"].document instanceof t.DocumentTouch, x.supportsPointerEvent = !1 !== n.pointerEnabled && !!h["default"].PointerEvent, x.isIOS = /iP(hone|od|ad)/.test(n.platform), x.isIOS7 = /iP(hone|od|ad)/.test(n.platform) && /OS 7[^\d]/.test(n.appVersion), x.isIe9 = /MSIE 9/.test(n.userAgent), x.isOperaMobile = "Opera" === n.appName && x.supportsTouch && /Presto/.test(n.userAgent), x.prefixedMatchesSelector = "matches" in e.prototype ? "matches" : "webkitMatchesSelector" in e.prototype ? "webkitMatchesSelector" : "mozMatchesSelector" in e.prototype ? "mozMatchesSelector" : "oMatchesSelector" in e.prototype ? "oMatchesSelector" : "msMatchesSelector", x.pEventTypes = x.supportsPointerEvent ? h["default"].PointerEvent === t.MSPointerEvent ? {
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
      } : null, x.wheelEvent = h["default"].document && "onmousewheel" in h["default"].document ? "mousewheel" : "wheel";
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
  },
      w = x;
  b["default"] = w;
  var _ = {};

  function P(t) {
    var e = t.parentNode;

    if (i["default"].docFrag(e)) {
      for (; (e = e.host) && i["default"].docFrag(e);) {
        ;
      }

      return e;
    }

    return e;
  }

  function O(t, n) {
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
      if (O(t, e)) return t;
      t = P(t);
    }

    return null;
  }, _.parentNode = P, _.matchesSelector = O, _.indexOfDeepestElement = function (t) {
    for (var n, r = [], o = 0; o < t.length; o++) {
      var i = t[o],
          a = t[n];
      if (i && o !== n) if (a) {
        var s = S(i),
            l = S(a);
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

            var d = [c[f - 1], c[f], r[f]];
            if (d[0]) for (var p = d[0].lastChild; p;) {
              if (p === d[1]) {
                n = o, r = c;
                break;
              }

              if (p === d[2]) break;
              p = p.previousSibling;
            }
          } else v = i, g = a, void 0, void 0, (parseInt(e.getWindow(v).getComputedStyle(v).zIndex, 10) || 0) >= (parseInt(e.getWindow(g).getComputedStyle(g).zIndex, 10) || 0) && (n = o);
        } else n = o;
      } else n = o;
    }

    var v, g;
    return n;
  }, _.matchesUpTo = function (t, e, n) {
    for (; i["default"].element(t);) {
      if (O(t, e)) return !0;
      if ((t = P(t)) === n) return O(t, e);
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
    for (var e = []; t;) {
      e.push(t), t = P(t);
    }

    return e;
  }, _.trySelector = function (t) {
    return !!i["default"].string(t) && (h["default"].document.querySelector(t), !0);
  };

  var S = function S(t) {
    return t.parentNode || t.host;
  };

  function E(t, e) {
    for (var n, r = [], o = t; (n = S(o)) && o !== e && n !== o.ownerDocument;) {
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
    var o,
        a = t;
    return i["default"].string(a) ? a = D(a, e, n) : i["default"].func(a) && (a = a.apply(void 0, function (t) {
      if (Array.isArray(t)) return I(t);
    }(o = r) || function (t) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t);
    }(o) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return I(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? I(t, e) : void 0;
      }
    }(o) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }())), i["default"].element(a) && (a = (0, _.getElementRect)(a)), a;
  }, k.rectToXY = function (t) {
    return t && {
      x: "x" in t ? t.x : t.left,
      y: "y" in t ? t.y : t.top
    };
  }, k.xywhToTlbr = function (t) {
    return !t || "left" in t && "top" in t || ((t = (0, j["default"])({}, t)).left = t.x || 0, t.top = t.y || 0, t.right = t.right || t.left + t.width, t.bottom = t.bottom || t.top + t.height), t;
  }, k.tlbrToXywh = function (t) {
    return !t || "x" in t && "y" in t || ((t = (0, j["default"])({}, t)).x = t.left || 0, t.y = t.top || 0, t.width = t.width || (t.right || 0) - t.x, t.height = t.height || (t.bottom || 0) - t.y), t;
  }, k.addEdges = function (t, e, n) {
    t.left && (e.left += n.x), t.right && (e.right += n.x), t.top && (e.top += n.y), t.bottom && (e.bottom += n.y), e.width = e.right - e.left, e.height = e.bottom - e.top;
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
  var R = {};

  function z(t) {
    return t.trim().split(/ +/);
  }

  Object.defineProperty(R, "__esModule", {
    value: !0
  }), R["default"] = function t(e, n, r) {
    if (r = r || {}, i["default"].string(e) && -1 !== e.search(" ") && (e = z(e)), i["default"].array(e)) return e.reduce(function (e, o) {
      return (0, j["default"])(e, t(o, n, r));
    }, r);
    if (i["default"].object(e) && (n = e, e = ""), i["default"].func(n)) r[e] = r[e] || [], r[e].push(n);else if (i["default"].array(n)) for (var o = 0; o < n.length; o++) {
      var a;
      a = n[o], t(e, a, r);
    } else if (i["default"].object(n)) for (var s in n) {
      var l = z(s).map(function (t) {
        return "".concat(e).concat(t);
      });
      t(l, n[s], r);
    }
    return r;
  };
  var C = {};
  Object.defineProperty(C, "__esModule", {
    value: !0
  }), C["default"] = void 0, C["default"] = function (t, e) {
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
  var B = {};

  function W(t) {
    return t instanceof h["default"].Event || t instanceof h["default"].Touch;
  }

  function L(t, e, n) {
    return t = t || "page", (n = n || {}).x = e[t + "X"], n.y = e[t + "Y"], n;
  }

  function U(t, e) {
    return e = e || {
      x: 0,
      y: 0
    }, b["default"].isOperaMobile && W(t) ? (L("screen", t, e), e.x += window.scrollX, e.y += window.scrollY) : L("page", t, e), e;
  }

  function V(t, e) {
    return e = e || {}, b["default"].isOperaMobile && W(t) ? L("screen", t, e) : L("client", t, e), e;
  }

  function N(t) {
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

  Object.defineProperty(B, "__esModule", {
    value: !0
  }), B.copyCoords = function (t, e) {
    t.page = t.page || {}, t.page.x = e.page.x, t.page.y = e.page.y, t.client = t.client || {}, t.client.x = e.client.x, t.client.y = e.client.y, t.timeStamp = e.timeStamp;
  }, B.setCoordDeltas = function (t, e, n) {
    t.page.x = n.page.x - e.page.x, t.page.y = n.page.y - e.page.y, t.client.x = n.client.x - e.client.x, t.client.y = n.client.y - e.client.y, t.timeStamp = n.timeStamp - e.timeStamp;
  }, B.setCoordVelocity = function (t, e) {
    var n = Math.max(e.timeStamp / 1e3, .001);
    t.page.x = e.page.x / n, t.page.y = e.page.y / n, t.client.x = e.client.x / n, t.client.y = e.client.y / n, t.timeStamp = n;
  }, B.setZeroCoords = function (t) {
    t.page.x = 0, t.page.y = 0, t.client.x = 0, t.client.y = 0;
  }, B.isNativePointer = W, B.getXY = L, B.getPageXY = U, B.getClientXY = V, B.getPointerId = function (t) {
    return i["default"].number(t.pointerId) ? t.pointerId : t.identifier;
  }, B.setCoords = function (t, e, n) {
    var r = e.length > 1 ? q(e) : e[0];
    U(r, t.page), V(r, t.client), t.timeStamp = n;
  }, B.getTouchPair = N, B.pointerAverage = q, B.touchBBox = function (t) {
    if (!t.length) return null;
    var e = N(t),
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
  }, B.touchDistance = function (t, e) {
    var n = e + "X",
        r = e + "Y",
        o = N(t),
        i = o[0][n] - o[1][n],
        a = o[0][r] - o[1][r];
    return (0, C["default"])(i, a);
  }, B.touchAngle = function (t, e) {
    var n = e + "X",
        r = e + "Y",
        o = N(t),
        i = o[1][n] - o[0][n],
        a = o[1][r] - o[0][r];
    return 180 * Math.atan2(a, i) / Math.PI;
  }, B.getPointerType = function (t) {
    return i["default"].string(t.pointerType) ? t.pointerType : i["default"].number(t.pointerType) ? [void 0, void 0, "touch", "pen", "mouse"][t.pointerType] : /touch/.test(t.type || "") || t instanceof h["default"].Touch ? "touch" : "mouse";
  }, B.getEventTargets = function (t) {
    var e = i["default"].func(t.composedPath) ? t.composedPath() : t.path;
    return [_.getActualElement(e ? e[0] : t.target), _.getActualElement(t.currentTarget)];
  }, B.newCoords = function () {
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
  }, B.coordsToEvent = function (t) {
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
  }, Object.defineProperty(B, "pointerExtend", {
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

  function H(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty($, "__esModule", {
    value: !0
  }), $.BaseEvent = void 0;

  var K = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), H(this, "type", void 0), H(this, "target", void 0), H(this, "currentTarget", void 0), H(this, "interactable", void 0), H(this, "_interaction", void 0), H(this, "timeStamp", void 0), H(this, "immediatePropagationStopped", !1), H(this, "propagationStopped", !1), this._interaction = e;
    }

    var e, n;
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
    }]) && G(e.prototype, n), t;
  }();

  $.BaseEvent = K, Object.defineProperty(K.prototype, "interaction", {
    get: function get() {
      return this._interaction._proxy;
    },
    set: function set() {}
  });
  var Z = {};
  Object.defineProperty(Z, "__esModule", {
    value: !0
  }), Z.find = Z.findIndex = Z.from = Z.merge = Z.remove = Z.contains = void 0, Z.contains = function (t, e) {
    return -1 !== t.indexOf(e);
  }, Z.remove = function (t, e) {
    return t.splice(t.indexOf(e), 1);
  };

  var J = function J(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      t.push(r);
    }

    return t;
  };

  Z.merge = J, Z.from = function (t) {
    return J([], t);
  };

  var Q = function Q(t, e) {
    for (var n = 0; n < t.length; n++) {
      if (e(t[n], n, t)) return n;
    }

    return -1;
  };

  Z.findIndex = Q, Z.find = function (t, e) {
    return t[Q(t, e)];
  };
  var tt = {};

  function et(t) {
    return (et = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function nt(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function rt(t, e) {
    return (rt = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function ot(t, e) {
    return !e || "object" !== et(e) && "function" != typeof e ? it(t) : e;
  }

  function it(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function at(t) {
    return (at = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function st(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(tt, "__esModule", {
    value: !0
  }), tt.DropEvent = void 0;

  var lt = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && rt(t, e);
    }(a, t);
    var e,
        n,
        r,
        o,
        i = (r = a, o = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }(), function () {
      var t,
          e = at(r);

      if (o) {
        var n = at(this).constructor;
        t = Reflect.construct(e, arguments, n);
      } else t = e.apply(this, arguments);

      return ot(this, t);
    });

    function a(t, e, n) {
      var r;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, a), st(it(r = i.call(this, e._interaction)), "target", void 0), st(it(r), "dropzone", void 0), st(it(r), "dragEvent", void 0), st(it(r), "relatedTarget", void 0), st(it(r), "draggable", void 0), st(it(r), "timeStamp", void 0), st(it(r), "propagationStopped", !1), st(it(r), "immediatePropagationStopped", !1);
      var o = "dragleave" === n ? t.prev : t.cur,
          s = o.element,
          l = o.dropzone;
      return r.type = n, r.target = s, r.currentTarget = s, r.dropzone = l, r.dragEvent = e, r.relatedTarget = e.target, r.draggable = e.interactable, r.timeStamp = e.timeStamp, r;
    }

    return e = a, (n = [{
      key: "reject",
      value: function value() {
        var t = this,
            e = this._interaction.dropState;
        if ("dropactivate" === this.type || this.dropzone && e.cur.dropzone === this.dropzone && e.cur.element === this.target) if (e.prev.dropzone = this.dropzone, e.prev.element = this.target, e.rejected = !0, e.events.enter = null, this.stopImmediatePropagation(), "dropactivate" === this.type) {
          var n = e.activeDrops,
              r = Z.findIndex(n, function (e) {
            var n = e.dropzone,
                r = e.element;
            return n === t.dropzone && r === t.target;
          });
          e.activeDrops.splice(r, 1);
          var o = new a(e, this.dragEvent, "dropdeactivate");
          o.dropzone = this.dropzone, o.target = this.target, this.dropzone.fire(o);
        } else this.dropzone.fire(new a(e, this.dragEvent, "dragleave"));
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
    }]) && nt(e.prototype, n), a;
  }($.BaseEvent);

  tt.DropEvent = lt;
  var ut = {};

  function ct(t, e) {
    for (var n = 0; n < t.slice().length; n++) {
      var r = t.slice()[n],
          o = r.dropzone,
          i = r.element;
      e.dropzone = o, e.target = i, o.fire(e), e.propagationStopped = e.immediatePropagationStopped = !1;
    }
  }

  function ft(t, e) {
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
              element: c,
              rect: a.getRect(c)
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

  function dt(t, e, n) {
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

  function pt(t, e, n) {
    var r = t.dropState,
        o = {
      enter: null,
      leave: null,
      activate: null,
      deactivate: null,
      move: null,
      drop: null
    };
    return "dragstart" === n.type && (o.activate = new tt.DropEvent(r, n, "dropactivate"), o.activate.target = null, o.activate.dropzone = null), "dragend" === n.type && (o.deactivate = new tt.DropEvent(r, n, "dropdeactivate"), o.deactivate.target = null, o.deactivate.dropzone = null), r.rejected || (r.cur.element !== r.prev.element && (r.prev.dropzone && (o.leave = new tt.DropEvent(r, n, "dragleave"), n.dragLeave = o.leave.target = r.prev.element, n.prevDropzone = o.leave.dropzone = r.prev.dropzone), r.cur.dropzone && (o.enter = new tt.DropEvent(r, n, "dragenter"), n.dragEnter = r.cur.element, n.dropzone = r.cur.dropzone)), "dragend" === n.type && r.cur.dropzone && (o.drop = new tt.DropEvent(r, n, "drop"), n.dropzone = r.cur.dropzone, n.relatedTarget = r.cur.element), "dragmove" === n.type && r.cur.dropzone && (o.move = new tt.DropEvent(r, n, "dropmove"), o.move.dragmove = n, n.dropzone = r.cur.dropzone)), o;
  }

  function vt(t, e) {
    var n = t.dropState,
        r = n.activeDrops,
        o = n.cur,
        i = n.prev;
    e.leave && i.dropzone.fire(e.leave), e.enter && o.dropzone.fire(e.enter), e.move && o.dropzone.fire(e.move), e.drop && o.dropzone.fire(e.drop), e.deactivate && ct(r, e.deactivate), n.prev.dropzone = o.dropzone, n.prev.element = o.element;
  }

  function ht(t, e) {
    var n = t.interaction,
        r = t.iEvent,
        o = t.event;

    if ("dragmove" === r.type || "dragend" === r.type) {
      var i = n.dropState;
      e.dynamicDrop && (i.activeDrops = ft(e, n.element));
      var a = r,
          s = dt(n, a, o);
      i.rejected = i.rejected && !!s && s.dropzone === i.cur.dropzone && s.element === i.cur.element, i.cur.dropzone = s && s.dropzone, i.cur.element = s && s.element, i.events = pt(n, 0, a);
    }
  }

  Object.defineProperty(ut, "__esModule", {
    value: !0
  }), ut["default"] = void 0;
  var gt = {
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
              var n = (0, R["default"])(e.listeners),
                  r = Object.keys(n).reduce(function (t, e) {
                return t[/^(enter|leave)/.test(e) ? "drag".concat(e) : /^(activate|deactivate|move)/.test(e) ? "drop".concat(e) : e] = n[e], t;
              }, {});
              t.off(t.options.drop.listeners), t.on(r), t.options.drop.listeners = r;
            }

            return i["default"].func(e.ondrop) && t.on("drop", e.ondrop), i["default"].func(e.ondropactivate) && t.on("dropactivate", e.ondropactivate), i["default"].func(e.ondropdeactivate) && t.on("dropdeactivate", e.ondropdeactivate), i["default"].func(e.ondragenter) && t.on("dragenter", e.ondragenter), i["default"].func(e.ondragleave) && t.on("dragleave", e.ondragleave), i["default"].func(e.ondropmove) && t.on("dropmove", e.ondropmove), /^(pointer|center)$/.test(e.overlap) ? t.options.drop.overlap = e.overlap : i["default"].number(e.overlap) && (t.options.drop.overlap = Math.max(Math.min(1, e.overlap), 0)), "accept" in e && (t.options.drop.accept = e.accept), "checker" in e && (t.options.drop.checker = e.checker), t;
          }

          return i["default"].bool(e) ? (t.options.drop.enabled = e, t) : t.options.drop;
        }(this, t);
      }, r.prototype.dropCheck = function (t, e, n, r, o, a) {
        return function (t, e, n, r, o, a, s) {
          var l = !1;
          if (!(s = s || t.getRect(a))) return !!t.options.drop.checker && t.options.drop.checker(e, n, l, t, a, r, o);
          var u = t.options.drop.overlap;

          if ("pointer" === u) {
            var c = (0, A["default"])(r, o, "drag"),
                f = B.getPageXY(e);
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

          return v && i["default"].number(u) && (l = Math.max(0, Math.min(s.right, v.right) - Math.max(s.left, v.left)) * Math.max(0, Math.min(s.bottom, v.bottom) - Math.max(s.top, v.top)) / (v.width * v.height) >= u), t.options.drop.checker && (l = t.options.drop.checker(e, n, l, t, a, r, o)), l;
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
      }), e.methodDict.drop = "dropzone", t.dynamicDrop = !1, o.actions.drop = gt.defaults;
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
          o.activeDrops = null, o.events = null, o.activeDrops = ft(e, n.element), o.events = pt(n, 0, r), o.events.activate && (ct(o.activeDrops, o.events.activate), e.fire("actions/drop:start", {
            interaction: n,
            dragEvent: r
          }));
        }
      },
      "interactions:action-move": ht,
      "interactions:after-action-move": function interactionsAfterActionMove(t, e) {
        var n = t.interaction,
            r = t.iEvent;
        "drag" === n.prepared.name && (vt(n, n.dropState.events), e.fire("actions/drop:move", {
          interaction: n,
          dragEvent: r
        }), n.dropState.events = {});
      },
      "interactions:action-end": function interactionsActionEnd(t, e) {
        if ("drag" === t.interaction.prepared.name) {
          var n = t.interaction,
              r = t.iEvent;
          ht(t, e), vt(n, n.dropState.events), e.fire("actions/drop:end", {
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
    getActiveDrops: ft,
    getDrop: dt,
    getDropEvents: pt,
    fireDropEvents: vt,
    defaults: {
      enabled: !1,
      accept: null,
      overlap: "pointer"
    }
  },
      yt = gt;
  ut["default"] = yt;
  var mt = {};

  function bt(t) {
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
      if (n.touches = [o[0], o[1]], a) n.distance = B.touchDistance(o, l), n.box = B.touchBBox(o), n.scale = 1, n.ds = 0, n.angle = B.touchAngle(o, l), n.da = 0, e.gesture.startDistance = n.distance, e.gesture.startAngle = n.angle;else if (s) {
        var u = e.prevEvent;
        n.distance = u.distance, n.box = u.box, n.scale = u.scale, n.ds = 0, n.angle = u.angle, n.da = 0;
      } else n.distance = B.touchDistance(o, l), n.box = B.touchBBox(o), n.scale = n.distance / e.gesture.startDistance, n.angle = B.touchAngle(o, l), n.ds = n.scale - e.gesture.scale, n.da = n.angle - e.gesture.angle;
      e.gesture.distance = n.distance, e.gesture.angle = n.angle, i["default"].number(n.scale) && n.scale !== 1 / 0 && !isNaN(n.scale) && (e.gesture.scale = n.scale);
    }
  }

  Object.defineProperty(mt, "__esModule", {
    value: !0
  }), mt["default"] = void 0;
  var xt = {
    id: "actions/gesture",
    before: ["actions/drag", "actions/resize"],
    install: function install(t) {
      var e = t.actions,
          n = t.Interactable,
          r = t.defaults;
      n.prototype.gesturable = function (t) {
        return i["default"].object(t) ? (this.options.gesture.enabled = !1 !== t.enabled, this.setPerAction("gesture", t), this.setOnEvents("gesture", t), this) : i["default"].bool(t) ? (this.options.gesture.enabled = t, this) : this.options.gesture;
      }, e.map.gesture = xt, e.methodDict.gesture = "gesturable", r.actions.gesture = xt.defaults;
    },
    listeners: {
      "interactions:action-start": bt,
      "interactions:action-move": bt,
      "interactions:action-end": bt,
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
      wt = xt;
  mt["default"] = wt;
  var _t = {};

  function Pt(t, e, n, r, o, a, s) {
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

  function Ot(t) {
    var e = t.iEvent,
        n = t.interaction;

    if ("resize" === n.prepared.name && n.resizeAxes) {
      var r = e;
      n.interactable.options.resize.square ? ("y" === n.resizeAxes ? r.delta.x = r.delta.y : r.delta.y = r.delta.x, r.axes = "xy") : (r.axes = n.resizeAxes, "x" === n.resizeAxes ? r.delta.y = 0 : "y" === n.resizeAxes && (r.delta.x = 0));
    }
  }

  Object.defineProperty(_t, "__esModule", {
    value: !0
  }), _t["default"] = void 0;
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
          return i["default"].object(e) ? (t.options.resize.enabled = !1 !== e.enabled, t.setPerAction("resize", e), t.setOnEvents("resize", e), i["default"].string(e.axis) && /^x$|^y$|^xy$/.test(e.axis) ? t.options.resize.axis = e.axis : null === e.axis && (t.options.resize.axis = n.defaults.actions.resize.axis), i["default"].bool(e.preserveAspectRatio) ? t.options.resize.preserveAspectRatio = e.preserveAspectRatio : i["default"].bool(e.square) && (t.options.resize.square = e.square), t) : i["default"].bool(e) ? (t.options.resize.enabled = e, t) : t.options.resize;
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
        }(t), Ot(t);
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
        }(t), Ot(t);
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
                u[c] = Pt(c, l.edges[c], s, e._latestPointer.eventTarget, r, o, l.margin || St.defaultMargin);
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
      Et = St;
  _t["default"] = Et;
  var Tt = {};
  Object.defineProperty(Tt, "__esModule", {
    value: !0
  }), Tt["default"] = void 0;
  var Mt = {
    id: "actions",
    install: function install(t) {
      t.usePlugin(mt["default"]), t.usePlugin(_t["default"]), t.usePlugin(c["default"]), t.usePlugin(ut["default"]);
    }
  };
  Tt["default"] = Mt;
  var jt = {};
  Object.defineProperty(jt, "__esModule", {
    value: !0
  }), jt["default"] = void 0;
  var kt,
      It,
      Dt = 0,
      At = {
    request: function request(t) {
      return kt(t);
    },
    cancel: function cancel(t) {
      return It(t);
    },
    init: function init(t) {
      if (kt = t.requestAnimationFrame, It = t.cancelAnimationFrame, !kt) for (var e = ["ms", "moz", "webkit", "o"], n = 0; n < e.length; n++) {
        var r = e[n];
        kt = t["".concat(r, "RequestAnimationFrame")], It = t["".concat(r, "CancelAnimationFrame")] || t["".concat(r, "CancelRequestAnimationFrame")];
      }
      kt = kt && kt.bind(t), It = It && It.bind(t), kt || (kt = function kt(e) {
        var n = Date.now(),
            r = Math.max(0, 16 - (n - Dt)),
            o = t.setTimeout(function () {
          e(n + r);
        }, r);
        return Dt = n + r, o;
      }, It = function It(t) {
        return clearTimeout(t);
      });
    }
  };
  jt["default"] = At;
  var Rt = {};
  Object.defineProperty(Rt, "__esModule", {
    value: !0
  }), Rt.getContainer = Ct, Rt.getScroll = Ft, Rt.getScrollSize = function (t) {
    return i["default"].window(t) && (t = window.document.body), {
      x: t.scrollWidth,
      y: t.scrollHeight
    };
  }, Rt.getScrollSizeDelta = function (t, e) {
    var n = t.interaction,
        r = t.element,
        o = n && n.interactable.options[n.prepared.name].autoScroll;
    if (!o || !o.enabled) return e(), {
      x: 0,
      y: 0
    };
    var i = Ct(o.container, n.interactable, r),
        a = Ft(i);
    e();
    var s = Ft(i);
    return {
      x: s.x - a.x,
      y: s.y - a.y
    };
  }, Rt["default"] = void 0;
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
      zt.isScrolling = !0, jt["default"].cancel(zt.i), t.autoScroll = zt, zt.interaction = t, zt.prevTime = zt.now(), zt.i = jt["default"].request(zt.scroll);
    },
    stop: function stop() {
      zt.isScrolling = !1, zt.interaction && (zt.interaction.autoScroll = null), jt["default"].cancel(zt.i);
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
          var f = Ft(a);
          i["default"].window(a) ? a.scrollBy(c.x, c.y) : a && (a.scrollLeft += c.x, a.scrollTop += c.y);
          var d = Ft(a),
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

      zt.isScrolling && (jt["default"].cancel(zt.i), zt.i = jt["default"].request(zt.scroll));
    },
    check: function check(t, e) {
      var n;
      return null == (n = t.options[e].autoScroll) ? void 0 : n.enabled;
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

  function Ft(t) {
    return i["default"].window(t) && (t = window.document.body), {
      x: t.scrollLeft,
      y: t.scrollTop
    };
  }

  var Xt = {
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
  Rt["default"] = Xt;
  var Yt = {};
  Object.defineProperty(Yt, "__esModule", {
    value: !0
  }), Yt.warnOnce = function (t, n) {
    var r = !1;
    return function () {
      return r || (e.window.console.warn(n), r = !0), t.apply(this, arguments);
    };
  }, Yt.copyAction = function (t, e) {
    return t.name = e.name, t.axis = e.axis, t.edges = e.edges, t;
  }, Yt.sign = void 0, Yt.sign = function (t) {
    return t >= 0 ? 1 : -1;
  };
  var Bt = {};

  function Wt(t) {
    return i["default"].bool(t) ? (this.options.styleCursor = t, this) : null === t ? (delete this.options.styleCursor, this) : this.options.styleCursor;
  }

  function Lt(t) {
    return i["default"].func(t) ? (this.options.actionChecker = t, this) : null === t ? (delete this.options.actionChecker, this) : this.options.actionChecker;
  }

  Object.defineProperty(Bt, "__esModule", {
    value: !0
  }), Bt["default"] = void 0;
  var Ut = {
    id: "auto-start/interactableMethods",
    install: function install(t) {
      var e = t.Interactable;
      e.prototype.getAction = function (e, n, r, o) {
        var i = function (t, e, n, r, o) {
          var i = t.getRect(r),
              a = {
            action: null,
            interactable: t,
            interaction: n,
            element: r,
            rect: i,
            buttons: e.buttons || {
              0: 1,
              1: 4,
              3: 8,
              4: 16
            }[e.button]
          };
          return o.fire("auto-start:check", a), a.action;
        }(this, n, r, o, t);

        return this.options.actionChecker ? this.options.actionChecker(e, n, i, this, o, r) : i;
      }, e.prototype.ignoreFrom = (0, Yt.warnOnce)(function (t) {
        return this._backCompatOption("ignoreFrom", t);
      }, "Interactable.ignoreFrom() has been deprecated. Use Interactble.draggable({ignoreFrom: newValue})."), e.prototype.allowFrom = (0, Yt.warnOnce)(function (t) {
        return this._backCompatOption("allowFrom", t);
      }, "Interactable.allowFrom() has been deprecated. Use Interactble.draggable({allowFrom: newValue})."), e.prototype.actionChecker = Lt, e.prototype.styleCursor = Wt;
    }
  };
  Bt["default"] = Ut;
  var Vt = {};

  function Nt(t, e, n, r, o) {
    return e.testIgnoreAllow(e.options[t.name], n, r) && e.options[t.name].enabled && Ht(e, n, t, o) ? t : null;
  }

  function qt(t, e, n, r, o, i, a) {
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

  function $t(t, e, n, r, o) {
    var a = [],
        s = [],
        l = r;

    function u(t) {
      a.push(t), s.push(l);
    }

    for (; i["default"].element(l);) {
      a = [], s = [], o.interactables.forEachMatch(l, u);
      var c = qt(t, e, n, a, s, r, o);
      if (c.action && !c.interactable.options[c.action.name].manualStart) return c;
      l = _.parentNode(l);
    }

    return {
      action: null,
      interactable: null,
      element: null
    };
  }

  function Gt(t, e, n) {
    var r = e.action,
        o = e.interactable,
        i = e.element;
    r = r || {
      name: null
    }, t.interactable = o, t.element = i, (0, Yt.copyAction)(t.prepared, r), t.rect = o && r.name ? o.getRect(i) : null, Jt(t, n), n.fire("autoStart:prepared", {
      interaction: t
    });
  }

  function Ht(t, e, n, r) {
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

  function Kt(t, e) {
    return i["default"].number(t) ? (e.autoStart.maxInteractions = t, this) : e.autoStart.maxInteractions;
  }

  function Zt(t, e, n) {
    var r = n.autoStart.cursorElement;
    r && r !== t && (r.style.cursor = ""), t.ownerDocument.documentElement.style.cursor = e, t.style.cursor = e, n.autoStart.cursorElement = e ? t : null;
  }

  function Jt(t, e) {
    var n = t.interactable,
        r = t.element,
        o = t.prepared;

    if ("mouse" === t.pointerType && n && n.options.styleCursor) {
      var a = "";

      if (o.name) {
        var s = n.options[o.name].cursorChecker;
        a = i["default"].func(s) ? s(o, n, r, t._interacting) : e.actions.map[o.name].getCursor(o);
      }

      Zt(t.element, a || "", e);
    } else e.autoStart.cursorElement && Zt(e.autoStart.cursorElement, "", e);
  }

  Object.defineProperty(Vt, "__esModule", {
    value: !0
  }), Vt["default"] = void 0;
  var Qt = {
    id: "auto-start/base",
    before: ["actions"],
    install: function install(t) {
      var e = t.interactStatic,
          n = t.defaults;
      t.usePlugin(Bt["default"]), n.base.actionChecker = null, n.base.styleCursor = !0, (0, j["default"])(n.perAction, {
        manualStart: !1,
        max: 1 / 0,
        maxPerElement: 1,
        allowFrom: null,
        ignoreFrom: null,
        mouseButtons: 1
      }), e.maxInteractions = function (e) {
        return Kt(e, t);
      }, t.autoStart = {
        maxInteractions: 1 / 0,
        withinInteractionLimit: Ht,
        cursorElement: null
      };
    },
    listeners: {
      "interactions:down": function interactionsDown(t, e) {
        var n = t.interaction,
            r = t.pointer,
            o = t.event,
            i = t.eventTarget;
        n.interacting() || Gt(n, $t(n, r, o, i, e), e);
      },
      "interactions:move": function interactionsMove(t, e) {
        !function (t, e) {
          var n = t.interaction,
              r = t.pointer,
              o = t.event,
              i = t.eventTarget;
          "mouse" !== n.pointerType || n.pointerIsDown || n.interacting() || Gt(n, $t(n, r, o, i, e), e);
        }(t, e), function (t, e) {
          var n = t.interaction;

          if (n.pointerIsDown && !n.interacting() && n.pointerWasMoved && n.prepared.name) {
            e.fire("autoStart:before-start", t);
            var r = n.interactable,
                o = n.prepared.name;
            o && r && (r.options[o].manualStart || !Ht(r, n.element, n.prepared, e) ? n.stop() : (n.start(n.prepared, r, n.element), Jt(n, e)));
          }
        }(t, e);
      },
      "interactions:stop": function interactionsStop(t, e) {
        var n = t.interaction,
            r = n.interactable;
        r && r.options.styleCursor && Zt(n.element, "", e);
      }
    },
    maxInteractions: Kt,
    withinInteractionLimit: Ht,
    validateAction: Nt
  };
  Vt["default"] = Qt;
  var te = {};
  Object.defineProperty(te, "__esModule", {
    value: !0
  }), te["default"] = void 0;
  var ee = {
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
                  }(f, t) && Vt["default"].validateAction(i, t, d, r, e)) return t;
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
  te["default"] = ee;
  var ne = {};

  function re(t) {
    var e = t.prepared && t.prepared.name;
    if (!e) return null;
    var n = t.interactable.options;
    return n[e].hold || n[e].delay;
  }

  Object.defineProperty(ne, "__esModule", {
    value: !0
  }), ne["default"] = void 0;
  var oe = {
    id: "auto-start/hold",
    install: function install(t) {
      var e = t.defaults;
      t.usePlugin(Vt["default"]), e.perAction.hold = 0, e.perAction.delay = 0;
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        t.interaction.autoStartHoldTimer = null;
      },
      "autoStart:prepared": function autoStartPrepared(t) {
        var e = t.interaction,
            n = re(e);
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
        re(e) > 0 && (e.prepared.name = null);
      }
    },
    getHoldDuration: re
  };
  ne["default"] = oe;
  var ie = {};
  Object.defineProperty(ie, "__esModule", {
    value: !0
  }), ie["default"] = void 0;
  var ae = {
    id: "auto-start",
    install: function install(t) {
      t.usePlugin(Vt["default"]), t.usePlugin(ne["default"]), t.usePlugin(te["default"]);
    }
  };
  ie["default"] = ae;
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
  var de = {};
  Object.defineProperty(de, "__esModule", {
    value: !0
  }), de["default"] = void 0, de["default"] = {};
  var pe,
      ve = {};
  Object.defineProperty(ve, "__esModule", {
    value: !0
  }), ve["default"] = void 0, function (t) {
    t.touchAction = "touchAction", t.boxSizing = "boxSizing", t.noListeners = "noListeners";
  }(pe || (pe = {}));
  pe.touchAction, pe.boxSizing, pe.noListeners;
  var he = {
    id: "dev-tools",
    install: function install() {}
  };
  ve["default"] = he;
  var ge = {};
  Object.defineProperty(ge, "__esModule", {
    value: !0
  }), ge["default"] = function t(e) {
    var n = {};

    for (var r in e) {
      var o = e[r];
      i["default"].plainObject(o) ? n[r] = t(o) : i["default"].array(o) ? n[r] = Z.from(o) : n[r] = o;
    }

    return n;
  };
  var ye = {};

  function me(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) {
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
      }
    }(t, e) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return be(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? be(t, e) : void 0;
      }
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function be(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function xe(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function we(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(ye, "__esModule", {
    value: !0
  }), ye.getRectOffset = Oe, ye["default"] = void 0;

  var _e = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), we(this, "states", []), we(this, "startOffset", {
        left: 0,
        right: 0,
        top: 0,
        bottom: 0
      }), we(this, "startDelta", void 0), we(this, "result", void 0), we(this, "endResult", void 0), we(this, "edges", void 0), we(this, "interaction", void 0), this.interaction = e, this.result = Pe();
    }

    var e, n;
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

        this.prepareStates(o), this.edges = (0, j["default"])({}, r.edges), this.startOffset = Oe(r.rect, e), this.startDelta = {
          x: 0,
          y: 0
        };
        var i = this.fillArg({
          phase: n,
          pageCoords: e,
          preEnd: !1
        });
        return this.result = Pe(), this.startAll(i), this.result = this.setAll(i);
      }
    }, {
      key: "fillArg",
      value: function value(t) {
        var e = this.interaction;
        return t.interaction = e, t.interactable = e.interactable, t.element = e.element, t.rect = t.rect || e.rect, t.edges = this.edges, t.startOffset = this.startOffset, t;
      }
    }, {
      key: "startAll",
      value: function value(t) {
        for (var e = 0; e < this.states.length; e++) {
          var n = this.states[e];
          n.methods.start && (t.state = n, n.methods.start(t));
        }
      }
    }, {
      key: "setAll",
      value: function value(t) {
        var e = t.phase,
            n = t.preEnd,
            r = t.skipModifiers,
            o = t.rect;
        t.coords = (0, j["default"])({}, t.pageCoords), t.rect = (0, j["default"])({}, o);

        for (var i = r ? this.states.slice(r) : this.states, a = Pe(t.coords, t.rect), s = 0; s < i.length; s++) {
          var l,
              u = i[s],
              c = u.options,
              f = (0, j["default"])({}, t.coords),
              d = null;
          null != (l = u.methods) && l.set && this.shouldDo(c, n, e) && (t.state = u, d = u.methods.set(t), k.addEdges(this.interaction.edges, t.rect, {
            x: t.coords.x - f.x,
            y: t.coords.y - f.y
          })), a.eventProps.push(d);
        }

        a.delta.x = t.coords.x - t.pageCoords.x, a.delta.y = t.coords.y - t.pageCoords.y, a.rectDelta.left = t.rect.left - o.left, a.rectDelta.right = t.rect.right - o.right, a.rectDelta.top = t.rect.top - o.top, a.rectDelta.bottom = t.rect.bottom - o.bottom;
        var p = this.result.coords,
            v = this.result.rect;

        if (p && v) {
          var h = a.rect.left !== v.left || a.rect.right !== v.right || a.rect.top !== v.top || a.rect.bottom !== v.bottom;
          a.changed = h || p.x !== a.coords.x || p.y !== a.coords.y;
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

        for (var l = 0; l < [[o, a], [r, s]].length; l++) {
          var u = me([[o, a], [r, s]][l], 2),
              c = u[0],
              f = u[1];
          c.page.x += f.x, c.page.y += f.y, c.client.x += f.x, c.client.y += f.y;
        }

        var d = this.result.rectDelta,
            p = t.rect || e.rect;
        p.left += d.left, p.right += d.right, p.top += d.top, p.bottom += d.bottom, p.width = p.right - p.left, p.height = p.bottom - p.top;
      }
    }, {
      key: "setAndApply",
      value: function value(t) {
        var e = this.interaction,
            n = t.phase,
            r = t.preEnd,
            o = t.skipModifiers,
            i = this.setAll(this.fillArg({
          preEnd: r,
          phase: n,
          pageCoords: t.modifiedCoords || e.coords.cur.page
        }));
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
            var f = me(u[c], 2),
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
          return (0, ge["default"])(t);
        }), this.result = Pe((0, j["default"])({}, t.result.coords), (0, j["default"])({}, t.result.rect));
      }
    }, {
      key: "destroy",
      value: function value() {
        for (var t in this) {
          this[t] = null;
        }
      }
    }]) && xe(e.prototype, n), t;
  }();

  function Pe(t, e) {
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

  function Oe(t, e) {
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

  ye["default"] = _e;
  var Se = {};

  function Ee(t) {
    var e = t.iEvent,
        n = t.interaction.modification.result;
    n && (e.modifiers = n.eventProps);
  }

  Object.defineProperty(Se, "__esModule", {
    value: !0
  }), Se.makeModifier = function (t, e) {
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

    return e && "string" == typeof e && (o._defaults = n, o._methods = r), o;
  }, Se.addEventModifiers = Ee, Se["default"] = void 0;
  var Te = {
    id: "modifiers/base",
    before: ["actions"],
    install: function install(t) {
      t.defaults.perAction.modifiers = [];
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        var e = t.interaction;
        e.modification = new ye["default"](e);
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
      "interactions:action-start": Ee,
      "interactions:action-move": Ee,
      "interactions:action-end": Ee,
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
  Se["default"] = Te;
  var Me = {};
  Object.defineProperty(Me, "__esModule", {
    value: !0
  }), Me.defaults = void 0, Me.defaults = {
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
  var je = {};

  function ke(t) {
    return (ke = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function Ie(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function De(t, e) {
    return (De = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function Ae(t, e) {
    return !e || "object" !== ke(e) && "function" != typeof e ? Re(t) : e;
  }

  function Re(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function ze(t) {
    return (ze = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function Ce(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(je, "__esModule", {
    value: !0
  }), je.InteractEvent = void 0;

  var Fe = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && De(t, e);
    }(a, t);
    var e,
        n,
        r,
        o,
        i = (r = a, o = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }(), function () {
      var t,
          e = ze(r);

      if (o) {
        var n = ze(this).constructor;
        t = Reflect.construct(e, arguments, n);
      } else t = e.apply(this, arguments);

      return Ae(this, t);
    });

    function a(t, e, n, r, o, s, l) {
      var u;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, a), Ce(Re(u = i.call(this, t)), "target", void 0), Ce(Re(u), "currentTarget", void 0), Ce(Re(u), "relatedTarget", null), Ce(Re(u), "screenX", void 0), Ce(Re(u), "screenY", void 0), Ce(Re(u), "button", void 0), Ce(Re(u), "buttons", void 0), Ce(Re(u), "ctrlKey", void 0), Ce(Re(u), "shiftKey", void 0), Ce(Re(u), "altKey", void 0), Ce(Re(u), "metaKey", void 0), Ce(Re(u), "page", void 0), Ce(Re(u), "client", void 0), Ce(Re(u), "delta", void 0), Ce(Re(u), "rect", void 0), Ce(Re(u), "x0", void 0), Ce(Re(u), "y0", void 0), Ce(Re(u), "t0", void 0), Ce(Re(u), "dt", void 0), Ce(Re(u), "duration", void 0), Ce(Re(u), "clientX0", void 0), Ce(Re(u), "clientY0", void 0), Ce(Re(u), "velocity", void 0), Ce(Re(u), "speed", void 0), Ce(Re(u), "swipe", void 0), Ce(Re(u), "timeStamp", void 0), Ce(Re(u), "axes", void 0), Ce(Re(u), "preEnd", void 0), o = o || t.element;
      var c = t.interactable,
          f = (c && c.options || Me.defaults).deltaSource,
          d = (0, A["default"])(c, o, n),
          p = "start" === r,
          v = "end" === r,
          h = p ? Re(u) : t.prevEvent,
          g = p ? t.coords.start : v ? {
        page: h.page,
        client: h.client,
        timeStamp: t.coords.cur.timeStamp
      } : t.coords.cur;
      return u.page = (0, j["default"])({}, g.page), u.client = (0, j["default"])({}, g.client), u.rect = (0, j["default"])({}, t.rect), u.timeStamp = g.timeStamp, v || (u.page.x -= d.x, u.page.y -= d.y, u.client.x -= d.x, u.client.y -= d.y), u.ctrlKey = e.ctrlKey, u.altKey = e.altKey, u.shiftKey = e.shiftKey, u.metaKey = e.metaKey, u.button = e.button, u.buttons = e.buttons, u.target = o, u.currentTarget = o, u.preEnd = s, u.type = l || n + (r || ""), u.interactable = c, u.t0 = p ? t.pointers[t.pointers.length - 1].downTime : h.t0, u.x0 = t.coords.start.page.x - d.x, u.y0 = t.coords.start.page.y - d.y, u.clientX0 = t.coords.start.client.x - d.x, u.clientY0 = t.coords.start.client.y - d.y, u.delta = p || v ? {
        x: 0,
        y: 0
      } : {
        x: u[f].x - h[f].x,
        y: u[f].y - h[f].y
      }, u.dt = t.coords.delta.timeStamp, u.duration = u.timeStamp - u.t0, u.velocity = (0, j["default"])({}, t.coords.velocity[f]), u.speed = (0, C["default"])(u.velocity.x, u.velocity.y), u.swipe = v || "inertiastart" === r ? u.getSwipe() : null, u;
    }

    return e = a, (n = [{
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
    }]) && Ie(e.prototype, n), a;
  }($.BaseEvent);

  je.InteractEvent = Fe, Object.defineProperties(Fe.prototype, {
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
  var Xe = {};

  function Ye(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(Xe, "__esModule", {
    value: !0
  }), Xe.PointerInfo = void 0, Xe.PointerInfo = function t(e, n, r, o, i) {
    !function (t, e) {
      if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
    }(this, t), Ye(this, "id", void 0), Ye(this, "pointer", void 0), Ye(this, "event", void 0), Ye(this, "downTime", void 0), Ye(this, "downTarget", void 0), this.id = e, this.pointer = n, this.event = r, this.downTime = o, this.downTarget = i;
  };
  var Be,
      We,
      Le = {};

  function Ue(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Ve(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(Le, "__esModule", {
    value: !0
  }), Object.defineProperty(Le, "PointerInfo", {
    enumerable: !0,
    get: function get() {
      return Xe.PointerInfo;
    }
  }), Le["default"] = Le.Interaction = Le._ProxyMethods = Le._ProxyValues = void 0, Le._ProxyValues = Be, function (t) {
    t.interactable = "", t.element = "", t.prepared = "", t.pointerIsDown = "", t.pointerWasMoved = "", t._proxy = "";
  }(Be || (Le._ProxyValues = Be = {})), Le._ProxyMethods = We, function (t) {
    t.start = "", t.move = "", t.end = "", t.stop = "", t.interacting = "";
  }(We || (Le._ProxyMethods = We = {}));

  var Ne = 0,
      qe = function () {
    function t(e) {
      var n = this,
          r = e.pointerType,
          o = e.scopeFire;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), Ve(this, "interactable", null), Ve(this, "element", null), Ve(this, "rect", void 0), Ve(this, "_rects", void 0), Ve(this, "edges", void 0), Ve(this, "_scopeFire", void 0), Ve(this, "prepared", {
        name: null,
        axis: null,
        edges: null
      }), Ve(this, "pointerType", void 0), Ve(this, "pointers", []), Ve(this, "downEvent", null), Ve(this, "downPointer", {}), Ve(this, "_latestPointer", {
        pointer: null,
        event: null,
        eventTarget: null
      }), Ve(this, "prevEvent", null), Ve(this, "pointerIsDown", !1), Ve(this, "pointerWasMoved", !1), Ve(this, "_interacting", !1), Ve(this, "_ending", !1), Ve(this, "_stopped", !0), Ve(this, "_proxy", null), Ve(this, "simulation", null), Ve(this, "doMove", (0, Yt.warnOnce)(function (t) {
        this.move(t);
      }, "The interaction.doMove() method has been renamed to interaction.move()")), Ve(this, "coords", {
        start: B.newCoords(),
        prev: B.newCoords(),
        cur: B.newCoords(),
        delta: B.newCoords(),
        velocity: B.newCoords()
      }), Ve(this, "_id", Ne++), this._scopeFire = o, this.pointerType = r;
      var i = this;
      this._proxy = {};

      var a = function a(t) {
        Object.defineProperty(n._proxy, t, {
          get: function get() {
            return i[t];
          }
        });
      };

      for (var s in Be) {
        a(s);
      }

      var l = function l(t) {
        Object.defineProperty(n._proxy, t, {
          value: function value() {
            return i[t].apply(i, arguments);
          }
        });
      };

      for (var u in We) {
        l(u);
      }

      this._scopeFire("interactions:new", {
        interaction: this
      });
    }

    var e, n;
    return e = t, (n = [{
      key: "pointerMoveTolerance",
      get: function get() {
        return 1;
      }
    }, {
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
        return !(this.interacting() || !this.pointerIsDown || this.pointers.length < ("gesture" === t.name ? 2 : 1) || !e.options[t.name].enabled) && ((0, Yt.copyAction)(this.prepared, t), this.interactable = e, this.element = n, this.rect = e.getRect(n), this.edges = this.prepared.edges ? (0, j["default"])({}, this.prepared.edges) : {
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
        this.pointerIsDown && !this.pointerWasMoved && (r = this.coords.cur.client.x - this.coords.start.client.x, o = this.coords.cur.client.y - this.coords.start.client.y, this.pointerWasMoved = (0, C["default"])(r, o) > this.pointerMoveTolerance);
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
        i || B.setCoordVelocity(this.coords.velocity, this.coords.delta), this._scopeFire("interactions:move", s), i || this.simulation || (this.interacting() && (s.type = null, this.move(s)), this.pointerWasMoved && B.copyCoords(this.coords.prev, this.coords.cur));
      }
    }, {
      key: "move",
      value: function value(t) {
        t && t.event || B.setZeroCoords(this.coords.delta), (t = (0, j["default"])({
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
        var e = B.getPointerId(t);
        return "mouse" === this.pointerType || "pen" === this.pointerType ? this.pointers.length - 1 : Z.findIndex(this.pointers, function (t) {
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
        var o = B.getPointerId(t),
            i = this.getPointerIndex(t),
            a = this.pointers[i];
        return r = !1 !== r && (r || /(down|start)$/i.test(e.type)), a ? a.pointer = t : (a = new Xe.PointerInfo(o, t, e, null, null), i = this.pointers.length, this.pointers.push(a)), B.setCoords(this.coords.cur, this.pointers.map(function (t) {
          return t.pointer;
        }), this._now()), B.setCoordDeltas(this.coords.delta, this.coords.prev, this.coords.cur), r && (this.pointerIsDown = !0, a.downTime = this.coords.cur.timeStamp, a.downTarget = n, B.pointerExtend(this.downPointer, t), this.interacting() || (B.copyCoords(this.coords.start, this.coords.cur), B.copyCoords(this.coords.prev, this.coords.cur), this.downEvent = e, this.pointerWasMoved = !1)), this._updateLatestPointer(t, e, n), this._scopeFire("interactions:update-pointer", {
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
        return new je.InteractEvent(this, t, this.prepared.name, e, this.element, n, r);
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
    }]) && Ue(e.prototype, n), t;
  }();

  Le.Interaction = qe;
  var $e = qe;
  Le["default"] = $e;
  var Ge = {};

  function He(t) {
    t.pointerIsDown && (Qe(t.coords.cur, t.offset.total), t.offset.pending.x = 0, t.offset.pending.y = 0);
  }

  function Ke(t) {
    Ze(t.interaction);
  }

  function Ze(t) {
    if (!function (t) {
      return !(!t.offset.pending.x && !t.offset.pending.y);
    }(t)) return !1;
    var e = t.offset.pending;
    return Qe(t.coords.cur, e), Qe(t.coords.delta, e), k.addEdges(t.edges, t.rect, e), e.x = 0, e.y = 0, !0;
  }

  function Je(t) {
    var e = t.x,
        n = t.y;
    this.offset.pending.x += e, this.offset.pending.y += n, this.offset.total.x += e, this.offset.total.y += n;
  }

  function Qe(t, e) {
    var n = t.page,
        r = t.client,
        o = e.x,
        i = e.y;
    n.x += o, n.y += i, r.x += o, r.y += i;
  }

  Object.defineProperty(Ge, "__esModule", {
    value: !0
  }), Ge.addTotal = He, Ge.applyPending = Ze, Ge["default"] = void 0, Le._ProxyMethods.offsetBy = "";
  var tn = {
    id: "offset",
    before: ["modifiers", "pointer-events", "actions", "inertia"],
    install: function install(t) {
      t.Interaction.prototype.offsetBy = Je;
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
        return He(t.interaction);
      },
      "interactions:before-action-start": Ke,
      "interactions:before-action-move": Ke,
      "interactions:before-action-end": function interactionsBeforeActionEnd(t) {
        var e = t.interaction;
        if (Ze(e)) return e.move({
          offset: !0
        }), e.end(), !1;
      },
      "interactions:stop": function interactionsStop(t) {
        var e = t.interaction;
        e.offset.total.x = 0, e.offset.total.y = 0, e.offset.pending.x = 0, e.offset.pending.y = 0;
      }
    }
  };
  Ge["default"] = tn;
  var en = {};

  function nn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function rn(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(en, "__esModule", {
    value: !0
  }), en["default"] = en.InertiaState = void 0;

  var on = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), rn(this, "active", !1), rn(this, "isModified", !1), rn(this, "smoothEnd", !1), rn(this, "allowResume", !1), rn(this, "modification", void 0), rn(this, "modifierCount", 0), rn(this, "modifierArg", void 0), rn(this, "startCoords", void 0), rn(this, "t0", 0), rn(this, "v0", 0), rn(this, "te", 0), rn(this, "targetOffset", void 0), rn(this, "modifiedOffset", void 0), rn(this, "currentOffset", void 0), rn(this, "lambda_v0", 0), rn(this, "one_ve_v0", 0), rn(this, "timeout", void 0), rn(this, "interaction", void 0), this.interaction = e;
    }

    var e, n;
    return e = t, (n = [{
      key: "start",
      value: function value(t) {
        var e = this.interaction,
            n = an(e);
        if (!n || !n.enabled) return !1;
        var r = e.coords.velocity.client,
            o = (0, C["default"])(r.x, r.y),
            i = this.modification || (this.modification = new ye["default"](e));
        if (i.copyFrom(e.modification), this.t0 = e._now(), this.allowResume = n.allowResume, this.v0 = o, this.currentOffset = {
          x: 0,
          y: 0
        }, this.startCoords = e.coords.cur.page, this.modifierArg = i.fillArg({
          pageCoords: this.startCoords,
          preEnd: !0,
          phase: "inertiastart"
        }), this.t0 - e.coords.cur.timeStamp < 50 && o > n.minSpeed && o > n.endSpeed) this.startInertia();else {
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
            n = an(this.interaction),
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
        this.timeout = jt["default"].request(function () {
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
            i = this,
            a = this.interaction,
            s = an(a).resistance,
            l = (a._now() - this.t0) / 1e3;

        if (l < this.te) {
          var u,
              c = 1 - (Math.exp(-s * l) - this.lambda_v0) / this.one_ve_v0;
          this.isModified ? (0, 0, t = this.targetOffset.x, e = this.targetOffset.y, n = this.modifiedOffset.x, r = this.modifiedOffset.y, u = {
            x: sn(o = c, 0, t, n),
            y: sn(o, 0, e, r)
          }) : u = {
            x: this.targetOffset.x * c,
            y: this.targetOffset.y * c
          };
          var f = {
            x: u.x - this.currentOffset.x,
            y: u.y - this.currentOffset.y
          };
          this.currentOffset.x += f.x, this.currentOffset.y += f.y, a.offsetBy(f), a.move(), this.onNextFrame(function () {
            return i.inertiaTick();
          });
        } else a.offsetBy({
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
            r = an(e).smoothEndDuration;

        if (n < r) {
          var o = {
            x: ln(n, 0, this.targetOffset.x, r),
            y: ln(n, 0, this.targetOffset.y, r)
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
        }), (0, B.copyCoords)(o.coords.prev, o.coords.cur), this.stop();
      }
    }, {
      key: "end",
      value: function value() {
        this.interaction.move(), this.interaction.end(), this.stop();
      }
    }, {
      key: "stop",
      value: function value() {
        this.active = this.smoothEnd = !1, this.interaction.simulation = null, jt["default"].cancel(this.timeout);
      }
    }]) && nn(e.prototype, n), t;
  }();

  function an(t) {
    var e = t.interactable,
        n = t.prepared;
    return e && e.options && n.name && e.options[n.name].inertia;
  }

  function sn(t, e, n, r) {
    var o = 1 - t;
    return o * o * e + 2 * o * t * n + t * t * r;
  }

  function ln(t, e, n, r) {
    return -n * (t /= r) * (t - 2) + e;
  }

  en.InertiaState = on;
  var un = {
    id: "inertia",
    before: ["modifiers", "actions"],
    install: function install(t) {
      var e = t.defaults;
      t.usePlugin(Ge["default"]), t.usePlugin(Se["default"]), t.actions.phases.inertiastart = !0, t.actions.phases.resume = !0, e.perAction.inertia = {
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
        e.inertia = new on(e);
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
      "interactions:action-resume": Se.addEventModifiers,
      "interactions:action-inertiastart": Se.addEventModifiers,
      "interactions:after-action-inertiastart": function interactionsAfterActionInertiastart(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      },
      "interactions:after-action-resume": function interactionsAfterActionResume(t) {
        return t.interaction.modification.restoreInteractionCoords(t);
      }
    }
  };
  en["default"] = un;
  var cn = {};

  function fn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function dn(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  function pn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      if (t.immediatePropagationStopped) break;
      r(t);
    }
  }

  Object.defineProperty(cn, "__esModule", {
    value: !0
  }), cn.Eventable = void 0;

  var vn = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), dn(this, "options", void 0), dn(this, "types", {}), dn(this, "propagationStopped", !1), dn(this, "immediatePropagationStopped", !1), dn(this, "global", void 0), this.options = (0, j["default"])({}, e || {});
    }

    var e, n;
    return e = t, (n = [{
      key: "fire",
      value: function value(t) {
        var e,
            n = this.global;
        (e = this.types[t.type]) && pn(t, e), !t.propagationStopped && n && (e = n[t.type]) && pn(t, e);
      }
    }, {
      key: "on",
      value: function value(t, e) {
        var n = (0, R["default"])(t, e);

        for (t in n) {
          this.types[t] = Z.merge(this.types[t] || [], n[t]);
        }
      }
    }, {
      key: "off",
      value: function value(t, e) {
        var n = (0, R["default"])(t, e);

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
    }]) && fn(e.prototype, n), t;
  }();

  cn.Eventable = vn;
  var hn = {};
  Object.defineProperty(hn, "__esModule", {
    value: !0
  }), hn["default"] = function (t, e) {
    if (e.phaselessTypes[t]) return !0;

    for (var n in e.map) {
      if (0 === t.indexOf(n) && t.substr(n.length) in e.phases) return !0;
    }

    return !1;
  };
  var gn = {};
  Object.defineProperty(gn, "__esModule", {
    value: !0
  }), gn.createInteractStatic = function (t) {
    var e = function e(n, r) {
      var o = t.interactables.get(n, r);
      return o || ((o = t.interactables["new"](n, r)).events.global = e.globalEvents), o;
    };

    return e.getPointerAverage = B.pointerAverage, e.getTouchBBox = B.touchBBox, e.getTouchDistance = B.touchDistance, e.getTouchAngle = B.touchAngle, e.getElementRect = _.getElementRect, e.getElementClientRect = _.getElementClientRect, e.matchesSelector = _.matchesSelector, e.closest = _.closest, e.globalEvents = {}, e.version = "1.10.11", e.scope = t, e.use = function (t, e) {
      return this.scope.usePlugin(t, e), this;
    }, e.isSet = function (t, e) {
      return !!this.scope.interactables.get(t, e && e.context);
    }, e.on = (0, Yt.warnOnce)(function (t, e, n) {
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

      return (0, hn["default"])(t, this.scope.actions) ? this.globalEvents[t] ? this.globalEvents[t].push(e) : this.globalEvents[t] = [e] : this.scope.events.add(this.scope.document, t, e, {
        options: n
      }), this;
    }, "The interact.on() method is being deprecated"), e.off = (0, Yt.warnOnce)(function (t, e, n) {
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
      return (0, hn["default"])(t, this.scope.actions) ? t in this.globalEvents && -1 !== (s = this.globalEvents[t].indexOf(e)) && this.globalEvents[t].splice(s, 1) : this.scope.events.remove(this.scope.document, t, e, n), this;
    }, "The interact.off() method is being deprecated"), e.debug = function () {
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
  var yn = {};

  function mn(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function bn(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(yn, "__esModule", {
    value: !0
  }), yn.Interactable = void 0;

  var xn = function () {
    function t(n, r, o, i) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), bn(this, "options", void 0), bn(this, "_actions", void 0), bn(this, "target", void 0), bn(this, "events", new cn.Eventable()), bn(this, "_context", void 0), bn(this, "_win", void 0), bn(this, "_doc", void 0), bn(this, "_scopeEvents", void 0), bn(this, "_rectChecker", void 0), this._actions = r.actions, this.target = n, this._context = r.context || o, this._win = (0, e.getWindow)((0, _.trySelector)(n) ? this._context : n), this._doc = this._win.document, this._scopeEvents = i, this.set(r);
    }

    var n, r;
    return n = t, (r = [{
      key: "_defaults",
      get: function get() {
        return {
          base: {},
          perAction: {},
          actions: {}
        };
      }
    }, {
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
          "listeners" === o && this.updatePerActionListeners(t, a.listeners, s), i["default"].array(s) ? a[o] = Z.from(s) : i["default"].plainObject(s) ? (a[o] = (0, j["default"])(a[o] || {}, (0, ge["default"])(s)), i["default"].object(n.perAction[o]) && "enabled" in n.perAction[o] && (a[o].enabled = !1 !== s.enabled)) : i["default"].bool(s) && i["default"].object(n.perAction[o]) ? a[o].enabled = s : a[o] = s;
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
            a = (0, R["default"])(e, n);

        for (var s in a) {
          "wheel" === s && (s = b["default"].wheelEvent);

          for (var l = 0; l < a[s].length; l++) {
            var u = a[s][l];
            (0, hn["default"])(s, this._actions) ? this.events[t](s, u) : i["default"].string(this.target) ? this._scopeEvents["".concat(o, "Delegate")](this.target, this._context, s, u, r) : this._scopeEvents[o](this.target, s, u, r);
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

        for (var n in i["default"].object(t) || (t = {}), this.options = (0, ge["default"])(e.base), this._actions.methodDict) {
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
    }]) && mn(n.prototype, r), t;
  }();

  yn.Interactable = xn;
  var wn = {};

  function _n(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Pn(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(wn, "__esModule", {
    value: !0
  }), wn.InteractableSet = void 0;

  var On = function () {
    function t(e) {
      var n = this;
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), Pn(this, "list", []), Pn(this, "selectorMap", {}), Pn(this, "scope", void 0), this.scope = e, e.addListeners({
        "interactable:unset": function interactableUnset(t) {
          var e = t.interactable,
              r = e.target,
              o = e._context,
              a = i["default"].string(r) ? n.selectorMap[r] : r[n.scope.id],
              s = Z.findIndex(a, function (t) {
            return t.context === o;
          });
          a[s] && (a[s].context = null, a[s].interactable = null), a.splice(s, 1);
        }
      });
    }

    var e, n;
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
        var a = Z.find(o, function (e) {
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
    }]) && _n(e.prototype, n), t;
  }();

  wn.InteractableSet = On;
  var Sn = {};

  function En(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function Tn(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  function Mn(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) {
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
      }
    }(t, e) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return jn(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? jn(t, e) : void 0;
      }
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function jn(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(Sn, "__esModule", {
    value: !0
  }), Sn["default"] = void 0;

  var kn = function () {
    function t(e) {
      !function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, t), Tn(this, "currentTarget", void 0), Tn(this, "originalEvent", void 0), Tn(this, "type", void 0), this.originalEvent = e, (0, F["default"])(this, e);
    }

    var e, n;
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
    }]) && En(e.prototype, n), t;
  }();

  function In(t) {
    if (!i["default"].object(t)) return {
      capture: !!t,
      passive: !1
    };
    var e = (0, j["default"])({}, t);
    return e.capture = !!t.capture, e.passive = !!t.passive, e;
  }

  var Dn = {
    id: "events",
    install: function install(t) {
      var e,
          n = [],
          r = {},
          o = [],
          a = {
        add: s,
        remove: l,
        addDelegate: function addDelegate(t, e, n, i, a) {
          var l = In(a);

          if (!r[n]) {
            r[n] = [];

            for (var f = 0; f < o.length; f++) {
              var d = o[f];
              s(d, n, u), s(d, n, c, !0);
            }
          }

          var p = r[n],
              v = Z.find(p, function (n) {
            return n.selector === t && n.context === e;
          });
          v || (v = {
            selector: t,
            context: e,
            listeners: []
          }, p.push(v)), v.listeners.push([i, l]);
        },
        removeDelegate: function removeDelegate(t, e, n, o, i) {
          var a,
              s = In(i),
              f = r[n],
              d = !1;
          if (f) for (a = f.length - 1; a >= 0; a--) {
            var p = f[a];

            if (p.selector === t && p.context === e) {
              for (var v = p.listeners, h = v.length - 1; h >= 0; h--) {
                var g = Mn(v[h], 2),
                    y = g[0],
                    m = g[1],
                    b = m.capture,
                    x = m.passive;

                if (y === o && b === s.capture && x === s.passive) {
                  v.splice(h, 1), v.length || (f.splice(a, 1), l(e, n, u), l(e, n, c, !0)), d = !0;
                  break;
                }
              }

              if (d) break;
            }
          }
        },
        delegateListener: u,
        delegateUseCapture: c,
        delegatedEvents: r,
        documents: o,
        targets: n,
        supportsOptions: !1,
        supportsPassive: !1
      };

      function s(t, e, r, o) {
        var i = In(o),
            s = Z.find(n, function (e) {
          return e.eventTarget === t;
        });
        s || (s = {
          eventTarget: t,
          events: {}
        }, n.push(s)), s.events[e] || (s.events[e] = []), t.addEventListener && !Z.contains(s.events[e], r) && (t.addEventListener(e, r, a.supportsOptions ? i : i.capture), s.events[e].push(r));
      }

      function l(t, e, r, o) {
        var i = In(o),
            s = Z.findIndex(n, function (e) {
          return e.eventTarget === t;
        }),
            u = n[s];
        if (u && u.events) if ("all" !== e) {
          var c = !1,
              f = u.events[e];

          if (f) {
            if ("all" === r) {
              for (var d = f.length - 1; d >= 0; d--) {
                l(t, e, f[d], i);
              }

              return;
            }

            for (var p = 0; p < f.length; p++) {
              if (f[p] === r) {
                t.removeEventListener(e, r, a.supportsOptions ? i : i.capture), f.splice(p, 1), 0 === f.length && (delete u.events[e], c = !0);
                break;
              }
            }
          }

          c && !Object.keys(u.events).length && n.splice(s, 1);
        } else for (e in u.events) {
          u.events.hasOwnProperty(e) && l(t, e, "all");
        }
      }

      function u(t, e) {
        for (var n = In(e), o = new kn(t), a = r[t.type], s = Mn(B.getEventTargets(t), 1)[0], l = s; i["default"].element(l);) {
          for (var u = 0; u < a.length; u++) {
            var c = a[u],
                f = c.selector,
                d = c.context;

            if (_.matchesSelector(l, f) && _.nodeContains(d, s) && _.nodeContains(d, l)) {
              var p = c.listeners;
              o.currentTarget = l;

              for (var v = 0; v < p.length; v++) {
                var h = Mn(p[v], 2),
                    g = h[0],
                    y = h[1],
                    m = y.capture,
                    b = y.passive;
                m === n.capture && b === n.passive && g(o);
              }
            }
          }

          l = _.parentNode(l);
        }
      }

      function c(t) {
        return u(t, !0);
      }

      return null == (e = t.document) || e.createElement("div").addEventListener("test", null, {
        get capture() {
          return a.supportsOptions = !0;
        },

        get passive() {
          return a.supportsPassive = !0;
        }

      }), t.events = a, a;
    }
  };
  Sn["default"] = Dn;
  var An = {};
  Object.defineProperty(An, "__esModule", {
    value: !0
  }), An["default"] = void 0;
  var Rn = {
    methodOrder: ["simulationResume", "mouseOrPen", "hasPointer", "idle"],
    search: function search(t) {
      for (var e = 0; e < Rn.methodOrder.length; e++) {
        var n;
        n = Rn.methodOrder[e];
        var r = Rn[n](t);
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
          if (s.simulation && !zn(s, n)) continue;
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
        if (zn(o, e)) return o;
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

  function zn(t, e) {
    return t.pointers.some(function (t) {
      return t.id === e;
    });
  }

  var Cn = Rn;
  An["default"] = Cn;
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
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) {
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
      }
    }(t, e) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return Bn(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? Bn(t, e) : void 0;
      }
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function Bn(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  function Wn(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function Ln(t, e) {
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

  function Vn(t, e) {
    return !e || "object" !== Xn(e) && "function" != typeof e ? function (t) {
      if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      return t;
    }(t) : e;
  }

  function Nn(t) {
    return (Nn = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  Object.defineProperty(Fn, "__esModule", {
    value: !0
  }), Fn["default"] = void 0;
  var qn = ["pointerDown", "pointerMove", "pointerUp", "updatePointer", "removePointer", "windowBlur"];

  function $n(t, e) {
    return function (n) {
      var r = e.interactions.list,
          o = B.getPointerType(n),
          i = Yn(B.getEventTargets(n), 2),
          a = i[0],
          s = i[1],
          l = [];

      if (/^touch/.test(n.type)) {
        e.prevTouchTime = e.now();

        for (var u = 0; u < n.changedTouches.length; u++) {
          var c = n.changedTouches[u],
              f = {
            pointer: c,
            pointerId: B.getPointerId(c),
            pointerType: o,
            eventType: n.type,
            eventTarget: a,
            curEventTarget: s,
            scope: e
          },
              d = Gn(f);
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
            pointerId: B.getPointerId(n),
            pointerType: o,
            eventType: n.type,
            curEventTarget: s,
            eventTarget: a,
            scope: e
          },
              g = Gn(h);
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

  function Gn(t) {
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

  function Hn(t, e) {
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

  var Kn = {
    id: "core/interactions",
    install: function install(t) {
      for (var e = {}, n = 0; n < qn.length; n++) {
        var r = qn[n];
        e[r] = $n(r, t);
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
        }(s, e);
        var n,
            r,
            o,
            i,
            a = (o = s, i = function () {
          if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
          if (Reflect.construct.sham) return !1;
          if ("function" == typeof Proxy) return !0;

          try {
            return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})), !0;
          } catch (t) {
            return !1;
          }
        }(), function () {
          var t,
              e = Nn(o);

          if (i) {
            var n = Nn(this).constructor;
            t = Reflect.construct(e, arguments, n);
          } else t = e.apply(this, arguments);

          return Vn(this, t);
        });

        function s() {
          return Wn(this, s), a.apply(this, arguments);
        }

        return n = s, (r = [{
          key: "pointerMoveTolerance",
          get: function get() {
            return t.interactions.pointerMoveTolerance;
          },
          set: function set(e) {
            t.interactions.pointerMoveTolerance = e;
          }
        }, {
          key: "_now",
          value: function value() {
            return t.now();
          }
        }]) && Ln(n.prototype, r), s;
      }(Le["default"]), t.interactions = {
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
        return Hn(t, "add");
      },
      "scope:remove-document": function scopeRemoveDocument(t) {
        return Hn(t, "remove");
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
    onDocSignal: Hn,
    doOnInteractions: $n,
    methodNames: qn
  };
  Fn["default"] = Kn;
  var Zn = {};

  function Jn(t) {
    return (Jn = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function Qn(t, e, n) {
    return (Qn = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (t, e, n) {
      var r = function (t, e) {
        for (; !Object.prototype.hasOwnProperty.call(t, e) && null !== (t = nr(t));) {
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

  function tr(t, e) {
    return (tr = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function er(t, e) {
    return !e || "object" !== Jn(e) && "function" != typeof e ? function (t) {
      if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
      return t;
    }(t) : e;
  }

  function nr(t) {
    return (nr = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function rr(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
  }

  function or(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function ir(t, e, n) {
    return e && or(t.prototype, e), n && or(t, n), t;
  }

  function ar(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(Zn, "__esModule", {
    value: !0
  }), Zn.initScope = lr, Zn.Scope = void 0;

  var sr = function () {
    function t() {
      var e = this;
      rr(this, t), ar(this, "id", "__interact_scope_".concat(Math.floor(100 * Math.random()))), ar(this, "isInitialized", !1), ar(this, "listenerMaps", []), ar(this, "browser", b["default"]), ar(this, "defaults", (0, ge["default"])(Me.defaults)), ar(this, "Eventable", cn.Eventable), ar(this, "actions", {
        map: {},
        phases: {
          start: !0,
          move: !0,
          end: !0
        },
        methodDict: {},
        phaselessTypes: {}
      }), ar(this, "interactStatic", (0, gn.createInteractStatic)(this)), ar(this, "InteractEvent", je.InteractEvent), ar(this, "Interactable", void 0), ar(this, "interactables", new wn.InteractableSet(this)), ar(this, "_win", void 0), ar(this, "document", void 0), ar(this, "window", void 0), ar(this, "documents", []), ar(this, "_plugins", {
        list: [],
        map: {}
      }), ar(this, "onWindowUnload", function (t) {
        return e.removeDocument(t.target);
      });
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
          }), e && tr(t, e);
        }(i, t);
        var e,
            r,
            o = (e = i, r = function () {
          if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
          if (Reflect.construct.sham) return !1;
          if ("function" == typeof Proxy) return !0;

          try {
            return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})), !0;
          } catch (t) {
            return !1;
          }
        }(), function () {
          var t,
              n = nr(e);

          if (r) {
            var o = nr(this).constructor;
            t = Reflect.construct(n, arguments, o);
          } else t = n.apply(this, arguments);

          return er(this, t);
        });

        function i() {
          return rr(this, i), o.apply(this, arguments);
        }

        return ir(i, [{
          key: "_defaults",
          get: function get() {
            return n.defaults;
          }
        }, {
          key: "set",
          value: function value(t) {
            return Qn(nr(i.prototype), "set", this).call(this, t), n.fire("interactable:set", {
              options: t,
              interactable: this
            }), this;
          }
        }, {
          key: "unset",
          value: function value() {
            Qn(nr(i.prototype), "unset", this).call(this), n.interactables.list.splice(n.interactables.list.indexOf(this), 1), n.fire("interactable:unset", {
              interactable: this
            });
          }
        }]), i;
      }(yn.Interactable);
    }

    return ir(t, [{
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
        return this.isInitialized ? this : lr(this, t);
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
            return t[e] = !0, t[ur(e)] = !0, t;
          }, {}); n < r; n++) {
            var i = this.listenerMaps[n].id;
            if (o[i] || o[ur(i)]) break;
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

  function lr(t, n) {
    return t.isInitialized = !0, i["default"].window(n) && e.init(n), h["default"].init(n), b["default"].init(n), jt["default"].init(n), t.window = n, t.document = n.document, t.usePlugin(Fn["default"]), t.usePlugin(Sn["default"]), t;
  }

  function ur(t) {
    return t && t.replace(/\/.*$/, "");
  }

  Zn.Scope = sr;
  var cr = {};
  Object.defineProperty(cr, "__esModule", {
    value: !0
  }), cr["default"] = void 0;
  var fr = new Zn.Scope(),
      dr = fr.interactStatic;
  cr["default"] = dr;
  var pr = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof window ? window : void 0;
  fr.init(pr);
  var vr = {};
  Object.defineProperty(vr, "__esModule", {
    value: !0
  }), vr["default"] = void 0, vr["default"] = function () {};
  var hr = {};
  Object.defineProperty(hr, "__esModule", {
    value: !0
  }), hr["default"] = void 0, hr["default"] = function () {};
  var gr = {};

  function yr(t, e) {
    return function (t) {
      if (Array.isArray(t)) return t;
    }(t) || function (t, e) {
      if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) {
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
      }
    }(t, e) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return mr(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? mr(t, e) : void 0;
      }
    }(t, e) || function () {
      throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }

  function mr(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(gr, "__esModule", {
    value: !0
  }), gr["default"] = void 0, gr["default"] = function (t) {
    var e = [["x", "y"], ["left", "top"], ["right", "bottom"], ["width", "height"]].filter(function (e) {
      var n = yr(e, 2),
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
        var f = yr(e[c], 2),
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
  var br = {};
  Object.defineProperty(br, "__esModule", {
    value: !0
  }), Object.defineProperty(br, "edgeTarget", {
    enumerable: !0,
    get: function get() {
      return vr["default"];
    }
  }), Object.defineProperty(br, "elements", {
    enumerable: !0,
    get: function get() {
      return hr["default"];
    }
  }), Object.defineProperty(br, "grid", {
    enumerable: !0,
    get: function get() {
      return gr["default"];
    }
  });
  var xr = {};
  Object.defineProperty(xr, "__esModule", {
    value: !0
  }), xr["default"] = void 0;
  var wr = {
    id: "snappers",
    install: function install(t) {
      var e = t.interactStatic;
      e.snappers = (0, j["default"])(e.snappers || {}, br), e.createSnapGrid = e.snappers.grid;
    }
  };
  xr["default"] = wr;
  var _r = {};

  function Pr(t, e) {
    var n = Object.keys(t);

    if (Object.getOwnPropertySymbols) {
      var r = Object.getOwnPropertySymbols(t);
      e && (r = r.filter(function (e) {
        return Object.getOwnPropertyDescriptor(t, e).enumerable;
      })), n.push.apply(n, r);
    }

    return n;
  }

  function Or(t) {
    for (var e = 1; e < arguments.length; e++) {
      var n = null != arguments[e] ? arguments[e] : {};
      e % 2 ? Pr(Object(n), !0).forEach(function (e) {
        Sr(t, e, n[e]);
      }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(n)) : Pr(Object(n)).forEach(function (e) {
        Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(n, e));
      });
    }

    return t;
  }

  function Sr(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(_r, "__esModule", {
    value: !0
  }), _r.aspectRatio = _r["default"] = void 0;
  var Er = {
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
        var f = new ye["default"](t.interaction);
        f.copyFrom(t.interaction.modification), f.prepareStates(l), e.subModification = f, f.startAll(Or({}, t));
      }
    },
    set: function set(t) {
      var e = t.state,
          n = t.rect,
          r = t.coords,
          o = (0, j["default"])({}, r),
          i = e.equalDelta ? Tr : Mr;
      if (i(e, e.xIsPrimaryAxis, r, n), !e.subModification) return null;
      var a = (0, j["default"])({}, n);
      (0, k.addEdges)(e.linkedEdges, a, {
        x: r.x - o.x,
        y: r.y - o.y
      });
      var s = e.subModification.setAll(Or(Or({}, t), {}, {
        rect: a,
        edges: e.linkedEdges,
        pageCoords: r,
        prevCoords: r,
        prevRect: a
      })),
          l = s.delta;
      return s.changed && (i(e, Math.abs(l.x) > Math.abs(l.y), s.coords, s.rect), (0, j["default"])(r, s.coords)), s.eventProps;
    },
    defaults: {
      ratio: "preserve",
      equalDelta: !1,
      modifiers: [],
      enabled: !1
    }
  };

  function Tr(t, e, n) {
    var r = t.startCoords,
        o = t.edgeSign;
    e ? n.y = r.y + (n.x - r.x) * o : n.x = r.x + (n.y - r.y) * o;
  }

  function Mr(t, e, n, r) {
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

  _r.aspectRatio = Er;
  var jr = (0, Se.makeModifier)(Er, "aspectRatio");
  _r["default"] = jr;
  var kr = {};
  Object.defineProperty(kr, "__esModule", {
    value: !0
  }), kr["default"] = void 0;

  var Ir = function Ir() {};

  Ir._defaults = {};
  var Dr = Ir;
  kr["default"] = Dr;
  var Ar = {};
  Object.defineProperty(Ar, "__esModule", {
    value: !0
  }), Object.defineProperty(Ar, "default", {
    enumerable: !0,
    get: function get() {
      return kr["default"];
    }
  });
  var Rr = {};

  function zr(t, e, n) {
    return i["default"].func(t) ? k.resolveRectLike(t, e.interactable, e.element, [n.x, n.y, e]) : k.resolveRectLike(t, e.interactable, e.element);
  }

  Object.defineProperty(Rr, "__esModule", {
    value: !0
  }), Rr.getRestrictionRect = zr, Rr.restrict = Rr["default"] = void 0;
  var Cr = {
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
        var u = zr(a.restriction, o, i);

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
          a = zr(o.restriction, n, e);

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
  Rr.restrict = Cr;
  var Fr = (0, Se.makeModifier)(Cr, "restrict");
  Rr["default"] = Fr;
  var Xr = {};
  Object.defineProperty(Xr, "__esModule", {
    value: !0
  }), Xr.restrictEdges = Xr["default"] = void 0;
  var Yr = {
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

  function Wr(t, e) {
    for (var n = ["top", "left", "bottom", "right"], r = 0; r < n.length; r++) {
      var o = n[r];
      o in t || (t[o] = e[o]);
    }

    return t;
  }

  var Lr = {
    noInner: Yr,
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
        Wr(l, Yr), Wr(u, Br), n.top ? e.y = Math.min(Math.max(u.top + i.top, s.y), l.top + i.top) : n.bottom && (e.y = Math.max(Math.min(u.bottom + i.bottom, s.y), l.bottom + i.bottom)), n.left ? e.x = Math.min(Math.max(u.left + i.left, s.x), l.left + i.left) : n.right && (e.x = Math.max(Math.min(u.right + i.right, s.x), l.right + i.right));
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
  Xr.restrictEdges = Lr;
  var Ur = (0, Se.makeModifier)(Lr, "restrictEdges");
  Xr["default"] = Ur;
  var Vr = {};
  Object.defineProperty(Vr, "__esModule", {
    value: !0
  }), Vr.restrictRect = Vr["default"] = void 0;
  var Nr = (0, j["default"])({
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
      qr = {
    start: Rr.restrict.start,
    set: Rr.restrict.set,
    defaults: Nr
  };
  Vr.restrictRect = qr;
  var $r = (0, Se.makeModifier)(qr, "restrictRect");
  Vr["default"] = $r;
  var Gr = {};
  Object.defineProperty(Gr, "__esModule", {
    value: !0
  }), Gr.restrictSize = Gr["default"] = void 0;
  var Hr = {
    width: -1 / 0,
    height: -1 / 0
  },
      Kr = {
    width: 1 / 0,
    height: 1 / 0
  },
      Zr = {
    start: function start(t) {
      return Xr.restrictEdges.start(t);
    },
    set: function set(t) {
      var e = t.interaction,
          n = t.state,
          r = t.rect,
          o = t.edges,
          i = n.options;

      if (o) {
        var a = k.tlbrToXywh((0, Rr.getRestrictionRect)(i.min, e, t.coords)) || Hr,
            s = k.tlbrToXywh((0, Rr.getRestrictionRect)(i.max, e, t.coords)) || Kr;
        n.options = {
          endOnly: i.endOnly,
          inner: (0, j["default"])({}, Xr.restrictEdges.noInner),
          outer: (0, j["default"])({}, Xr.restrictEdges.noOuter)
        }, o.top ? (n.options.inner.top = r.bottom - a.height, n.options.outer.top = r.bottom - s.height) : o.bottom && (n.options.inner.bottom = r.top + a.height, n.options.outer.bottom = r.top + s.height), o.left ? (n.options.inner.left = r.right - a.width, n.options.outer.left = r.right - s.width) : o.right && (n.options.inner.right = r.left + a.width, n.options.outer.right = r.left + s.width), Xr.restrictEdges.set(t), n.options = i;
      }
    },
    defaults: {
      min: null,
      max: null,
      endOnly: !1,
      enabled: !1
    }
  };
  Gr.restrictSize = Zr;
  var Jr = (0, Se.makeModifier)(Zr, "restrictSize");
  Gr["default"] = Jr;
  var Qr = {};
  Object.defineProperty(Qr, "__esModule", {
    value: !0
  }), Object.defineProperty(Qr, "default", {
    enumerable: !0,
    get: function get() {
      return kr["default"];
    }
  });
  var to = {};
  Object.defineProperty(to, "__esModule", {
    value: !0
  }), to.snap = to["default"] = void 0;
  var eo = {
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
      }) : [{
        index: 0,
        relativePoint: null,
        x: e.x,
        y: e.y
      }];
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
          var g,
              y = o.targets[v];
          (g = i["default"].func(y) ? y(d, p, e._proxy, f, v) : y) && u.push({
            x: (i["default"].number(g.x) ? g.x : d) + f.x,
            y: (i["default"].number(g.y) ? g.y : p) + f.y,
            range: i["default"].number(g.range) ? g.range : o.range,
            source: y,
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
            P = x.y - l.y,
            O = (0, C["default"])(_, P),
            S = O <= w;

        w === 1 / 0 && m.inRange && m.range !== 1 / 0 && (S = !1), m.target && !(S ? m.inRange && w !== 1 / 0 ? O / w < m.distance / m.range : w === 1 / 0 && m.range !== 1 / 0 || O < m.distance : !m.inRange && O < m.distance) || (m.target = x, m.distance = O, m.range = w, m.inRange = S, m.delta.x = _, m.delta.y = P);
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
  to.snap = eo;
  var no = (0, Se.makeModifier)(eo, "snap");
  to["default"] = no;
  var ro = {};

  function oo(t, e) {
    (null == e || e > t.length) && (e = t.length);

    for (var n = 0, r = Array(e); n < e; n++) {
      r[n] = t[n];
    }

    return r;
  }

  Object.defineProperty(ro, "__esModule", {
    value: !0
  }), ro.snapSize = ro["default"] = void 0;
  var io = {
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
      }, e.targetFields = e.targetFields || [["width", "height"], ["x", "y"]], to.snap.start(t), e.offsets = t.state.offsets, t.state = e;
    },
    set: function set(t) {
      var e,
          n,
          r = t.interaction,
          o = t.state,
          a = t.coords,
          s = o.options,
          l = o.offsets,
          u = {
        x: a.x - l[0].x,
        y: a.y - l[0].y
      };
      o.options = (0, j["default"])({}, s), o.options.targets = [];

      for (var c = 0; c < (s.targets || []).length; c++) {
        var f = (s.targets || [])[c],
            d = void 0;

        if (d = i["default"].func(f) ? f(u.x, u.y, r) : f) {
          for (var p = 0; p < o.targetFields.length; p++) {
            var v = (e = o.targetFields[p], n = 2, function (t) {
              if (Array.isArray(t)) return t;
            }(e) || function (t, e) {
              if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) {
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
              }
            }(e, n) || function (t, e) {
              if (t) {
                if ("string" == typeof t) return oo(t, e);
                var n = Object.prototype.toString.call(t).slice(8, -1);
                return "Object" === n && t.constructor && (n = t.constructor.name), "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? oo(t, e) : void 0;
              }
            }(e, n) || function () {
              throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }()),
                h = v[0],
                g = v[1];

            if (h in d || g in d) {
              d.x = d[h], d.y = d[g];
              break;
            }
          }

          o.options.targets.push(d);
        }
      }

      var y = to.snap.set(t);
      return o.options = s, y;
    },
    defaults: {
      range: 1 / 0,
      targets: null,
      offset: null,
      endOnly: !1,
      enabled: !1
    }
  };
  ro.snapSize = io;
  var ao = (0, Se.makeModifier)(io, "snapSize");
  ro["default"] = ao;
  var so = {};
  Object.defineProperty(so, "__esModule", {
    value: !0
  }), so.snapEdges = so["default"] = void 0;
  var lo = {
    start: function start(t) {
      var e = t.edges;
      return e ? (t.state.targetFields = t.state.targetFields || [[e.left ? "left" : "right", e.top ? "top" : "bottom"]], ro.snapSize.start(t)) : null;
    },
    set: ro.snapSize.set,
    defaults: (0, j["default"])((0, ge["default"])(ro.snapSize.defaults), {
      targets: null,
      range: null,
      offset: {
        x: 0,
        y: 0
      }
    })
  };
  so.snapEdges = lo;
  var uo = (0, Se.makeModifier)(lo, "snapEdges");
  so["default"] = uo;
  var co = {};
  Object.defineProperty(co, "__esModule", {
    value: !0
  }), Object.defineProperty(co, "default", {
    enumerable: !0,
    get: function get() {
      return kr["default"];
    }
  });
  var fo = {};
  Object.defineProperty(fo, "__esModule", {
    value: !0
  }), Object.defineProperty(fo, "default", {
    enumerable: !0,
    get: function get() {
      return kr["default"];
    }
  });
  var po = {};
  Object.defineProperty(po, "__esModule", {
    value: !0
  }), po["default"] = void 0;
  var vo = {
    aspectRatio: _r["default"],
    restrictEdges: Xr["default"],
    restrict: Rr["default"],
    restrictRect: Vr["default"],
    restrictSize: Gr["default"],
    snapEdges: so["default"],
    snap: to["default"],
    snapSize: ro["default"],
    spring: co["default"],
    avoid: Ar["default"],
    transform: fo["default"],
    rubberband: Qr["default"]
  };
  po["default"] = vo;
  var ho = {};
  Object.defineProperty(ho, "__esModule", {
    value: !0
  }), ho["default"] = void 0;
  var go = {
    id: "modifiers",
    install: function install(t) {
      var e = t.interactStatic;

      for (var n in t.usePlugin(Se["default"]), t.usePlugin(xr["default"]), e.modifiers = po["default"], po["default"]) {
        var r = po["default"][n],
            o = r._defaults,
            i = r._methods;
        o._methods = i, t.defaults.perAction[n] = o;
      }
    }
  };
  ho["default"] = go;
  var yo = {};

  function mo(t) {
    return (mo = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  function bo(t, e) {
    for (var n = 0; n < e.length; n++) {
      var r = e[n];
      r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r);
    }
  }

  function xo(t, e) {
    return (xo = Object.setPrototypeOf || function (t, e) {
      return t.__proto__ = e, t;
    })(t, e);
  }

  function wo(t, e) {
    return !e || "object" !== mo(e) && "function" != typeof e ? _o(t) : e;
  }

  function _o(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
  }

  function Po(t) {
    return (Po = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
      return t.__proto__ || Object.getPrototypeOf(t);
    })(t);
  }

  function Oo(t, e, n) {
    return e in t ? Object.defineProperty(t, e, {
      value: n,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }) : t[e] = n, t;
  }

  Object.defineProperty(yo, "__esModule", {
    value: !0
  }), yo.PointerEvent = yo["default"] = void 0;

  var So = function (t) {
    !function (t, e) {
      if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
      t.prototype = Object.create(e && e.prototype, {
        constructor: {
          value: t,
          writable: !0,
          configurable: !0
        }
      }), e && xo(t, e);
    }(a, t);
    var e,
        n,
        r,
        o,
        i = (r = a, o = function () {
      if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
      if (Reflect.construct.sham) return !1;
      if ("function" == typeof Proxy) return !0;

      try {
        return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})), !0;
      } catch (t) {
        return !1;
      }
    }(), function () {
      var t,
          e = Po(r);

      if (o) {
        var n = Po(this).constructor;
        t = Reflect.construct(e, arguments, n);
      } else t = e.apply(this, arguments);

      return wo(this, t);
    });

    function a(t, e, n, r, o, s) {
      var l;

      if (function (t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
      }(this, a), Oo(_o(l = i.call(this, o)), "type", void 0), Oo(_o(l), "originalEvent", void 0), Oo(_o(l), "pointerId", void 0), Oo(_o(l), "pointerType", void 0), Oo(_o(l), "double", void 0), Oo(_o(l), "pageX", void 0), Oo(_o(l), "pageY", void 0), Oo(_o(l), "clientX", void 0), Oo(_o(l), "clientY", void 0), Oo(_o(l), "dt", void 0), Oo(_o(l), "eventable", void 0), B.pointerExtend(_o(l), n), n !== e && B.pointerExtend(_o(l), e), l.timeStamp = s, l.originalEvent = n, l.type = t, l.pointerId = B.getPointerId(e), l.pointerType = B.getPointerType(e), l.target = r, l.currentTarget = null, "tap" === t) {
        var u = o.getPointerIndex(e);
        l.dt = l.timeStamp - o.pointers[u].downTime;
        var c = l.timeStamp - o.tapTime;
        l["double"] = !!(o.prevTap && "doubletap" !== o.prevTap.type && o.prevTap.target === l.target && c < 500);
      } else "doubletap" === t && (l.dt = e.timeStamp - o.tapTime);

      return l;
    }

    return e = a, (n = [{
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
    }]) && bo(e.prototype, n), a;
  }($.BaseEvent);

  yo.PointerEvent = yo["default"] = So;
  var Eo = {};
  Object.defineProperty(Eo, "__esModule", {
    value: !0
  }), Eo["default"] = void 0;
  var To = {
    id: "pointer-events/base",
    before: ["inertia", "modifiers", "auto-start", "actions"],
    install: function install(t) {
      t.pointerEvents = To, t.defaults.actions.pointerEvents = To.defaults, (0, j["default"])(t.actions.phaselessTypes, To.types);
    },
    listeners: {
      "interactions:new": function interactionsNew(t) {
        var e = t.interaction;
        e.prevTap = null, e.tapTime = 0;
      },
      "interactions:update-pointer": function interactionsUpdatePointer(t) {
        var e = t.down,
            n = t.pointerInfo;
        !e && n.hold || (n.hold = {
          duration: 1 / 0,
          timeout: null
        });
      },
      "interactions:move": function interactionsMove(t, e) {
        var n = t.interaction,
            r = t.pointer,
            o = t.event,
            i = t.eventTarget;
        t.duplicate || n.pointerIsDown && !n.pointerWasMoved || (n.pointerIsDown && ko(t), Mo({
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

          if (u.targets.length) {
            for (var d = 1 / 0, p = 0; p < u.targets.length; p++) {
              var v = u.targets[p].eventable.options.holdDuration;
              v < d && (d = v);
            }

            s.duration = d, s.timeout = setTimeout(function () {
              Mo({
                interaction: n,
                eventTarget: i,
                pointer: r,
                event: o,
                type: "hold"
              }, e);
            }, d);
          }
        }(t, e), Mo(t, e);
      },
      "interactions:up": function interactionsUp(t, e) {
        ko(t), Mo(t, e), function (t, e) {
          var n = t.interaction,
              r = t.pointer,
              o = t.event,
              i = t.eventTarget;
          n.pointerWasMoved || Mo({
            interaction: n,
            eventTarget: i,
            pointer: r,
            event: o,
            type: "tap"
          }, e);
        }(t, e);
      },
      "interactions:cancel": function interactionsCancel(t, e) {
        ko(t), Mo(t, e);
      }
    },
    PointerEvent: yo.PointerEvent,
    fire: Mo,
    collectEventTargets: jo,
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

  function Mo(t, e) {
    var n = t.interaction,
        r = t.pointer,
        o = t.event,
        i = t.eventTarget,
        a = t.type,
        s = t.targets,
        l = void 0 === s ? jo(t, e) : s,
        u = new yo.PointerEvent(a, r, o, i, n, e.now());
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
      var h = u["double"] ? Mo({
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

  function jo(t, e) {
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
      var e;
      return t.eventable.options.holdDuration === (null == (e = n.pointers[s]) ? void 0 : e.hold.duration);
    })), c.targets;
  }

  function ko(t) {
    var e = t.interaction,
        n = t.pointerIndex,
        r = e.pointers[n].hold;
    r && r.timeout && (clearTimeout(r.timeout), r.timeout = null);
  }

  var Io = To;
  Eo["default"] = Io;
  var Do = {};

  function Ao(t) {
    var e = t.interaction;
    e.holdIntervalHandle && (clearInterval(e.holdIntervalHandle), e.holdIntervalHandle = null);
  }

  Object.defineProperty(Do, "__esModule", {
    value: !0
  }), Do["default"] = void 0;
  var Ro = {
    id: "pointer-events/holdRepeat",
    install: function install(t) {
      t.usePlugin(Eo["default"]);
      var e = t.pointerEvents;
      e.defaults.holdRepeatInterval = 0, e.types.holdrepeat = t.actions.phaselessTypes.holdrepeat = !0;
    },
    listeners: ["move", "up", "cancel", "endall"].reduce(function (t, e) {
      return t["pointerEvents:".concat(e)] = Ao, t;
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
  Do["default"] = Ro;
  var zo = {};

  function Co(t) {
    return (0, j["default"])(this.events.options, t), this;
  }

  Object.defineProperty(zo, "__esModule", {
    value: !0
  }), zo["default"] = void 0;
  var Fo = {
    id: "pointer-events/interactableTargets",
    install: function install(t) {
      var e = t.Interactable;
      e.prototype.pointerEvents = Co;
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
  zo["default"] = Fo;
  var Xo = {};
  Object.defineProperty(Xo, "__esModule", {
    value: !0
  }), Xo["default"] = void 0;
  var Yo = {
    id: "pointer-events",
    install: function install(t) {
      t.usePlugin(Eo), t.usePlugin(Do["default"]), t.usePlugin(zo["default"]);
    }
  };
  Xo["default"] = Yo;
  var Bo = {};

  function Wo(t) {
    var e = t.Interactable;
    t.actions.phases.reflow = !0, e.prototype.reflow = function (e) {
      return function (t, e, n) {
        for (var r = i["default"].string(t.target) ? Z.from(t._context.querySelectorAll(t.target)) : [t.target], o = n.window.Promise, a = o ? [] : null, s = function s() {
          var i = r[l],
              s = t.getRect(i);
          if (!s) return "break";
          var u = Z.find(n.interactions.list, function (n) {
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
                p = B.coordsToEvent(d);

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
              i.interactable = e, i.element = n, i.prevEvent = o, i.updatePointer(o, o, n, !0), B.setZeroCoords(i.coords.delta), (0, Yt.copyAction)(i.prepared, r), i._doPhase(a);
              var s = t.window.Promise,
                  l = s ? new s(function (t) {
                i._reflowResolve = t;
              }) : void 0;
              return i._reflowPromise = l, i.start(r, e, n), i._interacting ? (i.move(a), i.end(o)) : (i.stop(), i._reflowResolve()), i.removePointer(o, o), l;
            }(n, t, i, e, p);
          }
          a && a.push(c);
        }, l = 0; l < r.length && "break" !== s(); l++) {
          ;
        }

        return a && o.all(a).then(function () {
          return t;
        });
      }(this, e, t);
    };
  }

  Object.defineProperty(Bo, "__esModule", {
    value: !0
  }), Bo.install = Wo, Bo["default"] = void 0;
  var Lo = {
    id: "reflow",
    install: Wo,
    listeners: {
      "interactions:stop": function interactionsStop(t, e) {
        var n = t.interaction;
        "reflow" === n.pointerType && (n._reflowResolve && n._reflowResolve(), Z.remove(e.interactions.list, n));
      }
    }
  };
  Bo["default"] = Lo;
  var Uo = {
    exports: {}
  };

  function Vo(t) {
    return (Vo = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(Uo.exports, "__esModule", {
    value: !0
  }), Uo.exports["default"] = void 0, cr["default"].use(se["default"]), cr["default"].use(Ge["default"]), cr["default"].use(Xo["default"]), cr["default"].use(en["default"]), cr["default"].use(ho["default"]), cr["default"].use(ie["default"]), cr["default"].use(Tt["default"]), cr["default"].use(Rt["default"]), cr["default"].use(Bo["default"]);
  var No = cr["default"];
  if (Uo.exports["default"] = No, "object" === Vo(Uo) && Uo) try {
    Uo.exports = cr["default"];
  } catch (t) {}
  cr["default"]["default"] = cr["default"], Uo = Uo.exports;
  var qo = {
    exports: {}
  };

  function $o(t) {
    return ($o = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }

  Object.defineProperty(qo.exports, "__esModule", {
    value: !0
  }), qo.exports["default"] = void 0;
  var Go = Uo["default"];
  if (qo.exports["default"] = Go, "object" === $o(qo) && qo) try {
    qo.exports = Uo["default"];
  } catch (t) {}
  return Uo["default"]["default"] = Uo["default"], qo.exports;
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--36-0!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.js?vue&type=script&lang=js& ***!
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
  !*** ./node_modules/babel-loader/lib??ref--36-0!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.js?vue&type=script&lang=js& ***!
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
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--32-2!./node_modules/sass-loader/dist/cjs.js??ref--32-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& ***!
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
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--32-2!./node_modules/sass-loader/dist/cjs.js??ref--32-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "/* stylelint-disable property-no-vendor-prefix, property-no-unkown */\n.ccm-avatar-creator-container[data-v-838c3fb0] {\n  position: relative;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions[data-v-838c3fb0] {\n  position: absolute;\n  z-index: 20000;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-838c3fb0] {\n  display: inline-block;\n  font-weight: 600;\n  opacity: 0.8;\n  text-align: center;\n  text-decoration: none;\n  transition: all 0.5s;\n  width: 50%;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-838c3fb0]:hover {\n  opacity: 1;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a[data-v-838c3fb0]::before {\n  font-family: \"Font Awesome 5 Free\";\n  font-size: 16px;\n  text-align: center;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-cancel[data-v-838c3fb0] {\n  color: #ff4136;\n  float: right;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-cancel[data-v-838c3fb0]::before {\n  content: \"\\F00D\";\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-okay[data-v-838c3fb0] {\n  color: #3d9970;\n}\n.ccm-avatar-creator-container .ccm-avatar-actions > a.ccm-avatar-okay[data-v-838c3fb0]::before {\n  content: \"\\F00C\";\n}\n.ccm-avatar-creator-container .ccm-avatar-creator[data-v-838c3fb0] {\n  border: solid 1px #999;\n  overflow: hidden;\n  position: relative;\n  transition: all 0.3s;\n  z-index: 10000;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator > img.ccm-avatar-current[data-v-838c3fb0] {\n  display: inline;\n  height: 100%;\n  width: 100%;\n  z-index: 998;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator > div.saving[data-v-838c3fb0] {\n  background: rgba(127, 219, 255, 0.5);\n  color: #111;\n  font-size: 16px;\n  font-weight: bolder;\n  height: 100%;\n  left: 0;\n  position: absolute;\n  text-align: center;\n  top: 0;\n  width: 100%;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator[data-v-838c3fb0]::before {\n  background-color: rgba(238, 238, 238, 0.8);\n  color: #3d9970;\n  content: \"\\F303\";\n  display: block;\n  font-family: \"Font Awesome 5 Free\";\n  font-weight: 600;\n  height: 100%;\n  line-height: 0%;\n  margin: 0 auto;\n  opacity: 0;\n  padding-top: 50%;\n  position: absolute;\n  text-align: center;\n  transition: all 0.3s;\n  vertical-align: middle;\n  width: 100%;\n  z-index: 999;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-started[data-v-838c3fb0]::before {\n  -webkit-animation: pulse-data-v-838c3fb0 1s infinite;\n  animation: pulse-data-v-838c3fb0 1s infinite;\n  opacity: 1;\n}\n@-webkit-keyframes pulse-data-v-838c3fb0 {\n0% {\n    transform: scale(1);\n}\n50% {\n    transform: scale(1.3);\n}\n100% {\n    transform: scale(1);\n}\n}\n@keyframes pulse-data-v-838c3fb0 {\n0% {\n    transform: scale(1);\n}\n50% {\n    transform: scale(1.3);\n}\n100% {\n    transform: scale(1);\n}\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.editing[data-v-838c3fb0]::before {\n  display: none;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-838c3fb0] {\n  cursor: \"pointer\";\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-838c3fb0]:hover, .ccm-avatar-creator-container .ccm-avatar-creator.dz-drag-hover[data-v-838c3fb0] {\n  border-color: #3d9970;\n  box-shadow: 0 0 20px -10px #2ecc40;\n}\n.ccm-avatar-creator-container .ccm-avatar-creator.dz-clickable[data-v-838c3fb0]:hover::before, .ccm-avatar-creator-container .ccm-avatar-creator.dz-drag-hover[data-v-838c3fb0]::before {\n  opacity: 1;\n}", ""]);

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--32-2!./node_modules/sass-loader/dist/cjs.js??ref--32-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--32-2!../../../../../../../../sass-loader/dist/cjs.js??ref--32-3!./Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Avatar.scss?vue&type=style&index=0&id=547cd8e4&lang=scss&scoped=true&");

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
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--32-2!./node_modules/sass-loader/dist/cjs.js??ref--32-3!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../css-loader!../../../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../postcss-loader/src??ref--32-2!../../../../../../../../sass-loader/dist/cjs.js??ref--32-3!./Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/@concretecms/bedrock/assets/account/js/frontend/components/Avatar/Cropper.scss?vue&type=style&index=0&id=838c3fb0&lang=scss&scoped=true&");

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

/***/ 8:
/*!*******************************************************************************!*\
  !*** multi ./node_modules/@concretecms/bedrock/assets/account/js/frontend.js ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/node_modules/@concretecms/bedrock/assets/account/js/frontend.js */"./node_modules/@concretecms/bedrock/assets/account/js/frontend.js");


/***/ })

/******/ });