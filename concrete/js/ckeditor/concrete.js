<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/file-manager.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/file-manager.js ***!
  \***************************************************************************************/
/***/ (() => {

/* eslint-disable eqeqeq */
(function () {
  CKEDITOR.plugins.add('concretefilemanager', {
    requires: 'filebrowser',
    init: function init() {
      CKEDITOR.on('dialogDefinition', function (event) {
        var editor = event.editor;
        var dialogDefinition = event.data.definition;
        var tabContent = dialogDefinition.contents.length;
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

/***/ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/inline.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/inline.js ***!
  \*********************************************************************************/
/***/ (() => {

/* eslint-disable eqeqeq */
CKEDITOR.plugins.add('concreteinline', {
  init: function init(editor) {
    // Save plugin is for replace mode only.
    if (editor.elementMode != CKEDITOR.ELEMENT_MODE_INLINE) {
      return;
    }
    editor.addCommand('c5save', {
      exec: function exec(editor) {
        $('#' + editor.element.$.id + '_content').val(editor.getData());
        ConcreteEvent.fire('EditModeBlockSaveInline');
        editor.destroy();
      }
    });
    editor.addCommand('c5cancel', {
      exec: function exec(editor) {
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

/***/ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/link.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/link.js ***!
  \*******************************************************************************/
/***/ (() => {

/* eslint-disable no-new, no-unused-vars, camelcase, no-useless-call, eqeqeq */
/* global ccmi18n_editor */
(function () {
  CKEDITOR.plugins.add('concretelink', {
    requires: 'link',
    init: function init(editor) {
      CKEDITOR.on('dialogDefinition', function (ev) {
        // Take the dialog name and its definition from the event data.
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;
        var commonLang = editor.lang.common;
        var commitParams = function commitParams(page, data) {
          if (!data[page]) {
            data[page] = {};
          }
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
              label: ccmi18n_editor.sitemap,
              title: ccmi18n_editor.sitemap,
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
            if (targetSelect.items[3][1] != 'lightbox') {
              targetSelect.items.splice(3, 0, ['<lightbox>', 'lightbox']);
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
                  label: ccmi18n_editor.lightboxFeatures,
                  children: [{
                    type: 'hbox',
                    children: [{
                      type: 'checkbox',
                      id: 'imageLightbox',
                      label: ccmi18n_editor.imageLink,
                      setup: function setup(data) {
                        var link = getSelectedLink();
                        if (link !== null && typeof data.target !== 'undefined') {
                          if (data.target.name == 'lightbox' && link.data('concrete-link-lightbox') == 'image') {
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
                          if (data.target.name == 'lightbox' && link.hasAttribute('data-concrete-link-lightbox-width')) {
                            this.setValue(link.data('concrete-link-lightbox-width'));
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
                          if (data.target.name == 'lightbox' && link.hasAttribute('data-concrete-link-lightbox-height')) {
                            this.setValue(link.data('concrete-link-lightbox-height'));
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
                if (data.target.name == 'lightbox') {
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
                  if (data.target.type == 'lightbox') {
                    if (data.target.imageLightbox) {
                      link.data('concrete-link-lightbox', 'image');
                      removed = {
                        'data-concrete-link-lightbox-width': 1,
                        'data-concrete-link-lightbox-height': 1
                      };
                    } else {
                      link.data('concrete-link-lightbox', 'iframe');
                      if (data.target.lightboxWidth && data.target.lightboxHeight) {
                        link.data('concrete-link-lightbox-width', data.target.lightboxWidth);
                        link.data('concrete-link-lightbox-height', data.target.lightboxHeight);
                      } else {
                        removed = {
                          'data-concrete-link-lightbox-width': 1,
                          'data-concrete-link-lightbox-height': 1
                        };
                      }
                    }
                  } else {
                    removed = {
                      'data-concrete-link-lightbox': 1,
                      'data-concrete-link-lightbox-width': 1,
                      'data-concrete-link-lightbox-height': 1
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

/***/ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/normalizeonchange.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/normalizeonchange.js ***!
  \********************************************************************************************/
/***/ (() => {

(function () {
  CKEDITOR.plugins.add('normalizeonchange', {
    init: function init(editor) {
      CKEDITOR.on('instanceReady', function (ck) {
        ck.editor.on('change', function (e) {
          var sel = ck.editor.getSelection();
          if (sel) {
            var selected = sel.getStartElement();
            if (selected && selected.$) {
              sel.getStartElement().$.normalize();
            }
          }
        });
      });
    }
  });
})();

/***/ }),

/***/ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/styles.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/styles.js ***!
  \*********************************************************************************/
/***/ (() => {

/* eslint-disable no-new, no-unused-vars, camelcase, new-cap, eqeqeq */
/* global ccmi18n_editor */
(function () {
  CKEDITOR.plugins.add('concretestyles', {
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
            var strToLook = '> </';
            var code = definition.html;

            // Check to see if we have selected text:
            var sel = editor.getSelection();
            var selectedText = sel && sel.getSelectedText();
            if (code.indexOf(strToLook) != -1 && selectedText) {
              // Build list of block elements to be replaced
              var blockElems = ['address', 'article', 'aside', 'audio', 'blockquote', 'canvas', 'dd', 'div', 'dl', 'fieldset', 'figcaption', 'figure', 'figcaption', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'noscript', 'ol', 'output', 'p', 'pre', 'section', 'span', 'table', 'tfoot', 'ul', 'video'];

              // Get HTML and Text from selection
              var ranges = sel.getRanges();
              var el = new CKEDITOR.dom.element('div');
              for (var i = 0, len = ranges.length; i < len; ++i) {
                var range = ranges[i];
                var bookmark = range.createBookmark2();
                el.append(range.cloneContents());
                range.moveToBookmark(bookmark);
                range.select();
              }
              var selectedHtml = el.getHtml();

              // Replace block elements from html
              for (var _i = 0; _i < blockElems.length; _i++) {
                var pattern = '(<' + blockElems[_i] + '[^>]*>|</' + blockElems[_i] + '>)';
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
            }
            return activeItems;
          }
        });
      }
      var buttons = {
        name: 'snippets',
        icon: 'snippet.png',
        title: ccmi18n_editor.snippets,
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
              "class": this.spanClass
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

/***/ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/upload-image.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/upload-image.js ***!
  \***************************************************************************************/
/***/ (() => {

/* eslint-disable eqeqeq */

(function () {
  CKEDITOR.plugins.add('concreteuploadimage', {
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
        var data = evt.data;
        var xhr = data.fileLoader.xhr;
        if (xhr.status == 200) {
          var respObj = jQuery.parseJSON(xhr.responseText);
          if (respObj.error) {
            data.message = respObj.errors.join();
            evt.cancel();
          } else if (respObj.files.length > 0) {
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

/***/ "./node_modules/ckeditor4/adapters/jquery.js":
/*!***************************************************!*\
  !*** ./node_modules/ckeditor4/adapters/jquery.js ***!
  \***************************************************/
/***/ (() => {

/*
 Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
*/
(function (a) {
  if ("undefined" == typeof a) throw Error("jQuery should be loaded before CKEditor jQuery adapter.");
  if ("undefined" == typeof CKEDITOR) throw Error("CKEditor should be loaded before CKEditor jQuery adapter.");
  CKEDITOR.config.jqueryOverrideVal = "undefined" == typeof CKEDITOR.config.jqueryOverrideVal ? !0 : CKEDITOR.config.jqueryOverrideVal;
  a.extend(a.fn, {
    ckeditorGet: function ckeditorGet() {
      var a = this.eq(0).data("ckeditorInstance");
      if (!a) throw "CKEditor is not initialized yet, use ckeditor() with a callback.";
      return a;
    },
    ckeditor: function ckeditor(g, e) {
      if (!CKEDITOR.env.isCompatible) throw Error("The environment is incompatible.");
      if ("function" !== typeof g) {
        var m = e;
        e = g;
        g = m;
      }
      var k = [];
      e = e || {};
      this.each(function () {
        var b = a(this),
          c = b.data("ckeditorInstance"),
          f = b.data("_ckeditorInstanceLock"),
          h = this,
          l = new a.Deferred();
        k.push(l.promise());
        if (c && !f) g && g.apply(c, [this]), l.resolve();else if (f) c.once("instanceReady", function () {
          setTimeout(function d() {
            c.element ? (c.element.$ == h && g && g.apply(c, [h]), l.resolve()) : setTimeout(d, 100);
          }, 0);
        }, null, null, 9999);else {
          if (e.autoUpdateElement || "undefined" == typeof e.autoUpdateElement && CKEDITOR.config.autoUpdateElement) e.autoUpdateElementJquery = !0;
          e.autoUpdateElement = !1;
          b.data("_ckeditorInstanceLock", !0);
          c = a(this).is("textarea") ? CKEDITOR.replace(h, e) : CKEDITOR.inline(h, e);
          b.data("ckeditorInstance", c);
          c.on("instanceReady", function (e) {
            var d = e.editor;
            setTimeout(function n() {
              if (d.element) {
                e.removeListener();
                d.on("dataReady", function () {
                  b.trigger("dataReady.ckeditor", [d]);
                });
                d.on("setData", function (a) {
                  b.trigger("setData.ckeditor", [d, a.data]);
                });
                d.on("getData", function (a) {
                  b.trigger("getData.ckeditor", [d, a.data]);
                }, 999);
                d.on("destroy", function () {
                  b.trigger("destroy.ckeditor", [d]);
                });
                d.on("save", function () {
                  a(h.form).trigger("submit");
                  return !1;
                }, null, null, 20);
                if (d.config.autoUpdateElementJquery && b.is("textarea") && a(h.form).length) {
                  var c = function c() {
                    b.ckeditor(function () {
                      d.updateElement();
                    });
                  };
                  a(h.form).on("submit", c);
                  a(h.form).on("form-pre-serialize", c);
                  b.on("destroy.ckeditor", function () {
                    a(h.form).off("submit", c);
                    a(h.form).off("form-pre-serialize", c);
                  });
                }
                d.on("destroy", function () {
                  b.removeData("ckeditorInstance");
                });
                b.removeData("_ckeditorInstanceLock");
                b.trigger("instanceReady.ckeditor", [d]);
                g && g.apply(d, [h]);
                l.resolve();
              } else setTimeout(n, 100);
            }, 0);
          }, null, null, 9999);
        }
      });
      var f = new a.Deferred();
      this.promise = f.promise();
      a.when.apply(this, k).then(function () {
        f.resolve();
      });
      this.editor = this.eq(0).data("ckeditorInstance");
      return this;
    }
  });
  CKEDITOR.config.jqueryOverrideVal && (a.fn.val = CKEDITOR.tools.override(a.fn.val, function (g) {
    return function (e) {
      if (arguments.length) {
        var m = this,
          k = [],
          f = this.each(function () {
            var b = a(this),
              c = b.data("ckeditorInstance");
            if (b.is("textarea") && c) {
              var f = new a.Deferred();
              c.setData(e, function () {
                f.resolve();
              });
              k.push(f.promise());
              return !0;
            }
            return g.call(b, e);
          });
        if (k.length) {
          var b = new a.Deferred();
          a.when.apply(this, k).done(function () {
            b.resolveWith(m);
          });
          return b.promise();
        }
        return f;
      }
      var f = a(this).eq(0),
        c = f.data("ckeditorInstance");
      return f.is("textarea") && c ? c.getData() : g.call(f);
    };
  }));
})(window.jQuery);

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
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!**************************************************************************!*\
  !*** ./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete.js ***!
  \**************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var ckeditor4_adapters_jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor4/adapters/jquery */ "./node_modules/ckeditor4/adapters/jquery.js");
/* harmony import */ var ckeditor4_adapters_jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(ckeditor4_adapters_jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _concrete_normalizeonchange__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./concrete/normalizeonchange */ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/normalizeonchange.js");
/* harmony import */ var _concrete_normalizeonchange__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_concrete_normalizeonchange__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _concrete_inline__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./concrete/inline */ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/inline.js");
/* harmony import */ var _concrete_inline__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_concrete_inline__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _concrete_link__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./concrete/link */ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/link.js");
/* harmony import */ var _concrete_link__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_concrete_link__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _concrete_file_manager__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./concrete/file-manager */ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/file-manager.js");
/* harmony import */ var _concrete_file_manager__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_concrete_file_manager__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _concrete_styles__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./concrete/styles */ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/styles.js");
/* harmony import */ var _concrete_styles__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_concrete_styles__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _concrete_upload_image__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./concrete/upload-image */ "./node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete/upload-image.js");
/* harmony import */ var _concrete_upload_image__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_concrete_upload_image__WEBPACK_IMPORTED_MODULE_6__);
// Import the jQuery adapter


// Import the CKEditor plugins






})();

/******/ })()
;
=======
(()=>{var e={7307:()=>{CKEDITOR.plugins.add("concretefilemanager",{requires:"filebrowser",init:function(){CKEDITOR.on("dialogDefinition",(function(e){for(var t=e.editor,n=e.data.definition,i=n.contents.length,o=0;o<i;o++){var a=n.contents[o].get("browse");null!==a&&(a.hidden=!1,a.onClick=function(){t._.filebrowserSe=this;var e=this.getDialog();ConcreteFileManager.launchDialog((function(n){jQuery.fn.dialog.showLoader(),ConcreteFileManager.getFileDetails(n.fID,(function(n){jQuery.fn.dialog.hideLoader();var i=n.files[0];"image"!=e.getName()&&"image2"!=e.getName()||"info"!=e._.currentTabId?CKEDITOR.tools.callFunction(t._.filebrowserFn,i.urlDownload):CKEDITOR.tools.callFunction(t._.filebrowserFn,i.urlInline,(function(){var t;e.dontResetSize=!0,(t=e.getContentElement("info","txtWidth"))&&t.setValue(""),(t=e.getContentElement("info","width"))&&t.setValue(""),(t=e.getContentElement("info","txtHeight"))&&t.setValue(""),(t=e.getContentElement("info","height"))&&t.setValue(""),(t=e.getContentElement("info","txtAlt"))&&t.setValue(i.title),(t=e.getContentElement("info","alt"))&&t.setValue(i.title)}))}))}))})}}))}})},6341:()=>{CKEDITOR.plugins.add("concreteinline",{init:function(e){e.elementMode==CKEDITOR.ELEMENT_MODE_INLINE&&(e.addCommand("c5save",{exec:function(e){$("#"+e.element.$.id+"_content").val(e.getData()),ConcreteEvent.fire("EditModeBlockSaveInline"),e.destroy()}}),e.addCommand("c5cancel",{exec:function(e){e.checkDirty()?ConcreteAlert.confirm(ccmi18n_editor.cancelPrompt,(function(){$.fn.dialog.closeTop(),ConcreteEvent.fire("EditModeExitInline"),e.destroy()}),"btn-danger",ccmi18n_editor.cancelPromptButton):(ConcreteEvent.fire("EditModeExitInline"),e.destroy())}}),e.ui.addButton&&(e.ui.addButton("concrete_save",{label:e.lang.common.ok,command:"c5save",toolbar:"document,0"}),e.ui.addButton("concrete_cancel",{label:e.lang.common.cancel,command:"c5cancel",toolbar:"document,1"})))}})},1449:()=>{CKEDITOR.plugins.add("concretelink",{requires:"link",init:function(e){CKEDITOR.on("dialogDefinition",(function(t){var n=t.data.name,i=t.data.definition,o=e.lang.common,a=function(e,t){t[e]||(t[e]={}),t[e][this.id]=this.getValue()||""},r=function(e){return a.call(this,"target",e)},l=function(){return CKEDITOR.plugins.link.getSelectedLink(t.editor)};if("link"==n){var s=i.getContents("info");null===s.get("sitemapBrowse")&&t.editor.config.sitemap&&s.add({type:"button",id:"sitemapBrowse",label:ccmi18n_editor.sitemap,title:ccmi18n_editor.sitemap,onClick:function(){jQuery.fn.dialog.open({width:"90%",height:"70%",modal:!1,title:ccmi18n_sitemap.choosePage,href:CCM_DISPATCHER_FILENAME+"/ccm/system/dialogs/page/sitemap_selector"}),ConcreteEvent.unsubscribe("SitemapSelectPage"),ConcreteEvent.subscribe("SitemapSelectPage",(function(e,t){jQuery.fn.dialog.closeTop();var n=i.dialog.getContentElement("info","url");n&&n.setValue(CCM_APPLICATION_URL+"/index.php?cID="+t.cID)}))}},"browse");var c=i.getContents("target");if(null!==c.get("linkTargetType")){var d=c.get("linkTargetType");"lightbox"!=d.items[3][1]&&(d.items.splice(3,0,["<lightbox>","lightbox"]),d.items.join()),null===c.get("lightboxFeatures")&&c.elements.push({type:"vbox",width:"100%",align:"center",padding:2,id:"lightboxFeatures",children:[{type:"fieldset",label:ccmi18n_editor.lightboxFeatures,children:[{type:"hbox",children:[{type:"checkbox",id:"imageLightbox",label:ccmi18n_editor.imageLink,setup:function(e){var t=l();null!==t&&void 0!==e.target&&("lightbox"==e.target.name&&"image"==t.data("concrete-link-lightbox")?this.setValue(1):this.setValue(0))},commit:r,onChange:function(e){this.getValue()?this.getDialog().getContentElement("target","lightboxDimensions").getElement().hide():this.getDialog().getContentElement("target","lightboxDimensions").getElement().show()}}]},{type:"hbox",id:"lightboxDimensions",children:[{type:"text",widths:["50%","50%"],labelLayout:"horizontal",label:o.width,id:"lightboxWidth",setup:function(e){var t=l();null!==t&&void 0!==e.target&&("lightbox"==e.target.name&&t.hasAttribute("data-concrete-link-lightbox-width")?this.setValue(t.data("concrete-link-lightbox-width")):this.setValue(null))},commit:r},{type:"text",labelLayout:"horizontal",widths:["50%","50%"],label:o.height,id:"lightboxHeight",setup:function(e){var t=l();null!==t&&void 0!==e.target&&("lightbox"==e.target.name&&t.hasAttribute("data-concrete-link-lightbox-height")?this.setValue(t.data("concrete-link-lightbox-height")):this.setValue(null))},commit:r}],setup:function(){this.getDialog().getContentElement("target","imageLightbox").getValue()?this.getElement().hide():this.getElement().show()}}]}],setup:function(){this.getDialog().getContentElement("info","linkType")||this.getElement().hide(),"lightbox"!=this.getDialog().getContentElement("target","linkTargetType").getValue()&&this.getElement().hide()}}),d.onChange=CKEDITOR.tools.override(d.onChange,(function(e){return function(){var t=this.getDialog().getContentElement("target","lightboxFeatures").getElement();"lightbox"!=this.getValue()||this._.selectedElement?t.hide():t.show(),e.call(this)}})),d.setup=function(e){e.target&&("lightbox"==e.target.name&&(e.target.type=e.target.name),this.setValue(e.target.type||"notSet")),this.onChange.call(this)},d.commit=function(e){e.target||(e.target={}),e.target.type=this.getValue()},i.onOk=CKEDITOR.tools.override(i.onOk,(function(e){return function(){var t={},n={};this.commitContent(t),e.call(this);var i=l();null!==i&&("lightbox"==t.target.type?t.target.imageLightbox?(i.data("concrete-link-lightbox","image"),n={"data-concrete-link-lightbox-width":1,"data-concrete-link-lightbox-height":1}):(i.data("concrete-link-lightbox","iframe"),t.target.lightboxWidth&&t.target.lightboxHeight?(i.data("concrete-link-lightbox-width",t.target.lightboxWidth),i.data("concrete-link-lightbox-height",t.target.lightboxHeight)):n={"data-concrete-link-lightbox-width":1,"data-concrete-link-lightbox-height":1}):n={"data-concrete-link-lightbox":1,"data-concrete-link-lightbox-width":1,"data-concrete-link-lightbox-height":1},i.removeAttributes(n))}}))}}}))}})},6823:()=>{CKEDITOR.plugins.add("normalizeonchange",{init:function(e){CKEDITOR.on("instanceReady",(function(e){e.editor.on("change",(function(t){var n=e.editor.getSelection();if(n){var i=n.getStartElement();i&&i.$&&n.getStartElement().$.normalize()}}))}))}})},9726:()=>{CKEDITOR.plugins.add("concretestyles",{requires:["widget","stylescombo","menubutton"],init:function(e){},afterInit:function(e){var t=this;function n(e){return{exec:function(t){var n="> </",i=e.html,o=t.getSelection(),a=o&&o.getSelectedText();if(-1!=i.indexOf(n)&&a){for(var r=["address","article","aside","audio","blockquote","canvas","dd","div","dl","fieldset","figcaption","figure","figcaption","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","noscript","ol","output","p","pre","section","span","table","tfoot","ul","video"],l=o.getRanges(),s=new CKEDITOR.dom.element("div"),c=0,d=l.length;c<d;++c){var g=l[c],u=g.createBookmark2();s.append(g.cloneContents()),g.moveToBookmark(u),g.select()}for(var h=s.getHtml(),m=0;m<r.length;m++){var f=new RegExp("(<"+r[m]+"[^>]*>|</"+r[m]+">)","gi");h=h.replace(f,"")}i=i.replace(n,">"+h+"</")}t.insertHtml(i)}}}var i={name:"snippets",icon:"snippet.png",title:ccmi18n_editor.snippets,items:[]};if(e.config.snippets&&($.each(e.config.snippets,(function(t,n){e.widgets.add(n.scsHandle,{template:n.scsName});var o={};o.name=n.scsHandle,o.icon="snippet.png",o.title=n.scsName,o.html='<span class="ccm-content-editor-snippet" contenteditable="false" data-scsHandle="'+n.scsHandle+'">'+n.scsName+"</span>",i.items.push(o)})),function(i){for(var o=i.items,a={},r=0;r<o.length;r++){var l=o[r],s=l.name;e.addCommand(s,n(l)),a[s]={label:l.title,command:s,group:i.name,role:"menuitem"}}e.addMenuGroup(i.name,1),e.addMenuItems(a),e.ui.add(i.name,CKEDITOR.UI_MENUBUTTON,{label:i.title,icon:t.path+"/icons/"+i.icon,toolbar:i.toolbar||"insert",onMenu:function(){var e={};for(var t in a)e[t]=CKEDITOR.TRISTATE_OFF;return e}})}(i,i.name)),e.config.classes){var o=[];$.each(e.config.classes,(function(){var e={};e.name=this.title,void 0!==this.element?e.element=this.element:void 0!==this.forceBlock&&1==this.forceBlock?e.element=["h1","h2","h3","h4","h5","h6","p"]:e.element="span",void 0!==this.spanClass&&(e.attributes={class:this.spanClass}),void 0!==this.attributes&&(e.attributes=this.attributes),void 0!==this.styles&&(e.styles=this.styles),"widget"===this.type&&void 0!==this.widget&&(e.type="widget",e.widget=this.widget),o.push(e)})),e.fire("stylesSet",{styles:o})}}})},1740:()=>{CKEDITOR.plugins.add("concreteuploadimage",{requires:"uploadimage",init:function(e){e.on("fileUploadRequest",(function(e){var t=e.data.fileLoader,n=t.xhr,i=new FormData;i.append("ccm_token",CCM_SECURITY_TOKEN),i.append("files[]",t.file,t.fileName),n.send(i),e.stop()})),e.on("fileUploadResponse",(function(e){e.stop();var t=e.data,n=t.fileLoader.xhr;if(200==n.status){var i=jQuery.parseJSON(n.responseText);i.error?(t.message=i.errors.join(),e.cancel()):i.files.length>0&&(t.url=i.files[0].urlInline)}else t.message=n.responseText,e.cancel()}))}})},9513:()=>{!function(e){if(void 0===e)throw Error("jQuery should be loaded before CKEditor jQuery adapter.");if("undefined"==typeof CKEDITOR)throw Error("CKEditor should be loaded before CKEditor jQuery adapter.");CKEDITOR.config.jqueryOverrideVal=void 0===CKEDITOR.config.jqueryOverrideVal||CKEDITOR.config.jqueryOverrideVal,e.extend(e.fn,{ckeditorGet:function(){var e=this.eq(0).data("ckeditorInstance");if(!e)throw"CKEditor is not initialized yet, use ckeditor() with a callback.";return e},ckeditor:function(t,n){if(!CKEDITOR.env.isCompatible)throw Error("The environment is incompatible.");if("function"!=typeof t){var i=n;n=t,t=i}var o=[];n=n||{},this.each((function(){var i=e(this),a=i.data("ckeditorInstance"),r=i.data("_ckeditorInstanceLock"),l=this,s=new e.Deferred;o.push(s.promise()),a&&!r?(t&&t.apply(a,[this]),s.resolve()):r?a.once("instanceReady",(function(){setTimeout((function e(){a.element?(a.element.$==l&&t&&t.apply(a,[l]),s.resolve()):setTimeout(e,100)}),0)}),null,null,9999):((n.autoUpdateElement||void 0===n.autoUpdateElement&&CKEDITOR.config.autoUpdateElement)&&(n.autoUpdateElementJquery=!0),n.autoUpdateElement=!1,i.data("_ckeditorInstanceLock",!0),a=e(this).is("textarea")?CKEDITOR.replace(l,n):CKEDITOR.inline(l,n),i.data("ckeditorInstance",a),a.on("instanceReady",(function(n){var o=n.editor;setTimeout((function a(){if(o.element){if(n.removeListener(),o.on("dataReady",(function(){i.trigger("dataReady.ckeditor",[o])})),o.on("setData",(function(e){i.trigger("setData.ckeditor",[o,e.data])})),o.on("getData",(function(e){i.trigger("getData.ckeditor",[o,e.data])}),999),o.on("destroy",(function(){i.trigger("destroy.ckeditor",[o])})),o.on("save",(function(){return e(l.form).trigger("submit"),!1}),null,null,20),o.config.autoUpdateElementJquery&&i.is("textarea")&&e(l.form).length){var r=function(){i.ckeditor((function(){o.updateElement()}))};e(l.form).on("submit",r),e(l.form).on("form-pre-serialize",r),i.on("destroy.ckeditor",(function(){e(l.form).off("submit",r),e(l.form).off("form-pre-serialize",r)}))}o.on("destroy",(function(){i.removeData("ckeditorInstance")})),i.removeData("_ckeditorInstanceLock"),i.trigger("instanceReady.ckeditor",[o]),t&&t.apply(o,[l]),s.resolve()}else setTimeout(a,100)}),0)}),null,null,9999))}));var a=new e.Deferred;return this.promise=a.promise(),e.when.apply(this,o).then((function(){a.resolve()})),this.editor=this.eq(0).data("ckeditorInstance"),this}}),CKEDITOR.config.jqueryOverrideVal&&(e.fn.val=CKEDITOR.tools.override(e.fn.val,(function(t){return function(n){if(arguments.length){var i=this,o=[],a=this.each((function(){var i=e(this),a=i.data("ckeditorInstance");if(i.is("textarea")&&a){var r=new e.Deferred;return a.setData(n,(function(){r.resolve()})),o.push(r.promise()),!0}return t.call(i,n)}));if(o.length){var r=new e.Deferred;return e.when.apply(this,o).done((function(){r.resolveWith(i)})),r.promise()}return a}var l=(a=e(this).eq(0)).data("ckeditorInstance");return a.is("textarea")&&l?l.getData():t.call(a)}})))}(window.jQuery)}},t={};function n(i){var o=t[i];if(void 0!==o)return o.exports;var a=t[i]={exports:{}};return e[i](a,a.exports,n),a.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var i in t)n.o(t,i)&&!n.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";n(9513),n(6823),n(6341),n(1449),n(7307),n(9726),n(1740)})()})();
>>>>>>> 9.3.x
