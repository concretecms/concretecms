<?
defined('C5_EXECUTE') or die("Access Denied.");
//$replaceOnUnload = 1;
$bc = $b->getBlockCollectionObject();

$class = strtolower('ccm-content-editor-' . $controller->getIdentifier());
if (is_object($bc)) {
	$class .= "_" . $bc->getCollectionID();
}

$form = Loader::helper('form');
print $form->textarea($this->field('content'), $controller->getContentEditMode(), array(
	'class' => $class,
	'style' => 'width: 580px; height: 380px'
));
?>

<script type="text/javascript">
$(function() {
	$('.<?=$class?>').redactor({
		'plugins': ['concrete5']
	});
});
</script>