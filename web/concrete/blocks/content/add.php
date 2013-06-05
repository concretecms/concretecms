<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="redactor-edit-content"></div>
<textarea style="display: none" id="redactor-content" name="content"></textarea>

<script type="text/javascript">

var CCM_EDITOR_SECURITY_TOKEN = "<?=Loader::helper('validation/token')->generate('editor')?>";

$(function() {
	$('#redactor-edit-content').redactor({
		'plugins': [
			'concrete5inline', 'concrete5'
		]
	});
	$('#redactor-edit-content').setFocus();
});
</script>