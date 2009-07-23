ccmValidateBlockForm = function() {
	if ($("#ccm-b-image-value").val() == '' || $("#ccm-b-image-value").val() == 0) { 
		ccm_addError(ccm_t('image-required'));
	}
	return false;
}