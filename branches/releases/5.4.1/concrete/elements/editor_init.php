<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php 
if (isset($GLOBALS['CCM_SITEMAP_NODE_NUM'])) {
	$GLOBALS['CCM_EDITOR_SITEMAP_NODE_NUM']++;
} else {
	$GLOBALS['CCM_EDITOR_SITEMAP_NODE_NUM'] = 1;
}

?>

<script type="text/javascript">
var ccm_editorCurrentAuxTool = '';

// store the selection/position for ie..
var bm; 
setBookMark = function () {
	tinyMCE.activeEditor.focus();
	bm = tinyMCE.activeEditor.selection.getBookmark();
}

ccm_selectSitemapNode<?php echo $GLOBALS['CCM_EDITOR_SITEMAP_NODE_NUM']?> = function(cID, cName) {
	var mceEd = tinyMCE.activeEditor;	
	var url = '<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=' + cID;
	
	mceEd.selection.moveToBookmark(bm);
	var selectedText = mceEd.selection.getContent();
	
	if (selectedText != '') {		
		mceEd.execCommand('mceInsertLink', false, {
			href : url,
			title : cName,
			target : null,
			'class' : null
		});
	} else {
		var selectedText = '<a href="<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=' + cID + '" title="' + cName + '">' + cName + '<\/a>';
		tinyMCE.execCommand('mceInsertRawHTML', false, selectedText, true); 
	}
	
}

ccm_chooseAsset = function(obj) {
	var mceEd = tinyMCE.activeEditor;
	mceEd.selection.moveToBookmark(bm); // reset selection to the bookmark (ie looses it)

	switch(ccm_editorCurrentAuxTool) {
		case "image":
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
			break;
		default: // file
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
		break;
	}
}
</script>
