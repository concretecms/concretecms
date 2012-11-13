<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="redactor-edit-content"></div>
<textarea style="display: none" id="redactor-content" name="content"><?=$controller->getContentEditMode()?></textarea>

<script type="text/javascript">
$(function() {
	$('#redactor-edit-content').redactor({
		'plugins': [
			'concrete5inline'
		]
	});
	$('#redactor-edit-content').setFocus();
});
</script>