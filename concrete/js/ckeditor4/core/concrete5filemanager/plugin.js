(function () {
    CKEDITOR.plugins.add('concrete5filemanager', {
        requires: 'filebrowser',
        init: function () {
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