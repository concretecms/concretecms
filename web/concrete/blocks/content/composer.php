<?php
defined('C5_EXECUTE') or die("Access Denied.");
$class = 'ccm-block-content-editor-composer';
$form = Loader::helper('form');
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<?php if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>
	<div class="controls">
		<?php
		print $form->textarea($view->field('content'), $controller->getContentEditMode(), array(
			'class' => $class
		));
		?>
	</div>
</div>

<script type="text/javascript">
var CCM_EDITOR_SECURITY_TOKEN = "<?=Loader::helper('validation/token')->generate('editor')?>";

$(function() {
	$('.<?=$class?>').redactor({
		'plugins': ['concrete5'],
        'minHeight': 380
	});
});
</script>