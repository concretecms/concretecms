/* User Profile Functionality */

ccm_enableUserProfileMenu = function() {
	var container = $('#ccm-account-menu-container');
	if (container.length == 0) {
		var container = $('<div />').appendTo(document.body);
	}
	container.addClass('ccm-ui').attr('id', 'ccm-account-menu-container');
	$('#ccm-account-menu').appendTo(container);
}