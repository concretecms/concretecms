(function($) {

var methods = {

	init: function(options) {
		return $.each($(this), function(i, obj) {
			$(this).find('.ccm-conversation-attachment-container').each(function() {
				if($(this).is(':visible')) {
					$(this).toggle();
				}
			});
		});
	}

}

$.fn.ccmconversationattachments = function(method) {
	
	if ( methods[method] ) {
		return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	} else if ( typeof method === 'object' || ! method ) {
		return methods.init.apply( this, arguments );
	} else {
		$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
	}    

}

})(jQuery);