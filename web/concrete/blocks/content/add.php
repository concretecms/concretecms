<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="redactor-edit-content"></div>
<textarea style="display: none" id="redactor-content" name="content"><?=$controller->getContentEditMode()?></textarea>

<?
print Loader::helper("html")->css('redactor.css');
print Loader::helper("html")->javascript('redactor.js');
print Loader::helper("html")->javascript('redactor.concrete5.js');

?>

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