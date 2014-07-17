if (!RedactorPlugins) var RedactorPlugins = {};

RedactorPlugins.fontfamily = {
    init: function ()
    {
        var fonts = [ 'Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Monospace' ];
        var that = this;
        var dropdown = {};

        $.each(fonts, function(i, s)
        {
            dropdown['s' + i] = { title: s, callback: function() { that.setFontfamily(s); }};
        });

        dropdown['remove'] = { title: ccmi18n_redactor.remove_font, callback: function() { that.resetFontfamily(); }};

        this.buttonAddAfter('formatting', 'fontfamily', ccmi18n_redactor.change_font_family, false, dropdown);
    },
    setFontfamily: function (value)
    {
        this.inlineSetStyle('font-family', value);
    },
    resetFontfamily: function()
    {
        this.inlineRemoveStyle('font-family');
    }
};