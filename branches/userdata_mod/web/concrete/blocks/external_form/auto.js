ccmValidateBlockForm = function() {
	if ($("#cstFilename").val() == '') { 
		ccm_addError('You must select a form.');
	}
	return false;
}