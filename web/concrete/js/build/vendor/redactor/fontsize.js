if (!RedactorPlugins) var RedactorPlugins = {};

RedactorPlugins.fontsize = {
    init: function()
    {
        var fonts = [10, 11, 12, 14, 16, 18, 20, 24, 28, 30];
        var that = this;
        var dropdown = {};

        $.each(fonts, function(i, s)
        {
            dropdown['s' + i] = { title: s + 'px', callback: function() { that.setFontsize(s); } };
        });

        dropdown['remove'] = { title: ccmi18n_redactor.remove_font_size, callback: function() { that.resetFontsize(); } };

        this.buttonAddAfter('fontfamily', 'fontsize', ccmi18n_redactor.change_font_size, false, dropdown);
    },
    setFontsize: function(size)
    {
        this.inlineSetStyle('font-size', size + 'px');
    },
    resetFontsize: function()
    {
        this.inlineRemoveStyle('font-size');
    }
};