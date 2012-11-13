<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="redactor-edit-content"><?=$controller->getContentEditMode()?></div>
<textarea style="display: none" id="redactor-content" name="content"><?=$controller->getContentEditMode()?></textarea>

<script type="text/javascript">
$(function() {
	$('#redactor-edit-content').redactor({
		'plugins': [
			'concrete5inline'
		]
	});
});
</script>