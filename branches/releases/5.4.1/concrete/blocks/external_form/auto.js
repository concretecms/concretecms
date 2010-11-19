ccmValidateBlockForm = function() {
	if ($("#cstFilename").val() == '') { 
		ccm_addError(ccm_t('form-required'));
	}
	return false;
}