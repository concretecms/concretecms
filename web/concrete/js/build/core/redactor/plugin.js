// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5inline = {

    init: function() {

        var obj = this;
        this.$toolbar.addClass("ccm-inline-toolbar");
        this.$toolbar.append($('<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel"><button id="ccm-redactor-cancel-button" type="button" class="btn btn-mini">Cancel</button></li><li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save"><button id="ccm-redactor-save-button" type="button" class="btn btn-primary btn-mini">Save</button></li>'));
        var toolbar = this.$toolbar;

        $('#ccm-redactor-cancel-button').unbind().on('click', function() {
            toolbar.hide();
            $('li#ccm-redactor-actions-buttons').hide();
            ConcreteEvent.fire('EditModeExitInline');
            obj.destroy();
        });
        $('#ccm-redactor-save-button').unbind().on('click', function() {
            $('#redactor-content').val(obj.get());
            toolbar.hide();
            obj.destroy();
            $('#ccm-block-form').submit();
        });

    }

}

RedactorPlugins.concrete5 = {

    init: function() {

        var plugin = this;

        $.ajax({
            'type': 'get',
            'dataType': 'json',
            'url': CCM_TOOLS_PATH + '/system_content_editor_menu?ccm_token=' + CCM_EDITOR_SECURITY_TOKEN,
            success: function(response) {
                dropdownOptions = {};

                plugin.snippetsByHandle = {};
                $.each(response.snippets, function(i, snippet) {
                    plugin.snippetsByHandle[snippet.scsHandle] = {
                        'scsHandle': snippet.scsHandle,
                        'scsName': snippet.scsName
                    }
                    dropdownOptions[snippet.scsHandle] = {
                        'title': snippet.scsName,
                        'callback': function(option, $item, obj, e) {
                            var editor = this;
                            var selectedSnippet = plugin.snippetsByHandle[option];
                            var html = String() +
                                '<span class="ccm-content-editor-snippet" contenteditable="false" data-scsHandle="' + selectedSnippet.scsHandle + '">' +
                                selectedSnippet.scsName +
                                '</span>';
                            editor.insertHtml(html);
                        }
                    }
                });

                if (response.snippets.length > 0) {
                    plugin.buttonAddFirst('wrench', 'Snippets', false, dropdownOptions);
                }
            }
        });
    }
}