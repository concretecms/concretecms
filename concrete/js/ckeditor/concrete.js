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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 319);
/******/ })
/************************************************************************/
/******/ ({

/***/ 319:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(320);


/***/ }),

/***/ 320:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__normalizeonchange__ = __webpack_require__(321);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__normalizeonchange___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__normalizeonchange__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__inline__ = __webpack_require__(322);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__inline___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__inline__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__link__ = __webpack_require__(323);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__link___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__link__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__file_manager__ = __webpack_require__(324);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__file_manager___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3__file_manager__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__styles__ = __webpack_require__(325);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__styles___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4__styles__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__upload_image__ = __webpack_require__(326);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__upload_image___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5__upload_image__);
// Import the concrete5 CKEditor plugins







/***/ }),

/***/ 321:
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

/***/ 322:
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

/***/ 323:
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
                };
                // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
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
                        }

                        // Add the UI that is shown when the user selects our new target type
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
                                }

                                // Let the original link dialog insert the link into the text.
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
                        };

                        // When the type select box is supposed to save its value
                        targetSelect.commit = function (data) {
                            if (!data.target) {
                                data.target = {};
                            }
                            data.target.type = this.getValue();
                        };

                        // When OK is pressed in the dialog. In some cases we need to
                        // post-process the link we are inserting.
                        dialogDefinition.onOk = CKEDITOR.tools.override(dialogDefinition.onOk, function (original) {
                            return function () {

                                var data = {};
                                var removed = {};
                                // Collect data from fields.
                                this.commitContent(data);
                                // Let the original link dialog insert the link into the text.
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

/***/ 324:
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
                        var dialog = this.getDialog();ConcreteFileManager.launchDialog(function (data) {
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

/***/ 325:
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
                            code = definition.html;

                        // Check to see if we have selected text:
                        var sel = editor.getSelection(),
                            selectedText = sel && sel.getSelectedText();

                        if (code.indexOf(strToLook) != -1 && selectedText) {
                            // Build list of block elements to be replaced
                            var blockElems = ['address', 'article', 'aside', 'audio', 'blockquote', 'canvas', 'dd', 'div', 'dl', 'fieldset', 'figcaption', 'figure', 'figcaption', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'noscript', 'ol', 'output', 'p', 'pre', 'section', 'span', 'table', 'tfoot', 'ul', 'video'];

                            // Get HTML and Text from selection
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
                            var selectedHtml = el.getHtml();

                            // Replace block elements from html
                            for (i = 0; i < blockElems.length; i++) {
                                var pattern = '(<' + blockElems[i] + '[^>]*>|<\/' + blockElems[i] + '>)';
                                var re = new RegExp(pattern, 'gi');
                                selectedHtml = selectedHtml.replace(re, '');
                            }

                            // Do the actual replacing
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
                var items = {};

                // add menuitem from config.itemlist
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
                        }return activeItems;
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
                        style.attributes = { 'class': this.spanClass };
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
                editor.fire('stylesSet', { styles: additionalStyles });
            }
        }
    });
})();

/***/ }),

/***/ 326:
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
                    var files = jQuery.parseJSON(xhr.responseText);
                    if (files.length > 0) {
                        data.url = files[0].urlInline;
                    }
                } else {
                    data.message = xhr.responseText;
                    evt.cancel();
                }
            });
        }
    });
})();

/***/ })

/******/ });