<?
$textEditorHeight=intval(Config::get('CONTENTS_TXT_EDITOR_HEIGHT'));
$textEditorWidth = '100%';
//else $textEditorWidth=  $textEditorWidth;
if($textEditorHeight<100)  $textEditorHeight=380;
else $textEditorHeight= $textEditorHeight-70;

if (!isset($editor_selector)) {
	$editor_selector = 'ccm-advanced-editor';
}

if (isset($editor_height)) {
	$textEditorHeight = $editor_height;
}

if (isset($editor_width)) {
	$textEditorWidth = $editor_width;
}

if (!isset($editor_mode)) {
	$txtEditorMode=Config::get('CONTENTS_TXT_EDITOR_MODE');
} else {
	$txtEditorMode = $editor_mode;
}

$theme = PageTheme::getSiteTheme();
?> 
<script language="javascript">
$(function() {
	tinyMCE.init({
		mode : "textareas",
		width: "<?=$textEditorWidth?>", 
		height: "<?=$textEditorHeight?>px", 	
		browser_spellcheck: true,
		gecko_spellcheck: true,
		inlinepopups_skin : "concreteMCE",
		entity_encoding: 'raw',
		theme_concrete_buttons2_add : "spellchecker",
		relative_urls : false,
		document_base_url: '<?=BASE_URL . DIR_REL?>/',
		convert_urls: false,
		content_css : "<?=$theme->getThemeEditorCSS()?>",
		<?
		if( $txtEditorMode=='CUSTOM' ){ ?>
			//theme : "concrete",
			<?
			$code = Config::get('CONTENTS_TXT_EDITOR_CUSTOM_CODE').'';
			if ($editor_selector != 'ccm-advanced-editor') {
				$code = str_replace('ccm-advanced-editor', $editor_selector, $code);
			}
			print $code;
		}elseif($txtEditorMode=='ADVANCED'){ ?>
			plugins: "inlinepopups,spellchecker,safari,advimage,advlink,table,advhr,xhtmlxtras,emotions,insertdatetime,paste,visualchars,nonbreaking,pagebreak,style",
			editor_selector : "<?=$editor_selector?>",
			theme : "advanced",
			theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,undo,redo,|,styleselect,formatselect,fontsizeselect,fontselect",
			theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,|,forecolor,backcolor,|,image,charmap,emotions",
			theme_advanced_buttons3 : "cleanup,code,help,charmap,insertdate,inserttime,visualchars,nonbreaking,pagebreak,hr,|,tablecontrols",
			theme_advanced_blockformats : "p,address,pre,h1,h2,h3,div,blockquote,cite",
			theme_advanced_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
			theme_advanced_font_sizes : "1,2,3,4,5,6,7",
			theme_advanced_more_colors : 1,						
			theme_advanced_toolbar_location : "top",
			//theme_advanced_styles: "Note=ccm-note",		
			theme_advanced_toolbar_align : "left",
			spellchecker_languages : "+English=en"
		<? }elseif($txtEditorMode=='OFFICE'){ ?> 
			editor_selector : "<?=$editor_selector?>",
			spellchecker_languages : "+English=en",
			theme : "advanced",
			plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras", //,template,imagemanager,filemanager",		
			theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,styleselect,formatselect,fontselect,fontsizeselect,", //save,newdocument,help,|,		
			theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor", //
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,insertdate,inserttime,|,ltr,rtl,", //
			theme_advanced_buttons4 : "charmap,emotions,iespell,media,advhr,|,fullscreen,|,styleprops,spellchecker,|,cite,del,ins,attribs,|,visualchars,nonbreaking,blockquote,pagebreak", //insertlayer,moveforward,movebackward,absolute,|,|,abbr,acronym,template,insertfile,insertimage		
			theme_advanced_blockformats : "p,address,pre,h1,h2,h3,div,blockquote,cite",
			theme_advanced_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
			theme_advanced_font_sizes : "1,2,3,4,5,6,7",
			theme_advanced_more_colors : 1,				
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",		
			theme_advanced_statusbar_location : "bottom",
			//theme_advanced_styles: "Note=ccm-note",		
			theme_advanced_resizing : true				
		<? }elseif($txtEditorMode=='BASIC'){ ?> 
			editor_selector : "<?=$editor_selector?>",
			spellchecker_languages : "+English=en",
			theme : "simple",
			plugins: "paste,inlinepopups,spellchecker,safari,advlink"
		<? }else{ //simple ?>
			theme : "concrete", 
			plugins: "paste,inlinepopups,spellchecker,safari,advlink,advimage,advhr",
			editor_selector : "<?=$editor_selector?>",
			spellchecker_languages : "+English=en"		
		<? } ?>
	});
});
</script>
