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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/blocks/gallery/gallery.js":
/*!******************************************!*\
  !*** ./assets/blocks/gallery/gallery.js ***!
  \******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _concretecms_bedrock_assets_cms_components_gallery_GalleryEdit__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @concretecms/bedrock/assets/cms/components/gallery/GalleryEdit */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue");

window.Concrete.Vue.createContext('gallery', {
  GalleryEdit: _concretecms_bedrock_assets_cms_components_gallery_GalleryEdit__WEBPACK_IMPORTED_MODULE_0__["default"]
});

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue":
/*!**************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue ***!
  \**************************************************************************/
/*! exports provided: icons, types, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Icon_vue_vue_type_template_id_4f5a39bd_functional_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Icon.vue?vue&type=template&id=4f5a39bd&functional=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=template&id=4f5a39bd&functional=true&");
/* harmony import */ var _Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Icon.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "icons", function() { return _Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["icons"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["types"]; });

/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Icon_vue_vue_type_template_id_4f5a39bd_functional_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Icon_vue_vue_type_template_id_4f5a39bd_functional_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  true,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default, icons, types */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../babel-loader/lib??ref--32-0!../../../../../vue-loader/lib??vue-loader-options!./Icon.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "icons", function() { return _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["icons"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["types"]; });

 /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=template&id=4f5a39bd&functional=true&":
/*!*************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=template&id=4f5a39bd&functional=true& ***!
  \*************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_template_id_4f5a39bd_functional_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../vue-loader/lib??vue-loader-options!./Icon.vue?vue&type=template&id=4f5a39bd&functional=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=template&id=4f5a39bd&functional=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_template_id_4f5a39bd_functional_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_template_id_4f5a39bd_functional_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue":
/*!********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue ***!
  \********************************************************************************/
/*! exports provided: types, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _IconButton_vue_vue_type_template_id_2ba8a44f_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true&");
/* harmony import */ var _IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./IconButton.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["types"]; });

/* harmony import */ var _IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _IconButton_vue_vue_type_template_id_2ba8a44f_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _IconButton_vue_vue_type_template_id_2ba8a44f_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  true,
  null,
  "2ba8a44f",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************/
/*! exports provided: default, types */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../babel-loader/lib??ref--32-0!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["types"]; });

 /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&":
/*!******************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& ***!
  \******************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../style-loader!../../../../../css-loader!../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../postcss-loader/src??ref--28-2!../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true&":
/*!*******************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true& ***!
  \*******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_template_id_2ba8a44f_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_template_id_2ba8a44f_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_template_id_2ba8a44f_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue":
/*!*****************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue ***!
  \*****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _GalleryEdit_vue_vue_type_template_id_a5d890aa_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true&");
/* harmony import */ var _GalleryEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./GalleryEdit.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _GalleryEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _GalleryEdit_vue_vue_type_template_id_a5d890aa_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _GalleryEdit_vue_vue_type_template_id_a5d890aa_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "a5d890aa",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../babel-loader/lib??ref--32-0!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../style-loader!../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--28-2!../../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true&":
/*!************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true& ***!
  \************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_template_id_a5d890aa_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_template_id_a5d890aa_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_template_id_a5d890aa_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue":
/*!***************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ImageCell_vue_vue_type_template_id_ad918c68_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true&");
/* harmony import */ var _ImageCell_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ImageCell.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _ImageCell_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ImageCell_vue_vue_type_template_id_ad918c68_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ImageCell_vue_vue_type_template_id_ad918c68_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  true,
  null,
  "ad918c68",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../babel-loader/lib??ref--32-0!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&":
/*!*************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& ***!
  \*************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../style-loader!../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--28-2!../../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true&":
/*!**************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true& ***!
  \**************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_template_id_ad918c68_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_template_id_ad918c68_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_template_id_ad918c68_scoped_true_functional_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue":
/*!*****************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue ***!
  \*****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ImageDetail_vue_vue_type_template_id_ec81668a_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true&");
/* harmony import */ var _ImageDetail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ImageDetail.vue?vue&type=script&lang=js& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&");
/* harmony import */ var _vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _ImageDetail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ImageDetail_vue_vue_type_template_id_ec81668a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ImageDetail_vue_vue_type_template_id_ec81668a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "ec81668a",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../babel-loader/lib??ref--32-0!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_32_0_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../style-loader!../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--28-2!../../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_28_2_sass_loader_dist_cjs_js_ref_28_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true&":
/*!************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true& ***!
  \************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_template_id_ec81668a_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_template_id_ec81668a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _vue_loader_lib_loaders_templateLoader_js_vue_loader_options_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_template_id_ec81668a_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/iconlist.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/iconlist.js ***!
  \*****************************************************************************/
/*! exports provided: types, icons */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "types", function() { return types; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "icons", function() { return icons; });
var types = {
  svg: 'svg',
  fontawesomeSolid: 'fas',
  fontawesomeRegular: 'far',
  fontawesomeBrand: 'fab',
  fas: 'fas',
  far: 'far',
  fab: 'fab'
};
var icons = {
  svg: {
    menuLauncher: 'menu-launcher',
    search: 'search',
    dialogHelp: 'dialog-help',
    cog: 'cog',
    dashboard: 'dashboard',
    plus: 'plus',
    info: 'info',
    bookmarkPage: 'bookmark-page',
    pencil: 'pencil',
    sitemap: 'sitemap',
    arrowLeft: 'arrow-left',
    help: 'help',
    dialogClose: 'dialog-close'
  },
  fas: {
    ad: 'ad',
    addressBook: 'address-book',
    addressCard: 'address-card',
    adjust: 'adjust',
    airFreshener: 'air-freshener',
    alignCenter: 'align-center',
    alignJustify: 'align-justify',
    alignLeft: 'align-left',
    alignRight: 'align-right',
    allergies: 'allergies',
    ambulance: 'ambulance',
    americanSignLanguageInterpreting: 'american-sign-language-interpreting',
    anchor: 'anchor',
    angleDoubleDown: 'angle-double-down',
    angleDoubleLeft: 'angle-double-left',
    angleDoubleRight: 'angle-double-right',
    angleDoubleUp: 'angle-double-up',
    angleDown: 'angle-down',
    angleLeft: 'angle-left',
    angleRight: 'angle-right',
    angleUp: 'angle-up',
    angry: 'angry',
    ankh: 'ankh',
    appleAlt: 'apple-alt',
    archive: 'archive',
    archway: 'archway',
    arrowAltCircleDown: 'arrow-alt-circle-down',
    arrowAltCircleLeft: 'arrow-alt-circle-left',
    arrowAltCircleRight: 'arrow-alt-circle-right',
    arrowAltCircleUp: 'arrow-alt-circle-up',
    arrowCircleDown: 'arrow-circle-down',
    arrowCircleLeft: 'arrow-circle-left',
    arrowCircleRight: 'arrow-circle-right',
    arrowCircleUp: 'arrow-circle-up',
    arrowDown: 'arrow-down',
    arrowLeft: 'arrow-left',
    arrowRight: 'arrow-right',
    arrowUp: 'arrow-up',
    arrowsAlt: 'arrows-alt',
    arrowsAltH: 'arrows-alt-h',
    arrowsAltV: 'arrows-alt-v',
    assistiveListeningSystems: 'assistive-listening-systems',
    asterisk: 'asterisk',
    at: 'at',
    atlas: 'atlas',
    atom: 'atom',
    audioDescription: 'audio-description',
    award: 'award',
    baby: 'baby',
    babyCarriage: 'baby-carriage',
    backspace: 'backspace',
    backward: 'backward',
    bacon: 'bacon',
    bahai: 'bahai',
    balanceScale: 'balance-scale',
    balanceScaleLeft: 'balance-scale-left',
    balanceScaleRight: 'balance-scale-right',
    ban: 'ban',
    bandAid: 'band-aid',
    barcode: 'barcode',
    bars: 'bars',
    baseballBall: 'baseball-ball',
    basketballBall: 'basketball-ball',
    bath: 'bath',
    batteryEmpty: 'battery-empty',
    batteryFull: 'battery-full',
    batteryHalf: 'battery-half',
    batteryQuarter: 'battery-quarter',
    batteryThreeQuarters: 'battery-three-quarters',
    bed: 'bed',
    beer: 'beer',
    bell: 'bell',
    bellSlash: 'bell-slash',
    bezierCurve: 'bezier-curve',
    bible: 'bible',
    bicycle: 'bicycle',
    biking: 'biking',
    binoculars: 'binoculars',
    biohazard: 'biohazard',
    birthdayCake: 'birthday-cake',
    blender: 'blender',
    blenderPhone: 'blender-phone',
    blind: 'blind',
    blog: 'blog',
    bold: 'bold',
    bolt: 'bolt',
    bomb: 'bomb',
    bone: 'bone',
    bong: 'bong',
    book: 'book',
    bookDead: 'book-dead',
    bookMedical: 'book-medical',
    bookOpen: 'book-open',
    bookReader: 'book-reader',
    bookmark: 'bookmark',
    borderAll: 'border-all',
    borderNone: 'border-none',
    borderStyle: 'border-style',
    bowlingBall: 'bowling-ball',
    box: 'box',
    boxOpen: 'box-open',
    boxTissue: 'box-tissue',
    boxes: 'boxes',
    braille: 'braille',
    brain: 'brain',
    breadSlice: 'bread-slice',
    briefcase: 'briefcase',
    briefcaseMedical: 'briefcase-medical',
    broadcastTower: 'broadcast-tower',
    broom: 'broom',
    brush: 'brush',
    bug: 'bug',
    building: 'building',
    bullhorn: 'bullhorn',
    bullseye: 'bullseye',
    burn: 'burn',
    bus: 'bus',
    busAlt: 'bus-alt',
    businessTime: 'business-time',
    calculator: 'calculator',
    calendar: 'calendar',
    calendarAlt: 'calendar-alt',
    calendarCheck: 'calendar-check',
    calendarDay: 'calendar-day',
    calendarMinus: 'calendar-minus',
    calendarPlus: 'calendar-plus',
    calendarTimes: 'calendar-times',
    calendarWeek: 'calendar-week',
    camera: 'camera',
    cameraRetro: 'camera-retro',
    campground: 'campground',
    candyCane: 'candy-cane',
    cannabis: 'cannabis',
    capsules: 'capsules',
    car: 'car',
    carAlt: 'car-alt',
    carBattery: 'car-battery',
    carCrash: 'car-crash',
    carSide: 'car-side',
    caravan: 'caravan',
    caretDown: 'caret-down',
    caretLeft: 'caret-left',
    caretRight: 'caret-right',
    caretSquareDown: 'caret-square-down',
    caretSquareLeft: 'caret-square-left',
    caretSquareRight: 'caret-square-right',
    caretSquareUp: 'caret-square-up',
    caretUp: 'caret-up',
    carrot: 'carrot',
    cartArrowDown: 'cart-arrow-down',
    cartPlus: 'cart-plus',
    cashRegister: 'cash-register',
    cat: 'cat',
    certificate: 'certificate',
    chair: 'chair',
    chalkboard: 'chalkboard',
    chalkboardTeacher: 'chalkboard-teacher',
    chargingStation: 'charging-station',
    chartArea: 'chart-area',
    chartBar: 'chart-bar',
    chartLine: 'chart-line',
    chartPie: 'chart-pie',
    check: 'check',
    checkCircle: 'check-circle',
    checkDouble: 'check-double',
    checkSquare: 'check-square',
    cheese: 'cheese',
    chess: 'chess',
    chessBishop: 'chess-bishop',
    chessBoard: 'chess-board',
    chessKing: 'chess-king',
    chessKnight: 'chess-knight',
    chessPawn: 'chess-pawn',
    chessQueen: 'chess-queen',
    chessRook: 'chess-rook',
    chevronCircleDown: 'chevron-circle-down',
    chevronCircleLeft: 'chevron-circle-left',
    chevronCircleRight: 'chevron-circle-right',
    chevronCircleUp: 'chevron-circle-up',
    chevronDown: 'chevron-down',
    chevronLeft: 'chevron-left',
    chevronRight: 'chevron-right',
    chevronUp: 'chevron-up',
    child: 'child',
    church: 'church',
    circle: 'circle',
    circleNotch: 'circle-notch',
    city: 'city',
    clinicMedical: 'clinic-medical',
    clipboard: 'clipboard',
    clipboardCheck: 'clipboard-check',
    clipboardList: 'clipboard-list',
    clock: 'clock',
    clone: 'clone',
    closedCaptioning: 'closed-captioning',
    cloud: 'cloud',
    cloudDownloadAlt: 'cloud-download-alt',
    cloudMeatball: 'cloud-meatball',
    cloudMoon: 'cloud-moon',
    cloudMoonRain: 'cloud-moon-rain',
    cloudRain: 'cloud-rain',
    cloudShowersHeavy: 'cloud-showers-heavy',
    cloudSun: 'cloud-sun',
    cloudSunRain: 'cloud-sun-rain',
    cloudUploadAlt: 'cloud-upload-alt',
    cocktail: 'cocktail',
    code: 'code',
    codeBranch: 'code-branch',
    coffee: 'coffee',
    cog: 'cog',
    cogs: 'cogs',
    coins: 'coins',
    columns: 'columns',
    comment: 'comment',
    commentAlt: 'comment-alt',
    commentDollar: 'comment-dollar',
    commentDots: 'comment-dots',
    commentMedical: 'comment-medical',
    commentSlash: 'comment-slash',
    comments: 'comments',
    commentsDollar: 'comments-dollar',
    compactDisc: 'compact-disc',
    compass: 'compass',
    compress: 'compress',
    compressAlt: 'compress-alt',
    compressArrowsAlt: 'compress-arrows-alt',
    conciergeBell: 'concierge-bell',
    cookie: 'cookie',
    cookieBite: 'cookie-bite',
    copy: 'copy',
    copyright: 'copyright',
    couch: 'couch',
    creditCard: 'credit-card',
    crop: 'crop',
    cropAlt: 'crop-alt',
    cross: 'cross',
    crosshairs: 'crosshairs',
    crow: 'crow',
    crown: 'crown',
    crutch: 'crutch',
    cube: 'cube',
    cubes: 'cubes',
    cut: 'cut',
    database: 'database',
    deaf: 'deaf',
    democrat: 'democrat',
    desktop: 'desktop',
    dharmachakra: 'dharmachakra',
    diagnoses: 'diagnoses',
    dice: 'dice',
    diceD20: 'dice-d20',
    diceD6: 'dice-d6',
    diceFive: 'dice-five',
    diceFour: 'dice-four',
    diceOne: 'dice-one',
    diceSix: 'dice-six',
    diceThree: 'dice-three',
    diceTwo: 'dice-two',
    digitalTachograph: 'digital-tachograph',
    directions: 'directions',
    disease: 'disease',
    divide: 'divide',
    dizzy: 'dizzy',
    dna: 'dna',
    dog: 'dog',
    dollarSign: 'dollar-sign',
    dolly: 'dolly',
    dollyFlatbed: 'dolly-flatbed',
    donate: 'donate',
    doorClosed: 'door-closed',
    doorOpen: 'door-open',
    dotCircle: 'dot-circle',
    dove: 'dove',
    download: 'download',
    draftingCompass: 'drafting-compass',
    dragon: 'dragon',
    drawPolygon: 'draw-polygon',
    drum: 'drum',
    drumSteelpan: 'drum-steelpan',
    drumstickBite: 'drumstick-bite',
    dumbbell: 'dumbbell',
    dumpster: 'dumpster',
    dumpsterFire: 'dumpster-fire',
    dungeon: 'dungeon',
    edit: 'edit',
    egg: 'egg',
    eject: 'eject',
    ellipsisH: 'ellipsis-h',
    ellipsisV: 'ellipsis-v',
    envelope: 'envelope',
    envelopeOpen: 'envelope-open',
    envelopeOpenText: 'envelope-open-text',
    envelopeSquare: 'envelope-square',
    equals: 'equals',
    eraser: 'eraser',
    ethernet: 'ethernet',
    euroSign: 'euro-sign',
    exchangeAlt: 'exchange-alt',
    exclamation: 'exclamation',
    exclamationCircle: 'exclamation-circle',
    exclamationTriangle: 'exclamation-triangle',
    expand: 'expand',
    expandAlt: 'expand-alt',
    expandArrowsAlt: 'expand-arrows-alt',
    externalLinkAlt: 'external-link-alt',
    externalLinkSquareAlt: 'external-link-square-alt',
    eye: 'eye',
    eyeDropper: 'eye-dropper',
    eyeSlash: 'eye-slash',
    fan: 'fan',
    fastBackward: 'fast-backward',
    fastForward: 'fast-forward',
    faucet: 'faucet',
    fax: 'fax',
    feather: 'feather',
    featherAlt: 'feather-alt',
    female: 'female',
    fighterJet: 'fighter-jet',
    file: 'file',
    fileAlt: 'file-alt',
    fileArchive: 'file-archive',
    fileAudio: 'file-audio',
    fileCode: 'file-code',
    fileContract: 'file-contract',
    fileCsv: 'file-csv',
    fileDownload: 'file-download',
    fileExcel: 'file-excel',
    fileExport: 'file-export',
    fileImage: 'file-image',
    fileImport: 'file-import',
    fileInvoice: 'file-invoice',
    fileInvoiceDollar: 'file-invoice-dollar',
    fileMedical: 'file-medical',
    fileMedicalAlt: 'file-medical-alt',
    filePdf: 'file-pdf',
    filePowerpoint: 'file-powerpoint',
    filePrescription: 'file-prescription',
    fileSignature: 'file-signature',
    fileUpload: 'file-upload',
    fileVideo: 'file-video',
    fileWord: 'file-word',
    fill: 'fill',
    fillDrip: 'fill-drip',
    film: 'film',
    filter: 'filter',
    fingerprint: 'fingerprint',
    fire: 'fire',
    fireAlt: 'fire-alt',
    fireExtinguisher: 'fire-extinguisher',
    firstAid: 'first-aid',
    fish: 'fish',
    fistRaised: 'fist-raised',
    flag: 'flag',
    flagCheckered: 'flag-checkered',
    flagUsa: 'flag-usa',
    flask: 'flask',
    flushed: 'flushed',
    folder: 'folder',
    folderMinus: 'folder-minus',
    folderOpen: 'folder-open',
    folderPlus: 'folder-plus',
    font: 'font',
    footballBall: 'football-ball',
    forward: 'forward',
    frog: 'frog',
    frown: 'frown',
    frownOpen: 'frown-open',
    funnelDollar: 'funnel-dollar',
    futbol: 'futbol',
    gamepad: 'gamepad',
    gasPump: 'gas-pump',
    gavel: 'gavel',
    gem: 'gem',
    genderless: 'genderless',
    ghost: 'ghost',
    gift: 'gift',
    gifts: 'gifts',
    glassCheers: 'glass-cheers',
    glassMartini: 'glass-martini',
    glassMartiniAlt: 'glass-martini-alt',
    glassWhiskey: 'glass-whiskey',
    glasses: 'glasses',
    globe: 'globe',
    globeAfrica: 'globe-africa',
    globeAmericas: 'globe-americas',
    globeAsia: 'globe-asia',
    globeEurope: 'globe-europe',
    golfBall: 'golf-ball',
    gopuram: 'gopuram',
    graduationCap: 'graduation-cap',
    greaterThan: 'greater-than',
    greaterThanEqual: 'greater-than-equal',
    grimace: 'grimace',
    grin: 'grin',
    grinAlt: 'grin-alt',
    grinBeam: 'grin-beam',
    grinBeamSweat: 'grin-beam-sweat',
    grinHearts: 'grin-hearts',
    grinSquint: 'grin-squint',
    grinSquintTears: 'grin-squint-tears',
    grinStars: 'grin-stars',
    grinTears: 'grin-tears',
    grinTongue: 'grin-tongue',
    grinTongueSquint: 'grin-tongue-squint',
    grinTongueWink: 'grin-tongue-wink',
    grinWink: 'grin-wink',
    gripHorizontal: 'grip-horizontal',
    gripLines: 'grip-lines',
    gripLinesVertical: 'grip-lines-vertical',
    gripVertical: 'grip-vertical',
    guitar: 'guitar',
    hSquare: 'h-square',
    hamburger: 'hamburger',
    hammer: 'hammer',
    hamsa: 'hamsa',
    handHolding: 'hand-holding',
    handHoldingHeart: 'hand-holding-heart',
    handHoldingMedical: 'hand-holding-medical',
    handHoldingUsd: 'hand-holding-usd',
    handHoldingWater: 'hand-holding-water',
    handLizard: 'hand-lizard',
    handMiddleFinger: 'hand-middle-finger',
    handPaper: 'hand-paper',
    handPeace: 'hand-peace',
    handPointDown: 'hand-point-down',
    handPointLeft: 'hand-point-left',
    handPointRight: 'hand-point-right',
    handPointUp: 'hand-point-up',
    handPointer: 'hand-pointer',
    handRock: 'hand-rock',
    handScissors: 'hand-scissors',
    handSparkles: 'hand-sparkles',
    handSpock: 'hand-spock',
    hands: 'hands',
    handsHelping: 'hands-helping',
    handsWash: 'hands-wash',
    handshake: 'handshake',
    handshakeAltSlash: 'handshake-alt-slash',
    handshakeSlash: 'handshake-slash',
    hanukiah: 'hanukiah',
    hardHat: 'hard-hat',
    hashtag: 'hashtag',
    hatCowboy: 'hat-cowboy',
    hatCowboySide: 'hat-cowboy-side',
    hatWizard: 'hat-wizard',
    hdd: 'hdd',
    headSideCough: 'head-side-cough',
    headSideCoughSlash: 'head-side-cough-slash',
    headSideMask: 'head-side-mask',
    headSideVirus: 'head-side-virus',
    heading: 'heading',
    headphones: 'headphones',
    headphonesAlt: 'headphones-alt',
    headset: 'headset',
    heart: 'heart',
    heartBroken: 'heart-broken',
    heartbeat: 'heartbeat',
    helicopter: 'helicopter',
    highlighter: 'highlighter',
    hiking: 'hiking',
    hippo: 'hippo',
    history: 'history',
    hockeyPuck: 'hockey-puck',
    hollyBerry: 'holly-berry',
    home: 'home',
    horse: 'horse',
    horseHead: 'horse-head',
    hospital: 'hospital',
    hospitalAlt: 'hospital-alt',
    hospitalSymbol: 'hospital-symbol',
    hospitalUser: 'hospital-user',
    hotTub: 'hot-tub',
    hotdog: 'hotdog',
    hotel: 'hotel',
    hourglass: 'hourglass',
    hourglassEnd: 'hourglass-end',
    hourglassHalf: 'hourglass-half',
    hourglassStart: 'hourglass-start',
    houseDamage: 'house-damage',
    houseUser: 'house-user',
    hryvnia: 'hryvnia',
    iCursor: 'i-cursor',
    iceCream: 'ice-cream',
    icicles: 'icicles',
    icons: 'icons',
    idBadge: 'id-badge',
    idCard: 'id-card',
    idCardAlt: 'id-card-alt',
    igloo: 'igloo',
    image: 'image',
    images: 'images',
    inbox: 'inbox',
    indent: 'indent',
    industry: 'industry',
    infinity: 'infinity',
    info: 'info',
    infoCircle: 'info-circle',
    italic: 'italic',
    jedi: 'jedi',
    joint: 'joint',
    journalWhills: 'journal-whills',
    kaaba: 'kaaba',
    key: 'key',
    keyboard: 'keyboard',
    khanda: 'khanda',
    kiss: 'kiss',
    kissBeam: 'kiss-beam',
    kissWinkHeart: 'kiss-wink-heart',
    kiwiBird: 'kiwi-bird',
    landmark: 'landmark',
    language: 'language',
    laptop: 'laptop',
    laptopCode: 'laptop-code',
    laptopHouse: 'laptop-house',
    laptopMedical: 'laptop-medical',
    laugh: 'laugh',
    laughBeam: 'laugh-beam',
    laughSquint: 'laugh-squint',
    laughWink: 'laugh-wink',
    layerGroup: 'layer-group',
    leaf: 'leaf',
    lemon: 'lemon',
    lessThan: 'less-than',
    lessThanEqual: 'less-than-equal',
    levelDownAlt: 'level-down-alt',
    levelUpAlt: 'level-up-alt',
    lifeRing: 'life-ring',
    lightbulb: 'lightbulb',
    link: 'link',
    liraSign: 'lira-sign',
    list: 'list',
    listAlt: 'list-alt',
    listOl: 'list-ol',
    listUl: 'list-ul',
    locationArrow: 'location-arrow',
    lock: 'lock',
    lockOpen: 'lock-open',
    longArrowAltDown: 'long-arrow-alt-down',
    longArrowAltLeft: 'long-arrow-alt-left',
    longArrowAltRight: 'long-arrow-alt-right',
    longArrowAltUp: 'long-arrow-alt-up',
    lowVision: 'low-vision',
    luggageCart: 'luggage-cart',
    lungs: 'lungs',
    lungsVirus: 'lungs-virus',
    magic: 'magic',
    magnet: 'magnet',
    mailBulk: 'mail-bulk',
    male: 'male',
    map: 'map',
    mapMarked: 'map-marked',
    mapMarkedAlt: 'map-marked-alt',
    mapMarker: 'map-marker',
    mapMarkerAlt: 'map-marker-alt',
    mapPin: 'map-pin',
    mapSigns: 'map-signs',
    marker: 'marker',
    mars: 'mars',
    marsDouble: 'mars-double',
    marsStroke: 'mars-stroke',
    marsStrokeH: 'mars-stroke-h',
    marsStrokeV: 'mars-stroke-v',
    mask: 'mask',
    medal: 'medal',
    medkit: 'medkit',
    meh: 'meh',
    mehBlank: 'meh-blank',
    mehRollingEyes: 'meh-rolling-eyes',
    memory: 'memory',
    menorah: 'menorah',
    mercury: 'mercury',
    meteor: 'meteor',
    microchip: 'microchip',
    microphone: 'microphone',
    microphoneAlt: 'microphone-alt',
    microphoneAltSlash: 'microphone-alt-slash',
    microphoneSlash: 'microphone-slash',
    microscope: 'microscope',
    minus: 'minus',
    minusCircle: 'minus-circle',
    minusSquare: 'minus-square',
    mitten: 'mitten',
    mobile: 'mobile',
    mobileAlt: 'mobile-alt',
    moneyBill: 'money-bill',
    moneyBillAlt: 'money-bill-alt',
    moneyBillWave: 'money-bill-wave',
    moneyBillWaveAlt: 'money-bill-wave-alt',
    moneyCheck: 'money-check',
    moneyCheckAlt: 'money-check-alt',
    monument: 'monument',
    moon: 'moon',
    mortarPestle: 'mortar-pestle',
    mosque: 'mosque',
    motorcycle: 'motorcycle',
    mountain: 'mountain',
    mouse: 'mouse',
    mousePointer: 'mouse-pointer',
    mugHot: 'mug-hot',
    music: 'music',
    networkWired: 'network-wired',
    neuter: 'neuter',
    newspaper: 'newspaper',
    notEqual: 'not-equal',
    notesMedical: 'notes-medical',
    objectGroup: 'object-group',
    objectUngroup: 'object-ungroup',
    oilCan: 'oil-can',
    om: 'om',
    otter: 'otter',
    outdent: 'outdent',
    pager: 'pager',
    paintBrush: 'paint-brush',
    paintRoller: 'paint-roller',
    palette: 'palette',
    pallet: 'pallet',
    paperPlane: 'paper-plane',
    paperclip: 'paperclip',
    parachuteBox: 'parachute-box',
    paragraph: 'paragraph',
    parking: 'parking',
    passport: 'passport',
    pastafarianism: 'pastafarianism',
    paste: 'paste',
    pause: 'pause',
    pauseCircle: 'pause-circle',
    paw: 'paw',
    peace: 'peace',
    pen: 'pen',
    penAlt: 'pen-alt',
    penFancy: 'pen-fancy',
    penNib: 'pen-nib',
    penSquare: 'pen-square',
    pencilAlt: 'pencil-alt',
    pencilRuler: 'pencil-ruler',
    peopleArrows: 'people-arrows',
    peopleCarry: 'people-carry',
    pepperHot: 'pepper-hot',
    percent: 'percent',
    percentage: 'percentage',
    personBooth: 'person-booth',
    phone: 'phone',
    phoneAlt: 'phone-alt',
    phoneSlash: 'phone-slash',
    phoneSquare: 'phone-square',
    phoneSquareAlt: 'phone-square-alt',
    phoneVolume: 'phone-volume',
    photoVideo: 'photo-video',
    piggyBank: 'piggy-bank',
    pills: 'pills',
    pizzaSlice: 'pizza-slice',
    placeOfWorship: 'place-of-worship',
    plane: 'plane',
    planeArrival: 'plane-arrival',
    planeDeparture: 'plane-departure',
    planeSlash: 'plane-slash',
    play: 'play',
    playCircle: 'play-circle',
    plug: 'plug',
    plus: 'plus',
    plusCircle: 'plus-circle',
    plusSquare: 'plus-square',
    podcast: 'podcast',
    poll: 'poll',
    pollH: 'poll-h',
    poo: 'poo',
    pooStorm: 'poo-storm',
    poop: 'poop',
    portrait: 'portrait',
    poundSign: 'pound-sign',
    powerOff: 'power-off',
    pray: 'pray',
    prayingHands: 'praying-hands',
    prescription: 'prescription',
    prescriptionBottle: 'prescription-bottle',
    prescriptionBottleAlt: 'prescription-bottle-alt',
    print: 'print',
    procedures: 'procedures',
    projectDiagram: 'project-diagram',
    pumpMedical: 'pump-medical',
    pumpSoap: 'pump-soap',
    puzzlePiece: 'puzzle-piece',
    qrcode: 'qrcode',
    question: 'question',
    questionCircle: 'question-circle',
    quidditch: 'quidditch',
    quoteLeft: 'quote-left',
    quoteRight: 'quote-right',
    quran: 'quran',
    radiation: 'radiation',
    radiationAlt: 'radiation-alt',
    rainbow: 'rainbow',
    random: 'random',
    receipt: 'receipt',
    recordVinyl: 'record-vinyl',
    recycle: 'recycle',
    redo: 'redo',
    redoAlt: 'redo-alt',
    registered: 'registered',
    removeFormat: 'remove-format',
    reply: 'reply',
    replyAll: 'reply-all',
    republican: 'republican',
    restroom: 'restroom',
    retweet: 'retweet',
    ribbon: 'ribbon',
    ring: 'ring',
    road: 'road',
    robot: 'robot',
    rocket: 'rocket',
    route: 'route',
    rss: 'rss',
    rssSquare: 'rss-square',
    rubleSign: 'ruble-sign',
    ruler: 'ruler',
    rulerCombined: 'ruler-combined',
    rulerHorizontal: 'ruler-horizontal',
    rulerVertical: 'ruler-vertical',
    running: 'running',
    rupeeSign: 'rupee-sign',
    sadCry: 'sad-cry',
    sadTear: 'sad-tear',
    satellite: 'satellite',
    satelliteDish: 'satellite-dish',
    save: 'save',
    school: 'school',
    screwdriver: 'screwdriver',
    scroll: 'scroll',
    sdCard: 'sd-card',
    search: 'search',
    searchDollar: 'search-dollar',
    searchLocation: 'search-location',
    searchMinus: 'search-minus',
    searchPlus: 'search-plus',
    seedling: 'seedling',
    server: 'server',
    shapes: 'shapes',
    share: 'share',
    shareAlt: 'share-alt',
    shareAltSquare: 'share-alt-square',
    shareSquare: 'share-square',
    shekelSign: 'shekel-sign',
    shieldAlt: 'shield-alt',
    shieldVirus: 'shield-virus',
    ship: 'ship',
    shippingFast: 'shipping-fast',
    shoePrints: 'shoe-prints',
    shoppingBag: 'shopping-bag',
    shoppingBasket: 'shopping-basket',
    shoppingCart: 'shopping-cart',
    shower: 'shower',
    shuttleVan: 'shuttle-van',
    sign: 'sign',
    signInAlt: 'sign-in-alt',
    signLanguage: 'sign-language',
    signOutAlt: 'sign-out-alt',
    signal: 'signal',
    signature: 'signature',
    simCard: 'sim-card',
    sitemap: 'sitemap',
    skating: 'skating',
    skiing: 'skiing',
    skiingNordic: 'skiing-nordic',
    skull: 'skull',
    skullCrossbones: 'skull-crossbones',
    slash: 'slash',
    sleigh: 'sleigh',
    slidersH: 'sliders-h',
    smile: 'smile',
    smileBeam: 'smile-beam',
    smileWink: 'smile-wink',
    smog: 'smog',
    smoking: 'smoking',
    smokingBan: 'smoking-ban',
    sms: 'sms',
    snowboarding: 'snowboarding',
    snowflake: 'snowflake',
    snowman: 'snowman',
    snowplow: 'snowplow',
    soap: 'soap',
    socks: 'socks',
    solarPanel: 'solar-panel',
    sort: 'sort',
    sortAlphaDown: 'sort-alpha-down',
    sortAlphaDownAlt: 'sort-alpha-down-alt',
    sortAlphaUp: 'sort-alpha-up',
    sortAlphaUpAlt: 'sort-alpha-up-alt',
    sortAmountDown: 'sort-amount-down',
    sortAmountDownAlt: 'sort-amount-down-alt',
    sortAmountUp: 'sort-amount-up',
    sortAmountUpAlt: 'sort-amount-up-alt',
    sortDown: 'sort-down',
    sortNumericDown: 'sort-numeric-down',
    sortNumericDownAlt: 'sort-numeric-down-alt',
    sortNumericUp: 'sort-numeric-up',
    sortNumericUpAlt: 'sort-numeric-up-alt',
    sortUp: 'sort-up',
    spa: 'spa',
    spaceShuttle: 'space-shuttle',
    spellCheck: 'spell-check',
    spider: 'spider',
    spinner: 'spinner',
    splotch: 'splotch',
    sprayCan: 'spray-can',
    square: 'square',
    squareFull: 'square-full',
    squareRootAlt: 'square-root-alt',
    stamp: 'stamp',
    star: 'star',
    starAndCrescent: 'star-and-crescent',
    starHalf: 'star-half',
    starHalfAlt: 'star-half-alt',
    starOfDavid: 'star-of-david',
    starOfLife: 'star-of-life',
    stepBackward: 'step-backward',
    stepForward: 'step-forward',
    stethoscope: 'stethoscope',
    stickyNote: 'sticky-note',
    stop: 'stop',
    stopCircle: 'stop-circle',
    stopwatch: 'stopwatch',
    stopwatch20: 'stopwatch-20',
    store: 'store',
    storeAlt: 'store-alt',
    storeAltSlash: 'store-alt-slash',
    storeSlash: 'store-slash',
    stream: 'stream',
    streetView: 'street-view',
    strikethrough: 'strikethrough',
    stroopwafel: 'stroopwafel',
    subscript: 'subscript',
    subway: 'subway',
    suitcase: 'suitcase',
    suitcaseRolling: 'suitcase-rolling',
    sun: 'sun',
    superscript: 'superscript',
    surprise: 'surprise',
    swatchbook: 'swatchbook',
    swimmer: 'swimmer',
    swimmingPool: 'swimming-pool',
    synagogue: 'synagogue',
    sync: 'sync',
    syncAlt: 'sync-alt',
    syringe: 'syringe',
    table: 'table',
    tableTennis: 'table-tennis',
    tablet: 'tablet',
    tabletAlt: 'tablet-alt',
    tablets: 'tablets',
    tachometerAlt: 'tachometer-alt',
    tag: 'tag',
    tags: 'tags',
    tape: 'tape',
    tasks: 'tasks',
    taxi: 'taxi',
    teeth: 'teeth',
    teethOpen: 'teeth-open',
    temperatureHigh: 'temperature-high',
    temperatureLow: 'temperature-low',
    tenge: 'tenge',
    terminal: 'terminal',
    textHeight: 'text-height',
    textWidth: 'text-width',
    th: 'th',
    thLarge: 'th-large',
    thList: 'th-list',
    theaterMasks: 'theater-masks',
    thermometer: 'thermometer',
    thermometerEmpty: 'thermometer-empty',
    thermometerFull: 'thermometer-full',
    thermometerHalf: 'thermometer-half',
    thermometerQuarter: 'thermometer-quarter',
    thermometerThreeQuarters: 'thermometer-three-quarters',
    thumbsDown: 'thumbs-down',
    thumbsUp: 'thumbs-up',
    thumbtack: 'thumbtack',
    ticketAlt: 'ticket-alt',
    times: 'times',
    timesCircle: 'times-circle',
    tint: 'tint',
    tintSlash: 'tint-slash',
    tired: 'tired',
    toggleOff: 'toggle-off',
    toggleOn: 'toggle-on',
    toilet: 'toilet',
    toiletPaper: 'toilet-paper',
    toiletPaperSlash: 'toilet-paper-slash',
    toolbox: 'toolbox',
    tools: 'tools',
    tooth: 'tooth',
    torah: 'torah',
    toriiGate: 'torii-gate',
    tractor: 'tractor',
    trademark: 'trademark',
    trafficLight: 'traffic-light',
    trailer: 'trailer',
    train: 'train',
    tram: 'tram',
    transgender: 'transgender',
    transgenderAlt: 'transgender-alt',
    trash: 'trash',
    trashAlt: 'trash-alt',
    trashRestore: 'trash-restore',
    trashRestoreAlt: 'trash-restore-alt',
    tree: 'tree',
    trophy: 'trophy',
    truck: 'truck',
    truckLoading: 'truck-loading',
    truckMonster: 'truck-monster',
    truckMoving: 'truck-moving',
    truckPickup: 'truck-pickup',
    tshirt: 'tshirt',
    tty: 'tty',
    tv: 'tv',
    umbrella: 'umbrella',
    umbrellaBeach: 'umbrella-beach',
    underline: 'underline',
    undo: 'undo',
    undoAlt: 'undo-alt',
    universalAccess: 'universal-access',
    university: 'university',
    unlink: 'unlink',
    unlock: 'unlock',
    unlockAlt: 'unlock-alt',
    upload: 'upload',
    user: 'user',
    userAlt: 'user-alt',
    userAltSlash: 'user-alt-slash',
    userAstronaut: 'user-astronaut',
    userCheck: 'user-check',
    userCircle: 'user-circle',
    userClock: 'user-clock',
    userCog: 'user-cog',
    userEdit: 'user-edit',
    userFriends: 'user-friends',
    userGraduate: 'user-graduate',
    userInjured: 'user-injured',
    userLock: 'user-lock',
    userMd: 'user-md',
    userMinus: 'user-minus',
    userNinja: 'user-ninja',
    userNurse: 'user-nurse',
    userPlus: 'user-plus',
    userSecret: 'user-secret',
    userShield: 'user-shield',
    userSlash: 'user-slash',
    userTag: 'user-tag',
    userTie: 'user-tie',
    userTimes: 'user-times',
    users: 'users',
    usersCog: 'users-cog',
    utensilSpoon: 'utensil-spoon',
    utensils: 'utensils',
    vectorSquare: 'vector-square',
    venus: 'venus',
    venusDouble: 'venus-double',
    venusMars: 'venus-mars',
    vial: 'vial',
    vials: 'vials',
    video: 'video',
    videoSlash: 'video-slash',
    vihara: 'vihara',
    virus: 'virus',
    virusSlash: 'virus-slash',
    viruses: 'viruses',
    voicemail: 'voicemail',
    volleyballBall: 'volleyball-ball',
    volumeDown: 'volume-down',
    volumeMute: 'volume-mute',
    volumeOff: 'volume-off',
    volumeUp: 'volume-up',
    voteYea: 'vote-yea',
    vrCardboard: 'vr-cardboard',
    walking: 'walking',
    wallet: 'wallet',
    warehouse: 'warehouse',
    water: 'water',
    waveSquare: 'wave-square',
    weight: 'weight',
    weightHanging: 'weight-hanging',
    wheelchair: 'wheelchair',
    wifi: 'wifi',
    wind: 'wind',
    windowClose: 'window-close',
    windowMaximize: 'window-maximize',
    windowMinimize: 'window-minimize',
    windowRestore: 'window-restore',
    wineBottle: 'wine-bottle',
    wineGlass: 'wine-glass',
    wineGlassAlt: 'wine-glass-alt',
    wonSign: 'won-sign',
    wrench: 'wrench',
    xRay: 'x-ray',
    yenSign: 'yen-sign',
    yinYang: 'yin-yang'
  },
  far: {
    addressBook: 'address-book',
    addressCard: 'address-card',
    angry: 'angry',
    arrowAltCircleDown: 'arrow-alt-circle-down',
    arrowAltCircleLeft: 'arrow-alt-circle-left',
    arrowAltCircleRight: 'arrow-alt-circle-right',
    arrowAltCircleUp: 'arrow-alt-circle-up',
    bell: 'bell',
    bellSlash: 'bell-slash',
    bookmark: 'bookmark',
    building: 'building',
    calendar: 'calendar',
    calendarAlt: 'calendar-alt',
    calendarCheck: 'calendar-check',
    calendarMinus: 'calendar-minus',
    calendarPlus: 'calendar-plus',
    calendarTimes: 'calendar-times',
    caretSquareDown: 'caret-square-down',
    caretSquareLeft: 'caret-square-left',
    caretSquareRight: 'caret-square-right',
    caretSquareUp: 'caret-square-up',
    chartBar: 'chart-bar',
    checkCircle: 'check-circle',
    checkSquare: 'check-square',
    circle: 'circle',
    clipboard: 'clipboard',
    clock: 'clock',
    clone: 'clone',
    closedCaptioning: 'closed-captioning',
    comment: 'comment',
    commentAlt: 'comment-alt',
    commentDots: 'comment-dots',
    comments: 'comments',
    compass: 'compass',
    copy: 'copy',
    copyright: 'copyright',
    creditCard: 'credit-card',
    dizzy: 'dizzy',
    dotCircle: 'dot-circle',
    edit: 'edit',
    envelope: 'envelope',
    envelopeOpen: 'envelope-open',
    eye: 'eye',
    eyeSlash: 'eye-slash',
    file: 'file',
    fileAlt: 'file-alt',
    fileArchive: 'file-archive',
    fileAudio: 'file-audio',
    fileCode: 'file-code',
    fileExcel: 'file-excel',
    fileImage: 'file-image',
    filePdf: 'file-pdf',
    filePowerpoint: 'file-powerpoint',
    fileVideo: 'file-video',
    fileWord: 'file-word',
    flag: 'flag',
    flushed: 'flushed',
    folder: 'folder',
    folderOpen: 'folder-open',
    frown: 'frown',
    frownOpen: 'frown-open',
    futbol: 'futbol',
    gem: 'gem',
    grimace: 'grimace',
    grin: 'grin',
    grinAlt: 'grin-alt',
    grinBeam: 'grin-beam',
    grinBeamSweat: 'grin-beam-sweat',
    grinHearts: 'grin-hearts',
    grinSquint: 'grin-squint',
    grinSquintTears: 'grin-squint-tears',
    grinStars: 'grin-stars',
    grinTears: 'grin-tears',
    grinTongue: 'grin-tongue',
    grinTongueSquint: 'grin-tongue-squint',
    grinTongueWink: 'grin-tongue-wink',
    grinWink: 'grin-wink',
    handLizard: 'hand-lizard',
    handPaper: 'hand-paper',
    handPeace: 'hand-peace',
    handPointDown: 'hand-point-down',
    handPointLeft: 'hand-point-left',
    handPointRight: 'hand-point-right',
    handPointUp: 'hand-point-up',
    handPointer: 'hand-pointer',
    handRock: 'hand-rock',
    handScissors: 'hand-scissors',
    handSpock: 'hand-spock',
    handshake: 'handshake',
    hdd: 'hdd',
    heart: 'heart',
    hospital: 'hospital',
    hourglass: 'hourglass',
    idBadge: 'id-badge',
    idCard: 'id-card',
    image: 'image',
    images: 'images',
    keyboard: 'keyboard',
    kiss: 'kiss',
    kissBeam: 'kiss-beam',
    kissWinkHeart: 'kiss-wink-heart',
    laugh: 'laugh',
    laughBeam: 'laugh-beam',
    laughSquint: 'laugh-squint',
    laughWink: 'laugh-wink',
    lemon: 'lemon',
    lifeRing: 'life-ring',
    lightbulb: 'lightbulb',
    listAlt: 'list-alt',
    map: 'map',
    meh: 'meh',
    mehBlank: 'meh-blank',
    mehRollingEyes: 'meh-rolling-eyes',
    minusSquare: 'minus-square',
    moneyBillAlt: 'money-bill-alt',
    moon: 'moon',
    newspaper: 'newspaper',
    objectGroup: 'object-group',
    objectUngroup: 'object-ungroup',
    paperPlane: 'paper-plane',
    pauseCircle: 'pause-circle',
    playCircle: 'play-circle',
    plusSquare: 'plus-square',
    questionCircle: 'question-circle',
    registered: 'registered',
    sadCry: 'sad-cry',
    sadTear: 'sad-tear',
    save: 'save',
    shareSquare: 'share-square',
    smile: 'smile',
    smileBeam: 'smile-beam',
    smileWink: 'smile-wink',
    snowflake: 'snowflake',
    square: 'square',
    star: 'star',
    starHalf: 'star-half',
    stickyNote: 'sticky-note',
    stopCircle: 'stop-circle',
    sun: 'sun',
    surprise: 'surprise',
    thumbsDown: 'thumbs-down',
    thumbsUp: 'thumbs-up',
    timesCircle: 'times-circle',
    tired: 'tired',
    trashAlt: 'trash-alt',
    user: 'user',
    userCircle: 'user-circle',
    windowClose: 'window-close',
    windowMaximize: 'window-maximize',
    windowMinimize: 'window-minimize',
    windowRestore: 'window-restore'
  },
  fab: {
    '500px': '500px',
    accessibleIcon: 'accessible-icon',
    accusoft: 'accusoft',
    acquisitionsIncorporated: 'acquisitions-incorporated',
    adn: 'adn',
    adobe: 'adobe',
    adversal: 'adversal',
    affiliatetheme: 'affiliatetheme',
    airbnb: 'airbnb',
    algolia: 'algolia',
    alipay: 'alipay',
    amazon: 'amazon',
    amazonPay: 'amazon-pay',
    amilia: 'amilia',
    android: 'android',
    angellist: 'angellist',
    angrycreative: 'angrycreative',
    angular: 'angular',
    appStore: 'app-store',
    appStoreIos: 'app-store-ios',
    apper: 'apper',
    apple: 'apple',
    applePay: 'apple-pay',
    artstation: 'artstation',
    asymmetrik: 'asymmetrik',
    atlassian: 'atlassian',
    audible: 'audible',
    autoprefixer: 'autoprefixer',
    avianex: 'avianex',
    aviato: 'aviato',
    aws: 'aws',
    bandcamp: 'bandcamp',
    battleNet: 'battle-net',
    behance: 'behance',
    behanceSquare: 'behance-square',
    bimobject: 'bimobject',
    bitbucket: 'bitbucket',
    bitcoin: 'bitcoin',
    bity: 'bity',
    blackTie: 'black-tie',
    blackberry: 'blackberry',
    blogger: 'blogger',
    bloggerB: 'blogger-b',
    bluetooth: 'bluetooth',
    bluetoothB: 'bluetooth-b',
    bootstrap: 'bootstrap',
    btc: 'btc',
    buffer: 'buffer',
    buromobelexperte: 'buromobelexperte',
    buyNLarge: 'buy-n-large',
    canadianMapleLeaf: 'canadian-maple-leaf',
    ccAmazonPay: 'cc-amazon-pay',
    ccAmex: 'cc-amex',
    ccApplePay: 'cc-apple-pay',
    ccDinersClub: 'cc-diners-club',
    ccDiscover: 'cc-discover',
    ccJcb: 'cc-jcb',
    ccMastercard: 'cc-mastercard',
    ccPaypal: 'cc-paypal',
    ccStripe: 'cc-stripe',
    ccVisa: 'cc-visa',
    centercode: 'centercode',
    centos: 'centos',
    chrome: 'chrome',
    chromecast: 'chromecast',
    cloudscale: 'cloudscale',
    cloudsmith: 'cloudsmith',
    cloudversify: 'cloudversify',
    codepen: 'codepen',
    codiepie: 'codiepie',
    confluence: 'confluence',
    connectdevelop: 'connectdevelop',
    contao: 'contao',
    cottonBureau: 'cotton-bureau',
    cpanel: 'cpanel',
    creativeCommons: 'creative-commons',
    creativeCommonsBy: 'creative-commons-by',
    creativeCommonsNc: 'creative-commons-nc',
    creativeCommonsNcEu: 'creative-commons-nc-eu',
    creativeCommonsNcJp: 'creative-commons-nc-jp',
    creativeCommonsNd: 'creative-commons-nd',
    creativeCommonsPd: 'creative-commons-pd',
    creativeCommonsPdAlt: 'creative-commons-pd-alt',
    creativeCommonsRemix: 'creative-commons-remix',
    creativeCommonsSa: 'creative-commons-sa',
    creativeCommonsSampling: 'creative-commons-sampling',
    creativeCommonsSamplingPlus: 'creative-commons-sampling-plus',
    creativeCommonsShare: 'creative-commons-share',
    creativeCommonsZero: 'creative-commons-zero',
    criticalRole: 'critical-role',
    css3: 'css3',
    css3Alt: 'css3-alt',
    cuttlefish: 'cuttlefish',
    dAndD: 'd-and-d',
    dAndDBeyond: 'd-and-d-beyond',
    dailymotion: 'dailymotion',
    dashcube: 'dashcube',
    delicious: 'delicious',
    deploydog: 'deploydog',
    deskpro: 'deskpro',
    dev: 'dev',
    deviantart: 'deviantart',
    dhl: 'dhl',
    diaspora: 'diaspora',
    digg: 'digg',
    digitalOcean: 'digital-ocean',
    discord: 'discord',
    discourse: 'discourse',
    dochub: 'dochub',
    docker: 'docker',
    draft2digital: 'draft2digital',
    dribbble: 'dribbble',
    dribbbleSquare: 'dribbble-square',
    dropbox: 'dropbox',
    drupal: 'drupal',
    dyalog: 'dyalog',
    earlybirds: 'earlybirds',
    ebay: 'ebay',
    edge: 'edge',
    elementor: 'elementor',
    ello: 'ello',
    ember: 'ember',
    empire: 'empire',
    envira: 'envira',
    erlang: 'erlang',
    ethereum: 'ethereum',
    etsy: 'etsy',
    evernote: 'evernote',
    expeditedssl: 'expeditedssl',
    facebook: 'facebook',
    facebookF: 'facebook-f',
    facebookMessenger: 'facebook-messenger',
    facebookSquare: 'facebook-square',
    fantasyFlightGames: 'fantasy-flight-games',
    fedex: 'fedex',
    fedora: 'fedora',
    figma: 'figma',
    firefox: 'firefox',
    firefoxBrowser: 'firefox-browser',
    firstOrder: 'first-order',
    firstOrderAlt: 'first-order-alt',
    firstdraft: 'firstdraft',
    flickr: 'flickr',
    flipboard: 'flipboard',
    fly: 'fly',
    fontAwesome: 'font-awesome',
    fontAwesomeAlt: 'font-awesome-alt',
    fontAwesomeFlag: 'font-awesome-flag',
    fonticons: 'fonticons',
    fonticonsFi: 'fonticons-fi',
    fortAwesome: 'fort-awesome',
    fortAwesomeAlt: 'fort-awesome-alt',
    forumbee: 'forumbee',
    foursquare: 'foursquare',
    freeCodeCamp: 'free-code-camp',
    freebsd: 'freebsd',
    fulcrum: 'fulcrum',
    galacticRepublic: 'galactic-republic',
    galacticSenate: 'galactic-senate',
    getPocket: 'get-pocket',
    gg: 'gg',
    ggCircle: 'gg-circle',
    git: 'git',
    gitAlt: 'git-alt',
    gitSquare: 'git-square',
    github: 'github',
    githubAlt: 'github-alt',
    githubSquare: 'github-square',
    gitkraken: 'gitkraken',
    gitlab: 'gitlab',
    gitter: 'gitter',
    glide: 'glide',
    glideG: 'glide-g',
    gofore: 'gofore',
    goodreads: 'goodreads',
    goodreadsG: 'goodreads-g',
    google: 'google',
    googleDrive: 'google-drive',
    googlePlay: 'google-play',
    googlePlus: 'google-plus',
    googlePlusG: 'google-plus-g',
    googlePlusSquare: 'google-plus-square',
    googleWallet: 'google-wallet',
    gratipay: 'gratipay',
    grav: 'grav',
    gripfire: 'gripfire',
    grunt: 'grunt',
    gulp: 'gulp',
    hackerNews: 'hacker-news',
    hackerNewsSquare: 'hacker-news-square',
    hackerrank: 'hackerrank',
    hips: 'hips',
    hireAHelper: 'hire-a-helper',
    hooli: 'hooli',
    hornbill: 'hornbill',
    hotjar: 'hotjar',
    houzz: 'houzz',
    html5: 'html5',
    hubspot: 'hubspot',
    ideal: 'ideal',
    imdb: 'imdb',
    instagram: 'instagram',
    instagramSquare: 'instagram-square',
    intercom: 'intercom',
    internetExplorer: 'internet-explorer',
    invision: 'invision',
    ioxhost: 'ioxhost',
    itchIo: 'itch-io',
    itunes: 'itunes',
    itunesNote: 'itunes-note',
    java: 'java',
    jediOrder: 'jedi-order',
    jenkins: 'jenkins',
    jira: 'jira',
    joget: 'joget',
    joomla: 'joomla',
    js: 'js',
    jsSquare: 'js-square',
    jsfiddle: 'jsfiddle',
    kaggle: 'kaggle',
    keybase: 'keybase',
    keycdn: 'keycdn',
    kickstarter: 'kickstarter',
    kickstarterK: 'kickstarter-k',
    korvue: 'korvue',
    laravel: 'laravel',
    lastfm: 'lastfm',
    lastfmSquare: 'lastfm-square',
    leanpub: 'leanpub',
    less: 'less',
    line: 'line',
    linkedin: 'linkedin',
    linkedinIn: 'linkedin-in',
    linode: 'linode',
    linux: 'linux',
    lyft: 'lyft',
    magento: 'magento',
    mailchimp: 'mailchimp',
    mandalorian: 'mandalorian',
    markdown: 'markdown',
    mastodon: 'mastodon',
    maxcdn: 'maxcdn',
    mdb: 'mdb',
    medapps: 'medapps',
    medium: 'medium',
    mediumM: 'medium-m',
    medrt: 'medrt',
    meetup: 'meetup',
    megaport: 'megaport',
    mendeley: 'mendeley',
    microblog: 'microblog',
    microsoft: 'microsoft',
    mix: 'mix',
    mixcloud: 'mixcloud',
    mixer: 'mixer',
    mizuni: 'mizuni',
    modx: 'modx',
    monero: 'monero',
    napster: 'napster',
    neos: 'neos',
    nimblr: 'nimblr',
    node: 'node',
    nodeJs: 'node-js',
    npm: 'npm',
    ns8: 'ns8',
    nutritionix: 'nutritionix',
    odnoklassniki: 'odnoklassniki',
    odnoklassnikiSquare: 'odnoklassniki-square',
    oldRepublic: 'old-republic',
    opencart: 'opencart',
    openid: 'openid',
    opera: 'opera',
    optinMonster: 'optin-monster',
    orcid: 'orcid',
    osi: 'osi',
    page4: 'page4',
    pagelines: 'pagelines',
    palfed: 'palfed',
    patreon: 'patreon',
    paypal: 'paypal',
    pennyArcade: 'penny-arcade',
    periscope: 'periscope',
    phabricator: 'phabricator',
    phoenixFramework: 'phoenix-framework',
    phoenixSquadron: 'phoenix-squadron',
    php: 'php',
    piedPiper: 'pied-piper',
    piedPiperAlt: 'pied-piper-alt',
    piedPiperHat: 'pied-piper-hat',
    piedPiperPp: 'pied-piper-pp',
    piedPiperSquare: 'pied-piper-square',
    pinterest: 'pinterest',
    pinterestP: 'pinterest-p',
    pinterestSquare: 'pinterest-square',
    playstation: 'playstation',
    productHunt: 'product-hunt',
    pushed: 'pushed',
    python: 'python',
    qq: 'qq',
    quinscape: 'quinscape',
    quora: 'quora',
    rProject: 'r-project',
    raspberryPi: 'raspberry-pi',
    ravelry: 'ravelry',
    react: 'react',
    reacteurope: 'reacteurope',
    readme: 'readme',
    rebel: 'rebel',
    redRiver: 'red-river',
    reddit: 'reddit',
    redditAlien: 'reddit-alien',
    redditSquare: 'reddit-square',
    redhat: 'redhat',
    renren: 'renren',
    replyd: 'replyd',
    researchgate: 'researchgate',
    resolving: 'resolving',
    rev: 'rev',
    rocketchat: 'rocketchat',
    rockrms: 'rockrms',
    safari: 'safari',
    salesforce: 'salesforce',
    sass: 'sass',
    schlix: 'schlix',
    scribd: 'scribd',
    searchengin: 'searchengin',
    sellcast: 'sellcast',
    sellsy: 'sellsy',
    servicestack: 'servicestack',
    shirtsinbulk: 'shirtsinbulk',
    shopify: 'shopify',
    shopware: 'shopware',
    simplybuilt: 'simplybuilt',
    sistrix: 'sistrix',
    sith: 'sith',
    sketch: 'sketch',
    skyatlas: 'skyatlas',
    skype: 'skype',
    slack: 'slack',
    slackHash: 'slack-hash',
    slideshare: 'slideshare',
    snapchat: 'snapchat',
    snapchatGhost: 'snapchat-ghost',
    snapchatSquare: 'snapchat-square',
    soundcloud: 'soundcloud',
    sourcetree: 'sourcetree',
    speakap: 'speakap',
    speakerDeck: 'speaker-deck',
    spotify: 'spotify',
    squarespace: 'squarespace',
    stackExchange: 'stack-exchange',
    stackOverflow: 'stack-overflow',
    stackpath: 'stackpath',
    staylinked: 'staylinked',
    steam: 'steam',
    steamSquare: 'steam-square',
    steamSymbol: 'steam-symbol',
    stickerMule: 'sticker-mule',
    strava: 'strava',
    stripe: 'stripe',
    stripeS: 'stripe-s',
    studiovinari: 'studiovinari',
    stumbleupon: 'stumbleupon',
    stumbleuponCircle: 'stumbleupon-circle',
    superpowers: 'superpowers',
    supple: 'supple',
    suse: 'suse',
    swift: 'swift',
    symfony: 'symfony',
    teamspeak: 'teamspeak',
    telegram: 'telegram',
    telegramPlane: 'telegram-plane',
    tencentWeibo: 'tencent-weibo',
    theRedYeti: 'the-red-yeti',
    themeco: 'themeco',
    themeisle: 'themeisle',
    thinkPeaks: 'think-peaks',
    tradeFederation: 'trade-federation',
    trello: 'trello',
    tripadvisor: 'tripadvisor',
    tumblr: 'tumblr',
    tumblrSquare: 'tumblr-square',
    twitch: 'twitch',
    twitter: 'twitter',
    twitterSquare: 'twitter-square',
    typo3: 'typo3',
    uber: 'uber',
    ubuntu: 'ubuntu',
    uikit: 'uikit',
    umbraco: 'umbraco',
    uniregistry: 'uniregistry',
    unity: 'unity',
    untappd: 'untappd',
    ups: 'ups',
    usb: 'usb',
    usps: 'usps',
    ussunnah: 'ussunnah',
    vaadin: 'vaadin',
    viacoin: 'viacoin',
    viadeo: 'viadeo',
    viadeoSquare: 'viadeo-square',
    viber: 'viber',
    vimeo: 'vimeo',
    vimeoSquare: 'vimeo-square',
    vimeoV: 'vimeo-v',
    vine: 'vine',
    vk: 'vk',
    vnv: 'vnv',
    vuejs: 'vuejs',
    waze: 'waze',
    weebly: 'weebly',
    weibo: 'weibo',
    weixin: 'weixin',
    whatsapp: 'whatsapp',
    whatsappSquare: 'whatsapp-square',
    whmcs: 'whmcs',
    wikipediaW: 'wikipedia-w',
    windows: 'windows',
    wix: 'wix',
    wizardsOfTheCoast: 'wizards-of-the-coast',
    wolfPackBattalion: 'wolf-pack-battalion',
    wordpress: 'wordpress',
    wordpressSimple: 'wordpress-simple',
    wpbeginner: 'wpbeginner',
    wpexplorer: 'wpexplorer',
    wpforms: 'wpforms',
    wpressr: 'wpressr',
    xbox: 'xbox',
    xing: 'xing',
    xingSquare: 'xing-square',
    yCombinator: 'y-combinator',
    yahoo: 'yahoo',
    yammer: 'yammer',
    yandex: 'yandex',
    yandexInternational: 'yandex-international',
    yarn: 'yarn',
    yelp: 'yelp',
    yoast: 'yoast',
    youtube: 'youtube',
    youtubeSquare: 'youtube-square',
    zhihu: 'zhihu'
  }
};

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: icons, types, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _iconlist__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iconlist */ "./node_modules/@concretecms/bedrock/assets/cms/components/iconlist.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "icons", function() { return _iconlist__WEBPACK_IMPORTED_MODULE_1__["icons"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"]; });

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

 // Reexport the icons and types to make them easy to get at

 // Export our component definition

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    spritePath: {
      type: String,
      "default": vue__WEBPACK_IMPORTED_MODULE_0___default.a.config.spritePath || '/concrete/images/icons/bedrock/sprites.svg'
    },
    icon: {
      type: String,
      required: true
    },
    type: {
      type: String,
      "default": _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"].fas,
      validator: function validator(type) {
        return _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"][type] === type;
      }
    },
    color: {
      type: String,
      "default": 'currentColor'
    },
    iconTypes: {
      "default": function _default() {
        return _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"];
      }
    },
    iconList: {
      "default": function _default() {
        return _iconlist__WEBPACK_IMPORTED_MODULE_1__["icons"];
      }
    }
  },
  methods: {
    /**
     * Filters for checking types, these have to be methods because you can't use piped filters with v-if
     */
    isFontAwesome: function isFontAwesome(type) {
      return [_iconlist__WEBPACK_IMPORTED_MODULE_1__["types"].fas, _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"].far, _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"].fab].indexOf(type) >= 0;
    },
    isSvg: function isSvg(type) {
      return type === _iconlist__WEBPACK_IMPORTED_MODULE_1__["types"].svg;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************/
/*! exports provided: types, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "types", function() { return types; });
/* harmony import */ var _Icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Icon */ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue");
var _classMap;

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

var types = {
  add: 'add',
  save: 'save',
  "delete": 'delete',
  cancel: 'cancel',
  outline: 'outline',
  floating: 'floating'
};
/* harmony default export */ __webpack_exports__["default"] = ({
  classMap: (_classMap = {}, _defineProperty(_classMap, types.add, 'btn-success'), _defineProperty(_classMap, types.save, 'btn-primary'), _defineProperty(_classMap, types["delete"], 'btn-danger'), _defineProperty(_classMap, types.cancel, 'btn-outline-secondary'), _defineProperty(_classMap, types.outline, 'btn-outline-secondary'), _defineProperty(_classMap, types.floating, 'btn-outline'), _classMap),
  defaultClass: 'btn-outline-primary',
  props: {
    type: {
      type: String,
      "default": types.add
    },
    disabled: {
      type: Boolean,
      "default": false
    },
    labelPosition: {
      type: String,
      "default": 'right'
    },
    icon: {
      type: String,
      required: true
    },
    iconType: {
      type: String
    },
    iconColor: {
      type: String
    },
    buttonType: {
      type: String,
      "default": 'button'
    },
    buttonClass: [String, Array, Object]
  },
  components: {
    Icon: _Icon__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  methods: {
    showSlot: function showSlot(children) {
      if (children && children.length) {
        // Handle blank children
        if (children[0].tag === undefined && !children[0].text.trim()) {
          return false;
        }

        return true;
      }

      return false;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ImageCell__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ImageCell */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue");
/* harmony import */ var _ImageDetail__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ImageDetail */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  components: _objectSpread({
    ImageCell: _ImageCell__WEBPACK_IMPORTED_MODULE_0__["default"],
    ImageDetail: _ImageDetail__WEBPACK_IMPORTED_MODULE_1__["default"]
  }, _ImageCell__WEBPACK_IMPORTED_MODULE_0__["default"].components, {}, _ImageDetail__WEBPACK_IMPORTED_MODULE_1__["default"].components),
  data: function data() {
    return {
      activeTab: 'image',
      activeImage: null
    };
  },
  methods: {
    openTab: function openTab(tab) {
      this.activeTab = tab;
    },
    openImage: function openImage(image, index, event) {
      var _this = this;

      if (this.activeImage && this.activeImage === index) {
        this.closeImage();
      } else {
        this.activeImage = index;
        this.$nextTick(function () {
          var container = _this.$refs.imageContainer;
          container.scrollTop = _this.$refs.cell[index].offsetTop;
        });
      }
    },
    closeImage: function closeImage() {
      this.activeImage = null;
    },
    deleteImage: function deleteImage(index) {
      if (this.activeImage === index) {
        this.closeImage();
      }

      this.gallery.splice(index, 1);
    },
    addImage: function addImage() {
      var me = this;
      ConcreteFileManager.launchDialog(function (data) {
        ConcreteFileManager.getFileDetails(data.fID, function (file) {
          file = file.files[0] || {};
          me.gallery.push({
            id: data.fID,
            title: file.title,
            description: file.description,
            src: file.url,
            attributes: [],
            imageUrl: file.url,
            thumbUrl: file.url,
            displayChoices: JSON.parse(JSON.stringify(me.choices)),
            fileSize: file.fileSize || '-',
            detailUrl: file.urlDetail
          });
          var lastIndex = me.gallery.length - 1;
          me.openImage(me.gallery[lastIndex], lastIndex);
        });
      });
    }
  },
  props: {
    gallery: {
      type: Array,
      required: true
    },
    choices: {
      type: Object,
      required: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Icon */ "./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    Icon: _Icon__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  props: {
    isActive: {
      type: Boolean,
      "default": false
    },
    src: {
      type: String,
      required: true
    },
    fileSize: {
      type: String,
      required: true
    },
    size: {
      type: String,
      required: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--32-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _IconButton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../IconButton */ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  components: _objectSpread({
    IconButton: _IconButton__WEBPACK_IMPORTED_MODULE_0__["default"]
  }, _IconButton__WEBPACK_IMPORTED_MODULE_0__["default"].components),
  props: {
    image: {
      type: Object,
      required: true
    }
  },
  methods: {
    goToDetails: function goToDetails(url) {
      window.open(url, '_blank');
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "button .label[data-v-2ba8a44f] {\n  margin: 0 10px;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".ccm-gallery-edit .comingsoon[data-v-a5d890aa] {\n  bottom: 0;\n  display: flex;\n  flex-direction: column;\n  justify-content: center;\n  left: 0;\n  pointer-events: none;\n  position: absolute;\n  right: 0;\n  text-align: center;\n  top: 100px;\n}\n.ccm-gallery-edit .nav .nav-tab[data-v-a5d890aa] {\n  display: flex;\n}\n.ccm-gallery-edit .nav .nav-item[data-v-a5d890aa] {\n  flex: 1;\n  text-align: center;\n}\n.ccm-gallery-edit .nav .nav-item .nav-link[data-v-a5d890aa] {\n  border-color: #f4f4f4;\n  cursor: pointer;\n}\n.ccm-gallery-edit .nav .nav-item .nav-link.active[data-v-a5d890aa] {\n  border-color: #4a90e2;\n}\n.ccm-gallery-edit .nav .nav-item .nav-link.active[data-v-a5d890aa]:hover {\n  border-color: #4a90e2;\n}\n.ccm-gallery-edit .nav .nav-item .nav-link[data-v-a5d890aa]:hover {\n  border-color: #ccc;\n}\n.ccm-gallery-edit .image-container[data-v-a5d890aa] {\n  align-items: center;\n  display: flex;\n  flex-wrap: wrap;\n  justify-content: flex-start;\n  overflow-y: auto;\n  position: relative;\n}\n.ccm-gallery-edit .image-container.active-image[data-v-a5d890aa] {\n  height: 200px;\n}\n.ccm-gallery-edit .image-container .ccm-image-cell-container[data-v-a5d890aa] {\n  min-width: 130px;\n  position: relative;\n  width: 20%;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".ccm-image-cell[data-v-ad918c68] {\n  cursor: pointer;\n  display: inline-flex;\n  flex-direction: column;\n  margin: 0.625rem;\n  position: relative;\n}\n.ccm-image-cell:hover .delete[data-v-ad918c68] {\n  opacity: 1;\n  transition: opacity 0.2s ease-out;\n}\n.ccm-image-cell p[data-v-ad918c68] {\n  font-size: 1rem;\n  letter-spacing: 0;\n  margin-top: 0.625rem;\n  text-transform: uppercase;\n}\n.ccm-image-cell img[data-v-ad918c68] {\n  padding: 0.5rem;\n}\n.ccm-image-cell .delete[data-v-ad918c68] {\n  background-color: #6a6f7b;\n  border: 0;\n  border-radius: 20px;\n  box-shadow: 2px 2px 4px 0 rgba(0, 0, 0, 0.2);\n  float: left;\n  height: 30px;\n  margin-right: -10px;\n  margin-top: -10px;\n  opacity: 0;\n  position: absolute;\n  right: 0;\n  width: 30px;\n  z-index: 90;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".ccm-gallery-image-details[data-v-ec81668a] {\n  border-top: 1px solid #979797;\n  display: flex;\n  padding-top: 20px;\n}\n.ccm-gallery-image-details .image-preview[data-v-ec81668a],\n.ccm-gallery-image-details .image-details[data-v-ec81668a] {\n  flex: 1;\n  padding: 10px;\n  width: 50%;\n}\n.ccm-gallery-image-details .image-preview .image-container[data-v-ec81668a] {\n  align-items: center;\n  display: flex;\n  height: 100%;\n  justify-content: center;\n}\n.ccm-gallery-image-details .image-preview img[data-v-ec81668a] {\n  height: auto;\n  margin-bottom: 10px;\n  max-height: 100%;\n  max-width: 100%;\n  width: auto;\n}\n.ccm-gallery-image-details .image-details section[data-v-ec81668a] {\n  clear: both;\n  margin-bottom: 10px;\n}\n.ccm-gallery-image-details .image-details section p[data-v-ec81668a] {\n  color: #005164;\n  margin: 15px 0 15px 15px;\n}\n.ccm-gallery-image-details .image-details section p.image-title[data-v-ec81668a] {\n  font-weight: bold;\n}", ""]);

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../css-loader!../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../postcss-loader/src??ref--28-2!../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--28-2!../../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--28-2!../../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--28-2!./node_modules/sass-loader/dist/cjs.js??ref--28-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--28-2!../../../../../../sass-loader/dist/cjs.js??ref--28-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=template&id=4f5a39bd&functional=true&":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=template&id=4f5a39bd&functional=true& ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function(_h, _vm) {
  var _c = _vm._c
  return _vm.$options.methods.isFontAwesome(_vm.props.type)
    ? _c("i", {
        staticClass: "icon",
        class: [
          {
            fas: _vm.props.type === _vm.props.iconTypes.fas,
            far: _vm.props.type === _vm.props.iconTypes.far,
            fab: _vm.props.type === _vm.props.iconTypes.fab
          },
          (_vm.props.icon || []).indexOf("fa-") === 0
            ? _vm.props.icon
            : "fa-" + _vm.props.icon
        ],
        style: { color: _vm.props.color }
      })
    : _vm.$options.methods.isSvg(_vm.props.type)
    ? _c(
        "svg",
        { attrs: { viewport: "0 0 20 20", width: "20px", height: "20px" } },
        [
          _c("use", {
            style: "fill: " + _vm.props.color,
            attrs: {
              "xlink:href": _vm.props.spritePath + "#icon-" + _vm.props.icon
            }
          })
        ]
      )
    : _c("span", [_vm._v("Invalid icon type.")])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=template&id=2ba8a44f&scoped=true&functional=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function(_h, _vm) {
  var _c = _vm._c
  return _c(
    "button",
    _vm._b(
      {
        staticClass: "btn",
        class: [
          _vm.$options.classMap[_vm.props.type] || _vm.$options.defaultClass,
          _vm.props.buttonClass
        ],
        attrs: { disabled: _vm.props.disabled },
        on: { click: _vm.listeners.click }
      },
      "button",
      { type: _vm.props.buttonType, style: _vm.props.style },
      false
    ),
    [
      _vm.props.labelPosition === "right"
        ? _c(
            "Icon",
            _vm._b(
              {},
              "Icon",
              {
                icon: _vm.props.icon,
                type: _vm.props.iconType,
                color: _vm.props.iconColor
              },
              false
            )
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.$options.methods.showSlot(_vm.children)
        ? _c("span", { staticClass: "label" }, [_vm._t("default")], 2)
        : _vm._e(),
      _vm._v(" "),
      _vm.props.labelPosition !== "right"
        ? _c(
            "Icon",
            _vm._b(
              {},
              "Icon",
              {
                icon: _vm.props.icon,
                type: _vm.props.iconType,
                color: _vm.props.iconColor
              },
              false
            )
          )
        : _vm._e()
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true&":
/*!******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=template&id=a5d890aa&scoped=true& ***!
  \******************************************************************************************************************************************************************************************************************************************************************/
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
  return _c("div", { staticClass: "ccm-gallery-edit" }, [
    _c("input", {
      attrs: { type: "hidden", name: "field_json" },
      domProps: { value: JSON.stringify(_vm.$props.gallery) }
    }),
    _vm._v(" "),
    _c(
      "ul",
      {
        staticClass: "nav nav-tabs",
        attrs: { id: "galleryBlock", role: "tablist" }
      },
      [
        _c("li", { staticClass: "nav-item" }, [
          _c(
            "a",
            {
              staticClass: "nav-link",
              class: _vm.activeTab === "image" ? "active" : "",
              on: {
                click: function($event) {
                  return _vm.openTab("image")
                }
              }
            },
            [_vm._v("\n                Images\n            ")]
          )
        ]),
        _vm._v(" "),
        _c("li", { staticClass: "nav-item" }, [
          _c(
            "a",
            {
              staticClass: "nav-link",
              class: _vm.activeTab === "settings" ? "active" : "",
              on: {
                click: function($event) {
                  return _vm.openTab("settings")
                }
              }
            },
            [_vm._v("\n                Settings\n            ")]
          )
        ])
      ]
    ),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "tab-content", attrs: { id: "galleryBlockContent" } },
      [
        _vm.activeTab === "image"
          ? _c("div", { attrs: { id: "galleryImages" } }, [
              _c(
                "div",
                { staticClass: "text-right mt-4" },
                [
                  _c(
                    "icon-button",
                    {
                      staticClass: "btn btn-secondary",
                      attrs: {
                        icon: "plus",
                        "icon-type": "fas",
                        type: "outline"
                      },
                      on: {
                        click: function($event) {
                          return _vm.addImage()
                        }
                      }
                    },
                    [
                      _vm._v(
                        "\n                    Add Images\n                "
                      )
                    ]
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                {
                  ref: "imageContainer",
                  staticClass: "image-container mt-4",
                  class: _vm.activeImage !== null ? "active-image" : ""
                },
                _vm._l(_vm.$props.gallery, function(image, index) {
                  return _c(
                    "div",
                    {
                      key: index,
                      ref: "cell",
                      refInFor: true,
                      staticClass: "ccm-image-cell-container"
                    },
                    [
                      _c("ImageCell", {
                        attrs: {
                          src: image.thumbUrl,
                          "file-size": image.fileSize,
                          size: "120",
                          isActive: _vm.activeImage === index ? true : false
                        },
                        on: {
                          click: function($event) {
                            return _vm.openImage(image, index, $event)
                          },
                          delete: function($event) {
                            return _vm.deleteImage(index)
                          }
                        }
                      })
                    ],
                    1
                  )
                }),
                0
              ),
              _vm._v(" "),
              _vm.activeImage !== null
                ? _c(
                    "div",
                    [
                      _c("ImageDetail", {
                        attrs: { image: this.gallery[_vm.activeImage] },
                        on: {
                          delete: function($event) {
                            return _vm.deleteImage(_vm.activeImage)
                          }
                        }
                      })
                    ],
                    1
                  )
                : _vm._e()
            ])
          : _vm._e(),
        _vm._v(" "),
        _vm.activeTab === "settings"
          ? _c("div", { attrs: { id: "gallerySettings" } }, [
              _c("h3", { staticClass: "comingsoon" }, [
                _vm._v(
                  "\n                No Options Currently Available.\n            "
                )
              ])
            ])
          : _vm._e()
      ]
    )
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true&":
/*!********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=template&id=ad918c68&scoped=true&functional=true& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function(_h, _vm) {
  var _c = _vm._c
  return _c("div", { staticClass: "ccm-image-cell-grid" }, [
    _c(
      "div",
      {
        staticClass: "ccm-image-cell text-center",
        class: { active: _vm.props.isActive }
      },
      [
        _c(
          "button",
          {
            staticClass: "delete",
            attrs: { type: "button" },
            on: { click: _vm.listeners.delete }
          },
          [
            _c("Icon", { attrs: { icon: "times", type: "fas", color: "#fff" } })
          ],
          1
        ),
        _vm._v(" "),
        _c("div", { on: { click: _vm.listeners.click } }, [
          _c("img", {
            style: {
              width: _vm.props.size + "px",
              height: _vm.props.size + "px"
            },
            attrs: { src: _vm.props.src }
          }),
          _vm._v(" "),
          _c("p", [_vm._v(_vm._s(_vm.props.fileSize))])
        ])
      ]
    )
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true&":
/*!******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=template&id=ec81668a&scoped=true& ***!
  \******************************************************************************************************************************************************************************************************************************************************************/
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
  return _c("div", { staticClass: "ccm-gallery-image-details" }, [
    _c(
      "div",
      { staticClass: "image-preview text-center" },
      [
        _c("div", { staticClass: "image-container" }, [
          _c("img", { attrs: { src: this.$props.image.imageUrl } })
        ]),
        _vm._v(" "),
        _c(
          "IconButton",
          {
            attrs: { icon: "trash-alt", "icon-type": "far", type: "outline" },
            on: {
              click: function($event) {
                return _vm.$emit("delete")
              }
            }
          },
          [_vm._v("\n          Remove from Gallery\n        ")]
        )
      ],
      1
    ),
    _vm._v(" "),
    _c("div", { staticClass: "image-details" }, [
      _c(
        "section",
        [
          _c("strong", [_vm._v("Custom Attributes")]),
          _vm._v(" "),
          _c("p", { staticClass: "image-title" }, [
            _vm._v(_vm._s(this.$props.image.title))
          ]),
          _vm._v(" "),
          _c("p", { staticClass: "image-description" }, [
            _vm._v(_vm._s(this.$props.image.description))
          ]),
          _vm._v(" "),
          _vm._l(_vm.image.attributes, function(ref, idx) {
            var key = ref[0]
            var value = ref[1]
            return _c("p", { key: idx, staticClass: "image-attribute" }, [
              _c("strong", [_vm._v(_vm._s(key) + ":")]),
              _vm._v(" " + _vm._s(value) + "\n            ")
            ])
          }),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "mb-4 text-right" },
            [
              _vm.$props.image.detailUrl
                ? _c(
                    "IconButton",
                    {
                      attrs: {
                        icon: "pencil-alt",
                        "icon-type": "fas",
                        type: "outline"
                      },
                      on: {
                        click: function($event) {
                          return _vm.goToDetails(_vm.$props.image.detailUrl)
                        }
                      }
                    },
                    [
                      _vm._v(
                        "\n                  Edit Attributes\n                "
                      )
                    ]
                  )
                : _vm._e()
            ],
            1
          )
        ],
        2
      ),
      _vm._v(" "),
      !this.$props.image.displayChoices.length
        ? _c(
            "section",
            [
              _vm._m(0),
              _vm._v(" "),
              _vm._l(this.$props.image.displayChoices, function(choice, index) {
                return _c("div", { key: index }, [
                  choice.type === "text"
                    ? _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: choice.value,
                            expression: "choice.value"
                          }
                        ],
                        staticClass: "form-control mb-3",
                        attrs: { placeholder: choice.title, name: index },
                        domProps: { value: choice.value },
                        on: {
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(choice, "value", $event.target.value)
                          }
                        }
                      })
                    : _vm._e(),
                  _vm._v(" "),
                  choice.type === "select"
                    ? _c(
                        "select",
                        {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: choice.value,
                              expression: "choice.value"
                            }
                          ],
                          staticClass: "form-control mb-3",
                          attrs: { name: index },
                          on: {
                            change: function($event) {
                              var $$selectedVal = Array.prototype.filter
                                .call($event.target.options, function(o) {
                                  return o.selected
                                })
                                .map(function(o) {
                                  var val = "_value" in o ? o._value : o.value
                                  return val
                                })
                              _vm.$set(
                                choice,
                                "value",
                                $event.target.multiple
                                  ? $$selectedVal
                                  : $$selectedVal[0]
                              )
                            }
                          }
                        },
                        [
                          _c(
                            "option",
                            {
                              attrs: { selected: "", disabled: "", value: "0" }
                            },
                            [_vm._v(_vm._s(choice.title))]
                          ),
                          _vm._v(" "),
                          _vm._l(choice.options, function(option, index) {
                            return _c(
                              "option",
                              { key: index, domProps: { value: index } },
                              [
                                _vm._v(
                                  "\n                        " +
                                    _vm._s(option) +
                                    "\n                    "
                                )
                              ]
                            )
                          })
                        ],
                        2
                      )
                    : _vm._e()
                ])
              })
            ],
            2
          )
        : _vm._e()
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "mb-2" }, [
      _c("strong", [_vm._v("Display Options")])
    ])
  }
]
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
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
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

/***/ 6:
/*!************************************************!*\
  !*** multi ./assets/blocks/gallery/gallery.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/fabianbitter/Projekte/concrete5/core/9.0.0/build/assets/blocks/gallery/gallery.js */"./assets/blocks/gallery/gallery.js");


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