ccmValidateBlockForm = function() {
	if ($("#ccm-b-file-value").val() == '' || $("#ccm-b-file-value").val() == 0) { 
		ccm_addError('You must select a file.');
	}
	return false;
}