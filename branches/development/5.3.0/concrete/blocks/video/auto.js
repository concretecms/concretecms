ccmValidateBlockForm = function() {
	if ($("#ccm-b-flv-file-value").val() == '' || $("#ccm-b-flv-file-value").val() == 0) { 
		ccm_addError(ccm_t('flv-required'));
	}
	return false;
}