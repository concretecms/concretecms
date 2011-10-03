function ccm_previewInternalTheme(cID, themeID,themeName){
	var ctID=$("input[name=ctID]").val();
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/themes/preview?themeID="+themeID+'&previewCID='+cID+'&ctID='+ctID,
		width: '85%',
		modal: false,
		height: '75%' 
	});	
}

function ccm_previewMarketplaceTheme(cID, themeCID,themeName,themeHandle){
	var ctID=$("input[name=ctID]").val();
	
	$.fn.dialog.open({
		title: themeName,
		href: CCM_TOOLS_PATH + "/themes/preview?themeCID="+themeCID+'&previewCID='+cID+'&themeHandle='+encodeURIComponent(themeHandle)+'&ctID='+ctID,
		width: '85%',
		modal: false,
		height: '75%' 
	});
}
