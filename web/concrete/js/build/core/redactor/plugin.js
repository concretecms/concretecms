// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5inline = {

    init: function() {

        var obj = this;
        this.$toolbar.addClass("ccm-inline-toolbar");
        this.$toolbar.append($('<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel"><button id="ccm-redactor-cancel-button" type="button" class="btn btn-mini">' + ccmi18n_redactor.cancel + '</button></li><li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save"><button id="ccm-redactor-save-button" type="button" class="btn btn-primary btn-mini">' + ccmi18n_redactor.save + '</button></li>'));
        var toolbar = this.$toolbar;

        $('#ccm-redactor-cancel-button').unbind().on('click', function() {
            toolbar.hide();
            $('li#ccm-redactor-actions-buttons').hide();
            ConcreteEvent.fire('EditModeExitInline');
            obj.destroy();

            Concrete.getEditMode().scanBlocks();
        });
        $('#ccm-redactor-save-button').unbind().on('click', function() {
            $('#redactor-content').val(obj.get());
            toolbar.hide();
            $('#ccm-block-form').submit();
            ConcreteEvent.fire('EditModeExitInlineSaved');
            ConcreteEvent.fire('EditModeExitInline', {
                action: 'save_inline'
            });
        });

    }

}

RedactorPlugins.concrete5 = {

    styles: [],

    createButton: function(dropdown) {
        if (!dropdown) {
            var dropdown = [];
        }
        var plugin = this;
        plugin.buttonRemove('styles');
        if (plugin.buttonGet('formatting').length) {
            plugin.buttonAddAfter('formatting','styles', plugin.opts.curLang.customStyles, false, dropdown);
        } else {
            plugin.buttonAdd('styles', plugin.opts.curLang.customStyles, false, dropdown);
        }
    },

    init: function() {

        var plugin = this;

        $.ajax({
            'type': 'get',
            'dataType': 'json',
            'url': CCM_DISPATCHER_FILENAME + '/ccm/system/backend/editor_data',
            'data': {
                'ccm_token': CCM_EDITOR_SECURITY_TOKEN,
                'cID': CCM_CID
            },

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

                var dropdown = {};
                var button = plugin.buttonGet('styles');

                plugin.styles = response.classes;
                jQuery.each(response.classes, function(i, s)
                {
                    dropdown['s' + i] = { title: s.title, className:s.menuClass, callback: function() { plugin.setCustomFormat(s); }};
                });

                dropdown['remove'] = { title: ccmi18n_redactor.remove_style, callback: function() { plugin.resetCustomFormat(); }};
                plugin.createButton(dropdown);

            }
        });
        plugin.createButton();

    },

    setCustomFormat: function (s)
    {
        if (s.forceBlock != -1 && (s.forceBlock == 1 || (s.wrap && !(jQuery.inArray(s.wrap,['a','em','strong','small','s','cite','q','dfn','abbr','data','time','var','samp','kbd','i','b','u','mark','ruby','rt','rp','bdi','bdo','span','sub','sup','code']) > -1)))) {
            this.selectionWrap(s.wrap);
            //this.inlineFormat(s.wrap);
            if(s.style) this.blockSetAttr('style',s.style);
            if(s.spanClass) this.blockSetClass(s.spanClass);
        }
        else {
            if(s.wrap) this.inlineFormat(s.wrap);
            if(s.style) this.inlineSetAttr('style', s.style);
            if(s.spanClass) this.inlineSetClass(s.spanClass);
        }
    },
    resetCustomFormat: function()
    {
        var that = this;
        jQuery.each(this.styles, function(i,s) {
            if(s.spanClass) {
                that.inlineRemoveClass(s.spanClass);
                that.blockRemoveClass(s.spanClass);
                that.formatBlocks('p');
            }
        });
        this.inlineSetAttr('style','');
    }

}

RedactorPlugins.underline = {
        init: function()
        {
            var button = this.buttonAddAfter('italic', 'underline', 'Underline', this.format);
        },
        format: function(s)
        {
            if (jQuery(this.buttonGet('underline')).hasClass('redactor_act')) {
                this.inlineRemoveFormat('u');
            } else {
                this.inlineFormat('u');
            }
        }
};
