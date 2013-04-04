(function($, window) {

  var methods = {

    saveinterval: false,

    private:  {

      saveDraft: function($this) {
        console.log($this);
        console.log($this.data('settings'));
      },

    },

    init: function(options) {

      var settings = $.extend({
        autoSaveTimeout: 5000
      }, options);

      return this.each(function() {

        var $this = $(this);
        $this.data('settings', settings);

        methods.saveinternal = setInterval(function() {
          methods.private.saveDraft($this);
        }, settings.autoSaveTimeout);       

      });

    },


  };

  $.fn.ccmcomposer = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmcomposer' );
    }   

  };
})(jQuery, window);