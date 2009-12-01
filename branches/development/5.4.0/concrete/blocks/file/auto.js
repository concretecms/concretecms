ccmValidateBlockForm = function() {
	if ($("#ccm-b-file-value").val() == '' || $("#ccm-b-file-value").val() == 0) { 
		ccm_addError(ccm_t('file-required'));
	}
	return false;
}