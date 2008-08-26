ccmValidateBlockForm = function() {
	if ($("#ccm-b-image-value").val() == '' || $("#ccm-b-image-value").val() == 0) { 
		ccm_addError('You must select an image.');
	}
	return false;
}