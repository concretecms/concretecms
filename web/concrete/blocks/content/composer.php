<?
defined('C5_EXECUTE') or die("Access Denied.");
//$replaceOnUnload = 1;

$class = strtolower('ccm-content-editor-' . $controller->getIdentifier());

$form = Loader::helper('form');
print $form->textarea($this->field('content'), $controller->getContentEditMode(), array(
	'class' => $class,
	'style' => 'width: 580px; height: 380px'
));
?>

<script type="text/javascript">
var CCM_EDITOR_SECURITY_TOKEN = "<?=Loader::helper('validation/token')->generate('editor')?>";

$(function() {
	$('.<?=$class?>').redactor({
		'plugins': ['concrete5']
	});
});
</script>