/** 
 * TinyMCE
 */

var ccm_editorCurrentAuxTool = false;
ccm_editorSetupImagePicker = function() {
	tinyMCE.activeEditor.focus();
	var bm = tinyMCE.activeEditor.selection.getBookmark();
	ccm_chooseAsset = function(obj) {
		var mceEd = tinyMCE.activeEditor;
		mceEd.selection.moveToBookmark(bm); // reset selection to the bookmark (ie looses it)
		var args = {};
		tinymce.extend(args, {
			src : obj.filePathInline,
			alt : obj.title,
			width : obj.width,
			height : obj.height
		});
		
		mceEd.execCommand('mceInsertContent', false, '<img id="__mce_tmp" src="javascript:;" />', {skip_undo : 1});
		mceEd.dom.setAttribs('__mce_tmp', args);
		mceEd.dom.setAttrib('__mce_tmp', 'id', '');
		mceEd.undoManager.add();
	}
	
	return false;

}

ccm_editorSetupFilePicker = function() {
	tinyMCE.activeEditor.focus();
	var bm = tinyMCE.activeEditor.selection.getBookmark();
	ccm_chooseAsset = function(obj) {
		var mceEd = tinyMCE.activeEditor;
		mceEd.selection.moveToBookmark(bm); // reset selection to the bookmark (ie looses it)
		var selectedText = mceEd.selection.getContent();

		if(selectedText != '') { // make a link, let mce deal with the text of the link..
			mceEd.execCommand('mceInsertLink', false, {
				href : obj.filePath,
				title : obj.title,
				target : null,
				'class' :  null
			});
		} else { // insert a normal link
			var html = '<a href="' + obj.filePath + '">' + obj.title + '<\/a>';
			tinyMCE.execCommand('mceInsertRawHTML', false, html, true); 
		}
	}
	return false;
}

ccm_editorSitemapOverlay = function() {

	tinyMCE.activeEditor.focus();
	var bm = tinyMCE.activeEditor.selection.getBookmark();

    $.fn.dialog.open({
        title: ccmi18n_sitemap.choosePage,
        href: CCM_TOOLS_PATH + '/sitemap_search_selector.php?sitemap_select_mode=select_page&callback=ccm_editorSelectSitemapNode',
        width: '90%',
        modal: false,
        height: '70%'
    });

    
    ccm_editorSelectSitemapNode = function(cID, cName) {
		var mceEd = tinyMCE.activeEditor;	
		mceEd.selection.moveToBookmark(bm);
		var selectedText = mceEd.selection.getContent();

		var url = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID;
		
		if (selectedText != '') {		
			mceEd.execCommand('mceInsertLink', false, {
				href : url,
				title : cName,
				target : null,
				'class' : null
			});
		} else {
			var selectedText = '<a href="' + CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '?cID=' + cID + '" title="' + cName + '">' + cName + '<\/a>';
			tinyMCE.execCommand('mceInsertRawHTML', false, selectedText, true); 
		}
		
	}

};
