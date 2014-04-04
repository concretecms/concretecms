ccmValidateBlockForm = function() {
	if ($("#ccm-b-file-fm-value").val() == '' || $("#ccm-b-file-fm-value").val() == 0) { 
		ccm_addError(ccm_t('file-required'));
	}
	return false;
}