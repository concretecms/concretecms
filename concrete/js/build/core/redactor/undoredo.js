// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.undoredo = function() {

    return {
        init: function()
        {
            var undo = this.button.addFirst('undo', this.lang.get('undo'));
            var redo = this.button.addAfter('undo', 'redo', this.lang.get('redo'));
            this.button.setAwesome('undo', 'fa-undo');
            this.button.setAwesome('redo', 'fa-undo fa-flip-horizontal');

            this.button.addCallback(undo, this.buffer.undo);
            this.button.addCallback(redo, this.buffer.redo);
        }

    };
}