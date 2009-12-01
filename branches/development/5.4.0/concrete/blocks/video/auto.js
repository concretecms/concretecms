ccmValidateBlockForm = function() {
	if ($("#ccm-b-flv-file-value").val() == '' || $("#ccm-b-flv-file-value").val() == 0) { 
		ccm_addError(ccm_t('flv-required'));
	}
	return false;
}

ccm_chooseAsset = function(obj) {
	ccm_triggerSelectFile(obj.fID);
	if (obj.width) {
		$("#ccm-block-video-width").val(obj.width);
	}
	if (obj.height) {
		$("#ccm-block-video-height").val(obj.width);
	}
}