/** 
 * Basic concrete5 toolbar class
 */

var CCMToolbar = function() {

	var element = '#ccm-page-controls-wrapper';

	return {
		start: function() {
			if ($(element).length > 0) {

				
				$('a[data-toggle=ccm-toolbar-hover-menu]').hoverIntent(function() {
					$('.ccm-toolbar-hover-menu').hide();
					$($(this).data('toggle-menu')).show();
				}, function() {

				});

				$(element).find('.dialog-launch').dialog();

				$(document).on('click.ccm-toolbar', function() {
					$('.ccm-toolbar-hover-menu').hide();
				});

				$(element).find('#ccm-toolbar').on('click', function(e) {
					e.stopPropagation(); // so we don't close menus if we click on the toolbar buttons themselves.
				});

				$($(element).find('.ccm-toolbar-hover-menu a')).on('click', function() {
					$('.ccm-toolbar-hover-menu').hide();
				});

				$(element).find('#ccm-exit-edit-mode-publish-menu a').on('click', function() {
					switch($(this).data('publish-action')) {
						case 'approve':
							$('#ccm-approve-field').val('APPROVE');
							break;
						case 'discard':
							$('#ccm-approve-field').val('DISCARD');
							break;
					}

					$('#ccm-exit-edit-mode-comment form').submit();
				});

				$('#ccm-page-status-bar .alert').bind('closed', function() {
					$(this).remove();
					var visi = $('#ccm-page-status-bar .alert:visible').length;
					if (visi == 0) {
						$('#ccm-page-status-bar').remove();
					}
				});

				$('#ccm-page-status-bar .ccm-status-bar-ajax-form').ajaxForm({
					dataType: 'json',
					beforeSubmit: function() {
						jQuery.fn.dialog.showLoader();
					},
					success: function(r) {
						if (r.redirect) {
							window.location.href = r.redirect;
						}
					}
				});

			}
		}
	}

}();