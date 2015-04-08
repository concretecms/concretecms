// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5inline = function() {

    return {
        init: function() {

            var obj = this;
            this.$toolbar.addClass("ccm-inline-toolbar");
            this.$toolbar.append($('<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel"><button id="ccm-redactor-cancel-button" type="button" class="btn btn-mini">' + this.lang.get('cancel') + '</button></li><li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save"><button id="ccm-redactor-save-button" type="button" class="btn btn-primary btn-mini">' + this.lang.get('save') + '</button></li>'));
            var toolbar = this.$toolbar;

            $('#ccm-redactor-cancel-button').unbind().on('click', function() {
                toolbar.hide();
                $('li#ccm-redactor-actions-buttons').hide();
                ConcreteEvent.fire('EditModeExitInline');
                obj.core.destroy();

                // i don't believe this is necessary because I believe this is handled by EditModeExitInline
                //Concrete.getEditMode().scanBlocks();
            });
            $('#ccm-redactor-save-button').unbind().on('click', function() {
                $('#redactor-content').val(obj.code.get());
                toolbar.hide();
                ConcreteEvent.fire('EditModeBlockSaveInline');
            });

        }
    }
}