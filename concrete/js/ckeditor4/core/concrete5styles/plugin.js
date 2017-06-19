(function () {
    CKEDITOR.plugins.add('concrete5styles', {
        requires: ['widget', 'stylescombo', 'menubutton'],
        init: function (editor) {

        },

        afterInit: function (editor) {
            var plugin = this;
            /**
             * Function taken largely from the htmlbuttons plugin
             */
            function createCommand(definition) {
                return {
                    exec: function (editor) {
                        var strToLook = '> </',
                            code = definition.html;

                        // Check to see if we have selected text:
                        var sel = editor.getSelection(),
                            selectedText = sel && sel.getSelectedText();

                        if (code.indexOf(strToLook) != -1 && selectedText) {
                            // Build list of block elements to be replaced
                            var blockElems = ['address', 'article', 'aside', 'audio', 'blockquote', 'canvas', 'dd', 'div', 'dl', 'fieldset',
                                'figcaption', 'figure', 'figcaption', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup',
                                'hr', 'noscript', 'ol', 'output', 'p', 'pre', 'section', 'span', 'table', 'tfoot', 'ul', 'video'];

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
                    onMenu: function () {
                        var activeItems = {};

                        for (var item in items)
                            activeItems[item] = CKEDITOR.TRISTATE_OFF;

                        return activeItems;
                    }
                });
            }

            /**
             * async is set to false in order to make sure that we load our snippets prior to the toolbar menu getting created
             * otherwise the button never shows up
             */
            $.ajax({
                'type': 'get',
                /*
                'async': false,
                // Note - do we really need this? It introduces massive slowdown when
                // multiple editors are used at once.
                */
                'dataType': 'json',
                'url': CCM_DISPATCHER_FILENAME + '/ccm/system/backend/editor_data',
                'data': {
                    'ccm_token': CCM_EDITOR_SECURITY_TOKEN,
                    'cID': CCM_CID
                },

                success: function (response) {
                    var buttons = {
                        name: 'snippets',
                        icon: 'snippet.png',
                        title: 'Snippets',
                        items: []
                    };
                    $.each(response.snippets, function (i, snippet) {
                        editor.widgets.add(snippet.scsHandle, {
                            template: snippet.scsName
                        });
                        var button = {};
                        button.name = snippet.scsHandle;
                        button.icon = 'snippet.png';
                        button.title = snippet.scsName;
                        button.html =
                            '<span class="ccm-content-editor-snippet" contenteditable="false" data-scsHandle="' + snippet.scsHandle + '">' +
                            snippet.scsName +
                            '</span>';
                        buttons.items.push(button);
                    });
                    createMenuButton(buttons);

                    var additionalStyles = [];
                    $.each(response.classes, function () {
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
                            style.attributes = {'class': this.spanClass};
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
                    editor.fire('stylesSet', {styles: additionalStyles});
                }
            });

        }
    });
})();
