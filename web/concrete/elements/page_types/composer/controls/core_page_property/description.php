<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls">
		<?=$form->textarea($this->field('description'), $control->getPageTypeComposerControlDraftValue(), array('style' => 'height: 100px'))?>
	</div>
</div>
