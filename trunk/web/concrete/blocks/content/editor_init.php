<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<script language="javascript">
tinyMCE.init({
	mode : "textareas",
	editor_selector : "advancedEditor",
	theme : "concrete",
	width: "580px",
	height: "380px",
	plugins: "inlinepopups,spellchecker,safari,advlink",
	inlinepopups_skin : "concreteMCE",
	theme_concrete_buttons2_add : "spellchecker",
	spellchecker_languages : "+English=en",
	relative_urls : false,
	convert_urls: false,
	content_css : "<?=$th->getThemeEditorCSS()?>"
});
</script>

<script type="text/javascript">
var ccm_editorCurrentAuxTool = '';
var editor_id = 'ccm-content-<?=$a->getAreaID()?>';

// store the selection/position for ie..
var bm; 
setBookMark = function () {
	bm = tinyMCE.activeEditor.selection.getBookmark();
}

ccm_selectSitemapNode = function(cID, cName) {
	var mceEd = tinyMCE.activeEditor;	
	var url = '<?=BASE_URL . DIR_REL?>/index.php?cID=' + cID;
	
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
		var selectedText = '<a href="<?=BASE_URL . DIR_REL?>/index.php?cID=' + cID + '" title="' + cName + '">' + cName + '<\/a>';
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
				src : obj.filePath,
				alt : obj.fileName,
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
					title : obj.fileName,
					target : null,
					'class' :  null
				});
			} else { // insert a normal link
				var html = '<a href="' + obj.filePath + '">' + obj.fileName + '<\/a>';
				tinyMCE.execCommand('mceInsertRawHTML', false, html, true); 
			}
		break;
	}
		
}
</script>

<? Loader::element('editor_controls'); ?>