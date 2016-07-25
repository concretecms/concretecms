// concrete5 Redactor functionality
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.concrete5lightbox = function() {

    return {
        init: function()
        {
            this.opts.concrete5.lightbox = true;
        }

    };
}