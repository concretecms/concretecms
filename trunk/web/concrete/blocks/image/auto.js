ccmValidateBlockForm = function() {
	if ($("#ccm-b-image-fm-value").val() == '' || $("#ccm-b-image-fm-value").val() == 0) { 
		ccm_addError(ccm_t('image-required'));
	}
	return false;
}