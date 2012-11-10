<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?=$controller->getContentEditMode()?>

<?
print Loader::helper("html")->css('redactor.css');
print Loader::helper("html")->javascript('redactor.js');
print Loader::helper("html")->javascript('redactor.concrete5.js');

?>

<script type="text/javascript">
$(function() {
	$('.ccm-block-edit-inline').redactor({
		'plugins': [
			'concrete5'
		]
	});
});
</script>