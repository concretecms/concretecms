<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<div class="controls">
		<?
		$content = $controller->getContentEditMode();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data = $view->getRequestValue();
			$content = $data['content'];
		}
		print Core::make('editor')->outputPageComposerEditor($view->field('content'), $content);
		?>
	</div>
</div>