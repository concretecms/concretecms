<?php
$textEditorOptions = array();
$textEditorOptions['width'] = '100%';

if(isset($editor_selector)) {
	$textEditorOptions['editor_selector'] = $editor_selector;
}
if(isset($editor_width)) {
	$textEditorOptions['width'] = $editor_height;
}
if(isset($editor_height)) {
	$textEditorOptions['height'] = $editor_height;
}
if(isset($editor_mode)) {
	$textEditorOptions['textEditorMode'] = $editor_mode;
}
$theme = PageTheme::getSiteTheme();
$textEditorOptions['content_css'] = $theme->getThemeEditorCSS();

$textEditorOptions = Loader::helper('tinymce')->getOptions($textEditorOptions);

// Let's set the value of some variables (defined in the old version of this 'editor_config' element... maybe someone want them)
$textEditorWidth = $textEditorOptions['width'];
$textEditorHeight = $textEditorOptions['height'];
$editor_selector = $textEditorOptions['editor_selector'];

?><script language="javascript" type="text/javascript">
$(function() {
	tinyMCE.init(<?php echo Loader::helper('json')->encode($textEditorOptions); ?>);
});
</script><?php


