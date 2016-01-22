CKEDITOR.plugins.add('concrete5inline', {
    icons: 'cancel',

    init: function (editor) {
        // Save plugin is for replace mode only.
        if (editor.elementMode != CKEDITOR.ELEMENT_MODE_INLINE)
            return;

        editor.addCommand('c5save', {
            'exec': function (editor) {
                $('#' + editor.element.$.id + '_content').val(editor.getData());
                ConcreteEvent.fire('EditModeBlockSaveInline');
                editor.destroy();
            }
        });

        editor.addCommand('c5cancel', {
            'exec': function (editor) {
                ConcreteEvent.fire('EditModeExitInline');
                editor.destroy();
            }
        });

        if (editor.ui.addButton) {
            editor.ui.addButton('Save', {
                label: editor.lang.save.toolbar,
                command: 'c5save',
                toolbar: 'document,0'
            });
            editor.ui.addButton('Cancel', {
                label: editor.lang.common.cancel,
                command: 'c5cancel',
                toolbar: 'document,1'
            });
        }
    }
});