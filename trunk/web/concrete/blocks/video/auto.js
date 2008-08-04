ccmValidateBlockForm = function() {
	if ($("#ccm-b-flv-file-value").val() == '' || $("#ccm-b-flv-file-value").val() == 0) { 
		ccm_addError('You must select an FLV file.');
	}
	return false;
}