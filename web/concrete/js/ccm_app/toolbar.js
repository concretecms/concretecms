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

				$(document.body).on('click', function() {
					$('.ccm-toolbar-hover-menu').hide();
				});

				$(element).on('click', function(e) {
					e.stopPropagation(); // so we don't close menus if we click on the toolbar buttons themselves.
				});

				$($(element).find('.ccm-toolbar-hover-menu a')).on('click', function() {
					$('.ccm-toolbar-hover-menu').hide();
				});

			}
		}
	}

}();

CCMToolbar.start();