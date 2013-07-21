<?php
$textEditorWidth=intval(Config::get('CONTENTS_TXT_EDITOR_WIDTH'));
$textEditorHeight=intval(Config::get('CONTENTS_TXT_EDITOR_HEIGHT'));
if($textEditorWidth<580)   $textEditorWidth=580;
//else $textEditorWidth=  $textEditorWidth;
if($textEditorHeight<100)  $textEditorHeight=380;
else $textEditorHeight= $textEditorHeight-70;
?> 
<script language="javascript">
tinyMCE.init({
	mode : "textareas",
	width: "100%", 
	height: "<?=$textEditorHeight?>px", 	
	inlinepopups_skin : "concreteMCE",
	theme_concrete_buttons2_add : "spellchecker",
	browser_spellcheck: true,
	gecko_spellcheck: true,
	relative_urls : false,
	document_base_url: '<?=BASE_URL . DIR_REL?>/',
	convert_urls: false,
	entity_encoding: 'raw',
	<? if (is_object($theme)) { ?>
		content_css : "<?=$theme->getThemeEditorCSS()?>",
	<? } ?>
	<?
	$txtEditorMode=Config::get('CONTENTS_TXT_EDITOR_MODE');
	if( $txtEditorMode=='CUSTOM' ){ ?>
		//theme : "concrete",
		<?
		echo Config::get('CONTENTS_TXT_EDITOR_CUSTOM_CODE').'';
	}elseif($txtEditorMode=='ADVANCED'){ ?>
		plugins: "inlinepopups,spellchecker,safari,advlink,table,advhr,advimage,xhtmlxtras,emotions,insertdatetime,paste,visualchars,nonbreaking,pagebreak,style",
		editor_selector : "ccm-advanced-editor",
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
		editor_selector : "ccm-advanced-editor",		
		spellchecker_languages : "+English=en",
		theme : "advanced",
		plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras", //,template,imagemanager,filemanager",		
		theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,styleselect,formatselect,fontselect,fontsizeselect,", //save,newdocument,help,|,		
		theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor", //
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,insertdate,inserttime,|,ltr,rtl,", //
		theme_advanced_buttons4 : "charmap,emotions,iespell,media,advhr,|,fullscreen,preview,|,styleprops,spellchecker,|,cite,del,ins,attribs,|,visualchars,nonbreaking,blockquote,pagebreak", //insertlayer,moveforward,movebackward,absolute,|,|,abbr,acronym,template,insertfile,insertimage		
		theme_advanced_blockformats : "p,address,pre,h1,h2,h3,div,blockquote,cite",
		theme_advanced_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
		theme_advanced_font_sizes : "1,2,3,4,5,6,7",
		theme_advanced_more_colors : 1,				
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",		
		theme_advanced_statusbar_location : "bottom",
		//theme_advanced_styles: "Note=ccm-note",		
		theme_advanced_resizing : true				
	<? }else{ //simple ?>
		theme : "concrete", 
		plugins: "paste,inlinepopups,spellchecker,safari,advlink,advimage,advhr",
		editor_selector : "ccm-advanced-editor",
		spellchecker_languages : "+English=en"		
	<? } ?>
});
</script>