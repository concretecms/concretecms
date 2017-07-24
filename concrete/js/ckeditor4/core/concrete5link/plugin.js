(function() {
    CKEDITOR.plugins.add('concrete5link', {
        requires: 'link',
        init: function(editor) {

            CKEDITOR.on('dialogDefinition', function(ev) {
                // Take the dialog name and its definition from the event data.
                var dialogName = ev.data.name;
                var dialogDefinition = ev.data.definition;
                var commonLang = editor.lang.common;
                var linkLang = editor.lang.link;

                var commitParams = function(page, data) {
                    if (!data[page])
                        data[page] = {};

                    data[page][this.id] = this.getValue() || '';
                };

                var commitLightboxParams = function(data) {
                    return commitParams.call(this, 'target', data);
                };

                var getSelectedLink = function() {
                    return CKEDITOR.plugins.link.getSelectedLink(editor);
                };
                // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
                if (dialogName == 'link') {
                    // Get a reference to the "Link Info" tab.
                    var infoTab = dialogDefinition.getContents('info');
                    if (infoTab.get('sitemapBrowse') === null) {
                        infoTab.add({
                                type: 'button',
                                id: 'sitemapBrowse',
                                label: 'Sitemap',
                                title: 'Sitemap',
                                onClick: function() {
                                    jQuery.fn.dialog.open({
                                        width: '90%',
                                        height: '70%',
                                        modal: false,
                                        title: ccmi18n_sitemap.choosePage,
                                        href: CCM_TOOLS_PATH + '/sitemap_search_selector'
                                    });
                                    ConcreteEvent.unsubscribe('SitemapSelectPage');
                                    ConcreteEvent.subscribe('SitemapSelectPage', function(e, data) {
                                        jQuery.fn.dialog.closeTop();
                                        var element = dialogDefinition.dialog.getContentElement('info', 'url');
                                        if (element) {
                                            element.setValue(CCM_APPLICATION_URL + '/index.php?cID=' + data.cID);
                                        }
                                    });
                                }
                            },
                            'browse'
                        );
                    }
                    var targetTab = dialogDefinition.getContents('target');
                    if (targetTab.get('linkTargetType') !== null) {
                        // add the lightbox option to the target type dropdown
                        var targetSelect = targetTab.get('linkTargetType');
                        targetSelect.items.splice(3, 0, ["<lightbox>", "lightbox"]);
                        targetSelect.items.join();

                        // Add the UI that is shown when the user selects our new target type
                        // option from the select box.
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
                                            setup: function(data) {
                                                var link = getSelectedLink();
                                                if (link !== null) {
                                                    if (data.target.name == "lightbox" && link.data('concrete5-link-lightbox') == "image") {
                                                        this.setValue(1);
                                                    } else {
                                                        this.setValue(0);
                                                    }
                                                }

                                            },
                                            commit: commitLightboxParams,
                                            onChange: function(data) {
                                                if (this.getValue()) {
                                                    this.getDialog().getContentElement('target', 'lightboxDimensions').getElement().hide();
                                                } else {
                                                    this.getDialog().getContentElement('target', 'lightboxDimensions').getElement().show();
                                                }
                                            }
                                        }]
                                    },
                                    {
                                        type: 'hbox',
                                        id: 'lightboxDimensions',
                                        children: [{
                                                type: 'text',
                                                widths: ['50%', '50%'],
                                                labelLayout: 'horizontal',
                                                label: commonLang.width,
                                                id: 'lightboxWidth',
                                                setup: function(data) {
                                                    var link = getSelectedLink();
                                                    if (link !== null) {
                                                        if (data.target.name == "lightbox" && link.hasAttribute('data-concrete5-link-lightbox-width')) {
                                                            this.setValue(link.data('concrete5-link-lightbox-width'));
                                                        } else {
                                                            this.setValue(null);
                                                        }
                                                    }

                                                },
                                                commit: commitLightboxParams
                                            },
                                            {
                                                type: 'text',
                                                labelLayout: 'horizontal',
                                                widths: ['50%', '50%'],
                                                label: commonLang.height,
                                                id: 'lightboxHeight',
                                                setup: function(data) {
                                                    var link = getSelectedLink();
                                                    if (link !== null) {
                                                        if (data.target.name == "lightbox" && link.hasAttribute('data-concrete5-link-lightbox-height')) {
                                                            this.setValue(link.data('concrete5-link-lightbox-height'));
                                                        } else {
                                                            this.setValue(null);
                                                        }
                                                    }

                                                },
                                                commit: commitLightboxParams
                                            }
                                        ],
                                        setup: function() {
                                            if (this.getDialog().getContentElement('target', 'imageLightbox').getValue()) {
                                                this.getElement().hide()
                                            } else {
                                                this.getElement().show();
                                            }
                                        }
                                    },
                                ]
                            }],
                            setup: function() {
                                if (!this.getDialog().getContentElement('info', 'linkType')) {
                                    this.getElement().hide();
                                }
                                if (this.getDialog().getContentElement('target', 'linkTargetType').getValue() != 'lightbox') {
                                    this.getElement().hide();
                                }
                            }
                        });

                        targetSelect.onChange = CKEDITOR.tools.override(targetSelect.onChange, function(original) {
                            return function() {
                                var dialog = this.getDialog();
                                var lightboxFeatures = dialog.getContentElement('target', 'lightboxFeatures').getElement();
                                if ((this.getValue() == 'lightbox') && !this._.selectedElement) {
                                    lightboxFeatures.show();
                                } else {
                                    lightboxFeatures.hide();
                                }

                                // Let the original link dialog insert the link into the text.
                                // We can't really customize this code, so we need to let it run
                                original.call(this);
                            };
                        });

                        targetSelect.setup = function(data) {
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
                        targetSelect.commit = function(data) {
                            if (!data.target) {
                                data.target = {};
                            }
                            data.target.type = this.getValue();

                        };

                        // When OK is pressed in the dialog. In some cases we need to
                        // post-process the link we are inserting.
                        dialogDefinition.onOk = CKEDITOR.tools.override(dialogDefinition.onOk, function(original) {
                            return function() {

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
