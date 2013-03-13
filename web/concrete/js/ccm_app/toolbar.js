/** 
 * Basic concrete5 toolbar class
 */

var CCMToolbar = function() {

	var element = '#ccm-toolbar';

	return {
		start: function() {
			if ($(element).length > 0) {

				
				$('a[data-toggle=ccm-toolbar-hover-menu]').hoverIntent(function() {
					$('.ccm-toolbar-hover-menu').hide();
					$($(this).data('toggle-menu')).show();
				}, function() {

				});

				$(element).find('.dialog-launch').dialog();

			}
		}
	}

}();

CCMToolbar.start();