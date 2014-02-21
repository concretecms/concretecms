<?php
$textEditorOptions = array();
$textEditorOptions['width'] = '100%';
if(isset($theme) && is_object($theme)) {
	$textEditorOptions['content_css'] = $theme->getThemeEditorCSS();
}
$textEditorOptions = Loader::helper('tinymce')->getOptions($textEditorOptions);
?><script language="javascript" type="text/javascript">
tinyMCE.init(<?php echo Loader::helper('json')->encode($textEditorOptions); ?>);
</script>