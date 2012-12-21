/*
*/

ccm_loadInlineEditor = function(cID, arHandle, aID, bID) {

	jQuery.fn.dialog.showLoader();
	ccm_enterInlineEditMode($('#b' + bID + '-' + aID));

	$.ajax({
	type: 'GET',
	url: CCM_TOOLS_PATH + '/edit_block_popup',
	data: 'btask=edit&cID=' + cID + '&bID=' + bID + '&arHandle=' + arHandle + '&aID=' + aID,
	success: function(r) {
		$('#b' + bID + '-' + aID).html(r);
		jQuery.fn.dialog.hideLoader();
	}});
}

ccm_loadInlineEditorAdd = function(cID, arHandle, aID, btID) {

	jQuery.fn.dialog.showLoader();
	ccm_enterInlineEditMode();
	$.ajax({
	type: 'GET',
	url: CCM_TOOLS_PATH + '/add_block_popup',
	data: 'btask=edit&cID=' + cID + '&arHandle=' + arHandle + '&btID=' + btID,
	success: function(r) {
		jQuery.fn.dialog.closeAll();
		$('#a' + aID).append($('<div id="a' + aID + '-bt' + btID + '" class="ccm-block-edit-inline-active">' + r + '</div>'));
		jQuery.fn.dialog.hideLoader();
	}});
}

ccm_exitInlineEditMode = function(activeObj) {

	$.fn.ccmmenu.enable();
	$('div.ccm-block-edit').removeClass('ccm-block-edit-disabled');
	$('div.ccm-area-footer-handle').removeClass('ccm-block-edit-disabled');

	jQuery.fn.dialog.hideLoader();
	if (activeObj) {
		activeObj.ccmmenu();
	}
}

ccm_enterInlineEditMode = function(activeObj) {
	
	$.fn.ccmmenu.disable();

	$('div.ccm-block-edit').addClass('ccm-block-edit-disabled');
	$('div.ccm-area-footer-handle').addClass('ccm-block-edit-disabled');
	if (activeObj) {
		activeObj.removeClass('ccm-block-edit-disabled').addClass('ccm-block-edit-inline-active');
	}

}