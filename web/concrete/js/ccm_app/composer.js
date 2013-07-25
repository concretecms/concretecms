
function ccm_previewComposerDraft(cID){
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/composer/preview?previewCID="+cID,
		width: '85%',
		modal: false,
		height: '75%' 
	});	
}
