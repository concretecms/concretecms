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
/******/ 	return __webpack_require__(__webpack_require__.s = 10);
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
/* harmony import */ var _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../babel-loader/lib??ref--34-0!../../../../../vue-loader/lib??vue-loader-options!./Icon.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "icons", function() { return _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["icons"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["types"]; });

 /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_Icon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

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
/* harmony import */ var _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../babel-loader/lib??ref--34-0!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "types", function() { return _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["types"]; });

 /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&":
/*!******************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& ***!
  \******************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../style-loader!../../../../../css-loader!../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../postcss-loader/src??ref--30-2!../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_IconButton_vue_vue_type_style_index_0_id_2ba8a44f_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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
/* harmony import */ var _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../babel-loader/lib??ref--34-0!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../style-loader!../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--30-2!../../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_GalleryEdit_vue_vue_type_style_index_0_id_a5d890aa_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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
/* harmony import */ var _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../babel-loader/lib??ref--34-0!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&":
/*!*************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& ***!
  \*************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../style-loader!../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--30-2!../../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageCell_vue_vue_type_style_index_0_id_ad918c68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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
/* harmony import */ var _babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../babel-loader/lib??ref--34-0!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_babel_loader_lib_index_js_ref_34_0_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& ***!
  \***************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../style-loader!../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--30-2!../../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&");
/* harmony import */ var _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_style_loader_index_js_css_loader_index_js_vue_loader_lib_loaders_stylePostLoader_js_postcss_loader_src_index_js_ref_30_2_sass_loader_dist_cjs_js_ref_30_3_vue_loader_lib_index_js_vue_loader_options_ImageDetail_vue_vue_type_style_index_0_id_ec81668a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "./node_modules/@concretecms/bedrock/node_modules/sortablejs/modular/sortable.esm.js":
/*!*******************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/sortablejs/modular/sortable.esm.js ***!
  \*******************************************************************************************/
/*! exports provided: default, MultiDrag, Sortable, Swap */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MultiDrag", function() { return MultiDragPlugin; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Sortable", function() { return Sortable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Swap", function() { return SwapPlugin; });
function _typeof2(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

/**!
 * Sortable 1.10.2
 * @author	RubaXa   <trash@rubaxa.org>
 * @author	owenm    <owen23355@gmail.com>
 * @license MIT
 */
function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

function _defineProperty(obj, key, value) {
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

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      _defineProperty(target, key, source[key]);
    });
  }

  return target;
}

function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};

  var target = _objectWithoutPropertiesLoose(source, excluded);

  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}

function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}

var version = "1.10.2";

function userAgent(pattern) {
  if (typeof window !== 'undefined' && window.navigator) {
    return !! /*@__PURE__*/navigator.userAgent.match(pattern);
  }
}

var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
var Edge = userAgent(/Edge/i);
var FireFox = userAgent(/firefox/i);
var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
var IOS = userAgent(/iP(ad|od|hone)/i);
var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);
var captureMode = {
  capture: false,
  passive: false
};

function on(el, event, fn) {
  el.addEventListener(event, fn, !IE11OrLess && captureMode);
}

function off(el, event, fn) {
  el.removeEventListener(event, fn, !IE11OrLess && captureMode);
}

function matches(
/**HTMLElement*/
el,
/**String*/
selector) {
  if (!selector) return;
  selector[0] === '>' && (selector = selector.substring(1));

  if (el) {
    try {
      if (el.matches) {
        return el.matches(selector);
      } else if (el.msMatchesSelector) {
        return el.msMatchesSelector(selector);
      } else if (el.webkitMatchesSelector) {
        return el.webkitMatchesSelector(selector);
      }
    } catch (_) {
      return false;
    }
  }

  return false;
}

function getParentOrHost(el) {
  return el.host && el !== document && el.host.nodeType ? el.host : el.parentNode;
}

function closest(
/**HTMLElement*/
el,
/**String*/
selector,
/**HTMLElement*/
ctx, includeCTX) {
  if (el) {
    ctx = ctx || document;

    do {
      if (selector != null && (selector[0] === '>' ? el.parentNode === ctx && matches(el, selector) : matches(el, selector)) || includeCTX && el === ctx) {
        return el;
      }

      if (el === ctx) break;
      /* jshint boss:true */
    } while (el = getParentOrHost(el));
  }

  return null;
}

var R_SPACE = /\s+/g;

function toggleClass(el, name, state) {
  if (el && name) {
    if (el.classList) {
      el.classList[state ? 'add' : 'remove'](name);
    } else {
      var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
      el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
    }
  }
}

function css(el, prop, val) {
  var style = el && el.style;

  if (style) {
    if (val === void 0) {
      if (document.defaultView && document.defaultView.getComputedStyle) {
        val = document.defaultView.getComputedStyle(el, '');
      } else if (el.currentStyle) {
        val = el.currentStyle;
      }

      return prop === void 0 ? val : val[prop];
    } else {
      if (!(prop in style) && prop.indexOf('webkit') === -1) {
        prop = '-webkit-' + prop;
      }

      style[prop] = val + (typeof val === 'string' ? '' : 'px');
    }
  }
}

function matrix(el, selfOnly) {
  var appliedTransforms = '';

  if (typeof el === 'string') {
    appliedTransforms = el;
  } else {
    do {
      var transform = css(el, 'transform');

      if (transform && transform !== 'none') {
        appliedTransforms = transform + ' ' + appliedTransforms;
      }
      /* jshint boss:true */

    } while (!selfOnly && (el = el.parentNode));
  }

  var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
  /*jshint -W056 */

  return matrixFn && new matrixFn(appliedTransforms);
}

function find(ctx, tagName, iterator) {
  if (ctx) {
    var list = ctx.getElementsByTagName(tagName),
        i = 0,
        n = list.length;

    if (iterator) {
      for (; i < n; i++) {
        iterator(list[i], i);
      }
    }

    return list;
  }

  return [];
}

function getWindowScrollingElement() {
  var scrollingElement = document.scrollingElement;

  if (scrollingElement) {
    return scrollingElement;
  } else {
    return document.documentElement;
  }
}
/**
 * Returns the "bounding client rect" of given element
 * @param  {HTMLElement} el                       The element whose boundingClientRect is wanted
 * @param  {[Boolean]} relativeToContainingBlock  Whether the rect should be relative to the containing block of (including) the container
 * @param  {[Boolean]} relativeToNonStaticParent  Whether the rect should be relative to the relative parent of (including) the contaienr
 * @param  {[Boolean]} undoScale                  Whether the container's scale() should be undone
 * @param  {[HTMLElement]} container              The parent the element will be placed in
 * @return {Object}                               The boundingClientRect of el, with specified adjustments
 */


function getRect(el, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
  if (!el.getBoundingClientRect && el !== window) return;
  var elRect, top, left, bottom, right, height, width;

  if (el !== window && el !== getWindowScrollingElement()) {
    elRect = el.getBoundingClientRect();
    top = elRect.top;
    left = elRect.left;
    bottom = elRect.bottom;
    right = elRect.right;
    height = elRect.height;
    width = elRect.width;
  } else {
    top = 0;
    left = 0;
    bottom = window.innerHeight;
    right = window.innerWidth;
    height = window.innerHeight;
    width = window.innerWidth;
  }

  if ((relativeToContainingBlock || relativeToNonStaticParent) && el !== window) {
    // Adjust for translate()
    container = container || el.parentNode; // solves #1123 (see: https://stackoverflow.com/a/37953806/6088312)
    // Not needed on <= IE11

    if (!IE11OrLess) {
      do {
        if (container && container.getBoundingClientRect && (css(container, 'transform') !== 'none' || relativeToNonStaticParent && css(container, 'position') !== 'static')) {
          var containerRect = container.getBoundingClientRect(); // Set relative to edges of padding box of container

          top -= containerRect.top + parseInt(css(container, 'border-top-width'));
          left -= containerRect.left + parseInt(css(container, 'border-left-width'));
          bottom = top + elRect.height;
          right = left + elRect.width;
          break;
        }
        /* jshint boss:true */

      } while (container = container.parentNode);
    }
  }

  if (undoScale && el !== window) {
    // Adjust for scale()
    var elMatrix = matrix(container || el),
        scaleX = elMatrix && elMatrix.a,
        scaleY = elMatrix && elMatrix.d;

    if (elMatrix) {
      top /= scaleY;
      left /= scaleX;
      width /= scaleX;
      height /= scaleY;
      bottom = top + height;
      right = left + width;
    }
  }

  return {
    top: top,
    left: left,
    bottom: bottom,
    right: right,
    width: width,
    height: height
  };
}
/**
 * Checks if a side of an element is scrolled past a side of its parents
 * @param  {HTMLElement}  el           The element who's side being scrolled out of view is in question
 * @param  {String}       elSide       Side of the element in question ('top', 'left', 'right', 'bottom')
 * @param  {String}       parentSide   Side of the parent in question ('top', 'left', 'right', 'bottom')
 * @return {HTMLElement}               The parent scroll element that the el's side is scrolled past, or null if there is no such element
 */


function isScrolledPast(el, elSide, parentSide) {
  var parent = getParentAutoScrollElement(el, true),
      elSideVal = getRect(el)[elSide];
  /* jshint boss:true */

  while (parent) {
    var parentSideVal = getRect(parent)[parentSide],
        visible = void 0;

    if (parentSide === 'top' || parentSide === 'left') {
      visible = elSideVal >= parentSideVal;
    } else {
      visible = elSideVal <= parentSideVal;
    }

    if (!visible) return parent;
    if (parent === getWindowScrollingElement()) break;
    parent = getParentAutoScrollElement(parent, false);
  }

  return false;
}
/**
 * Gets nth child of el, ignoring hidden children, sortable's elements (does not ignore clone if it's visible)
 * and non-draggable elements
 * @param  {HTMLElement} el       The parent element
 * @param  {Number} childNum      The index of the child
 * @param  {Object} options       Parent Sortable's options
 * @return {HTMLElement}          The child at index childNum, or null if not found
 */


function getChild(el, childNum, options) {
  var currentChild = 0,
      i = 0,
      children = el.children;

  while (i < children.length) {
    if (children[i].style.display !== 'none' && children[i] !== Sortable.ghost && children[i] !== Sortable.dragged && closest(children[i], options.draggable, el, false)) {
      if (currentChild === childNum) {
        return children[i];
      }

      currentChild++;
    }

    i++;
  }

  return null;
}
/**
 * Gets the last child in the el, ignoring ghostEl or invisible elements (clones)
 * @param  {HTMLElement} el       Parent element
 * @param  {selector} selector    Any other elements that should be ignored
 * @return {HTMLElement}          The last child, ignoring ghostEl
 */


function lastChild(el, selector) {
  var last = el.lastElementChild;

  while (last && (last === Sortable.ghost || css(last, 'display') === 'none' || selector && !matches(last, selector))) {
    last = last.previousElementSibling;
  }

  return last || null;
}
/**
 * Returns the index of an element within its parent for a selected set of
 * elements
 * @param  {HTMLElement} el
 * @param  {selector} selector
 * @return {number}
 */


function index(el, selector) {
  var index = 0;

  if (!el || !el.parentNode) {
    return -1;
  }
  /* jshint boss:true */


  while (el = el.previousElementSibling) {
    if (el.nodeName.toUpperCase() !== 'TEMPLATE' && el !== Sortable.clone && (!selector || matches(el, selector))) {
      index++;
    }
  }

  return index;
}
/**
 * Returns the scroll offset of the given element, added with all the scroll offsets of parent elements.
 * The value is returned in real pixels.
 * @param  {HTMLElement} el
 * @return {Array}             Offsets in the format of [left, top]
 */


function getRelativeScrollOffset(el) {
  var offsetLeft = 0,
      offsetTop = 0,
      winScroller = getWindowScrollingElement();

  if (el) {
    do {
      var elMatrix = matrix(el),
          scaleX = elMatrix.a,
          scaleY = elMatrix.d;
      offsetLeft += el.scrollLeft * scaleX;
      offsetTop += el.scrollTop * scaleY;
    } while (el !== winScroller && (el = el.parentNode));
  }

  return [offsetLeft, offsetTop];
}
/**
 * Returns the index of the object within the given array
 * @param  {Array} arr   Array that may or may not hold the object
 * @param  {Object} obj  An object that has a key-value pair unique to and identical to a key-value pair in the object you want to find
 * @return {Number}      The index of the object in the array, or -1
 */


function indexOfObject(arr, obj) {
  for (var i in arr) {
    if (!arr.hasOwnProperty(i)) continue;

    for (var key in obj) {
      if (obj.hasOwnProperty(key) && obj[key] === arr[i][key]) return Number(i);
    }
  }

  return -1;
}

function getParentAutoScrollElement(el, includeSelf) {
  // skip to window
  if (!el || !el.getBoundingClientRect) return getWindowScrollingElement();
  var elem = el;
  var gotSelf = false;

  do {
    // we don't need to get elem css if it isn't even overflowing in the first place (performance)
    if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
      var elemCSS = css(elem);

      if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == 'auto' || elemCSS.overflowX == 'scroll') || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == 'auto' || elemCSS.overflowY == 'scroll')) {
        if (!elem.getBoundingClientRect || elem === document.body) return getWindowScrollingElement();
        if (gotSelf || includeSelf) return elem;
        gotSelf = true;
      }
    }
    /* jshint boss:true */

  } while (elem = elem.parentNode);

  return getWindowScrollingElement();
}

function extend(dst, src) {
  if (dst && src) {
    for (var key in src) {
      if (src.hasOwnProperty(key)) {
        dst[key] = src[key];
      }
    }
  }

  return dst;
}

function isRectEqual(rect1, rect2) {
  return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
}

var _throttleTimeout;

function throttle(callback, ms) {
  return function () {
    if (!_throttleTimeout) {
      var args = arguments,
          _this = this;

      if (args.length === 1) {
        callback.call(_this, args[0]);
      } else {
        callback.apply(_this, args);
      }

      _throttleTimeout = setTimeout(function () {
        _throttleTimeout = void 0;
      }, ms);
    }
  };
}

function cancelThrottle() {
  clearTimeout(_throttleTimeout);
  _throttleTimeout = void 0;
}

function scrollBy(el, x, y) {
  el.scrollLeft += x;
  el.scrollTop += y;
}

function clone(el) {
  var Polymer = window.Polymer;
  var $ = window.jQuery || window.Zepto;

  if (Polymer && Polymer.dom) {
    return Polymer.dom(el).cloneNode(true);
  } else if ($) {
    return $(el).clone(true)[0];
  } else {
    return el.cloneNode(true);
  }
}

function setRect(el, rect) {
  css(el, 'position', 'absolute');
  css(el, 'top', rect.top);
  css(el, 'left', rect.left);
  css(el, 'width', rect.width);
  css(el, 'height', rect.height);
}

function unsetRect(el) {
  css(el, 'position', '');
  css(el, 'top', '');
  css(el, 'left', '');
  css(el, 'width', '');
  css(el, 'height', '');
}

var expando = 'Sortable' + new Date().getTime();

function AnimationStateManager() {
  var animationStates = [],
      animationCallbackId;
  return {
    captureAnimationState: function captureAnimationState() {
      animationStates = [];
      if (!this.options.animation) return;
      var children = [].slice.call(this.el.children);
      children.forEach(function (child) {
        if (css(child, 'display') === 'none' || child === Sortable.ghost) return;
        animationStates.push({
          target: child,
          rect: getRect(child)
        });

        var fromRect = _objectSpread({}, animationStates[animationStates.length - 1].rect); // If animating: compensate for current animation


        if (child.thisAnimationDuration) {
          var childMatrix = matrix(child, true);

          if (childMatrix) {
            fromRect.top -= childMatrix.f;
            fromRect.left -= childMatrix.e;
          }
        }

        child.fromRect = fromRect;
      });
    },
    addAnimationState: function addAnimationState(state) {
      animationStates.push(state);
    },
    removeAnimationState: function removeAnimationState(target) {
      animationStates.splice(indexOfObject(animationStates, {
        target: target
      }), 1);
    },
    animateAll: function animateAll(callback) {
      var _this = this;

      if (!this.options.animation) {
        clearTimeout(animationCallbackId);
        if (typeof callback === 'function') callback();
        return;
      }

      var animating = false,
          animationTime = 0;
      animationStates.forEach(function (state) {
        var time = 0,
            target = state.target,
            fromRect = target.fromRect,
            toRect = getRect(target),
            prevFromRect = target.prevFromRect,
            prevToRect = target.prevToRect,
            animatingRect = state.rect,
            targetMatrix = matrix(target, true);

        if (targetMatrix) {
          // Compensate for current animation
          toRect.top -= targetMatrix.f;
          toRect.left -= targetMatrix.e;
        }

        target.toRect = toRect;

        if (target.thisAnimationDuration) {
          // Could also check if animatingRect is between fromRect and toRect
          if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) && // Make sure animatingRect is on line between toRect & fromRect
          (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) {
            // If returning to same place as started from animation and on same axis
            time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
          }
        } // if fromRect != toRect: animate


        if (!isRectEqual(toRect, fromRect)) {
          target.prevFromRect = fromRect;
          target.prevToRect = toRect;

          if (!time) {
            time = _this.options.animation;
          }

          _this.animate(target, animatingRect, toRect, time);
        }

        if (time) {
          animating = true;
          animationTime = Math.max(animationTime, time);
          clearTimeout(target.animationResetTimer);
          target.animationResetTimer = setTimeout(function () {
            target.animationTime = 0;
            target.prevFromRect = null;
            target.fromRect = null;
            target.prevToRect = null;
            target.thisAnimationDuration = null;
          }, time);
          target.thisAnimationDuration = time;
        }
      });
      clearTimeout(animationCallbackId);

      if (!animating) {
        if (typeof callback === 'function') callback();
      } else {
        animationCallbackId = setTimeout(function () {
          if (typeof callback === 'function') callback();
        }, animationTime);
      }

      animationStates = [];
    },
    animate: function animate(target, currentRect, toRect, duration) {
      if (duration) {
        css(target, 'transition', '');
        css(target, 'transform', '');
        var elMatrix = matrix(this.el),
            scaleX = elMatrix && elMatrix.a,
            scaleY = elMatrix && elMatrix.d,
            translateX = (currentRect.left - toRect.left) / (scaleX || 1),
            translateY = (currentRect.top - toRect.top) / (scaleY || 1);
        target.animatingX = !!translateX;
        target.animatingY = !!translateY;
        css(target, 'transform', 'translate3d(' + translateX + 'px,' + translateY + 'px,0)');
        repaint(target); // repaint

        css(target, 'transition', 'transform ' + duration + 'ms' + (this.options.easing ? ' ' + this.options.easing : ''));
        css(target, 'transform', 'translate3d(0,0,0)');
        typeof target.animated === 'number' && clearTimeout(target.animated);
        target.animated = setTimeout(function () {
          css(target, 'transition', '');
          css(target, 'transform', '');
          target.animated = false;
          target.animatingX = false;
          target.animatingY = false;
        }, duration);
      }
    }
  };
}

function repaint(target) {
  return target.offsetWidth;
}

function calculateRealTime(animatingRect, fromRect, toRect, options) {
  return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options.animation;
}

var plugins = [];
var defaults = {
  initializeByDefault: true
};
var PluginManager = {
  mount: function mount(plugin) {
    // Set default static properties
    for (var option in defaults) {
      if (defaults.hasOwnProperty(option) && !(option in plugin)) {
        plugin[option] = defaults[option];
      }
    }

    plugins.push(plugin);
  },
  pluginEvent: function pluginEvent(eventName, sortable, evt) {
    var _this = this;

    this.eventCanceled = false;

    evt.cancel = function () {
      _this.eventCanceled = true;
    };

    var eventNameGlobal = eventName + 'Global';
    plugins.forEach(function (plugin) {
      if (!sortable[plugin.pluginName]) return; // Fire global events if it exists in this sortable

      if (sortable[plugin.pluginName][eventNameGlobal]) {
        sortable[plugin.pluginName][eventNameGlobal](_objectSpread({
          sortable: sortable
        }, evt));
      } // Only fire plugin event if plugin is enabled in this sortable,
      // and plugin has event defined


      if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
        sortable[plugin.pluginName][eventName](_objectSpread({
          sortable: sortable
        }, evt));
      }
    });
  },
  initializePlugins: function initializePlugins(sortable, el, defaults, options) {
    plugins.forEach(function (plugin) {
      var pluginName = plugin.pluginName;
      if (!sortable.options[pluginName] && !plugin.initializeByDefault) return;
      var initialized = new plugin(sortable, el, sortable.options);
      initialized.sortable = sortable;
      initialized.options = sortable.options;
      sortable[pluginName] = initialized; // Add default options from plugin

      _extends(defaults, initialized.defaults);
    });

    for (var option in sortable.options) {
      if (!sortable.options.hasOwnProperty(option)) continue;
      var modified = this.modifyOption(sortable, option, sortable.options[option]);

      if (typeof modified !== 'undefined') {
        sortable.options[option] = modified;
      }
    }
  },
  getEventProperties: function getEventProperties(name, sortable) {
    var eventProperties = {};
    plugins.forEach(function (plugin) {
      if (typeof plugin.eventProperties !== 'function') return;

      _extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
    });
    return eventProperties;
  },
  modifyOption: function modifyOption(sortable, name, value) {
    var modifiedValue;
    plugins.forEach(function (plugin) {
      // Plugin must exist on the Sortable
      if (!sortable[plugin.pluginName]) return; // If static option listener exists for this option, call in the context of the Sortable's instance of this plugin

      if (plugin.optionListeners && typeof plugin.optionListeners[name] === 'function') {
        modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
      }
    });
    return modifiedValue;
  }
};

function dispatchEvent(_ref) {
  var sortable = _ref.sortable,
      rootEl = _ref.rootEl,
      name = _ref.name,
      targetEl = _ref.targetEl,
      cloneEl = _ref.cloneEl,
      toEl = _ref.toEl,
      fromEl = _ref.fromEl,
      oldIndex = _ref.oldIndex,
      newIndex = _ref.newIndex,
      oldDraggableIndex = _ref.oldDraggableIndex,
      newDraggableIndex = _ref.newDraggableIndex,
      originalEvent = _ref.originalEvent,
      putSortable = _ref.putSortable,
      extraEventProperties = _ref.extraEventProperties;
  sortable = sortable || rootEl && rootEl[expando];
  if (!sortable) return;
  var evt,
      options = sortable.options,
      onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1); // Support for new CustomEvent feature

  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent(name, {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent('Event');
    evt.initEvent(name, true, true);
  }

  evt.to = toEl || rootEl;
  evt.from = fromEl || rootEl;
  evt.item = targetEl || rootEl;
  evt.clone = cloneEl;
  evt.oldIndex = oldIndex;
  evt.newIndex = newIndex;
  evt.oldDraggableIndex = oldDraggableIndex;
  evt.newDraggableIndex = newDraggableIndex;
  evt.originalEvent = originalEvent;
  evt.pullMode = putSortable ? putSortable.lastPutMode : undefined;

  var allEventProperties = _objectSpread({}, extraEventProperties, PluginManager.getEventProperties(name, sortable));

  for (var option in allEventProperties) {
    evt[option] = allEventProperties[option];
  }

  if (rootEl) {
    rootEl.dispatchEvent(evt);
  }

  if (options[onName]) {
    options[onName].call(sortable, evt);
  }
}

var pluginEvent = function pluginEvent(eventName, sortable) {
  var _ref = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
      originalEvent = _ref.evt,
      data = _objectWithoutProperties(_ref, ["evt"]);

  PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread({
    dragEl: dragEl,
    parentEl: parentEl,
    ghostEl: ghostEl,
    rootEl: rootEl,
    nextEl: nextEl,
    lastDownEl: lastDownEl,
    cloneEl: cloneEl,
    cloneHidden: cloneHidden,
    dragStarted: moved,
    putSortable: putSortable,
    activeSortable: Sortable.active,
    originalEvent: originalEvent,
    oldIndex: oldIndex,
    oldDraggableIndex: oldDraggableIndex,
    newIndex: newIndex,
    newDraggableIndex: newDraggableIndex,
    hideGhostForTarget: _hideGhostForTarget,
    unhideGhostForTarget: _unhideGhostForTarget,
    cloneNowHidden: function cloneNowHidden() {
      cloneHidden = true;
    },
    cloneNowShown: function cloneNowShown() {
      cloneHidden = false;
    },
    dispatchSortableEvent: function dispatchSortableEvent(name) {
      _dispatchEvent({
        sortable: sortable,
        name: name,
        originalEvent: originalEvent
      });
    }
  }, data));
};

function _dispatchEvent(info) {
  dispatchEvent(_objectSpread({
    putSortable: putSortable,
    cloneEl: cloneEl,
    targetEl: dragEl,
    rootEl: rootEl,
    oldIndex: oldIndex,
    oldDraggableIndex: oldDraggableIndex,
    newIndex: newIndex,
    newDraggableIndex: newDraggableIndex
  }, info));
}

var dragEl,
    parentEl,
    ghostEl,
    rootEl,
    nextEl,
    lastDownEl,
    cloneEl,
    cloneHidden,
    oldIndex,
    newIndex,
    oldDraggableIndex,
    newDraggableIndex,
    activeGroup,
    putSortable,
    awaitingDragStarted = false,
    ignoreNextClick = false,
    sortables = [],
    tapEvt,
    touchEvt,
    lastDx,
    lastDy,
    tapDistanceLeft,
    tapDistanceTop,
    moved,
    lastTarget,
    lastDirection,
    pastFirstInvertThresh = false,
    isCircumstantialInvert = false,
    targetMoveDistance,
    // For positioning ghost absolutely
ghostRelativeParent,
    ghostRelativeParentInitialScroll = [],
    // (left, top)
_silent = false,
    savedInputChecked = [];
/** @const */

var documentExists = typeof document !== 'undefined',
    PositionGhostAbsolutely = IOS,
    CSSFloatProperty = Edge || IE11OrLess ? 'cssFloat' : 'float',
    // This will not pass for IE9, because IE9 DnD only works on anchors
supportDraggable = documentExists && !ChromeForAndroid && !IOS && 'draggable' in document.createElement('div'),
    supportCssPointerEvents = function () {
  if (!documentExists) return; // false when <= IE11

  if (IE11OrLess) {
    return false;
  }

  var el = document.createElement('x');
  el.style.cssText = 'pointer-events:auto';
  return el.style.pointerEvents === 'auto';
}(),
    _detectDirection = function _detectDirection(el, options) {
  var elCSS = css(el),
      elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth),
      child1 = getChild(el, 0, options),
      child2 = getChild(el, 1, options),
      firstChildCSS = child1 && css(child1),
      secondChildCSS = child2 && css(child2),
      firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width,
      secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;

  if (elCSS.display === 'flex') {
    return elCSS.flexDirection === 'column' || elCSS.flexDirection === 'column-reverse' ? 'vertical' : 'horizontal';
  }

  if (elCSS.display === 'grid') {
    return elCSS.gridTemplateColumns.split(' ').length <= 1 ? 'vertical' : 'horizontal';
  }

  if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== 'none') {
    var touchingSideChild2 = firstChildCSS["float"] === 'left' ? 'left' : 'right';
    return child2 && (secondChildCSS.clear === 'both' || secondChildCSS.clear === touchingSideChild2) ? 'vertical' : 'horizontal';
  }

  return child1 && (firstChildCSS.display === 'block' || firstChildCSS.display === 'flex' || firstChildCSS.display === 'table' || firstChildCSS.display === 'grid' || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === 'none' || child2 && elCSS[CSSFloatProperty] === 'none' && firstChildWidth + secondChildWidth > elWidth) ? 'vertical' : 'horizontal';
},
    _dragElInRowColumn = function _dragElInRowColumn(dragRect, targetRect, vertical) {
  var dragElS1Opp = vertical ? dragRect.left : dragRect.top,
      dragElS2Opp = vertical ? dragRect.right : dragRect.bottom,
      dragElOppLength = vertical ? dragRect.width : dragRect.height,
      targetS1Opp = vertical ? targetRect.left : targetRect.top,
      targetS2Opp = vertical ? targetRect.right : targetRect.bottom,
      targetOppLength = vertical ? targetRect.width : targetRect.height;
  return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
},

/**
 * Detects first nearest empty sortable to X and Y position using emptyInsertThreshold.
 * @param  {Number} x      X position
 * @param  {Number} y      Y position
 * @return {HTMLElement}   Element of the first found nearest Sortable
 */
_detectNearestEmptySortable = function _detectNearestEmptySortable(x, y) {
  var ret;
  sortables.some(function (sortable) {
    if (lastChild(sortable)) return;
    var rect = getRect(sortable),
        threshold = sortable[expando].options.emptyInsertThreshold,
        insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold,
        insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;

    if (threshold && insideHorizontally && insideVertically) {
      return ret = sortable;
    }
  });
  return ret;
},
    _prepareGroup = function _prepareGroup(options) {
  function toFn(value, pull) {
    return function (to, from, dragEl, evt) {
      var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;

      if (value == null && (pull || sameGroup)) {
        // Default pull value
        // Default pull and put value if same group
        return true;
      } else if (value == null || value === false) {
        return false;
      } else if (pull && value === 'clone') {
        return value;
      } else if (typeof value === 'function') {
        return toFn(value(to, from, dragEl, evt), pull)(to, from, dragEl, evt);
      } else {
        var otherGroup = (pull ? to : from).options.group.name;
        return value === true || typeof value === 'string' && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
      }
    };
  }

  var group = {};
  var originalGroup = options.group;

  if (!originalGroup || _typeof(originalGroup) != 'object') {
    originalGroup = {
      name: originalGroup
    };
  }

  group.name = originalGroup.name;
  group.checkPull = toFn(originalGroup.pull, true);
  group.checkPut = toFn(originalGroup.put);
  group.revertClone = originalGroup.revertClone;
  options.group = group;
},
    _hideGhostForTarget = function _hideGhostForTarget() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, 'display', 'none');
  }
},
    _unhideGhostForTarget = function _unhideGhostForTarget() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, 'display', '');
  }
}; // #1184 fix - Prevent click event on fallback if dragged but item not changed position


if (documentExists) {
  document.addEventListener('click', function (evt) {
    if (ignoreNextClick) {
      evt.preventDefault();
      evt.stopPropagation && evt.stopPropagation();
      evt.stopImmediatePropagation && evt.stopImmediatePropagation();
      ignoreNextClick = false;
      return false;
    }
  }, true);
}

var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent(evt) {
  if (dragEl) {
    evt = evt.touches ? evt.touches[0] : evt;

    var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);

    if (nearest) {
      // Create imitation event
      var event = {};

      for (var i in evt) {
        if (evt.hasOwnProperty(i)) {
          event[i] = evt[i];
        }
      }

      event.target = event.rootEl = nearest;
      event.preventDefault = void 0;
      event.stopPropagation = void 0;

      nearest[expando]._onDragOver(event);
    }
  }
};

var _checkOutsideTargetEl = function _checkOutsideTargetEl(evt) {
  if (dragEl) {
    dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
  }
};
/**
 * @class  Sortable
 * @param  {HTMLElement}  el
 * @param  {Object}       [options]
 */


function Sortable(el, options) {
  if (!(el && el.nodeType && el.nodeType === 1)) {
    throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el));
  }

  this.el = el; // root element

  this.options = options = _extends({}, options); // Export instance

  el[expando] = this;
  var defaults = {
    group: null,
    sort: true,
    disabled: false,
    store: null,
    handle: null,
    draggable: /^[uo]l$/i.test(el.nodeName) ? '>li' : '>*',
    swapThreshold: 1,
    // percentage; 0 <= x <= 1
    invertSwap: false,
    // invert always
    invertedSwapThreshold: null,
    // will be set to same as swapThreshold if default
    removeCloneOnHide: true,
    direction: function direction() {
      return _detectDirection(el, this.options);
    },
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    dragClass: 'sortable-drag',
    ignore: 'a, img',
    filter: null,
    preventOnFilter: true,
    animation: 0,
    easing: null,
    setData: function setData(dataTransfer, dragEl) {
      dataTransfer.setData('Text', dragEl.textContent);
    },
    dropBubble: false,
    dragoverBubble: false,
    dataIdAttr: 'data-id',
    delay: 0,
    delayOnTouchOnly: false,
    touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
    forceFallback: false,
    fallbackClass: 'sortable-fallback',
    fallbackOnBody: false,
    fallbackTolerance: 0,
    fallbackOffset: {
      x: 0,
      y: 0
    },
    supportPointer: Sortable.supportPointer !== false && 'PointerEvent' in window,
    emptyInsertThreshold: 5
  };
  PluginManager.initializePlugins(this, el, defaults); // Set default options

  for (var name in defaults) {
    !(name in options) && (options[name] = defaults[name]);
  }

  _prepareGroup(options); // Bind all private methods


  for (var fn in this) {
    if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
      this[fn] = this[fn].bind(this);
    }
  } // Setup drag mode


  this.nativeDraggable = options.forceFallback ? false : supportDraggable;

  if (this.nativeDraggable) {
    // Touch start threshold cannot be greater than the native dragstart threshold
    this.options.touchStartThreshold = 1;
  } // Bind events


  if (options.supportPointer) {
    on(el, 'pointerdown', this._onTapStart);
  } else {
    on(el, 'mousedown', this._onTapStart);
    on(el, 'touchstart', this._onTapStart);
  }

  if (this.nativeDraggable) {
    on(el, 'dragover', this);
    on(el, 'dragenter', this);
  }

  sortables.push(this.el); // Restore sorting

  options.store && options.store.get && this.sort(options.store.get(this) || []); // Add animation state manager

  _extends(this, AnimationStateManager());
}

Sortable.prototype =
/** @lends Sortable.prototype */
{
  constructor: Sortable,
  _isOutsideThisEl: function _isOutsideThisEl(target) {
    if (!this.el.contains(target) && target !== this.el) {
      lastTarget = null;
    }
  },
  _getDirection: function _getDirection(evt, target) {
    return typeof this.options.direction === 'function' ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
  },
  _onTapStart: function _onTapStart(
  /** Event|TouchEvent */
  evt) {
    if (!evt.cancelable) return;

    var _this = this,
        el = this.el,
        options = this.options,
        preventOnFilter = options.preventOnFilter,
        type = evt.type,
        touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === 'touch' && evt,
        target = (touch || evt).target,
        originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target,
        filter = options.filter;

    _saveInputCheckedState(el); // Don't trigger start event when an element is been dragged, otherwise the evt.oldindex always wrong when set option.group.


    if (dragEl) {
      return;
    }

    if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) {
      return; // only left button and enabled
    } // cancel dnd if original target is content editable


    if (originalTarget.isContentEditable) {
      return;
    }

    target = closest(target, options.draggable, el, false);

    if (target && target.animated) {
      return;
    }

    if (lastDownEl === target) {
      // Ignoring duplicate `down`
      return;
    } // Get the index of the dragged element within its parent


    oldIndex = index(target);
    oldDraggableIndex = index(target, options.draggable); // Check filter

    if (typeof filter === 'function') {
      if (filter.call(this, evt, target, this)) {
        _dispatchEvent({
          sortable: _this,
          rootEl: originalTarget,
          name: 'filter',
          targetEl: target,
          toEl: el,
          fromEl: el
        });

        pluginEvent('filter', _this, {
          evt: evt
        });
        preventOnFilter && evt.cancelable && evt.preventDefault();
        return; // cancel dnd
      }
    } else if (filter) {
      filter = filter.split(',').some(function (criteria) {
        criteria = closest(originalTarget, criteria.trim(), el, false);

        if (criteria) {
          _dispatchEvent({
            sortable: _this,
            rootEl: criteria,
            name: 'filter',
            targetEl: target,
            fromEl: el,
            toEl: el
          });

          pluginEvent('filter', _this, {
            evt: evt
          });
          return true;
        }
      });

      if (filter) {
        preventOnFilter && evt.cancelable && evt.preventDefault();
        return; // cancel dnd
      }
    }

    if (options.handle && !closest(originalTarget, options.handle, el, false)) {
      return;
    } // Prepare `dragstart`


    this._prepareDragStart(evt, touch, target);
  },
  _prepareDragStart: function _prepareDragStart(
  /** Event */
  evt,
  /** Touch */
  touch,
  /** HTMLElement */
  target) {
    var _this = this,
        el = _this.el,
        options = _this.options,
        ownerDocument = el.ownerDocument,
        dragStartFn;

    if (target && !dragEl && target.parentNode === el) {
      var dragRect = getRect(target);
      rootEl = el;
      dragEl = target;
      parentEl = dragEl.parentNode;
      nextEl = dragEl.nextSibling;
      lastDownEl = target;
      activeGroup = options.group;
      Sortable.dragged = dragEl;
      tapEvt = {
        target: dragEl,
        clientX: (touch || evt).clientX,
        clientY: (touch || evt).clientY
      };
      tapDistanceLeft = tapEvt.clientX - dragRect.left;
      tapDistanceTop = tapEvt.clientY - dragRect.top;
      this._lastX = (touch || evt).clientX;
      this._lastY = (touch || evt).clientY;
      dragEl.style['will-change'] = 'all';

      dragStartFn = function dragStartFn() {
        pluginEvent('delayEnded', _this, {
          evt: evt
        });

        if (Sortable.eventCanceled) {
          _this._onDrop();

          return;
        } // Delayed drag has been triggered
        // we can re-enable the events: touchmove/mousemove


        _this._disableDelayedDragEvents();

        if (!FireFox && _this.nativeDraggable) {
          dragEl.draggable = true;
        } // Bind the events: dragstart/dragend


        _this._triggerDragStart(evt, touch); // Drag start event


        _dispatchEvent({
          sortable: _this,
          name: 'choose',
          originalEvent: evt
        }); // Chosen item


        toggleClass(dragEl, options.chosenClass, true);
      }; // Disable "draggable"


      options.ignore.split(',').forEach(function (criteria) {
        find(dragEl, criteria.trim(), _disableDraggable);
      });
      on(ownerDocument, 'dragover', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'mousemove', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'touchmove', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'mouseup', _this._onDrop);
      on(ownerDocument, 'touchend', _this._onDrop);
      on(ownerDocument, 'touchcancel', _this._onDrop); // Make dragEl draggable (must be before delay for FireFox)

      if (FireFox && this.nativeDraggable) {
        this.options.touchStartThreshold = 4;
        dragEl.draggable = true;
      }

      pluginEvent('delayStart', this, {
        evt: evt
      }); // Delay is impossible for native DnD in Edge or IE

      if (options.delay && (!options.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
        if (Sortable.eventCanceled) {
          this._onDrop();

          return;
        } // If the user moves the pointer or let go the click or touch
        // before the delay has been reached:
        // disable the delayed drag


        on(ownerDocument, 'mouseup', _this._disableDelayedDrag);
        on(ownerDocument, 'touchend', _this._disableDelayedDrag);
        on(ownerDocument, 'touchcancel', _this._disableDelayedDrag);
        on(ownerDocument, 'mousemove', _this._delayedDragTouchMoveHandler);
        on(ownerDocument, 'touchmove', _this._delayedDragTouchMoveHandler);
        options.supportPointer && on(ownerDocument, 'pointermove', _this._delayedDragTouchMoveHandler);
        _this._dragStartTimer = setTimeout(dragStartFn, options.delay);
      } else {
        dragStartFn();
      }
    }
  },
  _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(
  /** TouchEvent|PointerEvent **/
  e) {
    var touch = e.touches ? e.touches[0] : e;

    if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
      this._disableDelayedDrag();
    }
  },
  _disableDelayedDrag: function _disableDelayedDrag() {
    dragEl && _disableDraggable(dragEl);
    clearTimeout(this._dragStartTimer);

    this._disableDelayedDragEvents();
  },
  _disableDelayedDragEvents: function _disableDelayedDragEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, 'mouseup', this._disableDelayedDrag);
    off(ownerDocument, 'touchend', this._disableDelayedDrag);
    off(ownerDocument, 'touchcancel', this._disableDelayedDrag);
    off(ownerDocument, 'mousemove', this._delayedDragTouchMoveHandler);
    off(ownerDocument, 'touchmove', this._delayedDragTouchMoveHandler);
    off(ownerDocument, 'pointermove', this._delayedDragTouchMoveHandler);
  },
  _triggerDragStart: function _triggerDragStart(
  /** Event */
  evt,
  /** Touch */
  touch) {
    touch = touch || evt.pointerType == 'touch' && evt;

    if (!this.nativeDraggable || touch) {
      if (this.options.supportPointer) {
        on(document, 'pointermove', this._onTouchMove);
      } else if (touch) {
        on(document, 'touchmove', this._onTouchMove);
      } else {
        on(document, 'mousemove', this._onTouchMove);
      }
    } else {
      on(dragEl, 'dragend', this);
      on(rootEl, 'dragstart', this._onDragStart);
    }

    try {
      if (document.selection) {
        // Timeout neccessary for IE9
        _nextTick(function () {
          document.selection.empty();
        });
      } else {
        window.getSelection().removeAllRanges();
      }
    } catch (err) {}
  },
  _dragStarted: function _dragStarted(fallback, evt) {
    awaitingDragStarted = false;

    if (rootEl && dragEl) {
      pluginEvent('dragStarted', this, {
        evt: evt
      });

      if (this.nativeDraggable) {
        on(document, 'dragover', _checkOutsideTargetEl);
      }

      var options = this.options; // Apply effect

      !fallback && toggleClass(dragEl, options.dragClass, false);
      toggleClass(dragEl, options.ghostClass, true);
      Sortable.active = this;
      fallback && this._appendGhost(); // Drag start event

      _dispatchEvent({
        sortable: this,
        name: 'start',
        originalEvent: evt
      });
    } else {
      this._nulling();
    }
  },
  _emulateDragOver: function _emulateDragOver() {
    if (touchEvt) {
      this._lastX = touchEvt.clientX;
      this._lastY = touchEvt.clientY;

      _hideGhostForTarget();

      var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
      var parent = target;

      while (target && target.shadowRoot) {
        target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
        if (target === parent) break;
        parent = target;
      }

      dragEl.parentNode[expando]._isOutsideThisEl(target);

      if (parent) {
        do {
          if (parent[expando]) {
            var inserted = void 0;
            inserted = parent[expando]._onDragOver({
              clientX: touchEvt.clientX,
              clientY: touchEvt.clientY,
              target: target,
              rootEl: parent
            });

            if (inserted && !this.options.dragoverBubble) {
              break;
            }
          }

          target = parent; // store last element
        }
        /* jshint boss:true */
        while (parent = parent.parentNode);
      }

      _unhideGhostForTarget();
    }
  },
  _onTouchMove: function _onTouchMove(
  /**TouchEvent*/
  evt) {
    if (tapEvt) {
      var options = this.options,
          fallbackTolerance = options.fallbackTolerance,
          fallbackOffset = options.fallbackOffset,
          touch = evt.touches ? evt.touches[0] : evt,
          ghostMatrix = ghostEl && matrix(ghostEl, true),
          scaleX = ghostEl && ghostMatrix && ghostMatrix.a,
          scaleY = ghostEl && ghostMatrix && ghostMatrix.d,
          relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent),
          dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1),
          dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1); // only set the status to dragging, when we are actually dragging

      if (!Sortable.active && !awaitingDragStarted) {
        if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) {
          return;
        }

        this._onDragStart(evt, true);
      }

      if (ghostEl) {
        if (ghostMatrix) {
          ghostMatrix.e += dx - (lastDx || 0);
          ghostMatrix.f += dy - (lastDy || 0);
        } else {
          ghostMatrix = {
            a: 1,
            b: 0,
            c: 0,
            d: 1,
            e: dx,
            f: dy
          };
        }

        var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
        css(ghostEl, 'webkitTransform', cssMatrix);
        css(ghostEl, 'mozTransform', cssMatrix);
        css(ghostEl, 'msTransform', cssMatrix);
        css(ghostEl, 'transform', cssMatrix);
        lastDx = dx;
        lastDy = dy;
        touchEvt = touch;
      }

      evt.cancelable && evt.preventDefault();
    }
  },
  _appendGhost: function _appendGhost() {
    // Bug if using scale(): https://stackoverflow.com/questions/2637058
    // Not being adjusted for
    if (!ghostEl) {
      var container = this.options.fallbackOnBody ? document.body : rootEl,
          rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container),
          options = this.options; // Position absolutely

      if (PositionGhostAbsolutely) {
        // Get relatively positioned parent
        ghostRelativeParent = container;

        while (css(ghostRelativeParent, 'position') === 'static' && css(ghostRelativeParent, 'transform') === 'none' && ghostRelativeParent !== document) {
          ghostRelativeParent = ghostRelativeParent.parentNode;
        }

        if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
          if (ghostRelativeParent === document) ghostRelativeParent = getWindowScrollingElement();
          rect.top += ghostRelativeParent.scrollTop;
          rect.left += ghostRelativeParent.scrollLeft;
        } else {
          ghostRelativeParent = getWindowScrollingElement();
        }

        ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
      }

      ghostEl = dragEl.cloneNode(true);
      toggleClass(ghostEl, options.ghostClass, false);
      toggleClass(ghostEl, options.fallbackClass, true);
      toggleClass(ghostEl, options.dragClass, true);
      css(ghostEl, 'transition', '');
      css(ghostEl, 'transform', '');
      css(ghostEl, 'box-sizing', 'border-box');
      css(ghostEl, 'margin', 0);
      css(ghostEl, 'top', rect.top);
      css(ghostEl, 'left', rect.left);
      css(ghostEl, 'width', rect.width);
      css(ghostEl, 'height', rect.height);
      css(ghostEl, 'opacity', '0.8');
      css(ghostEl, 'position', PositionGhostAbsolutely ? 'absolute' : 'fixed');
      css(ghostEl, 'zIndex', '100000');
      css(ghostEl, 'pointerEvents', 'none');
      Sortable.ghost = ghostEl;
      container.appendChild(ghostEl); // Set transform-origin

      css(ghostEl, 'transform-origin', tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + '% ' + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + '%');
    }
  },
  _onDragStart: function _onDragStart(
  /**Event*/
  evt,
  /**boolean*/
  fallback) {
    var _this = this;

    var dataTransfer = evt.dataTransfer;
    var options = _this.options;
    pluginEvent('dragStart', this, {
      evt: evt
    });

    if (Sortable.eventCanceled) {
      this._onDrop();

      return;
    }

    pluginEvent('setupClone', this);

    if (!Sortable.eventCanceled) {
      cloneEl = clone(dragEl);
      cloneEl.draggable = false;
      cloneEl.style['will-change'] = '';

      this._hideClone();

      toggleClass(cloneEl, this.options.chosenClass, false);
      Sortable.clone = cloneEl;
    } // #1143: IFrame support workaround


    _this.cloneId = _nextTick(function () {
      pluginEvent('clone', _this);
      if (Sortable.eventCanceled) return;

      if (!_this.options.removeCloneOnHide) {
        rootEl.insertBefore(cloneEl, dragEl);
      }

      _this._hideClone();

      _dispatchEvent({
        sortable: _this,
        name: 'clone'
      });
    });
    !fallback && toggleClass(dragEl, options.dragClass, true); // Set proper drop events

    if (fallback) {
      ignoreNextClick = true;
      _this._loopId = setInterval(_this._emulateDragOver, 50);
    } else {
      // Undo what was set in _prepareDragStart before drag started
      off(document, 'mouseup', _this._onDrop);
      off(document, 'touchend', _this._onDrop);
      off(document, 'touchcancel', _this._onDrop);

      if (dataTransfer) {
        dataTransfer.effectAllowed = 'move';
        options.setData && options.setData.call(_this, dataTransfer, dragEl);
      }

      on(document, 'drop', _this); // #1276 fix:

      css(dragEl, 'transform', 'translateZ(0)');
    }

    awaitingDragStarted = true;
    _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
    on(document, 'selectstart', _this);
    moved = true;

    if (Safari) {
      css(document.body, 'user-select', 'none');
    }
  },
  // Returns true - if no further action is needed (either inserted or another condition)
  _onDragOver: function _onDragOver(
  /**Event*/
  evt) {
    var el = this.el,
        target = evt.target,
        dragRect,
        targetRect,
        revert,
        options = this.options,
        group = options.group,
        activeSortable = Sortable.active,
        isOwner = activeGroup === group,
        canSort = options.sort,
        fromSortable = putSortable || activeSortable,
        vertical,
        _this = this,
        completedFired = false;

    if (_silent) return;

    function dragOverEvent(name, extra) {
      pluginEvent(name, _this, _objectSpread({
        evt: evt,
        isOwner: isOwner,
        axis: vertical ? 'vertical' : 'horizontal',
        revert: revert,
        dragRect: dragRect,
        targetRect: targetRect,
        canSort: canSort,
        fromSortable: fromSortable,
        target: target,
        completed: completed,
        onMove: function onMove(target, after) {
          return _onMove(rootEl, el, dragEl, dragRect, target, getRect(target), evt, after);
        },
        changed: changed
      }, extra));
    } // Capture animation state


    function capture() {
      dragOverEvent('dragOverAnimationCapture');

      _this.captureAnimationState();

      if (_this !== fromSortable) {
        fromSortable.captureAnimationState();
      }
    } // Return invocation when dragEl is inserted (or completed)


    function completed(insertion) {
      dragOverEvent('dragOverCompleted', {
        insertion: insertion
      });

      if (insertion) {
        // Clones must be hidden before folding animation to capture dragRectAbsolute properly
        if (isOwner) {
          activeSortable._hideClone();
        } else {
          activeSortable._showClone(_this);
        }

        if (_this !== fromSortable) {
          // Set ghost class to new sortable's ghost class
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
          toggleClass(dragEl, options.ghostClass, true);
        }

        if (putSortable !== _this && _this !== Sortable.active) {
          putSortable = _this;
        } else if (_this === Sortable.active && putSortable) {
          putSortable = null;
        } // Animation


        if (fromSortable === _this) {
          _this._ignoreWhileAnimating = target;
        }

        _this.animateAll(function () {
          dragOverEvent('dragOverAnimationComplete');
          _this._ignoreWhileAnimating = null;
        });

        if (_this !== fromSortable) {
          fromSortable.animateAll();
          fromSortable._ignoreWhileAnimating = null;
        }
      } // Null lastTarget if it is not inside a previously swapped element


      if (target === dragEl && !dragEl.animated || target === el && !target.animated) {
        lastTarget = null;
      } // no bubbling and not fallback


      if (!options.dragoverBubble && !evt.rootEl && target !== document) {
        dragEl.parentNode[expando]._isOutsideThisEl(evt.target); // Do not detect for empty insert if already inserted


        !insertion && nearestEmptyInsertDetectEvent(evt);
      }

      !options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
      return completedFired = true;
    } // Call when dragEl has been inserted


    function changed() {
      newIndex = index(dragEl);
      newDraggableIndex = index(dragEl, options.draggable);

      _dispatchEvent({
        sortable: _this,
        name: 'change',
        toEl: el,
        newIndex: newIndex,
        newDraggableIndex: newDraggableIndex,
        originalEvent: evt
      });
    }

    if (evt.preventDefault !== void 0) {
      evt.cancelable && evt.preventDefault();
    }

    target = closest(target, options.draggable, el, true);
    dragOverEvent('dragOver');
    if (Sortable.eventCanceled) return completedFired;

    if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) {
      return completed(false);
    }

    ignoreNextClick = false;

    if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = !rootEl.contains(dragEl)) // Reverting item into the original list
    : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
      vertical = this._getDirection(evt, target) === 'vertical';
      dragRect = getRect(dragEl);
      dragOverEvent('dragOverValid');
      if (Sortable.eventCanceled) return completedFired;

      if (revert) {
        parentEl = rootEl; // actualization

        capture();

        this._hideClone();

        dragOverEvent('revert');

        if (!Sortable.eventCanceled) {
          if (nextEl) {
            rootEl.insertBefore(dragEl, nextEl);
          } else {
            rootEl.appendChild(dragEl);
          }
        }

        return completed(true);
      }

      var elLastChild = lastChild(el, options.draggable);

      if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
        // If already at end of list: Do not insert
        if (elLastChild === dragEl) {
          return completed(false);
        } // assign target only if condition is true


        if (elLastChild && el === evt.target) {
          target = elLastChild;
        }

        if (target) {
          targetRect = getRect(target);
        }

        if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
          capture();
          el.appendChild(dragEl);
          parentEl = el; // actualization

          changed();
          return completed(true);
        }
      } else if (target.parentNode === el) {
        targetRect = getRect(target);
        var direction = 0,
            targetBeforeFirstSwap,
            differentLevel = dragEl.parentNode !== el,
            differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical),
            side1 = vertical ? 'top' : 'left',
            scrolledPastTop = isScrolledPast(target, 'top', 'top') || isScrolledPast(dragEl, 'top', 'top'),
            scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;

        if (lastTarget !== target) {
          targetBeforeFirstSwap = targetRect[side1];
          pastFirstInvertThresh = false;
          isCircumstantialInvert = !differentRowCol && options.invertSwap || differentLevel;
        }

        direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
        var sibling;

        if (direction !== 0) {
          // Check if target is beside dragEl in respective direction (ignoring hidden elements)
          var dragIndex = index(dragEl);

          do {
            dragIndex -= direction;
            sibling = parentEl.children[dragIndex];
          } while (sibling && (css(sibling, 'display') === 'none' || sibling === ghostEl));
        } // If dragEl is already beside target: Do not insert


        if (direction === 0 || sibling === target) {
          return completed(false);
        }

        lastTarget = target;
        lastDirection = direction;
        var nextSibling = target.nextElementSibling,
            after = false;
        after = direction === 1;

        var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);

        if (moveVector !== false) {
          if (moveVector === 1 || moveVector === -1) {
            after = moveVector === 1;
          }

          _silent = true;
          setTimeout(_unsilent, 30);
          capture();

          if (after && !nextSibling) {
            el.appendChild(dragEl);
          } else {
            target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
          } // Undo chrome's scroll adjustment (has no effect on other browsers)


          if (scrolledPastTop) {
            scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
          }

          parentEl = dragEl.parentNode; // actualization
          // must be done before animation

          if (targetBeforeFirstSwap !== undefined && !isCircumstantialInvert) {
            targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
          }

          changed();
          return completed(true);
        }
      }

      if (el.contains(dragEl)) {
        return completed(false);
      }
    }

    return false;
  },
  _ignoreWhileAnimating: null,
  _offMoveEvents: function _offMoveEvents() {
    off(document, 'mousemove', this._onTouchMove);
    off(document, 'touchmove', this._onTouchMove);
    off(document, 'pointermove', this._onTouchMove);
    off(document, 'dragover', nearestEmptyInsertDetectEvent);
    off(document, 'mousemove', nearestEmptyInsertDetectEvent);
    off(document, 'touchmove', nearestEmptyInsertDetectEvent);
  },
  _offUpEvents: function _offUpEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, 'mouseup', this._onDrop);
    off(ownerDocument, 'touchend', this._onDrop);
    off(ownerDocument, 'pointerup', this._onDrop);
    off(ownerDocument, 'touchcancel', this._onDrop);
    off(document, 'selectstart', this);
  },
  _onDrop: function _onDrop(
  /**Event*/
  evt) {
    var el = this.el,
        options = this.options; // Get the index of the dragged element within its parent

    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options.draggable);
    pluginEvent('drop', this, {
      evt: evt
    });
    parentEl = dragEl && dragEl.parentNode; // Get again after plugin event

    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options.draggable);

    if (Sortable.eventCanceled) {
      this._nulling();

      return;
    }

    awaitingDragStarted = false;
    isCircumstantialInvert = false;
    pastFirstInvertThresh = false;
    clearInterval(this._loopId);
    clearTimeout(this._dragStartTimer);

    _cancelNextTick(this.cloneId);

    _cancelNextTick(this._dragStartId); // Unbind events


    if (this.nativeDraggable) {
      off(document, 'drop', this);
      off(el, 'dragstart', this._onDragStart);
    }

    this._offMoveEvents();

    this._offUpEvents();

    if (Safari) {
      css(document.body, 'user-select', '');
    }

    css(dragEl, 'transform', '');

    if (evt) {
      if (moved) {
        evt.cancelable && evt.preventDefault();
        !options.dropBubble && evt.stopPropagation();
      }

      ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);

      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
        // Remove clone(s)
        cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
      }

      if (dragEl) {
        if (this.nativeDraggable) {
          off(dragEl, 'dragend', this);
        }

        _disableDraggable(dragEl);

        dragEl.style['will-change'] = ''; // Remove classes
        // ghostClass is added in dragStarted

        if (moved && !awaitingDragStarted) {
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
        }

        toggleClass(dragEl, this.options.chosenClass, false); // Drag stop event

        _dispatchEvent({
          sortable: this,
          name: 'unchoose',
          toEl: parentEl,
          newIndex: null,
          newDraggableIndex: null,
          originalEvent: evt
        });

        if (rootEl !== parentEl) {
          if (newIndex >= 0) {
            // Add event
            _dispatchEvent({
              rootEl: parentEl,
              name: 'add',
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            }); // Remove event


            _dispatchEvent({
              sortable: this,
              name: 'remove',
              toEl: parentEl,
              originalEvent: evt
            }); // drag from one list and drop into another


            _dispatchEvent({
              rootEl: parentEl,
              name: 'sort',
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            });

            _dispatchEvent({
              sortable: this,
              name: 'sort',
              toEl: parentEl,
              originalEvent: evt
            });
          }

          putSortable && putSortable.save();
        } else {
          if (newIndex !== oldIndex) {
            if (newIndex >= 0) {
              // drag & drop within the same list
              _dispatchEvent({
                sortable: this,
                name: 'update',
                toEl: parentEl,
                originalEvent: evt
              });

              _dispatchEvent({
                sortable: this,
                name: 'sort',
                toEl: parentEl,
                originalEvent: evt
              });
            }
          }
        }

        if (Sortable.active) {
          /* jshint eqnull:true */
          if (newIndex == null || newIndex === -1) {
            newIndex = oldIndex;
            newDraggableIndex = oldDraggableIndex;
          }

          _dispatchEvent({
            sortable: this,
            name: 'end',
            toEl: parentEl,
            originalEvent: evt
          }); // Save sorting


          this.save();
        }
      }
    }

    this._nulling();
  },
  _nulling: function _nulling() {
    pluginEvent('nulling', this);
    rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable.dragged = Sortable.ghost = Sortable.clone = Sortable.active = null;
    savedInputChecked.forEach(function (el) {
      el.checked = true;
    });
    savedInputChecked.length = lastDx = lastDy = 0;
  },
  handleEvent: function handleEvent(
  /**Event*/
  evt) {
    switch (evt.type) {
      case 'drop':
      case 'dragend':
        this._onDrop(evt);

        break;

      case 'dragenter':
      case 'dragover':
        if (dragEl) {
          this._onDragOver(evt);

          _globalDragOver(evt);
        }

        break;

      case 'selectstart':
        evt.preventDefault();
        break;
    }
  },

  /**
   * Serializes the item into an array of string.
   * @returns {String[]}
   */
  toArray: function toArray() {
    var order = [],
        el,
        children = this.el.children,
        i = 0,
        n = children.length,
        options = this.options;

    for (; i < n; i++) {
      el = children[i];

      if (closest(el, options.draggable, this.el, false)) {
        order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
      }
    }

    return order;
  },

  /**
   * Sorts the elements according to the array.
   * @param  {String[]}  order  order of the items
   */
  sort: function sort(order) {
    var items = {},
        rootEl = this.el;
    this.toArray().forEach(function (id, i) {
      var el = rootEl.children[i];

      if (closest(el, this.options.draggable, rootEl, false)) {
        items[id] = el;
      }
    }, this);
    order.forEach(function (id) {
      if (items[id]) {
        rootEl.removeChild(items[id]);
        rootEl.appendChild(items[id]);
      }
    });
  },

  /**
   * Save the current sorting
   */
  save: function save() {
    var store = this.options.store;
    store && store.set && store.set(this);
  },

  /**
   * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
   * @param   {HTMLElement}  el
   * @param   {String}       [selector]  default: `options.draggable`
   * @returns {HTMLElement|null}
   */
  closest: function closest$1(el, selector) {
    return closest(el, selector || this.options.draggable, this.el, false);
  },

  /**
   * Set/get option
   * @param   {string} name
   * @param   {*}      [value]
   * @returns {*}
   */
  option: function option(name, value) {
    var options = this.options;

    if (value === void 0) {
      return options[name];
    } else {
      var modifiedValue = PluginManager.modifyOption(this, name, value);

      if (typeof modifiedValue !== 'undefined') {
        options[name] = modifiedValue;
      } else {
        options[name] = value;
      }

      if (name === 'group') {
        _prepareGroup(options);
      }
    }
  },

  /**
   * Destroy
   */
  destroy: function destroy() {
    pluginEvent('destroy', this);
    var el = this.el;
    el[expando] = null;
    off(el, 'mousedown', this._onTapStart);
    off(el, 'touchstart', this._onTapStart);
    off(el, 'pointerdown', this._onTapStart);

    if (this.nativeDraggable) {
      off(el, 'dragover', this);
      off(el, 'dragenter', this);
    } // Remove draggable attributes


    Array.prototype.forEach.call(el.querySelectorAll('[draggable]'), function (el) {
      el.removeAttribute('draggable');
    });

    this._onDrop();

    this._disableDelayedDragEvents();

    sortables.splice(sortables.indexOf(this.el), 1);
    this.el = el = null;
  },
  _hideClone: function _hideClone() {
    if (!cloneHidden) {
      pluginEvent('hideClone', this);
      if (Sortable.eventCanceled) return;
      css(cloneEl, 'display', 'none');

      if (this.options.removeCloneOnHide && cloneEl.parentNode) {
        cloneEl.parentNode.removeChild(cloneEl);
      }

      cloneHidden = true;
    }
  },
  _showClone: function _showClone(putSortable) {
    if (putSortable.lastPutMode !== 'clone') {
      this._hideClone();

      return;
    }

    if (cloneHidden) {
      pluginEvent('showClone', this);
      if (Sortable.eventCanceled) return; // show clone at dragEl or original position

      if (rootEl.contains(dragEl) && !this.options.group.revertClone) {
        rootEl.insertBefore(cloneEl, dragEl);
      } else if (nextEl) {
        rootEl.insertBefore(cloneEl, nextEl);
      } else {
        rootEl.appendChild(cloneEl);
      }

      if (this.options.group.revertClone) {
        this.animate(dragEl, cloneEl);
      }

      css(cloneEl, 'display', '');
      cloneHidden = false;
    }
  }
};

function _globalDragOver(
/**Event*/
evt) {
  if (evt.dataTransfer) {
    evt.dataTransfer.dropEffect = 'move';
  }

  evt.cancelable && evt.preventDefault();
}

function _onMove(fromEl, toEl, dragEl, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
  var evt,
      sortable = fromEl[expando],
      onMoveFn = sortable.options.onMove,
      retVal; // Support for new CustomEvent feature

  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent('move', {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent('Event');
    evt.initEvent('move', true, true);
  }

  evt.to = toEl;
  evt.from = fromEl;
  evt.dragged = dragEl;
  evt.draggedRect = dragRect;
  evt.related = targetEl || toEl;
  evt.relatedRect = targetRect || getRect(toEl);
  evt.willInsertAfter = willInsertAfter;
  evt.originalEvent = originalEvent;
  fromEl.dispatchEvent(evt);

  if (onMoveFn) {
    retVal = onMoveFn.call(sortable, evt, originalEvent);
  }

  return retVal;
}

function _disableDraggable(el) {
  el.draggable = false;
}

function _unsilent() {
  _silent = false;
}

function _ghostIsLast(evt, vertical, sortable) {
  var rect = getRect(lastChild(sortable.el, sortable.options.draggable));
  var spacer = 10;
  return vertical ? evt.clientX > rect.right + spacer || evt.clientX <= rect.right && evt.clientY > rect.bottom && evt.clientX >= rect.left : evt.clientX > rect.right && evt.clientY > rect.top || evt.clientX <= rect.right && evt.clientY > rect.bottom + spacer;
}

function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
  var mouseOnAxis = vertical ? evt.clientY : evt.clientX,
      targetLength = vertical ? targetRect.height : targetRect.width,
      targetS1 = vertical ? targetRect.top : targetRect.left,
      targetS2 = vertical ? targetRect.bottom : targetRect.right,
      invert = false;

  if (!invertSwap) {
    // Never invert or create dragEl shadow when target movemenet causes mouse to move past the end of regular swapThreshold
    if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
      // multiplied only by swapThreshold because mouse will already be inside target by (1 - threshold) * targetLength / 2
      // check if past first invert threshold on side opposite of lastDirection
      if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
        // past first invert threshold, do not restrict inverted threshold to dragEl shadow
        pastFirstInvertThresh = true;
      }

      if (!pastFirstInvertThresh) {
        // dragEl shadow (target move distance shadow)
        if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance // over dragEl shadow
        : mouseOnAxis > targetS2 - targetMoveDistance) {
          return -lastDirection;
        }
      } else {
        invert = true;
      }
    } else {
      // Regular
      if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
        return _getInsertDirection(target);
      }
    }
  }

  invert = invert || invertSwap;

  if (invert) {
    // Invert of regular
    if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
      return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
    }
  }

  return 0;
}
/**
 * Gets the direction dragEl must be swapped relative to target in order to make it
 * seem that dragEl has been "inserted" into that element's position
 * @param  {HTMLElement} target       The target whose position dragEl is being inserted at
 * @return {Number}                   Direction dragEl must be swapped
 */


function _getInsertDirection(target) {
  if (index(dragEl) < index(target)) {
    return 1;
  } else {
    return -1;
  }
}
/**
 * Generate id
 * @param   {HTMLElement} el
 * @returns {String}
 * @private
 */


function _generateId(el) {
  var str = el.tagName + el.className + el.src + el.href + el.textContent,
      i = str.length,
      sum = 0;

  while (i--) {
    sum += str.charCodeAt(i);
  }

  return sum.toString(36);
}

function _saveInputCheckedState(root) {
  savedInputChecked.length = 0;
  var inputs = root.getElementsByTagName('input');
  var idx = inputs.length;

  while (idx--) {
    var el = inputs[idx];
    el.checked && savedInputChecked.push(el);
  }
}

function _nextTick(fn) {
  return setTimeout(fn, 0);
}

function _cancelNextTick(id) {
  return clearTimeout(id);
} // Fixed #973:


if (documentExists) {
  on(document, 'touchmove', function (evt) {
    if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
      evt.preventDefault();
    }
  });
} // Export utils


Sortable.utils = {
  on: on,
  off: off,
  css: css,
  find: find,
  is: function is(el, selector) {
    return !!closest(el, selector, el, false);
  },
  extend: extend,
  throttle: throttle,
  closest: closest,
  toggleClass: toggleClass,
  clone: clone,
  index: index,
  nextTick: _nextTick,
  cancelNextTick: _cancelNextTick,
  detectDirection: _detectDirection,
  getChild: getChild
};
/**
 * Get the Sortable instance of an element
 * @param  {HTMLElement} element The element
 * @return {Sortable|undefined}         The instance of Sortable
 */

Sortable.get = function (element) {
  return element[expando];
};
/**
 * Mount a plugin to Sortable
 * @param  {...SortablePlugin|SortablePlugin[]} plugins       Plugins being mounted
 */


Sortable.mount = function () {
  for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) {
    plugins[_key] = arguments[_key];
  }

  if (plugins[0].constructor === Array) plugins = plugins[0];
  plugins.forEach(function (plugin) {
    if (!plugin.prototype || !plugin.prototype.constructor) {
      throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
    }

    if (plugin.utils) Sortable.utils = _objectSpread({}, Sortable.utils, plugin.utils);
    PluginManager.mount(plugin);
  });
};
/**
 * Create sortable instance
 * @param {HTMLElement}  el
 * @param {Object}      [options]
 */


Sortable.create = function (el, options) {
  return new Sortable(el, options);
}; // Export


Sortable.version = version;
var autoScrolls = [],
    scrollEl,
    scrollRootEl,
    scrolling = false,
    lastAutoScrollX,
    lastAutoScrollY,
    touchEvt$1,
    pointerElemChangedInterval;

function AutoScrollPlugin() {
  function AutoScroll() {
    this.defaults = {
      scroll: true,
      scrollSensitivity: 30,
      scrollSpeed: 10,
      bubbleScroll: true
    }; // Bind all private methods

    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    }
  }

  AutoScroll.prototype = {
    dragStarted: function dragStarted(_ref) {
      var originalEvent = _ref.originalEvent;

      if (this.sortable.nativeDraggable) {
        on(document, 'dragover', this._handleAutoScroll);
      } else {
        if (this.options.supportPointer) {
          on(document, 'pointermove', this._handleFallbackAutoScroll);
        } else if (originalEvent.touches) {
          on(document, 'touchmove', this._handleFallbackAutoScroll);
        } else {
          on(document, 'mousemove', this._handleFallbackAutoScroll);
        }
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref2) {
      var originalEvent = _ref2.originalEvent; // For when bubbling is canceled and using fallback (fallback 'touchmove' always reached)

      if (!this.options.dragOverBubble && !originalEvent.rootEl) {
        this._handleAutoScroll(originalEvent);
      }
    },
    drop: function drop() {
      if (this.sortable.nativeDraggable) {
        off(document, 'dragover', this._handleAutoScroll);
      } else {
        off(document, 'pointermove', this._handleFallbackAutoScroll);
        off(document, 'touchmove', this._handleFallbackAutoScroll);
        off(document, 'mousemove', this._handleFallbackAutoScroll);
      }

      clearPointerElemChangedInterval();
      clearAutoScrolls();
      cancelThrottle();
    },
    nulling: function nulling() {
      touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
      autoScrolls.length = 0;
    },
    _handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
      this._handleAutoScroll(evt, true);
    },
    _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
      var _this = this;

      var x = (evt.touches ? evt.touches[0] : evt).clientX,
          y = (evt.touches ? evt.touches[0] : evt).clientY,
          elem = document.elementFromPoint(x, y);
      touchEvt$1 = evt; // IE does not seem to have native autoscroll,
      // Edge's autoscroll seems too conditional,
      // MACOS Safari does not have autoscroll,
      // Firefox and Chrome are good

      if (fallback || Edge || IE11OrLess || Safari) {
        autoScroll(evt, this.options, elem, fallback); // Listener for pointer element change

        var ogElemScroller = getParentAutoScrollElement(elem, true);

        if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
          pointerElemChangedInterval && clearPointerElemChangedInterval(); // Detect for pointer elem change, emulating native DnD behaviour

          pointerElemChangedInterval = setInterval(function () {
            var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);

            if (newElem !== ogElemScroller) {
              ogElemScroller = newElem;
              clearAutoScrolls();
            }

            autoScroll(evt, _this.options, newElem, fallback);
          }, 10);
          lastAutoScrollX = x;
          lastAutoScrollY = y;
        }
      } else {
        // if DnD is enabled (and browser has good autoscrolling), first autoscroll will already scroll, so get parent autoscroll of first autoscroll
        if (!this.options.bubbleScroll || getParentAutoScrollElement(elem, true) === getWindowScrollingElement()) {
          clearAutoScrolls();
          return;
        }

        autoScroll(evt, this.options, getParentAutoScrollElement(elem, false), false);
      }
    }
  };
  return _extends(AutoScroll, {
    pluginName: 'scroll',
    initializeByDefault: true
  });
}

function clearAutoScrolls() {
  autoScrolls.forEach(function (autoScroll) {
    clearInterval(autoScroll.pid);
  });
  autoScrolls = [];
}

function clearPointerElemChangedInterval() {
  clearInterval(pointerElemChangedInterval);
}

var autoScroll = throttle(function (evt, options, rootEl, isFallback) {
  // Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=505521
  if (!options.scroll) return;
  var x = (evt.touches ? evt.touches[0] : evt).clientX,
      y = (evt.touches ? evt.touches[0] : evt).clientY,
      sens = options.scrollSensitivity,
      speed = options.scrollSpeed,
      winScroller = getWindowScrollingElement();
  var scrollThisInstance = false,
      scrollCustomFn; // New scroll root, set scrollEl

  if (scrollRootEl !== rootEl) {
    scrollRootEl = rootEl;
    clearAutoScrolls();
    scrollEl = options.scroll;
    scrollCustomFn = options.scrollFn;

    if (scrollEl === true) {
      scrollEl = getParentAutoScrollElement(rootEl, true);
    }
  }

  var layersOut = 0;
  var currentParent = scrollEl;

  do {
    var el = currentParent,
        rect = getRect(el),
        top = rect.top,
        bottom = rect.bottom,
        left = rect.left,
        right = rect.right,
        width = rect.width,
        height = rect.height,
        canScrollX = void 0,
        canScrollY = void 0,
        scrollWidth = el.scrollWidth,
        scrollHeight = el.scrollHeight,
        elCSS = css(el),
        scrollPosX = el.scrollLeft,
        scrollPosY = el.scrollTop;

    if (el === winScroller) {
      canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll' || elCSS.overflowX === 'visible');
      canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll' || elCSS.overflowY === 'visible');
    } else {
      canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll');
      canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll');
    }

    var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
    var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);

    if (!autoScrolls[layersOut]) {
      for (var i = 0; i <= layersOut; i++) {
        if (!autoScrolls[i]) {
          autoScrolls[i] = {};
        }
      }
    }

    if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
      autoScrolls[layersOut].el = el;
      autoScrolls[layersOut].vx = vx;
      autoScrolls[layersOut].vy = vy;
      clearInterval(autoScrolls[layersOut].pid);

      if (vx != 0 || vy != 0) {
        scrollThisInstance = true;
        /* jshint loopfunc:true */

        autoScrolls[layersOut].pid = setInterval(function () {
          // emulate drag over during autoscroll (fallback), emulating native DnD behaviour
          if (isFallback && this.layer === 0) {
            Sortable.active._onTouchMove(touchEvt$1); // To move ghost if it is positioned absolutely

          }

          var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
          var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;

          if (typeof scrollCustomFn === 'function') {
            if (scrollCustomFn.call(Sortable.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== 'continue') {
              return;
            }
          }

          scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
        }.bind({
          layer: layersOut
        }), 24);
      }
    }

    layersOut++;
  } while (options.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));

  scrolling = scrollThisInstance; // in case another function catches scrolling as false in between when it is not
}, 30);

var drop = function drop(_ref) {
  var originalEvent = _ref.originalEvent,
      putSortable = _ref.putSortable,
      dragEl = _ref.dragEl,
      activeSortable = _ref.activeSortable,
      dispatchSortableEvent = _ref.dispatchSortableEvent,
      hideGhostForTarget = _ref.hideGhostForTarget,
      unhideGhostForTarget = _ref.unhideGhostForTarget;
  if (!originalEvent) return;
  var toSortable = putSortable || activeSortable;
  hideGhostForTarget();
  var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
  var target = document.elementFromPoint(touch.clientX, touch.clientY);
  unhideGhostForTarget();

  if (toSortable && !toSortable.el.contains(target)) {
    dispatchSortableEvent('spill');
    this.onSpill({
      dragEl: dragEl,
      putSortable: putSortable
    });
  }
};

function Revert() {}

Revert.prototype = {
  startIndex: null,
  dragStart: function dragStart(_ref2) {
    var oldDraggableIndex = _ref2.oldDraggableIndex;
    this.startIndex = oldDraggableIndex;
  },
  onSpill: function onSpill(_ref3) {
    var dragEl = _ref3.dragEl,
        putSortable = _ref3.putSortable;
    this.sortable.captureAnimationState();

    if (putSortable) {
      putSortable.captureAnimationState();
    }

    var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);

    if (nextSibling) {
      this.sortable.el.insertBefore(dragEl, nextSibling);
    } else {
      this.sortable.el.appendChild(dragEl);
    }

    this.sortable.animateAll();

    if (putSortable) {
      putSortable.animateAll();
    }
  },
  drop: drop
};

_extends(Revert, {
  pluginName: 'revertOnSpill'
});

function Remove() {}

Remove.prototype = {
  onSpill: function onSpill(_ref4) {
    var dragEl = _ref4.dragEl,
        putSortable = _ref4.putSortable;
    var parentSortable = putSortable || this.sortable;
    parentSortable.captureAnimationState();
    dragEl.parentNode && dragEl.parentNode.removeChild(dragEl);
    parentSortable.animateAll();
  },
  drop: drop
};

_extends(Remove, {
  pluginName: 'removeOnSpill'
});

var lastSwapEl;

function SwapPlugin() {
  function Swap() {
    this.defaults = {
      swapClass: 'sortable-swap-highlight'
    };
  }

  Swap.prototype = {
    dragStart: function dragStart(_ref) {
      var dragEl = _ref.dragEl;
      lastSwapEl = dragEl;
    },
    dragOverValid: function dragOverValid(_ref2) {
      var completed = _ref2.completed,
          target = _ref2.target,
          onMove = _ref2.onMove,
          activeSortable = _ref2.activeSortable,
          changed = _ref2.changed,
          cancel = _ref2.cancel;
      if (!activeSortable.options.swap) return;
      var el = this.sortable.el,
          options = this.options;

      if (target && target !== el) {
        var prevSwapEl = lastSwapEl;

        if (onMove(target) !== false) {
          toggleClass(target, options.swapClass, true);
          lastSwapEl = target;
        } else {
          lastSwapEl = null;
        }

        if (prevSwapEl && prevSwapEl !== lastSwapEl) {
          toggleClass(prevSwapEl, options.swapClass, false);
        }
      }

      changed();
      completed(true);
      cancel();
    },
    drop: function drop(_ref3) {
      var activeSortable = _ref3.activeSortable,
          putSortable = _ref3.putSortable,
          dragEl = _ref3.dragEl;
      var toSortable = putSortable || this.sortable;
      var options = this.options;
      lastSwapEl && toggleClass(lastSwapEl, options.swapClass, false);

      if (lastSwapEl && (options.swap || putSortable && putSortable.options.swap)) {
        if (dragEl !== lastSwapEl) {
          toSortable.captureAnimationState();
          if (toSortable !== activeSortable) activeSortable.captureAnimationState();
          swapNodes(dragEl, lastSwapEl);
          toSortable.animateAll();
          if (toSortable !== activeSortable) activeSortable.animateAll();
        }
      }
    },
    nulling: function nulling() {
      lastSwapEl = null;
    }
  };
  return _extends(Swap, {
    pluginName: 'swap',
    eventProperties: function eventProperties() {
      return {
        swapItem: lastSwapEl
      };
    }
  });
}

function swapNodes(n1, n2) {
  var p1 = n1.parentNode,
      p2 = n2.parentNode,
      i1,
      i2;
  if (!p1 || !p2 || p1.isEqualNode(n2) || p2.isEqualNode(n1)) return;
  i1 = index(n1);
  i2 = index(n2);

  if (p1.isEqualNode(p2) && i1 < i2) {
    i2++;
  }

  p1.insertBefore(n2, p1.children[i1]);
  p2.insertBefore(n1, p2.children[i2]);
}

var multiDragElements = [],
    multiDragClones = [],
    lastMultiDragSelect,
    // for selection with modifier key down (SHIFT)
multiDragSortable,
    initialFolding = false,
    // Initial multi-drag fold when drag started
folding = false,
    // Folding any other time
dragStarted = false,
    dragEl$1,
    clonesFromRect,
    clonesHidden;

function MultiDragPlugin() {
  function MultiDrag(sortable) {
    // Bind all private methods
    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    }

    if (sortable.options.supportPointer) {
      on(document, 'pointerup', this._deselectMultiDrag);
    } else {
      on(document, 'mouseup', this._deselectMultiDrag);
      on(document, 'touchend', this._deselectMultiDrag);
    }

    on(document, 'keydown', this._checkKeyDown);
    on(document, 'keyup', this._checkKeyUp);
    this.defaults = {
      selectedClass: 'sortable-selected',
      multiDragKey: null,
      setData: function setData(dataTransfer, dragEl) {
        var data = '';

        if (multiDragElements.length && multiDragSortable === sortable) {
          multiDragElements.forEach(function (multiDragElement, i) {
            data += (!i ? '' : ', ') + multiDragElement.textContent;
          });
        } else {
          data = dragEl.textContent;
        }

        dataTransfer.setData('Text', data);
      }
    };
  }

  MultiDrag.prototype = {
    multiDragKeyDown: false,
    isMultiDrag: false,
    delayStartGlobal: function delayStartGlobal(_ref) {
      var dragged = _ref.dragEl;
      dragEl$1 = dragged;
    },
    delayEnded: function delayEnded() {
      this.isMultiDrag = ~multiDragElements.indexOf(dragEl$1);
    },
    setupClone: function setupClone(_ref2) {
      var sortable = _ref2.sortable,
          cancel = _ref2.cancel;
      if (!this.isMultiDrag) return;

      for (var i = 0; i < multiDragElements.length; i++) {
        multiDragClones.push(clone(multiDragElements[i]));
        multiDragClones[i].sortableIndex = multiDragElements[i].sortableIndex;
        multiDragClones[i].draggable = false;
        multiDragClones[i].style['will-change'] = '';
        toggleClass(multiDragClones[i], this.options.selectedClass, false);
        multiDragElements[i] === dragEl$1 && toggleClass(multiDragClones[i], this.options.chosenClass, false);
      }

      sortable._hideClone();

      cancel();
    },
    clone: function clone(_ref3) {
      var sortable = _ref3.sortable,
          rootEl = _ref3.rootEl,
          dispatchSortableEvent = _ref3.dispatchSortableEvent,
          cancel = _ref3.cancel;
      if (!this.isMultiDrag) return;

      if (!this.options.removeCloneOnHide) {
        if (multiDragElements.length && multiDragSortable === sortable) {
          insertMultiDragClones(true, rootEl);
          dispatchSortableEvent('clone');
          cancel();
        }
      }
    },
    showClone: function showClone(_ref4) {
      var cloneNowShown = _ref4.cloneNowShown,
          rootEl = _ref4.rootEl,
          cancel = _ref4.cancel;
      if (!this.isMultiDrag) return;
      insertMultiDragClones(false, rootEl);
      multiDragClones.forEach(function (clone) {
        css(clone, 'display', '');
      });
      cloneNowShown();
      clonesHidden = false;
      cancel();
    },
    hideClone: function hideClone(_ref5) {
      var _this = this;

      var sortable = _ref5.sortable,
          cloneNowHidden = _ref5.cloneNowHidden,
          cancel = _ref5.cancel;
      if (!this.isMultiDrag) return;
      multiDragClones.forEach(function (clone) {
        css(clone, 'display', 'none');

        if (_this.options.removeCloneOnHide && clone.parentNode) {
          clone.parentNode.removeChild(clone);
        }
      });
      cloneNowHidden();
      clonesHidden = true;
      cancel();
    },
    dragStartGlobal: function dragStartGlobal(_ref6) {
      var sortable = _ref6.sortable;

      if (!this.isMultiDrag && multiDragSortable) {
        multiDragSortable.multiDrag._deselectMultiDrag();
      }

      multiDragElements.forEach(function (multiDragElement) {
        multiDragElement.sortableIndex = index(multiDragElement);
      }); // Sort multi-drag elements

      multiDragElements = multiDragElements.sort(function (a, b) {
        return a.sortableIndex - b.sortableIndex;
      });
      dragStarted = true;
    },
    dragStarted: function dragStarted(_ref7) {
      var _this2 = this;

      var sortable = _ref7.sortable;
      if (!this.isMultiDrag) return;

      if (this.options.sort) {
        // Capture rects,
        // hide multi drag elements (by positioning them absolute),
        // set multi drag elements rects to dragRect,
        // show multi drag elements,
        // animate to rects,
        // unset rects & remove from DOM
        sortable.captureAnimationState();

        if (this.options.animation) {
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            css(multiDragElement, 'position', 'absolute');
          });
          var dragRect = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRect);
          });
          folding = true;
          initialFolding = true;
        }
      }

      sortable.animateAll(function () {
        folding = false;
        initialFolding = false;

        if (_this2.options.animation) {
          multiDragElements.forEach(function (multiDragElement) {
            unsetRect(multiDragElement);
          });
        } // Remove all auxiliary multidrag items from el, if sorting enabled


        if (_this2.options.sort) {
          removeMultiDragElements();
        }
      });
    },
    dragOver: function dragOver(_ref8) {
      var target = _ref8.target,
          completed = _ref8.completed,
          cancel = _ref8.cancel;

      if (folding && ~multiDragElements.indexOf(target)) {
        completed(false);
        cancel();
      }
    },
    revert: function revert(_ref9) {
      var fromSortable = _ref9.fromSortable,
          rootEl = _ref9.rootEl,
          sortable = _ref9.sortable,
          dragRect = _ref9.dragRect;

      if (multiDragElements.length > 1) {
        // Setup unfold animation
        multiDragElements.forEach(function (multiDragElement) {
          sortable.addAnimationState({
            target: multiDragElement,
            rect: folding ? getRect(multiDragElement) : dragRect
          });
          unsetRect(multiDragElement);
          multiDragElement.fromRect = dragRect;
          fromSortable.removeAnimationState(multiDragElement);
        });
        folding = false;
        insertMultiDragElements(!this.options.removeCloneOnHide, rootEl);
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref10) {
      var sortable = _ref10.sortable,
          isOwner = _ref10.isOwner,
          insertion = _ref10.insertion,
          activeSortable = _ref10.activeSortable,
          parentEl = _ref10.parentEl,
          putSortable = _ref10.putSortable;
      var options = this.options;

      if (insertion) {
        // Clones must be hidden before folding animation to capture dragRectAbsolute properly
        if (isOwner) {
          activeSortable._hideClone();
        }

        initialFolding = false; // If leaving sort:false root, or already folding - Fold to new location

        if (options.animation && multiDragElements.length > 1 && (folding || !isOwner && !activeSortable.options.sort && !putSortable)) {
          // Fold: Set all multi drag elements's rects to dragEl's rect when multi-drag elements are invisible
          var dragRectAbsolute = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRectAbsolute); // Move element(s) to end of parentEl so that it does not interfere with multi-drag clones insertion if they are inserted
            // while folding, and so that we can capture them again because old sortable will no longer be fromSortable

            parentEl.appendChild(multiDragElement);
          });
          folding = true;
        } // Clones must be shown (and check to remove multi drags) after folding when interfering multiDragElements are moved out


        if (!isOwner) {
          // Only remove if not folding (folding will remove them anyways)
          if (!folding) {
            removeMultiDragElements();
          }

          if (multiDragElements.length > 1) {
            var clonesHiddenBefore = clonesHidden;

            activeSortable._showClone(sortable); // Unfold animation for clones if showing from hidden


            if (activeSortable.options.animation && !clonesHidden && clonesHiddenBefore) {
              multiDragClones.forEach(function (clone) {
                activeSortable.addAnimationState({
                  target: clone,
                  rect: clonesFromRect
                });
                clone.fromRect = clonesFromRect;
                clone.thisAnimationDuration = null;
              });
            }
          } else {
            activeSortable._showClone(sortable);
          }
        }
      }
    },
    dragOverAnimationCapture: function dragOverAnimationCapture(_ref11) {
      var dragRect = _ref11.dragRect,
          isOwner = _ref11.isOwner,
          activeSortable = _ref11.activeSortable;
      multiDragElements.forEach(function (multiDragElement) {
        multiDragElement.thisAnimationDuration = null;
      });

      if (activeSortable.options.animation && !isOwner && activeSortable.multiDrag.isMultiDrag) {
        clonesFromRect = _extends({}, dragRect);
        var dragMatrix = matrix(dragEl$1, true);
        clonesFromRect.top -= dragMatrix.f;
        clonesFromRect.left -= dragMatrix.e;
      }
    },
    dragOverAnimationComplete: function dragOverAnimationComplete() {
      if (folding) {
        folding = false;
        removeMultiDragElements();
      }
    },
    drop: function drop(_ref12) {
      var evt = _ref12.originalEvent,
          rootEl = _ref12.rootEl,
          parentEl = _ref12.parentEl,
          sortable = _ref12.sortable,
          dispatchSortableEvent = _ref12.dispatchSortableEvent,
          oldIndex = _ref12.oldIndex,
          putSortable = _ref12.putSortable;
      var toSortable = putSortable || this.sortable;
      if (!evt) return;
      var options = this.options,
          children = parentEl.children; // Multi-drag selection

      if (!dragStarted) {
        if (options.multiDragKey && !this.multiDragKeyDown) {
          this._deselectMultiDrag();
        }

        toggleClass(dragEl$1, options.selectedClass, !~multiDragElements.indexOf(dragEl$1));

        if (!~multiDragElements.indexOf(dragEl$1)) {
          multiDragElements.push(dragEl$1);
          dispatchEvent({
            sortable: sortable,
            rootEl: rootEl,
            name: 'select',
            targetEl: dragEl$1,
            originalEvt: evt
          }); // Modifier activated, select from last to dragEl

          if (evt.shiftKey && lastMultiDragSelect && sortable.el.contains(lastMultiDragSelect)) {
            var lastIndex = index(lastMultiDragSelect),
                currentIndex = index(dragEl$1);

            if (~lastIndex && ~currentIndex && lastIndex !== currentIndex) {
              // Must include lastMultiDragSelect (select it), in case modified selection from no selection
              // (but previous selection existed)
              var n, i;

              if (currentIndex > lastIndex) {
                i = lastIndex;
                n = currentIndex;
              } else {
                i = currentIndex;
                n = lastIndex + 1;
              }

              for (; i < n; i++) {
                if (~multiDragElements.indexOf(children[i])) continue;
                toggleClass(children[i], options.selectedClass, true);
                multiDragElements.push(children[i]);
                dispatchEvent({
                  sortable: sortable,
                  rootEl: rootEl,
                  name: 'select',
                  targetEl: children[i],
                  originalEvt: evt
                });
              }
            }
          } else {
            lastMultiDragSelect = dragEl$1;
          }

          multiDragSortable = toSortable;
        } else {
          multiDragElements.splice(multiDragElements.indexOf(dragEl$1), 1);
          lastMultiDragSelect = null;
          dispatchEvent({
            sortable: sortable,
            rootEl: rootEl,
            name: 'deselect',
            targetEl: dragEl$1,
            originalEvt: evt
          });
        }
      } // Multi-drag drop


      if (dragStarted && this.isMultiDrag) {
        // Do not "unfold" after around dragEl if reverted
        if ((parentEl[expando].options.sort || parentEl !== rootEl) && multiDragElements.length > 1) {
          var dragRect = getRect(dragEl$1),
              multiDragIndex = index(dragEl$1, ':not(.' + this.options.selectedClass + ')');
          if (!initialFolding && options.animation) dragEl$1.thisAnimationDuration = null;
          toSortable.captureAnimationState();

          if (!initialFolding) {
            if (options.animation) {
              dragEl$1.fromRect = dragRect;
              multiDragElements.forEach(function (multiDragElement) {
                multiDragElement.thisAnimationDuration = null;

                if (multiDragElement !== dragEl$1) {
                  var rect = folding ? getRect(multiDragElement) : dragRect;
                  multiDragElement.fromRect = rect; // Prepare unfold animation

                  toSortable.addAnimationState({
                    target: multiDragElement,
                    rect: rect
                  });
                }
              });
            } // Multi drag elements are not necessarily removed from the DOM on drop, so to reinsert
            // properly they must all be removed


            removeMultiDragElements();
            multiDragElements.forEach(function (multiDragElement) {
              if (children[multiDragIndex]) {
                parentEl.insertBefore(multiDragElement, children[multiDragIndex]);
              } else {
                parentEl.appendChild(multiDragElement);
              }

              multiDragIndex++;
            }); // If initial folding is done, the elements may have changed position because they are now
            // unfolding around dragEl, even though dragEl may not have his index changed, so update event
            // must be fired here as Sortable will not.

            if (oldIndex === index(dragEl$1)) {
              var update = false;
              multiDragElements.forEach(function (multiDragElement) {
                if (multiDragElement.sortableIndex !== index(multiDragElement)) {
                  update = true;
                  return;
                }
              });

              if (update) {
                dispatchSortableEvent('update');
              }
            }
          } // Must be done after capturing individual rects (scroll bar)


          multiDragElements.forEach(function (multiDragElement) {
            unsetRect(multiDragElement);
          });
          toSortable.animateAll();
        }

        multiDragSortable = toSortable;
      } // Remove clones if necessary


      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
        multiDragClones.forEach(function (clone) {
          clone.parentNode && clone.parentNode.removeChild(clone);
        });
      }
    },
    nullingGlobal: function nullingGlobal() {
      this.isMultiDrag = dragStarted = false;
      multiDragClones.length = 0;
    },
    destroyGlobal: function destroyGlobal() {
      this._deselectMultiDrag();

      off(document, 'pointerup', this._deselectMultiDrag);
      off(document, 'mouseup', this._deselectMultiDrag);
      off(document, 'touchend', this._deselectMultiDrag);
      off(document, 'keydown', this._checkKeyDown);
      off(document, 'keyup', this._checkKeyUp);
    },
    _deselectMultiDrag: function _deselectMultiDrag(evt) {
      if (typeof dragStarted !== "undefined" && dragStarted) return; // Only deselect if selection is in this sortable

      if (multiDragSortable !== this.sortable) return; // Only deselect if target is not item in this sortable

      if (evt && closest(evt.target, this.options.draggable, this.sortable.el, false)) return; // Only deselect if left click

      if (evt && evt.button !== 0) return;

      while (multiDragElements.length) {
        var el = multiDragElements[0];
        toggleClass(el, this.options.selectedClass, false);
        multiDragElements.shift();
        dispatchEvent({
          sortable: this.sortable,
          rootEl: this.sortable.el,
          name: 'deselect',
          targetEl: el,
          originalEvt: evt
        });
      }
    },
    _checkKeyDown: function _checkKeyDown(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = true;
      }
    },
    _checkKeyUp: function _checkKeyUp(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = false;
      }
    }
  };
  return _extends(MultiDrag, {
    // Static methods & properties
    pluginName: 'multiDrag',
    utils: {
      /**
       * Selects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be selected
       */
      select: function select(el) {
        var sortable = el.parentNode[expando];
        if (!sortable || !sortable.options.multiDrag || ~multiDragElements.indexOf(el)) return;

        if (multiDragSortable && multiDragSortable !== sortable) {
          multiDragSortable.multiDrag._deselectMultiDrag();

          multiDragSortable = sortable;
        }

        toggleClass(el, sortable.options.selectedClass, true);
        multiDragElements.push(el);
      },

      /**
       * Deselects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be deselected
       */
      deselect: function deselect(el) {
        var sortable = el.parentNode[expando],
            index = multiDragElements.indexOf(el);
        if (!sortable || !sortable.options.multiDrag || !~index) return;
        toggleClass(el, sortable.options.selectedClass, false);
        multiDragElements.splice(index, 1);
      }
    },
    eventProperties: function eventProperties() {
      var _this3 = this;

      var oldIndicies = [],
          newIndicies = [];
      multiDragElements.forEach(function (multiDragElement) {
        oldIndicies.push({
          multiDragElement: multiDragElement,
          index: multiDragElement.sortableIndex
        }); // multiDragElements will already be sorted if folding

        var newIndex;

        if (folding && multiDragElement !== dragEl$1) {
          newIndex = -1;
        } else if (folding) {
          newIndex = index(multiDragElement, ':not(.' + _this3.options.selectedClass + ')');
        } else {
          newIndex = index(multiDragElement);
        }

        newIndicies.push({
          multiDragElement: multiDragElement,
          index: newIndex
        });
      });
      return {
        items: _toConsumableArray(multiDragElements),
        clones: [].concat(multiDragClones),
        oldIndicies: oldIndicies,
        newIndicies: newIndicies
      };
    },
    optionListeners: {
      multiDragKey: function multiDragKey(key) {
        key = key.toLowerCase();

        if (key === 'ctrl') {
          key = 'Control';
        } else if (key.length > 1) {
          key = key.charAt(0).toUpperCase() + key.substr(1);
        }

        return key;
      }
    }
  });
}

function insertMultiDragElements(clonesInserted, rootEl) {
  multiDragElements.forEach(function (multiDragElement, i) {
    var target = rootEl.children[multiDragElement.sortableIndex + (clonesInserted ? Number(i) : 0)];

    if (target) {
      rootEl.insertBefore(multiDragElement, target);
    } else {
      rootEl.appendChild(multiDragElement);
    }
  });
}
/**
 * Insert multi-drag clones
 * @param  {[Boolean]} elementsInserted  Whether the multi-drag elements are inserted
 * @param  {HTMLElement} rootEl
 */


function insertMultiDragClones(elementsInserted, rootEl) {
  multiDragClones.forEach(function (clone, i) {
    var target = rootEl.children[clone.sortableIndex + (elementsInserted ? Number(i) : 0)];

    if (target) {
      rootEl.insertBefore(clone, target);
    } else {
      rootEl.appendChild(clone);
    }
  });
}

function removeMultiDragElements() {
  multiDragElements.forEach(function (multiDragElement) {
    if (multiDragElement === dragEl$1) return;
    multiDragElement.parentNode && multiDragElement.parentNode.removeChild(multiDragElement);
  });
}

Sortable.mount(new AutoScrollPlugin());
Sortable.mount(Remove, Revert);
/* harmony default export */ __webpack_exports__["default"] = (Sortable);


/***/ }),

/***/ "./node_modules/@concretecms/bedrock/node_modules/vuedraggable/dist/vuedraggable.umd.js":
/*!**********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/node_modules/vuedraggable/dist/vuedraggable.umd.js ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

(function webpackUniversalModuleDefinition(root, factory) {
  if (( false ? undefined : _typeof(exports)) === 'object' && ( false ? undefined : _typeof(module)) === 'object') module.exports = factory(__webpack_require__(/*! sortablejs */ "./node_modules/@concretecms/bedrock/node_modules/sortablejs/modular/sortable.esm.js"));else if (true) !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! sortablejs */ "./node_modules/@concretecms/bedrock/node_modules/sortablejs/modular/sortable.esm.js")], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else {}
})(typeof self !== 'undefined' ? self : this, function (__WEBPACK_EXTERNAL_MODULE_a352__) {
  return (
    /******/
    function (modules) {
      // webpackBootstrap

      /******/
      // The module cache

      /******/
      var installedModules = {};
      /******/

      /******/
      // The require function

      /******/

      function __webpack_require__(moduleId) {
        /******/

        /******/
        // Check if module is in cache

        /******/
        if (installedModules[moduleId]) {
          /******/
          return installedModules[moduleId].exports;
          /******/
        }
        /******/
        // Create a new module (and put it into the cache)

        /******/


        var module = installedModules[moduleId] = {
          /******/
          i: moduleId,

          /******/
          l: false,

          /******/
          exports: {}
          /******/

        };
        /******/

        /******/
        // Execute the module function

        /******/

        modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
        /******/

        /******/
        // Flag the module as loaded

        /******/

        module.l = true;
        /******/

        /******/
        // Return the exports of the module

        /******/

        return module.exports;
        /******/
      }
      /******/

      /******/

      /******/
      // expose the modules object (__webpack_modules__)

      /******/


      __webpack_require__.m = modules;
      /******/

      /******/
      // expose the module cache

      /******/

      __webpack_require__.c = installedModules;
      /******/

      /******/
      // define getter function for harmony exports

      /******/

      __webpack_require__.d = function (exports, name, getter) {
        /******/
        if (!__webpack_require__.o(exports, name)) {
          /******/
          Object.defineProperty(exports, name, {
            enumerable: true,
            get: getter
          });
          /******/
        }
        /******/

      };
      /******/

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

      /******/
      // create a fake namespace object

      /******/
      // mode & 1: value is a module id, require it

      /******/
      // mode & 2: merge all properties of value into the ns

      /******/
      // mode & 4: return value when already ns object

      /******/
      // mode & 8|1: behave like require

      /******/


      __webpack_require__.t = function (value, mode) {
        /******/
        if (mode & 1) value = __webpack_require__(value);
        /******/

        if (mode & 8) return value;
        /******/

        if (mode & 4 && _typeof(value) === 'object' && value && value.__esModule) return value;
        /******/

        var ns = Object.create(null);
        /******/

        __webpack_require__.r(ns);
        /******/


        Object.defineProperty(ns, 'default', {
          enumerable: true,
          value: value
        });
        /******/

        if (mode & 2 && typeof value != 'string') for (var key in value) {
          __webpack_require__.d(ns, key, function (key) {
            return value[key];
          }.bind(null, key));
        }
        /******/

        return ns;
        /******/
      };
      /******/

      /******/
      // getDefaultExport function for compatibility with non-harmony modules

      /******/


      __webpack_require__.n = function (module) {
        /******/
        var getter = module && module.__esModule ?
        /******/
        function getDefault() {
          return module['default'];
        } :
        /******/
        function getModuleExports() {
          return module;
        };
        /******/

        __webpack_require__.d(getter, 'a', getter);
        /******/


        return getter;
        /******/
      };
      /******/

      /******/
      // Object.prototype.hasOwnProperty.call

      /******/


      __webpack_require__.o = function (object, property) {
        return Object.prototype.hasOwnProperty.call(object, property);
      };
      /******/

      /******/
      // __webpack_public_path__

      /******/


      __webpack_require__.p = "";
      /******/

      /******/

      /******/
      // Load entry module and return exports

      /******/

      return __webpack_require__(__webpack_require__.s = "fb15");
      /******/
    }(
    /************************************************************************/

    /******/
    {
      /***/
      "01f9":
      /***/
      function f9(module, exports, __webpack_require__) {
        "use strict";

        var LIBRARY = __webpack_require__("2d00");

        var $export = __webpack_require__("5ca1");

        var redefine = __webpack_require__("2aba");

        var hide = __webpack_require__("32e9");

        var Iterators = __webpack_require__("84f2");

        var $iterCreate = __webpack_require__("41a0");

        var setToStringTag = __webpack_require__("7f20");

        var getPrototypeOf = __webpack_require__("38fd");

        var ITERATOR = __webpack_require__("2b4c")('iterator');

        var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`

        var FF_ITERATOR = '@@iterator';
        var KEYS = 'keys';
        var VALUES = 'values';

        var returnThis = function returnThis() {
          return this;
        };

        module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
          $iterCreate(Constructor, NAME, next);

          var getMethod = function getMethod(kind) {
            if (!BUGGY && kind in proto) return proto[kind];

            switch (kind) {
              case KEYS:
                return function keys() {
                  return new Constructor(this, kind);
                };

              case VALUES:
                return function values() {
                  return new Constructor(this, kind);
                };
            }

            return function entries() {
              return new Constructor(this, kind);
            };
          };

          var TAG = NAME + ' Iterator';
          var DEF_VALUES = DEFAULT == VALUES;
          var VALUES_BUG = false;
          var proto = Base.prototype;
          var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
          var $default = $native || getMethod(DEFAULT);
          var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
          var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
          var methods, key, IteratorPrototype; // Fix native

          if ($anyNative) {
            IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));

            if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
              // Set @@toStringTag to native iterators
              setToStringTag(IteratorPrototype, TAG, true); // fix for some old engines

              if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
            }
          } // fix Array#{values, @@iterator}.name in V8 / FF


          if (DEF_VALUES && $native && $native.name !== VALUES) {
            VALUES_BUG = true;

            $default = function values() {
              return $native.call(this);
            };
          } // Define iterator


          if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
            hide(proto, ITERATOR, $default);
          } // Plug for library


          Iterators[NAME] = $default;
          Iterators[TAG] = returnThis;

          if (DEFAULT) {
            methods = {
              values: DEF_VALUES ? $default : getMethod(VALUES),
              keys: IS_SET ? $default : getMethod(KEYS),
              entries: $entries
            };
            if (FORCED) for (key in methods) {
              if (!(key in proto)) redefine(proto, key, methods[key]);
            } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
          }

          return methods;
        };
        /***/

      },

      /***/
      "02f4":
      /***/
      function f4(module, exports, __webpack_require__) {
        var toInteger = __webpack_require__("4588");

        var defined = __webpack_require__("be13"); // true  -> String#at
        // false -> String#codePointAt


        module.exports = function (TO_STRING) {
          return function (that, pos) {
            var s = String(defined(that));
            var i = toInteger(pos);
            var l = s.length;
            var a, b;
            if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
            a = s.charCodeAt(i);
            return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff ? TO_STRING ? s.charAt(i) : a : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
          };
        };
        /***/

      },

      /***/
      "0390":
      /***/
      function _(module, exports, __webpack_require__) {
        "use strict";

        var at = __webpack_require__("02f4")(true); // `AdvanceStringIndex` abstract operation
        // https://tc39.github.io/ecma262/#sec-advancestringindex


        module.exports = function (S, index, unicode) {
          return index + (unicode ? at(S, index).length : 1);
        };
        /***/

      },

      /***/
      "0bfb":
      /***/
      function bfb(module, exports, __webpack_require__) {
        "use strict"; // 21.2.5.3 get RegExp.prototype.flags

        var anObject = __webpack_require__("cb7c");

        module.exports = function () {
          var that = anObject(this);
          var result = '';
          if (that.global) result += 'g';
          if (that.ignoreCase) result += 'i';
          if (that.multiline) result += 'm';
          if (that.unicode) result += 'u';
          if (that.sticky) result += 'y';
          return result;
        };
        /***/

      },

      /***/
      "0d58":
      /***/
      function d58(module, exports, __webpack_require__) {
        // 19.1.2.14 / 15.2.3.14 Object.keys(O)
        var $keys = __webpack_require__("ce10");

        var enumBugKeys = __webpack_require__("e11e");

        module.exports = Object.keys || function keys(O) {
          return $keys(O, enumBugKeys);
        };
        /***/

      },

      /***/
      "1495":
      /***/
      function _(module, exports, __webpack_require__) {
        var dP = __webpack_require__("86cc");

        var anObject = __webpack_require__("cb7c");

        var getKeys = __webpack_require__("0d58");

        module.exports = __webpack_require__("9e1e") ? Object.defineProperties : function defineProperties(O, Properties) {
          anObject(O);
          var keys = getKeys(Properties);
          var length = keys.length;
          var i = 0;
          var P;

          while (length > i) {
            dP.f(O, P = keys[i++], Properties[P]);
          }

          return O;
        };
        /***/
      },

      /***/
      "214f":
      /***/
      function f(module, exports, __webpack_require__) {
        "use strict";

        __webpack_require__("b0c5");

        var redefine = __webpack_require__("2aba");

        var hide = __webpack_require__("32e9");

        var fails = __webpack_require__("79e5");

        var defined = __webpack_require__("be13");

        var wks = __webpack_require__("2b4c");

        var regexpExec = __webpack_require__("520a");

        var SPECIES = wks('species');
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
        });

        var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = function () {
          // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
          var re = /(?:)/;
          var originalExec = re.exec;

          re.exec = function () {
            return originalExec.apply(this, arguments);
          };

          var result = 'ab'.split(re);
          return result.length === 2 && result[0] === 'a' && result[1] === 'b';
        }();

        module.exports = function (KEY, length, exec) {
          var SYMBOL = wks(KEY);
          var DELEGATES_TO_SYMBOL = !fails(function () {
            // String methods call symbol-named RegEp methods
            var O = {};

            O[SYMBOL] = function () {
              return 7;
            };

            return ''[KEY](O) != 7;
          });
          var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL ? !fails(function () {
            // Symbol-named RegExp methods call .exec
            var execCalled = false;
            var re = /a/;

            re.exec = function () {
              execCalled = true;
              return null;
            };

            if (KEY === 'split') {
              // RegExp[@@split] doesn't call the regex's exec method, but first creates
              // a new one. We need to return the patched regex when creating the new one.
              re.constructor = {};

              re.constructor[SPECIES] = function () {
                return re;
              };
            }

            re[SYMBOL]('');
            return !execCalled;
          }) : undefined;

          if (!DELEGATES_TO_SYMBOL || !DELEGATES_TO_EXEC || KEY === 'replace' && !REPLACE_SUPPORTS_NAMED_GROUPS || KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC) {
            var nativeRegExpMethod = /./[SYMBOL];
            var fns = exec(defined, SYMBOL, ''[KEY], function maybeCallNative(nativeMethod, regexp, str, arg2, forceStringMethod) {
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
            });
            var strfn = fns[0];
            var rxfn = fns[1];
            redefine(String.prototype, KEY, strfn);
            hide(RegExp.prototype, SYMBOL, length == 2 // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
            // 21.2.5.11 RegExp.prototype[@@split](string, limit)
            ? function (string, arg) {
              return rxfn.call(string, this, arg);
            } // 21.2.5.6 RegExp.prototype[@@match](string)
            // 21.2.5.9 RegExp.prototype[@@search](string)
            : function (string) {
              return rxfn.call(string, this);
            });
          }
        };
        /***/

      },

      /***/
      "230e":
      /***/
      function e(module, exports, __webpack_require__) {
        var isObject = __webpack_require__("d3f4");

        var document = __webpack_require__("7726").document; // typeof document.createElement is 'object' in old IE


        var is = isObject(document) && isObject(document.createElement);

        module.exports = function (it) {
          return is ? document.createElement(it) : {};
        };
        /***/

      },

      /***/
      "23c6":
      /***/
      function c6(module, exports, __webpack_require__) {
        // getting tag from 19.1.3.6 Object.prototype.toString()
        var cof = __webpack_require__("2d95");

        var TAG = __webpack_require__("2b4c")('toStringTag'); // ES3 wrong here


        var ARG = cof(function () {
          return arguments;
        }()) == 'Arguments'; // fallback for IE11 Script Access Denied error

        var tryGet = function tryGet(it, key) {
          try {
            return it[key];
          } catch (e) {
            /* empty */
          }
        };

        module.exports = function (it) {
          var O, T, B;
          return it === undefined ? 'Undefined' : it === null ? 'Null' // @@toStringTag case
          : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T // builtinTag case
          : ARG ? cof(O) // ES3 arguments fallback
          : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
        };
        /***/

      },

      /***/
      "2621":
      /***/
      function _(module, exports) {
        exports.f = Object.getOwnPropertySymbols;
        /***/
      },

      /***/
      "2aba":
      /***/
      function aba(module, exports, __webpack_require__) {
        var global = __webpack_require__("7726");

        var hide = __webpack_require__("32e9");

        var has = __webpack_require__("69a8");

        var SRC = __webpack_require__("ca5a")('src');

        var $toString = __webpack_require__("fa5b");

        var TO_STRING = 'toString';
        var TPL = ('' + $toString).split(TO_STRING);

        __webpack_require__("8378").inspectSource = function (it) {
          return $toString.call(it);
        };

        (module.exports = function (O, key, val, safe) {
          var isFunction = typeof val == 'function';
          if (isFunction) has(val, 'name') || hide(val, 'name', key);
          if (O[key] === val) return;
          if (isFunction) has(val, SRC) || hide(val, SRC, O[key] ? '' + O[key] : TPL.join(String(key)));

          if (O === global) {
            O[key] = val;
          } else if (!safe) {
            delete O[key];
            hide(O, key, val);
          } else if (O[key]) {
            O[key] = val;
          } else {
            hide(O, key, val);
          } // add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative

        })(Function.prototype, TO_STRING, function toString() {
          return typeof this == 'function' && this[SRC] || $toString.call(this);
        });
        /***/
      },

      /***/
      "2aeb":
      /***/
      function aeb(module, exports, __webpack_require__) {
        // 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
        var anObject = __webpack_require__("cb7c");

        var dPs = __webpack_require__("1495");

        var enumBugKeys = __webpack_require__("e11e");

        var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

        var Empty = function Empty() {
          /* empty */
        };

        var PROTOTYPE = 'prototype'; // Create object with fake `null` prototype: use iframe Object with cleared prototype

        var _createDict = function createDict() {
          // Thrash, waste and sodomy: IE GC bug
          var iframe = __webpack_require__("230e")('iframe');

          var i = enumBugKeys.length;
          var lt = '<';
          var gt = '>';
          var iframeDocument;
          iframe.style.display = 'none';

          __webpack_require__("fab2").appendChild(iframe);

          iframe.src = 'javascript:'; // eslint-disable-line no-script-url
          // createDict = iframe.contentWindow.Object;
          // html.removeChild(iframe);

          iframeDocument = iframe.contentWindow.document;
          iframeDocument.open();
          iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
          iframeDocument.close();
          _createDict = iframeDocument.F;

          while (i--) {
            delete _createDict[PROTOTYPE][enumBugKeys[i]];
          }

          return _createDict();
        };

        module.exports = Object.create || function create(O, Properties) {
          var result;

          if (O !== null) {
            Empty[PROTOTYPE] = anObject(O);
            result = new Empty();
            Empty[PROTOTYPE] = null; // add "__proto__" for Object.getPrototypeOf polyfill

            result[IE_PROTO] = O;
          } else result = _createDict();

          return Properties === undefined ? result : dPs(result, Properties);
        };
        /***/

      },

      /***/
      "2b4c":
      /***/
      function b4c(module, exports, __webpack_require__) {
        var store = __webpack_require__("5537")('wks');

        var uid = __webpack_require__("ca5a");

        var _Symbol = __webpack_require__("7726").Symbol;

        var USE_SYMBOL = typeof _Symbol == 'function';

        var $exports = module.exports = function (name) {
          return store[name] || (store[name] = USE_SYMBOL && _Symbol[name] || (USE_SYMBOL ? _Symbol : uid)('Symbol.' + name));
        };

        $exports.store = store;
        /***/
      },

      /***/
      "2d00":
      /***/
      function d00(module, exports) {
        module.exports = false;
        /***/
      },

      /***/
      "2d95":
      /***/
      function d95(module, exports) {
        var toString = {}.toString;

        module.exports = function (it) {
          return toString.call(it).slice(8, -1);
        };
        /***/

      },

      /***/
      "2fdb":
      /***/
      function fdb(module, exports, __webpack_require__) {
        "use strict"; // 21.1.3.7 String.prototype.includes(searchString, position = 0)

        var $export = __webpack_require__("5ca1");

        var context = __webpack_require__("d2c8");

        var INCLUDES = 'includes';
        $export($export.P + $export.F * __webpack_require__("5147")(INCLUDES), 'String', {
          includes: function includes(searchString
          /* , position = 0 */
          ) {
            return !!~context(this, searchString, INCLUDES).indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
          }
        });
        /***/
      },

      /***/
      "32e9":
      /***/
      function e9(module, exports, __webpack_require__) {
        var dP = __webpack_require__("86cc");

        var createDesc = __webpack_require__("4630");

        module.exports = __webpack_require__("9e1e") ? function (object, key, value) {
          return dP.f(object, key, createDesc(1, value));
        } : function (object, key, value) {
          object[key] = value;
          return object;
        };
        /***/
      },

      /***/
      "38fd":
      /***/
      function fd(module, exports, __webpack_require__) {
        // 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
        var has = __webpack_require__("69a8");

        var toObject = __webpack_require__("4bf8");

        var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

        var ObjectProto = Object.prototype;

        module.exports = Object.getPrototypeOf || function (O) {
          O = toObject(O);
          if (has(O, IE_PROTO)) return O[IE_PROTO];

          if (typeof O.constructor == 'function' && O instanceof O.constructor) {
            return O.constructor.prototype;
          }

          return O instanceof Object ? ObjectProto : null;
        };
        /***/

      },

      /***/
      "41a0":
      /***/
      function a0(module, exports, __webpack_require__) {
        "use strict";

        var create = __webpack_require__("2aeb");

        var descriptor = __webpack_require__("4630");

        var setToStringTag = __webpack_require__("7f20");

        var IteratorPrototype = {}; // 25.1.2.1.1 %IteratorPrototype%[@@iterator]()

        __webpack_require__("32e9")(IteratorPrototype, __webpack_require__("2b4c")('iterator'), function () {
          return this;
        });

        module.exports = function (Constructor, NAME, next) {
          Constructor.prototype = create(IteratorPrototype, {
            next: descriptor(1, next)
          });
          setToStringTag(Constructor, NAME + ' Iterator');
        };
        /***/

      },

      /***/
      "456d":
      /***/
      function d(module, exports, __webpack_require__) {
        // 19.1.2.14 Object.keys(O)
        var toObject = __webpack_require__("4bf8");

        var $keys = __webpack_require__("0d58");

        __webpack_require__("5eda")('keys', function () {
          return function keys(it) {
            return $keys(toObject(it));
          };
        });
        /***/

      },

      /***/
      "4588":
      /***/
      function _(module, exports) {
        // 7.1.4 ToInteger
        var ceil = Math.ceil;
        var floor = Math.floor;

        module.exports = function (it) {
          return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
        };
        /***/

      },

      /***/
      "4630":
      /***/
      function _(module, exports) {
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
      "4bf8":
      /***/
      function bf8(module, exports, __webpack_require__) {
        // 7.1.13 ToObject(argument)
        var defined = __webpack_require__("be13");

        module.exports = function (it) {
          return Object(defined(it));
        };
        /***/

      },

      /***/
      "5147":
      /***/
      function _(module, exports, __webpack_require__) {
        var MATCH = __webpack_require__("2b4c")('match');

        module.exports = function (KEY) {
          var re = /./;

          try {
            '/./'[KEY](re);
          } catch (e) {
            try {
              re[MATCH] = false;
              return !'/./'[KEY](re);
            } catch (f) {
              /* empty */
            }
          }

          return true;
        };
        /***/

      },

      /***/
      "520a":
      /***/
      function a(module, exports, __webpack_require__) {
        "use strict";

        var regexpFlags = __webpack_require__("0bfb");

        var nativeExec = RegExp.prototype.exec; // This always refers to the native implementation, because the
        // String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
        // which loads this file before patching the method.

        var nativeReplace = String.prototype.replace;
        var patchedExec = nativeExec;
        var LAST_INDEX = 'lastIndex';

        var UPDATES_LAST_INDEX_WRONG = function () {
          var re1 = /a/,
              re2 = /b*/g;
          nativeExec.call(re1, 'a');
          nativeExec.call(re2, 'a');
          return re1[LAST_INDEX] !== 0 || re2[LAST_INDEX] !== 0;
        }(); // nonparticipating capturing group, copied from es5-shim's String#split patch.


        var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;
        var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED;

        if (PATCH) {
          patchedExec = function exec(str) {
            var re = this;
            var lastIndex, reCopy, match, i;

            if (NPCG_INCLUDED) {
              reCopy = new RegExp('^' + re.source + '$(?!\\s)', regexpFlags.call(re));
            }

            if (UPDATES_LAST_INDEX_WRONG) lastIndex = re[LAST_INDEX];
            match = nativeExec.call(re, str);

            if (UPDATES_LAST_INDEX_WRONG && match) {
              re[LAST_INDEX] = re.global ? match.index + match[0].length : lastIndex;
            }

            if (NPCG_INCLUDED && match && match.length > 1) {
              // Fix browsers whose `exec` methods don't consistently return `undefined`
              // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
              // eslint-disable-next-line no-loop-func
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
      "52a7":
      /***/
      function a7(module, exports) {
        exports.f = {}.propertyIsEnumerable;
        /***/
      },

      /***/
      "5537":
      /***/
      function _(module, exports, __webpack_require__) {
        var core = __webpack_require__("8378");

        var global = __webpack_require__("7726");

        var SHARED = '__core-js_shared__';
        var store = global[SHARED] || (global[SHARED] = {});
        (module.exports = function (key, value) {
          return store[key] || (store[key] = value !== undefined ? value : {});
        })('versions', []).push({
          version: core.version,
          mode: __webpack_require__("2d00") ? 'pure' : 'global',
          copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
        });
        /***/
      },

      /***/
      "5ca1":
      /***/
      function ca1(module, exports, __webpack_require__) {
        var global = __webpack_require__("7726");

        var core = __webpack_require__("8378");

        var hide = __webpack_require__("32e9");

        var redefine = __webpack_require__("2aba");

        var ctx = __webpack_require__("9b43");

        var PROTOTYPE = 'prototype';

        var $export = function $export(type, name, source) {
          var IS_FORCED = type & $export.F;
          var IS_GLOBAL = type & $export.G;
          var IS_STATIC = type & $export.S;
          var IS_PROTO = type & $export.P;
          var IS_BIND = type & $export.B;
          var target = IS_GLOBAL ? global : IS_STATIC ? global[name] || (global[name] = {}) : (global[name] || {})[PROTOTYPE];
          var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
          var expProto = exports[PROTOTYPE] || (exports[PROTOTYPE] = {});
          var key, own, out, exp;
          if (IS_GLOBAL) source = name;

          for (key in source) {
            // contains in native
            own = !IS_FORCED && target && target[key] !== undefined; // export native or passed

            out = (own ? target : source)[key]; // bind timers to global for call from export context

            exp = IS_BIND && own ? ctx(out, global) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out; // extend global

            if (target) redefine(target, key, out, type & $export.U); // export

            if (exports[key] != out) hide(exports, key, exp);
            if (IS_PROTO && expProto[key] != out) expProto[key] = out;
          }
        };

        global.core = core; // type bitmap

        $export.F = 1; // forced

        $export.G = 2; // global

        $export.S = 4; // static

        $export.P = 8; // proto

        $export.B = 16; // bind

        $export.W = 32; // wrap

        $export.U = 64; // safe

        $export.R = 128; // real proto method for `library`

        module.exports = $export;
        /***/
      },

      /***/
      "5eda":
      /***/
      function eda(module, exports, __webpack_require__) {
        // most Object methods by ES6 should accept primitives
        var $export = __webpack_require__("5ca1");

        var core = __webpack_require__("8378");

        var fails = __webpack_require__("79e5");

        module.exports = function (KEY, exec) {
          var fn = (core.Object || {})[KEY] || Object[KEY];
          var exp = {};
          exp[KEY] = exec(fn);
          $export($export.S + $export.F * fails(function () {
            fn(1);
          }), 'Object', exp);
        };
        /***/

      },

      /***/
      "5f1b":
      /***/
      function f1b(module, exports, __webpack_require__) {
        "use strict";

        var classof = __webpack_require__("23c6");

        var builtinExec = RegExp.prototype.exec; // `RegExpExec` abstract operation
        // https://tc39.github.io/ecma262/#sec-regexpexec

        module.exports = function (R, S) {
          var exec = R.exec;

          if (typeof exec === 'function') {
            var result = exec.call(R, S);

            if (_typeof(result) !== 'object') {
              throw new TypeError('RegExp exec method returned something other than an Object or null');
            }

            return result;
          }

          if (classof(R) !== 'RegExp') {
            throw new TypeError('RegExp#exec called on incompatible receiver');
          }

          return builtinExec.call(R, S);
        };
        /***/

      },

      /***/
      "613b":
      /***/
      function b(module, exports, __webpack_require__) {
        var shared = __webpack_require__("5537")('keys');

        var uid = __webpack_require__("ca5a");

        module.exports = function (key) {
          return shared[key] || (shared[key] = uid(key));
        };
        /***/

      },

      /***/
      "626a":
      /***/
      function a(module, exports, __webpack_require__) {
        // fallback for non-array-like ES3 and non-enumerable old V8 strings
        var cof = __webpack_require__("2d95"); // eslint-disable-next-line no-prototype-builtins


        module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
          return cof(it) == 'String' ? it.split('') : Object(it);
        };
        /***/
      },

      /***/
      "6762":
      /***/
      function _(module, exports, __webpack_require__) {
        "use strict"; // https://github.com/tc39/Array.prototype.includes

        var $export = __webpack_require__("5ca1");

        var $includes = __webpack_require__("c366")(true);

        $export($export.P, 'Array', {
          includes: function includes(el
          /* , fromIndex = 0 */
          ) {
            return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
          }
        });

        __webpack_require__("9c6c")('includes');
        /***/

      },

      /***/
      "6821":
      /***/
      function _(module, exports, __webpack_require__) {
        // to indexed object, toObject with fallback for non-array-like ES3 strings
        var IObject = __webpack_require__("626a");

        var defined = __webpack_require__("be13");

        module.exports = function (it) {
          return IObject(defined(it));
        };
        /***/

      },

      /***/
      "69a8":
      /***/
      function a8(module, exports) {
        var hasOwnProperty = {}.hasOwnProperty;

        module.exports = function (it, key) {
          return hasOwnProperty.call(it, key);
        };
        /***/

      },

      /***/
      "6a99":
      /***/
      function a99(module, exports, __webpack_require__) {
        // 7.1.1 ToPrimitive(input [, PreferredType])
        var isObject = __webpack_require__("d3f4"); // instead of the ES6 spec version, we didn't implement @@toPrimitive case
        // and the second argument - flag - preferred type is a string


        module.exports = function (it, S) {
          if (!isObject(it)) return it;
          var fn, val;
          if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
          if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
          if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
          throw TypeError("Can't convert object to primitive value");
        };
        /***/

      },

      /***/
      "7333":
      /***/
      function _(module, exports, __webpack_require__) {
        "use strict"; // 19.1.2.1 Object.assign(target, source, ...)

        var getKeys = __webpack_require__("0d58");

        var gOPS = __webpack_require__("2621");

        var pIE = __webpack_require__("52a7");

        var toObject = __webpack_require__("4bf8");

        var IObject = __webpack_require__("626a");

        var $assign = Object.assign; // should work with symbols and should have deterministic property order (V8 bug)

        module.exports = !$assign || __webpack_require__("79e5")(function () {
          var A = {};
          var B = {}; // eslint-disable-next-line no-undef

          var S = Symbol();
          var K = 'abcdefghijklmnopqrst';
          A[S] = 7;
          K.split('').forEach(function (k) {
            B[k] = k;
          });
          return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
        }) ? function assign(target, source) {
          // eslint-disable-line no-unused-vars
          var T = toObject(target);
          var aLen = arguments.length;
          var index = 1;
          var getSymbols = gOPS.f;
          var isEnum = pIE.f;

          while (aLen > index) {
            var S = IObject(arguments[index++]);
            var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
            var length = keys.length;
            var j = 0;
            var key;

            while (length > j) {
              if (isEnum.call(S, key = keys[j++])) T[key] = S[key];
            }
          }

          return T;
        } : $assign;
        /***/
      },

      /***/
      "7726":
      /***/
      function _(module, exports) {
        // https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
        var global = module.exports = typeof window != 'undefined' && window.Math == Math ? window : typeof self != 'undefined' && self.Math == Math ? self // eslint-disable-next-line no-new-func
        : Function('return this')();
        if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef

        /***/
      },

      /***/
      "77f1":
      /***/
      function f1(module, exports, __webpack_require__) {
        var toInteger = __webpack_require__("4588");

        var max = Math.max;
        var min = Math.min;

        module.exports = function (index, length) {
          index = toInteger(index);
          return index < 0 ? max(index + length, 0) : min(index, length);
        };
        /***/

      },

      /***/
      "79e5":
      /***/
      function e5(module, exports) {
        module.exports = function (exec) {
          try {
            return !!exec();
          } catch (e) {
            return true;
          }
        };
        /***/

      },

      /***/
      "7f20":
      /***/
      function f20(module, exports, __webpack_require__) {
        var def = __webpack_require__("86cc").f;

        var has = __webpack_require__("69a8");

        var TAG = __webpack_require__("2b4c")('toStringTag');

        module.exports = function (it, tag, stat) {
          if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, {
            configurable: true,
            value: tag
          });
        };
        /***/

      },

      /***/
      "8378":
      /***/
      function _(module, exports) {
        var core = module.exports = {
          version: '2.6.5'
        };
        if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef

        /***/
      },

      /***/
      "84f2":
      /***/
      function f2(module, exports) {
        module.exports = {};
        /***/
      },

      /***/
      "86cc":
      /***/
      function cc(module, exports, __webpack_require__) {
        var anObject = __webpack_require__("cb7c");

        var IE8_DOM_DEFINE = __webpack_require__("c69a");

        var toPrimitive = __webpack_require__("6a99");

        var dP = Object.defineProperty;
        exports.f = __webpack_require__("9e1e") ? Object.defineProperty : function defineProperty(O, P, Attributes) {
          anObject(O);
          P = toPrimitive(P, true);
          anObject(Attributes);
          if (IE8_DOM_DEFINE) try {
            return dP(O, P, Attributes);
          } catch (e) {
            /* empty */
          }
          if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
          if ('value' in Attributes) O[P] = Attributes.value;
          return O;
        };
        /***/
      },

      /***/
      "9b43":
      /***/
      function b43(module, exports, __webpack_require__) {
        // optional / simple context binding
        var aFunction = __webpack_require__("d8e8");

        module.exports = function (fn, that, length) {
          aFunction(fn);
          if (that === undefined) return fn;

          switch (length) {
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
      "9c6c":
      /***/
      function c6c(module, exports, __webpack_require__) {
        // 22.1.3.31 Array.prototype[@@unscopables]
        var UNSCOPABLES = __webpack_require__("2b4c")('unscopables');

        var ArrayProto = Array.prototype;
        if (ArrayProto[UNSCOPABLES] == undefined) __webpack_require__("32e9")(ArrayProto, UNSCOPABLES, {});

        module.exports = function (key) {
          ArrayProto[UNSCOPABLES][key] = true;
        };
        /***/

      },

      /***/
      "9def":
      /***/
      function def(module, exports, __webpack_require__) {
        // 7.1.15 ToLength
        var toInteger = __webpack_require__("4588");

        var min = Math.min;

        module.exports = function (it) {
          return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
        };
        /***/

      },

      /***/
      "9e1e":
      /***/
      function e1e(module, exports, __webpack_require__) {
        // Thank's IE8 for his funny defineProperty
        module.exports = !__webpack_require__("79e5")(function () {
          return Object.defineProperty({}, 'a', {
            get: function get() {
              return 7;
            }
          }).a != 7;
        });
        /***/
      },

      /***/
      "a352":
      /***/
      function a352(module, exports) {
        module.exports = __WEBPACK_EXTERNAL_MODULE_a352__;
        /***/
      },

      /***/
      "a481":
      /***/
      function a481(module, exports, __webpack_require__) {
        "use strict";

        var anObject = __webpack_require__("cb7c");

        var toObject = __webpack_require__("4bf8");

        var toLength = __webpack_require__("9def");

        var toInteger = __webpack_require__("4588");

        var advanceStringIndex = __webpack_require__("0390");

        var regExpExec = __webpack_require__("5f1b");

        var max = Math.max;
        var min = Math.min;
        var floor = Math.floor;
        var SUBSTITUTION_SYMBOLS = /\$([$&`']|\d\d?|<[^>]*>)/g;
        var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&`']|\d\d?)/g;

        var maybeToString = function maybeToString(it) {
          return it === undefined ? it : String(it);
        }; // @@replace logic


        __webpack_require__("214f")('replace', 2, function (defined, REPLACE, $replace, maybeCallNative) {
          return [// `String.prototype.replace` method
          // https://tc39.github.io/ecma262/#sec-string.prototype.replace
          function replace(searchValue, replaceValue) {
            var O = defined(this);
            var fn = searchValue == undefined ? undefined : searchValue[REPLACE];
            return fn !== undefined ? fn.call(searchValue, O, replaceValue) : $replace.call(String(O), searchValue, replaceValue);
          }, // `RegExp.prototype[@@replace]` method
          // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@replace
          function (regexp, replaceValue) {
            var res = maybeCallNative($replace, regexp, this, replaceValue);
            if (res.done) return res.value;
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
          }]; // https://tc39.github.io/ecma262/#sec-getsubstitution

          function getSubstitution(matched, str, position, captures, namedCaptures, replacement) {
            var tailPos = position + matched.length;
            var m = captures.length;
            var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;

            if (namedCaptures !== undefined) {
              namedCaptures = toObject(namedCaptures);
              symbols = SUBSTITUTION_SYMBOLS;
            }

            return $replace.call(replacement, symbols, function (match, ch) {
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
          }
        });
        /***/

      },

      /***/
      "aae3":
      /***/
      function aae3(module, exports, __webpack_require__) {
        // 7.2.8 IsRegExp(argument)
        var isObject = __webpack_require__("d3f4");

        var cof = __webpack_require__("2d95");

        var MATCH = __webpack_require__("2b4c")('match');

        module.exports = function (it) {
          var isRegExp;
          return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
        };
        /***/

      },

      /***/
      "ac6a":
      /***/
      function ac6a(module, exports, __webpack_require__) {
        var $iterators = __webpack_require__("cadf");

        var getKeys = __webpack_require__("0d58");

        var redefine = __webpack_require__("2aba");

        var global = __webpack_require__("7726");

        var hide = __webpack_require__("32e9");

        var Iterators = __webpack_require__("84f2");

        var wks = __webpack_require__("2b4c");

        var ITERATOR = wks('iterator');
        var TO_STRING_TAG = wks('toStringTag');
        var ArrayValues = Iterators.Array;
        var DOMIterables = {
          CSSRuleList: true,
          // TODO: Not spec compliant, should be false.
          CSSStyleDeclaration: false,
          CSSValueList: false,
          ClientRectList: false,
          DOMRectList: false,
          DOMStringList: false,
          DOMTokenList: true,
          DataTransferItemList: false,
          FileList: false,
          HTMLAllCollection: false,
          HTMLCollection: false,
          HTMLFormElement: false,
          HTMLSelectElement: false,
          MediaList: true,
          // TODO: Not spec compliant, should be false.
          MimeTypeArray: false,
          NamedNodeMap: false,
          NodeList: true,
          PaintRequestList: false,
          Plugin: false,
          PluginArray: false,
          SVGLengthList: false,
          SVGNumberList: false,
          SVGPathSegList: false,
          SVGPointList: false,
          SVGStringList: false,
          SVGTransformList: false,
          SourceBufferList: false,
          StyleSheetList: true,
          // TODO: Not spec compliant, should be false.
          TextTrackCueList: false,
          TextTrackList: false,
          TouchList: false
        };

        for (var collections = getKeys(DOMIterables), i = 0; i < collections.length; i++) {
          var NAME = collections[i];
          var explicit = DOMIterables[NAME];
          var Collection = global[NAME];
          var proto = Collection && Collection.prototype;
          var key;

          if (proto) {
            if (!proto[ITERATOR]) hide(proto, ITERATOR, ArrayValues);
            if (!proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
            Iterators[NAME] = ArrayValues;
            if (explicit) for (key in $iterators) {
              if (!proto[key]) redefine(proto, key, $iterators[key], true);
            }
          }
        }
        /***/

      },

      /***/
      "b0c5":
      /***/
      function b0c5(module, exports, __webpack_require__) {
        "use strict";

        var regexpExec = __webpack_require__("520a");

        __webpack_require__("5ca1")({
          target: 'RegExp',
          proto: true,
          forced: regexpExec !== /./.exec
        }, {
          exec: regexpExec
        });
        /***/

      },

      /***/
      "be13":
      /***/
      function be13(module, exports) {
        // 7.2.1 RequireObjectCoercible(argument)
        module.exports = function (it) {
          if (it == undefined) throw TypeError("Can't call method on  " + it);
          return it;
        };
        /***/

      },

      /***/
      "c366":
      /***/
      function c366(module, exports, __webpack_require__) {
        // false -> Array#indexOf
        // true  -> Array#includes
        var toIObject = __webpack_require__("6821");

        var toLength = __webpack_require__("9def");

        var toAbsoluteIndex = __webpack_require__("77f1");

        module.exports = function (IS_INCLUDES) {
          return function ($this, el, fromIndex) {
            var O = toIObject($this);
            var length = toLength(O.length);
            var index = toAbsoluteIndex(fromIndex, length);
            var value; // Array#includes uses SameValueZero equality algorithm
            // eslint-disable-next-line no-self-compare

            if (IS_INCLUDES && el != el) while (length > index) {
              value = O[index++]; // eslint-disable-next-line no-self-compare

              if (value != value) return true; // Array#indexOf ignores holes, Array#includes - not
            } else for (; length > index; index++) {
              if (IS_INCLUDES || index in O) {
                if (O[index] === el) return IS_INCLUDES || index || 0;
              }
            }
            return !IS_INCLUDES && -1;
          };
        };
        /***/

      },

      /***/
      "c649":
      /***/
      function c649(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        /* WEBPACK VAR INJECTION */

        (function (global) {
          /* harmony export (binding) */
          __webpack_require__.d(__webpack_exports__, "c", function () {
            return insertNodeAt;
          });
          /* harmony export (binding) */


          __webpack_require__.d(__webpack_exports__, "a", function () {
            return camelize;
          });
          /* harmony export (binding) */


          __webpack_require__.d(__webpack_exports__, "b", function () {
            return console;
          });
          /* harmony export (binding) */


          __webpack_require__.d(__webpack_exports__, "d", function () {
            return removeNode;
          });
          /* harmony import */


          var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a481");
          /* harmony import */


          var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__);

          function getConsole() {
            if (typeof window !== "undefined") {
              return window.console;
            }

            return global.console;
          }

          var console = getConsole();

          function cached(fn) {
            var cache = Object.create(null);
            return function cachedFn(str) {
              var hit = cache[str];
              return hit || (cache[str] = fn(str));
            };
          }

          var regex = /-(\w)/g;
          var camelize = cached(function (str) {
            return str.replace(regex, function (_, c) {
              return c ? c.toUpperCase() : "";
            });
          });

          function removeNode(node) {
            if (node.parentElement !== null) {
              node.parentElement.removeChild(node);
            }
          }

          function insertNodeAt(fatherNode, node, position) {
            var refNode = position === 0 ? fatherNode.children[0] : fatherNode.children[position - 1].nextSibling;
            fatherNode.insertBefore(node, refNode);
          }
          /* WEBPACK VAR INJECTION */

        }).call(this, __webpack_require__("c8ba"));
        /***/
      },

      /***/
      "c69a":
      /***/
      function c69a(module, exports, __webpack_require__) {
        module.exports = !__webpack_require__("9e1e") && !__webpack_require__("79e5")(function () {
          return Object.defineProperty(__webpack_require__("230e")('div'), 'a', {
            get: function get() {
              return 7;
            }
          }).a != 7;
        });
        /***/
      },

      /***/
      "c8ba":
      /***/
      function c8ba(module, exports) {
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
        /***/
      },

      /***/
      "ca5a":
      /***/
      function ca5a(module, exports) {
        var id = 0;
        var px = Math.random();

        module.exports = function (key) {
          return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
        };
        /***/

      },

      /***/
      "cadf":
      /***/
      function cadf(module, exports, __webpack_require__) {
        "use strict";

        var addToUnscopables = __webpack_require__("9c6c");

        var step = __webpack_require__("d53b");

        var Iterators = __webpack_require__("84f2");

        var toIObject = __webpack_require__("6821"); // 22.1.3.4 Array.prototype.entries()
        // 22.1.3.13 Array.prototype.keys()
        // 22.1.3.29 Array.prototype.values()
        // 22.1.3.30 Array.prototype[@@iterator]()


        module.exports = __webpack_require__("01f9")(Array, 'Array', function (iterated, kind) {
          this._t = toIObject(iterated); // target

          this._i = 0; // next index

          this._k = kind; // kind
          // 22.1.5.2.1 %ArrayIteratorPrototype%.next()
        }, function () {
          var O = this._t;
          var kind = this._k;
          var index = this._i++;

          if (!O || index >= O.length) {
            this._t = undefined;
            return step(1);
          }

          if (kind == 'keys') return step(0, index);
          if (kind == 'values') return step(0, O[index]);
          return step(0, [index, O[index]]);
        }, 'values'); // argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)

        Iterators.Arguments = Iterators.Array;
        addToUnscopables('keys');
        addToUnscopables('values');
        addToUnscopables('entries');
        /***/
      },

      /***/
      "cb7c":
      /***/
      function cb7c(module, exports, __webpack_require__) {
        var isObject = __webpack_require__("d3f4");

        module.exports = function (it) {
          if (!isObject(it)) throw TypeError(it + ' is not an object!');
          return it;
        };
        /***/

      },

      /***/
      "ce10":
      /***/
      function ce10(module, exports, __webpack_require__) {
        var has = __webpack_require__("69a8");

        var toIObject = __webpack_require__("6821");

        var arrayIndexOf = __webpack_require__("c366")(false);

        var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

        module.exports = function (object, names) {
          var O = toIObject(object);
          var i = 0;
          var result = [];
          var key;

          for (key in O) {
            if (key != IE_PROTO) has(O, key) && result.push(key);
          } // Don't enum bug & hidden keys


          while (names.length > i) {
            if (has(O, key = names[i++])) {
              ~arrayIndexOf(result, key) || result.push(key);
            }
          }

          return result;
        };
        /***/

      },

      /***/
      "d2c8":
      /***/
      function d2c8(module, exports, __webpack_require__) {
        // helper for String#{startsWith, endsWith, includes}
        var isRegExp = __webpack_require__("aae3");

        var defined = __webpack_require__("be13");

        module.exports = function (that, searchString, NAME) {
          if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
          return String(defined(that));
        };
        /***/

      },

      /***/
      "d3f4":
      /***/
      function d3f4(module, exports) {
        module.exports = function (it) {
          return _typeof(it) === 'object' ? it !== null : typeof it === 'function';
        };
        /***/

      },

      /***/
      "d53b":
      /***/
      function d53b(module, exports) {
        module.exports = function (done, value) {
          return {
            value: value,
            done: !!done
          };
        };
        /***/

      },

      /***/
      "d8e8":
      /***/
      function d8e8(module, exports) {
        module.exports = function (it) {
          if (typeof it != 'function') throw TypeError(it + ' is not a function!');
          return it;
        };
        /***/

      },

      /***/
      "e11e":
      /***/
      function e11e(module, exports) {
        // IE 8- don't enum bug keys
        module.exports = 'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'.split(',');
        /***/
      },

      /***/
      "f559":
      /***/
      function f559(module, exports, __webpack_require__) {
        "use strict"; // 21.1.3.18 String.prototype.startsWith(searchString [, position ])

        var $export = __webpack_require__("5ca1");

        var toLength = __webpack_require__("9def");

        var context = __webpack_require__("d2c8");

        var STARTS_WITH = 'startsWith';
        var $startsWith = ''[STARTS_WITH];
        $export($export.P + $export.F * __webpack_require__("5147")(STARTS_WITH), 'String', {
          startsWith: function startsWith(searchString
          /* , position = 0 */
          ) {
            var that = context(this, searchString, STARTS_WITH);
            var index = toLength(Math.min(arguments.length > 1 ? arguments[1] : undefined, that.length));
            var search = String(searchString);
            return $startsWith ? $startsWith.call(that, search, index) : that.slice(index, index + search.length) === search;
          }
        });
        /***/
      },

      /***/
      "f6fd":
      /***/
      function f6fd(module, exports) {
        // document.currentScript polyfill by Adam Miller
        // MIT license
        (function (document) {
          var currentScript = "currentScript",
              scripts = document.getElementsByTagName('script'); // Live NodeList collection
          // If browser needs currentScript polyfill, add get currentScript() to the document object

          if (!(currentScript in document)) {
            Object.defineProperty(document, currentScript, {
              get: function get() {
                // IE 6-10 supports script readyState
                // IE 10+ support stack trace
                try {
                  throw new Error();
                } catch (err) {
                  // Find the second match for the "at" string to get file src url from stack.
                  // Specifically works with the format of stack traces in IE.
                  var i,
                      res = (/.*at [^\(]*\((.*):.+:.+\)$/ig.exec(err.stack) || [false])[1]; // For all scripts on the page, if src matches or if ready state is interactive, return the script tag

                  for (i in scripts) {
                    if (scripts[i].src == res || scripts[i].readyState == "interactive") {
                      return scripts[i];
                    }
                  } // If no match, return null


                  return null;
                }
              }
            });
          }
        })(document);
        /***/

      },

      /***/
      "f751":
      /***/
      function f751(module, exports, __webpack_require__) {
        // 19.1.3.1 Object.assign(target, source)
        var $export = __webpack_require__("5ca1");

        $export($export.S + $export.F, 'Object', {
          assign: __webpack_require__("7333")
        });
        /***/
      },

      /***/
      "fa5b":
      /***/
      function fa5b(module, exports, __webpack_require__) {
        module.exports = __webpack_require__("5537")('native-function-to-string', Function.toString);
        /***/
      },

      /***/
      "fab2":
      /***/
      function fab2(module, exports, __webpack_require__) {
        var document = __webpack_require__("7726").document;

        module.exports = document && document.documentElement;
        /***/
      },

      /***/
      "fb15":
      /***/
      function fb15(module, __webpack_exports__, __webpack_require__) {
        "use strict"; // ESM COMPAT FLAG

        __webpack_require__.r(__webpack_exports__); // CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
        // This file is imported into lib/wc client bundles.


        if (typeof window !== 'undefined') {
          if (true) {
            __webpack_require__("f6fd");
          }

          var setPublicPath_i;

          if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
            __webpack_require__.p = setPublicPath_i[1]; // eslint-disable-line
          }
        } // Indicate to webpack that this file can be concatenated

        /* harmony default export */


        var setPublicPath = null; // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.assign.js

        var es6_object_assign = __webpack_require__("f751"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.starts-with.js


        var es6_string_starts_with = __webpack_require__("f559"); // EXTERNAL MODULE: ./node_modules/core-js/modules/web.dom.iterable.js


        var web_dom_iterable = __webpack_require__("ac6a"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.iterator.js


        var es6_array_iterator = __webpack_require__("cadf"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.keys.js


        var es6_object_keys = __webpack_require__("456d"); // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js


        function _arrayWithHoles(arr) {
          if (Array.isArray(arr)) return arr;
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js


        function _iterableToArrayLimit(arr, i) {
          if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
          var _arr = [];
          var _n = true;
          var _d = false;
          var _e = undefined;

          try {
            for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
              _arr.push(_s.value);

              if (i && _arr.length === i) break;
            }
          } catch (err) {
            _d = true;
            _e = err;
          } finally {
            try {
              if (!_n && _i["return"] != null) _i["return"]();
            } finally {
              if (_d) throw _e;
            }
          }

          return _arr;
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js


        function _arrayLikeToArray(arr, len) {
          if (len == null || len > arr.length) len = arr.length;

          for (var i = 0, arr2 = new Array(len); i < len; i++) {
            arr2[i] = arr[i];
          }

          return arr2;
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js


        function _unsupportedIterableToArray(o, minLen) {
          if (!o) return;
          if (typeof o === "string") return _arrayLikeToArray(o, minLen);
          var n = Object.prototype.toString.call(o).slice(8, -1);
          if (n === "Object" && o.constructor) n = o.constructor.name;
          if (n === "Map" || n === "Set") return Array.from(o);
          if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js


        function _nonIterableRest() {
          throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js


        function _slicedToArray(arr, i) {
          return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
        } // EXTERNAL MODULE: ./node_modules/core-js/modules/es7.array.includes.js


        var es7_array_includes = __webpack_require__("6762"); // EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.includes.js


        var es6_string_includes = __webpack_require__("2fdb"); // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js


        function _arrayWithoutHoles(arr) {
          if (Array.isArray(arr)) return _arrayLikeToArray(arr);
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js


        function _iterableToArray(iter) {
          if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js


        function _nonIterableSpread() {
          throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        } // CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js


        function _toConsumableArray(arr) {
          return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
        } // EXTERNAL MODULE: external {"commonjs":"sortablejs","commonjs2":"sortablejs","amd":"sortablejs","root":"Sortable"}


        var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_ = __webpack_require__("a352");

        var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default = /*#__PURE__*/__webpack_require__.n(external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_); // EXTERNAL MODULE: ./src/util/helper.js


        var helper = __webpack_require__("c649"); // CONCATENATED MODULE: ./src/vuedraggable.js


        function buildAttribute(object, propName, value) {
          if (value === undefined) {
            return object;
          }

          object = object || {};
          object[propName] = value;
          return object;
        }

        function computeVmIndex(vnodes, element) {
          return vnodes.map(function (elt) {
            return elt.elm;
          }).indexOf(element);
        }

        function _computeIndexes(slots, children, isTransition, footerOffset) {
          if (!slots) {
            return [];
          }

          var elmFromNodes = slots.map(function (elt) {
            return elt.elm;
          });
          var footerIndex = children.length - footerOffset;

          var rawIndexes = _toConsumableArray(children).map(function (elt, idx) {
            return idx >= footerIndex ? elmFromNodes.length : elmFromNodes.indexOf(elt);
          });

          return isTransition ? rawIndexes.filter(function (ind) {
            return ind !== -1;
          }) : rawIndexes;
        }

        function emit(evtName, evtData) {
          var _this = this;

          this.$nextTick(function () {
            return _this.$emit(evtName.toLowerCase(), evtData);
          });
        }

        function delegateAndEmit(evtName) {
          var _this2 = this;

          return function (evtData) {
            if (_this2.realList !== null) {
              _this2["onDrag" + evtName](evtData);
            }

            emit.call(_this2, evtName, evtData);
          };
        }

        function isTransitionName(name) {
          return ["transition-group", "TransitionGroup"].includes(name);
        }

        function vuedraggable_isTransition(slots) {
          if (!slots || slots.length !== 1) {
            return false;
          }

          var _slots = _slicedToArray(slots, 1),
              componentOptions = _slots[0].componentOptions;

          if (!componentOptions) {
            return false;
          }

          return isTransitionName(componentOptions.tag);
        }

        function getSlot(slot, scopedSlot, key) {
          return slot[key] || (scopedSlot[key] ? scopedSlot[key]() : undefined);
        }

        function computeChildrenAndOffsets(children, slot, scopedSlot) {
          var headerOffset = 0;
          var footerOffset = 0;
          var header = getSlot(slot, scopedSlot, "header");

          if (header) {
            headerOffset = header.length;
            children = children ? [].concat(_toConsumableArray(header), _toConsumableArray(children)) : _toConsumableArray(header);
          }

          var footer = getSlot(slot, scopedSlot, "footer");

          if (footer) {
            footerOffset = footer.length;
            children = children ? [].concat(_toConsumableArray(children), _toConsumableArray(footer)) : _toConsumableArray(footer);
          }

          return {
            children: children,
            headerOffset: headerOffset,
            footerOffset: footerOffset
          };
        }

        function getComponentAttributes($attrs, componentData) {
          var attributes = null;

          var update = function update(name, value) {
            attributes = buildAttribute(attributes, name, value);
          };

          var attrs = Object.keys($attrs).filter(function (key) {
            return key === "id" || key.startsWith("data-");
          }).reduce(function (res, key) {
            res[key] = $attrs[key];
            return res;
          }, {});
          update("attrs", attrs);

          if (!componentData) {
            return attributes;
          }

          var on = componentData.on,
              props = componentData.props,
              componentDataAttrs = componentData.attrs;
          update("on", on);
          update("props", props);
          Object.assign(attributes.attrs, componentDataAttrs);
          return attributes;
        }

        var eventsListened = ["Start", "Add", "Remove", "Update", "End"];
        var eventsToEmit = ["Choose", "Unchoose", "Sort", "Filter", "Clone"];
        var readonlyProperties = ["Move"].concat(eventsListened, eventsToEmit).map(function (evt) {
          return "on" + evt;
        });
        var draggingElement = null;
        var props = {
          options: Object,
          list: {
            type: Array,
            required: false,
            "default": null
          },
          value: {
            type: Array,
            required: false,
            "default": null
          },
          noTransitionOnDrag: {
            type: Boolean,
            "default": false
          },
          clone: {
            type: Function,
            "default": function _default(original) {
              return original;
            }
          },
          element: {
            type: String,
            "default": "div"
          },
          tag: {
            type: String,
            "default": null
          },
          move: {
            type: Function,
            "default": null
          },
          componentData: {
            type: Object,
            required: false,
            "default": null
          }
        };
        var draggableComponent = {
          name: "draggable",
          inheritAttrs: false,
          props: props,
          data: function data() {
            return {
              transitionMode: false,
              noneFunctionalComponentMode: false
            };
          },
          render: function render(h) {
            var slots = this.$slots["default"];
            this.transitionMode = vuedraggable_isTransition(slots);

            var _computeChildrenAndOf = computeChildrenAndOffsets(slots, this.$slots, this.$scopedSlots),
                children = _computeChildrenAndOf.children,
                headerOffset = _computeChildrenAndOf.headerOffset,
                footerOffset = _computeChildrenAndOf.footerOffset;

            this.headerOffset = headerOffset;
            this.footerOffset = footerOffset;
            var attributes = getComponentAttributes(this.$attrs, this.componentData);
            return h(this.getTag(), attributes, children);
          },
          created: function created() {
            if (this.list !== null && this.value !== null) {
              helper["b"
              /* console */
              ].error("Value and list props are mutually exclusive! Please set one or another.");
            }

            if (this.element !== "div") {
              helper["b"
              /* console */
              ].warn("Element props is deprecated please use tag props instead. See https://github.com/SortableJS/Vue.Draggable/blob/master/documentation/migrate.md#element-props");
            }

            if (this.options !== undefined) {
              helper["b"
              /* console */
              ].warn("Options props is deprecated, add sortable options directly as vue.draggable item, or use v-bind. See https://github.com/SortableJS/Vue.Draggable/blob/master/documentation/migrate.md#options-props");
            }
          },
          mounted: function mounted() {
            var _this3 = this;

            this.noneFunctionalComponentMode = this.getTag().toLowerCase() !== this.$el.nodeName.toLowerCase() && !this.getIsFunctional();

            if (this.noneFunctionalComponentMode && this.transitionMode) {
              throw new Error("Transition-group inside component is not supported. Please alter tag value or remove transition-group. Current tag value: ".concat(this.getTag()));
            }

            var optionsAdded = {};
            eventsListened.forEach(function (elt) {
              optionsAdded["on" + elt] = delegateAndEmit.call(_this3, elt);
            });
            eventsToEmit.forEach(function (elt) {
              optionsAdded["on" + elt] = emit.bind(_this3, elt);
            });
            var attributes = Object.keys(this.$attrs).reduce(function (res, key) {
              res[Object(helper["a"
              /* camelize */
              ])(key)] = _this3.$attrs[key];
              return res;
            }, {});
            var options = Object.assign({}, this.options, attributes, optionsAdded, {
              onMove: function onMove(evt, originalEvent) {
                return _this3.onDragMove(evt, originalEvent);
              }
            });
            !("draggable" in options) && (options.draggable = ">*");
            this._sortable = new external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default.a(this.rootContainer, options);
            this.computeIndexes();
          },
          beforeDestroy: function beforeDestroy() {
            if (this._sortable !== undefined) this._sortable.destroy();
          },
          computed: {
            rootContainer: function rootContainer() {
              return this.transitionMode ? this.$el.children[0] : this.$el;
            },
            realList: function realList() {
              return this.list ? this.list : this.value;
            }
          },
          watch: {
            options: {
              handler: function handler(newOptionValue) {
                this.updateOptions(newOptionValue);
              },
              deep: true
            },
            $attrs: {
              handler: function handler(newOptionValue) {
                this.updateOptions(newOptionValue);
              },
              deep: true
            },
            realList: function realList() {
              this.computeIndexes();
            }
          },
          methods: {
            getIsFunctional: function getIsFunctional() {
              var fnOptions = this._vnode.fnOptions;
              return fnOptions && fnOptions.functional;
            },
            getTag: function getTag() {
              return this.tag || this.element;
            },
            updateOptions: function updateOptions(newOptionValue) {
              for (var property in newOptionValue) {
                var value = Object(helper["a"
                /* camelize */
                ])(property);

                if (readonlyProperties.indexOf(value) === -1) {
                  this._sortable.option(value, newOptionValue[property]);
                }
              }
            },
            getChildrenNodes: function getChildrenNodes() {
              if (this.noneFunctionalComponentMode) {
                return this.$children[0].$slots["default"];
              }

              var rawNodes = this.$slots["default"];
              return this.transitionMode ? rawNodes[0].child.$slots["default"] : rawNodes;
            },
            computeIndexes: function computeIndexes() {
              var _this4 = this;

              this.$nextTick(function () {
                _this4.visibleIndexes = _computeIndexes(_this4.getChildrenNodes(), _this4.rootContainer.children, _this4.transitionMode, _this4.footerOffset);
              });
            },
            getUnderlyingVm: function getUnderlyingVm(htmlElt) {
              var index = computeVmIndex(this.getChildrenNodes() || [], htmlElt);

              if (index === -1) {
                //Edge case during move callback: related element might be
                //an element different from collection
                return null;
              }

              var element = this.realList[index];
              return {
                index: index,
                element: element
              };
            },
            getUnderlyingPotencialDraggableComponent: function getUnderlyingPotencialDraggableComponent(_ref) {
              var vue = _ref.__vue__;

              if (!vue || !vue.$options || !isTransitionName(vue.$options._componentTag)) {
                if (!("realList" in vue) && vue.$children.length === 1 && "realList" in vue.$children[0]) return vue.$children[0];
                return vue;
              }

              return vue.$parent;
            },
            emitChanges: function emitChanges(evt) {
              var _this5 = this;

              this.$nextTick(function () {
                _this5.$emit("change", evt);
              });
            },
            alterList: function alterList(onList) {
              if (this.list) {
                onList(this.list);
                return;
              }

              var newList = _toConsumableArray(this.value);

              onList(newList);
              this.$emit("input", newList);
            },
            spliceList: function spliceList() {
              var _arguments = arguments;

              var spliceList = function spliceList(list) {
                return list.splice.apply(list, _toConsumableArray(_arguments));
              };

              this.alterList(spliceList);
            },
            updatePosition: function updatePosition(oldIndex, newIndex) {
              var updatePosition = function updatePosition(list) {
                return list.splice(newIndex, 0, list.splice(oldIndex, 1)[0]);
              };

              this.alterList(updatePosition);
            },
            getRelatedContextFromMoveEvent: function getRelatedContextFromMoveEvent(_ref2) {
              var to = _ref2.to,
                  related = _ref2.related;
              var component = this.getUnderlyingPotencialDraggableComponent(to);

              if (!component) {
                return {
                  component: component
                };
              }

              var list = component.realList;
              var context = {
                list: list,
                component: component
              };

              if (to !== related && list && component.getUnderlyingVm) {
                var destination = component.getUnderlyingVm(related);

                if (destination) {
                  return Object.assign(destination, context);
                }
              }

              return context;
            },
            getVmIndex: function getVmIndex(domIndex) {
              var indexes = this.visibleIndexes;
              var numberIndexes = indexes.length;
              return domIndex > numberIndexes - 1 ? numberIndexes : indexes[domIndex];
            },
            getComponent: function getComponent() {
              return this.$slots["default"][0].componentInstance;
            },
            resetTransitionData: function resetTransitionData(index) {
              if (!this.noTransitionOnDrag || !this.transitionMode) {
                return;
              }

              var nodes = this.getChildrenNodes();
              nodes[index].data = null;
              var transitionContainer = this.getComponent();
              transitionContainer.children = [];
              transitionContainer.kept = undefined;
            },
            onDragStart: function onDragStart(evt) {
              this.context = this.getUnderlyingVm(evt.item);
              evt.item._underlying_vm_ = this.clone(this.context.element);
              draggingElement = evt.item;
            },
            onDragAdd: function onDragAdd(evt) {
              var element = evt.item._underlying_vm_;

              if (element === undefined) {
                return;
              }

              Object(helper["d"
              /* removeNode */
              ])(evt.item);
              var newIndex = this.getVmIndex(evt.newIndex);
              this.spliceList(newIndex, 0, element);
              this.computeIndexes();
              var added = {
                element: element,
                newIndex: newIndex
              };
              this.emitChanges({
                added: added
              });
            },
            onDragRemove: function onDragRemove(evt) {
              Object(helper["c"
              /* insertNodeAt */
              ])(this.rootContainer, evt.item, evt.oldIndex);

              if (evt.pullMode === "clone") {
                Object(helper["d"
                /* removeNode */
                ])(evt.clone);
                return;
              }

              var oldIndex = this.context.index;
              this.spliceList(oldIndex, 1);
              var removed = {
                element: this.context.element,
                oldIndex: oldIndex
              };
              this.resetTransitionData(oldIndex);
              this.emitChanges({
                removed: removed
              });
            },
            onDragUpdate: function onDragUpdate(evt) {
              Object(helper["d"
              /* removeNode */
              ])(evt.item);
              Object(helper["c"
              /* insertNodeAt */
              ])(evt.from, evt.item, evt.oldIndex);
              var oldIndex = this.context.index;
              var newIndex = this.getVmIndex(evt.newIndex);
              this.updatePosition(oldIndex, newIndex);
              var moved = {
                element: this.context.element,
                oldIndex: oldIndex,
                newIndex: newIndex
              };
              this.emitChanges({
                moved: moved
              });
            },
            updateProperty: function updateProperty(evt, propertyName) {
              evt.hasOwnProperty(propertyName) && (evt[propertyName] += this.headerOffset);
            },
            computeFutureIndex: function computeFutureIndex(relatedContext, evt) {
              if (!relatedContext.element) {
                return 0;
              }

              var domChildren = _toConsumableArray(evt.to.children).filter(function (el) {
                return el.style["display"] !== "none";
              });

              var currentDOMIndex = domChildren.indexOf(evt.related);
              var currentIndex = relatedContext.component.getVmIndex(currentDOMIndex);
              var draggedInList = domChildren.indexOf(draggingElement) !== -1;
              return draggedInList || !evt.willInsertAfter ? currentIndex : currentIndex + 1;
            },
            onDragMove: function onDragMove(evt, originalEvent) {
              var onMove = this.move;

              if (!onMove || !this.realList) {
                return true;
              }

              var relatedContext = this.getRelatedContextFromMoveEvent(evt);
              var draggedContext = this.context;
              var futureIndex = this.computeFutureIndex(relatedContext, evt);
              Object.assign(draggedContext, {
                futureIndex: futureIndex
              });
              var sendEvt = Object.assign({}, evt, {
                relatedContext: relatedContext,
                draggedContext: draggedContext
              });
              return onMove(sendEvt, originalEvent);
            },
            onDragEnd: function onDragEnd() {
              this.computeIndexes();
              draggingElement = null;
            }
          }
        };

        if (typeof window !== "undefined" && "Vue" in window) {
          window.Vue.component("draggable", draggableComponent);
        }
        /* harmony default export */


        var vuedraggable = draggableComponent; // CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib.js

        /* harmony default export */

        var entry_lib = __webpack_exports__["default"] = vuedraggable;
        /***/
      }
      /******/

    })["default"]
  );
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../webpack/buildin/module.js */ "./node_modules/webpack/buildin/module.js")(module)))

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--34-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/Icon.vue?vue&type=script&lang=js& ***!
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
  !*** ./node_modules/babel-loader/lib??ref--34-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=script&lang=js& ***!
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
  !*** ./node_modules/babel-loader/lib??ref--34-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ImageCell__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ImageCell */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue");
/* harmony import */ var _ImageDetail__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ImageDetail */ "./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue");
/* harmony import */ var vuedraggable__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuedraggable */ "./node_modules/@concretecms/bedrock/node_modules/vuedraggable/dist/vuedraggable.umd.js");
/* harmony import */ var vuedraggable__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vuedraggable__WEBPACK_IMPORTED_MODULE_2__);
function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

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
//
//
//
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
  components: _objectSpread(_objectSpread({
    draggable: vuedraggable__WEBPACK_IMPORTED_MODULE_2___default.a,
    ImageCell: _ImageCell__WEBPACK_IMPORTED_MODULE_0__["default"],
    ImageDetail: _ImageDetail__WEBPACK_IMPORTED_MODULE_1__["default"]
  }, _ImageCell__WEBPACK_IMPORTED_MODULE_0__["default"].components), _ImageDetail__WEBPACK_IMPORTED_MODULE_1__["default"].components),
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
        var _iterator = _createForOfIteratorHelper(data.fID),
            _step;

        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var fID = _step.value;
            ConcreteFileManager.getFileDetails(fID, function (file) {
              file = file.files[0] || {};
              me.gallery.push({
                id: file.fID,
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
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }, {
        multipleSelection: true
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
  !*** ./node_modules/babel-loader/lib??ref--34-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=script&lang=js& ***!
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
  !*** ./node_modules/babel-loader/lib??ref--34-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=script&lang=js& ***!
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
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& ***!
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
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& ***!
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
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& ***!
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
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& ***!
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
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../css-loader!../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../postcss-loader/src??ref--30-2!../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../vue-loader/lib??vue-loader-options!./IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/IconButton.vue?vue&type=style&index=0&id=2ba8a44f&lang=scss&scoped=true&");

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
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--30-2!../../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../../vue-loader/lib??vue-loader-options!./GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/GalleryEdit.vue?vue&type=style&index=0&id=a5d890aa&lang=scss&scoped=true&");

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
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--30-2!../../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageCell.vue?vue&type=style&index=0&id=ad918c68&lang=scss&scoped=true&");

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
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--30-2!./node_modules/sass-loader/dist/cjs.js??ref--30-3!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../css-loader!../../../../../../vue-loader/lib/loaders/stylePostLoader.js!../../../../../../postcss-loader/src??ref--30-2!../../../../../../sass-loader/dist/cjs.js??ref--30-3!../../../../../../vue-loader/lib??vue-loader-options!./ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/@concretecms/bedrock/assets/cms/components/gallery/ImageDetail.vue?vue&type=style&index=0&id=ec81668a&lang=scss&scoped=true&");

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
        _c(
          "div",
          {
            directives: [
              {
                name: "show",
                rawName: "v-show",
                value: _vm.activeTab === "image",
                expression: "activeTab === 'image'"
              }
            ],
            attrs: { id: "galleryImages" }
          },
          [
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
                  [_vm._v("\n                    Add Images\n                ")]
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
              [
                _c(
                  "draggable",
                  {
                    staticClass: "image-container",
                    attrs: { group: "people" },
                    on: {
                      start: function($event) {
                        _vm.drag = true
                      },
                      end: function($event) {
                        _vm.drag = false
                      }
                    },
                    model: {
                      value: _vm.$props.gallery,
                      callback: function($$v) {
                        _vm.$set(_vm.$props, "gallery", $$v)
                      },
                      expression: "$props.gallery"
                    }
                  },
                  _vm._l(_vm.$props.gallery, function(image, index) {
                    return _c("div", { key: index }, [
                      _c(
                        "div",
                        {
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
                    ])
                  }),
                  0
                )
              ],
              1
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
          ]
        ),
        _vm._v(" "),
        _c(
          "div",
          {
            directives: [
              {
                name: "show",
                rawName: "v-show",
                value: _vm.activeTab === "settings",
                expression: "activeTab === 'settings'"
              }
            ],
            attrs: { id: "gallerySettings" }
          },
          [
            _c("h3", [_vm._v("\n              Settings\n            ")]),
            _vm._v(" "),
            _c("div", { staticClass: "form-check" }, [
              _c("input", {
                staticClass: "form-check-input",
                attrs: {
                  type: "checkbox",
                  id: "includeDownloadLink",
                  name: "includeDownloadLink"
                },
                domProps: { value: 1, checked: _vm.$root.includeDownloadLink }
              }),
              _vm._v(" "),
              _c(
                "label",
                {
                  staticClass: "form-check-label",
                  attrs: { for: "includeDownloadLink" }
                },
                [
                  _vm._v(
                    "\n                  Include link to download original\n              "
                  )
                ]
              )
            ])
          ]
        )
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

/***/ 10:
/*!************************************************!*\
  !*** multi ./assets/blocks/gallery/gallery.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/andrewembler/projects/concrete5/build/assets/blocks/gallery/gallery.js */"./assets/blocks/gallery/gallery.js");


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