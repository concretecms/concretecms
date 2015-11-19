<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<?
	$content = $controller->getContentEditMode();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$data = $view->getRequestValue();
		$content = $data['content'];
	}
	print Core::make('editor')->outputPageComposerEditor($view->field('content'), $content);
	?>
</div>