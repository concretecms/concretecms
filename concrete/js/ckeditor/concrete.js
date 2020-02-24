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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete.js":
/*!***************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete.js ***!
  \***************************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var ckeditor4_adapters_jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor4/adapters/jquery */ "../../concretecms-bedrock/node_modules/ckeditor4/adapters/jquery.js");
/* harmony import */ var ckeditor4_adapters_jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(ckeditor4_adapters_jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _concrete_normalizeonchange__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./concrete/normalizeonchange */ "../../concretecms-bedrock/assets/ckeditor/js/concrete/normalizeonchange.js");
/* harmony import */ var _concrete_normalizeonchange__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_concrete_normalizeonchange__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _concrete_inline__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./concrete/inline */ "../../concretecms-bedrock/assets/ckeditor/js/concrete/inline.js");
/* harmony import */ var _concrete_inline__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_concrete_inline__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _concrete_link__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./concrete/link */ "../../concretecms-bedrock/assets/ckeditor/js/concrete/link.js");
/* harmony import */ var _concrete_link__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_concrete_link__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _concrete_file_manager__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./concrete/file-manager */ "../../concretecms-bedrock/assets/ckeditor/js/concrete/file-manager.js");
/* harmony import */ var _concrete_file_manager__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_concrete_file_manager__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _concrete_styles__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./concrete/styles */ "../../concretecms-bedrock/assets/ckeditor/js/concrete/styles.js");
/* harmony import */ var _concrete_styles__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_concrete_styles__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _concrete_upload_image__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./concrete/upload-image */ "../../concretecms-bedrock/assets/ckeditor/js/concrete/upload-image.js");
/* harmony import */ var _concrete_upload_image__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_concrete_upload_image__WEBPACK_IMPORTED_MODULE_6__);
// Import the jQuery adapter
 // Import the concrete5 CKEditor plugins








/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete/file-manager.js":
/*!****************************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete/file-manager.js ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  CKEDITOR.plugins.add('concrete5filemanager', {
    requires: 'filebrowser',
    init: function init() {
      CKEDITOR.on('dialogDefinition', function (event) {
        var editor = event.editor,
            dialogDefinition = event.data.definition,
            tabContent = dialogDefinition.contents.length;

        function makeButtonClickHandler() {
          return function () {
            editor._.filebrowserSe = this;
            var dialog = this.getDialog();
            ConcreteFileManager.launchDialog(function (data) {
              jQuery.fn.dialog.showLoader();
              ConcreteFileManager.getFileDetails(data.fID, function (r) {
                jQuery.fn.dialog.hideLoader();
                var file = r.files[0];

                if ((dialog.getName() == 'image' || dialog.getName() == 'image2') && dialog._.currentTabId == 'info') {
                  CKEDITOR.tools.callFunction(editor._.filebrowserFn, file.urlInline, function () {
                    dialog.dontResetSize = true;
                    var element;
                    element = dialog.getContentElement('info', 'txtWidth');

                    if (element) {
                      element.setValue('');
                    }

                    element = dialog.getContentElement('info', 'width');

                    if (element) {
                      element.setValue('');
                    }

                    element = dialog.getContentElement('info', 'txtHeight');

                    if (element) {
                      element.setValue('');
                    }

                    element = dialog.getContentElement('info', 'height');

                    if (element) {
                      element.setValue('');
                    }

                    element = dialog.getContentElement('info', 'txtAlt');

                    if (element) {
                      element.setValue(file.title);
                    }

                    element = dialog.getContentElement('info', 'alt');

                    if (element) {
                      element.setValue(file.title);
                    }
                  });
                } else {
                  CKEDITOR.tools.callFunction(editor._.filebrowserFn, file.urlDownload);
                }
              });
            });
          };
        }

        for (var i = 0; i < tabContent; i++) {
          var browseButton = dialogDefinition.contents[i].get('browse');

          if (browseButton !== null) {
            browseButton.hidden = false;
            browseButton.onClick = makeButtonClickHandler();
          }
        }
      });
    }
  });
})();

/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete/inline.js":
/*!**********************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete/inline.js ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

CKEDITOR.plugins.add('concrete5inline', {
  init: function init(editor) {
    // Save plugin is for replace mode only.
    if (editor.elementMode != CKEDITOR.ELEMENT_MODE_INLINE) return;
    editor.addCommand('c5save', {
      'exec': function exec(editor) {
        $('#' + editor.element.$.id + '_content').val(editor.getData());
        ConcreteEvent.fire('EditModeBlockSaveInline');
        editor.destroy();
      }
    });
    editor.addCommand('c5cancel', {
      'exec': function exec(editor) {
        ConcreteEvent.fire('EditModeExitInline');
        editor.destroy();
      }
    });

    if (editor.ui.addButton) {
      editor.ui.addButton('concrete_save', {
        label: editor.lang.common.ok,
        command: 'c5save',
        toolbar: 'document,0'
      });
      editor.ui.addButton('concrete_cancel', {
        label: editor.lang.common.cancel,
        command: 'c5cancel',
        toolbar: 'document,1'
      });
    }
  }
});

/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete/link.js":
/*!********************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete/link.js ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  CKEDITOR.plugins.add('concrete5link', {
    requires: 'link',
    init: function init(editor) {
      CKEDITOR.on('dialogDefinition', function (ev) {
        // Take the dialog name and its definition from the event data.
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;
        var commonLang = editor.lang.common;
        var linkLang = editor.lang.link;

        var commitParams = function commitParams(page, data) {
          if (!data[page]) data[page] = {};
          data[page][this.id] = this.getValue() || '';
        };

        var commitLightboxParams = function commitLightboxParams(data) {
          return commitParams.call(this, 'target', data);
        };

        var getSelectedLink = function getSelectedLink() {
          // whenever the editor is saved but the page not published and the editor put in edit mode again
          // the dialogDefinition event runs twice one after the other.
          // the first time the editor parameter of the init() function still references the previous instance
          // of the editor and the second time it references the new instance.
          // So when running the editor through the function getSelectedLink() it throws an error the first since
          // that instance of the editor doesn't exist anymore.
          // getting the editor from the event deals with that
          // But this whole double event might be a bug with how C5 loads the editor
          return CKEDITOR.plugins.link.getSelectedLink(ev.editor);
        }; // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).


        if (dialogName == 'link') {
          // Get a reference to the "Link Info" tab.
          var infoTab = dialogDefinition.getContents('info');

          if (infoTab.get('sitemapBrowse') === null && ev.editor.config.sitemap) {
            infoTab.add({
              type: 'button',
              id: 'sitemapBrowse',
              label: 'Sitemap',
              title: 'Sitemap',
              onClick: function onClick() {
                jQuery.fn.dialog.open({
                  width: '90%',
                  height: '70%',
                  modal: false,
                  title: ccmi18n_sitemap.choosePage,
                  href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/sitemap_selector'
                });
                ConcreteEvent.unsubscribe('SitemapSelectPage');
                ConcreteEvent.subscribe('SitemapSelectPage', function (e, data) {
                  jQuery.fn.dialog.closeTop();
                  var element = dialogDefinition.dialog.getContentElement('info', 'url');

                  if (element) {
                    element.setValue(CCM_APPLICATION_URL + '/index.php?cID=' + data.cID);
                  }
                });
              }
            }, 'browse');
          }

          var targetTab = dialogDefinition.getContents('target');

          if (targetTab.get('linkTargetType') !== null) {
            // add the lightbox option to the target type dropdown
            var targetSelect = targetTab.get('linkTargetType');

            if (targetSelect.items[3][1] != "lightbox") {
              targetSelect.items.splice(3, 0, ["<lightbox>", "lightbox"]);
              targetSelect.items.join();
            } // Add the UI that is shown when the user selects our new target type
            // option from the select box.


            if (targetTab.get('lightboxFeatures') === null) {
              targetTab.elements.push({
                type: 'vbox',
                width: '100%',
                align: 'center',
                padding: 2,
                id: 'lightboxFeatures',
                children: [{
                  type: 'fieldset',
                  label: 'Lightbox Features',
                  children: [{
                    type: 'hbox',
                    children: [{
                      type: 'checkbox',
                      id: 'imageLightbox',
                      label: 'Linking to an image',
                      setup: function setup(data) {
                        var link = getSelectedLink();

                        if (link !== null && typeof data.target !== 'undefined') {
                          if (data.target.name == "lightbox" && link.data('concrete5-link-lightbox') == "image") {
                            this.setValue(1);
                          } else {
                            this.setValue(0);
                          }
                        }
                      },
                      commit: commitLightboxParams,
                      onChange: function onChange(data) {
                        if (this.getValue()) {
                          this.getDialog().getContentElement('target', 'lightboxDimensions').getElement().hide();
                        } else {
                          this.getDialog().getContentElement('target', 'lightboxDimensions').getElement().show();
                        }
                      }
                    }]
                  }, {
                    type: 'hbox',
                    id: 'lightboxDimensions',
                    children: [{
                      type: 'text',
                      widths: ['50%', '50%'],
                      labelLayout: 'horizontal',
                      label: commonLang.width,
                      id: 'lightboxWidth',
                      setup: function setup(data) {
                        var link = getSelectedLink();

                        if (link !== null && typeof data.target !== 'undefined') {
                          if (data.target.name == "lightbox" && link.hasAttribute('data-concrete5-link-lightbox-width')) {
                            this.setValue(link.data('concrete5-link-lightbox-width'));
                          } else {
                            this.setValue(null);
                          }
                        }
                      },
                      commit: commitLightboxParams
                    }, {
                      type: 'text',
                      labelLayout: 'horizontal',
                      widths: ['50%', '50%'],
                      label: commonLang.height,
                      id: 'lightboxHeight',
                      setup: function setup(data) {
                        var link = getSelectedLink();

                        if (link !== null && typeof data.target !== 'undefined') {
                          if (data.target.name == "lightbox" && link.hasAttribute('data-concrete5-link-lightbox-height')) {
                            this.setValue(link.data('concrete5-link-lightbox-height'));
                          } else {
                            this.setValue(null);
                          }
                        }
                      },
                      commit: commitLightboxParams
                    }],
                    setup: function setup() {
                      if (this.getDialog().getContentElement('target', 'imageLightbox').getValue()) {
                        this.getElement().hide();
                      } else {
                        this.getElement().show();
                      }
                    }
                  }]
                }],
                setup: function setup() {
                  if (!this.getDialog().getContentElement('info', 'linkType')) {
                    this.getElement().hide();
                  }

                  if (this.getDialog().getContentElement('target', 'linkTargetType').getValue() != 'lightbox') {
                    this.getElement().hide();
                  }
                }
              });
            }

            targetSelect.onChange = CKEDITOR.tools.override(targetSelect.onChange, function (original) {
              return function () {
                var dialog = this.getDialog();
                var lightboxFeatures = dialog.getContentElement('target', 'lightboxFeatures').getElement();

                if (this.getValue() == 'lightbox' && !this._.selectedElement) {
                  lightboxFeatures.show();
                } else {
                  lightboxFeatures.hide();
                } // Let the original link dialog insert the link into the text.
                // We can't really customize this code, so we need to let it run


                original.call(this);
              };
            });

            targetSelect.setup = function (data) {
              if (data.target) {
                // the plugin checks from a list of allowed target types (so not lightbox)
                // and if not found sets target type to frame by default
                // so we need to revert it to lightbox if the name is lightbox
                if (data.target.name == "lightbox") {
                  data.target.type = data.target.name;
                }

                this.setValue(data.target.type || 'notSet');
              }

              this.onChange.call(this);
            }; // When the type select box is supposed to save its value


            targetSelect.commit = function (data) {
              if (!data.target) {
                data.target = {};
              }

              data.target.type = this.getValue();
            }; // When OK is pressed in the dialog. In some cases we need to
            // post-process the link we are inserting.


            dialogDefinition.onOk = CKEDITOR.tools.override(dialogDefinition.onOk, function (original) {
              return function () {
                var data = {};
                var removed = {}; // Collect data from fields.

                this.commitContent(data); // Let the original link dialog insert the link into the text.
                // We can't really customize this code, so we need to make our
                // changes afterwards

                original.call(this);
                var link = getSelectedLink();

                if (link !== null) {
                  if (data.target.type == "lightbox") {
                    if (data.target.imageLightbox) {
                      link.data('concrete5-link-lightbox', 'image');
                      removed = {
                        'data-concrete5-link-lightbox-width': 1,
                        'data-concrete5-link-lightbox-height': 1
                      };
                    } else {
                      link.data('concrete5-link-lightbox', 'iframe');

                      if (data.target.lightboxWidth && data.target.lightboxHeight) {
                        link.data('concrete5-link-lightbox-width', data.target.lightboxWidth);
                        link.data('concrete5-link-lightbox-height', data.target.lightboxHeight);
                      } else {
                        removed = {
                          'data-concrete5-link-lightbox-width': 1,
                          'data-concrete5-link-lightbox-height': 1
                        };
                      }
                    }
                  } else {
                    removed = {
                      'data-concrete5-link-lightbox': 1,
                      'data-concrete5-link-lightbox-width': 1,
                      'data-concrete5-link-lightbox-height': 1
                    };
                  }

                  link.removeAttributes(removed);
                }
              };
            });
          } // if target type select exists

        } // if dialog name is title

      });
    }
  });
})();

/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete/normalizeonchange.js":
/*!*********************************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete/normalizeonchange.js ***!
  \*********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  CKEDITOR.plugins.add('normalizeonchange', {
    init: function init(editor) {
      CKEDITOR.on('instanceReady', function (ck) {
        ck.editor.on("change", function (e) {
          var sel = ck.editor.getSelection();

          if (sel) {
            var selected = sel.getStartElement();
            if (selected && selected.$) sel.getStartElement().$.normalize();
          }
        });
      });
    }
  });
})();

/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete/styles.js":
/*!**********************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete/styles.js ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  CKEDITOR.plugins.add('concrete5styles', {
    requires: ['widget', 'stylescombo', 'menubutton'],
    init: function init(editor) {},
    afterInit: function afterInit(editor) {
      var plugin = this;
      /**
       * Function taken largely from the htmlbuttons plugin
       */

      function createCommand(definition) {
        return {
          exec: function exec(editor) {
            var strToLook = '> </',
                code = definition.html; // Check to see if we have selected text:

            var sel = editor.getSelection(),
                selectedText = sel && sel.getSelectedText();

            if (code.indexOf(strToLook) != -1 && selectedText) {
              // Build list of block elements to be replaced
              var blockElems = ['address', 'article', 'aside', 'audio', 'blockquote', 'canvas', 'dd', 'div', 'dl', 'fieldset', 'figcaption', 'figure', 'figcaption', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'noscript', 'ol', 'output', 'p', 'pre', 'section', 'span', 'table', 'tfoot', 'ul', 'video']; // Get HTML and Text from selection

              var ranges = sel.getRanges();
              var el = new CKEDITOR.dom.element('div');
              var i;

              for (i = 0, len = ranges.length; i < len; ++i) {
                var range = ranges[i],
                    bookmark = range.createBookmark2();
                el.append(range.cloneContents());
                range.moveToBookmark(bookmark);
                range.select();
              }

              var selectedHtml = el.getHtml(); // Replace block elements from html

              for (i = 0; i < blockElems.length; i++) {
                var pattern = '(<' + blockElems[i] + '[^>]*>|<\/' + blockElems[i] + '>)';
                var re = new RegExp(pattern, 'gi');
                selectedHtml = selectedHtml.replace(re, '');
              } // Do the actual replacing


              code = code.replace(strToLook, '>' + selectedHtml + '</');
            }

            editor.insertHtml(code);
          }
        };
      }
      /**
       * Function taken largely from the htmlbuttons plugin
       */


      function createMenuButton(definition) {
        var itemsConfig = definition.items;
        var items = {}; // add menuitem from config.itemlist

        for (var i = 0; i < itemsConfig.length; i++) {
          var item = itemsConfig[i];
          var commandName = item.name;
          editor.addCommand(commandName, createCommand(item));
          items[commandName] = {
            label: item.title,
            command: commandName,
            group: definition.name,
            role: 'menuitem'
          };
        }

        editor.addMenuGroup(definition.name, 1);
        editor.addMenuItems(items);
        editor.ui.add(definition.name, CKEDITOR.UI_MENUBUTTON, {
          label: definition.title,
          icon: plugin.path + '/icons/' + definition.icon,
          toolbar: definition.toolbar || 'insert',
          onMenu: function onMenu() {
            var activeItems = {};

            for (var item in items) {
              activeItems[item] = CKEDITOR.TRISTATE_OFF;
            }

            return activeItems;
          }
        });
      }

      var buttons = {
        name: 'snippets',
        icon: 'snippet.png',
        title: 'Snippets',
        items: []
      };

      if (editor.config.snippets) {
        $.each(editor.config.snippets, function (i, snippet) {
          editor.widgets.add(snippet.scsHandle, {
            template: snippet.scsName
          });
          var button = {};
          button.name = snippet.scsHandle;
          button.icon = 'snippet.png';
          button.title = snippet.scsName;
          button.html = '<span class="ccm-content-editor-snippet" contenteditable="false" data-scsHandle="' + snippet.scsHandle + '">' + snippet.scsName + '</span>';
          buttons.items.push(button);
        });
        createMenuButton(buttons, buttons.name);
      }

      if (editor.config.classes) {
        var additionalStyles = [];
        $.each(editor.config.classes, function () {
          var style = {};
          style.name = this.title;

          if (typeof this.element !== 'undefined') {
            style.element = this.element;
          } else if (typeof this.forceBlock !== 'undefined' && this.forceBlock == 1) {
            style.element = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'];
          } else {
            style.element = 'span';
          }

          if (typeof this.spanClass !== 'undefined') {
            style.attributes = {
              'class': this.spanClass
            };
          }

          if (typeof this.attributes !== 'undefined') {
            style.attributes = this.attributes;
          }

          if (typeof this.styles !== 'undefined') {
            style.styles = this.styles;
          }

          if (this.type === 'widget' && typeof this.widget !== 'undefined') {
            style.type = 'widget';
            style.widget = this.widget;
          }

          additionalStyles.push(style);
        });
        editor.fire('stylesSet', {
          styles: additionalStyles
        });
      }
    }
  });
})();

/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/js/concrete/upload-image.js":
/*!****************************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/js/concrete/upload-image.js ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  CKEDITOR.plugins.add('concrete5uploadimage', {
    requires: 'uploadimage',
    init: function init(editor) {
      editor.on('fileUploadRequest', function (evt) {
        var fileLoader = evt.data.fileLoader;
        var xhr = fileLoader.xhr;
        var formData = new FormData();
        formData.append('ccm_token', CCM_SECURITY_TOKEN);
        formData.append('files[]', fileLoader.file, fileLoader.fileName);
        xhr.send(formData);
        evt.stop();
      });
      editor.on('fileUploadResponse', function (evt) {
        evt.stop();
        var data = evt.data,
            xhr = data.fileLoader.xhr;

        if (xhr.status == 200) {
          var respObj = jQuery.parseJSON(xhr.responseText);

          if (respObj.files.length > 0) {
            data.url = respObj.files[0].urlInline;
          }
        } else {
          data.message = xhr.responseText;
          evt.cancel();
        }
      });
    }
  });
})();

/***/ }),

/***/ "../../concretecms-bedrock/assets/ckeditor/scss/concrete.scss":
/*!*******************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/scss/concrete.scss ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../concrete5/build/node_modules/css-loader!../../../../concrete5/build/node_modules/postcss-loader/src??ref--11-2!../../../../concrete5/build/node_modules/sass-loader/dist/cjs.js??ref--11-3!./concrete.scss */ "./node_modules/css-loader/index.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!../../concretecms-bedrock/assets/ckeditor/scss/concrete.scss");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../concrete5/build/node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "../../concretecms-bedrock/node_modules/ckeditor4/adapters/jquery.js":
/*!**************************************************************************************************!*\
  !*** /Users/andrewembler/Projects/concretecms-bedrock/node_modules/ckeditor4/adapters/jquery.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

﻿/*
 Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
*/
(function(a){if("undefined"==typeof a)throw Error("jQuery should be loaded before CKEditor jQuery adapter.");if("undefined"==typeof CKEDITOR)throw Error("CKEditor should be loaded before CKEditor jQuery adapter.");CKEDITOR.config.jqueryOverrideVal="undefined"==typeof CKEDITOR.config.jqueryOverrideVal?!0:CKEDITOR.config.jqueryOverrideVal;a.extend(a.fn,{ckeditorGet:function(){var a=this.eq(0).data("ckeditorInstance");if(!a)throw"CKEditor is not initialized yet, use ckeditor() with a callback.";return a},
ckeditor:function(g,e){if(!CKEDITOR.env.isCompatible)throw Error("The environment is incompatible.");if(!a.isFunction(g)){var m=e;e=g;g=m}var k=[];e=e||{};this.each(function(){var b=a(this),c=b.data("ckeditorInstance"),f=b.data("_ckeditorInstanceLock"),h=this,l=new a.Deferred;k.push(l.promise());if(c&&!f)g&&g.apply(c,[this]),l.resolve();else if(f)c.once("instanceReady",function(){setTimeout(function d(){c.element?(c.element.$==h&&g&&g.apply(c,[h]),l.resolve()):setTimeout(d,100)},0)},null,null,9999);
else{if(e.autoUpdateElement||"undefined"==typeof e.autoUpdateElement&&CKEDITOR.config.autoUpdateElement)e.autoUpdateElementJquery=!0;e.autoUpdateElement=!1;b.data("_ckeditorInstanceLock",!0);c=a(this).is("textarea")?CKEDITOR.replace(h,e):CKEDITOR.inline(h,e);b.data("ckeditorInstance",c);c.on("instanceReady",function(e){var d=e.editor;setTimeout(function n(){if(d.element){e.removeListener();d.on("dataReady",function(){b.trigger("dataReady.ckeditor",[d])});d.on("setData",function(a){b.trigger("setData.ckeditor",
[d,a.data])});d.on("getData",function(a){b.trigger("getData.ckeditor",[d,a.data])},999);d.on("destroy",function(){b.trigger("destroy.ckeditor",[d])});d.on("save",function(){a(h.form).submit();return!1},null,null,20);if(d.config.autoUpdateElementJquery&&b.is("textarea")&&a(h.form).length){var c=function(){b.ckeditor(function(){d.updateElement()})};a(h.form).submit(c);a(h.form).bind("form-pre-serialize",c);b.bind("destroy.ckeditor",function(){a(h.form).unbind("submit",c);a(h.form).unbind("form-pre-serialize",
c)})}d.on("destroy",function(){b.removeData("ckeditorInstance")});b.removeData("_ckeditorInstanceLock");b.trigger("instanceReady.ckeditor",[d]);g&&g.apply(d,[h]);l.resolve()}else setTimeout(n,100)},0)},null,null,9999)}});var f=new a.Deferred;this.promise=f.promise();a.when.apply(this,k).then(function(){f.resolve()});this.editor=this.eq(0).data("ckeditorInstance");return this}});CKEDITOR.config.jqueryOverrideVal&&(a.fn.val=CKEDITOR.tools.override(a.fn.val,function(g){return function(e){if(arguments.length){var m=
this,k=[],f=this.each(function(){var b=a(this),c=b.data("ckeditorInstance");if(b.is("textarea")&&c){var f=new a.Deferred;c.setData(e,function(){f.resolve()});k.push(f.promise());return!0}return g.call(b,e)});if(k.length){var b=new a.Deferred;a.when.apply(this,k).done(function(){b.resolveWith(m)});return b.promise()}return f}var f=a(this).eq(0),c=f.data("ckeditorInstance");return f.is("textarea")&&c?c.getData():g.call(f)}}))})(window.jQuery);

/***/ }),

/***/ "./assets/cms.scss":
/*!*************************!*\
  !*** ./assets/cms.scss ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./assets/themes/concrete/scss/main.scss":
/*!***********************************************!*\
  !*** ./assets/themes/concrete/scss/main.scss ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./assets/themes/dashboard/scss/main.scss":
/*!************************************************!*\
  !*** ./assets/themes/dashboard/scss/main.scss ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./assets/themes/elemental/scss/main.scss":
/*!************************************************!*\
  !*** ./assets/themes/elemental/scss/main.scss ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!../../concretecms-bedrock/assets/ckeditor/scss/concrete.scss":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/postcss-loader/src??ref--11-2!./node_modules/sass-loader/dist/cjs.js??ref--11-3!/Users/andrewembler/Projects/concretecms-bedrock/assets/ckeditor/scss/concrete.scss ***!
  \********************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../concrete5/build/node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".cke_toolgroup a.cke_button__concrete_save {\n  background-color: #337ab7;\n  background-image: linear-gradient(to bottom, #337ab7, #336fad);\n  text-shadow: none;\n}\n.cke_toolgroup a.cke_button__concrete_save:hover {\n  background: #286090 !important;\n  background-image: linear-gradient(to bottom, #336fad, #315f9b) !important;\n}\n.cke_toolgroup .cke_button__concrete_save .cke_button__concrete_save_label,\n.cke_toolgroup .cke_button__concrete_cancel .cke_button__concrete_cancel_label {\n  display: inline-block;\n  padding-left: 4px;\n  padding-right: 4px;\n}\n.cke_toolgroup .cke_button__concrete_save .cke_button__concrete_save_label {\n  color: #fff;\n  text-shadow: none;\n}\n.cke_toolgroup .cke_button__concrete_save .cke_button__concrete_save_icon,\n.cke_toolgroup .cke_button__concrete_cancel .cke_button__concrete_cancel_icon {\n  display: none;\n}\n\n.cke_editable:before,\n.cke_editable:after {\n  content: \"\";\n  display: table;\n}\n\n.cke_editable:after {\n  clear: both;\n}", ""]);

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
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
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
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


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
  }

	// blank or null?
	if (!css || typeof css !== "string") {
	  return css;
  }

  var baseUrl = location.protocol + "//" + location.host;
  var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");

	// convert each url(...)
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
	var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function(fullMatch, origUrl) {
		// strip quotes (if they exist)
		var unquotedOrigUrl = origUrl
			.trim()
			.replace(/^"(.*)"$/, function(o, $1){ return $1; })
			.replace(/^'(.*)'$/, function(o, $1){ return $1; });

		// already a full url? no change
		if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(unquotedOrigUrl)) {
		  return fullMatch;
		}

		// convert the url to a full url
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
		}

		// send back the fixed url(...)
		return "url(" + JSON.stringify(newUrl) + ")";
	});

	// send back the fixed css
	return fixedCss;
};


/***/ }),

/***/ 0:
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** multi ./node_modules/concretecms-bedrock/assets/ckeditor/js/concrete.js ./node_modules/concretecms-bedrock/assets/ckeditor/scss/concrete.scss ./assets/cms.scss ./assets/themes/concrete/scss/main.scss ./assets/themes/elemental/scss/main.scss ./assets/themes/dashboard/scss/main.scss ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /Users/andrewembler/Projects/concrete5/build/node_modules/concretecms-bedrock/assets/ckeditor/js/concrete.js */"../../concretecms-bedrock/assets/ckeditor/js/concrete.js");
__webpack_require__(/*! /Users/andrewembler/Projects/concrete5/build/node_modules/concretecms-bedrock/assets/ckeditor/scss/concrete.scss */"../../concretecms-bedrock/assets/ckeditor/scss/concrete.scss");
__webpack_require__(/*! /Users/andrewembler/Projects/concrete5/build/assets/cms.scss */"./assets/cms.scss");
__webpack_require__(/*! /Users/andrewembler/Projects/concrete5/build/assets/themes/concrete/scss/main.scss */"./assets/themes/concrete/scss/main.scss");
__webpack_require__(/*! /Users/andrewembler/Projects/concrete5/build/assets/themes/elemental/scss/main.scss */"./assets/themes/elemental/scss/main.scss");
module.exports = __webpack_require__(/*! /Users/andrewembler/Projects/concrete5/build/assets/themes/dashboard/scss/main.scss */"./assets/themes/dashboard/scss/main.scss");


/***/ })

/******/ });