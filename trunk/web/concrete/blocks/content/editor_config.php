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
	content_css : "<?=$theme->getThemeEditorCSS()?>"
});
</script>