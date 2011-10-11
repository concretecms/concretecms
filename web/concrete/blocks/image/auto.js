ccmValidateBlockForm = function() {
	if ($("#ccm-b-image-fm-value").val() == '' || $("#ccm-b-image-fm-value").val() == 0) { 
		ccm_addError(ccm_t('image-required'));
	}
	return false;
}

refreshLinkTypeControls = function() {
	var linkType = $('#linkType').val();
	$('#linkTypePage').toggle(linkType == 1);
	$('#linkTypeExternal').toggle(linkType == 2);
}

$(document).ready(function() {
	$('#linkType').change(refreshLinkTypeControls);
	refreshLinkTypeControls();
});