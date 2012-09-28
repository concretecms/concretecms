<?
defined('C5_EXECUTE') or die("Access Denied.");
//$replaceOnUnload = 1;
$bc = $b->getBlockCollectionObject();

$class = strtolower('ccm-advanced-editor-' . $controller->getIdentifier());
if (is_object($bc)) {
	$class .= "_" . $bc->getCollectionID();
}
Loader::element('editor_config', array('editor_selector' => $class));
Loader::element('editor_controls');

$form = Loader::helper('form');
print $form->textarea($this->field('content'), $controller->getContentEditMode(), array(
	'class' => 'advancedEditor ' . $class,
	'style' => 'width: 580px; height: 380px'
));

