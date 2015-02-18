<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<?php if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>
	<div class="controls">
		<?php
		print $form->textarea($view->field('content'), $content, array(
			'class' => $class,
			'style' => 'width: 580px; height: 380px'
		));
		?>
	</div>
</div>