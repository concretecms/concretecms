<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<?=$form->textarea($this->field('description'), $control->getPageTypeComposerControlDraftValue(), array('style' => 'height: 100px'))?>
</div>
